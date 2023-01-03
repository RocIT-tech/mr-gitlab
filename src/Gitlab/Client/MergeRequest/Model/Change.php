<?php

declare(strict_types=1);

namespace App\Gitlab\Client\MergeRequest\Model;

use App\Gitlab\Client\MergeRequest\Model\Change\Diff;

final class Change
{
    public function __construct(
        public readonly string $new_path,
        public readonly Diff   $diff
    ) {
    }
}
