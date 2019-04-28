<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ: 858908467 	E-mail: 858908467@qq.com
// +----------------------------------------------------------------------

//set_time_limit(0);
error_reporting(0);
error_reporting(E_ALL);
header('Content-type: text/html; charset=utf-8');

// 兼容相关常量
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
	ini_set('magic_quotes_runtime', 0);
	define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc() ? true : false);
} else {
	define('MAGIC_QUOTES_GPC', false);
}
define('WF_SYS_WIN', 'WIN' === strtoupper(substr(PHP_OS, 0,3)));


// 应用主目录
define('WEB_ROOT', str_replace(array('\\','//'), '/', dirname(__FILE__) . '/'));
define('WEB_PATH', str_replace(array('\\','//'), '/', dirname($_SERVER['SCRIPT_NAME']) . '/'));

// 系统核心目录、数据目录
define('WF_CORE_ROOT', WEB_ROOT . 'core/');
define('WF_DATA_PATH', WEB_ROOT . 'data/');

// API通信 常量
define('WF_API_ON',  false);
define('WF_API_KEY', 'eaa043d7d932729623439f2216fb86f6');
define('WF_API_URL', '');



// 加载系统函数库
require WF_CORE_ROOT . 'Functions.php';
require WF_CORE_ROOT . 'FileFS.class.php';
// 加载授权认证库
if (WF_API_ON) {
	require WF_CORE_ROOT . 'AuthRemote.class.php' ;
} else {
	require WF_CORE_ROOT . 'AuthLocal.class.php';
}


// 注册Error处理处理器
set_error_handler('error_handler_fun');
if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('PRC');
}

//  开启SESSION
if (isset($_GET['wf_ssid']) && !empty($_GET['wf_ssid'])) {
	session_id($_GET['wf_ssid']);
}
session_name('webftp_ssid');
session_save_path(WF_DATA_PATH . 'session');
session_set_cookie_params(1800, WEB_PATH);
session_start();


// 全局配置数组
$_CONFIG = array(
	'SYSTEM_NAME'           => 'WebFTP',
	'SYSTEM_VERSION'        => 'v3.6.2 专业版',

	/* 日志设置 */
	'LOG_ON'  				=> true, 	// 记录日志
	'LOG_TYPE'    			=> 'EMERG,ALERT,CRIT,ERR,WARNING,NOTICE,INFO,DEBUG',
	'LOG_FILE_SIZE'         => 2097152, // 默认2MB
	'LOG_SAVE_TYPE'         => 2,       // 1：只保留最新日志,2: 保留所有日志，

	/* 根目录设置 */
	'ROOT_PATH' => './data/nfs', // 系统存储根路径，请勿随意修改
	'USER_PATH' => '/_xx_',       // 用户存储虚拟路径，请勿随意修改

	/* 文件上传配置 */
	'UPLOAD' => array(
		'chunk_size'    => min(8, intval(ini_get('upload_max_filesize'))), // 文件分块大小，单位MB
		'max_file_size' => 1024,  // 上传单个文件限制大小，单位MB
		'filters' => array(
			array('All Files (*.rar;*.htm;*.jpg;*.pdf;*.doc;*.*)', '*,rar,zip,tar,gz,7z,php,js,css,htm,html,xml,jpg,png,gif,bmp,ico,pdf,doc,ppt,xls,docx,pptx,xlsx,wps,et,dps'),
			array('Archive Files (*.rar;*.zip;*.tar;*.gz;*.7z)', 'rar,zip,tar,gz,7z'),
			array('Script Files (*.php;*.js;*.css;*.htm;*.xml)', 'php,js,css,htm,html,xml'),
			array('Images Files (*.jpg;*.png;*.gif;*.bmp;*.ico)', 'jpg,png,gif,bmp,ico'),
			array('Document Files (*.doc;*.ppt;*.xls;*.pdf;wps;*.et;*.dps)', 'pdf,doc,ppt,xls,docx,pptx,xlsx,wps,et,dps'),
		)
	),

);

// 初始化配置参数
wf_config($_CONFIG);


//################### DEBUG
//var_dump( $_CONFIG );
//$des = end(get_defined_constants(true));
//var_dump($des);
//
