<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Serializer;

use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class GitlabNormalizer implements DenormalizerInterface, SerializerAwareInterface, NormalizerAwareInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    public const CONTEXT = 'gitlab-api';

    public function __construct(private readonly DenormalizerInterface $normalizer)
    {
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        return $this->normalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return true === ($context[self::CONTEXT] ?? false) && $this->normalizer->supportsDenormalization($data, $type, $format);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if (!$this->normalizer instanceof SerializerAwareInterface) {
            return;
        }

        $this->normalizer->setSerializer($serializer);
    }

    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        if (!$this->normalizer instanceof NormalizerAwareInterface) {
            return;
        }

        $this->normalizer->setNormalizer($normalizer);
    }

    public function setDenormalizer(DenormalizerInterface $denormalizer): void
    {
        if (!$this->normalizer instanceof DenormalizerAwareInterface) {
            return;
        }

        $this->normalizer->setDenormalizer($denormalizer);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return $this->normalizer instanceof CacheableSupportsMethodInterface && $this->normalizer->hasCacheableSupportsMethod();
    }
}
