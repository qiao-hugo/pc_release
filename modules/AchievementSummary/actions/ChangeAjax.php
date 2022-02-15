<?php
class AchievementSummary_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('confirmEnd');
        $this->exposeMethod('cancelConfirmEnd');
        $this->exposeMethod('applicationUpdateAchievement');
        $this->exposeMethod('exportFinanceCsv');
        $this->exposeMethod('updateAchievement');
        $this->exposeMethod('exportData');//暂扣交付发放导出
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
     * 申请调整业绩金额
     */
    public function applicationUpdateAchievement(Vtiger_Request $request){
        global $adb;
        $adjustachievement=$request->get("adjustachievement");
        $remarks=$request->get("remarks");
        $record=$request->get("record");
        do{
            $sql=" SELECT * FROM  vtiger_achievementsummary WHERE achievementid=? LIMIT 1 ";
            $detailInfo = $adb->pquery($sql,array($record));
            $result=$adb->query_result_rowdata($detailInfo,0);
            if($result['modulestatus']=='b_actioning'){
                $result=array('success'=>0,'message'=>"状态审核中不能调整业绩！");
                break;
            }
            if($result['confirmstatus']=='confirmed'){
                $result=array('success'=>0,'message'=>"已完结不能调整业绩！");
                break;
            }
            if($result['realarriveachievement']<$adjustachievement){
                $result=array('success'=>0,'message'=>"实际到账业绩金额不能小于业绩调整金额！");
                break;
            }
            $recordModel=Vtiger_Record_Model::getInstanceById($record,'AchievementSummary');
            if(!empty($result['crmid'])){
                $salesorderid=$result['crmid'];
                $sql=" UPDATE `vtiger_achievementsummary` SET `adjustachievementrecord`=?,remarks=? WHERE (`achievementid`=?) LIMIT 1 ";
                $adb->pquery($sql,array($adjustachievement,$remarks,$record));
            }else{
                $crmid=$adb->getUniqueID("vtiger_crmentity");
                $sql=" UPDATE `vtiger_achievementsummary` SET `adjustachievementrecord`=? ,crmid=?,remarks=? WHERE (`achievementid`=?) LIMIT 1 ";
                $adb->pquery($sql,array($adjustachievement,$crmid,$remarks,$record));
                $salesorderid=$crmid;
            }
            //本地测试工作流
            $workflowsid=2426427;
            //先删除已经生成的工作流
            $sql=" DELETE  FROM  vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowsid=? ";
            $adb->pquery($sql,array($salesorderid,$workflowsid));
            $recordModel->entity->makeWorkflows('AchievementSummary', $workflowsid,$salesorderid,false);
            //更新日志记录
            $currentTime = date('Y-m-d H:i:s');
            global $current_user;
            //更新记录
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $record, 'AchievementSummary', $current_user->id,$currentTime, 0));
            if(!$result['adjustachievement']) $result['adjustachievement']=0;
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?),(?,?,?,?)',
                Array($id,'adjustachievement',$result['adjustachievement'],$adjustachievement+$result['adjustachievement'],$id,'modulestatus','','b_actioning',$id,'remarks','',$remarks));
            $result=array('success'=>1,'message'=>"已申请");
        }while(false);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    public function confirmEnd(Vtiger_Request $request){
        global $adb;
        $module = $request->getModule();
        $achievementids = $request->get('achievementids');
        foreach ($achievementids as $achievementid){
            $recordModel = Vtiger_Record_Model::getInstanceById($achievementid,$module);
            $recordModel->set('achievementid',$achievementid);
            $recordModel->set('confirmstatus','confirmed');
            $recordModel->set('mode','edit');
            $recordModel->save();

            //完结详情表里面的内容
            $sql = "update vtiger_achievementallot_statistic set isover=? where receivedpaymentownid=? and achievementmonth=?";
            $adb->pquery($sql,array(1,$recordModel->get('userid'),$recordModel->get('achievementmonth')));
        }
        $response = new Vtiger_Response();
        $response->setResult(array(1));
        $response->emit();
    }

    public function cancelConfirmEnd(Vtiger_Request $request){
        global $adb;
        $module = $request->getModule();
        $achievementids = $request->get('achievementids');
        foreach ($achievementids as $achievementid){
            $recordModel = Vtiger_Record_Model::getInstanceById($achievementid,$module);
            $recordModel->set('achievementid',$achievementid);
            $recordModel->set('confirmstatus','tobeconfirm');
            $recordModel->set('mode','edit');
            $recordModel->save();
            //完结详情表里面的内容
            $sql = "update vtiger_achievementallot_statistic set isover=? where receivedpaymentownid=? and achievementmonth=?";
            $adb->pquery($sql,array(0,$recordModel->get('userid'),$recordModel->get('achievementmonth')));
        }
        $response = new Vtiger_Response();
        $response->setResult(array(1));
        $response->emit();
    }

    /**
     * 把查询到的数据保存到xslx文件(替换以前的csv)
     * @param Vtiger_Request $request
     */
    public function exportFinanceCsv(Vtiger_Request $request) {
        set_time_limit(0);
        global $current_user,$root_directory,$adb,$currentView;
        $currentView='List';
        $listViewModel = Vtiger_ListView_Model::getInstance('AchievementSummary');
        $listQuery = $listViewModel->getQuery();
        $listViewModel->getSearchWhere();
        $listQuery.=$this->getUserWhere();
        $queryGenerator = $listViewModel->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }

        $orderBy = $listViewModel->getForSql('orderby');
        $sortOrder = $listViewModel->getForSql('sortorder');
        if (empty($orderBy) && empty($sortOrder)) {
            $orderBy = 'vtiger_departments.parentdepartment DESC,vtiger_achievementsummary.achievementmonth DESC,vtiger_achievementsummary.createtime';
            $sortOrder = 'DESC';
        }
        $listQuery=str_replace(',vtiger_achievementsummary.achievementid FROM vtiger_achievementsummary',',vtiger_achievementsummary.achievementid FROM vtiger_achievementsummary LEFT JOIN vtiger_achievementsupdate ON (vtiger_achievementsupdate.uuserid=vtiger_achievementsummary.userid AND vtiger_achievementsupdate.uachievementmonth=vtiger_achievementsummary.achievementmonth AND vtiger_achievementsupdate.uachievementtype=vtiger_achievementsummary.achievementtype AND vtiger_achievementsupdate.uperformancetype=vtiger_achievementsummary.performancetype AND vtiger_achievementsupdate.deleted=0)',$listQuery);

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        //有业绩月份搜索时，需要替换这个条件
        $getMonth=$listViewModel->getSearchWhereAchievementmonth();
        if($getMonth){
            $getMonths=$getMonth;
            $str="vtiger_achievementsummary.achievementmonth >= '".$getMonth." 00:00:00'";
            $getMonth="vtiger_achievementsummary.achievementmonth >= '".$getMonth."'";
            $listQuery=str_replace($str,$getMonth,$listQuery);
            $str="vtiger_achievementsummary.achievementmonth <= '".$getMonths." 00:00:00'";
            $getMonths="vtiger_achievementsummary.achievementmonth <= '".$getMonths."'";
            $listQuery=str_replace($str,$getMonths,$listQuery);
        }

        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();

        include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $phpexecl=new PHPExcel();
        $phpexecl->getProperties()->setCreator("liu ganglin")
            ->setLastModifiedBy("liu ganglin")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("AccountPlatform");

        $headerCodes = getExcelHeaderCode(count($listViewHeaders)+5);
        $headerArray = [];
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    if($listViewHeaders[$key]['ishidden']){
                        continue;
                    }
                    $headerArray[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($headerArray)){
            $headerArray = $listViewHeaders;
        }
        $step = 0;
        foreach($headerArray as $key => $val){
            if($val['ishidden']){
                continue;
            }
            $headerTitle = vtranslate($key,'AchievementSummary');
            $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step].'1',$headerTitle);
            $step++;
            //导出表格在“区域”后增加体系
            if($key == 'invoicecompany'){
                $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step].'1','一级部门');
                $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+1].'1','二级部门');
                $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+2].'1','三级部门');
                $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+3].'1','四级部门');
                $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+4].'1','五级部门');
                $step += 5;
            }
            
        }

        ini_set('memory_limit','512M');
        $listViewModel->isAllCount = 1;
        $limit = 1000;
        $i=0;
        include 'crmcache/departmentanduserinfo.php';
        $current = 2;
        $depar = array(
            ['value'=>'','start'=>'','end'=>''],
            ['value'=>'','start'=>'','end'=>''],
            ['value'=>'','start'=>'','end'=>''],
            ['value'=>'','start'=>'','end'=>''],
            ['value'=>'','start'=>'','end'=>''],
        );
        while (1) {
            $limitSQL = " limit " . $i * $limit . ",". $limit;
            $i++;
            $result = $adb->pquery($listQuery . $limitSQL, array());
            $num=$adb->num_rows($result);
            if(0==$num){
                break;
            }
            while ($value = $adb->fetch_array($result)) {
                $departmentid = $value['departmentid_reference'];
                $step = 0;
                foreach ($headerArray as $keyheader => $valueheader) {
                    if($valueheader['ishidden']) {
                        continue;
                    }
                    $currnetValue = uitypeformat($valueheader, $value[$valueheader['columnname']], 'AchievementSummary');
                    $phpexecl->setActiveSheetIndex(0)->setCellValueExplicit($headerCodes[$step].$current, $currnetValue);
                    $step++;
                    //导出表格增加体系
                    if($keyheader == 'invoicecompany'){
                        $parentdepartment = $departmenttoparent[$departmentid];
                        $parentDepartmentArr = explode('::', $parentdepartment);
                        $parentDepartmentArr = array_values(array_diff($parentDepartmentArr, ['H1']));
                        for ($j=0; $j < 5; $j++) {
                            if(!isset($parentDepartmentArr[$j])){
                                $departmentsName = '';
                            }else{
                                $departmentsName = $cachedepartment[$parentDepartmentArr[$j]];
                            }
                            $phpexecl->setActiveSheetIndex(0)->setCellValueExplicit($headerCodes[$step+$j].$current, $departmentsName);

                            if($departmentsName != $depar[$j]['value'] && $depar[$j]['start'] != $depar[$j]['end']){
                                $phpexecl->setActiveSheetIndex(0)->mergeCells($depar[$j]['start'].':'.$depar[$j]['end']); 
                                $phpexecl->setActiveSheetIndex(0)->getStyle($depar[$j]['start'])->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            }

                            if($departmentsName == ''){
                                $depar[$j]['value'] = '';
                                $depar[$j]['end'] = $headerCodes[$step+$j].$current;
                                $depar[$j]['start'] = $headerCodes[$step+$j].$current;
                            }else{
                                if($departmentsName == $depar[$j]['value']){
                                    $depar[$j]['end'] = $headerCodes[$step+$j].$current;
                                }else{
                                    $depar[$j]['value'] = $departmentsName;
                                    $depar[$j]['start'] = $headerCodes[$step+$j].$current;
                                    $depar[$j]['end'] = $headerCodes[$step+$j].$current;
                                }
                            }
                        }
                        $step += 5;
                    }
                }
                $current++;
            }
            if($num!=$limit){break;}
        }
        //合并最后的相同部门
        foreach($depar as $val){
            if($val['start'] != $val['end']){
                $phpexecl->setActiveSheetIndex(0)->mergeCells($val['start'].':'.$val['end']); 
                $phpexecl->setActiveSheetIndex(0)->getStyle($val['start'])->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            }
        }

        // 设置工作表的名称
        $phpexecl->getActiveSheet()->setTitle('销售业绩汇总表');
        $phpexecl->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        
        global $root_directory,$site_URL,$current_user;
        $path = $root_directory.'temp/';
        !is_dir($path) && mkdir($path,'0755',true);
        $filename = $path.'achievementsummary'.$current_user->id.'.xlsx';
        @unlink($filename);
        $objWriter->save($filename);

        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
        exit;
    }

    public function exportData(Vtiger_Request $request) {
        set_time_limit(0);
        global $current_user,$root_directory,$adb,$currentView;
        $yearmonth=$request->get('yearmonth');
        $source=$request->get('source');
        $currentView='List';
        if($source=='grantdata'){//交付发放
            $query='SELECT
               vtiger_withholdroyalty.amountofmoney,vtiger_withholdroyalty.confirmationdate, vtiger_achievementallot_statistic.receivedpaymentsid,
                vtiger_achievementallot_statistic.contract_no,
            (SELECT  departmentname FROM vtiger_departments left join vtiger_user2department ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE vtiger_user2department.userid=vtiger_withholdroyalty.userid)as departmentname,
                #sum(amountofmoney),
            (SELECT accountname FROM vtiger_account LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid WHERE vtiger_servicecontracts.servicecontractsid=vtiger_achievementallot_statistic.servicecontractid) as accountname,
                last_name
            FROM
                vtiger_withholdroyalty
            LEFT JOIN vtiger_achievementallot_statistic ON vtiger_achievementallot_statistic.achievementallotid = vtiger_withholdroyalty.achievementallotid
            LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_withholdroyalty.userid
            WHERE
                amountofmoney > 0
            AND vtiger_achievementallot_statistic.achievementallotid>0
            AND left(vtiger_withholdroyalty.confirmationdate,7)=?';
            $fields=array('contract_no'=>'合同编号','accountname'=>'客户名称','amountofmoney'=>'金额','confirmationdate'=>'交付日期','last_name'=>'姓名','departmentname'=>'部门');
            $sourceName='交付发放明细';
        }else{//暂扣
            $query='SELECT contract_no,twentyroyalty,concat(vtiger_achievementallot_statistic.achievementmonth,\'-01\') as achievementmonth,(SELECT  departmentname FROM vtiger_departments left join vtiger_user2department ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE vtiger_user2department.userid=vtiger_achievementallot_statistic.receivedpaymentownid) as departmentname,
                (SELECT accountname FROM vtiger_account LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid WHERE vtiger_servicecontracts.servicecontractsid=vtiger_achievementallot_statistic.servicecontractid) as accountname,              
                (SELECT  last_name FROM vtiger_users  WHERE vtiger_users.id=vtiger_achievementallot_statistic.receivedpaymentownid) as last_name FROM vtiger_achievementallot_statistic WHERE istwentyroyalty=0 AND twentyroyalty>0 AND left(vtiger_achievementallot_statistic.achievementmonth,7)=?';
            $fields=array('contract_no'=>'合同编号','accountname'=>'客户名称','twentyroyalty'=>'金额','achievementmonth'=>'暂扣日期','last_name'=>'姓名','departmentname'=>'部门');
            $sourceName='暂扣明细';
        }
        $headerArray = $fields;
        ini_set('memory_limit','512M');
        $path = $root_directory;
        !is_dir($path) && mkdir($path,'0755',true);
        $urlfilename='temp/'.$sourceName.$current_user->id.'.csv';
        $filename = $path.$urlfilename;
        $array= array();
        foreach($headerArray as $key=>$value) {
            $array[] = iconv('utf-8','GB18030//IGNORE',$value);
        }
        $fp = fopen($filename,'w');
        fputcsv($fp, $array);
        $limit = 1000;
        $i=0;
        while (1) {
            $limitSQL = " limit " . $i * $limit . ",". $limit;
            $i++;
            $result = $adb->pquery($query . $limitSQL, array($yearmonth));
            $num=$adb->num_rows($result);
            if(0==$num){
                break;
            }
            while ($value = $adb->fetch_array($result)) {
                $array = array();
                foreach ($headerArray as $keyheader => $valueheader) {
                    $currnetValue = iconv('utf-8', 'GB18030//IGNORE', $value[$keyheader]);
                    $array[] = $currnetValue;
                }
                fputcsv($fp, $array);
            }
            if($num!=$limit){break;}
        }
        fclose($fp);
        $response=new Vtiger_Response();
        $response->setResult(array('urlpath'=>$urlfilename));
        $response->emit();
    }
    public function getUserWhere(){
        global $adb,$current_user;
        $query="SELECT permissions FROM vtiger_custompermtable WHERE module='AchievementallotStatistic' and userid=? limit 1";
        $result=$adb->pquery($query,array($current_user->id));
        $where=array();
        if($adb->num_rows($result)){
            $permissions=$result->fields['permissions'];
            $permissionsArray=explode(',',$permissions);
            foreach($permissionsArray as $value){
                $where=array_merge($where,getChildDepartment($value));
            }
        }
        $searchDepartment = $_REQUEST['department'];
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){
            $departments=getChildDepartment($searchDepartment);
            $departments=array_intersect($where,$departments);
            $departments=!empty($departments)?$departments:array(-1);
            $listQuery=' AND vtiger_achievementsummary.departmentid in (\''.implode("','",$departments).'\')';
        }else{
            $departments=array(-1);
            if(!empty($where)){
                $departments=$where;
            }
            $listQuery=' AND vtiger_achievementsummary.departmentid in (\''.implode("','",$departments).'\')';
        }
        return $listQuery;

    }

    /**
     * 调整提成
     * @param Vtiger_Request $request
     */
    public function updateAchievement(Vtiger_Request $request){
        do{
            global $adb,$current_user;
            $returnData=array('success'=>false);
            $record=$request->get('record');
            $mfield=$request->get('mfield');
            $mvalue=$request->get('mvalue');
            $remarks=$request->get('remarks');
            $recordModel=Vtiger_Record_Model::getCleanInstance('AchievementallotStatistic');
            if(!$recordModel->personalAuthority('AchievementallotStatistic','adjust')){
                $returnData['msg']='没有权限！';
                break;
            }
            if(!in_array($mfield,array('uroyalty','udeliverdetain','ugrantdetain'))){
                $returnData['msg']='无效的字段！';
                break;
            }
            if(empty($mvalue)){
                $returnData['msg']='调整值有误！';
                break;
            }
            if($mvalue!=$mvalue*1){
                $returnData['msg']='调整值有误！！';
                break;
            }
            if(empty($remarks)){
                $returnData['msg']='备注为空！';
                break;
            }
            $remarks=addslashes($remarks);
            $query='SELECT * FROM vtiger_achievementsummary WHERE achievementid=?';
            $result=$adb->pquery($query,array($record));
            $userid=$result->fields['userid'];
            $achievementmonth=$result->fields['achievementmonth'];
            $achievementtype=$result->fields['achievementtype'];
            $uperformancetype=$result->fields['performancetype'];
            $actualroyalty=$result->fields['actualroyalty'];//实际提成
            $aroyalty=$result->fields['royalty'];//提成
            $deliverdetain=$result->fields['deliverdetain'];//交付暂扣
            $grantdetain=$result->fields['grantdetain'];//交付发放
            $halfyearlyaward=$result->fields['halfyearlyaward'];//半年度奖
            $quarterlyaward=$result->fields['quarterlyaward'];//季度奖
            $annualpayment=$result->fields['annualpayment'];//年度发放
            $newactualroyalty=$aroyalty-$deliverdetain+$grantdetain+$halfyearlyaward+$quarterlyaward+$annualpayment;
            $query='SELECT * FROM `vtiger_achievementsupdate` WHERE uuserid=? AND uachievementmonth=? AND uachievementtype=? AND uperformancetype=? AND deleted=0';
            $result=$adb->pquery($query,array($userid,$achievementmonth,$achievementtype,$uperformancetype));
            $uroyalty=0;
            $datetime=date('Y-m-d H:i:s');
            if($adb->num_rows($result)){
                $uroyalty=$result->fields['uroyalty'];//调整提成
                $updateParams='uroyalty=uroyalty+'.$mvalue.",uroyaltyremark=concat(uroyaltyremark,'".$remarks.'\'),modifiedbyid='.$current_user->id.",modifiedtime='".$datetime."',modifiedlog=CONCAT(IFNULL(modifiedlog,''),'|".$mfield.'-'.$mvalue.'-'.$remarks."')";
                $updateSql='UPDATE `vtiger_achievementsupdate` SET '.$updateParams.' WHERE uuserid=? AND uachievementmonth=? AND uachievementtype=? AND uperformancetype=? AND deleted=0';
                $adb->pquery($updateSql,array($userid,$achievementmonth,$achievementtype,$uperformancetype));
            }else{
                //$insertSQL='INSERT INTO vtiger_achievementsupdate (uuserid,uachievementmonth,uachievementtype,uperformancetype,deleted,modifiedbyid,modifiedtime,'.$mfield.','.$mfieldremark.') VALUES(?,?,?,?,?,?,?,?,?)';
                //$adb->pquery($insertSQL,array($userid,$achievementmonth,$achievementtype,$uperformancetype,0,$current_user->id,$datetime,$mvalue,$remarks));
                $insertSQL='INSERT INTO vtiger_achievementsupdate (uuserid,uachievementmonth,uachievementtype,uperformancetype,deleted,modifiedbyid,modifiedtime,uroyalty,uroyaltyremark) VALUES(?,?,?,?,?,?,?,?,?)';
                $adb->pquery($insertSQL,array($userid,$achievementmonth,$achievementtype,$uperformancetype,0,$current_user->id,$datetime,$mvalue,$remarks));
            }
            $newactualroyalty=$newactualroyalty+$mvalue+$uroyalty;
            $updateSql='UPDATE vtiger_achievementsummary SET actualroyalty='.$newactualroyalty.' WHERE achievementid=?';
            $adb->pquery($updateSql,array($record));
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $record, 'AchievementSummary', $current_user->id,$datetime, 0));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?)',
                Array($id,'actualroyalty',$actualroyalty,$newactualroyalty,$id,$mfield,$$mfield,$mvalue));
            $query='SELECT uroyalty,udeliverdetain,ugrantdetain,uroyaltyremark,udeliverdetainremark,ugrantdetainremark,modifiedbyid FROM `vtiger_achievementsupdate` WHERE uuserid=? AND uachievementmonth=? AND uachievementtype=? AND deleted=0';
            $result=$adb->pquery($query,array($userid,$achievementmonth,$achievementtype));
            $data=$result->fields;
            $data['modifiedbyid']=$current_user->last_name;
            $returnData=array('success'=>true,'data'=>$data);
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }
}