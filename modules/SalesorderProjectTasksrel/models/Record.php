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
 * Inventory Record Model Class
 */
class SalesorderProjectTasksrel_Record_Model extends Vtiger_Record_Model {

// 	function getAllSalesorderWorkflowStages($records){
// 		$db = PearDatabase::getInstance();
// 		$result = $db->pquery("SELECT workflowstagesname,salesorderid FROM `vtiger_salesorderworkflowstages` where salesorderid in($records) and isaction=1 GROUP BY salesorderid",array());
// 		$stages=array();
// 		for($i=0; $i<$db->num_rows($result); $i++) {
// 			$workflowstagesname = $db->query_result($result, $i, 'workflowstagesname');
// 			$id =	$db->query_result($result, $i, 'salesorderid');
// 			//$workflowstagesname = $db->query_result($result, $i, 'workflowstagesname');
// 			$stages[$id]=array('salesorderid'=>$id,'workflowstagesname'=>$workflowstagesname);
// 		}
// 		return  Zend_Json::encode($stages);
// 	}
// 	/**
// 	 * 获取关于record的任务记录
// 	 * @param unknown $record
// 	 */
// 	function getAllSalesorderProjectTasksrel($record){
// 		$db = PearDatabase::getInstance();
// 		$result=$db->pquery("select * from `vtiger_salesorderprojecttasksrel` where salesorderid = ",array($record));
// 	}
}