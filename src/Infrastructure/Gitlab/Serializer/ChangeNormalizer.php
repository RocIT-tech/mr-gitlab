<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Serializer;

use App\Domain\Git\Diff;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Change;
use App\Infrastructure\Serializer\DiffNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ChangeNormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    /**
     * @param array $data
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Change
    {
        return new Change(
            $data['new_path'],
            $this->denormalizer->denormalize($data['diff'], Diff::class, DiffNormalizer::FORMAT, $context)
        );
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return Change::class === $type;
    }
}
