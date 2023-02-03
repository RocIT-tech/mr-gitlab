<?php

declare(strict_types=1);

namespace App\Domain\Metrics;

use ValueError;
use function preg_replace;
use function strtolower;
use function trim;

enum Category: string
{
    private const SEVERITY_MAPPING = [
        'securite'       => Category::CATEGORY_SECURITY,
        'performance'    => Category::CATEGORY_PERFORMANCE,
        'lisibilite'     => Category::CATEGORY_READABILITY,
        'question'       => Category::CATEGORY_READABILITY,
        'questions'      => Category::CATEGORY_READABILITY,
        'maintenance'    => Category::CATEGORY_MAINTAINABILITY,
        'maintenabilite' => Category::CATEGORY_MAINTAINABILITY,
        'maintenability' => Category::CATEGORY_MAINTAINABILITY,
        'qualite'        => Category::CATEGORY_QUALITY,
        'stabilite'      => Category::CATEGORY_STABILITY,
    ];

    case CATEGORY_SECURITY = 'SECURITY';

    case CATEGORY_PERFORMANCE = 'PERFORMANCE';

    case CATEGORY_READABILITY = 'READABILITY';

    case CATEGORY_TYPO = 'TYPO';

    case CATEGORY_MAINTAINABILITY = 'MAINTAINABILITY';

    case CATEGORY_QUALITY = 'QUALITY';

    case CATEGORY_STABILITY = 'STABILITY';

    case CATEGORY_NONE = 'N/A';

    public static function has(string $value): bool
    {
        return null !== self::tryFrom($value);
    }

    /**
     * @throws ValueError If $value is not part of this enum.
     */
    public static function map(string $value): self
    {
        try {
            return self::from($value);
        } catch (ValueError $e) {
        }

        return self::SEVERITY_MAPPING[self::slugify($value)] ?? throw new ValueError(sprintf('%s is not part of the %s enum.', $value, self::class), previous: $e);
    }

    public static function hasMap(string $candidate): bool
    {
        return null !== self::tryMap($candidate);
    }

    public static function tryMap(string $candidate): ?self
    {
        try {
            return self::map($candidate);
        } catch (ValueError) {
            return null;
        }
    }

    private static function slugify(string $text): string
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if ('' === $text) {
            return 'n-a';
        }

        return $text;
    }
}
