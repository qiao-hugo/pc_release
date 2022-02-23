<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger Entity Record Model Class
 */
class SContractNoGeneration_Record_Model extends Vtiger_Record_Model {

    private $commonView='common/view?contractId=';//合同预览
    public $sealType=array(
        'batchSign'=>'20220104175834114285',
        'SupplierContractsOtherFirst'=>'20220104174956895066',
        'SupplierContractsWeFirst'=>'20220104174934367538',
        'ServiceContractsOtherFirst'=>'20220104174911040211',
        'ServiceContractsWeFirst'=>'20220104174808456627',
        'ContractsAgreementWeFirst'=>'20220112105133561732',
        'ContractsAgreementOtherFirst'=>'20220112105104459630',
        'SuppContractsAgreementWeFirst'=>'20220112105029726898',
        'SuppContractsAgreementOtherFirst'=>'20220112104943975146',
        'defaultSign'=>'20220112203810740911'
    );

    public static function getMosaicSql($row){
        $sql='';
        $updatesql='';
        if(!empty($row['prefix'])){
            $sql.=" and prefix='".$row['prefix']."'";
            $prefix=$row['prefix'];
            $updatesql.="prefix='".$row['prefix']."',";
        }else{
            $sql.=" and prefix=''";
            $updatesql.="prefix='',";
            $prefix='';
        }
        if($row['company_code']==1){
            global $adb;
            $query="SELECT company_codeno FROM `vtiger_company_code` WHERE company_code=?";
            $result=$adb->pquery($query,array($_POST['company_code']));
            $dataresult=$adb->query_result_rowdata($result,0);
            $sql.=" and company_codeno='".$dataresult['company_codeno']."'";
            $company_code=$dataresult['company_codeno'];
            $_POST['company_codeno']=$dataresult['company_codeno'];
            $updatesql.="company_code='".$_POST['company_code']."',";
            $updatesql.="company_codeno='".$dataresult['company_codeno']."',";
        }else{
            $sql.=" and company_code=''";
            $company_code='';
            $updatesql.="company_code='',";
        }
        if($row['products_code']==1){
            $sql.=" and products_code='".$_POST['products_code']."'";
            $products_code=$_POST['products_code'];
            $products_codeflag=1;
            $updatesql.="products_code='".$_POST['products_code']."',";
        }else{
            $sql.=" and products_code=''";
            $products_code='';
            $products_codeflag=2;
            $updatesql.="products_code='',";
        }
        if($row['year_code']==1){
            $sql.=' and year_code='.date("Y");
            $updatesql.='year_code='.date("Y").',';
            $year_code=date("Y");
        }else{
            $sql.=" and year_code=''";
            $updatesql.="year_code='',";
            $year_code='';
        }
        if($row['month_code']==1){
            $sql.=" and month_code='".date("m")."'";
            $updatesql.="month_code='".date("m")."',";
            $month_code=date("m");
        }else{
            $sql.=" and month_code=''";
            $updatesql.="month_code='',";
            $month_code='';
        }

        if($row['day_code']==1){
            $sql.=" and day_code='".date("d")."'";
            $updatesql.="day_code='".date("d")."',";
            $day_code=date("d");
        }else{
            $sql.=" and day_code=''";
            $updatesql.="day_code='',";
            $day_code='';
        }

        if($row['number']>0){
            $sql.=' and number='.$row['number'];
            $updatesql.='number='.$row['number'].',';
        }

        if(!empty($row['interval_code_one'])){
            $sql.=" and interval_code_one='".$row['interval_code_one']."'";
            $updatesql.="interval_code_one='".$row['interval_code_one']."',";
            $interval_code_one=$row['interval_code_one'];
        }else{
            $sql.=" and interval_code_one=''";
            $updatesql.="interval_code_one='',";
            $interval_code_one='';
        }
        if(!empty($row['interval_code'])){
            $sql.=" and interval_code='".$row['interval_code']."'";
            $updatesql.="interval_code='".$row['interval_code']."',";
            $interval_code=$row['interval_code'];
        }else{
            $sql.=" and interval_code=''";
            $updatesql.="interval_code='',";
            $interval_code='';
        }

        if(!empty($row['interval_code_two'])){
            $sql.=" and interval_code_two='".$row['interval_code_two']."'";
            $updatesql.="interval_code_two='".$row['interval_code_two']."',";
            $interval_code_two=$row['interval_code_two'];
        }else{
            $sql.=" and interval_code_two=''";
            $updatesql.="interval_code_two='',";
            $interval_code_two='';
        }
        $codeprefix=$prefix.$interval_code.$company_code.$interval_code_one.$products_code.$interval_code_two.$year_code.$month_code.$day_code;
        return array('codeprefix'=>$codeprefix,'updatesql'=>$updatesql,'sql'=>$sql,'products_codeflag'=>$products_codeflag);
    }
    public static function getistrue(){
        $db=PearDatabase::getInstance();
        $query="SELECT * FROM `vtiger_servicecontracts_rule` WHERE servicecontractsruleid=?";
        $result=$db->pquery($query,array($_POST['sc_related_to']));
        $num=$db->num_rows($result);
        if($num){
            $row=$db->query_result_rowdata($result);
            $MosaicSql=self::getMosaicSql($row);
            $query='SELECT maxnumber FROM `vtiger_scontractnogeneration` WHERE 1=1'.$MosaicSql['sql'].' ORDER BY scontractnogenerationid DESC limit 1';
            $result=$db->pquery($query,array());
            $num=$db->num_rows($result);
            $str='1';
            $max_limit=str_pad($str,$row['number'],1,STR_PAD_LEFT);
            $max_limit=$max_limit*9;
            $arr['products_codeflag']=$MosaicSql['products_codeflag'];
            if($num){
                $scrow=$db->query_result_rowdata($result);
                $maxnumber=$scrow['maxnumber'];
                $arr['max_limit']=$max_limit-$maxnumber;
                return $arr;
            }else{
                $arr['max_limit']=$max_limit;
                return  $arr;
            }
        }else{
            return array('max_limit'=>0);
        }
    }

