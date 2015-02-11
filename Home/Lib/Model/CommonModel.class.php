<?php
/**
* 通用的model父类
*/
class CommonModel extends Model
{
	
	public $ret = array(
			'data'=>null, //数据
			'msg'=>"",//提示信息
			'status'=>false//状态
	);
}