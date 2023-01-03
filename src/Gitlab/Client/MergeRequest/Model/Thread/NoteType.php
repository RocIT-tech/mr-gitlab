<?php

declare(strict_types=1);

namespace App\Gitlab\Client\MergeRequest\Model\Thread;

enum NoteType: string
{
    case TYPE_DIFF_NOTE = 'DiffNote';
}
