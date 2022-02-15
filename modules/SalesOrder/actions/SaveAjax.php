<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SalesOrder_SaveAjax_Action extends Vtiger_SaveAjax_Action {
    public function process(Vtiger_Request $request){
        //防止工单完成后再次修改工单数据
        $recordId = $request->get('record');//合同的id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'SalesOrder');
        $modulestatus=$recordModel->entity->column_fields['modulestatus'];


        
        if( strstr($modulestatus,'c_') || strstr($modulestatus,'b_') ) {
            global $adb;
            $isaction = false;
            $products = $request->get('productids');
            if (!empty($products)) {
                $sql = "select isaction from vtiger_salesorderworkflowstages where salesorderid=? AND productid=? AND isaction=2 ";
                $sel_result = $adb->pquery($sql, array($recordId, $products[0]));
                $res_cnt = $adb->num_rows($sel_result);
                if($res_cnt > 0) {
                    $isaction = true;
                }
            }

            if ($isaction) {
                $result=array();
                $response = new Vtiger_Response();
                //$response->setEmitType(Vtiger_Response::$EMIT_JSON);
                $response->setResult(array('flag'=>1));
                $response->emit();
                exit;
            }
        }
        parent::process($request);
    }
}
