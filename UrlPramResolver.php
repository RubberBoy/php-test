<?php

/**
 * Class UrlPramResolver
 * url参数解析器
 */
class UrlPramResolver
{

    //参数值标识后缀
    const PARAM_VALUE_SUFFIX = "-value";

    //方法参数前缀
    const METHOD_PARAM_PREFIX = "m-";

    //处理的uri格式
    private $urlModel;

    //处理的uri字符串
    private $urlStr;

    //当前urlModel索引位置
    private $curModelIndex = 0;

    //当前urlStr索引位置
    private $curUrlIndex = 0;

    //下一分隔符起始位置
    private $nextSplitStartPos = 0;

    //参数key数组 - 当参数之间无分隔符时使用key来做分隔依据
    private $paramKeys;

    private $requestParams = array();

    private $methodParams = array();

    function __construct ($config){
        $this->urlModel = $config["urlModel"];
        $this->urlStr = $config["urlStr"];
        $this->paramKeys = $this->getParamKeyFlags();
        $this->initUrlParams();
    }

    public function getRequestParams(){
        return $this->requestParams;
    }

    public function getMethodParams(){
        return $this->methodParams;
    }

    public function initUrlParams() {

        while (TRUE) {

            //下一占位符处理前，将索引移至占位符起始位置：移动距离为分隔符的长度
            $split = $this->getNextSplit();
            if ($split === FALSE || $split["split_type"] == "END") {
                return FALSE;
            }

            //当下一分隔符和url当前索引后的字符匹配时,url索引前进
            if ($split["split_type"] == "STR"){
                if ( substr($this->urlStr,$this->curUrlIndex,$split["split_len"]) == $split["split_str"]){
                    $this->curUrlIndex += $split["split_len"];
                }else if (substr($this->urlStr,$this->curUrlIndex,$split["split_len"]) == trim($split["split_str"],"/")){
                    $this->curUrlIndex += $split["split_len"] - 1;
                }
            }
            //urlModel索引前进
            $this->curModelIndex += $split["split_len"];

            //获取下一占位符
            $flag = self::getNextFlag($this->urlModel,$this->curModelIndex);
            if ($flag === FALSE) {
                break;
            }
            $this->nextSplitStartPos = $flag["end_pos"] + 1;

            $key_len = strlen($flag["flag"]);
            //占位符为key
            if ( strpos($flag["flag"], self::PARAM_VALUE_SUFFIX) === FALSE ) {
                $uri_key = substr($this->urlStr,$this->curUrlIndex,$key_len);

                //key相等,则索引前进
                if ($uri_key == $flag["flag"]) {
                    $this->curUrlIndex += $key_len ;
                    $this->curModelIndex += $key_len + 2;
                    continue;

                    //key不等,说明该key无值,则model索引前进至下一key处
                }else{
                    $flag = self::getNextFlag($this->urlModel,$this->curModelIndex + $key_len + 2);
                    if ($flag === FALSE) {
                        break;
                    }

                    $this->curModelIndex = $flag["end_pos"] + 1;
                    $this->nextSplitStartPos = $flag["end_pos"] + 1;

                    continue;
                }

                //占位符为value: 取出uri中该参数的值
            } else {
                $cur_key = substr($flag["flag"],0,$key_len - strlen(self::PARAM_VALUE_SUFFIX));
                $cur_value = null;

                //获取参数值下一分隔符
                $split = $this->getNextSplit();

                //当分隔符为末尾时(结尾为`}`),参数为当前位置到结尾
                if ($split["split_type"] == "END") {
                    $cur_value = str_replace("/","",substr($this->urlStr,$this->curUrlIndex));

                    //当分隔符为字符串时，参数值为当前索引与下一分隔符之间的字符串
                }else if ($split["split_type"] == "STR") {
                    $value_end = strpos($this->urlStr,$split["split_str"],$this->curUrlIndex);
                    if ($value_end === FALSE){
                        if ($split["split_str"] == "/"){
                            $value_end = strlen($this->urlStr);
                            $cur_value = substr($this->urlStr,$this->curUrlIndex);
                        }else{
                            $value_end = strpos($this->urlStr,"/",$this->curUrlIndex);
                            $cur_value = substr($this->urlStr,$this->curUrlIndex,$value_end - $this->curUrlIndex);
                        }
                    }else {
                        $cur_value = substr($this->urlStr,$this->curUrlIndex,$value_end - $this->curUrlIndex);
                    }

                    //当分隔符为空(split_type=NONE)时,参数值为当前索引与下一参数key之间的字符串
                } else {
                    $value_end = FALSE;
                    /**
                     *	确定下一参数key：由于key是顺序的,遇到第一个匹配的即当前值的结束
                     *   这里考虑到下一参数可能没有,需要遍历key数组确定下一key
                     **/
                    foreach ($this->paramKeys as $key) {

                        $value_end = strpos($this->urlStr,$key,$this->curUrlIndex);
                        if ($value_end !== FALSE) {
                            break;
                        }
                    }

                    //当下一key无匹配时，说明没有后面的参数(无分隔符的)，当前参数值的结束位置先取「/」之前，再取到结尾
                    if ($value_end === FALSE) {
                        $value_end = strpos($this->urlStr,"/",$this->curUrlIndex);
                        if ( ! $value_end) {
                            $value_end = strlen($this->urlStr) + 1;
                        }
                    }

                    $cur_value = substr($this->urlStr,$this->curUrlIndex,$value_end - $this->curUrlIndex);
                }

                if ($cur_value !== FALSE){
                    if (strpos($cur_key, self::METHOD_PARAM_PREFIX) === FALSE){
                        $this->requestParams[$cur_key] = $cur_value;
                    }else{
                        $this->methodParams[substr($cur_key,strlen(self::METHOD_PARAM_PREFIX))] = $cur_value;
                    }
                }

                //参数值处理完，移动索引至当前参数值结尾处
                $this->curUrlIndex = $value_end;
                $this->curModelIndex += $key_len + 2;
            }

            if ($this->curUrlIndex > strlen($this->urlStr)){
                break;
            }
        }

    }

