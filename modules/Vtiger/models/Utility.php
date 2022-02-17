<?php
/**
 * 普通按钮是否可用
 */
class Vtiger_Utility_Model extends Vtiger_Action_Model {
	static $permission;
	public function isUtilityTool() {
		return true;
	}

	public function isModuleEnabled($module) {
		$db = PearDatabase::getInstance();
		if(!$module->isEntityModule()) {
			return false;
		}
		$tabId = $module->getId();
		$permission=self::$permission;
		if(empty($permission)){
			$result = $db->pquery("SELECT CONCAT(tabid,'-',activityid) as ta FROM vtiger_profile2utility  GROUP BY tabid,activityid");
			while($row=$db->fetch_array($result)){
				$permission[$row['ta']]=0;
			}
			self::$permission=$permission;	
		}
		$key=$tabId.'-'.$this->getId();
		if(isset($permission[$key])){
			return true;
		}else{
			return false;
		}
		$sql = 'SELECT 1 FROM vtiger_profile2utility WHERE tabid = ? AND activityid = ? LIMIT 1';
		//SELECT CONCAT(tabid,'-',activityid) as ta FROM vtiger_profile2utility  GROUP BY tabid,activityid
		$params = array($tabId, $this->getId());
		$result = $db->pquery($sql, $params);
		if($result && $db->num_rows($result) > 0) {
			return true;
		}
		return false;
	}

}