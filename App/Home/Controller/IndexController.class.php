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
            $textTpl = msgImg();
            if($msgType == 'text'){

                if($keyword == 'Hello2BizUser'){

                    $contentStr = '感谢关注留学独立说';
                }else{

                  //  $contentStr = 'https://www.baidu.com/img/bd_logo1.png';
                    $contentStr = 'lJ2BGsJQ5v1A4fXEmoOwFg2aO4pwxZjDkn6sdadYC8Q';
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
    public function sendMessage(){
        $openid = I('get.openid');
        $openid = $openid ;//? $openid : 'o0W5ms1hZCcATLP8hv5lV3QHogO0';
       // open($openid);die;
        $token = access_token();
        for($i=0;$i<4;$i++)
        {
            $contentStr="这是发送<a href='https://www.baidu.com'/>内容</a>".$i;
            $contentStr=urlencode($contentStr);
            $a=array("content"=>"{$contentStr}");
            $b=array("touser"=>"{$openid}","msgtype"=>"text","text"=>$a);
            $post=json_encode($b);
            $post=urldecode($post);
            $posturl="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$token;
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$posturl);//url
            curl_setopt($ch,CURLOPT_POST,1);//POST
            curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
            curl_exec($ch);
            curl_close($ch);
            open(json_encode($_POST));
        }
    }
}