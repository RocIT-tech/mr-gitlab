<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Gitlab\Serializer;

use App\Domain\Git\Diff;
use App\Infrastructure\Gitlab\Serializer\DiffNormalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[CoversClass(DiffNormalizer::class)]
final class DiffNormalizerTest extends TestCase
{
    //#[CoversFunction('supportsDenormalization()')]
    public function testSupportsOnlyDiffObjectInDiffFormat(): void
    {
        $diffNormalizer = new DiffNormalizer();

        $this->assertTrue($diffNormalizer->supportsDenormalization('fake data', Diff::class, DiffNormalizer::FORMAT));
        $this->assertFalse($diffNormalizer->supportsDenormalization('fake data', Diff::class));
        $this->assertFalse($diffNormalizer->supportsDenormalization('fake data', 'some\other\class', DiffNormalizer::FORMAT));
    }
}
