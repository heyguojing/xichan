<?php

// 本类由系统自动生成，仅供测试用途
class ProjectAction extends CommonAction {
	public function index(){
		$form = M('projectcategory');
		$count = $form->count();
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
		$lists = $form->limit(0,1)->order('cid asc')->find();
		$list = $form->limit($page->firstRow, $page->listRows)->order('paixu asc,cid asc')->select();
		foreach ($list as $k=>$v){
			if($lists['cid']==$v['cid']){
				$list[$k]['border'] = "";
				}
			else{
				$list[$k]['border'] = "projectborder";
				}	
			}
		$val = cookie('project');
		$this->assign('list',$list);
    	$this->display();
		}
    public function category() {
		$pid=$_GET['id'];
		$form = M('projectcategory');
		$list = $form->where('pid='.$pid)->select();
		foreach($list as $k=>$v){
			$list[$k]['project']= M('project')->where('cid='.$v['cid'])->limit(0,5)->select();
			}
		$this->assign('list',$list);
    	$this->display();
    }
	public function projectlist() {
		$cid=$_GET['id'];
		$form = M('project');
		$wheres['cid'] = $cid;
		$count = $form->where($wheres)->count();
		import("@.ORG.Page");
        $page = new Page($count, 6);
        $showPage = $page->showas();
		if(empty($showPage)){
			$this->assign('pa','0');
			$this->assign("page", $showPage);
			}
		else{	
		    $this->assign('pa','1');
		    $this->assign("page", $showPage);
		}
		$list = $form->where($wheres)->limit($page->firstRow, $page->listRows)->order('paixu asc,id desc')->select();
		$categorylist = M('projectcategory')->getField('cid,name');
		$cooktype = cookie('project');
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
		$this->assign('list',$list);
    	$this->display();
    }
	public function info(){
		echo $notify_url;
		$where['id'] = $this->_get('id');
		$where['status'] = 1;
		M('project')->where($where)->setInc('reshow'); // 浏览量+1
		$info = M('project')->where($where)->find();
		if (!$info)$this->error('信息不存在...');
		$info['chushou'] = $info['chushou']<20?20:$info['chushou'];
		if(M('youhui')->where('projectid='.$info['id'])->count() > 0){
			$youhui = M('youhui')->where('projectid='.$info['id'])->find();
		    $info['tuangou'] = $youhui['tuangou'];
			$info['tuangoustart'] = $youhui['tuangoustart'];
			$info['tuangouend'] = $youhui['tuangouend'];
			$info['shiyongstart'] = $youhui['shiyongstart'];
			$info['shiyongend'] = $youhui['shiyongend'];
			$info['tuangoujia'] = $youhui['tuangoujia'];
			$info['dazhe'] = $youhui['dazhe'];
			}
		else{
			$info['tuangou'] = 0;
			}	
		if($info['tuangou'] == 1){
			 $info['tuangoujia'] =preg_replace('/[^\d]/','',$info['tuangoujia']);
			 $info['tuangouname'] = '团购价：'.$info['tuangoujia'].'元&nbsp;&nbsp;<del>原价：'.$info['jiage'].'元</del>'; 
			 }
		 elseif($info['tuangou'] == 2){
			 $info['tuangoujia'] =preg_replace('/[^\d]/','',$info['jiage'])*$info['dazhe'];
			 $info['tuangouname'] = '折扣价：'.$info['tuangoujia'].'元&nbsp;&nbsp;<del>原价：'.$info['jiage'].'元</del>';  
			 }
		 	 	 		
		$cookistr=explode(',',$info['zhuanjia']);
		foreach ($cookistr as $sd=>$sid){
			  $expert = M('expert')->where('id='.$sid)->field("`id`,`title`")->find();
			  $elist[$sd]['id'] = $expert['id'];
			  $elist[$sd]['title'] = $expert['title'];
		  }
		$info['cidname'] = M('projectcategory')->where('cid='.$info['cid'].'')->find();
		$info['pidname'] = M('projectcategory')->where('cid='.$info['pid'].'')->find();
		if(empty($info['video'])){
			$info['videoty'] = 0;
			$info['video'] = '';
			}
		else{
			$info['videoty'] = 1;
			}
		if($info['tuangou']==2){
			$info['tuangoujia'] = $info['jiage']*$info['dazhe'];
			}	
		$info['zhuanjia'] = $elist; 
		$xihuan = explode(',',$info['xihuan']);
		$xuanzhong = array();
		  foreach ($xihuan as $k=>$v){
			  $cin =  M('project')->where("id=" .$v)->find();
			 $xuanzhong[$k]['id'] = $cin['id'];
			 $xuanzhong[$k]['pic'] = $cin['pic'];
			 $xuanzhong[$k]['title'] = $cin['title']; 
			}
		$info['xihuan'] = $xuanzhong;
		
		//$case = explode(',',$info['caselist']);
//		$caselist = array();
//		  foreach ($case as $k=>$v){
//			  $cin =  M('case')->where("id=" .$v)->find();
//			 $caselist[$k]['id'] = $cin['id'];
//			 $caselist[$k]['pic'] = $cin['pic'];
//			 $caselist[$k]['title'] = $cin['title']; 
//			}
//		$info['caselist'] = $caselist;
		$cooktype = cookie('project');
		$cookistr=explode(',',$cooktype);
		foreach ($cookistr as $li=>$ls){
			if($info['id']==$ls){
				$info['hover'] = "hover";
				}
			}
		$hotlist = M('project')->where("hot=1")->limit(0,16)->select();
		$lovecount = M('projectlove')->where('prid='.$info['id'])->count();
		$this->assign('lovecount',$lovecount);
		$lovelist = M('projectlove')->where('prid='.$info['id'])->order('paixu asc,id desc')->select();
		$this->assign('lovelist',$lovelist);
		$this->assign('hotlist',$hotlist);
		$this->assign('info', $info);
		$this->display('projects_show');
	}
	 public function backyanzheng(){
		$yanzheng = $_GET['yanzheng'];
		if($yanzheng == $_SESSION['mcode'] ){
			echo json_encode(array('status' => 1, 'info' => "正确"));
			}
		else{
			echo json_encode(array('status' => 0, 'info' => "验证码错误"));
			}	
		}
	 public function payment(){
		 $where['id'] = $this->_get('id');
		 $where['status'] = 1;
		 $info = M('project')->where($where)->find();
		 if (!$info)$this->error('信息不存在...');
		 if(M('youhui')->where('projectid='.$info['id'])->count() > 0){
			$youhui = M('youhui')->where('projectid='.$info['id'])->find();
		    $info['tuangou'] = $youhui['tuangou'];
			$info['tuangoustart'] = $youhui['tuangoustart'];
			$info['tuangouend'] = $youhui['tuangouend'];
			$info['shiyongstart'] = $youhui['shiyongstart'];
			$info['shiyongend'] = $youhui['shiyongend'];
			$info['tuangoujia'] = $youhui['tuangoujia'];
			$info['dazhe'] = $youhui['dazhe'];
			}
		else{
			$info['tuangou'] = 0;
			}
		 if($info['tuangou'] == 1){
			 $info['tuangoujia'] =preg_replace('/[^\d]/','',$info['tuangoujia']);
			 $info['tuangouname'] = '团购价：'.$info['tuangoujia'].'元&nbsp;&nbsp;'; 
			 }
		 elseif($info['tuangou'] == 2){
			 $info['tuangoujia'] =preg_replace('/[^\d]/','',$info['jiage'])*$info['dazhe'];
			 $info['tuangouname'] = '折扣价：'.$info['tuangoujia'].'元&nbsp;&nbsp;</span>';  
			 }
		 else{
			 $info['jiage'] =preg_replace('/[^\d]/','',$info['jiage']); 
			 $info['tuangouname'] = $info['jiage'].'元';
			 }	 	 		
		 $this->assign('info', $info);
		 $this->display();
		 }
	public function payment2(){
		 $data = $_POST;
		 $where['id'] = $data['id'];
		 $where['status'] = 1;
		 $info = M('project')->where($where)->find();
		 if(M('youhui')->where('projectid='.$info['id'])->count() > 0){
			$youhui = M('youhui')->where('projectid='.$info['id'])->find();
		    $info['tuangou'] = $youhui['tuangou'];
			$info['tuangoustart'] = $youhui['tuangoustart'];
			$info['tuangouend'] = $youhui['tuangouend'];
			$info['shiyongstart'] = $youhui['shiyongstart'];
			$info['shiyongend'] = $youhui['shiyongend'];
			$info['tuangoujia'] = $youhui['tuangoujia'];
			$info['dazhe'] = $youhui['dazhe'];
			}
		else{
			$info['tuangou'] = 0;
			}
		 if($info['tuangou'] == 1){
			 $info['tuangoujia'] =preg_replace('/[^\d]/','',$info['tuangoujia']);
			 $info['tuangouname'] = '团购价：'.$info['tuangoujia'].'元&nbsp;&nbsp;'; 
			 }
		 elseif($info['tuangou'] == 2){
			 $info['tuangoujia'] =preg_replace('/[^\d]/','',$info['jiage'])*$info['dazhe'];
			 $info['tuangouname'] = '折扣价：'.$info['tuangoujia'].'元&nbsp;&nbsp;';  
			 }
		 else{
			 $info['jiage'] =preg_replace('/[^\d]/','',$info['jiage']); 
			 $info['tuangouname'] = $info['jiage'].'元';
			 }
		 $data['tel'] = substr_replace($data['mobile'], '****', 3, 4);
		 $this->assign('data',$data);
		 $this->assign('info',$info);
		 $this->display();
		 }
	public function payment3(){
		 $data = $_POST;
		 $where['id'] = $data['id'];
		 $where['status'] = 1;
		 $info = M('project')->where($where)->find();
		 $this->assign('data',$data);
		 $this->assign('info',$info);
		 $this->display();
		 }	 
	public function mobile(){
		$mobile = $_GET['mobile'];
		$mcode = range(10,99);
		shuffle ($mcode);
		$code=array_slice($mcode,0,2); 
		$ordcode=$code[0].$code[1];
		$_SESSION['mcode'] = $ordcode;
		if(empty($mobile)){
			echo json_encode(array("status" => 1, "info" => "请输入您的手机号！"));
			}
		else{
			$mcode=$_SESSION['mcode'];
			$num = $mobile;
	        $message = "您好，您本次的验证码是：".$ordcode.",请尽快填写你的验证码。";//内容，如为中文一定要使用一下urlencode函数
			$getback = $this->set_phone($num,$message);
			//$getback=array("status" => 1, "info" => $message);
			echo json_encode($getback);
			}	
		
		}	 
	
