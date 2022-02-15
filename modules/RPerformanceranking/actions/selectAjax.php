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

class RPerformanceranking_selectAjax_Action extends Vtiger_Action_Controller {
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
            if($datetime==''){

                $tempdate=" ='".date('Y-m-d')."'";
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

        if(empty($userid)||$userid=='null'){
            if(!empty($departmentid)&&$departmentid!='null'){
                $where=array();
                foreach($departmentid as $value){
                    $userid=getDepartmentUser($value);
                    $temparr=getAccessibleUsers('RPerformanceranking','List',true);
                    if($temparr!='1=1'){
                        $temparr=array_intersect($temparr,$userid);
                    }else{
                        $temparr=$userid;
                    }
                    $where=array_merge($where,$temparr);
                }
                $where=array_unique($where);
                $sql = ' AND vtiger_servicecontracts_divide.receivedpaymentownid in('.implode(',',$where).') AND vtiger_user2department.departmentid=vtiger_servicecontracts_divide.signdempart';
            }else{var_dump($departmentid);
                $where=getAccessibleUsers('RPerformanceranking','List',false);
                if($where!='1=1'){
                    $sql =' AND vtiger_user2department.departmentid=vtiger_servicecontracts_divide.signdempart AND vtiger_servicecontracts_divide.receivedpaymentownid '.$where;
                }else{
                    $sql='';
                }
            }

        }else{
            $sql=" AND vtiger_user2department.departmentid=vtiger_servicecontracts_divide.signdempart AND vtiger_servicecontracts_divide.receivedpaymentownid={$userid}";
        }
        //$datetime=date('Y-m-d');
        $db=PearDatabase::getInstance();
        $query="SELECT
                    vtiger_users.last_name,
                    vtiger_servicecontracts_divide.receivedpaymentownid AS receiveid,
                    TRUNCATE(sum(IFNULL(vtiger_servicecontracts.total,0)*vtiger_servicecontracts_divide.scalling/100),2) AS totals
                FROM
                    vtiger_servicecontracts_divide
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_servicecontracts_divide.servicecontractid
                LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_servicecontracts_divide.receivedpaymentownid
		LEFT JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                WHERE
                    vtiger_crmentity.deleted = 0
                    AND vtiger_servicecontracts.modulestatus = 'c_complete'{$sql}
                    AND left(vtiger_servicecontracts.signdate,10){$tempdate}
                GROUP BY
                    vtiger_servicecontracts_divide.receivedpaymentownid
                ORDER BY
                    totals DESC
                {$limit}";
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

        /*$query1="SELECT
                    (SELECT vtiger_users.last_name FROM vtiger_users WHERE	vtiger_users.id = vtiger_servicecontracts.receiveid) AS user_name,
                    vtiger_servicecontracts.receiveid,
                    (sum(IFNULL(vtiger_receivedpayments.unit_price,0))-sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0))) AS totalprice
                FROM
                    vtiger_receivedpayments
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid
                LEFT JOIN vtiger_salesorderproductsrel ON vtiger_servicecontracts.servicecontractsid=vtiger_salesorderproductsrel.servicecontractsid
                WHERE
                    vtiger_receivedpayments.relatetoid>0
                AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                AND left(vtiger_receivedpayments.reality_date,10){$tempdate}
                {$sql}
                GROUP BY
                    vtiger_servicecontracts.receiveid
                ORDER BY totalprice DESC {$limit}";*/
        /*$query1="SELECT
                    vtiger_users.last_name AS user_name,
                    vtiger_achievementallot.receivedpaymentownid AS receiveid,
                    TRUNCATE(sum(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum((IFNULL(vtiger_salesorderproductsrel.purchasemount,0))*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100)	FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))),2) as totalprice
                FROM
                    `vtiger_achievementallot`
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                LEFT JOIN vtiger_salesorder ON vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                WHERE
                    1=1
                    AND vtiger_salesorder.salesorderid>0
                    AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                    AND left(vtiger_receivedpayments.reality_date,10){$tempdate} {$sql}
                GROUP BY vtiger_achievementallot.receivedpaymentownid
                ORDER BY totalprice DESC {$limit}";*/
	$query1="SELECT last_name AS user_name,userid AS receiveid,sum(totalprice) AS totalprice FROM vtiger_performance_evaluation WHERE (left(vtiger_performance_evaluation.orderdate,10){$tempdate} OR left(vtiger_performance_evaluation.receivedate,10){$tempdate}){$sql}  GROUP BY userid ORDER BY totalprice DESC {$limit}";
        $query1=str_replace('vtiger_servicecontracts_divide.receivedpaymentownid','userid',$query1);
         //echo $query1;
        $result1=$db->pquery($query1,array());
        $num1=$db->num_rows($result1);
        if($num1<1){
            $arr['Payment']='';
        }else{
            //$arr=array();
            for($i=0;$i<$num1;$i++){
                $arr['Payment'][$i]['user_name']=$db->query_result($result1,$i,'user_name');
                $arr['Payment'][$i]['totals']=$db->query_result($result1,$i,'totalprice');
                $arr['Payment'][$i]['cid']=$db->query_result($result1,$i,'receiveid');

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
            $where=getAccessibleUsers('RPerformanceranking','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery = ' AND id in('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers('RPerformanceranking','List',false);
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

    }
    public function getcontractdetaillist(Vtiger_Request $request){
        $datetime=$request->get('datetime');
        $enddatetime=$request->get('enddatetime');
        $datauserid=abs((int)$request->get('datauserid'));
        if(strtotime($datetime)>strtotime($enddatetime)){
            $tempdate=" BETWEEN '{$enddatetime}' AND '{$datetime}'";
        }elseif(strtotime($datetime)<strtotime($enddatetime)){
            $tempdate=" BETWEEN '{$datetime}' AND '{$enddatetime}'";
        }else{
            $tempdate=" ='{$datetime}'";
        }
        $fliter=$request->get('fliter');
        if($fliter=='thisweek'){
            $lastday=date('Y-m-d',strtotime("Sunday"));
            $firstday=date('Y-m-d',strtotime("$lastday -6 days"));
            $tempdate = " BETWEEN '{$firstday}' AND '{$lastday}'";
        }else if($fliter=='thismonth'){
            $firstday = date('Y-m-01');
            $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
            $tempdate = " BETWEEN '{$firstday}' AND '{$lastday}'";
        }
        $db=PearDatabase::getInstance();
        $query1="SELECT
                    vtiger_servicecontracts.contract_no,
                    (vtiger_account.accountname) AS sc_related_to,
                    vtiger_servicecontracts.servicecontractsid AS cid,
                    vtiger_servicecontracts.sc_related_to AS sc_related_to_reference,
                    vtiger_servicecontracts.contract_type,
                    IFNULL(left(vtiger_servicecontracts.receivedate,10),'') AS receivedate,
                    (SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid =(SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_servicecontracts.signid = vtiger_users.id ) AS signid,
                    IFNULL(vtiger_servicecontracts.signdate,'') AS signdate,
                    (SELECT	CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM	vtiger_departments WHERE departmentid = (SELECT	departmentid FROM	vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE vtiger_servicecontracts_divide.receivedpaymentownid = vtiger_users.id) AS receiveid,
                 vtiger_servicecontracts.signdate,
                 truncate(IFNULL(vtiger_servicecontracts.total,0)*vtiger_servicecontracts_divide.scalling/100,2) AS total,
                 IFNULL(REPLACE(vtiger_servicecontracts.productsearchid,'<br>',','),'') AS productsearchid,
                 IFNULL(vtiger_servicecontracts.firstreceivepaydate,'') AS firstreceivepaydate,
                 vtiger_servicecontracts.servicecontractsid
                FROM
                    vtiger_servicecontracts_divide
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_servicecontracts_divide.servicecontractid
                LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid
                LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
		LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_servicecontracts_divide.receivedpaymentownid
                LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_servicecontracts.productid
                WHERE
                    1 = 1
                AND vtiger_crmentity.deleted = 0
                AND vtiger_servicecontracts.modulestatus = 'c_complete'
		AND vtiger_servicecontracts_divide.signdempart=vtiger_user2department.departmentid
                AND vtiger_servicecontracts_divide.receivedpaymentownid={$datauserid}
                AND vtiger_servicecontracts.signdate IS NOT NULL
                AND vtiger_servicecontracts.signdate!=''
                AND left(vtiger_servicecontracts.signdate,10){$tempdate}
                ORDER BY
                    vtiger_servicecontracts.servicecontractsid DESC LIMIT 1000";
        //echo $query1;
        $result1=$db->run_query_allrecords($query1);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result1);
        $response->emit();

    }
    public function getpaymentdetaillist(Vtiger_Request $request){
        $datetime=$request->get('datetime');
        $enddatetime=$request->get('enddatetime');
        $datauserid=abs((int)$request->get('datauserid'));
        if(strtotime($datetime)>strtotime($enddatetime)){
            $tempdate=" BETWEEN '{$enddatetime}' AND '{$datetime}'";
        }elseif(strtotime($datetime)<strtotime($enddatetime)){
            $tempdate=" BETWEEN '{$datetime}' AND '{$enddatetime}'";
        }else{
            $tempdate=" ='{$datetime}'";
        }
        $fliter=$request->get('fliter');
        if($fliter=='thisweek'){
            $lastday=date('Y-m-d',strtotime("Sunday"));
            $firstday=date('Y-m-d',strtotime("$lastday -6 days"));
            $tempdate = " BETWEEN '{$firstday}' AND '{$lastday}'";
        }else if($fliter=='thismonth'){
            $firstday = date('Y-m-01');
            $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
            $tempdate = " BETWEEN '{$firstday}' AND '{$lastday}'";
        }
        $db=PearDatabase::getInstance();
        $query1="SELECT
	            distinct vtiger_achievementallot.receivedpaymentsid,
                    vtiger_receivedpayments.receivedpaymentsid as rid,
                    vtiger_users.last_name,
                    vtiger_account.accountname,
                    vtiger_account.accountid,
                    vtiger_servicecontracts.total,
                    vtiger_achievementallot.businessunit,
                    vtiger_servicecontracts.contract_no,
                    vtiger_servicecontracts.servicecontractsid AS cid,
                    TRUNCATE((IFNULL((SELECT sum((IFNULL(vtiger_salesorderproductsrel.purchasemount,0))*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0)+IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0)),2) as sss,
                    IFNULL(TRUNCATE((SELECT sum((IFNULL(vtiger_salesorderproductsrel.purchasemount,0))*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),2),'') as aaa,
                    IFNULL(TRUNCATE((SELECT sum(if(extra_type='沙龙',IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),2),'') as salong,
                    IFNULL(TRUNCATE((SELECT sum(if(extra_type='外采',IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),2),'') as waici,
                    IFNULL(TRUNCATE((SELECT sum(if(extra_type='媒介充值',IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),2),'') as meijai,
                    IFNULL(TRUNCATE((SELECT sum(if(extra_type!='媒介充值' AND extra_type!='外采' AND extra_type!='沙龙',IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),2),'') as other,
                    vtiger_receivedpayments.reality_date,
                    vtiger_achievementallot.scalling
                FROM
                    `vtiger_achievementallot`
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                LEFT JOIN vtiger_salesorder ON vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
		LEFT JOIN vtiger_performance_evaluation ON vtiger_performance_evaluation.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                WHERE
                  vtiger_achievementallot.receivedpaymentownid={$datauserid}
                AND (left(vtiger_performance_evaluation.receivedate,10){$tempdate} OR left(vtiger_performance_evaluation.orderdate,10){$tempdate})
                ORDER BY
                    vtiger_servicecontracts.servicecontractsid DESC LIMIT 1000";
        //echo $query1;
        $result1=$db->run_query_allrecords($query1);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result1);
        $response->emit();
    }
    //更新有效回款
    public function getrefreshday(){
        ignore_user_abort(true);//浏览器关闭后脚本还执行
        $db=PearDatabase::getInstance();
        $query="SELECT refreshtime FROM `vtiger_refreshtime` WHERE module='RPerformanceranking' limit 1";
        $result=$db->pquery($query,array());
        $resulttime=$db->query_result($result,0,'refreshtime');
        $nowtime=time();
        $interval=60*60;//间隔时间
        $result1=array();
        if($nowtime-$resulttime>$interval){
            $nowtime=time();
            $db->pquery("replace into vtiger_refreshtime(refreshtime,module) VALUES(?,?)",array($nowtime,'RPerformanceranking'));
            $db->pquery("DELETE FROM vtiger_performance_evaluation WHERE left(orderdate,7) IN(left(curdate(),7),left(date_add(curdate(),interval -1 month),7),left(date_add(curdate(),interval -2 month),7))",array());
            $db->pquery("INSERT into vtiger_performance_evaluation(receivedpaymentsid,userid,last_name,totalprice,receivedate,orderdate)
                    SELECT vtiger_achievementallot.receivedpaymentsid,vtiger_users.id,vtiger_users.last_name AS user_name,(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))) as totalprice,
                    left(vtiger_receivedpayments.reality_date,10) as receivedate,left(vtiger_crmentity.createdtime,10) as orderdate
                    from vtiger_achievementallot
                    LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    LEFT JOIN vtiger_salesorder ON vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                    left join vtiger_crmentity on vtiger_salesorder.salesorderid=vtiger_crmentity.crmid
                    where left(vtiger_receivedpayments.reality_date,7)=left(date_add(curdate(),interval -2 month),7)
                    AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                    and left(vtiger_crmentity.createdtime,10) BETWEEN date_add(curdate()-day(curdate())+1,interval -2 month) AND date_add(curdate()-day(curdate())+5,interval -1 month) and vtiger_crmentity.createdtime >=vtiger_receivedpayments.reality_date
                    UNION ALL
                    SELECT vtiger_achievementallot.receivedpaymentsid,vtiger_users.id,vtiger_users.last_name AS user_name,(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))) as totalprice,left(vtiger_receivedpayments.reality_date,10) as receivedate,left(vtiger_crmentity.createdtime,10) as orderdate
                    from vtiger_achievementallot
                    LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    LEFT JOIN vtiger_salesorder ON vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                    left join vtiger_crmentity on vtiger_salesorder.salesorderid=vtiger_crmentity.crmid
                    where left(vtiger_receivedpayments.reality_date,7) =left(date_add(curdate(),interval -2 month),7)
                    and vtiger_crmentity.createdtime <vtiger_receivedpayments.reality_date
                    AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                    union ALL
                    SELECT vtiger_achievementallot.receivedpaymentsid,vtiger_users.id,vtiger_users.last_name AS user_name,(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))) as totalprice,left(vtiger_receivedpayments.reality_date,10) as receivedate,left(vtiger_crmentity.createdtime,10) as orderdate
                    from vtiger_salesorder
                    left join vtiger_servicecontracts on vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                    LEFT JOIN vtiger_receivedpayments ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    left join vtiger_achievementallot on vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                    left join vtiger_crmentity on vtiger_salesorder.salesorderid=vtiger_crmentity.crmid
                    where left(vtiger_crmentity.createdtime,10) BETWEEN date_add(curdate()-day(curdate())+6,interval -2 month) AND date_add(curdate()-day(curdate())+5,interval -1 month)
                    and  left(vtiger_receivedpayments.reality_date,10) <left(vtiger_crmentity.createdtime,10)
                    AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                    AND vtiger_receivedpayments.reality_date<date_add(curdate()-day(curdate())+1,interval -2 month)",array());
            $db->pquery("INSERT INTO vtiger_performance_evaluation(receivedpaymentsid,userid,last_name,totalprice,receivedate,orderdate)
                    SELECT vtiger_achievementallot.receivedpaymentsid,vtiger_users.id,vtiger_users.last_name AS user_name,(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))) as totalprice,
                    left(vtiger_receivedpayments.reality_date,10) as receivedate,left(vtiger_crmentity.createdtime,10) as orderdate
                    from vtiger_achievementallot
                    LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    LEFT JOIN vtiger_salesorder ON vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                    left join vtiger_crmentity on vtiger_salesorder.salesorderid=vtiger_crmentity.crmid
                    where left(vtiger_receivedpayments.reality_date,7)=left(date_add(curdate(),interval -1 month),7)
                    AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                    and left(vtiger_crmentity.createdtime,10) BETWEEN date_add(curdate()-day(curdate())+1,interval -1 month) AND DATE_ADD(curdate(),interval -day(curdate())+5 day) and vtiger_crmentity.createdtime >=vtiger_receivedpayments.reality_date
                    UNION ALL
                    SELECT vtiger_achievementallot.receivedpaymentsid,vtiger_users.id,vtiger_users.last_name AS user_name,(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))) as totalprice,left(vtiger_receivedpayments.reality_date,10) as receivedate,left(vtiger_crmentity.createdtime,10) as orderdate
                    from vtiger_achievementallot
                    LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    LEFT JOIN vtiger_salesorder ON vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                    left join vtiger_crmentity on vtiger_salesorder.salesorderid=vtiger_crmentity.crmid
                    where left(vtiger_receivedpayments.reality_date,7) =left(date_add(curdate(),interval -1 month),7)
                    and vtiger_crmentity.createdtime <vtiger_receivedpayments.reality_date
                    AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                    union ALL
                    SELECT vtiger_achievementallot.receivedpaymentsid,vtiger_users.id,vtiger_users.last_name AS user_name,(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))) as totalprice,left(vtiger_receivedpayments.reality_date,10) as receivedate,left(vtiger_crmentity.createdtime,10) as orderdate
                    from vtiger_salesorder
                    left join vtiger_servicecontracts on vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                    LEFT JOIN vtiger_receivedpayments ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    left join vtiger_achievementallot on vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                    left join vtiger_crmentity on vtiger_salesorder.salesorderid=vtiger_crmentity.crmid
                    where left(vtiger_crmentity.createdtime,10) BETWEEN date_add(curdate()-day(curdate())+6,interval -1 month) AND DATE_ADD(curdate(),interval -day(curdate())+5 day)
                    and  left(vtiger_receivedpayments.reality_date,10) <left(vtiger_crmentity.createdtime,10)
                    AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                    AND vtiger_receivedpayments.reality_date<date_add(curdate()-day(curdate())+1,interval -1 month)",array());
            $db->pquery("INSERT into vtiger_performance_evaluation(receivedpaymentsid,userid,last_name,totalprice,receivedate,orderdate)
                    SELECT vtiger_achievementallot.receivedpaymentsid,vtiger_users.id,vtiger_users.last_name AS user_name,(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))) as totalprice,
                    left(vtiger_receivedpayments.reality_date,10) as receivedate,left(vtiger_crmentity.createdtime,10) as orderdate
                    from vtiger_achievementallot
                    LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    LEFT JOIN vtiger_salesorder ON vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                    left join vtiger_crmentity on vtiger_salesorder.salesorderid=vtiger_crmentity.crmid
                    where left(vtiger_receivedpayments.reality_date,7)=left(curdate(),7)
                    AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                    and left(vtiger_crmentity.createdtime,10) BETWEEN DATE_ADD(curdate(),interval -day(curdate())+1 day) AND date_add(curdate()-day(curdate())+5,interval 1 month) and vtiger_crmentity.createdtime >=vtiger_receivedpayments.reality_date
                    UNION ALL
                    SELECT vtiger_achievementallot.receivedpaymentsid,vtiger_users.id,vtiger_users.last_name AS user_name,(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))) as totalprice,left(vtiger_receivedpayments.reality_date,10) as receivedate,left(vtiger_crmentity.createdtime,10) as orderdate
                    from vtiger_achievementallot
                    LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    LEFT JOIN vtiger_salesorder ON vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                    left join vtiger_crmentity on vtiger_salesorder.salesorderid=vtiger_crmentity.crmid
                    where left(vtiger_receivedpayments.reality_date,7) =left(curdate(),7)
                    AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                    and vtiger_crmentity.createdtime <vtiger_receivedpayments.reality_date
                    union ALL
                    SELECT vtiger_achievementallot.receivedpaymentsid,vtiger_users.id,vtiger_users.last_name AS user_name,(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))) as totalprice,left(vtiger_receivedpayments.reality_date,10) as receivedate,left(vtiger_crmentity.createdtime,10) as orderdate
                    from vtiger_salesorder
                    left join vtiger_servicecontracts on vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                    LEFT JOIN vtiger_receivedpayments ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    left join vtiger_achievementallot on vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                    left join vtiger_crmentity on vtiger_salesorder.salesorderid=vtiger_crmentity.crmid
                    where left(vtiger_crmentity.createdtime,10) BETWEEN DATE_ADD(curdate(),interval -day(curdate())+6 day) AND date_add(curdate()-day(curdate())+5,interval 1 month)
                    and  left(vtiger_receivedpayments.reality_date,10) <left(vtiger_crmentity.createdtime,10)
                    AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                    AND vtiger_receivedpayments.reality_date<DATE_ADD(curdate(),interval -day(curdate())+1 day)",array());
            $db->pquery("DELETE FROM vtiger_performance_evaluation WHERE userid IS NULL",array());
            $result1['msg']='更新完成......';
        }else{
            $interval=60-ceil(($nowtime-$resulttime)/60);
            $result1['msg']="请在{$interval}分钟后再更新";
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result1);
        $response->emit();
    }
}
