<?php
	class PushAction extends CommonAction{
		public function shzt(){
			header('Content-type:text/json;charset=utf-8');
			if(IS_POST){
				if(strlen($_POST['username'])>10){
					echo json_encode(array('status'=>0,'info'=>'姓名长度不符合！'));exit;
				}
				if(strlen($_POST['tel_number']) == 11){
					if (preg_match('/^1[34578]{1}\d{9}$/', $_POST['tel_number'])) { 
						$_POST['ctime'] = time();
						$res = M('client')->add($_POST);
						if($res){
							echo json_encode(array('status'=>1,'info'=>'信息登记成功！'));exit;
						}else{
							echo json_ecode(array('status'=>0,'info'=>'信息登记失败，请稍后再试！'));exit;
						}				         
				 } else{
					echo json_encode(array('status'=>0, 'info'=>'请填写十一位的手机号码！'));exit;
				 }
				}else{
					echo json_encode(array('status'=>0, 'info'=>'请填写十一位的手机号码！'));exit;
				}
			}else{
				$ad = M('ad');
				$res = $ad->where('status=1 and cid = 6')->order('paixu asc,id desc')->limit(0,6)->select();
				$adlist = $ad->where('status=1 and cid = 7')->order('paixu asc')->limit(0,3)->select();
				$adyh = $ad->where('status=1 and cid = 8')->order('id desc,paixu asc')->find();
				$this->assign('adyh',$adyh);
				$this->assign('adlist',$adlist);
				$this->assign('banner',$res);
				$this->display();
			}
		}
		
		
		
		public function zjzuozhen(){
			$res = M('ad')->field('`id`,`pic`')->where('status=1 and cid= 9')->order('paixu asc,id desc')->select();
			$this->assign('list',$res);
			$this->display();
		}
		public function special928(){
			if($_POST){
				if(!preg_match('/^1[34578]{1}\d{9}$/', $_POST['tel_number'])){
					echo json_encode(array('status'=>0,'info'=>'请输出十一位手机号码！'));exit;
				}
				if(strlen($_POST['username'])>12 || strlen($_POST['username'])<2){
					echo json_encode(array('status'=>0,'info'=>'姓名长度不符合！'));exit;
				}
				$_POST['ctime'] = time();
				$res = M('client')->add($_POST);
				if($res){
					echo json_encode(array('status'=>1,'info'=>'信息登记成功！'));exit;
				}else{
					echo json_ecode(array('status'=>0,'info'=>'信息登记失败，请稍后再试！'));exit;
				}				         
			}
			$this->display();
		}

		public function special1101(){
			if($_POST){
				$M = M('client_new');
				if($M->where("tel =".$_POST['tel'])->find()){
					echo json_encode(array('status'=>0,'info'=>'信息已提交，请勿重复提交！'));exit;
				}
				if(!preg_match('/^1[34578]{1}\d{9}$/', $_POST['tel'])){
					echo json_encode(array('status'=>0,'info'=>'请输出正确的手机号码！'));exit;
				}
				if(strlen($_POST['username'])>12 || strlen($_POST['username'])<2){
					echo json_encode(array('status'=>0,'info'=>'姓名长度不符合！'));exit;
				}
				if(!is_numeric($_POST['project']) || $_POST['project'] == ''){
					echo json_encode(array('status'=>0,'info'=>'请选择一个项目！'));exit;
				}
				if($_POST['credit'] && !empty($_POST['credit'])){
					if(!is_numeric($_POST['credit']) || strlen($_POST['credit'])>4){
						echo json_encode(array('status'=>0,'info'=>'请输入正确的芝麻信用分！'));exit;
					}
				}
				$_POST['ctime'] = time();
				$res = $M->add($_POST);
				if($res){
					echo json_encode(array('status'=>1,'info'=>'信息登记成功！','url'=>'reload'));exit;
				}else{
					echo json_ecode(array('status'=>0,'info'=>'信息登记失败，请稍后再试！'));exit;
				}		
			}
			$arr = array(
				1=>'韩式双眼皮    日供18元',
				2=>'眼袋          日供11元',
				3=>'双眼皮+开眼角 日供36元',
				4=>'除皱眉间纹    日供7元',
				5=>'动感飘眉      日供11元',
				6=>'隆鼻          日供11元',
				7=>'隆鼻加耳软骨  日供31元',
				8=>'丰胸          日供51元',
				9=>'吸脂腰腹综合  日供60元',
				10=>'自体脂肪填充  日供31元'
			);
			$this->assign('data',$arr);
			$this->display();
		}
	}
?>