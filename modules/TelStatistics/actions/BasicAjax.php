<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class TelStatistics_BasicAjax_Action extends Vtiger_Action_Controller {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('add');
        $this->exposeMethod('deleted');
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
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
    }

    /* 查看权限添加
     * @param Vtiger_Request $request
     */

    function add(Vtiger_Request $request) {
        $roleid = $request->get("roleid");
        $classname = $request->get("classname");
        $modulename = $request->get("modulename");
        $data = '添加失败';
        do {
            if (empty($roleid) || empty($modulename) || empty($classname)) {
                break;
            }
            $deletesql = 'UPDATE vtiger_telstatistics_manage SET deleted=1,deletedid=?,deletedtime=? WHERE userid=? AND module=?';
            $sql = "INSERT INTO vtiger_telstatistics_manage(roleid,module,classname,classnamezh,createdid,createdtime) SELECT ?,module,mode,modename,?,? FROM vtiger_telstatistics_rpatymtable where module=? AND mode in('" . implode("','", $classname) . "')";
            $db = PearDatabase::getInstance();
            global $current_user;
            $datetime = date('Y-m-d H:i:s');
            $db->pquery($deletesql, array($current_user->id, $datetime, $roleid, $modulename));
            $db->pquery($sql, array($roleid, $current_user->id, $datetime, $modulename));
            $data = '添加成功';
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 权限删除
     * @param Vtiger_Request $request
     */
    function deleted(Vtiger_Request $request) {
        $moduleModel = Vtiger_Module_Model::getInstance('TelStatistics');
//        if ($moduleModel->exportGrouprt('TelStatistics', 'ExportRM')) {   //权限验证
            global $current_user;
            $id = $request->get("id");
            $delsql = "UPDATE vtiger_telstatistics_manage SET deleted=1,deletedid=?,deletedtime=? WHERE telstatisticsmanageid=?";
            $db = PearDatabase::getInstance();
            $datetime = date('Y-m-d H:i:s');
            $db->pquery($delsql, array($current_user->id, $datetime, $id));
//        }
        $data = '更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
