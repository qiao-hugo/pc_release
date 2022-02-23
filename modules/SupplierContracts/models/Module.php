<?php

class SupplierContracts_Module_Model extends Vtiger_Module_Model{

	 public function getSideBarLinks($linkParams) {
		$parentQuickLinks = array();
		$quickLink = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '采购/费用合同列表',
				'linkurl' => $this->getListViewUrl(),
				'linkicon' => '',
		);
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

         if($this->exportGrouprt('SupplierContracts','dempartConfirm'))
         {
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '采购/费用合同审核设置',
                 'linkurl' => $this->getListViewUrl() . '&public=dempartConfirm',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         if($this->exportGrouprt('SupplierContracts','Received')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '采购/费用合同领取',
                 'linkurl' => $this->getListViewUrl() . '&public=Received',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         if($this->exportGrouprt('SupplierContracts','Received')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '采购/费用合同归还',
                 'linkurl' => $this->getListViewUrl() . '&public=Returned',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         if($this->exportGrouprt('SupplierContracts','Export')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '采购/费用合同导出',
                 'linkurl' => $this->getListViewUrl() . '&public=Export',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }

         if($this->exportGrouprt('SupplierContracts','BatchArchive')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '批量归档',
                 'linkurl' => $this->getListViewUrl() . '&public=BatchArchive',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }

         if($this->exportGrouprt('SupplierContracts','ArchiveCode')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '归档编号图表生成',
                 'linkurl' => $this->getListViewUrl() . '&public=ArchiveCode',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }


         /*if($this->exportGrouprt('SupplierContracts','Received')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '采购合同未签归还',
                 'linkurl' => $this->getListViewUrl() . '&public=NoSignReturned',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }*/
		return $parentQuickLinks;
	}
    public function exportGrouprt($module,$classname,$id=0){
        if($id==0)
        {
            global $current_user;
            $id = $current_user->id;
        }
        $db=PearDatabase::getInstance();
        $query="SELECT 1 FROM vtiger_exportmanage WHERE deleted=0 AND userid=? AND module=? AND classname=?";
        $result=$db->pquery($query,array($id,$module,$classname));
        $num=$db->num_rows($result);
        if($num){
            return true;
        }
        return false;
    }
    /**
     *
     *合同超领份数
     **/
    public function servicecontracts_reviced($receiveid){
        $db=PearDatabase::getInstance();
        $sql = "SELECT IFNULL(sum(1),0) AS totals FROM `vtiger_suppliercontracts` LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_crmentity.smownerid=? AND vtiger_suppliercontracts.modulestatus='c_receive' AND vtiger_crmentity.deleted=0 AND vtiger_suppliercontracts.sideagreement=0";
        $result = $db->pquery($sql,array($receiveid));
        $num=$db->query_result($result,0,'totals');
        if($num>=5){
            return $num;
        }else{
            return false;
        }
    }

    /**
     * 导出采购合同数据
     * @param Vtiger_Request $request
     */
    public function exportSuppData(Vtiger_Request $request){
        set_time_limit(0);
        $departments=$request->get('department');
        $startdate=$request->get('datatime');
        $enddatatime=$request->get('enddatatime');
        $timeselected=$request->get('timeselected');
        if(!empty($departments))
        {
            $array=getDepartmentUser($departments);
        }
        else
        {
            $array=getDepartmentUser('H1');
        }
        if($timeselected==1){
            $selectDate='vtiger_suppliercontracts.returndate';
        }
        else
        {
            $selectDate='vtiger_suppliercontracts.signdate';
        }
        $listQuery= ' and vtiger_crmentity.smownerid IN('.implode(',',$array).')';

        ob_clean();                              //清空缓存
        header('Content-type: text/html;charset=utf-8');
        if(strtotime($startdate)<strtotime($enddatatime)){
            $listQuery.=" and {$selectDate}  between '{$startdate}' and '{$enddatatime}'";
        }elseif(strtotime($startdate)==strtotime($enddatatime)){
            $listQuery.=" and {$selectDate}='{$enddatatime}'";
        }elseif(strtotime($startdate)>strtotime($enddatatime)){
            $listQuery.=" and {$selectDate} between '{$enddatatime}' and '{$startdate}'";
        }
        global $root_directory;
        $db=PearDatabase::getInstance();

        $query="SELECT 
                    vtiger_suppliercontracts.effectivetime,
                    vtiger_suppliercontracts.contract_no,
                    vtiger_suppliercontracts.suppliercontractsstatus,
                    vtiger_suppliercontracts.workflowsnode,
                    vtiger_suppliercontracts.modulestatus,
                    vtiger_suppliercontracts.invoicecompany,
                    vtiger_suppliercontracts.pagenumber, 
                    (vtiger_vendor.vendorname) as vendorid,
                    vtiger_suppliercontracts.vendorid as vendorid_reference,
                    IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid,
                    vtiger_crmentity.smownerid as smownerid_owner,
                    vtiger_suppliercontracts.receivedate,
                    (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_suppliercontracts.signid=vtiger_users.id) as signid,
                    (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_suppliercontracts.receiptorid=vtiger_users.id) as receiptorid,
                    (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_suppliercontracts.cancelid=vtiger_users.id) as cancelid,
                    vtiger_suppliercontracts.accountsdue,
                    vtiger_suppliercontracts.signdate,
                    vtiger_suppliercontracts.canceltime,
                    vtiger_suppliercontracts.receiptnumber, 
                    (vtiger_workflows.workflowsname) as workflowsid,
                    vtiger_suppliercontracts.workflowsid as workflowsid_reference,
                    vtiger_suppliercontracts.workflowstime,
                    IF(vtiger_suppliercontracts.iscomplete=1,'是','否') as iscomplete,
                    vtiger_suppliercontracts.returndate,
                    vtiger_suppliercontracts.currencytype,
                    vtiger_suppliercontracts.total,
                    vtiger_suppliercontracts.file,
                    vtiger_suppliercontracts.remark,
                    vtiger_suppliercontracts.cancelvoid,
                    (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_suppliercontracts.cancelfeeid=vtiger_users.id) as cancelfeeid,
                    vtiger_suppliercontracts.cancelremark,
                    vtiger_suppliercontracts.paymethed,
                    vtiger_suppliercontracts.suppliercontractsid 
                FROM vtiger_suppliercontracts 
                LEFT JOIN vtiger_crmentity ON vtiger_suppliercontracts.suppliercontractsid = vtiger_crmentity.crmid 
                LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid=vtiger_suppliercontracts.vendorid 
                LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_suppliercontracts.workflowsid 
                WHERE 
                    vtiger_crmentity.deleted=0
                    AND vtiger_suppliercontracts.modulestatus='c_complete'
                    {$listQuery}
                ORDER BY vtiger_suppliercontracts.suppliercontractsid";
        $result= $db->run_query_allrecords($query);

        $supplierstatus=array('GY'=>'业务供应商合同','GX'=>'行政供应商合同');
        include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';

        $phpexecl=new PHPExcel();
        $phpexecl->getProperties()->setCreator("liu ganglin")
            ->setLastModifiedBy("liu ganglin")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("servicecontracts");


        // 添加头信处
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '采购合同编号')
            ->setCellValue('B1', '供应商名称')
            ->setCellValue('C1', '合同类型')
            ->setCellValue('D1', '合同状态')
            ->setCellValue('E1', '合同主体')
            ->setCellValue('F1', '合同领取人')
            ->setCellValue('G1', '领用日期')
            ->setCellValue('H1', '签订人员')
            ->setCellValue('I1', '签订日期')
            ->setCellValue('J1', '归还日期')
            ->setCellValue('K1', '合同金额')
            ->setCellValue('L1', '代领人')
            ->setCellValue('M1', '有效时间');
        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:M1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if(!empty($result)){
            foreach($result as $key=>$value){
                $current=$key+2;
                //$purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$current, $value['contract_no'])
                    ->setCellValueExplicit('B'.$current, $value['vendorid'])
                    ->setCellValueExplicit('C'.$current, $supplierstatus[$value['suppliercontractsstatus']])
                    ->setCellValueExplicit('D'.$current, "已签收")
                    ->setCellValueExplicit('E'.$current, $value['invoicecompany'])
                    ->setCellValueExplicit('F'.$current, $value['smownerid'])
                    ->setCellValueExplicit('G'.$current, $value['receivedate'])
                    ->setCellValueExplicit('H'.$current, $value['signid'])
                    ->setCellValueExplicit('I'.$current, $value['signdate'])
                    ->setCellValueExplicit('J'.$current, $value['returndate'])
                    ->setCellValueExplicit('K'.$current, $value['total'])
                    ->setCellValueExplicit('L'.$current, $value['receiptorid'])
                    ->setCellValueExplicit('M'.$current, $value['effectivetime']);


                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':M'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }



        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('采购合同导出');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="采购合同导出.xlsx"');
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

    public function exportArchiveData(Vtiger_Request $request)
    {
        $company_code = $request->get('department');
        $start_time = $request->get('datatime');
        $end_time = $request->get('enddatatime');
        $end_time_str = strtotime($end_time) + 24*3600;

        ob_clean();                              //清空缓存
        header('Content-type: text/html;charset=utf-8');
        global $adb;

        $sql = "select count(*) as code_active,min(code) as min_code, max(code) as max_code,ym 
                    from vtiger_archive_log   where companycode = '".$company_code."' 
                    and create_time > '".strtotime($start_time)."' and create_time < '".$end_time_str."' group by ym";
        $res = $adb->run_query_allrecords($sql);

        $invoicecompany = $adb->run_query_allrecords("SELECT * FROM vtiger_invoicecompany");
        $invoicecompany = array_column($invoicecompany, 'invoicecompany','companycode');
        $rows = [];
        if(count($res)){
            $sql = "select code,ym from vtiger_archive_log where companycode = '".$company_code."'  and status = 0
                                and create_time > '".strtotime($start_time)."' and create_time < '".$end_time_str."'";
            $_codes = $adb->run_query_allrecords($sql);
            $time_code_diff = [];
            array_map(function ($val) use (&$time_code_diff){
                $time_code_diff[$val['ym']][] = str_pad($val['code'],4,"0",STR_PAD_LEFT);
            }, $_codes);

            array_map(function ($val) use (&$rows, $time_code_diff, $invoicecompany , $company_code){
                $month_start_time = strtotime($val['ym'] .'01');
                $star_time = date('Y年m月d日', $month_start_time);
                $end_time = date('Y年m月d日', strtotime("last day of this month",$month_start_time) );
                $ym_diff = count($time_code_diff[$val['ym']]) ? $time_code_diff[$val['ym']] : [];
                $rows[] = [
                    'company_name'=> $invoicecompany[$company_code],
                    'time_range'=>$star_time . '-' . $end_time,
                    'code_range'=>str_pad($val['min_code'],4,"0",STR_PAD_LEFT) . '-' . str_pad($val['max_code'],4,"0",STR_PAD_LEFT),
                    'code_diff'=> $ym_diff ? implode(',', $ym_diff) : '',
                    'code_active'=>$val['code_active'] - count($ym_diff) ,
                ];
            }, $res);
        }
        include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $phpexecl=new PHPExcel();

        $phpexecl->getProperties()->setCreator("CRM")
            ->setLastModifiedBy("CRM")
            ->setTitle("采购订单归档编号")
            ->setCategory(" 归档编号图表生成");


        // 添加头信处
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '合同主体')
            ->setCellValue('B1', '归档日期区间')
            ->setCellValue('C1', '档案编号')
            ->setCellValue('D1', '断档编号')
            ->setCellValue('E1', '有效档案数');

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:E1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if(!empty($rows)){
            foreach($rows as $key=>$value){
                $current=$key+2;
                //$purchasemount=$value['purchasemount']+$value['waici']+$value['qite']+$value['meijai']+ $value['xalong']+$value['costing'];
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$current, $value['company_name'])
                    ->setCellValueExplicit('B'.$current, $value['time_range'])
                    ->setCellValueExplicit('C'.$current, $value['code_range'])
                    ->setCellValueExplicit('D'.$current, $value['code_diff'])
                    ->setCellValueExplicit('E'.$current, $value['code_active']);

                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':E'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="采购合同归档编号导出.xlsx"');
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
?>
