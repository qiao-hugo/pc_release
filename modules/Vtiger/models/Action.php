<?php
/*+*******
 * 页面按钮控制
 * 业务层按钮新增需要加入vtiger_actionmapping和vtiger_profile2utility表和下面的配置
 ********/

/**
 * Vtiger Action Model Class
 */
class Vtiger_Action_Model extends Vtiger_Base_Model {

	static $standardActions = array('0' => 'Save','1' => 'EditView','2' => 'Delete','3' => 'index','4' => 'DetailView','11'=>'Popup');
	static $nonConfigurableActions = array('Save', 'index', 'SavePriceBook', 'SaveVendor',
											'DetailViewAjax', 'PriceBookEditView', 'QuickCreate', 'VendorEditView',
											'DeletePriceBook', 'DeleteVendor', 'PriceBookDetailView',
											'TagCloud', 'VendorDetailView');
	static $utilityActions = array('5' => 'Import', '6' => 'Export', '8' => 'Merge', '9' => 'ConvertLead', '10' => 'DuplicatesHandling','12'=>'Protect','13'=>'ToVoid','14'=>'ReturnTicket','15'=>'ListBtnADD','16'=>'ListBtnEDIT','17'=>'NegativeEdit','18'=>'ConvertLead','20'=>'IdcEDIT');
	
	
	static $authlist=array('LBL_VIEWDATA_NONE','LBL_VIEWDATA_OWNER','LBL_VIEWDATA_SUBOR','LBL_VIEWDATA_RELATE','LBL_VIEWDATA_DEPART','LBL_VIEWDATA_SYS','LBL_VIEWDATA_ALL','LBL_VIEWDATA_COMPANY'/*,'LBL_VIEWDATA_MAIN_COMPANY'*/);

	public function getId() {
		return $this->get('actionid');
	}

	public function getName() {
		return $this->get('actionname');
	}

	public function isUtilityTool() {
		return false;
	}

	public function isModuleEnabled($module) {
		$db = PearDatabase::getInstance();
		if(!$module->isEntityModule()) {
			return false;
		}
		if(in_array($this->getName(), self::$standardActions)) {
			return true;
		}
		$tabId = $module->getId();
		$sql = 'SELECT 1 FROM vtiger_profile2standardpermissions WHERE tabid = ? AND operation = ? LIMIT 1';
		$params = array($tabId, $this->getId());
		$result = $db->pquery($sql, $params);
		if($result && $db->num_rows($result) > 0) {
			return true;
		}
		return false;
	}

	public static function getInstanceFromQResult($result, $rowNo=0) {
		$db = PearDatabase::getInstance();
		$row = $db->query_result_rowdata($result, $rowNo);
		$className = 'Vtiger_Action_Model';
		$actionName = $row['actionname'];
		if(!in_array($actionName, self::$standardActions)) {
			$className = 'Vtiger_Utility_Model';
		}
		$actionModel = new $className();
		return $actionModel->setData($row);
	}

	protected static $cachedInstances = NULL;
	public static function getInstance($value, $force=false) {
		if (!self::$cachedInstances || $force) {
			self::$cachedInstances = self::getAll();
		}
		if (self::$cachedInstances) {
			$actionid = Vtiger_Utils::isNumber($value) ? $value : false;
			foreach (self::$cachedInstances as $instance) {
				if($actionid !== false) {
					if ($instance->get('actionid') == $actionid) {
						return $instance;
					}
				} else {
					if ($instance->get('actionname') == $value) {
						return $instance;
					}
				}
			}
		}
		return null;
	}
	
	public static function getInstanceWithIdOrName($value) {
		$db = PearDatabase::getInstance();

		if(Vtiger_Utils::isNumber($value)) {
			$sql = 'SELECT * FROM vtiger_actionmapping WHERE actionid=? LIMIT 1';
		} else {
			$sql = 'SELECT * FROM vtiger_actionmapping WHERE actionname=?';
		}
		$params = array($value);
		$result = $db->pquery($sql, $params);
		if($db->num_rows($result) > 0) {
			return self::getInstanceFromQResult($result);
		}
		return null;
	}

	public static function getAll($configurable=false) {
		$actionModels = Vtiger_Cache::get('vtiger', 'actions');
        if(!$actionModels){
            $db = PearDatabase::getInstance();

            $sql = 'SELECT * FROM vtiger_actionmapping';
            $params = array();
            if($configurable) {
                $sql .= ' WHERE actionname NOT IN ('. generateQuestionMarks(self::$nonConfigurableActions) .')';
                array_push($params, self::$nonConfigurableActions);
            }
            $result = $db->pquery($sql, $params);
            $noOfRows = $db->num_rows($result);
            $actionModels = array();
            for($i=0; $i<$noOfRows; ++$i) {
                $actionModels[] = self::getInstanceFromQResult($result, $i);
            }
            Vtiger_Cache::set('vtiger','actions', $actionModels);
        }
		return $actionModels;
	}
	
	//获取方法列表
	public static function getAllBasic($configurable=false) {
		$db = PearDatabase::getInstance();

		$basicActionIds = array_keys(self::$standardActions);
		$sql = 'SELECT * FROM vtiger_actionmapping WHERE actionid IN ('. generateQuestionMarks($basicActionIds) .')';
		$params = $basicActionIds;
		if($configurable) {
			$sql .= ' AND actionname NOT IN ('. generateQuestionMarks(self::$nonConfigurableActions) .')';
			$params = array_merge($params, self::$nonConfigurableActions);
		}
		$result = $db->pquery($sql, $params);
		$noOfRows = $db->num_rows($result);
		$actionModels = array();
		for($i=0; $i<$noOfRows; ++$i) {
			//print_r($db->query_result_rowdata($result, $i));
			$row = $db->query_result_rowdata($result, $i);
			$actionModels[$row['actionid']] = self::getInstanceFromQResult($result, $i);
		}
		
		//print_r($actionModels);
		//exit;
		return $actionModels;
	}

	public static function getAllUtility($configurable=false) {
		$db = PearDatabase::getInstance();

		$basicActionIds = array_keys(self::$standardActions);
		$sql = 'SELECT * FROM vtiger_actionmapping WHERE actionid NOT IN ('. generateQuestionMarks($basicActionIds) .')';
		$params = $basicActionIds;
		if($configurable) {
			$sql .= ' AND actionname NOT IN ('. generateQuestionMarks(self::$nonConfigurableActions) .')';
			$params = array_merge($params, self::$nonConfigurableActions);
		}
		$result = $db->pquery($sql, $params);
		$noOfRows = $db->num_rows($result);
		$actionModels = array();
		for($i=0; $i<$noOfRows; ++$i) {
			$actionModels[] = self::getInstanceFromQResult($result, $i);
		}
		return $actionModels;
	}

}