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
 * Vtiger ListView Model Class
 */
include_once 'include/QueryGenerator/KQueryGenerator.php';
class Vtiger_ListView_Model extends Vtiger_Base_Model {
    public $isAllCount=0;
    public $isFromMobile=0;


	/**
	 * Function to get the Module Model
	 * @return Vtiger_Module_Model instance
	 */
	public function getModule() {
		return $this->get('module');
	}

	/**
	 * Function to get the Quick Links for the List view of the module
	 * @param <Array> $linkParams
	 * @return <Array> List of Vtiger_Link_Model instances
	 */
	public function getSideBarLinks($linkParams) {

		$linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
		$moduleLinks = $this->getModule()->getSideBarLinks($linkParams);

		$listLinkTypes = array('LISTVIEWSIDEBARLINK', 'LISTVIEWSIDEBARWIDGET');
		$listLinks = Vtiger_Link_Model::getAllByType($this->getModule()->getId(), $listLinkTypes);

		if($listLinks['LISTVIEWSIDEBARLINK']) {
			foreach($listLinks['LISTVIEWSIDEBARLINK'] as $link) {
				$moduleLinks['SIDEBARLINK'][] = $link;
			}
		}

		if($listLinks['LISTVIEWSIDEBARWIDGET']) {
			foreach($listLinks['LISTVIEWSIDEBARWIDGET'] as $link) {
				$moduleLinks['SIDEBARWIDGET'][] = $link;
			}
		}

		return $moduleLinks;
	}

	/**
	 * 模块列表页面显示链接 保留新增 Edit By Joe @20150511
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
	 */
	public function getListViewLinks($linkParams) {
		$basicLinks = array();
		$links=array();
		$moduleModel = $this->getModule();
		$createPermission = isPermitted($moduleModel->getName(), 'EditView');
		if($createPermission=='yes') {
			$basicLinks[] = array(
					'linktype' => 'LISTVIEWBASIC',
					'linklabel' => 'LBL_ADD_RECORD',
					'linkurl' => $moduleModel->getCreateRecordUrl(),
					'linkicon' => ''
			);
			foreach($basicLinks as $basicLink) {
				$links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
			}
		}

		return $links;
		exit;

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$moduleModel = $this->getModule();

		$linkTypes = array('LISTVIEWBASIC', 'LISTVIEW', 'LISTVIEWSETTING');
		$links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);

		$basicLinks = $this->getBasicLinks();

		foreach($basicLinks as $basicLink) {
			$links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
		}

		$advancedLinks = $this->getAdvancedLinks();

		foreach($advancedLinks as $advancedLink) {
			$links['LISTVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($advancedLink);
		}

		if($currentUserModel->isAdminUser()) {

			$settingsLinks = $this->getSettingLinks();
			foreach($settingsLinks as $settingsLink) {
				$links['LISTVIEWSETTING'][] = Vtiger_Link_Model::getInstanceFromValues($settingsLink);
			}
		}

		return $links;
	}

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 */
	public function getListViewMassActions($linkParams) {
		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleModel = $this->getModule();

		$linkTypes = array('LISTVIEWMASSACTION');
		$links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);

