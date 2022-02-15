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

class SeparateInto extends CRMEntity
{
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_separateinto';
    var $table_index = 'separateintoid';
    var $tab_name = Array('vtiger_crmentity', 'vtiger_separateinto');
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 'vtiger_separateinto' => 'separateintoid');
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();


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
    var $popup_fields = Array('separateintoid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'separateintoid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'separateintoid';

    // Required Information for enabling Import feature
    var $required_fields = Array('separateintoid' => 1);

    // Callback function list during Importing
    var $special_functions = Array('separateintoid');

    var $default_order_by = 'separateintoid';
    var $default_sort_order = 'DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('separateintoid');

    function __construct()
    {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module)
    {
        $suoshugongsi = $_REQUEST['suoshugongsi'];
        $suoshuren = $_REQUEST['suoshuren'];
        $bili = $_REQUEST['bili'];
        global $current_user;
        $workflowsid=2186946;
        $workflowstagesid=2186948;
        $id=$_REQUEST['record']>0?$_REQUEST['record']:$this->id;
        $servicecontractsid_display = $_REQUEST['servicecontractsid_display'];
        $this->db->pquery("DELETE FROM `vtiger_servicecontracts_separate` WHERE separateintoid =?",array($id));
        if(!empty($suoshuren) ){
            $sql = "INSERT INTO `vtiger_servicecontracts_separate` (owncompanys, receivedpaymentownid,scalling, separateintoid,signdempart) SELECT invoicecompany,?,?,?, vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid WHERE vtiger_users.id =?";
            $sqlsub="INSERT INTO vtiger_salesorderworkflowstages (workflowstagesname,workflowstagesid,sequence,salesorderid,isaction,actiontime,addtime,workflowsid,modulename,smcreatorid,createdtime,productid,departmentid,ishigher,higherid,workflowstagesflag) values (?,?,?,?,?,?, NOW(),?,?,?,NOW(),?,?,?,?,?)";
            $actiontime=date('Y-m-d H:i:s');
            $result=$this->db->pquery("SELECT * FROM `vtiger_custompowers` WHERE custompowerstype='separateintoauditing' LIMIT 1",array());
            $data=$this->db->raw_query_result_rowdata($result,0);
            $roles=explode(',',$data['roles']);
            $accountId = $_REQUEST['accountid'];
            //查询客户信息 看看客户是不是来自市场部
            $account = $this->db->pquery("SELECT frommarketing FROM `vtiger_account` WHERE accountid=? LIMIT 1",array($accountId));
            $accountData = $this->db->raw_query_result_rowdata($account,0);
            $isPlus=0;
            //如果是来自于市场部则添加一条戴子龙审核分成单
            if($accountData['frommarketing']==1){
                $isPlus+=1;
                $reports_to_id =19;
                $this->db->pquery($sqlsub,array('市场负责人审核',$workflowstagesid,$isPlus,$id,1,$actiontime,$workflowsid,'SeparateInto',$current_user->id,0,$current_user->current_user_parent_departments,1,$reports_to_id,''));
            }
            for ($i=0;$i<count($suoshuren);++$i){
                $this->db->pquery($sql,array($suoshuren[$i],$bili[$i],$id,$suoshuren[$i]));
                if($suoshuren[$i]>1 && $suoshuren[$i]!=38){
                    $reportsModel = Users_Privileges_Model::getInstanceById($suoshuren[$i]);
                    $reports_to_id=$this->findreport($reportsModel,$roles);
                    $temp=$i+1+$isPlus;
                    $isaction=0;
                    if($i==0 && $isPlus==0){
                        $isaction=1;
                    }
                    // 如果直属上级是赵总 则修改审核人为当前分成人
                    if($reports_to_id==38){
                        $this->db->pquery($sqlsub,array('分成人<'.$reportsModel->last_name.'>审核',$workflowstagesid,$temp,$id,$isaction,$actiontime,$workflowsid,'SeparateInto',$current_user->id,0,$current_user->current_user_parent_departments,1,$reportsModel->id,''));
                    }else{
                        $this->db->pquery($sqlsub,array('分成人<'.$reportsModel->last_name.'>上级审核',$workflowstagesid,$temp,$id,$isaction,$actiontime,$workflowsid,'SeparateInto',$current_user->id,0,$current_user->current_user_parent_departments,1,$reports_to_id,''));
                    }
                }
            }
            $query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_separateinto
				SET vtiger_salesorderworkflowstages.accountid=vtiger_separateinto.accountid,
				    vtiger_salesorderworkflowstages.salesorder_nono =?,
				    vtiger_salesorderworkflowstages.modulestatus ='p_process',
				    vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_crmentity.label FROM vtiger_crmentity WHERE vtiger_crmentity.crmid=vtiger_separateinto.accountid)
				WHERE vtiger_separateinto.separateintoid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?  AND  vtiger_salesorderworkflowstages.workflowsid=? ";
            $this->db->pquery($query,array($servicecontractsid_display,$id,$workflowsid));
            $query="UPDATE vtiger_separateinto SET modulestatus='b_check',workflowsid={$workflowsid} WHERE separateintoid=?";
            $this->db->pquery($query,array($id));
            //新建时 消息提醒第一审核人进行审核
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$id,'salesorderworkflowstagesid'=>0));
        }
    }



    /**
     * @审核工作流程后置触发
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request)
    {
        $recordid = $request->get('record');
        // $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'SeparateInto',true);
        // if ($recordModel->get('modulestatus') == 'c_complete') {

        $sql = "SELECT modulestatus,servicecontractsid FROM vtiger_separateinto where separateintoid=? limit 1";
        $separateintoQuery = $this->db->pquery($sql, array($recordid));
        $moduleStatus = $this->db->query_result($separateintoQuery,0,'modulestatus');
        global $log;
        $log->info('分成单id：'.$recordid.', 审核状态：'.$moduleStatus);
        $servicecontractsid = $this->db->query_result($separateintoQuery,0,'servicecontractsid');
        if($moduleStatus == 'c_complete'){
            //$servicecontractsid=$recordModel->get('servicecontractsid');
            $this->db->pquery("DELETE FROM `vtiger_servicecontracts_separate` WHERE servicecontractid =?",array($servicecontractsid));
            $this->db->pquery("DELETE FROM `vtiger_servicecontracts_divide` WHERE servicecontractid =?", array($servicecontractsid));
            /*$sql = "UPDATE vtiger_servicecontracts_separate SET servicecontractid=? WHERE separateintoid=?";
            $this->db->pquery($sql, array($servicecontractsid,$recordid));*/
            $sql="insert into vtiger_servicecontracts_divide(owncompanys,receivedpaymentownid,scalling,servicecontractid,signdempart) SELECT owncompanys,receivedpaymentownid,scalling,?,signdempart FROM vtiger_servicecontracts_separate WHERE separateintoid=?";
            $this->db->pquery($sql,array($servicecontractsid,$recordid));
        }
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $stagerecordid=$request->get('stagerecordid');
        $query="SELECT
	      vtiger_salesorderworkflowstages.workflowsid
	      FROM
	      `vtiger_salesorderworkflowstages`
	      WHERE
	         vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ? ";
        $result=$this->db->pquery($query,array($stagerecordid));
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $params['workflowsid']=$workflowsid;
        $params['salesorderid']=$request->get('record');
        $this->hasAllAuditorsChecked($params);

    }
    /**
     * @审核工作流程触发
     * @发票领取结点!!!关联发票领取人
     * @指定结点有
     * @param Vtiger_Request $request
     */
    function workflowcheckbefore(Vtiger_Request $request){
        //$stagerecordid=$request->get('stagerecordid');
        $record=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,"SeparateInto");
        $servicecontractsid=$recordModel->get('servicecontractsid');
        $serviceRecordModel=Vtiger_Record_Model::getInstanceById($servicecontractsid,"ServiceContracts");
        if(!in_array($serviceRecordModel->get('modulestatus'),array('已发放','c_recovered'))){
            $resultaa['success'] = 'false';
            $resultaa['error']['message'] = "： 只有已发放或已收回的合同才能进行此操作。";
            //若果是移动端请求则走这个返回
            if( $request->get('isMobileCheck')==1){
                return $resultaa;
            }else{
                echo json_encode($resultaa);
                exit;
            }
        }
    }
    /**
     * 合同作废打回后置处理
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request)
    {
        $record=$request->get('record');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='SeparateInto' ",array($record));

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

    }
    public function findreport($reportsModel,$roles){
        $reports_to_id=$reportsModel->reports_to_id;
		echo $reports_to_id;
        $reportsModel = Users_Privileges_Model::getInstanceById($reports_to_id);
        if(in_array($reportsModel->roleid,$roles) || $reportsModel->reports_to_id=='38' || empty($reportsModel->reports_to_id) || $reports_to_id=='38'){
            return $reports_to_id;
        }
        return $this->findreport($reportsModel,$roles);
    }
    }
?>
