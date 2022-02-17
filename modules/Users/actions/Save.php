<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Users_Save_Action extends Vtiger_Save_Action {
	
	public function checkPermission(Vtiger_Request $request) {
	    return true;
		$moduleName = $request->getModule();
		$record = $request->get('record');
		$recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record) || $currentUserModel->get('id') != $recordModel->getId()) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	/**
	 * Function to get the record model based on the request parameters
	 * @param Vtiger_Request $request
	 * @return Vtiger_Record_Model or Module specific Record Model instance
	 */
	protected function getRecordModelFromRequest(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		if(!empty($recordId)) {
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('id', $recordId);
			$sharedType = $request->get('sharedtype');
			if(!empty($sharedType))
				$recordModel->set('calendarsharedtype', $request->get('sharedtype'));
			$recordModel->set('mode', 'edit');
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('mode', '');
		}

		foreach ($modelData as $fieldName => $value) {
			$requestFieldExists = $request->has($fieldName);
			if(!$requestFieldExists){
				continue;
			}
			$fieldValue = $request->get($fieldName, null);
			if ($fieldName === 'is_admin' && !$fieldValue) {
				$fieldValue = 'off';
			}
			if($fieldValue !== null) {
				if(!is_array($fieldValue)) {
					$fieldValue = trim($fieldValue);
				}
				$recordModel->set($fieldName, $fieldValue);
			}
		}
		
		
		$homePageComponents = $recordModel->getHomePageComponents();
		$selectedHomePageComponents = $request->get('homepage_components', array());
		foreach ($homePageComponents as $key => $value) {
			if(in_array($key, $selectedHomePageComponents)) {
				$request->setGlobal($key, $key);
			} else {
				$request->setGlobal($key, '');
			}
		}

		// Tag cloud save
		$tagCloud = $request->get('tagcloudview');
		if($tagCloud == "on") {
			$recordModel->set('tagcloud', 0);
		} else {
			$recordModel->set('tagcloud', 1);
		}
		
		
		return $recordModel;
	}
	//人员转移汇报对象 BY Joe@20150417
	public function process(Vtiger_Request $request) {
		//$result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
		//$_FILES = $result['imagename'];
		
		$mode=$request->get('mode');
		if($mode=='removeuser'){
			$toid= $request->get('toid');
			$removeuser= $request->get('removeuser');
			if(!in_array($toid,$removeuser)){
				$db = PearDatabase::getInstance();
				$db->pquery('update vtiger_users set reports_to_id=? where id in('.implode(',',$removeuser).')',array($toid));
				echo 1;
			}	
		}else{
		//用户是否已存在？
		$db = PearDatabase::getInstance();
		$fieldvalue=$request->get('user_name');
		$query = 'SELECT id FROM vtiger_users WHERE user_name = ?';
		$result = $db->pquery($query, array($fieldvalue));
		$row=$db->num_rows($result);
		if($row){
			while($r=$db->fetch_array($result)){
				if($r['id']!=$request->get('record')){
						header("Location:index.php?module=Users&parent=Settings&view=Detail&record=".$r['id']);
				}
			
			}
		
		}
		
		
		
		//设置工号不可用 By Joe@20150519
			$usercode=$request->get('usercode');
			//$maxcode=$request->get('maxcode');
			
			//if($maxcode!=$usercode){
			$db->pquery('update vtiger_userscode set status=1 where ucode=?',array($usercode));
				
			//}
			
		
		$recordModel = $this->saveRecord($request);
		$this->updateSchoolresume($request, $recordModel->getId(), $request->get('department'), $request->get('title'));
		$this->updateCompanyId($recordModel->getId(),$request->get('invoicecompany'));
			if ($request->get('relationOperation')) {
				$parentRecordModel = Vtiger_Record_Model::getInstanceById($request->get('sourceRecord'), $request->get('sourceModule'));
				$loadUrl = $parentRecordModel->getDetailViewUrl();
			} else if ($request->get('isPreference')) {
				$loadUrl =  $recordModel->getPreferenceDetailViewUrl();
			} else {
				$loadUrl = $recordModel->getDetailViewUrl();
			}
		}
		//修改用户信息后自动执行缓存更新操作 By Joe@20150415
		//$cache=new Settings_Cacheinfo_Index_View();
		//$cache->process($request);
       //Vtiger_Cache::clear();//2015-4-15 young 保存用户信息更新内存缓存，防止数据延迟
		if($mode!='removeuser'){
			header("Location: $loadUrl");
		}
		
	}

    /**
     * cxh 2019-09-03 更新用户所在公司id
     */
	public function updateCompanyId($id,$invoicecompany){
          $sql = " SELECT companyid FROM vtiger_invoicecompany WHERE invoicecompany=? LIMIT 1 ";
          $db = PearDatabase::getInstance();
          $result = $db->pquery($sql,array($invoicecompany));
          $row = $db->query_result_rowdata($result, 0);
          if($row){
              $companyid=$row['companyid'];
              $sql=" UPDATE vtiger_users SET companyid=? WHERE  id=? ";
              $db->pquery($sql,array($companyid,$id));
          }
    }


	/**
     *  如果该用户 是招聘过来的 更新招聘信息
     * @param $request
     */
	public function updateSchoolresume(Vtiger_Request $request, $userid, $department='', $title='') {
		$sid = $request->get('schoolresumeid');
		if (empty($sid)) {return false;}
		// 解密
		$schoolresumeid = base64_decode($sid) - 99999;

		if (! empty($schoolresumeid)) {
			$db = PearDatabase::getInstance();

			$query = "SELECT schoolresumeid,shool_resume_source FROM vtiger_schoolresume WHERE schoolresumeid=? LIMIT 1"; // 判断 简历id是否有效
			$sel_result = $db->pquery($query, array($schoolresumeid));
			$res_cnt = $db->num_rows($sel_result);
			if($res_cnt > 0 && $userid > 0) {
				$row = $db->query_result_rowdata($sel_result, 0);
				$query = 'update vtiger_users set schoolresumeid=?,shool_resume_source=? where id=?';
				$db->pquery($query, array($row['schoolresumeid'], $row['shool_resume_source'], $userid));
			
				// 入职时间 和 用户名称
				$user_entered = $request->get('user_entered');
				$sql = "update vtiger_schoolresume set userid=?, entrydate=?, is_formal_entry=1 where schoolresumeid=?";
				$db->pquery($sql, array($userid, $user_entered, $schoolresumeid));

				// 更新录用人员信息
				$sql = "update vtiger_schoolemploypeople set userid=?, department=?, title=? where schoolresumeid=?";
				$db->pquery($sql, array($userid, $department, $title, $schoolresumeid));
			}
		}
	}

    /**
     * 重写添加用微信企业号通讯录成员
     * @param $request
     * @return Vtiger_Record_Model
     */
    public function saveRecord($request) {

        $recordModel = $this->getRecordModelFromRequest($request);

        $recordid=$request->get('record');

        $oldemail=$recordModel->entity->column_fields['email1'];//取得未更改之前的ID
        $oldstatus=$recordModel->entity->column_fields['status'];//取得未更改之前的ID
        $oldisdimission=$recordModel->entity->column_fields['isdimission'];//取得未更改之前的离职状态
        $oldName=$recordModel->entity->column_fields['last_name'];//
        $userstatus=$request->get('status');//当前的状态 Inactive
        $useremail=$request->get('email1');//新的EMAIL
        $isdimission=$request->get('isdimission');//新的离职状态
        $departmentid=$request->get('departmentid');//部门ID
        $oldDepartmentid=$recordModel->get('departmentid');//原部门ID
        $userstatusflag=$userstatus;
        if($userstatus=='Inactive'){
            $request->set('isdimission','on');
        }
        if(($isdimission=='on' || $isdimission==1) && $oldisdimission==0){
            $userstatus='Inactive';
        }
        $sessionid=1;
        if($isdimission=='on' || $isdimission==1 || $userstatus=='Inactive'){
            $wexinData=$this->delUserWexinStatus($recordid);
            if($wexinData['flag']){
                $sessionid=$wexinData['sessionid'];
            }
        }

        $recordModel->save();
        $useremail=trim($useremail);
        $oldemail=trim($oldemail);
        $datas['sessionid']=$sessionid;
        $datas['username']=$request->get('last_name');
        $datas['email']=$useremail;
        $datas['oldemail']=empty($oldemail)?$useremail:$oldemail;
        $datas['ERPDOIT']=456321;
        $datas['departmentid']=trim($departmentid,'H');
        $datas['mobile']=$request->get('mobile');
        $datas['flag']=0;
        if(empty($recordid) && $userstatus=='Active'){
            $datas['flag']=1;
        }else{
            if($userstatus=='Active' && $isdimission!='on' && $isdimission!=1){
                if($useremail!=$oldemail || $oldisdimission==1){//更新用户删了重建
                    $datas['flag']=2;
                }elseif($oldstatus=='Inactive' ){//重新账号
                    $datas['flag']=1;
                }elseif($oldDepartmentid!=$departmentid || $oldName!=$request->get('last_name')){//修改用户信息
                    $datas['flag']=11;
                }
            }elseif($userstatus=='Inactive' && $oldstatus=='Active'){//禁用账号
                $datas['flag']=3;
            }elseif($userstatusflag=='Active' && ($isdimission=='on' || $isdimission==1) && $oldisdimission==0){//禁用账号
                $datas['flag']=3;
            }
        }
        if($datas['flag']>0){
            $this->setweixincontracts($datas);
        }

        /*if(empty($recordid) && $userstatus=='Active'){//新建用户
            $datas['username']=$request->get('last_name');
            $datas['email']=$useremail;
            $datas['oldemail']=$useremail;
            $datas['flag']=1;
            $datas['ERPDOIT']=456321;
            $datas['departmentid']=trim($departmentid,'H');
	    $datas['mobile']=$request->get('mobile');
            $this->setweixincontracts($datas);
        }elseif(!empty($recordid) && $userstatus=='Active' && $oldemail!=$useremail){//更新用户
            $datas['username']=$request->get('last_name');
            $datas['email']=$useremail;
            $datas['oldemail']=$oldemail;
            $datas['sessionid']=$sessionid;
            $datas['departmentid']=trim($departmentid,'H');
            $datas['ERPDOIT']=456321;
            $datas['flag']=2;
	    $datas['mobile']=$request->get('mobile');
            $this->setweixincontracts($datas);
        }elseif(!empty($recordid) && $userstatus=='Inactive' && $oldstatus=='Active'){//禁用账号
            $datas['username']=$request->get('last_name');
            $datas['email']=$oldemail;
            $datas['oldemail']=$oldemail;
            $datas['ERPDOIT']=456321;
            $datas['departmentid']=trim($departmentid,'H');
            $datas['flag']=3;
	        $datas['mobile']=$request->get('mobile');
            $datas['sessionid']=$sessionid;
            $this->setweixincontracts($datas);
        }elseif(!empty($recordid) && $userstatus=='Active' && $oldstatus=='Inactive'){//重启账号
            $datas['username']=$request->get('last_name');
            $datas['email']=$useremail;
            $datas['departmentid']=trim($departmentid,'H');
            $datas['ERPDOIT']=456321;
            $datas['oldemail']=$oldemail;
            $datas['flag']=1;
	    $datas['mobile']=$request->get('mobile');
            $this->setweixincontracts($datas);
        }*/
        if($userstatusflag=='Active' && ($isdimission=='on' || $isdimission==1) && $oldisdimission==0){
            $this->sendMailToReportto($recordid);
        }

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
     * 设置微信企业号上的成员信息
	 *测试上不添加
     * @param Vtiger_Request $request
     */
    private function setweixincontracts($data){
        $userkey='c0b3Ke0Q4c%2BmGXycVaQ%2BUEcbU0ldxTBeeMAgUILM0PK5Q59cEp%2B40n6qUSJiPQ';
        global $m_crm_domian_api_url;
        $url = $m_crm_domian_api_url;
        $url = "http://mtest.crm.71360.com/api.php";
//        $url = "http://m.crm.71360.com/api.php";
        $ch  = curl_init();
        $data['tokenauth']=$userkey;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);
    }
    /**
     * 删除微信session日志信息
     * @param $id
     */
    public function delUserWexinStatus($id){
        global $adb;
        $adb->pquery('DELETE FROM `vtiger_soapservice` WHERE id=?',array($id));
        $query='SELECT * FROM `vtiger_weixinclientsession` WHERE userid=?';
        $result=$adb->pquery($query,array($id));
        $returndata['flag']=false;
        if($adb->num_rows($result)){
            $data=$adb->raw_query_result_rowdata($result,0);
            $returndata['flag']=true;
            $returndata['sessionid']=$data['sessionid'];
            $adb->pquery('DELETE FROM `vtiger_weixinclientsession` WHERE userid=?',array($id));
        }
        return $returndata;
    }

    /**
     * 修改密码发邮件
     * @param $id
     * @throws Exception
     */
    public function sendMailToReportto($id)
    {
        return false;//禁用该功能
        global $adb;
        $query = 'SELECT a.last_name AS alastname,a.user_name,a.crypt_type,b.last_name AS blastname,b.email1 AS bemail FROM vtiger_users a LEFT JOIN vtiger_users b ON a.reports_to_id=b.id WHERE a.id=?';
        $result = $adb->pquery($query, array($id));
        if ($adb->num_rows($result)) {
            $resultData = $adb->raw_query_result_rowdata($result, 0);
            $length = rand(6, 14);
            $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
                'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
                't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
                'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
                'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', /*'!',
                '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_',
                '[', ']', '{', '}', '<', '>', '~', '`', '+', '=', ',',
                '.', ';', ':', '/', '?', '|'*/);
            $password = '';
            for ($i = 0; $i < $length; $i++) {
                shuffle($chars);
                shuffle($chars);
                shuffle($chars);
                $password .= current($chars);
            }
            $newpasswd = $this->encrypt_password($resultData['user_name'], $password, $resultData['crypt_type']);
            $updateSql = "update vtiger_users set user_password=? where id=?";
            $adb->pquery($updateSql, array($newpasswd, $id));
            $Subject = '离职人员用户密码修改';
            $body = "员工：{$resultData['alastname']}<br>用户名：{$resultData['user_name']}<br>密码：{$password}<br>";
            $address = array(
                array('mail' => $resultData['bemail'], 'name' => $resultData['blastname'])
            );
            Vtiger_Record_Model::sendMail($Subject, $body, $address);

        }
    }

    /**
     * 密码加密码
     * @param $user_name
     * @param $user_password
     * @param string $crypt_type
     * @return string
     */
    public function encrypt_password($user_name,$user_password, $crypt_type='') {
        // encrypt the password.
        $salt = mb_substr($user_name, 0, 2,'utf-8');
        // For more details on salt format look at: http://in.php.net/crypt
        if($crypt_type == 'MD5') {
            $salt = '$1$' . $salt . '$';
        } elseif($crypt_type == 'BLOWFISH') {
            $salt = '$2$' . $salt . '$';
        } elseif($crypt_type == 'PHP5.3MD5') {
            //only change salt for php 5.3 or higher version for backward
            //compactibility.
            //crypt API is lot stricter in taking the value for salt.
            $salt = '$1$' . str_pad($salt, 9, '0');
        }

        $encrypted_password = crypt($user_password, $salt);
        return $encrypted_password;
    }
}
