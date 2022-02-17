<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Invoice_RelationListView_Model extends Inventory_RelationListView_Model {
    static $relatedquerylist = array(
        'Billing'=>'SELECT billingid as crmid,taxpayers_no,registeraddress,depositbank,telephone,accountnumber,isformtable,businessnamesone,modulestatus FROM vtiger_billing WHERE vtiger_billing.accountid=(SELECT accountid FROM vtiger_invoice WHERE invoiceid=? limit 1)','Invoicesign'=>"SELECT invoicesignid as crmid,path FROM vtiger_invoicesign WHERE vtiger_invoicesign.invoiceid=?",
    );

    public function getEntries($pagingModel){
        //获取关联模块查询语句
        //marketprice
        $relatedModuleName=$_REQUEST['relatedModule'];
        $relatedquerylist=self::$relatedquerylist;
        if(isset($relatedquerylist[$relatedModuleName])){
            $parentId = $_REQUEST['record'];
            $this->relationquery=str_replace('?',$parentId,$relatedquerylist[$relatedModuleName]);
        }
        return parent::getEntries($pagingModel);
    }
}
?>