<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


ini_set('include_path',ini_get('include_path'). PATH_SEPARATOR . '../..');
//ini_set('include_path','C:\\Program Files\\vtigercrm600\\apache\\htdocs\\vtigerCRM\\');


require_once 'includes/Loader.php';
require_once 'include/utils/utils.php';
require_once('include/utils/CommonUtils.php');
require_once('vtlib/Vtiger/Functions.php');
require_once("config.inc.php");
require_once("libraries/HTTP_Session/Session.php");
require_once('include/database/PearDatabase.php');
require_once 'include/Webservices/Utils.php';
require_once("modules/Users/Users.php");
//require_once("modules/Users/models/Record.php");
require_once("include/Webservices/State.php");
require_once("include/Webservices/OperationManager.php");
require_once("include/Webservices/SessionManager.php");
require_once("include/Zend/Json.php");
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once("include/Webservices/VtigerCRMObject.php");
require_once("include/Webservices/VtigerCRMObjectMeta.php");
require_once("include/Webservices/DataTransform.php");
require_once("include/Webservices/WebServiceError.php");
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/utils/VtlibUtils.php';
require_once('include/logging.php');
require_once 'include/Webservices/WebserviceEntityOperation.php';
require_once 'include/Webservices/Retrieve.php';
require_once('modules/Emails/mail.php');
require_once 'modules/Users/Users.php';
require_once 'modules/com_vtiger_workflow/include.inc';
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once 'includes/runtime/LanguageHandler.php';

function vtRunTaskJob($adb){
	$util = new VTWorkflowUtils();
	$adminUser = $util->adminUser();print_r($task);
	$tq = new VTTaskQueue($adb);
	$readyTasks = $tq->getReadyTasks();
	$tm = new VTTaskManager($adb);
	
	foreach($readyTasks as $taskDetails){
		list($taskId, $entityId, $taskContents) = $taskDetails;
		$task = $tm->retrieveTask($taskId);
		
        //If task is not there then continue
        if(empty($task)){
            continue;
        }

		$task->setContents($taskContents);
		
        //$entity = VTEntityCache::getCachedEntity($entityId);
		
        //if(!$entity) {
        	
            //$entity = new VTWorkflowEntity($adminUser, $entityId);
        //}
        
		$task->doTask($entity);
	}
}
vtRunTaskJob($adb);



?>
