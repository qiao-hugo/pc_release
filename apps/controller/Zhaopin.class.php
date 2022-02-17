<?php
/**
 * OA系统接口使用
 * @author cxh
 * @Date 2019/11/13
 */
class Zhaopin extends baseapp
{
    private $zhaopinurl='http://192.168.44.128:18080/';
    private $zhaopinfileurl='http://192.168.44.128:18181/';
    public function index(){
        $this->smarty->display('Zhaopin/index.html');
    }
    /**
     * 对外接口
     */
    public function ZhaopinMobile(){
        $m = $_REQUEST['method'];
        $this->_cs_logs("参数:".$m);
        $body = file_get_contents('php://input');
        $this->_cs_logs("参数1:".$body);
        /*$postArray=array('/department/train',
            '/department/tree/trainer',
            '/department/verify',
            '/employ/listEmploy',
            '/employ/listEmployHeadData',
            '/employ/modifyEmployEntryTime',
            '/employ/moveResumeToStored',
            '/employ/reportEmployEntryTime',
            '/geology/deleteTbagvalue',
            '/geology/deleteTbagvalueDemo',
            '/geology/selectListLabel',
            '/geology/updateTbagValue',
            '/interview/arrangeDeleteInterview',
            '/interview/arrangeModifyInterview',
            '/interview/arrangeNewInterview',
            '/interview/listInterview',
            '/interview/listInterviewHeadData',
            '/interview/makeInterviewFailed',
            '/interview/makeInterviewSuccess',
            '/interview/makeInterviewUncertain',
            '/interview/signInForInterview',
            '/menu/powers',
            '/position/deletePosition',
            '/position/deletePositionSeg',
            '/position/deletesegmentation',
            '/position/importData',
            '/position/insertPosition',
            '/position/listInsertPositionSeg',
            '/position/listPosition',
            '/position/listPositionChannel',
            '/position/listPositionHeadData',
            '/position/listPositionRecruitment',
            '/position/updatePosition',
            '/position/updateState',
            '/resume/backRecommendResult',
            '/resume/batchUpdateResumeToForFollow',
            '/resume/changeResumeInviterByBatch',
            '/resume/detailResume',
            '/resume/findResumeByPhone',
            '/resume/getRecommendDetail',
            '/resume/importResumeData',
            '/resume/listResume',
            '/resume/listResumeCanOffer',
            '/resume/listResumeFile',
            '/resume/listResumeFollow',
            '/resume/listResumeHeadData',
            '/resume/listResumeInterview',
            '/resume/listResumeInterviewer',
            '/resume/listResumeJob',
            '/resume/listResumeMessage',
            '/resume/listResumeRecruitChannel',
            '/resume/listResumeRecruitChannelDetail',
            '/resume/modifyResume',
            '/resume/resetLastStep',
            '/resume/resumeRecommendForwarding',
            '/resume/resumeToInterviewByBatch',
            '/resume/saveResume',
            '/resume/saveResumeFollow',
            '/resume/saveResumeMessage',
            '/resume/sharingResumeByBatch',
            '/resume/updateResumeToForFollow',
            '/resumeTrain/ReNotSignIn',
            '/resumeTrain/batchBriefSummary',
            '/resumeTrain/batchNotSignIn',
            '/resumeTrain/batchSignIn',
            '/resumeTrain/firstTrainReports',
            '/resumeTrain/listResumeTrain',
            '/resumeTrain/listTrainActualTime',
            '/resumeTrain/listTrainReports',
            '/resumeTrain/listTrainState',
            '/resumeTrain/listTrainStateApp',
            '/resumeTrain/listTrainThrough',
            '/resumeTrain/listTrainerUserId',
            '/resumeTrain/reBriefSummary',
            '/resumeTrain/resignIn',
            '/resumeTrainVerify/listResumeTrainVerify',
            '/resumeTrainVerify/listTrainVerifyPassed',
            '/resumeTrainVerify/listTrainVerifyState',
            '/resumeTrainVerify/listTrainVerifyStateApp',
            '/resumeTrainVerify/listTrainerVerifyTime',
            '/resumeTrainVerify/listTrainerVerifyUserId',
            '/resumeTrainVerify/reexamination',
            '/role/add',
            '/role/assign/user',
            '/role/condition',
            '/role/update',
            '/role/update/department',
            '/role/update/menu',
            '/statistics/all',
            '/statistics/channel',
            '/statistics/dataReportForms',
            '/statistics/department',
            '/statistics/graph',
            '/statistics/graphForApp',
            '/statistics/interview',
            '/statistics/invite',
            '/statistics/train',
            '/stored/WithdrawResumeStored',
            '/stored/listResumeStored',
            '/stored/listResumeStoredHeadData',
            '/stored/makeNewResumeFromStored',
            '/stored/makeNewResumeFromStoredSingle',
            '/stored/modifyResumeStoredTag',
            '/stored/moveResumeStored',
            '/stored/removeResumeStoredTag',
            '/stored/saveResumeStoredTag',
            '/upload/deleteFile',
            '/upload/saveFile',
            '/upload/uploadFile',
            '/user/add',
            '/user/all',
            '/user/assign/roles',
            '/user/condition',
            '/user/crm',
            '/user/openOrClose');*/
        $mmethod=explode('&',$m);
        //$method=in_array($mmethod[0],$postArray)?'POST':'GET';
        $method=$_SERVER['REQUEST_METHOD']!='GET'?'POST':'GET';
        if($mmethod[0]!='/upload/uploadFile'){
            echo $this->https_request($this->zhaopinurl.$m, $body,$method);
        }else{
            $this->getfileUpload($this->zhaopinurl.$m);
        }
    }

