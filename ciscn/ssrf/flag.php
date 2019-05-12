<?php
$ip = $_SERVER['REMOTE_ADDR'];
var_dump($ip);
if ($ip =='127.0.0.1'){
    echo('flag:qwmn12oi4n8hz');
}else{
    echo('Allow only local access');
    die();
}