<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class EmployeeAbility_ListView_Model extends Vtiger_ListView_Model {
    //根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='EmployeeAbility';

		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');
		if(empty($orderBy) && empty($sortOrder)){
			$orderBy = 'employeeabilityid';
			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();
        
        $listQuery.=$this->getUserWhere();
        global $current_user;
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        $listQuery = str_replace("vtiger_employee_ability.isdimission","vtiger_users.isdimission",$listQuery);
//        echo $listQuery;die;
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
      	$listQuery .= " LIMIT $startIndex,".($pageLimit);

		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
		    $rawData['id'] = $rawData['employeeabilityid'];
		    $rawData['employeestage'] = $this->getEmployeeStage($rawData['user_entered']);
			$listViewRecordModels[] = $rawData;
		}
		return $listViewRecordModels;
	}

	public function getEmployeeStage($user_entered){
        $currentdate=date("Y-m-d");
        $entered=explode('-',$user_entered);
        if($entered[2]>15){
            $entered[1]=$entered[1]+1;
            if($entered[1]<13){
                $enteredday=$entered[0].'-'.$entered[1].'-01';
            }else{
                $enteredday=($entered[0]+1).'-01-01';
            }
        }else{
            $enteredday=$entered[0].'-'.$entered[1].'-01';;
        }

        $currentDiffMonth=$this->getMonthNum($enteredday,$currentdate);
        return ($currentDiffMonth>=0?
            ($currentDiffMonth>1?
                ($currentDiffMonth>3?
                    ($currentDiffMonth>6?
                        ($currentDiffMonth>12?'12个月以上':'6~12个月')
                        :'3~6个月')
                    :'1~3个月')
                :'1个月内')
            :'12个月以上');
    }

    public function getMonthNum($date1,$date2){
        if(strtotime($date1)>strtotime($date2)){
            $tmp=$date2;
            $date2=$date1;
            $date1=$tmp;
        }
        list($Y1,$m1,$d1)=explode('-',$date1);
        list($Y2,$m2,$d2)=explode('-',$date2);
        $Y=$Y2-$Y1;
        $m=$m2-$m1;
        $d=$d2-$d1;
        if($d<0){
            $d+=(int)date('t',strtotime("-1 month $date2"));
            $m--;
        }
        if($m<0){
            $m+=12;
            $Y--;
        }
        return $Y*12+$m+($d>0?1:0);
    }

    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');
        if(!empty($sourceModule)){
           return $queryGenerator->getModule()->getPopupFields();
        }else{
            $list=$queryGenerator->getModule()->getListFields();
            $temp=array();
            foreach($list as $fields){
                $temp[$fields['fieldlabel']]=$fields;
            }
           return $temp;
        }

        return $queryGenerator->getFocus()->list_fields_name;
        
    }
    public function getUserWhere(){
        global $current_user;
        $listQuery='';
        $searchDepartment = $_REQUEST['department'];
        $moduleModel = EmployeeAbility_Module_Model::getCleanInstance('EmployeeAbility');
        $where=getAccessibleUsers('EmployeeAbility','List');
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            require('crmcache/departmentanduserinfo.php');
            $deparr=$departmentinfo[$searchDepartment];
            if($where!='1=1'){
                $listQuery .=' AND (vtiger_employee_ability.userid'.$where.')';
            }
            if(!empty($deparr)){
                $listQuery.=' AND vtiger_employee_ability.departmentid IN(\''.implode("','",$deparr).'\')';
            }
        }else{
            if($where!='1=1' && !$moduleModel->exportGrouprt('EmployeeAbility','employeeAbility')){
                    $listQuery .=' AND (vtiger_employee_ability.userid'.$where.')';
            }
        }
        if(!$moduleModel->exportGrouprt('EmployeeAbility','employeeAbility')){
            $listQuery .= ' AND vtiger_users.isdimission=0';
        }

        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}