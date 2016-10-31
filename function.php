<?php

function getArgs($arg1, $arg2, $type, $arg3){
    $arg_list = func_get_args();
    echo __CLASS__;
    echo __FUNCTION__;
    var_dump($arg_list);
}

$method = new ReflectionFunction("getArgs");
$params = $method->getParameters();

$data1 = array("arg1"=>"a","arg2"=>"b", "arg3"=>"d", "type"=>"c",);
$data = array();
$i = 0;
foreach ($params as $param) {
    $data[$param->getName()] = $data1[$param->getName()];
}

$method->invokeArgs($data);
$method->invokeArgs($data1);