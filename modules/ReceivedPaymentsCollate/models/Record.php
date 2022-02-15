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
class ReceivedPaymentsCollate_Record_Model extends Vtiger_Record_Model {

    public function getReceivedPayments($recordId){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("select a.oldpaymentchannel,a.oldowncompany,a.oldreality_date,a.oldpaymentcode from vtiger_receivedpaymentscollate a where receivedpaymentscollateid=?",array($recordId));
        if(!$db->num_rows($result)){
            return array();
        }
        $row=$db->fetchByAssoc($result,0);
        return $row;
    }

    public function getReceivedPaymentSidByCollate($recordId){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("select a.receivedpaymentsid,a.paymentchannel,a.owncompany,a.reality_date,a.paymentcode from vtiger_receivedpayments  a left join vtiger_receivedpaymentscollate b on a.receivedpaymentsid=b.receivedpaymentsid where receivedpaymentscollateid=?",array($recordId));
        if(!$db->num_rows($result)){
            return array();
        }
        $row=$db->fetchByAssoc($result,0);
        return $row['receivedpaymentsid'];
    }
}
