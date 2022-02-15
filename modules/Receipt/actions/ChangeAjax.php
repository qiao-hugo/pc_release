<?php
class Receipt_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('saveComment');
        $this->exposeMethod('getproductlist');
        $this->exposeMethod('getPayApply');
        $this->exposeMethod('getRelationReceivedPayments');
        $this->exposeMethod('getReceivedPayments');
    }



	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
	}

    /**
     * 获取合同关联回款信息
     * @param Vtiger_Request $request
     */
    public  function getReceivedPayments(Vtiger_Request $request){
        $receivedpaymentsid = $request->get('receivedpaymentsid');
        global $adb;
        $query="SELECT * FROM vtiger_receivedpayments WHERE receivedpaymentsid=? LIMIT 1";
        $result=$adb->pquery($query,array($receivedpaymentsid));
        $resultdata=$adb->query_result_rowdata($result,0);
        $returnData = array(
            'invoicerayment'=>[$resultdata],
            'invoicedTotal'=>0
        );
        $response = new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }

    /**
     * 获取合同关联回款信息
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

        //通过合同匹配金额大于0的回款
        $query_sql = "SELECT
                    {$invoicecompany},
                    IF(vtiger_receivedpayments.paytitle!='',vtiger_receivedpayments.paytitle,vtiger_staypayment.payer) AS t_paytitle,
                    vtiger_receivedpayments.receivedpaymentsid,
                    CONCAT(
                        vtiger_receivedpayments.reality_date,
                        '【',
                        vtiger_receivedpayments.receivedpaymentsid,
                        '】',
                        ' ￥',
                        vtiger_receivedpayments.unit_price,
                        ' ',
                        vtiger_receivedpayments.paytitle,
                        ' [',
                        {$contract_no},
                        ']'
                    ) AS paytitle,
                    vtiger_receivedpayments.unit_price,
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

        //获取已申请的开票金额
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


	public function saveComment(Vtiger_Request $request){
        $recordId=$request->get('recordId');
        $fllowupdate=$request->get('fllowupdate');
        $nextdate=$request->get('nextdate');
        $hasaccess=$request->get('hasaccess');
        $currentprogess=$request->get('currentprogess');
        $nextwork=$request->get('nextwork');
        $policeindicator=$request->get('policeindicator');
        global $adb,$current_user;
        $Sql='INSERT INTO vtiger_channelcomment(channelid,fllowdate,nextdate,hasaccess,currentprogess,nextwork,policeindicator,smownerid,createdtime) VALUES(?,?,?,?,?,?,?,?,?)';
        $adb->pquery($Sql,array($recordId,$fllowupdate,$nextdate,$hasaccess,$currentprogess,$nextwork,$policeindicator,$current_user->id,date('Y-m-d H:i:s')));
        $Sql='update vtiger_channels set hasaccess=?,fllowdate=?,nextdate=? WHERE  channelid=?';
        $adb->pquery($Sql,array($hasaccess,$fllowupdate,$nextdate,$recordId));
    }

    /**
     * 产品的类型
     * @param Vtiger_Request $request
     */
    function getproductlist(Vtiger_Request $request){
        $parentcate=$request->get('parentcate');
        $db=PearDatabase::getInstance();
        $query = 'SELECT soncateid,soncate FROM vtiger_soncate WHERE deleted = 0 AND parentcate='."'".$parentcate."'";
        $arrrecords = $db->run_query_allrecords($query);
        $arrlist=array();
        if(!empty($arrrecords)){
            foreach($arrrecords as $value){
                $arrlist[]=$value;
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arrlist);
        $response->emit();
    }

    //获取用户名称,ID
    public function getPayApply(Vtiger_Request $request){
        $parentcate=$request->get('parentcate');
        $soncate=$request->get('soncate');

        $payApplyRecordModel = PayApply_Record_Model::getCleanInstance("PayApply");
        $data1 = $payApplyRecordModel->getPayApply($parentcate,$soncate);
        $data['success']=true;
        $data['list']=$data1;
        echo json_encode($data);
    }
}
