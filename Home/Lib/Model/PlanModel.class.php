<?php
class PlanModel extends CommonModel{
	
	const RUNNING = 1;//执行中
	const COMPLATE = 2;//执行完成
	
	/**
	 * 验证定制的私有data在共有data中是否存在
	 */
	public function checkParam($arr){
		$keys = array_keys($arr);
		foreach ($keys as $key){
			$data = D('Data')->getByKey($key);
			if (!$data){
				$this->ret['data'] = $key;
				return $this->ret;
			}
		}
		$this->ret['status'] = true;
		return $this->ret;
	}
	
	/**
	 * 请求参数解析
	 * @param 参数字符串 $str
	 */
	public function parseParam($str){
		$datas = explode('&', $str);
		$temp = array();
		foreach ($datas as $data){
			$arr = explode("=", $data);
			if (count($arr) != 2){
				return false;
			}
			$temp[$arr[0]] = $arr[1];
		}
		return $temp;
	}
	
	
	/**
	 * 根据给定的条件查询t_plan
	 * @param 查询条件 $where
	 */
	public function getPlan($where = '1=1'){
		return $this->where($where)->select();
	}
}