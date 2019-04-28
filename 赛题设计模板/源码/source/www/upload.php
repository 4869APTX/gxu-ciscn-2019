<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ: 858908467 	E-mail: 858908467@qq.com
// +----------------------------------------------------------------------
require './config.php';

if(!WF_Auth::isLogin()){
    wf_redirect('login.php?act=in');
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta name="renderer" content="webkit">
    <title>WebFTP 文件上传组件</title>

    <script src="static/js/jquery-1.7.2.min.js"></script>
    <script src="static/plupload/plupload.min.js"></script>
     <!-- <script src="static/plupload/plupload.js"></script>
    <script src="static/plupload/plupload.flash.js"></script>
    <script src="static/plupload/plupload.html4.js"></script>
    <script src="static/plupload/plupload.html5.js"></script> -->

    <link rel="stylesheet" href="static/plupload/jquery.plupload.queue/jquery.plupload.queue.css" />
    <script src="static/plupload/jquery.plupload.queue/jquery.plupload.queue.min.js"></script>

    <script src="static/js/webftp.core.js"></script>
    <script src="static/js/webftp.upload.js"></script>
    <style type="text/css">
        body { font-family:Verdana, Geneva, sans-serif; font-size: 13px; color: #333; margin: 0; }
        .plupload_container {padding: 0;};
    </style>
</head>
<body >
<div id="uploader" style="width:100%;height:10%;">Your browser doesn't support upload.</div>
<script type="text/javascript">
$(function (){
    // 配置参数
    // app.debug = true;
    app.api  = {
        key: 'asljasasfgjsag',
        url: './webftp.php?mod=nfs&act=upload',
        path: '/?/', cover: 0,
        wf_ssid: '<?php echo session_id();?>',
    };

    app.settings = {
        max_file_size: '<?php echo wf_config('UPLOAD.MAX_FILE_SIZE');?>', 
        chunk_size: '<?php echo wf_config('UPLOAD.CHUNK_SIZE');?>',
        filters: []
    };
    
    
    // 执行初始化
    app.upload.init();
    window.dialog = parent.$.dialog({id : 'upload'});
    window.dialog.title('文件上传 - 准备就绪');
});
</script>
</body>
</html>