<?php
namespace Home\Controller;
use Think\Controller;
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
class IndexController extends Controller {

    public function index(){
        //OPENTM207685059
      // $a ='<xml><ToUserName><![CDATA[gh_cbfe978fe9e3]]></ToUserName> <FromUserName><![CDATA[o0W5ms_CO3BqzzXbN0NuvMR41Wx8]]></FromUserName> <CreateTime>1463395333</CreateTime> <MsgType><![CDATA[event]]></MsgType> <Event><![CDATA[SCAN]]></Event> <EventKey><![CDATA[6]]></EventKey> <Ticket><![CDATA[gQFI8ToAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL25VaVdUYVBsOXVHUHNoOE0wMllWAAIE_Jw5VwMEgPQDAA==]]></Ticket> </xml>';
//        $json = '{
//    "ToUserName":"gh_cbfe978fe9e3",
//    "FromUserName":"o0W5ms1hZCcATLP8hv5lV3QHogO0",
//    "CreateTime":"1463645435",
//    "MsgType":"text",
//    "Content":"哈哈",
//    "MsgId":"6286309276681876036"
//}';
//        $json = '{
//    "ToUserName":"gh_cbfe978fe9e3",
//    "FromUserName":"o0W5ms_CO3BqzzXbN0NuvMR41Wx8",
//    "CreateTime":"1463646741",
//    "MsgType":"event",
//    "Event":"SCAN",
//    "EventKey":"11",
//    "Ticket":"gQFN8ToAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xLzdFaWxiMXZseWVHd0dXNGk0R1lWAAIEuLk6VwMEgPQDAA=="
//}'; //扫码
        if(checkSignature()) {
            echo $_GET['echostr'];
            $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
            $postObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
             //$postObj = json_decode($json);
            //  open(json_encode($postObj));
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $msgType = $postObj->MsgType;
            $time = time();
            $id = 0;
            $textTpl = msgText();

            $model = D('activity');

            switch ($msgType) {
                case 'text':  //发送了文字内容
                    if ($keyword == 'Hello2BizUser') {
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', "感谢关注留学独立说");
                        echo $resultStr;
                    } else {
                        $where = "back_keyword = '{$keyword}' and start_time < {$time} and end_time > {$time}";
                        $res = $model->getFind($where);

                        if ($res) {
                            if ($res['is_start'] != 1) {

                                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', "这个活动已经结束报名啦，下次早点来哦！"); //推送活动信息
                                echo $resultStr;
                                die;
                            }
                            $contentStr = "{$res['title']}";
                            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr); //推送活动信息
                            echo $resultStr;
                            _curl($fromUsername, $res['id']); //发送活动其他信息
                            die;
                        } else {
//
                            die('success');
                        }
                    }
                    break;
                case 'event':  //有事件
                    switch ($postObj->Event) {
                        case 'subscribe':  //未关注用户扫码时间
                            $arr = explode('qrscene_', $postObj->EventKey);
                            $id = $arr[1];
                            break;
                        case 'SCAN': //已关注用户扫码事件
                            $id = $postObj->EventKey;
                            break;
                    }
                    break;
                default:
                    die('success');
                    break;
            }
            if ($id > 0) {  //扫码用户
                $shar = D('share');
                $supp = $shar->getInfo('id=' . $id); //活动支持信息
                if (!$supp) {
                    die('success');
                }
                $aid = $supp['a_id'];
                $user_id = $supp['user_id'];
                //获取扫码用户的信息
                $info = D('member')->getInfo("openid='{$fromUsername}'");
                $a_user_id = isset($info['id']) ? $info['id'] : 0;
                if ($user_id == $a_user_id) {
                    die('success');
                }
                $share_array = $shar->getInfo("user_id = {$a_user_id} and a_id = {$aid}"); //活动信息
               // var_dump($share_array);
                if (!$share_array) {  //用户未参加活动
                    _curl($fromUsername, $aid); //发送活动其他信息
                }
                self::support($id, $fromUsername);
            }
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
        $result = $model->getUser(array('openid'=>$openid));
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
        $share_info = $share->getInfo('user_id='.$user_id.' and a_id='.$id);

        $activity = D('activity');
        $a_info = $activity->getFind('id='.$id); //活动信息

        //拿到分享图片
        if($share_info && ($share_info['up_time'] + 3*24*60*60) > time() && $share_info['share']){  //素材未过期
            $media_id = $share_info['media_id'];
        }else if(($share_info['up_time'] + 3*24*60*60) <= time() && $share_info['share']){  //素材过期  重新上传
            $media_id = add_material(array('filename'=>__APP__.ltrim($share_info['share'],'.'), 'content-type'=>'image/png','filelength'=>'11011')); //上传素材
            $share->getUpdate('id='.$share_info['id'],array('media_id'=>$media_id,'up_time'=>time())); //更新用户活动数据
        }else{  //不存在信息
            //保存用户分享信息
            if(!$share_info){
                $share_data = array(
                    'user_id' => $user_id,
                    'a_id'      =>  $id,
                    'share'     =>  '',
                    'up_time'   =>  time(),
                    'at_time'   =>  time(),
                    'media_id'  =>  '',
                    'number'    =>  0
                );
                $share_id =  $share->Insert($share_data);
            }else{
                $share_id = $share_info['id'];
            }

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
            $media_id = add_material($file_data);

            //保存分享的图片 与微信上传的素材
            $share_save = array(
                'share' => $fiel,
                'media_id' => $media_id
            );
            $share->getUpdate(array('id'=>$share_id),$share_save);
        }
        if($a_info['text_content']){
            $array = explode('||',$a_info['text_content']);
            //发送用户参加活动的信息
            for($i=0;$i<count($array);$i++){

                $msgArray = '{
                    "touser":"'.$openid.'",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"'.$array[$i].'"
                    }
                }';
                if(isset($array[$i]) && $array[$i]){
                    sendMessage($msgArray);
                }
            }
        }
        //推送给用户的信息.
        $array = '{
                    "touser":"'.$openid.'",
                    "msgtype":"image",
                    "image":
                    {
                         "media_id":"'.$media_id.'"
                    }
                }';
        sendMessage($array);
    }
    //用户支持用户扫码事件
    public static function support($id,$openid){
       // open(json_encode(array($id,$openid)));

        if($id <= 0) {
            return false;
        }
        $share = D('share');
        $share_info = $share->getInfo('id='.$id);  //用户分享详情

        if(!$share_info) {
            return false;
        }
        $aid = $share_info['a_id'];  //活动ud
        $a_user_id = $share_info['user_id']; //被支持的用户
        $model = D('member');
        $user_info = $model->getInfo("openid = '$openid'"); //支持用户的详情
        $a_user_info =$model->getInfo(array('id'=>$a_user_id)); //被支持着

        if(!$user_info) { //如果用户信息不存在
            $user = getUser($openid);
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
                'at_time'       =>  time()
            ];
            $user_id = $model->Insert($data); //保存新用户信息
        }else{
            $user = getUser($openid);
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
            $model->getUpdate(array('id'=>$user_info['id']),$data);
            $user_id = $user_info['id'];
        }

        $activity = D('activity');
        $a_info = $activity->getInfo(array('id'=>$aid));
        if(!$a_info){
            return false;
        }
        $support = D('support');  //支持model
        $res = $support->getInfo(array('user_id'=>$a_user_id,'a_id'=>$aid,'s_user_id'=>$user_id));
        if($res){ //用户已经支持过了  回复用户错误信息
            $msgArray = '{
                    "touser":"'.$openid.'",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"'.$a_info['re_invite_content'].'"
                    }
                }';
            tempMessage($a_user_info['openid'],$a_info['invite_url'],$a_info['invite_content'],$user_info['nickname']);
        //   sendMessage($msgArray);
          //  return ;
        }else{  //用户为支持过
            if($a_user_id == $user_id){ //自己支持自己
//                $msgArray = '{
//                    "touser":"'.$openid.'",
//                    "msgtype":"text",
//                    "text":
//                    {
//                         "content":"'.$a_info['re_invite_content'].'"
//                    }
//                }';
//                sendMessage($msgArray); //达到条件
                return;
            }
            $s_data = array(
                'user_id' => $a_user_id,
                'a_id'      =>  $aid,
                's_user_id' => $user_id,
                'at_time'   =>  time()
            );
            $support->Insert($s_data); //保存支持信息
            $share->where(array('user_id'=>$a_user_id,'a_id'=>$aid))->setInc('number'); //活动信息支持人数加1
           $number = $share_info['number']+1;  //人数

            if($a_info['success_condition'] == 1){ //代表a条件
                if($number == $a_info['continue_num']){ //达到继续邀请人数的条件
                    //continue_content
                    $msgArray = '{
                    "touser":"'.$a_user_info['openid'].'",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"'.$a_info['continue_content'].'"
                    }
                }';
                    sendMessage($msgArray); //达到条件
                }else  if($number >= $a_info['invite_num']){ //达到A条件

                    //succ_content  达到条件成功通知的内容
                    $msgArray = '{
                    "touser":"'.$a_user_info['openid'].'",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"'.$a_info['succ_content'].'"
                    }
                }';
                    sendMessage($msgArray); //达到条件
                    $msgArray = '{
                    "touser":"'.$a_user_info['openid'].'",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"'.$a_info['egg_content'].'"
                    }
                }';
                    sendMessage($msgArray); //出发彩蛋消息

                }

                if($number == $a_info['egg_num']){ //彩蛋条件
                    $msgArray = '{
                    "touser":"'.$a_user_info['openid'].'",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"'.$a_info['finish_egg_content'].'"
                    }
                }';
                    sendMessage($msgArray); //完成彩蛋通知内容
                }



            }else{ //B条件
                $rank = D('share')->where(array('a_id'=>$aid,'number'=>array('GT'=>$number)))->count();
                $rank++; //用户排名

                if($rank <= $a_info['rank_list']){
                    $msgArray = '{
                    "touser":"'.$a_user_info['openid'].'",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"'.$a_info['rank_notice'].'"
                    }
                }';
                    sendMessage($msgArray); //完成彩蛋通知内容
                }else{
                    $msgArray = '{
                    "touser":"'.$a_user_info['openid'].'",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"'.$a_info['un_rank_notice'].'"
                    }
                }';
                    sendMessage($msgArray); //完成彩蛋通知内容
                }

            }
        }

    }
}
