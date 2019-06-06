<?php
class RealityAction extends CommonAction {
	public function index(){
		//美人制造
		$M = M("reality");
		if(!empty($_POST['keywords'])){
			 $wheres['username'] = array('like',"%".$_POST['keywords']."%");
			}
		//print_r($_GET['status']);
        $count = $M->where($wheres)->count();
        import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 20);
        $showPage = $page->show();
        $list = $M->where($wheres)->field("`id`,`title`,`username`,`status`,`paixu`,`pic`,`worke`,`xiangmu`,`zhuanjia`,`published`")->order("paixu asc,id desc")->limit($page->firstRow, $page->listRows)->select();
		$statusArr = array("隐藏", "显示");
        foreach ($list as $k => $v) {
			if(empty($v['xihuan'])){
				$list[$k]['xihuan'] = 0;
				}
			else{
				$list[$k]['xihuan'] = 1;
				}
			$list[$k]['status'] = $statusArr[$v['status']];
			$xiangmustr=explode(',',$v['xiangmu']);
			$zhuanjiastr=explode(',',$v['zhuanjia']);	
			foreach ($xiangmustr as $ks=>$vs){
				$slist = M("project")->where('id='.$vs)->field("`id`,`title`")->find();
				  if($xiang==""){
				    $xiang=$slist['title'];
					}
				  else{
					  $xiang=$xiang.'&nbsp;&nbsp;&nbsp;'.$slist['title'];
					}
				  }
			$list[$k]['xiangmu'] = $xiang;
			unset($xiang);
			foreach ($zhuanjiastr as $ks=>$vs){
				$info_expert = M('expert')->where('id='.$vs)->field("`id`,`title`")->find();
				
				  if($xiang==""){
				    $xiang=$info_expert['title'];
					}
				  else{
					  $xiang=$xiang.'&nbsp;&nbsp;&nbsp;'.$info_expert['title'];
					}
				  }
			$list[$k]['zhuanjia'] = $xiang;
			unset($xiang);	  
        }
        $this->assign("page", $showPage);
        $this->assign("list", $list);
        $this->display();
		}
	/*public function category() {
        if (IS_POST) {
            echo json_encode(D("Reality")->category());
        } else {
        	$this->assign("list", D("Reality")->category());
            $this->display();
        }
    }*/
	public function add(){
		//添加项目
            if (IS_POST) {
            $M = M("Reality");
			$data = $_POST['info'];
			$data['content'] = stripslashes(htmlspecialchars_decode($data['content']));
			$data['duibi'] = stripslashes(htmlspecialchars_decode($data['duibi']));
			$data['published'] = time();
			$zhuanjia = $data['zhuanjia'];
			  foreach ($zhuanjia as $k=>$v){
				  if($zhuan==""){
				    $zhuan=$v;
					}
				  else{
					  $zhuan=$zhuan.','.$v;
					}
				  }
			  $data['zhuanjia'] = $zhuan;
			  unset($zhuan);
			
			$xiangmu = $data['xiangmu'];
			foreach ($xiangmu as $k=>$v){
				  if($xiang==""){
				    $xiang=$v;
					}
				  else{
					  $xiang=$xiang.','.$v;
					}
				  }
			  $data['xiangmu'] = $xiang;
			  unset($xiang);
			  
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();// 实例化上传类
			$upload->maxSize  = 3145728 ;// 设置附件上传大小
			$upload->saveRule  = date('Ymj').'_p'.time();// 设置文件名
			//$upload->saveRule  = uniqid;// 设置文件名
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath =  C("TMPL_PARSE_STRING.__realityimg__");// 设置附件上传目录
			if(!$upload->upload()) {
				//$this->error($upload->getErrorMsg());// 上传错误提示错误信息
			}else{
				$info =  $upload->getUploadFileInfo();// 上传成功 获取上传文件信息
			}
			
			if($info){
				//$data['img'] = $info[0]['savename'];
				$data['pic'] = $info[0]['savename'];
			}
            if ($M->add($data)) {
				echo json_encode(array('status' => 1, 'info' => "已经发布", 'url' => U('Reality/index')));
			} else {
				echo json_encode(array('status' => 0, 'info' => "发布失败，请刷新页面尝试操作"));
			}
        } else {
            //$this->assign("list", D("Reality")->category());
			$info['status'] = 1;
			$info['reshow'] = 0;
			$info['fenxiang'] = 0;
			$paixu = M("Reality")->limit(0,1)->order('paixu desc')->find();
			$info['paixu'] = $paixu['paixu']+1;
			$info_expert = M('expert')->field("`id`,`title`")->select();
			foreach ($info_expert as $k=>$v){
				$info_expert[$k]['xuanzhpng'] = 0;
				}
			$info['expert'] = $info_expert ;
			$M = M("projectcategory");
			$clist = $M->where('pid=0')->select();
			foreach ($clist as $k=>$v){
				$slist = M("project")->where('cid='.$v['cid'])->field("`id`,`title`")->select();
				foreach ($slist as $ks=>$vs){
					 $slist[$ks]['xuanzhong'] = 0;
					}
				$clist[$k]['smlist'] = $slist;	
				}
			$info['clist'] = $clist;
			$titlename = "添加美人制造信息";
			$this->assign('titlename',$titlename);	
			$this->assign('info',$info);
            $this->display();
        }
		}
	public function edit(){
		//修改项目
          $M = M("Reality");
		  if (IS_POST) {
			  $this->checkToken();
			  $data = $_POST['info'];
			  $refeurl = $_POST['refeurl'];
			  $data['content'] = stripslashes(htmlspecialchars_decode($data['content']));
			  $data['duibi'] = stripslashes(htmlspecialchars_decode($data['duibi']));
			  $data['published'] = time();
			  $zhuanjia = $data['zhuanjia'];
				foreach ($zhuanjia as $k=>$v){
					if($zhuan==""){
					  $zhuan=$v;
					  }
					else{
						$zhuan=$zhuan.','.$v;
					  }
					}
				$data['zhuanjia'] = $zhuan;
				unset($zhuan);
			  
			  $xiangmu = $data['xiangmu'];
			  foreach ($xiangmu as $k=>$v){
					if($xiang==""){
					  $xiang=$v;
					  }
					else{
						$xiang=$xiang.','.$v;
					  }
					}
				$data['xiangmu'] = $xiang;
				unset($xiang);
				import('ORG.Net.UploadFile');
				$upload = new UploadFile();// 实例化上传类
				$upload->maxSize  = 3145728 ;// 设置附件上传大小
				$upload->saveRule  = date('Ymj').'_p'.time();// 设置文件名
				//$upload->saveRule  = uniqid;// 设置文件名
				$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
				$upload->savePath =  C("TMPL_PARSE_STRING.__realityimg__");// 设置附件上传目录
				if(!$upload->upload()) {
					//$this->error($upload->getErrorMsg());// 上传错误提示错误信息
				}else{
					$info =  $upload->getUploadFileInfo();// 上传成功 获取上传文件信息
				}
				
				if($info){
					@unlink(C("TMPL_PARSE_STRING.__realityimg__").$data['pic']);
					//@unlink(C("TMPL_PARSE_STRING.__realityimg__").$data['img']);
				    //$data['img'] = $info[0]['savename'];
					$data['pic'] = $info[0]['savename'];
				}
				
				if ($M->save($data)) {
					echo json_encode(array('status' => 1, 'info' => "已经更新", 'url' => ''.$refeurl.''));
				} else {
					echo json_encode(array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作"));
				}
		  } else {
			  $refeurl = $_SERVER['HTTP_REFERER'];
			  $info = $M->where("id=" . (int) $_GET['id'])->find();
			  if ($info['id'] == '') {
				  $this->error("不存在该记录");
			  }
			  
			  $info_expert = M('expert')->field("`id`,`title`")->select();
			  $cookistr=explode(',',$info['zhuanjia']);
			  foreach ($info_expert as $k=>$v){
				  $info_expert[$k]['xuanzhpng'] = 0;
				  foreach ($cookistr as $sd=>$sid){
					if($sid==$v['id']){
						$info_expert[$k]['xuanzhpng'] = 1;
						}	
					}
				  }
			  $info['expert'] = $info_expert ;
			  
			  $M = M("projectcategory");
			  $clist = $M->where('pid=0')->select();
			  $xiangmustr=explode(',',$info['xiangmu']);
			  foreach ($clist as $k=>$v){
				  $slist = M("project")->where('cid='.$v['cid'])->field("`id`,`title`")->select();
				  foreach ($slist as $ks=>$vs){
					   $slist[$ks]['xuanzhong'] = 0;
					  foreach ($xiangmustr as $sd=>$sid){
						if($sid==$vs['id']){
							$slist[$ks]['xuanzhong'] = 1;
							}	
						}
					  }
				  $clist[$k]['smlist'] = $slist;	
				  }
			  $info['clist'] = $clist;	
			  $this->assign("refeurl",$refeurl);
			  $this->assign("info", $info);
			  $titlename = "修改美人制造信息";
			  $this->assign('titlename',$titlename);
			  //$this->assign("list", D("Reality")->category());
			  $this->display("add");
		  }
		}
	public function zongpai(){
		//$refeurl = $_SERVER['HTTP_REFERER'];
		//$paiid = $_POST['t_id'];
		$id=$_GET['id'];
		$paixu = $_GET['paixu'];
		if(empty($id)){
			echo json_encode(array("status"=>0,'info'=>"无修改，不用提交"));
			}
		else{	
		$arrid = explode(',',$id);
		$arrpaixu = explode(',',$paixu);
		foreach ($arrid as $k=>$v){
			M("Reality")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			}
		echo json_encode(array("status"=>1,'info'=>"排序成功"));	
		  }
		}	
	public function submit() {
		$id = $_GET['id'];
		if(count(explode(',',$id))>1){
			 $where = 'id in('.$id.')';  
			 }
		  else{     
		     $where = 'id='.$id; 
			 }   
		  $list=M("Reality")->where($where)->delete();   
		  if($list!==false) {     
		    echo json_encode(array('status' => 1, 'info' => "成功删除{$list}条")); 
			}else{    
			echo json_encode(array('status' => 0, 'info' => "删除失败" ));  
		    } 
    }					
	public function paixu(){
		  $id=$_GET['id'];
		  $paixu = $_GET['paixu'];
		  if(empty($id)){
			  echo json_encode(array("status"=>0,'info'=>"无修改，终止提交"));
			  }
		  else{	
		  $arrid = explode(',',$id);
		  $arrpaixu = explode(',',$paixu);
		  foreach ($arrid as $k=>$v){
			  M("Reality")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			  }
		  echo json_encode(array("status"=>1,'info'=>"排序成功"));	
			}
		}
	 public function guanlian(){
		$M = M("realitylove");
		$cid=$_GET['cid'];	  
		if(IS_POST){
			$act = $_POST['act'];
            $data = $_POST['info'];
			$cid = $_POST['cid'];
			$wheres['title'] = $data['title'];
			$wheres['urlclass'] = $data['urlclass'];
			$wheres['prid'] = $cid;
			$wheres['id'] = $data['id'];
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();// 实例化上传类
			$upload->maxSize  = 3145728 ;// 设置附件上传大小
			$upload->saveRule  = date('Ymj').'_p'.time();// 设置文件名
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath =  C("TMPL_PARSE_STRING.__pictureimg__");// 设置附件上传目录
			if(!$upload->upload()) {
				//$this->error($upload->getErrorMsg());// 上传错误提示错误信息
			}else{
				$info =  $upload->getUploadFileInfo();// 上传成功 获取上传文件信息
			}
			if($info){
				$wheres['picurl'] = $info[0]['savename'];
			}
			if($act=='add'){
				 if ($M->add($wheres)) {
					  echo json_encode(array("status" => 1, "info" => "添加成功" ,'url' => U('Reality/guanlian?cid='.$cid.'')));
				  } else {
					  echo json_encode(array("status" => 0, "info" => "添加失败"));
				  }
			   }
			 else{
				 if ($M->save($wheres)) {
					  echo json_encode(array("status" => 1, "info" => "修改成功" ,'url' => U('Reality/guanlian?cid='.$cid.'')));
				  } else {
					  echo json_encode(array("status" => 0, "info" => "修改失败"));
				  }
			  }  
			
			}
		else{
			$id=$_GET['id'];
			if(empty($id)){
				$this->assign('act','add');
				}
			else{
				$cinfo= $M->where('id='.$id)->find();
				$this->assign('act','edit');
				$this->assign('cinfo',$cinfo);
				}	
			//$list = D("Project")->category();
			$list = $M->where('prid='.$cid)->select();
			$this->assign('cid',$cid);
        	$this->assign("list", $list);
			$this->display();
			}	
		}
	public function guanzongpai(){
		//$refeurl = $_SERVER['HTTP_REFERER'];
		//$paiid = $_POST['t_id'];
		$id=$_GET['id'];
		$paixu = $_GET['paixu'];
		if(empty($id)){
			echo json_encode(array("status"=>0,'info'=>"无修改，不用提交"));
			}
		else{	
		$arrid = explode(',',$id);
		$arrpaixu = explode(',',$paixu);
		foreach ($arrid as $k=>$v){
			M("realitylove")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			}
		echo json_encode(array("status"=>1,'info'=>"排序成功"));	
		  }
		}	
	public function guansubmit() {
		$id = $_GET['id'];
		if(count(explode(',',$id))>1){
			 $where = 'id in('.$id.')';  
			 }
		  else{     
		     $where = 'id='.$id; 
			 }   
		  $list=M("realitylove")->where($where)->delete();   
		  if($list!==false) {     
		    echo json_encode(array('status' => 1, 'info' => "成功删除{$list}条")); 
			}else{    
			echo json_encode(array('status' => 0, 'info' => "删除失败" ));  
		    } 
    }					
	public function guanpaixu(){
		  $id=$_GET['id'];
		  $paixu = $_GET['paixu'];
		  if(empty($id)){
			  echo json_encode(array("status"=>0,'info'=>"无修改，终止提交"));
			  }
		  else{	
		  $arrid = explode(',',$id);
		  $arrpaixu = explode(',',$paixu);
		  foreach ($arrid as $k=>$v){
			  M("realitylove")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			  }
		  echo json_encode(array("status"=>1,'info'=>"排序成功"));	
			}
		}			

	}
?>