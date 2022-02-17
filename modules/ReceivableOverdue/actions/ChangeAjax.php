<?php

class ReceivableOverdue_ChangeAjax_Action extends Vtiger_Action_Controller {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('exportData');
        $this->exposeMethod('collateReceivable');//核对
        $this->exposeMethod('batchCollateReceivable');//核对
        $this->exposeMethod('checkLog');//核对记录
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
        $listViewModel = ReceivableOverdue_ListView_Model::getInstance("ReceivableOverdue");
        $listViewModel->getSearchWhere();
        $ListData = $listViewModel->getListView();

        $moduleName = 'ReceivableOverdue';
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
        $filename = '逾期应收明细表';
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
            ->setCellValue('C1', '合同编号')
            ->setCellValue('D1', '业务类型')
            ->setCellValue('E1', '合同额')
            ->setCellValue('F1', '合同阶段')
            ->setCellValue('G1', '应收金额')
            ->setCellValue('H1', '收款情况')
            ->setCellValue('I1', '逾期天数')
            ->setCellValue('J1', '合同签订人')
            ->setCellValue('K1', '签订日期')
            ->setCellValue('L1', '应收时间')
            ->setCellValue('M1', '产品类型')
            ->setCellValue('N1', '最近一次跟进记录')
            ->setCellValue('O1', '最近一次跟进时间');
        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:O1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:Z1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $current=2;
        if(!empty($ListData)) {
            foreach ($ListData as $row){
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A' . $current, $current-1)
                    ->setCellValue('B' . $current, $row['accountid'])
                    ->setCellValue('C' . $current, $row['contract_no'])
                    ->setCellValue('D' . $current, vtranslate($row['bussinesstype'],'ServiceContracts'))
                    ->setCellValue('E' . $current, $row['contracttotal'])
                    ->setCellValue('F' . $current, $row['stageshow'])
                    ->setCellValue('G' . $current, $row['receiveableamount'])
                    ->setCellValue('H' . $current, vtranslate($row['collection'],'ReceivableOverdue'))
                    ->setCellValue('I' . $current, $row['overduedays'])
                    ->setCellValue('J' . $current, $row['last_name'])
                    ->setCellValue('K' . $current, $row['signdate'])
                    ->setCellValue('L' . $current, $row['receiverabledate'])
                    ->setCellValue('M' . $current, $row['productname'])
                    ->setCellValue('N' . $current, $row['commentcontent'])
                    ->setCellValue('O' . $current, $row['lastfollowtime']);
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A' . $current . ':O' . $current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $current++;
            }
        }

        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('逾期应收明细表'.$date);


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.'逾期应收明细表'.$date.'.xlsx"');
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

    /**
     * 导出数据
     * @param Vtiger_Request $request
     */
    public function exportData(Vtiger_Request $request) {
        set_time_limit(0);
        global $current_user,$root_directory,$adb,$currentView;
        $currentView='List';
        $listViewModel = Vtiger_ListView_Model::getInstance('ReceivableOverdue');
        $listQuery = $listViewModel->getQuery();
        $listViewModel->getSearchWhere();
        $listQuery.=$listViewModel->getUserWhere();
        $queryGenerator = $listViewModel->get('query_generator');

        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        /* 替换select字段 start */
        $listQuery = str_replace('(vtiger_products.productname) as productid',' ifnull( vtiger_products.productname, vtiger_servicecontracts.contract_type) AS productid',$listQuery);
        //最后跟进内容
        $listQuery = str_replace('vtiger_receivable_overdue.commentcontent,',
            'vtiger_modcomments.commentcontent as commentcontent,',$listQuery);
        //最后跟进时间
        $listQuery = str_replace('vtiger_receivable_overdue.lastfollowtime,',
            'vtiger_modcomments.addtime AS lastfollowtime,vtiger_receivable_overdue.contractid,'
            ,$listQuery);
        //核对结果
        $listQuery = str_replace("IF(vtiger_receivable_overdue.checkresult=1,'是','否') as checkresult,",
            "IF(vtiger_receivablecheck.checkresult IS NULL,'未核对',IF(vtiger_receivablecheck.checkresult=1,'符合','不符合')) as checkresult,",
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_receivable_overdue.checktime,',
            'vtiger_receivablecheck.checktime,',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_receivablecheck.collator,',
            "(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active' AND isdimission=0,'','[离职]'))) as last_name from vtiger_users where vtiger_receivablecheck.collator=vtiger_users.id) as collator,",
            $listQuery);
        //最后核对内容
        $listQuery = str_replace('vtiger_receivable_overdue.checkremark,',
            'vtiger_receivablecheck.remark AS checkremark,'
            ,$listQuery);
        /* 替换select字段 end */

        //替换from
        $listQuery = str_replace(' FROM vtiger_receivable_overdue',
            " FROM vtiger_receivable_overdue LEFT JOIN vtiger_receivablecheck ON vtiger_receivablecheck.type='ReceivableOverdue' AND vtiger_receivablecheck.relation_id = vtiger_receivable_overdue.contractid AND vtiger_receivablecheck.stage = vtiger_receivable_overdue.stage LEFT JOIN (SELECT * FROM (SELECT addtime, commentcontent, moduleid, modcommentpurpose FROM vtiger_modcomments WHERE modulename='ServiceContracts' ORDER BY addtime DESC) vtiger_modcomments GROUP BY moduleid,modcommentpurpose) vtiger_modcomments ON vtiger_modcomments.moduleid = vtiger_receivable_overdue.contractid AND vtiger_modcomments.modcommentpurpose = vtiger_receivable_overdue.stageshow",
            $listQuery);

        /* 替换where字段 start */
        //最后跟进内容
        $listQuery = str_replace('vtiger_receivable_overdue.commentcontent',
            'vtiger_modcomments.commentcontent',
            $listQuery);
        //最后跟进时间
        $listQuery = str_replace('vtiger_receivable_overdue.lastfollowtime',
            'vtiger_modcomments.addtime',
            $listQuery);
        //核对结果
        $listQuery = str_replace('vtiger_receivable_overdue.checkresult = -1 AND vtiger_receivable_overdue.checkresult IS NOT NULL',
            'vtiger_receivablecheck.checkresult IS NULL',
            $listQuery);
        $listQuery = str_replace('vtiger_receivable_overdue.checkresult',
            'vtiger_receivablecheck.checkresult',
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_receivable_overdue.checktime',
            'vtiger_receivablecheck.checktime',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_receivable_overdue.collator',
            'vtiger_receivablecheck.collator',
            $listQuery);
        //最后跟进内容
        $listQuery = str_replace('vtiger_receivable_overdue.checkremark',
            'vtiger_receivablecheck.remark',
            $listQuery);
        /* 替换where字段 end */

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
        $filename = $path.'逾期应收明细表'.date('Ymd').$current_user->id.'.csv';
        $array= array();
        foreach($headerArray as $key=>$value) {
            $array[] = iconv('utf-8','gb2312',vtranslate($key,'ReceivableOverdue'));
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
                        if($valueheader['columnname']=='collection'){
                            $currnetValue = uitypeformat($valueheader, $value[$valueheader['columnname']], 'ReceivableOverdue');
                            $currnetValue=vtranslate($currnetValue,'ReceivableOverdue');
                        }else{
                            $currnetValue = uitypeformat($valueheader, $value[$valueheader['columnname']], 'ReceivableOverdue');
                        }

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

    /**
     * 应收核对
     */
    public function collateReceivable(Vtiger_Request $request)
    {
        $checktype = $request->get('checktype');
        $contractid = $request->get('contractid');
        $stage = $request->get('stage');
        $checkresult = $request->get('checkresult');
        $remark = $request->get('remark');
        global $current_user,$adb;
        $query = "SELECT id FROM vtiger_receivablecheck WHERE type = ? AND relation_id = ? AND stage = ?";
        $result = $adb->pquery($query, [$checktype, $contractid, $stage]);
        $num = $adb->num_rows($result);
        $now = date('Y-m-d H:i:s');
        if ($num >0) {
            $row = $adb->query_result_rowdata($result);
            $query = 'UPDATE vtiger_receivablecheck SET remark=?, checkresult=?, collator=?, checktime=? WHERE id=?';
            $adb->pquery($query, [$remark, $checkresult, $current_user->id, $now, $row['id']]);
        } else {
            $query = 'INSERT INTO vtiger_receivablecheck(type, relation_id, stage, remark, checkresult, collator, checktime) VALUES (?, ?, ?, ?, ?, ?, ?)';
            $adb->pquery($query, [$checktype, $contractid, $stage, $remark, $checkresult, $current_user->id, $now]);
        }
        //插入核对日志
        $query = 'INSERT INTO vtiger_receivablecheck_log(type, relation_id, stage, remark, checkresult, collator, checktime) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $adb->pquery($query, [$checktype, $contractid, $stage, $remark, $checkresult, $current_user->id, $now]);

        $data = ['status'=>'success', 'msg'=>'成功核对'];
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 批量核对
     */
    public function batchCollateReceivable(Vtiger_Request $request)
    {
        global $current_user, $adb, $currentView;
        $currentView='List';
        $checkresult = $request->get('checkresult');
        $remark = $request->get('remark');
        $listViewModel = Vtiger_ListView_Model::getInstance('ReceivableOverdue');
        $listViewModel->getSearchWhere();
        $queryGenerator =$listViewModel->get('query_generator');
        //用户条件
        $where = $listViewModel->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listQuery .= " and vtiger_receivable_overdue.iscancel=0";
        //替换select
        $listQuery = str_replace('count(1) as counts',
            'vtiger_receivable_overdue.receivableoverdueid,vtiger_receivable_overdue.contractid,vtiger_receivable_overdue.stage,vtiger_receivablecheck.id AS receivablecheckid',
            $listQuery);
            //替换from
        $listQuery = str_replace(' FROM vtiger_receivable_overdue',
            " FROM vtiger_receivable_overdue LEFT JOIN vtiger_receivablecheck ON vtiger_receivablecheck.type='ReceivableOverdue' AND vtiger_receivablecheck.relation_id = vtiger_receivable_overdue.contractid AND vtiger_receivablecheck.stage = vtiger_receivable_overdue.stage LEFT JOIN (SELECT * FROM (SELECT addtime, commentcontent, moduleid, modcommentpurpose FROM vtiger_modcomments WHERE modulename='ServiceContracts' ORDER BY addtime DESC) vtiger_modcomments GROUP BY moduleid,modcommentpurpose) vtiger_modcomments ON vtiger_modcomments.moduleid = vtiger_receivable_overdue.contractid AND vtiger_modcomments.modcommentpurpose = vtiger_receivable_overdue.stageshow",
            $listQuery);
        /* 替换where字段 start */
        //最后跟进内容
        $listQuery = str_replace('vtiger_receivable_overdue.commentcontent',
            'vtiger_modcomments.commentcontent',
            $listQuery);
        //最后跟进时间
        $listQuery = str_replace('vtiger_receivable_overdue.lastfollowtime',
            'vtiger_modcomments.addtime',
            $listQuery);
        //核对结果
        $listQuery = str_replace('vtiger_receivable_overdue.checkresult = -1 AND vtiger_receivable_overdue.checkresult IS NOT NULL',
            'vtiger_receivablecheck.checkresult IS NULL',
            $listQuery);
        $listQuery = str_replace('vtiger_receivable_overdue.checkresult',
            'vtiger_receivablecheck.checkresult',
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_receivable_overdue.checktime',
            'vtiger_receivablecheck.checktime',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_receivable_overdue.collator',
            'vtiger_receivablecheck.collator',
            $listQuery);
        //最后跟进内容
        $listQuery = str_replace('vtiger_receivable_overdue.checkremark',
            'vtiger_receivablecheck.remark',
            $listQuery);
        /* 替换where字段 end */
        $result = $adb->pquery($listQuery, []);
        $num = $adb->num_rows($result);
        if ($num<=0) {
            $data = ['status'=>'error', 'msg'=>'未查到需核对的数据'];
        } elseif($num>1000) {
            $data = ['status'=>'error', 'msg'=>sprintf('当前共%d条数据,超过单次允许核对的最大记录数(1000)', $num)];
        } else {
            $now = date('Y-m-d H:i:s');
            $checktype = 'ReceivableOverdue';
            while ($row = $adb->fetchByAssoc($result)) {
                //判断之前是否核对过
                if (empty($row['receivablecheckid'])) {
                    $query = 'INSERT INTO vtiger_receivablecheck(type, relation_id, stage, remark, checkresult, collator, checktime) VALUES (?, ?, ?, ?, ?, ?, ?)';
                    $adb->pquery($query, [$checktype, $row['contractid'], $row['stage'], $remark, $checkresult, $current_user->id, $now]);
                } else {
                    $query = 'UPDATE vtiger_receivablecheck SET remark=?, checkresult=?, collator=?, checktime=? WHERE id=?';
                    $adb->pquery($query, [$remark, $checkresult, $current_user->id, $now, $row['receivablecheckid']]);
                }
                //插入核对日志
                $query = 'INSERT INTO vtiger_receivablecheck_log(type, relation_id, stage, remark, checkresult, collator, checktime) VALUES (?, ?, ?, ?, ?, ?, ?)';
                $adb->pquery($query, [$checktype, $row['contractid'], $row['stage'], $remark, $checkresult, $current_user->id, $now]);
            }
            $data = ['status'=>'success', 'msg'=>sprintf('成功核对%d条数据', $num)];
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 核对记录
     * @param Vtiger_Request $request
     */
    public function checkLog(Vtiger_Request $request)
    {
        global $adb;
        $type = $request->get('checktype');
        $contractid = $request->get('contractid');
        $stage = $request->get('stage');
        $query = "SELECT id, remark, IF(checkresult=1, '符合', '不符合') AS checkresult, (SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT departmentid FROM vtiger_user2department WHERE userid=vtiger_users.id LIMIT 1)),''),']',(IF(`status`='Active' AND isdimission=0,'','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_receivablecheck_log.collator=vtiger_users.id) AS collator, checktime FROM vtiger_receivablecheck_log WHERE type = ? AND relation_id = ? AND stage = ? ORDER BY id DESC";
        $result = $adb->pquery($query, [$type, $contractid, $stage]);
        $num = $adb->num_rows($result);
        $list = [];
        if ($num > 0)
        {
            while ($row = $adb->fetchByAssoc($result)) {
                $list[]= $row;
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($list);
        $response->emit();
    }
}
