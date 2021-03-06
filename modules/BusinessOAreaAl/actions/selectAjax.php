<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * 
 *************************************************************************************/

class BusinessOAreaAl_selectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getCountsday');
        $this->exposeMethod('getdata');
        $this->exposeMethod('getnewlist');
        $this->exposeMethod('getconlist');
        $this->exposeMethod('getsinlist');
        $this->exposeMethod('getpaymentdetaillist');
        $this->exposeMethod('getUsers');
    }
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    public function process(Vtiger_Request $request) {
		$mode=$request->getMode();
        if(!empty($mode)){
            echo $this->invokeExposedMethod($mode,$request);
            exit;
        }
	}
    public function getdata(Vtiger_Request $request){
        global $current_user;
        $start = $request->get('datetime');
        $end   = $request->get('enddatetime');
        if(strtotime($start)>strtotime($end)){
            $tempdate=" AND TO_DAYS(vtiger_leaddetails.mapcreattime) BETWEEN TO_DAYS('{$start}') AND TO_DAYS('{$end}')";
        }elseif(strtotime($start)<strtotime($end)){
            $tempdate=" AND TO_DAYS(vtiger_leaddetails.mapcreattime) BETWEEN TO_DAYS('{$start}') AND TO_DAYS('{$end}')";
        }else{
            $tempdate=" AND TO_DAYS(vtiger_leaddetails.mapcreattime) =TO_DAYS('{$start}')";
        }
        $oriwhere_str =  getAccessibleUsers('BusinessOpportunities','List',false);
        $where='';
        if($oriwhere_str !=='1=1'){
            $where= " AND ( vtiger_leaddetails.assigner =".$oriwhere_str."  OR vtiger_crmentity.smownerid ". $oriwhere_str.")";
        }
        $new_sql = "SELECT count(1) AS value,sum(IF(assignerstatus = 'c_allocated',1,0)) AS allocated,sum(IF(assignerstatus = 'c_cancelled',1,0)) AS cancelled,substring_index(vtiger_leaddetails.address,'#',1) AS name FROM vtiger_leaddetails LEFT JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_leaddetails.address!='###' {$tempdate} {$where} GROUP BY substring_index(vtiger_leaddetails.address,'#',1)";
        $adb = PearDatabase::getInstance();
        //echo $new_sql;
        $new_data=array();
        $new_data['province']=array();
        $new_data['city']=array();
        $new_result = $adb->pquery($new_sql,array());
        do{
            if($adb->num_rows($new_result)==0){
                break;
            }
            for($i=0;$i<$adb->num_rows($new_result);$i++){
                $new_data['province'][]=$adb->fetchByAssoc($new_result);
            }
        }while(0);
        $new_sql = "SELECT count(1) AS value,sum(IF(assignerstatus = 'c_allocated',1,0)) AS allocated,sum(IF(assignerstatus = 'c_cancelled',1,0)) AS cancelled,substring_index(vtiger_leaddetails.address,'#',2) AS name FROM vtiger_leaddetails LEFT JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_leaddetails.address!='###' {$tempdate} {$where} GROUP BY substring_index(vtiger_leaddetails.address,'#',2)";
        //$adb = PearDatabase::getInstance();
        //echo $new_sql;
        $new_result = $adb->pquery($new_sql,array());
        do{
            if($adb->num_rows($new_result)==0){
                break;
            }
            for($i=0;$i<$adb->num_rows($new_result);$i++){
                $new_data['city'][]=$adb->fetchByAssoc($new_result);
            }
        }while(0);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($new_data);
        $response->emit();
    }


    /*
     *??????????????????????????????????????????
     * ????????????????????????
     */
    public function getCountsday($begin,$end,$new_sql){
        $adb = PearDatabase::getInstance();
        $new_data = array();
        $new_result = $adb->pquery($new_sql,array($begin,$end+86400));
        for($i=0;$i<$adb->num_rows($new_result);$i++){
            $new_data[]=$adb->fetchByAssoc($new_result);
        }
        return $new_data;
    }

    //??????????????????????????????????????????
    public function getnewlist(Vtiger_Request $request){
        global $current_user;
        $start = $request->get('datetime');
        $end   = $request->get('enddatetime');
        $classes   = $request->get('classes');
        $seriesindex   = $request->get('seriesindex');
        $leadsnum   = $request->get('leadsnum');
        if(strtotime($start)>strtotime($end)){
            $tempdate=" AND TO_DAYS(vtiger_leaddetails.mapcreattime) BETWEEN TO_DAYS('{$start}') AND TO_DAYS('{$end}')";
        }elseif(strtotime($start)<strtotime($end)){
            $tempdate=" AND TO_DAYS(vtiger_leaddetails.mapcreattime) BETWEEN TO_DAYS('{$start}') AND TO_DAYS('{$end}')";
        }else{
            $tempdate=" AND TO_DAYS(vtiger_leaddetails.mapcreattime) =TO_DAYS('{$start}')";
        }
        $oriwhere_str =  getAccessibleUsers('BusinessOpportunities','List',false);
        $where='';
        if($oriwhere_str !=='1=1'){
            $where= " AND ( vtiger_leaddetails.assigner =".$oriwhere_str."  OR vtiger_crmentity.smownerid ". $oriwhere_str.")";
        }
        if($seriesindex==0){
            $assignerstatus="";
        }elseif($seriesindex==1){
            $assignerstatus=" AND assignerstatus = 'c_allocated'";
        }elseif($seriesindex==2){
            $assignerstatus=" AND assignerstatus = 'c_cancelled'";
        }


        switch($classes){
            case 'one':
                $sqlnew = "SELECT
                        leadid,mapcreattime,vtiger_leaddetails.company,vtiger_leadaddress.mobile,vtiger_leadaddress.phone,vtiger_leaddetails.accountid,accountname,vtiger_leaddetails.company,assignerstatus,description,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[??????]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id) AS assigner,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[??????]' ))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id) AS smownerid
                    FROM
                        vtiger_leaddetails
                    INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = crmid
                    LEFT JOIN vtiger_account ON   vtiger_leaddetails.accountid=vtiger_account.accountid
                    LEFT JOIN vtiger_leadaddress ON   vtiger_leaddetails.leadid=vtiger_leadaddress.leadaddressid
                    WHERE
                        deleted=0 {$tempdate} {$where}{$assignerstatus} AND substring_index(vtiger_leaddetails.address,'#',1)='".$leadsnum."'";
                break;
            case 'two':
                $sqlnew = "SELECT
                        leadid,mapcreattime,vtiger_leaddetails.company,vtiger_leadaddress.mobile,vtiger_leadaddress.phone,vtiger_leaddetails.accountid,accountname,vtiger_leaddetails.company,assignerstatus,description,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[??????]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id) AS assigner,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[??????]' ))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id) AS smownerid
                    FROM
                        vtiger_leaddetails
                    INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = crmid
                    LEFT  JOIN vtiger_account ON   vtiger_leaddetails.accountid=vtiger_account.accountid
                    LEFT JOIN vtiger_leadaddress ON   vtiger_leaddetails.leadid=vtiger_leadaddress.leadaddressid
                    WHERE
                        deleted=0 {$tempdate} {$where}{$assignerstatus} AND substring_index(vtiger_leaddetails.address,'#',2)='".$leadsnum."'";
                break;
            default:
                break;
        }
        /*$sqlnew = "SELECT
                        leadid,mapcreattime ,vtiger_leaddetails.accountid,accountname,vtiger_leaddetails.company,assignerstatus,description,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[??????]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id) AS assigner,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[??????]' ))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id) AS smownerid
                    FROM
                        vtiger_leaddetails
                    INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = crmid
                    LEFT  JOIN vtiger_account ON   vtiger_leaddetails.accountid=vtiger_account.accountid
                    LEFT JOIN vtiger_servicecontracts ON (vtiger_servicecontracts.sc_related_to=vtiger_leaddetails.accountid AND vtiger_servicecontracts.sc_related_to IS NOT NULL AND vtiger_servicecontracts.sc_related_to!=0)
                    WHERE
                        deleted=0 AND vtiger_leaddetails.accountid IS NOT NULL AND vtiger_leaddetails.accountid!=0 AND vtiger_servicecontracts.firstcontract=1 {$tempdate} {$where} AND vtiger_leaddetails.leadsourcetnum='".$leadsnum."'";*/

        $adb=PearDatabase::getInstance();
        $result = $adb->run_query_allrecords($sqlnew);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }





    public function getUsers(Vtiger_Request $request){
        $departmentid=$request->get('department');
        if(!empty($departmentid)&&$departmentid!='H1'){
            $userid=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('Rsalesananalysis','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery = ' AND id in('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers('Rsalesananalysis','List',false);
            if($where!='1=1'){
                $listQuery =' AND id '.$where;
            }else{
                $listQuery='';
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(Reporting_Record_Model::getuserinfo($listQuery));
        $response->emit();
        //return Reporting_Record_Model::getuserinfo($listQuery);

    }



}
