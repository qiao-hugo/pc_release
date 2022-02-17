<?php
/**
 * 获取T云激活码
 * @author Jeff
 *
 */
class ActivationCode extends baseapp
{
    //private $urlUserGet='http://tyunapi.71360.com/api/cms/UserGet';//获取用户名信息
	private $urlUserGet='http://apityun.71360.com/api/cms/UserGet';//获取用户名信息
    private $urlUserUpgrade='http://apityun.71360.com/api/cms/UserUpgrade';//升级接口
    private $urlUserRenew='http://apityun.71360.com/api/cms/UserRenew';//续费接口

    //CRM新API-创建购买订单
    //====线上地址========================================================
    //private $tyun_buy_url = "http://tyunapi.71360.com/CRM/GetSecretkey";
    //获取手机验证码
    //private $mobilecode_url = "http://tyunapi.71360.com/api/cms/GetMobileCode";

    //===预发布地址=========================================================
    private $mobilecode_url = "http://apityun.71360.com/api/cms/GetMobileCode";
    private $tyun_buy_url = "http://apityun.71360.com/api/CRM/GetSecretkey";

    //===测试地址=========================================================
    //private $mobilecode_url = "http://192.168.40.118:8630/api/cms/GetMobileCode";
    //private $tyun_buy_url = "http://192.168.40.118:8630/api/CRM/GetSecretkey";


    //获取T云激活码
    public function add()
    {
    	/* require_once './jssdk1.php';
    	$appId = "wxb04894fa4f668ab5";
    	$secret = "w2YD7_wjHgoD7UxOJLRqodepg093fs_45jeTTbqZ-HS4dyDmga7cp81pOXJsEeVX";
    	$jssdk = new jssdk1($appId, $secret); 
    	$signPackage = $jssdk->getSignPackage();
    	//print_r($signPackage);
    	$this->smarty->assign('signPackage', $signPackage); */
    	
    	require_once "jssdk.php";
    	$jssdk = new JSSDK("wx74d59c197d3976ee", "8afc371fd3c51ee97d3d8f93647fe219");
    	$signPackage = $jssdk->GetSignPackage();
    	$this->smarty->assign('signPackage',$signPackage);
    	$params = array(
    			'userid'			=> $this->userid
    		);
    	$list = $this->call('findParentDepartment', $params);
    	$this->_logs(array("findParentDepartment：".$this->userid, $list));
		$arr1 = array(
			'H1::H2::H3::H72::H4'=>10567,
			'H1::H2::H3::H72::H7'=>10568,
			'H1::H2::H3::H72::H5'=>10582,
			'H1::H2::H3::H72::H8'=>10569,
			'H1::H2::H102'=>10574,
			'H1::H2::H3::H24'=>10573,
			'H1::H2::H3::H133'=>10566,
			'H1::H2::H3::H125'=>10575,
			'H1::H2::H3::H160'=>10598,
			'H1::H2::H3::H156'=>10599,
			'H1::H2::H3::H175'=>10600,
			'H1::H2::H3::H22'=>10487,
			'H1::H2::H3::H111'=>10573,
            'H1::H2::H3::H72::H310'=>10769,
		);
		$agents = 0;
		$userarray=array(43,199);
		$threearray=array(427);//三营
		foreach ($arr1 as $k=>$v){
		    if(in_array($this->userid,$userarray)){
                //$agents = 10567;
                $agents = 10642;
                break;
            }
			if(in_array($this->userid,$threearray)){
                $agents = 10582;
                break;
            }
			if(false !== strpos($list[0]['parentdepartment'], $k)){
				$agents = $v;
				break;
			}
		}
		$this->_logs(array("agents：", $agents));
    	$this->smarty->assign('agents', $agents);
    	$this->smarty->assign('userid', $this->userid);
        $this->smarty->display('ActivationCode/add.html');
    }

    
    public function ttt(){
    	$arrMails = array(
    			10567 => 'ingram.ye@71360.com',
    			10568 => 'weibo.wang@71360.com',
    			10582 => 'anne.li@71360.com',
    			10569 => 'daveth.chen@71360.com',
    			10574 => 'andy.ma@71360.com',
    			10566 => 'fei.gu@71360.com',
    			10575 => 'king.yu@71360.com',
    			10598 => 'martin.que@71360.com',
    			10599 => 'david.zhang@71360.com',
    			10600 => 'summer.yu@71360.com',
    			10487 => 'Bill.ma@71360.com',
    			10573 => 'tana.huang@71360.com',
            10769=>'wilson.xiao@71360.com',
    	);
    	$params = array(
    			'fieldname' => array(
    					//'agent_mail' => $arrMails[$agents]?$arrMails[$agents]:'',
    					'agent_mail' => 'jeff.zheng@71360.com',
    			),
    			'userid'			=> $this->userid
    	);
    	$list = $this->call('sendMailAgent', $params);
    	print_r($list);
    }
    
