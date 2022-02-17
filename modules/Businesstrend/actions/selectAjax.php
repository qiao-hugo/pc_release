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

class Businesstrend_selectAjax_Action extends Vtiger_Action_Controller {
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
        $start = strtotime($request->get('datetime'));
        $end   = strtotime($request->get('enddatetime'));
        $char  = $request->get('charid'); //负责人
        $begin = "";
        if($start<$end){
            $begin = $start;
        }else{
            $begin = $end;
            $end = $start;
        }
        $datearr = array();
        for($i=0;strtotime("+".$i."days",$begin)<=$end;$i++){
            $datearr[] = date('Y-m-d',strtotime("+".$i."days",$begin));
        }
        $sql="";
        if($char>0 && $char !=='1'){
           //echo '这个是商机的负责人,稍后解决';die;
           //echo $current_user->id;die;
           //$where="(vtiger_crmentity.smownerid = 1 OR vtiger_leaddetails.assigner = 1)";
           $where=" vtiger_leaddetails.assigner =".$char." AND ";
        }else{
            $where="";
        }
        //$new_sql = "SELECT COUNT(leadid) num, DATE_FORMAT(mapcreattime, '%Y-%m-%d') new, GROUP_CONCAT(leadid SEPARATOR ',') AS id FROM vtiger_leaddetails INNER JOIN vtiger_crmentity WHERE crmid = leadid AND deleted = 0 AND mapcreattime BETWEEN DATE_FORMAT( FROM_UNIXTIME(?), '%Y-%m-%d' ) AND DATE_FORMAT( FROM_UNIXTIME(?),'%Y-%m-%d') GROUP BY DATE_FORMAT(mapcreattime, '%Y-%m-%d') ORDER BY mapcreattime";
        $new_sql = "SELECT COUNT(leadid) num, DATE_FORMAT(mapcreattime, '%Y-%m-%d') new, GROUP_CONCAT(leadid SEPARATOR ',') AS id FROM vtiger_leaddetails INNER JOIN vtiger_crmentity ON crmid = leadid WHERE ". $where ." crmid = leadid AND deleted = 0 AND mapcreattime BETWEEN DATE_FORMAT( FROM_UNIXTIME(?), '%Y-%m-%d' ) AND DATE_FORMAT( FROM_UNIXTIME(?), '%Y-%m-%d' ) GROUP BY DATE_FORMAT(mapcreattime, '%Y-%m-%d') ORDER BY mapcreattime";
        $new_data =  $this->getCountsday($begin,$end,$new_sql);
        $con_sql = "SELECT COUNT(leadid) num, DATE_FORMAT(createdtime, '%Y-%m-%d') new, GROUP_CONCAT(leadid SEPARATOR ',') AS id FROM vtiger_leaddetails INNER JOIN vtiger_account ON vtiger_account.accountid = vtiger_leaddetails.accountid INNER JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid WHERE ". $where ." assignerstatus = 'c_complete' AND deleted = 0 AND createdtime BETWEEN DATE_FORMAT( FROM_UNIXTIME(?), '%Y-%m-%d' ) AND DATE_FORMAT( FROM_UNIXTIME(?), '%Y-%m-%d' ) GROUP BY DATE_FORMAT(createdtime, '%Y-%m-%d') ORDER BY createdtime";
        $con_data = $this->getCountsday($begin,$end,$con_sql);
        //var_dump($con_data);die;
        $sin_sql = "SELECT COUNT(leadid) num, DATE_FORMAT(completetime, '%Y-%m-%d') new, GROUP_CONCAT(leadid SEPARATOR ',') AS id FROM vtiger_servicecontracts INNER JOIN vtiger_leaddetails ON sc_related_to = accountid INNER JOIN vtiger_crmentity ON crmid = servicecontractsid WHERE ". $where ."  isfrommarket = 1 AND firstcontract = 1 AND deleted = 0 AND completetime BETWEEN DATE_FORMAT( FROM_UNIXTIME(?), '%Y-%m-%d' ) AND DATE_FORMAT( FROM_UNIXTIME(?), '%Y-%m-%d' ) GROUP BY DATE_FORMAT(completetime, '%Y-%m-%d') ORDER BY completetime";
        $sin_data= $this->getCountsday($begin,$end,$sin_sql);
        //var_dump($sin_data);
        $return=array();
        foreach($datearr as $val){
            foreach($new_data as $val0){
                if($val0['new']==$val){
                    $return[$val]['new'] = $val0['num'];
                }else{
                    if(!isset($return[$val]['new'])){
                        $return[$val]['new'] = 0;
                    }
                }
            }
            foreach($con_data as $val1){
                if($val1['new']==$val){
                    $return[$val]['con'] = $val1['num'];
                }else{
                    if(!isset($return[$val]['con'])){
                        $return[$val]['con'] = 0;
                    }
                }
            }
            foreach($sin_data as $val2){
                if($val2['new']==$val){
                    $return[$val]['sin'] = $val2['num'];
                }else{
                    if(!isset($return[$val]['sin'])){
                        $return[$val]['sin'] = 0;
                    }
                }
            }
        }
        //var_dump($return);
        $return_data = array($datearr,$new_data,$con_data);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($return);
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
        $charid = $request->get('charid');
        $date = $request->get('date');
        $oriwhere_arr =  getAccessibleUsers('Businesstrend','List',true);
        $oriwhere_str =  getAccessibleUsers('Businesstrend','List',false);
        $sqlnew = "SELECT
	leadid,mapcreattime ,vtiger_leaddetails.accountid,accountname,vtiger_leaddetails.company,assignerstatus,
(
SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id
) AS assigner
FROM
	vtiger_leaddetails
INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = crmid
LEFT  JOIN vtiger_account ON   vtiger_leaddetails.accountid=vtiger_account.accountid
WHERE
	deleted=0 AND DATE_FORMAT(mapcreattime, '%Y-%m-%d') ='".$date."'";
        if($charid>0){
            if($oriwhere_arr !=='1=1'){
                $where1= " AND ( vtiger_leaddetails.assigner =".$charid."  OR vtiger_crmentity.smownerid ". $oriwhere_str.")";
            }else{
                $where1= "AND ( vtiger_leaddetails.assigner =".$charid.")";
            }
        }else{
            if($oriwhere_arr !=='1=1'){
                $where1= " AND (vtiger_crmentity.smownerid ". $oriwhere_str.")";
            }
        }
        $querynew=$sqlnew.$where1;
        $adb=PearDatabase::getInstance();
        $result = $adb->run_query_allrecords($querynew);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }

    public function getconlist(Vtiger_Request $request){
        global $current_user;
        $charid = $request->get('charid');
        $date = $request->get('date');
        $oriwhere_arr =  getAccessibleUsers('Businesstrend','List',true);
        $oriwhere_str =  getAccessibleUsers('Businesstrend','List',false);
        $sqlnew = "SELECT leadid, mapcreattime, vtiger_leaddetails.accountid, accountname, vtiger_leaddetails.company, assignerstatus, ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id ) AS assigner FROM vtiger_leaddetails INNER JOIN vtiger_account ON vtiger_account.accountid = vtiger_leaddetails.accountid INNER JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid WHERE assignerstatus = 'c_complete' AND deleted = 0 AND DATE_FORMAT(createdtime, '%Y-%m-%d') = '".$date."'";
        if($charid>0){
            if($oriwhere_arr !=='1=1'){
                $where1= " AND ( vtiger_leaddetails.assigner =".$charid."  OR vtiger_crmentity.smownerid ". $oriwhere_str.")";
            }else{
                $where1= "AND ( vtiger_leaddetails.assigner =".$charid.")";
            }
        }else{
            if($oriwhere_arr !=='1=1'){
                $where1= " AND (vtiger_crmentity.smownerid ". $oriwhere_str.")";
            }
        }
        $querynew=$sqlnew.$where1;
        $adb=PearDatabase::getInstance();
        $result = $adb->run_query_allrecords($querynew);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }

    public function getsinlist(Vtiger_Request $request){
        global $current_user;
        $charid = $request->get('charid');
        $date = $request->get('date');
        $oriwhere_arr =  getAccessibleUsers('Businesstrend','List',true);
        $oriwhere_str =  getAccessibleUsers('Businesstrend','List',false);
        $sqlnew = "SELECT leadid, mapcreattime, vtiger_leaddetails.accountid, accountname, vtiger_leaddetails.company, assignerstatus, servicecontractsid, contract_no, ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_leaddetails.assigner = vtiger_users.id ) AS assigner FROM vtiger_servicecontracts INNER JOIN vtiger_leaddetails ON sc_related_to = accountid INNER JOIN vtiger_crmentity ON crmid = servicecontractsid INNER JOIN vtiger_account ON vtiger_account.accountid = sc_related_to WHERE isfrommarket = 1 AND firstcontract = 1 AND deleted = 0 AND DATE_FORMAT(createdtime, '%Y-%m-%d') = '".$date."'";
        if($charid>0){
            if($oriwhere_arr !=='1=1'){
                $where1= " AND ( vtiger_leaddetails.assigner =".$charid."  OR vtiger_crmentity.smownerid ". $oriwhere_str.")";
            }else{
                $where1= "AND ( vtiger_leaddetails.assigner =".$charid.")";
            }
        }else{
            if($oriwhere_arr !=='1=1'){
                $where1= " AND (vtiger_crmentity.smownerid ". $oriwhere_str.")";
            }
        }
        $querynew=$sqlnew.$where1;
        //echo $querynew;die;
        $adb=PearDatabase::getInstance();
        $result = $adb->run_query_allrecords($querynew);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }

    public function getdays(){
        $datearr = array();
        for($i=0;strtotime("+".$i."days",$begin)<=$end;$i++){
            $datearr[] = date('Y-m-d',strtotime("+".$i."days",$begin));
        }
        return $datearr;
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