		$massActionLinks = array();
		if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
			$massActionLinks[] = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_EDIT',
				'linkurl' => 'javascript:Vtiger_List_Js.triggerMassEdit("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");',
				'linkicon' => ''
			);
		}
		if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
			$massActionLinks[] = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_DELETE',
				'linkurl' => 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");',
				'linkicon' => ''
			);
		}
		//隐藏评论 by Joe at 20150116
		/*if($moduleModel->isCommentEnabled()) {
			$massActionLinks[] = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_ADD_COMMENT',
				'linkurl' => 'index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showAddCommentForm',
				'linkicon' => ''
			);
		}*/

		foreach($massActionLinks as $massActionLink) {
			$links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}

		return $links;
	}

	/**
	 * Function to get the list view header
	 * @return <Array> - List of Vtiger_Field_Model instances
	 */
	public function getListViewHeaders() {
        $listViewContoller = $this->get('listview_controller');
        $module = $this->getModule();
		$headerFieldModels = array();
		$headerFields = $listViewContoller->getListViewHeaderFields();

		foreach($headerFields as $fieldName => $webserviceField) {

			if($webserviceField && !in_array($webserviceField->getPresence(), array(0,2))) continue;
			$headerFieldModels[$fieldName] = Vtiger_Field_Model::getInstance($fieldName,$module);
		}

		return $headerFieldModels;
	}

	/**
	 * Function to get the list view entries
	 * @param Vtiger_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();

		$moduleName = $this->getModule()->get('name');


		$moduleFocus = CRMEntity::getInstance($moduleName);   //重点，读取每个栏目下的module文件
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);	//生成module实体，包括id，名称等信息

		$queryGenerator = $this->get('query_generator');
		$listViewContoller = $this->get('listview_controller');

		$searchKey = $this->get('search_key');
		$searchValue = $this->get('search_value');
		$operator = $this->get('operator');

		if(!empty($searchKey)) {
                    $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
		}

                $orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
			$orderBy = 'modifiedtime';
			$sortOrder = 'DESC';
		}

        if(!empty($orderBy)){
            $columnFieldMapping = $moduleModel->getColumnFieldMapping();//array

            $orderByFieldName = $columnFieldMapping[$orderBy];

            $orderByFieldModel = $moduleModel->getField($orderByFieldName);
            if($orderByFieldModel && $orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE){
                //IF it is reference add it in the where fields so that from clause will be having join of the table
                $queryGenerator = $this->get('query_generator');
                $queryGenerator->addWhereField($orderByFieldName);
                //$queryGenerator->whereFields[] = $orderByFieldName;
            }

        }
		$listQuery = $this->getQuery();

		$sourceModule = $this->get('src_module');
		if(!empty($sourceModule)) {
			if(method_exists($moduleModel, 'getQueryByModuleField')) {
				$overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery);
				if(!empty($overrideQuery)) {
					$listQuery = $overrideQuery;
				}
			}
		}


		global $current_user;


		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		if(!empty($orderBy)) {
            if($orderByFieldModel && $orderByFieldModel->isReferenceField()){
                $referenceModules = $orderByFieldModel->getReferenceList();
                $referenceNameFieldOrderBy = array();
                foreach($referenceModules as $referenceModuleName) {
                    $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModuleName);
                    $referenceNameFields = $referenceModuleModel->getNameFields();
                    $columnList = array();
                    foreach($referenceNameFields as $nameField) {
                        $fieldModel = $referenceModuleModel->getField($nameField);
                        $columnList[] = $fieldModel->get('table').$orderByFieldModel->getName().'.'.$fieldModel->get('column');
                    }
                    if(count($columnList) > 1) {
                        $referenceNameFieldOrderBy[] = getSqlForNameInDisplayFormat(array('first_name'=>$columnList[0],'last_name'=>$columnList[1]),'Users').' '.$sortOrder;
                    } else {
                        $referenceNameFieldOrderBy[] = implode('', $columnList).' '.$sortOrder ;
                    }
                }
                $listQuery .= ' ORDER BY '. implode(',',$referenceNameFieldOrderBy);
            }else{
                $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
            }
		}

		$viewid = ListViewSession::getCurrentView($moduleName);

		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

		$listQuery .= " LIMIT $startIndex,".($pageLimit+1);
		//echo $listQuery;die();
		$listResult = $db->pquery($listQuery, array());
		$listViewRecordModels = array();
		$listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus,$moduleName, $listResult);//获取视图的数据记录,这里已经获取到了数据

		$pagingModel->calculatePageRange($listViewEntries);

		if($db->num_rows($listResult) > $pageLimit){
			array_pop($listViewEntries);
			$pagingModel->set('nextPageExists', true);
		}else{
			$pagingModel->set('nextPageExists', false);
		}
		//3.在进行一次转化，目的何在
		$index = 0;
		foreach($listViewEntries as $recordId => $record) {
			$rawData = $db->query_result_rowdata($listResult, $index++);
			$record['id'] = $recordId;
			$listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
		}



		return $listViewRecordModels;
	}

	/**
	 * 获取记录行数[先从session获取]
	 * @param Vtiger_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */
	public function getListViewCount() {
        if(0==$this->isAllCount && 0==$this->isFromMobile){
            return 0;
        }
		$db = PearDatabase::getInstance();
		global $currentModule;

		$listQuery=$_SESSION[$currentModule.'_listquery'];
		if(empty($listQuery)){



		$queryGenerator = $this->get('query_generator');

        $searchKey = $this->get('search_key');
		$searchValue = $this->get('search_value');
		$operator = $this->get('operator');
		if(!empty($searchKey)) {
			$queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
		}

		$listQuery = $this->getQuery();


		$sourceModule = $this->get('src_module');
		if(!empty($sourceModule)) {
			$moduleModel = $this->getModule();
			if(method_exists($moduleModel, 'getQueryByModuleField')) {
				$overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery);
				if(!empty($overrideQuery)) {
					$listQuery = $overrideQuery;
				}
			}
		}
		}
		$position = stripos($listQuery, ' from ');
		if ($position) {
			$split = spliti(' from ', $listQuery);
			$splitCount = count($split);
			$listQuery = 'SELECT count(*) AS count '.' FROM ' .$split[$splitCount-1];
			//for ($i=1; $i<$splitCount; $i++) {
				//$listQuery = $listQuery. ' FROM ' .$split[$i];
			//}
		}



		if($this->getModule()->get('name') == 'Calendar'){
			$listQuery .= ' AND activitytype <> "Emails"';
		}

		$listResult = $db->pquery($listQuery, array());
		return $db->query_result($listResult, 0, 'count');
	}

	function getQuery() {
		$queryGenerator = $this->get('query_generator');
		$listQuery = $queryGenerator->getQuery();
		//echo $listQuery;
		return $listQuery;
	}
	/**
	 * Static Function to get the Instance of Vtiger ListView model for a given module and custom view
	 * @param <String> $moduleName - Module Name
	 * @param <Number> $viewId - Custom View Id
	 * @return Vtiger_ListView_Model instance
	 */
	public static function getInstance($moduleName, $viewId='0') {
		$db = PearDatabase::getInstance();
		$currentUser = vglobal('current_user');


     if(in_array($moduleName,array('Formdesign','TelStatistics','Rsalesanalysis','RPerformanceranking','Rsalesananalysis','RSalescomparison','StaffPositions','RVisitingorderTransaction','Businesstrend','BusinessOpportunities','BusinessOCRate','BusinessAnalysis','BusinessSysAnalysis','BusinessCycleAnalysis','VCommentAnalysis','TyunReportanalysis','IncomeCostAnalysis','RSalesorderBackTop','BusinessOppMonth','RIndustrysalesanalysis','Accounts','OvertAccounts','ContractsProducts','Workflows','ServiceContracts','QiaoServiceContracts','ServiceComments','ServiceMaintenance','ServiceComplaints','DisposeComplaints','DisposeMaintenance','SalesOrder','WorkFlowCheck','Knowledge', 'ReceivedPayments','ServiceAssignSet','ServiceAssignRule','ServiceProducts','Potentials','VisitingOrder','WorkSummarize','Products','AccountsMerge','SaleManager','Quotes','SalesorderProductsrel','Project','Invoice','Achievementallot','JobAlerts','Receivedpaymentstatistics','Smallbusiness','IdcRecords','AutoTask','Leads','IronAccount','Sendmailer','Reporting','Billing','MaintainerAccount','Vacate','Vacatedetail','Guarantee','OrderChargeback','Staypayment','ServiceReturnplan','ServiceReturnList','ServiceContractsPrint','ServiceContractsRule','ActivationCode','ActivationCodeDetail','Vendors','SContractNoGeneration','CompayCode','Recharge','RefillApplication','RechargeDetail','SupplierContracts', 'Salestarget', 'Staffcapacity','SalesDaily','Salestargetdetail','ReceivedPaymentsThrow','DepaSalestarget','DepaSalestargetdetail', 'Visitsign','ExtensionTrial','PurchaseInvoice','Compensation', 'ReceivedPaymentsNotes','Suppcontractsextension','School','Schoolcontacts','Schoolcomments', 'Schoolrecruit', 'Schoolresume', 'Schoolqualified','Schoolqualifiedpeople','Schoolassessment','Schoolassessmentpeople','Schooladopt','Schooladoptpeople','Schoolpractical','Schoolpracticalpeople','Schoolemploy','Schoolinterview','Schooleligibility','Schoolinterqua','Approval','Newinvoice', 'Schoolemploypeople','RechargePlatform','Scoreobject','Scoremodel','Scorevendor','Medium','Channels','ShareAccount','VisitAccountContract','VisitImprovement','VisitDepartment','Schoolvisit','OrganizationChart','ContractTemplate','ContractsAgreement','TyunUpgradeRule','ContractTplSet','GroupBuyAccount','AccountFollowUp','ContractActivaCode','SuppContractsAgreement','ShareVendors','AccountPlatform','VendorContacts','ProductProvider','ContractGuarantee','VendorProduct','RefundTimeoutAudit','Personneluser','SeparateInto','ActivationCodeSupp','ApplyIncrement','TyunWebBuyService','UserManger','IndicatorSetting','AchievementallotStatistic', 'AchievementSummary','ClosingDate', 'ContractExecution', 'AccountReceivable','ReceivableOverdue','ContractReceivable','AchievementSummaryManager','EmployeeAbility','DataTransfer','PreInvoiceDeferral','InputInvoice','Files','CustomerStatement', 'SupplierStatement','Authentication','Vmatefiles','SearchMatch','DelayMatch','ApanageManagement','ContractDelaySign','PayApply','Item','Receipt','ReceivedPaymentsClassify','ReceivedPaymentsCollate'))) {


            $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'ListView', $moduleName);

            $instance = new $modelClassName();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//对象实例化放入缓存中

            $entityInstance = CRMEntity::getInstance($moduleName);
            $queryGenerator = new KQueryGenerator($currentUser,$entityInstance,$moduleModel);//查询条件，
            return $instance->set('module', $moduleModel)->set('query_generator', $queryGenerator);
        }else{
            $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'ListView', $moduleName);

            $instance = new $modelClassName();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//对象实例化放入缓存中


            $queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);//查询条件，

            //$customView = new CustomView();  // 当前的检索条件的设置
            vglobal($moduleName.'_viewid',$viewId);
            if (!empty($viewId) && $viewId != "0") {
                //$queryGenerator->initForCustomViewById($viewId);//初始化查询条件，主要包括读取列表
                $queryGenerator->initForCustomViewById($moduleName);
                //Used to set the viewid into the session which will be used to load the same filter when you refresh the page
                //$viewId = $customView->getViewId($moduleName);//你吗de

            } else {
                //$viewId = $customView->getViewId($moduleName);
                //在管理页面，因为没有viewid，所以都要到这里执行
                if(!empty($viewId) && $viewId != 0) {
                    //echo $viewId;
                    $queryGenerator->initForCustomViewById($moduleName);
                } else {
                    $queryGenerator->initForCustomViewById($moduleName);
                    $entityInstance = CRMEntity::getInstance($moduleName);//为啥这里读取的是module/xxx.php 下的list_fieds_name，在customview是读取的vtiger_cvcolumnlist的数据
                    //young 这里开启智慧影响了，列表数据的读取
                   /*$listFields = $entityInstance->list_fields_name;
                    $listFields[] = 'id';
                    $queryGenerator->setFields($listFields);*/
                }
            }
            $controller = new ListViewController($db, $currentUser, $queryGenerator);




            return $instance->set('module', $moduleModel)->set('query_generator', $queryGenerator)->set('listview_controller', $controller);
        }
	}

    /*
	 * Static Function to get the Instance of Vtiger ListView model for a given module and custom view
	 * @param <String> $value - Module Name
	 * @param <Number> $viewId - Custom View Id
	 * @return Vtiger_ListView_Model instance
	 */
	public static function getInstanceForPopup($value) {
		$db = PearDatabase::getInstance();
		$currentUser = vglobal('current_user');

        if(in_array($value,array('Formdesign','TelStatistics','Accounts','OvertAccounts','ContractsProducts','Workflows','ServiceContracts','QiaoServiceContracts','ServiceComments','ServiceMaintenance','ServiceComplaints','DisposeComplaints','DisposeMaintenance','SalesOrder','WorkFlowCheck','Knowledge', 'ReceivedPayments','ServiceAssignSet','ServiceAssignRule','ServiceProducts','Potentials','VisitingOrder','WorkSummarize','Products','AccountsMerge','SaleManager','Quotes','SalesorderProductsrel','Project','Invoice','JobAlerts','IdcRecords','AutoTask','Leads','IronAccount','Sendmailer','Reporting','Billing','MaintainerAccount','Vacate','Vacatedetail','Guarantee','OrderChargeback','Staypayment','ServiceReturnplan','ServiceReturnList','ServiceContractsRule','ActivationCode','ActivationCodeDetail','Vendors','SContractNoGeneration','CompayCode','Recharge','RefillApplication','RechargeDetail','SupplierContracts', 'Salestarget', 'Staffcapacity','SalesDaily','Salestargetdetail','ReceivedPaymentsThrow','DepaSalestarget','DepaSalestargetdetail','Visitsign','ExtensionTrial','PurchaseInvoice','Compensation', 'ReceivedPaymentsNotes','Suppcontractsextension','School','Schoolcontacts','Schoolcomments', 'Schoolrecruit', 'Schoolresume','Schoolqualified','Schoolqualifiedpeople','Schoolassessment','Schoolassessmentpeople','Schooladopt','Schooladoptpeople','Schoolpractical','Schoolpracticalpeople','Schoolemploy','Schoolinterview','Schooleligibility','Schoolinterqua','Approval','Newinvoice', 'Schoolemploypeople','RechargePlatform','Scoreobject','Scoremodel','Scorevendor','Medium','Channels','ShareAccount','VisitAccountContract','VisitImprovement','VisitDepartment','Schoolvisit','OrganizationChart','ContractTemplate','ContractsAgreement','TyunUpgradeRule','ContractTplSet','GroupBuyAccount','AccountFollowUp','ContractActivaCode','SuppContractsAgreement','ShareVendors','AccountPlatform','VendorContacts','ProductProvider','ContractGuarantee','VendorProduct','RefundTimeoutAudit','Personneluser','SeparateInto','ActivationCodeSupp','ApplyIncrement','TyunWebBuyService','UserManger', 'IndicatorSetting','AchievementallotStatistic', 'AchievementSummary', 'ContractExecution', 'AccountReceivable','ReceivableOverdue','ContractReceivable','AchievementSummaryManager','EmployeeAbility','DataTransfer','InputInvoice','Files','CustomerStatement', 'SupplierStatement','Authentication','Vmatefiles','SearchMatch','DelayMatch','ApanageManagement','ContractDelaySign','PayApply','Item','Receipt','ReceivedPaymentsClassify','ReceivedPaymentsCollate'))) {


            $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'ListView', $value);
            $instance = new $modelClassName();
            $moduleModel = Vtiger_Module_Model::getInstance($value);
            $entityInstance = CRMEntity::getInstance($value);
            $queryGenerator = new KQueryGenerator($currentUser,$entityInstance,$moduleModel);//查询条件，

            $listFields = $moduleModel->getPopupFields();
            $listFields[] = 'id';
            $queryGenerator->setFields($listFields);

            $controller = new ListViewController($db, $currentUser, $queryGenerator);

            return $instance->set('module', $moduleModel)->set('query_generator', $queryGenerator);
        }else{
            $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'ListView', $value);
            $instance = new $modelClassName();
            $moduleModel = Vtiger_Module_Model::getInstance($value);

            $queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);

            $listFields = $moduleModel->getPopupFields();
            $listFields[] = 'id';
            $queryGenerator->setFields($listFields);

            $controller = new ListViewController($db, $currentUser, $queryGenerator);

            return $instance->set('module', $moduleModel)->set('query_generator', $queryGenerator)->set('listview_controller', $controller);
        }
	}

	/*
	 * Function to give advance links of a module
	 *	@RETURN array of advanced links
	 */
	public function getAdvancedLinks(){
		//exit;
		$moduleModel = $this->getModule();
		$createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView');

		$advancedLinks = array();
		$importPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Import');
		if($importPermission && $createPermission) {
			$advancedLinks[] = array(
							'linktype' => 'LISTVIEW',
							'linklabel' => 'LBL_IMPORT',
							'linkurl' => $moduleModel->getImportUrl(),
							'linkicon' => ''
			);
		}
		/*导出功能那个需要那个继承
		$exportPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Export');
		if($exportPermission) {
			$advancedLinks[] = array(
					'linktype' => 'LISTVIEW',
					'linklabel' => 'LBL_EXPORT',
					'linkurl' => 'javascript:Vtiger_List_Js.triggerExportAction("'.$this->getModule()->getExportUrl().'")',
					'linkicon' => ''
				);
		}
	*/
		$duplicatePermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'DuplicatesHandling');
		if($duplicatePermission) {
			$advancedLinks[] = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_FIND_DUPLICATES',
				'linkurl' => 'Javascript:Vtiger_List_Js.showDuplicateSearchForm("index.php?module='.$moduleModel->getName().
								'&view=MassActionAjax&mode=showDuplicatesSearchForm")',
				'linkicon' => ''
			);
		}

		return $advancedLinks;
	}

	/*
	 * Function to get Setting links
	 * @return array of setting links
	 */
	public function getSettingLinks() {
		return $this->getModule()->getSettingLinks();
	}

	/*
	 * Function to get Basic links
	 * @return array of Basic links
	 */
	public function getBasicLinks(){
		$basicLinks = array();
		$moduleModel = $this->getModule();
		$createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView');
		if($createPermission) {
			$basicLinks[] = array(
					'linktype' => 'LISTVIEWBASIC',
					'linklabel' => 'LBL_ADD_RECORD',
					'linkurl' => $moduleModel->getCreateRecordUrl(),
					'linkicon' => ''
			);
		}
		return $basicLinks;
	}

	public function extendPopupFields($fieldsList) {
		$moduleModel = $this->get('module');
		$queryGenerator = $this->get('query_generator');
		$listFields = $moduleModel->getPopupFields();
		$listFields[] = 'id';
		$listFields = array_merge($listFields, $fieldsList);
		$queryGenerator->setFields($listFields);
		$this->get('query_generator', $queryGenerator);
	}
    //全局搜索
    public function getSearchWhere(){

        $searchKey = $this->get('search_key');
        $queryGenerator = $this->get('query_generator');
        $queryGenerator -> addSearchWhere('');//置空
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if(!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator ,'leftkh'=>'','rightkh'=>'','andor'=>''));
        }

        $BugFreeQuery=isset($_REQUEST['BugFreeQuery'])?$_REQUEST['BugFreeQuery']:'';

        if(!empty($BugFreeQuery)){
            $BugFreeQuery=json_decode($BugFreeQuery,true);
            if(isset($BugFreeQuery['BugFreeQuery[queryRowOrder]'])){
                $SearchConditionRow=$BugFreeQuery['BugFreeQuery[queryRowOrder]'];
                $SearchConditionRow=explode(',',$SearchConditionRow);
                $counts=count($SearchConditionRow);
                if(is_array($SearchConditionRow)&&!empty($SearchConditionRow)){
                    foreach($SearchConditionRow as $key=>$val){

                        $val=str_replace('SearchConditionRow','',$val);

                        $leftkh=$BugFreeQuery['BugFreeQuery[leftParenthesesName'.$val.']'];
                        $rightkh=$BugFreeQuery['BugFreeQuery[rightParenthesesName'.$val.']'];
                        $andor=$BugFreeQuery['BugFreeQuery[andor'.$val.']'];
                        $searchKey=$BugFreeQuery['BugFreeQuery[field'.$val.']'];
                        $operator=$BugFreeQuery['BugFreeQuery[operator'.$val.']'];
                        $searchValue=$BugFreeQuery['BugFreeQuery[value'.$val.']'];
                        if($searchKey!='department'){
                            if(($key+2)==$counts){
                                $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator ,'leftkh'=>$leftkh,'rightkh'=>$rightkh,'andor'=>'',"counts"=>$counts));
                            }else{
                                $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator ,'leftkh'=>$leftkh,'rightkh'=>$rightkh,'andor'=>$andor,"counts"=>$counts));
                            }

                        }
                    }
                }
            }
        }
    }


	//自定义列表字段的操作
	public function getSelectFields($fields='',$type='get'){
		global $adb;
		global $current_user;
		$moduleModel = $this->getModule();
		if($type=='reset'){
            $adb->pquery('delete from vtiger_fieldlistview where uid=? and mouldname=?',array($current_user->id,$moduleModel->name));
            return false;
        }
		$result=$adb->pquery('select fields from vtiger_fieldlistview where uid=? and mouldname=?',array($current_user->id,$moduleModel->name));
		if($adb->num_rows($result)>0){
			if($type=='get'){
				return array_flip(explode(',',$adb->query_result($result, 0, 'fields')));
			}else{
				$adb->pquery('update vtiger_fieldlistview set fields=? where uid=? and mouldname=?',array($fields,$current_user->id,$moduleModel->name));
			}

		}else{
			if($type=='get'){
				return false;
			}else{
				$adb->pquery('insert into vtiger_fieldlistview(uid,mouldname,fields) values(?,?,?)',array($current_user->id,$moduleModel->name,$fields));
			}
		}
	}
	public function getWorkflowStagesUserid($relationid,$useridstring,$moduleName){
	    return " OR EXISTS(SELECT 1 FROM vtiger_workflowstagesuserid WHERE vtiger_workflowstagesuserid.salesorderid={$relationid} AND vtiger_workflowstagesuserid.userid{$useridstring} AND modulename='{$moduleName}')";
    }



}
