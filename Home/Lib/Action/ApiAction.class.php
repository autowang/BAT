<?php

/**
 * API模块控制器
 * @author wangsheng03
 *
 */
class ApiAction extends CommonAction {

    /**
     * 根据查询条件，查询出合适的api
     */
    public function alist() {
        $apiId = $this->_get('apiId');
        if (!empty($apiId)) {
            $condition['id'] = trim($apiId);
        } else {
            $path = $this->_get('path');
            if (!empty($path)) {
                $condition['path'] = trim($path);
            }
            $method = $this->_get('method');
            if (!empty($method)) {
                $condition['method'] = trim($method);
            }
            $owner = $this->_get('owner');
            if (!empty($owner)) {
            	$user = D("User")->getUser(array('name'=>trim($owner)));
                $condition['userId'] = $user[0]['id'];
            }
            $mid = $this->_get("mid");
            if (!empty($mid)){
            	$condition['mid'] = $mid;
            }
        }
        $condition = $this->merge($condition);
        //分页
        $page = $this->getPaging('Api', $condition);
        $paging = $page->show();
        $this->assign('paging', $paging);

        //根据查询条件获取数据
        $apis = D('Api')->where($condition)->order('updateTime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $data = array();
        foreach ($apis as $api) {
        	//添加user信息
        	$api['user'] = reset(D('User')->getUser(array('id'=>$api['userId'])));
        	$api['module'] = reset(D("Module")->getModule(array('id'=>$api['mid'])));
        	//添加引用次数信息
            $api['times'] = D("Step")->apiUseTimes($api['id']);
            $data[] = $api;
        }
        $this->assign('apis', $data);
        $this->display();
    }

    /**
     * API新增页面
     */
    public function add() {
        $this->display();
    }

    /**
     * api新增操作
     */
    public function doAdd() {
        $api = D('Api')->create();
        if (!$api) {
            $this->ajaxReturn(null, D('Api')->getError(), 'success:false');
        }
        //判断传入参数是否符合格式
        $api = D('Api')->parseRequestHeader($api);
        if (empty($api)) {
            $this->ajaxReturn(0, "请求头信息输入不正确，请检查格式！", "success:false");
        }
        $api['userId'] = getUserId();
        //判断传入参数是否重复
        $condition['host'] = $api['host'];
        $condition['method'] = $api['method'];
        $condition['path'] = $api['path'];
        $condition = $this->merge($condition);
        $temp = D('Api')->checkDuplicate($condition);
        if (!empty($temp)) {
            $this->ajaxReturn(0, "已经有相同的api存在，不允许重复录入", "success:false");
        }
        $ret = D('Api')->add($api);
        if ($ret) {
            $this->ajaxReturn($ret, "成功新增Api数据！", "success:true");
        } else {
            $this->ajaxReturn(0, "新增Api数据失败！", "success:false");
        }
    }

    /**
     * 删除api--软删除
     */
    public function delete() {
        $ids = $this->_post('ids');
        if (empty($ids)) {
            $this->ajaxReturn(0, "请选择待删除的Api。", "success:false");
        }

        $ret = D("Api")->isUsing($ids);
        if (!empty($ret)) {
            $ids = implode(",", $ret);
            $this->ajaxReturn($ids, "选择的API与Case[$ids]存在引用关系，不能删除！", 'success:false');
        }
        $condition["id"] = array("in", $ids);
        $data['status'] = C('INVALID');
        $ret = D('Api')->where($condition)->save($data);
        if (empty($ret)) {
            $this->ajaxReturn($ret, "删除Api失败！", "success:false");
        }
        $this->ajaxReturn($ret, "成功删除Api。", "success:true");
    }

    /**
     * api更新页面显示
     */
    public function update() {
        $id = $this->_get('id');
        $this->checkApp('Api', $id, 'alist');
        if (empty($id)) {
            $this->error("请选择待修改的Api", "alist", 2);
        }

        //根据id查询api信息
        $ret = D('Api')->getById($id);

        if (empty($ret)) {
            $this->error("ID=" . $id . "对应的API不存在！！！", "alist", 2);
        }
        $this->assign("api", $ret);
        $this->display();
    }

    /**
     * api更新操作
     */
    public function doUpdate() {
        $data = D('Api')->create();
        if (!$data) {
            $this->ajaxReturn(null, D('Api')->getError(), 'success:false');
        }
        //判断请求包是否符合格式
        $api = D('Api')->parseRequestHeader($data);
        if (empty($api)) {
            $this->ajaxReturn(0, "请求头信息输入不正确，请检查格式！", "success:false");
        }

        //判断是否已经存在
        $condition['id'] = array(neq, $data['id']);
        $condition['method'] = $api['method'];
        $condition['path'] = $api['path'];
        $condition['host'] = $api['host'];
        $condition = $this->merge($condition);
        $rows = D('Api')->checkDuplicate($condition);
        if (!empty($rows)) {
            $this->ajaxReturn(0, "已经存在的API数据，不能重复录入", "success:false");
        }
        //执行更新
        $ret = D('Api')->save($api);
        if ($ret === false) {
            $this->ajaxReturn(0, "更新Api数据失败！", "success:false");
        }
        $this->ajaxReturn($ret, "成功更新Api数据！", "success:true");
    }
    
    /**
     * 获取与输入api-path相关的推荐
     */
    public function apis(){
    	$path = $this->_get("path");
    	$condition['path'] = array('like','%'.$path.'%');
    	$condition['appId'] = getAppId();
    	$apis = D("Api")->where($condition)->field('path')->select();
    	$temp = array();
    	foreach ($apis as $api){
    		$temp[] = $api['path'];
    	}
    	$this->ajaxReturn(json_encode($temp),'成功','success:true');
    }
}

?>