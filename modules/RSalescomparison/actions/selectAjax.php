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

class RSalescomparison_selectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getCountsday');
        $this->exposeMethod('getdetaillist');
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
        $departmentid=$request->get('department');
        $userid=$request->get('userdata');
        if(empty($datetime)){
            $datetime=date("Y");
        }
        $flag=0;
        $result=array();
        $query1='SELECT ';
        $query2='SELECT ';
        $db=PearDatabase::getInstance();
        if($userid!='null'&&!empty($userid)){
            $query="SELECT id,last_name FROM vtiger_users WHERE id in(".implode(',',$userid).") limit 5";
            $uresult=$db->pquery($query);
            $num=$db->num_rows($uresult);
            if($num>0){
                for($i=0;$i<$num;++$i){
                    for($j=1;$j<=12;++$j) {
                        $utime=$j<10?$datetime.'-0'.$j:$datetime.'-'.$j;
                        $arrnum['user' . $db->query_result($uresult, $i, 'id')][$utime] = 1;
                    }
                    $result['newdepartment']['user'.$db->query_result($uresult,$i,'id')]=$db->query_result($uresult,$i,'last_name');
                    $query1.="sum(if(vtiger_servicecontracts_divide.receivedpaymentownid IN (".$db->query_result($uresult,$i,'id')."),truncate(IFNULL(vtiger_servicecontracts.total,0)*vtiger_servicecontracts_divide.scalling/100,2),0)) as user".$db->query_result($uresult,$i,'id').",";
                    $query2.="truncate(sum(if(vtiger_achievementallot.receivedpaymentownid  IN (".$db->query_result($uresult,$i,'id')."),(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum((IFNULL(vtiger_salesorderproductsrel.purchasemount,0))*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))),0)),2) as user".$db->query_result($uresult,$i,'id').",";
                }
                $flag=1;
            }
        }else{
            if($departmentid=="null"||empty($departmentid)){
                $departmentid=array();
                $departmentid[]='H1';
            }
            $cachedepartment=getDepartment();
            //$arrnum=array();//部门中有多少个人
            //部门不能超过5个
            $arrnum=$this->getcurrentmonth($departmentid);

            for($i=0;$i<count($departmentid)&&$i<5;++$i){
                $userid=getDepartmentUser($departmentid[$i]);
                $where=getAccessibleUsers('RSalescomparison','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                //没有负责人的部门直接不查询该部门
                if(empty($where)||count($where)==0){
                    continue;
                }
                $flag=1;
                //$arrnum[strtolower($departmentid[$i])]=$nowdepartement($where);
                //$arrnum[strtolower($departmentid[$i])]=$nowdepartement[strtolower($departmentid[$i])];
                $result['newdepartment'][strtolower($departmentid[$i])]=str_replace(array('|','—'),array('',''),$cachedepartment[$departmentid[$i]]);
                $query1.="sum(if(vtiger_servicecontracts.receiveid IN (".implode(',',$where)."),truncate(IFNULL(vtiger_servicecontracts.total,0)*vtiger_servicecontracts_divide.scalling/100,2),0)) as {$departmentid[$i]},";
                //$query2.="truncate(sum(if(vtiger_achievementallot.receivedpaymentownid  IN (".implode(',',$where)."),(vtiger_achievementallot.businessunit-(IFNULL((SELECT sum((IFNULL(vtiger_salesorderproductsrel.purchasemount,0))*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))-(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))),0)),2) as {$departmentid[$i]},";
		$query2.="sum(if(vtiger_performance_evaluation.userid  IN (".implode(',',$where)."),vtiger_performance_evaluation.totalprice,0)) as {$departmentid[$i]},";
            }
        }
        if($flag==0){
            $result['newdepartment']['hno']='';
            $result['Contracts']=array('hno'=>'','returntime'=>'');
            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($result);
            $response->emit();
            exit;
        }
        //echo $query1;
        $query1.="left(vtiger_servicecontracts.returndate,7) as returntime
            FROM
                vtiger_servicecontracts_divide
            LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_servicecontracts_divide.servicecontractid
            LEFT JOIN vtiger_users ON vtiger_users.id =  vtiger_servicecontracts_divide.receivedpaymentownid
            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
            WHERE
                vtiger_crmentity.deleted = 0
                AND vtiger_servicecontracts.modulestatus = 'c_complete'
                AND left(vtiger_servicecontracts.returndate,4) ='{$datetime}'
                AND vtiger_servicecontracts.returndate is not null
            GROUP BY
            left(vtiger_servicecontracts.returndate,7)";
 $query2.=" if(vtiger_performance_evaluation.orderdate>vtiger_performance_evaluation.receivedate,left(vtiger_performance_evaluation.orderdate,7),left(vtiger_performance_evaluation.receivedate,7)) as returntime
                FROM vtiger_performance_evaluation
                WHERE 1=1 AND if(vtiger_performance_evaluation.orderdate>vtiger_performance_evaluation.receivedate,left(vtiger_performance_evaluation.orderdate,4)='{$datetime}',left(vtiger_performance_evaluation.receivedate,4)='{$datetime}') GROUP BY if(vtiger_performance_evaluation.orderdate>vtiger_performance_evaluation.receivedate,left(vtiger_performance_evaluation.orderdate,7),left(vtiger_performance_evaluation.receivedate,7))  ORDER BY left(vtiger_performance_evaluation.receivedate,7) ASC
";
        /*$query2.=" left(vtiger_receivedpayments.reality_date,7) as returntime
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
                AND left(vtiger_receivedpayments.reality_date,4) ='{$datetime}'
                 GROUP BY
                left(vtiger_receivedpayments.reality_date,7)";*/
        //echo $query1;
        //echo $query2;

        $result['Contracts']=$db->run_query_allrecords($query1);
        //求平均值
        $i=0;
        foreach($result['Contracts'] as $value){
            foreach($value as $avgkey=>$avgvalue){
                if(!is_numeric($avgkey)){
                    if($avgkey=='returntime'){
                        $result['Contractsavg'][$i][$avgkey]=$avgvalue;
                    }else{
                        $num=empty($arrnum[$avgkey][$value['returntime']])?1:$arrnum[$avgkey][$value['returntime']];
                        $result['Contractsavg'][$i][$avgkey]=round($avgvalue/$num,2);
                    }
                }
            }
            ++$i;
        }
        $result['Payment']=$db->run_query_allrecords($query2);
        $i=0;
        foreach($result['Payment'] as $valu){
            foreach($valu as $avgke=>$avgvalu){
                if(!is_numeric($avgke)){
                    if($avgke=='returntime'){
                        $result['Paymentavg'][$i][$avgke]=$avgvalu;
                    }else{
                        $num=empty($arrnum[$avgkey][$value['returntime']])?1:$arrnum[$avgkey][$value['returntime']];
                        $result['Paymentavg'][$i][$avgke]=round($avgvalu/$num,2);
                    }
                }
            }
            ++$i;
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }

    public function getUsers(Vtiger_Request $request){
        $departmentid=$request->get('department');
        if(!empty($departmentid)&&$departmentid!='H1'){
            $userid=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('RSalescomparison','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery = ' AND id in('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers('RSalescomparison','List',false);
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
        $datauserid=$request->get('datauserid');

        if(is_numeric($datauserid)){
            $datauserids="=".(int)$datauserid;
        }else{
            $userid=getDepartmentUser(strtoupper($datauserid));
            $where=getAccessibleUsers('RSalescomparison','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $datauserids = ' in('.implode(',',$where).')';
        }
        if(empty($datetime)) {
            $tempdate = date('Y-m');
        }else{
            $tempdate=" ='{$datetime}'";
        }
        $db=PearDatabase::getInstance();
        $query1="SELECT
                    vtiger_servicecontracts.contract_no,
                    vtiger_servicecontracts.servicecontractsid AS cid,
                    (vtiger_account.accountname) AS sc_related_to,
                    vtiger_servicecontracts.sc_related_to AS sc_related_to_reference,
                    vtiger_servicecontracts.contract_type,
                    IFNULL(left(vtiger_servicecontracts.receivedate,10),'') AS receivedate,
                    (SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid =(SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_servicecontracts.signid = vtiger_users.id ) AS signid,
                    IFNULL(vtiger_servicecontracts.signdate,'') AS signdate,
                    (SELECT	CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM	vtiger_departments WHERE departmentid = (SELECT	departmentid FROM	vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE vtiger_servicecontracts_divide.receivedpaymentownid = vtiger_users.id) AS receiveid,
                 vtiger_servicecontracts.returndate,
                 TRUNCATE(IFNULL(vtiger_servicecontracts.total,0)*vtiger_servicecontracts_divide.scalling/100,2) AS total,
                 IFNULL(REPLACE(vtiger_servicecontracts.productsearchid,'<br>',','),'') AS productsearchid,
                 IFNULL(vtiger_servicecontracts.firstreceivepaydate,'') AS firstreceivepaydate,
                 vtiger_servicecontracts.servicecontractsid
                FROM
                    vtiger_servicecontracts_divide
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_servicecontracts_divide.servicecontractid
                LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid
                LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
                LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_servicecontracts.productid
                WHERE
                    1 = 1
                AND vtiger_crmentity.deleted = 0
                AND vtiger_servicecontracts.modulestatus = 'c_complete'
                AND vtiger_servicecontracts_divide.receivedpaymentownid{$datauserids}
                AND vtiger_servicecontracts.returndate IS NOT NULL
                AND vtiger_servicecontracts.returndate!=''
                AND left(vtiger_servicecontracts.returndate,7){$tempdate}
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
        $datauserid=$request->get('datauserid');

        if(is_numeric($datauserid)){
            $datauserids="=".(int)$datauserid;
        }else{
            $userid=getDepartmentUser(strtoupper($datauserid));
            $where=getAccessibleUsers('RSalescomparison','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $datauserids = ' in('.implode(',',$where).')';
        }
        if(empty($datetime)) {
            $tempdate = date('Y-m');
        }else{
            $tempdate=" ='{$datetime}'";
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
                    TRUNCATE(((IFNULL((SELECT sum((IFNULL(vtiger_salesorderproductsrel.purchasemount,0))*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) from vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_receivedpayments.relatetoid),0))+(IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments_extra.extra_price,0) * vtiger_achievementallot.scalling/100) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid = vtiger_receivedpayments.receivedpaymentsid),0))),2) as sss,
                    vtiger_receivedpayments.reality_date,
                    vtiger_achievementallot.scalling
                FROM
                    `vtiger_achievementallot`
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to
                LEFT JOIN vtiger_salesorder ON vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
		LEFT JOIN vtiger_performance_evaluation ON vtiger_performance_evaluation.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid
                WHERE
                    vtiger_receivedpayments.relatetoid>0
                AND vtiger_salesorder.salesorderid>0
                AND vtiger_achievementallot.receivedpaymentownid{$datauserids}
                AND (vtiger_receivedpayments.isguarantee IS NULL OR vtiger_receivedpayments.isguarantee=0)
                AND if(vtiger_performance_evaluation.orderdate>vtiger_performance_evaluation.receivedate,left(vtiger_performance_evaluation.orderdate,7){$tempdate},left(vtiger_performance_evaluation.receivedate,7){$tempdate})
                ORDER BY
                    vtiger_servicecontracts.servicecontractsid DESC LIMIT 1000";
        //echo $query1;
        $result1=$db->run_query_allrecords($query1);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result1);
        $response->emit();
    }
    //求当所有的在每个月的在职人数
    public function getcurrentmonth($departments=array()){
        $db=PearDatabase::getInstance();
        $query="SELECT ";
        foreach($departments as $value){
            $query.="sum(if(userid IN(SELECT userid FROM vtiger_user2department  LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
WHERE CONCAT(vtiger_departments.parentdepartment,'::') REGEXP '{$value}::' ),1,0)) as {$value},";
        }
        $query.="activedate FROM `vtiger_useractivemonth` GROUP BY activedate";
        //echo $query;
        $result=$db->run_query_allrecords($query);
        $arr=array();
        foreach($result as $values){
            foreach($values as $key=>$value){
                if(is_numeric($key)||$key=='activedate'){
                    continue;
                }
                $value=$value==0?1:$value;//如果出现为0的防止相除出现问题设为1
                $arr[$key][$values['activedate']]=$value;
            }
        }
        return $arr;


    }

}
