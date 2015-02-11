<?php
/**
 * 用户类
 * @author wangsheng03
 *
 */
class UserAction extends CommonAction{
	/**
	 * 用户列表
	 */
	public function ulist(){
		//传入参数判断
		$id = $this->_get('id');
	
		if (!empty($id)){
			$condition['id'] = trim($id);
		}else{	
			$name = $this->_get('name');
			if (!empty($name)){
				$condition['name'] = array('LIKE','%'.trim($name).'%');
			}
			$phone = $this->_get('phone');
			if (!empty($phone)){
				$condition['phone'] = array('LIKE','%'.trim($phone).'%');
			}
		}
		$condition = $this->merge($condition);
		//分页
		$page = $this->getPaging('User', $condition);
		$paging = $page->show();
		$this->assign('paging',$paging);
	
		//根据查询条件获取数据
		$users = D('User')->relation(true)->where($condition)->order('id asc')->limit($page->firstRow.','.$page->listRows)->select();
		foreach ($users as $user){
			$apps = ''; 
			foreach ($user['apps'] as $app)
			{
				$apps .= $app['appName'] . " ";
			}
			$user['appNames'] = $apps;
			$data[] = $user;
		}
		$this->assign('users',$data);
		$this->display();
	}
	
	/**
	 * 新增用户
	 */
	public function add()
	{
		$apps = D('App')->getAllApps();
		$this->assign('apps',$apps);
		$this->display();
	}
	
	/**
	 * 执行新增用户操作
	 */
	public function doAdd()
	{
		$user = D('User')->create();
			
		if(!$user){
			$this->ajaxReturn(null,D('User')->getError(),'success:false');
		}
		
		//判断传入参数是否符合格式
		$user = D('User')->checkUser($user);
		//判断user名称是否重复
		$condition['name'] = $user['name'];
		$temp = D('User')->checkDuplicate($condition);
		if(!empty($temp)) {
			$this->ajaxReturn(0,"已经有相同的user存在，不允许重复录入","success:false");
		}
		/*用户插入*/
		$user['password'] = md5(123456);
		$user['role'] = D('User')->getRoleIdByRoleName($user['role']);
		$user['defaultApp'] = D('App')->getIdByAppName($user['defaultApp']);
		$ret = D('User')->add($user);
		
		/*关联表插入*/
		$user['addedApps'] = $_POST['addedApps'];
		$apps = explode(' ',$user['addedApps']);
		$user['apps'] = array();
		for($i=0; $i<count($apps)-1; $i++) {
			$user['apps'][$i] = D('App')->getIdByAppName($apps[$i]);
		}
		$userId = D('User')->getUserIdByName($user['name']);
		$user['id'] = $userId;
		$userApp = M('UserApp');
		for($i=0; $i<count($user['apps']); $i++)
		{
			$data['userId'] = $user['id'];
			$data['appId'] = $user['apps'][$i];
			$userApp->add($data);
		}
		if(!in_array($user['defaultApp'], $user['apps'])){
			$data['userId'] = $user['id'];
			$data['appId'] = $user['defaultApp'];
			$userApp->add($data);
		}
		if ($ret) {
			$this->ajaxReturn($ret,"成功新增User数据！","success:true");
		} else {
			$this->ajaxReturn(0,"新增User数据失败！","success:false");
		} 
	}
	
	/**
	 * 删除user
	 */
	public function delete(){
		$ids = $this->_post('ids');
		if (empty($ids)) {
			$this->ajaxReturn(0,"请选择待删除的User。","success:false");
		}
		$condition1["id"] = array("in", $ids);
		$ret1 = D('User')->where($condition1)->delete();
		$condition2["userId"] = array("in", $ids);
		$ret2 = D('UserApp')->where($condition2)->delete();
		if (empty($ret1) || empty($ret2)) {
			$this->ajaxReturn($ret1,"删除User失败！","success:false");
		}
		$this->ajaxReturn($ret1,"成功删除User。","success:true");
	}
	
