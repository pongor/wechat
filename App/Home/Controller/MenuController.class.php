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

        $file_data = array(
            'filename'=>__APP__.'/img/9.jpg',  //国片相对于网站根目录的路径
            'content-type'=>'image/jpg',  //文件类型
            'filelength'=>'11011'         //图文大小
        );
       $a = I('get.token');
        if($a == 1){
            S('access_token',null);
        }
        $url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=";

        add_material($file_data,$url,1);
        die;
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".access_token();
        httpPost($url);
        echo urldecode(json_encode(($this->menu())));
    }
    public function menu(){
       $this->array =  array(
            "button" => [
                [

                    'name' => urlencode('理工科'),
                    'sub_button'  =>  [
                        [
                            'name'  =>  urlencode('统计学'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/227.html'),
                        ],
                        [
                            'name'  =>  urlencode('计算机科学'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/161.html'),
                        ],
                        [
                            'name'  =>  urlencode('电子/电机工程'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/164.html'),
                        ],
                        [
                            'name'  =>  urlencode('材料工程'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/226.html'),
                        ],
                        [
                            'name'  =>  urlencode('其他'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/index/cat_id/3.html'),
                        ],
                    ],
                ],
                [
                    'name' => urlencode('文商科'),
                    'sub_button'  =>  [
                        [
                            'name'  =>  urlencode('统计学'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/227.html'),
                        ],
                        [
                            'name'  =>  urlencode('计算机科学'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/161.html'),
                        ],
                        [
                            'name'  => urlencode('电子/电机工程'),
                            'type' => "view",
                            'url'   => urlencode('http://pm.dulishuo.com/Product/details/id/164.html'),
                        ],
                        [
                            'name'  =>  urlencode('材料工程'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/details/id/226.html'),
                        ],
                        [
                            'name'  =>  urlencode('其他'),
                            'type' => "view",
                            'url'   =>  urlencode('http://pm.dulishuo.com/Product/index/cat_id/3.html'),
                        ],
                    ],
                ],
            ],
        );
        return $this->array;
    }
}