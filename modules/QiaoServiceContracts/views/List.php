<?php

class QiaoServiceContracts_List_View  extends Vtiger_KList_View{


    function preProcess(Vtiger_Request $request, $display=true) {


        parent::preProcess($request, false);
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        //$this->viewName = $request->get('viewname');
        // $viewer->assign('VIEWNAME', $this->viewName);
        global $current_user;
        $userId=$current_user->id;
        $viewer->assign('USERID',$userId);
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName);
        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));// module 和action

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        // $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        if($request->get('public') == 'NoComplete'){
            $viewer->assign('SEARCHRECORD_STRUCTURE', $moduleModel->getNoCompleteSearchFields());
        }else{
            $viewer->assign('SEARCHRECORD_STRUCTURE', $moduleModel->getSearchFields());
        }
        $viewer->assign('SOURCE_MODULE',$moduleName);

        $quickLinkModels = $listViewModel->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $quickLinkModels);




        /*    if(empty($this->viewName)){
               //If not view name exits then get it from custom view
               //This can return default view id or view id present in session
               $customView = new CustomView();
               $this->viewName = $customView->getViewId($moduleName);
           } */
        $this->initializeListViewContents($request, $viewer);//竟然调用两
        //$viewer->assign('VIEWID', $this->viewName);

        if($display) {
            $this->preProcessDisplay($request);
        }

    }

    function process (Vtiger_Request $request)
    {


        $strPublic = $request->get('public');

        if ($strPublic == 'Export') {               //导出
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('SETTLEMENTMONTH',$this->settlementMonth());
            $viewer->view('export.tpl', $moduleName);
            exit;
        }elseif ($strPublic == 'ExportRI') {               //导出
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('exportri.tpl', $moduleName);
            exit;
        }elseif ($strPublic == 'ExportRIV') {               //导出
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('exportriv.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'ExportRIS') {               //导出
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('exportris.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'ExportRINV') {               //导出
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('exportrinv.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'isfulldelivery') {               //已签收合同导出
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','isfulldelivery')){   //权限验证
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->view('isfulldelivery.tpl', $moduleName);
            return ;
        }elseif($strPublic == 'isfulldeliverydata') {               //已签收合同导出
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','isfulldelivery')){   //权限验证
                return;
            }
            ob_clean();
            $moduleModel->isFulldeliveryData($request);
            exit ;
        }elseif ($strPublic == 'protected') {               //超领设置

            $moduleName = $request->getModule();
            $moduleModel=Vtiger_Module_Model::getInstance($moduleName);
            if(!$moduleModel->exportGrouprt('ServiceContracts','protected')){   //权限验证
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',$moduleModel->getUserInfo());
            $viewer->assign('RECOEDS',$moduleModel->getProtectData());
            $viewer->view('protectedsetting.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'ExportComplete') {               //已签收合同导出
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('exportrincomplete.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'ExportCompleteD') {
            //已签收合同导出
            set_time_limit(0);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','ExportComplete')){   //权限验证
                return;
            }
            $departments=$request->get('department');
            $startdate=$request->get('datatime');
            $enddatatime=$request->get('enddatatime');
            $array=array();
            if(!empty($departments))
            {
                foreach($departments as $departmentid)
                {
                    $userid=getDepartmentUser($departmentid);
                    $array=array_merge($userid,$array);
                }

            }
            else
            {
                $array=getDepartmentUser('H1');
            }

            $listQuery= ' and vtiger_servicecontracts.signid IN('.implode(',',$array).')';

            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');
            if(strtotime($startdate)<strtotime($enddatatime)){
                $listQuery.=" and vtiger_servicecontracts.returndate  between '{$startdate}' and '{$enddatatime}'";
            }elseif(strtotime($startdate)==strtotime($enddatatime)){
                $listQuery.=" vtiger_servicecontracts.returndate='{$enddatatime}'";
            }elseif(strtotime($startdate)>strtotime($enddatatime)){
                $listQuery.=" vtiger_servicecontracts.returndate between '{$enddatatime}' and '{$startdate}'";
            }
            global $root_directory;
            $db=PearDatabase::getInstance();

            $query="SELECT
                            vtiger_servicecontracts.contract_no,
                            vtiger_account.accountname,
                            vtiger_servicecontracts.contract_type,
                            '已签收' AS statuss,
                            if(vtiger_servicecontracts.isstandard=1,'是','否') AS isstandard,
                            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.signid=vtiger_users.id) as signid,
							(SELECT GROUP_CONCAT(if(vtiger_files.style='files_style1','A',if(vtiger_files.style='files_style2','B',if(vtiger_files.style='files_style3','C',if(vtiger_files.style='files_style4','D',if(vtiger_files.style='files_style5','E',if(vtiger_files.style='files_style6','F','G'))))))) FROM vtiger_files WHERE vtiger_files.relationid=vtiger_servicecontracts.servicecontractsid AND vtiger_files.delflag=0) AS cstatus,
                            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.receiveid=vtiger_users.id) as receiveid, 
                            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as reptid,
                             vtiger_products.productname,
                            vtiger_servicecontracts.receivedate,
                            vtiger_servicecontracts.signdate,
                            vtiger_servicecontracts.returndate,
                            vtiger_servicecontracts.bussinesstype,
                            vtiger_servicecontracts.invoicecompany,
                            vtiger_servicecontracts.total,
                            vtiger_servicecontracts.remark
                        FROM
                            vtiger_servicecontracts
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                        LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
                        LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_servicecontracts.productid
                        WHERE
                            vtiger_servicecontracts.modulestatus = 'c_complete'
                        AND vtiger_crmentity.deleted = 0
                        {$listQuery}
                        ORDER BY
                            vtiger_servicecontracts.signdate,
                            vtiger_servicecontracts.contract_no";
            $result= $db->run_query_allrecords($query);

            include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';

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
                ->setCellValue('A1', '合同编号')
                ->setCellValue('B1', '客户名称')
                ->setCellValue('C1', '合同类型')
                ->setCellValue('D1', '合同套餐')
                ->setCellValue('E1', '合同状态')
                ->setCellValue('F1', '非标合同')
                ->setCellValue('G1', '领用日期')
                ->setCellValue('H1', '签订人员')
                ->setCellValue('I1', '签订日期')
                ->setCellValue('J1', '归还日期')
                ->setCellValue('K1', '合同金额')
                ->setCellValue('L1', '领取人')
                ->setCellValue('M1', '提单人')
                ->setCellValue('N1', '附件')
                ->setCellValue('O1', '备注&说明')
                ->setCellValue('P1', '业务类型')
                ->setCellValue('Q1', '合同主体');
            //设置自动居中
            $phpexecl->getActiveSheet()->getStyle('A1:O1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //设置边框
            $phpexecl->getActiveSheet()->getStyle('A1:O1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            if(!empty($result)){
                foreach($result as $key=>$value){
                    $current=$key+2;
                    //$purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                    $phpexecl->setActiveSheetIndex(0)
                        ->setCellValueExplicit('A'.$current, $value['contract_no'])
                        ->setCellValueExplicit('B'.$current, $value['accountname'])
                        ->setCellValueExplicit('C'.$current, $value['contract_type'])
                        ->setCellValueExplicit('D'.$current, $value['productname'])
                        ->setCellValueExplicit('E'.$current, $value['statuss'])
                        ->setCellValueExplicit('F'.$current, $value['isstandard'])
                        ->setCellValueExplicit('G'.$current, $value['receivedate'])
                        ->setCellValueExplicit('H'.$current, $value['signid'])
                        ->setCellValueExplicit('I'.$current, $value['signdate'])
                        ->setCellValueExplicit('J'.$current, $value['returndate'])
                        ->setCellValueExplicit('K'.$current, $value['total'])
                        ->setCellValueExplicit('L'.$current, $value['reptid'])
                        ->setCellValueExplicit('M'.$current, $value['receiveid'])
                        ->setCellValueExplicit('N'.$current, $value['cstatus'])
                        ->setCellValueExplicit('O'.$current, $value['remark'])
                        ->setCellValueExplicit('P'.$current, vtranslate($value['bussinesstype'],'ServiceContracts'))
                        ->setCellValueExplicit('Q'.$current, $value['invoicecompany']);

                    //加上边框
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':Q'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }



            // 设置工作表的名移
            $phpexecl->getActiveSheet()->setTitle('已签收合同导出');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpexecl->setActiveSheetIndex(0);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="已签收合同导出.xlsx"');
            header('Cache-Control: max-age=0');

            header('Cache-Control: max-age=1');


            header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }elseif($strPublic=='Received' || $strPublic=='Returned' || $strPublic=='NoSignReturned' ||$strPublic=='notsign' ) {               //领取
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','Received')){   //权限验证
                return;
            }
            $arrStatus=array('Received'=>1,'Returned'=>2,'NoSignReturned'=>3,'notsign'=>4);
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->assign('CSELECTED',$arrStatus[$strPublic]);
            $viewer->view('received.tpl', $moduleName);
            exit;
            // cxh 2020-05-09 start
        }elseif($strPublic == 'contractCheck'){
            global  $current_user;
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $LASTNAME=$current_user->last_name."[".$current_user->department."]";
            $viewer->assign("LASTNAME",$LASTNAME);
            $viewer->view('contractCheck.tpl', $moduleName);
            exit;
            // cxh 2020-05-09 end
        }elseif($strPublic == 'Changelead') {               //变更领用人
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','Changelead')){   //权限验证
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('Changelead.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'ContractCancel') {               //申请作废
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','ContractCancel')){   //权限验证
                //return;
            }
            $viewer = $this->getViewer($request);
            //$viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('ContractCancel.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'dempartConfirm') {//非标合同部门负责审核设置
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','dempartConfirm')){   //权限验证
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(" AND vtiger_users.`status`='Active'"));
            $viewer->assign('RECOEDS',ServiceContracts_Record_Model::getAuditsettings('ContractsAuditset'));
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('dempartConfirm.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'OrderCancelExport') {               //领取
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('SalesOrder','OrderCancelExport')){   //权限验证
                return;
            }
            $viewer = $this->getViewer($request);
            //$viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('ordercancel.tpl', $moduleName);
            exit;
        }elseif($strPublic=='ExportD'){             //导出数据
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGroup()){   //权限验证
                return;
            }

            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');
            $arrMonth = $this->settlementMonth();

            global $dbconfig;
            $conn = mysql_connect($dbconfig['db_server'],$dbconfig['db_username'],$dbconfig['db_password']) or die ("数据连接错误!!!");//原生的支持存储过程
            mysql_query("set names 'utf8' ");
            mysql_query("set character_set_client=utf8");
            mysql_query("set character_set_results=utf8");
            mysql_select_db($dbconfig['db_name'],$conn);

            $b=mysql_query("call sp_makeproductprice('".$arrMonth['Received'][0]."','".$arrMonth['Received'][1]."','".$arrMonth['System'][0]."','".$arrMonth['System'][1]."')");//执行
            $result=mysql_query('call sp_makeproductprice_make(1)');
            $temp ='<table cellpadding="0" cellspacing="0" width="100%" style="width:568px;"><tbody>';
            $temp.='<tr height="22" style=";height:22px" class="firstRow"><td height="22" width="171">销售组</td><td width="57" style="">业务员</td><td width="59" style="">状态</td><td width="355" style="">客户名称</td><td width="104" style="">合同签订日期</td><td width="164" style="">合同编号</td><td width="103" style="">工单编号</td><td width="292" style="">合同业务</td><td width="76" style="">合同金额</td><td width="121" style="">第一次收款时间</td><td width="72" style="">收款金额</td><td width="76" style="">应收金额</td><td width="76" style="">未收款项</td><td width="104" style="">人力成本合计</td><td width="104" style="">外采成本合计</td><td width="104" style="">成本合计</td><td width="88" style="">IDC内采成本</td><td width="88" style="">IDC外采成本</td><td width="88" style="">IDC明细</td><td width="104" style="">开发部成本</td><td width="104" style="">开发部市场价</td><td width="104" style="">开发部明细</td><td width="104" style="">创建时间</td><td width="104" style="">分成比例</td></tr>';
            while($row = mysql_fetch_array($result)){
                $temp.="<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td><td>".$row[11]."</td><td>".$row[12]."</td><td>".$row[13]."</td><td>".$row[14]."</td><td>".$row[15]."</td><td>".$row[16]."</td><td>".$row[17]."</td><td>".$row[18]."</td><td>".$row[19]."</td><td>".$row[20]."</td><td>".$row[21]."</td><td>".$row[22]."</td><td>".$row[25]."%</td></tr>";
            }
            $temp.='</table>';

            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=数据.xls");
            //echo mb_convert_encoding($temp,'gb2312','utf-8');
            echo $temp;
            exit;
        }elseif($strPublic=='OrderCancelExportD'){             //导出作废工单数据
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('SalesOrder','OrderCancelExport')){   //权限验证
                return;
            }
            $startdate=$request->get('datatime');
            $enddatatime=$request->get('enddatatime');
            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');
            if(strtotime($startdate)<strtotime($enddatatime)){
                $sql=" AND left(vtiger_salesorder.voiddatetime,10) between '{$startdate}' and '{$enddatatime}'";
            }elseif(strtotime($startdate)==strtotime($enddatatime)){
                $sql=" AND left(vtiger_salesorder.voiddatetime,10)='{$enddatatime}'";
            }elseif(strtotime($startdate)>strtotime($enddatatime)){
                $sql=" AND left(vtiger_salesorder.voiddatetime,10) between '{$enddatatime}' and '{$startdate}'";
            }
            global $root_directory;
            $db=PearDatabase::getInstance();

            $query="SELECT 
                        vtiger_salesorder.salesorder_no,
                        vtiger_salesorder.voidreason,
                        vtiger_salesorder.subject,
                        vtiger_salesorder.modulestatus,
                        IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid,
                        (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_salesorder.voiduserid=vtiger_users.id) as voiduserid,
                        vtiger_salesorder.voiddatetime,
                        (vtiger_account.accountname) as accountid,
                        (vtiger_servicecontracts.contract_no) as servicecontractsid,
                        (select createdtime from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_salesorder.salesorderid and vtiger_crmentity.deleted=0) as createdtime,
                        IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smcreatorid=vtiger_users.id),'--') as createower
                    FROM vtiger_salesorder 
                    LEFT JOIN vtiger_crmentity ON vtiger_salesorder.salesorderid = vtiger_crmentity.crmid 
                    LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesorder.accountid 
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_salesorder.servicecontractsid 
                    WHERE vtiger_crmentity.deleted=0 and ((vtiger_salesorder.modulestatus = 'c_cancel' AND vtiger_salesorder.modulestatus IS NOT NULL)) {$sql}
                    ORDER BY vtiger_salesorder.salesorderid DESC";
            $result= $db->run_query_allrecords($query);

            include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';

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
                ->setCellValue('A1', '工单主题')
                ->setCellValue('B1', '工单单号')
                ->setCellValue('C1', '合同编号')
                ->setCellValue('D1', '客户名称')
                ->setCellValue('E1', '业务员')
                ->setCellValue('F1', '提单人')
                ->setCellValue('G1', '提单日期')
                ->setCellValue('H1', '作废原因')
                ->setCellValue('I1', '作废人员')
                ->setCellValue('J1', '作废日期');

            //设置自动居中
            $phpexecl->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //$result=array(1,2,3,4,5,6,7,3,8,9,10);
            //设置边框
            $phpexecl->getActiveSheet()->getStyle('A1:J1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            if(!empty($result)){
                foreach($result as $key=>$value){
                    $current=$key+2;
                    //$purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                    $phpexecl->setActiveSheetIndex(0)
                        ->setCellValueExplicit('A'.$current, $value['subject'])
                        ->setCellValueExplicit('B'.$current, $value['salesorder_no'])
                        ->setCellValueExplicit('C'.$current, $value['servicecontractsid'])
                        ->setCellValueExplicit('D'.$current, $value['accountid'])
                        ->setCellValueExplicit('E'.$current, $value['smownerid'])
                        ->setCellValueExplicit('F'.$current, $value['createower'])
                        ->setCellValueExplicit('G'.$current, $value['createdtime'])
                        ->setCellValueExplicit('H'.$current, $value['voidreason'])
                        ->setCellValueExplicit('I'.$current, $value['voiduserid'])
                        ->setCellValueExplicit('J'.$current, $value['voiddatetime']);


                    //加上边框
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':J'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }



            // 设置工作表的名移
            $phpexecl->getActiveSheet()->setTitle('作废工单');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpexecl->setActiveSheetIndex(0);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="作废工单'.date('Y-m-d').time().'.xlsx"');
            header('Cache-Control: max-age=0');

            header('Cache-Control: max-age=1');


            header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }elseif($strPublic=='ExportRID'){             //导出数据
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','ExportRI')){   //权限验证
                return;
            }
            $searchDepartment=$request->get('department');
            $startdate=$request->get('datatime');
            $enddatatime=$request->get('enddatatime');
            $timechecked=$request->get('timeselected');
            $listQuery='';
            if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
                $userid=getDepartmentUser($searchDepartment);
                $where=getAccessibleUsers('ServiceContracts','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                $listQuery= ' and vtiger_servicecontracts.signid in('.implode(',',$where).')';
            }else{
                $where=getAccessibleUsers();
                if($where!='1=1'){
                    $listQuery= ' and vtiger_servicecontracts.signid '.$where;
                }
            }
            if($timechecked==1){
                $checkedfield='vtiger_servicecontracts.signdate';
                $checkedlabel='签订日期';
            }else{
                $checkedfield='vtiger_servicecontracts.returndate';
                $checkedlabel='归还日期';
            }
            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');

            global $dbconfig;
            $conn = mysql_connect($dbconfig['db_server'].$dbconfig['db_port'],$dbconfig['db_username'],$dbconfig['db_password']) or die ("数据连接错误!!!");//原生的支持存储过程
            mysql_query("set names 'utf8' ");
            mysql_query("set character_set_client=utf8");
            mysql_query("set character_set_results=utf8");
            mysql_select_db($dbconfig['db_name'],$conn);
            /*$query="SELECT
                    if(vtiger_servicecontracts.firstfrommarket=1,'是','否') AS firstfrommarket,if(vtiger_servicecontracts.firstcontract=1,'是','否') AS firstcontract,vtiger_servicecontracts.contract_no,vtiger_account.accountname,left({$checkedfield},10) AS signdate,vtiger_servicecontracts.total,vtiger_parent_contracttype.parent_contracttype,
                    if(vtiger_receivedpayments.isguarantee=1,'是','否') as guarantee,vtiger_receivedpayments.unit_price,left(vtiger_receivedpayments.reality_date,10) AS reality_date,vtiger_receivedpayments.owncompany,vtiger_invoice.businessnames,
                    (SELECT sum(IFNULL(unit_price,0)) FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid) AS allunit_price,
                    TRUNCATE((SELECT sum(IFNULL(taxtotal,0)) FROM vtiger_invoice WHERE vtiger_servicecontracts.servicecontractsid = vtiger_invoice.contractid),2) AS alltotal,
                     vtiger_invoiceextend.invoice_noextend AS invoice_no,vtiger_invoice.taxtotal,vtiger_invoice.invoicecompany,
                    (SELECT last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_servicecontracts.receiveid) as lastname,
                    (SELECT vtiger_departments.departmentname FROM vtiger_departments LEFT JOIN vtiger_user2department ON vtiger_departments.departmentid=vtiger_user2department.departmentid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.smownerid=vtiger_user2department.userid WHERE vtiger_crmentity.crmid = vtiger_account.accountid) AS department,
                    (SELECT last_name FROM vtiger_users  LEFT JOIN vtiger_crmentity ON vtiger_crmentity.smownerid=vtiger_users.id WHERE vtiger_crmentity.crmid=vtiger_account.accountid) AS accountuser,vtiger_salesorder.salesorder_no
                    FROM
                        vtiger_servicecontracts
                    LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid
                    LEFT JOIN vtiger_invoice ON vtiger_servicecontracts.servicecontractsid = vtiger_invoice.contractid
                    LEFT JOIN vtiger_invoiceextend ON vtiger_invoice.invoiceid=vtiger_invoiceextend.invoiceid
                    LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid
                    LEFT JOIN vtiger_salesorder ON vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                    LEFT JOIN vtiger_parent_contracttype ON vtiger_parent_contracttype.parent_contracttypeid=vtiger_servicecontracts.parent_contracttypeid
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid
                    WHERE vtiger_servicecontracts.modulestatus='c_complete' AND vtiger_crmentity.deleted=0 AND EXISTS (SELECT 1 FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid WHERE vtiger_users.id = vtiger_servicecontracts.signid AND vtiger_user2department.departmentid IN((SELECT vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.parentdepartment REGEXP '{$departments}'))) AND left({$checkedfield},10) BETWEEN '{$startdate}' AND '{$enddatatime}'";*/
            $query="SELECT
                    IF(vtiger_account.frommarketing=1,'是', '否') AS frommarketing,
                    IF(vtiger_servicecontracts.firstfrommarket = 1,
                        (SELECT vtiger_leaddetails.company FROM vtiger_leaddetails WHERE vtiger_leaddetails.accountid=vtiger_account.accountid limit 1),
                        (SELECT vtiger_leaddetails.company FROM vtiger_leaddetails WHERE vtiger_leaddetails.company=vtiger_account.accountname limit 1)
                    ) AS leandetails_company,
                    if(vtiger_servicecontracts.firstfrommarket=1,'是','否') AS firstfrommarket,if(vtiger_servicecontracts.firstcontract=1,'是','否') AS firstcontract,vtiger_servicecontracts.contract_no,vtiger_account.accountname,left({$checkedfield},10) AS signdate,vtiger_servicecontracts.total,vtiger_parent_contracttype.parent_contracttype,
                    if(vtiger_receivedpayments.isguarantee=1,'是','否') as guarantee,vtiger_receivedpayments.unit_price,left(vtiger_receivedpayments.reality_date,10) AS reality_date,vtiger_receivedpayments.owncompany,
                    (SELECT sum(IFNULL(unit_price,0)) FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid) AS allunit_price,
                    (SELECT last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_servicecontracts.receiveid) as lastname,
                    vtiger_servicecontracts.pre_deposit,
		    vtiger_servicecontracts.signdempart,
                    vtiger_servicecontracts.service_charge,
                    vtiger_servicecontracts.account_opening_fee,
                    vtiger_servicecontracts.tax_point,
                    (SELECT vtiger_departments.departmentname FROM vtiger_departments LEFT JOIN vtiger_user2department ON vtiger_departments.departmentid=vtiger_user2department.departmentid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.smownerid=vtiger_user2department.userid WHERE vtiger_crmentity.crmid = vtiger_account.accountid) AS department,
                    (SELECT last_name FROM vtiger_users  LEFT JOIN vtiger_crmentity ON vtiger_crmentity.smownerid=vtiger_users.id WHERE vtiger_crmentity.crmid=vtiger_account.accountid) AS accountuser
                    FROM
                        vtiger_servicecontracts
                    LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid
                    LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid
                    LEFT JOIN vtiger_parent_contracttype ON vtiger_parent_contracttype.parent_contracttypeid=vtiger_servicecontracts.parent_contracttypeid
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid
                    WHERE vtiger_servicecontracts.modulestatus='c_complete' AND vtiger_crmentity.deleted=0 {$listQuery} AND left({$checkedfield},10) BETWEEN '{$startdate}' AND '{$enddatatime}'";
            $result=mysql_query($query);
            global $root_directory;
            require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
            include 'crmcache/departmentanduserinfo.php';

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
                ->setCellValue('B1', '所属合同编码')
                ->setCellValue('C1', '合同抬头（客户名称）')
                ->setCellValue('D1', $checkedlabel)
                ->setCellValue('E1', '合同金额')
                ->setCellValue('F1', '合同类别')
                ->setCellValue('G1', '签约部门')
                ->setCellValue('H1', '提单人')
                ->setCellValue('I1', '销售组')
                ->setCellValue('J1', '客户负责人')
                ->setCellValue('K1', '合同执行开始时间')
                ->setCellValue('L1', '合同执行结束时间')
                ->setCellValue('M1', '服务执行阶段')
                ->setCellValue('N1', '预存费')
                ->setCellValue('O1', '服务费')
                ->setCellValue('P1', '开户费')
                ->setCellValue('Q1', '税点')
                ->setCellValue('R1', '是否有担保')
                ->setCellValue('S1', '汇款抬头')
                ->setCellValue('T1', '收款金额')
                ->setCellValue('U1', '该合同收款金额合计')
                ->setCellValue('V1', '收款日期')
                ->setCellValue('W1', '公司收款账号')
                ->setCellValue('X1', '收款方')
                ->setCellValue('Y1', '来自市场部')
                ->setCellValue('Z1', '第一份合同')
                ->setCellValue('AA1', '商机名称')
                ->setCellValue('AB1', '是否来自市场部');

            //设置自动居中
            $phpexecl->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //$result=array(1,2,3,4,5,6,7,3,8,9,10);

            //设置边框
            $phpexecl->getActiveSheet()->getStyle('A1:Z1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $current=2;
            if(!empty($result)) {
                while ($row = mysql_fetch_array($result)) {
                    $owncompany = explode('##', $row['owncompany']);

                    $phpexecl->setActiveSheetIndex(0)
                        ->setCellValue('A' . $current, $current - 1)
                        ->setCellValue('B' . $current, $row['contract_no'])
                        ->setCellValue('C' . $current, $row['accountname'])
                        ->setCellValue('D' . $current, $row['signdate'])
                        ->setCellValue('E' . $current, $row['total'])
                        ->setCellValue('F' . $current, $row['parent_contracttype'])
                        ->setCellValue('G' . $current, $cachedepartment[$row['signdempart']])
                        ->setCellValue('H' . $current, $row['lastname'])
                        ->setCellValue('I' . $current, $row['department'])
                        ->setCellValue('J' . $current, $row['accountuser'])
                        ->setCellValue('K' . $current, '')
                        ->setCellValue('L' . $current, '')
                        ->setCellValue('M' . $current, '')
                        ->setCellValue('N' . $current, $row['pre_deposit'])
                        ->setCellValue('O' . $current, $row['service_charge'])
                        ->setCellValue('P' . $current, $row['account_opening_fee'])
                        ->setCellValue('Q' . $current, $row['tax_point'])
                        ->setCellValue('R' . $current, $row['guarantee'])
                        ->setCellValue('S' . $current, $row['paytitle'])
                        ->setCellValue('T' . $current, $row['unit_price'])
                        ->setCellValue('U' . $current, $row['allunit_price'])
                        ->setCellValue('V' . $current, $row['reality_date'])
                        ->setCellValue('W' . $current, $owncompany[1])
                        ->setCellValue('X' . $current, $owncompany[0])
                        ->setCellValue('Y' . $current, $row['firstfrommarket'])
                        ->setCellValue('Z' . $current, $row['firstcontract'])
                        ->setCellValue('AA' . $current, $row['leandetails_company'])
                        ->setCellValue('AB' . $current, $row['frommarketing']);
                    //加上边框
                    $phpexecl->getActiveSheet()->getStyle('A' . $current . ':AB' . $current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    $current++;
                }
            }

            // 设置工作表的名移
            $phpexecl->getActiveSheet()->setTitle('合同回款发票数据');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpexecl->setActiveSheetIndex(0);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="合同回款发票数据.xlsx"');
            header('Cache-Control: max-age=0');

            header('Cache-Control: max-age=1');


            header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
            $objWriter->save('php://output');
            exit;

        }elseif($strPublic=='ExportRIVD'){             //导出数据
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','ExportRIV')){   //权限验证
                return;
            }
            $departments=$request->get('department');
            $startdate=$request->get('datatime');
            $enddatatime=$request->get('enddatatime');
            $listQuery='';
            if(!empty($departments)&&$departments!='H1'){  //20150525 柳林刚 加入
                $userid=getDepartmentUser($departments);
                $where=getAccessibleUsers('ServiceContracts','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                $listQuery= ' and vtiger_crmentity.smownerid in('.implode(',',$where).')';
            }else{
                $where=getAccessibleUsers();
                if($where!='1=1'){
                    $listQuery= ' and vtiger_crmentity.smownerid '.$where;
                }
            }
            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');

            global $dbconfig;
            $conn = mysql_connect($dbconfig['db_server'].$dbconfig['db_port'],$dbconfig['db_username'],$dbconfig['db_password']) or die ("数据连接错误!!!");//原生的支持存储过程
            mysql_query("set names 'utf8' ");
            mysql_query("set character_set_client=utf8");
            mysql_query("set character_set_results=utf8");
            mysql_select_db($dbconfig['db_name'],$conn);
            $query="SELECT vtiger_servicecontracts.contract_no,vtiger_servicecontracts.invoicecompany,vtiger_servicecontracts.isstandard,vtiger_account.accountname,LEFT(vtiger_servicecontracts.receivedate,10) AS receivedate,vtiger_servicecontracts.total,vtiger_servicecontracts.contract_type,vtiger_servicecontracts.confirmlasttime,if(vtiger_servicecontracts.isconfirm=1,'是','否') AS isconfirm,(SELECT last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_crmentity.smownerid) AS lastname,(SELECT vtiger_departments.departmentname FROM vtiger_departments LEFT JOIN vtiger_user2department ON vtiger_departments.departmentid = vtiger_user2department.departmentid WHERE vtiger_user2department.userid = vtiger_crmentity.smownerid) AS department,( SELECT ( IF ( status = 'Active', '在职', '离职' ) )  FROM vtiger_users WHERE vtiger_users.id = vtiger_crmentity.smownerid ) AS status,(select template_version from vtiger_servicecontracts_print where vtiger_servicecontracts_print.servicecontracts_no = vtiger_servicecontracts.contract_no order by vtiger_servicecontracts_print.servicecontractsprintid desc limit 1) AS tpl_version FROM vtiger_servicecontracts LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE	vtiger_servicecontracts.modulestatus = '已发放' AND vtiger_crmentity.deleted=0 {$listQuery} AND ((left(vtiger_servicecontracts.receivedate,10) BETWEEN '{$startdate}' AND '{$enddatatime}'))";
            $result=mysql_query($query);
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
            $phpexecl->setActiveSheetIndex(0)
                ->setCellValue('A1', '序号')
                ->setCellValue('B1', '所属合同编码')
                ->setCellValue('C1', '合同领取日期')
                ->setCellValue('D1', '合同类别')
                ->setCellValue('E1', '销售组')
                ->setCellValue('F1', '业务员')
                ->setCellValue('G1', '员工状态')
                ->setCellValue('H1', '是否审查')
                ->setCellValue('I1', '最后审查时间')
                ->setCellValue('J1', '客户名称')
                ->setCellValue('K1', '合同主体')
                ->setCellValue('L1', '版本号');

            //设置自动居中
            $phpexecl->getActiveSheet()->getStyle('A1:L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //$result=array(1,2,3,4,5,6,7,3,8,9,10);

            //设置边框
            $phpexecl->getActiveSheet()->getStyle('A1:Z1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $current=2;
            if(!empty($result)) {
                while ($row = mysql_fetch_array($result)) {
                    $phpexecl->setActiveSheetIndex(0)
                        ->setCellValue('A' . $current, $current-1)
                        ->setCellValue('B' . $current, $row['contract_no'])
                        ->setCellValue('C' . $current, $row['receivedate'])
                        ->setCellValue('D' . $current, $row['contract_type'])
                        ->setCellValue('E' . $current, $row['department'])
                        ->setCellValue('F' . $current, $row['lastname'])
                        ->setCellValue('G' . $current, $row['status'])
                        ->setCellValue('H' . $current, $row['isconfirm'])
                        ->setCellValue('I' . $current, $row['confirmlasttime']);
                    if($row['isstandard']){
                        //5.56非标合同要有客户名称
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('J' . $current, $row['accountname']);
                    }else{
                        $phpexecl->setActiveSheetIndex(0)->setCellValue('J' . $current, '');
                    }
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('K' . $current, $row['invoicecompany']);
                    //----加上合同对应合同模板的版本号
                    $phpexecl->setActiveSheetIndex(0)->setCellValue('L' . $current, $row['tpl_version']);

                    //加上边框
                    $phpexecl->getActiveSheet()->getStyle('A' . $current . ':L' . $current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    $current++;
                }
            }

            // 设置工作表的名移
            $phpexecl->getActiveSheet()->setTitle('合同已发送审查数据');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpexecl->setActiveSheetIndex(0);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="合同已发送审查数据.xlsx"');
            header('Cache-Control: max-age=0');

            header('Cache-Control: max-age=1');


            header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }elseif($strPublic=='ExportRIVS'){             //导出数据
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','ExportRIS')){   //权限验证
                return;
            }

            $departments=$request->get('department');
            $startdate=$request->get('datatime');
            $enddatatime=$request->get('enddatatime');
            $listQuery='';

            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery= ' and vtiger_servicecontracts.signid '.$where;
            }

            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');
            include 'crmcache/departmentanduserinfo.php';
            include 'crmcache/user2departmentname.php';
            global $dbconfig;
            $conn = mysql_connect($dbconfig['db_server'].$dbconfig['db_port'],$dbconfig['db_username'],$dbconfig['db_password']) or die ("数据连接错误!!!");//原生的支持存储过程
            mysql_query("set names 'utf8' ");
            mysql_query("set character_set_client=utf8");
            mysql_query("set character_set_results=utf8");
            mysql_select_db($dbconfig['db_name'],$conn);

            $query="SELECT vtiger_salesorder.*,vtiger_crmentity.smownerid,vtiger_products.productname AS prouctsname,vtiger_servicecontracts.contract_no,vtiger_salesorderproductsrel.costing,vtiger_user2department.departmentid FROM `vtiger_salesorderproductsrel` LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_salesorderproductsrel.salesorderid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid
                    LEFT JOIN vtiger_salesorderworkflowstages ON (vtiger_salesorderworkflowstages.salesorderid=vtiger_salesorderproductsrel.salesorderid AND vtiger_salesorderworkflowstages.productid=vtiger_salesorderproductsrel.productid)
                    LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_salesorderworkflowstages.auditorid
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_salesorder.servicecontractsid
                    LEFT JOIN vtiger_products ON vtiger_salesorderproductsrel.productid=vtiger_products.productid
                    WHERE  vtiger_crmentity.deleted=0
                    AND LEFT(vtiger_salesorder.workflowstime,10) BETWEEN '{$startdate}' AND '{$enddatatime}'{$listQuery}
                    AND vtiger_salesorder.modulestatus='c_complete'
                    AND vtiger_salesorder.servicecontractsid>0
                    GROUP BY vtiger_salesorderworkflowstages.salesorderid,vtiger_salesorderworkflowstages.productid";
            $result=mysql_query($query);
            $newdepart=array();
            $newdeparttemp=array();
            $i=0;
            while($row = mysql_fetch_array($result)){
                if($row['costing']>0){
                    $newdepart[$i]['department']=$user2departmentname[$row['smownerid']];
                    $newdepart[$i]['servicecontract']=$row['contract_no'];
                    $tempdepart=empty($row['departmentid'])?'H1':$row['departmentid'];
                    $newdepart[$i]['departmentid']=$tempdepart;
                    $newdepart[$i]['salesorder_no']=$row['salesorder_no'];
                    $newdepart[$i]['prouctsname']=$row['prouctsname'];
                    $newdepart[$i]['costing']=$row['costing'];
                    $newdeparttemp[$tempdepart]='';

                    $i++;
                }

            }
            $newdeparttemp=array_intersect_key($cachedepartment,$newdeparttemp);
            $newarray=array('department'=>'申请部门','servicecontract'=>'合同编号','salesorder_no'=>'工单编号','productname'=>'产品名称','manpower'=>'人力成本');
            $newarray=array_merge($newarray,$newdeparttemp);

            $newcachedepartment=array_keys($newarray);
            $newcachedepartment=array_fill_keys($newcachedepartment,'');
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
            $countarrnum=count($newarray);
            for($i='A',$j=0;($j<$countarrnum || (--$i<'A')) && ($i<='Z');++$i,++$j){
                $phpexecl->setActiveSheetIndex(0)->setCellValue($i.'1',current($newarray));
                //设置自动居中
                $phpexecl->getActiveSheet()->getStyle('A1:'.$i.'1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                //$result=array(1,2,3,4,5,6,7,3,8,9,10);

                //设置边框
                $phpexecl->getActiveSheet()->getStyle('A1:'.$i.'1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                next($newarray);
            }


            if(!empty($result)) {
                $current=2;
                foreach($newdepart as $value){
                    $newcachedepartment['department']=$value['department'];
                    $newcachedepartment['servicecontract']=$value['servicecontract'];
                    $newcachedepartment['productname']=$value['prouctsname'];
                    $newcachedepartment['salesorder_no']=$value['salesorder_no'];
                    $newcachedepartment['manpower']=$value['costing'];

                    $newcachedepartment[$value['departmentid']]=$value['costing'];
                    for($i='A',$j=0;($j<$countarrnum) && ($i<='Z');++$i,++$j){
                        $phpexecl->setActiveSheetIndex(0)->setCellValue($i.$current,current($newcachedepartment));
                        next($newcachedepartment);
                        $phpexecl->getActiveSheet()->getStyle('A' . $current . ':'.$i. $current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $phpexecl->getActiveSheet()->getStyle('A' . $current . ':'.$i. $current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    }
                    reset($newcachedepartment);

                    $newcachedepartment[$value['departmentid']]='';
                    $current++;
                }
            }

            // 设置工作表的名移
            $phpexecl->getActiveSheet()->setTitle('已完成工单人力成本数据');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpexecl->setActiveSheetIndex(0);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="已完成工单人力成本数据.xlsx"');
            header('Cache-Control: max-age=0');

            header('Cache-Control: max-age=1');


            header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
            $objWriter->save('php://output');
            exit;

        }elseif($strPublic == 'AuditSettings'){
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','AuditSettings')){   //权限验证
                parent::process($request);
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(''));
            //$tt = ServiceContracts_Record_Model::getAuditsettings();
            $viewer->assign('RECOEDS',ServiceContracts_Record_Model::getAuditsettings());
            $viewer->assign('DEPARTMENT',getDepartment());

            $viewer->assign('CLASSNAME',ServiceContracts_Record_Model::getSetPermissions());
            $viewer->view('auditSettings.tpl', $moduleName);
            exit;

        }elseif($strPublic == 'ExportRM') {               //导出有效回款
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','ExportRM')){   //权限验证
                parent::process($request);
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(" AND `status`='Active'"));
            $viewer->assign('RECOEDS',ServiceContracts_Record_Model::getReportPermissions());
            $viewer->assign('ModuleName',ServiceContracts_Record_Model::getModulePicklist());


            $viewer->assign('CLASSNAME',ServiceContracts_Record_Model::getSetPermissions());
            $viewer->view('exportrm.tpl', $moduleName);
            exit;
        }elseif($strPublic=='ExportRINVD'){             //导出有效回款数据
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','ExportRINV')){   //权限验证
                return;
            }
            $departments=$request->get('department');
            $startdate=$request->get('datatime');
            $enddatatime=$request->get('enddatatime');
            $listQuery='';
            if(!empty($departments)&&$departments!='H1'){  //20150525 柳林刚 加入
                $userid=getDepartmentUser($departments);
                $where=getAccessibleUsers('ServiceContracts','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                $listQuery= ' and vtiger_crmentity.smownerid in('.implode(',',$where).')';
            }else{
                $where=getAccessibleUsers();
                if($where!='1=1'){
                    $listQuery= ' and vtiger_crmentity.smownerid '.$where;
                }
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
                        IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid =(select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid,
                        vtiger_crmentity.smownerid as smownerid_owner,
                        vtiger_invoice.trialtime,
                        vtiger_invoice.invoicecompany,
                        vtiger_invoice.taxtype,
                        (vtiger_workflows.workflowsname) as workflowsid,
                        vtiger_invoice.workflowsid as workflowsid_reference,
                        vtiger_servicecontracts.contract_no,
                        vtiger_invoice.contractid,
                        vtiger_invoice.taxtotal,
                        vtiger_invoiceextend.invoicecodeextend,
                        vtiger_negativeinvoice.negativeinvoicecodeextend,
                        vtiger_invoiceextend.invoice_noextend,
                        vtiger_negativeinvoice.negativeinvoice_noextend,
                        vtiger_invoice.businessnamesone,
                        vtiger_negativeinvoice.negativebusinessnamesextend,
                        (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_invoiceextend.drawerextend=vtiger_users.id) as drawer,
                        (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_negativeinvoice.negativedrawerextend=vtiger_users.id) as negativedrawer,
                        vtiger_invoiceextend.billingtimeextend,
                        vtiger_negativeinvoice.negativebillingtimerextend,
                        vtiger_invoiceextend.commoditynameextend,
                        vtiger_negativeinvoice.negativecommoditynameextend,
                        vtiger_invoiceextend.amountofmoneyextend,
                        vtiger_negativeinvoice.negativeamountofmoneyextend,
                        vtiger_invoiceextend.taxrateextend,
                        vtiger_negativeinvoice.negativetaxrateextend,
                        vtiger_invoiceextend.taxextend,
                        vtiger_negativeinvoice.negativetaxextend,
                        vtiger_invoice.businesscontent,
                        vtiger_invoiceextend.totalandtaxextend,
                        vtiger_negativeinvoice.negativetotalandtaxextend,
                        vtiger_invoiceextend.remarkextend,
                        vtiger_negativeinvoice.negativeremarkextend,
                        vtiger_invoice.taxpayers_no,
                        vtiger_invoice.registeraddress,
                        vtiger_invoice.depositbank,
                        vtiger_invoice.telephone,
                        vtiger_invoice.accountnumber,
                        IF(isformtable=1,'是','否') as isformtable,
                        vtiger_invoice.file,vtiger_invoice.total,
                        (vtiger_account.accountname) as accountid,
                        vtiger_invoice.accountid as accountid_reference,
                        vtiger_invoice.invoicestatus,
                        vtiger_invoice.modulestatus,
                        vtiger_invoice.workflowstime,
                        vtiger_invoice.workflowsnode,
                        (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_invoice.receiveid=vtiger_users.id) as receiveid,
                        vtiger_invoice.receivedate,
			vtiger_servicecontracts.modulestatus as smodulestatus,
                        vtiger_invoiceextend.businessnamesextend,
                        vtiger_invoice.billingcontent,
                        vtiger_invoice.invoiceid
                    FROM vtiger_invoice
                    LEFT JOIN vtiger_crmentity
                        ON vtiger_invoice.invoiceid = vtiger_crmentity.crmid
                    LEFT JOIN vtiger_invoicebillads
                        ON vtiger_invoice.invoiceid = vtiger_invoicebillads.invoicebilladdressid
                    LEFT JOIN vtiger_invoiceshipads
                        ON vtiger_invoice.invoiceid = vtiger_invoiceshipads.invoiceshipaddressid
                    LEFT JOIN vtiger_invoicecf
                        ON vtiger_invoice.invoiceid = vtiger_invoicecf.invoiceid
                    LEFT JOIN vtiger_inventoryproductrel
                        ON vtiger_invoice.invoiceid = vtiger_inventoryproductrel.id
                    LEFT JOIN vtiger_workflows
                        ON vtiger_workflows.workflowsid=vtiger_invoice.workflowsid
                    LEFT JOIN vtiger_servicecontracts
                        ON vtiger_servicecontracts.servicecontractsid=vtiger_invoice.contractid
                    LEFT JOIN vtiger_invoiceextend
                        ON vtiger_invoiceextend.invoiceid=vtiger_invoice.invoiceid
                    LEFT JOIN vtiger_negativeinvoice
                        ON vtiger_invoiceextend.invoiceextendid=vtiger_negativeinvoice.invoiceextendid
                    LEFT JOIN vtiger_account
                        ON vtiger_account.accountid=vtiger_invoice.accountid
                    WHERE
                        vtiger_crmentity.deleted=0
                        and((vtiger_invoiceextend.billingtimeextend{$sql} AND vtiger_invoiceextend.billingtimeextend IS NOT NULL)) {$listQuery}";
            $result= $db->run_query_allrecords($query);

            include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';

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
                ->setCellValue('A1', '开票公司')
                ->setCellValue('B1', '申请人')
                ->setCellValue('C1', '申请日期')
                ->setCellValue('D1', '工作流')
                ->setCellValue('E1', '票据类型')
                ->setCellValue('F1', '实际开票抬头')
                ->setCellValue('G1', '开票人')
                ->setCellValue('H1', '开票日期')
                ->setCellValue('I1', '发票代码')
                ->setCellValue('J1', '(红冲)发票代码')
                ->setCellValue('K1', '发票号码')
                ->setCellValue('L1', '(红冲)发票号码')
                ->setCellValue('M1', '商品名称')
                ->setCellValue('N1', '金额')
                ->setCellValue('O1', '(红冲)金额')
                ->setCellValue('P1', '税率')
                ->setCellValue('Q1', '税额')
                ->setCellValue('R1', '(红冲)税额')
                ->setCellValue('S1', '价税合计')
                ->setCellValue('T1', '(红冲)价税合计')
                ->setCellValue('U1', '申请发票备注')
                ->setCellValue('V1', '操作人备注')
                ->setCellValue('W1', '纳税人部分别税号/税号')
                ->setCellValue('X1', '注册地址')
                ->setCellValue('Y1', '开户行')
                ->setCellValue('Z1', '电话')
                ->setCellValue('AA1', '账号')
                ->setCellValue('AB1', '已有加盖公单开票信息报表')
                ->setCellValue('AC1', '附件')
                ->setCellValue('AD1', '发票发放人')
                ->setCellValue('AE1', '服务合同')
                ->setCellValue('AF1', '合同状态')
                ->setCellValue('AG1', '合同方公司抬头');
            //设置自动居中
            $phpexecl->getActiveSheet()->getStyle('A1:AG1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //$result=array(1,2,3,4,5,6,7,3,8,9,10);

            //设置边框
            $phpexecl->getActiveSheet()->getStyle('A1:AG1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            if(!empty($result)){
                foreach($result as $key=>$value){
                    $current=$key+2;
                    //$purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                    $phpexecl->setActiveSheetIndex(0)
                        ->setCellValueExplicit('A'.$current, $value['invoicecompany'])
                        ->setCellValueExplicit('B'.$current, $value['smownerid'])
                        ->setCellValueExplicit('C'.$current, $value['trialtime'])
                        ->setCellValueExplicit('D'.$current, $value['workflowsid'])
                        ->setCellValueExplicit('E'.$current, vtranslate($value['taxtype'],'Invoice'))
                        ->setCellValueExplicit('F'.$current, $value['businessnamesone'])
                        ->setCellValueExplicit('G'.$current, $value['drawer'])
                        ->setCellValueExplicit('H'.$current, $value['billingtimeextend'])
                        ->setCellValueExplicit('I'.$current, $value['invoicecodeextend'])
                        ->setCellValueExplicit('J'.$current, $value['negativeinvoicecodeextend'])
                        ->setCellValueExplicit('K'.$current, $value['invoice_noextend'])
                        ->setCellValueExplicit('L'.$current, $value['negativeinvoice_noextend'])
                        ->setCellValueExplicit('M'.$current, $value['commoditynameextend'])
                        ->setCellValueExplicit('N'.$current, $value['amountofmoneyextend'])
                        ->setCellValueExplicit('O'.$current, $value['negativeamountofmoneyextend'])
                        ->setCellValueExplicit('P'.$current, $value['taxrateextend'])
                        ->setCellValueExplicit('Q'.$current, $value['taxextend'])
                        ->setCellValueExplicit('R'.$current, $value['negativetaxextend'])
                        ->setCellValueExplicit('S'.$current, $value['totalandtaxextend'])
                        ->setCellValueExplicit('T'.$current, $value['negativetotalandtaxextend'])
                        ->setCellValueExplicit('U'.$current, $value['businesscontent'])
                        ->setCellValueExplicit('V'.$current, $value['remarkextend'])
                        ->setCellValueExplicit('W'.$current, $value['taxpayers_no'])
                        ->setCellValueExplicit('X'.$current, $value['registeraddress'])
                        ->setCellValueExplicit('Y'.$current, $value['depositbank'])
                        ->setCellValueExplicit('Z'.$current, $value['telephone'])
                        ->setCellValueExplicit('AA'.$current, $value['accountnumber'])
                        ->setCellValueExplicit('AB'.$current, $value['isformtable'])
                        ->setCellValueExplicit('AC'.$current, $value['file'])
                        ->setCellValueExplicit('AD'.$current, $value['receiveid'])
                        ->setCellValueExplicit('AE'.$current, $value['contract_no'])
                        ->setCellValueExplicit('AF'.$current, vtranslate($value['modulestatus'],'ServiceContracts'))
                        ->setCellValueExplicit('AG'.$current, $value['accountid']);


                    //加上边框
                    $phpexecl->getActiveSheet()->getStyle('A'.$current.':AG'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
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
        }elseif($strPublic == 'setproduct2code') {//非标合同部门负责审核设置
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if (!$moduleModel->exportGrouprt('ServiceContracts', 'ProductsCodeProductId')) {   //权限验证
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('PRODUCTCODE', ServiceContracts_Record_Model::productcode());
            $viewer->assign('RECOEDS', ServiceContracts_Record_Model::product2productcode());
            $tyunwebbuyservicerecordmodel = TyunWebBuyService_Record_Model::getCleanInstance('TyunWebBuyService');
            $products = $tyunwebbuyservicerecordmodel->allPackageAndProduct();
            $viewer->assign('PRODUCTS', $products['products']);
            $viewer->assign('TYPETEXT', array('buy'=>'新增','renew'=>'续费','degrade'=>'降级','upgrade'=>'升级'));
            $viewer->view('setproduct2code.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'setproduct2codenotyun') {//非标合同部门负责审核设置
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if (!$moduleModel->exportGrouprt('ServiceContracts', 'ProductsCodeProductId')) {   //权限验证
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('RECOEDS', ServiceContracts_Record_Model::product2productcodenotyun());
            $viewer->view('setproduct2codenotyun.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'ExportCancel') {               //作废合同导出
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT', getDepartment());
            $viewer->view('exportcancel.tpl', $moduleName);
            exit;
        }elseif($strPublic=='ExportCancelRI'){
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ServiceContracts','ExportCancel')){   //权限验证
                return;
            }
            $searchDepartment=$request->get('department');
            $startdate=$request->get('datatime');
            $enddatatime=$request->get('enddatatime');
            $timechecked=$request->get('timeselected');
            $listQuery='';
            if(!empty($searchDepartment)&&$searchDepartment!='H1'){
                $userid=getDepartmentUser($searchDepartment);
                $where=getAccessibleUsers('ServiceContracts','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                $listQuery= ' and vtiger_servicecontracts.signid in('.implode(',',$where).')';
            }else{
                $where=getAccessibleUsers();
                if($where!='1=1'){
                    $listQuery= ' and vtiger_servicecontracts.signid '.$where;
                }
            }
            if($timechecked==1){
                $checkedfield='vtiger_servicecontracts.signdate';
                $checkedlabel='签订日期';
            }else{
                $checkedfield='vtiger_servicecontracts.returndate';
                $checkedlabel='归还日期';
            }
            ob_clean();                              //清空缓存
            header('Content-type: text/html;charset=utf-8');

            global $dbconfig;
            $conn = mysql_connect($dbconfig['db_server'].$dbconfig['db_port'],$dbconfig['db_username'],$dbconfig['db_password']) or die ("数据连接错误!!!");//原生的支持存储过程
            mysql_query("set names 'utf8' ");
            mysql_query("set character_set_client=utf8");
            mysql_query("set character_set_results=utf8");
            mysql_select_db($dbconfig['db_name'],$conn);
            $query = "SELECT
       	vtiger_servicecontracts.contract_no,
	    vtiger_servicecontracts.invoicecompany,
	    vtiger_servicecontracts.modulestatus,
       left({$checkedfield},10) AS signdate,
	IFNULL(
	(
SELECT
	CONCAT(
	last_name,
	'[',
	IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ),
	']',
	( IF ( `status` = 'Active', '', '[离职]' ) ) 
	) AS last_name 
FROM
	vtiger_users 
WHERE
	vtiger_crmentity.smownerid = vtiger_users.id 
	),
	'--' 
	) AS smownerid,
	vtiger_crmentity.smownerid AS smownerid_owner,
    vtiger_servicecontracts.receivedate,
  	(
SELECT
	CONCAT(
	last_name,
	'[',
	IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ),
	']',
	( IF ( `status` = 'Active', '', '[离职]' ) ) 
	) AS last_name 
FROM
	vtiger_users 
WHERE
	vtiger_servicecontracts.cancelid = vtiger_users.id 
	) AS cancelid,
    	vtiger_servicecontracts.cancelvoid,
   	vtiger_servicecontracts.cancelremark,
	vtiger_servicecontracts.canceltime,
	(
SELECT
	CONCAT(
	last_name,
	'[',
	IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ),
	']',
	( IF ( `status` = 'Active', '', '[离职]' ) ) 
	) AS last_name 
FROM
	vtiger_users 
WHERE
	vtiger_servicecontracts.cancelfeeid = vtiger_users.id 
	) AS cancelfeeid,
       	vtiger_servicecontracts.accountsdue
FROM
	vtiger_servicecontracts
	LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid
	LEFT JOIN vtiger_activationcode ON vtiger_activationcode.contractid = vtiger_servicecontracts.servicecontractsid
	LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
	LEFT JOIN vtiger_quotes ON vtiger_quotes.quoteid = vtiger_servicecontracts.quotes_no 
WHERE
	1 = 1 
	AND vtiger_crmentity.deleted = 0 
    AND vtiger_servicecontracts.modulestatus='c_cancel' AND vtiger_crmentity.deleted=0 {$listQuery} AND left({$checkedfield},10) BETWEEN '{$startdate}' AND '{$enddatatime}'
ORDER BY
	vtiger_servicecontracts.servicecontractsid";

            $result=mysql_query($query);
            global $root_directory;
            require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
            include 'crmcache/departmentanduserinfo.php';

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
                ->setCellValue('C1', '合同主体')
                ->setCellValue('D1', $checkedlabel)
                ->setCellValue('E1', '合同状态')
                ->setCellValue('F1', '合同领取人')
                ->setCellValue('G1', '合同领取日期')
                ->setCellValue('H1', '作废申请人')
                ->setCellValue('I1', '作废申请原因')
                ->setCellValue('J1', '作废备注')
                ->setCellValue('K1', '作废申请时间')
                ->setCellValue('L1', '出纳作废审核')
                ->setCellValue('M1', '已收款金额');

            //设置自动居中
            $phpexecl->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //$result=array(1,2,3,4,5,6,7,3,8,9,10);

            //设置边框
            $phpexecl->getActiveSheet()->getStyle('A1:Z1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $current=2;
            if(!empty($result)) {
                while ($row = mysql_fetch_array($result)) {
                    $phpexecl->setActiveSheetIndex(0)
                        ->setCellValue('A' . $current, $current - 1)
                        ->setCellValue('B' . $current, $row['contract_no'])
                        ->setCellValue('C' . $current, $row['invoicecompany'])
                        ->setCellValue('D' . $current, $row['signdate'])
                        ->setCellValue('E' . $current, vtranslate($row['modulestatus'],'ServiceContracts'))
                        ->setCellValue('F' . $current, $row['smownerid'])
                        ->setCellValue('G' . $current, $row['receivedate'])
                        ->setCellValue('H' . $current, $row['cancelid'])
                        ->setCellValue('I' . $current, vtranslate($row['cancelvoid'],'ServiceContracts'))
                        ->setCellValue('J' . $current, $row['cancelremark'])
                        ->setCellValue('K' . $current, $row['canceltime'])
                        ->setCellValue('L' . $current, $row['cancelfeeid'])
                        ->setCellValue('M' . $current, $row['accountsdue']);
                    //加上边框
                    $phpexecl->getActiveSheet()->getStyle('A' . $current . ':M' . $current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    $current++;
                }
            }

            // 设置工作表的名移
            $phpexecl->getActiveSheet()->setTitle('作废合同数据');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpexecl->setActiveSheetIndex(0);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="作废合同数据.xlsx"');
            header('Cache-Control: max-age=0');

            header('Cache-Control: max-age=1');


            header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }elseif($strPublic=='NoComplete'){
            $viewer = $this->getViewer($request);
            $viewer->assign('public', 'NoComplete');
        }

        // 判断该用户是否有权限 修改服务合同的关闭状态
        global $current_user;
        $adb = PearDatabase::getInstance();
        $sql = "select * FROM vtiger_custompowers where custompowerstype='updateContractsStates' OR custompowerstype='updateContractsClose' LIMIT 2";
        $sel_result = $adb->pquery($sql, array());
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt > 0) {
            while($rawData = $adb->fetch_array($sel_result)) {
                $roles_arr = explode(',', $rawData['roles']);
                $user_arr = explode(',', $rawData['user']);
                if (in_array($current_user->current_user_roles, $roles_arr) || in_array($current_user->id, $user_arr)) {
                    $viewer = $this->getViewer($request);

                    if ($rawData['custompowerstype'] == 'updateContractsStates') {
                        $viewer->assign('ISUPDATECONTRACTSSTATES', '1');
                    } else if($rawData['custompowerstype'] == 'updateContractsClose') {
                        $viewer->assign('ISUPDATECONTRACTSCLOSE', '1');
                    }

                }
            }

        }

        parent::process($request);
    }


    /**
     * 结算月
     * @return array
     */
    public function settlementMonth(){
        $iMonth=date('Y-m',time());
        $iDay = date('d',time());
        if($iDay>15){		//大于15号导出当前月
            $iMonth=date('Y-m',strtotime('+1 month'));
        }
        $arrMonth=array(
            '2015-08'=>array('Received'=>array('2015-07-03 00:00:00','2015-08-02 23:59:59'),'System'=>array('2015-07-06 00:00:00','2015-08-05 23:59:59')),
            '2015-09'=>array('Received'=>array('2015-08-03 00:00:00','2015-09-02 23:59:59'),'System'=>array('2015-08-06 00:00:00','2015-09-05 23:59:59')),
            '2015-10'=>array('Received'=>array('2015-08-03 00:00:00','2015-09-02 23:59:59'),'System'=>array('2015-09-06 00:00:00','2015-10-10 23:59:59'))
        );
        if(isset($arrMonth[$iMonth])){
            return $arrMonth[$iMonth];
        }
        return array('Received'=>array('-','-'),'System'=>array('-','-'));
    }
}
