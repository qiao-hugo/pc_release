<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class VisitingOrder_ListView_Model extends Vtiger_ListView_Model {


	/**
	 * Function to get the list of Mass actions for the module
	 * @param $linkParams
	 * @return array <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 * @internal param $ <Array> $linkParams
	 */
	public function getListViewMassActions($linkParams) {
		$massActionLinks = parent::getListViewMassActions($linkParams);

		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$emailModuleModel = Vtiger_Module_Model::getInstance('Emails');

		if($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
			$massActionLink = array(
					'linktype' => 'LISTVIEWMASSACTION',
					'linklabel' => 'LBL_SEND_EMAIL',
					'linkurl' => 'javascript:Vtiger_List_Js.triggerSendEmail("index.php?module='.$this->getModule()->getName().'&view=MassActionAjax&mode=showComposeEmailForm&step=step1","Emails");',
					'linkicon' => ''
			);
			$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}

		$SMSNotifierModuleModel = Vtiger_Module_Model::getInstance('SMSNotifier');
		if($currentUserModel->hasModulePermission($SMSNotifierModuleModel->getId())) {
			$massActionLink = array(
					'linktype' => 'LISTVIEWMASSACTION',
					'linklabel' => 'LBL_SEND_SMS',
					'linkurl' => 'javascript:Vtiger_List_Js.triggerSendSms("index.php?module='.$this->getModule()->getName().'&view=MassActionAjax&mode=showSendSMSForm","SMSNotifier");',
					'linkicon' => ''
			);
			$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}

		$moduleModel = $this->getModule();
		if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
			$massActionLink = array(
					'linktype' => 'LISTVIEWMASSACTION',
					'linklabel' => 'LBL_TRANSFER_OWNERSHIP',
					'linkurl' => 'javascript:Vtiger_List_Js.triggerTransferOwnership("index.php?module='.$moduleModel->getName().'&view=MassActionAjax&mode=transferOwnership")',
					'linkicon' => ''
			);
			$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}

		return $massActionLinks;
	}

	/**
	 *
	 * @param $linkParams
	 * @return array
	 */
	function getListViewLinks($linkParams) {
		$links = parent::getListViewLinks($linkParams);

		$index=0;
		foreach($links['LISTVIEWBASIC'] as $link) {
			if($link->linklabel == 'Send SMS') {
				unset($links['LISTVIEWBASIC'][$index]);
			}
			$index++;
		}
		return $links;
	}


	/*//根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='VisitingOrder';
		// 如果包含vtiger_visitingorder.filteringrules拼接说明是使用过滤规则来查询 如果没有选择过滤规则 走else
		if(strpos($_REQUEST['BugFreeQuery'],'vtiger_visitingorder.filteringrules')){
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

                $orderBy = 'visitingorderid';
                //$orderBy = 'vtiger_crmentity.modifiedtime';
                $sortOrder = 'DESC';
            }
            $this->getSearchWhereRules();
            $listQuery = $this->getQuery();

            if(strpos($_REQUEST['BugFreeQuery'],'threemonthsvisit')){
                //$listQuery = str_replace('vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid',' (vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.signnum=1) ',$listQuery);

                //echo $listQuery;die();
                //$wheres=$this->getSearchWhereAboutThreeMonthsVisit();
                //$wheres =  empty($wheres) ? '1=1': $wheres;
                //$sql=' AND vtiger_visitingorder.visitingorderid IN (SELECT s.visitingorderid FROM vtiger_visitingorder as s  LEFT JOIN vtiger_visitsign as signs ON (signs.visitingorderid=s.visitingorderid AND signs.signnum=1) WHERE  '.$wheres.'  GROUP BY s.related_to,signs.userid ) ';
                //$sql=' AND vtiger_visitingorder.visitingorderid IN (SELECT MAX(visitingorderid) FROM (SELECT s.visitingorderid,s.related_to,s.extractid FROM vtiger_visitingorder AS s INNER JOIN vtiger_visitsign AS signs ON ( signs.visitingorderid = s.visitingorderid  AND signs.signnum = 1 ) WHERE  '.$wheres.' AND s.related_to > 0  GROUP BY s.related_to, signs.userid ) as a GROUP BY a.related_to,a.extractid ORDER BY a.visitingorderid desc)';
                //$listQuery.=$sql;
            }
            $listQuery.=$this->getUserWhere();
            global $current_user;
            $startIndex = $pagingModel->getStartIndex();
            $pageLimit = $pagingModel->getPageLimit();
            //$listQuery=str_replace('issign=1','vtiger_visitingorder.issign=1',$listQuery);
            $listQuery=str_replace('as signtime,','as signtimes,',$listQuery);
            $listQuery=str_replace('as signaddress,','as signaddresss,',$listQuery);
            $listQuery=str_replace('vtiger_visitingorder.modulestatus,','vtiger_visitingorder.modulestatus,(SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id=vtiger_visitingorder.extractid LIMIT 1) as email,',$listQuery);
            $listQuery=str_replace('vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','(vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.visitsigntype=\'陪同人\')',$listQuery);

            if(strpos($_REQUEST['BugFreeQuery'],'threemonthsvisit')){
                $listQueryID = str_replace('SELECT vtiger_visitingorder.signaddress','SELECT  MAX(vtiger_visitsign.userid) as queryid,vtiger_visitingorder.signaddress',$listQuery);
                $listQueryID=str_replace('(vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.visitsigntype=\'陪同人\')',' (vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.signnum=1) ',$listQueryID);
                $listQueryID.= '  AND  vtiger_visitingorder.related_to > 0  GROUP BY  vtiger_visitsign.userid ';
                $listQueryID = ' SELECT queryid FROM ('.$listQueryID.') as a  ';
                $wheres=$this->getSearchWhereAboutThreeMonthsVisit();
                $wheres =  empty($wheres) ? '1=1': $wheres;
                $queryrelated_to = ' SELECT MAX( vo.related_to) as related_to  FROM  vtiger_visitsign as vs LEFT JOIN vtiger_visitingorder as vo  ON vo.visitingorderid = vs.visitingorderid  WHERE  vs.signnum=1 AND vs.userid IN('.$listQueryID.')  AND  '.$wheres.' GROUP BY  vo.related_to  ';
                $listQuery .= ' AND  vtiger_visitingorder.related_to NOT IN ('.$queryrelated_to.') ';
                $listQuery .= ' AND  vtiger_visitingorder.related_to > 0  GROUP BY vtiger_visitingorder.visitingorderid  ';//加上分组来去重
                $listQuery = ' SELECT * FROM ('.$listQuery.') as  result GROUP BY result.related_to_reference ORDER BY '. $orderBy . ' ' .$sortOrder;
            }else{
                $listQuery .= ' AND  vtiger_visitingorder.related_to > 0  GROUP BY vtiger_visitingorder.visitingorderid ORDER BY '. $orderBy . ' ' .$sortOrder;//加上分组来去重
            }


            $viewid = ListViewSession::getCurrentView($moduleName);

            ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,


            if(strpos($_REQUEST['BugFreeQuery'],'duplicateremoval')){
                $listQuery=" SELECT * FROM  (".$listQuery.") as a  GROUP BY  a.related_to ORDER BY  a.visitingorderid  DESC  LIMIT $startIndex,".($pageLimit);
            }else{
                 $listQuery .= " LIMIT $startIndex,".($pageLimit);
            }

            $listResult = $db->pquery($listQuery, array());


            $index = 0;
            while($rawData=$db->fetch_array($listResult)) {
                // 判断是否是陪同人 2016-7-14 周海
                $rawData['t_accompany'] = '提单人';
                if (!empty($rawData['accompany'])) {
                    $accompanyArr = explode(' |##| ', $rawData['accompany']);
                    if (in_array($current_user->id, $accompanyArr)) {
                        $rawData['t_accompany'] = '陪同人';
                    }
                }
                $rawData['id'] = $rawData['visitingorderid'];
                $listViewRecordModels[$rawData['visitingorderid']] = $rawData;
            }

            return $listViewRecordModels;
        // 正常查询
        }else{
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

                $orderBy = 'visitingorderid';
                //$orderBy = 'vtiger_crmentity.modifiedtime';
                $sortOrder = 'DESC';
            }
            $this->getSearchWhere();
            $listQuery = $this->getQuery();

            $listQuery.=$this->getUserWhere();
            global $current_user;

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
        //$listQuery=str_replace('issign=1','vtiger_visitingorder.issign=1',$listQuery);
        $listQuery=str_replace('as signtime,','as signtimes,',$listQuery);
        $listQuery=str_replace('as signaddress,','as signaddresss,',$listQuery);
        $listQuery=str_replace('vtiger_visitingorder.modulestatus,','vtiger_visitingorder.modulestatus,(SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id=vtiger_visitingorder.extractid LIMIT 1) as email,',$listQuery);
        $listQuery=str_replace('vtiger_visitsign ON  vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','(	select vtiger_visitsign.* from vtiger_visitsign left join vtiger_visitingorder on vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid where vtiger_visitsign.visitsigntype = \'陪同人\' and vtiger_visitsign.issign=1 and vtiger_visitsign.signnum =1 group by vtiger_visitsign.visitingorderid) as vtiger_visitsign ON vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid',$listQuery);
//        $listQuery=str_replace('vtiger_visitsign ON  vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','(vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.visitsigntype=\'陪同人\')',$listQuery);

            $listQuery .= ' GROUP BY vtiger_visitingorder.visitingorderid ORDER BY '. $orderBy . ' ' .$sortOrder;//加上分组来去重

            $viewid = ListViewSession::getCurrentView($moduleName);

            ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

            $listQuery .= " LIMIT $startIndex,".($pageLimit);

        //echo $listQuery;die();

            $listResult = $db->pquery($listQuery, array());


            $index = 0;
            while($rawData=$db->fetch_array($listResult)) {
                // 判断是否是陪同人 2016-7-14 周海
                $rawData['t_accompany'] = '提单人';
                if (!empty($rawData['accompany'])) {
                    $accompanyArr = explode(' |##| ', $rawData['accompany']);
                    if (in_array($current_user->id, $accompanyArr)) {
                        $rawData['t_accompany'] = '陪同人';
                    }
                }
                $rawData['id'] = $rawData['visitingorderid'];
                $listViewRecordModels[$rawData['visitingorderid']] = $rawData;
            }

            return $listViewRecordModels;
        }


	}*/
	/*// 的到过滤的sql
    function  getSearchWhereAboutThreeMonthsVisit(){
	    $sqlStr = '';
        $BugFreeQuery=isset($_REQUEST['BugFreeQuery']) ? $_REQUEST['BugFreeQuery']:'';
        if(!empty($BugFreeQuery)){
            $BugFreeQuery=json_decode($BugFreeQuery,true);
            if(isset($BugFreeQuery['BugFreeQuery[queryRowOrder]'])){
                $SearchConditionRow=$BugFreeQuery['BugFreeQuery[queryRowOrder]'];
                $SearchConditionRow=explode(',',$SearchConditionRow);
                if(is_array($SearchConditionRow)&&!empty($SearchConditionRow)){
                    foreach($SearchConditionRow as $key=>$val){
                        $val=str_replace('SearchConditionRow','',$val);
                        // 如果包含是 过滤规则 则不在这里组装这个过滤规则的sql语句
                        $searchKey=$BugFreeQuery['BugFreeQuery[field'.$val.']'];
                        $operator=$BugFreeQuery['BugFreeQuery[operator'.$val.']'];
                        $searchValue=$BugFreeQuery['BugFreeQuery[value'.$val.']'];
                        //如果是时间  如果带>= 的说明是最小时间 如果不是带 >= 的是最大时间
                        if(strpos($searchKey,'tiger_visitingorder.startdate')){
                            if($operator=='>='){
                                //date("Y-m-d",)
                                $date=strtotime("-3 month",strtotime($searchValue));
                                $date="'".date("Y-m-d",$date)."'";
                                if(!empty($sqlStr)){
                                    $sqlStr .= ' AND vo.startdate  '.$operator.$date.'  AND vo.startdate <= \'.$searchValue.\' ';
                                }else{
                                    $sqlStr = " vo.startdate  ".$operator.$date." AND vo.startdate <= '".$searchValue."'";
                                }
                            }
                        }
                        //如果选择指定的提单人 或者 拜访人
                        if(strpos($searchKey,'tiger_visitingorder.extractid')){
                            if(!empty($sqlStr)){
                                $sqlStr .= ' AND vo.extractid  '.$operator.$searchValue;
                            }else{
                                $sqlStr = ' vo.extractid  '.$operator.$searchValue;
                            }
                        }else if(strpos($searchKey,'tiger_visitingorder.accompany')){
                            if(!empty($sqlStr)){
                                $sqlStr .= ' AND vs.userid  '.$operator.$searchValue.'  AND vs.visitsigntype=\'陪同人\'';
                            }else{
                                $sqlStr = ' vs.userid  '.$operator.$searchValue.' AND vs.visitsigntype=\'陪同人\'';
                            }
                        }
                    }
                }
            }
        }
        return $sqlStr;
    }*/
    //如果是规则果过滤查询专用where 组装
    public function getSearchWhereRules(){

        $searchKey = $this->get('search_key');
        $queryGenerator = $this->get('query_generator');
        $queryGenerator -> addSearchWhere('');//置空
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if(!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator ,'leftkh'=>'','rightkh'=>'','andor'=>''));
        }

        $BugFreeQuery=isset($_REQUEST['BugFreeQuery'])?$_REQUEST['BugFreeQuery']:'';

        if(!empty($BugFreeQuery)){
            $BugFreeQuery=json_decode($BugFreeQuery,true);
            if(isset($BugFreeQuery['BugFreeQuery[queryRowOrder]'])){
                $SearchConditionRow=$BugFreeQuery['BugFreeQuery[queryRowOrder]'];
                $SearchConditionRow=explode(',',$SearchConditionRow);
                $counts=count($SearchConditionRow);
                if(is_array($SearchConditionRow)&&!empty($SearchConditionRow)){
                    $isExists = false;// 记录是过滤规则字段是否已经存在
                    foreach($SearchConditionRow as $key=>$val){
                        $val=str_replace('SearchConditionRow','',$val);
                        // 如果包含是 过滤规则 则不在这里组装这个过滤规则的sql语句
                        if(!strpos($BugFreeQuery['BugFreeQuery[field'.$val.']'],'tiger_visitingorder.filteringrules')){
                            $leftkh=$BugFreeQuery['BugFreeQuery[leftParenthesesName'.$val.']'];
                            $rightkh=$BugFreeQuery['BugFreeQuery[rightParenthesesName'.$val.']'];
                            $andor=$BugFreeQuery['BugFreeQuery[andor'.$val.']'];
                            $searchKey=$BugFreeQuery['BugFreeQuery[field'.$val.']'];
                            $operator=$BugFreeQuery['BugFreeQuery[operator'.$val.']'];
                            $searchValue=$BugFreeQuery['BugFreeQuery[value'.$val.']'];
                            if($searchKey!='department'){
                                // ($key+2)==$counts  指就到当前的key 的值为查询条件，所以到最后一个时不加 and or
                                //(($key+3)==$counts && $isExists==false)指的是如果在执行 倒数第二个条件时 如果$isExists==false  说明最后一个是过滤规则字段该字段只是做判断不加入 查询条件中所以 如果过滤字段为最后一个 则倒数第二个不加 and or
                                if(($key+2)==$counts ||(($key+3)==$counts && $isExists==false)){
                                    $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator ,'leftkh'=>$leftkh,'rightkh'=>$rightkh,'andor'=>'',"counts"=>$counts));
                                }else{
                                    $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator ,'leftkh'=>$leftkh,'rightkh'=>$rightkh,'andor'=>$andor,"counts"=>$counts));
                                }
                            }
                        }else{
                            $isExists =true;
                        }
                    }
                }
            }
        }
    }
    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');
        if(!empty($sourceModule)){
           return $queryGenerator->getModule()->getPopupFields();
        }else{

            $list=$queryGenerator->getModule()->getListFields();
            $temp=array();
            $valuearray=array('signtime'=>'signtimes','signaddress'=>'signaddresss',);
            $keyarray=array('signtime','signaddress');
            $skipkey=array('modulename','accountnamer');
            foreach($list as $fields){
                if(in_array($fields['columnname'],$skipkey)){
                    continue;
                }
                if(in_array($fields['columnname'],$keyarray) && $fields['tablename'] !='vtiger_visitingorder'){
                    $fields['columnname']=$valuearray[$fields['columnname']];
                    $fields['fieldname']=$valuearray[$fields['columnname']];
                }
                $temp[$fields['fieldlabel']]=$fields;
            }
           return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;

    }
    public function getUserWhere(){
       global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $sourceModule = $this->get('src_module');
        $listQuery=' ';
        //notfollow是未跟进的拜访单的状态followup是跟进后的拜访单状态
  		if($_REQUEST['public']=='unaudited'){
            //审核中待跟进的拜访单
            $listQuery .=" and vtiger_visitingorder.workflowsid=400 and vtiger_visitingorder.followstatus='notfollow'";
            $listQuery .=" and LOCATE('b_',vtiger_visitingorder.modulestatus)>0 ";
        }elseif($_REQUEST['public']=='pass'){
            //已跟进待审核的拜访单
            $listQuery .=" and vtiger_visitingorder.workflowsid=400 and vtiger_visitingorder.followstatus='followup'";
            $listQuery .=" and LOCATE('b_',vtiger_visitingorder.modulestatus)>0 ";
            $listQuery .=" and vtiger_visitingorder.workflowsnode='提单人上级审批' ";
        }elseif($_REQUEST['public']=='FollowUp'){
            //24小时内待跟进的拜访单
            $datetime=time()-86400;
            $newdatetime=date("Y-m-d H:i:s",$datetime);
            $listQuery .=" and vtiger_visitingorder.workflowsid=400 AND  vtiger_visitingorder.followstatus='notfollow'";
            $listQuery .=" and LOCATE('b_',vtiger_visitingorder.modulestatus)>0 ";
            $listQuery .=" AND vtiger_crmentity.createdtime>'{$newdatetime}' ";
        }elseif($_REQUEST['public']=='yesterday'){
            //昨日拜访单
            $currentstarttime = date("Y-m-d",strtotime('-1 day')).' 00:00';
            $currentendtime = date("Y-m-d",strtotime($currentstarttime)+24*60*60).' 00:00';
            $listQuery .= "and vtiger_visitingorder.startdate >'{$currentstarttime}' and vtiger_visitingorder.startdate <'{$currentendtime}'";
        }elseif($_REQUEST['public']=='today'){
            //今日拜访单
            $currentstarttime = date("Y-m-d").' 00:00';
            $currentendtime = date("Y-m-d",strtotime($currentstarttime)+24*60*60).' 00:00';
            $listQuery .= "and vtiger_visitingorder.startdate >'{$currentstarttime}' and vtiger_visitingorder.startdate <'{$currentendtime}'";
        }elseif($_REQUEST['public']=='tomorrow'){
            //明日拜访单
            $currentstarttime = date("Y-m-d",strtotime(date("Y-m-d"))+24*60*60).' 00:00';
            $currentendtime = date("Y-m-d",strtotime($currentstarttime)+24*60*60).' 00:00';
            $listQuery .= "and vtiger_visitingorder.startdate >'{$currentstarttime}' and vtiger_visitingorder.startdate <'{$currentendtime}'";
        }elseif ($_REQUEST['public']=='toapprove'){
            $listQuery .= " and vtiger_visitingorder.modulestatus='a_normal' and vtiger_visitingorder.auditorid={$current_user->id}";
        }elseif ($_REQUEST['public']=='quit'){
            //$listQuery .= " and vtiger_users.status='Inactive'";//去掉条件
        }
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('VisitingOrder','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            //$listQuery .= ' and vtiger_visitingorder.extractid in ('.implode(',',$where).')';

            //$listQuery .= ' and vtiger_crmentity.smownerid in ('.implode(',',$where).')';
            $listQuery .= ' and ((vtiger_visitingorder.extractid in ('.implode(',',$where).') OR exists (select 1 from vtiger_visitsign_mulit where vtiger_visitingorder.visitingorderid=vtiger_visitsign_mulit.visitingorderid and vtiger_visitsign_mulit.visitsigntype=\'陪同人\' and
vtiger_visitsign_mulit.userid in ('.implode(',',$where).'))))';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                //$listQuery .= ' and vtiger_visitingorder.extractid '.$where;
                //$listQuery .= ' and vtiger_crmentity.smownerid '.$where;
                $listQuery .= ' and ((vtiger_visitingorder.extractid '.$where . ' OR exists (select 1 from vtiger_visitsign_mulit where vtiger_visitingorder.visitingorderid=vtiger_visitsign_mulit.visitingorderid and vtiger_visitsign_mulit.visitsigntype=\'陪同人\' and
