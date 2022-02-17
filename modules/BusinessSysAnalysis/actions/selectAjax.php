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

class BusinessSysAnalysis_selectAjax_Action extends Vtiger_Action_Controller {
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
        $new_sql = "SELECT sum(if(vtiger_leaddetails.assignerstatus='c_allocated' OR vtiger_leaddetails.assignerstatus='c_cancelled' OR vtiger_leaddetails.assignerstatus='c_transformation' OR vtiger_servicecontracts.firstcontract=1,1,0)) AS allvalue,sum(if(vtiger_leaddetails.assignerstatus='c_allocated',1,0)) AS allocated,sum(if(vtiger_leaddetails.assignerstatus='c_transformation',1,0)) AS transformation,sum(if(vtiger_leaddetails.assignerstatus='c_cancelled',1,0)) AS cancelled,sum(if(vtiger_servicecontracts.firstcontract=1,1,0)) AS firstcontr,vtiger_leaddetails.leadsystem AS name FROM vtiger_leaddetails LEFT JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid LEFT JOIN vtiger_servicecontracts ON (vtiger_servicecontracts.sc_related_to=vtiger_leaddetails.accountid AND vtiger_servicecontracts.sc_related_to IS NOT NULL AND vtiger_servicecontracts.sc_related_to!=0) WHERE vtiger_crmentity.deleted=0 AND (vtiger_leaddetails.purproduct!='' AND vtiger_leaddetails.purproduct IS NOT NULL) {$tempdate} {$where} GROUP BY vtiger_leaddetails.leadsystem";
        //echo $new_sql;
        $adb = PearDatabase::getInstance();
        $new_data=array();
        $new_result = $adb->pquery($new_sql,array());
        do{
            if($adb->num_rows($new_result)==0){
                break;
            }
            for($i=0;$i<$adb->num_rows($new_result);$i++){
                $new_datas=$adb->fetchByAssoc($new_result);
                $new_data['name'][]=$new_datas['name'];
                $new_data['transformation'][]=$new_datas['transformation'];
                $new_data['allocated'][]=$new_datas['allocated'];
                $new_data['cancelled'][]=$new_datas['cancelled'];
                $new_data['firstcontr'][]=$new_datas['firstcontr'];
                $new_data['allvalue'][]=$new_datas['allvalue'];
            }
        }while(0);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($new_data);
        $response->emit();
    }


    /*
     *获取两个时间区段之间的的记录
     * 以及时间端的计算
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

    //获取新增商机的详细列表信息；
    public function getnewlist(Vtiger_Request $request){
        global $current_user;
        $start = $request->get('datetime');
        $end   = $request->get('enddatetime');
        $classes   = $request->get('classes');
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

        switch($classes){
            case 'allocated':
                $sqlnew = "SELECT
                        leadid,mapcreattime ,vtiger_leaddetails.accountid,accountname,vtiger_leaddetails.company,assignerstatus,description,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id) AS assigner,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id) AS smownerid
                    FROM
                        vtiger_leaddetails
                    INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = crmid
                    LEFT  JOIN vtiger_account ON   vtiger_leaddetails.accountid=vtiger_account.accountid
                    WHERE
                        deleted=0 {$tempdate} {$where} AND vtiger_leaddetails.leadsystem='".$leadsnum."' AND vtiger_leaddetails.assignerstatus='c_allocated'";
                break;
            case 'transformation':
                $sqlnew = "SELECT
                        leadid,mapcreattime ,vtiger_leaddetails.accountid,accountname,vtiger_leaddetails.company,assignerstatus,description,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id) AS assigner,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id) AS smownerid
                    FROM
                        vtiger_leaddetails
                    INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = crmid
                    LEFT  JOIN vtiger_account ON   vtiger_leaddetails.accountid=vtiger_account.accountid
                    WHERE
                        deleted=0 {$tempdate} {$where} AND vtiger_leaddetails.leadsystem='".$leadsnum."' AND vtiger_leaddetails.assignerstatus='c_transformation'";
                break;
            case 'cancelled':
                $sqlnew = "SELECT
                        leadid,mapcreattime ,vtiger_leaddetails.accountid,accountname,vtiger_leaddetails.company,assignerstatus,description,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id) AS assigner,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id) AS smownerid
                    FROM
                        vtiger_leaddetails
                    INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = crmid
                    LEFT  JOIN vtiger_account ON   vtiger_leaddetails.accountid=vtiger_account.accountid
                    WHERE
                        deleted=0 {$tempdate} {$where} AND vtiger_leaddetails.leadsystem='".$leadsnum."' AND vtiger_leaddetails.assignerstatus='c_cancelled'";
                break;
            case 'firstcontr':
                $sqlnew = "SELECT
                        leadid,mapcreattime ,vtiger_leaddetails.accountid,accountname,vtiger_leaddetails.company,assignerstatus,description,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id) AS assigner,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id) AS smownerid
                    FROM
                        vtiger_leaddetails
                    INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = crmid
                    LEFT  JOIN vtiger_account ON   vtiger_leaddetails.accountid=vtiger_account.accountid
                    LEFT JOIN vtiger_servicecontracts ON (vtiger_servicecontracts.sc_related_to=vtiger_leaddetails.accountid AND vtiger_servicecontracts.sc_related_to IS NOT NULL AND vtiger_servicecontracts.sc_related_to!=0)
                    WHERE
                        deleted=0 AND vtiger_leaddetails.accountid IS NOT NULL AND vtiger_leaddetails.accountid!=0 AND vtiger_servicecontracts.firstcontract=1 {$tempdate} {$where} AND vtiger_leaddetails.leadsystem='".$leadsnum."'";
                break;
            case 'allvalue':
                $sqlnew = "SELECT
                        leadid,mapcreattime ,vtiger_leaddetails.accountid,accountname,vtiger_leaddetails.company,assignerstatus,description,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id) AS assigner,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id) AS smownerid
                    FROM
                        vtiger_leaddetails
                    INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = crmid
                    LEFT  JOIN vtiger_account ON   vtiger_leaddetails.accountid=vtiger_account.accountid
                    LEFT JOIN vtiger_servicecontracts ON (vtiger_servicecontracts.sc_related_to=vtiger_leaddetails.accountid AND vtiger_servicecontracts.sc_related_to IS NOT NULL AND vtiger_servicecontracts.sc_related_to!=0)
                    WHERE
                        deleted=0 AND (vtiger_leaddetails.assignerstatus='c_allocated' OR vtiger_leaddetails.assignerstatus='c_cancelled' OR vtiger_leaddetails.assignerstatus='c_transformation' OR vtiger_servicecontracts.firstcontract=1) {$tempdate} {$where} AND vtiger_leaddetails.leadsystem='".$leadsnum."'";
                break;
            default:
                break;
        }
        //echo $sqlnew;
        /*$sqlnew = "SELECT
                        leadid,mapcreattime ,vtiger_leaddetails.accountid,accountname,vtiger_leaddetails.company,assignerstatus,description,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id) AS assigner,
                        (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id) AS smownerid
                    FROM
                        vtiger_leaddetails
                    INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = crmid
                    LEFT  JOIN vtiger_account ON   vtiger_leaddetails.accountid=vtiger_account.accountid
                    LEFT JOIN vtiger_servicecontracts ON (vtiger_servicecontracts.sc_related_to=vtiger_leaddetails.accountid AND vtiger_servicecontracts.sc_related_to IS NOT NULL AND vtiger_servicecontracts.sc_related_to!=0)
                    WHERE
                        deleted=0 AND vtiger_leaddetails.accountid IS NOT NULL AND vtiger_leaddetails.accountid!=0 AND vtiger_servicecontracts.firstcontract=1 {$tempdate} {$where} AND vtiger_leaddetails.leadsourcetnum='".$leadsnum."'";*/
    //echo $sqlnew;
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
