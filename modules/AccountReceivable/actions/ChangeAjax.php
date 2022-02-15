<?php

class AccountReceivable_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct(){
        parent::__construct();
        $this->exposeMethod('exportData');
        $this->exposeMethod('relationAccount');
        $this->exposeMethod('batchCollateReceivable');//批量核对
    }

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
       $mode=$request->getMode();
        if(!empty($mode)){
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
	}

    public function exportDatabak(){
        global $currentView;
        $currentView='List';
        $listViewModel = AccountReceivable_ListView_Model::getInstance("AccountReceivable");
        $listViewModel->getSearchWhere();
        $ListData = $listViewModel->getListView();

        $moduleName = 'AccountReceivable';
//        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
//            if(!$moduleModel->exportGrouprt('ServiceContracts','ExportRIV')){   //权限验证
//                return;
//            }

        ob_clean();                              //清空缓存
        header('Content-type: text/html;charset=utf-8');
        $date = date('Ymd');
        global $root_directory;
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        set_time_limit(0);
        ini_set('memory_limit','2048M');
        $path=$root_directory.'temp/';
        $filename = '客户运营应收总表';
        $filename = (strtolower(substr(PHP_OS,0,3))=='win') ? mb_convert_encoding($filename,'gbk','UTF-8') : $filename;
        $filename=$path.$filename.$date.'.xlsx';
        !is_dir($path)&&mkdir($path,'0777',true);
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
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '客户名称')
            ->setCellValue('C1', '合同数')
            ->setCellValue('D1', '业务大类数')
            ->setCellValue('E1', '总合同额')
            ->setCellValue('F1', '合同应收金额')
            ->setCellValue('G1', '合同实收金额')
            ->setCellValue('H1', '合同应收余额')
            ->setCellValue('I1', '合同开票金额')
            ->setCellValue('J1', '合计逾期应收余额')
            ->setCellValue('K1', '状态');
        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:K1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $current=2;
        if(!empty($ListData)) {
            foreach ($ListData as $row){
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A' . $current, $current-1)
                    ->setCellValue('B' . $current, $row['accountid'])
                    ->setCellValue('C' . $current, $row['contractnum'])
                    ->setCellValue('D' . $current, $row['bussinesstypenum'])
                    ->setCellValue('E' . $current, $row['contracttotal'])
                    ->setCellValue('F' . $current, $row['contractreceivableamount'])
                    ->setCellValue('G' . $current, $row['contractpaidamount'])
                    ->setCellValue('H' . $current, $row['contractreceivablebalance'])
                    ->setCellValue('I' . $current, $row['contractinvoiceamount'])
                    ->setCellValue('J' . $current, $row['contractoverduebalance'])
                    ->setCellValue('K' . $current, vtranslate($row['receivestatus'],'AccountReceivable'));
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A' . $current . ':K' . $current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $current++;
            }
        }

        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('客户运营应收总表'.$date);


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);



        $listViewModel = ReceivableOverdue_ListView_Model::getInstance("ContractReceivable");
        $listViewModel->getSearchWhere();
        $ListData = $listViewModel->getListView();

        $phpexecl->createSheet();

        // 添加头信处
        $phpexecl->setActiveSheetIndex(1)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '合同编号')
            ->setCellValue('C1', '客户名称')
            ->setCellValue('D1', '业务类型')
            ->setCellValue('E1', '产品类型')
            ->setCellValue('F1', '合同签订人')
            ->setCellValue('G1', '签订部门')
            ->setCellValue('H1', '合同额')
            ->setCellValue('I1', '合同收款金额')
            ->setCellValue('J1', '合同开票总额')
            ->setCellValue('K1', '合同状态')
            ->setCellValue('L1', '框架合同')
            ->setCellValue('M1', '应收金额')
            ->setCellValue('N1', '应收余额')
            ->setCellValue('O1', '收款情况')
            ->setCellValue('P1', '签订日期');
        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:P1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:Z1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $current=2;
        if(!empty($ListData)) {
            foreach ($ListData as $row){
                $phpexecl->setActiveSheetIndex(1)
                    ->setCellValue('A' . $current, $current-1)
                    ->setCellValue('B' . $current, $row['contractid'])
                    ->setCellValue('C' . $current, $row['accountid'])
                    ->setCellValue('D' . $current, vtranslate($row['bussinesstype'],'ServiceContracts'))
                    ->setCellValue('E' . $current, $row['productid'])
                    ->setCellValue('F' . $current, $row['signname'])
                    ->setCellValue('G' . $current, $row['signdempart'])
                    ->setCellValue('H' . $current, $row['contracttotal'])
                    ->setCellValue('I' . $current, $row['contractpaidamount'])
                    ->setCellValue('J' . $current, $row['contractinvoiceamount'])
                    ->setCellValue('K' . $current, vtranslate($row['modulestatus'],'ServiceContracts'))
                    ->setCellValue('L' . $current, vtranslate($row['frameworkcontract'],'ContractReceivable'))
                    ->setCellValue('M' . $current, $row['contractreceivableamount'])
                    ->setCellValue('N' . $current, $row['contractreceivablebalance'])
                    ->setCellValue('O' . $current, vtranslate($row['collectionstatus'],'ContractReceivable'))
                    ->setCellValue('P' . $current, $row['signdate']);
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A' . $current . ':P' . $current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $current++;
            }
        }

        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('合同应收明细表'.$date);


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.'客户运营应收总表'.$date.'.xlsx"');
        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');


        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
