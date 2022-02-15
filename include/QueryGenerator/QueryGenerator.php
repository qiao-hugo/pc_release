<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/

require_once 'data/CRMEntity.php';
require_once 'modules/CustomView/CustomView.php';
require_once 'include/Webservices/Utils.php';
require_once 'include/Webservices/RelatedModuleMeta.php';

/**
 * Description of QueryGenerator
 *
 * @author MAK
 */
class QueryGenerator {
	private $module;    
	private $customViewColumnList;
	private $stdFilterList;
	private $conditionals;
	private $manyToManyRelatedModuleConditions;
	private $groupType;
	private $whereFields;
	/**
	 *
	 * @var VtigerCRMObjectMeta
	 */
	private $meta;
	/**
	 *
	 * @var Users
	 */
	private $user;
	private $advFilterList;
	private $fields;
	private $referenceModuleMetaInfo;
	private $moduleNameFields;
	private $referenceFieldInfoList;
	private $referenceFieldList;
	private $ownerFields;
	private $columns;
	private $fromClause;
	private $whereClause;
	private $query;
	private $groupInfo;
	public $conditionInstanceCount;
	private $conditionalWhere;
	public static $AND = 'AND';
	public static $OR = 'OR';
	private $customViewFields;
	/**
	 * Import Feature
	 */
	private $ignoreComma;
	public function __construct($module, $user) {
		
		$db = PearDatabase::getInstance();
		$this->module = $module;
		$this->customViewColumnList = null;  //这个字段竟然在其他地方都没有使用用到，只是在做视图的列表字段的时候作为过渡使用
		$this->stdFilterList = null;
		$this->conditionals = array();
		$this->user = $user;
		$this->advFilterList = null;
		$this->fields = array();
		$this->referenceModuleMetaInfo = array();
		$this->moduleNameFields = array();
		$this->whereFields = array();
		$this->groupType = self::$AND;
		$this->meta = $this->getMeta($module);
		$this->moduleNameFields[$module] = $this->meta->getNameFields();
		
		$this->referenceFieldInfoList = $this->meta->getReferenceFieldDetails();//返回reference详细array('字段名称'=>object)
		$this->referenceFieldList = array_keys($this->referenceFieldInfoList);//获得列表
		$this->ownerFields = $this->meta->getOwnerFields();

		$this->columns = null;
		$this->fromClause = null;
		$this->whereClause = null;
		$this->query = null;
		$this->conditionalWhere = null;
		$this->groupInfo = '';
		$this->manyToManyRelatedModuleConditions = array();
		$this->conditionInstanceCount = 0;
		$this->customViewFields = array();
		
	}

	/**
	 * 类似于初始化数据，读取的表vtiger_ws_entity中的数据并实例化
	 * VtigerModuleOperation 实例化->VtigerCRMObjectMeta
	 * @param String:ModuleName $module
	 * @return EntityMeta
	 */
	public function getMeta($module) {
		$db = PearDatabase::getInstance();//为何引用
		
		if (empty($this->referenceModuleMetaInfo[$module])) {
			$handler = vtws_getModuleHandlerFromName($module, $this->user);
			
			$meta = $handler->getMeta();
			
			
			$this->referenceModuleMetaInfo[$module] = $meta;
			$this->moduleNameFields[$module] = $meta->getNameFields();
		}
		
		return $this->referenceModuleMetaInfo[$module];
	}

	public function reset() {
		$this->fromClause = null;
		$this->whereClause = null;
		$this->columns = null;
		$this->query = null;
	}

	public function setFields($fields) {
		$this->fields = $fields;
	}

	public function getCustomViewFields() {
		return $this->customViewFields;
	}

	public function getFields() {
		return $this->fields;
	}

	public function getWhereFields() {
		return $this->whereFields;
	}

    public function addWhereField($fieldName) {
        $this->whereFields[] = $fieldName;
    }

	public function getOwnerFieldList() {
		return $this->ownerFields;
	}

	public function getModuleNameFields($module) {
		return $this->moduleNameFields[$module];
	}

	public function getReferenceFieldList() {
		return $this->referenceFieldList;
	}

	public function getReferenceFieldInfoList() {
		return $this->referenceFieldInfoList;
	}

	public function getModule () {
		return $this->module;
	}

	public function getConditionalWhere() {
		return $this->conditionalWhere;
	}

	public function getDefaultCustomViewQuery() {
		$customView = new CustomView($this->module);
		$viewId = $customView->getViewId($this->module);
		return $this->getCustomViewQueryById($viewId);
	}

