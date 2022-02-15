<?php

class baseapp{
	
	public $client;
	public $Server_Path;
	public $request;
	public $smarty;
	public $userid;
    public $modulestatus=array(
        'c_complete'=>'已签收',
        'c_cancelings'=>'作废中.',
        'a_normal'=>'正常',
        'a_exception'=>'打回中',
        'b_check'=>'审核中',
        'c_complete'=>'完成',
        'c_canceling'=>'作废中',
        'c_cancel'=>'作废',
        'b_actioning'=>'执行中',
        'c_recovered'=>'已收回',
        'c_stamp'=>'已盖章',);
	function baseapp($client,$Server_Path,$request,$smarty) {
		//if(!isset($_SESSION['customer_id'])||empty($_SESSION['customer_id'])) {
		//	header("Location: login.php");
		//	exit;
		//}
		$this->client 		= $client;
		$this->Server_Path 	= $Server_Path;
		$this->request 		= $request;
		$this->smarty 		= $smarty;
		$this->userid		= $_SESSION['customer_id'];

	}

	 /**
	  * 微信取商务下的人员信息
	  * @return mixed
	 */
         public function getWeixinDepartMsgAll(){
	        $cache_token=@file_get_contents('./wtoken.txt');
	        $token=json_decode($cache_token,true);
	        //$token['access_token']='Uz1RF8TqXwjLmUGkhn8LQO29PWZjJSYSCCR0lY2HPXWZ8rPH4plUAqYu51B2Bz1R';
	        $url='https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token='.$token['access_token'].'&department_id=1&fetch_child=1&status=1';

	        $ch  = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_HEADER, 0);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
	        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
	        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	        $output = curl_exec($ch);
	        curl_close($ch);
	        return $output;
        }
	#构造查询字段
	public function create_search_field($fields){
		$res = array();

		if(empty($fields)){
			return '';
		}else{
			$count = count($fields);
			$row = '';
			for($i=0;$i<$count;$i++){
				$res["BugFreeQuery[leftParenthesesName{$i}]"]	= isset($fields[$i]['leftParenthesesName'])?$fields[$i]['leftParenthesesName']:"";
				$res["BugFreeQuery[field{$i}]"]					= isset($fields[$i]['field'])?$fields[$i]['field']:"";
				$res["BugFreeQuery[operator{$i}]"]				= isset($fields[$i]['operator'])?$fields[$i]['operator']:"";
				$res["BugFreeQuery[value{$i}]"]					= isset($fields[$i]['value'])?$fields[$i]['value']:"";
				$res["BugFreeQuery[rightParenthesesName{$i}]"]  = isset($fields[$i]['rightParenthesesName'])?$fields[$i]['rightParenthesesName']:"";
				$res["BugFreeQuery[andor{$i}]"]					= isset($fields[$i]['andor'])?$fields[$i]['andor']:"";
				$row .= 'SearchConditionRow'.$i.',';
			}
			$res['BugFreeQuery[queryRowOrder]']=$row;

			return json_encode($res);
		}
	}
	public function call($function,$params){
		$result = $this->client->call($function, $params, $this->Server_Path, $this->Server_Path,true);
		return $result;
	}
	public function run($action='index'){

		$this->$action();
		
	}
	
	public function __Call($m, $params){
		echo 'no this action';
		return false;
	}

	public function get_request($params=array()){
		if(empty($params)){
			return $this->request;
		}else{
			if(is_array($params)){
				
				$result = array();
				foreach ($params as $key => $value) {
					if(isset($this->request[$value])){
						$result[$value] = $this->request[$value];
					}else{
						$result[$value] = null;
					}
				}
				return $result;

			}else{
				if(isset($this->request[$params])){
					return $this->request[$params];
				}else{
					return false;
				}
			}
		}
	}
	#通用ajax返回信息
	public function response($error=true,$code=null){
		$res = array('res'=>'success','code'=>$code);
		if(false===$error){
			$res['res'] 	= 'error';
			$res['code'] 	=  $code;
		}
		echo json_encode($res);
		exit;
	}
	
    /**
     * 生成token
     * @param $name
     */
	public function setAddToken($name){
        if(empty($name)){
            $name='userinfo'.$this->userid;
        }
        $_SESSION[$name]='yes';
    }

    /**
     * 表单提交验证token
     * @param $name
     * @return bool
     */
    public function getAddToken($name){
        if('yes'==$_SESSION[$name]){
            $_SESSION[$name]='no';
            return false;
        }
        return true;
    }
	
	/**
	 * 写日志，用于测试,可以开启关闭
	 * @param data mixed
	 */
	public function _logs($data, $file = 'logs_'){
		$year	= date("Y");
		$month	= date("m");
		$dir	= './Logs/' . $year . '/' . $month . '/';
		if(!is_dir($dir)) {
			mkdir($dir,0755,true);
		}
		$file = $dir . $file . date('Y-m-d').'.txt';
		@file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
	}
    /**
     * @param array $filedname
     * @return mixed
     * @author: steel.liu
     * @Date:xxx
     * 文件上传
     */
    public function file_upload($filedname=array('module' => 'ServiceContracts')){
        $pictureid=$_POST['pictureid'];
        $url="https://qyapi.weixin.qq.com/cgi-bin/media/get?media_id={$pictureid}&access_token=";
        $tempfile = $this->getWeixinMsg($url);
        $filedname['filename']="image.jpg";
        $filedname['filetype']="image/jpeg";
        $filedname['filesize']=50;
        $filedname['filecontents']=base64_encode($tempfile);
        $params = array(
            'fieldname' =>$filedname,
            'userid' => $this->userid
        );
        $list = $this->call('mobile_upload', $params);
        return $list[0];
    }
    /**
     * 移动端文件下载
     */
    public function download($fileid,$module='ServiceContracts'){
        $params = array(
            'fieldname' => array(
                'module' => $module,
                'fileid' => urldecode($fileid)
            ),
            'userid' => $this->userid
        );
        $list = $this->call('mobile_download', $params);

        ob_clean();
        header("Content-type: ".$list[0][1]);
        header("Pragma: public");
        header("Cache-Control: private");
        /*$openfileArray=array('image/bmp','image/gif','image/jpeg','image/png','image/tiff','image/x-icon');
        if(!in_array($list[0][1],$openfileArray)) {//只有图片直接打开,其它的下载方式
            header("Content-Disposition: attachment; filename={$list[0][2]}");
        }*/
        header("Content-Description: PHP Generated Data");
        echo base64_decode($list[0][3]);
        exit;
    }
    /**
     * 微信取商务下的人员信息
     * @return mixed
     */
    public function getWeixinMsg($url){
        //$cache_token=trim(substr(file_get_contents("./access_token1.php"), 15));
        $cache_token=trim(file_get_contents("./wtoken.txt"));
        $token=json_decode($cache_token,true);
        $url=$url.$token['access_token'];
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * 接口转输的加密算法
     * @param $encrypt
     * @param string $key
     * @return string
     */
    public function encrypt($encrypt, $key="sdfesdcf\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0") {
        $mcrypt = MCRYPT_TRIPLEDES;
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $passcrypt = mcrypt_encrypt($mcrypt, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
        $encode = base64_encode($passcrypt);
        return $encode;
    }

    /**
     * 接口转输的解密算法
     * @param $decrypt
     * @param string $key
     * @return string
     */
    public function decrypt($decrypt, $key="sdfesdcf\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"){
        $decoded = str_replace(' ','%20',$decrypt);
        $decoded = base64_decode($decoded);
        $mcrypt = MCRYPT_TRIPLEDES;
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $decrypted = mcrypt_decrypt($mcrypt, $key, $decoded, MCRYPT_MODE_ECB, $iv);
        return $decrypted;
    }
    /**
     * 取得HEADER头信息
     * @return array
     */
    public function getHeaders(){
        $header=array();
        $header[]='loginId:'.$_SESSION['customer_id'];
        $header[]='customer_id:'.$_SESSION['customer_id'];
        $header[]='reportCode:'.$_SESSION['reports_to_id'];
        $header[]='username:'.$_SESSION['customer_name'];
        $header[]='fullName:'.$this->UnicodeEncode1($_SESSION['last_name']);
        $header[]='last_name:'.$this->UnicodeEncode1($_SESSION['last_name']);
        $header[]='roleId:'.$_SESSION['roleid'];
        $header[]='departId:'.$_SESSION['departmentid'];
        return $header;
    }
    /**
     * 汉字编码
     * @param $str
     * @return string
     */
    private function UnicodeEncode1($str)
    {
        //split word
        preg_match_all('/./u', $str, $matches);
        $unicodeStr = "";
        foreach ($matches[0] as $m) {
            //拼接
            $unicodeStr .= "&#" . base_convert(bin2hex(iconv('UTF-8', "UCS-4", $m)), 16, 10);
        }
        return $unicodeStr;
    }
    /**
     * CURL文件转送
     * @param $url
     * @param $path
     * @param $minetype
     * @param $postname
     * @return array|bool|string
     */
    private function CURLfileUpload($url,$path,$minetype,$postname){
        //1.初识化curl
        $curl = curl_init($url);
        if (class_exists('\CURLFile')) {
            $data = array('file' => new \CURLFile(realpath($path),$minetype,$postname));//>=5.5
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
            $data = array('file' => '@' . realpath($path));//<=5.5
        }
        $header=$this->getHeaders();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, true );
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true );
        curl_setopt($curl, CURLOPT_TIMEOUT, 100 );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $data=curl_exec($curl);
        curl_close($curl);
        return $data;
    }
    /**
     * 文件上传调用
     * @param $url
     * @return array|bool|string
     */
    public function getfileUpload($url,$file='file'){
        $fileInfo = $_FILES[$file];
        $filename = $fileInfo['name'];
        $type = $fileInfo['type'];
        $tmp_name = $fileInfo['tmp_name'];
        $size = $fileInfo['size'];
        $error = $fileInfo['error'];
        //2.判断错误号，只有为0或者是UPLOAD_ERR_OK,没有错误发生，上传成功
        if($error == 0){
            return $this->CURLfileUpload($url,$tmp_name,$type,$filename);
        }else{
            //匹配错误信息
            $a='';
            switch($error){
                case 1:
                    $a='上传文件超过了php配置文件中upload_max_filesize选项的值';
                    break;
                case 2:
                    $a='超过了表单MAX_FILE_SIZE限制的大小';
                    break;
                case 3:
                    $a='文件部分被上传';
                    break;
                case 4:
                    $a='没有选择上传文件';
                    break;
                case 6:
                    $a='没有找到临时目录';
                    break;
                case 7:
                case 8:
                    $a='系统错误';
                    break;
            }
        }
        return '{"code":500,"data":"","errorList":null,"message":"'.$a.'"}';
    }

    /**
     * 加载资源文件
     */
    public function loadResourceFile($url){
        $fileextend=explode('.',$url);
        $extends=array(
            'bmp'=>'image/bmp',
            'css'=>'text/css',
            'doc'=>'application/msword',
            'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'eml'=>'message/rfc822',
            'htm'=>'text/html',
            'html'=>'text/html',
            'ico'=>'image/x-icon',
            'ief'=>'image/ief',
            'isp'=>'application/x-internet-signup',
            'jfif'=>'image/pipeg',
            'jpe'=>'image/jpeg',
            'jpeg'=>'image/jpeg',
            'jpg'=>'image/jpeg',
            'png'=>'image/png',
            'pdf'=>'application/pdf',
            'js'=>'application/x-javascript',
            'roff'=>'application/x-troff',
            'rtf'=>'application/rtf',
            'texi'=>'application/x-texinfo',
            'texinfo'=>'application/x-texinfo',
            'tgz'=>'application/x-compressed',
            'tif'=>'image/tiff',
            'tiff'=>'image/tiff',
            'xof'=>'x-world/x-vrml',
            'xls'=>'application/vnd.ms-excel',
            'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xpm'=>'image/x-xpixmap',
            'xwd'=>'image/x-xwindowdump',
            'zip'=>'application/zip'
        );
        $contentType=$extends[end($fileextend)];
        if(empty($contentType)){
            $contentType='text/html';
        }
        header("Content-type: ".$contentType);
        echo  file_get_contents($url);
    }

    /**
     * 转输数据
     * @param $url
     * @param $data
     * @param $headers
     * @param string $method
     * @return bool|string
     */
    public function curlTransferData($url,$data,$headers,$method = 'GET')
    {
        //初始化
        $ch = curl_init();
        if($method == 'GET'){
            $request=$_REQUEST;
            if($request){
                $querystring = http_build_query($request);
                $url = $url.'?'.$querystring;
            }
        }
        // 请求头，可以传数组
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);         // 执行后不直接打印出来
        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');     // 请求方式
            curl_setopt($ch, CURLOPT_POST, true);               // post提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);              // post的变量
        }
        if($method == 'PUT'){
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        }
        if($method == 'DELETE'){
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不从证书中检查SSL加密算法是否存在
        $output = curl_exec($ch); //执行并获取HTML文档内容
        curl_close($ch); //释放curl句柄
        return $output;
    }
    /**
     * 对外数据转送接口
     */
    public function sendData(){
        $m = $_REQUEST['filepath'];
        $body = file_get_contents('php://input');
        $method=$_SERVER['REQUEST_METHOD'];
        $headers=$this->getHeaders();
        $headers[]="Content-type:application/json;charset=utf-8";
        $url=$this->curlSendDataURL.$m;
        echo $this->curlTransferData($url,$body,$headers,$method);
    }
    /**
     * 文件上传
     */
    public function fileUpload(){
        $m = $_REQUEST['filepath'];
        $this->getfileUpload($this->curlFileUploadURL.$m);
    }

    /**
     * 获取资源文件
     */
    public function getResourceFile(){
        $m = $_REQUEST['filepath'];
        $url=$this->curlResourceURL.$m;
        $this->loadResourceFile($url);
    }
    /**
     * 获取文件
     */
    public function getFile(){
        $m = $_REQUEST['filepath'];
        $url=$this->curlFileURL.$m;
        $this->loadResourceFile($url);
    }

    /**
     * 人口文件
     * @param $url
     */
    public function indexFile($url){
        $this->loadResourceFile($url);
    }
}