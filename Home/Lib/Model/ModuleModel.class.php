<?php
class ModuleModel extends CommonModel{
	
	public function getModule($where="1=1"){
		$where['status'] = C("VALID");
		$where['appId'] = getAppId();
		return $this->where($where)->select();
	}
}