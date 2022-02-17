<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Staypayment_List_View extends Vtiger_KList_View {
    function process (Vtiger_Request $request)
    {

        $strPublic = $request->get('public');
        $bugArray=$request->get('BugFreeQuery');
        if ($strPublic == 'Export') {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->view('exportri.tpl', $moduleName);
            exit;
        }else if($strPublic == 'Delay'){
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
            $viewer->assign('MODULE_MODEL', $moduleModel);
            $viewer->view('ListViewContentsDelay.tpl', $moduleName);
        }else if($strPublic == 'delayExport'){
            set_time_limit(0);
            ini_set('memory_limit',-1);
            global $root_directory,$site_URL,$adb,$current_user;
            $path=$root_directory.'temp/';
            $filename = '代付款延迟签收导出';
            $filename = (strtolower(substr(PHP_OS,0,3))=='win') ? mb_convert_encoding($filename,'gbk','UTF-8') : $filename;
            $filename=$path.$filename.date('Ymd').$current_user->id.'.csv';
            !is_dir($path)&&mkdir($path,'0777',true);
            @unlink($filename);
            $viewer = $this->getViewer($request);
            $this->viewName = $request->get('viewname');
            $viewer->assign('VIEWNAME', $this->viewName);
            $_REQUEST['pageDelay']=1;
            $_REQUEST['limitDelay']=100000;
            $this->initializeListViewContents($request, $viewer);
            $listViewRecordModels=$this->listViewEntries;
            Matchreceivements_Record_Model::recordLog('1','exportstay');
            $fp=fopen($filename,'w');
            $array=array_map(function ($value){
                return iconv('utf-8','gb2312',$value);
            },array('代付款编号','合同编号','合同客户名称','打款人全称','签订代付款金额','代付款已使用金额','代付款剩余金额','首次回款匹配时间','代付款最晚签收时间','代付款签收时间','代付款状态','是否延期','是否模拟新建'));
            fputcsv($fp,$array);
            if(!empty($listViewRecordModels)){
                foreach($listViewRecordModels as $key=>$value){
                    if($value['modulestatus'] != 'c_complete'){
                        $value['workflowstime']='';
                        $value['status']='未签收';
                    }else{
                        $value['status']='已签收';
                    }
                    if($value['modulestatus']=='c_complete'&&$value['last_sign_time']&&(strtotime(date('Y-m-d H:i:s'))-strtotime($value['last_sign_time']))>0){
                        $value['isdelay']='是';
                    }else{
                        $value['isdelay']='否';
                    }
                    if($value['isauto'] == 1){
                        $value['isauto']='是';
                    }else{
                        $value['isauto']='否';
                    }
                    $newValue=array($value['staymentcode'],$value['contractid'],$value['accountid'],$value['payer'],$value['staypaymentjine'],$value['staypaymentjine']-$value['surplusmoney'],$value['surplusmoney'],$value['changetime'],$value['last_sign_time'],$value['workflowstime'],$value['status'],$value['isdelay'],$value['isauto']);
                    $newValue=array_map(function ($val){
                        return iconv('utf-8','gb2312',$val)."\t";
                    },$newValue);
                    fputcsv($fp,$newValue);
                }
            }
            fclose($fp);
            $response = new Vtiger_Response();
            $response->setResult(array('flag'=>true,'msg'=>'temp/代付款延迟签收导出'.date('Ymd').$current_user->id.'.csv'));
            $response->emit();
        }else{
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
            $viewer->assign('MODULE_MODEL', $moduleModel);

            if($bugArray&&$bugArray['cpublic']=='ExportAll'){
                $listViewEntries=$this->listViewEntries;
                foreach ($listViewEntries as $listViewEntriy){
                    $sql=$listViewEntriy['sql'];
                    break;
                }
                $request->set('sql',$sql);
                $basicObject=new Staypayment_BasicAjax_Action();
                $basicObject->export($request);
            }
            $viewer->view('ListViewContents.tpl', $moduleName);
        }
    }

    function preProcessTplName(Vtiger_Request $request=null) {
        $strPublic = $request->get('public');
        if($strPublic == 'Delay'){
            return 'ListViewPreProcessDelay.tpl';
        }
        return 'ListViewPreProcess.tpl';
    }
}