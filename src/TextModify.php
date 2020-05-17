<?php

namespace Amano7\RedmineTimeRegister;

class TextModify
{
    public $redLines = [];
    /**
     * テキストファイルPathを受け取って時間計算結果を追加して下記戻す
     *
     * @param string $fileName
     */
    public function readTextAddWorkTime($fileName)
    {
        // ファイルを行単位で配列に格納
        $lines = file($fileName);
        $newLines = $this->addWorkTime($lines);
        // ファイルに書き戻し
        file_put_contents($fileName, $newLines);
    }

    /**
     * テキストを受け取って、行末に作業時間を追加。upload用の配列も作成。
     *
     * @param string $lines
     */
    public function addWorkTime($lines)
    {
        // for Windows \r\n
        // for Mac \n
        $nl = PHP_EOL;
        $parser = new LineParser();
        $newLines = '';
        // 2行目取得のため総数から1をへらす
        $lineCount = count($lines) - 1;
        // 日付収集
        $spentOn = '';
        for($i = 0; $i < $lineCount; $i++ ){
            // 一行目と2行目を取得
            $line1 = preg_replace("/[\r\n]/u", '', $lines[$i]);
            $line2 = preg_replace("/[\r\n]/u", '', $lines[$i+1]);
            $cuppleOfLines = $line1.$nl.$line2;

            // 日付取得
            if (preg_match("/^[#]+ ([\d]{4}[\/\-][\d]{1,2}[\/\-][\d]{1,2})$/u", $line1, $spentOnFind)) {
                $spentOn = str_replace('/', '-', $spentOnFind[1]);
            }

            // 行ごとのパーサー
            $ret = $parser->parse($cuppleOfLines);
            // チケット番号がなければそのまま
            if ($ret['redNum'] != "") {
                array_push($this->redLines, ['spentOn' => $spentOn, 'data' => $ret]);
                // 行末に時間を追加
                $newLines .= $line1 . ' ' . $ret['redTime'];
            } else {
                $newLines .= $line1;
            }
            $newLines .= $nl;
        }
        // 最終行を追加
        $newLines .= $lines[$lineCount];
        return $newLines;
    }
}
