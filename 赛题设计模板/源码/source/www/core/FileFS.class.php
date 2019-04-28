<?php
/**
 +------------------------------------------------------------------------------
 * 文件名称： core/FileFS.class.php
 +------------------------------------------------------------------------------
 * 文件描述： LocalFS 文件系统
 +------------------------------------------------------------------------------
 */
defined('WF_CORE_ROOT') or die( 'Access not allowed');

class WF_FileFS
{

	// 错误信息
	private $error = '';

	/**
	 * 架构方法
	 *
	 */
	public function __construct()
	{
	}


	/**
     * get_gpath获取以/开头的虚拟路径，GB2312 用于访问文件系统
     * get_upath获取以/开头的虚拟路径，UTF-8  用于页面输出
     * @param unknown_type $path
     * @param unknown_type $type
     * @return unknown
     */
	public function get_gpath($path, $type='u')
	{
		if ('r' == $type) $path = str_replace('//', '/', WF_REAL_ROOT_PATH . $path);
		if ('u' == $type) $path = str_replace('//', '/', WF_REAL_USER_PATH . $path);
		$path = WF_SYS_WIN ? wf_u2g($path) : $path;
		return $path;
	}
	private function get_upath($path, $type='u')
	{
		$path = WF_SYS_WIN ? wf_g2u($path) : $path;
		if ('r' == $type) $path = substr($path, strlen(WF_REAL_ROOT_PATH) -1);
		if ('u' == $type) $path = substr($path, strlen(WF_REAL_USER_PATH) -1);
		return str_replace('//', '/', $path);
	}

	/**
     *  遍历目录返回详单
     *
     * @param string  $path 起始路径
     * @param integer $deep 遍历深度 -1 
     * @return unknown
     */
	private function get_golblist($path, $deep=-1)
	{
		if(0 == $deep || !is_readable($path)) return array();

		$arrs = $info = array();
		$arrs = glob($path.'{,.}*', GLOB_MARK|GLOB_BRACE) or array();
		foreach ($arrs as $v){
			$v = str_replace('\\', '/', $v);
			if(in_array(basename($v), array('.','..'))) continue;

			$info[] = $v;
			if('/' == substr($v, -1, 1)){
				$info = array_merge($info, $this->get_golblist($v, --$deep));
			}
		}
		return $info;
	}

	/**
     * 返回给定目录的文件 列表详单
     *
     * @param unknown_type $path
     * @param unknown_type $deep
     * @return unknown
     */
	public function nlist($path, &$list=array(), &$path2='')
	{
		$list = array('dirs'=>array(),'files'=>array());

		$path2 = $this->get_gpath($path);
		if (!is_dir($path2)){
			$this->error = '文件系统错误，目录不存在';
			return false;
		}else if(!is_readable($path2)){
			$this->error = '文件系统错误，没有访问权限';
			return false;
		}

		$tlist = glob($path2.'{,.}*', GLOB_MARK|GLOB_BRACE) or array();
		foreach($tlist as $file2){
			if('.' == basename($file2) || '..' == basename($file2)) continue;
			$file = str_replace('\\', '/', $file2);
			$file = $this->get_upath($file);
			$stat = stat($file2);
			if (!$stat){
				$stat = array('size'=>0,'atime'=>0,'ctime'=>0,'mtime'=>0);
			}

			$stat['name']   = get_basename($file);
			$stat['path']   = $path . $stat['name'];
			$stat['fsize']  = get_deal_size($stat['size']);
			$stat['chmod']  = get_deal_chmod($file2, false);
			$stat['fchmod'] = get_deal_chmod($file2, true);
			$stat['fatime'] = date('Y-m-d H:i:s', $stat['atime']);
			$stat['fctime'] = date('Y-m-d H:i:s', $stat['ctime']);
			$stat['fmtime'] = date('Y-m-d H:i:s', $stat['mtime']);


			if('/' == substr($file, -1, 1)){
				$stat['path'] .= '/';
				$stat['type'] = 'dir';
				$stat['ext']  = '_dir';
			}else{
				$stat['type'] = 'file';
				$stat['ext']  = get_fileext($file);
			}
			$list["{$stat['type']}s"][] = $stat;
		}

		unset($tlist, $stat, $path, $file, $file2);//$path2,
		return true;
	}

