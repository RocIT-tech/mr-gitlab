<?php

declare(strict_types=1);

namespace App\Tests\Gitlab\Client\MergeRequest\Model\Thread;

use App\Gitlab\Client\MergeRequest\Model\Thread\Note;
use App\Gitlab\Client\MergeRequest\Model\Thread\NotePosition;
use App\Gitlab\Client\MergeRequest\Model\Thread\NoteType;

final class NoteFixture
{
    public static function default(
        string $note = 'This is a note.',
        bool   $resolved = false,
        bool   $system = false,
    ): Note {
        return new Note(
            id: random_int(0, 1_000_000),
            type: NoteType::TYPE_DIFF_NOTE,
            body: $note,
            position: new NotePosition('/some/file'),
            resolved: $resolved,
            system: $system,
        );
    }
}
