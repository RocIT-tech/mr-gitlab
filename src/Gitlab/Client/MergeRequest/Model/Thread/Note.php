<?php

declare(strict_types=1);

namespace App\Gitlab\Client\MergeRequest\Model\Thread;

final class Note
{
    public function __construct(
        public readonly int          $id,
        public readonly NoteType     $type,
        public readonly string       $body,
        public readonly NotePosition $position,
        public readonly bool         $resolved = false,
        public readonly bool         $system = true,
    ) {
    }
}
