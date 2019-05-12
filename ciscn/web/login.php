<meta charset="UTF-8">
<?php
    include 'config.php';
	$conn = new mysqli($dbservername,$dbusername,$dbpassword,$dbname);
	if($conn->connect_error){
   		die("Connect failed:".$conn->connect_error);
    }

    session_start();
	if(!empty($_POST['username'])&&!empty($_POST['password'])){
		$sql = "select id,password from user where username = ?" ;
		$sql_stmt = $conn->prepare($sql);
		$sql_stmt->bind_param("s",$_POST['username']);
		$sql_stmt->bind_result($id,$password);
		$sql_stmt->execute();
		$sql_stmt->fetch();
		$sql_stmt->free_result();
		$sql_stmt->close();
		$conn->close();
		if ($password === $_POST['password']) {
			$_SESSION['id'] = $id;
			setcookie('id',$id);
			setcookie('check',md5($id.'user'));
			header('location:index.php');
		}else{
			echo "<script>alert('password or username no goodgood');</script>";
			echo "<script language='javascript' type='text/javascript'>";
			echo "window.location.href='login.php'";
			echo "</script>";
		}
	}elseif(!empty($_GET['action'])){
		if($_GET['action']=='logout'){
			unset($_SESSION['id']);
			session_destroy();
			echo "<script>alert('logout success');</script>";
			echo "<script language='javascript' type='text/javascript'>";
			echo "window.location.href='login.php'";
			echo "</script>";
		}
	}else{
		echo <<<EOF
			<meta charset="utf-8">
			<title>login</title>
			<a>welcome to green shop</a>
			<form method="POST" action="login.php">
				<label>username</label>
				<input type="text" name="username">
				<label>password</label>
				<input type="password" name="password">
				<button>login</button>
            </form>
			<a href="register.php">register</a>
			<!-- 我不会写前端 -->
			
EOF;
	}

