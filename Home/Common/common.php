<?php

//取用户ID
function getUserId(){
	return session('uid');
}

//取用户名
function getUserName($id){
	$user = M('User')->field('name')->getById($id);
	return $user['name'];
}

//根据用户名查询用户id
function getUserIdByName($name){
	$user = M('User')->field('id')->getByName($name);
	return $user['id'];
}

//获取APPid
function getAppId(){
	return session('appId');
}

function setSession($arr){
	foreach ($arr as $key => $value) {
		session($key,$value);
	}
}

//获取登录用户role
function  getRole(){
	return session('role');
}
//获取当前时间并格式化
function getCurrentTime(){
	return date('Y-m-d H:i:s',time());
}

function getUserApp(){
	return array('appId'=>getAppId(),'userId'=>getUserId());
}

/**
 * 获取当前页面完整URL地址
 */
function getUrl() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

?>