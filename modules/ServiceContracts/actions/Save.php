<?php
/* +****************
 *合同保存验证
 *新增产品必填，打回后产品不可编辑
 * ******************* */

class ServiceContracts_Save_Action extends Vtiger_Save_Action {
    private $elecContractWorkflowsid=372;

	public function saveRecord($request) {
        $is_collate = $request->get('is_collate');
        //判断是否是核对编辑
	    if ($is_collate) {
            if ($request->get('total') <= 0) {
                $this->showMsg('合同“总额”必需大于0！');
                exit;
            }
            if ($_REQUEST['productid'] != "") {
                $productid = implode(',', $_REQUEST['productid']);
                $request->set('productid', $productid);
            } else {  //复选框产品为空时
                $request->set('productid', "");
            }
            if ($_REQUEST['extraproductid'] != "") {
                $productid = implode(',', $_REQUEST['extraproductid']);
                $request->set('extraproductid', $productid);
            } else {  //复选框产品为空时
                $request->set('extraproductid', "");
            }
            $recordModel = $this->getRecordModelFromRequest($request);
            $this->setRequestData();
            $adb = PearDatabase::getInstance();
            $productnamestr = '';
            $tempproductname = $request->get('productname');
            if (!empty($tempproductname)) {
                $productnamestr .= implode(',', $request->get('productname'));
            }
            $recordModel->set('productname', $productnamestr);
            $recordModel->save();

            //更新产品类型
            if ($request->get("categoryid") || $request->get("categoryid") === '0') {
                $adb->pquery("UPDATE vtiger_servicecontracts SET categoryid=? WHERE servicecontractsid=?", array($request->get("categoryid"), $recordModel->getId()));
            }
            //修改合同应收里面的合同金额和逾期合同金额
            $adb->pquery("update vtiger_contract_receivable set contracttotal=? where contractid=?", array($request->get('total'), $recordModel->getId()));
            $adb->pquery("update vtiger_receivable_overdue set contracttotal=? where contractid=?", array($request->get('total'), $recordModel->getId()));

            return $recordModel;
        } else {
            global $configcontracttypeNameTYUN;
            /* echo $request->get('modulestatus')."<br>";
            echo $request->get('parent_contracttypeid')."<br>";
            echo $request->get('contract_type');
            echo "<pre>";
            print_r($request);
            echo "</pre>";
            exit; */
            //echo 11;print_r($request);print_r(implode(',',$_REQUEST['productid']));exit;
            $modulestatus = $request->get('modulestatus');
            $checkedproductid = $request->get('productid');//是否是T云
            $servicecontractstype = $request->get('servicecontractstype');//是否是续费
            $parent_contracttypeid = $request->get('parent_contracttypeid');//前面类型
            $contract_type = $request->get('contract_type');//类型二级菜单
            $receiveid = $_REQUEST['assigned_user_id'];
            $iscomplete = $request->get('iscomplete');
            $signaturetype = $request->get('signaturetype');
            if ($signaturetype == 'eleccontract' && $parent_contracttypeid == 2) {
                $this->showMsg('T云电子合同请到移动端下单');
                exit;
            }
            if ($request->get('total') < 1 && $request->get('frameworkcontract') == 'no') {
                $this->showMsg('合同“总额”必需大于0！');
                exit;
            }

            /**
             * 判断是否有资质
             */
//            $isNeedFlag = false;
//            if ($request->get('sc_related_to') && $request->get('needZizhi') == 'yes') {
//                //非框架合同和有客户id的判断是否有资质
//                $basicAjaxObject = new ServiceContracts_BasicAjax_Action();
//                $isNeed = $basicAjaxObject->isNeedZizhiFujian($request->get('sc_related_to'));
//                if ($isNeed) {
//                    //如果没有资质附件那就打回
//                    $isNeedFlag = true;
//                    $ziZhiFileArray = $request->get('zizhifile');
//                    if (!$ziZhiFileArray) {
//                        $this->showMsg('此客户需要上传客户资质证明');
//                        exit;
//                    }
//                }
//            }

            if ($request->get("record")) {
                $newInvoiceRecordModel = Newinvoice_Record_Model::getCleanInstance("Newinvoice");
                $invoiceCompany = $newInvoiceRecordModel->getInvoiceCompanyByContractId($request->get("record"));
                if ($invoiceCompany && $invoiceCompany != $request->get("invoicecompany")) {
                    $this->showMsg('已存在可用发票的开票公司是:' . $invoiceCompany);
                    exit;
                }

                $matchReceivedPaymentRecordModel = Matchreceivements_Record_Model::getCleanInstance("Matchreceivements");
                $matchTotal = $matchReceivedPaymentRecordModel->contractMatchTotalByContractId($request->get("record"));
                if($request->get('frameworkcontract')=='no' && $request->get("total")<$matchTotal){
                    $this->showMsg('已回款的金额大于您输入的合同金额');
                    exit;
                }
                $matchAccountName = $matchReceivedPaymentRecordModel->getMatchedAccountName($request->get("record"));
                $oldRecordModel = ServiceContracts_Record_Model::getInstanceById($request->get("record"),"ServiceContracts");
                if($matchTotal>0 && (($request->get("sc_related_to")!=$oldRecordModel->get("sc_related_to")&&$oldRecordModel->get("sc_related_to")>0) || count($matchAccountName)>1 || (!empty($matchAccountName) && !in_array($request->get("sc_related_to_display"),$matchAccountName)))){
                    $this->showMsg('已匹配回款，不允许变更客户');
                    exit;
                }
            }

            /*$supercollar=$_REQUEST['supercollar'];*/
            $servicenum = ServiceContracts_Record_Model::servicecontracts_reviced($receiveid);
            if ($modulestatus == '已发放' && $servicenum/* && empty($supercollar)*/) {
                echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝领取</h2><p class="text">该领取人有' . $servicenum . '份合同没有交回,不允许再领取了,请先把合同收回后,方能领取!!!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                exit;
            }

            //if($modulestatus=='c_complete' && (empty($_REQUEST['Returndate']) || empty($_REQUEST['Receivedate']) || empty($_REQUEST['signdate']))){
            if ($iscomplete == 'on' && (empty($_REQUEST['Returndate']) || empty($_REQUEST['signdate']))) {
                echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">已签收的合同日期必填!!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                exit;
            }

            if ($iscomplete == 'on' && $request->get('record') && $contract_type == 'T云WEB版') {
                //编辑已签收时判断是否有未签收的字段
                $flag = $this->checkHasNotSignContract($request->get('record'));
                if (!$flag) {
                    echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">合同下单之前存在有效订单，需把有效订单处理（签收或作废），处理完后合同才能签收。</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                    exit;
                }
            }

            if ($_REQUEST['productid'] != "") {
                $productid = implode(',', $_REQUEST['productid']);
                $request->set('productid', $productid);
            } else {  //复选框产品为空时
                $request->set('productid', "");
            }
            if ($_REQUEST['extraproductid'] != "") {
                $productid = implode(',', $_REQUEST['extraproductid']);
                $request->set('extraproductid', $productid);
            } else {  //复选框产品为空时
                $request->set('extraproductid', "");
            }
            //$productids=$request->get('productids');
            $record = $request->get('record');

            /* if(empty($productids) && empty($record)){
                //echo 23555;die;
                throw new AppException(vtranslate('ServiceContracts').' '.vtranslate('LBL_NOT_ACCESSIBLE'));
                exit;
            } */

            if ($request->get('actualeffectivetime')) {
                $request->set('effectivetime', $request->get('actualeffectivetime'));
            }
            //////steel 2015-03-06 添加合同后更新最后客户的成交时间
            $recordModel = $this->getRecordModelFromRequest($request);
            $isTyunSite = false;
            $is_tyunweb_contract = false;
            $this->setRequestData();
            $accountid = $request->get('sc_related_to');
            $sideagreement = $recordModel->get("sideagreement");
            $isReceived = false;//是否是合同管理员权限
            if ($iscomplete == 'on' && $parent_contracttypeid == '2' && $sideagreement != '1' && $contract_type != 'T云系列补充协议（非标）') {
                if (!in_array($contract_type, $configcontracttypeNameTYUN)) {
                    $isTyunSite = $recordModel->checkTyunCrmSiteProduct($request);

                    if ($isTyunSite == true) {
                        // 移动CRM建站合同推送到T云
                        $contract_no = $recordModel->get('contract_no');
                        $accountid = $request->get('sc_related_to');
                        $result_data = $recordModel->uploadCrmSiteContractToTyun($contract_no, $accountid, $_REQUEST['productid'], $_REQUEST['extraproductid']);
                        if ($result_data['success'] == true || $result_data['success'] == 1) {
                        } else {
                            echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">' . $result_data["message"] . '</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                            exit;
                        }
                    } else {
                        //验证T云合同和购买服务是否一致 gaocl add 2018/06/12
                        $is_tyun_contract = false;
                        $activationcodeid = '0';
                        $contractstatus = '1';

                        $result = $recordModel->checkTyunProductActivationCode($request, false);
                        if (!$result['success']) {
                            $msg = $result['msg'];
                            //更新状态，后续做变更处理
                            if ($result['ismodify'] && $result['is_active']) {
                                $recordModel->updateRejectionReason($result);
                            }
                            echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">' . $msg . '</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                            exit;
                        }
                        $activationcodeid = $result['id'];
                        $contractstatus = $result['contractstatus'];
                        if ($activationcodeid != '0') {
                            $is_tyun_contract = true;
                        }
                    }
                } else {
//                $tyunWebReturn=$recordModel->checkTyunWebconfim($request);
                    $is_tyunweb_contract = true;
//                if($tyunWebReturn['flag']){
//                    $msg=$tyunWebReturn['msg'];
//                    echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">' . $msg . '</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
//                    exit;
//                }else if($tyunWebReturn['pushstatus']==1){
//                    $is_tyunweb_contract=false;
//                }
                }
            }
            $adb = PearDatabase::getInstance();
            if ($iscomplete == 'on') {
                $isReceived = $recordModel->personalAuthority('ServiceContracts', 'Received');
                if ($isReceived) {
                    $this->checkAmountreceivable($request);
                }
                //如果是分期合同则校验是否已签收分期协议
//                $contractsAgreementRecordModel = Vtiger_Record_Model::getCleanInstance("ContractsAgreement");
                if ($recordModel->get("isstage") && $signaturetype == 'papercontract') {
                    $fileRecordModel = Files_Record_Model::getCleanInstance("Files");
                    if(!$fileRecordModel->isExistFile('ServiceContracts',$record,'files_style13')){
                        $msg='请先上传分期付款附件';
                        echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">' . $msg . '</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                        exit;
                    }
//                    $isSign = $contractsAgreementRecordModel->isSignContractsAgreement($record);
//                    if (!$isSign['success']) {
//                        $msg = $isSign['msg'];
//                        echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">' . $msg . '</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
//                        exit;
//                    }
                }

                $query = 'SELECT producttype,accountname FROM vtiger_account WHERE accountid=' . $accountid . ' LIMIT 1';
                $accountResult1 = $adb->pquery($query, array());
                $producttype = array();
                if ($adb->num_rows($accountResult1)) {
                    $accountData1 = $adb->raw_query_result_rowdata($accountResult1, 0);
                    if (!empty($accountData1['producttype'])) {
                        $producttype = explode(',', $accountData1['producttype']);
                        $producttype = array_unique($producttype);
                    }
                    $recordModel->set('customer_name', $accountData1['accountname']);
                }
                $producttypename = array();
                foreach ($_POST['productid'] as $value) {
                    if (!in_array($_POST['producttypename'][$value], $producttype)) {
                        $producttype[] = $_POST['producttypename'][$value];
                    }
                    if (!in_array($_POST['producttypename'][$value], $producttypename)) {
                        $producttypename[] = $_POST['producttypename'][$value];
                    }
                }
                foreach ($_POST['extraproductid'] as $value) {
                    if (!in_array($_POST['eproducttypename'][$value], $producttype)) {
                        $producttype[] = $_POST['eproducttypename'][$value];
                    }
                    if (!in_array($_POST['eproducttypename'][$value], $producttypename)) {
                        $producttypename[] = $_POST['eproducttypename'][$value];
                    }
                }
                $recordModel->set('productname', implode(',', $producttypename));
                $sql = "UPDATE vtiger_account SET producttype='" . implode(',', $producttype) . "' WHERE accountid=" . $accountid . ' LIMIT 1';
                $adb->pquery($sql, array());
                if ($recordModel->checkContractClient($request)) {
                    $msg = "数据迁移合同签收失败,请联系相关人员处理";
                    echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">' . $msg . '</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                    exit;
                }
                if ($parent_contracttypeid == '2') {
                    $recordModel->tyun71360ContractConfirm();//合同签收通知71360平台
                }
            } else {
                $productnamestr = '';
                /*if(!empty($_POST['productid'])){
                    $productnamestr=implode(',',$_POST['productid']);
                }*/
                $tempproductname = $request->get('productname');
                if (!empty($tempproductname)) {
                    $productnamestr .= implode(',', $request->get('productname'));
                }
                $recordModel->set('productname', $productnamestr);
            }
            ///////////
            $oldmodulestatus = $recordModel->entity->column_fields['modulestatus'];//取得未更改之前的ID

            if ($signaturetype == 'eleccontract' && $record > 0) {
                $recordModel->set('eleccontractid', $recordModel->entity->column_fields['eleccontractid']);
            }

            if($request->get('frameworkcontract')=='no'&& $request->get("record") &&$oldRecordModel->get("total")>0 && $request->get("total")>$oldRecordModel->get("total")){
                $adb->pquery("update vtiger_servicecontracts set contractstate=0,ispay=0,paytotyun=? where servicecontractsid=?",array($matchTotal,$request->get("record")));
            }

            if($signaturetype=='eleccontract'&&$request->get("wkcode")&&!$record){
                //已存在该订单号对应的有效合同，不能重新发起合同
                $result = $adb->pquery("select 1 from vtiger_servicecontracts_wk_extend a 
  left join vtiger_servicecontracts b on a.servicecontractsid=b.servicecontractsid 
  left join vtiger_crmentity c on b.servicecontractsid=c.crmid
where a.wkcode=? and b.modulestatus !='c_cancel' and c.deleted=0",
                    array($request->get("wkcode")));
                if($adb->num_rows($result)){
                    $msg = "已存在该订单号对应的有效合同，不能重新发起合同";
                    echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">' . $msg . '</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                    exit;
                }
            }

            $recordModel->save();

            //将附件信息存入account表
//            if ($isNeedFlag) {
//                $accountRecordObject = new Accounts_Record_Model();
//                $accountRecordObject->updateAccountZiZhiFile($request->get('sc_related_to'), $request->get('zizhifile'));
//            }

            //当时新建
            $saveRecord=new ServiceContracts();
//            $basicAjaxObject=new ServiceContracts_BasicAjax_Action();
//            $isNeedFlag=$basicAjaxObject->isNeedZizhiFujian($accountid);
//            if($recordModel->get('isstandard')==0&&!$isNeedFlag&&!$record){
                //非标合同还有不需要资质直接生成合同号
                if(!$record&&$recordModel->get('isstandard')==0){
                    $saveRecord->makeContractNo($recordModel->getId());
                }
//            }


            //如果存在券码和券码用户名 则存入remark中
            $coupon_sql = "SELECT couponcode,couponname FROM vtiger_activationcode  WHERE contractid=? AND status IN(0,1)";
            $result = $adb->pquery($coupon_sql, array($recordModel->getId()));
            if ($adb->num_rows($result)) {
                $data = $adb->fetchByAssoc($result, 0);
                if ($data['couponcode']) {
                    $remark = $request->get("remark") . " 券码:" . $data['couponcode'] . ' 券码用户名:' . $data['couponname'];
                    $adb->pquery("update vtiger_servicecontracts set remark=? where servicecontractsid=?", array($remark, $recordModel->getId()));
                }
            }

            //三方合同
            $contract_classification = $request->get("contract_classification");
            if ('tripcontract' == $contract_classification) {
                $agentname = $request->get("agentname");
                $agentid = $request->get("agentid");
                $result = $adb->pquery("select accountid from vtiger_account a left join vtiger_crmentity b on a.accountid=b.crmid where a.accountname=? and b.deleted=0 limit 1", array('代理商-' . $agentname));
                if (!$adb->num_rows($result)) {
                    //不存在则插入客户表中，生成一个客户
                    $newagentname = '代理商-' . $agentname;
                    $accountRecordModel = Accounts_Record_Model::getCleanInstance("Accounts");
                    $accountRecordModel->set('module', 'Accounts');
                    $accountRecordModel->set('action', 'Save');
                    $accountRecordModel->set('mode', '');
                    $accountRecordModel->set("accountname", $newagentname);
                    $accountRecordModel->set("customertype", 'ChannelCustomers');
                    $accountRecordModel->save();
                    $result = $adb->pquery("select accountid from vtiger_account a left join vtiger_crmentity b on a.accountid=b.crmid where a.accountname=? and b.deleted=0 limit 1", array($newagentname));
                }
                $row = $adb->fetchByAssoc($result, 0);
                $adb->pquery("update vtiger_servicecontracts set agentname=?,agentid=?,agentaccountid=? where servicecontractsid=?", array($agentname, $agentid, $row['accountid'], $recordModel->getId()));
            }
            $contract_type = $request->get('contract_type');
            if (in_array($contract_type, $configcontracttypeNameTYUN)) {
                $query = "UPDATE vtiger_modtracker_detail,vtiger_modtracker_basic SET vtiger_modtracker_detail.fieldname='productidd' WHERE fieldname='productid' AND vtiger_modtracker_detail.id=vtiger_modtracker_basic.id AND vtiger_modtracker_basic.crmid=?";
                $adb->pquery($query, array($recordModel->getId()));
                $query = "UPDATE vtiger_modtracker_detail,vtiger_modtracker_basic SET vtiger_modtracker_detail.fieldname='extraproductidd' WHERE fieldname='extraproductid' AND vtiger_modtracker_detail.id=vtiger_modtracker_basic.id AND vtiger_modtracker_basic.crmid=?";
                $adb->pquery($query, array($recordModel->getId()));
            }
            //合同保存时状发生改变
            /*if($oldmodulestatus!=$request->get('modulestatus')){
                ServiceContracts_Record_Model::setSalesorderandAlert($request->get('modulestatus'),array(),$recordModel->getId());
            }*/
            //合同签收状态
            if ($iscomplete == 'on' && $is_tyun_contract == true && $sideagreement != '1') {
                $adb->pquery('UPDATE vtiger_servicecontracts SET checkstatus=0,modulestatus=\'c_complete\' WHERE servicecontractsid=?', array($recordModel->getId()));
                //调用T云接口，告知合同录入成功
                if ($is_tyun_contract/* && $contractstatus =='0'*/) {
                    $request->set('contract_no', $recordModel->get('contract_no'));
                    $result = $recordModel->tyunContractConfirm($request);
                    if (!$result['success']) {
                        $msg = $result['message'];
                        echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">' . $msg . '</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                        exit;
                    }
                    $adb->pquery('UPDATE vtiger_activationcode SET contractstatus=1 WHERE activationcodeid=?', array($activationcodeid));
                }
            }
            if ($is_tyunweb_contract && $iscomplete == 'on') {
                $adb->pquery('UPDATE vtiger_activationcode SET contractstatus=1 WHERE contractid=? AND comeformtyun=1 AND `status` in(0,1)', array($recordModel->getId()));
            }
            if ($iscomplete == 'on' && $oldmodulestatus != 'c_complete') {
                $adb->pquery("UPDATE vtiger_servicecontracts SET signfor_date='" . date('Y-m-d H:i:s') . "' WHERE servicecontractsid=?", array($recordModel->getId()));
                $accountid = $request->get('sc_related_to');
                if (!empty($_REQUEST['productid']) && $accountid > 0 && ($servicecontractstype == 'newlyadded' || $servicecontractstype == '新增')) {
                    $productid = implode(' |##| ', $_REQUEST['productid']);
                    $query = 'SELECT servicetype FROM vtiger_account WHERE accountid=' . $accountid . ' LIMIT 1';
                    $accountResult = $adb->pquery($query, array());
                    $accountData = $adb->raw_query_result_rowdata($accountResult);
                    if (!empty($accountData['servicetype'])) {
                        $servicetype = explode(' |##| ', $accountData['servicetype']);
                        $productid = array_merge($_REQUEST['productid'], $servicetype);
                        $productid = array_unique($productid);
                        $productid = implode(' |##| ', $productid);
                    }
                    $sql = "UPDATE vtiger_account SET servicetype='" . $productid . "' WHERE accountid=" . $accountid . ' LIMIT 1';
                    $adb->pquery($sql, array());
                }
            }
            if ($iscomplete == 'on') {
                $isautoclose = $request->get('frameworkcontract') == 'yes' ? 0 : 1;
                $adb->pquery("UPDATE vtiger_servicecontracts SET signfor_date='" . date('Y-m-d H:i:s') . "',isautoclose=" . $isautoclose . " WHERE servicecontractsid=? AND signfor_date IS NULL", array($recordModel->getId()));
                $this->deleteContractsExecution($recordModel);//将原添加的数据删除
                if ($isReceived) {
                    if ($recordModel->canCreateExecution()) {
                        $this->addContractphasesplit($request, $recordModel);
                    } else {
//                    $this->deleteContractsExecution($recordModel);//将原添加的数据删除
                    }
                }


                //记录签收时候
                $contractAgreementRecordModel = ContractsAgreement_Record_Model::getCleanInstance("ContractsAgreement");
                $contractAgreementRecordModel->recordContractDelaySign($recordModel->getId(), $recordModel->entity->column_fields["sideagreement"], 'c_complete');

            }
            if ($signaturetype == 'eleccontract') {
                $this->doElecContract($request, $recordModel);
            }
            if ($request->get("categoryid") || $request->get("categoryid") === '0') {
                $adb->pquery("UPDATE vtiger_servicecontracts SET categoryid=? WHERE servicecontractsid=?", array($request->get("categoryid"), $recordModel->getId()));
            }

            //修改合同应收里面的合同金额和逾期合同金额
            $adb->pquery("update vtiger_contract_receivable set contracttotal=? where contractid=?", array($request->get('total'), $recordModel->getId()));
            $adb->pquery("update vtiger_receivable_overdue set contracttotal=? where contractid=?", array($request->get('total'), $recordModel->getId()));

            return $recordModel;
        }
	}

