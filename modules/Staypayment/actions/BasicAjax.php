<?php
class Staypayment_BasicAjax_Action extends Vtiger_BasicAjax_Action {
    public $stayPaymentWorkFlowSid = 2430423;  //代付款在线签收id
    function __construct() {
        parent::__construct();
        $this->exposeMethod('getservicecontractsinfo');
        $this->exposeMethod('getaccountnocheck');
        $this->exposeMethod('savesignimage');
        $this->exposeMethod('getImage');
        $this->exposeMethod('supplydata');
	    $this->exposeMethod('downfileZip');
        $this->exposeMethod('getIdCardCheck');
        $this->exposeMethod('reSubmit');
        $this->exposeMethod('export');
        $this->exposeMethod('submitremark');
        $this->exposeMethod('isNeedFile');
        $this->exposeMethod('delReceive');
        $this->exposeMethod('delayExport');
    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    function getservicecontractsinfo(Vtiger_Request $request){
        $contractid = $request->get('record');
        $return = Staypayment_Record_Model::getaccinfoBYcontractid($contractid);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($return);
        $response->emit();
    }

    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    function getaccountnocheck(Vtiger_Request $request){
        $staypaymentaccountno = $request->get('staypaymentaccountno');
        $staypaymentname =$request->get('staypaymentname');
        $return = ReceivedPayments_Record_Model::checkNoMatchReceivedPayment($staypaymentaccountno,$staypaymentname);
        $return = array('success'=>true,'msg'=>'','data'=>array(array(
            'paytitle'=>'xxxx',
            'paymentaccountno'=>"23232333",
            'unit_price'=>80,
            'reality_date'=>'2018-12-23'
        )));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($return);
        $response->emit();
    }

    public function getImage(Vtiger_Request $request){
        $fileid=(int)base64_decode($request->get('filename'));
        if($fileid>0){
            global $adb;
            $result = $adb->pquery("SELECT * FROM vtiger_files WHERE attachmentsid=?", array($fileid));
            if($adb->num_rows($result)) {
                $fileDetails = $adb->query_result_rowdata($result);
                $filePath = $fileDetails['path'];
                $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
                $t_fileName = base64_encode($fileName);
                $t_fileName = str_replace('/', '', $t_fileName);
                $savedFile = $fileDetails['attachmentsid']."_".$t_fileName;
                if(!file_exists($filePath.$savedFile)){
                    $savedFile = $fileDetails['attachmentsid']."_".$fileName;
                }
                $fileSize = filesize($filePath.$savedFile);
                $fileSize = $fileSize + ($fileSize % 1024);
                if (fopen($filePath.$savedFile, "r")) {
                    $fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);
                    header("Content-type: ".$fileDetails['type']);
                    header("Pragma: public");
                    header("Cache-Control: private");
//					header("Content-Disposition: attachment; filename=$fileName");
                    header('Content-Disposition: attachment; filename="'.$fileName.'"');
                    header("Content-Description: PHP Generated Data");
                }

                echo $fileContent;
            }else{
                echo 'no file exist';
            }
        }
    }

