<?php
class CommonAction extends Action {
	public function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
        
        $systemConfig = include WEB_ROOT . 'Common/systemConfig.php';
		$this->assign('systemConfig', $systemConfig);
        
		$projectcount = M('youhui')->where('status=1')->count();
		$this->assign('projectcount',$projectcount);
		
		$topicscount = M('topics')->count();
		$this->assign('topicscount',$topicscount);
		
		if(empty( $_SESSION['USER_AUTH_KEY'])){
			    $menureg = '<div id="nologin">';
                $menureg .= '<p class="u_loginname">';
                $menureg .= '    <img src="__PUBLIC__/images/nav_uico.jpg"/>';
                $menureg .= '    <strong>游客</strong>';
                $menureg .= '</p>';
                $menureg .= '<p class="u_loginop ">';
                $menureg .= '    <a href="'.U('User/index').'">登录</a> <a href="'.U('User/regist').'">注册</a>';
                $menureg .= '</p>';
                $menureg .= '</div>';
				$this->assign('menureg',$menureg);
			  }
		else{
			$where['uid']=$_SESSION['USER_AUTH_KEY'];
			$userinfo = M('member')->where($where)->find();
			$menureg = '<div id="islogin">';
            $menureg .= '    <p class="u_loginname">';
            $menureg .= '        <img src="__PUBLIC__/images/nav_uico.jpg"/>';
            $menureg .= '        <strong>'.$userinfo['nickname'].'</strong>';
            $menureg .= '    </p>';
            $menureg .= '    <p class="u_loginop ">';
            $menureg .= '        <a href="'.U('Member/index').'">个人中心</a><a href="'.U('Member/loginOut').'">退出</a>';
            $menureg .= '    </p>';
            $menureg .= '</div>';
			
			$this->assign('menureg',$menureg);
			$this->assign('userinfo',$info);
			}
		
	}

}