    //查找合同编号
    public function findContractNo(){
    	$id = trim($_REQUEST['id']);
    	if(!empty($id)){
	    	$idNo1 = strpos($id, ",");
			if($idNo1 !== false){
				$id = substr($id, ($idNo1+1));
				$idNo2 = strpos($id, "-");
				if($idNo2 !== false){
					$id = substr($id, 0, $idNo2);
				}
			}
			
    		$params = array(
    			'fieldname' => array(
    				'id' => $id,
    			),
    			'userid'			=> $this->userid
    		);
    		$list = $this->call('findContractNo', $params);
    		echo json_encode(array('contractNo'=>$list[0]));
    		exit;
    	}
    }
    
    /**
     * ajax获取手机验证码
     */
    public function ajaxGetMobileVerify(){
    	$mobile = $_REQUEST['mobile'];
    	//$myData = array('Mobile'=>$mobile);
    	//$tempData = json_encode($myData);
    	$data['data'] = $this->encrypt($mobile);
    	$postData = http_build_query($data);//传参数
    	//print_r($data);
    	/* echo "参数:" . $mobile."<br>";
    	echo "加密后";echo $postData."<br>"; */
    	$res = $this->https_request($this->mobilecode_url, $postData);
    	$result = json_decode($res, true);
    	$result = json_decode($result, true);
    	if($result['success']){
    		session_start();
    		$_SESSION['setMobileCodeVerify'] = $result['message'];
    		echo json_encode(array('success'=>1, 'msg'=>'已发送'));
    	}else{
    		echo json_encode(array('success'=>0, 'msg'=>$result['message']));
    	}
		exit();
    }
    
