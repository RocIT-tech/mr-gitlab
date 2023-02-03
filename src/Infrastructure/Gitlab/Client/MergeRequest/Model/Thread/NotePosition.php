<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;

final class NotePosition
{
    public function __construct(
        public readonly string $new_path
    ) {
    }
}
