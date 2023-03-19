<?php

declare(strict_types=1);

namespace App\Tests\Domain\Git;

use App\Domain\Git\Diff;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[CoversClass(Diff::class)]
//#[CoversFunction('__construct()')]
//#[CoversFunction('parse()')]
final class DiffTest extends TestCase
{
    public static function generateParseableData(): Generator
    {
        yield 'only additions' => [
            'data'     => <<<DIFF
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
            'data'     => <<<DIFF
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

        yield 'empty' => [
            'data'     => '',
            'expected' => new Diff(0, 0),
        ];

        yield 'new line only' => [
            'data'     => '\n',
            'expected' => new Diff(0, 0),
        ];
    }

    public function testDefaults(): void
    {
        $diff = new Diff();

        $this->assertSame(0, $diff->added);
        $this->assertSame(0, $diff->removed);
    }
}
