<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SalesOrder_RelationListView_Model extends Vtiger_RelationListView_Model {
    static $relatedquerylist = array(
        'IdcRecords'=>'SELECT idcrecordsid AS crmid,vtiger_IdcRecords.* FROM vtiger_IdcRecords WHERE salesorder_no = ?',
    );

    public function getEntries($pagingModel){
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