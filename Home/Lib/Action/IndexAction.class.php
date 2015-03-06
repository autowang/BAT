<?php
/**
 * 首页类
 */
class IndexAction extends CommonAction {
    public function index(){
    	$where['appId']=getAppId();
    	$users = D('UserApp')->where($where)->select();
    	
    	$where['status'] = C('VALID');
    	$totalApi = D('Api')->where($where)->count();
    	$totalCase = D('Case')->where($where)->count();
    	
    	$data = array();
    	foreach ($users as $user){
    		$where['userId'] = $user['userId'];
    		$temp['username'] = getUserName($user['userId']);
    		$temp['apiCount'] = D('Api')->where($where)->count();
    		$temp['caseCount'] = D('Case')->where($where)->count();
    		$data[] = $temp;
    	}
    	$this->assign('totalApi',$totalApi);
    	$this->assign('totalCase',$totalCase);
    	$this->assign("data",$data);
    	$this->display();
    }
}