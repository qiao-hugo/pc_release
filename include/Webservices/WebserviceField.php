<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/
require_once 'includes/runtime/Cache.php';

class WebserviceField{
	private $fieldId;
	private $uitype;
	private $blockId;
	private $blockName;
	private $nullable;
	private $default;
	private $tableName;
	private $columnName;
	private $fieldName;
	private $fieldLabel;
	private $editable;
	private $fieldType;
	private $displayType;
	private $mandatory;
	private $massEditable;
	private $tabid;
	private $presence;
	/**
	 *
	 * @var PearDatabase
	 */
	private $pearDB;
	private $typeOfData;
	private $fieldDataType;
	private $dataFromMeta;
	private static $tableMeta = array();
	private static $fieldTypeMapping = array();
	private $referenceList;
	private $defaultValuePresent;
	private $explicitDefaultValue;
	
	private $genericUIType = 10;//通用uitype

	private $readOnly = 0;
	private $multiple=0;
	
	private function __construct($adb,$row){
		$this->uitype = $row['uitype'];
		$this->blockId = $row['block'];
		$this->blockName = null;
		$this->tableName = $row['tablename'];	//
		$this->columnName = $row['columnname'];
		$this->fieldName = $row['fieldname'];
		$this->fieldLabel = $row['fieldlabel'];
		$this->displayType = $row['displaytype'];
		$this->massEditable = ($row['masseditable'] === '1')? true: false;
		$typeOfData = $row['typeofdata'];
		$this->presence = $row['presence'];
		$this->typeOfData = $typeOfData;
		$typeOfData = explode("~",$typeOfData);
		$this->mandatory = ($typeOfData[1] == 'M')? true: false;
		if($this->uitype == 4){
			$this->mandatory = false;
		}
		$this->fieldType = $typeOfData[0];
		$this->tabid = $row['tabid'];
		$this->fieldId = $row['fieldid'];
		
		//$this->multiple = $row['multiple'];
		$this->multiple = !empty($row['ismultiple'])?$row['ismultiple']:(!empty($row['multiple'])?$row['multiple']:'');
		$this->pearDB = $adb;
		$this->fieldDataType = null;
		$this->dataFromMeta = false;
		$this->defaultValuePresent = false;
		$this->referenceList = null;
		$this->explicitDefaultValue = false;
		
		$this->readOnly = (isset($row['readonly']))? $row['readonly'] : 0;

		if(array_key_exists('defaultvalue', $row)) {
			$this->setDefault($row['defaultvalue']);
		}
	}
	
	public static function fromQueryResult($adb,$result,$rowNumber){
		 return new WebserviceField($adb,$adb->query_result_rowdata($result,$rowNumber));
	}
	
	public static function fromArray($adb,$row){
		return new WebserviceField($adb,$row);
	}
	
	public function getTableName(){
		return $this->tableName;
	}
	
	public function getFieldName(){
		return $this->fieldName;
	}
	
	public function getFieldLabelKey(){
		return $this->fieldLabel;
	}
	
	public function getFieldType(){
		return $this->fieldType;
	}
	
	public function isMandatory(){
		return $this->mandatory;
	}
	
	public function getTypeOfData(){
		return $this->typeOfData;
	}
	
	public function getDisplayType(){
		return $this->displayType;
	}
	
	public function getMassEditable(){
		return $this->massEditable;
	}
	
	public function getFieldId(){
		return $this->fieldId;
	}
	
	public function getDefault(){
		if($this->dataFromMeta !== true && $this->explicitDefaultValue !== true){
			$this->fillColumnMeta();
		}
		return $this->default;
	}
	
	public function getColumnName(){
		return $this->columnName;
	}
	
	public function getBlockId(){
		return $this->blockId;
	}
	
	public function getBlockName(){
		if(empty($this->blockName)) {
			$this->blockName = getBlockName($this->blockId);
		}
		return $this->blockName;
	}

	public function getTabId(){
		return $this->tabid;
	}

	public function isNullable(){
		if($this->dataFromMeta !== true){
			$this->fillColumnMeta();
		}
		return $this->nullable;
	}
	
	public function hasDefault(){
		if($this->dataFromMeta !== true && $this->explicitDefaultValue !== true){
			$this->fillColumnMeta();
		}
		return $this->defaultValuePresent;
	}
	
	public function getUIType(){
		return $this->uitype;
	}

	public function isReadOnly() {
		if($this->readOnly == 1) return true;
		return false;
	}
	
	private function setNullable($nullable){
		$this->nullable = $nullable;
	}
	
	public function setDefault($value){
		$this->default = $value;
		$this->explicitDefaultValue = true;
		$this->defaultValuePresent = true;
	}
	
