<?php

class HandbookAction extends CommonAction {
	public function index(){
		$this->assign('list',D('Handbook')->field("`id`,`name`")->where('status = 1')->order('paixu asc,id desc')->select());
		$this->display();
	}
	
	public function detail(){
		$res = D('Handbook')->where("id = {$_GET['id']} and status = 1")->find();
		if($res){
			$this->assign('info',$res);
			$this->display();
		}else{
			$this->redirect('index');
		}
	}
	public function zhubao(){
		$this->display();
	}	
	// public function zhubao1(){
	// 	header('Content-type:text/html;charset=utf-8');
	// 	$url = 'd://wwwroot/ms/Uploads//bijou';
	// 	$path = $_POST['path'] ? $_POST['path'] : $url;
	// 	$arr = array();
	// 	$dir = $this->get_all_files($path);
	// 	foreach($dir as $key => $val){
	// 		$val = mb_convert_encoding($val,"utf-8","gb2312");
	// 		$arr[$key]['url'] = $val;
	// 		$ar = explode('/',$val);
	// 		$arr[$key]['val'] = $ar[count($ar) - 1];
	// 	}
	// 	$this->assign('dir',$arr);
	// 	$this->display();
	// }	
	public function zhubao1(){
		header('Content-type:text/html;charset=utf-8');
		//初始url
		$url = 'd://wwwroot/ms/Uploads//bijou';
		if($_GET['path'] && !empty($_GET['path'])){
			$uri = explode('/',$_GET['path']);
		}
		$path = $uri[1] ? $url.'/'.$uri[1] : $url;
		$path = mb_convert_encoding($path,"gb2312",'utf-8');
		$arr = array();
		//遍历当前url下的目录及文件夹返回一个数组到$dir
		$dir = $this->read_all_dir($path);
		//组成新的数组输出到页面
		foreach($dir['file'] as $key => $val){
			$val = mb_convert_encoding($val,"utf-8","gb2312");
			$arr[$key]['url'] = $val;

			//打开目录，读取目录信息，并判断目录的类型
			$rdir = readdir(opendir(iconv('UTF-8','GB2312',$arr[$key]['url'])));
			if(fileType($rdir) == 'dir'){
				$arr[$key]['type'] ='dir';
			}else{
				$arr[$key]['type'] = 'file';
				//获得文件的路径furi
				$furi = explode('ms',$arr[$key]['url']);
				$arr[$key]['file_uri'] = $furi[1];
			}
			//获得文件的创建时间
			// $arr[$key]['mtime'] = filemtime($arr[$key]['file_uri']);
			$ar = explode('/',$val);
			$arr[$key]['val'] = $ar[count($ar) - 1];
		}
		// echo '<pre>';
		// var_dump($arr);
		// echo '</pre>';
		$this->assign('dir',$arr);
		$this->display();
	}	

	public function zbdetail(){
		//初始url
		$url = 'd://wwwroot/ms/Uploads//bijou';
		if($_GET['path'] && !empty($_GET['path'])){
			$uri = explode('/',$_GET['path']);
		}
		$path = $uri[1] ? $url.'/'.$uri[1] : $url;
		$path = mb_convert_encoding($path,"gb2312",'utf-8');
		$arr = array();
		//遍历当前url下的目录及文件夹返回一个数组到$dir
		$dir = $this->read_all_dir($path);
		foreach($dir['file'] as $key => $val){
			$rdir = readdir(opendir(iconv('UTF-8','GB2312',$arr[$key]['url'])));
			$val = mb_convert_encoding($val,"utf-8","gb2312");
			if(fileType($rdir) !== 'dir'){
				$furi = explode('ms',$val);
				$arr[$key]['file_uri'] = $furi[1];
			}
		}
		$this->assign('url',$arr);
		$this->display();
	}

	private  function read_all_dir ( $dir )
    {
        $result = array();
        $handle = opendir($dir);
        if ( $handle )
        {
            while ( ( $file = readdir ( $handle ) ) !== false )
            {
                if ( $file != '.' && $file != '..')
                {
                    $cur_path = $dir.'/'.$file;
                    if ( is_dir ( $cur_path ) )
                    {
                        $result['dir'][$cur_path] = read_all_dir ( $cur_path );
                    }
                    else
                    {
                        $result['file'][] = $cur_path;
                    }
                }
            }
            closedir($handle);
        }
        return $result;
    }	
}