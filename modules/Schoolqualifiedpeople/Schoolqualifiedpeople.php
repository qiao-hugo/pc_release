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
class Schoolqualifiedpeople extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_schoolqualifiedpeople';
    var $table_index= 'schoolqualifiedpeopleid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    //var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
    //var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    var $tab_name = Array('vtiger_crmentity','vtiger_schoolqualifiedpeople');
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_schoolqualifiedpeople'=>'schoolqualifiedpeopleid');

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
    var $popup_fields = Array('schoolqualifiedpeopleid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'schoolqualifiedpeopleid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'schoolqualifiedpeopleid';

    // Required Information for enabling Import feature
    var $required_fields = Array('schoolqualifiedpeopleid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('schoolqualifiedpeopleid');

    
/*    var $relatedmodule_list=array('Schoolresume');
    var $relatedmodule_fields=array(
        'Schoolresume'=>
            array('name'=>'姓名','gendertype'=>'性别','highestdegree'=>'最高学历',
                'schoolid'=>'毕业学校及专业','origin'=>'籍贯', 'minority'=>'民族','politicalstatus'=>'政治面貌'),
    );

    var $search_fields = Array(
        'recruitname'=> Array('schoolrecruit', 'recruitname'),
    );
    var $search_fields_name = Array(
        'recruitname'=> 'recruitname',
    );*/


    var $default_order_by = 'schoolqualifiedpeopleid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('schoolqualifiedpeopleid');

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
        $is_train = $_REQUEST['is_train']; //已培训
        $is_trainok = $_REQUEST['is_trainok']; //培训合格

        $set = array();
        if ($is_train == 'on') {
            $set[] = " vtiger_schoolresume.is_train=1 ";
        }  
        if ($is_trainok == 'on') {
            $set[] = " vtiger_schoolresume.is_train_ok=1 ";
        }  
        if (count($set) > 0) {
            $sql = "UPDATE vtiger_schoolresume, vtiger_schoolqualifiedpeople SET ". implode(',', $set) ." WHERE vtiger_schoolqualifiedpeople.schoolresumeid=vtiger_schoolresume.schoolresumeid AND
            schoolqualifiedpeopleid=? ";
            $this->db->pquery($sql, array($this->id));
        }

        //echo "<pre/>";
        //print_r($this->column_fields);exit();
        //任务：手工录入的简历可直接录取及之后的操作 gaocl add 2018/03/09
        $schoolqualifiedid=$this->addSchoolqualified($this->column_fields['schoolrecruitid']);
        $db = PearDatabase::getInstance();
        $query="UPDATE vtiger_schoolqualifiedpeople SET schoolqualifiedid=? WHERE schoolqualifiedpeopleid=?";
        $db->pquery($query, array($schoolqualifiedid,$this->id));

        $query="UPDATE vtiger_schoolresume SET reportsdate=? WHERE schoolresumeid=?";
        $db->pquery($query, array($this->column_fields['p_reportsdate'],$this->column_fields['schoolresumeid']));

    }

    /**
     * 添加招邮学校记录
     * @param Vtiger_Request $request
     * @param $record
     */
    private function addSchoolqualified($record)
    {
        $reportsdate = $this->column_fields['p_reportsdate'];
        $reportsower = $this->column_fields['p_reportsower'];
        $reportaddress = $this->column_fields['p_reportaddress'];
        global $current_user;
        $db = PearDatabase::getInstance();
        $sql = "SELECT vtiger_schoolrecruit.schoolrecruitid, vtiger_schoolrecruit.accompany, vtiger_schoolrecruit.remarks FROM vtiger_schoolrecruit WHERE schoolrecruitid=? LIMIT 1";
        $sel_result = $db->pquery($sql, array($record));
        $res_cnt = $db->num_rows($sel_result);
        $schoolrecruit = array();
        if ($res_cnt > 0) {
            $schoolrecruit = $db->query_result_rowdata($sel_result, 0);
        }

        if (!empty($schoolrecruit)) {

            $_REQUES['record']='';
            $request = new Vtiger_Request($_REQUES, $_REQUES);
            $request->set('module', 'Schoolqualified');
            $request->set('action', 'SaveAjax');
            $request->set('schoolrecruitid', $schoolrecruit['schoolrecruitid']);
            $request->set('schoolrecruitsower', $current_user->id);
            $request->set('reportsower', $reportsower);
            $request->set('reportsdate', $reportsdate);
            $request->set('accompany', $schoolrecruit['accompany']);
            $request->set('reportaddress', $reportaddress);
            $request->set('remarks', $schoolrecruit['remarks']);
            $ressorder = new Vtiger_SaveAjax_Action();
            $ressorderecord = $ressorder->saveRecord($request);

            /*if (!empty($ressorderecord)) {
                $schoolqualifiedid = $ressorderecord->getId();

            }*/
            return $ressorderecord->getId();
        }
    }
}
?>
