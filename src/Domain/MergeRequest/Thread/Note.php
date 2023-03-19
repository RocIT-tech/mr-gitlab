<?php

declare(strict_types=1);

namespace App\Domain\MergeRequest\Thread;

final class Note
{
    public function __construct(
        public readonly int    $id,
        public readonly string $body,
        public readonly bool   $resolved = false,
    ) {
    }
}