    /**
     * 获取激活码
     */
    public function ajaxGetSecreCode(){
    	$tyunData = array();
    	$acData = array();

    	//T云接口参数
        $tyunData['ContractCode'] = $_REQUEST['contractname_display'];//合同编号
        $tyunData['CompanyName'] = $_REQUEST['customername'];//客户名称
        $tyunData['ProductID'] = $_REQUEST['productid'];//产品ID
        $tyunData['BuyYear'] = $_REQUEST['productlife'];//年限
        $tyunData['CustPhone'] = $_REQUEST['mobile'];//客户手机号码
        $tyunData['VerificationCode']  = $_SESSION['setMobileCodeVerify'];//获取验证码
        $tyunData['AgentIdentity'] = $_REQUEST['agents'];//代理商标识码

    	//$myData['SalesName'] = $_SESSION['customer_name']?$_SESSION['customer_name']:'';//销售人员
    	//$myData['SalesPhone'] = $_SESSION['phone_mobile']?$_SESSION['phone_mobile']:'';//销售人员联系方式,新加的

        //另购服务对象 gaocl add 2018-06-11
        //$json_crm_serviceinfo = "";
        $arr_crm_serviceinfo = array();
        if(!empty($_POST['inserti'])){
            $arr_exist_serviceid = array();
            //$arr_new_serviceinfo = array();
            foreach($_POST['inserti'] as $key=>$value){
                $serviceID = $_POST['ServiceID'][$value];
                $buycount = $_POST['BuyCount'][$value];
                $remark = $_POST['servicename_display'][$value].':'.$buycount;
                if(empty($serviceID) || $serviceID == '0'){
                    continue;
                }

                /*$tmp_serviceID1 = explode("|",$serviceID);
                if(!in_array($tmp_serviceID1[0],$arr_exist_serviceid)){
                    $arr_exist_serviceid[]=$tmp_serviceID1[0];
                    $arr_new_serviceinfo[]=array("ServiceID"=>$tmp_serviceID1[0],"BuyCount"=>$buycount,"Remark"=>$remark);
                }else{
                    for($i=0;$i<count($arr_new_serviceinfo);$i++){
                        $tmp_serviceID2 = $arr_new_serviceinfo[$i]['ServiceID'];
                        $tmp_serviceID2 = explode("|",$tmp_serviceID2);
                        $tmp_buycount2 = $arr_new_serviceinfo[$i]['BuyCount'];
                        $tmp_remark2 = $arr_new_serviceinfo[$i]['Remark'];
                        if($tmp_serviceID1[0] == $tmp_serviceID2[0]){
                            $arr_new_serviceinfo[$i]['BuyCount'] = bcadd($buycount,$tmp_buycount2);
                            $arr_new_serviceinfo[$i]['Remark'] = $tmp_remark2.';'.$remark;
                            break;
                        }
                    }
                }*/
                $arr_serviceinfo[]=array("ServiceID"=>$serviceID,"BuyCount"=>$_POST['TyunBuyCount'][$value]);
                $arr_crm_serviceinfo[]=array("ServiceID"=>$serviceID,"BuyCount"=>$_POST['BuyCount'][$value]);
                $arr_serviceinfo_display[]=array("servicename_display"=>$_POST['servicename_display'][$value],"buycount_display"=>$_POST['buycount_display'][$value]);
            }
            $tyunData['BuyServiceinfo'] = json_encode($arr_serviceinfo);
            //$json_crm_serviceinfo = json_encode($arr_serviceinfo);
        }else{
            $tyunData['BuyServiceinfo'] = json_encode(array());
        }
        date_default_timezone_set('PRC');
        //购买时间
        $buydate = $_REQUEST['buydate'];
        if(!empty($buydate)){
            $nowTime = date(" H:i:s", time());
            $tyunData['AddDate'] = $buydate.$nowTime;
        }else{
            $nowTime = date("Y-m-d H:i:s", time());
            $tyunData['AddDate'] = $nowTime;
        }

    	$authCode = $_REQUEST['auth_code'];//验证码
    	$mobileCode = $_SESSION['setMobileCodeVerify'];//获取验证码
    	$acData['customerid'] = $_REQUEST['customerid'];//客户id
    	$acData['address'] = $_REQUEST['sign_address'];//签到地址
    	$acData['contractid'] = $_REQUEST['contractid'];//合同编号ID

    	if($authCode <> $mobileCode){
    		echo json_encode(array('success'=>0, 'msg'=>'手机验证码错误'));
    		exit();
    	}

    	$this->_logs(array("data加密前数据：", $tyunData));
        $this->_logs(array("data加密前数据(json)：", json_encode($tyunData)));
    	$tempData['data'] = $this->encrypt(json_encode($tyunData));
    	$this->_logs(array("data加密后数据：", $tempData['data']));
    	//print_r($tyunData);
    	$mobileCode = $_SESSION['setMobileCodeVerify'];
    	//echo "sign加密前:" . $tyunData['ProductID'] . '_' . $tyunData['ContractCode'] . '_' . $mobileCode . '<br>';
    	$this->_logs(array('sign加密前：', $tyunData['ProductID'] . '_' . $tyunData['ContractCode'] . '_' . $mobileCode));
    	
    	$tempData['sign'] = md5($tyunData['ProductID'] . '_' . $tyunData['ContractCode'] . '_' . $mobileCode);
    	$this->_logs(array('sign加密前：', $tempData['sign']));
    	//echo "sign加密后:" . $tempData['sign']. '<br>';
    	$postData = http_build_query($tempData);//传参数
    	$res = $this->https_request($this->tyun_buy_url, $postData);
    	//print_r($res);exit;
    	$result = json_decode($res, true);
    	$result = json_decode($result, true);
        $this->_logs(array("调用".$this->tyun_buy_url."接口后返回数据：", $result));
    	//=====测试=======================================
    	//$result['success'] = 1;
    	//$result['message'] = 'ssssss-eerrrt-wedsf';
    	//================================================

        //保存调用T云接口返回数据====================================
        $t_params = array(
            'fieldname' => array(
                'contractno' => $tyunData['ContractCode'],
                'classtype' => 'buy',
                'tyunurl' => $this->tyun_buy_url,
                'crminput' => json_encode($tyunData),
                'success'=>$result['success'],
                'tyunoutput' => json_encode($result)
            ),
            'userid'			=> $this->userid
        );
        $this->call('saveTyunResposeData', $t_params);
        //============================================================

    	if($result['success']){
    		$acData['activecode'] = $result['SecretKey'];//激活码
    		$acData['contractname'] = $_REQUEST['contractname_display'];//合同编号
    		$acData['customername'] = $_REQUEST['customername'];//客户名称
    		$acData['agents'] = $_REQUEST['agents'];//代理商标识码
    		$acData['productlife'] = $_REQUEST['productlife'];//年限
    		$acData['productid'] = $_REQUEST['productid'];//产品ID
    		$acData['mobile'] = $_REQUEST['mobile'];//客户手机号码
            $acData['SalesName'] = $_SESSION['customer_name']?$_SESSION['customer_name']:'';//销售人员
            $acData['SalesPhone'] = $_SESSION['phone_mobile']?$_SESSION['phone_mobile']:'';//销售人员联系方式,新加的
    		//$acData['salesname'] = $tyunData['SalesName'];//销售人员
    		//$acData['salesphone'] = $tyunData['SalesPhone'];//销售人员联系方式
            //另购对象 gaocl add 2018/06/11
            $acData['t_buyserviceinfo'] = json_encode($arr_crm_serviceinfo);
            $acData['t_contractamount'] = $_REQUEST['contractamount'];//合同金额
            //$acData['crm_buyserviceinfo'] = $json_crm_serviceinfo;
            $acData['classtype'] = 'buy';
            $acData['buyid'] = 0;//购买id
            $acData['startdate'] = $_REQUEST['startdate'];
            $acData['buydate'] = $tyunData['AddDate'];

    		$ActivationCodeData = array();
    		$ActivationCodeData['module'] = 'ActivationCode';
    		$ActivationCodeData['action'] = 'Save';
    		$ActivationCodeData['receivetime'] = $tyunData['AddDate'];
    		$ActivationCodeData = array_merge($ActivationCodeData, $acData);
    		//print_r($ActivationCodeData);
    		$nowTime = date("Y-m-d H:i", time());
    		$this->_logs(array('activeation：', $ActivationCodeData));
    		$list = $this->call('saveSecreCodeInfo', array('fieldname'=>array('ActivationCodeData'=>$ActivationCodeData, 'acData'=>$acData), 'userid'=>$this->userid));
    		$this->_logs(array('返回list：', $list));
    		if(!empty($list[0])){
    			$arrMails = array(
    					10567 => 'ingram.ye@71360.com',
    					10568 => 'weibo.wang@71360.com',
    					10582 => 'anne.li@71360.com',
    					10569 => 'daveth.chen@71360.com',
    					10574 => 'andy.ma@71360.com',
    					10566 => 'fei.gu@71360.com',
    					10575 => 'king.yu@71360.com',
    					10598 => 'martin.que@71360.com',
    					10599 => 'david.zhang@71360.com',
    					10600 => 'summer.yu@71360.com',
    					10487 => 'Bill.ma@71360.com',
						10573 => 'tana.huang@71360.com',
                    10769=>'wilson.xiao@71360.com',
    			);
    			$agents = $acData['agents'];
    			$params = array(
    					'fieldname' => array(
    							'agent_mail' => $arrMails[$agents]?$arrMails[$agents]:'',
    							'customername' => $_REQUEST['customername'],
                                'contractno' => $_REQUEST['contractname_display'],
    							'productid' => $acData['productid'],
    							'productlife' => $tyunData['BuyYear'],
    							'nowTime' => $nowTime,
                                'classtype' => 'buy',
                                'Subject'=>'T云版本购买通知',
                                'buyserviceinfo'=>$arr_serviceinfo_display,
    							'mobile' => $acData['mobile'],
    					),
    					'userid'			=> $this->userid
    			);
    			$list = $this->call('sendMailAgent', $params);
    			
    			echo json_encode(array('success'=>1, 'msg'=>$result['message']));
    		}else{
    			echo json_encode(array('success'=>2, 'msg'=>'保存失败'));
    		}
    	}else{
    		echo json_encode(array('success'=>0, 'msg'=>$result['message']));
    	}
    	exit();
    }
    
