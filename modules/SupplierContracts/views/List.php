<?php

class SupplierContracts_List_View  extends Vtiger_KList_View{

    function process (Vtiger_Request $request){
        $strPublic = $request->get('public');
        global $adb;
        if($strPublic == 'dempartConfirm') {//非标合同部门负责审核设置
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('SupplierContracts','dempartConfirm')){   //权限验证
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(" AND `status`='Active'"));
            $viewer->assign('RECOEDS',SupplierContracts_Record_Model::getAuditsettings(array('SupplierCAuditset','SupplierStatementCAuditset')));
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('dempartConfirm.tpl', $moduleName);
            exit;
        }elseif($strPublic=='Received' || $strPublic=='Returned' || $strPublic=='NoSignReturned' ||$strPublic=='notsign' ) {               //领取
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('SupplierContracts','Received')){   //权限验证
                return;
            }
            $arrStatus=array('Received'=>1,'Returned'=>2,'NoSignReturned'=>3,'notsign'=>4);
            $viewer = $this->getViewer($request);
            //$viewer->assign('DEPARTMENT',getDepartment());
            $viewer->assign('CSELECTED',$arrStatus[$strPublic]);
            $viewer->view('received.tpl', $moduleName);
            exit;
        }elseif($strPublic=='Export') {               //导出
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('SupplierContracts','Export')){   //权限验证
                return;
            }

            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('export.tpl', $moduleName);
            exit;
        }elseif($strPublic=='ExportRID') {               //导出
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('SupplierContracts','Export')){   //权限验证
                return;
            }
            $moduleModel->exportSuppData($request);
            exit;
        }elseif ($strPublic=='export'){// 导出归档

            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            $moduleModel->exportArchiveData($request);
            exit;
        }elseif($strPublic=='BatchArchive') {               //归档
            $viewer = $this->getViewer($request);
            $tips = 0;
            if($request->get('is_post') == 1){
                $company_code = $request->get('department');
                $start_time = $request->get('datatime');
                $end_time = $request->get('enddatatime');
                if(empty($company_code) || empty($start_time) || empty($end_time)){
                    $tips = 1;
                }else{
                    $db=PearDatabase::getInstance();
                    $sql = "select suppliercontractsid from vtiger_archive_log where status = 1 
                        and companycode = '".$company_code."' and ym <= '".$end_time."' and ym >= '".$start_time."' ";
                    $contractsid = $db->run_query_allrecords($sql);
                    $contractsid = array_column($contractsid, 'suppliercontractsid');
                    if(count($contractsid)){
                        $sql = "update vtiger_suppliercontracts set archive_status= 'archive_yes', archive_time= '".date('Y-m-d H:i:s')."'
                 where archive_status = 'archive_waiting' and suppliercontractsid in (".implode(",", $contractsid).")";
                        $db->query($sql);
                        $tips = 2;
                    }
                }
            }
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('SupplierContracts','BatchArchive')){   //权限验证
                return;
            }
            $invoicecompany = $adb->run_query_allrecords("SELECT * FROM vtiger_invoicecompany");
            $invoicecompany = array_column($invoicecompany, 'invoicecompany','companycode');

            $viewer->assign('DEPARTMENT',$invoicecompany);
            $viewer->assign('tips',$tips);
            $viewer->view('BatchArchive.tpl', $moduleName);
            exit;
        }elseif($strPublic=='ArchiveCode'){ // 归档编号图表生成
            $viewer = $this->getViewer($request);
            $rows = [];
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('SupplierContracts','BatchArchive')){   //权限验证
                return;
            }

            $invoicecompany = $adb->run_query_allrecords("SELECT * FROM vtiger_invoicecompany");
            $invoicecompany = array_column($invoicecompany, 'invoicecompany','companycode');

            if($request->get('is_post') == 1){
                $company_code = $request->get('department');
                $start_time = $request->get('datatime');
                $end_time = $request->get('enddatatime');
                $end_time_str = strtotime($end_time) + 24*3600;
                if(empty($company_code) || empty($start_time) || empty($end_time)){
                    $tips = 1;
                }else{
                    $sql = "select count(*) as code_active,min(code) as min_code, max(code) as max_code,ym 
                    from vtiger_archive_log   where companycode = '".$company_code."' 
                    and create_time > '".strtotime($start_time)."' and create_time < '".$end_time_str."' group by ym";
                    $res = $adb->run_query_allrecords($sql);
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
                }
            }

            $viewer->assign('rows',$rows);
            $viewer->assign('tips',$tips);
            $viewer->assign('DEPARTMENT',$invoicecompany);
            $viewer->view('ArchiveCode.tpl', $moduleName);
            exit;
        }elseif($strPublic=='supplierstatus') {               //导出
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('SupplierContracts','supplierstatus')){   //权限验证
                return;
            }
            $recordModel=Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(" AND `status`='Active'"));
            $viewer->assign('RECOEDS',$recordModel->getSettingStatus());

            $viewer->view('supplierstatus.tpl', $moduleName);
            exit;
        }
        parent::process($request);
    }
}
