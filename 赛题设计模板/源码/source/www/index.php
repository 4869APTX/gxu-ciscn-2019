<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2015 OSDU.Net
// +----------------------------------------------------------------------
// | Author: 狂飙的小蜗牛
// | E-mail: 858908467@qq.com
// | QQ:     858908467
// +----------------------------------------------------------------------

require 'config.php';
if (!WF_Auth::isLogin()) {
	wf_redirect('login.php?act=in');
	exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8" />
	<title>WebFTP <?php echo $_CONFIG['SYSTEM_VERSION']; ?></title>

	<link rel="stylesheet" href="static/css/style.css" />
	<link rel="stylesheet" href="static/css/toolbar.css" />
	<link rel="shortcut icon" href="favicon.ico" />

	<script type="text/javascript">
		window.nfs   = {
			admin: <?php echo WF_Auth::isAdmin() ? 'true' : 'false';?>,
			host: '<?php echo wf_gpc('wf_uhost', 's');?>',
			path: '<?php echo wf_gpc('wf_upath', 's');?>'
		};
	</script>
</head>
<body id="body">
<div id="loading">正在加载...</div>

<div id="header">
	<h1 id="logo">
		<a target="_blank" href="http://www.osdu.net/?webftp">WebFTP</a>
		<i class="cline"></i>
		<span>WebFTP</span>
	</h1>
</div>

<div id="main">
	<div class="top"></div>

	<div class="col-sub">
		<ul id="main-menu">
		  	<?php if(WF_Auth::isAdmin() && is_dir('./admin')){?>
		  		<li id="help8">
		  			<span>
		  				<i class="icon"></i>
		  				<a target="_blank" href="./admin/">管理中心</a>
		  			</span>
		  		</li>
		  	<?php }?>
		  	<li id="help1">
		  		<span>
		  			<i class="icon"></i>
		  			<a href="javascript:void(0);" onclick="app.nfs.pathinfo();">目录详情</a>
		  		</span>
		  	</li>
			<li id="help4">
				<span>
					<i class="icon"></i>
					<a href="javascript:void(0);" onclick="app.mui.thumb = !app.mui.thumb;" rel="imageStyle">图片预览</a>
				</span>
			</li>
		  	<li id="help6">
		  		<span>
		  			<i class="icon"></i>
		  			<a href="javascript:void(0);" onclick="app.mui.resetpass();">修改密码</a>
		  		</span>
		  	</li>
		  	<li id="help7">
		  		<span>
		  			<i class="icon"></i>
		  			<a href="javascript:void(0);" onclick="app.mui.logout();">安全退出</a>
		  		</span>
		  	</li>
		</ul>
	</div>

	<div class="col-main">
		<div id="list">
			<div id="list_head">
				<span id="list_head_left"></span>
				<span id="list_head_center"></span>
				<span id="list_head_right"></span>
				<div class="clean"></div>
			</div>
			<div id="list_main">
				<span id="list_main_left"></span>
				<div id="list_main_center">
					<table class="tree-browser" cellpadding="0" cellspacing="0" >
						<tbody id="dirs-files-list">
							<th><font color="green">文件列表加载中...</font></th>
						</tbody>
					</table>
				</div>
				<span id="list_main_right"></span>
				<div class="clean"></div>
			</div>

		  	<div id="list_foot">
		  		<span id="list_foot_left"></span>
		  		<span id="list_foot_center"></span>
		  		<span id="list_foot_right"></span>
		  		<div class="clean"></div>
			</div>
		</div>
	</div>

  	<div class="bottom"></div>
</div>

<div id="footer">
	<a href="admin/about.html" target="_blank">关于程序</a> -
	<a href="admin/license.html" target="_blank">服务条款</a> -
	<a href="admin/help.html"  target="_blank">使用帮助</a> -
	<a href="admin/phpinfo.php" target="_blank">系统环境</a> -
	<a href="javascript:alert('请联系QQ：858908467');">加入我们</a> -
	<a href="mailto:858908467@qq.com" target="_self">意见反馈</a><br/>
	CopyRight ©2011-2015 <a href="http://www.osdu.net/?webftp" target="_blank">OSDU.Net</a> All Rights Reserved.
</div>


<div id="contextDirMenu"></div>
<div id="contextFileMenu"></div>

<script id="tools-bar" type="text/template">
  <div class="apptools">
	<div class="clearfix apptools-inner">
	  <a id="toolSelect" class="btn" href="javascript:app.mui.SelectAll();" title="选择"><span><img src="static/images/toolbar/select.gif" width="16" height="16" />选择</span></a> <span class="edge">|</span>
	  <a id="toolback" class="btn" href="javascript:void(0);" onclick="app.nfs.nlist(app.nfs.cpath, true);"><span><img src="static/images/toolbar/folder_up.gif" width="16" height="16" />刷新</span></a> <span class="edge">|</span>
	  <a id="toolNewDir" class="btn" href="javascript:void(0);" onclick="app.nfs.mkdir();"><span><img src="static/images/toolbar/folder_add.gif" width="16" height="16" />新建目录</span></a><span class="edge">|</span>
	  <a id="toolNewDir" class="btn" href="javascript:void(0);" onclick="app.nfs.mkdir('file');"><span><img src="static/images/toolbar/file_add.gif" width="16" height="16" />新建文件</span></a><span class="edge">|</span>
	  <a id="toolCut" class="btn" href="javascript:void(0);" onclick="app.nfs.cut(false);"><span><img src="static/images/toolbar/cut.gif" width="16" height="16" />剪切</span></a><span class="edge">|</span>
	  <a id="toolCopy" class="btn" href="javascript:void(0);" onclick="app.nfs.copy(false);"><span><img src="static/images/toolbar/share.gif" width="16" height="16" />复制</span></a><span class="edge">|</span>
	  <a id="toolPaste" class="btn" href="javascript:void(0);" onclick="app.nfs.paste(false);"><span><img src="static/images/toolbar/paste.gif" width="16" height="16" />粘贴</span></a><span class="edge">|</span>
	  <a id="toolUploadFile" class="btn" href="javascript:void(0);" onclick="app.nfs.upload();"><span><img src="static/images/toolbar/file_up.gif" width="16" height="16" />上传</span></a> <span class="edge">|</span>
	  <a id="toolDelete" class="btn" href="javascript:void(0);" onclick="app.nfs.rmdir(false);"><span><img src="static/images/toolbar/file_del.gif" width="16" height="16" />删除</span></a><span class="edge">|</span>
	  <a id="toolListView" class="btn" href="javascript:void(0);" onclick="app.mui.olist=!app.mui.olist;app.nfs.nlist(app.nfs.cpath, false);" title="切换视图"><span><img src="static/images/toolbar/view_thumb.gif" width="16" height="16" />视图</span></a><span class="edge">|</span>

	  <div class="dropdowndock"> <a id="tool_sort" class="btn btn-dropdown" href="javascript:void(0);" title="列表排列方式"><span><img src="static/images/toolbar/order_asc.gif" width="16" height="16" />排序</span></a>
		<div class="dropdownmenu-wrap" id="drop_sort" style="display:none;">
		  <div class="dropdownmenu">
			<ul class="dropdownmenu-list">
			  <li><a  href="javascript:void(0);" id="list_order_type_name" class="list_order_type" data-type="name">文件名称</a></li>
			  <li><a  href="javascript:void(0);" id="list_order_type_size" class="list_order_type" data-type="size">文件大小</a></li>
			  <li><a  href="javascript:void(0);" id="list_order_type_ext" class="list_order_type" data-type="ext">文件类型</a></li>
			  <li><a  href="javascript:void(0);" id="list_order_type_mtime" class="list_order_type" data-type="mtime">修改时间</a></li>
			  <li class="dropmenu-split">-</li>
			  <li><a  href="javascript:void(0);" id="list_order_sort_asc"  class="list_order_sort" data-sort="asc" >顺序排列</a></li>
			  <li><a  href="javascript:void(0);" id="list_order_sort_desc" class="list_order_sort" data-sort="desc">倒序排列</a></li>
			</ul>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</script>

<!--路径列表 - 模板 -->
<script id="nlist-path" type="text/template">
	<span id="list_head_left"></span>
	<span id="list_head_center">当前目录：{@each paths as dir}<a href="javascript:app.nfs.nlist('${dir.path}');">${dir.name}/</a>{@/each}</span>
	<span id="list_head_right"></span>
</script>

<!--目录列表 - 模板 -->
<script id="nlist-list-table" type="text/template">
	{@include "#tools-bar", subData}
	<table class="tree-browser" cellpadding="0" cellspacing="0">
		<tbody id="dirs-files-list">
			<th><font color="green">文件列表加载中...</font></th>
		</tbody>
   </table>
</script>

<script id="nlist-list-main" type="text/template">
	{# 表格头部 }
	<tr class="nlist-list">
		<td><input class="dir-disabled" name="dir-disabled" type="checkbox" value="" disabled /></td>
		<td><span class="ext ext_folder_go"></span></td>
		<td><a href="javascript:void(0);" onclick="app.nfs.nlist('${path.parent}');" class="js-slide-to">返回上级目录</a></td>
		<td>修改时间</td>
		<td>文件大小</td>
		<td>文件权限</td>
		<td align="center" colspan="3">相关操作</td>
	</tr>

	{# 目录列表 }
	{@each list.dirs as dir,idx}
		<tr class="dir-list-${idx}">
			<td><input class="dir-checkbox-id" name="dir-checkbox" type="checkbox" value="${dir.path}" /></td>
			<td><span class="ext ext_folder_open"></span></td>
			<td><a href="javascript:void(0);" onclick="app.nfs.nlist('${dir.path}')" id="dir-id-${idx}" data-path="${dir.path}" data-name="${dir.name}" data-mtime="${dir.mtime}" data-chmod="${dir.chmod}">${dir.name}</a></td>
			<td>${dir.fmtime}</td> <td>no size</td> <td title="${dir.fchmod}">${dir.chmod}</td>

			<td><a href="javascript:app.nfs.zip('${dir.path}','${dir.name}')">打包</a></td>
			<td><a href="javascript:app.nfs.download('${dir.path}','${dir.name}','dir')">下载</a></td>
			<td><a href="javascript:app.nfs.rmdir('${dir.path}','${dir.name}', 'dir')">删除</a></td>
		</tr>
	{@/each}

	{# 文件列表 }
	{@each list.files as file,idx}
		<tr class="file-list-${idx}">
			<td><input class="file-checkbox-id" name="file-checkbox" type="checkbox" value="${file.path}" /></td>
			<td><span class="ext ext_${file.ext}"></span></td>
			{@if file.ext=='jpg' || file.ext=='png' || file.ext=='gif' || file.ext=='bmp'}
				<td><a href="${nfs.host}${nfs.path}${file.path}" onclick="return false;" id="file-id-${idx}" data-path="${file.path}" data-name="${file.name}" data-ext="${file.ext}" data-mtime="${file.mtime}" data-chmod="${file.chmod}" title="双击预览图片" rel="show" colortitle="文件名称：<font color=red>${file.name}&nbsp;&nbsp;&nbsp;&nbsp;</font>图片大小: <font color=red>${file.fsize}</font>">${file.name}</a></td>
			{@else}
				<td><a href="${nfs.host}${nfs.path}${file.path}" id="file-id-${idx}" data-path="${file.path}" data-name="${file.name}" data-ext="${file.ext}" title="${file.name}" data-mtime="${file.mtime}" data-chmod="${file.chmod}" target="_blank" >${file.name}</a></td>
			{@/if}
			<td>${file.fmtime}</td> <td>${file.fsize}</td>
			<td title="${file.fchmod}">${file.chmod}</td>

			<td><a href="javascript:app.nfs.download('${file.path}', '${file.name}', 'file')">下载</a></td>
			<td><a href="javascript:app.nfs.rmdir('${file.path}', '${file.name}', 'file')">删除</a></td>
			<td>--</td>
		</tr>
	{@/each}
</script>

<!--目录列表 - 模板 -->
<script id="nlist-icon" type="text/template">
	{@include "#tools-bar", subData}
	<table id="view-dirs-files-list">
		<tr>
			<td class="rhumbnail">
				{# 表格头部 }
				<div>
					<ol class="f_icon rounded"><a href="javascript:void(0);" onclick="app.nfs.nlist('${path.parent}')"><div class="ext_big ext_big_upto"></div></a></ol>
					<ol class="f_name"><font color="green">返回上级目录</font></ol>
				</div>

				{# 目录列表 }
				{@each list.dirs as dir,idx}
					<div style="position:relative;left:0px;top:0px;">
						<ol class="f_icon rounded">
							<a href="javascript:void(0);" onclick="app.nfs.nlist('${dir.path}')" id="dir-id-${idx}" data-path="${dir.path}" data-name="${dir.name}" data-mtime="${dir.mtime}" data-chmod="${dir.chmod}">
								<div class="ext_big ext_big_dir"></div>
							</a>
						</ol>
						<ol class="f_name">
							<font color="blue">${dir.name}</font>
						</ol>
						<span style="position:absolute;left:10px;top:85px;">
							<input class="dir-checkbox-id" name="dir-checkbox" type="checkbox" value="${dir.path}" />
						</span>
					</div>
				{@/each}

				{# 文件列表 }
				{@each list.files as file,idx}
					<div style="position:relative;left:0px;top:0px;">
						{@if file.ext=='jpg' || file.ext=='png' || file.ext=='gif' || file.ext=='bmp'}
							<ol class="f_icon rounded">
							  <a href="${nfs.host}${nfs.path}${file.path}" id="file-id-${idx}" title="双击预览图片" rel="show" onclick="return false;" data-path="${file.path}" data-name="${file.name}" data-chmod="${file.chmod}"  data-ext="${file.ext}" data-mtime="${file.mtime}" colortitle="文件名称：<font color=red>${file.name}&nbsp;&nbsp;&nbsp;&nbsp;</font>图片大小: <font color=red>${file.fsize}</font>">
							    <div class="ext_big ext_big_${file.ext}"></div>
							  </a>
							</ol>
						{@else}
							<ol class="f_icon rounded">
								<a href="${nfs.host}${nfs.path}${file.path}" id="file-id-${idx}"  title="${file.name}" target="_blank" data-path="${file.path}" data-name="${file.name}" data-chmod="${file.chmod}" data-ext="${file.ext}">
									<div class="ext_big ext_big_${file.ext}"></div>
								</a>
							</ol>
						{@/if}
						<ol class="f_name">
							<font color="red">${file.name}</font>
						</ol>
						<span style="position:absolute;left:10px;top:85px;">
							<input class="file-checkbox-id" name="file-checkbox" type="checkbox" value="${file.path}" />
						</span>
					</div>
				{@/each}
			</td>
		</tr>
	</table>
</script>

<script id="app-nfs-rename" type="text/template">
	{@if code == 200}
		<font color="green">命名成功：</font><font color="red">${name}</font><br />
		<font color="green">执行耗时：</font><font color="red">${time} 秒</font><br />
	{@else}
		<font color="blue">重命名失败：</font><font color="red">${message}</font><br />
	{@/if}
</script>

<script id="app-nfs-pathinfo" type="text/template">
	{@if code == 200}
		<font color="green">当前目录：</font><font color="red">${data.path}</font><br />
		<font color="green">目录详情：</font><font color="red">包含 ${data.dnums} 个文件夹，${data.fnums} 个文件，共计 ${data.fsize}</font><br />
		<font color="green">目录权限：</font><font color="red">${data.chmod} - [${data.fchmod}]</font><br />
		<font color="green">执行耗时：</font><font color="red">${time} 秒</font><br />
	 {@else}
		<font color="blue">获取属性失败：</font><font color="red">${message}</font><br />
	 {@/if}
</script>

<script id="app-nfs-del" type="text/template">
	{@if code == 200}
		<font color="green">成功删除：</font><font color="red">${data.ds} 文件夹，${data.fs} 个文件，共计 ${data.si}</font><br />
		{@each data.el as item}
			<font color="blue">删除失败：</font><font color="red">${item}</font><br />
		{@/each}
	{@else}
		<font color="blue">删除失败：</font><font color="red">${message}</font><br />
	{@/if}
	<font color="green">执行耗时：</font><font color="red">${time} 秒</font><br />
</script>

<script id="app-nfs-zip" type="text/template">
	{@if code == 200}
		<font color="green">目标文件：</font><font color="red">${data.name}</font><br />
		<font color="green">文件详情：</font><font color="red">包含 ${data.dn} 个文件夹，${data.fn} 个文件</font><br />
		<font color="green">文档大小：</font><font color="red">共计 ${data.si}，压缩后 ${data.sc}</font><br />
		<font color="green">执行耗时：</font><font color="red">${time} 秒</font><br />
	{@else}
		<font color="blue">压缩失败：</font><font color="red">${message}</font><br />
		<font color="green">压缩耗时：</font><font color="red">${time} 秒</font><br />
	{@/if}
</script>

<script id="app-nfs-unzip" type="text/template">
	{@if code == 200}
		<font color="green">目标文件：</font><font color="red">${data.name}</font><br />
		<font color="green">文件详情：</font><font color="red">包含 ${data.dn} 个文件夹，${data.fn} 个文件</font><br />
		<font color="green">文档大小：</font><font color="red">共计 ${data.sc}，解压后 ${data.si}</font><br />
		<font color="green">解压耗时：</font><font color="red">${time} 秒</font><br />
	{@else}
		<font color="blue">解压失败：</font><font color="red">${message}</font><br />
		<font color="green">解压耗时：</font><font color="red">${time} 秒</font><br />
	{@/if}
</script>

<script id="app-nfs-paste" type="text/template">
	{@if data.mode == 'cut'}
		 {@if json.code == 200}
			<font color="green">极速移动：</font><font color="red">${json.data.success} 个成功，${json.data.errors} 个失败</font><br />
			<font color="green">移动耗时：</font><font color="red">${json.time} 秒</font><br />
			{@each json.data.permission as file}
				 <font color="blue">无访问权限：</font><font color="red">${file}</font><br />
			{@/each}
			{@each json.data.exists as file}
				 <font color="blue">文件已存在：</font><font color="red">${file}</font><br />
			{@/each}
		{@else}
			<font color="blue">移动失败：</font><font color="red">${message}</font><br />
			<font color="green">移动耗时：</font><font color="red">${time} 秒</font><br />
		{@/if}
	{@else}
		{@if json.code == 200}
			<font color="green">极速复制：</font><font color="red">${json.data.success} 个成功，${json.data.errors} 个失败</font><br />
			<font color="green">包含耗时：</font><font color="red">${json.data.dnumber} 个文件夹，${json.data.fnumber}文件</font><br />
			<font color="green">复制耗时：</font><font color="red">${json.time} 秒，共复制 ${json.data.size}</font><br />
			{@each json.data.permission as file}
				 <font color="blue">无访问权限：</font><font color="red">${file}</font><br />
			{@/each}
			{@each json.data.exists as file}
				 <font color="blue">文件已存在：</font><font color="red">${file}</font><br />
			{@/each}
		 {@else}
			<font color="blue">复制失败：</font><font color="red">${message}</font><br />
			<font color="green">复制耗时：</font><font color="red">${time} 秒</font><br />
		 {@/if}
	{@/if}
</script>

<script id="app-nfs-chmod" type="text/template">
	<style type="text/css">
		#container { width:420px; margin:0 auto; }
		fieldset { background:#f2f2e6; padding:0px; border:1px solid #fff; border-color:#fff #666661 #666661 #fff; margin-bottom:0px; width:420px; }
		input, textarea, select { font:12px/12px Arial, Helvetica, sans-serif; padding:0; }
		fieldset.action { background:#9da2a6; border-color:#316AC5; margin-top:-20px; }
		legend { background:#bfbf30; color:#fff; font:17px/21px Calibri, Arial, Helvetica, sans-serif; padding:0 10px; margin:0; font-weight:bold; border:1px solid #fff; border-color:#e5e5c3 #505014 #505014 #e5e5c3; }
		label { font-size:11px; font-weight:bold; color:#666; }
		label.opt { font-weight:normal; }
		dl { clear:both; }
		dt { float:left; text-align:right; width:90px; line-height:25px; margin:0 10px 10px 0; }
		dd { float:left; width:300px; line-height:25px; margin:0 0 10px 0; }
	</style>
	<div id="container" style="background-color: #F2F2E6;">
	  <form action="" method="post" class="niceform">
		<fieldset>
		<dl>
		  <dt> <label for="color">包含子目录:</label> </dt>
		  <dd>
			<input type="radio" name="chmod_deep" id="deep_chmod_1" value="1" {@if type=='file'}disabled{@/if} > <label for="deep_1" class="opt">是</label>
			<input type="radio" name="chmod_deep" id="deep_chmod_0" value="0" {@if type=='file'}disabled{@/if} checked="checked"/> <label for="deep_0" class="opt">否</label>
		  </dd>
		</dl>
		<dl>
		  <dt><label for="interests">所有者权限:</label> </dt>
		  <dd>
			<input type="checkbox" onclick="set_chmod_num();" id="chmod_owner_read"  value="400" /> <label for="read" class="opt">读取</label>
			<input type="checkbox" onclick="set_chmod_num();" id="chmod_owner_write" value="200" /> <label for="write" class="opt">写入</label>
			<input type="checkbox" onclick="set_chmod_num();" id="chmod_owner_exec"  value="100" /> <label for="run" class="opt">执行</label>
		  </dd>
		</dl>
		<dl>
		  <dt><label for="interests">同组权限:</label> </dt>
		  <dd>
			<input type="checkbox" onclick="set_chmod_num();" id="chmod_group_read"  value="40" /> <label for="read" class="opt">读取</label>
			<input type="checkbox" onclick="set_chmod_num();" id="chmod_group_write" value="20" /> <label for="write" class="opt">写入</label>
			<input type="checkbox" onclick="set_chmod_num();" id="chmod_group_exec"  value="10" /> <label for="run" class="opt">执行</label>
		  </dd>
		</dl>
		<dl>
		  <dt> <label for="interests">公共权限:</label> </dt>
		  <dd>
			<input type="checkbox" onclick="set_chmod_num();" id="chmod_public_read"  value="4" /> <label for="read" class="opt">读取</label>
			<input type="checkbox" onclick="set_chmod_num();" id="chmod_public_write" value="2" /> <label for="write" class="opt">写入</label>
			<input type="checkbox" onclick="set_chmod_num();" id="chmod_public_exec"  value="1" /> <label for="run" class="opt">执行</label>
		  </dd>
		</dl>
		<dl>
		  <dt><label for="interests">数值化权限:</label></dt>
		  <dd><input type="text" id="num_chmod" size="20" value="${chmod}" readonly /></dd>
		</dl>
		</fieldset>
	  </form>
	</div>
</script>
<script id="app-nfs-chmod-x" type="text/template">
	<font color="green">修改项目：</font><font color="red">${data.name} [${data.chmod}]</font><br />
	<font color="green">总计修改：</font><font color="red">${data.dn} 个目录，${data.fn} 个文件，失败 ${data.en}个</font><br />
	{@each data.el as item}
		<font color="blue">修改失败：</font><font color="red">${item}</font><br />
	{@/each}
	<font color="green">修改耗时：</font><font color="red">${time} 秒</font><br />
</script>

<iframe id="app-nfs-down" src="" frameborder="0" width="0" height="0"></iframe>

<!-- jQuery 库文件、模板引擎-->
<script src="static/js/jquery-1.7.2.min.js"></script>
<script src="static/js/juicer-0.6.5.min.js"></script>

<!-- artDialog 资源文件 -->
<link rel="stylesheet" href="static/plugins/artDialog/skins/default.css?v=4.1.7" />
<script src="static/plugins/artDialog/jquery.artDialog.min.js?v=4.1.7"></script>

<!-- contextMenu 资源文件 -->
<link rel="stylesheet" href="static/plugins/contextMenu/jquery.contextMenu.css?v=1.1.0" />
<script src="static/plugins/contextMenu/jquery.contextMenu.js?v=1.1.0"></script>

<!-- colorBox 资源文件 -->
<link rel="stylesheet" href="static/plugins/colorBox/jquery.colorBox.css?v=1.3.17.2"/>
<script src="static/plugins/colorBox/jquery.colorBox.min.js?v=1.3.17.2"></script>

<!-- ZeroClipboard 资源文件 -->
<script src="static/plugins/ZeroClipboard/ZeroClipboard.min.js?v=1.3.2"></script>

<!-- app 资源文件-->
<script src="static/js/webftp.core.js"></script>
<script src="static/js/webftp.util.js"></script>
<script src="static/js/webftp.nfs.js"></script>
<script src="static/js/webftp.mui.js"></script>
<script type="text/javascript">
$(function () {
	// 配置参数
	// app.debug = true;
	app.api  = {
		url: 'webftp.php',
		key: '0123456789',
		auth:'<?php $auth = wf_gpc('wf_uauth', 's'); echo implode(',', $auth);?>'.split(',')
	};

	// 执行初始化
	app.mui.init();
	app.nfs.init();
});
</script>
</body>
</html>