    /**
     * 验证手机号码
     */
    public function checkAuthCode(){
    	$code = $_REQUEST['code'];
    	$mobileCode = $_SESSION['setMobileCodeVerify'];
    	if($mobileCode == $code && !empty($mobileCode)){
    		echo json_encode(array('success'=>1));//验证码正确
    	}else{
    		echo json_encode(array('success'=>0));//验证码错误
    	}
    	exit();
    }
    
    /**
     * 合同作废
     */
    public function invalidContract(){
    	$myData['ContractCode'] = 'ZD-TYUN2017000333';//合同编号
    	$myData['SecretKeyID'] = '021a710d-0ede-11e7-8ff8-fa163e551eb6';//激活码ID
    	$url = "http://tyunapi.71360.com/api/cms/InvalidSecretKey";
    	$this->_logs(array("data加密前数据：", $myData));
    	$tempData['data'] = $this->encrypt(json_encode($myData));
    	$this->_logs(array("data加密后数据：", $tempData['data']));
    	$postData = http_build_query($tempData);//传参数
    	$res = $this->https_request($url, $postData);
    	$result = json_decode($res, true);
    	$result = json_decode($result, true);
    	print_r($result);
    	if($result['success']){
    		echo json_encode(array('success'=>1, 'msg'=>$result['message']));
    	}else{
    		echo json_encode(array('success'=>0, 'msg'=>$result['message']));
    	}
		exit();
    }
    
