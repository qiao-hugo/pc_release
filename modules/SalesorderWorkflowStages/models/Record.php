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
class SalesorderWorkflowStages_Record_Model extends Vtiger_Record_Model {

	/**
	 * 获取关于record的任务记录
	 * @param unknown $record
	 */
	 public static function getInstanceById($record, $module = NULL, $flag = false){
	 	$new=new self();
		return $new;
	}
	public function getAll($record,$modulename){
		$db = PearDatabase::getInstance();
		$result=$db->pquery("select * from `vtiger_salesorderworkflowstages` where salesorderid =? and modulename=?  order by workflowsid,sequence asc ",array($record,$modulename));
		//return  $result;
		//echo "select * from `vtiger_salesorderworkflowstages` where salesorderid =? and modulename=?  order by sequence asc";print_r(array($record,$modulename));die();
		$temparr =array();
		while($re = $db->fetch_array($result)){
			$temparr[]=$re;
		}
		return $temparr;
	}
	
	/**
	 * 获取所有的节点
	 * @param unknown $records
	 * @return string
	 */
	function getAllSalesorderWorkflowStages($records){
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT workflowstagesname,salesorderid,schedule FROM `vtiger_salesorderworkflowstages` where salesorderid in($records) and isaction=1 GROUP BY salesorderid",array());
		$stages=array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$workflowstagesname = $db->query_result($result, $i, 'workflowstagesname');
			$id =	$db->query_result($result, $i, 'salesorderid');
			//$workflowstagesname = $db->query_result($result, $i, 'workflowstagesname');
			$stages[$id]=array('salesorderid'=>$id,'workflowstagesname'=>$workflowstagesname);
		}
		return  Zend_Json::encode($stages);
	}
	
	/**
	 * 对于含有流程的数据的操作状态的判断
	 * @author young.yang 2015-1-3
	 * @param unknown $module
	 * @param unknown $record
	 * @return multitype:boolean string
	 */
	function getWorkflowsStatus($module,$record){
		$db=PearDatabase::getInstance();
		global $isallow;
		$isaction=false;
		$msg="不允许操作";
		if(in_array($module, $isallow)){
			$result=$db->pquery("select * from vtiger_salesorderworkflowstages where (salesorderid=? and modulename=?) ",array($record,$module));
			if($db->num_rows($result)){
				//将所有的状态统一读取出来
				$isaction_1=0;
				$isaction_2=0;
				$isaction_0=0;
				while($row=$db->fetch_array($result)){
					if($row['isaction']==2){
						$isaction_2++;continue;
					}
					if($row['isaction']==1){
						$isaction_1++;continue;
					}else{
						$isaction_0++;
					}
				}
				if($isaction_2==$db->num_rows($result)){
					$msg="审核完成，不允许再操作";
				}elseif($isaction_2==0){
					$isaction=true;
					$msg="未有审核通过的节点允许操作";
				}else{
					$msg="当前数据处于锁定状态不允许操作";
				}
			}else{
                $isaction=true;
            }
		}
		return array('success'=>$isaction,'result'=>$msg);
	}
	/**
	 * 通用的审核页面进入详细的验证，
	 * @param unknown $module
	 * @param unknown $record
	 */
	function getPermission($module,$record,$id=0){
		$db=PearDatabase::getInstance();
		if($id){
			$result=$db->pquery("select workflowstagesid from vtiger_salesorderworkflowstages where salesorderworkflowstagesid=?",array($id));
		}else{
			$result=$db->pquery("select workflowstagesid from vtiger_salesorderworkflowstages where (salesorderid=? and modulename=? and isaction=1)",array($record,$module));
		}
		if($db->num_rows($result)){
			$workflowstagesid=$db->query_result($result, 0,'workflowstagesid');
			$result1=$db->pquery("select * from vtiger_workflowstages where workflowstagesid=? limit 1",array($workflowstagesid));
			if($db->num_rows($result1)){
				$isrole=$db->query_result($result1,0,'isrole');
				$isrole=explode(' |##| ', $isrole);
				$user=Users_Privileges_Model::getCurrentUserPrivilegesModel();
				$userRole=$user->getRole();
				if(in_array($userRole, $isrole)){
					return true;
				}
			}else {
				return false;
			}
		}
		return false;
	}
    public static function getSalesorderSql($where){
        global $current_user;//审核人，包括上下级关系
        $sql=' EXISTS(select salesorderid from vtiger_salesorderworkflowstages where vtiger_crmentity.crmid=vtiger_salesorderworkflowstages.salesorderid   and modulename=\'SalesOrder\' and isvalidity=0 and auditorid '.$where.') ';
        //产品负责人中未审核的
        $sql .= " OR EXISTS(select salesorderid from vtiger_salesorderworkflowstages where vtiger_crmentity.crmid=vtiger_salesorderworkflowstages.salesorderid and isvalidity=0 and isaction=2 and productid>0 and productid in(select productid from vtiger_products where find_in_set(".$current_user->id.",replace(productman,' |##| ',','))  ))";
        return $sql;
    }
}