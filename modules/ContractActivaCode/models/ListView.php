<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractActivaCode_ListView_Model extends Vtiger_ListView_Model {

    /**
     * 模块列表页面显示链接 保留新增 Edit By Joe @20150511
     * @param <Array> $linkParams
     * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
     */
    public function getListViewLinks($linkParams) {
        $basicLinks = array();
        $links=array();$links['LISTVIEWBASIC'];
        return $links;

    }
	//根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='ContractActivaCode';

		
		if(!empty($request)){
			if(isset($request['BugFreeQuery'])){
				$_REQUEST['BugFreeQuery'] = $request['BugFreeQuery'];
			}
			if(isset($request['public'])){
				$_REQUEST['public'] = $request['public'];
			}
		}

		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'servicecontractsid';
			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();
        
        $listQuery.=$this->getUserWhere();
        $listQuery.=' order by '.$orderBy.' '.$sortOrder;
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);

        //echo $listQuery;die();

		$listResult = $db->pquery($listQuery, array());
		$index = 0;

        $moduleMOdel=$this->getModule();
		while($rawData=$db->fetch_array($listResult)) {
            $activedate=empty($rawData['startdate'])?date('Y-m-d'):$rawData['startdate'];
            $contractstatus = $rawData['contractstatus'];
            $current_date=date('Y-m-d');
            if($contractstatus == 't_c_cancel'){
                $docanceltime = $rawData['docanceltime'];
                $current_date=empty($docanceltime)?date('Y-m-d'):$docanceltime;
            }
            $currentDiffMonth=$moduleMOdel->getMonthNum(substr($current_date,0,7),substr($activedate,0,7));
            $currentDiffMonth=$currentDiffMonth['y']*12+$currentDiffMonth['m'];
            $maxMonth=$rawData['productlife']*12;
            $diffMonth=empty($rawData['startdate'])?0:($currentDiffMonth==0?1:($currentDiffMonth>$maxMonth?$maxMonth:$currentDiffMonth));
            $monthlyIncome=$rawData['servicecontractstotal']/$maxMonth;
            $monthlyIncome=number_format($monthlyIncome,2,'.','');
            $cumulativeIncome=$diffMonth!=$maxMonth?$diffMonth*$monthlyIncome:$rawData['servicecontractstotal'];
            $isMaturity = $currentDiffMonth > $maxMonth ? '是' : '否';
            if($contractstatus == 't_c_cancel') {
                $thisMonthlyIncome = '--';
                $isMaturity =  '是';
            }else{
                $thisMonthlyIncome = (empty($rawData['startdate']) || $currentDiffMonth > $maxMonth) ? 0 : $monthlyIncome;
            }
            $rawData['paymenttotal']=($rawData['paymenttotal']<=$rawData['servicecontractstotal'])?$rawData['paymenttotal']:$rawData['servicecontractstotal'];
            $rawData['id'] = $rawData['contractactivacodeid'];
            $rawData['thisMonthlyIncome'] = $thisMonthlyIncome;
            $rawData['isMaturity'] = $isMaturity;
            $rawData['monthlyIncome'] = $monthlyIncome;
            $rawData['cumulativeIncome'] = $cumulativeIncome;
            $rawData['accountsreceivable'] = $cumulativeIncome-$rawData['paymenttotal'];
			$listViewRecordModels[$rawData['contractactivacodeid']] = $rawData;
		}
		return $listViewRecordModels;
	}
	
    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');
        if(!empty($sourceModule)){
           return $queryGenerator->getModule()->getPopupFields();
        }else{

            $list=$queryGenerator->getModule()->getListFields();
            foreach($list as $fields){
                $temp[$fields['fieldlabel']]=$fields;
            }
           return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
        
    }
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];
        $listQuery='';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('ContractActivaCode','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);

            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery=' and vtiger_contractactivacode.signid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery= ' and vtiger_contractactivacode.signid '.$where;
            }
        }
        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();

        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}