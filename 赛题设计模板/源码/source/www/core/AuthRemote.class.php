<?php
/**
 +------------------------------------------------------------------------------
 * 文件名称： /core/AuthRemote.class.php
 +------------------------------------------------------------------------------
 * 文件描述： 远程认证库
 +------------------------------------------------------------------------------
 */
defined('WF_CORE_ROOT') or die( 'Access not allowed');

class WF_Auth
{
	// 验证是管理员
	static function isAdmin()
	{
		return 'admin' === wf_gpc('wf_uname', 's');
	}

	// 验证是否有权限
	static function isAllow()
	{
		$auth_arr = wf_gpc('wf_uauth', 'S');
		return (in_array('*', $auth_arr) || in_array(WF_ACTION_NAME, $auth_arr)) ? true : false;
	}

	// 验证是否登录
	static public function isLogin()
	{
		if($_SESSION['loggedIn']):
	        return true;
        else:
          return false;
        endif;
	}

	// 远程本地登陆认证
	static function loginCheck()
	{
		$uname  = $_POST['wf_uname'];
		$upawd  = $_POST['wf_upawd'];
		$uhash  = wf_gpc('wf_uhash', 'p', 'trim');


		define('WF_REAL_ROOT_PATH', str_replace('\\', '/', realpath(wf_config('ROOT_PATH'))));
		//		define('WF_REAL_USER_PATH', WF_REAL_ROOT_PATH . $user_info['upath']);
		$user_info = self::getUserData($uname);
		include('./mysql.php');
		$con = mysql_connect($mysqlHost,$mysqlUser,$mysqlPwd);
		if (!$con)
		{
		die('Could not connect: ' . mysql_error());
		}


        //include_once("functions.php");
        session_start();

		$sql = 'select pwd from admins where user="'.$uname.'" limit 1';
        $res = mysql_db_query($mysqlDB, $sql, $con);
		$row = mysql_fetch_row($res);
		mysql_close();
		
        if($row[0] == md5($upawd))
        {
            $_SESSION["login"]=$res['login'];
            $_SESSION["password"]=$res['password'];
            $_SESSION["admin"]=$res['admin'];
            $_SESSION["loggedIn"]=true;
            $_SESSION['wf_uauth'] = Array('*',);
            $_SESSION['wf_uname'] = $user_info['uname'];
            $_SESSION['wf_uroot'] = WF_REAL_ROOT_PATH;
            $_SESSION['wf_upath'] = '/';
            $_SESSION['wf_uhost'] = 'http://' . $_SERVER['HTTP_HOST'] . WEB_PATH . 'data/nfs';

            $_SESSION['wf_tokey'] = self::getTokey();
            $_SESSION['wf_error'] = '';
            wf_redirect('./');
            return true;
        }
        $_SESSION = array();
        $_SESSION['wf_error'] = '账户不存在或密码有误！';
        wf_redirect('login.php?act=in');
        exit();
    }

	// 登出
	static public function loginOut()
	{
		$_SESSION = array();
		unset($_SESSION);
	}

	static public function getTokey()
	{
		return md5($_SERVER['HTTP_USER_AGENT'] . date('Y-m-d') . WF_API_KEY);
	}
}