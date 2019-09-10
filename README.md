# Redmine 作業時間 計算・登録

## 本プログラムについて

テキストを読み取って、作業時間を計算後、テキストファイルを更新して、Redmineの作業時間も記録するプログラムです。(PHP版)

## 仕様

### テキストファイルのフォーマット

- テキストファイルは下記のフォーマットで記述されている必要があります。

  1. 行頭は「- 」(マイナス+スペース)
  1. 開始時間-終了時間
  1. 「#」につづいてチケット番号
  1. コメント(4文字以上)
  1. 上記2〜4はスペース区切り
  1. 活動を入れる場合は、行末に下記の内容で記述
     - [設計作業]
     - [開発作業]
     - [確認作業]
     - [打ち合わせ]
     - [調査]
     - [その他]
     - [営業活動]
     - [チケットの記入]

  例: 実行前

  ```md
  ## 06/01

  - 09:30-09:45 準備
  - 09:45-10:40 #8934 原因の調査と履歴更新 [開発作業]
  - 10:40-11:15 #8598 説明更新 [その他]
  - 11:15-11:40 #8934 原因の調査と履歴更新 [開発作業]
  ```

  ※ 上記の例では、1行目、2行目と3行目の「09:30-09:45 準備」は無視されます。(チケット番号がない、コメントが4文字ない)

  例: 実行後

  ```md
  ## 06/01

  - 09:30-09:45 準備
  - 09:45-10:40 #8934 原因の調査と履歴更新 [開発作業] 0:55
  - 10:40-11:15 #8598 説明更新 [その他] 0:35
  - 11:15-11:40 #8934 原因の調査と履歴更新 [開発作業] 0:25
  ```

- フォーマットを要約すると「行頭に時間とチケット番号、その後にコメントがあり、行末が時間ではない行」です。
- 活動が記録されていない場合は、初期値に設定されている活動で登録されます。
- プログラムは、上記のフォーマット以外を無視します。(空白行や行末に時間の記録のある行なども無視。)

### Redmineを更新するデータ

- 上記のフォーマットに合致した行の行頭で設定されている時間から、作業時間を割り出して行末に追記します。
- その後、上記のデータをRedmineの作業時間に追記します。
- Redmineには、自動的に本日の日付で追記されます。

### 注意

- 検索して処理する都合上、コメントは4文字以上にしてください。(スペースで埋めてもいいです。)
- 行単位で処理しますのでコメントに改行は入れられません。
- 文字コードは UTF-8 を想定しています。

## 使用方法

### 初回設定

#### PHP環境設定

本プログラムは、cURLを使用して通信を行っています。環境によっては設定が必要ですので、うまく動作しない場合は下記の設定を確認してください。

1. PHP.iniでcURLがコメントアウトされていないか

   行頭の「;」でコメントアウトされている場合は、外してください。

   PHP.ini

   ```php
   ;extension=curl
   extension=curl
   ```

1. 証明書がない場合、設定してください。
   証明書は、下記からダウンロードできますので、適切な位置に入れてパスを設定してください。(推奨パス：phpのパス\extras\ssl\cacert.pem)

   [https://curl.haxx.se/ca/cacert.pem](https://curl.haxx.se/ca/cacert.pem)

   PHP.ini

   ```php
   [curl]
   ; A default value for the CURLOPT_CAINFO option. This is required to be an
   ; absolute path.
   curl.cainfo = C:\php\extras\ssl\cacert.pem
   ```


#### パラメータの設定

Redmineの個人設定から「APIアクセスキー」を取得してtimeResister.phpに設定してください。

設定項目は下記のようになっていますが、基本的にAPIアクセスキーの設定のみで結構です。

##### 設定項目の詳細

  1. 活動IDの設定
  1. API Key
  1. Redmine URL
  1. 改行コード(テキストファイルに書き戻すときの改行コード)

     ```php
     // ---------------------- 設定 ----------------------
     //Redmineの活動ID 作業:11
     $activityID = 11;
     //RedmineAPI Key
     $apiKey = '67846d2f3b39e9d04b5bbfb927370ef612bb00f4';
     // Redmine時間記録URL
     $url = "https://my.redmine.jp/toumei/time_entries.xml";
     // for windows \r\n
     $nl = "\r\n";
     // ---------------------- /設定 ----------------------
     ```

### 通常の使用

- ターミナルから下記のように実行します。

  ```sh
  $ php timeResister.php schedule.md
  ```

  第一引数に読み取り元のテキストファイルを指定してください。
- 正常に実行されると、ターミナルに実行結果が表示されますので、確認してください。
