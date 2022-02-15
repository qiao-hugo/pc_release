<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceContracts_BasicAjax_Action extends Vtiger_Action_Controller {
    private $sequencefour=7;//工作流的节点标识
    private $sequencefive=8;//工作流的节点标识
    private $getTheContract="CREATE_SIGN_ONE";//工作流的节点标识
    private $recoverContract="CREATE_SIGN_TWO";//工作流的节点标识
    private $fangxinqianback='open/contract/back';//合同撤回（撤回并发送）
    private $getTemplates='erp/get_template?';//根据产品编号获取合同模板
    private $getEnclosures='open/contract/get_enclosures?productCode=';
    private $contract_sync='erp/synchronization?contractId=';//合同同步
    private $save_and_replace='erp/save_and_replace';//数据保存并替换接口
    private $backEdit='erp/back_edit';//数据保存并替换接口
    private $erp_edit='erp/edit';//数据编辑接口
    private $Kllcompanycode=array('KLL','WXKLL');//合同状态修改工作流ID
    private $Kllneedle='H283::';//凯丽隆部节门点
    private $WXKLLneedle='H249::';//无锡凯丽隆部节门点
	function __construct() {
		parent::__construct();
		$this->exposeMethod('inquireproduct');
		$this->exposeMethod('receivepay');
		$this->exposeMethod('getcurrencytype');
		$this->exposeMethod('getsmownerid');
		$this->exposeMethod('Received');
		$this->exposeMethod('Returned');
		$this->exposeMethod('NoSignReturned');
		$this->exposeMethod('NotSignInvalid');
		$this->exposeMethod('getservicecontractsinfo');
		$this->exposeMethod('add');
		$this->exposeMethod('addAuditsettings');
		$this->exposeMethod('deleted');
		$this->exposeMethod('setContractsStates');
		$this->exposeMethod('setContractsClose');
		$this->exposeMethod('files_deliver');
		$this->exposeMethod('files_delete');
		$this->exposeMethod('deletedAuditsettings');
		$this->exposeMethod('getSuppliercontracts');
		$this->exposeMethod('savesignimage');
		$this->exposeMethod('changeLead');//更换合同领取人
		$this->exposeMethod('saveChangeLeadSign');//更换合同领取人
		$this->exposeMethod('ContractCancel');//合同作废审请
		$this->exposeMethod('addExceedingNumber');//用户超领添加
		$this->exposeMethod('deletedExceedingNumber');//用户超领删除
                $this->exposeMethod('getCheckInvoice');
                $this->exposeMethod('addDivided');
        $this->exposeMethod('isCanDivided');//判断是否能修改分成
        $this->exposeMethod('addProduct2Code');//设置产品合同编码对应表
        $this->exposeMethod('deletedProduct2Code');//删除产品合同编码对应表
        $this->exposeMethod('getReceivement');//得到合同对应回款
        $this->exposeMethod('searchSupplierContractsNo');//数字威客查找采购合同
        $this->exposeMethod('wkSign');//数字威客

        //电子合同部分
        $this->exposeMethod('getElecContractTable');//获得电子合同对应列表
        $this->exposeMethod('doRevokeSending');//电子合同的撤销打回
        $this->exposeMethod('getElecTPLView');//电子合同的模板视图
        $this->exposeMethod('getElecTPLList');//电子合同的模板列表
        $this->exposeMethod('getContractSync');//电子合同的同步
        $this->exposeMethod('getFangXinQianFormId');//电子合同的FORMID
        $this->exposeMethod('saveAndReplace');//电子合同的保存和替换
        $this->exposeMethod('getPdfView');//电子合同的预览
        $this->exposeMethod('elecErpEdit');//电子编辑
        $this->exposeMethod('elecCommonTovoid');//放心签收废
        $this->exposeMethod('elecResend');//放心签重新签收
        $this->exposeMethod('getReceiverInfo');//电子合同的撤销打回获取接收人信息
        $this->exposeMethod('elecDoCancel');//电子合同的作废
        $this->exposeMethod('erpUpload');//电子合同的附件上传
        $this->exposeMethod('erpContractSet');//电子合同的附件上传
        $this->exposeMethod('erpGetArea');//定制合同签章位置
        $this->exposeMethod('syncProduct2CodeNoTyun');//同步非T云产品编码

        $this->exposeMethod('addPhaseSplit');//添加应收阶段列表
   	    $this->exposeMethod('customizeSendMessage');//发送自定义信息
   	    $this->exposeMethod('tripContractReceived');//三方合同领取
        $this->exposeMethod('changeLeadCheak');//修改合同领取人，验证信息
        $this->exposeMethod('preSaveChangeLeadSign');//修改合同领取人，预保存签名
        $this->exposeMethod('getAccountFile');
        $this->exposeMethod('isNeedZizhiFujian');
        $this->exposeMethod('closedContracts');
        $this->exposeMethod('fileupload');
        $this->exposeMethod('doChangeSmowner');//修改提单人
	$this->exposeMethod('setSpecialContracto');//特殊合同与普通合同切换
	}


	function checkPermission(Vtiger_Request $request) {
		return;
	}

	// 根据供应商获取采购合同
	function getSuppliercontracts(Vtiger_Request $request) {
		$vendorid = $request->get('vendorid');
		$adb = PearDatabase::getInstance();
		$sql = "select suppliercontractsid,contract_no from vtiger_suppliercontracts where vendorid=?";
		$result = $adb->pquery($sql, array($vendorid));
		$row = $adb->num_rows($result);
		$suppliercontracts = array();
		if ($row > 0) {
			while($rawData=$adb->fetch_array($result)) {
				$suppliercontracts[] = $rawData;
        	}
		}
		$response = new Vtiger_Response();
	    $response->setResult($suppliercontracts);
	    $response->emit();
	}
	// 删除附件
	function files_delete(Vtiger_Request $request) {
		$fileid = $request->get('record');
		$servicecontractsid = $request->get('srecorId');
		$srcModule= $request->get('srcModule');
		global $current_user;
        $recordModel=Vtiger_Record_Model::getInstanceById($servicecontractsid,'ServiceContracts');
        $column_fields=$recordModel->entity->column_fields;
        $modulestatus=array('已发放','c_complete','c_recovered');
        $dostatus=array('a_normal','a_exception');
        if(in_array($column_fields['modulestatus'],$dostatus) || (in_array($column_fields['modulestatus'],$modulestatus)&& is_custompowers('contractsFilesDelete')) )
        {
            $sql = "update vtiger_files set deleter=?,delflag=1 where attachmentsid=?";
            if($srcModule=='Vmatefiles'){
                $sql = "update vtiger_vmatefiles set deleter=?,delflag=1 where vmateattachmentsid=?";
            }
            $adb = PearDatabase::getInstance();
            $adb->pquery($sql, array($current_user->id, $fileid));
        }
		$data = array();
		$response = new Vtiger_Response();
	    $response->setResult($data);
	    $response->emit();
	}

	// 附件签收
	function files_deliver(Vtiger_Request $request) {
		$servicecontractsid = $request->get('record');
        $srcModule= $request->get('srcModule');
		global $current_user;

		$sql = "update vtiger_files set deliversuserid=?,filestate=?, delivertime=? where attachmentsid=?";
        if($srcModule=='Vmatefiles'){
            $sql = "update vtiger_vmatefiles set deliversuserid=?,filestate=?, delivertime=? where vmateattachmentsid=?";
        }

		$adb = PearDatabase::getInstance();

		$data = array($current_user->id,'filestate2', date('Y-m-d H:i:s'), $servicecontractsid);
		$adb->pquery($sql, $data);

		$data = array();
		$data['last_name'] = $current_user->last_name."[". $current_user->department ."]";
		$data['delivertime'] = date('Y-m-d H:i:s');

		$response = new Vtiger_Response();
	    $response->setResult($data);
	    $response->emit();
	}

	// 自动关闭状态
	function setContractsClose(Vtiger_Request $request) {
		$servicecontractsid = $request->get('record');
		$contractsStatus = $request->get('status');

		// 判断当前用户是否是 管理员和财务专员
		// 回款拆分的权限
        global $current_user;
        $adb = PearDatabase::getInstance();
        $sql = "select * FROM vtiger_custompowers where custompowerstype='updateContractsClose' LIMIT 1";
        $sel_result = $adb->pquery($sql, array());
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $roles_arr = explode(',', $row['roles']);
            $user_arr = explode(',', $row['user']);

            if (in_array($current_user->current_user_roles, $roles_arr) || in_array($current_user->id, $user_arr)) {
                $sql = "update vtiger_servicecontracts set isautoclose=? where servicecontractsid=? ";
                $adb->pquery($sql, array($contractsStatus, $servicecontractsid));
            }
        }

        $response = new Vtiger_Response();
	    $response->setResult(array());
	    $response->emit();
	}

	// 关闭状态
	function setContractsStates(Vtiger_Request $request) {
		$servicecontractsid = $request->get('record');
		$contractsStatus = $request->get('status');

		// 判断当前用户是否是 管理员和财务专员
		// 回款拆分的权限
        global $current_user;
        $adb = PearDatabase::getInstance();
        $sql = "select * FROM vtiger_custompowers where custompowerstype='updateContractsStates' LIMIT 1";
        $sel_result = $adb->pquery($sql, array());
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $roles_arr = explode(',', $row['roles']);
            $user_arr = explode(',', $row['user']);

            if (in_array($current_user->current_user_roles, $roles_arr) || in_array($current_user->id, $user_arr)) {
                $sql = "update vtiger_servicecontracts set contractstate=? where servicecontractsid=? ";
                $adb->pquery($sql, array($contractsStatus, $servicecontractsid));
            }
        }

        $response = new Vtiger_Response();
	    $response->setResult(array());
	    $response->emit();
	}

	/**
	 *  ajax请求返回josn格式 合同信息
	 * index.php?module=ServiceContracts&action=BasicAjax&record=合同id&mode=getservicecontractsinfo
     *@author 2015年4月30日 星期四 wangbin
     *@param  $request
     *@return string 客户 金额 负责人
	 */

	function getservicecontractsinfo(Vtiger_Request $request){
	    $servicecontractsid = $request->get('record');
	    $db=PearDatabase::getInstance();

	    //根据合同id读取该条合同下的客户，客户负责人，货币类型等等加载到，应收会计块去。
	    $sql = "SELECT usr.id,crm.label,usr.last_name, IF ( usr.`status` = 'Active', '', '[离职]' ) as status, ( SELECT departmentname FROM vtiger_departments WHERE departmentid = usd.departmentid ) AS departmentname, service.currencytype, service.total,service.contract_type FROM vtiger_servicecontracts service LEFT OUTER  JOIN vtiger_crmentity crm ON crm.crmid = service.sc_related_to LEFT OUTER  JOIN vtiger_users usr ON crm.smownerid = usr.id LEFT OUTER  JOIN vtiger_user2department usd ON usr.id = usd.userid WHERE service.servicecontractsid = ?";
	    $result = $db->pquery($sql,array($servicecontractsid));
	    $row = $db->num_rows($result);
	    $lis = array();
	    if($row>0){
	       for($i=0;$i<$row;++$i){
	           $lis[] = $db->fetchByAssoc($result);
	       }
	    }

	    //读取业绩分成表
	    $receivepayid = $request->get('receivepayid');
        $sql2 = "SELECT *, ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_achievementallot.receivedpaymentownid = vtiger_users.id ) AS receiveownid2 FROM vtiger_achievementallot WHERE receivedpaymentsid = ? AND servicecontractid = ?";
	    $receivementresult = $db->pquery($sql2,array($receivepayid,$servicecontractsid));
        $receivelis = array();
	     if($db->num_rows($receivementresult)>0){
	        for($j=0;$j<$db->num_rows($receivementresult);++$j){
	            $receivelis[] = $db->fetchByAssoc($receivementresult);
	        }
	    }else{
	        $receivelis = ServiceContracts_Record_Model::servicecontracts_divide($servicecontractsid);
	    }
	   // var_dump($receivelis);die;
	   $jsonresult = array($lis,$receivelis);
	    //end
	   $response = new Vtiger_Response();
	   $response->setEmitType(Vtiger_Response::$EMIT_JSON);
	   $response->setResult($jsonresult);
	   $response->emit();
	}
    /**
     * 2015-1-13 wangbin 发票关联尾款
     * index.php?module=ServiceContracts&action=BasicAjax&record=合同id&mode=receivepay
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function receivepay(Vtiger_Request $request){
		$productid  = $request->get('record');
		$db=PearDatabase::getInstance();
		$result = $db->pquery("SELECT * from vtiger_receivedpayments where relatetoid=? ",array($productid));
		$row=$db->num_rows($result);
		$lis=array();
		if($row>1){
			for ($i=0; $i<$row; ++$i) {
				$li = $db->fetchByAssoc($result);
				$lis[]=$li;
			}
		}elseif($row==1){
			$lis[] = $db->query_result_rowdata($result);
		}
		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($lis);
		$response->emit();
	}

	/**
     * index.php?module=ServiceContracts&action=BasicAjax&record=合同id&mode=getsmownerid
	 * @author wangbin
	 * @see Vtiger_Action_Controller::getViewer()
	 * @param int accountid 客户id
	 * @return string 客户负责人id
	 */
	public function getsmownerid(Vtiger_Request $request){
	    global $current_user;
	    $accountid = $request->get('record');
	    $signaturetype = $request->get('signaturetype');
	    $db=PearDatabase::getInstance();//young 20150427 优化代码
	    //$sql = 'SELECT (SELECT id FROM vtiger_users where id=vtiger_crmentity.smownerid) as id FROM vtiger_crmentity WHERE crmid = ?';
	    //2016-3-8 替换，判断是否是来自于市场部的客户的第一笔合同;
        $sql = "SELECT frommarketing,(SELECT id FROM vtiger_users where id=vtiger_crmentity.smownerid) AS smoid,linkname,mobile FROM vtiger_account INNER JOIN vtiger_crmentity ON crmid = accountid WHERE crmid = ?";
        $smownerdata = $db->pquery($sql,array($accountid));
	    if ($db->num_rows($smownerdata) > 0) {
	        $data['fromarket'] = $db->query_result($smownerdata,'0','frommarketing');
            $data['smoid'] = $db->query_result($smownerdata,'0','smoid');
            $data['contacts'][]=array('linkname'=>$db->query_result($smownerdata,'0','linkname'),'mobile'=>$db->query_result($smownerdata,'0','mobile'));
	    }
        $account_sql = 'SELECT * FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON crmid = servicecontractsid LEFT JOIN vtiger_account ON accountid = sc_related_to WHERE sc_related_to = ? AND vtiger_crmentity.deleted != 1';
        $frommarket = $db->pquery($account_sql,array($accountid));

        if($data['fromarket']=='1' && $db->num_rows($frommarket)==0){
            $data['from'] = true;
        }else{
            $data['from'] = false;
        }
        if($data['fromarket']=='1'){
            $separateIntoRecordModel = SeparateInto_Record_Model::getCleanInstance("SeparateInto");
            $shareInfo = $separateIntoRecordModel->getMarketingShareInfo($accountid);
            $data['shareInfo']=$shareInfo;
        }

        if(empty( $data['smoid'])){
	        $data['smoid'] = Users_Record_Model::getCurrentUserModel()->column_fields['currency_id'];
	    }
        if($signaturetype=='eleccontract'){
            $query='SELECT name,mobile FROM vtiger_contactdetails WHERE accountid=? AND accountid>0';
            $contactResult = $db->pquery($query,array($accountid));
            while($row=$db->fetch_array($contactResult)){
                $data['contacts'][]=array('linkname'=>$row['name'],'mobile'=>$row['mobile']);
            }
            $data['user']=array('mobile'=>$current_user->mobile,'name'=>$current_user->last_name);
	    }
        //判断是否需要资质附件
//        $isNeedZizhiFujian=$this->isNeedZizhiFujian($accountid);
//        if($isNeedZizhiFujian){
//            //需要资质附件
//            $data['needZizhi']='yes';
//        }
	    $response = new Vtiger_Response();
	    $response->setResult($data);
	    $response->emit();
	}

    /**
     * 需要验证资质
     * @param $accountId
     * @return bool
     */
	public function isNeedZizhiFujian($accountId){
	    global $adb,$tyunweburl;
        $sql="select accountid from vtiger_account where accountrank in ('visp_isv','iron_isv','bras_isv','silv_isv','gold_isv') and  accountid=".$accountId;
        $hasOrderCustomer=$adb->run_query_allrecords($sql);
        if($hasOrderCustomer){
            //有成交的订单
            return false;
        }else{
            $sql="select accountname,isnewcheck,file from vtiger_account where accountid=?";
            $result=$adb->pquery($sql,array($accountId));
            $accountName=$adb->query_result($result,'0','accountname');
            $isnewcheck=$adb->query_result($result,'0','isnewcheck');
            $file=$adb->query_result($result,'0','file');
            if($isnewcheck!=1){
                if($isnewcheck===0&&$file){
                    //已经上传过了,不需要再上传了
                    return false;
                }
                //该企业资质没有被审核通过
                $url=$tyunweburl."api/app/ai-business-search/search/isInOperation?companyName=".urlencode($accountName);
                $res = $this->https_requestTweb($url);
                $resultData = json_decode($res, true);
                if($resultData&&$resultData['data']==1){
                    //该企业存在
                    return false;
                }else{
                    //该企业不存在需要验证资质
                    return true;
                }
            }else{
                return false;
            }
        }
    }

    /**
     * 获得资质附件
     * @param Vtiger_Request $request
     */
    public function getEditZizhiFile(Vtiger_Request $request){
        global $adb;
        $record=$request->get('record');
        $isNeed=$this->isNeedZizhiFujian($record);
        if($isNeed){
            $sql="select file from vtiger_account where accountid=?";
            $result=$adb->pquery($sql,array($record));
            $fileStr=$adb->query_result($result,0,'file');
            $data=array('flag'=>true);
            if($fileStr){
                $fileArray=explode('*|*',$fileStr);
                foreach ($fileArray as $fileS){
                    $fileA=explode('##',$fileS);
                    $data['data'][$fileA[1]]=$fileA[0];
                }
            }
        }
//        $data=array('flag'=>true,'data'=>array('40715'=>'5.5.6.sql','40397'=>'词霸兼容.doc'));
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    public function getAccountFile(Vtiger_Request $request){
        global $adb;
        $record=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($record, 'ServiceContracts');
        $sql="select file from vtiger_account where accountid=?";
        $result=$adb->pquery($sql,array($recordModel->get('sc_related_to')));
        $fileStr=$adb->query_result($result,0,'file');
        $data=array('flag'=>true);
        if($fileStr){
            $fileArray=explode('*|*',$fileStr);
            foreach ($fileArray as $fileS){
                $fileA=explode('##',$fileS);
                $data['result'][base64_encode($fileA[1])]=$fileA[0];
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * tweb请求
     * @param $url
     * @return bool|string
     */
    function https_requestTweb($url){
        $curl = curl_init();
        $time = time() . '123';
        $sault = $time . 'multiModuleProjectDirectoryasdafdgfdhggijfgfdsadfggiytudstlllkjkgff';
        $token = md5($sault);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'S-Request-Token:'.$token,
                'S-Request-Time:'.$time
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * ajax请求返回josn格式数据货币类型
     * @param Vtiger_Request $request
     */
    public function getcurrencytype(Vtiger_Request $request){
        $recordId = $request->get('record');//合同的id
        $db=PearDatabase::getInstance();
        $sql = 'SELECT currencytype FROM `vtiger_servicecontracts` WHERE servicecontractsid=?';
        $currencytype = $db->pquery($sql,array($recordId));

        if ($db->num_rows($currencytype) > 0) {
            $data = $currencytype->fields['currencytype'];
        }else{
            $data="";
        }
        //var_dump($currencytype);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function process(Vtiger_Request $request) {
	    global $configcontracttypeNameTYUN;
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	    //2015-1-23 wangbin 合并两次请求ajax
	    $recordId = $request->get('record');//合同的id
		$salesorderid = $request->get('salesorderid');//工单的id 编辑模式
        $productidlist = array();
	    $db=PearDatabase::getInstance();
	    //if(empty($salesorderid)) {
            $query = 'SELECT vtiger_servicecontracts.contract_type,vtiger_servicecontracts.servicecontractstype,vtiger_servicecontracts.productid,vtiger_servicecontracts.extraproductid FROM `vtiger_servicecontracts` WHERE servicecontractsid=?';
            $servicecontract_result = $db->pquery($query, array($recordId));
            $servicecontract_num = $db->num_rows($servicecontract_result);
            if ($servicecontract_num) {
                $result_data = $db->raw_query_result_rowdata($servicecontract_result, 0);
                if (in_array($result_data['contract_type'],$configcontracttypeNameTYUN) && !empty($result_data['productid'])) {
                    $productidArray = explode(',', $result_data['productid']);
                    $servicecontractstype=($result_data['servicecontractstype']=='新增' ||
                        $result_data['servicecontractstype']=='againbuy')?'':
                        (($result_data['servicecontractstype']=='续费' || $result_data['servicecontractstype']=='renew')?'r':($result_data['servicecontractstype']=='upgrade'?'u':'d'));
                    foreach ($productidArray as $value) {
                        $query = "SELECT tempproducts.productid,'' AS `productform`,'' AS istyunweb,tempproducts1.productname as twebthepackage,'' AS salesorderid,tempproducts1.productname AS productcomboid FROM vtiger_products tempproducts
                            LEFT JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid=tempproducts.productid 
                            LEFT JOIN vtiger_products tempproducts1 ON tempproducts1.productid=vtiger_seproductsrel.productid
                            WHERE FIND_IN_SET(?,tempproducts1.twebpackageproductid)";
                        $productidlistResult = $db->pquery($query, array($servicecontractstype.$value));
                        if($db->num_rows($productidlistResult)){
                            while ($rowdata = $db->fetch_array($productidlistResult)) {
                                $productidlist[$rowdata['productid']] = array(
                                    'productid' => $rowdata['productid'],
                                    'productform' => $rowdata['productform'],
                                    'istyunweb' => $rowdata['istyunweb'],
                                    'twebthepackage' => $rowdata['twebthepackage'],
                                    'productcomboid' => $rowdata['productcomboid'],
                                );
                            }
                        }
                    }
                }
            }
        //}
        //查询合同下产品信息
        $sql = 'SELECT `productid`,`productform`,istyunweb,thepackage as twebthepackage, salesorderid, IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), \'--\' ) AS productcomboid FROM vtiger_salesorderproductsrel WHERE servicecontractsid =  ? AND (vtiger_salesorderproductsrel.multistatus=0 OR vtiger_salesorderproductsrel.multistatus=1)';
        $product = $db->pquery($sql,array($recordId));
        $productids = $db->num_rows($product);
        //合同下得有产品//没有无产品的合同提工单

        if($productids>0){
            $istyunweb=0;
            $twebproduct=array();
		    while($row=$db->fetchByAssoc($product)){
                $istyunweb=$row['istyunweb'];
                $twebthepackage=$row['twebthepackage'];
                if($istyunweb==1){
                    if($twebthepackage=='--'){
                        $query="SELECT * FROM vtiger_products WHERE ispackage=0 AND FIND_IN_SET(?,twebproductid)";
                        $resultdata=$db->pquery($query,array($row['productid']));
                        $resultnum=$db->num_rows($resultdata);
                        if($resultnum){
                            $resultdata1=$db->raw_query_result_rowdata($resultdata,0);
                            if(!in_array($resultdata1['productid'],$twebproduct)){
                                $twebproduct[]=$resultdata1['productid'];
                                $row['productid']=$resultdata1['productid'];
                                //$row['productcomboid']=$row['twebthepackage'];
                                $row['productcomboid']='--';
                                $productidlist[$resultdata1['productid']] = $row;
                            }
                        }
                    }
                }else{
                    $productidlist[$row['productid']] = $row;
                }
                //$module = Vtiger_Record_Model::getInstanceById($row['productid'],'Products');$productidlist[$i] = $module->getData();
                //$productidlist[$i]['solutions']= empty($row['salesorderid'])?$productidlist[$i]['notecontent']:$row['notecontent'];
                //$productidlist[$i]['productcomboid']= empty($row['productcomboid'])?'--':$row['productcomboid'];
            }
        }
        $isEditForm = true;
        //编辑工单
        if ($salesorderid) {
            $productlist = $db->pquery('SELECT vtiger_salesorder_productdetail.FormInput,vtiger_salesorder_productdetail.TplId, vtiger_products.productname,vtiger_products.product_no, vtiger_products.productid, vtiger_formdesign.content_parse,vtiger_formdesign.field FROM vtiger_salesorder_productdetail LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_salesorder_productdetail.Productid LEFT JOIN vtiger_formdesign ON vtiger_formdesign.formid = vtiger_salesorder_productdetail.TplId WHERE vtiger_salesorder_productdetail.SalesOrderId =?', array($salesorderid));
            $rows = $db->num_rows($productlist);
            //旧数据只有模版
            if (!$rows) {
                $isEditForm = false;
                $productlist = $db->pquery('SELECT vtiger_products.productname,vtiger_products.product_no, vtiger_products.productid from vtiger_products WHERE vtiger_products.productid in(' . implode(',', array_keys($productidlist)) . ')');
                $rows = $db->num_rows($productlist);
            }
        } else {
            $productlist = $db->pquery('SELECT vtiger_customer_modulefields.formid as tplid,vtiger_products.productname,vtiger_products.product_no,vtiger_products.productid,vtiger_formdesign.content_parse,vtiger_formdesign.field FROM vtiger_customer_modulefields LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_customer_modulefields.relateid LEFT JOIN vtiger_formdesign ON vtiger_formdesign.formid = vtiger_customer_modulefields.formid WHERE vtiger_customer_modulefields.relateid IN (' . implode(',', array_keys($productidlist)) . ')');
            $rows = $db->num_rows($productlist);
            if (!$rows) {
                //产品无表单
            }
        }
        //使用产品表单
        if ($rows) {
			$products=array();
			require 'include/utils/formparse.php';
			while($row=$db->fetchByAssoc($productlist)){
				if($isEditForm){
					$datas=json_decode(str_replace('&quot;','"',$row['field']),true);
					$values=(empty($row['forminput']))?array():json_decode(str_replace('&quot;','"',$row['forminput']),true);
					$productidlist[$row['productid']]['productform']=parse_toform($datas,$row['content_parse'],$values,$row['productid']);
					$productidlist[$row['productid']]['product_tplid']=$row['tplid'];
				}
				$productidlist[$row['productid']]['productname']=$row['productname'];
				$productidlist[$row['productid']]['product_no']=$row['product_no'];

			}
		}

	    //$product = $db->pquery('select `productid`,(select notecontent from vtiger_productcf where vtiger_productcf.productid=vtiger_salesorderproductsrel.productid) as notecontent from vtiger_salesorderproductsrel where servicecontractsid = ?',array($recordId));
	   /* $sql1="SELECT productname FROM vtiger_products WHERE productid=(select productcomboid FROM vtiger_servicecontracts WHERE servicecontractsid=?)";
        $package = $db->pquery($sql1,array($recordId))->fields['0'];//读取产品套餐*/
      /*   $package = array();
        $sql = 'SELECT `productid`, `productform` AS notecontent, salesorderid, IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), \'--\' ) AS productcomboid FROM vtiger_salesorderproductsrel WHERE servicecontractsid =  ?';
	    $product = $db->pquery($sql,array($recordId));
	    $li1 = $db->num_rows($product);
	    $productidlist = array();
		//echo $sql;
		//exit;
	    //young.yang 2015-1-29 修改原代码，简化
		if($li1>0){
			$i=0;
		    while($row=$db->fetch_row($product)){
		    	$module = Vtiger_Record_Model::getInstanceById($row['productid'],'Products');
		    	$productidlist[$i] = $module->getData();
		    	$productidlist[$i]['solutions']= empty($row['salesorderid'])?$productidlist[$i]['notecontent']:$row['notecontent'];
                $productidlist[$i]['productcomboid']= empty($row['productcomboid'])?'--':$row['productcomboid'];
		    	$i=$i+1;
		    }
		} */
	    //end
		//var_dump($productidlist);die;

		$moduleName = $request->get('module');
		$module = Vtiger_Record_Model::getInstanceById($recordId,'ServiceContracts');
		$result=array();
		$result=$module->getData();
		$accountsId=$result['sc_related_to'];
		//$smownerid=$result['assigned_user_id'];//合同领取人
		$smownerid=$result['Receiveid'];//2015年8月5日  wangbin  #1023 工单编辑负责人经常会变化
		$total=$result['total'];
        $remark = $result['remark'];
		$accountsModule=Vtiger_Record_Model::getInstanceById($accountsId,'Accounts');
		//合同数据
		$accounts=$accountsModule->getData();
		//合同回款记录
		$rp=ReceivedPayments_Record_Model::getAllReceivedPayments($recordId);
		$return=array('accountname'=>$accounts['accountname'],'id'=>$accounts['record_id'],'userid'=>$smownerid,'total'=>$total,'customerno'=>$accounts['account_no'],'rp'=>$rp,'remark'=>$remark);
		$datas=array($productidlist,$return,$package,$isEditForm);
		$response = new Vtiger_Response();
		$response->setResult($datas);
		$response->emit();
	}


    /**
     *
     * 合同领用
     * @param Vtiger_Request $request
     */
    public function Received(Vtiger_Request $request){
        $userid=$request->get('userid');
        $codenumber=$request->get('inputcode');
        $codenumber=trim($codenumber);
        $superconllar=$request->get('superconllar');
        $supermanid=$request->get('supermanid');
        $mod=new ServiceContracts_Module_Model();
        $datas=array();
        do{
            //有没有权限操作
            if(!$mod->exportGrouprt('ServiceContracts','Received')) {
                $datas['msg']='没有权限';
                $datas['rstatus']='no_status';
                break;
            }
            $db=PearDatabase::getInstance();
            //根据公司员工编号0001-9999四位数字这里只判断为数字，长度没有做处理，合同编号不能为全数字
            if($userid==0||(is_numeric($codenumber) && $superconllar==0)){
                $datas=$this->checkuser($codenumber);
                break;
            }
            if($userid!=0 && ($supermanid==0 || is_numeric($codenumber)) && $superconllar==1){
                $datas=$this->checkuser($codenumber);
                if($datas['rstatus']!='no_status'){
                    $datas['rstatus']='superman_status';
                }else{
                    $datas['msg']='超领人--'.$datas['msg'];
                }
                break;
            }
            if(empty($codenumber)||empty($userid)){
                $datas['msg']='不正确的输入';
                $datas['rstatus']='no_status';
                break;
            }
            if ($userid != $_SESSION['confirmServiceContractsReceived']) {
//                $datas['msg'] = '请先扫二维码确认!或领取人和扫码人不是同一人';
//                $datas['rstatus'] = 'no_status';
//                break;
            }
            $codenumbertemp = preg_replace('/-8$/', '', $codenumber);
            $codenumbertemp = is_numeric($codenumbertemp) ? $codenumbertemp : $codenumber;
            //$codenumbertemp=rtrim($codenumber,'-8');
            $status=$this->checkcontractsno(array($codenumbertemp,1));
            if($status['rstatus']=='no_status'){
                $datas=$status;
                break;
            }
            $servicenum=ServiceContracts_Record_Model::servicecontracts_reviced($userid);
            //指定的用户不限合同份数
            if($servicenum && !$mod->exportGrouprt('ServiceContracts','Received',$userid)){
                //if($servicenum>=$this->getCExceedingNumber($userid)){
                    $datas['msg']=$servicenum;
                    $datas['rstatus']='super_status';
                    break;
                //}
            }
            $tempfield = 'servicecontractsprintid';
            if (is_numeric($codenumbertemp)) {
                $query = "SELECT vtiger_servicecontracts.servicecontractsid,modulestatus,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.contract_classification FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid LEFT JOIN vtiger_servicecontracts_print ON vtiger_servicecontracts.contract_no=vtiger_servicecontracts_print.servicecontracts_no WHERE vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts_print.servicecontractsprintid=?";
                $result = $db->pquery($query, array($codenumbertemp));
                $contractsql = ' AND vtiger_servicecontracts.servicecontractsprintid=' . $codenumbertemp;
            } else {
                $query = "SELECT vtiger_servicecontracts.servicecontractsid,modulestatus,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.contract_classification FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts.contract_no=?";
                $result = $db->pquery($query, array($codenumbertemp));
                $tempfield = 'servicecontracts_no';
                $contractsql = ' AND vtiger_servicecontracts.contract_no=\'' . $codenumbertemp . '\'';
            }
            $num=$db->num_rows($result);
            if($num==0){
                $datas['msg']='合同已领取,生成错误请手工录入';
                $datas['rstatus']='no_status';
                break;
            }elseif($num>1){
                $datas['msg']='多个重复的合同编号';
                $datas['rstatus']='no_status';
                break;
            }
            $rows = $db->raw_query_result_rowdata($result);
            if($rows['modulestatus']=='c_complete'){
                $datas['msg']='合同已签收';
                $datas['rstatus']='no_status';
                break;
            }
            if ($rows['modulestatus'] == 'c_cancel') {
                $datas['msg'] = '合同已作废';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['modulestatus'] != 'c_stamp') {
                $datas['msg'] = '只有状态为已盖章的合同才能进行操作';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($this->checkNostand($userid, $contractsql)) {
                $datas['msg'] = '非标合同领取人不是指定的代领人!';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $signpath = $request->get('signpath');
            unset($_REQUEST); //防止信息干扰
            /* $_REQUEST['record']=$rows['servicecontractsid'];
              $request=new Vtiger_Request($_REQUEST, $_REQUEST);
              $_REQUEST['action']='SaveAjax';
              $request->set('assigned_user_id',$userid);
              $request->set('modulestatus', '已发放');
              $request->set('Receivedate', date('Y-m-d'));
              $request->set('module','ServiceContracts');
              $request->set('view','Edit');
              $request->set('action','SaveAjax');
              $ressorder=new ServiceContracts_Save_Action();
              $ressorderecord=$ressorder->saveRecord($request); */
            $recordModel = Vtiger_Record_Model::getInstanceById($rows['servicecontractsid'], 'ServiceContracts');
            if($rows['contract_classification']=='tripcontract'){
                $agentlists = $recordModel->getAgentList();
                $datas['userid']=$userid;
                $datas['contractno']=$rows['contract_no'];
                $datas['servicecontractsid']=$rows['servicecontractsid'];
                $datas['contract_classification']=$rows['contract_classification'];
                $datas['inputcode']=$codenumber;
                $datas['signpath']=$signpath;
                $datas['tempfield']=$tempfield;
                $datas['codenumbertemp']=$codenumbertemp;
                $datas['agentlists']=json_decode($agentlists,true);
                $datas['rstatus']='contractok';
                break;
            }
            $params = array(
                'servicecontractsid'=>$rows['servicecontractsid'],
                'userid'=>$userid,
                'inputcode'=>$codenumber,
                'signpath'=>$signpath,
                'recordId'=>$rows['servicecontractsid'],
                'tempfield'=>$tempfield,
                'codenumbertemp'=>$codenumbertemp,
            );

            $this->doReceived($params);
//            $this->dosignContracts(array('controllerType'=>'Received','userid'=>$userid,'inputcode'=>$codenumber,'signpath' => $signpath, 'userid' => $userid, "recordId" => $rows['servicecontractsid'], 'sequence' => $this->sequencefour));
//            $datetime = date('Y-m-d H:i:s');
//            global $current_user;
//            $sql = "UPDATE vtiger_servicecontracts_print SET constractsstatus='c_receive',receivedtime=?,receivedid=?,doreceivedid=? WHERE {$tempfield}=?";
//            $db->pquery('UPDATE vtiger_servicecontracts SET modulestatus=?,receivedate=? WHERE servicecontractsid=?', array('已发放', date('Y-m-d'), $rows['servicecontractsid']));
//            $db->pquery($sql, array($datetime, $userid, $current_user->id, $codenumbertemp));
//            $sql = 'UPDATE vtiger_crmentity SET smownerid=? WHERE crmid=?';
//            $db->pquery($sql, array($userid, $rows['servicecontractsid']));
//            $id = $db->getUniqueId('vtiger_modtracker_basic');
//            $currentTime=date('Y-m-d H:i:s');
//            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
//                array($id, $rows['servicecontractsid'], 'ServiceContracts', $current_user->id, $currentTime, 0));
//            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
//                Array($id, 'modulestatus', 'c_stamp', '已发放'));
//            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
//                Array($id, 'Receivedate', $recordModel->get('Receivedate'), date("Y-m-d")));
//            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
//                Array($id, 'assigned_user_id', $recordModel->get('assigned_user_id'), $userid));
            $datas['userid']=$userid;
            $datas['contractno']=$rows['contract_no'];
            $datas['servicecontractid']=$rows['servicecontractsid'];
            $datas['contract_classification']=$rows['contract_classification'];
            $datas['rstatus']='contractok';
            //合同领取成功消息提醒
            $recordModel->sendReceivedWx($rows['contract_no'],$userid);
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
        exit;
    }

    /**
     * 三方合同领取
     */
    public function tripContractReceived(Vtiger_Request $request){
        $datas = array();
        do {
            $servicecontractsid = $request->get("servicecontractsid");
            $userid = $request->get("userid");
            $recordModel = ServiceContracts_Record_Model::getInstanceById($servicecontractsid,'ServiceContracts');
            $params = array(
                'servicecontractsid'=>$servicecontractsid,
                'userid'=>$userid,
                'inputcode'=>$request->get("inputcode"),
                'signpath'=>$request->get("signpath"),
                'recordId'=>$servicecontractsid,
                'tempfield'=>$request->get("tempfield"),
                'codenumbertemp'=>$request->get("codenumbertemp"),
                'agentid'=>$request->get("agentid"),
                'agentname'=>$request->get("agentname"),
            );
            $this->doReceived($params);
            //合同领取成功消息提醒
            $recordModel->sendReceivedWx($recordModel->get('contract_no'),$userid);
            $datas['userid']=$userid;
            $datas['contractno']=$request->get("inputcode");
            $datas['rstatus']='contractok';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
        exit;
    }

    /**
     * 查看工号信息
     * @param $codenumber int用户工号
     * @return mixed
     * @throws Exception
     */
    public function checkuser($codenumber){
        $db=PearDatabase::getInstance();
        //修改合同领用的工号比对,用户编号改为6码，原有的用户编号不足6码的，前面补0 2017/03/01 gaocl edit
        $new_codenumber=str_pad($codenumber, 6, '0', STR_PAD_LEFT);
        $query="SELECT vtiger_users.id,vtiger_users.usercode,vtiger_users.last_name,vtiger_users.`status`,vtiger_departments.departmentname FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE vtiger_users.`status`='Active' AND (usercode=? OR usercode=?)";
        $result=$db->pquery($query,array($codenumber,$new_codenumber));
        $num=$db->num_rows($result);

        if($num<=0){
            $datas['msg']='不存在的工号';
            $datas['rstatus']='no_status';
        }else if($num==1){
            $rows=$db->query_result_rowdata($result);

            $userstatus=$rows['status']=='Active'?'':'[离职]';
            $datas['userid']=$rows['id'];
            $datas['ucode']=$rows['usercode'];
            $datas['username']=$rows['last_name'].'['.$rows['departmentname'].']'.$userstatus;
            $datas['rstatus']='userset';
        }else{
            $datas['msg']='该工号有多个人使用';
            $datas['rstatus']='no_status';
        }
        return $datas;
    }

    /**
     * 该合同是否是非标合同如果是则判断当前领用是否是合同的申请人
     * @param $userid
     * @param $contractsql
     * @return bool
     *
     */
    public function checkNostand($userid,$contractsql)
    {
        $db=PearDatabase::getInstance();
        //$query="SELECT smownerid FROM `vtiger_servicecontracts` WHERE nostand=1".$contractsql;
        $query="SELECT vtiger_servicecontracts.isstandard,vtiger_servicecontracts.receiptorid FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.isstandard=1".$contractsql;
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        if($num){
            $row=$db->query_result_rowdata($result);
            if($row['receiptorid']==$userid)
            {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 查看合同编号表中是否可以使用
     * @param $codenumber array ,合同编号,状态1为领取,2归还
     * @return array
     * @throws Exception
     */
    public function checkcontractsno($codenumber){
        $db=PearDatabase::getInstance();
        $query="SELECT * FROM `vtiger_servicecontracts_print` WHERE servicecontractsprintid=?";
        $result=$db->pquery($query,array($codenumber[0]));
        $num=$db->num_rows($result);
	if($num==0){
            $query="SELECT * FROM `vtiger_servicecontracts_print` WHERE servicecontracts_no=?";
            $result=$db->pquery($query,array($codenumber[0]));
            $num=$db->num_rows($result);
        }
        $datas=array('rstatus'=>'ok');
        do {
            if ($num <= 0) {
                $datas['msg'] = '不存在的合同编号';
                $datas['rstatus'] = 'no_status';
                break;
            }if ($num >1) {
                $datas['msg'] = '合同编号重复';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $rows = $db->raw_query_result_rowdata($result);
            if ($rows['constractsstatus'] == 'c_receive' && $codenumber[1]==1) {
                $datas['msg'] = '合同已领取';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['constractsstatus'] == 'c_recovered') {
                $datas['msg'] = '合同已收回';
                $datas['rstatus'] = 'no_status';
                break;
            }

            if ($rows['constractsstatus'] == 'c_generated') {
                $datas['msg'] = '合同未打印';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['constractsstatus'] == 'c_print') {
                $datas['msg'] = '合同未盖章';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['constractsstatus'] == 'c_stamp' && $codenumber[1]==2) {
                $datas['msg'] = '合同未领取';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['constractsstatus'] == 'c_cancel') {
                $datas['msg'] = '合同已作废';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['constractsstatus'] == 'c_stamp') {
                $datas['sno'] = $rows['servicecontracts_no'];
                $datas['rstatus'] = 'OK';
                break;
            }
        }while(0);
        return $datas;
    }

    /**
     * 合同的归还
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function Returned(Vtiger_Request $request){
        $userid=$request->get('userid');
        $codenumber=$request->get('inputcode');
        $codenumber=trim($codenumber);
        $mod=new ServiceContracts_Module_Model();
        $datas=array();
        do{
            if(!$mod->exportGrouprt('ServiceContracts','Received')) {
                $datas['msg']='没有权限';
                $datas['rstatus']='no_status';
                break;
            }
            $db=PearDatabase::getInstance();
            if($userid==0||is_numeric($codenumber)){
                $datas=$this->checkuser($codenumber);
                break;
            }
            if(empty($codenumber)||empty($userid)){
                $datas['msg']='不正确的输入';
                $datas['rstatus']='no_status';
                break;
            }
            /*if($userid!=$_SESSION['confirmServiceContractsReturned']){
                $datas['msg']='请先扫二维码确认!或归还人和扫码人不是同一人';
                $datas['rstatus']='no_status';
                break;
            }*/
            $codenumbertemp=preg_replace('/-8$/','',$codenumber);
            $codenumbertemp=is_numeric($codenumbertemp)?$codenumbertemp:$codenumber;
            //$codenumbertemp=rtrim($codenumber,'-8');
            $status=$this->checkcontractsno(array($codenumbertemp,2));
            if($status['rstatus']=='no_status'){
                $datas=$status;
                break;
            }
	    $tempfield='servicecontractsprintid';
            if(is_numeric($codenumbertemp)){
                $query="SELECT vtiger_servicecontracts.servicecontractsid,vtiger_servicecontracts.modulestatus,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.signaturetype,vtiger_servicecontracts.sideagreement,vtiger_contractsagreement.supplementarytype FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid LEFT JOIN vtiger_servicecontracts_print ON vtiger_servicecontracts.contract_no=vtiger_servicecontracts_print.servicecontracts_no LEFT JOIN vtiger_contractsagreement on vtiger_contractsagreement.newservicecontractsid=vtiger_servicecontracts.servicecontractsid  WHERE vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts_print.servicecontractsprintid=?";
                $result=$db->pquery($query,array($codenumbertemp));
            }else{
                $query="SELECT vtiger_servicecontracts.servicecontractsid,vtiger_servicecontracts.modulestatus,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.signaturetype,vtiger_servicecontracts.sideagreement,vtiger_contractsagreement.supplementarytype FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid LEFT JOIN vtiger_contractsagreement on vtiger_contractsagreement.newservicecontractsid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts.contract_no=?";
                $result=$db->pquery($query,array($codenumbertemp));
                $tempfield='servicecontracts_no';
            }
            $num=$db->num_rows($result);
            if($num==0){
                $datas['msg']='合同已领取,生成错误请手工录入';
                $datas['rstatus']='no_status';
                break;
            }elseif($num>1){
                $datas['msg']='多个重复的合同编号';
                $datas['rstatus']='no_status';
                break;
            }
            $rows = $db->raw_query_result_rowdata($result);
            if($rows['modulestatus']=='c_complete'){
                $datas['msg']='合同已签收';
                $datas['rstatus']='no_status';
                break;
            }
            if ($rows['modulestatus'] == 'c_cancel') {
                $datas['msg'] = '合同已作废';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if($rows['signaturetype']=='eleccontract'){
                $datas['msg'] = '电子合同不支持该操作';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['modulestatus'] != '已发放') {
                $datas['msg'] = '只有状态为已发放的合同才能进行操作';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $signpath=$request->get('signpath');
            /*unset($_REQUEST);//防止信息干扰
            $_REQUES['record']=$rows['servicecontractsid'];
            $request=new Vtiger_Request($_REQUES, $_REQUES);
            //$request->set('contract_no',$codenumber);
            //$request->set('Receiveid',$userid);
            $request->set('modulestatus', 'c_recovered');
            $request->set('Returndate', date('Y-m-d'));
            $_REQUEST['action']='SaveAjax';
            $request->set('module','ServiceContracts');
            $request->set('view','Edit');
            $request->set('action','SaveAjax');
            $ressorder=new ServiceContracts_Save_Action();
            $ressorder->saveRecord($request);*/
            $recordModel=Vtiger_Record_Model::getInstanceById($rows['servicecontractsid'],'ServiceContracts');

            $datetime=date('Y-m-d H:i:s');
            global $current_user;
            $sql="UPDATE vtiger_servicecontracts_print SET constractsstatus='c_recovered',recoveredtime=?,recoveredid=?,dorecoveredid=? WHERE {$tempfield}=?";
            $db->pquery('UPDATE vtiger_servicecontracts SET modulestatus=?,returndate=? WHERE servicecontractsid=?',array('c_recovered',date('Y-m-d'),$rows['servicecontractsid']));
            $this->dosignContracts(array('controllerType'=>'Returned','userid'=>$userid,'inputcode'=>$codenumber,'signpath'=>$signpath,'userid'=>$userid,"recordId"=>$rows['servicecontractsid'],'workflowstagesflag'=>$this->recoverContract));
            $db->pquery($sql,array($datetime,$userid,$current_user->id,$codenumbertemp));
            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $currentTime=date('Y-m-d H:i:s');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $rows['servicecontractsid'], 'ServiceContracts', $current_user->id, $currentTime, 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'modulestatus', '已发放', 'c_recovered'));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'Returndate', $recordModel->get('Returndate'), date("Y-m-d")));
            $datas['userid']=$userid;
            $datas['contractno']=$rows['contract_no'];
            $datas['contractid']=$rows['servicecontractsid'];
            $datas['rstatus']='contractok';

            //分期协议合同收回时，作为分期合同协议签收完成
//            if($rows['sideagreement'] && $rows['supplementarytype']=='stagepay'){
//                $contractAgreementRecordModel = ContractsAgreement_Record_Model::getCleanInstance("ContractsAgreement");
//                $contractAgreementRecordModel->recordContractDelaySign($rows['servicecontractsid'],$rows['sideagreement'],'c_recovered');
//            }


        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
        exit;
    }
    /**
     * 合同的(未签合同)归还
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function NoSignReturned(Vtiger_Request $request){
        $datas=array();
        do{
            $userid=$request->get('userid');
            $codenumber=$request->get('inputcode');
            if($userid==0||is_numeric($codenumber)){
                $datas=$this->checkuser($codenumber);
                break;
            }
            $receiveddata=$this->noSignCommon($request);
            if($receiveddata['rstatus']=='no_status'){
                $datas=$receiveddata;
                break;
            }

            if ($userid != $_SESSION['confirmServiceContractsNoSignReturned']) {
//                $datas['msg'] = '请先扫二维码确认!或归还人和扫码人不是同一人';
//                $datas['rstatus'] = 'no_status';
//                break;
            }
            $db = PearDatabase::getInstance();
            $query = "select vtiger_newinvoice.*,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.servicecontractsid from vtiger_newinvoice 
                        LEFT JOIN vtiger_servicecontracts on vtiger_servicecontracts.servicecontractsid = vtiger_newinvoice.contractid 
                        where vtiger_servicecontracts.contract_no =? and vtiger_newinvoice.modulestatus!='c_cancel' and vtiger_newinvoice.modulestatus!='a_exception'";
            $result = $db->pquery($query, array($codenumber));
            $num = $db->num_rows($result);
//            $num=0;
            if($num>0){
                $rows = $db->raw_query_result_rowdata($result);
                $query = "select * from vtiger_refillapplication where servicecontractsid =?";
                $result = $db->pquery($query, $rows['servicecontractsid']);
                $num = $db->num_rows($result);
                if($num>0){
//                    $datas['msg'] = '此合同已存在发票，请处理。';
//                    $datas['rstatus'] = 'no_status';
                }else{
                    $datas['msg'] = '此合同已存在发票，请处理。';
                    $datas['rstatus'] = 'no_status';
                       $response = new Vtiger_Response();
                $response->setResult($datas);
                $response->emit();
                exit;
                }

            }
            $codenumbertemp = $receiveddata['codenumbertemp'];
            $contract_no = $receiveddata['rows']['contract_no'];
            $tempfield = $receiveddata['field'];
            $recordId = $receiveddata['rows']['servicecontractsid'];
            $moduleName = $request->getModule();
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            //$recordModel->delete();
            $datetime=date('Y-m-d H:i:s');
            global $current_user;
            $db = PearDatabase::getInstance();
            //$query = "UPDATE vtiger_crmentity set deleted=1,modifiedtime=?,modifiedby=? where crmid=?";
            $query = "UPDATE vtiger_crmentity set modifiedtime=?,modifiedby=? where crmid=?";
            $db->pquery($query,array($datetime,$current_user->id,$recordId));
            $query = "UPDATE vtiger_servicecontracts SET modulestatus='c_stamp' WHERE servicecontractsid=?";
            $db->pquery($query,array($recordId));
            $query="UPDATE vtiger_servicecontracts_print SET constractsstatus='c_stamp',receivedtime=NULL,receivedid=NULL WHERE {$tempfield}=?";
            $db->pquery($query,array($codenumbertemp));
            $signpath=$request->get('signpath');
            $this->dosignContracts(array('signpath'=>$signpath,'userid'=>$userid,"recordId"=>$recordId,'workflowstagesflag'=>$this->recoverContract));
            if($recordModel->get("contract_classification")=='tripcontract'){
                //将三方合同复原 去掉代理商的相关信息
                $db->pquery("update vtiger_servicecontracts set agentid='',agentname='',agentaccountid='' where servicecontractsid=?",array($recordId));
            }
            $db->pquery("UPDATE vtiger_crmentity,vtiger_extensiontrial SET deleted=1 WHERE vtiger_crmentity.crmid=vtiger_extensiontrial.extensiontrialid AND servicecontractsid=?",array($recordId));
            $datas['msg']='合同编号'.$contract_no.'(未签)归还完成';
            $datas['rstatus']='do_ok';
            $datas['contract_no']=$contract_no;
            $datas['mode']='NoSignReturned';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
        exit;
    }
    /**
     * 合同的(未签合同)作废
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function NotSignInvalid(Vtiger_Request $request){
        $datas=array();
        do{
            $userid=$request->get('userid');
            $codenumber=$request->get('inputcode');
            if($userid==0||is_numeric($codenumber)){
                $datas=$this->checkuser($codenumber);
                break;
            }
            $receiveddata=$this->noSignCommon($request);
            if($receiveddata['rstatus']=='no_status'){
                $datas=$receiveddata;
                break;
            }
            $db = PearDatabase::getInstance();
            $query='SELECT 1 FROM vtiger_newinvoice LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_newinvoice.invoiceid WHERE  vtiger_crmentity.deleted=0 AND contractid=?
                UNION ALL 
                SELECT 1 FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.relatetoid=?
                UNION ALL
                SELECT 1 FROM vtiger_salesorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_salesorder.servicecontractsid=?';
            $recordId=$receiveddata['rows']['servicecontractsid'];
            $result=$db->pquery($query,array($recordId,$recordId,$recordId));
            $num=$db->num_rows($result);
            if($num){
                $receiveddata['rstatus']=='no_status';
                $datas['msg']='合同已关联回款,工单,或已开具发票!';
                break;
            }
            $signpath=$request->get('signpath');
            $codenumbertemp=$receiveddata['codenumbertemp'];
            $contract_no=$receiveddata['rows']['contract_no'];
            $tempfield=$receiveddata['field'];
            $_REQUES['record']=$receiveddata['rows']['servicecontractsid'];
            $request=new Vtiger_Request($_REQUES, $_REQUES);
            //$request->set('contract_no',$codenumber);
            //$request->set('Receiveid',$userid);
            $request->set('modulestatus', 'c_cancel');
            //$request->set('Returndate', date('Y-m-d'));
            $request->set('module','ServiceContracts');
            $request->set('view','Edit');
            $_REQUEST['action']='SaveAjax';
            $request->set('action','SaveAjax');
            $ressorder=new ServiceContracts_Save_Action();
            $ressorder->saveRecord($request);

            global $current_user;

            $datetime=date('Y-m-d H:i:s');
            $query="UPDATE vtiger_servicecontracts_print SET constractsstatus='c_cancel',canceltime=?,recoveredtime=?,dorecoveredid=? WHERE {$tempfield}=?";
            $db->pquery($query,array($datetime,$datetime,$current_user->id,$codenumbertemp));
            $db->pquery('UPDATE vtiger_servicecontracts SET modulestatus=? WHERE servicecontractsid=?',array('c_cancel',$_REQUES['record']));

            $this->dosignContracts(array('signpath'=>$signpath,'userid'=>$userid,"recordId"=>$_REQUES['record'],'workflowstagesflag'=>$this->recoverContract));

            $datas['msg']='合同编号'.$contract_no.'(未签)作废完成';
            $datas['rstatus']='do_ok';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
        exit;
    }
    public function noSignCommon(Vtiger_Request $request){
        $codenumber=$request->get('inputcode');
        $codenumber=trim($codenumber);
        $mod=new ServiceContracts_Module_Model();
        $datas=array();
        do {
            if (!$mod->exportGrouprt('ServiceContracts', 'Received')) {
                $datas['msg'] = '没有权限';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $db = PearDatabase::getInstance();
            if (is_numeric($codenumber)) {
                $datas['msg'] = '不存在的合同编号或您输入了工号';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if (empty($codenumber)) {
                $datas['msg'] = '不正确的输入';
                $datas['rstatus'] = 'no_status';
                break;
            }
            //$codenumbertemp=rtrim($codenumber,'-8');
            $codenumbertemp = preg_replace('/-8$/', '', $codenumber);
            $codenumbertemp=is_numeric($codenumbertemp)?$codenumbertemp:$codenumber;
            $status = $this->checkcontractsno(array($codenumbertemp, 2));
            if ($status['rstatus'] == 'no_status') {
                $datas = $status;
                break;
            }
            $datas['field'] = 'servicecontractsprintid';
            if (is_numeric($codenumbertemp)) {
                $query = "SELECT vtiger_servicecontracts.servicecontractsid,modulestatus,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.signaturetype FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid LEFT JOIN vtiger_servicecontracts_print ON vtiger_servicecontracts.contract_no=vtiger_servicecontracts_print.servicecontracts_no WHERE vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts_print.servicecontractsprintid=?";
                $result = $db->pquery($query, array($codenumbertemp));
                $activeCodeQuery="SELECT 1 FROM vtiger_activationcode LEFT JOIN vtiger_servicecontracts_print ON vtiger_activationcode.contractname = vtiger_servicecontracts_print.servicecontracts_no WHERE vtiger_servicecontracts_print.servicecontractsprintid =? AND vtiger_activationcode.`status`<>2";
                $activeCodeResult=$db->pquery($activeCodeQuery,array($codenumbertemp));

                $activeCodeNum=$db->num_rows($activeCodeResult);
            } else {
                $query = "SELECT vtiger_servicecontracts.servicecontractsid,modulestatus,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.signaturetype FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts.contract_no=?";
                $result = $db->pquery($query, array($codenumbertemp));
                $datas['field'] = 'servicecontracts_no';
                $activeCodeQuery="SELECT 1 FROM vtiger_activationcode WHERE vtiger_activationcode.contractname =? AND vtiger_activationcode.`status`<>2";
                $activeCodeResult=$db->pquery($activeCodeQuery,array($codenumbertemp));

                $activeCodeNum=$db->num_rows($activeCodeResult);
            }
            $num = $db->num_rows($result);
            if($activeCodeNum>0){
                $datas['msg'] = '激活码存在不允许未签归还!';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($num == 0) {
                $datas['msg'] = '没有相关记录';
                $datas['rstatus'] = 'no_status';
                break;
            } elseif ($num > 1) {
                $datas['msg'] = '多个重复的合同编号';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $rows = $db->raw_query_result_rowdata($result);
            if ($rows['modulestatus'] == 'c_complete') {
                $datas['msg'] = '合同已签收';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['modulestatus'] == 'c_cancel') {
                $datas['msg'] = '合同已作废';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['modulestatus'] != '已发放') {
                $datas['msg'] = '只有状态为已发放的合同才能进行操作';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if($rows['signaturetype']=='eleccontract'){
                $datas['msg'] = '电子合同不支持该操作';
                $datas['rstatus'] = 'no_status';
                break;
            }

            $datas['codenumbertemp']=$codenumbertemp;
            $datas['rows']=$rows;
            $datas['rstatus']='ok';
        }while(0);
        return $datas;
    }

    function deletedAuditsettings(Vtiger_Request $request) {
    	$moduleModel = Vtiger_Module_Model::getInstance('ServiceContracts');
        if($moduleModel->exportGrouprt('ServiceContracts','AuditSettings')){   //权限验证
            global $current_user;
            $id=$request->get("id");
            $delsql="delete from vtiger_auditsettings where auditsettingsid=?";
            $db=PearDatabase::getInstance();
            $datetime=date('Y-m-d H:i:s');
            $db->pquery($delsql,array($id));
        }
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    // 合同延期审核权限添加
    function addAuditsettings(Vtiger_Request $request) {
    	$auditsettingtype = $request->get("auditsettingtype");
    	$department = $request->get("department");
    	$oneaudituid = $request->get("oneaudituid");
    	$towaudituid = $request->get("towaudituid");
    	$threeaudituid = $request->get("threeaudituid");
    	$audituid4 = $request->get("audituid4");
    	$audituid5 = $request->get("audituid5");
    	$data = array('flag'=>'0', 'msg'=>'添加失败');
    	do {
    		$moduleModel = Vtiger_Module_Model::getInstance('ServiceContracts');
            if(!$moduleModel->exportGrouprt('ServiceContracts','AuditSettings')){   //权限验证
                break;
            }
            if (empty($auditsettingtype)) {
            	break;
            }
            if (empty($department)) {
            	break;
            }
            if (empty($oneaudituid)) {
            	break;
            }
            if (empty($towaudituid)) {
            	break;
            }
            /*if (empty($threeaudituid)) {
            	//break;
                $threeaudituid='';
            }*/
            $sql = "delete from vtiger_auditsettings where auditsettingtype=? AND department=? AND oneaudituid=? AND towaudituid=?";
            $sql2 = "INSERT INTO `vtiger_auditsettings` (`auditsettingsid`, `auditsettingtype`, `department`, `oneaudituid`, `towaudituid`,audituid3,audituid4,audituid5, `createtime`, `createid`) VALUES (NULL, ?, ?,?, ?, ?, ?, ?,?,?)";
            global $current_user;
            $db=PearDatabase::getInstance();
            $db->pquery($sql, array($auditsettingtype, $department, $oneaudituid, $towaudituid));
            $db->pquery($sql2, array($auditsettingtype, $department, $oneaudituid, $towaudituid,$threeaudituid,$audituid4,$audituid5, date('Y-m-d H:i:s'), $current_user->id));
    		$data = array('flag'=>'1', 'msg'=>'添加成功');
    	} while (0);
    	$response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**导出权限添加
     * @param Vtiger_Request $request
     */
    function add(Vtiger_Request $request){
        $userid=$request->get("userid");
        $classname=$request->get("classname");
        $modulename=$request->get("modulename");
        $data='添加失败';
        do {
            $moduleModel = Vtiger_Module_Model::getInstance('ServiceContracts');
            if(!$moduleModel->exportGrouprt('ServiceContracts','ExportRM')){   //权限验证
                break;
            }
            if(empty($userid)){
                break;
            }
            //追加充值申请单(RefillApplication)判断 2017-03-03 gaocl edit
            if(empty($modulename)) {
                break;
            }
            /*if($modulename != 'ServiceContracts' && $modulename != 'ReceivedPayments' && $modulename != 'RefillApplication') {
                break;
            }*/
            if(empty($classname)){
                break;
            }
            $db=PearDatabase::getInstance();
            $mode = "('" . implode("','", $classname) . "')";
            $sql = " SELECT classnamezh ,exportmanageid FROM  vtiger_exportmanage WHERE  module = ? AND userid=? AND  deleted =0 AND  classname IN ".$mode;
            $result = $db->pquery($sql, array($modulename, $userid));
            $repeatStr='';
            while ($rawData = $db->fetch_array($result)) {
                $repeatStr.=",".$rawData['classnamezh'];
            }
            if(!empty($repeatStr)){
                $data=array('status'=>true,'message'=>"当前用户已添加权限：".trim($repeatStr,',')."，请在设置框取消该权限后再操作.");
                break;
            }
            $sql="INSERT INTO vtiger_exportmanage(userid,module,classname,classnamezh,createdid,createdtime) SELECT ?,module,mode,modename,?,? FROM vtiger_contractrpatymtable where module=? AND mode in('".implode("','",$classname)."')";
            global $current_user;
            $datetime=date('Y-m-d H:i:s');
            $db->pquery($sql,array($userid,$current_user->id,$datetime,$modulename));
            $data='添加成功';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }

    /**
     * 导出权限删除
     * @param Vtiger_Request $request
     */
    function deleted(Vtiger_Request $request){
        $moduleModel = Vtiger_Module_Model::getInstance('ServiceContracts');
        if($moduleModel->exportGrouprt('ServiceContracts','ExportRM')){   //权限验证
            global $current_user;
            $id=$request->get("id");
            $delsql="UPDATE vtiger_exportmanage SET deleted=1,deletedid=?,deletedtime=? WHERE exportmanageid=?";
            $db=PearDatabase::getInstance();
            $datetime=date('Y-m-d H:i:s');
            $db->pquery($delsql,array($current_user->id,$datetime,$id));
        }
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }

/***************start扫描枪识别不了带字母的合同编号暂时停用*************************/
    /**
     *
     * 合同领用
     * @param Vtiger_Request $request
     */
    public function Receivedold(Vtiger_Request $request){
        $userid=$request->get('userid');
        $codenumber=$request->get('inputcode');
        $superconllar=$request->get('superconllar');
        $supermanid=$request->get('supermanid');
        $mod=new ServiceContracts_Module_Model();
        $datas=array();
        do{
            if(!$mod->exportGroupri()) {
                $datas['msg']='没有权限';
                $datas['rstatus']='no_status';
                break;
            }
            $db=PearDatabase::getInstance();
            //根据公司员工编号0001-9999四位数字这里只判断为数字，长度没有做处理，合同编号不能为全数字
            if($userid==0||(is_numeric($codenumber) && $superconllar==0)){
                $datas=$this->checkuser($codenumber);
                break;
            }
            if($userid!=0 && ($supermanid==0 || is_numeric($codenumber)) && $superconllar==1){
                $datas=$this->checkuser($codenumber);
                if($datas['rstatus']!='no_status'){
                    $datas['rstatus']='superman_status';
                }else{
                    $datas['msg']='超领人--'.$datas['msg'];
                }
                break;
            }
            if(empty($codenumber)||empty($userid)){
                $datas['msg']='不正确的输入';
                $datas['rstatus']='no_status';
                break;
            }
            $status=$this->checkcontractsno(array($codenumber,1));
            if($status['rstatus']=='no_status'){
                $datas=$status;
                break;
            }
            $servicenum=ServiceContracts_Record_Model::servicecontracts_reviced($userid);
            if($servicenum && $superconllar==0){
                $datas['msg']=$servicenum;
                $datas['rstatus']='super_status';
                break;
            }
            $query="SELECT 1 FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts.contract_no=?";
            $result=$db->pquery($query,array($codenumber));
            $num=$db->num_rows($result);
            if($num>0){
                $datas['msg']='合同已领取';
                $datas['rstatus']='no_status';
                break;
            }
            unset($_REQUEST);//防止信息干扰
            $_REQUES['record']='';
            $request=new Vtiger_Request($_REQUES, $_REQUES);
            $request->set('contract_no',$codenumber);
            $request->set('assigned_user_id',$userid);
            $request->set('modulestatus', '已发放');
            if($superconllar && $supermanid){
                $_REQUEST['supercollar']=$supermanid;
            }
            $request->set('Receivedate', date('Y-m-d'));
            $request->set('module','ServiceContracts');
            $request->set('view','Edit');
            $_REQUEST['action']='SaveAjax';
            $request->set('action','SaveAjax');
            $ressorder=new ServiceContracts_Save_Action();
            $ressorder->saveRecord($request);
            $datetime=date('Y-m-d H:i:s');
            global $current_user;
            $sql="UPDATE vtiger_servicecontracts_print SET constractsstatus='c_receive',receivedtime=?,receivedid=?,doreceivedid=? WHERE servicecontracts_no=?";
            $db->pquery($sql,array($datetime,$userid,$current_user->id,$codenumber));
            $datas['userid']=$userid;
            $datas['contractno']=$codenumber;
            $datas['rstatus']='contractok';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
        exit;
    }

    /**
     * 查看工号信息
     * @param $codenumber int用户工号
     * @return mixed
     * @throws Exception
     */
    public function checkuserold($codenumber){
        $db=PearDatabase::getInstance();
        $query="SELECT vtiger_users.id,vtiger_users.usercode,vtiger_users.last_name,vtiger_users.`status`,vtiger_departments.departmentname FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE vtiger_users.`status`='Active' AND usercode=?";
        $result=$db->pquery($query,array($codenumber));
        $num=$db->num_rows($result);

        if($num<=0){
            $datas['msg']='不存在的工号';
            $datas['rstatus']='no_status';
        }else if($num==1){
            $rows=$db->query_result_rowdata($result);

            $userstatus=$rows['status']=='Active'?'':'[离职]';
            $datas['userid']=$rows['id'];
            $datas['ucode']=$rows['usercode'];
            $datas['username']=$rows['last_name'].'['.$rows['departmentname'].']'.$userstatus;
            $datas['rstatus']='userset';
        }else{
            $datas['msg']='该工号有多个人使用';
            $datas['rstatus']='no_status';
        }
        return $datas;
    }

    /**
     *
     * 查看合同编号是否可以使用
     * @param $codenumber array ,合同编号,状态1为领取,2归还
     * @return array
     * @throws Exception
     */
    public function checkcontractsnoold($codenumber){
        $db=PearDatabase::getInstance();
        $query="SELECT * FROM `vtiger_servicecontracts_print` WHERE servicecontracts_no=?";
        $result=$db->pquery($query,array($codenumber[0]));
        $num=$db->num_rows($result);
        $datas=array('rstatus'=>'ok');
        do {
            if ($num <= 0) {
                $datas['msg'] = '不存在的合同编号';
                $datas['rstatus'] = 'no_status';
                break;
            }if ($num >1) {
                $datas['msg'] = '合同编号重复';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $rows = $db->raw_query_result_rowdata($result);
            if ($rows['constractsstatus'] == 'c_receive' && $codenumber[1]==1) {
                $datas['msg'] = '合同已领取';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['constractsstatus'] == 'c_recovered') {
                $datas['msg'] = '合同已收回';
                $datas['rstatus'] = 'no_status';
                break;
            }

            if ($rows['constractsstatus'] == 'c_generated') {
                $datas['msg'] = '合同未打印';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['constractsstatus'] == 'c_print' && $codenumber[1]==2) {
                $datas['msg'] = '合同未领取';
                $datas['rstatus'] = 'no_status';
                break;
            }
        }while(0);
        return $datas;
    }

    /**
     * 合同的归还
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function Returnedold(Vtiger_Request $request){
        $userid=$request->get('userid');
        $codenumber=$request->get('inputcode');
        $mod=new ServiceContracts_Module_Model();
        $datas=array();
        $codenumber=trim($codenumber);
        do{
            if(!$mod->exportGroupri()) {
                $datas['msg']='没有权限';
                $datas['rstatus']='no_status';
                break;
            }
            $db=PearDatabase::getInstance();
            if($userid==0||is_numeric($codenumber)){
                $datas=$this->checkuser($codenumber);
                break;
            }
            if(empty($codenumber)||empty($userid)){
                $datas['msg']='不正确的输入';
                $datas['rstatus']='no_status';
                break;
            }
            $status=$this->checkcontractsno(array($codenumber,2));
            if($status['rstatus']=='no_status'){
                $datas=$status;
                break;
            }
            $query="SELECT vtiger_servicecontracts.servicecontractsid,modulestatus FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts.contract_no=?";
            $result=$db->pquery($query,array($codenumber));
            $num=$db->num_rows($result);
            if($num==0){
                $datas['msg']='合同已领取,生成错误请手工录入';
                $datas['rstatus']='no_status';
                break;
            }elseif($num>1){
                $datas['msg']='多个重复的合同编号';
                $datas['rstatus']='no_status';
                break;
            }
            $rows = $db->raw_query_result_rowdata($result);
            if($rows['modulestatus']=='c_complete'){
                $datas['msg']='合同已签收';
                $datas['rstatus']='no_status';
                break;
            }
            unset($_REQUEST);//防止信息干扰
            $_REQUES['record']=$rows['servicecontractsid'];
            $request=new Vtiger_Request($_REQUES, $_REQUES);
            //$request->set('contract_no',$codenumber);
            $request->set('Receiveid',$userid);
            $request->set('modulestatus', 'c_recovered');
            $request->set('Returndate', date('Y-m-d'));
            $request->set('module','ServiceContracts');
            $request->set('view','Edit');
            $_REQUEST['action']='SaveAjax';
            $request->set('action','SaveAjax');
            $ressorder=new ServiceContracts_Save_Action();
            $ressorder->saveRecord($request);
            $datetime=date('Y-m-d H:i:s');
            global $current_user;
            $sql="UPDATE vtiger_servicecontracts_print SET constractsstatus='c_recovered',recoveredtime=?,recoveredid=?,dorecoveredid=? WHERE servicecontracts_no=?";
            $db->pquery($sql,array($datetime,$userid,$current_user->id,$codenumber));
            $datas['userid']=$userid;
            $datas['contractno']=$codenumber;
            $datas['rstatus']='contractok';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
        exit;
    }
    /***************END扫描枪识别不了带字母的合同编号暂时停用*************************/

    /**
     * 在线签名的保存
     * @param Vtiger_Request $request
     */
    public function savesignimage(Vtiger_Request $request){
        $data=$this->createStamp($request);
        $data=base64_encode($data);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 生成签名
     * @param Vtiger_Request $request
     * @return string
     */
    private function createStamp(Vtiger_Request $request){
        $imgstring=$request->get('image');
        $recordId=microtime(true);
        $newrecordid=md5($recordId);
        global $invoiceimagepath,$root_directory;
        include $root_directory.'modules'.DIRECTORY_SEPARATOR.'ServiceContracts'.DIRECTORY_SEPARATOR.'actions'.DIRECTORY_SEPARATOR.'circleSeal.class.php';

        $imagepath=$invoiceimagepath.date('Y').'/'.date('F').'/'.date('d').'/';
        //是否是目录不是则循环创建
        is_dir($root_directory.$imagepath) || mkdir($root_directory.$imagepath,0777,true);
        //文件相对保存的路径
        $newimagepath= $imagepath.$newrecordid.'.png';
        //以文档流方式创建文件
        $stats=$request->get('stats');
        $title='';
        if($stats=='Received'){
            $title='财务服务合同领用';
        }elseif($stats=='Returned'){
            $title='财务服务合同归还';
        }elseif($stats=='NoSignReturned'){
            $title='服务合同未签归还';
        }elseif($stats=='NotSignInvalid'){
            $title='服务合同作废归还';
        }elseif($stats=='srcuser'){
            $title='合同变更原领用人';
        }elseif($stats=='dstuser'){
            $title='合同变更新领用人';
        }
        $img=imagecreatefromstring(base64_decode(str_replace('data:image/png;base64,','',$imgstring)));
        $seal = new circleSeal($title,75,9,24,0,0,20,0);
        $img2=$seal->doImgNOut();
        //取得图片的宽和高
        $invoiceimagewidth=imagesx($img);
        $invoiceimageheight=imagesy($img);
        //写入相对应的日期
        $textcolor = imagecolorallocate($img, 255, 0, 0);
        //$img若直接保存的话背影为黑色新建一个真彩图片背景为白色让两张图片合并$img为带a的通道
        $other=imagecreatetruecolor($invoiceimagewidth,$invoiceimageheight);
        $white=imagecolorallocate($img, 255, 255, 255);
        //$other 填充为白色
        imagefill($other,0,0,$white);
        $datetime=date('Y-m-d H:i');
        //将日期写入$img中
        imagestring($img,5,$invoiceimagewidth-200,$invoiceimageheight-60,$datetime,$textcolor);
        //合并图片

        imagecopy($other,$img,0,0,0,0,$invoiceimagewidth,$invoiceimageheight);
        imagecopy($other,$img2,$invoiceimagewidth-$invoiceimagewidth/4,$invoiceimageheight/3,0,0,150,150);
        //保存图片
        imagepng($other,$root_directory.$newimagepath);
        //释放资源
        imagedestroy($img);
        imagedestroy($img2);
        imagedestroy($other);
        return $newimagepath;
    }
    /**
     * 写入签名
     * @param Vtiger_Request $request
     * @param $arr
     */
    private function dosignContracts($arr){
        global $current_user, $adb;
        $recordId = $arr['recordId'];
        $userid = $arr['userid'];
        //$sequence = $arr['sequence'];
        $workflowstagesflag = $arr['workflowstagesflag'];
        $newimagepath = base64_decode($arr['signpath']);
        $newrecordid = base64_encode($recordId);
        $datetime = date('Y-m-d H:i:s');
        $sql = 'INSERT INTO `vtiger_invoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
        $adb->pquery($sql, array($recordId, $newimagepath, $newrecordid, 'ServiceContracts', $datetime, $current_user->id));
        $adb->pquery('update vtiger_salesorderworkflowstages set auditorid=?,auditortime=?,schedule=?,isaction=?,isrejecting=? where salesorderid =? and workflowstagesflag=? and modulename=?', array($userid, $datetime, 100, 2, 0, $recordId, $workflowstagesflag, 'ServiceContracts'));
        // cxh 2019-08-17 start
        if($arr['controllerType']=='Received' || $arr['controllerType']=='Returned'){
            if($arr['controllerType']=='Returned'){
                $adb->pquery('update vtiger_salesorderworkflowstages set modulestatus=? where salesorderid =? and modulename=? AND isaction=2 ', array('c_complete',$recordId,'ServiceContracts'));
            }
            //获取发起人信息并提示发起人
            $stageSql = " SELECT s.salesorderworkflowstagesid,s.productid,s.higherid,s.ishigher,s.salesorderid,w.isrole,s.workflowstagesname,s.salesorder_nono,s.accountname,s.modulename,actiontime,(SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id=s.smcreatorid limit 1 ) as smcreator  FROM  vtiger_salesorderworkflowstages as s LEFT JOIN vtiger_workflowstages as w ON s.workflowstagesid = w.workflowstagesid LEFT JOIN vtiger_users as u ON u.id=s.smcreatorid WHERE  1=1  AND  s.workflowstagesflag=? AND   s.modulename='ServiceContracts'  AND  s.salesorderid = ? LIMIT 1  ";
            $result = $adb->pquery($stageSql, array($workflowstagesflag,$recordId));
            if($adb->num_rows($result)){
                $userEmailStr = '';
                $email = $adb->query_result($result,0,'email1');
                $actiontime = $adb->query_result($result,0,'actiontime');
                $module = $adb->query_result($result,0,'modulename');
                $modulename = vtranslate($module,'Vtiger','zh_cn');
                $smcreator  = $adb->query_result($result,0,'smcreator');
                $salesorder_nono = $adb->query_result($result,0,'salesorder_nono');
                $accountname = $adb->query_result($result,0,'accountname');
                $workflowstagesname = $adb->query_result($result,0,'workflowstagesname');
                //邮箱验证
                if($this->checkEmail(trim($email))){
                    $userEmailStr = trim($email);
                    $title="您的申请审核更新啦";
                    // 公共审核消息发送模板
                    $object = new SalesorderWorkflowStages_SaveAjax_Action();
                    $object->sendMsgToUser($title,$userEmailStr,$actiontime,$modulename,$smcreator,$salesorder_nono,$accountname,$workflowstagesname,$module,$recordId);
                }
                //如果是领取合同人 或者归还合同人的工号 给这个工号的人发送消息提醒
                $inputcode = $arr['inputcode'];
                $query = "SELECT vtiger_users.id,vtiger_users.email1,vtiger_users.usercode,vtiger_users.last_name,vtiger_users.`status`,vtiger_departments.departmentname FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE vtiger_users.`status`='Active' AND userid=? ";
                $result = $adb->pquery($query, array($userid));
                $email = $adb->query_result($result,0,'email1');
                //邮箱验证
                if($this->checkEmail(trim($email))){
                    $userEmailStr = trim($email);
                    //公共审核消息发送模板
                    $title = $arr['controllerType']=='Received' ? '您已领取合同':'您已归还合同';
                    $object = new SalesorderWorkflowStages_SaveAjax_Action();
                    // 下面参数共用 上面提单人参数除了 $title,$userEmailStr
                    $object->sendMsgToUser($title,$userEmailStr,$actiontime,$modulename,$smcreator,$salesorder_nono,$accountname,$workflowstagesname,$module,$recordId);
                }
            }

        }
        // cxh 2019-08-17 end
    }
    /**
     * 邮件格式验证
     * @param $str
     * @return bool
     */
    public function checkEmail($str){
        $str=trim($str);
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }

    //修改合同领取人，验证信息
    public function changeLeadCheak(Vtiger_Request $request)
    {
        $inputcode=$request->get('inputcode');
        $type = $request->get('type');
        do {
            if (empty($inputcode)) {
                $data['rstatus']='error';
                $data['msg']='无效的输入';
                break;
            }
            $codenumber = preg_replace('/-8$/','',$inputcode);
            global $adb;
            if ($type == 'srcuser') {
                $new_codenumber=str_pad($codenumber, 6, '0', STR_PAD_LEFT);
                $query="SELECT vtiger_users.id,
                            vtiger_users.last_name,
                            vtiger_departments.departmentname
                        FROM vtiger_users
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                        WHERE vtiger_users.`status`='Active' AND vtiger_users.usercode=?";
                $result = $adb->pquery($query,array($new_codenumber));
                $num=$adb->num_rows($result);
                if ($num<=0) {
                    $data['rstatus'] = 'error';
                    $data['msg'] = '工号不存在或账号已被禁用!';
                } else if($num == 1) {
                    $userInfo = $adb->fetch_row($result);
                    $data['rstatus'] = 'success';
                    $data['id'] = $userInfo['id'];
                    $data['username'] = $userInfo['last_name'].'【'.$userInfo['departmentname'].'】';
                } else {
                    $data['rstatus'] = 'error';
                    $data['msg']='工号重复!';
                }
                break;
            } elseif ($type == 'dstuser') {
                $new_codenumber=str_pad($codenumber, 6, '0', STR_PAD_LEFT);
                $query = "SELECT vtiger_users.id,
                            vtiger_users.last_name,
                            vtiger_departments.departmentname
                            FROM vtiger_users
                            LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                            LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                            WHERE vtiger_users.`status`='Active' AND vtiger_users.usercode=?";
                $resultUser=$adb->pquery($query, array($new_codenumber));
                $num=$adb->num_rows($resultUser);
                if ($num <= 0) {
                    $data['rstatus'] = 'error';
                    $data['msg'] = '工号不存在或账号已被禁用!';
                } elseif ($num == 1) {
                    $data['rstatus']='success';
                    $userInfo = $adb->fetch_row($resultUser);
                    $data['id'] = $userInfo['id'];
                    $data['username'] = $userInfo['last_name'].'【'.$userInfo['departmentname'].'】';
                    $recordModel = Vtiger_Record_Model::getCleanInstance('ServiceContracts');
                    $reviced = $recordModel->servicecontracts_reviced($userInfo['id']);
                    if ($reviced) {
                        $data['rstatus'] = 'error';
                        $data['msg']='新领用人有' . $reviced . '份合同没有归还,不允许操作';
                    }
                } else {
                    $data['rstatus']='error';
                    $data['msg']='工号重复!';
                }
            }
        } while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    //预保存签名
    public function preSaveChangeLeadSign(Vtiger_Request $request) {
        $newimagepath=$this->createStamp($request);
        $stats=$request->get('stats');
        $data = ['signpath'=> $newimagepath];
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    public function changeLead(Vtiger_Request $request) {
        $contactNo = trim($request->get('contactNo'));
        $srcuserid = $request->get('srcuserid');
        $dstuserid = $request->get('dstuserid');
        $srcUserSignPath = $request->get('srcUserSignPath');
        $dstUserSignPath = $request->get('dstUserSignPath');
        do{
            if (empty($contactNo)) {
                $data['rstatus'] = 'error';
                $data['msg'] = '合同编号不能为空!';
                break;
            }
            if (empty($srcuserid)) {
                $data['rstatus'] = 'error';
                $data['msg'] = '原合同领取人不能为空!';
                break;
            }
            if (empty($dstuserid)) {
                $data['rstatus'] = 'error';
                $data['msg'] = '原合同领取人签名不能为空!';
                break;
            }
            if (empty($srcUserSignPath)) {
                $data['rstatus'] = 'error';
                $data['msg'] = '新合同领取人不能为空!';
                break;
            }
            if (empty($dstUserSignPath)) {
                $data['rstatus'] = 'error';
                $data['msg'] = '新合同领取人签名不能为空!';
                break;
            }
            $codenumber = preg_replace('/-8$/','',$contactNo);
            if (is_numeric($codenumber)) {
                $consql = ' AND servicecontractsprintid=?';
            } else {
                $consql = ' AND contract_no=?';
            }
            global $current_user,$adb;
            $query = "SELECT servicecontractsid, contract_no, vtiger_crmentity.smownerid FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='已发放'".$consql;
            $result=$adb->pquery($query,array($codenumber));
            $num = $adb->num_rows($result);
            if ($num <= 0) {
                $data['rstatus']='error';
                $data['msg']='只有已发放的合同才能更改领用人';
                break;
            }
            $serviceContractInfo = $adb->fetch_row($result);
            if ($serviceContractInfo['smownerid'] != $srcuserid) {
                $data['rstatus']='error';
                $data['msg']='合同当前领取人和原领取人不一致';
                break;
            }
            $recordId = $serviceContractInfo['servicecontractsid'];
            $newrecordid=base64_encode($recordId);
            $datetime=date('Y-m-d H:i:s');
            $_REQUEST['record'] = $recordId;
            $request=new Vtiger_Request($_REQUEST, $_REQUEST);
            $request->set('assigned_user_id',$dstuserid);
            $request->set('module','ServiceContracts');
            $request->set('Receivedate', date('Y-m-d'));
            $_REQUEST['action']='SaveAjax';
            $request->set('action','SaveAjax');
            $request->set('view','Edit');
            $ressorder=new ServiceContracts_Save_Action();
            $ressorder->saveRecord($request);
            $query='SELECT last_name,email1,vtiger_departments.departmentname,wechatid FROM vtiger_users
                LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                WHERE id=?';
            $userResult1 = $adb->pquery($query,array($srcuserid));
            $userResult2 = $adb->pquery($query,array($dstuserid));
            $content='合同编号：'.$serviceContractInfo['contract_no'].'<br>原领取人：'.$userResult1->fields['last_name'].'【'.$userResult1->fields['departmentname'].'】'.'<br>新领取人：'.$userResult2->fields['last_name'].'【'.$userResult2->fields['departmentname'].'】'.'<br>变更时间：'.date('Y-m-d H:i');
            $recordModel = new Vtiger_Record_Model();
            $recordModel->sendWechatMessage(array('email'=>$userResult1->fields['wechatid'].'|'.$userResult2->fields['email1'],'description'=>$content,'dataurl'=>'#','title'=>'【合同变更提醒】','flag'=>7));
            $sql = 'INSERT INTO `vtiger_invoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
            $adb->pquery($sql,array($recordId, $srcUserSignPath, $newrecordid, 'ServiceContracts', $datetime, $current_user->id));
            $sql = 'INSERT INTO `vtiger_invoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
            $adb->pquery($sql, array($recordId, $dstUserSignPath, $newrecordid, 'ServiceContracts', $datetime, $current_user->id));
            $data['rstatus'] = 'success';
            $data['msg'] = sprintf('成功更改合同%s的领取人', $serviceContractInfo['contract_no']);
            $data['contractNo'] = $serviceContractInfo['contract_no'];
        } while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /*public function changeLead(Vtiger_Request $request){
        $inputcode=$request->get('inputcode');
        $recordid=$request->get('recordid');
        $srcuserid=$request->get('srcuserid');
        $dstuserid=$request->get('dstuserid');
        $inputcode=trim($inputcode);
        do{
            if(empty($inputcode)){
                $data['rstatus']='empty';
                $data['msg']='无效的输入';
                break;
            }
            global $adb;
            $codenumber=preg_replace('/-8$/','',$inputcode);
            if($recordid<=0){
                if(is_numeric($codenumber)){
                    $consql=' AND servicecontractsprintid=?';
                }else{
                    $consql=' AND contract_no=?';
                }
                $query="SELECT vtiger_servicecontracts.servicecontractsid FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='已发放'".$consql;
                $result=$adb->pquery($query,array($codenumber));
                $num=$adb->num_rows($result);
                if($num==1){
                    $data['rstatus']='contOK';
                    $servicecontractsid=$adb->fetch_row($result);
                    $data['msg']=$servicecontractsid['servicecontractsid'];
                }elseif($num==0){
                    $data['rstatus']='msgerr';
                    $data['msg']='只有已发放的合同才能更改领用人';
                }else{
                    $data['rstatus']='msgerr';
                    $data['msg']='合同编号重复';
                }
                break;
            }
            $new_codenumber=str_pad($codenumber, 6, '0', STR_PAD_LEFT);
            if($srcuserid<=0){
                $query="SELECT
                            vtiger_crmentity.smownerid,
                            vtiger_users.last_name,
                            vtiger_departments.departmentname
                        FROM
                            `vtiger_servicecontracts`
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                        LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_servicecontracts.modulestatus='已发放'
                        AND vtiger_servicecontracts.servicecontractsid =?
                        AND vtiger_users.usercode =?";
                $result=$adb->pquery($query,array($recordid,$new_codenumber));
                $num=$adb->num_rows($result);
                if($num==1){
                    $data['rstatus']='srcuserOK';
                    $smownerid=$adb->fetch_row($result);
                    $data['msg']=$smownerid['smownerid'];
                    $data['username']=$smownerid['last_name'].'【'.$smownerid['departmentname'].'】';
                }elseif($num==0){
                    $data['rstatus']='msgerr';
                    $data['msg']='当前用户不是合同领取人';
                }else{
                    $data['rstatus']='msgerr';
                    $data['msg']='合同编号重复';
                }
                break;
            }
            if($dstuserid<=0){
                $query="SELECT id,
                            vtiger_users.last_name,
                            vtiger_departments.departmentname
                             FROM vtiger_users
                              LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                              WHERE `status`='Active' AND usercode=?";
                $resultUser=$adb->pquery($query,array($new_codenumber));
                $numUser=$adb->num_rows($resultUser);
                if($numUser<=0){
                    $data['rstatus']='msgerr';
                    $data['msg']='工号不存在或账号禁用!';
                }elseif($numUser==1){
                    $data['rstatus']='dstuserOK';
                    $smownerid=$adb->fetch_row($resultUser);
                    $data['msg']=$smownerid['id'];
                    $data['username']=$smownerid['last_name'].'【'.$smownerid['departmentname'].'】';
                    $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
                    if($recordModel->servicecontracts_reviced($smownerid['id'])){
                        $query="SELECT
                            vtiger_users.id
                        FROM
                            `vtiger_servicecontracts`
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                        LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_servicecontracts.modulestatus='已发放'
                        AND vtiger_users.usercode =?";
                        $result=$adb->pquery($query,array($new_codenumber));
                        $num=$adb->num_rows($result);
                        $data['rstatus']='msgerr';
                        $data['msg']='新领用人有'.$num.'份合同没有归还,不允许操作';
                    }
                }else{
                    $data['rstatus']='msgerr';
                    $data['msg']='工号重复!';
                }
                break;
            }
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }*/

   /* public function saveChangeLeadSign(Vtiger_Request $request){
        global $current_user,$adb;
        $recordId=$request->get('recordid');
        $newimagepath=$this->createStamp($request);
        $newrecordid=base64_encode($recordId);
        $datetime=date('Y-m-d H:i:s');
        $stats=$request->get('stats');
        if($stats=='dstuser'){
            $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ServiceContracts');
            $orderuserid=$recordModel->get('assigned_user_id');
            $dstuserid=$request->get('dstuserid');
            $_REQUEST['record']=$recordId;
            $request=new Vtiger_Request($_REQUEST, $_REQUEST);
            $request->set('assigned_user_id',$dstuserid);
            $request->set('module','ServiceContracts');
            $request->set('Receivedate', date('Y-m-d'));
            $_REQUEST['action']='SaveAjax';
            $request->set('action','SaveAjax');
            $request->set('view','Edit');
            $ressorder=new ServiceContracts_Save_Action();
            $ressorder->saveRecord($request);
            $query='SELECT last_name,email1,vtiger_departments.departmentname FROM vtiger_users
            LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
            LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
            WHERE id=?';
            $userResult1=$adb->pquery($query,array($orderuserid));
            $userResult2=$adb->pquery($query,array($dstuserid));
            $content='合同编号：'.$recordModel->get('contract_no').'<br>原领取人：'.$userResult1->fields['last_name'].'【'.$userResult1->fields['departmentname'].'】'.'<br>新领取人：'.$userResult2->fields['last_name'].'【'.$userResult2->fields['departmentname'].'】'.'<br>变更时间：'.date('Y-m-d H:i');
            $recordModel->sendWechatMessage(array('email'=>$userResult1->fields['email1'].'|'.$userResult2->fields['email1'],'description'=>$content,'dataurl'=>'#','title'=>'【合同变更提醒】','flag'=>7));
        }
        $sql = 'INSERT INTO `vtiger_invoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
        $adb->pquery($sql,array($recordId,$newimagepath,$newrecordid,'ServiceContracts',$datetime,$current_user->id));
        $data=$stats;
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    */

    /**
     * 超领用户设置-添加
     * @param Vtiger_Request $request
     */
    public function addExceedingNumber(Vtiger_Request $request){
        $userid=$request->get("userid");
        $protectnum=$request->get("cnumber");
        $data='添加失败';
        do {
            $moduelModel=Vtiger_Module_Model::getInstance('ServiceContracts');
            if(!$moduelModel->exportGrouprt('ServiceContracts','protected')){
                break;
            }
            if(empty($userid)){
                break;
            }
            if(empty($protectnum)){
                break;
            }
            $sql="INSERT INTO vtiger_contractexceedingnumber(userid,cnumber) VALUES(?,?)";
            $delsql="DELETE FROM vtiger_contractexceedingnumber WHERE userid=?";
            $db=PearDatabase::getInstance();
            $db->pquery($delsql,array($userid));
            $db->pquery($sql,array($userid,$protectnum));
            $data='添加成功';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 超领合同用户设置-删除
     * @param Vtiger_Request $request
     */
    public function deletedExceedingNumber(Vtiger_Request $request){
        $id=$request->get("id");
        $moduelModel=Vtiger_Module_Model::getInstance('ServiceContracts');
        if($moduelModel->exportGrouprt('ServiceContracts','protected')){
            $delsql="DELETE FROM vtiger_contractexceedingnumber WHERE userid=?";
            $db=PearDatabase::getInstance();
            $db->pquery($delsql,array($id));
        }
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 读取用户的超领数量
     * @param $userid
     * @return int
     */
    public function getCExceedingNumber($userid){
        global $adb;
        $query="SELECT vtiger_contractexceedingnumber.cnumber FROM `vtiger_contractexceedingnumber` WHERE userid=? limit 1";
        $result=$adb->pquery($query,array($userid));
        $number=5;
        $datanumber=0;
        if($adb->num_rows($result)){
            $data=$adb->raw_query_result_rowdata($result,0);
            $datanumber=$data['cnumber'];
        }
        return $number+$datanumber;
    }
    /**
     * 服务合同签收时如果提交的分成单未审核完成给予提示
     */
    public function getCheckInvoice(Vtiger_Request $request){
        global $adb;
        $record=$request->get("record");
        $query = "select * from vtiger_separateinto where servicecontractsid=? and modulestatus!='c_complete' and  modulestatus!='a_exception'";
        $result=$adb->pquery($query,array($record));
        $data = $adb->fetch_row($result);
        $response = new Vtiger_Response();
        if(!$data){
            $data='success';
//            echo json_encode(array('error'=>'success','result'=>$data));
             $response->setResult($data);
        }else{
            $data='fail';
             $response->setResult($data);
        }
       $response->emit();
    }
    /**
     * 回款业绩添加分成比列
     */
    public function addDivided(){
        global $adb;
        global $current_user;
        $arr_data = $_REQUEST['arr_data'];

        $suoshugongsi = $_REQUEST['suoshugongsi'];
        $suoshuren = $_REQUEST['suoshuren'];
        $biliren = $_REQUEST['biliren'];
        $recordid = $_REQUEST['recordid'];
//        $receivedPayment=$_REQUEST['receivedPayment'];
        $sql = "INSERT INTO `vtiger_servicecontracts_divide_tmp` (owncompanys, receivedpaymentownid,scalling, servicecontractid,signdempart) SELECT ?,?,?,?,vtiger_user2department.departmentid FROM vtiger_user2department WHERE vtiger_user2department.userid=?";
        if(empty($suoshugongsi)) {
            $response = new Vtiger_Response();
            $response->setError(40001,'分成信息不能为空');
            $response->emit();
            exit;
        }
        $result=$adb->pquery("SELECT * FROM `vtiger_custompowers` WHERE custompowerstype='separateintoauditing' LIMIT 1",array());
        $data=$adb->raw_query_result_rowdata($result,0);
        $roles=explode(',',$data['roles']);
        //添加到工作流
        $workflowsid=2206489;
        $workflowstagesid=2206491;
        $actiontime=date('Y-m-d H:i:s');
        $adb->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE workflowsid =? and salesorderid=?", array($workflowsid,$recordid)); //删除同一个工作流id
        $sqlsub = "INSERT INTO vtiger_salesorderworkflowstages (workflowstagesname,workflowstagesid,sequence,salesorderid,isaction,actiontime,addtime,workflowsid,modulename,smcreatorid,createdtime,productid,departmentid,ishigher,higherid,workflowstagesflag) values (?,?,?,?,?,?, NOW(),?,?,?,NOW(),?,?,?,?,?)";

        //查询服务合同客户id
        $account = $adb->pquery("SELECT sc_related_to as accountid FROM `vtiger_servicecontracts` WHERE servicecontractsid=? LIMIT 1",array($recordid));
        $accountData = $adb->raw_query_result_rowdata($account,0);
        //查询客户信息 看看客户是不是来自市场部
        $account = $adb->pquery("SELECT frommarketing FROM `vtiger_account` WHERE accountid=? LIMIT 1",array($accountData['accountid']));
        $accountData = $adb->raw_query_result_rowdata($account,0);
        $isPlus=0;
        //如果是来自于市场部则添加一条戴子龙审核分成单
        if($accountData['frommarketing']==1){
            $isPlus+=1;
            $reports_to_id =19;
            $adb->pquery($sqlsub,array('市场负责人审核',$workflowstagesid,$isPlus,$recordid,1,$actiontime,$workflowsid,'ServiceContracts',$current_user->id,0,$current_user->current_user_parent_departments,1,$reports_to_id,''));
        }
        for ($i=0;$i<count($suoshugongsi);++$i){
            if(!empty($suoshuren[$i])){
                $adb->pquery($sql,array($suoshugongsi[$i],$suoshuren[$i],$biliren[$i],$recordid,$suoshuren[$i]));
                $reportsModel = Users_Privileges_Model::getInstanceById($suoshuren[$i]);
                $reports_to_id = $this->findreport($reportsModel->reports_to_id, $roles);
                $temp = $i + 1+$isPlus;
                $isaction = 0;
                if ($i == 0 && $isPlus==0) {
                    $isaction = 1;
                }
                // 如果直属上级是赵总 则修改审核人为当前分成人
                if($reports_to_id==38){
                    $adb->pquery($sqlsub,array('分成人<'.$reportsModel->last_name.'>审核',$workflowstagesid,$temp,$recordid,$isaction,$actiontime,$workflowsid,'ServiceContracts',$current_user->id,0,$current_user->current_user_parent_departments,1,$reportsModel->id,''));
                }else {
                    $adb->pquery($sqlsub, array('分成人<' . $reportsModel->last_name . '>上级审核', $workflowstagesid, $temp, $recordid, $isaction, $actiontime, $workflowsid, 'ServiceContracts', $current_user->id, 0, $current_user->current_user_parent_departments, 1, $reports_to_id, ''));
                }
            }
        }
        // 2020-03-30 去除添加工作流审核
        /*// 如果包含回款分成修改审核则再添加一个节点
        if(!empty($receivedPayment)){
            $temp+=1;
            $reports_to_id =19;
            // 这里的第五个参数指的是排序 设定100 指的是是最终的审核 即排在最后一个审核节点
            $adb->pquery($sqlsub,array('回款业绩修改财务审批',$workflowstagesid,$temp,$recordid,0,$actiontime,$workflowsid,'ServiceContracts',$current_user->id,0,$current_user->current_user_parent_departments,1,$reports_to_id,'UPDATERECEIVEMENTDIVIDE'));
            $sqldelete="DELETE FROM `vtiger_servicecontracts_update_receivedpayments_divide_tmp` WHERE (`servicecontractsid`=?)";
            $adb->pquery($sqldelete,array($recordid));
            $sqltmp="INSERT INTO `vtiger_servicecontracts_update_receivedpayments_divide_tmp` (`receivedpaymentsids`, `servicecontractsid`) VALUES (?, ?)";
            $receivedPayment=implode(",",$receivedPayment);
            $adb->pquery($sqltmp,array($receivedPayment,$recordid));
        }*/
        $query ="UPDATE vtiger_salesorderworkflowstages,
                 vtiger_separateinto
                SET vtiger_salesorderworkflowstages.accountid=vtiger_separateinto.accountid,
                 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_crmentity.label FROM vtiger_crmentity WHERE vtiger_crmentity.crmid=vtiger_separateinto.accountid)
                WHERE vtiger_separateinto.separateintoid=vtiger_salesorderworkflowstages.salesorderid
                AND vtiger_salesorderworkflowstages.salesorderid=?";
        $adb->pquery($query,array($recordid));
        $query="UPDATE vtiger_separateinto SET modulestatus='b_check',workflowsid={$workflowsid} WHERE separateintoid=?";
        $adb->pquery($query,array($recordid));

        $query="UPDATE vtiger_servicecontracts SET backstatus=modulestatus,modulestatus='b_actioning' WHERE servicecontractsid=?";
        $adb->pquery($query,array($recordid));
//            //添加到工作流
//            $_REQUEST['workflowsid'] = 2173824 ;
//            $focus = CRMEntity::getInstance('ServiceContracts');
//            $focus->makeWorkflows('ServiceContracts', $_REQUEST['workflowsid'], $recordid);
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$recordid,'salesorderworkflowstagesid'=>0));

        $response = new Vtiger_Response();
        $response->emit();
    }

    /**
     * 获取有审核权限的上级ID
     * @param $reports_to_id
     * @param $roles
     * @return int
     * @throws Exception
     */
    public function findreport($reports_to_id, $roles)
    {
        if ($reports_to_id == '38') {
            return $reports_to_id;
        }
        global $adb;
        $sql = "SELECT vtiger_users.reports_to_id, vtiger_user2role.roleid, vtiger_users.`status` FROM vtiger_users LEFT JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid WHERE vtiger_users.id=?";
        $result = $adb->pquery($sql, array($reports_to_id));
        if ($adb->num_rows($result) == 0) {
            return 6934;
        }
        $userInfo = $adb->query_result_rowdata($result, 0);
        if ($userInfo['status']!= 'Active') {
            //如果已离职且无直属上级(理论上，不存在此种情况)
            if (empty($userInfo['reports_to_id'])) {
                return 38;
            }
            return $this->findreport($userInfo['reports_to_id'], $roles);
        }
        if (in_array($userInfo['roleid'], $roles) || empty($userInfo['reports_to_id']) || $userInfo['reports_to_id'] == '38') {
            return $reports_to_id;
        }
        return $this->findreport($userInfo['reports_to_id'], $roles);
    }

    /**
     * 判断是否可以编辑合同详情页的分成
     * @author cxh
     * @param Vtiger_Request $request
     */
    public function isCanDivided(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $data = $recordModel->isCanDivided($request);
        //员工列表
        $data['staffList'] = get_username_array_divide('1=1');
        if($data['accountid']){
            $separateIntoRecordModel = SeparateInto_Record_Model::getCleanInstance("SeparateInto");
            $shareInfo = $separateIntoRecordModel->getMarketingShareInfo($data['accountid']);
            $data['shareInfo']=$shareInfo;
        }
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 得到汇款数据
     */
    public function getReceivement(Vtiger_Request $request){
        global $adb;
        $recorId=$request->get("record");
        $sql="SELECT * FROM vtiger_receivedpayments WHERE relatetoid=? AND  receivedstatus='normal' AND NOT EXISTS(SELECT 1 FROM vtiger_achievementallot_statistic WHERE receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid AND vtiger_achievementallot_statistic.isover=1) ";
        $divideSql="SELECT *,(SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_achievementallot.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownname FROM `vtiger_achievementallot` WHERE receivedpaymentsid = ? ";
        //$result = $adb->pquery($sql,array(379863));//本地测试用的
        $data=array();
        $result = $adb->pquery($sql,array($recorId));
        while($rawData=$adb->fetch_array($result)) {
            $divideInfo='';
            $rawData['receivedstatus']=vtranslate($rawData['receivedstatus'],"ReceivedPayments");
            $divideResult= $adb->pquery($divideSql,array($rawData['receivedpaymentsid']));
            while ($rowDatas=$adb->fetch_array($divideResult)){
                $divideInfo.="<p><".$rowDatas['receivedpaymentownname'].">".$rowDatas['scalling']."%</p>";
            }
            $rawData['divideInfo']=$divideInfo;
            $data[] = $rawData;
        }
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }


    /**
     * @param Vtiger_Request $request
     */

    function addProduct2Code(Vtiger_Request $request) {
        $products_codeid = $request->get("products_codeid");
        $productidcode = $request->get("productidcode");
        $productname = $request->get("productname");
        $servicecontractstype = $request->get("servicecontractstype");
        $productid = $request->get('productid');
        $ispackage = $request->get('ispackage');
        $data = array('flag'=>'0', 'msg'=>'添加失败');
        $response = new Vtiger_Response();
        do {
            $moduleModel = Vtiger_Module_Model::getInstance('ServiceContracts');
            if(!$moduleModel->exportGrouprt('ServiceContracts','ProductsCodeProductId')){   //权限验证
                break;
            }
            if (empty($products_codeid)) {
                break;
            }
            if (empty($productname)) {
                break;
            }
            if (empty($servicecontractstype)) {
                break;
            }

            $db=PearDatabase::getInstance();
            switch ($servicecontractstype){
                case "all":
                    $types = array('buy','renew','upgrade','degrade');
                    $sql2 = "select 1 from vtiger_products_code_productid where tyunproductid=? and ispackage=? and productid=?";
                    $existResults = $db->pquery($sql2,array($productid,$ispackage,$productidcode));
                    if($db->num_rows($existResults)){
                        $data = array('flag'=>'0', 'msg'=>'存在该产品');
                        $response->setResult($data);
                        $response->emit();
                        exit();
                    }
                    $sql = "insert into vtiger_products_code_productid(`productname`,`servicecontractstype`,`products_codeid`,`tyunproductid`,`productid`,`ispackage`) values";
                    foreach ($types as $type){
                        $sql .= "('".$productname."','".$type."',".$products_codeid.",'".$productid."',".$productidcode.",".$ispackage."),";
                    }
                    $sql = rtrim($sql,',');

                    $db->pquery($sql, array());
                    break;
                default:
                    $sql2 = "select 1 from vtiger_products_code_productid where tyunproductid=? and ispackage=? and productid=? and servicecontractstype=?";
                    $existResults = $db->pquery($sql2,array($productid,$ispackage,$productidcode,$servicecontractstype));
                    if($db->num_rows($existResults)){
                        $data = array('flag'=>'0', 'msg'=>'已存在该合同类型产品');
                        $response->setResult($data);
                        $response->emit();
                        exit();
                    }
                    $sql = "insert into vtiger_products_code_productid(`productname`,`servicecontractstype`,`products_codeid`,`tyunproductid`,`productid`,`ispackage`) values(?,?,?,?,?,?)";
                    $db->pquery($sql, array($productname,  $servicecontractstype,$products_codeid,$productid,$productidcode,$ispackage));
                    break;
            }
            $data = array('flag'=>'1', 'msg'=>'添加成功');

            $recordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
            $recordModel->syncProductToFangXinQian(array(
                'productName'=>$productname,
                "productCode"=>$productid,
            ));
        } while (0);
        $response->setResult($data);
        $response->emit();
    }


    /**
     * 删除产品和合同编码对应
     *
     * @param Vtiger_Request $request
     */
    function deletedProduct2Code(Vtiger_Request $request) {
        $moduleModel = Vtiger_Module_Model::getInstance('ServiceContracts');
        if ($moduleModel->exportGrouprt('ServiceContracts', 'ProductsCodeProductId')) {   //权限验证
            global $current_user;
            $id = $request->get("id");
            $delsql = "delete from vtiger_products_code_productid where products_code_productidid=?";
            $db = PearDatabase::getInstance();
            $db->pquery($delsql, array($id));
        }
        $data = '更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

/**
     * 电子合同撤销
     */
    public function doRevokeSending($request){
        global $current_user,$configcontracttypeNameTYUN,$adb;
        $recordId=$request->get('record');
        $emstatus=$request->get('emstatus');
        $inputusername=$request->get('inputusername');
        $inputmobile=$request->get('inputmobile');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ServiceContracts');
        $returnData=array('flag'=>false,'msg'=>'');
        do{
            if($recordModel->get('signaturetype')!='eleccontract'){
                $returnData['msg']='非电子合同拒绝操作！';
                break;
            }
            if(!in_array($recordModel->get('eleccontractstatus'),array('b_elec_actioning'))){
                $returnData['msg']='电子合同拒绝操作！';
                break;
            }
            if($recordModel->get('Receiveid')!=$current_user->id && $current_user->is_admin!='on'){
                $returnData['msg']='非提单人拒绝操作！';
                break;
            }
            if($emstatus==1){
                /*if(empty($inputusername)){
                    $returnData['msg']='接收人有误！';
                    break;
                }
                if(!preg_match("/^1[3456789]\d{9}$/",$inputmobile)){
                    $returnmsg['msg']='手机号码无效';
                    break;
                }*/
            }
            if($emstatus==2){
                if(in_array($recordModel->get('contract_type'))){
                    $returnData['msg']='T云合同不支持修改合同内容！';
                    break;
                }
                if($recordModel->get('sideagreement')==1){
                    $returnData['msg']='补充协议不支持该操作！';
                    break;
                }
            }
            if($emstatus==3){
                $detailViewModel=Vtiger_DetailView_Model::getInstance('ServiceContracts',$recordId);
                if(!$detailViewModel->getContractVoid($recordId)){
                    $returnData['msg']='存在发票,充值单,回款或工单请先处理！';
                    break;
                }
            }
            $contractId=$recordModel->get('eleccontractid');
            if($contractId==0){
                $returnData['msg']='放心签合同不存在！';
                break;
            }
            $newemstatus=$emstatus==1?1:($emstatus==2?3:2);//1：撤回并发送  2：仅撤回  3：撤回并修改合同",
            $args=array("contractId"=>$contractId,
                    "type"=>($newemstatus==1?1:2),
                    //"name"=>$inputusername,
                    //"phone"=>$inputmobile,
                    "name"=>$recordModel->get('elereceiver'),
                    "phone"=>$recordModel->get('elereceivermobile')
            );
            $backData=$recordModel->elecCommonBack($args);
            $jsonData=json_decode($backData,true);
            if($recordModel->get('contractattribute')=='customized'){
                $file=$recordModel->get('file');
                $files=explode('##',$file);
                $filesname=strtolower($files[0]);
                $filesname=trim($filesname,'.doc');
                $filesname=trim($filesname,'.docx');
                $eleccontracttpl=trim($filesname,'.pdf');
            }else{
                $eleccontracttpl=$recordModel->get('eleccontracttpl');
            }
            if($jsonData['success']){
                //$recordModel->fileSave($jsonData['data'],'files_style5');//保存合同附件
                if(1==$emstatus){//撤回并发送
                    $sql="UPDATE vtiger_servicecontracts SET receivedate=? WHERE servicecontractsid=?";
                    $adb->pquery($sql,array(date('Y-m-d'),$recordId));
                    $recordModel->sendSMS(array('statustype'=>'','mobile'=>$recordModel->get('elereceivermobile'),'eleccontracttpl'=>$eleccontracttpl,'url'=>$recordModel->elecContractUrl));
                    $recordModel->sendMailFXQ();
                    $recordModel->setModTracker('ServiceContracts',$recordId,array('Receivedate'=>array('oldValue'=>$recordModel->get('Receivedate'),'currentValue'=>date('Y-m-d'))));
                    $recordModel->syncVerifyResultToWk($recordId,$recordModel->elecContractUrl);

                }elseif(2==$emstatus){//撤回修改合同内容后发送
                    $sql="UPDATE vtiger_servicecontracts SET modulestatus=?,eleccontractstatus=? WHERE servicecontractsid=?";
                    $adb->pquery($sql,array('a_normal','a_elec_withdraw',$recordId));
                    $recordModel->sendSMSComm(array("mobile"=>$recordModel->get('elereceivermobile'),"content"=>'您好，珍岛集团已撤回合同《'.$eleccontracttpl.'》'));
                    $recordModel->setModTracker('ServiceContracts',$recordId,array('modulestatus'=>array('oldValue'=>$recordModel->get('modulestatus'),'currentValue'=>'a_normal'),'eleccontractstatus'=>array('oldValue'=>$recordModel->get('eleccontractstatus'),'currentValue'=>'a_elec_withdraw')));
                    $sql='DELETE FROM vtiger_salesorderworkflowstages WHERE workflowsid=372 and  salesorderid=?';
                    $adb->pquery($sql,array($recordId));
                }elseif(3==$emstatus){//已撤回
                    $sql="UPDATE vtiger_servicecontracts SET modulestatus=?,eleccontractstatus=? WHERE servicecontractsid=?";
                    $adb->pquery($sql,array('c_cancel','a_elec_withdraw',$recordId));

                    $recordModel->sendSMSComm(array("mobile"=>$recordModel->get('elereceivermobile'),"content"=>'您好，珍岛集团已撤回合同《'.$eleccontracttpl.'》'));
                    $recordModel->setModTracker('ServiceContracts',$recordId,array('modulestatus'=>array('oldValue'=>$recordModel->get('modulestatus'),'currentValue'=>'c_cancel'),'eleccontractstatus'=>array('oldValue'=>$recordModel->get('eleccontractstatus'),'currentValue'=>'a_elec_withdraw'),));

                    //仅撤回的时候作废T云订单
                    $recordModel->cancelOrderByContractNo($recordModel->get('contract_no'));
                }
                $returnData['flag']=true;
            }else{
                $returnData['msg']=$jsonData['msg'];
            }
        }while(0);
        $response=new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }
    /**
     * 合同预览
     * @param $request
     */
    public function getElecTPLView($request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $jsonoutput=$recordModel->getElecTPLView($request);
        $data=json_decode($jsonoutput,true);
        $data['data']['contracturl']=$data['data']['contract'];
        $data['data']['contracturlbase']=base64_encode($data['data']['contract']);
        $data['data']['contract']='/index.php?module=ServiceContracts&mode=getPdfView&action=BasicAjax&fileurl='.base64_encode($data['data']['contract']).'.pdf';
        echo json_encode($data);
    }
    /**
     * 获取模板列表
     * @param $request
     */
    public function getElecTPLList($request){
        global $fangxinqian_url,$adb;
        $contractattribute=$request->get('contractattribute');
        $clientproperty=$request->get('clientproperty');
        $classtpl=$request->get('classtpl');
        $contract_type=$request->get('contract_type');
        $servicecontractstype=$request->get('servicecontractstype');
        $returnData=array('success'=>false);
        do{
            if(empty($contractattribute)){
                $returnData['msg']="合同属性不能为空";
                break;
            }
            if(empty($contract_type)){
                $returnData['msg']="合同类型不能为空";
                break;
            }
            if(empty($servicecontractstype)){
                $returnData['msg']="购买类型不能为空";
                break;
            }
            $servicecontractstypeArr=array(1=>'新增',2=>'续费',3=>'upgrade',4=>'degrade','againbuy'=>5);
            $servicecontractstype=array_search($servicecontractstype,$servicecontractstypeArr);
            if($servicecontractstype==false){
                $returnData['msg']="购买类型不能不存在";
                break;
            }
            $query='SELECT * FROM vtiger_contract_type WHERE contract_type=?';
            $result=$adb->pquery($query,array($contract_type));
            if(!$adb->num_rows($result)){
                $returnData['msg']="没有找到相对应的模板编号";
                break;
            }
            $productclass=$result->fields['productclass'];
            $productclass=trim($productclass,'-');
            $productclass=trim($productclass);
            if(empty($productclass)){
                $returnData['msg']="没有找到相对应的模板编号";
                break;
            }
            $contractattributeArr=array('standard'=>0,'customized'=>1);
            if($classtpl=='ElecTPLList'){
                $viewURL=$fangxinqian_url.$this->getTemplates.'secEntry='.$productclass.'&contractType='.$contractattributeArr[$contractattribute].'&purchaseType='.$servicecontractstype;
            }else{
                $viewURL=$fangxinqian_url.$this->getTemplates.'secEntry='.$productclass.'&contractType='.$contractattributeArr[$contractattribute].'&purchaseType='.$servicecontractstype;
            }
            $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
            echo $recordModel->https_requestcomm($viewURL,null,$recordModel->getCURLHeader(false));
            exit;
        }while(0);
        echo json_encode($returnData);

    }
    /**
     * 获取附件模板列表
     * @param $request
     */
    public function getEnclosuresList($request){
        global $fangxinqian_url;
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $token=$recordModel->getFangXinQianToken();
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "token:".$token));
        $productCode=$request->get('productCode');
        $viewURL=$fangxinqian_url.$this->getEnclosures.$productCode;
        echo $recordModel->https_requestcomm($viewURL,null,$curlset);
    }
    public function getContractSync($request){
        global $fangxinqian_url;
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $token=$recordModel->getFangXinQianToken();
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "token:".$token));
        $contractId=$request->get('contractId');
        $viewURL=$fangxinqian_url.$this->contract_sync.$contractId;
        echo $recordModel->https_requestcomm($viewURL,null,$curlset);
    }

    /**
     * 防止表单重复提交，除查询意外接口，都需要在请求头信息上面带上formId参数
     */
    public function getFangXinQianFormId(){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        return json_decode($recordModel->getFangXinQianFormId(),true);
    }
    public function saveAndReplace($request){
        global $fangxinqian_url,$current_user;
        $record=$request->get('updateRecord');
        $paramsData=$this->saveAndReplaceParams($request);
        $sendArr=$paramsData['data'];
        $recordModel=$paramsData['recordModel'];
        //if($record>0 && $recordModel->get('eleccontractstatus')=='a_elec_withdraw'){
        if($record>0){
            $sendArr['contractId']=$recordModel->get('eleccontractid');//放心签合同id
            $viewURL=$fangxinqian_url.$this->backEdit;
            $returnJsonData= $recordModel->https_requestcomm($viewURL,json_encode($sendArr,JSON_UNESCAPED_UNICODE),$recordModel->getCURLHeader(),true);
            $returnData=json_encode($returnJsonData,true);
            if($returnData['success']){
                echo json_encode(array("success"=> true,
                            "errorCode"=>null,
                            "msg"=>"success",
                            "data"=>array(
                                "contractId"=>$recordModel->get('eleccontractid'),
                                "contractUrl"=> null
                )));
            }else{
                echo $returnJsonData;
            }
        }else{
            $sendArr["templateId"]=$request->get('templateId'); //合同模板id
            $viewURL=$fangxinqian_url.$this->save_and_replace;
            echo $recordModel->https_requestcomm($viewURL,json_encode($sendArr,JSON_UNESCAPED_UNICODE),$recordModel->getCURLHeader(),true);
        }
    }
    public function saveAndReplaceParams($request){
        global $current_user;
        $record=$request->get('updateRecord');
        if($record>0){
            $recordModel=Vtiger_Record_Model::getInstanceById($record,'ServiceContracts');
        }else{
            $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        }
        $invoicecompany=$request->get('invoicecompany');
        $invoicecompanyInfo=$recordModel->getInvoicecompanyInfo($invoicecompany);
        $clientpropertyArr=array('enterprise'=>0,'personal'=>1,'government'=>0,'otherorg'=>0);
        $clientproperty=$request->get('clientproperty');
        $clientproperty=$clientpropertyArr[$clientproperty]>0?$clientpropertyArr[$clientproperty]:0;
        $totalprice=$request->get('total');
        $totalprice=$totalprice>0?$totalprice:' ';
        $chinatotalprice=$totalprice>0?$recordModel->toChinaMoney($totalprice):' ';
        $accountRecordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
        $accountInfo=$accountRecordModel->getAccountInfo($request);
        $sendArr=array("needAudit"=>$request->get('needAudit'),
            "sender"=>array(
                "name"=>$request->get('senderName'),
                "phone"=>$request->get('senderPhone')
            ),
            "receiver"=>array(
                "name"=>$request->get('receiverName'),
                "phone"=>$request->get('receiverPhone'),
                "type"=>$clientproperty  //0.企业 1.个人
            ),
            "companyCode"=>$invoicecompanyInfo['company_codeno'], //商务所属分公司编号
            "templateId"=>$request->get('templateId'), //合同模板id
            "expirationTime"=>$request->get('expirationTime'), //合同过期时间
            "replaces"=>array(
                "address"=>$invoicecompanyInfo['address'],
                "company"=>$invoicecompanyInfo['companyfullname'],
                "bank"=>$invoicecompanyInfo["bank_account"],
                "banknumber"=>$invoicecompanyInfo["numbered_accounts"],
                "phone"=>$invoicecompanyInfo["telphone"],
                "taxnumber"=>$invoicecompanyInfo["taxnumber"],
                "fax"=>$invoicecompanyInfo["tax"],
                "name"=>$current_user->last_name,//商务的名字
                "email"=>$current_user->email1,//商务的EMAIL
                "totalprice"=>$totalprice,//总额
                "chinatotalprice"=>$chinatotalprice,//中文大写
                "firstcompany"=>$accountInfo['accountname'],//客户名称
                "firstaddress"=>implode('',explode('#',$accountInfo['address'])),//客户通信地址
                "firstname"=>$request->get('receiverName'),//客户联系人
                "firstemail"=>$accountInfo['email1']?$accountInfo['email1']:' ',//客户EMAIL
                "firstphone"=>$request->get('receiverPhone'),//客户电话
                "firstfax"=>$accountInfo['fax']?$accountInfo['fax']:' ',//客户传真
                "firstbank"=>$accountInfo['bank_account'],
                "firstbanknumber"=>$accountInfo['numbered_accounts'],
                'signdate'=>date("Y年m月d日"),//我方的签定时间
                'firstsigndate'=>date("Y年m月d日"),//客户的签订时间
            )
        );
        return array('recordModel'=>$recordModel,'data'=>$sendArr);
    }
    /**
     * 合同预览
     * @param $requesst
     */
    public function getPdfView($requesst){
        $fileurl = $requesst->get('fileurl');
        $fileurl=trim($fileurl,'.pdf');
        $fileurl=base64_decode($fileurl);
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        echo $recordModel->getPdfView($fileurl);
        exit;
    }
    /**
     * 电子合同编辑
     */
    public function elecErpEdit($request){
        global $fangxinqian_url;
        $contractId=$request->get('contractId');
        $udata=$request->get('udata');
        if(empty($udata)){
            echo json_encode(array("success"=>true,"errorCode"=>null,"msg"=>"success","data"=>null));
            exit;
        }
        $arrayData=array('contractId'=>$contractId,'itd'=>$udata);
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $url=$fangxinqian_url.$this->erp_edit;
        return $recordModel->https_requestcomm($url,json_encode($arrayData),$recordModel->getCURLHeader(),true);
    }

    /**
     * 放心签合同作废
     * @param $request
     */
    public function elecCommonTovoid($request){
        $contractId=$request->get('contractId');
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        echo $recordModel->elecCommonTovoid($contractId);
    }

    /**
     * 发送失败重新发送
     * @param $request
     */
    public function elecResend($request){
        global $adb,$current_user;
        $record=$request->get('recordid');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'ServiceContracts');
        $echoData='{"success":false,"msg":"没有权限"}';
        if($recordModel->get('modulestatus')=='已发放'
            && $recordModel->get('signaturetype')=='eleccontract'
            && $recordModel->get('eleccontractstatus')=='a_elec_actioning_fail'
            && $current_user->id== $recordModel->get('Receiveid')
        ){
            $echoData=$recordModel->elecAgainSign();
            $returnData=json_decode($echoData,true);
            if ($returnData['success']) {
                if($recordModel->get('contractattribute')=='customized'){
                    $file=$recordModel->get('file');
                    $files=explode('##',$file);
                    $filesname=strtolower($files[0]);
                    $filesname=trim($filesname,'.doc');
                    $filesname=trim($filesname,'.docx');
                    $eleccontracttpl=trim($filesname,'.pdf');
                }else{
                    $eleccontracttpl=$recordModel->get('eleccontracttpl');
                }
                $recordModel->fileSave($returnData['data'],'files_style8','放心签待签订件');
                $recordModel->sendSMS(array('statustype'=>'','mobile'=>$recordModel->get('elereceivermobile'),'eleccontracttpl'=>$eleccontracttpl,'url'=>$recordModel->elecContractUrl));
                $recordModel->sendMailFXQ();
                $sql="UPDATE vtiger_servicecontracts SET eleccontractstatus='b_elec_actioning',eleccontracturl=? WHERE servicecontractsid=?";
                $adb->pquery($sql,array($returnData['data'],$record));

                $recordModel->syncVerifyResultToWk($record,$returnData['data']);
            }
        }
        echo $echoData;
    }
    public function getReceiverInfo($request){
        $record=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'ServiceContracts');
        $response = new Vtiger_Response();
        $response->setResult(array('elereceiver'=>$recordModel->get('elereceiver'),'elereceivermobile'=>$recordModel->get('elereceivermobile'),'iscustomized'=>$recordModel->get('contractattribute')=='standard'?1:0));
        $response->emit();
    }

    /**
     * 申请作废
     * @param $request
     */
    public function elecDoCancel($request){
        global $current_user,$adb;
        $record=$request->get('recordid');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'ServiceContracts');
        $returnData=array('flag'=>false);
        do{
            if($recordModel->get('modulestatus')!='已发放'){
                $returnData['msg']='只有发放状态才能操作';
                break;
            }
            if($recordModel->get('signaturetype')!='eleccontract'){
                $returnData['msg']='只有电子合同才能操作';
                break;
            }
            if($recordModel->get('eleccontractstatus')!='b_elec_actioning'){
                $returnData['msg']='只有待签收的状态才能操作';
                break;
            }
            /*if($recordModel->get('Receiveid')!=$current_user->id){
                $returnData['msg']='只有签订人才能操作';
                break;
            }*/
            if(!$recordModel->checkUserPermission($recordModel->get('Receiveid'))){
                $returnData['msg']='没有权限操作';
                break;
            }
            $detailViewModel=Vtiger_DetailView_Model::getInstance('ServiceContracts',$record);
            if(!$detailViewModel->getContractVoid($record)){
                $returnData['msg']='存在发票,充值单,回款或工单请先处理！';
                break;
            }
            $eleccontractid=$recordModel->get('eleccontractid');
            if($eleccontractid<1){
                $returnData['msg']='合同错误';
                break;
            }
            /*$args=array("contractId"=>$eleccontractid,
                "type"=>2,
                "name"=>$recordModel->get('elereceiver'),
                "phone"=>$recordModel->get('elereceivermobile')
            );*/
            //$backData=$recordModel->elecCommonBack($args);
            $backData=$recordModel->elecCommonTovoid($eleccontractid);
            $jsonData=json_encode($backData,true);
            if($jsonData['success']){
                //$this->makeWorkflowStages($recordModel);
                $adb->pquery('UPDATE vtiger_servicecontracts SET modulestatus=?,eleccontractstatus=? WHERE servicecontractsid=?',array('c_cancel','a_elec_void',$record));
                global $configcontracttypeName;
                if($recordModel->get('contract_type')==$configcontracttypeName){
                    $recordModel->cancelOrderByContractNo($recordModel->get('contract_no'));
                }
                $recordModel->syncVerifyResultToWk($record,'','',1);
                $recordModel->setModTracker('ServiceContracts',$record,array('modulestatus'=>array('oldValue'=>$recordModel->get('modulestatus'),'currentValue'=>'c_cancel'),'eleccontractstatus'=>array('oldValue'=>$recordModel->get('eleccontractstatus'),'currentValue'=>'a_elec_withdraw')));
                $returnData=array('flag'=>true,'msg'=>'合同作废成功！');
            }else{
                $returnData['msg']=$jsonData['msg'];
            }
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }
    public function makeWorkflowStages($recordModel){
        $recordId=$recordModel->getId();
        global $current_user;
        $_REQUEST['workflowsid']=$recordModel->elecCancelWorkflowsid;
        $focus = CRMEntity::getInstance('ServiceContracts');
        $focus->makeWorkflows('ServiceContracts', $_REQUEST['workflowsid'], $recordId,'edit');
        $focus->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='b_check',creatorid=? WHERE servicecontractsid=?",array($current_user->id,$recordId));
        $departmentid=$_SESSION['userdepartmentid'];
        $focus->setAudituid('ContractsAuditset',$departmentid,$recordId,$_REQUEST['workflowsid']);
        $updateSql="UPDATE vtiger_salesorderworkflowstages SET ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='DO_CANCEL_ELEC' AND workflowsid =?";
        $focus->db->pquery($updateSql,array(16,$recordId,$_REQUEST['workflowsid']));
        if(in_array($recordModel->get('companycode'),$this->Kllcompanycode)){
            $query='SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE departmentid=? limit 1';
            $result=$focus->db->pquery($query,array($departmentid));
            $data=$focus->db->raw_query_result_rowdata($result,0);
            $parentdepartment=$data['parentdepartment'];
            $parentdepartment.='::';
            if(strpos($parentdepartment,$this->Kllneedle)!==false || strpos($parentdepartment,$this->WXKLLneedle)!==false){
                $data=$focus->getAudituid('ContractsAuditset',$departmentid);
                $userid1=$data['audituid4']>0?$data['audituid4']:$data['oneaudituid'];
                $userid2=$data['audituid4']>0?$data['oneaudituid']:$data['towaudituid'];
                $userid3=$data['audituid4']>0?$data['towaudituid']:$data['audituid3'];
                $updateSql="UPDATE vtiger_salesorderworkflowstages SET ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='AUDIT_VERIFICATION' AND workflowsid =?";
                $focus->db->pquery($updateSql,array($userid1,$recordId,$_REQUEST['workflowsid']));
                $updateSql="UPDATE vtiger_salesorderworkflowstages SET ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='TWO_VERIFICATION' AND workflowsid =?";
                $focus->db->pquery($updateSql,array($userid2,$recordId,$_REQUEST['workflowsid']));
                $updateSql="UPDATE vtiger_salesorderworkflowstages SET ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='THREE_VERIFICATION' AND workflowsid =?";
                $focus->db->pquery($updateSql,array($userid3,$recordId,$_REQUEST['workflowsid']));
                $updateSql="UPDATE vtiger_salesorderworkflowstages SET workflowstagesname='电子合同作废申请',ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='CREATE_CODE' AND workflowsid =?";
                $focus->db->pquery($updateSql,array(792,$recordId,$_REQUEST['workflowsid']));
            }
        }
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$recordId,'salesorderworkflowstagesid'=>0));
    }
    public function erpUpload($request){
        global $adb,$root_directory,$fangxinqian_url;;
        $fileid=$request->get('fileid');
        $result = $adb->pquery("SELECT * FROM vtiger_files WHERE delflag=0 AND attachmentsid=?", array($fileid));
        if($adb->num_rows($result)){
            $newfilename=$result->fields['newfilename'];
            $name=$result->fields['name'];
            $path=$result->fields['path'];
            $type=$result->fields['type'];
            $filepath=$root_directory.$path.$fileid.'_'.$newfilename;
            if(!file_exists($filepath)){
                echo json_encode(array('success'=>false,'msg'=>'文件不存在'));
                exit;
            }
            $nameArr=explode('.',$name);
            if(end($nameArr)!='docx'){
                echo json_encode(array('success'=>false,'msg'=>'只支持DOCX文件模板'));
                exit;
            }
            $url=$fangxinqian_url.'erp/upload';
            $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
            $token=$recordModel->getFangXinQianToken();
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "token:".$token));

            $jsonData=$recordModel->CURLfileUpload($url,$filepath,$type,$name,$curlset,true);
            $data=json_decode($jsonData,true);
            if($data['success']){
                $data['data']['orgurl']=$data['data']['url'];
                $data['data']['url']='/index.php?module=ServiceContracts&mode=getPdfView&action=BasicAjax&fileurl='.base64_encode($data['data']['url']);
                echo json_encode($data);
            }else{
                echo $jsonData;
            }
        }
    }

    /**
     * 非标合同发送
     * @param $request
     */
    public function erpContractSet($request){
        $paramsData = $this->saveAndReplaceParams($request);
        $record = $request->get('updateRecord');
        $sendArr = $paramsData['data'];
        $recordModel = $paramsData['recordModel'];
        $custromData = $request->get('custromData');
        $input = $custromData['input'];
        $areas =$custromData['areas'];
        $newData = array("contractUrl" => $request->get('tplurl'),
            "title" => $request->get('tplname'),//合同上传返回的文件名称
            "sender" => $sendArr['sender'],
            "receiver" => $sendArr['receiver'],
            "input" => $input,
            "areas" => $areas,
            "companyCode" => $sendArr["companyCode"],//所属合同主体
            "expirationTime" => $sendArr["expirationTime"]//过期时间
        );
        if($record>0){
            $newData['contractId']=$request->get('oldeleccontractid');//放心签合同id
            echo $recordModel->contractReSet($newData);
        }else{
            echo $recordModel->contractSet($newData);
        }
    }
    public function erpGetArea($request){
        $contractId=$request->get('contractId');
        if($contractId>0){
            $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
            echo $recordModel->contractReSetArea($contractId);
        }else{
            echo json_encode(array('success'=>false,'msg'=>'无效的contractId'));
        }
    }
    public function syncProduct2CodeNoTyun($request){
        global $adb;
        $id=$request->get('id');
        $sql = "SELECT * FROM `vtiger_contract_type` where contract_typeid=?";
        $result=$adb->pquery($sql,array($id));
        if($adb->num_rows($result)){
            $contract_type=$result->fields['contract_type'];
            $productclass=$result->fields['productclass'];
            $productclass=trim($productclass,'-');
            $productclass=trim($productclass);
            if(!empty($productclass)){
                $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
                echo $recordModel->addProduct(array("productName"=>$contract_type,"productCode"=>$productclass));
            }else{
                echo json_encode(array('success'=>false,'msg'=>'产品编号为空不能同步'));
            }
        }
    }
    public function addPhaseSplit(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $response=new Vtiger_Response();
        $response->setResult($recordModel->addPhaseSplit($request));
        $response->emit();
    }

        public function customizeSendMessage(Vtiger_Request $request){
        $recordid = $request->get("recordid");
        $recordModel = ServiceContracts_Record_Model::getInstanceById($recordid,"ServiceContracts");
        $content = $request->get('msg');
        $data = array('flag'=>'0', 'msg'=>'发送失败');
        if(!$content){
            $data['msg'] ='内容不能为空';
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
            exit;
        }
        $res = $recordModel->sendCustomizeMessage($recordid,$content);
        if($res){
            $data = array('flag'=>'1', 'msg'=>'发送成功');
        }

        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 数字威客一键签收查找供应商合同
     * @param Vtiger_Request $request
     */
    public function searchSupplierContractsNo(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $returnData=$recordModel->getSuppNOAndID($request);
        $response=new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * 数学威客合同一键签收
     */
    public function wkSign(Vtiger_Request $request){
        global $adb;
        $recordid=$request->get('recordid');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'ServiceContracts');
        $returnData=$recordModel->getSuppNOAndID($request);
        do {
            if (!$returnData['flag']) {
                break;
            }
            $suppliercontractsid=$request->get('suppliercontractsid');
            if($suppliercontractsid!=$returnData['data']['suppid']){
                $returnData=array('flag'=>false,'msg'=>'采购合同有误！');
                break;
            }
            $entity = $recordModel->entity->column_fields;
            if(empty($entity['contract_no'])){
                $recordModel->generationNumber($entity);
                $suppname=$request->get('suppname');
                $date=date("Y-m-d",strtotime("+1 year"));
                $adb->pquery("UPDATE vtiger_servicecontracts SET modulestatus='c_complete',iscomplete=1,total=if(total>0,total,0),suppliercontractsid=?,suppliercontractsno=?,remark=concat(remark,?),effectivetime=?,actualeffectivetime=?,signfor_date=? WHERE servicecontractsid=?",array($suppliercontractsid,$suppname,$suppname.'虚拟合同系统签收',$date,$date,date("Y-m-d H:i:s"),$recordid));
                $returnData['msg']='合同签收成功！';
            }else{
                $returnData=array('flag'=>false,'msg'=>'合同编号已生成，请走正常流程！');
            }

        }while(0);
        $response=new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }

    public function doReceived($params){
        $db = PearDatabase::getInstance();
        $signpath = $params['signpath'];
        $codenumber = $params['codenumber'];
        $userid = $params['userid'];
        $servicecontractsid = $params['servicecontractsid'];
        $codenumbertemp = $params['codenumbertemp'];
        $recordModel = Vtiger_Record_Model::getInstanceById($servicecontractsid, 'ServiceContracts');
        $datetime = date('Y-m-d H:i:s');
        global $current_user;
        $sql = "UPDATE vtiger_servicecontracts_print SET constractsstatus='c_receive',receivedtime=?,receivedid=?,doreceivedid=? WHERE {$params['tempfield']}=?";
        if($params['agentid']){
            $db->pquery('UPDATE vtiger_servicecontracts SET modulestatus=?,receivedate=?,agentid=?,agentname=?,firstreceivedate=IF(firstreceivedate IS NULL,?,firstreceivedate) WHERE servicecontractsid=?', array('已发放', date('Y-m-d'), $params['agentid'],$params['agentname'],$datetime,$params['servicecontractsid']));
        }else{
            $db->pquery('UPDATE vtiger_servicecontracts SET modulestatus=?,receivedate=?,firstreceivedate=IF(firstreceivedate IS NULL,?,firstreceivedate)  WHERE servicecontractsid=?', array('已发放', date('Y-m-d'),$datetime, $servicecontractsid));
        }
        $this->dosignContracts(array('controllerType'=>'Received','userid'=>$userid,'inputcode'=>$codenumber,'signpath' => $signpath, "recordId" => $servicecontractsid, 'workflowstagesflag' => $this->getTheContract));

        $db->pquery($sql, array($datetime, $userid, $current_user->id, $codenumbertemp));
        $sql = 'UPDATE vtiger_crmentity SET smownerid=? WHERE crmid=?';
        $db->pquery($sql, array($userid, $servicecontractsid));
        $id = $db->getUniqueId('vtiger_modtracker_basic');
        $currentTime=date('Y-m-d H:i:s');
        $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $servicecontractsid, 'ServiceContracts', $current_user->id, $currentTime, 0));
        $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
            Array($id, 'modulestatus', 'c_stamp', '已发放'));
        $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
            Array($id, 'Receivedate', $recordModel->get('Receivedate'), date("Y-m-d")));
        $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
            Array($id, 'assigned_user_id', $recordModel->get('assigned_user_id'), $userid));
    }

    public function getContractAmount(Vtiger_Request $request){
        $recordId=$request->get('record');
        $recordModel=new SearchMatch_Record_Model();
        $accountMoneyArray=$recordModel->getAccountMoneyArray($recordId);
        if(!$accountMoneyArray['paymentReceived']){
            $accountMoneyArray['paymentReceived']='0.00';
        }
        if(!$accountMoneyArray['paymentTotal']){
            $accountMoneyArray['paymentTotal']='0.00';
        }
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $accountMoneyArray['leastPayMoney']=$accountMoneyArray['paymentElse'];
        if($parentRecordModel->get('isstage')){
            $recordModel=new ServiceContracts_Record_Model();
            $result=$recordModel->leastPayMoney($recordId);
            $accountMoneyArray['leastPayMoney']=$result['data'];
        }
        $accountMoneyArray=array_map(function ($v){
            return number_format($v,2);
        },$accountMoneyArray);
        $accountMoneyArray['sideagreement']=$parentRecordModel->get('sideagreement');
        $response=new Vtiger_Response();
        $response->setResult($accountMoneyArray);
        $response->emit();
    }
    /**
     * 合同关闭功能
     * @param Vtiger_Request $request
     */
    public function closedContracts(Vtiger_Request $request){
        $record=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'ServiceContracts');
        $modulestatus=$recordModel->get('modulestatus');
        $contract_no=$recordModel->get('contract_no');
        do{
            if(!$recordModel->personalAuthority('ServiceContracts','closedContracts')){
                $returnData=array('flag'=>false,'msg'=>'没有权限操作！');
                break;
            }
            $returnData=$this->doingClosedContracts($recordModel,$modulestatus,$record,$contract_no);
        }while(0);
        $response=new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }
    public function doingClosedContracts($recordModel,$modulestatus,$record,$cno){
        do{
            if($modulestatus!='c_complete'){
                $returnData=array('flag'=>false,'msg'=>'关停失败只有签收的合同才能关停，其他状态的合同请执行其他流程！');
                break;
            }
            $ischeckdata=$recordModel->checkContract(array($record));
            if($ischeckdata['flag']){
                $returnData=array('flag'=>false,'msg'=>'关停失败'.$ischeckdata['data'][$record]);
                break;
            }
            $dataTYUNoclose=$recordModel->doCloseTYUNContract($cno);
            if(!$dataTYUNoclose['flag']){
                $returnData=array('flag'=>false,'msg'=>'关停失败'.$dataTYUNoclose['msg']);
                break;
            }
            $sql="UPDATE vtiger_servicecontracts SET modulestatus='c_completeclosed' WHERE servicecontractsid=?";
            global $adb;
            $adb->pquery($sql,array($record));
            $recordModel->setModTracker('ServiceContracts',$record,array('modulestatus'=>array('oldValue'=>'c_complete','currentValue'=>'c_completeclosed')));
            $returnData=array('flag'=>true,'msg'=>'关停成功！');
        }while(0);
        return $returnData;
    }

    /**
     * @param Vtiger_Request $request
     */
    public function fileupload(Vtiger_Request $request){
        set_time_limit(0);
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        if(!$recordModel->personalAuthority('ServiceContracts','closedContracts')){
            echo json_encode(array('success'=>false,'msg'=>'没有权限！'));
            exit;
        }
        $model=$request->get('module');
        $record=$request->get('record');
        $field = $request->get('field');
        $file = $request->get('file');
        $files = explode("base64,",$file);
        $filestream = $files[1];
        $name = $request->get('name');
        $size = $request->get('size');
        $type = $request->get('filedatatype');
        if($name != '' && $size > 0){
            global $current_user;
            global $upload_badext;
            global $root_directory;
            global $adb;
            $current_id = $adb->getUniqueID("vtiger_files");
            $ownerid = $current_user->id;
            $file_name = $name;
            $file_name=preg_replace('/(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+/u','',$file_name);
            $binFile = sanitizeUploadFileName($file_name, $upload_badext);
            $uploadfile=time();
            $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
            $filetype = $type;
            $filesize = $size;
            $filetmp_name = $name;
            $upload_file_path = decideFilePath();
            $filepathname=$root_directory.$upload_file_path . $current_id . "_" .$uploadfile;
            file_put_contents($filepathname,base64_decode($filestream));
            if(!file_exists($filepathname)){
                echo json_encode(array('success'=>false,'msg'=>'文件上传失败'));
                exit;
            }
            $sql2 = "insert into vtiger_files(attachmentsid, name,description, type, path,uploader,uploadtime,newfilename) values(?, ?,?, ?, ?,?,?,?)";
            $params2 = array($current_id, $filename, $model,$filetype, $upload_file_path,$current_user->id,date('Y-m-d H:i:s'),$uploadfile);
            $result = $adb->pquery($sql2, $params2);
            include $root_directory.'libraries/PHPExcel/PHPExcel/IOFactory.php';
            $inputFileName =$filepathname;
            date_default_timezone_set('PRC');
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                echo json_encode(array('success'=>false,'msg'=>'上传失败'));
                exit;
            }
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $stepRow=2000;
            $rowDataArray=array();
            $rightRowArr=array();
            $allData=array();
            $wrongRow=0;
            $rightRowNum=0;
            for ($row =2; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                $tempdata=trim($rowData[0][0]);
                if(!empty($tempdata)){
                    $rowDataArray[]=$tempdata;
                }
                $allData[md5($tempdata)]=$tempdata.',没有查到相关合同信息！';
                if(($row%$stepRow==0 || $highestRow==$row) && !empty($rowDataArray)){
                    $query="SELECT vtiger_servicecontracts.modulestatus,vtiger_servicecontracts.servicecontractsid,vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity 
                            ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND contract_no in('".implode("','",$rowDataArray)."')";
                    $resultContract=$adb->pquery($query,array());
                    $num=$adb->num_rows($resultContract);
                    if($num){
                        while($dbrow=$adb->fetch_array($resultContract)){
                            $closeData=$this->doingClosedContracts($recordModel,$dbrow['modulestatus'],$dbrow['servicecontractsid'],$dbrow['contract_no']);
                            if(!$closeData['flag']){
                                $allData[md5($dbrow['contract_no'])]=$dbrow['contract_no'].','.$closeData['msg'];
                            }else{
                                $rightRowNum++;
                                $rightRowArr[]=md5($dbrow['contract_no']);
                            }
                        }
                    }else{
                        $wrongRow+=count($rowDataArray);
                    }
                    $rowDataArray=array();
                }
            }
            $tempdatate=array();
            if(!empty($rightRowArr)){
                foreach($allData as $key=>$tempValue){
                    if(!in_array($key.$rightRowArr)){
                        $tempdatate[]=$tempValue;
                    }
                }
            }else{
                $tempdatate=$allData;
            }
            if(($highestRow-1-$rightRowNum)>0){
                $zipPath = $root_directory.'storage/download';
                !is_dir($zipPath)|| mkdir($zipPath, 0755,true);
                $filename='stopcontracts'.$ownerid. '.zip';
                $zipName = $zipPath . '/' . $filename;
                @unlink($zipName);
                $zipfile='storage/download/'.$filename;
                $zip = new ZipArchive();
                if($zip->open($zipName, ZIPARCHIVE::CREATE) !== TRUE) {
                    exit('create zip fault');
                }
                $zip->addFromString(iconv("UTF-8", "GBK//IGNORE", "1.txt"), implode("\n",$tempdatate));
                $zip->close();
                $msg='关停失败........'.($highestRow-1-$rightRowNum).'/'.($highestRow-1).'<br><a href="/'.$zipfile.'">查看失败记录</a>';
            }else{
                $msg='关停成功........'.($highestRow-1).'/'.($highestRow-1);
            }
            echo json_encode(array('success'=>true,'msg'=>$msg));
        }else{
            echo json_encode(array('success'=>false,'msg'=>'上传失败'));
        }
        exit;
    }

    /**
     * 修改领取人
     *
     * @param Vtiger_Request $request
     */
    public function doChangeSmowner(Vtiger_Request $request){
        $recordid = $request->get("recordid");
        $newsmownerid = $request->get("newsmownerid");
        $recordModel = ServiceContracts_Record_Model::getInstanceById($recordid,'ServiceContracts');
        if($recordModel->get("assigned_user_id")==$newsmownerid){
            echo json_encode(array('success'=>false,'msg'=>'新老领取人一致，不能发起'));
            exit();
        }
        $servicenum = ServiceContracts_Record_Model::servicecontracts_reviced($newsmownerid);
        if($recordModel->get("modulestatus")=='已发放' && $servicenum){
            echo json_encode(array('success'=>false,'msg'=>'不能转移，原因：该领取人有'.$servicenum.'份合同没有交回，需先将合同收回，方能转移！！！'));
            exit();
        }


        //生成审核工作流
        global $current_user;
        $db = PearDatabase::getInstance();
        $workflowsid=$recordModel->changeSmownerWorkflowsid;
        $result = $db->pquery("select * from vtiger_workflowstages where workflowsid=?",array($workflowsid));
        if(!$db->num_rows($result)){
            echo json_encode(array('success'=>false,'msg'=>'不能转移,原因:不存在工作流审核阶段!!!'));
            exit();
        }
        $detailViewModel=Vtiger_DetailView_Model::getInstance('ServiceContracts',$recordid);
        if(!$detailViewModel->getContractVoid($recordid)){
            echo json_encode(array('success'=>false,'msg'=>'该合同存在发票,充值单,回款或工单,请先处理！'));
            exit();
        }

        if($recordModel->hasOrder($recordid)){
            echo json_encode(array('success'=>false,'msg'=>'该合同存在可用订单，不能变更领取人,请先处理！'));
            exit();
        }

        $row=$db->fetchByAssoc($result,0);
        $actiontime=date('Y-m-d H:i:s');
        $db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE workflowsid =? and salesorderid=?", array($workflowsid,$recordid)); //删除同一个工作流id
        $sqlsub = "INSERT INTO vtiger_salesorderworkflowstages (workflowstagesname,workflowstagesid,sequence,salesorderid,isaction,actiontime,addtime,workflowsid,modulename,smcreatorid,createdtime,productid,departmentid,ishigher,higherid,workflowstagesflag,modulestatus) values (?,?,?,?,?,?, NOW(),?,?,?,NOW(),?,?,?,?,?,'p_process')";
        $db->pquery($sqlsub,array('新领取人审核',$row['workflowstagesid'],1,$recordid,1,$actiontime,$workflowsid,'ServiceContracts',$current_user->id,0,$current_user->current_user_parent_departments,1,$newsmownerid,'CHANGESMOWNER'));

        //将合同标注审核中
        $db->pquery("update vtiger_servicecontracts set modulestatus='b_check',backstatus='已发放' where servicecontractsid=?",array($recordid));
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$recordid,'salesorderworkflowstagesid'=>0));

        echo json_encode(array('success'=>true,'msg'=>'生成工作流成功，待新领取人审核通过后方可转移成功'));
    }
     /**
     * 普通合同与特殊合同切换
     * @param Vtiger_Request $request
     */
    public function setSpecialContracto(Vtiger_Request $request){
        global $adb;
        $recordid=$request->get('recordid');
        $query='SELECT 1 FROM vtiger_specialcontract WHERE specialcontractid=?';//特殊合同
        $result=$adb->pquery($query,array($recordid));
        if($adb->num_rows($result)){
            $sql='DELETE FROM vtiger_specialcontract WHERE specialcontractid=?';//特殊合同
            $adb->pquery($sql,array($recordid));
            $returnData=array('flag'=>true,'msg'=>'特殊合同切换为普通合同成功！');
        }else{
            $sql='INSERT INTO vtiger_specialcontract values(?)';//特殊合同
            $adb->pquery($sql,array($recordid));
            $returnData=array('flag'=>true,'msg'=>'普通合同切换为特殊合同成功！');
        }
        $response=new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }
}
