<?php

declare(strict_types=1);

namespace App\Tests\Gitlab\Client\MergeRequest\Model;

use App\Gitlab\Client\MergeRequest\Model\Thread;
use App\Gitlab\Client\MergeRequest\Model\Threads;
use App\Tests\Gitlab\Client\MergeRequest\Model\Thread\NotesFixture;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @coversDefaultClass \App\Gitlab\Client\MergeRequest\Model\Threads
 */
final class ThreadsTest extends TestCase
{
    public function generateRelevantThreads(): Generator
    {
        yield 'empty' => [
            'threads' => [],
            'count' => 0,
        ];

        yield 'empty notes' => [
            'threads' => [
                ThreadFixture::with(NotesFixture::empty())
            ],
            'count' => 0,
        ];
    }

    /**
     * @dataProvider generateRelevantThreads
     *
     * @covers ::__construct()
     * @covers ::count()
     *
     * @uses \App\Gitlab\Client\MergeRequest\Model\Thread\Notes
     *
     * @param Thread[] $threads
     */
    public function testKeepsOnlyRelevantThreads(array $threads, int $count): void
    {
        $threadsModel = new Threads($threads);

        $this->assertCount($count, $threadsModel);
    }
}
