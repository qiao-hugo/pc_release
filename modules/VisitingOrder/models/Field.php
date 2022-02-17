<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'vtlib/Vtiger/Field.php';

/**
 * Vtiger Field Model Class
 */
class VisitingOrder_Field_Model extends Vtiger_Field_Model {
	/**
	 * Function to get the field details
	 * @return <Array> - array of field values
	 */
	public function getFieldInfo() {
		$currentUser = Users_Record_Model::getCurrentUserModel();
        $fieldDataType = $this->getFieldDataType();
		$this->fieldInfo['mandatory'] = $this->isMandatory();
		$this->fieldInfo['presence'] = $this->isActiveField();
		$this->fieldInfo['quickcreate'] = $this->isQuickCreateEnabled();
		$this->fieldInfo['masseditable'] = $this->isMassEditable();
		$this->fieldInfo['defaultvalue'] = $this->hasDefaultValue();
		$this->fieldInfo['type'] = $fieldDataType;
		$this->fieldInfo['name'] = $this->get('name');
		$this->fieldInfo['label'] = vtranslate($this->get('label'), $this->getModuleName());
        //echo $this->get('name').':::'.$fieldDataType;
        //wangbin 2015年6月9日 添加对数字字段的验证类型;
		if($this->getFieldDataType() == 'number'){
		    $this->fieldInfo['type'] = 'number';
		}
        if($this->getFieldDataType() == 'negativenumber'){
            $this->fieldInfo['type'] = 'negativenumber';
        }
        if($fieldDataType == 'picklist' || $fieldDataType == 'multipicklist') {
            $pickListValues = $this->getPicklistValues();
            if(!empty($pickListValues)) {
                //工作流 状态根据模块筛选 只针对一个字段进行设置
                if($this->getFieldName()=='modulestatus'){
                    //获取模块下的关联
                    $modulename = $this->getModuleName();
                    $modulestatusmodule=Vtiger_Cache::get('modulestatusmodule', $modulename);
                    if($modulestatusmodule){
                        $this->fieldInfo['picklistvalues']=$modulestatusmodule;
                    }else{
                        $db = PearDatabase::getInstance();
                        $sql ="select * from `vtiger_modulestatus_module` where modulename= '".$modulename."'";
                        $result = $db->pquery($sql,array());
                        if($db->num_rows($result)>0){
                            $temp = array();
                            while($row = $db->fetch_row($result)){
                                if(!empty($pickListValues[$row['modulestatus']])){
                                    $temp[$row['modulestatus']] = $pickListValues[$row['modulestatus']];
                                }
                            }
                            unset($this->fieldInfo['picklistvalues']);
                            $this->fieldInfo['picklistvalues'] = $temp;
                        }else{
                            $this->fieldInfo['picklistvalues'] = $pickListValues;  // 如果没有那么就显示所有的
                        }
                        Vtiger_Cache::set('modulestatusmodule', $modulename, $this->fieldInfo['picklistvalues']);
                    }

                }else{
                    $this->fieldInfo['picklistvalues'] = $pickListValues;   // 默认的
                }
            }
        }

		if($this->getFieldDataType() == 'date' || $this->getFieldDataType() == 'datetime'){
			$currentUser = Users_Record_Model::getCurrentUserModel();
			$this->fieldInfo['date-format'] = $currentUser->get('date_format');
		}

		if($this->getFieldDataType() == 'time') {
			$currentUser = Users_Record_Model::getCurrentUserModel();
			$this->fieldInfo['time-format'] = $currentUser->get('hour_format');
		}

		if($this->getFieldDataType() == 'currency') {
			$currentUser = Users_Record_Model::getCurrentUserModel();
			$this->fieldInfo['currency_symbol'] = $currentUser->get('currency_symbol');
		}

		if($this->getFieldDataType() == 'owner') {
			$pickListValues = array();
			if($_REQUEST['view']=='List'){
				$userList = $this->getAccessibleUsers();
				$pickListValues[vtranslate('LBL_USERS', $this->getModuleName())] = $userList;
			}
			//$groupList = $currentUser->getAccessibleGroups();

			//$pickListValues[vtranslate('LBL_GROUPS', $this->getModuleName())] = $groupList;
			$this->fieldInfo['picklistvalues'] = $pickListValues;
		}
		//当前表单不满足现有验证时，通过字段的label（？）进行更加自定义的验证;
		if($this->fieldInfo['name']=='phone'){
			$this->fieldInfo['type'] = 'Phone';
		}
		if($this->fieldInfo['name']=='mobile'){
			$this->fieldInfo['type'] = 'Mobile';
		}
		if($this->fieldInfo['name']=='website'){
			$this->fieldInfo['type'] = 'Url';
		}
		if($this->fieldInfo['name']=='bill_code'){
			$this->fieldInfo['type']='Zcode';
		}
		if($this->fieldInfo['name']=='country'){
			$this->fieldInfo['type']='Country';
		}
		if($this->fieldInfo['name']=='bill_street'){
			$this->fieldInfo['type']='String';
		}
		if($this->fieldInfo['name']=='otherstreet'){
			$this->fieldInfo['type']='String';
		}
		if($this->fieldInfo['name']=='mailingstreet'){
			$this->fieldInfo['type']='String';
		}
        if($this->fieldInfo['name']=='accountname'){
			$this->fieldInfo['type']='Aname';
		}
        if($this->fieldInfo['name']=='telephone'){
			$this->fieldInfo['type'] = 'Phone';
		}
        if($this->fieldInfo['name']=='amountofmoneynegative'||$this->fieldInfo['name']=='taxnegative'||$this->fieldInfo['name']=='totalandtaxnegative'){
            $this->fieldInfo['type']='String';
        }
		/* if($this->fieldInfo['name']=='exchangerate'){
		    $this->fieldInfo['type'] = 'number';
		} */
		return $this->fieldInfo;
	}
    /**
     * 列出下属用户
     * @return <Array>
     */
    public function getAccessibleUsers($uitype='',$private="",$module = false,$departmentid="") {
        //error_reporting(E_ALL);
        //人员多选的场合不设置条件/gaocl 2015-01-13
        if ($uitype =='54'){
            $where="1=1_54";
        }else{
            $where=getAccessibleUsers();
        }
		$public=$_REQUEST['public'];
		if(!empty($public) && $public=='quit'){
			$accessibleUser = Vtiger_Cache::get('vtiger-'.md5($where),'accessibleusersquit');
		}else{
			$accessibleUser = Vtiger_Cache::get('vtiger-'.md5($where),'accessibleusers');
		}

        if(empty($accessibleUser)) {
			if(!empty($public) && $public=='quit'){
                    $accessibleUser = get_username_array($where,'Inactive');
                    Vtiger_Cache::set('vtiger-'.md5($where), 'accessibleusersquit',$accessibleUser);
			}else{	
				if ($uitype =='54'){
					$accessibleUser = get_user_department_array($departmentid);
				}else{
                    $accessibleUser = get_username_array($where,'Active');
                }
				Vtiger_Cache::set('vtiger-'.md5($where), 'accessibleusers',$accessibleUser);
            }
            
        }

        return $accessibleUser;
    }

}