	public function initForDefaultCustomView() {
		$customView = new CustomView($this->module);//只是获取了当前模型的id，
		$viewId = $customView->getViewId($this->module);
		
		
		$this->initForCustomViewById($viewId);
	}
	/**
	 * 初始化列表显示的字段,针对日历，活动文档的特殊的module的处理
	 * customview类，
	 * 增加列表排序功能
	 * @param unknown $viewId
	 */
	public function initForCustomViewById($moduleName) {
		$customView = new CustomView($this->module);
		$this->customViewColumnList = Vtiger_Cache::get('zdcrm_',$moduleName.'_fields');
		if(empty($this->customViewColumnList)){
			$moduleModel = Vtiger_Module_Model::getInstance($this->module);
			$Fields=$moduleModel->getListFields();
			global $all_crmtablename;
			if(!empty($Fields)){
                foreach ($Fields as  $field){
                    $tableName = $field['tablename'];
                    if(in_array($tableName,$all_crmtablename[$moduleName])){
                        $columnName = $field['columnname'];
                        $fieldName =$field['fieldname'];
                        $fieldLabel = $field['fieldlabel'];
                        $fieldTypeOfData = explode('~',$field['typeofdata']);
                        $fieldType = $fieldTypeOfData[0];
                        if($field['fieldtype'] == 'reference') {
                            $fieldType = 'V';
                        } else {
                            $fieldType = ChangeTypeOfData_Filter($tableName, $columnName, $fieldType);
                        }
                        $escapedFieldLabel = str_replace(' ', '_', $fieldLabel);
                        $moduleFieldLabel = $moduleName.'_'.$escapedFieldLabel;
                        $this->customViewColumnList[]=array($tableName,$columnName,$fieldName,$moduleFieldLabel,$fieldType);
                    }
                }
            }

			
			/*global $all_crmtablename;
			//$basetable=$moduleModel->get('basetable');
			$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);
			foreach ($recordStructureInstance->getStructure() as $field){
				foreach ($field  as $fieldmodel){
					$moduleName =$fieldmodel->getModuleName();
					$tableName = $fieldmodel->get('table');
					if(in_array($tableName,$all_crmtablename[$moduleName])){
						$columnName = $fieldmodel->get('column');
						$fieldName = $fieldmodel->get('name');
						$fieldLabel = $fieldmodel->get('label');
						$typeOfData = $fieldmodel->get('typeofdata');
						$fieldTypeOfData = explode('~', $typeOfData);
						$fieldType = $fieldTypeOfData[0];
						if($fieldmodel->getFieldDataType() == 'reference') {
							$fieldType = 'V';
						} else {
							$fieldType = ChangeTypeOfData_Filter($tableName, $columnName, $fieldType);
						}
						$escapedFieldLabel = str_replace(' ', '_', $fieldLabel);
						$moduleFieldLabel = $moduleName.'_'.$escapedFieldLabel;
						$this->customViewColumnList[]=array($tableName,$columnName,$fieldName,$moduleFieldLabel,$fieldType);
					}
				}
			}*/
			//Vtiger_Cache::set('zdcrm_',$moduleName.'_fields',$fields);
			Vtiger_Cache::set('zdcrm_',$moduleName.'_fields',$this->customViewColumnList);
		}
		
		
		//$customView->getColumnsListByCvid($viewId);//获取列表显示的列字段名称
		/* $moduleName = $request->get('source_module');
		 
		 */
		//$this->customViewColumnList;当时添加的字段信息
        if(!empty($this->customViewColumnList)){
            foreach ($this->customViewColumnList as $details) {
                if(empty($details[2]) && $details[1] == 'crmid' && $details[0] == 'vtiger_crmentity') {
                    $name = 'id';
                    $this->customViewFields[] = $name;
                } else {
                    $this->fields[] = $details[2];
                    $this->customViewFields[] = $details[2];
                }
            }
        }
		//针对日历的显示
		if($this->module == 'Calendar' && !in_array('activitytype', $this->fields)) {
			$this->fields[] = 'activitytype';
		}
		//针对文档
		if($this->module == 'Documents') {
			if(in_array('filename', $this->fields)) {
				if(!in_array('filelocationtype', $this->fields)) {
					$this->fields[] = 'filelocationtype';
				}
				if(!in_array('filestatus', $this->fields)) {
					$this->fields[] = 'filestatus';
				}
			}
		}
		$this->fields[] = 'id';
		$moduleName_viewid=$moduleName.'_viewid';
		global $$moduleName_viewid;    
		
		//echo $$moduleName_viewid;
		//exit;
		//$this->stdFilterList = $customView->getStdFilterByCvid($$moduleName_viewid);//标准搜索,目前没有数据，暂时忽略
		
		$this->advFilterList = $customView->getAdvFilterByCvid($$moduleName_viewid);
		
		
		
		//print_r($this->stdFilterList);
		//print_r($this->advFilterList);
		//根据标准搜索而来
		/*if(is_array($this->stdFilterList)) {
			$value = array();
			if(!empty($this->stdFilterList['columnname'])) {
				$this->startGroup('');
				$name = explode(':',$this->stdFilterList['columnname']);
				$name = $name[2];
				$value[] = $this->fixDateTimeValue($name, $this->stdFilterList['startdate']);
				$value[] = $this->fixDateTimeValue($name, $this->stdFilterList['enddate'], false);
				$this->addCondition($name, $value, 'BETWEEN');
			}
		}*/
		/**
		 * 1.只有高级搜索
		 * 2.包含标准，高级搜索
		 */
		if($this->conditionInstanceCount <= 0 && is_array($this->advFilterList) && count($this->advFilterList) > 0) {
			$this->startGroup('');
		} elseif($this->conditionInstanceCount > 0 && is_array($this->advFilterList) && count($this->advFilterList) > 0) {
			$this->addConditionGlue(self::$AND);
		}
		
        if(is_array($this->advFilterList) && count($this->advFilterList) > 0) {
			$this->parseAdvFilterList($this->advFilterList);
		}
		if($this->conditionInstanceCount > 0) {
			$this->endGroup();
		}

		
	}

	public function parseAdvFilterList($advFilterList, $glue=''){
		if(!empty($glue)) $this->addConditionGlue($glue);

		$customView = new CustomView($this->module);
		$dateSpecificConditions = $customView->getStdFilterConditions();
		foreach ($advFilterList as $groupindex=>$groupcolumns) {
			$filtercolumns = $groupcolumns['columns'];
			if(count($filtercolumns) > 0) {
				$this->startGroup('');
				foreach ($filtercolumns as $index=>$filter) {
					$nameComponents = explode(':',$filter['columnname']);
					if(empty($nameComponents[2]) && $nameComponents[1] == 'crmid' && $nameComponents[0] == 'vtiger_crmentity') {
						$name = $this->getSQLColumn('id');
					} else {
						$name = $nameComponents[2];
					}
					if(($nameComponents[4] == 'D' || $nameComponents[4] == 'DT') && in_array($filter['comparator'], $dateSpecificConditions)) {
						$filter['stdfilter'] = $filter['comparator'];
						$valueComponents = explode(',',$filter['value']);
						if($filter['comparator'] == 'custom') {
							if($nameComponents[4] == 'DT') {
								$startDateTimeComponents = explode(' ',$valueComponents[0]);
								$endDateTimeComponents = explode(' ',$valueComponents[1]);
								$filter['startdate'] = DateTimeField::convertToDBFormat($startDateTimeComponents[0]);
								$filter['enddate'] = DateTimeField::convertToDBFormat($endDateTimeComponents[0]);
							} else {
								$filter['startdate'] = DateTimeField::convertToDBFormat($valueComponents[0]);
								$filter['enddate'] = DateTimeField::convertToDBFormat($valueComponents[1]);
							}
						}
						$dateFilterResolvedList = $customView->resolveDateFilterValue($filter);
						$value[] = $this->fixDateTimeValue($name, $dateFilterResolvedList['startdate']);
						$value[] = $this->fixDateTimeValue($name, $dateFilterResolvedList['enddate'], false);
						$this->addCondition($name, $value, 'BETWEEN');
					} else if($nameComponents[4] == 'DT' && ($filter['comparator'] == 'e' || $filter['comparator'] == 'n')) {
						$filter['stdfilter'] = $filter['comparator'];
						$dateTimeComponents = explode(' ',$filter['value']);
						$filter['startdate'] = DateTimeField::convertToDBFormat($dateTimeComponents[0]);
						$filter['enddate'] = DateTimeField::convertToDBFormat($dateTimeComponents[0]);
						$dateTimeFilterResolvedList = $customView->resolveDateFilterValue($filter);
						$value[] = $this->fixDateTimeValue($name, $dateTimeFilterResolvedList['startdate']);
						$value[] = $this->fixDateTimeValue($name, $dateTimeFilterResolvedList['enddate'], false);
						if($filter['comparator'] == 'n') {
							$this->addCondition($name, $value, 'NOTEQUAL');
						} else {
							$this->addCondition($name, $value, 'BETWEEN');
						}
					} else if($nameComponents[4] == 'DT' && $filter['comparator'] == 'a') {
						$dateTime = explode(' ', $filter['value']);
						$value[] = $this->fixDateTimeValue($name, $dateTime[0], false);
						$this->addCondition($name, $value, $filter['comparator']);
					} else{
						$this->addCondition($name, $filter['value'], $filter['comparator']);
					}
					$columncondition = $filter['column_condition'];
					if(!empty($columncondition)) {
						$this->addConditionGlue($columncondition);
					}
				}
				$this->endGroup();
				$groupConditionGlue = $groupcolumns['condition'];
				if(!empty($groupConditionGlue))
					$this->addConditionGlue($groupConditionGlue);
			}
		}
	}

	public function getCustomViewQueryById($viewId) {
		$this->initForCustomViewById($viewId);
		return $this->getQuery();
	}

