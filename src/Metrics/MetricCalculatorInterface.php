<?php

declare(strict_types=1);

namespace App\Metrics;

use App\Gitlab\Client\MergeRequest\Model\Details;

interface MetricCalculatorInterface
{
    public function name(): string;

    public function description(): string;

    public function getDefaultConstraint(): string;

    public function result(Details $mergeRequestDetails): MetricResult;
}
