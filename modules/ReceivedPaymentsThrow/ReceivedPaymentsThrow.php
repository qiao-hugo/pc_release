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
class ReceivedPaymentsThrow extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'vtiger_receivedpayments';
    var $table_index= 'receivedpaymentsid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_receivedpayments');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_receivedpayments'   => 'receivedpaymentsid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (

         // Format: Field Label => Array(tablename, columnname)
         //  tablename should not have prefix 'vtiger_'
        
        
        //'Rel Operate'=> Array('receivedpayments', 'reloperate'),
        'Rel Id'=> Array('receivedpayments', 'unit_price'),
        'Related to'=>Array('receivedpayments','relatetoid'),
        
        //wangbin 2015-1-14 合同回款列表增加字段
        'description'=>Array('receivedpayments','overdue'),
        'RELATE DEPART'=>Array('receivedpayments','createid'),
        //'Status'=>Array('receivedpayments','status'),
        'Reality_date'=>Array('receivedpayments','reality_date'),
        'Modifieddate'=>Array('receivedpayments','modifiedtime'),
        'Mfr PartNo'=>Array('receivedpayments','createtime'),
        );

    
    var $list_fields_name = Array(
       //Format: Field Label => fieldname 
        //'Rel Operate'=>'reloperate',
        'Rel Id'=> 'unit_price',
        'Related to'=>'relatetoid',
        
        'description'=>'overdue',
        'RELATE DEPART'=>'createid',
        'Reality_date'=>'reality_date',
        'Modifieddate'=>'modifiedtime',
        'Mfr PartNo'=>'createtime',
    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support 弹出框的列表字段
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Rel Name'=> Array('receivedpayments', 'relmodule'),
        'Rel Operate'=> Array('receivedpayments', 'reloperate'),
        'Rel Id'=> Array('receivedpayments', 'unit_price'),
        'Related to'=>Array('receivedpayments','relatetoid')
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
        'Rel Name'=> 'relmodule',
        'Rel Operate'=>'reloperate',
        'Rel Id'=> 'unit_price',
        'Related to'=>'relatetoid'
    );

    // For Popup window record selection
    var $popup_fields = Array('relmodule');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'relmodule';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'relmodule';

    // Required Information for enabling Import feature
    var $required_fields = Array('relmodule'=>1);

    // Callback function list during Importing
    var $special_functions = Array('set_import_assigned_user');

    var $default_order_by = 'relmodule';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('relmodule');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }  
}

?>
