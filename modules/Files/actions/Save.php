<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Files_Save_Action extends Vtiger_Action_Controller {

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}


	public function upload(Vtiger_Request $request) {
		$files=$_FILES['File'];
//        dd($files);
		$model=$request->get('sourceModule');
		$record=$request->get('sourceRecord');
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
			$filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters

            //大小限制
            if($files['size']>10*1024*1024){
                echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>上传文件大小超过10M！</h2><p class="text"></p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                exit;
            }

			$filetype = $files['type'];
			$filesize = $files['size'];
			$filetmp_name = $files['tmp_name'];
			$upload_file_path = decideFilePath();

			//$aaa = base64_encode($binFile);
			//$aaa = str_replace('/', '', $aaa);
            $newfilename=time();
			//$ttt = $upload_file_path . $current_id . "_" . $aaa;
			$ttt = $upload_file_path . $current_id . "_" . $newfilename;
			$upload_status = move_uploaded_file($filetmp_name, $ttt);
            if(!$upload_status){
                echo json_encode(array('success'=>false,'result'=>array('id'=>$current_id,'name'=>$filename)));
                exit;
            }

            return array('name'=>$filename,'description'=>$model, 'type'=>$filetype, 'path'=>$upload_file_path, 'uploader'=>$current_user->id, 'uploadtime'=>date('Y-m-d H:i:s'), 'relationid'=>$record, 'attachmentsid'=>$current_id,'newfilename'=>$newfilename);
			//$save_file = 'true';
			//$sql2 = "insert into vtiger_files(attachmentsid, name,description, type, path,uploader,uploadtime) values(?, ?,?, ?, ?,?,?)";
			//$params2 = array($current_id, $filename, $model,$filetype, $upload_file_path,$current_user->id,date('Y-m-d H:i:s'));
			//$result = $adb->pquery($sql2, $params2);
            //防止上传后不保存不管保存与否都同步当前记录里
            /*if(!empty($record)&& $record>0){
                $recordModel=Vtiger_Record_Model::getInstanceById($record, $model);
                foreach($recordModel->getModule()->getFields() as $fieldName=>$fieldModel){
                    if($fieldModel->getFieldDataType() == 'FileUpload'){
                        $str=$recordModel->entity->column_fields[$fieldName].'*|*'.$filename.'##'.$current_id;
                        $str=ltrim($str,'*|*');
                        $sql="UPDATE {$recordModel->entity->table_name} SET $fieldName=? WHERE {$recordModel->entity->table_index}=?";
                        $adb->pquery($sql,array($str,$record));
                    }
                }
            }*/
        } else {
        	echo '附件不能为空';
        	exit;
        }
	}

	public function process(Vtiger_Request $request) {
		
		// 上传附件
		$fileData = $this->upload($request);
		$fileData['filestate'] = $request->get('filestate');
		$fileData['style'] = $request->get('style');
		$fileData['remarks'] = $request->get('remarks');
		//$recordModel = $this->saveRecord($request);
		

		$divideNames = array_keys($fileData);
		$divideValues = array_values($fileData);
		// 快速添加数据
		global $adb;
		$adb->pquery('INSERT INTO `vtiger_files` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
		// 更改服务合同的 已添加附件类型  attachmenttype 
		$style = $request->get('style');
		$record=$request->get('sourceRecord');
		if(! empty($style)) {
			$tflag = str_replace('files_style', '', $style);
			$ttt   = array('1'=>'A', '2'=>'B','3'=>'C','4'=>'D','5'=>'E');
			$sql   = "update vtiger_servicecontracts set attachmenttype= case when ISNULL(attachmenttype) then ? else CONCAT(attachmenttype,'/', ?) end where servicecontractsid=?";
			$adb->pquery($sql, array($ttt[$tflag], $ttt[$tflag], $record));
		}

		if($request->get('relationOperation')) {
            $str='';
		    if(!empty($_SERVER['HTTP_REFERER'])){
                $HTTP_REFERER=explode('&',$_SERVER['HTTP_REFERER']);
                foreach($HTTP_REFERER as $value){
                    if(strpos($value,'realoperate=')!==false){
                        $str='&'.$value;
                        break;
                    }
                }
            }
			$loadUrl = $this->getParentRelationsListViewUrl($request).$str;
		} else if ($request->get('returnToList')) {
			$loadUrl = $recordModel->getModule()->getListViewUrl();
		} else {
			$loadUrl = $recordModel->getDetailViewUrl();
		}
		if(empty($loadUrl)){
			if($request->getHistoryUrl()){
				$loadUrl=$request->getHistoryUrl();
			}else{
				$loadUrl="index.php";
			}
		}
        if($request->isAjax()){

        }else{
            header("Location: $loadUrl");
        }
	}

	/**
	 * Function to save record
	 * @param <Vtiger_Request> $request - values of the record
	 * @return <RecordModel> - record Model of saved record
	 */
	public function saveRecord($request) {
		$recordModel = $this->getRecordModelFromRequest($request);

		$recordModel->save();
		
		if($request->get('relationOperation')) {
			$parentModuleName = $request->get('sourceModule');
			$parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
			$parentRecordId = $request->get('sourceRecord');
			$relatedModule = $recordModel->getModule();
			$relatedRecordId = $recordModel->getId();

			$relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
			$relationModel->addRelation($parentRecordId, $relatedRecordId);
		}
		return $recordModel;
	}

	/**
	 * Function to get the record model based on the request parameters
	 * @param Vtiger_Request $request
	 * @return Vtiger_Record_Model or Module specific Record Model instance
	 */
	protected function getRecordModelFromRequest(Vtiger_Request $request) {
		
		
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		
		if(!empty($recordId)) {
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('modcommentsid', $recordId);

			$recordModel->set('mode', 'edit');
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('mode', '');
		}
		
		
		$fieldModelList = $moduleModel->getFields();
	
		foreach ($fieldModelList as $fieldName => $fieldModel) {
			$fieldValue = $request->get($fieldName, null);
			$fieldDataType = $fieldModel->getFieldDataType();
			if($fieldDataType == 'time'){
				$fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
			}
			if($fieldValue !== null) {
				if(!is_array($fieldValue)) {
					$fieldValue = trim($fieldValue);
				}
				$recordModel->set($fieldName, $fieldValue);
				
			}
			
		}
		
		
		return $recordModel;
	}
	
	//gaocl 2015-01-05 add start
	/**
	 * 关联模块编辑提交后返回一览页面URL取得
	 * @param Vtiger_Request $request
	 * @return 返回一览页面URL
	 */
	public function getParentRelationsListViewUrl(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$parentModuleName = $request->get('sourceModule');
		$parentRecordId = $request->get('sourceRecord');
		return 'index.php?module='.$parentModuleName.'&relatedModule='.$moduleName.'&view=Detail&record='.$parentRecordId.'&mode=showRelatedList&tab_label=Files';
	}
	//gaocl 2015-01-05 add end
}
