<?php
class ApiAction extends CommonAction{

	/**
	 * api列表
	 */
	public function alist(){
		$data = array('total' => 1010,'pageNum' => 10);
		$this->assign("data",$data);
		$bread = array(1,2,3);
		$this->assign('bread',$bread);
		$this->display();
	}

	/**
	 * API新增页面
	 */
	public function add(){
		$this->display();
	}


	/**
	 * api新增操作
	 */

	public function doAdd(){
		var_dump($_POST);exit;

		$this->redirect('Api/alist'); 
	}


}

?>