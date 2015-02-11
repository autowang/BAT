<?php
/**
* 用户model
*/
class UserModel extends RelationModel
{
	const ADMIN = 1;
	const QA = 2;
	const RD = 3;
	protected $_auto = array (
			array('updateTime','getCurrentTime',3,'function'),
	);
	
	/*
	 * 检查用户是否符合规范
	 */
	public function checkUser($condition){
		//TODO
		return $condition;
	}
	
	/*
	 * 检查用户是否已经存在
	 */
	public function checkDuplicate($condition){
		return M('User')->where($condition)->find();
	}
	//关联配置
	protected $_link = array(
			'App' => array(
					'mapping_type'=>MANY_TO_MANY,
					'class_name'=>'App',
					'foreign_key'=>'userId',
					'mapping_name'=>'apps',
					'relation_foreign_key'=>'appId',
					'relation_table'=>'t_user_app',
			),
	);
	
	/*
	 * 用户角色名称转化对应id
	 */
	public function getRoleIdByRoleName($roleName)
	{
		if($roleName == '管理员')
		{
			return self::ADMIN;
		}
		if($roleName == '测试人员')
		{
			return self::QA;
		}
		if($roleName == '开发人员')
		{
			return self::RD;
		}
	}
	
	/*
	 * 用户角色id转化对应角色名称
	 */
	public function getRoleNameByRoleId($roleId)
	{
		if($roleId == self::ADMIN)
		{
			return '管理员';
		}
		if($roleId == self::QA)
		{
			return '测试人员';
		}
		if($roleId == self::RD)
		{
			return '开发人员';
		}
	}
	
	/*
	 * 根据userName获取userId
	 */
	public function getUserIdByName($username){
		$user = $this->getUser(array('name'=>$username));
		return $user[0]['id'];
	}
	
	/**
	 * 根据条件查询用户
	 * @param 条件 $where
	 * @return mixed
	 */
	public function getUser($where='1=1'){
		return $this->where($where)->select();
	}
}