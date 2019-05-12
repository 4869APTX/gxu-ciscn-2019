<?php
$ip = $_SERVER['REMOTE_ADDR'];
var_dump($ip);
if ($ip =='127.0.0.1'){
    echo('flag{this_is_flag}');
}else{
    echo('Allow only local access');
    die();
}