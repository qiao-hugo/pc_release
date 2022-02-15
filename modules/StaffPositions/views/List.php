<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class StaffPositions_List_View extends Vtiger_KList_View {
		function __construct() {
		parent::__construct();
	}
	
	public function initial4444izeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
	    
	}
	
    function process(Vtiger_Request $request){
        $this->listViewHeaders=123;
        $db = PearDatabase::getInstance();
        $smallsql = "SELECT last_name,id,title,department FROM vtiger_users WHERE vtiger_users.`status`='Active' order by department";
        $recordInstances = array();
        $result = $db->pquery($smallsql,array());
        $num=$db->num_rows($result);
       if ($db->num_rows($result)>0){
           for ($i=0; $i<$num; $i++) {
               $recordInstances[] = $db->query_result_rowdata($result, $i);
           }
       }
        //print_r($recordInstances);
        //exit;
      // var_dump($recordInstances);die;
       $moduleName = $request->getModule();
       $viewer = $this->getViewer ($request);
       $viewer->assign('SMALLUSER', $recordInstances);
       $viewer->view('ListExpand.tpl', $moduleName);
    }

}