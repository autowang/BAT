<?php
class StepModel extends Model{
	
	/**
	 * 提供给case模块的查询方法
	 * @param 查询的关键词 $key
	 * @param 根据什么字段查询 $type
	 * @return string
	 */
	public function getCaseIdByKey($key,$type){
		$condition['type'] = CaseModel::HTTP;
		//查询以   id":"105",
		$prefix = ($type == 'id') ? 'id":"' : '';
		$condition['content'] = array('like','%'.$prefix.$key.'"%'); 
		$apis = M('Step')->where($condition)->field('caseId')->group('caseId')->select();
		Log::write(M('Step')->getLastSql());
		$ids = array();
		foreach ($apis as $api){
			$ids[] = $api['caseId'];
		}
		return implode(',',$ids);
	}
	
	
	/**
	 * 根据API的ID来统计该api被引用多少次
	 * @param apiid $apiId
	 */
	public function apiUseTimes($apiId){
		$condition['type'] = CaseModel::HTTP;
		$condition['content'] = array('like',"%id\":\"$apiId\"%");
		$ret = M("Step")->Distinct(true)->field('id')->where($condition)->count();
		$ret = is_null($ret) ? 0 : $ret;
		return $ret;
	}
	
	/**
	 * 根据查询条件获取用例步骤
	 * @param 查询条件 $where
	 */
	public function getStep($where='1=1'){
		return $this->where($where)->select();
	}
	
	public function http($content){
		$api = D('Api')->getById($content['id']);
		//添加header
		if (!empty($api['header'])){
			$header = A("Step")->tpl("Step:header",json_decode($api['header'],true));
		}
		$content['apiId'] = $api['id'];
		$content['apiDesc'] = $api['desc'];
		$content['header'] = $header;
		$content['paramStr'] = $content['params']; //把原始参数字符串保存一下
		$params = D("Api")->formatParams($content['params']);
		$value = reset(array_values($params));
		$content['params'] = is_null($value) ? null : $params;
		$content['fileUpload'] = empty($content['file_path']) ? 'false' : 'true';
		return $content;
	}
	
	//获取每步数据，拼装到jmx
	public function steps($case,$reportId,$sts=array()){
		$steps = $case['steps'];
		$over = count($steps) - 1;
		foreach ($steps as $index=>$step){
			if ($step['type'] != CaseModel::ASSERT && $step['type'] != CaseModel::REGEX && !empty($temp)){
				$sts[] = $temp;
				$temp = null;
			}
			$content = json_decode($step['content'],true);
			switch ($step['type']){
				case CaseModel::TESTCASE:
					$case = D("Case")->relation(true)->getById($content['id']);
					$sts = $this->steps($case,$reportId,$sts);
					break;
				case CaseModel::HTTP:
					$temp = $this->http($content);
					break;
				case CaseModel::ASSERT:
					$temp['assert'] = $this->assert($case['id'],$temp['apiId'],$reportId,$content);
					if ($over == $index){
						$sts[] = $temp;
					}
					break;
				case CaseModel::REGEX:
					$temp['regex'] = A("Step")->tpl("Step:regex",$content);
					if ($over == $index){
						$sts[] = $temp;
					}
					break;
				case CaseModel::BSF:
					$sts[] = A("Step")->tpl("Step:bsf",$content);
					$temp = null;
					break;
			}
		}
		return $sts;
	}
	
	public function assert($caseId,$apiId,$reportId,$content){
		$content['ip'] = C('DB_HOST');
		$content['db'] = C('DB_NAME');
		$content['username'] = C('DB_USER');
		$content['password'] = C('DB_PWD');
		$content['reportId'] = $reportId;
		$content['assert'] = addslashes($content['assert']);
		$table = "t_detail";
		$column = "(caseId,apiId,requestHeader,responseHeader,responseBody,latency,result,reportId,assertContent)";
		$content['query'] = "insert into ".$content['db'].".".$table.$column." values(".$caseId.",".$apiId.",?,?,?,?,?,?,?);";
		$assert = A("Step")->tpl('Step:assert',$content);
		return $assert;
	}
	
	//装载测试数据
	public function testdata($appId,$planId){
		//加载测试数据
		$condition['appId'] = $appId;
		$datas = M("Data")->where($condition)->select();
		if (empty($datas)){
			return '';
		}
		//用plan中定义的参数覆盖测试数据中对应的data
		if (!empty($planId)){
			$result = D("Plan")->field('data')->getById($planId);
			$planData = D("Plan")->parseParam($result['data']);
			$temp = array();
			foreach ($datas as $data){
				$keys = array_keys($planData);
				if (in_array($data['key'], $keys)){
					$data['value'] = $planData[$data['key']];
				}
				$temp[] = $data;
			}
		}
		return A("Step")->tpl('Step:testdata',$temp);
	}
}