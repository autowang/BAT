<?php
class ModuleAction extends CommonAction{
	
	public function mlist(){
		$name = $this->_get('name');
		if (!empty($name)) {
			$condition['name'] = array('like','%'.trim($name).'%');
		}
		$condition = $this->merge($condition);
		//分页
		$page = $this->getPaging('Module', $condition);
		$paging = $page->show();
		$this->assign('paging', $paging);
		//根据查询条件获取数据
		$modules = D('Module')->where($condition)->order('createTime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('datas', $modules);
		$this->display();
	}
	
	public function modules(){
		$where['status'] = C("VALID");
		$where['appId'] = getAppId();
		$modules = D("Module")->getModule($where);
		$this->assign("datas",$modules);
		$this->ajaxReturn($this->fetch(),"成功",'success:true');
	}
    /**
     * 新增module
     */
    public function add() {
        $apps = D('App')->getAllApps();
        $currentApp=session('appName');
        $this->assign('currentApp', $currentApp);
        $this->assign('apps',$apps);
        $this->display();
    }
    /**
     * 执行新增module操作
     */
    public function doAdd() {
        $module = D('Module')->create();
        if (!$module) {
            $this->ajaxReturn(null, D('Module')->getError(), 'success:false');
        }
        //判断传入参数是否符合格式
        $module = D('Module')->checkModule($module);
        /*表插入*/
        $currnet_appId=D('App')->getIdByAppName(session('appName'));
        //默认App插入module
        $data['name'] = $module['name'];
        $data['status'] = C('VALID');
        $data['appId'] = $currnet_appId;
        $temp = D('Module')->checkDuplicate($data);
        if (!empty($temp)) {
            $this->ajaxReturn(0,session('appName')."下已有同名的模块，不允许重复录入", "success:false");
        }
        $data['desc'] = $module['desc'];
        $ret = D('Module')->add($data);
        if (!$ret) {
            $this->ajaxReturn(0, "新增模块数据失败！", "success:false");
        }
        //关联App插入
        $module['addedApps'] = $_POST['addedApps'];
        $apps = explode(' ',$module['addedApps']);
        $module['apps']=array();
        for($i=0; $i<count($apps)-1; $i++) {
            $module['apps'][$i] = D('App')->getIdByAppName($apps[$i]);
        }
        for($i=0; $i<count($module['apps']); $i++) {
            //判断module名是否重复（同一app下不能重复）
            if($module['apps'][$i]!=$currnet_appId) {
                $condition['name'] = $module['name'];
                $condition['appId'] = $module['apps'][$i];
                $condition['status'] = C('VALID');
                $temp = D('Module')->checkDuplicate($condition);
                if (!empty($temp)) {
                    $this->ajaxReturn(0, $apps[$i] . "下已有同名的模块，不允许重复录入", "success:false");
                }
                $data['name'] = $module['name'];
                $data['desc'] = $module['desc'];
                $data['appId'] = $module['apps'][$i];
                $ret = D('Module')->add($data);
                if (!$ret) {
                    $this->ajaxReturn(0, "新增模块数据失败！", "success:false");
                }
            }
        }
            $this->ajaxReturn($ret, "成功新增模块数据！", "success:true");
    }

    /**
     * 修改module
     */
    public function update(){
        $id = $this->_get('id');
        $ret = D('Module')->getById($id);
        if(empty($ret)){
            $this->error("ID" . $id . "对应的Module不存在！！！", "mlist", 2);
        }
        $this->assign('module',$ret);

        //根据当前用户id查询相关app信息
        $this->assign("currentApp",$_SESSION['appName']);
        $this->display();
    }

    /**
     * 执行修改module操作
     */
    public function doUpdate() {
        $module = D('Module')->create();
        if (!$module) {
            $this->ajaxReturn(null, D('Module')->getError(), 'success:false');
        }

        //判断传入参数是否符合格式
        $module = D('Module')->checkModule($module);

        $module['appId'] = $_SESSION['appId'];;

        //查重条件
        $condition['id'] = array('NEQ', $module['id']);
        $condition['name'] = $module['name'];
        $condition['appId'] = $module['appId'];
        $condition['status'] = C('VALID');
        $temp = D('Module')->checkDuplicate($condition);
        if (!empty($temp)) {
            $this->ajaxReturn(0, "已经有相同的module存在，不允许重复录入", "success:false");
        }

        //执行更新
        $data['status'] = C('INVALID');
        $where['id'] = $module['id'];
        $temp = D('Module')->where($where)->save($module);
        if ($temp == false) {
            $this->ajaxReturn(0,"更新失败", "success:false");
        }
        $this->ajaxReturn($temp, "成功更新模块数据！", "success:true");
    }

    /**
     * 删除module--软删除
     */
    public function delete(){
        $id = $this->_post('ids');
        $ids = explode(",", $id);
        if (empty($ids)) {
            $this->ajaxReturn(0, "请选择待删除的Module。", "success:false");
        }

        $condition['status'] = C('VALID');
        $condition['appId'] = $_SESSION['appId'];
        foreach( $ids as $id ) {
            //查找模块下的api和用例
            $condition['mid'] = $id;
            $apis = D('Api')->where($condition)->getField('id', true);
            $cases = D('Case')->where($condition)->getField('id', true);

            if(!empty($apis) || !empty($cases)){
                $this->ajaxReturn($id, "删除模块[$id]失败,该模块下有api或者用例！","success:false");
            }

            //删除模块
            $where['id'] = $id;
            $data['status'] = C('INVALID');
            $ret = D('Module')->where($where)->save($data);
            if (empty($ret)) {
                $this->ajaxReturn($ret, "删除Module失败！", "success:false");
            }
        }
        $this->ajaxReturn(null, "成功删除Module。", "success:true");
    }
}