<?php
namespace Amano7\RedmineTimeRegister;

use PHPUnit\Framework\TestCase;

class LineParserTest extends TestCase
{
    public function test_行をパースできる()
    {
        $parser = new LineParser();

        $line = '- 09:00-10:00 #1047 事前検証テストケース一覧の更新' . "\n";
        $test = $parser->parse($line);

        $expected = [
            'redNum' => 1047,
            'redCom' => '事前検証テストケース一覧の更新',
            'redTime' => 1.00,
        ];
        $this->assertSame($expected, $test);
    }
}
