<?php
/**
* 登录类
*/
class LoginAction extends Action
{

	/**
	 * 登录页面
	 */
	public function login(){
		$uid = session('uid');
		$username = session('username');
		$appId = session('appId');
		//如果已经登录过，这直接跳转
		if(!empty($uid) && !empty($username) && !empty($appId)){
			$this->redirect('Index/index');
		}
		$this->display();
	}

	
	/**
	 *	执行登录
	 */
	public function doLogin(){
		$condition['name'] = $username = trim($_POST['username']);
		$condition['password'] = $password = md5(trim($_POST['password']));
		//稍后在加验证码验证逻辑
		//$imgCode = $_POST['imgCode'];
		if (empty($username) || empty($password)) {
			$this->ajaxReturn(null,C("ERR_MSG_70"),"success:false");
		}
		$user = D("User")->relation(true)->where($condition)->find();
		if(empty($user)){
			$this->ajaxReturn(null,"用户名或者密码错误","success:false");
		}
		if(empty($user['apps']) && $user['role'] != UserModel::ADMIN){
			$this->ajaxReturn(null,'该用户不属于任何一个APP，不允许登录,请联系管理员！','success:false');
		}
		if (empty($user)) {
			$this->ajaxReturn(null,C("ERR_MSG_70"),"success:false");
		}
		$defaultAppId = $user['defaultApp'] >= 0 ? $user['defaultApp'] :  $user['apps'][0]['id'];
		
		foreach ($user['apps'] as $app){
			if ($app['id'] == $defaultAppId){
				$appName = $app['appName'];
				break;
			}
		}
		$session = array('uid'=>$user['id'],'username'=>$username,'role'=> $user['role'],'appId' => $defaultAppId,'appName'=>$appName);
		setSession($session);
		$this->ajaxReturn($data,"恭喜，登录成功！","success:true");

	}

	/**
	 * 退出登录
	 */
	public function loginOut(){
		session(null);
		$this->redirect("Login/login");
	}


}