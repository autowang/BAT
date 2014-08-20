<?php
class ApiAction extends CommonAction{
	public function alist(){
		$data = array('total' => 1010,'pageNum' => 10);
		$this->assign("data",$data);
		$this->display();
	}
}

?>