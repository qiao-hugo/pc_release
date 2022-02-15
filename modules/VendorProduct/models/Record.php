<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Inventory Record Model Class
 */
class VendorProduct_Record_Model extends Vtiger_Record_Model {
    /**
     * 验证用户ID和账号信息是不能重复
     * @param Vtiger_Request $request
     */
    public function checkIdAndProductProvider(Vtiger_Request $request){
        $recordId=$request->get('record');
        $productid=$request->get('productid');
        $vendorid=$request->get('vendorid');
        $idaccount=$request->get('idaccount');
        global $adb;
        $recordId=$recordId>0?$recordId:0;
        //$result=$adb->pquery("SELECT 1 FROM `vtiger_productprovider` LEFT JOIN vtiger_crmentity ON vtiger_productprovider.productproviderid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_productprovider.productid=? AND vtiger_productprovider.vendorid=? AND vtiger_productprovider.productproviderid<>?",array($productid,$vendorid,$recordId));
        $result=$adb->pquery("SELECT 1 FROM `vtiger_productprovider` LEFT JOIN vtiger_crmentity ON vtiger_productprovider.productproviderid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_productprovider.idaccount=?  AND vtiger_productprovider.productproviderid<>?",array($idaccount,$recordId));
        $res=false;
        if($adb->num_rows($result)){
            $res=true;
        }
        return $res;
    }

    /**
     * 更新账号审核状态
     * @param $recordid
     */
    public function updataModuleStatus($recordid){
        global $adb;
        $adb->pquery('UPDATE vtiger_productprovider SET modulestatus=\'b_actioning\' WHERE productproviderid=?',array($recordid));
    }
    public function doResubmit(Vtiger_Request $request){
        $recordid=$request->get('record');
        $params['recordId']=$recordid;
        $params['fieldName']='modulestatus';
        $params['oldValue']='c_complete';
        $params['module']='ProductProvider';
        $params['Value']='a_normal';
        $this->setModTrack($params);
        global $adb;
        $adb->pquery('UPDATE vtiger_productprovider SET modulestatus=\'a_normal\' WHERE productproviderid=?',array($recordid));
        $adb->pquery('DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename=?',array($recordid,'ProductProvider'));


    }
    public function setModTrack($params){
        $datetime=date('Y-m-d H:i:s');
        global $adb,$current_user;
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id , $params['recordId'], $params['module'], $current_user->id, $datetime, 0));
        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
            Array($id, $params['fieldName'],$params['oldValue'], $params['Value']));
    }
}