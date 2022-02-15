<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


class RefillApplication_ExportPDF_Action extends Inventory_ExportPDF_Action {
    function checkPermission(Vtiger_Request $request) {
        set_time_limit(0);
        $record=$request->get('record');
        if($record){
            $recordModel=Vtiger_Record_Model::getInstanceById($record,'RefillApplication');
            if(!empty($recordModel)&&$recordModel){
                $module=$recordModel->entity->column_fields;
                $moduleStatus=$module['modulestatus'];
                $statusArray=array('c_cancel','a_exception');
                if(in_array($moduleStatus,$statusArray)){
                    echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:14px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>无法操作</h2><p class="text">当前状态不允许操作!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                    exit;
                }
                /*if($module['rechargesource']!='PreRecharge'){
                    echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:14px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>无法操作</h2><p class="text">当前状态不允许操作!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                    exit;
                }*/
                $title="充值审请单";
                /*if($recordModel->get('financialstate')==1){
                    $recordModel->set('grossadvances',0.00);
                    $recordModel->set('totalrecharge',$recordModel->get('actualtotalrecharge'));
                }*/
                $str=$this->setDataPreRechargeDisplay($request,$recordModel);
                $str.=$this->showWorkFlow($record);
                /*echo $str;
                exit;*/
                $this->printpdf($str,$title,$recordModel);
            }else{
                echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:14px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>无法操作</h2><p class="text">当前状态不允许操作!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                exit;
            }
        }


        exit;

    }
    public function printpdf($html,$title,$recordModel){
        set_time_limit(0);
        global $root_directory;
        $user = new Users();
        $current_userT = $user->retrieveCurrentUserInfoFromFile($recordModel->get('assigned_user_id'));
        //$current_userT = $user->retrieveCurrentUserInfoFromFile(15);
        include 'crmcache/departmentanduserinfo.php';
        $createdtime=$recordModel->get('createdtime');
        $rserialnumber=$recordModel->get('refillapplicationno');
        //$rserialnumber=$recordModel->get('rserialnumber');
        //$rserialnumber=str_pad($rserialnumber,8,0,STR_PAD_LEFT);
        $font_family='stsongstdlight';//设置字体

        //require_once($root_directory.'modules/Compensation/actions/tcpdf/examples/tcpdf_include.php');
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, 'pt', PDF_PAGE_FORMAT, true, 'UTF-8', true);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('胡阿有');
        $pdf->SetTitle($title);
        $pdf->SetSubject('who are you');
        $pdf->SetKeywords('胡阿有');
        $pdf->SetMargins(20, 100);
        $pdf->SetHeaderMargin(60);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->setPrintHeader(true);
        $pdf->setHeightCells(array(0, 0, '采购充值申请单', $border=0, $align='C',$fill=false, $ln=1, $x=0, $y=30,  $reseth=true, $stretch=0,$ishtml=false, $autopadding=true, $maxh=0, $valign='M', $fitcell=false),array('B',20));
        $pdf->setHeightCells(array(0, 0, '第 '.$rserialnumber.' 号', $border=0, $align='C',$fill=false, $ln=1, $x=250, $y=38,  $reseth=true, $stretch=0,$ishtml=false, $autopadding=true, $maxh=0, $valign='M', $fitcell=false),array('',10));
        $pdf->setHeightCells(array(0, 0, '申请人: '.$current_userT->last_name, $border=0, $align='L',$fill=false, $ln=1, $x=30, $y=60,  $reseth=true, $stretch=0,$ishtml=false, $autopadding=true, $maxh=0, $valign='M', $fitcell=false),array('',10));
        $pdf->setHeightCells(array(0, 0, '申请部门: '.$cachedepartment[$current_userT->departmentid], $border=0, $align='L',$fill=false, $ln=1, $x=230, $y=60,  $reseth=true, $stretch=0,$ishtml=false, $autopadding=true, $maxh=0, $valign='M', $fitcell=false),array('',10));
        $pdf->setHeightCells(array(0, 0, '申请日期: '.$createdtime, $border=0, $align='L',$fill=false, $ln=1, $x=430, $y=60,  $reseth=true, $stretch=0,$ishtml=false, $autopadding=true, $maxh=0, $valign='M', $fitcell=false),array('',10));
        //$pdf->setHeaderData('', $lw=0, $ht='', $hs='', $tc=array(0,0,0), $lc=array(0,0,0));
        //$pdf->SetFont($font_family, '', 7,'',true);

