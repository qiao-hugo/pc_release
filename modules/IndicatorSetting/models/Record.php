<?php

class IndicatorSetting_Record_Model extends Vtiger_Record_Model
{
    public static function getSpecialOperationByIndicatorSettingId($indicatorsettingid)
    {
        $adb = PearDatabase::getInstance();
        $sql = 'select * from vtiger_special_operation where indicatorsettingid = ? order by id';
        $special_operations = $adb->pquery($sql, array($indicatorsettingid));
        while ($row = $adb->fetchByAssoc($special_operations)) {
            $ret_lists[] = $row;
        }
        return $ret_lists;
    }

    public static function getAlreadySetDepartments()
    {
        $adb = PearDatabase::getInstance();
        $sql = 'select departmentid from vtiger_indicatorsetting where deleted =0 group by departmentid';
        $result = $adb->pquery($sql);
        while ($row = $adb->fetchByAssoc($result)) {
            $list[] = $row['departmentid'];
        }
        return $list;
    }

    /**
     * 执行修改的时候记录字段变动历史
     *
     * @param $recordModule
     * @param $i
     * @param $request
     * @param $module_name
     * @param $user_id
     */
    public static function saveIndicatorOperateHistories($recordid, $user_id, $i, $request)
    {
        $str = '';
        $recordModule = Vtiger_Record_Model::getInstanceById($recordid, 'IndicatorSetting');
        $operates = array('telnumber', 'telduration', 'intended_number', 'invite_number', 'visit_number', 'returned_money', 'remark', 'relationship_or');
        foreach ($operates as $operate) {
            $old_value = $recordModule->get($operate);
            $new_value = $request->get($operate . $i);
            if (is_array($new_value)) {
                $new_value = implode(',', $new_value);
            }
            if ($old_value != $new_value) {
                if ($operate == 'relationship_or') {
                    $old_value = IndicatorSetting_Module_Model::vtranslateRelation($old_value, 'IndicatorSetting');
                    $new_value = IndicatorSetting_Module_Model::vtranslateRelation($new_value, 'IndicatorSetting');
                }
                $str .= '设置: ' . vtranslate($operate, 'IndicatorSetting') . '由"' . $old_value . '"更新为"' . $new_value . '";';
            }
        }

        if ($request->get("staff_stage{$i}_basics_value")) {
            $basics_columns = $request->get("staff_stage{$i}_basics_column");
            foreach ($basics_columns as $key => $basics_column) {
                $basics_value = $request->get("staff_stage{$i}_basics_value")[$key];
                $special_operator_id = $request->get('special_operator_id')[$key];

                if (!$basics_value) {
                    continue;
                }
                $new_basics_column = $request->get("staff_stage{$i}_basics_column")[$key];
                $new_basics_operator = $request->get("staff_stage{$i}_basics_operator")[$key];
                $new_basics_value = $request->get("staff_stage{$i}_basics_value")[$key];
                $new_operate_column = $request->get("staff_stage{$i}_operate_column")[$key];
                $new_operate_operator = $request->get("staff_stage{$i}_operate_operator")[$key];
                $new_operate_value = $request->get("staff_stage{$i}_operate_value")[$key];
                if ($special_operator_id) {
                    $special_operation = IndicatorSetting_Record_Model::getSpecialOperationByRecord($special_operator_id);
                    $old_basics_column = $special_operation['basics_column'];
                    $old_basics_operator = $special_operation['basics_operator'];
                    $old_basics_value = $special_operation['basics_value'];
                    $old_operate_column = $special_operation['operate_column'];
                    $old_operate_operator = $special_operation['operate_operator'];
                    $old_operate_value = $special_operation['operate_value'];
                    if (($old_basics_operator == $new_basics_operator) && ($old_basics_value == $new_basics_value) &&
                        ($old_operate_operator == $new_operate_operator) && ($old_operate_value == $new_operate_value) &&
                        ($old_basics_column == $new_basics_column) && ($old_operate_column == $new_operate_column)) {
                        continue;
                    }
                    $str .= '特殊条件设置更改后为:' . vtranslate($new_basics_column, 'IndicatorSetting') .
                        $new_basics_operator . $new_basics_value . '则' . vtranslate($new_operate_column, 'IndicatorSetting') . $new_operate_operator . $new_operate_value . ';';

                } else {
                    $str .= '特殊条件设置为:' . vtranslate($new_basics_column, 'IndicatorSetting') .
                        '大于等于' . $new_basics_value . '则' . vtranslate($new_operate_column, 'IndicatorSetting') . '减去' . $new_operate_value . ';';
                }
            }
        }
        if(!$str){
            return;
        }
        $adb = PearDatabase::getInstance();
        $sql = 'insert into vtiger_indicator_operate_histories (indicatorsettingid,createdid,createdtime,detail_info) values(?,?,?,?)';
        $adb->pquery($sql, array($recordModule->get('id'), $user_id, date('Y-m-d H:i:s'), $str));

    }

