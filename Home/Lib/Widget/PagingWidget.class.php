<?php
class PagingWidget extends Widget{
	public function render($data){
		return $this->renderFile('paging',$data);
	}
}