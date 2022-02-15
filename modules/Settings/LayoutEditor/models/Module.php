<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_LayoutEditor_Module_Model extends Vtiger_Module_Model {

    public static $supportedModules = false;

    /**
	 * Function that returns all the fields for the module
	 * @return <Array of Vtiger_Field_Model> - list of field models
	 */
	public function getFields() {
		if(empty($this->fields)){
			$fieldList = array();
			$blocks = $this->getBlocks();
            $blockId = array();
			foreach ($blocks as $block) {
                //to skip events hardcoded block id
                if($block->get('id') == 'EVENT_INVITE_USER_BLOCK_ID') {
                    continue;
                }
				$blockId[] = $block->get('id');
			}
            if(count($blockId) > 0) {
                $fieldList = Settings_LayoutEditor_Field_Model::getInstanceFromBlockIdList($blockId,$moduleModel);
            }
            //To handle special case for invite users
            if($this->getName() == 'Events') {
                $blockModel = new Settings_LayoutEditor_Block_Model();
                $blockModel->set('id','EVENT_INVITE_USER_BLOCK_ID');
                $blockModel->set('label','LBL_INVITE_USER_BLOCK');
                $blockModel->set('module', $this);

                $fieldModel = new Settings_LayoutEditor_Field_Model();
                $fieldModel->set('name','selectedusers');
                $fieldModel->set('label','LBL_INVITE_USERS');
                $fieldModel->set('block',$blockModel);
                $fieldModel->setModule($this);
                $fieldList[] = $fieldModel;
            }
            $this->fields = $fieldList;
		}
		return $this->fields;
	}

    /**
	 * Function returns all the blocks for the module
	 * @return <Array of Vtiger_Block_Model> - list of block models
	 */
	public function getBlocks() {
		if(empty($this->blocks)) {
			$blocksList = array();
			$moduleBlocks = Settings_LayoutEditor_Block_Model::getAllForModule($this);
			foreach($moduleBlocks as $block){
				if(!$block->get('label')) {
					continue;
				}
                if($this->getName() == 'HelpDesk' && $block->get('label') == 'LBL_COMMENTS'){
                    continue;
                }

				if($block->get('label') != 'LBL_ITEM_DETAILS') {
					$blocksList[$block->get('label')] = $block;
				}
			}
            //To handle special case for invite users block
            if($this->getName() == 'Events') {
                $blockModel = new Settings_LayoutEditor_Block_Model();
                $blockModel->set('id','EVENT_INVITE_USER_BLOCK_ID');
                $blockModel->set('label','LBL_INVITE_USER_BLOCK');
                $blockModel->set('module', $this);
                $blocksList['LBL_INVITE_USER_BLOCK'] = $blockModel;
            }
			$this->blocks = $blocksList;
		}
		return $this->blocks;
	}

    public function getAddSupportedFieldTypes() {
        return array(
            'Text','Decimal','Integer','Percent','Currency','Date','Email','Phone','Picklist',
            'URL','Checkbox','TextArea','MultiSelectCombo','Skype','Time'
        );
    }

    /**
     * Function whcih will give information about the field types that are supported for add
     * @return <Array>
     */
    public function getAddFieldTypeInfo() {
        $fieldTypesInfo = array();
        $addFieldSupportedTypes = $this->getAddSupportedFieldTypes();
        $lengthSupportedFieldTypes = array('Text','Decimal','Integer','Currency');
        foreach($addFieldSupportedTypes as $fieldType) {
            $details = array();
            if(in_array($fieldType,$lengthSupportedFieldTypes)) {
                $details['lengthsupported'] = true;
            }
            if($fieldType == 'Decimal' || $fieldType == 'Currency') {
                $details['decimalSupported']  = true;
                $details['maxFloatingDigits'] = 5;
                if($fieldType == 'Currency') {
                    $details['decimalReadonly'] = true;
                }
                //including mantisaa and integer part
                $details['maxLength'] = 64;
            }
            if($fieldType == 'Picklist' || $fieldType == 'MultiSelectCombo') {
                $details['preDefinedValueExists'] = true;
                //text area value type , can give multiple values
                $details['preDefinedValueType'] = 'text';
                if($fieldType == 'Picklist')
                    $details['picklistoption'] = true;
            }
            $fieldTypesInfo[$fieldType] = $details;
        }
        return $fieldTypesInfo;
    }

    //新增字段
    public function addField($fieldType, $blockId, $params) {

        $db = PearDatabase::getInstance();

        $label = $params['fieldLabel'];
        if($this->checkFIeldExists($label)){
            throw new Exception(vtranslate('LBL_DUPLICATE_FIELD_EXISTS', 'Settings::LayoutEditor'), 513);
        }
        $supportedFieldTypes = $this->getAddSupportedFieldTypes();
        if(!in_array($fieldType, $supportedFieldTypes)) {
            throw new Exception(vtranslate('LBL_WRONG_FIELD_TYPE', 'Settings::LayoutEditor'), 513);
        }

        $max_fieldid = $db->getUniqueID("vtiger_field");
		$columnName = 'cf_'.$max_fieldid;
		$custfld_fieldid = $max_fieldid;
        $moduleName = $this->getName();

        $focus = CRMEntity::getInstance($moduleName);
        if (isset($focus->customFieldTable)) {
            $tableName=$focus->customFieldTable[0];
        } else {
            $tableName= 'vtiger_'.strtolower($moduleName).'cf';
        }

        $details = $this->getTypeDetailsForAddField($fieldType, $params);
        $uitype = $details['uitype'];
        $typeofdata = $details['typeofdata'];
        $dbType = $details['dbType'];

        $quickCreate = in_array($moduleName,  getInventoryModules()) ? 3 : 1;

        $fieldModel = new Settings_LayoutEditor_Field_Model();
        $fieldModel->set('name', $columnName)
                   ->set('table', $tableName)
                   ->set('generatedtype',2)
                   ->set('uitype', $uitype)
                   ->set('label', $label)
                   ->set('typeofdata',$typeofdata)
                   ->set('quickcreate',$quickCreate)
                   ->set('columntype', $dbType);

        $blockModel = Vtiger_Block_Model::getInstance($blockId, $this);
        $blockModel->addField($fieldModel);

        if($fieldType == 'Picklist' || $fieldType == 'MultiSelectCombo') {
            $pickListValues = explode(',',$params['pickListValues']);
            $fieldModel->setPicklistValues($pickListValues);
        }
        return $fieldModel;
    }

    public function getTypeDetailsForAddField($fieldType,$params) {
        switch ($fieldType) {
                Case 'Text' :	//文本
                               $fieldLength = $params['fieldLength'];
                               $uichekdata='V~O~LE~'.$fieldLength;
                               $uitype = 1;
                               $type = "VARCHAR(".$fieldLength.") default ''"; // adodb type
                               break;
                Case 'Decimal' :	//小数
                               $fieldLength = $params['fieldLength'];
                               $decimal = $params['decimal'];
                               $uitype = 7;
                               //this may sound ridiculous passing decimal but that is the way adodb wants
                               $dbfldlength = $fieldLength + $decimal + 1;
                               $type="NUMERIC(".$dbfldlength.",".$decimal.")";	// adodb type
                               // Fix for http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/6363
                               $uichekdata='NN~O';
                               break;
               Case 'Percent' :		//百分比
                               $uitype = 9;
                               $type="NUMERIC(5,2)"; //adodb type
                               $uichekdata='N~O~2~2';
                               break;
               Case 'Currency' :	//货币
                               $fieldLength = $params['fieldLength'];
                               $decimal = $params['decimal'];
                               $uitype = 71;
                               $dbfldlength = $fieldLength + $decimal + 1;
                               $decimal = $decimal + 3;
                               $type="NUMERIC(".$dbfldlength.",".$decimal.")"; //adodb type
                               $uichekdata='N~O';
                               break;
               Case 'Date' :	//日期
                               $uichekdata='D~O';
                               $uitype = 5;
                               $type = "DATE"; // adodb type
                               break;
               Case 'Email' :	//Email
                               $uitype = 13;
                               $type = "VARCHAR(50) default '' "; //adodb type
                               $uichekdata='E~O';
                               break;
               Case 'Time' :	//时间
                               $uitype = 14;
                               $type = "TIME";
                               $uichekdata='T~O';
                               break;
               Case 'Phone' :	//电话号码
                               $uitype = 11;
                               $type = "VARCHAR(30) default '' "; //adodb type
                               $uichekdata='V~O';
                               break;
               Case 'Picklist' :	//Picklist
                               $uitype = 16;
                               if(!empty($params['isRoleBasedPickList']))
                                    $uitype = 15;
                               $type = "VARCHAR(255) default '' "; //adodb type
                               $uichekdata='V~O';
                               break;
               Case 'URL' :		//链接
                               $uitype = 17;
                               $type = "VARCHAR(255) default '' "; //adodb type
                               $uichekdata='V~O';
                               break;
               Case 'Checkbox' :	//复选框
                               $uitype = 56;
                               $type = "VARCHAR(3) default 0"; //adodb type
                               $uichekdata='C~O';
                               break;
               Case 'TextArea' :	//文本区域
                               $uitype = 21;
                               $type = "TEXT"; //adodb type
                               $uichekdata='V~O';
                               break;
               Case 'MultiSelectCombo' :	//多选组合框
                               $uitype = 33;
                               $type = "TEXT"; //adodb type
                               $uichekdata='V~O';
                               break;
               Case 'Skype' :
                               $uitype = 85;
                               $type = "VARCHAR(255) default '' "; //adodb type
                               $uichekdata='V~O';
                               break;
               Case 'Integer' :		//整数
                               $fieldLength = $params['fieldLength'];
                               $uitype = 7;
							   if ($fieldLength > 10) {
                                    $type = "BIGINT(".$fieldLength.")"; //adodb type
							   } else {
                                    $type = "INTEGER(".$fieldLength.")"; //adodb type
							   }
                               $uichekdata='I~O';
                               break;
        }
        return array(
            'uitype' => $uitype,
            'typeofdata' => $uichekdata,
            'dbType' => $type,
        );

    }

    public function checkFIeldExists($fieldLabel) {
        $db = PearDatabase::getInstance();
        $tabId = array($this->getId());
        if($this->getName() == 'Calendar' || $this->getName() == 'Events') {
            //Check for fiel exists in both calendar and events module
            $tabId = array('9','16');
        }
        $query = 'SELECT 1 FROM vtiger_field WHERE tabid IN ('.  generateQuestionMarks($tabId).') AND fieldlabel=?';
        $result = $db->pquery($query, array($tabId,$fieldLabel));
        return ($db->num_rows($result) > 0 ) ? true : false;
    }

    public static function getSupportedModules() {
        if(empty(self::$supportedModules)) {
           self::$supportedModules = self::getEntityModulesList();
        }
        return self::$supportedModules;
    }


    public static function getInstanceByName($moduleName) {
        $moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
        $objectProperties = get_object_vars($moduleInstance);
		$selfInstance = new self();
		foreach($objectProperties as $properName=>$propertyValue){
			$selfInstance->$properName = $propertyValue;
		}
		return $selfInstance;
	}

	/**
	 * Function to get Entity module names list
	 * @return <Array> List of Entity modules
	 */
	public static function getEntityModulesList() {
		$db = PearDatabase::getInstance();
		self::preModuleInitialize2();   //缓存实体vtiger_entityname

		$presence = array(0, 2);		//@TODO 为什么默认为0和2
		$restrictedModules = array('Webmails', 'SMSNotifier', 'Emails', 'Integration', 'Dashboard', 'ModComments', 'PBXManager', 'vtmessages', 'vttwitter');
		
		// 生成传递参数的格式？，
		$query = 'SELECT name FROM vtiger_tab WHERE
						presence IN ('. generateQuestionMarks($presence) .')
						AND isentitytype = ?
						AND name NOT IN ('. generateQuestionMarks($restrictedModules) .')';
		$result = $db->pquery($query, array($presence, 1, $restrictedModules));
		$numOfRows = $db->num_rows($result);

		$modulesList = array();
		for($i=0; $i<$numOfRows; $i++) {
			$moduleName = $db->query_result($result, $i, 'name');
			$modulesList[$moduleName] = $moduleName;
		}
		return $modulesList;
	}

	/**
	 * Function to check field is editable or not
	 * @return <Boolean> true/false
	 */
	public function isSortableAllowed() {
		$moduleName = $this->getName();
		if (in_array($moduleName, array('Calendar', 'Events'))) {
			return false;
		}
		return true;
	}

	/**
	 * Function to check blocks are sortable for the module
	 * @return <Boolean> true/false
	 */
	public function isBlockSortableAllowed() {
		$moduleName = $this->getName();
		if (in_array($moduleName, array('Calendar', 'Events'))) {
			return false;
		}
		return true;
	}

	/**
	 * Function to check fields are sortable for the block
	 * @return <Boolean> true/false
	 */
	public function isFieldsSortableAllowed($blockName) {
		$moduleName = $this->getName();
		$blocksEliminatedArray = array('HelpDesk' => array('LBL_TICKET_RESOLUTION', 'LBL_COMMENTS'),
										'Faq' => array('LBL_COMMENT_INFORMATION'),
										'Calendar' => array('LBL_TASK_INFORMATION', 'LBL_DESCRIPTION_INFORMATION'),
										'Events' => array('LBL_EVENT_INFORMATION', 'LBL_REMINDER_INFORMATION', 'LBL_RECURRENCE_INFORMATION', 'LBL_RELATED_TO', 'LBL_DESCRIPTION_INFORMATION', 'LBL_INVITE_USER_BLOCK'));
		if (in_array($moduleName, array('Calendar', 'Events', 'HelpDesk', 'Faq'))) {
			if(!empty($blocksEliminatedArray[$moduleName])) {
				if(in_array($blockName, $blocksEliminatedArray[$moduleName])) {
					return false;
				}
			} else {
				return false;
			}
		}
		return true;
	}

	public function getRelations() {
		if($this->relations === null) {
			$this->relations = Vtiger_Relation_Model::getAllRelations($this, false);
		}

		// Contacts relation-tab is turned into custom block on DetailView.
		if ($this->getName() == 'Calendar') {
			$contactsIndex = false;
			foreach ($this->relations as $index => $model) {
				if ($model->getRelationModuleName() == 'Contacts') {
					$contactsIndex = $index;
					break;
				}
			}
			if ($contactsIndex !== false) {
				array_splice($this->relations, $contactsIndex, 1);
			}
		}

		return $this->relations;
	}
}