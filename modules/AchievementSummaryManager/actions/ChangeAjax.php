<?php
class AchievementSummaryManager_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('confirmEnd');
        $this->exposeMethod('cancelConfirmEnd');
        $this->exposeMethod('applicationUpdateDate');
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
     * 申请调整业绩金额
     */
    public function applicationUpdateDate(Vtiger_Request $request){
        global $adb;
        $date=$request->get("date");
        $remarks=$request->get("remarks");
        $record=$request->get("record");
        do{
            $sql=" SELECT * FROM  vtiger_closingdate WHERE id=? LIMIT 1 ";
            $detailInfo = $adb->pquery($sql,array($record));
            $result=$adb->query_result_rowdata($detailInfo,0);
            if($result['modulestatus']=='b_actioning'){
                $result=array('success'=>0,'message'=>"状态审核中不能调整业绩！");
                break;
            }
            $recordModel=Vtiger_Record_Model::getInstanceById($record,'ClosingDate');
            $sql=" UPDATE `vtiger_closingdate` SET `recorddate`=?,remarks=? WHERE (`id`=?) LIMIT 1 ";
            $adb->pquery($sql,array($date,$remarks,$record));

            //本地测试工作流
            $workflowsid=2426441;
            //先删除已经生成的工作流
            $sql=" DELETE  FROM  vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowsid=? ";
            $adb->pquery($sql,array($record,$workflowsid));
            $recordModel->entity->makeWorkflows('ClosingDate', $workflowsid,$record,false);
            //更新日志记录
            $currentTime = date('Y-m-d H:i:s');
            global $current_user;
            //更新记录
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $record, 'ClosingDate', $current_user->id,$currentTime, 0));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?),(?,?,?,?)',
                Array($id,'date',"每月".$result['date']."号","每月".$date."号",$id,'modulestatus','','b_actioning',$id,'remarks','',$remarks));
            $result=array('success'=>1,'message'=>"已申请");
        }while(false);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
