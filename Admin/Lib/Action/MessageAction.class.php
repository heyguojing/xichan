<?php
	class MessageAction extends CommonAction {
		public function index(){
			$M = M('message_car');
			if($_POST && !empty($_POST['keywords'])){
				 $wheres[$_POST['title']] = array('like',"%".$_POST['keywords']."%");
			}
			$count = $M->count();
	        import("ORG.Util.Page");       //载入分页类
	        $page = new Page($count, 15);
	        $showPage = $page->show();
	 		$list =	$M->limit($page->firstRow, $page->listRows)->where($wheres)->order('id desc')->select();
	 		$this->assign('list',$list);
			$this->assign('page',$showPage);
			$this->display();
		}
		public function fore_message(){
			if($_POST){
				$info = $_POST['info'];
				if(!preg_match("/^1[34578]{1}\d{9}$/",$info['tel'])){  
				    echo json_encode(array('status'=>0,'info'=>'请输入一个有效的电话号码！'));exit;  
				}
				
				//短信接口用户名 $uid
				$uid = 'LKSDK00085';
				//短信接口密码 $passwd
				$passwd = '506122';
				//发送到的目标手机号码 $telphone
				$telphone = $info['tel'];
				//短信内容 $message
				$message = $info['content'];
				if($info['template_id'] || !empty($info['temlate_id'])){
					$res = M('message_template')->where("id = {$info['template_id']}")->find();
					if(!$res){
						echo json_encode(array('status'=>0,'info'=>'不存在的模板信息！'));exit;
					}
				}
				if($info['member_id'] && !is_numeric($info['member_id'])){
					echo json_encode(array('status'=>0,'info'=>'会员卡号必须为数字'));exit;
				}
				if($info['people_num'] && !is_numeric($info['people_num'])){
					echo json_encode(array('status'=>0,'info'=>'乘车人数必须为数字'));exit;
				}
				//生成验证码
				$info['code'] = mb_substr($telphone,7,3,'utf-8').Rand(100,999);
				if(M('message_car')->where("code = {$info['code']}")->find()){
					$info['code'] = mb_substr($telphone,7,3,'utf-8').Rand(100,999);
				}				
				$info['published'] = time();
				$info['e_start_time'] = strtotime($info['e_start_time']);				
				$info['e_end_time'] = strtotime($info['e_end_time']) +60*60*23;
				
				if($info['e_start_time'] > $info['e_end_time'] || ($info['e_start_time']+60*60*24)<time() || ($info['e_start_time']+60*60*24)<time()){
					echo json_encode(array('status'=>0,'info'=>'请选择正确的时间！'));exit;
				}
				$info['content'] = str_replace('[]',$info['code'].'(有效时间：'.date('m-d',$info['e_start_time']).'至'.date('m-d',$info['e_end_time']).')',$res['content']);
				
				if(!M('message_car')->add($info)){
					echo json_encode(array('status'=>0,'info'=>'信息发送失败！请稍后再试！'));exit;
				}
				
				$message_tow=rawurlencode(mb_convert_encoding($info['content'], "gb2312", "utf-8"));
				
				$utime = date("Y-m-d H:i:s");
				
				$gateway = "http://mb345.com/ws/batchSend.aspx?CorpID={$uid}&Pwd={$passwd}&Mobile={$telphone}&Content={$message_tow}&Cell=&SendTime=";
				
				$result = file_get_contents($gateway);
				if(  $result == 0 || $result == 1)
				{
					echo json_encode(array('status'=>1,'info'=>'短信发送成功！','url'=>'index'));exit;
				}
				else
				{
					echo json_encode(array('status'=>0,'info'=>'短信发送失败，请稍后再试！'));exit;
				}
			}
			$this->assign('template',M('message_template')->select());
			$this->assign('titlename','发送短信');
			$this->display();
		}
		
		Public function message_temp_add(){
			if($_POST){
				$info = $_POST['info'];
				$info['utime'] = time();
				if(M('message_template')->where("title ='{$info['title']}'")->find()){
					echo json_encode(array('stauts'=>0,'info'=>'此模板名已存在！'));exit;
				}
				if(M('message_template')->add($info)){
					echo json_encode(array('status'=>1,'info'=>'模板添加成功！','url'=>'message_temp_list'));exit;
				}else{
					echo json_encode(array('status'=>0,'info'=>'模板添加失败，请稍后再试！'));exit;
				}
			}			
			$this->assign('titlename','添加短信模板');
			$this->display();
		}
		
		
		//模板列表
		public function message_temp_list(){
			$this->assign('list',M('message_template')->select());
			$this->display();
		}
		
		
		//删除功能
		public function Delete(){
			if($_GET){
				$ids = explode(',',$_GET['id']);
				if(is_array($ids)){
					$wheres = "id in ({$_GET['id']})";
				}else{
					$wheres = "id = {$_GET['id']}";
				}
				if(M($_GET['tname'])->where($wheres)->delete()){
					echo json_encode(array('status'=>1,'info'=>'删除成功！','url'=>'index'));exit;
				}else{
					echo json_encode(array('status'=>0,'info'=>'删除失败，请稍后再试！'));exit;
				}
			}
		}
		
		
		//修改模板信息
		public function edit_template(){
			if(!empty($_GET['id']) && is_numeric($_GET['id'])){
				$res = M('message_template')->where("id={$_GET['id']}")->find();
				if($res){
					$this->assign('info',$res);
				}else{
					$this->redirect('message_temp_list');
				}
			}else{
				$this->redirect('message_temp_list');
			}
			
			if($_POST){
				$info = $_POST['info'];
				$info['utime'] = time();
				if(M('message_template')->save($info)){
					echo json_encode(array('status'=>1,'info'=>'模板信息修改成功！','url'=>'message_temp_list'));exit;
				}else{
					echo json_encode(array('status'=>0,'info'=>'模板信息修改失败，请稍后再试！'));exit;
				}
			}
			$this->assign('titlename','修改模板信息');
			$this->display('message_temp_add');
		}
		
		//显示信息
		public function view(){
			if($_GET['id'] && is_numeric($_GET['id'])){
				$res = M('message_car')->where("id={$_GET['id']}")->find();
				if($res){
					$this->assign('info',$res);
				}else{
					$this->redirect('index');
				}
			}else{
				$this->redirect('index');
			}
			$this->assign('titlename','已发信息展示');
			$this->display();
		}
	}