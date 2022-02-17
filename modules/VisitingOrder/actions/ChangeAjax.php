<?php
class VisitingOrder_ChangeAjax_Action extends Vtiger_Action_Controller {
    public $doAppealWorkflowsId=2426349;
    function __construct() {
        parent::__construct();
        $this->exposeMethod('saveVisitImprovement');
        $this->exposeMethod('saveSchedule');
        $this->exposeMethod('doRevoke');
        $this->exposeMethod('doAppeal');
	$this->exposeMethod('locationAddress');
        $this->exposeMethod('doSpecialCancel');
        $this->exposeMethod('showImg');

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

    /**
     * 添加改进意见
     * @param Vtiger_Request $request
     */
	public function saveVisitImprovement(Vtiger_Request $request){
        $recordId=$request->get('record');
        $remark=$request->get('remark');

        global $adb,$current_user;
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'VisitingOrder');
        $user=Users_Privileges_Model::getInstanceById($recordModel->entity->column_fields['extractid']);
        $where=getAccessibleUsers('VisitingOrder','List',true);
        if($where=='1=1' || in_array($user->reports_to_id,$where) || $current_user->is_admin=='on'){
            $datetime=date('Y-m-d H:i:s');
            $Sql='INSERT INTO vtiger_visitimprovement(visitingorderid,module,extractid,userid,datetime,remark) SELECT vtiger_visitingorder.visitingorderid,\'VisitingOrder\',vtiger_visitingorder.extractid,?,?,? FROM vtiger_visitingorder WHERE visitingorderid=?';
            $adb->pquery($Sql,array($current_user->id,$datetime,$remark,$recordId));
        }

    }

