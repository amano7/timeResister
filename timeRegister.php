<?php

namespace Amano7\RedmineTimeRegister;

include "/Users/YasushiAmano/dev/timeRegister/src/TextModify.php";
include "/Users/YasushiAmano/dev/timeRegister/src/RedmineRegister.php";
include "/Users/YasushiAmano/dev/timeRegister/src/LineParser.php";

// 第一引数(ファイル名)
$fileName = $argv[1];

// ファイルを読み込んで、時間計算後ファイルを更新
$textDm = new TextModify();
$textDm->readTextAddWorkTime($fileName);

// 変更したデータをRedmineに登録
$red = new RedmineRegister();
$red->register($textDm->redLines);
