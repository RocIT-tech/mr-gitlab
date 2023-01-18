<?php

declare(strict_types=1);

namespace App\Cli;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Gitlab\Client\MergeRequest\Query\GetDetailsQuery;
use App\Gitlab\Parser\MergeRequestUrl;
use App\Metrics\MetricsAggregator;
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
use function count;

#[AsCommand(
    name: 'gitlab:merge-request:parse',
    description: 'Parse the given Merge Request.',
)]
final class ParseMergeRequest extends Command
{
    use HandleTrait;

    private readonly MetricsAggregator $metricsAggregator;

    public function __construct(
        MessageBusInterface $queryBus,
        MetricsAggregator   $metricsAggregator,
    ) {
        $this->messageBus        = $queryBus;
        $this->metricsAggregator = $metricsAggregator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'URL of the Merge Request', null)
            ->setHelp(<<<TXT
            The <info>%command.name%</info> command parses the given merge request url for metrics:

            <info>php %command.full_name% https://{gitlab.url}/{group/project}/-/merge_requests/{id}</info>
            TXT,
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

        $metricResults = $this->metricsAggregator->getResult($mergeRequestDetails);

        $rows = [];

        foreach ($metricResults as $metricName => $metricResult) {
            $rows[] = [
                $metricName,
                $metricResult->description,
                $metricResult->constraint,
                $metricResult->currentValue->currentValue,
                true === $metricResult->success ? Emoji::checkMark() : Emoji::crossMark(),
            ];
        }
        $io->table(['Name', 'Description', 'Expected', 'Calculated', 'Result'], $rows);

        $io->definitionList(
            ['Number of Alerts' => $metricResults->stats->countSeverityAlert],
            ['Number of Warning' => $metricResults->stats->countSeverityWarning],
            ['Number of Suggestion' => $metricResults->stats->countSeveritySuggestion],
            new TableSeparator(),
            ['Number of Security' => $metricResults->stats->countCategorySecurity],
            ['Number of Performance' => $metricResults->stats->countCategoryPerformance],
            ['Number of Readability' => $metricResults->stats->countCategoryReadability],
            ['Number of Typo' => $metricResults->stats->countCategoryTypo],
            ['Number of Maintainability' => $metricResults->stats->countCategoryMaintainability],
            ['Number of Quality' => $metricResults->stats->countCategoryQuality],
            ['Number of Stability' => $metricResults->stats->countCategoryStability],
            new TableSeparator(),
            ['Number of replies' => $metricResults->stats->numberOfReplies],
            ['Number of threads' => $metricResults->stats->numberOfThreads],
            ['Max comments on a single thread' => $metricResults->stats->maxCommentsOnThread],
            ['Unresolved threads' => $metricResults->stats->countUnresolvedThreads],
        );

        $countTotal        = count($metricResults);
        $countSuccess      = $metricResults->countSuccess();
        $conclusionMessage = "{$countSuccess} / {$countTotal} have succeeded.";

        if ($countSuccess === $countTotal) {
            $io->success($conclusionMessage);

            return Command::SUCCESS;
        }

        $io->error($conclusionMessage);

        return Command::FAILURE;
    }
}