	public function getQuery() {
		if(empty($this->query)) {
			
			$conditionedReferenceFields = array();
			$allFields = array_merge($this->whereFields,$this->fields);//where和列字段合并
			
			foreach ($allFields as $fieldName) {
				if(in_array($fieldName,$this->referenceFieldList)) {
					$moduleList = $this->referenceFieldInfoList[$fieldName];
					foreach ($moduleList as $module) {
						if(empty($this->moduleNameFields[$module])) {
							$meta = $this->getMeta($module);
						}
					}
				} elseif(in_array($fieldName, $this->ownerFields )) {
					$meta = $this->getMeta('Users');
					$meta = $this->getMeta('Groups');
				}
			}

			$query = "SELECT ";
			$query .= $this->getSelectClauseColumnSQL();
			$query .= $this->getFromClause();
			$query .= $this->getWhereClause();
			
			//echo $query;die;
			$this->query = $query;    //列表页面的sql查询条件
			return $query;
		} else {
			return $this->query;
		}
	}

	public function getSQLColumn($name) {
		if ($name == 'id') {
			$baseTable = $this->meta->getEntityBaseTable();
			$moduleTableIndexList = $this->meta->getEntityTableIndexList();
			$baseTableIndex = $moduleTableIndexList[$baseTable];
			return $baseTable.'.'.$baseTableIndex;
		}
		$moduleFields = $this->meta->getModuleFields();
		$field = $moduleFields[$name];
		$sql = '';
		//TODO optimization to eliminate one more lookup of name, incase the field refers to only
		//one module or is of type owner.
		$column = $field->getColumnName();
		if($field->getTableName()=='vtiger_crmentity'){
			return '(select '.$column.' from vtiger_crmentity where vtiger_crmentity.crmid='.$this->meta->getEntityBaseTable().'.'.$this->meta->table_index.' and vtiger_crmentity.deleted=0) as '.$column;
		}
		
		return $field->getTableName().'.'.$column;
	}
	/**
	 * 
	 * @history
	 * 	2014-10-28 young 针对评论来做专门修改if($this->module=='ModComments')这一行为新增
	 * @return Ambigous <NULL, string>
	 */
	public function getSelectClauseColumnSQL(){
		$columns = array();
		$moduleFields = $this->meta->getModuleFields();
		$accessibleFieldList = array_keys($moduleFields);

		$accessibleFieldList[] = 'id';
		$this->fields = array_intersect($this->fields, $accessibleFieldList);
		//var_dump($this->fields);


		foreach ($this->fields as $field) {
			$sql = $this->getSQLColumn($field);
			$columns[] = $sql;

			//To merge date and time fields
			//echo $this->meta->getEntityName();
			if($this->meta->getEntityName() == 'Calendar' && ($field == 'date_start' || $field == 'due_date' || $field == 'taskstatus' || $field == 'eventstatus')) {
				if($field=='date_start') {
					$timeField = 'time_start';
					$sql = $this->getSQLColumn($timeField);
				} else if ($field == 'due_date') {
					$timeField = 'time_end';
					$sql = $this->getSQLColumn($timeField);
				} else if ($field == 'taskstatus' || $field == 'eventstatus') {
					//In calendar list view, Status value = Planned is not displaying
					$sql = "CASE WHEN (vtiger_activity.status not like '') THEN vtiger_activity.status ELSE vtiger_activity.eventstatus END AS ";
					if ( $field == 'taskstatus') {
						$sql .= "status";
					} else {
						$sql .= $field;
					}
				}
				$columns[] = $sql;
			}
		}
		$this->columns = implode(', ',$columns);
		
		/*
		if($this->module=='ModComments'){
			$this->columns.= ',modcommentsid';
		}*/

		return $this->columns.','.$this->meta->getEntityBaseTable().'.'.$this->meta->table_index;
	}
	// 在这里开始拼接语句了 from 之后的子句
	public function getFromClause() {
		global $current_user;
		if(!empty($this->query) || !empty($this->fromClause)) {
			return $this->fromClause;
		}
		$baseModule = $this->getModule();
		$moduleFields = $this->meta->getModuleFields();//获取的是modules
		
		
		
		$tableList = array();
		$tableJoinMapping = array();
		$tableJoinCondition = array();
		$i =1;
		
		$moduleTableIndexList = $this->meta->getEntityTableIndexList();//   $tab_name_index
		
		
		/**
		 * 列表中展示的字段，以及关联的表，因为列表中展示的数据可能是通过其他的表来的，
		 * 对于属性表的理解，可以用表名+属性表名来理解，属性表即下拉项之类
		 * @todo 规则需要完善
		 */
		
		foreach ($this->fields as $fieldName) {
			if ($fieldName == 'id') {
				continue;
			}

			$field = $moduleFields[$fieldName];    //array('field'=>array())
			$baseTable = $field->getTableName();   //字段所属的表
			
			$tableIndexList = $this->meta->getEntityTableIndexList();//获取关联表
			$baseTableIndex = $tableIndexList[$baseTable];
			
			//对于有关联表的处理方法，目前来说
			if($field->getFieldDataType() == 'reference') {//如果有其他的关联表那么就需要
				$moduleList = $this->referenceFieldInfoList[$fieldName];
				
				$tableJoinMapping[$field->getTableName()] = 'INNER JOIN';
				foreach($moduleList as $module) {
					if($module == 'Users' && $baseModule != 'Users') {
						$tableJoinCondition[$fieldName]['vtiger_users'.$fieldName] = $field->getTableName().".".$field->getColumnName()." = vtiger_users".$fieldName.".id";
						//$tableJoinCondition[$fieldName]['vtiger_user2department'.$fieldName] = $field->getTableName().".".$field->getColumnName()." = vtiger_departments".$fieldName.".departmentid";
						//$tableJoinCondition[$fieldName]['vtiger_departments'.$fieldName] = $field->getTableName().".".$field->getColumnName()." = vtiger_departments".$fieldName.".departmentid";
						$tableJoinMapping['vtiger_users'.$fieldName] = 'LEFT JOIN vtiger_users AS';
						//$tableJoinMapping['vtiger_user2department'.$fieldName] = 'LEFT JOIN vtiger_user2department AS';
						//$tableJoinMapping['vtiger_departments'.$fieldName] = 'LEFT JOIN vtiger_departments AS';
						$i++;
					}
				}
			} elseif($field->getFieldDataType() == 'owner') {
				$tableJoinCondition[$fieldName]['vtiger_users'.$fieldName] = $field->getTableName().".".$field->getColumnName()." = vtiger_users".$fieldName.".id";
				//$tableList['vtiger_user2department'] = 'vtiger_users';
				//$tableList['vtiger_departments'] = 'vtiger_departments';
				$tableJoinMapping['vtiger_users'.$fieldName] = 'LEFT JOIN vtiger_users AS';
				//$tableJoinMapping['vtiger_user2department'] = 'LEFT JOIN';
				//$tableJoinMapping['vtiger_departments'] = 'LEFT JOIN';
			}
			$tableList[$field->getTableName()] = $field->getTableName();
				$tableJoinMapping[$field->getTableName()] =
						$this->meta->getJoinClause($field->getTableName());//判读left join ,inner join
		}
		
		$baseTable = $this->meta->getEntityBaseTable();
		
		$baseTableIndex = $moduleTableIndexList[$baseTable];
		
		
		foreach ($this->whereFields as $fieldName) {  //where条件,一般是通过customview中的筛选条件过来的
			if(empty($fieldName)) {
				continue;
			}
			$field = $moduleFields[$fieldName];
			if(empty($field)) {
				// not accessible field.
				continue;
			}
			$baseTable = $field->getTableName();
			// When a field is included in Where Clause, but not is Select Clause, and the field table is not base table,
			// The table will not be present in tablesList and hence needs to be added to the list.
			if(empty($tableList[$baseTable])) {
				$tableList[$baseTable] = $field->getTableName();
				$tableJoinMapping[$baseTable] = $this->meta->getJoinClause($field->getTableName());
			}
			if($field->getFieldDataType() == 'reference') {
				$moduleList = $this->referenceFieldInfoList[$fieldName];
				$tableJoinMapping[$field->getTableName()] = 'INNER JOIN';
				foreach($moduleList as $module) {
					$meta = $this->getMeta($module);
					$nameFields = $this->moduleNameFields[$module];
					$nameFieldList = explode(',',$nameFields);
					foreach ($nameFieldList as $index=>$column) {
						$referenceField = $meta->getFieldByColumnName($column);
						$referenceTable = $referenceField->getTableName();
						$tableIndexList = $meta->getEntityTableIndexList();
						$referenceTableIndex = $tableIndexList[$referenceTable];

						$referenceTableName = "$referenceTable";//"2015-1-31 bug#7583 $referenceTable $referenceTable$fieldName";
						//$referenceTable = "$referenceTable$fieldName";  2015-1-31 bug#7583
						//should always be left join for cases where we are checking for null
						//reference field values.
						if(!array_key_exists($referenceTable, $tableJoinMapping)) {		// table already added in from clause
							$tableJoinMapping[$referenceTableName] = 'LEFT JOIN';
							$tableJoinCondition[$fieldName][$referenceTableName] = $baseTable.'.'.
								$field->getColumnName().' = '.$referenceTable.'.'.$referenceTableIndex;
						}
					}
				}
			} else {
				$tableList[$field->getTableName()] = $field->getTableName();
				$tableJoinMapping[$field->getTableName()] =
						$this->meta->getJoinClause($field->getTableName());
			}
		}

		$defaultTableList = $this->meta->getEntityDefaultTableList(); //默认表是否关联vtiger_crmentity
		
		foreach ($defaultTableList as $table) {
			if(!in_array($table, $tableList)) {
				$tableList[$table] = $table;
				$tableJoinMapping[$table] = 'INNER JOIN';
			}
		}
		
		$ownerFields = $this->meta->getOwnerFields();
		if (count($ownerFields) > 0) {
			$ownerField = $ownerFields[0];
		}
		$baseTable = $this->meta->getEntityBaseTable();//又是多次调研
		$sql = " FROM $baseTable ";
		unset($tableList[$baseTable]);
		foreach ($defaultTableList as $tableName) {
			$sql .= " $tableJoinMapping[$tableName] $tableName ON $baseTable.".
					"$baseTableIndex = $tableName.$moduleTableIndexList[$tableName]";
			unset($tableList[$tableName]);
		}
		
		//print_r($tableList);die();
		//拼接链接
		foreach ($tableList as $tableName) {
			if($tableName == 'vtiger_users') {
				
					$field = $moduleFields[$ownerField];
					$sql .= " $tableJoinMapping[$tableName] $tableName ON ".$field->getTableName().".".
							$field->getColumnName()." = $tableName.id";
				
				
			} elseif($tableName == 'vtiger_departments') {
				$field = $moduleFields[$ownerField];
				$sql .= " $tableJoinMapping[$tableName] $tableName ON ".$field->getTableName().".".
					$field->getColumnName()." = $tableName.departmentid";
			} else {
				$sql .= " $tableJoinMapping[$tableName] $tableName ON $baseTable.".
					"$baseTableIndex = $tableName.$moduleTableIndexList[$tableName]";
			}
		}

		if( $this->meta->getTabName() == 'Documents') {
			$tableJoinCondition['folderid'] = array(
				'vtiger_attachmentsfolderfolderid'=>"$baseTable.folderid = vtiger_attachmentsfolderfolderid.folderid"
			);
			$tableJoinMapping['vtiger_attachmentsfolderfolderid'] = 'INNER JOIN vtiger_attachmentsfolder';
		}
		
		foreach ($tableJoinCondition as $fieldName=>$conditionInfo) {
			foreach ($conditionInfo as $tableName=>$condition) {
				if(!empty($tableList[$tableName])) {
					$tableNameAlias = $tableName.'2';
					$condition = str_replace($tableName, $tableNameAlias, $condition);
				} else {
					$tableNameAlias = '';
				}
				$sql .= " $tableJoinMapping[$tableName] $tableName $tableNameAlias ON $condition";
			}
		}
		
		foreach ($this->manyToManyRelatedModuleConditions as $conditionInfo) {
			$relatedModuleMeta = RelatedModuleMeta::getInstance($this->meta->getTabName(),
					$conditionInfo['relatedModule']);
			$relationInfo = $relatedModuleMeta->getRelationMeta();
			$relatedModule = $this->meta->getTabName();
			$sql .= ' INNER JOIN '.$relationInfo['relationTable']." ON ".
			$relationInfo['relationTable'].".$relationInfo[$relatedModule]=".
				"$baseTable.$baseTableIndex";
		}
		
		// Adding support for conditions on reference module fields
		// 为涉及到的模块字段增加支持条件
		if($this->referenceModuleField) {
			$referenceFieldTableList = array();
			foreach ($this->referenceModuleField as $index=>$conditionInfo) {

				$handler = vtws_getModuleHandlerFromName($conditionInfo['relatedModule'], $current_user);
				$meta = $handler->getMeta();
				$tableList = $meta->getEntityTableIndexList();
				$fieldName = $conditionInfo['fieldName'];
				$referenceFieldObject = $moduleFields[$conditionInfo['referenceField']];
				$fields = $meta->getModuleFields();
				$fieldObject = $fields[$fieldName];

				if(empty($fieldObject)) continue;

				$tableName = $fieldObject->getTableName();
				if(!in_array($tableName, $referenceFieldTableList)) {
					if($referenceFieldObject->getFieldName() == 'parent_id' && ($this->getModule() == 'Calendar' || $this->getModule() == 'Events')) {
						$sql .= ' LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid ';
					}
					//TODO : this will create duplicates, need to find a better way
					if($referenceFieldObject->getFieldName() == 'contact_id' && ($this->getModule() == 'Calendar' || $this->getModule() == 'Events')) {
						$sql .= ' LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid ';
					}
					$sql .= " LEFT JOIN ".$tableName.' AS '.$tableName.$conditionInfo['referenceField'].' ON
							'.$tableName.$conditionInfo['referenceField'].'.'.$tableList[$tableName].'='.
						$referenceFieldObject->getTableName().'.'.$referenceFieldObject->getColumnName();
					$referenceFieldTableList[] = $tableName;
				}
			}
		}
		
		$sql .= $this->meta->getEntityAccessControlQuery();
		$this->fromClause = $sql;
		return $sql;
	}
	/*
	 * 
	 */
	public function getWhereClause() {
		global $current_user;
		if(!empty($this->query) || !empty($this->whereClause)) {
			return $this->whereClause;
		}
		$deletedQuery = $this->meta->getEntityDeletedQuery();
		$sql = '';
		if(!empty($deletedQuery)) {
			$sql .= " WHERE $deletedQuery";
		}
		//大于0代表是有多个条件
		if($this->conditionInstanceCount > 0) {
			$sql .= ' AND ';
		} elseif(empty($deletedQuery)) {
			$sql .= ' WHERE ';
		}
		$baseModule = $this->getModule();//module
		$moduleFieldList = $this->meta->getModuleFields();
		$baseTable = $this->meta->getEntityBaseTable();//表
		
		$moduleTableIndexList = $this->meta->getEntityTableIndexList();//module名.php下的$tab_name_index

		$baseTableIndex = $moduleTableIndexList[$baseTable];
		$groupSql = $this->groupInfo;
		
		//conditionals 在addUserSearchConditions。addCondition。
		$fieldSqlList = array();
		//customview里面的筛选条件
		foreach ($this->conditionals as $index=>$conditionInfo) {
			$fieldName = $conditionInfo['name'];
			$field = $moduleFieldList[$fieldName];
			if(empty($field) || $conditionInfo['operator'] == 'None') {
				continue;
			}
			$fieldSql = '(';
			$fieldGlue = '';
			$valueSqlList = $this->getConditionValue($conditionInfo['value'],
				$conditionInfo['operator'], $field);
			if(!is_array($valueSqlList)) {
				$valueSqlList = array($valueSqlList);
			}
			foreach ($valueSqlList as $valueSql) {
				if (in_array($fieldName, $this->referenceFieldList)) {
					if($conditionInfo['operator'] == 'y'){
						$columnName = $field->getColumnName();
						$tableName = $field->getTableName();
						// We are checking for zero since many reference fields will be set to 0 if it doest not have any value
						$fieldSql .= "$fieldGlue $tableName.$columnName $valueSql OR $tableName.$columnName = '0'";
						$fieldGlue = ' OR';
					}else{
						$moduleList = $this->referenceFieldInfoList[$fieldName];
						foreach($moduleList as $module) {
							$nameFields = $this->moduleNameFields[$module];
							$nameFieldList = explode(',',$nameFields);
							$meta = $this->getMeta($module);
							$columnList = array();
							foreach ($nameFieldList as $column) {
								if($module == 'Users') {
									$instance = CRMEntity::getInstance($module);
									$referenceTable = $instance->table_name;
									if(count($this->ownerFields) > 0 ||
											$this->getModule() == 'Quotes') {
										$referenceTable .= $fieldName;
									}
								} else {
									$referenceField = $meta->getFieldByColumnName($column);
									$referenceTable = $referenceField->getTableName();//2015-1-31 bug#7583  .$fieldName
								}
								if(isset($moduleTableIndexList[$referenceTable])) {
									$referenceTable = "$referenceTable";//"2015-1-31 bug#7583 $referenceTable $referenceTable$fieldName";
								}
								$columnList[] = "$referenceTable.$column";//2015-1-31 bug#7583 $referenceTable.$column
							}
							if(count($columnList) > 1) {
								$columnSql = getSqlForNameInDisplayFormat(array('first_name'=>$columnList[0],'last_name'=>$columnList[1]),'Users');
								//$columnSql = 'vtiger_users.id';//getSqlForNameInDisplayFormat(array('first_name'=>$columnList[0],'last_name'=>$columnList[1]),'Users');
							} else {
								$columnSql = implode('', $columnList);
								//$columnSql = 'vtiger_users.id';
							}
							if($columnSql=='vtiger_users.last_name'){
								$columnSql = 'vtiger_users.id';
							}
							$fieldSql .= "$fieldGlue trim($columnSql) $valueSql";
							$fieldGlue = ' OR';
						}
					}
				} elseif (in_array($fieldName, $this->ownerFields)) {
					//$concatSql = getSqlForNameInDisplayFormat(array('first_name'=>"vtiger_users.first_name",'last_name'=>"vtiger_users.last_name"), 'Users');
					//$fieldSql .= "$fieldGlue (trim($concatSql) $valueSql or "."vtiger_groups.groupname $valueSql)";
					$concatSql = 'vtiger_users'.$fieldName.'.id';//getSqlForNameInDisplayFormat(array('id'=>"vtiger_users.id"), 'Users');
					$fieldSql .= "$fieldGlue (trim($concatSql) $valueSql)";
				} elseif($field->getFieldDataType() == 'date' && ($baseModule == 'Events' || $baseModule == 'Calendar') && ($fieldName == 'date_start' || $fieldName == 'due_date')) {
					$value = $conditionInfo['value'];
					$operator = $conditionInfo['operator'];
					/* if($fieldName == 'date_start') {
						$dateFieldColumnName = 'vtiger_activity.date_start';
						$timeFieldColumnName = 'vtiger_activity.time_start';
					} else {
						$dateFieldColumnName = 'vtiger_activity.due_date';
						$timeFieldColumnName = 'vtiger_activity.time_end';
					} */
					if($operator == 'bw') {
						$values = explode(',', $value);
						$startDateValue = explode(' ', $values[0]);
						$endDateValue = explode(' ', $values[1]);
						if(count($startDateValue) == 2 && count($endDateValue) == 2) {
							$fieldSql .= " CAST(CONCAT($dateFieldColumnName,' ',$timeFieldColumnName) AS DATETIME) $valueSql";
						} else {
							$fieldSql .= "$dateFieldColumnName $valueSql";
						}
					} else {
                        if(is_array($value)){
                            $value = $value[0];
                        }
						$values = explode(' ', $value);
						if(count($values) == 2) {
							$fieldSql .= "$fieldGlue CAST(CONCAT($dateFieldColumnName,' ',$timeFieldColumnName) AS DATETIME) $valueSql ";
						} else {
							$fieldSql .= "$fieldGlue $dateFieldColumnName $valueSql";
                        }
					}
				} elseif($field->getFieldDataType() == 'datetime') {
					$value = $conditionInfo['value'];
					$operator = strtolower($conditionInfo['operator']);
					if($operator == 'bw') {
						$values = explode(',', $value);
						$startDateValue = explode(' ', $values[0]);
						$endDateValue = explode(' ', $values[1]);
						if($startDateValue[1] == '00:00:00' && $endDateValue[1] == '00:00:00') {
							$fieldSql .= "$fieldGlue CAST(".$field->getTableName().'.'.$field->getColumnName()." AS DATE) $valueSql";
						} else {
							$fieldSql .= "$fieldGlue ".$field->getTableName().'.'.$field->getColumnName().' '.$valueSql;
						}
					} elseif($operator == 'between' || $operator == 'notequal' || $operator == 'a' || $operator == 'b') {
						$fieldSql .= "$fieldGlue ".$field->getTableName().'.'.$field->getColumnName().' '.$valueSql;
					} else {
						$values = explode(' ', $value);
						if($values[1] == '00:00:00') {
							$fieldSql .= "$fieldGlue CAST(".$field->getTableName().'.'.$field->getColumnName()." AS DATE) $valueSql";
						} else {
							$fieldSql .= "$fieldGlue ".$field->getTableName().'.'.$field->getColumnName().' '.$valueSql;
						}
					}
				} else {
					if($fieldName == 'birthday' && !$this->isRelativeSearchOperators(
							$conditionInfo['operator'])) {
						$fieldSql .= "$fieldGlue DATE_FORMAT(".$field->getTableName().'.'.
						$field->getColumnName().",'%m%d') ".$valueSql;
					} else {
						$fieldSql .= "$fieldGlue ".$field->getTableName().'.'.
						$field->getColumnName().' '.$valueSql;
					}
				}
				if($conditionInfo['operator'] == 'n' && ($field->getFieldDataType() == 'owner' || $field->getFieldDataType() == 'picklist') ) {
					$fieldGlue = ' AND';
				} else {
					$fieldGlue = ' OR';
				}
			}
			$fieldSql .= ')';
			$fieldSqlList[$index] = $fieldSql;
		}
		
		
		foreach ($this->manyToManyRelatedModuleConditions as $index=>$conditionInfo) {
			$relatedModuleMeta = RelatedModuleMeta::getInstance($this->meta->getTabName(),
					$conditionInfo['relatedModule']);
			$relationInfo = $relatedModuleMeta->getRelationMeta();
			$relatedModule = $this->meta->getTabName();
			$fieldSql = "(".$relationInfo['relationTable'].'.'.
			$relationInfo[$conditionInfo['column']].$conditionInfo['SQLOperator'].
			$conditionInfo['value'].")";
			$fieldSqlList[$index] = $fieldSql;
		}

		// This is added to support reference module fields
		if($this->referenceModuleField) {
			foreach ($this->referenceModuleField as $index=>$conditionInfo) {
				$handler = vtws_getModuleHandlerFromName($conditionInfo['relatedModule'], $current_user);
				$meta = $handler->getMeta();
				$fieldName = $conditionInfo['fieldName'];
				$fields = $meta->getModuleFields();
				$fieldObject = $fields[$fieldName];
				$columnName = $fieldObject->getColumnName();
				$tableName = $fieldObject->getTableName();
				$valueSQL = $this->getConditionValue($conditionInfo['value'], $conditionInfo['SQLOperator'], $fieldObject);
				$fieldSql = "(".$tableName.$conditionInfo['referenceField'].'.'.$columnName.' '.$valueSQL[0].")";
				$fieldSqlList[$index] = $fieldSql;
			}
		}
		// This is needed as there can be condition in different order and there is an assumption in makeGroupSqlReplacements API
		// that it expects the array in an order and then replaces the sql with its the corresponding place
		
		ksort($fieldSqlList);
		$groupSql = $this->makeGroupSqlReplacements($fieldSqlList, $groupSql);
		if($this->conditionInstanceCount > 0) {
			$this->conditionalWhere = $groupSql;
			$sql .= $groupSql;
		}
		$sql .= " AND $baseTable.$baseTableIndex > 0";
		$this->whereClause = $sql;
		
		//echo "<script>console.log(\"'.$sql.'\");</script>";
		return $sql;
	}

