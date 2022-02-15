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

class Rsalesanalysis_selectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getCountsday');
        $this->exposeMethod('getdetaillist');
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
        $datetime=(int)$request->get('datetime');
        $startdatetime=$request->get('startdatetime');
        $enddatetime=$request->get('enddatetime');
        $userid=$request->get('userid');
        $departmentid=$request->get('department');
        //处理时间
        //var_dump($datetime);
        if($datetime<1 || $datetime>14){
            $groupby='createdtime';
            $groupbyyear='createdtime';
            if($datetime==15){
                if(!$this->checkDate($startdatetime)||!$this->checkDate($enddatetime)){
                    $nowtime=date("Y-m");
                    $tempdate=" AND LEFT(createdtime,7)='{$nowtime}'";
                }else{
                    if($startdatetime<$enddatetime){
                        $tempdate=" AND createdtime BETWEEN '{$startdatetime}' AND '{$enddatetime}'";
                    }elseif($startdatetime>$enddatetime){
                        $tempdate=" AND createdtime BETWEEN '{$enddatetime}' AND '{$startdatetime}'";
                    }else{
                        $tempdate=" AND LEFT(createdtime,10)='{$startdatetime}'";
                    }
                }
            }elseif($datetime>2014){
                $tempdate=" AND LEFT(createdtime,4)='{$datetime}'";
                $groupby='left(createdtime,7)';
                $groupbyyear='left(createdtime,7) AS createdtime';
            }else{
                $nowtime=date("Y-m");
                $tempdate=" AND LEFT(createdtime,7)='{$nowtime}'";
            }

        }else{
            switch((int)$datetime){
                case 14:
                    $tempdate=" AND YEARWEEK(createdtime)=YEARWEEK(now())";
                    break;
                case 13:
                    $nowtime=date("Y-m");
                    $tempdate=" AND LEFT(createdtime,7)='{$nowtime}'";
                    break;
                default :
                    $nowtime=date("Y");
                    $newdatetime=$datetime<10?"0{$datetime}":$datetime;
                    $tempdate=" AND LEFT(createdtime,7)='{$nowtime}-{$newdatetime}'";
            }
            $groupby='createdtime';
            $groupbyyear='createdtime';
        }
        $fliter=$request->get('fliter');
        if($fliter=='thisweek'){
            $lastday=date('Y-m-d',strtotime("Sunday"));
            $firstday=date('Y-m-d',strtotime("$lastday -6 days"));
            $tempdate = "  AND LEFT(createdtime,10) BETWEEN '{$firstday}' AND '{$lastday}'";
        }else if($fliter=='thismonth'){
            $firstday = date('Y-m-01');
            $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
            $tempdate = "  AND LEFT(createdtime,10) BETWEEN '{$firstday}' AND '{$lastday}'";
        }
        //处理部门
        $db=PearDatabase::getInstance();
        $query="SELECT ";
        $arr=array();
        if($userid!='null'&&!empty($userid)){
            $query1="SELECT id,last_name FROM vtiger_users WHERE id in(".implode(',',$userid).") limit 5";
            $uresult=$db->pquery($query1);
            $num1=$db->num_rows($uresult);
            if($num1>0){
                for($i=0;$i<$num1;++$i){
                    //$arrnum['user'.$db->query_result($uresult,$i,'id')]=1;
                    $arr['newdepartmentid'][]='user'.$db->query_result($uresult,$i,'id');
                    $arr['newdepartment']['user'.$db->query_result($uresult,$i,'id')]=$db->query_result($uresult,$i,'last_name');
                    //$query.="sum(if(vtiger_servicecontracts.receiveid IN (".$db->query_result($uresult,$i,'id')."),IFNULL(vtiger_servicecontracts.total,0),0)) as user".$db->query_result($uresult,$i,'id').",";
                    $query.="sum(if(smownerid IN (".$db->query_result($uresult,$i,'id')."),daycounts,0)) as daycounts_user".$db->query_result($uresult,$i,'id').",
                            sum(if(smownerid IN (".$db->query_result($uresult,$i,'id')."),dayforp,0)) as dayforp_user".$db->query_result($uresult,$i,'id').",
                            sum(if(smownerid IN (".$db->query_result($uresult,$i,'id')."),daysaler,0)) as daysaler_user".$db->query_result($uresult,$i,'id').",
                            sum(if(smownerid IN (".$db->query_result($uresult,$i,'id')."),dayvisiting,0)) as dayvisiting_user".$db->query_result($uresult,$i,'id').",
                            ";
                }
                $flag=1;
            }
        }else{
            if($departmentid=="null"||empty($departmentid)){
                $departmentid=array();
                $departmentid[]='H1';
            }
            $cachedepartment=getDepartment();
            $arrnum=array();//部门中有多少个人
            //部门不能超过10个
            for($i=0;$i<count($departmentid)&&$i<10;++$i){
                $userid=getDepartmentUser($departmentid[$i]);
                $where=getAccessibleUsers('Rsalesanalysis','List',true);
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
                $arrnum[strtolower($departmentid[$i])]=count($where);
                $arr['newdepartmentid'][]=strtolower($departmentid[$i]);
                $arr['newdepartment'][strtolower($departmentid[$i])]=str_replace(array('|','—'),array('',''),$cachedepartment[$departmentid[$i]]);
                $query.="sum(if(smownerid IN (".implode(',',$where)."),daycounts,0)) as daycounts_{$departmentid[$i]},
                sum(if(smownerid IN (".implode(',',$where)."),dayforp,0)) as dayforp_{$departmentid[$i]},
                sum(if(smownerid IN (".implode(',',$where)."),daysaler,0)) as daysaler_{$departmentid[$i]},
                sum(if(smownerid IN (".implode(',',$where)."),dayvisiting,0)) as dayvisiting_{$departmentid[$i]},
                ";
            }
        }
        if($flag==0){
            $arr=Array('newdepartmentid' => Array('hno'),'newdepartment' => Array('hno' => '暂无数据'),'daycounts_hno' => Array(0),'dayforp_hno' => Array(0),'daysaler_hno' => Array(0),'dayvisiting_hno' => Array(0),'createdtime' => Array('暂无数据'));
            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($arr);
            $response->emit();
            exit;
        }
        //$datetime=date('Y-m-d');
        $db=PearDatabase::getInstance();
        $query.="{$groupbyyear}
                FROM
                    vtiger_reporting_view
                WHERE 1=1
                {$tempdate}
                GROUP BY
                    {$groupby}
                ";
        //echo $query;
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        //$arr=array();
        if($num>0){
            for($i=0;$i<$num;$i++){
                //print_r($db->query_result_rowdata($result,$i));
                foreach($db->query_result_rowdata($result,$i) as $key=>$value){
                    if(is_numeric($key)){
                        continue;
                    }else{
                        $arr[$key][]=$value;
                    }
                }
            }
        }else{
            $arr=Array('newdepartmentid' => Array('hno'),'newdepartment' => Array('hno' => '暂无数据'),'daycounts_hno' => Array(0),'dayforp_hno' => Array(0),'daysaler_hno' => Array(0),'dayvisiting_hno' => Array(0),'createdtime' => Array('暂无数据'));
        }
        //print_r($arr);
        //exit;
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();
    }

    public function getUsers(Vtiger_Request $request){
        $departmentid=$request->get('department');
        if(!empty($departmentid)&&$departmentid!='H1'){
            $userid=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('Rsalesanalysis','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery = ' AND id in('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers('Rsalesanalysis','List',false);
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
    private function checkDate($date,$format='Y-m-d'){
        if(!strtotime($date)){
           return false;
        }
        if(date($format,strtotime($date))==$date){
            return true;
        }
        return false;
    }
    //单击取得
    public function getdetaillist(Vtiger_Request $request){
        $dataIndex=$request->get('seriesIndex');
        $datatime=$request->get('datetime');
        $userid=$request->get('datauserid');
        if(strlen($datatime)==7){
            $tempdate=" left(####,7)='{$datatime}'";
        }else{
            $tempdate=" left(####,10)='{$datatime}'";
        }
        $fliter=$request->get('fliter');
        if($fliter=='thisweek'){
            $lastday=date('Y-m-d',strtotime("Sunday"));
            $firstday=date('Y-m-d',strtotime("$lastday -6 days"));
            $tempdate = "  AND LEFT(####,10) BETWEEN '{$firstday}' AND '{$lastday}'";
        }else if($fliter=='thismonth'){
            $firstday = date('Y-m-01');
            $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
            $tempdate = "  AND LEFT(####,10) BETWEEN '{$firstday}' AND '{$lastday}'";
        }
        $userid=ltrim($userid,'user');
        if(is_numeric($userid)){
            $sql=" AND vtiger_crmentity.smownerid={$userid}";
        }else{
            //if(!empty($userid)&&$userid!='H1'){
                $userid=strtoupper($userid);
                $usersid=getDepartmentUser($userid);
                $where=getAccessibleUsers('Rsalesanalysis','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$usersid);
                }else{
                    $where=$usersid;
                }
                $sql = ' AND vtiger_crmentity.smownerid in('.implode(',',$where).')';
            /*}else{
                $where=getAccessibleUsers('Rsalesanalysis','List',false);
                if($where!='1=1'){
                    $sql =' AND vtiger_crmentity.smownerid '.$where;
                }else{
                    $sql='';
                }
            }*/

        }
        switch($dataIndex){
            //每日新增客户数
            case 0:
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
                        AND {$tempdate}
                        {$sql}
                        ORDER BY
                            vtiger_account.mtime DESC LIMIT 1000";
                $query=str_replace('####','vtiger_crmentity.createdtime',$query);
                break;


            //每日新增40%客户数
            case 1:
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
                        AND {$tempdate}
                        {$sql} LIMIT 1000";
                $query=str_replace('####','vtiger_accountrankhistory.createdtime',$query);
                break;
            //每日拜访客户数
            case 2:
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
                        AND {$tempdate}{$sql} LIMIT 1000";
                $query=str_replace('vtiger_crmentity.smownerid','vtiger_visitingorder.extractid',$query);
                $query=str_replace('####','vtiger_visitingorder.startdate',$query);
                break;
            //每日成交客户数
            case 3:
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
                        AND {$tempdate}
                        {$sql}
                        GROUP BY vtiger_servicecontracts.servicecontractsid LIMIT 1000";
                $query=str_replace('vtiger_crmentity.smownerid','vtiger_servicecontracts.receiveid',$query);
                $query=str_replace('####','vtiger_servicecontracts.firstreceivepaydate',$query);
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
        switch($dataIndex){
            case 0:
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['industry']=vtranslate($arrlist[$i]['industry'],'Accounts');
                    $arrlist[$i]['regionalpartition']=vtranslate($arrlist[$i]['regionalpartition'],'Accounts');
                    $arrlist[$i]['createdtime']=substr($arrlist[$i]['createdtime'],0,10);
                    $arrlist[$i]['accountrank']=vtranslate($arrlist[$i]['accountrank']);
                }
                break;
            case 1:
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['makedecision']=vtranslate($arrlist[$i]['makedecision'],'Accounts');
                    $arrlist[$i]['createdtime']=substr($arrlist[$i]['createdtime'],0,10);
                    $arrlist[$i]['firstvisittime']=substr($arrlist[$i]['firstvisittime'],0,10);
                }
                break;
            case 2:
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['makedecision']=vtranslate($arrlist[$i]['makedecision'],'Accounts');
                    //$arrlist[$i]['purpose']=substr($arrlist[$i]['purpose'],0,10)==false?'':substr($arrlist[$i]['purpose'],0,10);
                }
                break;
            case 3:
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['industry']=vtranslate($arrlist[$i]['industry'],'Accounts');
                    $arrlist[$i]['data_entered']=substr($arrlist[$i]['data_entered'],0,10);
                    $arrlist[$i]['saleorderlastdealtime']=substr($arrlist[$i]['saleorderlastdealtime'],0,10);
                    $arrlist[$i]['visitingtimes']=$arrlist[$i]['visitingtimes']==null?0:$arrlist[$i]['visitingtimes'];
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

}
