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

class VisitingOrder extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_visitingorder';
    var $table_index= 'visitingorderid';
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
    var $tab_name = Array('vtiger_crmentity', 'vtiger_visitingorder');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
    	'vtiger_crmentity' => 'crmid',
        'vtiger_visitingorder'   => 'visitingorderid',);

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
    var $popup_fields = Array('visitingorderid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'visitingorderid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'visitingorderid';

    // Required Information for enabling Import feature
    var $required_fields = Array('visitingorderid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('visitingorderid');

    var $default_order_by = 'visitingorderid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('visitingorderid');
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
   		//获取登录用户信息
	   	$currentUser = Users_Record_Model::getCurrentUserModel();
	   	$userid = $currentUser->get('id');
	
	   	//更新添加人，添加时间，状态
        if(empty($_REQUEST['record'])){
            $update_query = "update vtiger_visitingorder set examinestatus='unaudited',followstatus='notfollow' where visitingorderid=?";
            $update_params = array($this->id);
            $this->db->pquery($update_query, $update_params);
            $result=$this->db->pquery("SELECT 1 FROM vtiger_crmentity WHERE setype='Accounts' AND crmid=?",array($_REQUEST['related_to']));
            if($this->db->num_rows($result)) {
                $visitaccountcontractid = $this->db->getUniqueId('vtiger_visitaccountcontract');
                $this->db->pquery('INSERT INTO `vtiger_visitaccountcontract`(visitaccountcontractid,accountid,visitingorderid,vextractid,vaccompany,vstartdate) SELECT ?,vtiger_visitingorder.related_to,vtiger_visitingorder.visitingorderid,vtiger_visitingorder.extractid,vtiger_visitingorder.accompany,vtiger_visitingorder.startdate FROM vtiger_visitingorder WHERE vtiger_visitingorder.related_to>0 AND visitingorderid=?', array($visitaccountcontractid, $this->id));
            }
        }else{
            //拜访单打回后重新编辑,修改其状态
            $this->db->pquery("update vtiger_visitingorder set modulestatus=if(modulestatus='a_exception','a_normal',modulestatus) where visitingorderid=?",array($this->id));
        }
        $this->db->pquery("UPDATE vtiger_crmentity,vtiger_visitingorder SET vtiger_visitingorder.accountnamer=vtiger_crmentity.label,vtiger_visitingorder.modulename=vtiger_crmentity.setype WHERE vtiger_visitingorder.related_to=vtiger_crmentity.crmid AND vtiger_visitingorder.visitingorderid=?",array($this->id));
        $this->db->pquery("UPDATE vtiger_visitingorder SET auditorid=(SELECT vtiger_users.reports_to_id FROM vtiger_users WHERE vtiger_users.id=vtiger_visitingorder.extractid LIMIT 1) WHERE visitingorderid=?",array($this->id));
    }
    /**
     * @审核工作流程触发
     * @拜访单提单人上级审核后来判断是否要升级
     * @当前拜访的客户等级如果是机会客户则升级为40%意向客户,其它不变
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request){
        $recordid=$request->get('record');
        $stagerecordid=$request->get('stagerecordid');
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'VisitingOrder'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $workflowstagesflag=$this->db->query_result($result,0,'workflowstagesflag');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'VisitingOrder',true);
        $entity=$recordModel->entity->column_fields;
        switch($workflowstagesflag){
            case 'revoke_visitingorder':
                $this->db->pquery("UPDATE vtiger_visitingorder SET modulestatus='c_cancel' WHERE visitingorderid=?",array($recordid));
                break;
            case 'DOAPPEAL'://申诉确认
                $resultData=$this->db->pquery('SELECT if(vtiger_visitsign.isappeal=1,vtiger_visitsign.userid,vtiger_visitsign_mulit.userid) as userid FROM vtiger_visitsign,vtiger_visitsign_mulit WHERE vtiger_visitsign.visitingorderid=vtiger_visitsign_mulit.visitingorderid AND vtiger_visitsign.visitingorderid=? AND (vtiger_visitsign.isappeal=1 OR vtiger_visitsign_mulit.isappeal=1) LIMIT 1',array($recordid));
                $userid=$this->db->query_result($resultData,0,'userid');
                $this->db->pquery('UPDATE vtiger_visitsign SET isappeal=2,issign=1 WHERE visitingorderid=? AND isappeal=1',array($recordid));
                $this->db->pquery('UPDATE vtiger_visitsign_mulit SET isappeal=2,issign=1 WHERE visitingorderid=? AND isappeal=1',array($recordid));
                $sql = 'UPDATE vtiger_visitingorder SET issign=1 WHERE visitingorderid=?';
                $this->db->pquery($sql,array($recordid));
                $recordModel->setEffectiveVisits($recordid,$userid);
                break;
            default:
                if($entity[related_to]>0 && $entity['modulestatus']=='c_complete'){

                    $now=time();
                    if($entity['newfirstvisting']==1){
                        $this->db->pquery("INSERT INTO vtiger_accountrankhistory(`accountid`,`oldaccountrank`,`newaccountrank`,`createdtime`) SELECT accountid,accountrank,'forp_notv',now() FROM vtiger_account WHERE accountid=? AND accountrank!='chan_notv'",array($entity['related_to']));
                    }
                    $this->db->pquery("UPDATE `vtiger_account` SET accountrank=(IF(accountrank='chan_notv','forp_notv',accountrank )),visitingtimes =IFNULL(visitingtimes,0)+1,visitingorderlastfollowtime={$now} WHERE accountid=?",array($entity['related_to']));
                    $updateSQL='UPDATE vtiger_effective_visits SET iscomplete=1 WHERE visitingorderid=?';
                    $this->db->pquery($updateSQL,array($recordid));
                }
        }
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['salesorderid']=$request->get('record');
        $params['workflowsid']=$workflowsid;
        $this->hasAllAuditorsChecked($params);
    }
    function retrieve_entity_info($record, $module){
        parent::retrieve_entity_info($record, $module);
        global $currentView,$current_user;
        if($currentView=='Edit'&&$this->column_fields['extractid']!=$current_user->id&&$current_user->id!=1){
            throw new AppException('只有提单人才能编辑!');
            exit;
        }
    }
    /**
     * 工作流打回后置处理事件
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request){
        $stagerecordid=$request->get('isrejectid');
        $record=$request->get('record');
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'VisitingOrder'";
        $result=$this->db->pquery($query,array($stagerecordid));

        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $workflowstagesflag=$this->db->query_result($result,0,'workflowstagesflag');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='VisitingOrder' AND vtiger_salesorderworkflowstages.workflowsid=?",array($record,$workflowsid));
        if($workflowstagesflag=='revoke_visitingorder'){
            $this->db->pquery("UPDATE vtiger_visitingorder SET modulestatus='c_complete' WHERE visitingorderid=?",array($record));
        }
        if($workflowstagesflag=='DOAPPEAL') {//申诉打回
            $this->db->pquery('UPDATE vtiger_visitsign SET isappeal=4 WHERE visitingorderid=? AND isappeal=1', array($record));
            $this->db->pquery('UPDATE vtiger_visitsign_mulit SET isappeal=4 WHERE visitingorderid=? AND isappeal=1', array($record));
            $this->db->pquery("UPDATE vtiger_visitingorder SET modulestatus='c_complete' WHERE visitingorderid=?", array($record));
        }
    }
    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=true){
        global $isallow;//移动端过来不加载,手动创建一个
        $isDoRevoke='';
        // 如果是撤销 生成工作流
        if($isedit=='doRevoke'){
            $isDoRevoke=$isedit;
            $isedit = false;

        }
        $isallow=empty($isallow)?array($modulename):$isallow;
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit);
        $query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_visitingorder
				SET vtiger_salesorderworkflowstages.accountid=vtiger_visitingorder.related_to,
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_crmentity.label FROM vtiger_crmentity WHERE vtiger_crmentity.crmid=vtiger_visitingorder.related_to)
				WHERE vtiger_visitingorder.visitingorderid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?";
        $this->db->pquery($query,array($salesorderid));
        // 如果是撤销 生成工作流
        if($isDoRevoke=='doRevoke'){
            $Sql="UPDATE vtiger_salesorderworkflowstages SET workflowstagesname='提单人上级作废审核' WHERE isaction=1 AND modulename='VisitingOrder' AND salesorderid=? AND workflowsid=?";
            $this->db->pquery($Sql,array($salesorderid,$workflowsid));
        }
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));

    }
}
?>
