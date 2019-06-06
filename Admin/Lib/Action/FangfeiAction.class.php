<?php
class FangfeiAction extends Action{
	
	public function index(){
		 $getback = $this->isWeixin();
		/*if($getback==false){
			$this->display('weixin');	
			}
		else{*/
		$id = $_GET['id'];
		$M = M('user');
		if($id==$_SESSION['id']){
			$info = M('user')->where('id='.$_SESSION['id'])->find();
			$logintime = $info['logintime'];
			$updatetime = $info['updatetime'];
			if($info['status'] == 1){
				$time = time();
				$backtime = strtotime(date('Y-m-d',$updatetime)." 23:59:59");
				if($time<$backtime){
					$fangfei = '你的风筝放飞了<span style="color:#FF0000">  '.$info['gaodu'].' </span>米！<br>';
					$list = $M->order('gaodu desc')->limit(0,5)->select();
					foreach ($list as $k=>$v){
					  $list[$k]['paiming'] = '<img src="__PUBLIC__/images/mingci'.$k.'.png"';
					  }
					$listone = $M->order('gaodu desc')->select();
					foreach ($listone as $k=>$v){
						if($v['id']==$_SESSION['id']){
							$ks = $k+1;
							$one =  $ks ;
							}
						}	
					$this->assign('one',$one);
					$this->assign('fangfei',$fangfei);
					$this->assign('list',$list);
					$this->assign('info',$info);
					$this->display('zhongjiang');
					}
				else{
					$info = M('user')->where('id='.$_SESSION['id'])->find();
					$this->display();//今天没玩过
					}
				}
			else{
				$info = M('user')->where('id='.$_SESSION['id'])->find();
		        $this->display();//未参与过游戏的，直接跳转
				}	
			}
		else{
			$id = $_GET['id'];
			$backtime = strtotime(date('Y-m-d',time())." 00:00:00");
			$wh['updatetime'] = array('egt',$today);
			$add = M('ip')->where($wh)->count();
			
			$user_IP=$_SERVER["REMOTE_ADDR"];  
			//echo $user_IP;
			$wheres['ip'] = array('like',$user_IP);
			$wheres['u_id'] = $id;
			$ipcinf = M('ip')->where($wheres)->find();
			if(empty($ipcinf['id'])){
			$getback = $this->suijishu();
			$xinxi = $M->where('id = '.$id)->find();
			$gaodu = $xinxi['gaodu']+$getback;
			$add['id'] = $id;
			$add['gaodu'] = $gaodu;
			$M->save($add);
			$adds['ip'] = $user_IP;
			$adds['u_id'] = $id;
			$adds['gaodu'] = $getback;
			$adds['updatetime'] = time();
			M('ip')->add($adds);
			$fangfei = '真厉害！<br>你为你朋友的风筝放飞提高了 <span style="color:#FF0000">'.$getback.'</span> 米！';
			}else{
				$fangfei = '&nbsp;<br>你已经为提高了 <span style="color:#FF0000">'.$ipcinf['gaodu']."</span> 米！";
			}
			$info = $M->where('id = '.$id)->find();
			$list = $M->order('gaodu desc')->limit(0,5)->select();
			foreach ($list as $k=>$v){
				$list[$k]['paiming'] = '<img src="__PUBLIC__/images/mingci'.$k.'.png"';
				}
			$listone = $M->order('gaodu desc')->select();
			foreach ($listone as $k=>$v){
				if($v['id']==$id){
					$ks = $k+1;
					$one =  $ks ;
					}
				}
			$this->assign('one',$one);
			$this->assign('fangfei',$fangfei);
			$this->assign('info',$info);
			$this->assign('list',$list);
			$this->display('zhuli');
			}
			//}	
	}
	
