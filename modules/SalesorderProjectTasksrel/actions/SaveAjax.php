<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class SalesorderProjectTasksrel_SaveAjax_Action extends Vtiger_SaveAjax_Action {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('autogeneration');
		
	}
	
	public function process(Vtiger_Request $request) {
	    $mode = $request->get('mode');
	    if($mode =='autogeneration'){
	        $this->autogeneration($request);
	        return;
	    }
	    //parent::process($request);
	
	}
	/* public function checkPermission(Vtiger_Request $request) {
	    
	    echo 4444;die;
	} */
    public function autogeneration(Vtiger_Request $request){
        $module=$request->get('module');
        $projectid=$request->get('projectid');//模板id
        $salesorderid=$request->get('record'); //工单id
//echo $salesorderid;
        
        $db = PearDatabase::getInstance();
        $db->startTransaction();
        
        $sqlprojecttask = "SELECT * FROM `vtiger_projecttask` tsk INNER JOIN vtiger_crmentity crm ON tsk.projecttaskid=crm.crmid WHERE projectid=?";
        $projects = $db->pquery($sqlprojecttask,array($projectid));

        $sqlproject = "SELECT vtiger_crmentity.smownerid FROM `vtiger_project`,`vtiger_crmentity` WHERE vtiger_project.projectid = vtiger_crmentity.crmid AND vtiger_project.projectid = ?";
        $smownerid = $db->pquery($sqlproject,array($projectid));
        $smownerid = $db->query_result($smownerid, 0,'0');  //当前工单任务所属人
        //var_dump($projects);
        
       $insertsql = "INSERT INTO `vtiger_salesorderprojecttasksrel` (
                    	salesorderprojecttasksrelid,
                    	salesorderprojecttaskname,
                    	salesorderid,
                    	sequence,
                    	ownerid
                    )
                    VALUES
                    	(?,?,?,?,?)";
       //echo $insertsql;die;
        $num_rows = $db->num_rows($projects);
         if($num_rows>0){

            for ($i = 0; $i < $num_rows; $i++) {
                CRMEntity::insertIntoCrmEntity($module, $fileid = '');
            //echo $this->id;
                $columnname = array($this->id,$db->query_result($projects, $i,'projecttaskname'),$salesorderid,$db->query_result($projects, $i,'sort'),$smownerid);
            //var_dump($columnname);
                $db->pquery($insertsql,$columnname);
        }
       } 
       $db->completeTransaction();
       
       // 返回ajax
       $response = new Vtiger_Response();
       $response->setEmitType(Vtiger_Response::$EMIT_JSON);
       $response->setResult($num_rows);
       $response->emit();
    }	
}
