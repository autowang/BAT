<?php
class ReportModel extends CommonRelationModel{
	
	const TESTCASE = 1;
	const PLAN = 2;
	
	const SUCCESS = 1;
	const FAILURE = 2;
	const WAIT = 3;
	
	const RUNNING = 1;
	const OVER = 2;
	//关联配置
	protected $_link = array(
			'Detail' => array(
					'mapping_type'=>HAS_MANY,
					'class_name'=>'Detail',
					'foreign_key'=>'reportId',
					'mapping_name'=>'details',
					'mapping_order'=>'id asc',
			),
	);
	
	/**
	 * 根据给定的查询条件获取结果
	 * @param string $where
	 * @param string $relation
	 */
	public function getReport($where='1=1',$relation=false){
		return $this->where($where)->relation($relation)->order('id desc')->limit(C('PAGING_SIZE'))->select();
	}
	
	/**
	 * 获取case的执行结果
	 * @param 每一步信息 $details
	 * @return string
	 */
	public function getResult($details){
		if (empty($details)){
			return self::WAIT;
		}
		foreach ($details as $detail){
			$status = $detail['result'];
			if ($status == 2){
				return self::FAILURE;
			}
		}
		return self::SUCCESS;
	}
	
	/**
	 * 新增测试报告
	 * @param 测试用例id $caseId
	 * @return 新增数据的id
	 */
	public function addReport($objId,$type,$prid=-1){
		$data['objId'] = $objId;
		$data['userId'] = getUserId();
		$data['status'] = ReportModel::RUNNING;
		$data['type'] = $type;
		$data['prid'] = $prid;
		return $this->add($data);
	}
	
	/**
	 * 将状态设置成执行完成,并写入log
	 * @param 设置的数据 $data
	 */
	public function setOver($data){
		//将执行该条case时的log保存下来
		$this->saveJmeterLog($data['id']);
		$details = D("Detail")->getDetail(array('reportId'=>$data['id']));
		$data['result'] = D("Report")->getResult($details);
		$data['status'] = ReportModel::OVER;
		$ret = $this->save($data);
		if(!$ret){
			Log::write("设置执行状态为完成失败，reportId=".$data['id']);
		}
	}
	
	public function saveJmeterLog($reportId){
		$log = file_get_contents(BASE_PATH.'/jmeter.log');
		$this->strToFile(BASE_PATH.'/Logs/', "$reportId", $log);
	}
	
	/**
	 * 获取测试计划中有多少case通过，多少失败
	 * @param 测试计划对应的执行记录id $reportId
	 */
	public function getSuccFailNum($reportId){
		$crlist = D('Report')->where(array('prid'=>$reportId))->select();
		$success = $fail = 0;
		foreach ($crlist as $cr){
			if($cr['result'] == self::SUCCESS){
				$success++;
			}else{
				$fail++;
			}
		}
		return array('success'=>$success,'fail'=>$fail);
	}
}