	public function setFieldDataType($dataType){
		$this->fieldDataType = $dataType;
	}
	
	public function setReferenceList($referenceList){
		$this->referenceList = $referenceList;
	}
	
	public function getTableFields(){
		$tableFields = null;
		if(isset(WebserviceField::$tableMeta[$this->getTableName()])){
			$tableFields = WebserviceField::$tableMeta[$this->getTableName()];
		}else{
			$dbMetaColumns = $this->pearDB->database->MetaColumns($this->getTableName());
			

			$tableFields = array();
			foreach ($dbMetaColumns as $key => $dbField) {
				$tableFields[$dbField->name] = $dbField;
			}
			WebserviceField::$tableMeta[$this->getTableName()] = $tableFields;
		}
		return $tableFields;
	}
	public function fillColumnMeta(){
		$tableFields = $this->getTableFields();
		foreach ($tableFields as $fieldName => $dbField) {
			if(strcmp($fieldName,$this->getColumnName())===0){
				$this->setNullable(!$dbField->not_null);
				if($dbField->has_default === true && !$this->explicitDefaultValue){
					$this->defaultValuePresent = $dbField->has_default;
					$this->setDefault($dbField->default_value);
				}
			}
		}
		$this->dataFromMeta = true;
	}
	
	public function getFieldDataType(){
		if($this->fieldDataType === null){
			$fieldDataType = $this->getFieldTypeFromUIType();
			if($fieldDataType === null){
				$fieldDataType = $this->getFieldTypeFromTypeOfData();
			}
			if($fieldDataType == 'date' || $fieldDataType == 'datetime' || $fieldDataType == 'time') {
				$tableFieldDataType = $this->getFieldTypeFromTable();
				if($tableFieldDataType == 'datetime'){
					$fieldDataType = $tableFieldDataType;
				}
			}
			$this->fieldDataType = $fieldDataType;
		}
		return $this->fieldDataType;
	}
	/**
	 * 根据权限判断此用户可访问哪些module，目前module下多少类型为reference，哪些类型是可访问的。
	 * @return array 可访问的reference类型
	 */
	public function getReferenceList(){
        $this->pearDB = PearDatabase::getInstance();
		static $referenceList = array();
		if($this->referenceList === null){
			if(isset($referenceList[$this->getFieldId()])){
				$this->referenceList = $referenceList[$this->getFieldId()];
				return $referenceList[$this->getFieldId()];
			}
			if(!isset(WebserviceField::$fieldTypeMapping[$this->getUIType()])){
				$this->getFieldTypeFromUIType();//设置uitype
			}
			$fieldTypeData = WebserviceField::$fieldTypeMapping[$this->getUIType()];//获得
			$referenceTypes = array();



			if($this->getUIType() != $this->genericUIType){
				$sql = "select * from vtiger_ws_referencetype where fieldtypeid=?";//获得
                $params = array($fieldTypeData['fieldtypeid']);
			}else{
				$sql = 'select relmodule as type from vtiger_fieldmodulerel where fieldid=?';//通用的字段10
				$params = array($this->getFieldId());
			}

			$result = $this->pearDB->pquery($sql,$params);
			$numRows = $this->pearDB->num_rows($result);
			for($i=0;$i<$numRows;++$i){
				array_push($referenceTypes,$this->pearDB->query_result($result,$i,"type"));
			}
			
			//to handle hardcoding done for Calendar module todo activities.
			
			if($this->tabid == 9 && $this->fieldName =='parent_id'){
				$referenceTypes[] = 'Invoice';
				$referenceTypes[] = 'Quotes';
				$referenceTypes[] = 'PurchaseOrder';
				$referenceTypes[] = 'SalesOrder';
				$referenceTypes[] = 'Campaigns';
			}
			
			global $current_user;
//			$types = vtws_listtypes(null, $current_user);
//
//
//			$accessibleTypes = $types['types'];
//			if(!is_admin($current_user) && !in_array('Users',$accessibleTypes)) {
//				array_push($accessibleTypes, 'Users');
//			}
			$referenceTypes = array_values($referenceTypes);//先判断可访问的module，然后reference取交集，获得其值
			$referenceList[$this->getFieldId()] = $referenceTypes;
			$this->referenceList = $referenceTypes;
			return $referenceTypes;
		}
		return $this->referenceList;
	}
	
