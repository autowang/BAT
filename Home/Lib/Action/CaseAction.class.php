<?php

/**
 * 测试用例类
 */
class CaseAction extends CommonAction {

    /**
     * 测试用例列表
     */
    public function clist() {
        //如果caseid不为空，则直接id查询，其他查询条件不生效
        $caseId = $this->_get('case_id');
        if (!empty($caseId)) {
            $condition['id'] = trim($caseId);
        } else {

            //如果填写了API-ID，则直接根据id进行查询，path查询条件不再生效
            $apiId = $this->_get('api_id');
            if (!empty($apiId)) {
                $ids = D('Step')->getCaseIdByKey($apiId, 'id');
                $condition['id'] = array('in', $ids);
            } else {
                $apiPath = $this->_get('api_path');
                if (!empty($apiPath)) {
                    $ids = D('Step')->getCaseIdByKey($apiPath, 'path');
                    $condition['id'] = array('in', $ids);
                }
            }

            $desc = $this->_get('case_desc');
            if (!empty($desc)) {
                $condition['desc'] = array('like', '%' . $desc . '%');
            }

            $owner = $this->_get('owner');
            if (!empty($owner)) {
                $condition['userId'] = getUserIdByName(trim($owner));
            }
            $type = $this->_get("case_type");
            if (!empty($type)){
            	$condition['type'] = $type;
            }
            $mid = $this->_get("mid");
            if (!empty($mid)){
            	$condition['mid'] = intval($mid);
            }
        }
        
        $new = $this->merge($condition);
        $page = $this->getPaging('Case', $new);
        $paging = $page->show();
        $this->assign('paging', $paging);
        $cases = D('Case')->where($new)->order('updateTime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        //添加username作为返回信息
        foreach ($cases as $case) {
            $case['username'] = getUserName($case['userId']);
            $case['module'] = reset(D("Module")->getModule(array('id'=>$case['mid'])));
            $data[] = $case;
        }
        $this->assign('cases', $data);
        $this->display();
    }

    //异步获取api信息，返回table
    public function allApis() {
        if (!empty($_GET['path'])) {
            $condition['path'] = trim($_GET['path']);
        }
        //分页
        $apis = D('Api')->getApis($condition);
        $this->assign('apis', $apis);
        $table = $this->fetch();
        $this->ajaxReturn($table, '成功', 'success:true');
    }

    /**
     * 用例新增页面
     */
    public function add() {
    	$apiId = $this->_get('apiId');
    	if (!empty($apiId)){
	    	$api = D('Api')->field('id,desc,host,port,path,protocol,method,params')->getById($apiId);
	    	$api['current'] = md5(time());
    		$http = $this->tpl('Component:http',$api);
    		$this->assign('http',$http);
    	}
    	
        $this->display();
    }

    /**
     * 用例新增
     */
    public function doAdd() {
        $caseData = json_decode($_POST['caseData'], true);
        if (empty($caseData)) {
            $this->ajaxReturn(null, C('ERR_MSG_00'), 'success:false');
        }
        $validator = D('Case')->caseValidator($caseData);
        if ($validator !== true) {
            $this->ajaxReturn(null, $validator['msg'], 'success:false');
        }
        $data = D('Case')->transformData($caseData);
        $data = array_merge($data,getUserApp());
        $ret = D('Case')->relation(true)->add($data);
        if (empty($ret)) {
            $this->ajaxReturn(null, '保存用例失败', 'success:false');
        }
        $this->ajaxReturn($ret, '保存用例成功', 'success:true');
    }

    /**
     * 用例更新页面
     */
    public function update() {
        $id = $this->_get('id');
        if (empty($id)) {
            $this->error('请选择需要更新的数据', 'clist');
        }
        $this->checkApp('Case', $id, 'clist');
        $detail = D("Case")->relation(true)->getById($id);
        if (empty($detail)) {
            $this->error('该条数据不存在，id=' . $id, 'clist');
        }
        $steps = '';
        foreach ($detail['steps'] as $index => $step) {
            $content = json_decode($step['content'], true);
            $content['type'] = $step['type'];
            $content['current'] = md5(microtime() + $index);
            if ($step['type'] == CaseModel::TESTCASE) {
                $case = D('Case')->field('id,desc')->getById($content['id']);
                $steps .= $this->tpl('Component:testcase', $case);
            }
            if ($step['type'] == CaseModel::HTTP) {
                $api = M('Api')->field('desc,params')->getById($content['id']);
                if (!empty($api['params'])) {
                    $new = array_keys(D("Api")->formatParams($api['params']));
                }
                if (!empty($content['params'])) {
                    $old = array_keys(D("Api")->formatParams($content['params']));
                }
                $diff = array_merge(array_diff($new, $old), array_diff($old, $new));
                $content['diffParam'] = empty($diff) ? '' : implode(',', $diff);
                $content['desc'] = $api['desc'];
                $steps .= $this->tpl('Component:http', $content);
            }
            if ($step['type'] == CaseModel::ASSERT) {
                //获取assert模板，替换其中的radio名
                $assertPage = $this->tpl('Component:assert', $content);
                $assertPage = str_replace("assert_type", "assert_type_".$index, $assertPage);
                $assertPage = str_replace("assert_full", "assert_full_".$index, $assertPage);
                $assertPage = str_replace("assert_rule", "assert_rule_".$index, $assertPage);
                $assertPage = str_replace("assertType", "assertType_".$index, $assertPage);
                if($content['assertType'] == 2){
                    //解析assert为key-value格式
                    $asserts = explode('(.*)', trim(str_replace('("?)', "", $content['assert']), '(.*)'));
                    $keyValueRow = "";
                    foreach ($asserts as $ast){
                        $assertContent['key'] = explode(":", $ast)[0];
                        $assertContent['value'] = explode(":", $ast)[1];
                        $keyValueRow .= $this->tpl('Component:keyvalue', $assertContent);
                    }
                    $assertPage = str_replace("</thead>", $keyValueRow."</thead>", $assertPage);
                }
                $steps .= $assertPage;
            }
            if ($step['type'] == CaseModel::REGEX) {
                $steps .= $this->tpl('Component:regex', $content);
            }
            if ($step['type'] == CaseModel::BSF){
            	$steps .= $this->tpl('Component:bsf',$content);
            }
        }
        unset($detail['steps']);
        $summary = $detail;
        $this->assign('summary', $summary);
        $this->assign('steps', $steps);
        $this->display();
    }

    /**
     * 执行更新操作
     */
    public function doUpdate() {
        $caseData = json_decode($_POST['caseData'], true);
        $case = D('Case');
        if (empty($caseData)) {
            $this->ajaxReturn(null, C('ERR_MSG_00'), 'success:false');
        }
        $validator = $case->caseValidator($caseData);
        if ($validator !== true) {
            $this->ajaxReturn(null, $validator['msg'], 'success:false');
        }
        $data = $case->transformData($caseData);
        $data['id'] = $this->_post('id');
        $case->startTrans();
        $deleteSteps = M("Step")->where('caseId=' . $data['id'])->delete();
        $ret = $case->relation(true)->save($data);
        if (!empty($ret) && !empty($deleteSteps)) {
            $result = $case->commit();
            $this->ajaxReturn(null, '更新成功', 'success:true');
        } else {
            $case->rollback();
            $this->ajaxReturn(null, '更新失败，事务回滚，请重试', 'success:false');
        }
    }

    /**
     * 执行删除
     */
    public function delete() {
        $ids = $_POST["ids"];
        if (empty($ids)) {
            $this->ajaxReturn(0, "请选择待删除的case。", "success:false");
        }
        $caseIds = explode(',', $ids);
        foreach ($caseIds as $caseId){
	        $cids = D("Case")->caseUsing($caseId);
	        $pids = D("Case")->planUsing($caseId);
	        if(!empty($cids)){
	        	$this->ajaxReturn(null,"删除用例失败:用例[id=$caseId]与用例[ids=$cids]存在引用关系！","success:false");
	        }
	        if(!empty($pids)){
	        	$this->ajaxReturn(null,"删除用例失败:用例[id=$caseId]与计划[ids=$pids]存在引用关系！","success:false");
	        }
	        $data["id"] = $caseId;
	        $data['status'] = C('INVALID');
	        $data['updateTime'] = getCurrentTime();
	        D('Case')->save($data);
        }
        $this->ajaxReturn(null,"操作完成","success:true");
    }

    /**
     * 获取模版
     * @param 模版参数 $content
     * @param 模版名称 $name
     * @return 渲染后的模版字符串 <string, NULL>
     */
    public function tpl($name, $content = null) {
        $this->assign('step', $content);
        return $this->fetch($name);
    }

    /**
     * 下载已经生成好的测试计划
     */
    public function downloadCase() {
        $caseId = $this->_get('id');
        if (empty($caseId)) {
            $this->error('请选择需要下载的测试用例！', 'clist');
        }
        $jmx = A('Step')->jmx($caseId, 999999999,$planId='download');
        D('Case')->strToFile(JMX_PATH.'/download/',"$caseId.jmx",$jmx);
        $fileName = JMX_PATH."download/$caseId.jmx";
        import("ORG.Net.Http");
        Http::download($fileName, $caseId . ".jmx");
    }
    
    /**
     * 根据ID获取case
     */
    public function getCase() {
        $id = $this->_get('id');
        $desc = $this->_get('desc');
       
        if (!empty($id)){
        	$where['id'] = $id;
        }
        if (!empty($desc)) {
        	$where['desc'] = array('like',"%$desc%");
        }
        if(empty($where)){
        	$this->ajaxReturn(null, '请输入查询条件', 'success:false');
        }
        $where['status'] = C("VALID");
        $where['type'] = array('neq',2);//排除异常类型的用例
        $where['appId'] = getAppId();
        $case = D('Case')->where($where)->field('id,desc')->limit(10)->select();
        if (empty($case)) {
            $this->ajaxReturn(null, "没有找到id=$id的测试用例", 'success:false');
        }
        $this->ajaxReturn(json_encode($case), '查询成功', 'success:true');
    }
    
    /**
     * 用例复制
     */
    public function copy(){
    	$id = $this->_post('id');
    	if(empty($id)){
    		$this->ajaxReturn(null,'请选择需要复制的用例','success:false');
    	}
    	$case = D("Case")->relation(true)->getById($id);
    	unset($case['createTime']);
    	unset($case['id']);
    	$case['updateTime'] = getCurrentTime();
    	$case['userId'] = getUserId();
    	$case['desc'] = $case['desc']."--[复制]";
    	$steps = array();
    	foreach ($case['steps'] as $step){
    		unset($step['createTime']);
    		unset($step['id']);
    		unset($step['caseId']);
    		$steps[] = $step;
    	}
    	$case['steps'] = $steps;
    	$ret = D("Case")->relation(true)->add($case);
    	if($ret === false){
    		$this->ajaxReturn(null,'复制用例失败，稍后重试！','success:false');
    	}
    	$this->ajaxReturn($ret,'成功','success:true');
    }
    
    /**
     * 执行测试用例
     */
    public function execute(){
    	$caseId = $this->_post('id');
    	if (empty($caseId)){
    		$this->ajaxReturn(null,'请选择需要执行的用例','success:false');
    	}
    	$type = $this->_post("type");
    	if($type == 3){
    		$this->ajaxReturn($caseId,"类型为方法的用例不能单独执行！",'success:false');
    	}
    	$report = D("Report")->getReport(array('objId'=>$caseId,'status'=>ReportModel::RUNNING));
    	if ($report){
    		$this->ajaxReturn($caseId,'当前用例正在执行，请稍后在试！','success:false');
    	}
    	//插入测试结果，状态：执行中
    	$reportId =  D('Report')->addReport($caseId,ReportModel::TESTCASE);
    	if(empty($reportId)){
    		$this->ajaxReturn($caseId,'记录本次执行失败！','success:false');
    	}
    	$ret = D('Case')->execute($caseId,$reportId);
    	if($ret === true){
    		$this->ajaxReturn($caseId,'执行用例成功！','success:true');
    	}else{
    		D('Report')->delete(array('id'=>$reportId));
    		$this->ajaxReturn($caseId,"执行异常，回滚数据！",'success:false');
    	}
    }
    
}