        $pdf->AddPage('default','A4');
        //$pdf->SetFont($font_family, 'B',20,'',true);
        //$pdf->MultiCell(0, 0, '采购充值申请单', $border=0, $align='C',$fill=false, $ln=1, $x=0, $y=30,  $reseth=true, $stretch=0,$ishtml=false, $autopadding=true, $maxh=0, $valign='M', $fitcell=false);
        //$pdf->SetFont($font_family, '',10,'',true);
        //$pdf->MultiCell(0, 0, '第 '.$rserialnumber.' 号', $border=0, $align='C',$fill=false, $ln=1, $x=220, $y=38,  $reseth=true, $stretch=0,$ishtml=false, $autopadding=true, $maxh=0, $valign='M', $fitcell=false);
        //$pdf->SetFont($font_family, 'B',10,'',true);
        //$pdf->MultiCell(0, 0, '申请人: '.$current_userT->last_name, $border=0, $align='L',$fill=false, $ln=1, $x=30, $y=60,  $reseth=true, $stretch=0,$ishtml=false, $autopadding=true, $maxh=0, $valign='M', $fitcell=false);
        //$pdf->MultiCell(0, 0, '申请部门: '.$cachedepartment[$current_userT->departmentid], $border=0, $align='L',$fill=false, $ln=1, $x=230, $y=60,  $reseth=true, $stretch=0,$ishtml=false, $autopadding=true, $maxh=0, $valign='M', $fitcell=false);
        //$pdf->MultiCell(0, 0, '申请日期: '.$createdtime, $border=0, $align='L',$fill=false, $ln=1, $x=430, $y=60,  $reseth=true, $stretch=0,$ishtml=false, $autopadding=true, $maxh=0, $valign='M', $fitcell=false);

