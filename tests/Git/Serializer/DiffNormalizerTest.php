<?php

declare(strict_types=1);

namespace App\Tests\Git\Serializer;

use App\Git\Serializer\DiffNormalizer;
use App\Gitlab\Client\MergeRequest\Model\Change\Diff;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @coversDefaultClass \App\Git\Serializer\DiffNormalizer
 *
 * @uses \App\Gitlab\Client\MergeRequest\Model\Change\Diff
 */
final class DiffNormalizerTest extends TestCase
{
    public function generateParseableData(): Generator
    {
        yield 'only additions' => [
            'data' => <<<DIFF
                    @@ -0,0 +1,7 @@\n
                    +# Title\n
                    +## SubTitle 1\n
                    +\n
                    +## SubTitle 2\n
                    +Some content\n
                    +\n
                    +Something else\n
                    DIFF,
            'expected' => new Diff(0, 7),
        ];

        yield 'only removals' => [
            'data' => <<<DIFF
                    @@ -0,0 +1,7 @@\n
                    -# Title\n
                    -## SubTitle 1\n
                    -\n
                    -## SubTitle 2\n
                    -Some content\n
                    -\n
                    -Something else\n
                    DIFF,
            'expected' => new Diff(-7, 0),
        ];
    }

    /**
     * @dataProvider generateParseableData
     *
     * @covers ::denormalize()
     * @covers ::parse()
     */
    public function testItCanParse(string $data, Diff $expected): void
    {
        $diffNormalizer = new DiffNormalizer();
        $result = $diffNormalizer->denormalize($data, Diff::class);

        $this->assertEquals($expected, $result);
    }
}
