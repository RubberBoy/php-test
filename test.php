<?php
header("Content-Type:text/html;charset=utf-8");

$comment = '{"addTimes":1,"type":"change_task_status","time":"2016-08-25 13:50:59","creator":"system","comment":"\u7684\u65b9\u5f0f"}';
var_dump(preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/u', create_function('$match', 'return mb_convert_encoding(pack("H*", $match[1]), "utf-8", "UTF-16BE");'), $comment));

$str = "3室2厅";
$sub = mb_substr($str, 0, 2, "utf-8");
var_dump($str);
var_dump($sub);

$scoreItem = array("Deco_CreditRecord_Role" => "Designer",
    "Deco_CreditRecord_PartyId" => "de",
    "Deco_CreditRecord_ProjectId" => "pr",
    "Deco_CreditRecord_Credit" => "c",
    "Deco_CreditRecord_Reason" => "r",
    "Deco_CreditRecord_Resource" => 'd',
    "Deco_CreditRecord_ReasonName" => 'b',
    "Deco_CreditRecord_CrtTime" => date('Y-m-d H:i:s'));

var_dump($scoreItem);