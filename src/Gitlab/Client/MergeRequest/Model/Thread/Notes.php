<?php

declare(strict_types=1);

namespace App\Gitlab\Client\MergeRequest\Model\Thread;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;
use function count;
use function reset;

final class Notes implements IteratorAggregate, Countable
{
    /**
     * @param Note[] $notes
     */
    public function __construct(private array $notes)
    {
    }

    public function isEmpty(): bool
    {
        return [] === $this->notes;
    }

    public function first(): Note
    {
        return reset($this->notes);
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