    /**
     *CURL数据转送
     * @param $url
     * @param null $data
     * @param string $method
     * @param string $type
     * @return bool|string
     */
    private function https_request($url, $data = null,$method='POST',$type='JSON'){
        $curl = curl_init();
        $this->_cs_logs($url);
        $header=$this->getHeaders();
        $header[]="Content-type:application/json;charset=utf-8";
        if($method == 'GET'){
            $getData=$_REQUEST;
            if($getData){
                $querystring = http_build_query($getData);
                $url = $url.'?'.$querystring;
            }
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $this->_cs_logs("返回结果:".$output);
        curl_close($curl);
        return $output;
    }

    /**
     * 写日志，用于测试,可以开启关闭
     * @param data mixed
     */
    private function _cs_logs($data, $file = 'logs_'){
        return ;
        $year	= date("Y");
        $month	= date("m");
        $dir	= './logs/oa/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }

    /**
     * 汉字编码
     * @param $str
     * @return string
     */
    private function UnicodeEncode($str)
    {
        //split word
        preg_match_all('/./u', $str, $matches);

        $unicodeStr = "";
        foreach ($matches[0] as $m) {
            //拼接
            $unicodeStr .= "&#" . base_convert(bin2hex(iconv('UTF-8', "UCS-4", $m)), 16, 10);
        }
        return $unicodeStr;
    }

    /**
     * CURL文件转送
     * @param $url
     * @param $path
     * @param $minetype
     * @param $postname
     * @return array|bool|string
     */
    private function CURLfileUpload($url,$path,$minetype,$postname){
        //1.初识化curl
        $curl = curl_init($url);
        if (class_exists('\CURLFile')) {
            $data = array('file' => new \CURLFile(realpath($path),$minetype,$postname));//>=5.5
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
            $data = array('file' => '@' . realpath($path));//<=5.5
        }
        $header=$this->getHeaders();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, true );
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true );
        curl_setopt($curl, CURLOPT_TIMEOUT, 100 );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $data=curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    /**
     * 文件上传调用
     * @param $url
     * @return array|bool|string
     */
    private function getfileUpload($url){
        $fileInfo = $_FILES['file'];
        $filename = $fileInfo['name'];
        $type = $fileInfo['type'];
        $tmp_name = $fileInfo['tmp_name'];
        $size = $fileInfo['size'];
        $error = $fileInfo['error'];
        //2.判断错误号，只有为0或者是UPLOAD_ERR_OK,没有错误发生，上传成功
        if($error == 0){
            return $this->CURLfileUpload($url,$tmp_name,$type,$filename);
        }else{
            //匹配错误信息
            $a='';
            switch($error){
                case 1:
                    $a='上传文件超过了php配置文件中upload_max_filesize选项的值';
                    break;
                case 2:
                    $a='超过了表单MAX_FILE_SIZE限制的大小';
                    break;
                case 3:
                    $a='文件部分被上传';
                    break;
                case 4:
                    $a='没有选择上传文件';
                    break;
                case 6:
                    $a='没有找到临时目录';
                    break;
                case 7:
                case 8:
                    $a='系统错误';
                    break;
            }
        }
        return '{"code":500,"data":"","errorList":null,"message":"'.$a.'"}';
    }
    public function loadfile(){
        $filepath=$this->zhaopinfileurl;
        $rfilepath=$_REQUEST['file'];
        $url=$filepath.$rfilepath;
        $fileextend=explode('.',$rfilepath);
        $extends=array(
            'bmp'=>'image/bmp',
            'css'=>'text/css',
            'doc'=>'application/msword',
            'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'eml'=>'message/rfc822',
            'htm'=>'text/html',
            'html'=>'text/html',
            'ico'=>'image/x-icon',
            'ief'=>'image/ief',
            'isp'=>'application/x-internet-signup',
            'jfif'=>'image/pipeg',
            'jpe'=>'image/jpeg',
            'jpeg'=>'image/jpeg',
            'jpg'=>'image/jpeg',
            'png'=>'image/png',
            'pdf'=>'application/pdf',
            'js'=>'application/x-javascript',
            'roff'=>'application/x-troff',
            'rtf'=>'application/rtf',
            'texi'=>'application/x-texinfo',
            'texinfo'=>'application/x-texinfo',
            'tgz'=>'application/x-compressed',
            'tif'=>'image/tiff',
            'tiff'=>'image/tiff',
            'xof'=>'x-world/x-vrml',
            'xls'=>'application/vnd.ms-excel',
            'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xpm'=>'image/x-xpixmap',
            'xwd'=>'image/x-xwindowdump',
            'zip'=>'application/zip'
        );
        $contentType=$extends[end($fileextend)];
        if(empty($contentType)){
            $contentType='text/html';
        }
        header("Content-type: ".$contentType);
        echo  file_get_contents($url);
    }

    /**
     * 取得HEADER头信息
     * @return array
     */
    private function getHeaders(){
        $header=array();
        $header[]='loginId:'.$_SESSION['customer_id'];
        //$header[]='customer_id:'.$_SESSION['customer_id'];
        $header[]='customer_id:5442';
        $header[]='reportCode:'.$_SESSION['reports_to_id'];
        $header[]='username:'.$_SESSION['customer_name'];
        $header[]='fullName:'.$this->UnicodeEncode($_SESSION['last_name']);
        $header[]='last_name:'.$this->UnicodeEncode('邱俊华');
        $header[]='roleId:'.$_SESSION['roleid'];
        $header[]='departId:'.$_SESSION['departmentid'];
        return $header;
    }
    function curlData($url,$data,$method = 'GET',$type='json')
    {
        //初始化
        $ch = curl_init();
        $headers = array(
            'form-data' => array('Content-Type: multipart/form-data'),
            'json'      => array('Content-Type: application/json'),
        );
        if($method == 'GET'){
            if($data){
                $querystring = http_build_query($data);
                $url = $url.'?'.$querystring;
            }
        }
        // 请求头，可以传数组
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers[$type]);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);         // 执行后不直接打印出来
        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');     // 请求方式
            curl_setopt($ch, CURLOPT_POST, true);               // post提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);              // post的变量
        }
        if($method == 'PUT'){
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        }
        if($method == 'DELETE'){
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不从证书中检查SSL加密算法是否存在
        $output = curl_exec($ch); //执行并获取HTML文档内容
        curl_close($ch); //释放curl句柄
        return $output;
    }
}
