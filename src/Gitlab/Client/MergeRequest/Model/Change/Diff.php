<?php

declare(strict_types=1);

namespace App\Gitlab\Client\MergeRequest\Model\Change;

final class Diff
{
    public function __construct(
        public int $removed = 0,
        public int $added = 0
    ) {
    }
}
