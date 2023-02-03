<?php

declare(strict_types=1);

namespace App\Domain\Metrics;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;
use function count;

/** @implements IteratorAggregate<string, ValidatedMetric> */
final class ValidatedMetrics implements IteratorAggregate, Countable
{
    /**
     * @var array<string, ValidatedMetric>
     */
    private array $validatedMetrics;

    private int $success;

    private int $failure;

    /**
     * @param iterable<string, ValidatedMetric> $validatedMetrics
     */
    public function __construct(
        public readonly StatsResult $stats,
        iterable                    $validatedMetrics,
    ) {
        $this->success = 0;
        $this->failure = 0;
        $this->aggregate($validatedMetrics);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->validatedMetrics);
    }

    public function count(): int
    {
        return count($this->validatedMetrics);
    }

    /**
     * @param iterable<string, ValidatedMetric> $validatedMetrics
     */
    private function aggregate(iterable $validatedMetrics): void
    {
        foreach ($validatedMetrics as $key => $validatedMetric) {
            $this->validatedMetrics[$key] = $validatedMetric;
            match ($validatedMetric->success) {
                true  => $this->success++,
                false => $this->failure++,
            };
        }
    }

    public function countSuccess(): int
    {
        return $this->success;
    }

    public function countFailure(): int
    {
        return $this->failure;
    }
}