	public function adnewgao(){
		$M = M('user');
		$add['gaodu'] = $_GET['gaodu']+$_SESSION['gaodu'];
		$add['status'] = 1;
		$add['id'] = $_SESSION['id'];
		$add['updatetime'] = time();
		$_SESSION['newgaodu'] = $_GET['gaodu'];
		if($_SESSION['gaodu']>0){
		$title = '<p class="wenzip1"><img src="__PUBLIC__/images/jiantou.gif"></p>';
        $title .= '<p class="wenzip1">今天为你的风筝放飞了 <span style="color:#F00" id="cgaodu">'.$_GET['gaodu'].'</span> 米！<br>你原有的高度为：<span style="color:#F00" id="cgaod">'.$_SESSION['gaodu'].'</span>米！</p><br>';
        $title .= '<p class="wenzip2">还有没有朋友？请TA帮你放风筝！<br>分享朋友圈或者朋友让他为你助力<br>让你的风筝飞的更高！</p>';
		}else{
		$title = '<p class="wenzip1"><img src="__PUBLIC__/images/jiantou.gif"></p>';
        $title .= '<p class="wenzip1">起飞了！<br>你的风筝放飞了 <span style="color:#F00" id="cgaodu">'.$_GET['gaodu'].'</span> 米！</p>';
        $title .= '<p class="wenzip2">还有没有朋友？请TA帮你放风筝！<br>分享朋友圈或者朋友让他为你助力<br>让你的风筝飞的更高！</p>';
		}
		if($M->save($add)){
			echo json_encode(array("status" => 1, "info" =>  "".$title.""));
			} else {
				echo json_encode(array("status" => 0, "info" => "可以使用"));
			}	
		}
	
	public function paiming(){
		$M = M('user');
		$info = $M->where('id='.$_SESSION['id'])->find();
		$gaodusss = $_SESSION['gaodu']+$_SESSION['newgaodu'];
		if($info['gaodu'] == $gaodusss){
			unset($_SESSION['gaodu']);
			unset($_SESSION['newgaodu']);
			}
		else{
			$add['gaodu'] = $gaodusss;
			$add['id'] = $_SESSION['id'];
			$_SESSION['gaodu'] = $_GET['gaodu'];
			$M->save($add);
			unset($_SESSION['gaodu']);
			unset($_SESSION['newgaodu']);
			}	
		$list =  M('user')->order('gaodu desc')->limit(0,5)->select();
		foreach ($list as $k=>$v){
				$list[$k]['paiming'] = '<img src="__PUBLIC__/images/mingci'.$k.'.png"';
				}
		$listone = M('user')->order('gaodu desc')->select();
			foreach ($listone as $k=>$v){
				if($v['id']==$_SESSION['id']){
					$ks = $k+1;
					$one = $ks;
					}
				}
		$this->assign('one',$one);					
		$this->assign('list',$list);
		$this->display();
		}
	
