<?php
/**
* 通用公共类
*/
class PublicAction extends Action
{
	public function imgCode(){
		import('ORG.Util.Image');
		Image::buildImageVerify(1,1,'png',20,30,"imgCode");
	}	
}