	private function set_phone($num,$message){
		  header("Content-type: text/html; charset=utf-8");
		  date_default_timezone_set('PRC'); //设置默认时区为北京时间
		  $uid = 'LKSDK00085';//短信接口用户名 $uid
		  $passwd = '506122';//短信接口密码 $passwd
		  $num =$num;  
		  $msg = rawurlencode(mb_convert_encoding($message, "gb2312", "utf-8"));
		  $gateway = "http://mb345.com:999/WS/BatchSend.aspx?CorpID={$uid}&Pwd={$passwd}&Mobile={$num}&Content={$msg}&Cell=&SendTime=";
		  $result = file_get_contents($gateway);
		  if($result >= 0 ){
			  $sunc=array("status" => 1, "info" => "获取验证码成功，请填写验证码！");
			 }
		  else{
			  $sunc=array("status" => 0, "info" => "请重新获取验证码或者联系管理员");
		  }
		  return $sunc;
	  }
	
	public function abpos(){
		$data = $_GET;
		$this->assign('data',$data);
		$this->display();
		}
	 	 
	public function ss(){
		$this->display();
		}	 
	 public function zhan(){
		   $M=M('project');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('project');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('project',$id);
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
		   $M=M('project');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('project');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('project',$id);
		   $ad['id']=$info['id'];
		   $ad['views'] = $info['views']+1;
			if ($M->save($ad)) {
				$back = $M->where($where)->find();
				echo json_encode(array("status" => 1, "info" => "".$back['views'].""));
			} else {
				echo json_encode(array("status" => 0, "info" => "点赞失败"));
			}
		}
    public function infoproject(){
		   $M=M('project');
		   $where['id'] = $_GET['id'];
		   $info=$M->where($where)->find(); 
		   if (!$info)$this->error('信息不存在...');
		   $val = cookie('infoproject');
		   if(empty($val)){
			   $id=$_GET['id'];
			   }
			else{
				$id=$val.','.$_GET['id'];
				}  
		   cookie('infoproject',$id);
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