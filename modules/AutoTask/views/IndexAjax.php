<?php
/*+***********************************************************************************
 * 上海珍岛信息技术有限公司CRM
 *************************************************************************************/

class AutoTask_IndexAjax_View extends Vtiger_IndexAjax_View {

	function __construct() {
		parent::__construct();
        $this->exposeMethod('showWorkflow');
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}


    /**
     * 显示流程图
     * @param Vtiger_Request $request
     */
    public function showWorkflow(Vtiger_Request $request){
        global $adb;
        $viewer = $this->getViewer($request);
        $record=$request->get('source_record');
        //读取当前流程下的所有的节点
        $sql = 'SELECT * FROM  vtiger_autoworkflowtaskentitys WHERE autoworkflowid=?';
        $result = $adb->pquery($sql,array($record));
        $arrModel=array();
        if($adb->num_rows($result)>0){
            // $strTasks='{"total":'.$adb->num_rows($result).',"list":[';
            while($row=$adb->fetch_array($result)){
                $arrModel[]=$row;
            }
        }
        $viewer->assign('MODULE_MODLE',$arrModel);
        $viewer->assign('FLOWID',$record);
        $viewer->view('showworkflow.tpl','AutoTask');
    }

    /**
     * 审核任务节点
     * @param Vtiger_Request $request
     */
    public function showTaskCheck(Vtiger_Request $request){
        return 0;
    }

}