vtiger_visitsign_mulit.userid '.$where.')))';
            }
        }
        if(isset($where) && !empty($where)){
              $_REQUEST['alluserid']=$where;
        }
        //echo $listQuery;
        //exit;
        return $listQuery;
    }

    //根据参数显示数据  #移动crm模拟$request请求---2015-12-16 罗志坚
    public function getListViewEntries($pagingModel,$request=array()) {
        $db = PearDatabase::getInstance();
        $moduleName ='VisitingOrder';
        // 如果包含vtiger_visitingorder.filteringrules拼接说明是使用过滤规则来查询 如果没有选择过滤规则 走else
        if(strpos($_REQUEST['BugFreeQuery'],'vtiger_visitingorder.filteringrules')){
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

                $orderBy = 'visitingorderid';
                //$orderBy = 'vtiger_crmentity.modifiedtime';
                $sortOrder = 'DESC';
            }
            $this->getSearchWhereRules();
            $listQuery = $this->getQuery();

            $listQuery.=$this->getUserWhere();
            global $current_user;
            $startIndex = $pagingModel->getStartIndex();
            $pageLimit = $pagingModel->getPageLimit();
            //$listQuery=str_replace('issign=1','vtiger_visitingorder.issign=1',$listQuery);
            $listQuery=str_replace('as signtime,','as signtimes,',$listQuery);
            $listQuery=str_replace('as signaddress,','as signaddresss,',$listQuery);
            $listQuery=str_replace('vtiger_visitingorder.modulestatus,','vtiger_visitingorder.modulestatus,vtiger_visitingorder.isstrangevisit,(SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id=vtiger_visitingorder.extractid LIMIT 1) as email,',$listQuery);
            //$listQuery=str_replace('vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','(vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.visitsigntype=\'陪同人\')',$listQuery);
            // 三个月有效拜访单 改单的提单人和陪同人在这个单子以以前的三个月内没有该客户拜访单子的显示出来
            if(strpos($_REQUEST['BugFreeQuery'],'threemonthsvisit')){
                 //提示测试环境的这个和线上的头一个查询字段内容不一致 所以测试环境和本地一致，线上环境不能动一动就报错 除非不替换了
                $listQueryID = str_replace("SELECT IF(vtiger_visitingorder.newfirstvisting=1,'是','否') as newfirstvisting","SELECT  vtiger_visitsign.userid as queryid,IF(vtiger_visitingorder.newfirstvisting=1,'是','否') as newfirstvisting",$listQuery);
                $listQueryID.=" AND vtiger_visitingorder.related_to > 0  AND  vtiger_visitsign.signnum=1 AND (vtiger_visitingorder.modulestatus ='c_complete' OR vtiger_visitingorder.modulestatus ='a_normal') ";
                $wheres=$this->getSearchWhereAboutThreeMonthsVisit();
                if(!empty($wheres)){
                    $listQueryID.=' AND vtiger_visitsign.userid IN ('.$wheres.')  ';
                }
                $listResultUserAccount = $db->pquery($listQueryID, array());
                $listQueryIDStr='';
                // 取出 对应该单前三个月有同一拜访人或者陪同人 有相同客户的单子的visitingorderid
                while($rawData=$db->fetch_array($listResultUserAccount)) {
                    $date=strtotime("-3 month",strtotime($rawData['startdate']));
                    $date="'".date("Y-m-d",$date)."'";
                    $rawstartdate="'".$rawData['startdate']."'";
                    $related_to_reference=$rawData['related_to_reference'];
                    $queryid=$rawData['queryid'];
                    $sqlThreeMonthsAgo=" SELECT * FROM  vtiger_visitingorder as vo  LEFT JOIN  vtiger_crmentity as c ON  vo.visitingorderid = c.crmid  LEFT JOIN  vtiger_visitsign as vs ON vs.visitingorderid=vo.visitingorderid  WHERE 1=1 AND c.deleted=0 AND vo.related_to> 0  AND  vo.startdate >= {$date} AND  vo.startdate <{$rawstartdate}  AND vo.related_to={$related_to_reference}  AND  vs.userid= {$queryid} AND vs.userid> 0   AND (vo.modulestatus ='c_complete' OR vo.modulestatus ='a_normal')  LIMIT 1 ";
                    $listResultDATA = $db->pquery($sqlThreeMonthsAgo, array());
                    //echo "<pre>";
                    //var_dump($listResultDATA);die();
                    //echo  $db->num_rows($listResultDATA);
                    if($db->num_rows($listResultDATA)>0){
                        $listQueryIDStr.=$rawData['visitingorderid'].',';
                    }
                }

                if(!empty($listQueryIDStr)){
                    $listQueryIDStr = trim($listQueryIDStr,',');
                    $listQuery.= '  AND  vtiger_visitingorder.visitingorderid NOT IN ('.$listQueryIDStr.') ';
                }
                $listQuery .= ' AND  vtiger_visitingorder.related_to > 0  AND (vtiger_visitingorder.modulestatus =\'c_complete\' OR vtiger_visitingorder.modulestatus =\'a_normal\')  GROUP BY vtiger_visitingorder.visitingorderid ORDER BY '. $orderBy . ' ' .$sortOrder;
                 /* //根据搜索条件查出满足条件的 userid Start

                //第一步替换sql 添加 MAX(vtiger_visitsign.userid) as queryid （提单人或者陪同人id）
                $listQueryID = str_replace('SELECT vtiger_visitingorder.signaddress','SELECT  MAX(vtiger_visitsign.userid) as queryid,vtiger_visitingorder.signaddress',$listQuery);
                //第二步替换掉陪同人条件限制（同一单可以是陪同人也可以是提单人）
                $listQueryID=str_replace('(vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.visitsigntype=\'陪同人\')',' (vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.signnum=1) ',$listQueryID);
                //第三步以（陪同人或者是提单人去重）
                $listQueryID.= '  AND  vtiger_visitingorder.related_to > 0  GROUP BY  vtiger_visitsign.userid ';
                //第四步  获取 userid
                $listQueryID = ' SELECT queryid FROM ('.$listQueryID.') as a  ';

                 //根据搜索条件查出满足条件的 userid End


                // 第五步 获取 筛选条件 以及组装日期为当前开始日期 之前的三个月日期条件
                $wheres=$this->getSearchWhereAboutThreeMonthsVisit();
                $wheres =  empty($wheres) ? '1=1': $wheres;
                //第六步 根据第五步和第四步获取的条件查询开始日期以前的三个月的 客户拜访单并以客户id 去重
                $queryrelated_to = ' SELECT MAX( vo.related_to) as related_to  FROM  vtiger_visitsign as vs LEFT JOIN vtiger_visitingorder as vo  ON vo.visitingorderid = vs.visitingorderid  WHERE  vs.signnum=1 AND vs.userid IN('.$listQueryID.')  AND  '.$wheres.' GROUP BY  vo.related_to  ';
                //第七步  根据查重要求 开始时间以前三个月的客户拜访单 在现在查询区间不显示 所以 正常区间查出的 客户拜访单应该不在 第六步内
                $listQuery .= ' AND  vtiger_visitingorder.related_to NOT IN ('.$queryrelated_to.') ';
                //第八步  去除同一拜访单去重
                $listQuery .= ' AND  vtiger_visitingorder.related_to > 0  GROUP BY vtiger_visitingorder.visitingorderid  ';//加上分组来去重
                // 同一个（提单人或者陪同人）和客户去重
                $listQuery = str_replace('SELECT vtiger_visitingorder.signaddress','SELECT  vtiger_visitsign.userid as duplicateuserid ,vtiger_visitingorder.signaddress',$listQuery);
                $listQuery = ' SELECT * FROM ('.$listQuery.') as  result GROUP BY result.related_to_reference ,result.duplicateuserid ORDER BY '. $orderBy . ' ' .$sortOrder;
                //数据查出之后再以客户去重
                //$listQuery = ' SELECT * FROM ('.$listQuery.') as  result GROUP BY result.related_to_reference ORDER BY '. $orderBy . ' ' .$sortOrder;*/

            }else{
                $listQuery .= ' AND  vtiger_visitingorder.related_to > 0  GROUP BY vtiger_visitingorder.visitingorderid ORDER BY '. $orderBy . ' ' .$sortOrder;//加上分组来去重
            }

            // 更换查询出 拜访单陪同人签到时间记录
            // cxh 注释 $listQuery=str_replace('vtiger_visitsign ON  vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','(	select vtiger_visitsign.* from vtiger_visitsign left join vtiger_visitingorder on vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid where vtiger_visitsign.visitsigntype = \'陪同人\' and vtiger_visitsign.issign=1 and vtiger_visitsign.signnum =1 group by vtiger_visitsign.visitingorderid) as vtiger_visitsign ON vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid',$listQuery);
            $listQuery=str_replace('vtiger_visitsign ON  vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','vtiger_visitsign ON (vtiger_visitsign.visitsigntype = \'陪同人\' AND vtiger_visitsign.issign = 1 AND vtiger_visitsign.signnum = 1 AND vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid) LEFT JOIN vtiger_visitsign as person_type ON person_type.visitingorderid = vtiger_visitingorder.visitingorderid  ',$listQuery);
            //替换成 用于 是提单人 还是陪同人筛选的条件
            $listQuery=str_replace('vtiger_visitsign.visitsigntype LIKE','person_type.visitsigntype LIKE',$listQuery);
            $listQuery=str_replace('AND vtiger_visitsign.visitsigntype IS NOT NULL','AND person_type.visitsigntype IS NOT NULL',$listQuery);
            // 如果筛选条件中包含了 签到人类型则 添加下面的 签到人在部门里的条件
            if(strpos($listQuery,'AND person_type.visitsigntype IS NOT NULL')){
                $listQuery=str_replace('GROUP BY',' AND person_type.userid IN('.implode(',',$_REQUEST['alluserid']).') GROUP BY   ',$listQuery);
            }
            $viewid = ListViewSession::getCurrentView($moduleName);

            ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

            // 如果是同一客户拜访单去重
            if(strpos($_REQUEST['BugFreeQuery'],'duplicateremoval')){
                $listQuery=" SELECT * FROM  (".$listQuery.") as a  GROUP BY  a.related_to_reference ORDER BY  a.visitingorderid  DESC  LIMIT $startIndex,".($pageLimit);
            }else{
                $listQuery .= " LIMIT $startIndex,".($pageLimit);
            }

            $listResult = $db->pquery($listQuery, array());


            $index = 0;
            while($rawData=$db->fetch_array($listResult)) {
                // 判断是否是陪同人 2016-7-14 周海
                $rawData['t_accompany'] = '提单人';
                if (!empty($rawData['accompany'])) {
                    $accompanyArr = explode(' |##| ', $rawData['accompany']);
                    if (in_array($current_user->id, $accompanyArr)) {
                        $rawData['t_accompany'] = '陪同人';
                    }
                }
                $rawData['id'] = $rawData['visitingorderid'];
                $listViewRecordModels[$rawData['visitingorderid']] = $rawData;
            }

            return $listViewRecordModels;
            // 正常查询
        }else{
            if($this->isFromMobile==1){
                return $this->getListViewEntriesforMobile($pagingModel,$request);
            }
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

                $orderBy = 'visitingorderid';
                //$orderBy = 'vtiger_crmentity.modifiedtime';
                $sortOrder = 'DESC';
            }
            $this->getSearchWhere();
            $listQuery = $this->getQuery();

            $listQuery.=$this->getUserWhere();
            global $current_user;
            if(!$_REQUEST['alluserid']){
                $_REQUEST['alluserid']='1==1';
            }

            $startIndex = $pagingModel->getStartIndex();
            $pageLimit = $pagingModel->getPageLimit();
            //$listQuery=str_replace('issign=1','vtiger_visitingorder.issign=1',$listQuery);
            $listQuery=str_replace('as signtime,','as signtimes,',$listQuery);
            $listQuery=str_replace('as signaddress,','as signaddresss,',$listQuery);
            $listQuery=str_replace('vtiger_visitingorder.modulestatus,','vtiger_visitingorder.modulestatus,vtiger_visitingorder.isstrangevisit,(SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id=vtiger_visitingorder.extractid LIMIT 1) as email,',$listQuery);
