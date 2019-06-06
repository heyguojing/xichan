<?php

// 本类由系统自动生成，仅供测试用途
class SearchAction extends CommonAction {
	
    public function index() {
		$getbox = $_GET;
		$keywords = trim($getbox['key']);
		$this->assign('keywords',$keywords);
		$Form   =   M('search');
		$wheres['search'] = array('like','%'.$keywords.'%');
		$li = $Form->where($wheres)->order('id desc')->select();
		foreach ($li as $k=>$v){
			$in = M($v['url'])->where('id='.$v['keyid'])->find();
			if (!$in){
				$Form->where("id=" . $v['id'])->delete();
				}
			}
		$count = $Form->where($wheres)->count();
		$this->assign('count',$count);
        import("@.ORG.Page");
        $page = new Page($count, 10);
        if(empty($showPage)){
			$this->assign('pa','0');
			$this->assign("page", $showPage);
			}
		else{	
		    $this->assign('pa','1');
		    $this->assign("page", $showPage);
		}
        $this->assign("page", $showPage);
        $list = $Form->where($wheres)->limit($page->firstRow, $page->listRows)->order('id desc')->select();
		foreach ($list as $k=>$v){
			$category = $v['url'] =='news'?'category':$v['url'].'category';
			if($v['url']=="news"){
				$curl = 'news';
				$cat = 'category';
				$picurl = '__newspic__';
				}
			elseif($v['url']=="expert"){
				$curl = 'expertinfo';
				$cat = 'expertlist';
				$picurl = '__expertimg__';
				}
			elseif($v['url']=="project"){
				$curl = 'proinfo';
				$cat = 'projectlist';
				$picurl = '__pictureimg__';
				}	
			elseif($v['url']=="case"){
				$curl = 'caseinfo';
				$cat = 'casecat';
				$picurl = '__pictureimg__';
				}	
			elseif($v['url']=="reality"){
				$curl = 'realshow';
				$cat = 'reality';
				$picurl = '__realityimg__';
				}
			elseif($v['url']=="brand"){
				$curl = 'brandinfo';
				$cat = 'category';
				$picurl = '__pictureimg__';
				}	
			$list[$k]['picurl'] = $picurl;				
			$list[$k]['categoryurl']=$category;
			$clist = M($v['url'])->where('id='.$v['keyid'])->find();
			$list[$k]['curl'] = $curl;
			$list[$k]['keywords'] = $clist['keywords'];
			$list[$k]['title'] = $clist['title'];
			$list[$k]['pic'] = $clist['pic'];
			$list[$k]['views'] = $clist['views'];
			$list[$k]['title'] = $v['url'] =='expert'?$clist['title1']:$clist['title'];
			$list[$k]['cat'] = $cat;
			$list[$k]['views'] = $clist['views'];
			$list[$k]['reshow'] = $clist['reshow'];
			$list[$k]['fenxiang'] = $clist['fenxiang'];
			$cteg = M($category)->where('cid = '.$clist['cid'])->find();
			$list[$k]['category'] = $cteg['name'];
			$list[$k]['cid'] =$v['url']=="reality"?'index':$cteg['cid'];
			}
		$this->assign('list',$list);	
    	$this->display();
    }
	public function chalou(){
		$M=M('project');
		$list = $M->select();
		foreach ($list as $k=>$v){
			  $count = M('search')->where('keyid = '.$v['id'].' and url = project')->count();
			  if($count==0){
				  echo $v['title'].'<br />';
				  $search['title'] = $v['title'];
				  $search['keyid'] = $v['id'];
				  $search['url']= 'project';
				  $search['search'] = $v['title'].','.$v['keywords'].','.$v['summary'];
				  M('search')->add($search);
				  }
			}
		echo "ok";	
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