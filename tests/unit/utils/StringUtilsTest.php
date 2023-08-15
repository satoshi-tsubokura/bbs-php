<?php

namespace Tests\Unit\Utils;

require_once __DIR__ . '/../../../app/utils/stringUtils.php';

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\CustomTestCase;

use function App\Utils\trimSpaceStr;

/**
 * /src/util/array_util.phpの関数群に対するテストクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
final class StringUtilsTest extends CustomTestCase
{
    #[DataProvider('beforeTrimDataProvider')]
    public function testTrim(string $plainString, string $expected): void
    {
        $this->assertEquals($expected, trimSpaceStr($plainString));
    }

    public static function beforeTrimDataProvider(): array
    {
        return [
          [' aaaa ', 'aaaa'],
          ['aa aa', 'aa aa'],
          ["aaaa\n", 'aaaa'],
          ["　　\r　 \naaaa　　\x00", 'aaaa'],
        ];
    }
}
