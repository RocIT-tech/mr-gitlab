<?php

declare(strict_types=1);

namespace App\Domain\MergeRequest;

enum Type: string
{
    case Gitlab = 'gitlab';
    case Github = 'github';
}
