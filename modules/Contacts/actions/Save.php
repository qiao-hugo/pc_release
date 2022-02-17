<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Contacts_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
		$result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
		$_FILES = $result['imagename'];

		//To stop saveing the value of salutation as '--None--'
		$salutationType = $request->get('salutationtype');
		if ($salutationType === '--None--') {
			$request->set('salutationtype', '');
		}
		parent::process($request);
	}
    public function saveRecord($request) {
        global $adb,$current_user;
        $mobile=$request->get('mobile');
        $record=$request->get('record');
        $recordModel = $this->getRecordModelFromRequest($request);
        $entity=$recordModel->entity->column_fields;
        $recordModel->save();
        if(!empty($record) && $mobile!=$entity['mobile']){
            function findreport($reportsModel,&$array=array()){
                if($reportsModel->id==1 || $reportsModel->id==38){
                    return $array;
                }
                $reportsModel = Users_Privileges_Model::getInstanceById($reportsModel->reports_to_id);
                $array['id'][]=$reportsModel->id;
                $array['email']=$array['email'].'|'.$reportsModel->email1;
                $array['reportemail'][$reportsModel->id]['mail']=$reportsModel->email1;
                $array['reportemail'][$reportsModel->id]['name']=$reportsModel->last_name;
                if($reportsModel->reports_to_id ==38 || $reportsModel->reports_to_id ==1 || empty($reportsModel->reports_to_id)){
                    return $array;
                }
                return findreport($reportsModel,$array);
            }
            $accountRecordModel=Vtiger_Record_Model::getInstanceById($recordModel->get('account_id'),'Accounts');
            $reportsModel = Users_Privileges_Model::getInstanceById($accountRecordModel->get('assigned_user_id'));
            $array=findreport($reportsModel);
            if(!empty($array) && in_array(43,$array['id'])){
                $query="SELECT  GROUP_CONCAT(departmentid,'|',departmentname) AS departmentidandname FROM vtiger_departments WHERE departmentid in('".$reportsModel->departmentid."','".$current_user->departmentid."')";
                $departmentsResult=$adb->pquery($query,array());
                $accountName=$reportsModel->last_name;
                $currentUserName=$current_user->last_name;
                if($adb->num_rows($departmentsResult)){
                    $departmentidandname=$departmentsResult->fields['departmentidandname'];
                    $departmentidandnameArrayTemp=explode(',',$departmentidandname);
                    $departmentidandnameArray=array();
                    foreach($departmentidandnameArrayTemp as $value){
                        $temp=explode('|',$value);
                        $departmentidandnameArray[$temp[0]]=$temp[1];
                    }
                    $accountName=$reportsModel->last_name.'【'.$departmentidandnameArray[$reportsModel->departmentid].'】';
                    $currentUserName=$current_user->last_name.'【'.$departmentidandnameArray[$current_user->departmentid].'】';
                }
                $email=trim($array['email'],'|');
                $content='客户名称:'.$accountRecordModel->get('accountname').'<br>客户负责人:'.$accountName.'<br>修改操作人:'.$currentUserName.'<br>修改前手机号:'.$entity['mobile'].'<br>修改后手机号:'.$mobile.'<br>修改时间:'.date('Y-m-d H:i:s');
                //$recordModel->sendWechatMessage(array('email' => trim($email), 'content' => $content,  'flag' => 6));
                $recordModel->sendWechatMessage(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>'修改手机号提醒！！'.$currentUserName,'flag'=>7));
                $recordModel->sendMail('修改手机号提醒！！-'.$currentUserName,$content,$array['reportemail'],'ERP系统');
            }
        }
        if($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
            $parentRecordId = $request->get('sourceRecord');
            $relatedModule = $recordModel->getModule();
            $relatedRecordId = $recordModel->getId();

            $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
            $relationModel->addRelation($parentRecordId, $relatedRecordId);
        }
        return $recordModel;
    }
    
        public function getRecordModelFromRequest(Vtiger_Request $request) {


        $moduleName = $request->getModule();
        $recordId = $request->get('record');

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        if(!empty($recordId)) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            $modelData = $recordModel->getData();
            $recordModel->set('modcommentsid', $recordId);

            $recordModel->set('mode', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $modelData = $recordModel->getData();
            $recordModel->set('mode', '');
        }


        $fieldModelList = $moduleModel->getFields();

        foreach ($fieldModelList as $fieldName => $fieldModel) {
            $fieldValue = $request->get($fieldName, null);
            $fieldDataType = $fieldModel->getFieldDataType();
            if($fieldDataType == 'time'){
                $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
            }
            if($fieldValue !== null) {
                if(!is_array($fieldValue)) {
                    $fieldValue = trim($fieldValue);
                }
                $recordModel->set($fieldName, $fieldValue);

            }

        }


        return $recordModel;
    }
}
