<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model;

use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Change;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Changes;

final class ChangesFixture
{
    /**
     * @return ($asArray is true ? array<int, Change> : Changes)
     */
    public static function empty(bool $asArray = false): Changes|array
    {
        $changesAsArray = [];

        return true === $asArray ? $changesAsArray : new Changes($changesAsArray);
    }

    /**
     * @return ($asArray is true ? array<int, Change> : Changes)
     */
    public static function default(bool $asArray = false): Changes|array
    {
        $changesAsArray = [
            new Change('/file/1', new \App\Domain\Git\Diff(10, 5)),
            new Change('/file/2', new \App\Domain\Git\Diff(10, 5)),
            new Change('/file/3', new \App\Domain\Git\Diff(10, 5)),
            new Change('/file/4', new \App\Domain\Git\Diff(10, 5)),
            new Change('/file/5', new \App\Domain\Git\Diff(10, 5)),
        ];

        return true === $asArray ? $changesAsArray : new Changes($changesAsArray);
    }
}
