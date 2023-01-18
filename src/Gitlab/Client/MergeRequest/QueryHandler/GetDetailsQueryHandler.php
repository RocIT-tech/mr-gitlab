<?php

declare(strict_types=1);

namespace App\Gitlab\Client\MergeRequest\QueryHandler;

use App\Gitlab\Client\MergeRequest\Model\Change;
use App\Gitlab\Client\MergeRequest\Model\Changes;
use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Gitlab\Client\MergeRequest\Model\Thread;
use App\Gitlab\Client\MergeRequest\Model\Threads;
use App\Gitlab\Client\MergeRequest\Query\GetDetailsQuery;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function array_merge;
use function sprintf;

final class GetDetailsQueryHandler
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(GetDetailsQuery $getDetailsQuery): Details
    {
        $getDetailsRequest  = $this->httpClient->request('GET', $getDetailsQuery->getDetailsUrl());
        $getChangesRequests = $this->getChanges($getDetailsQuery);
        $getThreadsRequests = $this->getThreads($getDetailsQuery);

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
    private function getThreads(GetDetailsQuery $getDetailsQuery): array
    {
        $getThreadsRequests = [];
        $page               = '1';

        do {
            $getThreadsCurrentRequest = $this->httpClient->request('GET', $getDetailsQuery->getThreadsUrl(), [
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
    private function getChanges(GetDetailsQuery $getDetailsQuery): array
    {
        $getChangesRequests = [];
        $page               = '1';

        do {
            $getChangesCurrentRequest = $this->httpClient->request('GET', $getDetailsQuery->getChangesUrl(), [
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
