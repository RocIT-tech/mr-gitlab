<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Client\MergeRequest\QueryHandler;

use App\Infrastructure\Gitlab\Client\HttpClientFactory;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Change;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Changes;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Threads;
use App\Infrastructure\Gitlab\Client\MergeRequest\Query\GetDetailsQuery;
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
    ) {
    }

    public function __invoke(GetDetailsQuery $getDetailsQuery): Details
    {
        $gitlabClient = $this->httpClientFactory->create($getDetailsQuery->config);

        $getDetailsRequest  = $gitlabClient->request('GET', $getDetailsQuery->getDetailsUrl());
        $getChangesRequests = $this->getChanges($gitlabClient, $getDetailsQuery);
        $getThreadsRequests = $this->getThreads($gitlabClient, $getDetailsQuery);

        /** @var Details $details */
        $details = $this->serializer->deserialize(
            $getDetailsRequest->getContent(true),
            Details::class,
            JsonEncoder::FORMAT,
        );

        $changes = [];
        foreach ($getChangesRequests as $getChangesRequest) {
            $changes[] = $this->serializer->deserialize(
                $getChangesRequest->getContent(true),
                sprintf('%s[]', Change::class),
                JsonEncoder::FORMAT,
            );
        }
        $details->changes = new Changes(array_merge(...$changes));

        $threads = [];
        foreach ($getThreadsRequests as $getThreadsRequest) {
            $threads[] = $this->serializer->deserialize(
                $getThreadsRequest->getContent(true),
                sprintf('%s[]', Thread::class),
                JsonEncoder::FORMAT,
            );
        }

        $details->threads = new Threads(array_merge(...$threads));

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
