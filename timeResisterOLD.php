<?php

// ---------------------- 設定 ----------------------
// Redmineの活動ID 作業:11
// Redmineの活動ID eilsystem 開発作業:9
$gl_activityID = 9;
// RedmineAPI Key
$apiKey = '67846d2f3b39e9d04b5bbfb927370ef612bb00f4';
// Redmine時間記録URL
$url = 'https://redmine.eilsystem.info/projects/polaris-export_support/time_entries.xml';
// for Windows \r\n
// for Mac \n
$nl = PHP_EOL;
// ---------------------- /設定 ----------------------


// -----------------------ファイル更新----------------------

// 第一引数(ファイル名)
$fileName = $argv[1];

// 行頭が-で始まり時間が 00:00-00:00 形式で記述、最終行に 00:00 がない行
// 例：
// - 09:50-10:00 #1047 事前検証テストケース一覧の更新
// UTF-8 を処理するため明示的に「u」をつけています。
$pattern = '/^- ([0-9]{1,2}:[0-9]{2})-([0-9]{1,2}:[0-9]{2})( .+[^0-9]{1,2}[^:][^0-9]{2})$/u';

// ファイルを行単位で配列に格納
$lines = file($fileName);

$newLines = '';
$redLines = [];
$activities = [
    "\[設計作業\]" => 8,
    "\[開発作業\]" => 9,
    "\[確認作業\]" => 10,
    "\[打ち合わせ\]" => 11,
    "\[調査\]" => 12,
    "\[その他\]" => 13,
    "\[営業活動\]" => 14,
    "\[チケットの記入\]" => 15
];

foreach ($lines as $line) {
    // Windows用の改行がターミナルに表示できないため改行を削除
    $line = preg_replace("/[\r\n]/u", '', $line);

    // $patternにマッチする行を処理
    if (preg_match($pattern, $line, $match)) {
        // 開始時間
        $startTime = strtotime($match[1]);
        // 終了時間
        $endTime = strtotime($match[2]);
        $workTime = gmdate('G:i', $endTime - $startTime);

        // 行末に時間を追加
        $newLines .= $line . ' ' . $workTime;
        $comment = $match[3];

        // コメントの先頭で「#」で始まる番号とコメント、作業時間を分けて配列に格納
        if (preg_match('/#([0-9]+) (.+)$/u', $comment, $matchNumber)) {
            // チケット番号とコメントを取得し配列に格納(Redmine登録用) ※処理を行ったもののみ記録
            $matchNumber = preg_replace("/[\r\n]/u", '', $matchNumber);
            $matchCom = $matchNumber[2];
            $activityID = '';
            foreach ($activities as $key => $value) {
                if (preg_match("/(.+ )($key)/u",$matchNumber[2], $matchComment)){
                    $activityID = $value;
                    $matchCom = $matchComment[1];
                    break;
                }
            }
            array_push($redLines, array(
                'redNum' => $matchNumber[1],
                'redCom' => $matchCom,
                'redTime' => $workTime,
                'activityID' => $activityID
            ));
        }
    } else {
        // 上記以外はそのまま出力
        $newLines .= $line;
    }
    $newLines .= $nl;
}

// ファイルに書き戻し
file_put_contents($fileName, $newLines);
// -----------------------/ファイル更新----------------------

// -----------------------Redmine登録----------------------

// 日付を指定していないので、本日(実行日)になります。
$headers = [
    'Content-type: application/xml',
    "X-Redmine-API-Key: $apiKey"
];

// curlオプションの設定
$curlObj = curl_init();
curl_setopt($curlObj, CURLOPT_URL, $url);
curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 300);
curl_setopt($curlObj, CURLOPT_POST, 1);
curl_setopt($curlObj, CURLOPT_HTTPHEADER, $headers);

// ファイル更新時、配列に格納したデータを処理
foreach ($redLines as $redLine) {
    if ($redLine['activityID'] == '') {
        $redLine['activityID'] = $gl_activityID;
    }
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
    // echo '$resXml = ' . $resXml ."\n";
    if (curl_errno($curlObj)) {
        echo curl_error($curlObj);
    }
    resultCheck($resXml);
}

curl_close($curlObj);
// -----------------------/Redmine登録----------------------

// 結果表示関数
function resultCheck($resXml)
{
    $xmlElement = new SimpleXMLElement($resXml);
    echo 'Regist Tcket #' . $xmlElement->issue['id'];
    echo "\tHours: " . $xmlElement->hours . ' h';
    echo "\tCreated On: " . $xmlElement->created_on;
    echo "\n\t" . $xmlElement->comments;
    echo "\n";
}