	/**
	 * 获取查询条件值
	 * @param mixed $value
	 * @param String $operator
	 * @param WebserviceField $field
	 */
	private function getConditionValue($value, $operator, $field) {

		$operator = strtolower($operator);
		$db = PearDatabase::getInstance();
		
		if(is_string($value) && $this->ignoreComma == false) {
			$valueArray = explode(',' , $value);
			if (($field->getFieldDataType() == 'multipicklist' ||$field->getUIType() == 54) && in_array($operator, array('e', 'n'))) {
				$valueArray = getCombinations($valueArray);
				
				foreach ($valueArray as $key => $value) {
					$valueArray[$key] = ltrim($value, ' |##| ');
				}
			}
		} elseif(is_array($value)) {
			$valueArray = $value;
		} else{
			$valueArray = array($value);
		}
		$sql = array();
		if($operator == 'between' || $operator == 'bw' || $operator == 'notequal') {
			if($field->getFieldName() == 'birthday') {
				$valueArray[0] = getValidDBInsertDateTimeValue($valueArray[0]);
				$valueArray[1] = getValidDBInsertDateTimeValue($valueArray[1]);
				$sql[] = "BETWEEN DATE_FORMAT(".$db->quote($valueArray[0]).", '%m%d') AND ".
						"DATE_FORMAT(".$db->quote($valueArray[1]).", '%m%d')";
			} else {
				if($this->isDateType($field->getFieldDataType())) {
					$valueArray[0] = getValidDBInsertDateTimeValue($valueArray[0]);
					$dateTimeStart = explode(' ',$valueArray[0]);
					if($dateTimeStart[1] == '00:00:00' && $operator != 'between') {
						$valueArray[0] = $dateTimeStart[0];
					}
					$valueArray[1] = getValidDBInsertDateTimeValue($valueArray[1]);
					$dateTimeEnd = explode(' ', $valueArray[1]);
					if($dateTimeEnd[1] == '00:00:00') {
						$valueArray[1] = $dateTimeEnd[0];
					}
				}
				
				if($operator == 'notequal') {
					$sql[] = "NOT BETWEEN ".$db->quote($valueArray[0])." AND ".
							$db->quote($valueArray[1]);
				} else {
					$sql[] = "BETWEEN ".$db->quote($valueArray[0])." AND ".
							$db->quote($valueArray[1]);
				}
			}
			return $sql;
		}
		foreach ($valueArray as $value) {
			if(!$this->isStringType($field->getFieldDataType())) {
				$value = trim($value);
			}
			if ($operator == 'empty' || $operator == 'y') {
				$sql[] = sprintf("IS NULL OR %s = ''", $this->getSQLColumn($field->getFieldName()));
				continue;
			}
			if((strtolower(trim($value)) == 'null') ||
					(trim($value) == '' && !$this->isStringType($field->getFieldDataType())) &&
							($operator == 'e' || $operator == 'n')) {
				if($operator == 'e'){
					$sql[] = "IS NULL";
					continue;
				}
				$sql[] = "IS NOT NULL";
				continue;
			} elseif($field->getFieldDataType() == 'boolean') {
				$value = strtolower($value);
				if ($value == 'yes') {
					$value = 1;
				} elseif($value == 'no') {
					$value = 0;
				}
			} elseif($this->isDateType($field->getFieldDataType())) {
				$value = getValidDBInsertDateTimeValue($value);
				$dateTime = explode(' ', $value);
				if($dateTime[1] == '00:00:00') {
					$value = $dateTime[0];
				}
			}

			if($field->getFieldName() == 'birthday' && !$this->isRelativeSearchOperators(
					$operator)) {
				$value = "DATE_FORMAT(".$db->quote($value).", '%m%d')";
			} else {
				$value = $db->sql_escape_string($value);
			}

			if(trim($value) == '' && ($operator == 's' || $operator == 'ew' || $operator == 'c')
					&& ($this->isStringType($field->getFieldDataType()) ||
					$field->getFieldDataType() == 'picklist' ||
					$field->getFieldDataType() == 'multipicklist')) {
				$sql[] = "LIKE ''";
				continue;
			}

			if(trim($value) == '' && ($operator == 'k') &&
					$this->isStringType($field->getFieldDataType())) {
				$sql[] = "NOT LIKE ''";
				continue;
			}

			switch($operator) {
				case 'e': $sqlOperator = "=";
					break;
				case 'n': $sqlOperator = "<>";
					break;
				case 's': $sqlOperator = "LIKE";
					$value = "$value%";
					break;
				case 'ew': $sqlOperator = "LIKE";
					$value = "%$value";
					break;
				case 'c': $sqlOperator = "LIKE";
					$value = "%$value%";
					break;
				case 'k': $sqlOperator = "NOT LIKE";
					$value = "%$value%";
					break;
				case 'l': $sqlOperator = "<";
					break;
				case 'g': $sqlOperator = ">";
					break;
				case 'm': $sqlOperator = "<=";
					break;
				case 'h': $sqlOperator = ">=";
					break;
				case 'a': $sqlOperator = ">";
					break;
				case 'b': $sqlOperator = "<";
					break;
			}
			if(!$this->isNumericType($field->getFieldDataType()) &&
					($field->getFieldName() != 'birthday' || ($field->getFieldName() == 'birthday'
							&& $this->isRelativeSearchOperators($operator)))){
				$value = "'$value'";
			}
			if($this->isNumericType($field->getFieldDataType()) && empty($value)) {
				$value = '0';
			}
			$sql[] = "$sqlOperator $value";
		}
		return $sql;
	}

