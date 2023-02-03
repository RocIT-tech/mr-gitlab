<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Client\MergeRequest\Model;

use App\Domain\Git\Diff;
use Countable;
use function array_reduce;
use function count;

final class Changes implements Countable
{
    /**
     * @param array<Change> $changes
     */
    public function __construct(
        public readonly array $changes,
    ) {
    }

    private ?Diff $diff = null;

    public function totalDiff(): Diff
    {
        return $this->diff ??= array_reduce($this->changes, static function (Diff $diff, Change $change): Diff {
            $diff->removed += $change->diff->removed;
            $diff->added   += $change->diff->added;

            return $diff;
        }, new Diff());
    }

    public function count(): int
    {
        return count($this->changes);
    }
}
