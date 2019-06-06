<?php

// 本类由系统自动生成，仅供测试用途
class IndexAction extends CommonAction {
	
    public function index() {
		$adlist = M('ad')->where('cid=1')->order('paixu asc')->select();
		$adlist2 = M('ad')->where('cid=4')->order('paixu asc,id asc')->select();
		$guanggao1 = $adlist2[0];
		$guanggao2 = $adlist2[1];
		$guanggao3 = $adlist2[2];
		$form = M('indexboxcategory');
		$list = $form->where('pid=0')->select();
		$shuxingarr = array('','<span class="hot">热</span>','<span class="hui">惠</span>','<span class="hui">团</span>');
		foreach ($list as $k=>$v){
			$inlist = M('indexbox')->where('cid='.$v['cid'])->limit(0,6)->order('id asc')->select();
			foreach ($inlist as $ks=>$vs){
				if($vs['title']=='查看更多'){
					$inlist[$ks]['more'] = 'class="more"';
					}
				else{
					$inlist[$ks]['more'] = '';
					}	
				
				$inlist[$ks]['shuxing'] = $shuxingarr[$vs['shuxing']]; 
				}
			$list[$k]['indexbox']= $inlist;
			}
		//$newslist = M('news')->where('tuijian=1')->select();
		 $wheres['status'] = 1;
		 $wheres['tuijian'] = 1;
		$Form   =   M('News');
		$count = $Form->where($wheres)->count();
        import("@.ORG.Page");
        $page = new Page($count, 3);
        $showPage = $page->showas();
        if(empty($showPage)){
			$this->assign('pa','0');
			$this->assign("page", $showPage);
			}
		else{	
		    $this->assign('pa','1');
		    $this->assign("page", $showPage);
		}
        $newslist = $Form->where($wheres)->limit($page->firstRow, $page->listRows)->order('id desc')->select();
		
		$cidArr = M("Category")->field("`cid`,`name`")->select();
        foreach ($cidArr as $k => $v) {
            $cids[$v['cid']] = $v;
        }
        unset($cidArr);
        foreach ($newslist as $k => $v) {
            $newslist[$k]['cidName'] = $cids[$v['cid']]['name'];
        }	
		$adbanerlist = M('ad')->where('cid=2')->limit(0,1)->find();
		
		$cidArr = M("brandcategory")->field("`cid`,`name`")->select();
        foreach ($cidArr as $k => $v) {
            $brandcategory[$v['cid']] = $v;
        }
        unset($cidArr);
		$cidArr = M("expertcategory")->field("`cid`,`name`")->select();
        foreach ($cidArr as $k => $v) {
            $expertcategory[$v['cid']] = $v;
        }
        unset($cidArr);
		$cidArr = M("projectcategory")->field("`cid`,`name`")->select();
        foreach ($cidArr as $k => $v) {
            $projectcategory[$v['cid']] = $v;
        }
        unset($cidArr);
		$expertlist = M('expert')->where('tuijian=1')->limit(0,2)->select();//专家
		$cooktypes = cookie('expert');
		$cookistrs=explode(',',$cooktypes);
		foreach ($expertlist as $k=>$v){
			$expertlist[$k]['categoryname'] = $expertcategory[$v['cid']]['name'];
			$expertlist[$k]['liclass'] = 'zhan';
			$expertlist[$k]['hover'] = "";
			foreach ($cookistrs as $li=>$ls){
				if($v['id']==$ls){
					$expertlist[$k]['hover'] = "hover";
					$expertlist[$k]['liclass'] = 'hodden';
					}
				}
			}
		$realitylist = M('reality')->where('tuijian=1')->limit(0,2)->select();//真人秀
		$cooktypes = cookie('reality');
		$cookistrs=explode(',',$cooktypes);
		foreach ($realitylist as $k=>$v){
			$realitylist[$k]['liclass'] = 'zhan';
			$realitylist[$k]['hover'] = "";
			foreach ($cookistrs as $li=>$ls){
				if($v['id']==$ls){
					$realitylist[$k]['hover'] = "hover";
					$realitylist[$k]['liclass'] = 'hodden';
					}
				}
			}
		$projectlist = M('project')->where('tuijian=1')->limit(0,2)->select();//项目
		$cooktypes = cookie('project');
		$cookistrs=explode(',',$cooktypes);
		foreach ($projectlist as $k=>$v){
			$projectlist[$k]['categoryname'] = $projectcategory[$v['cid']]['name'];
			$projectlist[$k]['liclass'] = 'zhan';
			$projectlist[$k]['hover'] = "";
			foreach ($cookistrs as $li=>$ls){
				if($v['id']==$ls){
					$projectlist[$k]['hover'] = "hover";
					$projectlist[$k]['liclass'] = 'hodden';
					}
				}
			}
		$brandlist = M('brand')->where('tuijian=1')->limit(0,2)->select();//品牌
		$cooktypes = cookie('brand');
		$cookistrs=explode(',',$cooktypes);
		foreach ($brandlist as $k=>$v){
			$brandlist[$k]['categoryname'] = $brandcategory[$v['cid']]['name'];
			$brandlist[$k]['liclass'] = 'zhan';
			$brandlist[$k]['hover'] = "";
			foreach ($cookistrs as $li=>$ls){
				if($v['id']==$ls){
					$brandlist[$k]['hover'] = "hover";
					$brandlist[$k]['liclass'] = 'hodden';
					}
				}
			}
		
		$this->assign('adlist',$adlist);
		$this->assign('guanggao1',$guanggao1);
		$this->assign('guanggao2',$guanggao2);
		$this->assign('guanggao3',$guanggao3);
		$this->assign('expertlist',$expertlist);
		$this->assign('realitylist',$realitylist);
		$this->assign('projectlist',$projectlist);
		$this->assign('brandlist',$brandlist);
		$this->assign('adbanerlist',$adbanerlist);
		$this->assign('list',$list);	
		$this->assign('newslist',$newslist);
    	$this->display();
    }
	public function kefu(){
		$getbox = $_GET;
		$where['id'] = $getbox['id'];
		$sqlname = $getbox['sqlname'];
		M($sqlname)->where($where)->setInc('reshow');
		$info = M($sqlname)->where($where)->find();
		echo json_encode(array("status" => 1, "info" => "".$info['reshow'].""));
		}
     public function zhan(){
		   $M=M('News');
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