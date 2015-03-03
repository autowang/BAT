<?php
/**
 * 首页类
 */
class IndexAction extends CommonAction {
    public function index(){
    	$where['appId']=getAppId();
    	$users = D('UserApp')->where($where)->select();
    	$data = array();
    	foreach ($users as $user){
    		$where['userId'] = $user['userId'];
    		$where['status'] = C('VALID');
    		$temp['username'] = getUserName($user['userId']);
    		$temp['apiCount'] = D('Api')->where($where)->count();
    		$temp['caseCount'] = D('Case')->where($where)->count();
    		$data[] = $temp;
    	}
    	$this->assign("datas",$data);
    	$this->display();
    }
}