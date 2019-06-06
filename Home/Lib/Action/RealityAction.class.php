<?php
class RealityAction extends CommonAction{
	
	public function index(){
		$Form   =   M('reality');
		$wheres['status'] = 1;
		$count = $Form->where($wheres)->count();
        import("@.ORG.Page");
        $page = new Page($count, 7);
        $showPage = $page->showas();
        if(empty($showPage)){
			$this->assign('pa','0');
			$this->assign("page", $showPage);
			}
		else{	
		    $this->assign('pa','1');
		    $this->assign("page", $showPage);
		}
        $list = $Form->where($wheres)->order("paixu asc,published DESC")->limit($page->firstRow, $page->listRows)->select();
		$cooktype = cookie('reality');
		$cookistr=explode(',',$cooktype);
		foreach ($list as $k=>$v){
			$list[$k]['liclass'] = 'zhan';
			$list[$k]['hover'] = "";
			foreach ($cookistr as $li=>$ls){
				if($v['id']==$ls){
					$list[$k]['hover'] = "hover";
					$list[$k]['liclass'] = 'hodden';
					}
				}
        }
		$this->assign('list', $list);
		$adbanerlist = M('ad')->where('cid=5')->limit(0,1)->find();
		$this->assign('adbanerlist',$adbanerlist);
		$this->display();
	}
	
	public function info(){
		$where['id'] = $this->_get('id');
		$where['status'] = 1;
		M('reality')->where($where)->setInc('reshow'); // 浏览量+1
		$info = M('reality')->where($where)->find();
		if (!$info)$this->error('信息不存在...');
		$xiangmustr=explode(',',$info['xiangmu']);
		$zhuanjiastr=explode(',',$info['zhuanjia']);	
		foreach ($xiangmustr as $k=>$v){
			$slist[$k] = M("project")->where('id='.$v)->field("`id`,`title`")->find();
		  }
		$info['xiangmu'] = $slist;
		unset($slist);
		foreach ($zhuanjiastr as $k=>$v){
			$info_expert[$k] = M('expert')->where('id='.$v)->field("`id`,`title`")->find();
			  }
		$info['zhuanjia'] = $info_expert;
		unset($xiang);	 
		$cooktype = cookie('reality');
		$cookistr=explode(',',$cooktype);
		foreach ($cookistr as $li=>$ls){
			if($info['id']==$ls){
				$info['hover'] = "hover";
				}
			}
		if(empty($info['video'])){
			$info['videoty'] = 0;
			$info['video'] = '';
			}
		else{
			$info['videoty'] = 1;
			}	
		$lovecount = M('lovecount')->where('prid='.$info['id'])->count();
		$this->assign('lovecount',$lovecount);	
		$lovelist = M('realitylove')->where('prid='.$info['id'])->order('paixu asc,id desc')->select();
		$this->assign('lovelist',$lovelist);	
		$this->assign('info', $info);
		$this->display();
	}
	 public function zhan(){
		   $M=M('reality');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('reality');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('reality',$id);
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
		   $M=M('reality');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('reality');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('reality',$id);
		   $ad['id']=$info['id'];
		   $ad['views'] = $info['views']+1;
			if ($M->save($ad)) {
				$back = $M->where($where)->find();
				echo json_encode(array("status" => 1, "info" => "".$back['views'].""));
			} else {
				echo json_encode(array("status" => 0, "info" => "点赞失败"));
			}
		}
	public function inforeality(){
		   $M=M('reality');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('inforeality');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('inforeality',$id);
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