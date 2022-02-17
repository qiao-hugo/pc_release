<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Inventory Record Model Class
 */
class AchievementallotStatistic_Record_Model extends Vtiger_Record_Model {
    /**
     * 求合
     * @return string
     */
    public function getListviewCountSql(){
        $listQuery = "SELECT
                      count(1) AS counts
                        FROM
                            vtiger_receivedpayments
                        RIGHT  JOIN  vtiger_achievementallot_statistic ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot_statistic.receivedpaymentsid
                                    left join vtiger_servicecontracts on vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid 
                        WHERE
                            1 = 1";

        return $listQuery;
    }

    /**
     * T云业绩分成
     * @return string
     */
    public function getListvViewSqlTyun(){
        /*return "SELECT
                    vtiger_achievementallot_statistic.scalling,
                    vtiger_achievementallot_statistic.achievementallotid,
                    vtiger_achievementallot_statistic.owncompanys,
                    vtiger_achievementallot_statistic.matchdate,
                    vtiger_achievementallot_statistic.departmentid,
                    vtiger_achievementallot_statistic.postingdate,
                    (SELECT CONCAT( last_name, '[', IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT departmentid FROM vtiger_user2department WHERE userid=vtiger_users.id LIMIT 1 )),''), ']', (IF(`status` = 'Active', '', '[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_achievementallot_statistic.receivedpaymentownid = vtiger_users.id) AS receivedpaymentownid,
                    vtiger_achievementallot_statistic.businessunit,
                    (SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid =(SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1)),''), ']',(IF( `status`='Active','','[离职]')))AS last_name FROM vtiger_users WHERE vtiger_receivedpayments.createid=vtiger_users.id )AS createid,
                    vtiger_receivedpayments.reality_date,
                    vtiger_receivedpayments.createdtime,
                    vtiger_receivedpayments.overdue,
                    vtiger_receivedpayments.unit_price,
                    vtiger_receivedpayments.modifiedtime,
                    vtiger_receivedpayments.paytitle,
                    vtiger_receivedpayments.owncompany,
                    IF(vtiger_receivedpayments.fallinto = 1,'是','否') AS fallinto,
                    vtiger_servicecontracts.contract_no AS relatetoid,
                    vtiger_servicecontracts.servicecontractsid AS relatetoid_reference,       
                    (SELECT CONCAT( last_name, '[',IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_receivedpayments.checkid = vtiger_users.id ) AS checkid,
                    vtiger_servicecontracts.total,	
                    vtiger_receivedpayments.exchangerate,
                    vtiger_receivedpayments.accountscompany,
                    vtiger_receivedpayments.receivementcurrencytype,
                    vtiger_receivedpayments.standardmoney,
                    vtiger_achievementallot_statistic.tyuncost,
                    vtiger_achievementallot_statistic.othercost,
                    vtiger_achievementallot_statistic.workorderdate,
                    vtiger_achievementallot_statistic.firstmarketprice,
                    vtiger_achievementallot_statistic.secondmarketprice,
                    vtiger_achievementallot_statistic.idccost,
                    vtiger_salesorder.modulestatus,
                    if(vtiger_servicecontracts.multitype>0,'是','否') AS multitype,
                    vtiger_products.productname AS productid,
                    vtiger_account.accountname,
                    vtiger_servicecontracts.signdate,
                    (SELECT sum(unit_price) FROM vtiger_receivedpayments AS AONE WHERE AONE.relatetoid=vtiger_receivedpayments.relatetoid AND AONE.deleted=0 AND AONE.receivedstatus='normal') AS cdtamount,
                    (SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_servicecontracts.servicecontractsid AND multistatus in(0,1) LIMIT 1) AS agelife,
                    vtiger_servicecontracts.servicecontractstype,
                    vtiger_salesorder.salesorder_no,
                    vtiger_receivedpayments.receivedpaymentsid
                FROM vtiger_receivedpayments
                RIGHT JOIN vtiger_achievementallot_statistic ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot_statistic.receivedpaymentsid
                LEFT JOIN vtiger_servicecontracts on vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid 
                LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid
                LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_servicecontracts.productid
                LEFT JOIN vtiger_salesorder ON (vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid AND vtiger_salesorder.modulestatus<>'c_cancel')
                WHERE
                    vtiger_receivedpayments.deleted=0
                AND vtiger_servicecontracts.modulestatus='c_complete'
                AND vtiger_receivedpayments.receivedstatus='normal'
                AND vtiger_servicecontracts.parent_contracttypeid=2";*/
        return "
                    SELECT
                        vtiger_achievementallot_statistic.achievementallotid,
                        vtiger_receivedpayments.owncompany,  /* 账户 */
                        left(vtiger_receivedpayments.createtime,10) as createtime , /* 创建时间*/ 
                        vtiger_receivedpayments.reality_date, /* 收款日期 */
                        vtiger_receivedpayments.matchdate,      /* 匹配日期 */   
                        vtiger_receivedpayments.paytitle,  /* 汇款抬头 */   
                        vtiger_receivedpayments.unit_price as unit_price, /* 汇款金额 */  
                        TRUNCATE(vtiger_receivedpayments.unit_price*vtiger_achievementallot_statistic.scalling/100,2) AS unit_prices,/*收款金额(已分成） */  
                        vtiger_receivedpayments.departmentid,/* 部门 */  
                        (SELECT dd.departmentname FROM vtiger_departments dd WHERE dd.parentdepartment=left(vtiger_departments.parentdepartment,10)) AS groupname,/* 业务员所属事业部 */  
                        vtiger_departments.departmentname,/* 销售组 */  
                        vtiger_users.last_name as receivedpaymentownid ,/* 业务员 */  
                        vtiger_servicecontracts.servicecontractstype, /* 新单/续费/升降/降级/另购 */  
                        left(vtiger_servicecontracts.signdate,10) AS signdate, /* 合同签订日期 */ 
                        vtiger_servicecontracts.contract_no, /* 合同编号 */ 
                        TRUNCATE(vtiger_servicecontracts.total*IFNULL((SELECT sum(vtiger_servicecontracts_divide.scalling)/count(1) FROM vtiger_servicecontracts_divide WHERE  vtiger_servicecontracts.servicecontractsid=vtiger_servicecontracts_divide.servicecontractid AND vtiger_servicecontracts_divide.receivedpaymentownid=vtiger_achievementallot_statistic.receivedpaymentownid),vtiger_achievementallot_statistic.scalling)/100,2) AS total,/*合同总金额*/
                        TRUNCATE((SELECT sum(IFNULL(vtiger_salesorderproductsrel.costing,0)*vtiger_achievementallot_statistic.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid = vtiger_servicecontracts.servicecontractsid),2) AS costing,/*工单人力成本*/
                        TRUNCATE((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot_statistic.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid = vtiger_servicecontracts.servicecontractsid),2) AS purchasemount,/*工单外采成本*/
                        vtiger_account.accountname, /* 客户名称 */
                        vtiger_products.productname AS productid, /* 产品名称 */
                        vtiger_activationcode.productlife, /*Tyun 类年限*/
                        (SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_servicecontracts.servicecontractsid AND multistatus in(0,1) LIMIT 1) AS agelife,/*非Tyun类年限*/
                        vtiger_activationcode.marketprice, /*业绩市场价*/
                        TRUNCATE(vtiger_activationcode.marketprice * vtiger_achievementallot_statistic.scalling,2) as dividemarketprice, /*已分成业绩市场价*/
                        TRUNCATE((SELECT sum(IF (extra_type = '沙龙',IFNULL(extra_price, 0) * vtiger_achievementallot_statistic.scalling / 100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS xalong,
                        TRUNCATE((SELECT sum(IF(extra_type = '外采',IFNULL(extra_price, 0) * vtiger_achievementallot_statistic.scalling / 100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS waici,
                        TRUNCATE((SELECT sum(IF(extra_type = '媒介充值',IFNULL(extra_price, 0) * vtiger_achievementallot_statistic.scalling / 100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS meijai,
                        TRUNCATE((SELECT sum(IF(extra_type != '沙龙' AND extra_type != '外采' AND extra_type = '媒介充值',IFNULL(extra_price, 0) * vtiger_achievementallot_statistic.scalling / 100,0)) FROM	vtiger_receivedpayments_extra	WHERE	vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS qite,
                        vtiger_workflows.workflowsname as workflowsid,/*工作流*/
                        vtiger_achievementallot_statistic.workflowstime,/*工作流时间*/
                        vtiger_achievementallot_statistic.workflowsnode,/*工作流节点*/
                        IF(vtiger_achievementallot_statistic.isover=1,'是','否') as isover /*是否完结*/
                    FROM
                         vtiger_achievementallot_statistic
                    LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot_statistic.receivedpaymentsid
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot_statistic.receivedpaymentownid
                    LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                    LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                    LEFT JOIN vtiger_parent_contracttype ON vtiger_servicecontracts.parent_contracttypeid=vtiger_parent_contracttype.parent_contracttypeid
                    LEFT JOIN vtiger_activationcode ON vtiger_activationcode.contractid =vtiger_servicecontracts.servicecontractsid 
                    LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_servicecontracts.productid
                    LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid = vtiger_achievementallot_statistic.workflowsid
                    WHERE
                        vtiger_receivedpayments.deleted=0
                    AND vtiger_servicecontracts.modulestatus='c_complete'
                    AND vtiger_receivedpayments.receivedstatus='normal'
                    AND vtiger_servicecontracts.parent_contracttypeid=2
        ";
    }
    /**
     * T云业绩分成之合
     * @return string
     */
    public function getListviewCountSqlTyun(){
        return "SELECT 
                    count(1) as counts
                    FROM
                         vtiger_achievementallot_statistic
                    LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot_statistic.receivedpaymentsid
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot_statistic.receivedpaymentownid
                    LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                    LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                    LEFT JOIN vtiger_parent_contracttype ON vtiger_servicecontracts.parent_contracttypeid=vtiger_parent_contracttype.parent_contracttypeid
                    LEFT JOIN vtiger_activationcode ON vtiger_activationcode.contractid =vtiger_servicecontracts.servicecontractsid 
                    LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_servicecontracts.productid
                    WHERE
                        vtiger_receivedpayments.deleted=0
                    AND vtiger_servicecontracts.modulestatus='c_complete'
                    AND vtiger_receivedpayments.receivedstatus='normal'"
                   ;
    }

    /**
     * @param Vtiger_Request $request
     * @return array
     * 回参
    新单收款  new_order_amount
    续费收款  ahand_amount
    新单有效回款   new_order_re_amount
    续费有效回款   ahand_re_amount
    新单到账业绩   new_order_achievement
    续费到账业绩   ahand_achievement
    SaaS类新单收款    saas_new_order_amount
    SaaS类新单有效回款    saas_new_order_re_amount
    SaaS类续费收款     saas_order_re_amount
    SaaS类续费有效回款    saas_ahand_re_amount
    用户id         user_id
    销售月份       sale_mounth
     拜访量 visits_number

     */
    public function getUserAchievement(Vtiger_Request $request){
        $rawData=file_get_contents('php://input');
        $jsonData=(array)json_decode($rawData,true);
        $return=array('success'=>false,'msg'=>'无效参数');
        do{
            if($jsonData['user_id']<0){
                $return['msg']='无效user_id';
                break;
            }
            $sale_mounth=explode('-',$jsonData['sale_mounth']);
            if(!checkdate($sale_mounth[1],1,$sale_mounth[0])){
                $return['msg']='无效sale_mounth';
                break;
            }
            $group_id=$jsonData['group_id'];
            global $adb;
            $firstday = date('Y-m-01', strtotime($jsonData['sale_mounth']));
            $lastday=date("Y-m-d",strtotime("+1 months",strtotime($jsonData['sale_mounth'])));
            //$lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
            if(2==$group_id){
                $query='SELECT vtiger_departments.departmentid FROM vtiger_departments LEFT JOIN vtiger_user2department ON FIND_IN_SET(vtiger_user2department.departmentid,REPLACE(vtiger_departments.parentdepartment,\'::\',\',\')) WHERE vtiger_user2department.userid=?';
                $departResult=$adb->pquery($query,array($jsonData['user_id']));
                if($adb->num_rows($departResult)){
                    $array=array();
                    while($row=$adb->fetch_array($departResult)){
                        $array[]=$row['departmentid'];
                    }
                    $userid=$receivedpaymentownid="departmentid in('".implode("','",$array)."')";
                }else{
                    $userid=$receivedpaymentownid="departmentid in('-1')";

                }
                $extractid='vtiger_visitingorder.extractid='.(int)$jsonData['user_id'];
            }else{
                $receivedpaymentownid='receivedpaymentownid='.(int)$jsonData['user_id'];
                $userid='userid='.(int)$jsonData['user_id'];
                $extractid='vtiger_visitingorder.extractid='.(int)$jsonData['user_id'];
            }
            //$visitsNumberResult=$adb->pquery('SELECT sum(visits_number)/2 as visits_number from (SELECT max(signnum) as visits_number FROM `vtiger_effective_visits` WHERE '.$userid.' AND visit_start_date BETWEEN ? AND ? AND iscomplete=1 GROUP BY accountid) as st',array($firstday,$lastday));
            $visitByOwner=$adb->pquery("SELECT visitingorderid FROM (SELECT vtiger_visitingorder.visitingorderid,vtiger_visitingorder.related_to as related_to_reference FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid WHERE 1 = 1 AND vtiger_crmentity.deleted = 0 AND vtiger_visitingorder.modulestatus='c_complete' AND vtiger_visitingorder.isstrangevisit=0 AND ".$extractid." AND vtiger_visitingorder.startdate >= ? AND  vtiger_visitingorder.enddate <= ? AND vtiger_visitingorder.related_to > 0 GROUP BY vtiger_visitingorder.visitingorderid ) AS a GROUP BY a.related_to_reference ",array($firstday,$lastday));
            $visitByOwnerNUmber=$adb->num_rows($visitByOwner);
            $achievementallotResult=$adb->pquery("SELECT 
                        sum(if(achievementtype='newadd',unit_prices,0)) as new_order_amount,
                        sum(if(achievementtype='renew',unit_prices,0)) as ahand_amount,
                        sum(if(achievementtype='newadd' OR ( achievementtype='renew' AND  more_years_renew=1 ),effectiverefund,0)) as new_order_re_amount,
                        sum(if(achievementtype='renew' AND more_years_renew=1 ,effectiverefund,0)) as ahand_re_amount,
                        sum(if(achievementtype='newadd' OR ( achievementtype='renew' AND  more_years_renew=1 ) ,arriveachievement,0)) as new_order_achievement,
                        sum(if(achievementtype='renew' AND more_years_renew=1,arriveachievement,0)) as ahand_achievement,
                        sum(if(achievementtype='newadd' AND producttype in(1,2,4),unit_prices,0)) as saas_new_order_amount,
                        sum(if(achievementtype='newadd' AND producttype in(1,2,4),effectiverefund,0)) as saas_new_order_re_amount,
                        sum(if(achievementtype='renew' AND producttype in(1,2,4),unit_prices,0)) as saas_order_re_amount,
                        sum(if(achievementtype='renew' AND producttype in(1,2,4),effectiverefund,0)) as saas_ahand_re_amount,
                        receivedpaymentownid as user_id,
                        achievementmonth as sale_mounth
                         FROM `vtiger_achievementallot_statistic` WHERE ".$receivedpaymentownid." AND achievementmonth=?",array($jsonData['sale_mounth']));
            $data=array(
                'new_order_amount'=>(float)$achievementallotResult->fields[0],
                'ahand_amount'=>(float)$achievementallotResult->fields[1],
                'new_order_re_amount'=>(float)$achievementallotResult->fields[2],
                'ahand_re_amount'=>(float)$achievementallotResult->fields[3],
                'new_order_achievement'=>(float)$achievementallotResult->fields[4],
                'ahand_achievement'=>(float)$achievementallotResult->fields[5],
                'saas_new_order_amount'=>(float)$achievementallotResult->fields[6],
                'saas_new_order_re_amount'=>(float)$achievementallotResult->fields[7],
                'saas_order_re_amount'=>(float)$achievementallotResult->fields[8],
                'saas_ahand_re_amount'=>(float)$achievementallotResult->fields[9],
                'user_id'=>(int)$jsonData['user_id'],
                'sale_mounth'=>$jsonData['sale_mounth'],
                //'visits_number'=>(float)$visitsNumberResult->fields[0],
                'visits_number'=>$visitByOwnerNUmber,
                'invitation_volume'=>$visitByOwnerNUmber
                );
            $return=array('success'=>true,'data'=>$data,'msg'=>'获取成功');
        }while(0);
        return $return;
    }
    /**
     * 获取员工的提成
     * @param $request
     * @return array
     */
    public function getPercentage($request){
        global $adb;
        $rawData=file_get_contents('php://input');
        $jsonData=(array)json_decode($rawData,true);
        $salay_year_month=$jsonData['assessMonth'];
        $userids=$jsonData['userIds'];
        $salay_year_month=substr($salay_year_month,0,7);
        if(empty($userids)){
            return array('success'=>false,'msg'=>'没有相关数据!');
        }
        $newUserIds=array_map(function($v){return (int)$v;},$userids);
        $query="SELECT userid,sum(IFNULL(actualroyalty,0)) as percentage,sum(if(achievementtype='newadd',IFNULL(actualroyalty,0),0)) as newaddpercentage,sum(if(achievementtype='renew',IFNULL(actualroyalty,0),0)) as renewpercentage FROM vtiger_achievementsummary WHERE  achievementmonth=? AND userid IN(".implode(',',$newUserIds).") GROUP BY userid";
        $result=$adb->pquery($query,array($salay_year_month));
        if($adb->num_rows($result)){
            $data=array();
            while($row=$adb->fetchByAssoc($result)){
                $data[]=$row;
            }
            $return=array('success'=>true,'data'=>$data,'msg'=>'获取成功');
        }else{
            $return=array('success'=>false,'msg'=>'没有相关数据');
        }
        return $return;
    }
    /**
     * 当前已经配置的权限用户
     * @return array
     */
    public static function getDepartmentData(){
        $db=PearDatabase::getInstance();
        $query="SELECT vtiger_custompermtable.custompermtableid as id,last_name,permissions FROM vtiger_custompermtable LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_custompermtable.userid WHERE module='AchievementallotStatistic' ORDER BY custompermtableid DESC";
        //$query="SELECT vtiger_custompermtable.custompermtableid as id,last_name,permissions FROM vtiger_custompermtable LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_custompermtable.userid  ORDER BY custompermtableid DESC";
        return $db->run_query_allrecords($query);
    }

}