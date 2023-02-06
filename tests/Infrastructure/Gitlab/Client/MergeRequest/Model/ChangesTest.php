<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model;

use App\Domain\Git\Diff;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Change;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Changes;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[CoversClass(Changes::class)]
#[UsesClass(Diff::class)]
final class ChangesTest extends TestCase
{
    /**
     * @return Generator<string, array{changes: array<int, Change>, expected: Diff}>
     */
    public static function generateChangesForDiff(): Generator
    {
        yield 'empty' => [
            'changes'  => ChangesFixture::empty(true),
            'expected' => new Diff(0, 0),
        ];

        yield 'some changes' => [
            'changes'  => ChangesFixture::default(true),
            'expected' => new Diff(50, 25),
        ];
    }

    /**
     * @param array<int, Change> $changes
     */
    #[DataProvider('generateChangesForDiff')]
    public function testCanCalculateTheAddedAndRemovedLines(array $changes, Diff $expected): void
    {
        $changes = new Changes($changes);
        $totalDiff = $changes->totalDiff();
        $this->assertEquals($expected, $totalDiff);
        $this->assertSame($totalDiff, $changes->totalDiff(), 'Cache miss.');
    }

    /**
     * @return Generator<string, array{changes: array<int, Change>, expected: int}>
     */
    public static function generateChangesForCount(): Generator
    {
        yield 'empty' => [
            'changes'  => ChangesFixture::empty(true),
            'expected' => 0,
        ];

        yield 'some changes' => [
            'changes'  => ChangesFixture::default(true),
            'expected' => 5,
        ];
    }

    /**
     * @param array<int, Change> $changes
     */
    #[DataProvider('generateChangesForCount')]
    public function testItCanCountTheChanges(array $changes, int $expected): void
    {
        $changes = new Changes($changes);
        $this->assertCount($expected, $changes);
    }
}
