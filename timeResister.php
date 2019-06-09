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

// 第一引数(ファイル名)
$fileName = $argv[1];

// ※ 最後の時間を除外する正規表現の関係で、時間以外のコメントは全角半角４文字以上必要
// 行頭が-で始まり時間が 00:00-00:00 形式で記述、最終行に 00:00 がない行
$pattern = "/(^- )([0-9]{1,2}:[0-9]{2})-([0-9]{1,2}:[0-9]{2})( .+)([^0-9]{1,2}[^:][^0-9]{2})$/u";

$lines = file($fileName);

$newLines = "";
$redLines = array();

foreach ($lines as $line) {
  // Windows用の改行がターミナルに表示できないため改行を削除
  $line = preg_replace("/[\r\n]/","",$line);
  if (preg_match($pattern, $line, $match)){
    // チケット番号で始まり、行末に時間がない行に時間を追加
    $workTime = gmdate('G:i', strtotime($match[3]) - strtotime($match[2]));
    $newLines .= $line . " " . $workTime;
    if (preg_match("/#([0-9]{4}) (.+)$/u", $match[4].$match[5], $matchNumber)){
      // チケット番号とコメントを取得し配列に格納(Redmine登録用) ※処理を行ったもののみ記録
      array_push($redLines, array(
        "redNum"=>$matchNumber[1],
        "redCom"=>$matchNumber[2],
        "redTime"=>$workTime
      ));
    };
  } else {
    // 上記以外はそのまま出力
    $newLines .= $line;
  }
  $newLines .= $nl;
}

// ファイルに書き戻し
file_put_contents($fileName, $newLines);

// 日付を指定していないので、本日(実行日)になります。
// xmlの組み立て
foreach ($redLines as $redLine) {
  $inputXml = "<time_entry>";
  $inputXml .= "<issue_id>".$redLine["redNum"]."</issue_id>";
  $inputXml .= "<activity_id>".$activityID."</activity_id>";
  $inputXml .= "<hours>".$redLine["redTime"]."</hours>";
  $inputXml .= "<comments>".$redLine["redCom"]."</comments>";
  $inputXml .= "</time_entry>";
  $resXml = regist_xml($inputXml, $url, $apiKey);
  resultCheck($resXml);
}

// ターミナルに表示
function resultCheck($resXml){
  $xmlElement = new SimpleXMLElement($resXml);
  echo "Regist Tcket #".$xmlElement->issue['id'];
  echo ": ".$xmlElement->comments;
  echo "\tHours: ".$xmlElement->hours;
  echo "\tCreated On: ".$xmlElement->created_on;
  echo "\n";
}

// Redmineに書き出し
function regist_xml($inputXml, $url, $apiKey){
  // パラメータ
  $curlObj = curl_init();
  curl_setopt($curlObj, CURLOPT_URL, $url);
  curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 300);
  curl_setopt($curlObj, CURLOPT_POST, 1);
  curl_setopt($curlObj, CURLOPT_HTTPHEADER, array("Content-Type: application/xml","X-Redmine-API-Key: $apiKey"));
  curl_setopt($curlObj, CURLOPT_POSTFIELDS,"xmlRequest=".$inputXml);
  $res = curl_exec($curlObj);
  curl_close($curlObj);
  return $res;
}