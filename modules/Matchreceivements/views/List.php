<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Matchreceivements_List_View extends Vtiger_KList_View {

    function __construct() {
        parent::__construct();
    }

    /**
     * 页面标题
     * @param Vtiger_Request $request
     * @return mixed|string
     */
    function getPageTitle(Vtiger_Request $request)
    {
        $public = $request->get('public');
        if ($public == 'receivedPaymentStatistics') {
            return '回款统计看板';
        }
        return vtranslate($request->getModule(), $request->get('module'));
    }

    function process(Vtiger_Request $request){
    $strPublic = $request->get('public');
    if ($strPublic=='receivedPaymentStatistics') {
        $moduleName = $request->getModule();
        global $current_user;
        $startMonth = date('Y-01');
        $endMonth = date('Y-m');
        $recordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
        $typeList = $recordModel->getReceivedPaymentsRules($current_user->id);
        $viewer = $this->getViewer($request);
        $viewer->assign('typeList', $typeList);
        $viewer->assign('startMonth', $startMonth);
        $viewer->assign('endMonth', $endMonth);
        $viewer->view('ReceivedPaymentStatistics.tpl', $moduleName);
        return;
    }

	$adb =PearDatabase::getInstance();
    $currentUser = Users_Record_Model::getCurrentUserModel();
    $currentid = $currentUser->get('id');
    $dateTime=date('Y-m-d');
	/*$sql1 = "SELECT
	  vtiger_receivedpayments.*,label,sc_related_to,servicecontractsid,contract_no
      FROM
	vtiger_receivedpayments
    LEFT JOIN vtiger_servicecontracts ON maybe_account = sc_related_to
    LEFT JOIN vtiger_crmentity ON maybe_account = crmid
    WHERE
	  relatetoid = '' AND  receiveid = ? AND maybe_account !='' AND receivedpaymentsid NOT IN (SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ?)";*/

    // 2016-8-30 回款匹配的客户的负责人是当前用户 周海   新添加了 sql 中的 OR 后面条件
    // 匹配的是 receiveid=?  是服务合同的提单人
    $sql1 = "SELECT
                vtiger_receivedpayments.*, label,
                sc_related_to,
                servicecontractsid,
                IFNULL((SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid=? and vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=vtiger_servicecontracts.sc_related_to),0) AS shareuser,
                contract_no
            FROM
                vtiger_receivedpayments
            LEFT JOIN vtiger_servicecontracts ON (maybe_account = sc_related_to and vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')) ## 回款可能客户 = 服务合同里面的客户
            LEFT JOIN vtiger_crmentity ON maybe_account = crmid
           LEFT JOIN vtiger_account ON vtiger_receivedpayments.maybe_account = vtiger_account.accountid
            WHERE
                vtiger_receivedpayments.deleted=0 and 
                (
                    (relatetoid = '' or relatetoid=0 or relatetoid is null)
                    AND receivedstatus = 'normal'
                    AND maybe_account != ''
                    and contract_no != ''
                    AND vtiger_account.serviceid = ?     ## ## 客服负责人事当前用户
                    AND servicecontractsid != ''  ## 合同不能为空
                    AND contractstate = '0'       ## 合同的关闭状态为正常
                    and sideagreement=0
                    and unit_price>0
                    AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
                    /*AND vtiger_servicecontracts.effectivetime >='{$dateTime}'*/
                    AND receivedpaymentsid NOT IN (
                        SELECT DISTINCT
                            receivepaymentid
                        FROM
                            `vtiger_receivedpayments_throw`
                        WHERE
                            userid = ?
                        AND vtiger_receivedpayments_throw.deleted=0
                    )
                ) OR
                (
                    (relatetoid = '' or relatetoid=0 or relatetoid is null)
                    AND receivedstatus = 'normal'
                    AND receiveid = ?
                    AND maybe_account != ''
                    AND servicecontractsid != ''
                    and contract_no != ''
                    AND contractstate = '0'       ## 合同的关闭状态为正常
                    and sideagreement=0
                    and unit_price>0
                    AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
                    /*AND vtiger_servicecontracts.effectivetime >='{$dateTime}'*/
                    AND receivedpaymentsid NOT IN (
                        SELECT DISTINCT
                            receivepaymentid
                        FROM
                            `vtiger_receivedpayments_throw`
                        WHERE
                            userid = ?
                        AND vtiger_receivedpayments_throw.deleted=0
                    )
                ) OR
                (
                    (relatetoid = '' or relatetoid=0 or relatetoid is null)
                    AND receivedstatus = 'normal'
                    AND maybe_account != ''
                    AND vtiger_crmentity.smownerid = ?     ## ## 可能客户的负责人是当前用户
                    AND servicecontractsid != ''  ## 合同不能为空
                    and contract_no != ''
                    AND contractstate = '0'       ## 合同的关闭状态为正常
                    and sideagreement=0
                    and unit_price>0
                    AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
                    /*AND vtiger_servicecontracts.effectivetime >='{$dateTime}'*/
                    AND receivedpaymentsid NOT IN (
                        SELECT DISTINCT
                            receivepaymentid
                        FROM
                            `vtiger_receivedpayments_throw`
                        WHERE
                            userid = ?
                        AND vtiger_receivedpayments_throw.deleted=0
                    )
                ) OR
                (
                    (relatetoid = '' or relatetoid=0 or relatetoid is null)
                    AND receivedstatus = 'normal'
                    AND maybe_account != ''
                    AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
                    /*AND vtiger_servicecontracts.effectivetime >='{$dateTime}'*/
                    AND EXISTS(SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid=? and vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=vtiger_servicecontracts.sc_related_to)     ## ## 共享商务
                    AND servicecontractsid != ''  ## 合同不能为空
                    and contract_no != ''
                    AND contractstate = '0'       ## 合同的关闭状态为正常
                    and sideagreement=0
                    and unit_price>0
                    AND receivedpaymentsid NOT IN (
                        SELECT DISTINCT
                            receivepaymentid
                        FROM
                            `vtiger_receivedpayments_throw`
                        WHERE
                            userid = ?
                        AND vtiger_receivedpayments_throw.deleted=0
                    )
                )";

	$result1 = $adb->pquery($sql1,array($currentid, $currentid, $currentid, $currentid, $currentid, $currentid, $currentid,$currentid, $currentid));
    $data = array();
    $ttt_data = array();
    if($adb->num_rows($result1)>0){
        for($i=0;$i<$adb->num_rows($result1);$i++){
            $res = $adb->fetchByAssoc($result1,$i);
            $res['modulename'] = 'ServiceContracts';
            $data[] = $res;
            $ttt_data[] = $res['servicecontractsid'];
        }
    }
    $query="SELECT 
                 vtiger_receivedpayments.*, 
                 (SELECT crmtable.label FROM vtiger_crmentity AS crmtable WHERE crmtable.crmid=vtiger_receivedpayments.accountid) AS label,
                 vtiger_suppliercontracts.vendorid AS sc_related_to,
                 vtiger_suppliercontracts.suppliercontractsid AS servicecontractsid,
                  0 AS shareuser,
                contract_no
                FROM vtiger_receivedpayments
                LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.vendorid=vtiger_receivedpayments.accountid
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid
                WHERE 
                    vtiger_receivedpayments.deleted=0 and 
                    vtiger_receivedpayments.receivedstatus='RebateAmount'
                AND vtiger_receivedpayments.deleted=0
                AND vtiger_receivedpayments.relatetoid=0 
                AND vtiger_receivedpayments.accountid>0
                AND vtiger_crmentity.deleted=0
                AND vtiger_suppliercontracts.effectivetime>='{$dateTime}'
                AND (vtiger_suppliercontracts.signid=? OR vtiger_crmentity.smownerid=?
                OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmaccount WHERE crmaccount.crmid=vtiger_receivedpayments.accountid AND crmaccount.smownerid=?)
                )
                AND receivedpaymentsid NOT IN (
                    SELECT DISTINCT receivepaymentid
                    FROM `vtiger_receivedpayments_throw`
                    WHERE userid = ?
                    AND vtiger_receivedpayments_throw.deleted=0)";
        $result1 = $adb->pquery($query,array($currentid, $currentid, $currentid, $currentid));
        if($adb->num_rows($result1)>0){
            for($i=0;$i<$adb->num_rows($result1);$i++){
                $res = $adb->fetchByAssoc($result1,$i);
                if(!in_array($res['servicecontractsid'], $ttt_data)) {
                    $res['maybe_account'] = $res['accountid'];
                    $res['modulename'] = 'SupplierContracts';
                    $data[] = $res;
                    $ttt_data[] = $res['servicecontractsid'];
                }
            }
        }
        $staypaymentjine = array();
        $query="SELECT vtiger_receivedpayments.*, vtiger_staypayment.staypaymentname,vtiger_staypayment.staypaymentid as staypaymentidid,vtiger_staypayment.staypaymentjine,vtiger_staypayment.staypaymenttype,vtiger_staypayment.payer,
                                vtiger_suppliercontracts.contract_no,
                                vtiger_suppliercontracts.suppliercontractsid AS servicecontractsid
                            FROM
                                vtiger_receivedpayments
                            LEFT JOIN vtiger_staypayment ON  ( (REPLACE(vtiger_staypayment.staypaymentname,' ','') =  REPLACE(vtiger_receivedpayments.paytitle,' ','') and vtiger_staypayment.staypaymentname!='') or (REPLACE(vtiger_staypayment.payer,' ','') like REPLACE(REPLACE(vtiger_receivedpayments.paytitle,' ',''),'*','_')) )
                            LEFT JOIN vtiger_suppliercontracts ON (vtiger_suppliercontracts.suppliercontractsid = vtiger_staypayment.contractid  and vtiger_suppliercontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed'))
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid
                            WHERE
                                vtiger_receivedpayments.deleted=0 and 
                                (vtiger_receivedpayments.relatetoid = '' OR vtiger_receivedpayments.relatetoid = 0) AND vtiger_receivedpayments.receivedstatus = 'RebateAmount'
                            AND (vtiger_suppliercontracts.signid=? OR vtiger_crmentity.smownerid=?
                            OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmcontract WHERE crmcontract.crmid=vtiger_suppliercontracts.suppliercontractsid AND crmcontract.smownerid=?)
                            OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmaccount WHERE crmaccount.crmid=vtiger_receivedpayments.accountid AND crmaccount.smownerid=?)
                            )
                            AND vtiger_crmentity.deleted = 0
                            AND vtiger_suppliercontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
							AND 
                                vtiger_staypayment.overdute IS NOT NULL
							AND receivedpaymentsid NOT IN (
                                SELECT DISTINCT receivepaymentid
                                FROM `vtiger_receivedpayments_throw`
                                WHERE userid = ?
                                AND vtiger_receivedpayments_throw.deleted=0)
                          AND
                              vtiger_staypayment.modulestatus !='a_exception'
                          AND
                              (
                                (vtiger_staypayment.staypaymenttype='nofixation' and vtiger_receivedpayments.reality_date>=vtiger_staypayment.startdate and vtiger_receivedpayments.reality_date<=vtiger_staypayment.enddate) 
                                  or 
                                (vtiger_staypayment.staypaymenttype='fixation' and vtiger_receivedpayments.unit_price<=vtiger_staypayment.surplusmoney and vtiger_staypayment.surplusmoney>0)
                              )
                         AND 
                            (
                                (REPLACE(vtiger_staypayment.payer,' ','') =  REPLACE(vtiger_receivedpayments.paytitle,' ',''))         
                                or 
                                (REPLACE(vtiger_staypayment.payer,' ','') like REPLACE(REPLACE(vtiger_receivedpayments.paytitle,' ',''),'*','_'))
                                or
                                (REPLACE(vtiger_staypayment.staypaymentname,' ','') =  REPLACE(vtiger_receivedpayments.paytitle,' ','')) 
                            )
                         /*AND vtiger_suppliercontracts.effectivetime>='{$dateTime}'*/
";
        $result1 = $adb->pquery($query,array($currentid, $currentid, $currentid,$currentid, $currentid));
        if($adb->num_rows($result1)>0){
            for($i=0;$i<$adb->num_rows($result1);$i++){
                $res = $adb->fetchByAssoc($result1,$i);
                if(!in_array($res['servicecontractsid'], $ttt_data)) {
                    $res['maybe_account'] = $res['accountid'];
                    $res['modulename'] = 'SupplierContracts';
                    $data[] = $res;
                    $ttt_data[] = $res['servicecontractsid'];
                    if($res['staypaymentidid']){
                        $staypaymentjine[$res['staypaymentidid']] = array(
                            'staypaymentjine'=>  $res['staypaymentjine'],
                            'contract_no'=>$res['contract_no']
                        )
                        ;
                    }

                }
            }
        }
        //$sql2="SELECT vtiger_receivedpayments.*, vtiger_staypayment.staypaymentname,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.servicecontractsid FROM vtiger_receivedpayments LEFT JOIN vtiger_staypayment ON vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid WHERE relatetoid = '' AND receiveid = ? AND maybe_account = '' AND staypaymentname != '' AND deleted = 0 AND receivedpaymentsid NOT IN (SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ?)";
         /* $sql2="SELECT vtiger_receivedpayments.*, vtiger_staypayment.staypaymentname,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.servicecontractsid FROM vtiger_receivedpayments LEFT JOIN vtiger_staypayment ON vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid WHERE relatetoid = '' AND receiveid = ? AND staypaymentname != '' AND deleted = 0 AND( overdute  IS NULL OR overdute > SYSDATE()) AND receivedpaymentsid NOT IN ( SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ? )";*/
        $sql2 = "SELECT vtiger_receivedpayments.*, vtiger_staypayment.staypaymentname,vtiger_staypayment.staypaymentid as staypaymentidid,vtiger_staypayment.staypaymentjine,vtiger_staypayment.staypaymenttype,vtiger_staypayment.payer, 'staypayment' as matchtype,
                                vtiger_servicecontracts.contract_no,
                                vtiger_servicecontracts.servicecontractsid
                            FROM
                                vtiger_receivedpayments
                            LEFT JOIN vtiger_staypayment ON  ( (vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle and vtiger_staypayment.staypaymentname!='' ) or (vtiger_staypayment.payer like REPLACE(vtiger_receivedpayments.paytitle,'*','_')))
                            LEFT JOIN vtiger_servicecontracts ON (vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid  and vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed'))
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid
                            LEFT JOIN vtiger_account ON vtiger_receivedpayments.maybe_account = vtiger_account.accountid ## 客服的客户id等于合同的客户id
                            WHERE
							vtiger_receivedpayments.paymentchannel='支付宝转账' and 
                            vtiger_receivedpayments.deleted=0 and
                                (((vtiger_receivedpayments.relatetoid = '' OR vtiger_receivedpayments.relatetoid = 0 OR vtiger_receivedpayments.relatetoid is null) AND vtiger_receivedpayments.receivedstatus = 'normal') OR (vtiger_receivedpayments.relatetoid > 0 AND vtiger_receivedpayments.receivedstatus= 'NonPayCertificate'))
                            AND (receiveid = ? OR vtiger_account.serviceid = ? OR vtiger_servicecontracts.signid=?
                            OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmcontract WHERE crmcontract.crmid=vtiger_servicecontracts.servicecontractsid AND crmcontract.smownerid=?)
                            OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmaccount WHERE crmaccount.crmid=vtiger_servicecontracts.sc_related_to AND crmaccount.smownerid=?)
                            )     ## ## 客服负责人事当前用户
														
                            AND vtiger_crmentity.deleted = 0
                            and contract_no != ''
                            AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
                            AND contractstate = '0'       ## 合同的关闭状态为正常
                            and sideagreement=0
                            and unit_price>0
                            and (vtiger_staypayment.payer is not null and vtiger_staypayment.payer!='')    
                            AND receivedpaymentsid NOT IN (
                                SELECT DISTINCT
                                    receivepaymentid
                                FROM
                                    `vtiger_receivedpayments_throw`
                                WHERE
                                    userid = ?
                                AND vtiger_receivedpayments_throw.deleted=0
                            )
                          AND
                              vtiger_staypayment.modulestatus !='a_exception'
                          AND
                              (
                                (vtiger_staypayment.staypaymenttype='nofixation' and vtiger_receivedpayments.reality_date>=vtiger_staypayment.startdate and vtiger_receivedpayments.reality_date<=vtiger_staypayment.enddate) 
                                  or 
                                (vtiger_staypayment.staypaymenttype='fixation' and vtiger_receivedpayments.unit_price<=vtiger_staypayment.surplusmoney and vtiger_staypayment.surplusmoney>0)
                              )    
							UNION all
										SELECT vtiger_receivedpayments.*, vtiger_staypayment.staypaymentname,vtiger_staypayment.staypaymentid as staypaymentidid,vtiger_staypayment.staypaymentjine,vtiger_staypayment.staypaymenttype,vtiger_staypayment.payer, 'staypayment' as matchtype,
                                vtiger_servicecontracts.contract_no,
                                vtiger_servicecontracts.servicecontractsid
                            FROM
                                vtiger_receivedpayments
                            LEFT JOIN vtiger_staypayment ON  ( (vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle and vtiger_staypayment.staypaymentname!='' ) or (vtiger_staypayment.payer = vtiger_receivedpayments.paytitle))
                            LEFT JOIN vtiger_servicecontracts ON (vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid  and vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed'))
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid
                            LEFT JOIN vtiger_account ON vtiger_receivedpayments.maybe_account = vtiger_account.accountid ## 客服的客户id等于合同的客户id
                            WHERE
							IFNULL(vtiger_receivedpayments.paymentchannel,'')!='支付宝转账' and 
                            vtiger_receivedpayments.deleted=0 and
                                (((vtiger_receivedpayments.relatetoid = '' OR vtiger_receivedpayments.relatetoid = 0 OR vtiger_receivedpayments.relatetoid is null) AND vtiger_receivedpayments.receivedstatus = 'normal') OR (vtiger_receivedpayments.relatetoid > 0 AND vtiger_receivedpayments.receivedstatus= 'NonPayCertificate'))
                            AND (receiveid = ? OR vtiger_account.serviceid = ? OR vtiger_servicecontracts.signid=?
                            OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmcontract WHERE crmcontract.crmid=vtiger_servicecontracts.servicecontractsid AND crmcontract.smownerid=?)
                            OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmaccount WHERE crmaccount.crmid=vtiger_servicecontracts.sc_related_to AND crmaccount.smownerid=?)
                            )     ## ## 客服负责人事当前用户
														
                            AND vtiger_crmentity.deleted = 0
                            and contract_no != ''
                            AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
                            AND contractstate = '0'       ## 合同的关闭状态为正常
                            and sideagreement=0
                            and unit_price>0
                            and (vtiger_staypayment.payer is not null and vtiger_staypayment.payer!='')    
                            AND receivedpaymentsid NOT IN (
                                SELECT DISTINCT
                                    receivepaymentid
                                FROM
                                    `vtiger_receivedpayments_throw`
                                WHERE
                                    userid = ?
                                AND vtiger_receivedpayments_throw.deleted=0
                            )
                          AND
                              vtiger_staypayment.modulestatus !='a_exception'
                          AND
                              (
                                (vtiger_staypayment.staypaymenttype='nofixation' and vtiger_receivedpayments.reality_date>=vtiger_staypayment.startdate and vtiger_receivedpayments.reality_date<=vtiger_staypayment.enddate) 
                                  or 
                                (vtiger_staypayment.staypaymenttype='fixation' and vtiger_receivedpayments.unit_price<=vtiger_staypayment.surplusmoney and vtiger_staypayment.surplusmoney>0)
                              )";
        $result2 = $adb->pquery($sql2,array($currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid));
        $data2 = array();
        if($adb->num_rows($result2)>0){
            for($i=0;$i<$adb->num_rows($result2);$i++){
                $res = $adb->fetchByAssoc($result2,$i);
                if(!in_array($res['servicecontractsid'], $ttt_data)) {
                    $res['modulename'] = 'ServiceContracts';
                    $data[] = $res;
                    if($res['staypaymentidid']) {
                        $staypaymentjine[$res['receivedpaymentsid']][$res['staypaymentidid']] = array(
                            'staypaymenttype'=>$res['staypaymenttype'],
                            'staypaymentjine'=>  $res['staypaymentjine'],
                            'contract_no'=>$res['contract_no']
                        );
                    }
                }
            }
        }
		$moduleName = $request->getModule();
        $viewer = $this->getViewer ($request);
		$viewer->assign('DATA',$data);
		$viewer->assign('STAYPAYMENTJINE',$staypaymentjine);
        $viewer->view('List.tpl', $moduleName);
}
}
