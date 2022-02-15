<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

 Class Workflows_Record_Model extends Vtiger_Record_Model {
 	function get($key) {
 		$value = parent::get($key);
 		if ($key === 'notecontent') {
 			return decode_html($value);
 		}
 		return $value;
 	}
	function getOne(){
		return true;
	}
	/**
	 * 根据角色获取有执行权限的流程
	 */
	function getRoleWorkflows(){
		return false;
	}

     public function getFilterWorkFlows($data){
         $db = PearDatabase::getInstance();
         $workflowstageids = $data['workflowstageids'];
         $result2 = $db->pquery("select workflowstagesname,workflowstagesid from vtiger_workflowstages where workflowstagesid in (".$workflowstageids.') order by field(workflowstagesid,'.$workflowstageids.')',array());
         $sonCateWorkFlows=array();
         if($db->num_rows($result2)){
             while ($row2 =$db->fetchByAssoc($result2)){
                 $workflowstage[] = $row2;
             }
//             $sonCateWorkFlows[] =$workflowstage;
         }
         return $workflowstage;
     }

     public function getWorkFlowStage($workflowsid){
		$db = PearDatabase::getInstance();
		$result = $db->pquery("select workflowstagesid,workflowstagesname from vtiger_workflowstages where workflowsid=? order by sequence asc",array($workflowsid));
		if(!$db->num_rows($result)){
			return array();
		}
		$data= array();
		while ($row=$db->fetchByAssoc($result)){
			$data[] = $row;
		}
		return $data;
	 }


	 public function createFilterWorkFlowStagesbak($sourceid,$modulename,$workflowsid,$invoicecompany,$companycode,$workflowstageids,$creator,$ceocheck){
			$db = PearDatabase::getInstance();
			$sql = "insert into vtiger_filterworkflowstage(sourceid,modulename,workflowsid,invoicecompany,companycode,workflowstageids,creator,ceocheck) values (?,?,?,?,?,?,?,?)";
			$db->pquery($sql,array($sourceid,$modulename,$workflowsid,$invoicecompany,$companycode,$workflowstageids,$creator,$ceocheck));
	 }
	 public function createFilterWorkFlowStages($params){
		 $db = PearDatabase::getInstance();
		 $questionMarks=implode(',',array_map(function(){return '?';},$params));
		 $columns=implode(',',array_keys($params));
		 $sql = "insert into vtiger_filterworkflowstage(".$columns.") values (".$questionMarks.")";
		 $db->pquery($sql,$params);
	 }

	 public function updateFilterWorkFlow($filterworkflowstageid,$workflowstageids,$reviser,$ceocheck,$departmentid,$departments){
         $db = PearDatabase::getInstance();
         $sql = "update vtiger_filterworkflowstage set workflowstageids=?,reviser=?,updateat=?,ceocheck=?,departmentid=?,department=? where filterworkflowstageid=?";
         $db->pquery($sql,array($workflowstageids,$reviser,date("Y-m-d H:i:s"),$ceocheck,$departmentid,$departments,$filterworkflowstageid));
	 }
	 public function deleteRecordWorkFlow($filterworkflowstageid,$reviser){
		 $db = PearDatabase::getInstance();
		 $sql = "update vtiger_filterworkflowstage set deleted=1,reviser=?,updateat=? where filterworkflowstageid=?";
		 $db->pquery($sql,array($reviser,date("Y-m-d H:i:s"),$filterworkflowstageid));
	 }
	 public function checkCreateFilterWorkFlow($recordid,$departmentid,$filterworkflowstageid){
		 $db = PearDatabase::getInstance();
		 $departmentidStr='(';
		 foreach($departmentid AS $value){
			 $departmentidStr.="FIND_IN_SET('".$value."',departmentid) OR ";
		 }
		 $departmentidStr=trim($departmentidStr,' OR ');
		 $departmentidStr.=')';
		 $filterworkflowstageidstr='';
		 if($filterworkflowstageid>0){
			 $filterworkflowstageidstr=' AND filterworkflowstageid!='.$filterworkflowstageid;
		 }
		 $sql = "SELECT department FROM vtiger_filterworkflowstage WHERE deleted=0 AND sourceid=?".$filterworkflowstageidstr." AND ".$departmentidStr;
		 $result=$db->pquery($sql,array($recordid));
		 $data=array('falg'=>false);
		 if($db->num_rows($result)){
			 $data=array('falg'=>true);
			 while($row=$db->fetch_array($result)){
				 $data['data'].=$row['department'].'<br>';
			 }
		 }
		 return $data;
	 }
 }
?>
