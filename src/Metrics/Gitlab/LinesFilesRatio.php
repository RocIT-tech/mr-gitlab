<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricInterface;
use App\Metrics\MetricResult;
use Exception;
use LogicException;
use function abs;
use function count;

final class LinesFilesRatio implements MetricInterface
{
    public function name(): string
    {
        return 'Lines / Files Ratio';
    }

    /**
     * @throws LogicException If you see this one please call me...
     */
    public function result(Details $mergeRequestDetails): MetricResult
    {
        if (0 === count($mergeRequestDetails->changes)) {
            throw new LogicException('How could your Merge Request have no files change but lines do... ? 🧐');
        }

        $totalDiff      = $mergeRequestDetails->changes->totalDiff();
        $linesFileRatio = abs(($totalDiff->added + $totalDiff->removed) / count($mergeRequestDetails->changes));

        return new MetricResult(
            success: $linesFileRatio < 40,
            expectedValue: '< 40',
            currentValue: (string) $linesFileRatio,
            description: <<<TXT
            Ratio entre la somme des lignes modifiées et le nombre de fichiers modifiés
            TXT
        );
    }

    public static function getDefaultPriority(): int
    {
        return 80;
    }
}
