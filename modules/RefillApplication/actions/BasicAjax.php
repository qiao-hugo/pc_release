<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RefillApplication_BasicAjax_Action extends Vtiger_Action_Controller {
    private $refundworkflowsid=2126542;
	function __construct() {
		parent::__construct();

		$this->exposeMethod('getaccountinfo');
        $this->exposeMethod('addAuditsettings');
        $this->exposeMethod('deletedAuditsettings');
        $this->exposeMethod('refillDetailExportData');
        $this->exposeMethod('RefillSumExportData');
        $this->exposeMethod('getReceivedPayments');
        $this->exposeMethod('getAccountPlatform');
        $this->exposeMethod('getRechargeSheet');
        $this->exposeMethod('dorefundsOrTransfers');
        $this->exposeMethod('addNewReffRayment');
        $this->exposeMethod('doAddNewReffRayment');
        $this->exposeMethod('setChargeGuarantee');
        $this->exposeMethod('delChargeGuarantee');
        $this->exposeMethod('setAccountChargeGuarantee');
        $this->exposeMethod('delAccountChargeGuarantee');
        $this->exposeMethod('getVendorBankInfo');
        $this->exposeMethod('getDetailList');
        $this->exposeMethod('getSalesorderRelalist');
        $this->exposeMethod('docancel');
        $this->exposeMethod('getSaleSorderPayments');
        $this->exposeMethod('submitRefund');
        $this->exposeMethod('getReceivedPaymentsHistory');
        $this->exposeMethod('AmountRepaidContract');
        $this->exposeMethod('checkRefundsOrTransfers');
        $this->exposeMethod('checkTechprocurement');
        $this->exposeMethod('getVendorList');
        $this->exposeMethod('setAuditInformation');
        $this->exposeMethod('revokeRelation');
        $this->exposeMethod('relationPaymentsData');
        $this->exposeMethod('exportdata');
        $this->exposeMethod('financialstate');
        $this->exposeMethod('dorefundsOrTransfersconfirm');
        $this->exposeMethod('matchReceivementsList');
        $this->exposeMethod('doAddNewReffRaymentMore');
        $this->exposeMethod('showTable');
        $this->exposeMethod('getAddEditCommon');
        $this->exposeMethod('hcDetailsExport');
        $this->exposeMethod('getSupplierAccountInfo');
        $this->exposeMethod('startToContractChangesExport');
        $this->exposeMethod('getAccountplatformInfo');
	    $this->exposeMethod('uploadBatchImport');
        $this->exposeMethod('uploadBatchOutput');
	}

	function checkPermission(Vtiger_Request $request) {
		return;
	}



    /**
     * 取得合同对应的信息,对应的客户,对应的合同总额,对应的回款,对应的发票,对应的工单
     * @param Vtiger_Request $request
     */
    public function getaccountinfo(Vtiger_Request $request){
        $recordId = $request->get('record');//合同的id
        $salesorderid = $request->get('salesorderid');//工单的id 编辑模式
        $db=PearDatabase::getInstance();
        //查询合同下产品信息
        $sql = 'SELECT `productid`, `productform`, salesorderid, IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), \'--\' ) AS productcomboid FROM vtiger_salesorderproductsrel WHERE servicecontractsid =  ? AND multistatus in(0,3)';
        $product = $db->pquery($sql,array($recordId));
        $productids = $db->num_rows($product);
        //合同下得有产品//没有无产品的合同提工单
        $productidlist = array();
        if($productids>0){
            while($row=$db->fetchByAssoc($product)){
                $productidlist[$row['productid']] = $row;
                //$module = Vtiger_Record_Model::getInstanceById($row['productid'],'Products');$productidlist[$i] = $module->getData();
                //$productidlist[$i]['solutions']= empty($row['salesorderid'])?$productidlist[$i]['notecontent']:$row['notecontent'];
                //$productidlist[$i]['productcomboid']= empty($row['productcomboid'])?'--':$row['productcomboid'];
            }
        }
        $query='SELECT sum(IFNULL(unit_price,0)) AS unit_price from vtiger_receivedpayments WHERE relatetoid=?';
        $receivedpayments=$db->pquery($query,array($recordId));
        $receivedpaymentstotal=$db->query_result_rowdata($receivedpayments);


        $moduleName = $request->get('module');
        $module = Vtiger_Record_Model::getInstanceById($recordId,'ServiceContracts');
        $result=array();
        $result=$module->getData();
        //三方合同的取得是代理商的客户id  2021/03/04 njw
        $accountsId=$result['sc_related_to'];
        if($result['agentid']){
            $accountsId = $result['agentaccountid'];
        }
        $smownerid=$result['Receiveid'];
        $isautoclose = $result['isautoclose'];
        $total=$isautoclose==1?$result['total']:0;
        $remark = $result['remark'];
        $signdate = $result['signdate'];
        $modulestatus = $result['modulestatus'];

        $sumrefilltotal=0;
        if($total>0 && $isautoclose==1){
            $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
            $sumrefilltotal=$recordModel->getSumActualtotalrecharge($recordId);
        }
        $accountsModule=Vtiger_Record_Model::getInstanceById($accountsId,'Accounts');
        //合同数据
        $accounts=$accountsModule->getData();
        //相应合同对应充值申请单
        $strHtml='';
        //如果是合同变更申请则请走下面并获取变更数据
        if($_REQUEST['rechargesource']=='contractChanges'){
            $record = $request->get("record");
            $oldrechargesource =$request->get("oldrechargesource");
            if(!empty($record)){
                $refillapplicationList = RefillApplication_Record_Model::getListAboutServiceContractRefillapplication($record,$oldrechargesource);
                foreach ($refillapplicationList as $key=>$val){
                    $strHtml.='<tr class="needToRemove">'.
                                    '<td><input type="checkbox" value="'.$val['refillapplicationid'].'"  data-grossadvances="'.$val['grossadvances'].'" data-actualtotalrecharge="'.$val['actualtotalrecharge'].'" data-totalreceivables="'.$val['totalreceivables'].'" class="entryCheckBox"  name="contractChangeApplication[]" title="合同变更申请"></td>'.
                                    '<td>'.$val['refillapplicationno'].'</td>'.
                                    '<td>'.vtranslate($val['rechargesource'],"RefillApplication").'</td>'.
                                    '<td>'.$val['smownerid'].'</td>'.
                                    '<td>'.$val['createdtime'].'</td>'.
                                    '<td>'.$val['grossadvances'].'</td>'.
                                    '<td>'.$val['actualtotalrecharge'].'</td>'.
                                    '<td>'.$val['totalreceivables'].'</td>'.
                              '</tr>';
                }
            }
        }
        //合同回款记录
        $rp=ReceivedPayments_Record_Model::getAllReceivedPayments($recordId);
        $return=array('signdate'=>$signdate,'modulestatus'=>$modulestatus,'accountname'=>$accounts['accountname'],'customertype'=>$accounts['customertype'],'id'=>$accounts['record_id'],'advancesmoney'=>$accounts['advancesmoney'],'userid'=>$smownerid,'total'=>$total,'sumrefilltotal'=>$sumrefilltotal,'rp'=>$rp,'remark'=>$remark,'rtotal'=>$receivedpaymentstotal['unit_price'],'salesorderlist'=>OrderChargeback_Record_Model::getRelateProduct($recordId),'invoicelist'=>OrderChargeback_Record_Model::getRelateInvoice($recordId),'strHtml'=>$strHtml,'isautoclose'=>$isautoclose);
        $datas=array($productidlist,$return);
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
    }

    /**cxh 2019-10-31 新增
     * 取得供应商合同对应的信息
     * @param Vtiger_Request $request
     */
    public function getSupplierAccountInfo(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $recordId = $request->get('record');//合同的id
        $module = Vtiger_Record_Model::getInstanceById($recordId,'SupplierContracts');
        $result=$module->getData();
        $vendorid=$result['vendorid'];
        $total=$result['total']?$result['total']:0;
        $signdate = $result['signdate'];
        $modulestatus = $result['modulestatus'];
        //查询供应商合同对应的充值单已充值金额(求和充值点明细金额求和（除作废 和打回的）-红冲明细退款（除删除的）)
        $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $sumrefilltotal=$recordModel->getSumTotalreceivables($recordId);

        // accountname  id   total   modulestatus  customertype   signdate
        $accountsModule=Vtiger_Record_Model::getInstanceById($vendorid,'Vendors');
        //合同数据
        $accounts=$accountsModule->getData();
        //相应合同对应充值申请单
        $strHtml='';
        //如果是合同变更申请则请走下面并获取关联供应商数据更数据
        if($_REQUEST['rechargesource']=='contractChanges'){
            $record=$request->get("record");
            $oldrechargesource =$request->get("oldrechargesource");
            if(!empty($record)){
                $refillapplicationList = RefillApplication_Record_Model::getListAboutSupplierContractRefillapplication($record,$oldrechargesource);
                foreach ($refillapplicationList as $key=>$val){
                    $strHtml.='<tr class="needToRemove">'.
                        '<td><input type="checkbox" value="'.$val['refillapplicationid'].'"  data-grossadvances="'.$val['grossadvances'].'" data-actualtotalrecharge="'.$val['actualtotalrecharge'].'" data-totalreceivables="'.$val['totalreceivables'].'" class="entryCheckBox"  name="contractChangeApplication[]" title="合同变更申请"></td>'.
                        '<td>'.$val['refillapplicationno'].'</td>'.
                        '<td>'.vtranslate($val['rechargesource'],"RefillApplication").'</td>'.
                        '<td>'.$val['smownerid'].'</td>'.
                        '<td>'.$val['createdtime'].'</td>'.
                        '<td>'.$val['grossadvances'].'</td>'.
                        '<td>'.$val['actualtotalrecharge'].'</td>'.
                        '<td>'.$val['totalreceivables'].'</td>'.
                        '</tr>';
                }
            }
        }
        $return=array('signdate'=>$signdate,'modulestatus'=>$modulestatus,'accountname'=>$accounts['vendorname'],'customertype'=>$accounts['vendortype'],'id'=>$vendorid,'total'=>$total,'sumrefilltotal'=>$sumrefilltotal,'strHtml'=>$strHtml);
        $datas=$return;
        $datas=array(array(),$datas);
        $response = new Vtiger_Response();
        $response->setResult($datas);
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

    // 充值申请单审核追加
    function addAuditsettings(Vtiger_Request $request) {
        $auditsettingtype = $request->get("auditsettingtype");
        $department = $request->get("department");
        $oneaudituid = $request->get("oneaudituid");
        $towaudituid = $request->get("towaudituid");
        $audituid3 = $request->get("audituid3");
        $data = array('flag'=>'0', 'msg'=>'添加失败');

        do {
            $moduleModel = Vtiger_Module_Model::getInstance('RefillApplication');
            if(!$moduleModel->exportGrouprt('RefillApplication','AuditSettings')){   //权限验证
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
            /*if (empty($towaudituid)) {
                break;
            }*/
            //$sql = "delete from vtiger_auditsettings where auditsettingtype=? AND department=? AND oneaudituid=?";
            $sql2 = "INSERT INTO `vtiger_auditsettings` (`auditsettingsid`, `auditsettingtype`, `department`, `oneaudituid`, `towaudituid`,`audituid3`, `createtime`, `createid`) VALUES (NULL, ?,?, ?, ?,?,?,?);";
            global $current_user;
            $db=PearDatabase::getInstance();
            //$db->pquery($sql, array($auditsettingtype, $department, $oneaudituid, $towaudituid));
            $db->pquery($sql2, array($auditsettingtype, $department, $oneaudituid, $towaudituid, $audituid3,date('Y-m-d H:i:s'), $current_user->id));
            $data = array('flag'=>'1', 'msg'=>'添加成功');
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    function deletedAuditsettings(Vtiger_Request $request) {
        $moduleModel = Vtiger_Module_Model::getInstance('RefillApplication');

        if($moduleModel->exportGrouprt('RefillApplication','AuditSettings')){   //权限验证
            global $current_user;
            $id=$request->get("id");
            $delsql="delete from vtiger_auditsettings where auditsettingsid=?";
            $db=PearDatabase::getInstance();
            $db->pquery($delsql,array($id));
        }
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    /**
     * 导出充值审请明细
     * @param Vtiger_Request $request
     */
    public function refillDetailExportData(Vtiger_Request $request){
        $moduleModel=Vtiger_Module_Model::getInstance('RefillApplication');
        $moduleModel->refillDetailExportDataExcel($request);
    }

    /**
     * 导出充值审请单汇总信息
     * @param Vtiger_Request $request
     */
    public function RefillSumExportData(Vtiger_Request $request){
        $moduleModel=Vtiger_Module_Model::getInstance('RefillApplication');
        $moduleModel->refillSumExportDataExcel($request);
    }

    /**
     * 取得可用的充值金额
     * @param Vtiger_Request $request
     */
    public function getReceivedPayments(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $return = $recordModel->getReceivedPaymentsData($request);

        $response=new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }

    /**
     * @param $servicecontractid
     * @return array
     * 取得回款列表
     */
    private function getReceivedPaymentsData($servicecontractid,$receivedstatus="receivedstatus='normal'"){
        $db=PearDatabase::getInstance();
        $result=$db->pquery("SELECT * FROM vtiger_receivedpayments WHERE {$receivedstatus} AND deleted=0 AND rechargeableamount>0 AND relatetoid=?",array($servicecontractid));
        $data=array();
        while($row=$db->fetch_array($result)){
            $row['rorigin']=(($row['receivedstatus']=='virtualrefund')?'赠'.$row['rorigin']:'正常');
            $data[]=$row;
        };
        return $data;
    }

    /**
     * @param Vtiger_Request $request
     * 获取用户平台信息
     */
    public function getAccountPlatform(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $return = $recordModel->getAccountPlatform($request);
        $response=new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }
    /**
     * 读取供应商银行账户及发票信息
     */
    public function getVendorBankInfo(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $return = $recordModel->getVendorBankInfo($request);
        $response=new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * 获取退款或退款列表
     */
    public function getRechargeSheet(Vtiger_Request $request){
        $recordId=$request->get('record');
        $rechargesheetid=$request->get('rechargesheetid');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'RefillApplication');
        $modulestatus=$recordModel->get('modulestatus');
        $rechargesource=$recordModel->get('rechargesource');
        $ispayment=$recordModel->get('ispayment');
        if(($rechargesource=='Vendors'||$rechargesource=='Vendors') && $ispayment=='inpayment'){
            echo 1;
            exit();
        }
        /*if($recordModel->get('isbackwash')!=0){
            echo "<h3 style='text-align: center;color:red;'>红冲退款审核中!";
            exit;
        }*/
        if(!$recordModel->getisbackwash($rechargesheetid)){
            echo "<h3 style='text-align: center;color:red;'>尚有未处理红冲数据,请先处理!";
            exit;
        }
        if($modulestatus!='c_complete'){
            echo "<h3 style='text-align: center;color:red;'>只有状态是完成的申请单才能操作";
            exit;
        }
        global $current_user;
        if(!in_array($rechargesource,array('Accounts','Vendors','NonMediaExtraction'))){
            echo "<h3 style='text-align: center;color:red;'>不支持该操作</h3>";
            exit;
        }
        /*if($current_user->id!=$recordModel->get('assigned_user_id')){
            echo "<h3 style='text-align: center;color:red;'>只有提单人才能执行操作</h3>";
            exit;
        }*/

        $db=PearDatabase::getInstance();
        $result=$db->pquery('SELECT * FROM `vtiger_rechargesheet` WHERE deleted=0 AND transferamount>refundamount AND rechargesheetid=? limit 1',array($rechargesheetid));
        $data=$db->raw_query_result_rowdata($result,0);
        if($db->num_rows($result)==0){
            echo "<h3 style='text-align: center;color:red;'>金额已使用完</h3>";
            exit;
        }
        $supplierRefundString='';

        if($rechargesource=='Vendors'){
            $supplierRefundString='<input type="hidden" name="ispayment" value="'.$ispayment.'"/>';
            // cxh 2020-04-14 start 如果是未付款不带出退款信息
            if($ispayment!='unpaid'){
                $supplierRefundData=$recordModel->getPaymentsSupplierRefund($recordModel->get('vendorid'));
                if(empty($supplierRefundData) && $ispayment!='unpaid'){
                    echo "<h3 style='text-align: center;color:red;'>供应商退款未匹配或退款金额用完!</h3>";
                    exit;
                }
            }
            // cxh end

            if(!empty($supplierRefundData)) {
                $supplierRefundString = '<input type="hidden" name="ispayment" value="'.$ispayment.'"/><table class="table table-bordered blockContainer newinvoicerayment_tab detailview-table" >
                <thead><tr><td><label class="muted">供应商退款信息</label></td>
                <td nowrap><label class="muted">退款金额</label></td>
                <td nowrap><label class="muted">入账日期</label></td>
                <td nowrap><label class="muted">已红冲金额</label></td>
                <td nowrap><label class="muted">可红冲金额</label></td>
                <td nowrap><label class="muted">退款现金</label></td>
                </tr></thead>
                <tbody>';
                foreach ($supplierRefundData as $refundValue) {
                    $rechargeableamount=bcsub($refundValue['rechargeableamount'],$refundValue['backwashtotal'],2);
                    $supplierRefundString .= '<tr class="receiveprayment"><td><input type="hidden" name="updaterepayment' . $refundValue['receivedpaymentsid'] . '" value="' . $refundValue['receivedpaymentsid'] . '"><input type="hidden" name="owncompany' . $refundValue['receivedpaymentsid'] . '"  value="' . $refundValue['owncompany'] . '"><div class="row-fluid"><span class="span10"><select name="paytitle' . $refundValue['receivedpaymentsid'] . '" data-id="' . $refundValue['receivedpaymentsid'] . '" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="' . $refundValue['paytitle'] . '">' . $refundValue['paytitle'] . $refundValue['owncompany'] . '</option></select></td>
                        <td><input type="hidden" class="input-large repaymentunit_price' . $refundValue['receivedpaymentsid'] . '" name="unit_price' . $refundValue['receivedpaymentsid'] . '" data-id="' . $refundValue['receivedpaymentsid'] . '" readonly="readonly" value="' . $refundValue['unit_price'] . '">' . $refundValue['unit_price'] . '</td>
                        <td><input type="hidden" class="input-large repaymentreality_date' . $refundValue['receivedpaymentsid'] . '" name="reality_date' . $refundValue['receivedpaymentsid'] . '" data-id="' . $refundValue['receivedpaymentsid'] . '" value="' . $refundValue['reality_date'] . '">' . $refundValue['reality_date'] . '</td>
                        <td><input type="hidden" class="input-large canrefundvalue' . $refundValue['receivedpaymentsid'] . '" name="canrefundvalue' . $refundValue['receivedpaymentsid'] . '" data-id="' . $refundValue['receivedpaymentsid'] . '" value="' . bcsub($refundValue['unit_price'], $rechargeableamount, 2) . '">' . bcsub($refundValue['unit_price'], $rechargeableamount, 2) . '</td>
                        <td><input type="hidden" class="input-large repaymentbackwashtotal' . $refundValue['receivedpaymentsid'] . '"  name="repaymentbackwashtotal' . $refundValue['receivedpaymentsid'] . '" data-id="' . $refundValue['receivedpaymentsid'] . '" value="' .$rechargeableamount. '">' .$rechargeableamount. '</td>
                        <td><input type="text" class="input-large repaymenttotal' . $refundValue['receivedpaymentsid'] . '" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="repaymenttotal' . $refundValue['receivedpaymentsid'] . '" data-id="' . $refundValue['receivedpaymentsid'] . '" value="0"></td>
                        </tr>';
                }
                $supplierRefundString .= '<input  type=\'hidden\' name=\'supplierRefundIsExists\' value=\'1\'   /></tbody></table>';
            }else{
                $supplierRefundString.="<input  type='hidden' name='supplierRefundIsExists' value='0'   />";
            }
        }
        $str=$recordModel->setDataPreRechargeDisplay($request);
        $reprament='<table class="table table-bordered blockContainer newinvoicerayment_tab detailview-table" >
                    <thead><tr><td><label class="muted">回款信息</label></td>
                    <td nowrap><label class="muted">入账金额</label></td>
                    <td nowrap><label class="muted">入账日期</label></td>
                    <td nowrap><label class="muted">充值金额</label></td>
                    <td nowrap><label class="muted">可退款金额</label></td>
                    <td nowrap><label class="muted">退款现金</label></td>
                    </tr></thead>
                    <tbody>';

        //$query='SELECT * FROM `vtiger_refillapprayment` WHERE deleted=0 AND refillapplicationid=? AND receivedstatus=\'normal\'';
        $query='SELECT vtiger_refillapprayment.*,IFNULL((SELECT sum(vtiger_refillredrefund.backwashtotal) FROM `vtiger_refillredrefund` WHERE vtiger_refillredrefund.refillapplicationid=vtiger_refillapprayment.refillapplicationid AND vtiger_refillredrefund.receivedpaymentsid=vtiger_refillapprayment.receivedpaymentsid AND vtiger_refillredrefund.deleted=0),0) AS backwashtotaling FROM `vtiger_refillapprayment` WHERE deleted=0 AND refillapplicationid=? AND receivedstatus=\'normal\'';
        $result=$db->pquery($query,array($recordId));

        $repramentlist='';
        while($row=$db->fetchByAssoc($result)){
            if(bccomp($row['backwashtotal'],$row['backwashtotaling'],2)<1){
                continue;
            }
            $backwashtotal=bcsub($row['backwashtotal'],$row['backwashtotaling'],2);
            $repramentlist.='<tr class="refillprayment"><td><input type="hidden" name="updaterefillprayment'.$row['refillappraymentid'].'" value="'.$row['refillappraymentid'].'"><input type="hidden" name="owncompany'.$row['refillappraymentid'].'"  value="'.$row['owncompany'].'"><div class="row-fluid"><span class="span10"><select name="paytitle'.$row['refillappraymentid'].'" data-id="'.$row['refillappraymentid'].'" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="'.$row['paytitle'].'">'.$row['paytitle'].$row['owncompany'].'</option></select></td>
                        <td><input type="hidden" class="input-large total'.$row['refillappraymentid'].'" name="total'.$row['refillappraymentid'].'" data-id="'.$row['refillappraymentid'].'" readonly="readonly" value="'.$row['total'].'">'.$row['total'].'</td>
                        <td><input type="hidden" class="input-large arrivaldate'.$row['refillappraymentid'].'" name="arrivaldate'.$row['refillappraymentid'].'" data-id="'.$row['refillappraymentid'].'" value="'.$row['arrivaldate'].'">'.$row['arrivaldate'].'</td>
                        <td><input type="hidden" class="input-large allowrefillapptotal'.$row['refillappraymentid'].'" name="refillapptotal'.$row['refillappraymentid'].'" data-id="'.$row['refillappraymentid'].'" value="'.$row['refillapptotal'].'">'.$row['refillapptotal'].'</td>
                        <td><input type="hidden" class="input-large backwashtotal'.$row['refillappraymentid'].'"  name="backwashtotal'.$row['refillappraymentid'].'" data-id="'.$row['refillappraymentid'].'" value="'.$backwashtotal.'">'.$backwashtotal.'</td>
                        <td><input type="text" class="input-large refillapptotal'.$row['refillappraymentid'].'" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="refundamount'.$row['refillappraymentid'].'" data-id="'.$row['refillappraymentid'].'" value="0"></td>
                        </tr>';
        }
        $reprament.=$repramentlist.'</tbody></table>';
        echo '<form id="refundsTransfers" method="post">'.$str,$reprament,$supplierRefundString,'</form>';
        $_SESSION['refundsOrTransfers']=12345678;
    }

    /**
     * @param Vtiger_Request $request
     * 执行退款或退货操作
     */
    public function dorefundsOrTransfers(Vtiger_Request $request){
        $recordid=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'RefillApplication');
        $rechargesource=$recordModel->get('rechargesource');
        $actualtotalrecharge=$recordModel->get('actualtotalrecharge');//实际充值金额
        $totalrecharge=$recordModel->get('totalrecharge');//现金金额
        $data=$request->get('data');
        $ispayment=$data['ispayment'];
        $updaterefillprayment=$request->get('updaterefillprayment');
        $refundamount=$request->get('refundamount');//红冲金额
        $refundAmountSum=array_sum($refundamount);//红冲对应的现金款额
        $currentRefund=$actualtotalrecharge-$totalrecharge;//垫款额
        $mrefundamount=$data['mrefundamount'];//红冲的应付款额
        $refenddiff=$mrefundamount-$refundAmountSum;//红冲差额
        $repaymenttotal=$request->get('repaymenttotal');//供应商退款
        $updaterepayment=$request->get('updaterepayment');//供应商退款ID
        $repaymenttotalSum=array_sum($repaymenttotal);//供应商退款合计
        $remark=$data['mstatus'];//备注
        $amountpayable=$data['amountpayable']>0?$data['amountpayable']:0;//应付款金额
        $financialstate=$recordModel->get('financialstate');//手动销账
        $return=array('flag'=>false);
        do {
            $returnData=$this->checkRefundsOrTransfers($request,0);
            if(!$returnData['flag']){
                $return=$returnData;
                break;
            }
            if($rechargesource=='Vendors'){
                if(empty($amountpayable) && $ispayment!='unpaid'){
                    $return['msg']='供应商退款金额为0!';
                    break;
                }
                //红冲金额必需和供应商金额一致
                if(bccomp($repaymenttotalSum,$amountpayable)!=0 && $ispayment!='unpaid'){
                    $return['msg']='红冲金额不等!';
                    break;
                }

            }
            /*if (($mrefundamount - $refundAmountSum) != 0) {
                $return['msg']='退款金额不等!';
                break;
            }*/
            if(bccomp($actualtotalrecharge,$refundAmountSum)<0){
                //退款金额大于实际金额
                $return['msg']='退款或退货有问题!,请重新修改!';
                break;
            }
            if(bccomp($mrefundamount,$refundAmountSum)<0){
                //一般不会出现
                $return['msg']='退款或退货有问题!,请重新修改!';
                break;
            }
            //主要处理这个
            if(bccomp($mrefundamount,$refundAmountSum)>0 && $financialstate!=1){
                //红冲的应付款额>红冲对应的现金额
                if(bccomp($refenddiff,$currentRefund)>0){
                    $return['msg']='红冲垫款额大于实际垫款额!,请重新修改!';
                    break;
                }
            }
            if($_SESSION['refundsOrTransfers']!=12345678){
                $return['msg']='重复提交!';
                break;
            }
            unset($_SESSION['refundsOrTransfers']);
            $rechargesheetid=$data['rechargesheetid'];
            $mprestoreadrate=$data['mprestoreadrate'];
            $mrechargeamount=$data['mrechargeamount'];
            /*$mfactorage=$data['mfactorage'];
            $mactivationfee=$data['mactivationfee'];
            $mtaxation=$data['mtaxation'];*/
            $mfactorage=$data['factorage'];
            $mactivationfee=$data['activationfee'];
            $mtaxation=$data['taxation'];
            $db=PearDatabase::getInstance();
            foreach($updaterefillprayment as $value) {
                $array=array();
                $refundamount='refundamount'.$value;
                $refundamountd=$data[$refundamount];
                if($refundamountd<=0){
                    continue;
                }
                $query="SELECT * FROM vtiger_refillapprayment WHERE refillappraymentid=?";
                $reuslt=$db->pquery($query,array($value));
                $dataResult=$db->raw_query_result_rowdata($reuslt,0);
                $receivedpaymentsid=$dataResult['receivedpaymentsid'];
                $array['receivedpaymentsid']=$receivedpaymentsid;
                $array['backwashtotal']=$refundamountd;
                $array['refillapplicationid']=$recordid;
                $array['deleted']=0;
                $array['refillappraymentid']=$value;
                $array['rechargesheetid']=$rechargesheetid;
                $array['mstatus']='normal';
                $array['isshow']='1';

                $db->pquery('INSERT INTO `vtiger_refillredrefund` (`receivedpaymentsid`, `backwashtotal`, `refillapplicationid`, `deleted`, `refillappraymentid`,rechargesheetid,mstatus,isshow) VALUES (?,?,?,?,?,?,?,?)',$array);
            }
            if($rechargesource=='Vendors') {
                foreach ($updaterepayment as $value) {
                    $array=array();
                    $tarray=array();
                    $repayment = 'repaymenttotal' . $value;
                    $repaymentd = $data[$repayment];
                    if ($repaymentd <= 0) {
                        continue;
                    }
                    $receivedpaymentsid = $value;
                    $array['receivedpaymentsid'] = $value;
                    $array['backwashtotal'] = $repaymentd;
                    $array['refillapplicationid'] = $recordid;
                    $array['deleted'] = 0;
                    $array['refillappraymentid'] = $value;
                    $array['mstatus'] = 'SupplierRefund';
                    $db->pquery('INSERT INTO `vtiger_refillredrefund` (`receivedpaymentsid`, `backwashtotal`, `refillapplicationid`, `deleted`, `refillappraymentid`,mstatus,rechargesheetid,isshow) VALUES (?,?,?,?,?,?,'.$rechargesheetid.',1)', $array);

                    $tarray[]=0;
                    $tarray[]=$repaymentd;
                    $tarray[]=$remark;
                    $tarray[]=$recordid;
                    $tarray[]=$repaymentd;
                    $tarray[]=date("Y-m-d");
                    $tarray[]=date("Y-m-d H:i:s");
                    $tarray[]='SupplierRefund';
                    $tarray[]=$repaymentd;
                    $tarray[]=$value;
                    //$sql="INSERT INTO vtiger_refillapprayment(`servicecontractsid`,`receivedpaymentsid`,`total`,`arrivaldate`,`refillapptotal`,`allowrefillapptotal`,`remarks`,`refillapplicationid`,`paytitle`,`backwashtotal`,`owncompany`,`matchdate`,createdtime) SELECT ?,? FROM vtiger_receivedpayments WHERE receivedpaymentsid=?";
                    $sql="INSERT INTO vtiger_refillapprayment(`servicecontractsid`,`receivedpaymentsid`,`total`,`arrivaldate`,`refillapptotal`,`allowrefillapptotal`,`remarks`,`refillapplicationid`,`paytitle`,`backwashtotal`,`owncompany`,`matchdate`,createdtime,receivedstatus,refundamount) 
                          SELECT ?,receivedpaymentsid,vtiger_receivedpayments.unit_price,vtiger_receivedpayments.reality_date,?,vtiger_receivedpayments.rechargeableamount,?,?,vtiger_receivedpayments.paytitle,?,vtiger_receivedpayments.owncompany,?,?,?,? FROM vtiger_receivedpayments WHERE receivedpaymentsid=?";
                    $db->pquery($sql,$tarray);
                }
            }
            $datetime=date('Y-m-d H:i:s');
            $array=array(
                $rechargesheetid,
                $recordid,
                $mprestoreadrate,
                $mrechargeamount,
                $mfactorage,
                $mactivationfee,
                $mtaxation,
                $mrefundamount,
                $datetime,
                $remark,
                $amountpayable,
                1
            );
            $db->pquery('INSERT INTO vtiger_rubricrechargesheet(rechargesheetid,refillapplicationid,prestoreadrate,rechargeamount,factorage,activationfee,taxation,refundamount,createdtime,remark,amountpayable,isbackwash) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)',$array);
            $db->pquery("UPDATE vtiger_refillapplication SET isbackwash=1 WHERE refillapplicationid=?", array($recordid));
            /*
            //生成工作流
            $sequence=1;
            $departmentid=$_SESSION['userdepartmentid'];
            $ispayment=$recordModel->get('ispayment');
            if($rechargesource=='Vendors') {
                $_REQUEST['workflowsid'] = 2131276;
                if($ispayment=='unpaid'){
                    $needle='H283::';
                    $query='SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE departmentid=?';
                    $result=$db->pquery($query,array($departmentid));
                    $data=$db->raw_query_result_rowdata($result,0);
                    $parentdepartment=$data['parentdepartment'];
                    $parentdepartment.='::';
                    $_REQUEST['workflowsid'] = 2199184;
                    if(strpos($parentdepartment,$needle)!==false){
                        $sequence=2;
                    }
                }
            }else{
                $_REQUEST['workflowsid'] = $this->refundworkflowsid;
            }
            $db->pquery('DELETE FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.salesorderid=?',array(2126542,$recordid));
            $db->pquery('DELETE FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.salesorderid=?',array(2131276,$recordid));
            $db->pquery('DELETE FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.salesorderid=?',array(2199184,$recordid));
            global $current_user;
            $focus = CRMEntity::getInstance('RefillApplication');
            $focus->makeWorkflows('RefillApplication', $_REQUEST['workflowsid'], $recordid);
            //$departmentid=empty($current_user->departmentid)?'H1':$current_user->departmentid;

            //$result=$focus->db->pquery("SELECT vtiger_auditsettings.oneaudituid FROM `vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid=?) AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='RefillApplication' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1",array($departmentid));
            //$data=$focus->db->query_result_rowdata($result,0);
            $data=$focus->getAudituid('RefillApplication',$departmentid);
            $focus->db->pquery("UPDATE vtiger_refillapplication SET modulestatus='b_actioning',isbackwash=1 WHERE refillapplicationid=?",array($recordid));
            if($rechargesource=='Vendors') {
                if($ispayment=='unpaid'){
                    $isaction='';
                    if($sequence==2){
                        $focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.workflowsid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='RefillApplication'",array(7629,$recordid,$_REQUEST['workflowsid']));
                    }else{
                        $focus->db->pquery('DELETE FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.sequence=1',array($_REQUEST['workflowsid'],$recordid));
                        $isaction=',isaction=1';
                    }
                    $focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1{$isaction},higherid=? WHERE vtiger_salesorderworkflowstages.sequence=2 AND vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='RefillApplication'",array($data['oneaudituid'],$_REQUEST['workflowsid'],$recordid));
                    $return=array('flag'=>true);
                    break;
                }
            }
            $focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='RefillApplication'",array($data['oneaudituid'],$recordid));
            */
            $return=array('flag'=>true);
        }while(0);
        $response=new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * 执行退款或退货操作Bak
     */
    public function dorefundsOrTransfersBak(Vtiger_Request $request){
        $recordid=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'RefillApplication');
        $rechargesource=$recordModel->get('rechargesource');
        $actualtotalrecharge=$recordModel->get('actualtotalrecharge');//实际充值金额
        $totalrecharge=$recordModel->get('totalrecharge');//现金金额
        $data=$request->get('data');
        $updaterefillprayment=$request->get('updaterefillprayment');
        $refundamount=$request->get('refundamount');//红冲金额
        $refundAmountSum=array_sum($refundamount);//红冲对应的现金款额
        $currentRefund=$actualtotalrecharge-$totalrecharge;//垫款额
        $mrefundamount=$data['mrefundamount'];//红冲的应付款额
        $refenddiff=$mrefundamount-$refundAmountSum;//红冲差额
        $return=array('flag'=>false);
        do {
            if($_SESSION['refundsOrTransfers']!=12345678){
                $return['msg']='重复提交!';
                break;
            }

            /*if (($mrefundamount - $refundAmountSum) != 0) {
                $return['msg']='退款金额不等!';
                break;
            }*/
            if($actualtotalrecharge<$refundAmountSum){
                //退款金额大于实际金额
                $return['msg']='退款或退货有问题!,请重新修改!';
                break;
            }
            if($mrefundamount<$refundAmountSum){
                //一般不会出现
                $return['msg']='退款或退货有问题!,请重新修改!';
                break;
            }
            //主要处理这个
            if($mrefundamount>$refundAmountSum){
                //红冲的应付款额>红冲对应的现金额
                if($refenddiff>$currentRefund){
                    $return['msg']='红冲垫款额大于实际垫款额!,请重新修改!';
                    break;
                }
            }
            unset($_SESSION['refundsOrTransfers']);
            $rechargesheetid=$data['rechargesheetid'];
            $mprestoreadrate=$data['mprestoreadrate'];
            $mrechargeamount=$data['mrechargeamount'];
            /*$mfactorage=$data['mfactorage'];
            $mactivationfee=$data['mactivationfee'];
            $mtaxation=$data['mtaxation'];*/
            $mfactorage=0;
            $mactivationfee=0;
            $mtaxation=0;
            $db=PearDatabase::getInstance();
            foreach($updaterefillprayment as $value) {
                $refundamount='refundamount'.$value;
                $refundamountd=$data[$refundamount];
                if($refundamountd<=0){
                    continue;
                }
                $db->pquery('UPDATE
                                      `vtiger_refillapprayment` 
                                SET backwashtotal=if((backwashtotal-'.$refundamountd.')>0,backwashtotal-'.$refundamountd.',0),
                                refundamount=if((refundamount+'.$refundamountd.')>refillapptotal,refillapptotal,refundamount+'.$refundamountd.') 
                                WHERE refillappraymentid=?',array($value));
                $db->pquery("UPDATE `vtiger_receivedpayments` SET rechargeableamount=if((rechargeableamount+{$refundamountd})>unit_price,unit_price,(rechargeableamount+{$refundamountd})) WHERE receivedpaymentsid=(SELECT vtiger_refillapprayment.receivedpaymentsid FROM vtiger_refillapprayment WHERE vtiger_refillapprayment.refillappraymentid=? LIMIT 1)",array($value));
                $query="SELECT * FROM vtiger_refillapprayment WHERE refillappraymentid=?";
                $reuslt=$db->pquery($query,array($value));
                $dataResult=$db->raw_query_result_rowdata($reuslt,0);
                $receivedpaymentsid=$dataResult['receivedpaymentsid'];
                $receivedPaymentsRecordModel=Vtiger_Record_Model::getInstanceById($receivedpaymentsid,'ReceivedPayments');
                $rechargeableamount=$receivedPaymentsRecordModel->get('rechargeableamount');
                $this->setTracker('ReceivedPayments',$receivedpaymentsid,array('fieldName'=>'rechargeableamount','oldValue'=>$rechargeableamount,'currentValue'=>($rechargeableamount-$refundamountd).'充值回款红冲'));

            }
            if($actualtotalrecharge==$totalrecharge){
                //无垫款
                $difftemp=$actualtotalrecharge-$refundAmountSum;
                $db->pquery("UPDATE vtiger_refillapplication SET totalrecharge=?,actualtotalrecharge=? WHERE refillapplicationid=?",array($difftemp,$difftemp,$recordid));
                $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'totalrecharge','oldValue'=>$totalrecharge,'currentValue'=>$difftemp.'红冲'));
                $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'actualtotalrecharge','oldValue'=>$actualtotalrecharge,'currentValue'=>$difftemp.'红冲'));
            }elseif($actualtotalrecharge>$totalrecharge && $totalrecharge>0){
                //部分垫款
                //1红冲==实际的充值现金
                $adifftemp=$actualtotalrecharge-$mrefundamount;
                $tdifftemp=$totalrecharge-$refundAmountSum;
                $newgrossadvances=$adifftemp-$tdifftemp;
                $grossadvances=$recordModel->get('grossadvances');
                $db->pquery("UPDATE vtiger_refillapplication SET totalrecharge=?,actualtotalrecharge=?,grossadvances=? WHERE refillapplicationid=?",array($tdifftemp,$adifftemp,$newgrossadvances,$recordid));
                $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'totalrecharge','oldValue'=>$totalrecharge,'currentValue'=>$tdifftemp.'红冲'));
                $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'actualtotalrecharge','oldValue'=>$actualtotalrecharge,'currentValue'=>$adifftemp.'红冲'));
                $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'grossadvances','oldValue'=>$grossadvances,'currentValue'=>$adifftemp.'红冲'));

                if($mrefundamount>$refundAmountSum){
                    $accountRecordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
                    $accountRecordModel->setAdvancesmoney($recordModel->get('accountid'),$refenddiff*-1,'回款红冲');
                }

            }elseif($actualtotalrecharge>$totalrecharge && $totalrecharge==0){
                //全部垫款

                $difftemp=$actualtotalrecharge-$mrefundamount;
                $grossadvances=$recordModel->get('grossadvances');
                $db->pquery("UPDATE vtiger_refillapplication SET actualtotalrecharge=?,grossadvances=? WHERE refillapplicationid=?",array($difftemp,$difftemp,$recordid));
                $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'actualtotalrecharge','oldValue'=>$actualtotalrecharge,'currentValue'=>$difftemp.'红冲'));
                $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'grossadvances','oldValue'=>$grossadvances,'currentValue'=>$difftemp.'红冲'));
                $accountRecordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
                $accountRecordModel->setAdvancesmoney($recordModel->get('accountid'),$mrefundamount*-1,'回款红冲');
            }
            $db->pquery("UPDATE vtiger_rechargesheet SET refundamount=if((refundamount+{$mrefundamount})>transferamount,transferamount,(refundamount+{$mrefundamount})) WHERE rechargesheetid=?",array($rechargesheetid));
            $array=array(
                $rechargesheetid,
                $recordid,
                $mprestoreadrate,
                $mrechargeamount,
                $mfactorage,
                $mactivationfee,
                $mtaxation,
                $mrefundamount
            );
            $db->pquery('INSERT vtiger_rubricrechargesheet(rechargesheetid,refillapplicationid,prestoreadrate,rechargeamount,factorage,activationfee,taxation,refundamount) VALUES(?,?,?,?,?,?,?,?)',$array);
            $return=array('flag'=>true);
            $db->pquery("UPDATE vtiger_refillapplication SET isbackwash=0 WHERE refillapplicationid=?", array($recordid));
        }while(0);
        $response=new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }
    /**
     * @param Vtiger_Request $request
     * 获取回款匹配列表
     */
    public function addNewReffRayment(Vtiger_Request $request){
        $record=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'RefillApplication');
        $totalrecharge=$recordModel->get('totalrecharge');
        $actualtotalrecharge=$recordModel->get('actualtotalrecharge');
        $data=array('flag'=>false,'msg'=>'没有可匹配的金额!');
        $amountAvailable=$actualtotalrecharge-$totalrecharge;
        if($amountAvailable>0){
            $receivedstatus="receivedstatus in('virtualrefund','normal')";
            $data['dataresult']=$this->getReceivedPaymentsData($recordModel->get('servicecontractsid'),$receivedstatus);
            $data['totalrecharge']=$amountAvailable;
            $data['flag']=true;
            $data['msg']='';
        }

        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * 执行回款的匹配
     */
    public function doAddNewReffRayment(Vtiger_Request $request){
        $raymentid=$request->get('raymentid');
        $record=$request->get('record');
        $sendNum=$request->get('sendNum');
        $data=array('flag'=>false);
        do{
            if($_SESSION['doAddNewReffRayment'.$raymentid.$record]==123456789 && $sendNum!=2){
                $data['msg']='重复提交请过一段时间再试!';
                break;
            }
            $recordModel=Vtiger_Record_Model::getInstanceById($record,'RefillApplication');
            $data['refillapplicationno']=$recordModel->get('refillapplicationno');
            $modulestatus=$recordModel->get('modulestatus');
            if($modulestatus!='c_complete'){
                $data['msg']="只有状态是完成的申请单才能操作";
                break;
            }
            global $current_user;
            /*if($current_user->id!=$recordModel->get('assigned_user_id')){
                $data['msg']="只有提单人才能执行操作!";
                break;
            }*/
            $totalrecharge=$recordModel->get('totalrecharge');//充值现金
            $actualtotalrecharge=$recordModel->get('actualtotalrecharge');//实际充值总额
            $refillapptotal=$request->get('refillapptotal');//匹配回款金额
            if($refillapptotal<=0){
                $data['msg']='无效的匹配金额!';
                break;
            }
            $recePaymentsRecordModel=Vtiger_Record_Model::getInstanceById($raymentid,'ReceivedPayments',true);
            $receRechargeableamount=$recePaymentsRecordModel->get('rechargeableamount');//可充值金额

            if(bccomp($refillapptotal,$receRechargeableamount)>0){
                $data['msg']='可以匹配的金额大于可用充值金额!';
                break;
            }

            $rremarks=$request->get('rremarks');
            $amountAvailable=$actualtotalrecharge-$totalrecharge;
            if(bccomp($refillapptotal,$amountAvailable)>0){
                $data['msg']='可以匹配的金额不一致!';
                break;
            }
            if($sendNum!=2){
                $_SESSION['doAddNewReffRayment'.$raymentid.$record]=123456789;
            }
            $tarray[]=$recordModel->get('servicecontractsid');
            $tarray[]=$raymentid;
            $tarray[]=$refillapptotal;
            $tarray[]=$rremarks;
            $tarray[]=$record;
            $tarray[]=$refillapptotal;
            $tarray[]=date("Y-m-d");
            $tarray[]=date('Y-m-d H:i:s');
            $tarray[]=$raymentid;
            $db=PearDatabase::getInstance();
            $sql="INSERT INTO vtiger_refillapprayment
                  (`servicecontractsid`,`receivedpaymentsid`,`total`,`arrivaldate`,`refillapptotal`,`allowrefillapptotal`,`remarks`,`refillapplicationid`,`paytitle`,`backwashtotal`,`owncompany`,`matchdate`,createdtime,receivedstatus) 
                  SELECT ?,?,unit_price,reality_date,?,`rechargeableamount`,?,?,`paytitle`,?,`owncompany`,?,?,'normal' FROM vtiger_receivedpayments WHERE receivedpaymentsid=?";
            //做记录
            $db->pquery($sql,$tarray);
            //消回款可充值金额
            $currentValue=($receRechargeableamount-$refillapptotal)>0?($receRechargeableamount-$refillapptotal):0;
            $refillapplicationno=$recordModel->get('refillapplicationno');
            $this->setTracker('ReceivedPayments',$raymentid,array('fieldName'=>'rechargeableamount','oldValue'=>$receRechargeableamount,'currentValue'=>$currentValue.'('.$refillapplicationno.':充值回款匹配)'));
            $db->pquery("UPDATE `vtiger_receivedpayments` SET rechargeableamount=if((rechargeableamount-{$refillapptotal})>0,(rechargeableamount-{$refillapptotal}),0) WHERE receivedpaymentsid=?",array($raymentid));
            //修改申请单上数据
            $grossadvances=$recordModel->get('grossadvances');
            $currentValue=($grossadvances-$refillapptotal)>0?($grossadvances-$refillapptotal):0;
            $this->setTracker('RefillApplication',$record,array('fieldName'=>'totalrecharge','oldValue'=>$totalrecharge,'currentValue'=>($totalrecharge+$refillapptotal).'回款匹配'));
            $this->setTracker('RefillApplication',$record,array('fieldName'=>'grossadvances','oldValue'=>$grossadvances,'currentValue'=>$currentValue.'回款匹配'));
            $db->pquery("UPDATE vtiger_refillapplication SET totalrecharge=totalrecharge+? WHERE refillapplicationid=?",array($refillapptotal,$record));
            $db->pquery("UPDATE vtiger_refillapplication SET grossadvances={$currentValue} WHERE refillapplicationid=?",array($record));
            $db->pquery("UPDATE vtiger_refillapplication SET iscushion=if(grossadvances=0,0,1) WHERE refillapplicationid=?",array($record));
            //消客户上的垫付款金额
            $accountRecordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
            $accountRecordModel->setAdvancesmoney($recordModel->get('accountid'),$refillapptotal*-1,'('.$refillapplicationno.'回款匹配回充');
            //$db->pquery("UPDATE vtiger_account SET advancesmoney=(IFNULL(advancesmoney,0)-?) WHERE accountid=?",array($refillapptotal,$recordModel->get('accountid')));
            $data['msg']='成功';
            $data['flag']=true;
        }while(0);
        if($sendNum!=2){
            $response=new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
        }else{
            return $data;
        }
    }

    /**
     * 设置默认担保信息
     * @param Vtiger_Request $request
     */
    public function setChargeGuarantee(Vtiger_Request $request){
        $userid = $request->get("userid");
        $unitprice = $request->get("unitprice");
        $twoleveluserid = $request->get("twoleveluserid");
        $twounitprice = $request->get("twounitprice");
        $threeleveluserid = $request->get("threeleveluserid");
        $threeunitprice = $request->get("threeunitprice");
        $department = $request->get("department");
        $domodule = $request->get("domodule");
        $data = array('flag'=>'0', 'msg'=>'添加失败');

        do {
            $moduleModel = Vtiger_Module_Model::getInstance('RefillApplication');
            if(!$moduleModel->exportGrouprt('RefillApplication','rechargeguarantee')){   //权限验证
                break;
            }
            if(!$moduleModel->exportGrouprt('RefillApplication','dorechargeguarantee')){   //权限验证
                break;
            }
            if (empty($userid)) {
                break;
            }
            if (empty($unitprice) || $unitprice<=0) {
                break;
            }

            $sql2="INSERT INTO `vtiger_rechargeguarantee` (`userid`, department,`unitprice`,twoleveluserid,twounitprice,threeleveluserid,threeunitprice, `createdid`, `createdate`,domodule) VALUES (?,?,?,?,?,?,?,?,?,?)";
            $sql1="UPDATE vtiger_rechargeguarantee SET deleted=1,deleteddate=?,deletedid=? WHERE department=?";
            global $current_user;
            $db=PearDatabase::getInstance();
            $db->pquery($sql1, array(date('Y-m-d H:i:s'), $current_user->id,$department ));
            $db->pquery($sql2, array($userid,$department, $unitprice,$twoleveluserid,$twounitprice,$threeleveluserid,$threeunitprice, $current_user->id, date('Y-m-d H:i:s'),$domodule));
            $data = array('flag'=>'1', 'msg'=>'添加成功');
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 删除默认担保信息
     * @param Vtiger_Request $request
     */
    public function delChargeGuarantee(Vtiger_Request $request){
        $id = $request->get("id");
        $data = array('flag'=>'0', 'msg'=>'添加失败');
        do {
            $moduleModel = Vtiger_Module_Model::getInstance('RefillApplication');
            if(!$moduleModel->exportGrouprt('RefillApplication','rechargeguarantee')){   //权限验证
                break;
            }
            if(!$moduleModel->exportGrouprt('RefillApplication','dorechargeguarantee')){   //权限验证
                break;
            }
            if (empty($id)) {
                break;
            }

            $sql1="UPDATE vtiger_rechargeguarantee SET deleted=1,deleteddate=?,deletedid=? WHERE rechargeguaranteeid=?";
            global $current_user;
            $db=PearDatabase::getInstance();
            $db->pquery($sql1, array(date('Y-m-d H:i:s'), $current_user->id,$id));
            $data = array('flag'=>'1', 'msg'=>'添加成功');
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 设置客户担保信息
     * @param Vtiger_Request $request
     */
    public function setAccountChargeGuarantee(Vtiger_Request $request){
        $userid = $request->get("userid");
        $unitprice = $request->get("unitprice");
        $accountid = $request->get("accountid");
        $twoleveluserid = $request->get("twoleveluserid");
        $twounitprice = $request->get("twounitprice");
        $threeleveluserid = $request->get("threeleveluserid");
        $threeunitprice = $request->get("threeunitprice");
        $data = array('flag'=>'0', 'msg'=>'添加失败');

        do {
            $moduleModel = Vtiger_Module_Model::getInstance('RefillApplication');
            if(!$moduleModel->exportGrouprt('RefillApplication','rechargeguarantee')){   //权限验证
                break;
            }
            if(!$moduleModel->exportGrouprt('RefillApplication','dorechargeguarantee')){   //权限验证
                break;
            }
            if (empty($userid)) {
                break;
            }
            if (empty($unitprice) || $unitprice<=0) {
                break;
            }
            if ($accountid<=0) {
                break;
            }

            $sql2="INSERT INTO `vtiger_accountrechargeguarantee` (`userid`,`unitprice`,twoleveluserid,twounitprice,threeleveluserid,threeunitprice,accountid, `createdid`, `createdate`) VALUES ( ?,?,?,?,?,?,?,?,?)";
            $sql1="UPDATE vtiger_accountrechargeguarantee SET deleted=1,deleteddate=?,deletedid=? WHERE accountid=? and deleted=0";
            global $current_user;
            $db=PearDatabase::getInstance();
            $db->pquery($sql1, array(date('Y-m-d H:i:s'), $current_user->id,$accountid));
            $db->pquery($sql2, array($userid,$unitprice,$twoleveluserid,$twounitprice,$threeleveluserid,$threeunitprice,$accountid, $current_user->id, date('Y-m-d H:i:s')));
            $data = array('flag'=>'1', 'msg'=>'添加成功');
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 删除客户担保信息
     * @param Vtiger_Request $request
     *
     *
     */
    public function delAccountChargeGuarantee(Vtiger_Request $request){
        $id = $request->get("id");
        $data = array('flag'=>'0', 'msg'=>'添加失败');
        do {
            $moduleModel = Vtiger_Module_Model::getInstance('RefillApplication');
            if(!$moduleModel->exportGrouprt('RefillApplication','rechargeguarantee')){   //权限验证
                break;
            }
            if(!$moduleModel->exportGrouprt('RefillApplication','dorechargeguarantee')){   //权限验证
                break;
            }
            if (empty($id)) {
                break;
            }

            $sql1="UPDATE vtiger_accountrechargeguarantee SET deleted=1,deleteddate=?,deletedid=? WHERE accountrechargeguaranteeid=?";
            global $current_user;
            $db=PearDatabase::getInstance();
            $db->pquery($sql1, array(date('Y-m-d H:i:s'), $current_user->id,$id));
            $data = array('flag'=>'1', 'msg'=>'添加成功');
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 获取充值明细列表
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function getDetailList(Vtiger_Request $request){
        $record=$request->get('record');
        $listView=new RefillApplication_List_View();
        $listViewModel=Vtiger_ListView_Model::getInstance('RefillApplication');
        $query=$listViewModel->getQuery();
        global $adb;
        // 如果是rechargesource=contractChanges则 执行下面拼接sql 因为合同变更申请和vtiger_rechargesheet 无关 所以无需加上 AND vtiger_rechargesheet.isentity=0 AND vtiger_rechargesheet.deleted=0 （加上就查不出数据了）
        if($request->get('rechargesource')=='contractChanges'){
            $query.=' AND vtiger_refillapplication.refillapplicationid IN( SELECT vtiger_changecontract_detail.detail_refillapplicationid  FROM  vtiger_changecontract_detail WHERE  vtiger_changecontract_detail.refillapplicationid=? )';
        }else{
            $query.=' AND vtiger_rechargesheet.isentity=0 AND vtiger_rechargesheet.deleted=0 AND vtiger_refillapplication.refillapplicationid=?';
        }
        $result=$adb->pquery($query,array($record));
        if($adb->num_rows($result)){
            $listViewRecordModels=array();
            while($rawData=$adb->fetch_array($result)){
                $rawData['id'] = $record;
                $listViewRecordModels[] = $rawData;
            }
            $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
            $listViewHeaders = $listViewModel->getListViewHeaders();
            $temp = array();
            if(!empty($LISTVIEW_FIELDS)){
                foreach($LISTVIEW_FIELDS as $key=>$val){
                    if(isset($listViewHeaders[$key])){
                        $temp[$key]=$listViewHeaders[$key];
                    }
                }
            }
            if(empty($temp)){
                $temp = $listViewHeaders;
            }
            $viewer=$listView->getViewer($request);
            $viewer->assign('RECHARGESOURCE',$request->get('rechargesource'));
            $viewer->assign('LISTVIEW_HEADERS',$temp);
            $viewer->assign('LISTVIEW_ENTRIES',$listViewRecordModels);
            $viewer->view('ListViewSubDetialContents.tpl', 'RefillApplication');
        }
    }

    /**
     * 获取工单的可以充值明细的成本
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function getSalesorderRelalist(Vtiger_Request $request){
        $salesorderid=$request->get('record');
        $salesoderRecordModel=Vtiger_Record_Model::getInstanceById($salesorderid,'SalesOrder');
        $servicecontractsid=$salesoderRecordModel->get("servicecontractsid");
        $servicecontractRecordModel=Vtiger_Record_Model::getInstanceById($servicecontractsid,'ServiceContracts');
        $contract_no=$servicecontractRecordModel->get('contract_no');
        $sc_related_to=$servicecontractRecordModel->get('sc_related_to');
        $occupationamount=$salesoderRecordModel->get('occupationamount');
        $accountRecordModel=Vtiger_Record_Model::getInstanceById($sc_related_to,'Accounts');
        global $adb;
        $result=$adb->pquery('SELECT sum(costing) AS costings,sum(vtiger_salesorderproductsrel.purchasemount) as purchasemount FROM `vtiger_salesorderproductsrel` WHERE multistatus=3 AND salesorderid=?',array($salesorderid));
        $costing=array('costings'=>0,'purchasemount'=>0);
        if($adb->num_rows($result)){
            $data=$adb->query_result_rowdata($result);
            $costing['costings']=$data['costings'];
            $costing['purchasemount']=$data['purchasemount'];
        }
        $return=array('accountname'=>$accountRecordModel->get('accountname'),
            'id'=>$sc_related_to,
            'advancesmoney'=>$accountRecordModel->get('advancesmoney'),
            'total'=>$servicecontractRecordModel->get('total'),
            'costing'=>$costing,
            'contract_no'=>$contract_no,
            'servicecontractsid'=>$servicecontractsid,
            'occupationamount'=>$occupationamount,
        );
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 作废处理
     */
    public function docancel(Vtiger_Request $request){
        $record=$request->get('record');
        $voidreason=$request->get('voidreason');
        $recordModle=Vtiger_Record_Model::getInstanceById($record,'RefillApplication');
        $modulestatus=$recordModle->get('modulestatus');
        if($modulestatus=='a_exception'){
            $moduleModel=Vtiger_Module_Model::getInstance('RefillApplication');
            if($moduleModel->exportGrouprt('RefillApplication','docancel')){
                global $adb,$current_user;
                $currentTime = date('Y-m-d H:i:s');
                $adb->pquery("UPDATE vtiger_refillapplication SET modulestatus=?,voidreason=?,voiduserid=?,voiddatetime=? WHERE refillapplicationid=?",array('c_cancel',$voidreason,$current_user->id,$currentTime,$record));
            }
        }
    }

    /**
     * 添加更新记录
     * @param $sourceModule
     * @param $sourceId
     * @param $array
     * @param string $table
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function setTracker($sourceModule, $sourceId, $array,$table='') {
        global $adb, $current_user;
        $currentTime = date('Y-m-d H:i:s');
        if(!empty($table)){
            $sql = "SELECT * FROM {$table['tablename']} WHERE {$table['fieldName']}=? LIMIT 1";
            $sel_result = $adb->pquery($sql, array($sourceId));
            if($adb->num_rows($sel_result)){
                $row = $adb->query_result_rowdata($sel_result, 0);
                $array['oldValue']=$row[$array['fieldName']];
            }
        }
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $sourceId, $sourceModule, $current_user->id, $currentTime, 0));
        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
            Array($id, $array['fieldName'], $array['oldValue'], $array['currentValue']));
    }
    /**
     * 取得可用的充值金额
     * @param Vtiger_Request $request
     */
    public function getSaleSorderPayments(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $data=$recordModel->getSalesorderPayments($request);
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 提交红冲作废申请
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function submitRefund(Vtiger_Request $request){
        return ;
        $recordId=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,"RefillApplication");

        global $current_user;
        if($recordModel->get('modulestatus')=='c_complete' && $recordModel->get('assigned_user_id')==$current_user->id && $recordModel->get('isbackwash')==0) {
            $_REQUEST['workflowsid']=$this->refundworkflowsid;

            $focus = CRMEntity::getInstance('RefillApplication');
            $focus->makeWorkflows('RefillApplication', $_REQUEST['workflowsid'], $recordId);
            $departmentid=empty($current_user->departmentid)?'H1':$current_user->departmentid;
            $result=$focus->db->pquery("SELECT vtiger_auditsettings.oneaudituid FROM `vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid=?) AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='RefillApplication' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1",array($departmentid));
            $data=$focus->db->query_result_rowdata($result,0);
            $focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='RefillApplication'",array($data['oneaudituid'],$recordId));
            $focus->db->pquery("UPDATE vtiger_refillapplication SET modulestatus='b_actioning' WHERE refillapplicationid=?",array($recordId));
        }
    }

    /**
     * 回款成本的使用明细
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function getReceivedPaymentsHistory(Vtiger_Request $request){
        //$recordId=$request->get('record');
        /*global $adb;
        $query='SELECT * FROM `vtiger_refillapprayment` WHERE receivedpaymentsid=? AND deleted=0 order by refillappraymentid desc';
        $result=$adb->pquery($query,array($recordId));
        $data=array();
        while($row=$adb->fetch_array($result)){
            $data[]=$row;
        }*/
        $receivedPaymentsRecordModel=Vtiger_Record_Model::getCleanInstance('ReceivedPayments');
        $data=$receivedPaymentsRecordModel->getReceivedPaymentsUseDetail($request);
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    /**
     * 充值金额与采购合同金额比较
     * @param Vtiger_Request $request
     * @return array
     * @author: steel.liu
     * @Date:xxx
     *
     **/
    public function AmountRepaidContract(Vtiger_Request $request){
        $data=$request->get('data');
        $recordid=$request->get('record');
        $rechargesource=$data['rechargesource'];
        if($rechargesource=='Vendors'){
            $mservicecost='mservicecost[';
            $transferamount=$data['servicecost'];
        }elseif($rechargesource=='PreRecharge'){
            $mservicecost='mrechargeamount[';
            $transferamount=$data['rechargeamount'];
        }elseif($rechargesource=='NonMediaExtraction'){
            $mservicecost='mpurchaseamount[';
            $transferamount=$data['purchaseamount'];
        }
        $suppliercontractsid=$data['suppliercontractsid'];
        $array=array($suppliercontractsid=>$transferamount);
        $temparray=array($suppliercontractsid);
        $msuppliercontractsid=$request->get('msuppliercontractsid');

        foreach($msuppliercontractsid as $key=>$value){
            $tempkey=$key+1;
            $mtransferamount=$data[$mservicecost.$tempkey];
            if(in_array($value,$temparray)){
                $array[$value]=$array[$value]+$mtransferamount;
            }else{
                $array[$value]=$mtransferamount;
            }
        }
        $RecordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $data=$RecordModel->AmountRepaidContract($recordid,$array);
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    /**
     * 验证红冲的金额是否有问题
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function checkRefundsOrTransfers(Vtiger_Request $request,$flag=1){
        global $adb;
        $recordid=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'RefillApplication',true);
        $rechargesource=$recordModel->get('rechargesource');
        $actualtotalrecharge=$recordModel->get('actualtotalrecharge');//原实际充值金额
        $totalrecharge=$recordModel->get('totalrecharge');//原现金金额
        $data=$request->get('data');
        $updaterefillprayment=$request->get('updaterefillprayment');
        $refundamount=$request->get('refundamount');//提红冲金额(回款)
        $refundAmountSum=array_sum($refundamount);//提红冲对应的现金款额(回款)
        $currentRefund=$actualtotalrecharge-$totalrecharge;//垫款额
        $mrefundamount=$data['mrefundamount'];//提红冲的应付款额
        $refenddiff=$mrefundamount-$refundAmountSum;//红冲差额
        $repaymenttotal=$request->get('repaymenttotal');//提供应商退款
        $updaterepayment=$request->get('updaterepayment');//提供应商退款ID
        $repaymenttotalSum=array_sum($repaymenttotal);//提供应商退款合计
        $remark=$data['mstatus'];//提备注
        $amountpayable=$data['amountpayable']>0?$data['amountpayable']:0;//应付款金额
        $financialstate=$recordModel->get('financialstate');//手动销账
        $grossadvances=$recordModel->get('grossadvances');//手动销账
        //print_r($request);
        $return=array('flag'=>false);
        do {
            if($rechargesource=='Vendors'){
                //红冲金额必需和供应商金额一致
                /*if(bccomp($repaymenttotalSum,$mrefundamount)!=0){
                    $return['msg']='红冲金额不等!';
                    break;
                }*/

            }
            /*if (($mrefundamount - $refundAmountSum) != 0) {
                $return['msg']='退款金额不等!';
                break;
            }*/
            if(bccomp($grossadvances,0,2)==0 && $financialstate!=1){
                if(bccomp($mrefundamount,$refundAmountSum,2)!=0){
                    $return['msg']='无垫款,红冲金额与红冲回垫一致!';
                    break;
                }
            }
            //未提交的红冲金额
            $result=$adb->pquery('SELECT IFNULL(sum(refundamount),0) as refundamount FROM `vtiger_rubricrechargesheet` WHERE isbackwash=1 AND deleted=0 AND refillapplicationid=?',array($recordid));
            $dataResult=$adb->raw_query_result_rowdata($result,0);
            $nosubmitrefundamount=$dataResult['refundamount'];
            //未提交回款金额
            $result=$adb->pquery('SELECT IFNULL(sum(backwashtotal),0) AS backwashtotal FROM `vtiger_refillredrefund` WHERE deleted=0 AND mstatus=\'normal\' AND refillapplicationid=?',array($recordid));
            $dataResult=$adb->raw_query_result_rowdata($result,0);
            $backwashtotal=$dataResult['backwashtotal'];
            //未提交的红冲金额
            $noCurrentRefund=bcsub($nosubmitrefundamount,$backwashtotal,2);
            $noCurrentRefund=bcadd($noCurrentRefund,$refenddiff,2);
            if($financialstate==1){
                $query="SELECT sum(backwashtotal) AS backwashtotal FROM `vtiger_refillapprayment` WHERE refillapplicationid=? AND receivedstatus='normal' AND deleted=0";
                $result=$adb->pquery($query,array($recordid));
                $praymentdataResult=$adb->raw_query_result_rowdata($result,0);
                $praymentbackwashtotal=$praymentdataResult['backwashtotal'];
                $fbackwashtotal=bcsub($praymentbackwashtotal,$backwashtotal,2);
                $query='SELECT sum(transferamount-refundamount) AS diffamount FROM `vtiger_rechargesheet` WHERE refillapplicationid=? AND deleted=0';
                $result=$adb->pquery($query,array($recordid));//可红冲金额总合
                $praymentdataResult=$adb->raw_query_result_rowdata($result,0);
                $diffamount=$praymentdataResult['diffamount'];
                $diffamount=bcsub($diffamount,$nosubmitrefundamount,2);
                $reddiffaoumt=bcsub($diffamount,$fbackwashtotal,2);
                if($reddiffaoumt<0){
                    $return['msg']='红冲垫款金额大于实际垫款金额!';
                    break;
                }
                if(bccomp($refenddiff,$reddiffaoumt,3)>0){
                    $return['msg']='红冲垫款金额大于实际垫款金额!';
                    break;
                }
                $return=array('flag'=>true,'msg'=>'');
                break;
            }
            if(bccomp($currentRefund,$noCurrentRefund,2)==-1 && $financialstate!=1){
                $return['msg']='红冲垫款金额大于实际垫款金额!';
                break;
            }
            if(bccomp($actualtotalrecharge,$refundAmountSum)<0){
                //退款金额大于实际金额
                $return['msg']='退款或退货有问题!,请重新修改!';
                break;
            }
            if(bccomp($mrefundamount,$refundAmountSum)<0){
                //一般不会出现
                $return['msg']='退款或退货有问题!,请重新修改!';
                break;
            }
            //主要处理这个
            if(bccomp($mrefundamount,$refundAmountSum)>0 && $financialstate!=1){
                //红冲的应付款额>红冲对应的现金额
                if(bccomp($refenddiff,$currentRefund)>0){
                    $return['msg']='红冲退款金额减去退款现金总额不可大于充值单合计垫款额!,请重新修改!';
                    break;
                }
            }
            $return=array('flag'=>true);
        }while(0);
        if($flag==1){
            $response=new Vtiger_Response();
            $response->setResult($return);
            $response->emit();
        }else{
            return $return;
        }
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function checkTechprocurement(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $return=$recordModel->checkTechprocurement($request);
        $response=new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }
    /**
     * 获取充值单列表
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function getVendorList(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $data=$recordModel->getVendorList($request);
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 验证充值单几级担保
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function setAuditInformation(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $accountid=$request->get('accountid');
        $advancesmoney=$request->get('advancesmoney');
        $returnData=$recordModel->checkAuditInformation($accountid,$advancesmoney);
        $response=new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }

    /**
     * 撤销回款关联匹配申请
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function revokeRelation(Vtiger_Request $request){
        global $adb,$current_user;
        $recordid=$request->get('record');
        $refillappraymentid=$request->get('refillappraymentid');
        $flag=false;
        try{
            $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'RefillApplication');
        }catch(Exception $e){
            $flag=true;
        }

        $returnData=array('flag'=>false);
        do{
            if($recordModel->get("receivedstatus")=="virtualrefund"){
                $returnData=array('flag'=>true,'msg'=>'赠款申请不允许撤销！');
                break;
            }
            if($flag){
                $returnData=array('flag'=>true,'msg'=>'记录有误！');
                break;
            }
            if($recordModel->get('assigned_user_id')!=$current_user->id){
                $returnData=array('flag'=>true,'msg'=>'没有权限操作！');
                break;
            }
            $modulestatus=$recordModel->get('modulestatus');
            if($modulestatus!='c_complete'){
                $returnData=array('flag'=>true,'msg'=>'只有完成状态的充值单，才能进行此操作！');
                break;
            }

            $query="SELECT * FROM `vtiger_refillapprayment` WHERE deleted=0 AND refillappraymentid=? AND refillapplicationid=? limit 1";
            $result=$adb->pquery($query,array($refillappraymentid,$recordid));
            if($adb->num_rows($result)==0){
                $returnData=array('flag'=>true,'msg'=>'没有找到相关记录！');
                break;
            }
            $data=$adb->query_result_rowdata($result,0);
            $backwashtotal=$data['backwashtotal'];
            $refillapptotal=$data['refillapptotal'];
            /*$rechargesource=$recordModel->get('modulestatus');
            if(!($rechargesource=="Accounts" || $rechargesource=="Vendors")){
                if(bccomp($backwashtotal,$refillapptotal,2)!=0){
                    $returnData=array('flag'=>true,'msg'=>'已做过退款或红冲的,不允许撤销！');
                    break;
                }
            }*/
            if($backwashtotal==0){
                $returnData=array('flag'=>true,'msg'=>'无可用的金额！');
                break;
            }
            $receivedpaymentsid=$data['receivedpaymentsid'];
            $flag=false;
            try{
                Vtiger_Record_Model::getInstanceById($receivedpaymentsid,'ReceivedPayments');
            }catch(Exception $e){
                $flag=true;
            }
            if($flag){
                $returnData=array('flag'=>true,'msg'=>'回款记录有误！');
                break;
            }
            $rechargesource=$recordModel->get('rechargesource');
            if($rechargesource=='Vendors' || $rechargesource=='NonMediaExtraction') {
                $_REQUEST['workflowsid'] = 2153164;
            }else{
                $_REQUEST['workflowsid'] = 2153160;
            }
            $adb->pquery('DELETE FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.salesorderid=?',array($_REQUEST['workflowsid'],$recordid));
            $focus = CRMEntity::getInstance('RefillApplication');
            $focus->makeWorkflows('RefillApplication', $_REQUEST['workflowsid'], $recordid);
            $adb->pquery("UPDATE vtiger_refillapplication SET modulestatus='b_actioning' WHERE refillapplicationid=?",array($recordid));
            $Sql="UPDATE `vtiger_refillapprayment` SET receivedstatus='revokerelation' WHERE refillappraymentid=? limit 1";
            $adb->pquery($Sql,array($refillappraymentid));
            // 撤销回款后提醒第一节点审核人
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$recordid,'salesorderworkflowstagesid'=>0));
        }while(0);
        $response=new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }

    public function relationPaymentsData(Vtiger_Request $request){
        $moduleModel=Vtiger_Module_Model::getInstance('RefillApplication');
        $moduleModel->relationPayments($request);
    }
    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 自定义导出
     */
    public function exportdata(Vtiger_Request $request){
        set_time_limit(0);
        global $site_URL,$current_user,$root_directory,$adb;
        if(isset($_POST['rechargesource'])){
            $_GET['rechargesource']=$_POST['rechargesource'];
        }
        $listViewModel = Vtiger_ListView_Model::getInstance("RefillApplication");
        $listViewModel->getSearchWhere();
        $query = $listViewModel->getQuery();
        $query=str_replace('vtiger_refillapplication.refillapplicationid FROM',
            'vtiger_rechargesheet.rechargesheetid,vtiger_refillapprayment.refillappraymentid,vtiger_refillapprayment.paytitle,vtiger_refillapprayment.total,vtiger_refillapprayment.arrivaldate,vtiger_refillapprayment.refillapptotal,vtiger_refillapprayment.refundamount,
                    (SELECT sum(vtiger_rubricrechargesheet.prestoreadrate) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS rprestoreadrate,
                    (SELECT sum(vtiger_rubricrechargesheet.refundamount) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redrefundamount,
                    (SELECT sum(vtiger_rubricrechargesheet.activationfee) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redactivationfee,
                    (SELECT sum(vtiger_rubricrechargesheet.taxation) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redtaxation,
                    (SELECT sum(vtiger_rubricrechargesheet.factorage) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redfactorage,
                    (SELECT sum(vtiger_rubricrechargesheet.amountpayable) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redamountpayable,
                    vtiger_refillapplication.refillapplicationid FROM',$query);
        $query=str_replace('WHERE 1=1',"LEFT JOIN vtiger_refillapprayment ON (vtiger_refillapprayment.refillapplicationid=vtiger_refillapplication.refillapplicationid AND vtiger_refillapprayment.deleted=0 AND vtiger_refillapprayment.receivedstatus in('normal','virtualrefund')) WHERE 1=1",$query);
        $query .= ' AND vtiger_rechargesheet.deleted=0';
        $query.=$listViewModel->getUserWhere();
        $query.=' order by vtiger_refillapplication.refillapplicationid';
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)) {
            $temp = $listViewHeaders;
        }
        $arr=array("paytitle" => Array("tabid"=> 148,"columnname" => "paytitle","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "paytitle","fieldlabel" => "paytitle"),
            "total" => Array("tabid"=> 148,"columnname" => "total","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "total","fieldlabel" => "total"),
            "arrivaldate" => Array("tabid"=> 148,"columnname" => "arrivaldate","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "arrivaldate","fieldlabel" => "arrivaldate"),
            "refillapptotal" => Array("tabid"=> 148,"columnname" => "refillapptotal","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "refillapptotal","fieldlabel" => "refillapptotal"),
           // "refundamount" => Array("tabid"=> 148,"columnname" => "refundamount","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "refundamount","fieldlabel" => "refundamount"),
            "rprestoreadrate" => Array("tabid"=> 148,"columnname" => "rprestoreadrate","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "rprestoreadrate","fieldlabel" => "rprestoreadrate"),
            "redrefundamount" => Array("tabid"=> 148,"columnname" => "redrefundamount","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redrefundamount","fieldlabel" => "redrefundamount"),
            "redtaxation" => Array("tabid"=> 148,"columnname" => "redtaxation","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redtaxation","fieldlabel" => "redtaxation"),
            "redactivationfee" => Array("tabid"=> 148,"columnname" => "redactivationfee","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redactivationfee","fieldlabel" => "redactivationfee"),
            "redfactorage" => Array("tabid"=> 148,"columnname" => "redfactorage","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redfactorage","fieldlabel" => "redfactorage"),
            "redamountpayable" => Array("tabid"=> 148,"columnname" => "redamountpayable","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redamountpayable","fieldlabel" => "redamountpayable"),
        );
        $headerArray=array_merge($temp,$arr);
        ini_set('memory_limit','512M');
        $path=$root_directory.'temp/';
        $filename=$path.'refillapplition'.$current_user->id.'.csv';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $array= array();
        foreach($headerArray as $key=>$value){
            $array[]=iconv('utf-8','gb2312',vtranslate($key,'RefillApplication'));
        }
        $fp=fopen($filename,'w');
        fputcsv($fp,$array);
        if(isset($_POST['rechargesource'])) {
            $flag=true;
        }else{
            $flag=false;
        }
        $limitStep=500;
        $i=0;
        while(1){
            $limitSQL=" limit ".$i*$limitStep.",".$limitStep;
            $result = $adb->pquery($query . $limitSQL, array());
            if($adb->num_rows($result) && ($flag || $i<20)){
                while ($value = $adb->fetch_array($result)) {
                    $array = array();
                    foreach ($headerArray as $keyheader => $valueheader) {
                        if ($valueheader['uitype'] == 10) {
                            $currnetValue = uitypeformat($valueheader, $value, 'RefillApplication');
                            $pattern = '/<[^>]+>/';
                            $currnetValue = preg_replace($pattern, '', $currnetValue);
                        } elseif ($valueheader['uitype'] == 15) {
                            $currnetValue = vtranslate($value[$keyheader], 'RefillApplication');
                            $pattern = '/<[^>]+>/';
                            $currnetValue = preg_replace($pattern, '', $currnetValue);
                        } elseif($valueheader['uitype'] == 19) {
                            $currnetValue=$value[$keyheader];
                        }else {
                            $currnetValue = uitypeformat($valueheader, $value, 'RefillApplication');
                        }
                        if(in_array($valueheader['fieldname'],array('did','modifiedtime','createdtime','workflowstime','banknumber','voiddatetime','rechargefinishtime','expecteddatepayment','expectedpaymentdeadline','newservicesigndate','expcashadvances','servicesigndate','arrivaldate','signdate'))){
                            $currnetValue="\t".$currnetValue."\t";
                        }
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
            $i++;
        }
        fclose($fp);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    // 导出查询数据
    public  function startToContractChangesExport(&$data){
        global $root_directory,$current_user;
        $db=PearDatabase::getInstance();
        set_time_limit(0);
        $strRefillapplicationId="";
        foreach ($data as $key=>$val){
            $strRefillapplicationId.=",".$val['refillapplicationid'];
        }
        $strRefillapplicationId = trim($strRefillapplicationId,",");
        $sql = " SELECT  r.refillapplicationno as changerefillapplicationno,oldR.refillapplicationno,r.changecontracttype,r.oldcontract_no,r.account_name,r.newcontract_no,r.newaccount_name,r.finishedtime  FROM  vtiger_refillapplication as r
                            LEFT JOIN vtiger_crmentity as c ON r.refillapplicationid = c.crmid
                            LEFT JOIN vtiger_changecontract_detail as cd ON cd.refillapplicationid=r.refillapplicationid
                            LEFT JOIN vtiger_refillapplication as oldR ON oldR.refillapplicationid=cd.detail_refillapplicationid
                            WHERE 1 = 1 AND c.deleted = 0  AND r.rechargesource = 'contractChanges' AND r.refillapplicationid IN(".$strRefillapplicationId.")";
        $result = $db->pquery($sql,array());
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $phpexecl=new PHPExcel();
        // Set document properties
        $phpexecl->getProperties()->setCreator("cxh")
            ->setLastModifiedBy("cxh")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("t cloud reconciliation");
        $phpexecl->getActiveSheet()->mergeCells( 'A1:AM1');
        //添加头信处
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '变更合同申请单号')
            ->setCellValue('B1', '充值申请单号')
            ->setCellValue('C1', '变更类型')
            ->setCellValue('D1', '原合同号')
            ->setCellValue('E1', '原客户/供应商')
            ->setCellValue('F1', '目标合同号')
            ->setCellValue('G1', '目标客户/供应商')
            ->setCellValue('H1', '变更日期');
        //重新排序数组keys 从零开始递增 否则导出数据会有问题

        $array= array();
        $array[]=iconv('utf-8', 'GBK//IGNORE', '变更合同申请单号');
        $array[]=iconv('utf-8', 'GBK//IGNORE', '充值申请单号');
        $array[]=iconv('utf-8', 'GBK//IGNORE', '变更类型');
        $array[]=iconv('utf-8', 'GBK//IGNORE', '原合同号');
        $array[]=iconv('utf-8', 'GBK//IGNORE', '原客户/供应商');
        $array[]=iconv('utf-8', 'GBK//IGNORE', '目标合同号');
        $array[]=iconv('utf-8', 'GBK//IGNORE', '目标客户/供应商');
        $array[]=iconv('utf-8', 'GBK//IGNORE', '变更日期');
        ini_set('memory_limit','512M');
        $path=$root_directory.'temp/';
        $filename=$path.'refillapplition'.$current_user->id.'.csv';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $fp=fopen($filename,'w');
        fputcsv($fp,$array);

        while($rowData=$db->fetch_array($result)){
            $array= array();
            $array[]= iconv('utf-8', 'GBK//IGNORE', $rowData['changerefillapplicationno']);
            $array[]= iconv('utf-8', 'GBK//IGNORE', $rowData['refillapplicationno']);
            $array[]= iconv('utf-8', 'GBK//IGNORE', vtranslate($rowData['changecontracttype'],"RefillApplication"));
            $array[]= iconv('utf-8', 'GBK//IGNORE', $rowData['oldcontract_no']);
            $array[]= iconv('utf-8', 'GBK//IGNORE', $rowData['account_name']);
            $array[]= iconv('utf-8', 'GBK//IGNORE', $rowData['newcontract_no']);
            $array[]= iconv('utf-8', 'GBK//IGNORE', $rowData['newaccount_name']);
            $array[]= iconv('utf-8', 'GBK//IGNORE', $rowData['finishedtime']);
            fputcsv($fp, $array);
        }
        ob_flush();
        flush();
        fclose($fp);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();exit();
    }
    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 自定义导出
     */
    public function exportdatabak(Vtiger_Request $request){
        set_time_limit(0);
        $limit='';
        if(isset($_POST['rechargesource'])){
            $_GET['rechargesource']=$_POST['rechargesource'];
        }else{
            $limit=' limit 20';
        }
        $listViewModel = Vtiger_ListView_Model::getInstance("RefillApplication");
        $listViewModel->getSearchWhere();
        $query = $listViewModel->getQuery();
        $query=str_replace('vtiger_refillapplication.refillapplicationid FROM',
            'vtiger_rechargesheet.rechargesheetid,vtiger_refillapprayment.refillappraymentid,vtiger_refillapprayment.paytitle,vtiger_refillapprayment.total,vtiger_refillapprayment.arrivaldate,vtiger_refillapprayment.refillapptotal,vtiger_refillapprayment.refundamount,
                    (SELECT sum(vtiger_rubricrechargesheet.refundamount) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redrefundamount,
                    (SELECT sum(vtiger_rubricrechargesheet.activationfee) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redactivationfee,
                    (SELECT sum(vtiger_rubricrechargesheet.taxation) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redtaxation,
                    (SELECT sum(vtiger_rubricrechargesheet.factorage) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redfactorage,
                    (SELECT sum(vtiger_rubricrechargesheet.amountpayable) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redamountpayable,
                    vtiger_refillapplication.refillapplicationid FROM',$query);
        $query=str_replace('WHERE 1=1',"LEFT JOIN vtiger_refillapprayment ON (vtiger_refillapprayment.refillapplicationid=vtiger_refillapplication.refillapplicationid AND vtiger_refillapprayment.deleted=0 AND vtiger_refillapprayment.receivedstatus in('normal','virtualrefund')) WHERE 1=1",$query);
        $query .= ' AND vtiger_rechargesheet.deleted=0';
        $query.=$listViewModel->getUserWhere();
        /*$where=getAccessibleUsers('RefillApplication','List',true);
        if($where!='1=1'){
            $query.= ' AND vtiger_crmentity.smownerid in('.implode(',',$where).')';
        }*/
        $query.=' order by vtiger_refillapplication.refillapplicationid';
        $query.=$limit;
        global $adb;
        $result=$adb->pquery($query,array());
        $listViewRecordModels = array();
        while ($rawData = $adb->fetch_array($result)) {
            $listViewRecordModels[] = $rawData;
        }
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)) {
            $temp = $listViewHeaders;
        }
        $arr=array("paytitle" => Array("tabid"=> 148,"columnname" => "paytitle","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "paytitle","fieldlabel" => "paytitle"),
            "total" => Array("tabid"=> 148,"columnname" => "total","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "total","fieldlabel" => "total"),
            "arrivaldate" => Array("tabid"=> 148,"columnname" => "arrivaldate","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "arrivaldate","fieldlabel" => "arrivaldate"),
            "refillapptotal" => Array("tabid"=> 148,"columnname" => "refillapptotal","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "refillapptotal","fieldlabel" => "refillapptotal"),
            "refundamount" => Array("tabid"=> 148,"columnname" => "refundamount","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "refundamount","fieldlabel" => "refundamount"),
            "redrefundamount" => Array("tabid"=> 148,"columnname" => "redrefundamount","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redrefundamount","fieldlabel" => "redrefundamount"),
            "redtaxation" => Array("tabid"=> 148,"columnname" => "redtaxation","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redtaxation","fieldlabel" => "redtaxation"),
            "redactivationfee" => Array("tabid"=> 148,"columnname" => "redactivationfee","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redactivationfee","fieldlabel" => "redactivationfee"),
            "redfactorage" => Array("tabid"=> 148,"columnname" => "redfactorage","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redfactorage","fieldlabel" => "redfactorage"),
            "redamountpayable" => Array("tabid"=> 148,"columnname" => "redamountpayable","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redamountpayable","fieldlabel" => "redamountpayable"),
            );
        $temp=array_merge($temp,$arr);
        //return ;

        global $site_URL,$current_user,$root_directory;
        set_time_limit(0);
        ini_set('memory_limit','2048M');
        $data['name']='充值审请明细导出';
        $path=$root_directory.'temp/';
        $filename=$path.'refillapplition'.$current_user->id.'.xlsx';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $data['data']=array('LISTVIEW_HEADERS'=>$temp,'LISTVIEW_ENTRIES'=>$listViewRecordModels);
        $moduleModel = Vtiger_Module_Model::getInstance("RefillApplication");
        $moduleModel->refillExportDataExcel($data);
        //header('location:'.$site_URL.'temp/refillapplition'.$current_user->id.'.xlsx');
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 修改财务状态
     */
    public function financialstate(Vtiger_Request $request){
        $recordId=$request->get('record');
        $amountofsales=$request->get('amountofsales');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'RefillApplication');
        $data['flag']=false;
        do{
            if(!$recordModel->personalAuthority('RefillApplication','financialstate')){
                $data['msg']='没有权限';
                break;
            }

            if($recordModel->get('modulestatus')!='c_complete'){
                $data['msg']='未完成的状态不允许操作!';
                break;
            }
            $financialstate=$recordModel->get('financialstate');
            $grossadvances=$recordModel->get('grossadvances');
            $iscushion='';
            $refillapplicationno=$recordModel->get('refillapplicationno');
            $currnetamountofsales=$recordModel->get('amountofsales');
            $totalrecharge=$recordModel->get('totalrecharge');//使用回款金额
            if($financialstate==0){
                if(bccomp($amountofsales,0,2)<0){
                    $data['msg']='销账金额，必需大于0';
                    break;
                }
                if(bccomp($grossadvances,0,2)<0){
                    $data['msg']='无可销账金额！';
                    break;
                }
                if(bccomp($amountofsales,$grossadvances,2)>0){
                    $data['msg']='销账金额，必需小于垫款金额！';
                    break;
                }
                $currentgrossadvances=bcsub($grossadvances,$amountofsales,2);
                if(bccomp($currentgrossadvances,0,2)==0){
                    $iscushion='iscushion=0,';
                }
                $amountAvailable=-$amountofsales;
                $totalrecharge=bcadd($totalrecharge,$amountofsales,2);
                $msg = '('.$refillapplicationno.':手动销账(冲))';
            }else{
                $currentgrossadvances=bcadd($grossadvances,$currnetamountofsales,2);
                $amountofsales=0;
                $iscushion='iscushion=1,';
                $amountAvailable=$currnetamountofsales;
                $msg = '('.$refillapplicationno.':手动销账(还))';
                $totalrecharge=bcsub($totalrecharge,$currnetamountofsales,2);
            }
            $Finalfinancialstate=$financialstate==1?0:1;
            $db=PearDatabase::getInstance();
            $db->pquery('UPDATE vtiger_refillapplication SET financialstate=?,'.$iscushion.'grossadvances=?,amountofsales=?,totalrecharge=?,removeaccounttime=? WHERE refillapplicationid=?',array($Finalfinancialstate,$currentgrossadvances,$amountofsales,$totalrecharge,date('Y-m-d H:i:s', time()),$recordId));
            $accountRecordModule = Vtiger_Record_Model::getCleanInstance("Accounts");
            $accountRecordModule->setAdvancesmoney($recordModel->get('accountid'), $amountAvailable, $msg);
            $this->setTracker('RefillApplication',$recordId,array('fieldName'=>'financialstate','oldValue'=>$financialstate.'(手动销账)','currentValue'=>$Finalfinancialstate));
            $this->setTracker('RefillApplication',$recordId,array('fieldName'=>'grossadvances','oldValue'=>$grossadvances.'(手动销账)','currentValue'=>$currentgrossadvances));
            $this->setTracker('RefillApplication',$recordId,array('fieldName'=>'amountofsales','oldValue'=>$currnetamountofsales.'(手动销账)','currentValue'=>$amountofsales));
            $data['flag']=true;
            $data['msg']='财务状态修改成功!';

        }while(0);
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 红冲退款生成审核流
     */
    public function dorefundsOrTransfersconfirm(Vtiger_Request $request){
        $recordid=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'RefillApplication');
        $rechargesource=$recordModel->get('rechargesource');
        $isbackwash=$recordModel->get('isbackwash');//是否红冲
        $return=array('flag'=>false);
        do {
            if($isbackwash!=1){
                $return=array('flag'=>false,'msg'=>'状态错误！');
                break;
            }

            if($recordModel->get('modulestatus')!='c_complete'){
                $return=array('flag'=>false,'msg'=>'状态错误！');
                break;
            }
            //生成工作流
            $db=PearDatabase::getInstance();
            $sequence=1;
            $departmentid=$_SESSION['userdepartmentid'];
            $ispayment=$recordModel->get('ispayment');
            if($rechargesource=='Vendors') {
                $_REQUEST['workflowsid'] = 2131276;
                if($ispayment=='unpaid'){
                    $needle='H283::';
                    $query='SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE departmentid=?';
                    $result=$db->pquery($query,array($departmentid));
                    $data=$db->raw_query_result_rowdata($result,0);
                    $parentdepartment=$data['parentdepartment'];
                    $parentdepartment.='::';
                    $_REQUEST['workflowsid'] = 2199184;
                    if(strpos($parentdepartment,$needle)!==false){
                        $sequence=2;
                    }
                }
            }else{
                $_REQUEST['workflowsid'] = $this->refundworkflowsid;
            }
            $db->pquery('DELETE FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.salesorderid=?',array(2126542,$recordid));
            $db->pquery('DELETE FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.salesorderid=?',array(2131276,$recordid));
            $db->pquery('DELETE FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.salesorderid=?',array(2199184,$recordid));
            global $current_user;
            $focus = CRMEntity::getInstance('RefillApplication');
            $focus->makeWorkflows('RefillApplication', $_REQUEST['workflowsid'], $recordid);
            $data=$focus->getAudituid('RefillApplication',$departmentid);
            $focus->db->pquery("UPDATE vtiger_refillapplication SET modulestatus='b_actioning' WHERE refillapplicationid=?",array($recordid));
            $tempflag=true;
            if($rechargesource=='Vendors') {
                if($ispayment=='unpaid'){
                    $isaction='';
                    if($sequence==2){
                        $focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.workflowsid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='RefillApplication'",array(7629,$recordid,$_REQUEST['workflowsid']));
                    }else{
                        $focus->db->pquery('DELETE FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.sequence=1',array($_REQUEST['workflowsid'],$recordid));
                        $isaction=',isaction=1';
                    }
                    $focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1{$isaction},higherid=? WHERE vtiger_salesorderworkflowstages.sequence=2 AND vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='RefillApplication'",array($data['oneaudituid'],$_REQUEST['workflowsid'],$recordid));
                    $tempflag=false;
                }
            }
            $focus->db->pquery("UPDATE vtiger_rubricrechargesheet SET backwashstatus=0 WHERE refillapplicationid=?",array($recordid));
            if($tempflag) {
                $focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='RefillApplication'", array($data['oneaudituid'], $recordid));
            }
            // 确认红冲后提醒第一节点审核人
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$recordid,'salesorderworkflowstagesid'=>0));
            $return=array('flag'=>true);
        }while(0);
        $response=new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }
    public function matchReceivementsList(Vtiger_Request $request){
        global $current_user,$adb;
        $records=$request->get('records');
        $servicecontractsid=$request->get('servicecontractsid');
        $sendNum=$request->get('sendNum');
        $recordsArr=explode(',',$records);
        $countNum=count($recordsArr);
        $query="SELECT vtiger_refillapplication.refillapplicationno,
                (vtiger_servicecontracts.contract_no) as servicecontractsid,
                vtiger_refillapplication.servicecontractsid as servicecontractsid_reference, 
                (vtiger_account.accountname) as accountid,vtiger_refillapplication.accountid as accountid_reference, 
                vtiger_refillapplication.srcterminal as srcterminal_reference,
                vtiger_refillapplication.customertype,
                IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid,
                vtiger_crmentity.smownerid as smownerid_owner, 
                vtiger_refillapplication.paymentperiod,
                vtiger_refillapplication.ispayment,
                vtiger_refillapplication.banklist,
                vtiger_crmentity.createdtime,
                vtiger_refillapplication.rechargefinishtime,
                IF(vtiger_refillapplication.financialstate=1,'是','否') as financialstate,
                vtiger_crmentity.modifiedtime,
                vtiger_refillapplication.modulestatus,vtiger_refillapplication.remarks, 
                vtiger_refillapplication.rechargesource, vtiger_refillapplication.iscontracted,
                vtiger_refillapplication.humancost,vtiger_refillapplication.purchasecost,vtiger_refillapplication.contractamount,
                vtiger_refillapplication.voidreason,
                (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_refillapplication.voiduserid=vtiger_users.id) as voiduserid,vtiger_refillapplication.voiddatetime,vtiger_refillapplication.servicesigndate,vtiger_refillapplication.grossadvances,IF(vtiger_refillapplication.iscushion=1,'是','否') as iscushion,
                vtiger_refillapplication.expecteddatepayment,vtiger_refillapplication.expectedpaymentdeadline,vtiger_refillapplication.beardepartment,vtiger_refillapplication.bearratio,vtiger_refillapplication.totalreceivables,vtiger_refillapplication.usecontractamount,
                vtiger_refillapplication.bankcode,vtiger_refillapplication.removeaccounttime,vtiger_refillapplication.customer_name,vtiger_refillapplication.totalrecharge,vtiger_refillapplication.actualtotalrecharge,vtiger_refillapplication.expcashadvances,vtiger_refillapplication.totalcashin,vtiger_refillapplication.totalcashtransfer,vtiger_refillapplication.totalturnoverofaccount,vtiger_refillapplication.totaltransfertoaccount,vtiger_refillapplication.refillapplicationid 
                FROM 
                vtiger_refillapplication 
                LEFT JOIN vtiger_crmentity ON vtiger_refillapplication.refillapplicationid = vtiger_crmentity.crmid 
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_refillapplication.servicecontractsid 
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_refillapplication.accountid 
                WHERE 1=1 AND vtiger_refillapplication.refillapplicationid in(".implode(',',$recordsArr).") AND vtiger_crmentity.deleted=0 AND 
                vtiger_crmentity.smownerid=? AND vtiger_refillapplication.servicecontractsid=? AND vtiger_refillapplication.modulestatus='c_complete' AND vtiger_refillapplication.grossadvances>0";
        $resultRefill=$adb->pquery($query,array($current_user->id,$servicecontractsid));
        $num=$adb->num_rows($resultRefill);
        $return=array('flag'=>false);
        do{
            if($num!=$countNum){
                $return['msg']="选择错误!";
                break;
            }
            if($num==0){
                $return['msg']="没有要匹配的充值单!";
                break;
            }
            $query="SELECT * FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.relatetoid=? AND receivedstatus in('normal','virtualrefund') AND deleted=0 AND rechargeableamount>0";
            $resultPayments=$adb->pquery($query,array($servicecontractsid));

            if($adb->num_rows($resultPayments)==0){
                $return['msg']="没有可以匹配的回款!";
                break;
            }
            $Refill=array();
            while($row=$adb->fetch_array($resultRefill)){
                $Refill[]=$row;
            }
            $Payments=array();
            while($row=$adb->fetch_array($resultPayments)){
                $Payments[]=$row;
            }
            $return['data']=array('refill'=>$Refill,'payments'=>$Payments);
            $return['flag']=true;
        }while(0);
        if(1==$sendNum){
            $response=new Vtiger_Response();
            $response->setResult($return);
            $response->emit();
        }else{
            return  $return['flag'];
        }
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 批量回款匹配
     */
    public function doAddNewReffRaymentMore(Vtiger_Request $request){
        $receivedpaymentsid=$request->get('receivedpaymentsid');
        $refillapptotal=$request->get('refillapptotal');
        $rremarks=$request->get('rremarks');
        $parmentsRecordModel=Vtiger_Record_Model::getInstanceById($receivedpaymentsid,'ReceivedPayments');
        $records='';
        $refillapptotalArr=array();
        foreach($refillapptotal as $key=>$value){
            if($value<=0){
                continue;
            }
            $refillapptotalArr[$key]=$value;
            $records.=$key.',';
        }
        $records=trim($records,',');
        $request->set('records',$records);
        $request->set('sendNum',2);
        $request->set('servicecontractsid',$parmentsRecordModel->get('relatetoid'));
        $return=array('flag'=>false);
        do{
            if(!$this->matchReceivementsList($request)){
                $return['msg']='无效匹配!';
                break;
            }
            $msg='';
            foreach($refillapptotalArr as $key=>$value){
                $request->set('raymentid',$receivedpaymentsid);
                $request->set('record',$key);
                $request->set('refillapptotal',$value);
                $request->set('rremarks',$rremarks[$key]);
                $NewReffRayment=$this->doAddNewReffRayment($request);

                $msg.='['.$NewReffRayment['refillapplicationno'].']'.$NewReffRayment['msg']."<br>";
            }
            $return['msg']=$msg;
            $return['flag']=true;

        }while(0);
        $response=new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }
    public function showTable(Vtiger_Request $request){
        global $current_user,$adb;
        $accountid=$request->get('accountid');
        $query="SELECT vr.*,vu.last_name,vu.department FROM vtiger_refillapplication as vr left JOIN  vtiger_crmentity as vc  on vc.crmid=vr.refillapplicationid LEFT JOIN vtiger_users as vu on vc.smownerid = vu.id WHERE  vr.iscushion=1 AND  vr.accountid=? AND vr.grossadvances>0";
        $resultRefill=$adb->pquery($query,array($accountid));
        $data=array();
        while($row=$adb->fetch_array($resultRefill)){
            $row['modulestatus'] = vtranslate($row['modulestatus'], 'RefillApplication');//模块翻译
            $row['rechargesource'] = vtranslate($row['rechargesource'], 'RefillApplication');//模块翻译
            $data[]=$row;
        }

        $return['data']=$data;
        $return['flag']=true;
        $response=new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }
    public function getAddEditCommon(Vtiger_Request $request){
        $moduleName = $request->getModule();
        $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
        echo $recordModel->getAddEditCommon($request);
    }

    /**
     * 红冲导出
     * @global type $root_directory
     * @global type $current_user
     * @global type $site_URL
     * @global type $root_directory
     * @param Vtiger_Request $request
     */
    public function hcDetailsExport(Vtiger_Request $request){
            //导出数据
            set_time_limit(0);
            global $root_directory,$current_user,$site_URL;
            $path=$root_directory.'temp/';
            $filename=$path.'erppayment'.$current_user->id.'.xlsx';
            !is_dir($path)&&mkdir($path,'0777',true);
            @unlink($filename);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
//            if(!$moduleModel->exportGrouprt('ReceivedPayments','ExportRI')){   //权限验证
//                return;
//            }
            $departments=$request->get('department');
            $startdate=$request->get('datatime');
            $enddatatime=$request->get('enddatatime');
            $datetimesub=strtotime($startdate)-strtotime($enddatatime);
            $listQuery='';
            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');
            if($startdate>$enddatatime){
                $sql=" and left(vtiger_rubricrechargesheet.refundtime,10)>='{$enddatatime}' and left(vtiger_rubricrechargesheet.refundtime,10)<='{$startdate}'";
            }elseif($startdate==$enddatatime){
                $sql=" and left(vtiger_rubricrechargesheet.refundtime,10)='{$enddatatime}'";
            }elseif($startdate<$enddatatime){
                $sql=" and left(vtiger_rubricrechargesheet.refundtime,10)<='{$enddatatime}' and left(vtiger_rubricrechargesheet.refundtime,10)>='{$startdate}'";
            }
            global $root_directory;
            $db=PearDatabase::getInstance();
            $query="SELECT
	vtiger_users.last_name,
	vtiger_refillapplication.refillapplicationno,
	vtiger_servicecontracts.contract_no,
	vtiger_account.accountname,
  vtiger_products.productname,
	vtiger_rechargesheet.did,
	vtiger_rechargesheet.discount,
	vtiger_rechargesheet.accountrebatetype,
	vtiger_rubricrechargesheet.prestoreadrate,
	vtiger_rubricrechargesheet.refundtime,
	vtiger_rubricrechargesheet.remark
FROM
	vtiger_rubricrechargesheet
LEFT JOIN vtiger_refillapplication ON vtiger_rubricrechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid
LEFT JOIN vtiger_rechargesheet ON vtiger_rechargesheet.rechargesheetid = vtiger_rubricrechargesheet.rechargesheetid
LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_refillapplication.servicecontractsid
LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_rechargesheet.productid
LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid
LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_refillapplication.accountid
WHERE 1 = 1 and vtiger_rubricrechargesheet.deleted != 1 {$sql}{$listQuery}";
//            echo $query;die;
            $result= $db->run_query_allrecords($query);
ob_end_clean();
ob_clean();
            require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';

            $phpexecl=new PHPExcel();

            // Set document properties
            $phpexecl->getProperties()->setCreator("liu ganglin")
                ->setLastModifiedBy("liu ganglin")
                ->setTitle("Office 2007 XLSX servicecontracts Document")
                ->setSubject("Office 2007 XLSX servicecontracts Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("servicecontracts");


            // 添加头信处
            $phpexecl->setActiveSheetIndex(0)
                ->setCellValue('A1', '红冲申请人')
                ->setCellValue('B1', '申请单号')
                ->setCellValue('C1', '合同编号')
                ->setCellValue('D1', '公司名称')
                ->setCellValue('E1', '充值平台')
                ->setCellValue('F1', '账户ID')
                ->setCellValue('G1', '客户返点比例')
                ->setCellValue('H1', '客户返点类型')
                ->setCellValue('I1', '红冲账户币')
                ->setCellValue('J1', '红冲日期')
                ->setCellValue('K1', '备注');

            //设置自动居中
            $phpexecl->getActiveSheet()->getStyle('A1:P1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpexecl->getActiveSheet()->getStyle('R1:X1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //$result=array(1,2,3,4,5,6,7,3,8,9,10);
            //设置边框
            $phpexecl->getActiveSheet()->getStyle('A1:X1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            if(!empty($result)){
                foreach($result as $key=>$value){
                    $current=$key+2;
                    $phpexecl->setActiveSheetIndex(0)
                        ->setCellValue('A'.$current, $value['last_name'])
                        ->setCellValue('B'.$current, $value['refillapplicationno'])
                        ->setCellValue('C'.$current, $value['contract_no'])
                        ->setCellValue('D'.$current, $value['accountname'])
                        ->setCellValue('E'.$current, $value['productname'])
                        ->setCellValue('F'.$current, $value['did'].' ')
                        ->setCellValue('G'.$current, $value['discount'])
                        ->setCellValue('H'.$current,  vtranslate($value['accountrebatetype']))
                        ->setCellValue('I'.$current, $value['prestoreadrate'])
                        ->setCellValue('J'.$current, date('Y-m-d',strtotime($value['refundtime'])))
                        ->setCellValue('K'.$current, $value['remark']);
                    //加上边框
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':AC'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }

            // 设置工作表的名移
            $phpexecl->getActiveSheet()->setTitle('红冲明细导出');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="红冲明细导出.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
        exit;

    }

    /**
     *获取单个account
     * @param Vtiger_Request $request
     */
    public function getAccountplatformInfo(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $recordId=$request->get('record');
        $query = "SELECT 
                      vtiger_accountplatform.accountid,
                      vtiger_accountplatform_detail.accountplatform,
                      vtiger_accountplatform.accountplatformid,
                      vtiger_accountplatform.accountrebate,
                      vtiger_accountplatform.effectiveendaccount,
                      vtiger_accountplatform.effectivestartaccount,
                      vtiger_accountplatform_detail.idaccount,
                      vtiger_accountplatform.customeroriginattr,
                      vtiger_accountplatform.accountrebatetype,
                      vtiger_accountplatform.isprovideservice,
                      IFNULL(vtiger_accountplatform.rebatetype,'GoodsBack') AS rebatetype,
                      vtiger_accountplatform.modulestatus,
                      vtiger_accountplatform.supplierrebate,
                      (SELECT vtiger_products.productname FROM `vtiger_products` WHERE vtiger_products.productid=vtiger_accountplatform.productid) as topplatform,
                      vtiger_accountplatform.productid,
                      IF((SELECT
                            1
                        FROM
                            vtiger_refillapplication
                        LEFT JOIN vtiger_rechargesheet ON vtiger_rechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_refillapplication.accountid =vtiger_accountplatform.accountid
                        AND vtiger_rechargesheet.did =vtiger_accountplatform.idaccount limit 1)=1,1,0) AS rechargetypedetail,
                      vtiger_accountplatform.vendorid
                FROM vtiger_accountplatform 
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_accountplatform.accountplatformid
                LEFT JOIN vtiger_accountplatform_detail ON  vtiger_accountplatform_detail.accountplatformid=vtiger_accountplatform.accountplatformid
                WHERE vtiger_crmentity.deleted=0 
                AND vtiger_accountplatform.modulestatus='c_complete'
                AND vtiger_accountplatform.isforbidden=0
                AND vtiger_accountplatform.accountplatformid=?";
        $result = $db->pquery($query, array($recordId));
        $row = $db->fetch_array($result);
        $rechargetypedetail='OpenAnAccount';
        if($row['rechargetypedetail']==1){
            $rechargetypedetail='renew';
        }
        $tmp['accountid']=$row['accountid'];
        $tmp['accountplatform']=$row['accountplatform'];
        $tmp['accountplatformid']=$row['accountplatformid'];
        $tmp['accountrebate']=$row['accountrebate'];
        $tmp['effectiveendaccount']=$row['effectiveendaccount'];
        $tmp['effectivestartaccount']=$row['effectivestartaccount'];
        $tmp['idaccount']=$row['idaccount'];
        $tmp['modulestatus']=$row['modulestatus'];
        $tmp['supplierrebate']=$row['supplierrebate'];
        $tmp['topplatform']=$row['topplatform'];
        $tmp['productid']=$row['productid'];
        $tmp['vendorid']=$row['vendorid'];
        $tmp['accountrebatetype']=$row['accountrebatetype'];
        $tmp['rechargetypedetail']=$rechargetypedetail;
        $tmp['customeroriginattr']=$row['customeroriginattr'];
        $tmp['isprovideservice']=$row['isprovideservice'];
        $tmp['didcount']=$row['didcount'];
        $tmp['rebatetype']=$row['rebatetype'];
        $response=new Vtiger_Response();
        $response->setResult($tmp);
        $response->emit();
    }

    public function getExcelData($fileName,$fileData,$didIds){
        global $root_directory;
        $dir	= $root_directory.'/temp/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $filePath = $dir .  time().$fileName;
        @file_put_contents($filePath,base64_decode($fileData));

        if(!file_exists($filePath)) {
            return array("success"=>false,'msg'=>'文件上传失败');
        }
        require_once $root_directory.'/libraries/PHPExcel/PHPExcel.php';
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($filePath)) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath)) {
                return array("success"=>false,'msg'=>'无法识别此文件');

            }
        }
        $phpexecl = $PHPReader->load($filePath);
        $currentSheet = $phpexecl->getSheet(0);
        $allColumn = $currentSheet->getHighestColumn();
        $allRow = $currentSheet->getHighestRow();
        $excelResult = array();  //声明数组
        $columnData = array();
        if($allRow>21){
            return array("success"=>false,'msg'=>'单次最多上传20条!!!');
        }
        for($currentRow = 2;$currentRow <= $allRow;$currentRow++) {
            $did = strval($currentSheet->getCellByColumnAndRow(ord("A") - 65, $currentRow)->getValue());
            for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65, $currentRow)->getValue();
                switch ($currentColumn){
                    case 'A':
                        if(!in_array($val,$didIds)){
                            unlink($filePath);
                            return json_encode(array("success"=>false,'msg'=>'存在不合法的账户ID'.implode("','",$didIds)));
                        }
                        $columnData[$did]['did'] =  $val;
                        break;
                    case 'B':
                        $columnData[$did]['receivementcurrencytype'] =  $val;
                        break;
                    case 'C':
                        $columnData[$did]['prestoreadrate'] = $val;
                        $columnData[$did]['accounttransfer'] = $val;
                        break;
                    case 'D':
                        $columnData[$did]['factorage'] = $val;
                        break;
                    case 'E':
                        $columnData[$did]['activationfee'] =  $val;
                        break;
                    case 'F':
                        $columnData[$did]['transferamount'] =  $val;
                        break;
                    case 'G':
                        $columnData[$did]['taxation'] =  $val;
                        break;
                    case 'H':
                        $columnData[$did]['mstatus'] =  $val;
                        break;
                }
            }
        }
        return array("success"=>true,'msg'=>'','data'=>$columnData);
    }

    public function getCoinReturnExcelData($fileName,$fileData,$didIds){
        global $root_directory;
        $dir	= $root_directory.'/temp/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $filePath = $dir .  time().$fileName;
        @file_put_contents($filePath,base64_decode($fileData));

        if(!file_exists($filePath)) {
            return array("success"=>false,'msg'=>'文件上传失败');
        }
        require_once $root_directory.'/libraries/PHPExcel/PHPExcel.php';
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($filePath)) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath)) {
                return array("success"=>false,'msg'=>'无法识别此文件');

            }
        }
        $phpexecl = $PHPReader->load($filePath);
        $currentSheet = $phpexecl->getSheet(0);
        $allColumn = $currentSheet->getHighestColumn();
        $allRow = $currentSheet->getHighestRow();
        $excelResult = array();  //声明数组
        $columnData = array();
        if($allRow>21){
            return array("success"=>false,'msg'=>'单次最多上传20条!!!');
        }
        for($currentRow = 2;$currentRow <= $allRow;$currentRow++) {
            $did = strval($currentSheet->getCellByColumnAndRow(ord("A") - 65, $currentRow)->getValue());
            for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65, $currentRow)->getValue();
                switch ($currentColumn){
                    case 'A':
                        if(!in_array($val,$didIds)){
                            unlink($filePath);
                            return json_encode(array("success"=>false,'msg'=>'存在不合法的账户ID'.implode("','",$didIds)));
                        }
                        $columnData[$did]['did'] =  $val;
                        break;
                    case 'B':
                        $columnData[$did]['accounttransfer'] =  $val;
                        break;
                }
            }
        }
        return array("success"=>true,'msg'=>'','data'=>$columnData);
    }

    public function uploadBatchImport(Vtiger_Request $request){
//        error_reporting(E_ALL);
        $accountId = $request->get('record');
        $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $didAccounts = $this->getDidAccounts($accountId);
        if(!count($didAccounts)){
            echo json_encode(array("success"=>false,'msg'=>'没有可用账户ID'));
            exit();
        }
        $didAccountIds =array_values(array_map('array_shift',$didAccounts));

        $excelData = $this->getExcelData($request->get("fileName"),$request->get("fileData"),$didAccountIds);
        if(!$excelData['success']){
            echo json_encode($excelData);
            exit();
        }

        $newdata = [];
        foreach($excelData['data'] as $k=>$v){
            $newdata[] = array_merge($v,$didAccounts[$k]);
        }
        if(!count($newdata)){
            echo json_encode(array("success"=>false,'msg'=>'无可用信息'));
            exit();
        }
        echo json_encode(array("success"=>true,'data'=>$newdata,'msg'=>'成功'));
    }
    public function uploadBatchOutput(Vtiger_Request $request){
//        error_reporting(E_ALL);
        $accountId = $request->get('record');
        $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $didAccounts = $this->getDidAccounts($accountId);
        if(!count($didAccounts)){
            echo json_encode(array("success"=>false,'msg'=>'没有可用账户ID'));
            exit();
        }
        $didAccountIds =array_values(array_map('array_shift',$didAccounts));

        $excelData = $this->getCoinReturnExcelData($request->get("fileName"),$request->get("fileData"),$didAccountIds);
        if(!$excelData['success']){
            echo json_encode($excelData);
            exit();
        }

        $newdata = [];
        foreach($excelData['data'] as $k=>$v){
            $newdata[] = array_merge($v,$didAccounts[$k]);
        }
        if(!count($newdata)){
            echo json_encode(array("success"=>false,'msg'=>'无可用信息'));
            exit();
        }
        echo json_encode(array("success"=>true,'data'=>$newdata,'msg'=>'成功'));
    }
    /**
     * 获取did信息列表
     *
     * @param $recordId
     * @return array
     */
    public function getDidAccounts($accountId){
        $db=PearDatabase::getInstance();
        $query = "SELECT 
                      vtiger_accountplatform.accountid,
                      vtiger_accountplatform_detail.accountplatform,
                      vtiger_accountplatform.accountplatformid,
                      vtiger_accountplatform.accountrebate,
                      vtiger_accountplatform.effectiveendaccount,
                      vtiger_accountplatform.effectivestartaccount,
                      vtiger_accountplatform_detail.idaccount,
                      vtiger_accountplatform.customeroriginattr,
                      vtiger_accountplatform.accountrebatetype,
                      vtiger_accountplatform.isprovideservice,
                      IFNULL(vtiger_accountplatform.rebatetype,'GoodsBack') AS rebatetype,
                      vtiger_accountplatform.modulestatus,
                      vtiger_accountplatform.supplierrebate,
                      (SELECT vtiger_products.productname FROM `vtiger_products` WHERE vtiger_products.productid=vtiger_accountplatform.productid) as topplatform,
                      vtiger_accountplatform.productid,
                      IF((SELECT
                            1
                        FROM
                            vtiger_refillapplication
                        LEFT JOIN vtiger_rechargesheet ON vtiger_rechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_refillapplication.accountid =vtiger_accountplatform.accountid
                        AND vtiger_rechargesheet.did =vtiger_accountplatform.idaccount limit 1)=1,1,0) AS rechargetypedetail,
                      vtiger_accountplatform.vendorid
                FROM vtiger_accountplatform 
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_accountplatform.accountplatformid
                LEFT JOIN vtiger_accountplatform_detail ON  vtiger_accountplatform_detail.accountplatformid=vtiger_accountplatform.accountplatformid
                WHERE vtiger_crmentity.deleted=0 
                AND vtiger_accountplatform.modulestatus='c_complete'
                AND vtiger_accountplatform.isforbidden=0
                AND vtiger_accountplatform.accountid=?";
        $result = $db->pquery($query, array($accountId));
        while ($row = $db->fetch_array($result)) {
            $rechargetypedetail='OpenAnAccount';
            if($row['rechargetypedetail']==1){
                $rechargetypedetail='renew';
            }
            $tmp['idaccount']=$row['idaccount'];
            $tmp['accountid']=$row['accountid'];
            $tmp['accountplatform']=$row['accountplatform'];
            $tmp['accountplatformid']=$row['accountplatformid'];
            $tmp['accountrebate']=$row['accountrebate'];
            $tmp['effectiveendaccount']=$row['effectiveendaccount'];
            $tmp['effectivestartaccount']=$row['effectivestartaccount'];
            $tmp['modulestatus']=$row['modulestatus'];
            $tmp['supplierrebate']=$row['supplierrebate'];
            $tmp['topplatform']=$row['topplatform'];
            $tmp['productid']=$row['productid'];
            $tmp['vendorid']=$row['vendorid'];
            $tmp['accountrebatetype']=$row['accountrebatetype'];
            $tmp['rechargetypedetail']=$rechargetypedetail;
            $tmp['customeroriginattr']=$row['customeroriginattr'];
            $tmp['isprovideservice']=$row['isprovideservice'];
            $tmp['didcount']=$row['didcount'];
            $tmp['rebatetype']=$row['rebatetype'];
            $return[strval($row['idaccount'])] = $tmp;
        }
        return $return;
    }
}