	/**
	 * 重命名 文件夹、文件
	 *
	 * @param unknown_type $oldname
	 * @param unknown_type $newname
	 * @return unknown
	 */
	public function rename($oldname, $newname)
	{
		$oldname = $this->get_gpath($oldname);
		$newname = $this->get_gpath($newname);

		if (!file_exists($oldname)) {
			$this->error = '文件系统错误，原始文件不存在';
		} elseif(file_exists($newname)) {
			$this->error = '文件系统错误，已存在同名文件';
		} elseif(!is_writeable(get_dirname($oldname))) {
			$this->error = '文件系统错误，没有修改权限';
		} elseif (!rename($oldname, $newname)) {
			$this->error = '文件系统错误，重命名失败';
		} else {
			$this->error = '';
		}

		return empty($this->error);
	}

	/**
	 * 新建 文件夹
	 *
	 * @param unknown_type $path
	 * @param unknown_type $mode
	 * @return unknown
	 */
	public function mkdir($path, $mode=0755)
	{
		$path = $this->get_gpath($path);
		if(file_exists($path)){
			$this->error = '文件系统错误，目录已存在！';
		}else if(!is_writeable(get_dirname($path))){
			$this->error = '文件系统错误，没有访问权限';
		}else if(!mkdir($path, $mode)){
			$this->error = '文件系统错误，新建失败';
		}else{
			$this->error = '';
		}

		return empty($this->error);
	}

	/**
	 * 新建 文件
	 *
	 * @param unknown_type $path
	 * @param unknown_type $content
	 * @return unknown
	 */
	public function mkfile($path, $content=' ')
	{
		$path = $this->get_gpath($path);
		if(file_exists($path)){
			$this->error = '文件系统错误，文件已存在！';
		}else if(!is_writeable(get_dirname($path))){
			$this->error = '文件系统错误，没有访问权限！';
		}else if(!file_put_contents($path, $content)){
			$this->error = '文件系统错误，文件写入失败';
		}else{
			$this->error = '';
		}

		return empty($this->error);
	}
	public function rmdir($path, $deep=false, &$info=array())
	{
		$path = $this->get_gpath($path);
		$info['de'] = $info['ds'] = $info['fe'] = $info['fs'] = $info['si'] = 0;
		$info['el'] = array();

		if(!is_dir($path)){
			$this->error = '文件系统错误，目录不存在';
			return false;
		}else if(!is_writeable($path)){
			$this->error = '文件系统错误，没有访问权限';
			return false;
		}

		$list = $this->get_golblist($path, -1);
		$list = array_reverse($list);
		foreach ($list as $val){
			if ('/' == substr($val, -1, 1)){
				if (is_writeable($val) && rmdir($val)){
					$info['ds'] += 1;
				} else{
					$info['de'] += 1;
					$info['el'][] = $this->get_upath($val);
				}
			} else{
				$size = filesize($val) or 0;
				if (is_writeable($val) && unlink($val)){
					$info['fs'] += 1;
					$info['si'] += $size;
				} else{
					$info['fe'] += 1;
					$info['el'][] = $this->get_upath($val);
				}
			}
		}

		$info['si'] = get_deal_size($info['si']);
		if (!$info['el'] && rmdir($path)){
			$info['ds'] += 1;
		} else{
			$info['de'] += 1;
			$info['el'][]  = $this->get_upath($path);
		}

		$info['el'] = array_reverse( $info['el']);
		return true;
	}

	/**
     * 文件删除
     *
     * @param unknown_type $type
     * @param unknown_type $objs
     * @param unknown_type $info
     * @return unknown
     */
	public function rmfile($path, $deep=false, &$info=array())
	{
		$path = $this->get_gpath($path);
		if(!is_file($path)){
			$this->error = '文件系统错误，文件不存在';
		} else if(!is_writeable($path)){
			$this->error = '文件系统错误，没有访问权限';
			return false;
		} else if(!unlink($path)){
			$this->error = '文件系统错误，文件删除失败';
		} else {
			$this->error = '';
		}
		return empty($this->error);
	}

