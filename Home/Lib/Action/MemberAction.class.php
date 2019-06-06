<?php
class MemberAction extends UsercommAction{
	public function index(){
		R('User/checkLogin');
		$res = M('member')->where("uid='".$_SESSION['uid']."'")->find();
		$this->assign('userinfo',$res);
		$this->display('member');
		}
	public function myorders(){
		//我的订单
		$this->display('myaccount');
		}	
	public function myvoucher(){
		//我的订单
		$this->display('myaccount');
		}
	public function myaccount(){
		//设置
		$where['uid']=$_SESSION['USER_AUTH_KEY'];
		$memberinfo = M('member')->where($where)->find();
		$this->assign('memberinfo',$memberinfo);
		$this->display('myaccount');
		}	
	public function loginOut() {
        $_SESSION['USER_AUTH_KEY'] ='';
		$_SESSION['uid']='';
		$_SESSION['is_login'] ='';
		$_SESSION['expire'] ='';
		$_SESSION['user'] = '';
            unset($_SESSION);
            unset($_COOKIE);
        $this->redirect("Index/index");
    }
	
	public function password(){
		if(IS_POST){
			$post = $_POST;
			header("Content-Type:text/html; charset=utf-8");
            header('Content-Type:application/json; charset=utf-8');
		    $oldpossword = trim($post['oldpossword']);
			$newspossword = trim($post['newspossword']);
			$where['uid']=$_SESSION['USER_AUTH_KEY'];
			$M = M("member");
			$memberinfo = M('member')->where($where)->find();  
			  if ($memberinfo['pwd'] == encrypt($oldpossword)) {
				  $data['uid']=$memberinfo['uid'];
				  $data['pwd']=encrypt($newspossword);
				  $M->save($data);
				  $_SESSION['USER_AUTH_KEY'] ='';
				  $_SESSION['uid']='';
				  unset($_SESSION);
				  echo json_encode(array('status' => 1, 'info' => "修改成功，请重新登录", 'url' => U("User/index")));
				 
			  } else {
				  echo json_encode(array('status' => 0, 'info' => "账号或密码错误"));
			  }
			}
		else{
			$this->display();
			}	
		}
		public function avatar(){
		if(IS_POST){
			header("Content-Type:text/html; charset=utf-8");
            header('Content-Type:application/json; charset=utf-8');
			$M = M("member");
			$data = $_POST['info'];
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();// 实例化上传类
			$upload->maxSize  = 3145728 ;// 设置附件上传大小
			$upload->saveRule  = date('Ymj').'_p'.time();// 设置文件名
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath =  "./Uploads/image/user/";// 设置附件上传目录
			if(!$upload->upload()) {
				//$this->error($upload->getErrorMsg());// 上传错误提示错误信息
			}else{
				$info =  $upload->getUploadFileInfo();// 上传成功 获取上传文件信息
			}
			
			if($info){
				$data['avatar'] = $info[0]['savename'];
			}
			$data['uid']=$_SESSION['USER_AUTH_KEY']; 
			  if ($M->save($data)) {
				  echo json_encode(array('status' => 1, 'info' => "修改成功!", 'url' => U("Member/index")));
				  //$this->redirect("Member/avatar");
				 
			  } else {
				  echo json_encode(array('status' => 0, 'info' => "上传失败"));
			  }
			}
		else{
			$where['uid']=$_SESSION['USER_AUTH_KEY'];
			$newsinfo = M('member')->where($where)->find();
			if(empty($newsinfo['avatar'])){
				$newsinfo['avatar'] = '<img src="__PUBLIC__/images/member/user.jpg" width="40%" />';
				}
			else{
				$newsinfo['avatar'] = '<img src="__userimg__'.$newsinfo['avatar'].'"  width="40%" />';
				}	
			$this->assign('newsinfo',$newsinfo);
			$this->display();
			}	
		}		
	public function webbang(){
		$datas = $_GET;
		$where['weibo_uid'] = $datas['weibouid'];
		$where['webstatus'] = 1;
		$where['uid']=$_SESSION['USER_AUTH_KEY'];
		if(M('member')->save($where)){
			echo json_encode(array('status' => 1, 'info' => "绑定成功！", 'url' => U('Member/myaccount')));
			}
		else{
			echo json_encode(array('status' => 0, 'info' => '对不起，系统错误，请刷新重试！'));
		    }
		}
	public function webdel(){
		$where['weibo_uid'] = "";
		$where['webstatus'] = 0;
		$where['uid']=$_SESSION['USER_AUTH_KEY'];
		if(M('member')->save($where)){
			echo json_encode(array('status' => 1, 'info' => "解除绑定成功！", 'url' => U('Member/myaccount')));
			}
		else{
			echo json_encode(array('status' => 0, 'info' => '对不起，系统错误，请刷新重试！'));
		    }
		}
		
