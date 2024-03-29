<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Calculator;

use App\Domain\Metrics\Metric;
use App\Domain\Metrics\MetricCalculatorInterface;
use App\Domain\Metrics\MetricResult;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use function count;

final class FilesChanged implements MetricCalculatorInterface
{
    public static function supportedMetric(): string
    {
        return Metric::FilesChanged->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 30';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $filesChanged = count($mergeRequestDetails->changes);

        return new MetricResult(
            currentValue: (string) $filesChanged,
        );
    }

    public static function getDefaultPriority(): int
    {
        return 70;
    }
}
