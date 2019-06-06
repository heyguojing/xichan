<?php
class FormyyAction extends CommonAction {
		public function index(){
		//项目列表
		$M = M("Form");
		if(!empty($_GET['id'])){
		   $wheres['id'] = $_GET['id'];
		   $classone = M('Form')->where($wheres)->find();
		      
		  }	
        $count = $M->where($wheres)->count();
        import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 15);
        $showPage = $page->show();
        $list = $M->where($wheres)->field("`id`,`name`,`sexy`,`telphone`,`place`,`content`,`time`")->order("id desc")->limit($page->firstRow, $page->listRows)->select();
        foreach ($cidArr as $k => $v) {
            $cids[$v['id']] = $v;
        }
        unset($cidArr);
        $this->assign("page", $showPage);
        $this->assign("list", $list);
        $this->display();
		echo('THINK_VERSION');die;
		}
		public function submit() {
		$id = $_GET['id'];
		if(count(explode(',',$id))>1){
			 $where = 'id in('.$id.')';  
			 }
		  else{     
		     $where = 'id='.$id; 
			 }   
		  $list=M("Form")->where($where)->delete();   
		  if($list!==false) {     
		    echo json_encode(array('status' => 1, 'info' => "成功删除{$list}条",'url'=>U('Form/index'))); 
			}else{    
			echo json_encode(array('status' => 0, 'info' => "删除失败" ));  
		    } 
    }
}
?>