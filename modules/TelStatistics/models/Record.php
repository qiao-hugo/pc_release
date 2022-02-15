<?php

class TelStatistics_Record_Model extends Vtiger_Record_Model
{
    static function getReportPermissions()
    {
        $db = PearDatabase::getInstance();
        $query = "SELECT a.telstatisticsmanageid as id,b.rolename,a.classnamezh,a.module 
        FROM vtiger_telstatistics_manage a LEFT JOIN vtiger_role  b ON a.roleid=b.roleid 
        WHERE a.deleted=0 ORDER BY a.telstatisticsmanageid DESC";
        return $db->run_query_allrecords($query);
    }

    /**
     * 模块的名称做通用的
     * @return array
     */
    public static function getModulePicklist()
    {
        $db = PearDatabase::getInstance();
        $query = "SELECT mountmodule as module
        FROM vtiger_mountmodule where type = 'TelStatistics' ";
        return $db->run_query_allrecords($query);
    }


    /**
     * 显示可导出的列表
     * @return array
     */
    public static function getSetPermissions()
    {
        $db = PearDatabase::getInstance();
        $query = "SELECT * FROM vtiger_telstatistics_rpatymtable WHERE deleted=0";
        $result = $db->run_query_allrecords($query, array());
        $arr = array();
        if (!empty($result)) {
            foreach ($result as $value) {
                $arr[$value['module']] .= '<option value="' . $value['mode'] . '">' . $value['modename'] . '</option>';
            }
        }
        return json_encode($arr);
    }

    /**
     *
     */
    public static function isInManage($user_id)
    {

    }

    public static function matchingStandard($departmentid,$currentDiffMonth,$params){
        $telnumber = $params['telnumber'];
        $telduration = $params['telduration'];
        $intended_number = $params['addacounts'] + $params['transferaccount'] + $params['highseaaccount'];#意向客户数
        $invite_number = $params['billvisits'] + $params['numbervisitors'];#邀约量
        $visit_number = $params['nactualvisitors'];
        $returned_money = $params['amountpaid'];

        $staff_stage = $currentDiffMonth >= 0 ?
            ($currentDiffMonth > 1 ?
                ($currentDiffMonth > 3 ?
                    ($currentDiffMonth > 6 ?
                        ($currentDiffMonth > 12 ? '5' : '4')
                        : '3')
                    : '2')
                : '1')
            : '5';

        $departments = getSuperirosDepartments($departmentid);
        krsort($departments);
        $pending_columns = array('telnumber', 'telduration', 'intended_number', 'invite_number', 'visit_number', 'returned_money');
        require('crmcache/indicatorsetting.php');
        $indicatorsetting = TelStatistics_Record_Model::getSettedIndicatorByDepartmentids($indicatorsetting,$departments, $staff_stage);
        if($indicatorsetting[0]){
            foreach ($pending_columns as $pending_column){
                if($$pending_column >= $indicatorsetting[0][$pending_column]){
                    $data[$pending_column] = true;
                }else{
                    $data[$pending_column] = false;
                }
            }
        }
        return $data;
    }

