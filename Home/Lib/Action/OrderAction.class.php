<?php
class OrderAction extends CommonAction {
	 /** 
	 * 支付订单 
	 */  
	  public function pay(){   
		  print_r($_GET);  
		  header('Content-type: text/html; charset=utf-8');    
		  $id = args("id", 0);  
		  $DAO = new OrderModel();  
		  $order = $DAO->where("id=".$id)->find(); 
		  $error = "";  
		  if(!isset($order)){  
			$error = "订单不存在";  
		  }else if($order['status'] == 1){  
			$error = "此订单已经完成，无需再次支付！";  
		  } else if($order['status'] == 2){  
			$error = "此订单已经取消，无法支付，请重新下单！";   
		  }   
		  if($error != ""){  
			$this->_FAIL("系统错误",$error,$this->getErrorLinks());   
		  return ;   
		  }   
		  //支付宝  
		  if($order['payment'] == 'alipay'){  
			$this->payWithAlipay($order);   
		  }    
		  else if($order['payment'] == 'ecpss'){   
			$this->payWithEcpss($order);   
		  }   
		  else if($order['payment'] == 'dinpay'){    
			$this->payWithDinpay($order);   
		  }  
	  } 
	  /** 
	  * 以支付宝形式支付 
	  * @param unknown_type $order 
	  */  
	 private function payWithAlipay($order){  
			//引入支付宝相关的文件  
			require_once(VENDOR_PATH."Alipay/alipay.config.php");  
			require_once(VENDOR_PATH."Alipay/lib/alipay_submit.class.php");   
	
			$payment_type = "1"; //支付类型  
			$notify_url = C("HOST")."index.php/Order/notifyOnAlipay"; //必填，不能修改  服务器异步通知页面路径    
			$return_url = C("HOST")."index.php/Order";//页面跳转同步通知页面路径     
			$seller_email = $alipay_config['seller_email']; //卖家支付宝帐户 
			$out_trade_no = $order['tradeNo']; //必填  商户订单号, 从订单对象中获取  
			$subject = $order['subject'];  //商户网站订单系统中唯一订单号，必填  订单名称 
			$price = $order['price'];//必填  //付款金额    
			$body = $order['subject'];  //必填
			$show_url = C('HOST');   //商品展示地址  
	  
		   //构造要请求的参数数组，无需改动  
		  $parameter = array(  
		  "service" => "create_partner_trade_by_buyer",  
		  "partner" => trim($alipay_config['partner']),  
		  "payment_type"=> $payment_type,  
		  "notify_url"=> $notify_url,  
		  "return_url"=> $return_url,  
		  "seller_email"=> $seller_email,  
		  "out_trade_no"=> $out_trade_no,  
		  "subject"=> $subject,   
		  "price"=> $price,  
		  "quantity"=> "1",  
		  "logistics_fee"=> "0.00",  
		  "logistics_type"=> "EXPRESS",  
		  "logistics_payment"=> "SELLER_PAY",  
		  "body"=> $body,  
		  "show_url"=> $show_url,  
		  "receive_name"=> "",  
		  "receive_address"=> "",  
		  "receive_zip"=> "",  
		  "receive_phone"=> "",  
		  "receive_mobile"=> "",  
		  "_input_charset"=> trim(strtolower($alipay_config['input_charset']))  
		  );  
			 
		  //建立请求  
		  $alipaySubmit = new AlipaySubmit($alipay_config);  
		  $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "去支付");  
		  echo $html_text;  
	 } 
	  /** 
      * 支付宝异步通知 
      */  
     public function notifyOnAlipay(){  
        require_once(VENDOR_PATH."Alipay/alipay.config.php");  
        require_once(VENDOR_PATH."Alipay/lib/alipay_notify.class.php");
		$orderLogDao = new OrderLogModel();  
		$alipayNotify = new AlipayNotify($alipay_config); //计算得出通知验证结果 
		$verify_result = $alipayNotify->verifyNotify(); 
		if($verify_result) {//验证成功  
		$out_trade_no = $_POST['out_trade_no'];//商户订单号    
		$trade_no = $_POST['trade_no'];//支付宝交易号  
		$DAO = new OrderModel(); //根据订单号获取订单 
		$order = $DAO->where("tradeNo='".$out_trade_no."'")->find();  
		if(!isset($order)){  //如果订单不存在，设置为0
		$orderId = 0;  
		}  
		else{  
		$orderId = $order['id'];  
		}  
		//交易状态  
		$trade_status = $_POST['trade_status'];  
		$log = "notify from Alipay, trade_status=".$trade_status." alipay sign=".$_POST['sign']; 
		$orderLog['order_id'] = $orderId;  
		$orderLog['addDate'] = sqlDate();  
		if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {  
		//该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款 
		   }  
	   /* 
		* 成功付款后，进行积分操作 
		*/  
		else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {  
		//该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货  
			  if(isset($order) && $order['status'] == 0){  
			  $resultInfo = $this->doAfterPaySuccess($DAO, $order);  
			  $log.= $resultInfo;    
			  }  
		   }  
		else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {  
		//该判断表示卖家已经发了货，但买家还没有做确认收货的操作  
		   }  
		else if($_POST['trade_status'] == 'TRADE_FINISHED') {  
		//该判断表示买家已经确认收货，这笔交易完成
		   }  
		else {  
		   }  
		 /* 
		  * 保存orderlog 
		  */  
		 $orderLog['log'] = $log;  
		 $orderLogDao->add($orderLog);  
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
		echo "success"; //返回成功标记给支付宝  
		}  
		else {  
		   //验证不通过时，也记录下来  
		   $orderLog['log'] = "notify from Alipay, 但是验证不通过，sign=".$_POST['sign'];  
		   $orderLog['order_id'] = -1;  
		   $orderLog['addDate'] = sqlDate();  
		   $orderLogDao->add($orderLog);  
		   echo "fail";//验证失败   
		}   
     }

     //我的订单内容
     public function myorder(){
     	R('User/checkLogin');
     	$uid = $_SESSION['uid'];
     	if($_POST){
     		$wheres = "order_num = '".$_POST['keywords']."' and uid = ".$uid; 
     	}else{
     		$wheres = 'uid = '.$uid;
     	}
     	$res = M('order')->field('`id`,`order_num`,`uid`,`status`,`price`,`gid_num`,`total_num`,`gids`')->where($wheres)->order('ctime desc')->select();
     	$goods = M('goods');
     	foreach($res as $key=>$val){
     		$res[$key]['goods'] = $goods->where('id in('.$val['gids'].')')->select();
     		$gid_num = explode(',',$val['gid_num']);
     		if(count($gid_num)>1){
     			foreach($gid_num as $k=>$v){
     				$num = explode('_',$v);
     				$res[$key]['goods'][$k]['num'] = $num[1];
     				$res[$key]['goods'][$k]['gid'] = $num[0];
     			}
     		}else{
     			$num = explode('_',$gid_num[0]);
 				$res[$key]['goods'][0]['num'] = $num[1];
 				$res[$key]['goods'][0]['gid'] = $num[0];
     		}
     	}
     	$this->assign('restart','javascript:;');
     	$this->assign("list",$res);
     	$this->display();
     }   
     //重新提交订单
     public function resettlement(){
     	if($_POST){
     		if($_POST['order_num'] != ''){
     			$wheres = 'uid='.$_SESSION['uid'].' and order_num ="'.$_POST['order_num'].'"';
     			$res = M('order')->where($wheres)->find();
     			$gid_num = explode(',',$res['gid_num']);
     			$M = M('goods');
 				foreach($gid_num as $key => $val){
 					$arr = explode('_',$val);
 					$price = $M->field('`id`,`price`')->where('id='.$arr[0])->find();
 					$goods[$key]['gid'] = $arr[0];
 					$goods[$key]['num'] = $arr[1];
 					$goods[$key]['price'] = $price['price'];
 					$goods[$key]['goods_price'] = $arr[1] * $price['price'];
 				}
 				foreach($goods as $k => $v){
 					$total_fee += $v['goods_price'];
 				}
 				if($res['price'] == $total_fee){
					$_SESSION['order_num'] = $res['order_num'];
	 				$_SESSION['goods_data'] = $goods;
					echo json_encode(array('status'=>1,'info'=>'正在跳转至确认页面','url'=>U('Shop/settlement')));exit;
 				}else{
 					echo json_encode(array('status'=>0,'info'=>'未知错误，请稍后再试!'));exit;
 				}
     		}
     	}
     }
	}
?>