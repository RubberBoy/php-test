<?php

/**
 * Class Uri_pram_resolver
 * url参数解析器
 */
class Uri_pram_resolver
{

	//处理的uri格式
    private $uri_model;

	//处理的uri字符串
    private $uri_str;

	//当前uri_model索引位置
    private $cur_model_index = 0;

	//当前uri_str索引位置
    private $cur_uri_index = 0;
	
	//下一分隔符起始位置
    private $next_split_start_pos = 0;

	//参数key数组 - 当参数之间无分隔符时使用key来做分隔依据
    private $param_keys;

    function __construct ($uri_model,$uri_str){
        $this->uri_model = $uri_model;
        $this->uri_str = $uri_str;
        $this->param_keys = self::get_param_keys($this->uri_model);
    }

    public function get_params() {
        $params = array();

        while (TRUE) {
			
			//下一占位符处理前，将索引移至占位符起始位置：移动距离为分隔符的长度
            $split = $this->get_next_split();
            if ($split === FALSE) {
                return FALSE;
            }
            $this->cur_uri_index += $split["split_len"];
            $this->cur_model_index += $split["split_len"];

			//获取下一占位符
            $flag = self::get_next_flag($this->uri_model,$this->cur_model_index);
            if ($flag === FALSE) {
                break;
            }
            $this->next_split_start_pos = $flag["end_pos"] + 1;

            $key_len = strlen($flag["flag"]);
            //占位符为key
            if ( strpos($flag["flag"],"value") === FALSE ) {
                $uri_key = substr($this->uri_str,$this->cur_uri_index,$key_len);

                //key相等,则索引同时后移
                if ($uri_key == $flag["flag"]) {
                    $this->cur_uri_index += $key_len ;
                    $this->cur_model_index += $key_len + 2;
                    continue;
					
				//key不等,说明该key无值,则model索引移植下一key处
                }else{
                    $flag = self::get_next_flag($this->uri_model,$this->cur_model_index + $key_len + 2);
                    if ($flag === FALSE) {
                        break;
                    }
                    $this->cur_model_index = $flag["end_pos"] + 1;
                    $this->next_split_start_pos = $flag["end_pos"] + 1;

                    continue;
                }

            //占位符为value: 取出uri中该参数的值
            } else {
				$cur_key = substr($flag["flag"],0,$key_len - 6);
				
				//获取参数值下一分隔符
				$split = $this->get_next_split();
				if ($split === FALSE) {
					return FALSE;
				}

				//当分隔符为字符串时，参数值为当前索引与下一分隔符之间的字符串
                if ($split["split_type"] == "STR") {

                    $value_end = strpos($this->uri_str,$split["split_str"],$this->cur_uri_index);
                    $value = substr($this->uri_str,$this->cur_uri_index,$value_end - $this->cur_uri_index);
                    $params[$cur_key] = $value;
				
				//当分隔符为空(split_type=NONE)时,参数值为当前索引与下一参数key之间的字符串
                } else {
                    $value_end = 0;
					/**
					*	确定下一参数key：由于key是顺序的,遇到第一个匹配的即当前值的结束
					*   这里考虑到下一参数可能没有,需要遍历key数组确定下一key
					**/
                    foreach ($this->param_keys as $key) {

                        $value_end = strpos($this->uri_str,$key,$this->cur_uri_index);
                        if ($value_end !== FALSE) {
                            break;
                        }
                    }
					
					//当下一key无匹配时，说明没有后面的参数(无分隔符的)，当前参数值的结束位置先取「/」之前，再取到结尾
                    if ($value_end === FALSE) {
                        $value_end = strpos($this->uri_str,"/",$this->cur_uri_index);
                        if ( ! $value_end) {
                            $value_end = strlen($this->uri_str) + 1;
                        }
                    }

                    $value = substr($this->uri_str,$this->cur_uri_index,$value_end - $this->cur_uri_index);

                    $params[$cur_key] = $value;
                }
				
				//参数值处理完，移动索引至当前参数值结尾处
                $this->cur_uri_index = $value_end;
                $this->cur_model_index += $key_len + 2;
            }
        }
        return $params;
    }

    /**
     * 获取uri_model中所有的key
     * @param $uri_model
     * @return array
     */
    public static function get_param_keys($uri_model) {
        $keys = array();
        $index = 0;
        while (TRUE) {
            $flag_info = self::get_next_flag($uri_model,$index);

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
     * 获取下一占位符
     * @param $uri_model
     * @param $start
     * @return array|bool
     */
    public static function get_next_flag($uri_model,$start) {
        $start_pos = strpos($uri_model,"{",$start);
        if ($start_pos !== FALSE) {
            $end_pos = strpos($uri_model,"}",$start_pos);
            $flag = substr($uri_model,$start_pos + 1,$end_pos - $start_pos -1 );

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
    private function get_next_split() {
        $data = array();
        $next_flag_start_pos = strpos($this->uri_model,"{",$this->next_split_start_pos);
        if ($next_flag_start_pos === FALSE) {
            $data["split_type"] = "STR";
            $data["split_str"] = substr($this->uri_model,$this->next_split_start_pos);
            $data["split_len"] = strlen($this->uri_model) - $this->next_split_start_pos;
        } else if ($next_flag_start_pos - $this->next_split_start_pos == 0){
            $data["split_type"] = "NONE";
            $data["split_len"] = 0;
        } else {
            $data["split_type"] = "STR";
            $data["split_len"] = $next_flag_start_pos - $this->next_split_start_pos;
            $data["split_str"] = substr($this->uri_model,$this->next_split_start_pos,$data["split_len"]);
        }
        return $data;
    }
}