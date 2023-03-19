<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Client\MergeRequest\QueryHandler;

use App\Domain\MergeRequest\Id;
use App\Domain\MergeRequest\MergeRequest;
use App\Domain\MergeRequest\Metadata;
use App\Domain\MergeRequest\Type;
use App\Infrastructure\Gitlab\Client\HttpClientFactory;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Change;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Changes;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Threads;
use App\Infrastructure\Gitlab\Client\MergeRequest\Query\GetDetailsQuery;
use App\Infrastructure\Gitlab\Serializer\GitlabNormalizer;
use Psr\Clock\ClockInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function array_merge;
use function sprintf;

final class GetDetailsQueryHandler
{
    public function __construct(
        private readonly HttpClientFactory   $httpClientFactory,
        private readonly SerializerInterface $serializer,
        private readonly ClockInterface $clock,
    ) {
    }

    public function __invoke(GetDetailsQuery $getDetailsQuery): Details
    {
        $gitlabClient = $this->httpClientFactory->create($getDetailsQuery->config);

        $getDetailsRequest  = $gitlabClient->request('GET', $getDetailsQuery->getDetailsUrl());
        $getChangesRequests = $this->getChanges($gitlabClient, $getDetailsQuery);
        $getThreadsRequests = $this->getThreads($gitlabClient, $getDetailsQuery);

//        dd($getDetailsRequest->toArray(true));

        /** @var Details $details */
        $details = $this->serializer->deserialize(
            $getDetailsRequest->getContent(true),
            Details::class,
            JsonEncoder::FORMAT,
            [GitlabNormalizer::CONTEXT => true],
        );

        $changes = [];
        foreach ($getChangesRequests as $getChangesRequest) {
            $changes[] = $this->serializer->deserialize(
                $getChangesRequest->getContent(true),
                sprintf('%s[]', Change::class),
                JsonEncoder::FORMAT,
                [GitlabNormalizer::CONTEXT => true],
            );
        }
        $details->changes = new Changes(array_merge(...$changes));

        $threads = [];
        foreach ($getThreadsRequests as $getThreadsRequest) {
            $threads[] = $this->serializer->deserialize(
                $getThreadsRequest->getContent(true),
                sprintf('%s[]', Thread::class),
                JsonEncoder::FORMAT,
                [GitlabNormalizer::CONTEXT => true],
            );
        }

        $details->threads = new Threads(array_merge(...$threads));

//        $result = new MergeRequest(
//            Id::generate($this->clock),
//            $details->title,
//            $openedAt, // TODO
//            $description, // TODO
//            new Metadata( // TODO : add project id
//                Type::Gitlab,
//                $getDetailsQuery->mergeRequestIid,
//                $getDetailsQuery->getBaseUrl(), // TODO
//            ),
//        );

        return $details;
    }

    /**
     * @return ResponseInterface[]
     */
    private function getThreads(HttpClientInterface $gitlabClient, GetDetailsQuery $getDetailsQuery): array
    {
        $getThreadsRequests = [];
        $page               = '1';

        do {
            $getThreadsCurrentRequest = $gitlabClient->request('GET', $getDetailsQuery->getThreadsUrl(), [
                'query' => [
                    'page' => $page,
                ],
            ]);

            $getThreadsRequests[] = $getThreadsCurrentRequest;

            $threadsNextPage = $getThreadsCurrentRequest->getHeaders(true)['x-next-page'][0];

            $page = $threadsNextPage;
        } while ('' !== $threadsNextPage);

        return $getThreadsRequests;
    }

    /**
     * @return ResponseInterface[]
     */
    private function getChanges(HttpClientInterface $gitlabClient, GetDetailsQuery $getDetailsQuery): array
    {
        $getChangesRequests = [];
        $page               = '1';

        do {
            $getChangesCurrentRequest = $gitlabClient->request('GET', $getDetailsQuery->getChangesUrl(), [
                'query' => [
                    'page' => $page,
                ],
            ]);

            $getChangesRequests[] = $getChangesCurrentRequest;

            $threadsNextPage = $getChangesCurrentRequest->getHeaders(true)['x-next-page'][0];

            $page = $threadsNextPage;
        } while ('' !== $threadsNextPage);

        return $getChangesRequests;
    }
}
