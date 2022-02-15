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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Accounts/Accounts.php,v 1.53 2005/04/28 08:06:45 rank Exp $
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

// Account is used to store vtiger_account information.
class Staffcapacity extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_staffcapacity";
	var $table_index= 'staffcapacityid';
	//var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
	//var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    var $tab_name = Array('vtiger_staffcapacity');
    var $tab_name_index = Array('vtiger_staffcapacity'=>'staffcapacityid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('businessid', 'staffcapacityid');
	var $entity_table = "vtiger_crmentity";

	var $column_fields = Array();

	var $sortby_fields = Array();

	//var $groupTable = Array('vtiger_accountgrouprelation','accountid');

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
			);

	var $list_fields_name = Array(
			);
	var $list_link_field= '';

	var $search_fields = Array(
			);

	var $search_fields_name = Array(
			);
	// This is the list of vtiger_fields that are required
	var $required_fields =  array();

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array();

	//Default Fields for Email Templates -- Pavani
	var $emailTemplate_defaultFields = array();

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'staffcapacityid';
	var $default_sort_order = 'DESC';

	// For Alphabetical search
	var $def_basicsearch_col = '';
	var $related_module_table_index = array(
	);
	
	var $relatedmodule_list=array();
	//'Potentials','Quotes'
	var $relatedmodule_fields=array(
	
	);

	function Staffcapacity() {
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Staffcapacity');
	}

	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module) {
	}
}

?>