	private function makeGroupSqlReplacements($fieldSqlList, $groupSql) {
		$pos = 0;
		$nextOffset = 0;
		foreach ($fieldSqlList as $index => $fieldSql) {
			$pos = strpos($groupSql, $index.'', $nextOffset);
			if($pos !== false) {
				$beforeStr = substr($groupSql,0,$pos);
				$afterStr = substr($groupSql, $pos + strlen($index));
				$nextOffset = strlen($beforeStr.$fieldSql);
				$groupSql = $beforeStr.$fieldSql.$afterStr;
			}
		}
		return $groupSql;
	}

	private function isRelativeSearchOperators($operator) {
		$nonDaySearchOperators = array('l','g','m','h');
		return in_array($operator, $nonDaySearchOperators);
	}
	private function isNumericType($type) {
		return ($type == 'integer' || $type == 'double' || $type == 'currency');
	}

	private function isStringType($type) {
		return ($type == 'string' || $type == 'text' || $type == 'email' || $type == 'reference');
	}

	private function isDateType($type) {
		return ($type == 'date' || $type == 'datetime');
	}
	
	public function fixDateTimeValue($name, $value, $first = true) {
		$moduleFields = $this->meta->getModuleFields();
		$field = $moduleFields[$name];
		$type = $field ? $field->getFieldDataType() : false;
		if($type == 'datetime') {
			if(strrpos($value, ' ') === false) {
				if($first) {
					return $value.' 00:00:00';
				}else{
					return $value.' 23:59:59';
				}
			}
		}
		return $value;
	}

