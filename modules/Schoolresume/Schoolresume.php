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
class Schoolresume extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_schoolresume';
    var $table_index= 'schoolresumeid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    //var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
    //var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    var $tab_name = Array('vtiger_crmentity','vtiger_schoolresume');
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_schoolresume'=>'schoolresumeid');

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
    var $popup_fields = Array('schoolresumeid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'schoolresumeid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'schoolresumeid';

    // Required Information for enabling Import feature
    var $required_fields = Array('schoolresumeid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('schoolresumeid');

/*    var $relatedmodule_list=array('Schoolcontacts');
    var $relatedmodule_fields=array(
        'Schoolcontacts'=>
                    array('schoolcontactsname'=>'联系人','position'=>'职位','gendertype'=>'性别','phone'=>'手机','email'=>'email'),
    );*/


    var $search_fields = Array(
        'name'=> Array('schoolresume', 'name'),
    );
    var $search_fields_name = Array(
        'name'=> 'name',
        'interviewuserid'=>'interviewuserid'
    );


    var $default_order_by = 'schoolresumeid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('schoolresumeid');

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
        $is_resume_qualified = $this->column_fields['is_resume_qualified'];
        if($is_resume_qualified == "on" || $is_resume_qualified == "1"){
            //选中招聘录取后直接到 校招录取人员名单显示
            $sql = "SELECT COUNT(1) as cnt FROM vtiger_schoolqualifiedpeople WHERE schoolresumeid=?";
            $db = PearDatabase::getInstance();
            $sel_result = $db->pquery($sql, array($this->id));
            $res_cnt = $db->query_result($sel_result, 0, 'cnt');
            if ($res_cnt > 0) {
                return;
            }
            //$schoolqualifiedid=$this->addSchoolqualified($request,$this->column_fields['schoolrecruitid']);

            $_REQUE['record']='';
            $newrequest=new Vtiger_Request($_REQUE, $_REQUE);
            $newrequest->set('schoolresumeid',$this->id);
            $newrequest->set('schoolrecruitid',$this->column_fields['schoolrecruitid']);
//            $newrequest->set('p_reportaddress',$p_reportaddress);
//            $newrequest->set('p_reportsdate',$p_reportsdate);
//            $newrequest->set('p_reportsower',$p_reportsower);
            //$newrequest->set('schoolqualifiedid',$schoolqualifiedid);
            $newrequest->set('module','Schoolqualifiedpeople');
            $newrequest->set('action','SaveAjax');
            $newrequest->set('view','Edit');
            $ressorder = new Vtiger_Save_Action();
            $ressorder->saveRecord($newrequest);

            $query="UPDATE vtiger_schoolresume SET is_resume_qualified=1,mailstatus=1 WHERE schoolresumeid=?";
            $db->pquery($query, array($this->id));

        }
    }

    function importRecord($importdata_object, $fieldData){
        $query='SELECT 1 FROM `vtiger_schoolresume` LEFT JOIN vtiger_crmentity ON vtiger_schoolresume.schoolresumeid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_schoolresume.`name`=? AND vtiger_schoolresume.telephone=?';
        $result=$this->db->pquery($query,array($fieldData['name'],$fieldData['telephone']));
        $num=$this->db->num_rows($result);
        if($num>0) {
            return '';
        }
        $fieldData['schoolrecruitid'] = getEntityId('Schoolrecruit', $fieldData['schoolrecruitid']);
        if ($fieldData['schoolrecruitid'] == '0') $fieldData['schoolrecruitid'] = '';
        if (!empty($fieldData['birthdate'])) $fieldData['birthdate'] = date("Y-m-d", strtotime($fieldData['birthdate']));
        $fieldData['schoolid'] = getEntityId('School', $fieldData['schoolid']);
        $fieldData["module"] = 'Schoolresume';
        $fieldData["action"] = 'Save';
        $save = new Vtiger_Save_Action();
        $res = $save->saveRecord(new Vtiger_Request($fieldData, $fieldData));
        return array('id' => $res->getId());

    }

}
?>
