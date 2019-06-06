<?php

// 叫兽，砖家页
class ExpertAction extends CommonAction {
	
    public function index(){
		if ($_GET['id']){
        	$wheres['cid'] = $_GET['id'];
			$ex = M('expertcategory')->where('cid='.$_GET['id'])->find();
        }
		else{
			$ex = M('expertcategory')->limit(0,1)->find();
			$wheres['cid'] = $ex['cid'];
			
		}
        $wheres['status'] = 1;
		$Form   =   M('expert');
		$count = $Form->where($wheres)->count();
        import("@.ORG.Page");
        $page = new Page($count, 8);
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
		$categorylist = M('expertcategory')->getField('cid,name');
		$cooktype = cookie('expert');
		$cookistr=explode(',',$cooktype);
		foreach($list as $ks=>$vs){
			    $list[$ks]['guoji'] = empty($vs['gouji'])?'':'('.$vs['gouji'].')';
			    $list[$ks]['category'] = $categorylist[$vs['cid']];	
				$list[$ks]['liclass'] = 'zhan';
			    $list[$ks]['hover'] = "";
			    foreach ($cookistr as $li=>$ls){
				  if($vs['id']==$ls){
					  $list[$ks]['hover'] = "hover";
					  $list[$ks]['liclass'] = 'hodden';
					}
				}
			}
        $category = M('expertcategory')->order('cid asc')->select();
		foreach ($category as $k=>$v){
			if($k==0){
				$category[$k]['css'] = ' class="first"';
				}
			elseif($k==2){
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
		$where['id'] = $this->_get('id');
		$where['status'] = 1;
		M('expert')->where($where)->setInc('reshow'); // 浏览量+1
		$info = M('expert')->where($where)->find();
		if(empty($info['gouji'])){
			$info['guoji'] = '';
			}
		else{
			$info['guoji'] = '('.$info['guoji'].')';
			}	
		$info['description']=str_replace("<br>","</li><li>",$info['description']);
		$cooktype = cookie('expert');
		$cookistr=explode(',',$cooktype);
		foreach ($cookistr as $li=>$ls){
			if($info['id']==$ls){
				$info['hover'] = "hover";
				}
			}
		$cat = M('expertcategory')->where('cid='.$info['cid'])->find();
		$hotlist = M('project')->where("id<>".$this->_get('id'))->limit(0,16)->select();
		$lovecount = M('lovecount')->where('prid='.$info['id'])->count();
		$this->assign('lovecount',$lovecount);
		$lovelist = M('expertlove')->where('prid='.$info['id'])->order('paixu asc,id desc')->select();
		$this->assign('lovelist',$lovelist);
		$this->assign('hotlist',$hotlist);
		$this->assign('cat',$cat);
		$this->assign('info',$info);
		$this->display();
	}
     public function zhan(){
		   $M=M('expert');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('expert');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('expert',$id);
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
		   $M=M('expert');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('expert');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('expert',$id);
		   $ad['id']=$info['id'];
		   $ad['views'] = $info['views']+1;
			if ($M->save($ad)) {
				$back = $M->where($where)->find();
				echo json_encode(array("status" => 1, "info" => "".$back['views'].""));
			} else {
				echo json_encode(array("status" => 0, "info" => "点赞失败"));
			}
		}
   public function infoexpert(){
		   $M=M('expert');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('infoexpert');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('infoexpert',$id);
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