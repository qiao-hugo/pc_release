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

class ExtensionTrial extends CRMEntity
{
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_extensiontrial';
    var $table_index = 'extensiontrialid';
    var $tab_name = Array('vtiger_crmentity', 'vtiger_extensiontrial');
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 'vtiger_extensiontrial' => 'extensiontrialid');
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
    var $popup_fields = Array('extensiontrialid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'extensiontrialid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'extensiontrialid';

    // Required Information for enabling Import feature
    var $required_fields = Array('extensiontrialid' => 1);

    // Callback function list during Importing
    var $special_functions = Array('extensiontrialid');

    var $default_order_by = 'extensiontrialid';
    var $default_sort_order = 'DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('extensiontrialid');

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
        $servicecontractsid=$_REQUEST['servicecontractsid'];
        $id=$_REQUEST['record']>0?$_REQUEST['record']:$this->id;
        if($servicecontractsid) {
            $recordModel = Vtiger_Record_Model::getInstanceById($servicecontractsid, 'ServiceContracts');
            $entity = $recordModel->entity->column_fields;
            $res = $this->db->pquery('SELECT 1 FROM vtiger_extensiontrial WHERE servicecontractsid=?', array($servicecontractsid));
            $num = $this->db->num_rows($res);
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile($entity['assigned_user_id']);
            //$query="SELECT vtiger_auditsettings.oneaudituid,vtiger_auditsettings.towaudituid FROM`vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$current_user->departmentid}') AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) INNER JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='ServiceContracts' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1";
            //$resultAuditSettings=$this->db->pquery($query,array());
            //$oneaudituid=$this->db->query_result($resultAuditSettings,0,'oneaudituid');
            //$towaudituid=$this->db->query_result($resultAuditSettings,0,'towaudituid');
            $auditSettings=$this->getAudituid('ServiceContracts',$current_user->departmentid);
            $oneaudituid=$auditSettings['oneaudituid'];
            $towaudituid=$auditSettings['towaudituid'];
            if ($num == 1)
            {
                $auditor=$oneaudituid;
            }
            else if($num==2)
            {
                $auditor=$towaudituid;
            }
            else
            {
                $auditor=0;
            }
            $this->db->pquery('UPDATE vtiger_extensiontrial SET extensionfrequency=?,auditor=? WHERE extensiontrialid=?',array($num,$auditor,$id));
        }

    }

    /**
     * 本地有线上没有然后直接把本地有的先上传上来待测试内容
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     * @throws Exception
     */
    function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=''){
        global  $current_user;
        $deparmentid = $current_user->departmentid;
        /*echo "<pre>";
        var_dump($_REQUEST);
        die();*/
        //  cxh start 是为了创建延期申请时 把指定审核人 更新进去 // admin 用户不存在 指定审核人。
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit);
        $result=$this->db->pquery('SELECT 1 FROM vtiger_extensiontrial WHERE servicecontractsid=?',array($_REQUEST['servicecontractsid']));
        $numbers = $this->db->num_rows($result);
        $higherid='';
        // 查询当前部门信息
        $auditSettings=$this->getAudituid('ServiceContracts',$deparmentid);
        $oneaudituid=$auditSettings['oneaudituid'];
        $towaudituid=$auditSettings['towaudituid'];
        if($numbers==1){
            $higherid=$oneaudituid;
            // 查询该部门的父部门H1::H2::H3
            /*$parentDepartment=$this->db->pquery("SELECT * FROM vtiger_departments WHERE  departmentid=? LIMIT  1  ",array($deparmentid));
            $parentDepartment=$this->db->query_result($parentDepartment,0,'parentdepartment');
            $parentDepartment=explode("::",$parentDepartment);
            //H1::H2::H3 遍历 id  部门等级最低的包含指定人设置的为指定审核人。
            foreach ($parentDepartment as $key=>$value){
                $auditsetting = $this->db->pquery("SELECT * FROM vtiger_auditsettings  WHERE  department=? AND auditsettingtype='ServiceContracts'  LIMIT  1  ",array($value));
                if($this->db->num_rows($auditsetting)>0){
                    $higherid = $this->db->query_result($auditsetting,0,'oneaudituid');
                }
            }*/
            // 如果存在 给与设置sql 设置第一指定人
            if($higherid>0){
                $higherid = ', vtiger_salesorderworkflowstages.ishigher=1,vtiger_salesorderworkflowstages.higherid='.$higherid;
            }
            /*// 查询这个部门的审核人id
            $result=$this->db->pquery("SELECT * FROM vtiger_auditsettings WHERE auditsettingtype='ServiceContracts' AND department=? ",array($deparmentid));
            $higherid=$this->db->query_result($result,0,'oneaudituid');//towaudituid*/
        }else if($numbers==2){
            $higherid=$towaudituid;
            // 查询该部门的父部门H1::H2::H3
            /*$parentDepartment=$this->db->pquery("SELECT * FROM vtiger_departments WHERE  departmentid=? LIMIT  1  ",array($deparmentid));
            $parentDepartment=$this->db->query_result($parentDepartment,0,'parentdepartment');
            $parentDepartment=explode("::",$parentDepartment);

            //H1::H2::H3 遍历 id  部门等级最低的包含指定人设置的为指定审核人。
            foreach ($parentDepartment as $key=>$value){
                $auditsetting = $this->db->pquery("SELECT * FROM vtiger_auditsettings  WHERE  department=? AND auditsettingtype='ServiceContracts'  LIMIT  1  ",array($value));
                if($this->db->num_rows($auditsetting)>0){
                    $higherid = $this->db->query_result($auditsetting,0,'towaudituid');
                }
            }*/
            // 如果存在 给与设置sql 设置第二审核人为指定人审核人
            if($higherid>0){
                $higherid = ', vtiger_salesorderworkflowstages.ishigher=1,vtiger_salesorderworkflowstages.higherid='.$higherid;
            }
        }else{
            echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">合同延期只允许申请两次!!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
            exit;
        }
        //cxh end 是为了创建延期申请时 把指定审核人 更新进去

        //创建延期申请单后 把服务合同编号更新到 vtiger_salesorderworkflowstages
        $servicecontractsid_display=$_REQUEST['servicecontractsid_display'];
        $this->db->pquery("UPDATE  vtiger_salesorderworkflowstages  SET vtiger_salesorderworkflowstages.salesorder_nono=?, vtiger_salesorderworkflowstages.modulestatus='p_process'".$higherid." WHERE vtiger_salesorderworkflowstages.salesorderid=?   AND  vtiger_salesorderworkflowstages.workflowsid=? ",array($servicecontractsid_display,$salesorderid,$workflowsid));
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
    }

    /**
     * @审核工作流程后置触发
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request)
    {
        $recordid = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'ExtensionTrial');
        $entity = $recordModel->entity->column_fields;
        if ($entity['servicecontractsid'] > 0 && $entity['modulestatus'] == 'c_complete') {
            //$reportsModel = Users_Privileges_Model::getInstanceById($entity['assigned_user_id']);
            //$reportsModel=Users_Privileges_Model::getInstanceById($reportsModel->reports_to_id);
            global $current_user;
            $db = PearDatabase::getInstance();
            $sql = "UPDATE vtiger_servicecontracts SET vtiger_servicecontracts.confirmvalue=TRIM(TRAILING '##' FROM CONCAT('" . $current_user->last_name. "," . date('Y-m-d H:i:s') . "##',IFNULL(confirmvalue,''))),isconfirm=1,delayuserid=?,confirmlasttime='" . date('Y-m-d H:i:s') . "' WHERE servicecontractsid=?";
            $db->pquery($sql, array($current_user->id, $entity['servicecontractsid']));
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
    //延期申请单打回后去除表中数据
    public function backallAfter(Vtiger_Request $request){
        $stagerecordid = $request->get('isrejectid');
        $record = $request->get('record');
        $query = "SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'ExtensionTrial'";
        $result = $this->db->pquery($query, array($stagerecordid));
        //$currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ExtensionTrial' AND vtiger_salesorderworkflowstages.workflowsid=?", array($record, $workflowsid));
    }
}
?>
