<?php

$config_arr1 = include_once WEB_ROOT . 'Common/config.php';
$DB_PREFIX = $config_arr1['DB_PREFIX'];
$config_arr2 = array(
    'admin_big_menu' => array(
        'Index' => '首页',
		//'Banner'=>'banner管理',
		
		'Project'=>'项目管理',
//      'Member' => '用户管理',
		//'Indexbox' => '首页信息设置',
        'Reality' => '美人制造',
		'Topics' => '专题管理',
        //'Webinfo'=>'系统设置'
		'Brand'=>'品牌管理',
		'Case' => '案例管理',
		'Expert'=>'专家团队',
		'News' => '资讯管理',
		// 'Message' =>'短信管理',
		'Informaction'=>'来院地图',
        'SysData' => '数据管理',
        // 'Push'=>'客户管理',
		// 'Handbook' =>'手册管理',
        'Access' => '权限管理',
        // 'Other'=>'其他功能',
        'Shop'=>'购物管理',
    ),
    'admin_sub_menu' => array(
        'Common' => array(
            'Index/myInfo' => '修改密码',
            'Index/cache' => '缓存清理',
            'News/add' => '新闻发布',
			'Banner/index' => '广告位设置',
			'Indexbox/index' => '首页整形项目设置',
			'Member/dingdan' => '查询订单',
			'Pay/index' =>'支付设置',
            'Message/index'=>'短信管理',
            'Handbook/index'=>'手册管理',
            'Push/index'=>'客户管理',
        ),
        // 'Other'=>array(
        //     'Message/index'=>'短信管理',
        //     ),
		'Pay' => array(
            'index' => '支付管理',
        ),
        'Handbook'=>array(
        	'index'=>'手册列表',
        	'add'  =>'添加手册',
        ),
        'Webinfo' => array(
            'index' => '站点配置',
            'setEmailConfig' => '邮箱配置',
            'setSafeConfig' => '安全配置',
        ),
		'Project' => array(
            'index' => '项目列表',
			'category' => '项目分类管理',
			'add' => '添加项目',
			'youhui' => '团购项目列表',
			'youhuiadd' => '添加团购项目',
			'dazhe' => '打折项目列表',
			'dazheadd' => '添加打折项目',
        ),
		'Banner' => array(
            'index' => 'banner列表',
			'category' => '项目分类管理',
			'add' => '添加图片',
        ),
		'Brand' => array(
            'index' => '品牌列表',
			'category' => '分类管理',
			'add' => '添加信息',
        ),
		'Indexbox' => array(
            'index' => '项目列表',
			'category' => '项目分类管理',
        ),
		'Expert' => array(
            'index' => '专家列表',
			'category' => '专家分类管理',
			'add' => '添加专家',
        ),
		'Topics' => array(
            'index' => '专题列表',
			'category' => '专题分类管理',
			'add' => '添加专题',
        ),
		'Reality' => array(
            'index' => '美人制造列表',
			'add' => '添加信息',
        ),
		
		'Case' => array(
            'index' => '案例列表',
			'category' => '案例分类管理',
			'add' => '添加案例',
        ),
		'Informaction'=>array(
		    'index' => '医院名称管理',
		),
        'Member' => array(
            'index' => '注册用户列表',
			'dingdan' => '查询订单',
        ),
        'News' => array(
            'index' => '新闻列表',
            'category' => '新闻分类管理',
			'add' => '发布新闻',
        ),
        'SysData' => array(
            'index' => '数据库备份',
            'restore' => '数据库导入',
            'zipList' => '数据库压缩包',
            'repair' => '数据库优化修复'
        ),
        'Access' => array(
            'index' => '后台用户',
            'nodeList' => '节点管理',
            'roleList' => '角色管理',
            'addAdmin' => '添加管理员',
            'addNode' => '添加节点',
            'addRole' => '添加角色',
        ),
		'Push'=>array(
        	"index"=>'客户信息列表',
            'client_list' =>'整形分期用户统计'
        ),
        'Message'=>array(
        	'index'=>'已发短信列表',
        	'fore_message'=>'发送短信',
        	'message_temp_list'=>'短信模板列表',
        	'message_temp_add'=>'添加模板',
        ),
        'Shop'=>array(
            'index'     =>'商品列表',
            'goodsAdd'  =>'添加商品',
            'Goods_cate'=>'商品分类列表',//基于每期活动分类
            'member'    =>'会员中心',
            'order'     =>'订单列表',
            'validate'  =>'订单验证',
        )
    ),
    // 'SHOW_PAGE_TRACE'=>true,
    /*
     * 以下是RBAC认证配置信息
     */
    'USER_AUTH_ON' => true,
    'USER_AUTH_TYPE' => 2, // 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY' => 'authId', // 用户认证SESSION标记
    //'ADMIN_AUTH_KEY' => 'appli113@163.com',
    'USER_AUTH_MODEL' => 'Admin', // 默认验证数据表模型
    'AUTH_PWD_ENCODER' => 'md5', // 用户认证密码加密方式encrypt
    'USER_AUTH_GATEWAY' => '/Admins.php/Public/index', // 默认认证网关
    'NOT_AUTH_MODULE' => 'Public', // 默认无需认证模块
    'REQUIRE_AUTH_MODULE' => '', // 默认需要认证模块
    'NOT_AUTH_ACTION' => '', // 默认无需认证操作
    'REQUIRE_AUTH_ACTION' => '', // 默认需要认证操作
    'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
    'GUEST_AUTH_ID' => 0, // 游客的用户ID
    'RBAC_ROLE_TABLE' => $DB_PREFIX . 'role',
    'RBAC_USER_TABLE' => $DB_PREFIX . 'role_user',
    'RBAC_ACCESS_TABLE' => $DB_PREFIX . 'access',
    'RBAC_NODE_TABLE' => $DB_PREFIX . 'node',
    /*
     * 系统备份数据库时每个sql分卷大小，单位字节
     */
    'sqlFileSize' => 5242880, //该值不可太大，否则会导致内存溢出备份、恢复失败，合理大小在512K~10M间，建议5M一卷
        //10M=1024*1024*10=10485760
        //5M=5*1024*1024=5242880
);
return array_merge($config_arr1, $config_arr2);
?>