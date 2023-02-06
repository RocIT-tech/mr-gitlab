<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model;

use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[CoversClass(Thread::class)]
#[UsesClass(Thread\Notes::class)] // ::getIterator
final class ThreadTest extends TestCase
{
    /**
     * @return Generator<string, array{thread: Thread, fullyResolved: bool}>
     */
    public static function generateThreadToTestIfFullyResolved(): Generator
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

    #[DataProvider('generateThreadToTestIfFullyResolved')]
    //#[CoversFunction('isFullyResolved()')]
    public function testIsFullyResolved(Thread $thread, bool $fullyResolved): void
    {
        $this->assertSame($fullyResolved, $thread->isFullyResolved());
    }
}
