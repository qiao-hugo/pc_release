<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'include/InventoryPDFController.php';
include_once dirname(__FILE__). '/OrderChargebackPDFHeaderViewer.php';
include_once dirname(__FILE__). '/OrderChargebackPDFContentViewer.php';
class Vtiger_OrderChargebackPDFController extends Vtiger_InventoryPDFController{
	function buildHeaderModelTitle() {
		$singularModuleNameKey = 'SINGLE_'.$this->moduleName;
		$translatedSingularModuleLabel = getTranslatedString($singularModuleNameKey, $this->moduleName);
		if($translatedSingularModuleLabel == $singularModuleNameKey) {
			$translatedSingularModuleLabel = getTranslatedString($this->moduleName, $this->moduleName);
		}
		return sprintf("%s: %s", $translatedSingularModuleLabel, $this->focusColumnValue('orderchargeback_no'));
	}

	function getHeaderViewer() {
		$headerViewer = new OrderChargebackPDFHeaderViewer();
		$headerViewer->setModel($this->buildHeaderModel());
		return $headerViewer;
	}
	
	function buildHeaderModelColumnLeft() {
		$modelColumnLeft = parent::buildHeaderModelColumnLeft();
		return $modelColumnLeft;
	}
	
	function buildHeaderModelColumnCenter() {
		$subject = $this->focusColumnValue('subject');
		$customerName = $this->resolveReferenceLabel($this->focusColumnValue('accountid'), 'Accounts');
		$contactName = $this->resolveReferenceLabel($this->focusColumnValue('contact_id'), 'Contacts');
		$purchaseOrder = $this->focusColumnValue('vtiger_purchaseorder');
		$quoteName = $this->resolveReferenceLabel($this->focusColumnValue('quote_id'), 'Quotes');
		
		$subjectLabel = getTranslatedString('Subject', $this->moduleName);
        $quoteNameLabel = getTranslatedString('Quote Name', $this->moduleName);
		$customerNameLabel = getTranslatedString('Customer Name', $this->moduleName);
		$contactNameLabel = getTranslatedString('Contact Name', $this->moduleName);
		$purchaseOrderLabel = getTranslatedString('Purchase Order', $this->moduleName);

		$modelColumn1 = array(
				$subjectLabel		=>	$subject,
				$customerNameLabel	=>	$customerName,
				$contactNameLabel	=>	$contactName,
				$purchaseOrderLabel =>  $purchaseOrder,
                $quoteNameLabel => $quoteName
			);
		//return $modelColumn1;

	}

	function buildHeaderModelColumnRight() {
		$issueDateLabel = getTranslatedString('Issued Date', $this->moduleName);
		$validDateLabel = getTranslatedString('Due Date', $this->moduleName);
		$billingAddressLabel = getTranslatedString('Billing Address', $this->moduleName);
		$shippingAddressLabel = getTranslatedString('Shipping Address', $this->moduleName);


		$modelColumn2 = array(
				'dates' => array(
					$issueDateLabel  => $this->formatDate(date("Y-m-d")),
					$validDateLabel => $this->formatDate($this->focusColumnValue('duedate')),
				),
				//$billingAddressLabel  => $this->buildHeaderBillingAddress(),
				//'退款申请负责人'  => '■'.$this->resolveReferenceLabel($this->focusColumnValue('assigned_user_id'), 'Users'),
				//'申请日期' => $this->focusColumnValue('applytime')
			);
		return '';
	}

