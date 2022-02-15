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

class AchievementallotStatistic extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'vtiger_achievementallot_statistic';
    var $table_index= 'achievementallotid';
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
    var $tab_name = Array('vtiger_achievementallot_statistic');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_achievementallot_statistic'   => 'achievementallotid',);

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
    /**
     * 申核后置事件
     * @param $request
     */
    public function workflowcheckafter(Vtiger_Request $request){
        $recordid = $request->get('record');
        $sql=" SELECT * FROM vtiger_achievementallot_statistic WHERE achievementallotid=? ";
        $recordinfo=$this->db->pquery($sql,array($recordid));
        $recordinfo=$this->db->query_result_rowdata($recordinfo,0);
        // cxh 2020-01-09 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['workflowsid']=$recordinfo['workflowsid'];
        $params['salesorderid']=$recordinfo['crmid'];
        $this->hasAllAuditorsChecked($params);
        //更新日志记录
        $currentTime = date('Y-m-d H:i:s');
        global $current_user;
        //更新记录
        $id = $this->db->getUniqueId('vtiger_modtracker_basic');
        $this->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $recordid, 'AchievementallotStatistic', $current_user->id,$currentTime, 0));
        if(!$recordinfo['adjustachievement']) $recordinfo['adjustachievement']=0;
        $this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?),(?,?,?,?)',
            Array($id,'adjustachievement',$recordinfo['adjustachievement'],$recordinfo['adjustachievementrecord']+$recordinfo['adjustachievement'],$id,'modulestatus','','c_complete',$id,'adjustremarks','',$recordinfo['adjustremarks']));
        $this->db->pquery('UPDATE `vtiger_achievementallot_statistic` SET `adjustachievement`=?, arriveachievement=arriveachievement-?,modulestatus=? WHERE (`achievementallotid`=?) LIMIT 1',
            array($recordinfo['adjustachievement']+$recordinfo['adjustachievementrecord'],$recordinfo['adjustachievementrecord'],'c_complete', $recordid));
        $this->db->pquery('UPDATE `vtiger_achievementsummary` SET realarriveachievement=realarriveachievement-? WHERE `userid`=? AND achievementmonth=? AND achievementtype=? LIMIT 1',
            array($recordinfo['adjustachievementrecord'],$recordinfo['receivedpaymentownid'],$recordinfo['achievementmonth'],$recordinfo['achievementtype']));
    }
}
?>
