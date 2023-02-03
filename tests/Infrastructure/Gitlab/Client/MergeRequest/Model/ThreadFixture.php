<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model;

use App\Domain\Metrics\Category;
use App\Domain\Metrics\Severity;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;
use App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\NoteFixture;
use App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\NotesFixture;
use function is_array;
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
                note: NoteFixture::noteBodyWith(
                    severity: Severity::SEVERITY_SUGGESTION,
                    categories: [
                        Category::CATEGORY_READABILITY,
                    ],
                    note: "{$i}#This is a note.",
                ),
                resolved: $resolved > 0,
            );
            --$resolved;
        }
        $thread->notes = NotesFixture::with($notes);

        return $thread;
    }

    /**
     * @param \App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\Notes|array<int, \App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\Note>|null $notes
     */
    public static function with(\App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\Notes|array|null $notes = null): Thread
    {
        $thread        = new Thread();
        $thread->id    = '#thread-id#';
        $thread->notes = is_array($notes) ? NotesFixture::with($notes) : ($notes ?? NotesFixture::default(5));

        return $thread;
    }
}
