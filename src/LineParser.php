<?php

namespace Amano7\RedmineTimeRegister;

class LineParser
{
    /**
     * @param string $line
     *
     * @return  array [
     *              'redNum' => <<TicketNumber>>,
     *              'redCom' => <<Comments>>,
     *              'redTime' => <<Hours>>,
     *          ]
     */
    public function parse(string $line): array
    {
        $redLines = [];
        $pattern = '/^- ([0-9]{1,2}:[0-9]{2})-([0-9]{1,2}:[0-9]{2})( .+[^0-9]{1,2}[^:][^0-9]{2})$/u';
        $activities = [
            "\[設計作業\]"=>8,
            "\[開発作業\]"=>9,
            "\[確認作業\]"=>10,
            "\[打ち合わせ\]"=>11,
            "\[調査\]"=>12,
            "\[その他\]"=>13,
            "\[営業活動\]"=>14,
            "\[チケットの記入\]"=>15
        ];
        if (preg_match($pattern, $line, $match)) {
            // 開始時間
            $startTime = strtotime($match[1]);
            // 終了時間
            $endTime = strtotime($match[2]);
            $workTime = gmdate('G:i', $endTime - $startTime);
            $comment = $match[3];
            // コメントの先頭で「#」で始まる番号とコメント、作業時間を分けて配列に格納
            if (preg_match('/#([0-9]+) (.+)$/u', $comment, $matchNumber)) {
                // チケット番号とコメントを取得し配列に格納(Redmine登録用) ※処理を行ったもののみ記録
                $matchNumber = preg_replace("/[\r\n]/u", '', $matchNumber);
                // array_push($redLines, array(
                    //     'redNum' => $matchNumber[1],
                    //     'redCom' => $matchNumber[2],
                    //     'redTime' => $workTime
                    // ));
                $matchCom = $matchNumber[2];
                $actID = 12;
                foreach ($activities as $key => $value) {
                    if (preg_match("/(.+ )($key)/u",$matchNumber[2], $matchComments)){
                        $actID = $value;
                        $matchCom = $matchComments[1];
                        break;
                    }
                }
                $redLines = array(
                    'redNum' => strval($matchNumber[1]),
                    'redCom' => $matchCom,
                    'redTime' => strval($workTime),
                    'activityID' => $actID
                );
            }
            return $redLines;
        } else {
            // 上記以外はそのまま出力
            return $line;
        }
    }
}
