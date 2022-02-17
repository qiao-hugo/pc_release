<?php
/*+********
 *客户信息管理
 **********/

class RefillApplication_Record_Model extends Vtiger_Record_Model {
    /**
     * 充值明细信息
     * @param $id
     * @return array
     */
    public static function getRechargeSheet($id){
        if($id>0){
            global $adb;
            $query='SELECT (SELECT vtiger_products.productname FROM `vtiger_products` WHERE vtiger_products.productid=vtiger_rechargesheet.productid) as topplatform,(SELECT vtiger_suppliercontracts.contract_no FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid=vtiger_rechargesheet.suppliercontractsid) AS suppliercontractsname,vtiger_rechargesheet.* FROM `vtiger_rechargesheet` WHERE deleted=0 AND isentity=0 AND refillapplicationid='.$id;
            return $adb->run_query_allrecords($query);
        }
        return  array();
    }

    /**
     * 充值平台信息
     * @return array
     */
    public static function getRechargeProduct(){
        global $adb;
        $query='SELECT * FROM `vtiger_topplatform` ORDER BY sortorderid';
        return $adb->run_query_allrecords($query);
    }

    /**
    * 汇率
     * @return array
     */
    public static function getReceivementCurrencyType(){
        global $adb;
        $query='SELECT * FROM `vtiger_receivementcurrencytype` ORDER BY sortorderid';
        return $adb->run_query_allrecords($query);
    }

    /**
     * 实时发送邮件
     * @param Vtiger_Request $request
     * @param $array
     * @throws Exception
     */
    static public function backallsendmail(Vtiger_Request $request,$array)
    {
        global $adb, $current_user;
        $query = "SELECT * FROM `vtiger_systems` WHERE server_type='email' AND id=1";
        $result = $adb->pquery($query, array());
        //代替goto语句
        do{
            if (!$adb->num_rows($result)) {
                break;
            }
            $result = $adb->query_result_rowdata($result);
            $result['from_email_field'] = $result['from_email_field'] != '' ? $result['from_email_field'] : $result['server_username'];
            $query1 = "SELECT last_name, email1, email2 FROM `vtiger_users` WHERE id in(".implode(',',$array['userid']).")";
            $result1 = $adb->run_query_allrecords($query1);



            $Subject = '充值申请单 '.$array['refillapplicationno'].' 打回通知';
            $str='';

            $Body='<div>
                    <div><font size="2" face="Verdana"><font size="2" face="微软雅黑"><font size="2" face="微软雅黑"><span style="COLOR: #000000">Dear:
                    </span></font></font></font></div>
                    <blockquote style="MARGIN-TOP: 0px; PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt" id="ntes-flashmail-quote">
                      <div>
                      <blockquote style="MARGIN-TOP: 0px; PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt"><font size="2" face="Verdana"><font size="2" face="微软雅黑"></font></font><font size="2" face="Verdana">
                        </font><div><font size="2" face="Verdana">
                        </font>
                        <div><font size="2" face="微软雅黑">
                        充值申请单号:'.$array['refillapplicationno'].'  已被 '.$array['username'].'打回,请及时跟进</font></div></div></blockquote></div></blockquote></div>
                        <div>'.$str.'</div>
                    <font size="2" face="Verdana">
                    <blockquote style="PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt"><font size="2" face="Verdana"></font>
                      <blockquote style="PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt"><font size="2" face="Verdana">


                        <span>

                        <div><font size="2"><font face="微软雅黑"><span></span></font></font>&nbsp;</div>
                        <div><font size="3" face="微软雅黑"><span></span></font>&nbsp;</div></span>
                        <div align="center"><font color="#c0c0c0" size="2" face="Verdana"></font>&nbsp;</div>
                        <div align="left"><font color="#c0c0c0" size="2" face="Verdana">'.date('Y-m-d').'</font></div>
                        <div align="left"><font size="2" face="Verdana">
                        <hr style="WIDTH: 122px; HEIGHT: 2px" id="SignNameHR" align="left" size="2">
                        </font></div>
                    <div align="left"><font size="2" face="微软雅黑"><span>
                        <p style="LINE-HEIGHT: 16.5pt; MARGIN: 0cm 0cm 0pt; FONT-FAMILY: 宋体; FONT-SIZE: 12pt; WORD-BREAK: break-all" class="MsoNormal"><span style="FONT-SIZE: 10pt" lang="EN-US"></span></p></span></font></div>
                        <div align="left"><font color="#c0c0c0" size="2" face="Verdana"><span></span></font>&nbsp;</div></font></blockquote></blockquote></font>';
            require_once 'modules/Emails/class.phpmailer.php';
            $mailer=new PHPMailer();
            $mailer->IsSmtp();
            //$mailer->SMTPDebug = true;
            $mailer->SMTPAuth=$result['smtp_auth'];
            $mailer->Host=$result['server'];
            //$mailer->Host='smtp.qq.com';
            $mailer->SMTPSecure = "SSL";
            //$mailer->Port = $result['server_port'];
            $mailer->Username = $result['server_username'];//用户名
            $mailer->Password = $result['server_password'];//密码
            $mailer->From = $result['from_email_field'];//发件人
            $mailer->FromName = '系统';
            foreach($result1 as $result1value){
                $result1value['email1'] = $result1value['email1'] != '' ? $result1value['email1'] : $result1value['email2'];
                if (self::checkEmails($result1value['email1'])) {
                    $mailer->AddAddress($result1value['email1'], $result1value['last_name']);//收件人的地址
                }
            }
            $mailer->WordWrap = 100;
            $mailer->IsHTML(true);
            //$mailer->addembeddedimage('./logo.jpg', 'logoimg', 'logo.jpg');
            $mailer->Subject = $Subject;
            $mailer->Body = $Body;
            //$mail->AltBody = '收邮件了';//
            $email_flag=$mailer->Send()?'SENT':'Faile';
        }while(0);
        //$arr=array($mailer->From,$result1['email1'],'["zongmi@71360.com,'.$result1['acc'].'"]','[""]',$Subject,$Body,$current_user->id,'Leads',$email_flag);
        //$adb->pquery('INSERT INTO vtiger_emaildetails (`emailid`,`from_email`,`to_email`,`cc_email`,`bcc_email`,`subject`,`body`,`assigned_user_email`,`module`,`email_flag`) SELECT emailid+1,?,?,?,?,?,?,?,?,? FROM vtiger_emaildetails ORDER BY emailid DESC LIMIT 1',$arr);
    }

    /**
     * 邮箱格式验证
     * @param $str
     * @return bool
     */
    public function checkEmails($str){
        $str=trim($str);
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }
    public function getMatchPaymentsList(){
        $recordId=$this->getId();
        global $adb;
        $query='SELECT vtiger_refillapprayment.*,vtiger_receivedpayments.receivedstatus AS rreceivedstatus FROM `vtiger_refillapprayment` LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_refillapprayment.receivedpaymentsid WHERE vtiger_refillapprayment.deleted=0 AND vtiger_refillapprayment.refillapplicationid='.$recordId;
        return $adb->run_query_allrecords($query);
    }
    public function getChargeGuarantee($domodule){
        $query="SELECT vtiger_rechargeguarantee.*,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_rechargeguarantee.userid) AS username,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_rechargeguarantee.twoleveluserid) AS twousername,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_rechargeguarantee.threeleveluserid) AS threeusername,(SELECT vtiger_departments.departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid=vtiger_rechargeguarantee.department) AS departmentname FROM  vtiger_rechargeguarantee WHERE  deleted=0 AND domodule='{$domodule}'";
        global $adb;
        return $adb->run_query_allrecords($query);
    }
    public function getAccountChargeGuarantee(){
        $query="SELECT vtiger_accountrechargeguarantee.*,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.deletedid) AS deletedname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.createdid) AS createdname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.userid) AS username,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.twoleveluserid) AS twousername,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.threeleveluserid) AS threeusername,(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_accountrechargeguarantee.accountid) AS accountname FROM  vtiger_accountrechargeguarantee ORDER BY accountrechargeguaranteeid DESC";
        global $adb;
        return $adb->run_query_allrecords($query);
    }

    /**
     * @param Vtiger_Request $request
     * 获取用户平台信息
     */
    public function getAccountPlatform(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $recordId=$request->get('record');
        $searchdid=$request->get('searchdid');
        $pageNum=$request->get('pageNum');
        $return = array();
        if($recordId>0) {
            $searchdidSQL=' limit '.($pageNum*50).',50';
            $params=array($recordId);
            if(!empty($searchdid) && $searchdid!=''){
                $params[]='%'.$searchdid.'%';
                $searchdidSQL=' AND vtiger_accountplatform_detail.idaccount like ? limit '.($pageNum*50).',50';
            }
            $query = "SELECT 
                      vtiger_accountplatform.accountid,
                      vtiger_accountplatform_detail.accountplatform,
                      vtiger_accountplatform.accountplatformid,
                      vtiger_accountplatform.accountrebate,
                      vtiger_accountplatform.effectiveendaccount,
                      vtiger_accountplatform.effectivestartaccount,
                      vtiger_accountplatform_detail.idaccount,
                      vtiger_accountplatform.customeroriginattr,
                      vtiger_accountplatform.accountrebatetype,
                      vtiger_accountplatform.isprovideservice,
                      IFNULL(vtiger_accountplatform.rebatetype,'GoodsBack') AS rebatetype,
                      vtiger_accountplatform.modulestatus,
                      vtiger_accountplatform.supplierrebate,
                      (SELECT vtiger_products.productname FROM `vtiger_products` WHERE vtiger_products.productid=vtiger_accountplatform.productid) as topplatform,
                      vtiger_accountplatform.productid,
                      IF((SELECT
                            1
                        FROM
                            vtiger_refillapplication
                        LEFT JOIN vtiger_rechargesheet ON vtiger_rechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_refillapplication.accountid =vtiger_accountplatform.accountid
                        AND vtiger_rechargesheet.did =vtiger_accountplatform.idaccount limit 1)=1,1,0) AS rechargetypedetail,
                      vtiger_accountplatform.vendorid
                FROM vtiger_accountplatform 
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_accountplatform.accountplatformid
                LEFT JOIN vtiger_accountplatform_detail ON  vtiger_accountplatform_detail.accountplatformid=vtiger_accountplatform.accountplatformid
                WHERE vtiger_crmentity.deleted=0 
                AND vtiger_accountplatform.modulestatus='c_complete'
                AND vtiger_accountplatform.isforbidden=0
                AND vtiger_accountplatform.accountid=?".$searchdidSQL;
                //#AND vtiger_accountplatform.effectiveendaccount>=?

            $date = date('Y-m-d');
            //$result = $db->pquery($query, array($date, $recordId));
            //$result = $db->pquery($query, array($recordId));
            $result = $db->pquery($query, $params);
            while ($row = $db->fetch_array($result)) {
                /*$query='SELECT
                            1
                        FROM
                            vtiger_refillapplication
                        LEFT JOIN vtiger_rechargesheet ON vtiger_rechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_refillapplication.accountid =?
                        AND vtiger_rechargesheet.did =?';
                $thisResult=$db->pquery($query, array($row['accountid'], $row['idaccount']));
                $rechargetypedetail='OpenAnAccount';

                if($db->num_rows($thisResult)){
                    $rechargetypedetail='renew';
                }*/
                $rechargetypedetail='OpenAnAccount';
                if($row['rechargetypedetail']==1){
                    $rechargetypedetail='renew';
                }
                $tmp['accountid']=$row['accountid'];
                $tmp['accountplatform']=$row['accountplatform'];
                $tmp['accountplatformid']=$row['accountplatformid'];
                $tmp['accountrebate']=$row['accountrebate'];
                $tmp['effectiveendaccount']=$row['effectiveendaccount'];
                $tmp['effectivestartaccount']=$row['effectivestartaccount'];
                $tmp['idaccount']=$row['idaccount'];
                $tmp['modulestatus']=$row['modulestatus'];;
                $tmp['supplierrebate']=$row['supplierrebate'];;
                $tmp['topplatform']=$row['topplatform'];
                $tmp['productid']=$row['productid'];
                $tmp['vendorid']=$row['vendorid'];
                $tmp['accountrebatetype']=$row['accountrebatetype'];
                $tmp['rechargetypedetail']=$rechargetypedetail;
                $tmp['customeroriginattr']=$row['customeroriginattr'];
                $tmp['isprovideservice']=$row['isprovideservice'];
                $tmp['didcount']=$row['didcount'];
                $tmp['rebatetype']=$row['rebatetype'];
                $return[] = $tmp;
            }
        }
        return $return;
    }

