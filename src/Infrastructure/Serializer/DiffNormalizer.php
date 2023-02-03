<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer;

use App\Domain\Git\Diff;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class DiffNormalizer implements DenormalizerInterface
{
    public const FORMAT = 'git-diff';

    /**
     * @param string $data
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Diff
    {
        return Diff::parse($data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return Diff::class === $type && $format === self::FORMAT;
    }
}
