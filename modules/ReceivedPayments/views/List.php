<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
set_time_limit(0);
ini_set('memory_limit', '-1');
class ReceivedPayments_List_View extends Vtiger_KList_View {
    function process (Vtiger_Request $request)
    {

        $strPublic = $request->get('public');
        if ($strPublic == 'ExportRI') {               //导出

            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('exportri.tpl', $moduleName);
            exit;
        }elseif($strPublic=='ExportRID'){
            //导出数据
            set_time_limit(0);
            global $root_directory,$current_user,$site_URL;
            $path=$root_directory.'temp/';
            $exportFormat=$request->get('exportFormat');
            $format='.csv';
            if($exportFormat=='excel'){
                $format='.xlsx';
            }
            $filename=$path.'erppayment'.$current_user->id.$format;
            !is_dir($path)&&mkdir($path,'0777',true);
            @unlink($filename);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ReceivedPayments','ExportRI')){   //权限验证
                return;
            }
            $departments=$request->get('department');
            $startdate=$request->get('f');
            $enddatatime=$request->get('s');
            $timeselected=$request->get('timeselected');
            $datetimesub=strtotime($startdate)-strtotime($enddatatime);
            /*if(abs($datetimesub)>8035200 && date('H')!=12){
                //导出时间大于2个月则导出时间不在中午12:00-12:59这段时间
                throw new AppException('大于3个月的请在12:00-13:00这段是时间内操作!');
                exit;
            }*/
            //$where=getAccessibleUsers();
            $lng = translateLng("ServiceContracts");
            $listQuery='';
            /*if($where!='1=1'){
                $listQuery= ' and vtiger_receivedpayments.createid '.$where;
                vtiger_achievementallot.receivedpaymentownid
            }*/

            if($departments!='H1'){
                $userid=getDepartmentUser($departments);
                if(!empty($userid)){
                    $listQuery= ' and vtiger_achievementallot.receivedpaymentownid in('.implode(',',$userid).')';
                }else{
                    $listQuery= ' and vtiger_achievementallot.receivedpaymentownid in(0)';
                }

            }
            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');
            if($timeselected==2){
                $exportdate='vtiger_receivedpayments_notes.createtime';
            }else{
                $exportdate='vtiger_receivedpayments.reality_date';
            }
            if($startdate>$enddatatime){
                $sql=" and left({$exportdate},20)>='{$enddatatime}' and left({$exportdate},20)<='{$startdate}'";
            }elseif($startdate==$enddatatime){
                $sql=" and left({$exportdate},20)='{$enddatatime}'";
            }elseif($startdate<$enddatatime){
                $sql=" and left({$exportdate},20)<='{$enddatatime}' and left({$exportdate},20)>='{$startdate}'";
            }
            $enddate=substr($enddatatime,0,7);
            global $root_directory;
            $db=PearDatabase::getInstance();

            $query="SELECT
                        IF(vtiger_receivedpayments.ismanualmatch='1','是',IF(vtiger_receivedpayments.ismanualmatch='2','否','')) AS ismanualmatch,
                        IF(vtiger_account.frommarketing='1','是','否') AS 'frommarketing',
                        IF(vtiger_receivedpayments.istimeoutmatch='1','是','否') AS 'istimeoutmatch',
                        IF(vtiger_receivedpayments.iscrossmonthmatch='1','是','否') AS 'iscrossmonthmatch',
                        IF(vtiger_receivedpayments.ischeckachievement='1','是','否') AS 'ischeckachievement',
                        vtiger_receivedpayments.paymentcode,
                        vtiger_receivedpayments.owncompany,
                        vtiger_receivedpayments.reality_date,
                        vtiger_receivedpayments.receivedstatus,
                        TRUNCATE(vtiger_receivedpayments.unit_price*vtiger_achievementallot.scalling/100,2) AS unit_price,
                        (SELECT dd.departmentname FROM vtiger_departments dd WHERE dd.parentdepartment=left(vtiger_departments.parentdepartment,10)) AS groupname,
                        vtiger_departments.departmentname,
                        vtiger_departments.parentdepartment,
                        vtiger_users.last_name,
                        IFNULL((SELECT gradename FROM vtiger_usergraderoyalty where userid=vtiger_achievementallot.receivedpaymentownid AND assessmonth='".$enddate."' LIMIT 1),(SELECT gradename FROM vtiger_usergraderoyalty where userid=vtiger_achievementallot.receivedpaymentownid ORDER BY usergraderoyaltyid DESC LIMIT 1)) AS title,
                        vtiger_receivedpayments.paytitle,
                        vtiger_receivedpayments.departmentid,
                        vtiger_receivedpayments.paymentamount,
                        left(vtiger_receivedpayments.createtime,10) as createtime,
                        vtiger_receivedpayments.unit_price as unit_prices,
                        vtiger_servicecontracts.servicecontractstype as newrenewa,
                        vtiger_account.accountname,
                        left(vtiger_servicecontracts.signdate,10) AS signdate,
                        vtiger_servicecontracts.contract_no,
                        vtiger_parent_contracttype.parent_contracttype,
                        replace(vtiger_servicecontracts.productsearchid,'<br>','-') AS productsearchid,
                        vtiger_servicecontracts.contract_type,
                        TRUNCATE(vtiger_servicecontracts.total*IFNULL((SELECT sum(vtiger_servicecontracts_divide.scalling)/count(1) FROM vtiger_servicecontracts_divide WHERE  vtiger_servicecontracts.servicecontractsid=vtiger_servicecontracts_divide.servicecontractid AND vtiger_servicecontracts_divide.receivedpaymentownid=vtiger_achievementallot.receivedpaymentownid),vtiger_achievementallot.scalling)/100,2) AS total,
                        TRUNCATE((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid = vtiger_servicecontracts.servicecontractsid),2) AS purchasemount,
                        TRUNCATE((SELECT sum(IFNULL(vtiger_salesorderproductsrel.costing,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid = vtiger_servicecontracts.servicecontractsid),2) AS costing,
                        TRUNCATE((SELECT sum(IF (extra_type = '沙龙',IFNULL(extra_price, 0) * vtiger_achievementallot.scalling / 100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS xalong,
                        TRUNCATE((SELECT sum(IF(extra_type = '外采',IFNULL(extra_price, 0) * vtiger_achievementallot.scalling / 100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS waici,
                        TRUNCATE((SELECT sum(IF(extra_type = '媒介充值',IFNULL(extra_price, 0) * vtiger_achievementallot.scalling / 100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS meijai,
                        TRUNCATE((SELECT sum(IF(extra_type != '沙龙' AND extra_type != '外采' AND extra_type = '媒介充值',IFNULL(extra_price, 0) * vtiger_achievementallot.scalling / 100,0)) FROM	vtiger_receivedpayments_extra	WHERE	vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS qite,
                        (SELECT GROUP_CONCAT(vtiger_receivedpayments_extra.extra_remark) FROM	vtiger_receivedpayments_extra	WHERE	vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid GROUP BY vtiger_receivedpayments_extra.receivementid) AS extra_remark,
                        if(vtiger_receivedpayments.relatetoid>0,(SELECT vtiger_receivedpayments_notes.createtime FROM vtiger_receivedpayments_notes 
                        WHERE vtiger_receivedpayments_notes.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid ORDER BY receivedpaymentsnotesid DESC LIMIT 1),'') AS notescreatetime
                         ,(select vd.departmentname FROM vtiger_departments as vd WHERE vd.departmentid= vtiger_achievementallot  
                        .departmentid
                        ) as departmentname_2,
			vtiger_servicecontracts.bussinesstype,
			        vtiger_receivedpayments.receivedpaymentsid,
	                (select SUM(vtiger_receivedpayments_extra.extra_price) from vtiger_receivedpayments_extra where vtiger_receivedpayments_extra.receivementid=vtiger_receivedpayments.receivedpaymentsid ) as sumextra
                    FROM
                        vtiger_receivedpayments
                    LEFT JOIN vtiger_achievementallot ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot.receivedpaymentsid
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                    LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_achievementallot.receivedpaymentownid
                    LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                    LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                    LEFT JOIN vtiger_parent_contracttype ON vtiger_servicecontracts.parent_contracttypeid=vtiger_parent_contracttype.parent_contracttypeid
                    LEFT JOIN (select * from (SELECT * from vtiger_receivedpayments_notes ORDER BY createtime desc) as vtiger_receivedpayments_notes GROUP BY receivedpaymentsid) as vtiger_receivedpayments_notes ON vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_notes.receivedpaymentsid
                    WHERE
                        vtiger_receivedpayments.deleted=0  
                        {$sql}{$listQuery}";
//            echo $query;die;
            $result= $db->run_query_allrecords($query);
            if($format=='.xlsx'){
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
                    ->setCellValue('A1', '账户')
                    ->setCellValue('B1', '创建日期')
                    ->setCellValue('C1', '收款日期')
                    ->setCellValue('D1', '匹配日期')
                    ->setCellValue('E1', '汇款抬头')
                    ->setCellValue('F1', '汇款金额(入账)')
                    ->setCellValue('G1', '汇款金额')
                    ->setCellValue('H1', '收款金额(已分成)')
                    //->setCellValue('I1', '部门')
                    //->setCellValue('J1', '业务员所属事业部')
                    //->setCellValue('K1', '销售组')
                    ->setCellValue('I1', '业务类型')
                    ->setCellValue('J1', '一级部门')
                    ->setCellValue('K1', '二级部门')
                    ->setCellValue('L1', '三级部门')
                    ->setCellValue('M1', '四级部门')
                    ->setCellValue('N1', '五级部门')
                    ->setCellValue('O1', '六级部门')
                    ->setCellValue('P1', '七级部门')
                    ->setCellValue('Q1', '八级部门')
                    ->setCellValue('R1', '业务员')
                    ->setCellValue('S1', '职位')
                    ->setCellValue('T1', '新单/续费')
                    ->setCellValue('U1', '客户名称')
                    ->setCellValue('V1', '合同签订日期')
                    ->setCellValue('W1', '合同编号')
                    ->setCellValue('X1', '项目种类')
                    ->setCellValue('Y1', '项目明细')
                    ->setCellValue('Z1', '业务种类')
                    ->setCellValue('AA1', '合同总金额')
                    ->setCellValue('AB1', '人力成本')
                    ->setCellValue('AC1', '外采成本')
                    ->setCellValue('AD1', '直接外采成本')
                    ->setCellValue('AE1', '充值账户币总额')
                    ->setCellValue('AF1', '沙龙支出')
                    ->setCellValue('AG1', '媒介充值')
                    ->setCellValue('AH1', '其他')
                    ->setCellValue('AI1', '额外成本金额')
                    ->setCellValue('AJ1', '成本合计')
                    ->setCellValue('AK1', '业绩所在部门')
                    ->setCellValue('AL1', '备注')
                    ->setCellValue('AM1', '是否来自市场部')
                    ->setCellValue('AN1', '是否核算')
                    ->setCellValue('AO1', '回款类型')
                    ->setCellValue('AP1', '是否超时')
                    ->setCellValue('AQ1', '是否跨月')
                    ->setCellValue('AR1', '是否已计算业绩')
                    ->setCellValue('AS1', '交易单号')
                ;

                //设置自动居中
                $phpexecl->getActiveSheet()->getStyle('A1:S1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $phpexecl->getActiveSheet()->getStyle('R1:X1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                //$result=array(1,2,3,4,5,6,7,3,8,9,10);
                require 'crmcache/departmentanduserinfo.php';
                //设置边框
                $phpexecl->getActiveSheet()->getStyle('A1:AS1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                if(!empty($result)){
                    foreach($result as $key=>$value){
                        $value['summoney']=$this->setChongZhiBi($value['receivedpaymentsid']);
                        $current=$key+2;
                        $purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                        $value['departmentid']=$cachedepartment[$value['departmentid']];
                        $parentdepartment=explode('::',$value['parentdepartment']);
                        array_shift($parentdepartment);
                        $department0='-';
                        $department1='-';
                        $department2='-';
                        $department3='-';
                        $department4='-';
                        $department5='-';
                        $department6='-';
                        $department7='-';
                        foreach($parentdepartment as $departKey=>$departValue){
                            $tempdepart='department'.$departKey;
                            $$tempdepart=$cachedepartment[$departValue];
                        }
                        $phpexecl->setActiveSheetIndex(0)
                            ->setCellValue('A'.$current, $value['owncompany'])
                            ->setCellValue('B'.$current, $value['createtime'])
                            ->setCellValue('C'.$current, $value['reality_date'])
                            ->setCellValue('D'.$current, $value['notescreatetime'])

                            ->setCellValue('E'.$current, $value['paytitle'])
                            ->setCellValue('F'.$current, $value['paymentamount'])
                            ->setCellValue('G'.$current, $value['unit_prices'])
                            ->setCellValue('H'.$current, $value['unit_price'])
                            ->setCellValue('I'.$current, $value['bussinesstype']?$lng[$value['bussinesstype']]:'')
                            ->setCellValue('J'.$current, $department0)
                            ->setCellValue('K'.$current, $department1)
                            ->setCellValue('L'.$current, $department2)
                            ->setCellValue('M'.$current, $department3)
                            ->setCellValue('N'.$current, $department4)
                            ->setCellValue('O'.$current, $department5)
                            ->setCellValue('P'.$current, $department6)
                            ->setCellValue('Q'.$current, $department7)
                            ->setCellValue('R'.$current, $value['last_name'])
                            ->setCellValue('S'.$current, $value['title'])
                            ->setCellValue('T'.$current, $value['newrenewa'])
                            ->setCellValue('U'.$current, $value['accountname'])
                            ->setCellValue('V'.$current, $value['signdate'])
                            ->setCellValue('W'.$current, $value['contract_no'])
                            ->setCellValue('X'.$current, $value['parent_contracttype'])
                            ->setCellValue('Y'.$current, $value['productsearchid'])
                            ->setCellValue('Z'.$current, $value['contract_type'])
                            ->setCellValue('AA'.$current, $value['total'])
                            ->setCellValue('AB'.$current, $value['costing'])
                            ->setCellValue('AC'.$current, $value['purchasemount'])
                            ->setCellValue('AD'.$current, $value['waici'])
                            ->setCellValue('AE'.$current, $value['summoney'])
                            ->setCellValue('AF'.$current, $value['xalong'])
                            ->setCellValue('AG'.$current, $value['meijai'])
                            ->setCellValue('AH'.$current, $value['qite'])
                            ->setCellValue('AI'.$current, $value['sumextra'])
                            ->setCellValue('AJ'.$current,  $purchasemount)
                            ->setCellValue('AK'.$current, $value['departmentname_2'])
                            ->setCellValue('AL'.$current, $value['extra_remark'])
                            ->setCellValue('AM'.$current, $value['frommarketing'])
                            ->setCellValue('AN'.$current, $value['ismanualmatch'])
                            ->setCellValue('AO'.$current, vtranslate($value['receivedstatus'],'ReceivedPayments' ))
                            ->setCellValue('AP'.$current, $value['istimeoutmatch'])
                            ->setCellValue('AQ'.$current, $value['iscrossmonthmatch'])
                            ->setCellValue('AR'.$current, $value['ischeckachievement'])
                            ->setCellValue('AS'.$current, $value['paymentcode'])
                        ;

                        //加上边框
                        $phpexecl->getActiveSheet()->getStyle('A'.$current.':AS'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    }
                }

                // 设置工作表的名移
                $phpexecl->getActiveSheet()->setTitle('回款合同');


                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $phpexecl->setActiveSheetIndex(0);

                $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
                //$objWriter->save('php://output');
                $objWriter->save($filename);
                header('location:'.$site_URL.'/temp/erppayment'.$current_user->id.$format);
                exit;
            }else{
                global $root_directory;
                //csv
                header("Content-type:text/csv;");   //header设置
                header("Content-Disposition: attachment;filename=erppayment".$current_user->id.$format);
                header('Cache-Control: max-age=0');
                header('Pragma:no-cache');
                require 'crmcache/departmentanduserinfo.php';
                $head = array('账户','创建日期','收款日期','匹配日期','汇款抬头','汇款金额(入账)','汇款金额','收款金额(已分成)','一级部门','二级部门','三级部门','四级部门','五级部门','六级部门','七级部门','八级部门','业务员','职位','新单/续费','客户名称','合同签订日期','合同编号','项目种类','项目明细','业务种类','合同总金额','人力成本','外采成本','直接外采成本','沙龙支出','媒介充值','其他','成本合计','备注','是否来自市场部','业绩所在部门','是否核算','业务类型','回款类型','是否超时','是否跨月','是否已计算业绩','交易单号');  //表头信息
                foreach($head as $k=>$v){
                    $head[$k] = iconv("UTF-8","GBK//IGNORE",$v);    //将utf-8编码转为gbk。理由是： Excel 以 ANSI 格式打开，不会做编码识别。如果直接用 Excel 打开 UTF-8 编码的 CSV 文件会导致汉字部分出现乱码。
                }
                $head = implode(',', $head) . PHP_EOL;
                $fileName=$root_directory.'temp/erppayment'.$current_user->id.$format;
                file_put_contents($fileName,$head);
                $myfile = fopen($fileName, "a");
                $data = [];  //要导出的数据的顺序与表头一致；提前将最后的值准备好（比如：时间戳转为日期等）

                foreach ($result as $key => $value) {
                    $parentdepartment=explode('::',$value['parentdepartment']);
                    array_shift($parentdepartment);
                    $department0='-';
                    $department1='-';
                    $department2='-';
                    $department3='-';
                    $department4='-';
                    $department5='-';
                    $department6='-';
                    $department7='-';
                    foreach($parentdepartment as $departKey=>$departValue){
                        $tempdepart='department'.$departKey;
                        $$tempdepart=$cachedepartment[$departValue];
                    }
                    $purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                    $data['owncompany']=str_replace(',','',$value['owncompany']);
                    $data['createtime']="\t".$value['createtime'];
                    $data['reality_date']="\t".$value['reality_date'];
                    $data['notescreatetime']="\t".$value['notescreatetime'];
                    $data['paytitle']=$value['paytitle'];
                    $data['paymentamount']=$value['paymentamount'];
                    $data['unit_prices']=$value['unit_prices'];
                    $data['unit_price']=$value['unit_price'];
                    $data['departmentid0']=$department0;
                    $data['departmentid1']=$department1;
                    $data['departmentid2']=$department2;
                    $data['departmentid3']=$department3;
                    $data['departmentid4']=$department4;
                    $data['departmentid5']=$department5;
                    $data['departmentid6']=$department6;
                    $data['departmentid7']=$department7;
                    $data['last_name']=$value['last_name'];
                    $data['title']=str_replace(',','',$value['title']);
                    $data['newrenewa']=$value['newrenewa'];
                    $data['accountname']=str_replace(',','',$value['accountname']);
                    $data['signdate']="\t".$value['signdate'];
                    $data['contract_no']=$value['contract_no'];
                    $data['parent_contracttype']=str_replace(',','',$value['parent_contracttype']);
                    $data['productsearchid']=str_replace(',','',$value['productsearchid']);
                    $data['contract_type']=str_replace(',','',$value['contract_type']);
                    $data['total']=$value['total'];
                    $data['costing']=$value['costing'];
                    $data['purchasemount']=$value['purchasemount'];
                    $data['waici']=$value['waici'];
                    $data['xalong']=$value['xalong'];
                    $data['meijai']=$value['meijai'];
                    $data['qite']=$value['qite'];
                    $data['purchase']=$purchasemount;
                    $data['extra_remark']=str_replace(',','',$value['extra_remark']);
                    $data['frommarketing']=$value['frommarketing'];
                    $data['departmentname_2']=$value['departmentname_2'];
                    $data['ismanualmatch']=$value['ismanualmatch'];
                    $data['bussinesstype']=$value['bussinesstype']?$lng[$value['bussinesstype']]:'';
                    $data['receivedstatus']=vtranslate($value['receivedstatus'],'ReceivedPayments');
                    $data['istimeoutmatch']=$value['istimeoutmatch'];
                    $data['iscrossmonthmatch']=$value['iscrossmonthmatch'];
                    $data['ischeckachievement']=$value['ischeckachievement'];
                    $data['paymentcode']=$value['paymentcode'];
                    foreach($data as $i =>$item){  //$item为一维数组哦
						$item1=str_replace(',','',$item);//去掉逗号，逗号影响CSV数据对齐
                        $data[$i] = iconv("UTF-8","GB18030//IGNORE",$item1);  //转为gbk的时候可能会遇到特殊字符“_”之类的会报错，加ignore表示这个特殊字符直接忽略不做转换。
                    }
                    $content= implode(',', $data) . PHP_EOL;
                    fwrite($myfile, $content);
                }
                fclose($myfile);
                echo file_get_contents($fileName);
                unlink($fileName);
                exit();
            }
    }elseif($strPublic == 'ExportR') {               //导出有效回款
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('DEPARTMENT',getDepartment());
        $viewer->view('exportr.tpl', $moduleName);
        exit;
    }elseif($strPublic=='ExportRD'){             //导出有效回款数据
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        if(!$moduleModel->exportGrouprt('ReceivedPayments','ExportR')){   //权限验证
            return;
        }
        $departments=$request->get('department');
        $startdate=$request->get('datatime');
        $enddatatime=$request->get('enddatatime');
        $listQuery='';
        $where=getAccessibleUsers();
        if($where!='1=1'){
            $listQuery= ' and vtiger_receivedpayments.createid '.$where;
        }
        ob_clean();                              //清空缓存
        header('Content-type: text/html;charset=utf-8');
        if(strtotime($startdate)<strtotime($enddatatime)){
            $sql="  between '{$startdate}' and '{$enddatatime}'";
        }elseif(strtotime($startdate)==strtotime($enddatatime)){
            $sql=" ='{$enddatatime}'";
        }elseif(strtotime($startdate)>strtotime($enddatatime)){
            $sql="  between '{$enddatatime}' and '{$startdate}'";
        }
        global $root_directory;
        $db=PearDatabase::getInstance();

        $query="SELECT
                    vtiger_performance_evaluation.last_name,
                    vtiger_performance_evaluation.totalprice,
                    vtiger_performance_evaluation.receivedate,
                    if(vtiger_performance_evaluation.receivedate>=vtiger_performance_evaluation.orderdate,vtiger_performance_evaluation.receivedate,vtiger_performance_evaluation.orderdate) AS revoceday,
                    vtiger_servicecontracts.contract_no,
                    vtiger_servicecontracts.signdate,
                    vtiger_servicecontracts.total,
                    vtiger_account.accountname,
                    vtiger_departments.departmentname,
                    vtiger_receivedpayments.unit_price*vtiger_achievementallot.scalling/100 AS rprice,
                    TRUNCATE((SELECT sum(IFNULL(vtiger_salesorderproductsrel.purchasemount,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid = vtiger_servicecontracts.servicecontractsid),2) AS purchasemount,
                    TRUNCATE((SELECT sum(IFNULL(vtiger_salesorderproductsrel.costing,0)*vtiger_achievementallot.scalling/100*vtiger_receivedpayments.unit_price/vtiger_servicecontracts.total) FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid = vtiger_servicecontracts.servicecontractsid),2) AS costing,
                    TRUNCATE((SELECT sum(IF (extra_type = '沙龙',IFNULL(extra_price, 0) * vtiger_achievementallot.scalling / 100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS xalong,
                    TRUNCATE((SELECT sum(IF(extra_type = '外采',IFNULL(extra_price, 0) * vtiger_achievementallot.scalling / 100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS waici,
                    TRUNCATE((SELECT sum(IF(extra_type = '媒介充值',IFNULL(extra_price, 0) * vtiger_achievementallot.scalling / 100,0)) FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS meijai,
                    TRUNCATE((SELECT sum(IF(extra_type != '沙龙' AND extra_type != '外采' AND extra_type = '媒介充值',IFNULL(extra_price, 0) * vtiger_achievementallot.scalling / 100,0)) FROM	vtiger_receivedpayments_extra	WHERE	vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_extra.receivementid),2) AS qite,
                    (SELECT dd.departmentname FROM vtiger_departments dd WHERE dd.parentdepartment=left(vtiger_departments.parentdepartment,10)) AS groupname
                FROM
                    vtiger_receivedpayments
                LEFT JOIN vtiger_performance_evaluation ON vtiger_receivedpayments.receivedpaymentsid = vtiger_performance_evaluation.receivedpaymentsid
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to
                LEFT JOIN vtiger_users ON vtiger_users.id= vtiger_performance_evaluation.userid
                LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                LEFT JOIN vtiger_achievementallot ON (vtiger_achievementallot.receivedpaymentsid=vtiger_performance_evaluation.receivedpaymentsid AND vtiger_achievementallot.receivedpaymentownid=vtiger_performance_evaluation.userid)
                WHERE
                    vtiger_receivedpayments.deleted=0 AND
                    if(vtiger_performance_evaluation.receivedate>=vtiger_performance_evaluation.orderdate,vtiger_performance_evaluation.receivedate {$sql},vtiger_performance_evaluation.orderdate {$sql})
                    {$listQuery}
                    ";
        $result= $db->run_query_allrecords($query);

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
            ->setCellValue('A1', '回款日期')
            ->setCellValue('B1', '回款金额')
            ->setCellValue('C1', '有效回款计算日期')
            ->setCellValue('D1', '有效回款金额')
            ->setCellValue('E1', '业务员所属事业部')
            ->setCellValue('F1', '销售组')
            ->setCellValue('G1', '业务员')
            ->setCellValue('H1', '合同签订日期')
            ->setCellValue('I1', '合同编号')
            ->setCellValue('J1', '合同金额')
            ->setCellValue('K1', '人力成本')
            ->setCellValue('L1', '外采成本')
            ->setCellValue('M1', '直接外采成本')
            ->setCellValue('N1', '沙龙支出')
            ->setCellValue('O1', '媒介充值')
            ->setCellValue('P1', '其他')
            ->setCellValue('Q1', '成本合计');
        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:Q1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:Q1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if(!empty($result)){
            foreach($result as $key=>$value){
                $current=$key+2;
                $purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A'.$current, $value['receivedate'])
                    ->setCellValue('B'.$current, $value['rprice'])
                    ->setCellValue('C'.$current, $value['revoceday'])
                    ->setCellValue('D'.$current, $value['totalprice'])
                    ->setCellValue('E'.$current, $value['groupname'])
                    ->setCellValue('F'.$current, $value['departmentname'])
                    ->setCellValue('G'.$current, $value['last_name'])
                    ->setCellValue('H'.$current, $value['signdate'])
                    ->setCellValue('I'.$current, $value['contract_no'])
                    ->setCellValue('J'.$current, $value['total'])
                    ->setCellValue('K'.$current, $value['costing'])
                    ->setCellValue('L'.$current, $value['purchasemount'])
                    ->setCellValue('M'.$current, $value['waici'])
                    ->setCellValue('N'.$current, $value['xalong'])
                    ->setCellValue('O'.$current, $value['meijai'])
                    ->setCellValue('P'.$current, $value['qite'])
                    ->setCellValue('Q'.$current, $purchasemount);
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':Q'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }



        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('有效回款');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="有效回款数据.xlsx"');
        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');


        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }elseif($strPublic == 'ExportRALL'){               //导出有效回款
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('exportrall.tpl', $moduleName);
            exit;
        }elseif($strPublic =='ExportNPP'){               //导出有效回款
            $moduleName = $request->getModule();
            $owncompany=ReceivedPayments_Record_Model::getowncompany();
            $viewer = $this->getViewer($request);
            $viewer->assign('OWNCOMPANY',$owncompany);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('exportrnpp.tpl', $moduleName);
            exit;
        }elseif($strPublic=='ExportNPPD'){             //导出有效回款数据
            set_time_limit(0);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ReceivedPayments','ExportNPP')){   //权限验证
                return;
            }
            $owncompany=$request->get('owncompany');
            $startdate=$request->get('datatime');
            $enddatatime=$request->get('enddatatime');
            $listQuery='';
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery= ' and vtiger_receivedpayments.createid '.$where;
            }
            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');
            if(strtotime($startdate)<strtotime($enddatatime)){
                $sql=" and reality_date  between '{$startdate}' and '{$enddatatime}'";
            }elseif(strtotime($startdate)==strtotime($enddatatime)){
                $sql=" and reality_date='{$enddatatime}'";
            }elseif(strtotime($startdate)>strtotime($enddatatime)){
                $sql=" and reality_date  between '{$enddatatime}' and '{$startdate}'";
            }


            if($owncompany!='all')
            {
                $owncompany=" and owncompany='{$owncompany}'";
            }else{
                $owncompany='';
            }
            global $root_directory;
            $db=PearDatabase::getInstance();

            $query="SELECT IF(fallinto=1,'是','否') as fallinto,vtiger_receivedpayments.newrenewa,IF(longagents=1,'是','否') as longagents,vtiger_receivedpayments.receivedstatus,vtiger_receivedpayments.departmentid,vtiger_receivedpayments.newdepartmentid,IF(ismatchdepart=1,'是','否') as ismatchdepart,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_receivedpayments.createid=vtiger_users.id) as createid,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_receivedpayments.checkid=vtiger_users.id) as checkid,vtiger_receivedpayments.paytitle, relatetoid,vtiger_receivedpayments.relatetoid as relatetoid_reference,IF(isguarantee=1,'是','否') as isguarantee,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_receivedpayments.guaranteeperson=vtiger_users.id) as guaranteeperson,vtiger_receivedpayments.receivementcurrencytype,IF(isdownpayment=1,'是','否') as isdownpayment,vtiger_receivedpayments.standardmoney,vtiger_receivedpayments.exchangerate,vtiger_receivedpayments.unit_price,vtiger_receivedpayments.overdue,vtiger_receivedpayments.createtime,vtiger_receivedpayments.modifiedtime,vtiger_receivedpayments.reality_date,vtiger_receivedpayments.owncompany,vtiger_receivedpayments.duedate,vtiger_receivedpayments.receivedpaymentsid,IFNULL(vtiger_account.accountname,'') AS acconame FROM vtiger_receivedpayments LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_receivedpayments.maybe_account
                    WHERE 1=1{$sql}{$owncompany} AND (relatetoid IS NULL OR relatetoid='') AND vtiger_receivedpayments.receivedstatus='normal'{$listQuery}";
            require 'crmcache/departmentanduserinfo.php';
            $result= $db->run_query_allrecords($query);

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
                ->setCellValue('A1', '回款日期')
                ->setCellValue('B1', '回款金额')
                ->setCellValue('C1', '汇款抬头')
                ->setCellValue('D1', '创建人')
                ->setCellValue('E1', '创建时间')
                ->setCellValue('F1', '货币类型')
                ->setCellValue('G1', '汇款账号')
                ->setCellValue('H1', '回款类型')
                ->setCellValue('I1', '可能客户')
                ->setCellValue('J1', '部门');
            //设置自动居中
            $phpexecl->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //$result=array(1,2,3,4,5,6,7,3,8,9,10);

            //设置边框
            $phpexecl->getActiveSheet()->getStyle('A1:J1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            if(!empty($result)){
                foreach($result as $key=>$value){
                    $current=$key+2;
                    $cachename=empty($value['departmentid'])||$value['departmentid']==""?'':$cachedepartment[$value['departmentid']];
                    $phpexecl->setActiveSheetIndex(0)
                        ->setCellValue('A'.$current, $value['reality_date'])
                        ->setCellValue('B'.$current, $value['unit_price'])
                        ->setCellValue('C'.$current, $value['paytitle'])
                        ->setCellValue('D'.$current, $value['createid'])
                        ->setCellValue('E'.$current, $value['createtime'])
                        ->setCellValue('F'.$current, $value['receivementcurrencytype'])
                        ->setCellValue('G'.$current, $value['owncompany'])
                        ->setCellValue('H'.$current, vtranslate($value['receivedstatus'],"ReceivedPayments"))
                        ->setCellValue('I'.$current, $value['acconame'])
                        ->setCellValue('J'.$current, $cachename);
                    //加上边框
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':J'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }
            // 设置工作表的名移
            $phpexecl->getActiveSheet()->setTitle('未匹配回款');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpexecl->setActiveSheetIndex(0);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="有效回款数据.xlsx"');
            header('Cache-Control: max-age=0');

            header('Cache-Control: max-age=1');


            header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }elseif($strPublic=='ExportRDALL'){             //导出有效回款按部门
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            do {
                $cmodule = $request->get('cmodule');
                if ($cmodule == 1) {
                    $cmodulename = 'ServiceContracts';
                } elseif ($cmodule == 2) {
                    $cmodulename = 'ReceivedPayments';
                }else{
                    echo "请先授权";
                    break;
                }
                $Permissions = ReceivedPayments_Record_Model::getImportUserPermissions($cmodulename);

                if ($Permissions){
                    $startdate = $request->get('datatime');
                    $enddatatime = $request->get('enddatatime');
                    if (strtotime($startdate) > strtotime($enddatatime)) {
                        $Temptime = $startdate;
                        $startdate = $enddatatime;
                        $enddatatime = $Temptime;
                    }
                    ob_clean();                              //清空缓存
                    header('Content-type: text/html;charset=utf-8');
                    $Permissionstemp = explode(',', $Permissions);
                    global $root_directory;
                    require_once $root_directory . 'libraries/PHPExcel/PHPExcel.php';

                    $phpexecl = new PHPExcel();
                    if ($cmodule == 1) {
                        $timechecked=$request->get('timeselected');
                        if($timechecked==1){
                            $checkedfield='vtiger_servicecontracts.signdate';
                            //$checkedlabel='签订日期';
                        }else{
                            $checkedfield='vtiger_servicecontracts.returndate';
                            //$checkedlabel='归还日期';
                        }
                        $phpexecl->getProperties()->setCreator("liu ganglin")
                            ->setLastModifiedBy("liu ganglin")
                            ->setTitle("Office 2007 XLSX servicecontracts Document")
                            ->setSubject("Office 2007 XLSX servicecontracts Document")
                            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
                            ->setKeywords("office 2007 openxml php")
                            ->setCategory("ServiceContracts");
                        $Temparray = array();
                        foreach ($Permissionstemp as $value) {
                            $userids = getDepartmentUser($value);
                            $Temparray = array_merge($Temparray, $userids);
                        }
                        $Temparray = array_unique($Temparray);

                        //以前的sql
                        /*$ttt = "SELECT
                                        vtiger_servicecontracts.contract_no,
                                        vtiger_account.accountname,
                                        vtiger_servicecontracts.contract_type,
                                        vtiger_servicecontracts.servicecontractstype,
                                        (vtiger_products.productname) as productid,
                                        vtiger_servicecontracts.modulestatus,
                                        IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid,
                                        vtiger_servicecontracts.receivedate,
                                        (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.signid=vtiger_users.id) as signid,
                                        vtiger_servicecontracts.signdate,
                                        (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.receiveid=vtiger_users.id) as receiveid,
                                        IF(firstcontract=1,'是','否') as firstcontract,
                                        vtiger_servicecontracts.returndate,
                                        vtiger_servicecontracts.currencytype,
                                        vtiger_servicecontracts.total,
                                        vtiger_servicecontracts.productsearchid,
                                        vtiger_servicecontracts.remark,
                                        vtiger_servicecontracts.firstreceivepaydate,
                                        vtiger_servicecontracts.servicecontractsid
                                    FROM vtiger_servicecontracts
                                    LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid
                                    LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to
                                    LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_servicecontracts.productid
                                    LEFT JOIN vtiger_servicecomments ON (vtiger_account.accountid = vtiger_servicecomments.related_to and vtiger_servicecomments.assigntype = 'accountby')
                                    WHERE
                                    1=1 and vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='c_complete' AND LEFT({$checkedfield},10) between '{$startdate}' and '{$enddatatime}' AND vtiger_servicecontracts.receiveid in(" . implode(',', $Temparray) . ")";*/

                        $ServiceContractsSql = "
                                    SELECT vtiger_servicecontracts.contract_no, vtiger_account.accountname, vtiger_servicecontracts.contract_type, vtiger_servicecontracts.servicecontractstype, ( vtiger_products.productname ) AS productid, vtiger_servicecontracts.modulestatus, IFNULL( ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ), '--' ) AS smownerid, vtiger_servicecontracts.receivedate, ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_servicecontracts.signid = vtiger_users.id ) AS signid, vtiger_servicecontracts.signdate, ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) FROM vtiger_users WHERE vtiger_servicecontracts.receiveid = vtiger_users.id ) AS receiveid, IF ( firstcontract = 1, '是', '否') AS firstcontract, vtiger_servicecontracts.returndate, vtiger_servicecontracts.currencytype, vtiger_servicecontracts.total, vtiger_servicecontracts.productsearchid, vtiger_servicecontracts.remark, vtiger_servicecontracts.firstreceivepaydate, vtiger_servicecontracts.servicecontractsid, (SELECT vtiger_users.last_name FROM vtiger_receivedpayments_notes LEFT JOIN vtiger_users ON vtiger_receivedpayments_notes.smownerid=vtiger_users.id WHERE vtiger_receivedpayments_notes.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid LIMIT 1) AS receivedpaymentsnotes, (SELECT vtiger_receivedpayments.createdtime FROM vtiger_receivedpayments_notes LEFT JOIN vtiger_users ON vtiger_receivedpayments_notes.smownerid=vtiger_users.id WHERE vtiger_receivedpayments_notes.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid LIMIT 1) AS receivedpaymentcreatedtime, IF(vtiger_account.frommarketing='1','是','否') AS frommarketing FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.relatetoid=vtiger_servicecontracts.servicecontractsid LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_servicecontracts.productid LEFT JOIN vtiger_servicecomments ON ( vtiger_account.accountid = vtiger_servicecomments.related_to AND vtiger_servicecomments.assigntype = 'accountby') WHERE 1=1 and vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='c_complete'
                                    AND LEFT({$checkedfield},10) between '{$startdate}' and '{$enddatatime}' AND vtiger_servicecontracts.receiveid in(" . implode(',', $Temparray) . ")";
                        global $adb;
                        require 'crmcache/departmentanduserinfo.php';
                        $result = $adb->run_query_allrecords($ServiceContractsSql);
                        $phpexecl->setActiveSheetIndex(0)
                            ->setCellValue('A1', '合同编号')
                            ->setCellValue('B1', '客户')
                            ->setCellValue('C1', '合同类型')
                            ->setCellValue('D1', '领取日期')
                            ->setCellValue('E1', '签订日期')
                            ->setCellValue('F1', '归还日期')
                            ->setCellValue('G1', '提单人')
                            ->setCellValue('H1', '签订人')
                            ->setCellValue('I1', '领取人')
                            ->setCellValue('J1', '新增/续费')
                            ->setCellValue('K1', '合同总额')
                            ->setCellValue('L1', '合同状态')
                            ->setCellValue('M1', '备注')
                            ->setCellValue('N1', '匹配人')
                            ->setCellValue('O1', '匹配时间')
                            ->setCellValue('P1', '是否来自市场部');
                        $phpexecl->getActiveSheet()->getStyle('A1:P1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        foreach ($result as $key => $value) {
                            $current = $key + 2;
                            $phpexecl->setActiveSheetIndex(0)
                                ->setCellValue('A' . $current, $value['contract_no'])
                                ->setCellValue('B' . $current, $value['accountname'])
                                ->setCellValue('C' . $current, $value['contract_type'])
                                ->setCellValue('D' . $current, $value['receivedate'])
                                ->setCellValue('E' . $current, $value['signdate'])
                                ->setCellValue('F' . $current, $value['returndate'])
                                ->setCellValue('G' . $current, $value['receiveid'])
                                ->setCellValue('H' . $current, $value['signid'])
                                ->setCellValue('I' . $current, $value['smownerid'])
                                ->setCellValue('J' . $current, $value['servicecontractstype'])
                                ->setCellValue('K' . $current, $value['total'])
                                ->setCellValue('L' . $current, '已签收')
                                ->setCellValue('M' . $current, $value['remark'])

                                ->setCellValue('N' . $current, $value['receivedpaymentsnotes'])
                                ->setCellValue('O' . $current, $value['receivedpaymentcreatedtime'])
                                ->setCellValue('P' . $current, $value['frommarketing']);
                            //加上边框
                            $phpexecl->getActiveSheet()->getStyle('A' . $current . ':P' . $current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        }
                        // 设置工作表的名移
                        $phpexecl->getActiveSheet()->setTitle('部门核对合同');
                        header('Content-Disposition: attachment;filename="部门核对合同.xlsx"');
                    } else {
                        $phpexecl->getProperties()->setCreator("liu ganglin")
                            ->setLastModifiedBy("liu ganglin")
                            ->setTitle("Office 2007 XLSX servicecontracts Document")
                            ->setSubject("Office 2007 XLSX servicecontracts Document")
                            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
                            ->setKeywords("office 2007 openxml php")
                            ->setCategory("ReceivedPayments");
                        $childsql = '';
                        foreach ($Permissionstemp as $val) {
                            $childsql .= " parentdepartment like concat((SELECT parentdepartment FROM `vtiger_departments` WHERE departmentid='{$val}'),'%') OR";
                        }
                        $childsql = rtrim($childsql, ' OR');
                        $sql=" AND (vtiger_receivedpayments.departmentid in(SELECT departmentid FROM vtiger_departments WHERE {$childsql}) OR vtiger_receivedpayments.newdepartmentid in(SELECT departmentid FROM vtiger_departments WHERE {$childsql}))";

                        $paymentssql = "SELECT
                                    vtiger_receivedpayments.unit_price,
                                vtiger_receivedpayments.reality_date,
                                vtiger_receivedpayments.departmentid,
                                vtiger_receivedpayments.paytitle,
                                vtiger_receivedpayments.newrenewa,
                                vtiger_receivedpayments.owncompany,
                                vtiger_receivedpayments.overdue,
                                vtiger_receivedpayments.newdepartmentid,
                                vtiger_receivedpayments.ismatchdepart,
                                vtiger_servicecontracts.contract_no,
                                vtiger_account.accountname
                                FROM
                                    vtiger_receivedpayments
                                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid
                                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_receivedpayments.maybe_account
                                WHERE
                                    LEFT(vtiger_receivedpayments.reality_date,10) between '{$startdate}' and '{$enddatatime}'{$sql} AND vtiger_receivedpayments.receivedstatus='normal'";
                        global $adb;
                        require 'crmcache/departmentanduserinfo.php';
                        $result = $adb->run_query_allrecords($paymentssql);

                        $phpexecl->setActiveSheetIndex(0)
                            ->setCellValue('A1', '原部门')
                            ->setCellValue('B1', '匹配部门')
                            ->setCellValue('C1', '汇款抬头')
                            ->setCellValue('D1', '回款类型')
                            ->setCellValue('E1', '公司账号')
                            ->setCellValue('F1', '金额')
                            ->setCellValue('G1', '入账日期')
                            ->setCellValue('H1', '合同编号')
                            ->setCellValue('I1', '可能客户')
                            ->setCellValue('J1', '备注');
                        $phpexecl->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        foreach ($result as $key => $value) {
                            $current = $key + 2;
                            $phpexecl->setActiveSheetIndex(0)
                                ->setCellValue('A' . $current, $cachedepartment[$value['departmentid']])
                                ->setCellValue('B' . $current, $cachedepartment[$value['newdepartmentid']])
                                ->setCellValue('C' . $current, $value['paytitle'])
                                ->setCellValue('D' . $current, $value['newrenewa'])
                                ->setCellValue('E' . $current, $value['owncompany'])
                                ->setCellValue('F' . $current, $value['unit_price'])
                                ->setCellValue('G' . $current, $value['reality_date'])
                                ->setCellValue('H' . $current, $value['contract_no'])
                                ->setCellValue('I' . $current, $value['accountname'])
                                ->setCellValue('J' . $current, $value['overdue']);
                            //加上边框
                            $phpexecl->getActiveSheet()->getStyle('A' . $current . ':J' . $current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        }
                        // 设置工作表的名移
                        $phpexecl->getActiveSheet()->setTitle('部门核对回款');
                        header('Content-Disposition: attachment;filename="部门核对回款.xlsx"');
                    }
                } else {
                    echo "请先授权";
                    break;
                }
                $phpexecl->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

                header('Cache-Control: max-age=0');

                header('Cache-Control: max-age=1');


                header('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
                header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header('Pragma: public'); // HTTP/1.0

                $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
                $objWriter->save('php://output');
            }while(0);
            exit;
        }elseif($strPublic =='ExportPerformanceSmall'){               //导出回款合同商务有效回款
            $moduleName = $request->getModule();
            //$owncompany=ReceivedPayments_Record_Model::getowncompany();
            $viewer = $this->getViewer($request);
            //$viewer->assign('OWNCOMPANY',$owncompany);
            $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(''));
            //$viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('smallperformance.tpl', $moduleName);
            exit;
        }elseif($strPublic=='ExportPerformanceSmallD'){             //导出回款合同商务有效回款数据
            set_time_limit(0);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ReceivedPayments','ExportPerformanceSmall')){   //权限验证
                return;
            }

            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');



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
            $classic=$request->get('classic');
            if($classic==2)
            {
                $moduleModel->exportSmallPerformancePerson($request,$phpexecl);
            }else{
                $moduleModel->exportSmallPerformanceAll($request,$phpexecl);
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="中小商务有效回款数据.xlsx"');
            header('Cache-Control: max-age=0');

            header('Cache-Control: max-age=1');


            header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }elseif($strPublic == 'ExportRM') {               //导出有效回款
            $moduleName = $request->getModule();
            $modmod=new ReceivedPayments_Module_Model();
            if(!$modmod->exportsalesetset()){
                parent::process($request);
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(''));
            $viewer->assign('RECOEDS',ReceivedPayments_Record_Model::getReportPermissions());
            include 'crmcache/departmentanduserinfo.php';

            $viewer->assign('USERDEPARTMENT',getDepartment());
            $viewer->assign('PARTMENTS',$cachedepartment);
            $viewer->view('exportrm.tpl', $moduleName);
            exit;
        }else if($strPublic == 'ExportCD') {
            set_time_limit(0);
            $record = $request->get('record');
            global $root_directory, $current_user, $site_URL;
            $path = $root_directory . 'temp/';
            $filename = $path . 'receivedpayments_changedetails' . $current_user->id . '.xlsx';
            !is_dir($path) && mkdir($path, '0777', true);
            @unlink($filename);
            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');
            $db = PearDatabase::getInstance();
            $query = "SELECT
	t3.owncompany,
	t3.reality_date,
	t3.paytitle,
	t1.changetime,
	t1.changetype,
	t1.old_contract_no,
	t7.accountname as old_accountname,
	t1.contract_no,
	t5.accountname,
	t1.old_staypaymentid,
	t1.staypaymentid
FROM
	vtiger_receivedpayments_changedetails t1
	LEFT JOIN vtiger_users t2 ON t1.changerid = t2.id 
	LEFT JOIN vtiger_receivedpayments t3 on t1.receivedpaymentsid=t3.receivedpaymentsid
	LEFT JOIN vtiger_servicecontracts t4 on t1.servicecontractsid=t4.servicecontractsid
	LEFT JOIN vtiger_account t5 on t4.sc_related_to=t5.accountid
	LEFT JOIN vtiger_servicecontracts t6 on t1.old_servicecontractsid=t6.servicecontractsid
	LEFT JOIN vtiger_account t7 on t6.sc_related_to=t7.accountid
WHERE
	t1.receivedpaymentsid =".$record." 
ORDER BY
	t1.changetime DESC";
            $result = $db->run_query_allrecords($query);
            require_once $root_directory . 'libraries/PHPExcel/PHPExcel.php';
            $phpexecl = new PHPExcel();
            // Set document properties
            $phpexecl->getProperties()->setCreator("tianxin")
                ->setLastModifiedBy("tianxin")
                ->setTitle("Office 2007 XLSX receivedpayments_changedetails Document")
                ->setSubject("Office 2007 XLSX receivedpayments_changedetails Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("receivedpayments_changedetails");
            // 添加头信处
            $phpexecl->setActiveSheetIndex(0)
                ->setCellValue('A1', '主体账户')
                ->setCellValue('B1', '入账时间')
                ->setCellValue('C1', '回款抬头')
                ->setCellValue('D1', '变更时间')
                ->setCellValue('E1', '变更前合同')
                ->setCellValue('F1', '变更前合同客户名称')
                ->setCellValue('G1', '变更前代付款证明')
                ->setCellValue('H1', '变更后合同')
                ->setCellValue('I1', '变更后合同客户名称')
                ->setCellValue('J1', '变更后代付款证明');
            //设置自动居中
            $phpexecl->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //设置边框
            $phpexecl->getActiveSheet()->getStyle('A1:J1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    $current = $key + 2;
                    if($value['old_staypaymentid']){
                        $sql="select * from vtiger_salesorderworkflowstages where salesorderid=".$value['old_staypaymentid']."  and modulename='Staypayment'";
                        $res = $db->run_query_allrecords($sql);
                        if($res){
                            $actionArray=array_column($res,'isaction');
                            if(in_array(1,$actionArray)||in_array(0,$actionArray)){
                                //没有1和0代表流程已走完是有效的代付款
                                $value['old_staypaymentid']='无';
                            }else{
                                $value['old_staypaymentid']='有';
                            }
                        }else{
                            $value['old_staypaymentid']='无';
                        }
                    }
                    if($value['staypayment']){
                        $sql="select * from vtiger_salesorderworkflowstages where salesorderid=".$value['staypayment']."  and modulename='Staypayment'";
                        $res = $db->run_query_allrecords($sql);
                        if($res){
                            $actionArray=array_column($res,'isaction');
                            if(in_array(1,$actionArray)||in_array(0,$actionArray)){
                                //没有1和0代表流程已走完是有效的代付款
                                $value['staypayment']='无';
                            }else{
                                $value['staypayment']='有';
                            }
                        }else{
                            $value['staypayment']='无';
                        }
                    }
                    $phpexecl->setActiveSheetIndex(0)
                        ->setCellValue('A' . $current, $value['owncompany'])
                        ->setCellValue('B' . $current, $value['reality_date'])
                        ->setCellValue('C' . $current, $value['paytitle'])
                        ->setCellValue('D' . $current, $value['changetime'])
                        ->setCellValue('E' . $current, $value['old_contract_no'])
                        ->setCellValue('F' . $current, $value['old_accountname'])
                        ->setCellValue('G' . $current, $value['old_staypaymentid'])
                        ->setCellValue('H' . $current, $value['contract_no'])
                        ->setCellValue('I' . $current, $value['accountname'])
                        ->setCellValue('J' . $current, $value['staypayment']);
                    //加上边框
                    $phpexecl->getActiveSheet()->getStyle('A' . $current . ':J' . $current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }
            // 设置工作表的名移
            $phpexecl->getActiveSheet()->setTitle('回款变更详情');
            $phpexecl->setActiveSheetIndex(0);
            $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
            //$objWriter->save('php://output');
            $objWriter->save($filename);
            header('location:' . $site_URL . '/temp/receivedpayments_changedetails' . $current_user->id . '.xlsx');
            exit;
        }
        // 回款拆分的权限
        global $adb, $current_user;
        $sql = "select * FROM vtiger_custompowers where custompowerstype='split_received_rayments' OR custompowerstype='receivedpaymentsEdit' OR custompowerstype='isEditAllowinvoicetotal' OR custompowerstype='receivedpaymentsRepeat'";
        $sel_result = $adb->pquery($sql, array());
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt > 0) {
            while($row=$adb->fetch_array($sel_result)) {
                $roles_arr = explode(',', $row['roles']);
                $user_arr = explode(',', $row['user']);
                $viewer = $this->getViewer($request);
                if (in_array($current_user->current_user_roles, $roles_arr) || in_array($current_user->id, $user_arr)) {
                    if($row['custompowerstype'] =='split_received_rayments'){
                        $viewer->assign('IS_SPLIT', 1);
                    } else if($row['custompowerstype'] =='receivedpaymentsEdit'){
                        $viewer->assign('IS_EDIT', 1);
                    } else if($row['custompowerstype'] =='isEditAllowinvoicetotal') {
                        $viewer->assign('isEditAllowinvoicetotal', 1);
                    }else if($row['custompowerstype'] =='receivedpaymentsRepeat') {
                        $viewer->assign('ISREPEATRECEIVEDPAYMENTS', 1);
                    }
                }
            }
        }
        parent::process($request);
    }

    /**
     * 获得真实的账户充值币
     * @param $receivedpaymentsid
     */
    public function  setChongZhiBi($receivedpaymentsid){
        $request=new Vtiger_Request(array());
        $request->set('record',$receivedpaymentsid);
        $recordClass=new  ReceivedPayments_Record_Model();
        $receivedPaymentsUseDetail=$recordClass->getReceivedPaymentsUseDetail($request);
        return array_sum(array_column($receivedPaymentsUseDetail,'rate'));
    }

}