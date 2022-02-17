<?php

class baseapi{
	
	public $client;
	public $Server_Path;
	public $request;
	public $smarty;
	public $userid;
	function baseapi($client,$Server_Path,$request,$smarty) {
		if(!isset($_SESSION['customer_id_api'])||empty($_SESSION['customer_id_api'])) {
			//header("Location: login.php");
			exit;
		}
		$this->client 		= $client;
		$this->Server_Path 	= $Server_Path;
		$this->request 		= $request;
		$this->smarty 		= $smarty;
		$this->userid		= $_SESSION['customer_id_api'];

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

    public function https_request($url, $data = null,$curlset=array()){
        $this->_logs(array("发送到T云服务端的url请求", $url));
        $curl = curl_init();
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $this->_logs(array("返回处理结果：", $output));
        curl_close($curl);
        return $output;
    }

    public function getVerifyCode($mobile){
	    date_default_timezone_set('Asia/Shanghai');
        $url = 'http://tyapi.71360.com/pi/app/aggregateservice-api/v1.0.0/api/SMS/SendMobileCaptcha';
        $sault='multiModuleProjectDirectoryasdafdgfdhggijfgfdsadfggiytudstlllkjkgff';
        $time=time().'123';
        $token=md5($time.$sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $postData = array(
            "mobile"=>$mobile
        );
        $this->_logs(array("getTyunWebUserCode：", $postData));
        $this->_logs(array("getTyunWebUserHeader：", $curlset));
        $res = $this->https_request($url, json_encode($postData),$curlset);
        $res = json_decode($res,true);
        if($res['success']){
            return array('success'=>true,'message'=>'已发送');
        }
        return array('success'=>false,'message'=>$res['message']);
    }

    public function checkVerifyCode($mobile,$code){
	    $url = 'http://tyapi.71360.com/api/app/aggregateservice-api/v1.0.0/api/SMS/CheckMobileCode';
        $sault='multiModuleProjectDirectoryasdafdgfdhggijfgfdsadfggiytudstlllkjkgff';
        $time=time().'123';
        $token=md5($time.$sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $postData = array(
            "mobile"=>$mobile,
            'code'=>strval($code)
        );
        $res = $this->https_request($url, json_encode($postData),$curlset);
        $res = json_decode($res,true);
        if($res['success']){
            return array('success'=>true,'message'=>'验证通过');
        }
        return array('success'=>false,'message'=>$res['message']);
    }

    /**
     * 获取微信TOKEN
     * @return mixed
     */
    public function getQYWXToken(){
        $cache_token=@file_get_contents('./wtoken.txt');
        $token=json_decode($cache_token,true);
        return $token['access_token'];
    }
    /**
     * 验证token
     * @param $sault
     */
    public function checkToken($sault)
    {
        $ntime=time();
        $HTTP_ERP_REQUEST_TOKEN=$_SERVER['HTTP_ERP_REQUEST_TOKEN'];
        $HTTP_ERP_REQUEST_TIME=$_SERVER['HTTP_ERP_REQUEST_TIME'];
        $ntoken=md5($HTTP_ERP_REQUEST_TIME.$sault);
        if($HTTP_ERP_REQUEST_TIME>$ntime+45 ||  $HTTP_ERP_REQUEST_TIME<$ntime-45 || $ntoken!=$HTTP_ERP_REQUEST_TOKEN){
            header("HTTP/1.1 500 Internal Server Error");
            exit;
        }
    }
}