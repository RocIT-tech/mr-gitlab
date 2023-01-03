<?php

declare(strict_types=1);

namespace App\Gitlab\Serializer;

use App\Gitlab\Client\MergeRequest\Model\Thread\Note;
use App\Gitlab\Client\MergeRequest\Model\Thread\Notes;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use function array_filter;
use function sprintf;

final class NotesNormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    /**
     * @param array $data
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Notes
    {
        return new Notes($this->denormalizer->denormalize(
            array_filter($data, static function (array $note): bool {
                return isset($note['position']) === true && $note['system'] === false;
            }),
            sprintf('%s[]', Note::class),
            $format,
            $context
        ));
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return Notes::class === $type;
    }
}
