<?php

/**
 * APP类
 */
class AppAction extends CommonAction {

    /**
     * App列表
     */
    public function applist() {
        //传入参数判断
        $id = $this->_get('id');
        if (!empty($id)) {
            $condition['id'] = trim($id);
        } else {
            $appName = $this->_get('appName');
            if (!empty($appName)) {
                $condition['appName'] = array('LIKE', '%' . trim($appName) . '%');
            }
        }
        $condition = $this->merge($condition);
        //分页
        $page = $this->getPaging('App', $condition);
        $paging = $page->show();
        $this->assign('paging', $paging);

        //根据查询条件获取数据
        $apps = D('App')->where($condition)->order('id asc')->limit($page->firstRow . ',' . $page->listRows)->select();
        //添加username作为返回信息
        foreach ($apps as $app) {
            $data[] = $app;
        }
        $this->assign('apps', $data);
        $this->display();
    }

    /**
     * 新增app
     */
    public function add() {
        $this->display();
    }

    /**
     * 执行新增app操作
     */
    public function doAdd() {
        $app = D('App')->create();
        if (!$app) {
            $this->ajaxReturn(null, D('App')->getError(), 'success:false');
        }

        //判断传入参数是否符合格式
        $app = D('App')->checkApp($app);

        //判断app名称是否重复
        $condition['appName'] = $app['appName'];
        $temp = D('App')->checkDuplicate($condition);
        if (!empty($temp)) {
            $this->ajaxReturn(0, "已经有相同的app存在，不允许重复录入", "success:false");
        }

        $ret = D('App')->add($app);

        if ($ret) {
            $this->ajaxReturn($ret, "成功新增App数据！", "success:true");
        } else {
            $this->ajaxReturn(0, "新增App数据失败！", "success:false");
        }
    }

    /**
     * 删除app
     */
    public function delete() {
        $ids = $this->_post('ids');
        if (empty($ids)) {
            $this->ajaxReturn(0, "请选择待删除的App。", "success:false");
        }
        $condition["id"] = array("in", $ids);
        $ret = D('App')->where($condition)->delete();
        if (empty($ret)) {
            $this->ajaxReturn($ret, "删除App失败！", "success:false");
        }
        $this->ajaxReturn($ret, "成功删除App。", "success:true");
    }

    /**
     * app更新页面显示
     */
    public function update() {
        $id = $this->_get('id');
        //根据id查询app信息
        $ret = D('App')->getById($id);
        if (empty($ret)) {
            $this->error("ID=" . $id . "对应的APP不存在！！！", "applist", 2);
        }
        $this->assign("app", $ret);
        $this->display();
    }

    /**
     * app更新操作
     */
    public function doUpdate() {
        $app = D('App')->create();
        if (!$app) {
            $this->ajaxReturn(null, D('App')->getError(), 'success:false');
        }

        //判断传入参数是否符合格式
        $app = D('App')->checkApp($app);

        //判断app名称是否重复
        $condition['id'] = array(neq, $app['id']);
        $condition['appName'] = $app['appName'];
        $temp = D('App')->checkDuplicate($condition);
        if (!empty($temp)) {
            $this->ajaxReturn(0, "已经有相同的app存在，不允许重复录入", "success:false");
        }
        //执行更新
        $ret = D('App')->save($app);
        if ($ret === false) {
            $this->ajaxReturn($ret, "更新App数据失败！", "success:false");
        }
        $this->ajaxReturn(0, "成功更新App数据！", "success:true");
    }

    /**
     * 获取用户关联的app信息
     */
    public function appInfo() {
        $uid = session('uid');
        $userApps = D('User')->relation(true)->getById($uid);
        $apps = $userApps['apps'];
        
        if(empty($apps)){
        	if ($userApps['role'] == UserModel::ADMIN){
        		$apps = D('App')->select();
        	}else{
        		$this->ajaxReturn(null, "当前登录用户不属于任何项目，请联系管理员", "success:false");
        	}
        }
        $this->assign("apps", $apps);
        $content = $this->fetch();
        $this->ajaxReturn($content, '成功', 'success:true');
    }

    /**
     * 切换app空间
     */
    public function switchApp() {
        $appId = $this->_post('appId');
        $appName = $this->_post('appName');
        if (empty($appId) || empty($appName)) {
            $this->ajaxReturn(null, "空间切换失败，请重试。。", "success:false");
        } else {
            session('appId', $appId);
            session('appName', $appName);
            $this->ajaxReturn(null, "切换成功", "success:true");
        }
    }

}
