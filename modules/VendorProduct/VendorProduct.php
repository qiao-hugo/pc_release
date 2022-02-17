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

require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
// Faq is used to store vtiger_faq information.
class VendorProduct extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_vendorsrebate';
    var $table_index= 'vendorsrebateid';
    var $column_fields = Array();


    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    //var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
    //var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    var $tab_name = Array('vtiger_crmentity','vtiger_vendorsrebate',);
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_vendorsrebate'=>'vendorid');

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        //'Subject'=> Array('visitingorder', 'subject'),

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
        //'Subject'=> 'subject',
    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        //'schoolname'=> Array('School', 'schoolname')
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
        //'schoolname'=> 'schoolname'
    );

    /*var $search_fields = Array(
        'schoolname'=> Array('School', 'schoolname')
    );
    var $search_fields_name = Array(
    /* Format: Field Label => fieldname 
        'schoolname'=> 'schoolname'
    );*/


    // For Popup window record selection
    var $popup_fields = Array('vendorsrebateid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'vendorsrebateid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'vendorsrebateid';

    // Required Information for enabling Import feature
    var $required_fields = Array('vendorsrebateid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('vendorsrebateid');

    var $relatedmodule_list=array();
    var $relatedmodule_fields=array(
        //'Schoolcontacts'=>
                    //array('schoolcontactsname'=>'联系人','position'=>'职位','gendertype'=>'性别','phone'=>'手机','email'=>'email'),
    );

    var $related_module_table_index = array(
        //'ServiceContracts' => array('table_name' => 'vtiger_servicecontracts', 'table_index' => 'servicecontractsid', 'rel_index' => 'sc_related_to'),

        //'Schoolrecruit'=>array('table_name' => 'vtiger_schoolrecruit', 'table_index' => 'schoolrecruitid', 'rel_index' => 'schoolid')

    );


    var $default_order_by = 'vendorsrebateid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('vendorsrebateid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module) {


    }
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=''){
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit);
        $query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_productprovider
				SET vtiger_salesorderworkflowstages.accountid=vtiger_productprovider.vendorid,
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_vendor.vendorname FROM vtiger_vendor WHERE vtiger_vendor.vendorid=vtiger_productprovider.vendorid)
				WHERE vtiger_productprovider.productproviderid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?";
        $this->db->pquery($query,array($salesorderid));
    }
    /**
     * 合同作废打回中处理
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request)
    {
        $record=$request->get('record');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ProductProvider'",array($record));
    }

}
?>