<?php

class MemberAction extends CommonAction {

    public function index() {
		$M = M('member');
		$count = $M->count();
        import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 20);
        $showPage = $page->show();
		$this->assign('page',$showPage);
		$list = $M->order('uid desc')->limit($page->firstRow, $page->listRows)->select();
		$sexArr = array("女", "男");
		foreach ($list as $k=>$v){
			$list[$k]['sex'] = $sexArr[$v['sex']];
			}
		$this->assign('list',$list);
        $this->display();
    }
    public function dingdan(){
		$M = M('pay');
		$count = $M->count();
        import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 20);
        $showPage = $page->show();
		$this->assign('page',$showPage);
		$list = $M->order('id desc')->limit($page->firstRow, $page->listRows)->select();
		$yesArr = array("未支付", "已支付");
		foreach ($list as $k=>$v){
			$info = M('project')->where('id='.$v['projectid'])->find();
			$list[$k]['title'] = $info['title'];
			$list[$k]['yes'] = $yesArr[$v['yes']];
			}
		$this->assign('list',$list);
        $this->display();
		}
		
	public function submit() {
		
		$id = $_POST['dell'];
		if(is_array($id)){     
		     $where = 'id in('.implode(',',$id).')';
		}
		else{     
		     $where = 'id='.$id;   
		}  
			  //dump($where);   
		 $list=M('member')->where($where)->delete();   
		  if($list!==false) {     
		    echo json_encode(array('status' => 1, 'info' => "成功删除{$list}条" ,'url' => U('Member/index'))); 
			}else{    
			echo json_encode(array('status' => 0, 'info' => "删除失败" ));  
		    } 
    }
	
	 public function del() {
		$temp = M("member")->where("id=" . (int) $_GET['id'])->find();
    	
        if (M("member")->where("id=" . (int) $_GET['id'])->delete()) {
        	  echo json_encode(array("status" => 1, "info" => "删除成功",'url' => U('Member/index')));
        } else {
            echo json_encode(array("status" => 0, "info" => "删除失败，可能是不存在该ID的记录"));
        }
	}	
	
	public function dingdansubmit() {
		
		$id = $_POST['dell'];
		if(is_array($id)){     
		     $where = 'id in('.implode(',',$id).')';
		}
		else{     
		     $where = 'id='.$id;   
		}  
			  //dump($where);   
		 $list=M('pay')->where($where)->delete();   
		  if($list!==false) {     
		    echo json_encode(array('status' => 1, 'info' => "成功删除{$list}条" ,'url' => U('Member/dingdan'))); 
			}else{    
			echo json_encode(array('status' => 0, 'info' => "删除失败" ));  
		    } 
    }
	
	 public function dingdandel() {
		$temp = M("pay")->where("id=" . (int) $_GET['id'])->find();
        if (M("pay")->where("id=" . (int) $_GET['id'])->delete()) {
        	  echo json_encode(array("status" => 1, "info" => "删除成功",'url' => U('Member/dingdan')));
        } else {
            echo json_encode(array("status" => 0, "info" => "删除失败，可能是不存在该ID的记录"));
        }
	}		
}