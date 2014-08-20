<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
<title>Baidu API Test</title>
<link rel="shortcut icon" href="http://static.bootcss.com/www/assets/ico/favicon.png">
<link href="__PUBLIC__/css/bootstrap.css" rel="stylesheet">
<link href="__PUBLIC__/css/docs.css" rel="stylesheet">
<link href="__PUBLIC__/css/prettify.css" rel="stylesheet">
<link href="__PUBLIC__/css/common.css" rel="stylesheet">
<style type="text/css">
  body {
    padding-top: 60px;
    padding-bottom: 40px;
  }
</style>

		<link href="__PUBLIC__/css/api.css" rel="stylesheet">
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      </button>
      <a class="brand" href="#">BAT</a>
      <ul class="nav" style="float:right;">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">空间切换<b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="#">New DSP</a></li>
            <li><a href="#">DAN</a></li>
          </ul>
        </li>
      </ul>
      <div class="nav-collapse collapse">
        <ul class="nav">
          <li id="index_page"><a href="<?php echo U('Index/welcome');?>">首页</a></li>
          <li id="api_page"><a href="<?php echo U('Api/alist');?>">API</a></li>
          <li id="case_page"><a href="<?php echo U('Case/clist');?>">测试用例</a></li>
          <li id="plan_page"><a href="<?php echo U('Plan/plist');?>">测试计划</a></li>
          <!--
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">项目设置<b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="#">业务模块</a></li>
              <li class="divider"></li>
              <li><a href="#">预留</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">自动化<b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo U('Api/alist');?>">API管理</a></li>
              <li class="divider"></li>
              <li><a href="#">用例管理</a></li>
            </ul>
          </li>
          -->
        </ul>
      </div>
    </div>
  </div>
</div>
		<div class="container-fluid">
			<div class="row-fluid">
			<ul class="breadcrumb well">
				<li><a href="<?php echo U('Index/welcome');?>">首页</a> <span class="divider">/</span></li>
				<li><a href="<?php echo U('Api/alist');?>">API</a><span class="divider">/</span></li>
				<li><a href="<?php echo U('Api/add');?>">新增</a>
			</ul>
				<div class="container">
					<form class="form-horizontal" method="post" action="<?php echo U('Api/doAdd');?>">
						<div class="control-group">
							<label class="control-label" for="inputEmail"><i class="required">*</i>请求信息：</label>
							<div class="controls">
								<textarea name="request" rows="10" class="span8" placeholder="请求的头信息" required></textarea>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="inputPassword"><i class="required">*</i>响应信息：</label>
							<div class="controls">
								<textarea name="reponse" rows="10" class="span8" placeholder="响应的头信息" required></textarea>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="inputPassword"><i class="required">*</i>描述说明：</label>
							<div class="controls">
								<input name="apiDesc" type="text" class="span8" placeholder="对该API的一些描述说明" required/>
							</div>
						</div>
						<div style="text-align:center"><button type="submit" class="btn btn-info" style="text-align:center;">保存</button><div>
					</form>
				</div>
			</div>
		</div>
		<div class="footer">
				<footer>
	    <div class="container">
			<p >&copy;from  2014 <i class="icon-check"></i>使用中有任何问题请联系HI:wangsheng14591</p>
		</div>
	</footer>
<script src="__PUBLIC__/js/jquery.js"></script>
<script src="__PUBLIC__/js/bootstrap.min.js"></script>

 
		</div>
	</body>
</html>
<script src="__PUBLIC__/js/api.js"></script>