    /**
     * 获取章管家access_token
     */
    public function getSealAccessToken()
    {
        global $sealHandleUrl, $sealClientId, $sealClientSecret,$root_directory;

        $cache_token = @file_get_contents($root_directory.'/zhangguanjiaaccesstoken.txt');
        $tokens = json_decode($cache_token, true);
        if ($tokens['errcode']==0 && $tokens['timeout'] > time()) {
            return $tokens['accessToken'];
        }

        $postData = array(
            "client_id" => $sealClientId,
            "client_secret" => $sealClientSecret,
            'grant_type'=>'code',
            'response_type'=>'json',
        );

        $result = $this->https_requestcomm($sealHandleUrl.'api/getToken.htm' . "?" . http_build_query($postData), "", '',true);
        $res = json_decode($result, true);
        if ($res['errcode']!=0) {
            return false;
        }
        $res['timeout'] = time() + 55 * 60;
        file_put_contents($root_directory.'/zhangguanjiaaccesstoken.txt', json_encode($res));
        return $res['accessToken'];
    }

    function postCURLHeader(){
        $tokenHeader = $this->getSealAccessToken();
        return array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "Authorization:".'Bearer '.$tokenHeader
        ));
    }

    function getCURLHeader(){
        $tokenHeader = $this->getSealAccessToken();
        return array(CURLOPT_HTTPHEADER=>array(
            "Authorization:".'Bearer '.$tokenHeader
        ));
    }

    //添加用印申请to章管家
    public function batchCreateSeal($sealTypeKey,$uid,$sealApplyId,$name,$sealIds,$file_count=2,$apply_status=6,$isUpdate=false){
        $args=array();
        $sealType=$this->sealType[$sealTypeKey];
        $args1=array();
        foreach ($sealIds as $key=>$sealId){
            $num=1+$key;
            $args1['seal_ids'.$num]=strval($sealId);
            $args1['use_count'.$num]=strval(1000);
            $args1['is_sealed_bid'.$num]=true;
            $args1['sealed_bid_count'.$num]=strval(1000);
        }

        $args2=array(
            'file_count'=>strval($file_count),
            'uid'=>strval($uid),
            'apply_status'=>strval($apply_status),
            'sealapply_id'=>$sealApplyId,
            'type_id'=>$sealType,
            'name'=>$name,
        );
        $args=array_merge($args1,$args2);

        global $sealHandleUrl;
        $viewURL=$sealHandleUrl.'api/sealApply/batchCreate.htm';
        return $this->https_requestcomm($viewURL,json_encode(array($args)),$this->postCURLHeader(),true);
//        if(!$isUpdate){
//            $viewURL=$sealHandleUrl.'api/sealApply/batchCreate.htm';
//            return $this->https_requestcomm($viewURL,json_encode(array($args)),$this->postCURLHeader(),true);
//        }else{
//            $viewURL=$sealHandleUrl.'api/sealApply/update.htm';
//            return $this->https_requestcomm2($viewURL,json_encode($args),$this->postCURLHeader(),true,'put');
//        }
    }

    //非标准向章管家同步文件
    public function sendFile($attachmentsid,$sealapply_id,$uid){
        global $adb,$root_directory,$sealHandleUrl;
        $result = $adb->pquery("SELECT * FROM vtiger_files WHERE delflag=0 and attachmentsid=?", array($attachmentsid));
        if($adb->num_rows($result)) {
            while ($row=$adb->fetchByAssoc($result)){
                $newfilename = $row['newfilename'];
                $name = $row['name'];
                $path = $row['path'];
                $type = $row['type'];
                $fileid = $row['attachmentsid'];
                $filepath = $root_directory . $path . $fileid.'_'.$newfilename;

                $url = $sealHandleUrl . 'api/sealApply/uploadFile.htm';
                $curlset=$this->getCURLHeader();
                $postData=array(
                    'uid'=>$uid,
                    'sealapply_id'=>$sealapply_id
                );
                $jsonData = $this->CURLfileUpload($url, $filepath, $type, $name,$curlset , true,$postData);
                $jsonData=json_decode($jsonData,true);
                if($jsonData['errcode']!=0){
                    return array('success'=>false,'msg'=>'文件上传失败');
                }
            }
            return array('success'=>true,'data'=>array('name'=>$name));
        }
        return array('success'=>false,'msg'=>'无可用附件');
    }



    public function CURLfileUpload($url,$path,$minetype,$postname,$curlset=array(),$islog=false,$postData=array()){
        //1.初识化curl
        $curl = curl_init();
        if (class_exists('CURLFile')) {
            $data = array('file' => new CURLFile(realpath($path),$minetype,$postname));//>=5.5
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
            $data = array('file' => '@' . realpath($path));//<=5.5
        }
        if($postData){
            $data=array_merge($data,$postData);
        }
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        //curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, true );
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true );
        curl_setopt($curl, CURLOPT_TIMEOUT, 100 );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $output=curl_exec($curl);
        curl_close($curl);
        if($islog){
            $this->comm_logs(array('请求URL',$url));
            $this->comm_logs(array('发送DATA',$data));
            $this->comm_logs(array('返回DATA',$output));
        }
        return $output;
    }

    public function getFileData($sealApplyId,$printId=0,$module='',$uid=''){
        global $sealHandleUrl;
        $viewURL=$sealHandleUrl.'api/getSealApplyWatermark.htm?sealapply_id='.$sealApplyId;
        $data = $this->https_requestcomm($viewURL,'',$this->getCURLHeader(),true);
        $data=json_decode($data,true);
        if($data['errcode']!=0){
            return array("success"=>false,'msg'=>$data['errmsg']);
        }

        $fileStr='';
        $fileIds=array();
        foreach ($data['data_list'] as $data_list){
            $returnData = $this->fileSave($data_list['fileContext'],'files_style14',$data_list['fileName'],$printId,$module,$uid);
            if(!$printId){
                $fileStr .=$returnData['fileName'].'##'.$returnData['fileid'].'*|*';
            }
            $contract_no = substr($data_list['fileName'],0,-4);
            $fileIds[$contract_no]=$returnData['fileid'];
        }
        return array('success'=>true,'msg'=>'获取成功','fileStr'=>rtrim($fileStr,'*|*'),'fileIds'=>$fileIds);
    }

    /**
     * 合同附件的保存暂为PDF
     * @param $filepath//附件的URL
     * @param $filestate／／附件的类别
     */
    public function fileSave($filepath,$filestate,$fileName,$relationid,$module='ServiceContractsPrint',$uid=0){
        global $adb;
        $fileData=$this->fileUpload($filepath);
        $fileData['style'] = $filestate;
        $fileData['description'] = $module;
        $fileData['relationid'] =$relationid;
        $fileData['name'] = $fileName;
        $fileData['remarks'] = '';
        $fileData['uploader'] = $uid;
        $fileData['uploadtime'] = date('Y-m-d H:i:s');
        $divideNames = array_keys($fileData);
        $divideValues = array_values($fileData);
        $adb->pquery('INSERT INTO `vtiger_files` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
        return array('fileid'=>$fileData['attachmentsid'],'fileName'=>$fileData['name']);
    }

    /**
     * 文件的保存
     * @param $filepath
     * @return array
     */
    public function fileUpload($fileData,$isHeader=true){
        global $adb;
        $current_id = $adb->getUniqueID("vtiger_files");
        $upload_file_path = decideFilePath();
        $newfilename=time();
        $navFilePath = $upload_file_path . $current_id . "_" . $newfilename;

        file_put_contents($navFilePath,base64_decode($fileData,true));
        return array('path'=>$upload_file_path,'type'=>'application/pdf',  'attachmentsid'=>$current_id,'newfilename'=>$newfilename);
    }


    //模板的doc文件地址
    public function getTemplateDocFile($contract_template,$scontractnogenerationid){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_contract_template where contract_template=?",array($contract_template));
        if(!$db->num_rows($result)){
            return '';
        }
        $row=$db->fetchByAssoc($result,0);
        $file =$row['file'];
        $fileid = explode("##",$file);
        $path = $this->down($fileid[1],$scontractnogenerationid);
        return $path;
    }

    public function down($fileid,$scontractnogenerationid)
    {
        global $current_user,$adb;
        $result = $adb->pquery("SELECT * FROM vtiger_files WHERE attachmentsid=?", array($fileid));
        if($adb->num_rows($result)) {
            $fileDetails = $adb->query_result_rowdata($result);
            global $root_directory;
            $filePath = $root_directory.$fileDetails['path'];
            $fileName = $scontractnogenerationid.'.docx';
            if($fileDetails['newfilename']>0){
                $savedFile = $fileDetails['attachmentsid'] . "_" . $fileDetails['newfilename'];
            }else{
                $t_fileName = base64_encode($fileName);
                $t_fileName = str_replace('/', '', $t_fileName);
                $savedFile = $fileDetails['attachmentsid'] . "_" . $t_fileName;
            }
            if(!file_exists($filePath.$savedFile)){
                $savedFile = $fileDetails['attachmentsid']."_".$fileName;
            }
            $fileSize = filesize($filePath.$savedFile);
            $fileSize = $fileSize + ($fileSize % 1024);
            $dir = $root_directory.'/storage/contracts/';
            $dir=$dir.date('Y').'/'.date('F').'/'.date('d').'/';
            if (fopen($filePath.$savedFile, "r")) {
                $fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);
                if(!is_dir($dir)) {
                    mkdir($dir,0755,true);
                }
                file_put_contents($dir.$fileName,$fileContent);
            }
            return $dir.$fileName;
        }
        return '';
    }



    //数据推到章管家
    public function syncToSealHandler($sealParams,$module,$isUpdate=false){
        $uid=$sealParams['uid'];
        $name=$sealParams['name'];
        $sealapply_id=$sealParams['sealapply_id'];
        $sealseq=$sealParams['sealseq']?$module.$sealParams['sealseq']:'defaultSign';
        $invoicecompany=$sealParams['invoicecompany'];
        $companycode=$sealParams['companycode'];
        $sealplace=$sealParams['sealplace'];

        if( ($companycode == 'ZDWX' || $invoicecompany == '无锡珍岛数字生态服务平台技术有限公司')&& $sealplace == '市区') {
            $companycode = 'ZDWX-cs'; // 无锡珍岛数字生态服务平台技术有限公司-市区时 合同主体编码 切换为ZDWX-cs
        }

        $file_count=$sealParams['file_count']?$sealParams['file_count']:2;
        $apply_status=($sealParams['apply_status']===0 || $sealParams['apply_status'])?$sealParams['apply_status']:6;
        $companyCodeRecordModel = CompayCode_Record_Model::getCleanInstance("CompayCode");
        if ($companycode) {
            $sealcode = $companyCodeRecordModel->getSealCode('',$companycode);
        } else {
            $sealcode = $companyCodeRecordModel->getSealCode($invoicecompany,$companycode);
        }

        if(!$sealcode){
            return array('success'=>false,'msg'=>'请先联系管理员在公司信息设置中添加章管家印章编码');
        }

        $result = $this->batchCreateSeal($sealseq,$uid,$sealapply_id,$name,array($sealcode),$file_count,$apply_status,$isUpdate);

        $data=json_decode($result,true);
        if($data['errcode']!=0){
            return array('success'=>false,'msg'=>$data['errmsg']);
        }
        return array('success'=>true,'msg'=>'');
    }

    public function sendFileToZhangGuanJia($sealParams){
        $uid=$sealParams['uid'];
        $sealapply_id=$sealParams['sealapply_id'];
        $attachmentsids=$sealParams['attachmentsids'];
        $module=$sealParams['module'];
        $servicecontractsprintid=$sealParams['servicecontractsprintid'];

        //发送文件到章管家
        foreach ($attachmentsids as $attachmentsid){
            $data2 = $this->sendFile($attachmentsid,$sealapply_id,$uid);
            if(!$data2['success']){
                return array('success'=>false,'msg'=>$data2['msg']);
            }
        }

        //获取章管家的附件信息
        $data3 = $this->getFileData($sealapply_id,$servicecontractsprintid,$module,$uid);
        if(!$data3['success']){
            return array('success'=>false,'msg'=>$data3['msg']);
        }

        return array('success'=>true,'msg'=>'','fileStr'=>$data3['fileStr'],'fileIds'=>$data3['fileIds']);
    }


    //处理标准合同的模板
    public function handleTemplateData($contract_template,$uid,$scontractnogenerationid){
        global $root_directory;
        $root_directoryt=rtrim($root_directory,"/");
        $root_directoryt=rtrim($root_directoryt,"\\");
        require $root_directoryt.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPWord'.DIRECTORY_SEPARATOR.'PHPWord.php';
        $PHPWord = new PHPWord();
        $tpl = $this->getTemplateDocFile($contract_template,$scontractnogenerationid);
        if(!$tpl){
            return '';
        }
        $dir = $root_directory.'/storage/contracts/';
        $dir=$dir.date('Y').'/'.date('F').'/'.date('d').'/';

        $sql = 'SELECT vtiger_servicecontracts_print.servicecontractsprintid,vtiger_servicecontracts_print.servicecontracts_no,
        vtiger_servicecontracts_print.contract_template,vtiger_company_code.numbered_accounts,vtiger_company_code.bank_account,
        vtiger_company_code.company_code,vtiger_company_code.address,vtiger_company_code.companyfullname,vtiger_company_code.companyname,
        vtiger_company_code.tax,vtiger_company_code.telphone,vtiger_company_code.website,vtiger_company_code.email,vtiger_company_code.zipcode,
        vtiger_servicecontracts_print.contract_template,vtiger_company_code.taxnumber FROM vtiger_servicecontracts_print 
        LEFT JOIN vtiger_company_code ON vtiger_servicecontracts_print.company_code=vtiger_company_code.company_code 
        WHERE vtiger_servicecontracts_print.constractsstatus=\'c_generated\'  and vtiger_servicecontracts_print.contract_template=? 
        and vtiger_servicecontracts_print.scontractnogenerationid=?';
        global $adb;
        $sales = $adb->pquery($sql, array($contract_template,$scontractnogenerationid));
        $rows = $adb->num_rows($sales);
        if ($rows) {
            $fileMatchDatas=array();
            while ($row = $adb->fetchByAssoc($sales)) {
                $newTpl=$dir.$row['servicecontractsprintid'].'.doc';
                $fp=fopen($newTpl,'w');
                fwrite($fp, file_get_contents($tpl));
                fclose($fp);
                $document=$PHPWord->loadTemplate($newTpl);
                $document->setValue('servicecontractsid',urlencode(cookiecode($row['servicecontractsprintid'],'')));
//                $input['servicecontractsid']=urlencode(cookiecode($row['servicecontractsprintid'],''));
//                $input['scpid']=$row['servicecontractsprintid'].'-8';
                $input['servicecontracts_no'] = $row['servicecontracts_no'] == null ? '' : $row['servicecontracts_no'];
                $input['contract_template'] = $row['contract_template'] == null ? '' : $row['contract_template'];
                $input['company_code'] = $row['company_code'] == null ? '' : $row['company_code'];
                $input['address'] = $row['address'] == null ? '' : $row['address'];
                $input['companyfullname'] = $row['companyfullname'] == null ? '' : $row['companyfullname'];
                $input['companyname'] = $row['companyname'] == null ? '' : $row['companyname'];
                $input['fax'] = $row['tax'] == null ? '' : $row['tax'];
                $input['telphone'] = $row['telphone'] == null ? '' : $row['telphone'];
                $input['numbered_accounts'] = $row['numbered_accounts'] == null ? '' : $row['numbered_accounts'];
                $input['bank_account'] = $row['bank_account'] == null ? '' : $row['bank_account'];
                $input['taxnumber'] = $row['taxnumber'] == null ? '' : $row['taxnumber'];
                $input['website'] = $row['website'] == null ? '' : $row['website'];
                $input['email'] = $row['email'] == null ? '' : $row['email'];
                $input['zipcode'] = $row['zipcode'] == null ? '' : $row['zipcode'];

//                $input['number'] = $row['servicecontracts_no'] == null ? '' : $row['servicecontracts_no'];
                $input['bank'] = $row['bank_account'] == null ? '' : $row['bank_account'];
                $input['banknumber'] = $row['numbered_accounts'] == null ? '' : $row['numbered_accounts'];
                $input['company'] = $row['companyname'] == null ? '' : $row['companyname'];
                $input['address'] = $row['address'] == null ? '' : $row['address'];
                $document->setFooter('scpid',$row['servicecontractsprintid'].'-8');
                $document->setHeader('number',$row['servicecontracts_no'],'#','#');
                foreach ($input as $k=>$v){
                    $document->setValue($k,$v,'#','#');
                }
                $document->save($newTpl);


                //上传到章管家
                $this->serverFileUpload($uid,'9999'.$scontractnogenerationid,$newTpl,'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    $row['servicecontracts_no'].'.doc');
                $fileMatchDatas[$row['servicecontracts_no']]=$row['servicecontractsprintid'];
            }
            //从章管家获取
            $data3 = $this->getFileData('9999'.$scontractnogenerationid,'','ServiceContractsPrint',$uid);
            if(!empty($data3['fileIds'])&&!empty($fileMatchDatas)){
                foreach ($data3['fileIds'] as $k=>$fileid){
                    $adb->pquery("update vtiger_servicecontracts_print set templatefileid=? where servicecontractsprintid=?",
                        array($fileid,$fileMatchDatas[$k]));
                }

            }
        }

        unlink($tpl);
    }

    public function serverFileUpload($uid,$sealapply_id,$filepath,$type,$name){
        global $sealHandleUrl;;
        $url = $sealHandleUrl . 'api/sealApply/uploadFile.htm';
        $curlset=$this->getCURLHeader();
        $postData=array(
            'uid'=>$uid,
            'sealapply_id'=>$sealapply_id
        );
        $jsonData = $this->CURLfileUpload($url, $filepath, $type, $name,$curlset , true,$postData);
        $jsonData=json_decode($jsonData,true);
        if(!$jsonData['errcode']){
            return array('success'=>false,'msg'=>'文件上传失败');
        }
        return array('success'=>true,'data'=>array('name'=>$name));
    }


}
