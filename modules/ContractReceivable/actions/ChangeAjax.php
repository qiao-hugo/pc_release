<?php

class ContractReceivable_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct(){
        parent::__construct();
        $this->exposeMethod('exportData');
        $this->exposeMethod('getReceivableOverdueData');
        $this->exposeMethod('earlyWarningSavedata');
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
    public function exportDatabak(Vtiger_Request $request){
        global $currentView;
        $currentView='List';
        $listViewModel = ReceivableOverdue_ListView_Model::getInstance("ContractReceivable");
        $listViewModel->getSearchWhere();
        $ListData = $listViewModel->getListView();

        $moduleName = 'ContractReceivable';
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
        $filename = '合同应收明细表'.vtranslate($request->get('bussinesstype'),'ContractReceivable');
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
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A' . $current, $current-1)
                    ->setCellValue('B' . $current, $row['contractid'])
                    ->setCellValue('C' . $current, $row['accountid'])
                    ->setCellValue('D' . $current, vtranslate($row['bussinesstype'],'ServiceContracts'))
                    ->setCellValue('E' . $current, $row['productname'])
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
        header('Content-Disposition: attachment;filename="'.'合同应收明细表'.$date.'.xlsx"');
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
        $listViewModel = Vtiger_ListView_Model::getInstance('ContractReceivable');
        $listQuery = $listViewModel->getQuery();
        $listViewModel->getSearchWhere();
        $listQuery.=$listViewModel->getUserWhere();
        $queryGenerator = $listViewModel->get('query_generator');

        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $bussinesstype = $_REQUEST['bussinesstype'];
        if($bussinesstype=='bigsass'){
            $listQuery .= " and vtiger_contract_receivable.bussinesstype='".$bussinesstype."' ";
        }else{
            $listQuery .= " and vtiger_contract_receivable.bussinesstype in('smallsass','smallsassdirect')";
            $listQuery = str_replace("vtiger_servicecontracts.bussinesstype","vtiger_contract_receivable.bussinesstype",$listQuery);
        }
        $listQuery = str_replace('vtiger_contract_receivable.productid',"vtiger_servicecontracts.contract_type as productid",$listQuery);
        $listQuery .= " and vtiger_contract_receivable.iscancel=0";
        //核对结果
        $listQuery = str_replace("IF(vtiger_contract_receivable.checkresult=1,'是','否') as checkresult,",
            "IF(vtiger_receivablecheck.checkresult IS NULL,'未核对',IF(vtiger_receivablecheck.checkresult=1,'符合','不符合')) as checkresult,",
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_contract_receivable.checktime,',
            'vtiger_receivablecheck.checktime,',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_receivablecheck.collator,',
            "(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active' AND isdimission=0,'','[离职]'))) as last_name from vtiger_users where vtiger_receivablecheck.collator=vtiger_users.id) as collator,",
            $listQuery);
        //最后核对内容
        $listQuery = str_replace('vtiger_contract_receivable.checkremark,',
            'vtiger_receivablecheck.remark AS checkremark,'
            ,$listQuery);
        /* 替换select字段 end */

        //替换from
        $listQuery = str_replace(' FROM vtiger_contract_receivable',
            " FROM vtiger_contract_receivable LEFT JOIN vtiger_receivablecheck ON vtiger_receivablecheck.type='ContractReceivable' AND vtiger_receivablecheck.relation_id = vtiger_contract_receivable.contractid",
            $listQuery);

        /* 替换where字段 start */
        //核对结果
        $listQuery = str_replace('vtiger_contract_receivable.checkresult = -1 AND vtiger_contract_receivable.checkresult IS NOT NULL',
            'vtiger_receivablecheck.checkresult IS NULL',
            $listQuery);
        $listQuery = str_replace('vtiger_contract_receivable.checkresult',
            'vtiger_receivablecheck.checkresult',
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_contract_receivable.checktime',
            'vtiger_receivablecheck.checktime',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_contract_receivable.collator',
            'vtiger_receivablecheck.collator',
            $listQuery);
        //最后跟进内容
        $listQuery = str_replace('vtiger_contract_receivable.checkremark',
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
        $filename = $path.'合同应收明细表'.vtranslate($request->get('bussinesstype'),'ContractReceivable').date('Ymd').$current_user->id.'.csv';
        $array= array();
        foreach($headerArray as $key=>$value) {
            $array[] = iconv('utf-8','gb2312',vtranslate($key,'ContractReceivable'));
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
                        $currnetValue = uitypeformat($valueheader, $value[$valueheader['columnname']], 'ContractReceivable');
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
     * 开始~结束SQL
     * @param $request
     * @param $field
     * @return string
     */
    public function getDatetimeAndEndDatetimeSQL($request,$field, $splitjoint){
        $checkboxed=$request->get('checkboxed');
        $tempdate='';
        if($checkboxed==1){
            $datetime=$request->get('datetime');//开始日期
            $enddatetime=$request->get('enddatetime');//结束日期
            if(strtotime($datetime)>strtotime($enddatetime)){
                $tempdate=$splitjoint.$field." BETWEEN '{$enddatetime}' AND '{$datetime}'";
            }elseif(strtotime($datetime)<strtotime($enddatetime)){
                $tempdate=$splitjoint.$field." BETWEEN '{$datetime}' AND '{$enddatetime}'";
            }else{
                $tempdate=$splitjoint.$field."='{$datetime}'";
            }
        }

        return $tempdate;
    }

    /**
     * 逾期天数SQL
     * @param $request
     * @param $field
     * @return string
     */
    public function getOverduedaysSQL($request,$field,$splitjoint){
        $overduedaysStr='';
        $overduedays=$request->get('overduedays');//逾期天数
        $overduedayscondition=$request->get('overduedayscondition');//逾期条件
        if($overduedays>0){
            if($overduedayscondition=='gqt'){
                $overduedaysStr=$splitjoint.$field.'>='.$overduedays.' AND '.$field.'>0';
            }elseif($overduedayscondition=='lqt'){
                $overduedaysStr=$splitjoint.$field.'<='.$overduedays.' AND '.$field.'>0';
            }elseif($overduedayscondition=='qt'){
                $overduedaysStr=$splitjoint.$field.'='.$overduedays;
            }
        }
        return $overduedaysStr;
    }

    /**
     * 收款情况
     * @param $request
     * @param $field
     * @return string
     */
    public function getcollectionSQL($request,$field,$splitjoint){
        $collection=$request->get('collection');//收款情况
        $collectionStr='';
        if($collection=='all'){
            $collectionStr='';
        }elseif($collection=='normal'){
            $collectionStr=$splitjoint.$field."='normal'";
        }elseif($collection=='overduereceived'){
            $collectionStr=$splitjoint.$field."='overduereceived'";
        }elseif($collection=='overduecollection'){
            $collectionStr=$splitjoint.$field."='overdue'";
        }
        return $collectionStr;
    }

    /**
     * 业务类型
     * @param $request
     * @param $field
     * @return string
     */
    public function getBusinesstypeSQL($request,$field,$splitjoint){
        $businesstype=$request->get('businesstype');
        $businesstypeStr='';
        if($businesstype==0){
            $businesstypeStr='';
        }elseif($businesstype==1){
            $businesstypeStr=$splitjoint.$field." in('smallsassdirect','smallsass')";
        }elseif($businesstype==2){
            $businesstypeStr=$splitjoint.$field."='bigsass'";
        }
        return $businesstypeStr;
    }

    /**
     * 获取部门
     * @param $request
     * @param $field
     * @return string
     */
    public function getDepartments($request,$field,$splitjoint){
        $departmentid=$request->get('department');
        $departmentidStr='';
        if($departmentid!='null' && is_array($departmentid)){
            global $adb;
            $query="SELECT departmentid FROM vtiger_departments WHERE FIND_IN_SET(?,REPLACE(parentdepartment,'::',','))";
            $departmentidArr=array();
            foreach($departmentid as $value){
                $result=$adb->pquery($query,array($value));
                if($adb->num_rows($result)){
                    while($row=$adb->fetch_array($result)){
                        $departmentidArr[]=$row['departmentid'];
                    }
                }
            }
            if(!empty($departmentidArr)){
                $departmentidStr=$splitjoint.$field." IN('".implode("','",$departmentidArr)."')";
            }
        }
        return $departmentidStr;
    }
    public function getDepartmentsUserID($request,$field,$splitjoint){
        $departmentid=$request->get('department');
        $where=getAccessibleUsers('ServiceContracts','List',true);
        if($departmentid!='null' && is_array($departmentid)){
            $userid=array();
            foreach($departmentid as $value){
                $userid=array_merge(getDepartmentUser($value),$userid);
            }
            if($where!='1=1') {
                $userid = array_intersect($where, $userid);
            }
            if(empty($userid)){
                $userid=array(-1);
            }
            $userid=array_unique($userid);
            $departmentidStr=$splitjoint.$field." IN('".implode("','",$userid)."')";

        }else{
            if($where!='1=1') {
                $departmentidStr=$splitjoint.$field." IN('".implode("','",$where)."')";
            }else{
                $departmentidStr='';
            }
        }
        return $departmentidStr;
    }
    //获取报表数据
    public function getReceivableOverdueData(Vtiger_Request $request){
        $splitjoint=' AND ';
        $datetimeSQL=$this->getDatetimeAndEndDatetimeSQL($request,'vtiger_contracts_execution_detail.receiverabledate',$splitjoint);
        $overduedaysSQL=$this->getOverduedaysSQL($request,'vtiger_contracts_execution_detail.overduedays',$splitjoint);
        $collectionSQL=$this->getcollectionSQL($request,'vtiger_contracts_execution_detail.collection',$splitjoint);
        $businesstypeSQL=$this->getBusinesstypeSQL($request,'vtiger_servicecontracts.bussinesstype',$splitjoint);
        $departmentsSQL=$this->getDepartments($request,'vtiger_servicecontracts.signdempart',$splitjoint);
        $departmentsUserIDsql=$this->getDepartmentsUserID($request,'vtiger_servicecontracts.signid',$splitjoint);
        $query='SELECT 
                    sum(sumreceiveableamount) AS sumreceiveableamount,
                    sum(sumcontractreceivable) as sumcontractreceivable,
                    sum(vtiger_servicecontracts.total) as sumcontractamount,
                    st.departmentid,st.departmentname,
                    st.parentdepartment,
                    st.depth,
                    sum((SELECT sum(unit_price) FROM vtiger_receivedpayments WHERE receivedstatus=\'normal\' AND deleted=0 AND relatetoid=st.servicecontractsid)) as sumreceivepaymentsamount
                     FROM (SELECT
                        vtiger_servicecontracts.servicecontractsid,
                        vtiger_departments.departmentid,vtiger_departments.departmentname,parentdepartment,vtiger_departments.depth, 
                        sum(IF(vtiger_contracts_execution_detail.receiveableamount>0,vtiger_contracts_execution_detail.receiveableamount,0)) AS sumreceiveableamount,
                        sum(IF(vtiger_contracts_execution_detail.contractreceivable>0,vtiger_contracts_execution_detail.contractreceivable,0)) AS sumcontractreceivable
                    FROM vtiger_servicecontracts
                    LEFT JOIN vtiger_contracts_execution_detail ON vtiger_servicecontracts.servicecontractsid = vtiger_contracts_execution_detail.contractid
                    LEFT JOIN vtiger_departments ON vtiger_departments.departmentid = vtiger_servicecontracts.signdempart
                    WHERE vtiger_contracts_execution_detail.contractid > 0 AND vtiger_contracts_execution_detail.iscancel=0';

        $query.=$datetimeSQL.$overduedaysSQL.$collectionSQL.$businesstypeSQL.$departmentsSQL;
        $query.=" GROUP BY vtiger_servicecontracts.servicecontractsid) as st
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=st.servicecontractsid
                    WHERE 1=1".$departmentsUserIDsql."
                    GROUP BY st.departmentid
                    ORDER BY CONCAT(st.parentdepartment,':') DESC,st.depth DESC";
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($this->getperformance($query));
        $response->emit();
    }

    public function earlyWarningSavedata($request){
        global $adb;
        $Overduewarning=$request->get('Overduewarning');
        $isclose=$request->get('isclose');
        $isclose1=$request->get('isclose1');
        $rbexp=$request->get('rbexp');
        $forwardday=$request->get('forwardday');
        $forwardday=$forwardday>1?$forwardday:1;
        $isclose=$isclose==1?$isclose:0;
        $isclose1=$isclose1==1?$isclose1:0;
        $Overduewarning=is_array($Overduewarning)?implode(',',$Overduewarning):'';
        $rbexp=is_array($rbexp)?implode(',',$rbexp):'';
        $sql='UPDATE `vtiger_earlywarningsetting` SET `forwardday`=?, `alertchannels`=?, `isclose`=? WHERE `earlywarningsettingid`=?';
        $adb->pquery($sql,array($forwardday,$rbexp,$isclose,1));
        $adb->pquery($sql,array(0,$Overduewarning,$isclose1,2));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }

    public function getperformance($query){
        global $adb,$root_directory;
        $result=$adb->pquery($query,array());
        $num_rows=$adb->num_rows($result);
        if($num_rows){
            $tableTr='';
            $leavelNum=6;
            $departmentsArray=array();
            $departmentsNum=array();
            $sumcontractamount=0;
            $sumreceivepaymentsamount=0;
            $sumreceiveableamount=0;
            $sumcontractreceivable=0;
            $allsumcontractamount=0;
            $allsumreceivepaymentsamount=0;
            $allsumreceiveableamount=0;
            $allsumcontractreceivable=0;
            $secorddepartment='';
            $falgkey=1;
            include $root_directory.'crmcache/departmentanduserinfo.php';
            while($row=$adb->fetchByAssoc($result)){

                $TdArray[0]='';
                $TdArray[1]='';
                $TdArray[2]='';
                $TdArray[3]='';
                $TdArray[4]='';
                $TdArray[5]='';
                $TdArray[6]='';
                $TdArray[7]='';
                if($row['depth']==0 && $row['departmentid']!='H1'){
                    continue;
                }
                $parentdepartment=explode('::',$row['parentdepartment']);
                foreach($parentdepartment as $key=>$value){
                    if($row['depth']>0){
                        if(!in_array($value,$departmentsArray)){
                            $colspan=$key!=$row['depth']?'':' colspan="'.($leavelNum-$row['depth']).'"';
                            $TdArray[$key]='<td rowspan="#'.$value.'#"'.$colspan.' style="text-align: center;vertical-align:middle;">'.$cachedepartment[$value].'</td>';
                            $departmentsNum[$value]=1;
                            $departmentsArray[]=$value;
                        }else{
                            $TdArray[$key]='';
                            $departmentsNum[$value]++;
                        }
                    }else{
                        if(!in_array("A".$value.'A',$departmentsArray)){
                            $colspan=$key!=$row['depth']?'':' colspan="'.($leavelNum-$row['depth']-1).'"';
                            $TdArray[$key]='<td rowspan="#A'.$value.'#A"'.$colspan.' style="text-align: center;vertical-align:middle;">'.$cachedepartment[$value].'</td>';
                            $departmentsNum["A".$value.'A']=1;
                            $departmentsArray[]="A".$value.'A';
                            if(!in_array($value,$departmentsArray)){
                                $departmentsNum[$value]=1;
                            }else{
                                $departmentsNum[$value]++;
                            }
                        }else{
                            $TdArray[$key]='';
                            $departmentsNum[$value]++;
                        }
                    }
                }
                if($secorddepartment!=$parentdepartment[1]){
                    $newsumcontractamount=$sumcontractamount;
                    $newsumreceivepaymentsamount=$sumreceivepaymentsamount;
                    $newsumreceiveableamount=$sumreceiveableamount;
                    $newsumcontractreceivable=$sumcontractreceivable;
                    $sumcontractamount=0;
                    $sumreceivepaymentsamount=0;
                    $sumreceiveableamount=0;
                    $sumcontractreceivable=0;
                }
                $sumcontractamount+=$row['sumcontractamount'];
                $sumreceivepaymentsamount+=$row['sumreceivepaymentsamount'];
                $sumreceiveableamount+=$row['sumreceiveableamount'];
                $sumcontractreceivable+=$row['sumcontractreceivable'];
                $allsumcontractamount+=$row['sumcontractamount'];
                $allsumreceivepaymentsamount+=$row['sumreceivepaymentsamount'];
                $allsumreceiveableamount+=$row['sumreceiveableamount'];
                $allsumcontractreceivable+=$row['sumcontractreceivable'];
                $MasttableTr='<tr>'.$TdArray[0].$TdArray[1].$TdArray[2].$TdArray[3].$TdArray[4].$TdArray[5].$TdArray[6].$TdArray[7].'
                        <td colspan="1" style="text-align: center;vertical-align:middle;">'.$row['sumcontractamount'].'</td>
                        <td colspan="1" style="text-align: center;vertical-align:middle;">'.$row['sumreceivepaymentsamount'].'</td>
                        <td colspan="1" style="text-align: center;vertical-align:middle;">'.$row['sumreceiveableamount'].'</td>
                        <td colspan="1" style="text-align: center;vertical-align:middle;">'.$row['sumcontractreceivable'].'</td>
                    </tr>';
                if($falgkey==1){
                    $secorddepartment=$parentdepartment[1];
                }
                if($secorddepartment!=$parentdepartment[1]){
                    $tableTr.='<tr>
                    <td colspan="'.($leavelNum-1).'" style="text-align: center;vertical-align:middle;"><span class="label label-a_normal">'.$cachedepartment[$secorddepartment].'<span></span></td>
                    <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-c_stamp">'.$newsumcontractamount.'</span></td>
                    <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-c_stamp">'.$newsumreceivepaymentsamount.'</span></td>
                    <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-c_stamp">'.$newsumreceiveableamount.'</span></td>
                    <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-c_stamp">'.$newsumcontractreceivable.'</span></td>
                </tr>'.$MasttableTr;
                    $secorddepartment=$parentdepartment[1];
                    $departmentsNum[$parentdepartment[0]]++;
                }else{
                    $tableTr.=$MasttableTr;
                }
                if($falgkey==$num_rows){
                    $tableTr.='<tr>
                    <td colspan="'.($leavelNum-1).'" style="text-align: center;vertical-align:middle;"><span class="label label-a_normal">'.$cachedepartment[$parentdepartment[1]].'</span></td>
                    <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-c_stamp">'.$sumcontractamount.'</span></td>
                    <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-c_stamp">'.$sumreceivepaymentsamount.'</span></td>
                    <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-c_stamp">'.$sumreceiveableamount.'</span></td>
                    <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-c_stamp">'.$sumcontractreceivable.'</span></td>
                </tr>';
                    $departmentsNum[$parentdepartment[0]]++;
                }
                $falgkey++;
            }
            $deapartmentkeyarray=array_keys($departmentsNum);
            $deapartmentkeyarray=array_map(function($v){return '#'.$v.'#';},$deapartmentkeyarray);
            $tableTr=str_replace($deapartmentkeyarray,$departmentsNum,$tableTr);
            $tableTr.='<tr>
                        <td colspan="'.($leavelNum).'" style="text-align: center;vertical-align:middle;"><span class="label label-a_normal">'.$cachedepartment['H1'].'<span class="label label-a_normal"></td>
                        <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-b_actioning">'.$allsumcontractamount.'</span></td>
                        <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-b_actioning">'.$allsumreceivepaymentsamount.'</span></td>
                        <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-b_actioning">'.$allsumreceiveableamount.'</span></td>
                        <td colspan="1" style="text-align: center;vertical-align:middle;"><span class="label label-b_actioning">'.$allsumcontractreceivable.'</span></td>
                    </tr>';
            $table='<table class="table table-bordered table-striped" id="one1">
                    <thead>
                        <tr id="flalte1"  style="background-color:#ffffff;">
                            <th colspan="'.($leavelNum).'" style="text-align: center;vertical-align:middle;">部门</th>
                            <th colspan="1" style="text-align: center;vertical-align:middle;">合同金额</th>
                            <th colspan="1" style="text-align: center;vertical-align:middle;">实收金额</th>
                            <th colspan="1" style="text-align: center;vertical-align:middle;">应收金额</th>
                            <th colspan="1" style="text-align: center;vertical-align:middle;">应收余额</th>
                        </tr>
                    </thead>
                    <tbody>
                    '.$tableTr.'
                    </tbody>
                </table>';
        }else{

            $table='<table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="text-align: center;vertical-align:middle;">没有记录</th>
                    </tr></thead></table>';
        }
        return $table;
    }
}
