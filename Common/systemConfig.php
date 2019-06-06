<?php	return array ( 'SITE_INFO' => array ( 'name' => '西婵_成都整形美容_四川西婵整形美容医院官网', 'upload' => '', 'version' => '3.0', 'icp' => '蜀ICP备12000892号<br>咨询：400-028-6666   固话：028-85432111　地址：成都市武侯区新南路新83号', 'service' => 'xichan@xichan.cn', 'tel' => '4000286666', 'qq' => '4000286666', 'fax' => '02885313071', 'address' => '四川省成都市武侯区新南路新83号', 'postcode' => '610041', 'keyword' => '西婵_成都整形美容_四川西婵整形美容医院官网', 'description' => '西婵,西婵整形,成都整形,成都整形领衔医院,四川成都西婵整形美容医院被评为中国十大整形美容医院,医院设有成都吸脂、隆鼻、瘦脸、双眼皮整形、口腔美容整形、疤痕美容整形、激光皮肤美容等整形美容项目,提供详细的成都西婵整形资料,成都西婵整形美容医院电话：400-028-6666', 'copyright' => 'Copyright © 2015 - 2016 xichan.cn', 'tongji' => '<div style="display:none"><script src=\'http://s108.cnzz.com/stat.php?id=492772&web_id=492772\' language=\'JavaScript\' charset=\'gb2312\'></script></div>
<!-- meiqia -->
<script>
	function readMessage(msg) {
		// // 获取头像
		// var agent = msg[0].agent.avatar;
		// // 获取ID信息
		// var nickname = msg[0].agent.nickname;
		// console.log(agent);
		// console.log(nickname);
		var text = \'\',
			num = 0;
		if (msg === \'hasBeenRead\') { // 消息已被阅读
			num = 0;
		} else if (typeof (msg) === \'object\') {
			var unreadNum = document.getElementById(\'unreadNum\').innerHTML,
				lastMsg = msg[msg.length - 1];
			num = isNaN(+unreadNum) ? msg.length : +unreadNum + msg.length;
			if (lastMsg.content_type === \'text\') {
				text = lastMsg.content.replace(
					/<img [^>]*src=[\'"]([^\'"]+)[^>]*>/gi, \'[表情]\'
				);
			} else if (lastMsg.content_type === \'photo\') {
				text = \'[图片]\';
			} else if (lastMsg.content_type === \'file\') {
				text = \'[文件]\';
			} else {
				text = \'[新消息]\';
			}
		}
		// 未读消息数量
		document.getElementById(\'unreadNum\').innerHTML = num;
		// 最后一条消息的内容
		document.getElementById(\'unreadMsg\').innerHTML = text;
		// 设置头像
		// document.getElementById(\'bubble-agent-avatar\').setAttribute("src", agent);
		// document.getElementById(\'bubble-agent-avatar\').setAttribute("alt", "西婵");
		// // 设置ID
		// document.getElementById(\'bubble-agent-name\').innerHTML = nickname;
		// 设置消息隐藏显示
		var showdiv = document.getElementsByClassName(\'bubble-before-after\');
		var numRead = document.getElementById(\'unreadNum\');
		if(numRead.innerText > 0){
			showdiv[0].style.display = "block";
		}else{
			showdiv[0].style.display = "none";
		}
	}
</script>	
<script type=\'text/javascript\'>
    (function(m, ei, q, i, a, j, s) {
        m[i] = m[i] || function() {
            (m[i].a = m[i].a || []).push(arguments)
        };
        j = ei.createElement(q),
            s = ei.getElementsByTagName(q)[0];
        j.async = true;
        j.charset = \'UTF-8\';
        j.src = \'https://static.meiqia.com/dist/meiqia.js?_=t\';
        s.parentNode.insertBefore(j, s);
    })(window, document, \'script\', \'_MEIQIA\');
	_MEIQIA(\'entId\', \'9041\');
	// 获取未读消息
	_MEIQIA(\'getUnreadMsg\', readMessage);
	_MEIQIA(\'init\');
</script>', ), 'WEB_ROOT' => 'http://m.xichan.cn/', 'AUTH_CODE' => 'ViYzSV', 'ADMIN_AUTH_KEY' => 'admin@root.com', 'DB_HOST' => '127.0.0.1', 'DB_NAME' => 'mobileweb1', 'DB_USER' => 'xichanms', 'DB_PWD' => 'SheSi#59', 'DB_PORT' => '3306', 'DB_PREFIX' => 'zy_', 'webPath' => '/', 'TOKEN' => array ( 'admin_marked' => 'zhouyun', 'admin_timeout' => '3600', 'member_marked' => 'http://www.xichan.cn', 'member_timeout' => '3600', ), 'SYSTEM_EMAIL' => array ( 'smtp_host' => 'smtp.qq.com', 'smtp_port' => '25', 'from_email' => 'xichan@xichan.cn', 'from_name' => '西婵', 'smtp_user' => 'xichan@xichan.cn', 'smtp_pass' => '', 'reply_email' => '', 'reply_name' => '', 'test_email' => 'zcj@xichan.cn', ), );?>