//            $listQuery=str_replace('vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','(vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.visitsigntype=\'陪同人\')',$listQuery);
            // 更换查询出 拜访单陪同人签到时间记录
            $listQuery=str_replace('vtiger_visitsign ON  vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','vtiger_visitsign ON (vtiger_visitsign.visitsigntype = \'陪同人\' AND vtiger_visitsign.issign = 1 AND vtiger_visitsign.signnum = 1 AND vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid) LEFT JOIN vtiger_visitsign as person_type ON person_type.visitingorderid = vtiger_visitingorder.visitingorderid  ',$listQuery);
            //替换成 用于 是提单人 还是陪同人筛选的条件
            $listQuery=str_replace('vtiger_visitsign.visitsigntype LIKE','person_type.visitsigntype LIKE',$listQuery);
            $listQuery=str_replace('AND vtiger_visitsign.visitsigntype IS NOT NULL','AND person_type.visitsigntype IS NOT NULL',$listQuery);
            $listQuery=str_replace(" FROM vtiger_visitingorder "," FROM vtiger_visitingorder left join vtiger_users on vtiger_visitingorder.extractid=vtiger_users.id",$listQuery);
            $listQuery .= ' GROUP BY visitingorderid  ORDER BY '. $orderBy . ' ' .$sortOrder;
