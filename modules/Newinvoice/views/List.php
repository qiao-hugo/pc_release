<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class Newinvoice_List_View extends Vtiger_KList_View {
    /*
     * Function to initialize the required data in smarty to display the List View Contents
     * 初始化smarty列表显示的内容的数据
     */

    public function process(Vtiger_Request $request) {
        $filter = $request->get('filter');
        if ($filter == 'export') {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            //$viewer->assign('DEPARTMENT', getDepartment());
            $viewer->view('export.tpl', $moduleName);
            exit;
        } else if ($filter == 'export_data') {
            $this->export_data($request);
            exit;
        } else if($filter == 'search_invoice') {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(''));
            $viewer->view('search_invoice.tpl', $moduleName);
            exit;
        } else if($filter == 'search_invoice_data') {
            $this->search_invoice_data($request);
            exit;
        } else if($filter == 'search_newinvoiceextende') {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(''));
            $viewer->view('search_newinvoiceextende.tpl', $moduleName);
            exit;
        } else if($filter == 'search_newinvoiceextende_data') {
            $this->search_newinvoiceextende_data($request);
            exit;
        } else if($filter == 'search_billingNotMatch') {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            //$viewer->assign('DEPARTMENT', getDepartment());
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(""));
            $viewer->view('billingNotMatch.tpl', $moduleName);
            exit;
        } else if($filter == 'search_billingNotMatchData') {
            $this->search_billingNotMatchData($request);
            exit;
        } else if($filter == 'all_export') {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            //$viewer->assign('DEPARTMENT', getDepartment());
            $viewer->view('all_export.tpl', $moduleName);
            die;
        } else if($filter == 'all_export_data') {
            $this->all_export_data($request);
            exit;
        }else if($filter == 'need_invoice') {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            //$viewer->assign('DEPARTMENT', getDepartment());
            $viewer->view('need_invoice.tpl', $moduleName);
            die;
        } else if($filter == 'need_invoice_data') {
            $this->needInvoiceData($request);
            exit;
         }else if($filter == 'contract_change_invoice') {
            //合同变更发票导出页面 gaocl add 2018/05/29
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->view('contract_change_invoice.tpl', $moduleName);
            die;
        } else if($filter == 'contract_change_invoice_data') {
            //合同变更发票导出处理 gaocl add 2018/05/29
            $this->contractChangeInvoiceData($request);
            exit;
        } else if($filter == 'pre_invoice_audit') {
            //预开票审核设置
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('Newinvoice','pre_invoice_audit')){   //权限验证
                parent::process($request);
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(''));
            $viewer->assign('RECOEDS',Newinvoice_Record_Model::getPreInvoiceAuditSettings());
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('preInvoiceAuditSettings.tpl', $moduleName);
            exit;
        }else if($filter == 'pre_invoice_remind') {
            //预开票审核设置
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('Newinvoice','pre_invoice_remind')){   //权限验证
                parent::process($request);
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(''));
            $viewer->assign('RECOEDS',Newinvoice_Record_Model::getPreInvoiceRemindSettings());
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('preInvoiceRemindSettings.tpl', $moduleName);
            exit;
        }else if($filter == 'pre_invoice_delay') {
            //预开票审核设置
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('Newinvoice','pre_invoice_delay')){   //权限验证
                parent::process($request);
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(''));
            $viewer->assign('RECOEDS',Newinvoice_Record_Model::getPreInvoiceDelaySettings());
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('preInvoiceDelay.tpl', $moduleName);
            exit;
        }else if($filter == 'pre_invoice_delay_export') {
            //导出所有
            $this->pre_invoice_delay_export($request);
            exit;
        }

        //parent::process($request);
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        $this->viewName = $request->get('viewname');
        $viewer->assign('VIEWNAME', $this->viewName);

        if ($request->isAjax()) {
            $this->initializeListViewContents($request, $viewer);//竟然调用两次，这边其实是ajax调用的，哈哈！！
            $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        }

        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('ISSIGN', Users_Privileges_Model::isPermitted($moduleName, 'DuplicatesHandling'));
        $viewer->assign('MODULE_MODEL', $moduleModel);
        global $current_user ;
        $viewer->assign('IS_EXPORTABLE',Newinvoice_Module_Model::exportGrouprt($moduleName,'is_exportable',$current_user->id));
