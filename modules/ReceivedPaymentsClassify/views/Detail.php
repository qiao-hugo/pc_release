<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *************************************************************************************/
//error_reporting(-1);
//ini_set("display_errors",1);
class ReceivedPaymentsClassify_Detail_View extends Vtiger_Detail_View {
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
		$this->exposeMethod('getdetailinfo');
        $this->exposeMethod('getReceivedPaymentsUseDetail');
        $this->exposeMethod('showChangeDetails');
	}
	function process(Vtiger_Request $request) {
	    //2015年4月21日 星期二 wangbin 增加详情页面 回款业绩分配
	    $db=PearDatabase::getInstance();
	    $recordId = $request->get('record');
	    $sql = "SELECT achievementallotid, owncompanys, receivedpaymentsid, ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_achievementallot.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownid, businessunit,scalling FROM `vtiger_achievementallot` WHERE receivedpaymentsid = ?";
	    $achievementallot = $db->pquery("$sql",array($recordId));
	    $nums = $db->num_rows($achievementallot);
	    $achievementallotdata = array();
	    if($nums > 0) {
	        for($i=0; $i<$nums; ++$i) {
	            $achievementallotdata[] = $db->query_result_rowdata($achievementallot, $i);
	        }
	    }
        $extra_data = array();
        $sql2 = "SELECT * FROM vtiger_receivedpayments_extra WHERE receivementid = ?";
        $receivepaymentextra = $db->pquery("$sql2",array($recordId));
        $count_extra = $db->num_rows($receivepaymentextra);
        $extra_data = array();
        if($count_extra>0){
            for($i=0;$i<$count_extra;$i++){
                $extra_data[] = $db->query_result_rowdata($receivepaymentextra,$i);
            }
        }
        $query="SELECT relatetoid,modulename FROM vtiger_receivedpayments WHERE receivedpaymentsid=?";
	    $dataResult=$db->pquery($query,array($recordId));
        $row = $db->query_result_rowdata($dataResult, 0);
        if($row['modulename']=='SupplierContracts'){
            $accountinfo=$db->pquery("SELECT
                                        crm.label,
                                        usr.last_name,
                                    IF(usr.`status` = 'Active','','[离职]') AS STATUS,
                                     (SELECT departmentname	FROM vtiger_departments	WHERE	departmentid = usd.departmentid)AS departmentname,
                                     service.currencytype,
                                     service.total
                                    FROM
                                        vtiger_suppliercontracts service
                                    LEFT OUTER JOIN vtiger_crmentity crm ON crm.crmid = service.vendorid
                                    LEFT OUTER JOIN vtiger_users usr ON crm.smownerid = usr.id
                                    LEFT OUTER JOIN vtiger_user2department usd ON usr.id = usd.userid
                                    WHERE
                                        service.suppliercontractsid=?",array($row['relatetoid']));
        }else{
            //$accountinfo =  $db->pquery("SELECT crm.label, usr.last_name, IF ( usr.`status` = 'Active', '', '[离职]' ) AS STATUS, ( SELECT departmentname FROM vtiger_departments WHERE departmentid = usd.departmentid ) AS departmentname, service.currencytype, service.total FROM vtiger_servicecontracts service LEFT OUTER JOIN vtiger_crmentity crm ON crm.crmid = service.sc_related_to LEFT OUTER JOIN vtiger_users usr ON crm.smownerid = usr.id LEFT OUTER JOIN vtiger_user2department usd ON usr.id = usd.userid WHERE service.servicecontractsid = ( SELECT relatetoid FROM vtiger_receivedpayments WHERE receivedpaymentsid = ?)",array($recordId));
            $accountinfo =  $db->pquery("SELECT crm.label, usr.last_name, IF ( usr.`status` = 'Active', '', '[离职]' ) AS STATUS, ( SELECT departmentname FROM vtiger_departments WHERE departmentid = usd.departmentid ) AS departmentname, service.currencytype, service.total FROM vtiger_servicecontracts service LEFT OUTER JOIN vtiger_crmentity crm ON crm.crmid = service.sc_related_to LEFT OUTER JOIN vtiger_users usr ON crm.smownerid = usr.id LEFT OUTER JOIN vtiger_user2department usd ON usr.id = usd.userid WHERE service.servicecontractsid =?",array($row['relatetoid']));
        }
        //wangibn 2015年5月13日 星期三 回款详细页面 添加客户信息

	       $row = $db->num_rows($accountinfo);
	       $lis = array();
	       if($row>0){
	           for($i=0;$i<$row;++$i){
	               $lis[] = $db->fetchByAssoc($accountinfo);
	           }
	       }
	       //var_dump($lis);die;
	       //客户:上海川仪工程技术有限公司;负责人:杨丹1[四营四部];欧元:1000
            $str = "";

	         $lis = $lis['0'];

             if (empty($lis['label'])){
                 $str .="无客户被找到;";
             }else{
                 $str .="客户:".$lis['label'].";";
             }
             if(!empty($lis['label']) && !empty($lis['last_name'])){
                 $str .="客户负责人:".$lis['last_name']."[".$lis['departmentname']."]".$lis[status].";";
             }
             if(empty($lis['currencytype'])){
                 $str .="人民币：";
             }else{
                 $str .= $lis['currencytype'].":";
             }
             if(empty($lis['total'])){
                 $str .= "0";
             }else {
                 $str .= $lis['total'];
             }

	    //end
	     $servicecontractid = ReceivedPayments_Record_Model::getReceivedPaymentsinfo($recordId);

	    $currency = ServiceContracts_Record_Model::getcurrencytype($servicecontractid);//获取当前人民币的字段类型
	    $currency=empty($currency)?"人民币":$currency;
	    $viewer = $this->getViewer($request);
	    $viewer->assign('ACHIEVEMENTALLOTDATA',$achievementallotdata);
        $viewer->assign('EXTRA_DATA',$extra_data);//额外回款
	    $viewer->assign("STR",$str);
	    $viewer->assign('CURRENCY',$currency);
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if ($currentUserModel->get('default_record_view') === 'Summary') {
		    echo $this->showModuleBasicView($request);
		} else {
			echo $this->showModuleDetailView($request);
		}

	}

	//public function postProcess(Vtiger_Request $request) {} 右侧关联重复加载的问题，竟然是在这里出现的。


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
     * 回款变更详情
     * @param Vtiger_Request $request
     */
	function showChangeDetails(Vtiger_Request $request){
	    global $adb;
        $parentRecordId = $request->get('record');
        $moduleName = $request->getModule();
        $changeDetails = $adb->run_query_allrecords("select vtiger_receivedpayments_changedetails.*,vtiger_users.last_name from vtiger_receivedpayments_changedetails left join vtiger_users on vtiger_receivedpayments_changedetails.changerid=vtiger_users.id where vtiger_receivedpayments_changedetails.receivedpaymentsid=".$parentRecordId." order by vtiger_receivedpayments_changedetails.changetime desc");
        $viewer = $this->getViewer($request);
        $viewer->assign('RECENT_ACTIVITIES', $changeDetails);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('record', $parentRecordId);
        echo $viewer->view('ChangeDetails.tpl', $moduleName, 'true');
    }

	/**
	 * Function returns latest comments
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showRecentComments(Vtiger_Request $request) {
		$parentId = $request->get('record');
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
      /*回款详细里读取相关的产品信息，以及对应回款记录
     * 2015年5月30日 wangbin
     *
     * */
	function getdetailinfo(Vtiger_Request $request){
       // var_dump($request);
	    $recorid = $request->get('record');//回款id
	    $db=PearDatabase::getInstance();
        $sql1 = 'SELECT relatetoid FROM `vtiger_receivedpayments` WHERE receivedpaymentsid = ? ';
        $sql2 = 'SELECT vtiger_salesorderproductsrel.salesorderproductsrelid, IFNULL(( SELECT SUM(alreadyprice) FROM vtiger_receivementproducts AS aa WHERE aa.serviceid = vtiger_salesorderproductsrel.servicecontractsid AND aa.productsid = vtiger_salesorderproductsrel.productid ), 0 ) AS already, IFNULL(( SELECT alreadyprice FROM vtiger_receivementproducts AS aa WHERE aa.serviceid = vtiger_salesorderproductsrel.servicecontractsid AND aa.productsid = vtiger_salesorderproductsrel.productid AND aa.receivedpaymentid = ? LIMIT 1 ), 0 ) AS alreadyprice, vtiger_products.productid, vtiger_products.productname, vtiger_salesorderproductsrel.marketprice FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_products ON vtiger_salesorderproductsrel.productid = vtiger_products.productid LEFT JOIN vtiger_receivementproducts ON vtiger_salesorderproductsrel.salesorderproductsrelid = vtiger_receivementproducts.salesorderproductsid WHERE vtiger_salesorderproductsrel.servicecontractsid = ? GROUP BY vtiger_salesorderproductsrel.productid';
        $serviceids = $db->pquery($sql1,array($recorid));

        if(!empty($serviceids->fields['0'])){
            $id = $serviceids->fields['0']; //合同id
            $productprice = $db->pquery($sql2,array($recorid,$id));
            if($db->num_rows($productprice)>0){
                for ($i=0;$i<$db->num_rows($productprice);++$i){
                    $productlis[] = $db->fetchByAssoc($productprice);
                }
            }
            $mark= 0 ;
            $alprice= 0 ;
            $alreadyprice = 0;
            foreach ($productlis as $key){
                $mark += $key['marketprice']; //市场总金额;
                $alprice +=$key['already']; //已收款总金额;
                $alreadyprice +=$key['alreadyprice']; //收款金额;
            }
            //wangbin 根据合同id 读取回款表里的回款记录，加载到回款历史块
            $sql3 = "SELECT * FROM vtiger_receivedpayments WHERE relatetoid = ?";
            $receivedresult = $db->pquery($sql3,array($id));
            if($db->num_rows($receivedresult)>0){
                for ($i=0;$i<$db->num_rows($receivedresult);++$i){
                    $receivedlis[] = $db->fetchByAssoc($receivedresult);
                }
            }
            $subtract =  sprintf("%0.2f",$mark-$alprice);
            $viewer = $this->getViewer($request);
            $viewer->assign("SUBTRACT",$subtract);
            $viewer->assign('PRICETOTAL',$alprice);
            $viewer->assign('RECEIVEDHISTORY',$receivedlis);
            $viewer->assign(PRODUCTLIS,$productlis);
            $viewer->view('detailwidgetContainer.tpl', 'ReceivedPaymentsClassify');
        }
	}

    /**
     * 获取回款使用明细 gaocl add 2018/05/22
     * @param Vtiger_Request $reques
     */
	function getReceivedPaymentsUseDetail(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ReceivedPaymentsClassify');
        $viewer = $this->getViewer($request);
        $viewer->assign('RECEIVED_PAYMENTS_USE_DETAIL',$recordModel->getReceivedPaymentsUseDetail($request));
        $viewer->view('UseDetailwidgetContainer.tpl', 'ReceivedPaymentsClassify');
        exit;
    }

}
