<?php
/**
 * 测试用例model
 * @author wangsheng03
 *
 */
class CaseModel extends CommonRelationModel{
	
	const HTTP = 1;
	const ASSERT = 2;
	const REGEX = 3;
	const TESTCASE = 4;
	const BSF = 5;
	
	//定义map
	protected $_map = array(
			'case_desc'=>'desc',
			'thread_num'=>'threadNum',
			'run_time'=>'runTime',
			'case_type'=>'type',
		);
	
	//关联配置
	protected $_link = array(
		'Step' => array(
			'mapping_type'=>HAS_MANY,
				'class_name'=>'Step',
				'foreign_key'=>'caseId',
				'mapping_name'=>'steps',
				'mapping_order'=>'stepId asc',
		),
	);
	
	
	function caseValidator($caseData){
		//检查summary信息
		$ret_summary = $this->checkSummary($caseData['summary']);
		if($ret_summary['status'] === false){
			return $ret_summary;
		}
		
		$steps = $caseData['steps'];
		if(empty($steps)){
			return array('status'=>false,'msg'=>"请添加测试步骤！");
		}
		//数据校验
		foreach ($steps as $k => $v){
			//检查api步骤
			if($v['type'] == self::HTTP){
				$ret_http = $this->checkHttp($v);
				if($ret_http['status'] === false){
					$ret_http['msg'] = "第".($k+1)."步--".$ret_http['msg'];
					return $ret_http;
				}
				continue;
			}
			//检查断言步骤
			if($v['type'] == self::ASSERT){
				$ret_assert = $this->checkAssert($v);
				if($ret_assert['status'] === false){
					$ret_assert['msg'] = "第".($k+1)."步--".$ret_assert['msg'];
					return $ret_assert;
				}
				continue;
			}
			//检查参数提取器步骤
			if($v['type'] == self::REGEX){
				$ret_regex = $this->checkRegex($v);
				if($ret_regex['status'] === false){
					$ret_regex['msg'] = "第".($k+1)."步--".$ret_regex['msg'];
					return $ret_regex;
				}
				continue;
			}
		}
		return true;
	}
	
	//检验用例的基本信息
	function checkSummary($summary){
		$result = array('status'=>false);
		if(empty($summary['case_desc'])){
			$result['msg'] = '用例描述不能为空';
			return $result;
		}
		if(empty($summary['thread_num'])){
			$result['msg'] = '线程数不能为空';
			return $result;
		}
		if(empty($summary['rampUpPeriod'])){
			$result['msg'] = '启动策略不能为空';
			return $result;
		}
		if (empty($summary['case_type'])){
			$result['msg'] = '用例类型必须选择';
			return $result;
		}
		if (empty($summary['mid'])){
			$result['msg'] = '请指定用例所属模块';
			return $result;
		}
		return true;
	}
	
	//检查断言
	public function checkAssert($assert){
		$result = array('status'=>false);
		if(empty($assert['assert'])){
			$result['msg'] = '添加了断言步骤，但是断言内容为空！';
			return $result;
		}
		return true;
	}
	
	//检查api字段
	public function checkHttp($http){
		$result = array('status'=>false);
		if(empty($http['id'])){
			$result['msg'] = "API的ID不能为空！";
			return $result;
		}
		if(empty($http['host'])){
			$result['msg'] = "域名不能为空！";
			return $result;
		}
		if(empty($http['port'])){
			$result['msg'] = "端口不能为空！";
			return $result;
		}
		if(empty($http['method'])){
			$result['msg'] = "请求的method不能为空！";
			return $result;
		}
		if(empty($http['protocol'])){
			$result['msg'] = "协议类型不能为空！";
			return $result;
		}
		if(empty($http['path'])){
			$result['msg'] = "请求路径不能为空！";
			return $result;
		}
		return true;
	}
	//检查参数提取器字段
	public function checkRegex($regex){
		$result = array('status'=>false);
		if(empty($regex['param_name'])){
			$result['msg'] = '参数提取器中，变量名不能为空！';
			return $result;
		}
		if(empty($regex['regex_expression'])){
			$result['msg'] = '参数提取器中，正则表达式不能为空！';
			return $result;
		}
		if($regex['select_which'] < 0){
			$result['msg'] = '参数提取器中，请填写选择匹配结果的索引！';
			return $result;
		}
		return true;
	}
	
	//前端传过来的json string做处理，返回array，直接insert
	public function transformData($caseData){
		$data = D('Case')->create($caseData['summary']);
		$data['updateTime'] = getCurrentTime();
		$data['status'] = C('VALID');
		$data['steps'] = array();
		foreach ($caseData['steps'] as $index=>$v){
			$step = array();
			$step['stepId'] = $index;
			$step['type'] = $v['type'];
			unset($v['type']);
			$step['content'] = json_encode($v,JSON_UNESCAPED_SLASHES);
			$data['steps'][] = $step;
		}
		return $data;
	}
	
	/**
	 * 根据caseid获取case当前的状态
	 * @param caseid $id
	 */
	public function isValid($id){
		$case = $this->field('id,status')->getById($id);
		if ($case['status'] == C('VALID')){
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * 根据给定的条件查询case
	 * @param string $where
	 */
	public function getCase($where='1=1',$relation=false){
		return $this->relation($relation)->where($where)->select();
	}
	
	/**
	 * 执行测试用例
	 * @param unknown $resultId
	 */
	public function runCmd($caseId,$planId){
		$jmx = JMX_PATH."$planId/$caseId.jmx";
		$cmd = C('JMETER')." -n -t ".$jmx;
		exec($cmd,$output,$retval);
		return array('msg'=>$output,'code'=>$retval);
	}
	
	/**
	 * 根据caseId执行case
	 * @param 用例id $caseId
	 * @param 报告类别 $type
	 * @return boolean
	 */
	public function execute($caseId,$reportId,$planId='debug'){
		//获取测试用例内容
		$jmx = A("Step")->jmx($caseId,$reportId,$planId);
		//写入文件
		$this->strToFile(JMX_PATH."$planId/","$caseId.jmx",$jmx);
		//执行case
		$ret = $this->runCmd($caseId, $planId);
		$data['log'] = implode("\r",$ret['msg']);
		$data['id'] = $reportId;
		D('Report')->setOver($data);
		if($ret['code'] === 0){
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * 检查某一个case是否被其他case引用
	 */
	public function caseUsing($caseId){
		$where['type'] = self::TESTCASE;
		$where['content'] = array('like',"%$caseId%");
		$steps = D("Step")->getStep($where);
		
		if (empty($steps)){
			return null;
		}
		$cids = array();
		foreach ($steps as $step){
			$cids[] = $step['caseId'];
		}
		return implode(',', array_unique($cids));
		
	}
	
	/**
	 * 检查某一个case是否被其他plan引用
	 */
	public function planUsing($caseId){
		$where['appId']=getAppId();
		$plans = D("Plan")->where($where)->select();
		if (empty($plans)){
			return null;
		}
		$pids = array();
		foreach ($plans as $plan){
			if (in_array($caseId, explode(',', $plan['caseIds']))){
				$pids[] = $plan['id'];
			}
		}
		return implode(',', array_unique($pids));
	}
}