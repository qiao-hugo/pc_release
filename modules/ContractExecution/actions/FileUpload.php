<?php
/*+************
 * 独立文件上传
 *20141222
 **************/

class ContractExecution_FileUpload_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request)
    {
        $files = $_FILES['File'];
        $model = $request->get('module');
        $record = $request->get('record');
        if($files['size']>1024*1024*3){
            echo json_encode(array('success'=>false,'result'=>array('msg'=>'文件大于3M')));
            exit;
        }
        if(!in_array($files['type'],array('application/pdf','image/jpeg','image/png','image/jpg'))){
            echo json_encode(array('success'=>false,'result'=>array('msg'=>'只支持pdf/png/jpg类型文件')));
            exit;
        }
        if ($files['name'] != '' && $files['size'] > 0) {
            global $root_directory;
            global $current_user;
            global $upload_badext;
            global $adb;
            $current_id = $adb->getUniqueID("vtiger_files");
            //$date_var = date("Y-m-d H:i:s");
            $ownerid = $current_user->id;
            $file_name = $files['name'];
            $binFile = sanitizeUploadFileName($file_name, $upload_badext);
            $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
            //格式限制
            $filetype = $files['type'];
            $allowFileType = ['image/jpeg','image/png','image/gif','image/bmp','application/msword','application/pdf','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            if(!in_array($filetype,$allowFileType)){
                echo json_encode(array('success'=>false,'msg'=>'格式错误！'));
                exit;
            }
            //大小限制
            if($files['size']>10*1024*1024){
                echo json_encode(array('success'=>false,'msg'=>'上传文件大小超过10M！'));
                exit;
            }
            $filetype = $files['type'];
            $filesize = $files['size'];
            $filetmp_name = $files['tmp_name'];
            $upload_file_path = decideFilePath();
            $new_file_name = time();
            $upload_status = move_uploaded_file($filetmp_name, $upload_file_path . $current_id . "_" . $new_file_name);
            $file_path = $root_directory . $upload_file_path . $current_id . "_" . $new_file_name;
            //echo $file_path;
            $save_file = 'true';
            $sql2 = "insert into vtiger_files(attachmentsid, name,description, type, path,uploader,uploadtime,newfilename) values(?, ?,?, ?, ?,?,?,?)";
            $params2 = array($current_id, $filename, $model, $filetype, $upload_file_path, $current_user->id, date('Y-m-d H:i:s'),$new_file_name);
            $result = $adb->pquery($sql2, $params2);
            //防止上传后不保存不管保存与否都同步当前记录里
            if (!empty($record) && $record > 0) {
                $recordModel = Vtiger_Record_Model::getInstanceById($record, $model);
                foreach ($recordModel->getModule()->getFields() as $fieldName => $fieldModel) {
                    if ($fieldModel->getFieldDataType() == 'FileUpload') {
                        $str = $recordModel->entity->column_fields[$fieldName] . '*|*' . $filename . '##' . $current_id;
                        $str = ltrim($str, '*|*');
                        $sql = "UPDATE {$recordModel->entity->table_name} SET $fieldName=? WHERE {$recordModel->entity->table_index}=?";
                        $adb->pquery($sql, array($str, $record));
                    }
                }
            }

            echo json_encode(array('success' => true, 'result' => array('id' => $current_id, 'name' => $filename, 'path' => $file_path)));
        }
    }

	
}
