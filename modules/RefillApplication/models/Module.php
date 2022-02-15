<?php

class RefillApplication_Module_Model extends Vtiger_Module_Model{

	 public function getSideBarLinks($linkParams) {
		$parentQuickLinks = array();
		$quickLink = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '充值申请单',
				'linkurl' => $this->getListViewUrl(),
				'linkicon' => '',
		);
		$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
         if($this->exportGrouprt('RefillApplication','AuditSettings')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '充值申请单审核设置',
                 'linkurl' => $this->getListViewUrl() . '&public=AuditSettings',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         /*
         //if($this->exportGrouprt('RefillApplication','RefillDetailExport')){
             $quickLink3 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '充值申请单明细导出',
                 'linkurl' => $this->getListViewUrl() . '&public=RefillDetailExport',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);
         //}
         if($this->exportGrouprt('RefillApplication','RefillSumExport')){
             $quickLink4 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '回款充值申请单导出',
                 'linkurl' => $this->getListViewUrl() . '&public=RefillSumExport',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
         }*/
         if($this->exportGrouprt('RefillApplication','relationPaymentsExport')){
             $quickLink4 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '充值单回款操作记录导出',
                 'linkurl' => $this->getListViewUrl() . '&public=relationPaymentsExport',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
         }
         if($this->exportGrouprt('RefillApplication','rechargeguarantee')){
             $quickLink5 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '默认担保',
                 'linkurl' => $this->getListViewUrl() . '&public=rechargeguarantee',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink5);
             $quickLink6 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '客户担保',
                 'linkurl' => $this->getListViewUrl() . '&public=accountrechargeguarantee',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
             $quickLink6 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '技术充值设置',
                 'linkurl' => $this->getListViewUrl().'&public=techprocurement',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
             $quickLink6 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '预充值设置',
                 'linkurl' => $this->getListViewUrl().'&public=PreRecharge',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
             $quickLink6 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '其他充值设置',
                 'linkurl' => $this->getListViewUrl().'&public=OtherProcurement',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);

         }
         foreach(array('Accounts','Vendors','PreRecharge','PACKVENDORS','TECHPROCUREMENT','NonMediaExtraction','COINRETURN','INCREASE','contractChanges') as $value){
             $quickLink4 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => vtranslate($value,'RefillApplication').'列表',
                 'linkurl' => $this->getListViewUrl() . '&rechargesource='.$value,
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
         }
         
            if($this->exportGrouprt('RefillApplication','hcDetailsExport')){
                $quickLink4 = array(
                    'linktype' => 'SIDEBARLINK',
                    'linklabel' => '红冲明细导出',
                    'linkurl' => $this->getListViewUrl() . '&public=hcDetailsExport',
                    'linkicon' => '',
                );
                $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
            }
            
            return $parentQuickLinks;
	}

    /**邮件通知
     * @param string $recordid 申请单id
     * @param int $is_add 是否追加申请单（1：是，0：否）
     */
    public static function sendRefillApplicationMail($recordid="",$is_add=0) {
        global $current_user;
        $user_id=$current_user->id;
        $db=PearDatabase::getInstance();
        if($is_add != 1){
            $result_user = $db->pquery("SELECT smcreatorid FROM vtiger_crmentity WHERE crmid =? ",array($recordid));
            $user_id = $db->query_result($result_user, 0, 'smcreatorid');
        }
        //获取最后一个节点
        $last_sequence = 0;
        $result_user = $db->pquery("SELECT MAX(sequence) AS last_sequence FROM vtiger_salesorderworkflowstages WHERE salesorderid = ? ",array($recordid));
        $last_sequence = $db->query_result($result_user, 0, 'last_sequence');

        //查找客户垫款
        $advancesmoney = 0;

        $accountid =  $_REQUEST['accountid'];
        if(empty($accountid)){
            //获取客户id
            $result_account = $db->pquery("SELECT accountid FROM vtiger_refillapplication WHERE refillapplicationid=? ",array($recordid));
            $accountid = $db->query_result($result_account, 0, 'accountid');
        }

        $result = $db->pquery("SELECT advancesmoney  FROM vtiger_account WHERE accountid =? ",array($accountid));
        if ($result && $db->num_rows($result) > 0) {
            $row = $db->fetch_array($result);
            $advancesmoney =  $row['advancesmoney'];
        }

        //获取当前审核节点
        //$audit_level = 1;
        //if($is_add == 0){
            $check_sql ="SELECT salesorderworkflowstagesid,sequence as audit_level, 
                    (SELECT count(1) FROM vtiger_workflowstages WHERE vtiger_workflowstages.workflowstagesid=vtiger_salesorderworkflowstages.workflowstagesid AND vtiger_workflowstages.handleaction='PlatformCheck') AS platform_cnt
                  FROM vtiger_salesorderworkflowstages 
                    LEFT JOIN vtiger_refillapplication ON(vtiger_salesorderworkflowstages.salesorderid=vtiger_refillapplication.refillapplicationid)
                    WHERE vtiger_salesorderworkflowstages.modulename='RefillApplication' AND vtiger_refillapplication.modulestatus!='c_complete' AND vtiger_salesorderworkflowstages.isaction=1
                    AND vtiger_refillapplication.refillapplicationid=?";
            $result_check = $db->pquery($check_sql,array($recordid));
            if (empty($result_check) || $db->num_rows($result_check) == 0) return;
            //$audit_level = $db->query_result($result_check, 0, 'sequence');
        $result_check_row = $array_mail = $db->query_result_rowdata($result_check, 0);
        //}

        //查找申请人所属的审核人
        $auditSql = "SELECT DISTINCT
            vtiger_auditsettings.oneaudituid,vtiger_auditsettings.towaudituid,vtiger_auditsettings.audituid3,
            vtiger_auditsettings.department,
            u1.email1 as u1_email1,u1.email2  as u1_email2,u1.last_name as u1_last_name,
            u2.email1 as u2_email1,u2.email2  as u2_email2,u2.last_name as u2_last_name,
            u3.email1 as u3_email1,u3.email2  as u3_email2,u3.last_name as u3_last_name
            FROM vtiger_auditsettings
            LEFT JOIN vtiger_users u1 ON(vtiger_auditsettings.oneaudituid = u1.id)
            LEFT JOIN vtiger_users u2 ON(vtiger_auditsettings.towaudituid = u2.id)
            LEFT JOIN vtiger_users u3 ON(vtiger_auditsettings.audituid3 = u3.id)
            WHERE
            vtiger_auditsettings.auditsettingtype='RefillApplication' 
            AND FIND_IN_SET(vtiger_auditsettings.department,(SELECT REPLACE(parentdepartment,'::',',') FROM vtiger_departments WHERE vtiger_departments.departmentid =(SELECT departmentid FROM vtiger_user2department WHERE userid=?)))
            ORDER BY vtiger_auditsettings.department DESC";
        $result_audit = $db->pquery($auditSql,array($user_id));

        if ($result_audit) {
            $rows = $db->num_rows($result_audit);
            if ($rows > 0) {
                //获取当前用户所属的上级所有部门
                $cur_parentdepartment = "";
                $departmentSql = "SELECT REPLACE(parentdepartment,'::',',') as parentdepartment FROM vtiger_departments WHERE vtiger_departments.departmentid =(SELECT departmentid FROM vtiger_user2department WHERE userid=?)";
                $result_department = $db->pquery($departmentSql,array($user_id));
                if ($result_department && $db->num_rows($result_department) > 0) {
                    $row = $db->fetch_array($result_department);
                    $cur_parentdepartment =  $row['parentdepartment'];
                }
                $array_department = explode(',',$cur_parentdepartment);
                if(empty($array_department) || count($array_department) == 0) return;
                //取得申请人所在部门的上级最近部门
                //$cur_audit_department = end($array_department);
                $cnt = count($array_department);
                //发邮件处理
                $check = 0;
                for($j=$cnt-1;$j>=0;$j--){
                    $cur_audit_department = $array_department[$j];
                    for($i=0; $i<$rows; $i++) {
                        $department = $db->query_result($result_audit, $i, 'department');
                        if($cur_audit_department == $department){
                            $array_mail = $db->query_result_rowdata($result_audit, $i);
                            self::send_RefillApplicationMail($advancesmoney,$array_mail,$recordid,$result_check_row,$last_sequence);
                            $check = 1;
                            break;
                        }
                    }
                    if($check == 1) break;
                }
            }
        }
    }
    /**
     * @param int $advancesmoney 垫款金额(默认0)
     * @param array $array_mail 审核人信息(邮箱和名称)
     * @param string $recordid 充值申请单ID
     * @param int $result_check_row 当前节点信息
     */
    public static function send_RefillApplicationMail($advancesmoney=0, $array_mail=array(),$recordid="",$result_check_row =array(),$last_sequence=0) {
        return ;
        if(empty($array_mail) || count($array_mail) == 0) return;
        $db=PearDatabase::getInstance();
        $u1_email1 = $array_mail['u1_email1'];
        $u1_email2 = $array_mail['u1_email2'];
        $u1_last_name = $array_mail['u1_last_name'];
        $u2_email1 = $array_mail['u2_email1'];
        $u2_email2 = $array_mail['u2_email2'];
        $u2_last_name = $array_mail['u2_last_name'];
        $u3_email1 = $array_mail['u3_email1'];
        $u3_email2 = $array_mail['u3_email2'];
        $u3_last_name = $array_mail['u3_last_name'];

        //节点id
        $salesorderworkflowstagesid = $result_check_row["salesorderworkflowstagesid"];
        //节点级别
        $audit_level = $result_check_row["audit_level"];
        //平台负责人邮箱(多个)
        $emails = $result_check_row["emails"];
        //平台个数
        $platform_cnt = $result_check_row["platform_cnt"];

        //标题
        $Subject = '待审核充值申请单邮件提醒';
        //内容
        $body  = '有充值申请单,需要您的审核,详情如下：<br><br> ';
        //$body .= '&nbsp;&nbsp;充值平台:&nbsp;&nbsp;'.$_REQUEST['topplatform'].'<br> ';
        // $body .= '&nbsp;&nbsp;货币类型:&nbsp;&nbsp;'.$_REQUEST['receivementcurrencytype'].'<br>';
        //$body .= '&nbsp;&nbsp;充值金额:&nbsp;&nbsp;'.$_REQUEST['prestoreadrate'].'<br> ';
        if(empty($recordid)) return;
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication');
        $entity=$recordModel->entity->column_fields;
        //$body .= '&nbsp;&nbsp;充值申请单编号:&nbsp;&nbsp;'.$entity['refillapplicationno'].'<br> ';
        $body .= "申请单编号:&nbsp;&nbsp;<a href='".$_SERVER['HTTP_HOST']."/index.php?module=RefillApplication&view=Detail&record={$recordid}'>{$entity['refillapplicationno']}</a><br/>";
        $body .= '申请时间:&nbsp;&nbsp;'.date('Y-m-d H:i:s');
        //收件人地址
        $address=array();

        //更新审核人节点
        $query="UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.modulename='RefillApplication' AND vtiger_salesorderworkflowstages.salesorderworkflowstagesid=?";

        if($audit_level == 1){
            //通知一级审核人
            $u1_email1 = $u1_email1 != '' ? trim($u1_email1) : trim($u1_email2);
            $address[] = array("mail"=>$u1_email1,"name"=>$u1_last_name);
            //更新一级审核人节点
            $db->pquery($query,array($array_mail['oneaudituid'],$salesorderworkflowstagesid));
            if($advancesmoney > 0){
                //有垫款：走二级审核（邮件提醒审核人）
            }else{
                //无垫款：走一级审核（邮件同时提醒两级审核人）
                $u2_email1 = $u2_email1 != '' ? trim($u2_email1) : trim($u2_email2);
                $address[] = array("mail"=>$u2_email1,"name"=>$u2_last_name);
                $u3_email1 = $u3_email1 != '' ? trim($u3_email1) : trim($u3_email2);
                $address[] = array("mail"=>$u3_email1,"name"=>$u3_last_name);
            }
        }else{
            if($platform_cnt > 0){
                $sql2="SELECT 
                (SELECT GROUP_CONCAT(IFNULL(vtiger_users.email1,vtiger_users.email2)) FROM vtiger_users WHERE FIND_IN_SET(vtiger_users.id, REPLACE(GROUP_CONCAT(platformids), ' |##| ', ','))) AS emails
                FROM vtiger_salesorderworkflowstages 
                WHERE vtiger_salesorderworkflowstages.modulename='RefillApplication'
                AND vtiger_salesorderworkflowstages.salesorderid=? AND NOT ISNULL(platformids)";
                $result2 = $db->pquery($sql2,array($recordid));
                $emails = "";
                if ($result2 && $db->num_rows($result2) > 0) {
                    $row = $db->fetch_array($result2);
                    $emails =  $row['emails'];
                }
                //当前节点是平台审核的情况
                if(!empty($emails)){
                    //有平台负责人的情况
                    //查找同级阶段是否都已经审核
                    $sql3="SELECT isaction FROM vtiger_salesorderworkflowstages WHERE salesorderid = ? AND modulename =? AND sequence =? ORDER BY sequence ASC";
                    $result3=$db->pquery($sql3,array($recordid,"RefillApplication",$audit_level));
                    $audit_cnt = -1;
                    if($db->num_rows($result3)) {
                        $audit_cnt = 0;
                        while ($row = $db->fetch_array($result3)) {
                            if ($row['isaction'] == 2) {
                                $audit_cnt++;break;
                            }
                        }
                    }

                    //第一次发邮件提醒
                    if($audit_cnt == 0){
                        $arr_emails = explode(',',$emails);
                        $cnt = count($arr_emails);
                        for($i=0;$i<$cnt;$i++) {
                            $address[] = array("mail"=>$arr_emails[$i],"name"=>"");
                        }
                    }
                }else{
                    //无平台审核负责人的情况跳过平台审核
                    if($audit_level == 3 && !empty($array_mail['towaudituid'])){
                        $u2_email1 = $u2_email1 != '' ? trim($u2_email1) : trim($u2_email2);
                        $address[] = array("mail"=>$u2_email1,"name"=>$u2_last_name);
                        if($last_sequence > $audit_level){
                            //更新两级审核人节点
                            $db->pquery($query,array($array_mail['towaudituid'],$salesorderworkflowstagesid));
                        }
                    }else if($audit_level == 4 && !empty($array_mail['audituid3'])){
                        $u3_email1 = $u3_email1 != '' ? trim($u3_email1) : trim($u3_email2);
                        $address[] = array("mail"=>$u3_email1,"name"=>$u3_last_name);
                        if($last_sequence > $audit_level){
                            //更新三级审核人节点
                            $db->pquery($query, array($array_mail['audituid3'], $salesorderworkflowstagesid));
                        }
                    }else{
                        return;
                    }
                }
            }else{
                //无平台审核的情况
                if($audit_level == 3 && !empty($array_mail['towaudituid'])){
                    $u2_email1 = $u2_email1 != '' ? trim($u2_email1) : trim($u2_email2);
                    $address[] = array("mail"=>$u2_email1,"name"=>$u2_last_name);
                    if($last_sequence > $audit_level){
                        //更新两级审核人节点
                        $db->pquery($query,array($array_mail['towaudituid'],$salesorderworkflowstagesid));
                    }
                }else if($audit_level == 4 && !empty($array_mail['audituid3'])){
                    $u3_email1 = $u3_email1 != '' ? trim($u3_email1) : trim($u3_email2);
                    $address[] = array("mail"=>$u3_email1,"name"=>$u3_last_name);
                    if($last_sequence > $audit_level){
                        //更新三级审核人节点
                        $db->pquery($query, array($array_mail['audituid3'], $salesorderworkflowstagesid));
                    }
                }else{
                    return;
                }
            }
        }
        //发送邮件
        if(!empty($address)){
            Vtiger_Record_Model::sendMail($Subject,$body,$address);
        }
    }
	/**
     * 处理充值审请明细导出
     * @param Vtiger_Request $request
     */
    public function refillDetailExportDataExcel(Vtiger_Request $request){
        global $site_URL,$current_user,$root_directory;
        set_time_limit(0);
        ini_set('memory_limit','2048M');
        $data['name']='充值审请明细导出';
        $path=$root_directory.'temp/';
        $filename=$path.'refillapplition'.$current_user->id.'.xlsx';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $data['data']=$this->refillDetailExportData($request);
        $this->refillExportDataExcel($data);
        header('location:'.$site_URL.'temp/refillapplition'.$current_user->id.'.xlsx');
        //$this->refillExportDataCSV($data);
    }

    /**处理充值审请汇总导出
     * @param Vtiger_Request $request
     */
    public function refillSumExportDataExcel(Vtiger_Request $request){
        $data['name']='充值审请汇总导出';
        $tempData=$this->refillAndPaymentExportData($request);
        /*$temparray=array();
        foreach($tempData as $value){
            if(empty($value)){
                //没有值退出当次
                continue;
            }
            $tempstr=trim($value['contract_no']).trim($value['topplatform']).trim($value['accountzh']);
            $value['rechargeamount']=is_numeric($value['rechargeamount'])?$value['rechargeamount']:0;
            $value['prestoreadrate']=is_numeric($value['prestoreadrate'])?$value['prestoreadrate']:0;
            if(in_array($tempstr,$temparray)){
                $data['data'][md5($tempstr)]['rechargeamount']+=$value['rechargeamount'];
                $data['data'][md5($tempstr)]['prestoreadrate']+=$value['prestoreadrate'];
            }else{
                $temparray[]=$tempstr;
                $data['data'][md5($tempstr)]=$value;//用字符串MD5加密作键避免出现空格,等乱字符扰乱
            }
        }*/
        $this->refillAndPaymentExportDataExcel($tempData);
    }

    /**
     * 导出的Execl方法
     * @param $data
     */
    public function refillExportDataExcel($data){
        global $root_directory,$current_user;
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $phpexecl=new PHPExcel();
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
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
        $pcolumn=0;
        $headerArray=$data['data']['LISTVIEW_HEADERS'];
        foreach($headerArray as $key=>$value){
            $phpexecl->setActiveSheetIndex(0)->setCellValueByColumnAndRow($pcolumn, 1,vtranslate($key,'RefillApplication'));
            $pcolumn++;
        }
        $execlNum=$this->instanceCellNum();
        /*//设置填充颜色
        $phpexecl->getActiveSheet()->getStyle('A1:D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFC000');
        $phpexecl->getActiveSheet()->getStyle('E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFDA9694');
        $phpexecl->getActiveSheet()->getStyle('F1:G1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $phpexecl->getActiveSheet()->getStyle('H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFDA9694');
        $phpexecl->getActiveSheet()->getStyle('I1:L1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $phpexecl->getActiveSheet()->getStyle('N1:R1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $phpexecl->getActiveSheet()->getStyle('M1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFDE9D9');
        $phpexecl->getActiveSheet()->getStyle('S1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFF79646');
        $phpexecl->getActiveSheet()->getStyle('T1:W1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $phpexecl->getActiveSheet()->getStyle('X1:AM1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8E4BC');*/

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:'.$execlNum[$pcolumn].'1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:'.$execlNum[$pcolumn].'1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $LISTVIEW_ENTRIES=$data['data']['LISTVIEW_ENTRIES'];
        if(!empty($LISTVIEW_ENTRIES)){
            $rechargesheetid=array();
            $refillappraymentid=array();
            $refillapplicationid=array();
            //$rechargesheetidfield=array('rechargesheetid','refillapplicationid','topplatform','accountzh','did','rechargetype','rechargeamount','prestoreadrate','discount','tax','factorage','activationfee','totalcost','dailybudget','transferamount','rebateamount','totalgrossprofit','servicecost','mstatus','isentity','modifiedby','modifiedtime','deleted','createdtime','createdid','receivementcurrencytype','exchangerate','rechargetypedetail','taxation','isprovideservice','supprebate','refundamount','suppliercontractsid','productservice','havesignedcontract','signdate','productid','rebates','purchaseamount','purchasequantity','purchaseprice','customeroriginattr','amountpayablesum','amountpayable','rebatetype','accountrebatetype');
            $rechargesheetidfield=array();
            $refillappraymentidfield=array('arrivaldate','refillapptotal');
            $refillapplicationidfield=array();
            $redrefundfield=array("redrefundamount","redtaxation","redactivationfee","redfactorage","redamountpayable");

            foreach($LISTVIEW_ENTRIES as $key=>$value){
                $current=$key+2;
                $pcolumn=0;
                foreach($headerArray as $keyheader=>$valueheader){
                    /*if((in_array($value['rechargesheetid'],$rechargesheetid) && in_array($valueheader['columnname'],$rechargesheetidfield))
                    || (in_array($value['refillapplicationid'],$refillapplicationid) && in_array($valueheader['columnname'],$refillapplicationidfield))
                    || (in_array($value['refillappraymentid'],$refillappraymentid) && in_array($valueheader['columnname'],$refillappraymentidfield))
                    ){*/
                    if(in_array($value['refillappraymentid'],$refillappraymentid) && in_array($valueheader['columnname'],$refillappraymentidfield)
                        || in_array($value['rechargesheetid'],$rechargesheetid) && in_array($valueheader['columnname'],$redrefundfield)
                    ){
                        $currnetValue='/';
                    }else{
                        if($valueheader['uitype']==10){
                            //$currnetValue=$value[$keyheader];
                            $currnetValue=uitypeformat($valueheader,$value,'RefillApplication');
                            $pattern='/<[^>]+>/';
                            $currnetValue=preg_replace($pattern,'',$currnetValue);
                        }elseif($valueheader['uitype']==15){
                            $currnetValue=vtranslate($value[$keyheader],'RefillApplication');
                        }else{
                            $currnetValue=uitypeformat($valueheader,$value,'RefillApplication');
                        }
                    }

                    $phpexecl->setActiveSheetIndex(0)->setCellValueByColumnAndRow($pcolumn, $current,$currnetValue);
                    $pcolumn++;
                }
                /*if($value['refillapplicationid']>0 && !in_array($value['refillapplicationid'],$refillapplicationid)){
                    $refillapplicationid[]=$value['refillapplicationid'];
                }*/
                if($value['refillappraymentid']>0 && !in_array($value['refillappraymentid'],$refillappraymentid)){
                    $refillappraymentid[]=$value['refillappraymentid'];
                }
                if($value['rechargesheetid']>0 && !in_array($value['rechargesheetid'],$rechargesheetid)){
                    $rechargesheetid[]=$value['rechargesheetid'];
                }
                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':'.$execlNum[$pcolumn].$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
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
        $filename=$path.'refillapplition'.$current_user->id.'.xlsx';
        $objWriter->save($filename);
    }
    /**
     * 导出的Execl方法bak
     * @param $data
     */
    public function refillAndPaymentExportDataExcel($data){
        global $root_directory,$current_user,$site_URL;
        $path=$root_directory.'temp/';
        $filename=$path.'refillandpayment'.$current_user->id.'.xlsx';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
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
        //$phpexecl->setActiveSheetIndex(0)->mergeCells('A1:AF1');
        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '账户')
            ->setCellValue('B1', '收款日期')
            ->setCellValue('C1', '汇款抬头')
            ->setCellValue('D1', '汇款金额')
            ->setCellValue('E1', '收款金额(已分成)')
            ->setCellValue('F1', '销售组')
            ->setCellValue('G1', '业务员')
            ->setCellValue('H1', '客户名称')
            ->setCellValue('I1', '合同签订日期')
            ->setCellValue('J1', '合同编号')
            ->setCellValue('K1', '申请单编号')
            ->setCellValue('L1', '申请人')
            ->setCellValue('M1', '服务合同')
            ->setCellValue('N1', '客户')
            ->setCellValue('O1', '客户类型')
            ->setCellValue('P1', '充值平台')
            ->setCellValue('Q1', '账户名称')
            ->setCellValue('R1', 'ID')
            ->setCellValue('S1', '充值账户币')
            ->setCellValue('T1', '供应商返点类型')
            ->setCellValue('U1', '客户返点类型')
            ->setCellValue('V1', '供应商返点比例')
            ->setCellValue('W1', '客户返点比例')
            ->setCellValue('X1', '代理商服务费')
            ->setCellValue('Y1', '开户费')
            ->setCellValue('Z1', '税费')
            ->setCellValue('AA1', '合计费用')
            ->setCellValue('AB1', '毛利总计')
            ->setCellValue('AC1', '成本')
            ->setCellValue('AD1', '使用回款金额')
        ;
        //设置填充颜色
        /*$phpexecl->getActiveSheet()->getStyle('A1:D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFC000');
        $phpexecl->getActiveSheet()->getStyle('E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFDA9694');
        $phpexecl->getActiveSheet()->getStyle('F1:G1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $phpexecl->getActiveSheet()->getStyle('H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFDA9694');
        $phpexecl->getActiveSheet()->getStyle('I1:L1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $phpexecl->getActiveSheet()->getStyle('N1:R1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $phpexecl->getActiveSheet()->getStyle('M1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFDE9D9');
        $phpexecl->getActiveSheet()->getStyle('S1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFF79646');
        $phpexecl->getActiveSheet()->getStyle('T1:W1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $phpexecl->getActiveSheet()->getStyle('X1:AM1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8E4BC');*/

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:AD1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:AD1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $current=2;
        foreach($data as $value){
            $phpexecl->setActiveSheetIndex(0)
                ->setCellValue('A'.$current, $value['owncompany'])
                ->setCellValue('B'.$current, $value['reality_date'])
                ->setCellValue('C'.$current, $value['paytitle'])
                ->setCellValue('D'.$current, $value['unit_price'])
                ->setCellValue('E'.$current, $value['businessunit'])
                ->setCellValue('F'.$current, $value['departmentname'])
                ->setCellValue('G'.$current, $value['username'])
                ->setCellValue('H'.$current, $value['serviceaccountname'])
                ->setCellValue('I'.$current, $value['signdate'])
                ->setCellValue('J'.$current, $value['contract_no'])
                ->setCellValue('K'.$current, $value['refillapplicationno'])
                ->setCellValue('L'.$current, $value['smownerid'])
                ->setCellValue('M'.$current, $value['refillcontractno'])
                ->setCellValue('N'.$current, $value['refillaccountname'])
                ->setCellValue('O'.$current, vtranslate($value['customertype'],"RefillApplication"))
                ->setCellValue('P'.$current, $value['productname'])
                ->setCellValue('Q'.$current, $value['accountzh'])
                ->setCellValue('R'.$current, $value['did'])
                ->setCellValue('S'.$current, $value['prestoreadrate'])
                ->setCellValue('T'.$current, vtranslate($value['rebatetype'],"RefillApplication"))
                ->setCellValue('U'.$current, vtranslate($value['accountrebatetype'],"RefillApplication"))
                ->setCellValue('V'.$current, $value['supprebate'])
                ->setCellValue('W'.$current, $value['discount'])
                ->setCellValue('X'.$current, $value['factorage'])
                ->setCellValue('Y'.$current, $value['activationfee'])
                ->setCellValue('Z'.$current, $value['taxation'])
                ->setCellValue('AA'.$current,$value['totalcost'])
                ->setCellValue('AB'.$current, $value['totalgrossprofit'])
                ->setCellValue('AC'.$current, $value['servicecost'])
                ->setCellValue('AD'.$current, $value['refillapptotal'])
                ;
            $phpexecl->getActiveSheet()->getStyle('A'.$current.':AD'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpexecl->getActiveSheet()->getStyle('A'.$current.':AD'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $phpexecl->getActiveSheet()->getStyle('A'.$current.':AD'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $current++;
        }
        // 设置工作表的名称
        $phpexecl->getActiveSheet()->setTitle('回款充值单');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        //$objWriter->save('php://output');
        $objWriter->save($filename);
        header('location:'.$site_URL.'temp/refillandpayment'.$current_user->id.'.xlsx');
    }
    /**
     * 取得充值审请单的数据bak
     * @param Vtiger_Request $request
     * @return array
     */
    /*
    public function refillDetailExportDatabak(Vtiger_Request $request){
        $startdate=$request->get('datatime');
        $enddate=$request->get('enddatatime');
        $listQuery="SELECT 
                        vtiger_account.accountname,
                        vtiger_refillapplication.modulestatus,
                        vtiger_refillapplication.customertype,
                        vtiger_refillapplication.refillapplicationno,
                        (SELECT last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_crmentity.smownerid) AS username,
                        vtiger_crmentity.createdtime,
                        (SELECT GROUP_CONCAT(vtiger_receivedpayments.paytitle) FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.relatetoid=vtiger_refillapplication.servicecontractsid) AS paytitle,
                        vtiger_servicecontracts.contract_no,
                        vtiger_servicecontracts.service_charge,
                        vtiger_servicecontracts.account_opening_fee,
                        vtiger_rechargesheet.topplatform,
                        vtiger_rechargesheet.accountzh,
                        vtiger_rechargesheet.did,
                        vtiger_rechargesheet.rechargeamount,
                        vtiger_rechargesheet.rechargetypedetail,
                        vtiger_rechargesheet.prestoreadrate
                     FROM vtiger_refillapplication 
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid
                    LEFT JOIN vtiger_rechargesheet ON vtiger_refillapplication.refillapplicationid=vtiger_rechargesheet.refillapplicationid 
                    LEFT JOIN vtiger_servicecontracts ON vtiger_refillapplication.servicecontractsid=vtiger_servicecontracts.servicecontractsid
                    LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_refillapplication.accountid
                    WHERE vtiger_refillapplication.modulestatus='c_complete' AND left(vtiger_refillapplication.workflowstime,10)>=? AND left(vtiger_refillapplication.workflowstime,10)<=? ORDER BY vtiger_refillapplication.servicecontractsid";
        $db=PearDatabase::getInstance();
        $result=$db->pquery($listQuery,array($startdate,$enddate));
        $dataRow=array();
        while($dataRow[]=$db->fetch_array($result));
        return $dataRow;

    }*/
    /**
     * 取得充值审请单的数据
     * @param Vtiger_Request $request
     * @return array
     */
    public function refillDetailExportData(Vtiger_Request $request)
    {
        $startdate = $request->get('datatime');
        $enddate = $request->get('enddatatime');
        $listViewModel = Vtiger_ListView_Model::getInstance('RefillApplication');
        $query = $listViewModel->getQuery();
        $query=str_replace('vtiger_refillapplication.refillapplicationid FROM','vtiger_rechargesheet.rechargesheetid,vtiger_refillapprayment.refillappraymentid,vtiger_refillapprayment.paytitle,vtiger_refillapprayment.total,vtiger_refillapprayment.arrivaldate,vtiger_refillapprayment.refillapptotal,vtiger_refillapprayment.refundamount,
        (SELECT sum(vtiger_rubricrechargesheet.refundamount) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redrefundamount,
        (SELECT sum(vtiger_rubricrechargesheet.activationfee) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redactivationfee,
        (SELECT sum(vtiger_rubricrechargesheet.taxation) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redtaxation,
        (SELECT sum(vtiger_rubricrechargesheet.factorage) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redfactorage,
        (SELECT sum(vtiger_rubricrechargesheet.amountpayable) FROM `vtiger_rubricrechargesheet` WHERE vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid AND vtiger_rubricrechargesheet.deleted=0) AS redamountpayable,
        vtiger_refillapplication.refillapplicationid FROM',$query);
        $query=str_replace('WHERE 1=1',"LEFT JOIN vtiger_refillapprayment ON (vtiger_refillapprayment.refillapplicationid=vtiger_refillapplication.refillapplicationid AND vtiger_refillapprayment.deleted=0 AND vtiger_refillapprayment.receivedstatus='normal') WHERE 1=1",$query);
        $query .= ' AND vtiger_rechargesheet.deleted=0';
        $where=getAccessibleUsers('RefillApplication','List',true);
        if($where!='1=1'){
            $query.= ' AND vtiger_crmentity.smownerid in('.implode(',',$where).')';
        }
        if(strtotime($startdate) < strtotime($enddate)){
            $tempdate = " BETWEEN '{$startdate}' AND '{$enddate}'";
        }elseif (strtotime($startdate) > strtotime($enddate)) {
            $tempdate = " BETWEEN '{$enddate}' AND '{$startdate}'";
        }else {
            $tempdate = "='{$startdate}'";
        }
        $query.=' AND left(vtiger_crmentity.createdtime,10)'.$tempdate;
        $query.=' ORDER BY vtiger_refillapplication.refillapplicationid';
        global $adb;
        $result=$adb->pquery($query,array());
        $listViewRecordModels = array();
        while ($rawData = $adb->fetch_array($result)) {
            $listViewRecordModels[] = $rawData;
        }
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)) {
            $temp = $listViewHeaders;
        }
        $arr=array("paytitle" => Array("tabid"=> 148,"columnname" => "paytitle","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "paytitle","fieldlabel" => "paytitle"),
            "total" => Array("tabid"=> 148,"columnname" => "total","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "total","fieldlabel" => "total"),
            "arrivaldate" => Array("tabid"=> 148,"columnname" => "arrivaldate","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "arrivaldate","fieldlabel" => "arrivaldate"),
            "refillapptotal" => Array("tabid"=> 148,"columnname" => "refillapptotal","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "refillapptotal","fieldlabel" => "refillapptotal"),
            "refundamount" => Array("tabid"=> 148,"columnname" => "refundamount","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "refundamount","fieldlabel" => "refundamount"),
            "redrefundamount" => Array("tabid"=> 148,"columnname" => "redrefundamount","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redrefundamount","fieldlabel" => "redrefundamount"),
            "redtaxation" => Array("tabid"=> 148,"columnname" => "redtaxation","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redtaxation","fieldlabel" => "redtaxation"),
            "redactivationfee" => Array("tabid"=> 148,"columnname" => "redactivationfee","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redactivationfee","fieldlabel" => "redactivationfee"),
            "redfactorage" => Array("tabid"=> 148,"columnname" => "redfactorage","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redfactorage","fieldlabel" => "redfactorage"),
            "redamountpayable" => Array("tabid"=> 148,"columnname" => "redamountpayable","tablename"=> "vtiger_rechargesheet", "generatedtype" => 1, "uitype" => 1,"fieldname" => "redamountpayable","fieldlabel" => "redamountpayable"),
        );
        $temp=array_merge($temp,$arr);
        return array('LISTVIEW_HEADERS'=>$temp,'LISTVIEW_ENTRIES'=>$listViewRecordModels);
    }

    /**
     * 生成A-YZ序列
     * @return array
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function instanceCellNum(){
        $arr=array();
        for($e='A';$e<='Z';$e++){
            $arr[]=$e;
        }
        return $arr;
    }

    /**
     * @param $data
     * @author: steel.liu
     * @Date:xxx
     * 导出csv格式的数据
     */
    public function refillExportDataCSV($data){
        $headerArray=$data['data']['LISTVIEW_HEADERS'];
        $str='';
        foreach($headerArray as $key=>$value){
            $str.=iconv('utf-8','gb2312',vtranslate($key,'RefillApplication')).',';
        }
        $str=trim($str,',');
        $str.="\n";
        $LISTVIEW_ENTRIES=$data['data']['LISTVIEW_ENTRIES'];
        if(!empty($LISTVIEW_ENTRIES)){
            foreach($LISTVIEW_ENTRIES as $key=>$value){
                foreach($headerArray as $keyheader=>$valueheader){
                    if($valueheader['uitype']==10){
                        $currnetValue=uitypeformat($valueheader,$value,'RefillApplication');
                        $pattern='/<[^>]+>/';
                        $currnetValue=preg_replace($pattern,'',$currnetValue);
                    }elseif($valueheader['uitype']==15){
                        $currnetValue=vtranslate($value[$keyheader],'RefillApplication');
                    }else{
                        $currnetValue=uitypeformat($valueheader,$value,'RefillApplication');
                    }
                    $currnetValue=iconv('utf-8','gb2312',$currnetValue);
                    $str.=$currnetValue.',';
                }
                $str=trim($str,',');
                $str.="\n";
            }
        }

        $filename = $data['name'].date('YmdHis').'.csv'; //设置文件名
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $str;
    }

    /**
     * @param Vtiger_Request $request
     * @return array
     * @author: steel.liu
     * @Date:xxx
     * 导出回款充值记录数据
     */
    public function refillAndPaymentExportData(Vtiger_Request $request){
        $startdate = $request->get('datatime');
        $enddate = $request->get('enddatatime');
        $query="SELECT 
                    vtiger_receivedpayments.owncompany,
                    vtiger_receivedpayments.reality_date,
                    vtiger_receivedpayments.paytitle,
                    vtiger_receivedpayments.unit_price,
                    vtiger_achievementallot.businessunit,
                    (SELECT departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid=vtiger_achievementallot.departmentid LIMIT 1) AS departmentname,
                    (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_achievementallot.receivedpaymentownid=vtiger_users.id) as username,
                    (SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_servicecontracts.sc_related_to limit 1) AS serviceaccountname,
                    vtiger_servicecontracts.signdate,
                    vtiger_servicecontracts.contract_no,
                    vtiger_refillapplication.refillapplicationno,
                    (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,
                    (SELECT servicetable.contract_no FROM vtiger_servicecontracts AS servicetable WHERE servicetable.servicecontractsid=vtiger_refillapplication.servicecontractsid LIMIT 1) AS refillcontractno,
                    (SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_refillapplication.accountid LIMIT 1) AS refillaccountname,
                    vtiger_refillapplication.customertype,
                    (SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid=vtiger_rechargesheet.productid LIMIT 1) AS productname,
                    vtiger_rechargesheet.accountzh,
                    vtiger_rechargesheet.did,
                    vtiger_rechargesheet.rebatetype,
                    vtiger_rechargesheet.accountrebatetype,
                    vtiger_rechargesheet.supprebate,
                    vtiger_rechargesheet.discount,
                    vtiger_rechargesheet.activationfee,
                    vtiger_rechargesheet.taxation,
                    vtiger_rechargesheet.totalcost,
                    vtiger_rechargesheet.totalgrossprofit,
                    vtiger_refillapprayment.refillapptotal,
                    vtiger_rechargesheet.servicecost
                    FROM vtiger_achievementallot
                    LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                    LEFT JOIN vtiger_servicecontracts ON vtiger_receivedpayments.relatetoid=vtiger_servicecontracts.servicecontractsid
                    LEFT JOIN vtiger_refillapprayment ON (vtiger_refillapprayment.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid AND vtiger_refillapprayment.deleted=0 AND vtiger_refillapprayment.receivedstatus='normal')
                    LEFT JOIN vtiger_refillapplication ON vtiger_refillapplication.refillapplicationid=vtiger_refillapprayment.refillapplicationid
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid
                    LEFT JOIN vtiger_rechargesheet ON (vtiger_rechargesheet.refillapplicationid=vtiger_refillapplication.refillapplicationid AND vtiger_rechargesheet.deleted=0)
                    WHERE 
                    vtiger_receivedpayments.deleted=0 AND vtiger_refillapplication.refillapplicationid>0";
        if(strtotime($startdate) < strtotime($enddate)){
            $tempdate = " BETWEEN '{$startdate}' AND '{$enddate}'";
        }elseif (strtotime($startdate) > strtotime($enddate)) {
            $tempdate = " BETWEEN '{$enddate}' AND '{$startdate}'";
        }else {
            $tempdate = "='{$startdate}'";
        }
        $query.=' AND left(vtiger_receivedpayments.reality_date,10)'.$tempdate;
        global $adb;
        $result=$adb->pquery($query,array());
        $listViewRecordModels = array();
        while ($rawData = $adb->fetch_array($result)) {
            $listViewRecordModels[] = $rawData;
        }
        return $listViewRecordModels;
    }
    /**
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function relationPayments(Vtiger_Request $request){
        global $root_directory,$current_user,$site_URL;
        $path=$root_directory.'temp/';
        $filename=$path.'relationpayments'.$current_user->id.'.xlsx';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $data=$this->relationPaymentsData($request);
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
            ->setCellValue('A1', '操作者')
            ->setCellValue('B1', '操作时间')
            ->setCellValue('C1', '回款抬头')
            ->setCellValue('D1', '入账金额')
            ->setCellValue('E1', '入账日期')
            ->setCellValue('F1', '使用回款金额')
            ->setCellValue('G1', '充值单号')
            ->setCellValue('H1', '充值单状态')
            ->setCellValue('I1', '回款状态')
            ->setCellValue('J1', '退款金额')
            ->setCellValue('K1', '充值平台')
        ;

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:K1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $current=2;
        foreach($data as $value){
            $receivedstatus=($value['receivedstatus']=='normal'&& $value['deleted']==0)?'正常':(
                ($value['receivedstatus']=='normal'&& $value['deleted']==1)?'解除使用':(
                ($value['receivedstatus']=='revokerelation'&& $value['deleted']==0)?'解除中':'手动解除使用'
                ));
            $phpexecl->setActiveSheetIndex(0)
                ->setCellValue('A'.$current, $value['smownerid'])
                ->setCellValue('B'.$current, $value['createdtime'])
                ->setCellValue('C'.$current, $value['paytitle'])
                ->setCellValue('D'.$current, $value['total'])
                ->setCellValue('E'.$current, $value['arrivaldate'])
                ->setCellValue('F'.$current, $value['refillapptotal'])
                ->setCellValue('G'.$current, $value['refillapplicationno'])
                ->setCellValue('H'.$current, vtranslate($value['modulestatus'],"RefillApplication"))
                ->setCellValue('I'.$current, $receivedstatus)
                ->setCellValue('J'.$current, $value['refundamount'])
                ->setCellValue('K'.$current, str_replace(',','/',$value['productname']))
            ;
            $phpexecl->getActiveSheet()->getStyle('A'.$current.':K'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpexecl->getActiveSheet()->getStyle('A'.$current.':K'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $phpexecl->getActiveSheet()->getStyle('A'.$current.':K'.$current)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $current++;
        }
        // 设置工作表的名称
        $phpexecl->getActiveSheet()->setTitle('回款充值单');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        //$objWriter->save('php://output');
        $objWriter->save($filename);
        header('location:'.$site_URL.'temp/relationpayments'.$current_user->id.'.xlsx');
    }
    public function relationPaymentsData(Vtiger_Request $request){
        $startdate = $request->get('datatime');
        $enddate = $request->get('enddatatime');
        $query="SELECT 
                    (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,
                    vtiger_refillapprayment.createdtime,
                    vtiger_refillapprayment.paytitle,
                    vtiger_refillapprayment.total,
                    vtiger_refillapprayment.arrivaldate,
                    vtiger_refillapprayment.refillapptotal,
                    vtiger_refillapplication.refillapplicationno,
                    vtiger_refillapplication.modulestatus,
                    vtiger_refillapprayment.receivedstatus,
                    vtiger_refillapprayment.deleted,
                    vtiger_refillapprayment.refundamount,
                    (SELECT GROUP_CONCAT(vtiger_products.productname) FROM `vtiger_products`,(SELECT vtiger_rechargesheet.productid,vtiger_rechargesheet.refillapplicationid	FROM	vtiger_rechargesheet WHERE	vtiger_rechargesheet.deleted = 0 GROUP BY	vtiger_rechargesheet.refillapplicationid,vtiger_rechargesheet.productid) AS rechrename	WHERE (rechrename.productid = vtiger_products.productid AND rechrename.refillapplicationid = vtiger_refillapprayment.refillapplicationid)) AS productname
                FROM vtiger_refillapprayment 
                LEFT JOIN vtiger_refillapplication ON vtiger_refillapplication.refillapplicationid=vtiger_refillapprayment.refillapplicationid
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid
                WHERE 1=1";
        if(strtotime($startdate) < strtotime($enddate)){
            $tempdate = " BETWEEN '{$startdate}' AND '{$enddate}'";
        }elseif (strtotime($startdate) > strtotime($enddate)) {
            $tempdate = " BETWEEN '{$enddate}' AND '{$startdate}'";
        }else {
            $tempdate = "='{$startdate}'";
        }
        $query.=' AND vtiger_refillapprayment.matchdate'.$tempdate;
        global $adb;
        $result=$adb->pquery($query,array());
        $listViewRecordModels = array();
        while ($rawData = $adb->fetch_array($result)) {
            $listViewRecordModels[] = $rawData;
        }
        return $listViewRecordModels;
    }
}
?>