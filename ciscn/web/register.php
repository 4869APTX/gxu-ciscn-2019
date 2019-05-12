<?php
include 'config.php';
session_start();
function checkUserName($checkusername){
    include 'config.php';
    $conn = new mysqli($dbservername,$dbusername,$dbpassword,$dbname);
    if($conn->connect_error){
        die("Connect failed:".$conn->connect_error);
    }
    $sql = "select id from user where username = ?";
    $sql_stmt = $conn->prepare($sql);
    $sql_stmt->bind_param("s",$checkusername);
    $sql_stmt->bind_result($username_result);
    $sql_stmt->execute();
    $sql_stmt->fetch();
    $sql_stmt->free_result();
    $sql_stmt->close();
    $conn->close();
    return $username_result;
}

function alert($word,$link){
    echo "<script>alert('$word');</script>";
    echo "<script language='javascript' type='text/javascript'>";
    echo "window.location.href='$link.php'";
    echo "</script>";
}

function insertUser($username,$password){
    include 'config.php';
    echo 'inserting....';
    $conn = new mysqli($dbservername,$dbusername,$dbpassword,$dbname);
    if($conn->connect_error){
        die("Connect failed:".$conn->connect_error);
    }
    try{
        $sql = "insert into user (username,password) values (?,?)";
        $sql_stmt = $conn->prepare($sql);
        $sql_stmt->bind_param("ss",$username,$password);
        $sql_stmt->execute();
        $sql_stmt->close();
        $conn->close();
        return TRUE;
    }catch( Exception $e){
        return FALSE;
    }
    
}

if(isset($_SESSION['id'])){
    header("location:index.php");
}else{
    if(isset($_POST['checkusername'])){
        // echo $_POST['checkusername'];
        if(checkUserName($_POST['checkusername'])!==NULL){
            $result['result']='yes';
        }else{
            $result['result']='no';
        }
        echo json_encode($result);
    }elseif(isset($_POST['username'])&&isset($_POST['password'])&&isset($_POST['verifytext'])){
        $verifycode=strtolower($_POST['verifytext']);  //转换为小写
        if($verifycode!=strtolower($_SESSION['code'])){
            $_SESSION['code']='';
            alert('verifycode error','register');
            // header('location:index.php');
        }else{
            $_SESSION['code']='';
            if(checkUserName($_POST['username'])==NULL){
                try{
                    if(insertUser($_POST['username'],$_POST['password'])){
                        alert('register success','index');
                    }
                    else{
                        alert('register error','register');
                    }
                    
                }catch(Exception $e){
                    alert('system error!!!','register');
                }
                
            }else{
                alert('username error','register');
            }
        }
        
    }else{
        echo<<<EOF
<head>
<script src="https://lib.sinaapp.com/js/jquery/2.0.2/jquery-2.0.2.min.js">
</script>
</head>
<meta charset="utf-8">

<title>login</title>


<div class="row">
<form action="register.php" method="post" class="col-lg-6 col-lg-offset-3">
    <div class="form-group">
        <label>用户名：</label>
        <input type="text" class="form-control" name="username" placeholder="" required>
        <span id="notice"></span>
    </div>
    <div class="form-group">
        <label>密码：</label>
        <input type="password" class="form-control" name="password" placeholder="" required>
    </div>

    <div >
        <label class="mdui-textfield-label">验证码</label>
        <input  class="mdui-textfield-input" type="text" name="verifytext" id="verifytext" required/>
        <img  src="verifycode.php" name="verifycode" height="25" onclick="this.src='verifycode.php?tm='+Math.random()" >  
    </div>
    <button class="btn btn-primary pull-right" type="submit">注册</button>
</form>
</div>
<br/>

<script>
$(document).ready(function(){
    $("input[name='username']").blur(function(){
    if ($(this).val()=='')
    {
        $("#notice").text("");
        return;
    }
    $.post('register.php',{'checkusername':$(this).val()},function(data){
        if(data["result"]=="yes")
        {
            $("#notice").text("用户名已注册");
        }
        else if (data["result"]=="no")
        {
            $("#notice").text("用户名未注册");
        }
        else
        {
            $("#notice").text("");
        }
    },'json');
    });
});
</script>
EOF;
    }


}