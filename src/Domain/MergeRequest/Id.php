<?php

declare(strict_types=1);

namespace App\Domain\MergeRequest;

use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Ulid;
use function is_string;

final class Id
{
    public readonly string $value;

    public function __construct(string|Ulid $value)
    {
        if (is_string($value) === true) {
            $value = new Ulid($value);
        }

        $this->value = $value->toRfc4122();
    }

    public static function generate(ClockInterface $clock): self
    {
        return new self(Ulid::generate($clock->now()));
    }
}
