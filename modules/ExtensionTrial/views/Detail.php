<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ExtensionTrial_Detail_View extends Vtiger_Detail_View {
    function preProcess(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$viewer->assign('NO_SUMMARY', true);
		parent::preProcess($request);
	}
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 */
	function showModuleDetailView(Vtiger_Request $request) {
		//echo parent::showModuleDetailView($request);
		//echo $this->getWorkflowsM($request);
		global $current_user;
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
	
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
	
	
		$recordModel = $this->record->getRecord();

		echo $this->getWorkflowsM($request,$recordModel);
		
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();


		$moduleModel = $recordModel->getModule();
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
	
		return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
	}
	
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 * @return type
	 */
	function showDetailViewByMode(Vtiger_Request $request) {
		return $this->showModuleDetailView($request);
	}
	
	function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}


	/**
	 * 生成工作流
	 * 2014-12-26 young.yang 从工单模块迁移过来，作为通用
	 * @param Vtiger_Request $request
	 */
	function getWorkflowsbak(Vtiger_Request $request){

		$moduleName = $request->getModule();
		$recordId=$request->get('record');
		$db=PearDatabase::getInstance();
		global $current_user;
		//$salesorderModule=Vtiger_Record_Model::getInstanceById($recordId,'SalesOrder');
	
		//获取工作流
		$modelModule = SalesorderWorkflowStages_Record_Model::getInstanceById($recordId);
		$model=$modelModule->getAll($recordId,$moduleName);
		//$modelcount = count($model);
		$roleandworkflowsstages=getWorkflowsByUserid();
		//$temp=array();
		$isrole=0;
		if(!empty($roleandworkflowsstages)){
			$roleandworkflowsstages=explode(',',$roleandworkflowsstages);
		}else{
			$roleandworkflowsstages=array();
		}

		//页面审核按钮根据权限生成
		//$user=getAccessibleUsers('WorkFlowCheck','List',true);
		$user=Users_Privileges_Model::getInstanceById($current_user->id);
		//end
		//$stagerecordid= 0 ;
		//获取当前活动节点
		//管理员或有下级审核权限的显示审核
		$workObj=new WorkFlowCheck_ListView_Model();
		$allStagers = $workObj->getActioning($moduleName,$recordId);
		$isaction=0;

		foreach($model as $key=>$val){
			if($val[isaction]==1){
				//管理员或者有审核节点
				//if($current_user->is_admin=='on' || isset($allStagers[$val['salesorderworkflowstagesid']])){
				//审核所有或下属
					//if($user=='1=1'|| in_array($val['smcreatorid'],$user)){
					//if(isset($allStagers[$val['salesorderworkflowstagesid']])){
						$val['check']=1;
						$isrole=1;

						if(empty($stagerecordid)){
							$stagerecordid=$val['salesorderworkflowstagesid'];
							$stagerecordname=$val['workflowstagesname'];
							$workflowsstageid=$val['workflowstagesid'];
							$salesorderid=$val['salesorderid'];
							//$_SESSION['isyourcode']=$moduleName.$recordId;//当前人有审核的权限
							if($val['productid']){
								$result=$db->pquery('select salesorderproductsrelid from vtiger_salesorderproductsrel where (servicecontractsid=? or salesorderid=?) and productid=?',array($salesorderid,$salesorderid,$val['productid']));
								if($db->num_rows($result)){
									$data=array('module'=>'SalesorderProductsrel','record'=>$db->query_result($result, 0,'salesorderproductsrelid'));
								}
							}else{
								$data=array('module'=>$val['modulename'],'record'=>$val['salesorderid']);
							}
						}
					//}	
				//}
			}
			$models[$val['sequence']][$key]=$val;  //将 workflowstagesid 换成 sequence为兼容自动生成的节点没有 workflowstagesid =0；
		}
        /*if($isaction==0){
            unset($_SESSION['isyourcode']);
        }*/

		//actionid
		//获取当前活动时间
		$db=PearDatabase::getInstance();
		//获取打回历史
		$salesorderhistory = $db->pquery('SELECT last_name,rejecttime,reject,rejectname,rejectnameto FROM vtiger_salesorderhistory soh,vtiger_users user WHERE soh.rejectid=user.id and soh.salesorderid=? ORDER BY soh.salesorderhistoryid DESC', array($recordId));
        //获取备注列表
        $remarklist = $db->pquery('SELECT salesorderhistoryid,modifytime,last_name, rejecttime, reject, rejectname, rejectnameto, rejectid FROM vtiger_salesorderremark soh LEFT JOIN vtiger_users USER ON soh.rejectid = USER.id where soh.salesorderid =? ORDER BY soh.salesorderhistoryid DESC',array($recordId));
        //获取流程节点审核
        $workflowsstagelist = $db->pquery("SELECT salesorderworkflowstagesid, workflowstagesname, isaction, IF ( isaction = 2, '已审核', IF ( isaction = 1, '审核中', '未激活' )) AS actionstatus, actiontime, IF ( ishigher = 1, ( SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '<br>' ) AS last_name FROM vtiger_users WHERE vtiger_salesorderworkflowstages.higherid = vtiger_users.id ), ( SELECT ( SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '<br>' ) FROM vtiger_users WHERE id IN ( SELECT vtiger_user2role.userid FROM vtiger_user2role WHERE vtiger_user2role.roleid IN ( SELECT vtiger_role.roleid FROM vtiger_role WHERE FIND_IN_SET( vtiger_role.roleid, REPLACE ( vtiger_workflowstages.isrole, ' |##| ', ',' ))))) FROM vtiger_workflowstages WHERE vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid AND vtiger_workflowstages.isrole IN ('H102', 'H104', 'H90'))) AS higherid, IFNULL(( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_salesorderworkflowstages.auditorid = vtiger_users.id ), '--' ) AS auditorid, auditortime, createdtime, ( SELECT ( SELECT GROUP_CONCAT(rolename) FROM vtiger_role WHERE FIND_IN_SET( vtiger_role.roleid, REPLACE ( vtiger_workflowstages.isrole, ' |##| ', ',' ))) FROM vtiger_workflowstages WHERE vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid ) AS isrole, ( SELECT ( SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '<br>' ) FROM vtiger_users WHERE FIND_IN_SET( vtiger_users.id, REPLACE ( vtiger_products.productman, ' |##| ', ',' ))) FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderworkflowstages.productid ) AS productid FROM vtiger_salesorderworkflowstages WHERE salesorderid = ? ORDER BY vtiger_salesorderworkflowstages.sequence ASC",array($recordId));

        //注释掉，是老的代码
        /*$recordModel = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
                $moduleModel = $recordModel->getModule();
                /*$fieldList = $moduleModel->getFields();
                $requestFieldList = array_intersect_key( $fieldList);

                foreach($requestFieldList as $fieldName=>$fieldValue){
                    $fieldModel = $fieldList[$fieldName];

                    if($fieldModel->isEditable()) {
                        $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
                    }
                }*/
		
		//wangbin 2015年03月25日 星期三  
		$sqlproject = "SELECT `projectid`,`projectname` FROM  `vtiger_project`";
		$projects = $db->pquery($sqlproject,array());
		
	    $projectarr = array();
        
        while($row=$db->fetch_array($projects)){
            $projectarr[] = array($row['projectid'],$row['projectname']);
        }
		$viewer = $this->getViewer($request);
		$viewer->assign('STAGES',$models); //工作流stagesid
		$viewer->assign('STAGESCOUNT',count($models));//工作流的数量

		$viewer->assign('ISROLE', $this->isrole($request));   //是否有权限审核

		//$viewer->assign('ISROLE', '0');
		$viewer->assign('STAGERECORDID',$stagerecordid);//当前工作流的id
		$viewer->assign('STAGERECORDNAME',$stagerecordname);//当前工作流的名字
		$viewer->assign('SALESORDERHISTORY',$salesorderhistory);//打回历史记录
        $viewer->assign('WORKFLOWSSTAGELIST',$workflowsstagelist);//审核节点
		$viewer->assign('REMARKLIST',$remarklist);
		$viewer->assign('DATA',$data);
		$viewer->assign('USER',$user->id);
		$viewer->assign('RECORD',$recordId);
		
		$viewer->assign('PROJECTNAME',$projectarr);
		return $viewer->view('LineItemsWorkflows.tpl', "$moduleName",true);
	}

	/*
	*重写延期申请是否有权限审核
	*2021-08-13作废
	*/
	public function isrole(Vtiger_Request $request) {
		global $current_user;
		$record = $request->get('record');

		$sql = "SELECT ( SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE vtiger_departments.departmentid = vtiger_user2department.departmentid ) AS parentdepartment, vtiger_user2department.departmentid, vtiger_extensiontrial.extensionfrequency FROM vtiger_extensiontrial LEFT JOIN vtiger_crmentity ON vtiger_extensiontrial.extensiontrialid = vtiger_crmentity.crmid LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_crmentity.smownerid WHERE vtiger_extensiontrial.extensiontrialid = ? LIMIT 1";
		$db = PearDatabase::getInstance();
		$sel_result = $db->pquery($sql,array($record));
		$res_cnt = $db->num_rows($sel_result);
		$parentdepartment = ''; //上级部门
		$extensionfrequency = 1;
		if($res_cnt > 0) {
		    $row = $db->query_result_rowdata($sel_result, 0);
		    $parentdepartment = $row['parentdepartment'];
		    $extensionfrequency = empty($row['extensionfrequency']) ? 1 : $row['extensionfrequency'];
		}
		$parentTepamentData = explode('::', $parentdepartment);

		$t_array = array(
			'1'=>'oneaudituid',
			'2'=>'towaudituid'
		);
		$sql = "SELECT department FROM vtiger_auditsettings WHERE auditsettingtype='ServiceContracts' AND ".$t_array[$extensionfrequency]."=?";
		$sel_result = $db->pquery($sql, array($current_user->id));
		$settingTepamentData = array();
		while($rawData=$db->fetch_array($sel_result)) {
            $settingTepamentData[] = $rawData['department'];
        }
        // 解决思想 找到该客户所应部门的上级（数组） 和 我们设置 vtiger_auditsettings 表里面的部门（数组） 交集
		// 有值-》有权限  没值-》没权限

        $tt = array_intersect($parentTepamentData, $settingTepamentData);

        if (count($tt) > 0) {
        	return '1';
        }

        return 0;
 	}
}
	