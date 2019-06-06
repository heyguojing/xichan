<?php
	class PushAction extends CommonAction{
		public function index(){
			header("Content-Type:text/html; charset=utf-8");
       		 	header('Content-Type:application/json; charset=utf-8');
        			$client = M('client');
        			$result = $client->order('id desc')->select();
        			$this->assign('data',$result);
			$this->display();
		}	
		
		public function del(){
			$client = M('client');
			$sql = "delete from zy_client where id={$_GET['id']}";
			$client->execute($sql);
			$this->redirect('Push/index');
		}
		public function del_list(){
			$client = M('client_new');
			$sql = "delete from zy_client_new where id={$_GET['id']}";
			$client->execute($sql);
			$this->redirect('Push/client_list');
		}


		Public function client_list(){
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
			$this->assign('data',M('client_new')->order('id desc')->select());
			$this->assign('pro',$arr);
			$this->display();
		}
	}

?>