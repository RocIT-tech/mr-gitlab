<?php

declare(strict_types=1);

namespace App\Tests\Metrics;

use App\Metrics\StatsResult;
use ReflectionMethod;
use ReflectionParameter;
use function array_reduce;
use function func_get_args;

final class StatsResultFixture
{
    public static function default(): StatsResult
    {
        return new StatsResult();
    }

    public static function with(
        ?int $countSeverityAlert = null,
        ?int $countSeverityWarning = null,
        ?int $countSeveritySuggestion = null,
        ?int $countCategorySecurity = null,
        ?int $countCategoryPerformance = null,
        ?int $countCategoryReadability = null,
        ?int $countCategoryTypo = null,
        ?int $countCategoryMaintainability = null,
        ?int $countCategoryQuality = null,
        ?int $countCategoryStability = null,
        ?int $maxCommentsOnThread = null,
        ?int $numberOfReplies = null,
        ?int $numberOfThreads = null,
        ?int $countUnresolvedThreads = null,
    ): StatsResult {
        $reflectionMethod = new ReflectionMethod(__METHOD__);

        $args = func_get_args();

        $keyValuePair = array_reduce(
            $reflectionMethod->getParameters(),
            static function (array $keyValuePair, ReflectionParameter $reflectionParameter) use ($args) {
                if (($args[$reflectionParameter->getPosition()] ?? null) === null) {
                    return $keyValuePair;
                }

                $keyValuePair[$reflectionParameter->getName()] = $args[$reflectionParameter->getPosition()];

                return $keyValuePair;
            },
            [],
        );

        $statsResult = new StatsResult();
        foreach ($keyValuePair as $key => $value) {
            $statsResult->$key = $value;
        }

        return $statsResult;
    }
}
