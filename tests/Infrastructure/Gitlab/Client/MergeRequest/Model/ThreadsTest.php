<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model;

use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\Notes;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Threads;
use App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\NotesFixture;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[CoversClass(Threads::class)]
#[UsesClass(Notes::class)]
final class ThreadsTest extends TestCase
{
    public static function generateRelevantThreads(): Generator
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
     * @param Thread[] $threads
     */
    #[DataProvider('generateRelevantThreads')]
    //#[CoversFunction('__construct()')]
    //#[CoversFunction('count()')]
    public function testKeepsOnlyRelevantThreads(array $threads, int $count): void
    {
        $threadsModel = new Threads($threads);

        $this->assertCount($count, $threadsModel);
    }
}
