<?php

declare(strict_types=1);

namespace App\Tests\Gitlab\Client\MergeRequest\Model;

use App\Gitlab\Client\MergeRequest\Model\Details;

final class DetailsFixture
{
    public static function empty(): Details
    {
        $details          = new Details(
            0,
            'Fake Title',
            'https://gitlab.com'
        );
        $details->changes = ChangesFixture::empty();
        $details->threads = ThreadsFixture::empty();

        return $details;
    }

    public static function emptyThreads(): Details
    {
        $details          = new Details(
            0,
            'Fake Title',
            'https://gitlab.com'
        );
        $details->changes = ChangesFixture::default();
        $details->threads = ThreadsFixture::empty();

        return $details;
    }

    public static function default(int $numberOfThreads = 5, bool|int $threadsResolved = false): Details
    {
        $details          = new Details(
            0,
            'Fake Title',
            'https://gitlab.com'
        );
        $details->changes = ChangesFixture::default();
        $details->threads = ThreadsFixture::default(
            numberOfThreads: $numberOfThreads,
            resolved: $threadsResolved,
        );

        return $details;
    }

    public static function growingNumberOfNotes(int $numberOfThreads = 5, bool|int $threadsResolved = false): Details
    {
        $details          = new Details(
            0,
            'Fake Title',
            'https://gitlab.com'
        );
        $details->changes = ChangesFixture::default();
        $details->threads = ThreadsFixture::growingNumberOfNotes(
            numberOfThreads: $numberOfThreads,
            resolved: $threadsResolved,
        );

        return $details;
    }

    public static function full(): Details
    {
        $details          = new Details(
            0,
            'Fake Title',
            'https://gitlab.com'
        );
        $details->changes = ChangesFixture::default();
        $details->threads = ThreadsFixture::severityAndCategoryMatrix();

        return $details;
    }
}
