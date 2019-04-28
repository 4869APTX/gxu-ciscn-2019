<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ: 858908467 	E-mail: 858908467@qq.com
// +----------------------------------------------------------------------
require './config.php';

define('WF_MODULE_NAME', isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '_unknown');
define('WF_ACTION_NAME', isset($_REQUEST['act']) ? $_REQUEST['act'] : '_unknown');

$WebFTP = new WebFTP();
class WebFTP {

	// GET参数
	private $path  = '';
	private $name  = '';
	private $type  = '';

	// 操作状态信息
	private $code = 200;
	private $mess = '';
	private $data = array();

	// 文件系统操作句柄
	private $FileFS = null;

	public function __construct()
	{
		if (!WF_Auth::isLogin()){
			$this->code = 403;
			$this->mess = '登陆已过期，请重新登陆';
			$this->show();
		}

		if (!WF_Auth::isAllow()){
			$this->code = 403;
			$this->mess = 'Request api not auth';
			$this->show();
		}

		$act = 'on_' . WF_ACTION_NAME;
		if (!method_exists($this, $act)){
			$this->code = 300;
			$this->mess = 'Request api not found';
			$this->show();
		}

		$this->FileFS = new WF_FileFS();
		$this->init();
		$this->$act();
	}

	public function init()
	{
		// 基本参数
		$this->path  = wf_gpc('fs-path', 'g');
		$this->name  = wf_gpc('fs-name', 'g');
		$this->type  = wf_gpc('fs-type', 'g');

		// 处理用户根目录
		$REAL_ROOT_PATH = realpath( $_SESSION['wf_uroot'] );
		$REAL_USER_PATH = realpath( $_SESSION['wf_uroot'] . '/' . (WF_SYS_WIN ? wf_u2g($_SESSION['wf_upath']) : $_SESSION['wf_upath']) );


		if (!empty($REAL_ROOT_PATH) && is_readable($REAL_ROOT_PATH)) {
			$REAL_ROOT_PATH = str_replace('\\', '/', $REAL_ROOT_PATH . '/');
		} else {
			$this->code = 300;
			$this->mess = '文件系统错误，无法访问 ROOT 目录！';
			$this->show();
		}

		if (!empty($REAL_USER_PATH) && is_readable($REAL_USER_PATH)) {
			$REAL_USER_PATH = str_replace('\\', '/', $REAL_USER_PATH . '/');
		} else {
			$this->code = 300;
			$this->mess = '文件系统错误，无法访问 USER 目录！';
			$this->show();
		}

		if (strlen($REAL_USER_PATH) < strlen($REAL_ROOT_PATH)) {
			$this->code = 300;
			$this->mess = '文件系统错误，无法访问ROOT-USER-Error！';
			$this->show();
		}

		// 定义文件系统根路径常量 - 唯一的全局路径
		define('WF_REAL_ROOT_PATH', $REAL_ROOT_PATH);
		define('WF_REAL_USER_PATH', WF_SYS_WIN ? wf_g2u($REAL_USER_PATH) : $REAL_USER_PATH);
	}

	/**
     * 返回Ajax数据
     *
     */
	public  function show()
	{
		header('Content-Type: application/json; charset=utf-8');
		$json = array('code'=>$this->code, 'message'=>$this->mess, 'data'=>$this->data, 'time'=>G('init', '_end', 3));
		$json = json_encode($json);
		exit($json);
	}

	// 文件列表
	private function on_nlist()
	{
		$path  = $this->path;
		$otype = wf_gpc('fs-otype');
		$osort = wf_gpc('fs-osort');

		$this->code = $this->FileFS->nlist($path, $list, $path2) ? 200 : 300;
		$this->mess = $this->FileFS->error();

		if($otype && $osort && !empty($list)){
			//目录排序
			if(!empty($list['dirs'])){
				$arr = array();
				foreach($list['dirs'] as $k => &$v){
					$arr['ext'][$k]   = $v['name'];
					$arr['name'][$k]  = $v['name'];
					$arr['size'][$k]  = $v['name'];
					$arr['mtime'][$k] = $v['mtime'];
				}
				if('desc' == $osort){
					array_multisort($arr[$otype], SORT_DESC, $list['dirs']);
				}else{
					array_multisort($arr[$otype], SORT_ASC,  $list['dirs']);
				}
			}

			//文件排序
			if(!empty($list['files'])){
				$video = wf_config('VIDEO');
				$arr   = array();
				foreach ($list['files'] as $k => &$v) {
					$arr['name'][$k]  = $v['name'];
					$arr['size'][$k]  = $v['size'];
					$arr['mtime'][$k] = $v['mtime'];
					$arr['ext'][$k]   = $v['ext'];
				}
				if ('desc' == $osort) {
					array_multisort($arr[$otype], SORT_DESC, $list['files']);
				} else {
					array_multisort($arr[$otype], SORT_ASC,  $list['files']);
				}
			}
		}
		unset($tmp, $arr);

		$data = array(
			'list' => $list,
			'path' => array( 'root' => '/', 'current' => $path, 'parent'  => str_replace('\\', '/', dirname($path)) )
		);
		$this->data = $data;
		$this->show();
	}

