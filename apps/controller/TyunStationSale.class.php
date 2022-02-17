<?php
/**
 * 建站服务购买
 * @author gaocl
 *
 */
class TyunStationSale extends baseapp
{
    private $urlUserGet='http://tyunapi.71360.com/api/cms/UserGet';//获取用户名信息
    private $urlUserUpgrade='http://tyunapi.71360.com/api/cms/UserUpgrade';//升级接口
    private $urlUserRenew='http://tyunapi.71360.com/api/cms/UserRenew';//续费接口
    private $GetCloudSiteUser="http://apityun.71360.com/api/CRM/GetCloudSiteUser";
    //建站服务购买
    public function add()
    {
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
    	//$this->smarty->assign('agents', $agents);
    	$this->smarty->assign('agents', $this->getAgents());
    	$this->smarty->assign('userid', $this->userid);
        $this->smarty->display('TyunStationSale/add.html');
    }
    public function getAgents(){
        $params = array(
            'userid'			=> $this->userid
        );
        $list = $this->call('getagentid', $params);
        return $list[0];
    }

    /**
     * 续费页面调用
     */
    public function renew(){
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
        //$this->smarty->assign('agents', $agents);
        $this->smarty->assign('agents', $this->getAgents());
        $this->smarty->assign('userid', $this->userid);
        $this->smarty->display('TyunStationSale/stationrenew.html');
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
		$url = "http://tyunapi.71360.com/api/cms/GetMobileCode";
    	//$myData = array('Mobile'=>$mobile);
    	//$tempData = json_encode($myData);
    	$data['data'] = $this->encrypt($mobile);
    	$postData = http_build_query($data);//传参数
    	//print_r($data);
    	/* echo "参数:" . $mobile."<br>";
    	echo "加密后";echo $postData."<br>"; */
    	$res = $this->https_request($url, $postData);
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
     * 保存购买信息
     */
    public function ajaxSaveTyunStationSale(){
        $pramData = array();

        //验证码check
    	$authCode = $_REQUEST['auth_code'];//验证码
    	$mobileCode = $_SESSION['setMobileCodeVerify'];//获取验证码
    	if($authCode <> $mobileCode){
    		echo json_encode(array('success'=>0, 'msg'=>'手机验证码错误'));
    		exit();
    	}

        $nowTime = date("Y-m-d H:i", time());
        $pramData['contractid'] = $_REQUEST['contractid'];//合同ID
        $pramData['accountid'] = $_REQUEST['customerid'];//客户id
        $pramData['companyname'] = $_REQUEST['customername_display'];//客户名称
        $pramData['agentcode'] = $_REQUEST['agents'];//代理商标识码
        //$pramData['servicetype'] = $_REQUEST['servicetype'];//服务类型 1小程序建站 2云网站制作
        //$pramData['buycount'] = $_REQUEST['buycount'];//购买数量
        //$pramData['buyyear'] = $_REQUEST['buyyear'];//使用年限
        $pramData['signaddress'] = $_REQUEST['sign_address'];//签到地址
        //$pramData['signdate'] = $nowTime;//签单时间
        $pramData['custphone'] = $_REQUEST['mobile'];//客户手机号码
        //$pramData['status'] = 1;//状态 传1 表示合同有效
        $pramData['contractcode'] = $_REQUEST['contractname_display'];//合同编号
        //$pramData['loginname'] = '';//客户账号
        //$pramData['opendate'] = null;//开通时间
        //$pramData['finnishdate'] = null;// 完成时间
        $pramData['salesname'] = $_SESSION['last_name']?$_SESSION['last_name']:'';//销售人员
        $pramData['salesphone'] = $_SESSION['phone_mobile']?$_SESSION['phone_mobile']:'';//销售人员联系方式
        $pramData['serviceloginname'] = '';//客服人员crm登陆账号
        $pramData['createdid'] = $this->userid;//创建者id

        //1微信小程序 2 PC 3 移动 5,百度小程序
        //[{"servicetype":"1","count":"1","year":"1"},{"servicetype":"2","count":"0","yaer":"0"},{"servicetype":"3","count":"0","yaer":"0"}],  1小程序 2PC 3移动 ，每次必传三个
        $arr_serviceinfo = array(array('servicetype'=>$_REQUEST['servicetype1'],'count'=>$_REQUEST['count1'],'year'=>$_REQUEST['year1']),
            array('servicetype'=>$_REQUEST['servicetype2'],'count'=>$_REQUEST['count2'],'year'=>$_REQUEST['year2']),
            array('servicetype'=>$_REQUEST['servicetype3'],'count'=>$_REQUEST['count3'],'year'=>$_REQUEST['year3']),
            array('servicetype'=>$_REQUEST['servicetype5'],'count'=>$_REQUEST['count5'],'year'=>$_REQUEST['year5']),
            array('servicetype'=>$_REQUEST['servicetype6'],'count'=>$_REQUEST['count6'],'year'=>$_REQUEST['year6'])
            );
        $pramData['serviceinfo'] = json_encode($arr_serviceinfo);
        //计算总数量
        $count1 = $_REQUEST['count1'];
        $count2 = $_REQUEST['count2'];
        $count3 = $_REQUEST['count3'];
        $count5 = $_REQUEST['count5'];
        $count6 = $_REQUEST['count6'];
        $pramData['servicecount'] = $count1*1+$count2*1+$count3*1+$count5*1+$count6*1;

        $this->_logs(array('建站服务购买保存数据：', $pramData));
        $result = $this->call('saveTyunStationSale', array('fieldname'=>$pramData, 'userid'=>$this->userid));
        if($result[0]['success'] == 1){
            //发送短信
            //【珍岛集团】尊敬的@客户名称,您已成功购买：@服务名字1 购买数量：@数量 购买时长：@年限年,@服务名字2 购买数量：@数量 购买时长：@年限年,感谢您的合作，勿回谢谢！
            $message = "【珍岛集团】尊敬的{$_REQUEST['customername_display']},您已成功购买：";
            $buyContent = "";
            if($count1 > 0){
                $message .= "云建站3.0微信小程序标准建站 购买数量：".$count1." 购买时长：". $_REQUEST['year1']."年 ";
                $buyContent .= "<br><br>购买服务：云建站3.0微信小程序标准建站<br>购买数量：".$count1." 个<br>购买时长：".$_REQUEST['year1']." 年";
            }
            if($count2 > 0){
                $message .= "云建站3.0PC标准建站 购买数量：".$count2." 购买时长：". $_REQUEST['year2']."年 ";
                $buyContent .= "<br><br>购买服务：云建站3.0PC标准建站<br>购买数量：".$count2." 个<br>购买时长：".$_REQUEST['year2']." 年";
            }
            if($count3 > 0){
                $message .= "云建站3.0移动标准建站 购买数量：".$count3." 购买时长：". $_REQUEST['year3']."年 ";
                $buyContent .= "<br><br>购买服务：云建站3.0移动标准建站<br>购买数量：".$count3." 个<br>购买时长：".$_REQUEST['year3']." 年";
            }
            if($count5 > 0){
                $message .= "云建站3.0百度小程序标准建站 购买数量：".$count5." 购买时长：". $_REQUEST['year5']."年 ";
                $buyContent .= "<br><br>购买服务：云建站3.0百度小程序标准建站<br>购买数量：".$count5." 个<br>购买时长：".$_REQUEST['year5']." 年";
            }
            if($count6 > 0){
                $message .= "T云建站独立IP 购买数量：".$count6." 购买时长：". $_REQUEST['year6']."年 ";
                $buyContent .= "<br><br>购买服务：T云建站独立IP<br>购买数量：".$count6." 个<br>购买时长：".$_REQUEST['year6']." 年";
            }
            $message .= " ,感谢您的合作，勿回谢谢！";
            //发送短信
            $this->sendToAccountSMS($message);

            //发送邮件
            $this->_logs(array('购买邮件内容：', $buyContent));
            if(!empty($buyContent)){
                $pramData['buyContent'] = $buyContent;
                //发送购买成功邮件
                $this->_logs(array('建站服务购买邮件发送：', $pramData));
                $result_mail = $this->call('sendMailTyunStationSale', array('fieldname'=>$pramData, 'userid'=>$this->userid));
                //echo json_encode($result_mail);exit();
                $this->_logs(array('返回发送邮件结果：', $result_mail));
            }
       }
        $this->_logs(array('返回list：', $result));
        echo json_encode($result[0]);
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
     * des加密
     * @param unknown $encrypt 原文
     * @return string
     */
    public function encrypt($encrypt, $key='sdfesdcf') {
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
     * 向客户发送购买成功短信
     */
    public function sendToAccountSMS($msgContent){
        $mobile = $_REQUEST['mobile'];
        //线上
        //$url = "http://tyunapi.71360.com/api/cms/CrmSendSMS";
        //本地测试
        $url = "http://api.tyun.71360.com/api/cms/CrmSendSMS";
        //预发布地址
        //$t_url = "http://apityun.71360.com/api/cms/CrmSendSMS";

        $myData = array('Mobile'=>$mobile,'Content'=>$msgContent);
        $tempData = json_encode($myData);
        $this->_logs('短信发送内容：'.$tempData);
        $data['data'] = $this->encrypt($tempData);
        $postData = http_build_query($data);//传参数

        $res = $this->https_request($url, $postData);
        $result = json_decode($res, true);
        $result = json_decode($result, true);
        if($result['success']){
            $this->_logs('建站服务购买短信发送成功');
        }else{
            $this->_logs('建站服务购买短信发送失败,原因:'.$result['message']);
        }
    }
    /**
     *续费订单生成
     */
    public function saveStationRenew(){

        $arr_serviceinfo = array(array('servicetype'=>1,'count'=>0,'year'=>0),
            array('servicetype'=>2,'count'=>0,'year'=>0),
            array('servicetype'=>3,'count'=>0,'year'=>0),
            array('servicetype'=>5,'count'=>0,'year'=>0),
            array('servicetype'=>6,'count'=>0,'year'=>0),
        );
        $result=$this->GetCloudSiteUserData(array('LoginName'=>$_REQUEST['loginname']));
        $message = "【珍岛集团】尊敬的{$_REQUEST['customername_display']},您已成功续费：\n";
        $buyContent = "";
        foreach($result['data']['BuyProduct'] as $value){
            $ServiceType=$value['ServiceType'];
            $arr_serviceinfo[$ServiceType-1]['count']=$value['Count'];
            $arr_serviceinfo[$ServiceType-1]['year']=$_REQUEST['productlife'];
            if($value['Count']>0){
                if($ServiceType==1){
                    $message .= "云建站3.0微信小程序标准建站\n续费数量：".$value['Count']."\n续费时长：". $_REQUEST['productlife']."年\n";
                    $buyContent .= "<br><br>续费服务：云建站3.0小程序标准建站<br>续费数量：".$value['Count']." 个<br>续费时长：".$_REQUEST['productlife']." 年\n";
                }
                if($ServiceType==2){
                    $message .= "云建站3.0PC标准建站\n续费数量：".$value['Count']."\n续费时长：". $_REQUEST['productlife']."年\n";
                    $buyContent .= "<br><br>续费服务：云建站3.0PC标准建站<br>续费数量：".$value['Count']." 个<br>续费时长：".$_REQUEST['productlife']." 年\n";
                }
                if($ServiceType==3){
                    $message .= "云建站3.0移动标准建站\n续费数量：".$value['Count']."\n续费时长：". $_REQUEST['productlife']."年\n";
                    $buyContent .= "<br><br>续费服务：云建站3.0移动标准建站<br>续费数量：".$value['Count']." 个<br>续费时长：".$_REQUEST['productlife']." 年";
                }
                if($ServiceType==5){
                    $message .= "云建站3.0百度小程序标准建站\n续费数量：".$value['Count']."\n续费时长：". $_REQUEST['productlife']."年\n";
                    $buyContent .= "<br><br>续费服务：云建站3.0百度小程序标准建站<br>续费数量：".$value['Count']." 个<br>续费时长：".$_REQUEST['productlife']." 年";
                }
                if($ServiceType==6){
                    $message .= "T云建站独立IP\n续费数量：".$value['Count']."\n续费时长：". $_REQUEST['productlife']."年\n";
                    $buyContent .= "<br><br>续费服务：T云建站独立IP<br>续费数量：".$value['Count']." 个<br>续费时长：".$_REQUEST['productlife']." 年";
                }
            }
            //发送短信

        }
        $message .= "感谢您的合作，勿回谢谢！";
        $serviceinfo_info=json_encode($arr_serviceinfo);
        $inputData=array(
            'contractid'=>$_REQUEST['contractid'],
            'contractamount'=>$_REQUEST['contractamount'],
            'signdate'=>date('Y-m-d H:i:s'),
            'contractcode'=>$_REQUEST['contractname_display'],
            'salesname'=>$_SESSION['last_name'],
            'salesphone'=>$_SESSION['phone_mobile'],
            'serviceloginname'=>"",
            'createdid'=>$this->userid,
            'createdtime'=>date('Y-m-d H:i:s'),
            'productlife'=>$_REQUEST['productlife'],
            'loginname'=>$_REQUEST['loginname'],
            'oldclosedate'=>$_REQUEST['oldexpiredate'],
            'serviceinfo'=>$serviceinfo_info,
            'agentcode'=>$_REQUEST['agents'],
            'buyContent'=>$buyContent,
            'userid'			=> $this->userid
        );
        $t_params = array(
            'fieldname' => $inputData,
            'userid'	=> $this->userid
        );

        $list = $this->call('saveStationRenew', $t_params);
        $arr=$list[0]['item'];
        if($arr['success']==1){
            $_REQUEST['mobile']=$arr['mobile'];
            $this->sendToAccountSMS($message);
        }
        echo json_encode($arr);
    }

    /**
     * CURL,RAW请求
     * @param $url
     * @param null $data
     * @return bool|string
     */
    public function https_requestRaw($url, $data = null){
        $curl = curl_init();
        $this->_logs($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/json;charset=utf-8"));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $this->_logs($output);
        curl_close($curl);
        return $output;
    }
    /**
     * 获取T云账号信息
     */
    public function GetCloudSiteUserData($myData){
        $tempData = json_encode($myData);
        $postData['data'] = urlencode($this->encrypt($tempData));
        $res = $this->https_requestRaw($this->GetCloudSiteUser, json_encode($postData));
        $res=trim($res,'"');
        $res=str_replace('\\','',$res);
        $result = json_decode($res, true);
        return $result;
    }

    /**
     * 获取T云账号信息调用
     */
    public function GetCloudSiteUser(){
        $usercode=$_REQUEST['tyun_account'];
        if(empty($usercode)){
            echo json_encode(array('success'=>false, 'message'=>'账户不能为空'));
            exit;
        }
        $myData = array('LoginName'=>$usercode);
        $result=$this->GetCloudSiteUserData($myData);
        if($result['success']){
            echo json_encode(array('success'=>true, 'message'=>'','buyList'=>$result['data']));
        }else{
            echo json_encode(array('success'=>false, 'message'=>$result['message']));
        }

    }
}
