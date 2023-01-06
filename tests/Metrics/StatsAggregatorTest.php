<?php

declare(strict_types=1);

namespace App\Tests\Metrics;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\StatsAggregator;
use App\Metrics\StatsResult;
use App\Tests\Gitlab\Client\MergeRequest\Model\DetailsFixture;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @coversDefaultClass \App\Metrics\StatsAggregator
 * @covers ::__construct()
 * @covers ::getResult()
 * @covers ::calculateStats()
 * @covers \App\Metrics\Gitlab\Notes\ParseNoteTrait::parseNoteForLabels
 *
 * @uses  \App\Gitlab\Client\MergeRequest\Model\Details
 * @uses  \App\Gitlab\Client\MergeRequest\Model\Thread
 * @uses  \App\Gitlab\Client\MergeRequest\Model\Threads
 * @uses  \App\Gitlab\Client\MergeRequest\Model\Thread\Notes
 * @uses  \App\Metrics\Category
 * @uses  \App\Metrics\Severity
 */
final class StatsAggregatorTest extends TestCase
{
    public function generateMergeRequestDetails(): Generator
    {
        yield 'empty details' => [
            'details' => DetailsFixture::empty(),
            'result'  => StatsResultFixture::default(),
        ];

        yield 'empty threads and 5 changes' => [
            'details' => DetailsFixture::emptyThreads(),
            'result'  => StatsResultFixture::default(),
        ];

        yield '5 threads (3 resolved) and 5 changes' => [
            'details' => DetailsFixture::default(
                numberOfThreads: 5,
                threadsResolved: 3,
            ),
            'result'  => StatsResultFixture::with(
                countSeveritySuggestion: 5,
                countCategoryReadability: 5,
                maxCommentsOnThread: 5,
                numberOfReplies: 20,
                numberOfThreads: 5,
                countUnresolvedThreads: 2,
            ),
        ];

        yield '5 threads (3 resolved) with growing number of notes and 5 changes' => [
            'details' => DetailsFixture::growingNumberOfNotes(
                numberOfThreads: 5,
                threadsResolved: 3,
            ),
            'result'  => StatsResultFixture::with(
                countSeveritySuggestion: 5,
                countCategoryReadability: 5,
                maxCommentsOnThread: 5,
                numberOfReplies: 10,
                numberOfThreads: 5,
                countUnresolvedThreads: 2,
            ),
        ];

        yield '5 threads (all resolved) and 5 changes' => [
            'details' => DetailsFixture::default(
                numberOfThreads: 5,
                threadsResolved: true,
            ),
            'result'  => StatsResultFixture::with(
                countSeveritySuggestion: 5,
                countCategoryReadability: 5,
                maxCommentsOnThread: 5,
                numberOfReplies: 20,
                numberOfThreads: 5,
            ),
        ];

        yield '5 threads (none resolved) and 5 changes' => [
            'details' => DetailsFixture::default(
                numberOfThreads: 5,
                threadsResolved: false,
            ),
            'result'  => StatsResultFixture::with(
                countSeveritySuggestion: 5,
                countCategoryReadability: 5,
                maxCommentsOnThread: 5,
                numberOfReplies: 20,
                numberOfThreads: 5,
                countUnresolvedThreads: 5,
            ),
        ];

        yield 'as many threads required by the severities and categories matrix' => [
            'details' => DetailsFixture::full(),
            'result'  => StatsResultFixture::with(
                countSeverityAlert: 1,
                countSeverityWarning: 1,
                countSeveritySuggestion: 1,
                countCategorySecurity: 3,
                countCategoryPerformance: 3,
                countCategoryReadability: 3,
                countCategoryTypo: 3,
                countCategoryMaintainability: 3,
                countCategoryQuality: 3,
                countCategoryStability: 3,
                maxCommentsOnThread: 1,
                numberOfReplies: 0,
                numberOfThreads: 3,
                countUnresolvedThreads: 3,
            ),
        ];
    }

    /**
     * @dataProvider generateMergeRequestDetails
     */
    public function testItCalculateTheStats(Details $details, StatsResult $result): void
    {
        $statsAggregator  = new StatsAggregator();
        $calculatedResult = $statsAggregator->getResult($details);

        $this->assertEquals($result, $calculatedResult);
        $this->assertSame($calculatedResult, $statsAggregator->getResult($details));
    }
}
