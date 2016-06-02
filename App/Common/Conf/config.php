<?php
return array(
	//'配置项'=>'配置值'
    'LOAD_EXT_CONFIG' => 'db',

    'DEFAULT_MODULE'  =>  'Home',
    //'BIND_MODULE'       => 'Home',
    'URL_MODEL'         => 2,
  //  'MULTI_MODULE'      =>  true,
    'is_debug'          =>  false, // 调试模式
    'MODULE_ALLOW_LIST' => array('Home','Backend'),
     /*****************************公共配置***************************************/
    'APPID'     =>  'wxcb2fc2626c66a114',// */'wx62f712f042e87831', //
    'APPSECRET' => '514dd06182829b67ff0d84841b0dffab',//*/ '6169e3da0e21fecce48a833ebac8af76',
    'TOKEN'     => 'qwea',  //消息token
    'TOKEN_URL' => 'https://api.weixin.qq.com/cgi-bin/token', //获取用户登录授权的token 地址
    'USER_INFO'     => 'https://api.weixin.qq.com/cgi-bin/user/info',//获取用户基本信息
    'CODE_ticket'          =>  'https://api.weixin.qq.com/cgi-bin/qrcode/create', //二维码ticket
    'CODE_IMG'      =>  'https://mp.weixin.qq.com/cgi-bin/showqrcode', //获取二维码图片
    'template'      =>  'https://api.weixin.qq.com/cgi-bin/message/template/send', //发送模板消息
    'template_id'   =>  'https://api.weixin.qq.com/cgi-bin/template/api_add_template', //获取模板id
    'IMG_TEXT_COLOR' => '#ffffff', //文字颜色
    'IMG_HEADER_SIZE'   =>  198, //头像大小
    'IMG_height'    =>  180, //头像距离图片正上方的距离
    'IMG_NAME_HEIGHT'=> 5, //用户名称与头像的距离
    'IMG_LEFT'          =>  142, //二维码图片距离左边
    'IMG_NEXT'          =>  421, //二维码图片距离下边的距离
    'IMG_CODE'          =>  187, //二维码图片大小
    'template_send_id' => /*'5gVSzuTCcJ2wLF1zDOWW99CcT0kpWrOpxfSjbqPzZew',//  */ 'lJ2BGsJQ5v1A4fXEmoOwFg2aO4pwxZjDkn6sdadYC8Q', //发送消息模板id
);