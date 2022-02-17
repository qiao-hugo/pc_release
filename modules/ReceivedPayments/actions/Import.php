<?php
/*+************
 * 独立文件上传
 *20141222
 **************/
//error_reporting(-1);
//ini_set("display_errors",1);
class ReceivedPayments_Import_Action extends Vtiger_Save_Action {
    private $listCount=0;


    public function process(Vtiger_Request $request) {
        $files=$_FILES['importFile'];
        $filetmp_name = $files['tmp_name'];
        $upload_file_path = decideFilePath();
        $file_name=$upload_file_path .time().'.xlsx';
        $upload_status = move_uploaded_file($filetmp_name, $file_name);
        if(!$upload_status){
            echo json_encode(array('success'=>false,'result'=>array('name'=>$files['name'])));
            exit;
        }
        $this->import($file_name);
    }

    /**
     * 导入
     * @param $fileName
     * @throws Exception
     */
    public function import($fileName){
        global $root_directory;
        include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($fileName)) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($fileName)) {
                echo json_encode(array('success'=>false,'result'=>array('name'=>$fileName)));
            }
        }
        $PHPExcel = $PHPReader->load($fileName);
        $currentSheet = $PHPExcel->getSheet(0);
        /**取得一共有多少列*/
        $allColumn = $currentSheet->getHighestColumn();
        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();
        $all = array();
        for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
            $flag = 0;
            $col = array();
            for ($currentColumn = 'A'; $this->getascii($currentColumn) <= $this->getascii($allColumn); $currentColumn++) {
                $address = $currentColumn . $currentRow;
                $string = $currentSheet->getCell($address)->getValue();
                $col[$flag] = $string;
                $flag++;
            }
            $all[] = $col;
        }
        $this->listCount=count($all);
        $resultMsg=$this->insertRecePayment($all);
        $successCount=mb_substr_count($resultMsg,'导入成功');
        $total=$this->listCount;
        $resultMsg='共导入'.($total-1).'条，成功'.$successCount.'条，失败'.($total-1-$successCount).'条'.PHP_EOL.$resultMsg;
        echo json_encode(array('success'=>true,'result'=>$resultMsg,'data'=>$all,'column'=>$allColumn,'row'=>$allRow));
        exit;
    }

    /**
     * 对所有record进行处理
     * @param $allRecords
     */
    public function insertRecePayment($allRecords){
        $total=$this->listCount;
        date_default_timezone_set("Asia/Shanghai");
        global $adb,$current_user;
        if(count($allRecords)==1){
            return '导入失败，原因文件中无要导入的信息';
        }
        unset($allRecords[0]);//去掉中文
        $request=new Vtiger_Request(array());
        $saveObject=new Vtiger_Save_Action();
        $msgTotal='';
        foreach ($allRecords as $key => $record){
            $record=array_map(function ($value){
                return trim($value);
            },$record);
            $recordValueArray=array_unique(array_values($record));
            if(!$recordValueArray||empty($recordValueArray)||!isset($recordValueArray[1])){
                $total=(int)$total-1;
                continue;
            }
            $flag=true;
            $msg='第'.$key.'行导入成功。';
            $errMsg='';
            if(empty($record[0])){
                $flag=false;
                $errMsg.='【公司名】不能为空,';
            }
            if($record[10]&&($record[10]=='扫码'||$record[10]=='对公转账')&&empty($record[1])&&!in_array($record[0],array('美国凯丽隆国际控股（香港）有限公司','凯丽隆国际控股（香港）有限公司'))){
                $flag=false;
                $errMsg.='【支付渠道】是扫码或者对公转账（除香港和美国凯丽隆海外公司）时【银行名】不能为空,';
            }
            if($record[10]&&$record[10]=='对公转账'&&empty($record[2])&&!in_array($record[0],array('美国凯丽隆国际控股（香港）有限公司','凯丽隆国际控股（香港）有限公司'))){
                $flag=false;
                $errMsg.='【支付渠道】是对公转账时（除香港和美国凯丽隆海外公司）【支行名】不能为空,';
            }
            if(empty($record[3])){
                $flag=false;
                $errMsg.='【账号】不能为空,';
            }else{
                $record[3]=(string)$record[3];
            }
            if(empty($record[4])){
                $flag=false;
                $errMsg.='【货币类型】不能为空,';
            }else{
                if($record[4]=='人民币'&&$record[5]!=1){
                    $flag=false;
                    $errMsg.='【货币类型】是人民币时【汇率】必须为1,';
                }
            }
            if(empty($record[5])){
                $flag=false;
                $errMsg.='【汇率】不能为空,';
            }
            if(empty($record[6])){
                $flag=false;
                $errMsg.='【入账日期】不能为空,';
            }
            if(empty($record[10])){
                $flag=false;
                $errMsg.='【支付渠道】不能为空,';
            }
            if(empty($record[11])){
                $flag=false;
                $errMsg.='【交易单号】不能为空,';
            }
            if($record[10]&&$record[10]=='支付宝转账'&&empty($record[7])){
                $flag=false;
                $errMsg.='【支付渠道】是支付宝转账时【回款抬头】不能为空,';
            }
            if($record[10]&&$record[10]=='对公转账'&&empty($record[7])){
                $flag=false;
                $errMsg.='【支付渠道】是对公转账时【回款抬头】不能为空,';
            }
            if(empty($record[8])){
                $flag=false;
                $errMsg.='【金额(￥)】不能为空,';
            }
            if(empty($record[9])){
                $flag=false;
                $errMsg.='【原币金额】不能为空,';
            }
            if(intval($record[9])<=0){
                $flag=false;
                $errMsg.='【原币金额】必须大于0,';
            }else{
                if(preg_match('/\.[0-9]{2,}[1-9][0-9]*$/', (string)$record[9])>0){
                    //判断是否不是2位小数
                    $flag=false;
                    $errMsg.='【原币金额】不能大于2位小数,';
                }
            }
            if(intval($record[8])<=0){
                $flag=false;
                $errMsg.='【金额(￥)】必须大于0,';
            }else{
                if(preg_match('/\.[0-9]{2,}[1-9][0-9]*$/', (string)$record[8])>0){
                    //判断是否不是2位小数
                    $flag=false;
                    $errMsg.='【金额(￥)】不能大于2位小数,';
                }
                if(abs(bcsub($record[8],bcmul($record[9],$record[5],9),1))>0.1){
                    $flag=false;
                    $errMsg.='【原币金额】*【汇率】与【金额(￥)】相差大于0.1,';
                }
            }
            if (preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$record[11])) {
                $flag=false;
                $errMsg.='【交易单号】不允许填写中文,';
            }
            if($flag){
                //判断是否有公司账号
                if($record[10]=='对公转账'&&!in_array($record[0],array('美国凯丽隆国际控股（香港）有限公司','凯丽隆国际控股（香港）有限公司'))){
                    $sql="select id from vtiger_companyaccounts where company=? and channel=? and bank=? and subbank=? and account=?";
                    $result=$adb->pquery($sql,array($record[0],'对公转账',$record[1],$record[2],$record[3]));
                    if($adb->num_rows($result)==0){
                        $flag=false;
                        $errMsg.='【支付渠道】是对公转账时（除香港和美国凯丽隆海外公司）,没有【公司账号】是'.$record[0].'##'.$record[1].'-'.$record[2].'（'.$record[3].'）';
                    }
                    $ownCompany=$record[0].'##'.$record[1].'-'.$record[2].'（'.$record[3].'）';
                }else if($record[10]=='支付宝转账'){
                    $sql="select id from vtiger_companyaccounts where company=? and channel=? and account=?";
                    $result=$adb->pquery($sql,array($record[0],'支付宝转账',$record[3]));
                    if($adb->num_rows($result)==0){
                        $flag=false;
                        $errMsg.='【支付渠道】是支付宝转账时,没有【公司账号】是'.$record[0].'##（'.$record[3].'）';
                    }
                    $ownCompany=$record[0].'##（'.$record[3].'）';
                }else if($record[10]=='扫码'){
                    $sql="select id from vtiger_companyaccounts where company=? and channel=? and bank=? and account=?";
                    $result=$adb->pquery($sql,array($record[0],'扫码',$record[1],$record[3]));
                    if($adb->num_rows($result)==0){
                        $flag=false;
                        $errMsg.='【支付渠道】是扫码时,没有【公司账号】是'.$record[0].'##'.$record[1].'（'.$record[3].'）';
                    }
                    $ownCompany=$record[0].'##'.$record[1].'（'.$record[3].'）';
                }else if($record[10]=='对公转账'&&in_array($record[0],array('美国凯丽隆国际控股（香港）有限公司','凯丽隆国际控股（香港）有限公司'))){
                    $sql="select id from vtiger_companyaccounts where company=? and channel=? and account=?";
                    $result=$adb->pquery($sql,array($record[0],'对公转账',$record[3]));
                    if($adb->num_rows($result)==0){
                        $flag=false;
                        $errMsg.='【支付渠道】是对公转账时（公司是香港和美国凯丽隆海外公司）,没有【公司账号】是'.$record[0].'##（'.$record[3].'）';
                    }
                    $ownCompany=$record[0].'##（'.$record[3].'）';
                }else if(!in_array($record[10],array('对公转账','扫码','支付宝转账'))){
                    $flag=false;
                    $errMsg.='【支付渠道】请填写对公转账,扫码,支付宝转账';
                }
            }

            if($flag){
                $request->set('module','ReceivedPayments');
                $request->set('owncompany',$ownCompany);
                $request->set('receivementcurrencytype',$record[4]);
                $request->set('exchangerate',$record[5]);
                $request->set('reality_date',date('Y-m-d',strtotime($record[6])));
                $request->set('paytitle',$record[7]);
                $request->set('unit_price',$record[8]);
                $request->set('standardmoney',$record[9]);
                $request->set('paymentchannel',$record[10]);
                $request->set('paymentcode',$record[11]);
                $request->set('overdue',$record[12]);
                $request->set('createtime',date('Y-m-d H:i:s'));
                $request->set('modifiedtime',date('Y-m-d H:i:s'));
                $request->set('checkid',$current_user->id);
                $request->set('createid',$current_user->id);
                $request->set('maybe_account',0);
                if($record[7]){
                    $accountdata = ReceivedPayments_Record_Model::match_account(trim($record[7]));
                    if($accountdata['crmid']){
                        $request->set('maybe_account',$accountdata['crmid']);
                    }
                }
                $recordModel=$saveObject->saveRecord($request);
                $recordId=$recordModel->getId();
                if($recordId){
                    //系统分类
                    $classifyRecordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
                    $classifyRecordModel->systemClassification($recordId);

                    $sql="update vtiger_receivedpayments set allowinvoicetotal=?,rechargeableamount=?,receivementcurrencytype=?,maybe_account=?,ancestor_receivedpaymentsid=? where receivedpaymentsid=?";
                    $adb->pquery($sql,array($record[8],$record[8],$record[4],$request->get('maybe_account'),$recordId,$recordId));
                    //本行失败
                    $msgTotal.=$msg.PHP_EOL;
                }else{
                    //本行失败
                    $msg='第'.$key.'行导入失败，原因是：sql执行失败。'.PHP_EOL;
                    $msgTotal.=$msg;
                }
            }else{
                //本行失败
                $msg='第'.$key.'行导入失败，原因是：'.rtrim($errMsg,',').'。'.PHP_EOL;
                $msgTotal.=$msg;
            }
        }
        $this->listCount=$total;
        return $msgTotal;
    }

    /**
     * 读取字符串的ASCII码
     * @param $ch
     * @return int
     */
    function getascii( $ch) {
        if(strlen($ch) == 1){
            return ord($ch)-65;
        }
        return ord($ch[1])-38;
    }


}