	public function fangfei(){
		$getback = $this->isWeixin();
		if($getback==false){
			$this->display('weixin');	
			}
		else{
		$getbox= $_GET;
		$M = M('user');
		
		if(empty($_SESSION['id'])){
			$id = $getbox['id'];
			
			$user_IP=$_SERVER["REMOTE_ADDR"];

			//$ipp = str_replace('.','',$user_IP);
			$wheres['ip'] = array('like',$user_IP);
			$wheres['u_id'] = $id;
			echo $_REQUEST['REMOTE_HOST'];
			$ipcinf = M('ip')->where($wheres)->find();
			if(empty($ipcinf['id'])){
			$getback = $this->suijishu();
			$xinxi = $M->where('id = '.$id)->find();
			$gaodu = $xinxi['gaodu']+$getback;
			$add['id'] = $id;
			$add['gaodu'] = $gaodu;
			$M->save($add);
			$adds['ip'] = $user_IP;
			$adds['u_id'] = $id;
			$adds['gaodu'] = $getback;
			$adds['updatetime'] = time();
			M('ip')->add($adds);
			$fangfei = '真厉害！<br>你为你朋友的风筝放飞提高了<span style="color:#FF0000"> '.$getback.'</span> 米！';
			}else{
				$fangfei = '你已经为你朋友的风筝放飞提高了 <span style="color:#FF0000">'.$ipcinf['gaodu']."</span> 米！";
			}
			$info = $M->where('id = '.$id)->find();
			$list = $M->order('gaodu desc')->limit(0,5)->select();
			foreach ($list as $k=>$v){
				$list[$k]['paiming'] = '<img src="__PUBLIC__/images/mingci'.$k.'.png"';
				}
			$listone = $M->order('gaodu desc')->select();
			foreach ($listone as $k=>$v){
				if($v['id']==$id){
					$ks = $k+1;
					$one =  $ks ;
					}
				}
			$this->assign('one',$one);
			$this->assign('fangfei',$fangfei);
			$this->assign('info',$info);
			$this->assign('list',$list);
			$this->display('zhuli');
			}
		else{
			if(empty($_SESSION['id'])!=$id){
				
				$id = $getbox['id'];
			
			$user_IP=$_SERVER["REMOTE_ADDR"];

			//$ipp = str_replace('.','',$user_IP);
			$wheres['ip'] = array('like',$user_IP);
			$wheres['u_id'] = $id;
			echo $_REQUEST['REMOTE_HOST'];
			$ipcinf = M('ip')->where($wheres)->find();
			if(empty($ipcinf['id'])){
			$getback = $this->suijishu();
			$xinxi = $M->where('id = '.$id)->find();
			$gaodu = $xinxi['gaodu']+$getback;
			$add['id'] = $id;
			$add['gaodu'] = $gaodu;
			$M->save($add);
			$adds['ip'] = $user_IP;
			$adds['u_id'] = $id;
			$adds['gaodu'] = $getback;
			M('ip')->add($adds);
			$fangfei = '真厉害！<br>你为你朋友的风筝放飞提高了<span style="color:#FF0000"> '.$getback.'</span> 米！';
			}else{
				$fangfei = '你已经为你朋友的风筝放飞提高了 <span style="color:#FF0000">'.$ipcinf['gaodu']."</span> 米！";
			}
			$info = $M->where('id = '.$id)->find();
			$list = $M->order('gaodu desc')->limit(0,5)->select();
			foreach ($list as $k=>$v){
				$list[$k]['paiming'] = '<img src="__PUBLIC__/images/mingci'.$k.'.png"';
				}
			$listone = $M->order('gaodu desc')->select();
			foreach ($listone as $k=>$v){
				if($v['id']==$id){
					$ks = $k+1;
					$one =  $ks ;
					}
				}
			$this->assign('one',$one);
			$this->assign('fangfei',$fangfei);
			$this->assign('info',$info);
			$this->assign('list',$list);
			$this->display('zhuli');
				
				
				}
			else{
				$xinxi = $M->where('id = '.$_SESSION['id'])->find();
			    if($xinxi['status'] == 1){
				$info = $M->where('id = '.$_SESSION['id'])->find();
				$fangfei = "你的风筝放飞了 ".$info['gaodu']." 米！<br>";
				$list = $M->order('gaodu desc')->limit(0,5)->select();
				$listone = $M->order('gaodu desc')->select();
				foreach ($listone as $k=>$v){
					if($v['id']==$_SESSION['id']){
						$ks = $k+1;
						$one =  $ks ;
						}
					}	
				$this->assign('one',$one);
				$this->assign('fangfei',$fangfei);
				$this->assign('list',$list);
				$this->assign('info',$info);
				$this->display('zhongjiang');
				}	
				}	
			}
			}	
		}
		
	private function suijishu(){
		$mcode = range(1,9);
		shuffle ($mcode);
		$code=array_slice($mcode,0,1); 
		$ordcode=$code[0];
		return $ordcode;
		}
	private function isWeixin(){ 
		  $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		  $is_weixin = strpos($agent, 'micromessenger') ? true : false ;   
		  if($is_weixin){
			  return true;
		  }else{
			  return false;
		  }
		}
	private function chushu(){
		$mcode = range(5,30);
		shuffle ($mcode);
		$code=array_slice($mcode,0,1); 
		$ordcode=$code[0];
		return $ordcode;
		}	
	
}
?>