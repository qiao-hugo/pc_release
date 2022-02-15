<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


class SalesDaily_ListView_Model extends Vtiger_ListView_Model {
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();

        $db=PearDatabase::getInstance();
        $query='SELECT dproduct,sproduct,dlabel,tyundownup FROM `vtiger_productdownupgrade` WHERE deleted=0';
        $result=$db->pquery($query,array());
        $array=array(0);
        while($row=$db->fetch_array($result))
        {
            if(!empty($row['dproduct'])){
                $array[$row['tyundownup']][$row['sproduct']][]=array('product'=>$row['dproduct'],'name'=>$row['dlabel']);
            }
        }
		$moduleName = 'SalesDaily';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');
		if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
			$orderBy = 'vtiger_salesdaily_basic.salesdailybasicid';
			$sortOrder = 'DESC';
		}
		
        $this->getSearchWhere();
        
        $listQuery = $this->getQuery();
        $listQuery=$this->replaceSql($listQuery);

        $listQuery.=$this->getUserWhere();

	
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

        if($_REQUEST['public']=='CanDeal') {
            $aaa=strpos($listQuery,'FROM vtiger_salesdailycandeal');
            if($aaa!==false){
                $newlistQuery=substr($listQuery,$aaa);
                $listQuery.=' AND vtiger_salesdailycandeal.salesdailycandealid=(SELECT id from (SELECT max(vtiger_salesdailycandeal.salesdailycandealid) as id,vtiger_salesdailycandeal.accountid,vtiger_salesdaily_basic.smownerid '.$newlistQuery.' GROUP BY vtiger_salesdailycandeal.accountid,vtiger_salesdaily_basic.smownerid) AS t WHERE t.accountid=vtiger_salesdailycandeal.accountid  AND t.smownerid=vtiger_salesdaily_basic.smownerid)';

            }
            $listQuery.=' GROUP BY vtiger_salesdailycandeal.accountid,vtiger_salesdaily_basic.smownerid';
        }
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        if($_REQUEST['public']=='NoDaily'){
            $aaa=stripos($listQuery,'WHERE 1=1');
            if($aaa!==false){
                $newlistQuery=substr($listQuery,$aaa);
                $search=array(
                    'vtiger_salesdaily_basic.salesdailybasicid',
                    'vtiger_salesdaily_basic.dailydatetime',
                    'vtiger_salesdaily_basic.smownerid',
                );
                $replace=array(
                    'vtiger_nosalesdaily.nosalesdailyid',
                    'vtiger_nosalesdaily.workday',
                    'vtiger_nosalesdaily.userid',

                );
                //
                $listQuery=str_replace($search,$replace,$newlistQuery);
                $listQuery="SELECT vtiger_nosalesdaily.nosalesdailyid as salesdailybasicid,vtiger_nosalesdaily.workday as 'dailydatetime',vtiger_departments.departmentid,IFNULL(vtiger_departments.departmentname,'--') as departmentname,CONCAT(vtiger_users.last_name,'[',IFNULL(vtiger_departments.departmentname,'--'),']',IF(vtiger_users.`status`!='Active','[离职]','')) as smownerid FROM vtiger_nosalesdaily LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_nosalesdaily.userid LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid
                            LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid {$listQuery}";
            }

        }

		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit+1);

        //echo $listQuery;die;
		$listResult = $db->pquery($listQuery, array());
		$listViewRecordModels = array();
		//3.在进行一次转化，目的何在
		$index = 0;
        if($_REQUEST['public']=='DayDeal' || $_REQUEST['public']=='ItemNotv' || $_REQUEST['public']=='CanDeal' || $_REQUEST['public']=='NextDayVisit'){
            while($rawData=$db->fetch_array($listResult)) {
                $rawData['id'] = $rawData['salesdailybasicid'];
                $listViewRecordModels[$rawData['tempid']] = $rawData;
            }
        }else{
            while($rawData=$db->fetch_array($listResult)) {
                $rawData['id'] = $rawData['salesdailybasicid'];
                $listViewRecordModels[$rawData['salesdailybasicid']] = $rawData;
            }
        }

