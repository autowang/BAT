<?php
class ReportAction extends CommonAction{
	/**
	 * 根据用例id获取用例的执行记录
	 */
	public function crlist() {
		$caseId = $this->_get('caseId');
		if (empty($caseId)) {
			$this->error("非法的参数-caseId", "__APP__/Case/clist", 2);
		}
		$condition['objId'] = $caseId;
		$condition['type'] = ReportModel::TESTCASE;
		$reports = D("Report") ->getReport($condition,true);
		$datas = array();
		foreach ($reports as $report) {
			$cases = D('Case')->getCase(array('id' => $report['objId']),false);
			$users = D('User')->getUser(array('id' => $report['userId']));
			$report['case'] = reset($cases);
			$report['user'] = reset($users);
			$datas[] = $report;
		}
		$this->assign('datas', $datas);
		$this->display();
	}
	
	/**
	 * 根据计划id获取计划的执行记录
	 */
	public function prlist(){
		$planId = $this->_get('planId');
		if(empty($planId)){
			$this->error("参数planId非法", "__APP__/Plan/plist", 2);
		}
		$where['objId'] = $planId;
		$where['type'] = ReportModel::PLAN; 
		$reports = D("Report")->getReport($where,false);
		$datas = array();
		foreach ($reports as $report){
			$count = D("Report")->getSuccFailNum($report['id']);
			$plans = D("Plan")->getPlan(array('id'=>$planId));
			$users = D('User')->getUser(array('id' => $report['userId']));
			$report['user'] = reset($users);
			$report['plan'] = reset($plans);
			if ($report['status'] == ReportModel::RUNNING){
				$report['success'] = $report['fail'] = '--';
			}else{
				$report['success'] = $count['success'];
				$report['fail'] = $count['fail'];
			}
			$datas[] = $report;
		}
		$this->assign('datas',$datas);
		$this->display();
	}
	
	/**
	 * 获取对应计划上一次执行的case结果集合
	 */
	public function cases(){
		$where['prid'] = $prid = $this->_get('prid');
		if (empty($prid)){
			$this->error("非法的测试计划report id",'prlist');
		}
		$casesId = $this->_get('caseId');
		$result = $this->_get('result');
		if (!empty($casesId)){
			$where['objId'] = $casesId;
		}
		if(!empty($result)){
			$where['result'] = $result;
		}
		$where['type'] = ReportModel::TESTCASE;
		
		$page = $this->getPaging('Report', $where);
		$paging = $page->show();
		$this->assign('paging', $paging);
		$crlist = D('Report')->relation(true)->where($where)->order('createTime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$datas = array();
		foreach ($crlist as $cr){
			$cases = D('Case')->getCase(array('id' => $cr['objId']),false);
			$cr['case'] = reset($cases);
			$datas[] = $cr;
		}
		$this->assign('datas',$datas);
		$this->display();
	}
	
	/**
	 * 获取执行日志
	 */
	public function getLog() {
		$reportId = $this->_get("reportId");
		$result = reset(D("Report")->getReport(array('id'=>$reportId)));
		$this->ajaxReturn($result['log'], "成功", 'success:true');
	}
	
	/**
	 * 根据id获取report记录的status
	 */
	public function getStatus(){
		$prid = $this->_get('prid');
		$data = D("Report")->field('status')->getById($prid);
		$this->ajaxReturn($data['status'],'成功','success:true');
	}
}