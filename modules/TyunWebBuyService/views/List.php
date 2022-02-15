<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class TyunWebBuyService_List_View extends Vtiger_KList_View {
    function process (Vtiger_Request $request)
    {
        $strPublic = $request->get('public');
        if ($strPublic=='exportdata'){
            $recordModel = Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
            if(!$recordModel->personalAuthority('TyunWebBuyService','exportdatakai')){
                exit;
            }
            global $site_URL,$current_user;
            header('location:'.$site_URL.'temp/tweb'.$current_user->id.'.csv');
            exit;
        }else if($strPublic == 'exportDataReconciliationResult') {
            $this->all_export_data($request);
            exit;
        }
	if ($strPublic=='setagent'){
            $recordModel = Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
            if(!$recordModel->personalAuthority('TyunWebBuyService','setagent')){
                exit;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',$recordModel->getUserInfo());
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->assign('RECOEDS',$recordModel->getDepartmentData());
            $viewer->view('setagent.tpl', 'TyunWebBuyService');
            return ;
        }
        /*if ($strPublic=='ExportRID'){
            $moduleName = $request->getModule();//导出
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ContractActivaCode','ExportV')){   //权限验证
                return;
            }
            $moduleModel->CAExportDataExcel($request);
            exit;
        }*/
        // cxh 2019-10-12  把从 listview 传递过来的对账结果的个数记录传递到页面显示
        global $current_user;
        $moduleName = $request->getModule();//导出
        $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);//module相关的数据
        $viewer = $this->getViewer($request);
        $viewer->assign('RECONCILIATIONRESULT',$_REQUEST['reconciliationResult']);
        //对账结果列表
        $viewer->assign('RECONCLILITION_LIST',$_REQUEST['orderType']);
        //对账结果列表
        $viewer->assign('RECONCLILIATIONDATA',$_REQUEST['reconciliationData']);
        parent::process($request);
    }
    public function preProcess(Vtiger_Request $request, $display=true) {
        global $current_user;
        $moduleName = $request->getModule();//导出
        $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);//module相关的数据
        $viewer = $this->getViewer($request);
        $viewer->assign('FLAGEXPORTDATA', $recordModel->personalAuthority($moduleName,'exportdatakai'));
        //归档权限
        $viewer->assign('IS_FILED', $recordModel->personalAuthority($moduleName,'filed',$current_user->id));
        //对账权限
        $viewer->assign('IS_RECONCLILITION', $recordModel->personalAuthority($moduleName,'reconciliation',$current_user->id));
        //对账结果列表
        $viewer->assign('RECONCLILITION_LIST',$_REQUEST['orderType']);
        parent::preProcess($request);
    }
    //导出对账有误数据内容
    public function all_export_data(Vtiger_Request $request) {
        global $root_directory;
        $db=PearDatabase::getInstance();
        set_time_limit(0);
        $recorId = $_REQUEST['record'];
        $sql = " SELECT * FROM  vtiger_reconciliation_record WHERE id= ? LIMIT 1 ";
        $result = $db->pquery($sql,array($recorId));
        $reconciliationData=$db->raw_query_result_rowdata($result,0);
        $reconciliationData=json_decode($reconciliationData['content'],true);
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $phpexecl=new PHPExcel();
        // Set document properties
        $phpexecl->getProperties()->setCreator("cxh")
            ->setLastModifiedBy("cxh")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("t cloud reconciliation");
        $phpexecl->getActiveSheet()->mergeCells( 'A1:AM1');
        //添加头信处
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '红色无背景色数据为：代理商管理后台相对erp对账错误的数据，灰色背景数据为：代理商管理后台缺失数据，黄色背景数据为：erp相对代理商管理后台缺失数据。')
            ->setCellValue('A2', '订单编号')
            ->setCellValue('B2', '订单状态')
            ->setCellValue('C2', '订单来源')
            ->setCellValue('D2', '客户类型')
            ->setCellValue('E2', '原订单编号')
            ->setCellValue('F2', '原版本')
            ->setCellValue('G2', '客户名称')
            ->setCellValue('H2', '所属员工')
            ->setCellValue('I2', '客服')
            ->setCellValue('J2', '合同类型')
            ->setCellValue('K2', '代理商标识')
            ->setCellValue('L2', '是否激活')
            ->setCellValue('M2', '客户等级')
            ->setCellValue('N2', '下单时间')
            ->setCellValue('O2', '合同编号')
            ->setCellValue('P2', '合同状态')
            ->setCellValue('Q2', '版本')
            ->setCellValue('R2', '年限')
            ->setCellValue('S2', '开始时间')
            ->setCellValue('T2', '到期时间')
            ->setCellValue('U2', '签订时间')
            ->setCellValue('V2', '订单金额')
            ->setCellValue('W2', '合同金额')
            ->setCellValue('X2', '升级转款')
            ->setCellValue('Y2', '付款情况')
            ->setCellValue('Z2', '首付')
            ->setCellValue('AA2', '首付款时间')
            ->setCellValue('AB2', '发票情况')
            ->setCellValue('AC2', '开票金额')
            ->setCellValue('AD2', '回款金额')
            ->setCellValue('AE2', '回款流水')
            ->setCellValue('AF2', '回款方式')
            ->setCellValue('AG2', '匹配时间')
            ->setCellValue('AH2', '回款比例')
            ->setCellValue('AI2', '当月回款(运营)')
            ->setCellValue('AJ2', '当月回款(会计)')
            ->setCellValue('AK2', '退款金额')
            ->setCellValue('AL2', '禁用时间')
            ->setCellValue('AM2', '是否禁用');
        /*//设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:N1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);*/
        /*//设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:N1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);*/
        if(!empty($reconciliationData)){
            $comparedFields=array('createdtimes'=>'N','ordercodes'=>'A','customernames'=>'G','classtypes'=>'J','onofflines'=>'C','productnames'=>'Q','startdates'=>'S','expiredates'=>'T','orderamounts'=>'V','contractprices'=>'W','contractnames'=>'O','productlifes'=>'R');
            //重新排序数组keys 从零开始递增 否则导出数据会有问题
            foreach ($reconciliationData as $key=>$value){
                if($value['agents']==10817 || 10642){
                    unset($reconciliationData[$key]);
                }
            }
            $reconciliationData = array_values($reconciliationData);
            foreach($reconciliationData as $key=>$value){
                if($value['customerid']){
                    $listQuery = "SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label, id AS id FROM vtiger_users WHERE id=?  LIMIT 1 ";
                    $listResult = $db->pquery($listQuery, array($value['customerid']));
                    $value['customerid'] = $db->query_result($listResult,0,'label');
                }
                $current=$key+3;
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A'.$current, $value['ordercode'])
                    ->setCellValue('B'.$current, vtranslate($value['orderstatus'],'TyunWebBuyService'))
                    ->setCellValue('C'.$current, vtranslate($value['onoffline'],'TyunWebBuyService'))
                    ->setCellValue('D'.$current, vtranslate($value['customerstype'],'TyunWebBuyService'))
                    ->setCellValue('E'.$current, vtranslate($value['orderordercode'],'TyunWebBuyService'))
                    ->setCellValue('F'.$current, vtranslate($value['oldproductname'],'TyunWebBuyService'))
                    ->setCellValue('G'.$current, $value['customername'])
                    ->setCellValue('H'.$current, $value['creator'])
                    ->setCellValue('I'.$current, $value['customerid'])
                    ->setCellValue('J'.$current, vtranslate($value['classtype'],'TyunWebBuyService'))
                    ->setCellValue('K'.$current, vtranslate($value['agents'],'TyunWebBuyService'))
                    ->setCellValue('L'.$current, $value['status'])
                    ->setCellValue('M'.$current, vtranslate($value['accountrank'],'TyunWebBuyService'))
                    ->setCellValue('N'.$current, $value['createdtime'])
                    ->setCellValue('O'.$current, ' '.$value['contractname'])
                    ->setCellValue('P'.$current, vtranslate($value['contractstatus'],'TyunWebBuyService'))
                    ->setCellValue('Q'.$current, $value['productname'])
                    ->setCellValue('R'.$current, $value['productlife'])
                    ->setCellValue('S'.$current, $value['startdate'])
                    ->setCellValue('T'.$current, $value['expiredate'])
                    ->setCellValue('U'.$current, $value['signdate'])
                    ->setCellValue('V'.$current, $value['orderamount'])
                    ->setCellValue('W'.$current, $value['contractprice'])
                    ->setCellValue('X'.$current, $value['upgradetransfer'])
                    ->setCellValue('Y'.$current, vtranslate($value['paymentsituation'],'TyunWebBuyService'))
                    ->setCellValue('Z'.$current, $value['downpayment'])
                    ->setCellValue('AA'.$current, $value['downpaymenttime'])
                    ->setCellValue('AB'.$current, vtranslate($value['invoicesituation'],'TyunWebBuyService'))
                    ->setCellValue('AC'.$current, $value['invoicetotal'])
                    ->setCellValue('AD'.$current, ' '.$value['paymenttotal'])
                    ->setCellValue('AE'.$current, $value['paymentno'])
                    ->setCellValue('AF'.$current,vtranslate($value['paymentmethod'],'TyunWebBuyService'))
                    ->setCellValue('AG'.$current, $value['matchtime'])
                    ->setCellValue('AH'.$current, $value['paymentratio'])
                    ->setCellValue('AI'.$current, $value['camountrepayment1'])
                    ->setCellValue('AJ'.$current, $value['camountrepayment'])
                    ->setCellValue('AK'.$current, $value['refundamount'])
                    ->setCellValue('AL'.$current, $value['canceldatetime'])
                    ->setCellValue('AM'.$current, $value['isdisabled']);
                // Tyun记录缺失数据
                if($value['errorType']==1){
                    $phpexecl->getActiveSheet()->getStyle( 'A'.$current.':AM'.$current)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $phpexecl->getActiveSheet()->getStyle( 'A'.$current.':AM'.$current)->getFill()->getStartColor()->setARGB('FF808080');
                // crm erp 相对Tyun 缺失数据
                }else if($value['errorType']==2){
                    $phpexecl->getActiveSheet()->getStyle( 'A'.$current.':AM'.$current)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $phpexecl->getActiveSheet()->getStyle( 'A'.$current.':AM'.$current)->getFill()->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);
                }
                foreach ($comparedFields as $key=>$val){
                    if($value[$key]){
                        $phpexecl->getActiveSheet()->getStyle($val.$current)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
                    }
                }
            }
        }
        //设置A1描述 样式
        $phpexecl->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
        $phpexecl->getActiveSheet()->getStyle( 'A1')->getFont()->setSize(20);
        $phpexecl->getActiveSheet()->getStyle( 'A1')->getFont()->setBold(true);
        // 设置列宽度
        $phpexecl->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpexecl->getActiveSheet()->getColumnDimension('E')->setWidth(40);
        $phpexecl->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $phpexecl->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $phpexecl->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $phpexecl->getActiveSheet()->getColumnDimension('J')->setWidth(35);
        $phpexecl->getActiveSheet()->getColumnDimension('M')->setWidth(30);
        $phpexecl->getActiveSheet()->getColumnDimension('N')->setWidth(25);
        $phpexecl->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
        $phpexecl->getActiveSheet()->getColumnDimension('S')->setWidth(25);
        $phpexecl->getActiveSheet()->getColumnDimension('T')->setWidth(25);
        $phpexecl->getActiveSheet()->getColumnDimension('U')->setWidth(25);
        $phpexecl->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('对账结果');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="对账结果.xlsx"');
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