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
 * ModComments Record Model
 */
class ContractTplSet_Record_Model extends Vtiger_Record_Model {

    /**
     * @param Vtiger_Request $request
     * @return bool
     */
    public function isRepeatTpl(Vtiger_Request $request)
    {
        $products_code=$request->get('products_code');
        $company_code=$request->get('company_code');
        $db=PearDatabase::getInstance();
        $query='SELECT 1 FROM `vtiger_contracttplset` WHERE company_code=? AND products_code=?;';
        $result=$db->pquery($query,array($company_code,$products_code));
        if($db->num_rows($result)){
            return true;
        }
        return false;
    }

}