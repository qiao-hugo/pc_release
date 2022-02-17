<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractsProducts_Detail_View extends Vtiger_Detail_View {
    function preProcess(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $viewer->assign('NO_SUMMARY', true);
        parent::preProcess($request);
    }
    public function process(Vtiger_Request $request) {

        $viewer = $this->getViewer ($request);
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $db = PearDatabase::getInstance();
        $sql="SELECT vtiger_contract_type.contract_type as contract_type FROM `vtiger_contract_type`WHERE vtiger_contract_type.contract_typeid =(SELECT vtiger_contractsproductsrel.contract_type FROM `vtiger_contractsproductsrel` WHERE relcontractsproductsid='".$recordId."')";
        $result = $db->pquery($sql);
        $contract_type = $db->query_result($result,'contract_type');

//        $contract_type = $db->fetchByAssoc($result);
        if ($db->num_rows($result)>0) {
            $viewer->assign('RECORD_CONTRACT_TYPE', $contract_type);
        }

//        if ($db->num_rows($result)>0) {
//            for($i=0;$i < $db->num_rows($result);$i++)
//            {
////                    $product_list[] = $db->query_result($result,$i);
//                $contractsproduct_type_list[] = $db->fetchByAssoc($result);
//            }
//        }
        //读取产品类型
//        foreach($contractsproduct_type_list as $contractsproduct_type_value){
//            $contract_type = $contractsproduct_type_value['contract_type'];
//
//        }
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ContractsProducts');
       //print_r($recordModel);

        $entity=$recordModel->entity->column_fields;
        $query="SELECT contract_type FROM vtiger_contract_type WHERE  contract_typeid=?";
        $contract_result = $db->pquery($query,array($entity['contract_type']));

        $contract_namelist = $db->query_result($contract_result,0,'contract_type');

        $productid=explode(' |##| ', $entity['relproductid']);
         foreach($productid as $value){

             $product_result = $db->pquery(" SELECT productname FROM `vtiger_products` WHERE productid=".$value."");

             $product_namelist[] = $db->query_result($product_result,'productname');
         }

        $viewer->assign('RECORD_CONTRSCT_NAMELIST',  $contract_namelist);
        $viewer->assign('RECORD_PRODUCT_NAMELIST', $product_namelist);
        $viewer->assign('INVOICECOMPANYBILL', $recordModel->getInvoicecompanyList($recordId));

        //根据合同类型查询对应的产品

       parent::process($request);
    }

}
	