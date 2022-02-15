<?php
/*+************
 * 独立文件上传
 *20141222
 **************/

class Staypayment_FileUpload_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
        $colum='file';
        if(isset($_FILES['Explainfile'])){
            $files=$_FILES['Explainfile'];
            $colum='explainfile';
        }else{
            $files=$_FILES['File'];
        }

		$model=$request->get('module');
		$record=$request->get('record');
		if($files['name'] != '' && $files['size'] > 0){
			global $current_user;
			global $upload_badext;
			global $adb;
			$current_id = $adb->getUniqueID("vtiger_files");
			//$date_var = date("Y-m-d H:i:s");
			$ownerid = $current_user->id;
			$file_name = $files['name'];
            $file_name=preg_replace('/(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+/u','',$file_name);
			$binFile = sanitizeUploadFileName($file_name, $upload_badext);
			//$uploadfile=str_replace('/','',base64_encode($binFile));//去掉因base64l加密后成的/在误解析成路径引起的问题
            $uploadfile=time();
            $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
            //格式限制
            $filetype = $files['type'];
            $allowFileType = ['image/jpeg','image/png','image/gif','image/bmp','application/msword','application/pdf','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            if(!in_array($filetype,$allowFileType)){
                echo json_encode(array('success'=>false,'msg'=>'文件类型不对，无法上传，只支持Excel、word 、pdf、图片！'));
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
			$upload_status = move_uploaded_file($filetmp_name, $upload_file_path . $current_id . "_" .$uploadfile);
            if(!$upload_status){
                echo json_encode(array('success'=>false,'result'=>array('id'=>$current_id,'name'=>$filename)));
                exit;
            }
			$save_file = 'true';
			$sql2 = "insert into vtiger_files(attachmentsid, name,description, type, path,uploader,uploadtime,newfilename) values(?, ?,?, ?, ?,?,?,?)";
			$params2 = array($current_id, $filename, $model,$filetype, $upload_file_path,$current_user->id,date('Y-m-d H:i:s'),$uploadfile);
			$result = $adb->pquery($sql2, $params2);
            //防止上传后不保存不管保存与否都同步当前记录里
            if(!empty($record)&& $record>0){
                $recordModel=Vtiger_Record_Model::getInstanceById($record, $model);
                $str=$recordModel->entity->column_fields[$colum].'*|*'.$filename.'##'.$current_id;
                $str=ltrim($str,'*|*');
                $sql="UPDATE {$recordModel->entity->table_name} SET {$colum}=? WHERE {$recordModel->entity->table_index}=?";
                $adb->pquery($sql,array($str,$record));
            }
			echo json_encode(array('success'=>true,'result'=>array('id'=>$current_id,'name'=>$filename)));
		}else{
            echo json_encode(array('success'=>false));
		}
		exit;
	}

	
}
