<?php

namespace Amano7\RedmineTimeRegister;

use PHPUnit\Framework\TestCase;

class TextModifyFileTest extends TestCase
{
    public function test_テキストファイルを変更(){
        $filePath = '/Users/YasushiAmano/dev/timeRegister/tests/testTextBefore.md';
        $originaLlinesBefore = file($filePath);
        $mod = new TextModify();
        $mod->readTextAddWorkTime($filePath);
        $originaLlinesAfter = file($filePath);
        $expfilePath = '/Users/YasushiAmano/dev/timeRegister/tests/testTextAfter.md';
        $originaLlinesExpected = file($expfilePath);

        $this->assertSame($originaLlinesExpected, $originaLlinesAfter);
    }
}