<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');


class ContractExecution extends CRMEntity {

    var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'vtiger_contracts_execution';
    var $table_index = 'contractexecutionid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;
//    var $multirow_tables = array('vtiger_contracts_execution','vtiger_contracts_execution_detail','vtiger_servicecontracts');

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_crmentity', 'vtiger_contracts_execution');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 'vtiger_contracts_execution' => 'contractexecutionid');

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'

    );
    var $list_fields_name = Array(/* Format: Field Label => fieldname */

    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'

    );
    var $search_fields_name = Array(/* Format: Field Label => fieldname */

    );




    // For Popup window record selection
    var $popup_fields = Array('contractexecutionid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'contractexecutionid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'contractexecutionid';

    // Required Information for enabling Import feature
    var $required_fields = Array('contractexecutionid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('contractexecutionid');

    var $default_order_by = 'contractexecutionid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('contractexecutionid');

    var $relatedmodule_list = array( 'Files');
    var $relatedmodule_fields = array(
        'Files' => array(
            'name' => 'name',
            'uploader' => 'uploader',
            'uploadtime' => 'uploadtime',
            'style' => 'style',
            'filestate' => 'filestate',
            'deliversuserid' => 'deliversuserid',
            'delivertime' => 'delivertime'
        )
    );
    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }
    public function save_module(){}

    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename, $workflowsid, $salesorderid, $isedit = '') {
        parent::makeWorkflows($modulename, $workflowsid, $salesorderid, $isedit = '');
        $sql = "select stageshow,stage from vtiger_contracts_execution_detail where contractexecutionid=? order by stage desc limit 1";
        $result = $this->db->pquery($sql,array($salesorderid));
        $row = $this->db->query_result_rowdata($result,0);
        $stageshow = '合同执行'.$row['stageshow'];
        $stage = $row['stage'];

        $query = "UPDATE vtiger_salesorderworkflowstages,
				 vtiger_contracts_execution
				SET vtiger_salesorderworkflowstages.accountid=vtiger_contracts_execution.sc_related_to,
				     vtiger_salesorderworkflowstages.modulestatus='p_process',
				     vtiger_salesorderworkflowstages.salesorder_nono=vtiger_contracts_execution.contract_no,
				    vtiger_salesorderworkflowstages.workflowstagesname=?,
				    vtiger_salesorderworkflowstages.sequence=?,
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_contracts_execution.sc_related_to)
				WHERE vtiger_contracts_execution.contractexecutionid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=? AND  vtiger_salesorderworkflowstages.workflowsid=? AND isaction=1";
        $this->db->pquery($query, array($stageshow,$stage,$salesorderid,$workflowsid));
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
    }

    function workflowcheckafter(Vtiger_Request $request)
    {
        $stagerecordid = $request->get('stagerecordid');
        $record = $request->get('record');

        $query = "SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_salesorderworkflowstages.sequence,
        		    vtiger_salesorderworkflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'ContractExecution'";
        $result = $this->db->pquery($query, array($stagerecordid));
        $currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $sequence = $this->db->query_result($result, 0, 'sequence');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'ContractExecution', TRUE);
        $entity = $recordModel->entity->column_fields;
        $currentflag = trim($currentflag);
        $datetime = date('Y-m-d H:i:s');

        //将对应的执行合同详情进行修改
        $fileid = $request->get('fileid');
        $filename = $request->get('filename');
        $filenamevalue='';
        if($fileid){
            $this->db->pquery('update vtiger_files set relationid=? where attachmentsid=?',array($record,$fileid));
            $filenamevalue=$filename.'##'.$fileid;
        }
        global $current_user;
        $executiondetailid= $entity['executiondetailid'];
        //$this->db->pquery("update vtiger_contracts_execution_detail set executestatus='c_executed',executor=?,executedate=?,receiverabledate=?,voucher=? where executiondetailid=? and stage=?",array($current_user->id,date('Y-m-d H:i:s'),date('Y-m-d'),$filename.'##'.$fileid,$executiondetailid,$sequence));
        $this->db->pquery("update vtiger_contracts_execution_detail set executestatus='c_executed',executor=?,executedate=?,receiverabledate=?,voucher=? where contractexecutionid=? and stage=?",array($current_user->id,date('Y-m-d H:i:s'),date('Y-m-d'),$filenamevalue,$record,$sequence));
        $result=$this->db->pquery('SELECT executiondetailid FROM vtiger_contracts_execution_detail WHERE contractexecutionid=? AND stage=?',array($record,$sequence));
        $executiondetailid=$result->fields['executiondetailid'];
        $params['salesorderid']=$request->get('record');
        $params['workflowsid']=$workflowsid;
        $this->hasAllAuditorsChecked($params);
        $query='SELECT 1 FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND isaction in(1,0) LIMIT 1';
        $result = $this->db->pquery($query,array($record));
        $status='c_execution_complete';
        $executiondetailidsql='';
        if($this->db->num_rows($result)){
            $status='b_execution_actioning';
            $executiondetailidsql=',executiondetailid=(SELECT vtiger_contracts_execution_detail.executiondetailid FROM vtiger_contracts_execution_detail WHERE vtiger_contracts_execution_detail.contractexecutionid=vtiger_contracts_execution.contractexecutionid AND vtiger_contracts_execution_detail.stage='.($sequence+1).')';
        }
        $this->db->pquery("update vtiger_contracts_execution set updatedate=?,processdate=?,status=?".$executiondetailidsql." where contractexecutionid=?",array(date('Y-m-d H:i:s'),date("Y-m-d"),$status,$record));
        /*if($recordModel->isLastExecuted($recordModel->get('contractid'))){
            $this->db->pquery("update vtiger_contracts_execution set updatedate=?,processdate=?,status=?,executiondetailid=? where contractexecutionid=?",array(date('Y-m-d H:i:s'),date("Y-m-d"),'c_execution_complete',$executiondetailid,$record));
        }else{
            $this->db->pquery("update vtiger_contracts_execution set updatedate=?,processdate=?,status=?,executiondetailid=? where contractexecutionid=?",array(date('Y-m-d H:i:s'),date("Y-m-d"),'b_execution_actioning',$executiondetailid,$record));
        }*/

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

            $sql = sprintf('SELECT %s FROM %s WHERE %s %s', $column_clause, $from_clause, $where_clause, $limit_clause);

            $sql = str_replace('LEFT JOIN vtiger_contracts_execution ON vtiger_contracts_execution.contractexecutionid=vtiger_crmentity.crmid',
                'LEFT JOIN vtiger_contracts_execution ON vtiger_contracts_execution.contractexecutionid = vtiger_crmentity.crmid 
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_contracts_execution.contractid 
                left join vtiger_products on vtiger_products.productid=vtiger_servicecontracts.productid 
                LEFT JOIN vtiger_contracts_execution_detail ON vtiger_contracts_execution_detail.executiondetailid = vtiger_contracts_execution.executiondetailid 
                left join vtiger_contract_type on vtiger_contract_type.contract_type = vtiger_servicecontracts.contract_type
                ',$sql);
            $sql = str_replace('vtiger_contracts_execution.contract_no AS vtiger_contracts_executioncontract_no','vtiger_servicecontracts.contract_no AS vtiger_contracts_executioncontract_no',$sql);
//            $sql = str_replace('vtiger_servicecontracts.productid AS vtiger_servicecontractsproductid','( vtiger_products.productid ) AS vtiger_servicecontractsproductid',$sql);
            $sql = str_replace('vtiger_servicecontracts.sc_related_to AS vtiger_servicecontractssc_related_to','vtiger_servicecontracts.sc_related_to AS vtiger_contracts_executionsc_related_to',$sql);
            $sql = str_replace('vtiger_contracts_execution.stageshow AS vtiger_contracts_executionstageshow','vtiger_contracts_execution_detail.stageshow AS vtiger_contracts_executionstageshow',$sql);
            $sql = str_replace('vtiger_contracts_execution.executestatus AS vtiger_contracts_executionexecutestatus','vtiger_contracts_execution_detail.executestatus AS vtiger_contracts_executionexecutestatus',$sql);
            $sql = str_replace('vtiger_servicecontracts.productid AS vtiger_servicecontractsproductid','ifnull(vtiger_products.productid,vtiger_contract_type.contract_typeid) AS vtiger_servicecontractsproductid',$sql);
            $sql = str_replace("vtiger_servicecontracts.bussinesstype AS vtiger_servicecontractsbussinesstype","vtiger_contract_type.bussinesstype AS vtiger_servicecontractsbussinesstype",$sql);
//            echo $sql;die;
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