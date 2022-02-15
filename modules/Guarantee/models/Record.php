<?php
/*+************
 * 数据记录模型 用于新增编辑
 ***************/
class Guarantee_Record_Model extends Vtiger_Record_Model {

    static public function getguaranteelist($id){
        $db=PearDatabase::getInstance();
        global $current_user;
        $query="SELECT (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_guarantee.userid=vtiger_users.id) as userid, (vtiger_servicecontracts.contract_no) as contractid,vtiger_guarantee.contractid as contractid_reference, (vtiger_salesorder.subject) as salesorderid,vtiger_guarantee.salesorderid as salesorderid_reference,vtiger_guarantee.total,vtiger_guarantee.guaranteeid,vtiger_guarantee.createdtime FROM vtiger_guarantee  LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_guarantee.contractid  LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_guarantee.salesorderid  WHERE 1=1 AND deleted=0 AND userid=".$current_user->id.' ORDER BY vtiger_guarantee.guaranteeid DESC';
        $arr['user']=$db->run_query_allrecords($query);
        $query="SELECT (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_guarantee.userid=vtiger_users.id) as userid, (vtiger_servicecontracts.contract_no) as contractid,vtiger_guarantee.contractid as contractid_reference, (vtiger_salesorder.subject) as salesorderid,vtiger_guarantee.salesorderid as salesorderid_reference,vtiger_guarantee.total,vtiger_guarantee.guaranteeid,vtiger_guarantee.createdtime FROM vtiger_guarantee  LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_guarantee.contractid  LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_guarantee.salesorderid  WHERE 1=1 AND deleted=0 AND vtiger_guarantee.salesorderid=".$id." ORDER BY vtiger_guarantee.presence ASC,vtiger_guarantee.guaranteeid ASC";
        $arr['saleorder']=$db->run_query_allrecords($query);
        $query="SELECT (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_guarantee.userid=vtiger_users.id) as userid, (vtiger_servicecontracts.contract_no) as contractid,vtiger_guarantee.contractid as contractid_reference, (vtiger_salesorder.subject) as salesorderid,vtiger_guarantee.salesorderid as salesorderid_reference,vtiger_guarantee.total,vtiger_guarantee.guaranteeid,vtiger_guarantee.createdtime,vtiger_guarantee.delta,vtiger_guarantee.deltatime FROM vtiger_guarantee  LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_guarantee.contractid  LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_guarantee.salesorderid  WHERE 1=1 AND deleted=1 AND vtiger_guarantee.salesorderid=".$id." ORDER BY vtiger_guarantee.presence ASC,vtiger_guarantee.guaranteeid ASC";
        $arr['saleorderhistory']=$db->run_query_allrecords($query);
        $arr['alls']='';
        if(in_array($current_user->id,getDepartmentUser('H25'))||$current_user->id==1){
            $query="SELECT (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_guarantee.userid=vtiger_users.id) as userid,sum(vtiger_guarantee.total) AS totals FROM vtiger_guarantee  LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_guarantee.contractid  LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_guarantee.salesorderid  WHERE 1=1 AND deleted=0 GROUP BY vtiger_guarantee.userid ORDER BY vtiger_guarantee.guaranteeid DESC";
            $arr['alls']=$db->run_query_allrecords($query);
        }
        return $arr;
    }
    static public function getsalesoderid($salesorder){
        $db=PearDatabase::getInstance();
        $key=key($salesorder);
        $value=current($salesorder);
        $query="SELECT salesorderid,servicecontractsid from vtiger_salesorder  WHERE {$key}=?";
        $result=$db->pquery($query,array($value));
        $num=$db->num_rows($result);
        if($num>0){
            return $db->query_result_rowdata($result);
        }else{
            return false;
        }
    }

