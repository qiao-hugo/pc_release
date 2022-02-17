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

class BusinessCycleAnalysis_selectAjax_Action extends Vtiger_Action_Controller {
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
        $oriwhere_str =  getAccessibleUsers('BusinessCycleAnalysis','List',false);
        $where='';
        if($oriwhere_str !=='1=1'){
            $where= " AND ( vtiger_leaddetails.assigner =".$oriwhere_str."  OR vtiger_crmentity.smownerid ". $oriwhere_str.")";
        }
        $new_sql = "SELECT avg(TO_DAYS(vtiger_servicecontracts.firstreceivepaydate)-TO_DAYS(vtiger_leaddetails.mapcreattime)) AS value,vtiger_leaddetails.leadsystem AS name FROM	vtiger_leaddetails LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.sc_related_to = vtiger_leaddetails.accountid WHERE vtiger_servicecontracts.firstcontract=1 AND vtiger_servicecontracts.firstfrommarket=1 {$tempdate} {$where} AND vtiger_servicecontracts.firstreceivepaydate IS NOT NULL GROUP BY vtiger_leaddetails.leadsystem";
        //echo $new_sql;
        $adb = PearDatabase::getInstance();
        $new_data=array();
        $new_data['systems']['name']=array();
        $new_result = $adb->pquery($new_sql,array());
        do{
            if($adb->num_rows($new_result)==0){
                break;
            }
            for($i=0;$i<$adb->num_rows($new_result);$i++){
                $new_datas=$adb->fetchByAssoc($new_result);
                $new_data['systems']['name'][]=$new_datas['name'];
                $new_data['systems']['value'][]=(int)$new_datas['value'];

            }
        }while(0);
        $new_sql = "SELECT avg(TO_DAYS(vtiger_servicecontracts.firstreceivepaydate)-TO_DAYS(vtiger_leaddetails.mapcreattime)) AS value,vtiger_leaddetails.purproduct AS name FROM	vtiger_leaddetails LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.sc_related_to = vtiger_leaddetails.accountid WHERE vtiger_servicecontracts.firstcontract=1 AND vtiger_servicecontracts.firstfrommarket=1 {$tempdate} {$where} AND vtiger_servicecontracts.firstreceivepaydate IS NOT NULL GROUP BY vtiger_leaddetails.purproduct";
        $new_data['leadstype']['name']=array();
        $new_result = $adb->pquery($new_sql,array());
        do{
            if($adb->num_rows($new_result)==0){
                break;
            }
            for($i=0;$i<$adb->num_rows($new_result);$i++){
                $new_datas=$adb->fetchByAssoc($new_result);
                $new_data['leadstype']['name'][]=$new_datas['name'];
                $new_data['leadstype']['value'][]=(int)$new_datas['value'];

            }
        }while(0);
        //echo $new_sql;
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
        $leadsnum= $request->get('leadsnum');
        $classes   = $request->get('classes');
        $start = $request->get('datetime');
        $end   = $request->get('enddatetime');
        if(strtotime($start)>strtotime($end)){
            $tempdate=" AND TO_DAYS(vtiger_leaddetails.mapcreattime) BETWEEN TO_DAYS('{$start}') AND TO_DAYS('{$end}')";
        }elseif(strtotime($start)<strtotime($end)){
            $tempdate=" AND TO_DAYS(vtiger_leaddetails.mapcreattime) BETWEEN TO_DAYS('{$start}') AND TO_DAYS('{$end}')";
        }else{
            $tempdate=" AND TO_DAYS(vtiger_leaddetails.mapcreattime) =TO_DAYS('{$start}')";
        }
        $oriwhere_str =  getAccessibleUsers('BusinessCycleAnalysis','List',false);
        $where='';
        if($oriwhere_str !=='1=1'){
            $where= " AND ( vtiger_leaddetails.assigner =".$oriwhere_str."  OR vtiger_crmentity.smownerid ". $oriwhere_str.")";
        }
        if($classes=='one'){
            $query=" AND vtiger_leaddetails.leadsystem='{$leadsnum}'";
        }else{
            $query=" AND vtiger_leaddetails.purproduct='{$leadsnum}'";
        }

        $sqlnew = "SELECT
                      vtiger_leaddetails.leadsystem,
                      vtiger_leaddetails.leadid,
                      left(vtiger_leaddetails.mapcreattime,10) AS mapcreattime,
                      vtiger_account.accountname,
                      vtiger_leadaddress.mobile,
                      vtiger_servicecontracts.firstreceivepaydate,
                      vtiger_servicecontracts.total,
                      (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id) AS assigner,
                      vtiger_servicecontracts.contract_type,
                      (TO_DAYS(vtiger_servicecontracts.firstreceivepaydate)-TO_DAYS(vtiger_leaddetails.mapcreattime)) AS diffday,
                      vtiger_account.accountid
                  FROM	vtiger_leaddetails
                  LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.sc_related_to = vtiger_leaddetails.accountid
                  LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_leaddetails.accountid
                  LEFT JOIN vtiger_leadaddress ON vtiger_leaddetails.leadid=vtiger_leadaddress.leadaddressid
                  LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid
                  WHERE
                      vtiger_servicecontracts.firstcontract=1
                      AND vtiger_servicecontracts.firstfrommarket=1
                      AND vtiger_crmentity.deleted=0
                      AND vtiger_servicecontracts.firstreceivepaydate IS NOT NULL {$tempdate} {$where} {$query}";
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
