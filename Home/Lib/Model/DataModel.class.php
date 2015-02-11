<?php
/**
* 
*/
class DataModel extends CommonModel
{
	protected $_auto = array (
			array('appId','getAppId',1,'function'),
			array('updateTime','getCurrentTime',3,'function'),
		);

	protected $_validate = array(
			array('key','require','KEY不能为空！'),
			array('value','require','VALUE不能为空！'),
			array('desc','require','描述信息不能为空！'),
		);
	
	public function keyExist($data){
		$key = $data['key'];
		$where['appId'] = getAppId();
		$where['key'] = $key;
		$ret = $this->where($where)->select();
		if(empty($ret)){
			return true;
		}
		return false;
	}
	
	public function getData($where){
		return $this->where($where)->find();
	}
	
	
	/**
	 * 判断待删除的测试数据是否有测试用例在引用
	 * @param 测试数据的id $ids
	 * @return boolean
	 */
	public function isUsing($ids){
		$condition['id'] = array('in',$ids);
		//根据删除的id获取对应的key
		$datas = $this->where($condition)->field('key')->select();
		$cids = array();//存放有引用关系的用例id
		//根据key查询step表，和case表
		foreach ($datas as $data){
			$where['content'] = array('like','%${'.$data['key'].'}%');
			$where['type'] = CaseModel::HTTP;
			$steps = D("Step")->where($where)->select();
			//如果返回结果为空，说明没引用，直接返回
			if(empty($steps)){
				break;
			}
			//根据step的返回结果，确定对应的case的状态
			foreach ($steps as $step){
				$ret = D("Case")->isValid($step['caseId']);
				if(!is_null($ret)){
					$cids[] = $step['caseId'];
				}
			}
		}
		return array_unique($cids);
	}
}
