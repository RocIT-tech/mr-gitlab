<?php

declare(strict_types=1);

namespace App\Domain\Metrics;

use App\Domain\Metrics\Calculator\Notes\ParseNoteTrait;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;
use WeakMap;
use function count;

final class StatsAggregator
{
    use ParseNoteTrait;

    /** @var WeakMap<Details, StatsResult> */
    private WeakMap $cache;

    public function __construct()
    {
        $this->cache = new WeakMap();
    }

    public function getResult(Details $mergeRequestDetails): StatsResult
    {
        return $this->cache[$mergeRequestDetails] ??= $this->calculateStats($mergeRequestDetails);
    }

    private function calculateStats(Details $mergeRequestDetails): StatsResult
    {
        $result                  = new StatsResult();
        $result->numberOfThreads = count($mergeRequestDetails->threads);

        $numberOfReplies = 0;
        foreach ($mergeRequestDetails->threads as $thread) {
            /** @var Thread $thread */
            if ($thread->isFullyResolved() === false) {
                $result->countUnresolvedThreads++;
            }

            $note = $thread->notes->first();
            [$severity, $categories] = $this->parseNoteForLabels($note->body);

            match ($severity) {
                Severity::SEVERITY_ALERT => ++$result->countSeverityAlert,
                Severity::SEVERITY_WARNING => ++$result->countSeverityWarning,
                Severity::SEVERITY_SUGGESTION => ++$result->countSeveritySuggestion,
                default => '',
            };

            foreach ($categories as $category) {
                match ($category) {
                    Category::CATEGORY_STABILITY => ++$result->countCategoryStability,
                    Category::CATEGORY_READABILITY => ++$result->countCategoryReadability,
                    Category::CATEGORY_TYPO => ++$result->countCategoryTypo,
                    Category::CATEGORY_PERFORMANCE => ++$result->countCategoryPerformance,
                    Category::CATEGORY_SECURITY => ++$result->countCategorySecurity,
                    Category::CATEGORY_MAINTAINABILITY => ++$result->countCategoryMaintainability,
                    Category::CATEGORY_QUALITY => ++$result->countCategoryQuality,
                    default => '',
                };
            }

            $countNotes = count($thread->notes);
            if ($countNotes > $result->maxCommentsOnThread) {
                $result->maxCommentsOnThread = $countNotes;
            }

            if ($countNotes > 1) {
                $numberOfReplies += $countNotes - 1;
            }
        }

        $result->numberOfReplies = $numberOfReplies;

        return $result;
    }
}
