<?php

declare(strict_types=1);

namespace App\Application\Cli;

use App\Domain\Metrics\MetricsAggregator;
use App\Domain\Tenant\ConfigLoaderInterface;
use App\Domain\Tenant\TenantId;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use App\Infrastructure\Gitlab\Client\MergeRequest\Query\GetDetailsQuery;
use App\Infrastructure\Gitlab\Parser\MergeRequestUrl;
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

    private readonly ConfigLoaderInterface $configLoader;

    public function __construct(
        MessageBusInterface   $queryBus,
        MetricsAggregator     $metricsAggregator,
        ConfigLoaderInterface $configLoader,
    ) {
        $this->messageBus        = $queryBus;
        $this->metricsAggregator = $metricsAggregator;
        $this->configLoader      = $configLoader;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'URL of the Merge Request', null)
            ->addArgument('account', InputArgument::REQUIRED, 'Account ID', null)
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
        $accountId       = $input->getArgument('account');

        $config = $this->configLoader->load(new TenantId($accountId), $mergeRequestUrl->baseUrl);

        $getDetailsQuery = new GetDetailsQuery(
            config: $config,
            projectId: $mergeRequestUrl->projectId,
            mergeRequestIid: $mergeRequestUrl->mergeRequestIid,
            baseUrl: $mergeRequestUrl->getBaseApiV4Url(),
        );

        /** @var Details $mergeRequestDetails */
        $mergeRequestDetails = $this->handle($getDetailsQuery);

        $io->title($mergeRequestDetails->web_url);

        $metricResults = $this->metricsAggregator->getResult($config, $mergeRequestDetails);

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
