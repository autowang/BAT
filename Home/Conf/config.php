<?php
require_once("constant.php");
require_once("error.php");

$config = array(

	//'SHOW_PAGE_TRACE' =>true, //开启页面Trace
	
	'LAYOUT_ON' => 'true',//开启模版布局

	'URL_CASE_INSENSITIVE' =>true,//URL不区分大小写

	'DB_PREFIX' => 't_', // 数据库表前缀

	'SESSION_OPTIONS'=>array(
    	'type'=> 'db',//session采用数据库保存
    	'expire'=>1800,//session过期时间
  	),

	'SESSION_TABLE'=>'t_session', //session表
	
	'SESSION_AUTO_START' => true,//开启session支持

	
	'DB_TYPE'  => 'mysql', // 数据库类型
	'DB_HOST'  => '127.0.0.1:8123', // 服务器地址
	'DB_NAME'  => 'bat', // 数据库名
	'DB_USER'  => 'root', // 用户名
	'DB_PWD'  => '123456', 
	'TMPL_ACTION_ERROR' => 'Public:error', //重新定义错误跳转页面
	
	'JMETER' => BASE_PATH."/Agent/bin/jmeter",

);

return array_merge($config,$errMsg,$constant);

?>