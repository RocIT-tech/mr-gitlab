<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model;

use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @coversDefaultClass \App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread
 */
final class ThreadTest extends TestCase
{
    /**
     * @return Generator<string, array{thread: Thread, fullyResolved: bool}>
     */
    public function generateThreadToTestIfFullyResolved(): Generator
    {
        yield 'not resolved at all' => [
            'thread'        => ThreadFixture::default(
                numberOfNotes: 5,
                resolved: false,
            ),
            'fullyResolved' => false,
        ];

        yield 'partially resolved' => [
            'thread'        => ThreadFixture::default(
                numberOfNotes: 5,
                resolved: 3,
            ),
            'fullyResolved' => false,
        ];

        yield 'fully resolved' => [
            'thread'        => ThreadFixture::default(
                numberOfNotes: 5,
                resolved: true,
            ),
            'fullyResolved' => true,
        ];
    }

    /**
     * @covers ::isFullyResolved()
     *
     * @dataProvider generateThreadToTestIfFullyResolved
     *
     * @uses         \App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\Notes::getIterator
     */
    public function testIsFullyResolved(Thread $thread, bool $fullyResolved): void
    {
        $this->assertSame($fullyResolved, $thread->isFullyResolved());
    }
}
