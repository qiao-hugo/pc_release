<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vendors_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
        $this->exposeMethod('setVendorState');
        $this->exposeMethod('setAllowtransaction');
        $this->exposeMethod('addWorkFlows');
        $this->exposeMethod('addAuditsettings');
        $this->exposeMethod('deletedAuditsettings');
        $this->exposeMethod('checkVendorName');
        $this->exposeMethod('files_deliver');
        $this->exposeMethod('files_delete');
        $this->exposeMethod('setVendorScore');
        $this->exposeMethod('doChangeSmowner');
	}

	function checkPermission(Vtiger_Request $request) {
		return ;
	}



    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

    // 添加工作流
    public function addWorkFlows(Vtiger_Request $request) {
        $recordId = $request->get('recordid');
        $recordPermission = Users_Privileges_Model::isPermitted('Vendors', 'DetailView', $recordId);
        do {
            if (!$recordPermission) {
                $resultMsg=array("success"=>false,"msg"=>"没有权限");
                break;
            }
            $db = PearDatabase::getInstance();
            $query = "SELECT 1 FROM `vtiger_vendor` 
                      WHERE vendorid=? 
                      AND (bankaccount IS NULL 
                      OR bankaccount='' 
                      OR bankname IS NULL OR bankname='' 
                      OR banknumber IS NULL OR banknumber='')";
            $result=$db->pquery($query, array($recordId));
            if($db->num_rows($result)){
                $resultMsg=array("success"=>false,"msg"=>"账号信息不能为空!");
                break;
            }
            $query = "SELECT 1 from vtiger_files where relationid=? AND delflag=0";
            $result=$db->pquery($query, array($recordId));
            if($db->num_rows($result)==0){
                $resultMsg=array("success"=>false,"msg"=>"至少要添加一个附件!");
                break;
            }
            //转正必须要有一个资质证明
            $query = "SELECT 1 from vtiger_files where relationid=? AND delflag=0 and style=?";
            $result=$db->pquery($query, array($recordId,'files_style10'));
            if($db->num_rows($result)==0){
                $resultMsg=array("success"=>false,"msg"=>"至少要添加一个附件类型是营业执照的附件!");
                break;
            }

            $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Vendors');
            if($recordModel->get('vendortype')=='MediaProvider'){
                $workflowsid = '2131277';//媒介类供应商审核流程
            }else{
                $workflowsid = '792623';
            }
            $on_focus = CRMEntity::getInstance('Vendors');
            $on_focus->makeWorkflows('Vendors', $workflowsid, $recordId, 'edit');
            $sql = "update vtiger_vendor set workflowsid=?,modulestatus='b_actioning' where vendorid=?";
            $db->pquery($sql, array($workflowsid, $recordId));
            if($recordModel->get('vendortype')!='MediaProvider') {
                global $current_user;
                $query = "SELECT vtiger_auditsettings.oneaudituid,vtiger_auditsettings.towaudituid,vtiger_auditsettings.audituid3 FROM`vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$current_user->departmentid}') AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='VendorsAuditset' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1";
                $resultAuditSettings = $db->pquery($query, array());
                $oneaudituid = $db->query_result($resultAuditSettings, 0, 'oneaudituid');
                $towaudituid = $db->query_result($resultAuditSettings, 0, 'towaudituid');
                if ($oneaudituid == $towaudituid) {//当前第一审核人与第二审核人是同一人，则第二个节点关闭直接跳到第三个工作流
                    $db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=1 AND vtiger_salesorderworkflowstages.modulename='Vendors'", array($oneaudituid, $recordId));//第1个节点激活
                    $db->pquery("DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND sequence=2 AND modulename='Vendors'", array($recordId));//第三个节点激活
                } else {
                    $db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=1 AND vtiger_salesorderworkflowstages.modulename='Vendors'", array($oneaudituid, $recordId));//第1个节点激活
                    $db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=2 AND vtiger_salesorderworkflowstages.modulename='Vendors'", array($towaudituid, $recordId));
                }
            }
            //新建时 消息提醒第一审核人进行审核
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$recordId,'salesorderworkflowstagesid'=>0));
            $resultMsg=array("success"=>true,"msg"=>"OK");
        }while(0);

        $response = new Vtiger_Response();
        $response->setResult($resultMsg);
        $response->emit();
    }


    public function setAllowtransaction(Vtiger_Request $request) {
        $record = $request->get('record');
        $status = $request->get('status');

        // 这里还有权限 没有写
        $db = PearDatabase::getInstance();


        // 判断有否id
        $sql = "select vendorid,allowtransaction from  vtiger_vendor where vendorid=?";
        $sel_result = $db->pquery($sql, array($record));
        $res_cnt = $db->num_rows($sel_result);
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);

            $sql = "update vtiger_vendor set allowtransaction=? where vendorid=?";
            $db->pquery($sql, array($status, $record));


            // 做更新记录
            global $current_user;
            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                            array($id, $record, 'Vendors', $current_user->id, date('Y-m-d H:i:s'), 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'allowtransaction', $row['allowtransaction'], $status));
        }



        $data = array();
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    public function setVendorState(Vtiger_Request $request) {
        $record = $request->get('record');
        $status = $request->get('status');

        // 这里还有权限 没有写
        $db = PearDatabase::getInstance();

        // 判断有否id
        $sql = "select vendorid,vendorstate from  vtiger_vendor where vendorid=?";
        $sel_result = $db->pquery($sql, array($record));
        $res_cnt = $db->num_rows($sel_result);
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);

            $sql = "update vtiger_vendor set vendorstate=? where vendorid=?";
            $db->pquery($sql, array($status, $record));


            // 做更新记录
            global $current_user;
            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                            array($id, $record, 'Vendors', $current_user->id, date('Y-m-d H:i:s'), 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'vendorstate', $row['vendorstate'], $status));
        }

        $data = array();
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    // 删除附件
    function files_delete(Vtiger_Request $request) {
        $fileid = $request->get('record');
        $vendorid = $request->get('srecorId');
        global $current_user;
        $recordModel=Vtiger_Record_Model::getInstanceById($vendorid,'Vendors');
        $column_fields=$recordModel->entity->column_fields;
        $modulestatus=array('c_complete');
        $dostatus=array('a_normal','a_exception');
//        if(in_array($column_fields['modulestatus'],$dostatus) || (in_array($column_fields['modulestatus'],$modulestatus)&& is_custompowers('scontractsFilesDelete')) ) {
            $sql = "update vtiger_files set deleter=?,delflag=1 where attachmentsid=?";
            $adb = PearDatabase::getInstance();
            $adb->pquery($sql, array($current_user->id, $fileid));
//        }
        $data = array();
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    // 附件签收
    function files_deliver(Vtiger_Request $request) {
        $fileid = $request->get('record');
        global $current_user;

        $sql = "update vtiger_files set deliversuserid=?,filestate=?, delivertime=? where attachmentsid=?";
        $adb = PearDatabase::getInstance();

        $data = array($current_user->id,'filestate2', date('Y-m-d H:i:s'), $fileid);
        $adb->pquery($sql, $data);

        $data = array();
        $data['last_name'] = $current_user->last_name."[". $current_user->department ."]";
        $data['delivertime'] = date('Y-m-d H:i:s');

        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    // 审核权限添加
    function addAuditsettings(Vtiger_Request $request) {
        $auditsettingtype = $request->get("auditsettingtype");
        $department = $request->get("department");
        $oneaudituid = $request->get("oneaudituid");
        $towaudituid = $request->get("towaudituid");
        $threeaudituid = $request->get("threeaudituid");
        $data = array('flag'=>'0', 'msg'=>'添加失败');
        do {
            $moduleModel = Vtiger_Module_Model::getInstance('Vendors');
            if(!$moduleModel->exportGrouprt('Vendors','dempartConfirm')){   //权限验证
                break;
            }
            if (empty($auditsettingtype)) {
                break;
            }
            if (empty($department)) {
                break;
            }
            if (empty($oneaudituid)) {
                break;
            }
            if (empty($towaudituid)) {
                break;
            }
            /*if (empty($threeaudituid)) {
            	//break;
                $threeaudituid='';
            }*/
            $sql = "delete from vtiger_auditsettings where auditsettingtype=? AND department=? AND oneaudituid=? AND towaudituid=?";
            $sql2 = "INSERT INTO `vtiger_auditsettings` (`auditsettingsid`, `auditsettingtype`, `department`, `oneaudituid`, `towaudituid`,audituid3, `createtime`, `createid`) VALUES (NULL, ?, ?,?, ?, ?, ?, ?);";
            global $current_user;
            $db=PearDatabase::getInstance();
            $db->pquery($sql, array($auditsettingtype, $department, $oneaudituid, $towaudituid));
            $db->pquery($sql2, array($auditsettingtype, $department, $oneaudituid, $towaudituid,$threeaudituid, date('Y-m-d H:i:s'), $current_user->id));
            $data = array('flag'=>'1', 'msg'=>'添加成功');
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    function deletedAuditsettings(Vtiger_Request $request) {
        $moduleModel = Vtiger_Module_Model::getInstance('Vendors');
        if($moduleModel->exportGrouprt('Vendors','dempartConfirm')){   //权限验证
            global $current_user;
            $id=$request->get("id");
            $delsql="delete from vtiger_auditsettings where auditsettingsid=?";
            $db=PearDatabase::getInstance();
            $datetime=date('Y-m-d H:i:s');
            $db->pquery($delsql,array($id));
        }
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 检查供应商名称是否重复
     * @param Vtiger_Request $request
     */
    public function checkVendorName(Vtiger_Request $request){
        $recordModule=Vtiger_Record_Model::getCleanInstance('Vendors');
	    $data=$recordModule->checkVendorName($request);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 供应商评分
     */
    public function setVendorScore(Vtiger_Request $request){
        global $adb;
        $recordid=$request->get('recordid');
        $vendorscore=$request->get('vendorscore');
        $sql="UPDATE vtiger_vendor SET vendorscore=?,vendorscoredate=? WHERE vendorid=?";
        $date=date('Y-m-d');
        $adb->pquery($sql,array($vendorscore,$date,$recordid));
        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }


    //更改供应商负责人
    public function doChangeSmowner(Vtiger_Request $request){
        $recordid=$request->get('recordid');
        $newsmownerid=$request->get('newsmownerid');
        $recordModel = Vendors_Record_Model::getInstanceById($recordid,"Vendors");
        $data = $recordModel->doChangeSmowner($recordid,$newsmownerid);
        echo json_encode($data);
    }
}