	public function addCondition($fieldname,$value,$operator,$glue= null,$newGroup = false,
		$newGroupType = null, $ignoreComma = false) {
		$conditionNumber = $this->conditionInstanceCount++;
		if($glue != null && $conditionNumber > 0)
			$this->addConditionGlue ($glue);

		$this->groupInfo .= "$conditionNumber ";
		$this->whereFields[] = $fieldname;
		$this->ignoreComma = $ignoreComma;
		$this->reset();
		$this->conditionals[$conditionNumber] = $this->getConditionalArray($fieldname,
				$value, $operator);
	}
	//加入关联模块条件
	public function addRelatedModuleCondition($relatedModule,$column, $value, $SQLOperator) {
		$conditionNumber = $this->conditionInstanceCount++;
		$this->groupInfo .= "$conditionNumber ";
		$this->manyToManyRelatedModuleConditions[$conditionNumber] = array('relatedModule'=>
			$relatedModule,'column'=>$column,'value'=>$value,'SQLOperator'=>$SQLOperator);
	}

	public function addReferenceModuleFieldCondition($relatedModule, $referenceField, $fieldName, $value, $SQLOperator, $glue=null) {
		$conditionNumber = $this->conditionInstanceCount++;
		if($glue != null && $conditionNumber > 0)
			$this->addConditionGlue($glue);

		$this->groupInfo .= "$conditionNumber ";
		$this->referenceModuleField[$conditionNumber] = array('relatedModule'=> $relatedModule,'referenceField'=> $referenceField,'fieldName'=>$fieldName,'value'=>$value,
			'SQLOperator'=>$SQLOperator);
	}

