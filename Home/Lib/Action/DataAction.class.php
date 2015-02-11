<?php
/**
* 测试数据管理action
*/
class DataAction extends CommonAction
{

	public function index(){
		$this->display();
	}

	/**
	 * 测试数据list
	 */
	public function dlist(){
		//查询条件准备
		$id = $this->_get('id');
		if(!empty($id)){
			$condition['id'] = trim($id);
		}else{
			
			$key = $this->_get('key');
			if(!empty($key)){
				$condition['key'] = array('LIKE',"%".trim($key)."%");
			}
			
			$value = $this->_get('value');
			if(!empty($value)){
				$condition['value'] = array('LIKE',"%".trim($value)."%");
			}
		}
		
		$condition['appId'] = getAppId();
		//分页
		$page = $this->getPaging('Data', $condition);
		$paging = $page->show();
		$this->assign('paging',$paging);
		$this->assign('searchKeys',$searchKeys);
		$datas = D('Data')->where($condition)->order('updateTime desc')->limit($page->firstRow.','.$page->listRows)->select();
		
		$this->assign("testData",$datas);
		$this->display();
	}
	
	public function add(){
		$this->display();
	}
	
	/**
	 * 执行新增操作
	 */
	public function doAdd(){
		$data = D('Data')->create($_POST);
		if(!$data){
			$this->error(D('Data')->getError(),'add',2);
		}
		$where['appId'] = getAppId();
		$where['key'] = $data['key'];
		$ret = D("Data")->getData($where);
		if ($ret){
			$this->error("key[".$data['key']."]已存在，不可重复定义！","add",2);
		}
		$file = $_FILES;
		if($file['upload_file']['error'] == 0){
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();
			$upload->saveRule = '';
			$path = UPLOAD_PATH.getAppId()."/";
			if (!file_exists($path)){
				mkdir($path,0777,true);
			}
			$upload->savePath = $path;
			if(!$upload->upload()){
				$this->error($upload->getErrorMsg(),__URL__.'add',2);
			}
			$data['value'] = $path.$file['upload_file']['name'];
		}
		if(D('Data')->add($data)){
			$this->redirect("Data:dlist");
		}else{
			$this->error('新增测试数据失败','dlist',2);
		}
	}
	
	public function update(){
		$id = $this->_get('id');
		$ret = D('Data')->getById($id);
		if(!$ret){
			$this->error("id=$id的记录不存在！",'dlist',2);
		}
		$this->assign("data",$ret);
		$this->display();
	}
	
	/**
	 * 执行数据添加和更新操作
	 */
	public function doUpdate(){	
		$data = D('Data')->create();
		if(!$data){
			$this->error(D('Data')->getError(),"/update?id=".$data['id'],2);
		}
		$where['appId'] = getAppId();
		$where['key'] = $data['key'];
		$where['id'] = array('neq',$data['id']);
		$ret = D("Data")->getData($where);
		if ($ret){
			$this->error("key[".$data['key']."]已存在，不可重复定义！","update?id=".$data['id'],2);
		}
		$file = $_FILES;
		if($file['upload_file']['error'] == 0){
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();
			$upload->saveRule = '';
			$path = UPLOAD_PATH.getAppId()."/";
			if (!file_exists($path)){
				mkdir($path,0777,true);
			}
			$upload->savePath = $path;
			if(!$upload->upload()){
				$this->error($upload->getErrorMsg(),__URL__."/update?id=".$data['id'],2);
			}
			$data['value'] = $path.$file['upload_file']['name'];
		}
		
		$ret = D('Data')->save($data);
		if ($ret === false) {
			$this->error("更新数据失败！","/update?id=".$data['id'],2);
		}
		$this->redirect("dlist");
	}

	/**
	 * 删除Data
	 */
	public function delete(){
		$ids = $this->_post("ids");
		if (empty($ids)) {
			$this->ajaxReturn(0,"请选择待删除的Data。","success:false");
		}
		$ret = D('Data')->isUsing($ids);
		if(!empty($ret)){
			$ids = implode(",", $ret);
			$this->ajaxReturn($ids,"删除的数据与用例[$ids]存在引用关系，不能删除！",'success:false');
		}
		$condition["id"] = array("in", $ids);
		$ret = D('Data')->where($condition)->delete();
		if ($ret) {
			$this->ajaxReturn($ret,"成功删除Data。","success:true");
		} else {
			$this->ajaxReturn($ret,"删除Data失败！","success:false");
		}
	}
	
	/**
	 * 获取与输入key相关的推荐
	 */
	public function keys(){
		$key = $this->_get("key");
		$condition['key'] = array('like','%'.$key.'%');
		$condition['appId'] = getAppId();
		$keys = M("Data")->where($condition)->field('key')->select();
		$temp = array();
		foreach ($keys as $key){
			$temp[] = $key['key'];
		}
		$this->ajaxReturn(json_encode($temp),'成功','success:true');
	}
}