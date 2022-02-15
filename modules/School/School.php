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
class School extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_school';
    var $table_index= 'schoolid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    //var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
    //var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    var $tab_name = Array('vtiger_crmentity','vtiger_school');
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_school'=>'schoolid');

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
        'schoolname'=> Array('School', 'schoolname')
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
        'schoolname'=> 'schoolname'
    );

    /*var $search_fields = Array(
        'schoolname'=> Array('School', 'schoolname')
    );
    var $search_fields_name = Array(
    /* Format: Field Label => fieldname 
        'schoolname'=> 'schoolname'
    );*/


    // For Popup window record selection
    var $popup_fields = Array('schoolid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'schoolid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'schoolid';

    // Required Information for enabling Import feature
    var $required_fields = Array('schoolid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('schoolid');

    var $relatedmodule_list=array('Schoolcontacts',/*'Schoolvisit',*/'Schoolrecruit','VisitingOrder');
    var $relatedmodule_fields=array(
        'Schoolcontacts'=>
                    array('schoolcontactsname'=>'联系人','position'=>'职位','gendertype'=>'性别','phone'=>'手机','email'=>'email'),
        'Schoolvisit'=>array('subject'=>'subject','schoolid'=>'schoolid','contacts'=>'contacts','purpose'=>'purpose','extractid'=>'extractid','accompany'=>'accompany','startdate'=>'startdate','enddate'=>'enddate','remark'=>'remark','destination'=>'destination','outobject'=>'outobject','workflowsid'=>'workflowsid','modulestatus'=>'modulestatus'),
        'Schoolrecruit'=>array('recruitname'=>'recruitname','recruitmode'=>'recruitmode','schoolid'=>'schoolid','starttime'=>'starttime','endtime'=>'endtime','estimate'=>'estimate','schoolcontacts'=>'schoolcontacts','createuserid'=>'createuserid','recruitaddress'=>'recruitaddress','accompany'=>'accompany','remarks'=>'remarks','actualestimate'=>'actualestimate','actualsower'=>'actualsower'),
        'VisitingOrder'=>array('purpose'=>'purpose','destination'=>'Destination','outobjective'=>'OutObjective','contacts'=>'Contacts','extractid'=>'ExtractId','accompany'=>'Accompany','startdate'=>'StartDate','enddate'=>'EndDate','modulestatus'=>'Modulestatus'),
    );

    var $related_module_table_index = array(
        'ServiceContracts' => array('table_name' => 'vtiger_servicecontracts', 'table_index' => 'servicecontractsid', 'rel_index' => 'sc_related_to'),

        'Schoolrecruit'=>array('table_name' => 'vtiger_schoolrecruit', 'table_index' => 'schoolrecruitid', 'rel_index' => 'schoolid')

    );


    var $default_order_by = 'schoolid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('schoolid');

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


}
?>
