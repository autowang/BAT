<?php
class DetailModel extends CommonRelationModel{

	public function getDetail($where='1=1'){
		return $this->where($where)->select();
	}
}