    public static function saveSpecialOperationHistories($recordid, $user_id)
    {
        $sql = 'select * from vtiger_special_operation where id = ?';
        $adb = PearDatabase::getInstance();
        $result = $adb->pquery($sql, array($recordid));
        while ($row = $adb->fetchByAssoc($result)) {
            $list = $row;
        }
        $str = '删除特殊条件设置:' . vtranslate($list['basics_column'], 'IndicatorSetting') . '大于等于' . $list['basics_value'] . '则' .
            vtranslate($list['operate_column'], 'IndicatorSetting') . '减去' . $list['operate_value'];

        $adb = PearDatabase::getInstance();
        $sql = 'insert into vtiger_indicator_operate_histories (indicatorsettingid,createdid,createdtime,detail_info) values(?,?,?,?)';
        $adb->pquery($sql, array($list['indicatorsettingid'], $user_id, date('Y-m-d H:i:s'), $str));
    }

    public static function getSpecialOperationByRecord($recordid)
    {
        $adb = PearDatabase::getInstance();
        $sql = 'select * from vtiger_special_operation where id = ?';
        $result = $adb->pquery($sql, array($recordid));
        if ($adb->num_rows($result)) {
            while ($row = $adb->fetchByAssoc($result)) {
                $list[] = $row;
            }
        }
        return $list[0];
    }

    /**
     * 获取记录的修改历史
     * 老数据异常已屏蔽 By Joe @20150508
     * @param <type> $limit - number of latest changes that need to retrieved
     * @return <array> - list of  ModTracker_Record_Model
     */
    public static function getUpdates($parentRecordId, $pagingModel)
    {
        $db = PearDatabase::getInstance();
        $recordInstances = array();

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $listQuery = "select b.last_name,a.createdtime,a.detail_info from vtiger_indicator_operate_histories a left join vtiger_users b on a.createdid=b.id where a.indicatorsettingid=? " .
            " ORDER BY a.id DESC LIMIT $startIndex, $pageLimit";

        $result = $db->pquery($listQuery, array($parentRecordId));
        $rows = $db->num_rows($result);
        global $currentModule;
        for ($i = 0; $i < $rows; $i++) {
            $row = $db->fetchByAssoc($result);
            $recordInstances[] = $row;
        }
        return $recordInstances;
    }

    /*
     * 配置项存入缓存文件
     */
    public function settingToCache(){
        global $root_directory,$adb;
        $code = "array(";
        $result = $adb->pquery("select * from vtiger_indicatorsetting where deleted=0");
        while ($row = $adb->fetchByAssoc($result)){
            $key= $row['staff_stage'].$row['departmentid'];
            $code .= "'".$key."'=>array('telnumber'=>'".$row['telnumber']."','telduration'=>'".$row['telduration']."','intended_number'=>'".$row['intended_number']."','invite_number'=>'".
                $row['invite_number']."','visit_number'=>'".$row['visit_number']."','returned_money'=>'".$row['returned_money']."','relationship_or'=>'".$row['relationship_or']."'),";

        }

        $code2 = "array(";
        $result = $adb->pquery("select a.*,b.departmentid,b.staff_stage from vtiger_special_operation a left join vtiger_indicatorsetting b on a.indicatorsettingid=b.id where b.deleted=0");
        while ($row = $adb->fetchByAssoc($result)){
            $key= $row['staff_stage'].$row['departmentid'];
            $specialOperations[$key][] = "array('basics_column'=>'".$row['basics_column']."','basics_operator'=>'".$row['basics_operator'].
                "','basics_value'=>'".$row['basics_value']."','operate_column'=>'".$row['operate_column']."','operate_operator'=>'".$row['operate_operator']."','indicatorsettingid'=>'".$row['indicatorsettingid'].
                "','operate_value'=>'".$row['operate_value']."')";
        }
        foreach ($specialOperations as $key=>$specialOperation){
            $code2.="'".$key."'=>array(".implode(',',$specialOperation)."),";
        }

        $handle=@fopen($root_directory.'crmcache/indicatorsetting.php',"w+");
        if($handle){
            $newbuf ="<?php\n\n";
            $newbuf .= '$indicatorsetting='.rtrim($code,',').");\n";
            $newbuf .= '$specialoperation='.rtrim($code2,',').");\n";
            $newbuf .= "?>";
            fputs($handle, $newbuf);
            fclose($handle);
        }
    }
}