	/**
	 * 合同作废
	 */
	public function invalidContract($contractNo, $activeCode){
		$myData['ContractCode'] = $contractNo;//合同编号
		$myData['SecretKeyID'] = $activeCode;//激活码ID
		$url = "http://tyunapi.71360.com/api/cms/InvalidSecretKey";
		$this->_logs(array("data加密前数据：", $myData));
		$tempData['data'] = $this->encrypt(json_encode($myData));
		$this->_logs(array("data加密后数据：", $tempData['data']));
		$postData = http_build_query($tempData);//传参数
		$res = $this->https_request($url, $postData);
		$result = json_decode($res, true);
		$result = json_decode($result, true);
		return $result;
		/* if($result['success']){
			echo json_encode(array('success'=>1, 'msg'=>$result['message']));
		}else{
			echo json_encode(array('success'=>0, 'msg'=>$result['message']));
		}
		exit(); */
	}

	/**
	 * 激活信息更新
	 */
	public function updateSecretInfo($contractNo, $customerName, $productLife, $productId){
		$myData['ContractCode'] = $contractNo;//合同编号
		$myData['CompanyName'] = $customerName;//客户名称
		$myData['ProductLife'] = $productLife;//年限
		$myData['ProductID'] = $productId;//产品编号
		$url = "http://tyunapi.71360.com/api/cms/UpdateSecretKey";
		$this->_logs(array("data加密前数据：", $myData));
		$tempData['data'] = $this->encrypt(json_encode($myData));
		$this->_logs(array("data加密后数据：", $tempData['data']));
		$postData = http_build_query($tempData);//传参数
		$res = $this->https_request($url, $postData);
		$result = json_decode($res, true);
		$result = json_decode($result, true);
		return $result;
		/* if($result['success']){
			echo json_encode(array('success'=>1, 'msg'=>$result['message']));
		}else{
			echo json_encode(array('success'=>0, 'msg'=>$result['message']));
		}
		exit(); */
	}