        $pdf->SetFont($font_family, '', 8,'',true);
        $pdf->writeHTMLCell(0, 0, 30, 80, $html, 0, 1, 0, true, '', true);
        $pdf->Output('whoareyou_001'.time().'.pdf', 'I');
    }
    /*public function setDataPreRechargeDisplayBak(Vtiger_Request $request,$recordModel){
        $record=$request->get('record');
        global $adb;
        $depositbank=$recordModel->get('depositbank');//开户行
        $bankname=$recordModel->get('bankname');//开户名
        $accountnumber=$recordModel->get('accountnumber');//账号
        $query="SELECT vtiger_rechargesheet.*,vtiger_products.productname FROM vtiger_rechargesheet LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_rechargesheet.productid WHERE vtiger_rechargesheet.deleted=0 AND refillapplicationid=?";
        $result=$adb->pquery($query,array($record));

        $str='<table border="1" align="center" cellpadding="0" cellspacing="0">
        <tr align="center" valign="middle">
            <td rowspan="2" colspan="2" align="center" valign="middle" style="font-size:12px;line-height: 400%;width:30%;">
                产品服务
            </td>
            <td colspan="10"  style="line-height: 200%;width:40%;font-size:12px;">
                充值金额
            </td>
            <td rowspan="2" style="line-height: 400%;width:15%;font-size:12px;">
                返点
            </td>
            <td rowspan="2" style="line-height: 400%;width:15%;font-size:12px;">
                充值币金额
            </td>
        </tr>
        <tr>
            <td style="width:4%;line-height: 80%;font-size:12px;">
                仟
            </td>
            <td style="width:4%;line-height: 80%;font-size:12px;">
                佰
            </td>
            <td style="width:4%;line-height: 80%;font-size:12px;">
                拾
            </td>
            <td style="width:4%;line-height: 80%;font-size:12px;">
                万
            </td>
            <td style="width:4%;line-height: 80%;font-size:12px;">
                仟
            </td>
            <td style="width:4%;line-height: 80%;font-size:12px;">
                佰
            </td>
            <td style="width:4%;line-height: 80%;font-size:12px;">
                十
            </td>
            <td style="width:4%;line-height: 80%;font-size:12px;">
                元
            </td>
            <td style="width:4%;line-height: 80%;font-size:12px;">
                角
            </td>
            <td style="width:4%;line-height: 80%;font-size:12px;">
                分
            </td>
        </tr>';
        $sumrechargeamount=0;
        $sumprestoreadrate=0;
        $mstatus='';
        while($row=$adb->fetch_array($result)) {
            $sumprestoreadrate+=$row['prestoreadrate'];
            $sumrechargeamount+=$row['rechargeamount'];
            $str .= '<tr>
                <td style="width:30%;line-height: 200%;font-size: 12px;">
                    '.$row['productname'].'
                </td>'
                .$this->setFormatdataTable($row['rechargeamount']).
                '<td style="width:15%;line-height: 200%;font-size: 12px;">
                    '.$row['discount'].'%
                </td>
                <td  align="right" style="width:15%;line-height: 200%;font-size: 12px;">
                    '.$row['prestoreadrate'].'
                </td>
            </tr>';
            $mstatus.=$row['mstatus'].'<br>';
        }
        $query='SELECT vtiger_salesorderworkflowstages.*,(select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',(if(`status`=\'Active\',\'\',\'[离职]\'))) as last_name from vtiger_users where vtiger_salesorderworkflowstages.auditorid=vtiger_users.id) as auditoridname 
                FROM `vtiger_salesorderworkflowstages` 
                WHERE salesorderid=? ORDER BY sequence;';
        $workResult=$adb->pquery($query,array($record));
        $workMsg='';
        while($row=$adb->fetch_array($workResult)){
            $workMsg.=$row['workflowstagesname'].': '.$row['auditoridname'].'  '.$row['auditortime'].'<br>';
        }
        $str.='<tr>
            <td style="width:30%;text-align: left;line-height: 200%;font-size: 12px;">
                合计:(大写)'.$this->toChineseNumber($sumrechargeamount).'
            </td>
            '.$this->setFormatdataTable($sumrechargeamount).'
            
            <td colspan="2" align="right" style="width:30%;line-height: 200%;font-size: 12px;">
                '.$sumprestoreadrate.'
            </td>
        </tr>';
        $str.='<tr>
            <td style="text-align: left;line-height: 200%;font-size: 10px;">
                账户名:'.$bankname.'
            </td>
            <td colspan="10" style="text-align: left;line-height: 200%;font-size: 10px;">
                账号:'.$accountnumber.'
            </td>
            <td colspan="2" style="text-align: left;line-height: 200%;font-size: 10px;">
                开户行:'.$depositbank.'
            </td>
        </tr>
        <tr>
            
            <td colspan="13" align="left" style="text-align: left;line-height: 150%;border:#000 solid 10px;font-size: 12px;">
                <table border="0" align="center" cellpadding="0" cellspacing="0">
                <tr><td style="text-align: left;line-height: 50%;"></td></tr>
                <tr><td width="1%"></td><td align="left" width="98%;" style="text-align: left;font-size: 12px;">备注：'.$mstatus.'</td><td width="1%"></td></tr>
                <tr><td style="text-align: left;line-height: 50%;"></td></tr>
                </table>
                
            </td>
        </tr>
        <tr>
            <td colspan="13" align="left" style="">
             <table border="0" align="center" cellpadding="0" cellspacing="0">
                <tr><td style="text-align: left;line-height: 50%;"></td></tr>
                <tr><td width="1%"></td><td align="left" width="98%;"style="text-align: left;line-height: 150%;font-size: 12px;">'.$workMsg.'</td><td width="1%"></td></tr>
                <tr><td style="text-align: left;line-height: 50%;"></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="13" align="left" valign="middle" style="font-size: 12px;line-height: 150%;">
                注意：此费用报销单填写后部门经理审核前需将相对应的补充资料或者发票送至财务部；
            </td>
        </tr>
    </table>';
        return $str;

    }
    public function setFormatdataTable($rechargeamount){
        $temp=array_fill(0,7,'');
        $pointtemp=array_fill(0,2,0);
        $rechargeamountarr=split('\.',$rechargeamount);
        if(!empty($rechargeamountarr[1])){
            $pointtempdata=strrev($rechargeamountarr[1]);
            $pointtemp=str_split($pointtempdata);
        }
        $tempdata=strrev($rechargeamountarr[0]);
        $lengthtemp=strlen($tempdata);

        for($i=0;$i<$lengthtemp;$i++){
            $temp[$i]=$tempdata[$i];
        }
        $str = '<td style="width:4%;line-height: 200%;font-size: 12px;">
                    '.$temp[7].'
                </td>
                <td style="width:4%;line-height: 200%;font-size: 12px;">
                    '.$temp[6].'
                </td>
                <td style="width:4%;line-height: 200%;font-size: 12px;">
                    '.$temp[5].'
                </td>
                <td style="width:4%;line-height: 200%;font-size: 12px;">
                    '.$temp[4].'
                </td>
                <td style="width:4%;line-height: 200%;font-size: 12px;">
                    '.$temp[3].'
                </td>
                <td style="width:4%;line-height: 200%;font-size: 12px;">
                    '.$temp[2].'
                </td>
                <td style="width:4%;line-height: 200%;font-size: 12px;">
                    '.$temp[1].'
                </td>
                <td style="width:4%;line-height: 200%;font-size: 12px;">
                    '.$temp[0].'
                </td>
                <td style="width:4%;line-height: 200%;font-size: 12px;">
                    '.$pointtemp[1].'
                </td>
                <td style="width:4%;line-height: 200%;font-size: 12px;">
                    '.$pointtemp[0].'
                </td>
               ';
        return $str;
    }
    public function toChineseNumber($money){
        $money = round($money,2);
        $cnynums = array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖");
        $cnyunits = array("圆","角","分");
        $cnygrees = array("拾","佰","仟","万","拾","佰","仟","亿");
        list($int,$dec) = explode(".",$money,2);
        $cnyzheng=empty($dec)?'整':'';
        $dec = array_filter(array($dec[1],$dec[0]));
        $ret = array_merge($dec,array(implode("",$this->cnyMapUnit(str_split($int),$cnygrees)),""));
        $ret = implode("",array_reverse($this->cnyMapUnit($ret,$cnyunits)));
        return str_replace(array_keys($cnynums),$cnynums,$ret).$cnyzheng;
    }
    public function cnyMapUnit($list,$units) {
        $ul=count($units);
        $xs=array();
        foreach (array_reverse($list) as $x) {
            $l=count($xs);
            if ($x!="0" || !($l%4))
                $n=($x=='0'?'':$x).($units[($l-1)%$ul]);
            else $n=is_numeric($xs[0][0])?$x:'';
            array_unshift($xs,$n);
        }
        return $xs;
    }*/
    public function setDataPreRechargeDisplay(Vtiger_Request $request,$recordModel){
        $recordId = $request->get('record');
        $moduleName = 'RefillApplication';
        global $isallow,$current_user;
        if(in_array($moduleName, $isallow)){
            //echo $this->getWorkflowsM($request);
        }
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
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
        $rechargesource=($rechargesource=='Accounts')?'Accounts':(($rechargesource=='Vendors')?'Vendors':
            ($rechargesource=='TECHPROCUREMENT'?'TECHPROCUREMENT':
                ($rechargesource=='PreRecharge'?'PreRecharge':
                    ($rechargesource=='OtherProcurement'?'OtherProcurement':
                        ($rechargesource=='NonMediaExtraction'?'NonMediaExtraction':
                            ($rechargesource=='PACKVENDORS'?'PACKVENDORS':
                                ($rechargesource=='COINRETURN'?'COINRETURN':
                                    ($request->get('rechargesource')=='INCREASE' || $rechargesource=='INCREASE')?'INCREASE':
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
        if($rechargesource=='COINRETURN') {
            if ($rechargeSheetCount > 2) {
                $incount = 0;
                $rechargeSheetCount = 1;
                $inarray = array();
                $outarray = array();
                foreach ($rechargeSheet as $value) {
                    if ($value['turninorout'] == 'in') {
                        $value['seqnum'] = ++$incount;
                        $inarray[] = $value;
                    } else {
                        $value['seqnum'] = ++$rechargeSheetCount;
                        $outarray[] = $value;

                    }
                }
                $rechargeSheet = array_merge($outarray, $inarray);
            } else {
                $rechargeSheetCount = 1;
                $rechargeSheet[0]['seqnum'] = 1;
            }
        }
        $viewer->assign('INCOUNT', $incount);
        $viewer->assign('RECHARGESHEETCOUNT', $rechargeSheetCount);
        $viewer->assign('C_RECHARGESHEET', $rechargeSheet);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        return $viewer->view('DetailViewBlockViewPrint.tpl', $moduleName, true);
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

