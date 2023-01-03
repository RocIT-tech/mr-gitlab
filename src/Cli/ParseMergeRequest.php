<?php

declare(strict_types=1);

namespace App\Cli;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Gitlab\Client\MergeRequest\Query\GetDetailsQuery;
use App\Gitlab\Parser\MergeRequestUrl;
use App\Metrics\MetricsAggregator;
use App\Metrics\StatsAggregator;
use Spatie\Emoji\Emoji;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'gitlab:merge-request:parse',
    description: 'Parse the given Merge Request.',
)]
final class ParseMergeRequest extends Command
{
    use HandleTrait;

    private readonly MetricsAggregator $metricsAggregator;

    private readonly StatsAggregator $statsAggregator;

    public function __construct(
        MessageBusInterface $queryBus,
        MetricsAggregator   $metricsAggregator,
        StatsAggregator     $statsAggregator,
    ) {
        $this->messageBus        = $queryBus;
        $this->metricsAggregator = $metricsAggregator;
        $this->statsAggregator   = $statsAggregator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'URL of the Merge Request', null)
            ->setHelp(<<<TXT
            The <info>%command.name%</info> command parses the given merge request url for metrics:

            <info>php %command.full_name% https://{gitlab.url}/{group/project}/-/merge_requests/{id}</info>
            TXT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $mergeRequestUrl = MergeRequestUrl::fromRaw((string) $input->getArgument('url'));

        $getDetailsQuery = new GetDetailsQuery(
            projectId: $mergeRequestUrl->projectId,
            mergeRequestIid: $mergeRequestUrl->mergeRequestIid,
            baseUrl: $mergeRequestUrl->getBaseApiV4Url(),
        );

        /** @var Details $mergeRequestDetails */
        $mergeRequestDetails = $this->handle($getDetailsQuery);

        $io->title($mergeRequestDetails->web_url);

        $results = $this->metricsAggregator->getResult($mergeRequestDetails);

        $countSuccess = 0;
        $countTotal   = 0;

        $rows = [];

        foreach ($results as $metricName => $metricResult) {
            $countTotal++;
            if (true === $metricResult->success) {
                $countSuccess++;
            }

            $rows[] = [
                $metricName,
                $metricResult->description,
                $metricResult->expectedValue,
                $metricResult->currentValue,
                true === $metricResult->success ? Emoji::checkMark() : Emoji::crossMark(),
            ];
        }
        $io->table(['Name', 'Description', 'Expected', 'Calculated', 'Result'], $rows);

        $stats = $this->statsAggregator->getResult($mergeRequestDetails);
        $io->definitionList(
            ['Number of Alerts' => $stats->countSeverityAlert],
            ['Number of Warning' => $stats->countSeverityWarning],
            ['Number of Suggestion' => $stats->countSeveritySuggestion],
            new TableSeparator(),
            ['Number of Security' => $stats->countCategorySecurity],
            ['Number of Performance' => $stats->countCategoryPerformance],
            ['Number of Readability' => $stats->countCategoryReadability],
            ['Number of Typo' => $stats->countCategoryTypo],
            ['Number of Maintainability' => $stats->countCategoryMaintainability],
            ['Number of Quality' => $stats->countCategoryQuality],
            ['Number of Stability' => $stats->countCategoryStability],
            new TableSeparator(),
            ['Number of replies' => $stats->numberOfReplies],
            ['Number of threads' => $stats->numberOfThreads],
            ['Max comments on a single thread' => $stats->maxCommentsOnThread],
            ['Unresolved threads' => $stats->countUnresolvedThreads],
        );

        $conclusionMessage = "{$countSuccess} / {$countTotal} have succeeded.";

        if ($countSuccess === $countTotal) {
            $io->success($conclusionMessage);

            return Command::SUCCESS;
        }

        $io->error($conclusionMessage);

        return Command::FAILURE;
    }
}
