<?php

ob_start();
ini_set('date.timezone', 'Asia/Shanghai');
define('THINK_PATH', './ThinkPHP/');
define('APP_NAME', 'Home');
define('APP_PATH', './Home/');
define('APP_DEBUG', TRUE);
define('SITE_PATH', getcwd());//网站当前路径
define("RUNTIME_PATH", SITE_PATH . "/Cache/Runtime/Home/");
define("WEB_ROOT", dirname(__FILE__) . "/");
if (!file_exists(WEB_ROOT.'Common/systemConfig.php')) {
    header("Location: install/");
    exit;
}
//$_SERVER['PATH_INFO'] = str_replace('/index.php', '', $_SERVER['PATH_INFO']);
require(THINK_PATH . "ThinkPHP.php");



?>