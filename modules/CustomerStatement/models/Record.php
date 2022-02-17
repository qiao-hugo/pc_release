<?php
/*+********
 *客户信息管理
 **********/

class CustomerStatement_Record_Model extends Vtiger_Record_Model {
    static function createStaypayment($fake_request){
        $db=PearDatabase::getInstance();
        //$result=$db->run_query_allrecords('');
        $ressorder=new Vtiger_Save_Action();
        $ressorder->saveRecord($fake_request);
        //$crmid=$db->getUniqueID('vtiger_crmentity');求表ID当前最大的
    }

    static function getaccinfoBYcontractid($contractid){
        $adb=PearDatabase::getInstance();
        $query="SELECT * FROM vtiger_crmentity WHERE crmid=? limit 1";
        $result=$adb->pquery($query,array($contractid));
        $resultdata=$adb->query_result_rowdata($result,0);
        if($resultdata['setype']=='ServiceContracts'){
            $sql = "SELECT accountid, accountname,effectivetime,companycode FROM vtiger_servicecontracts INNER JOIN vtiger_account ON sc_related_to = accountid WHERE servicecontractsid =? limit 1";
        }else{
            $sql = "SELECT vtiger_suppliercontracts.vendorid AS  accountid,vtiger_vendor.vendorname AS accountname,effectivetime,companycode FROM vtiger_suppliercontracts INNER JOIN vtiger_vendor ON vtiger_suppliercontracts.vendorid = vtiger_vendor.vendorid WHERE vtiger_suppliercontracts.suppliercontractsid =? limit 1";
        }

        $result = $adb->pquery($sql,array($contractid));
        if($adb->num_rows($result)>0){
            $temp = $adb->query_result_rowdata($result,0);
        }else{
            $temp = array();
        }
        return $temp;
    }

    /**
     * steel 2015-11-26
     * 是否已经签名
     * @throws
     */
    static public function checksign($recordid){
        $db=PearDatabase::getInstance();
        $result=$db->pquery("select 1 from vtiger_staypaymentsign where vtiger_staypaymentsign.setype='Staypayment' AND vtiger_staypaymentsign.staypaymentid=?",array($recordid));
        if($db->num_rows($result)>0){
            return false;
        }
        return true;
    }

    /**
     * 获取附件链接和附件
     */
    public function getFile($recordid){
        global $adb;
        $sql = "select file from vtiger_staypayment where staypaymentid=?";
        $result = $adb->pquery($sql,array($recordid));
        $file = '';
        if($adb->num_rows($result)){
            $row = $adb->query_result_rowdata($result,0);
            $file = $row['file'];
        }
        if(!$file){
            return array();
        }

        $files = explode("##",$file);
        $sql2 = "select name,path,type from vtiger_files where attachmentsid = ? ";
        $result2 = $adb->pquery($sql2,array($files[1]));
        if($adb->num_rows($result2)){
            $row2 = $adb->query_result_rowdata($result2,0);
            $path = $row2['path'];
            $name = $row2['name'];
            $type = explode('/',$row2['type']);
        }
        return array(
            '/'.$path.$name,
            strtolower($type[1])=='pdf'?'pdf':strtolower($type[0])
        );

    }

