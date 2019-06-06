<?php
class ProjectAction extends CommonAction {
	public function index(){
		//项目列表
		$M = M("project");
		if(!empty($_GET['cid'])){
		   $wheres['cid'] = $_GET['cid'];
		   
		  }
		  
		if(!empty($_POST['keywords'])){
			 $wheres['title'] = array('like',"%".$_POST['keywords']."%");
			}
		
        $count = $M->where($wheres)->count();
        import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 20);
        $showPage = $page->show();
        $list = $M->where($wheres)->field("`id`,`title`,`hot`,`paixu`,`caselist`,`status`,`xihuan`,`pic`,`tuangou`,`published`,`cid`")->order("paixu asc,id desc")->limit($page->firstRow, $page->listRows)->select();
		$edltArr = array("","推荐");
		$hotArr = array("","热门");
		$tuangouArr = array("","团购","折扣");
		$statusArr = array("隐藏状态", "显示状态");
        $cidArr = M("Projectcategory")->field("`cid`,`name`")->select();
        foreach ($cidArr as $k => $v) {
            $cids[$v['cid']] = $v;
        }
        unset($cidArr);
        foreach ($list as $k => $v) {
            $list[$k]['cidName'] = $cids[$v['cid']]['name'];
			$list[$k]['pidName'] = $cids[$v['pid']]['name'];
			$list[$k]['edltname'] = $edltArr[$v['edlt']];
			$list[$k]['status'] = $statusArr[$v['status']];
			$list[$k]['tuangouname'] = $tuangouArr[$v['tuangou']];
			$list[$k]['hot'] = $hotArr[$v['hot']];
			if(empty($v['xihuan'])){
				$list[$k]['xihuan'] = 0;
				}
			else{
				$list[$k]['xihuan'] = 1;
				}
			if(empty($v['caselist'])){
				$list[$k]['caselist'] = 0;
				}
			else{
				$list[$k]['caselist'] = 1;
				}		
			//$list[$k][''] = [$v[]];
        }
        $this->assign("page", $showPage);
        $this->assign("list", $list);
		