    /**
     * 获取urlModel中所有的key
     * @return array
     */
    public function getParamKeyFlags() {
        $keys = array();
        $index = 0;
        while (TRUE) {
            $flag_info = self::getNextFlag($this->urlModel,$index);

            if ($flag_info) {
                if ( strpos($flag_info["flag"],"value") === FALSE ) {
                    $keys[] = $flag_info["flag"];
                }
                $index = $flag_info["end_pos"] + 1;
            } else {
                break;
            }
        }
        return $keys;
    }

    /**
     * 获取urlModel中参数 值参数名-key参数名对应
     * @return array
     */
    public function getParamNames(){
        $hasKeyFlags = array();
        $noKeyFlags = array();
        $index = 0;
        $key = null;
        $value = null;
        while (TRUE) {
            $flag_info = self::getNextFlag($this->urlModel,$index);

            if ($flag_info) {
                if ( strpos($flag_info["flag"],self::PARAM_VALUE_SUFFIX) === FALSE ) {
                    $key = $flag_info["flag"];
                    $index = $flag_info["end_pos"] + 1;

                    $flag_info = self::getNextFlag($this->urlModel,$index);

                    if ($flag_info) {
                        if (strpos($flag_info["flag"], self::PARAM_VALUE_SUFFIX) !== FALSE) {
                            $value = substr($flag_info["flag"],0,strlen($flag_info["flag"]) - strlen(self::PARAM_VALUE_SUFFIX));
                            if (strpos($value, self::METHOD_PARAM_PREFIX) !== FALSE){
                                $value = substr($value,strlen(self::METHOD_PARAM_PREFIX));
                            }
                            $hasKeyFlags[$value] = $key;
                            $index = $flag_info["end_pos"] + 1;
                        }
                    }else{
                        break;
                    }
                }else{
                    $value = substr($flag_info["flag"],0,strlen($flag_info["flag"]) - strlen(self::PARAM_VALUE_SUFFIX));
                    if (strpos($value, self::METHOD_PARAM_PREFIX) === FALSE){
                        $noKeyFlags[] = $value;
                    }else{
                        $noKeyFlags[] = substr($value,strlen(self::METHOD_PARAM_PREFIX));
                    }
                    $index = $flag_info["end_pos"] + 1;
                }
            } else {
                break;
            }
        }
        return array("hasKeyParams"=>$hasKeyFlags,"noKeyParams"=>$noKeyFlags);
    }

    /**
     * 获取下一占位符
     * @param $urlModel
     * @param $start
     * @return array|bool
     */
    public static function getNextFlag($urlModel,$start) {
        $start_pos = strpos($urlModel,"{",$start);
        if ($start_pos !== FALSE) {
            $end_pos = strpos($urlModel,"}",$start_pos);
            $flag = substr($urlModel,$start_pos + 1,$end_pos - $start_pos -1 );

            $data = array("flag" => $flag,"end_pos" => $end_pos);
            return $data;
        }
        return FALSE;
    }

    /**
     * 获取下一分隔符
     *  当上一参数的value和下一参数的key之间无分隔时,split_type=NONE
     * @return array|bool
     */
    private function getNextSplit() {
        $data = array();
        if (strlen($this->urlModel) == $this->nextSplitStartPos){
            $data["split_type"] = "END";
        }else{
            $next_flag_start_pos = strpos($this->urlModel,"{",$this->nextSplitStartPos);

            if ($next_flag_start_pos === FALSE) {
                $data["split_type"] = "STR";
                $data["split_str"] = substr($this->urlModel,$this->nextSplitStartPos);
                $data["split_len"] = strlen($this->urlModel) - $this->nextSplitStartPos;
            } else if ($next_flag_start_pos - $this->nextSplitStartPos == 0){
                $data["split_type"] = "NONE";
                $data["split_len"] = 0;
            } else {
                $data["split_type"] = "STR";
                $data["split_len"] = $next_flag_start_pos - $this->nextSplitStartPos;
                $data["split_str"] = substr($this->urlModel,$this->nextSplitStartPos,$data["split_len"]);
            }
        }
        return $data;
    }
}