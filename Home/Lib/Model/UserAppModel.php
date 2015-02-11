<?php
/*
 * Filename:	UserAppModel.php
 * Todo:		description

 * Author:		zhaihuayang
 * Version:		1.0
 * Date:		2015年1月4日下午8:32:47
 */
class UserAppModel extends RelationModel{
	public function add($user){
		$userApp = M('UserApp');
		for($i=0; $i<count($user['apps']); $i++)
		{
			$data['userId'] = $user['id'];
			$data['appId'] = $user['apps'][$i];
			$userApp->add($data);
		}
	}
}
?>