<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Calculator;

use App\Domain\Metrics\Metric;
use App\Domain\Metrics\MetricCalculatorInterface;
use App\Domain\Metrics\MetricResult;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use LogicException;
use function abs;
use function count;

final class LinesFilesRatio implements MetricCalculatorInterface
{
    public static function supportedMetric(): string
    {
        return Metric::LinesFilesRatio->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 40';
    }

    /**
     * @throws LogicException If you see this one please call me...
     */
    public function result(Details $mergeRequestDetails): MetricResult
    {
        if (0 === count($mergeRequestDetails->changes)) {
            throw new LogicException('How could your Merge Request have no files change but lines do... ? 🧐');
        }

        $totalDiff      = $mergeRequestDetails->changes->totalDiff();
        $linesFileRatio = abs(($totalDiff->added + $totalDiff->removed) / count($mergeRequestDetails->changes));

        return new MetricResult(
            currentValue: (string) $linesFileRatio,
        );
    }

    public static function getDefaultPriority(): int
    {
        return 80;
    }
}
