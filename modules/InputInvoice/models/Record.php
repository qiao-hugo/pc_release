<?php
class InputInvoice_Record_Model extends Vtiger_Record_Model
{
    public function discountCouponInfo($record){
        $db = PearDatabase::getInstance();
        $sql = "select * from vtiger_input_discount_coupon where inputinvoiceid=?  order by inputdiscountcouponid desc limit 1";
        $result = $db->pquery($sql,array($record));
        if(!$db->num_rows($result)){
            return array();
        }
        return $db->fetchByAssoc($result,0);
    }

    public function saveDiscountCoupon($params){
        $inputInvoiceId = $params['inputinvoiceid'];
        $db = PearDatabase::getInstance();
        $sql = "select inputdiscountcouponid from vtiger_input_discount_coupon where inputinvoiceid = ?";
        $result = $db->pquery($sql,array($inputInvoiceId));
        if(!$db->num_rows($result)){
            $insertSql = "INSERT INTO vtiger_input_discount_coupon(`inputinvoiceid`, `salename`, `buyername`, `invoicecode`,
                                         `invoiceno`, `servicename`, `amount`, `taxrate`, `taxamount`, `totalpricetax`) 
                                         VALUES (?,?,?,?,?,?,?,?,?,?)";
            $db->pquery($insertSql,array($inputInvoiceId,$params['salename'],$params['buyername'],$params['invoicecode'],$params['invoiceno'],
                $params['servicename'],$params['amount'],$params['taxrate'],$params['taxamount'],$params['totalpricetax']));
        }else{
            $updateSql = "UPDATE vtiger_input_discount_coupon set salename=?,buyername=?,invoicecode=?,invoiceno=?,
                                        servicename=?,amount=?,taxrate=?,taxamount=?,totalpricetax=? where inputinvoiceid=?";
            $db->pquery($updateSql,array($params['salename'],$params['buyername'],$params['invoicecode'],$params['invoiceno'],
                $params['servicename'],$params['amount'],$params['taxrate'],$params['taxamount'],$params['totalpricetax'],$inputInvoiceId));
        }
        return true;
    }

    public static function getInvoiceCompanyCode($invoicecompany){
        $db = PearDatabase::getInstance();
        $sql = "select companycode from vtiger_invoicecompany where invoicecompany=? limit 1";
        $result = $db->pquery($sql,array($invoicecompany));
        if(!$db->num_rows($result)){
            return '';
        }
        $row = $db->fetchByAssoc($result,0);
        return $row['companycode'];
    }

    public function exportGrouprt($module,$classname,$id=0){
        if($id==0)
        {
            global $current_user;
            $id = $current_user->id;
        }
        $db=PearDatabase::getInstance();
        $query="SELECT 1 FROM vtiger_exportmanage WHERE deleted=0 AND userid=? AND module=? AND classname=?";
        $result=$db->pquery($query,array($id,$module,$classname));
        $num=$db->num_rows($result);
        if($num){
            return true;
        }
        return false;
    }

}