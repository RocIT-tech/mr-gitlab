<?php

declare(strict_types=1);

namespace App\Git\Serializer;

use App\Gitlab\Client\MergeRequest\Model\Change\Diff;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use function array_pop;
use function count;
use function preg_match;
use function preg_split;

final class DiffNormalizer implements DenormalizerInterface
{
    public const FORMAT = 'git-diff';

    /**
     * @param string $data
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Diff
    {
        [$removed, $added] = $this->parse($data);

        return new Diff(
            removed: $removed,
            added: $added
        );
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return Diff::class === $type && $format === self::FORMAT;
    }

    /**
     * @return int[]
     */
    private function parse(string $string): array
    {
        $lines = preg_split('(\r\n|\r|\n)', $string);

        if ([] !== $lines && '' === $lines[count($lines) - 1]) {
            array_pop($lines);
        }

        $added   = 0;
        $removed = 0;

        foreach ($lines as $line) {
            $match = [];
            if (preg_match('#(?P<modifier>^[-+])#', $line, $match) !== 0) {
                $modifier = $match['modifier'];

                match ($modifier) {
                    '-' => ++$removed,
                    '+' => ++$added,
                };
            }
        }

        return [-$removed, $added];
    }
}
