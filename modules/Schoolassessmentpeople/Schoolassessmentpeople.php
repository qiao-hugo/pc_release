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
class Schoolassessmentpeople extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_schoolassessmentpeople';
    var $table_index= 'assessmentpeopleid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    //var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
    //var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    var $tab_name = Array('vtiger_schoolassessmentpeople');
    var $tab_name_index = Array('vtiger_schoolassessmentpeople'=>'assessmentpeopleid');

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


    // For Popup window record selection
    var $popup_fields = Array('assessmentpeopleid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'assessmentpeopleid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'assessmentpeopleid';

    // Required Information for enabling Import feature
    var $required_fields = Array('assessmentpeopleid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('assessmentpeopleid');

    
/*    var $relatedmodule_list=array('Schoolresume');
    var $relatedmodule_fields=array(
        'Schoolresume'=>
            array('name'=>'??????','gendertype'=>'??????','highestdegree'=>'????????????',
                'schoolid'=>'?????????????????????','origin'=>'??????', 'minority'=>'??????','politicalstatus'=>'????????????'),
    );

    var $search_fields = Array(
        'recruitname'=> Array('schoolrecruit', 'recruitname'),
    );
    var $search_fields_name = Array(
        'recruitname'=> 'recruitname',
    );*/


    var $default_order_by = 'assessmentpeopleid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('assessmentpeopleid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * ???????????????
     * @param unknown $module
     */
    function save_module($module) {
        $assessmentresult = $_REQUEST['assessmentresult']; //??????????????????
        if ($assessmentresult == 'assessmentresult_yes') {
            $sql = "UPDATE vtiger_schoolresume, vtiger_schoolassessmentpeople SET vtiger_schoolresume.is_assessment_ok=1 WHERE vtiger_schoolassessmentpeople.schoolresumeid=vtiger_schoolresume.schoolresumeid AND
            assessmentpeopleid=? ";
            $this->db->pquery($sql, array($this->id));
        }  

    }


}
?>
