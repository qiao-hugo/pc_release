<?php
class VisitingOrder_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('saveVisitImprovement');
        $this->exposeMethod('saveSchedule');
        $this->exposeMethod('doRevoke');

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
                $Sql="UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE isaction=1 AND modulename='VisitingOrder' AND salesorderid=?";
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
                $recordModel->entity->makeWorkflows('VisitingOrder', $_REQUEST['workflowsid'], $recordId,false);
                $Sql="UPDATE vtiger_visitingorder SET modulestatus='c_canceling' WHERE visitingorderid=?";
                $adb->pquery($Sql,array($recordId));
                $Sql="UPDATE vtiger_salesorderworkflowstages SET workflowstagesname='提单人上级作废审核' WHERE isaction=1 AND modulename='VisitingOrder' AND salesorderid=? AND workflowsid=?";
                $adb->pquery($Sql,array($recordId,$_REQUEST['workflowsid']));
            }
        }
    }
}
