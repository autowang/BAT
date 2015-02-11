<?php
/**
 * 登录态检查，权限检查的Action基类
 * 所有需要检查登陆或者权限的action必须继承此类
 */
class CommonAction extends Action{
	
	const NO_LOGIN = 10001;
	
	public function _initialize(){
		if (!session('?uid') || !session('?appId')){
			if ($this->isAjax()){
				$this->ajaxReturn(null,'登录态失效！',self::NO_LOGIN);
			}else{
				$this->redirect('Login/login');
			}
		}
		//TODO 数据权限检查
	}
	
	/**
	 * 检查数据的app信息和当前登录的用户所属app是否一致
	 * @param 模块名 $action
	 * @param 数据id $ids
	 */
	public function checkApp($action,$ids,$jumpUrl){
		$condition['id'] = array('in',strval($ids));
		$ret = M($action)->where($condition)->select();
		foreach ($ret as $data){
			if ($data['appId'] != session('appId')){
				$this->error('您无权操作该条数据，请切换到正确的空间！',$jumpUrl);
			}
		}
	}
	
	//分页对象
	public function getPaging($model,$condition){
		import("ORG.Util.Page");
		$count = M($model)->where($condition)->count();
		return new Page($count,C('PAGING_SIZE'));
	}
	
	/**
	 * merge 查询条件
	 * @param 原生查询条件 $old
	 * @return merge之后的查询条件
	 */
	public function merge($old=null){
		$condition['appId'] = getAppId();
		$condition['status'] = C('VALID');
		if(empty($old)){
			return $condition;
		}else{
			return array_merge($old,$condition);
		}
		
	}
}