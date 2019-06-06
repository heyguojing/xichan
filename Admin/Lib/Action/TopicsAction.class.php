<?php
class TopicsAction extends CommonAction {
	public function index(){
		//项目列表
		$M = M("topics");
		  
		if(!empty($_GET['cid'])){
		   $wheres['cid'] = $_GET['cid'];
		   $classone = M('topicscategory')->where($wheres)->find();  
		  }
		  
		if(!empty($_POST['keywords'])){
			 $wheres['title'] = array('like',"%".$_POST['keywords']."%");
			}
		
        $count = $M->where($wheres)->count();
        import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 20);
        $showPage = $page->show();
        $list = $M->where($wheres)->field("`id`,`title`,`published`,`paixu`,`status`,`pic`,`cid`")->order("paixu asc,id desc")->limit($page->firstRow, $page->listRows)->select();
		$statusArr = array("隐藏状态", "显示状态");
        $cidArr = M("topicscategory")->field("`cid`,`name`")->select();
        foreach ($cidArr as $k => $v) {
            $cids[$v['cid']] = $v;
        }
        unset($cidArr);
        foreach ($list as $k => $v) {
            $list[$k]['guoji'] = $v['guoji']==""?'':'('.$v['guoji'].')';
			$list[$k]['status'] = $statusArr[$v['status']];
            $list[$k]['cidName'] = $cids[$v['cid']]['name'];
        }
        $this->assign("page", $showPage);
        $this->assign("list", $list);
		$category = M('topicscategory')->field("`cid`,`name`")->select();
		$this->assign('category',$category);
        $this->display();
		}
	public function category() {
        if (IS_POST) {
            echo json_encode(D("Topics")->category());
        } else {
        	$this->assign("list", D("Topics")->category());
            $this->display();
        }
    }
	public function add(){
		//添加项目
            if (IS_POST) {
            $M = M("topics");
			$data = $_POST['info'];
			$data['content'] = stripslashes(htmlspecialchars_decode($data['content']));
			$data['published'] = time();
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();// 实例化上传类
			$upload->maxSize  = 3145728 ;// 设置附件上传大小
			$upload->saveRule  = date('Ymj').'_p'.time();// 设置文件名
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath =  C("TMPL_PARSE_STRING.__topicsimg__");// 设置附件上传目录
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
			$search['url']= 'topics';
			$search['search'] = $data['title'].','.$data['keywords'].','.$data['description'].','.$data['summary'].','.$data['xiaoguo'];
			M('search')->add($search);
				echo json_encode(array('status' => 1, 'info' => "已经发布", 'url' => U('Topics/index')));
			} else {
				echo json_encode(array('status' => 0, 'info' => "发布失败，请刷新页面尝试操作"));
			}
        } else {
			$info['status'] = 1;
			$info['reshow'] = 0;
			$info['fenxiang'] = 0;
			$paixu = M("topics")->limit(0,1)->order('paixu desc')->find();
			$info['paixu'] = $paixu['paixu']+1;
			$this->assign('info',$info);
            $this->assign("list", D("Topics")->category());
			$titlename = "添加专题信息";
			$this->assign('titlename',$titlename);
            $this->display();
        }
		}
	public function edit(){
		//修改项目
          $M = M("topics");
		  if (IS_POST) {
			  $this->checkToken();
			  $data = $_POST['info'];
			  $data['content'] = stripslashes(htmlspecialchars_decode($data['content']));
			  $refeurl = $_POST['refeurl'];
			  import('ORG.Net.UploadFile');
			  $upload = new UploadFile();// 实例化上传类
			  $upload->maxSize  = 3145728 ;// 设置附件上传大小
			  $upload->saveRule  = date('Ymj').'_p'.time();// 设置文件名
			  $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			  $upload->savePath =  C("TMPL_PARSE_STRING.__topicsimg__");// 设置附件上传目录
			  if(!$upload->upload()) {
				  //$this->error($upload->getErrorMsg());// 上传错误提示错误信息
			  }else{
				  $info =  $upload->getUploadFileInfo();// 上传成功 获取上传文件信息
			  }
			  
			  if($info){
				  @unlink(C("TMPL_PARSE_STRING.__Topicsimg__").$data['pic']);
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
			  $this->assign("refeurl",$refeurl);
			  $this->assign("info", $info);
			  $titlename = "修改专题信息";
			  $this->assign('titlename',$titlename);
			  $this->assign("list", D("Topics")->category());
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
			M("topics")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
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
		  $list=M("topics")->where($where)->delete();   
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
			  M("topics")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			  }
		  echo json_encode(array("status"=>1,'info'=>"排序成功"));	
			}
		}
	 public function guanlian(){
		$M = M("topicslove");
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
					  echo json_encode(array("status" => 1, "info" => "添加成功" ,'url' => U('Topics/guanlian?cid='.$cid.'')));
				  } else {
					  echo json_encode(array("status" => 0, "info" => "添加失败"));
				  }
			   }
			 else{
				 if ($M->save($wheres)) {
					  echo json_encode(array("status" => 1, "info" => "修改成功" ,'url' => U('Topics/guanlian?cid='.$cid.'')));
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
			M("brandlove")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
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
		  $list=M("topicslove")->where($where)->delete();   
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
			  M("topicslove")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			  }
		  echo json_encode(array("status"=>1,'info'=>"排序成功"));	
			}
		}		
		
	}
?>