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
class Compensation extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_compensation';
    var $table_index= 'compensationid';
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
    var $tab_name = Array('vtiger_compensation');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_compensation'   => 'compensationid',);

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

    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */

    );

    // For Popup window record selection
    var $popup_fields = Array('compensationid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'compensationid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'compensationid';

    // Required Information for enabling Import feature
    var $required_fields = Array('compensationid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('compensationid');

    var $default_order_by = 'compensationid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('compensationid');

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



    // 回款导入
    function importRecord($importdata_object, $fieldData){
        global $current_user;
        $adb = PearDatabase::getInstance();

/*        print_r($importdata_object);
        exit;*/

        $recorid = $adb->getUniqueID('vtiger_compensation');

        $addData = array();
        $addData['compensationid'] = $recorid;
        $addData['releasedate'] = $fieldData['releasedate'];
        $addData['fixedsalary'] = $fieldData['fixedsalary'];
        $addData['name'] = empty($fieldData['name']) ? $recorid : $fieldData['name'];
        //$addData['name'] = $recorid;
        $addData['insuredtype'] = $fieldData['insuredtype'];
        $addData['meritpay'] = $fieldData['meritpay'];
        $addData['allowancebonus'] = $fieldData['allowancebonus'];
        $addData['percentage'] = $fieldData['percentage'];

        //replenishment
        $addData['replenishment'] = $fieldData['replenishment'];
        $addData['debit'] = $fieldData['debit'];
        $addData['wagejointventure'] = $fieldData['wagejointventure'];

        $addData['individualsocialinsuran'] = $fieldData['individualsocialinsuran'];
        $addData['accumulationfund'] = $fieldData['accumulationfund'];
        $addData['individualincometax'] = $fieldData['individualincometax'];

        $addData['taxdeductions'] = $fieldData['taxdeductions'];
        $addData['realwage'] = $fieldData['realwage'];



        $fieldNames = array_keys($addData);
        $fieldValues = array_values($addData);

        $adb->pquery('INSERT INTO vtiger_compensation ('. implode(',', $fieldNames).') VALUES ('. generateQuestionMarks($fieldValues) .')', $fieldValues);

    }

}
?>
