<?php
class ModuleAction extends CommonAction{
	
	public function mlist(){
		$desc = $this->_get('desc');
		if (!empty($desc)) {
			$condition['desc'] = array('like','%'.trim($desc).'%');
		}
		$condition = $this->merge($condition);
		//分页
		$page = $this->getPaging('Module', $condition);
		$paging = $page->show();
		$this->assign('paging', $paging);
		//根据查询条件获取数据
		$modules = D('Module')->where($condition)->order('createTime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('datas', $modules);
		$this->display();
	}
	
	public function modules(){
		$where['status'] = C("VALID");
		$where['appId'] = getAppId();
		$modules = D("Module")->getModule($where);
		$this->assign("datas",$modules);
		$this->ajaxReturn($this->fetch(),"成功",'success:true');
	}
}