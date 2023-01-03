<?php

declare(strict_types=1);

namespace App\Gitlab\Serializer;

use App\Gitlab\Client\MergeRequest\Model\Details;
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
            web_url: $data['web_url'],
        );
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return Details::class === $type;
    }
}
