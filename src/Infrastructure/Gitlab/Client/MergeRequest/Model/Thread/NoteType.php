<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;

enum NoteType: string
{
    case TYPE_DIFF_NOTE = 'DiffNote';
    case TYPE_DISCUSSION_NOTE = 'DiscussionNote';
    case TYPE_INDIVIDUAL_NOTE = 'IndividualNote';
}
