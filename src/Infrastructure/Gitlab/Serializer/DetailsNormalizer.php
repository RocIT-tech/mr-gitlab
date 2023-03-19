<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Serializer;

use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use DateTimeImmutable;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class DetailsNormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    /**
     * @param array $data
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Details
    {
        return new Details(
            id: $data['id'],
            title: $data['title'],
            description: $data['description'],
            webUrl: $data['web_url'],
            createdAt: $this->denormalizer->denormalize($data['created_at'], DateTimeImmutable::class, $format, $context),
        );
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return Details::class === $type;
    }
}
