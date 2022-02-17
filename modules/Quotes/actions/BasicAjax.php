<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Quotes_BasicAjax_Action extends Vtiger_Action_Controller {
	
    function checkPermission(Vtiger_Request $request) {
        return;
    }
    
    public function process(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $db=PearDatabase::getInstance();
        $accountname = $db->pquery('SELECT `accountname`,`accountid` FROM vtiger_account WHERE accountid = (SELECT related_to FROM vtiger_potential WHERE potentialid = ?)',array($recordId));
        $data=array();
        $data[0]=$accountname->fields['0'];
        $data[1]=$accountname->fields['1'];
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
