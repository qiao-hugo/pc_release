<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OrderChargeback_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
        $this->exposeMethod('checkContractAmount');
		$this->exposeMethod('getInvoiceSalesorderList');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}
	


    /**
     * 取得合同对应的信息,对应的客户,对应的合同总额,对应的回款,对应的发票,对应的工单
     * @param Vtiger_Request $request
     */
    public function getInvoiceSalesorderList(Vtiger_Request $request){
        $recordId = $request->get('record');//合同的id
        $salesorderid = $request->get('salesorderid');//工单的id 编辑模式
        $db=PearDatabase::getInstance();
        //查询合同下产品信息
        $sql = 'SELECT `productid`, `productform`, salesorderid, IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), \'--\' ) AS productcomboid FROM vtiger_salesorderproductsrel WHERE servicecontractsid =  ? AND multistatus in(0,3)';
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
        $query='SELECT sum(IFNULL(unit_price,0)) AS unit_price from vtiger_receivedpayments WHERE relatetoid=?';
        $receivedpayments=$db->pquery($query,array($recordId));
        $receivedpaymentstotal=$db->query_result_rowdata($receivedpayments);

        $moduleName = $request->get('module');
        $module = Vtiger_Record_Model::getInstanceById($recordId,'ServiceContracts');
        $result=array();
        $result=$module->getData();
        $accountsId=$result['sc_related_to'];
        $smownerid=$result['Receiveid'];
        $total=$result['total'];
        $remark = $result['remark'];
        $company=$result['invoicecompany'];
        $accountsModule=Vtiger_Record_Model::getInstanceById($accountsId,'Accounts');
        //合同数据
        $accounts=$accountsModule->getData();
        //合同回款记录
        $rp=ReceivedPayments_Record_Model::getAllReceivedPayments($recordId);
        $return=array('accountname'=>$accounts['accountname'],'id'=>$accounts['record_id'],'userid'=>$smownerid,'total'=>$total,'rp'=>$rp,'remark'=>$remark,'rtotal'=>$receivedpaymentstotal['unit_price'],'salesorderlist'=>OrderChargeback_Record_Model::getRelateProduct($recordId),'invoicelist'=>OrderChargeback_Record_Model::getRelateInvoice($recordId),'invoicecompany'=>$company);
        //$datas=array($productidlist,$return);
        $response = new Vtiger_Response();
        $response->setResult($return);
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

	}

    //退款不终止业务、退款终止业务，框架合同 进行金额校验。累计退款金额 + 本次申请退款金额  ＞ 合同金额，则不允许用户发起退款申请
	public function checkContractAmount(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $record = $request->get('record');
        $serviceId = $request->get('serviceId');
        $amount = $request->get('amount');
        $sql="select sum(IFNULL(t1.refundamount,0)) as cumulativeAmount,t2.total,t2.frameworkcontract from vtiger_orderchargeback t1 left join vtiger_servicecontracts t2 on t1.servicecontractsid=t2.servicecontractsid   where t1.servicecontractsid=? and t2.frameworkcontract!='yes' and t1.refundreason!='客户重复打款'";
        //如果有这个退款记录了，去掉
        $condition=array($serviceId);
        if($record>0){
            $sql.=" and t1.orderchargebackid!=?";
            array_push($condition,$record);
        }
        $result=$db->pquery($sql,$condition);
        $resultArray=$db->query_result_rowdata($result);
        if(!$resultArray['cumulativeamount']){
            $resultArray['cumulativeamount']=0;
        }
        $compareResult=bccomp($resultArray['total'],bcadd($amount,$resultArray['cumulativeamount']));
        $return['success']=true;
        if($compareResult==-1&&$resultArray['frameworkcontract']&&$resultArray['frameworkcontract']!='yes'){
            $return['success']=false;
        }
        $return['total']=$resultArray['total'];
        $return['cumulativeAmount']=$resultArray['cumulativeamount'];
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
	}
}
