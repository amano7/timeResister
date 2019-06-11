<?php

// ---------------------- 設定 ----------------------
//Redmineの活動ID 作業:11
$activityID = 11;
//RedmineAPI Key
$apiKey = 'b06ee7656b9dd371a0ae2c5b55411f299ba124f4';
// Redmine時間記録URL
$url = "https://my.redmine.jp/toumei/time_entries.xml";
// for windows \r\n
//for Mac \n
$nl = "\r\n";
// ---------------------- /設定 ----------------------


// -----------------------ファイル更新----------------------

// 第一引数(ファイル名)
$fileName = $argv[1];

// ※ 最後の時間を除外する正規表現の関係で、時間以外のコメントは全角半角４文字以上必要
// 行頭が-で始まり時間が 00:00-00:00 形式で記述、最終行に 00:00 がない行
// 例：
// - 09:50-10:00 #1047 事前検証テストケース一覧の更新
// UTF-8 を処理するため明示的に「u」をつけています。
$pattern = "/(^- )([0-9]{1,2}:[0-9]{2})-([0-9]{1,2}:[0-9]{2})( .+)([^0-9]{1,2}[^:][^0-9]{2})$/u";

// ファイルを行単位で配列に格納
$lines = file($fileName);

$newLines = "";
$redLines = array();

foreach ($lines as $line) {
    // Windows用の改行がターミナルに表示できないため改行を削除
    $line = preg_replace("/[\r\n]/", "", $line);
    // $patternにマッチする行を処理
    if (preg_match($pattern, $line, $match)) {
        // 開始時間
        $startTime = strtotime($match[2]);
        // 終了時間
        $endTime = strtotime($match[3]);
        $workTime = gmdate('G:i', $endTime - $startTime);

        // 行末に時間を追加
        $newLines .= $line . " " . $workTime;
        $comment = $match[4] . $match[5];

        // コメントの先頭で「#」で始まる番号とコメント、作業時間を分けて配列に格納
        preg_match("/#([0-9]{4}) (.+)$/u", $comment, $matchNumber);
        // チケット番号とコメントを取得し配列に格納(Redmine登録用) ※処理を行ったもののみ記録
        $matchNumber = preg_replace("/[\r\n]/", "", $matchNumber);
        array_push($redLines, array(
            "redNum" => $matchNumber[1],
            "redCom" => $matchNumber[2],
            "redTime" => $workTime
        ));
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
$headers = array(
    "Content-type: application/xml",
    "X-Redmine-API-Key: $apiKey"
);

// curlオプションの設定
$curlObj = curl_init();
curl_setopt($curlObj, CURLOPT_URL, $url);
curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 300);
curl_setopt($curlObj, CURLOPT_POST, 1);
curl_setopt($curlObj, CURLOPT_HTTPHEADER, $headers);

// ファイル更新時、配列に格納したデータを処理
foreach ($redLines as $redLine) {
    // xmlの組み立て
    $inputXml = "<time_entry>\n";
    $inputXml .= "<issue_id>" . $redLine["redNum"] . "</issue_id>\n";
    $inputXml .= "<activity_id>" . $activityID . "</activity_id>\n";
    $inputXml .= "<hours>" . $redLine["redTime"] . "</hours>\n";
    $inputXml .= "<comments>" . $redLine["redCom"] . "</comments>\n";
    $inputXml .= "</time_entry>\n";
    // Redmineに書き出し
    curl_setopt($curlObj, CURLOPT_POSTFIELDS, "xmlRequest=" . $inputXml);
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
    echo "Regist Tcket #" . $xmlElement->issue['id'];
    echo "\tHours: " . $xmlElement->hours . " h";
    echo "\tCreated On: " . $xmlElement->created_on;
    echo "\n\t" . $xmlElement->comments;
    echo "\n";
}

