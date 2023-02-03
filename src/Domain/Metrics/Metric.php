<?php

declare(strict_types=1);

namespace App\Domain\Metrics;

use function sprintf;

enum Metric: string
{
    case NumberOfThreads = 'number_of_threads';
    case NumberOfUnresolvedThreads = 'number_of_unresolved_threads';
    case ThreadsFilesRatio = 'threads_files_ratio';
    case LinesFilesRatio = 'lines_files_ratio';
    case FilesChanged = 'files_changed';
    case LinesAdded = 'lines_added';
    case LinesRemoved = 'lines_removed';
    case RepliesPerThreadRatio = 'replies_per_thread_ratio';
    case AlertRatio = 'alert_ratio';
    case WarningRatio = 'warning_ratio';
    case ReadabilityRatio = 'readability_ratio';
    case SecurityRatio = 'security_ratio';

    public function name(): string
    {
        return match ($this) {
            self::NumberOfThreads           => 'Number of Threads',
            self::NumberOfUnresolvedThreads => 'Number of unresolved threads',
            self::ThreadsFilesRatio         => 'Thread / Files Ratio',
            self::LinesFilesRatio           => 'Lines / Files Ratio',
            self::FilesChanged              => 'Files Changed',
            self::LinesAdded                => 'Lines Added',
            self::LinesRemoved              => 'Lines Removed',
            self::RepliesPerThreadRatio     => 'Replies per Thread Ratio',
            self::AlertRatio                => 'Alert Ratio',
            self::WarningRatio              => 'Warning Ratio',
            self::ReadabilityRatio          => 'Readability Ratio',
            self::SecurityRatio             => 'Security Ratio',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NumberOfThreads           => 'Nombre de threads ouvert',
            self::NumberOfUnresolvedThreads => 'Nombre de Threads non "resolved".',
            self::ThreadsFilesRatio         => 'Ratio entre le nombre de thread ouverts et le nombre de fichiers modifiés',
            self::LinesFilesRatio           => 'Ratio entre la somme des lignes modifiées et le nombre de fichiers modifiés',
            self::FilesChanged              => 'Nombre de fichiers changés',
            self::LinesAdded                => 'Nombre de lignes ajoutées',
            self::LinesRemoved              => 'Nombre de lignes supprimées',
            self::RepliesPerThreadRatio     => 'Nombre de réponses / nombre de threads',
            self::AlertRatio                => sprintf('Nombre de threads de type "%s" / Nombre de threads', Severity::SEVERITY_ALERT->value),
            self::WarningRatio              => sprintf('Nombre de threads de type "%s" / Nombre de threads', Severity::SEVERITY_WARNING->value),
            self::ReadabilityRatio          => sprintf('Nombre de threads de catégorie "%s" / Nombre de threads', Category::CATEGORY_READABILITY->value),
            self::SecurityRatio             => sprintf('Nombre de threads de catégorie "%s" / Nombre de threads', Category::CATEGORY_SECURITY->value),
        };
    }
}