	public function rmall($paths, $deep=false, &$info=array())
	{
		$info['de'] = $info['ds'] = $info['fe'] = $info['fs'] = $info['si'] = 0;
		$info['el'] = array();

		foreach ($paths as $path){
			$path = $this->get_gpath($path);

			// 删除文件
			if (is_file($path)){
				$info['si'] += filesize($path);
				if (is_writeable($path) && unlink($path)){
					$info['fs'] += 1;
				}else{
					$info['el'][] = $this->get_upath($path);
				}
				continue;
			}

			// 删除文件夹
			$list = $this->get_golblist($path, -1);
			$list = array_reverse($list);
			foreach ($list as $val){
				if ('/' == substr($val, -1, 1)){
					if (is_writeable($val) && rmdir($val)){
						$info['ds'] += 1;
					} else{
						$info['de'] += 1;
						$info['el'][] = $this->get_upath($val);
					}
				} else{
					$size = filesize($val);
					if (is_writeable($val) && unlink($val)){
						$info['fs'] += 1;
						$info['si'] += $size;
					} else{
						$info['fe'] += 1;
						$info['el'][] = $this->get_upath($val);
					}
				}
			}

			if (is_writeable($path) && rmdir($path)){
				$info['ds'] += 1;
			} else{
				$info['de'] += 1;
				$info['el'][]  = $this->get_upath($path);
			}
		}

		$info['si'] = get_deal_size($info['si']);
		$info['el'] = array_reverse( $info['el']);
		return true;
	}

	/**
     * 剪切目录、文件
     *
     * @param string $path
     * @param array $list
     * @param boolean $force
     * @param array $info
     * @return boolean
     */
	public function cut($path, $list=array(), $force=false, &$info=array())
	{
		$path2 = $this->get_gpath($path);
		if(!is_writeable($path2)){
			$this->error = '文件系统错误，没有访问权限';
			return false;
		}

		$info['success'] = $info['errors'] = 0;
		$info['exists']  = $info['permission'] = array();
		foreach ($list as $file){
			$from = $this->get_gpath($file);
			$to   = $this->get_gpath($path.get_basename($file));
			if (file_exists($to)){
				$info['errors'] += 1;
				$info['exists'][] = $file;
			} else if(!is_writeable($from)){
				$info['errors'] += 1;
				$info['permission'][] = $file;
			} else if(!rename($from, $to)){
				$info['errors'] += 1;
			} else {
				$info['success'] += 1;
			}
		}
		return true;
	}

	/**
     * 复制目录、文件
     *
     * @param string $path
     * @param array $list
     * @param boolean $force
     * @param array $info
     * @return boolean
     */
	public function copy($path, $list=array(), $force=false, &$info=array())
	{
		$path2 = $this->get_gpath($path);
		if(!is_writeable($path2)){
			$this->error = '文件系统错误，没有访问权限';
			return false;
		}

		$info['success'] = $info['errors'] = $info['dnumber'] = $info['fnumber'] = $info['size'] = 0;
		$info['exists']  = $info['permission'] = $info['list'] = array();
		foreach ($list as $val){
			$from = $this->get_gpath($val);
			if (!is_readable($from)){
				$info['errors'] += 1;
				$info['permission'][] = $val;
			} else if (is_dir($from)){
				$to = $this->get_gpath($path.get_basename($val));
				if (file_exists($to)){
					$info['errors'] += 1;
					$info['exists'][] = $val;
					continue;
				} else if (!mkdir($to, 0755, true)){
					$info['errors'] += 1;
					$info['permission'][] = $this->get_upath($to);
				}else{
					$info['success'] += 1;
					$info['dnumber'] += 1;
				}

				$files  = $this->get_golblist($from, -1);
				$strpos = strlen(get_dirname($from)) + 1;
				foreach ($files as $v){
					$to  = $path2 . substr($v, $strpos);
					if (substr($to, -1) == '/'){
						if (!mkdir($to, 0755, true)){
							$info['errors'] += 1;
							$info['permission'][] = $this->get_upath($to);
						}else{
							$info['success'] += 1;
							$info['dnumber'] += 1;
						}
					} else {
						if (file_exists($to)){
							$info['errors'] += 1;
							$info['exists'][] = $this->get_upath($v);
						} else if(!copy($v, $to)){
							$info['errors'] += 1;
							$info['permission'][] = $this->get_upath($v);
						} else {
							$info['size'] += filesize($to);
							$info['success'] += 1;
							$info['fnumber'] += 1;
						}
					}
				}
			} else if (is_file($from)){
				$to = $this->get_gpath($path.get_basename($val));
				if (file_exists($to)){
					$info['errors'] += 1;
					$info['exists'][] = $val;
				} else if(!copy($from, $to)){
					$info['errors'] += 1;
					$info['permission'][] = $val;
				} else {
					$info['size'] = filesize($to);
					$info['success'] += 1;
					$info['fnumber'] += 1;
				}
			}
		}

		$info['size'] = get_deal_size($info['size']);
		return true;
	}

