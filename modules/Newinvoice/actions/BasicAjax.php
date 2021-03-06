<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'modules/Emails/mail.php';
class Newinvoice_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('savesignimage');
        $this->exposeMethod('relatebilling');
        $this->exposeMethod('addRedInvoice');
        $this->exposeMethod('addCancel');
        $this->exposeMethod('addCancelFlag');
        $this->exposeMethod('getNewinvoicerayment');
        $this->exposeMethod('addNewinvoice');
        $this->exposeMethod('getReceivedpayments');
        $this->exposeMethod('tovoid');
        $this->exposeMethod('is_show_tovoid');
        $this->exposeMethod('emptyInvoiceRey');
        $this->exposeMethod('isInvoicecodeextendCheck');
        $this->exposeMethod('getUserInfo');
        $this->exposeMethod('getuserlist');
        $this->exposeMethod('changereceived');
        $this->exposeMethod('docancel');
        $this->exposeMethod('makeWorkflowStages');
        $this->exposeMethod('deletedNewinvoicePayment');
        $this->exposeMethod('getRelationReceivedPayments');
        $this->exposeMethod('repeatServiceContracts');
        $this->exposeMethod('hasRepeatServiceContracts');
        $this->exposeMethod('getDetailNewinvoicerayment');
        $this->exposeMethod('getChangeServiceContracts');
        $this->exposeMethod('savesignimages');
        $this->exposeMethod('nFillInCancel');
        $this->exposeMethod('noNeedToExport');
        $this->exposeMethod('needToExport');
        $this->exposeMethod('addPreInvoiceAudit');
        $this->exposeMethod('delPreInvoiceAudit');
        $this->exposeMethod('addPreInvoiceRemind');
        $this->exposeMethod('delPreInvoiceRemind');
        $this->exposeMethod('addNewInvoiceDelay');
        $this->exposeMethod('updateNewInvoiceDelay');
        $this->exposeMethod('getFinanceList');
        $this->exposeMethod('abandonedWorkFlow');
        $this->exposeMethod('saveRedInvoiceInfo');
        $this->exposeMethod('getSystemUserOrder');
        $this->exposeMethod('disassociateDongchaliOrder');
        $this->exposeMethod('downloadPdf');
	}

	function checkPermission(Vtiger_Request $request) {
		return;
	}

    public function isInvoicecodeextendCheck(Vtiger_Request $request) {
        $invoiceextendid = $request->get('invoiceextendid');
        $s = $request->get('s');
        $sql = "SELECT invoiceextendid FROM  vtiger_newinvoiceextend WHERE 
                CONCAT(invoicecodeextend,invoice_noextend)=?
                AND deleted=0 LIMIT 1";
        $db = PearDatabase::getInstance();
        $sel_result = $db->pquery($sql, array($s));
        $res_cnt = $db->num_rows($sel_result);
        $res = array('flag'=>false);
        if ($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $res['flag'] = true;
            if($invoiceextendid > 105) {  // ??????????????????????????????
                if($invoiceextendid - 105 == $row['invoiceextendid']) {
                    $res['flag'] = false;
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($res);
        $response->emit();
    }

    public function emptyInvoiceRey(Vtiger_Request $request) {
        $invoiceid = $request->get('invoiceid');

        // ?????? ????????????
        $db = PearDatabase::getInstance();
        $sql = "select newinvoiceraymentid,receivedpaymentsid,invoicetotal from vtiger_newinvoicerayment where invoiceid=? AND deleted=0";
        $sel_result = $db->pquery($sql, array($invoiceid));
        $res_cnt = $db->num_rows($sel_result);
        if ($res_cnt > 0) {

            $recordModel = Vtiger_DetailView_Model::getInstance('Newinvoice', $invoiceid)->getRecord();
            $entityData = $recordModel ->entity->column_fields;

            while($rawData=$db->fetch_array($sel_result)) {
                $sql = "update vtiger_newinvoicerayment set deleted=1  where newinvoiceraymentid=? ";
                $db->pquery($sql, array($rawData['newinvoiceraymentid']));

                Newinvoice_Record_Model::setAllowinvoicetotalLog($rawData['receivedpaymentsid'], $rawData['invoicetotal'], '???????????????????????????????????????'. ' ????????????:'.$entityData['invoiceno'].'???');

                $sql = " UPDATE vtiger_receivedpayments SET allowinvoicetotal=allowinvoicetotal+{$rawData['invoicetotal']} WHERE receivedpaymentsid=? ";
                $db->pquery($sql, array($rawData['receivedpaymentsid']));
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($res);
        $response->emit();
    }

    // ??????????????????????????????
    public function is_show_tovoid(Vtiger_Request $request) {
        $invoiceid = $request->get('invoiceid');
        $db = PearDatabase::getInstance();
        $sql = "select invoicetype from vtiger_newinvoice where invoiceid=? limit 1";
        $sel_result = $db->pquery($sql, array($invoiceid));
        $res_cnt = $db->num_rows($sel_result);

        $res = array('flag'=>false);
        if ($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $invoicetype = $row['invoicetype'];

            $sql = "select invoiceid from vtiger_newinvoicerayment where invoiceid=? AND deleted=0 LIMIT 1";
            $sel_result = $db->pquery($sql, array($invoiceid));
            $res_cnt = $db->num_rows($sel_result);

            if ($invoicetype == 'c_billing' && $res_cnt > 0) { // ??????????????????
                $needTotal = Newinvoice_Record_Model::caclNeedTotal($invoiceid);
                if ($needTotal != 0) {  // ??????????????????????????????0
                    $res['flag'] = true;
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($res);
        $response->emit();
    }

    public function tovoid(Vtiger_Request $request){
        $invoiceextendid = $request->get('invoiceextendid'); // ??????id
        // ??????????????????
        $db = PearDatabase::getInstance();
        $sql = " update vtiger_newinvoiceextend set  invoicestatus='tovoid',processstatus=2 where invoiceextendid=?";
        $db->pquery($sql, array($invoiceextendid));
        $sql = "select invoiceid from vtiger_newinvoiceextend where invoiceextendid=? ";
        $sel_result2 = $db->pquery($sql, array($invoiceextendid));
        $res_cnt2    = $db->num_rows($sel_result2);
        if ($res_cnt2  > 0) {
            $row = $db->query_result_rowdata($sel_result2, 0);
            $invoiceid = $row['invoiceid'];
            Newinvoice_Record_Model::calcActualtotal($invoiceid, true);
        }
        //?????????????????????
        Newinvoice_Record_Model::updateInvoiceWithOutPayment($invoiceid);
        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

    public function getReceivedpayments(Vtiger_Request $request) {
        $receivedpaymentsids = $request->get('receivedpaymentsids');
        $record = $request->get('record');
        $db = PearDatabase::getInstance();

        $flag = false;
        $modulename='ServiceContracts';
        if (!empty($record)) {
            $sql = "select modulestatus,modulename from vtiger_newinvoice where invoiceid=?";
            $listResult = $db->pquery($sql, array($record));
            $res_cnt = $db->num_rows($listResult);
            if($res_cnt > 0) {
                $row = $db->query_result_rowdata($listResult, 0);
                $modulename=$row['modulename'];
                if($row['modulestatus'] == 'a_exception') {
                    $flag = true;
                }
            }
        }

        $res = array();
        $res["account_same_flag"] = 0;
        //?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????" gaocl add 2018/03/28
        if (!empty($record)) {
            if($modulename=='ServiceContracts'){
                $contracts_sql = "SELECT 1 FROM vtiger_newinvoice
                                INNER JOIN vtiger_servicecontracts ON(vtiger_newinvoice.contractid=vtiger_servicecontracts.servicecontractsid)
                                INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid)
                                WHERE vtiger_crmentity.deleted=0 AND invoiceid=?
                                AND (vtiger_newinvoice.accountid = vtiger_servicecontracts.sc_related_to OR vtiger_servicecontracts.sc_related_to=0)";
            }else{
                $contracts_sql = "SELECT 1 FROM vtiger_newinvoice
                                INNER JOIN vtiger_suppliercontracts ON(vtiger_newinvoice.contractid=vtiger_suppliercontracts.suppliercontractsid)
                                INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid = vtiger_suppliercontracts.suppliercontractsid)
                                WHERE vtiger_crmentity.deleted=0 AND invoiceid=?
                                AND (vtiger_newinvoice.accountid = vtiger_suppliercontracts.vendorid OR  vtiger_suppliercontracts.vendorid=0)";

            }
            $contracts_result = $db->pquery($contracts_sql, array($record));
            $account_count = $db->num_rows($contracts_result);
            if($account_count > 0) {
                $res["account_same_flag"] = 1;
            }
        }else{
            //????????????????????????(????????????????????????????????????????????????????????????)
            $res["account_same_flag"] = 1;
        }

        $res["invoicecompany_same_flag"] = 0;
        //?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????" gaocl add 2018/03/28
        $invoicecompany = $request->get('invoicecompany');
        if (!empty($record) && !empty($invoicecompany)) {
            $invoicecompany_sql = "SELECT vtiger_servicecontracts.invoicecompany FROM vtiger_newinvoice
                                INNER JOIN vtiger_servicecontracts ON(vtiger_newinvoice.contractid=vtiger_servicecontracts.servicecontractsid)
                                INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid)
                                WHERE vtiger_crmentity.deleted=0 AND invoiceid=?";
            $invoicecompany_result = $db->pquery($invoicecompany_sql, array($record));
            $invoicecompany_count = $db->num_rows($invoicecompany_result);
            if($invoicecompany_count > 0) {
                $row_data = $db->query_result_rowdata($invoicecompany_result,0);
                if(empty($row_data['invoicecompany']) || $row_data['invoicecompany'] == $invoicecompany){
                    $res["invoicecompany_same_flag"] = 1;
                }
            }else{
                $res["invoicecompany_same_flag"] = 1;
            }
        }else{
            //????????????????????????(????????????????????????????????????????????????????????????)
            $res["invoicecompany_same_flag"] = 1;
        }

        if(empty($record) || $flag) { // ????????? ???????????? ??????
            if (!empty($receivedpaymentsids)) {
                $sql = "select receivedpaymentsid,allowinvoicetotal from vtiger_receivedpayments where receivedpaymentsid IN ({$receivedpaymentsids})";
                $listResult = $db->pquery($sql, array());
                while($rawData=$db->fetch_array($listResult)) {
                    $res[$rawData['receivedpaymentsid']] = $rawData['allowinvoicetotal'];
                }
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($res);
        $response->emit();
    }

    public function addNewinvoice(Vtiger_Request $request) {
        $record = $request->get('record');
        $receivedpaymentsid = $request->get('receivedpaymentsid');
        $db=PearDatabase::getInstance();
        // ???????????????????????????????????????
        $res = array('falg'=>false, 'msg'=>'');
        do {
            if (empty($receivedpaymentsid)) {
                $res['msg'] = '??????????????????';
                break;
            }

            $sql = "select newinvoiceraymentid from vtiger_newinvoicerayment where invoiceid=? AND receivedpaymentsid=? AND deleted=0 LIMIT 1";
            $sel_result = $db->pquery($sql, array($record, $receivedpaymentsid));
            $res_cnt = $db->num_rows($sel_result);
            if ($res_cnt > 0) {
                $res['msg'] = '?????????????????????';
                break;
            }

            //??????????????????????????????????????????????????????????????????????????? gaocl add
            if (!empty($record)) {
                $recordModel=Vtiger_Record_Model::getInstanceById($record,'Newinvoice');
                $moudlename=$recordModel->get('modulename');
                if($moudlename=='ServiceContracts') {
                    $contracts_sql = "SELECT 1 FROM vtiger_newinvoice
                                INNER JOIN vtiger_servicecontracts ON(vtiger_newinvoice.contractid=vtiger_servicecontracts.servicecontractsid)
                                INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid)
                                WHERE vtiger_crmentity.deleted=0 AND invoiceid=?
                                AND vtiger_newinvoice.accountid = vtiger_servicecontracts.sc_related_to";
                }else{
                    $contracts_sql="SELECT 1 FROM vtiger_newinvoice
                                INNER JOIN vtiger_suppliercontracts ON(vtiger_newinvoice.contractid=vtiger_suppliercontracts.suppliercontractsid)
                                INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid = vtiger_suppliercontracts.suppliercontractsid)
                                WHERE vtiger_crmentity.deleted=0 AND invoiceid=?
                                AND vtiger_newinvoice.accountid = vtiger_suppliercontracts.vendorid";
                }
                $contracts_result = $db->pquery($contracts_sql, array($record));
                $account_count = $db->num_rows($contracts_result);
                if($account_count == 0) {
                    $res['msg'] = '???????????????????????????????????????????????????????????????????????????';
                    break;
                }
            }

            // ?????????????????????????????????????????? ????????????????????????
            $allowinvoicetotal = 0;
            $sql = "select allowinvoicetotal from vtiger_receivedpayments where receivedpaymentsid=? LIMIT 1";
            $sel_result = $db->pquery($sql, array($receivedpaymentsid));
            $res_cnt = $db->num_rows($sel_result);
            if ($res_cnt > 0) {
                $row = $db->query_result_rowdata($sel_result, 0);
                $allowinvoicetotal = $row['allowinvoicetotal'];
            }

            // ????????????
            $invoicetotal = $request->get('invoicetotal'); // ????????????
            $invoicetotal=number_format($invoicetotal,2,'.','');
            $invoicetotal=$invoicetotal*1;
            $allowinvoicetotal=number_format($allowinvoicetotal,2,'.','');
            $allowinvoicetotal=$allowinvoicetotal*1;
            if (bccomp($invoicetotal,$allowinvoicetotal)>0) {
                $res['msg'] = '???????????????????????????????????????';
                break;
            }


            // ?????? ???????????????????????? ???????????????????????????
            $needTotal = Newinvoice_Record_Model::caclNeedTotal($record);
            $needTotal=number_format($needTotal,2,'.','');
            $needTotal=$needTotal*1;
            if(bccomp($invoicetotal,$needTotal) >0) {
                $res['msg'] = '????????????????????????????????????????????????????????????';
                break;
            }
            $data = array(
                'newinvoiceraymentid'=>'',
                'servicecontractsid'=>$request->get('servicecontractsid'),
                'receivedpaymentsid'=>$receivedpaymentsid,
                'total'=>$request->get('total'),
                'arrivaldate'=>$request->get('arrivaldate'),
                'invoicetotal'=>$invoicetotal,
                'allowinvoicetotal'=>$allowinvoicetotal,
                'invoicecontent'=>$request->get('invoicecontent'),
                'remarks'=>$request->get('remarks'),
                'invoiceid'=>$record,
                'contract_no'=>$request->get('contract_no'),
                'surpluinvoicetotal'=>$request->get('invoicetotal')
            );

            //$sql = "INSERT INTO `vtiger_newinvoicerayment` (`newinvoiceraymentid`, `servicecontractsid`, `receivedpaymentsid`, `total`, `arrivaldate`, `invoicetotal`, `allowinvoicetotal`, `invoicecontent`, `remarks`, `invoiceid`, `contract_no`) VALUES ";
            $divideNames = array_keys($data);
            $divideValues = array_values($data);
            $db->pquery('INSERT INTO `vtiger_newinvoicerayment` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);


            $recordModel = Vtiger_DetailView_Model::getInstance('Newinvoice', $record)->getRecord();
            $entityData = $recordModel ->entity->column_fields;
            Newinvoice_Record_Model::setAllowinvoicetotalLog($receivedpaymentsid, - $invoicetotal, '???????????????????????????'. ' ????????????:'.$entityData['invoiceno'].'???');

            // ????????????????????? ??????????????????   invoicetotal
            $sql = " UPDATE vtiger_receivedpayments SET allowinvoicetotal=allowinvoicetotal-{$invoicetotal} WHERE receivedpaymentsid=? ";
            $db->pquery($sql, array($receivedpaymentsid));
            $db->pquery("UPDATE vtiger_newinvoice SET matchover=if(actualtotal=(SELECT sum(invoicetotal) FROM `vtiger_newinvoicerayment` WHERE deleted=0 AND invoiceid=?),1,0) WHERE invoiceid=? ", array($record,$record));
            //??????????????????????????????????????????
            Newinvoice_Record_Model::updateInvoiceRemind($record);
            $res['flag'] = true;
            $res['msg'] = '??????????????????';

        }while (0);

        $response = new Vtiger_Response();
        $response->setResult($res);
        $response->emit();

    }

    /**
     * @param Vtiger_Request $request
     *
     */
    public function deletedNewinvoicePayment(Vtiger_Request $request) {
    $record = $request->get('record');
    $db=PearDatabase::getInstance();
    // ???????????????????????????????????????
    $res = array('falg'=>false, 'msg'=>'');
    $moduleModel=Vtiger_Module_Model::getInstance("Newinvoice");
    do {
        if(!$moduleModel->exportGrouprt("Newinvoice","unlinkPayment")){
            $res['msg'] = '????????????';
            break;
        }
        if (empty($record)) {
            $res['msg'] = '??????????????????';
            break;
        }
        $sql = "SELECT `newinvoiceraymentid`, `servicecontractsid`, `receivedpaymentsid`, `total`, `arrivaldate`, `invoicetotal`, `allowinvoicetotal`, `invoicecontent`, `remarks`, `invoiceid`, `contract_no`, `deleted`, `modifiedby`, `modifiedtime`, `paytitle`, `surpluinvoicetotal` FROM `vtiger_newinvoicerayment` WHERE deleted=0 AND newinvoiceraymentid=?";
        $sel_result = $db->pquery($sql, array($record, $record));
        $res_cnt = $db->num_rows($sel_result);
        if ($res_cnt== 0) {
            $res['msg'] = '??????????????????!';
            break;
        }
        $row = $db->query_result_rowdata($sel_result, 0);
        $invoicetotal = $row['invoicetotal'];
        $receivedpaymentsid = $row['receivedpaymentsid'];
        $invoiceid = $row['invoiceid'];

        $db->pquery('UPDATE vtiger_newinvoicerayment SET deleted=1 WHERE newinvoiceraymentid=?',array($record));

        $recordModel = Vtiger_DetailView_Model::getInstance('Newinvoice', $invoiceid)->getRecord();
        $entityData = $recordModel ->entity->column_fields;
        Newinvoice_Record_Model::setAllowinvoicetotalLog($receivedpaymentsid, $invoicetotal, '?????????????????????????????????'. ' ????????????:'.$entityData['invoiceno'].'???');

        // ????????????????????? ??????????????????   invoicetotal
        $sql = " UPDATE vtiger_receivedpayments SET allowinvoicetotal=allowinvoicetotal+{$invoicetotal} WHERE receivedpaymentsid=? ";
        $db->pquery($sql, array($receivedpaymentsid));
        $db->pquery("UPDATE vtiger_newinvoice SET matchover=0 WHERE invoiceid=? ", array($invoiceid));
        //???????????????????????????????????????
        Newinvoice_Record_Model::updateInvoiceLock($invoiceid);
        $res['flag'] = true;
        $res['msg'] = '????????????????????????!';

    }while (0);
    $response = new Vtiger_Response();
    $response->setResult($res);
    $response->emit();

}
    /**
     * ????????????????????????????????????
     * @param Vtiger_Request $request
     */
    public  function hasRepeatServiceContracts(Vtiger_Request $request)
    {
        $record = $request->get('record');
        $arr_result = array('change_flag'=>Newinvoice_Record_Model::hasRepeatServiceContracts($record));
        $response = new Vtiger_Response();
        $response->setResult($arr_result);
        $response->emit();
    }

    /**
     * ????????????????????????
     * @param Vtiger_Request $request
     */
    public  function repeatServiceContracts(Vtiger_Request $request)
    {
        global $adb,$current_user;
        $record = $request->get('record');
        $service_no = $request->get('service_no');
        $arr_result = array('msg'=>'');

        $query_sql = "SELECT vtiger_servicecontracts.servicecontractsid,vtiger_servicecontracts.billcontent,vtiger_account.accountid,vtiger_servicecontracts.invoicecompany
                    FROM vtiger_servicecontracts 
                    INNER JOIN vtiger_crmentity ON(vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid)
                    LEFT JOIN vtiger_account ON(vtiger_account.accountid=vtiger_servicecontracts.sc_related_to)
                    WHERE vtiger_crmentity.deleted =0 
                    AND vtiger_servicecontracts.modulestatus = 'c_complete' AND vtiger_servicecontracts.contract_no=?
                    AND EXISTS (SELECT 1 FROM vtiger_newinvoice WHERE vtiger_newinvoice.accountid=vtiger_servicecontracts.sc_related_to)";
        $sel_result = $adb->pquery($query_sql, array($service_no));
        $res_cnt = $adb->num_rows($sel_result);

        if($res_cnt <= 0){
            $arr_result = array('msg'=>'?????????????????????????????????????????????'. $service_no .'????????????');
        }else{
            $servicecontractsid = $adb->query_result($sel_result,0,'servicecontractsid');
            $billcontent = $adb->query_result($sel_result,0,'billcontent');
            $accountid = $adb->query_result($sel_result,0,'accountid');
            $new_invoicecompany = $adb->query_result($sel_result,0,'invoicecompany');

            //?????????????????????
            $query_sql1 = "SELECT vtiger_servicecontracts.servicecontractsid,vtiger_servicecontracts.contract_no,vtiger_newinvoice.invoicecompany FROM vtiger_newinvoice
                    INNER JOIN vtiger_crmentity ON(vtiger_newinvoice.invoiceid=vtiger_crmentity.crmid)
                    LEFT JOIN vtiger_servicecontracts ON(vtiger_servicecontracts.servicecontractsid=vtiger_newinvoice.contractid)
                    WHERE vtiger_crmentity.deleted=0 AND vtiger_newinvoice.invoiceid=?";
            $sel_result1 = $adb->pquery($query_sql1, array($record));
            $res_cnt1 = $adb->num_rows($sel_result1);
            $old_servicecontractsid = 0;
            $old_contract_no = '';
            $old_invoicecompany = "";
            if($res_cnt1 > 0){
                $rowData1 = $adb->query_result_rowdata($sel_result1, 0);
                $old_servicecontractsid = $rowData1['servicecontractsid'];
                $old_contract_no = $rowData1['contract_no'];
                $old_invoicecompany = $rowData1['invoicecompany'];
            }

            //???????????????????????????????????????????????????????????????????????????????????????????????????
            if(empty($old_invoicecompany) || $new_invoicecompany == $old_invoicecompany) {
                $update_sql="UPDATE vtiger_newinvoice SET contractid=?,billingcontent=?,accountid=? WHERE invoiceid=?";
                $adb->pquery($update_sql,array($servicecontractsid,$billcontent,$accountid,$record));

                $update_sql="DELETE FROM vtiger_newinvoicerayment WHERE invoiceid=?";
                $adb->pquery($update_sql,array($record));

                //?????????????????????????????????
                $adb->pquery("INSERT INTO vtiger_newinvoice_history (`invoiceid`, `oldcontract_id`, `oldcontract_no`, `newcontract_id`, `newcontract_no`, `modifiedby`, `modifiedtime`,`remark`) VALUES (?,?,?,?,?,?,NOW(),?)",array($record,$old_servicecontractsid,$old_contract_no,$servicecontractsid,$service_no,$current_user->id,'???????????????????????????'));

                //??????????????????????????????0?????????
                /*$query_sql = "SELECT
                        vtiger_servicecontracts.invoicecompany,
                        vtiger_receivedpayments.paytitle AS t_paytitle,
                        vtiger_receivedpayments.receivedpaymentsid,
                        CONCAT(
                            vtiger_receivedpayments.reality_date,
                            '???',
                            vtiger_receivedpayments.receivedpaymentsid,
                            '???',
                            ' ???',
                            vtiger_receivedpayments.unit_price,
                            ' ',
                            vtiger_receivedpayments.paytitle,
                            ' [',
                            vtiger_servicecontracts.contract_no,
                            ']'
                        ) AS paytitle,
                        vtiger_receivedpayments.unit_price,
                        vtiger_receivedpayments.reality_date,
                        vtiger_servicecontracts.servicecontractsid,
                        vtiger_servicecontracts.contract_no,
                        vtiger_servicecontracts.billcontent AS billingcontent,
                        vtiger_receivedpayments.allowinvoicetotal
                    FROM
                        vtiger_servicecontracts
                    LEFT JOIN vtiger_receivedpayments ON (vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid)
                    WHERE
                      vtiger_receivedpayments.deleted=0
                    AND vtiger_receivedpayments.receivedstatus = 'normal'
                    AND vtiger_receivedpayments.allowinvoicetotal>0
                    AND vtiger_servicecontracts.servicecontractsid = ?";
                $sel_result = $adb->pquery($query_sql, array($servicecontractsid));
                $res_cnt = $adb->num_rows($sel_result);
                $invoicerayment = array();
                if($res_cnt > 0) {
                    while($rawData=$adb->fetch_array($sel_result)) {
                        $invoicerayment[] = $rawData;
                    }
                    //
                    for($i=0;$i<count($invoicerayment);$i++){
                        $data = array(
                            'newinvoiceraymentid'=>'',
                            'servicecontractsid'=>$servicecontractsid,
                            'receivedpaymentsid'=>$invoicerayment[$i]['receivedpaymentsid'],
                            'total'=>$invoicerayment[$i]['unit_price'],
                            'arrivaldate'=>$invoicerayment[$i]['reality_date'],
                            'invoicetotal'=>$invoicerayment[$i]['allowinvoicetotal'],
                            'allowinvoicetotal'=>$invoicerayment[$i]['allowinvoicetotal'],
                            'invoicecontent'=>$invoicerayment[$i]['billingcontent'],
                            'invoiceid'=>$record,
                            'contract_no'=>$service_no,
                            'surpluinvoicetotal'=>$invoicerayment[$i]['allowinvoicetotal']
                        );

                        $divideNames = array_keys($data);
                        $divideValues = array_values($data);
                        $adb->pquery('INSERT INTO `vtiger_newinvoicerayment` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
                    }
                }else{
                    $arr_result = array('msg'=>'??????????????????????????????'. $service_no .'??????????????????');
                }*/
            }else{
                $arr_result = array('msg'=>'??????????????????'. $service_no .'?????????????????????????????????????????????????????????');
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($arr_result);
        $response->emit();
    }

    public function getDetailNewinvoicerayment(Vtiger_Request $request){
        $recordid = $request->get('recordid');
        $servicecontractsid = $request->get('servicecontractsid');
        $modulestatus = $request->get('modulestatus');
        global $adb;
        $query_sql = "SELECT 1 FROM vtiger_newinvoicerayment WHERE deleted=0 AND invoiceid=?";
        $sel_result = $adb->pquery($query_sql, array($recordid));
        $res_cnt = $adb->num_rows($sel_result);
        $arr_result = array();

        if($res_cnt > 0) {
            $arr_result = Newinvoice_Record_Model::getNewinvoicerayment($recordid);
        }

        $query_sql = "SELECT 1 FROM vtiger_newinvoice 
                    WHERE actualtotal =
                    (SELECT SUM(invoicetotal) FROM vtiger_newinvoicerayment WHERE deleted=0 AND invoiceid=?)
                    AND invoiceid=?";
        $sel_result = $adb->pquery($query_sql, array($recordid,$recordid));
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt > 0) {
            $response = new Vtiger_Response();
            $response->setResult($arr_result);
            $response->emit();
            return;
        }

        if($modulestatus == "c_complete"){
            $recordModule=Vtiger_Record_Model::getInstanceById($recordid,'Newinvoice');
            $moudlename=$recordModule->get('modulename');
            if($moudlename=='ServiceContracts') {
            //??????????????????????????????0?????????
            $query_sql = "SELECT
                '0' AS data_flag,
                vtiger_servicecontracts.invoicecompany,
                vtiger_receivedpayments.paytitle AS paytitle,
                vtiger_receivedpayments.receivedpaymentsid,
                CONCAT(
                    vtiger_receivedpayments.reality_date,
                    '???',
                    vtiger_receivedpayments.receivedpaymentsid,
                    '???',
                    ' ???',
                    vtiger_receivedpayments.unit_price,
                    ' ',
                    vtiger_receivedpayments.paytitle,
                    ' [',
                    vtiger_servicecontracts.contract_no,
                    ']'
                ) AS paytitle,
                vtiger_receivedpayments.unit_price as total,
                vtiger_receivedpayments.reality_date as arrivaldate,
                vtiger_servicecontracts.servicecontractsid,
                vtiger_servicecontracts.contract_no,
                vtiger_servicecontracts.billcontent AS invoicecontent,
                vtiger_receivedpayments.allowinvoicetotal
            FROM
                vtiger_servicecontracts
            LEFT JOIN vtiger_receivedpayments ON (vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid)
            WHERE
              vtiger_receivedpayments.deleted=0
            AND vtiger_receivedpayments.receivedstatus = 'normal'
            AND vtiger_receivedpayments.allowinvoicetotal>0
            AND NOT EXISTS (SELECT 1 FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid AND vtiger_newinvoicerayment.deleted=0 AND vtiger_newinvoicerayment.invoiceid=?)
            AND vtiger_servicecontracts.servicecontractsid = ?";
            }else{
                $query_sql="SELECT
                '0' AS data_flag,
                vtiger_suppliercontracts.invoicecompany,
                vtiger_receivedpayments.paytitle AS paytitle,
                vtiger_receivedpayments.receivedpaymentsid,
                CONCAT(
                    vtiger_receivedpayments.reality_date,
                    '???',
                    vtiger_receivedpayments.receivedpaymentsid,
                    '???',
                    ' ???',
                    vtiger_receivedpayments.unit_price,
                    ' ',
                    vtiger_receivedpayments.paytitle,
                    ' [',
                    vtiger_suppliercontracts.contract_no,
                    ']'
                ) AS paytitle,
                vtiger_receivedpayments.unit_price as total,
                vtiger_receivedpayments.reality_date as arrivaldate,
                vtiger_suppliercontracts.suppliercontractsid as servicecontractsid,
                vtiger_suppliercontracts.contract_no,
                vtiger_suppliercontracts.billcontent AS invoicecontent,
                vtiger_receivedpayments.allowinvoicetotal
            FROM
                vtiger_suppliercontracts
            LEFT JOIN vtiger_receivedpayments ON (vtiger_suppliercontracts.suppliercontractsid = vtiger_receivedpayments.relatetoid)
            WHERE
              vtiger_receivedpayments.deleted=0
            AND vtiger_receivedpayments.receivedstatus = 'RebateAmount'
            AND vtiger_receivedpayments.allowinvoicetotal>0
            AND NOT EXISTS (SELECT 1 FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid AND vtiger_newinvoicerayment.deleted=0 AND vtiger_newinvoicerayment.invoiceid=?)
            AND vtiger_suppliercontracts.suppliercontractsid =?";
            }
            $sel_result = $adb->pquery($query_sql, array($recordid,$servicecontractsid));
            $res_cnt = $adb->num_rows($sel_result);
            if($res_cnt > 0) {
                while($rawData=$adb->fetch_array($sel_result)) {
                    $arr_result[] = $rawData;
                }
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($arr_result);
        $response->emit();
    }
    /**
     * ??????????????????????????????
     * @param Vtiger_Request $request
     */
    public  function getRelationReceivedPayments(Vtiger_Request $request){
        $servicecontractsidnum = $request->get('servicecontractsid');
//        $tyunWebRecordModel=TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
//        $data = $tyunWebRecordModel->getAllowInvoiceTotal($servicecontractsidnum);
//        if(!$data['success']){
//            $invoicerayment=array();
//            $response = new Vtiger_Response();
//            $response->setResult($invoicerayment);
//            $response->emit();
//            exit();
//        }
        global $adb;
        $query="SELECT * FROM vtiger_crmentity WHERE crmid=? LIMIT 1";
        $result=$adb->pquery($query,array($servicecontractsidnum));
        $resultdata=$adb->query_result_rowdata($result,0);
        if($resultdata['setype']!='SupplierContracts'){
            $invoicecompany='vtiger_servicecontracts.invoicecompany';
            $servicecontractsid='vtiger_servicecontracts.servicecontractsid';
            $servicecontractsid1='vtiger_servicecontracts.servicecontractsid';
            $contract_no='vtiger_servicecontracts.contract_no';
            $billcontent='vtiger_servicecontracts.billcontent';
            $tablename='vtiger_servicecontracts';
            $receivedstatus='normal';
        }else{
            $invoicecompany='vtiger_suppliercontracts.invoicecompany';
            $servicecontractsid='vtiger_suppliercontracts.suppliercontractsid AS servicecontractsid';
            $servicecontractsid1='vtiger_suppliercontracts.suppliercontractsid';
            $contract_no='vtiger_suppliercontracts.contract_no';
            $billcontent='vtiger_suppliercontracts.billcontent';
            $tablename='vtiger_suppliercontracts';
            $receivedstatus='RebateAmount';
        }

        //??????????????????????????????0?????????
        $query_sql = "SELECT
                    {$invoicecompany},
                    IF(vtiger_receivedpayments.paytitle!='',vtiger_receivedpayments.paytitle,vtiger_staypayment.payer) AS t_paytitle,
                    vtiger_receivedpayments.receivedpaymentsid,
                    CONCAT(
                        vtiger_receivedpayments.reality_date,
                        '???',
                        vtiger_receivedpayments.receivedpaymentsid,
                        '???',
                        ' ???',
                        vtiger_receivedpayments.unit_price,
                        ' ',
                        vtiger_receivedpayments.paytitle,
                        ' [',
                        {$contract_no},
                        ']'
                    ) AS paytitle,
                    vtiger_receivedpayments.unit_price,
                    vtiger_receivedpayments.standardmoney,
                    vtiger_receivedpayments.reality_date,
                    {$servicecontractsid},
                    {$contract_no},
                    {$billcontent} AS billingcontent,
                    vtiger_receivedpayments.allowinvoicetotal
                FROM
                    {$tablename}
                LEFT JOIN vtiger_receivedpayments ON ({$servicecontractsid1} = vtiger_receivedpayments.relatetoid)
                left join vtiger_staypayment on vtiger_staypayment.staypaymentid=vtiger_receivedpayments.staypaymentid
                WHERE
                  vtiger_receivedpayments.deleted=0
                AND vtiger_receivedpayments.receivedstatus = '{$receivedstatus}'
                AND vtiger_receivedpayments.allowinvoicetotal>0
                AND {$servicecontractsid1}=?";
        $sel_result = $adb->pquery($query_sql, array($servicecontractsidnum));
        $res_cnt = $adb->num_rows($sel_result);
        $invoicerayment = array();

        if($res_cnt > 0) {
            while($rawData=$adb->fetch_array($sel_result)) {
                $invoicerayment[] = $rawData;
            }
        }

        //??????????????????????????????
        $invoicedTotal=0;
        $result3 = $adb->pquery("select sum(taxtotal) as total from vtiger_newinvoice where contractid=? and modulestatus !='c_cancel'",array($servicecontractsidnum));
        if($adb->num_rows($result3)){
            $data2=$adb->fetchByAssoc($result3,0);
            $invoicedTotal=$data2['total'];
        }

        $receivedTotal=0;
        $result4 = $adb->pquery("select sum(unit_price) as total from vtiger_receivedpayments where vtiger_receivedpayments.relatetoid=? ",array($servicecontractsidnum));
        if($adb->num_rows($result4)){
            $data4=$adb->fetchByAssoc($result4,0);
            $receivedTotal=$data4['total'];
        }

        $returnData = array(
            'invoicerayment'=>$invoicerayment,
            'invoicedTotal'=>($receivedTotal-$invoicedTotal)>0?($receivedTotal-$invoicedTotal):0
        );
        $response = new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }

    public function getNewinvoicerayment(Vtiger_Request $request) {
        $account_id=$request->get('account_id');
        $invoicecompany=$request->get('invoicecompany');
        $recordid=$request->get('recordid');

        $isEdit = true;
        if (empty($recordid)) {
            $isEdit = false;
        }
        /*$sql = "SELECT vtiger_receivedpayments.receivedpaymentsid, CONCAT(vtiger_receivedpayments.paytitle,' [', vtiger_receivedpayments.unit_price, ']') AS paytitle, vtiger_receivedpayments.unit_price, vtiger_receivedpayments.reality_date, vtiger_servicecontracts.servicecontractsid, vtiger_servicecontracts.contract_no, vtiger_contractsproductsrel.billingcontent, vtiger_receivedpayments.allowinvoicetotal FROM vtiger_servicecontracts LEFT JOIN vtiger_receivedpayments ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid LEFT JOIN vtiger_contract_type ON vtiger_servicecontracts.contract_type=vtiger_contract_type.contract_type LEFT JOIN vtiger_contractsproductsrel ON vtiger_contractsproductsrel.contract_type=vtiger_contract_type.contract_typeid WHERE vtiger_servicecontracts.sc_related_to=? AND vtiger_receivedpayments.relatetoid != 0 AND vtiger_receivedpayments.relatetoid IS NOT NULL";

        $db=PearDatabase::getInstance();
        $sel_result = $db->pquery($sql, array($account_id));
        $res_cnt = $db->num_rows($sel_result);
        $invoicerayment = array();

        if($res_cnt > 0) {
            while($rawData=$db->fetch_array($sel_result)) {
                $invoicerayment[$rawData['receivedpaymentsid']] = $rawData;
            }
        }*/
        $invoicerayment = Newinvoice_Record_Model::getNewinvoiceraymentInfo($account_id, $invoicecompany, $isEdit);
        $response = new Vtiger_Response();
        $response->setResult($invoicerayment);
        $response->emit();
    }


    /**
     * ?????????????????????
     * @param Vtiger_Request $request
     */
    public function savesignimage(Vtiger_Request $request){
        $imgstring=$request->get('image');
        $recordId = $request->get('record');//?????????id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Newinvoice');
        // ???????????????
        //?????????????????????????????????????????????????????????????????????
        /*if(!Users_Privileges_Model::isPermitted('Newinvoice', 'DuplicatesHandling', $recordId)||$recordModel->entity->column_fields['modulestatus']!='c_complete' || !Invoice_Record_Model::checksign($recordId)){
            $data='';
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
            exit;
        }*/

        $newrecordid=base64_encode($recordId);
        global $root_directory,$current_user;

        $invoiceimagepath = $invoiceimagepath='/storage/invoice/';
        $imagepath=$invoiceimagepath.date('Y').'/'.date('F').'/'.date('d').'/';
        //????????????????????????????????????
        is_dir($root_directory.$imagepath) || mkdir($root_directory.$imagepath,0777,true);
        //???????????????????????????
        $newimagepath= $imagepath.$newrecordid.'.png';
        //??????????????????????????????
        $img=imagecreatefromstring(base64_decode(str_replace('data:image/png;base64,','',$imgstring)));
        //????????????????????????
        $invoiceimagewidth=imagesx($img);
        $invoiceimageheight=imagesy($img);
        //????????????????????????
        $textcolor = imagecolorallocate($img, 255, 0, 0);
        //$img????????????????????????????????????????????????????????????????????????????????????????????????$img??????a?????????
        $other=imagecreatetruecolor($invoiceimagewidth,$invoiceimageheight);
        $white=imagecolorallocate($img, 255, 255, 255);
        //$other ???????????????
        imagefill($other,0,0,$white);
        $datetime=date('Y-m-d H:i');
        //???????????????$img???
        imagestring($img,5,$invoiceimagewidth-200,$invoiceimageheight-60,$datetime,$textcolor);
        //????????????
        imagecopy($other,$img,0,0,0,0,$invoiceimagewidth,$invoiceimageheight);
        //????????????
        imagepng($other,$root_directory.$newimagepath);
        //????????????
        imagedestroy($img);
        imagedestroy($other);
        $db=PearDatabase::getInstance();
        $sql = 'INSERT INTO `vtiger_newinvoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
        $db->pquery($sql,array($recordId,$newimagepath,$newrecordid,'Invoice',$datetime,$current_user->id));
        if ($db->getLastInsertID()<1) {
            //???????????????????????????????????????
            unlink($root_directory.$newimagepath);
        }
        $userid=$request->get("id");
        $sql="UPDATE vtiger_newinvoice SET havasigned=1,receiptorid=?,receiptordate=? WHERE invoiceid=?";
        $db->pquery($sql,array($userid,$datetime,$recordId));
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
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
     * ??????????????????
     * @param Vtiger_Request $request
     */
    public function relatebilling(Vtiger_Request $request){
        $recordid=$request->get('record');
        $db=PearDatabase::getInstance();
        $sql="UPDATE vtiger_newinvoice,
                 vtiger_billing
                SET vtiger_newinvoice.taxpayers_no = vtiger_billing.taxpayers_no,
                 vtiger_newinvoice.registeraddress = vtiger_billing.registeraddress,
                 vtiger_newinvoice.depositbank = vtiger_billing.depositbank,
                 vtiger_newinvoice.telephone = vtiger_billing.telephone,
                 vtiger_newinvoice.accountnumber = vtiger_billing.accountnumber
                WHERE vtiger_newinvoice.billingid=vtiger_billing.billingid AND vtiger_newinvoice.invoiceid=?";
        $db->pquery($sql,array($recordid));
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    /**
     *  ??????????????????
     */
    public function addRedInvoice(Vtiger_Request $request){
        $datasave=$request->get('savedata');
        $recordId = $request->get('record');//?????????id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Invoice');
        $db=PearDatabase::getInstance();
        $array=array('negativeinvoiceextendid','invoiceextendid','negativedrawerextend','negativebillingtimerextend','negativeinvoicecodeextend','negativeinvoice_noextend','negativebusinessnamesextend','negativetaxrateextend','negativecommoditynameextend','negativetotalandtaxextend','negativeremarkextend','negativeamountofmoneyextend','negativetaxextend');
        if(empty($datasave)){
            $data='??????????????????';
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
            exit;
        }
        $temparr=array('negativetotalandtaxextend','negativetaxextend','negativeamountofmoneyextend');
        $insertname='';
        $insertvalue='';
        foreach($datasave as $value){
            if(in_array($value['name'],$array)){
                $insertname.='`'.$value['name'].'`,';
                if(in_array($value['name'],$temparr)){
                    $insertvalue.="'-".$value['value']."',";
                }else{
                    $insertvalue.="'".$value['value']."',";
                }
                if($value['name']=='invoiceextendid'){
                    $invoiceextendid=$value['value'];
                }
            }
        }

        global $current_user;
        $insertname.='`negativedrawerextend`';
        $insertvalue.=$current_user->id;

        if(!Invoice_Record_Model::exportGroupri()||!Users_Privileges_Model::isPermitted('Invoice', 'NegativeEdit', $recordId)||$recordModel->entity->column_fields['modulestatus']!='c_complete' || !Invoice_Record_Model::checkNegativeInvoice(array($recordId,$invoiceextendid,1))){
            $data='???????????????';
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
            exit;
        }
        $sql="INSERT INTO vtiger_negativeinvoice({$insertname}) VALUES({$insertvalue})";
        $db->pquery($sql,array());
        $datetime=date('Y-m-d H:i:s');
        $sql="UPDATE vtiger_invoiceextend SET invoicestatus='redinvoice',processstatus=2,operator=?,operatortime=? WHERE invoiceid=? AND invoiceextendid=?";
        $db->pquery($sql,array($current_user->id,$datetime,$recordId,$invoiceextendid));

        //???????????????(???????????????????????????????????????) gaocl add 2018/05/29
        Newinvoice_Record_Model::calcActualtotal($recordId, true);

        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);

        $response->emit();
        exit;
    }
    public function addCancel(Vtiger_Request $request){
        $invoiceextendid=$request->get('invoiceextendid');
        $recordId = $request->get('record');//?????????id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Newinvoice');
        $db=PearDatabase::getInstance();
        global $current_user;
        do{
            if(!Invoice_Record_Model::exportGroupri()||!Users_Privileges_Model::isPermitted('Invoice', 'ToVoid', $recordId)||$recordModel->entity->column_fields['modulestatus']!='c_complete' || !Invoice_Record_Model::checkNegativeInvoice(array($recordId,$invoiceextendid,1))){
                break;
            }
            $datetime=date('Y-m-d H:i:s');
            $sql="UPDATE vtiger_newinvoiceextend SET invoicestatus='tovoid',processstatus=2,operator=?,operatortime=? WHERE invoiceid=? AND invoiceextendid=?";
            $db->pquery($sql,array($current_user->id,$datetime,$recordId,$invoiceextendid));
        }while(0);

        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
        exit;
    }
    //???????????????????????? ???????????????????????????????????????1?????????????????????
    public function addCancelFlag(Vtiger_Request $request){
        $invoiceextendid=$request->get('invoiceextendid');
        $recordId = $request->get('record');//?????????id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Newinvoice');
        $db=PearDatabase::getInstance();
        global $current_user;
        do{
            if(!Invoice_Record_Model::exportGroupri()||!Users_Privileges_Model::isPermitted('Newinvoice', 'ToVoid', $recordId)||!Users_Privileges_Model::isPermitted('Newinvoice', 'NegativeEdit', $recordId)||$recordModel->entity->column_fields['modulestatus']!='c_complete' || !Invoice_Record_Model::checkNegativeInvoice(array($recordId,$invoiceextendid,1))){
                break;
            }
            $sql="UPDATE vtiger_newinvoiceextend SET invoicestatus='normal',processstatus=0 WHERE invoiceid=? AND invoiceextendid=?";
            $db->pquery($sql,array($recordId,$invoiceextendid));
        }while(0);

        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
        exit;
    }

    /**
     * ????????????
     * @param Vtiger_Request $request
     */
    public function getUserInfo(Vtiger_Request $request){

        $userCode=$request->get("userCode");
        $arr=array("flag"=>false,"msg"=>"???????????????");
        if(!empty($userCode)){
            $userCode=str_pad($userCode,6,0,STR_PAD_LEFT);
            $db=PearDatabase::getInstance();
            $query="SELECT id,last_name FROM vtiger_users WHERE `status`='Active' AND usercode=? limit 1";
            $result=$db->pquery($query,array($userCode));

            if($db->num_rows($result)){
                $data=$db->query_result_rowdata($result);
                $arr=array("flag"=>true,"data"=>$data);
            }

        }
        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
        exit;
    }
    //???????????????
    public function changereceived(Vtiger_Request $request)
    {
        $recordId = $request->get('recordid');
        $userid = $request->get('userid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Newinvoice');
        $entity=$recordModel->entity->column_fields;
        $arr = array();
        //????????????
        if (false &&$entity['modulestatus']=='c_complete' && !empty($userid)) {
            global $current_user;
            $user=Users_Privileges_Model::getInstanceById($entity['assigned_user_id']);
            if($current_user->id==$user->reports_to_id || $current_user->is_admin=='on') {
                $db = PearDatabase::getInstance();
                $datetime=date("Y-m-d H:i:s");
                $id = $db->getUniqueId('vtiger_modtracker_basic');
                $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                    array($id, $recordId, 'Newinvoice', $current_user->id, $datetime, 0));
                $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'assigned_user_id', $entity['assigned_user_id'], $userid));
                $query = "UPDATE vtiger_crmentity SET smownerid=? WHERE crmid=?";
                $db->pquery($query, array($userid,$recordId));
            }

        }

        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();

    }
    //??????????????????,ID
    public function getuserlist(){
        $db=PearDatabase::getInstance();
        $query="SELECT id,CONCAT(vtiger_users.last_name,'[',IFNULL(vtiger_departments.departmentname,'--'),']',IF(vtiger_users.`status`!='Active','[??????]','')) as username FROM  vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid
                            LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE vtiger_users.`status`='Active'";
        $result = $db->pquery($query, array());
        $arr=array();
        while($row= $db->fetchByAssoc($result)){$arr[]=$row;};
        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
    }
    /**
     * ??????????????????
     * @param Vtiger_Request $request
     */
    public function docancel(Vtiger_Request $request){
        $recordid=$request->get('record');
        $voidreason=$request->get('voidreason');
        global $current_user,$adb;
        $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'Newinvoice');
        //$modulestatus=array('c_cancel','c_complete');
        $modulestatus=array('c_cancel');
        if(($current_user->is_admin=='on' || !in_array($recordModel->entity->column_fields['modulestatus'],$modulestatus))){
//        if(($current_user->is_admin=='on' || $recordModel->getModule()->exportGrouprt('Newinvoice','invoiceback'))&&!in_array($recordModel->entity->column_fields['modulestatus'],$modulestatus)){
            //????????????
            $currentTime = date('Y-m-d H:i:s');
            $adb->pquery("UPDATE vtiger_newinvoice SET iscancel=1,modulestatus=?,voidreason=?,voiduserid=?,voiddatetime=? WHERE invoiceid=?",array('c_cancel',$voidreason,$current_user->id,$currentTime,$recordid));
            // ???????????????????????????
            $adb->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE modulename='Newinvoice' AND isaction=1 AND salesorderid=?",array($recordid));
        }
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ???????????????????????????
     * @param Vtiger_Request $request
     */
    public function noNeedToExport(Vtiger_Request $request){
        $recordid=$request->get('record');
        $voidreason=$request->get('voidreason');
        global $current_user,$adb;
        //??????
        $currentTime = date('Y-m-d H:i:s');
        $adb->pquery("UPDATE vtiger_newinvoice SET is_exportable='unable_export',unable_export_reason=? WHERE invoiceid=?",array($voidreason,$recordid));
        //????????????
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $recordid, 'Newinvoice', $current_user->id,$currentTime, 0));
        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?)',
            Array($id, 'is_exportable','able_toexport','unable_export',$id, 'unable_export_reason','???',$voidreason));
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    /**
     * ????????????????????????
     * @param Vtiger_Request $request
     */
    public function needToExport(Vtiger_Request $request){
        $recordid=$request->get('record');
        global $current_user,$adb;
        $oldReason=$adb->pquery("SELECT *  FROM vtiger_newinvoice WHERE invoiceid=? ",array($recordid));
        $oldReason = $adb->query_result_rowdata($oldReason, 0);
        //??????
        $currentTime = date('Y-m-d H:i:s');
        $adb->pquery("UPDATE vtiger_newinvoice SET is_exportable='able_toexport',unable_export_reason='' WHERE invoiceid=?",array($recordid));
        //????????????
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $recordid, 'Newinvoice', $current_user->id,$currentTime, 0));
        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?)',
            Array($id, 'is_exportable','unable_export','able_toexport',$id, 'unable_export_reason',$oldReason['unable_export_reason'],'???'));
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ??????PDF
     * @param Vtiger_Request $request
     *
     */
    public function downloadPdf(Vtiger_Request $request){
        $recordid=$request->get('record');
        global $adb;
        $oldReason=$adb->pquery("SELECT vtiger_newinvoice.*,vtiger_servicecontracts.contract_no,vtiger_account.accountname  FROM vtiger_newinvoice LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_newinvoice.contractid LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_newinvoice.accountid WHERE vtiger_newinvoice.invoiceid=? ",array($recordid));
        $data = $adb->query_result_rowdata($oldReason, 0);
        $amount = $data['taxtotal'] - $data['invoice_fee'];
        $amount = sprintf("%.2f",$amount);

        $service = 'Google Adwords(????????????)';
        if(strpos($data['contract_no'],'GG') || strpos($data['contract_no'],'GOOGLE')){
            $service = 'Google Adwords(????????????)';
        }

        if(strpos($data['contract_no'],'YANDEX')){
            $service = 'Yandex.Direct(yandex??????)';
        }


        require_once("./libraries/tcpdf/tcpdf.php");
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // ???????????????????????????
        if($data['invoicecompany'] == '?????????????????????????????????????????????'){
            $pdf->SetHeaderData('', 30, '', 'KAILILONG INTERNATIONAL HOLDING (HK) LIMITED');
        }else{
            $pdf->SetHeaderData('', 30, '', 'AMERICAN KAILILONG INTERNATIONAL HOLDING (H.K.) LIMITED');
        }

        // ????????????
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetAutoPageBreak(TRUE, 25);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('droidsansfallback', '', 12);

        $pdf->AddPage();
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $tbl_hk =  <<<EOD
<html>
<body>
<table border="0" cellspacing="0" cellpadding="0" width="100%"
	   style="table-layout:fixed;word-wrap:break-word;margin-bottom: 10px;">
	<tr>
		<td></td>
		<td>KAILILONG INTERNATIONAL HOLDING</td>
	</tr>
	<tr>
		<td></td>
		<td>(H.K.) LIMITED</td>
	</tr>
	<tr>
		<td></td>
		<td>Invoice No.:{$data['invoice_num']}</td>
	</tr>
	</table>
	<br>
	<table border="0" cellspacing="0" cellpadding="0" width="100%"
	   style="table-layout:fixed;word-wrap:break-word;margin-bottom: 20px;">
	<tr>
		<td><h1>Invoice</h1></td>
		<td></td>
	</tr>
	<tr>
		<td>Date: {$data['open_invoice_time']}</td>
		<td></td>
	</tr>
	<tr>
		<td>Bill To: {$data['accountname']}</td>
		<td></td>
	</tr>
</table>
<br>
<table border="1" cellspacing="0" cellpadding="5" width="100%" style="table-layout:fixed;word-wrap:break-word;margin-bottom: 20px;">
	<tr>
		<td width="70%" align="center">Description</td>
		<td width="30%" align="center">Amount(USD)</td>
	</tr>
	<tr>
		<td>Advertising campaign consumption: Period ???{$data['invoice_term']}???<br>???{$service}???</td>
		<td>$ {$amount}</td>
	</tr>
	<tr>
		<td>bank fee </td>
		<td>$ {$data['invoice_fee']}</td>
	</tr>
	<tr>
		<td>Actual payment </td>
		<td>$ {$data['taxtotal']}</td>
	</tr>
</table>
<br><br>
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="table-layout:fixed;word-wrap:break-word;margin-bottom: 10px;">
	<tr>
		<td>Note:</td>
	</tr>
	<tr>
		<td>???&nbsp; Please ensure all wire fees are paid by sender of the wire and above the invoiced amount.</td>
	</tr>
	<tr>
		<td></td>
	</tr>
</table>
<hr style="border: 1px solid;"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="table-layout:fixed;word-wrap:break-word;">
	<tr>
		<td>For remittance, please remit to:</td>
	</tr>
	<tr>
		<td>BENEFICIARY???S BANK: CHINA MERCHANTS BANK, H.O. SHENZHEN</td>
	</tr>
	<tr>
		<td>ADD: 7088 SHENNAN BOULEVARD, SHENZHEN</td>
	</tr>
	<tr>
		<td>CHIPS UID: 298375 </td>
	</tr>
	<tr>
		<td>Swift Code: CMBCCNBS</td>
	</tr>
	<tr>
		<td>Company Name:KAILILONG INTERNATIONAL HOLDING (H.K.) LIMITED</td>
	</tr>
	<tr>
		<td>A/C NO.:OSA510904366632301</td>
	</tr>
</table>
<table class="footer" border="0" cellspacing="0" cellpadding="0" width="100%" style="table-layout:fixed;word-wrap:break-word;">	<tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td>PAYMENT IS DUE IMMEDIATELY AFTER RECIEPT OF THIS INVOICE</td>
	</tr>
</table>
</body>
</html>
EOD;
        $tbl_am =  <<<EOD
<html>
<body>
<table border="0" cellspacing="0" cellpadding="0" width="100%"
	   style="table-layout:fixed;word-wrap:break-word;margin-bottom: 10px;">
	<tr>
		<td></td>
		<td>AMERICAN KAILILONG INTERNATIONAL</td>
	</tr>
	<tr>
		<td></td>
		<td>HOLDING (H.K.) LIMITED</td>
	</tr>
	<tr>
		<td></td>
		<td>Invoice No.:{$data['invoice_num']}</td>
	</tr>
	</table>
	<br>
	<table border="0" cellspacing="0" cellpadding="0" width="100%"
	   style="table-layout:fixed;word-wrap:break-word;margin-bottom: 20px;">
	<tr>
		<td><h1>Invoice</h1></td>
		<td></td>
	</tr>
	<tr>
		<td>Date: {$data['open_invoice_time']}</td>
		<td></td>
	</tr>
	<tr>
		<td>Bill To: {$data['accountname']}</td>
		<td></td>
	</tr>
</table>
<br>
<table border="1" cellspacing="0" cellpadding="5" width="100%" style="table-layout:fixed;word-wrap:break-word;margin-bottom: 20px;">
	<tr>
		<td width="70%" align="center">Description</td>
		<td width="30%" align="center">Amount(USD)</td>
	</tr>
	<tr>
		<td>Advertising campaign consumption: Period ???{$data['invoice_term']}???<br>???{$service}???</td>
		<td>$ {$amount}</td>
	</tr>
	<tr>
		<td>bank fee </td>
		<td>$ {$data['invoice_fee']}</td>
	</tr>
	<tr>
		<td>Actual payment </td>
		<td>$ {$data['taxtotal']}</td>
	</tr>
</table>
<br><br>
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="table-layout:fixed;word-wrap:break-word;margin-bottom: 10px;">
	<tr>
		<td>Note:</td>
	</tr>
	<tr>
		<td>???&nbsp; Please ensure all wire fees are paid by sender of the wire and above the invoiced amount.</td>
	</tr>
	<tr>
		<td></td>
	</tr>
</table>
<hr style="border: 1px solid;"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="table-layout:fixed;word-wrap:break-word;">
	<tr>
		<td>For remittance, please remit to:</td>
	</tr>
	<tr>
		<td>Bank Name: China Merchants Bank, Hong Kong Branch</td>
	</tr>
	<tr>
		<td>Bank Address: 21/F, Bank of America Tower,12 Harcourt Road, Central, Hong Kong</td>
	</tr>
	<tr>
		<td>Bank CODE: 238 </td>
	</tr>
	<tr>
		<td>Swift Code: CMBCHKHH</td>
	</tr>
	<tr>
		<td>Company Name:AMERICAN KAILILONG INTERNATIONAL HOLDING (H.K.) LIMITED</td>
	</tr>
	<tr>
		<td>USD Account Number 20550098</td>
	</tr>
</table>
<table class="footer" border="0" cellspacing="0" cellpadding="0" width="100%" style="table-layout:fixed;word-wrap:break-word;">	<tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td>PAYMENT IS DUE IMMEDIATELY AFTER RECIEPT OF THIS INVOICE</td>
	</tr>
</table>
</body>
</html>
EOD;
        if($data['invoicecompany'] == '?????????????????????????????????????????????'){
            $pdf->writeHTML($tbl_hk, true, false, false, false, '');
        }else{
            $pdf->writeHTML($tbl_am, true, false, false, false, '');
        }

        ob_clean();
        $upload_file_path = decideFilePath();
        $path = $upload_file_path . $data['invoiceid'].'_'.time().'.pdf';
        $pdf->Output($path, 'F');

        $data=['code'=>0,'data'=>$path];
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ???????????????????????????
     * @param $recordId
     */
    public function makeWorkflowStagesByOrder($recordId){
        global $current_user;
        $detailModel=Vtiger_DetailView_Model::getInstance('Newinvoice',$recordId);
        $recordModel=$detailModel->getRecord();
        $ncolumn_fields=$recordModel->entity->column_fields;
        $data=array("falg"=>false);
        do {
            if (!in_array($ncolumn_fields['modulestatus'], array('a_normal', 'a_exception'))
                || $ncolumn_fields['assigned_user_id'] != $current_user->id){
                $data['msg']="???????????????";
                break;
            }
            $db = $recordModel->entity->db;
            $workflowsid='599627';//?????????
            $_REQUEST['workflowsid']=$workflowsid;
            $focus=CRMEntity::getInstance('Newinvoice');
            $focus->makeWorkflows('Newinvoice',$_REQUEST['workflowsid'],$recordId,'edit');
            $query="UPDATE vtiger_salesorderworkflowstages,vtiger_newinvoice set vtiger_salesorderworkflowstages.modulestatus='p_process' WHERE vtiger_newinvoice.invoiceid=vtiger_salesorderworkflowstages.salesorderid AND vtiger_salesorderworkflowstages.salesorderid=?";
            $focus->db->pquery($query,array($recordId));
            $departmentid=$_SESSION['userdepartmentid'];
            $focus->setAudituid('ContractsAuditset',$departmentid,$recordId,$workflowsid);
            //????????? ???????????????????????????????????????
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$recordId,'salesorderworkflowstagesid'=>0));
            $sql = "select workflowstagesname from vtiger_workflowstages where workflowsid=? order by sequence LIMIT 1";
            $sel_result=$focus->db->pquery($sql, array($workflowsid));
            $res_cnt=$db->num_rows($sel_result);
            $workflowsnode='';
            if ($res_cnt > 0) {
                $row = $db->query_result_rowdata($sel_result, 0);
                $workflowsnode = $row['workflowstagesname'];
            }
            $focus->db->pquery("UPDATE `vtiger_newinvoice` SET matchover=1,modulestatus='b_check',workflowsid=?,workflowsnode=? WHERE invoiceid=?", array($workflowsid, $workflowsnode, $recordId));
            $data=array("falg"=>true);

        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ??????????????????????????????
     * @param $request
     */
    public function isSignedWithContractAndStayPayment($servicecontractsid){
        global $adb;
        $sql="select servicecontractsid,contract_no,isstage from vtiger_servicecontracts where modulestatus='c_complete' and  servicecontractsid=".$servicecontractsid;
        $contractArray=$adb->run_query_allrecords($sql);
        if(!$contractArray){
            //??????????????????????????????
            return false;
        }
        $sql="SELECT vtiger_staypayment.staypaymentid FROM vtiger_staypayment 
  LEFT JOIN vtiger_servicecontracts ON vtiger_staypayment.contractid=vtiger_servicecontracts.servicecontractsid 
  LEFT JOIN vtiger_receivedpayments ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid 
 LEFT JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_staypayment.staypaymentid
WHERE vtiger_staypayment.modulestatus !='c_complete' and vtiger_crmentity.deleted=0 and vtiger_receivedpayments.staypaymentid>0 AND  vtiger_staypayment.contractid=".$servicecontractsid;
        $stayPayArray=$adb->run_query_allrecords($sql);
        if($stayPayArray){
            //????????????????????????c_complete?????????
            return false;
        }
        return true;
    }

    /**
     * ?????????????????????
     * @param Vtiger_Request $request
     */
    public function makeWorkflowStages(Vtiger_Request $request){
        $recordId=$request->get('recordid');
        $detailModel=Vtiger_DetailView_Model::getInstance('Newinvoice',$recordId);
        global $current_user;
        $recordModel=$detailModel->getRecord();
        $ncolumn_fields=$recordModel->entity->column_fields;
        $request->set('contractid',$ncolumn_fields['contractid']);
        if($ncolumn_fields['billingsourcedata']=='ordersource'){
            //???????????????????????????????????????
            $this->makeWorkflowStagesByOrder($recordId);
            exit();
        }
        $request->set("servicecontractsid", $ncolumn_fields['contractid']);
        if($ncolumn_fields['invoicetype']=='c_normal'){
            $tyunWebRecordModel = TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
            $data = $tyunWebRecordModel->getAllowInvoiceTotal($request);
            if (!$data['success'] || $data['success'] && $data['allowTotal'] <= 0) {
                $response = new Vtiger_Response();
                $data = array("falg" => false, 'msg' => '??????????????????????????????????????????!!');
                $response->setResult($data);
                $response->emit();
                exit;
            }
        }

        //???????????????????????? ??????????????????
        if(!$recordModel->canSubmitVerifyInvoice($ncolumn_fields['contractid'])){
            $response = new Vtiger_Response();
            $data = array("falg" => false, 'msg' => '???????????????????????????????????????????????????????????????!!');
            $response->setResult($data);
            $response->emit();
            exit;
        }

        //5.65???????????? ???????????????????????????,??????????????????????????????
        if (!$this->isSignedWithContractAndStayPayment($ncolumn_fields['contractid']) && $ncolumn_fields['invoicetype'] == 'c_normal') {
            $response = new Vtiger_Response();
            $data = array("falg" => false, 'msg' => '????????????????????????????????????????????????????????????????????????????????????!!');
            $response->setResult($data);
            $response->emit();
            exit;
        }

        $preInvoiceFlag=in_array($ncolumn_fields['invoicecompany'],array('?????????????????????????????????????????????','?????????????????????????????????????????????','???????????????????????????????????????????????????','????????????????????????????????????????????????'));
        $data=array("falg"=>false);
        do {
            if (!in_array($ncolumn_fields['modulestatus'], array('a_normal', 'a_exception'))
                || $ncolumn_fields['assigned_user_id'] != $current_user->id){
                $data['msg']="???????????????";
                break;
            }

            $db = $recordModel->entity->db;
            //???????????????????????????????????????????????????
            $sql = "select b.signaturetype,b.modulestatus,a.invoicetype from vtiger_newinvoice a left join vtiger_servicecontracts b on a.contractid=b.servicecontractsid where a.invoiceid=?";
            $result = $db->pquery($sql, array($recordId));
            $contractRow=$db->fetchByAssoc($result,0);
            if($contractRow['modulestatus']!='c_complete' && $contractRow['signaturetype']=='eleccontract' && $contractRow['invoicetype']!='c_normal'){
                $data['msg']="???????????????????????????????????????????????????";
                break;
            }

            if ($ncolumn_fields['invoicetype'] == 'c_normal') {
                $query="SELECT sum(invoicetotal) AS invoicetotal FROM vtiger_newinvoicerayment WHERE deleted=0 AND invoiceid=?";
                $invoiceTotalResult=$db->pquery($query,array($recordId));
                $query="SELECT sum(vtiger_receivedpayments.allowinvoicetotal) AS allowinvoicetotal FROM vtiger_receivedpayments LEFT JOIN vtiger_newinvoicerayment ON vtiger_newinvoicerayment.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid WHERE  vtiger_newinvoicerayment.deleted=0 AND vtiger_newinvoicerayment.invoiceid=?";
                $allowInvoiceTotalResult=$db->pquery($query,array($recordId));
                $invoiceTotalData = $db->query_result_rowdata($invoiceTotalResult, 0);
                $allowInvoiceTotalData = $db->query_result_rowdata($allowInvoiceTotalResult, 0);
                if($allowInvoiceTotalData['allowinvoicetotal']<=0 || ($allowInvoiceTotalData['allowinvoicetotal']-$invoiceTotalData['invoicetotal'])<0){
                    $data['msg']="?????????????????????????????????!";
                    break;
                }
                //???????????????????????????????????????
                $sql="select distinct receivedpaymentsid from vtiger_newinvoicerayment WHERE deleted=0 AND invoiceid=".$recordId;
                $invoicePaymentArray=array_column($db->run_query_allrecords($sql),'receivedpaymentsid');
                $sql="select receivedpaymentsid from vtiger_receivedpayments where relatetoid=0 and  receivedpaymentsid in (".implode(',',$invoicePaymentArray).")";
                $invoicePaymentArray=$db->run_query_allrecords($sql);
                if($invoicePaymentArray){
                    //??????????????????
                    $data['msg']="????????????????????????!";
                    break;
                }
            }



            $newinvoiceWordflows=array('1'=>'599627', '2'=>'599631', '3'=>'599639','4'=>778075,'5'=>'2690719'); //?????????
            if($ncolumn_fields['modulename']=='ServiceContracts'){
                $accountRecordModel=Vtiger_Record_Model::getInstanceById($recordModel->entity->column_fields['account_id'],"Accounts");
                $accountcolumn_fields=$accountRecordModel->entity->column_fields;
                $accountname=$accountcolumn_fields['accountname'];
            }else{
                $accountRecordModel=Vtiger_Record_Model::getInstanceById($recordModel->entity->column_fields['account_id'],"Vendors");
                $accountcolumn_fields=$accountRecordModel->entity->column_fields;
                $accountname=$accountcolumn_fields['vendorname'];
            }
            $accountname=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|???|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\??|\??????|\???|\??????|\???|\???|\???|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\{|\???|\}|\???|\???|\???|\???|\???|\???|\#|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\??|\???|\???|\???|\???|\???|\???|\??????|\???|\???|\??|\???|\???|\???|\??????|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\||\??|\~|\???|\@|\#|\???|\%|\??????|\???|\&|\*|\???|\???|\-|\??????|\=|\+/u','',$accountname);
            $accountname=strtoupper($accountname);
            $businessnamesone=$ncolumn_fields['businessnamesone'];
            $businessnamesone=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|???|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\??|\??????|\???|\??????|\???|\???|\???|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\{|\???|\}|\???|\???|\???|\???|\???|\???|\#|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\??|\???|\???|\???|\???|\???|\???|\??????|\???|\???|\??|\???|\???|\???|\??????|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\???|\||\??|\~|\???|\@|\#|\???|\%|\??????|\???|\&|\*|\???|\???|\-|\??????|\=|\+/u','',$businessnamesone);
            $businessnamesone=strtoupper($businessnamesone);
            $matchover='';
            if ($ncolumn_fields['invoicetype']=='c_normal'){
                if ($accountname==$businessnamesone) {
                    $workflowsid=$newinvoiceWordflows['1'];
                } else {
                    $workflowsid=$newinvoiceWordflows['2'];
                }
                $matchover='matchover=1,matchtimeover=0,';
            } else {
                if ($accountname==$businessnamesone) {
                    if($preInvoiceFlag){
                        $workflowsid=$newinvoiceWordflows['5'];
                    }else{
                        $workflowsid=$newinvoiceWordflows['3'];
                    }
                } else {
                    $workflowsid=$newinvoiceWordflows['4'];
                }
            }
            $_REQUEST['workflowsid']=$workflowsid;
            $focus=CRMEntity::getInstance('Newinvoice');
            $focus->makeWorkflows('Newinvoice',$_REQUEST['workflowsid'],$recordId,'edit');
            // 2019-08-20 cxh start
            $query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_newinvoice
				SET vtiger_salesorderworkflowstages.accountid=vtiger_newinvoice.accountid, vtiger_salesorderworkflowstages.salesorder_nono = vtiger_newinvoice.invoiceno,
				  vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_newinvoice.accountid)
				WHERE vtiger_newinvoice.invoiceid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?";
            $focus->db->pquery($query,array($recordId));
            //????????????????????????????????????
            $departmentid=$_SESSION['userdepartmentid'];
            if($preInvoiceFlag&&$ncolumn_fields['invoicetype']!='c_normal'&&$accountname==$businessnamesone){
                $focus->setAudituid('PreInvoiceAuditSetting',$departmentid,$recordId,$workflowsid);
            }else{
                $focus->setAudituid('ContractsAuditset',$departmentid,$recordId,$workflowsid);
            }
            //????????? ???????????????????????????????????????
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$recordId,'salesorderworkflowstagesid'=>0));
            // 2019-08-20 cxh end
            $sql = "select workflowstagesname from vtiger_workflowstages where workflowsid=? order by sequence LIMIT 1";
            $sel_result=$focus->db->pquery($sql, array($workflowsid));
            $res_cnt=$db->num_rows($sel_result);
            $workflowsnode='';
            if ($res_cnt > 0) {
                $row = $db->query_result_rowdata($sel_result, 0);
                $workflowsnode = $row['workflowstagesname'];
            }
            $focus->db->pquery("UPDATE `vtiger_newinvoice` SET {$matchover}modulestatus='b_check',workflowsid=?,workflowsnode=? WHERE invoiceid=?", array($workflowsid, $workflowsnode, $recordId));
            if ($ncolumn_fields['invoicetype'] == 'c_normal') {
                $query="SELECT receivedpaymentsid,invoicetotal FROM vtiger_newinvoicerayment WHERE deleted=0 AND invoiceid=?";
                $receResult=$db->pquery($query,array($recordId));
                while($row=$db->fetch_array($receResult)){
                    Newinvoice_Record_Model::setAllowinvoicetotalLog($row['receivedpaymentsid'], -$row['invoicetotal'], '???????????????????????????'. ' ????????????:'.$ncolumn_fields['invoiceno'].'???');
                    $sql = " UPDATE vtiger_receivedpayments SET allowinvoicetotal=if(allowinvoicetotal-{$row['invoicetotal']}<0,0,allowinvoicetotal-{$row['invoicetotal']}) WHERE receivedpaymentsid=? ";
                    $db->pquery($sql, array($row['receivedpaymentsid']));
                }
                Newinvoice_Record_Model::calcTaxtotal($recordId, true);
            }
            //?????????????????????????????????????????????????????? gaocl del 2018/05/31
            //Newinvoice_Record_Model::calcActualtotal($recordId, true);
            $data=array("falg"=>true);
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ???????????????????????????????????????
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function getChangeServiceContracts(Vtiger_Request $request) {
        global $adb,$current_user;
        $recordId = $request->get('record');  // ??????id

        //??????????????????????????????
        //???????????????=0
        //???????????????????????????
        //????????????????????????????????????????????????

        $sql="SELECT vtiger_servicecontracts.contract_no
                    FROM vtiger_servicecontracts 
                    INNER JOIN vtiger_crmentity ON(vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid)
                    WHERE vtiger_crmentity.deleted =0 
                    AND vtiger_servicecontracts.modulestatus = 'c_complete' 
                    AND NOT EXISTS(SELECT 1 FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.deleted=0 AND vtiger_newinvoicerayment.allowinvoicetotal>0 AND vtiger_newinvoicerayment.invoiceid=?)
                    AND EXISTS (
                                SELECT 1 FROM vtiger_newinvoice
                                INNER JOIN vtiger_crmentity ON(vtiger_newinvoice.invoiceid=vtiger_crmentity.crmid)
                                WHERE vtiger_crmentity.deleted=0 
                                 AND vtiger_newinvoice.invoicetype='c_billing' 
                                AND vtiger_newinvoice.accountid=vtiger_servicecontracts.sc_related_to
                                AND vtiger_newinvoice.invoicecompany=vtiger_servicecontracts.invoicecompany
                                AND vtiger_newinvoice.contractid != vtiger_servicecontracts.servicecontractsid
                                AND vtiger_newinvoice.invoiceid=?)";

        $sel_result = $adb->pquery($sql, array($recordId,$recordId));
        $res_cnt = $adb->num_rows($sel_result);

        $contract_no_arr = array();
        if($res_cnt > 0) {
            while($rawData = $adb->fetch_array($sel_result)) {
                if (!empty($rawData['contract_no'])) {
                    $contract_no_arr[]['contract_no'] = $rawData['contract_no'];
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setResult(array('arr_contract_no'=>$contract_no_arr));
        $response->emit();
    }
    public function saveImage(Vtiger_Request $request){
        $imgstring=$request->get('image');
        $newrecordid=base64_encode(uniqid());
        global $root_directory,$current_user;

        $invoiceimagepath = $invoiceimagepath='/storage/invoice/';
        $imagepath=$invoiceimagepath.date('Y').'/'.date('F').'/'.date('d').'/';
        //????????????????????????????????????
        is_dir($root_directory.$imagepath) || mkdir($root_directory.$imagepath,0777,true);
        //???????????????????????????
        $newimagepath= $imagepath.$newrecordid.'.png';
        //??????????????????????????????
        $img=imagecreatefromstring(base64_decode(str_replace('data:image/png;base64,','',$imgstring)));
        //????????????????????????
        $invoiceimagewidth=imagesx($img);
        $invoiceimageheight=imagesy($img);
        //????????????????????????
        $textcolor = imagecolorallocate($img, 255, 0, 0);
        //$img????????????????????????????????????????????????????????????????????????????????????????????????$img??????a?????????
        $other=imagecreatetruecolor($invoiceimagewidth,$invoiceimageheight);
        $white=imagecolorallocate($img, 255, 255, 255);
        //$other ???????????????
        imagefill($other,0,0,$white);
        $datetime=date('Y-m-d H:i');
        //???????????????$img???
        imagestring($img,5,$invoiceimagewidth-200,$invoiceimageheight-60,$datetime,$textcolor);
        //????????????
        imagecopy($other,$img,0,0,0,0,$invoiceimagewidth,$invoiceimageheight);
        //????????????
        imagepng($other,$root_directory.$newimagepath);
        //????????????
        imagedestroy($img);
        imagedestroy($other);
        return array('newimagepath'=>$newimagepath,'newrecordid'=>$newrecordid);

    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * ?????????????????????
     */
    public function savesignimages(Vtiger_Request $request){
        global $current_user;
        $recordIds = $request->get('records');//?????????id
        $recordIdarray=explode(',',$recordIds);
        $db=PearDatabase::getInstance();
        $sql1 = 'INSERT INTO `vtiger_newinvoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
        $sql2="UPDATE vtiger_newinvoice SET havasigned=1,receiptorid=?,receiptordate=? WHERE invoiceid=?";
        $userid=$request->get("id");
        $imageinfo=$this->saveImage($request);
        $newimagepath=$imageinfo['newimagepath'];
        $newrecordid=$imageinfo['newrecordid'];
        $datetime=date('Y-m-d H:i:s');
        foreach($recordIdarray as $recordId){
            $db->pquery($sql1,array($recordId,$newimagepath,$newrecordid,'NewInvoice',$datetime,$current_user->id));
            $db->pquery($sql2,array($userid,$datetime,$recordId));
            $sql="UPDATE vtiger_newinvoice SET receiveid=?,receivedate=?,modulestatus='c_complete',workflowsnode='?????????',workflowstime=? WHERE invoiceid=?";
            $db->pquery($sql,array($current_user->id,date('Y-m-d H:i:s'),date('Y-m-d H:i:s'),$recordId));
            $sql="UPDATE `vtiger_salesorderworkflowstages`
                    SET `auditorid` = ?,
                     `auditortime` = ?,
                     `schedule` = 100,
                     `isaction` = 2
                    WHERE
                        `modulename` = 'Newinvoice'
                    AND `isaction` = 1
                    AND `salesorderid`=?";
            $db->pquery($sql,array($current_user->id,date('Y-m-d H:i:s'),$recordId));
            $sql="UPDATE `vtiger_salesorderworkflowstages`
                    SET `modulestatus` ='c_complete'
                    WHERE
                        `modulename` = 'Newinvoice'
                    AND `salesorderid`=?";
            $db->pquery($sql,array($recordId));
        }
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function nFillInCancel(Vtiger_Request $request){
        $recordid=$request->get('record');
        global $current_user,$adb;
        $detailViewModel=Vtiger_DetailView_Model::getInstance('Newinvoice',$recordid);
        $recordModel=$detailViewModel->getRecord();
        if(in_array($recordModel->get('modulestatus'),array('a_normal','b_check','b_actioning'))
            && $recordModel->entity->column_fields['assigned_user_id']==$current_user->id
            && $detailViewModel->fillInInvoice($recordid)){
            //????????????
            $currentTime = date('Y-m-d H:i:s');
            $adb->pquery("UPDATE vtiger_newinvoice SET iscancel=1,modulestatus=?,voidreason=?,voiduserid=?,voiddatetime=? WHERE invoiceid=?",array('c_cancel','???????????????',$current_user->id,$currentTime,$recordid));
            if(in_array($recordModel->get('modulestatus'),array('b_check','b_actioning'))){
                $query="SELECT * FROM vtiger_newinvoicerayment WHERE invoiceid=? AND deleted=0";
                $result=$adb->pquery($query,array($recordid));
                $invoiceno=$recordModel->get('invoiceno');
                $datetime=date("Y-m-d H:i:s");
                if($adb->num_rows($result)){
                    while($row=$adb->fetch_array($result)){
                        $surpluinvoicetotal=$row['surpluinvoicetotal'];
                        $receivedpaymentsid=$row['receivedpaymentsid'];
                        $rapmentRecordModel=Vtiger_Record_Model::getInstanceById($receivedpaymentsid,'ReceivedPayments',true);
                        $allowinvoicetotal=$rapmentRecordModel->get('allowinvoicetotal');
                        $unit_price=$rapmentRecordModel->get('unit_price');
                        $temp=bcadd($allowinvoicetotal,$surpluinvoicetotal,2);
                        if(bccomp($unit_price,$temp,2)<0){
                            $temp=$unit_price;
                        }
                        $raymentSql='UPDATE vtiger_receivedpayments SET allowinvoicetotal=? WHERE receivedpaymentsid=?';
                        $adb->pquery($raymentSql,array($temp,$receivedpaymentsid));

                        $id = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                            array($id, $receivedpaymentsid, 'ReceivedPayments', $current_user->id, $datetime, 0));
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                            Array($id, 'allowinvoicetotal', $allowinvoicetotal, $temp."???????????? ????????????:{$invoiceno}"));
                    }
                }
            }
            $adb->pquery("UPDATE vtiger_newinvoicerayment SET deleted=1,modifiedby=?,modifiedtime=? WHERE invoiceid=?",array($current_user->id,$datetime,$recordid));
            // ???????????????????????????
            $adb->pquery("DELETE  FROM  vtiger_salesorderworkflowstages WHERE modulename='Newinvoice' AND salesorderid=?",array($recordid));
            //?????????????????????????????????
            Newinvoice_Record_Model::updateInvoiceWithOutPayment($recordid);
        }
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ?????????????????????
     * @param Vtiger_Request $request
     */
    public function addPreInvoiceAudit(Vtiger_Request $request){
        $auditsettingtype = $request->get("auditsettingtype");
        $department = $request->get("department");
        $oneaudituid = $request->get("oneaudituid");
        $towaudituid = $request->get("towaudituid");
        $threeaudituid = $request->get("threeaudituid");
        $audituid4 = $request->get("audituid4");
        $audituid5 = $request->get("audituid5");
        $data = array('flag'=>'0', 'msg'=>'????????????');
        do {
            $moduleModel = Vtiger_Module_Model::getInstance('Newinvoice');
            if(!$moduleModel->exportGrouprt('Newinvoice','pre_invoice_audit')){   //????????????
                break;
            }
            if (empty($auditsettingtype)) {
                break;
            }
            if (empty($department)) {
                break;
            }
            if (empty($oneaudituid)) {
                break;
            }
            if (empty($towaudituid)) {
                break;
            }
            /*if (empty($threeaudituid)) {
            	//break;
                $threeaudituid='';
            }*/
            $sql = "delete from vtiger_auditsettings where auditsettingtype=? AND department=? AND oneaudituid=? AND towaudituid=?";
            $sql2 = "INSERT INTO `vtiger_auditsettings` (`auditsettingsid`, `auditsettingtype`, `department`, `oneaudituid`, `towaudituid`,audituid3,audituid4,audituid5, `createtime`, `createid`) VALUES (NULL, ?, ?,?, ?, ?, ?, ?,?,?)";
            global $current_user;
            $db=PearDatabase::getInstance();
            $db->pquery($sql, array($auditsettingtype, $department, $oneaudituid, $towaudituid));
            $db->pquery($sql2, array($auditsettingtype, $department, $oneaudituid, $towaudituid,$threeaudituid,$audituid4,$audituid5, date('Y-m-d H:i:s'), $current_user->id));
            $data = array('flag'=>'1', 'msg'=>'????????????');
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }


    /**
     * ??????
     * @param Vtiger_Request $request
     */
    public function delPreInvoiceAudit(Vtiger_Request $request){
        $moduleModel = Vtiger_Module_Model::getInstance('Newinvoice');
        if($moduleModel->exportGrouprt('Newinvoice','pre_invoice_audit')){   //????????????
            global $current_user;
            $id=$request->get("id");
            $delsql="delete from vtiger_auditsettings where auditsettingsid=?";
            $db=PearDatabase::getInstance();
            $datetime=date('Y-m-d H:i:s');
            $db->pquery($delsql,array($id));
        }
        $data='????????????';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ?????????????????????
     * @param Vtiger_Request $request
     */
    public function addPreInvoiceRemind(Vtiger_Request $request){
        $remindtype = $request->get("remindtype");
        $department = $request->get("department");
        $days = $request->get("days");
        $over_days = $request->get("over_days");
        $data = array('flag'=>'0', 'msg'=>'????????????');
        $moduleModel = Vtiger_Module_Model::getInstance('Newinvoice');
        if($moduleModel->exportGrouprt('Newinvoice','pre_invoice_remind')&&$remindtype&&$department&&$days&&$over_days){   //????????????
            //???????????????
            global $current_user;
            $sql = "delete from  vtiger_newinvoiceremind where remindtype=? AND department=? AND days=? and over_days =?";
            $sql2 = "INSERT INTO `vtiger_newinvoiceremind` (`remindtype`, `department`, `days`,`createtime`, `createid`,`over_days`) VALUES (?,?,?,?,?,?)";
            $db=PearDatabase::getInstance();
            $db->pquery($sql, array($remindtype, $department, $days,$over_days));
            $db->pquery($sql2, array($remindtype, $department, $days,  date('Y-m-d H:i:s'), $current_user->id,$over_days));
            $data = array('flag'=>'1', 'msg'=>'????????????');
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ??????
     * @param Vtiger_Request $request
     */
    public function delPreInvoiceRemind(Vtiger_Request $request){
        $moduleModel = Vtiger_Module_Model::getInstance('Newinvoice');
        if($moduleModel->exportGrouprt('Newinvoice','pre_invoice_remind')){   //????????????
            $id=$request->get("id");
            $delsql="delete from vtiger_newinvoiceremind where remindid=?";
            $db=PearDatabase::getInstance();
            $db->pquery($delsql,array($id));
        }
        $data='????????????';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ??????????????????
     * @param Vtiger_Request $request
     */
    public function addNewInvoiceDelay(Vtiger_Request $request){
        $auditsettingtype = $request->get("auditsettingtype");
        $department = $request->get("department");
        $oneaudituid = $request->get("oneaudituid");
        $data = array('flag'=>'0', 'msg'=>'????????????');
        do {
            $moduleModel = Vtiger_Module_Model::getInstance('Newinvoice');
            if(!$moduleModel->exportGrouprt('Newinvoice','pre_invoice_delay')){   //????????????
                break;
            }
            if (empty($auditsettingtype)) {
                break;
            }
            if (empty($department)) {
                break;
            }
            if (empty($oneaudituid)) {
                break;
            }
            $sql = "delete from vtiger_auditsettings where auditsettingtype=? AND department=? AND oneaudituid=?";
            $sql2 = "INSERT INTO `vtiger_auditsettings` (`auditsettingtype`, `department`, `oneaudituid`,  `createtime`, `createid`) VALUES (?, ?,?, ?,?)";
            global $current_user;
            $db=PearDatabase::getInstance();
            $db->pquery($sql, array($auditsettingtype, $department, $oneaudituid));
            $db->pquery($sql2, array($auditsettingtype, $department, $oneaudituid,  date('Y-m-d H:i:s'), $current_user->id));
            $data = array('flag'=>'1', 'msg'=>'????????????');
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ??????????????????????????????
     * @param Vtiger_Request $request
     */
    public function updateNewInvoiceDelay(Vtiger_Request $request){
        $moduleModel = Vtiger_Module_Model::getInstance('Newinvoice');
        if($moduleModel->exportGrouprt('Newinvoice','pre_invoice_delay')){   //????????????
            global $current_user;
            $id=$request->get("id");
            $delsql="delete from vtiger_auditsettings where auditsettingsid=?";
            $db=PearDatabase::getInstance();
            $datetime=date('Y-m-d H:i:s');
            $db->pquery($delsql,array($id));
        }
        $data='????????????';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ??????????????????list?????????list
     * @param Vtiger_Request $request
     */
    public function getFinanceList(Vtiger_Request $request){
        $recordId=$request->get('record');
        $type=$request->get('type');
        $billingsourcedata=$request->get('billingsourcedata');
        if($type&&$type=='redInvoice'){
            //??????????????????
            $data['invoice']=Newinvoice_Record_Model::getMoreinvoice($recordId);
        }else{
            //??????????????????
            $data['invoice']=Newinvoice_Record_Model::getMoreinvoiceWithStatus($recordId);
        }
        //??????????????????????????????
        $flag=Newinvoice_Record_Model::isClearVoidOrRed($recordId);
        if($flag){
            $data['msg']='???????????????????????????????????????????????????';
        }else{
            if($billingsourcedata=='ordersource'){
                //?????????????????????
                $data['order']=Newinvoice_Record_Model::getDongchaliListWithDeleted($recordId);
            }else{
                $data['payment']=Newinvoice_Record_Model::getNewinvoicerayment($recordId);
                !$data['payment']&&$data['msg']='???????????????';
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }



    /**
     * ??????????????????
     * @param Vtiger_Request $request
     */
    public function abandonedWorkFlow(Vtiger_Request $request){
        global $current_user;
        $data=array("flag"=>false);
        $recordId=$request->get('record');
        $extendIds=$request->get('extendIds');
        $newInvoicePaymentIds=$request->get('newInvoicePaymentIds');
        $newInvoicePayment=$request->get('newInvoicePayment');
        $billingsourcedata=$request->get('billingsourcedata');

        $detailModel=Vtiger_DetailView_Model::getInstance('Newinvoice',$recordId);
        $recordModel=$detailModel->getRecord();
        $ncolumn_fields=$recordModel->entity->column_fields;
        $db = $recordModel->entity->db;
        $type=$request->get('type');
        if($ncolumn_fields['modulestatus']!='c_complete'){
            $data['msg']="???????????????";
        }else{
            //??????
            $focus=CRMEntity::getInstance('Newinvoice');
            $workflowsid=$focus->abandonedWorkFlowId;//??????id
            //?????????????????????
            $deleteSql="delete from vtiger_salesorderworkflowstages where workflowsid=? and salesorderid=?";
            $db->pquery($deleteSql,array($workflowsid,$recordId));
            //????????????
            $focus->makeWorkflows('Newinvoice',$workflowsid,$recordId);
            if($type&&$type=='redInvoice'){
                //?????????????????????????????????
                $deleteSql="delete from vtiger_salesorderworkflowstages where workflowsid=? and salesorderid=? and workflowstagesflag=?";
                $db->pquery($deleteSql,array($workflowsid,$recordId,'APPLICATION_VOID'));
                //?????????????????????
                $updateSql="UPDATE vtiger_salesorderworkflowstages SET schedule=?,isaction=? WHERE workflowsid=? and salesorderid=? and workflowstagesflag=?";
                $db->pquery($updateSql,array(100,2,$workflowsid,$recordId,'APPLICATION_RED'));
                $workflowsnode='????????????';
            }else{
                //?????????????????????????????????
                $deleteSql="delete from vtiger_salesorderworkflowstages where workflowsid=? and salesorderid=? and workflowstagesflag=?";
                $db->pquery($deleteSql,array($workflowsid,$recordId,'APPLICATION_RED'));
                //?????????????????????
                $updateSql="UPDATE vtiger_salesorderworkflowstages SET schedule=?,isaction=? WHERE workflowsid=? and salesorderid=? and workflowstagesflag=?";
                $db->pquery($updateSql,array(100,2,$workflowsid,$recordId,'APPLICATION_VOID'));
                $workflowsnode='????????????';
            }
            $updateSql="UPDATE vtiger_salesorderworkflowstages SET isaction=? WHERE workflowsid=? and salesorderid=? and workflowstagesflag=?";
            $db->pquery($updateSql,array(1,$workflowsid,$recordId,'INVOICE_ADMIN_THROUGH'));
            //????????????????????????
            $updateSql="UPDATE vtiger_newinvoice SET modulestatus='b_check',workflowsid=?,workflowsnode=? WHERE invoiceid=?";
            $db->pquery($updateSql,array($workflowsid,$workflowsnode,$recordId));
            //????????????????????????
            foreach ($extendIds as $extendId){
                $extendSql="UPDATE vtiger_newinvoiceextend SET processstatus=1,invoicestatus='normal' where invoiceextendid=?";
                $db->pquery($extendSql,array($extendId));
            }
            //???????????????????????????
            foreach ($newInvoicePaymentIds as $key => $newInvoicePaymentId){
                if($billingsourcedata=='ordersource'){
                    $paymentSql="UPDATE vtiger_dongchaliorder SET voidmoney=".$newInvoicePayment[$key]." where dongchaliorderid=?";
                }else{
                    $paymentSql="UPDATE vtiger_newinvoicerayment SET voidorredtotal=".$newInvoicePayment[$key]." where newinvoiceraymentid=?";
                }
                $db->pquery($paymentSql,array($newInvoicePaymentId));
            }
            $data['flag']=true;
            $data['msg']="??????????????????";
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     *????????????????????????
     */
    public function saveRedInvoiceInfo(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $saveInfo=$request->get('saveInfo');
        $invoiceId=$request->get('extendId');
        $sql="update vtiger_newinvoiceextend set negativeinfo=? where invoiceextendid=?";
        $db->pquery($sql,array(json_encode($saveInfo[0]),$invoiceId));
        $response = new Vtiger_Response();
        $data['flag']=true;
        $data['msg']="????????????";
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ??????????????????????????????????????????
     * @param Vtiger_Request $request
     */
    public function getSystemUserOrder(Vtiger_Request $request){
        global $testtyunweburl;
        $sault='multiModuleProjectDirectoryasdafdgfdhggijfgfdsadfggiytudstlllkjkgff';
        $time=time().'123';
        $token=md5($time.$sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $postData = array(
            "userID"=>$request->get('systemuser_id'),
            'pageIndex'=>0,
            'pageSize'=>20,
            'tradingStatus'=>1
        );
        $url =$testtyunweburl.'api/Order/GetCanInvoiceOrderPageData';
        $res = json_decode($this->https_request($url, json_encode($postData),$curlset),true);
        $data=array();
        if($res['success']){
            if($res['recordsTotal']){
                $usingOrderArray=$this->getUsedDongcailiOrder($request->get('systemuser_id'));
                foreach ($res['data'] as $key=>$value){
                    if(in_array($value['OrderCode'],$usingOrderArray)){
                        unset($res['data'][$key]);
                    }
                }
                $data['data']=array_merge($res['data']);
            }
        }
        $data['flag']=true;
        $data['msg']="????????????";
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * ???????????????????????????????????????
     * @param $systemuser_id
     */
    public function getUsedDongcailiOrder($systemuser_id){
        global $adb;
        $sql="select distinct ordercode from  vtiger_dongchaliorder where systemuserid='".$systemuser_id."' and isused=1";
        return array_column($adb->run_query_allrecords($sql),'ordercode');
    }

    /**
     *????????????
     * @param Vtiger_Request $request
     */
    public function disassociateDongchaliOrder(Vtiger_Request $request){
        global $adb;
        $record=$request->get('record');
        $dongchaliorderid=$request->get('dongchaliorderid');
        $sql="update vtiger_dongchaliorder set deleted=?,isused=? where invoiceid=? and dongchaliorderid=?";
        $adb->pquery($sql,array(1,0,$record,$dongchaliorderid));
        $response = new Vtiger_Response();
        $response->setResult();
        $response->emit();
    }

    /**
     * ?????????????????????
     * @param $url
     * @param null $data
     * @param array $curlset
     * @return bool|string
     */
    public function https_request($url, $data = null,$curlset=array()){
        $curl = curl_init();
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post????????????
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

}
