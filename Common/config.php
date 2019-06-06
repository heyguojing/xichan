<?php

/*
 * 通用配置文件
 * Author：成都迈思信息技术有限公司（appli113@163.com）
 * Date:2013-11-25
 */
$config1 = array(
    /* 数据库设置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'SHOW_PAGE_TRACE' => false,
    'TOKEN_ON' => true, // 是否开启令牌验证
    'TOKEN_NAME' => '__hash__', // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE' => 'md5', //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => FALSE, //令牌验证出错后是否重置令牌 默认为true
    /* 开发人员相关信息 */
    'AUTHOR_INFO' => array(
        'author' => 'zhouyun',
        'author_email' => 'appli113@163.com',
    ),
    'TMPL_PARSE_STRING'  => array(
		'__newspic__'=>'./Uploads/image/news/',  //文章封面图片
    	'__pictureimg__'=>'./Uploads/image/picture/',  //案例图片
		'__expertimg__'=>'./Uploads/image/expert/',  //专家图片
		'__realityimg__'=>'./Uploads/image/reality/',  //美人制造图片
		'__topicsimg__'=>'./Uploads/image/topics/',  //专题图片
		'__adimg__'=>'./Uploads/image/ad/',  //专题图片
        '__userimg__'=>'./Uploads/image/user/',  //专题图片
		'__goodsimg__'=>'./Uploads/image/goods/',  //专题图片
	)
);
$config2 = WEB_ROOT . "Common/systemConfig.php";
$config2 = file_exists($config2) ? include "$config2" : array();
return array_merge($config1, $config2);
?>