//        print_r($request->get('view'));exit;
        $viewer->view('ListViewContents.tpl', $moduleName);
    }

    public function all_export_data(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        /*if(!$moduleModel->exportGrouprt('ReceivedPayments','ExportRI')){   //权限验证
            return;
        }*/
        $departments=$request->get('department');
        $startdate=$request->get('datatime');
        $enddatatime=$request->get('enddatatime');

        $where=getAccessibleUsers();
        $listQuery='';
        if($where!='1=1'){
            $listQuery = ' and vtiger_crmentity.smownerid '.$where;
        }
        ob_end_clean();
        header('Content-type: text/html;charset=utf-8');
        if($startdate>$enddatatime){
            $sql=" and vtiger_newinvoiceextend.billingtimeextend>='{$enddatatime}' and vtiger_newinvoiceextend.billingtimeextend<='{$startdate}'";
            $negativesql=" and vtiger_newnegativeinvoice.negativebillingtimerextend>='{$enddatatime}' and vtiger_newnegativeinvoice.negativebillingtimerextend<='{$startdate}'";
        }elseif($startdate==$enddatatime){
            $sql=" and vtiger_newinvoiceextend.billingtimeextend='{$enddatatime}'";
            $negativesql=" and vtiger_newnegativeinvoice.negativebillingtimerextend='{$enddatatime}'";
        }elseif($startdate<$enddatatime){
            $sql=" and vtiger_newinvoiceextend.billingtimeextend<='{$enddatatime}' and vtiger_newinvoiceextend.billingtimeextend>='{$startdate}'";
            $negativesql=" and vtiger_newnegativeinvoice.negativebillingtimerextend<='{$enddatatime}' and vtiger_newnegativeinvoice.negativebillingtimerextend>='{$startdate}'";
        }
        global $root_directory;
        $db=PearDatabase::getInstance();

        /*$query="SELECT IFNULL( ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ), '--' ) AS smownerid, (SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_newinvoice.accountid) AS accountid, vtiger_newinvoice.trialtime, vtiger_newinvoice.businessnamesone, vtiger_newinvoice.invoicecompany, vtiger_newinvoice.taxtotal, (SELECT IF (SUM(vtiger_newinvoicerayment.invoicetotal)>0,SUM(vtiger_newinvoicerayment.invoicetotal),0)AS invoicetotal FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.invoiceid=vtiger_newinvoice.invoiceid ) AS invoicetotal FROM vtiger_newinvoice LEFT JOIN vtiger_crmentity ON vtiger_newinvoice.invoiceid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_newinvoice.invoicetype='c_billing' {$sql}{$listQuery}";*/
        /*$query = "SELECT  vtiger_newinvoiceextend.amountofmoneyextend,vtiger_newinvoiceextend.invoicecodeextend,vtiger_newinvoiceextend.billingtimeextend, vtiger_newinvoiceextend.invoice_noextend, CASE vtiger_newinvoiceextend.invoicestatus WHEN vtiger_newinvoiceextend.invoicestatus = 'normal' THEN '正常' WHEN vtiger_newinvoiceextend.invoicestatus = 'redinvoice' THEN '红冲' WHEN vtiger_newinvoiceextend.invoicestatus = 'tovoid' THEN '作废' END as 'invoicestatus', vtiger_newinvoiceextend.createdtime, IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_newinvoiceextend.smcreatorid = vtiger_users.id ), '--') AS 'smcreatorid' , vtiger_newinvoiceextend.commoditynameextend, vtiger_newinvoiceextend.totalandtaxextend, vtiger_newinvoiceextend.taxextend, vtiger_newinvoiceextend.amountofmoneyextend, vtiger_newinvoiceextend.businessnamesextend FROM vtiger_newinvoiceextend LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid = vtiger_newinvoiceextend.invoiceid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_newinvoice.invoiceid WHERE vtiger_newinvoiceextend.deleted=0 {$sql} {$listQuery}";*/
        $query = "SELECT vtiger_newinvoiceextend.amountofmoneyextend, vtiger_newinvoiceextend.invoicecodeextend, vtiger_newinvoiceextend.billingtimeextend, vtiger_newinvoiceextend.invoice_noextend, CASE WHEN vtiger_newinvoiceextend.invoicestatus = 'normal' THEN '正常' WHEN vtiger_newinvoiceextend.invoicestatus = 'redinvoice' THEN '红冲' WHEN vtiger_newinvoiceextend.invoicestatus = 'tovoid' THEN '作废' END AS 'invoicestatus' , vtiger_newinvoiceextend.createdtime, IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_newinvoiceextend.smcreatorid = vtiger_users.id ), '--') AS 'smcreatorid', vtiger_newinvoiceextend.commoditynameextend, vtiger_newinvoiceextend.totalandtaxextend, vtiger_newinvoiceextend.taxextend , vtiger_newinvoiceextend.amountofmoneyextend, vtiger_newinvoiceextend.businessnamesextend, IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ), '--') AS 'smownerid', vtiger_newinvoice.invoicecompany, if(vtiger_newinvoice.taxtype = 'specialinvoice', '增值税专用发票', '增值税普通发票') AS 'taxtype' , ( SELECT vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = vtiger_newinvoice.contractid ) AS 'contractid', vtiger_newinvoice.businessnamesone,
       (SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid = vtiger_newinvoice.accountid ) AS account_display FROM vtiger_newinvoiceextend LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid = vtiger_newinvoiceextend.invoiceid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_newinvoice.invoiceid WHERE vtiger_newinvoiceextend.deleted=0 {$sql} {$listQuery}";

       $query .= ' UNION ALL ';
       //红冲的发票信息
       $query .= " SELECT (0 - vtiger_newnegativeinvoice.negativeamountofmoneyextend) AS amountofmoneyextend,vtiger_newnegativeinvoice.negativeinvoicecodeextend AS invoicecodeextend,vtiger_newnegativeinvoice.negativebillingtimerextend AS billingtimeextend,vtiger_newnegativeinvoice.negativeinvoice_noextend AS invoice_noextend,CASE WHEN vtiger_newinvoiceextend.invoicestatus = 'normal' THEN '正常' WHEN vtiger_newinvoiceextend.invoicestatus = 'redinvoice' THEN '红冲' WHEN vtiger_newinvoiceextend.invoicestatus = 'tovoid' THEN '作废' END AS invoicestatus,vtiger_newinvoiceextend.createdtime,IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1)),''),']',IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_newnegativeinvoice.negativedrawerextend = vtiger_users.id),'--') AS 'smcreatorid',vtiger_newnegativeinvoice.negativecommoditynameextend AS commoditynameextend,(0 - vtiger_newnegativeinvoice.negativetotalandtaxextend) AS totalandtaxextend,(0 - vtiger_newnegativeinvoice.negativetaxextend) AS taxextend,(0 - vtiger_newnegativeinvoice.negativeamountofmoneyextend) AS amountofmoneyextend,vtiger_newnegativeinvoice.negativebusinessnamesextend AS businessnamesextend,IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1)),''),']',IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id),'--') AS smownerid,vtiger_newinvoice.invoicecompany,IF(vtiger_newinvoice.taxtype = 'specialinvoice','增值税专用发票','增值税普通发票') AS taxtype,(SELECT vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = vtiger_newinvoice.contractid) AS contractid,vtiger_newinvoice.businessnamesone,(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid = vtiger_newinvoice.accountid) AS account_display FROM vtiger_newnegativeinvoice JOIN vtiger_newinvoiceextend ON vtiger_newnegativeinvoice.invoiceextendid = vtiger_newinvoiceextend.invoiceextendid LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid = vtiger_newinvoiceextend.invoiceid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_newinvoice.invoiceid WHERE vtiger_newinvoiceextend.deleted = 0 AND vtiger_newnegativeinvoice.`deleted` = 0 {$negativesql} {$listQuery}";
        //echo $query;die;
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
            ->setCellValue('A1', '发票代码')
            ->setCellValue('B1', '发票号码')
            ->setCellValue('C1', '发票状态')
            ->setCellValue('D1', '开票日期')
            ->setCellValue('E1', '开票人')

            ->setCellValue('F1', '商品名称')
            ->setCellValue('G1', '金额')

            ->setCellValue('H1', '税价合计')
            ->setCellValue('I1', '税额')

            ->setCellValue('J1', '负责人')
            ->setCellValue('K1', '开票公司')
            ->setCellValue('L1', '票据类型')
            ->setCellValue('M1', '服务合同')
            ->setCellValue('N1', '实际开票抬头')
            ->setCellValue('O1', '合同方客户抬头');

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:N1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$phpexecl->getActiveSheet()->getStyle('R1:X1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);
        //require 'crmcache/departmentanduserinfo.php';
        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:N1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if(!empty($result)){
            foreach($result as $key=>$value){
                $current=$key+2;
                //$purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                //$value['departmentid']=$cachedepartment[$value['departmentid']];
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A'.$current, $value['invoicecodeextend'])
                    ->setCellValue('B'.$current, ' '.$value['invoice_noextend'])
                    ->setCellValue('C'.$current, $value['invoicestatus'])

                    ->setCellValue('D'.$current, $value['billingtimeextend'])

                    ->setCellValue('E'.$current, $value['smcreatorid'])

                    ->setCellValue('F'.$current, $value['commoditynameextend'])
                    ->setCellValue('G'.$current, $value['amountofmoneyextend'])

                    ->setCellValue('H'.$current, $value['totalandtaxextend'])

                    ->setCellValue('I'.$current, $value['taxextend'])

                    ->setCellValue('J'.$current, $value['smownerid'])
                    ->setCellValue('K'.$current, $value['invoicecompany'])
                    ->setCellValue('L'.$current, $value['taxtype'])
                    ->setCellValue('M'.$current, $value['contractid'])
                    ->setCellValue('N'.$current, $value['businessnamesone'])
                    ->setCellValue('O'.$current, $value['account_display'])
                    ;
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':N'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('发票');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="发票数据.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
        exit;
    }

    public function pre_invoice_delay_export(Vtiger_Request $request){
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        if(!$moduleModel->exportGrouprt('Newinvoice','pre_invoice_delay_export')){   //权限验证
            parent::process($request);
            return;
        }
        ob_end_clean();
        header('Content-type: text/html;charset=utf-8');
        global $root_directory;
        $db=PearDatabase::getInstance();
        $query = "SELECT
  ( SELECT vtiger_newinvoice.lockstatus FROM vtiger_newinvoice WHERE vtiger_newinvoice.invoiceid = vtiger_preinvoicedeferral.invoiceid ) AS lockstatus,
	invoiceno,
	( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_preinvoicedeferral.userid ) AS username1,
	( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_preinvoicedeferral.applicantid ) AS username2,
	( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_preinvoicedeferral.reviewerid ) AS username3,
	applicantreason,
	applicantdate,
	applicantdays,
	reviewerdate,
	workflowsnode
FROM
	vtiger_preinvoicedeferral
	";
        $result= $db->run_query_allrecords($query);
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $phpexecl=new PHPExcel();
        $phpexecl->getProperties()->setCreator("stark tian")
            ->setLastModifiedBy("stark tian")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("pre_invoice_delay_export.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("newInvoice");


        // 添加头信处
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '发票代码')
            ->setCellValue('B1', '开票人账号锁定详情')

            ->setCellValue('C1', '发票申请人')

            ->setCellValue('D1', '延迟申请人')
            ->setCellValue('E1', '延迟申请日期')
            ->setCellValue('F1', '延迟最后日期')
            ->setCellValue('G1', '延迟申请理由')

            ->setCellValue('H1', '批复人')
            ->setCellValue('I1', '批复日期')

            ->setCellValue('J1', '流程状态');

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$phpexecl->getActiveSheet()->getStyle('R1:X1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);
        //require 'crmcache/departmentanduserinfo.php';
        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:I1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if(!empty($result)){
            foreach($result as $key=>$value){
                $current=$key+2;
                //$purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                //$value['departmentid']=$cachedepartment[$value['departmentid']];

                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A'.$current, $value['invoiceno'])
                    ->setCellValue('B'.$current, $value['lockstatus']==1?'锁定':'未加锁')

                    ->setCellValue('C'.$current, $value['username1'])

                    ->setCellValue('D'.$current, $value['username2'])
                    ->setCellValue('E'.$current, $value['applicantdate'])
                    ->setCellValue('F'.$current, $value['applicantdays'])
                    ->setCellValue('G'.$current, $value['applicantreason'])

                    ->setCellValue('H'.$current, $value['username3'])
                    ->setCellValue('I'.$current, $value['reviewerdate'])

                    ->setCellValue('J'.$current, $value['workflowsnode'])
                ;
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':I'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('预开票延迟信息导出');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="预开票延迟信息导出数据.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
        exit;
    }

    public function search_billingNotMatchData(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        /*if(!$moduleModel->exportGrouprt('ReceivedPayments','ExportRI')){   //权限验证
            return;
        }*/
        $departments=$request->get('department');
        $startdate=$request->get('datatime');
        $enddatatime=$request->get('enddatatime');
        $userid=$request->get('userid');

        $listQuery='';
        if($userid>0){
            $listQuery= ' and vtiger_crmentity.smownerid='.$userid;
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery= ' and vtiger_crmentity.smownerid '.$where;
            }
        }
        ob_end_clean();
        header('Content-type: text/html;charset=utf-8');
        if($startdate>$enddatatime){
            $sql=" and vtiger_newinvoice.trialtime>='{$enddatatime}' and vtiger_newinvoice.trialtime<='{$startdate}'";
        }elseif($startdate==$enddatatime){
            $sql=" and vtiger_newinvoice.trialtime='{$enddatatime}'";
        }elseif($startdate<$enddatatime){
            $sql=" and vtiger_newinvoice.trialtime<='{$enddatatime}' and vtiger_newinvoice.trialtime>='{$startdate}'";
        }
        global $root_directory;
        $db=PearDatabase::getInstance();

        /*$query="SELECT IFNULL( ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ), '--' ) AS smownerid, (SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_newinvoice.accountid) AS accountid, vtiger_newinvoice.trialtime, vtiger_newinvoice.businessnamesone, vtiger_newinvoice.invoicecompany, vtiger_newinvoice.taxtotal, (SELECT IF (SUM(vtiger_newinvoicerayment.invoicetotal)>0,SUM(vtiger_newinvoicerayment.invoicetotal),0)AS invoicetotal FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.invoiceid=vtiger_newinvoice.invoiceid ) AS invoicetotal FROM vtiger_newinvoice LEFT JOIN vtiger_crmentity ON vtiger_newinvoice.invoiceid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_newinvoice.invoicetype='c_billing' {$sql}{$listQuery}";*/
        $query = "SELECT vtiger_newinvoice.invoiceno, b.label as contract_no,IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ), '--') AS smownerid, vtiger_newinvoice.trialtime, vtiger_newinvoice.invoicecompany, ( SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid = vtiger_newinvoice.accountid ) AS accountid, vtiger_newinvoice.businessnamesone , IF(vtiger_newinvoice.taxtype = 'specialinvoice', '增值税专用发票', '增值税普通发票') AS 'taxtype', IF(vtiger_newinvoice.invoicetype = 'c_billing', '预开票', '正常') AS invoicetype, CASE vtiger_newinvoice.modulestatus WHEN 'b_check' THEN '审核中' WHEN 'a_normal' THEN '正常' WHEN 'a_exception' THEN '打回中' WHEN 'c_complete' THEN '完成' WHEN 'c_cancel' THEN '作废' WHEN 'c_returnTicket' THEN '退票' END AS modulestatus, vtiger_newinvoice.taxtotal, ( SELECT SUM(vtiger_newinvoiceextend.totalandtaxextend) FROM vtiger_newinvoiceextend WHERE vtiger_newinvoiceextend.invoiceid = vtiger_newinvoice.invoiceid AND vtiger_newinvoiceextend.deleted = 0 AND vtiger_newinvoiceextend.invoicestatus != 'tovoid' ) AS totalandtaxextend , ( SELECT SUM(vtiger_newnegativeinvoice.negativetotalandtaxextend) FROM vtiger_newnegativeinvoice WHERE vtiger_newnegativeinvoice.invoiceid = vtiger_newinvoice.invoiceid AND vtiger_newnegativeinvoice.deleted = 0 ) AS negativetotalandtaxextend, ( SELECT SUM(vtiger_newinvoicerayment.invoicetotal) FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.invoiceid = vtiger_newinvoice.invoiceid AND vtiger_newinvoicerayment.deleted = 0 ) AS invoicetotal, CASE vtiger_newinvoice.invoicestatus WHEN 'userinvoice' THEN '使用' WHEN 'redinvoice' THEN '红冲' WHEN 'returnticket' THEN '退票' WHEN 'tovoid' THEN '作废' END AS invoicestatus FROM vtiger_newinvoice LEFT JOIN vtiger_crmentity ON vtiger_newinvoice.invoiceid = vtiger_crmentity.crmid LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_newinvoice.contractid  left join vtiger_crmentity b on b.crmid=vtiger_newinvoice.contractid WHERE vtiger_crmentity.deleted = 0  AND vtiger_newinvoice.is_exportable='able_toexport' AND vtiger_newinvoice.invoicetype = 'c_billing' {$sql}{$listQuery} GROUP BY vtiger_newinvoice.invoiceid DESC";
//        echo $query;die;
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
            ->setCellValue('A1', '负责人')
            ->setCellValue('B1', '申请日期')
            ->setCellValue('C1', '开票公司')
            ->setCellValue('D1', '合同方客户抬头')
            ->setCellValue('E1', '实际开票抬头')
            ->setCellValue('F1', '票据类型')
            ->setCellValue('G1', '申请类型')
            ->setCellValue('H1', '流程状态')
            ->setCellValue('I1', '发票状态')

            ->setCellValue('J1', '申请开票总额')
            ->setCellValue('K1', '发票实际开票金额')
            ->setCellValue('L1', '已匹配金额')
            ->setCellValue('M1', '未匹配金额')
            ->setCellValue('N1', '合同编号')
            ->setCellValue('O1', '发票编号');



        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:N1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$phpexecl->getActiveSheet()->getStyle('R1:X1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);
        //require 'crmcache/departmentanduserinfo.php';
        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:N1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if(!empty($result)){
            $current = 2;
            foreach($result as $key=>$value){



                if(empty($value['totalandtaxextend'])) {
                    $value['totalandtaxextend'] = 0;
                }

                if(empty($value['taxtotal'])) {
                    $value['taxtotal'] = 0;
                }
                if(empty($value['actualtotal'])) {
                    $value['actualtotal'] = 0;
                }
                if(empty($value['invoicetotal'])) {
                    $value['invoicetotal'] = 0;
                }

                //$purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                //$value['departmentid']=$cachedepartment[$value['departmentid']];

                // 实际开票金额 = 开票金额-红冲金额
                if(empty($value['negativetotalandtaxextend'])) {
                    $value['negativetotalandtaxextend'] = 0;
                }
                $totalandtaxextend = $value['totalandtaxextend'] - $value['negativetotalandtaxextend'];

                // 未匹配金额 = 实际开票金额 - 已匹配金额
                $t_totalandtaxextend = $totalandtaxextend - $value['invoicetotal'];
                /*if($t_totalandtaxextend == 0) {
                    continue;
                }*/
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A'.$current, $value['smownerid'])
                    ->setCellValue('B'.$current, $value['trialtime'])
                    ->setCellValue('C'.$current, $value['invoicecompany'])
                    ->setCellValue('D'.$current, $value['accountid'])
                    ->setCellValue('E'.$current, $value['businessnamesone'])
                    ->setCellValue('F'.$current, $value['taxtype'])

                    ->setCellValue('G'.$current, $value['invoicetype'])
                    ->setCellValue('H'.$current, $value['modulestatus'])
                    ->setCellValue('I'.$current, $value['invoicestatus'])

                    ->setCellValue('J'.$current, $value['taxtotal'])

                    ->setCellValue('K'.$current, $totalandtaxextend)

                    ->setCellValue('L'.$current, $value['invoicetotal'])
                    ->setCellValue('M'.$current, $t_totalandtaxextend)
                    ->setCellValue('N'.$current, $value['contract_no'])
                    ->setCellValue('O'.$current, $value['invoiceno']);

                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':N'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $current ++;
            }
        }

        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('发票');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="预开票未匹配金额导出.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
        exit;
    }


    public function search_newinvoiceextende_data(Vtiger_Request $request) {
        $requestData = array();
        $requestData['invoicecodeextend'] =  $request->get('invoicecodeextend');
        $requestData['invoice_noextend'] =  $request->get('invoice_noextend');
        $requestData['businessnamesextend'] =  $request->get('businessnamesextend');
        $requestData['drawerextend'] =  $request->get('drawerextend');
        $requestData['billingtimerextend_start'] =  $request->get('billingtimerextend_start');
        $requestData['billingtimerextend_end'] =  $request->get('billingtimerextend_end');

        if(empty($requestData['billingtimerextend_start']) && empty($requestData['billingtimerextend_end'])) {
            $requestData['billingtimerextend_start'] = date('Y-m-d', strtotime('-1 month'));
            $requestData['billingtimerextend_end'] = date('Y-m-d');
        }

        $where = "";
        foreach ($requestData as $key=>$value) {
            if(!empty($value)) {
                if($key == 'billingtimerextend_start') {
                    $where .= " AND  unix_timestamp(vtiger_newinvoiceextend.billingtimeextend)>=unix_timestamp('{$value}') ";
                } else if($key == 'billingtimerextend_end') {
                    $where .= " AND  unix_timestamp(vtiger_newinvoiceextend.billingtimeextend)<=unix_timestamp('{$value}') ";
                } else {
                    $where .= " AND vtiger_newinvoiceextend.{$key}='{$value}' ";
                }
            }
        }

        $sql = "SELECT CASE vtiger_newinvoiceextend.invoicestatus WHEN 'tovoid' THEN '作废' WHEN 'redinvoice' THEN '红冲' ELSE '正常' END AS t_invoicestatus, IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_newinvoiceextend.drawerextend = vtiger_users.id ), '--') AS t_drawerextend, vtiger_newinvoiceextend.*, totalandtaxextend - IF(( SELECT SUM(negativetotalandtaxextend) FROM vtiger_newnegativeinvoice WHERE invoiceextendid = vtiger_newinvoiceextend.invoiceextendid ) IS NULL, 0, ( SELECT SUM(negativetotalandtaxextend) FROM vtiger_newnegativeinvoice WHERE invoiceextendid = vtiger_newinvoiceextend.invoiceextendid )) AS 't_totalandtaxextend' FROM vtiger_newinvoiceextend WHERE vtiger_newinvoiceextend.deleted = 0 {$where} GROUP BY vtiger_newinvoiceextend.invoiceextendid DESC LIMIT 300";

        $db=PearDatabase::getInstance();
        $result= $db->run_query_allrecords($sql);

        $viewer = $this->getViewer($request);
        $viewer->assign('RES_DATA', $result);
        $moduleName = $request->getModule();
        $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(''));
        $viewer->view('search_newinvoiceextende.tpl', $moduleName);
    }

    public function search_invoice_data(Vtiger_Request $request) {
        $contract_no = $request->get('contract_no');
        $signdate_start = $request->get('signdate_start');
        $signdate_end = $request->get('signdate_end');
        $signid = $request->get('signid');
        $receiveid = $request->get('receiveid');

        if(empty($signdate_start) && empty($signdate_end)) {
            $signdate_start = date('Y-m-d', strtotime('-1 month'));
            $signdate_end = date('Y-m-d');
        }

        $where = "";
        if(!empty($contract_no)) {
            $where .= " AND  vtiger_servicecontracts.contract_no='{$contract_no}' ";
        }
        if(!empty($signid)) {
            $where .= " AND  vtiger_servicecontracts.signid='{$signid}' ";
        }
        if(!empty($receiveid)) {
            $where .= " AND  vtiger_servicecontracts.receiveid='{$receiveid}' ";
        }
        if(!empty($signdate_start)) {
            $where .= " AND  unix_timestamp(vtiger_servicecontracts.signdate)>=unix_timestamp('{$signdate_start}') ";
        }
        if(!empty($signdate_end)) {
            $where .= " AND  unix_timestamp(vtiger_servicecontracts.signdate)<=unix_timestamp('{$signdate_end}') ";
        }

        $sql = "SELECT vtiger_servicecontracts.contract_no, vtiger_servicecontracts.signdate, IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_servicecontracts.signid = vtiger_users.id ), '--') AS signid, IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_servicecontracts.receiveid = vtiger_users.id ), '--') AS receiveid, vtiger_servicecontracts.total , vtiger_newinvoicerayment.arrivaldate, vtiger_newinvoicerayment.paytitle, vtiger_newinvoicerayment.total AS 'newinvoiceayment_totoal', vtiger_newinvoicerayment.invoicetotal, vtiger_newinvoicerayment.surpluinvoicetotal , vtiger_newinvoicerayment.invoiceid FROM vtiger_servicecontracts LEFT JOIN vtiger_newinvoicerayment ON vtiger_newinvoicerayment.servicecontractsid = vtiger_servicecontracts.servicecontractsid LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid WHERE vtiger_newinvoicerayment.deleted = 0 AND vtiger_crmentity.deleted = 0 {$where} GROUP BY vtiger_servicecontracts.signdate DESC LIMIT 300";
        $db=PearDatabase::getInstance();
        $result= $db->run_query_allrecords($sql);

        $viewer = $this->getViewer($request);
        $viewer->assign('RES_DATA', $result);
        $moduleName = $request->getModule();
        $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(''));
        $viewer->view('search_invoice.tpl', $moduleName);
    }

    public function export_data(Vtiger_Request $request) {
        set_time_limit(0);
        global $root_directory,$current_user,$site_URL;
        $path=$root_directory.'temp/';
        $filename=$path.'newinvoicedata'.$current_user->id.'.csv';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        /*if(!$moduleModel->exportGrouprt('ReceivedPayments','ExportRI')){   //权限验证
            return;
        }*/
        $invoice_select=$request->get('invoice_select');
        $departments=$request->get('department');
        $startdate=$request->get('datatime');
        $enddatatime=$request->get('enddatatime');

        $where=getAccessibleUsers();
        $listQuery='';
        if($where!='1=1'){
            $listQuery= ' and vtiger_crmentity.smownerid '.$where;
        }
        ob_end_clean();
        header('Content-type: text/html;charset=utf-8');
        if($startdate>$enddatatime){
            $sql=" and vtiger_newinvoice.trialtime>='{$enddatatime}' and vtiger_newinvoice.trialtime<='{$startdate}'";
        }elseif($startdate==$enddatatime){
            $sql=" and vtiger_newinvoice.trialtime='{$enddatatime}'";
        }elseif($startdate<$enddatatime){
            $sql=" and vtiger_newinvoice.trialtime<='{$enddatatime}' and vtiger_newinvoice.trialtime>='{$startdate}'";
        }

        if($invoice_select == 1){
            if($startdate>$enddatatime){
                $sql=" and vtiger_newinvoicerayment.associatedtime>='{$enddatatime} 00:00:01' and vtiger_newinvoicerayment.associatedtime<='{$startdate} 23:59:59' ";
            }elseif($startdate==$enddatatime){
                $sql=" and vtiger_newinvoicerayment.associatedtime='{$enddatatime}'";
            }elseif($startdate<$enddatatime){
                $sql=" and vtiger_newinvoicerayment.associatedtime<='{$enddatatime} 23:59:59' and vtiger_newinvoicerayment.associatedtime>='{$startdate} 00:00:01' ";
            }
        }
        if($invoice_select == 2){
            if($startdate>$enddatatime){
                $sql=" and vtiger_newinvoice.cleanassociatedtime>='{$enddatatime} 00:00:01'  and vtiger_newinvoice.cleanassociatedtime<='{$startdate} 23:59:59' ";
            }elseif($startdate==$enddatatime){
                $sql=" and vtiger_newinvoice.cleanassociatedtime='{$enddatatime}'";
            }elseif($startdate<$enddatatime){
                $sql=" and vtiger_newinvoice.cleanassociatedtime<='{$enddatatime} 23:59:59' and vtiger_newinvoice.cleanassociatedtime>='{$startdate} 00:00:01'";
            }
        }
        global $root_directory;
        $db=PearDatabase::getInstance();

        /*$query="SELECT IFNULL( ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ), '--' ) AS smownerid, (SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_newinvoice.accountid) AS accountid, vtiger_newinvoice.trialtime, vtiger_newinvoice.businessnamesone, vtiger_newinvoice.invoicecompany, vtiger_newinvoice.taxtotal, (SELECT IF (SUM(vtiger_newinvoicerayment.invoicetotal)>0,SUM(vtiger_newinvoicerayment.invoicetotal),0)AS invoicetotal FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.invoiceid=vtiger_newinvoice.invoiceid ) AS invoicetotal FROM vtiger_newinvoice LEFT JOIN vtiger_crmentity ON vtiger_newinvoice.invoiceid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_newinvoice.invoicetype='c_billing' {$sql}{$listQuery}";*/
//        $query = "SELECT IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ), '--') AS smownerid, ( SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid = vtiger_newinvoice.accountid ) AS accountid, vtiger_newinvoice.trialtime, vtiger_newinvoice.businessnamesone, vtiger_newinvoice.invoicecompany , IF(vtiger_newinvoice.invoicetype = 'c_normal', '正常', '预开票') AS 'invoicetype', IF(vtiger_newinvoice.taxtype = 'specialinvoice', '增值税专用发票', '增值税普通发票') AS 'taxtype', vtiger_newinvoice.taxtotal, ( SELECT IF(SUM(vtiger_newinvoicerayment.invoicetotal) > 0, SUM(vtiger_newinvoicerayment.invoicetotal), 0) AS invoicetotal FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.invoiceid = vtiger_newinvoice.invoiceid ) AS invoicetotal, vtiger_newinvoiceextend.invoicecodeextend , vtiger_newinvoiceextend.invoice_noextend, vtiger_newinvoiceextend.billingtimeextend, vtiger_newinvoiceextend.commoditynameextend, CASE vtiger_newinvoiceextend.invoicestatus WHEN 'redinvoice' THEN '红冲' WHEN 'tovoid' THEN '作废' WHEN 'normal' THEN '正常' ELSE vtiger_newinvoiceextend.invoicestatus END AS 'invoicestatus', vtiger_newinvoiceextend.amountofmoneyextend , vtiger_newinvoiceextend.taxrateextend, vtiger_newinvoiceextend.taxextend, vtiger_newinvoiceextend.totalandtaxextend, ( SELECT IF(SUM(vtiger_newnegativeinvoice.negativetotalandtaxextend) IS NULL, 0, SUM(vtiger_newnegativeinvoice.negativetotalandtaxextend)) FROM vtiger_newnegativeinvoice WHERE vtiger_newnegativeinvoice.invoiceextendid = vtiger_newinvoiceextend.invoiceextendid ) AS negativetotalandtaxextend, ( SELECT IF(SUM(vtiger_newinvoicetovoid.tovoidtotal) IS NULL, 0, SUM(vtiger_newinvoicetovoid.tovoidtotal)) FROM vtiger_newinvoicetovoid WHERE vtiger_newinvoicetovoid.invoiceextendid = vtiger_newinvoiceextend.invoiceextendid AND vtiger_newinvoicetovoid.type = 1 ) AS tovoidtotal FROM vtiger_newinvoice LEFT JOIN vtiger_crmentity ON vtiger_newinvoice.invoiceid = vtiger_crmentity.crmid LEFT JOIN vtiger_newinvoiceextend ON vtiger_newinvoice.invoiceid = vtiger_newinvoiceextend.invoiceid WHERE vtiger_crmentity.deleted = 0 AND vtiger_newinvoiceextend.deleted = 0 AND vtiger_newinvoice.invoicetype = 'c_billing' {$sql}{$listQuery} GROUP BY vtiger_newinvoice.invoiceid DESC";

        $query = "SELECT (SELECT contractcrm.label FROM vtiger_crmentity AS contractcrm WHERE contractcrm.crmid = vtiger_newinvoice.contractid LIMIT 1) AS contractid, IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ), '--') AS smownerid, ( SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid = vtiger_newinvoice.accountid ) AS accountid, vtiger_newinvoice.trialtime, vtiger_newinvoice.businessnamesone, vtiger_newinvoice.invoicecompany , IF(vtiger_newinvoice.invoicetype = 'c_normal', '正常', '预开票') AS 'invoicetype', IF(vtiger_newinvoice.taxtype = 'specialinvoice', '增值税专用发票', '增值税普通发票') AS 'taxtype', vtiger_newinvoice.taxtotal, ( SELECT IF(SUM(vtiger_newinvoicerayment.invoicetotal) > 0, SUM(vtiger_newinvoicerayment.invoicetotal), 0) AS invoicetotal FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.invoiceid = vtiger_newinvoice.invoiceid ) AS invoicetotal, vtiger_newinvoiceextend.invoicecodeextend , vtiger_newinvoiceextend.invoice_noextend, vtiger_newinvoiceextend.billingtimeextend, vtiger_newinvoiceextend.commoditynameextend, CASE vtiger_newinvoiceextend.invoicestatus WHEN 'redinvoice' THEN '红冲' WHEN 'tovoid' THEN '作废' WHEN 'normal' THEN '正常' ELSE vtiger_newinvoiceextend.invoicestatus END AS 'invoicestatus', vtiger_newinvoiceextend.amountofmoneyextend , vtiger_newinvoiceextend.taxrateextend, vtiger_newinvoiceextend.taxextend, vtiger_newinvoiceextend.totalandtaxextend, ( SELECT IF(SUM(vtiger_newnegativeinvoice.negativetotalandtaxextend) IS NULL, 0, SUM(vtiger_newnegativeinvoice.negativetotalandtaxextend)) FROM vtiger_newnegativeinvoice WHERE vtiger_newnegativeinvoice.invoiceextendid = vtiger_newinvoiceextend.invoiceextendid ) AS negativetotalandtaxextend, ( SELECT IF(SUM(vtiger_newinvoicetovoid.tovoidtotal) IS NULL, 0, SUM(vtiger_newinvoicetovoid.tovoidtotal)) FROM vtiger_newinvoicetovoid WHERE vtiger_newinvoicetovoid.invoiceextendid = vtiger_newinvoiceextend.invoiceextendid AND vtiger_newinvoicetovoid.type = 1 ) AS tovoidtotal ,vtiger_newinvoicerayment.paytitle,vtiger_newinvoicerayment.arrivaldate,vtiger_newinvoicerayment.total,vtiger_newinvoicerayment.invoicetotal as invoicetotal2,vtiger_newinvoicerayment.contract_no,vtiger_newinvoice.cleanassociatedtime,IF (vtiger_newinvoicerayment.invoicetotal != '',(vtiger_newinvoicerayment.invoicetotal) ,'') AS 'match_amount' , IF(vtiger_newinvoice.matchover = '1', '是', '否') AS 'matchover' ,vtiger_newinvoice.actualtotal,vtiger_newinvoicerayment.invoiceid ,vtiger_newinvoiceextend.invoiceextendid,(SELECT SUM(vtiger_newinvoicerayment.invoicetotal) FROM vtiger_newinvoicerayment WHERE  vtiger_newinvoicerayment.invoiceid = vtiger_newinvoice.invoiceid AND vtiger_newinvoicerayment.deleted = 0) as match_amount2 FROM vtiger_newinvoice LEFT JOIN vtiger_crmentity ON vtiger_newinvoice.invoiceid = vtiger_crmentity.crmid LEFT JOIN vtiger_newinvoiceextend ON vtiger_newinvoice.invoiceid = vtiger_newinvoiceextend.invoiceid  LEFT JOIN vtiger_newinvoicerayment ON (vtiger_newinvoicerayment.invoiceid = vtiger_newinvoice.invoiceid and vtiger_newinvoicerayment.deleted=0) WHERE vtiger_crmentity.deleted = 0 AND vtiger_newinvoiceextend.deleted = 0 AND vtiger_newinvoice.invoicetype = 'c_billing' {$sql}{$listQuery} ORDER BY vtiger_newinvoicerayment.invoiceid";
//        echo $query;die;
        $result= $db->run_query_allrecords($query);
        $fp=fopen($filename,'w');
        $array=array(iconv('utf-8','gb2312','负责人'),iconv('utf-8','gb2312','申请日期'),iconv('utf-8','gb2312','服务合同'),iconv('utf-8','gb2312','开票公司'),iconv('utf-8','gb2312','申请类型'),iconv('utf-8','gb2312','票据类型'),iconv('utf-8','gb2312','客户名称'),iconv('utf-8','gb2312','实际开票抬头'),iconv('utf-8','gb2312','实际开票金额'),iconv('utf-8','gb2312','申请开票总额'),iconv('utf-8','gb2312','发票代码'),iconv('utf-8','gb2312','发票号码'),iconv('utf-8','gb2312','开票日期'),iconv('utf-8','gb2312','商品名称'),iconv('utf-8','gb2312','发票状态'),iconv('utf-8','gb2312', '金额'),iconv('utf-8','gb2312','税率'),iconv('utf-8','gb2312', '税额'),iconv('utf-8','gb2312', '税价合计'),iconv('utf-8','gb2312','红冲金额'),iconv('utf-8','gb2312', '作废金额'),iconv('utf-8','gb2312','汇款抬头'),iconv('utf-8','gb2312','入账日期'),iconv('utf-8','gb2312','入账金额'),iconv('utf-8','gb2312','使用开票金额'),iconv('utf-8','gb2312','回款合同'),iconv('utf-8','gb2312','关联回款时间'),iconv('utf-8','gb2312','清除回款关联时间'),iconv('utf-8','gb2312','已匹配金额'),iconv('utf-8','gb2312', '回款全部匹配'));
        fputcsv($fp,$array);
        if(!empty($result)){
            foreach($result as $key=>$value) {
                $temp=array();
                $temp[]=iconv('utf-8','gb2312',$value['smownerid']);
                $temp[]=iconv('utf-8','gb2312',$value['trialtime']);
                $temp[]=iconv('utf-8','gb2312',$value['contractid']);
                $temp[]=iconv('utf-8','gb2312',$value['invoicecompany']);
                $temp[]=iconv('utf-8','gb2312',$value['invoicetype']);
                $temp[]=iconv('utf-8','gb2312',$value['taxtype']);
                $temp[]=iconv('utf-8','gb2312',$value['accountid']);
                $temp[]=iconv('utf-8','gb2312',$value['businessnamesone']);
                $temp[]=iconv('utf-8','gb2312',$value['actualtotal']);
                $temp[]=iconv('utf-8','gb2312',$value['taxtotal']);
                $temp[]=iconv('utf-8','gb2312',$value['invoicecodeextend']);
                $temp[]=iconv('utf-8','gb2312',$value['invoice_noextend']);
                $temp[]=iconv('utf-8','gb2312',$value['billingtimeextend']);
                $temp[]=iconv('utf-8','gb2312',$value['commoditynameextend']);
                $temp[]=iconv('utf-8','gb2312',$value['invoicestatus']);
                $temp[]=iconv('utf-8','gb2312',$value['amountofmoneyextend']);
                $temp[]=iconv('utf-8','gb2312',$value['taxrateextend']);
                $temp[]=iconv('utf-8','gb2312',$value['taxextend']);
                $temp[]=iconv('utf-8','gb2312',$value['totalandtaxextend']);
                $temp[]=iconv('utf-8','gb2312',$value['negativetotalandtaxextend']);
                $temp[]=iconv('utf-8','gb2312',$value['tovoidtotal']);
                $temp[]=iconv('utf-8','gb2312',$value['paytitle']);
                $temp[]=iconv('utf-8','gb2312',$value['arrivaldate']);
                $temp[]=iconv('utf-8','gb2312',$value['total']);
                $temp[]=iconv('utf-8','gb2312',$value['invoicetotal2']);
                $temp[]=iconv('utf-8','gb2312',$value['contract_no']);
                $temp[]=iconv('utf-8','gb2312',$value['associatedtime']);
                $temp[]=iconv('utf-8','gb2312',$value['cleanassociatedtime']);
                $temp[]=iconv('utf-8','gb2312',$value['match_amount2']);
                $temp[]=iconv('utf-8','gb2312',$value['matchover']);
                fputcsv($fp,$temp);
            }
        }
        fclose($fp);
        header('location:'.$site_URL.'temp/newinvoicedata'.$current_user->id.'.csv');
        exit;
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
            ->setCellValue('A1', '负责人')
            ->setCellValue('B1', '申请日期')
            ->setCellValue('C1', '开票公司')
            ->setCellValue('D1', '申请类型')
            ->setCellValue('E1', '票据类型')
            ->setCellValue('F1', '客户名称')
            ->setCellValue('G1', '实际开票抬头')
            ->setCellValue('H1', '实际开票金额')
            ->setCellValue('I1', '开票金额')
            ->setCellValue('J1', '发票代码')
            ->setCellValue('K1', '发票号码')
            ->setCellValue('L1', '开票日期')
            ->setCellValue('M1', '商品名称')
            ->setCellValue('N1', '发票状态')
            ->setCellValue('O1', '金额')
            ->setCellValue('P1', '税率')
            ->setCellValue('Q1', '税额')
            ->setCellValue('R1', '税价合计')
            ->setCellValue('S1', '红冲金额')
            ->setCellValue('T1', '作废金额')
                 ->setCellValue('U1', '汇款抬头')
                 ->setCellValue('V1', '入账日期')
                 ->setCellValue('W1', '入账金额')
                 ->setCellValue('X1', '使用开票金额')
                 ->setCellValue('Y1', '回款合同')
                 ->setCellValue('Z1', '关联回款时间')
                 ->setCellValue('AA1', '清除回款关联时间')
                 ->setCellValue('AB1', '已匹配金额')
                ->setCellValue('AC1', '回款全部匹配');
$phpexecl->getActiveSheet()->getColumnDimension('Y1')->setAutoSize(true);
$phpexecl->getActiveSheet()->getColumnDimension('Z1')->setAutoSize(true);
$phpexecl->getActiveSheet()->getColumnDimension('AA1')->setAutoSize(true);
$phpexecl->getActiveSheet()->getColumnDimension('AB1')->setAutoSize(true);
$phpexecl->getActiveSheet()->getColumnDimension('AC1')->setAutoSize(true);


        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:T1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$phpexecl->getActiveSheet()->getStyle('R1:X1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);
        //require 'crmcache/departmentanduserinfo.php';
        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:T1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if(!empty($result)){
            foreach($result as $key=>$value){
                $current=$key+2;
                //$purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                //$value['departmentid']=$cachedepartment[$value['departmentid']];
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A'.$current, $value['smownerid'])
                    ->setCellValue('B'.$current, $value['trialtime'])
                    ->setCellValue('C'.$current, $value['invoicecompany'])
                    ->setCellValue('D'.$current, $value['invoicetype'])
                    ->setCellValue('E'.$current, $value['taxtype'])
                    ->setCellValue('F'.$current, $value['accountid'])
                    ->setCellValue('G'.$current, $value['businessnamesone'])
                    ->setCellValue('H'.$current, $value['actualtotal'])
                    ->setCellValue('I'.$current, $value['taxtotal'])
                    ->setCellValue('J'.$current, $value['invoicecodeextend'])
                    ->setCellValue('K'.$current, $value['invoice_noextend'])
                    ->setCellValue('L'.$current, $value['billingtimeextend'])
                    ->setCellValue('M'.$current, $value['commoditynameextend'])
                    ->setCellValue('N'.$current, $value['invoicestatus'])
                    ->setCellValue('O'.$current, $value['amountofmoneyextend'])
                    ->setCellValue('P'.$current, $value['taxrateextend'])
                    ->setCellValue('Q'.$current, $value['taxextend'])
                    ->setCellValue('R'.$current, $value['totalandtaxextend'])
                    ->setCellValue('S'.$current, $value['negativetotalandtaxextend'])
                    ->setCellValue('T'.$current, $value['tovoidtotal'])
                         ->setCellValue('U'.$current, $value['paytitle'])
                         ->setCellValue('V'.$current, $value['arrivaldate'])
                         ->setCellValue('W'.$current, $value['total'])
                         ->setCellValue('X'.$current, $value['invoicetotal2'])
                         ->setCellValue('Y'.$current, $value['contract_no'])
                         ->setCellValue('Z'.$current, $value['associatedtime'])
                         ->setCellValue('AA'.$current, $value['cleanassociatedtime'])
                         ->setCellValue('AB'.$current, $value['match_amount2'].' ')
                         ->setCellValue('AC'.$current, $value['matchover']);
//                $match_amount=0;
//                 foreach($result as $k=>$v){
//                       if($value['invoiceid']==$v['invoiceid']){
//                              $match_amount= $v['match_amount']+$match_amount;
//                       }
//                 }
//                  $phpexecl->setActiveSheetIndex(0)->setCellValue('AB'.$current, $match_amount.' ');
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':T'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('发票');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        /*header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="发票数据.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0*/
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        ob_end_clean();
        //$objWriter->save('php://output');
        $objWriter->save($filename);
        header('location:'.$site_URL.'temp/newinvoicedata'.$current_user->id.'.xlsx');
        exit;
    }

    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
        $moduleName = $request->getModule();
        $cvId = $this->viewName;

        $pageNumber = $request->get('page');//页数
        $orderBy = $request->get('orderby');//排序
        $sortOrder = $request->get('sortorder');//排序
        $pageLimit = $request->get('limit');//排序
        if($sortOrder == "ASC"){
            $nextSortOrder = "DESC";
            $sortImage = "icon-chevron-down";
        }else{
            $nextSortOrder = "ASC";
            $sortImage = "icon-chevron-up";
        }

        if(empty ($pageNumber)){
            $pageNumber = '1';
        }
        //20150416 young 每页显示数量
        if(empty ($pageLimit)){
            $pageLimit = '20';
        }
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);//初始化各种数据,在这里其实初始化的是module_listview_model类，次类又同时将QueryGenerator,CustomView包含了

        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'), 'CVID'=>$cvId);

        $pagingModel = new Vtiger_Paging_Model();   //分页
        $pagingModel->set('page', $pageNumber);
        $pagingModel->set('limit', $pageLimit);//20150416 young 每页显示数量
        if(!empty($orderBy)) {
            $listViewModel->set('orderby', $orderBy);
            $listViewModel->set('sortorder',$sortOrder);
        }

        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator = $request->get('operator');
        if(!empty($operator)) {
            $listViewModel->set('operator', $operator);
            $viewer->assign('OPERATOR',$operator);
            $viewer->assign('ALPHABET_VALUE',$searchValue);
        }
        if(!empty($searchKey) && !empty($searchValue)) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        if(!$this->listViewHeaders){
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
        }
        if(!$this->listViewEntries){
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        }


        $noOfEntries = $listViewModel->getListViewCount();

        $viewer->assign('MODULE', $moduleName);

        if(!$this->listViewLinks){
            $this->listViewLinks = $listViewModel->getListViewLinks($linkParams);
        }
        $viewer->assign('LISTVIEW_LINKS', $this->listViewLinks);

        $viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);

        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('PAGE_NUMBER',$pageNumber);

        $viewer->assign('ORDER_BY',$orderBy);
        $viewer->assign('SORT_ORDER',$sortOrder);
        $viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
        $viewer->assign('SORT_IMAGE',$sortImage);
        $viewer->assign('COLUMN_NAME',$orderBy);

        $viewer->assign('LISTVIEW_ENTIRES_COUNT',$noOfEntries);
        //插入字段信息
        //20150428 young 将模板的字段验证转移到后台验证，便于控制
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $this->listViewHeaders;
        $temp = array();
        if(!empty($LISTVIEW_FIELDS)){
            foreach($LISTVIEW_FIELDS as $key=>$val){
                if(isset($listViewHeaders[$key])){
                    $temp[$key]=$listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)){
            $temp = $listViewHeaders;
        }

        $viewer->assign('LISTVIEW_HEADERS', $temp);
        //$viewer->assign('LISTVIEW_FIELDS', $listViewModel->getSelectFields());
        $viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
        //end



        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int) $noOfEntries / (int) $pageLimit);

        if($pageCount == 0){
            $pageCount = 1;
        }
        $viewer->assign('PAGE_COUNT', $pageCount);
        $viewer->assign('PAGE_CU', $pageNumber);
        $viewer->assign('LISTVIEW_COUNT', $noOfEntries);
        //发票退票作废 steel 2015-05-27
        $viewer->assign('IS_MODULE_TOVOID', $listViewModel->getModule()->isPermitted('ToVoid'));
        $viewer->assign('IS_MODULE_TICKET', $listViewModel->getModule()->isPermitted('ReturnTicket'));
        //新增和编辑按钮
        $viewer->assign('IS_MODULE_LISTBTNADD', '1');
        $viewer->assign('IS_MODULE_DUPLICATES', '1');
        $viewer->assign('IS_MODULE_LISTBTNEDIT', '1');
        //$viewer->assign('IS_MODULE_LISTBTNADD', $listViewModel->getModule()->isPermitted('ListBtnADD'));
        //$viewer->assign('IS_MODULE_DUPLICATES', $listViewModel->getModule()->isPermitted('DuplicatesHandling'));
        //$viewer->assign('IS_MODULE_LISTBTNEDIT', $listViewModel->getModule()->isPermitted('ListBtnEDIT'));
        //--end--//
        $viewer->assign('IS_MODULE_EDITABLE', $listViewModel->getModule()->isPermitted('EditView'));
        $viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));
        $viewer->assign('IS_MODULE_CANCEL', $listViewModel->getModule()->exportGrouprt('Newinvoice','invoicecancel'));


    }
    public function needInvoiceData(Vtiger_Request $request){
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        /*if(!$moduleModel->exportGrouprt('ReceivedPayments','ExportRI')){   //权限验证
            return;
        }*/
        $startdate=$request->get('datatime');
        $enddatatime=$request->get('enddatatime');

        $where=getAccessibleUsers();
        $listQuery='';
        /*if($where!='1=1'){
            $listQuery = ' and vtiger_crmentity.smownerid '.$where;
        }*/
        ob_end_clean();
        header('Content-type: text/html;charset=utf-8');
        if($startdate>$enddatatime){
            $sql=" and vtiger_newinvoiceextend.billingtimeextend>='{$enddatatime}' and vtiger_newinvoiceextend.billingtimeextend<='{$startdate}'";
        }elseif($startdate==$enddatatime){
            $sql=" and vtiger_newinvoiceextend.billingtimeextend='{$enddatatime}'";
        }elseif($startdate<$enddatatime){
            $sql=" and vtiger_newinvoiceextend.billingtimeextend<='{$enddatatime}' and vtiger_newinvoiceextend.billingtimeextend>='{$startdate}'";
        }
        global $root_directory;
        $db=PearDatabase::getInstance();

        /*$query="SELECT IFNULL( ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ), '--' ) AS smownerid, (SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_newinvoice.accountid) AS accountid, vtiger_newinvoice.trialtime, vtiger_newinvoice.businessnamesone, vtiger_newinvoice.invoicecompany, vtiger_newinvoice.taxtotal, (SELECT IF (SUM(vtiger_newinvoicerayment.invoicetotal)>0,SUM(vtiger_newinvoicerayment.invoicetotal),0)AS invoicetotal FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.invoiceid=vtiger_newinvoice.invoiceid ) AS invoicetotal FROM vtiger_newinvoice LEFT JOIN vtiger_crmentity ON vtiger_newinvoice.invoiceid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_newinvoice.invoicetype='c_billing' {$sql}{$listQuery}";*/
        /*$query = "SELECT  vtiger_newinvoiceextend.amountofmoneyextend,vtiger_newinvoiceextend.invoicecodeextend,vtiger_newinvoiceextend.billingtimeextend, vtiger_newinvoiceextend.invoice_noextend, CASE vtiger_newinvoiceextend.invoicestatus WHEN vtiger_newinvoiceextend.invoicestatus = 'normal' THEN '正常' WHEN vtiger_newinvoiceextend.invoicestatus = 'redinvoice' THEN '红冲' WHEN vtiger_newinvoiceextend.invoicestatus = 'tovoid' THEN '作废' END as 'invoicestatus', vtiger_newinvoiceextend.createdtime, IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_newinvoiceextend.smcreatorid = vtiger_users.id ), '--') AS 'smcreatorid' , vtiger_newinvoiceextend.commoditynameextend, vtiger_newinvoiceextend.totalandtaxextend, vtiger_newinvoiceextend.taxextend, vtiger_newinvoiceextend.amountofmoneyextend, vtiger_newinvoiceextend.businessnamesextend FROM vtiger_newinvoiceextend LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid = vtiger_newinvoiceextend.invoiceid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_newinvoice.invoiceid WHERE vtiger_newinvoiceextend.deleted=0 {$sql} {$listQuery}";*/
        $query = "SELECT vtiger_newinvoiceextend.amountofmoneyextend, vtiger_newinvoiceextend.invoicecodeextend, vtiger_newinvoiceextend.billingtimeextend, vtiger_newinvoiceextend.invoice_noextend, CASE WHEN vtiger_newinvoiceextend.invoicestatus = 'normal' THEN '正常' WHEN vtiger_newinvoiceextend.invoicestatus = 'redinvoice' THEN '红冲' WHEN vtiger_newinvoiceextend.invoicestatus = 'tovoid' THEN '作废' END AS 'invoicestatus' , vtiger_newinvoiceextend.createdtime, IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_newinvoiceextend.smcreatorid = vtiger_users.id ), '--') AS 'smcreatorid', vtiger_newinvoiceextend.commoditynameextend, vtiger_newinvoiceextend.totalandtaxextend, vtiger_newinvoiceextend.taxextend , vtiger_newinvoiceextend.amountofmoneyextend, vtiger_newinvoiceextend.businessnamesextend, IFNULL(( SELECT CONCAT(last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), ''), ']', IF(`status` = 'Active', '', '[离职]')) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ), '--') AS 'smownerid', vtiger_newinvoice.invoicecompany, if(vtiger_newinvoice.taxtype = 'specialinvoice', '增值税专用发票', '增值税普通发票') AS 'taxtype' , ( SELECT vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = vtiger_newinvoice.contractid ) AS 'contractid', vtiger_newinvoice.businessnamesone,if(vtiger_newinvoice.invoicetype='c_normal','正常(已匹配回款)','预开票(未匹配回款)') AS invoicetype,vtiger_newinvoice.trialtime,vtiger_newinvoice.invoiceno FROM vtiger_newinvoice LEFT JOIN vtiger_newinvoiceextend ON (vtiger_newinvoice.invoiceid=vtiger_newinvoiceextend.invoiceid && vtiger_newinvoiceextend.deleted=0) LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_newinvoice.invoiceid WHERE vtiger_crmentity.deleted=0 AND vtiger_newinvoice.isaccountinvoice='noneed' {$sql} {$listQuery}";
        //echo $query;die;
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
            ->setCellValue('A1', '发票代码')
            ->setCellValue('B1', '发票号码')
            ->setCellValue('C1', '发票状态')
            ->setCellValue('D1', '开票日期')
            ->setCellValue('E1', '开票人')

            ->setCellValue('F1', '商品名称')
            ->setCellValue('G1', '金额')

            ->setCellValue('H1', '税价合计')
            ->setCellValue('I1', '税额')

            ->setCellValue('J1', '负责人')
            ->setCellValue('K1', '开票公司')
            ->setCellValue('L1', '票据类型')
            ->setCellValue('M1', '服务合同')
            ->setCellValue('N1', '实际开票抬头')
            ->setCellValue('O1', '开票内容')
            ->setCellValue('P1', '发票抬头')
            ->setCellValue('Q1', '申请日期')
            ->setCellValue('R1', '发票编号');



        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:R1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$phpexecl->getActiveSheet()->getStyle('R1:X1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);
        //require 'crmcache/departmentanduserinfo.php';
        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:R1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if(!empty($result)){
            foreach($result as $key=>$value){
                $current=$key+2;
                //$purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                //$value['departmentid']=$cachedepartment[$value['departmentid']];
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A'.$current, $value['invoicecodeextend'])
                    ->setCellValue('B'.$current, ' '.$value['invoice_noextend'])
                    ->setCellValue('C'.$current, $value['invoicestatus'])

                    ->setCellValue('D'.$current, $value['billingtimeextend'])

                    ->setCellValue('E'.$current, $value['smcreatorid'])

                    ->setCellValue('F'.$current, $value['commoditynameextend'])
                    ->setCellValue('G'.$current, $value['amountofmoneyextend'])

                    ->setCellValue('H'.$current, $value['totalandtaxextend'])

                    ->setCellValue('I'.$current, $value['taxextend'])

                    ->setCellValue('J'.$current, $value['smownerid'])
                    ->setCellValue('K'.$current, $value['invoicecompany'])
                    ->setCellValue('L'.$current, $value['taxtype'])
                    ->setCellValue('M'.$current, $value['contractid'])
                    ->setCellValue('N'.$current, $value['businessnamesone'])
                    ->setCellValue('O'.$current, $value['invoicetype'])
                    ->setCellValue('P'.$current, $value['businessnamesextend'])
                    ->setCellValue('Q'.$current, $value['trialtime'])
                    ->setCellValue('R'.$current, $value['invoiceno'])
                ;
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':R'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('发票');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="不需要开票客户.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
        exit;
    }

    /**
     * 合同变更发票数据导出
     * @param Vtiger_Request $request
     * @throws AppException
     * @throws Exception
     */
    public function contractChangeInvoiceData(Vtiger_Request $request){
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        /*if(!$moduleModel->exportGrouprt('Newinvoice','ExportRI')){   //权限验证
            return;
        }*/
        $startdate=$request->get('datatime');
        $enddatatime=$request->get('enddatatime');

        //$where=getAccessibleUsers();
        $listQuery='';
        /*if($where!='1=1'){
            $listQuery = ' and vtiger_crmentity.smownerid '.$where;
        }*/

        ob_end_clean();//清除缓冲区,避免乱码

        header('Content-type: text/html;charset=utf-8');
        if($startdate>$enddatatime){
            $sql=" and DATE_FORMAT(vtiger_newinvoice_history.modifiedtime,'%Y-%m-%d')>='{$enddatatime}' and DATE_FORMAT(vtiger_newinvoice_history.modifiedtime,'%Y-%m-%d')<='{$startdate} 23:59:59'";
        }elseif($startdate==$enddatatime){
            $sql=" and DATE_FORMAT(vtiger_newinvoice_history.modifiedtime,'%Y-%m-%d')='{$enddatatime}'";
        }elseif($startdate<$enddatatime){
            $sql=" and DATE_FORMAT(vtiger_newinvoice_history.modifiedtime,'%Y-%m-%d')<='{$enddatatime} 23:59:59' and DATE_FORMAT(vtiger_newinvoice_history.modifiedtime,'%Y-%m-%d')>='{$startdate}'";
        }
        global $root_directory;
        $db=PearDatabase::getInstance();

        $query = "SELECT
                vtiger_newinvoice.invoiceno,
                vtiger_users.last_name AS smownername,
                vtiger_account.accountname,
                vtiger_newinvoice.businessnamesone,
                vtiger_newinvoice.invoicecompany,
                vtiger_newinvoice.taxtotal,
                vtiger_newinvoice.actualtotal,
                vtiger_newinvoice.trialtime,
            vtiger_newinvoiceextend.billingtimeextend,
            vtiger_newinvoiceextend.invoice_noextend,
            vtiger_newinvoiceextend.invoicecodeextend,
                vtiger_newinvoice_history.modifiedtime,
                vtiger_newinvoice_history.oldcontract_no,
                vtiger_newinvoice_history.newcontract_no,
                vtiger_newinvoice_history.remark
                FROM vtiger_newinvoice_history
              INNER JOIN vtiger_newinvoiceextend ON (vtiger_newinvoice_history.invoiceid=vtiger_newinvoiceextend.invoiceid)
                LEFT JOIN vtiger_newinvoice ON(vtiger_newinvoice.invoiceid=vtiger_newinvoice_history.invoiceid)
                LEFT JOIN vtiger_crmentity ON(vtiger_newinvoice.invoiceid=vtiger_crmentity.crmid)
                LEFT JOIN vtiger_users ON(vtiger_users.id=vtiger_crmentity.smownerid)
                LEFT JOIN vtiger_account ON(vtiger_account.accountid=vtiger_newinvoice.accountid)
                WHERE vtiger_crmentity.deleted=0  AND vtiger_newinvoiceextend.deleted=0 {$sql} {$listQuery}";

        $result= $db->run_query_allrecords($query);
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';

        $phpexecl=new PHPExcel();

        // Set document properties
        $phpexecl->getProperties()->setCreator("crm")
            ->setLastModifiedBy("crm")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("servicecontracts");

        $arr_execl_col = array("invoiceno"=>"发票编号",
            "smownername"=>"申请人",
            "accountname"=>"合同方公司抬头",
            "businessnamesone"=>"实际开票抬头",
            "invoicecompany"=>"开票公司",
            "taxtotal"=>"申请开票总额",
            "actualtotal"=>"实际开票金额",
            "trialtime"=>"申请日期",
            "billingtimeextend"=>"开票日期",
            "invoice_noextend"=>"发票号码",
            "invoicecodeextend"=>"发票代码",
            "modifiedtime"=>"变更合同日期",
            "oldcontract_no"=>"原发票合同编号",
            "newcontract_no"=>"变更后合同编号",
            "remark"=>"备注");

        $col_num = 0;
        //设置默认宽度
        $phpexecl->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
        //表头
        foreach ($arr_execl_col as $key => $value) {
            $phpexecl->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col_num,1,$value);
            //设置自动居中
            $phpexecl->getActiveSheet()->getStyleByColumnAndRow($col_num,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //设置边框
            $phpexecl->getActiveSheet()->getStyleByColumnAndRow($col_num,1)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $col_num ++;
        }
        $phpexecl->getActiveSheet()->getColumnDimensionByColumn(4)->setWidth(30);

        //数据
        if(!empty($result)){
            $row_num = 2;
            foreach($result as $key=>$dataRow){
                $col_num = 0;
                foreach ($arr_execl_col as $key => $value) {
                    $phpexecl->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col_num, $row_num, $dataRow[$key]);
                    //设置自动居中
                    $phpexecl->getActiveSheet()->getStyleByColumnAndRow($col_num,$row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    //设置边框
                    $phpexecl->getActiveSheet()->getStyleByColumnAndRow($col_num,$row_num)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $col_num++;
                }
                $row_num ++;
            }
        }

        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('合同变更发票');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="合同变更发票数据.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
        exit;
    }
}
?>
