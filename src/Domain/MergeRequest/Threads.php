<?php

declare(strict_types=1);

namespace App\Domain\MergeRequest;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;
use function count;

/**
 * @implements IteratorAggregate<int, Thread>
 */
final class Threads implements IteratorAggregate, Countable
{
    /**
     * @param Thread[] $threads
     */
    public function __construct(private array $threads)
    {
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->threads);
    }

    public function count(): int
    {
        return count($this->threads);
    }
}
