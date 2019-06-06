<?php

class NewsModel extends Model {

   

    public function category() {
        if (IS_POST) {
            $act = $_POST[act];
            $data = $_POST['data'];
            $data['name'] = addslashes($data['name']);
            $M = M("Category");
            if ($act == "add") { //添加分类
                unset($data[cid]);
                if ($M->where($data)->count() == 0) {
                    return ($M->add($data)) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功添加到系统中', 'url' => U('News/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 添加失败');
                } else {
                    return array('status' => 0, 'info' => '系统中已经存在分类' . $data['name']);
                }
            } else if ($act == "edit") { //修改分类
                if (empty($data['name'])) {
                    unset($data['name']);
                }
                if ($data['pid'] == $data['cid']) {
                    unset($data['pid']);
                }
                return ($M->save($data)) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功更新', 'url' => U('News/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 更新失败');
            } else if ($act == "del") { //删除分类
                unset($data['pid'], $data['name']);
                return ($M->where($data)->delete()) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功删除', 'url' => U('News/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 删除失败');
            }
        } else {
            import("Category");
            $cat = new Category('Category', array('cid', 'pid', 'name', 'fullname'));
            return $cat->getList();               //获取分类结构
        }
    }

    public function addNews() {
        $M = M("News");
        $data = $_POST['info'];
        $data['published'] = time();
        $data['aid'] = $_SESSION['my_info']['aid'];
		$data['content'] = stripslashes(htmlspecialchars_decode($data['content']));
        if (empty($data['summary'])) {
            $data['summary'] = cutStr($data['content'], 200);
        }
        $data['hot'] = 0;
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->saveRule  = date('Ymj').'_p'.time();// 设置文件名
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->savePath =  C("TMPL_PARSE_STRING.__newspic__");// 设置附件上传目录
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
			$search['url']= 'news';
			$search['search'] = $data['title'].','.$data['keywords'].','.$data['description'].','.$data['summary'];
			M('search')->add($search);
            return array('status' => 1, 'info' => "已经发布", 'url' => U('News/index?cid='.$data['cid'].''));
        } else {
            return array('status' => 0, 'info' => "发布失败，请刷新页面尝试操作");
        }
    }

    public function edit() {
        $M = M("News");
        $data = $_POST['info'];
		$data['content'] = stripslashes(htmlspecialchars_decode($data['content']));
        $data['update_time'] = time();
        $refeurl = $_POST['refeurl'];
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->saveRule  = date('Ymj').'_p'.time();// 设置文件名
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->savePath =  C("TMPL_PARSE_STRING.__newspic__");// 设置附件上传目录
        if(!$upload->upload()) {
        	//$this->error($upload->getErrorMsg());// 上传错误提示错误信息
        }else{
        	$info =  $upload->getUploadFileInfo();// 上传成功 获取上传文件信息
        }
        
        if($info){
        	@unlink(C("TMPL_PARSE_STRING.__newspic__").$data['pic']);
        	$data['pic'] = $info[0]['savename'];
        }
        
        if ($M->save($data)) {
            return array('status' => 1, 'info' => "已经更新", 'url' => ''.$refeurl.'');
        } else {
            return array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作");
        }
    }

}

?>
