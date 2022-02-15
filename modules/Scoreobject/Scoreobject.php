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
class Scoreobject extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_scoreobject';
    var $table_index= 'scoreobjectid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    //var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
    //var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    var $tab_name = Array('vtiger_crmentity','vtiger_scoreobject');
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_scoreobject'=>'scoreobjectid');

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

    );
    var $search_fields_name = Array(
    );

    /*var $search_fields = Array();
    var $search_fields_name = Array(

    );*/


    // For Popup window record selection
    var $popup_fields = Array('scoreobjectid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'scoreobjectid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'scoreobjectid';

    // Required Information for enabling Import feature
    var $required_fields = Array('scoreobjectid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('scoreobjectid');

    var $relatedmodule_list=array();
    var $relatedmodule_fields=array(
       
    );

    var $related_module_table_index = array(
    );


    var $default_order_by = 'scoreobjectid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('scoreobjectid');

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
        
        $scoreobjectid = $this->id;
        $sql = "update vtiger_scorepara set scorepara_deleted=1 where scoreobjectid=?";
            $this->db->pquery($sql, array($scoreobjectid));
        //修改
        if(!empty($_REQUEST['updatei'])){
            
            $scorepara_item = 'scorepara_item =CASE scoreparaid ';
            $scorepara_score= 'scorepara_score=CASE scoreparaid ';
            $scorepara_upper= 'scorepara_upper=CASE scoreparaid ';
            $scorepara_lower= 'scorepara_lower=CASE scoreparaid ';
            $scorepara_deleted='scorepara_deleted=CASE scoreparaid ';
            foreach($_REQUEST['updatei'] as $key=>$value){
                $valueid = $value;
                $scorepara_item .=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['scorepara_item'][$key]);
                $scorepara_score.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['scorepara_score'][$key]);
                $scorepara_upper.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['scorepara_upper'][$key]);
                $scorepara_lower.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['scorepara_lower'][$key]);
                $scorepara_deleted.=sprintf(" WHEN %d THEN %d",$valueid, 0);
            }
            $sql="UPDATE vtiger_scorepara SET
                       {$scorepara_item} ELSE scorepara_item END,
                        {$scorepara_score} ELSE scorepara_score END,
                        {$scorepara_upper} ELSE scorepara_upper END,
                        {$scorepara_lower} ELSE scorepara_lower END,
                        {$scorepara_deleted} ELSE scorepara_deleted END
                        WHERE scoreobjectid={$scoreobjectid}";
            $this->db->pquery($sql, array());
        }

        // 添加组件参数
        $inserti = $_REQUEST['inserti'];
        if (!empty($inserti)) {
            $query = '';
            foreach ($inserti as $key=>$value) {
                $query .= "(NULL, {$this->id}, '{$_REQUEST['scorepara_item'][$value]}', '{$_REQUEST['scorepara_score'][$value]}', '{$_REQUEST['scorepara_upper'][$value]}', '{$_REQUEST['scorepara_lower'][$value]}', 0),";
            }
            $query = trim($query, ',');
            $sql = "INSERT INTO `vtiger_scorepara` (`scoreparaid`, `scoreobjectid`, `scorepara_item`, `scorepara_score`, `scorepara_upper`, `scorepara_lower`, `scorepara_deleted`) VALUES ".$query;
            $this->db->pquery($sql, array());
        }
    }


}
?>