	private function getConditionalArray($fieldname,$value,$operator) {
		if(is_string($value)) {
			$value = trim($value);
		} elseif(is_array($value)) {
			$value = array_map(trim, $value);
		}
		return array('name'=>$fieldname,'value'=>$value,'operator'=>$operator);
	}

	public function startGroup($groupType) {
		$this->groupInfo .= " $groupType (";
	}

	public function endGroup() {
		$this->groupInfo .= ')';
	}

	public function addConditionGlue($glue) {
		$this->groupInfo .= " $glue ";
	}
	//使用在listview.php 177行
	public function addUserSearchConditions($input) {
		global $log,$default_charset;
		if($input['searchtype']=='advance') {

			$json = new Zend_Json();
			$advft_criteria = $_REQUEST['advft_criteria'];
			if(!empty($advft_criteria))	$advft_criteria = $json->decode($advft_criteria);
			$advft_criteria_groups = $_REQUEST['advft_criteria_groups'];
			if(!empty($advft_criteria_groups))	$advft_criteria_groups = $json->decode($advft_criteria_groups);

			if(empty($advft_criteria) || count($advft_criteria) <= 0) {
				return ;
			}

			$advfilterlist = getAdvancedSearchCriteriaList($advft_criteria, $advft_criteria_groups, $this->getModule());

			if(empty($advfilterlist) || count($advfilterlist) <= 0) {
				return ;
			}

			if($this->conditionInstanceCount > 0) {
				$this->startGroup(self::$AND);
			} else {
				$this->startGroup('');
			}
			foreach ($advfilterlist as $groupindex=>$groupcolumns) {
				$filtercolumns = $groupcolumns['columns'];
				if(count($filtercolumns) > 0) {
					$this->startGroup('');
					foreach ($filtercolumns as $index=>$filter) {
						$name = explode(':',$filter['columnname']);
						if(empty($name[2]) && $name[1] == 'crmid' && $name[0] == 'vtiger_crmentity') {
							$name = $this->getSQLColumn('id');
						} else {
							$name = $name[2];
						}
						$this->addCondition($name, $filter['value'], $filter['comparator']);
						$columncondition = $filter['column_condition'];
						if(!empty($columncondition)) {
							$this->addConditionGlue($columncondition);
						}
					}
					$this->endGroup();
					$groupConditionGlue = $groupcolumns['condition'];
					if(!empty($groupConditionGlue))
						$this->addConditionGlue($groupConditionGlue);
				}
			}
			$this->endGroup();
		} elseif($input['type']=='dbrd') {
			if($this->conditionInstanceCount > 0) {
				$this->startGroup(self::$AND);
			} else {
				$this->startGroup('');
			}
			$allConditionsList = $this->getDashBoardConditionList();
			$conditionList = $allConditionsList['conditions'];
			$relatedConditionList = $allConditionsList['relatedConditions'];
			$noOfConditions = count($conditionList);
			$noOfRelatedConditions = count($relatedConditionList);
			foreach ($conditionList as $index=>$conditionInfo) {
				$this->addCondition($conditionInfo['fieldname'], $conditionInfo['value'],
						$conditionInfo['operator']);
				if($index < $noOfConditions - 1 || $noOfRelatedConditions > 0) {
					$this->addConditionGlue(self::$AND);
				}
			}
			foreach ($relatedConditionList as $index => $conditionInfo) {
				$this->addRelatedModuleCondition($conditionInfo['relatedModule'],
						$conditionInfo['conditionModule'], $conditionInfo['finalValue'],
						$conditionInfo['SQLOperator']);
				if($index < $noOfRelatedConditions - 1) {
					$this->addConditionGlue(self::$AND);
				}
			}
			$this->endGroup();
		} else {
			if(isset($input['search_field']) && $input['search_field'] !="") {
				$fieldName=vtlib_purify($input['search_field']);
			} else {
				return ;
			}
			if($this->conditionInstanceCount > 0) {
				$this->startGroup(self::$AND);
			} else {
				$this->startGroup('');
			}
			$moduleFields = $this->meta->getModuleFields();
			$field = $moduleFields[$fieldName];
			$type = $field->getFieldDataType();
			if(isset($input['search_text']) && $input['search_text']!="") {
				// search other characters like "|, ?, ?" by jagi
				$value = $input['search_text'];
				$stringConvert = function_exists(iconv) ? @iconv("UTF-8",$default_charset,$value)
						: $value;
				if(!$this->isStringType($type)) {
					$value=trim($stringConvert);
				}

				if($type == 'picklist') {
					global $mod_strings;
					// Get all the keys for the for the Picklist value
					$mod_keys = array_keys($mod_strings, $value);
					if(sizeof($mod_keys) >= 1) {
						// Iterate on the keys, to get the first key which doesn't start with LBL_      (assuming it is not used in PickList)
						foreach($mod_keys as $mod_idx=>$mod_key) {
							$stridx = strpos($mod_key, 'LBL_');
							// Use strict type comparision, refer strpos for more details
							if ($stridx !== 0) {
								$value = $mod_key;
								break;
							}
						}
					}
				}
				if($type == 'currency') {
					// Some of the currency fields like Unit Price, Total, Sub-total etc of Inventory modules, do not need currency conversion
					if($field->getUIType() == '72') {
						$value = CurrencyField::convertToDBFormat($value, null, true);
					} else {
						$currencyField = new CurrencyField($value);
						$value = $currencyField->getDBInsertedValue();
					}
				}
			}
			if(!empty($input['operator'])) {
				$operator = $input['operator'];
			} elseif(trim(strtolower($value)) == 'null'){
				$operator = 'e';
			} else {
				if(!$this->isNumericType($type) && !$this->isDateType($type)) {
					$operator = 'c';
				} else {
					$operator = 'h';
				}
			}
			$this->addCondition($fieldName, $value, $operator);
			$this->endGroup();
		}
	}

