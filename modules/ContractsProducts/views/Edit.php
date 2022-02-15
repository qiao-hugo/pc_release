<?php
header("Content-type: text/html; charset=utf-8");
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class ContractsProducts_Edit_View extends Vtiger_Edit_View
{
    protected $record = false;

    function __construct()
    {
        parent::__construct();
    }


    public function process(Vtiger_Request $request)
    {
        /*
         * 自定义编辑显示标题
         * */
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        $record = $request->get('record');

        $db = PearDatabase::getInstance();
        $sql="SELECT vtiger_contract_type.contract_type as contract_type FROM `vtiger_contract_type`WHERE vtiger_contract_type.contract_typeid =(SELECT vtiger_contractsproductsrel.contract_type FROM `vtiger_contractsproductsrel` WHERE relcontractsproductsid='".$record."')";
        $resultsql = $db->pquery($sql);
        $contract_type = $db->query_result($resultsql,'contract_type');
        if ($db->num_rows($resultsql)>0) {
            $viewer->assign('RECORD_CONTRACT_TYPE', $contract_type);
        }


        // 加入到期时间的条件，过期的就不显示出来
        $result = $db->pquery("SELECT productid,productname FROM `vtiger_products`  WHERE enddate>'".date('Y-m-d 00:00:00',time())."' ORDER BY productname");
        if ($db->num_rows($result)>0) {
            for($i=0;$i < $db->num_rows($result);$i++)
            {
//                    $product_list[] = $db->query_result($result,$i);
                $product_list[] = $db->fetchByAssoc($result);
            }
        }
        $record = $request->get('record');
        $contract_typeid='';
        if(!empty($record)){
            $recordModel = Vtiger_Record_Model::getInstanceById($record, 'ContractsProducts');
            $entity=$recordModel->entity->column_fields;
            $productid=explode(' |##| ', $entity['relproductid']);
            $contract_typeid= $entity['contract_type'];

        }else{
            $productid=array();
        }

        $viewer->assign('RECORD_PRODUCT_ID',$productid);
        $viewer->assign('RECORD_PRODUCT_LIST', $product_list);

        //读取合同产品关联表
        $relcontract_typeid = $db->pquery("SELECT contract_type FROM `vtiger_contractsproductsrel`");
        if ($db->num_rows($relcontract_typeid)>0) {
            for($m=0;$m < $db->num_rows($relcontract_typeid);$m++)
            {
               // $relcontract_typeid_list[] = $db->query_result($result,$m,'contract_type');
                $row= $db->fetchByAssoc($relcontract_typeid);
                $relcontract_typeid_list[$row['contract_type']] =1 ;
            }
        }

       // echo $viewer->view("EditViewBlocksProducts.tpl",$moduleName);
        //读取合同类型
        $result_contract_type = $db->pquery("SELECT contract_typeid,contract_type FROM `vtiger_contract_type` ORDER BY contract_type");
        if ($db->num_rows($result_contract_type)>0) {
            for($j=0;$j < $db->num_rows($result_contract_type);$j++)
            {
                $row= $db->fetchByAssoc($result_contract_type);
                if(isset($relcontract_typeid_list[$row['contract_typeid']]) && $row['contract_typeid']!=$contract_typeid){
                    continue;
                }
                $contract_type_list[] = $row;
            }
        }

        $viewer->assign('RECORD_CONTRACTTYPE_ID_LIST',$relcontract_typeid_list);  //关联表所有合同id
        $viewer->assign('RECORD_CONTRACTTYPE_ID',$contract_typeid);          //关联表所拥有的部分合同id(编辑)
        $viewer->assign('RECORD_CONTRACT_TYPE_LIST', $contract_type_list);   //合同类型表所有合同类型
        parent::process($request);
    }
}