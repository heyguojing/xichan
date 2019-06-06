<?php

$config_arr1 = include_once WEB_ROOT . 'Common/config.php';
$config_arr2 = array(
    'URL_MODEL'            =>1,
    //'SHOW_PAGE_TRACE'      =>True,
    'DB_FIELDTYPE_CHECK'   =>true,
    'TMPL_STRIP_SPACE'     =>true,
    'OUTPUT_ENCODE'        =>true,
    'URL_CASE_INSENSITIVE' =>true,
    'URL_HTML_SUFFIX'      =>'.html',
    'OUTPUT_ENCODE' => false,//关闭gzip压缩功能
    //'URL_PATHINFO_DEPR'=>'_',
    'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES' => array(
    	'news/:id\d'                 => 'News/info',
		'category/:id\d'             => 'News/index',
		'newszhan/:id\d'             => 'News/zhan',
		'newszhans/:id\d'            => 'News/zhans',
		'infonews/:id\d'             => 'News/infonews',
		'proinfo/:id\d'              => 'Project/info',
		'p_category/:id\d'           => 'Project/category',
		'projectlist/:id\d'          => 'Project/projectlist',
		'payment/:id\d'              => 'Project/payment',
		'projectzhan/:id\d'          => 'Project/zhan',
		'projectzhans/:id\d'         => 'Project/zhans',
		'infoproject/:id\d'          => 'Project/infoproject',
		'infoexpert/:id\d'           => 'Expert/infoexpert',
		'expertinfo/:id\d'           => 'Expert/info',
		'expertzhan/:id\d'           => 'Expert/zhan',
		'expertzhans/:id\d'          => 'Expert/zhans',
		'brandinfo/:id\d'            => 'Brand/brandinfo',
		'expertlist/:id\d'           => 'Expert/index',
		'realshow/:id\d'             => 'Reality/info',
		'reality1/:id\d'             => 'Reality/index',
		'realityzhan/:id\d'          => 'Reality/zhan',
		'realityzhans/:id\d'         => 'Reality/zhans',
		'inforeality/:id\d'          => 'Reality/inforeality',
		'brandinfo/:id\d'            => 'Brand/info',
		//'brand'                      => 'Brand/index',
		'brandzhan/:id\d'            => 'Brand/zhan',
		'brandzhans/:id\d'           => 'Brand/zhans',
		'infobrand/:id\d'            => 'Brand/infobrand',
		'brandlist/:id\d'            => 'Brand/index',
		'discount/index'             => 'Discount/index',
		'discount'                   => 'Discount/index',
		'tuangou/:id\d'              => 'Discount/index',
		'discountzhan/:id\d'         => 'Discount/zhan',
		'topics/:id\d'               => 'Topics/index',
		'topics/index'               => 'Topics/index',
		'topieszhan/:id\d'           => 'Topics/zhan',
		'topicsinfo/:id\d'           => 'Topics/info',
		'caseinfo/:id\d'             => 'Case/info',
		'casecat/:id\d'              => 'Case/index',
		'casezhan/:id\d'             => 'Case/zhan',
	),
    //短信验证接入功能
    'message_uid' => 'LKSDK00085',
    'message_passwd' => '506122',


	    //支付宝配置参数
    'alipay_config'=>array(
           'partner' =>'2088201228403271',   //这里是你在成功申请支付宝接口后获取到的PID；
           'key'=>'xaa4no88d8ih2ndprk8awc5rwnln1ogl',//这里是你在成功申请支付宝接口后获取到的Key
           'sign_type'=>strtoupper('MD5'),
           'input_charset'=> strtolower('utf-8'),
           'cacert'=> getcwd().'\\cacert.pem',
           'transport'=> 'http',
          ),
         //以上配置项，是从接口包中alipay.config.php 文件中复制过来，进行配置；

    'alipay'   =>array(
	    'seller_email'=>'shesays@xichan.cn',//这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
	    'notify_url'=>'http://m.xichan.cn/Pay/notifyurl', //这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
	    'return_url'=>'http://m.xichan.cn/Pay/returnurl',//这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
	    'successpage'=>'http://m.xichan.cn/User/myorder?ordtype=payed', //支付成功跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参payed（已支付列表）
	    'errorpage'=>'http://m.xichan.cn/User/myorder?ordtype=unpay', //支付失败跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参unpay（未支付列表）
    ),
	'THINK_SDK_QQ' => array(
		'APP_KEY'    => '1106610638', //应用注册成功后分配的 APP ID
		'APP_SECRET' => 'CnsKL1eiXx0To94a', //应用注册成功后分配的KEY
		'CALLBACK'   => URL_CALLBACK . 'qq',
	),
	//新浪微博配置
	'THINK_SDK_SINA' => array(
		'APP_KEY'    => '1086249468', //应用注册成功后分配的 APP ID
		'APP_SECRET' => 'b8992c7040a695c19f7f8009f9b492ef', //应用注册成功后分配的KEY
		'CALLBACK'   => URL_CALLBACK,
	),
	'TMPL_PARSE_STRING'  => array(
		'__newspic__'	=>__ROOT__.'/Uploads/image/news/',  //文章封面图片
		'__pictureimg__'=>__ROOT__.'/Uploads/image/picture/',  //案例图片
		'__expertimg__'=>__ROOT__.'/Uploads/image/expert/',  //专家图片
		'__realityimg__'=>__ROOT__.'/Uploads/image/reality/',  //美人制造图片
		'__topicsimg__'=>__ROOT__.'/Uploads/image/topics/',  //专题图片
		'__adimg__'=>__ROOT__.'/Uploads/image/ad/',  //专题图片
		'__userimg__'=>__ROOT__.'/Uploads/image/user/',  //专题图片
		'__flv__'=>__ROOT__.'/Uploads/flv/',  //专题图片
		'__push__'=>__ROOT__.'Uploads/image/push1',//专题图片
	)
);


return array_merge($config_arr1, $config_arr2);
?>