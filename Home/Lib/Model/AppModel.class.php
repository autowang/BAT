<?php
/**
* APP的model类
*/
class AppModel extends Model
{
	protected $_auto = array (
		array('updateTime','getCurrentTime',3,'function'),
	);
	protected $_validate = array(
		array('name','require','app名称不能为空'),
		array('desc','require','描述内容不能为空'),
	);
    //关联配置
    protected $_link = array(
        'Module' => array(
            'mapping_type'=>MANY_TO_MANY,
            'class_name'=>'Module',
            'foreign_key'=>'appId',
            'mapping_name'=>'modules',
        ),
    );
	
	/*
	 * 检查App是否符合规范
	 */
	public function checkApp($condition){
		//TODO
		return $condition;
	}
	
	/*
	 * 检查App是否已经存在
	 */
	public function checkDuplicate($condition){
		return M('App')->where($condition)->find();
	}
	
	/*
	 * 返回所有App
	 */
	public function getAllApps()
	{
		return M('App')->select();
	}
	
	/*
	 * 根据App名称返回AppId
	 */
	public function getIdByAppName($appName)
	{
		$condition['appName'] = $appName;
		$app = M('App')->where($condition)->select();		
		return $app[0]['id'];
	}
	/*
	 * 根据AppId返回App名称
	 */
	public function getAppNameByAppId($appId)
	{
		$condition['id'] = $appId;
		$app = M('App')->where($condition)->select();
		return $app[0]['appName'];
	}
}