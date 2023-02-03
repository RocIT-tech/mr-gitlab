<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Client\MergeRequest\Model;

use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\Note;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\Notes;
use function array_reduce;
use function iterator_to_array;

final class Thread
{
    public int|string $id;

    public Notes $notes;

    public function isFullyResolved(): bool
    {
        return array_reduce(
            iterator_to_array($this->notes),
            static fn(bool $result, Note $note): bool => $result && $note->resolved,
            true,
        );
    }
}
