<?php

/*
 * 系统组件类
 */
class ComponentAction extends CommonAction{
	
	//http模版
	public function http(){
		$id=$this->_get('id');
		$api = D("Api")->getById($id);
		$api['current'] = md5(microtime());
		if(empty($api)){
			$this->ajaxReturn(null,'添加失败，请重试','success:false');
		}
		$this->assign('step',$api);
		$content = $this->fetch();
		$this->ajaxReturn($content,"成功",'success:true');
	}
	
	//断言模版
	public function assert(){
		$step['current'] = md5(microtime());
		$this->assign('step',$step);
		$content = $this->fetch();
		$this->ajaxReturn($content,"成功",'success:true');
	}
	
	//参数提取器
	public function regex(){
		$step['current'] = md5(microtime());
		$this->assign('step',$step);
		$content = $this->fetch();
		$this->ajaxReturn($content,"成功",'success:true');
	}
	
	/*
	 * 测试用例
	 */
	public function testcase(){
		$id=$this->_get('id');
		$case = D("Case")->getById($id);
		if(empty($case)){
			$this->ajaxReturn(null,'添加失败，请重试','success:false');
		}
		$this->assign('step',$case);
		$content = $this->fetch();
		$this->ajaxReturn($content,"成功",'success:true');
	}
	
	public function bsf(){
		$step['current'] = md5(microtime());
		$this->assign('step',$step);
		$content = $this->fetch();
		$this->ajaxReturn($content,"成功",'success:true');
	}
}