<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Businesstrend_List_View extends Vtiger_KList_View {
		function __construct() {
		parent::__construct();
	}
	
	public function initial4444izeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
	    
	}
	
    function process(Vtiger_Request $request){
        $this->listViewHeaders=123;
        $moduleName = $request->getModule();
        $viewer = $this->getViewer ($request);
        include "crmcache/departmentanduserinfo.php";
        $where=getAccessibleUsers('Businesstrend','List',false);
        if($where!='1=1'){
            $str =' AND id '.$where;
        }else{
            $str='';
        }
        $viewer->assign('USERDEPARTMENT',Rsalesananalysis_Record_Model::getuserinfo($str));
        $viewer->assign('USERYEARS',Rsalesananalysis_Record_Model::getyears());
        $viewer->view('ListExpand.tpl', $moduleName);
    }
}