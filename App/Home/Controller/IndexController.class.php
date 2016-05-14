<?php
namespace Home\Controller;
use Think\Controller;
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
class IndexController extends Controller {

    public function index(){

        if(checkSignature()){
            echo $_GET['echostr'];
            $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
            $postObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;

            $toUsername = $postObj->ToUserName;

            $keyword = trim($postObj->Content);
            $msgType = $postObj->MsgType;
            $time = time();

            $textTpl = msgText();
            if($msgType == 'text'){

                if($keyword == 'Hello2BizUser'){

                    $contentStr = '感谢关注留学独立说';
                }else{
                    $model = D('activity');
                    $time = time();
                    $where = "instr(back_keyword,'{$keyword}')>0 and start_time < {$time} and end_time > {$time}";
                    $res = $model->getFind($where);

                    if($res){
                        $contentStr ="{$res['title']}".$keyword;
                    }else{
                        $contentStr ="没有活动！！！".$keyword;
                        die(' ');
                    }
                }
            }elseif ($msgType == 'image'){
                $picUrl = $postObj->PicUrl;
                $MediaId = $postObj->MediaId;
                $contentStr = '图片';
                die(' ');

            }elseif ($msgType == 'event'){
                switch ($postObj->Event){
                    case 'subscribe':   //扫描待参数的二维码
                           $contentStr = $postObj->EventKey .'扫描';

                        break;
                    case 'SCAN':   //用户已关注 扫描事件
                        $contentStr = $postObj->EventKey .'扫描';

                        break;
                    default:
                        $contentStr = '';
                        break;
                }

            }

            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
            echo $resultStr;
            //_curl($fromUsername);
            die;
        }else{
            exit();
        }
   }
    /*
     D:/web/wechat/img/2531170_213554844000_2.jpgarray(3) { ["type"]=> string(5) "image" ["media_id"]=> string(64) "ZXXVLzkpUxp5hPpcMHYchh_qw83F60oTtJAWPo2b1B2TNpXV9e2BuNUum0rbi2f4" ["created_at"]=> int(1462937403) } string(64) "ZXXVLzkpUxp5hPpcMHYchh_qw83F60oTtJAWPo2b1B2TNpXV9e2BuNUum0rbi2f4"
     */
    public function sendMessage(){
        $a = time();
        $openid = I('get.openid');

        $openid = $openid ;//? $openid : 'o0W5ms1hZCcATLP8hv5lV3QHogO0';
        $user = getUser($openid); // 获取用户信息

        $model = D('member');
        $result = $model->getUser(array('openid'=>$user['openid']));
        $data = [
            'nickname'      =>  $user['nickname'],
            'headimgurl'    =>  $user['headimgurl'],
            'openid'        =>  $user['openid'],
            'sex'           =>  $user['sex'],
            'province'      =>  $user['province'],
            'city'          =>   $user['city'],
            'country'       =>  $user['country'],
            'subscribe_time' => $user['subscribe_time'],
            'privilege'     =>  $user['privilege'],
            'remark'        =>   $user['remark'],
        ];

        if(1==1){ //如果用户存在
//            $model->getUpdate('id='.$result['id'],$data);
//            $user_id = $result['id'];
            //拿到分享图片


            // 是否素材是否过期

            //上传微信素材 有效期三天



    ///    }else{

            $data['at_time']  = time();
          //  $user_id = $model->insert($data);  //是新用户.
            //拿到二维码
            $array = array(
                'action_info' => array(
                    'scene' => array(
                        'scene_str' => $data['id']
                    ),
                ),
            );
            $codeUrl = getCode($array);
            $file_code = saveCode($codeUrl, $data['id']); // 二维码图片路径

            //下载用户头像
            $headimg = dowload($data['headimgurl'].'.jpg');

            //生成分享图片
           $headimg = get_lt_rounder_corner($headimg, $data['openid']); //圆角头像

           $fiel =  imgTo('./img/807893500556499641.png',$headimg,$file_code,$data['nickname']);
           $fiel =  ltrim($fiel,'.');
            //上传微信素材服务器  获取素材media_id
            $file_data = array(
                'filename'=>__APP__.$fiel,'.',  //国片相对于网站根目录的路径
                'content-type'=>'image/png',  //文件类型
                'filelength'=>'11011'         //图文大小
            );
           $media_id = add_material($file_data);
        }

        //获取活动要推送给用户的信息.

            $array = array(
                    'touser'    =>  $openid,
                    'msgtype'   =>  'image',
                    'image'     =>  array(
                        "media_id"  => $media_id
                    ),

            );
//            $contentStr="这是发送<a href='https://www.baidu.com'/>内容</a>";
//            $contentStr=urlencode($contentStr);
//            $a=array("content"=>"{$contentStr}");
//            $b=array("touser"=>"{$openid}","msgtype"=>"text","text"=>$a);
        sendMessage($array);
        echo $a-time();

    }

}
