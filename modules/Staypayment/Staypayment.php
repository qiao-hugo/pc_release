<?php
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class Staypayment extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_staypayment";
	var $table_index= 'staypaymentid';
    var $tab_name_index = Array('vtiger_crmentity' =>'crmid','vtiger_staypayment'=>'staypaymentid');//'vtiger_crmentity' => 'crmid',
	var $tab_name = Array('vtiger_crmentity','vtiger_staypayment');
	var $column_fields = Array();
	var $sortby_fields = Array();
	var $list_fields = Array();
	var $list_fields_name = Array();
	var $list_link_field= 'staypaymentname';
	var $search_fields = Array();
	var $search_fields_name = Array();
	var $required_fields =  array();
	var $mandatory_fields = Array();
	var $emailTemplate_defaultFields = array();
	var $default_order_by = 'staypaymentname';
	var $default_sort_order = 'ASC';
	// For Alphabetical search
	var $def_basicsearch_col = 'staypaymentname';
	var $related_module_table_index = array();
	//关联模块的一些字段和数组;
	var $relatedmodule_list=array();
	var $relatedmodule_fields=array(

	);
    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }
	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module) {
        $sql='UPDATE vtiger_staypayment,vtiger_crmentity SET modulename=setype WHERE vtiger_crmentity.crmid=vtiger_staypayment.contractid AND vtiger_staypayment.staypaymentid=?';
        $this->db->pquery($sql,array($this->id));

	}

    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename, $workflowsid, $salesorderid, $isedit = '') {
        parent::makeWorkflows($modulename, $workflowsid, $salesorderid, $isedit = '');
        $recordModel =Vtiger_Record_Model::getInstanceById($salesorderid, 'Staypayment', TRUE);
        $companyCode=$this->getContractsCompanyCode($recordModel->get('modulename'),$recordModel->get('contractid'));

        $query = "UPDATE vtiger_salesorderworkflowstages,
				 vtiger_staypayment
				SET vtiger_salesorderworkflowstages.accountid=vtiger_staypayment.accountid,
				     vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_staypayment.accountid),
				    vtiger_staypayment.modulestatus='b_check',
				    vtiger_salesorderworkflowstages.companycode=?
				WHERE vtiger_staypayment.staypaymentid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=? AND  vtiger_salesorderworkflowstages.workflowsid=? ";
        $this->db->pquery($query, array($companyCode,$salesorderid,$workflowsid));
        $type='out';
        if(in_array($recordModel->get('payertype'),array('inperson','incompany'))){
            $type='in';
        }
        $request=new Vtiger_Request();
        $request->set('taxpayers_no',$recordModel->get('taxpayers_no'));
        $request->set('payer',$recordModel->get('payer'));
        $request->set('idcard',$recordModel->get('idcard'));
        $request->set('accountid',$recordModel->get('accountid'));
        $request->set('taxpayers_no',$recordModel->get('taxpayers_no'));
        $request->set('type',$type);
        $request->set('record',$salesorderid);
        $request->set('stay_type',$recordModel->get('payertype'));
        $result=$this->isNeedFile($request);
        if(!$result){
            //删掉审核流程
            $sql="delete from vtiger_salesorderworkflowstages where salesorderid=? and workflowsid=? and workflowstagesflag in ('CFO')";
            $this->db->pquery($sql, array($salesorderid,$workflowsid));
            //激活下一流程
            $sql="update vtiger_salesorderworkflowstages set isaction=1, actiontime=? where  salesorderid=? and workflowsid=?";
            $this->db->pquery($sql,array(date('Y-m-d H:i:s'),$salesorderid,$workflowsid));
        }
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
    }

    /**
     * 判断是否需要流程
     * @param Vtiger_Request $request
     * @return bool
     * @throws Exception
     */
    public function isNeedFile(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $taxpayers_no = trim($request->get('taxpayers_no'));
        $payer = trim($request->get('payer'));
        $idcard=trim($request->get('idcard'));
        $accountid=trim($request->get('accountid'));
        $type=trim($request->get('type'));
        $record=trim($request->get('record'));
        $stay_type=trim($request->get('stay_type'));
        $flag=true;
        //不需要判断支付渠道
//        if($payer&&$record>0){
//            //编辑时
//            $sql="select * from vtiger_receivedpayments where staypaymentid =? order by createtime desc limit 1";
//            $result=$db->pquery($sql,array($record));
//            if($db->num_rows($result)>0){
//                //代付款已经匹配上了，使用过了
//                $paymentchannel=$db->query_result($result,0,'paymentchannel');
//                $paytitle=$db->query_result($result,0,'paytitle');
//                $receivedpaymentsid=$db->query_result($result,0,'receivedpaymentsid');
//                if($paymentchannel=='对公转账'&&$paytitle!=$payer){
//                    return true;
//                }else if($paymentchannel=='支付宝转账'){
//                    $sql="SELECT * FROM vtiger_receivedpayments WHERE REPLACE (?,' ','') LIKE REPLACE (REPLACE (paytitle,' ',''),'*','_') and receivedpaymentsid=?";
//                    $result=$db->pquery($sql,array($payer,$receivedpaymentsid));
//                    if($db->num_rows($result)==0){
//                        return true;
//                    }
//                }
//            }
//        }
        if($flag){
            if($type=='in'){
                if($idcard&&$payer){
                    $sql="select staypaymentid from vtiger_staypayment where accountid=? and idcard=? and payer=? and modulestatus=? and payertype=?";
                    $result=$db->pquery($sql,array($accountid,$idcard,$payer,'c_complete',$stay_type));
                    if($db->num_rows($result)>0){
                        //此人已经给这个客户做过代付款了，直接过
                        return false;
                    }else{
                        //身份证和打款人都有的情况下去判断要不要流程
                        $sql="select staypaymentid from vtiger_staypayment where accountid!=? and idcard=? and payer=? and payertype=?";
                        $result=$db->pquery($sql,array($accountid,$idcard,$payer,$stay_type));
                        if($db->num_rows($result)>0){
                            //如果他给其他客户做过付款,那就给CFO审核
                            return true;
                        }else{
                            return false;
                        }
                    }
                }else if($payer&&$taxpayers_no){
                    $sql="select staypaymentid from vtiger_staypayment where accountid=? and taxpayers_no=? and modulestatus=? and payertype=?";
                    $result=$db->pquery($sql,array($accountid,$taxpayers_no,'c_complete',$stay_type));
                    if($db->num_rows($result)>0){
                        return false;
                    }else{
                        //纳税人识别号和打款人都有的情况下去判断要不要流程
                        $sql="select staypaymentid from vtiger_staypayment where accountid!=? and taxpayers_no=? and payertype=?";
                        $result=$db->pquery($sql,array($accountid,$taxpayers_no,$stay_type));
                        if($db->num_rows($result)>0){
                            return true;
                        }else{
                            return false;
                        }
                    }
                }
            }else{
                //境外
                $sql="select staypaymentid from vtiger_staypayment where accountid=? and  payer=? and modulestatus=? and payertype=?";
                $result=$db->pquery($sql,array($accountid,$payer,'c_complete',$stay_type));
                if($db->num_rows($result)>0){
                    return false;
                }else{
                    $sql="select staypaymentid from vtiger_staypayment where accountid!=? and  payer=? and payertype=?";
                    $result=$db->pquery($sql,array($accountid,$payer,$stay_type));
                    if($db->num_rows($result)>0){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
        }
        return false;
    }

    /**
     * 审核前判断
     * @param Vtiger_Request $request
     * @throws Exception
     */
    function workflowcheckbefore(Vtiger_Request $request){
        $stagerecordid=$request->get('stagerecordid');
        $record=$request->get('record');
        $staypaymentjine=trim($request->get('staypaymentjine'));
        $currencytype=trim($request->get('currencytype'));
        $staypaymenttype=$request->get('staypaymenttype');
        $startdate=trim($request->get('startdate'));
        $enddate=trim($request->get('enddate'));
        $db=PearDatabase::getInstance();
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'Staypayment'";
        $result=$db->pquery($query,array($stagerecordid));
        $currentflag=$db->query_result($result, 0, 'workflowstagesflag');

//        if($currentflag=='PAYMENTCONTRACT'){
//            $resultaa['success'] = 'false';
//            if($staypaymenttype=='fixation'){
//                //固定
//                $sql="select * from vtiger_staypayment where staypaymentid=".$record;
//                $stayPaymentArray=$db->run_query_allrecords($sql);
//                $stayPaymentArray=$stayPaymentArray[0];
//                if($stayPaymentArray['isauto']){
//                    //是自动生成的
//                    if($currencytype!=$stayPaymentArray['currencytype']){
//                        $resultaa['error']['message'] = "虚拟新建的代付款不允许更改货币类型";
//                        echo json_encode($resultaa);
//                        exit;
//                    }
//                    if(bccomp($staypaymentjine,$stayPaymentArray['staypaymentjine'])>=0){
//                        $sql = "update vtiger_staypayment set staypaymentjine = ?,surplusmoney=? where staypaymentid=?";
//                        $surplusmoney=bcadd(bcsub($staypaymentjine,$staypaymentjine),$stayPaymentArray['surplusmoney']);
//                        $db->pquery($sql,array($staypaymentjine,$surplusmoney,$record));
//                    }else{
//                        $resultaa['error']['message'] = "虚拟新建的代付款代付款金额更改必须不小于原始金额";
//                        echo json_encode($resultaa);
//                        exit;
//                    }
//                }else{
//                    $sql = "update vtiger_staypayment set staypaymentjine = ?,surplusmoney=?,currencytype=? where staypaymentid=?";
//                    $db->pquery($sql,array($staypaymentjine,$staypaymentjine,$currencytype,$record));
//                }
//            }else{
//                //非固定
//                $sql = "update vtiger_staypayment set startdate = ?,enddate=? where staypaymentid=?";
//                $db->pquery($sql,array($startdate,$enddate,$record));
//            }
////            $resultaa['success'] = 'true';
////            echo json_encode($resultaa);
////            exit;
//        }
    }


       /** 节点审核时到了指定节点抓取时间
     * 后置事件
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request){
        $stagerecordid = $request->get('stagerecordid');
        $record = $request->get('record');

        $query = "SELECT
                    vtiger_workflowstages.workflowstagesflag,
        		    vtiger_salesorderworkflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'Staypayment'";
        $result = $this->db->pquery($query, array($stagerecordid));
        $currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'Staypayment', TRUE);
        $entity = $recordModel->entity->column_fields;
        $currentflag = trim($currentflag);

        $this->db->pquery("UPDATE vtiger_staypayment SET workflowsnode=(SELECT vtiger_salesorderworkflowstages.workflowstagesname FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='Staypayment' LIMIT 1) WHERE staypaymentid=?", array($record, $record));
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['salesorderid'] = $request->get('record');
        $params['workflowsid'] = $workflowsid;
        $this->hasAllAuditorsChecked($params);
        //发送审核通过邮件
        $recordModel->sendWarningEmail($record);
        $recordModel->sendWarningWx($record);
    }


    /**
     * 审核打回中处理
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request) {
        $stagerecordid = $request->get('isrejectid');
        $record = $request->get('record');
        $query = "SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'Staypayment'";
        $result = $this->db->pquery($query, array($stagerecordid));

        $currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'Staypayment');

        //匹配过
        $sql='select receivedpaymentsid from vtiger_receivedpayments where staypaymentid=?';
        $result=$this->db->pquery($sql,array($record));
        $receivedpaymentsid=$this->db->query_result($result,0,'receivedpaymentsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='Staypayment' AND vtiger_salesorderworkflowstages.workflowsid=?", array($record, $workflowsid));
        if($receivedpaymentsid){
            $this->db->pquery("UPDATE vtiger_staypayment SET workflowsnode='打回中',modulestatus='a_normal' WHERE staypaymentid=?", array($record));
        }else{
            $this->db->pquery("UPDATE vtiger_staypayment SET workflowsnode='打回中',modulestatus='a_exception' WHERE staypaymentid=?", array($record));
        }
        //发送打回邮件
        $recordModel->sendWarningEmail($record,$request->get('reject'));
        $recordModel->sendWarningWx($record,$request->get('reject'));

    }

}
?>