    /**
     * 添加改进意见进度
     * @param Vtiger_Request $request
     */
    public function saveSchedule(Vtiger_Request $request){
        $recordId=$request->get('record');
        $dataid=$request->get('dataid');
        $schedule=$request->get('schedule');
        $remark=$request->get('remark');
        global $adb,$current_user;
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'VisitingOrder');
        $user=Users_Privileges_Model::getInstanceById($recordModel->entity->column_fields['extractid']);
        $where=getAccessibleUsers('VisitingOrder','List',true);
        if($where=='1=1' || in_array($user->reports_to_id,$where) || $current_user->is_admin=='on'){
            $datetime=date('Y-m-d H:i:s');
            $Sql='INSERT INTO vtiger_improveschedule(visitimprovementid,module,`schedule`,userid,createdtime,remark) VALUES(?,?,?,?,?,?)';
            $adb->pquery($Sql,array($dataid,'VisitingOrder',$schedule,$current_user->id,$datetime,$remark));
        }
    }
    public function doRevoke(Vtiger_Request $request){
        $recordId=$request->get('record');
        global $adb,$current_user;
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'VisitingOrder');
        $columnFields=$recordModel->entity->column_fields;
        if($columnFields['extractid']==$current_user->id && $columnFields['issign']==0 && in_array($columnFields['modulestatus'],array('c_complete','a_normal'))){
            if($columnFields['modulestatus']=='a_normal'){
                $Sql="DELETE FROM vtiger_salesorderworkflowstages  WHERE isaction=1 AND modulename='VisitingOrder' AND salesorderid=?";
                $adb->pquery($Sql,array($recordId));
                $Sql="UPDATE vtiger_visitingorder SET modulestatus='c_cancel' WHERE visitingorderid=?";
                $adb->pquery($Sql,array($recordId));
                $id = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                    array($id, $recordId, 'VisitingOrder', $current_user->id, date('Y-m-d H:i:s'), 0));
                $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'modulestatus', 'a_normal', 'c_cancel'));
            }else{
                $_REQUEST['workflowsid']=828430;
                $recordModel->entity->makeWorkflows('VisitingOrder', $_REQUEST['workflowsid'], $recordId,'doRevoke');
                $Sql="UPDATE vtiger_visitingorder SET modulestatus='c_canceling' WHERE visitingorderid=?";
                $adb->pquery($Sql,array($recordId));
                
            }
        }
    }
    /**
     * 已拜访未签到申诉
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function doAppeal(Vtiger_Request $request){
        global $adb,$current_user;
        $recordId=$request->get('record');
        $visitsignid=$request->get('visitsignid');
        $remark=$request->get('remark');

        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'VisitingOrder',true);
        $columnFields=$recordModel->entity->column_fields;
        $returnData=array('success'=>false);
        do{
            if($columnFields['modulestatus']!='c_complete'){
                $returnData['msg']='拜访单未审核不允许申诉';
                break;
            }
            if(strtotime($columnFields['enddate'])>time()){
                $returnData['msg']='拜访单结束时间未过,不允许申诉';
                break;
            }
            if($columnFields['outobjective']!='出差'){
                // 获取拜访单的签到信息
                $sql = "SELECT
                            userid,isappeal,issign
                        FROM
                            vtiger_visitsign
                        WHERE
                            visitsignid = ? limit 1
                        ";
                $tablename='vtiger_visitsign';
            }else{
                // 获取拜访单的签到信息
                $sql = "SELECT
                            userid,isappeal,issign
                        FROM
                            vtiger_visitsign_mulit
                        WHERE
                            visitsignid = ? limit 1";
                $tablename='vtiger_visitsign_mulit';
            }
            $t_result = $adb->pquery($sql, array($visitsignid));
            $rawData=$adb->raw_query_result_rowdata($t_result,0);
            if($rawData['issign']!=0){
                $returnData['msg']='已签到不允许申诉';
                break;
            }
            if($rawData['isappeal']!=0){
                $returnData['msg']='不允许重复申诉';
                break;
            }
            if($current_user->id!=$rawData['userid']){
                $returnData['msg']='只有提单人才能申诉';
                break;
            }
            $query='UPDATE '.$tablename.' SET isappeal=1,reasonsforappeal=? WHERE visitsignid=?';
            $adb->pquery($query,array($remark,$visitsignid));
            $_REQUEST['workflowsid']=$this->doAppealWorkflowsId;
            $adb->pquery('DELETE FROM `vtiger_salesorderworkflowstages` WHERE workflowsid=? AND salesorderid=?',array($_REQUEST['workflowsid'],$recordId));
            $recordModel->entity->makeWorkflows('VisitingOrder', $_REQUEST['workflowsid'], $recordId,false);
            $Sql="UPDATE vtiger_visitingorder SET modulestatus='b_check' WHERE visitingorderid=?";
            $adb->pquery($Sql,array($recordId));
            $query="SELECT 1 FROM vtiger_departments WHERE departmentid=? AND FIND_IN_SET('H3',replace(parentdepartment,'::',','))";
            $result=$adb->pquery($query,array($current_user->departmentid));
            if($adb->num_rows($result)==0){//不是中小商务的提单人上级审核
                $adb->pquery('UPDATE vtiger_salesorderworkflowstages SET higherid=?,ishigher=1 WHERE workflowsid=? AND salesorderid=?',array($current_user->reports_to_id,$_REQUEST['workflowsid'],$recordId));
            }
            $returnData=array('success'=>true,'msg'=>'');
        }while(0);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($returnData);
        $response->emit();
    }
    
    public function locationAddress(Vtiger_Request $request){
        global $mapKey;
        $keyWord = $request->get("keyword");
        $url = "https://apis.map.qq.com/ws/place/v1/suggestion?keyword=".$keyWord."&region=全国&key=".$mapKey."&page_index=1&page_size=10";
        $data = $this->https_request($url);
        echo $data;
    }

    public function https_request($url, $data = null){
        $curl = curl_init();
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);

        //throw new Exception($curl);

        return $output;
    }

    public function doSpecialCancel(Vtiger_Request $request){
        global $adb,$current_user;
        $reject = $request->get('remark');
        $record = $request->get('record');
        $time = date("Y-m-d H:i:s");
        $userid = $current_user->id;
        $rejectname = '';
        $stagerecordid = 0;
        $modulename = 'VisitingOrder';
        $isbackname = '作废';
        //作废拜访单
        $adb->pquery("update vtiger_visitingorder set modulestatus='c_cancel'  where visitingorderid=?",array($record));
        $adb->pquery('insert into vtiger_salesorderhistory (`reject`,`salesorderid`,`rejecttime`,`rejectid`,`rejectname`,`workflowerstagesid`,`modulename`,`rejectnameto`) values(?,?,?,?,?,?,?,?)', array($reject,$record,$time,$userid,$rejectname,$stagerecordid,$modulename,$isbackname));
        $response = new Vtiger_Response();
        $response->setResult(array('success'=>1,'msg'=>'作废成功'));
        $response->emit();
    }

    /**
     * 移动端文件点击预览
     */
    public function showImg(Vtiger_Request $request){
        error_reporting(0);
        $fileid = $request->get('filename');
        $recordModel = VisitingOrder_Record_Model::getCleanInstance("VisitingOrder");
        $list = $recordModel->getFileImg($fileid);
        ob_clean();
        header("Content-type: ".$list[0][1]);
        header("Pragma: public");
        header("Cache-Control: private");
        $openfileArray=array('image/bmp','image/gif','image/jpeg','image/png','image/tiff','image/x-icon');
        if(!in_array($list[0][1],$openfileArray)) {//只有图片直接打开,其它的下载方式
            header("Content-Disposition: attachment; filename={$list[0][2]}");
        }
        echo base64_decode($list[0][3]);
        exit;
    }

}
