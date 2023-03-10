<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Client\MergeRequest\Model;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;
use function array_filter;
use function count;

/**
 * @implements IteratorAggregate<int, Thread>
 */
final class Threads implements IteratorAggregate, Countable
{
    /**
     * @var Thread[]
     */
    private array $threads;

    /**
     * @param Thread[] $threads
     */
    public function __construct(array $threads)
    {
        $this->threads = array_filter($threads, static function (Thread $thread): bool {
            return $thread->notes->isEmpty() === false;
        });
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
