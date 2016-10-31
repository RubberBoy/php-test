<?php
header("Content-Type:text/html;charset=utf-8");

include("UrlPramResolver.php");

$uri_model = "manager/list/{p}{pzlx-value}{f}{flly-value}{q}{qt-value}{b}{bzfy-value}{z}{zsgd-value}{s}{sgsl-value}{x}{sort-value}{y}{y-value}/{m-page-value}";
$uri_str = "manager/list/p76y12";

$urlPramResolver = new UrlPramResolver(array("urlModel"=>$uri_model,"urlStr"=>$uri_str));

$method = new ReflectionMethod($urlPramResolver, "__construct");
var_dump($method);
$params = $method->getParameters();
foreach ($params as $param) {
    var_dump($param);
}

