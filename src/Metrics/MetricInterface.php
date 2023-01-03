<?php

declare(strict_types=1);

namespace App\Metrics;

use App\Gitlab\Client\MergeRequest\Model\Details;

interface MetricInterface
{
    public function name(): string;

    public function result(Details $mergeRequestDetails): MetricResult;
}
