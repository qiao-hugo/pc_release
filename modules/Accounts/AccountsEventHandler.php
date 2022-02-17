<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once 'modules/com_vtiger_workflow/VTEventHandler.inc';
require_once 'modules/Emails/mail.php';
require_once 'modules/HelpDesk/HelpDesk.php';

class AccountsEventHandler extends VTEventHandler {

	function handleEvent($eventName, $data) {
		//if($eventName == 'vtiger.entity.beforesave.final') {
			// Entity is about to be saved, take required action
// 			echo 'vtiger.entity.beforesave.final';
// 			$file=fopen('C:\Program Files\vtigercrm600\apache\htdocs\vtigerCRM\logs\test.txt', 'a+');
// 			fwrite($file,$eventName);
// 			fclose($file);
		//}
		
	}
}
