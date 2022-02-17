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

class Rsalesananalysis_selectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getCountsday');
        $this->exposeMethod('getcontractdetaillist');
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
    //打开时加载
    public function getCountsday(Vtiger_Request $request){
        $datetime=$request->get('datetime');
        $userid=$request->get('userid');
        $departmentid=$request->get('department');
        $dateyear=array();
        if(!empty($datetime)){
            //过滤一下把非法提交的年份过滤掉
            $temparr=array();
            foreach($datetime as $value){
                if(checkdate('6','6',$value)&& !in_array($value,$temparr)){
                    $temparr[]=$value;
                }
            }
            $tarr=array();
            $timearr=array();
            //按年来求月份从一份到十二月份
            if(count($temparr)>=1){
                foreach($temparr as $val){
                    $dateyear[]=$val;
                    for($i=1;$i<=12;$i++){
                        $k=$i<=9?'0'.$i:$i;
                        $j=$val.'-'.$k;
                        $tarr[]=$j;//生成sql查询的条件
                        $timearr[$val][$k]=0;//匹配sql生成的值
                    }
                }
            }else{
                $val=date('Y');
                $dateyear[]=$val;
                for($i=1;$i<=12;$i++){
                    $k=$i<=9?'0'.$i:$i;
                    $j=$val.'-'.$k;
                    $tarr[]=$j;//生成sql查询的条件
                    $timearr[$val][$k]=0;//匹配sql生成的值
                }
            }
            $tempdate=" IN ('".implode("','",$tarr)."')";

        }else{
            $tarr=array();
            $timearr=array();
            //按年来求月份从一份到十二月份
             $val=date('Y');
            $dateyear[]=$val;
            for($i=1;$i<=12;$i++){
                $k=$i<=9?'0'.$i:$i;
                $j=$val.'-'.$k;
                $tarr[]=$j;//生成sql查询的条件
                $timearr[$val][$k]=0;
            }
            $tempdate=" IN ('".implode("','",$tarr)."')";
        }
        if(empty($userid)){
            if(!empty($departmentid)&&$departmentid!='H1'){
                $userid=getDepartmentUser($departmentid);
                $where=getAccessibleUsers('Rsalesananalysis','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                $sql = ' AND vtiger_servicecontracts_divide.receivedpaymentownid in('.implode(',',$where).') AND vtiger_servicecontracts_divide.signdempart=vtiger_departments.departmentid';
            }else{
                $where=getAccessibleUsers('Rsalesananalysis','List',false);
                if($where!='1=1'){
                    $sql =' AND vtiger_servicecontracts_divide.signdempart=vtiger_departments.departmentid AND vtiger_servicecontracts_divide.receivedpaymentownid '.$where;
                }else{
                    $sql='';
                }
            }

        }else{
            $sql=" AND vtiger_servicecontracts_divide.signdempart=vtiger_departments.departmentid AND vtiger_servicecontracts_divide.receivedpaymentownid={$userid}";
        }
        //$datetime=date('Y-m-d');
        $db=PearDatabase::getInstance();
        $query="SELECT
                    TRUNCATE(sum(IFNULL(vtiger_servicecontracts.total,0)*vtiger_servicecontracts_divide.scalling/100),2) AS totals,
                    LEFT(vtiger_servicecontracts.returndate,7) AS daymonth
                FROM
                 vtiger_servicecontracts_divide
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_servicecontracts_divide.servicecontractid
                LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_servicecontracts_divide.receivedpaymentownid
 		LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                WHERE
                    vtiger_crmentity.deleted = 0
                AND vtiger_servicecontracts.modulestatus = 'c_complete'
                AND vtiger_servicecontracts.returndate IS NOT NULL
                AND LEFT (vtiger_servicecontracts.returndate,7)
                {$tempdate}
                {$sql}
                GROUP BY
                    LEFT (vtiger_servicecontracts.returndate,7)
                ORDER BY vtiger_servicecontracts.returndate";
        //echo $query;
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        $Contracts=$timearr;
        if($num>0){
            for($i=0;$i<$num;$i++){
                $temp=$db->query_result($result,$i,'daymonth');
                $Contracts[substr($temp,0,4)][substr($temp,5,2)]=$db->query_result($result,$i,'totals');
            }
        }
        $arr['Contracts']=$Contracts;
        /*$query1="SELECT
                    vtiger_users.last_name AS user_name,
                    vtiger_achievementallot.receivedpaymentownid AS receiveid,
                    TRUNCATE(sum(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))),2) as totalprice
                FROM
                    `vtiger_achievementallot`
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                WHERE
                    1=1
                    AND left(vtiger_receivedpayments.reality_date,10){$tempdate} {$sql}
                GROUP BY vtiger_achievementallot.receivedpaymentownid
                ORDER BY totalprice DESC";*/
        /*$query="SELECT
                    left(vtiger_receivedpayments.reality_date,7) AS daymonth,
                    TRUNCATE(sum(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum((IFNULL(vtiger_salesorderproductsrel.purchasemount,0))*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0)),2) as totalprice
                FROM
                    `vtiger_achievementallot`
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                LEFT JOIN vtiger_salesorder ON vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                WHERE
                    vtiger_receivedpayments.relatetoid>0
                AND vtiger_salesorder.salesorderid>0
                AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                AND left(vtiger_receivedpayments.reality_date,7)
                {$tempdate}
                {$sql}
                GROUP BY
                    left(vtiger_receivedpayments.reality_date,7)
                ORDER BY left(vtiger_receivedpayments.reality_date,7) ASC";*/
$query="SELECT if(vtiger_performance_evaluation.orderdate>vtiger_performance_evaluation.receivedate,left(vtiger_performance_evaluation.orderdate,7),left(vtiger_performance_evaluation.receivedate,7)) AS daymonth,userid AS receiveid,sum(totalprice) AS totalprice
                FROM vtiger_performance_evaluation
                WHERE 1=1 {$sql}  GROUP BY if(vtiger_performance_evaluation.orderdate>vtiger_performance_evaluation.receivedate,left(vtiger_performance_evaluation.orderdate,7),left(vtiger_performance_evaluation.receivedate,7))  ORDER BY left(vtiger_performance_evaluation.receivedate,7) ASC
";
        $query=str_replace('vtiger_servicecontracts_divide.receivedpaymentownid','userid',$query);
        //echo $query;
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        $Payments=$timearr;
        if($num>0){
            for($i=0;$i<$num;$i++){
                $temp=$db->query_result($result,$i,'daymonth');
                $Payments[substr($temp,0,4)][substr($temp,5,2)]=$db->query_result($result,$i,'totalprice');
            }
        }
        $arr['Payments']=$Payments;
        $arr['dateyear']=$dateyear;
        $arr=json_decode(json_encode(new arrayObject($arr)),true);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
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
    public function getcontractdetaillist(Vtiger_Request $request){
        $tempdate=$request->get('datetime');
        $departmentid=$request->get('department');

            if(!empty($departmentid)&&$departmentid!='H1'){
                $userid=getDepartmentUser($departmentid);
                $where=getAccessibleUsers('Rsalesananalysis','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                $sql = ' AND vtiger_servicecontracts.receiveid in('.implode(',',$where).') AND vtiger_servicecontracts_divide.signdempart=vtiger_departments.departmentid';
            }else{
                $where=getAccessibleUsers('Rsalesananalysis','List',false);
                if($where!='1=1'){
                    $sql =' AND vtiger_servicecontracts.receiveid  '.$where.' AND vtiger_servicecontracts_divide.signdempart=vtiger_departments.departmentid';
                }else{
                    $sql='';
                }
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
                    (SELECT	CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM	vtiger_departments WHERE departmentid = (SELECT	departmentid FROM	vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE	vtiger_servicecontracts_divide.receivedpaymentownid= vtiger_users.id) AS receiveid,
                 vtiger_servicecontracts.returndate,
                 truncate(IFNULL(vtiger_servicecontracts.total,0)*vtiger_servicecontracts_divide.scalling/100,2) AS total,
                 IFNULL(REPLACE(vtiger_servicecontracts.productsearchid,'<br>',','),'') AS productsearchid,
                 IFNULL(vtiger_servicecontracts.firstreceivepaydate,'') AS firstreceivepaydate,
                 vtiger_servicecontracts.servicecontractsid
                FROM
                    vtiger_servicecontracts_divide
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_servicecontracts_divide.servicecontractid
                LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid
		LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_servicecontracts_divide.receivedpaymentownid
                LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
                LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_servicecontracts.productid
                WHERE
                    1 = 1
                AND vtiger_crmentity.deleted = 0
                AND vtiger_servicecontracts.modulestatus = 'c_complete'
                {$sql}
                AND vtiger_servicecontracts.returndate IS NOT NULL
                AND vtiger_servicecontracts.returndate!=''
                AND left(vtiger_servicecontracts.returndate,7)='{$tempdate}'
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
        $tempdate=$request->get('datetime');
        $departmentid=$request->get('department');

        if(!empty($departmentid)&&$departmentid!='H1'){
            $userid=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('Rsalesananalysis','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
           $sql = ' AND `vtiger_achievementallot`.receivedpaymentownid in('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers('Rsalesananalysis','List',false);
            if($where!='1=1'){
                $sql =' AND `vtiger_achievementallot`.receivedpaymentownid  '.$where;
            }else{
                $sql='';
            }
        }
        $db=PearDatabase::getInstance();
        $query1="SELECT
	DISTINCT vtiger_achievementallot.achievementallotid,
                    vtiger_users.last_name,
                    vtiger_account.accountname,
                    vtiger_account.accountid,
                    vtiger_servicecontracts.total,
                    vtiger_achievementallot.businessunit,
                    vtiger_servicecontracts.contract_no,
                    vtiger_servicecontracts.servicecontractsid AS cid,
                    TRUNCATE((IFNULL((SELECT sum((IFNULL(vtiger_salesorderproductsrel.purchasemount,0))*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0)+IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0)),2) as sss,
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
                  vtiger_receivedpayments.relatetoid>0
                AND vtiger_salesorder.salesorderid>0
		AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0) 
                   {$sql}
                AND if(vtiger_performance_evaluation.orderdate>vtiger_performance_evaluation.receivedate,left(vtiger_performance_evaluation.orderdate,7)='{$tempdate}',left(vtiger_performance_evaluation.receivedate,7)='{$tempdate}')
                ORDER BY
                    vtiger_servicecontracts.servicecontractsid DESC LIMIT 1000";
        $result1=$db->run_query_allrecords($query1);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result1);
        $response->emit();
    }


}
