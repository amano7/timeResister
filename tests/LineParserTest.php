<?php
namespace Amano7\RedmineTimeRegister;

use PHPUnit\Framework\TestCase;

class LineParserTest extends TestCase
{
    public function test_行をパースできる()
    {
        $parser = new LineParser();

        $line = '- 09:15-09:45 #9910 東邦会打ち合わせ [打ち合わせ]' . "\n";
        $test = $parser->parse($line);

        $expected = [
            'redNum' => '9910',
            'redCom' => '東邦会打ち合わせ ',
            'redTime' => '0:30',
            'activityID' => 11
        ];
        $this->assertSame($expected, $test);
    }
}