	/**
	 * des加密
	 * @param unknown $encrypt 原文
	 * @return string
	 */
	public function encrypt($encrypt, $key='sdfesdcf') {
		$mcrypt = MCRYPT_TRIPLEDES;
		$iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$passcrypt = mcrypt_encrypt($mcrypt, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
		$encode = base64_encode($passcrypt);
		return $encode;
	}

	/**
	 * des解密
	 * @param unknown $decrypt
	 * @return string
	 */
	/* public function decrypt($decrypt, $key='sdfesdcf'){
		$decoded = str_replace(' ','%20',$decrypt);
		$decoded = base64_decode($decrypt);
		$mcrypt = MCRYPT_TRIPLEDES;
		$iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$decrypted = mcrypt_decrypt($mcrypt, $key, $decoded, MCRYPT_MODE_ECB, $iv);
		return $decrypted;
	} */

	public function https_request($url, $data = null){
		$curl = curl_init();
		$this->_logs($url);
		//curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		$this->_logs($output);
		curl_close($curl);
		return $output;
	}

	/**
	 * 写日志，用于测试,可以开启关闭
	 * @param data mixed
	 */
	public function _logs($data, $file = 'logs_'){
		$year	= date("Y");
		$month	= date("m");
		$dir	= './logs/tyun/' . $year . '/' . $month . '/';
		if(!is_dir($dir)) {
			mkdir($dir,0755,true);
		}
		$file = $dir . $file . date('Y-m-d').'.txt';
		@file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
	}
	public function setRequestData(){
	    $array=array("thepackagename",
            "productname",
            "thepackage",
            "isextra",
            "vendorid",
            "suppliercontractsid",
            "productnumber",
            "pmarketprice",
            "punit_price",
            "prealprice",
            "agelife",
            "realprice",
            "purchasemount",
            "opendate",
            "closedate",
            "unit_price",
            "realmarketprice",
            "productcomboid",
            "productsolution",
            "producttext");
	    foreach($array as $value){
	        if(!empty($_REQUEST[$value])){
	            $temp=array();
	            foreach($_REQUEST[$value] as $key=>$value1){
	                $temp1=explode('DZE',$key);
                    if(!empty($temp1[1])){
                        $temp[$temp1[1]]=$value1;
                    }
                }
                $_REQUEST[$value]=$_REQUEST[$value]+$temp;
            }
        }
    }

    /**
     * 合同附件保存
     * @param $request
     * @param $recordModel
     */
    public function saveFileData($request,$recordModel){
        $eleccontractidurl=$this->getElecTPLView($request);
        $recordModel->fileSave($eleccontractidurl,'files_style6','-合同审核件');
    }
    public function showMsg($msg,$title='拒绝操作'){
        echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>'.$title.'</h2><p class="text">'.$msg.'</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
    }

    /**
     * 电子合同处理
     */
    public function doElecContract($request,$recordModel){
        global $adb;
        $contractattribute=$request->get('contractattribute');
        $servicecontractid = $recordModel->getId();
        $adb->pquery('UPDATE vtiger_files SET delflag=1 WHERE relationid=?',array($servicecontractid));
        if($contractattribute=='standard'){
            $focus = CRMEntity::getInstance('ServiceContracts');
            $recordModelData = Vtiger_Record_Model::getInstanceById($servicecontractid, 'ServiceContracts');
            $focus->createServiceContractsNo($recordModelData->entity->column_fields);
            $recordModelData = Vtiger_Record_Model::getInstanceById($servicecontractid, 'ServiceContracts', true);
            $contract_no = $recordModelData->get('contract_no');
            $eleccontractid = $recordModelData->get('eleccontractid');
            $arrayData = array("contractId" => $eleccontractid,//放心签平台返回的合同id
                "number" => $contract_no, //珍岛生成的最终合同编号
            );
            $returnData = $recordModelData->contractSend($arrayData);//审核通过
            $jsonData = json_decode($returnData, true);
            $eleccontractstatus='a_elec_actioning_fail';;
            $receivedate='';
            if($jsonData['success']){
                $eleccontractstatus='b_elec_actioning';
                $receivedate=date('Y-m-d');
                $recordModelData->fileSave($jsonData['data'],'files_style8','');
                $recordModelData->sendMailFXQ();
                $recordModelData->sendSMS(array('statustype'=>'','mobile'=>$recordModelData->get('elereceivermobile'),'eleccontracttpl'=>$recordModelData->get('eleccontracttpl'),'url'=>$recordModelData->elecContractUrl));
            }
            $sql='UPDATE vtiger_servicecontracts SET workflowsnode=\'\',modulestatus=\'已发放\',receivedate=?,eleccontractstatus=? WHERE servicecontractsid=?';
            $adb->pquery($sql,array($receivedate,$eleccontractstatus,$servicecontractid));
        }elseif($contractattribute=='customized'){
            $eleccontractstatus='a_elec_sending';
            $focus = CRMEntity::getInstance('ServiceContracts');
            $_REQUEST['workflowsid']=$this->elecContractWorkflowsid;
            $focus->makeWorkflows('ServiceContracts', $_REQUEST['workflowsid'], $servicecontractid,'edit');
            $focus->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='b_check',isstandard=1,eleccontractstatus=? WHERE servicecontractsid=?",array($eleccontractstatus,$servicecontractid));
            $departmentid=$_SESSION['userdepartmentid'];
            $focus->setAudituid('ContractsAuditset',$departmentid,$servicecontractid,$_REQUEST['workflowsid']);
            $this->saveFileData($request,$recordModel);
            $recordModel=Vtiger_Record_Model::getInstanceById($servicecontractid, 'ServiceContracts',true);
            $recordModel->setWorkflowUserID($servicecontractid,$recordModel->get('companycode'));//财务主管流程节点设置
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$servicecontractid,'salesorderworkflowstagesid'=>0));
            $sql='UPDATE vtiger_servicecontracts SET eleccontracturl=?,workflowsnode=\'\' WHERE servicecontractsid=?';
            $adb->pquery($sql,array($request->get('eleccontractidurl'),$servicecontractid));
        }

