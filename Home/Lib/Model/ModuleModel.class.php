<?php
class ModuleModel extends CommonModel{
	
	public function getModule($where="1=1"){
		return $this->where($where)->select();
	}
}