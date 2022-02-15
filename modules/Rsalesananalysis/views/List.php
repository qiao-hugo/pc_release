<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Rsalesananalysis_List_View extends Vtiger_KList_View {
		function __construct() {
		parent::__construct();
	}
	
	public function initial4444izeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
	    
	}
	
    function process(Vtiger_Request $request){
        $this->listViewHeaders=123;
       /* $db = PearDatabase::getInstance();
        $smallsql = "SELECT vtiger_users.id, vtiger_users.last_name, ( SELECT roleid FROM vtiger_user2role WHERE userid = id LIMIT 1 ) AS roleid, ( SELECT rolename FROM vtiger_role WHERE roleid = ( SELECT roleid FROM vtiger_user2role WHERE userid = vtiger_users.id LIMIT 1 )) AS rolename, ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) AS departmentid, ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )) AS departmentname, ( SELECT parentdepartment FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )) AS parentdepartment_orderby, vtiger_users.department, vtiger_users.user_sys FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_departments.departmentid = vtiger_user2department.departmentid WHERE vtiger_users.id > 0 AND vtiger_departments.parentdepartment LIKE 'H1::H2::H3::%' AND vtiger_users. STATUS = 'Active' ORDER BY parentdepartment_orderby";
        $recordInstances = array();
        $result = $db->pquery($smallsql,array());
        if ($db->num_rows($result)>0){
           for ($i=0; $i<$db->num_rows($result); $i++) {
               $row = $db->query_result_rowdata($result, $i);
               if($row['departmentid'] !=='H24'){
                   $recordInstances[$row['departmentname']][]= array('name'=>$row['last_name'],'roleid'=>$row['roleid'],'role'=>$row['rolename'],'status'=>$row['status'],'department'=>$row['departmentname']);
               }
           }
        }*/
        // var_dump($recordInstances);die;
        $moduleName = $request->getModule();
        $viewer = $this->getViewer ($request);
        include "crmcache/departmentanduserinfo.php";
        $where=getAccessibleUsers('Rsalesananalysis','List',false);
        if($where!='1=1'){
            $str =' AND id '.$where;
        }else{
            $str='';
        }
        //$str=' AND id in('.$user2departmentinfo['H1'].')';
        //print_r(Reporting_Record_Model::getuserinfo($str));
        $viewer->assign('USERDEPARTMENT',Rsalesananalysis_Record_Model::getuserinfo($str));
        $viewer->assign('USERYEARS',Rsalesananalysis_Record_Model::getyears());
        $viewer->assign('DEPARTMENTUSER',$departlevel);
        //$viewer->assign('SMALLUSER', $recordInstances);
        $viewer->view('ListExpand.tpl', $moduleName);
    }
}