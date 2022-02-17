<?php
class VisitDepartment_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('saveVisitImprovement');
        $this->exposeMethod('saveSchedule');

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
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'VisitDepartment');
        $isimprovement=$recordModel->entity->column_fields['isimprovement'];

        if($isimprovement==0){
            $datetime=date('Y-m-d H:i:s');
            $Sql='INSERT INTO vtiger_visitimprovement(visitingorderid,`module`,userid,datetime,remark) VALUES(?,?,?,?,?)';
            $adb->pquery($Sql,array($recordId,'VisitDepartment',$current_user->id,$datetime,$remark));
            $Sql='UPDATE vtiger_visitdepartment SET improvement=?,isimprovement=1 WHERE visitdepartmentid=?';
            $adb->pquery($Sql,array($remark,$recordId));
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
        $datetime=date('Y-m-d H:i:s');
        $Sql='INSERT INTO vtiger_improveschedule(visitimprovementid,module,`schedule`,userid,createdtime,remark) VALUES(?,?,?,?,?,?)';
        $adb->pquery($Sql,array($dataid,'VisitDepartment',$schedule,$current_user->id,$datetime,$remark,));
        $Sql='UPDATE `vtiger_visitimprovement` SET `schedule`=? WHERE visitimprovementid=?';
        $adb->pquery($Sql,array($schedule,$dataid));
        $Sql='UPDATE vtiger_visitdepartment SET `schedule`=?,scheduleremark=? WHERE visitdepartmentid=?';
        $adb->pquery($Sql,array($schedule,$remark,$recordId));

    }

}
