<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Accounts_ListView_Model extends Vtiger_ListView_Model {

    public $counts = 0;

    /**
     * Function to get the list of Mass actions for the module
     * @param <Array> $linkParams
     * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
     */
    public function getListViewMassActions($linkParams) {
        $massActionLinks = parent::getListViewMassActions($linkParams);

        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $emailModuleModel = Vtiger_Module_Model::getInstance('Emails');

        if ($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
            $massActionLink = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_SEND_EMAIL',
                'linkurl' => 'javascript:Vtiger_List_Js.triggerSendEmail("index.php?module=' . $this->getModule()->getName() . '&view=MassActionAjax&mode=showComposeEmailForm&step=step1","Emails");',
                'linkicon' => ''
            );
            $massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        $SMSNotifierModuleModel = Vtiger_Module_Model::getInstance('SMSNotifier');
        if ($currentUserModel->hasModulePermission($SMSNotifierModuleModel->getId())) {
            $massActionLink = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_SEND_SMS',
                'linkurl' => 'javascript:Vtiger_List_Js.triggerSendSms("index.php?module=' . $this->getModule()->getName() . '&view=MassActionAjax&mode=showSendSMSForm","SMSNotifier");',
                'linkicon' => ''
            );
            $massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        $moduleModel = $this->getModule();
        if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
            $massActionLink = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_TRANSFER_OWNERSHIP',
                'linkurl' => 'javascript:Vtiger_List_Js.triggerTransferOwnership("index.php?module=' . $moduleModel->getName() . '&view=MassActionAjax&mode=transferOwnership")',
                'linkicon' => ''
            );
            $massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        return $massActionLinks;
    }

    /**
     * Function to get the list of listview links for the module
     * @param <Array> $linkParams
     * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
     */
    function getListViewLinks($linkParams) {
        $links = parent::getListViewLinks($linkParams);

        $index = 0;
        foreach ($links['LISTVIEWBASIC'] as $link) {
            if ($link->linklabel == 'Send SMS') {
                unset($links['LISTVIEWBASIC'][$index]);
            }
            $index++;
        }
        return $links;
    }

    //????????????????????????  #??????crm??????$request??????---2015-12-22 ?????????
    public function getListViewEntries($pagingModel, $request = array()) {
        $db = PearDatabase::getInstance();
        $moduleName = 'Accounts';

        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if (!empty($request)) {
            if (isset($request['filter'])) {
                $_REQUEST['filter'] = $request['filter'];
            }
        }
        //List view will be displayed on recently created/modified records
        //????????????????????????????????????????????????  ---???????????????
        /*
          if(empty($orderBy) && empty($sortOrder)){

          $orderBy = 'vtiger_account.accountid';
          //$orderBy = 'vtiger_crmentity.modifiedtime';
          $sortOrder = 'DESC';
          }
          $this->getSearchWhere();
          $listQuery = $this->getQuery();

          $listQuery.=$this->getUserWhere();
         */


        global $current_user;

        //???????????????????????????????????? steel 2015-05-15
        if ($_REQUEST['filter'] == "changeHistory") {
            $listQuery = "SELECT
                            vtiger_account.accountname,
                            vtiger_account.accountid,
                            vtiger_accountsmowneridhistory.id,
                            vtiger_accountsmowneridhistory.createdtime,
                            IFNULL( ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[??????]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_accountsmowneridhistory.oldsmownerid ), '--' ) AS olsuser,
                            IFNULL( ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[??????]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_accountsmowneridhistory.newsmownerid ), '--' ) AS newuser,
                            IFNULL( ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[??????]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_accountsmowneridhistory.modifiedby ), '--' ) AS modiuser
                        FROM `vtiger_accountsmowneridhistory`
                        LEFT JOIN vtiger_account  ON vtiger_accountsmowneridhistory.accountid = vtiger_account.accountid
                        WHERE  vtiger_accountsmowneridhistory.accountid IS NOT NULL ";
            $where = getAccessibleUsers('Accounts', 'List', false);
            if ($where != '1=1') {
                $listQuery .= " AND (vtiger_accountsmowneridhistory . newsmownerid  {$where} OR vtiger_accountsmowneridhistory . oldsmownerid  {$where} ) ";
            }
            $this->getSearchWhere();
            $queryGenerator = $this->get('query_generator');
            $searchwhere = $queryGenerator->getSearchWhere();
            if (!empty($searchwhere)) {
                $listQuery .= ' and ' . $searchwhere;
            }
            $orderBy = 'vtiger_accountsmowneridhistory.id';
            //$orderBy = 'vtiger_crmentity.modifiedtime';
            $sortOrder = 'DESC';
        } elseif (in_array($_REQUEST['src_module'], array('VisitingOrder', 'Staypayment', 'Billing'))) {
            $listQuery = $this->getVisitingOrderListSQL();
            //$listQuery.=$this->getUseListAuthority();
            //$orderBy = 'vtiger_crmentity.crmid';
            $orderBy = 'accountid';
            $sortOrder = 'DESC';
            //$listQuery.=$this->getSearchAccountForPop();
        } else {
            if (empty($orderBy) && empty($sortOrder)) {
                //$orderBy = 'vtiger_account.accountid';
                //$orderBy = 'vtiger_crmentity.modifiedtime';
                $orderBy = 'vtiger_account.mtime';
                $sortOrder = 'DESC';
            }
            if ($_REQUEST['filter'] == 'nofifity') {
                $orderBy = 'vtiger_account.protectday';
                $sortOrder = 'ASC';
            }
            $this->getSearchWhere();
            $listQuery = $this->getQuery();
            $listQuery = str_replace("FROM vtiger_account", "FROM vtiger_account FORCE INDEX(accountcategory)", $listQuery);

            $listQuery .= $this->getUserWhere();
            //?????????????????????
            $listQuery .= ' AND vtiger_account.accountid>0';
        }

