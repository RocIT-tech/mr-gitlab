<?php

declare(strict_types=1);

namespace App\Domain\Git;

final class Diff
{
    public function __construct(
        public int $removed = 0,
        public int $added = 0,
    ) {
    }
}
