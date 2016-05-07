<?php
namespace Backend\Controller;
use Think\Controller;

class ActivityController extends Controller{
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
        $whereArray['start'] = ($p - 1) * 20;
        $count = M('Activity')->where($whereArray)->count();
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
		$intArray = array('invite_num','egg_num','continue_num','rank_list');
		$timeArray = array('start_time','end_time','notice_time');
		$model = D('Activity');
		// $editor = D('User')->getField(array('id'=>intval($this->user_id)),'username')['username'];
		// $data$data['editor'] = $editor;
        $data['edit_time'] = time();
		foreach ($saveList as $key => $value) {
			if(in_array($key,$intArray)){
				$data[$key] = intval($value);
			}else if(in_array($key,$timeArray)){
				//时间戳函数]
				$data[$key] = ($value != '') ?  strtotime($value) : '';
			}else if($key == 'success_condition'){
				if(trim($value) == '邀请人数'){
					$data['success_condition'] = 1;
				}else if(trim($value) == '排行榜'){
					$data['success_condition'] = 2;
				}else{
					$data['success_condition'] = 0;
				}
			}else{
				$data[$key] = $value;
			}
		}
		$text_content = '';
		foreach ($data['text_content'] as $k => $v) {
			if($v != ''){
				$text_content .= $v.'||';
			}
		}
		
		if($text_content != ''){
			$text_content = substr($text_content,0,strlen($text_content)-2); 
		}
		$data['text_content'] = $text_content;
		// echo "<pre>";
		// // var_dump($data);
		// die;
		if($id){
			//修改
			$r = $model->saveData(array('id'=>$id),$data);
		}else{
			//新建
			$r = $model->addData($data);
		}
		if ($r) {
			redirect(U('Activity/Index',array('time'=>time())),0.2,'<script>alert("保存成功");</script>');
		}else{
			redirect(U('Activity/Index',array('time'=>time())),0.2,'<script>alert("保存失败");</script>');
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
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        if($field == 'background_pic'){
        	$upload->exts = array('jpg','jpeg','gif','png');// 设置图片附件上传类型
        }else{
        	$upload->exts = array('mp3','wav','3gp');// 设置音频附件上传类型
        }
        //删除之前上传内容
        if($oldpath != ''){
        	$oldpath = getcwd().$oldpath;
        	var_dump($oldpath);
        	unlink(getcwd().$oldpath);
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