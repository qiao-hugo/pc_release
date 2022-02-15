<?php

class TyunWebBuyService_Module_Model extends Vtiger_Module_Model{

	 public function getSideBarLinks($linkParams) {
		$parentQuickLinks = array();
		if(!isset($_REQUEST['orderType'])){
               $Title='<div style="border-bottom: 1px solid #006FB6;">T云WEB订单管理</div>';
        }else{
               $Title='T云WEB订单管理';
        }
		$quickLink = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => $Title,
				'linkurl' => $this->getListViewUrl(),
				'linkicon' => '',
		);
		if($_REQUEST['orderType']=='filed'){
           $titles='<div style="border-bottom: 1px solid #006FB6;">T云WEB归档数据</div>';
        }else{
            $titles='T云WEB归档数据';
        }
         $quickLink1 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => $titles,
             'linkurl' =>'index.php?module=TyunWebBuyService&view=List&orderType=filed',
             'linkicon' => '',
         );
        /* $quickLink2 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => 'T云WEB对账',
             'linkurl' => $this->getListViewUrl(),
             'linkicon' => '',
         );*/
		$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
       /* $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);*/
        /*if($this->exportGrouprt('ContractActivaCode','ExportV')){
             $quickLink3 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => 'T云表格导出',
                 'linkurl' => $this->getListViewUrl() . '&public=ExportV',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);
         }*/
	if($this->exportGrouprt('TyunWebBuyService','setagent')){
             $quickLink3 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '代理商设置',
                 'linkurl' => $this->getListViewUrl() . '&public=setagent',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);
         }


		return $parentQuickLinks;
	}
    public function exportGrouprt($module,$classname){
        global $current_user;
        $id=$current_user->id;
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
     * 处理充值审请明细导出
     * @param Vtiger_Request $request
     */
    public function CAExportDataExcel(Vtiger_Request $request){
        $data['name']='T云表格管理';
        $data['data']=$this->exportData($request);
        $this->exportDataExcel($data);
    }
    /**
     * 导出的Execl方法
     * @param $data
     */
    public function exportDataExcel($data){
        set_time_limit(0);
        ini_set('memory_limit','2048M');

        global $root_directory,$current_user,$site_URL;
        $path=$root_directory.'temp/';
        $filename=$path.'contractactivacode'.$current_user->id.'.xlsx';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        ob_clean();
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
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
        //$phpexecl->setActiveSheetIndex(0)->mergeCells('A1:AF1');
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '合同编号')
            ->setCellValue('B1', '所属员工')
            ->setCellValue('C1', '对应客服')
            ->setCellValue('D1', '客户名称')
            ->setCellValue('E1', '年限')
            ->setCellValue('F1', '激活码')
            ->setCellValue('G1', '激活时间')
            ->setCellValue('H1', '网站上线时间')
            ->setCellValue('I1', '付款情况')
            ->setCellValue('J1', '发票情况')
            ->setCellValue('K1', '合同金额')
            ->setCellValue('L1', '回款金额')
            ->setCellValue('M1', '开票金额')
            ->setCellValue('N1', '版本')
            ->setCellValue('O1', '是否激活')
            ->setCellValue('P1', '购买类型')
            ->setCellValue('Q1', '签订时间')
            ->setCellValue('R1', '未开票金额')
            ->setCellValue('S1', '未回款金额')
            ->setCellValue('T1', '客户激活名称')
            ->setCellValue('U1', '首款')
            ->setCellValue('V1', '首款时间')
            ->setCellValue('W1', '每月确认收入')
            ->setCellValue('X1', '累计确认收入')
            ->setCellValue('Y1', '客户用户名')
            ->setCellValue('Z1', '本月确认收入')
            ->setCellValue('AA1', '是否到期')
            ->setCellValue('AB1', '合同状态')
            ->setCellValue('AC1', '开始时间')
            ->setCellValue('AD1', '作废时间')
            ->setCellValue('AE1', '合同主体')
            ->setCellValue('AF1', '合同应收账款')
            ->setCellValue('AG1', '购买数量')
            ->setCellValue('AH1', '客户等级')
        ;

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:AH1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:AH1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $current=2;

        foreach($data['data'] as $value){
            $paymenttotal=$value['servicecontractstotal']-$value['paymenttotal'];//未收款金额
            $paymenttotal=$paymenttotal>0?$paymenttotal:0;
            $contractstatus = $value['contractstatus'];//合同状态
            $current_date=date("Y-m-d");
            if($contractstatus == 't_c_cancel'){
                $docanceltime = $value['docanceltime'];
                $current_date=empty($docanceltime)?date('Y-m-d'):$docanceltime;
            }

            $invoicetotal=$value['servicecontractstotal']-$value['invoicetotal'];//未开票金额
            $invoicetotal=$invoicetotal>0?$invoicetotal:0;
            $activedate=empty($value['activedate'])?date('Y-m-d'):$value['activedate'];
            $currentDiffMonth=$this->getMonthNum(substr($current_date,0,7),substr($activedate,0,7));
            $currentDiffMonth=$currentDiffMonth['y']*12+$currentDiffMonth['m'];//收入确认月份=当前月份-开始月份
            $maxMonth=$value['productlife']*12;//合同有效的月份
            $diffMonth=empty($value['activedate'])?0:($currentDiffMonth==0?1:($currentDiffMonth>$maxMonth?$maxMonth:$currentDiffMonth));
            $monthlyIncome=bcdiv($value['servicecontractstotal'],$maxMonth,2);//每月确认收入
            //$monthlyIncome=number_format($monthlyIncome,2,'.','');
            //累计收入确认
            $cumulativeIncome=$diffMonth!=$maxMonth?$diffMonth*$monthlyIncome:$value['servicecontractstotal'];
            //$thisMonthlyIncome=(empty($value['activedate']) || $currentDiffMonth>$maxMonth)?0:$monthlyIncome;
            $isMaturity=$currentDiffMonth>$maxMonth?'是':'否';//是否到期
            if($contractstatus == 't_c_cancel') {
                $thisMonthlyIncome = '--';
                $isMaturity =  '是';
            }else{
                //$thisMonthlyIncome//本月确认收入
                $thisMonthlyIncome = (empty($rawData['startdate']) || $currentDiffMonth > $maxMonth) ? 0 : $monthlyIncome;
            }
            //$spaymenttotal回款金额
            $spaymenttotal=($value['paymenttotal']<=$value['servicecontractstotal'])?$value['paymenttotal']:$value['servicecontractstotal'];
            $accountsreceivable=$cumulativeIncome-$spaymenttotal;//合同应收账款
            $phpexecl->setActiveSheetIndex(0)
                ->setCellValue('A'.$current, $value['contract_no'])
                ->setCellValue('B'.$current, $value['signid'])
                ->setCellValue('C'.$current, $value['servicerid'])
                ->setCellValue('D'.$current, $value['accountname'])
                ->setCellValue('E'.$current, $value['productlife'])
                ->setCellValue('F'.$current, $value['activecode'])
                ->setCellValue('G'.$current, $value['activedate'])
                ->setCellValue('H'.$current, $value['onlinetime'])
                ->setCellValue('I'.$current, vtranslate($value['paymentsituation'],'ContractActivaCode'))
                ->setCellValue('J'.$current, vtranslate($value['invoicesituation'],'ContractActivaCode'))
                ->setCellValue('K'.$current, $value['servicecontractstotal'])
                ->setCellValue('L'.$current, $spaymenttotal)
                ->setCellValue('M'.$current, $value['invoicetotal'])
                ->setCellValue('N'.$current, $value['productid'])
                ->setCellValue('O'.$current, $value['tyunative'])
                ->setCellValue('P'.$current, $value['classtype'])
                ->setCellValue('Q'.$current, $value['signdate'])
                ->setCellValue('R'.$current, $invoicetotal)
                ->setCellValue('S'.$current, $paymenttotal)
                ->setCellValue('T'.$current, $value['companyname'])
                ->setCellValue('U'.$current,$value['downpayment'] )
                ->setCellValue('V'.$current,$value['downpaymenttime'] )
                ->setCellValue('W'.$current, $monthlyIncome)
                ->setCellValue('X'.$current, $cumulativeIncome)
                ->setCellValue('Y'.$current, $value['usercode'])
                ->setCellValue('Z'.$current, $thisMonthlyIncome)
                ->setCellValue('AA'.$current, $isMaturity)
                ->setCellValue('AB'.$current, vtranslate($value['contractstatusname'],'ContractActivaCode'))
                ->setCellValue('AC'.$current, $value['startdate'])
                ->setCellValue('AD'.$current, $value['docanceltime'])
                ->setCellValue('AE'.$current, $value['invoicecompany'])
                ->setCellValue('AF'.$current, $accountsreceivable)
                ->setCellValue('AG'.$current, $value['purchasequantity'])
                ->setCellValue('AH'.$current, vtranslate($value['accountrank'],'ContractActivaCode'))
            ;
            $phpexecl->getActiveSheet()->getStyle('A'.$current.':AH'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpexecl->getActiveSheet()->getStyle('A'.$current.':AH'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $phpexecl->getActiveSheet()->getStyle('A'.$current.':AH'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $current++;
        }
        // 设置工作表的名称
        $phpexecl->getActiveSheet()->setTitle($data['name']);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        /*header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$data['name'].date('Y-m-dHis').'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0*/

        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        //$objWriter->save('php://output');
        $path=$root_directory.'temp/';
        $filename=$path.'contractactivacode'.$current_user->id.'.xlsx';
        $objWriter->save($filename);
        header('location:'.$site_URL.'temp/contractactivacode'.$current_user->id.'.xlsx');
    }

    /**
     * 取得的数据
     * @param Vtiger_Request $request
     * @return array
     */
    public function exportData(Vtiger_Request $request){

        $listQuery="SELECT 
                        vtiger_contractactivacode.contract_no,
                        vtiger_contractactivacode.signdate,
                        (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_contractactivacode.signid=vtiger_users.id) as signid,
                        (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_contractactivacode.servicerid=vtiger_users.id) as servicerid,
                        vtiger_contractactivacode.accountname,
                        vtiger_contractactivacode.accountrank,
                        vtiger_contractactivacode.productlife,
                        IF(vtiger_contractactivacode.tyunative=1,'是','否') as tyunative,
                        vtiger_contractactivacode.receivetime,
                        vtiger_contractactivacode.activedate,
                        IF(vtiger_contractactivacode.contractstatus='t_c_cancel','--',vtiger_contractactivacode.startdate) AS startdate,
                        vtiger_contractactivacode.onlinetime,
                        vtiger_contractactivacode.paymentsituation,
                        vtiger_contractactivacode.invoicesituation,
                        vtiger_contractactivacode.receivetime,
                        vtiger_contractactivacode.servicecontractstotal,
                        vtiger_contractactivacode.paymenttotal,
                        vtiger_contractactivacode.invoicetotal,
                        vtiger_contractactivacode.activecode,
                        vtiger_contractactivacode.usercode,
                        vtiger_contractactivacode.companyname,
                        vtiger_contractactivacode.downpayment,
                        vtiger_contractactivacode.downpaymenttime,
                        vtiger_contractactivacode.invoicecompany,
                        vtiger_contractactivacode.purchasequantity,
                        (CASE classtype
                        WHEN 'buy' THEN '购买'
                        WHEN 'upgrade' THEN '升级'
                        WHEN 'degrade' THEN '降级'
                        WHEN 'renew' THEN '续费'
                        WHEN 'againbuy' THEN '另购'
                        ELSE classtype
                        END) AS classtype,
                        vtiger_contractactivacode.productid,
                        vtiger_contractactivacode.productid as productid_reference,
                        vtiger_contractactivacode.contractactivacodeid,
                        vtiger_contractactivacode.contractstatus,
                        vtiger_contractactivacode.docanceltime,
                        (CASE vtiger_contractactivacode.contractstatus
                        WHEN 't_c_complete' THEN '已签收'
                        WHEN 't_c_recovered' THEN '已回收'
                        WHEN 't_c_cancel' THEN '已作废'
                        ELSE vtiger_contractactivacode.contractstatus
                        END) AS contractstatusname
                    FROM vtiger_contractactivacode 
                    WHERE 1=1";
        $listQuery.=$this->searchField($request);
        $db=PearDatabase::getInstance();
        $result=$db->pquery($listQuery,array());
        $dataRow=array();
        while($row=$db->fetch_array($result)){$dataRow[]=$row;}
        return $dataRow;
    }

    /**
     * 生成搜索条件
     * @param Vtiger_Request $request
     * @return
     */
    private function searchField(Vtiger_Request $request){
        $startdate=$request->get('datatime');
        $enddate=$request->get('enddatatime');
        $dateField=$request->get('timeselected');
        $searchDepartment = $_REQUEST['department'];
        if(strtotime($startdate) > strtotime($enddate)){
            $tempdate=" BETWEEN '{$startdate}' AND '{$enddate}'";
        }elseif(strtotime($startdate) < strtotime($enddate)) {
            $tempdate=" BETWEEN '{$startdate}' AND '{$enddate}'";
        }else{
            $tempdate="='{$startdate}'";
        }
        if($startdate ==''){
            $tempdate="='".date('Y-m-d')."'";
        }
        $dateFieldSearch=$dateField==1?' AND vtiger_contractactivacode.signdate'.$tempdate:' AND left(vtiger_contractactivacode.activedate,10)'.$tempdate;
        $listQuery='';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('ContractActivaCode','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);

            }else{
                $where=$userid;
            }
            $listQuery=' and vtiger_contractactivacode.signid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery= ' and vtiger_contractactivacode.signid '.$where;
            }
        }
        return $dateFieldSearch.$listQuery;
    }

    /**
     * 求两个日期相差的月份
     * @param $date1
     * @param $date2
     * @param string $tags
     * @return number
     */
    public function getMonthNum($date1,$date2){
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $interval = $datetime1->diff($datetime2);
        $time['y']         = $interval->format('%Y');
        $time['m']         = $interval->format('%m');
        $time['d']         = $interval->format('%d');
        $time['h']         = $interval->format('%H');
        $time['i']         = $interval->format('%i');
        $time['s']         = $interval->format('%s');
        $time['a']         = $interval->format('%a');    // 两个时间相差总天数
        return $time;
    }
}
?>
