<?php
header("Content-Type:text/html;charset=utf-8");

echo $_SERVER ['HTTP_HOST']."\r\n";
$host = $_SERVER ['HTTP_HOST'];

$key = "Sogou Spider";
if (preg_match("|".preg_quote($key)."|i", "111111Sogou News Spider2222"))
{
    echo "robot \r\n";
}else{
    echo "human \r\n";
}

/**
 *  获取搜索关键字
 * @return bool|string
 */
function _getKeyword(){
    $url = "https://www.sogou.com/link?url=DSOYnZeCC_rkKZ_aQy7ppm3vn71rSWJXbhhp9J6i36M.&query=3%E7%A9%BA%E9%97%B4";
    $engines = array(
        array("host" => ".baidu.com", "key" => array("wd","word")),
        array("host" => ".so.com", "key" => array("q","k")),
        array("host" => ".sogou.com", "key" => array("query","keyword")),
        array("host" => ".sm.cn", "key" => "q"),
        array("host" => ".google.", "key" => "q"),
    );

    foreach ($engines as $engine) {
        if (strpos($url,$engine["host"]) !== FALSE){
            echo "host:".$engine["host"]."\r\n";
            if(is_array($engine["key"])){
                foreach ($engine["key"] as $key){
                    $value = _getValueFromUrl($url,$key);
                    if ($value){
                        return $value;
                    }
                }
            }else{
                $value = _getValueFromUrl($url,$key);
                if ($value){
                    return $value;
                }
            }
        }
    }

    return "";
}

function _getValueFromUrl($url,$key){
    $keyPreg = '@.*\&?{key}\=([^\&]*)\&?.*@i';
    $preg = str_replace("{key}",$key,$keyPreg);
    preg_match($preg, $url, $matches);

    if(!empty($matches) && isset($matches[1])){
        if(!empty($matches[1])){
            return urldecode($matches[1]);
        }
    }
    return FALSE;
}

echo _getKeyword()."____";