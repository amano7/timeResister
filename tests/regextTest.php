<?php
namespace Amano7\RedmineTimeRegister;

use PHPUnit\Framework\TestCase;

class RegextTest extends TestCase {
    public function test_regurerExtention() {
        $regObject = new regext;

        $regSample[0] = '- 09:15-09:45 #9910 東邦会打ち合わせ [設計作業]' . "\n";
        $regSample[1] = '- 09:45-10:45 #9911 東邦会打ち合 111:1' . "\n";
        $regSample[2] = '- 10:45-10:50 #9912 東邦会打ち0000 01:03' . "\n";
        $regSample[3] = '- 10:50-11:20 #9913 東邦会打ち--+ [営業活動]' . "\n";
        $regSample[4] = '- 11:20-12:30 #9914 東邦会打ち\ [チケットの記入]' . "\n";
        $regSample[5] = '- 11:20-12:30 #9914 東邦会打ち\ [チケットの記入] 0:05' . "\n";

        $expected[0] = [$regSample[0], ' #9910 東邦会打ち合わせ [設計作業]'."\n"];
        $expected[1] = [$regSample[1], ' #9911 東邦会打ち合 111:1'."\n"];
        $expected[2] = [$regSample[2], ''];
        $expected[3] = [$regSample[3], ' #9913 東邦会打ち--+ [営業活動]'."\n"];
        $expected[4] = [$regSample[4], ' #9914 東邦会打ち\ [チケットの記入]'."\n"];
        $expected[5] = [$regSample[5], ''];
        for ($i = 0; $i < 5; $i++ ){
            $test[$i] = $regObject->regtest($regSample[$i]);
        }

        $this->assertSame($expected, $test);
    }
}
