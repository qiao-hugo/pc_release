<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Workflows_BasicAjax_Action extends Vtiger_BasicAjax_Action {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('savecompanycodeuserid');
        $this->exposeMethod('deletedInvoiceCompany');
        $this->exposeMethod('deletedInvoiceCompanyUser');
        $this->exposeMethod('saveCWSH');
        $this->exposeMethod('deletedCWSH');
    }
    function checkPermission(Vtiger_Request $request) {
        return;
    }

    public function process(Vtiger_Request $request) {
        $moduleName = $request->get('module');
        $recordId = $request->get('record');

        if(!empty($recordId)){
            $module = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
            $result=array();

            $response = new Vtiger_Response();
            $response->setResult($module->getData());
            $response->emit();
        }else{
            $mode = $request->getMode();
            if(!empty($mode)) {
                echo $this->invokeExposedMethod($mode, $request);
                return;
            }
            parent::process($request);
        }
    }

    /**
     * 设置审核人
     * @param Vtiger_Request $request
     */
    public function savecompanycodeuserid(Vtiger_Request $request) {
        $companycode= $request->get('companycode');
        $userid=$request->get('userid');
        $workflowstagesflag=$request->get('workflowstagesflag');
        do{
            if(empty($companycode)){
                $data = ['status'=>'error', 'msg'=>'主体公司不能为空'];
                break;
            }
            if(empty($userid)){
                $data = ['status'=>'error', 'msg'=>'人员不能为空'];
                break;
            }
            if(empty($workflowstagesflag)){
                $data = ['status'=>'error', 'msg'=>'节点标识不能为空'];
                break;
            }
            $db = PearDatabase::getInstance();
            foreach($companycode as $value){
                $db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1, higherid=? WHERE isaction in(0,1) AND handleaction='maincompany' AND workflowstagesflag=? AND companycode=?",
                    array($userid,$workflowstagesflag,$value));
                $db->pquery("DELETE FROM vtiger_auditinvoicecompany WHERE workflowstagesflag=? AND companycode=?",
                    array($workflowstagesflag,$value));
                $db->pquery('INSERT INTO `vtiger_auditinvoicecompany`(`workflowstagesflag`, `userid`, `companycode`) VALUES (?,?,?)',
                    array($workflowstagesflag,$userid,$value));
            }
            $data = ['status'=>'success', 'msg'=>'成功添加审核人'];
        }while(0);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($data);
        $response->emit();
    }
    
    /**
     * 删除审核人
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function deletedInvoiceCompanyUser(Vtiger_Request $request){
        $id=$request->get('id');
        $db = PearDatabase::getInstance();
        $sql='DELETE FROM vtiger_auditinvoicecompany WHERE auditinvoicecompanyid=?';
        $db->pquery($sql,array($id));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }

    /**
     * 保存CWSH
     * @param Vtiger_Request $request
     */
    public function saveCWSH(Vtiger_Request $request){
        $department=$request->get('department');
        $supervisor=$request->get('supervisor');
        $manager=$request->get('manager');
        $db = PearDatabase::getInstance();
        $sql="select * from vtiger_auditCWSH where department=?";
        $result=$db->pquery($sql,array($department));
        if($db->num_rows($result)){
            $rs['flag']=false;
            $rs['msg']='已存在此部门的数据';
        }else{
            $rs['flag']=true;
            $data['workflowstagesflag']='CWSH';
            $data['department']=$department;
            $data['supervisor']=$supervisor;
            $data['manager']=$manager;
            $db->run_insert_data('vtiger_auditCWSH',$data);
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($rs);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     */
    public function deletedCWSH(Vtiger_Request $request){
        $id=$request->get('id');
        $db = PearDatabase::getInstance();
        $sql='DELETE FROM vtiger_auditCWSH WHERE auditCWSHid=?';
        $db->pquery($sql,array($id));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }
}
