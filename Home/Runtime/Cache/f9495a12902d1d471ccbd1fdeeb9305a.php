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
			<div class="row-fluid well">
				<form class="form-inline search_tool">
					<label>ID：</label><input type="text" id="api_id" placeholder="API--ID">
					<label>PATH：</label><input type="text" id="api_id" placeholder="API的path部分">
					<label>负责人：</label><input type="text" id="api_id" placeholder="API录入的同学">
					<button type="submit" class="btn btn-info">查找</button>
				</form>
			</div>
			<div class="row-fluid toolbar">
				<a href="#"><button type="button" class="btn btn-info">新增</button></a>
				<a href="#"><button type="button" class="btn btn-info">修改</button></a>
				<a href="#"><button type="button" class="btn btn-info">删除</button></a>
			</div>
			<div class="row-fluid">
				<table id="api_list" class="table table-hover table-condensed table-bordered table-striped" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th width="2%" style="text-align:center;"><input id="selectAll" type="checkbox" /></th>
							<th width="5%" >ID</th>
							<th width="30%">请求</th>
							<th width="30%">响应</th>
							<th width="28%">描述</th>
							<th width="5%">责任人</th>
						</tr>
					</thead>
					<tbody>
						<?php $__FOR_START_27888__=1;$__FOR_END_27888__=16;for($i=$__FOR_START_27888__;$i < $__FOR_END_27888__;$i+=1){ ?><tr>
								<td style="text-align:center;">
									<input class="selectCell" name="apis[]" type="checkbox" value="<?php echo ($i); ?>"/>
								</td>
								<td><?php echo ($i); ?></td>
								<td>System Architect</td>
								<td>System Architect</td>
								<td>Edinburgh</td>
								<td>61</td>
							</tr><?php } ?>
					</tbody>
				</table>
				<span><?php echo W('Paging',$data,true);?></span>
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