<?php
class UsercommAction extends Action {
	public function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
        $systemConfig = include WEB_ROOT . 'Common/systemConfig.php';
		$this->assign('systemConfig', $systemConfig);
		
        $projectcount = M('project')->where('status=1 and tuangou>0')->count();
		$this->assign('projectcount',$projectcount);
		
		$topicscount = M('topics')->count();
		$this->assign('topicscount',$topicscount);
		
		if(!isset( $_SESSION['USER_AUTH_KEY'])){

		}else{
			$where['uid']=$_SESSION['USER_AUTH_KEY'];
			$userinfo = M('member')->where($where)->find();
			$login = '<div id="islogin">';
            $login = '    <p class="u_loginname">';
            $login = '        <img src="__PUBLIC__/images/nav_uico.jpg"/>';
            $login = '       <strong>'.$userinfo['nickname'].'</strong>';
            $login = '    </p>';
            $login = '    <p class="u_loginop ">';
            $login = '        <a href="'.U('/Member/index').'">管理</a>';
            $login = '    </p>';
            $login = '</div>';
			$this->assign('login',$login);
			$this->assign('userinfo',$userinfo);
			}	 
	}

	
	
}