		$category = M('Projectcategory')->where('pid=0')->field("`cid`,`name`")->select();
		$this->assign('category',$category);
        $this->display();
		}
	public function category() {
        if (IS_POST) {
            echo json_encode(D("Project")->category());
        } else {
			$cid=$_GET['id'];
			if(empty($cid)){
				$this->assign('act','add');
				}
			else{
				$cinfo= M('Projectcategory')->where('cid='.$cid)->find();
				$this->assign('act','edit');
				$this->assign('cinfo',$cinfo);
				}	
			//$list = D("Project")->category();
			$list = M("projectcategory")->where('pid=0')->order('paixu asc')->select();
        	$this->assign("list", $list);
            $this->display();
        }
    }
	public function add(){
		//添加项目
            if (IS_POST) {
            $this->checkToken();
            $M = M("Project");
			$data = $_POST['info'];
			if($data['tuangou']==0){
				$data['tuangoustart']="";
				$data['tuangouend']="";
				$data['shiyongstart']="";
				$data['shiyongend']="";
				}
			else{
				$data['tuangoustart']=strtotime($data['tuangoustart'].' 00:00:00');
				$data['tuangouend']=strtotime($data['tuangouend'].' 00:00:00');;
				$data['shiyongstart']=strtotime($data['shiyongstart'].' 00:00:00');
				$data['shiyongend']=strtotime($data['shiyongend'].' 00:00:00');
				}	
			$data['published'] = time();
			$data['aid'] = $_SESSION['my_info']['aid'];
			$data['content'] = stripslashes(htmlspecialchars_decode($data['content']));
			$data['caselist'] = stripslashes(htmlspecialchars_decode($data['caselist']));
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
				$data['pic'] = $info[0]['savename'];
			}
			$tid=$M->add($data);
            if ($tid) {
			$search['title'] = $data['title'];
			$search['keyid'] = $tid;
			$search['url']= 'project';
			$search['search'] = $data['title'].','.$data['keywords'].','.$data['description'].','.$data['summary'].','.$data['xiaoguo'];
			M('search')->add($search);
				echo json_encode(array('status' => 1, 'info' => "已经发布", 'url' => U('Project/index?cid='.$data['cid'].'')));
			} else {
				echo json_encode(array('status' => 0, 'info' => "发布失败，请刷新页面尝试操作"));
			}
        } else {
			$info['status'] = 1;
			$info['reshow'] = 0;
			$info['fenxiang'] = 0;
			$info['tuangou'] = 0;
			$info['tuangoustart'] = time();
			$info['tuangouend'] = time();
			$info['shiyongstart'] = time();
			$info['shiyongend'] = time();
			$paixu = M("Project")->limit(0,1)->order('paixu desc')->find();
			$info['paixu'] = $paixu['paixu']+1;
			$info_expert = M('expert')->field("`id`,`title`")->select();
			foreach ($info_expert as $k=>$v){
				$info_expert[$k]['xuanzhpng'] = 0;
				}
			$info['expert'] = $info_expert ;
			$titlename = "添加项目信息";
			$this->assign('titlename',$titlename);
            $this->assign("list", D("Project")->category());
			$this->assign('info',$info);
            $this->display();
        }
		}
	public function edit(){
		//修改项目
          $M = M("Project");
		  if (IS_POST) {
			  $this->checkToken();
			  $data = $_POST['info'];
			  $refeurl = $_POST['refeurl'];
			  $data['published'] = time();
			  $data['aid'] = $_SESSION['my_info']['aid'];	
			  $data['content'] = stripslashes(htmlspecialchars_decode($data['content']));
			  $data['caselist'] = stripslashes(htmlspecialchars_decode($data['caselist']));
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
				  @unlink(C("TMPL_PARSE_STRING.__pictureimg__").$data['pic']);
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
			  $titlename = "修改项目信息";
			  $this->assign('titlename',$titlename);
			  $info['expert'] = $info_expert ;
			  $this->assign("info", $info);
			  $this->assign("refeurl",$refeurl);
			  $this->assign("list", D("Project")->category());
			  $this->display("add");
		  }
		}
	
	 public function guanlian(){
		$M = M("projectlove");
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
					  echo json_encode(array("status" => 1, "info" => "添加成功" ,'url' => U('Project/guanlian?cid='.$cid.'')));
				  } else {
					  echo json_encode(array("status" => 0, "info" => "添加失败"));
				  }
			   }
			 else{
				 if ($M->save($wheres)) {
					  echo json_encode(array("status" => 1, "info" => "修改成功" ,'url' => U('Project/guanlian?cid='.$cid.'')));
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
			M("projectlove")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
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
		  $list=M("projectlove")->where($where)->delete();   
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
			  M("projectlove")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			  }
		  echo json_encode(array("status"=>1,'info'=>"排序成功"));	
			}
		}		
		
		public function caselist(){
		$M = M("Project");	  
		if(IS_POST){
			$data = $_POST['info'];
			$dataxihuan =  $data['xihuanid'];
			$add="";
			foreach ($dataxihuan as $k=>$v){
				if(empty($add)){
					$add = $v;
					}
				else{
					$add .=",".$v;
					}	
				}
			$where['caselist'] = $add;
			unset($add);
			$where['id'] = $data['id'];	
			if ($M->save($where)) {
				  echo json_encode(array('status' => 1, 'info' => "已经更新", 'url' => U('Project/index')));
			  } else {
				  echo json_encode(array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作"));
			  }
			}
		else{
			//xihuan
			$info['id'] = $_GET['id'];
			$list = M('case')->where("status=1")->select();
			$this->assign("info", $info);  
			$this->assign("list", $list);  
			$this->display();
			}	
		}	
	public function caseedit(){
		$M = M("Project");	  
		if(IS_POST){
			$data = $_POST['info'];
			$dataxihuan =  $data['xihuanid'];
			$add="";
			foreach ($dataxihuan as $k=>$v){
				if(empty($add)){
					$add = $v;
					}
				else{
					$add .=",".$v;
					}	
				}
			$where['caselist'] = $add;
			unset($add);
			$where['id'] = $data['id'];	
			if ($M->save($where)) {
				  echo json_encode(array('status' => 1, 'info' => "已经更新", 'url' => U('Project/index')));
			  } else {
				  echo json_encode(array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作"));
			  }
			}
		else{
			//xihuan
			 $info = $M->where("id=" . (int) $_GET['id'])->find();
			  if ($info['id'] == '') {
				  $this->error("不存在该记录");
			  }
			$xihuan = explode(',',$info['caselist']);
			$xuanzhong = array();
			  foreach ($xihuan as $k=>$v){
				  $cin =  M('case')->where("id=" .$v)->find();
				 $xuanzhong[$k]['id'] = $cin['id'];
				 $xuanzhong[$k]['title'] = $cin['title']; 
				}
			$list = M('case')->where("status=1")->select();
			foreach ($list as $k=>$v){
				foreach ($xihuan as $s=>$d){ 
				   if($d==$v['id']){
					   unset($list[$k]);
				    }
				  }
				}
			$this->assign('xuanzhong',$xuanzhong);		
			$this->assign('list',$list);	
			$this->assign("info", $info);  
			$this->display();
			}	
		}
		
	
	public function cdel(){
		//删除项目
           $temp = M("projectcategory")->where("cid=" . (int) $_GET['id'])->find();
    	
        if (M("projectcategory")->where("cid=" . (int) $_GET['id'])->delete()) {
            echo json_encode(array("status" => 1, "info" => "删除成功",'url' => U('Project/category')));
        } else {
            echo json_encode(array("status" => 0, "info" => "删除失败，可能是不存在该ID的记录"));
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
			M("Project")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
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
		  $list=M("Project")->where($where)->delete();   
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
			  M("Project")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			  }
		  echo json_encode(array("status"=>1,'info'=>"排序成功"));	
			}
		}
		
	public function youhui(){
		//项目列表
		$M = M("youhui");
		$wheres['tuangou'] = 1;
		$count = $M->where($wheres)->count();
		import("ORG.Util.Page");       //载入分页类
		$page = new Page($count, 20);
		$showPage = $page->show();
		$list = $M->where($wheres)->order("paixu asc,id desc")->limit($page->firstRow, $page->listRows)->select();
		$cidArr = M("Projectcategory")->field("`cid`,`name`")->select();
		$statusArr = array("隐藏状态", "显示状态");
		foreach ($cidArr as $k => $v) {
            $cids[$v['cid']] = $v;
        }
        unset($cidArr);
		foreach ($list as $k => $v) {
		   $project = M("Project")->where("id=" .$v['projectid'])->find();
		   $list[$k]['title'] = $project['title']; 
		   $list[$k]['pic'] = $project['pic'];
		   $list[$k]['cidName'] = $cids[$project['cid']]['name'];
		   $list[$k]['status'] = $statusArr[$v['status']];
		}
		$this->assign("page", $showPage);
		$this->assign("list", $list);
		$this->display();
	}	
	public function youhuiadd(){ 
		if(IS_POST){
			$data = $_POST['info'];
			$where['projectid'] = $data['xihuanid'];	
			$where['tuangoustart']=strtotime($data['tuangoustart'].' 00:00:00');
			$where['tuangouend']=strtotime($data['tuangouend'].' 00:00:00');
			$where['shiyongstart']=strtotime($data['shiyongstart'].' 00:00:00');
			$where['shiyongend']=strtotime($data['shiyongend'].' 00:00:00');
			$where['tuangoujia'] = $data['tuangoujia'];
			$where['status'] = $data['status'];
			$where['tuangou'] = 1;
			$add['tuangou'] = 1;
			$add['id'] = $data['xihuanid'];
			M("Project")->save($add);
			if (M('youhui')->add($where)) {
				  echo json_encode(array('status' => 1, 'info' => "设置成功", 'url' => U('Project/youhui')));
			  } else {
				  echo json_encode(array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作"));
			  }
			}
		else{
			//xihuan
			$info['tuangoustart'] = time();
			$info['tuangouend'] = time();
			$info['shiyongstart'] = time();
			$info['shiyongend'] = time();
			$info['status'] = 1;
			$paixu = M("youhui")->where('tuangou=1')->limit(0,1)->order('paixu desc')->find();
			$info['paixu'] = $paixu['paixu']+1;
			$cat = M("Projectcategory")->where('pid=0')->select();
			foreach($cat as $k=>$v){
				$lit =M("Project")->where("status=1 and cid=".$v['cid'])->select(); 
				foreach ( $lit as $ks=>$vs){
					 if(M('youhui')->where('projectid='.$vs['id'])->count() >0){
					  unset($lis[$ks]);
					  }
					}
				$cat[$k]['project'] = $lit;	
				}
			
			$this->assign("info", $info);  
			$this->assign("list", $cat);  
			$this->display();
			}	
	}	
	public function youhuiedit(){
		if(IS_POST){
			$data = $_POST['info'];
			$where['projectid'] = $data['xihuanid'];	
			$where['id'] = $data['id'];	
			$where['tuangoustart']=strtotime($data['tuangoustart'].' 00:00:00');
			$where['tuangouend']=strtotime($data['tuangouend'].' 00:00:00');
			$where['shiyongstart']=strtotime($data['shiyongstart'].' 00:00:00');
			$where['shiyongend']=strtotime($data['shiyongend'].' 00:00:00');
			$where['tuangoujia'] = $data['tuangoujia'];
			$where['status'] = $data['status'];
			$where['tuangou'] = 1;
			$add['tuangou'] = 1;
			$add['id'] = $data['xihuanid'];
			M("Project")->save($add); 
			if (M('youhui')->save($where)) {
				  echo json_encode(array('status' => 1, 'info' => "已经更新", 'url' => U('Project/youhui')));
			  } else {
				  echo json_encode(array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作"));
			  }
			}
		else{
			//xihuan
			 $info = M('youhui')->where("id=" . (int) $_GET['id'])->find();
			  if ($info['id'] == '') {
				  $this->error("不存在该记录");
			  }
			$info['project'] = M("Project")->where("id=" .$info['projectid'])->find();	
			$this->assign("info", $info);  
			$this->display();
			}	
		}
	public function youhuizongpai(){
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
			M("youhui")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			}
		echo json_encode(array("status"=>1,'info'=>"排序成功"));	
		  }
		}	
	public function youhuisubmit() {
		$id = $_GET['id'];
		if(count(explode(',',$id))>1){
			 $where = 'id in('.$id.')';  
			 }
		  else{     
		     $where = 'id='.$id; 
			 }   
		  $list=M("youhui")->where($where)->delete();   
		  if($list!==false) {     
		    echo json_encode(array('status' => 1, 'info' => "成功删除{$list}条")); 
			}else{    
			echo json_encode(array('status' => 0, 'info' => "删除失败" ));  
		    } 
    }					
	public function youhuipaixu(){
		  $id=$_GET['id'];
		  $paixu = $_GET['paixu'];
		  if(empty($id)){
			  echo json_encode(array("status"=>0,'info'=>"无修改，终止提交"));
			  }
		  else{	
		  $arrid = explode(',',$id);
		  $arrpaixu = explode(',',$paixu);
		  foreach ($arrid as $k=>$v){
			  M("youhui")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			  }
		  echo json_encode(array("status"=>1,'info'=>"排序成功"));	
			}
		}		
	  
	 public function dazhe(){
		//项目列表
		$M = M("youhui");
		$wheres['tuangou'] = 2;
		$count = $M->where($wheres)->count();
		import("ORG.Util.Page");       //载入分页类
		$page = new Page($count, 20);
		$showPage = $page->show();
		$list = $M->where($wheres)->order("paixu asc,id desc")->limit($page->firstRow, $page->listRows)->select();
		$cidArr = M("Projectcategory")->field("`cid`,`name`")->select();
		$statusArr = array("隐藏状态", "显示状态");
		foreach ($cidArr as $k => $v) {
            $cids[$v['cid']] = $v;
        }
        unset($cidArr);
		foreach ($list as $k => $v) {
		   $project = M("Project")->where("id=" .$v['projectid'])->find();
		   $list[$k]['title'] = $project['title']; 
		   $list[$k]['pic'] = $project['pic'];
		   $list[$k]['cidName'] = $cids[$project['cid']]['name'];
		   $list[$k]['status'] = $statusArr[$v['status']];
		}
		$this->assign("page", $showPage);
		$this->assign("list", $list);
		$this->display();
	} 
	  public function dazheadd(){ 
		if(IS_POST){
			$data = $_POST['info'];
			$where['projectid'] = $data['xihuanid'];	
			$where['tuangoustart']=strtotime($data['tuangoustart'].' 00:00:00');
			$where['tuangouend']=strtotime($data['tuangouend'].' 00:00:00');
			$where['shiyongstart']=strtotime($data['shiyongstart'].' 00:00:00');
			$where['shiyongend']=strtotime($data['shiyongend'].' 00:00:00');
			$where['dazhe'] = $data['dazhe'];
			$where['status'] = $data['status'];
			$where['tuangou'] = 2;
			$add['tuangou'] = 2;
			$add['id'] = $data['xihuanid'];
			M("Project")->save($add);
			if (M('youhui')->add($where)) {
				  echo json_encode(array('status' => 1, 'info' => "设置成功", 'url' => U('Project/dazhe')));
			  } else {
				  echo json_encode(array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作"));
			  }
			}
		else{
			//xihuan
			$info['tuangoustart'] = time();
			$info['tuangouend'] = time();
			$info['shiyongstart'] = time();
			$info['shiyongend'] = time();
			$info['status'] = 1;
			$paixu = M("youhui")->where('tuangou=1')->limit(0,1)->order('paixu desc')->find();
			$info['paixu'] = $paixu['paixu']+1;
			$cat = M("Projectcategory")->where('pid=0')->select();
			foreach($cat as $k=>$v){
				$lit =M("Project")->where("status=1 and cid=".$v['cid'])->select(); 
				foreach ( $lit as $ks=>$vs){
					 if(M('youhui')->where('projectid='.$vs['id'])->count() >0){
					  unset($lis[$ks]);
					  }
					}
				$cat[$k]['project'] = $lit;	
				}
			
			$this->assign("info", $info);  
			$this->assign("list", $cat); 
			$this->display();
			}	
	}	
	public function dazheedit(){
		if(IS_POST){
			$data = $_POST['info'];
			$where['projectid'] = $data['xihuanid'];	
			$where['id'] = $data['id'];	
			$where['tuangoustart']=strtotime($data['tuangoustart'].' 00:00:00');
			$where['tuangouend']=strtotime($data['tuangouend'].' 00:00:00');
			$where['shiyongstart']=strtotime($data['shiyongstart'].' 00:00:00');
			$where['shiyongend']=strtotime($data['shiyongend'].' 00:00:00');
			$where['dazhe'] = $data['dazhe'];
			$where['status'] = $data['status'];
			$where['tuangou'] = 2;
			$add['tuangou'] = 2;
			$add['id'] = $data['xihuanid'];
			M("Project")->save($add);
			if(M('youhui')->save($where)) {
				  echo json_encode(array('status' => 1, 'info' => "已经更新", 'url' => U('Project/dazhe')));
			  } else {
				  echo json_encode(array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作"));
			  }
			}
		else{
			//xihuan
			 $info = M('youhui')->where("id=" . (int) $_GET['id'])->find();
			  if ($info['id'] == '') {
				  $this->error("不存在该记录");
			  }
			$info['project'] = M("Project")->where("id=" .$info['projectid'])->find();	
			$this->assign("info", $info);  
			$this->display();
			}	
		}
	public function dazhezongpai(){
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
			M("youhui")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			}
		echo json_encode(array("status"=>1,'info'=>"排序成功"));	
		  }
		}	
	public function dazhesubmit() {
		$id = $_GET['id'];
		if(count(explode(',',$id))>1){
			 $where = 'id in('.$id.')';  
			 }
		  else{     
		     $where = 'id='.$id; 
			 }   
		  $list=M("youhui")->where($where)->delete();   
		  if($list!==false) {     
		    echo json_encode(array('status' => 1, 'info' => "成功删除{$list}条")); 
			}else{    
			echo json_encode(array('status' => 0, 'info' => "删除失败" ));  
		    } 
    }					
	public function dazhepaixu(){
		  $id=$_GET['id'];
		  $paixu = $_GET['paixu'];
		  if(empty($id)){
			  echo json_encode(array("status"=>0,'info'=>"无修改，终止提交"));
			  }
		  else{	
		  $arrid = explode(',',$id);
		  $arrpaixu = explode(',',$paixu);
		  foreach ($arrid as $k=>$v){
			  M("youhui")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			  }
		  echo json_encode(array("status"=>1,'info'=>"排序成功"));	
			}
		}
	}
	
?>