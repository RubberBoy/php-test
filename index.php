<?php
header("Content-Type:text/html;charset=utf-8");

include("Uri_pram_resolver.php");

$uri_model = "/manager/aa{p}{pzgz-value}{f}{flly-value}{q}{qt-value}{b}{bzfy-value}{z}{zsgd-value}{s}{s-value}{x}{x-value}{y}{y-value}/{page-value}/";
$uri_str = "/manager/aap123q123b432x0y1/123/\r\n";

echo "uri模版: ".$uri_model."\r\n";
echo "uri_str: ".$uri_str."\r\n";
$uri_pram_resolver = new Uri_pram_resolver($uri_model,$uri_str);

$params = $uri_pram_resolver->get_params();
var_dump($params);

echo "\r\n";

$uri_model = "/manager/aa-{p}-{p-value}{f}-{f-value}{q}-{q-value}{b}-{b-value}{z}-{z-value}{s}-{s-value}{x}-{x-value}{y}-{y-value}/{page-value}/";
$uri_str = "/manager/aa-p-123q-123b-432x-88/123/\r\n";

echo "uri模版: ".$uri_model."\r\n";
echo "uri_str: ".$uri_str."\r\n";
$uri_pram_resolver = new Uri_pram_resolver($uri_model,$uri_str);
$params = $uri_pram_resolver->get_params();
var_dump($params);

echo "\r\n";

$uri_model = "/project/detail/t-{id-value}/";
$uri_str = "/project/detail/t-123/";

echo "uri模版: ".$uri_model."\r\n";
echo "uri_str: ".$uri_str."\r\n";
$uri_pram_resolver = new Uri_pram_resolver($uri_model,$uri_str);
$params = $uri_pram_resolver->get_params();
var_dump($params);


#$preg_str = "/\\/manager\\/(p.*)(q.*)(b.*)(z.*)?/";
#$match_res = array();
#preg_match($preg_str,$uri_str,$match_res);
#var_dump($match_res);