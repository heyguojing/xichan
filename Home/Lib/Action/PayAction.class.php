<?php
class PayAction extends Action{
	
	 public function _initialize() {
		vendor('Alipay.Corefunction');
		vendor('Alipay.Md5function');
		vendor('Alipay.Notify');
		vendor('Alipay.Submit');
		vendor('Alipay.rsafunction');  
	}
	
	  public function alipay(){
    		R('User/checkLogin');
		 /**************************支付宝配置**************************/
		  $pay = M('alipay')->where('id=1')->find();
		  $alipay_config['partner']        = $pay['partner'];
		  $alipay_config['seller_email']    = $pay['alipayname'];
		  $alipay_config['key']            = $pay['key'];
		  $alipay_config['private_key_path']	= 'key/rsa_private_key.pem';
          $alipay_config['ali_public_key_path']= 'key/alipay_public_key.pem';
		  $alipay_config['sign_type']    = strtoupper('MD5');
		  $alipay_config['input_charset']= strtolower('utf-8');
		  $alipay_config['cacert']    = getcwd().'\\cacert.pem';
		  $alipay_config['transport']    = 'http';
		   
		  $notify_url = "http://" . $_SERVER ['SERVER_NAME'].U("Pay/notify_url"); 
          $call_back_url = "http://" . $_SERVER ['SERVER_NAME'].U("Pay/return_url");
          $merchant_url = "http://" . $_SERVER ['SERVER_NAME'].U("Member/index");
		  /**************************支付宝配置**************************/  
		   $price=$_POST['WIDtotal_fee'];//支付金额
		   $projectid = rtrim($_POST['WIDprojectid'],',');//订单项目的id
		   $titlename = rtrim($_POST['WIDsubject'],',');//订单主题
		   $WIDreceive_mobile = $_POST['WIDreceive_mobile'];
		   if(!$_SESSION['order_num'] || $_SESSION['order_num'] == ''){
			   	$out_trade_no="M".Date("Ymd-His",time()).'-'.substr(time(),'-4').rand(100,999);//商户订单号
		   }else{
		   		$out_trade_no=$_SESSION['order_num'];
		   }
		/**************************查询计算总额是否正确，正确则存入订单表**************************/
		   $res = explode(',',$projectid); //按照逗号分隔字符串
		   if(!$_SESSION['goods_data'] || empty($_SESSION['goods_data'])){
		   		$this->error('商品参数错误!',$_SERVER[HTTP_REFERER]);exit;
		   }
		   for($i=0;$i<count($res);$i++){
		   		$arr1 = explode('_',$res[$i]); //分隔出商品id和数量
		   		$result = M('goods')->field('`id`,`price`,`status`,`goods_name`')->where("id='".(int)$arr1[0]."' and status=1")->find();
		   		if(!$result){
		   			$this->error('商品参数错误!',$_SERVER[HTTP_REFERER]);exit;
		   		}

		   		if(!is_numeric($arr1[0]) || !is_numeric($arr1[1])){
		   			$this->error('商品参数错误!',$_SERVER[HTTP_REFERER]);exit;
		   		}
		   		$arr[$i]['status'] 		 = $result['status'];
		   		$arr[$i]['goods_num']	 = $arr1[1];
		   		$arr[$i]['gid']			 = $arr1[0];
		   		$arr[$i]['goods_price']  = $result['price'] * $arr1[1];
		   }
		   //利用查询到的数据和商品的数量，计算出总价钱
		   foreach($arr as $key=>$val){
		   		$total_price += $val['goods_price'];
		   		$total_num 	 += $val['goods_num'];
		   		$gids 		 .= $val['gid'].',';	
		   }
		   if($price != $total_price){
	   			$this->error('商品参数错误!',$_SERVER[HTTP_REFERER]);exit;
		   }
		   //组合订单表的参数
		   $order_arr['gid_num']  	= $projectid;
		   $order_arr['title']		= $titlename;
		   $order_arr['total_num']  = $total_num;
		   $order_arr['uid']		= $_SESSION['uid'];
		   $order_arr['price']		= $total_price;
		   $order_arr['gids']		= rtrim($gids,',');
		   $order_arr['order_num']	= $out_trade_no;
		   $order_arr['status']		= 2;
		   $order_arr['ctime']		= time();
		   $order_arr['ctime']		= time();
		   $order = M("order");
		   if($res = $order->where("order_num = '".$out_trade_no."'")->find()){
		   		if($res['status'] !=2){
		   			$this->error('订单已存在，请稍后再试!',$_SERVER[HTTP_REFERER]);exit;
		   		}
		   }
		   //如果订单生成失败，则不再向下执行
		   if(!$_SESSION['order_num'] || $_SESSION['order_num'] ==''){
			   if(!M('order')->add($order_arr)){
			   		$this->error('订单生成失败，请稍后再试!',$_SERVER[HTTP_REFERER]);exit;
			   }
		   }else{
		   		$order_id = $order->field('`id`')->where('order_num="'.$out_trade_no.'"')->find();
		   		$arr1['id']  	     = $res['id'];
		    	$arr1['type']         = 1; //支付宝支付
		    	if(!$order->save($arr1)){
		    		$this->redirect('订单异常，请稍候再试!',U('order/myorder'));exit;
		    	}
		   }
		   
		/**************************存入充值记录**************************/
			vendor('Alipay.Corefunction');
			vendor('Alipay.Md5function');
			vendor('Alipay.Notify');
			vendor('Alipay.Submit');
			vendor('Alipay.rsafunction'); 
		$data['mobile']=$WIDreceive_mobile;
		$data['rmb']=$price;
		$data['projectid'] = $order_arr['gids'];
		$data['type']="支付宝";
		$data['code']=$out_trade_no;
		$data['time']=$_SERVER[REQUEST_TIME];
		$data['yes']=0;
		$pay = M("Pay");
		//再次支付不再重复提交订单
	   if(!$_SESSION['order_num'] || $_SESSION['order_num'] ==''){
		    $ispay=M('Pay')->add($data);
		    if(!$ispay){
		        $this->error("订单写入失败");//提交过来入库,如果入库失败,则不往下执行支付宝
		    }
	   }
       $format = "xml";//必填，不需要修改//返回格式
       $v = "2.0";//必填，不需要修改//请求号
       $req_id = date('Ymdhis');//必填，须保证每次请求都是唯一//**req_data详细信息**
       $subject = $titlename;
       $total_fee = $price;//必填//付款金额
       $req_data = '<direct_trade_create_req>
       <notify_url>' . $notify_url . '</notify_url>
       <call_back_url>' . $call_back_url . '</call_back_url>
       <seller_account_name>' . trim($alipay_config['seller_email']) . '</seller_account_name>
       <out_trade_no>' . $out_trade_no . '</out_trade_no>
       <subject>' . $subject . '</subject>
       <total_fee>' . $total_fee . '</total_fee>
       <merchant_url>' . $merchant_url . '</merchant_url>
       </direct_trade_create_req>';//必填


     /************************************************************/
       //订单生成成功，删除原购物车的商品
			if($_SESSION['order_num'] && $_SESSION['order_num'] != ''){//如果是订单中心传过来的订单号
				unset($_SESSION['order_num']);
				unset($_SESSION['goods_data']);
			}else{
				//删除当前会员购物车信息
				$gids = explode(',',$order_arr['gids']);
				if(count($gids)>1){
					$wheres = 'gid in ('.$order_arr["gids"].') and uid = '.$_SESSION['uid'];
				}else{
					$wheres = 'gid = '.$order_arr["gids"].' and uid = '.$_SESSION['uid'];
				}
				//根据组建的$wheres删除购物车的信息
				$res = M('shopcar')->where($wheres)->delete();
				if($res){
					unset($_SESSION['goods_data']);
				}else{
					$this->error('未知错误',$_SERVER[HTTP_REFERER]);exit;
				}
				//清除session中存在的商品信息
			}  


     /************************************************************/
//构造要请求的参数数组，无需改动
$para_token = array(
		"service" => "alipay.wap.trade.create.direct",
		"partner" => trim($alipay_config['partner']),
		"sec_id" => trim($alipay_config['sign_type']),
		"format"	=> $format,
		"v"	=> $v,
		"req_id"	=> $req_id,
		"req_data"	=> $req_data,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);
//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestHttp($para_token);
//URLDECODE返回的信息
$html_text = urldecode($html_text);
//解析远程模拟提交后返回的信息
$para_html_text = $alipaySubmit->parseResponse($html_text);
//获取request_token
$request_token = $para_html_text['request_token'];
/**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/
//业务详细
$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
//必填
//构造要请求的参数数组，无需改动
$parameter = array(
		"service" => "alipay.wap.auth.authAndExecute",
		"partner" => trim($alipay_config['partner']),
		"sec_id" => trim($alipay_config['sign_type']),
		"format"	=> $format,
		"v"	=> $v,
		"req_id"	=> $req_id,
		"req_data"	=> $req_data,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);
//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');
echo $html_text;

}

public function callbackurl(){
	
	$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	pre($verify_result);
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	//商户订单号
	$out_trade_no = $_GET['out_trade_no'];

	//支付宝交易号
	$trade_no = $_GET['trade_no'];

	//交易状态
	$result = $_GET['result'];


	//判断该笔订单是否在商户网站中已经做过处理
		//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
		//如果有做过处理，不执行商户的业务程序
		
	echo "验证成功<br />";

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    echo "验证失败";
}
	
	
	}


//支付宝同步跳转
public function return_url(){
	header('Content-type:text/html;charset=utf-8');
	/**************************支付宝配置**************************/
		  $pay = M('alipay')->where('id=1')->find();
		  $alipay_config['partner']        = $pay['partner'];
		  $alipay_config['seller_email']    = $pay['alipayname'];
		  $alipay_config['key']            = $pay['key'];
		  $alipay_config['private_key_path']	= 'key/rsa_private_key.pem';
          $alipay_config['ali_public_key_path']= 'key/alipay_public_key.pem';
		  $alipay_config['sign_type']    = strtoupper('MD5');
		  $alipay_config['input_charset']= strtolower('utf-8');
		  $alipay_config['cacert']    = getcwd().'\\cacert.pem';
		  $alipay_config['transport']    = 'http';
		   
		  // $notify_url = "http://" . $_SERVER ['SERVER_NAME'].U("Pay/notify_url"); 
    //       $call_back_url = "http://" . $_SERVER ['SERVER_NAME'].U("Pay/return_url");
    //       $merchant_url = "http://" . $_SERVER ['SERVER_NAME'].U("Member/index");
		  /**************************支付宝配置**************************/  
		   // $price=$_POST['WIDtotal_fee'];//支付金额
		   // $projectid = $_POST['WIDprojectid'];
		   // $titlename = $_POST['WIDsubject'];
		   // $WIDreceive_mobile = $_POST['WIDreceive_mobile'];
		   // $out_trade_no="M".Date("YmdHis",time()).time();//商户订单号
		/**************************存入充值记录**************************/
			vendor('Alipay.Corefunction');
			vendor('Alipay.Md5function');
			vendor('Alipay.Notify');
			vendor('Alipay.Submit');
			vendor('Alipay.rsafunction'); 
    $alipayNotify = new AlipayNotify($alipay_config);
    $verify_result = $alipayNotify->verifyReturn();
    if($verify_result) {
        //商户订单号
        $out_trade_no = $_GET['out_trade_no'];
        //支付宝交易号
        $trade_no = $_GET['trade_no'];
        //交易状态
        //这里由自己写,也可以在这里直接写充值到账,也可以判断是否充值成功
        //异步通知写到账,我在这判断异步是否处理,这个由自己发挥
        $trade_status = $_GET['trade_status'];
        $result = $_GET['result'];
        if($result == 'success'){
	        $where['code']=$out_trade_no;
	        $wheres['order_num'] = $out_trade_no;
	        $F=M('Pay')->where($where)->find(); //查询支付订单信息pay
	        $order = M('order')->where($wheres)->find();
	        if($F['yes']==1 && $order['status'] == 3 ){
	            $this->success("支付成功",U('order/myorder'));
	        }elseif($F['yes'] !=1 || $order['status'] !=3){
	            $this->success("支付失败或正在处理,请等待几分钟",U('order/myorder'));
	        }
        }else{
        	echo '支付失败';
        }
    }else {
        echo "验证失败";
    }
}
//支付宝异步通知
public function notify_url(){
	/**************************支付宝配置**************************/
		 $pay = M('alipay')->where('id=1')->find();
		  $alipay_config['partner']        = $pay['partner'];
		  $alipay_config['seller_email']    = $pay['alipayname'];
		  $alipay_config['key']            = $pay['key'];
		  $alipay_config['private_key_path']	= 'key/rsa_private_key.pem';
          $alipay_config['ali_public_key_path']= 'key/alipay_public_key.pem';
		  $alipay_config['sign_type']    = strtoupper('MD5');
		  $alipay_config['input_charset']= strtolower('utf-8');
		  $alipay_config['cacert']    = getcwd().'\\cacert.pem';
		  $alipay_config['transport']    = 'http';
		   
		  $notify_url = "http://" . $_SERVER ['SERVER_NAME'].U("Pay/notify_url"); 
          $call_back_url = "http://" . $_SERVER ['SERVER_NAME'].U("Pay/return_url");
          $merchant_url = "http://" . $_SERVER ['SERVER_NAME'] .U("Member/index");
		  // /**************************支付宝配置**************************/  
		  //  $price=$_POST['WIDtotal_fee'];//支付金额
		  //  $projectid = $_POST['WIDprojectid'];
		  //  $titlename = $_POST['WIDsubject'];
		  //  $WIDreceive_mobile = $_POST['WIDreceive_mobile'];
		  //  $out_trade_no="M".Date("YmdHis",time()).time();//商户订单号
		/**************************存入充值记录**************************/
			vendor('Alipay.Corefunction');
			vendor('Alipay.Md5function');
			vendor('Alipay.Notify');
			vendor('Alipay.Submit');
			vendor('Alipay.rsafunction'); 
    $alipayNotify = new AlipayNotify($alipay_config);
    $verify_result = $alipayNotify->verifyNotify();
    if($verify_result) {//验证成功
    	$M = M('pay');
        $notify_arr = xmlToArray($_POST['notify_data']); //接收异步xml参数转为数组
        
        $trade_no 	  = $notify_arr['trade_no'];//支付宝交易号
        $out_trade_no = $notify_arr['out_trade_no'];//账户订单号
        $trade_status = $notify_arr['trade_status']; //交易状态
        $trade_price  = $notify_arr['total_fee']; //交易金额

        if($trade_status == 'WAIT_BUYER_PAY') {
	        //该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
	        $res = $M->where("code ='".$out_trade_no."'")->find();
	        if($res['yes'] != 0){
    			logResult("订单信息修改失败-".$out_trade_no.",金额:".$trade_status."\r\n");
	        }
            //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                
            echo "success";        //请不要修改或删除
    
            //调试用，写文本函数记录程序运行情况是否正常
            logResult("产生了交易记录，但没有付款-".$out_trade_no."\r\n");
        }else if( $trade_status == 'TRADE_SUCCESS' || $trade_status == 'TRADE_FINISHED' ) {
		        //该判断表示买家已经付款，这笔交成功
		       $res =  $M->where("code='".$out_trade_no."'")->find();
		       	//判断该笔订单是否在商户网站中已经做过处理
		        if($res['yes'] != 1){
		       		$arr['yes'] 	 = 1;
		       		$arr['type']	 = '支付宝';
		       		$arr['id']		 = $res['id'];
		       		$arr['trade_no'] = $trade_no;
			   		if(!$M->save($arr)){
						logResult("支付订单信息修改失败pay-".$out_trade_no.",金额:".$trade_price."\r\n");
			   		}
		        }
		   		//查询订单表信息
		   		$order = M('order')->where("order_num='".$out_trade_no."'")->find();
		   		if($order){//能够查询到相关订单
		   			if($order['price'] == $trade_price){//金额与回调的订单金额相等
		   				if($order['status'] == 2){
		   					$arr1['status']  = 3;
		   					$arr1['id']		= $order['id'];
		   					if(!M('order')->save($arr1)){
		            			logResult("订单信息修改失败order-".$out_trade_no.",金额:".$trade_price."\r\n");
		   					}
		   					//将订单的信息存入子订单中
		   					//根据order中gids的信息，查询出商品的详情。然后整理数据分别出入到子订单表中
		   					// $goods_data = M('goods')->where('id in('.$order['gid'].')')->select();
		   					$g_ns = explode(',',$order['gid_num']);//分隔出多个商品的信息
		   					$goods = M('goods');
		   					foreach($g_ns as $k => $v){
		   						$gid_nums 			= explode('_',$v);//分割出商品的id和num
		   						$goods_data 		= $goods->where('id ='.$gid_nums[0])->find();
		   						$c_arr['order_id']  = $order['id'];
		   						$c_arr['gid'] 		= $goods_data['id'];
		   						$c_arr['surplus']	= $gid_nums[1];
		   						$c_arr['num']		= $gid_nums[1];
		   						$c_arr['status']	= 1;
		   						M('order_children')->add($c_arr);
		   					}


		   					//发送短信
		   					$notice = M('message_notice');
							//短信发送内容拼装
							//短信接口用户名 $uid
							$uid = 'LKSDK00085';
							//短信接口密码 $passwd
							$passwd = '506122';
							//根据订单表的uid查询出用户的电话号码
							$mobile = M('member')->field('`uid`,`mobile`')->where("uid=".$order['uid'])->find();
							//发送到的目标手机号码 $telphone
							$telphone = $mobile['mobile'];
							//查询出之前是否获取过验证码且未过期，有则发送给用户，没有则重新获取
							
							//短信内容 $message
							$message = '您已成功购买'.$order['title'].'美容服务及产品，您的订单号是:'.$out_trade_no.'，欢迎到店使用!如有疑问请致电4000286666!';
							$message_tow=rawurlencode(mb_convert_encoding($message, "gb2312", "utf-8"));
							//验证码请求时间	
							$data['ctime'] = time();
							//验证码内容
							$data['content'] = $message;
							//验证码用户id
							$data['uid'] = $order['uid'];
							//用户的手机号码
							$data['orderid'] = $order['id'];
							$data['mobile'] = $telphone;
							$gateway = "http://mb345.com/ws/batchSend.aspx?CorpID=".$uid."&Pwd=".$passwd."&Mobile=".$telphone."&Content=".$message_tow."&Cell=&SendTime=";
							//发送短信	
							$result = file_get_contents($gateway);
							$res = $notice->add($data);
		   				}
		   			}else{
			            logResult("订单交易总额不正确:".$out_trade_no."\r\n");
		   			}
		   		}else{
		            logResult("没有查询到相关订单信息order:".$out_trade_no."\r\n");
		   		}
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //如果有做过处理，不执行商户的业务程序
                
            echo "success";        //请不要修改或删除
            //调试用，写文本函数记录程序运行情况是否正常
            logResult("支付成功-".$out_trade_no."\r\n");
        } else {
            //其他状态判断
            echo "success";
    
            //调试用，写文本函数记录程序运行情况是否正常
       		 logResult('其他失败:'.var_export($notify_arr,ture));

        }
    }else {
        //验证失败
        echo "fail";
        logResult ("验证失败-{$_POST['out_trade_no']}\r\n");
    }   
    
}

}
?>