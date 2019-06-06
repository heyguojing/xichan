<?php

// 本类设置项目一些常用信息
class WebinfoAction extends CommonAction {

    /**
      +----------------------------------------------------------
     * 配置网站信息
      +----------------------------------------------------------
     */
    public function index() {
        $this->checkSystemConfig();
    }

    /**
      +----------------------------------------------------------
     * 配置网站邮箱信息
      +----------------------------------------------------------
     */
    public function setEmailConfig() {
        $this->checkSystemConfig("SYSTEM_EMAIL");
    }

    /**
      +----------------------------------------------------------
     * 配置网站信息
      +----------------------------------------------------------
     */
    public function setSafeConfig() {
        $this->checkSystemConfig("TOKEN");
    }

    /**
      +----------------------------------------------------------
     * 网站配置信息保存操作等
      +----------------------------------------------------------
     */
    private function checkSystemConfig($obj = "SITE_INFO") {
        if (IS_POST) {
            $this->checkToken();
            
            import('ORG.Net.UploadFile');
    		$upload = new UploadFile();// 实例化上传类
    		$upload->maxSize  = 3145728 ;// 设置附件上传大小
    		$upload->saveRule  = 'logo';// 设置文件名
    		$upload->uploadReplace=true;//如果存在同名文件是否进行覆盖 
    		$upload->allowExts  = array('gif');// 设置附件上传类型
    		$upload->savePath =  './Public/Images/';// 设置附件上传目录
    		
    		if($upload->upload()) {
    			//$this->error($upload->getErrorMsg());// 上传错误提示错误信息
    			exit(json_encode(array('status' => 1, 'info' => 'LOGO更新成功...')));
    		}
            
            $config = WEB_ROOT . "Common/systemConfig.php";
            $config = file_exists($config) ? include "$config" : array();
            $config = is_array($config) ? $config : array();
            $config = array_merge($config, array("$obj" => $_POST));
            if ($obj=='SITE_INFO'){
            	$str = '网站配置信息';
            }elseif ($obj=='SYSTEM_EMAIL'){
            	$str = '系统邮箱配置';
            }else {
            	$str = '安全设置';
            }
            //$str = $obj == "SITE_INFO" ? "网站配置信息" : $obj == "SYSTEM_EMAIL" ? "系统邮箱配置" : "安全设置";
            if (F("systemConfig", $config, WEB_ROOT . "Common/")) {
                delDirAndFile(WEB_CACHE_PATH . "Runtime/Admin/~runtime.php");
                if ($obj == "TOKEN") {
                    unset($_SESSION, $_COOKIE);
                    echo json_encode(array('status' => 1, 'info' => $str . '已更新，你需要重新登录', 'url' => __APP__ . '?' . time()));
                } else {
                    echo json_encode(array('status' => 1, 'info' => $str . '已更新'));
                }
            } else {
                echo json_encode(array('status' => 0, 'info' => $str . '失败，请检查', 'url' => __ACTION__));
            }
        } else {
            $this->display();
        }
    }

    /**
      +----------------------------------------------------------
     * 测试邮件账号是否配置正确
      +----------------------------------------------------------
     */
    public function testEmailConfig() {
        C('TOKEN_ON', false);
        $return = send_mail($_POST['test_email'], "", "测试配置是否正确", "这是一封测试邮件，如果收到了说明配置没有问题", "", $_POST);
        if ($return == 1) {
            echo json_encode(array('status' => 1, 'info' => "测试邮件已经发往你的邮箱" . $_POST['test_email'] . "中，请注意查收"));
        } else {
            echo json_encode(array('status' => 0, 'info' => "$return"));
        }
    }

}

?>