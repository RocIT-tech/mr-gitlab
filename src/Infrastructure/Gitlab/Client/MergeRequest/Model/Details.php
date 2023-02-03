<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Client\MergeRequest\Model;

final class Details
{
    public Changes $changes;

    public Threads $threads;

    public function __construct(
        public readonly int    $id,
        public readonly string $title,
        public readonly string $web_url,
    ) {
    }
}
