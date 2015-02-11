<?php
class StepAction extends CommonAction{
	
	public function jmx($caseId,$resultId,$planId=null){
		$case = D("Case")->relation(true)->getById($caseId);
		$cookie = $this->tpl("Step:cookie");
		$testData = D("Step")->testData($case['appId'],$planId);
		$steps = D("Step")->steps($case,$resultId);
		foreach ($steps as $index=>$step){
			if(count($step) > 1){
				$jmx .= $this->tpl('Step:http',$step);
				continue;
			}
			$jmx .= $step;
		}
		$jmx = $cookie.$testData.$jmx;
		$this->assign('case',$case);
		$this->assign('steps',$jmx);
		$content = $this->fetch('Step:thread');
		return $content;
	}
	
	/**
	 * 获取模版
	 * @param 模版参数 $content
	 * @param 模版名称 $name
	 * @return 渲染后的模版字符串 <string, NULL>
	 */
	public function tpl($name,$content=null){
		$this->assign("step",$content);
		return $this->fetch($name)."\n";
	}
	

	
}