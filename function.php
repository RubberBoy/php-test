<?php

function getArgs($arg1, $arg2, $arg3, $type){
    $arg_list = func_get_args();
    echo __CLASS__;
    echo __FUNCTION__;
    var_dump($arg_list);
}

getArgs("1", "a", "c", "d");

$designer_credit = array(
    //设计师加入平台签约, 只加一次
    "sign"=>array(
        "reason"=>"平台签约",
        "rule"=>500,
    ),

    "ownerEvaluation"=>array(
        "reason"=>"业主评价",
        "rule"=>function($rate) {
            $score = 0;
            if ($rate == 5) {
                $score = 100;
            } elseif ($rate = 4) {
                $score = 50;
            } elseif ($rate = 3) {
                $score = 0;
            } elseif ($rate =2 ) {
                $score = -50;
            } elseif ($rate =1) {
                $score = -100;
            }

            return $score;
        }
    )
);

echo gettype($arr['method']);
var_dump($arr);
echo $arr['method'](4);