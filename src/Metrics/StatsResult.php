<?php

declare(strict_types=1);

namespace App\Metrics;

final class StatsResult
{
    public int $countSeverityAlert = 0;
    public int $countSeverityWarning = 0;
    public int $countSeveritySuggestion = 0;

    public int $countCategorySecurity = 0;
    public int $countCategoryPerformance = 0;
    public int $countCategoryReadability = 0;
    public int $countCategoryTypo = 0;
    public int $countCategoryMaintainability = 0;
    public int $countCategoryQuality = 0;
    public int $countCategoryStability = 0;

    public int $maxCommentsOnThread = 1;

    public int $numberOfReplies = 0;
    public int $numberOfThreads = 0;

    public int $countUnresolvedThreads = 0;
}
