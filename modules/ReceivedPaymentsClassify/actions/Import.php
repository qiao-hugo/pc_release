<?php

/**
 * 导入
 * Class ReceivedPaymentsClassify_Import_Action
 */
class ReceivedPaymentsClassify_Import_Action extends Vtiger_Save_Action {
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
        $recordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
        $receivedPaymentsRules = $recordModel->getAllReceivedPaymentsRules();
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
                $errMsg.='【流水ID】不能为空,';
            }
            if(empty($record[1])){
                $flag=false;
                $errMsg.='【人工分类】不能为空,';
            }
            $artificialclassfication = array_search($record[1], $receivedPaymentsRules);
            if (!$artificialclassfication) {
                $flag=false;
                $errMsg.='【人工分类】不存在,';
            }
            $result = $adb->pquery("select artificialclassfication from vtiger_receivedpayments where receivedpaymentsid=?",array($record[0]));
            if(!$adb->num_rows($result)){
                $flag=false;
                $errMsg.='不存在流水ID为'.$record[0].'的回款';
            }

            if($flag){
                $data = $adb->fetchByAssoc($result,0);
                $oldValue = $data['artificialclassfication'];
                $adb->pquery("update vtiger_receivedpayments set artificialclassfication=? where receivedpaymentsid=?", array($artificialclassfication, $record[0]));

                $modtrackerDetailData = array();
                $modtrackerDetailData['id'] = $record[0];
                $modtrackerDetailData['fieldname'] = 'artificialclassfication';
                $modtrackerDetailData['prevalue'] = $oldValue?$receivedPaymentsRules[$oldValue]:'';
                $modtrackerDetailData['postvalue'] = $record[1];

                $divideNames = array_keys($modtrackerDetailData);
                $divideValues = array_values($modtrackerDetailData);
                $adb->pquery('INSERT INTO `vtiger_modtracker_detail` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);

                $msgTotal.=$msg.PHP_EOL;
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
