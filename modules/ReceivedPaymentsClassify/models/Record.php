<?php

class ReceivedPaymentsClassify_Record_Model extends Vtiger_Record_Model {

    /**
     * 导出权限设置
     * @param string $module
     * @return bool|mixed|string
     * @throws Exception
     */
    static public function getImportUserPermissions($module='')
    {
        global $current_user,$adb;
        if(empty($module)){
            $query="SELECT userid,permissions FROM vtiger_custompermtable WHERE deleted=0 AND module in('ServiceContracts','ReceivedPayments') AND userid=? limit 1";
            $result=$adb->pquery($query,array($current_user->id));
        }else{
            $query="SELECT userid,permissions FROM vtiger_custompermtable WHERE deleted=0 AND module=? AND userid=? limit 1";
            $result=$adb->pquery($query,array($module,$current_user->id));
        }
        $num=$adb->num_rows($result);
        if($num==0){
            return false;
        }
        return $adb->query_result($result,0,'permissions');

    }

    /**
     * 当前已经配置的权限用户
     * @return array
     */
    public static function getReportPermissions()
    {
        $db=PearDatabase::getInstance();
        $query="SELECT vtiger_custompermtable.custompermtableid as id,last_name,permissions,if(module='ReceivedPayments','回款','合同') AS module FROM vtiger_custompermtable LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_custompermtable.userid WHERE module in('ReceivedPayments','ServiceContracts') ORDER BY custompermtableid DESC";
        return $db->run_query_allrecords($query);
    }

    /**
     * 收款账号
     * @return array
     */
    public static function getowncompany()
    {
        $db=PearDatabase::getInstance();
        $query="SELECT owncompany FROM vtiger_receivedpayments WHERE owncompany IS NOT NULL AND owncompany!='' GROUP BY owncompany";
        return $db->run_query_allrecords($query);
    }

    /**
     * 获取所有二级分类
     * @param int $userId
     * @return array
     */
    public function getAllReceivedPaymentsRuleTree($userId=0){
        global $adb;
        $rules = [];
        $result = $adb->pquery("select id, parent, name from vtiger_receivedpayments_rule where deleted=0");
        if(!$adb->num_rows($result)){
            return $rules;
        }
        while ($row=$adb->fetchByAssoc($result)){
            $rules[$row['parent']][$row['id']]=$row['name'];
        }
        return $rules;
    }

    /**
     * 获取所有二级分类
     * @param int $userId
     * @return array
     */
    public function getAllReceivedPaymentsRules($userId=0){
        global $adb;
        $rules = [];
        $result = $adb->pquery("select id, parent, name from vtiger_receivedpayments_rule where deleted=0");
        if(!$adb->num_rows($result)){
            return $rules;
        }
        while ($row=$adb->fetchByAssoc($result)){
            $rules[$row['id']]=$row['name'];
        }
        return $rules;
    }

    public function getReceivedPaymentsRules($userId=0){
        $db= PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_receivedpayments_rule where deleted=0");
        if(!$db->num_rows($result)){
            return array();
        }
        $ruleIds=array();
        $isAdmin=0;
        if($userId){
            global $current_user;
            $user=new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
            $result2 = $db->pquery("select * from vtiger_receivedpayments_permission where userid=?",array($userId));
            if($db->num_rows($result2)){
                while ($row2=$db->fetchByAssoc($result2)){
                    $ruleIds[]=$row2['ruleid'];
                }
            }
            if($current_user->is_admin=='on'){
                $isAdmin=1;
            }
        }
        while ($row=$db->fetchByAssoc($result)){
            if(!in_array($row['id'],$ruleIds) && count($ruleIds)>0 && !$isAdmin){
                continue;
            }
            $rules[$row['parent']][$row['id']]=$row['name'];
        }
        return $rules;
    }
    public function getSecondReceivedPaymentsRules($userId=0){
        $db= PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_receivedpayments_rule where deleted=0");
        if(!$db->num_rows($result)){
            return array();
        }
        $ruleIds=array();
        $isAdmin=0;
        if($userId){
            global $current_user;
            $user=new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
            $result2 = $db->pquery("select * from vtiger_receivedpayments_permission where userid=?",array($userId));
            if($db->num_rows($result2)){
                while ($row2=$db->fetchByAssoc($result2)){
                    $ruleIds[]=$row2['ruleid'];
                }
            }
            if($current_user->is_admin=='on'){
                $isAdmin=1;
            }
        }

        while ($row=$db->fetchByAssoc($result)){
            if(!in_array($row['id'],$ruleIds) && count($ruleIds)>0 && !$isAdmin){
                continue;
            }
            $rules[$row['id']]=$row['name'];
        }

        return $rules;
    }

