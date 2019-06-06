<?php

// 本类由系统自动生成，仅供测试用途
class TopicsAction extends CommonAction {
	
    public function index() {
    if ($_GET['id']){
        	$wheres['cid'] = $_GET['id'];
			$ex = M('topicscategory')->where('cid='.$_GET['id'])->find();
        }
		else{
			$ex = array('cid'=>0,'name'=>'全部');
			//$wheres['cid'] = $ex['cid'];	
		}
        $wheres['status'] = 1;
		$Form   =   M('topics');
		$count = $Form->where($wheres)->count();
        import("@.ORG.Page");
        $page = new Page($count, 5);
        $showPage = $page->showas();
        if(empty($showPage)){
			$this->assign('pa','0');
			$this->assign("page", $showPage);
			}
		else{	
		    $this->assign('pa','1');
		    $this->assign("page", $showPage);
		}
        $list = $Form->where($wheres)->limit($page->firstRow, $page->listRows)->order('paixu asc,id desc')->select();
        $categorylist = M('topicscategory')->getField('cid,name');
		$cooktype = cookie('topics');
		$cookistr=explode(',',$cooktype);
        foreach ($list as $k=>$v){
        	$list[$k]['category'] = $categorylist[$v['cid']];
			$list[$k]['liclass'] = 'zhan';
			$list[$k]['hover'] = "";
			foreach ($cookistr as $li=>$ls){
				if($v['id']==$ls){
					$list[$k]['hover'] = "hover";
					$list[$k]['liclass'] = 'hodden';
					}
				}
        }
        $category = M('topicscategory')->order('cid asc')->select();
		foreach ($category as $k=>$v){
			if($k==2){
				$category[$k]['css'] = ' class="last"';
				}
		    else{
				$category[$k]['css'] = '';
				}	
			}
        $this->assign('category',$category);
		$this->assign('ex',$ex);
		$this->assign('list', $list);
		$this->display();
	}
	
	public function info(){
		$where['id'] = $_GET['id'];
		M('topics')->where($where)->setInc('reshow'); // 浏览量+1//
		$info = M('topics')->where($where)->find();
		if (!$info)$this->error('信息不存在...');
		
		$this->assign('info', $info);
		$this->display();
	}
	 public function zhan(){
		   $M=M('topics');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('topics');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('topics',$id);
		   $ad['id']=$info['id'];
		   $ad['views'] = $info['views']+1;
			if ($M->save($ad)) {
				$back = $M->where($where)->find();
				echo json_encode(array("status" => 1, "info" => "".$back['views'].""));
			} else {
				echo json_encode(array("status" => 0, "info" => "点赞失败"));
			}
		}
	public function zhans(){
		   $M=M('topics');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('indextopics');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('indextopics',$id);
		   $ad['id']=$info['id'];
		   $ad['views'] = $info['views']+1;
			if ($M->save($ad)) {
				$back = $M->where($where)->find();
				echo json_encode(array("status" => 1, "info" => "".$back['views'].""));
			} else {
				echo json_encode(array("status" => 0, "info" => "点赞失败"));
			}
		}
}