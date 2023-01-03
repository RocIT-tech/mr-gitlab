<?php

declare(strict_types=1);

namespace App\Tests\Gitlab\Client\MergeRequest\Model\Thread;

use App\Gitlab\Client\MergeRequest\Model\Thread\Note;
use App\Gitlab\Client\MergeRequest\Model\Thread\Notes;
use function array_fill;

final class NotesFixture
{
    /**
     * @param array<Note> $notes
     */
    public static function with(array $notes): Notes
    {
        return new Notes($notes);
    }

    public static function empty(): Notes
    {
        return new Notes([]);
    }

    public static function default(int $numberOfElements = 1): Notes
    {
        return new Notes(array_fill(0, $numberOfElements, NoteFixture::default()));
    }
}
