<?php

declare(strict_types=1);

namespace App\Tests\Gitlab\Client\MergeRequest\Model;

use App\Gitlab\Client\MergeRequest\Model\Thread;
use App\Tests\Gitlab\Client\MergeRequest\Model\Thread\NoteFixture;
use App\Tests\Gitlab\Client\MergeRequest\Model\Thread\NotesFixture;
use function is_int;

final class ThreadFixture
{
    public static function default(int $numberOfNotes = 5, bool|int $resolved = false): Thread
    {
        $thread     = new Thread();
        $thread->id = '#thread-id#';

        if (is_int($resolved) === false) {
            $resolved = true === $resolved ? $numberOfNotes : 0;
        }

        $notes = [];
        for ($i = 0; $i < $numberOfNotes; $i++) {
            $notes[] = NoteFixture::default(
                note: "{$i}#This is a note.",
                resolved: $resolved > 0,
            );
            --$resolved;
        }
        $thread->notes = NotesFixture::with($notes);

        return $thread;
    }
}
