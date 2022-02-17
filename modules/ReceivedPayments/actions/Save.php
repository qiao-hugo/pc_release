<?php
class ReceivedPayments_Save_Action extends Vtiger_Save_Action {
	
	public function process(Vtiger_Request $request) {
        $is_collate = $request->get('is_collate');
        //判断是否是核对编辑
        if ($is_collate) {
            $requestData = $request->getAll();
            $moduleName = $request->getModule();
            $recordId = $request->get('record');
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            $recordData = $recordModel->getData();
            $adb = PearDatabase::getInstance();
            global $current_user;
            $now = date('Y-m-d H;i:s');
            $companyaccountsid='';
            if($requestData['owncompany']){
                $companyaccountsid = $recordModel->getCompanyAccountsIdByOwnCompany($requestData['owncompany']);
            }
            $sql = "UPDATE vtiger_receivedpayments SET paymentchannel=?, paymentcode=?, owncompany=?, reality_date=?, modifiedtime=?, checkid=?,companyaccountsid=? WHERE receivedpaymentsid=?";
            $adb->pquery($sql,
                [
                    $requestData['paymentchannel'],
                    $requestData['paymentcode'],
                    $requestData['owncompany'],
                    $requestData['reality_date'],
                    $now,
                    $current_user->id,
                    $companyaccountsid,
                    $recordId
                ]
            );
            //更新发票对应回款记录信息
            $sql = 'UPDATE vtiger_newinvoicerayment SET arrivaldate=? WHERE receivedpaymentsid=?';
            $adb->pquery($sql,
                [
                    $requestData['reality_date'],
                    $recordId
                ]
            );
            //更新充值申请单对应回款记录信息
            $sql = 'UPDATE vtiger_refillapprayment SET arrivaldate=?, owncompany=? WHERE receivedpaymentsid=?';
            $adb->pquery($sql,
                [
                    $requestData['reality_date'],
                    $requestData['owncompany'],
                    $recordId
                ]
            );
            /* 记录变动的字段 start*/
            $fields = ['paymentchannel', 'paymentcode', 'owncompany', 'reality_date'];
            $changedFields = [];
            foreach ($fields as $field) {
                if ($recordData[$field] != $requestData[$field]) {
                    $changedFields[] = [
                       'fieldname' => $field,
                       'prevalue' => $recordData[$field],
                       'postvalue' => $requestData[$field]
                    ];
                }
            }
            if ($changedFields) {
                $result = $adb->query("SELECT id FROM vtiger_modtracker_basic_seq limit 1");
                $seq = $adb->query_result_rowdata($result);
                $seqId = $seq['id'] + 1;
                $adb->query("UPDATE vtiger_modtracker_basic_seq SET id=id+1");
                $adb->pquery(
                    "INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?, ?, 'ReceivedPayments', ?, ?, 0)",
                    [$seqId, $recordId, $current_user->id, $now]
                );
                $valuesStr = '';
                foreach ($changedFields as $item) {
                    $valuesStr .= "({$seqId}, '{$item['fieldname']}', '{$item['prevalue']}', '{$item['postvalue']}'),";
                }
                $valuesStr = rtrim($valuesStr, ',');
                $adb->query("INSERT INTO vtiger_modtracker_detail(id, fieldname, prevalue, postvalue) VALUES". $valuesStr);
            }

            //系统分类
            $classifyRecordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
            $classifyRecordModel->systemClassification($recordId);
            /* 记录变动的字段 end*/
            $loadUrl = $recordModel->getDetailViewUrl();
            if(empty($loadUrl)){
                $loadUrl="index.php";
            }
            header("Location: $loadUrl");
        } else {
		    $this->db = PearDatabase::getInstance();

		    //2015年6月1日 星期一 wangbin 如果是首次回款，把当前的回款事件插入到合同表里面去;
		    //var_dump($request);
	    
		    $relatetoid = $request->get('relatetoid');
		    $reality_date = $request->get('reality_date');
		    if(!empty($relatetoid)){
	           if($this->db->num_rows($this->db->pquery('SELECT * FROM vtiger_receivedpayments WHERE relatetoid = ?',array($relatetoid)))==0){
	               $this->db->pquery('UPDATE vtiger_servicecontracts SET firstreceivepaydate = ? WHERE servicecontractsid = ?',array($reality_date,$relatetoid));
	           }
		    }
		    //end
	    
		    global $current_user;
		    $userid=$current_user->id;
		    $record=$request->get('record');
		    $date_var = date("Y-m-d H:i:s");
		    $date = $this->db->formatDate($date_var, true);
	    
		    //添加创建时间，创建人，以及修改时间和修改人;
		   if (empty($record)){
		       $request->set('modifiedtime',$date);
		       $request->set('checkid',$userid);
		       $request->set('createtime',$date);
		       $request->set('createid',$userid);
	           if(!$request->get('maybe_account')){
	               //没可能客户
	               $accountdata = ReceivedPayments_Record_Model::match_account(trim($request->get('paytitle')));
	               if($accountdata['crmid']){
	                   $request->set('maybe_account',$accountdata['crmid']);
	               }
	           }
		   }else{
		       $request->set('modifiedtime',$date);
		       $request->set('checkid',$userid);
		   }
	   
	// 	   echo $request->get('isguarantee') ;die;
		   //如果没有勾选是否担保，就将担保人赋值为空  wangbin 2015年7月15日
		   if($request->get('isguarantee') == '0'){
		       $request->set('guaranteeperson', '');
		   }
		   //end
            // 获取
            $receivepayid = $request->get('record');
            $servicecontractsid = $request->get('relatetoid');
            if($servicecontractsid>0){
                echo '编辑时不允许匹配合同!';
                exit;
            }
            $adb = $this->db;
            $old_relatetoid = 0;
            if (!empty($receivepayid)) {
                $receivepayment_data = Vtiger_Record_Model::getInstanceById($receivepayid, 'ReceivedPayments');
                $receivepayment = $receivepayment_data->getdata(); //回款以前的信息
                $old_relatetoid = $receivepayment['relatetoid'];
                $chargebacks=$receivepayment_data->get('chargebacks');
                if($chargebacks!=0){
                    if(!empty($_REQUEST['unit_price'])){
                        if(bccomp($_REQUEST['unit_price'],$chargebacks,2)==-1){
                            echo '入账金额小于扣款金额,不能保存!';
                            exit;
                        }
                    }

                }
                $_REQUEST['old_reality_date']=$receivepayment['reality_date'];
            }



           /* $maybe_account =  $request->get('maybe_acc');
            $request->set(maybe_account,$maybe_account);*/
           //去掉paytitle前后的普通空格 圆角空格
            $paytitle = trim($request->get('paytitle'),' ');
            $paytitle = str_replace("　",' ',$paytitle);
            $paytitle = preg_replace("/^[\s\v".chr(227).chr(128)."]+/","", $paytitle); //替换开头空字符
            $paytitle = rtrim($paytitle);

            //        $paytitle = rtrim($paytitle,'　');
    //        $paytitle = preg_replace("/[\s\v".chr(227).chr(128)."]+$/","", $paytitle); //替换结尾空字符
            $request->set('paytitle',$paytitle);
            $recordModel = $this->saveRecord($request);

            $standardmoney = $request->get('standardmoney');
            $owncompany = $request->get('owncompany');
            $paytitle = $request->get('paytitle');
            $adb->pquery("update vtiger_refundtimeoutaudit set unit_price=?,reality_date=?,owncompany=?,paytitle=? where receivedpaymentsid=?",
                  array($standardmoney,$reality_date, $owncompany,$paytitle,$record));



            // 1)当合同金额>0的情况下，若合同的收款金额>=合同金额，则合同自动关闭
           // 2016-10-10 周海

            $unit_price = 0; //回款金额
            // 回款的金额
            $sql = "select sum(unit_price) AS unit_price_total  from vtiger_receivedpayments where relatetoid=?";
            $sel_result = $adb->pquery($sql, array($servicecontractsid));
            $res_cnt = $adb->num_rows($sel_result);
            if ($res_cnt > 0) {
                $receivedpayments_row = $adb->query_result_rowdata($sel_result, 0);
                $unit_price = $receivedpayments_row['unit_price_total'];
            }


            if (! empty($receivepayid)) {  //修改
                if (!empty($old_relatetoid) && $old_relatetoid != $servicecontractsid) {
                    // 把以前的回款的服务合同的关闭状态改为正常
                    // 判断以前的回款的合同是否要 关闭状态
                    $sql = "select sum(unit_price) AS unit_price_total  from vtiger_receivedpayments where relatetoid=?";
                    $sel_result = $adb->pquery($sql, array($old_relatetoid));
                    $res_cnt = $adb->num_rows($sel_result);
                    $old_unit_price = 0;
                    if ($res_cnt > 0) {
                        $old_receivedpayments_row = $adb->query_result_rowdata($sel_result, 0);
                        $old_unit_price = $old_receivedpayments_row['unit_price_total'];
                    }
                    $servicecontracts_data = Vtiger_Record_Model::getInstanceById($old_relatetoid, 'ServiceContracts');
                    $servicecontracts = $servicecontracts_data->getdata(); //合同的信息
                    if (intval($old_unit_price) < intval($servicecontracts['total'])) {
                        // 合同自动关闭
                        $sql = "update vtiger_servicecontracts set contractstate=? where servicecontractsid=? and isautoclose='1'";
                        $adb->pquery($sql, array('0', $old_relatetoid));
                    }
                }
                if (!empty($servicecontractsid)) {  //服务合同不能为空
                    $servicecontracts_data = Vtiger_Record_Model::getInstanceById($servicecontractsid, 'ServiceContracts');
                    $servicecontracts = $servicecontracts_data->getdata(); //合同的信息
                    if (intval($servicecontracts['total']) > 0 &&  intval($unit_price) >= intval($servicecontracts['total'])) {
                        // 合同自动关闭
                        $sql = "update vtiger_servicecontracts set contractstate=? where servicecontractsid=? AND isautoclose='1'";
                        $adb->pquery($sql, array('1', $servicecontractsid));
                    }
                    if (intval($unit_price) < intval($servicecontracts['total'])) {
                        $sql = "update vtiger_servicecontracts set contractstate=? where servicecontractsid=? AND isautoclose='1'";
                        $adb->pquery($sql, array('0', $servicecontractsid));
                    }
                    $query="SELECT vtiger_crmentity.* FROM vtiger_crmentity WHERE crmid=?";
                    $result=$adb->pquery($query,array($servicecontractsid));
                    $resultdata=$adb->query_result_rowdata($result,0);
                    $sql = "UPDATE vtiger_receivedpayments SET modulename=? WHERE receivedpaymentsid = ?";
                    $adb->pquery($sql, array($resultdata['setype'], $recordModel->getId()));
                }
            }


            //添加
            if (empty($receivepayid)) {
                if(! empty($servicecontractsid)) {
                    $this->receivedpaymentsNote($recordModel->getId());
                }
            } else {
                //修改操作 并 修改了服务合同
                if ($old_relatetoid != $servicecontractsid) {
                    $this->receivedpaymentsNote($receivepayid);
                }
            }



            if($request->get('relationOperation')) {

                $loadUrl = $this->getParentRelationsListViewUrl($request);
                $loadUrl.='&tab_label=ReceivedPayments';
            } else if ($request->get('returnToList')) {
                $loadUrl = $recordModel->getModule()->getListViewUrl();
            } else {
                $loadUrl = $recordModel->getDetailViewUrl();
            }
            if(empty($loadUrl)){
                if($request->getHistoryUrl()){
                    $loadUrl=$request->getHistoryUrl();
                }else{
                    $loadUrl="index.php";
                }
            }
            header("Location: $loadUrl");
        }
	}
	
	// 回款匹配记录
	public function receivedpaymentsNote($receivepayid) {
		global $current_user;
		$receivedpaymentsNotesId = $this->db->getUniqueID("vtiger_receivedpayments_notes");
        $receivedpaymentsNotesData = array(
            'createtime'=>date('Y-m-d H:i:s'),
            'smownerid'=>$current_user->id,
            'receivedpaymentsid'=>$receivepayid,
            'notestype'=>'notestype2',
            'receivedpaymentsnotesid'=>$receivedpaymentsNotesId
        );
        //更新回款上的匹配时间用于计算商务的业绩
        $sql = "update vtiger_receivedpayments set matchdate=? where receivedpaymentsid=?";
        $this->db->pquery($sql, array(date('Y-m-d'),$receivepayid));
        $divideNames = array_keys($receivedpaymentsNotesData);
        $divideValues = array_values($receivedpaymentsNotesData);
        $this->db->pquery('INSERT INTO `vtiger_receivedpayments_notes` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
	}
}