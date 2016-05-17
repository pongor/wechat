<?php
namespace Backend\Controller;
use Think\Controller;

class ActivityController extends RbacController{
	public function index(){
		$title = I('get.title');
        $p = I('get.p');
        if($title){
            $whereArray['title'] = array('like','%'.$title.'%');
            $this->assign('title',$title);
        }else{
            $whereArray = array();
        }
        $p = $p ? $p : 1;
        $count = M('Activity')->where($whereArray)->count();
        $whereArray['start'] = ($p - 1) * 20;
		$list = D('Activity')->getList($whereArray);

		$this->assign('p',$p);
        $this->assign('count',$count);
		$this->assign('list',$list);
		$this->display();
	}

	//详情页
	public function detail(){
		$id = intval(I('get.id'));
		if($id){
			//修改
			$result = D('Activity')->getField(array('id'=>$id));
			$this->assign('result',$result);
		}
		$this->display();
	}

	//保存详情页
	public function saveDetail(){
		$id = intval(I('get.id'));
		$saveList = $_POST;
		$model = D('Activity');
		cookie('activity_id',$id,3600); 
		// echo "<pre>";
		// var_dump($saveList);
		// die;
		// $editor = D('User')->getField(array('id'=>intval($this->user_id)),'username')['username'];
		// $data$data['editor'] = $editor;
        if (!$model->create($saveList)){
		     // 如果创建失败 表示验证没有通过 输出错误提示信息
        	if($id){
        		redirect(U('Activity/detail',array('time'=>time(),'id'=>$id)),0.2,'<script>alert("'.$model->getError().'");</script>');
        	}
        	redirect(U('Activity/detail',array('time'=>time())),0.2,'<script>alert("'.$model->getError().'");</script>');
		}else{
			if($id){
				//修改
				$r = $model->where(array('id'=>$id))->save();
			}else{
				//新建
				$r = $model->add();
			}
		}
		
		if($r) {
			redirect(U('Activity/index',array('time'=>time())),0.2,'<script>alert("保存成功");</script>');
		}else{
			redirect(U('Activity/index',array('time'=>time())),0.2,'<script>alert("保存失败");</script>');
		}
		
	}

	//删除数据
	public function deleteData(){
		$id = intval(I('get.id'));
		$r = D('Activity')->deleteData(array('id'=>$id));
		if($r){
			redirect(U('Activity/Index',array('time'=>time())),0.2,'<script>alert("删除成功");</script>');
		}else{
			redirect(U('Activity/Index',array('time'=>time())),0.2,'<script>alert("删除失败");</script>');
		}
	}

	//是否开启活动
	public function changeStatus(){
		$id = intval(I('get.id'));
		$isStart = intval(I('get.status'));
		$r = D('Activity')->saveData(array('id'=>$id),array('is_start'=>$isStart));
		if($r){
			redirect(U('Activity/Index',array('time'=>time())),0.2,'<script>alert("操作成功");</script>');
		}else{
			redirect(U('Activity/Index',array('time'=>time())),0.2,'<script>alert("操作失败");</script>');
		}
	}

	//上传文件
	public function upload(){
		$oldpath = $_POST['old'];
        $path = $_POST['path'];
		$field = $_POST['field'];
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     314572800 ;// 设置附件上传大小
        if($field == 'background_pic'){
        	$upload->exts = array('jpg','jpeg','gif','png');// 设置图片附件上传类型
        }else if($field == 'volume'){
        	$upload->exts = array('mp3','wav','3gp');// 设置音频附件上传类型
        }else{
        	echo json_encode(array('error'=>1,'msg'=>'上传格式错误'));die;
        }
        //删除之前上传内容

        if($oldpath != '' && $oldpath != 'undefined'){
    		$oldpath =getcwd().$oldpath;
        	@unlink($oldpath);
        }
        $upload->autoSub = false;//关闭子目录
        self::mkDirs($path);
        $upload->rootPath  =  $path; // 设置附件上传根目录
        //$upload->saveName = time().'_'.mt_rand();
        $upload->saveName = (string)time();
        $upload->replace=true;
        $info   =   $upload->uploadOne($_FILES[$field]);
        if(!$info){
        	echo json_encode(array('error'=>1,'pathsrc'=>$upload->getError(),'msg'=>'文件上传失败'));die;
        }
        $infoPath=ltrim($path.$info['savepath'].$info['savename'], ".");
        echo json_encode(array('error'=>0,'url'=>$infoPath));die;
        
    }

    //微信群发
    public function sendAll(){
    	$text = $_POST['text'];
    	$access_token = access_token();
		$url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=".$access_token;
    	$string = '{
		   "filter":{
		      "is_to_all":false,
		      "tag_id":2
		   },
		   "text":{
		      "content":"'.$text.'"
		   },
		    "msgtype":"text"
		}';
    	$result = httpPost($url,$string);
    	echo $result;die;
    	
    }
	public function test(){
		$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/get?access_token='.access_token();
		var_dump(httpPost($url,'{"msg_id":"416998735"}'));
	}
    private static function mkDirs($dir){
        if(!is_dir($dir)){
            if(!self::mkDirs(dirname($dir))){
                return false;
            }
            if(!mkdir($dir,0777)){
                return false;
            }
        }
        return true;
    }
}