<?php
class CaseAction extends CommonAction {
	public function index(){
		//项目列表
		$M = M("Case");
		  
		if(!empty($_GET['cid'])){
		   $wheres['cid'] = $_GET['cid'];
		   $classone = M('Casecategory')->where($wheres)->find();  
		  }
		  
		if(!empty($_POST['keywords'])){
			 $wheres['title'] = array('like',"%".$_POST['keywords']."%");
			}
		
		$wheres['status'] = 1;	
        $count = $M->where($wheres)->count();
        import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 20);
        $showPage = $page->show();
        $list = $M->where($wheres)->order("`published` DESC")->limit($page->firstRow, $page->listRows)->select();
        $aidArr = M("Admin")->field("`aid`,`email`,`nickname`")->select();
        foreach ($aidArr as $k => $v) {
            $aids[$v['aid']] = $v;
        }
        unset($aidArr);
        $cidArr = M("Casecategory")->field("`cid`,`name`")->select();
        foreach ($cidArr as $k => $v) {
            $cids[$v['cid']] = $v;
        }
        unset($cidArr);
		$statusArr = array("审核状态", "已发布状态");
		$hotArr = array('','<span style="color:#F00">(热)</span>');
        foreach ($list as $k => $v) {
            $list[$k]['status'] = $statusArr[$v['status']];
            $list[$k]['cidName'] = $cids[$v['cid']]['name'];
			$list[$k]['hot'] = $hotArr[$v['hot']];
			$cookistr=explode(';',$v['project']);
			   foreach ($cookistr as $sd=>$sid){
				   $str=explode(',',$sid);
				$project .= $str[0].'&nbsp;&nbsp;';
				}
			 $list[$k]['project'] = $project;
			 unset($project);
        }
        $this->assign("page", $showPage);
        $this->assign("list", $list);
		$category = M('Casecategory')->field("`cid`,`name`")->select();
		$this->assign('category',$category);
        $this->display();
		}
	public function category() {
        if (IS_POST) {
            echo json_encode(D("Case")->category());
        } else {
        	$this->assign("list", D("Case")->category());
            $this->display();
        }
    }
	 public function checkNewsTitle() {
        $M = M("Case");
        $where = "title='" . $this->_get('title') . "'";
        if (!empty($_GET['id'])) {
            $where.=" And id !=" . (int) $_GET['id'];
        }
        if ($M->where($where)->count() > 0) {
            echo json_encode(array("status" => 0, "info" => "已经存在，请修改标题"));
        } else {
            echo json_encode(array("status" => 1, "info" => "可以使用"));
        }
    }
	public function add(){
		//添加项目
            if (IS_POST) {
            $this->checkToken();
            $M = M("Case");
			$data = $_POST['info'];
			$pid = M('Casecategory')->where('cid='.$data['cid'])->find();
			$data['pid']= $pid['pid'];
			$data['published']=time();
			$data['content'] = stripslashes(htmlspecialchars_decode($data['content']));
			$projecttitle = $data['projecttitle'];
			$projecturl = $data['projecturl'];
			
			foreach ($projecttitle as $k=>$v){
				if($zhuan==""){
				    $zhuan=$v.','.$projecturl[$k];
					}
				  else{
					  $zhuan=$zhuan.';'.$v.','.$projecturl[$k];
				  }
				}
		    $data['project'] = $zhuan; 
			unset($data['projecttitle']);
			unset($data['projecturl']);
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
			$search['url']= 'case';
			$search['search'] = $data['title'].','.$data['keywords'].','.$data['description'];
			M('search')->add($search);
				echo json_encode(array('status' => 1, 'info' => "已经发布", 'url' => U('Case/index?cid='.$data['cid'].'')));
			} else {
				echo json_encode(array('status' => 0, 'info' => "发布失败，请刷新页面尝试操作"));
			}
        } else {
			$info['status'] = 1;
			$info['reshow'] = 0;
			$info['fenxiang'] = 0;
			$info['hot'] = 0;
			$titlename = "添加案例信息";
			$this->assign('titlename',$titlename);
			$paixu = M("case")->limit(0,1)->order('paixu desc')->find();
			$info['paixu'] = $paixu['paixu']+1;
            $this->assign("list", D("Case")->category());
			$this->assign('info',$info);
            $this->display();
        }
		}
	public function edit(){
		//修改项目
          $M = M("Case");
		  if (IS_POST) {
			  $this->checkToken();
			  $data = $_POST['info'];
			  $refeurl = $_POST['refeurl'];
			  $pid = M('Casecategory')->where('cid='.$data['cid'])->find();
			  $data['pid']= $pid['pid'];
			  $data['published'] = time();
			  $data['content'] = stripslashes(htmlspecialchars_decode($data['content']));
			  $projecttitle = $data['projecttitle'];
			  $projecturl = $data['projecturl'];
			  
			  foreach ($projecttitle as $k=>$v){
				  if($zhuan==""){
					  $zhuan=$v.','.$projecturl[$k];
					  }
					else{
						$zhuan=$zhuan.';'.$v.','.$projecturl[$k];
					}
				  }
			  $data['project'] = $zhuan; 
			  unset($data['projecttitle']);
			  unset($data['projecturl']);
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
			  $cookistr=explode(';',$info['project']);
			   unset($info['project']);
			   $project=array();
			   foreach ($cookistr as $sd=>$sid){
				   $str=explode(',',$sid);
					$project[$sd]['title'] = $str[0];
					$project[$sd]['url'] = $str[1];
				}
			  $info['project'] = $project;
			  $titlename = "修改案例信息";
			  $this->assign('titlename',$titlename);
			  $this->assign("info", $info);
			  $this->assign("list", D("Case")->category());
			  $this->assign("refeurl",$refeurl);
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
			M("Case")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
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
		  $list=M("Case")->where($where)->delete();   
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
			  M("Case")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			  }
		  echo json_encode(array("status"=>1,'info'=>"排序成功"));	
			}
		}
	 public function guanlian(){
		$M = M("caselove");
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
					  echo json_encode(array("status" => 1, "info" => "添加成功" ,'url' => U('Case/guanlian?cid='.$cid.'')));
				  } else {
					  echo json_encode(array("status" => 0, "info" => "添加失败"));
				  }
			   }
			 else{
				 if ($M->save($wheres)) {
					  echo json_encode(array("status" => 1, "info" => "修改成功" ,'url' => U('Case/guanlian?cid='.$cid.'')));
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
			M("caselove")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
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
		  $list=M("caselove")->where($where)->delete();   
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
			  M("caselove")->save(array('id'=>$v,'paixu'=>$arrpaixu[$k]));
			  }
		  echo json_encode(array("status"=>1,'info'=>"排序成功"));	
			}
		}			
			
	}
?>