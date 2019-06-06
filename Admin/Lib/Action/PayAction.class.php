<?php
class PayAction extends CommonAction {
    
	public function index(){
		$alipay = M('alipay')->where('id=1')->find();
		$wxpay = M('wxpay')->where('id=1')->find();
		$this->assign('alipay',$alipay);
		$this->assign('wxpay',$wxpay);
		$this->display();
		}
	public function alipay(){
		$getbox = $_GET;
		$alipayname=trim($getbox['alipayname']);
		$partner=trim($getbox['alipaypartner']);
		$key=trim($getbox['alipaykey']);
		$status=1;
		$data['alipayname']=$alipayname;
		$data['partner']=$partner;
		$data['key']=$key;
		$data['status']=$status;
		$data['pay_type']='alipay';
        $data['name']='手机支付宝';
		$data['id']=1;
		if (M('alipay')->save($data)) {
			  echo json_encode(array('status' => 1, 'info' => "已经更新"));
		  } else {
			  echo json_encode(array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作"));
		  }
		}
	public function wxpay(){
		$getbox = $_GET;
		$appid=trim($getbox['w_appid']);
		$appsecret=trim($getbox['w_secret']);
		$signkey=trim($getbox['w_signkey']);
		$partnerid=trim($getbox['w_partnerid']);
		$partnerkey=trim($getbox['w_partnerkey']);
		$data['appid']=$appid;
		$data['appsecret']=$appsecret;
		$data['signkey']=$signkey;
		$data['partnerid']=$partnerid;
		$data['partnerkey']=$partnerkey;
		$data['status']=1;
		$data['pay_type']='wxpay';
		$data['name']='微信支付';
		$data['id']=1;
        if (M('wxpay')->save($data)) {
			  echo json_encode(array('status' => 1, 'info' => "已经更新"));
		  } else {
			  echo json_encode(array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作"));
		  }
	   }			
}
?>