<?php
class DiscountAction extends CommonAction{
	
	public function index(){
		$where = 'status=1';
		if($_GET['id']==1){
			$where .= ' and tuangou=1';
			$tuangouid=1;
			}
		elseif($_GET['id']==2){
			$where .=' and tuangou=2';
			$tuangouid=2;
			}	
		else{
			$where .=' and tuangou=1';
			$tuangouid=1;
			}
		$this->assign('tuangouid',$tuangouid);
		$form = M('youhui');	
		$count = $form->where($where)->count();
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
        $list = $form->where($where)->limit($page->firstRow, $page->listRows)->order('paixu asc,id desc')->select();
		$categorylist = M('projectcategory')->getField('cid,name');
		$cooktype = cookie('discount');
		$cookistr=explode(',',$cooktype);
		foreach($list as $k=>$v){
			$project = M('project')->where('id='.$v['projectid'])->find();
			$project['category'] = $categorylist[$project['cid']];
			$project['liclass'] = 'zhan';
			$project['hover'] = "";
			foreach ($cookistr as $li=>$ls){
				if($project['id']==$ls){
					$project['hover'] = "hover";
					$project['liclass'] = 'hodden';
					}
				}
			$list[$k]['project'] = $project;	
			}
		$this->assign('list',$list);
		$this->display();
	}
	
	 public function zhan(){
		   $M=M('project');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('discount');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('discount',$id);
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