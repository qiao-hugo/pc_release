<?php
/*+************
 * 独立文件上传
 *20141222
 **************/

class VisitingOrder_DownloadFile_Action extends Vtiger_DownloadFile_Action {
	public function checkPermission(Vtiger_Request $request) {
	return true;
		$moduleName = $request->getModule();
		$record = $request->get('record');

		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	public function process(Vtiger_Request $request) {
		$fileid=(int)base64_decode($request->get('filename'));
		if($fileid>0){
			global $adb;
			$result = $adb->pquery("SELECT * FROM vtiger_files WHERE attachmentsid=?", array($fileid));
			if($adb->num_rows($result)) {
				$fileDetails = $adb->query_result_rowdata($result);
				$filePath = $fileDetails['path'];
				$fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
				$savedFile = $fileDetails['attachmentsid']."_".base64_encode($fileName);
				if(!file_exists($filePath.$savedFile)){
					$savedFile = $fileDetails['attachmentsid']."_".$fileName;
				}
                echo "<img src=$filePath$savedFile>";die;
			}else{
				echo 'no file exist';
			}
		}
			exit;
			
	}

    //移动端解析上传图片;
    public function app_parse($returnpicture){
        global $adb;
        $fileid = explode('##',$returnpicture)[1];
        $fieldname = explode('##',$returnpicture)[0];
        if($fileid>0){
            $result = $adb->pquery("SELECT * FROM vtiger_files WHERE attachmentsid=?", array($fileid));
            if($adb->num_rows($result)) {
                $fileDetails = $adb->query_result_rowdata($result);
                $filePath = $fileDetails['path'];
                $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
                $savedFile = $fileDetails['attachmentsid']."_".base64_encode($fileName);
                if(file_exists($filePath.$savedFile)){
                    $image_file = $filePath.$savedFile;
                    $image_info = getimagesize($image_file);
                    $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($image_file)));
                    //$savedFile = $fileDetails['attachmentsid']."_".$fileName;
                    return array(fieldname=>$fieldname,base64_image_content=>$base64_image_content);
                }
              return array();
            }
        }
    }

	
}
