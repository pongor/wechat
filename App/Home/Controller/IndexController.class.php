<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        $data = json_encode($_REQUEST);
        echo  $_GET["echostr"];
        $data .= json_encode($GLOBALS["HTTP_RAW_POST_DATA"]);
        open($data);
       // echo 'success';
   }
}