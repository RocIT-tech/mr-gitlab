<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model;

use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Change;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Changes;

final class ChangesFixture
{
    public static function empty(): Changes
    {
        return new Changes([]);
    }

    public static function default(): Changes
    {
        return new Changes([
            new Change('/file/1', new \App\Domain\Git\Diff(10, 5)),
            new Change('/file/2', new \App\Domain\Git\Diff(10, 5)),
            new Change('/file/3', new \App\Domain\Git\Diff(10, 5)),
            new Change('/file/4', new \App\Domain\Git\Diff(10, 5)),
            new Change('/file/5', new \App\Domain\Git\Diff(10, 5)),
        ]);
    }
}
