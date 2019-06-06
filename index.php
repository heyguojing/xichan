<?php
// define( "GZIP_ENABLE", function_exists ( 'ob_gzhandler') );
// ob_start( GZIP_ENABLE ? 'ob_gzhandler': null );
ob_start();//开启缓存
ini_set('date.timezone', 'Asia/Shanghai');
define('THINK_PATH', './ThinkPHP/');
define('APP_NAME', 'Home');
define('APP_PATH', './Home/');
define('URL_CALLBACK','http://m.xichan.cn/index.php/member/index');
define('APP_DEBUG', true);
define('SITE_PATH', getcwd());//网站当前路径
define("RUNTIME_PATH", SITE_PATH . "/Cache/Runtime/Home/");
define("WEB_ROOT", dirname(__FILE__) . "/");
if (!file_exists(WEB_ROOT.'Common/systemConfig.php')) {
    header("Location: install/");
    exit;
}
$_SERVER['PATH_INFO'] = str_replace('/index.php', '', $_SERVER['PATH_INFO']);
require(THINK_PATH . "ThinkPHP.php");

?>