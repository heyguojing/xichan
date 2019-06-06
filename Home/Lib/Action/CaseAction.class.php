<?php

// 本类由系统自动生成，仅供测试用途
class CaseAction extends CommonAction {
	
    public function index(){
		
		if ($_GET['id']>0){
        	$wheres['cid'] = $_GET['id'];
			$ex = M('casecategory')->where('cid='.$_GET['id'])->find();
        }else{
			$wheres['hot'] = 1;
			}
        $wheres['status'] = 1;
		$Form   =   M('case');
		$count = $Form->where($wheres)->count();
        import("@.ORG.Page");
        $page = new Page($count, 10);
        $showPage = $page->showas();
        if(empty($showPage)){
			$this->assign('pa','0');
			$this->assign("page", $showPage);
			}
		else{	
		    $this->assign('pa','1');
		    $this->assign("page", $showPage);
		}
		$this->assign('totalPages',$page->totalPages);
        $list = $Form->where($wheres)->limit($page->firstRow, $page->listRows)->order('paixu asc,id desc')->select();
        $categorylist = M('casecategory')->getField('cid,name');
        foreach ($list as $k=>$v){
			$cookistr=explode(';',$v['project']);
			   foreach ($cookistr as $sd=>$sid){
				   $str=explode(',',$sid);
					$projecttitle .= '<a href="'.$str[1].'">'.$str[0].'</a>';
				}
			$list[$k]['projecttitle'] = $projecttitle;	
			unset($cookistr);
			unset($projecttitle);
        	$list[$k]['category'] = $categorylist[$v['cid']];
			
        }
        $category = M('casecategory')->order('cid asc')->select();
        $this->assign('category',$category);
		$this->assign('ex',$ex);
		$this->assign('list', $list);
		$this->display();
	}
	
	public function info(){
		$where['id'] = $this->_get('id');
		$where['status'] = 1;
		M('case')->where($where)->setInc('reshow'); // 浏览量+1
		$info = M('case')->where($where)->find();
		
		if (!$info)$this->error('信息不存在...');
		
		$categoryname = M('casecategory')->where('id='.$info['cid'].'')->find();
		
		$info['category'] = M('casecategory')->where('cid='.$info['cid'].'')->find();
		 $cookistr=explode(';',$info['project']);
			   unset($info['project']);
			   $project=array();
			   foreach ($cookistr as $sd=>$sid){
				   $str=explode(',',$sid);
					$project[$sd]['title'] = $str[0];
					$project[$sd]['url'] = $str[1];
				}
			  $info['project'] = $project;
		$cat = M('casecategory')->where('cid='.$info['cid'])->find();
		$lovelist = M('caselove')->where('prid='.$info['id'])->order('paixu asc,id desc')->select();
		$this->assign('lovelist',$lovelist);
		$category = M('casecategory')->order('cid asc')->select();
        $this->assign('category',$category);
		$this->assign('cat',$cat);
		$this->assign('info', $info);
		
		$this->display();
	}
	 public function zhan(){
		   $M=M('case');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
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