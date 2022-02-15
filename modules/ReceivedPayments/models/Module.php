<?php 
/**
 * wangbin 2015-1-20 13:58:37 添加跟多回款的筛选项
 * */
class ReceivedPayments_Module_Model extends Vtiger_Module_Model {
	public function getSideBarLinks($linkParams) {
		$parentQuickLinks = parent::getSideBarLinks($linkParams);
		$quickLink1 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '7天内回款到期',
				'linkurl' => 'index.php?module=ReceivedPayments&view=List&filter=sevreceived',
				'linkicon' => '',
		);
		$quickLink2 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '15天内回款到期',
				'linkurl' => 'index.php?module=ReceivedPayments&view=List&filter=fifreceived',
				'linkicon' => '',
		);
		$quickLink3 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '回款未收',
				'linkurl' => 'index.php?module=ReceivedPayments&view=List&filter=noreceived',
				'linkicon' => '',
		);
		$quickLink4 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '回款已收',
				'linkurl' => 'index.php?module=ReceivedPayments&view=List&filter=isreceived',
				'linkicon' => '',
		);
		$quickLink5 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '已超期',
				'linkurl' => 'index.php?module=ReceivedPayments&view=List&filter=overreceived',
				'linkicon' => '',
		);

        $quickLink5 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '无合同回款列表',
            'linkurl' => 'index.php?module=ReceivedPayments&view=List&filter=noservicecontracts',
            'linkicon' => '',
        );
        global $current_user;
        $userId=$current_user->id;
		$quickLink6 = array(
		    'linktype' => 'SIDEBARLINK',
		    'linklabel' => '业绩分成明细',
		    'linkurl' => 'index.php?module=Achievementallot&view=List',
		    'linkicon' => '',
		);
		
		$quickLink7 = array(
		    'linktype' => 'SIDEBARLINK',
		    'linklabel' => '未匹配合同回款列表',
		    'linkurl' => 'index.php?module=ReceivedPayments&view=List&filter=noservice',
		    'linkicon' => '',
		);

        if($this->exportGrouprt('ReceivedPayments','ExportRI')){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '导出回款合同数据',
                'linkurl' =>'index.php?module=ReceivedPayments&view=List&public=ExportRI',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }
		$moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
		if($permission) {
		    //wangbin 2015年5月7日 星期四 去掉 回款列表上的筛选字段列表
			//$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
			//$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
			//$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);
			//$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
			//$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink5);
			//$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink5);
            //临时方法处理
            if($userId!=10710){
                $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
            }
		    $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink7);
            //end
		}
        if($this->exportGrouprt('ReceivedPayments','ExportR')){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '导出有效回款数据',
                'linkurl' => 'index.php?module=ReceivedPayments&view=List&public=ExportR',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }
        
        if($this->exportGrouprt('ReceivedPayments','ExportRM')){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '设置导出权限',
                'linkurl' => 'index.php?module=ReceivedPayments&view=List&public=ExportRM',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }

        if(ReceivedPayments_Record_Model::getImportUserPermissions()){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '合同，回款导出（商务）',
                'linkurl' => 'index.php?module=ReceivedPayments&view=List&public=ExportRALL',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }
        if($this->exportGrouprt('AchievementallotStatistic','departset')){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '业绩导出设置',
                'linkurl' => 'index.php?module=AchievementallotStatistic&view=List&public=departset',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }

        if($this->exportGrouprt('ReceivedPayments','ExportNPP')){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '未匹配回款',
                'linkurl' => 'index.php?module=ReceivedPayments&view=List&public=ExportNPP',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }

        if($this->exportGrouprt('ReceivedPayments','Import')){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '回款导入',
                'linkurl' => 'index.php?module=ReceivedPayments&view=Import',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }
        if($this->exportGrouprt('ReceivedPayments','ExportPerformanceSmall')){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '中小商务有效业绩导出',
                'linkurl' => 'index.php?module=ReceivedPayments&view=List&public=ExportPerformanceSmall',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }
        //临时方法处理
        if($userId!=10710){
            $quickLink9 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '销售业绩明细表',
                'linkurl' => 'index.php?module=AchievementallotStatistic&view=List',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink9);

            $quickLink10 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '销售业绩汇总表',
                'linkurl' => 'index.php?module=AchievementSummary&view=List',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink10);
        }
		return $parentQuickLinks;
	}
    /**
     * 可导出数据的权限
     * @return bool
     */
    public function exportGroupri(){
        global $current_user;
        $id=$current_user->id;
        $db=PearDatabase::getInstance();
        //不必过滤是否在职因为离职的根本就登陆不了系统
        $query="select vtiger_user2department.userid from vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE CONCAT(vtiger_departments.parentdepartment,'::') REGEXP 'H25::'";
        $result=$db->run_query_allrecords($query);
        $userids=array();
        foreach($result as $values){
            $userids[]=$values['userid'];
        }
        $userids[]=1;
        //$userids=array(1,2155,323,1923);//有访问权限的
        if(in_array($id,$userids)){
            return true;
        }
        return false;
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
     * 设置导出权限
     * @return bool
     */
    public function exportsalesetset(){
        global $current_user;
        //$userids=array(1,2155,323,1923);//有访问权限的
        if($current_user->is_admin=='on'){
            return true;
        }
        return false;
    }

    /**
     * 导出有效业绩汇总
     * @param Vtiger_Request $request
     * @param $phpexecl
     * @return mixed
     */
    public function exportSmallPerformanceAll(Vtiger_Request $request,$phpexecl){
        $db=PearDatabase::getInstance();

        $datetime=$request->get('datatime');
        //$query="SELECT last_name AS user_name, userid AS receiveid,businesstcost,sum(if(discount>=1,(businessunit-businesstcost),if(discount>=0.75,((businessunit*discount)-businesstcost),0))) AS totalprice,sum(if(otherdiscount>=1,(businessunit-businesstcost),if(otherdiscount>=0.75,((businessunit*otherdiscount)-businesstcost),0))) AS othertotalprice FROM vtiger_performance_evaluation WHERE IF(LEFT(vtiger_performance_evaluation.orderdate,7) > LEFT(vtiger_performance_evaluation.receivedpaymatchdate,7),LEFT(vtiger_performance_evaluation.orderdate,7) = '{$datetime}',LEFT(vtiger_performance_evaluation.receivedpaymatchdate,7) = '{$datetime}') GROUP BY userid ORDER BY null";
        $query="SELECT (SELECT last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_achievementallot.receivedpaymentownid) AS user_name, 
                    vtiger_achievementallot.receivedpaymentownid AS receiveid,
                    sum(vtiger_achievementallot.businessunit) AS businessunit,
                    TRUNCATE(sum(if(vtiger_achievementallot.discount>=1 OR vtiger_achievementallot.discount IS NULL,(vtiger_achievementallot.businessunit-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*(vtiger_achievementallot.tyuncost)*vtiger_achievementallot.scalling/100,0)),if(discount>=0.75,(vtiger_achievementallot.businessunit*vtiger_achievementallot.discount-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*(vtiger_achievementallot.tyuncost)*vtiger_achievementallot.scalling/100,0)),0))),2) AS totalprice,
                    TRUNCATE(sum(if(vtiger_achievementallot.seconddiscount>=1 OR vtiger_achievementallot.seconddiscount IS NULL,(vtiger_achievementallot.businessunit-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*(vtiger_achievementallot.tyuncost)*vtiger_achievementallot.scalling/100,0)),if(seconddiscount>=0.75,(vtiger_achievementallot.businessunit*vtiger_achievementallot.seconddiscount-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*(vtiger_achievementallot.tyuncost)*vtiger_achievementallot.scalling/100,0)),0))),2) AS othertotalprice
                FROM vtiger_achievementallot 
                WHERE vtiger_achievementallot.achievementdate='{$datetime}' GROUP BY vtiger_achievementallot.receivedpaymentownid ORDER BY null";
        $result= $db->run_query_allrecords($query);
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '姓名')
            ->setCellValue('B1', '(总部,深圳)当月业绩')
            ->setCellValue('C1', '非(总部,深圳)当月业绩')
            ->setCellValue('D1', '月份');
        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:D1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if(!empty($result)){
            foreach($result as $key=>$value){
                $current=$key+2;
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A'.$current, $value['user_name'])
                    ->setCellValue('B'.$current, $value['totalprice'])
                    ->setCellValue('C'.$current, $value['othertotalprice'])
                    ->setCellValue('D'.$current, $datetime);
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':D'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }
        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle($datetime.'有效业绩');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        return $phpexecl;
    }

    /**
     * 个人有效业绩的明细
     * @param Vtiger_Request $request
     * @param $phpexecl
     * @return mixed
     */
    public function exportSmallPerformancePerson(Vtiger_Request $request,$phpexecl){
        $db=PearDatabase::getInstance();

        $datetime=$request->get('datatime');
        $userid=$request->get('userid');
        //$query="SELECT last_name AS user_name,userid AS receiveid,vtiger_servicecontracts.contract_no,vtiger_performance_evaluation.receivedpaymatchdate,vtiger_performance_evaluation.matchdate,vtiger_performance_evaluation.orderdate,vtiger_performance_evaluation.receivedate,vtiger_performance_evaluation.businesstcost,vtiger_performance_evaluation.businessunit,vtiger_performance_evaluation.discount,vtiger_performance_evaluation.otherdiscount,IF (discount >= 1,(businessunit - businesstcost),IF(discount >= 0.75,(	(businessunit * discount) - businesstcost),0)) AS totalprice,IF(otherdiscount >= 1,(businessunit - businesstcost),IF(otherdiscount >= 0.75,((businessunit * otherdiscount)-businesstcost),0)) AS othertotalprice FROM vtiger_performance_evaluation LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid = vtiger_performance_evaluation.receivedpaymentsid LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid WHERE vtiger_performance_evaluation.userid={$userid} AND IF(LEFT(vtiger_performance_evaluation.orderdate,7) > LEFT(vtiger_performance_evaluation.receivedpaymatchdate,7),LEFT(vtiger_performance_evaluation.orderdate,7) = '{$datetime}',LEFT(vtiger_performance_evaluation.receivedpaymatchdate,7) = '{$datetime}')";
        $query="SELECT
                    (SELECT last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_achievementallot.receivedpaymentownid) AS user_name,
                    vtiger_achievementallot.receivedpaymentownid AS receiveid,
                    vtiger_receivedpayments.owncompany,
                    vtiger_achievementallot.businessunit AS unit_price,
                    vtiger_receivedpayments.owncompany,
                    vtiger_receivedpayments.reality_date,
                    vtiger_receivedpayments.overdue,
                    vtiger_receivedpayments.createdtime,
                    vtiger_servicecontracts.signdate,
                    vtiger_departments.departmentname,
                    vtiger_account.accountname,
                    vtiger_servicecontracts.contract_no,
                    vtiger_servicecontracts.total,
                    vtiger_servicecontracts.productid,
                    (SELECT CONCAT(vtiger_products.productname) FROM vtiger_products WHERE vtiger_products.productid IN(vtiger_servicecontracts.productid)) AS productname,
                    vtiger_achievementallot.firstmarketprice AS marketprice,
                    vtiger_achievementallot.secondmarketprice AS marketpricetwo,
                    vtiger_achievementallot.achievementdate as receivedpaymatchdate,
                    vtiger_achievementallot.matchdate as matchdate,
                    vtiger_achievementallot.workorderdate AS orderdate,
                    vtiger_achievementallot.postingdate as receivedate,
                    vtiger_achievementallot.tyuncost AS businesstcost,
                    vtiger_achievementallot.businessunit AS businessunit,
                    vtiger_achievementallot.scalling,
                    vtiger_achievementallot.othercost,
                    vtiger_achievementallot.idccost,
                    vtiger_achievementallot.discount as discount,
                    vtiger_achievementallot.seconddiscount as otherdiscount,
                    TRUNCATE(if(vtiger_achievementallot.discount>=1 OR vtiger_achievementallot.discount IS NULL,(vtiger_achievementallot.businessunit-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.tyuncost,0)-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.othercost,0)-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.idccost,0)),if(discount>=0.75,(vtiger_achievementallot.businessunit*vtiger_achievementallot.discount-vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.tyuncost-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.othercost,0)-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.idccost,0)),0)),2) AS totalprice,
                    TRUNCATE(if(vtiger_achievementallot.seconddiscount>=1 OR vtiger_achievementallot.seconddiscount IS NULL,(vtiger_achievementallot.businessunit-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.tyuncost,0)-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.othercost,0)-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.idccost,0)),if(seconddiscount>=0.75,ifnull(vtiger_achievementallot.businessunit*vtiger_achievementallot.seconddiscount-vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.tyuncost-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.othercost,0)-IFNULL(vtiger_achievementallot.businessunit/vtiger_achievementallot.concattotal*vtiger_achievementallot.idccost,0),0),0)),2) AS othertotalprice
                FROM
                    vtiger_achievementallot
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_achievementallot.servicecontractid
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_achievementallot.receivedpaymentownid
                LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                WHERE
                vtiger_receivedpayments.receivedpaymentsid>0
                AND vtiger_achievementallot.achievementdate= '{$datetime}'
                AND vtiger_achievementallot.discount>0
                ORDER BY vtiger_achievementallot.receivedpaymentownid";
        $result= $db->run_query_allrecords($query);
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '账号')
            ->setCellValue('B1', '收款日期')
            ->setCellValue('C1', '年')
            ->setCellValue('D1', '月')
            ->setCellValue('E1', '日')
            ->setCellValue('F1', '收款金额')
            ->setCellValue('G1', '业务员所属事业部')
            ->setCellValue('H1', '销售组')
            ->setCellValue('I1', '业务员')
            ->setCellValue('J1', '')
            ->setCellValue('K1', '合同抬头')
            ->setCellValue('L1', '合同签订日期')
            ->setCellValue('M1', '合同编号')
            ->setCellValue('N1', '产品种类')
            ->setCellValue('O1', '产品名称')
            ->setCellValue('P1', '业务种类')
            ->setCellValue('Q1', '合同总金额')
            ->setCellValue('R1', '')
            ->setCellValue('S1', '未收款项')
            ->setCellValue('T1', '应收款/备注')
            ->setCellValue('U1', '是否开票')
            ->setCellValue('V1', '开票日期')
            ->setCellValue('W1', '开票号码')
            ->setCellValue('X1', '开票金额')
            ->setCellValue('Y1', '是否截稿')
            ->setCellValue('Z1', '提成')
            ->setCellValue('AA1', '总成本合计')
            ->setCellValue('AB1', '人力成本合计')
            ->setCellValue('AC1', 'IDC珍岛空间成本合计')
            ->setCellValue('AD1', '外采成本合计')
            ->setCellValue('AE1', '其他内部成本合计')
            ->setCellValue('AF1', '其他业务成本')
            ->setCellValue('AG1', 'POS机手续费')
            ->setCellValue('AH1', 'IDC人力成本')
            ->setCellValue('AI1', 'IDC珍岛空间成本')
            ->setCellValue('AJ1', 'IDC外采成本')
            ->setCellValue('AK1', 'IDC成本合计')
            ->setCellValue('AL1', '开发部人力成本')
            ->setCellValue('AM1', '开发部外采成本')
            ->setCellValue('AN1', '开发部部其他内部成本')
            ->setCellValue('AO1', '开发部成本合计')
            ->setCellValue('AP1', 'T-云运营部人力成本')
            ->setCellValue('AQ1', 'T-云运营部外采成本')
            ->setCellValue('AR1', 'T-云运营部其他内部成本')
            ->setCellValue('AS1', 'T-云运营部成本合计')
            ->setCellValue('AT1', '品牌客户部人力成本')
            ->setCellValue('AU1', '品牌客户部外采成本')
            ->setCellValue('AV1', '品牌客户部其他内部成本')
            ->setCellValue('AW1', '品牌客户部成本合计')
            ->setCellValue('AX1', '研发部人力成本')
            ->setCellValue('AY1', '研发部外采成本')
            ->setCellValue('AZ1', '研发部其他内部成本')
            ->setCellValue('BA1', '电商事业部成本合计')
            ->setCellValue('BB1', '中小渠道部人力成本')
            ->setCellValue('BC1', '中小渠道部外采成本')
            ->setCellValue('BD1', '中小渠道部其他内部成本')
            ->setCellValue('BE1', '中小渠道部成本合计')
            ->setCellValue('BF1', '技术服务部人力成本')
            ->setCellValue('BG1', '技术服务部外采成本')
            ->setCellValue('BH1', '技术服务部其他内部成本')
            ->setCellValue('BI1', '技术服务部成本合计')
            ->setCellValue('BJ1', '海外事业部人力成本')
            ->setCellValue('BK1', '海外事业部外采成本')
            ->setCellValue('BL1', '海外事业部其他内部成本')
            ->setCellValue('BM1', '海外成本合计')
            ->setCellValue('BN1', 'KA事业部人力成本')
            ->setCellValue('BO1', 'KA事业部外采成')
            ->setCellValue('BP1', 'KA事业部其他内部成本')
            ->setCellValue('BQ1', 'KA事业部成本合计')
            ->setCellValue('BR1', '客户服务部人力成本合计')
            ->setCellValue('BS1', '客户服务部外采成本合计')
            ->setCellValue('BT1', '其他外采成本')
            ->setCellValue('BU1', '提成月份')
            ->setCellValue('BV1', '项目名称')
            ->setCellValue('BW1', '提成类型')
            ->setCellValue('BX1', '市场金额')
            ->setCellValue('BY1', '成本扣除数')
            ->setCellValue('BZ1', '新单到账业绩')
            ->setCellValue('CA1', '续费到账业绩')
            ->setCellValue('CB1', '续费提成')
            ->setCellValue('CC1', '折扣')
            ->setCellValue('CD1', '业务备注')
            ->setCellValue('CE1', '市场价')
            ->setCellValue('CF1', '分成比例')
            ->setCellValue('CG1', '入帐日期')
            ->setCellValue('CH1', '工单日期')
            ->setCellValue('CI1', '匹配日期')
            ->setCellValue('CJ1', '折分后回款金额')
            ->setCellValue('CK1', '(总部,深圳)折扣')
            ->setCellValue('CL1', '非(总部,深圳)折扣')
            ->setCellValue('CM1', '(总部,深圳)当月业绩')
            ->setCellValue('CN1', '非(总部,深圳)当月业绩')
            ->setCellValue('CO1', '非总部市场价')
            ->setCellValue('CP1', '额外成本')
            ->setCellValue('CQ1', 'IDC赠选成本');

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:CQ1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$result=array(1,2,3,4,5,6,7,3,8,9,10);
        $phpexecl->getActiveSheet()->getStyle('AI1:AL1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFFCC');
        $phpexecl->getActiveSheet()->getStyle('AY1:BB1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFFCC');
        $phpexecl->getActiveSheet()->getStyle('BG1:BJ1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFFCC');
        $phpexecl->getActiveSheet()->getStyle('BO1:BR1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFFCC');
        $phpexecl->getActiveSheet()->getStyle('BO1:BR1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFFCC');
        $phpexecl->getActiveSheet()->getStyle('BU1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFFCC');
        $phpexecl->getActiveSheet()->getStyle('BV1:CC1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF8080');
        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:CQ1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $username='';
        if(!empty($result)){
            foreach($result as $key=>$value){
                $relnewfeeflag=strpos($value['productname'],'续费');
                $current=$key+2;
                $username=$value['user_name'];
                $phpexecl->setActiveSheetIndex(0)
                    ->setCellValue('A'.$current, $value['owncompany'])
                    ->setCellValue('B'.$current, $value['reality_date'])
                    ->setCellValue('C'.$current, substr($value['createdtime'],0,4))
                    ->setCellValue('D'.$current, substr($value['createdtime'],5,2))
                    ->setCellValue('E'.$current, substr($value['createdtime'],8,2))
                    ->setCellValue('F'.$current, $value['unit_price'])
                    ->setCellValue('G'.$current, '')
                    ->setCellValue('H'.$current, $value['departmentname'])
                    ->setCellValue('I'.$current, $value['user_name'])
                    ->setCellValue('J'.$current, '')
                    ->setCellValue('K'.$current, $value['accountname'])
                    ->setCellValue('L'.$current, $value['signdate'])
                    ->setCellValue('M'.$current, $value['contract_no'])
                    ->setCellValue('N'.$current, '')
                    ->setCellValue('O'.$current,'')
                    ->setCellValue('P'.$current, $value['productname'])
                    ->setCellValue('Q'.$current, $value['total'])
                    ->setCellValue('R'.$current, $value['total'])
                    ->setCellValue('S'.$current, '')
                    ->setCellValue('T'.$current, '')
                    ->setCellValue('U'.$current, '')
                    ->setCellValue('V'.$current, '')
                    ->setCellValue('W'.$current, '')
                    ->setCellValue('X'.$current, '')
                    ->setCellValue('Y'.$current, '')
                    ->setCellValue('Z'.$current, '')
                    ->setCellValue('AA'.$current,'')
                    ->setCellValue('AB'.$current, '')
                    ->setCellValue('AC'.$current, '')
                    ->setCellValue('AD'.$current, '')
                    ->setCellValue('AE'.$current, '')
                    ->setCellValue('AF'.$current, '')
                    ->setCellValue('AG'.$current, '')
                    ->setCellValue('AH'.$current, '')
                    ->setCellValue('AI'.$current, '')
                    ->setCellValue('AJ'.$current, '')
                    ->setCellValue('AK'.$current, '')
                    ->setCellValue('AL'.$current, '')
                    ->setCellValue('AM'.$current, '')
                    ->setCellValue('AN'.$current, '')
                    ->setCellValue('AO'.$current, '')
                    ->setCellValue('AP'.$current, '')
                    ->setCellValue('AQ'.$current, '')
                    ->setCellValue('AR'.$current, '')
                    ->setCellValue('AS'.$current, '')
                    ->setCellValue('AT'.$current, '')
                    ->setCellValue('AU'.$current, '')
                    ->setCellValue('AV'.$current, '')
                    ->setCellValue('AW'.$current, '')
                    ->setCellValue('AX'.$current, '')
                    ->setCellValue('AY'.$current, '')
                    ->setCellValue('AZ'.$current, '')
                    ->setCellValue('BA'.$current, '')
                    ->setCellValue('BB'.$current, '')
                    ->setCellValue('BC'.$current, '')
                    ->setCellValue('BD'.$current, '')
                    ->setCellValue('BE'.$current, '')
                    ->setCellValue('BF'.$current, '')
                    ->setCellValue('BG'.$current, '')
                    ->setCellValue('BH'.$current, '')
                    ->setCellValue('BI'.$current, '')
                    ->setCellValue('BJ'.$current, '')
                    ->setCellValue('BK'.$current, '')
                    ->setCellValue('BL'.$current, '')
                    ->setCellValue('BM'.$current, '')
                    ->setCellValue('BN'.$current, '')
                    ->setCellValue('BO'.$current, '')
                    ->setCellValue('BP'.$current, '')
                    ->setCellValue('BQ'.$current, '')
                    ->setCellValue('BR'.$current, '')
                    ->setCellValue('BS'.$current, '')
                    ->setCellValue('BT'.$current, '')
                    ->setCellValue('BU'.$current, $datetime)
                    ->setCellValue('BV'.$current, $value['productname'])
                    ->setCellValue('BW'.$current, '')
                    ->setCellValue('BX'.$current, $value['marketprice'])
                    ->setCellValue('BY'.$current, $value['businesstcost'])
                    ->setCellValue('BZ'.$current, (!$relnewfeeflag?($value['totalprice']==0?$value['othertotalprice']:$value['totalprice']):''))
                    ->setCellValue('CA'.$current, ($relnewfeeflag?($value['totalprice']==0?$value['othertotalprice']:$value['totalprice']):''))
                    ->setCellValue('CB'.$current, '')
                    ->setCellValue('CC'.$current, $value['discount'])
                    ->setCellValue('CD'.$current, '')
                    ->setCellValue('CE'.$current, '')
                    ->setCellValue('CF'.$current, $value['scalling'].'%')
                    ->setCellValue('CG'.$current, $value['receivedate'])
                    ->setCellValue('CH'.$current, $value['matchdate'])
                    ->setCellValue('CI'.$current, $value['orderdate'])
                    ->setCellValue('CJ'.$current, $value['businessunit'])
                    ->setCellValue('CK'.$current, $value['discount'])
                    ->setCellValue('CL'.$current, $value['otherdiscount'])
                    ->setCellValue('CM'.$current, $value['totalprice'])
                    ->setCellValue('CN'.$current, $value['othertotalprice'])
                    ->setCellValue('CO'.$current, $value['marketpricetwo'])
                    ->setCellValue('CP'.$current, $value['othercost'])
                    ->setCellValue('CQ'.$current, $value['idccost']);
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('BV'.$current.':CC'.$current)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF8080');
                $phpexecl->getActiveSheet()->getStyle('CF'.$current.':CQ'.$current)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF8080');
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':CE'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }
        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle($datetime.'有效业绩');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        return $phpexecl;
    }
}