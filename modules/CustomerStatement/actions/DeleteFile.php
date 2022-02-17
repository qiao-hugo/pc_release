<?php
/*+************
 * 独立文件上传
 *20141222
 **************/

class CustomerStatement_DeleteFile_Action extends Vtiger_Save_Action {



	public function checkPermission(Vtiger_Request $request) {
	return true;
		$moduleName = $request->getModule();
		$record = $request->get('record');

		if(!Users_Privileges_Model::isPermitted($moduleName, 'Edit', $record)){
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	public function process(Vtiger_Request $request) {
		//$fileid=(int)base64_decode($request->get('filename'));
		$fileid=(int)$request->get('id');
        $recordid=$request->get('record');
        $moduleName=$request->get('module');
        //echo $fileid;
		if($fileid>0){
			global $adb,$current_user;
			$result = $adb->pquery("SELECT * FROM vtiger_files WHERE delflag=0 AND attachmentsid=?", array($fileid));
			if($adb->num_rows($result)) {
				$fileDetails = $adb->query_result_rowdata($result);
                $where=getAccessibleUsers($request->getModule(),'List',true);
                if(!empty($where)&&$where!='1=1'){

                    if(!in_array($fileDetails['uploader'],$where)){
                        echo json_encode(array('success'=>false,'result'=>array('msg'=>'没有权限')));
                        exit;
                    }
                }
				$filePath = $fileDetails['path'];
				$fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
				$savedFile = $fileDetails['attachmentsid']."_".base64_encode($fileName);
				if(!file_exists($filePath.$savedFile)){
					$savedFile = $fileDetails['attachmentsid']."_".$fileDetails['newfilename'];;
				}
                if(unlink($filePath.$savedFile)){
                    $adb->pquery("UPDATE vtiger_files SET deleter=?,deletertime=?,delflag=? WHERE attachmentsid=?", array($current_user->id,date("Y-m-d H:i:s"),'1',$fileid));
                    if(!empty($recordid)&& $recordid>0){
                        $recordModel=Vtiger_Record_Model::getInstanceById($recordid, $moduleName);
                        foreach($recordModel->getModule()->getFields() as $fieldName=>$fieldModel){
                            if($fieldModel->getFieldDataType() == 'FileUpload'){
                                if(empty($recordModel->entity->column_fields[$fieldName]))break;
                                $newfileone=explode('*|*',$recordModel->entity->column_fields[$fieldName]);
                                foreach($newfileone as $key=>$val){
                                    $newfiletwo=explode('##',$val);
                                    if(in_array($fileid,$newfiletwo)){
                                        unset($newfileone[$key]);
                                    }
                                }
                                $str='';
                                if(!empty($newfileone)){
                                    $str=implode('*|*',$newfileone);
                                }
                                $sql="UPDATE {$recordModel->entity->table_name} SET $fieldName=? WHERE {$recordModel->entity->table_index}=?";
                                $adb->pquery($sql,array($str,$recordid));
                            }
                        }
                    }
                    echo json_encode(array('success'=>true,'result'=>array('msg'=>'文件删除成功')));
                    exit;
                }
                echo json_encode(array('success'=>false,'result'=>array('msg'=>'删除失败')));
                exit;
			}else{
                echo json_encode(array('success'=>false,'result'=>array('msg1'=>'文件不存在')));
                exit;
			}
		}
			exit;

	}


}
