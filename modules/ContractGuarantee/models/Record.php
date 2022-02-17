<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger Entity Record Model Class
 */
class ContractGuarantee_Record_Model extends Vtiger_Record_Model {

    public function exportData(Vtiger_Request $request){
        $searchDepartment=$_REQUEST['department'];
        $where=getAccessibleUsers('ServiceContracts','List',true);
        $userid=getDepartmentUser($searchDepartment);

        //$departments=$request->get('department');
        $startdate=$request->get('datatime');
        $enddatatime=$request->get('enddatatime');
        $listQuery="SELECT * FROM ( SELECT 
                    vtiger_contractguarantee.modulename,
                    IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid,
                    IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_contractguarantee.oneconfirm=vtiger_users.id),'--') as oneconfirm,
                    IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_contractguarantee.twoconfirm=vtiger_users.id),'--') as twoconfirm,
                    (SELECT crm.label FROM vtiger_crmentity as crm WHERE crm.crmid=vtiger_contractguarantee.contractid) AS contractid,
                    vtiger_contractguarantee.guaranteedate,
                    vtiger_contractguarantee.oneguaranteedate,
                    if(vtiger_contractguarantee.modulename='ServiceContracts',
                    (SELECT vtiger_servicecontracts.modulestatus FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid=vtiger_contractguarantee.contractid),
                    (SELECT vtiger_suppliercontracts.modulestatus FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid=vtiger_contractguarantee.contractid)) AS contractstatus,
                    vtiger_contractguarantee.accountname,
                    IF (
                        (select COUNT(vtiger_newinvoice.invoiceid) from vtiger_newinvoice where vtiger_newinvoice.contractid = vtiger_contractguarantee.contractid and 
                                                        vtiger_newinvoice.modulestatus NOT in('c_cancel','a_normal','a_exception')) >=1,
                        '是',
                        '否'
                    ) as 'is_invoice',
                    IF (  (select COUNT(*)  from vtiger_refillapplication where vtiger_refillapplication.servicecontractsid = vtiger_contractguarantee.contractid and vtiger_refillapplication.modulestatus NOT in('c_cancel','a_normal','a_exception'))>=1, 
                           '是', 
                          ( IF (
                                (SELECT
                                        COUNT(vtiger_rechargesheet.suppliercontractsid)
                                FROM
                                        vtiger_rechargesheet  LEFT JOIN vtiger_refillapplication ON vtiger_rechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid 
                                WHERE 
                                vtiger_rechargesheet.suppliercontractsid = vtiger_contractguarantee.contractid 
                                        AND vtiger_refillapplication.modulestatus NOT IN ('c_cancel','a_normal','a_exception'
                                                )
                                ) >= 1,
                                '是',
                                '否'
                            )
                          )
                    ) AS 'is_contractguarantee'
                    FROM vtiger_contractguarantee  
                    LEFT JOIN vtiger_crmentity ON vtiger_contractguarantee.contractguaranteeid = vtiger_crmentity.crmid 
                    LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_contractguarantee.workflowsid 
                    WHERE 1=1 and vtiger_crmentity.deleted=0 and vtiger_contractguarantee.modulestatus='c_complete'  AND vtiger_contractguarantee.is_exportable='able_toexport' ";
        if(!empty($searchDepartment)){
            if(!empty($where)&&$where!='1=1'){
                $where=array_intersect($where,$userid);
                $listQuery .= " AND vtiger_crmentity.smownerid IN(".implode(',', $where).")";
            }else{
                //$where=$userid;
            }
        }
        /*if(strtotime($startdate)<strtotime($enddatatime)){
            $listQuery.=" and vtiger_contractguarantee.guaranteedate  between '{$startdate}' and '{$enddatatime}'";
        }elseif(strtotime($startdate)==strtotime($enddatatime)){
            $listQuery.=" vtiger_contractguarantee.guaranteedate='{$enddatatime}'";
        }elseif(strtotime($startdate)>strtotime($enddatatime)){
            $listQuery.=" vtiger_contractguarantee.guaranteedate between '{$enddatatime}' and '{$startdate}'";
        }*/
        $listQuery.="GROUP BY  vtiger_contractguarantee.contractid   ) as a WHERE a.contractstatus NOT IN('c_complete') ";
