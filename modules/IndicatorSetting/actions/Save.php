<?php

class IndicatorSetting_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        global $current_user;
        $now_time = date('Y-m-d H:i:s');
        $departmentid = $request->get("departmentid");
        $createdid = $request->get('createdid');
        $recordId = $request->get('record');

        //权限判定
        for ($i = 1; $i < 6; $i++) {
            if (!$request->get("staff_stage{$i}")) {
                continue;
            }
            $telnumber = $request->get("telnumber{$i}");
            $telduration = $request->get("telduration{$i}");
            $intended_number = $request->get("intended_number{$i}");
            $invite_number = $request->get("invite_number{$i}");
            $visit_number = $request->get("visit_number{$i}");
            $returned_money = $request->get("returned_money{$i}");
            $remark = $request->get("remark{$i}");
            $staff_stage = $request->get("staff_stage{$i}");
//            if (!$telnumber || !$telduration || !$intended_number || !$invite_number || !$visit_number || !$returned_money) {
//                continue;
//            }

            $relationship_or = $request->get("relationship_or{$i}");
            if (count($relationship_or) > 0) {
                $relationship_or = implode(',', $relationship_or);
            }
            $adb = PearDatabase::getInstance();
            if ($recordId) {
                //记录变动
                IndicatorSetting_Record_Model::saveIndicatorOperateHistories($recordId,$current_user->id,$i,$request);

                $sql = ' update vtiger_indicatorsetting set modifiedby=? , modifiedtime=? , departmentid=? , staff_stage=? , 
 telnumber=? , telduration=? , intended_number=? , invite_number=? , visit_number=? , returned_money=? , remark=? , relationship_or=? where id=?' ;
                $array =array($current_user->id,$now_time,$departmentid,$staff_stage,$telnumber,$telduration,$intended_number,$invite_number,$visit_number,$returned_money,$remark,$relationship_or,$recordId);
                $adb->pquery($sql,$array);
                $record_model_id = $recordId;
            } else {
                $sql = 'insert into vtiger_indicatorsetting(createdid,createdtime,departmentid,staff_stage,telnumber,telduration,intended_number,
invite_number,visit_number,returned_money,remark,relationship_or) values(?,?,?,?,?,?,?,?,?,?,?,?)';
                $array =array($createdid,$now_time,$departmentid,$staff_stage,$telnumber,$telduration,$intended_number,$invite_number,$visit_number,$returned_money,$remark,$relationship_or,$recordId);
                $adb->pquery($sql,$array);
                $record_model_id = $adb->getLastInsertID();

            }

            //创建特殊条件设置
            if ($request->get("staff_stage{$i}_basics_value")) {
                $basics_columns = $request->get("staff_stage{$i}_basics_column");
                foreach ($basics_columns as $key => $basics_column) {
                    $basics_value = $request->get("staff_stage{$i}_basics_value")[$key];
                    $operate_value = $request->get("staff_stage{$i}_operate_value")[$key];
                    if (!$basics_value||!$operate_value) {
                        continue;
                    }
                    $basics_operator = $request->get("staff_stage{$i}_basics_operator")[$key];
                    $operate_column = $request->get("staff_stage{$i}_operate_column")[$key];
                    $operate_operator = $request->get("staff_stage{$i}_operate_operator")[$key];
                    $special_operator_id = $request->get('special_operator_id')[$key];
                    if ($special_operator_id) {
                        $sql = "update vtiger_special_operation set `indicatorsettingid`={$record_model_id},
`basics_column`='{$basics_column}',`basics_operator`='{$basics_operator}',`basics_value`='{$basics_value}',
`operate_column`='{$operate_column}',`operate_operator`='{$operate_operator}',`operate_value`={$operate_value}  where id = ?";
                        $adb->pquery($sql, array($special_operator_id));
                    } else {
                        $sql = "insert into vtiger_special_operation (`indicatorsettingid`,`basics_column`,`basics_operator`,`basics_value`,`operate_column`,`operate_operator`,`operate_value`)
                    values({$record_model_id},'{$basics_column}','{$basics_operator}',{$basics_value},'{$operate_column}','{$operate_operator}',{$operate_value})";
                        $adb->pquery($sql, array());
                    }

                }
            }
        }
        $recordModel = IndicatorSetting_Record_Model::getInstanceById($recordId,'IndicatorSetting');
        $recordModel->settingToCache();

        //新建就返回到列表 修改则跳转到详情页面
        if (!$recordId) {
            header("Location: index.php?module=IndicatorSetting&view=List");
            exit;
        }
        header("Location: index.php?module=IndicatorSetting&view=Detail&record=" . $recordId);
    }
}
