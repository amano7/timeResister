<?php
namespace Amano7\RedmineTimeRegister;

use PHPUnit\Framework\TestCase;

class LineParserTest extends TestCase
{
    public function test_行をパースできる()
    {
        $parser = new LineParser();

        $line[0] = '- 08:00-08:30 #9910 状況確認、準備など [確認作業]'."\n";
        $line[1] = '- 08:30-11:00 #8279 注残集計方法の変更（フォーム） [開発作業]'."\n";
        // $line[2] = '- 10:45-10:50 #9912 東邦会打ち0000'."\n";
        // $line[3] = '- 10:50-11:20 #9913 東邦会打ち--+ [営業活動]'."\n";
        // $line[4] = '- 11:20-12:30 #9914 東邦会打ち\ [チケットの記入]'."\n";
        // $line[5] = '- 11:20-12:30 #9914 東邦会打ち\ [チケットの記入] 0:05'."\n";

        $expected[0] = [
            'redNum' => '9910',
            'redCom' => '状況確認、準備など ',
            'redTime' => '0:30',
            'activityID' => 10,
        ];
        $expected[1] = [
            'redNum' => '8279',
            'redCom' => '注残集計方法の変更（フォーム） ',
            'redTime' => '2:30',
            'activityID' => 9,
        ];
        // $expected[2] = [
        //     'redNum' => '9912',
        //     'redCom' => '東邦会打ち0000',
        //     'redTime' => '0:05',
        //     'activityID' => 9,
        // ];
        // $expected[3] = [
        //     'redNum' => '9913',
        //     'redCom' => '東邦会打ち--+ ',
        //     'redTime' => '0:30',
        //     'activityID' => 14,
        // ];
        // $expected[4] = [
        //     'redNum' => '9914',
        //     'redCom' => '東邦会打ち\\ ',
        //     'redTime' => '1:10',
        //     'activityID' => 15,
        // ];
        // $expected[5] = [
        //     'redNum' => '- 11:20-12:30 #9914 東邦会打ち\ [チケットの記入] 0:05' . "\n",
        //     'redCom' => '',
        //     'redTime' => '',
        //     'activityID' => '',
        // ];
        // $expected[5] = [
        //     0 => '- 11:20-12:30 #9914 東邦会打ち\ [チケットの記入] 0:05\\n',
        // ];
        for ($i = 0, $cnt = count($line); $i < $cnt; $i++) {
            $test[$i] = $parser->parse($line[$i]);
        }
        // $test[0] = $parser->parse($line[0]);

        $this->assertSame($expected, $test);
    }
}
