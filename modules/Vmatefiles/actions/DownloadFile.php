<?php
/*+************
 * 独立文件上传
 *20141222
 **************/

class Vmatefiles_DownloadFile_Action extends Vtiger_Save_Action {



	public function checkPermission(Vtiger_Request $request) {
	return true;
		$moduleName = $request->getModule();
		$record = $request->get('record');

		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

    public function process(Vtiger_Request $request)
    {
        $batch = $request->get('batch');
        if ($batch == 1) {
            $records = $request->get('records');
            if (!empty($records)) {
                global $adb;
                $result = $adb->pquery("SELECT * FROM vtiger_vmatefiles WHERE vmateattachmentsid IN (" . $records . ")", []);
                $rowNums = $adb->num_rows($result);
                if ($rowNums == 0) {
                    exit('no file exist1');
                }
                $fileList = [];
                for ($i = 0; $i < $rowNums; $i++) {
                    $fileDetails = $adb->fetch_array($result);
                    $filePath = $fileDetails['path'];
                    $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
                    if ($fileDetails['newfilename'] > 0) {
                        $savedFile = $fileDetails['vmateattachmentsid'] . "_" . $fileDetails['newfilename'];
                    } else {
                        $t_fileName = base64_encode($fileName);
                        $t_fileName = str_replace('/', '', $t_fileName);
                        $savedFile = $fileDetails['vmateattachmentsid'] . "_" . $t_fileName;
                    }
                    if (!file_exists($filePath . $savedFile)) {
                        $savedFile = $fileDetails['vmateattachmentsid'] . "_" . $fileName;
                    }
                    if (file_exists($filePath . $savedFile)) {
                        $fileList[] = ['fullpath' => $filePath . $savedFile, 'filename' => $fileName];
                    }
                }
                if (empty($fileList)) {
                    exit('no file exist2');
                }
                $zipPath = 'storage/download';
                if (!is_dir($zipPath)) {
                    mkdir($zipPath, 0755);
                }
                $zipName = $zipPath . '/' . microtime(true) . '.zip';
                $zip = new ZipArchive();
                if ($zip->open($zipName, ZIPARCHIVE::CREATE) !== TRUE) {
                    exit('create zip fault');
                }
                foreach ($fileList as $k => $file) {
                    $filename = ($k + 1) . '-' . iconv("UTF-8", "GBK", $file['filename']);
                    $zip->addFile($file['fullpath'], $filename);
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
            }
            exit('no file exist3');
        } else {
            $record = $request->get('record');
            if ($record > 0) {
                global $adb;
                $result = $adb->pquery("SELECT * FROM vtiger_vmatefiles WHERE vmateattachmentsid=?", array($record));
                if($adb->num_rows($result)) {
                    $fileDetails = $adb->query_result_rowdata($result);
                    $filePath = $fileDetails['path'];
                    $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
                    if($fileDetails['newfilename']>0){
                        $savedFile = $fileDetails['vmateattachmentsid'] . "_" . $fileDetails['newfilename'];
                    }else{
                        $t_fileName = base64_encode($fileName);
                        $t_fileName = str_replace('/', '', $t_fileName);
                        $savedFile = $fileDetails['vmateattachmentsid'] . "_" . $t_fileName;
                    }
                    if(!file_exists($filePath.$savedFile)){
                        $savedFile = $fileDetails['vmateattachmentsid']."_".$fileName;
                    }
                    $fileSize = filesize($filePath.$savedFile);
                    $fileSize = $fileSize + ($fileSize % 1024);
                    $openfileArray=array('application/pdf','image/bmp','image/gif','image/jpeg','image/png','image/tiff','image/x-icon');
                    $type=$request->get('type');
                    if (fopen($filePath.$savedFile, "r")) {
                        $fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);
                        header("Content-type: ".$fileDetails['type']);
                        header("Pragma: public");
                        header("Cache-Control: private");
                        if(!in_array($fileDetails['type'],$openfileArray)||$type=1){
                            header("Content-Disposition: attachment; filename=\"$fileName\"");
                        }
                        header("Content-Description: PHP Generated Data");
                    }
                    echo $fileContent;
                } else {
                    echo 'no file exist4';
                }
            }
            exit('no file exist5');
        }
    }
}
