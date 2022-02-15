<?php

set_time_limit(0);
ini_set('memory_limit', '-1');
class ReceivedPaymentsClassify_List_View extends Vtiger_KList_View {
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    /**
     * 页面标题
     * @param Vtiger_Request $request
     * @return mixed|string
     */
    function getPageTitle(Vtiger_Request $request)
    {
        $public = $request->get('public');
        if ($public == 'configuration') {
            return '回款统计分类配置';
        } elseif ($public == 'PermissonSet') {
            return '查询权限设置';
        }
       return vtranslate($request->getModule(), $request->get('module'));
    }

    function preProcess(Vtiger_Request $request, $display=true)
    {
        $recordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
        $viewer = $this->getViewer($request);
        $viewer->assign('classficationList', $recordModel->getAllReceivedPaymentsRuleTree());
        parent::preProcess($request);
    }

    function process (Vtiger_Request $request)
    {
        $strPublic = $request->get('public');
        if ($strPublic == 'PermissonSet') {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(" AND `status`='Active'"));
            $viewer->assign('RECOEDS', ReceivedPaymentsClassify_Record_Model::getRulePermissions());
            $recordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
            $viewer->assign("ARTIFICIALCLASSFICATIONS",$recordModel->getAllReceivedPaymentsRuleTree());
            $viewer->view('exportrm.tpl', $moduleName);
            exit;
        } elseif ($strPublic=='configuration') {
            $moduleName = $request->getModule();
            global $adb;
            $ruleList = [];
            $sql = 'SELECT id, name, parent FROM vtiger_receivedpayments_rule WHERE deleted=0';
            $result = $adb->pquery($sql);
            if($adb->num_rows($result)) {
                while ($row = $adb->fetchByAssoc($result)) {
                    $ruleList[$row['parent']][] = $row;
                }
            }
            $sql = 'SELECT rule_contract.ruleid, rule_contract.contract_prefix FROM vtiger_receivedpayments_rule_contract AS rule_contract
            INNER JOIN vtiger_receivedpayments_rule AS rule ON rule_contract.ruleid = rule.id AND rule.deleted=0';
            $result = $adb->pquery($sql);
            $ruleContractList = [];
            if($adb->num_rows($result)) {
                while ($row = $adb->fetchByAssoc($result)) {
                    $ruleContractList[$row['ruleid']][] = $row;
                }
            }
            $sql = 'SELECT rule_department.ruleid, rule_department.departmentid, rule_department.parentid FROM vtiger_receivedpayments_rule_department AS rule_department
            INNER JOIN vtiger_receivedpayments_rule AS rule ON rule_department.ruleid = rule.id AND rule.deleted=0';
            $result = $adb->pquery($sql);
            $ruleDepartmentList = [];
            $departmentIds = [];
            if($adb->num_rows($result)) {
                while ($row = $adb->fetchByAssoc($result)) {
                    if($row['departmentid']) {
                        $departmentIds[] = $row['parentid'];
                        $departmentIds[] = $row['departmentid'];
                    } else {
                        $departmentIds[] = $row['parentid'];
                    }
                    $departmentIds[] = $row;
                    $ruleDepartmentList[$row['ruleid']][] = $row;
                }
                $departmentIds = array_unique($departmentIds);
                $departmentIdStr = "'". implode("','", $departmentIds) ."'" ;
                $sql = "SELECT departmentid, departmentname FROM vtiger_departments WHERE departmentid IN({$departmentIdStr})";
                $result = $adb->pquery($sql);
                if ($adb->num_rows($result)) {
                    $deparmentList = [];
                    while ($row = $adb->fetchByAssoc($result)) {
                        $deparmentList[$row['departmentid']] = $row['departmentname'];
                    }
                }
                foreach ($ruleDepartmentList as $key => $rule) {
                    foreach ($rule as $k => $item) {
                        if (isset($deparmentList[$item['parentid']])) {
                            $ruleDepartmentList[$key][$k]['parentname'] = $deparmentList[$item['parentid']];
                        }
                        if ($item['departmentid']) {
                            if (isset($deparmentList[$item['departmentid']])) {
                                $ruleDepartmentList[$key][$k]['departmentname'] = $deparmentList[$item['departmentid']];
                            }
                        } else {
                            $ruleDepartmentList[$key][$k]['departmentname'] = '全部';
                        }
                    }
                }
            }

            $sql = 'SELECT rule_companyaccount.ruleid, rule_companyaccount.companyaccountid, rule_companyaccount.company, companyaccounts.bank, companyaccounts.subbank, companyaccounts.account FROM vtiger_receivedpayments_rule_companyaccount AS rule_companyaccount
            INNER JOIN vtiger_receivedpayments_rule AS rule ON rule_companyaccount.ruleid = rule.id AND rule.deleted=0
            LEFT JOIN vtiger_companyaccounts AS companyaccounts ON rule_companyaccount.companyaccountid =companyaccounts.id';
            $result = $adb->pquery($sql);
            $ruleCompanyAccountList = [];
            if($adb->num_rows($result)) {
                while ($row = $adb->fetchByAssoc($result)) {
                    if($row['companyaccountid']) {
                        $row['accountname'] = sprintf('%s##%s%s(%s)', $row['company'], $row['bank'], ($row['subbank']? '-'.$row['subbank']: ''), $row['account']);
                    } else {
                        $row['accountname'] = $row['company']. '##全部';
                    }
                    $ruleCompanyAccountList[$row['ruleid']][] = $row;
                }
            }
            $starYear = 2021;
            $endYear = date('Y', strtotime('+1 year'));
            $yearList = [];
            for($i=$starYear; $i<=$endYear; $i++) {
                $yearList[] = $i;
            }
            $topType = ['saas', 'paas', 'iaas', '大数据'];
            $viewer = $this->getViewer($request);
            $viewer->assign('ruleList', $ruleList);
            $viewer->assign('ruleContractList', $ruleContractList);
            $viewer->assign('ruleDepartmentList', $ruleDepartmentList);
            $viewer->assign('ruleCompanyAccountList', $ruleCompanyAccountList);
            $viewer->assign('topType', $topType);
            $viewer->assign('yearList', $yearList);
            $viewer->view('ClassifyConfiguration.tpl', $moduleName);
            return;
        }
        // 回款拆分的权限
        global $adb, $current_user;
        $sql = "select * FROM vtiger_custompowers where custompowerstype='split_received_rayments' OR custompowerstype='receivedpaymentsEdit' OR custompowerstype='isEditAllowinvoicetotal' OR custompowerstype='receivedpaymentsRepeat'";
        $sel_result = $adb->pquery($sql, array());
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt > 0) {
            while($row=$adb->fetch_array($sel_result)) {
                $roles_arr = explode(',', $row['roles']);
                $user_arr = explode(',', $row['user']);
                $viewer = $this->getViewer($request);
                if (in_array($current_user->current_user_roles, $roles_arr) || in_array($current_user->id, $user_arr)) {
                    if($row['custompowerstype'] =='split_received_rayments'){
                        $viewer->assign('IS_SPLIT', 1);
                    } else if($row['custompowerstype'] =='receivedpaymentsEdit'){
                        $viewer->assign('IS_EDIT', 1);
                    } else if($row['custompowerstype'] =='isEditAllowinvoicetotal') {
                        $viewer->assign('isEditAllowinvoicetotal', 1);
                    } else if($row['custompowerstype'] =='receivedpaymentsRepeat') {
                        $viewer->assign('ISREPEATRECEIVEDPAYMENTS', 1);
                    }
                }
            }
        }
        $recordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
        $viewer->assign("ARTIFICIALCLASSFICATIONS",$recordModel->getAllReceivedPaymentsRuleTree());
        parent::process($request);
    }
}
