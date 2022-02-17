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

class UserManger extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_usermanger';
    var $table_index= 'usermangerid';
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
    var $tab_name = Array('vtiger_crmentity', 'vtiger_usermanger');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
    	'vtiger_crmentity' => 'crmid',
        'vtiger_usermanger'   => 'usermangerid');

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
        'Name'=>Array('vtiger_users'=>'last_name'),
        'Email'=>Array('vtiger_users'=>'email1')
    );
    var $search_fields_name = Array(
        'User Name'=>'last_name',
        'Email'=>'email1',
        'Department'=>'department'
    );

    // For Popup window record selection
    var $popup_fields = Array('last_name');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'usermangerid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'usermangerid';

    // Required Information for enabling Import feature
    var $required_fields = Array('usermangerid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('usermangerid');
    var $default_order_by = 'usermangerid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('usermangerid');
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
    /**
     * @审核工作流程触发
     * @拜访单提单人上级审核后来判断是否要升级
     * @当前拜访的客户等级如果是机会客户则升级为40%意向客户,其它不变
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request){
        $recordid=$request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'UserManger',true);
        $entity=$recordModel->entity->column_fields;
        if($entity['modulestatus']=='c_complete'){
            $array=array();
            $req=new Vtiger_Request($array,$array);
            $dontmodify=array('user_password');
            foreach($entity as $key=>$value){
                if(($entity['userid']>0 && in_array($key,$dontmodify)) || $key=='usercode'){
                    continue;
                }
                if($key!='record'){
                    $req->set($key,$value);
                    $_REQUEST[$key]=$value;
                }

                if(in_array($key,array('fillinsales','showbackstage','isdimission'))&& !empty($value)){
                    $req->set($key,'on');
                    $_REQUEST[$key]='on';
                }
            }
            if(empty($entity['userid'])){
                $req->set('record','');
                $req->set('is_admin','0');
                $_REQUEST['is_admin']=0;
                /*$max=$this->db->getUniqueID("vtiger_usercode");
                $max=str_pad($max, 6, '0', STR_PAD_LEFT);
                //用户编号改为6码，原有的用户编号不足6码的，前面补0
                $req->set('usercode',$max);
                $this->db->pquery('insert into vtiger_userscode (ucode,status) values(?,?)',array($max,1));*/
            }else{
                $req->set('id',$entity['userid']);
                $req->set('record',$entity['userid']);
            }
            $req->set('module','Users');
            $req->set('action','Save');
            $userSaveAction=new Users_Save_Action();
            $recordModel=$userSaveAction->saveRecord($req);
            $userSaveAction->updateCompanyId($recordModel->getId(),$entity['invoicecompany']);
            if(empty($entity['userid'])){
                $sql='UPDATE vtiger_usermanger SET userid=? WHERE usermangerid=?';
                $this->db->pquery($sql,array($recordModel->getId(),$recordid));
            }

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
                AND vtiger_salesorderworkflowstages.modulename = 'UserManger'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='UserManger' AND vtiger_salesorderworkflowstages.workflowsid=?",array($record,$workflowsid));
        $this->db->pquery("UPDATE vtiger_usermanger SET modulestatus='a_normal' WHERE usermangerid=?",array($record));

    }
}
?>