        //数字威客的电子合同则进入威客合同扩展表
        if($request->get("wkcode")){
            $result = $adb->pquery("select * from vtiger_servicecontracts_wk_extend where servicecontractsid=?",array($servicecontractid));
            if($adb->num_rows($result)){
                $adb->pquery("update vtiger_servicecontracts_wk_extend set wkcode=?,wkcontactname=?,wkcontactphone=? where servicecontractsid=?",array($servicecontractid));
            }else{
                $adb->pquery("insert into vtiger_servicecontracts_wk_extend (servicecontractsid,wkcode,wkcontactname,wkcontactphone) values (?,?,?,?)",
                    array($servicecontractid,$request->get("wkcode"),$request->get("wkcontactname"),$request->get("wkcontactphone")));

                $recordModel->contractAfterNotifyWk($request->get("wkcode"),$request->get("wkcontactphone"),$request->get('eleccontractidurl'));
            }
        }

    }
    public function getElecTPLView($request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $request->set('contractId',$request->get('eleccontractid'));
        $jsonoutput=$recordModel->getElecTPLView($request);
        $data=json_decode($jsonoutput,true);
        return $data['data']['contract'];
    }
    /**
     * 校验应收阶段金额之和与合同总额是否相等
     * @param $request
     */
    public function checkAmountreceivable($request){
        $frameworkcontract=$request->get('frameworkcontract');
        if($frameworkcontract=='no'){
            $mamountreceivable=$request->get('mreceiveableamount');
            $total=$request->get('total');
            $total=str_replace(',','',$total);
            $totalmamountreceivable=0;
            if(count($mamountreceivable)){
                foreach($mamountreceivable as $value){
                    $totalmamountreceivable=bcadd($totalmamountreceivable,$value);
                }
                if(bccomp($total,$totalmamountreceivable)!=0){
                    $this->showMsg('合同金额与阶段收款总和不对！');
                    exit;
                }
            }else{
                $this->showMsg('非框架合同阶段必填！');
                exit;
            }

        }
    }

    /**
     * 添加应收数据
     * @param $request
     * @param $recordModel
     */
    public function addContractphasesplit($request,$recordModel){
        global $current_user,$adb,$isallow;
        $record=$recordModel->getId();
        $query='SELECT * FROM `vtiger_contracts_execution` WHERE contractid=?';
        $result=$adb->pquery($query,array($record));
        $contractexecutionid=0;
        $datetime=date('Y-m-d H:i:s');
        if($adb->num_rows($result)){
                $contractexecutionid=$result->fields['contractexecutionid'];
            $query="UPDATE vtiger_crmentity SET deleted=1 WHERE crmid=?";
            $adb->pquery($query,array($contractexecutionid));
            $sql='DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=?';
            $adb->pquery($sql,array($contractexecutionid));
        }

        $flag = false;
        //小SaaS的处理
        $sql = "select startdate from vtiger_activationcode where contractid = ? AND `status`!=2 AND iscollegeedition=0";
        $result = $adb->pquery($sql,array($record));
        if($adb->num_rows($result)){
            $flag = true;
        }

        $frameworkcontract=$request->get('frameworkcontract');
        $mamountreceivable=$request->get('mreceiveableamount');
        $mcollectiondesc=$request->get('mcollectiondescription');
        $accountid=$recordModel->get('sc_related_to');
        $contractreceivablebalance =0;
        $updateexecutiondetailsql='UPDATE vtiger_contracts_execution,vtiger_contracts_execution_detail SET vtiger_contracts_execution.executiondetailid=vtiger_contracts_execution_detail.executiondetailid
                            WHERE vtiger_contracts_execution.contractexecutionid=vtiger_contracts_execution_detail.contractexecutionid
                                                AND vtiger_contracts_execution_detail.stage=1 AND vtiger_contracts_execution.contractexecutionid=?';
        $i=1;
        if(!$flag){
            if($frameworkcontract=='no'){
                if(count($mamountreceivable)){
                    $ContractExecutionRecordModel=Vtiger_Record_Model::getCleanInstance('ContractExecution');
                    $contractexecutionid = $this->createContractsExecution($recordModel,$contractexecutionid);
                    $sql='DELETE FROM `vtiger_contracts_execution_detail` WHERE contractexecutionid=?';
                    $adb->pquery($sql,array($contractexecutionid));
                    $valueStr='';
                    $isallow=array('ContractExecution');
                    $_REQUEST['workflowsid']=$ContractExecutionRecordModel->contractWorkFlowSid;
                    $crmentity = CRMEntity::getInstance('ContractExecution');
                    $crmentity->makeWorkflows('ContractExecution', $ContractExecutionRecordModel->contractWorkFlowSid, $contractexecutionid, '');
                    $query='SELECT * FROM vtiger_salesorderworkflowstages WHERE salesorderid=? LIMIT 1';
                    $sworkResult=$adb->pquery($query,array($contractexecutionid));
                    $firstData=$adb->fetchByAssoc($sworkResult);
                    $salesorderworkflowstagesid=$firstData['salesorderworkflowstagesid'];
                    unset($firstData['salesorderworkflowstagesid']);
                    unset($firstData['actiontime']);
                    foreach($mamountreceivable as $key=>$value){
                        if(1==$i){
                            $sql="UPDATE vtiger_salesorderworkflowstages SET workflowstagesname='合同执行第1阶段',sequence=1 WHERE salesorderid=? AND salesorderworkflowstagesid=?";
                            $adb->pquery($sql,array($contractexecutionid,$salesorderworkflowstagesid));
                        }else{
                            $firstData['workflowstagesname']="合同执行第".$i."阶段";
                            $firstData['isaction']=0;
                            $firstData['sequence']=$i;
                            $adb->pquery("INSERT INTO `vtiger_salesorderworkflowstages`(".implode(",", array_keys($firstData)).") VALUES(" . generateQuestionMarks($firstData) . ")",$firstData);
                        }
                        $valueStr.="(".$i.",'第".$i."阶段',".$contractexecutionid.','.$value.",'".$mcollectiondesc[$key].'\',\'a_no_execute\',\'合同生成\','.$accountid.','.$record.','.$value.",'normal'".'),';
                        $i++;
                        $contractreceivablebalance +=$value;
                    }
                    $valueStr=trim($valueStr,',');
                    $sql='INSERT INTO `vtiger_contracts_execution_detail` (`stage`, `stageshow`,contractexecutionid, `receiveableamount`, `collectiondescription`,  `executestatus`,`stagetype`,accountid,contractid,`contractreceivable`,`collection`) VALUES';
                    $adb->pquery($sql.$valueStr,array());
                    $adb->pquery($updateexecutiondetailsql,array($contractexecutionid));
                    $ContractRecordModel = ContractReceivable_Record_Model::getCleanInstance('ContractReceivable');
                    if(!$ContractRecordModel->isExist($record)) {
                        $ContractExecutionRecordModel->insertintoContractReceivable($adb, $contractexecutionid);
                    }
                }
            }else{
                $ContractExecutionRecordModel=Vtiger_Record_Model::getCleanInstance('ContractExecution');
                $ContractRecordModel = ContractReceivable_Record_Model::getCleanInstance('ContractReceivable');
                if(!$ContractRecordModel->isExist($record)) {
                    $ContractExecutionRecordModel->insertintoContractReceivableFrameContract($adb, $record);
                }
            }
            return;
        }
        //小SaaS的相关判断
		$sql='DELETE FROM `vtiger_contracts_execution_detail` WHERE contractid=?';
		$adb->pquery($sql,array($record));
		$i=1;
		$contractreceivablebalance=0;
        $valueStr = '';
        $row = $adb->fetchByAssoc($result,0);

        $executestatus = 'a_no_execute';
        $receiverabledate = '';
        if($row['startdate'] && $row['startdate']!='0000-00-00 00:00:00'){
            $executestatus='c_executed';
            $receiverabledate = date("Y-m-d",strtotime($row['startdate']));
        }
        //$contractexecutionid=$this->createContractsExecution($recordModel,$contractexecutionid);
        foreach($mamountreceivable as $key=>$value){
            $valueStr.="(".$i.",'第".$i."阶段',".$contractexecutionid.','.$value.",'".$mcollectiondesc[$key]."','".$executestatus."','合同生成',".$accountid.','.$record.','.$value.",'normal','".$receiverabledate."'),";
            $i++;
            $contractreceivablebalance +=$value;
        }
        $valueStr=trim($valueStr,',');
        $sql='INSERT INTO `vtiger_contracts_execution_detail` (`stage`, `stageshow`,contractexecutionid, `receiveableamount`, `collectiondescription`,  `executestatus`,`stagetype`,accountid,contractid,`contractreceivable`,`collection`,`receiverabledate`) VALUES';
        $adb->pquery($sql.$valueStr,array());
        //$adb->pquery($updateexecutiondetailsql,array($contractexecutionid));


        $ContractRecordModel = ContractReceivable_Record_Model::getCleanInstance('ContractReceivable');
        if($ContractRecordModel->isExist($record)){
            $query='SELECT * FROM vtiger_contract_type WHERE contract_type=? limit 1';
            $result=$adb->pquery($query,array($request->get('contract_type')));
            $row = $adb->fetchByAssoc($result,0);
            $sql2 = "update vtiger_contract_receivable set contracttotal=?,contractreceivableamount=?,contractreceivablebalance=?,signid=?,bussinesstype=? where contractid=?";
            $adb->pquery($sql2,array($request->get('total'),$contractreceivablebalance,$contractreceivablebalance,$request->get('Signid'),$row['bussinesstype'],$record));
        }else{
            if($row['startdate']=='0000-00-00 00:00:00' || $row['startdate']>=date('Y-m-d')){
                return;
            }
            $sql2 = "insert into vtiger_contract_receivable(`contractid`,`accountid`,`contract_no`,`bussinesstype`,`productid`,`signid`,
                                         `isautoclose`,`contracttotal`,`contractreceivableamount`,`contractreceivablebalance`,
                                         `contractinvoiceamount`,`contractpaidamount`,`collectionstatus`,`signdempart`)
                        select
                        a.contractid,a.customerid as accountid,b.contract_no,
                        b.bussinesstype,
                        b.productid,
                        b.signid,
                        b.isautoclose,
                        ifnull(b.total,0) as contracttotal,
                        ifnull((select sum(receiveableamount) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=a.contractid),0)  as contractreceivableamount,
                        ifnull((select sum(contractreceivable) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=a.contractid),0)  as contractreceivablebalance,
                        ifnull((select sum(actualtotal) from vtiger_newinvoice where vtiger_newinvoice.contractid=a.contractid and vtiger_newinvoice.modulestatus='c_complete'),0) as contractinvoiceamount,
                        ifnull ((select sum(unit_price) from vtiger_receivedpayments where relatetoid=b.servicecontractsid and receivedstatus='normal' and deleted=0),0) as contractpaidamount,
                        if((select count(1) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=d.contractid and vtiger_contracts_execution_detail.receiverabledate<CURRENT_DATE and executestatus='a_no_execute')>0,'overdue','normal') as status,
                      b.signdempart
                    from vtiger_activationcode a
                    left join vtiger_account c on c.accountid = a.customerid
                    left join vtiger_servicecontracts b on a.contractid = b.servicecontractsid
                    left join vtiger_contracts_execution_detail d on d.contractid=a.contractid
                    where a.contractid=?  and a.startdate is not null  group by contract_no";
            $adb->pquery($sql2,array($record));
        }

    }

    /**
     * @param $recordModel
     * 将原添加的数据删除
     */
    public function deleteContractsExecution($recordModel){
        global $adb;
        $record=$recordModel->getId();
        $sql = "select 1 from vtiger_activationcode where `status`!=2 AND contractid =? AND iscollegeedition=0 ";
        $result = $adb->pquery($sql,array($record));
        if($adb->num_rows($result)){//T云订单表中有的不删除
            return ;
        }
        $query='SELECT * FROM `vtiger_contracts_execution` WHERE contractid=?';
        $result=$adb->pquery($query,array($record));
        if($adb->num_rows($result)){
            $contractexecutionid=$result->fields['contractexecutionid'];
            $query="UPDATE vtiger_crmentity SET deleted=1 WHERE crmid=?";
            $adb->pquery($query,array($contractexecutionid));
            $sql='DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=?';
            $adb->pquery($sql,array($contractexecutionid));
            $sql='DELETE FROM `vtiger_contracts_execution_detail` WHERE contractexecutionid=?';
            $adb->pquery($sql,array($contractexecutionid));
            $sql='DELETE FROM vtiger_contract_receivable WHERE contractid=?';
            $adb->pquery($sql,array($record));
        }
    }

    /**
     * 合同执行信息生成或更改
     * @param $recordModel
     * @param $contractexecutionid
     * @return mixed
     */
    public function createContractsExecution($recordModel,$contractexecutionid){
        global $adb, $current_user;
        $record=$recordModel->getId();
        $dateTime = date('Y-m-d H:i:s');
        $ContractExecutionRecordModel = Vtiger_Record_Model::getCleanInstance('ContractExecution');
        if($contractexecutionid>0){
            $contractexecutionidNO=$ContractExecutionRecordModel->setContractExecutionNo($contractexecutionid);
            $sql="UPDATE vtiger_crmentity SET deleted=0,label=? WHERE crmid=?";
            $adb->pquery($sql,array($contractexecutionidNO,$contractexecutionid));
            $fieldData['contractexecutionno'] = $contractexecutionidNO;
            $fieldData['contractid'] = $record;
            $fieldData['accountid'] = $recordModel->get('sc_related_to');
            $fieldData['sc_related_to'] = $recordModel->get('sc_related_to');
            $fieldData['modulestatus'] = 'a_normal';
            $fieldData['status'] = 'b_execution_actioning';
            $fieldData['createdate'] = $dateTime;
            $fieldData['workflowsid']=$ContractExecutionRecordModel->contractWorkFlowSid;
            $fieldNames = array_keys($fieldData);
            $fieldValues = array_values($fieldData);
            $fieldValues[]=$contractexecutionid;
            $fieldNamesSql=array_map(function($v){return $v.'=?';},$fieldNames);
            $sql='UPDATE vtiger_contracts_execution SET '.implode(',',$fieldNamesSql).' WHERE contractexecutionid=?';
            $adb->pquery($sql,$fieldValues);
        }else {
            $dateTime = date('Y-m-d H:i:s');
            $contractexecutionid = $adb->getUniqueID('vtiger_crmentity');
            $contractexecutionidNO = $ContractExecutionRecordModel->setContractExecutionNo($contractexecutionid);
            $sql = "INSERT INTO `vtiger_crmentity` (`crmid`, `smcreatorid`, `smownerid`, `modifiedby`, `setype`, `description`, `createdtime`, `modifiedtime`, `viewedtime`, `status`, `version`, `presence`, `deleted`, `label`) 
                    VALUES(?,?,?,?, 'ContractExecution', NULL, ?, ?, NULL, NULL, '0', '1', '0',?)";

            $adb->pquery($sql, array($contractexecutionid, $current_user->id, $current_user->id, $current_user->id, $dateTime, $dateTime, $contractexecutionidNO));
            $fieldData['contractexecutionid'] = $contractexecutionid;
            $fieldData['contractexecutionno'] = $contractexecutionidNO;
            $fieldData['contractid'] = $record;
            $fieldData['accountid'] = $recordModel->get('sc_related_to');
            $fieldData['sc_related_to'] = $recordModel->get('sc_related_to');
            $fieldData['status'] = 'b_execution_actioning';
            $fieldData['modulestatus'] = 'a_normal';
            $fieldData['createdate'] = $dateTime;
            $fieldData['workflowsid'] = $ContractExecutionRecordModel->contractWorkFlowSid;
            $fieldNames = array_keys($fieldData);
            $fieldValues = array_values($fieldData);
            $adb->pquery('INSERT INTO  vtiger_contracts_execution (' . implode(',', $fieldNames) . ') VALUES (' . generateQuestionMarks($fieldValues) . ')', $fieldValues);
        }
        return $contractexecutionid;
    }

    /**
     * 检察签收此合同之前是否有未签收合同
     * @param $contractId
     * @return bool
     * @throws Exception
     */
    public function checkHasNotSignContract($contractId){
        global $adb;
        $sql="select usercode from vtiger_activationcode where contractid=?";
        $result = $adb->pquery($sql,array($contractId));
        if($adb->num_rows($result)==0){
            //没有正常
            return true;
        }else{
            $usercode=$adb->query_result($result,0,'usercode');
            $sql="select 1 from vtiger_activationcode acttemp where usercode=? and contractstatus=0 and  status!=2 and contractid!=?
			AND activationcodeid<(select activationcodeid from vtiger_activationcode where vtiger_activationcode.usercode=acttemp.usercode and vtiger_activationcode.contractstatus=0 and  vtiger_activationcode.status!=2 and vtiger_activationcode.contractid=? ORDER BY activationcodeid DESC limit 1)";
            $result = $adb->pquery($sql,array($usercode,$contractId,$contractId));
            if($adb->num_rows($result)>0){
                return false;
            }
            return true;
        }
    }

}
