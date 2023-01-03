<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab\Notes;

use App\Metrics\Category;
use App\Metrics\Severity;
use function array_map;
use function array_reduce;
use function explode;
use function strtoupper;
use function trim;

trait ParseNoteTrait
{
    /**
     * @return array{Severity, Category[]}
     */
    private function parseNoteForLabels(string $note): array
    {
        $matches         = null;
        $numberOfMatches = preg_match_all('#\[(?P<labels>[^\]]*)\]#', $note, $matches);

        if (false === $numberOfMatches || 0 === $numberOfMatches) {
            return [Severity::SEVERITY_NONE, [Category::CATEGORY_NONE]];
        }

        $labels = array_reduce(
            $matches['labels'],
            static function (array $labels, string $label): array {
                $label     = strtoupper($label);
                $labelList = explode(',', $label);
                $labelList = array_map(
                    static fn(string $label): string => trim($label),
                    $labelList
                );

                return [...$labels, ...$labelList];
            },
            []
        );

        $severity   = Severity::SEVERITY_NONE;
        $categories = [];

        foreach ($labels as $label) {
            if (Severity::has($label) === true) {
                $severity = Severity::from($label);
                continue;
            }

            if (Category::hasMap($label) === true) {
                $categories[] = Category::tryMap($label);
                continue;
            }
        }

        if ([] === $categories) {
            $categories = [Category::CATEGORY_NONE];
        }

        return [$severity, $categories];
    }
}
