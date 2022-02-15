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
class ContractsProducts_Record_Model extends Vtiger_Record_Model {
    /**
     * Function to retieve display value for a field
     * @param <String> $fieldName - field name for which values need to get
     * @return <String>
     * 详情页面自定义的标题
     */
    public function getDisplayValue($fieldName,$recordId = false) {
        if(empty($recordId)) {
            $recordId = $this->getId();
        }
        $fieldModel = $this->getModule()->getField($fieldName);
        if($fieldModel) {
            $db = PearDatabase::getInstance();
            $sql="SELECT vtiger_contract_type.contract_type as contract_type FROM `vtiger_contract_type`WHERE vtiger_contract_type.contract_typeid =".$this->get($fieldName)."";
            $result = $db->pquery($sql);
            $contract_type = $db->query_result($result,'contract_type');
            return $contract_type;
        }
        return false;
    }

    /**
     * @param $id
     * @return array
     * @author: steel.liu
     * @Date:xxx
     * 获取对应关联的不同主体的开票内容
     */
    public function getInvoicecompanyList($id){
        global $adb;
        $query='SELECT * FROM vtiger_invoicecompanybill WHERE deleted=0 AND relcontractsproductsid=?';
        $result=$adb->pquery($query,array($id));
        $array=array();
        while($row=$adb->fetch_array($result)){
            $array[]=$row;
        }
        return $array;
    }

}