//        $objWriter->save('php://output');

        $objWriter->save($filename);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    public function exportData(Vtiger_Request $request) {
        set_time_limit(0);
        global $current_user,$root_directory,$adb,$currentView;
        $currentView='List';
        $listViewModel = Vtiger_ListView_Model::getInstance('AccountReceivable');
        $listQuery = $listViewModel->getQuery();
        $listViewModel->getSearchWhere();
        $listQuery.=$listViewModel->getUserWhere();
        $queryGenerator = $listViewModel->get('query_generator');

        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $listQuery = $listViewModel->replaceSQL($listQuery);
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    if($listViewHeaders[$key]['ishidden']){
                        continue;
                    }
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)) {
            $temp = $listViewHeaders;
        }
        $headerArray = $temp;
        ini_set('memory_limit','1024M');
        $path = $root_directory.'temp/';
        !is_dir($path) && mkdir($path,'0755',true);
        $filename = $path.'客户运营应收总表'.date('Ymd').$current_user->id.'.csv';
        $array= array();
        foreach($headerArray as $key=>$value) {
            $array[] = iconv('utf-8','gb2312',vtranslate($key,'AccountReceivable'));
        }
        $fp = fopen($filename,'w');
        fputcsv($fp, $array);
        $limit = 5000;
        $i = 0;
        while(true){
            $limitSQL = " limit " . $i * $limit . ",". $limit;
            $i++;
            $result = $adb->pquery($listQuery . $limitSQL, array());
            if($adb->num_rows($result)){
                while ($value = $adb->fetch_array($result)) {
                    $array = array();
                    foreach ($headerArray as $keyheader => $valueheader) {
                        if($valueheader['ishidden']) {
                            continue;
                        }
                        $currnetValue = uitypeformat($valueheader, $value[$valueheader['columnname']], 'AccountReceivable');
                        $currnetValue=preg_replace('/<[^>]*>/','',$currnetValue);
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
        }
        fclose($fp);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

    public function relationAccount(Vtiger_Request $request){
        $accountreceivableid = $request->get('accountreceivableid');
        $type = $request->get('type');
        global $adb;
        $recordModel = AccountReceivable_Record_Model::getInstanceById($accountreceivableid,'AccountReceivable');
        $accountid = $recordModel->get('accountid');


        $sql = "select ".$type." from vtiger_contract_receivable where accountid=? and iscancel=0 group by ".$type;
        $result = $adb->pquery($sql,array($accountid));
        $bussinessType = array();
        if($adb->num_rows($result)){
            while ($row = $adb->fetchByAssoc($result)){
                $bussinessType[] = vtranslate($row[$type],'ContractExecution');
            }
        }
        $response=new Vtiger_Response();
        $response->setResult($bussinessType);
        $response->emit();
    }

    /**
     * 批量核对
     */
    public function batchCollateReceivable(Vtiger_Request $request)
    {
        global $current_user, $adb, $currentView;
        $currentView = 'List';
        $checkresult = $request->get('checkresult');
        $remark = $request->get('remark');
        $listViewModel = Vtiger_ListView_Model::getInstance('AccountReceivable');
        $listViewModel->getSearchWhere();
        $queryGenerator =$listViewModel->get('query_generator');
        //用户条件
        $where = $listViewModel->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery = $queryGenerator->getQueryCount();
        $listQuery = $listViewModel->replaceSQL($listQuery);
        $listQuery = str_replace('count(1) as counts',
            'vtiger_account_receivable.accountreceivableid, vtiger_account_receivable.accountid, vtiger_receivablecheck.id AS receivablecheckid',
            $listQuery);
        $result = $adb->pquery($listQuery, []);
        $num = $adb->num_rows($result);
        if ($num<=0) {
            $data = ['status'=>'error', 'msg'=>'未查到需核对的数据'];
        } elseif($num>1000) {
            $data = ['status'=>'error', 'msg'=>sprintf('当前共%d条数据,超过单次允许核对的最大记录数(1000)', $num)];
        } else {
            $now = date('Y-m-d H:i:s');
            $checktype = 'AccountReceivable';
            while ($row = $adb->fetchByAssoc($result)) {
                //判断之前是否核对过
                if (empty($row['receivablecheckid'])) {
                    $query = 'INSERT INTO vtiger_receivablecheck(type, relation_id, stage, remark, checkresult, collator, checktime) VALUES (?, ?, ?, ?, ?, ?, ?)';
                    $adb->pquery($query, [$checktype, $row['accountid'], 0, $remark, $checkresult, $current_user->id, $now]);
                } else {
                    $query = 'UPDATE vtiger_receivablecheck SET remark=?, checkresult=?, collator=?, checktime=? WHERE id=?';
                    $adb->pquery($query, [$remark, $checkresult, $current_user->id, $now, $row['receivablecheckid']]);
                }
                //插入核对日志
                $query = 'INSERT INTO vtiger_receivablecheck_log(type, relation_id, stage, remark, checkresult, collator, checktime) VALUES (?, ?, ?, ?, ?, ?, ?)';
                $adb->pquery($query, [$checktype, $row['accountid'], 0, $remark, $checkresult, $current_user->id, $now]);
            }
            $data = ['status'=>'success', 'msg'=>sprintf('成功核对%d条数据', $num)];
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