    /**
     * 激活信息更新
     */
    public function updateSecretInfo(){
    	$myData['ContractCode'] = "ZD-TYUN2017000333";//合同编号
    	$myData['CompanyName'] = "上海菘海实业有限公司";//客户名称
    	$myData['ProductLife'] = "3";//年限
    	$myData['ProductID'] = "fb016797-4296-11e6-ad98-00155d069461";//产品编号
    	$url = "http://tyunapi.71360.com/api/cms/UpdateSecretKey";
    	$this->_logs(array("data加密前数据：", $myData));
    	$tempData['data'] = $this->encrypt(json_encode($myData));
    	$this->_logs(array("data加密后数据：", $tempData['data']));
    	$postData = http_build_query($tempData);//传参数
    	$res = $this->https_request($url, $postData);
    	$result = json_decode($res, true);
    	$result = json_decode($result, true);
    	
    	if($result['success']){
    		echo json_encode(array('success'=>1, 'msg'=>$result['message']));
    	}else{
    		echo json_encode(array('success'=>0, 'msg'=>$result['message']));
    	}
    	exit();
    }
    
    
    
    #查找合同编号
    public function searchContract(){
    	$contract_no = trim($_REQUEST['contract_no']);
    	if(!empty($contract_no)){
    		$params = array(
    				'fieldname'=>array(
    						'contract_no'	=> $contract_no,
    				),
    				'userid'			=> $this->userid
    		);
    		$list = $this->call('contractSearch', $params);
    		$list = $list[0];
    		if(!empty($list['item']) && count($list) == 1){
    			$list = array($list['item']);
    		}
    		echo json_encode($list);
    		exit;
    	}
    	echo "";exit;
    	//print_r($list);
    
    }
    
    /**
     * 据据客户ID取得合同
     */
    public function searchContractWAid(){
        $contract_no = trim($_REQUEST['accountid']);
        if(!empty($contract_no)){
            $params = array(
                'fieldname'=>array(
                    'accountid'	=> $contract_no,
                ),
                'userid'			=> $this->userid
            );
            $list = $this->call('contractWithAccountid', $params);
            $list = $list[0];
            if(!empty($list['item']) && count($list) == 1){
                $list = array($list['item']);
            }
            echo json_encode($list);
            exit;
        }
        echo "";exit;
        //print_r($list);

    }
    #添加拜访单
    public function doadd()
    {

        $params = array(
            'fieldname' => array($_REQUEST),
            'userid' => $this->userid
        );
        $result=$this->call('addSalesDaily', $params);

        header("location:index.php?action=mycrm");
        exit;

    }
    
