<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/25
 * Time: 10:52
 */

namespace Home\Controller;


use Think\Controller;

class MenuController extends Controller
{
    public $array = array();
    //创建菜单
    public function create(){
      //  dump(autoMessage(9));
        //ScBINfXZiha6z2o4pk58hTPsbXs_WpCfmEAor4joHNY

        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".access_token();
        //$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".access_token(); //s删除
        //var_dump(file_get_contents($url));die;
        $post =  urldecode(json_encode(($this->menu())));
        var_dump(httpPost($url,$post));
    }
    public function menu(){
       $this->array =  array(
            "button" => [
                [

                    'name' => urlencode('文商科'),
                    'sub_button'  =>  [
                        [
                            'name'  =>  urlencode('金融1'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/235.html'),
                        ],
                        [
                            'name'  =>  urlencode('金融2'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/250.html'),
                        ],
                        [
                            'name'  =>  urlencode('人力资源'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/179.html'),
                        ],
                        [
                            'name'  =>  urlencode('传媒／新闻'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/237.html'),
                        ],
                        [
                            'name'  =>  urlencode('其他'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/index/cat_id/4.html'),
                        ],
                    ],
                ],
                [
                    'name' => urlencode('理工科'),
                    'sub_button'  =>  [
                        [
                            'name'  =>  urlencode('计算机科学'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/165.html'),
                        ],
                        [
                            'name'  =>  urlencode('统计学／数学'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/153.html'),
                        ],
                        [
                            'name'  => urlencode('通信工程'),
                            'type' => "view",
                            'url'   => urlencode('http://pm.dulishuo.com/Product/details/id/244.html'),
                        ],
                        [
                            'name'  =>  urlencode('高中生'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/241.html'),
                        ],
                        [
                            'name'  =>  urlencode('其他'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/index/cat_id/3.html'),
                        ],
                    ],
                ],
                [
                    "type"=>"click",
                    'name'=> urlencode('来学英语'),
                    'key' => 'weilaiyingyu'
                ],
            ],
        );
        return $this->array;
    }
}