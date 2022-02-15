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

class Reporting_selectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getCountsday');
        $this->exposeMethod('getdetaillist');
        $this->exposeMethod('getUsers');
        $this->exposeMethod('getrefreshday');
        $this->exposeMethod('getvisitstatistics');
        $this->exposeMethod('getvisitrefresh');
        $this->exposeMethod('getvisitexp');
        $this->exposeMethod('getaccountstatistics');
        $this->exposeMethod('getaccountstatisticsexport');
        $this->exposeMethod('getentrystatistics');
        $this->exposeMethod('getentrystatisticsexp');
        $this->exposeMethod('getperformance');
        $this->exposeMethod('getPerformanceexp');
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
            if ($datetime == '') {

                $tempdate = " ='" . date('Y-m-d') . "'";
            }
        }
        $query='SELECT ';
        $arr=array();
        $cachedepartment=getDepartment();
        $db=PearDatabase::getInstance();
        if(empty($userid)||!is_numeric($userid)){

            if(!empty($departmentid)&&$departmentid!='null'){
                foreach($departmentid as $value){
                    $userid=getDepartmentUser($value);
                    $where=getAccessibleUsers('Reporting','List',true);
                    if($where!='1=1'){
                        $where=array_intersect($where,$userid);
                    }else{
                        $where=$userid;
                    }
                    if(empty($where)||count($where)==0){
                        continue;
                    }
                    $arr['department'][strtolower($value)]=str_replace(array('|','—'),array('',''),$cachedepartment[$value]);
                    $query.='sum(if(smownerid in('.implode(',',$where).'),countnums,0)) as '.$value.',';

                }
            }else{
                $where=getAccessibleUsers('Reporting','List',false);
                if($where!='1=1'){
                    $arr['department'][strtolower('H1')]=str_replace(array('|','—'),array('',''),$cachedepartment['H1']);
                    $query.='sum(if(smownerid in('.implode(',',$where).'),countnums,0)) as H1,';
                }else{
                    $query.='sum(IFNULL(countnums,0)) as H1,';
                    $arr['department'][strtolower('H1')]=str_replace(array('|','—'),array('',''),$cachedepartment['H1']);
                }
            }
        }else{

            $query.='sum(if(smownerid ='.$userid.',countnums,0)) as H1,';
            $arr['department'][strtolower('H1')]=str_replace(array('|','—'),array('',''),$cachedepartment['H1']);
        }
        $query.='classification FROM vtiger_reporting_view WHERE createdtime'.$tempdate.' GROUP BY classification';
        //echo $query;

        $result=$db->run_query_allrecords($query);
        foreach($result as $value){
            foreach($value as $key=>$val){
                if(!is_numeric($key)){
                    $arr['dataall'][$key][]=$val;
                }
            }
        }
        if(empty($arr['dataall'])){
            $arr['dataall']['empty']=array();
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();
        exit;
        //$datetime=date('Y-m-d');

        $query="SELECT
                    IFNULL(sum(vtiger_reporting_view.dayforp),0) as dayforp,
                    IFNULL(sum(vtiger_reporting_view.dayforforp),0) as dayforforp,
                    IFNULL(sum(vtiger_reporting_view.daycounts),0) as daycounts,
                    IFNULL(sum(vtiger_reporting_view.dayvisiting),0) as dayvisiting,
                    IFNULL(sum(vtiger_reporting_view.gonghai),0) as gonghai,
                    IFNULL(sum(vtiger_reporting_view.daysaler),0) as daysaler,
                    IFNULL(sum(vtiger_reporting_view.daynotfollow),0) as daynotfollow,
                    IFNULL(sum(vtiger_reporting_view.dayallvisiting),0) as dayallvisiting
                FROM vtiger_reporting_view
                WHERE createdtime{$tempdate} {$sql}";
        //echo $query;
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        if($num<1){
            $arr=array();
            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($arr);
            $response->emit();
            return;
        }
        $arr=array();
        for($i=0;$i<$num;$i++){
            $arr['dayforp']=$db->query_result($result,$i,'dayforp');
            $arr['gonghai']=$db->query_result($result,$i,'gonghai');
            $arr['dayforforp']=$db->query_result($result,$i,'dayforforp');
            $arr['daycounts']=$db->query_result($result,$i,'daycounts');
            $arr['dayvisiting']=$db->query_result($result,$i,'dayvisiting');
            $arr['dayallvisiting']=$db->query_result($result,$i,'dayallvisiting');
            $arr['daynotfollow']=$db->query_result($result,$i,'daynotfollow');
            $arr['daysaler']=$db->query_result($result,$i,'daysaler');
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();
    }
    //按条件查询
    public function getdetaillist(Vtiger_Request $request){
        $dataIndex=$request->get('dataIndex');
        $datatime=$request->get('datetime');
        $enddatetime=$request->get('enddatetime');
        $userid=$request->get('userid');
        $paramcolum=$request->get('paramcolum');
        $departmentid=$request->get('department');
        $departmentid=strtoupper($departmentid);
        if(strtotime($datatime)>strtotime($enddatetime)){
            $tempdate=" BETWEEN TO_DAYS('{$enddatetime}') AND TO_DAYS('{$datatime}')";
        }elseif(strtotime($datatime)<strtotime($enddatetime)){
            $tempdate=" BETWEEN TO_DAYS('{$datatime}') AND TO_DAYS('{$enddatetime}')";
        }else{
            $tempdate=" =TO_DAYS('{$datatime}')";
        }
        if($datatime==''){
            $tempdate=" =TO_DAYS('{$datatime}')";
        }
        $fliter=$request->get('fliter');
        if($fliter=='thisweek'){
            $lastday=date('Y-m-d',strtotime("Sunday"));
            $firstday=date('Y-m-d',strtotime("$lastday -6 days"));
            $tempdate = " BETWEEN TO_DAYS('{$firstday}') AND TO_DAYS('{$lastday}')";
        }else if($fliter=='thismonth'){
            $firstday = date('Y-m-01');
            $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
            $tempdate = " BETWEEN TO_DAYS('{$firstday}') AND TO_DAYS('{$lastday}')";
        }
        if(empty($userid)){
            if(!empty($departmentid)&&$departmentid!='H1'){
                $userid=getDepartmentUser($departmentid);
                $where=getAccessibleUsers('Reporting','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                $sql = ' AND vtiger_crmentity.smownerid in('.implode(',',$where).')';
            }else{
                $where=getAccessibleUsers('Reporting','List',false);
                if($where!='1=1'){
                    $sql =' AND vtiger_crmentity.smownerid '.$where;
                }else{
                    $sql='';
                }
            }

        }else{
            $sql=" AND vtiger_crmentity.smownerid={$userid}";
        }

        switch($paramcolum){
            //每日新增客户数
            case 'daycounts':
                $query="SELECT
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.servicetype,'') AS servicetype,
                            IFNULL(vtiger_account.accountrank,'') AS accountrank,
                            IFNULL(vtiger_account.linkname,'') AS linkname,
                            IFNULL(vtiger_departments.departmentname,'--') AS department,
                            IFNULL(vtiger_users.last_name,'--'	) AS smownerid,
                            IFNULL(vtiger_account.industry,'') AS industry,
                            IFNULL(vtiger_account.annual_revenue,'') AS annual_revenue,
                            IFNULL(vtiger_account.address,'') AS address,
                            IFNULL(vtiger_account.makedecision,'') AS makedecision,
                            IFNULL(vtiger_account.business,'') AS business,
                            IFNULL(vtiger_account.regionalpartition,'') AS regionalpartition,
                            IFNULL(vtiger_account.title,'') AS title,
                            IFNULL(vtiger_account.leadsource,'') AS leadsource,
                            IFNULL(vtiger_account.linkname,'') AS linkname,
                            IFNULL(vtiger_account.businessarea,'') AS businessarea,
                            IFNULL(vtiger_crmentity.createdtime,'') AS createdtime,
                            IFNULL(vtiger_account.customerproperty,'') AS customerproperty,
                            IFNULL(vtiger_account.accountid,'') AS accountid
                        FROM
                            vtiger_account
                        LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE
                            1 = 1
                        AND vtiger_crmentity.deleted = 0
                        AND TO_DAYS(vtiger_crmentity.createdtime){$tempdate}
                        {$sql}
                        ORDER BY
                            vtiger_account.mtime DESC LIMIT 1000";
                break;
            //公海转入客户数
            case 'gonghai':
                $query="SELECT
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.servicetype,'') AS servicetype,
                            IFNULL(vtiger_account.accountrank,'') AS accountrank,
                            IFNULL(vtiger_account.linkname,'') AS linkname,
                            IFNULL(vtiger_departments.departmentname,'--') AS department,
                            IFNULL(vtiger_users.last_name,'--'	) AS smownerid,
                            IFNULL(vtiger_account.industry,'') AS industry,
                            IFNULL(vtiger_account.annual_revenue,'') AS annual_revenue,
                            IFNULL(vtiger_account.address,'') AS address,
                            IFNULL(vtiger_account.makedecision,'') AS makedecision,
                            IFNULL(vtiger_account.business,'') AS business,
                            IFNULL(vtiger_account.regionalpartition,'') AS regionalpartition,
                            IFNULL(vtiger_account.title,'') AS title,
                            IFNULL(vtiger_account.leadsource,'') AS leadsource,
                            IFNULL(vtiger_account.linkname,'') AS leadsource,
                            IFNULL(vtiger_account.businessarea,'') AS businessarea,
                            IFNULL(vtiger_crmentity.createdtime,'') AS createdtime,
                            IFNULL(vtiger_account.customerproperty,'') AS customerproperty,
                            IFNULL(vtiger_account.accountid,'') AS accountid
                        FROM
                            vtiger_account
                        LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE
                            1 = 1
                        AND vtiger_crmentity.deleted = 0
                        AND EXISTS(SELECT 1 FROM vtiger_accountgonghaioutrel WHERE vtiger_accountgonghaioutrel.accountid=vtiger_account.accountid AND TO_DAYS(vtiger_accountgonghaioutrel.createdtime){$tempdate} )
                        {$sql}
                        ORDER BY
                            vtiger_account.mtime DESC LIMIT 1000";
                break;
            //每日新增客户转40%客户数
            case 'dayforforp':
                $query="SELECT
                            IFNULL(vtiger_crmentity.createdtime,'') AS createdtime,
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.accountid,'') AS accountid,
                            IFNULL(vtiger_account.makedecision,'') AS makedecision,
                            IFNULL((SELECT (SELECT departmentname	FROM vtiger_departments WHERE	departmentid =(SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)) AS last_name FROM vtiger_users	WHERE	vtiger_crmentity.smownerid = vtiger_users.id),'--'	) AS department,
                            IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM vtiger_departments WHERE	departmentid =(SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users	WHERE	vtiger_crmentity.smownerid = vtiger_users.id),'--'	) AS smownerid,
                            IFNULL((SELECT startdate FROM vtiger_visitingorder WHERE vtiger_visitingorder.related_to=vtiger_account.accountid ORDER BY startdate ASC limit 1),'') AS firstvisittime
                        FROM
                            vtiger_account
                        LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_accountrankhistory ON vtiger_accountrankhistory.accountid = vtiger_account.accountid
                        WHERE
                            vtiger_accountrankhistory.newaccountrank = 'forp_notv'
                        AND TO_DAYS(vtiger_accountrankhistory.createdtime)=TO_DAYS(vtiger_crmentity.createdtime)
                        AND
                            TO_DAYS(vtiger_accountrankhistory.createdtime) {$tempdate}
                        {$sql} LIMIT 1000";
                break;
            //每日新增40%客户数
            case 'dayforp':
                $query="SELECT
                            IFNULL(vtiger_crmentity.createdtime,'') AS createdtime,
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.accountid,'') AS accountid,
                            IFNULL(vtiger_account.makedecision,'') AS makedecision,
                            IFNULL((SELECT (SELECT departmentname	FROM vtiger_departments WHERE	departmentid =(SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)) AS last_name FROM vtiger_users	WHERE	vtiger_crmentity.smownerid = vtiger_users.id),'--'	) AS department,
                            IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM vtiger_departments WHERE	departmentid =(SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users	WHERE	vtiger_crmentity.smownerid = vtiger_users.id),'--'	) AS smownerid,
                            IFNULL((SELECT startdate FROM vtiger_visitingorder WHERE vtiger_visitingorder.related_to=vtiger_account.accountid ORDER BY startdate ASC limit 1),'') AS firstvisittime
                        FROM
                            vtiger_account
                        LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_accountrankhistory ON vtiger_accountrankhistory.accountid = vtiger_account.accountid
                        WHERE
                            vtiger_accountrankhistory.newaccountrank = 'forp_notv'
                        AND
                            TO_DAYS(vtiger_accountrankhistory.createdtime) {$tempdate}
                        {$sql} LIMIT 1000";
                break;
            //每日拜访客户数
            case 'dayvisiting':
                $query="SELECT
                            IFNULL(vtiger_departments.departmentname,''	) AS department,
                            IFNULL(CONCAT(vtiger_users.last_name,if(vtiger_users.`status`='Active','','[离职]')),'') AS smownerid,
                            IFNULL(left(vtiger_visitingorder.startdate,10),'') AS visitingtime,
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.accountrank,'') AS accountrank,
                            IFNULL(vtiger_account.visitingtimes,'') AS visitingtimes,
                            IFNULL(vtiger_visitingorder.contacts,'') AS contacts,
                            IFNULL(vtiger_visitingorder.purpose,'') AS purpose,
                            vtiger_visitingorder.related_to AS accountid,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT title FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as title,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.makedecision,(SELECT makedecision FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as makedecision,
                            IFNULL((SELECT GROUP_CONCAT(vtiger_products.productname) FROM vtiger_products WHERE  FIND_IN_SET(vtiger_products.productid,REPLACE(vtiger_account.servicetype,' |##| ',','))),'') as servicetypename
                        FROM
                                vtiger_visitingorder
                        LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_visitingorder.related_to
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_visitingorder.extractid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE
                            vtiger_visitingorder.modulestatus='c_complete'
                        AND vtiger_visitingorder.related_to>0
                        AND vtiger_crmentity.deleted = 0
                        AND TO_DAYS(vtiger_visitingorder.startdate){$tempdate}{$sql} LIMIT 1000";
                $query=str_replace('vtiger_crmentity.smownerid','vtiger_visitingorder.extractid',$query);
                break;
            //每日成交客户数
            case 'dayallvisiting':
                $query="SELECT
                            IFNULL(vtiger_departments.departmentname,''	) AS department,
                            IFNULL(CONCAT(vtiger_users.last_name,if(vtiger_users.`status`='Active','','[离职]')),'') AS smownerid,
                            IFNULL(left(vtiger_visitingorder.startdate,10),'') AS visitingtime,
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.accountrank,'') AS accountrank,
                            IFNULL(vtiger_account.visitingtimes,'') AS visitingtimes,
                            IFNULL(vtiger_visitingorder.contacts,'') AS contacts,
                            IFNULL(vtiger_visitingorder.purpose,'') AS purpose,
                            vtiger_visitingorder.related_to AS accountid,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT title FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as title,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.makedecision,(SELECT makedecision FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as makedecision,
                            IFNULL((SELECT GROUP_CONCAT(vtiger_products.productname) FROM vtiger_products WHERE  FIND_IN_SET(vtiger_products.productid,REPLACE(vtiger_account.servicetype,' |##| ',','))),'') as servicetypename
                        FROM
                                vtiger_visitingorder
                        LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_visitingorder.related_to
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_visitingorder.extractid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE
                        vtiger_crmentity.deleted = 0
                        AND vtiger_visitingorder.modulestatus='a_normal'
                        AND TO_DAYS(vtiger_visitingorder.startdate){$tempdate}{$sql} LIMIT 1000";
                $query=str_replace('vtiger_crmentity.smownerid','vtiger_visitingorder.extractid',$query);
                break;
                case 'daynotfollow':
                $query="SELECT
                            IFNULL(vtiger_departments.departmentname,''	) AS department,
                            IFNULL(CONCAT(vtiger_users.last_name,if(vtiger_users.`status`='Active','','[离职]')),'') AS smownerid,
                            IFNULL(left(vtiger_visitingorder.enddate,16),'') AS visitingtime,
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.visitingtimes,'') AS visitingtimes,
                            IFNULL(vtiger_visitingorder.contacts,'') AS contacts,
                            IFNULL(vtiger_visitingorder.purpose,'') AS purpose,
                            vtiger_visitingorder.related_to AS accountid,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT title FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as title,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.makedecision,(SELECT makedecision FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as makedecision,
                            IFNULL((SELECT GROUP_CONCAT(vtiger_products.productname) FROM vtiger_products WHERE  FIND_IN_SET(vtiger_products.productid,REPLACE(vtiger_account.servicetype,' |##| ',','))),'') as servicetypename
                        FROM
                                vtiger_visitingorder
                        LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_visitingorder.related_to
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_visitingorder.extractid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE
                        vtiger_crmentity.deleted = 0
                        AND vtiger_visitingorder.dayfollowup!='是'
                        AND vtiger_visitingorder.modulestatus='c_complete'
                        AND TO_DAYS(DATE_SUB(vtiger_visitingorder.enddate,interval -1 day)){$tempdate}{$sql} LIMIT 1000";
                        //AND TO_DAYS(vtiger_visitingorder.enddate){$tempdate}{$sql} LIMIT 1000";
                $query=str_replace('vtiger_crmentity.smownerid','vtiger_visitingorder.extractid',$query);
                break;
            case 'daysaler':
                $query="SELECT
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            vtiger_servicecontracts.sc_related_to AS accountid,
                            IFNULL(left(vtiger_users.user_entered,10),'') AS user_entered,
                            IFNULL(vtiger_departments.departmentname,'') AS department,
                            IFNULL(vtiger_account.industry,'') AS industry,
                            IFNULL(vtiger_account.visitingtimes,'') AS visitingtimes,
                            IFNULL(CONCAT(vtiger_users.last_name,IF(vtiger_users.`status` = 'Active','','[离职]')),'') as smownerid,
                            IFNULL((SELECT ss.last_name FROM vtiger_users ss WHERE ss.id=vtiger_users.reports_to_id),'') AS report_name,
                            IFNULL(vtiger_servicecontracts.total,'') AS salescommission,
                            IF(vtiger_servicecontracts.total-(IFNULL(vtiger_receivedpayments.unit_price,0))>0,'否','是') AS until_price,
                            replace(IFNULL(vtiger_servicecontracts.productsearchid,''),'<br>',',　') AS productname,
                            IFNULL(vtiger_servicecontracts.firstreceivepaydate,'') as saleorderlastdealtime,
                            IFNULL(vtiger_servicecontracts.servicecontractsid,'') as c_id,
                            IFNULL(vtiger_servicecontracts.contract_no,'') as c_no
                        FROM
                            vtiger_servicecontracts
                        LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
                        LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_servicecontracts.productid
                        LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid
                        LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_servicecontracts.receiveid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
                        WHERE
                            1 = 1
                        AND vtiger_crmentity.deleted = 0
                        AND vtiger_servicecontracts.modulestatus='c_complete'
                        AND vtiger_servicecontracts.firstreceivepaydate IS NOT NULL
                        AND vtiger_servicecontracts.firstreceivepaydate != ''
                        AND vtiger_account.accountid>0
                        AND TO_DAYS(vtiger_servicecontracts.firstreceivepaydate){$tempdate}
                        {$sql}
                        GROUP BY vtiger_servicecontracts.servicecontractsid";
                $query=str_replace('vtiger_crmentity.smownerid','vtiger_servicecontracts.receiveid',$query);
                break;
            //已跟进未签到的拜访单数
            case 'didnotsignup':
                $query="SELECT
                            IFNULL(vtiger_departments.departmentname,''	) AS department,
                            IFNULL(CONCAT(vtiger_users.last_name,if(vtiger_users.`status`='Active','','[离职]')),'') AS smownerid,
                            IFNULL(left(vtiger_visitingorder.startdate,10),'') AS visitingtime,
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.visitingtimes,'') AS visitingtimes,
                            IFNULL(vtiger_visitingorder.contacts,'') AS contacts,
                            IFNULL(vtiger_visitingorder.purpose,'') AS purpose,
                            vtiger_visitingorder.related_to AS accountid,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT title FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as title,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.makedecision,(SELECT makedecision FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as makedecision,
                            IFNULL((SELECT GROUP_CONCAT(vtiger_products.productname) FROM vtiger_products WHERE  FIND_IN_SET(vtiger_products.productid,REPLACE(vtiger_account.servicetype,' |##| ',','))),'') as servicetypename
                        FROM
                                vtiger_visitingorder
                        LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_visitingorder.related_to
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_visitingorder.extractid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE
                            vtiger_visitingorder.modulestatus='c_complete'
                        AND vtiger_visitingorder.related_to>0
                        AND vtiger_crmentity.deleted = 0
                        AND (vtiger_visitingorder.issign!=1 OR vtiger_visitingorder.issign IS NULL)
                        AND TO_DAYS(vtiger_visitingorder.startdate){$tempdate}{$sql} LIMIT 1000";
                $query=str_replace('vtiger_crmentity.smownerid','vtiger_visitingorder.extractid',$query);
                break;
            //已跟进已签到的拜访单数
            case 'hasbeenfollowedup':
                $query="SELECT
                            IFNULL(vtiger_departments.departmentname,''	) AS department,
                            IFNULL(CONCAT(vtiger_users.last_name,if(vtiger_users.`status`='Active','','[离职]')),'') AS smownerid,
                            IFNULL(left(vtiger_visitingorder.startdate,10),'') AS visitingtime,
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.visitingtimes,'') AS visitingtimes,
                            IFNULL(vtiger_visitingorder.contacts,'') AS contacts,
                            IFNULL(vtiger_visitingorder.purpose,'') AS purpose,
                            vtiger_visitingorder.related_to AS accountid,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT title FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as title,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.makedecision,(SELECT makedecision FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as makedecision,
                            IFNULL((SELECT GROUP_CONCAT(vtiger_products.productname) FROM vtiger_products WHERE  FIND_IN_SET(vtiger_products.productid,REPLACE(vtiger_account.servicetype,' |##| ',','))),'') as servicetypename
                        FROM
                                vtiger_visitingorder
                        LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_visitingorder.related_to
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_visitingorder.extractid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE
                            vtiger_visitingorder.modulestatus='c_complete'
                        AND vtiger_visitingorder.related_to>0
                        AND vtiger_crmentity.deleted = 0
                        AND vtiger_visitingorder.issign=1
                        AND TO_DAYS(vtiger_visitingorder.startdate){$tempdate}{$sql} LIMIT 1000";
                $query=str_replace('vtiger_crmentity.smownerid','vtiger_visitingorder.extractid',$query);
                break;
            default:
                break;

        }
        //echo $query;
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        if($num<1){
            $arrlist=array();
            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($arrlist);
            $response->emit();
            return;
        }
        $arrlist=array();
        switch($paramcolum){
            case 'daycounts':
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['industry']=vtranslate($arrlist[$i]['industry'],'Accounts');
                    $arrlist[$i]['regionalpartition']=vtranslate($arrlist[$i]['regionalpartition'],'Accounts');
                    $arrlist[$i]['createdtime']=substr($arrlist[$i]['createdtime'],0,10);
                    $arrlist[$i]['accountrank']=vtranslate($arrlist[$i]['accountrank']);
                }
                break;
            case 'gonghai':
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['industry']=vtranslate($arrlist[$i]['industry'],'Accounts');
                    $arrlist[$i]['regionalpartition']=vtranslate($arrlist[$i]['regionalpartition'],'Accounts');
                    $arrlist[$i]['createdtime']=substr($arrlist[$i]['createdtime'],0,10);
                    $arrlist[$i]['accountrank']=vtranslate($arrlist[$i]['accountrank']);
                }
                break;
            case 'dayforforp':
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['makedecision']=vtranslate($arrlist[$i]['makedecision'],'Accounts');
                    $arrlist[$i]['createdtime']=substr($arrlist[$i]['createdtime'],0,10);
                    $arrlist[$i]['firstvisittime']=substr($arrlist[$i]['firstvisittime'],0,10);
                }
                break;
                //{didnotsignup:'已跟进未签到的拜访单数',hasbeenfollowedup:'已跟进已签到的拜访单数'}
            case 'dayforp':
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['makedecision']=vtranslate($arrlist[$i]['makedecision'],'Accounts');
                    $arrlist[$i]['createdtime']=substr($arrlist[$i]['createdtime'],0,10);
                    $arrlist[$i]['firstvisittime']=substr($arrlist[$i]['firstvisittime'],0,10);
                }
                break;
            case 'dayvisiting':
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['makedecision']=vtranslate($arrlist[$i]['makedecision'],'Accounts');
                    $arrlist[$i]['accountname']=$arrlist[$i]['accountname'].'【'.vtranslate($arrlist[$i]['accountrank'],'Accounts')."】";
                    //$arrlist[$i]['purpose']=substr($arrlist[$i]['purpose'],0,10)==false?'':substr($arrlist[$i]['purpose'],0,10);
                }
                break;
            case 'dayallvisiting':
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['makedecision']=vtranslate($arrlist[$i]['makedecision'],'Accounts');
                    //$arrlist[$i]['purpose']=substr($arrlist[$i]['purpose'],0,10)==false?'':substr($arrlist[$i]['purpose'],0,10);
                }
                break;
            case 'daynotfollow':
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['makedecision']=vtranslate($arrlist[$i]['makedecision'],'Accounts');
                    //$arrlist[$i]['purpose']=substr($arrlist[$i]['purpose'],0,10)==false?'':substr($arrlist[$i]['purpose'],0,10);
                }
                break;
            case 'daysaler':
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['industry']=vtranslate($arrlist[$i]['industry'],'Accounts');
                    $arrlist[$i]['data_entered']=substr($arrlist[$i]['data_entered'],0,10);
                    $arrlist[$i]['saleorderlastdealtime']=substr($arrlist[$i]['saleorderlastdealtime'],0,10);
                    $arrlist[$i]['visitingtimes']=$arrlist[$i]['visitingtimes']==null?0:$arrlist[$i]['visitingtimes'];
                }
                break;
            case 'didnotsignup':
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['makedecision']=vtranslate($arrlist[$i]['makedecision'],'Accounts');
                    //$arrlist[$i]['purpose']=substr($arrlist[$i]['purpose'],0,10)==false?'':substr($arrlist[$i]['purpose'],0,10);
                }
                break;
            case 'hasbeenfollowedup':
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['makedecision']=vtranslate($arrlist[$i]['makedecision'],'Accounts');
                    //$arrlist[$i]['purpose']=substr($arrlist[$i]['purpose'],0,10)==false?'':substr($arrlist[$i]['purpose'],0,10);
                }
                break;
            default:
                break;

        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arrlist);
        $response->emit();
    }
    public function getUsers(Vtiger_Request $request){
        $departmentid=$request->get('department');
        if(!empty($departmentid)&&$departmentid!='H1'){
            $userid=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('Reporting','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery = ' AND id in('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers('Reporting','List',false);
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
    public function getrefreshday(){
        ignore_user_abort(true);//浏览器关闭后脚本还执行
        $db=PearDatabase::getInstance();
        $query="SELECT refreshtime FROM `vtiger_refreshtime` WHERE module='Reporting' limit 1";
        $result=$db->pquery($query,array());
        $resulttime=$db->query_result($result,0,'refreshtime');
        $nowtime=time();
        $interval=4*60*60;//间隔时间
        $result1=array();
        if($nowtime-$resulttime>$interval){
            $db->pquery("TRUNCATE TABLE vtiger_reporting_view",array());

            $db->pquery("INSERT INTO vtiger_reporting_view(classification,`countnums`,`dayforp`,`dayforforp`,`daycounts`,`dayvisiting`,`gonghai`,`daysaler`,`dayallvisiting`,`daynotfollow`,`didnotsignup`,`hasbeenfollowedup`,`createdtime`,`smownerid`)
                        SELECT 'dayforp',count(1) AS `daytemp`,count(1) AS `dayforp`,0 AS `dayforforp`,0 AS `daycounts`,0 AS `dayvisiting`,0 AS `gonghai`,0 AS `daysaler`,0 AS `dayallvisiting`,0 AS `daynotfollow`,0 AS `didnotsignup`,0 AS `hasbeenfollowedup`,LEFT(`vtiger_accountrankhistory`.`createdtime`,10) AS `createdtime`,`vtiger_crmentity`.`smownerid` AS `smownerid`
                        FROM `vtiger_account` LEFT JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`crmid` = `vtiger_account`.`accountid` LEFT JOIN `vtiger_accountrankhistory` ON `vtiger_accountrankhistory`.`accountid` = `vtiger_account`.`accountid` WHERE `vtiger_accountrankhistory`.`newaccountrank` = 'forp_notv' GROUP BY `vtiger_crmentity`.`smownerid`,LEFT(`vtiger_accountrankhistory`.`createdtime`,10)
                        UNION
                        SELECT 'dayforforp',count(1) AS `daytemp`,0 AS `dayforp`,count(1) AS `dayforforp`,0 AS `daycounts`,0 AS `dayvisiting`,0 AS `gonghai`,0 AS `daysaler`,0 AS `dayallvisiting`,0 AS `daynotfollow`,0 AS `didnotsignup`,0 AS `hasbeenfollowedup`,LEFT(`vtiger_crmentity`.`createdtime`,10) AS `createdtime`,`vtiger_crmentity`.`smownerid` AS `smownerid`
                        FROM `vtiger_account` LEFT JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`crmid` = `vtiger_account`.`accountid` LEFT JOIN `vtiger_accountrankhistory` ON `vtiger_accountrankhistory`.`accountid` = `vtiger_account`.`accountid` WHERE `vtiger_accountrankhistory`.`newaccountrank` = 'forp_notv' AND LEFT(`vtiger_accountrankhistory`.`createdtime`,10)=LEFT(`vtiger_crmentity`.`createdtime`,10) GROUP BY `vtiger_crmentity`.`smownerid`,LEFT(`vtiger_accountrankhistory`.`createdtime`,10)
                        UNION
                        SELECT 'daycounts',count(1) AS `daytemp`,0 AS `dayforp`,0 AS `dayforforp`,count(1) AS `daycounts`,0 AS `dayvisiting`,0 AS `gonghai`,0 AS `daysaler`,0 AS `dayallvisiting`,0 AS `daynotfollow`,0 AS `didnotsignup`,0 AS `hasbeenfollowedup`,LEFT(`vtiger_crmentity`.`createdtime`,10) AS `createdtime`,`vtiger_crmentity`.`smownerid` AS `smownerid`
                        FROM `vtiger_account` LEFT JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`crmid` = `vtiger_account`.`accountid` WHERE `vtiger_crmentity`.`deleted` = 0 AND `vtiger_crmentity`.`smownerid` > 0 GROUP BY `vtiger_crmentity`.`smownerid`,LEFT(`vtiger_crmentity`.`createdtime`,10)
                        UNION
                        SELECT 'dayvisiting',count(1) AS `daytemp`,0 AS `dayforp`,0 AS `dayforforp`,0 AS `daycounts`,count(1) AS `dayvisiting`,0 AS `gonghai`,0 AS `daysaler`,0 AS `dayallvisiting`,0 AS `daynotfollow`,0 AS `didnotsignup`,0 AS `hasbeenfollowedup`,LEFT(`vtiger_visitingorder`.`startdate`,10) AS `createdtime`,`vtiger_visitingorder`.`extractid` AS `smownerid`
                        FROM `vtiger_visitingorder` LEFT JOIN `vtiger_crmentity` ON `vtiger_visitingorder`.`visitingorderid` = `vtiger_crmentity`.`crmid` LEFT JOIN `vtiger_account` ON `vtiger_account`.`accountid` = `vtiger_visitingorder`.`related_to` WHERE (1 = 1) AND `vtiger_crmentity`.`deleted` = 0 AND	`vtiger_visitingorder`.`modulestatus` = 'c_complete' AND `vtiger_visitingorder`.`related_to` > 0 GROUP BY `vtiger_visitingorder`.`extractid`,LEFT(`vtiger_visitingorder`.`startdate`,10)
                        UNION
                        SELECT 'gonghai',count(1) AS `daytemp`,0 AS `dayforp`,0 AS `dayforforp`,0 AS `daycounts`,0 AS `dayvisiting`,count(1) AS `gonghai`,0 AS `daysaler`,0 AS `dayallvisiting`,0 AS `daynotfollow`,0 AS `didnotsignup`,0 AS `hasbeenfollowedup`,LEFT (`vtiger_accountgonghaioutrel`.`createdtime`,10) AS `createdtime`,`vtiger_accountgonghaioutrel`.`userid` AS `smownerid`
                        FROM `vtiger_accountgonghaioutrel` GROUP BY `vtiger_accountgonghaioutrel`.`userid`,LEFT(`vtiger_accountgonghaioutrel`.`createdtime`,10)
                        UNION
                        SELECT 'daysaler',count(1) AS `daytemp`,0 AS `dayforp`,0 AS `dayforforp`,0 AS `daycounts`,0 AS `dayvisiting`,0 AS `gonghai`,count(1) AS `daysaler`,0 AS `dayallvisiting`,0 AS `daynotfollow`,0 AS `didnotsignup`,0 AS `hasbeenfollowedup`,`vtiger_servicecontracts`.`firstreceivepaydate` AS `createdtime`,`vtiger_servicecontracts`.`receiveid` AS `smownerid`
                        FROM `vtiger_servicecontracts`LEFT JOIN `vtiger_crmentity` ON `vtiger_servicecontracts`.`servicecontractsid` = `vtiger_crmentity`.`crmid` WHERE `vtiger_crmentity`.`deleted` = 0 AND `vtiger_servicecontracts`.`modulestatus` = 'c_complete'
                        AND `vtiger_servicecontracts`.`sc_related_to` > 0 AND `vtiger_servicecontracts`.`firstreceivepaydate` IS NOT NULL AND `vtiger_servicecontracts`.`firstreceivepaydate` <> '' GROUP BY `vtiger_servicecontracts`.`firstreceivepaydate`,`vtiger_servicecontracts`.`receiveid`
                        UNION
                        SELECT 'dayallvisiting',count(1) AS `daytemp`,0 AS `dayforp`,0 AS `dayforforp`,0 AS `daycounts`,0 AS `dayvisiting`,0 AS `gonghai`,0 AS `daysaler`,count(1) AS `dayallvisiting`,0 AS `daynotfollow`,0 AS `didnotsignup`,0 AS `hasbeenfollowedup`,LEFT(`vtiger_visitingorder`.`startdate`,10) AS `createdtime`,`vtiger_visitingorder`.`extractid` AS `smownerid`
                        FROM `vtiger_visitingorder` LEFT JOIN `vtiger_crmentity` ON `vtiger_visitingorder`.`visitingorderid` = `vtiger_crmentity`.`crmid` LEFT JOIN `vtiger_account` ON `vtiger_account`.`accountid` = `vtiger_visitingorder`.`related_to` WHERE `vtiger_visitingorder`.`modulestatus` = 'a_normal' AND `vtiger_crmentity`.`deleted` = 0 GROUP BY `vtiger_visitingorder`.`extractid`,LEFT(`vtiger_visitingorder`.`startdate`,10)
                        UNION
                        SELECT 'daynotfollow',count(1) AS `daytemp`,0 AS `dayforp`,0 AS `dayforforp`,0 AS `daycounts`,0 AS `dayvisiting`,0 AS `gonghai`,0 AS `daysaler`,0 AS `dayallvisiting`,count(1) AS `daynotfollow`,0 AS `didnotsignup`,0 AS `hasbeenfollowedup`,date_sub(LEFT(`vtiger_visitingorder`.`enddate`,10),interval -1 day) AS `createdtime`,`vtiger_visitingorder`.`extractid` AS `smownerid`
                        FROM `vtiger_visitingorder` LEFT JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`crmid` = `vtiger_visitingorder`.`visitingorderid` WHERE `vtiger_visitingorder`.`dayfollowup` <> '是' AND `vtiger_crmentity`.`deleted` = 0 AND vtiger_visitingorder.modulestatus='c_complete' GROUP BY `vtiger_visitingorder`.`extractid`,LEFT(`vtiger_visitingorder`.`enddate`,10)
                        UNION
                        SELECT 'didnotsignup',count(1) AS `daytemp`,0 AS `dayforp`,0 AS `dayforforp`,0 AS `daycounts`,0 AS `dayvisiting`,0 AS `gonghai`,0 AS `daysaler`,0 AS `dayallvisiting`,0 AS `daynotfollow`,count(1) AS `didnotsignup`,0 AS hasbeenfollowedup,LEFT(`vtiger_visitingorder`.`enddate`,10) AS `createdtime`,`vtiger_visitingorder`.`extractid` AS `smownerid` FROM `vtiger_visitingorder` LEFT JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`crmid` = `vtiger_visitingorder`.`visitingorderid` WHERE `vtiger_visitingorder`.`dayfollowup` <> '是' AND `vtiger_crmentity`.`deleted` = 0 AND (vtiger_visitingorder.issign!=1 OR vtiger_visitingorder.issign IS NULL) AND vtiger_visitingorder.modulestatus='c_complete' GROUP BY `vtiger_visitingorder`.`extractid`,LEFT(`vtiger_visitingorder`.`enddate`,10)
                        UNION
                        SELECT 'hasbeenfollowedup',count(1) AS `daytemp`,0 AS `dayforp`,0 AS `dayforforp`,0 AS `daycounts`,0 AS `dayvisiting`,0 AS `gonghai`,0 AS `daysaler`,0 AS `dayallvisiting`,0 AS `daynotfollow`,0 AS `didnotsignup`,count(1) AS hasbeenfollowedup,LEFT(`vtiger_visitingorder`.`enddate`,10) AS `createdtime`,`vtiger_visitingorder`.`extractid` AS `smownerid`
                        FROM `vtiger_visitingorder` LEFT JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`crmid` = `vtiger_visitingorder`.`visitingorderid` WHERE `vtiger_visitingorder`.`dayfollowup` <> '是' AND `vtiger_crmentity`.`deleted` = 0 AND vtiger_visitingorder.issign=1 AND vtiger_visitingorder.modulestatus='c_complete' GROUP BY `vtiger_visitingorder`.`extractid`,LEFT(`vtiger_visitingorder`.`enddate`,10)",array());
            $nowtime=time();
            $db->pquery("replace into vtiger_refreshtime(refreshtime,module) VALUES(?,?)",array($nowtime,'Reporting'));
            $result1['msg']='更新完成......';
        }else{
            $interval=4*60-ceil(($nowtime-$resulttime)/60);
            if(floor($interval/60)==0){
                $result1['msg']="请在{$interval}分钟后再更新";
            }else{
                $result1['msg']="请在".floor($interval/60)."小时".($interval%60)."分钟后再更新";
            }

        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result1);
        $response->emit();
    }
    public function getvisitsdata(Vtiger_Request $request){
        $datatime=$request->get('datetime');
        $userid=$request->get('userid');
        $departmentid=$request->get('department');
        /* echo $datatime;
        var_dump($userid);
        print_r($departmentid);
        exit */;
        $querySql='';
        if(empty($userid)||!is_numeric($userid)){
            $departmentarr=array();
            if(!empty($departmentid)&&$departmentid!='null'){
                foreach($departmentid as $value){
                    $userid=getDepartmentUser($value);
                    $where=getAccessibleUsers('VisitingOrder','List',true);
                    if($where!='1=1'){
                        $where=array_intersect($where,$userid);
                    }else{
                        $where=$userid;
                    }
                    if(empty($where)||count($where)==0){
                        continue;
                    }
                    $departmentarr=array_merge($departmentarr,$where);
                }
                $querySql=' AND userid IN('.implode(',',$departmentarr).')';
            }else{
                $where=getAccessibleUsers('VisitingOrder','List',false);
                if($where!='1=1'){
                    $querySql=' AND userid IN('.implode(',',$where).')';
                }
            }
        }else{

            $querySql=' AND userid='.$userid;
        }

        $query='SELECT
                    `january`,
                    `february`,
                    `march`,
                    `april`,
                    `may`,
                    `june`,
                    `july`,
                    `august`,
                    `september`,
                    `october`,
                    `november`,
                    `december`,
                    `firstquarter`,
                    `secondquarter`,
                    `threequarter`,
                    `fourthquarter`,
                    `allyear`,
                    `currentyear`,
                    `department`,
                    `username`,
                    `userid`,
                    ajanuary,
                    afebruary,
                    amarch,
                    aapril,
                    amay,
                    ajune,
                    ajuly,
                    aaugust,
                    aseptember,
                    aoctober,
                    anovember,
                    adecember,
                    afirstquarter,
                    asecondquarter,
                    athreequarter,
                    afourthquarter,
                    aallyear
                FROM
                    vtiger_visitstatistics
                WHERE
                    currentyear=?
                    '.$querySql.'
                ORDER BY department';
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array($datatime));
        $num=$db->num_rows($result);
        if($num){
            $array=array();
            $cachedepartment=getDepartment();
            for($i=0;$i<$num;$i++){
                $depart=$db->query_result($result,$i,'department');
                $depart=explode('::',$depart);
                if(!empty($departmentid)&&$departmentid!='null'){
                    if(in_array($depart[1],$departmentid)){
                        $array[$depart[1]][$depart[1].'D'][]=$db->query_result_rowdata($result,$i);
                        $array[$depart[1]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                        $array[$depart[1]][$depart[1].'D']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]).'D';
                    }else{
                        $array[$depart[0]][$depart[1].'M'][]=$db->query_result_rowdata($result,$i);
                        $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                        $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                    }
                    
                }else{
                    $array[$depart[0]][$depart[1].'M'][]=$db->query_result_rowdata($result,$i);
                    $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                    $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]); 
                }
                
            }
            $depnum=array();
            foreach($array as $key=>$value){
                if($key=='name')continue;
                foreach($value as $k=>$v){
                    if($k=='name')continue;
                    $depnum[$key]+=count($v);
                    $depnum[$k]+=count($v);
                }
            }

            return array('data'=>$array,'num'=>$depnum);


        }else{

            return array();
        }
    }
    public function getvisitstatistics(Vtiger_Request $request){
        $data=$this->getvisitsdata($request);
        $datatime=$request->get('datetime');
        if(!empty($data)){

            $array=$data['data'];
            $depnum=$data['num'];
            $text='';
            foreach($array as $key1=>$value1){
                $i=0;
                if($key1=='name'){
                    continue;
                }
                $alljanuary=0;
                $allfebruary=0;
                $allmarch=0;
                $allfirstquarter=0;
                $allapril=0;
                $allmay=0;
                $alljune=0;
                $allsecondquarter=0;
                $alljuly=0;
                $allaugust=0;
                $allseptember=0;
                $allthreequarter=0;
                $alloctober=0;
                $allnovember=0;
                $alldecember=0;
                $allfourthquarter=0;
                $allallyear=0;

                $accountalljanuary=0;
                $accountallfebruary=0;
                $accountallmarch=0;
                $accountallfirstquarter=0;
                $accountallapril=0;
                $accountallmay=0;
                $accountalljune=0;
                $accountallsecondquarter=0;
                $accountalljuly=0;
                $accountallaugust=0;
                $accountallseptember=0;
                $accountallthreequarter=0;
                $accountalloctober=0;
                $accountallnovember=0;
                $accountalldecember=0;
                $accountallfourthquarter=0;
                $accountallallyear=0;

                foreach($value1 as $key2=>$value2){
                    $j=0;
                    if($key2=='name') {
                        continue;
                    }
                    $sjanuary=0;
                    $sfebruary=0;
                    $smarch=0;
                    $sfirstquarter=0;
                    $sapril=0;
                    $smay=0;
                    $sjune=0;
                    $ssecondquarter=0;
                    $sjuly=0;
                    $saugust=0;
                    $sseptember=0;
                    $sthreequarter=0;
                    $soctober=0;
                    $snovember=0;
                    $sdecember=0;
                    $sfourthquarter=0;
                    $sallyear=0;

                    $accountsjanuary=0;
                    $accountsfebruary=0;
                    $accountsmarch=0;
                    $accountsfirstquarter=0;
                    $accountsapril=0;
                    $accountsmay=0;
                    $accountsjune=0;
                    $accountssecondquarter=0;
                    $accountsjuly=0;
                    $accountsaugust=0;
                    $accountsseptember=0;
                    $accountsthreequarter=0;
                    $accountsoctober=0;
                    $accountsnovember=0;
                    $accountsdecember=0;
                    $accountsfourthquarter=0;
                    $accountsallyear=0;

                    foreach($value2 as $key3=>$value3){

                        /*if("name"==$key3){
                            continue;
                        }*/
                        if(!is_numeric($key3)){
                            continue;
                        }

                        if($i==0){
                            $center='<td rowspan="'.($depnum[$key1]+1).'" style="text-align: center;vertical-align:middle;">'.$value1['name'].'</td>';
                        }else{
                            $center='';
                        }
                        if($j==0){
                            $departname='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;">'.$value2['name'].'</td>';
                        }else{
                            $departname='';
                        }

                        $text.='<tr>
                                    '.$center.$departname.'
                                    <td style="text-align: center;vertical-align:middle;">'.$value3['username'].'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="1月">'.(empty($value3['january'])?'&nbsp;':$value3['january']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="1月">'.(empty($value3['ajanuary'])?'&nbsp;':$value3['ajanuary']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="2月">'.(empty($value3['february'])?'&nbsp;':$value3['february']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="2月">'.(empty($value3['afebruary'])?'&nbsp;':$value3['afebruary']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="3月">'.(empty($value3['march'])?'&nbsp;':$value3['march']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="3月">'.(empty($value3['amarch'])?'&nbsp;':$value3['amarch']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="第一季度">'.(empty($value3['firstquarter'])?'&nbsp;':$value3['firstquarter']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="第一季度">'.(empty($value3['afirstquarter'])?'&nbsp;':$value3['afirstquarter']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="4月">'.(empty($value3['april'])?'&nbsp;':$value3['april']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="4月">'.(empty($value3['aapril'])?'&nbsp;':$value3['aapril']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="5月">'.(empty($value3['may'])?'&nbsp;':$value3['may']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="5月">'.(empty($value3['amay'])?'&nbsp;':$value3['amay']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="6月">'.(empty($value3['june'])?'&nbsp;':$value3['june']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="6月">'.(empty($value3['ajune'])?'&nbsp;':$value3['ajune']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="第二季度">'.(empty($value3['secondquarter'])?'&nbsp;':$value3['secondquarter']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="第二季度">'.(empty($value3['asecondquarter'])?'&nbsp;':$value3['asecondquarter']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="7月">'.(empty($value3['july'])?'&nbsp;':$value3['july']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="7月">'.(empty($value3['ajuly'])?'&nbsp;':$value3['ajuly']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="8月">'.(empty($value3['august'])?'&nbsp;':$value3['august']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="8月">'.(empty($value3['aaugust'])?'&nbsp;':$value3['aaugust']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="9">'.(empty($value3['september'])?'&nbsp;':$value3['september']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="9">'.(empty($value3['aseptember'])?'&nbsp;':$value3['aseptember']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="第三季度">'.(empty($value3['threequarter'])?'&nbsp;':$value3['threequarter']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="第三季度">'.(empty($value3['athreequarter'])?'&nbsp;':$value3['athreequarter']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="10月">'.(empty($value3['october'])?'&nbsp;':$value3['october']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="10月">'.(empty($value3['aoctober'])?'&nbsp;':$value3['aoctober']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="11月">'.(empty($value3['november'])?'&nbsp;':$value3['november']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="11月">'.(empty($value3['anovember'])?'&nbsp;':$value3['anovember']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="12月">'.(empty($value3['december'])?'&nbsp;':$value3['december']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="12月">'.(empty($value3['adecember'])?'&nbsp;':$value3['adecember']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="第四季度">'.(empty($value3['fourthquarter'])?'&nbsp;':$value3['fourthquarter']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="第四季度">'.(empty($value3['afourthquarter'])?'&nbsp;':$value3['afourthquarter']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="年度">'.(empty($value3['allyear'])?'&nbsp;':'<span class="label label-a_exception">'.$value3['allyear'].'</span>').'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="年度">'.(empty($value3['allyear'])?'&nbsp;':'<span class="label label-a_exception">'.$value3['aallyear'].'</span>').'</td>
                                </tr>';
                        $i=1;$j=1;
                        $alljanuary+=$value3['january'];
                        $allfebruary+=$value3['february'];
                        $allmarch+=$value3['march'];
                        $allfirstquarter+=$value3['firstquarter'];
                        $allapril+=$value3['april'];
                        $allmay+=$value3['may'];
                        $alljune+=$value3['june'];
                        $allsecondquarter+=$value3['secondquarter'];
                        $alljuly+=$value3['july'];
                        $allaugust+=$value3['august'];
                        $allseptember+=$value3['september'];
                        $allthreequarter+=$value3['threequarter'];
                        $alloctober+=$value3['october'];
                        $allnovember+=$value3['november'];
                        $alldecember+=$value3['december'];
                        $allfourthquarter+=$value3['fourthquarter'];
                        $allallyear+=$value3['allyear'];

                        $accountalljanuary+=$value3['ajanuary'];
                        $accountallfebruary+=$value3['afebruary'];
                        $accountallmarch+=$value3['amarch'];
                        $accountallfirstquarter+=$value3['afirstquarter'];
                        $accountallapril+=$value3['aapril'];
                        $accountallmay+=$value3['amay'];
                        $accountalljune+=$value3['ajune'];
                        $accountallsecondquarter+=$value3['asecondquarter'];
                        $accountalljuly+=$value3['ajuly'];
                        $accountallaugust+=$value3['aaugust'];
                        $accountallseptember+=$value3['aseptember'];
                        $accountallthreequarter+=$value3['athreequarter'];
                        $accountalloctober+=$value3['aoctober'];
                        $accountallnovember+=$value3['anovember'];
                        $accountalldecember+=$value3['adecember'];
                        $accountallfourthquarter+=$value3['afourthquarter'];
                        $accountallallyear+=$value3['aallyear'];


                        $sjanuary+=$value3['january'];
                        $sfebruary+=$value3['february'];
                        $smarch+=$value3['march'];
                        $sfirstquarter+=$value3['firstquarter'];
                        $sapril+=$value3['april'];
                        $smay+=$value3['may'];
                        $sjune+=$value3['june'];
                        $ssecondquarter+=$value3['secondquarter'];
                        $sjuly+=$value3['july'];
                        $saugust+=$value3['august'];
                        $sseptember+=$value3['september'];
                        $sthreequarter+=$value3['threequarter'];
                        $soctober+=$value3['october'];
                        $snovember+=$value3['november'];
                        $sdecember+=$value3['december'];
                        $sfourthquarter+=$value3['fourthquarter'];
                        $sallyear+=$value3['allyear'];

                        $accountsjanuary+=$value3['ajanuary'];
                        $accountsfebruary+=$value3['afebruary'];
                        $accountsmarch+=$value3['amarch'];
                        $accountsfirstquarter+=$value3['afirstquarter'];
                        $accountsapril+=$value3['aapril'];
                        $accountsmay+=$value3['amay'];
                        $accountsjune+=$value3['ajune'];
                        $accountssecondquarter+=$value3['asecondquarter'];
                        $accountsjuly+=$value3['ajuly'];
                        $accountsaugust+=$value3['aaugust'];
                        $accountsseptember+=$value3['aseptember'];
                        $accountsthreequarter+=$value3['athreequarter'];
                        $accountsoctober+=$value3['aoctober'];
                        $accountsnovember+=$value3['anovember'];
                        $accountsdecember+=$value3['adecember'];
                        $accountsfourthquarter+=$value3['afourthquarter'];
                        $accountsallyear+=$value3['aallyear'];
                    }
                    $text.='<tr>

                        <td style="text-align: center;vertical-align:middle;"><span class="label label-a_normal"> 部门小计</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-a_normal">'.$sjanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-a_normal">'.$accountsjanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-a_normal">'.$sfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-a_normal">'.$accountsfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-a_normal">'.$smarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-a_normal">'.$accountsmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-warning">'.$sfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-warning">'.$accountsfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-a_normal">'.$sapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-a_normal">'.$accountsapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-a_normal">'.$smay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-a_normal">'.$accountsmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-a_normal">'.$sjune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-a_normal">'.$accountsjune.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-b_check">'.$ssecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-b_check">'.$accountssecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-a_normal">'.$sjuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-a_normal">'.$accountsjuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-a_normal">'.$saugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-a_normal">'.$accountsaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-a_normal">'.$sseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-a_normal">'.$accountsseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-warning">'.$sthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-warning">'.$accountsthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-a_normal">'.$soctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-a_normal">'.$accountsoctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-a_normal">'.$snovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-a_normal">'.$accountsnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-a_normal">'.$sdecember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-a_normal">'.$accountsdecember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-b_check">'.$sfourthquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-b_check">'.$accountsfourthquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="年度"><span class="label label-inverse">'.$sallyear.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="年度"><span class="label label-inverse">'.$accountsallyear.'</span></td>
                    </tr>';
                }
                $text.='<tr>
                        <td style="text-align: center;vertical-align:middle;"><span class="label label-success">营总计</span></td>
                        <td>&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-success">'.$alljanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-success">'.$accountalljanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-success">'.$allfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-success">'.$accountallfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-success">'.$allmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-success">'.$accountallmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-success">'.$allfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-success">'.$accountallfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-success">'.$allapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-success">'.$accountallapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-success">'.$allmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-success">'.$accountallmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-success">'.$alljune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-success">'.$accountalljune.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-success">'.$allsecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-success">'.$accountallsecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-success">'.$alljuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-success">'.$accountalljuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-success">'.$allaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-success">'.$accountallaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-success">'.$allseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-success">'.$accountallseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-success">'.$allthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-success">'.$allthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-success">'.$alloctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-success">'.$accountalloctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-success">'.$allnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-success">'.$accountallnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-success">'.$alldecember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-success">'.$accountalldecember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-success">'.$allfourthquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-success">'.$accountallfourthquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="年度"><span class="label label-success">'.$allallyear.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="年度"><span class="label label-success">'.$accountallallyear.'</span></td>
                    </tr>';

            }
            $table='
                <table class="table table-bordered hide" id="flalted" style="z-index:1029;">
                    <thead>
                    <tr id="flalte1"  style="background-color:#ffffff;">
                        <th rowspan="3" style="text-align: center;vertical-align:middle;">中心</th>
                        <th rowspan="3" style="text-align: center;vertical-align:middle;">部门</th>
                        <th rowspan="3" style="text-align: center;vertical-align:middle;">姓名</th>
                        <th colspan="8" style="text-align: center;vertical-align:middle;">第一季度</th>
                        <th colspan="8" style="text-align: center;vertical-align:middle;">第二季度</th>
                        <th colspan="8" style="text-align: center;vertical-align:middle;">第三季度</th>
                        <th colspan="8" style="text-align: center;vertical-align:middle;">第四季度</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">'.$datatime.'年度</th>
                    </tr>
                    <tr id="flalte2"  style="background-color:#ffffff;">
                        <th colspan="2" style="text-align: center;vertical-align:middle;">一月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">二月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">三月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">四月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">五月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">六月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">七月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">八月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">九月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">十月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">十一月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">十二月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">汇总</th>
                    </tr>
                    <tr id="flalte3"  style="background-color:#ffffff;">
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                    </tr>
                    </thead>
                    
                </table>';
            $table.='
                <table class="table table-bordered table-striped" id="one1">
                    <thead>
                    <tr id="flaltt1">
                        <th rowspan="3" style="text-align: center;vertical-align:middle;">中心</th>
                        <th rowspan="3" style="text-align: center;vertical-align:middle;">部门</th>
                        <th rowspan="3" style="text-align: center;vertical-align:middle;">姓名</th>
                        <th colspan="8" style="text-align: center;vertical-align:middle;">第一季度</th>
                        <th colspan="8" style="text-align: center;vertical-align:middle;">第二季度</th>
                        <th colspan="8" style="text-align: center;vertical-align:middle;">第三季度</th>
                        <th colspan="8" style="text-align: center;vertical-align:middle;">第四季度</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">'.$datatime.'年度</th>
                    </tr>
                    <tr id="flaltt2">
                        <th colspan="2" style="text-align: center;vertical-align:middle;">一月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">二月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">三月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">四月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">五月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">六月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">七月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">八月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">九月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">十月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">十一月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">十二月</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="2" style="text-align: center;vertical-align:middle;">汇总</th>
                        
                    </tr>
                    <tr id="flaltt3">
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">客户数</th>
                    </tr>
                    </thead>
                    <tbody>
                    '.$text.'
                    </tbody>
                </table>';
            echo $table;
            exit;


        }else{

            echo '<table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="text-align: center;vertical-align:middle;">没有记录</th>
                    </tr></thead></table>';
            exit;
        }
    }
    public function getvisitrefresh(){
        ignore_user_abort(true);//浏览器关闭后脚本还执行
        $db=PearDatabase::getInstance();
        $query="SELECT refreshtime FROM `vtiger_refreshtime` WHERE module='visitrefresh' limit 1";
        $result=$db->pquery($query,array());
        $resulttime=$db->query_result($result,0,'refreshtime');
        $nowtime=time();
        $interval=20*60;//间隔时间
        $result1=array();
        if($nowtime-$resulttime>$interval){
            $db->pquery("TRUNCATE table vtiger_visitstatistics",array());

            $db->pquery("INSERT INTO `vtiger_visitstatistics`(
                              `january`,
                              `february`,
                              `march`,
                              `april`,
                              `may`,
                              `june`,
                              `july`,
                              `august`,
                              `september`,
                              `october`,
                              `november`,
                              `december`,
                              `firstquarter`,
                              `secondquarter`,
                              `threequarter`,
                              `fourthquarter`,
                              `allyear`,
                              `currentyear`,
                              `department`,
                              `username`,
                              `userid`,
                              ajanuary,
                                afebruary,
                                amarch,
                                aapril,
                                amay,
                                ajune,
                                ajuly,
                                aaugust,
                                aseptember,
                                aoctober,
                                anovember,
                                adecember,
                                afirstquarter,
                                asecondquarter,
                                athreequarter,
                                afourthquarter,
                                aallyear
                            ) SELECT
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=1,1,0)),
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=2,1,0)),
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=3,1,0)),
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=4,1,0)),
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=5,1,0)),
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=6,1,0)),
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=7,1,0)),
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=8,1,0)),
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=9,1,0)),
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=10,1,0)),
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=11,1,0)),
                                sum(if(date_format(vtiger_visitingorder.enddate,'%c')=12,1,0)),
                                sum(if(quarter(vtiger_visitingorder.enddate)=1,1,0)),
                                sum(if(quarter(vtiger_visitingorder.enddate)=2,1,0)),
                                sum(if(quarter(vtiger_visitingorder.enddate)=3,1,0)),
                                sum(if(quarter(vtiger_visitingorder.enddate)=4,1,0)),
                                count(1),
                                year(vtiger_visitingorder.enddate),
                                SUBSTRING_INDEX(vtiger_departments.parentdepartment,'::' ,- 2),
                                vtiger_users.last_name,
                                vtiger_users.id,
                                sum(if(date_format(tempvisiting.enddate,'%c')=1,1,0)),
                                sum(if(date_format(tempvisiting.enddate,'%c')=2,1,0)),
                                sum(if(date_format(tempvisiting.enddate,'%c')=3,1,0)),
                                sum(if(date_format(tempvisiting.enddate,'%c')=4,1,0)),
                                sum(if(date_format(tempvisiting.enddate,'%c')=5,1,0)),
                                sum(if(date_format(tempvisiting.enddate,'%c')=6,1,0)),
                                sum(if(date_format(tempvisiting.enddate,'%c')=7,1,0)),
                                sum(if(date_format(tempvisiting.enddate,'%c')=8,1,0)),
                                sum(if(date_format(tempvisiting.enddate,'%c')=9,1,0)),
                                sum(if(date_format(tempvisiting.enddate,'%c')=10,1,0)),
                                sum(if(date_format(tempvisiting.enddate,'%c')=11,1,0)),
                                sum(if(date_format(tempvisiting.enddate,'%c')=12,1,0)),
                                sum(if(quarter(tempvisiting.enddate)=1,1,0)),
                                sum(if(quarter(tempvisiting.enddate)=2,1,0)),
                                sum(if(quarter(tempvisiting.enddate)=3,1,0)),
                                sum(if(quarter(tempvisiting.enddate)=4,1,0)),
                                count(tempvisiting.enddate)
                            FROM
                                vtiger_visitingorder
                            LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_visitingorder.extractid
                            LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id
                            LEFT JOIN vtiger_departments ON vtiger_departments.departmentid = vtiger_user2department.departmentid
                            LEFT JOIN (SELECT v.enddate,v.visitingorderid FROM vtiger_visitingorder AS v WHERE  v.modulestatus = 'c_complete' AND v.followstatus = 'followup'  GROUP BY left(v.enddate,7),v.related_to) AS tempvisiting ON tempvisiting.visitingorderid=vtiger_visitingorder.visitingorderid

                            WHERE
                                vtiger_visitingorder.modulestatus = 'c_complete'
                            AND vtiger_visitingorder.followstatus = 'followup'
                            GROUP BY year(vtiger_visitingorder.enddate),vtiger_visitingorder.extractid",array());
            $nowtime=time();
            $db->pquery("replace into vtiger_refreshtime(refreshtime,module) VALUES(?,?)",array($nowtime,'visitrefresh'));
            $result1['msg']='更新完成......';
        }else{
            $interval=20-ceil(($nowtime-$resulttime)/60);
            if(floor($interval/60)==0){
                $result1['msg']="请在{$interval}分钟后再更新";
            }else{
                $result1['msg']="请在".floor($interval/60)."小时".($interval%60)."分钟后再更新";
            }

        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result1);
        $response->emit();
    }
    //拜访单导出
    public function getvisitexp(Vtiger_Request $request){
        $datatime=$request->get('datetime');
        $data=$this->getvisitsdata($request);
        global $root_directory;
        //print_r($data);
        //exit;
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';

        $phpexecl=new PHPExcel();

        // Set document properties
        $phpexecl->getProperties()->setCreator("liu ganglin")
            ->setLastModifiedBy("liu ganglin")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("servicecontracts");


        // 添加头信处
        $phpexecl->setActiveSheetIndex(0)->mergeCells('A1:A3')
        ->mergeCells('B1:B3')->mergeCells('C1:C3')->mergeCells('D1:K1')
            ->mergeCells('L1:S1')->mergeCells('T1:AA1')->mergeCells('AB1:AI1')
            ->mergeCells('AJ1:AK1')
            ->mergeCells('D2:E2')
            ->mergeCells('F2:G2')
            ->mergeCells('H2:I2')
            ->mergeCells('J2:K2')
            ->mergeCells('L2:M2')
            ->mergeCells('N2:O2')
            ->mergeCells('P2:Q2')
            ->mergeCells('R2:S2')
            ->mergeCells('T2:U2')
            ->mergeCells('V2:W2')
            ->mergeCells('X2:Y2')
            ->mergeCells('Z2:AA2')
            ->mergeCells('AB2:AC2')
            ->mergeCells('AD2:AE2')
            ->mergeCells('AF2:AG2')
            ->mergeCells('AH2:AI2')
            ->mergeCells('AJ2:AK2')
        ;
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '中心')
            ->setCellValue('B1', '部门')
            ->setCellValue('C1', '姓名')
            ->setCellValue('D1', '第一季度')
            ->setCellValue('L1', '第二季度')
            ->setCellValue('T1', '第三季度')
            ->setCellValue('AB1', '第四季度')
            ->setCellValue('AJ1', $datatime.'年度')
            ->setCellValue('D2', '一月')
            ->setCellValue('F2', '二月')
            ->setCellValue('H2', '三月')
            ->setCellValue('J2', '第一季度')
            ->setCellValue('L2', '四月')
            ->setCellValue('N2', '五月')
            ->setCellValue('P2', '六月')
            ->setCellValue('R2', '第二季度')
            ->setCellValue('T2', '七月')
            ->setCellValue('V2', '八月')
            ->setCellValue('X2', '九月')
            ->setCellValue('Z2', '第三季度')
            ->setCellValue('AB2', '十月')
            ->setCellValue('AD2', '十一月')
            ->setCellValue('AF2', '十二月')
            ->setCellValue('AH2', '第四季度')
            ->setCellValue('AJ2', '汇总')
            ->setCellValue('D3', '拜访数')
            ->setCellValue('E3', '客户数')
            ->setCellValue('F3', '拜访数')
            ->setCellValue('G3', '客户数')
            ->setCellValue('H3', '拜访数')
            ->setCellValue('I3', '客户数')
            ->setCellValue('J3', '拜访数')
            ->setCellValue('K3', '客户数')
            ->setCellValue('L3', '拜访数')
            ->setCellValue('M3', '客户数')
            ->setCellValue('N3', '拜访数')
            ->setCellValue('O3', '客户数')
            ->setCellValue('P3', '拜访数')
            ->setCellValue('Q3', '客户数')
            ->setCellValue('R3', '拜访数')
            ->setCellValue('S3', '客户数')
            ->setCellValue('T3', '拜访数')
            ->setCellValue('U3', '客户数')
            ->setCellValue('V3', '拜访数')
            ->setCellValue('W3', '客户数')
            ->setCellValue('X3', '拜访数')
            ->setCellValue('Y3', '客户数')
            ->setCellValue('Z3', '拜访数')
            ->setCellValue('AA3', '客户数')
            ->setCellValue('AB3', '拜访数')
            ->setCellValue('AC3', '客户数')
            ->setCellValue('AD3', '拜访数')
            ->setCellValue('AE3', '客户数')
            ->setCellValue('AF3', '拜访数')
            ->setCellValue('AG3', '客户数')
            ->setCellValue('AH3', '拜访数')
            ->setCellValue('AI3', '客户数')
            ->setCellValue('AJ3', '拜访数')
            ->setCellValue('AK3', '客户数')
            ;

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:AK3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$phpexecl->getActiveSheet()->getStyle('D2:AK3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        //$phpexecl->getActiveSheet()->getStyle('A1:AK1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $phpexecl->getActiveSheet()->getStyle('A1:AK3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $current=4;
        if(!empty($data)){
            $array=$data['data'];
            $depnum=$data['num'];
            $text='';
            foreach($array as $key1=>$value1){
                $i=0;
                if($key1=='name'){
                    continue;
                }
                $alljanuary=0;
                $allfebruary=0;
                $allmarch=0;
                $allfirstquarter=0;
                $allapril=0;
                $allmay=0;
                $alljune=0;
                $allsecondquarter=0;
                $alljuly=0;
                $allaugust=0;
                $allseptember=0;
                $allthreequarter=0;
                $alloctober=0;
                $allnovember=0;
                $alldecember=0;
                $allfourthquarter=0;
                $allallyear=0;

                $accountalljanuary=0;
                $accountallfebruary=0;
                $accountallmarch=0;
                $accountallfirstquarter=0;
                $accountallapril=0;
                $accountallmay=0;
                $accountalljune=0;
                $accountallsecondquarter=0;
                $accountalljuly=0;
                $accountallaugust=0;
                $accountallseptember=0;
                $accountallthreequarter=0;
                $accountalloctober=0;
                $accountallnovember=0;
                $accountalldecember=0;
                $accountallfourthquarter=0;
                $accountallallyear=0;


                foreach($value1 as $key2=>$value2){
                    $j=0;
                    if($key2=='name') {
                        continue;
                    }
                    $sjanuary=0;
                    $sfebruary=0;
                    $smarch=0;
                    $sfirstquarter=0;
                    $sapril=0;
                    $smay=0;
                    $sjune=0;
                    $ssecondquarter=0;
                    $sjuly=0;
                    $saugust=0;
                    $sseptember=0;
                    $sthreequarter=0;
                    $soctober=0;
                    $snovember=0;
                    $sdecember=0;
                    $sfourthquarter=0;
                    $sallyear=0;

                    $accountsjanuary=0;
                    $accountsfebruary=0;
                    $accountsmarch=0;
                    $accountsfirstquarter=0;
                    $accountsapril=0;
                    $accountsmay=0;
                    $accountsjune=0;
                    $accountssecondquarter=0;
                    $accountsjuly=0;
                    $accountsaugust=0;
                    $accountsseptember=0;
                    $accountsthreequarter=0;
                    $accountsoctober=0;
                    $accountsnovember=0;
                    $accountsdecember=0;
                    $accountsfourthquarter=0;
                    $accountsallyear=0;

                    foreach($value2 as $key3=>$value3){
                        if(!is_numeric($key3)){
                            continue;
                        }

                        if($i==0){
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('A'.$current.':A'.($current+$depnum[$key1]));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('A'.$current, $value1['name']);
                            $phpexecl->getActiveSheet()->getStyle('A'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        }
                        if($j==0){
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('B'.$current.':B'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('B'.$current, $value2['name']);
                            $phpexecl->getActiveSheet()->getStyle('B'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        }
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, $value3['username']);
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('D'.$current, (empty($value3['january'])?'':$value3['january']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('E'.$current, (empty($value3['ajanuary'])?'':$value3['ajanuary']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('F'.$current, (empty($value3['february'])?'':$value3['february']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('G'.$current, (empty($value3['afebruary'])?'':$value3['afebruary']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('H'.$current, (empty($value3['march'])?'':$value3['march']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('I'.$current, (empty($value3['amarch'])?'':$value3['amarch']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('J'.$current, (empty($value3['firstquarter'])?'':$value3['firstquarter']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('K'.$current, (empty($value3['afirstquarter'])?'':$value3['afirstquarter']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('L'.$current, (empty($value3['april'])?'':$value3['april']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('M'.$current, (empty($value3['aapril'])?'':$value3['aapril']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('N'.$current, (empty($value3['may'])?'':$value3['may']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('O'.$current, (empty($value3['amay'])?'':$value3['amay']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('P'.$current, (empty($value3['june'])?'':$value3['june']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('Q'.$current, (empty($value3['ajune'])?'':$value3['ajune']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('R'.$current, (empty($value3['secondquarter'])?'':$value3['secondquarter']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('S'.$current, (empty($value3['asecondquarter'])?'':$value3['asecondquarter']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('T'.$current, (empty($value3['july'])?'':$value3['july']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('U'.$current, (empty($value3['ajuly'])?'':$value3['ajuly']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('V'.$current, (empty($value3['august'])?'':$value3['august']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('W'.$current, (empty($value3['aaugust'])?'':$value3['aaugust']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('X'.$current, (empty($value3['september'])?'':$value3['september']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('Y'.$current, (empty($value3['aseptember'])?'':$value3['aseptember']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('Z'.$current, (empty($value3['threequarter'])?'':$value3['threequarter']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('AA'.$current, (empty($value3['athreequarter'])?'':$value3['athreequarter']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('AB'.$current, (empty($value3['october'])?'':$value3['october']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('AB'.$current, (empty($value3['aoctober'])?'':$value3['aoctober']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('AD'.$current, (empty($value3['november'])?'':$value3['november']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('AE'.$current, (empty($value3['anovember'])?'':$value3['anovember']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('AF'.$current, (empty($value3['december'])?'':$value3['december']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('AG'.$current, (empty($value3['adecember'])?'':$value3['adecember']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('AH'.$current, (empty($value3['fourthquarter'])?'':$value3['fourthquarter']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('AI'.$current, (empty($value3['afourthquarter'])?'':$value3['afourthquarter']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('AJ'.$current, (empty($value3['allyear'])?'':$value3['allyear']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('AK'.$current, (empty($value3['aallyear'])?'':$value3['aallyear']));
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':AK'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':AK'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':AK'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $i=1;$j=1;
                        $alljanuary+=$value3['january'];
                        $allfebruary+=$value3['february'];
                        $allmarch+=$value3['march'];
                        $allfirstquarter+=$value3['firstquarter'];
                        $allapril+=$value3['april'];
                        $allmay+=$value3['may'];
                        $alljune+=$value3['june'];
                        $allsecondquarter+=$value3['secondquarter'];
                        $alljuly+=$value3['july'];
                        $allaugust+=$value3['august'];
                        $allseptember+=$value3['september'];
                        $allthreequarter+=$value3['threequarter'];
                        $alloctober+=$value3['october'];
                        $allnovember+=$value3['november'];
                        $alldecember+=$value3['december'];
                        $allfourthquarter+=$value3['fourthquarter'];
                        $allallyear+=$value3['allyear'];

                        $accountalljanuary+=$value3['ajanuary'];
                        $accountallfebruary+=$value3['afebruary'];
                        $accountallmarch+=$value3['amarch'];
                        $accountallfirstquarter+=$value3['afirstquarter'];
                        $accountallapril+=$value3['aapril'];
                        $accountallmay+=$value3['amay'];
                        $accountalljune+=$value3['ajune'];
                        $accountallsecondquarter+=$value3['asecondquarter'];
                        $accountalljuly+=$value3['ajuly'];
                        $accountallaugust+=$value3['aaugust'];
                        $accountallseptember+=$value3['aseptember'];
                        $accountallthreequarter+=$value3['athreequarter'];
                        $accountalloctober+=$value3['aoctober'];
                        $accountallnovember+=$value3['anovember'];
                        $accountalldecember+=$value3['adecember'];
                        $accountallfourthquarter+=$value3['afourthquarter'];
                        $accountallallyear+=$value3['aallyear'];


                        $sjanuary+=$value3['january'];
                        $sfebruary+=$value3['february'];
                        $smarch+=$value3['march'];
                        $sfirstquarter+=$value3['firstquarter'];
                        $sapril+=$value3['april'];
                        $smay+=$value3['may'];
                        $sjune+=$value3['june'];
                        $ssecondquarter+=$value3['secondquarter'];
                        $sjuly+=$value3['july'];
                        $saugust+=$value3['august'];
                        $sseptember+=$value3['september'];
                        $sthreequarter+=$value3['threequarter'];
                        $soctober+=$value3['october'];
                        $snovember+=$value3['november'];
                        $sdecember+=$value3['december'];
                        $sfourthquarter+=$value3['fourthquarter'];
                        $sallyear+=$value3['allyear'];

                        $accountsjanuary+=$value3['ajanuary'];
                        $accountsfebruary+=$value3['afebruary'];
                        $accountsmarch+=$value3['amarch'];
                        $accountsfirstquarter+=$value3['afirstquarter'];
                        $accountsapril+=$value3['aapril'];
                        $accountsmay+=$value3['amay'];
                        $accountsjune+=$value3['ajune'];
                        $accountssecondquarter+=$value3['asecondquarter'];
                        $accountsjuly+=$value3['ajuly'];
                        $accountsaugust+=$value3['aaugust'];
                        $accountsseptember+=$value3['aseptember'];
                        $accountsthreequarter+=$value3['athreequarter'];
                        $accountsoctober+=$value3['aoctober'];
                        $accountsnovember+=$value3['anovember'];
                        $accountsdecember+=$value3['adecember'];
                        $accountsfourthquarter+=$value3['afourthquarter'];
                        $accountsallyear+=$value3['aallyear'];
                        ++$current;
                    }
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, '部门小计');
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('D'.$current, $sjanuary);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('E'.$current, $accountsjanuary);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('F'.$current, $sfebruary);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('G'.$current, $accountsfebruary);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('H'.$current, $smarch);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('I'.$current, $accountsmarch);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('J'.$current, $sfirstquarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('K'.$current, $accountsfirstquarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('L'.$current, $sapril);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('M'.$current, $accountsapril);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('N'.$current, $smay);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('O'.$current, $accountsmay);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('P'.$current, $sjune);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('Q'.$current, $accountsjune);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('R'.$current, $ssecondquarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('S'.$current, $accountssecondquarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('T'.$current, $sjuly);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('U'.$current, $accountsjuly);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('V'.$current, $saugust);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('W'.$current, $accountsaugust);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('X'.$current, $sseptember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('Y'.$current, $accountsseptember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('Z'.$current, $sthreequarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AA'.$current, $accountsthreequarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AB'.$current, $soctober);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AC'.$current, $accountsoctober);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AD'.$current, $snovember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AE'.$current, $accountsnovember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AF'.$current, $sdecember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AG'.$current, $accountsdecember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AH'.$current, $sfourthquarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AI'.$current, $accountsfourthquarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AJ'.$current, $sallyear);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AK'.$current, $accountsallyear);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':AK'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':AK'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':AK'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    ++$current;
                }
                $phpexecl->setActiveSheetIndex(0)->setCellValue('B'.$current, '营总计');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, '');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('D'.$current, $alljanuary);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('E'.$current, $accountalljanuary);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('F'.$current, $allfebruary);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('G'.$current, $accountallfebruary);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('H'.$current, $allmarch);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('I'.$current, $accountallmarch);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('J'.$current, $allfirstquarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('K'.$current, $accountallfirstquarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('L'.$current, $allapril);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('M'.$current, $accountallapril);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('N'.$current, $allmay);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('O'.$current, $accountallmay);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('P'.$current, $alljune);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('Q'.$current, $accountalljune);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('R'.$current, $allsecondquarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('S'.$current, $accountallsecondquarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('T'.$current, $alljuly);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('U'.$current, $accountalljuly);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('V'.$current, $allaugust);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('W'.$current, $accountallaugust);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('W'.$current, $allseptember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('Y'.$current, $accountallseptember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('Z'.$current, $allthreequarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AA'.$current, $accountallthreequarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AB'.$current, $alloctober);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AC'.$current, $accountalloctober);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AE'.$current, $allnovember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AE'.$current, $accountallnovember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AF'.$current, $alldecember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AG'.$current, $accountalldecember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AH'.$current, $allfourthquarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AI'.$current, $accountallfourthquarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AJ'.$current, $allallyear);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AK'.$current, $accountallallyear);
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':AK'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':AK'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':AK'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                ++$current;
                

            }
            
        }
        

        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('拜访量统计');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="拜访量统计'.date('Y-m-dHis').'.xlsx"');
        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');


        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        $objWriter->save('php://output');
    }
    //取得客户数据
    public function getaccountdata(Vtiger_Request $request){
        $datatime=$request->get('datetime');
        $userid=$request->get('userid');
        $departmentid=$request->get('department');
        $querySql='';
        if(empty($userid)||!is_numeric($userid)){
            $departmentarr=array();
            if(!empty($departmentid)&&$departmentid!='null'){
                foreach($departmentid as $value){
                    $userid=getDepartmentUser($value);
                    $where=getAccessibleUsers('Accounts','List',true);
                    if($where!='1=1'){
                        $where=array_intersect($where,$userid);
                    }else{
                        $where=$userid;
                    }
                    if(empty($where)||count($where)==0){
                        continue;
                    }
                    $departmentarr=array_merge($departmentarr,$where);
                }
                $querySql=' AND vtiger_customerstatistics.userid IN('.implode(',',$departmentarr).')';
                $querySqlHK=' AND vtiger_achievementallot.receivedpaymentownid IN('.implode(',',$departmentarr).')';
            }else{
                $where=getAccessibleUsers('Accounts','List',false);
                if($where!='1=1'){
                    $querySql=' AND vtiger_customerstatistics.userid IN('.implode(',',$where).')';
                    $querySqlHK=' AND vtiger_achievementallot.receivedpaymentownid IN('.implode(',',$where).')';
                }
            }
        }else{

            $querySql=' AND vtiger_customerstatistics.userid='.$userid;
            $querySqlHK=' AND vtiger_achievementallot.receivedpaymentownid='.$userid;
        }

        $query='SELECT 
                    sum(if(vtiger_customerstatistics.cmonth=1,vtiger_customerstatistics.newaccount,0)) AS addc1,
                    sum(if(vtiger_customerstatistics.cmonth=2,vtiger_customerstatistics.newaccount,0)) AS addc2,
                    sum(if(vtiger_customerstatistics.cmonth=3,vtiger_customerstatistics.newaccount,0)) AS addc3,
                    sum(if(vtiger_customerstatistics.cmonth=4,vtiger_customerstatistics.newaccount,0)) AS addc4,
                    sum(if(vtiger_customerstatistics.cmonth=5,vtiger_customerstatistics.newaccount,0)) AS addc5,
                    sum(if(vtiger_customerstatistics.cmonth=6,vtiger_customerstatistics.newaccount,0)) AS addc6,
                    sum(if(vtiger_customerstatistics.cmonth=7,vtiger_customerstatistics.newaccount,0)) AS addc7,
                    sum(if(vtiger_customerstatistics.cmonth=8,vtiger_customerstatistics.newaccount,0)) AS addc8,
                    sum(if(vtiger_customerstatistics.cmonth=9,vtiger_customerstatistics.newaccount,0)) AS addc9,
                    sum(if(vtiger_customerstatistics.cmonth=10,vtiger_customerstatistics.newaccount,0)) AS addc10,
                    sum(if(vtiger_customerstatistics.cmonth=11,vtiger_customerstatistics.newaccount,0)) AS addc11,
                    sum(if(vtiger_customerstatistics.cmonth=12,vtiger_customerstatistics.newaccount,0)) AS addc12,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=1,vtiger_customerstatistics.newaccount,0)) AS addcone,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=2,vtiger_customerstatistics.newaccount,0)) AS addctwo,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=3,vtiger_customerstatistics.newaccount,0)) AS addcthree,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=4,vtiger_customerstatistics.newaccount,0)) AS addcforth,
                    sum(vtiger_customerstatistics.newaccount) AS addcyear,
                    sum(if(vtiger_customerstatistics.cmonth=1,vtiger_customerstatistics.forthaccount,0)) AS forthc1,
                    sum(if(vtiger_customerstatistics.cmonth=2,vtiger_customerstatistics.forthaccount,0)) AS forthc2,
                    sum(if(vtiger_customerstatistics.cmonth=3,vtiger_customerstatistics.forthaccount,0)) AS forthc3,
                    sum(if(vtiger_customerstatistics.cmonth=4,vtiger_customerstatistics.forthaccount,0)) AS forthc4,
                    sum(if(vtiger_customerstatistics.cmonth=5,vtiger_customerstatistics.forthaccount,0)) AS forthc5,
                    sum(if(vtiger_customerstatistics.cmonth=6,vtiger_customerstatistics.forthaccount,0)) AS forthc6,
                    sum(if(vtiger_customerstatistics.cmonth=7,vtiger_customerstatistics.forthaccount,0)) AS forthc7,
                    sum(if(vtiger_customerstatistics.cmonth=8,vtiger_customerstatistics.forthaccount,0)) AS forthc8,
                    sum(if(vtiger_customerstatistics.cmonth=9,vtiger_customerstatistics.forthaccount,0)) AS forthc9,
                    sum(if(vtiger_customerstatistics.cmonth=10,vtiger_customerstatistics.forthaccount,0)) AS forthc10,
                    sum(if(vtiger_customerstatistics.cmonth=11,vtiger_customerstatistics.forthaccount,0)) AS forthc11,
                    sum(if(vtiger_customerstatistics.cmonth=12,vtiger_customerstatistics.forthaccount,0)) AS forthc12,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=1,vtiger_customerstatistics.forthaccount,0)) AS forthcone,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=2,vtiger_customerstatistics.forthaccount,0)) AS forthctwo,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=3,vtiger_customerstatistics.forthaccount,0)) AS forthcthree,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=4,vtiger_customerstatistics.forthaccount,0)) AS forthcforth,
                    sum(vtiger_customerstatistics.forthaccount) AS forthcyear,
                    sum(if(vtiger_customerstatistics.cmonth=1,vtiger_customerstatistics.dealaccount,0)) AS dealc1,
                    sum(if(vtiger_customerstatistics.cmonth=2,vtiger_customerstatistics.dealaccount,0)) AS dealc2,
                    sum(if(vtiger_customerstatistics.cmonth=3,vtiger_customerstatistics.dealaccount,0)) AS dealc3,
                    sum(if(vtiger_customerstatistics.cmonth=4,vtiger_customerstatistics.dealaccount,0)) AS dealc4,
                    sum(if(vtiger_customerstatistics.cmonth=5,vtiger_customerstatistics.dealaccount,0)) AS dealc5,
                    sum(if(vtiger_customerstatistics.cmonth=6,vtiger_customerstatistics.dealaccount,0)) AS dealc6,
                    sum(if(vtiger_customerstatistics.cmonth=7,vtiger_customerstatistics.dealaccount,0)) AS dealc7,
                    sum(if(vtiger_customerstatistics.cmonth=8,vtiger_customerstatistics.dealaccount,0)) AS dealc8,
                    sum(if(vtiger_customerstatistics.cmonth=9,vtiger_customerstatistics.dealaccount,0)) AS dealc9,
                    sum(if(vtiger_customerstatistics.cmonth=10,vtiger_customerstatistics.dealaccount,0)) AS dealc10,
                    sum(if(vtiger_customerstatistics.cmonth=11,vtiger_customerstatistics.dealaccount,0)) AS dealc11,
                    sum(if(vtiger_customerstatistics.cmonth=12,vtiger_customerstatistics.dealaccount,0)) AS dealc12,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=1,vtiger_customerstatistics.dealaccount,0)) AS dealcone,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=2,vtiger_customerstatistics.dealaccount,0)) AS dealctwo,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=3,vtiger_customerstatistics.dealaccount,0)) AS dealcthree,
                    sum(if(QUARTER(vtiger_customerstatistics.createdtime)=4,vtiger_customerstatistics.dealaccount,0)) AS dealcforth,
                    sum(vtiger_customerstatistics.dealaccount) AS dealcyear,
                    0 as hkc1,
                    0 as hkc2,
                    0 as hkc3,
                    0 as hkc4,
                    0 as hkc5,
                    0 as hkc6,
                    0 as hkc7,
                    0 as hkc8,
                    0 as hkc9,
                    0 as hkc10,
                    0 as hkc11,
                    0 as hkc12,
                    0 as hkcone,
                    0 as hkctwo,
                    0 as hkcthree,
                    0 as hkcforth,
                    0 as hkcyear,
                    vtiger_users.last_name AS username,
                    vtiger_customerstatistics.userid,
                    SUBSTRING_INDEX(vtiger_departments.parentdepartment,\'::\',-2) AS department
                FROM vtiger_customerstatistics
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_customerstatistics.userid
                LEFT JOIN vtiger_user2department ON vtiger_users.id= vtiger_user2department.userid
                LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                WHERE
                vtiger_departments.parentdepartment IS NOT NULL
                AND
                    vtiger_customerstatistics.cyear=?
                    '.$querySql.'
                GROUP BY vtiger_customerstatistics.userid
                ORDER BY department';
        //echo $query;exit;
        $hkquery='SELECT 
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=1,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc1,
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=2,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc2,
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=3,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc3,
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=4,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc4,
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=5,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc5,
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=6,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc6,
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=7,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc7,
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=8,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc8,
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=9,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc9,
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=10,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc10,
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=11,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc11,
                    SUM(IF(MONTH(vtiger_receivedpayments.reality_date)=12,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkc12,
                    SUM(IF(QUARTER(vtiger_receivedpayments.reality_date)=1,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkcone,
                    SUM(IF(QUARTER(vtiger_receivedpayments.reality_date)=2,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkctwo,
                    SUM(IF(QUARTER(vtiger_receivedpayments.reality_date)=3,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkcthree,
                    SUM(IF(QUARTER(vtiger_receivedpayments.reality_date)=4,IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hkcforth,
                    SUM(IFNULL(vtiger_achievementallot.businessunit,0)) AS hkcyear,
                    vtiger_achievementallot.receivedpaymentownid as userid,
                    vtiger_users.last_name AS username,
                        SUBSTRING_INDEX(vtiger_departments.parentdepartment,\'::\',-2) AS department
                FROM vtiger_achievementallot 
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid 
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                LEFT JOIN vtiger_user2department ON vtiger_users.id= vtiger_user2department.userid
                LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                WHERE              
                    vtiger_departments.parentdepartment IS NOT NULL
                    AND YEAR(vtiger_receivedpayments.reality_date)=?
                    '.$querySqlHK.'
                GROUP BY vtiger_achievementallot.receivedpaymentownid
                ORDER BY department';

        $db=PearDatabase::getInstance();
        $hkresult=$db->pquery($hkquery,array($datatime));//处理回款结果
        $hknum=$db->num_rows($hkresult);//处理回款结果
        $result=$db->pquery($query,array($datatime));
        $num=$db->num_rows($result);
        
        if($num || $hknum){
            $array=array();
            $cachedepartment=getDepartment();

            for($i=0;$i<$num;$i++){
                $depart=$db->query_result($result,$i,'department');
                $depart=explode('::',$depart);
                $useid=$db->query_result($result,$i,'userid');
                if(!empty($departmentid)&&$departmentid!='null'){
                    if(in_array($depart[1],$departmentid)){
                        $array[$depart[1]][$depart[1].'D'][$useid]=$db->query_result_rowdata($result,$i);
                        $array[$depart[1]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                        $array[$depart[1]][$depart[1].'D']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]).'D';
                    }else{
                        $array[$depart[0]][$depart[1].'M'][$useid]=$db->query_result_rowdata($result,$i);
                        $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                        $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                    }
                    
                }else{
                    $array[$depart[0]][$depart[1].'M'][$useid]=$db->query_result_rowdata($result,$i);
                    $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                    $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]); 
                }
                
            }
            $hk=array('addc1' =>0,'addc2' => 0,'addc3' => 0,'addc4' => 0,'addc5' => 0,
                'addc6' =>0,'addc7' =>0,'addc8' =>0,'addc9' =>0,'addc10' => 0,
                'addc11' => 0,'addc12' => 0,'addcone' => 0,'addctwo' => 0,'addcthree' => 0,
                'addcforth' => 0,'addcyear' => 0,'forthc1' => 0,'forthc2' => 0,'forthc3' => 0,
                'forthc4' => 0,'forthc5' => 0,'forthc6' => 0,'forthc7' => 0,'forthc8' => 0,
                'forthc9' => 0,'forthc10' => 0,'forthc11' => 0,'forthc12' => 0,'forthcone' => 0,
                'forthctwo' => 0,'forthcthree' =>0,'forthcforth' => 0,'forthcyear' => 0,'dealc1' => 0,
                'dealc2' => 0,'dealc3' => 0,'dealc4' => 0,'dealc5' => 0,'dealc6' => 0,'dealc7' => 0,
                'dealc8' => 0,'dealc9' => 0,'dealc10' => 0,'dealc11' => 0,'dealc12' => 0,'dealcone' => 0,
                'dealctwo' => 0,'dealcthree' => 0,'dealcforth' => 0,'dealcyear' =>0);
            //start处理回款
            for($i=0;$i<$hknum;$i++){
                $depart=$db->query_result($hkresult,$i,'department');
                $userid=$db->query_result($hkresult,$i,'userid');
                $depart=explode('::',$depart);
                if(!empty($departmentid)&&$departmentid!='null'){
                    if(in_array($depart[1],$departmentid)){
                        $temp=empty($array[$depart[1]][$depart[1].'D'][$userid])?$hk:$array[$depart[1]][$depart[1].'D'][$userid];
                        $array[$depart[1]][$depart[1].'D'][$userid]=array_merge($temp,$db->query_result_rowdata($hkresult,$i));
                        $array[$depart[1]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                        $array[$depart[1]][$depart[1].'D']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]).'D';
                    }else{
                        $temp=empty($array[$depart[0]][$depart[1].'M'][$userid])?$hk:$array[$depart[0]][$depart[1].'M'][$userid];
                        $array[$depart[0]][$depart[1].'M'][$userid]=array_merge($temp,$db->query_result_rowdata($hkresult,$i));
                        $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                        $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                    }
                }else{
                    $temp=empty($array[$depart[0]][$depart[1].'M'][$userid])?$hk:$array[$depart[0]][$depart[1].'M'][$userid];
                    $array[$depart[0]][$depart[1].'M'][$userid]=array_merge($temp,$db->query_result_rowdata($hkresult,$i));
                    $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                    $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                }
            }

            //end处理回款
            $depnum=array();
            foreach($array as $key=>$value){
                if($key=='name')continue;
                foreach($value as $k=>$v){
                    if($k=='name')continue;
                    $depnum[$key]+=count($v);
                    $depnum[$k]+=count($v);
                }
            }
            return array('data'=>$array,'num'=>$depnum);
        }else{

            return array();
        }
    }
    //客户统计
    public function getaccountstatistics(Vtiger_Request $request){
        $data=$this->getaccountdata($request);
        $datatime=$request->get('datetime');
        if(!empty($data)){
            $array=$data['data'];
            $depnum=$data['num'];
            $text='';
            foreach($array as $key1=>$value1){
                $i=0;
                if($key1=='name'){
                    continue;
                }
                $newcalljanuary=0;
                $newcallfebruary=0;
                $newcallmarch=0;
                $newcallfirstquarter=0;
                $newcallapril=0;
                $newcallmay=0;
                $newcalljune=0;
                $newcallsecondquarter=0;
                $newcalljuly=0;
                $newcallaugust=0;
                $newcallseptember=0;
                $newcallthreequarter=0;
                $newcalloctober=0;
                $newcallnovember=0;
                $newcalldecember=0;
                $newcallfourthquarter=0;
                $newcallallyear=0;
                
                $forthalljanuary=0;
                $forthallfebruary=0;
                $forthallmarch=0;
                $forthallfirstquarter=0;
                $forthallapril=0;
                $forthallmay=0;
                $forthalljune=0;
                $forthallsecondquarter=0;
                $forthalljuly=0;
                $forthallaugust=0;
                $forthallseptember=0;
                $forthallthreequarter=0;
                $forthalloctober=0;
                $forthallnovember=0;
                $forthalldecember=0;
                $forthallfourthquarter=0;
                $forthallallyear=0;
                
                $dealalljanuary=0;
                $dealallfebruary=0;
                $dealallmarch=0;
                $dealallfirstquarter=0;
                $dealallapril=0;
                $dealallmay=0;
                $dealalljune=0;
                $dealallsecondquarter=0;
                $dealalljuly=0;
                $dealallaugust=0;
                $dealallseptember=0;
                $dealallthreequarter=0;
                $dealalloctober=0;
                $dealallnovember=0;
                $dealalldecember=0;
                $dealallfourthquarter=0;
                $dealallallyear=0;
                //回款
                $hkalljanuary=0;
                $hkallfebruary=0;
                $hkallmarch=0;
                $hkallfirstquarter=0;
                $hkallapril=0;
                $hkallmay=0;
                $hkalljune=0;
                $hkallsecondquarter=0;
                $hkalljuly=0;
                $hkallaugust=0;
                $hkallseptember=0;
                $hkallthreequarter=0;
                $hkalloctober=0;
                $hkallnovember=0;
                $hkalldecember=0;
                $hkallfourthquarter=0;
                $hkallallyear=0;
                foreach($value1 as $key2=>$value2){
                    $j=0;
                    if($key2=='name') {
                        continue;
                    }
                    $newcsjanuary=0;
                    $newcsfebruary=0;
                    $newcsmarch=0;
                    $newcsfirstquarter=0;
                    $newcsapril=0;
                    $newcsmay=0;
                    $newcsjune=0;
                    $newcssecondquarter=0;
                    $newcsjuly=0;
                    $newcsaugust=0;
                    $newcsseptember=0;
                    $newcsthreequarter=0;
                    $newcsoctober=0;
                    $newcsnovember=0;
                    $newcsdecember=0;
                    $newcsfourthquarter=0;
                    $newcsallyear=0;
                    
                    $forthsjanuary=0;
                    $forthsfebruary=0;
                    $forthsmarch=0;
                    $forthsfirstquarter=0;
                    $forthsapril=0;
                    $forthsmay=0;
                    $forthsjune=0;
                    $forthssecondquarter=0;
                    $forthsjuly=0;
                    $forthsaugust=0;
                    $forthsseptember=0;
                    $forthsthreequarter=0;
                    $forthsoctober=0;
                    $forthsnovember=0;
                    $forthsdecember=0;
                    $forthsfourthquarter=0;
                    $forthsallyear=0;
                    
                    $dealsjanuary=0;
                    $dealsfebruary=0;
                    $dealsmarch=0;
                    $dealsfirstquarter=0;
                    $dealsapril=0;
                    $dealsmay=0;
                    $dealsjune=0;
                    $dealssecondquarter=0;
                    $dealsjuly=0;
                    $dealsaugust=0;
                    $dealsseptember=0;
                    $dealsthreequarter=0;
                    $dealsoctober=0;
                    $dealsnovember=0;
                    $dealsdecember=0;
                    $dealsfourthquarter=0;
                    $dealsallyear=0;



                    $hksjanuary=0;
                    $hksfebruary=0;
                    $hksmarch=0;
                    $hksfirstquarter=0;
                    $hksapril=0;
                    $hksmay=0;
                    $hksjune=0;
                    $hkssecondquarter=0;
                    $hksjuly=0;
                    $hksaugust=0;
                    $hksseptember=0;
                    $hksthreequarter=0;
                    $hksoctober=0;
                    $hksnovember=0;
                    $hksdecember=0;
                    $hksfourthquarter=0;
                    $hksallyear=0;

                    foreach($value2 as $key3=>$value3){

                        /*if("name"==$key3){
                            continue;
                        }*/
                        if(!is_numeric($key3)){
                            continue;
                        }

                        if($i==0){
                            $center='<td rowspan="'.($depnum[$key1]+1).'" style="text-align: center;vertical-align:middle;">'.$value1['name'].'</td>';
                        }else{
                            $center='';
                        }
                        if($j==0){
                            $departname='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;">'.$value2['name'].'</td>';
                        }else{
                            $departname='';
                        }
                        $text.='
                        <tr>'.$center.$departname.'
                            <td style="text-align: center;vertical-align:middle;">'.$value3['username'].'</td>
                            <td style="text-align: center;vertical-align:middle;" title="1月">'.(empty($value3['addc1'])?'&nbsp;':$value3['addc1']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="2月">'.(empty($value3['addc2'])?'&nbsp;':$value3['addc2']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="3">'.(empty($value3['addc3'])?'&nbsp;':$value3['addc3']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="4月">'.(empty($value3['addc4'])?'&nbsp;':$value3['addc4']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="5月">'.(empty($value3['addc5'])?'&nbsp;':$value3['addc5']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="6月">'.(empty($value3['addc6'])?'&nbsp;':$value3['addc6']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="7月">'.(empty($value3['addc7'])?'&nbsp;':$value3['addc7']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="8月">'.(empty($value3['addc8'])?'&nbsp;':$value3['addc8']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="9月">'.(empty($value3['addc9'])?'&nbsp;':$value3['addc9']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="10月">'.(empty($value3['addc10'])?'&nbsp;':$value3['addc10']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="11月">'.(empty($value3['addc11'])?'&nbsp;':$value3['addc11']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="12月">'.(empty($value3['addc12'])?'&nbsp;':$value3['addc12']).'</td>
                            
                            <td style="text-align: center;vertical-align:middle;" title="1月">'.(empty($value3['forthc1'])?'&nbsp;':$value3['forthc1']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="2月">'.(empty($value3['forthc2'])?'&nbsp;':$value3['forthc2']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="3">'.(empty($value3['forthc3'])?'&nbsp;':$value3['forthc3']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="4月">'.(empty($value3['forthc4'])?'&nbsp;':$value3['forthc4']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="5月">'.(empty($value3['forthc5'])?'&nbsp;':$value3['forthc5']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="6月">'.(empty($value3['forthc6'])?'&nbsp;':$value3['forthc6']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="7月">'.(empty($value3['forthc7'])?'&nbsp;':$value3['forthc7']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="8月">'.(empty($value3['forthc8'])?'&nbsp;':$value3['forthc8']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="9月">'.(empty($value3['forthc9'])?'&nbsp;':$value3['forthc9']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="10月">'.(empty($value3['forthc10'])?'&nbsp;':$value3['forthc10']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="11月">'.(empty($value3['forthc11'])?'&nbsp;':$value3['forthc11']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="12月">'.(empty($value3['forthc12'])?'&nbsp;':$value3['forthc12']).'</td>
                            
                            <td style="text-align: center;vertical-align:middle;" title="1月">'.(empty($value3['dealc1'])?'&nbsp;':$value3['dealc1']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="2月">'.(empty($value3['dealc2'])?'&nbsp;':$value3['dealc2']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="3">'.(empty($value3['dealc3'])?'&nbsp;':$value3['dealc3']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="4月">'.(empty($value3['dealc4'])?'&nbsp;':$value3['dealc4']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="5月">'.(empty($value3['dealc5'])?'&nbsp;':$value3['dealc5']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="6月">'.(empty($value3['dealc6'])?'&nbsp;':$value3['dealc6']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="7月">'.(empty($value3['dealc7'])?'&nbsp;':$value3['dealc7']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="8月">'.(empty($value3['dealc8'])?'&nbsp;':$value3['dealc8']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="9月">'.(empty($value3['dealc9'])?'&nbsp;':$value3['dealc9']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="10月">'.(empty($value3['dealc10'])?'&nbsp;':$value3['dealc10']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="11月">'.(empty($value3['dealc11'])?'&nbsp;':$value3['dealc11']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="12月">'.(empty($value3['dealc12'])?'&nbsp;':$value3['dealc12']).'</td>

                            <td style="text-align: center;vertical-align:middle;" title="1月">'.((empty($value3['hkc1']) || $value3['hkc1']==0.00)?'&nbsp;':$value3['hkc1']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="2月">'.((empty($value3['hkc2']) || $value3['hkc2']==0.00)?'&nbsp;':$value3['hkc2']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="3">'.((empty($value3['hkc3']) || $value3['hkc3']==0.00)?'&nbsp;':$value3['hkc3']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="4月">'.((empty($value3['hkc4']) || $value3['hkc4']==0.00)?'&nbsp;':$value3['hkc4']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="5月">'.((empty($value3['hkc5']) || $value3['hkc5']==0.00)?'&nbsp;':$value3['hkc5']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="6月">'.((empty($value3['hkc6']) || $value3['hkc6']==0.00)?'&nbsp;':$value3['hkc6']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="7月">'.((empty($value3['hkc7']) || $value3['hkc7']==0.00)?'&nbsp;':$value3['hkc7']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="8月">'.((empty($value3['hkc8']) || $value3['hkc8']==0.00)?'&nbsp;':$value3['hkc8']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="9月">'.((empty($value3['hkc9']) || $value3['hkc9']==0.00)?'&nbsp;':$value3['hkc9']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="10月">'.((empty($value3['hkc10']) || $value3['hkc10']==0.00)?'&nbsp;':$value3['hkc10']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="11月">'.((empty($value3['hkc11']) || $value3['hkc11']==0.00)?'&nbsp;':$value3['hkc11']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="12月">'.((empty($value3['hkc12']) || $value3['hkc12']==0.00)?'&nbsp;':$value3['hkc12']).'</td>

                            <td style="text-align: center;vertical-align:middle;" title="第一季度">'.(empty($value3['addcone'])?'&nbsp;':$value3['addcone']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第二季度">'.(empty($value3['addctwo'])?'&nbsp;':$value3['addctwo']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第三季度">'.(empty($value3['addcthree'])?'&nbsp;':$value3['addcthree']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第四季度">'.(empty($value3['addcforth'])?'&nbsp;':$value3['addcforth']).'</td>
                            
                            <td style="text-align: center;vertical-align:middle;" title="第一季度">'.(empty($value3['forthcone'])?'&nbsp;':$value3['forthcone']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第二季度">'.(empty($value3['forthctwo'])?'&nbsp;':$value3['forthctwo']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第三季度">'.(empty($value3['forthcthree'])?'&nbsp;':$value3['forthcthree']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第四季度">'.(empty($value3['forthcforth'])?'&nbsp;':$value3['forthcforth']).'</td>
                            
                            <td style="text-align: center;vertical-align:middle;" title="第一季度">'.(empty($value3['dealcone'])?'&nbsp;':$value3['dealcone']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第二季度">'.(empty($value3['dealctwo'])?'&nbsp;':$value3['dealctwo']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第三季度">'.(empty($value3['dealcthree'])?'&nbsp;':$value3['dealcthree']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第四季度">'.(empty($value3['dealcforth'])?'&nbsp;':$value3['dealcforth']).'</td>

                            <td style="text-align: center;vertical-align:middle;" title="第一季度">'.((empty($value3['hkcone']) || $value3['hkcone']==0.00)?'&nbsp;':$value3['hkcone']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第二季度">'.((empty($value3['hkctwo']) || $value3['hkcone']==0.00)?'&nbsp;':$value3['hkctwo']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第三季度">'.((empty($value3['hkcthree']) || $value3['hkcone']==0.00)?'&nbsp;':$value3['hkcthree']).'</td>
                            <td style="text-align: center;vertical-align:middle;" title="第四季度">'.((empty($value3['hkcforth']) || $value3['hkcone']==0.00)?'&nbsp;':$value3['hkcforth']).'</td>

                            <td style="text-align: center;vertical-align:middle;" title="新增客户">'.(empty($value3['addcyear'])?'&nbsp;':'<span class="label label-a_exception">'.$value3['addcyear'].'</span>').'</td>
                            <td style="text-align: center;vertical-align:middle;" title="转化客户">'.(empty($value3['forthcyear'])?'&nbsp;':'<span class="label label-a_exception">'.$value3['forthcyear'].'</span>').'</td>
                            <td style="text-align: center;vertical-align:middle;" title="成交客户">'.(empty($value3['dealcyear'])?'&nbsp;':'<span class="label label-a_exception">'.$value3['dealcyear'].'</span>').'</td>   
                            <td style="text-align: center;vertical-align:middle;" title="到账金额">'.(empty($value3['hkcyear'])?'&nbsp;':'<span class="label label-a_exception">'.$value3['hkcyear'].'</span>').'</td>
                        </tr>
                        ';
                        $i=1;$j=1;
                        
                        $newcsjanuary+=$value3['addc1'];
                        $newcsfebruary+=$value3['addc2'];
                        $newcsmarch+=$value3['addc3'];
                        $newcsfirstquarter+=$value3['addcone'];
                        $newcsapril+=$value3['addc4'];
                        $newcsmay+=$value3['addc5'];
                        $newcsjune+=$value3['addc6'];
                        $newcssecondquarter+=$value3['addctwo'];
                        $newcsjuly+=$value3['addc7'];
                        $newcsaugust+=$value3['addc8'];
                        $newcsseptember+=$value3['addc9'];
                        $newcsthreequarter+=$value3['addcthree'];
                        $newcsoctober+=$value3['addc10'];
                        $newcsnovember+=$value3['addc11'];
                        $newcsdecember+=$value3['addc12'];
                        $newcsfourthquarter+=$value3['addcforth'];
                        $newcsallyear+=$value3['addcyear'];
                        
                        $forthsjanuary+=$value3['forthc1'];
                        $forthsfebruary+=$value3['forthc2'];
                        $forthsmarch+=$value3['forthc3'];
                        $forthsfirstquarter+=$value3['forthcone'];
                        $forthsapril+=$value3['forthc4'];
                        $forthsmay+=$value3['forthc5'];
                        $forthsjune+=$value3['forthc6'];
                        $forthssecondquarter+=$value3['forthctwo'];
                        $forthsjuly+=$value3['forthc7'];
                        $forthsaugust+=$value3['forthc8'];
                        $forthsseptember+=$value3['forthc9'];
                        $forthsthreequarter+=$value3['forthcthree'];
                        $forthsoctober+=$value3['forthc10'];
                        $forthsnovember+=$value3['forthc11'];
                        $forthsdecember+=$value3['forthc12'];
                        $forthsfourthquarter+=$value3['forthcforth'];
                        $forthsallyear+=$value3['forthcyear'];
                        
                        $dealsjanuary+=$value3['dealc1'];
                        $dealsfebruary+=$value3['dealc2'];
                        $dealsmarch+=$value3['dealc3'];
                        $dealsfirstquarter+=$value3['dealcone'];
                        $dealsapril+=$value3['dealc4'];
                        $dealsmay+=$value3['dealc5'];
                        $dealsjune+=$value3['dealc6'];
                        $dealssecondquarter+=$value3['dealctwo'];
                        $dealsjuly+=$value3['dealc7'];
                        $dealsaugust+=$value3['dealc8'];
                        $dealsseptember+=$value3['dealc9'];
                        $dealsthreequarter+=$value3['dealcthree'];
                        $dealsoctober+=$value3['dealc10'];
                        $dealsnovember+=$value3['dealc11'];
                        $dealsdecember+=$value3['dealc12'];
                        $dealsfourthquarter+=$value3['dealcforth'];
                        $dealsallyear+=$value3['dealcyear'];




                        $hksjanuary+=$value3['hkc1'];
                        $hksfebruary+=$value3['hkc2'];
                        $hksmarch+=$value3['hkc3'];
                        $hksfirstquarter+=$value3['hkcone'];
                        $hksapril+=$value3['hkc4'];
                        $hksmay+=$value3['hkc5'];
                        $hksjune+=$value3['hkc6'];
                        $hkssecondquarter+=$value3['hkctwo'];
                        $hksjuly+=$value3['hkc7'];
                        $hksaugust+=$value3['hkc8'];
                        $hksseptember+=$value3['hkc9'];
                        $hksthreequarter+=$value3['hkcthree'];
                        $hksoctober+=$value3['hkc10'];
                        $hksnovember+=$value3['hkc11'];
                        $hksdecember+=$value3['hkc12'];
                        $hksfourthquarter+=$value3['hkcforth'];
                        $hksallyear+=$value3['hkcyear'];
                        
                        
                        //total
                        $newcalljanuary+=$value3['addc1'];
                        $newcallfebruary+=$value3['addc2'];
                        $newcallmarch+=$value3['addc3'];
                        $newcallfirstquarter+=$value3['addcone'];
                        $newcallapril+=$value3['addc4'];
                        $newcallmay+=$value3['addc5'];
                        $newcalljune+=$value3['addc6'];
                        $newcallsecondquarter+=$value3['addctwo'];
                        $newcalljuly+=$value3['addc7'];
                        $newcallaugust+=$value3['addc8'];
                        $newcallseptember+=$value3['addc9'];
                        $newcallthreequarter+=$value3['addcthree'];
                        $newcalloctober+=$value3['addc10'];
                        $newcallnovember+=$value3['addc11'];
                        $newcalldecember+=$value3['addc12'];
                        $newcallfourthquarter+=$value3['addcforth'];
                        $newcallallyear+=$value3['addcyear'];
                        
                        $forthalljanuary+=$value3['forthc1'];
                        $forthallfebruary+=$value3['forthc2'];
                        $forthallmarch+=$value3['forthc3'];
                        $forthallfirstquarter+=$value3['forthcone'];
                        $forthallapril+=$value3['forthc4'];
                        $forthallmay+=$value3['forthc5'];
                        $forthalljune+=$value3['forthc6'];
                        $forthallsecondquarter+=$value3['forthctwo'];
                        $forthalljuly+=$value3['forthc7'];
                        $forthallaugust+=$value3['forthc8'];
                        $forthallseptember+=$value3['forthc9'];
                        $forthallthreequarter+=$value3['forthcthree'];
                        $forthalloctober+=$value3['forthc10'];
                        $forthallnovember+=$value3['forthc11'];
                        $forthalldecember+=$value3['forthc12'];
                        $forthallfourthquarter+=$value3['forthcforth'];
                        $forthallallyear+=$value3['forthcyear'];
                        //total
                        $dealalljanuary+=$value3['dealc1'];
                        $dealallfebruary+=$value3['dealc2'];
                        $dealallmarch+=$value3['dealc3'];
                        $dealallfirstquarter+=$value3['dealcone'];
                        $dealallapril+=$value3['dealc4'];
                        $dealallmay+=$value3['dealc5'];
                        $dealalljune+=$value3['dealc6'];
                        $dealallsecondquarter+=$value3['dealctwo'];
                        $dealalljuly+=$value3['dealc7'];
                        $dealallaugust+=$value3['dealc8'];
                        $dealallseptember+=$value3['dealc9'];
                        $dealallthreequarter+=$value3['dealcthree'];
                        $dealalloctober+=$value3['dealc10'];
                        $dealallnovember+=$value3['dealc11'];
                        $dealalldecember+=$value3['dealc12'];
                        $dealallfourthquarter+=$value3['dealcforth'];
                        $dealallallyear+=$value3['dealcyear'];



                        $hkalljanuary+=$value3['hkc1'];
                        $hkallfebruary+=$value3['hkc2'];
                        $hkallmarch+=$value3['hkc3'];
                        $hkallfirstquarter+=$value3['hkcone'];
                        $hkallapril+=$value3['hkc4'];
                        $hkallmay+=$value3['hkc5'];
                        $hkalljune+=$value3['hkc6'];
                        $hkallsecondquarter+=$value3['hkctwo'];
                        $hkalljuly+=$value3['hkc7'];
                        $hkallaugust+=$value3['hkc8'];
                        $hkallseptember+=$value3['hkc9'];
                        $hkallthreequarter+=$value3['hkcthree'];
                        $hkalloctober+=$value3['hkc10'];
                        $hkallnovember+=$value3['hkc11'];
                        $hkalldecember+=$value3['hkc12'];
                        $hkallfourthquarter+=$value3['hkcforth'];
                        $hkallallyear+=$value3['hkcyear'];
                    }
                    $text.='<tr>
                        <td style="text-align: center;vertical-align:middle;"><span class="label label-b_check"> 部门小计</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-a_normal">'.$newcsjanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-a_normal">'.$newcsfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-a_normal">'.$newcsmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-a_normal">'.$newcsapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-a_normal">'.$newcsmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-a_normal">'.$newcsjune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-a_normal">'.$newcsjuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-a_normal">'.$newcsaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-a_normal">'.$newcsseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-a_normal">'.$newcsoctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-a_normal">'.$newcsnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-a_normal">'.$newcsdecember.'</span></td>
                        
                        
                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-warning">'.$forthsjanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-warning">'.$forthsfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-warning">'.$forthsmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-warning">'.$forthsapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-warning">'.$forthsmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-warning">'.$forthsjune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-warning">'.$forthsjuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-warning">'.$forthsaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-warning">'.$forthsseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-warning">'.$forthsoctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-warning">'.$forthsnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-warning">'.$forthsdecember.'</span></td>
                        
                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-success">'.$dealsjanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-success">'.$dealsfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-success">'.$dealsmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-success">'.$dealsapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-success">'.$dealsmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-success">'.$dealsjune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-success">'.$dealsjuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-success">'.$dealsaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-success">'.$dealsseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-success">'.$dealsoctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-success">'.$dealsnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-success">'.$dealsdecember.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-b_check">'.$hksjanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-b_check">'.$hksfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-b_check">'.$hksmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-b_check">'.$hksapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-b_check">'.$hksmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-b_check">'.$hksjune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-b_check">'.$hksjuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-b_check">'.$hksaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-b_check">'.$hksseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-b_check">'.$hksoctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-b_check">'.$hksnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-b_check">'.$hksdecember.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-a_normal">'.$newcsfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-a_normal">'.$newcssecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-a_normal">'.$newcsthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-a_normal">'.$newcsfourthquarter.'</span></td>
                        
                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-warning">'.$forthsfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-warning">'.$forthssecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-warning">'.$forthsthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-warning">'.$forthsfourthquarter.'</span></td>
                        
                    
                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-success">'.$dealsfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-success">'.$dealssecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-success">'.$dealsthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-success">'.$dealsfourthquarter.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-b_check">'.$hksfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-b_check">'.$hkssecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-b_check">'.$hksthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-b_check">'.$hksfourthquarter.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="新增客户"><span class="label label-inverse">'.$newcsallyear.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="转化客户"><span class="label label-inverse">'.$forthsallyear.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="成交客户"><span class="label label-inverse">'.$dealsallyear.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="成交客户"><span class="label label-inverse">'.$hksallyear.'</span></td>

                    
                    </tr>';
                }
                $text.='<tr>
                        <td style="text-align: center;vertical-align:middle;"><span class="label label-a_exception">营总计</span></td>
                        <td>&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-warning">'.$newcalljanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-warning">'.$newcallfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-warning">'.$newcallmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-warning">'.$newcallapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-warning">'.$newcallmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-warning">'.$newcalljune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-warning">'.$newcalljuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-warning">'.$newcallaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-warning">'.$newcallseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-warning">'.$newcalloctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-warning">'.$newcallnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-warning">'.$newcalldecember.'</span></td>
                        
                        
                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-success">'.$forthalljanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-success">'.$forthallfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-success">'.$forthallmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-success">'.$forthallapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-success">'.$forthallmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-success">'.$forthalljune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-success">'.$forthalljuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-success">'.$forthallaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-success">'.$forthallseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-success">'.$forthalloctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-success">'.$forthallnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-success">'.$forthalldecember.'</span></td>
                        
                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-a_normal">'.$dealalljanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-a_normal">'.$dealallfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-a_normal">'.$dealallmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-a_normal">'.$dealallapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-a_normal">'.$dealallmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-a_normal">'.$dealalljune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-a_normal">'.$dealalljuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-a_normal">'.$dealallaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-a_normal">'.$dealallseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-a_normal">'.$dealalloctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-a_normal">'.$dealallnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-a_normal">'.$dealalldecember.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-inverse">'.$hkalljanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-inverse">'.$hkallfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-inverse">'.$hkallmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-inverse">'.$hkallapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-inverse">'.$hkallmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-inverse">'.$hkalljune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-inverse">'.$hkalljuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-inverse">'.$hkallaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-inverse">'.$hkallseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-inverse">'.$hkalloctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-inverse">'.$hkallnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-inverse">'.$hkalldecember.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-warning">'.$newcallfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-warning">'.$newcallsecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-warning">'.$newcallthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-warning">'.$newcallfourthquarter.'</span></td>
                        
                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-success">'.$forthallfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-success">'.$forthallsecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-success">'.$forthallthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-success">'.$forthallfourthquarter.'</span></td>
                        
                    
                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-a_normal">'.$dealallfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-a_normal">'.$dealallsecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-a_normal">'.$dealallthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-a_normal">'.$dealallfourthquarter.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-a_exception">'.$hkallfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-a_exception">'.$hkallsecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-a_exception">'.$hkallthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-a_exception">'.$hkallfourthquarter.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="新增客户"><span class="label label-inverse">'.$newcallallyear.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="转化客户"><span class="label label-inverse">'.$forthallallyear.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="成交客户"><span class="label label-inverse">'.$dealallallyear.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="到账金额"><span class="label label-inverse">'.$hkallallyear.'</span></td>
                    </tr>';

            }
            $table='
            <div id="fixscrollrf" class="hide" style="overflow:hidden;z-index:1029;">
                <table class="table table-bordered" id="flalted" style="position:relative;overflow-y: auto">
                    <thead>
                    <tr id="flalte1"  style="background-color:#ffffff;">
                        <th rowspan="3" scope="col" style="text-align: center;vertical-align:middle;">中心</th>
                        <th rowspan="3" scope="col" style="text-align: center;vertical-align:middle;">部门</th>
                        <th rowspan="3" scope="col" style="text-align: center;vertical-align:middle;">姓名</th>
                        <th colspan="48" scope="col" style="text-align: center;vertical-align:middle;">月度</th>
                        <th colspan="16" scope="col" style="text-align: center;vertical-align:middle;">季度</th>
                        <th colspan="4" rowspan="2" scope="col" style="text-align: center;vertical-align:middle;">'.$datatime.'年度</th>
                    </tr>
                    <tr id="flalte2" style="background-color:#ffffff;">
                        <th colspan="12" style="text-align: center;vertical-align:middle;">新增客户</th>
                        <th colspan="12" style="text-align: center;vertical-align:middle;">转化客户</th>
                        <th colspan="12" style="text-align: center;vertical-align:middle;">成交客户</th>
                        <th colspan="12" style="text-align: center;vertical-align:middle;">到账金额</th>
                        <th colspan="4" style="text-align: center;vertical-align:middle;">新增客户</th>
                        <th colspan="4" style="text-align: center;vertical-align:middle;">转化客户</th>
                        <th colspan="4" style="text-align: center;vertical-align:middle;">成交客户</th>
                        <th colspan="4" style="text-align: center;vertical-align:middle;">到账金额</th>
                    </tr>
                    <tr id="flalte3"  style="background-color:#ffffff;">
                        <th style="text-align: center;vertical-align:middle;">一月</th>
                        <th style="text-align: center;vertical-align:middle;">二月</th>
                        <th style="text-align: center;vertical-align:middle;">三月</th>
                        <th style="text-align: center;vertical-align:middle;">四月</th>
                        <th style="text-align: center;vertical-align:middle;">五月</th>
                        <th style="text-align: center;vertical-align:middle;">六月</th>
                        <th style="text-align: center;vertical-align:middle;">七月</th>
                        <th style="text-align: center;vertical-align:middle;">八月</th>
                        <th style="text-align: center;vertical-align:middle;">九月</th>
                        <th style="text-align: center;vertical-align:middle;">十月</th>
                        <th style="text-align: center;vertical-align:middle;">十一月</th>
                        <th style="text-align: center;vertical-align:middle;">十二月</th>
                        <th style="text-align: center;vertical-align:middle;">一月</th>
                        <th style="text-align: center;vertical-align:middle;">二月</th>
                        <th style="text-align: center;vertical-align:middle;">三月</th>
                        <th style="text-align: center;vertical-align:middle;">四月</th>
                        <th style="text-align: center;vertical-align:middle;">五月</th>
                        <th style="text-align: center;vertical-align:middle;">六月</th>
                        <th style="text-align: center;vertical-align:middle;">七月</th>
                        <th style="text-align: center;vertical-align:middle;">八月</th>
                        <th style="text-align: center;vertical-align:middle;">九月</th>
                        <th style="text-align: center;vertical-align:middle;">十月</th>
                        <th style="text-align: center;vertical-align:middle;">十一月</th>
                        <th style="text-align: center;vertical-align:middle;">十二月</th>
                        <th style="text-align: center;vertical-align:middle;">一月</th>
                        <th style="text-align: center;vertical-align:middle;">二月</th>
                        <th style="text-align: center;vertical-align:middle;">三月</th>
                        <th style="text-align: center;vertical-align:middle;">四月</th>
                        <th style="text-align: center;vertical-align:middle;">五月</th>
                        <th style="text-align: center;vertical-align:middle;">六月</th>
                        <th style="text-align: center;vertical-align:middle;">七月</th>
                        <th style="text-align: center;vertical-align:middle;">八月</th>
                        <th style="text-align: center;vertical-align:middle;">九月</th>
                        <th style="text-align: center;vertical-align:middle;">十月</th>
                        <th style="text-align: center;vertical-align:middle;">十一月</th>
                        <th style="text-align: center;vertical-align:middle;">十二月</th>
                        <th style="text-align: center;vertical-align:middle;">一月</th>
                        <th style="text-align: center;vertical-align:middle;">二月</th>
                        <th style="text-align: center;vertical-align:middle;">三月</th>
                        <th style="text-align: center;vertical-align:middle;">四月</th>
                        <th style="text-align: center;vertical-align:middle;">五月</th>
                        <th style="text-align: center;vertical-align:middle;">六月</th>
                        <th style="text-align: center;vertical-align:middle;">七月</th>
                        <th style="text-align: center;vertical-align:middle;">八月</th>
                        <th style="text-align: center;vertical-align:middle;">九月</th>
                        <th style="text-align: center;vertical-align:middle;">十月</th>
                        <th style="text-align: center;vertical-align:middle;">十一月</th>
                        <th style="text-align: center;vertical-align:middle;">十二月</th>
                        <th style="text-align: center;vertical-align:middle;">第一季度</th>
                        <th style="text-align: center;vertical-align:middle;">第二季度</th>
                        <th style="text-align: center;vertical-align:middle;">第三季度</th>
                        <th style="text-align: center;vertical-align:middle;">第四季度</th>
                        <th style="text-align: center;vertical-align:middle;">第一季度</th>
                        <th style="text-align: center;vertical-align:middle;">第二季度</th>
                        <th style="text-align: center;vertical-align:middle;">第三季度</th>
                        <th style="text-align: center;vertical-align:middle;">第四季度</th>
                        <th style="text-align: center;vertical-align:middle;">第一季度</th>
                        <th style="text-align: center;vertical-align:middle;">第二季度</th>
                        <th style="text-align: center;vertical-align:middle;">第三季度</th>
                        <th style="text-align: center;vertical-align:middle;">第四季度</th>
                        <th style="text-align: center;vertical-align:middle;">第一季度</th>
                        <th style="text-align: center;vertical-align:middle;">第二季度</th>
                        <th style="text-align: center;vertical-align:middle;">第三季度</th>
                        <th style="text-align: center;vertical-align:middle;">第四季度</th>
                        <th style="text-align: center;vertical-align:middle;">新增客户</th>
                        <th style="text-align: center;vertical-align:middle;">转化客户</th>
                        <th style="text-align: center;vertical-align:middle;">成交客户</th>   
                        <th style="text-align: center;vertical-align:middle;">到账金额</th>
                    </tr>
                    </thead>
                </table></div>';
            $table.='
                    <div id="scrollrf" style="overflow: auto;">
                    <table class="table table-bordered table-striped" id="one1">
                        <thead>
                        <tr id="flaltt1">
                            <th rowspan="3" scope="col" style="text-align: center;vertical-align:middle;">中心</th>
                            <th rowspan="3" scope="col" style="text-align: center;vertical-align:middle;">部门</th>
                            <th rowspan="3" scope="col" style="text-align: center;vertical-align:middle;">姓名</th>
                            <th colspan="48" scope="col" style="text-align: center;vertical-align:middle;">月度</th>
                            <th colspan="16" scope="col" style="text-align: center;vertical-align:middle;">季度</th>
                            <th colspan="4" rowspan="2" scope="col" style="text-align: center;vertical-align:middle;">'.$datatime.'年度</th>
                        </tr>
                        <tr id="flaltt2">
                            <th colspan="12" style="text-align: center;vertical-align:middle;">新增客户</th>
                            <th colspan="12" style="text-align: center;vertical-align:middle;">转化客户</th>
                            <th colspan="12" style="text-align: center;vertical-align:middle;">成交客户</th>
                            <th colspan="12" style="text-align: center;vertical-align:middle;">到账金额</th>
                            <th colspan="4" style="text-align: center;vertical-align:middle;">新增客户</th>
                            <th colspan="4" style="text-align: center;vertical-align:middle;">转化客户</th>
                            <th colspan="4" style="text-align: center;vertical-align:middle;">成交客户</th>
                            <th colspan="4" style="text-align: center;vertical-align:middle;">到账金额</th>
                        </tr>
                        <tr id="flaltt3">
                            <th style="text-align: center;vertical-align:middle;">一月</th>
                            <th style="text-align: center;vertical-align:middle;">二月</th>
                            <th style="text-align: center;vertical-align:middle;">三月</th>
                            <th style="text-align: center;vertical-align:middle;">四月</th>
                            <th style="text-align: center;vertical-align:middle;">五月</th>
                            <th style="text-align: center;vertical-align:middle;">六月</th>
                            <th style="text-align: center;vertical-align:middle;">七月</th>
                            <th style="text-align: center;vertical-align:middle;">八月</th>
                            <th style="text-align: center;vertical-align:middle;">九月</th>
                            <th style="text-align: center;vertical-align:middle;">十月</th>
                            <th style="text-align: center;vertical-align:middle;">十一月</th>
                            <th style="text-align: center;vertical-align:middle;">十二月</th>
                            <th style="text-align: center;vertical-align:middle;">一月</th>
                            <th style="text-align: center;vertical-align:middle;">二月</th>
                            <th style="text-align: center;vertical-align:middle;">三月</th>
                            <th style="text-align: center;vertical-align:middle;">四月</th>
                            <th style="text-align: center;vertical-align:middle;">五月</th>
                            <th style="text-align: center;vertical-align:middle;">六月</th>
                            <th style="text-align: center;vertical-align:middle;">七月</th>
                            <th style="text-align: center;vertical-align:middle;">八月</th>
                            <th style="text-align: center;vertical-align:middle;">九月</th>
                            <th style="text-align: center;vertical-align:middle;">十月</th>
                            <th style="text-align: center;vertical-align:middle;">十一月</th>
                            <th style="text-align: center;vertical-align:middle;">十二月</th>
                            <th style="text-align: center;vertical-align:middle;">一月</th>
                            <th style="text-align: center;vertical-align:middle;">二月</th>
                            <th style="text-align: center;vertical-align:middle;">三月</th>
                            <th style="text-align: center;vertical-align:middle;">四月</th>
                            <th style="text-align: center;vertical-align:middle;">五月</th>
                            <th style="text-align: center;vertical-align:middle;">六月</th>
                            <th style="text-align: center;vertical-align:middle;">七月</th>
                            <th style="text-align: center;vertical-align:middle;">八月</th>
                            <th style="text-align: center;vertical-align:middle;">九月</th>
                            <th style="text-align: center;vertical-align:middle;">十月</th>
                            <th style="text-align: center;vertical-align:middle;">十一月</th>
                            <th style="text-align: center;vertical-align:middle;">十二月</th>
                            <th style="text-align: center;vertical-align:middle;">一月</th>
                            <th style="text-align: center;vertical-align:middle;">二月</th>
                            <th style="text-align: center;vertical-align:middle;">三月</th>
                            <th style="text-align: center;vertical-align:middle;">四月</th>
                            <th style="text-align: center;vertical-align:middle;">五月</th>
                            <th style="text-align: center;vertical-align:middle;">六月</th>
                            <th style="text-align: center;vertical-align:middle;">七月</th>
                            <th style="text-align: center;vertical-align:middle;">八月</th>
                            <th style="text-align: center;vertical-align:middle;">九月</th>
                            <th style="text-align: center;vertical-align:middle;">十月</th>
                            <th style="text-align: center;vertical-align:middle;">十一月</th>
                            <th style="text-align: center;vertical-align:middle;">十二月</th>
                            <th style="text-align: center;vertical-align:middle;">第一季度</th>
                            <th style="text-align: center;vertical-align:middle;">第二季度</th>
                            <th style="text-align: center;vertical-align:middle;">第三季度</th>
                            <th style="text-align: center;vertical-align:middle;">第四季度</th>
                            <th style="text-align: center;vertical-align:middle;">第一季度</th>
                            <th style="text-align: center;vertical-align:middle;">第二季度</th>
                            <th style="text-align: center;vertical-align:middle;">第三季度</th>
                            <th style="text-align: center;vertical-align:middle;">第四季度</th>
                            <th style="text-align: center;vertical-align:middle;">第一季度</th>
                            <th style="text-align: center;vertical-align:middle;">第二季度</th>
                            <th style="text-align: center;vertical-align:middle;">第三季度</th>
                            <th style="text-align: center;vertical-align:middle;">第四季度</th>
                            <th style="text-align: center;vertical-align:middle;">第一季度</th>
                            <th style="text-align: center;vertical-align:middle;">第二季度</th>
                            <th style="text-align: center;vertical-align:middle;">第三季度</th>
                            <th style="text-align: center;vertical-align:middle;">第四季度</th>
                            <th style="text-align: center;vertical-align:middle;">新增客户</th>
                            <th style="text-align: center;vertical-align:middle;">转化客户</th>
                            <th style="text-align: center;vertical-align:middle;">成交客户</th>   
                            <th style="text-align: center;vertical-align:middle;">到账金额</th>
                        </tr>
                        </thead>
                        <tbody>
                        '.$text.'
                        </tbody>
                    </table></div>';
            echo $table;
            exit;


        }else{

            echo '<table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="text-align: center;vertical-align:middle;">没有记录</th>
                    </tr></thead></table>';
            exit;
        }
    }
    
    
    public function getaccountstatisticsexport(Vtiger_Request $request){
        $data=$this->getaccountdata($request);
        $datatime=$request->get('datetime');
        global $root_directory;
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $phpexecl=new PHPExcel();
        // Set document properties
        $phpexecl->getProperties()->setCreator("liu ganglin")
            ->setLastModifiedBy("liu ganglin")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("servicecontracts");

        // 添加头信处
        $phpexecl->setActiveSheetIndex(0)->mergeCells('A1:A3')
        ->mergeCells('B1:B3')->mergeCells('C1:C3')
            ->mergeCells('D1:AY1')//月度
            ->mergeCells('AZ1:BO1')//季度
            ->mergeCells('BP1:BS2')//年度
            ->mergeCells('D2:O2')//
            ->mergeCells('P2:AA2')
            ->mergeCells('AB2:AM2')
            ->mergeCells('AN2:AY2')
            ->mergeCells('AZ2:BC2')
            ->mergeCells('BD2:BG2')
            ->mergeCells('BH2:BK2')
            ->mergeCells('BL2:BO2')
            ;
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '中心')
            ->setCellValue('B1', '部门')
            ->setCellValue('C1', '姓名')
            ->setCellValue('D1', '月度')
            ->setCellValue('AZ1', '季度')
            ->setCellValue('BP1', $datatime.'年度')
            ->setCellValue('D2', '新增客户')
            ->setCellValue('P2', '转化客户')
            ->setCellValue('AB2', '成交客户')
            ->setCellValue('AN2', '到账金额')
            ->setCellValue('AZ2', '新增客户')
            ->setCellValue('BD2', '转化客户')
            ->setCellValue('BH2', '成交客户')
            ->setCellValue('BL2', '到账金额')
            ->setCellValue('D3', '一月')
            ->setCellValue('E3', '二月')
            ->setCellValue('F3', '三月')
            ->setCellValue('G3', '四月')
            ->setCellValue('H3', '五月')
            ->setCellValue('I3', '六月')
            ->setCellValue('J3', '七月')
            ->setCellValue('K3', '八月')
            ->setCellValue('L3', '九月')
            ->setCellValue('M3', '十月')
            ->setCellValue('N3', '十一月')
            ->setCellValue('O3', '十二月')
            
            ->setCellValue('P3', '一月')
            ->setCellValue('Q3', '二月')
            ->setCellValue('R3', '三月')
            ->setCellValue('S3', '四月')
            ->setCellValue('T3', '五月')
            ->setCellValue('U3', '六月')
            ->setCellValue('V3', '七月')
            ->setCellValue('W3', '八月')
            ->setCellValue('X3', '九月')
            ->setCellValue('Y3', '十月')
            ->setCellValue('Z3', '十一月')
            ->setCellValue('AA3', '十二月')
            
            ->setCellValue('AB3', '一月')
            ->setCellValue('AC3', '二月')
            ->setCellValue('AD3', '三月')
            ->setCellValue('AE3', '四月')
            ->setCellValue('AF3', '五月')
            ->setCellValue('AG3', '六月')
            ->setCellValue('AH3', '七月')
            ->setCellValue('AI3', '八月')
            ->setCellValue('AJ3', '九月')
            ->setCellValue('AK3', '十月')
            ->setCellValue('AL3', '十一月')
            ->setCellValue('AM3', '十二月')

            ->setCellValue('AN3', '一月')
            ->setCellValue('AO3', '二月')
            ->setCellValue('AP3', '三月')
            ->setCellValue('AQ3', '四月')
            ->setCellValue('AR3', '五月')
            ->setCellValue('AS3', '六月')
            ->setCellValue('AT3', '七月')
            ->setCellValue('AU3', '八月')
            ->setCellValue('AV3', '九月')
            ->setCellValue('AW3', '十月')
            ->setCellValue('AX3', '十一月')
            ->setCellValue('AY3', '十二月')
            
            ->setCellValue('AZ3', '第一季度')
            ->setCellValue('BA3', '第二季度')
            ->setCellValue('BB3', '第三季度')
            ->setCellValue('BC3', '第四季度')

            ->setCellValue('BD3', '第一季度')
            ->setCellValue('BE3', '第二季度')
            ->setCellValue('BF3', '第三季度')
            ->setCellValue('BG3', '第四季度')

            ->setCellValue('BH3', '第一季度')
            ->setCellValue('BI3', '第二季度')
            ->setCellValue('BJ3', '第三季度')
            ->setCellValue('BK3', '第四季度')

            ->setCellValue('BL3', '第一季度')
            ->setCellValue('BM3', '第二季度')
            ->setCellValue('BN3', '第三季度')
            ->setCellValue('BO3', '第四季度')

            ->setCellValue('BP3', '新增客户')
            ->setCellValue('BQ3', '转化客户')
            ->setCellValue('BR3', '成交客户')
            ->setCellValue('BS3', '到账金额')
            ;

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:BS3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$phpexecl->getActiveSheet()->getStyle('D2:T2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:BS3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        //$phpexecl->getActiveSheet()->getStyle('A2:T2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $current=4;
        if(!empty($data)){
            $array=$data['data'];
            $depnum=$data['num'];
            $text='';
            foreach($array as $key1=>$value1){
                $i=0;
                if($key1=='name'){
                    continue;
                }
                $newcalljanuary=0;
                $newcallfebruary=0;
                $newcallmarch=0;
                $newcallfirstquarter=0;
                $newcallapril=0;
                $newcallmay=0;
                $newcalljune=0;
                $newcallsecondquarter=0;
                $newcalljuly=0;
                $newcallaugust=0;
                $newcallseptember=0;
                $newcallthreequarter=0;
                $newcalloctober=0;
                $newcallnovember=0;
                $newcalldecember=0;
                $newcallfourthquarter=0;
                $newcallallyear=0;
                
                $forthalljanuary=0;
                $forthallfebruary=0;
                $forthallmarch=0;
                $forthallfirstquarter=0;
                $forthallapril=0;
                $forthallmay=0;
                $forthalljune=0;
                $forthallsecondquarter=0;
                $forthalljuly=0;
                $forthallaugust=0;
                $forthallseptember=0;
                $forthallthreequarter=0;
                $forthalloctober=0;
                $forthallnovember=0;
                $forthalldecember=0;
                $forthallfourthquarter=0;
                $forthallallyear=0;
                
                $dealalljanuary=0;
                $dealallfebruary=0;
                $dealallmarch=0;
                $dealallfirstquarter=0;
                $dealallapril=0;
                $dealallmay=0;
                $dealalljune=0;
                $dealallsecondquarter=0;
                $dealalljuly=0;
                $dealallaugust=0;
                $dealallseptember=0;
                $dealallthreequarter=0;
                $dealalloctober=0;
                $dealallnovember=0;
                $dealalldecember=0;
                $dealallfourthquarter=0;
                $dealallallyear=0;

                $hkalljanuary=0;
                $hkallfebruary=0;
                $hkallmarch=0;
                $hkallfirstquarter=0;
                $hkallapril=0;
                $hkallmay=0;
                $hkalljune=0;
                $hkallsecondquarter=0;
                $hkalljuly=0;
                $hkallaugust=0;
                $hkallseptember=0;
                $hkallthreequarter=0;
                $hkalloctober=0;
                $hkallnovember=0;
                $hkalldecember=0;
                $hkallfourthquarter=0;
                $hkallallyear=0;
                foreach($value1 as $key2=>$value2){
                    $j=0;
                    if($key2=='name') {
                        continue;
                    }
                    $newcsjanuary=0;
                    $newcsfebruary=0;
                    $newcsmarch=0;
                    $newcsfirstquarter=0;
                    $newcsapril=0;
                    $newcsmay=0;
                    $newcsjune=0;
                    $newcssecondquarter=0;
                    $newcsjuly=0;
                    $newcsaugust=0;
                    $newcsseptember=0;
                    $newcsthreequarter=0;
                    $newcsoctober=0;
                    $newcsnovember=0;
                    $newcsdecember=0;
                    $newcsfourthquarter=0;
                    $newcsallyear=0;
                    
                    $forthsjanuary=0;
                    $forthsfebruary=0;
                    $forthsmarch=0;
                    $forthsfirstquarter=0;
                    $forthsapril=0;
                    $forthsmay=0;
                    $forthsjune=0;
                    $forthssecondquarter=0;
                    $forthsjuly=0;
                    $forthsaugust=0;
                    $forthsseptember=0;
                    $forthsthreequarter=0;
                    $forthsoctober=0;
                    $forthsnovember=0;
                    $forthsdecember=0;
                    $forthsfourthquarter=0;
                    $forthsallyear=0;
                    
                    $dealsjanuary=0;
                    $dealsfebruary=0;
                    $dealsmarch=0;
                    $dealsfirstquarter=0;
                    $dealsapril=0;
                    $dealsmay=0;
                    $dealsjune=0;
                    $dealssecondquarter=0;
                    $dealsjuly=0;
                    $dealsaugust=0;
                    $dealsseptember=0;
                    $dealsthreequarter=0;
                    $dealsoctober=0;
                    $dealsnovember=0;
                    $dealsdecember=0;
                    $dealsfourthquarter=0;
                    $dealsallyear=0;


                    $hksjanuary=0;
                    $hksfebruary=0;
                    $hksmarch=0;
                    $hksfirstquarter=0;
                    $hksapril=0;
                    $hksmay=0;
                    $hksjune=0;
                    $hkssecondquarter=0;
                    $hksjuly=0;
                    $hksaugust=0;
                    $hksseptember=0;
                    $hksthreequarter=0;
                    $hksoctober=0;
                    $hksnovember=0;
                    $hksdecember=0;
                    $hksfourthquarter=0;
                    $hksallyear=0;

                    foreach($value2 as $key3=>$value3){

                        /*if("name"==$key3){
                            continue;
                        }*/
                        if(!is_numeric($key3)){
                            continue;
                        }

                        if($i==0){
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('A'.$current.':A'.($current+$depnum[$key1]));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('A'.$current, $value1['name']);
                            $phpexecl->getActiveSheet()->getStyle('A'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        }
                        if($j==0){
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('B'.$current.':B'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('B'.$current, $value2['name']);
                            $phpexecl->getActiveSheet()->getStyle('B'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        }
                        
                        $phpexecl->setActiveSheetIndex(0)
                                    ->setCellValue('C'.$current, $value3['username'])
                                    ->setCellValue('D'.$current, (empty($value3['addc1'])?'':$value3['addc1']))
                                    ->setCellValue('E'.$current, (empty($value3['addc2'])?'':$value3['addc2']))
                                    ->setCellValue('F'.$current, (empty($value3['addc3'])?'':$value3['addc3']))
                                    ->setCellValue('G'.$current, (empty($value3['addc4'])?'':$value3['addc4']))
                                    ->setCellValue('H'.$current, (empty($value3['addc5'])?'':$value3['addc5']))
                                    ->setCellValue('I'.$current, (empty($value3['addc6'])?'':$value3['addc6']))
                                    ->setCellValue('J'.$current, (empty($value3['addc7'])?'':$value3['addc7']))
                                    ->setCellValue('K'.$current, (empty($value3['addc8'])?'':$value3['addc8']))
                                    ->setCellValue('L'.$current, (empty($value3['addc9'])?'':$value3['addc9']))
                                    ->setCellValue('M'.$current, (empty($value3['addc10'])?'':$value3['addc10']))
                                    ->setCellValue('N'.$current, (empty($value3['addc11'])?'':$value3['addc11']))
                                    ->setCellValue('O'.$current, (empty($value3['addc12'])?'':$value3['addc12']))
                                    ->setCellValue('P'.$current, (empty($value3['forthc1'])?'':$value3['forthc1']))
                                    ->setCellValue('Q'.$current, (empty($value3['forthc2'])?'':$value3['forthc2']))
                                    ->setCellValue('R'.$current, (empty($value3['forthc3'])?'':$value3['forthc3']))
                                    ->setCellValue('S'.$current, (empty($value3['forthc4'])?'':$value3['forthc4']))
                                    ->setCellValue('T'.$current, (empty($value3['forthc5'])?'':$value3['forthc5']))
                                    ->setCellValue('U'.$current, (empty($value3['forthc6'])?'':$value3['forthc6']))
                                    ->setCellValue('V'.$current, (empty($value3['forthc7'])?'':$value3['forthc7']))
                                    ->setCellValue('W'.$current, (empty($value3['forthc8'])?'':$value3['forthc8']))
                                    ->setCellValue('X'.$current, (empty($value3['forthc9'])?'':$value3['forthc9']))
                                    ->setCellValue('Y'.$current, (empty($value3['forthc10'])?'':$value3['forthc10']))
                                    ->setCellValue('Z'.$current, (empty($value3['forthc11'])?'':$value3['forthc11']))
                                    ->setCellValue('AA'.$current, (empty($value3['forthc12'])?'':$value3['forthc12']))

                                    ->setCellValue('AB'.$current, (empty($value3['dealc1'])?'':$value3['dealc1']))
                                    ->setCellValue('AC'.$current, (empty($value3['dealc2'])?'':$value3['dealc2']))
                                    ->setCellValue('AD'.$current, (empty($value3['dealc3'])?'':$value3['dealc3']))
                                    ->setCellValue('AE'.$current, (empty($value3['dealc4'])?'':$value3['dealc4']))
                                    ->setCellValue('AF'.$current, (empty($value3['dealc5'])?'':$value3['dealc5']))
                                    ->setCellValue('AG'.$current, (empty($value3['dealc6'])?'':$value3['dealc6']))
                                    ->setCellValue('AH'.$current, (empty($value3['dealc7'])?'':$value3['dealc7']))
                                    ->setCellValue('AI'.$current, (empty($value3['dealc8'])?'':$value3['dealc8']))
                                    ->setCellValue('AJ'.$current, (empty($value3['dealc9'])?'':$value3['dealc9']))
                                    ->setCellValue('AK'.$current, (empty($value3['dealc10'])?'':$value3['dealc10']))
                                    ->setCellValue('AL'.$current, (empty($value3['dealc11'])?'':$value3['dealc11']))
                                    ->setCellValue('AM'.$current, (empty($value3['dealc12'])?'':$value3['dealc12']))

                                    ->setCellValue('AN'.$current, ((empty($value3['hkc1']) || $value3['hkc1']==0.00)?'':$value3['hkc1']))
                                    ->setCellValue('AO'.$current, ((empty($value3['hkc2']) || $value3['hkc2']==0.00)?'':$value3['hkc2']))
                                    ->setCellValue('AP'.$current, ((empty($value3['hkc3']) || $value3['hkc3']==0.00)?'':$value3['hkc3']))
                                    ->setCellValue('AQ'.$current, ((empty($value3['hkc4']) || $value3['hkc4']==0.00)?'':$value3['hkc4']))
                                    ->setCellValue('AR'.$current, ((empty($value3['hkc5']) || $value3['hkc5']==0.00)?'':$value3['hkc5']))
                                    ->setCellValue('AS'.$current, ((empty($value3['hkc6']) || $value3['hkc6']==0.00)?'':$value3['hkc6']))
                                    ->setCellValue('AT'.$current, ((empty($value3['hkc7']) || $value3['hkc7']==0.00)?'':$value3['hkc7']))
                                    ->setCellValue('AU'.$current, ((empty($value3['hkc8']) || $value3['hkc8']==0.00)?'':$value3['hkc8']))
                                    ->setCellValue('AV'.$current, ((empty($value3['hkc9']) || $value3['hkc9']==0.00)?'':$value3['hkc9']))
                                    ->setCellValue('AW'.$current, ((empty($value3['hkc10']) || $value3['hkc10']==0.00)?'':$value3['hkc10']))
                                    ->setCellValue('AX'.$current, ((empty($value3['hkc11']) || $value3['hkc11']==0.00)?'':$value3['hkc11']))
                                    ->setCellValue('AY'.$current, ((empty($value3['hkc12']) || $value3['hkc12']==0.00)?'':$value3['hkc12']))
                                    
                                    ->setCellValue('AZ'.$current, (empty($value3['addcone'])?'':$value3['addcone']))
                                    ->setCellValue('BA'.$current, (empty($value3['addctwo'])?'':$value3['addctwo']))
                                    ->setCellValue('BB'.$current, (empty($value3['addcthree'])?'':$value3['addcthree']))
                                    ->setCellValue('BC'.$current, (empty($value3['addcforth'])?'':$value3['addcforth']))
                                    
                                    ->setCellValue('BD'.$current, (empty($value3['forthcone'])?'':$value3['forthcone']))
                                    ->setCellValue('BE'.$current, (empty($value3['forthctwo'])?'':$value3['forthctwo']))
                                    ->setCellValue('BF'.$current, (empty($value3['forthcthree'])?'':$value3['forthcthree']))
                                    ->setCellValue('BG'.$current, (empty($value3['forthcforth'])?'':$value3['forthcforth']))
                                    
                                    ->setCellValue('BH'.$current, (empty($value3['dealcone'])?'':$value3['dealcone']))
                                    ->setCellValue('BI'.$current, (empty($value3['dealctwo'])?'':$value3['dealctwo']))
                                    ->setCellValue('BJ'.$current, (empty($value3['dealcthree'])?'':$value3['dealcthree']))
                                    ->setCellValue('BK'.$current, (empty($value3['dealcforth'])?'':$value3['dealcforth']))

                                    ->setCellValue('BL'.$current, (empty($value3['hkcone'])?'':$value3['hkcone']))
                                    ->setCellValue('BM'.$current, (empty($value3['hkctwo'])?'':$value3['hkctwo']))
                                    ->setCellValue('BN'.$current, (empty($value3['hkcthree'])?'':$value3['hkcthree']))
                                    ->setCellValue('BO'.$current, (empty($value3['hkcforth'])?'':$value3['hkcforth']))
                                    ->setCellValue('BP'.$current, (empty($value3['addcyear'])?'':$value3['addcyear']))
                                    ->setCellValue('BQ'.$current, (empty($value3['forthcyear'])?'':$value3['forthcyear']))
                                    ->setCellValue('BR'.$current, (empty($value3['dealcyear'])?'':$value3['dealcyear']))
                                    ->setCellValue('BS'.$current, (empty($value3['hkcyear'])?'':$value3['hkcyear']));
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':BS'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':BS'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':BS'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                       
                        $i=1;$j=1;

                        $newcsjanuary+=$value3['addc1'];
                        $newcsfebruary+=$value3['addc2'];
                        $newcsmarch+=$value3['addc3'];
                        $newcsfirstquarter+=$value3['addcone'];
                        $newcsapril+=$value3['addc4'];
                        $newcsmay+=$value3['addc5'];
                        $newcsjune+=$value3['addc6'];
                        $newcssecondquarter+=$value3['addctwo'];
                        $newcsjuly+=$value3['addc7'];
                        $newcsaugust+=$value3['addc8'];
                        $newcsseptember+=$value3['addc9'];
                        $newcsthreequarter+=$value3['addcthree'];
                        $newcsoctober+=$value3['addc10'];
                        $newcsnovember+=$value3['addc11'];
                        $newcsdecember+=$value3['addc12'];
                        $newcsfourthquarter+=$value3['addcforth'];
                        $newcsallyear+=$value3['addcyear'];

                        $forthsjanuary+=$value3['forthc1'];
                        $forthsfebruary+=$value3['forthc2'];
                        $forthsmarch+=$value3['forthc3'];
                        $forthsfirstquarter+=$value3['forthcone'];
                        $forthsapril+=$value3['forthc4'];
                        $forthsmay+=$value3['forthc5'];
                        $forthsjune+=$value3['forthc6'];
                        $forthssecondquarter+=$value3['forthctwo'];
                        $forthsjuly+=$value3['forthc7'];
                        $forthsaugust+=$value3['forthc8'];
                        $forthsseptember+=$value3['forthc9'];
                        $forthsthreequarter+=$value3['forthcthree'];
                        $forthsoctober+=$value3['forthc10'];
                        $forthsnovember+=$value3['forthc11'];
                        $forthsdecember+=$value3['forthc12'];
                        $forthsfourthquarter+=$value3['forthcforth'];
                        $forthsallyear+=$value3['forthcyear'];

                        $dealsjanuary+=$value3['dealc1'];
                        $dealsfebruary+=$value3['dealc2'];
                        $dealsmarch+=$value3['dealc3'];
                        $dealsfirstquarter+=$value3['dealcone'];
                        $dealsapril+=$value3['dealc4'];
                        $dealsmay+=$value3['dealc5'];
                        $dealsjune+=$value3['dealc6'];
                        $dealssecondquarter+=$value3['dealctwo'];
                        $dealsjuly+=$value3['dealc7'];
                        $dealsaugust+=$value3['dealc8'];
                        $dealsseptember+=$value3['dealc9'];
                        $dealsthreequarter+=$value3['dealcthree'];
                        $dealsoctober+=$value3['dealc10'];
                        $dealsnovember+=$value3['dealc11'];
                        $dealsdecember+=$value3['dealc12'];
                        $dealsfourthquarter+=$value3['dealcforth'];
                        $dealsallyear+=$value3['dealcyear'];




                        $hksjanuary+=$value3['hkc1'];
                        $hksfebruary+=$value3['hkc2'];
                        $hksmarch+=$value3['hkc3'];
                        $hksfirstquarter+=$value3['hkcone'];
                        $hksapril+=$value3['hkc4'];
                        $hksmay+=$value3['hkc5'];
                        $hksjune+=$value3['hkc6'];
                        $hkssecondquarter+=$value3['hkctwo'];
                        $hksjuly+=$value3['hkc7'];
                        $hksaugust+=$value3['hkc8'];
                        $hksseptember+=$value3['hkc9'];
                        $hksthreequarter+=$value3['hkcthree'];
                        $hksoctober+=$value3['hkc10'];
                        $hksnovember+=$value3['hkc11'];
                        $hksdecember+=$value3['hkc12'];
                        $hksfourthquarter+=$value3['hkcforth'];
                        $hksallyear+=$value3['hkcyear'];


                        //total
                        $newcalljanuary+=$value3['addc1'];
                        $newcallfebruary+=$value3['addc2'];
                        $newcallmarch+=$value3['addc3'];
                        $newcallfirstquarter+=$value3['addcone'];
                        $newcallapril+=$value3['addc4'];
                        $newcallmay+=$value3['addc5'];
                        $newcalljune+=$value3['addc6'];
                        $newcallsecondquarter+=$value3['addctwo'];
                        $newcalljuly+=$value3['addc7'];
                        $newcallaugust+=$value3['addc8'];
                        $newcallseptember+=$value3['addc9'];
                        $newcallthreequarter+=$value3['addcthree'];
                        $newcalloctober+=$value3['addc10'];
                        $newcallnovember+=$value3['addc11'];
                        $newcalldecember+=$value3['addc12'];
                        $newcallfourthquarter+=$value3['addcforth'];
                        $newcallallyear+=$value3['addcyear'];

                        $forthalljanuary+=$value3['forthc1'];
                        $forthallfebruary+=$value3['forthc2'];
                        $forthallmarch+=$value3['forthc3'];
                        $forthallfirstquarter+=$value3['forthcone'];
                        $forthallapril+=$value3['forthc4'];
                        $forthallmay+=$value3['forthc5'];
                        $forthalljune+=$value3['forthc6'];
                        $forthallsecondquarter+=$value3['forthctwo'];
                        $forthalljuly+=$value3['forthc7'];
                        $forthallaugust+=$value3['forthc8'];
                        $forthallseptember+=$value3['forthc9'];
                        $forthallthreequarter+=$value3['forthcthree'];
                        $forthalloctober+=$value3['forthc10'];
                        $forthallnovember+=$value3['forthc11'];
                        $forthalldecember+=$value3['forthc12'];
                        $forthallfourthquarter+=$value3['forthcforth'];
                        $forthallallyear+=$value3['forthcyear'];
                        //total
                        $dealalljanuary+=$value3['dealc1'];
                        $dealallfebruary+=$value3['dealc2'];
                        $dealallmarch+=$value3['dealc3'];
                        $dealallfirstquarter+=$value3['dealcone'];
                        $dealallapril+=$value3['dealc4'];
                        $dealallmay+=$value3['dealc5'];
                        $dealalljune+=$value3['dealc6'];
                        $dealallsecondquarter+=$value3['dealctwo'];
                        $dealalljuly+=$value3['dealc7'];
                        $dealallaugust+=$value3['dealc8'];
                        $dealallseptember+=$value3['dealc9'];
                        $dealallthreequarter+=$value3['dealcthree'];
                        $dealalloctober+=$value3['dealc10'];
                        $dealallnovember+=$value3['dealc11'];
                        $dealalldecember+=$value3['dealc12'];
                        $dealallfourthquarter+=$value3['dealcforth'];
                        $dealallallyear+=$value3['dealcyear'];



                        $hkalljanuary+=$value3['hkc1'];
                        $hkallfebruary+=$value3['hkc2'];
                        $hkallmarch+=$value3['hkc3'];
                        $hkallfirstquarter+=$value3['hkcone'];
                        $hkallapril+=$value3['hkc4'];
                        $hkallmay+=$value3['hkc5'];
                        $hkalljune+=$value3['hkc6'];
                        $hkallsecondquarter+=$value3['hkctwo'];
                        $hkalljuly+=$value3['hkc7'];
                        $hkallaugust+=$value3['hkc8'];
                        $hkallseptember+=$value3['hkc9'];
                        $hkallthreequarter+=$value3['hkcthree'];
                        $hkalloctober+=$value3['hkc10'];
                        $hkallnovember+=$value3['hkc11'];
                        $hkalldecember+=$value3['hkc12'];
                        $hkallfourthquarter+=$value3['hkcforth'];
                        $hkallallyear+=$value3['hkcyear'];
                        ++$current;
                    }
                    $phpexecl->setActiveSheetIndex(0)
                                ->setCellValue('C'.$current, '部门小计')
                                ->setCellValue('D'.$current,$newcsjanuary )
                                ->setCellValue('E'.$current,$newcsfebruary )
                                ->setCellValue('F'.$current,$newcsmarch)
                                ->setCellValue('G'.$current,$newcsapril)
                                ->setCellValue('H'.$current,$newcsmay)
                                ->setCellValue('I'.$current,$newcsjune)
                                ->setCellValue('J'.$current,$newcsjuly)
                                ->setCellValue('K'.$current,$newcsaugust)
                                ->setCellValue('L'.$current,$newcsseptember)
                                ->setCellValue('M'.$current,$newcsoctober)
                                ->setCellValue('N'.$current,$newcsnovember)
                                ->setCellValue('O'.$current,$newcsdecember)
                                ->setCellValue('P'.$current,$forthsjanuary)
                                ->setCellValue('Q'.$current,$forthsfebruary)
                                ->setCellValue('R'.$current,$forthsmarch)
                                ->setCellValue('S'.$current,$forthsapril)
                                ->setCellValue('T'.$current,$forthsmay)
                                ->setCellValue('U'.$current,$forthsjune)
                                ->setCellValue('V'.$current,$forthsjuly)
                                ->setCellValue('W'.$current,$forthsaugust)
                                ->setCellValue('X'.$current,$forthsseptember)
                                ->setCellValue('Y'.$current,$forthsoctober)
                                ->setCellValue('Z'.$current,$forthsnovember)
                                ->setCellValue('AA'.$current,$forthsdecember)
                                ->setCellValue('AB'.$current,$dealsjanuary)
                                ->setCellValue('AC'.$current,$dealsfebruary)
                                ->setCellValue('AD'.$current,$dealsmarch)
                                ->setCellValue('AE'.$current,$dealsapril)
                                ->setCellValue('AF'.$current,$dealsmay)
                                ->setCellValue('AG'.$current,$dealsjune)
                                ->setCellValue('AH'.$current,$dealsjuly)
                                ->setCellValue('AI'.$current,$dealsaugust)
                                ->setCellValue('AJ'.$current,$dealsseptember)
                                ->setCellValue('AK'.$current,$dealsoctober)
                                ->setCellValue('AL'.$current,$dealsnovember)
                                ->setCellValue('AM'.$current,$dealsdecember)

                                ->setCellValue('AN'.$current,$hksjanuary)
                                ->setCellValue('AO'.$current,$hksfebruary)
                                ->setCellValue('AP'.$current,$hksmarch)
                                ->setCellValue('AQ'.$current,$hksapril)
                                ->setCellValue('AR'.$current,$hksmay)
                                ->setCellValue('AS'.$current,$hksjune)
                                ->setCellValue('AT'.$current,$hksjuly)
                                ->setCellValue('AU'.$current,$hksaugust)
                                ->setCellValue('AV'.$current,$hksseptember)
                                ->setCellValue('AW'.$current,$hksoctober)
                                ->setCellValue('AX'.$current,$hksnovember)
                                ->setCellValue('AY'.$current,$hksdecember)
                                
                                ->setCellValue('AZ'.$current,$newcsfirstquarter)
                                ->setCellValue('BA'.$current,$newcssecondquarter)
                                ->setCellValue('BB'.$current,$newcsthreequarter)
                                ->setCellValue('BC'.$current,$newcsfourthquarter)
                                
                                ->setCellValue('BD'.$current,$forthsfirstquarter)
                                ->setCellValue('BE'.$current,$forthssecondquarter)
                                ->setCellValue('BF'.$current,$forthsthreequarter)
                                ->setCellValue('BG'.$current,$forthsfourthquarter)
                                
                                ->setCellValue('BH'.$current,$dealsfirstquarter)
                                ->setCellValue('BI'.$current,$dealssecondquarter)
                                ->setCellValue('BJ'.$current,$dealsthreequarter)
                                ->setCellValue('BK'.$current,$dealsfourthquarter)

                                ->setCellValue('BL'.$current,$hksfirstquarter)
                                ->setCellValue('BM'.$current,$hkssecondquarter)
                                ->setCellValue('BN'.$current,$hksthreequarter)
                                ->setCellValue('BO'.$current,$hksfourthquarter)

                                ->setCellValue('BP'.$current,$newcsallyear)
                                ->setCellValue('BQ'.$current,$forthsallyear)
                                ->setCellValue('BR'.$current,$dealsallyear)
                                ->setCellValue('BS'.$current,$hksallyear);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':BS'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':BS'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':BS'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                       
                    ++$current;
                    
                }
                $phpexecl->setActiveSheetIndex(0)->setCellValue('B'.$current, '营总计')
                                ->setCellValue('C'.$current, '')
                                ->setCellValue('D'.$current,$newcalljanuary )
                                ->setCellValue('E'.$current,$newcallfebruary )
                                ->setCellValue('F'.$current,$newcallmarch)
                                ->setCellValue('G'.$current,$newcallapril)
                                ->setCellValue('H'.$current,$newcallmay)
                                ->setCellValue('I'.$current,$newcalljune)
                                ->setCellValue('J'.$current,$newcalljuly)
                                ->setCellValue('K'.$current,$newcallaugust)
                                ->setCellValue('L'.$current,$newcallseptember)
                                ->setCellValue('M'.$current,$newcalloctober)
                                ->setCellValue('N'.$current,$newcallnovember)
                                ->setCellValue('O'.$current,$newcalldecember)
                                ->setCellValue('P'.$current,$forthalljanuary)
                                ->setCellValue('Q'.$current,$forthallfebruary)
                                ->setCellValue('R'.$current,$forthallmarch)
                                ->setCellValue('S'.$current,$forthallapril)
                                ->setCellValue('T'.$current,$forthallmay)
                                ->setCellValue('U'.$current,$forthalljune)
                                ->setCellValue('V'.$current,$forthalljuly)
                                ->setCellValue('W'.$current,$forthallaugust)
                                ->setCellValue('X'.$current,$forthallseptember)
                                ->setCellValue('Y'.$current,$forthalloctober)
                                ->setCellValue('Z'.$current,$forthallnovember)
                                ->setCellValue('AA'.$current,$forthalldecember)
                                ->setCellValue('AB'.$current,$dealalljanuary)
                                ->setCellValue('AC'.$current,$dealallfebruary)
                                ->setCellValue('AD'.$current,$dealallmarch)
                                ->setCellValue('AE'.$current,$dealallapril)
                                ->setCellValue('AF'.$current,$dealallmay)
                                ->setCellValue('AG'.$current,$dealalljune)
                                ->setCellValue('AH'.$current,$dealalljuly)
                                ->setCellValue('AI'.$current,$dealallaugust)
                                ->setCellValue('AJ'.$current,$dealallseptember)
                                ->setCellValue('AK'.$current,$dealalloctober)
                                ->setCellValue('AL'.$current,$dealallnovember)
                                ->setCellValue('AM'.$current,$dealalldecember)

                                ->setCellValue('AN'.$current,$hkalljanuary)
                                ->setCellValue('AO'.$current,$hkallfebruary)
                                ->setCellValue('AP'.$current,$hkallmarch)
                                ->setCellValue('AQ'.$current,$hkallapril)
                                ->setCellValue('AR'.$current,$hkallmay)
                                ->setCellValue('AS'.$current,$hkalljune)
                                ->setCellValue('AT'.$current,$hkalljuly)
                                ->setCellValue('AU'.$current,$hkallaugust)
                                ->setCellValue('AV'.$current,$hkallseptember)
                                ->setCellValue('AW'.$current,$hkalloctober)
                                ->setCellValue('AX'.$current,$hkallnovember)
                                ->setCellValue('AY'.$current,$hkalldecember)
                                
                                ->setCellValue('AZ'.$current,$newcallfirstquarter)
                                ->setCellValue('BA'.$current,$newcallsecondquarter)
                                ->setCellValue('BB'.$current,$newcallthreequarter)
                                ->setCellValue('BC'.$current,$newcallfourthquarter)
                                
                                ->setCellValue('BD'.$current,$forthallfirstquarter)
                                ->setCellValue('BE'.$current,$forthallsecondquarter)
                                ->setCellValue('BF'.$current,$forthallthreequarter)
                                ->setCellValue('BG'.$current,$forthallfourthquarter)
                                
                                ->setCellValue('BG'.$current,$dealallfirstquarter)
                                ->setCellValue('BI'.$current,$dealallsecondquarter)
                                ->setCellValue('BJ'.$current,$dealallthreequarter)
                                ->setCellValue('BK'.$current,$dealallfourthquarter)

                                ->setCellValue('BL'.$current,$hkallfirstquarter)
                                ->setCellValue('BM'.$current,$hkallsecondquarter)
                                ->setCellValue('BN'.$current,$hkallthreequarter)
                                ->setCellValue('BO'.$current,$hkallfourthquarter)
                                ->setCellValue('BP'.$current,$newcallallyear)
                                ->setCellValue('BQ'.$current,$forthallallyear)
                                ->setCellValue('BR'.$current,$dealallallyear)
                                ->setCellValue('BS'.$current,$hkallallyear);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':BS'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':BS'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':BS'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                       
                    ++$current;
            }
            

        }
        
        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('客户统计');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="客户统计'.date('Y-m-dHis').'.xlsx"');
        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');


        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        $objWriter->save('php://output');
    }
    public function getentrydata(Vtiger_Request $request){
        $datatime=$request->get('datetime');
        $userid=$request->get('userid');
        $departmentid=$request->get('department');
        /* echo $datatime;
        var_dump($userid);
        print_r($departmentid);
        exit */;
        $querySql='';
        if(empty($userid)||!is_numeric($userid)){
            $departmentarr=array();
            if(!empty($departmentid)&&$departmentid!='null'){
                foreach($departmentid as $value){
                    $userid=getDepartmentUser($value);
                    $where=getAccessibleUsers('Accounts','List',true);
                    if($where!='1=1'){
                        $where=array_intersect($where,$userid);
                    }else{
                        $where=$userid;
                    }
                    if(empty($where)||count($where)==0){
                        continue;
                    }
                    $departmentarr=array_merge($departmentarr,$where);
                }
                $querySql=' AND vtiger_servicecontracts.signid IN('.implode(',',$departmentarr).')';
                $hkquerySql=' AND vtiger_achievementallot.receivedpaymentownid IN('.implode(',',$departmentarr).')';
            }else{
                $where=getAccessibleUsers('Accounts','List',false);
                if($where!='1=1'){
                    $querySql=' AND vtiger_servicecontracts.signid IN('.implode(',',$where).')';
                    $hkquerySql=' AND vtiger_achievementallot.receivedpaymentownid IN('.implode(',',$where).')';
                }
            }
        }else{

            $querySql=' AND vtiger_servicecontracts.signid='.$userid;
            $hkquerySql=' AND vtiger_achievementallot.receivedpaymentownid='.$userid;
        }

        $query="SELECT
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -1 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(vtiger_users.user_entered,7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue1,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -2 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -1 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue2,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -3 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -2 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue3,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -4 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -3 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue4,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -5 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -4 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue5,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -6 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -5 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue6,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -7 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -6 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue7,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -8 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -7 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue8,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -9 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -8 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue9,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -10 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -9 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue10,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -11 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -10 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue11,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -12 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -11 MONTH),7)=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS yue12,
                    sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -1 MONTH),7)<=LEFT(vtiger_salesorder.workflowstime,7),1,0),IF(LEFT(vtiger_users.user_entered,7)<=LEFT(vtiger_salesorder.workflowstime,7),1,0))) AS allyear,
                    0 as hk1,
                    0 as hk2,
                    0 as hk3,
                    0 as hk4,
                    0 as hk5,
                    0 as hk6,
                    0 as hk7,
                    0 as hk8,
                    0 as hk9,
                    0 as hk10,
                    0 as hk11,
                    0 as hk12,
                    0 as hkallyear,
                    vtiger_users.user_entered,
                    vtiger_servicecontracts.signid as userid,
                    vtiger_users.last_name AS username,
                    SUBSTRING_INDEX(vtiger_departments.parentdepartment,'::',-2) AS department
                FROM
                    vtiger_salesorder
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_salesorder.servicecontractsid
                LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_servicecontracts.signid
                LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id
                LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
                WHERE
                    vtiger_salesorder.modulestatus = 'c_complete'
                AND vtiger_users.user_entered IS NOT NULL
                AND left(vtiger_users.user_entered,4)=?
                {$querySql}
                GROUP BY vtiger_servicecontracts.signid
                ORDER BY department";

        $hkquery="SELECT
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -1 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(vtiger_users.user_entered,7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk1,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -2 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -1 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk2,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -3 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -2 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk3,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -4 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -3 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk4,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -5 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -4 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk5,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -6 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -5 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk6,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -7 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -6 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk7,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -8 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -7 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk8,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -9 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -8 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk9,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -10 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -9 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk10,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -11 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -10 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk11,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -12 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -11 MONTH),7)=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hk12,
                        sum(if(DATE_FORMAT(vtiger_users.user_entered,'%d')>15,IF(LEFT(SUBDATE(vtiger_users.user_entered,INTERVAL -1 MONTH),7)<=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0),IF(LEFT(vtiger_users.user_entered,7)<=LEFT(vtiger_receivedpayments.reality_date,7),IFNULL(vtiger_achievementallot.businessunit,0),0))) AS hkallyear,
                        vtiger_users.user_entered,
                        vtiger_achievementallot.receivedpaymentownid as userid,
                        vtiger_users.last_name AS username,
                        SUBSTRING_INDEX(vtiger_departments.parentdepartment,'::',-2) AS department
                FROM
                        vtiger_achievementallot

                LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_achievementallot.receivedpaymentownid
                LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id
                LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                WHERE

                 vtiger_users.user_entered IS NOT NULL
                AND left(vtiger_users.user_entered,4)=?
                {$hkquerySql}
                GROUP BY vtiger_achievementallot.receivedpaymentownid
                ORDER BY department";
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array($datatime));
        $num=$db->num_rows($result);
        $hkresult=$db->pquery($hkquery,array($datatime));
        $hknum=$db->num_rows($hkresult);
        if($num || $hknum){
            $array=array();
            $cachedepartment=getDepartment();

            for($i=0;$i<$num;$i++){
                $depart=$db->query_result($result,$i,'department');
                $depart=explode('::',$depart);
                $useid=$db->query_result($result,$i,'userid');
                if(!empty($departmentid)&&$departmentid!='null'){
                    if(in_array($depart[1],$departmentid)){
                        $array[$depart[1]][$depart[1].'D'][$useid]=$db->query_result_rowdata($result,$i);
                        $array[$depart[1]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                        $array[$depart[1]][$depart[1].'D']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]).'D';
                    }else{
                        $array[$depart[0]][$depart[1].'M'][$useid]=$db->query_result_rowdata($result,$i);
                        $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                        $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                    }

                }else{
                    $array[$depart[0]][$depart[1].'M'][$useid]=$db->query_result_rowdata($result,$i);
                    $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                    $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                }

            }
            $hk=array('yue1' =>0,'yue2' => 0,'yue3' => 0,'addc4' => 0,'yue5' => 0,
                'yue6' =>0,'yue7' =>0,'yue8' =>0,'yue9' =>0,'yue10' => 0,
                'yue11' => 0,'yue12' => 0,'allyear' => 0);
            //start处理回款
            for($i=0;$i<$hknum;$i++){
                $depart=$db->query_result($hkresult,$i,'department');
                $userid=$db->query_result($hkresult,$i,'userid');
                $depart=explode('::',$depart);
                if(!empty($departmentid)&&$departmentid!='null'){
                    if(in_array($depart[1],$departmentid)){
                        $temp=empty($array[$depart[1]][$depart[1].'D'][$userid])?$hk:$array[$depart[1]][$depart[1].'D'][$userid];
                        $array[$depart[1]][$depart[1].'D'][$userid]=array_merge($temp,$db->query_result_rowdata($hkresult,$i));
                        $array[$depart[1]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                        $array[$depart[1]][$depart[1].'D']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]).'D';
                    }else{
                        $temp=empty($array[$depart[0]][$depart[1].'M'][$userid])?$hk:$array[$depart[0]][$depart[1].'M'][$userid];
                        $array[$depart[0]][$depart[1].'M'][$userid]=array_merge($temp,$db->query_result_rowdata($hkresult,$i));
                        $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                        $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                    }
                }else{
                    $temp=empty($array[$depart[0]][$depart[1].'M'][$userid])?$hk:$array[$depart[0]][$depart[1].'M'][$userid];
                    $array[$depart[0]][$depart[1].'M'][$userid]=array_merge($temp,$db->query_result_rowdata($hkresult,$i));
                    $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                    $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                }
            }
            $depnum=array();
            foreach($array as $key=>$value){
                if($key=='name')continue;
                foreach($value as $k=>$v){
                    if($k=='name')continue;
                    $depnum[$key]+=count($v);
                    $depnum[$k]+=count($v);
                }
            }

            return array('data'=>$array,'num'=>$depnum);


        }else{

            return array();
        }
    }
    public function getentrystatistics(Vtiger_Request $request){
        $data=$this->getentrydata($request);
        $datatime=$request->get('datetime');
        if(!empty($data)){

            $array=$data['data'];
            $depnum=$data['num'];
            $text='';
            foreach($array as $key1=>$value1){
                $i=0;
                if($key1=='name'){
                    continue;
                }
                $alljanuary=0;
                $allfebruary=0;
                $allmarch=0;
                $allapril=0;
                $allmay=0;
                $alljune=0;
                $alljuly=0;
                $allaugust=0;
                $allseptember=0;
                $alloctober=0;
                $allnovember=0;
                $alldecember=0;
                $allallyears=0;
                $allallyear=0;

                $hkalljanuary=0;
                $hkallfebruary=0;
                $hkallmarch=0;
                $hkallapril=0;
                $hkallmay=0;
                $hkalljune=0;
                $hkalljuly=0;
                $hkallaugust=0;
                $hkallseptember=0;
                $hkalloctober=0;
                $hkallnovember=0;
                $hkalldecember=0;
                $hkallallyears=0;
                $hkallallyear=0;


                foreach($value1 as $key2=>$value2){
                    $j=0;
                    if($key2=='name') {
                        continue;
                    }
                    $sjanuary=0;
                    $sfebruary=0;
                    $smarch=0;
                    $sapril=0;
                    $smay=0;
                    $sjune=0;
                    $sjuly=0;
                    $saugust=0;
                    $sseptember=0;
                    $soctober=0;
                    $snovember=0;
                    $sdecember=0;
                    $sallyears=0;
                    $sallyear=0;

                    $hksjanuary=0;
                    $hksfebruary=0;
                    $hksmarch=0;
                    $hksapril=0;
                    $hksmay=0;
                    $hksjune=0;
                    $hksjuly=0;
                    $hksaugust=0;
                    $hksseptember=0;
                    $hksoctober=0;
                    $hksnovember=0;
                    $hksdecember=0;
                    $hksallyears=0;
                    $hksallyear=0;

                    foreach($value2 as $key3=>$value3){
                        if(!is_numeric($key3)){
                            continue;
                        }

                        if($i==0){
                            $center='<td rowspan="'.($depnum[$key1]+1).'" style="text-align: center;vertical-align:middle;">'.$value1['name'].'</td>';
                        }else{
                            $center='';
                        }
                        if($j==0){
                            $departname='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;">'.$value2['name'].'</td>';
                        }else{
                            $departname='';
                        }


                        $december=$value3['yue1']+$value3['yue2']+$value3['yue3']+$value3['yue4']+$value3['yue5']+$value3['yue6']+$value3['yue7']+$value3['yue8']+$value3['yue9']+$value3['yue10']+$value3['yue11']+$value3['yue12'];
                        $hkdecember=$value3['hk1']+$value3['hk2']+$value3['hk3']+$value3['hk4']+$value3['hk5']+$value3['hk6']+$value3['hk7']+$value3['hk8']+$value3['hk9']+$value3['hk10']+$value3['hk11']+$value3['hk12'];


                        $text.='<tr>
                                    '.$center.$departname.'
                                    <td style="text-align: center;vertical-align:middle;">'.$value3['username'].'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="入职日期">'.(empty($value3['user_entered'])?'&nbsp;':$value3['user_entered']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="1月">'.(empty($value3['yue1'])?'&nbsp;':$value3['yue1']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="1月">'.((empty($value3['hk1']) || $value3['hk1']==0)?'&nbsp;':$value3['hk1']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="2月">'.(empty($value3['yue2'])?'&nbsp;':$value3['yue2']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="2月">'.((empty($value3['hk2']) || $value3['hk2']==0)?'&nbsp;':$value3['hk2']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="3月">'.(empty($value3['yue3'])?'&nbsp;':$value3['yue3']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="3月">'.((empty($value3['hk3']) || $value3['hk3']==0)?'&nbsp;':$value3['hk3']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="4月">'.(empty($value3['yue4'])?'&nbsp;':$value3['yue4']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="4月">'.((empty($value3['hk4']) || $value3['hk4']==0)?'&nbsp;':$value3['hk4']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="5月">'.(empty($value3['yue5'])?'&nbsp;':$value3['yue5']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="5月">'.((empty($value3['hk5']) || $value3['hk5']==0)?'&nbsp;':$value3['hk5']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="6月">'.(empty($value3['yue6'])?'&nbsp;':$value3['yue6']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="6月">'.((empty($value3['hk6']) || $value3['hk6']==0)?'&nbsp;':$value3['hk6']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="7月">'.(empty($value3['yue7'])?'&nbsp;':$value3['yue7']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="7月">'.((empty($value3['hk7']) || $value3['hk7']==0)?'&nbsp;':$value3['hk7']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="8月">'.(empty($value3['yue8'])?'&nbsp;':$value3['yue8']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="8月">'.((empty($value3['hk8']) || $value3['hk8']==0)?'&nbsp;':$value3['hk8']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="9月">'.(empty($value3['yue9'])?'&nbsp;':$value3['yue9']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="9月">'.((empty($value3['hk9']) || $value3['hk9']==0)?'&nbsp;':$value3['hk9']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="10月">'.(empty($value3['yue10'])?'&nbsp;':$value3['yue10']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="10月">'.((empty($value3['hk10']) || $value3['hk10']==0)?'&nbsp;':$value3['hk10']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="11月">'.(empty($value3['yue11'])?'&nbsp;':$value3['yue11']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="11月">'.((empty($value3['hk11']) || $value3['hk11']==0)?'&nbsp;':$value3['hk11']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="12月">'.(empty($value3['yue12'])?'&nbsp;':$value3['yue12']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="12月">'.((empty($value3['hk12']) || $value3['hk12']==0)?'&nbsp;':$value3['hk12']).'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="入职12月"><span class="label label-inverse">'.$december.'</span></td>
                                    <td style="text-align: center;vertical-align:middle;" title="入职12月"><span class="label label-inverse">'.$hkdecember.'</span></td>
                                    <td style="text-align: center;vertical-align:middle;" title="入职至今">'.(empty($value3['allyear'])?'&nbsp;':'<span class="label label-a_exception">'.$value3['allyear'].'</span>').'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="入职至今">'.(empty($value3['hkallyear'])?'&nbsp;':'<span class="label label-a_exception">'.$value3['hkallyear'].'</span>').'</td>
                                </tr>';
                        $i=1;$j=1;


                        $alljanuary+=$value3['yue1'];
                        $allfebruary+=$value3['yue2'];
                        $allmarch+=$value3['yue3'];
                        $allapril+=$value3['yue4'];
                        $allmay+=$value3['yue5'];
                        $alljune+=$value3['yue6'];
                        $alljuly+=$value3['yue7'];
                        $allaugust+=$value3['yue8'];
                        $allseptember+=$value3['yue9'];
                        $alloctober+=$value3['yue10'];
                        $allnovember+=$value3['yue11'];
                        $alldecember+=$value3['yue12'];
                        $allallyears+=$december;
                        $allallyear+=$value3['allyear'];


                        $sjanuary+=$value3['yue1'];
                        $sfebruary+=$value3['yue2'];
                        $smarch+=$value3['yue3'];
                        $sapril+=$value3['yue4'];
                        $smay+=$value3['yue5'];
                        $sjune+=$value3['yue6'];
                        $sjuly+=$value3['yue7'];
                        $saugust+=$value3['yue8'];
                        $sseptember+=$value3['yue9'];
                        $soctober+=$value3['yue10'];
                        $snovember+=$value3['yue11'];
                        $sdecember+=$value3['yue12'];
                        $sallyears+=$december;
                        $sallyear+=$value3['allyear'];


                        $hkalljanuary+=$value3['hk1'];
                        $hkallfebruary+=$value3['hk2'];
                        $hkallmarch+=$value3['hk3'];
                        $hkallapril+=$value3['hk4'];
                        $hkallmay+=$value3['hk5'];
                        $hkalljune+=$value3['hk6'];
                        $hkalljuly+=$value3['hk7'];
                        $hkallaugust+=$value3['hk8'];
                        $hkallseptember+=$value3['hk9'];
                        $hkalloctober+=$value3['hk10'];
                        $hkallnovember+=$value3['hk11'];
                        $hkalldecember+=$value3['hk12'];
                        $hkallallyears+=$hkdecember;
                        $hkallallyear+=$value3['hkallyear'];


                        $hksjanuary+=$value3['hk1'];
                        $hksfebruary+=$value3['hk2'];
                        $hksmarch+=$value3['hk3'];
                        $hksapril+=$value3['hk4'];
                        $hksmay+=$value3['hk5'];
                        $hksjune+=$value3['hk6'];
                        $hksjuly+=$value3['hk7'];
                        $hksaugust+=$value3['hk8'];
                        $hksseptember+=$value3['hk9'];
                        $hksoctober+=$value3['hk10'];
                        $hksnovember+=$value3['hk11'];
                        $hksdecember+=$value3['hk12'];
                        $hksallyears+=$hkdecember;
                        $hksallyear+=$value3['hkallyear'];

                    }
                    $text.='<tr>

                        <td style="text-align: center;vertical-align:middle;"><span class="label label-a_normal"> 部门小计</span></td>
                         <td>&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="入职1月"><span class="label label-a_normal">'.$sjanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职1月"><span class="label label-a_normal">'.$hksjanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职2月"><span class="label label-a_normal">'.$sfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职2月"><span class="label label-a_normal">'.$hksfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职3月"><span class="label label-a_normal">'.$smarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职3月"><span class="label label-a_normal">'.$hksmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职4月"><span class="label label-a_normal">'.$sapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职4月"><span class="label label-a_normal">'.$hksapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职5月"><span class="label label-a_normal">'.$smay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职5月"><span class="label label-a_normal">'.$hksmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职6月"><span class="label label-a_normal">'.$sjune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职6月"><span class="label label-a_normal">'.$hksjune.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="入职7月"><span class="label label-a_normal">'.$sjuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职7月"><span class="label label-a_normal">'.$hksjuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职8月"><span class="label label-a_normal">'.$saugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职8月"><span class="label label-a_normal">'.$hksaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职9月"><span class="label label-a_normal">'.$sseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职9月"><span class="label label-a_normal">'.$hksseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职10月"><span class="label label-a_normal">'.$soctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职10月"><span class="label label-a_normal">'.$hksoctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职11月"><span class="label label-a_normal">'.$snovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职11月"><span class="label label-a_normal">'.$hksnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职12月"><span class="label label-a_normal">'.$sdecember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职12月"><span class="label label-a_normal">'.$hksdecember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职12月合计"><span class="label label-a_normal">'.$sallyears.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职12月合计"><span class="label label-a_normal">'.$hksallyears.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职至今"><span class="label label-inverse">'.$sallyear.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职至今"><span class="label label-inverse">'.$hksallyear.'</span></td>
                    </tr>';
                }
                $text.='<tr>
                        <td style="text-align: center;vertical-align:middle;"><span class="label label-success">营总计</span></td>
                        <td>&nbsp;</td>
                         <td>&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="入职1月"><span class="label label-success">'.$alljanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职1月"><span class="label label-success">'.$hkalljanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职2月"><span class="label label-success">'.$allfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职2月"><span class="label label-success">'.$hkallfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职3月"><span class="label label-success">'.$allmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职3月"><span class="label label-success">'.$hkallmarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职4月"><span class="label label-success">'.$allapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职4月"><span class="label label-success">'.$hkallapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职5月"><span class="label label-success">'.$allmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职5月"><span class="label label-success">'.$hkallmay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职6月"><span class="label label-success">'.$alljune.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职6月"><span class="label label-success">'.$hkalljune.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="入职7月"><span class="label label-success">'.$alljuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职7月"><span class="label label-success">'.$hkalljuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职8月"><span class="label label-success">'.$allaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职8月"><span class="label label-success">'.$hkallaugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职9月"><span class="label label-success">'.$allseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职9月"><span class="label label-success">'.$hkallseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职10月"><span class="label label-success">'.$alloctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职10月"><span class="label label-success">'.$hkalloctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职11月"><span class="label label-success">'.$allnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职11月"><span class="label label-success">'.$hkallnovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职12月"><span class="label label-success">'.$alldecember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职12月"><span class="label label-success">'.$hkalldecember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职12月合计"><span class="label label-success">'.$allallyears.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职12月合计"><span class="label label-success">'.$hkallallyears.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职至今"><span class="label label-success">'.$allallyear.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="入职至今"><span class="label label-success">'.$hkallallyear.'</span></td>
                    </tr>';

            }
            $table='
                <div id="fixscrollrf" class="hide" style="overflow:hidden;z-index:1033;">
                <table class="table table-bordered" id="flalted" style="position:relative;overflow-y: auto">
                    <thead>
                    <tr id="flalte1"  style="background-color:#ffffff;">
                        <th style="text-align: center;vertical-align:middle;">中心</th>
                        <th style="text-align: center;vertical-align:middle;">部门</th>
                        <th style="text-align: center;vertical-align:middle;">姓名</th>
                        <th style="text-align: center;vertical-align:middle;">入职日期</th>
                        <th style="text-align: center;vertical-align:middle;">入职一月</th>
                        <th style="text-align: center;vertical-align:middle;">入职一月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职二月</th>
                        <th style="text-align: center;vertical-align:middle;">入职二月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职三月</th>
                        <th style="text-align: center;vertical-align:middle;">入职三月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职四月</th>
                        <th style="text-align: center;vertical-align:middle;">入职四月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职五月</th>
                        <th style="text-align: center;vertical-align:middle;">入职五月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职六月</th>
                        <th style="text-align: center;vertical-align:middle;">入职六月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职七月</th>
                        <th style="text-align: center;vertical-align:middle;">入职七月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职八月</th>
                        <th style="text-align: center;vertical-align:middle;">入职八月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职九月</th>
                        <th style="text-align: center;vertical-align:middle;">入职九月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职十月</th>
                        <th style="text-align: center;vertical-align:middle;">入职十月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职十一月</th>
                        <th style="text-align: center;vertical-align:middle;">入职十一月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职十二月</th>
                        <th style="text-align: center;vertical-align:middle;">入职十二月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职十二月合计</th>
                        <th style="text-align: center;vertical-align:middle;">入职十二月合计到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职至今</th>
                        <th style="text-align: center;vertical-align:middle;">入职至今到账金额</th>
                    </tr>
                    </thead>

                </table></div>';
            $table.='
                <div id="scrollrf" style="overflow: auto;">
                <table class="table table-bordered table-striped" id="one1">
                    <thead>
                    <tr id="flaltt1">
                        <th style="text-align: center;vertical-align:middle;">中心</th>
                        <th style="text-align: center;vertical-align:middle;">部门</th>
                        <th style="text-align: center;vertical-align:middle;">姓名</th>
                        <th style="text-align: center;vertical-align:middle;">入职日期</th>
                        <th style="text-align: center;vertical-align:middle;">入职一月</th>
                        <th style="text-align: center;vertical-align:middle;">入职一月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职二月</th>
                        <th style="text-align: center;vertical-align:middle;">入职二月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职三月</th>
                        <th style="text-align: center;vertical-align:middle;">入职三月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职四月</th>
                        <th style="text-align: center;vertical-align:middle;">入职四月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职五月</th>
                        <th style="text-align: center;vertical-align:middle;">入职五月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职六月</th>
                        <th style="text-align: center;vertical-align:middle;">入职六月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职七月</th>
                        <th style="text-align: center;vertical-align:middle;">入职七月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职八月</th>
                        <th style="text-align: center;vertical-align:middle;">入职八月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职九月</th>
                        <th style="text-align: center;vertical-align:middle;">入职九月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职十月</th>
                        <th style="text-align: center;vertical-align:middle;">入职十月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职十一月</th>
                        <th style="text-align: center;vertical-align:middle;">入职十一月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职十二月</th>
                        <th style="text-align: center;vertical-align:middle;">入职十二月到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职十二月合计</th>
                        <th style="text-align: center;vertical-align:middle;">入职十二月合计到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">入职至今</th>
                        <th style="text-align: center;vertical-align:middle;">入职至今到账金额</th>
                    </tr>
                    </thead>
                    <tbody>
                    '.$text.'
                    </tbody>
                </table></div>';
            echo $table;
            exit;


        }else{

            echo '<table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="text-align: center;vertical-align:middle;">没有记录</th>
                    </tr></thead></table>';
            exit;
        }
    }
    //入职签单导出
    public function getentrystatisticsexp(Vtiger_Request $request){
        $datatime=$request->get('datetime');
        $data=$this->getentrydata($request);
        global $root_directory;
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';

        $phpexecl=new PHPExcel();

        // Set document properties
        $phpexecl->getProperties()->setCreator("liu ganglin")
            ->setLastModifiedBy("liu ganglin")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("servicecontracts");


        // 添加头信处
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '中心')
            ->setCellValue('B1', '部门')
            ->setCellValue('C1', '姓名')
            ->setCellValue('D1', '入职日期')
            ->setCellValue('E1', '入职一月')
            ->setCellValue('F1', '入职一月到账金额')
            ->setCellValue('G1', '入职二月')
            ->setCellValue('H1', '入职二月到账金额')
            ->setCellValue('I1', '入职三月')
            ->setCellValue('J1', '入职三月到账金额')
            ->setCellValue('K1', '入职四月')
            ->setCellValue('L1', '入职四月到账金额')
            ->setCellValue('M1', '入职五月')
            ->setCellValue('N1', '入职五月到账金额')
            ->setCellValue('O1', '六月')
            ->setCellValue('P1', '六月到账金额')
            ->setCellValue('Q1', '七月')
            ->setCellValue('R1', '七月到账金额')
            ->setCellValue('S1', '八月')
            ->setCellValue('T1', '八月到账金额')
            ->setCellValue('U1', '九月')
            ->setCellValue('V1', '九月到账金额')
            ->setCellValue('W1', '十月')
            ->setCellValue('X1', '十月到账金额')
            ->setCellValue('Y1', '十一月')
            ->setCellValue('Z1', '十一月到账金额')
            ->setCellValue('AA1', '十二月')
            ->setCellValue('AB1', '十二月到账金额')
            ->setCellValue('AC1', '入职十二月合计')
            ->setCellValue('AD1', '入职十二月合计到账金额')
            ->setCellValue('AE1', '入职至今')
            ->setCellValue('AF1', '入职至今到账金额');

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:AF1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:AF1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $current=2;
        if(!empty($data)){
            $array=$data['data'];
            $depnum=$data['num'];
            foreach($array as $key1=>$value1){
                $i=0;
                if($key1=='name'){
                    continue;
                }
                $alljanuary=0;
                $allfebruary=0;
                $allmarch=0;
                $allapril=0;
                $allmay=0;
                $alljune=0;
                $alljuly=0;
                $allaugust=0;
                $allseptember=0;
                $alloctober=0;
                $allnovember=0;
                $alldecember=0;
                $allallyears=0;
                $allallyear=0;

                $hkalljanuary=0;
                $hkallfebruary=0;
                $hkallmarch=0;
                $hkallapril=0;
                $hkallmay=0;
                $hkalljune=0;
                $hkalljuly=0;
                $hkallaugust=0;
                $hkallseptember=0;
                $hkalloctober=0;
                $hkallnovember=0;
                $hkalldecember=0;
                $hkallallyears=0;
                $hkallallyear=0;

                foreach($value1 as $key2=>$value2){
                    $j=0;
                    if($key2=='name') {
                        continue;
                    }
                    $sjanuary=0;
                    $sfebruary=0;
                    $smarch=0;
                    $sapril=0;
                    $smay=0;
                    $sjune=0;
                    $sjuly=0;
                    $saugust=0;
                    $sseptember=0;
                    $soctober=0;
                    $snovember=0;
                    $sdecember=0;
                    $sallyears=0;
                    $sallyear=0;

                    $hksjanuary=0;
                    $hksfebruary=0;
                    $hksmarch=0;
                    $hksapril=0;
                    $hksmay=0;
                    $hksjune=0;
                    $hksjuly=0;
                    $hksaugust=0;
                    $hksseptember=0;
                    $hksoctober=0;
                    $hksnovember=0;
                    $hksdecember=0;
                    $hksallyears=0;
                    $hksallyear=0;

                    foreach($value2 as $key3=>$value3){
                        if(!is_numeric($key3)){
                            continue;
                        }

                        if($i==0){
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('A'.$current.':A'.($current+$depnum[$key1]));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('A'.$current, $value1['name']);
                            $phpexecl->getActiveSheet()->getStyle('A'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        }
                        if($j==0){
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('B'.$current.':B'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('B'.$current, $value2['name']);
                            $phpexecl->getActiveSheet()->getStyle('B'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        }

                        $december=$value3['yue1']+$value3['yue2']+$value3['yue3']+$value3['yue4']+$value3['yue5']+$value3['yue6']+$value3['yue7']+$value3['yue8']+$value3['yue9']+$value3['yue10']+$value3['yue11']+$value3['yue12'];
                        $hkdecember=$value3['hk1']+$value3['hk2']+$value3['hk3']+$value3['hk4']+$value3['hk5']+$value3['hk6']+$value3['hk7']+$value3['hk8']+$value3['hk9']+$value3['hk10']+$value3['hk11']+$value3['hk12'];

                        $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, $value3['username'])
                                    ->setCellValue('D'.$current, (empty($value3['user_entered'])?'':$value3['user_entered']))
                                    ->setCellValue('E'.$current, (empty($value3['yue1'])?'':$value3['yue1']))
                                    ->setCellValue('F'.$current, ((empty($value3['hk1']) || $value3['hk1']==0)?'':$value3['hk1']))
                                    ->setCellValue('G'.$current, (empty($value3['yue2'])?'':$value3['yue2']))
                                    ->setCellValue('H'.$current, ((empty($value3['hk2']) || $value3['hk2']==0)?'':$value3['hk2']))
                                    ->setCellValue('I'.$current, (empty($value3['yue3'])?'':$value3['yue3']))
                                    ->setCellValue('J'.$current, ((empty($value3['hk3']) || $value3['hk3']==0)?'':$value3['hk3']))
                                    ->setCellValue('K'.$current, (empty($value3['yue4'])?'':$value3['yue4']))
                                    ->setCellValue('L'.$current, ((empty($value3['hk4']) || $value3['hk4']==0)?'':$value3['hk4']))
                                    ->setCellValue('M'.$current, (empty($value3['yue5'])?'':$value3['yue5']))
                                    ->setCellValue('N'.$current, ((empty($value3['hk5']) || $value3['hk5']==0)?'':$value3['hk5']))
                                    ->setCellValue('O'.$current, (empty($value3['yue6'])?'':$value3['yue6']))
                                    ->setCellValue('P'.$current, ((empty($value3['hk6']) || $value3['hk6']==0)?'':$value3['hk6']))
                                    ->setCellValue('Q'.$current, (empty($value3['yue7'])?'':$value3['yue7']))
                                    ->setCellValue('R'.$current, ((empty($value3['hk7']) || $value3['hk7']==0)?'':$value3['hk7']))
                                    ->setCellValue('S'.$current, (empty($value3['yue8'])?'':$value3['yue8']))
                                    ->setCellValue('T'.$current, ((empty($value3['hk8']) || $value3['hk8']==0)?'':$value3['hk8']))
                                    ->setCellValue('U'.$current, (empty($value3['yue9'])?'':$value3['yue9']))
                                    ->setCellValue('V'.$current, ((empty($value3['hk9']) || $value3['hk9']==0)?'':$value3['hk9']))
                                    ->setCellValue('W'.$current, (empty($value3['yue10'])?'':$value3['yue10']))
                                    ->setCellValue('X'.$current, ((empty($value3['hk10']) || $value3['hk10']==0)?'':$value3['hk10']))
                                    ->setCellValue('Y'.$current, (empty($value3['yue11'])?'':$value3['yue11']))
                                    ->setCellValue('Z'.$current, ((empty($value3['hk11']) || $value3['hk11']==0)?'':$value3['hk11']))
                                    ->setCellValue('AA'.$current, (empty($value3['yue12'])?'':$value3['yue12']))
                                    ->setCellValue('AB'.$current, ((empty($value3['hk12']) || $value3['hk12']==0)?'':$value3['hk12']))
                                    ->setCellValue('AC'.$current, $december)
                                    ->setCellValue('AD'.$current, $hkdecember)
                                    ->setCellValue('AE'.$current, (empty($value3['allyear'])?'':$value3['allyear']))
                                    ->setCellValue('AF'.$current, (empty($value3['hkallyear'])?'':$value3['hkallyear']));



                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':AF'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':AF'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':AF'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $i=1;$j=1;

                        $alljanuary+=$value3['yue1'];
                        $allfebruary+=$value3['yue2'];
                        $allmarch+=$value3['yue3'];
                        $allapril+=$value3['yue4'];
                        $allmay+=$value3['yue5'];
                        $alljune+=$value3['yue6'];
                        $alljuly+=$value3['yue7'];
                        $allaugust+=$value3['yue8'];
                        $allseptember+=$value3['yue9'];
                        $alloctober+=$value3['yue10'];
                        $allnovember+=$value3['yue11'];
                        $alldecember+=$value3['yue12'];
                        $allallyears+=$december;
                        $allallyear+=$value3['allyear'];


                        $sjanuary+=$value3['yue1'];
                        $sfebruary+=$value3['yue2'];
                        $smarch+=$value3['yue3'];
                        $sapril+=$value3['yue4'];
                        $smay+=$value3['yue5'];
                        $sjune+=$value3['yue6'];
                        $sjuly+=$value3['yue7'];
                        $saugust+=$value3['yue8'];
                        $sseptember+=$value3['yue9'];
                        $soctober+=$value3['yue10'];
                        $snovember+=$value3['yue11'];
                        $sdecember+=$value3['yue12'];
                        $sallyears+=$december;
                        $sallyear+=$value3['allyear'];



                        $hkalljanuary+=$value3['hk1'];
                        $hkallfebruary+=$value3['hk2'];
                        $hkallmarch+=$value3['hk3'];
                        $hkallapril+=$value3['hk4'];
                        $hkallmay+=$value3['hk5'];
                        $hkalljune+=$value3['hk6'];
                        $hkalljuly+=$value3['hk7'];
                        $hkallaugust+=$value3['hk8'];
                        $hkallseptember+=$value3['hk9'];
                        $hkalloctober+=$value3['hk10'];
                        $hkallnovember+=$value3['hk11'];
                        $hkalldecember+=$value3['hk12'];
                        $hkallallyears+=$hkdecember;
                        $hkallallyear+=$value3['hkallyear'];


                        $hksjanuary+=$value3['hk1'];
                        $hksfebruary+=$value3['hk2'];
                        $hksmarch+=$value3['hk3'];
                        $hksapril+=$value3['hk4'];
                        $hksmay+=$value3['hk5'];
                        $hksjune+=$value3['hk6'];
                        $hksjuly+=$value3['hk7'];
                        $hksaugust+=$value3['hk8'];
                        $hksseptember+=$value3['hk9'];
                        $hksoctober+=$value3['hk10'];
                        $hksnovember+=$value3['hk11'];
                        $hksdecember+=$value3['hk12'];
                        $hksallyears+=$hkdecember;
                        $hksallyear+=$value3['hkallyear'];

                        ++$current;
                    }
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, '部门小计');
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('D'.$current, "");
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('E'.$current, $sjanuary);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('F'.$current, $hksjanuary);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('G'.$current, $sfebruary);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('H'.$current, $hksfebruary);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('I'.$current, $smarch);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('J'.$current, $hksmarch);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('K'.$current, $sapril);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('L'.$current, $hksapril);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('M'.$current, $smay);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('N'.$current, $hksmay);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('O'.$current, $sjune);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('P'.$current, $hksjune);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('Q'.$current, $sjuly);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('R'.$current, $hksjuly);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('S'.$current, $saugust);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('T'.$current, $hksaugust);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('U'.$current, $sseptember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('V'.$current, $hksseptember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('W'.$current, $soctober);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('X'.$current, $hksoctober);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('Y'.$current, $snovember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('Z'.$current, $hksnovember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AA'.$current, $sdecember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AB'.$current, $hksdecember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AC'.$current, $sallyears);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AD'.$current, $hksallyears);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AE'.$current, $sallyear);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AF'.$current, $hksallyear);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':AF'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':AF'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':AF'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    ++$current;
                }
                $phpexecl->setActiveSheetIndex(0)->setCellValue('B'.$current, '营总计');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, '');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('D'.$current, '');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('E'.$current, $alljanuary);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('F'.$current, $hkalljanuary);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('G'.$current, $allfebruary);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('H'.$current, $hkallfebruary);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('I'.$current, $allmarch);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('J'.$current, $hkallmarch);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('K'.$current, $allapril);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('L'.$current, $hkallapril);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('M'.$current, $allmay);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('N'.$current, $hkallmay);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('O'.$current, $alljune);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('P'.$current, $hkalljune);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('Q'.$current, $alljuly);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('R'.$current, $hkalljuly);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('S'.$current, $allaugust);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('T'.$current, $hkallaugust);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('U'.$current, $allseptember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('V'.$current, $hkallseptember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('W'.$current, $alloctober);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('X'.$current, $hkalloctober);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('Y'.$current, $allnovember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('Z'.$current, $hkallnovember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AA'.$current, $alldecember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AB'.$current, $hkalldecember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AC'.$current, $allallyears);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AD'.$current, $hkallallyears);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AE'.$current, $allallyear);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AF'.$current, $hkallallyear);
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':AF'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':AF'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':AF'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                ++$current;


            }

        }


        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('入职签单量统计');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="入职签单量统计'.date('Y-m-dHis').'.xlsx"');
        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');


        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function getperformance(Vtiger_Request $request){
        $data=$this->getPerformanceData($request);
        $datatime=$request->get('datetime');
        if(!empty($data)){

            $array=$data['data'];
            $depnum=$data['num'];
            $salesorder=$data['salesorder'];
            $enty=$data['enty'];
            $text='';
            foreach($array as $key1=>$value1){
                $i=0;
                if($key1=='name'){
                    continue;
                }
                $alljanuary=0;
                $allfebruary=0;
                $allmarch=0;
                $allfirstquarter=0;
                $allapril=0;
                $allmay=0;
                $alljune=0;
                $allsecondquarter=0;
                $alljuly=0;
                $allaugust=0;
                $allseptember=0;
                $allthreequarter=0;
                $alloctober=0;
                $allnovember=0;
                $alldecember=0;
                $allfourthquarter=0;
                $allallyear=0;


                foreach($value1 as $key2=>$value2){
                    $j=0;
                    if($key2=='name') {
                        continue;
                    }
                    $sjanuary=0;
                    $sfebruary=0;
                    $smarch=0;
                    $sfirstquarter=0;
                    $sapril=0;
                    $smay=0;
                    $sjune=0;
                    $ssecondquarter=0;
                    $sjuly=0;
                    $saugust=0;
                    $sseptember=0;
                    $sthreequarter=0;
                    $soctober=0;
                    $snovember=0;
                    $sdecember=0;
                    $sfourthquarter=0;
                    $sallyear=0;

                    foreach($value2 as $key3=>$value3){

                        /*if("name"==$key3){
                            continue;
                        }*/
                        if(!is_numeric($key3)){
                            continue;
                        }

                        if($i==0){
                            $center='<td rowspan="'.($depnum[$key1]+1).'" style="text-align: center;vertical-align:middle;">'.$value1['name'].'</td>';
                        }else{
                            $center='';
                        }
                        if($j==0){
                            $departname='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;">'.$value2['name'].'</td>';

                            $departsale1='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="1月部门人数">'.(empty($enty[$value3['departmentid']]['counts01'])?0:$enty[$value3['departmentid']]['counts01']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="1月部门签单数">'.(empty($salesorder[$value3['departmentid']]['counts01'])?0:$salesorder[$value3['departmentid']]['counts01']).'</td>';
                            $departsale2='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="2月部门人数">'.(empty($enty[$value3['departmentid']]['counts02'])?0:$enty[$value3['departmentid']]['counts02']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="2月部门签单数">'.(empty($salesorder[$value3['departmentid']]['counts02'])?0:$salesorder[$value3['departmentid']]['counts02']).'</td>';
                            $departsale3='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="3月部门人数">'.(empty($enty[$value3['departmentid']]['counts03'])?0:$enty[$value3['departmentid']]['counts03']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="3月部门签单数">'.(empty($salesorder[$value3['departmentid']]['counts03'])?0:$salesorder[$value3['departmentid']]['counts03']).'</td>';
                            $departsale4='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="4月部门人数">'.(empty($enty[$value3['departmentid']]['counts03'])?0:$enty[$value3['departmentid']]['counts03']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="4月部门签单数">'.(empty($salesorder[$value3['departmentid']]['counts03'])?0:$salesorder[$value3['departmentid']]['counts03']).'</td>';
                            $departsale5='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="5月部门人数">'.(empty($enty[$value3['departmentid']]['counts03'])?0:$enty[$value3['departmentid']]['counts03']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="5月部门签单数">'.(empty($salesorder[$value3['departmentid']]['counts03'])?0:$salesorder[$value3['departmentid']]['counts03']).'</td>';
                            $departsale6='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="6月部门人数">'.(empty($enty[$value3['departmentid']]['counts06'])?0:$enty[$value3['departmentid']]['counts06']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="6月部门签单数">'.(empty($salesorder[$value3['departmentid']]['counts06'])?0:$salesorder[$value3['departmentid']]['counts06']).'</td>';
                            $departsale7='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="7月部门人数">'.(empty($enty[$value3['departmentid']]['counts07'])?0:$enty[$value3['departmentid']]['counts07']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="7月部门签单数">'.(empty($salesorder[$value3['departmentid']]['counts07'])?0:$salesorder[$value3['departmentid']]['counts07']).'</td>';
                            $departsale8='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="8月部门人数">'.(empty($enty[$value3['departmentid']]['counts08'])?0:$enty[$value3['departmentid']]['counts08']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="8月部门签单数">'.(empty($salesorder[$value3['departmentid']]['counts08'])?0:$salesorder[$value3['departmentid']]['counts08']).'</td>';
                            $departsale9='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="9月部门人数">'.(empty($enty[$value3['departmentid']]['counts09'])?0:$enty[$value3['departmentid']]['counts09']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="9月部门签单数">'.(empty($salesorder[$value3['departmentid']]['counts09'])?0:$salesorder[$value3['departmentid']]['counts09']).'</td>';
                            $departsale10='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="10月部门人数">'.(empty($enty[$value3['departmentid']]['counts10'])?0:$enty[$value3['departmentid']]['counts10']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="10月部门签单数">'.(empty($salesorder[$value3['departmentid']]['counts10'])?0:$salesorder[$value3['departmentid']]['counts10']).'</td>';
                            $departsale11='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="11月部门人数">'.(empty($enty[$value3['departmentid']]['counts11'])?0:$enty[$value3['departmentid']]['counts11']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="12月部门签单数">'.(empty($salesorder[$value3['departmentid']]['counts11'])?0:$salesorder[$value3['departmentid']]['counts11']).'</td>';
                            $departsale12='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="12月部门人数">'.(empty($enty[$value3['departmentid']]['counts12'])?0:$enty[$value3['departmentid']]['counts12']).'</td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="">'.(empty($salesorder[$value3['departmentid']]['counts12'])?0:$salesorder[$value3['departmentid']]['counts12']).'</td>';
                            $quarter1='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title=""></td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="">'.($salesorder[$value3['departmentid']]['counts01']+$salesorder[$value3['departmentid']]['counts02']+$salesorder[$value3['departmentid']]['counts03']).'</td>';
                            $quarter2='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title=""></td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="">'.($salesorder[$value3['departmentid']]['counts04']+$salesorder[$value3['departmentid']]['counts05']+$salesorder[$value3['departmentid']]['counts06']).'</td>';
                            $quarter3='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title=""></td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="">'.($salesorder[$value3['departmentid']]['counts07']+$salesorder[$value3['departmentid']]['counts08']+$salesorder[$value3['departmentid']]['counts09']).'</td>';
                            $quarter4='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title=""></td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="">'.($salesorder[$value3['departmentid']]['counts10']+$salesorder[$value3['departmentid']]['counts11']+$salesorder[$value3['departmentid']]['counts12']).'</td>';
                            $allquarter='<td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title=""></td>
                            <td rowspan="'.($depnum[$key2]).'" style="text-align: center;vertical-align:middle;" title="">'.($salesorder[$value3['departmentid']]['countsall']).'</td>';


                        }else{
                            $departname='';
                            $departsale1='';
                            $departsale2='';
                            $departsale3='';
                            $departsale4='';
                            $departsale5='';
                            $departsale6='';
                            $departsale7='';
                            $departsale8='';
                            $departsale9='';
                            $departsale10='';
                            $departsale11='';
                            $departsale12='';
                            $quarter1='';
                            $quarter2='';
                            $quarter3='';
                            $quarter4='';
                            $allquarter='';
                        }
                        $text.='<tr>
                                    '.$center.$departname.'
                                    <td style="text-align: center;vertical-align:middle;">'.$value3['username']."【{$value3['title']}】".'</td>
                                    <td style="text-align: center;vertical-align:middle;">'.$value3['user_entered'].'</td>
                                    <td style="text-align: center;vertical-align:middle;">'.$value3['leavedate'].'</td>
                                    <td style="text-align: center;vertical-align:middle;" title="1月业绩">'.(empty($value3['hk1'])?'&nbsp;':$value3['hk1']).'</td>
                                    '.$departsale1.'
                                    <td style="text-align: center;vertical-align:middle;" title="2月业绩">'.(empty($value3['hk2'])?'&nbsp;':$value3['hk2']).'</td>
                                    '.$departsale2.'
                                    <td style="text-align: center;vertical-align:middle;" title="3月业绩">'.(empty($value3['hk3'])?'&nbsp;':$value3['hk3']).'</td>
                                    '.$departsale3.'
                                    <td style="text-align: center;vertical-align:middle;" title="第一季汇总">'.($value3['hk1']+$value3['hk2']+$value3['hk3']).'</td>
                                    '.$quarter1.'
                                    
                                    <td style="text-align: center;vertical-align:middle;" title="4月业绩">'.(empty($value3['hk4'])?'&nbsp;':$value3['hk4']).'</td>
                                    '.$departsale4.'
                                    <td style="text-align: center;vertical-align:middle;" title="5月业绩">'.(empty($value3['hk5'])?'&nbsp;':$value3['hk5']).'</td>
                                    '.$departsale5.'
                                    <td style="text-align: center;vertical-align:middle;" title="6月业绩">'.(empty($value3['hk6'])?'&nbsp;':$value3['hk6']).'</td>
                                    '.$departsale6.'
                                    <td style="text-align: center;vertical-align:middle;" title="第二季汇总">'.($value3['hk4']+$value3['hk5']+$value3['hk6']).'</td>
                                    '.$quarter2.'
                                    <td style="text-align: center;vertical-align:middle;" title="7月业绩">'.(empty($value3['hk7'])?'&nbsp;':$value3['hk7']).'</td>
                                    '.$departsale7.'
                                    <td style="text-align: center;vertical-align:middle;" title="8月业绩">'.(empty($value3['hk8'])?'&nbsp;':$value3['hk8']).'</td>
                                    '.$departsale8.'
                                    <td style="text-align: center;vertical-align:middle;" title="9月业绩">'.(empty($value3['hk9'])?'&nbsp;':$value3['hk9']).'</td>
                                    '.$departsale9.'
                                    <td style="text-align: center;vertical-align:middle;" title="第三季汇总">'.($value3['hk7']+$value3['hk8']+$value3['hk9']).'</td>
                                    '.$quarter3.'
                                    <td style="text-align: center;vertical-align:middle;" title="10月业绩">'.(empty($value3['hk10'])?'&nbsp;':$value3['hk10']).'</td>
                                    '.$departsale10.'
                                    <td style="text-align: center;vertical-align:middle;" title="11月业绩">'.(empty($value3['hk11'])?'&nbsp;':$value3['hk11']).'</td>
                                    '.$departsale11.'
                                    <td style="text-align: center;vertical-align:middle;" title="12月业绩">'.(empty($value3['hk12'])?'&nbsp;':$value3['hk12']).'</td>
                                    '.$departsale12.'
                                    <td style="text-align: center;vertical-align:middle;" title="第四季汇总">'.($value3['hk10']+$value3['hk11']+$value3['hk12']).'</td>
                                    '.$quarter4.'
                                    <td style="text-align: center;vertical-align:middle;" title="总年">'.($value3['allyear']).'</td>
                                    '.$allquarter.'
                                    
                            
                                </tr>';
                        $i=1;$j=1;
                        $alljanuary+=$value3['hk1'];
                        $allfebruary+=$value3['hk2'];
                        $allmarch+=$value3['hk3'];
                        $allfirstquarter+=$value3['hk1']+$value3['hk2']+$value3['hk3'];
                        $allapril+=$value3['hk4'];
                        $allmay+=$value3['hk5'];
                        $alljune+=$value3['hk6'];
                        $allsecondquarter+=$value3['hk4']+$value3['hk5']+$value3['hk6'];
                        $alljuly+=$value3['hk7'];
                        $allaugust+=$value3['hk8'];
                        $allseptember+=$value3['hk9'];
                        $allthreequarter+=$value3['hk7']+$value3['hk8']+$value3['hk9'];
                        $alloctober+=$value3['hk10'];
                        $allnovember+=$value3['hk11'];
                        $alldecember+=$value3['hk12'];
                        $allfourthquarter+=$value3['hk10']+$value3['hk11']+$value3['hk12'];
                        $allallyear+=$value3['allyear'];


                        $sjanuary+=$value3['hk1'];
                        $sfebruary+=$value3['hk2'];
                        $smarch+=$value3['hk3'];
                        $sfirstquarter+=$value3['hk1']+$value3['hk2']+$value3['hk3'];
                        $sapril+=$value3['hk4'];
                        $smay+=$value3['hk5'];
                        $sjune+=$value3['hk6'];
                        $ssecondquarter+=$value3['hk4']+$value3['hk5']+$value3['hk6'];
                        $sjuly+=$value3['hk7'];
                        $saugust+=$value3['hk8'];
                        $sseptember+=$value3['hk9'];
                        $sthreequarter+=$value3['hk7']+$value3['hk8']+$value3['hk9'];
                        $soctober+=$value3['hk10'];
                        $snovember+=$value3['hk11'];
                        $sdecember+=$value3['hk12'];
                        $sfourthquarter+=$value3['hk10']+$value3['hk11']+$value3['hk12'];
                        $sallyear+=$value3['allyear'];
                    }
                    $text.='<tr>

                        <td colspan="3" style="text-align: center;vertical-align:middle;"><span class="label label-a_normal"> 部门小计</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-a_normal">'.$sjanuary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-a_normal">'.$sfebruary.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-a_normal">'.$smarch.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-warning">'.$sfirstquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-a_normal">'.$sapril.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-a_normal">'.$smay.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-a_normal">'.$sjune.'</span></td>

                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-b_check">'.$ssecondquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-a_normal">'.$sjuly.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-a_normal">'.$saugust.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-a_normal">'.$sseptember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-warning">'.$sthreequarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-a_normal">'.$soctober.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-a_normal">'.$snovember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-a_normal">'.$sdecember.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-b_check">'.$sfourthquarter.'</span></td>
                        <td style="text-align: center;vertical-align:middle;" title="年度"><span class="label label-inverse">'.$sallyear.'</span></td>
                    </tr>';
                }
                $text.='<tr>
                        <td colspan="4" style="text-align: center;vertical-align:middle;"><span class="label label-success">营总计</span></td>
                        <td  style="text-align: center;vertical-align:middle;" title="1月"><span class="label label-success">'.$alljanuary.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td  style="text-align: center;vertical-align:middle;" title="2月"><span class="label label-success">'.$allfebruary.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="3月"><span class="label label-success">'.$allmarch.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td  style="text-align: center;vertical-align:middle;" title="第一季度"><span class="label label-success">'.$allfirstquarter.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="4月"><span class="label label-success">'.$allapril.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="5月"><span class="label label-success">'.$allmay.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="6月"><span class="label label-success">'.$alljune.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="第二季度"><span class="label label-success">'.$allsecondquarter.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="7月"><span class="label label-success">'.$alljuly.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="8月"><span class="label label-success">'.$allaugust.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="9月"><span class="label label-success">'.$allseptember.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="第三季度"><span class="label label-success">'.$allthreequarter.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="10月"><span class="label label-success">'.$alloctober.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="11月"><span class="label label-success">'.$allnovember.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="12月"><span class="label label-success">'.$alldecember.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="第四季度"><span class="label label-success">'.$allfourthquarter.'</span></td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align: center;vertical-align:middle;" title="年度"><span class="label label-success">'.$allallyear.'</span></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>';

            }
            $table='
                <div id="fixscrollrf" class="hide" style="overflow:hidden;z-index:1033;">
                <table class="table table-bordered" id="flalted"  style="position:relative;overflow-y: auto">
                    <thead>
                    <tr id="flalte1"  style="background-color:#ffffff;">
                        <th rowspan="3" style="text-align: center;vertical-align:middle;">中心</th>
                        <th rowspan="3" style="text-align: center;vertical-align:middle;">部门</th>
                        <th rowspan="2" colspan="3" style="text-align: center;vertical-align:middle;">姓名</th>
                        <th colspan="12" style="text-align: center;vertical-align:middle;">第一季度</th>
                        <th colspan="12" style="text-align: center;vertical-align:middle;">第二季度</th>
                        <th colspan="12" style="text-align: center;vertical-align:middle;">第三季度</th>
                        <th colspan="12" style="text-align: center;vertical-align:middle;">第四季度</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">'.$datatime.'年度</th>
                    </tr>
                    <tr id="flalte2"  style="background-color:#ffffff;">
                        <th colspan="3" style="text-align: center;vertical-align:middle;">一月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">二月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">三月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">四月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">五月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">六月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">七月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">八月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">九月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">十月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">十一月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">十二月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">汇总</th>
                    </tr>
                    <tr id="flalte3"  style="background-color:#ffffff;">
                        <th style="text-align: center;vertical-align:middle;">姓名</th>
                        <th style="text-align: center;vertical-align:middle;">入职日期</th>
                        <th style="text-align: center;vertical-align:middle;">离职时间</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                    </tr>
                    </thead>
                    
                </table></div>';
            $table.='
                <div id="scrollrf" style="overflow: auto;">
                <table class="table table-bordered table-striped" id="one1">
                    <thead>
                    <tr id="flaltt1">
                        <th rowspan="3" style="text-align: center;vertical-align:middle;">中心</th>
                        <th rowspan="3" style="text-align: center;vertical-align:middle;">部门</th>
                        <th rowspan="2" colspan="3" style="text-align: center;vertical-align:middle;">姓名</th>
                        <th colspan="12" style="text-align: center;vertical-align:middle;">第一季度</th>
                        <th colspan="12" style="text-align: center;vertical-align:middle;">第二季度</th>
                        <th colspan="12" style="text-align: center;vertical-align:middle;">第三季度</th>
                        <th colspan="12" style="text-align: center;vertical-align:middle;">第四季度</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">'.$datatime.'年度</th>
                    </tr>
                    <tr id="flaltt2">
                       
                        <th colspan="3" style="text-align: center;vertical-align:middle;">一月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">二月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">三月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">四月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">五月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">六月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">七月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">八月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">九月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">十月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">十一月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">十二月</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">汇总</th>
                        <th colspan="3" style="text-align: center;vertical-align:middle;">汇总</th>
                    </tr>
                    <tr id="flaltt3"  style="background-color:#ffffff;">
                        <th style="text-align: center;vertical-align:middle;">姓名</th>
                        <th style="text-align: center;vertical-align:middle;">入职日期</th>
                        <th style="text-align: center;vertical-align:middle;">离职时间</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        <th style="text-align: center;vertical-align:middle;">业绩</th>
                        <th style="text-align: center;vertical-align:middle;">人数</th>
                        <th style="text-align: center;vertical-align:middle;">出单人数</th>
                        
                        
                    </tr>
                    </thead>
                    <tbody>
                    '.$text.'
                    </tbody>
                </table></div>';
            //header("Content-type: application/vnd.ms-excel; charset=utf8");
            //header("Content-Disposition: attachment; filename=filename.xls");
            echo $table;
            exit;


        }else{

            echo '<table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="text-align: center;vertical-align:middle;">没有记录</th>
                    </tr></thead></table>';
            exit;
        }
    }

    /**
     * 商务业绩报表
     * @param Vtiger_Request $request
     * @return array
     */
    public function getPerformanceData(Vtiger_Request $request){
        $datatime=$request->get('datetime');
        $userid=$request->get('userid');
        $departmentid=$request->get('department');
        /* echo $datatime;
        var_dump($userid);
        print_r($departmentid);
        exit */;
        $querySql='';
        if(empty($userid)||!is_numeric($userid)){
            $departmentarr=array();
            if(!empty($departmentid)&&$departmentid!='null'){
                foreach($departmentid as $value){
                    $userid=getDepartmentUser($value);
                    $where=getAccessibleUsers('Accounts','List',true);
                    if($where!='1=1'){
                        $where=array_intersect($where,$userid);
                    }else{
                        $where=$userid;
                    }
                    if(empty($where)||count($where)==0){
                        continue;
                    }
                    $departmentarr=array_merge($departmentarr,$where);
                }
                $querySql=' AND servicestemp.signid IN('.implode(',',$departmentarr).')';
                $hkquerySql=' AND vtiger_achievementallot.receivedpaymentownid IN('.implode(',',$departmentarr).')';
            }else{
                $where=getAccessibleUsers('Accounts','List',false);
                if($where!='1=1'){
                    $querySql=' AND servicestemp.signid IN('.implode(',',$where).')';
                    $hkquerySql=' AND vtiger_achievementallot.receivedpaymentownid IN('.implode(',',$where).')';
                }
            }
        }else{

            $querySql=' AND servicestemp.signid='.$userid;
            $hkquerySql=' AND vtiger_achievementallot.receivedpaymentownid='.$userid;
        }
        global $current_user;
        /*if(in_array($current_user->id,array(38,43,1,2110))){
            $newdempartid=empty($departmentid)?array('H1'):$departmentid;
            $querySql=' AND vtiger_servicecontracts.signdempart in(\''.implode("','",$newdempartid).'\')';
            $hkquerySql=' AND vtiger_achievementallot.departmentid in(\''.implode("','",$newdempartid).'\')';
        }*/

        $query="SELECT
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='01',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk1,
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='02',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk2,
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='03',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk3,
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='04',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk4,
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='05',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk5,
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='06',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk6,
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='07',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk7,
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='08',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk8,
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='09',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk9,
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='10',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk10,
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='11',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk11,
                    sum(if(SUBSTR(vtiger_achievementallot.matchdate,6,2)='12',IFNULL(vtiger_achievementallot.businessunit,0),0)) AS hk12,
                    sum(IFNULL(vtiger_achievementallot.businessunit,0)) AS allyear,
                    vtiger_achievementallot.receivedpaymentownid as userid,
                    vtiger_achievementallot.departmentid,
                    left(vtiger_achievementallot.matchdate,7) AS mdate,
                    vtiger_users.last_name AS username,
                    vtiger_users.title,
                    vtiger_users.user_entered,
                    vtiger_users.leavedate,
                    SUBSTRING_INDEX(vtiger_departments.parentdepartment,'::',-2) AS department
                FROM
                    vtiger_achievementallot
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                LEFT JOIN vtiger_departments ON vtiger_achievementallot.departmentid = vtiger_departments.departmentid
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                WHERE
                        left(vtiger_achievementallot.matchdate,4)=?
                        AND vtiger_achievementallot.departmentid IS NOT NULL 
                        AND vtiger_achievementallot.departmentid !=''
                        {$hkquerySql}
                GROUP BY vtiger_achievementallot.receivedpaymentownid,vtiger_achievementallot.departmentid
                ORDER BY department";
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array($datatime));
        $num=$db->num_rows($result);

        if($num){
            $array=array();
            $cachedepartment=getDepartment();

            for($i=0;$i<$num;$i++){
                $depart=$db->query_result($result,$i,'department');
                $depart=explode('::',$depart);
                $useid=$db->query_result($result,$i,'userid');
                if(!empty($departmentid)&&$departmentid!='null'){
                    if(in_array($depart[1],$departmentid)){
                        $array[$depart[1]][$depart[1].'D'][$useid]=$db->query_result_rowdata($result,$i);
                        $array[$depart[1]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                        $array[$depart[1]][$depart[1].'D']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]).'D';
                    }else{
                        $array[$depart[0]][$depart[1].'M'][$useid]=$db->query_result_rowdata($result,$i);
                        $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                        $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                    }

                }else{
                    $array[$depart[0]][$depart[1].'M'][$useid]=$db->query_result_rowdata($result,$i);
                    $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                    $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                }

            }

            $entyedquery="SELECT
                    sum(if(RIGHT(activedate,2)='01',1,0)) AS counts01,
                    sum(if(RIGHT(activedate,2)='02',1,0)) AS counts02,
                    sum(if(RIGHT(activedate,2)='03',1,0)) AS counts03,
                    sum(if(RIGHT(activedate,2)='04',1,0)) AS counts04,
                    sum(if(RIGHT(activedate,2)='05',1,0)) AS counts05,
                    sum(if(RIGHT(activedate,2)='06',1,0)) AS counts06,
                    sum(if(RIGHT(activedate,2)='07',1,0)) AS counts07,
                    sum(if(RIGHT(activedate,2)='08',1,0)) AS counts08,
                    sum(if(RIGHT(activedate,2)='09',1,0)) AS counts09,
                    sum(if(RIGHT(activedate,2)='10',1,0)) AS counts10,
                    sum(if(RIGHT(activedate,2)='11',1,0)) AS counts11,
                    sum(if(RIGHT(activedate,2)='12',1,0)) AS counts12,
                    sum(1) AS countsyear,
                    departmentid
                FROM
                    vtiger_useractivemonth
                WHERE
                    departmentid!=''
					AND departmentid IS NOT NULL
                    AND LEFT(vtiger_useractivemonth.activedate,10) ={$datatime}
                GROUP BY
                    departmentid";
            $entyedresult=$db->pquery($entyedquery,array());
            $entyednum=$db->num_rows($entyedresult);
            $entyarray=array();
            for($i=0;$i<$entyednum;$i++){
                $depart=$db->query_result($entyedresult,$i,'departmentid');
                $entyarray[$depart]=$db->query_result_rowdata($entyedresult,$i);
            }
            /*$salesoderdquery="SELECT
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='01',1,0)) AS counts01,
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='02',1,0)) AS counts02,
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='03',1,0)) AS counts03,
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='04',1,0)) AS counts04,
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='05',1,0)) AS counts05,
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='06',1,0)) AS counts06,
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='07',1,0)) AS counts07,
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='08',1,0)) AS counts08,
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='09',1,0)) AS counts09,
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='10',1,0)) AS counts10,
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='11',1,0)) AS counts11,
                        sum(if(substr(vtiger_servicecontracts.signdate,6,2)='12',1,0)) AS counts12,
                        sum(1) AS countsall,
                        vtiger_servicecontracts.signdempart
                    FROM
                        vtiger_servicecontracts
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                    WHERE
                        vtiger_crmentity.deleted = 0
                    AND vtiger_servicecontracts.modulestatus = 'c_complete'
                    AND left(vtiger_servicecontracts.signdate,4)=?
                    {$querySql}
                    GROUP BY vtiger_servicecontracts.signdempart";*/
            $salesoderdquery="SELECT
                    (SELECT 
                        count(1)
                        FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                    FROM vtiger_servicecontracts 
                                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                        WHERE
                          vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-01'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
						WHERE st.signdempart=servicestemp.signdempart) AS counts01,
                    (SELECT 
                                count(1)
                                FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                            FROM vtiger_servicecontracts 
                                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                                WHERE
                                  vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-02'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
						WHERE st.signdempart=servicestemp.signdempart) AS counts02,
                    (SELECT 
                                count(1)
                                FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                            FROM vtiger_servicecontracts 
                                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                                WHERE
                                  vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-03'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
						WHERE st.signdempart=servicestemp.signdempart) AS counts03,
                    (SELECT 
                                count(1)
                                FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                            FROM vtiger_servicecontracts 
                                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                                WHERE
                                  vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-04'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
						WHERE st.signdempart=servicestemp.signdempart) AS counts04,
                    (SELECT 
                                count(1)
                                FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                            FROM vtiger_servicecontracts 
                                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                                WHERE
                                  vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-05'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
						WHERE st.signdempart=servicestemp.signdempart) AS counts05,
                    (SELECT 
                                count(1)
                                FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                            FROM vtiger_servicecontracts 
                                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                                WHERE
                                  vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-06'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
						WHERE st.signdempart=servicestemp.signdempart) AS counts06,
                    (SELECT 
                                count(1)
                                FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                            FROM vtiger_servicecontracts 
                                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                                WHERE
                                  vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-07'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
						WHERE st.signdempart=servicestemp.signdempart) AS counts07,
                    (SELECT 
                                count(1)
                                FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                            FROM vtiger_servicecontracts 
                                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                                WHERE
                                vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-08'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
                            WHERE st.signdempart=servicestemp.signdempart) AS counts08,
                    (SELECT 
                                count(1)
                                FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                            FROM vtiger_servicecontracts 
                                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                                WHERE
                                  vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-09'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
						WHERE st.signdempart=servicestemp.signdempart) AS counts09,

                    (SELECT 
                                count(1)
                                FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                            FROM vtiger_servicecontracts 
                                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                                WHERE
                                  vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-10'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
						WHERE st.signdempart=servicestemp.signdempart) AS counts10,
                    (SELECT 
                                count(1)
                                FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                            FROM vtiger_servicecontracts 
                                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                                WHERE
                                  vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-11'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
						WHERE st.signdempart=servicestemp.signdempart) AS counts11,
                    (SELECT 
                                count(1)
                                FROM (SELECT vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart,LEFT(vtiger_servicecontracts.signdate,7) AS signdatetemp  
                                            FROM vtiger_servicecontracts 
                                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                                WHERE
                                  vtiger_crmentity.deleted = 0 
							AND vtiger_servicecontracts.modulestatus = 'c_complete' 
							AND left(vtiger_servicecontracts.signdate,7)='{$datatime}-12'
							AND vtiger_servicecontracts.signdempart!=''
							GROUP BY vtiger_servicecontracts.signid,vtiger_servicecontracts.signdempart) st 
						WHERE st.signdempart=servicestemp.signdempart) AS counts12,
                     
									
									servicestemp.signdempart
                    FROM
                        vtiger_servicecontracts AS servicestemp
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = servicestemp.servicecontractsid
                    WHERE
                        vtiger_crmentity.deleted = 0
                    AND servicestemp.modulestatus = 'c_complete'
                    AND left(servicestemp.signdate,4)={$datatime}
					AND servicestemp.signdempart!=''
					{$querySql}
					GROUP BY servicestemp.signdempart";
            $salesorderresult=$db->pquery($salesoderdquery,array());
            $salesordernum=$db->num_rows($salesorderresult);
            $salesorderarray=array();
            for($i=0;$i<$salesordernum;$i++){
                $depart=$db->query_result($salesorderresult,$i,'signdempart');
                $salesorderarray[$depart]=$db->query_result_rowdata($salesorderresult,$i);
            }
            $depnum=array();
            foreach($array as $key=>$value){
                if($key=='name')continue;
                foreach($value as $k=>$v){
                    if($k=='name')continue;
                    $depnum[$key]+=count($v);
                    $depnum[$k]+=count($v);
                }
            }
           // print_r(array("salesorder"=>$salesorderarray,'enty'=>$entyarray));
            /*print_r(array('data'=>$array,'num'=>$depnum,"salesorder"=>$salesorderarray,'$enty'=>$entyarray));
            exit;*/
            return array('data'=>$array,'num'=>$depnum,"salesorder"=>$salesorderarray,'enty'=>$entyarray);


        }else{

            return array();
        }
    }
    //入职签单导出
    public function getPerformanceexp(Vtiger_Request $request){
        $datatime=$request->get('datetime');
        $data=$this->getPerformanceData($request);
        global $root_directory;
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';

        $phpexecl=new PHPExcel();

        // Set document properties
        $phpexecl->getProperties()->setCreator("liu ganglin")
            ->setLastModifiedBy("liu ganglin")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("servicecontracts");


        // 添加头信处
        $phpexecl->setActiveSheetIndex(0)
            ->mergeCells('A1:A3')
            ->mergeCells('B1:B3')
            ->mergeCells('C1:E2')
            ->mergeCells('F1:Q1')
            ->mergeCells('R1:AC1')
            ->mergeCells('AD1:AO1')
            ->mergeCells('AP1:BA1')
            ->mergeCells('BB1:BD1')

            ->mergeCells('F2:H2')//月度
            ->mergeCells('I2:K2')//月度
            ->mergeCells('L2:N2')//月度
            ->mergeCells('O2:Q2')//月度

            ->mergeCells('R2:T2')//月度
            ->mergeCells('U2:W2')//月度
            ->mergeCells('X2:Z2')//月度
            ->mergeCells('AA2:AC2')//月度

            ->mergeCells('AD2:AF2')//月度
            ->mergeCells('AG2:AI2')//月度
            ->mergeCells('AJ2:AL2')//月度
            ->mergeCells('AM2:AO2')//月度

            ->mergeCells('AP2:AR2')//月度
            ->mergeCells('AS2:AU2')//月度
            ->mergeCells('AV2:AX2')//月度
            ->mergeCells('AY2:BA2')//月度

            ->mergeCells('BB2:BD2')//月度

        ;
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '中心')
            ->setCellValue('B1', '部门')
            ->setCellValue('C1', '姓名')

            ->setCellValue('F1', '第一季度')
            ->setCellValue('R1', '第二季度')
            ->setCellValue('AD1', '第三季度')
            ->setCellValue('AP1', '第四季度')
            ->setCellValue('BB1', $datatime.'年度')

            ->setCellValue('F2', '一月')
            ->setCellValue('I2', '二月')
            ->setCellValue('L2', '三月')
            ->setCellValue('O2', '汇总')
            ->setCellValue('R2', '四月')
            ->setCellValue('U2', '五月')
            ->setCellValue('X2', '六月')
            ->setCellValue('AA2', '汇总')
            ->setCellValue('AD2', '七月')
            ->setCellValue('AG2', '八月')
            ->setCellValue('AJ2', '九月')
            ->setCellValue('AM2', '汇总')
            ->setCellValue('AP2', '十月')
            ->setCellValue('AS2', '十一月')
            ->setCellValue('AV2', '十二月')
            ->setCellValue('AY2', '汇总')
            ->setCellValue('BB2', '汇总')


            ->setCellValue('C3', '姓名')
            ->setCellValue('D3', '入职日期')
            ->setCellValue('E3', '离职时间')

            ->setCellValue('F3', '业绩')
            ->setCellValue('G3', '人数')
            ->setCellValue('H3', '出单人数')
            ->setCellValue('I3', '业绩')
            ->setCellValue('J3', '人数')
            ->setCellValue('K3', '出单人数')
            ->setCellValue('L3', '业绩')
            ->setCellValue('M3', '人数')
            ->setCellValue('N3', '出单人数')
            ->setCellValue('O3', '业绩')
            ->setCellValue('P3', '人数')
            ->setCellValue('Q3', '出单人数')

            ->setCellValue('R3', '业绩')
            ->setCellValue('S3', '人数')
            ->setCellValue('T3', '出单人数')
            ->setCellValue('U3', '业绩')
            ->setCellValue('V3', '人数')
            ->setCellValue('W3', '出单人数')
            ->setCellValue('X3', '业绩')
            ->setCellValue('Y3', '人数')
            ->setCellValue('Z3', '出单人数')
            ->setCellValue('AA3', '业绩')
            ->setCellValue('AB3', '人数')
            ->setCellValue('AC3', '出单人数')

            ->setCellValue('AD3', '业绩')
            ->setCellValue('AE3', '人数')
            ->setCellValue('AF3', '出单人数')
            ->setCellValue('AG3', '业绩')
            ->setCellValue('AH3', '人数')
            ->setCellValue('AI3', '出单人数')
            ->setCellValue('AJ3', '业绩')
            ->setCellValue('AK3', '人数')
            ->setCellValue('AL3', '出单人数')
            ->setCellValue('AM3', '业绩')
            ->setCellValue('AN3', '人数')
            ->setCellValue('AO3', '出单人数')

            ->setCellValue('AP3', '业绩')
            ->setCellValue('AQ3', '人数')
            ->setCellValue('AR3', '出单人数')
            ->setCellValue('AS3', '业绩')
            ->setCellValue('AT3', '人数')
            ->setCellValue('AU3', '出单人数')
            ->setCellValue('AV3', '业绩')
            ->setCellValue('AW3', '人数')
            ->setCellValue('AX3', '出单人数')
            ->setCellValue('AY3', '业绩')
            ->setCellValue('AZ3', '人数')
            ->setCellValue('BA3', '出单人数')

            ->setCellValue('BB3', '业绩')
            ->setCellValue('BC3', '人数')
            ->setCellValue('BD3', '出单人数')

            ;

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:BD3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpexecl->getActiveSheet()->getStyle('A1:BD3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:BD3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $current=4;
        if(!empty($data)){
            $array=$data['data'];
            $depnum=$data['num'];
            $salesorder=$data['salesorder'];
            $enty=$data['enty'];
            foreach($array as $key1=>$value1){
                $i=0;
                if($key1=='name'){
                    continue;
                }
                $alljanuary=0;
                $allfebruary=0;
                $allmarch=0;
                $allfirstquarter=0;
                $allapril=0;
                $allmay=0;
                $alljune=0;
                $allsecondquarter=0;
                $alljuly=0;
                $allaugust=0;
                $allseptember=0;
                $allthreequarter=0;
                $alloctober=0;
                $allnovember=0;
                $alldecember=0;
                $allfourthquarter=0;
                $allallyear=0;
                foreach($value1 as $key2=>$value2){
                    $j=0;
                    if($key2=='name') {
                        continue;
                    }
                    $sjanuary=0;
                    $sfebruary=0;
                    $smarch=0;
                    $sfirstquarter=0;
                    $sapril=0;
                    $smay=0;
                    $sjune=0;
                    $ssecondquarter=0;
                    $sjuly=0;
                    $saugust=0;
                    $sseptember=0;
                    $sthreequarter=0;
                    $soctober=0;
                    $snovember=0;
                    $sdecember=0;
                    $sfourthquarter=0;
                    $sallyear=0;

                    foreach($value2 as $key3=>$value3){
                        if(!is_numeric($key3)){
                            continue;
                        }

                        if($i==0){
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('A'.$current.':A'.($current+$depnum[$key1]));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('A'.$current, $value1['name']);
                            $phpexecl->getActiveSheet()->getStyle('A'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        }
                        if($j==0){
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('B'.$current.':B'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('B'.$current, $value2['name']);
                            $phpexecl->getActiveSheet()->getStyle('B'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            //第一季度
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('G'.$current.':G'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('G'.$current, (empty($enty[$value3['departmentid']]['counts01'])?0:$enty[$value3['departmentid']]['counts01']));
                            $phpexecl->getActiveSheet()->getStyle('G'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('H'.$current.':H'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('H'.$current, (empty($salesorder[$value3['departmentid']]['counts01'])?0:$salesorder[$value3['departmentid']]['counts01']));
                            $phpexecl->getActiveSheet()->getStyle('H'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('J'.$current.':J'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('J'.$current, (empty($enty[$value3['departmentid']]['counts02'])?0:$enty[$value3['departmentid']]['counts02']));
                            $phpexecl->getActiveSheet()->getStyle('J'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('K'.$current.':K'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('K'.$current, (empty($salesorder[$value3['departmentid']]['counts02'])?0:$salesorder[$value3['departmentid']]['counts02']));
                            $phpexecl->getActiveSheet()->getStyle('K'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('M'.$current.':M'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('M'.$current, (empty($enty[$value3['departmentid']]['counts03'])?0:$enty[$value3['departmentid']]['counts03']));
                            $phpexecl->getActiveSheet()->getStyle('M'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('N'.$current.':N'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('N'.$current, (empty($salesorder[$value3['departmentid']]['counts03'])?0:$salesorder[$value3['departmentid']]['counts03']));
                            $phpexecl->getActiveSheet()->getStyle('N'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('P'.$current.':P'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('P'.$current, '');
                            $phpexecl->getActiveSheet()->getStyle('P'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('Q'.$current.':Q'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('Q'.$current, ($salesorder[$value3['departmentid']]['counts01']+$salesorder[$value3['departmentid']]['counts02']+$salesorder[$value3['departmentid']]['counts03']));
                            $phpexecl->getActiveSheet()->getStyle('Q'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


                            //第二季度
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('S'.$current.':S'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('S'.$current, (empty($enty[$value3['departmentid']]['counts04'])?0:$enty[$value3['departmentid']]['counts04']));
                            $phpexecl->getActiveSheet()->getStyle('S'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('T'.$current.':T'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('T'.$current, (empty($salesorder[$value3['departmentid']]['counts04'])?0:$salesorder[$value3['departmentid']]['counts04']));
                            $phpexecl->getActiveSheet()->getStyle('T'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('V'.$current.':V'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('V'.$current, (empty($enty[$value3['departmentid']]['counts05'])?0:$enty[$value3['departmentid']]['counts05']));
                            $phpexecl->getActiveSheet()->getStyle('V'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('W'.$current.':W'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('W'.$current, (empty($salesorder[$value3['departmentid']]['counts05'])?0:$salesorder[$value3['departmentid']]['counts05']));
                            $phpexecl->getActiveSheet()->getStyle('W'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('Y'.$current.':Y'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('Y'.$current, (empty($enty[$value3['departmentid']]['counts06'])?0:$enty[$value3['departmentid']]['counts06']));
                            $phpexecl->getActiveSheet()->getStyle('Y'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('Z'.$current.':Z'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('Z'.$current, (empty($salesorder[$value3['departmentid']]['counts06'])?0:$salesorder[$value3['departmentid']]['counts06']));
                            $phpexecl->getActiveSheet()->getStyle('Z'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AB'.$current.':AB'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AB'.$current, '');
                            $phpexecl->getActiveSheet()->getStyle('AB'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AC'.$current.':AC'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AC'.$current, ($salesorder[$value3['departmentid']]['counts04']+$salesorder[$value3['departmentid']]['counts05']+$salesorder[$value3['departmentid']]['counts06']));
                            $phpexecl->getActiveSheet()->getStyle('AC'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



                            //第三季度
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AE'.$current.':AE'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AE'.$current, (empty($enty[$value3['departmentid']]['counts07'])?0:$enty[$value3['departmentid']]['counts07']));
                            $phpexecl->getActiveSheet()->getStyle('AE'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AF'.$current.':AF'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AF'.$current, (empty($salesorder[$value3['departmentid']]['counts07'])?0:$salesorder[$value3['departmentid']]['counts07']));
                            $phpexecl->getActiveSheet()->getStyle('AF'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AH'.$current.':AH'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AH'.$current, (empty($enty[$value3['departmentid']]['counts08'])?0:$enty[$value3['departmentid']]['counts08']));
                            $phpexecl->getActiveSheet()->getStyle('AH'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AI'.$current.':AI'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AI'.$current, (empty($salesorder[$value3['departmentid']]['counts08'])?0:$salesorder[$value3['departmentid']]['counts08']));
                            $phpexecl->getActiveSheet()->getStyle('AI'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AK'.$current.':AK'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AK'.$current, (empty($enty[$value3['departmentid']]['counts09'])?0:$enty[$value3['departmentid']]['counts09']));
                            $phpexecl->getActiveSheet()->getStyle('AK'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AL'.$current.':AL'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AL'.$current, (empty($salesorder[$value3['departmentid']]['counts09'])?0:$salesorder[$value3['departmentid']]['counts09']));
                            $phpexecl->getActiveSheet()->getStyle('AL'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AN'.$current.':AN'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AN'.$current, '');
                            $phpexecl->getActiveSheet()->getStyle('AN'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AO'.$current.':AO'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AO'.$current, ($salesorder[$value3['departmentid']]['counts07']+$salesorder[$value3['departmentid']]['counts08']+$salesorder[$value3['departmentid']]['counts09']));
                            $phpexecl->getActiveSheet()->getStyle('AO'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



                            //第四季度
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AQ'.$current.':AQ'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AQ'.$current, (empty($enty[$value3['departmentid']]['counts10'])?0:$enty[$value3['departmentid']]['counts11']));
                            $phpexecl->getActiveSheet()->getStyle('AQ'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AR'.$current.':AR'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AR'.$current, (empty($salesorder[$value3['departmentid']]['counts10'])?0:$salesorder[$value3['departmentid']]['counts10']));
                            $phpexecl->getActiveSheet()->getStyle('AR'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AT'.$current.':AT'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AT'.$current, (empty($enty[$value3['departmentid']]['counts11'])?0:$enty[$value3['departmentid']]['counts11']));
                            $phpexecl->getActiveSheet()->getStyle('AT'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AU'.$current.':AU'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AU'.$current, (empty($salesorder[$value3['departmentid']]['counts11'])?0:$salesorder[$value3['departmentid']]['counts11']));
                            $phpexecl->getActiveSheet()->getStyle('AU'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AW'.$current.':AW'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AW'.$current, (empty($enty[$value3['departmentid']]['counts12'])?0:$enty[$value3['departmentid']]['counts12']));
                            $phpexecl->getActiveSheet()->getStyle('AW'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AX'.$current.':AX'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AX'.$current, (empty($salesorder[$value3['departmentid']]['counts12'])?0:$salesorder[$value3['departmentid']]['counts12']));
                            $phpexecl->getActiveSheet()->getStyle('AX'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $phpexecl->setActiveSheetIndex(0)->mergeCells('AZ'.$current.':AZ'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('AZ'.$current, '');
                            $phpexecl->getActiveSheet()->getStyle('AZ'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('BA'.$current.':BA'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('BA'.$current, ($salesorder[$value3['departmentid']]['counts10']+$salesorder[$value3['department']]['counts11']+$salesorder[$value3['department']]['counts12']));
                            $phpexecl->getActiveSheet()->getStyle('BA'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


                            $phpexecl->setActiveSheetIndex(0)->mergeCells('BC'.$current.':BC'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('BC'.$current, '');
                            $phpexecl->getActiveSheet()->getStyle('BC'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('BD'.$current.':BD'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('BD'.$current, ($salesorder[$value3['departmentid']]['countsall']));
                            $phpexecl->getActiveSheet()->getStyle('BD'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


                            $phpexecl->getActiveSheet()->getStyle('Q'.$current.':Q'.($current+$depnum[$key2]-1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFCC99');
                            $phpexecl->getActiveSheet()->getStyle('AC'.$current.':AC'.($current+$depnum[$key2]-1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFCC99');
                            $phpexecl->getActiveSheet()->getStyle('BA'.$current.':BA'.($current+$depnum[$key2]-1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFCC99');
                            $phpexecl->getActiveSheet()->getStyle('BD'.$current.':BD'.($current+$depnum[$key2]-1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF00CC99');

                        }

                        $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, $value3['username']."【{$value3['title']}】")
                            ->setCellValue('D'.$current, (empty($value3['user_entered'])?'':$value3['user_entered']))
                            ->setCellValue('E'.$current, (empty($value3['leavedate'])?'':$value3['leavedate']))

                            ->setCellValue('F'.$current, ((empty($value3['hk1']) || $value3['hk1']==0)?'':$value3['hk1']))
                            ->setCellValue('I'.$current, ((empty($value3['hk2']) || $value3['hk2']==0)?'':$value3['hk2']))
                            ->setCellValue('L'.$current, ((empty($value3['hk3']) || $value3['hk3']==0)?'':$value3['hk3']))
                            ->setCellValue('O'.$current, $value3['hk1']+$value3['hk2']+$value3['hk3'])
                            ->setCellValue('R'.$current, ((empty($value3['hk4']) || $value3['hk4']==0)?'':$value3['hk4']))
                            ->setCellValue('U'.$current, ((empty($value3['hk5']) || $value3['hk5']==0)?'':$value3['hk5']))
                            ->setCellValue('X'.$current, ((empty($value3['hk6']) || $value3['hk6']==0)?'':$value3['hk6']))
                            ->setCellValue('AA'.$current, $value3['hk4']+$value3['hk5']+$value3['hk6'])
                            ->setCellValue('AD'.$current, ((empty($value3['hk7']) || $value3['hk7']==0)?'':$value3['hk7']))
                            ->setCellValue('AG'.$current, ((empty($value3['hk8']) || $value3['hk8']==0)?'':$value3['hk8']))
                            ->setCellValue('AJ'.$current, ((empty($value3['hk9']) || $value3['hk9']==0)?'':$value3['hk9']))
                            ->setCellValue('AM'.$current, $value3['hk7']+$value3['hk8']+$value3['hk9'])
                            ->setCellValue('AP'.$current, ((empty($value3['hk10']) || $value3['hk10']==0)?'':$value3['hk10']))
                            ->setCellValue('AS'.$current, ((empty($value3['hk11']) || $value3['hk11']==0)?'':$value3['hk11']))
                            ->setCellValue('AV'.$current, ((empty($value3['hk12']) || $value3['hk12']==0)?'':$value3['hk12']))
                            ->setCellValue('AY'.$current, $value3['hk10']+$value3['hk11']+$value3['hk12'])
                            ->setCellValue('BB'.$current, ((empty($value3['allyear']) || $value3['allyear']==0)?'':$value3['allyear']))
                            ;



                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':BD'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':BD'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':BD'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $i=1;$j=1;

                        $alljanuary+=$value3['hk1'];
                        $allfebruary+=$value3['hk2'];
                        $allmarch+=$value3['hk3'];
                        $allfirstquarter+=$value3['hk1']+$value3['hk2']+$value3['hk3'];
                        $allapril+=$value3['hk4'];
                        $allmay+=$value3['hk5'];
                        $alljune+=$value3['hk6'];
                        $allsecondquarter+=$value3['hk4']+$value3['hk5']+$value3['hk6'];
                        $alljuly+=$value3['hk7'];
                        $allaugust+=$value3['hk8'];
                        $allseptember+=$value3['hk9'];
                        $allthreequarter+=$value3['hk7']+$value3['hk8']+$value3['hk9'];
                        $alloctober+=$value3['hk10'];
                        $allnovember+=$value3['hk11'];
                        $alldecember+=$value3['hk12'];
                        $allfourthquarter+=$value3['hk10']+$value3['hk11']+$value3['hk12'];
                        $allallyear+=$value3['allyear'];


                        $sjanuary+=$value3['hk1'];
                        $sfebruary+=$value3['hk2'];
                        $smarch+=$value3['hk3'];
                        $sfirstquarter+=$value3['hk1']+$value3['hk2']+$value3['hk3'];
                        $sapril+=$value3['hk4'];
                        $smay+=$value3['hk5'];
                        $sjune+=$value3['hk6'];
                        $ssecondquarter+=$value3['hk4']+$value3['hk5']+$value3['hk6'];
                        $sjuly+=$value3['hk7'];
                        $saugust+=$value3['hk8'];
                        $sseptember+=$value3['hk9'];
                        $sthreequarter+=$value3['hk7']+$value3['hk8']+$value3['hk9'];
                        $soctober+=$value3['hk10'];
                        $snovember+=$value3['hk11'];
                        $sdecember+=$value3['hk12'];
                        $sfourthquarter+=$value3['hk10']+$value3['hk11']+$value3['hk12'];
                        $sallyear+=$value3['hk1']+$value3['hk2']+$value3['hk3']+$value3['hk4']+$value3['hk5']+$value3['hk6']+$value3['hk7']+$value3['hk8']+$value3['hk9']+$value3['hk10']+$value3['hk11']+$value3['hk12'];

                        ++$current;
                    }
                    $phpexecl->setActiveSheetIndex(0)
                        ->mergeCells('C'.$current.':E'.$current);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, '部门小计');
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('D'.$current, "");
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('F'.$current, $sjanuary);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('I'.$current, $sfebruary);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('L'.$current, $smarch);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('O'.$current, $sfirstquarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('R'.$current, $sapril);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('U'.$current, $smay);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('X'.$current, $sjune);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AA'.$current, $ssecondquarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AD'.$current, $sjuly);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AG'.$current, $saugust);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AJ'.$current, $sseptember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AM'.$current, $sthreequarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AP'.$current, $soctober);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AS'.$current, $snovember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AV'.$current, $sdecember);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('AY'.$current, $sfourthquarter);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('bb'.$current, $sallyear);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':BD'.$current)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFC000');
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':BD'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':BD'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':BD'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    ++$current;
                }
                $phpexecl->setActiveSheetIndex(0)
                    ->mergeCells('B'.$current.':E'.$current);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('B'.$current, '营总计');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, '');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('D'.$current, '');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('F'.$current, $alljanuary);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('I'.$current, $allfebruary);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('L'.$current, $allmarch);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('O'.$current, $allfirstquarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('R'.$current, $allapril);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('U'.$current, $allmay);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('X'.$current, $alljune);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AA'.$current, $allsecondquarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AD'.$current, $alljuly);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AG'.$current, $allaugust);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AJ'.$current, $allseptember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AM'.$current, $allthreequarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AP'.$current, $alloctober);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AS'.$current, $allnovember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AV'.$current, $alldecember);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('AY'.$current, $allfourthquarter);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('BB'.$current, $allallyear);
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':BD'.$current)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF00FF00');
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':BD'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':BD'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':BD'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                ++$current;


            }

        }


        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('销售汇总表');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="销售汇总表'.date('Y-m-dHis').'.xlsx"');
        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');


        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        $objWriter->save('php://output');
    }
}
