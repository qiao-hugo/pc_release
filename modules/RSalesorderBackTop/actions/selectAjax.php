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

class RSalesorderBackTop_selectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getCountsday');
        $this->exposeMethod('getcontractdetaillist');
        $this->exposeMethod('getpaymentdetaillist');
        $this->exposeMethod('getrefreshday');
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
    //打开时加载
    public function getCountsday(Vtiger_Request $request){
        $datetime=$request->get('datetime');
        $enddatetime=$request->get('enddatetime');
        $userid=$request->get('userid');
        $pagenum=$request->get('pagenum');
        $departmentid=$request->get('department');
        $fliter=$request->get('fliter');
        if($fliter=='thisweek'){
            $datetime=$lastday=date('Y-m-d',strtotime("Sunday"));
            $firstday=date('Y-m-d',strtotime("$lastday -6 days"));
            $tempdate = " BETWEEN '{$firstday}' AND '{$lastday}'";
        }else if($fliter=='thismonth'){
            $datetime=$firstday = date('Y-m-01');
            $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
            $tempdate = " BETWEEN '{$firstday}' AND '{$lastday}'";
        }else {
            if (strtotime($datetime) > strtotime($enddatetime)) {
                $tempdate = " BETWEEN '{$enddatetime}' AND '{$datetime}'";
            } elseif (strtotime($datetime) < strtotime($enddatetime)) {
                $tempdate = " BETWEEN '{$datetime}' AND '{$enddatetime}'";
            } else {
                $tempdate = " ='{$datetime}'";
            }
        }
        if(empty($pagenum)){
            $limit=" LIMIT 10";
        }else if($pagenum=="ALL"){
            $limit="";
        }else{
            $pagenum=abs((int)$pagenum);
            $limit=" LIMIT {$pagenum}";
        }
        if($datetime==''){

            $tempdate=" ='".date('Y-m-d')."'";
        }
        if(empty($userid)||$userid=='null'){
            if(!empty($departmentid)){

                $where=array();
                foreach($departmentid as $value){
                    $userid=getDepartmentUser($value);
                    $temparr=getAccessibleUsers('RSalesorderBackTop','List',true);
                    if($temparr!='1=1'){
                        $temparr=array_intersect($temparr,$userid);
                    }else{
                        $temparr=$userid;
                    }
                    $where=array_merge($where,$temparr);
                }
                $where=array_unique($where);
                $sql = ' AND vtiger_servicecontracts.receiveid in('.implode(',',$where).')';
            }else{
                $where=getAccessibleUsers('RSalesorderBackTop','List',false);
                if($where!='1=1'){
                    $sql =' AND vtiger_servicecontracts.receiveid '.$where;
                }else{
                    $sql='';
                }
            }

        }else{
            $sql=" AND vtiger_servicecontracts.receiveid={$userid}";
        }
        $db=PearDatabase::getInstance();
        $query="SELECT vtiger_users.last_name,vtiger_servicecontracts.receiveid,SUM(IFNULL(vtiger_receivedpayments.unit_price,0)) AS totals
                FROM vtiger_receivedpayments
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                LEFT JOIN vtiger_users ON vtiger_servicecontracts.receiveid=vtiger_users.id
                WHERE vtiger_receivedpayments.relatetoid>0
                AND vtiger_servicecontracts.receiveid>0
                AND left(vtiger_receivedpayments.reality_date,10) {$tempdate} {$sql}
                GROUP BY vtiger_servicecontracts.receiveid ORDER BY totals DESC
                {$limit}";
        //echo $query;
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        $arr=array();
        if($num<1){

            $arr['Contract']='';
        }else{
            for($i=0;$i<$num;$i++){
                $arr['Contract'][$i]['user_name']=$db->query_result($result,$i,'last_name');
                $arr['Contract'][$i]['totals']=$db->query_result($result,$i,'totals');
                $arr['Contract'][$i]['cid']=$db->query_result($result,$i,'receiveid');

            }
        }


        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();
    }

    public function getUsers(Vtiger_Request $request){
        $departmentid=$request->get('department');
        if(!empty($departmentid)&&$departmentid!='H1'){
            $userid=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('RSalesorderBackTop','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery = ' AND id in('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers('RSalesorderBackTop','List',false);
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
    public function getcontractdetaillist(Vtiger_Request $request){
        $datetime=$request->get('datetime');
        $enddatetime=$request->get('enddatetime');
        $datauserid=abs((int)$request->get('datauserid'));
        $fliter=$request->get('fliter');
        if($fliter=='thisweek'){
            $lastday=date('Y-m-d',strtotime("Sunday"));
            $firstday=date('Y-m-d',strtotime("$lastday -6 days"));
            $tempdate = " BETWEEN '{$firstday}' AND '{$lastday}'";
        }else if($fliter=='thismonth'){
            $firstday = date('Y-m-01');
            $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
            $tempdate = " BETWEEN '{$firstday}' AND '{$lastday}'";
        }else {
            if (strtotime($datetime) > strtotime($enddatetime)) {
                $tempdate = " BETWEEN '{$enddatetime}' AND '{$datetime}'";
            } elseif (strtotime($datetime) < strtotime($enddatetime)) {
                $tempdate = " BETWEEN '{$datetime}' AND '{$enddatetime}'";
            } else {
                $tempdate = " ='{$datetime}'";
            }
        }
        $db=PearDatabase::getInstance();
        $query1="SELECT
                        (SELECT	CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM	vtiger_departments WHERE departmentid = (SELECT	departmentid FROM	vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE vtiger_servicecontracts.receiveid = vtiger_users.id) AS receiveid,
                        vtiger_receivedpayments.receivedpaymentsid,
                        IFNULL(vtiger_receivedpayments.unit_price,0) AS total,
                        vtiger_receivedpayments.reality_date AS returndate,
                        vtiger_servicecontracts.servicecontractsid AS cid,
                        vtiger_account.accountid,
                        vtiger_servicecontracts.contract_no,
                        vtiger_account.accountname
                FROM vtiger_receivedpayments
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid
                WHERE vtiger_receivedpayments.relatetoid>0
                AND vtiger_servicecontracts.receiveid>0
                AND vtiger_servicecontracts.receiveid={$datauserid}
                AND left(vtiger_receivedpayments.reality_date,10){$tempdate}
               LIMIT 1000";
        //echo $query1;
        $result1=$db->run_query_allrecords($query1);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result1);
        $response->emit();

    }


}
