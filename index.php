<?php
header("Content-Type:text/html;charset=utf-8");

include("UrlPramResolver.php");

$uri_model = "manager/list/{p}{pzlx-value}{f}{flly-value}{q}{qt-value}{b}{bzfy-value}{z}{zsgd-value}{s}{sgsl-value}{x}{sort-value}{y}{y-value}/{m-page-value}";
$uri_str = "manager/list/p76y12";

echo "uri模版: ".$uri_model."\r\n";
echo "uri_str: ".$uri_str."\r\n";
$UrlPramResolver = new UrlPramResolver(array("urlModel"=>$uri_model,"urlStr"=>$uri_str));

$params = $UrlPramResolver->getRequestParams();
echo "requestParams:\r\n";
var_dump($params);

echo "methodParams:\r\n";
$params = $UrlPramResolver->getMethodParams();
var_dump($params);

var_dump($UrlPramResolver->getParamNames());

echo "\r\n";

//$uri_model = "/manager/list/{p}{pzlx-value}-{f}{flly-value}-{q}{qt-value}-{b}{bzfy-value}-{z}{zsgd-value}-{s}{sgsl-value}-{x}{sort-value}-{y}{y-value}/{m-page-value}/";
//$uri_str = "/manager/list/p123-q234/1/";
//
//echo "uri模版: ".$uri_model."\r\n";
//echo "uri_str: ".$uri_str."\r\n";
//$UrlPramResolver = new UrlPramResolver(array("urlModel"=>$uri_model,"urlStr"=>$uri_str));
//$params = $UrlPramResolver->getRequestParams();
//echo "requestParams:\r\n";
//var_dump($params);
//
//echo "methodParams:\r\n";
//$params = $UrlPramResolver->getMethodParams();
//var_dump($params);
//
//echo "\r\n";
//
//$uri_model = "/project/detail/t-{id-value}";
//$uri_str = "/project/detail/t-123/";
//
//echo "uri模版: ".$uri_model."\r\n";
//echo "uri_str: ".$uri_str."\r\n";
//$UrlPramResolver = new UrlPramResolver(array("urlModel"=>$uri_model,"urlStr"=>$uri_str));
//$params = $UrlPramResolver->getUrlParams();
//var_dump($params);


#$preg_str = "/\\/manager\\/(p.*)(q.*)(b.*)(z.*)?/";
#$match_res = array();
#preg_match($preg_str,$uri_str,$match_res);
#var_dump($match_res);