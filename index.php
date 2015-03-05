<?php
	//指定项目名称
	define('APP_NAME', 'Home');
	//指定项目路径
	define('APP_PATH', './Home/');
	
	define('BASE_PATH', dirname(__FILE__));
	define('UPLOAD_PATH', BASE_PATH.'/Upload/');
	define('JMX_PATH', BASE_PATH.'/Jmx/');
	//开启debug模式
	define('APP_DEBUG', true);
	
	//引入thinkphp核心文件
	require './ThinkPHP/ThinkPHP.php';
	
?>