	private function getFieldTypeFromTable(){
		$tableFields = $this->getTableFields();
		foreach ($tableFields as $fieldName => $dbField) {
			if(strcmp($fieldName,$this->getColumnName())===0){
				return $dbField->type;
			}
		}
		//This should not be returned if entries in DB are correct.
		return null;
	}
	/**
	 * 字段类型，最后一位字母的代表
	 * @return string
	 */
	private function getFieldTypeFromTypeOfData(){
		switch($this->fieldType){
			case 'T': return "time";
			case 'D':
			case 'DT': return "date";
			case 'E': return "email";
			case 'N':
			case 'NN': return "double";
			case 'P': return "password";
			case 'I': return "integer";
			case 'V':
			default: return "string";
		}
	}
	/**
	 * 获得字段类型，
	 * @return NULL|unknown
	 */
	private function getFieldTypeFromUIType(){
        //$this->pearDB = PearDatabase::getInstance();
		// Cache all the information for futher re-use
		//缓存字段uitype表
        self::$fieldTypeMapping=Vtiger_Cache::get('global','getFieldTypeFromUIType');
		if(empty(self::$fieldTypeMapping)) {
			$result = $this->pearDB->pquery("select * from vtiger_ws_fieldtype", array());

			while($resultrow = $this->pearDB->fetch_array($result)) {
				self::$fieldTypeMapping[$resultrow['uitype']] = $resultrow;
			}
            Vtiger_Cache::set('global','getFieldTypeFromUIType',self::$fieldTypeMapping);
		}
		//查找
		if(isset(WebserviceField::$fieldTypeMapping[$this->getUIType()])){
			if(WebserviceField::$fieldTypeMapping[$this->getUIType()] === false){
				return null;
			}
			$row = WebserviceField::$fieldTypeMapping[$this->getUIType()];
			return $row['fieldtype'];
		} else {
			WebserviceField::$fieldTypeMapping[$this->getUIType()] = false;
			return null;
		}
	}
	
	function getPicklistDetails(){
		 $cache = Vtiger_Cache::get('global','getPicklistDetails'.$this->getTabId().$this->getFieldName());
        if(!empty($cache)) {
            return $cache;
        }else {
		$hardCodedPickListNames = array("hdntaxtype","email_flag");
		$hardCodedPickListValues = array(
				"hdntaxtype"=>array(
					array("label"=>"Individual","value"=>"individual"),
					array("label"=>"Group","value"=>"group")
				),
				"email_flag" => array(
					array('label'=>'SAVED','value'=>'SAVED'),
					array('label'=>'SENT','value' => 'SENT'),
					array('label'=>'MAILSCANNER','value' => 'MAILSCANNER')
				)
			);
		if(in_array(strtolower($this->getFieldName()),$hardCodedPickListNames)){
			return $hardCodedPickListValues[strtolower($this->getFieldName())];
		}
			$picklistDetails = $this->getPickListOptions($this->getFieldName());
			//$cache->setPicklistDetails($this->getTabId(),$this->getFieldName(),$picklistDetails);
            Vtiger_Cache::set('global','getPicklistDetails'.$this->getTabId().$this->getFieldName(),$picklistDetails);
			return $picklistDetails;
		}
	}

	function getPickListOptions(){
		$fieldName = $this->getFieldName();
        $this->pearDB = PearDatabase::getInstance();
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		$options = array();
		$sql = "select * from vtiger_picklist where name=?";
		$result = $this->pearDB->pquery($sql,array($fieldName));
		$numRows = $this->pearDB->num_rows($result);
		if($numRows == 0){
			$sql = "select * from vtiger_$fieldName";
			$result = $this->pearDB->pquery($sql,array());
			$numRows = $this->pearDB->num_rows($result);
			for($i=0;$i<$numRows;++$i){
				$elem = array();
				$picklistValue = $this->pearDB->query_result($result,$i,$fieldName);
				$picklistValue = decode_html($picklistValue);
				$moduleName = getTabModuleName($this->getTabId());
				if($moduleName == 'Events') $moduleName = 'Calendar';
				$elem["label"] = getTranslatedString($picklistValue,$moduleName);
				$elem["value"] = $picklistValue;
				array_push($options,$elem);
			}
		}else{
			$user = VTWS_PreserveGlobal::getGlobal('current_user');
			$details = getPickListValues($fieldName,$user->roleid);
			for($i=0;$i<sizeof($details);++$i){
				$elem = array();
				$picklistValue = decode_html($details[$i]);
				$moduleName = getTabModuleName($this->getTabId());
				if($moduleName == 'Events') $moduleName = 'Calendar';
				$elem["label"] = getTranslatedString($picklistValue,$moduleName);
				$elem["value"] = $picklistValue;
				array_push($options,$elem);
			}
		}
		return $options;
	}

	function getPresence() {
		return $this->presence;
	}

}

?>