	/**
     * 更改文件权限
     *
     * @param string $path
     * @param integer $chmod
     * @param boolean $deep
     * @param array $info
     * @return boolean
     */
	public function chmod($path, $chmod=0755, $deep=false, &$info=array())
	{
		$path2 = $this->get_gpath($path);
		$info['dn'] = $info['fn'] = $info['en'] = 0;
		$info['el'] = array();

		if (is_file($path2) || (!$deep && is_dir($path2)) ){
			if (!chmod($path2, $chmod)){
				$info['en'] += 1;
			}else {
				$info['fn']  += 1;
			}
			return $info['fn'];
		} else if($deep && is_dir($path2)){
			if (!chmod($path2, $chmod)){
				$info['en'] += 1;
				$info['el'][] = $path;
			}else{
				$info['dn']  += 1;
			}
			$list = $this->get_golblist($path2, -1);
			foreach ($list as $val){
				if (!chmod($val, $chmod)){
					$info['en'] += 1;
					$info['el'][] = $this->get_upath($val);
				}else {
					if (substr($val, -1) == '/'){
						$info['dn']  += 1;
					}else{
						$info['fn']  += 1;
					}
				}
			}
		} else{
			return false;
		}
		return true;
	}

	/**
     * 文件夹、文件压缩
     *
     * @param unknown_type $path
     * @param unknown_type $name
     * @param unknown_type $info
     * @return unknown
     */
	public function zip($path, $name, &$info=array())
	{
		$path = $this->get_gpath($path, 'u');
		$name = $this->get_gpath($name, 'x');

		if (!is_dir($path)){
			$this->error = '文件系统错误，目录不存在';
			return false;
		} else if(!is_writeable(get_dirname($path))){
			$this->error = '文件系统错误，当前目录没有写入权限';
			return false;
		} else if(!is_readable($path)){
			$this->error = '文件系统错误，目录文件没有访问权限';
			return false;
		}

		$ifix = 0;
		$name = get_dirname($path).'/'.$name;
		$file = $name . '.zip';
		while (file_exists($file) && 6 > $ifix++) {
			if (5 < $ifix) $ifix = time();
			$file  = "{$name}_X{$ifix}.zip";
		}

		require WF_CORE_ROOT . 'PclZip.class.php';
		$Zip = new WF_PclZip($file);
		if(!$Zip->create($path, PCLZIP_OPT_REMOVE_PATH, $path)){
			$this->error = '文件系统错误，目录归档错误</br>Error : '.$Zip->errorInfo(true);
			return false;
		}

		$info['dn']   = $info['fn'] = $info['si'] = $info['sc'] = 0;
		$list = $Zip->listContent() or array();
		foreach($list as $val){
			if ($val['folder']) {
				$info['dn'] += 1;
			} else{
				$info['sc'] += $val['compressed_size'];
				$info['si'] += $val['size'];
				$info['fn'] += 1;
			}
		}

		$info['name'] = $this->get_upath(get_basename($file), 'x');
		$info['si']   = get_deal_size($info['si']);
		$info['sc']   = get_deal_size($info['sc']);
		return true;
	}

