<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class InputInvoice_BasicAjax_Action extends Vtiger_Action_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('checkApplicationNo');
        $this->exposeMethod('offsetAmount');
        $this->exposeMethod('saveDeductMessage');
    }


    function checkPermission(Vtiger_Request $request)
    {
        return;
    }


    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function checkApplicationNo(Vtiger_Request $request)
    {
        $applicationno = $request->get("applicationno");
        global $adb;
        $sql = "select a.refillapplicationid,a.refillapplicationno,b.vendorname from vtiger_refillapplication a left join vtiger_vendor b on a.vendorid=b.vendorid where a.refillapplicationno=? order by b.vendorname desc limit 1";
        $result = $adb->pquery($sql, array($applicationno));
        $data = array('success' => false, 'msg' => '');
        if (!$adb->num_rows($result)) {
            $data['msg'] = '未搜索到对应的充值外采单';
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
            exit;
        }
        $row = $adb->fetchByAssoc($result, 0);
        $data = array(
            'success'=>true,
            'refillapplicationid' => $row['refillapplicationid'],
            'refillapplicationno' => $row['refillapplicationno'],
            'customer_name' => $row['vendorname']
        );

        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    public function offsetAmount(Vtiger_Request $request)
    {
        $record = $request->get('record');
        $offsetAmount = $request->get('offsetamount');
        global $adb;
        $sql = "select surplusamount,subamount from vtiger_input_invoice where inputinvoiceid=? limit 1";
        $result = $adb->pquery($sql, array($record));
        $data = array('success' => false, 'msg' => '抵消欠票金额失败');
        if (!$adb->num_rows($result)) {
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
            exit();
        }
        $row = $adb->fetchByAssoc($result, 0);
        $surplusamount = $row['surplusamount'] - $offsetAmount;
        $subamount = $row['subamount'] + $offsetAmount;
        $sql = 'update vtiger_input_invoice set surplusamount=?,subamount=? where inputinvoiceid=?';
        $adb->pquery($sql, array($surplusamount, $subamount, $record));
        $data = array('success' => true, 'msg' => '抵消欠票金额成功');
        $array[0] = array('fieldname' => 'surplusamount', 'prevalue' => $row['surplusamount'], 'postvalue' => $surplusamount);
        $this->setModTracker($record, $array);

        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    public function setModTracker($recordId, $array)
    {
        $db = PearDatabase::getInstance();
        global $current_user;
        $datetime = date('Y-m-d H:i:s');
        $id = $db->getUniqueId('vtiger_modtracker_basic');
        $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $recordId, 'InputInvoice', $current_user->id, $datetime, 0));
        foreach ($array as $value) {
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, $value['fieldname'], $value['prevalue'], $value['postvalue']));
        }
    }

    public function saveDeductMessage(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $recordModel = InputInvoice_Record_Model::getCleanInstance($moduleName);
        $params = array(
            'inputinvoiceid'=>$request->get("recordid"),
            'salename'=>$request->get("salename"),
            'buyername'=>$request->get("buyername"),
            'invoicecode'=>$request->get("invoicecode"),
            'invoiceno'=>$request->get("invoiceno"),
            'servicename'=>$request->get("servicename"),
            'amount'=>$request->get("amount"),
            'taxrate'=>$request->get("taxrate"),
            'taxamount'=>$request->get("taxamount"),
            'totalpricetax'=>$request->get("totalpricetax"),
        );
        $flag = $recordModel->saveDiscountCoupon($params);
        $data = array('success' => true, 'msg' => '保存成功');
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