    /**
     * 判定是否达标
     */
    public static function isReachStandard($indicatorsetting,$specialoperation,$departmentid, $params, $currentDiffMonth)
    {
        $telnumber = $params['telnumber'];
        $telduration = $params['telduration'];
        $intended_number = $params['addacounts'] + $params['transferaccount'] + $params['highseaaccount'];#意向客户数
        $invite_number = $params['billvisits'] + $params['numbervisitors'];#邀约量
        $visit_number = $params['nactualvisitors'];
        $returned_money = $params['amountpaid'];

        $staff_stage = $currentDiffMonth >= 0 ?
            ($currentDiffMonth > 1 ?
                ($currentDiffMonth > 3 ?
                    ($currentDiffMonth > 6 ?
                        ($currentDiffMonth > 12 ? '5' : '4')
                        : '3')
                    : '2')
                : '1')
            : '5';
        $adb = PearDatabase::getInstance();

        $departments = getSuperirosDepartments($departmentid);
        krsort($departments);
//        require('crmcache/indicatorsetting.php');
        $indicatorsetting = TelStatistics_Record_Model::getSettedIndicatorByDepartmentids($indicatorsetting,$departments, $staff_stage);
        //如果该部门和该部门上级部门都没有设置  则返回未知
        if (!$indicatorsetting[0]) {
            return '未知';
        }
        $key = $indicatorsetting[1];

        //获取比较符号
        $operate_operations = IndicatorSetting_Module_Model::$operate_operatoes;
        $pending_columns = array('telnumber', 'telduration', 'intended_number', 'invite_number', 'visit_number', 'returned_money');
        foreach ($pending_columns as $pending_column){
            $standard = 'standard_' . $pending_column;
            $$standard = $indicatorsetting[0][$pending_column];
        }
        foreach ($pending_columns as $pending_column) {
            $operationDatas = $specialoperation[$key];
            foreach ($operationDatas as $operationData){
                if($operationData['indicatorsettingid']==$indicatorsetting[0]['id'] &&
                    $operationData['basics_column']==$pending_column &&
                    $operationData['basics_value']<=  $$pending_column){
                    $standard_result[] = $operationData;
                    $standard_results[] = $operationData;
                }
                foreach ($standard_results as $key => $standard_result) {
                    $basics_column = $standard_result['basics_column'];
                    $basics_operation = $operate_operations[$standard_result['basics_operator']];
                    $basics_value = $standard_result['basics_value'];
                    $operate_column = $standard_result['operate_column'];
                    $operate_operator = $standard_result['operate_operator'];
                    $operate_value = $standard_result['operate_value'];
                    $compare_standard = 'standard_' . $operate_column;
                    $$compare_standard = $indicatorsetting[0][$operate_column];
                    switch ($operate_operator) {
                        case '+':
                            $$compare_standard += $operate_value;
                            break;
                        case '-':
                            $$compare_standard -= $operate_value;
                            break;
                        case '*':
                            $$compare_standard = $$compare_standard * $operate_value;
                            break;
                        case '/':
                            $$compare_standard = $$compare_standard / $operate_value;
                            break;
                    }
                }
            }
        }
        //处理或者关系
        $last_result = false;
        $filter_column_result = true;
        $relationship_ors = explode(',', $indicatorsetting[0]['relationship_or']);
        foreach ($relationship_ors as $relationship_or) {
            $standard = 'standard_' . $relationship_or;
            $result = $$relationship_or >= $$standard;
            if ($result) {
                $last_result = true;
            }
        }
        $filter_columns = array_diff($pending_columns, $relationship_ors);
        foreach ($filter_columns as $filter_column) {
            $standard = 'standard_' . $filter_column;
            $filter_column_compare_result = $$filter_column >= $$standard;
            if (!$filter_column_compare_result) {
                $filter_column_result = false;
            }
        }
        if ($last_result && $filter_column_result) {
            return '达标';
        }
        return '不达标';
    }

    public static function getSettedIndicatorByDepartmentids($indicatorsetting,$departmentids, $staff_stage)
    {
        $adb = PearDatabase::getInstance();
        foreach ($departmentids as $department) {
            $key = $staff_stage.$department;
            if(isset($indicatorsetting[$key])){
                return array($indicatorsetting[$key],$department);
            }
//            $sql = 'select * from vtiger_indicatorsetting where departmentid = ? and staff_stage =? order by id desc';
//            $indicatorsetting = $adb->pquery($sql, array($department, $staff_stage));
//            if ($adb->num_rows($indicatorsetting)) {
//                while ($row = $adb->fetchByAssoc($indicatorsetting)) {
//                    $list[] = $row;
//                }
//                return $list[0];
//            }
        }
        return array('','');
    }

    public static function getTelStasInfo($userid,$dailydate){
        $adb = PearDatabase::getInstance();
        $query = "select * from vtiger_telstatistics where useid=? and telnumberdate=? and deleted=0 limit 1";
        $result = $adb->pquery($query,array($userid,$dailydate));
        $row = $adb->fetchByAssoc($result,0);
        return $row;
    }
}