	/**
     * 文件解压
     *
     * @param unknown_type $path
     * @param unknown_type $name
     * @param unknown_type $info
     * @return unknown
     */
	public function unzip($path, $name, &$info=array())
	{
		$path = $this->get_gpath($path, 'u');
		$name = $this->get_gpath($name, 'x');

		if (false == stripos($name, '.zip') || !is_file($path)){
			$this->error = '文件系统错误，压缩文件不存在';
			return false;
		} else if(!is_writeable(get_dirname($path))){
			$this->error = '文件系统错误，当前目录没有写入权限';
			return false;
		} else if(!is_readable($path)){
			$this->error = '文件系统错误，目录文件没有访问权限';
			return false;
		}

		$ifix = 0;
		$name = get_dirname($path) .'/'. substr(get_basename($name), 0, -4);
		$file = $name . '/';
		while (file_exists($file) && 6 > $ifix++) {
			if (5 < $ifix) $ifix = time();
			$file = "{$name}_X{$ifix}/";
		}

		require WF_CORE_ROOT . 'PclZip.class.php';
		$Zip = new WF_PclZip($path);

		if (!$Zip->extract($file, false)){
			$this->error = '文件系统错误，文件解压失败</br>Error : '.$Zip->errorInfo(true);
			return false;
		}

		$info['dn']   = $info['fn'] = $info['si'] = $info['sc'] = 0;
		$list = $Zip->listContent() or array();
		foreach($list as $val){
			if ($val['folder']) {
				$info['dn'] += 1;
			} else{
				$info['sc'] += $val['compressed_size'];
				$info['si'] += $val['size'];
				$info['fn'] += 1;
			}
		}

		$info['name'] = $this->get_upath(get_basename($file), 'x');
		$info['si']   = get_deal_size($info['si']);
		$info['sc']   = get_deal_size($info['sc']);
		return true;
	}


