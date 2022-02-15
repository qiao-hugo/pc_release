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
class VisitDepartment extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_visitdepartment';
    var $table_index= 'visitdepartmentid';
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
    var $tab_name = Array('vtiger_visitdepartment');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_visitdepartment'   => 'visitdepartmentid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
  

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
 
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
    var $popup_fields = Array('visitdepartmentid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'visitdepartmentid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'visitdepartmentid';

    // Required Information for enabling Import feature
    var $required_fields = Array('visitdepartmentid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('visitdepartmentid');

    var $default_order_by = 'visitdepartmentid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('visitdepartmentid');
    var $relatedmodule_list=array('VisitAccountContract');
    //'Potentials','Quotes'
    var $relatedmodule_fields=array(
        'VisitAccountContract'=>array('vextractid'=>'vextractid',
            'accountid'=>'accountid',
            'linkname'=>'linkname',
            'commentstaus'=>'commentstaus',
            'commentstaus'=>'commentstaus',
            'commentstaus'=>'commentstaus',
            'vstartdate'=>'vstartdate'),
    );
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
        $id=empty($_REQUEST['record'])?$this->id:$_REQUEST['record'];
        $datetime=date('Y-m-d H:i:s');
        $yearAndMonth=$_REQUEST['year'].'-'.$_REQUEST['month'];
        $deparmentid=$_REQUEST['deparmentid'];
        $userid=getDepartmentUser($deparmentid[0]);
        $updatesql='update vtiger_visitcommentanalysis set deleted=1 WHERE visitdepartmentid='.$id;
        $sql="INSERT INTO vtiger_visitcommentanalysis(visitdepartmentid,visitingnum,visitingcommnum,poornumber,classic,commentresult,createdtime,deleted) 
        SELECT {$id},
            (SELECT count(1) FROM
                vtiger_visitingorder
            LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
            WHERE vtiger_crmentity.deleted = 0
            AND vtiger_visitingorder.related_to>0
            AND left(vtiger_visitingorder.startdate,7)='{$yearAndMonth}'
            AND vtiger_visitingorder.extractid in(".implode(',',$userid).")
            AND vtiger_visitingorder.modulestatus = 'c_complete') as allvnum,
            (SELECT count(1) FROM vtiger_visitaccountcontract AS a WHERE LEFT(a.vstartdate,7) = '{$yearAndMonth}' AND a.commentstaus!='' AND a.commentstaus IS NOT NULL AND a.vextractid IN(".implode(',',$userid).")) AS allnum,
            count(1) AS num,
            vtiger_visitaccountcontractsheet.classic,
            vtiger_visitaccountcontractsheet.commentresult,
            '{$datetime}',
            0
        FROM vtiger_visitaccountcontractsheet
        LEFT JOIN vtiger_visitaccountcontract ON vtiger_visitaccountcontractsheet.visitaccountcontractid = vtiger_visitaccountcontract.visitaccountcontractid
        WHERE  LEFT(vtiger_visitaccountcontract.vstartdate,7) = '{$yearAndMonth}' AND vtiger_visitaccountcontract.vextractid IN(".implode(',',$userid).")
        GROUP BY classic,commentresult";
        $this->db->pquery($updatesql,array());
        $this->db->pquery($sql,array());
    }
    /*function retrieve_entity_info($record, $module){
        parent::retrieve_entity_info($record, $module);
        global $currentView;
        if($currentView=='Edit'){
            throw new AppException('已禁用');
            exit;
        }
    }*/
}
?>
