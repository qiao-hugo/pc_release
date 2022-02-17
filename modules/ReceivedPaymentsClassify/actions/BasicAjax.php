<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ReceivedPaymentsClassify_BasicAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('artificialClassificationSelect');
        $this->exposeMethod('exportData');//导出数据
        $this->exposeMethod('exportFile');//导出数据
        $this->exposeMethod('add');//新增权限
        $this->exposeMethod('deleted');//删除权限
        $this->exposeMethod('getCurrentSecondReceivedPaymentsRules');//获取当前人可查看二级分类
        $this->exposeMethod('getClassifyData');//获取回款统计表
        $this->exposeMethod('addClassifyRule');//新增回款分类规则
        $this->exposeMethod('delClassifyRule');//删除回款分类规则
        $this->exposeMethod('getPlanList');//回款计划列表
        $this->exposeMethod('setReceivedPaymentsPlan');//设置回款计划
        $this->exposeMethod('getReceivedPaymentsRule');//根据规则ID获取分类规则
        $this->exposeMethod('saveReceivedPaymentsRule');//更新分类规则
    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }
    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * 修改人工分类
     * @param Vtiger_Request $request
     */
    public function artificialClassificationSelect(Vtiger_Request $request){
        global $adb;
        $recordId=$request->get('record');
        $artificialclassfication=$request->get('artificialclassfication');
        $data=array('flag'=>false,'msg'=>'修改失败');
        if($recordId){
            $sql="update vtiger_receivedpayments set artificialclassfication=? where  receivedpaymentsid=?";
            $adb->pquery($sql,array($artificialclassfication,$recordId));
            $data=array('flag'=>true);
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 导出数据
     * @param Vtiger_Request $request
     */
    public function exportData(Vtiger_Request $request)
    {
        $receivedpaymentids = $request->get('exportIds');
        set_time_limit(0);
        global $current_user,$root_directory,$adb,$currentView;
        $currentView = 'List';
        $listViewModel = Vtiger_ListView_Model::getInstance('ReceivedPaymentsClassify');
        $listQuery = $listViewModel->getQuery();
        $listViewModel->getSearchWhere();
        $listQuery .= $listViewModel->getUserWhere();
        $queryGenerator = $listViewModel->get('query_generator');
        //获取自定义语句拼接方法
        $pattern='(vtiger_servicecontracts.contract_no) as';
        $listQuery=str_replace($pattern,'(SELECT crm.label FROM vtiger_crmentity as crm WHERE crm.crmid=vtiger_receivedpayments.relatetoid ) AS ',$listQuery);
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $pattern='/\(vtiger_servicecontracts.contract_no(?!,)/';
        $listQuery=preg_replace($pattern,'vtiger_receivedpayments.relatetoid IN(SELECT crm2.crmid FROM vtiger_crmentity AS crm2 WHERE crm2.setype in(\'ServiceContracts\',\'SupplierContracts\') AND crm2.deleted=0 AND crm2.label',$listQuery);
        $listQuery = str_replace('AND vtiger_servicecontracts.contract_no IS NOT NULL', '', $listQuery);
        if(!empty($receivedpaymentids)){
            $listQuery .= ' and vtiger_receivedpayments.receivedpaymentsid in ('.implode(",",$receivedpaymentids).')';
        }
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    if($listViewHeaders[$key]['ishidden']){
                        continue;
                    }
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)) {
            $temp = $listViewHeaders;
        }
        $headerArray = $temp;
        ini_set('memory_limit','1024M');
        $path = $root_directory.'temp/';
        !is_dir($path) && mkdir($path,'0755',true);
        $filename = $path.'receivedpayments'.date('Ymd').$current_user->id.'.csv';
        $array= array();
        foreach($headerArray as $key=>$value) {
            $array[] = iconv('utf-8','gb2312',vtranslate($key, 'ReceivedPaymentsClassify'));
        }
        $recordModel = Vtiger_Record_Model::getCleanInstance('ReceivedPaymentsClassify');
        $receivedPaymentsRules = $recordModel->getAllReceivedPaymentsRules();
        $fp = fopen($filename,'w');
        fputcsv($fp, $array);
        $limit = 5000;
        $i = 0;
        while(true){
            $limitSQL = " limit " . $i * $limit . ",". $limit;
            $i++;
            $result = $adb->pquery($listQuery . $limitSQL, array());
            if($adb->num_rows($result)){
                while ($value = $adb->fetch_array($result)) {
                    $array = array();
                    foreach ($headerArray as $keyheader => $valueheader) {
                        if (in_array($valueheader['columnname'], ['first_collate_status', 'last_collate_status'])) {
                            if ($value[$valueheader['columnname']]=='fit') {
                                $currnetValue = '符合';
                            } elseif($value[$valueheader['columnname']]=='unfit') {
                                $currnetValue = '不符合';
                            } else {
                                $currnetValue = '';
                            }
                        } elseif (in_array($valueheader['columnname'], ['systemclassfication', 'artificialclassfication'])) {
                            $currnetValue = isset($receivedPaymentsRules[$value[$valueheader['columnname']]])? $receivedPaymentsRules[$value[$valueheader['columnname']]]: '';
                        } else {
                            $currnetValue = uitypeformat($valueheader, $value[$valueheader['columnname']], 'ReceivedPaymentsClassify');
                        }
                        $currnetValue=preg_replace('/<[^>]*>/','',$currnetValue);
                        $currnetValue = iconv('utf-8', 'GBK//IGNORE', $currnetValue);
                        $array[] = $currnetValue;
                    }
                    fputcsv($fp, $array);
                }
                ob_flush();
                flush();
            }else{
                break;
            }
        }
        fclose($fp);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

    public function exportFile()
    {
        global $site_URL,$current_user;
        header('location:'.$site_URL.'temp/'.'receivedpayments'.date('Ymd').$current_user->id.'.csv');
        exit;
    }

    function add(Vtiger_Request $request){
        $userids=$request->get("userids");
        $artificialclassications=$request->get("artificialclassications");
        $data=array("success"=>false,'msg'=>'添加失败');
        do {
            if(!count($userids)){
                break;
            }

            if(!count($artificialclassications)){
                break;
            }
            $db=PearDatabase::getInstance();
            $sql = "select 1 from vtiger_receivedpayments_permission where userid in (".implode(",",$userids).') and ruleid in('.implode(",",$artificialclassications).')';
            $result = $db->query($sql,array());
            if($db->num_rows($result)){
                $data=array("success"=>false,'msg'=>'重复设置查看权限');
                break;
            }

            global $current_user;

            $sql2='insert into vtiger_receivedpayments_permission (userid,ruleid,createdtime,createdby) values ';
            foreach ($userids as $userid){
                foreach ($artificialclassications as $artificialclassication){
                    $sql2.= '('.$userid.",".$artificialclassication.',"'.date("Y-m-d H:i:s").'",'.$current_user->id.'),';
                }
            }
            $sql2 = rtrim($sql2,',');
            $db->pquery($sql2,array());
            $data=array("success"=>true,'msg'=>'添加成功');
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    function deleted(Vtiger_Request $request){
        $id=$request->get("id");
        $delsql="DELETE FROM vtiger_receivedpayments_permission WHERE receivedpaymentspermissionid=?";
        $db=PearDatabase::getInstance();
        $db->pquery($delsql,array($id));
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    function getCurrentSecondReceivedPaymentsRules(Vtiger_Request $request){
        global $current_user;
        $recordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
        $data = $recordModel->getReceivedPaymentsRules($current_user->id);
        echo json_encode(array("success"=>true, 'rules'=>$data));
    }

    function getClassifyData(Vtiger_Request $request) {
        $startMonth=$request->get("startMonth");
        $endMonth=$request->get("endMonth");
        $ruleIds=$request->get("ruleIds");
        $recordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
        $classifyData = $recordModel->getClassifyData($startMonth,$endMonth,$ruleIds);
        $data = array(
            'success'=>true,
            'classifyData'=>$classifyData['classifyData'],
            'eChartsField'=>$classifyData['eChartsField'],
            'eChartsData'=>$classifyData['eChartsData']
        );
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 新增分类配置项
     * @param Vtiger_Request $request
     */
    public function addClassifyRule(Vtiger_Request $request)
    {
        global $adb, $current_user;
        $parentype = $request->get('parentype');
        $name = $request->get('typename');
        if(!$parentype) {
            $data = [
                'status'=>'error',
                'msg'=>'父级分类不能为空'
            ];
        } else if(!$name) {
            $data = [
                'status'=>'error',
                'msg'=>'分类名称不能为空'
            ];
        } else {
            $sql = 'SELECT id FROM vtiger_receivedpayments_rule WHERE deleted=0 AND parent=? AND name=?';
            $result = $adb->pquery($sql, [$parentype, $name]);
            if ($adb->num_rows($result) > 0) {
                $data = [
                    'status'=>'error',
                    'msg'=>'已存在相同名称的二级分类'
                ];
            } else {
                $sql = 'INSERT INTO vtiger_receivedpayments_rule(name, parent, createdby, createdtime) VALUES (?, ?, ?, ?)';
                $adb->pquery($sql, [$name, $parentype, $current_user->id, date('Y-m-d H:i:s')]);
                $data = [
                    'status' => 'success',
                    'msg'    => '成功新增分类行'
                ];
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 删除分类配置项
     * @param Vtiger_Request $request
     */
    public function delClassifyRule(Vtiger_Request $request)
    {
        global $adb, $current_user;
        $id = $request->get('id');
        if(!$id) {
            $data = [
                'status' => 'error',
                'msg'    => '分类ID不能为空'
            ];
        } else {
            $sql = 'UPDATE vtiger_receivedpayments_rule SET deleted=1, modifiedby=?, modifiedtime=? WHERE id=?';
            $adb->pquery($sql, [$current_user->id, date('Y-m-d H:i:s'), $id]);
            //更新回款的系统分类
            $sql='UPDATE vtiger_receivedpayments SET systemclassfication=0 WHERE systemclassfication=?';
            $adb->pquery($sql, [$id]);
            //更新回款的人工分类
            $sql='UPDATE vtiger_receivedpayments SET artificialclassfication=0 WHERE artificialclassfication=?';
            $adb->pquery($sql, [$id]);
            $data = [
                'status'=>'success',
                'msg'=>'成功删除分类行'
            ];
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 获取回款计划
     * @param Vtiger_Request $request
     */
    public function getPlanList(Vtiger_Request $request)
    {
        global $adb, $current_user;
        $year = $request->get('year');
        if(!$year) {
            $year = date('Y');
        }
        $sql = 'SELECT plan.`month`, plan.ruleid, plan.amount FROM vtiger_receivedpayments_plan AS plan
                 INNER JOIN vtiger_receivedpayments_rule AS rule ON plan.ruleid=rule.id
                 WHERE rule.deleted=0 AND plan.`month` like ?';
        $result = $adb->pquery($sql, ['%' . $year . '%']);
        $planList = [];
        if ($adb->num_rows($result)) {
            while ($row = $adb->fetchByAssoc($result)) {
                $row['month'] = intval(str_replace($year.'-','', $row['month']));
                $planList[] = $row;
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($planList);
        $response->emit();
    }

    /**
     * 设计回款计划
     * @param Vtiger_Request $request
     */
    public function setReceivedPaymentsPlan(Vtiger_Request $request)
    {
        global $adb, $current_user;
        $year = $request->get('year');
        $month = $request->get('month');
        $amountList = $request->get('amountList');
        if (!$year) {
            $data = [
                'status' => 'error',
                'msg'    => '年份不能为空'
            ];
        } elseif (!$month) {
            $data = [
                'status' => 'error',
                'msg'    => '月份不能为空'
            ];
        } elseif (!is_array($amountList) || empty($amountList)) {
            $data = [
                'status' => 'error',
                'msg'    => '计划回款金额不能为空'
            ];
        }
        if($month<10) {
            $month = $year . '-0' . $month;
        } else {
            $month = $year . '-' . $month;
        }
        $modifiedtime = date('Y-m-d H:i:s');
        $ruleIdsStr = implode(',', array_column($amountList, 'id'));
        $delSql = "DELETE FROM vtiger_receivedpayments_plan WHERE month='{$month}' AND ruleid IN({$ruleIdsStr})";
        $adb->pquery($delSql);
        $insertSql = 'INSERT INTO vtiger_receivedpayments_plan(month, ruleid, amount, modifiedtime, modifiedby) VALUES';
        foreach ($amountList as $key => $item) {
            if($key==0) {
                $insertSql .= "('{$month}', {$item['id']}, {$item['amount']}, '{$modifiedtime}', {$current_user->id})";
            } else {
                $insertSql .= ",('{$month}', {$item['id']}, {$item['amount']}, '{$modifiedtime}', {$current_user->id})";
            }
        }
        $adb->pquery($insertSql);
        $data = [
            'status'=>'success',
            'msg'=>'成功保存计划回款金额'
        ];
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 根据ID获取分类配置信息
     * @param Vtiger_Request $request
     */
    public function getReceivedPaymentsRule(Vtiger_Request $request)
    {
        $ruleid = $request->get('ruleid');
        if (!$ruleid) {
            $data = [
                'status' => 'error',
                'msg'    => '规则ID不能为空'
            ];
        } else {
            global $adb;
            $sql = 'SELECT id, name, parent, startmonth, endmonth FROM vtiger_receivedpayments_rule WHERE deleted=0 AND id=?';
            $result = $adb->pquery($sql, [$ruleid]);
            if ($adb->num_rows($result)==0) {
                $data = [
                    'status' => 'error',
                    'msg'    => '分类规则不存在'
                ];
            } else {
                $ruleInfo = $adb->fetchByAssoc($result);
                /* 合同编号 start */
                $contractList = [];
                $sql = 'SELECT contract_prefix FROM vtiger_receivedpayments_rule_contract WHERE ruleid = ?';
                $result = $adb->pquery($sql, [$ruleid]);

                if($adb->num_rows($result)) {
                    while ($row = $adb->fetchByAssoc($result)) {
                        $contractList[] = $row['contract_prefix'];
                    }
                }
                /* 合同编号 end */
                /* 部门 start */
                $departmentList = [];
                $sql = 'SELECT departmentid, parentid FROM vtiger_receivedpayments_rule_department WHERE ruleid = ?';
                $result = $adb->pquery($sql, [$ruleid]);
                if($adb->num_rows($result)) {
                    while ($row = $adb->fetchByAssoc($result)) {
                        $departmentList[] = $row;
                    }
                }
                /* 部门 end */
                /* 主体公司账号 start */
                $companyAccountList = [];
                $sql = 'SELECT companyaccountid, company FROM vtiger_receivedpayments_rule_companyaccount WHERE ruleid = ?';
                $result = $adb->pquery($sql, [$ruleid]);
                if($adb->num_rows($result)) {
                    while ($row = $adb->fetchByAssoc($result)) {
                        $companyAccountList[] = $row;
                    }
                }
                /* 主体公司账号 end */
                $ruleInfo['contractList'] = $contractList;
                $ruleInfo['departmentList'] = $departmentList;
                $ruleInfo['companyAccountList'] = $companyAccountList;
                /* 合同下拉项 start */
                $contractOptionList = [];
                $sql = "SELECT products_code FROM (SELECT products_code FROM vtiger_products_code WHERE presence = 1 AND products_code != ''
                        UNION SELECT DISTINCT productclass AS products_code FROM vtiger_contract_type WHERE productclass!='' AND productclass IS NOT NULL) tb
                        WHERE products_code NOT IN( SELECT rule_contract.contract_prefix FROM vtiger_receivedpayments_rule_contract AS rule_contract 
                        INNER JOIN vtiger_receivedpayments_rule AS rule ON rule_contract.ruleid = rule.id AND rule.deleted=0 AND rule.id != ?)";
                $result = $adb->pquery($sql, [$ruleid]);
                if ($adb->num_rows($result)) {
                    while ($row = $adb->fetchByAssoc($result)) {
                        $contractOptionList[] = $row['products_code'];
                    }
                }
                /* 合同下拉项 end */
                /* 部门下拉项 start */
                $deparmentOptionList = [];
                //获取一级部门，排除其他分类已经配置的一级部门
                $sql = "SELECT departmentid, departmentname FROM vtiger_departments WHERE depth=1 AND departmentid NOT IN(
                        SELECT rule_department.parentid FROM vtiger_receivedpayments_rule_department AS rule_department
                        INNER JOIN vtiger_receivedpayments_rule AS rule ON rule_department.ruleid = rule.id AND rule_department.departmentid='' AND rule.deleted=0 AND rule.id != ?)";
                $result = $adb->pquery($sql, [$ruleid]);
                if ($adb->num_rows($result)) {
                    while ($row = $adb->fetchByAssoc($result)) {
                        $deparmentOptionList[$row['departmentid']] = $row;
                    }
                }
                $parentIds = [];
                $departmentIds = [];
                $sql = "SELECT rule_department.departmentid, rule_department.parentid FROM vtiger_receivedpayments_rule_department AS rule_department
                    INNER JOIN vtiger_receivedpayments_rule AS rule ON rule_department.ruleid = rule.id AND rule.deleted=0
                    WHERE rule_department.departmentid != '' AND rule.id != ?";
                $result = $adb->pquery($sql, [$ruleid]);
                if ($adb->num_rows($result)) {
                    while ($row = $adb->fetchByAssoc($result)) {
                        $parentIds[] = $row['parentid'];
                        $departmentIds[] = $row['departmentid'];
                    }
                }
                $parentIds = array_unique($parentIds);
                $departmentIdStr = "'" . implode("','", $departmentIds) . "'";
                $subDeparmentOptionList = [];
                //获取二级部门，排除其他分类已经配置的二级部门
                $sql = "SELECT departmentid, departmentname, parentdepartment FROM vtiger_departments WHERE depth=2 AND departmentid NOT IN({$departmentIdStr})";
                $result = $adb->pquery($sql);
                if ($adb->num_rows($result)) {
                    while ($row = $adb->fetchByAssoc($result)) {
                        $parentdepartment = explode('::', $row['parentdepartment']);
                        unset($row['parentdepartment']);
                        $subDeparmentOptionList[$parentdepartment[1]][] = $row;
                    }
                }
                foreach ($deparmentOptionList as $key=>$item) {
                    if (isset($subDeparmentOptionList[$item['departmentid']])) {
                        $deparmentOptionList[$key]['children'] = $subDeparmentOptionList[$item['departmentid']];
                        $deparmentOptionList[$key]['hasAll'] = in_array($key, $parentIds)? false: true;
                    } else {
                        unset($deparmentOptionList[$key]);
                    }
                }
                /* 部门下拉项 end */
                $companyAccountOptionList = [];
                //获取主体公司，排除其他分类已经配置的主体公司
                $sql = "SELECT distinct company FROM vtiger_companyaccounts WHERE company NOT IN(
                        SELECT rule_companyaccount.company FROM vtiger_receivedpayments_rule_companyaccount AS rule_companyaccount
                        INNER JOIN vtiger_receivedpayments_rule AS rule ON rule_companyaccount.ruleid=rule.id AND rule.deleted = 0 AND rule_companyaccount.companyaccountid = 0 AND rule.id != ?)";
                $result = $adb->pquery($sql, [$ruleid]);
                if ($adb->num_rows($result)) {
                    while ($row = $adb->fetchByAssoc($result)) {
                        $companyAccountOptionList[$row['company']] = $row;
                    }
                }
                //获取主体公司账号
                $companys = [];
                $companyAccountIds = [0];
                $sql = "SELECT rule_companyaccount.companyaccountid, rule_companyaccount.company FROM vtiger_receivedpayments_rule_companyaccount AS rule_companyaccount
                        INNER JOIN vtiger_receivedpayments_rule AS rule ON rule_companyaccount.ruleid = rule.id AND rule.deleted=0
                        WHERE rule_companyaccount.companyaccountid!=0 AND rule.id !=?";
                $result = $adb->pquery($sql, [$ruleid]);
                if ($adb->num_rows($result)) {
                    while ($row = $adb->fetchByAssoc($result)) {
                        $companys[] = $row['company'];
                        $companyAccountIds[] = $row['companyaccountid'];
                    }
                }
                $companys = array_unique($companys);
                $companyAccountIdStr = implode(',', $companyAccountIds);
                $accountOptionList = [];
                $sql = "SELECT id, company, bank, subbank, account FROM vtiger_companyaccounts WHERE id NOT IN({$companyAccountIdStr})";
                $result = $adb->pquery($sql);
                if ($adb->num_rows($result)) {
                    while ($row = $adb->fetchByAssoc($result)) {
                        $accountOptionList[$row['company']][] = [
                            'id'=>$row['id'],
                            'accountname'=> $row['bank'] . ($row['subbank']? '-'. $row['subbank']:'') . '（' . $row['account'] . '）'
                        ];
                    }
                }
                foreach ($companyAccountOptionList as $key=>$item) {
                    if (isset($accountOptionList[$key])) {
                        $companyAccountOptionList[$key]['children'] = $accountOptionList[$key];
                        $companyAccountOptionList[$key]['hasAll'] = in_array($key, $companys)? false: true;
                    } else {
                        unset($companyAccountOptionList[$key]);
                    }
                }
                $data = [
                    'status' => 'success',
                    'ruleInfo' => $ruleInfo,
                    'contractOptionList' => $contractOptionList,
                    'deparmentOptionList' => $deparmentOptionList,
                    'companyAccountOptionList' => $companyAccountOptionList
                ];
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 根据ID获取分类配置信息
     * @param Vtiger_Request $request
     */
    public function saveReceivedPaymentsRule(Vtiger_Request $request)
    {
        $ruleid = $request->get('ruleid');
        $name = $request->get('name');
        $contactList = $request->get('contactList');
        $departmentList = $request->get('departmentList');
        $accountList = $request->get('accountList');
        $startmonth = $request->get('startmonth');
        $endmonth = $request->get('endmonth');
        if (!$ruleid) {
            $data = [
                'status' => 'error',
                'msg'    => '规则ID不能为空'
            ];
        } elseif(!$startmonth) {
            $data = [
                'status' => 'error',
                'msg'    => '开始月份不能为空'
            ];
        } elseif(!$endmonth) {
            $data = [
                'status' => 'error',
                'msg'    => '结束月份不能为空'
            ];
        } else {
            global $adb, $current_user;
            $sql = 'SELECT id FROM vtiger_receivedpayments_rule WHERE deleted=0 AND id=?';
            $result = $adb->pquery($sql, [$ruleid]);
            if ($adb->num_rows($result)==0) {
                $data = [
                    'status' => 'error',
                    'msg'    => '分类规则不存在'
                ];
            } else {
                $sql = 'UPDATE vtiger_receivedpayments_rule SET name=?, startmonth=?, endmonth=?, modifiedby=?, modifiedtime=? WHERE id=?';
                $result = $adb->pquery($sql, [$name, $startmonth, $endmonth, $current_user->id, date('Y-m-d H:i:s'), $ruleid]);
                /* 合同编号 start */
                $delSql = 'DELETE FROM vtiger_receivedpayments_rule_contract WHERE ruleid=?';
                $adb->pquery($delSql, [$ruleid]);
                if($contactList) {
                    $insertSql = 'INSERT INTO vtiger_receivedpayments_rule_contract(ruleid, contract_prefix) VALUES';
                    foreach ($contactList as $key => $item) {
                        if($key==0) {
                            $insertSql .= "({$ruleid}, '{$item}')";
                        } else {
                            $insertSql .= ",({$ruleid}, '{$item}')";
                        }
                    }
                    $adb->pquery($insertSql);
                }
                /* 合同编号 end */
                require 'crmcache/departmentanduserinfo.php';
                $departmentIds = [];
                /* 部门 start */
                $delSql = 'DELETE FROM vtiger_receivedpayments_rule_department WHERE ruleid=?';
                $adb->pquery($delSql, [$ruleid]);
                if($departmentList) {
                    $insertSql = 'INSERT INTO vtiger_receivedpayments_rule_department(ruleid, parentid, departmentid) VALUES';
                    foreach ($departmentList as $key => $item) {
                        list($parentId, $departmentId) = explode('-', $item);
                        if(!$departmentId) {
                            $departmentId = '';
                            //获取所有子部门
                            $departmentIds = array_merge($departmentIds, $departmentinfo[$parentId]);
                        } else {
                            //获取所有子部门
                            $departmentIds = array_merge($departmentIds, $departmentinfo[$departmentId]);
                        }
                        if($key==0) {
                            $insertSql .= "({$ruleid}, '{$parentId}', '{$departmentId}')";
                        } else {
                            $insertSql .= ",({$ruleid}, '{$parentId}', '{$departmentId}')";
                        }
                    }
                    $adb->pquery($insertSql);
                }
                /* 部门 end */
                /* 主体公司账号 start */
                $delSql = 'DELETE FROM vtiger_receivedpayments_rule_companyaccount WHERE ruleid=?';
                $adb->pquery($delSql, [$ruleid]);
                if($accountList) {
                    $insertSql = 'INSERT INTO vtiger_receivedpayments_rule_companyaccount(ruleid, company, companyaccountid) VALUES';
                    foreach ($accountList as $key => $item) {
                        list($company, $accoyntId) = explode('-', $item);
                        if($key==0) {
                            $insertSql .= "({$ruleid}, '{$company}', {$accoyntId})";
                        } else {
                            $insertSql .= ",({$ruleid}, '{$company}', {$accoyntId})";
                        }
                    }
                    $adb->pquery($insertSql);
                }
                /* 主体公司账号 end */
                /* 更新回款分类 start */
                $startdate = $startmonth.'-01';
                $enddate = $endmonth.'-31';
                //更新回款系统分类
                $sql='UPDATE vtiger_receivedpayments SET systemclassfication=0 WHERE systemclassfication=? AND reality_date>=? AND reality_date<=?';
                $adb->pquery($sql, [$ruleid, $startdate, $enddate]);
                //更新回款人工分类
                $sql='UPDATE vtiger_receivedpayments SET artificialclassfication=0 WHERE artificialclassfication=? AND reality_date>=? AND reality_date<=?';
                $adb->pquery($sql, [$ruleid, $startdate, $enddate]);
                /* 合同编号 start */
                if($contactList) {
                    $contractStr = '';
                    foreach ($contactList as $key => $item) {
                        if($key==0) {
                            $contractStr .= "contract_no LIKE '%{$item}%'";
                        } else {
                            $contractStr .= " OR contract_no LIKE '%{$item}%'";
                        }
                    }
                    $sql = "UPDATE vtiger_receivedpayments
                            INNER JOIN vtiger_servicecontracts ON vtiger_receivedpayments.relatetoid=vtiger_servicecontracts.servicecontractsid
                            SET systemclassfication=?
                            WHERE systemclassfication=0 AND reality_date>=? AND reality_date<=? AND ({$contractStr})";
                    $adb->pquery($sql, [$ruleid, $startdate, $enddate]);
                }
                /* 合同编号 end */
                /* 部门 start */
                if($departmentList) {
                    $departmentIds = array_unique($departmentIds);
                    $departmentIds = "'" . implode("','", $departmentIds) . "'";
                    $sql="UPDATE vtiger_receivedpayments 
                        INNER JOIN vtiger_servicecontracts ON vtiger_receivedpayments.relatetoid=vtiger_servicecontracts.servicecontractsid
                        LEFT JOIN vtiger_user2department ON vtiger_servicecontracts.signid = vtiger_user2department.userid
                        SET systemclassfication=?
                        WHERE systemclassfication=0 AND reality_date>=? AND reality_date<=? AND vtiger_user2department.departmentid IN({$departmentIds})";
                    $adb->pquery($sql, [$ruleid, $startdate, $enddate]);
                }
                /* 部门 end */
                /* 主体公司账号 start */
                if($accountList) {
                    $companys = [];
                    $accountIds = [];
                    foreach ($accountList as $key => $item) {
                        list($company, $accoyntId) = explode('-', $item);
                        if($accoyntId == 0) {
                            $companys[] = $company;
                        } else {
                            $accountIds[] = $accoyntId;
                        }
                    }
                    if (!empty($companys)) {
                        $companyStr = "'" . implode("','", $companys) . "'";
                        $sql = "SELECT id FROM vtiger_companyaccounts WHERE company IN({$companyStr})";
                        $result = $adb->pquery($sql);
                        if ($adb->num_rows($result)) {
                            while ($row = $adb->fetchByAssoc($result)) {
                                $accountIds[] = $row['id'];
                            }
                        }
                    }
                    $accountStr = implode(',', $accountIds);
                    $sql = "UPDATE vtiger_receivedpayments SET systemclassfication=? WHERE ismatchdepart=0 AND systemclassfication=0 AND reality_date>=? AND reality_date<=? AND companyaccountsid IN({$accountStr})";
                    $adb->pquery($sql, [$ruleid, $startdate, $enddate]);
                }
                /* 更新回款分类 end */
                $data = [
                    'status' => 'success',
                    'msg' => '成功保存分类配置信息'
                ];
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

}
