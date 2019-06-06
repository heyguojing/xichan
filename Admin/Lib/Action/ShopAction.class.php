<?php 
	class ShopAction extends CommonAction{
		//商品列表
		public function index(){
			//get传过来的分类id，组建查询条件
			if($_GET){
				if(is_numeric($_GET['cid'])){
					$wheres = "cid = ".$_GET['cid'];
				}else{
					$wheres = '';
				}
			}
			//post穿过来的查询条件，根据商品名称查询
			if($_POST){
				$wheres = "goods_name like '%".$_POST['keywords']."%'";
			}
			$res = M("goods")->where($wheres)->select();
			$this->assign('category',M('goods_categroy')->select());
			$this->assign('list',$res);
			$this->display();
		}
		//添加商品信息
		public function goodsAdd(){
			$M = M('goods');
			if($_POST){
			  $data = $_POST['info'];
			  unset($data['id']);
			  if($_FILES){
				  import('ORG.Net.UploadFile');
				  $upload = new UploadFile();// 实例化上传类
				  $upload->maxSize  = 3145728 ;// 设置附件上传大小
				  $upload->saveRule  = date('Ymj').'_p'.time();// 设置文件名
				  $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
				  $upload->savePath =  C("TMPL_PARSE_STRING.__goodsimg__");// 设置附件上传目录
				  if(!$upload->upload()) {
					  //$this->error($upload->getErrorMsg());// 上传错误提示错误信息
				  }else{
					  $info =  $upload->getUploadFileInfo();// 上传成功 获取上传文件信息
				  }
			 }else{
			  	echo json_encode(array('status'=>0,'info'=>'请选择一个商品图片后提交'));exit;
			  }
			  $data['img'] = $info[0]['savename'];//保存上传文件的文件名
			  $SN = $M->field('SN')->order('SN desc')->find(); //查询出最大的sn序列号，组成新的序列号
			  $SN = explode('SN',$SN['SN']);
			  $data["SN"] = "XCSN".($SN[1]+1);
			  $data['aid'] = $_SESSION['my_info']['aid'];
			  $data['published'] = time();//商品创建时间
			  if($M->add($data)){
			  	echo json_encode(array('status'=>1,'info'=>'商品添加成功!','url'=>U("Shop/index")));exit;
			  }
			}else{
				$this->assign('list',M('goods_categroy')->select());
				$this->assign('titlename','添加商品信息');
				$this->display('add');
			}
		}
		//删除商品信息
		public function submit(){
			if($_GET){
				$ids = explode(',',$_GET['id']);
				if(count($ids)>1){
					$wheres = 'id in ('.$_GET['id'].')';
				}else{
					$wheres = 'id = '.$_GET['id'];
				}
				if(M('goods')->where($wheres)->delete()){
					echo json_encode(array('status'=>1,'info'=>'成功删除'.count($ids).'条信息!！','url'=>U('Shop/index')));exit;
				}else{
					echo json_encode(array('status'=>0,'info'=>'删除失败，请稍候再试!'));exit;
				}
			}
		}


		//修改商品信息
		public function edit(){
			if($_POST){
				$data = $_POST['info'];
				$data['update'] = time();
				$data['aid'] = $_SESSION['my_info']['aid'];
				if($_FILES){
					  import('ORG.Net.UploadFile');
					  $upload = new UploadFile();// 实例化上传类
					  $upload->maxSize  = 3145728 ;// 设置附件上传大小
					  $upload->saveRule  = date('Ymj').'_p'.time();// 设置文件名
					  $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
					  $upload->savePath =  C("TMPL_PARSE_STRING.__goodsimg__");// 设置附件上传目录
					  if(!$upload->upload()) {
						  //$this->error($upload->getErrorMsg());// 上传错误提示错误信息
					  }else{
						  $info =  $upload->getUploadFileInfo();// 上传成功 获取上传文件信息
					  }
					$data['img'] = $info[0]['savename'];
				 }
				if(M('goods')->save($data)){
					echo json_encode(array('status'=>1,'info'=>'商品信息修改成成功!！','url'=>U('Shop/index')));exit;
				}else{
					echo json_encode(array('status'=>0,'info'=>'修改失败，请稍候再试!'));exit;
				}
			}else if($_GET['id'] && !empty($_GET['id']) && is_numeric($_GET['id'])){
				$res = M("goods")->where('id='.$_GET['id'])->find();
				if($res){
					$this->assign('info',$res);
				}else{
					$this->redirect('Shop/index');
				}
			}else{
				$this->redirect('Shop/index');
			}				
			$this->assign('list',M('goods_categroy')->select());
			$this->assign('titlename','修改商品信息');
			$this->display('add');
		}

		//商品分类列表
		public function Goods_cate(){
			$M = M('goods_categroy');
			if($_POST){
				$data = $_POST['info'];
				if($data['cid']==''){
					unset($data['cid']);
					if($M->where("cname ='".$data['cname']."'")->find()){
						echo json_encode(array('status'=>0,'info'=>'已存在的分类!'));exit;
					}
					if($M->add($data)){
						echo json_encode(array('status'=>1,'info'=>'分类创建成功!','url'=>U('Shop/Goods_cate')));exit;
					}else{
						echo json_encode(array('status'=>0,'info'=>'分类创建失败，请稍候再试!'));exit;
					}
				}else{
					if($M->save($data)){
						echo json_encode(array('status'=>1,'info'=>'分类修改成功!','url'=>U('Shop/Goods_cate')));exit;
					}else{
						echo json_encode(array('status'=>0,'info'=>'分类修改失败，请稍候再试!'));exit;
					}
				}
			}


			if($_GET['id']){
				$this->assign('act','edit');
				$where = "cid =".$_GET['id'];
				$this->assign('cinfo',$M->where($where)->find());
			}else{
				$this->assign('act','add');
			}
			$this->assign('list',M('goods_categroy')->select());
			$this->display('category');
		}
		//删除分类
		public function cdel(){
			if($_GET){
				if(is_numeric($_GET['id'])){
					if(M('goods_categroy')->where('cid='.$_GET['id'])->delete()){
						echo json_encode(array('status'=>1,'info'=>'成功删除一条记录','url'=>U('Shop/Goods_cate')));exit;
					}else{
						echo json_encode(array('status'=>0,'info'=>'删除失败，请稍后再试!'));exit;
					}
				}
			}
		}

		//订单列表
		public function order(){
			if($_POST['keywords']){
				$wheres = "order_num like '%".$_POST['keywords']."%'";
			}
			$res = M('order')->where($wheres)->order('order_num desc')->select();
			$this->assign("list",$res);
			$this->display();
		}
		//查看订单详情
		public function readOrder(){
			if($_GET['id']){
				$wheres = 'id ='.$_GET['id'];
				$res = M('order')->where($wheres)->find();
				$arr = explode(',',$res['gid_num']);
				foreach($arr as $key => $val){
					$arr1 = explode('_',$val);
					$arr2 = M('goods')->field('`goods_name`,`id`')->where("id=".$arr1[0])->find();
					$result[$key]['goods_name'] = $arr2['goods_name'];
					$result[$key]['num']	= $arr1[1];
					$result[$key]['id']		= $arr2['id'];
				}
				$result1 = M('member')->field('`uid`,`nickname`')->where("uid=".$res['uid'])->find();
				$res['nickname'] = $result1['nickname'];
				$this->assign('result',$result);
				$this->assign('info',$res);
			}else{
				$this->redirect('Shop/order');
			}

			$this->display();
		}
		//会员中心管理
		public function member(){
			$this->assign('list',M('member')->order('uid desc')->select());
			$this->display();
		}
		//会员信息
		public function memberInfo(){
			$this->assign('info',M('member')->where("uid = ".$_GET['id']."")->find());
			$this->display();
		}
		//会员禁用
		public function forbid(){
			if($_GET){
				$arr['uid'] = $_GET['id'];
				$arr['status'] = 0;//禁用会员
				if(M('member')->save($arr)){
					echo json_encode(array('status'=>1,'info'=>'此会员已禁止使用!','url'=>U('Shop/member')));exit;
				}else{
					echo json_encode(array('status'=>0,'info'=>'禁止失败，请稍后再试!'));exit;
				}
			}
		}

		//验证会员信息
		public function validate(){

			$this->display();
		}
		//查询获取到的信息
		public function validata(){
			if($_POST){
				$order = M('order');
				$order_data = $order->where('order_num = "'.$_POST['order_num'].'"')->find();
				if($order_data){
					$member = M('member')->where('uid ='.$order_data['uid'])->find();
					if($member['mobile'] == $_POST['mobile']){
						$order_data['nickname'] = $member['nickname'];
						$order_data['uid']		= $member['uid'];
						if($order_data['status'] == 3){
							$order_data['save'] = '<a href="editOrder?id='.$order_data["id"].'">修改 </a>';
						}else{
							$order_data['save'] = '<a href="readOrderInfo?id='.$order_data["id"].'">查看 </a>';
						}
						switch($order_data['status']){
							case 0:
								$order_data['status'] = '已取消';
								break;
							case 1:
								$order_data['status'] = '已验证完成';
								break;
							case 2:
								$order_data['status'] = '未支付';
								break;
							case 3:
								$order_data['status'] = '已支付未使用';
								break;
							default:
								$order_data['status'] = '未知错误';
								break;
						}
						$order_data['ctime'] = date('Y-m-d H:i:s',$order_data['ctime']);
						echo json_encode($order_data);exit;
					}else{
						echo json_encode(array('status'=>0,'info'=>'电话或订单错误，请确认后再试!'));exit;
					}
				}else{
					echo json_encode(array('status'=>0,'info'=>'没有此订单信息,请重新确认!'));exit;
				}
			}
		}

		//修改订单信息，并短信通知
		public function editOrder(){
			if(is_numeric($_GET['id'])){
				$order = M('order')->where('id ='.$_GET['id'])->find();
				if($order['status'] !=3){
					$this->error('参数错误','validate');
				}
				$goods = M('goods');
				//查询出订单信息显示到页面
				$member = M('member')->where('uid ='.$order['uid'])->find();
				$order['nickname'] = $member['nickname'];
				//查询出子订单信息
				$oc_data = M('order_children')->where('order_id ='.$order['id'])->select();
				foreach($oc_data as $k => $v){
					$orderC = $goods->where('id = '.$v['gid'])->find();//查询出商品的信息
					$orderChildrenData[$k]['gid'] 		= $orderC['id'];
					$orderChildrenData[$k]['surplus']   = $v['surplus'];
					$orderChildrenData[$k]['id']		= $v['id'];
					$orderChildrenData[$k]['num']		= $v['num'];
					$orderChildrenData[$k]['goods_name']= $orderC['goods_name'];
				}
				$this->assign('orderC',$orderChildrenData);
				$this->assign('info',$order);
			}else{
				$this->redirect($_SERVER['HTTP_REFERER']);
			}
			$status = array(3=>'已支付未使用',1=>'已验证完成');
			$this->assign('status',$status);
			$this->assign('titlename','修改订单状态');
			$this->display();
		}	
		//读取订单信息
		public function readOrderInfo(){
			if(is_numeric($_GET['id'])){
				$order = M('order')->where('id ='.$_GET['id'])->find();
				$member = M('member')->where('uid ='.$order['uid'])->find();
				$admin = M('admin')->field('`aid`,`email`')->where('aid='.$order['aid'])->find();
				$order['nickname'] = $member['nickname'];
				$order['email'] = $admin['email'];
				if($order['status'] == 1){
					if($order['type'] ==1 ){
						$order['type1'] = '支付宝';
					}elseif($order['type'] ==2){
						$order['type1'] = '微信';
					}
				}else{
						$order['type1'] = '无';
					}
				$this->assign('info',$order);
			}else{
				$this->redirect($_SERVER['HTTP_REFERER']);
			}
			$this->assign('titlename','验证订单信息');
			$this->display();
		}
	}