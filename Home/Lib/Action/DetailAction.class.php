<?php
class DetailAction extends CommonAction{
	
	/**
	 * 报告详情
	 */
	public function details() {
		$reportId = $this->_get('reportId');
		$caseId = $this->_get('caseId');
		$details = D('Detail')->getDetail(array("reportId"=>$reportId));
		if (empty($details)) {
			$this->error("暂时没有该次执行的报告详情，请查看日志", "__APP__/Report/crlist?caseId=$caseId", 2);
		}
		$data = array();
		$data['case'] = reset(D("Case")->getCase(array('id'=>$caseId)));
		foreach ($details as $detail) {
			$detail['api'] = reset(D("Api")->getApi(array('id'=>$detail['apiId'])));
			$data['details'][] = $detail;
		}
		$this->assign('data', $data);
		$this->display();
	}
}