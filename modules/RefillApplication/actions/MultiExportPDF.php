<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


class RefillApplication_MultiExportPDF_Action extends Inventory_ExportPDF_Action {
    function checkPermission(Vtiger_Request $request) {
        $record=$request->get('records');
        if(!empty($record)){
            $records=explode(',',$record);
                $font_family='stsongstdlight';//设置字体

                $pdf = new MYPDF(PDF_PAGE_ORIENTATION, 'pt', PDF_PAGE_FORMAT, true, 'UTF-8', true);

                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('whoareyou');
                $pdf->SetTitle('充值审请单');
                $pdf->SetSubject('who are you');
                $pdf->SetKeywords('whoareyou');
                $pdf->SetMargins(20, 100);
                $pdf->SetHeaderMargin(60);
                $pdf->setPrintFooter(false);
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                $pdf->setPrintHeader(true);
            include 'crmcache/departmentanduserinfo.php';
            $temparray=array();
            foreach($records as $value){
                if(is_numeric($value)&& !in_array($value,$temparray)){
                    try{
                        $temparray[]=$value;
                        $recordModel=Vtiger_Record_Model::getInstanceById($value,'RefillApplication',true);
                        $request->set('record',$value);
                        /*if($recordModel->get('financialstate')==1){
                            $recordModel->set('grossadvances',0.00);
                            $recordModel->set('totalrecharge',$recordModel->get('actualtotalrecharge'));
                        }*/
                        $module=$recordModel->entity->column_fields;
                        $moduleStatus=$module['modulestatus'];
                        $statusArray=array('c_cancel','a_exception');
                        if(in_array($moduleStatus,$statusArray)){
                            continue;
                        }
                        if($module['assigned_user_id']<1){
                            continue;
                        }
                        $user = new Users();
                        $current_userT = $user->retrieveCurrentUserInfoFromFile($module['assigned_user_id']);
                        $createdtime=$module['createdtime'];
                        $rserialnumber=$module['refillapplicationno'];
                        $title='<table border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr><td align="left" style="text-align: left;line-height: 150%;font-size: 12px;" colspan="3">&nbsp;</td></tr>
                                <tr><td align="left" style="text-align: center;line-height: 150%;font-size: 16px;font-weight:bold;" align="center" colspan="3">&nbsp;充值申请单<span style="font-size: 10px;">第'.$rserialnumber.'号</span></td></tr>
                                <tr><td align="left" style="text-align: left;line-height: 150%;font-size: 12px;">&nbsp;申请人:'.$current_userT->last_name.'</td>
                                <td align="left" style="text-align: left;line-height: 150%;font-size: 12px;">&nbsp;申请部门:'.$cachedepartment[$current_userT->departmentid].'</td>
                                <td align="left" style="text-align: left;line-height: 150%;font-size: 12px;">&nbsp;申请时间:'.$createdtime.'</td>
                                </tr>
                                </table>';

                        $str=$this->setDataPreRechargeDisplay($request,$recordModel);
                        $str.=$this->showWorkFlow($value);
                        $pdf->AddPage('default','A4');
                        $pdf->SetFont($font_family, '', 8,'',true);
                        $pdf->writeHTMLCell(0, 0, 30, 10, $title, 0, 1, 0, true, '', true);

                        $pdf->writeHTMLCell(0, 0, 30, 80, $str, 0, 1, 0, true, '', true);
                    }catch (Exception $e){
                    }
                }
            }
            $pdf->Output('whoareyou_001'.time().'.pdf', 'I');
        }

        exit;

    }
    public function printpdf($html,$title,$recordModel){
        set_time_limit(0);

    }
    public function setDataPreRechargeDisplay(Vtiger_Request $request,$recordModel){
        //$recordId = $request->get('record');
        $recordId = $recordModel->getId();
        $moduleName = 'RefillApplication';
        global $isallow,$current_user;
        if(in_array($moduleName, $isallow)){
            //echo $this->getWorkflowsM($request);
        }
        //if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        //}
        $recordModel = $this->record->getRecord();
        $detailView=new RefillApplication_Detail_View();
        $detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
        $detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
        $moduleModel = $recordModel->getModule();

        $modulestatus=$recordModel->get('modulestatus');
        $refundsOrTransfers=($modulestatus=='c_complete'&&
            $moduleModel->exportGrouprt('RefillApplication',"dobackwash")
            //$current_user->id==$recordModel->get('assigned_user_id')
        )?true:false;
        $receiveStatus=($modulestatus=='c_complete'&&
            $current_user->id==$recordModel->get('assigned_user_id')&&
            $recordModel->get('actualtotalrecharge')>$recordModel->get('totalrecharge')
        )?true:false;
        $rechargesource=$recordModel->get('rechargesource');
        $rechargesource=($rechargesource=='Vendors')?'Vendors':
            ($rechargesource=='TECHPROCUREMENT'?'TECHPROCUREMENT':
                ($rechargesource=='PreRecharge'?'PreRecharge':
                    ($rechargesource=='OtherProcurement'?'OtherProcurement':
                        ($rechargesource=='NonMediaExtraction'?'NonMediaExtraction':
                            ($rechargesource=='PACKVENDORS'?'PACKVENDORS':
                                ($rechargesource=='COINRETURN'?'COINRETURN':
                                    ($rechargesource=='INCREASE'?'INCREASE':
                                        'Accounts')))))));
        $detailDisplayList=array('productid'=>'topplatform','suppliercontractsid'=>'suppliercontractsname');
        $checkValueList=array('havesignedcontract','isprovideservice');

        $viewer = $detailView->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_SUMMARY', $detailView->showModuleSummaryView($request));
        $viewer->assign('RECHARGESOURCE', $rechargesource);

        $viewer->assign('DISPLAYFIELD', array_keys($detailDisplayList));
        $viewer->assign('DISPLAYVALUE', $detailDisplayList);
        $viewer->assign('CHECKVALUELIST', $checkValueList);
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_AJAX_ENABLED', $detailView->isAjaxEnabled($recordModel));
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAYMENTSLIST', $recordModel->getMatchPaymentsList());
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $viewer->assign('REFUNDSORTRANSFERSTUTAS', $refundsOrTransfers);
        $viewer->assign('RECEIVESTATUS', $receiveStatus);
        if($rechargesource=='PACKVENDORS'){
            $viewer->assign('VENDORLIST',$recordModel->getDetailVendorList($recordId));
        }
        $rechargeSheet=RefillApplication_Record_Model::getRechargeSheet($recordModel->entity->column_fields['record_id']);
        $rechargeSheetCount=count($rechargeSheet);
        $rechargeSheetCount+=1;
        $incount=1;
        if($rechargesource=='COINRETURN'){
            if($rechargeSheetCount>2) {
                $incount = 0;
                $rechargeSheetCount = 1;
                $inarray = array();
                $outarray = array();
                foreach ($rechargeSheet as $value) {
                    if ($value['turninorout'] == 'in') {
                        $value['seqnum']=++$incount;
                        $inarray[] = $value;
                    } else {
                        $value['seqnum']=++$rechargeSheetCount;
                        $outarray[] = $value;

                    }
                }
                $rechargeSheet = array_merge($outarray, $inarray);
            }else{
                $rechargeSheetCount=1;
                $rechargeSheet[0]['seqnum']=1;
            }
        }
        if($rechargesource=='COINRETURN'){
            $tempdata['vendorid']=$structuredValues['VENDOR_LBL_INFO']['vendorid'];
            $tempposition=array_search('servicecontractsid',array_keys($structuredValues['LBL_INFO']));
            $firstArray=array_splice($structuredValues['LBL_INFO'],0,$tempposition+1);
            $structuredValues['LBL_INFO']=array_merge($firstArray,$tempdata,$structuredValues['LBL_INFO']);
        }
        $viewer->assign('C_RECHARGESHEET', $rechargeSheet);
        $viewer->assign('INCOUNT', $incount);
        $viewer->assign('RECHARGESHEETCOUNT', $rechargeSheetCount);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $outData=$viewer->view('DetailViewBlockViewPrint.tpl', $moduleName, true);
        $res = preg_replace("/<a[^>]*>(.*?)<\/a>/is", "$1", $outData);
        return $res;
    }
    public function showWorkFlow($record){
        global $adb;
        $query='SELECT vtiger_salesorderworkflowstages.*,(select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',(if(`status`=\'Active\',\'\',\'[离职]\'))) as last_name from vtiger_users where vtiger_salesorderworkflowstages.auditorid=vtiger_users.id) as auditoridname 
                FROM `vtiger_salesorderworkflowstages` 
                WHERE salesorderid=? ORDER BY sequence;';
        $workResult=$adb->pquery($query,array($record));
        $workMsg='';
        if($adb->num_rows($workResult)){
            while($row=$adb->fetch_array($workResult)){
                $workMsg.='<tr><td align="left" style="text-align: left;line-height: 150%;font-size: 12px;">&nbsp;'.$row['workflowstagesname'].': '.$row['auditoridname'].'  '.$row['auditortime'].'</td></tr>';
            }
            return '
             <table border="1" align="center" cellpadding="0" cellspacing="0">
                '.$workMsg.'</table>
            ';
        }
        return '';
    }

}
require_once($root_directory.'modules/Compensation/actions/tcpdf/tcpdf.php');
class MYPDF extends TCPDF {
    private $headerData=array();
    private $fontArray=array();
    //Page header
    public function Header() {
        //$this->SetFont('stsongstdlight', 'B', 20);
        foreach($this->headerData as $key=>$value){
            $w=empty($value[0])?0:$value[0];
            $h=empty($value[1])?0:$value[1];
            $txt=empty($value[2])?'':$value[2];
            $border=empty($value[3])?0:$value[3];
            $align=empty($value[4])?'J':$value[4];
            $fill=empty($value[5])?false:$value[5];
            $ln=empty($value[6])?1:$value[6];
            $x=empty($value[7])?'':$value[7];
            $y=empty($value[8])?'':$value[8];
            $reseth=empty($value[9])?true:$value[9];
            $stretch=empty($value[10])?0:$value[10];
            $ishtml=empty($value[11])?false:$value[11];
            $autopadding=empty($value[12])?true:$value[12];
            $maxh=empty($value[13])?0:$value[13];
            $valign=empty($value[14])?'T':$value[14];
            $fitcell=empty($value[15])?false:$value[15];
            $style=$this->fontArray[$key][0];
            $size=$this->fontArray[$key][1];
            $this->SetFont('stsongstdlight',$style,$size);
            $this->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        }

    }
    public function setHeightCells($arr,$font){
        $this->headerData[]=$arr;
        $this->fontArray[]=$font;
        //$this->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link, $stretch, $ignore_min_height, $calign, $valign);
    }

}

