<?php
/**
* 测试计划类
*/
class PlanAction extends CommonAction
{
	
	/**
	 * 测试计划列表
	 */
	public function plist(){
		//如果caseid不为空，则直接id查询，其他查询条件不生效
		$planId = $this->_get('plan_id');
		if (!empty($planId)){
			$condition['id'] = trim($planId);
		}else {
			$desc = $this->_get('plan_desc');
			if (!empty($desc)){
				$condition['desc'] = array('like','%'.$desc.'%');
			}
				
			$owner = $this->_get('owner');
			if (!empty($owner)){
				$condition['userId'] = getUserIdByName(trim($owner));
			}
		}
		$new = $this->merge($condition);
		$page = $this->getPaging('Plan', $new);
		$paging = $page->show();
		$this->assign('paging',$paging);
		$plans = D('Plan')->where($new)->order('updateTime desc')->limit($page->firstRow.','.$page->listRows)->select();
		//添加username作为返回信息
		foreach ($plans as $plan){
			$plan['caseCount'] = count(explode(",", $plan['caseIds']));
			$plan['username'] = getUserName($plan['userId']);
			$data[] = $plan;
		}
		$this->assign('plans',$data);
		$this->display();
	}
	
	//新增计划页面
	public function add(){
		$this->display();
	}
	
	/**
	 * 执行新增操作
	 */
	public function doAdd(){
		$plan = D("Plan");
		$data = M("Plan")->create();
		if (!empty($data['data'])){
			$result = $plan->parseParam($data['data']);
			if(!$result){
				$this->ajaxReturn(null,'您定义了私有参数，但是定义的格式有误！','success:false');
			}
			$ret = $plan->checkParam($result);
			if($ret['status'] === false){
				$this->ajaxReturn(null,"有部分参数定义无效[".$ret['data']."]",'success:false');
			}
		}
		if(empty($data['caseIds'])){
			$this->ajaxReturn(null,"请添加测试用例","success:false");
		}
		$data['caseIds'] = implode(',', $data['caseIds']);
		$data['appId'] = getAppId();
		$data['userId'] = getUserId();
		$data['updateTime'] = getCurrentTime();
		$ret = $plan->add($data);
		if(empty($ret)){
			$this->ajaxReturn(null,'新增测试计划失败','success:false');
		}
		$this->ajaxReturn($ret,'成功','success:true');
	}
	
	/**
	 * 打开更新页面
	 */
	public function update(){
		$id = $this->_get('id');
		if(empty($id)){
			$this->ajaxReturn(null,'请选择需要修改的记录','success:false');
		}
		
		$plan = M('Plan')->getById($id);
		$where['id'] = array('in',$plan['caseIds']);
		$cases = D('Case')->where($where)->order('id asc')->field('id,desc')->select();
		$this->assign('cases',$cases);
		$this->assign('plan',$plan);
		$this->display();
	}
	
	/**
	 * 执行更新操作
	 */
	public function doUpdate(){
		$data = M("Plan")->create();
		$data['caseIds'] = implode(',', $data['caseIds']);
		$data['updateTime'] = getCurrentTime();
		$ret = M('Plan')->save($data);
		if(empty($ret)){
			$this->ajaxReturn(null,'修改测试计划失败','success:false');
		}
		$this->ajaxReturn($ret,'成功','success:true');
	}
	
	/**
	 * 删除测试用例
	 */
	public function delete(){
		$ids = $this->_post('ids');
		if (empty($ids)){
			$this->ajaxReturn(null,'请选择需要删除的数据','success:false');
		}
		$where['id'] = array('in',$ids);
		$data['status'] = C('INVALID');
		$ret = M('Plan')->where($where)->save($data);
		if(empty($ret)){
			$this->ajaxReturn(null,'删除测试计划失败','success:false');
		}
		$this->ajaxReturn($ret,'成功','success:true');
	}
	
	/**
	 * 获取所有有效的且不再当前测试计划内的case
	 */
	public function allCase(){
		$id = $this->_get('id');
        $caseId = $this->_get('case_id');
        $caseDesc = $this->_get('case_desc');
		$where = $this->merge();
		if ($id > 0){
			$plan = M('Plan')->getById($id);
			$where['id'] = array('not in',$plan['caseIds']);
		}
        if (!empty($caseId)){
            $where['id'] = $caseId;
        }
        if (!empty($caseDesc)) {
            $where['desc'] = array('like',"%$caseDesc%");
        }
		$where['type'] = array('neq',3);
		$cases = M("Case")->where($where)->field('id,desc')->select();
		$this->assign("cases",$cases);
		$content = $this->fetch("Plan:allcase");
		$this->ajaxReturn($content,'','success:true');
	}
	
	/**
	 * 执行测试计划
	 */
	public function execute(){
		$planId = $this->_get('planId');
		if (empty($planId)){
			$this->ajaxReturn(null,'请选择需要执行的测试计划','success:false');
		}
		
		$plan = M('Plan')->getById($planId);
		$caseIds = explode(',', $plan['caseIds']);
		
		if(empty($caseIds)){
			$this->ajaxReturn(null,"测试计划[id=".$plan['id'].']还没有添加任何测试用例','success:false');
		}
		$report = D("Report")->getReport(array('objId'=>$caseId,'status'=>ReportModel::RUNNING));
		if ($report){
			$this->ajaxReturn($caseId,'当前计划正在执行，无需重复运行！','success:false');
		}
		
		$planReportId = D("Report")->addReport($planId,ReportModel::PLAN);
		if (empty($planReportId)){
			$this->ajaxReturn(null,'测试计划执行失败','success:false');
		}
		foreach ($caseIds as $caseId){
			$caseReportId = D('Report')->addReport($caseId,ReportModel::TESTCASE,$planReportId);
			D('Case')->execute($caseId,$caseReportId,$planId);
		}
		$ret = D('Report')->setOver(array('id'=>$planReportId));
		if ($ret){
			$this->ajaxReturn($planId,'测试计划执行完成','success:true');
		}
	}
}