	/**
	 * 修改user
	 */
	public function update(){
		$id = $this->_get('id');
		$apps = D('App')->getAllApps();
		$this->assign("allApps",$apps);
		//根据id查询app信息
		$ret = D('User')->relation(true)->getById($id);
		if (empty($ret)) {
			$this->error("ID=".$id."对应的User不存在！！！","ulist",2);
		}
		$ret['defaultApp'] = D('App')->getAppNameByAppId($ret['defaultApp']);		
		$appNames = array();
		foreach ($ret['apps'] as $app){
			array_push($appNames, $app['appName']);
		}
		unset($ret['apps']);
		$ret['apps'] = $appNames;
		$this->assign("user",$ret);	
		$this->display();
	}
	/**
	 * user更新操作
	 */
	public function doUpdate(){
		$user = D('User')->create();
		if(!$user){
			$this->ajaxReturn(null,D('User')->getError(),'success:false');
		}
	
		//判断传入参数是否符合格式
		$app = D('User')->checkUser($user);
	
		//判断user名称是否重复
		$condition['id'] = array(neq,$user['id']);
		$condition['name'] = $user['name'];
		$temp = D('User')->checkDuplicate($condition);
		if(!empty($temp)) {
			$this->ajaxReturn(0,"已经有相同的user存在，不允许重复录入","success:false");
		}
		
		//执行User表更新
	/* 	//用户更改过密码，需要重新md5加密，否则不用加密
		if($_POST['oldpwd'] != $user['password']){
			$user['password'] = md5($user['password']);
		} */		
		$user['role'] = D('User')->getRoleIdByRoleName($user['role']);
		$user['defaultApp'] = D('App')->getIdByAppName($user['defaultApp']);
		$ret = D('User')->save($user);
	/*   var_dump($user);
		exit(); */
		
		//执行关联表表更新
		$user['addedApps'] = $_POST['addedApps'];
		$apps = explode(' ',$user['addedApps']);
		$user['apps'] = array();
		for($i=0; $i<count($apps)-1; $i++) {
			$user['apps'][$i] = D('App')->getIdByAppName($apps[$i]);
		}
		$userId = D('User')->getUserIdByName($user['name']);
		$user['id'] = $userId;
		$userApp = M('UserApp');
		$userApp->where("userId='{$userId}'")->delete();
		for($i=0; $i<count($user['apps']); $i++)
		{
			$data['userId'] = $user['id'];
			$data['appId'] = $user['apps'][$i];
			$userApp->add($data);
		}
		if(!in_array($user['defaultApp'], $user['apps'])){
			$data['userId'] = $user['id'];
			$data['appId'] = $user['defaultApp'];
			$userApp->add($data);
		}
		if ($ret === false) {
			$this->ajaxReturn($ret,"更新User数据失败！","success:false");
		}
		$this->ajaxReturn(0,"成功更新User数据！","success:true");
	
	}
	
	/**
	 * 更新个人资料
	 */
	public function updateInfo(){
		$id = session('uid');
		$ret = D('User')->getById($id);
	 	$roleName = D('User')->getRoleNameByRoleId($ret['role']);
		$ret['roleName'] = $roleName; 
		$this->assign("user",$ret);
		$this->display();
	}
	
	/**
	 * 执行个人资料更新
	 */
	public function doUpdateInfo(){
		$user = D('User')->create();
		
		if(!$user){
			$this->ajaxReturn(null,D('User')->getError(),'success:false');
		}
		
		//判断传入参数是否符合格式
		$app = D('User')->checkUser($user);
		
		//判断user名称是否重复
		$condition['id'] = array(neq,$user['id']);
		$condition['name'] = $user['name'];
		$temp = D('User')->checkDuplicate($condition);
		if(!empty($temp)) {
			$this->ajaxReturn(0,"已经有相同的user存在，不允许重复录入","success:false");
		}
		 $ret = D('User')->save($user);
		  /* var_dump($user); 
		 exit(); */
		 if ($ret === false) {
		 	$this->ajaxReturn($ret,"更新User数据失败！","success:false");
		 }
		 $this->ajaxReturn(0,"成功更新User数据！","success:true");
	}
	
	/**
	 * 密码修改
	 */
	public function updatePwd(){
		$id = session('uid');
		$ret = D('User')->getById($id);
		$this->assign("user",$ret);
		$this->display();
	}
	
	/**
	 * 执行密码修改
	 */
	public function doUpdatePwd(){
		$user = D('User')->create();
		if(!$user){
			$this->ajaxReturn(null,D('User')->getError(),'success:false');
		}
		//判断用户原始密码是否输入正确，正确则更新密码，否则返回提示信息
		$id = session('uid');
		if(md5($_POST['oldpwd']) != D('User')->getById($id)['password']){
			$this->ajaxReturn($ret,"原密码输入不正确！","success:false");
		}
		//判断两次新密码输入是否一致
		if($_POST['newpwd'] != $_POST['pwdConfirm']){
			$this->ajaxReturn($ret,"两次密码输入不一致，请重新输入！","success:false");
		}
		$user['password'] = md5($_POST['newpwd']);
		$ret = D('User')->save($user);
		/* var_dump($user);
		 exit(); */
		if ($ret === false) {
			$this->ajaxReturn($ret,"更新User数据失败！","success:false");
		}
		$this->ajaxReturn(0,"成功更新User数据！","success:true");
	} 
}









































