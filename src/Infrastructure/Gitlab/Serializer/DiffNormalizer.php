<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Serializer;

use App\Domain\Git\Diff;
use SebastianBergmann\Diff\Line;
use SebastianBergmann\Diff\Parser;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use const PHP_EOL;

final class DiffNormalizer implements DenormalizerInterface
{
    public const FORMAT = 'git-diff';

    private readonly Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * @param array{diff: string, old_path: string, new_path: string, a_mode: string, b_mode: string, new_file: bool, renamed_file: bool, deleted_file: bool} $data
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Diff
    {
        $diff = "diff --git a/{{$data['old_path']}} b/{{$data['new_path']}}" . PHP_EOL;
        $diff .= "index fake...fake {$data['a_mode']}" . PHP_EOL;
        $diff .= "--- a/{$data['old_path']}" . PHP_EOL;
        $diff .= "+++ b/{$data['new_path']}" . PHP_EOL;
        $diff .= $data['diff'];

        $added = $removed = 0;

        $parsed = $this->parser->parse($diff);
        foreach ($parsed as $diff) {
            foreach ($diff->getChunks() as $chunk) {
                foreach ($chunk->getLines() as $line) {
                    if ($line->getType() === Line::ADDED) {
                        $added++;
                    } elseif ($line->getType() === Line::REMOVED) {
                        $removed++;
                    }
                }
            }
        }

        return new Diff($removed, $added);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return Diff::class === $type && $format === self::FORMAT;
    }
}
