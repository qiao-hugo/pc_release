<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class DepaSalestarget_Detail_View extends Vtiger_Index_View {
	protected $record = false;
	function __construct() {
		parent::__construct();
		$this->exposeMethod('showDetailViewByMode');
		$this->exposeMethod('showModuleDetailView');
		$this->exposeMethod('showModuleSummaryView');
		$this->exposeMethod('showModuleBasicView');
		$this->exposeMethod('showRecentActivities');
		$this->exposeMethod('showRecentComments');
		$this->exposeMethod('showRelatedList');
		$this->exposeMethod('showChildComments');
		$this->exposeMethod('showAllComments');
		$this->exposeMethod('getActivities');
		$this->exposeMethod('getWorkflows');
		$this->exposeMethod('getWorkflowsContent');
		$this->exposeMethod('getProducts');
		$this->exposeMethod('getProductById');
		$this->exposeMethod('getProductBySalesorderid');
		$this->exposeMethod('editFields');
	}

	function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		//权限判断，在通过详细页面点过来的地方要使用，
		/* global $isallow;
		$referer_module=$request->get('refer_module');//来路
		$referer_id=$request->get('referer_id');
		if(!empty($referer_module)){
			//如果有必要做一次传入referer的数据验证
			if(!empty($recordId)){//新增
				if(in_array($moduleName,$isallow)){
					$module=SalesorderWorkflowStages_Record_Model::getInstanceById(0);
					$result=$module->getPermission($moduleName, $recordId,$referer_id);//@TODO 需要判断上下级关系，即上级也可以看到这个页面但是不能审核
					if($result){
						return true;
					}
				}
			}
		} */
		//1.编辑权限，有上下级关系的，或者本人，或者有审核权限的人
		/* if(!empty($recordId)){
			if(isset($_SESSION['isyourcode'])&&$_SESSION['isyourcode']==$moduleName.$recordId){
				//偶审核权限的人，通过isyourcode值来判断
			}else{
				$user=getAccessibleUsers($moduleName,'Edit',true);
					
				$recordModule=Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
				$recordField=$recordModule->getData();
				if(isset($recordField['assigned_user_id'])){
					$id=$recordField['assigned_user_id'];
				}elseif(isset($recordField['smcreatorid'])){
					$id=$recordField['smcreatorid'];
				}
				if(is_array($user)&& !in_array($id,$user)){
					throw new AppException(vtranslate('没有访问权限'));
				}
			}
				
		} */
		//end
		$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId);
		if(!$recordPermission) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
		return true;
	}

	function preProcess(Vtiger_Request $request, $display=true) {
		parent::preProcess($request, false);
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}

		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$summaryInfo = array();

		// Take first block information as summary information
		$stucturedValues = $recordStrucure->getStructure();
		foreach($stucturedValues as $blockLabel=>$fieldList) {
			$summaryInfo[$blockLabel] = $fieldList;
			break;
		}

		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);

		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
		$navigationInfo = null;//ListViewSession::getListViewNavigation($recordId);  // 20150611 young 这里造成详细页面错误，暂时为null

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
	//	$viewer->assign('NAVIGATION', $navigationInfo);
		//Intially make the prev and next records as null
		$prevRecordId = null;
		$nextRecordId = null;
		$found = false;
		if ($navigationInfo) {
			foreach($navigationInfo as $page=>$pageInfo) {
				foreach($pageInfo as $index=>$record) {
					//If record found then next record in the interation
					//will be next record
					if($found) {
						$nextRecordId = $record;
						break;
					}
					if($record == $recordId) {
						$found = true;
					}
					//If record not found then we are assiging previousRecordId
					//assuming next record will get matched
					if(!$found) {
						$prevRecordId = $record;
					}
				}
				//if record is found and next record is not calculated we need to perform iteration
				if($found && !empty($nextRecordId)) {
					break;
				}
			}
		}

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		if(!empty($prevRecordId)) {
			$viewer->assign('PREVIOUS_RECORD_URL', $moduleModel->getDetailViewUrl($prevRecordId));
		}
		if(!empty($nextRecordId)) {
			$viewer->assign('NEXT_RECORD_URL', $moduleModel->getDetailViewUrl($nextRecordId));
		}

		$viewer->assign('MODULE_MODEL', $this->record->getModule());
		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

		$viewer->assign('IS_EDITABLE', $this->record->getRecord()->isEditable($moduleName));
		$viewer->assign('IS_DELETABLE', $this->record->getRecord()->isDeletable($moduleName));

		$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
		$linkModels = $this->record->getSideBarLinks($linkParams);
		$viewer->assign('QUICK_LINKS', $linkModels);

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$viewer->assign('DEFAULT_RECORD_VIEW', $currentUserModel->get('default_record_view'));
		
		if($display) {
			$this->preProcessDisplay($request);
		}

	}

	/**
	 * 生成工作流
	 * 2014-12-26 young.yang 从工单模块迁移过来，作为通用
	 * @param Vtiger_Request $request
	 */
	function getWorkflows(Vtiger_Request $request){
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
				if($current_user->is_admin=='on' || isset($allStagers[$val['salesorderworkflowstagesid']])){
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
				}
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
		$viewer->assign('ISROLE',$isrole);   //是否有权限审核
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
	/*function editFields(Vtiger_Request $request){
		$recordModel = Vtiger_Record_Model::getInstanceById(1685,'SalesOrder');
		
		//$recordModel = $this->record;
		
		
		$moduleModel = $recordModel->getModule();
		$fieldList = $moduleModel->getFields();
		$requestFieldList = array_intersect_key($request->getAll(), $fieldList);
		
		foreach($requestFieldList as $fieldName=>$fieldValue){
			$fieldModel = $fieldList[$fieldName];
			
			if($fieldModel->isEditable() || $specialField) {
				$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
			}
		}
		$aaa=array('subject');
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
		$structuredValues = $recordStrucure->getEdit($aaa);
		
		
		
		
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE','SalesOrder');
		$viewer->assign('RECORD_ID',1685);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		echo  $viewer->view('ceshi.tpl', 'Vtiger',true);
	}*/
	function getWorkflowsM(Vtiger_Request $request){
		$moduleName = $request->getModule();
		$viewer = $this->getViewer($request);
		$viewer->assign('ModuleName',$moduleName); //工作流stagesid
		return $viewer->view('LineItemsWorkflowsM.tpl', 'Vtiger',true);
	}
	
	function preProcessTplName(Vtiger_Request $request) {
		return 'DetailViewPreProcess.tpl';
	}

	
	
	function process(Vtiger_Request $request) {
		$mode = $request->getMode();	
		//根据关联参数执行
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		//非关联信息显示
		if ($currentUserModel->get('default_record_view') === 'Summary') {
			echo $this->showModuleBasicView($request);
		} else {
			echo $this->showModuleDetailView($request);
		}	
	}

	public function postProcess(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

		$selectedTabLabel = $request->get('tab_label');

		if(empty($selectedTabLabel)) {
            if($currentUserModel->get('default_record_view') === 'Detail') {
                $selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
            } else{
                if($moduleModel->isSummaryViewSupported()) {
                    $selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_SUMMARY', $moduleName);
                } else {
                    $selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
                }
            } 
        }

		$viewer = $this->getViewer($request);

		$viewer->assign('SELECTED_TAB_LABEL', $selectedTabLabel);
		$viewer->assign('MODULE_MODEL', $this->record->getModule());
		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

		$viewer->view('DetailViewPostProcess.tpl', $moduleName);

		parent::postProcess($request);
	}


	public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			'modules.Vtiger.resources.Detail',
			"modules.$moduleName.resources.Detail",
			'modules.Vtiger.resources.RelatedList',
			"modules.$moduleName.resources.RelatedList",
			'libraries.jquery.jquery_windowmsg',
			"modules.Emails.resources.MassEdit",
			"modules.Vtiger.resources.CkEditor"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	function showDetailViewByMode($request) {
		$requestMode = $request->get('requestMode');
		if($requestMode == 'full') {
			return $this->showModuleDetailView($request);
		}
		return $this->showModuleBasicView($request);
	}

	/**
	 * Function shows the entire detail for the record
	 * @param Vtiger_Request $request
	 * @return <type>
     * 显示详细信息，两个地方都会显示
	 */
	function showModuleDetailView(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		//young.yang 2014-12-26 工作流
		global $isallow;
		if(in_array($moduleName, $isallow)){
			echo $this->getWorkflowsM($request);
		}
		//end	
		if(!$this->record){
		$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		//var_dump($recordStrucure);die;
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
	
	function showModuleSummaryView($request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);

        $moduleModel = $recordModel->getModule();
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
		$viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));

		return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
	}

	/**
	 * Function shows basic detail for the record
	 * @param <type> $request
	 */
	function showModuleBasicView($request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		
		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));

		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('MODULE_NAME', $moduleName);

		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();

        $moduleModel = $recordModel->getModule();
        
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());

        // 销售目标详情
        $record = $recordId;
        $sql = "select * from vtiger_depasalestargetdetail where salestargetid=? ORDER BY weeknum";
		$db=PearDatabase::getInstance();
		$sel_result = $db->pquery($sql, array($record));
		$res_cnt = $db->num_rows($sel_result);

		if ($res_cnt > 0) {
			$allData = array();
			$i = 1;
			while($rawData=$db->fetch_array($sel_result)) {
				if ($rawData['weekinvitationtarget'] > 0 || $rawData['weekvisittarget'] > 0 
					&& $rawData['weekachievementtargt'] || 0) {
					$rawData['show'] = '1';
				}
				$rawData['weekNum'] = $i;
	            $allData['week'. $i] = $rawData;
	            $i ++;
	        }
	        $dateArr = $allData;
	        $viewer->assign('DATE_ARR', $dateArr);
		}

		echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
	}

	/**
	 * Function returns recent changes made on the record
	 * @param Vtiger_Request $request
	 */
	function showRecentActivities (Vtiger_Request $request) {
		$parentRecordId = $request->get('record');
		$pageNumber = $request->get('page');
		$limit = $request->get('limit');
		$moduleName = $request->getModule();

		if(empty($pageNumber) || $pageNumber=='undefined') {
			$pageNumber = 1;
		}

		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $pageNumber);
		if(!empty($limit)) {
			$pagingModel->set('limit', $limit);
		}

		$recentActivities = ModTracker_Record_Model::getUpdates($parentRecordId, $pagingModel);
		$pagingModel->calculatePageRange($recentActivities);

		$viewer = $this->getViewer($request);
		$viewer->assign('RECENT_ACTIVITIES', $recentActivities);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('PAGING_MODEL', $pagingModel);

		echo $viewer->view('RecentActivities.tpl', $moduleName, 'true');
	}

	/**
	 * 跟进信息
	 */
	function showRecentComments(Vtiger_Request $request) {
		$parentId = $request->get('record');
		$pageNumber =(int)$request->get('page');
		$limit = $request->get('limit');
		$moduleName = $request->getModule();
		if(empty($pageNumber)){
			$pageNumber = 1;
		}
	
		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $pageNumber);
		if(!empty($limit)) {
			$pagingModel->set('limit', $limit);
		}

		$recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel,$moduleName);
		
		$pagingModel->calculatePageRange($recentComments);
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		//获取客户id
		$accountid="";
		if ($moduleName !="Accounts"){
			if(!$this->record){
				$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $parentId);
			}
			$recordModel = $this->record->getRecord();
			$accountid=$recordModel->get('related_to');
		}else{
			$accountid=$parentId;
		}
		
		$viewer = $this->getViewer($request);
		$viewer->assign('COMMENTS', $recentComments);
		$viewer->assign('ACCOUNTID', $accountid);
		$viewer->assign('COMMENTSMODE', ModComments_Record_Model::getModcommentmode());
		$viewer->assign('COMMENTSTYPE',ModComments_Record_Model::getModcommenttype());
		$viewer->assign('MODCOMMENTCONTACTS',ModComments_Record_Model::getModcommentContacts($accountid));
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('PAGING_MODEL', $pagingModel);
		
		return $viewer->view('RecentComments.tpl', $moduleName, 'true');
	}

	/**
	 * 加载关联模块下RelatedList
	 * 关联记录 修改优先级
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showRelatedList(Vtiger_Request $request) {
	    $moduleName = $request->getModule();
		$relatedModuleName = $request->get('relatedModule');
		//产品套餐特殊处理
		if($relatedModuleName=='Products' && $moduleName=='Products'){
			$relatedModuleName='ProductBundles';
		}
		$instance=CRMEntity::getInstance($moduleName);
		if(!empty($instance->relatedmodule_list) && in_array($relatedModuleName,$instance->relatedmodule_list) && isPermitted($relatedModuleName,'DetailView')=='yes'){
			$targetControllerClass = null;
		// Added to support related list view from the related module, rather than the base module.
			try {
				$targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'RelatedList', $moduleName);	
			}catch(AppException $e) {
				try {
					// If any module wants to have same view for all the relation, then invoke this.
					echo $targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'In'.$moduleName.'Relation', $relatedModuleName);
				}catch(AppException $e) {
					// Default related list
					$targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'InRelation', $relatedModuleName);
					//$targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'RelatedList', $moduleName);
				}
			}
			if($targetControllerClass) {
				$targetController = new $targetControllerClass(); //Vtiger_RelatedList_View
				return $targetController->process($request);
			}
		}else{	
			die('error related');	
		}
		
	}

	/**
	 * Function sends the child comments for a comment
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showChildComments(Vtiger_Request $request) {
		$parentCommentId = $request->get('commentid');
		$parentCommentModel = ModComments_Record_Model::getInstanceById($parentCommentId);
		$childComments = $parentCommentModel->getChildComments();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$viewer = $this->getViewer($request);
		$viewer->assign('PARENT_COMMENTS', $childComments);
		$viewer->assign('CURRENTUSER', $currentUserModel);

		return $viewer->view('CommentsList.tpl', $moduleName, 'true');
	}

	/**
	 * Function sends all the comments for a parent(Accounts, Contacts etc)
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showAllComments(Vtiger_Request $request) {
		$parentRecordId = $request->get('record');
		$commentRecordId = $request->get('commentid');
		$moduleName = $request->getModule();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$parentCommentModels = ModComments_Record_Model::getAllParentComments($parentRecordId);
		
		if(!empty($commentRecordId)) {
			//$currentCommentModel = ModComments_Record_Model::getInstanceById($commentRecordId);
		}
		
		//获取客户id
		$accountid="";
		if ($moduleName !="Accounts"){
			if(!$this->record){
				$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $parentRecordId);
			}
			$recordModel = $this->record->getRecord();
			$accountid=$recordModel->get('related_to');
		}else{
			$accountid=$parentRecordId;
		}
		
		
		$viewer = $this->getViewer($request);
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('ACCOUNTID', $accountid);
		$viewer->assign('PARENT_COMMENTS', $parentCommentModels);
		$viewer->assign('CURRENT_COMMENT', $currentCommentModel);
		$viewer->assign('COMMENTSMODE', ModComments_Record_Model::getModcommentmode());
		$viewer->assign('COMMENTSTYPE',ModComments_Record_Model::getModcommenttype());
		$viewer->assign('MODCOMMENTCONTACTS',ModComments_Record_Model::getModcommentContacts($accountid));
		
		return $viewer->view('ShowAllComments.tpl', $moduleName, 'true');
	}
	/**
	 * Function to get Ajax is enabled or not
	 * @param Vtiger_Record_Model record model
	 * @return <boolean> true/false
	 */
	function isAjaxEnabled($recordModel) {
		return $recordModel->isEditable();
	}

	/**
	 * Function to get activities
	 * @param Vtiger_Request $request
	 * @return <List of activity models>
	 */
	public function getActivities(Vtiger_Request $request) {
		return '';
	}

	
	/**
	 * Function returns related records based on related moduleName
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showRelatedRecords(Vtiger_Request $request) {
		$parentId = $request->get('record');
		$pageNumber = $request->get('page');
		$limit = $request->get('limit');
		$relatedModuleName = $request->get('relatedModule');
		$moduleName = $request->getModule();

		if(empty($pageNumber) || $pageNumber=='undefined') {
			$pageNumber = 1;
		}

		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $pageNumber);
		if(!empty($limit)) {
			$pagingModel->set('limit', $limit);
		}

		$parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
		$relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName);
		$models = $relationListView->getEntries($pagingModel);
		$header = $relationListView->getHeaders();

		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE' , $moduleName);
		$viewer->assign('RELATED_RECORDS' , $models);
		$viewer->assign('RELATED_HEADERS', $header);
		$viewer->assign('RELATED_MODULE' , $relatedModuleName);
		$viewer->assign('PAGING_MODEL', $pagingModel);

		return $viewer->view('SummaryWidgets.tpl', $moduleName, 'true');
	}
	public function getWorkflowsContent(Vtiger_Request $request){
		return '';
	}
	public function getProducts(Vtiger_Request $request){
		return '';
	}
	public function getProductById(Vtiger_Request $request){
		return '';
	}
	public function getProductBySalesorderid(Vtiger_Request $request){
		return '';
	}
}