	function getWatermarkContent() {
		return $this->focusColumnValue('sostatues');
	}
    function buildHeaderBillingAddress() {
        $this->focusColumnValues(array('assigned_user_id'));
        return '';
    }
    //主题内容
    function buildContentModels() {

        $associated_products = $this->focus->column_fields;
        $contentModels=array();
        $contentModel = new Vtiger_PDF_Model();
        $accountname = $this->resolveReferenceLabel($this->focusColumnValue('accountid'), 'Accounts');
        $userName = $this->resolveReferenceLabel($this->focusColumnValue('assigned_user_id'), 'Users');
        $servicecontractsno = $this->resolveReferenceLabel($this->focusColumnValue('servicecontractsid'), 'ServiceContracts');
        //$executedcost = $this->resolveReferenceLabel($this->focusColumnValue('executedcost'), 'OrderChargeback');
        //echo $customerName;
        //exit;
        //global $ado;
        $db=PearDatabase::getInstance();
        $result=$db->pquery('SELECT executedcost FROM vtiger_orderchargeback WHERE orderchargebackid=?',array($associated_products['record_id']));
        $executedcost=$db->query_result_rowdata($result,'executedcost',0);
        $result=$db->pquery('SELECT contract_type FROM vtiger_servicecontracts WHERE servicecontractsid=?',array($associated_products['servicecontractsid']));
        $servicecontractsclass=$db->query_result_rowdata($result,'contract_type',0);
        $resultall=$db->run_query_allrecords("SELECT salesorderworkflowstagesid, workflowstagesname, isaction, IF ( isaction = 2, '已审核', IF ( isaction = 1, '审核中', '未激活' )) AS actionstatus, actiontime, IF ( ishigher = 1, (SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '<br>' ) AS last_name FROM vtiger_users WHERE vtiger_salesorderworkflowstages.higherid = vtiger_users.id ), ( SELECT ( SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '<br>' ) FROM vtiger_users WHERE id IN ( SELECT vtiger_user2role.userid FROM vtiger_user2role WHERE vtiger_user2role.roleid IN ( SELECT vtiger_role.roleid FROM vtiger_role WHERE FIND_IN_SET( vtiger_role.roleid, REPLACE ( vtiger_workflowstages.isrole, ' |##| ', ',' ))))) FROM vtiger_workflowstages WHERE vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid AND vtiger_workflowstages.isrole IN ('H102', 'H104', 'H90'))) AS higherid, IFNULL(( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_salesorderworkflowstages.auditorid = vtiger_users.id ), '--' ) AS auditorid, auditortime, createdtime, ( SELECT ( SELECT GROUP_CONCAT(rolename) FROM vtiger_role WHERE FIND_IN_SET( vtiger_role.roleid, REPLACE ( vtiger_workflowstages.isrole, ' |##| ', ',' ))) FROM vtiger_workflowstages WHERE vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid ) AS isrole, ( SELECT ( SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '<br>' ) FROM vtiger_users WHERE FIND_IN_SET( vtiger_users.id, REPLACE ( vtiger_products.productman, ' |##| ', ',' ))) FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderworkflowstages.productid ) AS productid FROM vtiger_salesorderworkflowstages WHERE salesorderid =".$associated_products['record_id']." ORDER BY vtiger_salesorderworkflowstages.sequence ASC");
        $resultremark=$db->run_query_allrecords("SELECT salesorderhistoryid,workflowerstagesid,salesorderid,reject,IFNULL(( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_users.id = rejectid), '--' ) AS rejectid,rejecttime FROM vtiger_salesorderremark WHERE modulename='OrderChargeback' AND salesorderid=".$associated_products['record_id']);
        $resultremarklist=array();
        if(!empty($resultremark)){
            foreach($resultremark as $value){
                $resultremarklist[$value['workflowerstagesid']][]=$value;
            }
        }

       /* echo "<pre>";
        print_r($associated_products);*/
            $associated_products['accountname']=$accountname;
            $associated_products['resultremarklist']=$resultremarklist;
            $associated_products['processingresult']=vtranslate($associated_products['processingresult'],'OrderChargeback');
            $associated_products['refundreason']=vtranslate($associated_products['refundreason'],'OrderChargeback');
            $associated_products['originalcontractprocessing']=vtranslate($associated_products['originalcontractprocessing'],'OrderChargeback');
            $associated_products['resultall']=$resultall;
            $associated_products['servicecontractsclass']=$servicecontractsclass[0];
            $associated_products['userName']=$userName;
            $associated_products['servicecontractsno']=$servicecontractsno;
            $associated_products['executedcost']=$executedcost[0];
            $contentModel->set('accountname', $accountname);
            $contentModel->set('all',   $associated_products);
            /*echo "<pre>";
            print_r($contentModel);*/
            $contentModels[] = $contentModel;
            //print_r($contentModels);

        return $contentModels;
    }

    function buildContentLabelModel() {
        $labelModel = new Vtiger_PDF_Model();
        $labelModel->set('servicecontractsno',	  getTranslatedString('Service Contracts Id',$this->moduleName));
        $labelModel->set('userName',	  getTranslatedString('ExecutedCost',$this->moduleName));
        $labelModel->set('accountname',	  getTranslatedString('Account Name',$this->moduleName));
        $labelModel->set('Quantity',  getTranslatedString('ProcessingResult',$this->moduleName));
        $labelModel->set('Price',     getTranslatedString('Price',$this->moduleName));
        //$labelModel->set('Comment',   getTranslatedString('Comment'),$this->moduleName);
        return $labelModel;
    }
    function Output($filename, $type) {
        if(is_null($this->focus)) return;

        $pdfgenerator = $this->getPDFGenerator();
        //$pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewer());
        //$pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, 'I');
    }
    function getContentViewer() {
        if($this->focusColumnValue('hdnTaxType') == "individual") {
            $contentViewer = new Vtiger_PDF_InventoryContentViewer();
        } else {
            //$contentViewer = new Vtiger_PDF_InventoryTaxGroupContentViewer();
            $contentViewer = new OrderChargebackPDFContentViewer();
        }
        $contentViewer->setContentModels($this->buildContentModels());
        //$contentViewer->setSummaryModel($this->buildSummaryModel());
        $contentViewer->setLabelModel($this->buildContentLabelModel());
        //$contentViewer->setWatermarkModel($this->buildWatermarkModel());
        return $contentViewer;
    }
}
?>