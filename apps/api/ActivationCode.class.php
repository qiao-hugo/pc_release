<?php
/**
 * Api接口
 * @author Jeff
 *
 */
class ActivationCode extends baseapi
{
    /**
     * 更新crm中的激活信息
     */
    public function updateActiveCodeInfo(){
    	$decData = $this->decrypt($_POST['data']);
    	if(!empty($decData)){
    		$decData = trim($decData);
    		$this->_logs(array("T云传入数据：", $decData));
    		$decArr = json_decode($decData, true);
    		if(!empty($decArr)){
    			$params = array('fieldname'=>$decArr);
    			$res = $this->call('findActivecode', $params);
    			$this->_logs(array("处理返回结果：", $res));
    			if($res[0]['success'] == 1){
    				echo json_encode(array('success'=>'true', 'message'=>'操作成功'));
    			}else if($res[0]['success'] == 2){
    				echo json_encode(array('success'=>'false', 'message'=>'操作失败'));
    			}else{
    				echo json_encode(array('success'=>'false', 'message'=>'激活码不存在'));
    			}
    			exit();
    		}else{
    			echo json_encode(array('success'=>'false', 'message'=>'参数错误'));
    			exit();
    		}
    	}else{
    		echo json_encode(array('success'=>'false', 'message'=>'参数错误'));
    	}
    	exit();
    }
	public function updateActiveCodeInfo1(){
    	//$decData = $this->decrypt($_POST['data']);
    	if(true){
    		$decData = trim($decData);
    		$this->_logs(array("T云传入数据：", $decData));
    		$decArr = json_decode('{"loginname":"shpuxin","productid":"c83cce8e-4993-11e7-a335-5254003c6d38","activecode":"17226c0e-62e2-11e7-a335-5254003c6d38","activetype":"online","startdate":"2017/7/26 11:36:34","enddate":"2018/7/26 11:36:34"}', true);
    		if(!empty($decArr)){
    			$params = array('fieldname'=>$decArr);
    			$res = $this->call('findActivecode', $params);
    			$this->_logs(array("处理返回结果：", $res));
    			if($res[0]['success'] == 1){
    				echo json_encode(array('success'=>'true', 'message'=>'操作成功'));
    			}else if($res[0]['success'] == 2){
    				echo json_encode(array('success'=>'false', 'message'=>'操作失败'));
    			}else{
    				echo json_encode(array('success'=>'false', 'message'=>'激活码不存在'));
    			}
    			exit();
    		}else{
    			echo json_encode(array('success'=>'false', 'message'=>'参数错误'));
    			exit();
    		}
    	}else{
    		echo json_encode(array('success'=>'false', 'message'=>'参数错误'));
    	}
    	exit();
    }
    public function contractChange(){
        $decData = $this->decrypt($_POST['data']);
        if(!empty($decData)){
            $decData = trim($decData);
            //$this->_logs(array("订单变更：", $decData));
            $decArr = json_decode($decData, true);
            if(!empty($decArr)){
                $params = array('fieldname'=>$decArr);
                $res = $this->call('tyunContractChange', $params);
                //$this->_logs(array("处理返回结果：", $res));
                if($res[0]['success'] == 1){
                    echo json_encode(array('success'=>'true', 'message'=>'操作成功'));
                }else if($res[0]['success'] == 2){
                    echo json_encode(array('success'=>'false', 'message'=>'操作失败'));
                }else{
                    echo json_encode(array('success'=>'false', 'message'=>'合同编号不存在'));
                }
                exit();
            }else{
                echo json_encode(array('success'=>'false', 'message'=>'参数错误'));
                exit();
            }
        }else{
            echo json_encode(array('success'=>'false', 'message'=>'参数错误'));
        }
        exit();
    }

    /**
     * 获取公司销售及客服信息
     */
    public function getcompanysaleandserviceinfo(){
        $username = $_POST['username'];
        $companyname = $_POST['companyname'];
        $username = trim($username);
        $companyname=trim($companyname);
        if(!empty($username)){
                $params = array("fieldname"=>array('errorcode'=>$username,
                    'companyname'=>$companyname,));
                $res = $this->call('getCompanySaleServiceInfo', $params);
                if(!empty($res[0])){
                    $res[0]['errorcode']=0;
                    echo json_encode($res[0], JSON_UNESCAPED_UNICODE);
                }else{
                    echo json_encode(array('errorcode'=>'1', 'msg'=>'没有相关客户客服信息!'), JSON_UNESCAPED_UNICODE);
                }
                exit();

        }else{
            echo json_encode(array('errorcode'=>'1', 'msg'=>'用户账号为空'), JSON_UNESCAPED_UNICODE);
        }
        exit();
    }
    public function test(){
    	$decData = $this->decrypt($_REQUEST['data']);
    	$decData = trim($decData);
    	$decArr = json_decode($decData, true);
    	//print_r($decArr);
    	//echo $this->userid . ' ' .$_SESSION['customer_id'];
    	echo json_encode($decArr);
    	exit();
    }
    
