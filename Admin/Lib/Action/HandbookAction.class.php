<?php
class HandbookAction extends CommonAction {
		public function index(){
			$list = D('handbook')->order('paixu asc,id desc')->select();
			$this->assign('list',$list);
			$this->display();
		}
		
		public function add(){
			$m = M('handbook');
			if(IS_POST){
				$info  = $_POST['info'];
				$info['published'] = time();
				if($m->where("name ='{$info['name']}' and status = 1")->find()){
					echo json_encode(array('status'=>0 , 'info'=>'此手册信息已经存在，请稍后再试!'));exit;
				}
				if($m->add($info)){
					echo json_encode(array('status'=>1 , 'info'=>'手册信息发布成功!','url'=>'index'));exit;
				}else{
					echo json_encode(array('status'=>0 , 'info'=>'手册信息发布失败，请稍后再试!'));exit;
				}
			}else{
				$paixu = $m->field('paixu')->order('paixu desc')->find();
				$paixu['paixu'] = $paixu['paixu'] + 1;
				$titlename = '添加编辑手册信息';
				$this->assign('info',$paixu);
				$this->assign('titlename',$titlename);
				$this->display();
			}
		}
		
		public function edit(){
			$m = M('handbook');
			if(IS_POST){
				$info  = $_POST['info'];
				if($m->save($info)){
					echo json_encode(array('status'=>1 , 'info'=>'手册信息修改成功!','url'=>'index'));exit;
				}else{
					echo json_encode(array('status'=>0 , 'info'=>'手册信息修改失败，请稍后再试!'));exit;
				}
			}else{
				$titlename = '添加编辑手册信息';
				$this->assign('titlename',$titlename);
				$this->assign('info',$m->where("id = {$_GET['id']}")->find());
				$this->display('add');
			}
		}
		
		
		
		public function zongpai(){
		//$refeurl = $_SERVER['HTTP_REFERER'];
		//$paiid = $_POST['t_id'];
		$id=$_GET['id'];
		$paixu = $_GET['paixu'];
		if(empty($id)){
			echo json_encode(array("status"=>0,'info'=>"无修改，不用提交"));
			}
		else{	
		$arrid = explode(',',$id);
		$arrpaixu = explode(',',$paixu);
		foreach ($arrid as $k=>$v){
			M("Expert")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			}
		echo json_encode(array("status"=>1,'info'=>"排序成功"));	
		  }
		}	
		public function submit() {
			$id = $_GET['id'];
			if(count(explode(',',$id))>1){
				 $where = 'id in('.$id.')';  
				 }
			  else{     
			     $where = 'id='.$id; 
				 }   
			  $list=M("handbook")->where($where)->delete();   
			  if($list!==false) {     
			    echo json_encode(array('status' => 1, 'info' => "成功删除{$list}条",'url'=>'index')); 
				}else{    
				echo json_encode(array('status' => 0, 'info' => "删除失败" ));  
			    } 
	    }					
		public function paixu(){
			  $id=$_GET['id'];
			  $paixu = $_GET['paixu'];
			  if(empty($id)){
				  echo json_encode(array("status"=>0,'info'=>"无修改，终止提交"));
				  }
			  else{	
			  $arrid = explode(',',$id);
			  $arrpaixu = explode(',',$paixu);
			  foreach ($arrid as $k=>$v){
				  M("handbook")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
				  }
			  echo json_encode(array("status"=>1,'info'=>"排序成功"));	
				}
			}
			
	}
?>