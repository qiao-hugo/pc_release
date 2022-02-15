<?php

class EmployeeAbility_ChangeAjax_Action extends Vtiger_Action_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('abilityListByStaffLevel');
        $this->exposeMethod('operateInfo');
        $this->exposeMethod('fileupload');
        $this->exposeMethod('setEduUrl');
        $this->exposeMethod('rejectHistory');

    }

    function checkPermission(Vtiger_Request $request)
    {
        return true;
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * 根据用户级别获取对应的数据
     *
     * @param Vtiger_Request $request
     */
    public function abilityListByStaffLevel(Vtiger_Request $request)
    {
        $recordId = $request->get('recordid');
        $staffLevel = $request->get("stafflevel");
        global $adb;
        $sql = "select * from vtiger_employee_ability_detail where employeeabilityid = ? and stafflevel=? limit 1";
        $result = $adb->pquery($sql, array($recordId, $staffLevel));
        $response = new Vtiger_Response();

        if ($adb->num_rows($result)) {
            $rowData = $adb->fetchByAssoc($result, 0);
            $content = $rowData['content'];
            $content = json_decode($content, true);
            $response->setResult($content);
            $response->emit();
            exit();
        }
        $response->setError(-1, '请重试');
        $response->emit();
    }

    public function operateInfo(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $staffLevel = $request->get("stafflevel");
        $type = $request->get('type');
        $field = $request->get('field');
        $rejectreason = $request->get('rejectreason');
        $value = $request->get('status');
        $wordsub = $request->get('wordsub');
        $recordModel = EmployeeAbility_Record_Model::getInstanceById($recordId, 'EmployeeAbility');
        $columnvalues = $recordModel->typeColumnData($type,$field,$wordsub,$rejectreason);
        $updateColumn = $recordModel->updateDetailContent($field, $value, $recordId, $columnvalues,$type);
        $response = new Vtiger_Response();
        if (count($updateColumn)) {
            $response->setResult(array('success' => true, 'content' => $updateColumn));
            $response->emit();
            exit();
        }
        $response->setError(-1, '更新失败');
        $response->emit();
    }

    public function fileupload(Vtiger_Request $request){
        $model=$request->get('module');
        $record=$request->get('record');
        $field = $request->get('field');
        $file = $request->get('file');
        $files = explode("base64,",$file);
        $filestream = $files[1];
        $name = $request->get('name');
        $size = $request->get('size');
        $type = $request->get('filedatatype');

        if($name != '' && $size > 0){
            global $current_user;
            global $upload_badext;
            global $adb;
            $current_id = $adb->getUniqueID("vtiger_files");
            //$date_var = date("Y-m-d H:i:s");
            $ownerid = $current_user->id;
            $file_name = $name;
            $file_name=preg_replace('/(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+/u','',$file_name);
            $binFile = sanitizeUploadFileName($file_name, $upload_badext);
            //$uploadfile=str_replace('/','',base64_encode($binFile));//去掉因base64l加密后成的/在误解析成路径引起的问题
            $uploadfile=time();
            $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
            $filetype = $type;
            $filesize = $size;
            $filetmp_name = $name;
            $upload_file_path = decideFilePath();
            file_put_contents($upload_file_path . $current_id . "_" .$uploadfile,base64_decode($filestream));
            if(!file_exists($upload_file_path . $current_id . "_" .$uploadfile)){
                echo json_encode(array('success'=>false,'result'=>array('id'=>$current_id,'name'=>$filename)));
                exit;
            }
            $save_file = 'true';
            $sql2 = "insert into vtiger_files(attachmentsid, name,description, type, path,uploader,uploadtime,newfilename) values(?, ?,?, ?, ?,?,?,?)";
            $params2 = array($current_id, $filename, $model,$filetype, $upload_file_path,$current_user->id,date('Y-m-d H:i:s'),$uploadfile);
            $result = $adb->pquery($sql2, $params2);
            //防止上传后不保存不管保存与否都同步当前记录里
            if(!empty($record)&& $record>0){
                $result3  = $adb->pquery("select userid from vtiger_employee_ability where employeeabilityid=? limit 1",array($record));
                $employeeability = $adb->fetchByAssoc($result3,0);
                $userRecordModel = Vtiger_Record_Model::getCleanInstance("Users");
                global $zhongxiaojingli;
                $last_name = $userRecordModel->managerByCurrentUser($employeeability['userid'],$zhongxiaojingli);
                $columnvalues = array(
                    'wordsub' => 'submitted',
                    'status' => 'underreviewer',
                    'fileid'=>$filename.'##'.$current_id,
                    'nextreviewer'=>1,
                    'reviewresult'=>($last_name?$last_name:'经理').':待处理'
                );
                $recordModel=Vtiger_Record_Model::getInstanceById($record, $model);
                $recordModel->updateDetailContent($field, 'underreviewer', $record, $columnvalues,'');
            }

            echo json_encode(array('success'=>true,'result'=>array('id'=>$current_id,'name'=>$filename)));
        }else{
            echo json_encode(array('success'=>false,'msg'=>'上传失败'));
        }
        exit;
    }

    public function setEduUrl(Vtiger_Request $request){
        $eduurl = $request->get("eduurl");
        $record = $request->get("record");
        global $adb;
        $adb->pquery("update vtiger_employee_ability_column set eduurl=? where employeeabilitycolumnid=?" ,array($eduurl,$record));
        echo json_encode(array('success'=>true,'msg'=>'保存成功'));
    }

    public function rejectHistory(Vtiger_Request $request){
        $recordId = $request->get('record');
        $fieldName = $request->get('fieldname');
        $recordModel = EmployeeAbility_Record_Model::getInstanceById($recordId,'EmployeeAbility');
        $data = $recordModel->lastRejectHistory($recordId,$fieldName);
        if(!count($data)){
            echo json_encode(array('success'=>false,'result'=>$data));
            return;
        }
        echo json_encode(array('success'=>true,'result'=>$data));
    }
}