    /**
     * des加密
     * @param unknown $encrypt 原文
     * @return string
     */
    /* function encrypt($encrypt, $key='sdfesdcf') {
    	$mcrypt = MCRYPT_TRIPLEDES;
    	$iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
    	$passcrypt = mcrypt_encrypt($mcrypt, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
    	$encode = base64_encode($passcrypt);
    	return $encode;
    } */
    
    /**
     * des解密
     * @param unknown $decrypt
     * @return string
     */
    function decrypt($decrypt, $key='sdfesdcf'){
    	$decoded = str_replace(' ','%20',$decrypt);
    	$decoded = base64_decode($decrypt);
    	$mcrypt = MCRYPT_TRIPLEDES;
    	$iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
    	$decrypted = mcrypt_decrypt($mcrypt, $key, $decoded, MCRYPT_MODE_ECB, $iv);
    	return $decrypted;
    }
    
    /* public function https_request($url, $data = null){
    	$curl = curl_init();
    	//curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
    	curl_setopt($curl, CURLOPT_URL, $url);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    
    	if (!empty($data)){
    		curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
    		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    	}
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    	$output = curl_exec($curl);
    	curl_close($curl);
    	return $output;
    } */

    /**
     * 建站合同状态回传接口 gaocl add 2018/04/08
     */
    public function tyunReturnContractStatus(){
        $decData = $this->decrypt($_REQUEST['data']);
        if(!empty($decData)){
            $decData = trim($decData);

            $this->_logs(array("T云传入数据(tyunReturnContractStatus)：", $decData));
            $decArr = json_decode($decData, true);

            if(!empty($decArr)){
                $params = array('fieldname'=>$decArr);
                $res = $this->call('updateTyunStationSale', $params);
                $this->_logs(array("T云返回结果(tyunReturnContractStatus)：", $res));

                if($res[0]['success'] == 1){
                    echo json_encode(array('success'=>'true', 'message'=>'操作成功'));
                }else if($res[0]['success'] == 2){
                    echo json_encode(array('success'=>'false', 'message'=>'操作失败'));
                }else{
                    echo json_encode(array('success'=>'false', 'message'=>'合同编号不存在'));
                }
                exit();
            }else{
                echo json_encode(array('success'=>'false', 'message'=>'参数错误'));
                exit();
            }
        }else{
            echo json_encode(array('success'=>'false', 'message'=>'参数错误'));
        }
        exit();
    }
    /**
     * 建站合同服务进度接口 gaocl add 2018/05/03
     */
    public function tyunStationServiceProgress(){
        $decData = $this->decrypt($_REQUEST['data']);
        if(!empty($decData)){
            $decData = trim($decData);

            $this->_logs(array("T云建站合同服务进度传入数据(saveStationServiceProgress)：", $decData));
            $decArr = json_decode($decData, true);

            if(!empty($decArr)){
                $params = array('fieldname'=>$decArr);
                $res = $this->call('saveStationServiceProgress', $params);
                $this->_logs(array("T云返回结果(saveStationServiceProgress)：", $res));

                if($res[0]['success'] == 1){
                    echo json_encode(array('success'=>'true', 'message'=>'操作成功'));
                }else{
                    echo json_encode(array('success'=>'false', 'message'=>'操作失败'));
                }
                exit();
            }else{
                echo json_encode(array('success'=>'false', 'message'=>'参数错误'));
                exit();
            }
        }else{
            echo json_encode(array('success'=>'false', 'message'=>'参数错误'));
        }
        exit();
    }

    /**
     * 根据客户名称获取对应的客服信息
     */
    public function getCustomerService(){
        $data=str_replace(' ','+',$_REQUEST['data']);
        $decData = $this->decrypt($data);
        if(!empty($decData)){
            $decData = trim($decData);
            $this->_logs(array("getCustomerService(getCustomerService)：", $decData));
            $decArr = json_decode($decData, true);
            if(!empty($decArr)){
                $params = array('fieldname'=>$decArr);
                $res = $this->call('getCustomerService', $params);
                $this->_logs(array("T云返回结果(getCustomerService)：", $res));
                if($res[0]['success'] == 2){
                    echo json_encode(array('success'=>'true', 'data'=>$res[0]['data']),JSON_UNESCAPED_UNICODE);
                    exit();
                }
            }
        }
        echo json_encode(array('success'=>'false', 'data'=>array()));
        exit();
    }
    /**
     * 根据T云账户获取客服信息
     */
    public function getServiceUserCode(){
        $params = array(
            'fieldname' => array(
                'module' => 'ActivationCode',
                'action' => 'getServiceUserCode',
                'page'=>$_REQUEST['page'],
                'userid' => 0
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        $this->_logs(array("T云返回结果(getServiceUserCode)：", $res));
        if (!empty($res[0])) {
            $reutrn=array_values($res[0]);
            echo json_encode(array('success' => 'true', 'code' => 200, 'data' =>$reutrn), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }
    }
}
