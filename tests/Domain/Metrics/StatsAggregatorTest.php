<?php

declare(strict_types=1);

namespace App\Tests\Domain\Metrics;

use App\Domain\Metrics\Category;
use App\Domain\Metrics\Gitlab\Notes\ParseNoteTrait;
use App\Domain\Metrics\Severity;
use App\Domain\Metrics\StatsAggregator;
use App\Domain\Metrics\StatsResult;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\Notes;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Threads;
use App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model\DetailsFixture;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[CoversClass(StatsAggregator::class)]
//#[CoversFunction('__construct()')]
//#[CoversFunction('getResult()')]
//#[CoversFunction('calculateStats()')]
#[CoversClass(ParseNoteTrait::class)] // '::parseNoteForLabels()'
#[UsesClass(Details::class)]
#[UsesClass(Thread::class)]
#[UsesClass(Threads::class)]
#[UsesClass(Notes::class)]
#[UsesClass(Category::class)]
#[UsesClass(Severity::class)]
final class StatsAggregatorTest extends TestCase
{
    public static function generateMergeRequestDetails(): Generator
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

    #[DataProvider('generateMergeRequestDetails')]
    public function testItCalculateTheStats(Details $details, StatsResult $result): void
    {
        $statsAggregator  = new StatsAggregator();
        $calculatedResult = $statsAggregator->getResult($details);

        $this->assertEquals($result, $calculatedResult);
        $this->assertSame($calculatedResult, $statsAggregator->getResult($details));
    }
}
