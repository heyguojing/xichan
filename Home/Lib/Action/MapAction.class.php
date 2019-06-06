<?php

// 本类由系统自动生成，仅供测试用途
class MapAction extends CommonAction {
	
    public function index() {
		$adlist = M('ad')->where('cid=1')->select();
		$form = M('informaction');
		$list = $form->order('id asc')->select();
		$this->assign('list',$list);	
    	$this->display();
    }
    
}