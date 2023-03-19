<?php

declare(strict_types=1);

namespace App\Domain\MergeRequest;

final class Metadata
{
    public function __construct(
        public readonly Type   $type,
        public readonly string $id,
        public readonly string $canonicalUrl,
    ) {
    }
}
