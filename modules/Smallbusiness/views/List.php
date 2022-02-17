<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Smallbusiness_List_View extends Vtiger_KList_View {
		function __construct() {
		parent::__construct();
	}
	
	public function initial4444izeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
	    
	}
	
    function process(Vtiger_Request $request){
        $this->listViewHeaders=123;
        $db = PearDatabase::getInstance();
        $smallsql = "SELECT vtiger_users.id, vtiger_users.last_name, ( SELECT roleid FROM vtiger_user2role WHERE userid = id LIMIT 1 ) AS roleid, ( SELECT rolename FROM vtiger_role WHERE roleid = ( SELECT roleid FROM vtiger_user2role WHERE userid = vtiger_users.id LIMIT 1 )) AS rolename, ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) AS departmentid, ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )) AS departmentname, ( SELECT parentdepartment FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )) AS parentdepartment_orderby, vtiger_users.department, vtiger_users.user_sys FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_departments.departmentid = vtiger_user2department.departmentid WHERE vtiger_users.id > 0 AND vtiger_departments.parentdepartment LIKE 'H1::H3::%' AND vtiger_users. STATUS = 'Active' ORDER BY parentdepartment_orderby";
        $recordInstances = array();
       $result = $db->pquery($smallsql,array());
       if ($db->num_rows($result)>0){
           for ($i=0; $i<$db->num_rows($result); $i++) {
               $row = $db->query_result_rowdata($result, $i);
               if($row['departmentid'] !=='H24'){
                   $recordInstances[$row['departmentname']][]= array('name'=>$row['last_name'],'roleid'=>$row['roleid'],'role'=>$row['rolename'],'status'=>$row['status'],'department'=>$row['departmentname']);
               }
           }
       }
      // var_dump($recordInstances);die;
       $moduleName = $request->getModule();
       $viewer = $this->getViewer ($request);
       $viewer->assign('SMALLUSER', $recordInstances);
       $viewer->assign('SMALLUSERMONTH', $this->getcurrentuser());
       $viewer->view('ListExpand.tpl', $moduleName);
    }
    public function getcurrentuser(){
        $db = PearDatabase::getInstance();
        $smallsql = "SELECT vtiger_users.id, vtiger_users.last_name, ( SELECT roleid FROM vtiger_user2role WHERE userid = id LIMIT 1 ) AS roleid, ( SELECT rolename FROM vtiger_role WHERE roleid = ( SELECT roleid FROM vtiger_user2role WHERE userid = vtiger_users.id LIMIT 1 )) AS rolename, ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) AS departmentid, ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )) AS departmentname, ( SELECT parentdepartment FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )) AS parentdepartment_orderby, vtiger_users.department, vtiger_users.user_sys,vtiger_useractivemonth.activedate FROM vtiger_useractivemonth LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_useractivemonth.userid LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_departments.departmentid = vtiger_user2department.departmentid WHERE vtiger_users.id > 0 AND vtiger_departments.parentdepartment LIKE 'H1::H3::%' ORDER BY parentdepartment_orderby,vtiger_useractivemonth.activedate";
        $recordInstances = array();
        $result=$db->run_query_allrecords($smallsql);
        foreach($result as $values){
            if($values['departmentname']=='客户服务部'){
                continue;
            }
            $recordInstances[$values['departmentid']]['departmentname']=$values['departmentname'];
            $tempvalue='<span class="span2" style="margin-left:0px;"><span class="';
            if(strpos($values['rolename'], '商务总监')!==false){
                $tempvalue.='label label-success';
            }elseif(strpos($values['rolename'], '商务经理')!==false){
                $tempvalue.='label label-warning';
            }elseif(strpos($values['rolename'], '商务主管')!==false){
                $tempvalue.='label label-important';
            }elseif(strpos($values['rolename'], '商务助理')!==false){
                $tempvalue.='label label-info';
            }
            $tempvalue.='">'.$values['last_name'].'</span></span>';
            $recordInstances[$values['departmentid']][$values['activedate']][]=$tempvalue;
        }
        return $recordInstances;
    }
}