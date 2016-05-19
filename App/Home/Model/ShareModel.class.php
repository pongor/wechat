<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/14
 * Time: 15:50
 */

namespace Home\Model;


use Think\Model;

class ShareModel extends Model
{
    protected $tableName  = 'member_activity';
    /*
     * 获取用户参加活动信息
     */
    public function getInfo($where){
        return $this->where($where)->find();
    }
    /*
     * 保存用户参加活动的信息
     */
    public function Insert($data){
        return $this->add($data);
    }
    /*
     * 更新用户活动信息
     */
    public function getUpdate($where,$data){
        return $this->where($where)->save($data);
    }
}
/*
die;






            if($msgType == 'text'){

                if($keyword == 'Hello2BizUser'){

                    $contentStr = '感谢关注留学独立说';

                }else{


                    $where = "instr(back_keyword,'{$keyword}')>0 and start_time < {$time} and end_time > {$time}";
                    $res = $model->getFind($where);

                    if($res){
                        $contentStr ="{$res['title']}";
                    }else{
                        $contentStr ="没有活动！！！".$keyword;
                        die('success');
                    }
                }
            }elseif ($msgType == 'image'){
                $picUrl = $postObj->PicUrl;
                $MediaId = $postObj->MediaId;
                $contentStr = '图片';
                die('success');

            }elseif ($msgType == 'event'){
                switch ($postObj->Event){
                    case 'subscribe':   //用户没有关注
                          // $contentStr = $postObj->EventKey .'扫描';
                           $arr =  explode('qrscene_',$postObj->EventKey);
                           $id = $arr[1];

                        break;
                    case 'SCAN':   //用户已关注 扫描事件
                     //   $contentStr = $postObj->EventKey .'扫描';
                        $id = $postObj->EventKey;

                        break;
                    default:
                        $contentStr = '';
                        echo 'success';die;
                        break;
                }

            }


            if( isset($res['is_start']) &&  $res['is_start'] != 1  ){
                $contentStr = '这个活动已经结束报名啦，下次早点来哦！'.$res['is_start'].$res['id'];
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
                echo $resultStr;die;
            }else{

                if($contentStr){
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
                    echo $resultStr;//die;
                }else{ //扫码用户
                    if($id > 0 ){
                        $shar = D('share');
                        $supp =  $shar->getInfo('id='.$id);
                        if($supp){
                            $aid = $supp['a_id']; //活动id
                            $user_id = $supp['user_id']; //参加活动的用户

                            //获取扫码用户的信息
                            $info = D('member')->getInfo("openid='{$fromUsername}'");
                            if($info){
                                $a_user_id = $info['id'];
                                if($a_user_id == $user_id){
                                    die('success');
                                }
                                //判断用户是否参加了当前的活动
                                $result = $shar->getInfo("user_id={$a_user_id} and a_id = {$aid}");
                                if(!$result){ //用户没有参加活动
                                    //获取活动信息
                                    $scan =$model->getFind("id = {$aid} and start_time < {$time} and end_time > {$time} and is_start=1");
                                    if($scan){
                                        $contentStr = $scan['title'];
                                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
                                        echo $resultStr;
                                        _curl($fromUsername,$aid); //扫码用户参加活动
                                    }else{
                                        $contentStr = '这个活动已经结束报名啦，下次早点来哦！'.$res['is_start'].$res['id'];
                                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
                                        echo $resultStr;die;
                                    }
                                }
                            }else{
                                $scan =$model->getFind("id = {$aid} and start_time < {$time} and end_time > {$time} and is_start=1");
                            if($scan){
                                $contentStr = $scan['title'];
                                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
                                echo $resultStr;
                                _curl($fromUsername,$aid); //扫码用户参加活动
                               open(json_encode($info));
                            }else{
                                $contentStr = '这个活动已经结束报名啦，下次早点来哦！'.$res['is_start'].$res['id'];
                                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
                                echo $resultStr;die;
                            }

                            }

                        }else{
                            $contentStr = '这个活动已经结束报名啦，下次早点来哦！'.$res['is_start'].$res['id'];
                            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
                            echo $resultStr;die;
                        }
                    }
                }

            }
            echo "success";
            ob_flush();
            flush();
            if($id >0 ){ //扫码事件

                self::support($id,$fromUsername);

            }else{ //活动事件
                _curl($fromUsername,$res['id']);
            }
            die(' ');
        }else{
            exit( '');
        }
*/