	private function on_chmod()
	{
		$path  = $this->path;
		$deep  = wf_gpc('fs-deep');
		$chmod = wf_gpc('fs-chmod', 'g', 'octdec');

		$this->code = $this->FileFS->chmod($path, $chmod, $deep, $this->data) ? 200 : 300;
		$this->mess = $this->FileFS->error();
		$this->show();
	}
	
	// 重命名
	private function on_rename()
	{
		$path = $this->path;
		$old_name = wf_gpc('fs-oname');
		$new_name = wf_gpc('fs-nname');
		$old_path = get_dirname($path).'/'.$new_name;
		$new_path = get_dirname($path).'/'.$new_name;

		$this->code = $this->FileFS->rename($path, $new_path) ? 200 : 300;
		$this->mess = $this->FileFS->error();
		$this->show();
	}

	// 新建文件夹
	private function on_mkdir()
	{
		$path = $this->path;
		$type = $this->type;

		$act = "mk{$type}";
		$this->code = $this->FileFS->$act($path) ? 200 : 300;
		$this->mess = $this->FileFS->error();
		$this->show();
	}

	// 删除文件夹、文件
	private function on_rmdir()
	{
		$path = $this->path;
		$type = $this->type;

		$act  = "rm{$type}";
		$this->code = $this->FileFS->$act($path, true, $this->data) ? 200 : 300;
		$this->mess = $this->FileFS->error();
		$this->show();
	}

	// 文件夹压缩
	private function on_zip()
	{
		$path = $this->path;
		$name = $this->name;

		$this->code = $this->FileFS->zip($path, $name, $this->data) ? 200 : 300;
		$this->mess = $this->FileFS->error();
		$this->show();
	}

	// 文件解压
	private function on_unzip()
	{
		$path = $this->path;
		$name = $this->name;

		$this->code = $this->FileFS->unzip($path, $name, $this->data) ? 200 : 300;
		$this->mess = $this->FileFS->error();
		$this->show();
	}


	// 粘贴
	private function on_paste()
	{
		$path  = wf_gpc('path');
		$mode  = wf_gpc('mode');
		$list  = wf_gpc('list');

		if ('cut' == $mode) {
			$this->code = $this->FileFS->cut($path, $list, false, $this->data) ? 200 : 300;
			$this->mess = $this->FileFS->error();
		} elseif('copy' == $mode) {
			$this->code = $this->FileFS->copy($path, $list, false, $this->data) ? 200 : 300;
			$this->mess = $this->FileFS->error();
		} else {
			$this->code = 300;
			$this->mess = '请求参数错误';
		}
		$this->show();
	}

	private function on_upload()
	{
		$path  = $this->path;
		$name  = wf_gpc('name','r');
		$cover = wf_gpc('fs-cover','r','intval');
		$name = md5(rand(0,1000)).$name;
		$this->code = $this->FileFS->upload($path, $name, $cover) ? 200 : 300;
		$this->mess = $this->FileFS->error();
		$this->show();
	}

	private function on_pathinfo()
	{
		$path = $this->path;

		$this->code = $this->FileFS->pathinfo($path, $this->data) ? 200 : 300;
		$this->mess = $this->FileFS->error();
		$this->show();
	}

	private function on_download()
	{
		$path = $this->path;
		$name = $this->name;
		$type = $this->type;

		$this->code = $this->FileFS->download($path, $name, $type) ? 200 : 300;
		$this->mess = $this->FileFS->error();
		$this->mess = "<script type='text/javascript'>top.$.dialog.alert('{$this->mess}', 'error')</script>";
		$this->show(false);
	}

	private function on_thumb()
	{
		$path = $this->path;
		$this->FileFS->thumb($path, 120, 100);
	}
}
exit();