<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ: 858908467 	E-mail: 858908467@qq.com
// +----------------------------------------------------------------------
require './config.php';

if (isset($_GET['act'])){
	switch ($_GET['act']){
		case 'login_check': WF_Auth::loginCheck(); break;
		case 'resetpasswd': WF_Auth::updateUserPassword(); break;
		case 'out': WF_Auth::loginOut(); break;
		case 'in':  break;
		default: 
			exit(WF_Session::get('login_error'));
	}
}

//
$uhash = $_SESSION['wf_uhash'] = rand(1000, 9999);
$error = wf_gpc('wf_error', 'S');
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit">
    <title>WebFTP登录 - Powered by OSDU.Net</title>
    <style type="text/css">
        html { }
        body { color:#fff; font-size:14px; padding:0; overflow:hidden; margin:0; width:100%; height:100%; min-height:400px; background:#2784bd url(static/images/login/bg_line.gif) repeat-x left top; }
        #body { background:url(static/images/login/bg.jpg) no-repeat center top; }
        h1 { font-wieght:bold; font-size:20px; padding:0 28px; margin:0; text-shadow:0 1px 2px rgba(0, 0, 0, 0.4); }
        #login_form { position:absolute; width:500px; }


        .input { font-family: Georgia, serif;margin-left:5px;font-size: 22px; padding-left: 12px; padding-right: 0px; width: 234px; height:30px; background:url(static/images/login/spacer.gif); color:#383838; outline:medium none; border:none; }
        .input_div { position:absolute; margin-top:14px; width:290px; height:80px; background:url(static/images/login/login_input_bg.png) 0 10px; }
        .input_div input { position:absolute; top:26px; }
        .heightlight { background-position:0 -71px; }
        .input_bg_text {position:absolute; line-height:28px; top:26px; color:#A2AFC2; padding-left:13px; font-size: 22px; white-space:nowrap; }
        .submit_bg { background:url(static/images/login/login_submit.png); width:80px; height:42px; position:absolute; overflow:hidden; margin-top:34px; }
        .submit_text { position:absolute; text-shadow:0 2px 1px rgba(255, 255, 255, 0.4); color:#4F3400; font-weight:bold; width:80px; height:42px; text-align:center; line-height:40px; cursor:pointer; -moz-user-select:none; user-select:none; selcte:none; font-size:20px; }
        .submit_bg_hover { background-position:0 -80px; }
    </style>
    <script type="text/javascript">
    var userAgent = navigator.userAgent.toLowerCase();
    var is_opera = navigator.appName.indexOf('Opera') >= 0 ? true : false;
    var is_ie = navigator.appName == "Microsoft Internet Explorer" ? true : false;
    var ie = (userAgent.indexOf('msie') != -1 && !is_opera) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);

    function heightlight(obj) {
    	obj.className = 'input_div heightlight';
    	if (is_ie && ie < 7) {
    		var ie6bg = obj.getElementsByTagName('*')[0].getElementsByTagName('*')[0];
    		ie6bg.style.top = '-71px';
    	}
    }
    function offlight(obj) {
    	obj.className = 'input_div';
    	if (is_ie && ie < 7) {
    		var ie6bg = obj.getElementsByTagName('*')[0].getElementsByTagName('*')[0];
    		ie6bg.style.top = '10px';
    	}
    }
    function show_hover() {
    	var obj = document.getElementById('submit_bg');
    	obj.className = 'submit_bg submit_bg_hover';
    	if (is_ie && ie < 7) {
    		var ie6bg = obj.getElementsByTagName('*')[0].getElementsByTagName('*')[0];
    		ie6bg.style.marginTop = '-80px';
    	}
    }
    function show_up() {
    	var obj = document.getElementById('submit_bg');
    	obj.className = 'submit_bg';
    	if (is_ie && ie < 7) {
    		var ie6bg = obj.getElementsByTagName('*')[0].getElementsByTagName('*')[0];
    		ie6bg.style.marginTop = '';
    	}
    }
    function input_key_down(e) {
    	e = e || window.event;
    	if (e.keyCode == 13) {
    		show_hover();
    	}
    }
    function input_key_up(e, obj, type) {
    	var tobj = document.getElementById('input_default_text_' + type);
    	if (obj.value == '') {
    		var v = {
    		'username' : '用户名',
    		'password' : '密码'
    		};
    		tobj.innerHTML = v[type];
    	} else {
    		tobj.innerHTML = '';
    	}
    	show_up();
    }
    function check_submit(form) {
    	if (form.username.value == '') {
    		show_up();
    		form.username.select();
    		return false;
    	}
    	if (form.password.value == '') {
    		form.password.select();
    		show_up();
    		return false;
    	}
    	return true;
    }
    </script>
</head>
<body scroll="no">
<div id="body">
  <table border="0" align="center" height="100%" width="500">
    <tr>
      <td></td>
    </tr>
    <tr>
      <td height="200" align="center">
        <div style="text-align:left;width:400px;height:190px;margin-left:50px;background:url(static/images/login/admin.png) no-repeat left top;_background:none;_filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='static/images/login/admin.png');">
          <div style="position:absolute;margin:31px 0 0 231px;">
            <table border="0" cellpadding="0" cellspacing="0" width="30" height="45">
              <tr>
                <td align="left" valign="bottom"><img id="image1" style="width:38px;height:44px;" src="static/images/login/admin_p2.png" /></td>
              </tr>
            </table>
          </div>
          <div style="position:absolute;margin:28px 0 0 232px;">
            <table style="width:60px;height:50px;" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td align="left" valign="bottom"><img id="image2" style="width:60px;height:48px;" src="static/images/login/admin_p1.png" /></td>
              </tr>
            </table>
          </div>
          <div style="position:absolute;width:400px;height:190px;background:url(static/images/login/spacer.gif);"></div>
        </div>
        <script type="text/javascript">
        (function(){
        	var obj1 = document.getElementById('image1');
        	var obj2 = document.getElementById('image2');
        	if (is_ie && ie<7){
        		// for ie6
        		var src1 = obj1.src;
        		var src2 = obj2.src;
        		obj1.src = obj2.src = 'static/images/login/spacer.gif';
        		obj1.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src='"+src1+"')";
        		obj2.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src='"+src2+"')";
        	}
        	var n = 0,t = 1;
        	var run = function(){
        		obj1.style.width  = ( obj1.width  + (t*2) ) + 'px';
        		obj2.style.width  = ( obj2.width  - (t*2) ) + 'px';
        		obj2.style.height = ( obj2.height - t     ) + 'px';
        		n += t;
        		if(n==6 || n==0){
        			t = -t;
        		}
        	}
        	setInterval(run, 80);
        })();
        </script>
      </td>
    </tr>
    <tr>
      <td height="200" valign="top"><div id="login_form">
          <form method="post" id="myform" onSubmit="return check_submit(this);" action="login.php?act=login_check">
            <input type="hidden" name="forward" value="" />
            <div style="position:absolute;margin-left:-60px;">
              <h1>管理员登录</h1>
            </div>
            <div class="input_div" style="margin-left:-60px;">
              <!--[if lt IE 6.9]><div class="input_bg_forie6"><div class="inputbg"></div></div><![endif]-->
              <div style="padding:28px;">
                <div class="input_bg_text" id="input_default_text_username">用户名</div>
                <input tabindex="1" type="text" size="30" name="wf_uname" id="username" value="" class="input" onKeyDown="input_key_down(event)" onKeyUp="input_key_up(event,this,'username');" onFocus="heightlight(this.parentNode.parentNode)" onBlur="offlight(this.parentNode.parentNode)" />
              </div>
            </div>
            <div class="input_div" style="margin-left:193px;">
              <div class="input_bg_forie6">
                <div class="inputbg"></div>
              </div>
              <div style="padding:28px;">
                <div class="input_bg_text" id="input_default_text_password">密码</div>
                <input tabindex="2" type="password" name="wf_upawd" size="30" id="password" value="" class="input" onKeyDown="input_key_down(event);input_key_up(event,this,'password');" onFocus="heightlight(this.parentNode.parentNode)" onBlur="offlight(this.parentNode.parentNode)"  style="font-family:Tahoma,Simsun,Helvetica,sans-serif;" />
              </div>
            </div>
            <div class="submit_bg" id="submit_bg" style="margin-left:475px;">
              <!--[if lt IE 6.9]><div class="submit_bg_forie6"><div class="inputbg"></div></div><![endif]-->
              <div style="widht:1px;height:1px;overflow:hidden;">
                <input tabindex="3" onFocus="show_hover()" onBlur="show_up();" type="submit" value="_" />
              </div>
              <div class="submit_text" onMouseDown="show_hover()" onMouseUp="show_up()" onClick="var obj=document.getElementById('myform');if (check_submit(obj))obj.submit();">登录</div>
            </div>
            <div style="clear:both;height:90px;"></div>
            <div style="position:absolute;margin-left:-30px;font-size:13px;color:#ff0;display:none;">测试用户: demo &nbsp;密码: 123456</div>
            <input type="hidden" name="wf_uhash" class="login_area_ckstr" id="uhash" value="<?php echo $uhash;?>" maxlength="4" />
          </form>
        </div></td>
    </tr>
    <tr>
      <td></td>
    </tr>
  </table>
</div>

<script type="text/javascript">
var error = '<?php echo $error;?>';
setTimeout(function(){
	input_key_up({'keyCode': 13}, document.getElementById('username'), 'username');
	input_key_up({'keyCode': 13}, document.getElementById('password'), 'password');
	document.getElementById('username').focus();
	

	if (error) alert(error);
}, 50);
</script>
</body>
</html>
<?php $_SESSION['wf_error'] = '';?>