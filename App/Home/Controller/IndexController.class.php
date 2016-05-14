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
            _curl($fromUsername,$res['id']);
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
        $id = intval(I('get.id')) ? intval(I('get.id')) :1;
        $openid = $openid ? $openid : 'o0W5ms1hZCcATLP8hv5lV3QHogO0';
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

        if($result){ //如果用户存在
            $model->getUpdate('id='.$result['id'],$data);
            $user_id = $result['id'];

        }else {
            $data['at_time'] = time();
            $user_id = $model->insert($data);  //是新用户.
        }
        $share = D('share');
        $share_info = $share->getInfo('user_id='.$result['id'].' and a_id='.$id);

        $activity = D('activity');
        $a_info = $activity->getFind('id='.$id); //活动信息

        //拿到分享图片
        if(($share_info['up_time'] + 3*24*60*60) < time() && $share_info['share']){  //素材过期  重新上传
            $media_id = add_material(array('filename'=>__APP__.ltrim($share_info['share'],'.'), 'content-type'=>'image/png','filelength'=>'11011')); //上传素材
            $share->getUpdate('id='.$share_info['id'],array('media_id'=>$share_info['media_id'],'up_time'=>time())); //更新用户活动数据
        }else{  //不存在信息
            //生成二维码图片ca
            $array = array(
                'action_info' => array(
                    'scene' => array(
                        'scene_id' => $id
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
            $media_id = add_material($file_data);
            //保存用户分享信息
            $share_data = array(
                'user_id' => $user_id,
                'a_id'      =>  $id,
                'share'     =>  $fiel,
                'up_time'   =>  time(),
                'at_time'   =>  time(),
                'media_id'  =>  $media_id,
                'number'    =>  0
            );
            $share->Insert($share_data);
        }

        if($a_info['text_content']){
            $array = explode('||',$a_info['text_content']);
            //发送用户参加活动的信息
            for($i=0;$i<count($array);$i++){
                $msgArray = array(
                    'touser' => $openid,
                    'msgtype'=> 'text',
                    'text'   => array('content'=>$array[$i]),
                );
                if(isset($array[$i]) && $array[$i]){
                    sendMessage($msgArray);
                }
            }
        }
        //推送给用户的信息.
            $array = array(
                    'touser'    =>  $openid,
                    'msgtype'   =>  'image',
                    'image'     =>  array(
                        "media_id"  => $media_id
                    ),

            );
        sendMessage($array);
    }
    //用户支持用户扫码事件
    public static function support($id,$openid){
        if($id <= 0) {
            return false;
        }
        $share = D('share');
        $share_info = $share->getInfo('id='.$id);
        if(!$share_info) {
            return false;
        }
        $aid = $share_info['a_id'];

    }
}
