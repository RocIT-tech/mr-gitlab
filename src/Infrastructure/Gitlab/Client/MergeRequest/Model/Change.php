<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Client\MergeRequest\Model;

use App\Domain\Git\Diff;

final class Change
{
    public function __construct(
        public readonly string $new_path,
        public readonly Diff   $diff
    ) {
    }
}
