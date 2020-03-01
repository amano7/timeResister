<?php

namespace Amano7\RedmineTimeRegister;

class LineParser
{
    /**
    * @param string $pattern
    *
    */
    private $pattern = '/^(?!.+[0-9]{1,2}:[0-9]{2}$)^- ([0-9]{1,2}:[0-9]{2}) \#([0-9]+) (.+$)\n^- ([0-9]{1,2}:[0-9]{2}).*$/mu';
    public function setPattern($ptn){
        $this->pattern = $ptn;
    }

    /**
    * @param string $act
    * @param string $defID
    */
    private $activities = [
        '\[設計作業\]'        => 8,
        '\[開発作業\]'        => 9,
        '\[確認作業\]'        => 10,
        '\[打ち合わせ\]'      => 11,
        '\[調査\]'            => 12,
        '\[その他\]'          => 13,
        '\[営業活動\]'        => 14,
        '\[チケットの記入\]'  => 15,
    ];
    private $defaultId = 9;
    public function setActivities($act,$defID){
        $this->activities = $act;
        $this->defaultId = $defID;
    }

    /**
     * @param string $line
     *
     * @return  array [
     *      'redNum' => <<TicketNumber>>,
     *      'redCom' => <<Comments>>,
     *      'redTime' => <<Hours>>,
     *      'activityID'=> <<activity ID>>
     * ]
     */
    public function parse(string $line): array
    {
        $redLines = [];
        if (preg_match($this->pattern, $line, $match)) {
            // 開始時間
            $startTime = strtotime($match[1]);
            // 終了時間
            $endTime  = strtotime($match[4]);
            // 作業時間
            $workTime = gmdate('G:i', $endTime - $startTime);
            // チケット番号
            $chicketNum = $match[2];
            // コメント
            $comment  = $match[3];
            // アクティビティー/コメント
            $actID = $this->defaultId ;
            $matchCom = $comment ;
            foreach ($this->activities as $key => $value) {
                if (preg_match("/(.+ *)($key)/u", $comment, $matchComments)) {
                    $actID    = $value;
                    $matchCom = $matchComments[1];
                    break;
                }
            }
            // 配列に格納
            $redLines = [
                'redNum' => strval($chicketNum),
                'redCom' => $matchCom,
                'redTime' => strval($workTime),
                'activityID' => $actID,
            ];
        } else {
            $redLines = [
                'redNum'     => '',
                'redCom'     => '',
                'redTime'    => '',
                'activityID' => 0,
            ];
        }//end if
        return $redLines;
    }//end parse()
}//end class