    public function savesignimage(Vtiger_Request $request){
        $imgstring=$request->get('image');
        $recordId = $request->get('record');//合同的id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Staypayment');
        $id = $request->get('id');
        $newrecordid=base64_encode($recordId);
        global $root_directory,$current_user;

        $invoiceimagepath = $invoiceimagepath='/storage/staypayment/';
        $imagepath=$invoiceimagepath.date('Y').'/'.date('F').'/'.date('d').'/';
        //是否是目录不是则循环创建
        is_dir($root_directory.$imagepath) || mkdir($root_directory.$imagepath,0777,true);
        //文件相对保存的路径
        $newimagepath= $imagepath.$newrecordid.'.png';
        //以文档流方式创建文件
        $img=imagecreatefromstring(base64_decode(str_replace('data:image/png;base64,','',$imgstring)));
        //取得图片的宽和高
        $invoiceimagewidth=imagesx($img);
        $invoiceimageheight=imagesy($img);
        //写入相对应的日期
        $textcolor = imagecolorallocate($img, 255, 0, 0);
        //$img若直接保存的话背影为黑色新建一个真彩图片背景为白色让两张图片合并$img为带a的通道
        $other=imagecreatetruecolor($invoiceimagewidth,$invoiceimageheight);
        $white=imagecolorallocate($img, 255, 255, 255);
        //$other 填充为白色
        imagefill($other,0,0,$white);
        $datetime=date('Y-m-d H:i');
        //将日期写入$img中
        imagestring($img,5,$invoiceimagewidth-200,$invoiceimageheight-60,$datetime,$textcolor);
        //合并图片
        imagecopy($other,$img,0,0,0,0,$invoiceimagewidth,$invoiceimageheight);
        //保存图片
        imagepng($other,$root_directory.$newimagepath);
        //释放资源
        imagedestroy($img);
        imagedestroy($other);
        $db=PearDatabase::getInstance();
        $sql = 'INSERT INTO `vtiger_newinvoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
        $db->pquery($sql,array($recordId,$newimagepath,$newrecordid,'Staypayment',$datetime,$id));
        if ($db->getLastInsertID()<1) {
            //如果不成功则删除添加的图片
            unlink($root_directory.$newimagepath);
        }

        //签完名更改代付款状态为完成
        $recordModel->set('id', $recordId);
        $recordModel->set('mode','edit');
        $recordModel->set('modulestatus','c_complete');
        $recordModel->save();

        //同步到合同附件列表
        $servicecontractsid = $recordModel->get('contractid');

        $fileId = $recordModel->get('file');
        $newFile = explode('##',$fileId);
        $sql2 = "update vtiger_files set description='ServiceContracts',relationid=?,style='files_style1',deliversuserid=?,delivertime=?,filestate='filestate2' where attachmentsid=?";
        $db->pquery($sql2,array($servicecontractsid,$current_user->id,date('Y-m-d H:i:s'),$newFile[1]));

        $serviceRecordModel = Vtiger_Record_Model::getInstanceById($servicecontractsid,'ServiceContracts');
        $comparesql = '';
        if($serviceRecordModel->get('total')>=$recordModel->get('staypaymentjine')){
            $comparesql = ',isclose=1';
        }
        //节点自动审批
        $db->pquery("UPDATE vtiger_staypayment SET workflowsnode=(SELECT vtiger_salesorderworkflowstages.workflowstagesname FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='Staypayment' LIMIT 1),returnid=?".$comparesql." WHERE staypaymentid=?", array($recordId,$id, $recordId));
        $params['salesorderid'] = $request->get('record');


        $updateSql = " UPDATE  vtiger_salesorderworkflowstages SET modulestatus=?,isaction=2,auditorid=?,auditortime=?,schedule=100 WHERE salesorderid = ?  AND workflowsid =?";
        $db->pquery($updateSql,array('c_complete',$current_user->id,date("Y-m-d H:i:s"),$recordId,$this->stayPaymentWorkFlowSid));

        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$recordId,'salesorderworkflowstagesid'=>0));

        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    function supplydata(Vtiger_Request $request){
        $record = $request->get('record');
        $field = $request->get("field");
        $value = trim($request->get("value"));
        $value = str_replace("　",' ',$value);
        $value = preg_replace("/^[\s\v".chr(227).chr(128)."]+/","", $value); //替换开头空字符
        $value = rtrim($value);
//        $value = preg_replace("/[\s\v".chr(227).chr(128)."]+$/","", $value); //替换结尾空字符
        $return = array('success'=>false);
        $response = new Vtiger_Response();
        if(!$record || !in_array($field,array('currencytype','staypaymentname','staypaymentjine','startdate','enddate')) || !$value){
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($return);
            $response->emit();
            return;
        }
        $autoResult=$this->getAutoResult($record,$field,$value);
        if($autoResult['msg']){
            //如果返回有msg信息,代表是自动创建的
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($autoResult);
            $response->emit();
            return;
        }
        global $adb;
        $recordModel = Staypayment_Record_Model::getInstanceById($record,'Staypayment');
        if($recordModel->get('staypaymenttype')=='fixation' && $field=='staypaymentjine'){
            $sql = "update vtiger_staypayment set {$field} = ?,surplusmoney=? where staypaymentid=?";
            $res = $adb->pquery($sql,array($value,$value,$record));
        }else{
            $sql = "update vtiger_staypayment set {$field} = ? where staypaymentid=?";
            $res = $adb->pquery($sql,array($value,$record));
        }
        $return = array('success'=>true);
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($return);
        $response->emit();
    }
    public function downfileZip(Vtiger_Request $request){
        ini_set('memory_limit','1024M');//递归使用到了内存
        set_time_limit(0);
        global $adb,$root_directory;
        $filename=$request->get('filename');
        $filenames=explode(',',$filename);
        if(!empty($filenames)){
            $placeholder=array_map(function($v){return '?';},$filenames);
            $query="SELECT contract_no,vtiger_staypayment.file 
              FROM vtiger_staypayment LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_staypayment.contractid WHERE contract_no in(".implode(",",$placeholder).")";
            $result=$adb->pquery($query,$filenames);
            if($adb->num_rows($result)){
                $arrays=array();
                $cnoarrays=array();
                while($row=$adb->fetch_array($result)){
                    $fileinfo=explode('*|*',$row['file']);
                    $cno=$row['contract_no'];
                    if(!empty($fileinfo)){
                        foreach ($fileinfo as $value) {
                            $file=explode("##",$value);
                            if($file[1]>0){
                                $arrays[$file[1]]=iconv("UTF-8", "GBK//IGNORE", $cno.'--'.$file[0]);
                                $cnoarrays[$file[1]]=$cno;
                            }
                        }
                    }
                }
                if(!empty($arrays)){
                    $query="SELECT attachmentsid,CONCAT(path,attachmentsid,'_',newfilename) as 'filepath',attachmentsid FROM vtiger_files WHERE attachmentsid in(".implode(',',array_keys($arrays)).")";
                    $result=$adb->pquery($query,array());
                    if($adb->num_rows($result)){
                        $zipPath = $root_directory.'storage/download';
                        !is_dir($zipPath)|| mkdir($zipPath, 0755,true);
                        $zipName = $zipPath . '/' . microtime(true) . '.zip';
                        $zip = new ZipArchive();
                        if ($zip->open($zipName, ZIPARCHIVE::CREATE) !== TRUE) {
                            exit('create zip fault');
                        }
                        $newcnoarrays=array();
                        while($row=$adb->fetch_array($result)){
                            $filepath=$root_directory.$row['filepath'];
                            if(file_exists($filepath)){
                                $zip->addFile($filepath, $arrays[$row['attachmentsid']]);
                                $newcnoarrays[]=$cnoarrays[$row['attachmentsid']];
                            }
                        }
                        $filenamestmp=array_unique($filenames);
                        $newcnoarraystmp=array_unique($newcnoarrays);
                        $newdiff=array_diff($filenamestmp,$newcnoarraystmp);
                        if(!empty($newdiff)){
                            $zip->addFromString(iconv("UTF-8", "GBK//IGNORE", "不存在附件的合同编号.txt"), implode(",",$newdiff));
                        }
                        $zip->close();
                        header("Pragma: public");
                        header("Cache-Control: private");
                        header("Content-Description: File Transfer");
                        header('Content-Disposition: attachment; filename="download' . date('Ymd') . '.zip"');
                        header("Content-Type: application/zip");
                        header("Content-Transfer-Encoding: binary");
                        header('Content-Length: ' . filesize($zipName));
                        @readfile($zipName);
                        @unlink($zipName);
                    }else{
                        echo '没有附件';
                    }

                }else{
                    echo '没有附件1';
                }
            }else{
                echo '未找到合同';
            }
        }else{
            echo '没有合同';
        }
    }

    /**
     * 获取更改自动匹配效果
     * @param $record
     * @param $field
     * @param $value
     * @return array|bool
     */
    public function getAutoResult($record,$field,$value){
        global $adb;
        $sql="select * from vtiger_staypayment where staypaymentid=".$record;
        $stayPaymentArray=$adb->run_query_allrecords($sql);
        $stayPaymentArray=$stayPaymentArray[0];
        if($stayPaymentArray['isauto']){
            //是自动生成的
            if(in_array($field,array('currencytype','staypaymentname'))){
                return array('success'=>false,'msg'=>'虚拟新建的代付款不允许更改货币类型和代付款客户');
            }
            if($field=='staypaymentjine'){
                $staypaymentjine=$stayPaymentArray['staypaymentjine'];
                if(bccomp($value,$staypaymentjine,2)>=0){
                    $sql = "update vtiger_staypayment set {$field} = ?,surplusmoney=? where staypaymentid=?";
                    $surplusmoney=bcadd(bcsub($value,$staypaymentjine),$stayPaymentArray['surplusmoney']);
                    $adb->pquery($sql,array($value,$surplusmoney,$record));
                    return array('success'=>true,'msg'=>'更改金额以及修改剩下金额');
                }else{
                    return array('success'=>false,'msg'=>'虚拟新建的代付款代付款金额更改必须不小于原始金额');
                }
            }
        }
        return array('success'=>true);
    }

    /**
     * 查看身份证是否有效
     * @param Vtiger_Request $request
     */
    public function getIdCardCheck(Vtiger_Request $request){
        $idcard=$request->get('idcard');
        $payer=$request->get('payer');
        $record=$request->get('record');
        //先查我们的erp user数据库里有没有
        global $adb;
        $result=$adb->pquery('select id,last_name from vtiger_users where idcard=?',array($idcard));
        if($adb->num_rows($result)){
            //身份证号是内部员工的，不允许
            $data['flag']=false;
            $data['msg']='打款人身份证号存在于人员系统，无法提交';
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
        }else{
            //在user里查不到，去新的身份证库里查
            $result=$adb->pquery('select name from vtiger_idcard where idcard=?',array($idcard));
            $userName=$adb->query_result($result, 0, 'name');
            if($userName){
                if($payer!=$userName){
                    //库里所存的打款人姓名和身份证不符
                    $data['flag']=false;
                    $data['msg']='打款人姓名和身份证不符';
                    $response = new Vtiger_Response();
                    $response->setResult($data);
                    $response->emit();
                }else{
                    //通过验证
                    $data['flag']=true;
                    $response = new Vtiger_Response();
                    $response->setResult($data);
                    $response->emit();
                }
            }else{
                //我们库里没有存,先查库看有没有失败的再调用外来接口
                $sql="select id from vtiger_idcardlog where idcard=? and name=? and successorfail='fail'";
                $result=$adb->pquery($sql,array($idcard,$payer));
                if($adb->num_rows($result)){
                    //已经有失败的了，直接返回
                    $this->juHeLog(null,$idcard,$payer,$record);
                    $data['flag']=false;
                    $data['msg']='打款人的信息不真实，无法提交，请提供真实的打款人姓名和身份证号';
                    $response = new Vtiger_Response();
                    $response->setResult($data);
                    $response->emit();
                }else{
                    $serviceRecord=new ServiceContracts_Record_Model();
                    $verificationArray=$serviceRecord->realNameCheck(array('name'=>$payer,'identityNumber'=>$idcard));
                    //接口调用日志
                    $responseArray=json_decode($verificationArray['response'],true);
                    $this->juHeLog($verificationArray,$idcard,$payer,$record);
                    if($responseArray['code']&&$responseArray['code']=='10000'){
                        if($responseArray['data']['result']==1){
                            //通过验证,把数据整到数据库
                            $idcardArray['idcard']=$idcard;
                            $idcardArray['name']=$payer;
                            $adb->run_insert_data('vtiger_idcard',$idcardArray);
                            $data['flag']=true;
                            $response = new Vtiger_Response();
                            $response->setResult($data);
                            $response->emit();
                        }else{
                            $data['flag']=false;
                            $data['msg']='打款人信息不真实，无法提交，请提供真实的打款人姓名和身份证号';
                            $response = new Vtiger_Response();
                            $response->setResult($data);
                            $response->emit();
                        }
                    }else{
                        $data['flag']=false;
                        $data['msg']='验证失败';
                        $response = new Vtiger_Response();
                        $response->setResult($data);
                        $response->emit();
                    }
                }
            }
        }
    }

    /**
     * 记录调用接口日志
     * @param $verificationArray
     * @param $idcard
     * @param $payer
     * @param $record
     */
    public function juHeLog($verificationArray,$idcard,$payer,$record){
        global $current_user,$adb;
        $insert['creatid']=$current_user->id;
        $insert['createdtime']=date('Y-m-d H:i:s');
        $insert['requestjson']=$verificationArray['request'];
        $insert['responsejson']=$verificationArray['response'];
        $responseArray=json_decode($verificationArray['response'],true);
        $insert['successorfail']='fail';
        $responseArray['code']&&$responseArray['code']=='10000'&&$responseArray['data']['result']==1&&$insert['successorfail']='success';
        $insert['idcard']=$idcard;
        $insert['name']=$payer;
        $insert['recordid']=$record;
        $insert['source']='pc';
        $adb->run_insert_data('vtiger_idcardlog',$insert);
    }

    /**
     * 重新提交
     * @param Vtiger_Request $request
     */
    public function reSubmit(Vtiger_Request $request){
        global $adb;
        $record=$request->get('record');
        $sql="select receivedpaymentsid from vtiger_receivedpayments where staypaymentid=".$record;
        if(!$adb->run_query_allrecords($sql)){
            $adb->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='Staypayment'", array($record));
            $adb->pquery("UPDATE vtiger_staypayment SET workflowsid=null,workflowstime=null,workflowsnode='重新提交',modulestatus='a_normal' WHERE staypaymentid=?", array($record));
            $data['flag']=true;
            $data['msg']='重新提交成功';
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
        }else{
            $data['flag']=false;
            $data['msg']='此代付款已使用，不能重新提交';
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
        }
    }

    /**
     * 导出
     * @param Vtiger_Request $request
     */
    public function export(Vtiger_Request $request){
        set_time_limit(0);
        ini_set('memory_limit',-1);
        //文件名
        global $root_directory,$site_URL,$adb,$current_user;
        $path=$root_directory.'temp/';
        $filename = '代付款导出';
        $filename = (strtolower(substr(PHP_OS,0,3))=='win') ? mb_convert_encoding($filename,'gbk','UTF-8') : $filename;
        $filename=$path.$filename.date('Ymd').$current_user->id.'.csv';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        //sql执行
        $sql=$request->get('sql');
        $result=$adb->run_query_allrecords(htmlspecialchars_decode($sql));
        //头信息
        $fp=fopen($filename,'w');
        $array=array_map(function ($value){
            return iconv('utf-8','gb2312',$value);
        },array('代付款编号','合同编号','合同客户名称','代付款客户','代付款金额','货币类型','状态','匹配后剩余代付款金额','代付款开始时间','代付款到期时间','流程时间','创建时间','创建人','选择类型','备注&说明','打款人全称','打款人身份证号','打款人身份类型','纳税人识别号'));
        fputcsv($fp,$array);
        //设置边框
        if(!empty($result)){
            foreach($result as $key=>$value){
                $newValue=array($value['staymentcode'],$value['contractid'],$value['accountid'],$value['staypaymentname'],$value['staypaymentjine'],$value['currencytype'],vtranslate($value['modulestatus'],'Staypayment'),$value['surplusmoney'],$value['startdate'],$value['enddate'],$value['workflowstime'],$value['createdtime'],$value['smcreatorid'],vtranslate($value['staypaymenttype'],'Staypayment'),$value['overdue'],$value['payer'],$value['idcard'],vtranslate($value['payertype'],'Staypayment'),$value['taxpayers_no']);
                $newValue=array_map(function ($val){
                    return iconv('utf-8','gb2312',$val)."\t";
                },$newValue);
                fputcsv($fp,$newValue);
            }
        }
        fclose($fp);
        // 设置工作表的名移
        echo "<script>url='".$site_URL.'/temp/代付款导出'.date('Ymd').$current_user->id.'.csv'."'; window.location.href=url</script>";
    }

    public function submitremark(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        //备注时间  备注人  备注信息 备注节点
        $record = $request->get('record');
        $reject = $request->get('reject');
        $rejectname=$request->get('rejectname');
        $stagerecordid=$request->get('stagerecordid');
        $isbackname = $request->get('isbackname');
        $isbackid = $request->get('isrejectid');
        $modulename = $request->get('src_module');
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $userid=$currentUserModel->getId();
        $time = getDateFormat();
        $attachmentsids=$request->get('attachmentsid');
        $salesorderhistory = $db->pquery('insert into vtiger_salesorderremark (`reject`,`salesorderid`,`rejecttime`,`rejectid`,`rejectname`,`workflowerstagesid`,`modulename`,`rejectnameto`) values(?,?,?,?,?,?,?,?)', array($reject, $record, $time, $userid, $rejectname, $stagerecordid, $modulename, $isbackname));

        $sql="select salesorderhistoryid from vtiger_salesorderremark where salesorderid=? and reject=? order by rejecttime desc limit 1";
        $result=$db->pquery($sql,array($record,$reject));
        $salesorderhistoryid=$db->query_result($result,0,'salesorderhistoryid');
        foreach ($attachmentsids as $attachmentsid){
            $sql="update vtiger_files set description=?,relationid=? where attachmentsid=?";
            $db->pquery($sql,array('salesorderremark',$salesorderhistoryid,$attachmentsid));
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($salesorderhistory);
        $response->emit();
    }

    //是否需要文件
    public function isNeedFile(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $taxpayers_no = trim($request->get('taxpayers_no'));
        $payer = trim($request->get('payer'));
        $idcard=trim($request->get('idcard'));
        $accountid=trim($request->get('accountid'));
        $type=trim($request->get('type'));
        $record=trim($request->get('record'));
        $stay_type=trim($request->get('stay_type'));
        $staypaymenttype=trim($request->get('staypaymenttype'));
        $currencytype=trim($request->get('currencytype'));
        $staypaymentjine=trim($request->get('staypaymentjine'));

        $data['flag']=false;
        $flag=true;
        if($payer&&$record>0){
            //编辑时
            $sql="select * from vtiger_receivedpayments where staypaymentid =? order by createtime desc limit 1";
            $result=$db->pquery($sql,array($record));
            if($db->num_rows($result)>0){
                //代付款已经匹配上了，使用过了
                $paymentchannel=$db->query_result($result,0,'paymentchannel');
                $paytitle=$db->query_result($result,0,'paytitle');
                $receivedpaymentsid=$db->query_result($result,0,'receivedpaymentsid');
                if($paymentchannel=='对公转账'&&$paytitle!=$payer){
                    $data['flag']=true;
                    $data['type']=1;
                    $data['msg']='对应回款的付款渠道是【对公转账】，打款人全称与对应的回款抬头不一致（回款抬头是'.$paytitle.'）';
                    $flag=false;
                }else if($paymentchannel=='支付宝转账'){
                    $sql="SELECT * FROM vtiger_receivedpayments WHERE REPLACE (?,' ','') LIKE REPLACE (REPLACE (paytitle,' ',''),'*','_') and receivedpaymentsid=?";
                    $result=$db->pquery($sql,array($payer,$receivedpaymentsid));
                    if($db->num_rows($result)==0){
                        $data['flag']=true;
                        $data['type']=1;
                        $data['msg']='对应回款的付款渠道是【支付宝转账】，打款人全称与对应的回款抬头不相似（回款抬头是'.$paytitle.'）';
                        $flag=false;
                    }
                }
                if($staypaymenttype=='fixation'){
                    //处理固定金额虚拟打款下的问题
                    $sql="select * from vtiger_staypayment where staypaymentid=".$record;
                    $stayPaymentArray=$db->run_query_allrecords($sql);
                    $stayPaymentArray=$stayPaymentArray[0];
                    if($stayPaymentArray['isauto']){
                        //是自动生成的
                        if($stayPaymentArray['currencytype']!=$currencytype){
                            $data['flag']=true;
                            $data['type']=1;
                            $data['msg']='虚拟新建的代付款不允许更改货币类型';
                            $flag=false;
                        }
                    }
                    if(bccomp($staypaymentjine,$stayPaymentArray['staypaymentjine'],2)<0){
                        $data['flag']=true;
                        $data['type']=1;
                        $data['msg']='已匹配过回款的代付款金额更改必须不小于原始金额';
                        $flag=false;
                    }
                }
            }
        }
        if($flag){
            if($type=='in'){
                if($idcard&&$payer){
                    //已经有了
                    $sql="select staypaymentid from vtiger_staypayment where accountid=? and idcard=? and payer=? and modulestatus=? and payertype=?";
                    $result=$db->pquery($sql,array($accountid,$idcard,$payer,'c_complete',$stay_type));
                    if($db->num_rows($result)>0){
                        $data['flag']=false;
                    }else{
                        //身份证和打款人都有的情况下去判断要不要流程
                        $sql="select staypaymentid from vtiger_staypayment where accountid!=? and idcard=? and payer=? and payertype=?";
                        $result=$db->pquery($sql,array($accountid,$idcard,$payer,$stay_type));
                        if($db->num_rows($result)>0){
                            $data['flag']=true;
                        }
                    }
                }else if($payer&&$taxpayers_no){
                    $sql="select staypaymentid from vtiger_staypayment where accountid=? and taxpayers_no=? and modulestatus=? and payertype=?";
                    $result=$db->pquery($sql,array($accountid,$taxpayers_no,'c_complete',$stay_type));
                    if($db->num_rows($result)>0){
                        $data['flag']=false;
                    }else{
                        //纳税人识别号和打款人都有的情况下去判断要不要流程
                        $sql="select staypaymentid from vtiger_staypayment where accountid!=? and taxpayers_no=? and payertype=?";
                        $result=$db->pquery($sql,array($accountid,$taxpayers_no,$stay_type));
                        if($db->num_rows($result)>0){
                            $data['flag']=true;
                        }
                    }
                }
            }else{
                //境外
                $sql="select staypaymentid from vtiger_staypayment where accountid=? and  payer=? and modulestatus=? and payertype=?";
                $result=$db->pquery($sql,array($accountid,$payer,'c_complete',$stay_type));
                if($db->num_rows($result)>0){
                    $data['flag']=false;
                }else{
                    $sql="select staypaymentid from vtiger_staypayment where accountid!=? and  payer=? and payertype=?";
                    $result=$db->pquery($sql,array($accountid,$payer,$stay_type));
                    if($db->num_rows($result)>0){
                        $data['flag']=true;
                    }
                }
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($data);
        $response->emit();
    }

    public function delReceive(Vtiger_Request $request){
        global $adb,$current_user;
        $recordId=$request->get('record');
        $data=array('flag'=>false,'msg'=>'删除失败');
        if($recordId){
            $sql="select receivedpaymentsid from vtiger_receivedpayments where staypaymentid=?";
            $result=$adb->pquery($sql,array($recordId));
            if($adb->num_rows($result)>0){
                $data=array('flag'=>false,'msg'=>'代付款已被使用，请先解绑');
            }else{
                $sql="select smownerid from vtiger_crmentity where crmid=?";
                $result=$adb->pquery($sql,array($recordId));
                $smownerid=$adb->query_result($result,0,'smownerid');
                if($current_user->id!=$smownerid){
                    $Stay_Module = new Staypayment_Module_Model();
                    $flag=$Stay_Module->exportGrouprt('Staypayment','Delete');
                    if ($flag) {
                        //删除
                        $sql="delete from vtiger_staypayment where staypaymentid=?";
                        $adb->pquery($sql,array($recordId));
                        $data=array('flag'=>true);
                    }else{
                        //看是不是客户负责人
                        $sql="select vtiger_crmentity.smownerid from vtiger_staypayment left join vtiger_crmentity on vtiger_staypayment.accountid=vtiger_crmentity.crmid where vtiger_staypayment.staypaymentid=?";
                        $result=$adb->pquery($sql,array($recordId));
                        $accountSmownerid=$adb->query_result($result,0,'smownerid');
                        if($current_user->id!=$accountSmownerid){
                            $data=array('flag'=>false,'msg'=>'代付款需要创建人或者财务指定人或者客户负责人才能删除');
                        }else{
                            //删除
                            $sql="delete from vtiger_staypayment where staypaymentid=?";
                            $adb->pquery($sql,array($recordId));
                            $data=array('flag'=>true);
                        }
                    }
                }else{
                    //删除
                    $sql="delete from vtiger_staypayment where staypaymentid=?";
                    $adb->pquery($sql,array($recordId));
                    $data=array('flag'=>true);
                }
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 延迟导出
     */
    public function delayExport(Vtiger_Request $request){
        set_time_limit(0);
        ini_set('memory_limit',-1);
        global $root_directory,$site_URL,$adb,$current_user;
        $path=$root_directory.'temp/';
        $filename = '代付款延迟签收导出';
        $filename = (strtolower(substr(PHP_OS,0,3))=='win') ? mb_convert_encoding($filename,'gbk','UTF-8') : $filename;
        $filename=$path.$filename.date('Ymd').$current_user->id.'.csv';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $json= urldecode($request->get('json'));
        $jsonArray=explode('&',$json);
        $bugFreeQuery=array();
        foreach ($jsonArray as $value){
            $jsonList=explode('=',$value);
            $bugFreeQuery[$jsonList[0]]=$jsonList[1];
        }
        if(!empty($request)){
            $_REQUEST['BugFreeQuery'] = json_encode($bugFreeQuery);
            $_REQUEST['public']='Delay';
            $_REQUEST['page']=1;
            $_REQUEST['limit']=1000000;
        }
        $pagingModel = new Vtiger_Paging_Model();   //分页
        $pagingModel->set('page', 1);
        $pagingModel->set('limit', 1000);
        $listViewModel = new Staypayment_ListView_Model();
        $moduleModel = Vtiger_Module_Model::getInstance('Staypayment');//对象实例化放入缓存中
        $entityInstance = CRMEntity::getInstance('Staypayment');
        $queryGenerator = new KQueryGenerator($current_user,$entityInstance,$moduleModel);//查询条件，
        $listViewModel->set('module', $moduleModel)->set('query_generator', $queryGenerator);
        $listViewRecordModels=$listViewModel->getListViewEntries($pagingModel);
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
    }

}
