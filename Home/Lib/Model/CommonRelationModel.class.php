<?php
class CommonRelationModel extends RelationModel{
	
	/**
	 * 写入文件到指定的地方
	 */
	public function strToFile($path,$file,$content){
		if (!file_exists($path)){
			mkdir($path,0777,true);
		}
		$file = $path.$file;
		$handle = fopen($file, "w+");
		if(!$handle){
			return false;
		}
		if(!flock($handle,LOCK_EX)){
			unlink($file);
			return false;
		}
		if (!fwrite($handle,$content)){
			unlink($file);
			return false;
		}
		flock($handle,LOCK_UN);//释放锁定
		fclose($handle);
		return true;
	}
}