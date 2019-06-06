<?php
class IndexboxAction extends CommonAction {
	public function index(){
		//项目列表
		if (IS_POST) {
			$act = $_POST[act];
            $data = $_POST['data'];
			if ($act == "add") {
				if (M('indexbox')->add($data)) {
					  echo json_encode(array('status' => 1, 'info' => "已经发布", 'url' => U('Indexbox/index')));
				  } else {
					  echo json_encode(array('status' => 0, 'info' => "发布失败，请刷新页面尝试操作"));
				  }
				}
			elseif($act == "edit"){
				if (M('indexbox')->save($data)) {
					  echo json_encode(array('status' => 1, 'info' => "修改成功", 'url' => U('Indexbox/index')));
				  } else {
					  echo json_encode(array('status' => 0, 'info' => "修改失败，请刷新页面尝试操作"));
				  }
				}
			elseif($act == "del"){
				$decda['id'] = $data['id'];
				if (M('indexbox')->where($decda)->delete()) {
					  echo json_encode(array('status' => 1, 'info' => "删除成功", 'url' => U('Indexbox/index')));
				  } else {
					  echo json_encode(array('status' => 0, 'info' => "删除失败，请刷新页面尝试操作"));
				  }
				}		
			}
		else{	
		  $M = M("indexboxcategory");
		  $list = $M->select();
		  foreach ($list as $k => $v) {
			  $list[$k]['indexbox'] = M('indexbox')->where('cid='.$v['cid'])->select();
		  }
		  $this->assign("list", $list);
		  $this->display();
		  }
		}
	public function category() {
		 if (IS_POST) {
            $act = $_POST[act];
            $data = $_POST['data'];
            $data['name'] = addslashes($data['name']);
            $M = M("indexboxcategory");
            if ($act == "add") { //添加分类
                unset($data[cid]);
                if ($M->where($data)->count() == 0) {
                    echo json_encode(($M->add($data)) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功添加到系统中', 'url' => U('Indexbox/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 添加失败'));
                } else {
                    echo json_encode(array('status' => 0, 'info' => '系统中已经存在分类' . $data['name']));
                }
            } else if ($act == "edit") { //修改分类
                if (empty($data['name'])) {
                    unset($data['name']);
                }
                if ($data['pid'] == $data['cid']) {
                    unset($data['pid']);
                }
                echo json_encode(($M->save($data)) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功更新', 'url' => U('Indexbox/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 更新失败'));
            } else if ($act == "del") { //删除分类
                unset($data['pid'], $data['name']);
                echo json_encode(($M->where($data)->delete()) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功删除', 'url' => U('Indexbox/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 删除失败'));
            }
        } else {             
		    import("indexboxcategory");
            $cat = new Category('indexboxcategory', array('cid', 'pid', 'name', 'fullname'));
            $list=$cat->getList();
			$this->assign("list", $list);
            $this->display();
        }
    }
	
	public function del(){
		//删除项目
           $temp = M("Indexbox")->where("id=" . (int) $_GET['id'])->find();
    	
		  if (M("Indexbox")->where("id=" . (int) $_GET['id'])->delete()) {
			  echo json_encode(array("status" => 1, "info" => "删除成功",'url' => U('Indexbox/index')));
		  } else {
			  echo json_encode(array("status" => 0, "info" => "删除失败，可能是不存在该ID的记录"));
		  }
		}	
	}
?>