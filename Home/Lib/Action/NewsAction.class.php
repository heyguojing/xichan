<?php
class NewsAction extends CommonAction{
	
	public function index(){
		if ($_GET['id']){
        	$wheres['cid'] = $_GET['id'];
			$ex = M('category')->where('cid='.$_GET['id'])->find();
        }
		else{
			$ex = M('category')->limit(0,1)->find();
			$wheres['cid'] = $ex['cid'];
			
		}
        $wheres['status'] = 1;
		$Form   =   M('News');
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
        $categorylist = M('Category')->getField('cid,name');
		$cooktype = cookie('news');
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
        $category = M('category')->order('cid asc')->select();
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
		
		M('News')->where($where)->setInc('reshow'); // 浏览量+1
		
		$info = M('News')->where($where)->find();
		
		if (!$info)$this->error('信息不存在...');
		
		$categoryname = M('Category')->where('id='.$info['cid'].'')->find();
		
		$info['category'] = M('Category')->where('cid='.$info['cid'].'')->find();
		$cooktype = cookie('news');
		$cookistr=explode(',',$cooktype);
		foreach ($cookistr as $li=>$ls){
			if($info['id']==$ls){
				$info['hover'] = "hover";
				}
			}
		$lovecount = M('newslove')->where('prid='.$info['id'])->count();
		$this->assign('lovecount',$lovecount);	
		$lovelist = M('newslove')->where('prid='.$info['id'])->select();
		$this->assign('lovelist',$lovelist);
		$cat = M('Category')->where('cid='.$info['cid'])->find();
		$this->assign('cat',$cat);
		$this->assign('info', $info);
		
		$this->display();
	}
	 public function zhan(){
		   $M=M('News');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('news');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('news',$id);
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
		   $M=M('News');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('news');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('news',$id);
		   $ad['id']=$info['id'];
		   $ad['views'] = $info['views']+1;
			if ($M->save($ad)) {
				$back = $M->where($where)->find();
				echo json_encode(array("status" => 1, "info" => "".$back['views'].""));
			} else {
				echo json_encode(array("status" => 0, "info" => "点赞失败"));
			}
		}
	public function infonews(){
		   $M=M('News');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('infonews');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('infonews',$id);
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