//        var_dump($listQuery);
//        die;




        //end steel
        //echo $listQuery;die;
        //$where = $this->channelWhereSql();
        //$listQuery .= $where;
        //echo $listQuery;die;
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' GROUP BY accountid';

        $listQuery .= ' ORDER BY ' . $orderBy . ' ' . $sortOrder;

        $viewid = ListViewSession::getCurrentView($moduleName);

        //ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session??????????????????,

        $listQuery .= " LIMIT $startIndex," . ($pageLimit);

//        echo $listQuery;die();
        //print_r($current_user);die;

//        var_dump($listQuery);
//        die;

        $listResult = $db->pquery($listQuery, array());
        $numRows = $db->num_rows($listResult);
        if ($numRows) {
            $this->counts = $numRows;
        }
        $index = 0;
        /*
          while($rawData=$db->fetch_array($listResult)) {
          $rawData['id'] = $rawData['accountid'];
          $listViewRecordModels[$rawData['accountid']] = $rawData;
          $listViewRecordModels[$rawData['accountid']]['isown']= 0;
          if(!empty($current_user->subordinate_users) && in_array($rawData['smownerid_owner'],$current_user->subordinate_users)){
          $listViewRecordModels[$rawData['accountid']]['isown']= 1;
          }
          }
         */
        if ($_REQUEST['filter'] == "changeHistory") {
            $id = 'id';
        } else {
            $id = 'accountid';
        }
        $showflag = $_REQUEST['filter'] == 'overt' ? true : false;
        /*$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $protect=$moduleModel->isPermitted('Protect');*/
        $recordModel=Vtiger_Record_Model::getCleanInstance($moduleName);
        $protect=$recordModel->getProtectnum();
        while ($rawData = $db->fetch_array($listResult)) {
            $rawData['id'] = $rawData[$id];
            $rawData['accountranklng'] = getAccountrank()[$rawData['accountrank']];
            $listViewRecordModels[$rawData[$id]] = $rawData;
            $listViewRecordModels[$rawData[$id]]['isown'] = 0;
            if ($protect) {
                $listViewRecordModels[$rawData[$id]]['isown'] = 1;
            }
            if ($showflag) {
                $listViewRecordModels[$rawData[$id]]['phone'] = '?????????????????????';
                $listViewRecordModels[$rawData[$id]]['mobile'] = '?????????????????????';
            }
        }
        return $listViewRecordModels;
    }

    // ?????????????????????????????? ?????????????????????????????????????????????????????? ????????????
    public function channelWhereSql() {
        global $current_user;
        // ????????????????????????????????????
        /* $sql = "select departmentid from vtiger_departments where parentdepartment LIKE '%H147%'";
          $db = PearDatabase::getInstance();
          $listResult = $db->query($sql);
          $res_cnt = $db->num_rows($listResult);

          $departmentdData = array();
          if ($res_cnt > 0) {
          while ($rawData = $db->fetch_array($listResult)) {
          $departmentdData[] = $rawData['departmentid'];
          }
          } */
        /*
          $departmentdData = getChannelDepart();

          $where = '';
          if ($current_user->id != 1) {
          if (in_array($current_user->departmentid, $departmentdData) ) {
          if ($_REQUEST['filter']=="overt") {
          throw new Exception("??????????????????????????????");
          }
          $where = " AND vtiger_user2department.departmentid IN (";
          } else {
          $where = " AND vtiger_user2department.departmentid NOT IN (";
          }
          foreach ($departmentdData as $key=>$value) {
          $departmentdData[$key] = "'" .$value. "'";
          }
          $where .= implode(',', $departmentdData) . ")";
          } */
        return $where;
    }

    public function getAdvancedLinks() {
        $moduleModel = $this->getModule();

        $createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView');
        $advancedLinks = array();
        //???????????????
        $White_ListPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'White_List');
        if ($White_ListPermission) {
            $advancedLinks[] = array(
                'linktype' => 'LISTVIEW',
                'linklabel' => 'LBL_WHITE_LIST',
                'linkurl' => 'javascript:Vtiger_List_Js.whiteListRecords("index.php?module=' . $moduleModel->get('name') . '&action=WhiteList");',
                'linkicon' => '',
            );
        }
        $importPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Import');
        if ($importPermission && $createPermission) {
            $advancedLinks[] = array(
                'linktype' => 'LISTVIEW',
                'linklabel' => 'LBL_IMPORT',
                'linkurl' => $moduleModel->getImportUrl(),
                'linkicon' => ''
            );
        }

        $exportPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Export');


        if ($exportPermission) {
            $advancedLinks[] = array(
                'linktype' => 'LISTVIEW',
                'linklabel' => 'LBL_EXPORT',
                'linkurl' => 'javascript:Vtiger_List_Js.triggerExportAction("' . $this->getModule()->getExportUrl() . '")',
                'linkicon' => ''
            );
        }

        $duplicatePermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'DuplicatesHandling');
        if ($duplicatePermission) {
            $advancedLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_FIND_DUPLICATES',
                'linkurl' => 'Javascript:Vtiger_List_Js.showDuplicateSearchForm("index.php?module=' . $moduleModel->getName() .
                '&view=MassActionAjax&mode=showDuplicatesSearchForm")',
                'linkicon' => ''
            );
        }

        return $advancedLinks;
    }

    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');

        if (!empty($sourceModule)) {
            return $queryGenerator->getModule()->getPopupFields();
        } else {

            $list = $queryGenerator->getModule()->getListFields();
            $temp = array();
            foreach ($list as $fields) {
                $temp[$fields['fieldlabel']] = $fields;
            }
            return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
        return Array(
            'Account Name' => 'accountname',
            'Website' => 'website',
            'Phone' => 'phone',
            'Account No' => 'account_no',
            'industry' => 'industry',
            'Assigned To' => 'smownerid',
            'Last Modified By' => 'modifiedby'
        );
    }

    public function getUserWhere() {
        global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $sourceModule = $this->get('src_module');
        $listQuery = ' ';
        if ($_REQUEST['filter'] == 'overt') {
            $listQuery .= ' and vtiger_account.accountcategory=2';
        }elseif ($_REQUEST['filter'] == 'mobileSearch'){

        } else {                                                            //end
            if ($_REQUEST['filter'] == 'temporary') {
                $listQuery .= ' and vtiger_account.accountcategory=1';
            }  else {
                if ($sourceModule == 'Leads') {
                    $listQuery .= ' and vtiger_account.accountcategory=2';
                }else{
                    $listQuery .= ' and vtiger_account.accountcategory=0';
                }
            }
            //?????????*???????????????????????????*???????????????

            if (!empty($searchDepartment) && $searchDepartment != 'H1') {  //20150427 young ???????????????H1??????
                $userid = getDepartmentUser($searchDepartment);
                $where = getAccessibleUsers('Accounts', 'List', true);
                if ($where != '1=1') {
                    $where = array_intersect($where, $userid);
                } else {
                    $where = $userid;
                }
                $where=!empty($where)?$where:array(-1);
                //$accountList = $this->getModule()->getAllShareAccount();//?????????????????????????????????
                $listQuery .= ' and vtiger_crmentity.smownerid in ('.implode(',',$where).')';

            }else{
                if ($sourceModule == 'Leads') {
                   // $listQuery .= str_replace('and vtiger_account.accountcategory=0',' and vtiger_account.accountcategory=2 ',$listQuery);
                    return $listQuery;
                }
                //getDepartment
                $where = getAccessibleUsers();
                $populeserviceid = array('ServiceContracts', 'ServiceMaintenance', 'ServiceAssignRule', 'IronAccount', "AccountPlatform", "ProductProvider", "RefillApplication");

                if ($where != '1=1') {
                    if ($sourceModule != 'ServiceAssignRule') {

                        if (!empty($sourceModule) && in_array($sourceModule, $populeserviceid)) {
                            $db = PearDatabase::getInstance();
                            $arr_role = array();
                            $result_roleid = $db->pquery("SELECT roleid FROM vtiger_role WHERE rolename LIKE '%??????%'", array());
                            $num_role = $db->num_rows($result_roleid);
                            if ($num_role > 0) {
                                for ($i = 0; $i < $num_role; $i++) {
                                    $arr_role[] = $db->query_result($result_roleid, $i, 'roleid');
                                }
                            }
                            $shareaccount = '';
                            $sharSourceModule = array("ServiceContracts", "AccountPlatform", "ProductProvider", "RefillApplication");
                            if (in_array($sourceModule, $sharSourceModule)) {
                                $shareaccount = $accountList = $this->getModule()->getShareAccount();
                            }

                            //?????????????????? gaocl add 2018/03/14
                            //$listQuery .= ' and (vtiger_crmentity.smownerid '.$where. ' OR vtiger_servicecomments.serviceid '.$where.')';
                            if (!in_array($current_user->roleid, $arr_role)) {
                                $listQuery .= ' and (vtiger_crmentity.smownerid ' . $where . $shareaccount . ')';
                            } else {
                                //$listQuery .= ' and vtiger_servicecomments.serviceid '.$where;
                                //$listQuery .= ' and (vtiger_crmentity.smownerid '.$where. ' OR vtiger_servicecomments.serviceid '.$where.$shareaccount.')';
                                $listQuery .= ' and (vtiger_crmentity.smownerid ' . $where . ' OR vtiger_account.serviceid ' . $where . $shareaccount . ')';
                            }
                            //$listQuery .= ' and (vtiger_crmentity.smownerid '.$where.' or EXISTS(select 1 from vtiger_servicecomments where vtiger_account.accountid=vtiger_servicecomments.related_to AND serviceid '.$where.'))';
                            //$listQuery .= ' and vtiger_crmentity.smownerid '.$where;
                            //????????????????????????
                        } else if ($sourceModule == 'VisitingOrder') {
                            $accountList = $this->getModule()->getShareAccount();

                            $listQuery .= ' and (vtiger_crmentity.smownerid ' . $where . $accountList . ')';
                        } else if ($sourceModule == 'Newinvoice') {
                            $db = PearDatabase::getInstance();
                            $arr_role = array();
                            $result_roleid = $db->pquery("SELECT roleid FROM vtiger_role WHERE rolename LIKE '%??????%'", array());
                            $num_role = $db->num_rows($result_roleid);
                            if ($num_role > 0) {
                                for ($i = 0; $i < $num_role; $i++) {
                                    $arr_role[] = $db->query_result($result_roleid, $i, 'roleid');
                                }
                            }
                            //$listQuery .= ' and (vtiger_crmentity.smownerid '.$where. ' OR vtiger_servicecomments.serviceid '.$where.')';
                            //?????????????????? gaocl add 2018/03/14
                            if (!in_array($current_user->roleid, $arr_role)) {
                                $listQuery .= ' and vtiger_crmentity.smownerid ' . $where;
                            } else {
                                //$listQuery .= ' and vtiger_servicecomments.serviceid '.$where;
                                //$listQuery .= ' and (vtiger_crmentity.smownerid '.$where. ' OR vtiger_servicecomments.serviceid '.$where.')';
                                $listQuery .= ' and (vtiger_crmentity.smownerid ' . $where . ' OR vtiger_account.serviceid ' . $where . ')';
                            }
                        } else {
                            $accountList = $this->getModule()->getAllShareAccount();
                            $listQuery .= ' and (vtiger_crmentity.smownerid ' . $where.$accountList.')';
                        }
                    }
                }

                if (in_array($sourceModule, $populeserviceid)) {

                    $listQuery = str_replace('and vtiger_account.accountcategory=0', ' ', $listQuery);
                }
            }
        }



        if ($_REQUEST['filter'] == 'white') {
            $listQuery .= ' and vtiger_account.protected=1';
        } elseif ($_REQUEST['filter'] == 'noseven') {
            $listQuery .= ' and vtiger_account.protectday<=7';
        } elseif ($_REQUEST['filter'] == 'recentFollowUp') {
            $datatime = date('Y-m-d', strtotime("-1 week"));
            $listQuery .= " and left(vtiger_account.lastfollowuptime,10)>='{$datatime}'";
        } elseif ($_REQUEST['filter'] == 'appnoseven') {
            $listQuery .= ' and vtiger_account.protectday<=7 and vtiger_crmentity.smownerid=' . $current_user->id;
        } elseif ($_REQUEST['filter'] == 'nofifity') {
            $listQuery .= ' and vtiger_account.protectday<=15';
        } elseif ($_REQUEST['filter'] == 'nohooked') {
            //????????????[???????????????40%,60%,80%???????????????????????????]
            $listQuery .= ' and vtiger_account.accountrank in(\'chan_notv\',\'forp_notv\',\'sixp_notv\',\'eigp_notv\',\'norm_isv\')';
        } elseif ($_REQUEST['filter'] == 'willhooked') {
            $listQuery .= ' and vtiger_account.accountrank="40"';
        } elseif ($_REQUEST['filter'] == 'ishooked' || $sourceModule == 'IronAccount') {
            //????????????[?????????]
            $listQuery .= ' and vtiger_account.accountrank in(\'spec_isv\',\'visp_isv\',\'wlad_isv\',\'wlvp_isv\',\'wlbr_isv\',\'wlsi_isv\',\'wlgo_isv\',\'iron_isv\',\'bras_isv\',\'silv_isv\',\'gold_isv\')';
        } elseif ($_REQUEST['filter'] == 'addaccouns') {
            $listQuery .= ' and vtiger_crmentity.createdtime>' . date('Y-m-d', strtotime('-7 day'));
        } elseif ($_REQUEST['filter'] == 'myaccounts') {
            $listQuery .= ' and vtiger_crmentity.smownerid=' . $current_user->id;
        } elseif ($_REQUEST['filter'] == 'intentionality'){
            $listQuery .= " and vtiger_account.intentionality!='zeropercentage' and vtiger_account.intentionality !='' and vtiger_account.intentionality is not null and vtiger_account.accountcategory not in (1,2)";
        }

        return $listQuery;
        //return '';
    }

    public function getListViewCount() {
        if(0==$this->isAllCount && 0==$this->isFromMobile){
            return 0;
        }
        $db = PearDatabase::getInstance();
        if ($_REQUEST['src_module'] == 'VisitingOrder') {
            $listQuery = $this->getVisitingOrderListCountSQL();
            $listResult = $db->pquery($listQuery, array());
            $counts = 0;
            while ($row = $db->fetch_array($listResult)) {
                $counts += $row['counts'];
            }
            return $counts>500?500:$counts;
        }
        //echo $listQuery.'<br>';die();
        //steel 2015-05-15 ??????
        if($_REQUEST['filter']=="changeHistory"){
            global $current_user;
            $listQuery="SELECT
                          count(1) AS counts
                        FROM `vtiger_accountsmowneridhistory`
                        LEFT JOIN vtiger_account  ON vtiger_accountsmowneridhistory.accountid = vtiger_account.accountid
                        WHERE vtiger_accountsmowneridhistory.accountid IS NOT NULL
                         ";
            $where=getAccessibleUsers('Accounts','List',false);
            if($where!='1=1') {
                $listQuery.=" AND (vtiger_accountsmowneridhistory . newsmownerid  {$where} OR vtiger_accountsmowneridhistory . oldsmownerid  {$where} ) ";
            }
            $this->getSearchWhere();
            $queryGenerator = $this->get('query_generator');
            $searchwhere=$queryGenerator->getSearchWhere();
            if(!empty($searchwhere)){
                $listQuery.=' and '.$searchwhere;
            }
        }else{
            $queryGenerator = $this->get('query_generator');
            //print_r(debug_backtrace(0));
            //????????????
            //$this->getSearchWhere();
            //????????????
            $where=$this->getUserWhere();
            //$where.= ' AND accountname is NOT NULL';
            $queryGenerator->addUserWhere($where);
            $listQuery =  $queryGenerator->getQueryCount();
            //$listQuery=str_replace('count(1) as counts','1',$listQuery);
            //$listQuery.=' LIMIT '.$this->startIndex.','.($this->pageLimit*6+2);
            //$num=$db->num_rows($db->pquery($listQuery));
            //return $num>$this->counts?$num:$this->counts;
        }
        //steel 2015-05-15
        $listQuery .= ' GROUP BY accountid';

        $listResult = $db->pquery($listQuery, array());
//        return $db->query_result($listResult, 0, 'counts');
        return $db->num_rows($listResult);
    }

    /**
     * ???????????????????????????
     * @return string
     */
    public function getVisitingOrderListSQL() {
        /* $query="SELECT vtiger_crmentity.crmid as accountid,concat(vtiger_crmentity.label,'-->[',if(vtiger_crmentity.setype='Accounts','??????',if(vtiger_crmentity.setype='Vendors','?????????','??????')),']') as accountname,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[??????]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,vtiger_crmentity.smownerid AS smownerid_id,vtiger_crmentity.setype AS modulename,0 AS serviceid FROM vtiger_crmentity
          LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid
          LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid=vtiger_crmentity.crmid
          LEFT JOIN vtiger_schoolrecruit ON vtiger_schoolrecruit.schoolrecruitid=vtiger_crmentity.crmid
          WHERE vtiger_crmentity.deleted=0 AND ((vtiger_crmentity.setype ='Accounts' AND vtiger_account.accountcategory=0) OR (vtiger_crmentity.setype='Vendors' AND vtiger_vendor.vendorstate='al_approval') OR
          vtiger_crmentity.setype='School')"; */
        $useListAuthority = $this->getUseListAuthority();
        $searchAccountForPop = $this->getSearchAccountForPop();
        $accountQuery = "SELECT vtiger_crmentity.crmid as accountid,concat(vtiger_crmentity.label,'-->[??????]') as accountname,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[??????]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,vtiger_crmentity.smownerid AS smownerid_id,vtiger_crmentity.setype AS modulename,0 AS serviceid FROM vtiger_crmentity 
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid 
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='Accounts' AND vtiger_account.accountcategory=0";
        /* $vendorsQuery="SELECT vtiger_crmentity.crmid as accountid,concat(vtiger_crmentity.label,'-->[?????????]') as accountname,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[??????]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,vtiger_crmentity.smownerid AS smownerid_id,vtiger_crmentity.setype AS modulename,0 AS serviceid FROM vtiger_crmentity
          LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid=vtiger_crmentity.crmid
          WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='Vendors' AND vtiger_vendor.vendorstate='al_approval'"; */
        /* $schoolQuery="SELECT vtiger_crmentity.crmid as accountid,concat(vtiger_crmentity.label,'-->',IF(vtiger_crmentity.setype='School','[??????]','[?????????]')) as accountname,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[??????]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,vtiger_crmentity.smownerid AS smownerid_id,vtiger_crmentity.setype AS modulename,0 AS serviceid FROM vtiger_crmentity
          LEFT JOIN vtiger_schoolrecruit ON vtiger_schoolrecruit.schoolrecruitid=vtiger_crmentity.crmid
          WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype in('School','Vendors')"; */
        $vendorsQuery = "SELECT vtiger_crmentity.crmid as accountid,concat(vtiger_crmentity.label,'-->','[?????????]') as accountname,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[??????]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,vtiger_crmentity.smownerid AS smownerid_id,vtiger_crmentity.setype AS modulename,0 AS serviceid FROM vtiger_crmentity 
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='Vendors'";
        $schoolQuery = "SELECT vtiger_crmentity.crmid as accountid,concat(vtiger_crmentity.label,'-->','[??????]') as accountname,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[??????]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,vtiger_crmentity.smownerid AS smownerid_id,vtiger_crmentity.setype AS modulename,0 AS serviceid FROM vtiger_crmentity 
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='School'";
        return $accountQuery . $useListAuthority['account'] . $searchAccountForPop . ' limit 500 UNION ' . $vendorsQuery . $useListAuthority['vendors'] . $searchAccountForPop . ' UNION ' . $schoolQuery . $useListAuthority['school'] . $searchAccountForPop;
        //return $accountQuery.$useListAuthority.$searchAccountForPop.' limit 500 UNION '.$vendorsQuery.$useListAuthority.$searchAccountForPop.' UNION '.$schoolQuery.$useListAuthority.$searchAccountForPop;
    }

    /**
     * ?????????????????????????????????
     * @return string
     */
    public function getVisitingOrderListCountSQL() {
        /* $query="SELECT count(1) AS counts FROM vtiger_crmentity
          LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid
          LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid=vtiger_crmentity.crmid
          LEFT JOIN vtiger_schoolrecruit ON vtiger_schoolrecruit.schoolrecruitid=vtiger_crmentity.crmid
          WHERE vtiger_crmentity.deleted=0 AND ((vtiger_crmentity.setype ='Accounts' AND vtiger_account.accountcategory=0) OR (vtiger_crmentity.setype='Vendors' AND vtiger_vendor.vendorstate='al_approval') OR
          vtiger_crmentity.setype='School')"; */
        $useListAuthority = $this->getUseListAuthority();
        $searchAccountForPop = $this->getSearchAccountForPop();
        $accountQuery = "SELECT count(1) AS counts FROM vtiger_crmentity 
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid 
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='Accounts' AND vtiger_account.accountcategory=0";
        $vendorsQuery = "SELECT count(1) AS counts FROM vtiger_crmentity
               LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid=vtiger_crmentity.crmid
               WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='Vendors'";
        $schoolQuery = "SELECT count(1) AS counts FROM vtiger_crmentity 
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='School'";
        return $accountQuery . $useListAuthority['account'] . $searchAccountForPop . ' UNION ALL ' . $vendorsQuery . $useListAuthority['vendors'] . $searchAccountForPop . ' UNION ALL ' . $schoolQuery . $useListAuthority['school'] . $searchAccountForPop;
        //return $accountQuery.$useListAuthority.$searchAccountForPop.' UNION ALL '.$vendorsQuery.$useListAuthority.$searchAccountForPop.' UNION ALL '.$schoolQuery.$useListAuthority.$searchAccountForPop;;
    }

    /**
     * ??????????????????????????????????????????
     * @return string
     */
    public function checkVisitingOrderToRelatedId($related_to) {
        $db = PearDatabase::getInstance();
        $useListAuthority = $this->getUseListAuthority();
        $searchAccountForPop = $this->getSearchAccountForPop();
        $accountQuery = "SELECT vtiger_crmentity.crmid as accountid FROM vtiger_crmentity 
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid 
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='Accounts' AND vtiger_account.accountcategory=0 AND vtiger_crmentity.crmid = ?";
        $vendorsQuery = "SELECT vtiger_crmentity.crmid as accountid FROM vtiger_crmentity 
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='Vendors' AND vtiger_crmentity.crmid = ?";
        $schoolQuery = "SELECT vtiger_crmentity.crmid as accountid FROM vtiger_crmentity 
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='School' AND vtiger_crmentity.crmid = ?";
        $listQuery =  $accountQuery . $useListAuthority['account'] . $searchAccountForPop . ' limit 1 UNION ' . $vendorsQuery . $useListAuthority['vendors'] . $searchAccountForPop . ' UNION ' . $schoolQuery . $useListAuthority['school'] . $searchAccountForPop;

        $listResult = $db->pquery($listQuery, array($related_to,$related_to,$related_to));
        return $db->num_rows($listResult);

    }

    public function getUseListAuthority() {
        $menuModelsList = Vtiger_Menu_Model::getAll(true);
        global $current_user;
        $accoutQuery = '';
        if (in_array('Accounts', array_keys($menuModelsList))) {
            $tempWhere = getAccessibleUsers('Accounts', 'List', true);
            if ($tempWhere != '1=1') {
                $ShareAccountsData = $this->getShareAccountsInfo();
                $shareInfo = empty($ShareAccountsData) ? '' : " OR vtiger_crmentity.crmid IN(" . implode(',', $ShareAccountsData) . ")";
                $accoutQuery = ' AND (vtiger_crmentity.smownerid IN(' . implode(',', $tempWhere) . ')' . $shareInfo . ')';
            }
        } else {
            $accoutQuery = ' AND vtiger_crmentity.smownerid =' . $current_user->id;
        }
        $schoolQuery = '';
        if (in_array('School', array_keys($menuModelsList))) {
            $tempWhere = getAccessibleUsers('School', 'List', true);
            if ($tempWhere != '1=1') {
                $schoolQuery = ' AND vtiger_crmentity.smownerid IN(' . implode(',', $tempWhere) . ')';
            }
        } else {
            $schoolQuery = ' AND vtiger_crmentity.smownerid =' . $current_user->id;
        }
        $vendorsQuery = '';
        if (in_array('Vendors', array_keys($menuModelsList))) {
            $tempWhere = getAccessibleUsers('Vendors', 'List', true);
            if ($tempWhere != '1=1') {
                $ShareAccountsData = $this->getShareVendorsInfo();
                $shareInfo = empty($ShareAccountsData) ? '' : " OR vtiger_crmentity.crmid IN(" . implode(',', $ShareAccountsData) . ")";
                $vendorsQuery = ' AND (vtiger_crmentity.smownerid IN(' . implode(',', $tempWhere) . ')' . $shareInfo . ')';
            }
        } else {
            $vendorsQuery = ' AND vtiger_crmentity.smownerid =' . $current_user->id;
        }
        return array('account' => $accoutQuery, 'vendors' => $vendorsQuery, 'school' => $schoolQuery);
    }

    /**
     * ???????????????????????????
     * @return array
     */
    public function getShareAccountsInfo(){
        global $adb,$current_user;
        $result=$adb->pquery('SELECT accountid FROM vtiger_shareaccount WHERE userid=? AND sharestatus=1',array($current_user->id));
        $accountList=array();
        if($adb->num_rows($result)){
            while($rowdata=$adb->fetch_array($result)){$accountList[]=$rowdata['accountid'];}
        }
        return $accountList;
    }

    /**
     * ??????????????????????????????
     * @return array
     */
    public function getShareVendorsInfo(){
        global $adb,$current_user;
        $result=$adb->pquery('SELECT vendorsid FROM vtiger_sharevendors WHERE userid=? AND sharestatus=1',array($current_user->id));
        if($adb->num_rows($result)){
            while($rowdata=$adb->fetch_array($result)){$accountList[]=$rowdata['vendorsid'];}
        }
        return $accountList;
    }

    /**
     * ????????????SQL
     * @return string
     */
    public function getSearchAccountForPop() {
        $searchKey = $this->get('search_key');
        $listQuery = '';
        if ($searchKey == 'accountname') {
            $searchValue = $this->get('search_value');
            $searchValue = $this->check_input($searchValue);
            $listQuery = empty($searchValue) ? "" : " AND vtiger_crmentity.label LIKE '%" . $searchValue . "%'";
        }
        return $listQuery;
    }

    public function check_input($data) {
        //??????????????????????????????
        $data = addslashes($data);
        //???????????????????????????????????????
        if (get_magic_quotes_gpc()) {
            //???????????????
            $data = stripslashes($data);
        }
        //???'_'?????????
        $data = str_replace("_", "\_", $data);
        $data = str_replace("=", "", $data);
        $data = str_replace("'", "", $data);
        //???'%'?????????
        $data = str_replace("%", "\%", $data);
        //???'*'?????????
        $data = str_replace("*", "\*", $data);
        //????????????
        $data = nl2br($data);
        //??????????????????
        $data = trim($data);
        //???HTML???????????????????????????
        $data = htmlspecialchars($data);
        return $data;
    }

}
