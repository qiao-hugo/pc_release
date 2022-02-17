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

class AchievementSummary extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'vtiger_achievementsummary';
    var $table_index= 'achievementid';
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
    var $tab_name = Array('vtiger_achievementsummary');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_achievementsummary'   => 'achievementid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (

         // Format: Field Label => Array(tablename, columnname)
         //  tablename should not have prefix 'vtiger_'


        //'Rel Operate'=> Array('receivedpayments', 'reloperate'),

        );

    
    var $list_fields_name = Array(
       //Format: Field Label => fieldname
        //'Rel Operate'=>'reloperate',

    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support 弹出框的列表字段
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'

    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */

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
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module) {


    }

    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=true){
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit);
        $query="UPDATE vtiger_salesorderworkflowstages
				SET vtiger_salesorderworkflowstages.ishigher=1,
				 vtiger_salesorderworkflowstages.higherid=16,
				 vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.originalmoduleid=(SELECT achievementid FROM vtiger_achievementsummary WHERE crmid=? LIMIT 1 )
				WHERE  vtiger_salesorderworkflowstages.salesorderid=?";
        $this->db->pquery($query,array($salesorderid,$salesorderid));
        $query="UPDATE vtiger_achievementsummary
				SET vtiger_achievementsummary.workflowsid=?,
				    vtiger_achievementsummary.workflowstime=?,
                    vtiger_achievementsummary.workflowsnode=?,
				    vtiger_achievementsummary.modulestatus=?
				WHERE  vtiger_achievementsummary.crmid=?";
        $this->db->pquery($query,array($workflowsid,date("Y-m-d H:i:s"),' 财务复核人审核','b_actioning',$salesorderid));
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
    }
    /**
     * 工单打回的后置事件处理机制
     * 微信提醒
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request){
        $recordid=$request->get('record');
        $sql=" SELECT * FROM vtiger_achievementsummary WHERE achievementid=? ";
        $recordinfo=$this->db->pquery($sql,array($recordid));
        $recordinfo=$this->db->query_result_rowdata($recordinfo,0);
        /*echo json_encode($entity);die();*/
        $stagerecordid=$request->get('isrejectid');
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'AchievementSummary'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='AchievementSummary' AND vtiger_salesorderworkflowstages.workflowsid=?",array($recordinfo['crmid'],$workflowsid));
        //更新日志记录
        $currentTime = date('Y-m-d H:i:s');
        global $current_user;
        //更新记录
        $id = $this->db->getUniqueId('vtiger_modtracker_basic');
        $this->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $recordid, 'AchievementSummary', $current_user->id,$currentTime, 0));
        if(!$recordinfo['adjustachievement']) $recordinfo['adjustachievement']=0;
        $this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?),(?,?,?,?)',
            Array($id,'adjustachievement',$recordinfo['adjustachievement'],$recordinfo['adjustachievementrecord']+$recordinfo['adjustachievement'],$id,'modulestatus','','a_exception',$id,'remarks','',$recordinfo['remarks']));
    }

    /**
     * 充值申请单这申核后置事件
     * @param $request
     */
    public function workflowcheckafter(Vtiger_Request $request){
        $recordid = $request->get('record');
        $sql=" SELECT * FROM vtiger_achievementsummary WHERE achievementid=? ";
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
            array($id, $recordid, 'AchievementSummary', $current_user->id,$currentTime, 0));
        if(!$recordinfo['adjustachievement']) $recordinfo['adjustachievement']=0;
        $this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?),(?,?,?,?)',
            Array($id,'adjustachievement',$recordinfo['adjustachievement'],$recordinfo['adjustachievementrecord']+$recordinfo['adjustachievement'],$id,'modulestatus','','c_complete',$id,'remarks','',$recordinfo['remarks']));

        $this->db->pquery('UPDATE `vtiger_achievementsummary` SET `adjustachievement`=?, realarriveachievement=realarriveachievement-? WHERE (`achievementid`=?) LIMIT 1',
            array($recordinfo['adjustachievement']+$recordinfo['adjustachievementrecord'],$recordinfo['adjustachievementrecord'], $recordid));
    }
    function retrieve_entity_info($record, $module) {
        global $adb, $log, $app_strings;

        // INNER JOIN is desirable if all dependent table has entries for the record.
        // LEFT JOIN is desired if the dependent tables does not have entry.
        $join_type = 'LEFT JOIN';

        // Tables which has multiple rows for the same record
        // will be skipped in record retrieve - need to be taken care separately.
        $multirow_tables = NULL;
        if (isset($this->multirow_tables)) {
            $multirow_tables = $this->multirow_tables;
        } else {
            $multirow_tables = array(
                'vtiger_campaignrelstatus',
                'vtiger_attachments',
                //'vtiger_inventoryproductrel',
                //'vtiger_cntactivityrel',
                'vtiger_email_track'
            );
        }

        // Lookup module field cache
        $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
        if ($cachedModuleFields === false) {
            // Pull fields and cache for further use
            $tabid = getTabid($module);

            $sql0 = "SELECT fieldname, fieldid, fieldlabel, columnname, tablename, uitype, typeofdata,presence FROM vtiger_field WHERE tabid=?";
            // NOTE: Need to skip in-active fields which we will be done later.
            $result0 = $adb->pquery($sql0, array($tabid));
            if ($adb->num_rows($result0)) {
                while ($resultrow = $adb->fetch_array($result0)) {
                    // Update cache
                    VTCacheUtils::updateFieldInfo(
                        $tabid, $resultrow['fieldname'], $resultrow['fieldid'], $resultrow['fieldlabel'], $resultrow['columnname'], $resultrow['tablename'], $resultrow['uitype'], $resultrow['typeofdata'], $resultrow['presence']
                    );
                }
                // Get only active field information
                $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
            }
        }

        if ($cachedModuleFields) {
            $column_clause = '';
            $from_clause   = '';
            $where_clause  = '';
            $limit_clause  = ' LIMIT 1'; // to eliminate multi-records due to table joins.

            $params = array();
            $required_tables = $this->tab_name_index; // copies-on-write

            foreach ($cachedModuleFields as $fieldinfo) {
                if (in_array($fieldinfo['tablename'], $multirow_tables)) {
                    continue;
                }
                // Added to avoid picking shipping tax fields for Inventory modules, the shipping tax detail are stored in vtiger_inventoryshippingrel
                // table, but in vtiger_field table we have set tablename as vtiger_inventoryproductrel.
                if(($module == 'Invoice' || $module == 'Quotes' || $module == 'SalesOrder' || $module == 'PurchaseOrder')
                    && stripos($fieldinfo['columnname'], 'shtax') !== false) {
                    continue;
                }

                // Alias prefixed with tablename+fieldname to avoid duplicate column name across tables
                // fieldname are always assumed to be unique for a module
                $column_clause .=  $fieldinfo['tablename'].'.'.$fieldinfo['columnname'].' AS '.$this->createColumnAliasForField($fieldinfo).',';
            }


            if (isset($required_tables['vtiger_crmentity'])) {
                // 2014-10-29 young 如果是单独的表，就不需要这个字段
                $column_clause .= 'vtiger_crmentity.deleted';

                $from_clause  = ' vtiger_crmentity';
                unset($required_tables['vtiger_crmentity']);
                foreach ($required_tables as $tablename => $tableindex) {
                    if (in_array($tablename, $multirow_tables)) {
                        // Avoid multirow table joins.
                        continue;
                    }
                    $from_clause .= sprintf(' %s %s ON %s.%s=%s.%s', $join_type,
                        $tablename, $tablename, $tableindex, 'vtiger_crmentity', 'crmid');
                }
                $where_clause .= ' vtiger_crmentity.crmid=?';
            }else{
                $column_clause .= $this->table_name.'.'.$this->table_index;
                $where_clause .= ' '.$this->table_name.'.'.$this->table_index.'=?';
                $from_clause  = $this->table_name;
            }


            $params[] = $record;
            $lefeJoin='LEFT JOIN vtiger_achievementsupdate ON (vtiger_achievementsupdate.uuserid=vtiger_achievementsummary.userid AND vtiger_achievementsupdate.uachievementmonth=vtiger_achievementsummary.achievementmonth AND vtiger_achievementsupdate.uachievementtype=vtiger_achievementsummary.achievementtype AND vtiger_achievementsupdate.uperformancetype=vtiger_achievementsummary.performancetype AND vtiger_achievementsupdate.deleted=0)';
            $sql = sprintf('SELECT %s FROM %s %s WHERE %s %s', $column_clause, $from_clause,$lefeJoin, $where_clause, $limit_clause);
            //echo $sql;
            //exit;

            $result = $adb->pquery($sql, $params);
            if (!$result || $adb->num_rows($result) < 1) {
                throw new Exception($app_strings['LBL_RECORD_NOT_FOUND'], -1);
            } else {
                $resultrow = $adb->query_result_rowdata($result);
                if (!empty($resultrow['deleted'])) {
                    throw new Exception($app_strings['LBL_RECORD_DELETE'], 1);
                }

                foreach ($cachedModuleFields as $fieldinfo) {
                    $fieldvalue = '';
                    $fieldkey = $this->createColumnAliasForField($fieldinfo);
                    //Note : value is retrieved with a tablename+fieldname as we are using alias while building query
                    if (isset($resultrow[$fieldkey])) {
                        $fieldvalue = $resultrow[$fieldkey];
                    }
                    $this->column_fields[$fieldinfo['fieldname']] = $fieldvalue;
                }
            }
        }

        $this->column_fields['record_id'] = $record;
        $this->column_fields['record_module'] = $module;
    }
}
?>
