<?php

$conn = new Mongo('192.168.0.164');

$db = $conn->test1;             
$collection = $db->user;  

var_dump($collection->findOne(array("_id"=>"5cd7cfb348a8c7165c005864")));
// if (isset($_GET['id'])){
//     try{

//     }catch{

//     }   
// }
$t=time();
echo($t . "<br>");
$flag = 'ctf123123';
mongo($flag);
echo(date("Y-m-d H:i:s",$t));