<?php

declare(strict_types=1);

namespace App\Gitlab\Serializer;

use App\Gitlab\Client\MergeRequest\Model\Thread\Note;
use App\Gitlab\Client\MergeRequest\Model\Thread\NotePosition;
use App\Gitlab\Client\MergeRequest\Model\Thread\NoteType;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class NoteNormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    /**
     * @param array $data
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Note
    {
        return new Note(
            id: $data['id'],
            type: null === $data['type'] ? NoteType::TYPE_INDIVIDUAL_NOTE : NoteType::from($data['type']),
            body: $data['body'],
            position: $this->denormalizer->denormalize($data['position'], NotePosition::class, $format, $context),
            resolved: true === $data['resolvable'] ? ($data['resolved'] ?? false) : true,
        );
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return Note::class === $type;
    }
}
