<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class ShareVendors_Save_Action extends Vtiger_Save_Action {

    public function saveRecord($request) {
        $vendorsid=$request->get('vendorsid');
        $userid=$request->get('userid');
        $record=$request->get('record');
        global $adb;
        if($vendorsid && $userid){
            $accountModel=Vtiger_Record_Model::getInstanceById($vendorsid,'Vendors');
            $accountuserid=$accountModel->get('assigned_user_id');
            $servicecontractModule=Vtiger_Module_Model::getInstance('ServiceContracts');
            $authority=getAccessibleUsers('Vendors','List',true);
            if($authority=='1=1')$authority=array($accountuserid);
            if(!in_array($accountuserid,$authority) && !$servicecontractModule->exportGrouprt('ShareAccount','addshareaccount')){
                echo '你没有添加该客户共享权限<a href="javascript:history.go(-1);">返回</a>';
                exit;
            }

            if(empty($record)){

                $query='SELECT 1 FROM vtiger_sharevendors WHERE vendorsid=? AND userid=?';
                $result=$adb->pquery($query,array($vendorsid,$userid));
                if($adb->num_rows($result)){
                    echo '该客户共享商务已存在<a href="javascript:history.go(-1);">返回</a>';
                    exit;
                }
            }

        }else{
            echo '你没有添加该客户共享权限<a href="javascript:history.go(-1);">返回</a>';
            exit;
        }
        //$sharestatus=empty($request->get('sharestatus'))?0:1;
        $recordModel = $this->getRecordModelFromRequest($request);


        $recordModel->save();
        if(empty($record)) {
            global $current_user;
            $datetime=date('Y-m-d H:i:s');
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $newuser=new Users();
            $newuser->retrieveCurrentUserInfoFromFile($userid);
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $vendorsid, 'Vendors', $current_user->id, $datetime, 0));
            $description = $current_user->last_name.' 添加共享商务:'.$newuser->last_name;
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'description', '', $description));

            /*if($sharestatus){
                $adb->pquery('UPDATE vtiger_account SET shareaccountuserid=CONCAT(IFNULL(shareaccountuserid,\'\'),\'*'.$userid.'*\') WHERE accountid=?',array($accountid));
            }*/

        }else{
            /*if($recordModel->getEntity()->column_fields['sharestatus']!=$sharestatus){
                if($sharestatus){
                    $adb->pquery('UPDATE vtiger_account SET shareaccountuserid=CONCAT(IFNULL(shareaccountuserid,\'\'),\'*'.$userid.'*\') WHERE accountid=?',array($accountid));
                }else{
                    $adb->pquery('UPDATE vtiger_account SET shareaccountuserid=replace(shareaccountuserid,\'*'.$userid.'*\',\'\') WHERE accountid=?',array($accountid));
                }
            }*/
        }


        /*
        if($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
            $parentRecordId = $request->get('sourceRecord');
            $relatedModule = $recordModel->getModule();
            $relatedRecordId = $recordModel->getId();

            $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
            $relationModel->addRelation($parentRecordId, $relatedRecordId);
        }*/
        return $recordModel;
    }
}
