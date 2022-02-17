<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Home_BasicAjax_Action extends Vtiger_Action_Controller {
	
	function __construct(){
		parent::__construct();
		$this->exposeMethod('getListUser');
        $this->exposeMethod('getList');
        $this->exposeMethod('searchTyunBuyServiceInfo');
        $this->exposeMethod('searchTyunUpgradeProduct');
        $this->exposeMethod('searchTyunBuyServiceInfo1');
        $this->exposeMethod('searchTyunBuyServiceInfo2');
        $this->exposeMethod('searchTyunUpgradeProduct2');
        $this->exposeMethod('getPrice2');
        $this->exposeMethod('getPrice1');
        $this->exposeMethod('getAllCategory');
	    $this->exposeMethod('replyRejectServiceContract');
        $this->exposeMethod('getNotices');
	}
	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
	}
    public function getListUser(Vtiger_Request $request){
        $username=$request->get('term');
        $db=PearDatabase::getInstance();
        $sql="select id,last_name,user_name from vtiger_users where `status`='Active' and  (user_name like ? or last_name like ? ) limit 10";

        $result = $db->pquery($sql,array('%'.$username.'%','%'.$username.'%'));
        $temp=array();
        while($row=$db->fetch_array($result)){
            $temp[$row['last_name']]=$row['id'];

        }
        $_SESSION['referenceField']=$temp;
        echo json_encode(array_keys($temp));
        //$response = new Vtiger_Response();
        //$response->setResult($temp);
        //$response->emit();
    }
    public function getList(Vtiger_Request $request){
        $id=$request->get('record');
        $srcmodule=$request->get('src_module');
        $username=$request->get('term');
        $field=$request->get('field');
        if($field=='account_id'){
            $field='accountid';
        }
        $cache=Vtiger_Cache::get('global','fieldsmodulereferer');
		$adb = PearDatabase::getInstance();
        $searchreturn=array();
        if(empty($cache)){
            
            $sql='SELECT vtiger_field.tabid, vtiger_field.columnname, vtiger_field.fieldtype, vtiger_fieldmodulerel.relmodule, vtiger_entityname.tablename AS ntablename, vtiger_entityname.fieldname AS nfieldname, vtiger_entityname.entityidfield, vtiger_tab.`name` FROM vtiger_field LEFT JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid LEFT JOIN vtiger_entityname ON vtiger_entityname.modulename = vtiger_fieldmodulerel.relmodule LEFT JOIN vtiger_tab ON vtiger_field.tabid = vtiger_tab.tabid WHERE vtiger_field.presence IN (0, 2) AND vtiger_field.displaytype != 4 AND vtiger_field.displaytype != 0 AND vtiger_fieldmodulerel.relmodule is not NULL';
            $result=$adb->pquery($sql,array());
            $rows=$adb->num_rows($result);
            if($rows>0) {
                //$temp = array();

                while ($row = $adb->fetch_row($result)) {
                    $cache[$row['columnname']] = $row;
                }
                Vtiger_Cache::set('global','fieldsmodulereferer',$cache);
            }
        }

        if(!empty($cache)){
            $fieldrow=$cache[$field];
            if(isset($fieldrow)){
                $sql="select ".$fieldrow['nfieldname'].",".$fieldrow['entityidfield']." from ".$fieldrow['ntablename']." where ".$fieldrow['nfieldname']." like '%".$username."%' limit 10";
                
				$searchresult=$adb->pquery($sql,array());
                while($searchrow=$adb->fetch_row($searchresult)){
                    $searchreturn[$searchrow[$fieldrow['nfieldname']].'##'.$searchrow[$fieldrow['entityidfield']]]=$searchrow[$fieldrow['nfieldname']].'##'.$searchrow[$fieldrow['entityidfield']];
                }
            }

        }
        echo json_encode(array_keys($searchreturn));
    }

    /**
     * 通过T云账号获取原合同和产品信息
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function searchTyunBuyServiceInfo(Vtiger_Request $request){
	    global $adb;
        $tyun_account = $request->get('tyun_account');
        $query="SELECT 
            M.contractid,
			M.contractname,
			(SELECT SUM(total) FROM vtiger_servicecontracts WHERE servicecontractsid IN (SELECT T.contractid FROM vtiger_activationcode T WHERE T.status IN(0,1) AND T.usercode=?)) AS total,
			M.productid,
			vtiger_products.productname,
			vtiger_products.unit_price,
			(SELECT MM.activedate FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.classtype='buy' AND MM.usercode=M.usercode  LIMIT 1) AS activedate,
			(SELECT MAX(str_to_date(REPLACE(MM.expiredate,'/','-'),'%Y-%m-%d')) FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.usercode=M.usercode) AS expiredate,
			IFNULL(P.customerid,M.customerid) AS customerid,
			IFNULL(P.customername,M.customername) AS customername
			FROM vtiger_activationcode M
			LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid)
			LEFT JOIN vtiger_products ON(vtiger_products.tyunproductid=M.productid)
			WHERE M.status IN(0,1) AND M.classtype IN('buy','upgrade','degrade')
			AND M.usercode=? AND M.comeformtyun=0 ORDER BY M.receivetime DESC LIMIT 1";
        $listResult = $adb->pquery($query, array($tyun_account,$tyun_account));
        $rowdata=$adb->query_result_rowdata($listResult);
        $response = new Vtiger_Response();
        $response->setResult($rowdata);
        $response->emit();
    }
    /**
     * 通过产品查询T云升级产品
     * @param Vtiger_Request $request
     */
    function searchTyunUpgradeProduct(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ActivationCode');
        $p_productid = $request->get('p_productid');
        $request = new Vtiger_Request();
        $request->set("p_productid",$p_productid);
        $request->set("is_getname",1);
        $return = $recordModel->searchTyunUpgradeProduct($request);
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }
    /**
     * 通过T云账号获取原合同和产品信息
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function searchTyunBuyServiceInfo1(Vtiger_Request $request){
        global $adb,$tyunweburl,$sault;
        $tyun_account = $request->get('tyun_account');
        $query="SELECT
            M.contractid,
			M.contractname,
			M.productid,
            (SELECT p.expiredate FROM vtiger_activationcode p WHERE p.comeformtyun=1 AND M.usercode=p.usercode AND p.productid>0 ORDER BY p.expiredate desc LIMIT 1) AS expiredate,
			M.productname,
            (SELECT p.contractprice FROM vtiger_activationcode p WHERE p.comeformtyun=1 AND M.usercode=p.usercode AND p.productid>0 ORDER BY p.expiredate desc LIMIT 1) AS unit_price,
			M.customerid AS customerid,
			M.customername AS customername,
            M.usercodeid
			FROM vtiger_activationcode M
			WHERE M.status IN(0,1) 
/*			  AND M.classtype IN('buy','upgrade','degrade','cupgrade','crenew','cdegrade')*/
			AND M.usercode=? ORDER BY M.receivetime DESC LIMIT 1";
        $listResult = $adb->pquery($query, array($tyun_account));

        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));

        if($adb->num_rows($listResult)){
            $rowdata=$adb->query_result_rowdata($listResult,0);
            $userID = $rowdata['usercodeid'];//用户ID
            if(!$userID){
                //向账户中心获取对应的userid
                $getAccountByLoginName = $tyunweburl.'api/app/tcloud-account/v1.0.0/account/getAccountByLoginName';
                $res = $this->https_request($getAccountByLoginName.'?loginName='.$tyun_account, array('loginName'=>$tyun_account),$curlset);
                $res = json_decode($res,true);
                $this->_logs(array('getAccountByLoginNameres','url'=>$getAccountByLoginName.'?loginName='.$tyun_account,$res));
                if($res['success'] && $res['data']){
                    $userID = $res['data']['id'];
                    $rowdata['usercodeid'] = $res['data']['id'];
                }else{
                    echo json_encode(array('success' => false, 'message' => '没有可升级的产品4'));
                    exit;
                }
            }

            $categoryID = $request->get('classtyperenew');//产品分类编号(0国内版 1一带一路 2云市场 3数字媒体)
            $type = 0;//类型(0，全部 1，单品 2 套餐)
            $postData=json_encode(array("userID"=>$userID,'categoryID'=>$categoryID,"type"=>$type,"pageIndex"=>1,"pageSize"=>100));
            $getAllProductsurl=$tyunweburl.'api/micro/order-basic/v1.0.0/api/User_Product/GetUserPackageAndProductPageData';

            $res = $this->https_request($getAllProductsurl, $postData,$curlset);
            $res_jsonData=json_decode($res,true);
            if($res_jsonData['code']=='200') {
                $tempData = array();
                if (empty($res_jsonData['data'])) {
                    echo json_encode(array('success' => false, 'message' => '没有可升级的产品'));
                    exit;
                }
                $tdata=array();
                foreach($res_jsonData['data'] as $value) {
                    if ($value['CanUpgrade']) {
                        $userPackageUpgradeInfo = $tyunweburl . "api/micro/order-basic/v1.0.0/api/Package/GetUserPackageUpgradeInfo";
                        $postData = json_encode(array("userID" => $userID, 'userPackageID' => $value['ProductRecordID']));
                        $time = time() . '123';
                        $sault1 = $time . $sault;
                        $token = md5($sault1);
                        $curlset = array(CURLOPT_HTTPHEADER => array(
                            "Content-Type:application/json",
                            "S-Request-Token:" . $token,
                            "S-Request-Time:" . $time));
                        $res = $this->https_request($userPackageUpgradeInfo, $postData, $curlset);

                        $this->_logs(array($userPackageUpgradeInfo . '，返回结果：' . $res));
                        $jsonData = json_decode($res, true);
                        if ($jsonData['success']) {
                            $tdata=$jsonData['data'][0]['upgradePackageList'];
                        }
                    }else {
                        echo json_encode(array('success' => false, 'message' => '没有可升级的产品1'));
                        exit;
                    }
                }
                if(empty($tdata)){
                    echo json_encode(array('success' => false, 'message' => '没有可升级的产品2'));
                    exit;
                }
                $returndata=array_merge($rowdata,array('plist'=>$tdata));
                $returndata['success']=true;
                echo json_encode($returndata);
            }
        }else{
            echo json_encode(array('success' => false, 'message' => '没有可升级的产品3'));
            exit;
        }
    }
    /**
     * 通过产品查询T云升级产品
     * @param Vtiger_Request $request
     */
    function searchTyunUpgradeProduct1(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ActivationCode');
        $p_productid = $request->get('p_productid');
        $request = new Vtiger_Request();
        $request->set("p_productid",$p_productid);
        $request->set("is_getname",1);
        $return = $recordModel->searchTyunUpgradeProduct($request);
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }
    public function getPrice1(){
        global $tyunweburl,$sault;
        $userID = $_REQUEST['tyunusercode'];//用户ID
        $productid=$_REQUEST['productid'];
        $userPackageUpgradeMoney=$tyunweburl."api/micro/order-basic/v1.0.0/api/Package/GetUserPackageUpgradeMoney";
        $buyyear=$_REQUEST['buyyear'];
        $type = 0;//类型0 直销 1 渠道
        $postData=json_encode(array("agentType"=>$type,'userID'=>$userID,"packageID"=>$productid,"detailBuyTerm"=>$buyyear,"discount"=>1));
        $this->_logs(array($tyunweburl.'，返回结果：'.$postData));
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
        "Content-Type:application/json",
        "S-Request-Token:".$token,
        "S-Request-Time:".$time));
        echo $this->https_request($userPackageUpgradeMoney, $postData,$curlset);
    }
    /**
     * 通过T云账号获取原合同和产品信息
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function getPrice2(Vtiger_Request $request){
        global $tyunClienturl,$tyunweburl,$adb,$sault;
        $GetSecretKeySurplusMoney=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Package/GetPackageMoneyByClient';
        $userID = $request->get('usercode');
        $OldProductID = $request->get('productid');
        $buyyear = $request->get('buyyear');
        $productid = $request->get('p_productid');

        $SecretKeyID=$request->get('activecode');
        $ContractCode=$request->get('contractno');
        $OldCloseDate=$request->get('oldclosedate');

        $postData=json_encode(array('LoginName'=>$userID,
            'SecretKeyID'=>$SecretKeyID,
            'ContractCode'=>$ContractCode,
            'OldProductID'=>$OldProductID,
            'OldCloseDate'=>$OldCloseDate,
            'UpgradeProductID'=>$productid,
            'UpgradeYear'=>(int)$buyyear,
            'AddDate'=>date('Y-m-d')));
        $tempData['data'] = $this->encrypt($postData);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json"
        ));
        $GetSecretKeySurplusMoneyClient=$tyunClienturl.'api/CRM/GetSecretKeySurplusMoney';
        $this->_logs(array($GetSecretKeySurplusMoneyClient,$postData,$tempData));
        $res = $this->https_request($GetSecretKeySurplusMoneyClient, json_encode($tempData),$curlset);
        //$res ='"{\\r\\n  \\"success\\": true,\\r\\n  \\"message\\": \\"操作成功\\",\\r\\n  \\"oldSurplusMoney\\": 854.7945205479452054794520544\\r\\n}"';
        $res=str_replace(array('\\r','\\n'),'',$res);
        $this->_logs(array($res));
        $res=str_replace('\"','"',$res);
        $res=preg_replace('/^"|"$/','',$res);
        $data=json_decode($res,true);
        if($data['success']) {
            $query='SELECT left(expiredate,10) as expiredate,contractid,productlife,classtype,left(receivetime,10) AS receivetime,left(startdate,10) AS startdate FROM vtiger_activationcode WHERE usercode=? AND `status` in(0,1) AND comeformtyun=0 AND left(expiredate,10)>?';
            $result=$adb->pquery($query,array($userID,date('Y-m-d')));
            $SurplusMoney=0;
            if($adb->num_rows($result)){
                $currenttime=strtotime(date('Y-m-d'));
                while($row=$adb->fetch_array($result)){
                    $query='SELECT vtiger_servicecontracts.total FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE deleted=0 AND vtiger_servicecontracts.servicecontractsid=? and vtiger_servicecontracts.modulestatus=\'c_complete\'';
                    $serResult=$adb->pquery($query,array($row['contractid']));
                    if($adb->num_rows($serResult)){
                        $resultData=$adb->raw_query_result_rowdata($serResult,0);
                        if(in_array($row['classtype'],array('buy','','degrade','upgrade'))){
                            $starttime=strtotime($row['receivetime']);
                        }else{
                            $starttime=strtotime($row['startdate']);
                        }
                        if($currenttime<=$starttime){
                            $SurplusMoney=bcadd($SurplusMoney,$resultData['total'],6);
                        }else{
                            $starttime=strtotime($row['expiredate']);
                            if($starttime>$currenttime){
                                $starttime=$starttime-$currenttime;
                                $starttime=$starttime/(24*60*60);
                                $productlife=$row['productlife']*365;
                                $SurplusMoney=bcadd($SurplusMoney,bcmul(bcdiv($resultData['total'],$productlife,6),$starttime,6),6);
                            }
                        }
                    }
                }
            }
            $SurplusMoney=round($SurplusMoney,2);
            $postData=json_encode(array('BuyTerm'=>$buyyear,'Discount'=>1,'agentType'=>0,'ProductType'=>5,'surplusMoney'=>$SurplusMoney,'clientPackageID'=>$productid,'oldSurplusMoney'=>$data['oldSurplusMoney']));
            $this->_logs($GetSecretKeySurplusMoney.$postData);

            $time=time().'123';
            $sault1=$time.$sault;
            $token=md5($sault1);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_request($GetSecretKeySurplusMoney, $postData,$curlset);
            $this->_logs($res);
            echo $res;
            exit;
        }
        echo $res;
    }
    /**
     * 通过产品查询T云升级产品
     * @param Vtiger_Request $request
     */
    function searchTyunUpgradeProduct2(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
        $p_productid = $request->get('p_productid');
        $request = new Vtiger_Request();
        $request->set("p_productid",$p_productid);
        $request->set("is_getname",1);
        $return = $recordModel->searchTyunUpgradeProduct($request);
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }
    public function searchTyunBuyServiceInfo2(Vtiger_Request $request){
        global $adb;
        $tyun_account = $request->get('tyun_account');
        $query="SELECT 
            M.contractid,
			M.contractname,
			IFNULL(P.activecode,M.activecode) AS activecode,
			(SELECT SUM(total) FROM vtiger_servicecontracts WHERE servicecontractsid IN (SELECT T.contractid FROM vtiger_activationcode T WHERE T.status IN(0,1) AND T.usercode=?)) AS total,
			M.productid,
			vtiger_products.productname,
			vtiger_products.unit_price,
			(SELECT MM.activedate FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.classtype='buy' AND MM.usercode=M.usercode  LIMIT 1) AS activedate,
			(SELECT MAX(str_to_date(REPLACE(MM.expiredate,'/','-'),'%Y-%m-%d')) FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.usercode=M.usercode) AS expiredate,
			IFNULL(P.customerid,M.customerid) AS customerid,
			IFNULL(P.customername,M.customername) AS customername
			FROM vtiger_activationcode M
			LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid)
			LEFT JOIN vtiger_products ON(vtiger_products.tyunproductid=M.productid)
			WHERE M.status IN(0,1) AND M.classtype IN('buy','upgrade','degrade')
			AND M.usercode=? ORDER BY M.receivetime DESC LIMIT 1";
        $listResult = $adb->pquery($query, array($tyun_account,$tyun_account));
        $rowdata=$adb->query_result_rowdata($listResult);
        $response = new Vtiger_Response();
        $response->setResult($rowdata);
        $response->emit();
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
    public function https_request($url, $data = null,$curlset=array()){
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
        curl_close($curl);
        return $output;
    }
    public function _logs($data, $file = 'tweblog_'){
        $year	= date("Y");
        $month	= date("m");
        $dir	= './logs/tyun/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }
    public function getAllCategory(){
        global $tyunweburl,$sault;
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $AllCategory=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Category/AllCategory';
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_request($AllCategory, array(),$curlset);
        echo $res;
        exit;
    }
    
      /**
     * 回应处理被打回的合同
     *
     * @param Vtiger_Request $request
     */
    function replyRejectServiceContract(Vtiger_Request $request){
        $servicecontract_relationid = $request->get('id');
        $servicecontractsid = $request->get('servicecontractsid');
        $status = $request->get('status');
        $relationid =$request->get('relationid');
        $description = $request->get('description');
        if(!$servicecontract_relationid || !$status || !$description){
            $return = array('success'=>false,'message'=>'必填内容不能为空');
            $response = new Vtiger_Response();
            $response->setResult($return);
            $response->emit();
            exit;
        }
        global $current_user,$kefu_updatecontract_result;
        //向客服系统同步数据
        $data = array(
            'contractId'=>intval($servicecontractsid),
            'processBy'=>intval($current_user->id),
            'processDesc'=>$description,
            'processResult'=>intval($status),
            'relationid'=>intval($relationid)
        );
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json"
        ));
        //向客服系统同步
        $result = $this->https_request($kefu_updatecontract_result,json_encode($data),$curlset);
        $result = json_decode($result,true);
        $this->_logs(array('url'=>$kefu_updatecontract_result,'rparams'=>$data,'result'=>$result));
        if($result['resultCode']==200){
            $time = date('Y-m-d H:i:s',time());
            $adb = PearDatabase::getInstance();
            $sql = "update vtiger_servicecontract_relation set status=?,description=?,replyid=?,replytime=? where id=?";
            $adb->pquery($sql,array($status,$description,$current_user->id,$time,$servicecontract_relationid));
            $response = new Vtiger_Response();
            $response->setResult(array('success'=>true,'message'=>'处理成功'));
            $response->emit();
            exit;
        }

        $response = new Vtiger_Response();
        $response->setResult(array('success'=>false,'message'=>$result['message']));
        $response->emit();
    }

    function getNotices(Vtiger_Request $request)
    {
        $recordModule = Vtiger_Module_Model::getInstance('WorkFlowCheck');
        //系统消息
        $messageLink = [];
        //统计有多少条要审核的信息
        $recordcount = $recordModule->getConfirmation();
        if ($recordcount > 0) {
            $messageLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_MESSAGE_CONFIR'),
                'linkurl' => 'index.php?module=WorkFlowCheck&view=List'
            ];
        }
        //统计有多少条24小时待跟进拜访单
        $recordcount = $recordModule->getVisitingOrderFollowup();
        if ($recordcount > 0) {
            $messageLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_MESSAGE_ORDER'),
                'linkurl' => 'index.php?module=VisitingOrder&view=List&public=FollowUp'
            ];
        }
        //统计未写工作日报的人数
        $recordcount = $recordModule->getNoWrite();
        if ($recordcount > 0) {
            $messageLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_MESSAGE_NOWRITER'),
                'linkurl' => 'index.php?module=WorkSummarize&view=List&filter=nowrite'
            ];
        }
        //有多少要回复的工作日报记录数
        $recordcount = $recordModule->getReplynum();
        if ($recordcount > 0) {
            $messageLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_MESSAGE_REPLYNUM'),
                'linkurl' => 'index.php?module=WorkSummarize&view=List&filter=reply'
            ];
        }
        //超过24小时未审核信息
        $recordcount = $recordModule->getConfirmation('outnumberday');
        if ($recordcount > 0) {
            $messageLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_MESSAGE_OUTNUMBERDAY'),
                'linkurl' => 'index.php?module=WorkFlowCheck&view=List&public=outnumberday'
            ];
        }
        //全部未跟进客服的信息
        $recordcount = $recordModule->getSevenCustomer();
        if ($recordcount > 0) {
            $messageLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_MESSAGE_CUSTOMER'),
                'linkurl' => 'index.php?module=ServiceComments&view=List&public=allnofollowday'
            ];
        }
        //统计当前打回工单的记录条数
        $recordcount = WorkFlowCheck_Confirmation_Action :: getRefuse();
        if ($recordcount > 0) {
            $messageLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_MESSAGE_REFUSE'),
                'linkurl' => 'index.php?module=SalesOrder&view=List&public=refuse'
            ];
        }
        //统计当前没有合同的回款
        $recordcount = $recordModule->get_noservice_receivepayment();
        if ($recordcount > 0) {
            $messageLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_MESSAGE_NOSERVICE_RECEIVE'),
                'linkurl' => 'index.php?module=ReceivedPayments&view=List&filter=noservice'
            ];
        }
        // 提醒
        $remindLink = [];
        $recordcount = JobAlerts_Record_Model::getReminderResultCount('new');
        if ($recordcount > 0) {
            $remindLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_REMINDER_NEW'),
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=new'
            ];
        }
        $recordcount = JobAlerts_Record_Model::getReminderResultCount('wait');
        if ($recordcount > 0) {
            $remindLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_REMINDER_WAIT'),
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=wait'
            ];
        }
        $recordcount = JobAlerts_Record_Model::getReminderResultCount('finish');
        if ($recordcount > 0) {
            $remindLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_REMINDER_FINISH'),
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=finish',
            ];
        }
        $recordcount = JobAlerts_Record_Model::getReminderResultCount('myreminder');
        if ($recordcount > 0) {
            $remindLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_REMINDER_MY_REMINDER'),
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=myreminder',
            ];
        }
        $recordcount = JobAlerts_Record_Model::getReminderResultCount('relation');
        if ($recordcount > 0) {
            $remindLink[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_REMINDER_RELATION'),
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=relation',
            ];
        }
        // 提醒已读未读
        $remindLinkReadState = [];
        //未读未到期提醒
        $recordcount = JobAlerts_Record_Model::getReminderResultCountReadState('new');
        if ($recordcount > 0) {
            $remindLinkReadState[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_REMINDER_NEW_NOREAD'),
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=new',
            ];
        }
        //'LBL_CRM_REMINDER_WAIT_NOREAD'=>'未读待处理提醒',
        $recordcount = JobAlerts_Record_Model::getReminderResultCountReadState('wait');
        if ($recordcount > 0) {
            $remindLinkReadState[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_REMINDER_WAIT_NOREAD'),
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=wait',
            ];
        }
        //'LBL_CRM_REMINDER_MY_REMINDER_NOREAD'=>'未读待处理全部提醒',
        $recordcount = JobAlerts_Record_Model::getReminderResultCountReadState('myreminder');
        if ($recordcount > 0) {
            $remindLinkReadState[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_REMINDER_MY_REMINDER_NOREAD'),
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=myreminder'
            ];
        }
        // 'LBL_CRM_REMINDER_RELATION_NOREAD'=>'未读全部提醒',
        $recordcount = JobAlerts_Record_Model::getReminderResultCountReadState('relation');
        if ($recordcount > 0) {
            $remindLinkReadState[] = [
                'recordcount'=>$recordcount,
                'linklabel' => vtranslate('LBL_CRM_REMINDER_RELATION_NOREAD'),
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=relation',
            ];
        }
        $data = [
            'messageLink' => $messageLink,
            'remindLink' => $remindLink,
            'remindLinkReadState' => $remindLinkReadState
        ];
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
