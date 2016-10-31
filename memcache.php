<?php
header("Content-Type:text/html;charset=utf-8");


$mem = new Memcache();
$mem->connect("127.0.0.1", 11211);
memcache_set($mem, "aa",11);
$aa = memcache_get($mem,"aa");

var_dump($aa);
echo "----";