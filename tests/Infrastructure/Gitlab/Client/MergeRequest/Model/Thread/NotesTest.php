<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;

use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\Note;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\NotePosition;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\Notes;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[CoversClass(Notes::class)]
//#[CoversFunction('__construct()')]
#[UsesClass(Note::class)]
#[UsesClass(NotePosition::class)]
final class NotesTest extends TestCase
{
    //#[CoversFunction('isEmpty()')]
    public function testItIsEmpty(): void
    {
        $notes = NotesFixture::empty();
        $this->assertTrue($notes->isEmpty());
    }

    //#[CoversFunction('first()')]
    public function testCannotCallFirstOnEmpty(): void
    {
        $notes = NotesFixture::empty();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Something went wrong.');

        $notes->first();
    }

    //#[CoversFunction('first()')]
    public function testCanFetchTheFirstNoteIfNotEmpty(): void
    {
        $notes = NotesFixture::default();

        $note = $notes->first();
        $this->assertSame('[suggestion][typo, quality]This is a note.', $note->body);
    }

    //#[CoversFunction('count()')]
    public function testCountTheNumberOfNotes(): void
    {
        $notes = NotesFixture::default(10);

        $this->assertCount(10, $notes);
    }
}
