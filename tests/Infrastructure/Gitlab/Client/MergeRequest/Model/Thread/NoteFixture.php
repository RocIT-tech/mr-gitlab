<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread;

use App\Domain\Metrics\Category;
use App\Domain\Metrics\Severity;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\Note;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\NotePosition;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Thread\NoteType;
use function array_map;
use function implode;
use function sprintf;
use function strtolower;

final class NoteFixture
{
    public static function default(
        ?string $note = null,
        bool    $resolved = false,
    ): Note {
        return new Note(
            id: random_int(0, 1_000_000),
            type: NoteType::TYPE_DIFF_NOTE,
            body: $note ?? self::noteBodyWith(
                severity: Severity::SEVERITY_SUGGESTION,
                categories: [
                    Category::CATEGORY_TYPO,
                    Category::CATEGORY_QUALITY,
                ],
            ),
            position: new NotePosition('/some/file'),
            resolved: $resolved,
        );
    }

    /**
     * @param Category[] $categories
     */
    public static function noteBodyWith(Severity $severity, array $categories, string $note = 'This is a note.'): string
    {
        return sprintf(
            '[%s][%s]%s',
            strtolower($severity->value),
            implode(', ', array_map(static fn(Category $category) => strtolower($category->value), $categories)),
            $note,
        );
    }
}
