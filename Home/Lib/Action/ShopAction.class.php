<?php
use UserCommonAction;
	class ShopAction extends CommonAction{
		//购物车的内容
		public function carts(){
			if($_POST){
				//结算时，传入相应的参数到后台(商品ID，商品数量，商品总价钱)
				$data = $_POST;
				//计算出单价并返回
				$arr = $this->total($data['gid_num']);
				//如果返回的不是数组，则是json错误信息，直接输出错误
				if(!is_array($arr)){
					echo $arr;exit;
				}
				//计算出商品总价
				foreach($arr as $key=>$val){
					$total_price += $val['goods_price'];
				}
				//如果计算得出的商品总价格不等于传过来的总价格，则返回错误
				if($total_price != $data['total_price']){
					echo json_encode(array('status'=>0,'info'=>'商品参数错误！'));exit;
				}else{
					$_SESSION['goods_data'] = $arr;
					echo json_encode(array('status'=>1,'info'=>'正在跳转至确认页面','url'=>U('Shop/settlement')));exit;
				}

			}else{
				if($_SESSION['is_login'] && $_SESSION['is_login'] == 1 && !empty($_SESSION['uid'])){
					R('User/checkLogin');
					//查询出当前登录的用户的购物车表里边的商品信息
					$res = M('shopcar')->where("uid ='".$_SESSION['uid']."' and status=1")->select();
					//如果查询出没有结果，则输出还没有添加商品
					if($res){
						foreach($res as $key => $val){
							$ids .=$val['gid'].',';
						}
						$ids = rtrim($ids,',');
					//如果查询出有结果，则判断是否是购买了两件以上不同的商品。分别组合不同的商品id查询商品的信息
						if(explode(',',$ids)>1){
							$sql = "select gds.id,goods_name,price,description,img,goods_num from zy_goods as gds,zy_shopcar as car where gds.id in (".$ids.") and gds.id = car.gid";
						}else{
							$sql = "select gds.id,goods_name,price,description,img,goods_num from zy_goods as gds,zy_shopcar as car where gds.id in = ".$ids."  and gds.id = car.gid";
						}
						//查询出商品表里边的详情信息
						$res = M()->query($sql);
						//遍历出新的数组输出到购物车页面
						$this->assign('data',$res);
					}else{
						//如果没查询到内容，则提示购物车没有信息
					}
				}else{
					//如果用户未登录，则将用户添加到的数据存入cookie，用户点击结算，弹出提示登录
				}
			}

			$this->display();
		}
		//计算总价钱是否正确,$arr要计算的二维数组（gid和num）,$total传递的总价钱
		private function total($data){
			$gid_num = explode(':',trim($data,''));//将商品id和商品数量拆分成数组
				//分离出商品id和商品的数量，并查询计算出总价钱是否和提交过来的总价相等
				for($i=1;$i<=count($gid_num);$i++){
					foreach($gid_num as $key => $val){ //遍历查询出商品参数
						$res = explode('_',$val);
						if(is_numeric($res[0]) && is_numeric($res[1])){
							$arr[$key]['gid'] = $res[0];
							$arr[$key]['num'] = $res[1];
						}else{
							return json_encode(array('status'=>0,'info'=>'商品参数错误！'));exit;
						}
						//查询出商品的单价
						$price = M('goods')->field('`id`,`price`')->where("id='".$arr[$key]['gid']."'")->find();
						//如果没有查询到数据，则返回错误
						if(!$price){
							return json_encode(array('status'=>0,'info'=>'无效的商品参数！'));exit;
						}
						$arr[$key]['price'] = $price['price'];
						//算出每件商品的总额
						$arr[$key]['goods_price'] = $arr[$key]['price'] * $arr[$key]['num'];
					}
				}
				return $arr;
		}

		//结算
		public function settlement(){
			//判断用户是否登录
			if(!$_SESSION['uid'] || $_SESSION['uid'] ==''){
				//将当前url存入session中，便于登录后跳转到此页面
				$_SESSION['url']['settlement'] = $_SERVER[REQUEST_URI];
				$this->error('您还未登录，请登录',U('user/index'));
			}
			if($_POST){

			}else{
				//判断session中是否存在购物车结算是存的商品数据
				if(!$_SESSION['goods_data']){
					$this->redirect('order/myorder');				
				} 		
				$data  = $_SESSION['goods_data'];
				foreach($data as $key => $val){
					$result = M("goods")->where("id='".(int)$val['gid']."'")->find();
					if(!$result){ //如果根据id没有查询出结果，则直接返回购物车
						$this->redirect('shop/carts');
					}
					$goods_list[$key]['id'] 			= $result['id'];
					$goods_list[$key]['price'] 			= $result['price'];
					$goods_list[$key]['goods_name']		= $result['goods_name'];
					$goods_list[$key]['description']	= $result['description'];
					$goods_list[$key]['img']			= $result['img'];
					$goods_list[$key]['num'] 			= $val['num'];
				}
				$res = M('member')->field('mobile')->where("uid='".$_SESSION['uid']."'")->find();
				//计算出总价格输出
				foreach($goods_list as $k => $v){
					$res['total_price'] += $v['price'] * $v['num'];
				}
				$this->assign('res',$res);
				$this->assign('list',$goods_list);
				$this->display();
			}
		}

		//删除购物车的商品
		public function delCartsGoods(){
			if($_POST){
				//根据传过来的商品id和用户uid来删除订单信息
				$ids = $_POST['gid'];
				if(count(explode(',',$ids)) > 1){
					$wheres = "uid = ".$_SESSION['uid']." and gid in(".$ids.")";
				}else{
					$wheres = 'uid = '.$_SESSION['uid'].' and gid = '.$ids;
				}
				if(M('shopcar')->where($wheres)->delete()){
					echo json_encode(array('status'=>1,'info'=>'已成功移除商品!'));exit;
				}else{
					echo json_encode(array('status'=>0,'info'=>'商品移除失败!'));exit;
				}

			}
		}
	}
 ?>