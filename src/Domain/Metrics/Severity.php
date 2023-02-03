<?php

declare(strict_types=1);

namespace App\Domain\Metrics;

enum Severity: string
{
    case SEVERITY_ALERT = 'ALERT';

    case SEVERITY_WARNING = 'WARNING';

    case SEVERITY_SUGGESTION = 'SUGGESTION';

    case SEVERITY_NONE = 'N/A';

    public static function has(string $value): bool
    {
        return null !== self::tryFrom($value);
    }
}
