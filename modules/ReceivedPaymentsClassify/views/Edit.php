<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Class ReceivedPaymentsClassify_Edit_View extends Vtiger_Edit_View {
    protected $record = false;
	function __construct() {
		parent::__construct();
	}
	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');
        $collate = $request->get('collate');
        //判断是否为核对编辑
        if ($collate) {
            $module_Model = Vtiger_Module_Model::getCleanInstance($moduleName);
            $collate_premission = $module_Model->exportGrouprt($moduleName, 'COLLATE');
            if (!$collate_premission) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            }
            $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $moduleData = $recordModel->getData();
            if ($moduleData['last_collate_status'] == 'fit') {
                throw new AppException('回款已核对符合,不允许编辑!');
            } elseif (empty($moduleData['last_collate_status']) && $moduleData['first_collate_status'] == 'fit') {
                throw new AppException('回款已核对符合,不允许编辑!');
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('is_collate', $collate);
        } else {
            $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);

            if (!$recordPermission) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            }
            if (!empty($record)) {
                $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
                $moduleData = $recordModel->getData();
                if ($moduleData['last_collate_status'] == 'fit') {
                    throw new AppException('回款已核对符合,不允许编辑!');
                } elseif (empty($moduleData['last_collate_status']) && $moduleData['first_collate_status'] == 'fit') {
                    throw new AppException('回款已核对符合,不允许编辑!');
                }
                if ($moduleData['ismatchdepart'] == '1') {
                    throw new AppException('匹配成功的回款不能编辑！');
                    exit;
                }
                if ($moduleData['receivedstatus'] != 'normal') {
                    throw new AppException('只有正常状态的回款才能编辑！');
                    exit;
                }
            }
            //young.yang 2015-1-3 增加编辑页面，对于流程状态的控制，某些状态不允许编辑
            global $isallow;
            if (in_array($moduleName, $isallow)) {
                if (!empty($record)) {//新增排除
                    $module = SalesorderWorkflowStages_Record_Model::getInstanceById(0);
                    $result = $module->getWorkflowsStatus($moduleName, $record);
                    if (!empty($result)) {
                        if (!$result['success']) {
                            throw new AppException($result['result']);
                        }
                    }
                }

            }
            //end
        }
	}

	public function process(Vtiger_Request $request) {

		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$record = $request->get('record');
        if(!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
        }else if(!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }
        if(!$this->record){
            $this->record = $recordModel;
        }

		$moduleModel = $recordModel->getModule();
		$fieldList = $moduleModel->getFields();
		$requestFieldList = array_intersect_key($request->getAll(), $fieldList);
		foreach($requestFieldList as $fieldName=>$fieldValue){
			$fieldModel = $fieldList[$fieldName];
			$specialField = false;
			// We collate date and time part together in the EditView UI handling
			// so a bit of special treatment is required if we come from QuickCreate
			if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'time_start' && !empty($fieldValue)) {
				$specialField = true;
				// Convert the incoming user-picked time to GMT time
				// which will get re-translated based on user-time zone on EditForm
				$fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i");

			}

            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'date_start' && !empty($fieldValue)) {
                $startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
                $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
                list($startDate, $startTime) = explode(' ', $startDateTime);
                $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
            }
			if($fieldModel->isEditable() || $specialField) {
				$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
			}
		}
		//wangbin 2015-1-16 17:34:51 如果回款已收的话就不允许编辑
		$this->db = PearDatabase::getInstance();
		$status1=$this->db->pquery('select discontinued from vtiger_receivedpayments where receivedpaymentsid=?',array($record));
		$status1 = $status1->fields['discontinued'];
		if($status1==1){
			throw new AppException(vtranslate($moduleName).' '.vtranslate('LBL_NOT_ACCESS'));
			exit;
		}
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);

		$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);


		//2015年4月21日 星期二  添加回款业绩分配相关修改
		if(!empty($record)){
		    //$sql = "SELECT * FROM `vtiger_achievementallot` WHERE receivedpaymentsid = ?";
            $sql = "SELECT achievementallotid, owncompanys, receivedpaymentsid, receivedpaymentownid,( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_achievementallot.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownname, businessunit FROM `vtiger_achievementallot` WHERE receivedpaymentsid = ?";
		    $achievementallot = $this->db->pquery("$sql",array($record));
		    $nums = $this->db->num_rows($achievementallot);
		    $achievementallotdata = array();
		    if($nums > 0) {
		        for($i=0; $i<$nums; ++$i) {
		            $achievementallotdata[] = $this->db->query_result_rowdata($achievementallot, $i);
		        }
		    }
            $sql2 = "SELECT * FROM vtiger_receivedpayments_extra WHERE receivementid = ?";
            $receivepaymentextra = $this->db->pquery("$sql2",array($record));
            $count_extra = $this->db->num_rows($receivepaymentextra);
            $extra_data = array();
            if($count_extra>0){
                for($i=0;$i<$count_extra;$i++){
                    $extra_data[] = $this->db->query_result_rowdata($receivepaymentextra,$i);
                }
            }
		}else{
		    $achievementallotdata=array();
            $extra_data = array();
		}
		$accessibleUsers = get_username_array($where);  //人员信息
		$getcompanysql ="SELECT owncompany FROM `vtiger_owncompany`";
        $company = $this->db->pquery($getcompanysql,array());
        $owncompany = array();
        $sums=$this->db->num_rows($company);
        if($sums>0){
            while($row = $this->db->fetchByAssoc($company)){
                //var_dump();
                $owncompany[$row['owncompany']] = $row['owncompany'];
            }
        }
        $getBusinessUnitsql = "SELECT BusinessUnittype FROM `vtiger_businessunit`";
        $bussiness = $this->db->pquery($getBusinessUnitsql,array());
        $BusinessUnit = array();
        while($rows = $this->db->fetchByAssoc($bussiness)){
            $BusinessUnit[$rows['businessunittype']] = $rows['businessunittype'];
        }
        $viewer->assign('ACHIEVEMENTALLOTDATA',$achievementallotdata);
        $viewer->assign('EXTRA_DATA',$extra_data);
        $viewer->assign('BUSINESSUNIT',$BusinessUnit);
        $viewer->assign('OWNCOMPANY',$owncompany);
        $viewer->assign('ACCESSIBLE_USERS',$accessibleUsers);
        //end

		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE',Zend_Json::encode($picklistDependencyDatasource));
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('CURRENTDATE', date('Y-n-j'));
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('RECORD',$recordModel);


		$isRelationOperation = $request->get('relationOperation');

		//if it is relation edit
		$viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
		if($isRelationOperation) {
			$viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
			$viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
		}
		$viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
		$viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
		$viewer->view('EditView.tpl', $moduleName);

	}
}