    /**
     * des加密
     * @param unknown $encrypt 原文
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
     * des解密
     * @param unknown $decrypt
     * @return string
     */
    /* public function decrypt($decrypt, $key='sdfesdcf'){
    	$decoded = str_replace(' ','%20',$decrypt);
    	$decoded = base64_decode($decrypt);
    	$mcrypt = MCRYPT_TRIPLEDES;
    	$iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
    	$decrypted = mcrypt_decrypt($mcrypt, $key, $decoded, MCRYPT_MODE_ECB, $iv);
    	return $decrypted;
    } */
    
    public function https_request($url, $data = null){
    	$this->_logs(array("发送到T云服务端的url请求", $url));
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
    	$this->_logs(array("返回处理结果：", $output));
    	curl_close($curl);
    	return $output;
    }
    /**
     * T-YUN升级
     */
    public function tyunupgrade(){
        $token='tyunupgrade'.$this->userid;
        $this->setAddToken($token);
        $params = array(
            'userid'			=> $this->userid
        );
        $list = $this->call('findParentDepartment', $params);
        $upDownProduct = $this->call('getTyunProductDownUp', $params);

        $arr1 = array(
            'H1::H2::H3::H72::H4'=>10567,
            'H1::H2::H3::H72::H7'=>10568,
            'H1::H2::H3::H72::H5'=>10582,
            'H1::H2::H3::H72::H8'=>10569,
            'H1::H2::H102'=>10574,
			'H1::H2::H3::H24'=>10573,
            'H1::H2::H3::H133'=>10566,
            'H1::H2::H3::H125'=>10575,
            'H1::H2::H3::H160'=>10598,
            'H1::H2::H3::H156'=>10599,
            'H1::H2::H3::H175'=>10600,
            'H1::H2::H3::H22'=>10487,
            'H1::H2::H3::H72::H310'=>10769,
        );
        $agents = 0;
        $userarray=array(43);
        foreach ($arr1 as $k=>$v){
            if(in_array($this->userid,$userarray)){
                $agents = 10567;
                break;
            }
            if(false !== strpos($list[0]['parentdepartment'], $k)){
                $agents = $v;
                break;
            }
        }
        $array=array();
        foreach($upDownProduct[0] as $updown){
            $array[$updown['tyundownup']][$updown['sproduct']][]=array('product'=>$updown['dproduct'],'name'=>$updown['dlabel']);
        }
        $this->_logs(array("agents：", $agents));
        $this->smarty->assign('upgrade', json_encode($array['upgrade'],JSON_UNESCAPED_UNICODE));
        $this->smarty->assign('downgrade', json_encode($array['downgrade'],JSON_UNESCAPED_UNICODE));
        $this->smarty->assign('agents', $agents);
        $this->smarty->assign('userid', $this->userid);
        $this->smarty->display('ActivationCode/upgrade.html');
    }

