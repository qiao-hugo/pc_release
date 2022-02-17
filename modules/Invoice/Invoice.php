<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

// Account is used to store vtiger_account information.
class Invoice extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "vtiger_invoice";
	var $table_index= 'invoiceid';
	var $tab_name = Array('vtiger_crmentity','vtiger_invoice','vtiger_invoicebillads','vtiger_invoiceshipads','vtiger_invoicecf', 'vtiger_inventoryproductrel');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_invoice'=>'invoiceid','vtiger_invoicebillads'=>'invoicebilladdressid','vtiger_invoiceshipads'=>'invoiceshipaddressid','vtiger_invoicecf'=>'invoiceid','vtiger_inventoryproductrel'=>'id');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_invoicecf', 'invoiceid');

	var $column_fields = Array();

	var $update_product_array = Array();

	var $sortby_fields = Array('subject','invoice_no','invoicestatus','smownerid','accountname','lastname');

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
				//'Invoice No'=>Array('crmentity'=>'crmid'),
				'Invoice No'=>Array('invoice'=>'invoice_no'),
				'Subject'=>Array('invoice'=>'subject'),
				'Sales Order'=>Array('invoice'=>'salesorderid'),
				'Status'=>Array('invoice'=>'invoicestatus'),
				'Total'=>Array('invoice'=>'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid')
				);

	var $list_fields_name = Array(
				        'Invoice No'=>'invoice_no',
				        'Subject'=>'subject',
				        'Sales Order'=>'salesorder_id',
				        'Status'=>'invoicestatus',
				        'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id'
				      );
	var $list_link_field= 'subject';

	var $search_fields = Array(
				//'Invoice No'=>Array('crmentity'=>'crmid'),
				'Invoice No'=>Array('invoice'=>'invoice_no'),
				'Subject'=>Array('purchaseorder'=>'subject'),
                'Account Name'=>Array('contactdetails'=>'account_id'),
                'Created Date' => Array('crmentity'=>'createdtime'),
                'Assigned To'=>Array('crmentity'=>'smownerid'),
				);

	var $search_fields_name = Array(
				        'Invoice No'          => 'invoice_no',
				        'Subject'             => 'subject',
                        'Account Name'        => 'account_id',
                        'Created Time'        => 'createdtime',
                        'Assigned To'         => 'assigned_user_id'
				      );

	// This is the list of vtiger_fields that are required.
	var $required_fields =  array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'crmid';
	var $default_sort_order = 'ASC';

	//var $groupTable = Array('vtiger_invoicegrouprelation','invoiceid');

	var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime', 'assigned_user_id');
	var $_salesorderid;
	var $_recurring_mode;

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	var $entity_table = "vtiger_crmentity";

	// For workflows update field tasks is deleted all the lineitems.
	var $isLineItemUpdate = true;
    var $relatedmodule_list=array('Billing','Invoicesign');
    //'Potentials','Quotes'
    var $relatedmodule_fields=array(
        'Billing'=>array('businessnamesone'=>'开票对象','taxpayers_no'=>'纳税人识别税号/税号','registeraddress'=>'注册地址','depositbank'=>'开户行','telephone'=>'电话','accountnumber'=>'账号','isformtable'=>'已有加盖公章开票信息报表','modulestatus'=>'状态'),'Invoicesign'=>array('path'=>'签名'),
    );
	
	/**	Constructor which will set the column_fields in this object
	 */
	function Invoice() {
		$this->log =LoggerManager::getLogger('Invoice');
		$this->log->debug("Entering Invoice() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Invoice');
		$this->log->debug("Exiting Invoice method ...");
	}


	/** Function to handle the module specific save operations

	*/

	function save_module($module) {
		/* global $updateInventoryProductRel_deduct_stock;
		$updateInventoryProductRel_deduct_stock = true;

		//in ajax save we should not call this function, because this will delete all the existing product values
		if(isset($this->_recurring_mode) && $this->_recurring_mode == 'recurringinvoice_from_so' && isset($this->_salesorderid) && $this->_salesorderid!='') {
			// We are getting called from the RecurringInvoice cron service!
			$this->createRecurringInvoiceFromSO();

		} else if(isset($_REQUEST)) {
			if($_REQUEST['action'] != 'InvoiceAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW'
					&& $_REQUEST['action'] != 'MassEditSave' && $_REQUEST['action'] != 'ProcessDuplicates'
					&& $_REQUEST['action'] != 'SaveAjax' && $this->isLineItemUpdate != false) {
				//Based on the total Number of rows we will save the product relationship with this entity
				saveInventoryProductDetails($this, 'Invoice');
			} else if($_REQUEST['action'] == 'InvoiceAjax' || $_REQUEST['action'] == 'MassEditSave') {
				$updateInventoryProductRel_deduct_stock = false;
			}
		}
		// Update the currency id and the conversion rate for the invoice
		$update_query = "update vtiger_invoice set currency_id=?, conversion_rate=? where invoiceid=?";

		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id);
		$this->db->pquery($update_query, $update_params); */
       
//是否是多发票添加权限
    if(empty($_REQUEST['record'])||$_REQUEST['record']<1){
        $recordid=$this->id;
        $sql="UPDATE vtiger_invoice SET trialtime='".date('Y-m-d')."' WHERE invoiceid=?";
        $this->db->pquery($sql,array($recordid));
    }else{
        $sql="UPDATE vtiger_invoice SET modulestatus='a_normal' WHERE modulestatus='a_exception' AND invoiceid=?";
        $this->db->pquery($sql,array($_REQUEST['record']));
    }
        if($_REQUEST['moreinvoice']==md5(date('Y-m-d'))){
            global $current_user;
            $addtime=date('Y-m-d H:i:s');
            if($_REQUEST['record']>0){
                $recordid=$_REQUEST['record'];
                $sql="UPDATE vtiger_invoiceextend SET deleted=1,modifiedby={$current_user->id},modifiedtime='{$addtime}' WHERE vtiger_invoiceextend.invoiceid=?";
                $this->db->pquery($sql,array($recordid));
                $sql="DELETE FROM vtiger_invoicerelatedreceive WHERE vtiger_invoicerelatedreceive.invoiceid=?";
                $this->db->pquery($sql,array($recordid));
            }
            //加入发票添加关联回款
            if(!empty($_REQUEST['receivedid'])){
                $invalue='';
                foreach($_REQUEST['receivedid'] as $value){
                    $invalue.="({$value},{$recordid}),";
                }
                $invalue=rtrim($invalue,',');
                $sql="INSERT INTO vtiger_invoicerelatedreceive (receivedpaymentsid,invoiceid) VALUES {$invalue}";
                $this->db->pquery($sql,array());
            }
            //多发票的修改
            if(!empty($_REQUEST['updatei'])){
                $invoicecodeextend='invoicecodeextend=CASE invoiceextendid ';
                $invoice_noextend='invoice_noextend=CASE invoiceextendid ';
                $businessnamesextend='businessnamesextend=CASE invoiceextendid ';
                $drawerextend='drawerextend=CASE invoiceextendid ';
                $billingtimerextend='billingtimeextend=CASE invoiceextendid ';
                $commoditynameextend='commoditynameextend=CASE invoiceextendid ';
                $amountofmoneyextend='amountofmoneyextend=CASE invoiceextendid ';
                $taxrateextend='taxrateextend=CASE invoiceextendid ';
                $taxextend='taxextend=CASE invoiceextendid ';
                $totalandtaxextend='totalandtaxextend=CASE invoiceextendid ';
                $remarkextend='remarkextend=CASE invoiceextendid ';
                $deleted='deleted=CASE invoiceextendid ';
                $modifiedby='modifiedby=CASE invoiceextendid ';
                $modifiedtime='modifiedtime=CASE invoiceextendid ';
                foreach($_REQUEST['updatei'] as $value){
                    $valueid=$value-105;
                    $invoicecodeextend.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['invoicecodeextend'][$value]);
                    $invoice_noextend.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['invoice_noextend'][$value]);
                    $businessnamesextend.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['businessnamesextend'][$value]);
                    $drawerextend.=sprintf(" WHEN %d THEN %d",$valueid,$_REQUEST['drawerextend'][$value]);
                    $billingtimerextend.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['billingtimerextend'][$value]);
                    $commoditynameextend.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['commoditynameextend'][$value]);
                    $amountofmoneyextend.=sprintf(" WHEN %d THEN %0.2f",$valueid,$_REQUEST['amountofmoneyextend'][$value]);
                    $taxrateextend.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['taxrateextend'][$value]);
                    $taxextend.=sprintf(" WHEN %d THEN %0.2f",$valueid,$_REQUEST['taxextend'][$value]);
                    $totalandtaxextend.=sprintf(" WHEN %d THEN %0.2f",$valueid,$_REQUEST['totalandtaxextend'][$value]);
                    $remarkextend.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['remarkextend'][$value]);
                    $deleted.=sprintf(" WHEN %d THEN %d",$valueid,0);
                    $modifiedby.=sprintf(" WHEN %d THEN %d",$valueid,$current_user->id);
                    $modifiedtime.=sprintf(" WHEN %d THEN '%s'",$valueid,$addtime);
                }
                $sql="UPDATE vtiger_invoiceextend SET
                           {$invoicecodeextend} ELSE invoicecodeextend END,
                            {$invoice_noextend} ELSE invoice_noextend END,
                            {$businessnamesextend} ELSE businessnamesextend END,
                            {$drawerextend} ELSE drawerextend END,
                            {$billingtimerextend} ELSE billingtimeextend END,
                            {$commoditynameextend} ELSE commoditynameextend END,
                            {$amountofmoneyextend} ELSE amountofmoneyextend END,
                            {$taxrateextend} ELSE taxrateextend END,
                            {$taxextend} ELSE taxextend END,
                            {$totalandtaxextend} ELSE totalandtaxextend END,
                            {$remarkextend} ELSE remarkextend END,
                            {$deleted} ELSE deleted END,
                            {$modifiedby} ELSE modifiedby END,
                            {$modifiedtime} ELSE modifiedtime END
                            WHERE invoiceid={$recordid}";
                $this->db->pquery($sql,array());
            }
            //多发票的添加
            if(!empty($_REQUEST['inserti'])){
                $invalue='';
                foreach($_REQUEST['inserti'] as $value){
                    $invalue.="({$recordid},'{$_REQUEST['invoicecodeextend'][$value]}','{$_REQUEST['invoice_noextend'][$value]}','{$_REQUEST['businessnamesextend'][$value]}',{$_REQUEST['drawerextend'][$value]},'{$_REQUEST['billingtimerextend'][$value]}','{$_REQUEST['commoditynameextend'][$value]}',{$_REQUEST['amountofmoneyextend'][$value]},'{$_REQUEST['taxrateextend'][$value]}',{$_REQUEST['taxextend'][$value]},{$_REQUEST['totalandtaxextend'][$value]},'{$_REQUEST['remarkextend'][$value]}',{$current_user->id},'{$addtime}'),";
                }
                $invalue=rtrim($invalue,',');
                $sql="INSERT INTO vtiger_invoiceextend (`invoiceid`,`invoicecodeextend`,`invoice_noextend`,`businessnamesextend`,`drawerextend`, `billingtimeextend`,`commoditynameextend`,`amountofmoneyextend`,`taxrateextend`,`taxextend`,`totalandtaxextend`,`remarkextend`,`smcreatorid`,`createdtime`) VALUES {$invalue}";
                $this->db->pquery($sql,array());
            }
        }
	}

	/**
	 * Customizing the restore procedure.
	 */
	function restore($module, $id) {
		global $updateInventoryProductRel_deduct_stock;
		$status = getInvoiceStatus($id);
		if($status != 'Cancel') {
			$updateInventoryProductRel_deduct_stock = true;
		}
		parent::restore($module, $id);
	}

	/**
	 * Customizing the Delete procedure.
	 */
	function trash($module, $recordId) {
		$status = getInvoiceStatus($recordId);
		if($status != 'Cancel') {
			addProductsToStock($recordId);
		}
		parent::trash($module, $recordId);
	}
	
	/**	function used to get the name of the current object
	 *	@return string $this->name - name of the current object
	 */
	function get_summary_text()
	{
		global $log;
		$log->debug("Entering get_summary_text() method ...");
		$log->debug("Exiting get_summary_text method ...");
		return $this->name;
	}


	/**	function used to get the list of activities which are related to the invoice
	 *	@param int $id - invoice id
	 *	@return array - return an array which will be returned from the function GetRelatedList
	 */
	function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_activities(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/Activity.php");
		$other = new Activity();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="activity_mode">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid,
				vtiger_activity.*,vtiger_seactivityrel.crmid as parent_id,vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
				vtiger_crmentity.modifiedtime
				from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				where vtiger_seactivityrel.crmid=".$id." and activitytype='Task' and vtiger_crmentity.deleted=0
						and (vtiger_activity.status is not NULL and vtiger_activity.status != 'Completed')
						and (vtiger_activity.status is not NULL and vtiger_activity.status != 'Deferred')";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	/**	function used to get the the activity history related to the quote
	 *	@param int $id - invoice id
	 *	@return array - return an array which will be returned from the function GetHistory
	 */
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_contactdetails.lastname, vtiger_contactdetails.firstname,
				vtiger_contactdetails.contactid,vtiger_activity.*,vtiger_seactivityrel.*,
				vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime,
				vtiger_crmentity.createdtime, vtiger_crmentity.description,
				case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
				from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
                left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				where vtiger_activity.activitytype='Task'
					and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred')
					and vtiger_seactivityrel.crmid=".$id."
					and vtiger_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('Invoice',$query,$id);
	}



	/**	Function used to get the Status history of the Invoice
	 *	@param $id - invoice id
	 *	@return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	function get_invoicestatushistory($id)
	{
		global $log;
		$log->debug("Entering get_invoicestatushistory(".$id.") method ...");

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select vtiger_invoicestatushistory.*, vtiger_invoice.invoice_no from vtiger_invoicestatushistory inner join vtiger_invoice on vtiger_invoice.invoiceid = vtiger_invoicestatushistory.invoiceid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_invoice.invoiceid where vtiger_crmentity.deleted = 0 and vtiger_invoice.invoiceid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['Invoice No'];
		$header[] = $app_strings['LBL_ACCOUNT_NAME'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_INVOICE_STATUS'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Account Name , Amount are mandatory fields. So no need to do security check to these fields.
		global $current_user;

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$invoicestatus_access = (getFieldVisibilityPermission('Invoice', $current_user->id, 'invoicestatus') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('Invoice');

		$invoicestatus_array = ($invoicestatus_access != 1)? $picklistarray['invoicestatus']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($invoicestatus_access != 1)? 'Not Accessible': '-';

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			// Module Sequence Numbering
			//$entries[] = $row['invoiceid'];
			$entries[] = $row['invoice_no'];
			// END
			$entries[] = $row['accountname'];
			$entries[] = $row['total'];
			$entries[] = (in_array($row['invoicestatus'], $invoicestatus_array))? $row['invoicestatus']: $error_msg;
			$entries[] = DateTimeField::convertToUserFormat($row['lastmodified']);

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);

	 	$log->debug("Exiting get_invoicestatushistory method ...");

		return $return_data;
	}

	// Function to get column name - Overriding function of base class
	function get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype) {
		if ($columname == 'salesorderid') {
			if ($fldvalue == '') return null;
		}
		return parent::get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryPlanner){
		
		// Define the dependency matrix ahead
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('vtiger_crmentityInvoice', array('vtiger_usersInvoice', 'vtiger_groupsInvoice', 'vtiger_lastModifiedByInvoice'));		
		$matrix->setDependency('vtiger_inventoryproductrelInvoice', array('vtiger_productsInvoice', 'vtiger_serviceInvoice'));
		$matrix->setDependency('vtiger_invoice',array('vtiger_crmentityInvoice', "vtiger_currency_info$secmodule",
				'vtiger_invoicecf', 'vtiger_salesorderInvoice', 'vtiger_invoicebillads',
				'vtiger_invoiceshipads', 'vtiger_inventoryproductrelInvoice', 'vtiger_contactdetailsInvoice', 'vtiger_accountInvoice'));

		if (!$queryPlanner->requireTable('vtiger_invoice', $matrix)) {
			return '';
		}
		
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_invoice","invoiceid", $queryPlanner);
		
		if ($queryPlanner->requireTable('vtiger_crmentityInvoice', $matrix)) {
			$query .= " left join vtiger_crmentity as vtiger_crmentityInvoice on vtiger_crmentityInvoice.crmid=vtiger_invoice.invoiceid and vtiger_crmentityInvoice.deleted=0";
		}
		if ($queryPlanner->requireTable('vtiger_invoicecf')) {
			$query .= " left join vtiger_invoicecf on vtiger_invoice.invoiceid = vtiger_invoicecf.invoiceid";
		}
		if ($queryPlanner->requireTable("vtiger_currency_info$secmodule")) {
			$query .= " left join vtiger_currency_info as vtiger_currency_info$secmodule on vtiger_currency_info$secmodule.id = vtiger_invoice.currency_id";
		}
		if ($queryPlanner->requireTable('vtiger_salesorderInvoice')) {
			$query .= " left join vtiger_salesorder as vtiger_salesorderInvoice on vtiger_salesorderInvoice.salesorderid=vtiger_invoice.salesorderid";
		}
		if ($queryPlanner->requireTable('vtiger_invoicebillads')) {
			$query .= " left join vtiger_invoicebillads on vtiger_invoice.invoiceid=vtiger_invoicebillads.invoicebilladdressid";
		}
		if ($queryPlanner->requireTable('vtiger_invoiceshipads')) {
			$query .= " left join vtiger_invoiceshipads on vtiger_invoice.invoiceid=vtiger_invoiceshipads.invoiceshipaddressid";
		}
		if ($queryPlanner->requireTable('vtiger_inventoryproductrelInvoice', $matrix)) {
			$query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelInvoice on vtiger_invoice.invoiceid = vtiger_inventoryproductrelInvoice.id";
		}
		if ($queryPlanner->requireTable('vtiger_productsInvoice')) {
			$query .= " left join vtiger_products as vtiger_productsInvoice on vtiger_productsInvoice.productid = vtiger_inventoryproductrelInvoice.productid";
		}
		if ($queryPlanner->requireTable('vtiger_serviceInvoice')) {
			$query .= " left join vtiger_service as vtiger_serviceInvoice on vtiger_serviceInvoice.serviceid = vtiger_inventoryproductrelInvoice.productid";
		}
		if ($queryPlanner->requireTable('vtiger_groupsInvoice')) {
			$query .= " left join vtiger_groups as vtiger_groupsInvoice on vtiger_groupsInvoice.groupid = vtiger_crmentityInvoice.smownerid";
		}
		if ($queryPlanner->requireTable('vtiger_usersInvoice')) {
			$query .= " left join vtiger_users as vtiger_usersInvoice on vtiger_usersInvoice.id = vtiger_crmentityInvoice.smownerid";
		}
		if ($queryPlanner->requireTable('vtiger_contactdetailsInvoice')) {
			$query .= " left join vtiger_contactdetails as vtiger_contactdetailsInvoice on vtiger_invoice.contactid = vtiger_contactdetailsInvoice.contactid";
		}
		if ($queryPlanner->requireTable('vtiger_accountInvoice')) {
			$query .= " left join vtiger_account as vtiger_accountInvoice on vtiger_accountInvoice.accountid = vtiger_invoice.accountid";
		}
		if ($queryPlanner->requireTable('vtiger_lastModifiedByInvoice')) {
			$query .= " left join vtiger_users as vtiger_lastModifiedByInvoice on vtiger_lastModifiedByInvoice.id = vtiger_crmentityInvoice.modifiedby ";
		}
		
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Calendar" =>array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_invoice"=>"invoiceid"),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_invoice"=>"invoiceid"),
			"Accounts" => array("vtiger_invoice"=>array("invoiceid","accountid")),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts' || $return_module == 'Contacts') {
			$this->trash('Invoice',$id);
		} elseif($return_module=='SalesOrder') {
			$relation_query = 'UPDATE vtiger_invoice set salesorderid=? where invoiceid=?';
			$this->db->pquery($relation_query, array(null,$id));
		} else {
			$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
			$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
			$this->db->pquery($sql, $params);
		}
	}

	/*
	 * Function to get the relations of salesorder to invoice for recurring invoice procedure
	 * @param - $salesorder_id Salesorder ID
	 */
	function createRecurringInvoiceFromSO(){
		global $adb;
		$salesorder_id = $this->_salesorderid;
		$query1 = "SELECT * FROM vtiger_inventoryproductrel WHERE id=?";
		$res = $adb->pquery($query1, array($salesorder_id));
		$no_of_products = $adb->num_rows($res);
		$fieldsList = $adb->getFieldsArray($res);
		$update_stock = array();
		for($j=0; $j<$no_of_products; $j++) {
			$row = $adb->query_result_rowdata($res, $j);
			$col_value = array();
			for($k=0; $k<count($fieldsList); $k++) {
				if($fieldsList[$k]!='lineitem_id'){
					$col_value[$fieldsList[$k]] = $row[$fieldsList[$k]];
				}
			}
			if(count($col_value) > 0) {
				$col_value['id'] = $this->id;
				$columns = array_keys($col_value);
				$values = array_values($col_value);
				$query2 = "INSERT INTO vtiger_inventoryproductrel(". implode(",",$columns) .") VALUES (". generateQuestionMarks($values) .")";
				$adb->pquery($query2, array($values));
				$prod_id = $col_value['productid'];
				$qty = $col_value['quantity'];
				$update_stock[$col_value['sequence_no']] = $qty;
				updateStk($prod_id,$qty,'',array(),'Invoice');
			}
		}

		$query1 = "SELECT * FROM vtiger_inventorysubproductrel WHERE id=?";
		$res = $adb->pquery($query1, array($salesorder_id));
		$no_of_products = $adb->num_rows($res);
		$fieldsList = $adb->getFieldsArray($res);
		for($j=0; $j<$no_of_products; $j++) {
			$row = $adb->query_result_rowdata($res, $j);
			$col_value = array();
			for($k=0; $k<count($fieldsList); $k++) {
					$col_value[$fieldsList[$k]] = $row[$fieldsList[$k]];
			}
			if(count($col_value) > 0) {
				$col_value['id'] = $this->id;
				$columns = array_keys($col_value);
				$values = array_values($col_value);
				$query2 = "INSERT INTO vtiger_inventorysubproductrel(". implode(",",$columns) .") VALUES (". generateQuestionMarks($values) .")";
				$adb->pquery($query2, array($values));
				$prod_id = $col_value['productid'];
				$qty = $update_stock[$col_value['sequence_no']];
				updateStk($prod_id,$qty,'',array(),'Invoice');
			}
		}

		// Add the Shipping taxes for the Invoice
		$query3 = "SELECT * FROM vtiger_inventoryshippingrel WHERE id=?";
		$res = $adb->pquery($query3, array($salesorder_id));
		$no_of_shippingtax = $adb->num_rows($res);
		$fieldsList = $adb->getFieldsArray($res);
		for($j=0; $j<$no_of_shippingtax; $j++) {
			$row = $adb->query_result_rowdata($res, $j);
			$col_value = array();
			for($k=0; $k<count($fieldsList); $k++) {
				$col_value[$fieldsList[$k]] = $row[$fieldsList[$k]];
			}
			if(count($col_value) > 0) {
				$col_value['id'] = $this->id;
				$columns = array_keys($col_value);
				$values = array_values($col_value);
				$query4 = "INSERT INTO vtiger_inventoryshippingrel(". implode(",",$columns) .") VALUES (". generateQuestionMarks($values) .")";
				$adb->pquery($query4, array($values));
			}
		}

		//Update the netprice (subtotal), taxtype, discount, S&H charge, adjustment and total for the Invoice

		$updatequery  = " UPDATE vtiger_invoice SET ";
		$updateparams = array();
		// Remaining column values to be updated -> column name to field name mapping
		$invoice_column_field = Array (
			'adjustment' => 'txtAdjustment',
			'subtotal' => 'hdnSubTotal',
			'total' => 'hdnGrandTotal',
			'taxtype' => 'hdnTaxType',
			'discount_percent' => 'hdnDiscountPercent',
			'discount_amount' => 'hdnDiscountAmount',
			's_h_amount' => 'hdnS_H_Amount',
		);
		$updatecols = array();
		foreach($invoice_column_field as $col => $field) {
			$updatecols[] = "$col=?";
			$updateparams[] = $this->column_fields[$field];
		}
		if (count($updatecols) > 0) {
			$updatequery .= implode(",", $updatecols);

			$updatequery .= " WHERE invoiceid=?";
			array_push($updateparams, $this->id);

			$adb->pquery($updatequery, $updateparams);
		}
	}

	function insertIntoEntityTable($table_name, $module, $fileid = '')  {
		//Ignore relation table insertions while saving of the record
		if($table_name == 'vtiger_inventoryproductrel') {
			return;
		}
		parent::insertIntoEntityTable($table_name, $module, $fileid);
	}

	/*Function to create records in current module.
	**This function called while importing records to this module*/
	function createRecords($obj) {
		$createRecords = createRecords($obj);
		return $createRecords;
	}

	/*Function returns the record information which means whether the record is imported or not
	**This function called while importing records to this module*/
	function importRecord($obj, $inventoryFieldData, $lineItemDetails) {
		$entityInfo = importRecord($obj, $inventoryFieldData, $lineItemDetails);
		return $entityInfo;
	}

	/*Function to return the status count of imported records in current module.
	**This function called while importing records to this module*/
	function getImportStatusCount($obj) {
		$statusCount = getImportStatusCount($obj);
		return $statusCount;
	}

	function undoLastImport($obj, $user) {
		$undoLastImport = undoLastImport($obj, $user);
	}

	/** Function to export the lead records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Invoice Query.
	*/
	function create_export_query($where)
	{
		global $log;
		global $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Invoice", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);
		$fields_list .= getInventoryFieldsForExport($this->table_name);
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

		$query = "SELECT $fields_list FROM ".$this->entity_table."
				INNER JOIN vtiger_invoice ON vtiger_invoice.invoiceid = vtiger_crmentity.crmid
				LEFT JOIN vtiger_invoicecf ON vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
				LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_invoice.salesorderid
				LEFT JOIN vtiger_invoicebillads ON vtiger_invoicebillads.invoicebilladdressid = vtiger_invoice.invoiceid
				LEFT JOIN vtiger_invoiceshipads ON vtiger_invoiceshipads.invoiceshipaddressid = vtiger_invoice.invoiceid
				LEFT JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_invoice.invoiceid
				LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_inventoryproductrel.productid
				LEFT JOIN vtiger_service ON vtiger_service.serviceid = vtiger_inventoryproductrel.productid
				LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_invoice.contactid
				LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_invoice.accountid
				LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_invoice.currency_id
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";

		$query .= $this->getNonAdminAccessControlQuery('Invoice',$current_user);
		$where_auto = " vtiger_crmentity.deleted=0";

		if($where != "") {
			$query .= " where ($where) AND ".$where_auto;
		} else {
			$query .= " where ".$where_auto;
		}

		$log->debug("Exiting create_export_query method ...");
		return $query;
	}
     //不允许商务总监编辑
     /*
    function retrieve_entity_info($record, $module){
        parent::retrieve_entity_info($record, $module);
        global $currentView,$current_user;
        if($currentView=='Edit' && $current_user->column_fields['roleid']=='H79'){
            throw new AppException('当前状态不允许编辑!');
            exit;
        }
    }*/
    /**
     * @审核工作流程触发
     * @发票领取结点!!!关联发票领取人
     * @指定结点有
     * @param Vtiger_Request $request
     */
    function workflowcheckbefore(Vtiger_Request $request){
        $stagerecordid=$request->get('stagerecordid');
        $record=$request->get('record');
        $db=PearDatabase::getInstance();
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'Invoice'";
        $result=$db->pquery($query,array($stagerecordid));
        $currentflag=$db->query_result($result,0,'workflowstagesflag');

        //到发票审核节点先判断是财务部分字段否为空
        if($currentflag=='edit_financial'){
            $query="SELECT invoicecodeextend,invoice_noextend FROM vtiger_invoiceextend WHERE deleted=0 AND invoiceid=?";
            $result=$db->pquery($query,array($record));
            $invoicecode=$db->query_result($result,0,'invoicecodeextend');
            $invoice_no=$db->query_result($result,0,'invoice_noextend');
            if(empty($invoicecode) || empty($invoice_no)){
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = ":请先“编辑”填写完整信息再进行审核!";
                echo json_encode($resultaa);
                exit;
            }
        }
        //商务编辑取点
        if($currentflag=='edit_receive'){
            $query="SELECT contractid FROM vtiger_invoice WHERE invoiceid=?";
            $result=$db->pquery($query,array($record));
            $contractid=$db->query_result($result,0,'contractid');

            if($contractid<1){
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = ':请先“编辑”填写完整信息再进行审核!';
                echo json_encode($resultaa);
                exit;
            }
        }
        //发票领取时走的结点
        if($currentflag=='receive_invoice'){
            global $current_user;
            $sql="UPDATE vtiger_invoice SET receiveid=?,receivedate=? WHERE invoiceid=?";
            $db->pquery($sql,array($current_user->id,date('Y-m-d H:i:s'),$record));
        }
    }
    
    
}

?>