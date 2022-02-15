<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
class AccountPlatform_AccountExport_View extends Vtiger_Export_View {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('export');
    }

    function checkPermission(Vtiger_Request $request) {
        return true;
    }

    function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $public=$request->get('public');
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        if(!$moduleModel->exportGrouprt('AccountPlatform','AccountExport')){   //权限验证
            parent::process($request);
            return;
        }
        if($public=='export'){
            $this->export($request);
        }
        if($public=='import'){
            $this->import($request);
        }
        $viewer = $this->getViewer($request);
        $viewer->view('AccountExport.tpl', $moduleName);
    }

    public function export($request){
        $datatime=$request->get('datatime');
        $enddatatime=$request->get('enddatatime');
        $listQuery=" and vtiger_accountplatform.effectiveendaccount between '{$datatime}' and '{$enddatatime}' ";
        $where=getAccessibleUsers('AccountPlatform','AccountExport',true);
        if($where!='1=1'){
            $listQuery.= ' and vtiger_crmentity.smownerid '.$where;
        }

        ob_clean();                              //清空缓存
        header('Content-type: text/html;charset=utf-8');
        global $root_directory;
        $db=PearDatabase::getInstance();

        $query="SELECT
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
			vtiger_crmentity.modifiedby = vtiger_users.id 
		),
		'--' 
	) AS modifiedby,
	vtiger_crmentity.modifiedby AS modifiedby_reference,
	( vtiger_products.productname ) AS productid,
	vtiger_accountplatform.productid AS productid_reference,
IF
	( vtiger_accountplatform.isforbidden = 1, '是', '否' ) AS isforbidden,
	vtiger_accountplatform.customeroriginattr,
	vtiger_accountplatform.isprovideservice,
	( vtiger_suppliercontracts.contract_no ) AS suppliercontractsid,
	vtiger_accountplatform.suppliercontractsid AS suppliercontractsid_reference,
	vtiger_accountplatform.rebatetype,
	vtiger_accountplatform.accountrebatetype,
	( vtiger_workflows.workflowsname ) AS workflowsid,
	vtiger_accountplatform.workflowsid AS workflowsid_reference,
	vtiger_crmentity.createdtime,
	vtiger_crmentity.modifiedtime,
	vtiger_accountplatform.modulestatus,
	( SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id = vtiger_crmentity.smownerid LIMIT 1 ) AS email,
	vtiger_accountplatform.workflowstime,
	vtiger_accountplatform.workflowsnode,
	vtiger_accountplatform.effectivestartaccount,
	vtiger_accountplatform.effectiveendaccount,
	( vtiger_account.accountname ) AS accountid,
	vtiger_accountplatform.accountid AS accountid_reference,
	( vtiger_vendor.vendorname ) AS vendorid,
	vtiger_accountplatform.vendorid AS vendorid_reference,
	vtiger_accountplatform.supplierrebate,
	vtiger_accountplatform.accountrebate,
	vtiger_accountplatform.accountplatformid 
FROM
	vtiger_accountplatform
	LEFT JOIN vtiger_crmentity ON vtiger_accountplatform.accountplatformid = vtiger_crmentity.crmid
	LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_accountplatform.productid
	LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid = vtiger_accountplatform.suppliercontractsid
	LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid = vtiger_accountplatform.workflowsid
	LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_accountplatform.accountid
	LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_accountplatform.vendorid 
WHERE
	vtiger_crmentity.deleted = 0 
	{$listQuery}
ORDER BY
	accountplatformid DESC";
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
            ->setCategory("AccountPlatform");


        // 添加头信处
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '产品服务')
            ->setCellValue('B1', '客户来源属性')
            ->setCellValue('C1', '有无服务')
            ->setCellValue('D1', '采购合同')
            ->setCellValue('E1', '供应商返点类型')
            ->setCellValue('F1', '工作流')
            ->setCellValue('G1', '创建时间')
            ->setCellValue('H1', '流程状态')
            ->setCellValue('I1', '流程时间')
            ->setCellValue('J1', '流程节点')
            ->setCellValue('K1', '账户有效开始日期')
            ->setCellValue('L1', '账户有效截止日期')
            ->setCellValue('M1', '客户')
            ->setCellValue('N1', '供应商')
            ->setCellValue('O1', '供应商返点（%）')
            ->setCellValue('P1', '客户返点（%）');
        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:P1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:P1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if(!empty($result)){
            foreach($result as $key=>$value){
                $current=$key+2;
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$current, $value['productid'])
                    ->setCellValueExplicit('B'.$current, $value['customeroriginattr'])
                    ->setCellValueExplicit('C'.$current, $value['isprovideservice'])
                    ->setCellValueExplicit('D'.$current, $value['suppliercontractsid'])
                    ->setCellValueExplicit('E'.$current, $value['rebatetype'])
                    ->setCellValueExplicit('F'.$current, $value['workflowsid'])
                    ->setCellValueExplicit('G'.$current, $value['createdtime'])
                    ->setCellValueExplicit('H'.$current, $value['modulestatus'])
                    ->setCellValueExplicit('I'.$current, $value['workflowstime'])
                    ->setCellValueExplicit('J'.$current, $value['workflowsnode'])
                    ->setCellValueExplicit('K'.$current, $value['effectivestartaccount'])
                    ->setCellValueExplicit('L'.$current, $value['effectiveendaccount'])
                    ->setCellValueExplicit('M'.$current, $value['accountid'])
                    ->setCellValueExplicit('N'.$current, $value['vendorid'])
                    ->setCellValueExplicit('O'.$current, $value['supplierrebate'])
                    ->setCellValueExplicit('P'.$current, $value['accountrebate']);
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':P'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }



        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('账户导出');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="账户导出.xlsx"');
        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');


        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

}
