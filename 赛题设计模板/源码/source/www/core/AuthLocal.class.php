<?php
/**
 +------------------------------------------------------------------------------
 * 文件名称： /core/AuthLocal.class.php
 +------------------------------------------------------------------------------
 * 文件描述： 本地认证库
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
	static function isLogin()
	{   
		if($_SESSION['loggedIn']):
	        return true;
        else:
          return false;
        endif;
	}

	// 本地登陆认证
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
            $_SESSION["login"]=$uname;
            $_SESSION["password"]=md5($upawd);
            $_SESSION["admin"]=$uname;
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
	static function loginOut()
	{
		$_SESSION = array();
		unset($_SESSION);
		wf_redirect('login.php?act=in');
		exit();
	}


	static public function getTokey()
	{
		return md5($_SERVER['HTTP_USER_AGENT'] . date('Y-m-d') . WF_API_KEY);
	}

	/********* AUTH.TYPE' 为1(local:本地程序认证)是有效 ***************/
	//获取User本地数据路径
	static function getUserPath($uname)
	{
		return WF_DATA_PATH . 'user/' . md5($uname) . '.php';
	}

	// 获取用户
	static function getUserData($uname, $ufile = '')
	{
		$data = '';
		$file = is_file($ufile) ? $ufile : self::getUserPath($uname);

		if (is_file($file)) {
			$data = file_get_contents($file);
			$data = self::decodeUserData($data);
		}

		if(!is_array($data)){
			$data = array('uname'=>'xxx', 'upawd'=>'xxx', 'upath'=>'xxx', 'uauth'=>array());
		}

		//		$data['uauth'] = explode(',', $data['uauth']);
		return $data;
	}

	// 添加用户
	static function addUserData($uname, $upawd, $upath, $uauth=array('*'))
	{
		if (empty($uname) || empty($upawd)) {
			return false;
		}

		$file = self::getUserPath($uname);
		$data = array('uname'=>$uname,'upawd'=>$upawd, 'upath'=>trim($upath), 'uauth'=>$uauth);
		$data = self::encodeUserData($data);
		return file_put_contents($file, $data);
	}

	// 删除用户
	static function delUserData($uname)
	{
		$file = self::getUserPath($uname);
		return is_file($file) ? unlink($file) : true;
	}


	//更新管理员密码
	static function updateUserPassword()
	{
		$uname = wf_gpc('wf_uname', 'S');
		$uinfo = self::getUserData($uname);

		if(!empty($uname) && $uname == $uinfo['uname']){
			$uinfo['upawd'] = md5(wf_gpc('newpasswd','r'));
			if(self::addUserData($uinfo['uname'],$uinfo['upawd'],$uinfo['upath'],$uinfo['uauth'])){
				show(200,'密码已更新,请谨记新密码：<font color="red">'. wf_gpc('newpasswd','r') .'</font>');
			}
		}
		show(300,'更新失败：<font color="red">你可能无权更改此项设置！</font>');
	}

	/**
	 * 编码用户数据
	 *
	 * @param  array $data
	 * @return string
	 */
	static function encodeUserData($data)
	{
		return '<?php exit(); ?>' . serialize($data);
	}

	/**
	 * 解码用户数据
	 *
	 * @param  string $data
	 * @return array
	 */
	static function decodeUserData($data)
	{
		return unserialize(str_replace('<?php exit(); ?>', '', $data));
	}
}