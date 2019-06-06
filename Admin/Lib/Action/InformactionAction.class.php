<?php
class InformactionAction extends CommonAction {
	public function index(){
		if (IS_POST) {
			$post = $_POST;
			if($post['act'] == 'classadd'){
			    $M = M("Informaction");
			    $classname['title'] = $post['title'];
			    $classname['status'] = $post['status'];
				if ($M->add($classname)) {
					  echo json_encode(array("status" => 1, "info" => "单页面 ".$post['classname']." 添加成功",'url' => U('Informaction/index')));
				  } else {
					  echo json_encode(array("status" => 0, "info" => "类别添加失败"));
				  }
			   }
			elseif($post['act'] == 'edit'){
				$M = M("Informaction");
				$classname['id'] = $post['id'];
				if(!empty($post['title'])){
			    $classname['title'] = $post['title'];
				}
			    $classname['status'] = $post['status'];
				if ($M->save($classname)) {
					  echo json_encode(array("status" => 1, "info" => "单页面 ".$post['classname']." 修改成功",'url' => U('Informaction/index')));
				  } else {
					  echo json_encode(array("status" => 0, "info" => "类别添加失败"));
				  }
				}
        } else {
        	$form = M('Informaction');
			$list = $form->select();
			//$classidarr = M('Informactionclass')->select();
			$this->assign('list',$list);
            $this->display();
        }
		}
	public function editbox(){
		if (IS_POST) {
			$data = $_POST['info'];
			$data['content'] = stripslashes(htmlspecialchars_decode($data['content']));
           if (M('informaction')->save($data)) {
    			echo json_encode(array('status' => 1, 'info' => "发布成功", 'url' => U('Informaction/editbox?id='.$data['id'].'')));
    		}else {
    			echo json_encode(array('status' => 0, 'info' => "发布失败，请刷新页面尝试操作"));
    		}
        }
		else{
		 $info = M('informaction')->where("id=" . (int) $_GET['id'])->find();
            if ($info['id'] == '') {
                $this->error("不存在该记录");
            }
		$this->assign('info',$info);
		$this->assign('boxname','修改'.$info['title'].'信息');
    	$this->display();
		}
		}	
	}
?>