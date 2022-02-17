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
	function __construct() {
		parent::__construct();
		$this->exposeMethod('inquireproduct');
		$this->exposeMethod('receivepay');
		$this->exposeMethod('getcurrencytype');
		$this->exposeMethod('getsmownerid');
		$this->exposeMethod('getservicecontractsinfo');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
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
	    $accountid = $request->get('record');
	    $db=PearDatabase::getInstance();//young 20150427 优化代码
	    //$sql = 'SELECT (SELECT id FROM vtiger_users where id=vtiger_crmentity.smownerid) as id FROM vtiger_crmentity WHERE crmid = ?';
	    //2016-3-8 替换，判断是否是来自于市场部的客户的第一笔合同;
        $sql = "SELECT frommarketing,(SELECT id FROM vtiger_users where id=vtiger_crmentity.smownerid) AS smoid FROM vtiger_account INNER JOIN vtiger_crmentity ON crmid = accountid WHERE crmid = ?";
        $smownerdata = $db->pquery($sql,array($accountid));
	    if ($db->num_rows($smownerdata) > 0) {
	        $data['fromarket'] = $db->query_result($smownerdata,'0','frommarketing');
            $data['smoid'] = $db->query_result($smownerdata,'0','smoid');
	    }
        $account_sql = 'SELECT * FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON crmid = servicecontractsid LEFT JOIN vtiger_account ON accountid = sc_related_to WHERE sc_related_to = ? AND vtiger_crmentity.deleted != 1';
        $frommarket = $db->pquery($account_sql,array($accountid));

        if($data['fromarket']=='1' && $db->num_rows($frommarket)==0){
            $data['from'] = true;
        }else{
            $data['from'] = false;
        }
        if(empty( $data['smoid'])){
	        $data['smoid'] = Users_Record_Model::getCurrentUserModel()->column_fields['currency_id'];
	    }
	    $response = new Vtiger_Response();
	    $response->setResult($data);
	    $response->emit();
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
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	    //2015-1-23 wangbin 合并两次请求ajax
	    $recordId = $request->get('record');//合同的id
		$salesorderid = $request->get('salesorderid');//工单的id 编辑模式
	    $db=PearDatabase::getInstance();
		//查询合同下产品信息
		$sql = 'SELECT `productid`, `productform`, salesorderid, IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), \'--\' ) AS productcomboid FROM vtiger_salesorderproductsrel WHERE servicecontractsid =  ?';
	    $product = $db->pquery($sql,array($recordId));
	    $productids = $db->num_rows($product);
		//合同下得有产品//没有无产品的合同提工单
		$productidlist = array();
		if($productids>0){
		    while($row=$db->fetchByAssoc($product)){
				$productidlist[$row['productid']] = $row;
		    	//$module = Vtiger_Record_Model::getInstanceById($row['productid'],'Products');$productidlist[$i] = $module->getData();
		    	//$productidlist[$i]['solutions']= empty($row['salesorderid'])?$productidlist[$i]['notecontent']:$row['notecontent'];
				//$productidlist[$i]['productcomboid']= empty($row['productcomboid'])?'--':$row['productcomboid'];
		    }
		}
		$isEditForm=true;
		//编辑工单
		if($salesorderid){
			$productlist=$db->pquery('SELECT vtiger_salesorder_productdetail.FormInput,vtiger_salesorder_productdetail.TplId, vtiger_products.productname,vtiger_products.product_no, vtiger_products.productid, vtiger_formdesign.content_parse,vtiger_formdesign.field FROM vtiger_salesorder_productdetail LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_salesorder_productdetail.Productid LEFT JOIN vtiger_formdesign ON vtiger_formdesign.formid = vtiger_salesorder_productdetail.TplId WHERE vtiger_salesorder_productdetail.SalesOrderId =?',array($salesorderid));
			$rows=$db->num_rows($productlist);
			//旧数据只有模版
			if(!$rows){
				$isEditForm=false;
				$productlist=$db->pquery('SELECT vtiger_products.productname,vtiger_products.product_no, vtiger_products.productid from vtiger_products WHERE vtiger_products.productid in('.implode(',',array_keys($productidlist)).')');
				$rows=$db->num_rows($productlist);
			}
		}else{
			$productlist=$db->pquery('SELECT vtiger_customer_modulefields.formid as tplid,vtiger_products.productname,vtiger_products.product_no,vtiger_products.productid,vtiger_formdesign.content_parse,vtiger_formdesign.field FROM vtiger_customer_modulefields LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_customer_modulefields.relateid LEFT JOIN vtiger_formdesign ON vtiger_formdesign.formid = vtiger_customer_modulefields.formid WHERE vtiger_customer_modulefields.relateid IN ('.implode(',',array_keys($productidlist)).')');
			$rows=$db->num_rows($productlist);
			if(!$rows){
				//产品无表单
			}
		}
		//使用产品表单
		if($rows){
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
}