//        echo $listQuery;exit;
         ob_end_clean();//清除缓冲区,避免乱码
        global $root_directory;
        $db=PearDatabase::getInstance();
        $result= $db->run_query_allrecords($listQuery);

        include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';

        ob_clean();                              //清空缓存
        header('Content-type: text/html;charset=utf-8');
        $phpexecl=new PHPExcel();

        // Set document properties
        $phpexecl->getProperties()->setCreator("liu ganglin")
            ->setLastModifiedBy("liu ganglin")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("servicecontracts");

        $headerArray=array(
            'contractid'=>'合同编号',
            'smownerid'=>'申请人',
            'oneconfirm'=>'第一级审核',
            'oneguaranteedate'=>'第一级审核时间',
            'twoconfirm'=>'第二级审核',
            'guaranteedate'=>'第二级审核时间',
            'modulename'=>'类型',
            'contractstatus'=>'合同状态',
            'accountname'=>'客户/供应商名称',
            'is_invoice'=>'是否已提交发票',
            'is_contractguarantee'=>'是否已提交充值申请单',
            );
        // 添加头信处
        $pcolumn=0;
        foreach($headerArray as $key=>$value){
            $phpexecl->setActiveSheetIndex(0)->setCellValueByColumnAndRow($pcolumn, 1,$value);
            $pcolumn++;
        }

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:H1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if(!empty($result)){

            foreach($result as $key=>$value){
                $current=$key+2;
                $pcolumn=0;
                foreach($headerArray as $keyheader=>$valueheader){
                    if($keyheader=='modulename'){
                        $value[$keyheader]=$value[$keyheader]=='ServiceContracts'?'服务合同':'采购合同';
                    }
                    if($keyheader=='contractstatus'){
                        $value[$keyheader]=vtranslate($value[$keyheader],'ServiceContracts');
                    }
                    $phpexecl->setActiveSheetIndex(0)->setCellValueByColumnAndRow($pcolumn, $current,$value[$keyheader]);
                    $pcolumn++;
                }

                //加上边框
                $phpexecl->getActiveSheet()->getStyle('A'.$current.':H'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        // 设置工作表的名称
        $phpexecl->getActiveSheet()->setTitle('担保合同导出');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpexecl->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="担保合同导出.xlsx"');
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

    /**
     * 检查是否可以消除合同担保
     * @param Vtiger_Request $request
     * @return array
     * @throws Exception
     */
    public function checkIsCanCancel(Vtiger_Request $request){
        global $adb;
        $data=array();
        $record=$request->get('record');
        $contractType=$request->get("contractType");
        try{
            //判断担保合同是否已经作废
            $query= "SELECT modulestatus FROM  vtiger_contractguarantee  WHERE contractguaranteeid = ? limit 1 ";
            $modulestatus=$adb->pquery($query,array($record));
            $modulestatus = $adb->query_result($modulestatus, 0, 'modulestatus');
            if($modulestatus == 'c_cancel'){
                return array('result'=>false,'message'=> '担保合同已作废。');
            }
            // 如果是服务合同
            if($contractType=='ServiceContracts'){
                // 获取合同id
                $query= "SELECT contractid FROM  vtiger_contractguarantee  WHERE contractguaranteeid = ? limit 1 ";
                $contractid=$adb->pquery($query,array($record));
                $contractid = $adb->query_result($contractid, 0, 'contractid');
                //判断是否存在充值申请单
                $query= " SELECT count(1) as count FROM  vtiger_refillapplication as a INNER JOIN vtiger_crmentity as b ON a.refillapplicationid=b.crmid WHERE   b.deleted=0  AND a.rechargesource!='contractChanges' AND  ( a.modulestatus = 'b_actioning' OR a.modulestatus = 'b_check' OR a.modulestatus = 'c_complete' OR   a.modulestatus = 'a_exception') AND a.servicecontractsid = ? limit 1 ";
                $number=$adb->pquery($query,array($contractid));
                $number = $adb->query_result($number, 0, 'count');
                if($number>0){
                    return array('result'=>false,'message'=> '担保合同已提交充值申请单，不可消除担保。');
                }
                //判断是否存在  发票单
                $query= " SELECT count(1) as count FROM  vtiger_newinvoice as a  INNER JOIN vtiger_crmentity as b ON a.invoiceid=b.crmid WHERE  b.deleted=0  AND  ( a.modulestatus = 'b_actioning' OR a.modulestatus = 'b_check' OR a.modulestatus = 'c_complete'  OR   a.modulestatus = 'a_exception' OR  a.modulestatus = 'a_normal') AND a.contractid = ? limit 1 ";
                $number=$adb->pquery($query,array($contractid));
                $number = $adb->query_result($number, 0, 'count');
                if($number>0){
                    return array('result'=>false,'message'=> '担保合同已提交发票，不可消除担保。');
                }else{
                    return array('result'=>true);
                }
                //如果是采购合同
            }elseif($contractType=='SupplierContracts'){
                //获取合同id
                $query= "SELECT contractid FROM  vtiger_contractguarantee  WHERE contractguaranteeid = ? limit 1 ";
                $contractid=$adb->pquery($query,array($record));
                $contractid = $adb->query_result($contractid, 0, 'contractid');
                //判断是否存在充值申请单
                $query= " SELECT count(1) as count FROM  vtiger_rechargesheet  INNER JOIN  vtiger_refillapplication ON vtiger_refillapplication.refillapplicationid=vtiger_rechargesheet.refillapplicationid INNER JOIN vtiger_crmentity ON  vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid  WHERE  vtiger_crmentity.deleted=0  AND vtiger_refillapplication.rechargesource!='contractChanges' AND ( vtiger_refillapplication.modulestatus = 'b_actioning' OR vtiger_refillapplication.modulestatus = 'b_check' OR vtiger_refillapplication.modulestatus = 'c_complete'  OR vtiger_refillapplication.modulestatus = 'a_exception') AND suppliercontractsid = ? limit 1 ";
                $number=$adb->pquery($query,array($contractid));
                $number = $adb->query_result($number, 0, 'count');
                if($number>0){
                    return array('result'=>false,'message'=> '担保合同已提交充值申请单，不可消除担保。');
                }
                //判断是否存在  发票单
                $query= " SELECT count(1) as count FROM  vtiger_newinvoice as a  INNER JOIN vtiger_crmentity as b ON a.invoiceid=b.crmid WHERE  b.deleted=0  AND  ( a.modulestatus = 'b_actioning' OR a.modulestatus = 'b_check' OR a.modulestatus = 'c_complete'  OR   a.modulestatus = 'a_exception' OR  a.modulestatus = 'a_normal' ) AND a.contractid = ? limit 1 ";
                $number=$adb->pquery($query,array($contractid));
                $number = $adb->query_result($number, 0, 'count');
                if($number>0){
                    return array('result'=>false,'message'=> '担保合同已提交发票，不可消除担保。');
                }else{
                    return  array('result'=>true);
                }
            }else{
                return array('result'=>false,'message'=> '未知错误请重试','data'=>$request->getAll());
            }
        }catch (Exception $e){
            return array('result'=>false,'message'=> '出现异常请重试','data'=>$request->getAll());
        }

    }
    /**
     * 消除服务合同
     * @param Vtiger_Request $request
     * @return array
     *
     */
    public function cancelContract(Vtiger_Request $request){
        global $adb;
        global $current_user;

        $data=array();
        $record=$request->get('record');
        $cancelguaranteeresason=$request->get('voidreason');
        $contractType=$request->get("contractType");
        try{
            //如果是服务合同
            if($contractType=='ServiceContracts'){
                if($record>0){
                    // 更新合同担保表
                    $query=" UPDATE vtiger_contractguarantee SET modulestatus='c_cancel',username=?,userid=?,cancelguaranteeresason=?,cancelguaranteedate=? WHERE contractguaranteeid= ? ";
                    $adb->pquery($query,array($current_user->last_name,$current_user->id,$cancelguaranteeresason,date("Y-m-d H:i:s"),$record));
                    // 获取合同id
                    $query= "SELECT contractid FROM  vtiger_contractguarantee  WHERE contractguaranteeid = ? limit 1 ";
                    $contractid=$adb->pquery($query,array($record));$contractid=$adb->pquery($query,array($record));
                    $contractid = $adb->query_result($contractid, 0, 'contractid');
                    //更新合同表是否担保状态
                    $query="UPDATE vtiger_servicecontracts SET isguarantee=0 WHERE servicecontractsid= ? ";
                    $adb->pquery($query,array($contractid));
                    //冻结审核流程即让审核流程中的激活项转化成未激活状态 即状态改成0   如果此处修改 elseif 同改
                    $query="UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE  isaction = 1  AND salesorderid =? ";
                    $adb->pquery($query,array($record));
                    $data=array('result'=>true,'data'=>$contractid);
                }else{
                    $data=array('result'=>false,'data'=>$request->getAll(),'message'=>'请求参数错误');
                }
                //如果是采购合同
            }elseif($contractType=='SupplierContracts'){
                if($record>0){
                    // 更新合同担保表
                    $query=" UPDATE vtiger_contractguarantee SET modulestatus='c_cancel',username=?,userid=?,cancelguaranteeresason=?,cancelguaranteedate=? WHERE contractguaranteeid= ? ";
                    $adb->pquery($query,array($current_user->last_name,$current_user->id,$cancelguaranteeresason,date("Y-m-d H:i:s"),$record));
                    // 获取合同id
                    $query= "SELECT contractid FROM  vtiger_contractguarantee  WHERE contractguaranteeid = ? limit 1 ";
                    $contractid=$adb->pquery($query,array($record));
                    $contractid = $adb->query_result($contractid, 0, 'contractid');
                    //更新合同表是否担保状态
                    $query="UPDATE vtiger_suppliercontracts SET isguarantee=0 WHERE suppliercontractsid= ? ";
                    $adb->pquery($query,array($contractid));
                    //冻结审核流程即让审核流程中的激活项转化成未激活状态 即状态改成0
                    $query="UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE  isaction = 1  AND salesorderid =? ";
                    $adb->pquery($query,array($record));

                    $data=array('result'=>true,'data'=>$record);
                }else{
                    $data=array('result'=>false,'data'=>$request->getAll(),'message'=>'请求参数错误');
                }
            }
        }catch (Exception $e){
            $data = array('result'=>false,'data'=>$request->getAll(),'message'=>'未知异常重新尝试');
        }
        return array('data'=>$data);
    }
}