    public function getRulePermissions(){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT permission.receivedpaymentspermissionid AS id,users.last_name, rule.parent, rule.name
        FROM vtiger_receivedpayments_permission permission
        INNER JOIN vtiger_users users ON permission.userid = users.id
        INNER JOIN vtiger_receivedpayments_rule rule ON permission.ruleid = rule.id AND rule.deleted=0");
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            $data[]=$row;
        }
        return $data;
    }

    public function handleData($sql){
        $db = PearDatabase::getInstance();
        $result = $db->pquery($sql,array());
        if(!$db->num_rows($result)){
            return array();
        }

        $data= array();
        $monthData=array();
        while ($row = $db->fetchByAssoc($result)){
            $data[$row['ruleid']] +=$row['total'];
            $monthData[$row['ruleid']][$row['month']] =$row['total'];

        }
        return array($data,$monthData);

    }

    public function getMonthList($startMonth, $endMonth){
        $data[] = $startMonth;
        for($i=1;$i<=1000;$i++){
            $startMonth=date("Y-m", strtotime("+1 month",strtotime($startMonth)));
            if(strtotime($startMonth)>strtotime($endMonth)){
                return $data;
            }
            $data[]=$startMonth;
        }
        return $data;
    }

    /**
     * 获取统计数据
     * @param $startMonth
     * @param $endMonth
     * @param $ruleIds
     * @return array
     */
    public function getClassifyData($startMonth,$endMonth,$ruleIds){
        if(empty($ruleIds)) {
            global $current_user;
            $ruleIds = $this->getSecondReceivedPaymentsRules($current_user->id);
            $ruleIds = array_keys($ruleIds);
            $allLevelRules = $this->getReceivedPaymentsRules($current_user->id);
        } else {
            $allLevelRules = $this->getGroupRules($ruleIds);
        }
        $ruleIdStr = implode(',',$ruleIds);
        $db = PearDatabase::getInstance();
        //当期计划数
        $ruleData=array();
        $sql = "select sum(amount) as amounttotal, ruleid from vtiger_receivedpayments_plan where month >='".$startMonth."' and month<='".$endMonth."' group by ruleid";
        $result = $db->pquery($sql,array());
        while ($row=$db->fetchByAssoc($result)){
            $ruleData[$row['ruleid']]=$row['amounttotal'];
        }
        $startDate = $startMonth . '-01';
        $endDate = $endMonth . '-31';
        //当期回款数据—已匹配
        $sql = "SELECT SUM(unit_price) AS total, ruleid, left(reality_date,7) AS month FROM 
        (SELECT unit_price, IF(artificialclassfication=0, systemclassfication, artificialclassfication) AS ruleid, reality_date 
        FROM vtiger_receivedpayments WHERE deleted=0 AND receivedstatus='normal' AND ismatchdepart=1 AND reality_date>='{$startDate}' AND reality_date<='{$endDate}')
        receivedpayments  WHERE ruleid IN({$ruleIdStr}) GROUP BY ruleid, month";
        $matchedData = $this->handleData($sql);
        //当期回款数据—未匹配
        $sql = "SELECT SUM(unit_price) AS total, ruleid, left(reality_date,7) AS month FROM 
        (SELECT unit_price, IF(artificialclassfication=0, systemclassfication, artificialclassfication) AS ruleid, reality_date 
        FROM vtiger_receivedpayments WHERE deleted=0 AND receivedstatus='normal' AND ismatchdepart=0 AND reality_date>='{$startDate}' AND reality_date<='{$endDate}')
        receivedpayments  WHERE ruleid IN({$ruleIdStr}) GROUP BY ruleid, month";
        $umMatchedData = $this->handleData($sql);
        //上期回款数
        $lastPeriod=$this->getLastPeriod($startMonth,$endMonth);
        $lastStartMonth=$lastPeriod[0];
        $lastEndMonth=$lastPeriod[1];
        $startDate = $lastStartMonth . '-01';
        $endDate = $lastEndMonth . '-31';
        $sql = "SELECT SUM(unit_price) AS total, ruleid FROM 
        (SELECT unit_price, IF(artificialclassfication=0, systemclassfication, artificialclassfication) AS ruleid, reality_date 
        FROM vtiger_receivedpayments WHERE deleted=0 AND receivedstatus='normal' AND reality_date>='{$startDate}' AND reality_date<='{$endDate}')
        receivedpayments  WHERE ruleid IN({$ruleIdStr}) GROUP BY ruleid";
        $result = $db->pquery($sql);
        $lastPeriodData = [];
        if($db->num_rows($result)){
            while ($row = $db->fetchByAssoc($result)){
                $lastPeriodData[$row['ruleid']] = $row['total'];
            }
        }

        //去年同期回款数
        $lastYearStartMonth = date("Y-m",strtotime("-1 year",strtotime($startMonth)));
        $lastYearEndMonth  = date('Y-m',strtotime("-1 year",strtotime($endMonth)));

        $startDate = $lastYearStartMonth . '-01';
        $endDate = $lastYearEndMonth . '-31';
        $sql = "SELECT SUM(unit_price) AS total, ruleid FROM
        (SELECT unit_price, IF(artificialclassfication=0, systemclassfication, artificialclassfication) AS ruleid, reality_date 
        FROM vtiger_receivedpayments WHERE deleted=0 AND receivedstatus='normal' AND reality_date>='{$startDate}' AND reality_date<='{$endDate}')
        receivedpayments  WHERE ruleid IN({$ruleIdStr}) GROUP BY ruleid";
        $result = $db->pquery($sql);
        $lastYearPeriodData = [];
        if($db->num_rows($result)){
            while ($row = $db->fetchByAssoc($result)) {
                $lastYearPeriodData[$row['ruleid']] = $row['total'];
            }
        }
        //当年回款总额
        $currentYearTotal=[];
        $startYear = substr($startMonth,0,4);
        $endYear = substr($endMonth,0,4);
        //判断日期是否跨年
        if ($startYear == $endYear) {
            $startDate = $startYear.'-01-01';
            $endDate = $startYear.'-12-31';
            $sql = "SELECT SUM(unit_price) AS total, ruleid FROM 
            (SELECT unit_price, IF(artificialclassfication=0, systemclassfication, artificialclassfication) AS ruleid, reality_date 
            FROM vtiger_receivedpayments WHERE deleted=0 AND receivedstatus='normal' AND reality_date>='{$startDate}' AND reality_date<='{$endDate}')
            receivedpayments WHERE ruleid IN({$ruleIdStr}) GROUP BY ruleid";
            $result = $db->pquery($sql);
            if($db->num_rows($result)){
                while ($row = $db->fetchByAssoc($result)){
                    $currentYearTotal[$row['ruleid']] = $row['total'];
                }
            }
        }

        $eChartsFields = $this->getMonthList($startMonth,$endMonth);
        $classifyData=[];
        $eChartsData = [];
        $matchedDataTotalAll=0;
        $umMatchedDataTotalAll=0;
        $ruleDataTotalAll=0;
        $currentYearTotalTotalAll=0;
        $lastPeriodDataTotalAll=0;
        $lastYearPeriodDataTotalAll=0;

        $aasMatchedDataTotal=0;
        $aasUmMatchedDataTotal=0;
        $aasRuleDataTotal=0;
        $aasCurrentYearTotalTotal=0;
        $aasLastPeriodDataTotal=0;
        $aasLastYearPeriodDataTotal=0;
        $aasTypes = ['saas', 'paas', 'iaas'];
        $aasCount = 0;
        foreach ($allLevelRules as $key=>$allLevelRule) {
            $matchedDataTotal=0;
            $umMatchedDataTotal=0;
            $ruleDataTotal=0;
            $currentYearTotalTotal=0;
            $lastPeriodDataTotal=0;
            $lastYearPeriodDataTotal=0;
            $rowData = [];
            if (in_array($key, $aasTypes)) {
                $aasCount ++;
            }
            foreach ($allLevelRule as $key1=>$rule) {
                $rowData[] = [
                    $rule,
                    $matchedData[0][$key1]? $matchedData[0][$key1]: 0,
                    $umMatchedData[0][$key1]? $umMatchedData[0][$key1]: 0,
                    ($matchedData[0][$key1]+$umMatchedData[0][$key1]),
                    $ruleData[$key1] ? $ruleData[$key1]: 0,
                    $ruleData[$key1]>0?round((($matchedData[0][$key1]+$umMatchedData[0][$key1])/$ruleData[$key1])*100,2).'%':'/',
                    $lastPeriodData[$key1] ? $lastPeriodData[$key1]: 0,
                    $lastPeriodData[$key1]>0 ? round((($matchedData[0][$key1]+$umMatchedData[0][$key1])/$lastPeriodData[$key1])*100,2).'%':'/',
                    $lastYearPeriodData[$key1] ? $lastYearPeriodData[$key1]: 0,
                    $lastYearPeriodData[$key1]>0? round((($matchedData[0][$key1]+$umMatchedData[0][$key1])/$lastYearPeriodData[$key1])*100,2).'%':'/',
                    $currentYearTotal[$key1] ? $currentYearTotal[$key1]: 0
                ];
                $matchedDataTotal +=$matchedData[0][$key1];
                $umMatchedDataTotal +=$umMatchedData[0][$key1];
                $lastPeriodDataTotal +=$lastPeriodData[$key1];
                $ruleDataTotal +=$ruleData[$key1];
                $lastYearPeriodDataTotal +=$lastYearPeriodData[$key1];
                $currentYearTotalTotal +=$currentYearTotal[$key1];
                if (in_array($key, $aasTypes)) {
                    $aasMatchedDataTotal += $matchedData[0][$key1];
                    $aasUmMatchedDataTotal += $umMatchedData[0][$key1];
                    $aasRuleDataTotal += $ruleData[$key1];
                    $aasLastPeriodDataTotal += $lastPeriodData[$key1];
                    $aasLastYearPeriodDataTotal += $lastYearPeriodData[$key1];
                    $aasCurrentYearTotalTotal += $currentYearTotal[$key1];
                }
                $eChartsDataList=[];
                foreach ($eChartsFields as $eChartsField) {
                    $total = $matchedData[1][$key1][$eChartsField]+$umMatchedData[1][$key1][$eChartsField];
                    $eChartsDataList[] = $total>0?$total:0;
                }
                $eChartsData[] = [
                    'name'=>$rule,
                    'type'=>'line',
                    'data'=>$eChartsDataList
                ];
            }
            /* 仅当有多个二级分类时显示合计 */
            if(count($allLevelRule)>1) {
                $rowData[] = [
                    '合计',
                    $matchedDataTotal,
                    $umMatchedDataTotal,
                    $matchedDataTotal + $umMatchedDataTotal,
                    $ruleDataTotal,
                    $ruleDataTotal > 0 ? round((($matchedDataTotal + $umMatchedDataTotal) / $ruleDataTotal) * 100, 2) . '%' : '',
                    $lastPeriodDataTotal,
                    $lastPeriodDataTotal > 0 ? round((($matchedDataTotal + $umMatchedDataTotal) / $lastPeriodDataTotal) * 100, 2) . '%' : '/',
                    $lastYearPeriodDataTotal,
                    $lastYearPeriodDataTotal > 0 ? round((($matchedDataTotal + $umMatchedDataTotal) / $lastYearPeriodDataTotal) * 100, 2) . '%' : "/",
                    $currentYearTotalTotal
                ];
            }
            $matchedDataTotalAll+=$matchedDataTotal;
            $umMatchedDataTotalAll+=$umMatchedDataTotal;
            $ruleDataTotalAll+=$ruleDataTotal;
            $currentYearTotalTotalAll+=$currentYearTotalTotal;
            $lastPeriodDataTotalAll+=$lastPeriodDataTotal;
            $lastYearPeriodDataTotalAll+=$lastYearPeriodDataTotal;
            $classifyData[] = [
                'key' => $key,
                'value' => $rowData
            ];
        }
        /* 判断是否显示aas合计 */
        if ($aasCount > 1) {
            $classifyData[] = [
                "key"=>'',
                "value"=>[[
                'aas合计',
                $aasMatchedDataTotal,
                $aasUmMatchedDataTotal,
                $aasMatchedDataTotal + $aasUmMatchedDataTotal,
                $aasRuleDataTotal,
                $aasRuleDataTotal > 0 ? round((($aasMatchedDataTotal + $aasUmMatchedDataTotal) / $aasRuleDataTotal) * 100, 2) . '%' : '',
                $aasLastPeriodDataTotal,
                $aasLastPeriodDataTotal > 0 ? round((($aasMatchedDataTotal + $aasUmMatchedDataTotal) / $aasLastPeriodDataTotal) * 100, 2) . '%' : '/',
                $aasLastYearPeriodDataTotal,
                $aasLastYearPeriodDataTotal > 0 ? round((($aasMatchedDataTotal + $aasUmMatchedDataTotal) / $aasLastYearPeriodDataTotal) * 100, 2) . '%' : "/",
                $aasCurrentYearTotalTotal
                ]]
            ];
        }
        /* 仅当有多个一级分类时显示全部合计 */
        if(count($allLevelRules)>1) {
            $classifyData[] = [
                "key"=>'',
                "value"=>[[
                    '全部合计',
                    $matchedDataTotalAll,
                    $umMatchedDataTotalAll,
                    $matchedDataTotalAll+$umMatchedDataTotalAll,
                    $ruleDataTotalAll,
                    $ruleDataTotalAll>0?round((($matchedDataTotalAll+$umMatchedDataTotalAll)/$ruleDataTotalAll)*100,2).'%':'/',
                    $lastPeriodDataTotalAll,
                    $lastPeriodDataTotalAll>0?round((($matchedDataTotalAll+$umMatchedDataTotalAll)/$lastPeriodDataTotalAll)*100,2).'%':'/',
                    $lastYearPeriodDataTotalAll,
                    $lastYearPeriodDataTotalAll>0?round((($matchedDataTotalAll+$umMatchedDataTotalAll)/$lastYearPeriodDataTotalAll)*100,2).'%':'/',
                    $currentYearTotalTotalAll
               ]]
            ];
        }
        return [
          'classifyData'=>$classifyData,
          'eChartsField'=>$eChartsFields,
          'eChartsData'=>$eChartsData
        ];
    }

    public function getGroupRules($ruleIds){
        $db= PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_receivedpayments_rule where deleted=0 and id in(".implode(",",$ruleIds).')');
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            $rules[$row['parent']][$row['id']]=$row['name'];
        }
        return $rules;
    }

    public function getLastPeriod($startMonth,$endMonth){
        $start = explode("-",$startMonth);
        $end = explode("-",$endMonth);
        if($start[0]<$end[0]){
            $duration = $end[1]+(intval($end[0])-intval($start[0]))*12-$start[1]+1;
        }else{
            $duration=$end[1]-$start[1]+1;
        }
        $lastStartMonth = date("Y-m",strtotime("-{$duration} month",strtotime($startMonth)));
        $lastEndMonth = date("Y-m",strtotime("-{$duration} month",strtotime($endMonth)));
        return array($lastStartMonth,$lastEndMonth);
    }

    /**
     * 给回款作系统分类
     * @param $receivedPaymentSid
     */
    public function systemClassification($receivedPaymentSid){
        $db = PearDatabase::getInstance();
        $sql = "select a.matchdate,a.companyaccountsid,b.contract_no,b.signid from vtiger_receivedpayments a 
        left join vtiger_servicecontracts b on a.relatetoid=b.servicecontractsid
        where a.receivedpaymentsid=?";
        $result = $db->pquery($sql,array($receivedPaymentSid));
        if(!$db->num_rows($result)){
            return;
        }
        $row = $db->fetchByAssoc($result,0);
        //走未匹配回款逻辑
        if(!$row['matchdate']){
            $ruleid = $this->getNoMatchRuleId($row['companyaccountsid']);
        }else{
            //走已匹配回款判断逻辑
            $ruleid = $this->getMatchedRuleId($row['contract_no'],$row['signid']);
        }
        if (!$ruleid) {
            $ruleid = 0;
        }
        $db->pquery("update vtiger_receivedpayments set systemclassfication=? where receivedpaymentsid=?",array($ruleid,$receivedPaymentSid));
    }

    /**
     * 未匹配回款规则
     * @param $companyaccountsid
     * @return int|mixed
     */
    public function getNoMatchRuleId($companyaccountsid) {
        $db = PearDatabase::getInstance();
        $sql = "SELECT rule_companyaccount.ruleid FROM vtiger_receivedpayments_rule_companyaccount AS rule_companyaccount
        INNER JOIN vtiger_receivedpayments_rule AS rule ON rule_companyaccount.ruleid = rule.id AND rule.deleted = 0
        INNER JOIN vtiger_companyaccounts AS companyaccounts
        ON ((companyaccounts.id=rule_companyaccount.companyaccountid AND rule_companyaccount.companyaccountid !=0)
        OR (companyaccounts.company=rule_companyaccount.company AND rule_companyaccount.companyaccountid =0)) WHERE companyaccounts.id=?";
        $result = $db->pquery($sql, array($companyaccountsid));
        if(!$db->num_rows($result)){
            return 0;
        }
        $row=$db->fetchByAssoc($result,0);
        return $row['ruleid'];
    }

    /**
     * 已匹配回款规则
     * @param $contract_no
     * @param int $signid
     * @return int|mixed
     */
    public function getMatchedRuleId($contract_no, $signid = 0) {
        /*$contractNoArr = explode("-",$contract_no);
        $contractNoStr='';
        $countNum = count($contractNoArr);
        if($countNum==2){
            $contractNoStr=$contractNoArr[1];
        }elseif ($countNum>2){
            foreach ($contractNoArr as $key=>$value){
                if($key==0){
                    continue;
                }
                $contractNoStr .=$value.'-';
            }
            $contractNoStr = rtrim($contractNoStr,'-');
        }else{
            $contractNoStr=$contract_no;
        }
        $contractNoRevStr = strval(strrev($contractNoStr));
        $prefix = $this->getContractNoPrefix($contractNoRevStr);*/
        $db = PearDatabase::getInstance();
        if($contract_no) {
            //优先匹配长度大的编号
            $sql = "SELECT rule_contract.ruleid FROM vtiger_receivedpayments_rule_contract AS rule_contract 
            INNER JOIN vtiger_receivedpayments_rule AS rule ON rule_contract.ruleid = rule.id AND rule.deleted = 0 
            WHERE locate(rule_contract.contract_prefix, ?) > 0 ORDER BY CHAR_LENGTH(rule_contract.contract_prefix) DESC";
            $result = $db->pquery($sql, [$contract_no]);
            $numRows = $db->num_rows($result);
            if ($numRows) {
                $row = $db->fetchByAssoc($result, 0);
                return $row['ruleid'];
            }
        }
        if(is_numeric($signid)) {
            //如果非唯一 或者不存在 则走二级筛选
            $sql = "select c.parentdepartment from vtiger_users a left join vtiger_user2department b on a.id=b.userid left join vtiger_departments c on c.departmentid=b.departmentid where a.id=?";
            $result2 = $db->pquery($sql, array($signid));
            if (!$db->num_rows($result2)) {
                return 0;
            }
            $row2 = $db->fetchByAssoc($result2, 0);
            $parentdepartment = $row2['parentdepartment'];
            list($topParentId, $parentId, $departmentId) = explode("::", $parentdepartment);
            if (!$parentId) {
                return 0;
            }
            if (!$departmentId) {
                $departmentId = '';
            }
            $sql="SELECT rule_department.ruleid FROM vtiger_receivedpayments_rule_department AS rule_department
            INNER JOIN vtiger_receivedpayments_rule AS rule ON rule_department.ruleid = rule.id AND rule.deleted=0
            WHERE ((rule_department.parentid = ? AND rule_department.departmentid='') OR rule_department.departmentid = ?)";
            $result3 = $db->pquery($sql, [$parentId, $departmentId]);
            $numRows3 = $db->num_rows($result3);
            if ($numRows3) {
                $row3 = $db->fetchByAssoc($result3, 0);
                return $row3['ruleid'];
            }
        }
        return 0;
    }

    public function getContractNoPrefix($contractNo){
        for($i=9;$i<strlen($contractNo);$i++) {
            if (!is_numeric($contractNo[$i])) {
                return strrev(substr($contractNo, $i));
            }
        }
        return $contractNo;
    }

}
