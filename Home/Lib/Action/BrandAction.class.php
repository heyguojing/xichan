<?php
class BrandAction extends CommonAction{
	
	public function index(){
		if ($_GET['id']){
        	$wheres['cid'] = $_GET['id'];
			$ex = M('brandcategory')->where('cid='.$_GET['id'])->find();
        }
		else{
			$ex = M('brandcategory')->limit(0,1)->find();
			$wheres['cid'] = $ex['cid'];
			
		}
        $wheres['status'] = 1;
		
		$Form   =   M('brand');
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
        $categorylist = M('brandcategory')->getField('cid,name');
        $cooktype = cookie('brand');
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
        $category = M('brandcategory')->order('cid asc')->select();
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
		
		M('brand')->where($where)->setInc('reshow'); // 浏览量+1
		$info = M('brand')->where($where)->find();
		
		if (!$info)$this->error('信息不存在...');
		
		$categoryname = M('brandcategory')->where('id='.$info['cid'].'')->find();
		
		$info['category'] = M('brandcategory')->where('cid='.$info['cid'].'')->find();
		$lovelist = M('brandlove')->where('prid='.$info['id'])->select();
		$this->assign('lovelist',$lovelist);
		$cat = M('brandcategory')->where('cid='.$info['cid'])->find();
		$this->assign('cat',$cat);
		$this->assign('info', $info);
		
		$this->display();
	}
	
		 public function zhan(){
		   $M=M('brand');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('brand');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('brand',$id);
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
		   $M=M('brand');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('brand');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('brand',$id);
		   $ad['id']=$info['id'];
		   $ad['views'] = $info['views']+1;
			if ($M->save($ad)) {
				$back = $M->where($where)->find();
				echo json_encode(array("status" => 1, "info" => "".$back['views'].""));
			} else {
				echo json_encode(array("status" => 0, "info" => "点赞失败"));
			}
		}
		public function infobrand(){
		   $M=M('brand');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('infobrand');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('infobrand',$id);
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