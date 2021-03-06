<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class Newinvoice_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
        $taxtype=$request->get('taxtype');
        $invoicecompany = $request->get('invoicecompany');
        $contractid_display = $request->get('contractid_display');
        if($taxtype=='invoice') {
            $isaccountinvoice = $request->get('isaccountinvoice');
            $invoicefile = $request->get('invoicefile');
            if($isaccountinvoice=='yesneed' && empty($invoicefile) && $request->get('record')){
                echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>????????????invoice???????????????</h2><p class="text"></p><p class="links"><a id="button" href="javascript:history.go(-1);">??????</a></p></div>';
                exit;
            }
            if($invoicecompany!='?????????????????????????????????????????????' && $invoicecompany!='AMERICAN KAILILONG INTERNATIONAL HOLDING (H.K.) LIMITED'){
                echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>???????????????????????????</h2><p class="text"></p><p class="links"><a id="button" href="javascript:history.go(-1);">??????</a></p></div>';
                exit;
            }
            if(!strpos($contractid_display,'GG') && !strpos($contractid_display,'GOOGLE') && !strpos($contractid_display,'YANDEX')){
                echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>???????????????????????????</h2><p class="text"></p><p class="links"><a id="button" href="javascript:history.go(-1);">??????</a></p></div>';
                exit;
            }

        }
        $billingsourcedata=$request->get('billingsourcedata')?$request->get('billingsourcedata'):'contractsource';
        if($taxtype=='electronicinvoice') {
            if (!in_array($invoicecompany, array('????????????????????????????????????????????????', '??????????????????????????????????????????????????????', '???????????????????????????????????????????????????', '????????????????????????????????????', '????????????????????????????????????','?????????????????????????????????????????????????????????','?????????????????????????????????????????????????????????','????????????????????????????????????','????????????????????????????????????','????????????????????????????????????','????????????????????????????????????','?????????????????????????????????????????????????????????','???????????????????????????????????????','?????????????????????????????????????????????????????????'))) {
                $msginfo = "???????????????'????????????????????????????????????????????????','??????????????????????????????????????????????????????','???????????????????????????????????????????????????','????????????????????????????????????','????????????????????????????????????'???'?????????????????????????????????????????????????????????','?????????????????????????????????????????????????????????','????????????????????????????????????','????????????????????????????????????','????????????????????????????????????','????????????????????????????????????','?????????????????????????????????????????????????????????','???????????????????????????????????????','?????????????????????????????????????????????????????????',?????????????????????????????????????????????????????????????????????????????????";
                if($request->get('isFromMobile')){
                    return array('success'=>false,'msg'=>$msginfo);
                }
                echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>????????????</h2><p class="text">' . $msginfo . '</p><p class="links"><a id="button" href="javascript:history.go(-1);">??????</a></p></div>';
                exit;
            }
        }
		//print_r($_REQUEST);
        $db=PearDatabase::getInstance();
        // ???????????????????????????????????????????????????????????????
        //?????????????????????,?????????????????????????????????????????????????????????
        if(!empty($_REQUEST['receivedid'])){
            $receivedids=implode(',', $_REQUEST['receivedid']);
            $sql="SELECT sum(unit_price) AS sumcount FROM vtiger_receivedpayments WHERE receivedpaymentsid IN({$receivedids})";
            $resultdata=$db->pquery($sql,array());
            $result=$db->query_result($resultdata,0,'sumcount');
            if($request->get('record')>0 && empty($_REQUEST['taxtotal'])){
                $sql="SELECT vtiger_invoice.taxtotal FROM vtiger_invoice WHERE invoiceid=?";
                $resultdat=$db->pquery($sql,array($request->get('record')));
                $_REQUEST['taxtotal']=$db->query_result($resultdat,0,'taxtotal');
            }
            $_REQUEST['taxtotal']=str_replace(',','',$_REQUEST['taxtotal']);
            if($result!=$_REQUEST['taxtotal']){
                //echo '?????????????????????????????????<a href="javascript:history.go(-1);">??????</a>';
                //exit;
            }
        }
        if($billingsourcedata=='contractsource'&&$this->checkInvoicecompany($request)){
            if($request->get('isFromMobile')){
                return array('success'=>false,'msg'=>'????????????????????????????????????');
            }
            $this->showMsg('????????????????????????????????????!!');
            exit;
        }


        if(empty($request->get("inserti")) && $request->get("invoicetype")=='c_normal'){
            $request->set("servicecontractsid",$request->get('contractid'));
            $tyunWebRecordModel=TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
            $data = $tyunWebRecordModel->getAllowInvoiceTotal($request);
            if(!$data['success'] || $data['success'] && $data['allowTotal'] < 0) {
                if($request->get('isFromMobile')){
                    return array('success'=>false,'msg'=>'??????????????????????????????????????????');
                }
                $this->showMsg('??????????????????????????????????????????!!');
                exit;
            }
        }
        if($billingsourcedata=='contractsource'&&!$request->get('record')){
            $newInvocieRecordModel = Newinvoice_Record_Model::getCleanInstance("Newinvoice");
            //???????????????????????? ??????????????????
            if(!$newInvocieRecordModel->canSubmitVerifyInvoice($request->get('contractid'),$request->get('taxtotal'))){
                if($request->get('isFromMobile')){
                    return array('success'=>false,'msg'=>'?????????????????????????????????????????????????????????!!');
                }
                $this->showMsg('?????????????????????????????????????????????????????????!!');
                exit;
            }
        }

        //print_r($request->get('workflowsid'));

        //print_r($_REQUEST);die;
        //???????????????????????????????????????????????????,????????????????????????????????????

        /*
        if($_REQUEST['record']>0){
            $query='SELECT sum(unit_price) AS sumcount FROM vtiger_receivedpayments WHERE receivedpaymentsid IN (select receivedpaymentsid from vtiger_invoicerelatedreceive where invoiceid=?)';
            $resultdata=$db->pquery($query,array($_REQUEST['record']));
            $result=$db->query_result($resultdata,0,'sumcount');
            if($result!=0 && $result!=$_REQUEST['taxtotal']){
                echo '????????????????????????????????????????????????,????????????????????????????????????????????????????????????<a href="javascript:history.go(-1);">??????</a>';
                exit;
            }
        }*/
         //???????????????????????????6%?????????
        if(empty($_REQUEST['taxrate'])){
            $request->set('taxrate','6%');
        }
        //??????????????????????????????????????????
        if(!empty($_REQUEST['businessnamesone'])){
            $request->set('businessnames',$_REQUEST['businessnamesone']);
        }
        //print_r($_REQUEST);
        //exit;

		$recordModel = $this->saveRecord($request);

        if($request->get('isFromMobile')){
            return array('success'=>true,'msg'=>'????????????','invoiceid'=>$recordModel->getId());
        }

		$loadUrl = $recordModel->getDetailViewUrl();
		if(empty($loadUrl)){
			$loadUrl="index.php";
		}
		header("Location: $loadUrl");
	}
    public function checkInvoicecompany($request){
        $invoicecompany=$request->get('invoicecompany');
        if(empty($invoicecompany)){
            return true;
        }
        $servicecontractsid=$request->get('contractid');
        global $adb;
        $result1=$adb->pquery('SELECT 1 FROM vtiger_servicecontracts WHERE invoicecompany=? AND servicecontractsid=?',array($invoicecompany,$servicecontractsid));
        $result2=$adb->pquery('SELECT 1 FROM vtiger_suppliercontracts WHERE invoicecompany=? AND suppliercontractsid=?',array($invoicecompany,$servicecontractsid));
        if($adb->num_rows($result1) || $adb->num_rows($result2)){
            return false;
        }
        return true;
    }


    /**
     * ????????????
     * @param $msg
     */
    public function showMsg($msg){
        echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>????????????</h2><p class="text">'.$msg.'</p><p class="links"><a id="button" href="javascript:history.go(-1);">??????</a></p></div>';
    }

    public function saveRecord($request) {
        $recordModel = $this->getRecordModelFromRequest($request);
        $recordId=$request->get('record');
        $smownerid=0;
        $db='';
        if($recordId>0){
            $query='SELECT smownerid FROM vtiger_crmentity WHERE crmid=?';
            $db=$recordModel->getEntity()->db;
            $result=$db->pquery($query,array($recordId));
            $smownerid=$result->fields['smownerid'];
        }
        $recordModel->save();
        if($recordId>0 && $smownerid>0){
            $sql='UPDATE vtiger_crmentity SET smownerid=? WHERE crmid=?';
            $db->pquery($sql,array($smownerid,$recordId));
        }
//        if(count($request->get("attachmentsid"))>0){
//            $attachmentsids =  $request->get("attachmentsid");
//            $zizhifiles =  $request->get("zizhifile");
//            foreach ($attachmentsids as $attachmentsid){
//                $fileArray[$attachmentsid]=$zizhifiles[$attachmentsid];
//            }
//            $this->updateNewinvoiceZiZhiFile($recordModel->getId(),$fileArray);
//        }

        if(count($request->get("zizhifile"))>0){
            $zizhifiles =  $request->get("zizhifile");
            foreach ($zizhifiles as $k=>$v){
                $fileArray[$k]=$v;
            }
            $this->updateNewinvoiceZiZhiFile($recordModel->getId(),$fileArray);
        }

        if(count($request->get("invoicefile"))>0){
            $invoicefiles =  $request->get("invoicefile");
            foreach ($invoicefiles as $k=>$v){
                $fileArray[$k]=$v;
            }
            $this->updateNewinvoiceInvoiceFile($recordModel->getId(),$fileArray);
        }

        if($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
            $parentRecordId = $request->get('sourceRecord');
            $relatedModule = $recordModel->getModule();
            $relatedRecordId = $recordModel->getId();

            $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
            $relationModel->addRelation($parentRecordId, $relatedRecordId);
        }
        if($request->get('billingsourcedata')=='ordersource'&&$request->get('systemuser_id')){
            //?????????????????????????????????
            $orderList=$request->get('orderNo');
            $db=PearDatabase::getInstance();
            global $current_user;
            foreach ($orderList as $orderCode){
                $orderInfo=$this->getSystemUserOrderByCode($request->get('systemuser_id'),$orderCode);
                if($orderInfo){
                    $insertData['systemuserid']=$request->get('systemuser_id');
                    $insertData['systemuser']=$request->get('systemuser');
                    $insertData['invoiceid']=$recordModel->getId();
                    $insertData['paydate']=$orderInfo['PayDate'];
                    $insertData['paycode']=$orderInfo['PayCode'];
                    $insertData['ordercode']=$orderInfo['OrderCode'];
                    $insertData['adddate']=$orderInfo['AddDate'];
                    $insertData['producttype']=$orderInfo['ProductType'];
                    $insertData['producttitle']=$orderInfo['ProductTitle'];
                    $insertData['categoryid']=$orderInfo['CategoryID'];
                    $insertData['tradingstatus']=$orderInfo['TradingStatus'];
                    $insertData['money']=$insertData['invoicemoney']=$insertData['remainingmoney']=$orderInfo['Money'];
                    $insertData['payway']=$orderInfo['PayWay'];
                    $insertData['createtime']=date('Y-m-d H:i:s');
                    $insertData['createid']=$current_user->id;
                    $db->run_insert_data('vtiger_dongchaliorder',$insertData);
                }
            }
        }

        return $recordModel;
    }


    /**
     * ????????????code?????????????????????????????????
     */
    public function getSystemUserOrderByCode($systemuser_id,$orderCode){
        global $testtyunweburl;
        $sault='multiModuleProjectDirectoryasdafdgfdhggijfgfdsadfggiytudstlllkjkgff';
        $time=time().'123';
        $token=md5($time.$sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $postData = array(
            "userID"=>$systemuser_id,
            'pageIndex'=>0,
            'pageSize'=>1,
            'OrderCode'=>$orderCode,
            'tradingStatus'=>1
        );
        $url =$testtyunweburl.'api/Order/GetCanInvoiceOrderPageData';
        $res = json_decode($this->https_request($url, json_encode($postData),$curlset),true);
        $data=array();
        if($res['success']){
            if($res['recordsTotal']){
                $data=$res['data'][0];
            }
        }
        return $data;
    }

    /**
     * ?????????????????????
     * @param $url
     * @param null $data
     * @param array $curlset
     * @return bool|string
     */
    public function https_request($url, $data = null,$curlset=array()){
        $curl = curl_init();
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post????????????
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * ??????????????????
     */
    public function updateNewinvoiceZiZhiFile($invoiceid,$fileArray){
        global $adb;
        $str='';
        foreach ($fileArray as $fileId =>$fileName){
            $str.='*|*'.$fileName.'##'.$fileId;
        }
        $str=ltrim($str,'*|*');
        $sql="update vtiger_newinvoice set zizhifile=?  where invoiceid=?";
        $adb->pquery($sql,array($str,$invoiceid));
    }
    /**
     * ??????Invoice??????
     */
    public function updateNewinvoiceInvoiceFile($invoiceid,$fileArray){
        global $adb;
        $str='';
        foreach ($fileArray as $fileId =>$fileName){
            $str.='*|*'.$fileName.'##'.$fileId;
        }
        $str=ltrim($str,'*|*');
        $sql="update vtiger_newinvoice set invoicefile=?  where invoiceid=?";
        $adb->pquery($sql,array($str,$invoiceid));
    }
}
