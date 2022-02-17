<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class CompayCode_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('saveinvoicecompany');
		$this->exposeMethod('savecompanycodeuserid');
		$this->exposeMethod('deletedInvoiceCompany');
		$this->exposeMethod('deletedInvoiceCompanyUser');
		$this->exposeMethod('addCompanyFXQ');
	}
    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }

    }
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}
	//得到InvoiceCompany 和 vtiger_company_code 是否已经包含了此公司名称如果已包含不允许新增该公司名称
    public function getInvoiceCompanyInfo(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        //获取发票表中名称
        $query=' SELECT 1 FROM vtiger_invoicecompany WHERE invoicecompany=? limit 1 ';
        $result=$db->pquery($query,array($_REQUEST['companyFullName']));
        $return =array("success"=>false);
        if($db->num_rows($result)){
            $return = array("success"=>true,"message"=>'该公司名称已经使用过不能重复添加！');
        }
        // 获取公司表名称
        $query=' SELECT 1 FROM vtiger_company_code WHERE companyfullname=? limit 1 ';
        $result=$db->pquery($query,array($_REQUEST['companyFullName']));
        if($db->num_rows($result)){
            $return = array("success"=>true,"message"=>'该公司名称已经使用过不能重复添加！');
        }
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }
    /**
     * 2015-1-13 wangbin 商机客户查找
     * index.php?module=ServiceContracts&action=BasicAjax&record=合同id&mode=receivepay
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function saveinvoicecompany(Vtiger_Request $request){
        $invoicecompany  = $request->get('invoicecompany');
        $companycode  = $request->get('companycode');
        global $adb;
        $query="INSERT INTO `vtiger_invoicecompany`(`invoicecompany`, `companycode`) VALUES (?,?)";
        $adb->pquery($query,array($invoicecompany,$companycode));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }
    /**
     * 批量转换的商机列表
     * @param Vtiger_Request $request
     */
    public function savecompanycodeuserid(Vtiger_Request $request){

        $companycode= $request->get('companycode');

        $userid=$request->get('userid');
        $modulename=$request->get('modulename');
        do{


            $db = PearDatabase::getInstance();
            foreach($userid as $value){
                if(is_numeric($value)){

                    $db->pquery('INSERT INTO `vtiger_invoicecompanyuser` (`invoicecompany`, `userid`,modulename, `deleted`) VALUES (? ,?, ?, 0)',
                        array($companycode, $value,$modulename));

                }
            }
        }while(0);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }
    /**
     * 删除合同主体及编码
     * @param Vtiger_Request $request
     */
    public function deletedInvoiceCompany(Vtiger_Request $request){
        $id=$request->get('id');
        $db = PearDatabase::getInstance();
        $sql='DELETE FROM vtiger_invoicecompany WHERE invoicecompanyid=?';
        $db->pquery($sql,array($id));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }

    /**
     * 删除合同主体与用户
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function deletedInvoiceCompanyUser(Vtiger_Request $request){

        $id=$request->get('id');
        $db = PearDatabase::getInstance();
        $sql='DELETE FROM vtiger_invoicecompanyuser WHERE invoicecompanyuserid=?';
        $db->pquery($sql,array($id));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }
    public function addCompanyFXQ($request){
        global $fangxinqian_url,$adb;
        $record=$request->get('recordid');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'CompayCode');
        $contractsRecordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $data=array("companyName"=>$recordModel->get('companyfullname'),
                    "creditCode"=>$recordModel->get('taxnumber'),
                    "managerPhone"=>$recordModel->get('telphone'),
                    "companyCode"=>$recordModel->get('company_codeno'));
        $isSync = $recordModel->get('issync');
        //未同步过则新增
        if(!$isSync){
            $url=$fangxinqian_url.'common/add_company';
            $res =  $contractsRecordModel->https_requestcomm($url,json_encode($data),$contractsRecordModel->getCURLHeader(),true);
            $result = json_decode($res,true);
            if($result['success']){
                //修改是否同步过为1
                $sql = "update vtiger_company_code set issync=1 where companyid=?";
                $adb->pquery($sql,array($record));
            }
            echo $res;
            exit();
        }
        //已同步过 则调修改
        $url=$fangxinqian_url.'common/update_company';
        echo $contractsRecordModel->https_requestcomm($url,json_encode($data),$contractsRecordModel->getCURLHeader(),true);
    }


}
