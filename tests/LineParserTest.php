<?php
namespace Amano7\RedmineTimeRegister;

use PHPUnit\Framework\TestCase;

class LineParserTest extends TestCase
{
    public function test_行をパースできる()
    {
        $line[0] = '- 09:15 #9910 東邦会打ち合わせ [設計作業]'."\n".'- 09:45 #9911 東邦会打ち合 111:1'."\n";
        $line[1] = '- 09:45 #9911 東邦会打ち合 111:1'."\n".'- 10:45 #9912 東邦会打ち0000 01:03'."\n";
        $line[2] = '- 10:45 #9912 東邦会打ち0000 01:03'."\n".'- 10:50 #9913 東邦会打ち--+ [営業活動]'."\n";
        $line[3] = '- 10:50 #9913 東邦会打ち--+ [営業活動]'."\n".'- 11:20 #9914 東邦会打ち [チケットの記入]'."\n";
        $line[4] = '- 11:20 #9914 東邦会打ち [チケットの記入]'."\n".'- 11:45 #9915 東邦会打ち [チケットの記入] 01:05'."\n";
        $line[5] = '- 11:45 #9915 東邦会打ち [チケットの記入] 01:05'."\n".'- 12:45 #9911 東邦会打ち合 111:1'."\n";

        $expected[0] = [
            'redNum' => '9910',
            'redCom' => '東邦会打ち合わせ ',
            'redTime' => '0:30',
            'activityID' => 8,
        ];
        $expected[1] = [
            'redNum' => '9911',
            'redCom' => '東邦会打ち合 111:1',
            'redTime' => '1:00',
            'activityID' => 9,
        ];
        $expected[2] = [
            'redNum' => '',
            'redCom' => '',
            'redTime' => '',
            'activityID' => 0
        ];
        $expected[3] = [
            'redNum' => '9913',
            'redCom' => '東邦会打ち--+ ',
            'redTime' => '0:30',
            'activityID' => 14
        ];
        $expected[4] = [
            'redNum' => '9914',
            'redCom' => '東邦会打ち ',
            'redTime' => '0:25',
            'activityID' => 15
        ];
        $expected[5] = [
            'redNum' => '',
            'redCom' => '',
            'redTime' => '',
            'activityID' => 0
        ];
        $parser = new LineParser();
        for ($i = 0, $cnt = count($line); $i < $cnt; $i++) {
            $test[$i] = $parser->parse($line[$i]);
        }
        // $test[0] = $parser->parse($line[0]);

        $this->assertSame($expected, $test);
    }
}
