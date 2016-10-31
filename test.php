<?php
header("Content-Type:text/html;charset=utf-8");

$comment = '{"addTimes":1,"type":"change_task_status","time":"2016-08-25 13:50:59","creator":"system","comment":"\u7684\u65b9\u5f0f"}';
var_dump(preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/u', create_function('$match', 'return mb_convert_encoding(pack("H*", $match[1]), "utf-8", "UTF-16BE");'), $comment));

$str = "3室2厅";
$sub = mb_substr($str, 0, 2, "utf-8");
var_dump($str);
var_dump($sub);

$url = "http://api.3kongjian.com/leader/recode";
$url = str_replace('api.3kongjian.com', '3kjapidev.sinaapp.com', $url);

var_dump($url);

$index = strpos("design_new/aaa", "bb");
var_dump($index);