// 		echo "<pre>";
// 		print_r($listViewRecordModels);
// 		echo "</pre>";
// 		exit;
		return $listViewRecordModels;
	}
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];//部门    
         $listQuery='';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('SalesDaily','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_salesdaily_basic.smownerid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and vtiger_salesdaily_basic.smownerid '.$where;

            }
        }
        return $listQuery;
    }
    
    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');
        $temp=array();
        if($_REQUEST['public']=='ItemNotv'){

            $temparr=array(
                'leadsource',
                'accountname',
                'linkname',
                'mobile',
                'title',
                'startdatetime',
                'mangereturntime',
                'mcontent');
            foreach($temparr as $value){
                $temp[$value]=Array(
                    'columnname' => $value,
                    'fieldname'=> $value,
                    'fieldlabel' => $value
                );
            }


        }elseif($_REQUEST['public']=='CanDeal'){
            $temparr=array(
                'createdtime',
                'dailydatetime',
                'departmentid',
                'smownerid',
                'accountname',
                'title',
                'contactname',
                'mobile',
                'productname',
                'quote',
                'firstpayment',
                'issigncontract','accountcontent');
            foreach($temparr as $value){
                $temp[$value]=Array(
                    'columnname' => $value,
                    'fieldname'=> $value,
                    'fieldlabel' => $value
                );
            }

        }elseif($_REQUEST['public']=='DayDeal'){
            $temparr=array(
                'createdtime',
                'dailydatetime',
                'departmentid',
                'smownerid',
                'accountname',
                'productname',
                'marketprice',
                'dealamount',
                'allamount',
                'firstpayment',
                'discount',
                'arrivalamount',
                'visitingordercount',
                'oldcustomers',
                'industry',
                'visitingobj',
                'withvisitor',

                );
            foreach($temparr as $value){
                if($value=='firstpayment'){
                    $temp[$value.'daydeal']=Array(
                        'columnname' => $value,
                        'fieldname'=> $value,
                        'fieldlabel' => $value
                    );
                }else{
                    $temp[$value]=Array(
                        'columnname' => $value,
                        'fieldname'=> $value,
                        'fieldlabel' => $value
                    );
                }

            }
        }elseif($_REQUEST['public']=='NextDayVisit'){
            $temparr=array(
                'contacts',
                'title',
                'visitingordernum',
                'accountname',
                'purpose',
                'withvisitor',
                );
            foreach($temparr as $value){
                $temp[$value]=Array(
                    'columnname' => $value,
                    'fieldname'=> $value,
                    'fieldlabel' => $value
                );
            }
        }
        if(!empty($sourceModule)){
            return $queryGenerator->getModule()->getPopupFields();
        }else{

            $list=$queryGenerator->getModule()->getListFields();
            //$temp=array();
            foreach($list as $fields){
                $temp[$fields['fieldlabel']]=$fields;
            }

            if($_REQUEST['public']=='NoDaily'){
                $nodaily['dailydatetime']=$temp['dailydatetime'];
                $nodaily['smownerid']=$temp['smownerid'];
                $nodaily['departmentid']=$temp['departmentid'];
                return $nodaily;
            }else{
                return $temp;
            }

        }
        return $queryGenerator->getFocus()->list_fields_name;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');

        $where=$this->getUserWhere();

        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listQuery=$this->replaceSql($listQuery);

        if($_REQUEST['public']=='CanDeal') {
            $listQuery.=' GROUP BY vtiger_salesdailycandeal.accountid,vtiger_salesdaily_basic.smownerid';
            $listResult = $db->pquery($listQuery, array());
            return $db->num_rows($listResult);
        }
        if($_REQUEST['public']=='NoDaily'){
            $aaa=stripos($listQuery,'WHERE 1=1');
            if($aaa!==false){
                $newlistQuery=substr($listQuery,$aaa);
                $search=array(
                    'vtiger_salesdaily_basic.salesdailybasicid',
                    'vtiger_salesdaily_basic.dailydatetime',
                    'vtiger_salesdaily_basic.smownerid',
                );
                $replace=array(
                    'vtiger_nosalesdaily.nosalesdailyid',
                    'vtiger_nosalesdaily.workday',
                    'vtiger_nosalesdaily.userid',

                );
                $listQuery=str_replace($search,$replace,$newlistQuery);
                $listQuery="SELECT count(1) as counts FROM vtiger_nosalesdaily  {$listQuery}";
            }

        }
        //echo $listQuery;
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
    private  function replaceSql($listQuery){

        if($_REQUEST['public']=='ItemNotv'){
            $search=array(
                'vtiger_salesdaily_basic.accountname,',
                'vtiger_salesdaily_basic.accountname LIKE',
                'vtiger_salesdaily_basic.accountname IS NOT NULL',
                'FROM vtiger_salesdaily_basic'

            );
            $replace=array(
                'vtiger_salesdailyfournotv.accountname,vtiger_salesdailyfournotv.*,vtiger_salesdailyfournotv.salesdailyfournotvid as tempid,',
                'vtiger_salesdailyfournotv.accountname LIKE',
                'vtiger_salesdailyfournotv.accountname IS NOT NULL',
                'FROM vtiger_salesdailyfournotv LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdailyfournotv.salesdailybasicid=vtiger_salesdaily_basic.salesdailybasicid'

            );
            //
            $listQuery=str_replace($search,$replace,$listQuery);

        }elseif($_REQUEST['public']=='CanDeal'){
            $search=array(
                'vtiger_salesdaily_basic.accountname,',
                'vtiger_salesdaily_basic.accountname LIKE',
                'vtiger_salesdaily_basic.accountname IS NOT NULL',
                ',vtiger_salesdaily_basic.salesdailybasicid',
                'FROM vtiger_salesdaily_basic',


            );
            $replace=array(
                'vtiger_account.accountname,vtiger_salesdailycandeal.*,vtiger_salesdailycandeal.salesdailycandealid as tempid,',
                'vtiger_account.accountname LIKE',
                'vtiger_account.accountname IS NOT NULL',
                ',vtiger_salesdaily_basic.salesdailybasicid,max(vtiger_salesdailycandeal.salesdailycandealid) as fmaxid',
                'FROM vtiger_salesdailycandeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailycandeal.accountid LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdailycandeal.salesdailybasicid=vtiger_salesdaily_basic.salesdailybasicid'

            );
            //
            $listQuery=str_replace($search,$replace,$listQuery);

        }elseif($_REQUEST['public']=='DayDeal'){
            $search=array(
                'vtiger_salesdaily_basic.accountname,',
                'vtiger_salesdaily_basic.accountname LIKE',
                'vtiger_salesdaily_basic.accountname IS NOT NULL',
                'FROM vtiger_salesdaily_basic'
            );
            $replace=array(
                'vtiger_account.accountname,vtiger_salesdailydaydeal.*,vtiger_salesdailydaydeal.salesadailydaydealid as tempid,',
                'vtiger_account.accountname LIKE',
                'vtiger_account.accountname IS NOT NULL',
                'FROM vtiger_salesdailydaydeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailydaydeal.accountid LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdailydaydeal.salesdailybasicid=vtiger_salesdaily_basic.salesdailybasicid'

            );
            //
            $listQuery=str_replace($search,$replace,$listQuery);

        }elseif($_REQUEST['public']=='NextDayVisit'){
            $search=array(
                'vtiger_salesdaily_basic.accountname,',
                'vtiger_salesdaily_basic.accountname LIKE',
                'vtiger_salesdaily_basic.accountname IS NOT NULL',
                'FROM vtiger_salesdaily_basic'

            );
            $replace=array(
                'vtiger_salesdailynextdayvisit.accountname,vtiger_salesdailynextdayvisit.*,vtiger_salesdailynextdayvisit.salesdailynextdayvisitid as tempid,',
                'vtiger_salesdailynextdayvisit.accountname LIKE',
                'vtiger_salesdailynextdayvisit.accountname IS NOT NULL',
                'FROM vtiger_salesdailynextdayvisit LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdailynextdayvisit.salesdailybasicid=vtiger_salesdaily_basic.salesdailybasicid'

            );
            //
            $listQuery=str_replace($search,$replace,$listQuery);

        }
        return $listQuery;
    }




}