	/**
     * 文件上传
     *
     */
	public function upload($path, $name, $cover=false)
	{

		// HTTP headers for no cache etc
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		// Look for the content type header
		if (isset($_SERVER['HTTP_CONTENT_TYPE'])) $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
		if (isset($_SERVER['CONTENT_TYPE'])) $contentType = $_SERVER['CONTENT_TYPE'];

		// Get parameters
		$chunk  = wf_gpc('chunk', 'r','intval');
		$chunks = wf_gpc('chunks','r','intval');


		// 处理文件名
		$file = $this->get_gpath($path.$name);
		if(!is_writeable($this->get_gpath($path))){
			$this->error = '文件系统错误，当前目录没有写入权限';
			return false;
		} else if(!$cover && file_exists($file)){
			$this->error = '文件系统错误，目标文件已存在';
			return false;
		} else if($cover && file_exists($file) && !unlink($file)){
			$this->error = '文件系统错误，无法删除原始文件';
			return false;
		}

		// 上传写文件步骤,这一部分以下的代码可直接引用
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, 'multipart') !== false){
			if (!isset($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
				$this->error = 'Failed to move uploaded file.';
				return false;
			}

			// 分块一直接move，减小不分块时服务器负载
			if (0 == $chunk) {
				if (!move_uploaded_file($_FILES['file']['tmp_name'], "{$file}.part")){
					$tihs->error = 'Failed to open output stream.';
					return false;
				}
			} else {
				// 合并剩余分块数据
				$out = fopen("{$file}.part", $chunk == 0 ? 'wb' : 'ab');
				if (!$out) {
					$tihs->error = 'Failed to open output stream.';
					return false;
				}

				$in = fopen($_FILES['file']['tmp_name'], 'rb');
				if (!$in) {
					$tihs->error = 'Failed to open input stream.';
					return false;
				}
				while ($buff = fread($in, 4096)) {
					fwrite($out, $buff);
				}

				fclose($in);
				fclose($out);
			}

			file_exists($_FILES['file']['tmp_name']) && unlink($_FILES['file']['tmp_name']);
		} else {
			$out = fopen("{$file}.part", $chunk == 0 ? 'wb' : 'ab');
			if (!$out){
				$tihs->error = 'Failed to open output stream.';
				return false;
			}

			// Read binary input stream and append it to temp file
			$in = fopen("php://input", 'rb');
			if (!$in){
				$tihs->error = 'Failed to open input stream.';
				return false;
			}

			while ($buff = fread($in, 4096)){
				fwrite($out, $buff);
			}
			fclose($in);
			fclose($out);
		}

		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			return rename("{$file}.part", $file);
		}
		return true;
	}

	/**
     * 下载文件，支持XSend
     *
     * @param string $type 文件类型
     * @param string $path 文件路径
     * @param string $name 显示名称
     */
	public function download($path, $name, $type='file')
	{
		$path = $this->get_gpath($path);
		if ('file' == $type && is_readable($path)){
			$size = filesize($path);
		} else if('dir' == $type && is_readable($path)){
			require WF_CORE_ROOT . 'PclZip.class.php';
			$name .= '.zip';
			$temp = './data/tmp/'.md5($path).'.tmp';

			$Zip = new PclZip($temp);
			if(!$Zip->create($path, PCLZIP_OPT_REMOVE_PATH, $path)){
				$this->error = '文件系统错误，目录归档错误';
				return false;
			}
			$path = $temp;
			$size = filesize($temp);
		} else{
			$this->error = '文件系统错误，可能没有访问权限';
			return false;
		}

		// 输出文件头、处理中文文件名
		$encoded_nname = rawurlencode($name);
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/MSIE/', $ua)) {
			header("Content-Disposition: attachment; filename={$encoded_nname}");
		} else if(preg_match('/Firefox/', $ua)) {
			header("Content-Disposition: attachment; filename*=utf8'' {$name}");
		} else{
			header("Content-Disposition: attachment; filename={$name}");
		}
		header('Content-type: application/octet-stream');
		header('Content-Encoding: none');
		header('Cache-Control: private');
		header('Accept-Ranges: bytes');
		header('Pragma: no-cache');
		header('Expires: 0');
		header("Content-length: {$size}");
		header("Accept-Length: {$size}");

		// 开始下载文件
		if(!is_readable($path)){
			exit('没有读写权限：'.$path);
		} else if(wf_config('X_SENDFILE_ON')){
			// 使用X-Sendfile发送文件
			header("X-Sendfile: {$path}");
		} else{
			readfile($path);
		}

		if ('dir' == $type) unlink($path);
		exit();
	}

	/**
     * 文件属性
     *
     * @param string $type 
     * @param array  $objs
     * @return array
     */
	public function pathinfo($path, &$info=array())
	{
		$path = $this->get_gpath($path);
		if(!is_readable($path)){
			$this->error = '文件系统错误，目录不存在或者没有访问权限';
			return false;
		}

		$info = stat($path);
		$info['dnums'] = $info['fnums'] = $info['size'] = 0;

		$list = $this->get_golblist($path);
		foreach ($list as $val){
			if('/' == substr($val, -1, 1)){
				$info['dnums']  += 1;
			}else{
				$info['fnums'] += 1;
				$info['size']  += filesize($val);
			}
		}

		$info['name']   = get_basename($path);
		$info['path']   = $this->get_upath($path);
		$info['fsize']  = get_deal_size($info['size']);
		$info['chmod']  = get_deal_chmod($path, 0);
		$info['fchmod'] = get_deal_chmod($path, 1);

		$info['fatime'] = date('Y年m月d日 H:i:s', $info['atime']);
		$info['fctime'] = date('Y年m月d日 H:i:s', $info['ctime']);
		$info['fmtime'] = date('Y年m月d日 H:i:s', $info['mtime']);
		$info['fmtime'] = date('Y年m月d日 H:i:s', $info['mtime']);

		return true;
	}

	public function thumb($path, $windth, $height)
	{
		$expire       = 3600;
		$requestTime  = $_SERVER['REQUEST_TIME'];
		$lastModified = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;

		if ($lastModified && $requestTime <= ($lastModified + $expire)) {
			header('HTTP/1.1 304 Not Modified', true);
			$responseTime = $lastModified;
			$exit = true;
		} else {
			$responseTime = $requestTime;
			$exit = false;
		}

		header('Cache-Control: max-age='.$expire);
		header('Last-Modified: '. gmdate('D, d M Y H:i:s', $responseTime).' GMT');
		header('Expires: '. gmdate('D, d M Y H:i:s', $responseTime + $expire).' GMT');
		if ($exit) exit();

		include(WF_CORE_ROOT.'Thumb.class.php');
		$path = $this->get_gpath($path);
		if (!is_readable($path)){
			$path = WF_DATA_PATH . 'temp/nothumb.jpg';
		}

		$Thumb = new WF_Thumb();
		$Thumb->create($path, $windth, $height);
		$Thumb->show();
	}

	/**
     * 获取错误信息
     * 
     * @return string
     */
	public function error()
	{
		return $this->error;
	}
}