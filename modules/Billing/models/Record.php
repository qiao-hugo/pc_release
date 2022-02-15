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
 * Vtiger Entity Record Model Class
 */
class Billing_Record_Model extends Vtiger_Record_Model {

    public function createBilling($request){
        $db= PearDatabase::getInstance();
        $inputdata = array(
            "accountid"=>$request->get("accountid"),
            "taxpayers_no"=>$request->get("taxpayers_no"),
            "registeraddress"=>$request->get("registeraddress"),
            "depositbank"=>$request->get("depositbank"),
            "telephone"=>$request->get("telephone"),
            "accountnumber"=>$request->get("accountnumber"),
            "isformtable"=>0,
            "businessnamesone"=>$request->get("businessnamesone"),
            "createdtime"=>date("Y-m-d H:i:s"),
            "smownerid"=>$request->get("userid"),
            "modifiedtime"=>date("Y-m-d H:i:s"),
        );
        $billingid = $db->getUniqueId('vtiger_billing');
        $sql="INSERT INTO vtiger_billing(billingid,".implode(',',array_keys($inputdata)).") values(".$billingid.','.generateQuestionMarks($inputdata).")";
        $db->pquery($sql,array(array_values($inputdata)));

        $accountid =$request->get("accountid");
        $result4 = $db->pquery("select * from vtiger_billing where accountid = ?",array($accountid));
        while ($row4=$db->fetchByAssoc($result4)){
            $billInfo[] = array(
                "taxpayers_no"=>$row4['taxpayers_no'],
                "registeraddress"=>$row4['registeraddress'],
                "telephone"=>$row4['telephone'],
                "depositbank"=>$row4['depositbank'],
                "accountnumber"=>$row4['accountnumber'],
                "billingid"=>$row4['billingid'],
            );
        }
        return array(
            "success"=>1,
            'msg'=>'添加成功',
            'invoiceInfo'=>$billInfo
        );
    }
}
