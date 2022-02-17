<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * 
 *************************************************************************************/

class RVisitingorderTransaction_selectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getCountsday');
        $this->exposeMethod('getdetaillist');
        $this->exposeMethod('getUsers');
        $this->exposeMethod('getrefreshday');
        $this->exposeMethod('getvisitexp');
    }
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    public function process(Vtiger_Request $request) {
		$mode=$request->getMode();
        if(!empty($mode)){
            echo $this->invokeExposedMethod($mode,$request);
            exit;
        }
	}
    //打开时加载
    public function getCountsday(Vtiger_Request $request){
        $datetime=$request->get('datetime');
        $enddatetime=$request->get('enddatetime');
        $departmentid=$request->get('department');
        $fliter=$request->get('fliter');
        if($fliter=='thisweek'){
            $lastday=date('Y-m-d',strtotime("Sunday"));
            $firstday=date('Y-m-d',strtotime("$lastday -6 days"));
            $tempdate = " BETWEEN '{$firstday}' AND '{$lastday}'";
        }else if($fliter=='thismonth'){
            $firstday = date('Y-m-01');
            $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
            $tempdate = " BETWEEN '{$firstday}' AND '{$lastday}'";
        }else{
            if(strtotime($datetime)>strtotime($enddatetime)){
                $tempdate=" BETWEEN '{$enddatetime}' AND '{$datetime}'";
            }elseif(strtotime($datetime)<strtotime($enddatetime)){
                $tempdate=" BETWEEN '{$datetime}' AND '{$enddatetime}'";
            }else{
                $tempdate=" ='{$datetime}'";
            }
            if($datetime==''){

                $tempdate=" ='".date('Y-m-d')."'";
            }
        }
        $query='SELECT ';
        $cachedepartment=getDepartment();
        $arr=array();
        if(!empty($departmentid)){
            foreach($departmentid as $value){
                $userid=getDepartmentUser($value);
                $where=getAccessibleUsers('RVisitingorderTransaction','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                if(empty($where)||count($where)==0){
                    continue;
                }
                $arr['department'][strtolower($value)]=str_replace(array('|','—'),array('',''),$cachedepartment[$value]);
                $query.='sum(if(userid in('.implode(',',$where).'),1,0)) as '.$value.',';

            }
            $query=rtrim($query,',').',nums';

        }else{
            $where=getAccessibleUsers('RVisitingorderTransaction','List',true);
            if($where!='1=1'){
                $query.='sum(if(userid in('.implode(',',$where).'),1,0)) as H1,nums';
            }else{
                $query.='';
            }
        }

        $query.=' FROM vtiger_countsvisitingorder WHERE signedate '.$tempdate.' GROUP BY nums';
        $db=PearDatabase::getInstance();
        $result=$db->run_query_allrecords($query);
        foreach($result as $value){
            foreach($value as $key=>$val){
                if(!is_numeric($key)){
                    $arr['dataall'][$key][]=$val;
                }
            }
        }
        if(count($arr['dataall'])==0){
            $arr['dataall']['empty']=array();
        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();
    }
    //按条件查询
    public function getdetaillist(Vtiger_Request $request){
        $datanums=$request->get('nums');
        $datatime=$request->get('startdatetime');
        $enddatetime=$request->get('enddatetime');
        $departmentid=$request->get('deparement');
        $departmentid=strtoupper($departmentid);
        $fliter=$request->get('fliter');
        if($fliter=='thisweek'){
            $lastday=date('Y-m-d',strtotime("Sunday"));
            $firstday=date('Y-m-d',strtotime("$lastday -6 days"));
            $tempdate = " BETWEEN TO_DAYS('{$firstday}') AND TO_DAYS('{$lastday}')";
        }else if($fliter=='thismonth'){
            $firstday = date('Y-m-01');
            $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
            $tempdate = " BETWEEN TO_DAYS('{$firstday}') AND TO_DAYS('{$lastday}')";
        }else {
            if (strtotime($datatime) > strtotime($enddatetime)) {
                $tempdate = " BETWEEN TO_DAYS('{$enddatetime}') AND TO_DAYS('{$datatime}')";
            } elseif (strtotime($datatime) < strtotime($enddatetime)) {
                $tempdate = " BETWEEN TO_DAYS('{$datatime}') AND TO_DAYS('{$enddatetime}')";
            } else {
                $tempdate = " =TO_DAYS('{$datatime}')";
            }
            if ($datatime == '') {
                $tempdate = " =TO_DAYS('{$datatime}')";
            }
        }
        if($datanums==''){
            $datanums=1;
        }
        if(!empty($departmentid)&&$departmentid!='H1'){
            $userid=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('RVisitingorderTransaction','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $sql = ' AND vtiger_countsvisitingorder.userid in('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers('RVisitingorderTransaction','List',false);
            if($where!='1=1'){
                $sql =' AND vtiger_countsvisitingorder.userid '.$where;
            }else{
                $sql='';
            }
        }
        $query="SELECT vtiger_countsvisitingorder.nums,vtiger_account.accountid,vtiger_account.accountname,vtiger_countsvisitingorder.signedate,IFNULL( ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_crmentity.smownerid), '--' ) AS rusername FROM vtiger_countsvisitingorder LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_countsvisitingorder.accountid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid WHERE TO_DAYS(vtiger_countsvisitingorder.signedate){$tempdate} AND vtiger_countsvisitingorder.nums={$datanums}{$sql}";
        //echo $query;
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        if($num<1){
            $arrlist=array();
            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($arrlist);
            $response->emit();
            return;
        }
        $arrlist=array();
        for($i=0;$i<$num;$i++){
            $arrlist[$i]=$db->fetchByAssoc($result);
        }


        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arrlist);
        $response->emit();
    }
    public function getUsers(Vtiger_Request $request){
        $departmentid=$request->get('department');
        if(!empty($departmentid)&&$departmentid!='H1'){
            $userid=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('Reporting','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery = ' AND id in('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers('Reporting','List',false);
            if($where!='1=1'){
                $listQuery =' AND id '.$where;
            }else{
                $listQuery='';
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(Reporting_Record_Model::getuserinfo($listQuery));
        $response->emit();
        //return Reporting_Record_Model::getuserinfo($listQuery);

    }
    public function getrefreshday(){
        ignore_user_abort(true);//浏览器关闭后脚本还执行
        $db=PearDatabase::getInstance();
        $query="SELECT refreshtime FROM `vtiger_refreshtime` WHERE module='RVisitingorderTransaction' limit 1";
        $result=$db->pquery($query,array());
        $resulttime=$db->query_result($result,0,'refreshtime');
        $nowtime=time();
        $interval=4*60*60;//间隔时间
        $result1=array();
        if($nowtime-$resulttime>$interval){
            $db->pquery("TRUNCATE TABLE vtiger_countsvisitingorder;",array());

            $db->pquery("INSERT INTO vtiger_countsvisitingorder(nums,signedate,userid,accountid) SELECT IFNULL((SELECT count(1) FROM vtiger_visitingorder WHERE LEFT(vtiger_visitingorder.enddate,10)<=left(vtiger_servicecontracts.signdate,10) AND vtiger_visitingorder.related_to=vtiger_servicecontracts.sc_related_to AND vtiger_servicecontracts.modulestatus='c_complete'),0) AS counts,vtiger_servicecontracts.signdate,vtiger_servicecontracts.signid,vtiger_servicecontracts.sc_related_to FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.modulestatus='c_complete' AND vtiger_servicecontracts.signdate IS NOT NULL AND vtiger_servicecontracts.sc_related_to>0 AND vtiger_servicecontracts.firstcontract=1",array());
            $nowtime=time();
            $db->pquery("replace into vtiger_refreshtime(refreshtime,module) VALUES(?,?)",array($nowtime,'RVisitingorderTransaction'));
            $result1['msg']='更新完成......';
        }else{
            $interval=4*60-ceil(($nowtime-$resulttime)/60);
            if(floor($interval/60)==0){
                $result1['msg']="请在{$interval}分钟后再更新";
            }else{
                $result1['msg']="请在".floor($interval/60)."小时".($interval%60)."分钟后再更新";
            }

        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result1);
        $response->emit();
    }

    public function getvisitexp(Vtiger_Request $request)
    {
        $data=$this->getvisitsdata($request);
        global $root_directory;
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
        /*$phpexecl->setActiveSheetIndex(0)->mergeCells('A1:Q2')
            ->mergeCells('B1:B2')->mergeCells('C1:C2')->mergeCells('D1:G1')
            ->mergeCells('H1:K1')->mergeCells('L1:O1')->mergeCells('P1:S1');*/
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '中心')
            ->setCellValue('B1', '部门')
            ->setCellValue('C1', '姓名')
            ->setCellValue('D1', '拜访总量')
            ->setCellValue('E1', '成交拜访总量')
            ->setCellValue('F1', '成交拜访率')
            ->setCellValue('G1', '零次拜访成交量')
            ->setCellValue('H1', '一次拜访成交量')
            ->setCellValue('I1', '二次拜访成交量')
            ->setCellValue('J1', '三次拜访成交量')
            ->setCellValue('K1', '三次以上拜访成交量')
            ->setCellValue('L1', '零次拜访成交率')
            ->setCellValue('M1', '一次拜访成交率')
            ->setCellValue('N1', '二次拜访成交率')
            ->setCellValue('O1', '三次拜访成交率')
            ->setCellValue('P1', '三次以上拜访成交率')
        ;

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:P1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:P1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $current=2;
        if(!empty($data)){
            $array=$data['data'];
            $depnum=$data['num'];
            $text='';
            foreach($array as $key1=>$value1){
                $i=0;
                if($key1=='name'){
                    continue;
                }
                $allzero=0;
                $allone=0;
                $alltwo=0;
                $allthree=0;
                $allforth=0;
                $allsumall=0;
                $allvisitors=0;
                foreach($value1 as $key2=>$value2){
                    $j=0;
                    if($key2=='name') {
                        continue;
                    }
                    $szero=0;
                    $sone=0;
                    $stwo=0;
                    $sthree=0;
                    $sforth=0;
                    $ssumall=0;
                    $svisitors=0;

                    foreach($value2 as $key3=>$value3){
                        if(!is_numeric($key3)){
                            continue;
                        }

                        if($i==0){
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('A'.$current.':A'.($current+$depnum[$key1]));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('A'.$current, $value1['name']);
                            $phpexecl->getActiveSheet()->getStyle('A'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        }
                        if($j==0){
                            $phpexecl->setActiveSheetIndex(0)->mergeCells('B'.$current.':B'.($current+$depnum[$key2]-1));
                            $phpexecl->setActiveSheetIndex(0)->setCellValue('B'.$current, $value2['name']);
                            $phpexecl->getActiveSheet()->getStyle('B'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        }

                        $presumall=sprintf("%.2f",$value3['sumall']/$value3['visitors']*100);
                        $prezerocount=sprintf("%.2f",$value3['zerocount']/$value3['sumall']*100);
                        $preonecount=sprintf("%.2f",$value3['onecount']/$value3['sumall']*100);
                        $pretwocount=sprintf("%.2f",$value3['twocount']/$value3['sumall']*100);
                        $prethreecount=sprintf("%.2f",$value3['threecount']/$value3['sumall']*100);
                        $preothercount=sprintf("%.2f",$value3['othercount']/$value3['sumall']*100);
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, $value3['username']);
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('D'.$current, (empty($value3['visitors'])?'':$value3['visitors']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('E'.$current, (empty($value3['sumall'])?'':$value3['sumall']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('F'.$current, ((empty($presumall)||$presumall==0)?'':($presumall.'%')));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('G'.$current, (empty($value3['zerocount'])?'':$value3['zerocount']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('H'.$current, (empty($value3['onecount'])?'':$value3['onecount']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('I'.$current, (empty($value3['twocount'])?'':$value3['twocount']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('J'.$current, (empty($value3['threecount'])?'':$value3['threecount']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('K'.$current, (empty($value3['othercount'])?'':$value3['othercount']));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('L'.$current, ((empty($prezerocount) || $prezerocount==0)?'':$prezerocount.'%'));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('M'.$current, ((empty($preonecount) || $preonecount==0)?'':$preonecount.'%'));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('N'.$current, ((empty($pretwocount) || $pretwocount==0)?'':$pretwocount.'%'));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('O'.$current, ((empty($prethreecount) || $prethreecount==0)?'':$prethreecount.'%'));
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('P'.$current, ((empty($preothercount) || $preothercount==0)?'':$preothercount.'%'));

                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':P'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':P'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':P'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $i=1;$j=1;
                        $szero+=$value3['zerocount'];
                        $sone+=$value3['onecount'];
                        $stwo+=$value3['twocount'];
                        $sthree+=$value3['threecount'];
                        $sforth+=$value3['othercount'];
                        $ssumall+=$value3['sumall'];
                        $svisitors+=$value3['visitors'];

                        $allzero+=$value3['zerocount'];
                        $allone+=$value3['onecount'];
                        $alltwo+=$value3['twocount'];
                        $allthree+=$value3['threecount'];
                        $allforth+=$value3['othercount'];
                        $allsumall+=$value3['sumall'];
                        $allvisitors+=$value3['visitors'];

                        ++$current;
                    }
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, '部门小计');
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('D'.$current, $svisitors);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('E'.$current, $ssumall);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('F'.$current, sprintf("%.2f",$ssumall/$svisitors*100).'%');
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('G'.$current, $szero);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('H'.$current, $sone);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('I'.$current, $stwo);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('J'.$current, $sthree);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('K'.$current, $sforth);
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('L'.$current, sprintf("%.2f",$szero/$ssumall*100).'%');
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('M'.$current, sprintf("%.2f",$sone/$ssumall*100).'%');
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('N'.$current, sprintf("%.2f",$stwo/$ssumall*100).'%');
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('O'.$current, sprintf("%.2f",$sthree/$ssumall*100).'%');
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('P'.$current, sprintf("%.2f",$sforth/$ssumall*100).'%');
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':P'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':P'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':P'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    ++$current;
                }
                $phpexecl->setActiveSheetIndex(0)->setCellValue('B'.$current, '营总计');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('C'.$current, '');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('D'.$current, $allvisitors);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('E'.$current, $allsumall);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('F'.$current, sprintf("%.2f",$allsumall/$allvisitors*100).'%');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('G'.$current, $allzero);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('H'.$current, $allone);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('I'.$current, $alltwo);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('J'.$current, $allthree);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('K'.$current, $allforth);
                $phpexecl->setActiveSheetIndex(0)->setCellValue('L'.$current, sprintf("%.2f",$allzero/$allsumall*100).'%');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('M'.$current, sprintf("%.2f",$allone/$allsumall*100).'%');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('N'.$current, sprintf("%.2f",$alltwo/$allsumall*100).'%');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('O'.$current, sprintf("%.2f",$allthree/$allsumall*100).'%');
                $phpexecl->setActiveSheetIndex(0)->setCellValue('P'.$current, sprintf("%.2f",$sforth/$allsumall*100).'%');
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':P'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':P'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':P'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                ++$current;


            }

        }


        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('成交拜访量统计');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="成交拜访量统计'.date('Y-m-dHis').'.xlsx"');
        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');


        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function getvisitsdata(Vtiger_Request $request){

        $datetime=$request->get('datetime');
        $enddatetime=$request->get('enddatatime');
        $departmentid=$request->get('department');
        if(strtotime($datetime)>strtotime($enddatetime)){
            $tempdate=" BETWEEN '{$enddatetime}' AND '{$datetime}'";
        }elseif(strtotime($datetime)<strtotime($enddatetime)){
            $tempdate=" BETWEEN '{$datetime}' AND '{$enddatetime}'";
        }else{
            $tempdate=" ='{$datetime}'";
        }
        if($datetime==''){
            $tempdate=" ='".date('Y-m-d')."'";
        }
        $querysql='';
        $hkquerysql='';
        if(!empty($departmentid)){
            $departmentarr=array();
            foreach($departmentid as $value){
                $userid=getDepartmentUser($value);
                $where=getAccessibleUsers('VisitingOrder','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                if(empty($where)||count($where)==0){
                    continue;
                }
                $departmentarr=array_merge($departmentarr,$where);
            }
            $querysql.=' AND vtiger_countsvisitingorder.userid in('.implode(',',$departmentarr).')';
            $hkquerysql.=' AND vtiger_visitingorder.extractid in('.implode(',',$departmentarr).')';

        }else{
            $where=getAccessibleUsers('VisitingOrder','List',true);
            if($where!='1=1'){
                $querysql.=' AND vtiger_countsvisitingorder.userid in('.implode(',',$departmentarr).')';
                $hkquerysql.=' AND vtiger_visitingorder.extractid in('.implode(',',$departmentarr).')';
            }else{
                $querysql.='';
            }
        }
        $query="SELECT
                    SUM(IF(vtiger_countsvisitingorder.nums=0,1,0)) AS zerocount,
                    SUM(IF(vtiger_countsvisitingorder.nums=1,1,0)) AS onecount,
                    SUM(IF(vtiger_countsvisitingorder.nums=2,1,0)) AS twocount,
                    SUM(IF(vtiger_countsvisitingorder.nums=3,1,0)) AS threecount,
                    SUM(IF(vtiger_countsvisitingorder.nums>3,1,0)) AS othercount,
                    SUM(if(vtiger_countsvisitingorder.nums=0,1,vtiger_countsvisitingorder.nums)) AS sumall,
                    0 as visitors,
                    vtiger_countsvisitingorder.userid,
                    vtiger_users.last_name AS username,
                        SUBSTRING_INDEX(vtiger_departments.parentdepartment,'::',-2) AS department
                FROM  vtiger_countsvisitingorder

                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_countsvisitingorder.userid
                LEFT JOIN vtiger_user2department ON vtiger_users.id= vtiger_user2department.userid
                LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                WHERE
                   1=1 AND vtiger_countsvisitingorder.signedate{$tempdate}{$querysql}
                GROUP BY vtiger_countsvisitingorder.userid
                ORDER BY department";
        $hkquery="SELECT
                    count(1) as visitors,
                    vtiger_visitingorder.extractid AS userid,
                    vtiger_users.last_name AS username,
                    SUBSTRING_INDEX(vtiger_departments.parentdepartment,'::',-2) AS department
                FROM vtiger_visitingorder
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_visitingorder.extractid
                LEFT JOIN vtiger_user2department ON vtiger_users.id= vtiger_user2department.userid
                LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                WHERE vtiger_visitingorder.modulestatus='c_complete'
                AND left(vtiger_visitingorder.enddate,10){$tempdate}{$hkquerysql}
                GROUP BY vtiger_visitingorder.extractid ORDER BY department";
        $db=PearDatabase::getInstance();
        $hkresult=$db->pquery($hkquery,array());//处理回款结果
        $hknum=$db->num_rows($hkresult);//处理回款结果
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);

        if($num || $hknum){
            $array=array();
            $cachedepartment=getDepartment();

            for($i=0;$i<$num;$i++){
                $depart=$db->query_result($result,$i,'department');
                $depart=explode('::',$depart);
                $useid=$db->query_result($result,$i,'userid');
                if(!empty($departmentid)&&$departmentid!='null'){
                    if(in_array($depart[1],$departmentid)){
                        $array[$depart[1]][$depart[1].'D'][$useid]=$db->query_result_rowdata($result,$i);
                        $array[$depart[1]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                        $array[$depart[1]][$depart[1].'D']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]).'D';
                    }else{
                        $array[$depart[0]][$depart[1].'M'][$useid]=$db->query_result_rowdata($result,$i);
                        $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                        $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                    }

                }else{
                    $array[$depart[0]][$depart[1].'M'][$useid]=$db->query_result_rowdata($result,$i);
                    $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                    $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                }

            }
            $hk=array('zerocount' =>0,'onecount' => 0,'twocount' => 0,'threecount' => 0,'othercount' => 0,'sumall' =>0);
            //start处理回款
            for($i=0;$i<$hknum;$i++){
                $depart=$db->query_result($hkresult,$i,'department');
                $userid=$db->query_result($hkresult,$i,'userid');
                $depart=explode('::',$depart);
                if(!empty($departmentid)&&$departmentid!='null'){
                    if(in_array($depart[1],$departmentid)){
                        $temp=empty($array[$depart[1]][$depart[1].'D'][$userid])?$hk:$array[$depart[1]][$depart[1].'D'][$userid];
                        $array[$depart[1]][$depart[1].'D'][$userid]=array_merge($temp,$db->query_result_rowdata($hkresult,$i));
                        $array[$depart[1]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                        $array[$depart[1]][$depart[1].'D']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]).'D';
                    }else{
                        $temp=empty($array[$depart[0]][$depart[1].'M'][$userid])?$hk:$array[$depart[0]][$depart[1].'M'][$userid];
                        $array[$depart[0]][$depart[1].'M'][$userid]=array_merge($temp,$db->query_result_rowdata($hkresult,$i));
                        $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                        $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                    }
                }else{
                    $temp=empty($array[$depart[0]][$depart[1].'M'][$userid])?$hk:$array[$depart[0]][$depart[1].'M'][$userid];
                    $array[$depart[0]][$depart[1].'M'][$userid]=array_merge($temp,$db->query_result_rowdata($hkresult,$i));
                    $array[$depart[0]]['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[0]]);
                    $array[$depart[0]][$depart[1].'M']['name']=str_replace(array('|','—'),array('',''),$cachedepartment[$depart[1]]);
                }
            }

            //end处理回款
            $depnum=array();
            foreach($array as $key=>$value){
                if($key=='name')continue;
                foreach($value as $k=>$v){
                    if($k=='name')continue;
                    $depnum[$key]+=count($v);
                    $depnum[$k]+=count($v);
                }
            }
            return array('data'=>$array,'num'=>$depnum);
        }else{

            return array();
        }
    }

}
