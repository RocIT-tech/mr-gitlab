<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Serializer;

use App\Domain\Git\Diff;
use App\Infrastructure\Serializer\DiffNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @coversDefaultClass \App\Infrastructure\Serializer\DiffNormalizer
 */
final class DiffNormalizerTest extends TestCase
{
    /**
     * @covers ::supportsDenormalization()
     */
    public function testSupportsOnlyDiffObjectInDiffFormat(): void
    {
        $diffNormalizer = new DiffNormalizer();

        $this->assertTrue($diffNormalizer->supportsDenormalization('fake data', Diff::class, DiffNormalizer::FORMAT));
        $this->assertFalse($diffNormalizer->supportsDenormalization('fake data', Diff::class));
        $this->assertFalse($diffNormalizer->supportsDenormalization('fake data', 'some\other\class', DiffNormalizer::FORMAT));
    }
}