	public function tencentadd(){
		$datas = $_GET;
		$where['tencent_uid'] = $datas['tencentuid'];
		$where['tencentstatus'] = 1;
		$where['uid']=$_SESSION['USER_AUTH_KEY'];
		if(M('member')->save($where)){
			echo json_encode(array('status' => 1, 'info' => "绑定成功！", 'url' => U('Member/myaccount')));
			}
		else{
			echo json_encode(array('status' => 0, 'info' => '对不起，系统错误，请刷新重试！'));
		    }
		}
	public function tencentdel(){
		$where['tencent_uid'] = "";
		$where['tencentstatus'] = 0;
		$where['uid']=$_SESSION['USER_AUTH_KEY'];
		if(M('member')->save($where)){
			echo json_encode(array('status' => 1, 'info' => "解除绑定成功！", 'url' => U('Member/myaccount')));
			}
		else{
			echo json_encode(array('status' => 0, 'info' => '对不起，系统错误，请刷新重试！'));
		    }
		}	

	//找回会员密码的验证码验证
	public function verifiction(){
		if($_POST){
			if(strlen($_POST['code']) == 4 ){
				if($_SESSION['verify'] != md5($_POST['code'])){
	            	echo json_encode(array('status' => 0, 'info' => "验证码错误啦，再输入吧"));
				}else{
					echo json_encode(array('status' =>1));
				}
			}
			if(strlen($_POST['code']) == 11){
				if(M('member')->where("mobile='".$_POST['code']."'")->find()){
					echo json_encode(array('status' =>1));exit;
				}else{
					echo json_encode(array('status'=>0,'info'=>'此号码未注册'));exit;
				}
			}
		}
	}
	//发送短信及信息内容存入数据库，并记录发送时间和验证时间，有效期
	public function messageVer(){
		if($_POST){
			$member = M("member")->where("mobile='".$_POST['mobile']."'")->find();
			if(!$member){
				echo json_encode(array('status'=>0,'info'=>'此电话号码未注册！','url'=>U('Member/getpass')));exit;
			}
			//根据此号码查询出用户是否频繁请求，2分钟内不能重复请求短信
			$res = M('member_mess')->where("mobile='".$_POST['mobile']."'")->order('ctime desc')->find();
			if(($res['ctime']+120) >=time()){
				echo json_encode(array('status'=>0,'info'=>'请勿频繁请求,稍后再试！'));exit;
			}
			if($_SESSION['verify'] != md5($_POST['code'])){
            	echo json_encode(array('status' => 0, 'info' => "验证码错误啦，再输入吧"));
			}
			$M = M('member_mess');
			//短信发送内容拼装
			//短信接口用户名 $uid
			$uid = 'LKSDK00085';
			//短信接口密码 $passwd
			$passwd = '506122';
			//发送到的目标手机号码 $telphone
			$telphone = $_POST['mobile'];
			//查询出之前是否获取过验证码且未过期，有则发送给用户，没有则重新获取
			$pre_code = $M->where("mobile='".$_POST['mobile']."'")->order('ctime desc')->find();
			if($pre_code && ($pre_code['ctime']+60*30) > time()){
				$data['code'] = $pre_code['code'];
			}else{
				if($pre_code){
					$arr['id'] = $pre_code['id'];
					$arr['status'] = 0;//旧验证码状态改为已失效0
					$M->save($arr);
				}
				$data['code'] = Rand(100000,999999);
			}
			//短信内容 $message
			$message = '您的验证码是'.$data["code"].'，30分钟内验证有效，请妥善保管!';
			$message_tow=rawurlencode(mb_convert_encoding($message, "gb2312", "utf-8"));
			//验证码请求时间	
			$data['ctime'] = time();
			//验证码内容
			$data['content'] = $message;
			//验证码用户id
			$data['uid'] = $member['uid'];
			//验证码状态 未验证1
			$data['status'] = 1;
			//用户的手机号码
			$data['mobile'] = $_POST['mobile'];
			$gateway = "http://mb345.com/ws/batchSend.aspx?CorpID=".$uid."&Pwd=".$passwd."&Mobile=".$telphone."&Content=".$message_tow."&Cell=&SendTime=";
			//发送短信	
			$result = file_get_contents($gateway);
			$res = $M->add($data);
			if(  $result == 0 || $result == 1){
				echo json_encode(array('status'=>1,'info'=>'验证码已下发，请注意查收','url'=>'index'));exit;
			}
			else
			{
				echo json_encode(array('status'=>0,'info'=>'验证码请求失败，请稍后再试！'));exit;
			}
		}
	}
	//判断手机号码和验证码是否正确
	private function isMobile_verify($str){
		if(strlen($str) == 4){
			if(md5($str) != $_SESSION['verify']){
				return false;
			}else{
				return ture;
			}
		}elseif(strlen($str) == 11){
			if(M('member')->where("mobile='".$str."'")->count() <1){
				return false;
			}
			else{
				return ture;
			}
		}
	}

