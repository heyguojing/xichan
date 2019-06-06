<?php
	
	class MessageAction extends CommonAction{
		public function verify(){
			if($_POST){
				if(!is_numeric($_POST['tel'])){
					echo json_encode(array('status'=>0,'info'=>'电话尾数必须为4位数字！'));exit;
				}
				if(!is_numeric($_POST['code'])){
					echo json_encode(array('status'=>0,'info'=>'您好，你的验证码未通过！'));exit;
				}
				
				$res = M('message_car')->where("code = '{$_POST['code']}'")->find();
				//通过验证码查询是否存在信息
				if($res){
					
					//判断手机号码后4位是否正确
					if(mb_substr($res['tel'],7,4,'utf-8') === $_POST['tel']){
						//判断当前时间是否在有效期之内
						if(time() >= $res['e_start_time'] && time()<= $res['e_end_time']){
							if($res['is_verify'] == 1){
								//改变验证码的状态，添加验证的时间，成功则跳转验证成功页面
								$info['is_verify'] = 0;
								$info['verify_time'] = time();
								$info['id'] = $res['id'];
								if(M('message_car')->save($info)){
									echo json_encode(array('status'=>1,'info'=>'验证通过，欢迎乘车！','url'=>'success','num'=>$res['people_num']));exit;
								}
							}else{
								echo json_encode(array('status'=>0,'info'=>'对不起，此礼包码已被验证乘车！'));exit;
							}
						}else{
							echo json_encode(array('status'=>0,'info'=>'对不起，请在指定时间内验证！'));exit;
						}
					}
				}
				echo json_encode(array('status'=>0,'info'=>'对不起，验证未通过！'));exit;
			}
			$this->display();
		}
	}