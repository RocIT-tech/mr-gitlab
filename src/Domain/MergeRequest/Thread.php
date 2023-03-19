<?php

declare(strict_types=1);

namespace App\Domain\MergeRequest;

use App\Domain\MergeRequest\Thread\Notes;

final class Thread
{
    public function __construct(
        public readonly int|string $id,
        public readonly Notes      $notes,
    ) {
    }
}
