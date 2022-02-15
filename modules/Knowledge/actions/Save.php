<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Knowledge_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
		//Restrict to store indirect relationship from Potentials to Contacts
		$file = $request->get('file');
		$request->set('document',$file);
		parent::process($request);
	}
}
