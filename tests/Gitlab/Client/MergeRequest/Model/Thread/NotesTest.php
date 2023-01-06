<?php

declare(strict_types=1);

namespace App\Tests\Gitlab\Client\MergeRequest\Model\Thread;

use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @coversDefaultClass \App\Gitlab\Client\MergeRequest\Model\Thread\Notes
 * @covers ::__construct
 *
 * @uses \App\Gitlab\Client\MergeRequest\Model\Thread\Note
 * @uses \App\Gitlab\Client\MergeRequest\Model\Thread\NotePosition
 */
final class NotesTest extends TestCase
{
    /**
     * @covers ::isEmpty()
     */
    public function testItIsEmpty(): void
    {
        $notes = NotesFixture::empty();
        $this->assertTrue($notes->isEmpty());
    }

    /**
     * @covers ::first()
     *
     * @uses \App\Gitlab\Client\MergeRequest\Model\Thread\Notes::isEmpty
     */
    public function testCannotCallFirstOnEmpty(): void
    {
        $notes = NotesFixture::empty();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Something went wrong.');

        $notes->first();
    }

    /**
     * @covers ::first()
     *
     * @uses \App\Gitlab\Client\MergeRequest\Model\Thread\Notes::isEmpty
     */
    public function testCanFetchTheFirstNoteIfNotEmpty(): void
    {
        $notes = NotesFixture::default();

        $note = $notes->first();
        $this->assertSame('[suggestion][typo, quality]This is a note.', $note->body);
    }

    /**
     * @covers ::count()
     */
    public function testCountTheNumberOfNotes(): void
    {
        $notes = NotesFixture::default(10);

        $this->assertCount(10, $notes);
    }
}
