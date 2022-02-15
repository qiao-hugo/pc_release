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
class PreInvoiceDeferral_Record_Model extends Vtiger_Record_Model {


    /**
     * 获取流程id
     * @return bool|mixed
     */
    static public function getWorkFlowId(){
        global $adb;
        $sql="select workflowsid from vtiger_workflows where mountmodule='PreInvoiceDeferral' and workflowsname='预开票回款延期申请'";
        $result=$adb->pquery($sql,array());
        $row=$adb->fetch_row($result);
        if($row['workflowsid']){
            return $row['workflowsid'];
        }
        return false;
    }


}
