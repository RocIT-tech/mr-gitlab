<?php

declare(strict_types=1);

namespace App\Tests\Gitlab\Client\MergeRequest\Model;

use App\Gitlab\Client\MergeRequest\Model\Threads;
use App\Metrics\Category;
use App\Metrics\Severity;
use App\Tests\Gitlab\Client\MergeRequest\Model\Thread\NoteFixture;
use App\Tests\Gitlab\Client\MergeRequest\Model\Thread\NotesFixture;
use function array_filter;
use function array_map;
use function implode;
use function is_int;
use function sprintf;
use function strtolower;

final class ThreadsFixture
{
    public static function empty(): Threads
    {
        return new Threads([]);
    }

    public static function default(int $numberOfThreads = 5, bool|int $resolved = false): Threads
    {
        if (is_int($resolved) === false) {
            $resolved = true === $resolved ? $numberOfThreads : 0;
        }

        $threads = [];
        for ($i = 0; $i < $numberOfThreads; $i++) {
            $threads[] = ThreadFixture::default(
                numberOfNotes: 5,
                resolved: $resolved > 0,
            );
            --$resolved;
        }

        return new Threads($threads);
    }

    public static function growingNumberOfNotes(int $numberOfThreads = 5, bool|int $resolved = false): Threads
    {
        if (is_int($resolved) === false) {
            $resolved = true === $resolved ? $numberOfThreads : 0;
        }

        $threads = [];
        for ($i = 0; $i < $numberOfThreads; $i++) {
            $threads[] = ThreadFixture::default(
                numberOfNotes: ($i + 1),
                resolved: $resolved > 0,
            );
            --$resolved;
        }

        return new Threads($threads);
    }

    public static function severityAndCategoryMatrix(): Threads
    {
        $threads = [];
        foreach (Severity::cases() as $severity) {
            if (Severity::SEVERITY_NONE === $severity) {
                continue;
            }

            $categoryCases = array_filter(Category::cases(), static fn(Category $category): bool => $category !== Category::CATEGORY_NONE);
            $note = sprintf(
                '[%s][%s] This is a note.',
                strtolower($severity->value),
                implode(', ', array_map(static fn(Category $category): string => strtolower($category->value), $categoryCases)),
            );

            $threads[] = ThreadFixture::with(NotesFixture::with([NoteFixture::default(note: $note)]));
        }

        return new Threads($threads);
    }
}
