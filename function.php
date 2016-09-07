<?php

function getArgs($arg1, $arg2, $arg3, $type){
    $arg_list = func_get_args();
    echo __CLASS__;
    echo __FUNCTION__;
    var_dump($arg_list);
}

getArgs("1", "a", "c", "d");