    /**
     * 读取供应商银行账户及发票信息
     */
    public function getVendorBankInfo(Vtiger_Request $request){
        $record=$request->get('record');
        $rechargesource=$request->get('rechargesource');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'Vendors');
        $entity=$recordModel->getEntity();
        $column_fields=$entity->column_fields;
        $bankinfo[]=array(
            'bankaccount'=>$column_fields['bankaccount'],
            'bankcode'=>$column_fields['bankcode'],
            'bankname'=>$column_fields['bankname'],
            'banknumber'=>$column_fields['banknumber'],
            );
        $bankinfodata=$recordModel->getVendorBank($record);
        $bankinfo=array_merge($bankinfo,$bankinfodata);
        global $adb;
        $result=$adb->pquery('SELECT 
                                    purchaseinvoiceid,
                                    amountofmoney,
                                    businessname,
                                    invoicecompany,
                                    invoicecode,
                                    invoicenumber
                                     FROM vtiger_purchaseinvoice WHERE vendorid=?',array($record));
        $purchaseinvoice='';
        while($row=$adb->fetch_array($result)){
            $purchaseinvoice[]=$row;
        };
        $rechargesourceArray=array('TECHPROCUREMENT','NonMediaExtraction','PreRecharge');
        if($rechargesource=='Vendors' || $rechargesource=='COINRETURN'){
            $pageNum=$request->get('pageNum');
            $searchdidSQL=' limit '.($pageNum*50).',50';
            $accountid=$request->get('accountid');
            $searchDid=$request->get("searchDid");
            $params=array($record,$accountid);
            if(!empty($searchDid) && $searchDid!=''){
                $params[]='%'.$searchDid.'%';
                $searchdidSQL=' AND vtiger_productprovider_detail.idaccount like ? limit '.($pageNum*50).',50';
            }
            $current_date = date('Y-m-d');
            $result = $adb->pquery("SELECT vtiger_productprovider.`productid`,
                                  `vtiger_products`.productname,
                                  vtiger_productprovider.`vendorid`,
                                  vtiger_productprovider.`suppliercontractsid`,
                                  vtiger_productprovider.`supplierrebate`,
                                  vtiger_productprovider.`servicestartdate`,
                                  vtiger_productprovider.`serviceenddate`,
                                  vtiger_productprovider.`workflowstime`,
                                  vtiger_productprovider.`workflowsnode`,
                                  vtiger_productprovider_detail.idaccount,
                                  vtiger_productprovider.accountid,
                                  vtiger_productprovider_detail.accountzh,
                                  vtiger_productprovider.accountrebate,
                                  IFNULL(vtiger_productprovider.rebatetype,'GoodsBack') AS rebatetype,
                                  IFNULL(vtiger_productprovider.accountrebatetype,'GoodsBack') AS accountrebatetype,
                                  IFNULL(vtiger_suppliercontracts.contract_no,'担保合同') AS contract_no,
                                  vtiger_suppliercontracts.modulestatus,
                                  vtiger_productprovider.customeroriginattr,
                                  vtiger_productprovider.isprovideservice,
                                  vtiger_suppliercontracts.total,
                                  vtiger_suppliercontracts.signdate
                                FROM vtiger_productprovider 
                                LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_productprovider.productid 
                                LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid=vtiger_productprovider.`suppliercontractsid`
                                LEFT JOIN vtiger_productprovider_detail ON vtiger_productprovider_detail.productproviderid=vtiger_productprovider.productproviderid
                                WHERE
                                vtiger_productprovider.vendorid=? AND vtiger_productprovider.modulestatus='c_complete' AND vtiger_productprovider.isforbidden=0
                                AND vtiger_productprovider.accountid=? AND vtiger_productprovider.accountid>0 ".$searchdidSQL, $params);
                                //AND vtiger_productprovider.serviceenddate>=?
                                //AND vtiger_productprovider.accountid=? AND vtiger_productprovider.accountid>0", array($record, $current_date,$accountid));
            /*$result=$adb->pquery("SELECT vtiger_accountplatform.`productid`,
                                      `vtiger_products`.productname,
                                      vtiger_accountplatform.`vendorid`,
                                      vtiger_accountplatform.`suppliercontractsid`,
                                      vtiger_accountplatform.`supplierrebate`,
                                      vtiger_accountplatform.effectivestartaccount AS servicestartdate,
                                      vtiger_accountplatform.effectiveendaccount AS `serviceenddate`,
                                      vtiger_accountplatform.`workflowstime`,
                                      vtiger_accountplatform.`workflowsnode`,
                                      vtiger_accountplatform.idaccount,
                                      vtiger_accountplatform.accountid,
                                      vtiger_accountplatform.accountplatform AS accountzh,
                                      vtiger_accountplatform.accountrebate,
                                      vtiger_suppliercontracts.contract_no,
                                      vtiger_suppliercontracts.modulestatus,
                                      vtiger_accountplatform.customeroriginattr,
                                      vtiger_accountplatform.isprovideservice,
                                      vtiger_suppliercontracts.signdate
                                    FROM vtiger_accountplatform
                                    LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_accountplatform.productid
                                    LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid=vtiger_accountplatform.`suppliercontractsid`
                                    WHERE
                                    vtiger_accountplatform.vendorid=? AND vtiger_accountplatform.modulestatus='c_complete'
                                    AND vtiger_accountplatform.effectivestartaccount<=? AND vtiger_accountplatform.effectiveendaccount>=?",array($record,$current_date,$current_date));*/
            $productprovider = '';
            while ($row = $adb->fetch_array($result)) {
                //是否是开户的充值单
                $query = 'SELECT
                            1
                        FROM
                            vtiger_refillapplication
                        LEFT JOIN vtiger_rechargesheet ON vtiger_rechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_refillapplication.vendorid =?         
                        AND vtiger_rechargesheet.did =?';
                $thisResult = $adb->pquery($query, array($row['vendorid'], $row['idaccount']));
                $rechargetypedetail = 'OpenAnAccount';
                if ($adb->num_rows($thisResult)) {
                    $rechargetypedetail = 'renew';
                }
                $row['rechargetypedetail'] = $rechargetypedetail;
                //超期合同不能选用
                $query = 'SELECT 1 FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_suppliercontracts.suppliercontractsid=? AND vtiger_crmentity.deleted=0';
                $datetime=date('Y-m-d');
                $query.= " AND ((vtiger_suppliercontracts.modulestatus = 'c_complete' AND vtiger_suppliercontracts.effectivetime>='{$datetime}') OR (vtiger_suppliercontracts.isguarantee = 1 AND (vtiger_suppliercontracts.effectivetime IS NULL OR vtiger_suppliercontracts.effectivetime='' OR vtiger_suppliercontracts.effectivetime>='{$datetime}') AND vtiger_suppliercontracts.modulestatus IN('a_normal','b_check','b_actioning','c_stamp','c_recovered','c_receive')))";
                //return array($query);
                $thisResult = $adb->pquery($query, array($row['suppliercontractsid']));
                if ($adb->num_rows($thisResult) == 0) {
                    continue;
                }
                if($rechargesource=='COINRETURN'){
                    $trow=array();
                    $trow["accountid"]=$row["accountid"];
                    $trow["accountplatform"]=$row["accountzh"];
                    $trow["accountplatformid"]=$row["productproviderid"];
                    $trow["accountrebate"]=$row["accountrebate"];//客户返点
                    $trow["effectiveendaccount"]=$row["serviceenddate"];
                    $trow["effectivestartaccount"]=$row["servicestartdate"];
                    $trow["idaccount"]=$row["idaccount"];
                    $trow["modulestatus"]=$row["modulestatus"];
                    $trow["supplierrebate"]=$row["supplierrebate"];//供应商返点
                    $trow["topplatform"]=$row["productname"];
                    $trow["productid"]=$row["productid"];
                    $trow["vendorid"]=$row["vendorid"];
                    $trow["accountrebatetype"]=$row["accountrebatetype"];
                    $trow["rechargetypedetail"]=$rechargetypedetail;
                    $trow["customeroriginattr"]=$row["customeroriginattr"];;
                    $trow["isprovideservice"]=$row["isprovideservice"];
                    $trow["didcount"]='';
                    $trow["rebatetype"]=$row["rebatetype"];
                    $productprovider[] = $trow;
                }else{
                    $productprovider[] = $row;
                }
            };
        }elseif(in_array($rechargesource,$rechargesourceArray)){
            $productprovider=$this->getSupplierProducts($request);
        }
        $data=array('columnfields'=>$column_fields,'purchaseinvoice'=>$purchaseinvoice,'productprovider'=>$productprovider,'bankinfo'=>$bankinfo);
       return $data;
    }

    /**
     * 获取回款信息
     * @param $request
     * @return array
     * 取得回款列表
     */
    public function getReceivedPaymentsData(Vtiger_Request $request){
        $servicecontractid=$request->get('record');
        $receivedstatus=$request->get('receivedstatus');
        $rechargesource=$request->get('rechargesource');
        $current_receivedstatus="receivedstatus in('virtualrefund','normal')";
        if($rechargesource=='Accounts'){
            //$current_receivedstatus="receivedstatus in('virtualrefund','normal')";
            if($receivedstatus=='virtualrefund'){
                $current_receivedstatus="receivedstatus='virtualrefund'";
            }
        }
        $db=PearDatabase::getInstance();
        $result=$db->pquery("SELECT * FROM vtiger_receivedpayments WHERE {$current_receivedstatus} AND deleted=0 AND rechargeableamount>0 AND relatetoid=?",array($servicecontractid));
        $data=array();
        while($row=$db->fetch_array($result)){
            $row['rorigin']=(($row['receivedstatus']=='virtualrefund')?'赠'.$row['rorigin']:'正常');
            $data[]=$row;
        };
        return $data;
    }

    public function setAuditInformation($accountid,$advancesmoney,$moduleName='rechargeguarantee')
    {

        if ($advancesmoney > 0) {
            global $current_user, $adb;
            $result = $adb->pquery("SELECT advancesmoney  FROM vtiger_account WHERE accountid =?", array($accountid));

            if ($result && $adb->num_rows($result) > 0) {
                $row = $adb->fetch_array($result);
                //$advancesmoney += $row['advancesmoney'];
                $advancesmoney= bcadd($advancesmoney,$row['advancesmoney'],2);
            }
            $result = $adb->pquery("SELECT * FROM vtiger_accountrechargeguarantee WHERE deleted=0 AND accountid=?", array($accountid));
            //客户担保
            if ($adb->num_rows($result)>0) {
                $accountGuarantee = $adb->query_result_rowdata($result, 0);
                //if ($advancesmoney <= $accountGuarantee['unitprice']) {
                if (bccomp($advancesmoney,$accountGuarantee['unitprice'],2)<1) {
                    return array('flag' => true, 'userid' => array($accountGuarantee['userid']), 'advancesmoney' => $advancesmoney,'guarantee'=>'first');
                //} elseif ($advancesmoney <= $accountGuarantee['twounitprice']) {
                } elseif (bccomp($advancesmoney,$accountGuarantee['twounitprice'],2)<1) {
                    return array('flag' => true, 'userid' => array($accountGuarantee['userid'], $accountGuarantee['twoleveluserid']), 'advancesmoney' => $advancesmoney,'guarantee'=>'second');
                //} elseif ($advancesmoney <= $accountGuarantee['threeunitprice']) {
                } elseif (bccomp($advancesmoney,$accountGuarantee['threeunitprice'],2)<1) {
                    return array('flag' => true, 'userid' => array($accountGuarantee['userid'], $accountGuarantee['twoleveluserid'], $accountGuarantee['threeleveluserid']), 'advancesmoney' => $advancesmoney,'guarantee'=>'third');
                }
            } else {
                //默认担保
                //$departmentid = empty($current_user->departmentid) ? 'H1' : $current_user->departmentid;
                $departmentid=$_SESSION['userdepartmentid'];
                if(empty($departmentid)){
                    $id=$_SESSION['authenticated_user_id'];
                    if(empty($id)){
                        $id=$current_user->id;
                    }
                    $user = new Users();
                    $current_user_temp= $user->retrieveCurrentUserInfoFromFile($id);
                    $departmentid=$current_user_temp->departmentid;
                }
                /*$query = "SELECT vtiger_rechargeguarantee.userid,
                vtiger_rechargeguarantee.twoleveluserid,
                vtiger_rechargeguarantee.unitprice,
                vtiger_rechargeguarantee.twoleveluserid,
                vtiger_rechargeguarantee.threeunitprice,
                vtiger_rechargeguarantee.twounitprice
                FROM`vtiger_rechargeguarantee`
                INNER JOIN
                (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$departmentid}') AS tempdepart
                ON FIND_IN_SET(vtiger_rechargeguarantee.department,REPLACE(tempdepart.parentdepartment,'::',','))
                LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_rechargeguarantee.department
                WHERE vtiger_rechargeguarantee.deleted=0
                ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0)))
                LIMIT 1";
                $result = $adb->pquery($query, array());*/
                $accountGuarantee = $this->getRechargeGuarantee($moduleName,$departmentid);
                if(!empty($accountGuarantee)){
                /*if ($adb->num_rows($result)) {
                    $accountGuarantee = $adb->query_result_rowdata($result, 0);*/
                    if ($advancesmoney <= $accountGuarantee['unitprice']) {
                        return array('flag' => true, 'userid' => array($accountGuarantee['userid']), 'advancesmoney' => $advancesmoney,'guarantee'=>'first');
                    } elseif ($advancesmoney <= $accountGuarantee['twounitprice']) {
                        return array('flag' => true, 'userid' => array($accountGuarantee['userid'], $accountGuarantee['twoleveluserid']), 'advancesmoney' => $advancesmoney,'guarantee'=>'second');
                    } elseif ($advancesmoney <= $accountGuarantee['threeunitprice']) {
                        return array('flag' => true, 'userid' => array($accountGuarantee['userid'], $accountGuarantee['twoleveluserid'], $accountGuarantee['threeleveluserid']), 'advancesmoney' => $advancesmoney,'guarantee'=>'third');
                    }
                }
            }
            return array('flag' => false);
        }
        return array('flag' => true);
    }
    public function getPDF() {
        $recordId = $this->getId();
        $moduleName = $this->getModuleName();

        $controllerClassName = "Vtiger_". $moduleName ."PDFController";

        $controller = new $controllerClassName($moduleName);
        $controller->loadRecord($recordId);

        $fileName = $moduleName.'_'.getModuleSequenceNumber($moduleName, $recordId);
        $controller->Output($fileName.'.pdf', 'D');
    }

    /**
     * 红冲退款显示
     * @param Vtiger_Request $request
     * @param $tpl
     * @author: steel.liu
     * @Date: 2018/4/19 18:10
     *
     */
    public function setDataPreRechargeDisplay(Vtiger_Request $request){
        $rechargesheetid=$request->get('rechargesheetid');
        $db=PearDatabase::getInstance();
        $result=$db->pquery('SELECT * FROM `vtiger_rechargesheet` WHERE deleted=0 AND transferamount>refundamount AND rechargesheetid=? limit 1',array($rechargesheetid));
        $data=$db->raw_query_result_rowdata($result,0);
        $rubricre_res = $this->getRubricreChargesheet($data['refillapplicationid']);

        $factorage_tmp = 0;
        $taxation_tmp = 0;
        $activationfee_tmp = 0;
        foreach ($rubricre_res[$rechargesheetid] as $val){
            $factorage_tmp = bcadd($val['factorage'],$factorage_tmp,2);
            $taxation_tmp = bcadd($val['taxation'], $taxation_tmp,2);
            $activationfee_tmp = bcadd($val['activationfee'], $activationfee_tmp,2);
        }

        $data['factorage'] = bcsub($data['factorage'],$factorage_tmp,2);
        $data['taxation'] = bcsub($data['taxation'],$taxation_tmp,2);
        $data['activationfee'] = bcsub($data['activationfee'],$activationfee_tmp,2);
        if($db->num_rows($result)==0){
            echo "<h3 style='text-align: center;color:red;'>金额已使用完</h3>";
            exit;
        }
        $detailView=new RefillApplication_Edit_View();
        $viewer = $detailView->getViewer ($request);
        $moduleName = $request->getModule();
        $record = $request->get('record');
        if(!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
        }else if(!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
            $viewer->assign('RECORD_ID','');
        }
        if(!$this->record){
            $this->record = $recordModel;
        }

        $moduleModel = $recordModel->getModule();
        //读取模块的字段
        $fieldList = $moduleModel->getFields();

        //取交集?还不知道有什么用
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        if(!empty($requestFieldList)){
            foreach($requestFieldList as $fieldName=>$fieldValue){
                $fieldModel = $fieldList[$fieldName];
                if($fieldModel->isEditable()) {
                    $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
                }
            }
        }
        $rechargesource=$recordModel->get('rechargesource');
        $rechargesource=($request->get('rechargesource')=='Vendors' || $rechargesource=='Vendors')?'Vendors':
            (($request->get('rechargesource')=='TECHPROCUREMENT' || $rechargesource=='TECHPROCUREMENT')?'TECHPROCUREMENT':
                (($request->get('rechargesource')=='PreRecharge' || $rechargesource=='PreRecharge')?'PreRecharge':
                    (($request->get('rechargesource')=='OtherProcurement' || $rechargesource=='OtherProcurement')?'OtherProcurement':
                        (($request->get('rechargesource')=='NonMediaExtraction' || $rechargesource=='NonMediaExtraction')?'NonMediaExtraction':
                            (($request->get('rechargesource')=='PACKVENDORS' || $rechargesource=='PACKVENDORS')?'PACKVENDORS':
                                'Accounts')))));
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,'Edit');
        $productserviceValue='';
        /*if($rechargesource=='Vendors'){
            $supplierRefundData=$this->getPaymentsSupplierRefund($recordModel->get('vendorid'));
            if(empty($supplierRefundData)){
                echo "<h3 style='text-align: center;color:red;'>供应商退款未匹配或退款金额用完!</h3>";
                exit;
            }
            $viewer->assign('SUPPLIERREFUNDDATA',$supplierRefundData);
        }*/
        $moduleArr=array('Vendors','PreRecharge','TECHPROCUREMENT','NonMediaExtraction');
        if(in_array($rechargesource,$moduleArr)){
            $productserviceValue=$recordStructureInstance->getRecord()->getModule()->getFields();
            $productserviceValue=$productserviceValue['productid'];
            $productserviceValue=$productserviceValue->getEditViewDisplayValue($recordModel->get('productservice'));
        }
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
        $viewer->assign('RECHARGESOURCE', $rechargesource);
        $viewer->assign('ISENTITY', $data['isentity']);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('PRODUCTSERVICEVALUE', $productserviceValue);
        $viewer->assign('DATARESULT', $data);
        $detailDisplayList=array('productid'=>'topplatform','suppliercontractsid'=>'suppliercontractsname');
        $checkValueList=array('havesignedcontract','isprovideservice');
        //可红充金额
        $actualtotalrecharge=$recordModel->get('actualtotalrecharge');//实际充值金额
        $totalrecharge=$recordModel->get('totalrecharge');//现金金额
        $viewer->assign('DISPLAYFIELD', array_keys($detailDisplayList));
        $viewer->assign('DISPLAYVALUE', $detailDisplayList);
        $viewer->assign('CHECKVALUELIST', $checkValueList);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());//执行好多次，真是
        $viewer->assign('RECORD',$recordModel);//编辑页面显示不可编辑字段内容
        $viewer->assign('ACTUALTOTALRECHARGE',$actualtotalrecharge);
        $viewer->assign('TOTALRECHARGE',$totalrecharge);
        $isRelationOperation = $request->get('relationOperation');
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }

        //使用上传控件
        //$viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        //$viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        return $viewer->view('EditViewBlocksBack.tpl', $moduleName,true);

    }
    public function getRubricreChargesheet($refillapplicationid){
        global $adb;
        $query='SELECT * FROM `vtiger_rubricrechargesheet` WHERE refillapplicationid=? AND deleted=0';
        $result=$adb->pquery($query,array($refillapplicationid));
        $arr=array();
        while($row=$adb->fetch_array($result)){
            $arr[$row['rechargesheetid']][]=$row;
        }
        $query='SELECT vtiger_refillapprayment.*,
                    vtiger_refillredrefund.backwashtotal as tempbackwashtotal,
                    vtiger_refillredrefund.mstatus,
                    vtiger_refillredrefund.rechargesheetid 
                FROM vtiger_refillredrefund 
                LEFT JOIN vtiger_refillapprayment ON vtiger_refillredrefund.refillappraymentid=vtiger_refillapprayment.refillappraymentid 
                WHERE vtiger_refillredrefund.refillapplicationid=?  AND vtiger_refillredrefund.deleted=0 AND vtiger_refillapprayment.deleted=0 AND vtiger_refillredrefund.isshow=1';
        $result=$adb->pquery($query,array($refillapplicationid));
        while($row=$adb->fetch_array($result)){
            $arr['refillredrefund'][$row['rechargesheetid']][]=$row;
        }
        return $arr;
    }
    public function getSalesorderPayments(Vtiger_Request $request){
        $salesorderid=$request->get('record');
        global $adb;
        $query='SELECT vtiger_salesorderproductsrel.salesorderproductsrelid,vtiger_salesorderproductsrel.productid,vtiger_products.productname,vtiger_salesorderproductsrel.purchasemount,vtiger_salesorderproductsrel.costofuse FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_salesorderproductsrel.productid WHERE salesorderid=? AND multistatus=3 AND (vtiger_salesorderproductsrel.purchasemount-vtiger_salesorderproductsrel.costofuse)>0';
        $result=$adb->pquery($query,array($salesorderid));
        $arr=array();
        while($row=$adb->fetch_array($result)){
            $row['rechargeableamount']=$row['purchasemount']-$row['costofuse'];
            $row['receivedpaymentsid']=$row['salesorderproductsrelid'];
            $arr[]=$row;
        }
        return $arr;
    }
    public function dorefundsOrTransfers($recordid){
        $query='SELECT * FROM `vtiger_rubricrechargesheet` WHERE deleted=0 AND refillapplicationid=? AND isbackwash=1';
        $db=PearDatabase::getInstance();
        $result1=$db->pquery($query,array($recordid));
        $num=$db->num_rows($result1);
        $query='SELECT * FROM `vtiger_refillredrefund` WHERE refillapplicationid=? AND deleted=0';
        $result=$db->pquery($query,array($recordid));
        $refundAmountSum=0;//红充对应的现金款额
        $updaterefillprayment=array();//明细回款表
        $tempbackwashtotal=array();
        $updatereceivedpayments=array();
        $tempbackwashtotalpayments=array();
        while($row=$db->fetch_array($result)){
            $backwashtotalin=$backwashtotalinpay=$row['backwashtotal'];
            if($row['mstatus']=='normal'){
                $refundAmountSum+=$backwashtotalin;

            }else{
                $backwashtotalin=0;
            }
            if(empty($tempbackwashtotal[$row['refillappraymentid']])){
                $tempbackwashtotal[$row['refillappraymentid']]=$backwashtotalin;
            }else{
                $tempbackwashtotal[$row['refillappraymentid']]+=$backwashtotalin;
            }
            $row['backwashtotal']=$tempbackwashtotal[$row['refillappraymentid']];
            $updaterefillprayment[$row['refillappraymentid']]=$row;
            if(empty($tempbackwashtotalpayments[$row['receivedpaymentsid']])){
                $tempbackwashtotalpayments[$row['receivedpaymentsid']]=$backwashtotalinpay;
            }else{
                $tempbackwashtotalpayments[$row['receivedpaymentsid']]+=$backwashtotalinpay;
            }

            $row['backwashtotal']=$tempbackwashtotalpayments[$row['receivedpaymentsid']];
            $updatereceivedpayments[$row['receivedpaymentsid']]=$row;
        }
        $mrefundamount=0;
        $amountpayable=0;
        for($i=0;$i<$num;$i++){
            $rubricreData=$db->raw_query_result_rowdata($result1,$i);
            $refundamountT=$rubricreData['refundamount'];
            $mrefundamount=bcadd($mrefundamount,$refundamountT,2);//红充的应付款额
            $rechargesheetid=$rubricreData['rechargesheetid'];
            $amountpayableT=$rubricreData['amountpayable'];
            $amountpayable=bcadd($amountpayable,$amountpayableT,2);//代理商退款
            $db->pquery("UPDATE vtiger_rechargesheet SET refundamount=if((refundamount+{$refundamountT})>transferamount,transferamount,(refundamount+{$refundamountT})),amountpayablesum=(amountpayablesum+{$amountpayableT}),amountpayable={$amountpayableT} WHERE rechargesheetid=?",array($rechargesheetid));
        }
        $rechargesource=$this->get('rechargesource');
        $actualtotalrecharge=$this->get('actualtotalrecharge');//回款的总额
        $totalrecharge=$this->get('totalrecharge');//现金金额
        $totalreceivables=$this->get('totalreceivables');//应付款金额

        $refenddiff=$mrefundamount-$refundAmountSum;//红充差额


        $totalreceivables=$totalreceivables-$amountpayable;
        $sql='';
        if($rechargesource=='Vendors'){
            $sql=$totalreceivables>0?$totalreceivables:0;
            $sql="totalreceivables={$sql},";
        }

        foreach($updaterefillprayment as $value) {
            $refillappraymentid=$value['refillappraymentid'];
            $refundamountd=$value['backwashtotal'];
            if($refundamountd<=0){
                //continue;
            }
            if($value['mstatus']=='normal'){
                $db->pquery('UPDATE
                                  `vtiger_refillapprayment` 
                            SET backwashtotal=if((backwashtotal-'.$refundamountd.')>0,backwashtotal-'.$refundamountd.',0),
                            refundamount=if((refundamount+'.$refundamountd.')>refillapptotal,refillapptotal,refundamount+'.$refundamountd.') 
                            WHERE refillappraymentid=?',array($refillappraymentid));
            }
            $db->pquery("UPDATE vtiger_refillredrefund SET deleted=1 WHERE refillappraymentid=?",array($refillappraymentid));
        }
        $refillapplicationno=$this->get('refillapplicationno');
        foreach($updatereceivedpayments as $value){
            $refundamountd=$value['backwashtotal'];
            if($refundamountd<=0){
                continue;
            }
            $receivedpaymentsid=$value['receivedpaymentsid'];
            $receivedPaymentsRecordModel=Vtiger_Record_Model::getInstanceById($receivedpaymentsid,'ReceivedPayments');
            $rechargeableamount=$receivedPaymentsRecordModel->get('rechargeableamount');
            if($value['mstatus']=='normal'){
                $db->pquery("UPDATE `vtiger_receivedpayments` SET rechargeableamount=if((rechargeableamount+{$refundamountd})>unit_price,unit_price,(rechargeableamount+{$refundamountd})) WHERE receivedpaymentsid=?",array($receivedpaymentsid));
                $this->setTracker('ReceivedPayments',$receivedpaymentsid,array('fieldName'=>'rechargeableamount','oldValue'=>$rechargeableamount,'currentValue'=>($rechargeableamount+$refundamountd).'充值回款红冲'.$refillapplicationno));
            }else{
                $db->pquery("UPDATE `vtiger_receivedpayments` SET rechargeableamount=if((rechargeableamount-{$refundamountd})<=0,0,(rechargeableamount-{$refundamountd})) WHERE receivedpaymentsid=?",array($receivedpaymentsid));
                $this->setTracker('ReceivedPayments',$receivedpaymentsid,array('fieldName'=>'rechargeableamount','oldValue'=>$rechargeableamount,'currentValue'=>($rechargeableamount-$refundamountd).'充值回款红冲'.$refillapplicationno));
            }


        }
        if(bccomp($actualtotalrecharge,$totalrecharge)==0){
            //无垫款
            $amountofsalesSQL='';
            if($this->get('financialstate')==1){
                $amountofsales=$this->get('amountofsales');
                $amountofsales=$amountofsales-$mrefundamount+$refundAmountSum;
                $amountofsales=$amountofsales>0?$amountofsales:0;
                $amountofsalesSQL='amountofsales='.$amountofsales.',';
                $refundAmountSum=$mrefundamount;
            }
            $difftemp=$actualtotalrecharge-$refundAmountSum;
            $db->pquery("UPDATE vtiger_refillapplication SET {$sql}{$amountofsalesSQL}totalrecharge=?,actualtotalrecharge=? WHERE refillapplicationid=?",array($difftemp,$difftemp,$recordid));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'totalrecharge','oldValue'=>$totalrecharge,'currentValue'=>$difftemp.'红冲'));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'actualtotalrecharge','oldValue'=>$actualtotalrecharge,'currentValue'=>$difftemp.'红冲'));
        }elseif(bccomp($actualtotalrecharge,$totalrecharge)>0 && $totalrecharge>0){
            //部分垫款
            //1红充==实际的充值现金
            $adifftemp=$actualtotalrecharge-$mrefundamount;
            $tdifftemp=$totalrecharge-$refundAmountSum;
            $newgrossadvances=$adifftemp-$tdifftemp;
            $grossadvances=$this->get('grossadvances');
            $db->pquery("UPDATE vtiger_refillapplication SET {$sql}totalrecharge=?,actualtotalrecharge=?,grossadvances=? WHERE refillapplicationid=?",array($tdifftemp,$adifftemp,$newgrossadvances,$recordid));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'totalrecharge','oldValue'=>$totalrecharge,'currentValue'=>$tdifftemp.'红冲'));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'actualtotalrecharge','oldValue'=>$actualtotalrecharge,'currentValue'=>$adifftemp.'红冲'));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'grossadvances','oldValue'=>$grossadvances,'currentValue'=>$newgrossadvances.'红冲'));

            if(bccomp($mrefundamount,$refundAmountSum)>0){
                $accountRecordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
                $accountRecordModel->setAdvancesmoney($this->get('accountid'),$refenddiff*-1,'回款红充');
            }

        }elseif(bccomp($actualtotalrecharge,$totalrecharge)>0 && $totalrecharge==0){
            //全部垫款
            $difftemp=$actualtotalrecharge-$mrefundamount;
            $grossadvances=$this->get('grossadvances');
            $db->pquery("UPDATE vtiger_refillapplication SET {$sql}actualtotalrecharge=?,grossadvances=? WHERE refillapplicationid=?",array($difftemp,$difftemp,$recordid));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'actualtotalrecharge','oldValue'=>$actualtotalrecharge,'currentValue'=>$difftemp.'红冲'));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'grossadvances','oldValue'=>$grossadvances,'currentValue'=>$difftemp.'红冲'));
            $accountRecordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
            $accountRecordModel->setAdvancesmoney($this->get('accountid'),$mrefundamount*-1,'回款红充');
        }
        $datetime=date('Y-m-d H:i:s');
        $db->pquery("UPDATE vtiger_rubricrechargesheet SET isbackwash=0 WHERE isbackwash=1 AND refillapplicationid=?",array($recordid));
        $db->pquery("UPDATE vtiger_refillapplication SET isbackwash=0,iscushion=if(grossadvances=0,0,1) WHERE refillapplicationid=?", array($recordid));
        $query="UPDATE `vtiger_refillapprayment` SET iscomplete=1 WHERE refillapplicationid=?";
        $db->pquery($query,array($recordid));
    }
    public function dorefundsOrTransfersBak($recordid){
        $query='SELECT * FROM `vtiger_rubricrechargesheet` WHERE deleted=0 AND refillapplicationid=? AND isbackwash=1';
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array($recordid));
        $rubricreData=$db->raw_query_result_rowdata($result,0);
        $query='SELECT * FROM `vtiger_refillredrefund` WHERE refillapplicationid=? AND deleted=0';
        $result=$db->pquery($query,array($recordid));
        $refundAmountSum=0;//红充对应的现金款额
        $updaterefillprayment=array();
        while($row=$db->fetch_array($result)){
            if($row['mstatus']=='normal'){
                $refundAmountSum+=$row['backwashtotal'];
            }

            $updaterefillprayment[$row['refillappraymentid']]=$row;
        }

        $rechargesource=$this->get('rechargesource');
        $actualtotalrecharge=$this->get('actualtotalrecharge');//实际充值金额
        $totalrecharge=$this->get('totalrecharge');//现金金额
        $totalreceivables=$this->get('totalreceivables');//应付款金额
        $mrefundamount=$rubricreData['refundamount'];//红充的应付款额
        $refenddiff=$mrefundamount-$refundAmountSum;//红充差额
        $rechargesheetid=$rubricreData['rechargesheetid'];
        $amountpayable=$rubricreData['amountpayable'];//代理商退款
        $totalreceivables=$totalreceivables-$amountpayable;
        $sql='';
        if($rechargesource=='Vendors'){
            $sql=$totalreceivables>0?$totalreceivables:0;
            $sql="totalreceivables={$sql},";
        }

        foreach($updaterefillprayment as $value) {
            $refillappraymentid=$value['refillappraymentid'];
            $refundamountd=$value['backwashtotal'];
            if($refundamountd<=0){
                continue;
            }

            $receivedpaymentsid=$value['receivedpaymentsid'];
            $receivedPaymentsRecordModel=Vtiger_Record_Model::getInstanceById($receivedpaymentsid,'ReceivedPayments');
            $rechargeableamount=$receivedPaymentsRecordModel->get('rechargeableamount');
            if($value['mstatus']=='normal'){
                $db->pquery('UPDATE
                                  `vtiger_refillapprayment` 
                            SET backwashtotal=if((backwashtotal-'.$refundamountd.')>0,backwashtotal-'.$refundamountd.',0),
                            refundamount=if((refundamount+'.$refundamountd.')>refillapptotal,refillapptotal,refundamount+'.$refundamountd.') 
                            WHERE refillappraymentid=?',array($refillappraymentid));
                $db->pquery("UPDATE `vtiger_receivedpayments` SET rechargeableamount=if((rechargeableamount+{$refundamountd})>unit_price,unit_price,(rechargeableamount+{$refundamountd})) WHERE receivedpaymentsid=?",array($receivedpaymentsid));
            }else{
                $db->pquery("UPDATE `vtiger_receivedpayments` SET rechargeableamount=if((rechargeableamount-{$refundamountd})<=0,0,(rechargeableamount-{$refundamountd})) WHERE receivedpaymentsid=?",array($receivedpaymentsid));
            }
            $db->pquery("UPDATE vtiger_refillredrefund SET deleted=1 WHERE refillappraymentid=?",array($refillappraymentid));
            $this->setTracker('ReceivedPayments',$receivedpaymentsid,array('fieldName'=>'rechargeableamount','oldValue'=>$rechargeableamount,'currentValue'=>($rechargeableamount+$refundamountd).'充值回款红冲'));

        }
        if(bccomp($actualtotalrecharge,$totalrecharge)==0){
            //无垫款
            $amountofsalesSQL='';
            if($this->get('financialstate')==1){
                $amountofsales=$this->get('amountofsales');
                $amountofsales=$amountofsales-$mrefundamount+$refundAmountSum;
                $amountofsales=$amountofsales>0?$amountofsales:0;
                $amountofsalesSQL='amountofsales='.$amountofsales.',';
                $refundAmountSum=$mrefundamount;
            }
            $difftemp=$actualtotalrecharge-$refundAmountSum;
            $db->pquery("UPDATE vtiger_refillapplication SET {$sql}{$amountofsalesSQL}totalrecharge=?,actualtotalrecharge=? WHERE refillapplicationid=?",array($difftemp,$difftemp,$recordid));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'totalrecharge','oldValue'=>$totalrecharge,'currentValue'=>$difftemp.'红冲'));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'actualtotalrecharge','oldValue'=>$actualtotalrecharge,'currentValue'=>$difftemp.'红冲'));
        }elseif(bccomp($actualtotalrecharge,$totalrecharge)>0 && $totalrecharge>0){
            //部分垫款
            //1红充==实际的充值现金
            $adifftemp=$actualtotalrecharge-$mrefundamount;
            $tdifftemp=$totalrecharge-$refundAmountSum;
            $newgrossadvances=$adifftemp-$tdifftemp;
            $grossadvances=$this->get('grossadvances');
            $db->pquery("UPDATE vtiger_refillapplication SET {$sql}totalrecharge=?,actualtotalrecharge=?,grossadvances=? WHERE refillapplicationid=?",array($tdifftemp,$adifftemp,$newgrossadvances,$recordid));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'totalrecharge','oldValue'=>$totalrecharge,'currentValue'=>$tdifftemp.'红冲'));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'actualtotalrecharge','oldValue'=>$actualtotalrecharge,'currentValue'=>$adifftemp.'红冲'));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'grossadvances','oldValue'=>$grossadvances,'currentValue'=>$adifftemp.'红冲'));

            if(bccomp($mrefundamount,$refundAmountSum)>0){
                $accountRecordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
                $accountRecordModel->setAdvancesmoney($this->get('accountid'),$refenddiff*-1,'回款红充');
            }

        }elseif(bccomp($actualtotalrecharge,$totalrecharge)>0 && $totalrecharge==0){
            //全部垫款

            $difftemp=$actualtotalrecharge-$mrefundamount;
            $grossadvances=$this->get('grossadvances');
            $db->pquery("UPDATE vtiger_refillapplication SET {$sql}actualtotalrecharge=?,grossadvances=? WHERE refillapplicationid=?",array($difftemp,$difftemp,$recordid));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'actualtotalrecharge','oldValue'=>$actualtotalrecharge,'currentValue'=>$difftemp.'红冲'));
            $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'grossadvances','oldValue'=>$grossadvances,'currentValue'=>$difftemp.'红冲'));
            $accountRecordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
            $accountRecordModel->setAdvancesmoney($this->get('accountid'),$mrefundamount*-1,'回款红充');
        }
        $datetime=date('Y-m-d H:i:s');
        $db->pquery("UPDATE vtiger_rubricrechargesheet SET isbackwash=0,refundtime=? WHERE refillapplicationid=?",array($datetime,$recordid));
        $db->pquery("UPDATE vtiger_rechargesheet SET refundamount=if((refundamount+{$mrefundamount})>transferamount,transferamount,(refundamount+{$mrefundamount})),amountpayablesum=(amountpayablesum+{$amountpayable}),amountpayable={$amountpayable} WHERE rechargesheetid=?",array($rechargesheetid));
        $db->pquery("UPDATE vtiger_refillapplication SET isbackwash=0,iscushion=if(grossadvances=0,0,1) WHERE refillapplicationid=?", array($recordid));

    }
    public function setTracker($sourceModule, $sourceId, $array,$table='') {
        global $adb, $current_user;
        $currentTime = date('Y-m-d H:i:s');
        if(!empty($table)){
            $sql = "SELECT * FROM {$table['tablename']} WHERE {$table['fieldName']}=? LIMIT 1";
            $sel_result = $adb->pquery($sql, array($sourceId));
            if($adb->num_rows($sel_result)){
                $row = $adb->query_result_rowdata($sel_result, 0);
                $array['oldValue']=$row[$array['fieldName']];
            }
        }
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $sourceId, $sourceModule, $current_user->id, $currentTime, 0));
        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
            Array($id, $array['fieldName'], $array['oldValue'], $array['currentValue']));
    }

    /**
     * 已充值的合同总额
     * @param $servicecontractsid
     * @return mixed
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function getSumActualtotalrecharge($servicecontractsid){
        $query="SELECT sum(actualtotalrecharge) as actualtotalrecharge FROM `vtiger_refillapplication` LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid WHERE vtiger_crmentity.deleted=0 AND vtiger_refillapplication.modulestatus NOT in('a_exception','c_cancel') AND vtiger_refillapplication.servicecontractsid=?";
        global $adb;
        $result=$adb->pquery($query,array($servicecontractsid));
        $data=$adb->query_result_rowdata($result,0);
        return $data['actualtotalrecharge']>0?$data['actualtotalrecharge']:0;
    }

    /**
     * 查询供应商关联的充值申请单信息
     */
    public function getListAboutSupplierContractRefillapplication($suppliercontractsid,$rechargesource){
    $sql='      SELECT
                    vtiger_refillapplication.refillapplicationid,vtiger_refillapplication.refillapplicationno,vtiger_refillapplication.rechargesource,IFNULL((select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',(if(`status`=\'Active\',\'\',\'[离职]\'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),\'--\') as smownerid,vtiger_crmentity.createdtime,vtiger_refillapplication.grossadvances,vtiger_refillapplication.actualtotalrecharge,vtiger_refillapplication.totalreceivables 
                FROM
                    vtiger_refillapplication
                LEFT JOIN vtiger_crmentity ON vtiger_refillapplication.refillapplicationid = vtiger_crmentity.crmid
                LEFT JOIN vtiger_rechargesheet ON vtiger_refillapplication.refillapplicationid = vtiger_rechargesheet.refillapplicationid
                WHERE
                    1 = 1
                    AND vtiger_crmentity.deleted = 0
                    AND vtiger_rechargesheet.deleted = 0
                    AND vtiger_rechargesheet.suppliercontractsid=?
                    AND vtiger_refillapplication.modulestatus="c_complete"
                    AND vtiger_refillapplication.rechargesource=?
                GROUP BY
                    vtiger_refillapplication.refillapplicationid ';
        global $adb;
        $result=$adb->pquery($sql,array($suppliercontractsid,$rechargesource));
        $list=array();
        while ($rowData=$adb->fetch_array($result)){
            $list[]= $rowData;
        }
        return $list;
    }
    /**
     * 查询供应商合同已充值总额
     */
    public function getSumTotalreceivables($suppliercontractsid){
        // 因为使用的金额字段不一样 所以求和时  直接求和 累加 但是但是不同类型的充值申请单值为零 所以累加就相当于单个类型的某个金额求和
        $sql='  SELECT
                    SUM(vtiger_rechargesheet.servicecost+vtiger_rechargesheet.amountpayable+vtiger_rechargesheet.purchaseamount) as servicecost
                FROM
                    vtiger_refillapplication
                LEFT JOIN vtiger_crmentity ON vtiger_refillapplication.refillapplicationid = vtiger_crmentity.crmid
                LEFT JOIN vtiger_rechargesheet ON vtiger_refillapplication.refillapplicationid = vtiger_rechargesheet.refillapplicationid
                WHERE
                    1 = 1
                    AND vtiger_crmentity.deleted = 0
                    AND vtiger_rechargesheet.deleted = 0
                    AND vtiger_rechargesheet.suppliercontractsid=?
                    AND vtiger_refillapplication.modulestatus NOT in("a_exception","c_cancel")
                ';
        global $adb;
        $result=$adb->pquery($sql,array($suppliercontractsid));
        $data=$adb->query_result_rowdata($result,0);
        $servicecost=$data['servicecost'];
        $sql='SELECT
                    SUM(vtiger_rubricrechargesheet.amountpayable) as amountpayable
              FROM
                    vtiger_refillapplication
              LEFT JOIN vtiger_crmentity ON vtiger_refillapplication.refillapplicationid = vtiger_crmentity.crmid
              LEFT JOIN vtiger_rechargesheet ON vtiger_refillapplication.refillapplicationid = vtiger_rechargesheet.refillapplicationid
              LEFT JOIN vtiger_rubricrechargesheet ON (vtiger_refillapplication.refillapplicationid = vtiger_rubricrechargesheet.refillapplicationid AND vtiger_rubricrechargesheet.rechargesheetid=vtiger_rechargesheet.rechargesheetid )
              WHERE
              1 = 1
              AND vtiger_crmentity.deleted = 0
              AND vtiger_rechargesheet.deleted = 0
              AND vtiger_rubricrechargesheet.deleted=0
              AND vtiger_rechargesheet.suppliercontractsid=?
              AND vtiger_refillapplication.modulestatus NOT in("a_exception","c_cancel")';
        $result=$adb->pquery($sql,array($suppliercontractsid));
        $data=$adb->query_result_rowdata($result,0);
        $amountpayable=$data['amountpayable'];
        $total=$servicecost-$amountpayable;
        return $total;
    }
    /**
     * 取得供应商对应的退款记录
     * @param $contractsid
     * @return array
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function getPaymentsSupplierRefund($contractsid){
        //$contractsid=579440;
        //$query="SELECT * FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.accountid=? AND vtiger_receivedpayments.deleted=0 AND vtiger_receivedpayments.receivedstatus='SupplierRefund' AND vtiger_receivedpayments.rechargeableamount>0";
        $query="SELECT *,IFNULL((SELECT sum(vtiger_refillredrefund.backwashtotal) FROM vtiger_refillredrefund WHERE vtiger_refillredrefund.mstatus='SupplierRefund' AND vtiger_refillredrefund.deleted=0 AND vtiger_refillredrefund.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid),0) as backwashtotal FROM vtiger_receivedpayments WHERE  vtiger_receivedpayments.deleted=0 AND vtiger_receivedpayments.accountid=? AND vtiger_receivedpayments.receivedstatus='SupplierRefund' AND (vtiger_receivedpayments.rechargeableamount-IFNULL((SELECT sum(vtiger_refillredrefund.backwashtotal) FROM vtiger_refillredrefund WHERE vtiger_refillredrefund.mstatus='SupplierRefund' AND vtiger_refillredrefund.deleted=0 AND vtiger_refillredrefund.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid),0))>0";
        //$query="SELECT * FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.maybe_account=? AND vtiger_receivedpayments.maybe_account>0 AND vtiger_receivedpayments.deleted=0 AND vtiger_receivedpayments.receivedstatus='normal' AND vtiger_receivedpayments.rechargeableamount>0";
        global $adb;
        $result=$adb->pquery($query,array($contractsid));
        $data=array();
        while($row=$adb->fetch_array($result)){
            $data[]=$row;
        }
        return $data;
    }

    /**
     * 验证已充值的合同金额是否大于合同金额
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function AmountRepaidContract($recordid,$array)
    {
        global $adb;
        $recordSql = '';
        if ($recordid > 0) {
            $recordSql = ' AND vtiger_refillapplication.refillapplicationid!=' . $recordid;
        }
        foreach ($array as $key => $value) {
            $query = "SELECT vtiger_suppliercontracts.total FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_crmentity.deleted=0 AND suppliercontractsid=?";
            $result = $adb->pquery($query, array($key));
            $data = $adb->raw_query_result_rowdata($result, 0);
            $suppliercontractstotal = $data['total'];
            $query = "SELECT sum(vtiger_rechargesheet.servicecost-vtiger_rechargesheet.refundamount+vtiger_rechargesheet.amountpayable+vtiger_rechargesheet.purchaseamount+vtiger_rechargesheet.rechargeamount) AS servicecost FROM vtiger_refillapplication LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid LEFT JOIN vtiger_rechargesheet on vtiger_rechargesheet.refillapplicationid=vtiger_refillapplication.refillapplicationid WHERE vtiger_crmentity.deleted=0 AND vtiger_rechargesheet.deleted=0 and vtiger_refillapplication.modulestatus not in('c_cancel','a_exception') AND
        vtiger_rechargesheet.suppliercontractsid=?" . $recordSql;
            $result = $adb->pquery($query, array($key));
            $data = $adb->raw_query_result_rowdata($result, 0);
            $transferamount = $data['servicecost'];
            if ($suppliercontractstotal == 0) {

            } else {
                $refilltotal = bcadd($value, $transferamount);
                if (bccomp($suppliercontractstotal, $refilltotal,2) < 0) {
                    return true;
                }
            }
        }
    }
    public function getSupplierProducts(Vtiger_Request $request){
        $recordid=$request->get('record');
        global $adb,$current_user;
        /*$rechargesource=$request->get('rechargesource');
        if($rechargesource=='PreRecharge'){
            //$modulestatus=" AND vtiger_suppliercontracts.modulestatus='c_complete' AND vtiger_vendor.vendortype='MediaProvider'";
            $modulestatus=" AND vtiger_suppliercontracts.modulestatus='c_complete'";
        }else{*/

            //$modulestatus=" AND (vtiger_suppliercontracts.modulestatus='c_complete' OR vtiger_suppliercontracts.isguarantee=1)";
            $dateTime=date('Y-m-d');
            $modulestatus = " AND ((vtiger_suppliercontracts.modulestatus='c_complete' AND vtiger_suppliercontracts.effectivetime>='{$dateTime}') OR (vtiger_suppliercontracts.isguarantee=1 AND vtiger_suppliercontracts.effectivetime>='{$dateTime}') OR (vtiger_suppliercontracts.isguarantee=1 AND vtiger_suppliercontracts.effectivetime>='{$dateTime}') OR (vtiger_suppliercontracts.isguarantee=1 AND (vtiger_suppliercontracts.effectivetime IS NULL OR vtiger_suppliercontracts.effectivetime='')))";
        /*}*/
        $query="SELECT 
                vtiger_suppliercontracts.suppliercontractsid,
                IFNULL(vtiger_suppliercontracts.contract_no,'采购合同担保') AS contract_no,
                vtiger_vendorsrebate.productid,
                vtiger_suppliercontracts.signdate,
                vtiger_suppliercontracts.`vendorid`,
                vtiger_vendorsrebate.rebate AS supplierrebate,
                '' AS `servicestartdate`,
                '' AS `serviceenddate`,
                '' AS `workflowstime`,
                '' AS `workflowsnode`,
                IFNULL(vtiger_suppliercontracts.contract_no,'采购合同担保') AS idaccount,
                IFNULL(vtiger_vendorsrebate.rebatetype,'GoodsBack') AS rebatetype,
                '' AS accountid,
                '' AS accountzh,
                '' AS accountrebate,
                vtiger_suppliercontracts.modulestatus,
                '' AS customeroriginattr,
                '' AS isprovideservice,
                vtiger_suppliercontracts.total,
                vtiger_suppliercontracts.signdate,
                (SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid=vtiger_vendorsrebate.productid) AS productname 
                FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid 
                LEFT JOIN vtiger_vendorsrebate ON vtiger_vendorsrebate.suppliercontractsid=vtiger_suppliercontracts.suppliercontractsid
                LEFT JOIN vtiger_vendor ON vtiger_suppliercontracts.vendorid=vtiger_vendor.vendorid
                WHERE vtiger_crmentity.deleted=0
                {$modulestatus}
                AND vtiger_vendorsrebate.productid>0
                AND vtiger_vendorsrebate.enddate>=?
                AND vtiger_suppliercontracts.vendorid=?
                GROUP BY vtiger_vendorsrebate.productid,vtiger_suppliercontracts.suppliercontractsid
                ";
        $datetime=date('Y-m-d');
        $result=$adb->pquery($query,array($datetime,$recordid));
        $data=array();
        while($row=$adb->fetch_array($result)) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 技术充值验证
     * 验证充值金额与当前成本之间的关系
     */
    public function checkTechprocurement(Vtiger_Request $request){
        global $adb;
        $salesorderid=$request->get('salesorderid');
        $totalreceivables=$request->get('totalreceivables');
        $query='SELECT sum(IFNULL(purchasemount,0)) as purchasemount,SUM(IFNULL(costofuse,0)) AS costofuse FROM vtiger_salesorderproductsrel WHERE multistatus=3 AND salesorderid=?';
        $result=$adb->pquery($query,array($salesorderid));
        $data=$adb->raw_query_result_rowdata($result,0);
        $costofuse=bcadd($data['costofuse'],$totalreceivables,2);
        if(bccomp($data['purchasemount'],$costofuse)<0){
            return true;
        }
        return false;
    }
    public function getVendorList(Vtiger_Request $request){
        global $adb;
        $recordId=$request->get('record');
        $vendorid=$request->get('vendorid');
        $startdate=$request->get('startdate');
        $enddate=$request->get('enddate');
        $oldrefillapplication=array();
        if($recordId>0){
            $query="SELECT * FROM vtiger_packvendorlist WHERE deleted=0 AND prefillapplicationid=?";
            $result=$adb->pquery($query,array($recordId));
            while($row=$adb->fetch_array($result)){
                $oldrefillapplication[]=$row['refillapplicationids'];
            }
        }
        $query="SELECT 
                    vtiger_refillapplication.*,
                    IFNULL(vtiger_account.accountname,'') AS accountname,
                    vtiger_crmentity.createdtime,
                    (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as username,
                    IFNULL(vtiger_servicecontracts.contract_no,'采购合同') AS contract_no
                FROM `vtiger_refillapplication` 
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid 
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_refillapplication.accountid
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_refillapplication.servicecontractsid
                WHERE vtiger_crmentity.deleted=0
                AND vtiger_refillapplication.modulestatus='c_complete'
                AND vtiger_refillapplication.paymentperiod='postpayment'
                AND vtiger_refillapplication.ispayment='unpaid'
                AND vtiger_refillapplication.rechargesource in('Vendors','PreRecharge','NonMediaExtraction')
                AND vtiger_refillapplication.vendorid=?
                AND LEFT(vtiger_crmentity.createdtime,10)>=?
                AND LEFT(vtiger_crmentity.createdtime,10)<=?";
        $result=$adb->pquery($query,array($vendorid,$startdate,$enddate));
        $data=array();
        while($row=$adb->fetch_array($result)){
            $row['selected']=in_array($row['refillapplicationid'],$oldrefillapplication)?1:0;
            $row['rechargesource']=vtranslate($row['rechargesource'],'RefillApplication');
            $data[]=$row;
        }
        $recordModel=Vtiger_Record_Model::getInstanceById($vendorid,'Vendors');
        $entity=$recordModel->getEntity();
        $column_fields=$entity->column_fields;
        $bankinfo[]=array(
            'bankaccount'=>$column_fields['bankaccount'],
            'bankcode'=>$column_fields['bankcode'],
            'bankname'=>$column_fields['bankname'],
            'banknumber'=>$column_fields['banknumber'],
        );
        $bankinfodata=$recordModel->getVendorBank($vendorid);
        $bankinfo=array_merge($bankinfo,$bankinfodata);
        return array('data'=>$data,'columnfields'=>$column_fields,'bankinfo'=>$bankinfo);
    }
    public function checkPackvendorsList(Vtiger_Request $request){
        $totalreceivables=$request->get('totalreceivables');
        $insertid=$request->get('insertid');
        if($totalreceivables==0){
            return true;
        }
        if(empty($insertid)){
            return true;
        }
        $insertid=implode(",",$insertid);
        $query="SELECT sum(vtiger_refillapplication.totalreceivables) AS total FROM vtiger_refillapplication WHERE refillapplicationid in({$insertid})";
        global $adb;
        $result=$adb->pquery($query,array());
        $data=$adb->raw_query_result_rowdata($result,0);
        if(bccomp($data['total'],$totalreceivables,2)==0){
            return false;
        }
        return true;
    }
    public function getDetailVendorList($id){
        global $adb;
        $query="SELECT 
                    vtiger_refillapplication.*,
                    IFNULL(vtiger_account.accountname,'') AS accountname,
                    vtiger_crmentity.createdtime,
                    (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as username,
                    IFNULL(vtiger_servicecontracts.contract_no,'采购合同') AS contract_no
                FROM `vtiger_packvendorlist` 
                LEFT JOIN `vtiger_refillapplication` ON vtiger_refillapplication.refillapplicationid=vtiger_packvendorlist.refillapplicationids
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid 
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_refillapplication.accountid
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_refillapplication.servicecontractsid
                WHERE vtiger_crmentity.deleted=0
                AND vtiger_packvendorlist.prefillapplicationid=?
                AND vtiger_packvendorlist.deleted=0
                ";
        $result=$adb->pquery($query,array($id));
        $data=array();
        while($row=$adb->fetch_array($result)){
            $row['rechargesource']=vtranslate($row['rechargesource'],'RefillApplication');
            $data[]=$row;
        }
        return $data;
    }
    /**
     * 获取部门指定的审核人
     * @param $moduleName
     * @param $departmentid
     * @return array
     * @author: steel.liu
     * @Date:2018-07-25
     *
     */
    public function getRechargeGuarantee($moduleName,$departmentid){
        global $adb;
        $result=$adb->pquery("SELECT parentdepartment,departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid =?",array($departmentid));
        //$returnData=array('userid'=>2110,'twoleveluserid'=>2110,'threeleveluserid'=>2110,'unitprice'=>0,'twounitprice'=>0,'threeunitprice'=>0);
        $returnData=array();
        if($adb->num_rows($result)>0){
            $data=$adb->raw_query_result_rowdata($result,0);
            $departmentids=explode("::",$data['parentdepartment']);
            $departmentids=array_reverse($departmentids);
            foreach($departmentids AS $value){
                $result=$adb->pquery("SELECT vtiger_rechargeguarantee.userid,
                                                    vtiger_rechargeguarantee.twoleveluserid,
                                                    vtiger_rechargeguarantee.unitprice,
                                                    vtiger_rechargeguarantee.twounitprice,
                                                    vtiger_rechargeguarantee.threeleveluserid,
                                                    vtiger_rechargeguarantee.threeunitprice
                                                FROM`vtiger_rechargeguarantee` 
                                                WHERE domodule=?
                                                AND vtiger_rechargeguarantee.deleted=0
                                                AND vtiger_rechargeguarantee.department=?",array($moduleName,$value));
                if($adb->num_rows($result)>0){
                    $returnData=$adb->query_result_rowdata($result,0);
                    break;
                }
            }
        }
        return $returnData;
    }
    /**
     * 验证担保金额
     * @param $accountid 担保客户的ID
     * @param $advancesmoney 当前担保的金额
     * @return array
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function checkAuditInformation($accountid,$advancesmoney){
        $returnData=array('flag'=>false);
        if($advancesmoney>0){
            $data=$this->setAuditInformation($accountid,$advancesmoney);
            if($data['flag']){
                if($data['guarantee']=='first'){
                    $returnData=array('flag'=>false);
                }elseif($data['guarantee']=='second'){
                    $returnData=array('flag'=>true,'msg'=>'本客户超过一级担保额度，需要二级担保审核，是否确定提交!');
                }elseif($data['guarantee']=='third'){
                    $returnData=array('flag'=>true,'msg'=>'本客户超过一二级担保额度，需要三级担保审核，是否确定提交!');
                }else{
                    $returnData=array('flag'=>true,'msg'=>'超出最大担保金额!');
                }
            }else{
                $returnData=array('flag'=>true,'msg'=>'没有找到相关但保信息，请联系相关人员设置！');
            }
        }
        return $returnData;
    }

    /**
     * 撤销回款关联
     * @param $recordid
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function revokeRelation($recordid){
        global $adb,$current_user;
        $query="SELECT * FROM `vtiger_refillapprayment` WHERE deleted=0 AND receivedstatus='revokerelation' AND refillapplicationid=? limit 1";
        $result=$adb->pquery($query,array($recordid));
        $data=$adb->query_result_rowdata($result,0);
        //$backwashtotal=$data['backwashtotal'];
        $refillapptotal=$data['refillapptotal']-$data['refundamount'];
        $refillappraymentid=$data['refillappraymentid'];
        $receivedpaymentsid=$data['receivedpaymentsid'];
        $paymentRecordModel=Vtiger_Record_Model::getInstanceById($receivedpaymentsid,'ReceivedPayments');
        $actualtotalrecharge=$this->get('actualtotalrecharge');//应收款总额
        $oldgrossadvances=$this->get('grossadvances');//合计垫款金额
        $grossadvances=bcadd($oldgrossadvances,$refillapptotal,2);
        $grossadvances=bccomp($actualtotalrecharge,$grossadvances)>=0?$grossadvances:$actualtotalrecharge;//数据验证防止出错
        $oldtotalrecharge=$this->get('totalrecharge');//使用回款总额
        $totalrecharge=bcsub($oldtotalrecharge,$refillapptotal,2);
        $totalrecharge=$totalrecharge>=0?$totalrecharge:0;//数据验证防止出错grossadvances
        if($grossadvances>0){
            $iscushion=1;
        }else{
            $iscushion=0;
        }
        $Sql="UPDATE vtiger_refillapplication SET totalrecharge=?,grossadvances=?,iscushion=? WHERE refillapplicationid=? limit 1";
        $adb->pquery($Sql,array($totalrecharge,$grossadvances,$iscushion,$recordid));
        $Sql="UPDATE `vtiger_refillapprayment` SET deleted=1 WHERE refillappraymentid=? limit 1";
        $adb->pquery($Sql,array($refillappraymentid));
        $Sql="UPDATE `vtiger_receivedpayments` SET rechargeableamount=if((rechargeableamount+{$refillapptotal})>unit_price,unit_price,(rechargeableamount+{$refillapptotal})) WHERE receivedpaymentsid=?";
        $adb->pquery($Sql,array($receivedpaymentsid));
        $accountRecordModule = Vtiger_Record_Model::getCleanInstance("Accounts");
        $refillapplicationno=$this->get('refillapplicationno');
        $msg='('.$refillapplicationno.':充值单回款撤销关联)';

        $accountRecordModule->setAdvancesmoney($this->get('accountid'), $refillapptotal, $msg);
        $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'totalrecharge','oldValue'=>$oldtotalrecharge,'currentValue'=>$totalrecharge.'回款撤销关联'));
        $this->setTracker('RefillApplication',$recordid,array('fieldName'=>'grossadvances','oldValue'=>$oldgrossadvances,'currentValue'=>$grossadvances.'回款撤销关联'));
        $rechargeableamount=$paymentRecordModel->get('rechargeableamount');
        $currentValue=bcadd($rechargeableamount,$refillapptotal,2);
        $this->setTracker('ReceivedPayments',$receivedpaymentsid,array('fieldName'=>'rechargeableamount','oldValue'=>$rechargeableamount,'currentValue'=>$currentValue.$msg));
    }
    /**
     * 获取回款使用明细
     * @param Vtiger_Request $request
     */
    public function getReceivedPaymentsUseDetail($recorid){
        //$recorid = $request->get('record');//回款id

        $db=PearDatabase::getInstance();
        $query="SELECT receivedpaymentsid FROM `vtiger_refillapprayment` WHERE deleted=0 and refillapplicationid=?";
        $result=$db->pquery($query,array($recorid));

        if($db->num_rows($result)==0){
            return ;
        }
        $receivedpaymentsids='';
        while($row=$db->fetch_array($result)){
            $receivedpaymentsids.=$row['receivedpaymentsid'].',';
        }
        $receivedpaymentsids=trim($receivedpaymentsids,',');

        $sql1 = "SELECT 
                '1' AS type,
                vtiger_salesorderrayment.salesorderid AS recordid,
                vtiger_salesorder.salesorder_no AS recordno,
                vtiger_crmentity.createdtime,
                IFNULL(vtiger_users.last_name,'--') AS last_name,
                IFNULL(vtiger_salesorderrayment.modifiedtime,'--') AS matchdate,
                CONCAT('人力成本: ',vtiger_salesorderrayment.laborcost,' | 外采成本: ',vtiger_salesorderrayment.purchasecost) AS detail,
                vtiger_receivedpayments.unit_price,
                vtiger_receivedpayments.reality_date,
                vtiger_receivedpayments.paytitle,
                vtiger_salesorderrayment.receivedpaymentsid,
                '' AS productname,
                vtiger_salesorderrayment.remarks
                FROM vtiger_salesorderrayment
                JOIN vtiger_salesorder ON(vtiger_salesorderrayment.salesorderid=vtiger_salesorder.salesorderid)
                LEFT JOIN vtiger_crmentity ON vtiger_salesorder.salesorderid=vtiger_crmentity.crmid
                LEFT JOIN vtiger_users ON(vtiger_salesorderrayment.modifiedby=vtiger_users.id)
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_salesorderrayment.receivedpaymentsid
                WHERE vtiger_salesorderrayment.receivedpaymentsid in({$receivedpaymentsids}) AND vtiger_salesorderrayment.deleted=0
                UNION ALL
                SELECT 
                '2' AS type,
                vtiger_refillapprayment.refillapplicationid AS recordid,
                vtiger_refillapplication.refillapplicationno AS recordno,
                vtiger_crmentity.createdtime,
                IFNULL(vtiger_users.last_name,'--') AS last_name,
                IFNULL(vtiger_refillapprayment.modifiedtime,'--') AS matchdate,
                CONCAT('充值金额: ',vtiger_refillapprayment.refillapptotal) AS detail,
                vtiger_receivedpayments.unit_price,
                vtiger_receivedpayments.reality_date,
                vtiger_receivedpayments.paytitle,
                vtiger_refillapprayment.receivedpaymentsid,
                (SELECT GROUP_CONCAT(vtiger_products.productname) FROM `vtiger_products`,(SELECT vtiger_rechargesheet.productid,vtiger_rechargesheet.refillapplicationid FROM vtiger_rechargesheet WHERE vtiger_rechargesheet.deleted = 0 GROUP BY	vtiger_rechargesheet.refillapplicationid,vtiger_rechargesheet.productid) AS rechrename	WHERE (rechrename.productid = vtiger_products.productid AND rechrename.refillapplicationid = vtiger_refillapplication.refillapplicationid)) AS productname,
                vtiger_refillapprayment.remarks
                FROM vtiger_refillapprayment 
                JOIN vtiger_refillapplication ON(vtiger_refillapplication.refillapplicationid=vtiger_refillapprayment.refillapplicationid)
                LEFT JOIN vtiger_crmentity ON vtiger_refillapplication.refillapplicationid=vtiger_crmentity.crmid
                LEFT JOIN vtiger_users ON(vtiger_refillapprayment.modifiedby=vtiger_users.id)
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_refillapprayment.receivedpaymentsid
                WHERE vtiger_refillapprayment.receivedpaymentsid in({$receivedpaymentsids}) AND vtiger_refillapprayment.deleted=0 AND vtiger_refillapprayment.refillapptotal>0";
        $r_result = $db->pquery($sql1,array());
        $num=$db->num_rows($r_result);
        if($num>0){
            for ($i=0;$i<$num;++$i){
                $receivedPaymentsUseDetail[] = $db->fetchByAssoc($r_result);
            }
        }
        return $receivedPaymentsUseDetail;
    }

    /**
     * @param $rechargesheetid
     * @return bool
     * @author: steel.liu
     * @Date:xxx
     * 是否有等完成的
     */
    public function getisbackwash($rechargesheetid){
        global $adb;
        $query="SELECT 1 FROM `vtiger_rubricrechargesheet` WHERE rechargesheetid=? AND isbackwash=1 AND deleted=0";
        $result=$adb->pquery($query,array($rechargesheetid));
        if($adb->num_rows($result)){
            return false;
        }
        return true;
    }

    /**
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function getAddEditCommon(Vtiger_Request $request){
        $detailView=new RefillApplication_Edit_View();
        $viewer = $detailView->getViewer ($request);
        $moduleName = $request->getModule();
        $viewer->assign('MODE', 'edit');
        $moduleModel = $this->getModule();
        //读取模块的字段
        $fieldList = $moduleModel->getFields();

        //取交集?还不知道有什么用
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        if(!empty($requestFieldList)){
            foreach($requestFieldList as $fieldName=>$fieldValue){
                $fieldModel = $fieldList[$fieldName];
                if($fieldModel->isEditable()) {
                    $this->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
                }
            }
        }
        $rechargesource=$request->get('rechargesource');
        $dataNum=$request->get('datanum');
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($this,'Edit');
        $productserviceValue='';
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
        $viewer->assign('RECHARGESOURCE', $rechargesource);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('DATANUM', $dataNum);
        $viewer->assign('PRODUCTSERVICEVALUE', $productserviceValue);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());//执行好多次，真是
        $viewer->assign('RECORD',$this);//编辑页面显示不可编辑字段内容
        $isRelationOperation = $request->get('relationOperation');
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }
        return $viewer->view('MEditView.tpl', $moduleName,true);
    }
    /**
     * 移动端修改客户担保数据
     * @param Vtiger_Request $request
     * @return array
     */
    public function setAccountChargeGuarantee(Vtiger_Request $request){
        $userid = $request->get("userid");
        $unitprice = $request->get("unitprice");
        $accountid = $request->get("accountid");
        $twoleveluserid = $request->get("twoleveluserid");
        $twounitprice = $request->get("twounitprice");
        $threeleveluserid = $request->get("threeleveluserid");
        $threeunitprice = $request->get("threeunitprice");
        $currentuserid = $request->get("currentuserid");
        $data = array('flag'=>'0', 'msg'=>'添加失败');
        do{
            if(!$this->personalAuthority('RefillApplication','dorechargeguarantee',$currentuserid)){//权限验证
                break;
            }
            $sql2="INSERT INTO `vtiger_accountrechargeguarantee` (`userid`,`unitprice`,twoleveluserid,twounitprice,threeleveluserid,threeunitprice,accountid, `createdid`, `createdate`) VALUES ( ?,?,?,?,?,?,?,?,?)";
            $sql1="UPDATE vtiger_accountrechargeguarantee SET deleted=1,deleteddate=?,deletedid=? WHERE accountid=? and deleted=0";
            global $current_user;
            $db=PearDatabase::getInstance();
            $db->pquery($sql1, array(date('Y-m-d H:i:s'), $currentuserid,$accountid));
            $db->pquery($sql2, array($userid,$unitprice,$twoleveluserid,$twounitprice,$threeleveluserid,$threeunitprice,$accountid, $currentuserid, date('Y-m-d H:i:s')));
            $query='SELECT  accountrechargeguaranteeid FROM  vtiger_accountrechargeguarantee WHERE accountid=? AND deleted=0 ORDER BY accountrechargeguaranteeid DESC LIMIT 1';
            $result=$db->pquery($query, array($accountid));
            $data = array('flag'=>'1','data'=>$result->fields['accountrechargeguaranteeid'], 'msg'=>'添加成功');
        }while (0);
        return $data;
    }

    /**
     * 获取用户列表
     * @return mixed
     */
    public function getuserinfo(){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ReceivedPayments');
        return $recordModel->getuserinfo(" and status='Active'");
    }

    /**
     * 移动端获取客户担保
     * @param $request
     * @return array
     */
    public function getAccountGuaranteeDetailMobile($request){
        $accountguaranteeid=$request->get('accountguaranteeid');
        $isedit=$request->get('isedit');
        $array=array($accountguaranteeid);
        $accountNamesql='';

        $query="SELECT vtiger_accountrechargeguarantee.*,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.deletedid) AS deletedname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.createdid) AS createdname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.userid) AS username,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.twoleveluserid) AS twousername,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.threeleveluserid) AS threeusername,accountname FROM vtiger_accountrechargeguarantee LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_accountrechargeguarantee.accountid WHERE vtiger_accountrechargeguarantee.deleted=0 AND accountrechargeguaranteeid=?".$accountNamesql."
        ORDER BY accountrechargeguaranteeid DESC LIMIT 1";
        global $adb;
        $result=$adb->pquery($query,$array);
        if($isedit==1){
            return array($result->fields);
        }
        $reurndata['detail']=$result->fields;
        $query="SELECT vtiger_accountrechargeguarantee.*,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.deletedid) AS deletedname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.createdid) AS createdname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.userid) AS username,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.twoleveluserid) AS twousername,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.threeleveluserid) AS threeusername,accountname FROM vtiger_accountrechargeguarantee LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_accountrechargeguarantee.accountid WHERE vtiger_accountrechargeguarantee.deleted=1 AND vtiger_accountrechargeguarantee.accountid=?
        ORDER BY accountrechargeguaranteeid DESC";
        $result=$adb->pquery($query,array($result->fields['accountid']));
        $temp=array();
        while($row=$adb->fetchByAssoc($result)){
            $temp[]=$row;
        }
        $reurndata['list']=$temp;
        return $reurndata;
    }

    /**
     * 移动端获取客户担保列表
     * @param $request
     * @return array
     */
    public function getAccountChargeGuaranteeMobile($request){
        $pagecount=$request->get('pageCount');
        $pageNum=$request->get('pageNum');
        $accountName=$request->get('accountName');
        $accountNamesql='';
        $array=array();
        if(!empty($accountName)){
            $accountNamesql=' AND vtiger_account.accountname like ?';
            $array[]='%'.$accountName.'%';
        }
        $query="SELECT vtiger_accountrechargeguarantee.*,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.deletedid) AS deletedname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.createdid) AS createdname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.userid) AS username,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.twoleveluserid) AS twousername,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountrechargeguarantee.threeleveluserid) AS threeusername,accountname FROM vtiger_accountrechargeguarantee LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_accountrechargeguarantee.accountid WHERE vtiger_accountrechargeguarantee.deleted=0".$accountNamesql."
        ORDER BY accountrechargeguaranteeid DESC LIMIT ".$pageNum.','.$pagecount;
        global $adb;
        $result=$adb->pquery($query,$array);
        $reurndata=array();
        while($row=$adb->fetchByAssoc($result)){
            $reurndata[]=$row;
        }
        return $reurndata;
    }
    /**
     * 删除客户担保
     * @param $request
     * @return array
     */
    public function deleteAccountGuaranteeData($request){
        $id=$request->get('accountguaranteeid');
        $userid=$request->get('userid');
        $data = array('flag'=>'0', 'msg'=>'删除失败');
        do {
            if(!$this->personalAuthority('RefillApplication','dorechargeguarantee',$userid)){   //权限验证
                $data['msg']='没有权限';
                break;
            }
            if (empty($id)) {
                break;
            }
            $sql1="UPDATE vtiger_accountrechargeguarantee SET deleted=1,deleteddate=?,deletedid=? WHERE accountrechargeguaranteeid=?";
            $db=PearDatabase::getInstance();
            $db->pquery($sql1, array(date('Y-m-d H:i:s'),$userid,$id));
            $data = array('flag'=>'1', 'msg'=>'删除成功');
        } while (0);
        return $data;
    }


    /**
     * 获取did信息列表
     *
     * @param $recordId
     * @return array
     */
    public function getDidAccounts($accountId){
        $db=PearDatabase::getInstance();
        $query = "SELECT 
                      vtiger_accountplatform.accountid,
                      vtiger_accountplatform_detail.accountplatform,
                      vtiger_accountplatform.accountplatformid,
                      vtiger_accountplatform.accountrebate,
                      vtiger_accountplatform.effectiveendaccount,
                      vtiger_accountplatform.effectivestartaccount,
                      vtiger_accountplatform_detail.idaccount,
                      vtiger_accountplatform.customeroriginattr,
                      vtiger_accountplatform.accountrebatetype,
                      vtiger_accountplatform.isprovideservice,
                      IFNULL(vtiger_accountplatform.rebatetype,'GoodsBack') AS rebatetype,
                      vtiger_accountplatform.modulestatus,
                      vtiger_accountplatform.supplierrebate,
                      (SELECT vtiger_products.productname FROM `vtiger_products` WHERE vtiger_products.productid=vtiger_accountplatform.productid) as topplatform,
                      vtiger_accountplatform.productid,
                      IF((SELECT
                            1
                        FROM
                            vtiger_refillapplication
                        LEFT JOIN vtiger_rechargesheet ON vtiger_rechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_refillapplication.accountid =vtiger_accountplatform.accountid
                        AND vtiger_rechargesheet.did =vtiger_accountplatform.idaccount limit 1)=1,1,0) AS rechargetypedetail,
                      vtiger_accountplatform.vendorid
                FROM vtiger_accountplatform 
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_accountplatform.accountplatformid
                LEFT JOIN vtiger_accountplatform_detail ON  vtiger_accountplatform_detail.accountplatformid=vtiger_accountplatform.accountplatformid
                WHERE vtiger_crmentity.deleted=0 
                AND vtiger_accountplatform.modulestatus='c_complete'
                AND vtiger_accountplatform.isforbidden=0
                AND vtiger_accountplatform.accountid=?";
        $result = $db->pquery($query, array($accountId));
        while ($row = $db->fetch_array($result)) {
            $rechargetypedetail='OpenAnAccount';
            if($row['rechargetypedetail']==1){
                $rechargetypedetail='renew';
            }
            $tmp['idaccount']=$row['idaccount'];
            $tmp['accountid']=$row['accountid'];
            $tmp['accountplatform']=$row['accountplatform'];
            $tmp['accountplatformid']=$row['accountplatformid'];
            $tmp['accountrebate']=$row['accountrebate'];
            $tmp['effectiveendaccount']=$row['effectiveendaccount'];
            $tmp['effectivestartaccount']=$row['effectivestartaccount'];
            $tmp['modulestatus']=$row['modulestatus'];
            $tmp['supplierrebate']=$row['supplierrebate'];
            $tmp['topplatform']=$row['topplatform'];
            $tmp['productid']=$row['productid'];
            $tmp['vendorid']=$row['vendorid'];
            $tmp['accountrebatetype']=$row['accountrebatetype'];
            $tmp['rechargetypedetail']=$rechargetypedetail;
            $tmp['customeroriginattr']=$row['customeroriginattr'];
            $tmp['isprovideservice']=$row['isprovideservice'];
            $tmp['didcount']=$row['didcount'];
            $tmp['rebatetype']=$row['rebatetype'];
            $return[strval($row['idaccount'])] = $tmp;
        }
        return $return;
    }
    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 修改财务状态
     */
    public function financialstate(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $code=$request->get('code');
        $amountofsales=$request->get('advanceMoney');
        $userid=$request->get('userid');
        $data['flag']=false;
        do{
            $query='SELECT refillapplicationid,financialstate,modulestatus,grossadvances,refillapplicationno,amountofsales,totalrecharge,accountid FROM vtiger_refillapplication LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid WHERE vtiger_crmentity.deleted=0 AND refillapplicationno=?';
            $result=$db->pquery($query,array($code));
            if($db->num_rows($result)==0){
                $data['msg']='充值单编号有误!';
                break;
            }
            $recordId=$result->fields['refillapplicationid'];

            if($result->fields['modulestatus']!='c_complete'){
                $data['msg']='未完成的状态不允许操作!';
                break;
            }
            $financialstate=$result->fields['financialstate'];
            $grossadvances=$result->fields['grossadvances'];
            $iscushion='';
            $refillapplicationno=$result->fields['refillapplicationno'];
            $currnetamountofsales=$result->fields['amountofsales'];
            $totalrecharge=$result->fields['totalrecharge'];//使用回款金额
            $amountofsales=$grossadvances;//令销账金额=
            if($financialstate==0){
                if(bccomp($grossadvances,0,2)<0){
                    $data['msg']='无可销账金额！';
                    break;
                }
                if(bccomp($amountofsales,$grossadvances,2)>0){
                    $data['msg']='销账金额，必需等于垫款金额！';
                    break;
                }
                $currentgrossadvances=bcsub($grossadvances,$amountofsales,2);
                if(bccomp($currentgrossadvances,0,2)==0){
                    $iscushion='iscushion=0,';
                }
                $amountAvailable=-$amountofsales;
                $totalrecharge=bcadd($totalrecharge,$amountofsales,2);
                $msg = '('.$refillapplicationno.':手动销账(冲))';
            }else{
                $data['flag']=true;
                $data['msg']='已销账无需重复销账！';
                break;
            }
            $Finalfinancialstate=$financialstate==1?0:1;

            $db->pquery('UPDATE vtiger_refillapplication SET financialstate=?,'.$iscushion.'grossadvances=?,amountofsales=?,totalrecharge=?,removeaccounttime=? WHERE refillapplicationid=?',array($Finalfinancialstate,$currentgrossadvances,$amountofsales,$totalrecharge,date('Y-m-d H:i:s', time()),$recordId));
            $accountRecordModule = Vtiger_Record_Model::getCleanInstance("Accounts");
            $accountRecordModule->setAdvancesmoney($result->fields['accountid'], $amountAvailable, $msg);
            $this->setTracker('RefillApplication',$recordId,array('fieldName'=>'financialstate','oldValue'=>$financialstate.'(手动销账)','currentValue'=>$Finalfinancialstate));
            $this->setTracker('RefillApplication',$recordId,array('fieldName'=>'grossadvances','oldValue'=>$grossadvances.'(手动销账)','currentValue'=>$currentgrossadvances));
            $this->setTracker('RefillApplication',$recordId,array('fieldName'=>'amountofsales','oldValue'=>$currnetamountofsales.'(手动销账)','currentValue'=>$amountofsales));
            $data['flag']=true;
            $data['msg']='财务状态修改成功!';

        }while(0);
        return $data;
    }
}
