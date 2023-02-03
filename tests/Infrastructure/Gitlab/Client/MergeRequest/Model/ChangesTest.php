<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model;

use App\Domain\Git\Diff;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Changes;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @coversDefaultClass \App\Infrastructure\Gitlab\Client\MergeRequest\Model\Changes
 * @covers ::__construct
 */
final class ChangesTest extends TestCase
{
    /**
     * @return Generator<string, array{changes: Changes, expected: Diff}>
     */
    public function generateChangesForDiff(): Generator
    {
        yield 'empty' => [
            'changes'  => ChangesFixture::empty(),
            'expected' => new Diff(0, 0),
        ];

        yield 'some changes' => [
            'changes'  => ChangesFixture::default(),
            'expected' => new Diff(50, 25),
        ];
    }

    /**
     * @covers ::totalDiff()
     *
     * @uses         \App\Domain\Git\Diff
     *
     * @dataProvider generateChangesForDiff
     */
    public function testCanCalculateTheAddedAndRemovedLines(Changes $changes, Diff $expected): void
    {
        $totalDiff = $changes->totalDiff();
        $this->assertEquals($expected, $totalDiff);
        $this->assertSame($totalDiff, $changes->totalDiff(), 'Cache miss.');
    }

    /**
     * @return Generator<string, array{changes: Changes, expected: int}>
     */
    public function generateChangesForCount(): Generator
    {
        yield 'empty' => [
            'changes'  => ChangesFixture::empty(),
            'expected' => 0,
        ];

        yield 'some changes' => [
            'changes'  => ChangesFixture::default(),
            'expected' => 5,
        ];
    }

    /**
     * @covers ::count()
     *
     * @dataProvider generateChangesForCount
     */
    public function testItCanCountTheChanges(Changes $changes, int $expected): void
    {
        $this->assertCount($expected, $changes);
    }
}