//            $listQuery .= ' GROUP BY vtiger_visitingorder.visitingorderid ORDER BY '. $orderBy . ' ' .$sortOrder;//加上分组来去重

            // 如果筛选条件中包含了 签到人类型则 添加下面的 签到人在部门里的条件
            if(strpos($listQuery,'AND person_type.visitsigntype IS NOT NULL') && $_REQUEST['alluserid']!='1=1'){
                $listQuery=str_replace('GROUP BY',' AND person_type.userid IN('.implode(',',$_REQUEST['alluserid']).') GROUP BY  ',$listQuery);
            }
            $viewid = ListViewSession::getCurrentView($moduleName);

            ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

            $listQuery .= " LIMIT $startIndex,".($pageLimit);

            //echo $listQuery;die();

            $listResult = $db->pquery($listQuery, array());


            $recordModel=new Vtiger_Record_Model();
            $isStrangeVisit=$recordModel->personalAuthority('VisitingOrder','setStrangeVisit');
            $index = 0;
            while($rawData=$db->fetch_array($listResult)) {
                // 判断是否是陪同人 2016-7-14 周海
                $rawData['t_accompany'] = '提单人';
                if (!empty($rawData['accompany'])) {
                    $accompanyArr = explode(' |##| ', $rawData['accompany']);
                    if (in_array($current_user->id, $accompanyArr)) {
                        $rawData['t_accompany'] = '陪同人';
                    }
                }
                $rawData['id'] = $rawData['visitingorderid'];
                if($isStrangeVisit){
                    $rawData['isstrangevisit']=$rawData['isstrangevisit']==0?1:2;
                }else{
                    $rawData['isstrangevisit']=0;
                }
                $listViewRecordModels[$rawData['visitingorderid']] = $rawData;
            }

            return $listViewRecordModels;
        }


    }
    // 的到过滤的sql
    function  getSearchWhereAboutThreeMonthsVisit(){
        $sqlStr = '';
        $BugFreeQuery=isset($_REQUEST['BugFreeQuery']) ? $_REQUEST['BugFreeQuery']:'';
        if(!empty($BugFreeQuery)){
            $BugFreeQuery=json_decode($BugFreeQuery,true);
            if(isset($BugFreeQuery['BugFreeQuery[queryRowOrder]'])){
                $SearchConditionRow=$BugFreeQuery['BugFreeQuery[queryRowOrder]'];
                $SearchConditionRow=explode(',',$SearchConditionRow);
                if(is_array($SearchConditionRow)&&!empty($SearchConditionRow)){
                    foreach($SearchConditionRow as $key=>$val){
                        $val=str_replace('SearchConditionRow','',$val);
                        // 如果包含是 过滤规则 则不在这里组装这个过滤规则的sql语句
                        $searchKey=$BugFreeQuery['BugFreeQuery[field'.$val.']'];
                        $operator=$BugFreeQuery['BugFreeQuery[operator'.$val.']'];
                        $searchValue=$BugFreeQuery['BugFreeQuery[value'.$val.']'];
                        //如果选择指定的提单人 或者 拜访人
                        if(strpos($searchKey,'tiger_visitingorder.extractid') || strpos($searchKey,'tiger_visitingorder.accompany')){
                            if(!empty($searchValue) && !empty($sqlStr)){
                                $sqlStr.=",".$searchValue;
                            }else if(!empty($searchValue)){
                                $sqlStr=$searchValue;
                            }
                        }
                    }
                }
            }
        }
        return $sqlStr;
    }
    public function getListViewCount() {
        if(0==$this->isAllCount && 0==$this->isFromMobile){
            return 0;
        }
        $db = PearDatabase::getInstance();
        $moduleName ='VisitingOrder';

        if(strpos($_REQUEST['BugFreeQuery'],'vtiger_visitingorder.filteringrules')) {
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

                $orderBy = 'visitingorderid';
                //$orderBy = 'vtiger_crmentity.modifiedtime';
                $sortOrder = 'DESC';
            }
            $this->getSearchWhereRules();
            $listQuery = $this->getQuery();

            if(strpos($_REQUEST['BugFreeQuery'],'threemonthsvisit')){

            }
            $listQuery.=$this->getUserWhere();


            $listQuery=str_replace('as signtime,','as signtimes,',$listQuery);
            $listQuery=str_replace('as signaddress,','as signaddresss,',$listQuery);
            $listQuery=str_replace('vtiger_visitingorder.modulestatus,','vtiger_visitingorder.modulestatus,(SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id=vtiger_visitingorder.extractid LIMIT 1) as email,',$listQuery);
            //$listQuery=str_replace('vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','(vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.visitsigntype=\'陪同人\')',$listQuery);
            // 如果是三个月去重
            if(strpos($_REQUEST['BugFreeQuery'],'threemonthsvisit')){
                $listQueryID = str_replace('SELECT vtiger_visitingorder.signaddress','SELECT  vtiger_visitsign.userid as queryid,vtiger_visitingorder.signaddress',$listQuery);
                $listQueryID.=" AND vtiger_visitingorder.related_to > 0  AND  vtiger_visitsign.signnum=1 AND (vtiger_visitingorder.modulestatus ='c_complete' OR vtiger_visitingorder.modulestatus ='a_normal') ";
                $wheres=$this->getSearchWhereAboutThreeMonthsVisit();
                if(!empty($wheres)){
                    $listQueryID.=' AND vtiger_visitsign.userid IN ('.$wheres.')  ';
                }
                $listResultUserAccount = $db->pquery($listQueryID, array());
                $listQueryIDStr='';
                while($rawData=$db->fetch_array($listResultUserAccount)) {
                    //检查该用户是否存在 这个开始日期以前三个月同一客户拜访单的数据。
                    $date=strtotime("-3 month",strtotime($rawData['startdate']));
                    $date="'".date("Y-m-d",$date)."'";
                    $rawstartdate="'".$rawData['startdate']."'";
                    $related_to_reference=$rawData['related_to_reference'];
                    $queryid=$rawData['queryid'];
                    $sqlThreeMonthsAgo=" SELECT * FROM  vtiger_visitingorder as vo  LEFT JOIN  vtiger_crmentity as c ON  vo.visitingorderid = c.crmid  LEFT JOIN  vtiger_visitsign as vs ON vs.visitingorderid=vo.visitingorderid  WHERE 1=1 AND c.deleted=0 AND vo.related_to> 0  AND  vo.startdate >= {$date} AND  vo.startdate <{$rawstartdate}  AND vo.related_to={$related_to_reference}  AND  vs.userid= {$queryid} AND vs.userid> 0  AND (vo.modulestatus ='c_complete' OR vo.modulestatus ='a_normal')  LIMIT 1 ";
                    $listResultDATA = $db->pquery($sqlThreeMonthsAgo, array());
                    //echo "<pre>";
                    //var_dump($listResultDATA);die();
                    //echo  $db->num_rows($listResultDATA);
                    if($db->num_rows($listResultDATA)>0){
                        $listQueryIDStr.=$rawData['visitingorderid'].',';
                    }
                }

                if(!empty($listQueryIDStr)){
                    $listQueryIDStr = trim($listQueryIDStr,',');
                    $listQuery.= '  AND  vtiger_visitingorder.visitingorderid NOT IN ('.$listQueryIDStr.') ';
                }
                $listQuery .= ' AND  vtiger_visitingorder.related_to > 0 AND (vtiger_visitingorder.modulestatus =\'c_complete\' OR vtiger_visitingorder.modulestatus =\'a_normal\') GROUP BY vtiger_visitingorder.visitingorderid ';
                /*$listQueryID = str_replace('SELECT vtiger_visitingorder.signaddress','SELECT  MAX(vtiger_visitsign.userid) as queryid,vtiger_visitingorder.signaddress',$listQuery);
                $listQueryID=str_replace('(vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.visitsigntype=\'陪同人\')',' (vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.signnum=1) ',$listQueryID);
                $listQueryID.= '  AND  vtiger_visitingorder.related_to > 0  GROUP BY  vtiger_visitsign.userid ';
                $listQueryID = ' SELECT queryid FROM ('.$listQueryID.') as a  ';
                $wheres=$this->getSearchWhereAboutThreeMonthsVisit();
                $wheres =  empty($wheres) ? '1=1': $wheres;
                $queryrelated_to = ' SELECT MAX( vo.related_to) as related_to  FROM  vtiger_visitsign as vs LEFT JOIN vtiger_visitingorder as vo  ON vo.visitingorderid = vs.visitingorderid  WHERE  vs.signnum=1 AND vs.userid IN('.$listQueryID.')  AND  '.$wheres.' GROUP BY  vo.related_to  ';
                $listQuery .= ' AND  vtiger_visitingorder.related_to NOT IN ('.$queryrelated_to.') ';
                $listQuery .= ' AND  vtiger_visitingorder.related_to > 0  GROUP BY vtiger_visitingorder.visitingorderid  ';//加上分组来去重
                // 同一个（提单人或者陪同人）和客户去重
                $listQuery = str_replace('SELECT vtiger_visitingorder.signaddress','SELECT  vtiger_visitsign.userid as duplicateuserid ,vtiger_visitingorder.signaddress',$listQuery);
                $listQuery = ' SELECT * FROM ('.$listQuery.') as  result GROUP BY result.related_to_reference ,result.duplicateuserid ORDER BY '. $orderBy . ' ' .$sortOrder;
                //直接以客户id去重
                //$listQuery = ' SELECT * FROM ('.$listQuery.') as  result GROUP BY result.related_to_reference  ';*/
            } else {
                $listQuery .= ' AND  vtiger_visitingorder.related_to > 0  GROUP BY vtiger_visitingorder.visitingorderid ';//加上分组来去重
            }
            // 更换查询出 拜访单陪同人签到时间记录
            $listQuery=str_replace('vtiger_visitsign ON  vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','vtiger_visitsign ON (vtiger_visitsign.visitsigntype = \'陪同人\' AND vtiger_visitsign.issign = 1 AND vtiger_visitsign.signnum = 1 AND vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid) LEFT JOIN vtiger_visitsign as person_type ON person_type.visitingorderid = vtiger_visitingorder.visitingorderid  ',$listQuery);
            //替换成 用于 是提单人 还是陪同人筛选的条件
            $listQuery=str_replace('vtiger_visitsign.visitsigntype LIKE','person_type.visitsigntype LIKE',$listQuery);
            $listQuery=str_replace('AND vtiger_visitsign.visitsigntype IS NOT NULL','AND person_type.visitsigntype IS NOT NULL',$listQuery);
            // 如果筛选条件中包含了 签到人类型则 添加下面的 签到人在部门里的条件
            if(strpos($listQuery,'AND person_type.visitsigntype IS NOT NULL')){
                $listQuery=str_replace('GROUP BY',' AND person_type.userid IN('.implode(',',$_REQUEST['alluserid']).')  GROUP BY ',$listQuery);
            }
            $viewid = ListViewSession::getCurrentView($moduleName);

            ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,


            if(strpos($_REQUEST['BugFreeQuery'],'duplicateremoval')){
                $listQuery=" SELECT count(1) FROM  (".$listQuery.") as a  GROUP BY  a.related_to_reference ";
            }else if(strpos($_REQUEST['BugFreeQuery'],'threemonthsvisit')){
            }
            $listResult = $db->pquery($listQuery, array());
            return $db->num_rows($listResult);
            // 正常查询
        }else {
            $queryGenerator = $this->get('query_generator');
            //print_r(debug_backtrace(0));
            //搜索条件
            //$this->getSearchWhere();
            //用户条件
            $where = $this->getUserWhere();
            //$where.= ' AND accountname is NOT NULL';
            $queryGenerator->addUserWhere($where);
            $listQuery = $queryGenerator->getQueryCount();
            // CXH 注释掉内容
            //$listQuery = str_replace('vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid', '(vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.visitsigntype=\'陪同人\')', $listQuery);
            $listQuery=str_replace('vtiger_visitsign ON  vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','vtiger_visitsign ON (vtiger_visitsign.visitsigntype = \'陪同人\' AND vtiger_visitsign.issign = 1 AND vtiger_visitsign.signnum = 1 AND vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid) LEFT JOIN vtiger_visitsign as person_type ON person_type.visitingorderid = vtiger_visitingorder.visitingorderid  ',$listQuery);
            //替换成 用于 是提单人 还是陪同人筛选的条件
            $listQuery=str_replace('vtiger_visitsign.visitsigntype LIKE','person_type.visitsigntype LIKE',$listQuery);
            $listQuery=str_replace('AND vtiger_visitsign.visitsigntype IS NOT NULL','AND person_type.visitsigntype IS NOT NULL',$listQuery);
            //echo $listQuery.'<br>';die();
            $listQuery .= 'GROUP BY vtiger_visitingorder.visitingorderid';//加上分组来去重
            // 如果筛选条件中包含了 签到人类型则 添加下面的 签到人在部门里的条件
            if(strpos($listQuery,'AND person_type.visitsigntype IS NOT NULL')){
                $listQuery=str_replace('GROUP BY',' AND person_type.userid IN('.implode(',',$_REQUEST['alluserid']).')  GROUP BY ',$listQuery);
            }
            $listResult = $db->pquery($listQuery, array());
            //var_dump($db->num_rows($listResult));
            return $db->num_rows($listResult);
            //return $db->query_result($listResult, 0, 'counts');
        }
    }

    /**
     * 移动端调用
     */
    public function getListViewEntriesforMobile($pagingModel,$request=array()){
        $db=PearDatabase::getInstance();
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
            $orderBy = 'visitingorderid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $queryGenerator = $this->get('query_generator');
        $listQuery ='SELECT vtiger_visitingorder.related_to,vtiger_visitingorder.subject,vtiger_visitingorder.modulestatus,ifnull((SELECT vtiger_wexinpicture.picturepath FROM vtiger_wexinpicture WHERE vtiger_wexinpicture.userid=vtiger_visitingorder.extractid LIMIT 1),1) as email,vtiger_visitingorder.accompany,vtiger_visitingorder.startdate,vtiger_visitingorder.enddate,vtiger_visitingorder.purpose,vtiger_visitingorder.contacts,vtiger_visitingorder.outobjective,vtiger_visitingorder.accountnamer,vtiger_visitingorder.visitingorderid,vtiger_users.last_name as extractname 
                FROM vtiger_visitingorder  LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid 
                LEFT JOIN vtiger_visitsign ON vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid
                LEFT JOIN vtiger_users on vtiger_visitingorder.extractid=vtiger_users.id
';
        $whereClause=$queryGenerator->getWhereClause();
        $listQuery.=$whereClause.' AND vtiger_visitsign.signnum=1';
        $listQuery.=$this->getUserWhere();
        global $current_user;
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= ' GROUP BY vtiger_visitingorder.visitingorderid ORDER BY '. $orderBy . ' ' .$sortOrder;
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        $listResult = $db->pquery($listQuery, array());
        //$listViewRecordModels=array();
        while($rawData=$db->fetchByAssoc($listResult)) {
            // 判断是否是陪同人 2016-7-14 周海
            $rawData['t_accompany'] = '提单人';
            if (!empty($rawData['accompany'])) {
                $accompanyArr = explode(' |##| ', $rawData['accompany']);
                $result = $db->pquery("select last_name from vtiger_users where id in(".implode(",",$accompanyArr).')',array());
                if($db->num_rows($result)){
                    $accompanyStr = '';
                    while ($row = $db->fetchByAssoc($result)){
                        $accompanyStr .=$row['last_name'].' ';
                    }
                    $rawData['accompanyNames'] = rtrim($accompanyStr,' ');
                }

                if (in_array($current_user->id, $accompanyArr)) {
                    $rawData['t_accompany'] = '陪同人';
                }
            }

            $lang = translateLng("Accounts");
            $rawData['modulestatuslng'] = $lang[$rawData['modulestatus']];

            $rawData['id'] = $rawData['visitingorderid'];
            $listViewRecordModels[$rawData['visitingorderid']] = $rawData;
        }
        return $listViewRecordModels;
    }
}
