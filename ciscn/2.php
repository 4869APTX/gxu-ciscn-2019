<?php
$e  = stripcslashes(preg_replace('/[^0-9\\\]/','',isset($_GET['num']) ? $_GET['num'] : '25'));
echo $e.'<br>';
var_dump($e);
if (empty($e)){
   
}else{
    system('type '.$e);
}
highlight_file('2.php');