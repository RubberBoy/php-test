<?php
$model = "3空间北京品质工长装修网提供北京范围内的知名品质工长装修队、北京品质工长<辅料未认证>{qt-flag}<{bzfy-flag}保障费><{zsgd-flag}在施工地><施工数量{sgsl-flag}>装修工长、北京品质工长装修工头，更有北京品质工长装修公司排名、北京品质工长装修队排名以及北京品质工长装修公司评分。找装修，就选3空间装修网！";
$model = preg_replace("/<[^>]*?{.*?}[^>]*?>/", "", $model);
var_dump($model);

$model = preg_match("`^(?:.*/)?([^/]+)$`", "bbb", $matchs);
var_dump($model);
var_dump($matchs);

//$matchs = preg_match_all(
//    "/{(.*?)}/",
//    "【{city}{pzlx-flag}装修队】{city}{pzlx-flag}<辅料{flly-flag}>{qt-flag}<{bzfy-flag}保障费><{zsgd-flag}在施工地><施工数量{sgsl-flag}>装修工长_{city}{pzlx-flag}装修工头_{city}{pzlx-flag}装修公司-3空间",
//    $out);
//var_dump($matchs);
//var_dump($out);