    /**
     * Tyun续费
     */
    public function doUpgradeARenew(){
        $token='tyunupgrade'.$this->userid;
        if($this->getAddToken($token)){
            echo json_encode(array('success'=>2, 'msg'=>'操作过期!'));
            exit;
        }
        $islist = $this->call('checkUpgradeAndRenew', array('fieldname'=>array('contractid'=>$_REQUEST['contractid'],'classid'=>$_REQUEST['classid']), 'userid'=>$this->userid));
        $tempmsg=$_REQUEST['classid']=='upgrade'?'升级':'续费';
        if($islist[0]==1){
            echo json_encode(array('success'=>2, 'msg'=>'该合同已'.$tempmsg.',若有问题请登陆PC端查看!'));
            exit;
        }
        $myData = array();
        $acData = array();
        $myData['NewContractCode'] =$acData['contractcode'] = $_REQUEST['contractname_display'];//合同编号
        $myData['OldContractCode'] =$acData['originalcontractcode'] = $_REQUEST['scontract'];//原合同编号
        //$myData['SecretKeyID'] =$acData['activecode'] = $_REQUEST['activecode'];//合同激活码
        $myData['LoginName'] =$acData['usercode'] = $_REQUEST['LoginName'];//用户名
        $myData['RenewYear'] =$myData['Year'] =$acData['productlife'] = $_REQUEST['productlife'];//年限
        $myData['NewProductID'] =$acData['productid'] = $_REQUEST['productid'];//新产品编号
        $myData['OldProductID'] =$acData['oldproductid'] = $_REQUEST['oldproductid'];//老产品编号
        $myData['upgradeDate'] =$acData['upgradeDate'] = $_REQUEST['upgradedate'];//升级日期
        //$acData['originalcontractid'] = $_REQUEST['scontractid'];//原合同ID
        $acData['CompanyName'] = $_REQUEST['customername_display'];//客户名称
        $acData['SalesName'] = $_SESSION['customer_name']?$_SESSION['customer_name']:'';//销售人员
        $acData['customerid'] = $_REQUEST['customerid'];//客户id
        $acData['contractid'] = $_REQUEST['contractid'];//合同编号ID
        $acData['classid'] = $_REQUEST['classid'];//操作类型
        if($_REQUEST['classid']=='upgrade')
        {//升级
            $url=$this->urlUserUpgrade;
            //$url = "http://apityun.71360.com/api/cms/UserUpgrade";
        }else{
            //续费
            $acData['productid'] = $_REQUEST['oldproductid'];
            $url=$this->urlUserRenew;
            //$url="http://apityun.71360.com/api/cms/UserRenew";
        }
        $tempData['data'] = $this->encrypt(json_encode($myData));
        $postData = http_build_query($tempData);
        $res = $this->https_request($url, $postData);
        $result = json_decode($res, true);
        $result = json_decode($result, true);

        if($result['success']){
            $acData['resultmsg'] =$res;//操作类型
            $acData['salesname'] =  $_SESSION['customer_name']?$_SESSION['customer_name']:'';//销售人员
            $acData['salesphone'] = $_SESSION['phone_mobile']?$_SESSION['phone_mobile']:'';//销售人员联系方式,新加的
            $ActivationCodeData = array();
            $ActivationCodeData['module'] = 'ActivationCode';
            $ActivationCodeData['action'] = 'Save';
            $ActivationCodeData = array_merge($ActivationCodeData, $acData);
            $nowTime = date("Y-m-d H:i", time());
            $list = $this->call('upgradeAndRenew', array('fieldname'=>array('ActivationCodeData'=>$ActivationCodeData, 'acData'=>$acData), 'userid'=>$this->userid));
            if(!empty($list[0])){
                $arrMails = array(
                    10567 => 'ingram.ye@71360.com',
                    10568 => 'weibo.wang@71360.com',
                    10582 => 'anne.li@71360.com',
                    10569 => 'daveth.chen@71360.com',
                    10574 => 'andy.ma@71360.com',
                    10566 => 'fei.gu@71360.com',
                    10575 => 'king.yu@71360.com',
                    10598 => 'martin.que@71360.com',
                    10599 => 'david.zhang@71360.com',
                    10600 => 'summer.yu@71360.com',
                    10487 => 'Bill.ma@71360.com',
                    10769=>'wilson.xiao@71360.com',
                );
                $agents = $acData['agents'];
                $Subject = 'T云激活码'.$tempmsg.'通知';
                $body = "员工：last_name<br>部门：department<br>客户：customername<br>{$tempmsg}版本：productName<br>{$tempmsg}年限：productlife 年<br>{$tempmsg}时间：nowTime<br>";
                $params = array(
                    'fieldname' => array(
                        'agent_mail' => $arrMails[$agents]?$arrMails[$agents]:'',
                        'customername' => $acData['CompanyName'],
                        'productid' => $acData['productid'],
                        'productlife' => $acData['productlife'],
                        'nowTime' => $nowTime,
                        'mobile' => $acData['mobile'],
                        'Subject'=>$Subject,
                        'body'=>$body
                    ),
                    'userid'			=> $this->userid
                );
                $list = $this->call('sendMailAgent', $params);
                echo json_encode(array('success'=>1, 'msg'=>$result['message']));
            }else{
                echo json_encode(array('success'=>2, 'msg'=>'保存失败'));
            }
        }else{
            echo json_encode(array('success'=>0, 'msg'=>$result['message']));
        }
        exit();
    }
    public function getUserMsg()
    {
        $LoginName=$_REQUEST['LoginName'];
        $myData=['LoginName'=>$LoginName];
        $tempData['data'] = $this->encrypt(json_encode($myData));

        $postData = http_build_query($tempData);//传参数
        $res = $this->https_request($this->urlUserGet, $postData);
        $result = json_decode($res, true);
        $result = json_decode($result, true);
       
        echo json_encode($result);
    }
}
