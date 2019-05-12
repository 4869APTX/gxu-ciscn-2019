<?php

session_start();
if(!isset($_SESSION['id'])){
    header("location:login.php");
}else{
    $userid = $_SESSION['id'];
    $user = getUser($userid);
    if ($_COOKIE['check'] == md5($_SESSION['id'].'admin')){
        
        echo <<<EOF
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8"/>
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title>admin</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
        </head>
        <a href="login.php?action=logout">logout</a><br>
EOF;
        print('you are admin<br>');
        if (isset($_POST['url'])) { 
            echo $_POST['url'];
            $content = file_get_contents($_POST['url']); 
            $ip = $_SERVER['REMOTE_ADDR'];
            // var_dump($content);
            // $path = 'D:\\phpstudy\\PHPTutorial\\WWW\ciscn\\ssrf\\';
            // $filename =$path.'images\\'.rand().'img1.jpg'; 
            // file_put_contents($filename, $content); 
            $img = "<img src=\"".$content."\"/>"; 
        }
        echo @$img;
        
        echo 'look image site,enter you url';
        echo <<<EOF
        <form action = 'index.php' method='POST'>
            <input type="text" name='url'>
            <input id="subLogin"  name ="subLogin" type="submit" value="提交" />
        </form>
EOF;
    }
    if ($_COOKIE['check'] == md5($_SESSION['id'].'user')){
        print('you not admin');
    }
    $userid = $_SESSION['id'];
    $user = getUser($userid);
    // setcookie('a');
}
    





    function getUser($userid){
        include 'config.php';
        $sql = 'select * from user where id = ?';
        $conn = new mysqli($dbservername,$dbusername,$dbpassword,$dbname);
        if($conn->connect_error){
               die("Connect failed:".$conn->connect_error);
        }
        $user = [];
        $sql_stmt = $conn->prepare($sql);
        $sql_stmt->bind_param("i",$userid);
        $sql_stmt->bind_result($user['id'],$user['username'],$a,$user['balance'],$user['vip_level']);
        $sql_stmt->execute();
        $sql_stmt->fetch();
        $sql_stmt->free_result();
        $sql_stmt->close();
        $conn->close();
        return $user;
        
    }
