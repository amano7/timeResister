<?php

namespace Amano7\RedmineTimeRegister;

class RedmineRegister
{
    // -----------------------Redmine登録----------------------
    // RedmineAPI Key
    private $apiKey = '67846d2f3b39e9d04b5bbfb927370ef612bb00f4';
    // Redmine時間記録URL
    private $url = 'https://redmine.eilsystem.info/projects/polaris-export_support/time_entries.xml';
    /**
    * 配列を受け取ってRedmineAPIに投げる
    *
    * @param  string  $redLines
    *
    */
    // 日付を指定していないので、本日(実行日)になります。
    public function register($redLines){
        $headers = [
            'Content-type: application/xml',
            "X-Redmine-API-Key: $this->apiKey"
        ];
        // curlオプションの設定
        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_URL, $this->url);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($curlObj, CURLOPT_POST, 1);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, $headers);

        // ファイル更新時、配列に格納したデータを処理
        foreach ($redLines as $redLine) {
            // xmlの組み立て
            $inputXml = "<time_entry>\n";
            $inputXml .= '<issue_id>' . $redLine['redNum'] . "</issue_id>\n";
            $inputXml .= '<activity_id>' . $redLine['activityID'] . "</activity_id>\n";
            $inputXml .= '<hours>' . $redLine['redTime'] . "</hours>\n";
            $inputXml .= '<comments>' . $redLine['redCom'] . "</comments>\n";
            $inputXml .= "</time_entry>\n";
            // Redmineに書き出し
            curl_setopt($curlObj, CURLOPT_POSTFIELDS, 'xmlRequest=' . $inputXml);
            $resXml = curl_exec($curlObj);
            if (curl_errno($curlObj)) {
                echo curl_error($curlObj);
            }
            $this->resultCheck($resXml);
        }

        curl_close($curlObj);
    }
    // -----------------------/Redmine登録----------------------
    // 結果表示関数
    private function resultCheck($resXml)
    {
        $xmlElement = new \SimpleXMLElement($resXml);
        echo "\nRegist Tcket #" . $xmlElement->issue['id'];
        echo "\tHours: " . $xmlElement->hours . ' h';
        echo "\tCreated On: " . $xmlElement->created_on;
        echo "\n\t" . $xmlElement->comments;
        echo "\n";
    }
}