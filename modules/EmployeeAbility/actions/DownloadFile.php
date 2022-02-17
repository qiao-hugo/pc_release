<?php
/*+************
 * 独立文件上传
 *20141222
 **************/

class EmployeeAbility_DownloadFile_Action extends Vtiger_DownloadFile_Action {



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
            global $current_user,$adb;
            $result = $adb->pquery("SELECT * FROM vtiger_files WHERE attachmentsid=?", array($fileid));
            if($adb->num_rows($result)) {
                $fileDetails = $adb->query_result_rowdata($result);
                $filePath = $fileDetails['path'];
                $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
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
                $openfileArray=array('application/pdf','image/bmp','image/gif','image/jpeg','image/png','image/tiff','image/x-icon');
                if (fopen($filePath.$savedFile, "r")) {
                    $fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);
                    header("Content-type: ".$fileDetails['type']);
                    header("Pragma: public");
                    header("Cache-Control: private");
                    if(!in_array($fileDetails['type'],$openfileArray)){
                        header("Content-Disposition: attachment; filename=\"$fileName\"");
                    }
                    header("Content-Description: PHP Generated Data");
                }

                echo $fileContent;
            }else{
                echo 'no file exist';
            }
        }
        exit;

    }


	
}
