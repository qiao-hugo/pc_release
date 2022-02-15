<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


class SeparateInto_Record_Model extends Vtiger_Record_Model{

    /**
     * @param $separateintoid
     * @return array
     * @author: steel.liu
     * @Date:xxx
     * 分成单信息
     */
    public  function servicecontracts_divide($separateintoid){
        $db=PearDatabase::getInstance();
        $sql = "SELECT *,( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_servicecontracts_separate.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownname FROM `vtiger_servicecontracts_separate` WHERE separateintoid =?";
        $result = $db->pquery($sql,array($separateintoid));
        $result_li = array();
        if($db->num_rows($result)>0){
            for($i=0;$i<$db->num_rows($result);$i++){
                $result_li[] = $db->fetchByAssoc($result);
            }
        }
        return $result_li;
    }

    /**
     * @param $servicecontractsid
     * @return bool
     * @author: steel.liu
     * @Date:xxx
     * 合同是否重复
     */
    public  function servicecontracts_checked($servicecontractsid){
        $db=PearDatabase::getInstance();
        $sql = "SELECT 1 FROM `vtiger_separateinto` LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_separateinto.separateintoid WHERE vtiger_crmentity.deleted=0 AND vtiger_separateinto.modulestatus<>'a_exception' AND vtiger_separateinto.servicecontractsid=? LIMIT 1";
        $result = $db->pquery($sql,array($servicecontractsid));
        if($db->num_rows($result)>0){
            return true;
        }
        return false;
    }

    public function getMarketingShareInfo($accountId){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select frommarketing,mtime from vtiger_account where accountid=?",array($accountId));
        $row=$db->fetchByAssoc($result,0);
        $frommarketing=$row['frommarketing'];
        $mtime=$row['mtime'];
        if(!$frommarketing){
            return array();
        }

        global $marketingShareUserId;
        $query="SELECT a.invoicecompany,b.departmentid FROM vtiger_users a left join vtiger_user2department b on a.id=b.userid WHERE a.status='Active' AND a.id=? limit 1";
        $result3=$db->pquery($query,array($marketingShareUserId));
        $row3 = $db->fetchByAssoc($result3,0);
        $leadRecordModel = Leads_Record_Model::getCleanInstance("Leads");
        $shareSetting = $leadRecordModel->getCurrentAvailableShareSetting();
        $leadProtectDay = $leadRecordModel->getLeadProtectDay();
        $shareInfo=array(
            "protectday"=>$leadProtectDay,
            "userid"=>$marketingShareUserId,
            'invoicecompany'=>$row3['invoicecompany'],
            'promotionsharing'=>$shareSetting['promotionsharing'],
            'salesharing'=>$shareSetting['salesharing'],
            'departmentid'=>$shareSetting['departmentid'],
        );

        if(!$leadProtectDay){
            return $shareInfo;
        }

        $sql="select createdtime from vtiger_activationcode where customerid=? and status in(0,1) order by createdtime limit 1";
        $result2 = $db->pquery($sql,array($accountId));
        if(!$db->num_rows($result2)){
            return $shareInfo;
        }
        $row2=$db->fetchByAssoc($result2,0);
        if(strtotime($row2['createdtime'])<$mtime){
            return $shareInfo;
        }

        if(((strtotime($row2['createdtime'])+$leadProtectDay*24*60*60))<time() ){
            return array();
        }
        return $shareInfo;
    }


}