<?php
/*+************
 * 文件删除
 *20210929
 **************/

class OrderChargeback_DeleteFile_Action extends Vtiger_Save_Action {


	public function checkPermission(Vtiger_Request $request) {
	   return true;
	}

	public function process(Vtiger_Request $request) {
		
		$fileid=(int)$request->get('id');
        $recordid=$request->get('record');
        $moduleName=$request->get('module');
        //参数判断
        if($fileid <= 0){
            echo json_encode(array('success'=>false,'result'=>array('msg'=>'参数有误')));
            exit;
        }
        
		global $adb,$current_user;
        //判断数据表中是否存在此文件
		$result = $adb->pquery("SELECT * FROM vtiger_files WHERE delflag=0 AND attachmentsid=?", array($fileid));
		if(!$adb->num_rows($result)) {
            echo json_encode(array('success'=>false,'result'=>array('msg1'=>'文件不存在')));
            exit;
        }
		$fileDetails = $adb->query_result_rowdata($result);
        //判断是否有权限
        $where=getAccessibleUsers($request->getModule(),'List',true);
        if(!empty($where) && $where!='1=1' && !in_array($fileDetails['uploader'],$where)){
            echo json_encode(array('success'=>false,'result'=>array('msg'=>'没有权限')));
            exit;
        }
        //获取文件路径
		$filePath = $fileDetails['path'];
		$fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
		$savedFile = $fileDetails['attachmentsid']."_".base64_encode($fileName);
		if(!file_exists($filePath.$savedFile)){
			$savedFile = $fileDetails['attachmentsid']."_".$fileDetails['newfilename'];
		}
        //删除文件
        if(unlink($filePath.$savedFile)){
            $adb->pquery("UPDATE vtiger_files SET deleter=?,deletertime=?,delflag=? WHERE attachmentsid=?", array($current_user->id,date("Y-m-d H:i:s"),'1',$fileid));
            //修改附件所属主体的保存文件字段信息
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
		
	}
	
}
