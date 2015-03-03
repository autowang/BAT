<?php

class ApiModel extends CommonModel {
	
	protected $headKeys = array('X-Session-Token','Content-Type','Accept-Language','Cache-Control','Accept-Encoding','User-Agent','Accept');

	protected $_auto = array (
		array('appId','getAppId',1,'function'),
		array('updateTime','getCurrentTime',3,'function'),
		);

	protected $_validate = array(
		array('requestContent','require','请求包内容不能为空'),
		array('mid','require','请指定API的所属模块'),
		array('desc','require','描述内容不能为空'),
		);

	protected $_map = array(
		'apiId' => 'id',
		'apiDesc'  => 'desc',
		'request' =>  'requestContent',
		);


	/*
	 * 检查Api是否已经存在
	 */	
	public function checkDuplicate($condition){
		return M('Api')->where($condition)->find();
	}

	/*
	 * 解析请求包
	 */
	public function parseRequestHeader ($data) {
		$data = A('Api')->merge($data);
		$lines = explode("\n",$data['requestContent']);
		if (array_search("\r", $lines) === false){
			return false;
		}
		
		
		//http请求头信息也需要解析存储
 		$ret['header'] = $this->httpParseHeaders($data['requestContent']);
 		
		$arr = explode(" ",trim($lines[0]));
		$ret['method'] = $arr[0]; 
		//EG:GET http://ptcms.csdn.net/comment/comment/hot?pagesize=50&test=123
		$url = parse_url(trim($arr[1]));
		$ret['protocol'] = $url['scheme'];
		$ret['host'] = $url['host'];
        $ret['port'] = is_null($url['port']) ? 80 : $url['port'];
        $ret['path'] = $url['path'];
        //参数列表暂时先返回字符串吧。。。
        if ($ret['method'] == 'GET'){
        	$data['params'] = $url['query'];
        }else{
        	$data['params'] = $lines[array_search("\r", $lines) + 1];
        }
		if (in_array(null, $ret)) {
			return false;
		}
		return array_merge($data,$ret);
	}
	
	//异步获取api数据方法-返回渲染之后的table
	public function getApis($condition){
		$condition['status'] = C('VALID');
		$condition['appId'] = getAppId();
		$apis = M('Api')->where($condition)->order('createTime desc')->limit(9)->select();
		if (empty($apis)){
			return false;
		}
		return $apis;
	}
	
	//解析HTTP headers
	public function httpParseHeaders($string){
		
		$headers = array();
		foreach (explode("\n", $string) as $i => $h) {
			$h = explode(':', $h, 2);
			
			if (isset($h[1]) && in_array($h[0], $this->headKeys)) {
				$headers[$h[0]] = trim($h[1]);
			}
		}
		return json_encode($headers,JSON_UNESCAPED_SLASHES);
	}
	
	/**
	 * 检查该api是否被引用
	 * @param unknown $ids
	 */
	public function isUsing($ids){
		$ids = explode(",", $ids);
		$cids = array();//存放有引用关系的用例id
		foreach ($ids as $id){
			$condition['content'] = array('like',"%id\":\"$id%");
			$condition['type'] = CaseModel::HTTP;
			$steps = D("Step")->where($condition)->field("caseId")->select();
			//如果返回结果为空，说明没引用，直接返回
			if(empty($steps)){
				break;
			}
			foreach ($steps as $step){
				$ret = D("Case")->isValid($step['caseId']);
				if($ret){
					$cids[] = $step['caseId'];
				}
			} 
		}
		return array_unique($cids);
	}
	
	/**
	 * 格式化参数
	 */
	public function formatParams($str){
		$params = array();
		if(strlen($str) > 0){
			$pairs = explode("&", $str);
			foreach ($pairs as $p){
				$temp = explode("=", $p);
				$params[$temp[0]] = $temp[1];
			}
		}
		return $params;
	}
	
	public function getApi($where='1=1'){
		return $this->where($where)->select();
	}
}

?>