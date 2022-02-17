<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class TelStatistics extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_telstatistics';
    var $table_index= 'telstatisticsid';
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
    var $tab_name = Array('vtiger_telstatistics');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_telstatistics'   => 'telstatisticsid');

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
    var $popup_fields = Array('telstatisticsid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'telstatisticsid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'telstatisticsid';

    // Required Information for enabling Import feature
    var $required_fields = Array('telstatisticsid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('telstatisticsid');

    var $default_order_by = 'telstatisticsid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('telstatisticsid');
    /*var $relatedmodule_list=array('VisitImprovement');
    //'Potentials','Quotes'
    var $relatedmodule_fields=array(
        'VisitImprovement'=>array('userid'=>'userid','datetime'=>'datetime','remark'=>'remark'),
    );*/
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
    function insertIntoEntityTable($table_name, $module, $fileid = '') {
        /*global $log;
        global $current_user, $app_strings,$adb;
            $sql1 = "insert into $table_name(" . implode(",", $column) . ") values(" . generateQuestionMarks($value) . ")";
            $adb->pquery($sql1, $value);*/

    }


    // 回款导入
    function importRecord($importdata_object, $fieldData){
        global $current_user;
        $adb = PearDatabase::getInstance();
        //$recorid = $adb->getUniqueID('vtiger_telstatistics');
        //$fieldData['telstatisticsid'] = $recorid;
        $telnumberdate = date("Y-m-d",strtotime($fieldData['telnumberdate']));
        $departmentid = $fieldData['departmentid'];
        $usercode = trim($fieldData['usercode']);
        $usercode = str_pad ($usercode,6, "0" ,STR_PAD_LEFT);
        if(!empty($fieldData['useid'])){
                $sql = "SELECT id,departmentid FROM vtiger_users 
  left join vtiger_user2department on vtiger_users.id=vtiger_user2department.userid 
WHERE usercode=? and (vtiger_users.status='Active' or (vtiger_users.status!='Active' and vtiger_users.leavedate>=?)) LIMIT 1";
            $result = $adb->pquery($sql, array($usercode,$telnumberdate." 00:00:00") );
            $noofrows = $adb->num_rows($result);
            if ($noofrows > 0) {
                $department = $adb->fetch_array($result);
                $fieldData['useid'] = $department['id'];
                $fieldData['departmentid'] = $department['departmentid'];
            }else{
                return array('id'=>'');
            }
        }else{
            return array('id'=>'');
        }

        $tel_connect_rate = round((intval($fieldData['telnumber'])/intval($fieldData['total_telnumber']))*100,2);
        $fieldData['telnumberdate'] = $telnumberdate;
        $fieldData['tel_connect_rate'] = $tel_connect_rate;
        $sql = "select telstatisticsid from vtiger_telstatistics where useid=? and telnumberdate=?";
        $result = $adb->pquery($sql,array($fieldData['useid'],$telnumberdate));
        if($adb->num_rows($result)){
            $row = $adb->fetchByAssoc($result,0);
            $sql = "update vtiger_telstatistics set telnumber=?,telduration=?,total_telnumber=?,tel_connect_rate=?,modifiedby=?,modifiedtime=?,usercode=? where telstatisticsid=?";
            $adb->pquery($sql,array(
               $fieldData['telnumber'],$fieldData['telduration'],$fieldData['total_telnumber'],$tel_connect_rate,$current_user->id,date("Y-m-d H:i:s"),$usercode,$row['telstatisticsid']
            ));
            $recorid = $row['telstatisticsid'];
        }else{
            // 操作人
            $fieldData['smownerid'] = $current_user->id;
            $fieldData['createdtime'] = date('Y-m-d H:i:s');
            // 回款导入标示
            $fieldNames = array_keys($fieldData);
            $fieldValues = array_values($fieldData);
            $adb->pquery('INSERT INTO vtiger_telstatistics ('. implode(',', $fieldNames).') VALUES ('. generateQuestionMarks($fieldValues) .')', $fieldValues);
        }

        return array('id'=>$recorid);
    }

}
?>
