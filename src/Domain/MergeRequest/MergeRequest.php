<?php

declare(strict_types=1);

namespace App\Domain\MergeRequest;

use DateTimeImmutable;

final class MergeRequest
{
    public function __construct(
        public readonly Id                $id,
        public readonly string            $title,
        public readonly DateTimeImmutable $openedAt,
        public readonly string            $description,
        public readonly Metadata          $metadata,
    ) {
    }
}
