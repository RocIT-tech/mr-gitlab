<?php

declare(strict_types=1);

namespace App\Tests\Gitlab\Client\MergeRequest\Model;

use App\Gitlab\Client\MergeRequest\Model\Change;
use App\Gitlab\Client\MergeRequest\Model\Changes;

final class ChangesFixture
{
    public static function empty(): Changes
    {
        return new Changes([]);
    }

    public static function default(): Changes
    {
        return new Changes([
            new Change('/file/1', new Change\Diff(10, 5)),
            new Change('/file/2', new Change\Diff(10, 5)),
            new Change('/file/3', new Change\Diff(10, 5)),
            new Change('/file/4', new Change\Diff(10, 5)),
            new Change('/file/5', new Change\Diff(10, 5)),
        ]);
    }
}
