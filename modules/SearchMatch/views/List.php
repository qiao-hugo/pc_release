<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
//error_reporting(-1);
//ini_set("display_errors",1);
set_time_limit(0);
ini_set('memory_limit', '-1');
class SearchMatch_List_View extends Vtiger_KList_View {
    public function process(Vtiger_Request $request)
    {
        // 回款拆分的权限
        global $adb, $current_user;
        $sql = "select * FROM vtiger_custompowers where custompowerstype='split_received_rayments'";
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
                    }
                }
            }
        }
        parent::process($request);
    }
}