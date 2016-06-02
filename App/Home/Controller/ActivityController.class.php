<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/31
 * Time: 10:13
 */

namespace Home\Controller;


use Think\Controller;
//微信活动操作，活动信息检索
class ActivityController extends Controller
{
    public $time;// = time();
    public $fromUsername;
    public $toUsername;
    public $keyword;
    public $str;
    public $activity;
    public $EventKey;
    public $Event;

    public function Upload($data,$user_id,$aid){  //上传临时素材活动推广图片保存微信返回的id 并返回
        //生成二维码图片ca
        $array = array(
            'action_info' => array(
                'scene' => array(
                    'scene_id' => $share_id
                ),
            ),
        );
        $codeUrl = getCode($array);

        $file_code = codeImg($codeUrl,$user_id);//saveCode($codeUrl, 100); // 二维码图片路径

        //下载用户头像
        $headimg = downloadFile($data['headimgurl'].'.jpg');

        //生成分享图片
        $headimg = get_lt_rounder_corner($headimg, $data['openid']); //圆角头像

        $fiel =  imgTo($a_info['back_pic'],$headimg,$file_code,$data['nickname']);

        $fiel =  ltrim($fiel,'.');
        //上传微信素材服务器  获取素材media_id
        $file_data = array(
            'filename'=>__APP__.$fiel,  //国片相对于网站根目录的路径
            'content-type'=>'image/png',  //文件类型
            'filelength'=>'11011'         //图文大小
        );
    
    }


}