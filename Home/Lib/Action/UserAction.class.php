<?php
class UserAction extends CommonAction{
	public function index(){		
		ob_clean();
		if($_SESSION['is_login']==1 && !empty($_SESSION['user']) && $_SESSION['expire'] >time()){
			$this->redirect('Member/index');
		}else{
			if(IS_POST){
				  $datas = $_POST;
				  header("Content-Type:text/html; charset=utf-8");
	              header('Content-Type:application/json; charset=utf-8');
	              $M = M("member");
	              //判断是否是电话号码，组合查询条件
	              if(is_Mobile($datas['email']) == false){
	              	$wheres = "email='".$datas['email']."'";
	              }else{
	              	$wheres = "mobile ='".$datas['email']."'";
	              }
				  if ($M->where($wheres)->count() >= 1) {
					  $userinfo = $M->where($wheres)->find();
					  if($userinfo['status'] != 1){
					  	echo json_encode(array('status'=>0,'info'=>'账号异常，请联系管理员!'));exit;
					  }
					  if ($userinfo['pwd'] == encrypt($datas['pwd'])) {
						  $_SESSION['USER_AUTH_KEY'] = $userinfo['uid'];
						  $_SESSION['is_login'] = 1;
						  $_SESSION['expire'] = time()+60*60*72;
						  $data['uid']=$userinfo['uid'];
						  $data['login_time'] = time();
						  $data['login_ip'] = $_SERVER[REMOTE_ADDR];
						  $M->save($data);
						  $_SESSION['uid'] = $userinfo['uid'];
						  $_SESSION['user'] = $userinfo['email'];
						  //如果用户点击了记住密码，则存入cookie
						  if($datas['chkpass'] == 1){
						  	 setcookie("email",$userinfo['email'],time()+3600*24*7,'/');
  							 setcookie("pwd",$userinfo['pwd'],time()+3600*24*7,'/');
						  }
						  //判断上一级url的路径是否设置，如果有设置则跳转到之前浏览的页面，如果没有则跳转到会员中心
						  echo json_encode(array('status' => 1, 'info' => "登录成功", 'url' => U('Member/index')));exit;
						 
					  } else {
						  echo json_encode(array('status' => 0, 'info' => "账号或密码错误"));exit;
					  }
				  } else {
					  echo json_encode(array('status'=>0,'info'=>"账号为:".$datas['email']."不存在！"));exit;
				  }
				}else{
					$this->display('login');
				}
			}	
		}
	public function login($type = null){
		empty($type) && $this->error('参数错误');

		//加载ThinkOauth类并实例化一个对象
		import("ORG.ThinkSDK.ThinkOauth");
		$sns  = ThinkOauth::getInstance($type);

		//跳转到授权页面
		redirect($sns->getRequestCodeURL());
	}

	//授权回调地址
	public function callback($type = null, $code = null){
		(empty($type) || empty($code)) && $this->error('参数错误');
		
		//加载ThinkOauth类并实例化一个对象
		import("ORG.ThinkSDK.ThinkOauth");
		$sns  = ThinkOauth::getInstance($type);

		//腾讯微博需传递的额外参数
		$extend = null;
		if($type == 'tencent'){
			$extend = array('openid' => $this->_get('openid'), 'openkey' => $this->_get('openkey'));
		}

		//请妥善保管这里获取到的Token信息，方便以后API调用
		//调用方法，实例化SDK对象的时候直接作为构造函数的第二个参数传入
		//如： $qq = ThinkOauth::getInstance('qq', $token);
		$token = $sns->getAccessToken($code , $extend);

		//获取当前登录用户信息
		if(is_array($token)){
			$user_info = A('Type', 'Event')->$type($token);

			echo("<h1>恭喜！使用 {$type} 用户登录成功</h1><br>");
			echo("授权信息为：<br>");
			dump($token);
			echo("当前登录用户信息为：<br>");
			dump($user_info);
		}
	}	

	//会员注册的功能
	public function regist(){
		ob_clean();
		if(IS_POST){
			$post = $_POST;
			header("Content-Type:text/html; charset=utf-8");
          	header('Content-Type:application/json;charset=utf-8');
		    $email = trim($post['email']);
		    $pwd = trim($post['pwd']);
			$mobile = trim($post['mobile']);
			$nickname = trim($post['nickname']);
			$M = M("member");
			if($M->where("mobile='".$mobile."'")->count() >=1){
				echo json_encode(array('status'=>0,'info'=>'此电话号码已被注册过！'));exit;
			}
			if(strlen($nickname)>18){
				echo json_encode(array('status' => 0, 'info' => '姓名长度不能超过6个字符'));exit;
			}
			// if(is_specialCharacter($nickname) == true){
			// 	echo json_encode(array('status' => 0, 'info' => '姓名中不能含有特殊字符'));exit;
			// }
			if($M->where("`email`='" . $post['email'] . "'")->count() >= 1) {
				echo json_encode(array('status' => 0, 'info' => "已存在邮箱为：" . $post["email"] . '的账号！'));exit;
			  }
			 $add['email']=$email;
			 $add['pwd']=encrypt($pwd);
			 $add['mobile']=$mobile;
			 $add['nickname'] = $nickname;
			 $add['reg_date']=time();
			 $add['login_time']=time();
			 $add['reg_ip'] = $_SERVER['REMOTE_ADDR'];
			 $add['login_ip'] = $_SERVER['REMOTE_ADDR'];
			 $addnew = $M->add($add);
			 if($addnew){
				  $_SESSION['uid']=$addnew;
				  $_SESSION['USER_AUTH_KEY'] = $addnew;
				  $_SESSION['expire'] = time()+3600*24*7;
				  $_SESSION['is_login'] = 1;
				  $_SESSION['user'] = $add['email'];
				  echo json_encode(array('status' => 1, 'info' => '注册成功，正在跳转到会员中心', 'url' => U('Member/index')));
			  }else{
			  	  echo json_encode(array('status' => 0, 'info' => '对不起，系统错误，请刷新重试！'));
			  }
		}else{
		    $this->display();
		  }
		}	
		//检查用户是否登录
		public function checkLogin(){
		//判断标识是否为1,1已登录
		if(!$_SESSION['is_login'] || $_SESSION['is_login'] != 1 ){
			//判断用户是否在之前登录就记住密码，如果记住密码则直接读取信息进行验证登录
			if(!empty($_COOKIE['email']) && !empty($_COOKIE['pwd'])){
				$res = M('member')->where("email ='".$_COOKIE['email']."'")->find();
				if($res && $res['status'] ==1){
					if($res['pwd'] == $_COOKIE['pwd']){
						$_SESSION['uid'] = $res['uid'];
						$_SESSION['is_login'] = 1;
						$_SESSION['email'] = $res['email'];
						$_SESSION['USER_AUTH_KEY'] = $res['uid'];
					 	$_SESSION['expire'] = time()+60*60*72;
						$this->redirect('Member/index');
					}
				}else{
						$this->redirect('User/index');
					}
			}
			$this->redirect('User/index');
		}
		//判断用户的uid是否存在
		if(!$_SESSION['uid'] || empty($_SESSION['uid'])){
			$this->redirect('User/index');
		}
		//判断用户登录时间是否过期
		if(!$_SESSION['expire'] || empty($_SESSION['expire']) || $_SESSION['expire'] < time()){
			unset($_SESSION['expire']);
		//判断用户登录时间是否过期
			$this->redirect('User/index');	
		}
	}

	}
?>