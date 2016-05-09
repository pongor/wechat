<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        open(json_encode($_REQUEST).'-----'.json_encode($GLOBALS["HTTP_RAW_POST_DATA"]));
        if(checkSignature()){
           // echo $_GET['echostr'];
            echo 'success';
            $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
            reply($xml);

        }else{
            exit();
        }
//        $data = json_encode($_REQUEST);
//        echo  $_GET["echostr"];
//        $data .= json_encode($GLOBALS["HTTP_RAW_POST_DATA"]);
//        open($data);
       // echo 'success';
   }
}