    /**
     *
     */
    public function sendWarningEmail($record,$reason=''){
        global $adb,$current_user;
        $sql = "select a.modulestatus,c.contract_no,d.accountname,a.staypaymentname,a.currencytype,b.createdtime,a.staypaymentjine,e.email1,a.staypaymenttype from vtiger_staypayment a 
  left join vtiger_crmentity b on a.staypaymentid=b.crmid 
  left join vtiger_servicecontracts c on a.contractid = c.servicecontractsid
  left join vtiger_account d on a.accountid = d.accountid
  left join vtiger_users e on e.id = b.smcreatorid
where a.staypaymentid = ?";
        $result = $adb->pquery($sql,array($record));
        if(!$adb->num_rows($result)){
            return;
        }
        $row = $adb->fetchByAssoc($result,0);


        $Subject = '代付款审核！！！';
        $str = '';
        $currentDate = date("Y-m-d H:i:s");
        switch ($row['modulestatus']){
            case 'c_complete':
                $str .= '同事你好！你于 '.$row['createdtime'].' 创建的代付款信息，已经于 '.$currentDate.' 被 '.$current_user->last_name.' 审核通过';
                $str .= '<br><br><br>';
                $str .= "<table style='border: 1px solid black;border-collapse: collapse'><tr><th style='border-right: 1px solid black'>合同编号</th>
                    <th  style='border-right: 1px solid black'>合同客户名称</th>
                    <th style='border-right: 1px solid black'>代付款客户</th>";
                if($row['staypaymenttype']=='fixation'){
                    $str .= "    <th style='border-right: 1px solid black'>币种</th>
                    <th style='border-right: 1px solid black'>代付款金额</th></tr>";
                }
                $str .= '<tr style=\'border: 1px solid black\'>
                        <td style=\'border-right: 1px solid black\'>' . $row['contract_no'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $row['accountname'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $row['staypaymentname'] . '</td>';
                if($row['staypaymenttype']=='fixation') {
                    $str .= '<td style=\'border-right: 1px solid black\'>' . $row['currencytype'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $row['staypaymentjine'] . '</td>
                </tr>';
                }
                $str .= "</table><br>";
                break;
            case 'a_exception':
                $str .= '同事你好！你于 '.$row['createdtime'].' 创建的代付款信息，已经于 '.$currentDate.' 被 '.$current_user->last_name.' 打回,打回原因为:'.$reason;
                break;
        }
        if(!$str){
            return;
        }

        $address = $row['email1'];
        Vtiger_Record_Model::sendMail($Subject, $str, array(array('mail'=>$address, 'name'=>'')));
    }

    /**
     * 代付款审核微信消息
     *
     * @param $overdueDatas
     */
    public function sendWarningWx($record,$reason='')
    {
        global $adb,$current_user;
        $sql = "select a.modulestatus,c.contract_no,d.accountname,a.staypaymentname,a.currencytype,b.createdtime,a.staypaymentjine,e.email1,a.staypaymenttype from vtiger_staypayment a 
  left join vtiger_crmentity b on a.staypaymentid=b.crmid 
  left join vtiger_servicecontracts c on a.contractid = c.servicecontractsid
  left join vtiger_account d on a.accountid = d.accountid
  left join vtiger_users e on e.id = b.smcreatorid
where a.staypaymentid = ?";
        $result = $adb->pquery($sql,array($record));
        if(!$adb->num_rows($result)){
            return;
        }
        $row = $adb->fetchByAssoc($result,0);
        $currentDate = date("Y-m-d H:i:s");
        $content = '';
        switch ($row['modulestatus']){
            case 'c_complete':
                $content .= '同事你好！你于 '.$row['createdtime'].' 创建的代付款信息，已经于 '.$currentDate.' 被 '.$current_user->last_name.' 审核通过<br>';
                $content .= '合同编号:'.$row['contract_no'].'<br>合同客户名称:'.$row['accountname'].'<br>代付款客户:'.$row['staypaymentname'];
                if($row['staypaymenttype']=='fixation'){
                    $content  .= '<br>代付款金额:'.$row['staypaymentjine'].'<br>币种:'.$row['currencytype'].'<br>';
                }
                break;
            case 'a_exception':
                $content .= '同事你好！你于 '.$row['createdtime'].' 创建的代付款(合同编号:'.$row['contract_no'].')，已经于 '.$currentDate.' 被 '.$current_user->last_name.' 打回<br>';
                $content .= '合同编号:'.$row['contract_no'].'<br>合同客户名称:'.$row['accountname'].'<br>打回原因:'.$reason.'<br>';
                break;
        }

        $this->sendWechatMessage(array('email'=>trim($row['email1']),'description'=>$content,'dataurl'=>'#','title'=>'【代付款消息提醒】','flag'=>7));
    }
}
