<?php

declare(strict_types=1);

namespace App\Domain\Git;

use Exception;
use function array_pop;
use function count;
use function preg_match;
use function preg_split;
use const PREG_SPLIT_NO_EMPTY;

final class Diff
{
    public function __construct(
        public int $removed = 0,
        public int $added = 0
    ) {
    }

    /**
     * @throws Exception
     */
    public static function parse(string $string): self
    {
        $lines = preg_split('(\r\n|\r|\n)', $string, 0, PREG_SPLIT_NO_EMPTY);

        if (false === $lines) {
            throw new Exception('Something went wrong.');
        }

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
                    default => throw new Exception('Something went wrong.'),
                };
            }
        }

        return new self (removed: -$removed, added: $added);
    }
}
