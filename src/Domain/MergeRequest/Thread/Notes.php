<?php

declare(strict_types=1);

namespace App\Domain\MergeRequest\Thread;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;
use function count;

/**
 * @implements IteratorAggregate<int, Note>
 */
final class Notes implements IteratorAggregate, Countable
{
    /**
     * @param Note[] $notes
     */
    public function __construct(private readonly array $notes)
    {
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->notes);
    }

    public function count(): int
    {
        return count($this->notes);
    }
}
