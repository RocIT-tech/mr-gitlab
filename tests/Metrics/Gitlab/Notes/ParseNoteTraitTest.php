<?php

declare(strict_types=1);

namespace App\Tests\Metrics\Gitlab\Notes;

use App\Metrics\Category;
use App\Metrics\Gitlab\Notes\ParseNoteTrait;
use App\Metrics\Severity;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @coversDefaultClass \App\Metrics\Gitlab\Notes\ParseNoteTrait
 * @covers ::parseNoteForLabels()
 *
 * @uses \App\Metrics\Category
 * @uses \App\Metrics\Severity
 */
final class ParseNoteTraitTest extends TestCase
{
    public function generateNotesToTest(): Generator
    {
        yield 'no anchors' => [
            'note'       => 'This is a simple note.',
            'severity'   => Severity::SEVERITY_NONE,
            'categories' => [Category::CATEGORY_NONE],
        ];

        yield 'simple phrase' => [
            'note'       => 'My [suggestion] is to use x instead of y to avoid any [performance] issue.',
            'severity'   => Severity::SEVERITY_SUGGESTION,
            'categories' => [Category::CATEGORY_PERFORMANCE],
        ];

        yield 'simple phrase with spaces in anchors' => [
            'note'       => 'My [  suggestion  ] is to use x instead of y to avoid any [ performance ] issue.',
            'severity'   => Severity::SEVERITY_SUGGESTION,
            'categories' => [Category::CATEGORY_PERFORMANCE],
        ];

        yield 'multiple severities and no categories' => [
            'note'       => '[suggestion][warning][alert]',
            'severity'   => Severity::SEVERITY_ALERT,
            'categories' => [Category::CATEGORY_NONE],
        ];

        yield 'multiple categories and no severities' => [
            'note'       => '[security][performance][typo]',
            'severity'   => Severity::SEVERITY_NONE,
            'categories' => [Category::CATEGORY_SECURITY, Category::CATEGORY_PERFORMANCE, Category::CATEGORY_TYPO],
        ];

        yield 'multiple labels in one anchor' => [
            'note'       => '[security,performance,warning]',
            'severity'   => Severity::SEVERITY_WARNING,
            'categories' => [Category::CATEGORY_SECURITY, Category::CATEGORY_PERFORMANCE],
        ];

        yield 'multiple labels in multiple anchors' => [
            'note'       => '[security,performance,warning] and also [alert,typo]',
            'severity'   => Severity::SEVERITY_ALERT,
            'categories' => [Category::CATEGORY_SECURITY, Category::CATEGORY_PERFORMANCE, Category::CATEGORY_TYPO],
        ];
    }

    /**
     * @dataProvider generateNotesToTest
     *
     * @param Category[] $categories
     */
    public function testCanParseNoteForLabels(string $note, Severity $severity, array $categories): void
    {
        $fixture = $this->buildFixture();

        [$severityParsed, $categoriesParsed] = $fixture->parse($note);
        $this->assertSame($severity, $severityParsed);
        $this->assertSame($categories, $categoriesParsed);
    }

    private function buildFixture(): object
    {
        return new class {
            use ParseNoteTrait;

            public function parse(string $note): array
            {
                return $this->parseNoteForLabels($note);
            }
        };
    }
}