	//会员忘记密码重置
	public function getpass(){
		if($_POST){
			if($this->isMobile_verify($_SESSION['verify'])){
				echo json_encode(array('status'=>0,'info'=>'图形验证码输入错误'));exit;
			}
			if(!$this->isMobile_verify($_POST['mobile'])){
				echo json_encode(array('stauts'=>0,'info'=>'此手机号码未注册！'));exit;
			}
			$info = $_POST;
			$M = M('member_mess');
			//根据手机号 查询出最新请求的数据
			$res = $M->where("mobile='".$info['mobile']."'")->order('ctime desc')->find();
			if($res['status'] != 1){
				echo json_encode(array('status'=>0,'info'=>'验证码已失效，请重新获取!'));exit;
			}
			//如果验证码的时间+30分钟，大于当前时间则验证成功，跳转到设置新密码页面
			if($res && ($res['ctime']+60*30) >time()){
				if($info['code'] == $res['code']){
					  $userinfo = M('member')->field('`uid`,`email`')->where("uid='".$res['uid']."'")->find();
					  $_SESSION['USER_AUTH_KEY'] = $userinfo['uid'];
					  $_SESSION['is_login'] = 1;
					  $_SESSION['expire'] = time()+60*60*72;
					  $data['uid']=$userinfo['uid'];
					  $data['login_time'] = time();
					  M('member')->save($data);
					  $_SESSION['uid'] = $userinfo['uid'];
					  $_SESSION['user'] = $userinfo['email'];
					  //修改验证码的状态
					  $arr['id'] = $res['id'];
					  $arr['status'] = 2;
					  $M->save($arr);
					  
					  echo json_encode(array('status'=>'1', 'info'=>'信息验证成功！','url'=> U('Member/newpass')));exit;
				}else{
					echo json_encode(array('status'=>'0','info'=>'手机验证码错误，请重新输入!'));exit;
				}
			}else{
				echo json_encode(array('status'=>'0','info'=>'验证码已失效，请重新获取！'));exit;
			}

		}
		$this->display('Member/getpass');
	}
	//用户设置新密码
	public function newpass(){
		R('User/checkLogin');
		if($_POST){
			$data = $_POST;
			$arr['pwd'] = encrypt($data['pwd']);
			$arr['uid'] =$data['uid'];
			$res = M('member')->save($arr);
			if($res){
				echo json_encode(array('stauts'=>1,'info'=>'密码重置成功！','url'=>U('user/index')));
				unset($_SESSION['expire']);exit;
			}else{
				echo json_encode(array('stauts'=>0,'info'=>'密码重置失败!请稍候再试'));exit;
			}
		}
		$this->assign('uid',$_SESSION['uid']);
		$this->display();
	}
	//用户修改密码
	public function editp(){
		R('User/checkLogin');
		if($_POST){
			$data = $_POST;
			$M = M('member');
			$pwd = encrypt(trim($data['ypwd']));//原始密码加密，下方做检验
			$res = $M->where("uid='".$data['uid']."'")->find();
			if($res){
				if($res['pwd'] == $pwd){
					$arr['uid'] = $data['uid'];
					$arr['pwd'] = encrypt($data['pwd']);
					if($M->save($arr)){
						echo json_encode(array('status'=>1,'info'=>'密码修改成功!请重新登录！','url'=>U('User/index')));
							unset($_SESSION['expire']);exit;
					}else{
						echo json_encode(array('status'=>0,'info'=>'密码修改失败，请稍候再试!'));exit;
					}
				}else{
					echo json_encode(array('status'=>0,'info'=>'原始密码不正确'));exit;
				}
			}
		}
		$this->assign('uid',$_SESSION['uid']);
		$this->display();
	}
	//用户中心，页面展示
	public function data(){
		R('User/checkLogin');
		if($_POST){
			$m = M('member');
			$data = $_POST;
			if($data['nickname']){
				if(strlen($data['nickname'])>18){
					echo json_encode(array('status'=>0,'info'=>'昵称不能大于6个汉字，18个字符'));exit;
				}
			}
			if($data['mobile']){
				if($m->where("mobile ='".$data['mobile']."'")->find()){
					echo json_encode(array('status'=>0,'info'=>'此电话号码已注册'));exit;
				}
			}
			if($data['email']){
				if($m->where("email ='".$data['email']."'")->find()){
					echo json_encode(array('status'=>0,'info'=>'此邮箱已经注册'));exit;
				}
			}

			if($m->save($data)){
				echo json_encode(array('status'=>1,'info'=>'信息修改成功！'));exit;
			}else{
				echo json_encode(array('status'=>0,'info'=>'信息修改失败！'));exit;
			}
		}
		$this->assign('uid',$_SESSION['uid']);
		$res = M('member')->where("uid ='".$_SESSION['uid']."'")->find();
		$this->assign('data',$res);
		$this->display();
	}
	//账号绑定及退出登录
	public function set(){
		R('User/checkLogin');
		$this->display();
	}
	}
?>