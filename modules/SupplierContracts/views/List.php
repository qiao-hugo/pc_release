<?php

class SupplierContracts_List_View  extends Vtiger_KList_View{

    function process (Vtiger_Request $request){
        $strPublic = $request->get('public');
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