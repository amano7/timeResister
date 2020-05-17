<?php

namespace Amano7\RedmineTimeRegister;

use PHPUnit\Framework\TestCase;

class TextModifyTest extends TestCase
{
    public function test_テキストを変更(){
        $testLines = <<<eot
## 2020/05/15

- 09:15 #9910 東邦会打ち合わせ [設計作業]
- 09:45 #9911 東邦会打ち合 111:1
- 10:45 #9912 東邦会打ち0000 01:03

## 2020/05/16

- 10:50 #9913 東邦会打ち--+ [営業活動]
- 11:20 #9914 東邦会打ち [チケットの記入]
- 11:45 #9915 東邦会打ち [チケットの記入]
- 00:00

end of text and newline.
a
eot;
        $expected = <<<eot
## 2020/05/15

- 09:15 #9910 東邦会打ち合わせ [設計作業] 0:30
- 09:45 #9911 東邦会打ち合 111:1 1:00
- 10:45 #9912 東邦会打ち0000 01:03

## 2020/05/16

- 10:50 #9913 東邦会打ち--+ [営業活動] 0:30
- 11:20 #9914 東邦会打ち [チケットの記入] 0:25
- 11:45 #9915 東邦会打ち [チケットの記入]
- 00:00

end of text and newline.
a
eot;
        $mod = new TextModify();
        $arrLines = explode(PHP_EOL, $testLines);
        $modRetLines = $mod->addWorkTime($arrLines);
        $this->assertSame($expected, $modRetLines);
        // redmineに登録
        $red = new RedmineRegister();
        $red->register($mod->redLines);
    }
}