    /**
     * 该担保人可担保的总金额
     * @return int
     */
    static function getGuarantetotal(){
        global $current_user;
        $userids=getDepartmentUser('H9');//品牌客户部
        $arr=array('H80'=>5000,'H79'=>10000,'H78'=>10000,'H87'=>10000,'roleid'=>array('H79','H80','H78','H87'));//H80商务经理//H79商务总监,H78部门体系负责人
        if(in_array($current_user->column_fields['roleid'],$arr['roleid'])){
            return $arr[$current_user->column_fields['roleid']];
        }elseif(in_array($current_user->id,$userids) ||$current_user->id==1){
            return 5000;
        }
        return 0;
    }

    /**
     * 已担保的金额
     */
    static function getGuarantecurrentpay(){
        global $current_user;
        $db=PearDatabase::getInstance();
        $query="SELECT IFNULL(sum(total),0) as totals FROM vtiger_guarantee WHERE deleted=0 AND userid=?";
        $result=$db->pquery($query,array($current_user->id));
        return $db->query_result($result,0,'totals');
    }
    /**
     * 对应的工单的担保金额
     */
    static function getGuarantecurrent($salesorderid){
        //global $current_user;
        $db=PearDatabase::getInstance();
        $query="SELECT IFNULL(sum(total),0) as totals FROM vtiger_guarantee WHERE deleted=0 AND salesorderid=?";
        $result=$db->pquery($query,array($salesorderid));
        return $db->query_result($result,0,'totals');
    }

    /**
     * 该合同已收回款的金额
     */
    static function getreceivedayprice($contractid){
        $db=PearDatabase::getInstance();
        $query="SELECT IFNULL(sum(vtiger_receivedpayments.unit_price),0) AS sumtotal FROM `vtiger_receivedpayments` WHERE relatetoid =?";
        $results=$db->pquery($query,array($contractid));
        return $db->query_result($results,0,'sumtotal');//所有回款的之合
    }
    /**
     * 工单总成本
     * @param $salesorderid
     * @throws Exception
     */
    static function getrealprice($salesorderid){
        $db=PearDatabase::getInstance();
        $query = "SELECT sum(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)) AS realprice FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.salesorderid =? AND vtiger_salesorderproductsrel.multistatus in(0,3)";//回款总合
        $realprices=$db->pquery($query,array($salesorderid));
        return $db->query_result($realprices,0,'realprice');
    }
    /**
     * 已经担保工单总成本
     * @param $contractid
     * @throws Exception
     */
    static function alreadycalculate($contractid,$salesorderid){
        $db=PearDatabase::getInstance();
        $query = "SELECT ifnull(sum(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)),0) AS realprice FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.servicecontractsid=? AND vtiger_salesorderproductsrel.salesorderid!=? AND vtiger_salesorder.alreadycalculate=1 AND vtiger_salesorder.guaranteetotal=0 AND vtiger_salesorderproductsrel.multistatus in(0,3)";//回款总合
        $realprices=$db->pquery($query,array($contractid,$salesorderid));
        return $db->query_result($realprices,0,'realprice');
    }

    /**
     * 更新工单对应的担保金额
     * @param $currntprice
     * @param $salesorderid
     */
    static function updatesalesordertotal($currntprice,$occupancyamount,$salesorderid){
        $db=PearDatabase::getInstance();
        $sql="UPDATE vtiger_salesorder SET vtiger_salesorder.guaranteetotal=?,alreadycalculate=1,occupancyamount=? WHERE vtiger_salesorder.salesorderid=?";
        $db->pquery($sql,array($currntprice,$occupancyamount,$salesorderid));
    }

    /**
     * 已经占用回款的工单的总金额
     * @param $contractid
     * @param $salesorderid
     * @return mixed|string
     * @throws Exception
     */
    static public function getoccupancyamount($contractid,$salesorderid){
        $db=PearDatabase::getInstance();
        $query = " SELECT IFNULL(sum(vtiger_salesorder.occupancyamount),0) AS occupancyamount FROM `vtiger_salesorder` WHERE servicecontractsid=? AND salesorderid!=? AND modulestatus!='c_cancel'";
        $realprices=$db->pquery($query,array($contractid,$salesorderid));
        return $db->query_result($realprices,0,'occupancyamount');//对应已计算工单的占用的回款
    }
}
