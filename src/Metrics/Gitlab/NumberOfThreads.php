<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricCalculatorInterface;
use App\Metrics\MetricResult;
use function count;

final class NumberOfThreads implements MetricCalculatorInterface
{
    public function name(): string
    {
        return 'Number of Threads';
    }

    public function description(): string
    {
        return 'Nombre de threads ouvert';
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 30';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $numberOfThreads = count($mergeRequestDetails->threads);

        return new MetricResult(
            currentValue: (string) $numberOfThreads,
        );
    }

    public static function getDefaultPriority(): int
    {
        return 100;
    }
}