	public function getDashBoardConditionList() {
		if(isset($_REQUEST['leadsource'])) {
			$leadSource = $_REQUEST['leadsource'];
		}
		if(isset($_REQUEST['date_closed'])) {
			$dateClosed = $_REQUEST['date_closed'];
		}
		if(isset($_REQUEST['sales_stage'])) {
			$salesStage = $_REQUEST['sales_stage'];
		}
		if(isset($_REQUEST['closingdate_start'])) {
			$dateClosedStart = $_REQUEST['closingdate_start'];
		}
		if(isset($_REQUEST['closingdate_end'])) {
			$dateClosedEnd = $_REQUEST['closingdate_end'];
		}
		if(isset($_REQUEST['owner'])) {
			$owner = vtlib_purify($_REQUEST['owner']);
		}
		if(isset($_REQUEST['campaignid'])) {
			$campaignId = vtlib_purify($_REQUEST['campaignid']);
		}
		if(isset($_REQUEST['quoteid'])) {
			$quoteId = vtlib_purify($_REQUEST['quoteid']);
		}
		if(isset($_REQUEST['invoiceid'])) {
			$invoiceId = vtlib_purify($_REQUEST['invoiceid']);
		}
		if(isset($_REQUEST['purchaseorderid'])) {
			$purchaseOrderId = vtlib_purify($_REQUEST['purchaseorderid']);
		}

		$conditionList = array();
		if(!empty($dateClosedStart) && !empty($dateClosedEnd)) {

			$conditionList[] = array('fieldname'=>'closingdate', 'value'=>$dateClosedStart,
				'operator'=>'h');
			$conditionList[] = array('fieldname'=>'closingdate', 'value'=>$dateClosedEnd,
				'operator'=>'m');
		}
		if(!empty($salesStage)) {
			if($salesStage == 'Other') {
				$conditionList[] = array('fieldname'=>'sales_stage', 'value'=>'Closed Won',
					'operator'=>'n');
				$conditionList[] = array('fieldname'=>'sales_stage', 'value'=>'Closed Lost',
					'operator'=>'n');
			} else {
				$conditionList[] = array('fieldname'=>'sales_stage', 'value'=> $salesStage,
					'operator'=>'e');
			}
		}
		if(!empty($leadSource)) {
			$conditionList[] = array('fieldname'=>'leadsource', 'value'=>$leadSource,
					'operator'=>'e');
		}
		if(!empty($dateClosed)) {
			$conditionList[] = array('fieldname'=>'closingdate', 'value'=>$dateClosed,
					'operator'=>'h');
		}
		if(!empty($owner)) {
			$conditionList[] = array('fieldname'=>'assigned_user_id', 'value'=>$owner,
					'operator'=>'e');
		}
		$relatedConditionList = array();
		if(!empty($campaignId)) {
			$relatedConditionList[] = array('relatedModule'=>'Campaigns','conditionModule'=>
				'Campaigns','finalValue'=>$campaignId, 'SQLOperator'=>'=');
		}
		if(!empty($quoteId)) {
			$relatedConditionList[] = array('relatedModule'=>'Quotes','conditionModule'=>
				'Quotes','finalValue'=>$quoteId, 'SQLOperator'=>'=');
		}
		if(!empty($invoiceId)) {
			$relatedConditionList[] = array('relatedModule'=>'Invoice','conditionModule'=>
				'Invoice','finalValue'=>$invoiceId, 'SQLOperator'=>'=');
		}
		if(!empty($purchaseOrderId)) {
			$relatedConditionList[] = array('relatedModule'=>'PurchaseOrder','conditionModule'=>
				'PurchaseOrder','finalValue'=>$purchaseOrderId, 'SQLOperator'=>'=');
		}
		return array('conditions'=>$conditionList,'relatedConditions'=>$relatedConditionList);
	}

	public function initForGlobalSearchByType($type, $value, $operator='s') {
		$fieldList = $this->meta->getFieldNameListByType($type);
		if($this->conditionInstanceCount <= 0) {
			$this->startGroup('');
		} else {
			$this->startGroup(self::$AND);
		}
		$nameFieldList = explode(',',$this->getModuleNameFields($this->module));
		foreach ($nameFieldList as $nameList) {
			$field = $this->meta->getFieldByColumnName($nameList);
			$this->fields[] = $field->getFieldName();
		}
		foreach ($fieldList as $index => $field) {
			$fieldName = $this->meta->getFieldByColumnName($field);
			$this->fields[] = $fieldName->getFieldName();
			if($index > 0) {
				$this->addConditionGlue(self::$OR);
			}
			$this->addCondition($fieldName->getFieldName(), $value, $operator);
		}
		$this->endGroup();
		if(!in_array('id', $this->fields)) {
				$this->fields[] = 'id';
		}
	}

}
?>