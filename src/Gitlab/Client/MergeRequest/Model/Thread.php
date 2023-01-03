<?php

declare(strict_types=1);

namespace App\Gitlab\Client\MergeRequest\Model;

use App\Gitlab\Client\MergeRequest\Model\Thread\Note;
use App\Gitlab\Client\MergeRequest\Model\Thread\Notes;
use App\Gitlab\Client\MergeRequest\Model\Thread\NoteType;
use function array_filter;
use function array_reduce;
use function iterator_to_array;

final class Thread
{
    public int|string $id;

    public Notes $notes;

    public function isUserThread(): bool
    {
        return [] !== array_filter(iterator_to_array($this->notes), static function (Note $note): bool {
            return NoteType::TYPE_DIFF_NOTE === $note->type;
        });
    }

    public function isFullyResolved(): bool
    {
        return array_reduce(
            iterator_to_array($this->notes),
            static fn(bool $result, Note $note): bool => $result && $note->resolved,
            initial: true
        );
    }
}
