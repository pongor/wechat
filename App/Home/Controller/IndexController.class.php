<?php
namespace Home\Controller;
use Think\Controller;
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
            $user = getUser($fromUsername); // 获取用户信息

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
            if($result){
                $model->getUpdate('id='.$result['id'],$data);
                $user_id = $result['id'];
            }else{
                $data['at_time']  = time();
                $user_id = $model->insert($data);
            }
            $textTpl = msgText();
            if($msgType == 'text'){

                if($keyword == 'Hello2BizUser'){

                    $contentStr = '感谢关注留学独立说';
                }else{

                  //  $contentStr = 'https://www.baidu.com/img/bd_logo1.png';
//                    $file_data = array(
//                        'filename'=>__APP__.'/images/1.png',  //国片相对于网站根目录的路径
//                        'content-type'=>'image/png',  //文件类型
//                        'filelength'=>'11011'         //图文大小
//                    );
//                   $a = add_material($file_data);
//                    open(json_encode($a));
                    $contentStr ="欢迎回来！！！";
                }

            }elseif ($msgType == 'image'){
                $picUrl = $postObj->PicUrl;
                $MediaId = $postObj->MediaId;
                $contentStr = '图片';

            }elseif ($msgType == 'event'){
                switch ($postObj->Event){
                    case 'subscribe':

                        break;
                    default:

                        break;
                }

            }
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'image', $contentStr);
            echo $resultStr;
            _curl($fromUsername);
            die;
        }else{
            exit();
        }
   }
    /*
     D:/web/wechat/img/2531170_213554844000_2.jpgarray(3) { ["type"]=> string(5) "image" ["media_id"]=> string(64) "ZXXVLzkpUxp5hPpcMHYchh_qw83F60oTtJAWPo2b1B2TNpXV9e2BuNUum0rbi2f4" ["created_at"]=> int(1462937403) } string(64) "ZXXVLzkpUxp5hPpcMHYchh_qw83F60oTtJAWPo2b1B2TNpXV9e2BuNUum0rbi2f4"
     */
    public function sendMessage(){
        $openid = I('get.openid');
        $openid = $openid ;//? $openid : 'o0W5ms1hZCcATLP8hv5lV3QHogO0';
       // open($openid);die;
            $token = access_token();
            $array = array(
                    'touser'    =>  $openid,
                    'msgtype'   =>  'image',
                    'image'     =>  array(
                        "media_id"  =>  'ZXXVLzkpUxp5hPpcMHYchh_qw83F60oTtJAWPo2b1B2TNpXV9e2BuNUum0rbi2f4'
                    ),

            );
//            $contentStr="这是发送<a href='https://www.baidu.com'/>内容</a>";
//            $contentStr=urlencode($contentStr);
//            $a=array("content"=>"{$contentStr}");
//            $b=array("touser"=>"{$openid}","msgtype"=>"text","text"=>$a);
        sendMessage($array);
    }
}