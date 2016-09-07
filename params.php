<?php
include("UrlPramResolver.php");

$params = array("flly"=>76,"zsgd"=>56);

var_dump(getUrl(null,$params));

function getUrl($code,$params){

    $uri = "manager/list/{p}{pzlx-value}{flly-v}{flly-value}{q}{qt-value}{b}{bzfy-value}{z}{zsgd-value}{s}{sgsl-value}{x}{sort-value}{y}{y-value}/{m-page-value}/";

    $UrlPramResolver = new UrlPramResolver(array("urlModel"=>$uri,"urlStr"=>null));

    $paramNames = $UrlPramResolver->getParamNames();

    if ( ! empty($paramNames["hasKeyParams"])){
        foreach ($paramNames["hasKeyParams"] as $paramName=>$key){
            if (isset($params[$paramName])){
                $uri = preg_replace("+{(" . UrlPramResolver::METHOD_PARAM_PREFIX . ")?" . $paramName . UrlPramResolver::PARAM_VALUE_SUFFIX . "}+",
                    $params[$paramName], $uri);
                $uri = preg_replace("+{". $key ."}+", $key, $uri);
                unset($params[$paramName]);
            }
        }
    }

    if ( ! empty($paramNames["noKeyParams"])){
        foreach ($paramNames["noKeyParams"] as $paramName){
            if (isset($params[$paramName])) {
                $uri = preg_replace("{(" . UrlPramResolver::METHOD_PARAM_PREFIX . ")?." . $paramName . UrlPramResolver::PARAM_VALUE_SUFFIX . ".}",
                    $params[$paramName], $uri);
                unset($params[$paramName]);
            }
        }
    }

    $uri = preg_replace("+{.*?}+", "", $uri);
    $uri = preg_replace("+\\/{2,}+","/",$uri);

    if ( ! empty($params)){
        $uri .= '?' . http_build_query($params);
    }

    return $uri;

}