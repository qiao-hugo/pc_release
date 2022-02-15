<?php
class AccountPlatform_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('checkIdAndAccountplatform');
        $this->exposeMethod('Resubmit');
        $this->exposeMethod('getVendorInfo');
        $this->exposeMethod('changeStatus');
        $this->exposeMethod('deleteDetailOne');
        $this->exposeMethod('updateDetailOne');
        $this->exposeMethod('checkRepate');
        $this->exposeMethod('updateDetailBatch');
    }



	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
	}
	public function checkIdAndAccountplatform(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('AccountPlatform');
        $res['flag']=$recordModel->checkIdAndAccountplatform($request);
        $response = new Vtiger_Response();
        $response->setResult($res);
        $response->emit();
    }
    public function Resubmit(Vtiger_Request $request){
	    $recordModel=Vtiger_Record_Model::getInstanceById($request->get('record'),'AccountPlatform');
        $column_fields=$recordModel->entity->column_fields;
        global $current_user;
        if($column_fields['modulestatus']=='c_complete'){
            if($column_fields['assigned_user_id'] == $current_user->id ||$current_user->is_admin=='on' || $recordModel->personalAuthority('AccountPlatform',"doedit")){
                $recordModel->doResubmit($request);
                echo "打回成功";
            }else{
                try {
                    $accountRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get('accountid'), "Accounts");
                    if ($accountRecordModel->get('assigned_user_id') == $current_user->id) {
                        $recordModel->doResubmit($request);
                        echo "打回成功";
                    } else {
                        echo "无权操作";
                    }
                }catch(Exception $e){
                    echo "无权操作";
                }
            }
        }else{
            echo "无权操作";
        }
    }
    public function getVendorInfo(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('AccountPlatform');
        $data=$recordModel->getVendorInfos($request);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function changeStatus(Vtiger_Request $request){
        $record=$request->get('record');
        try{
            Vtiger_Record_Model::getInstanceById($record,'AccountPlatform');
            global $adb;
            $adb->pquery('UPDATE vtiger_accountplatform SET isforbidden=abs(isforbidden-1) WHERE accountplatformid=?',array($record));
        }catch(Exception $e){

        }
        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    //删除操作
    public function deleteDetailOne(Vtiger_Request $request){
        global $adb;
        $accountplatform_detail_id=$request->get("id");
        $record=$request->get("record");
        $sql=" DELETE FROM vtiger_accountplatform_detail WHERE accountplatform_detail_id=? AND accountplatformid=? ";
        $adb->pquery($sql,array($accountplatform_detail_id,$record));
        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    // 更新一条 或者插入一条
    public  function updateDetailOne(Vtiger_Request $request){
        global $adb,$current_user;
        $curren_time=date("Y-m-d");
        $accountplatform_detail_id=$request->get("id");
        $idaccount=$request->get("idaccount");
        $accountplatform=$request->get("accountplatform");
        $recordID=$request->get("record");
        $param['record']=$recordID;
        $param['module']='AccountPlatform';
        $param['userid']=$current_user->id;
        $param['status']=0;
        $param['strArray']=array();
        $result=$adb->pquery("SELECT 1 FROM `vtiger_accountplatform` LEFT JOIN vtiger_crmentity ON vtiger_accountplatform.accountplatformid=vtiger_crmentity.crmid LEFT JOIN vtiger_accountplatform_detail ON vtiger_accountplatform_detail.accountplatformid=vtiger_accountplatform.accountplatformid WHERE vtiger_crmentity.deleted=0 AND vtiger_accountplatform_detail.idaccount=?  AND vtiger_accountplatform_detail.accountplatformid<>?",array($idaccount,$recordID));


        if ($adb->num_rows($result) > 0) {
            $data = array("success" => false, "message" => "当前ID,账号重复不允许添加!!");
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
            exit();
        }
        // 如果原数据id存在则 更新
        if ($accountplatform_detail_id) {
            $result=$adb->pquery("SELECT * FROM vtiger_accountplatform_detail WHERE idaccount=?  AND accountplatform_detail_id<>? limit 1",array($idaccount,$accountplatform_detail_id));
            if ($adb->num_rows($result) > 0) {
                $data = array("success" => false, "message" => "当前ID,账号重复不允许添加!!");
                $response = new Vtiger_Response();
                $response->setResult($data);
                $response->emit();
                exit();
            }
            $sql = "SELECT * FROM vtiger_accountplatform_detail WHERE  accountplatform_detail_id=? limit 1";
            $result = $adb->pquery($sql, array($accountplatform_detail_id));
            $result = $adb->query_result_rowdata($result, 0);
            if ($result['idaccount'] != $idaccount) {
                $param['strArray'][0]['fieldname'] = 'idaccount';
                $param['strArray'][0]['prevalue'] = $result['idaccount'];
                $param['strArray'][0]['postvalue'] = $idaccount;
            }
            if ($result['accountplatform'] != $accountplatform) {
                $param['strArray'][1]['fieldname'] = 'accountplatform';
                $param['strArray'][1]['prevalue'] = $result['accountplatform'];
                $param['strArray'][1]['postvalue'] = $accountplatform;
            }
            if (!empty($param['strArray'])) {
                $recordModel = Vtiger_Record_Model::getCleanInstance("ProductProvider");
                $recordModel->addLogs($param);
                $update = " UPDATE vtiger_accountplatform_detail SET idaccount=?,accountplatform=?,updatetime=? WHERE accountplatform_detail_id=? ";
                $adb->pquery($update, array($idaccount, $accountplatform, $curren_time, $accountplatform_detail_id));
            }
            //走插入
        } else {
            $result=$adb->pquery("SELECT * FROM vtiger_accountplatform_detail WHERE idaccount=? limit 1",array($idaccount));
            if ($adb->num_rows($result) > 0) {
                $data = array("success" => false, "message" => "当前ID,账号重复不允许添加!!");
                $response = new Vtiger_Response();
                $response->setResult($data);
                $response->emit();
                exit();
            }
            $param['strArray'][0]['fieldname']='idaccount';
            $param['strArray'][0]['prevalue']=null;
            $param['strArray'][0]['postvalue']=$idaccount;
            $param['strArray'][1]['fieldname']='accountplatform';
            $param['strArray'][1]['prevalue']=null;
            $param['strArray'][1]['postvalue']=$accountplatform;
            $recordModel= Vtiger_Record_Model::getCleanInstance("ProductProvider");
            $recordModel->addLogs($param);
            $insert = "INSERT INTO vtiger_accountplatform_detail (`accountplatformid`, `idaccount`, `accountplatform`, `createtime`, `updatetime`) values(?,?,?,?,?)";
            $adb->pquery($insert, array($recordID, $idaccount, $accountplatform, $curren_time, $curren_time));
        }

        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    //  验证不能重复
    public function checkRepate(Vtiger_Request $request){
        global $adb;
        $data=array();
        $idaccount=$request->get("idaccount");
        $recordId=$request->get("record");
        $result=$adb->pquery("SELECT 1 FROM `vtiger_accountplatform` LEFT JOIN vtiger_crmentity ON vtiger_accountplatform.accountplatformid=vtiger_crmentity.crmid LEFT JOIN vtiger_accountplatform_detail ON vtiger_accountplatform_detail.accountplatformid=vtiger_accountplatform.accountplatformid WHERE vtiger_crmentity.deleted=0 AND vtiger_accountplatform_detail.idaccount=?  AND vtiger_accountplatform_detail.accountplatformid<>?",array($idaccount,$recordId));
        if($adb->num_rows($result)>0){
            $data=array("success"=>false,"message"=>"当前ID,账号重复不允许添加!!");
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /***
     * 批量添加或删除
     * @param Vtiger_Request $request
     * @throws Exception
     */
    function updateDetailBatch(Vtiger_Request $request){
        global $adb,$current_user;
        $curren_time=date("Y-m-d");
        $accountplatformJson=$request->get("detailJson");
        $data=array('success'=>true);
        foreach ($accountplatformJson as $accountplatform){
            $accountplatform_detail_id=$accountplatform['id'];
            $idaccount=$accountplatform['idaccount'];
            $accountplatform=$accountplatform['accountplatform'];
            $recordId=$request->get("record");
            $param['record']=$recordId;
            $param['module']='AccountPlatform';
            $param['userid']=$current_user->id;
            $param['status']=0;
            $param['strArray']=array();
            $result=$adb->pquery("SELECT 1 FROM `vtiger_accountplatform` LEFT JOIN vtiger_crmentity ON vtiger_accountplatform.accountplatformid=vtiger_crmentity.crmid LEFT JOIN vtiger_accountplatform_detail ON vtiger_accountplatform_detail.accountplatformid=vtiger_accountplatform.accountplatformid WHERE vtiger_crmentity.deleted=0 AND vtiger_accountplatform_detail.idaccount=?  AND vtiger_accountplatform_detail.accountplatformid<>?",array($idaccount,$recordId));
            if ($adb->num_rows($result) > 0) {
                $data['success']=false;
                $data['message'][]=array('idaccount'=>$idaccount,"message" => "当前ID,账号重复不允许添加!!");
                continue;
            }
            // 如果原数据id存在则 更新
            if ($accountplatform_detail_id) {
                $result=$adb->pquery("SELECT * FROM vtiger_accountplatform_detail WHERE idaccount=?  AND accountplatform_detail_id<>? limit 1",array($idaccount,$accountplatform_detail_id));
                if ($adb->num_rows($result) > 0) {
                    $data['success']=false;
                    $data['message'][]=array('idaccount'=>$idaccount,"message" => "当前ID,账号重复不允许添加!!");
                    continue;
                }
                $sql = "SELECT * FROM vtiger_accountplatform_detail WHERE  accountplatform_detail_id=? limit 1";
                $result = $adb->pquery($sql, array($accountplatform_detail_id));
                $result = $adb->query_result_rowdata($result, 0);
                if ($result['idaccount'] != $idaccount) {
                    $param['strArray'][0]['fieldname'] = 'idaccount';
                    $param['strArray'][0]['prevalue'] = $result['idaccount'];
                    $param['strArray'][0]['postvalue'] = $idaccount;
                }
                if ($result['accountplatform'] != $accountplatform) {
                    $param['strArray'][1]['fieldname'] = 'accountplatform';
                    $param['strArray'][1]['prevalue'] = $result['accountplatform'];
                    $param['strArray'][1]['postvalue'] = $accountplatform;
                }
                if (!empty($param['strArray'])) {
                    $recordModel = Vtiger_Record_Model::getCleanInstance("ProductProvider");
                    $recordModel->addLogs($param);
                    $update = " UPDATE vtiger_accountplatform_detail SET idaccount=?,accountplatform=?,updatetime=? WHERE accountplatform_detail_id=? ";
                    $adb->pquery($update, array($idaccount, $accountplatform, $curren_time, $accountplatform_detail_id));
                }
                //走插入
            } else {
                $result=$adb->pquery("SELECT * FROM vtiger_accountplatform_detail WHERE idaccount=? limit 1",array($idaccount));
                if ($adb->num_rows($result) > 0) {
                    $data['success']=false;
                    $data['message'][]=array('idaccount'=>$idaccount,"message" => "当前ID,账号重复不允许添加!!");
                    continue;
                }
                $param['strArray'][0]['fieldname']='idaccount';
                $param['strArray'][0]['prevalue']=null;
                $param['strArray'][0]['postvalue']=$idaccount;
                $param['strArray'][1]['fieldname']='accountplatform';
                $param['strArray'][1]['prevalue']=null;
                $param['strArray'][1]['postvalue']=$accountplatform;
                $recordModel= Vtiger_Record_Model::getCleanInstance("ProductProvider");
                $recordModel->addLogs($param);
                $insert = "INSERT INTO vtiger_accountplatform_detail (`accountplatformid`, `idaccount`, `accountplatform`, `createtime`, `updatetime`) values(?,?,?,?,?)";
                $adb->pquery($insert, array($recordId, $idaccount, $accountplatform, $curren_time, $curren_time));
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
