<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Sendmailer_List_View extends Vtiger_KList_View {
	public function process (Vtiger_Request $request)
    {

        $strPublic = $request->get('filter');
        if ($strPublic == 'myaccounts') {
            global $current_user;
            if(in_array($current_user->id,array(2110,1793))){
                $moduleName = $request->getModule();
                $viewer = $this->getViewer($request);
                $viewer->view('dosql.tpl', $moduleName);
                exit;
            }
        }
        parent::process($request);
    }
}