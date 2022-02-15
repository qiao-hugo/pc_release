<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolrecruit_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getCreateuser');
		$this->exposeMethod('isCheckTow');
		$this->exposeMethod('addSchoolqualified');
		$this->exposeMethod('showQRcode');

	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function addSchoolqualified(Vtiger_Request $request) {
		$record = $request->get('record');
		$reportsdate = $request->get('reportsdate');
		$reportsower = $request->get('reportsower');
		$reportaddress = $request->get('reportaddress');

		global $current_user;
		$db=PearDatabase::getInstance();
		$sql = "SELECT vtiger_schoolrecruit.schoolrecruitid, vtiger_schoolrecruit.accompany, vtiger_schoolrecruit.remarks FROM vtiger_schoolrecruit WHERE schoolrecruitid=? LIMIT 1";
		$sel_result = $db->pquery($sql, array($record));
		$res_cnt    = $db->num_rows($sel_result);
		$schoolrecruit = array();
		if ($res_cnt > 0) {
			$schoolrecruit = $db->query_result_rowdata($sel_result, 0);
		}

		$schoolqualifiedid = '';  //  合格简历名单id
		$successSchoolResumeId = array(); // 添加成功的简历id
		if (!empty($schoolrecruit)) {
			$sql = "select schoolqualifiedid, schoolrecruitid from vtiger_schoolqualified where schoolrecruitid=? LIMIT 1";
			$sel_result = $db->pquery($sql, array($record));
			$res_cnt    = $db->num_rows($sel_result);
			if ($res_cnt > 0) {
				$tt = $db->query_result_rowdata($sel_result, 0);
				$schoolqualifiedid = $tt['schoolqualifiedid'];
			} else {
				$request = new Vtiger_Request($_REQUES, $_REQUES);
	            $request->set('module', 'Schoolqualified');
	            $request->set('action', 'SaveAjax');
	            $request->set('schoolrecruitid', $schoolrecruit['schoolrecruitid']);
	            $request->set('schoolrecruitsower', $current_user->id);
	            $request->set('reportsower', $reportsower);
	            $request->set('reportsdate', $reportsdate);
	            $request->set('accompany', $schoolrecruit['accompany']);
	            $request->set('reportaddress', $reportaddress);
	            $request->set('remarks', $schoolrecruit['remarks']);
	            $ressorder = new Vtiger_SaveAjax_Action();
	            $ressorderecord = $ressorder->saveRecord($request);
	            $schoolqualifiedid = $ressorderecord->getId();
			}

			

            //$sualifiedSchoolresume = $this->getSualifiedSchoolresume($record);
            //print_r($sualifiedSchoolresume);die;

            if (! empty($schoolqualifiedid)) {
            	

            	$sualifiedSchoolresume = $this->getSualifiedSchoolresume($record);

            	//print_r($sualifiedSchoolresume);die;
            	
            	$ressorder = new Vtiger_SaveAjax_Action();
            	foreach ($sualifiedSchoolresume as $key => $value) {
            		$request = new Vtiger_Request($_REQUES, $_REQUES);
		            $request->set('module', 'Schoolqualifiedpeople');
		            $request->set('action', 'SaveAjax');
		            $request->set('qualifiedpeoplename', $value['name']);
		            $request->set('gendertype', $value['gendertype']);
		            $request->set('telephone', $value['telephone']);
		            $request->set('email', $value['email']);
		            $request->set('schoolresumeid', $value['schoolresumeid']);
		            $request->set('schoolqualifiedid', $schoolqualifiedid);
		            $request->set('schoolrecruitid', $schoolrecruit['schoolrecruitid']);

		            $request->set('p_reportaddress', $reportaddress);
		            $request->set('p_reportsdate', $reportsdate);
		            $request->set('p_reportsower', $reportsower);

		            $tt = $ressorder->saveRecord($request);
		            
		            $t_id = $tt->getId();
		            if (!empty($t_id)) {
		            	$successSchoolResumeId[] = $value['schoolresumeid'];
		            }
            	}
            	
            	$this->setSualifiedSchoolresumeQqualified($successSchoolResumeId);
            	// 添加合格校招合格简历
            	/**/
            }

		}

		$response = new Vtiger_Response();
		$response->setResult(array('num'=>count($successSchoolResumeId)));
		$response->emit();
	}

	public function setSualifiedSchoolresumeQqualified($successSchoolResumeId) {
		if (!empty($successSchoolResumeId)) {
			$sql = "update vtiger_schoolresume set is_qualified=1 where schoolresumeid IN (". implode(',', $successSchoolResumeId) .")";
			$db=PearDatabase::getInstance();
			$db->pquery($sql, array());
		}
	}

	public function getSualifiedSchoolresume($schoolrecruitid) {
		$db=PearDatabase::getInstance();
		$sql = "SELECT * FROM vtiger_schoolresume LEFT JOIN vtiger_crmentity ON vtiger_schoolresume.schoolresumeid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND is_resume_qualified=1 AND vtiger_schoolresume.schoolrecruitid=? AND vtiger_schoolresume.is_qualified=0 AND vtiger_schoolresume.shool_resume_source='school_recruit'";

		$sel_result = $db->pquery($sql, array($schoolrecruitid));
		$res_cnt    = $db->num_rows($sel_result);
		$schoolresume = array();
		if ($res_cnt > 0) {
			while($rawData=$db->fetch_array($sel_result)) {
	            $schoolresume[] = $rawData;
	        }
		}
		return $schoolresume;
	}
	

    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function isCheckTow(Vtiger_Request $request) {
		$recordId = $request->get('record'); //用户id
		$recruitname = $request->get('recruitname');

		$db=PearDatabase::getInstance();

		$datas = array('is_check'=>0);

		$sql        = "select schoolrecruitid,recruitname from vtiger_schoolrecruit where recruitname=?";
		$sel_result = $db->pquery($sql, array($recruitname));
		$res_cnt    = $db->num_rows($sel_result);

		if (empty($recordId)) {
			if ($res_cnt > 0) {
				$datas['is_check'] = 1;   //不可以修改
				$datas['message'] = "招聘计划名称重复，请重新输入。";
			}
		} else {
			$row = $db->query_result_rowdata($sel_result, 0);
			if ($res_cnt > 0 && $row['schoolrecruitid'] != $recordId) {
				$datas['is_check'] = 1;   //不可以修改
				$datas['message'] = "招聘计划名称重复，请重新输入。";
			}
		}
		$response = new Vtiger_Response();
		$response->setResult($datas);
		$response->emit();
	}


	public function getCreateuser(Vtiger_Request $request) {
		$schoolid = $request->get('schoolid'); //用户id

		$db=PearDatabase::getInstance();


		$sql = "SELECT vtiger_schoolcontacts.schoolcontactsid, vtiger_schoolcontacts.schoolcontactsname FROM vtiger_schoolcontacts WHERE vtiger_schoolcontacts.schoolid=?";
		$sel_result = $db->pquery($sql, array($schoolid));
		$res_cnt    = $db->num_rows($sel_result);

		$data = array();
		if ($res_cnt > 0) {
			while($row=$db->fetch_array($sel_result)) {
            	$data[] = $row;
			}
		}
		
		$response = new Vtiger_Response();
		$response->setResult($data);
		$response->emit();
	}
    public function showQRcode(Vtiger_Request $request){
	    $recordid=$request->get('record');
	    $recordModule=Vtiger_Record_Model::getInstanceById($recordid,'Schoolrecruit');
        $entity=$recordModule->getEntity();
        $column_fields=$entity->column_fields;
        $schoolRecordModule=Vtiger_Record_Model::getInstanceById($column_fields['schoolid'],'School');
        $schoolEntity=$schoolRecordModule->getEntity();
        $schoolColumn_fields=$schoolEntity->column_fields;

        $schoolid=$this->base64encode($column_fields['schoolid']);
        $schoolrecruitid=$this->base64encode($recordid);
        $schoolname=$schoolColumn_fields['schoolname'];
        $schoolname=urlencode($schoolname);
        include './libraries/qrcode/phpqrcode.php';
        global $school_recruit_basic_ajax_qrcode_url;
        $value = $school_recruit_basic_ajax_qrcode_url.'?schoolid='.$schoolid.'&from=qrcode&schoolrecruitid='.$schoolrecruitid.'&schoolname='.$schoolname;//二维码内容
//        $value = 'http://m.crm.71360.com/studentinput.php?schoolid='.$schoolid.'&from=qrcode&schoolrecruitid='.$schoolrecruitid.'&schoolname='.$schoolname;//二维码内容
        //$value = 'http://192.168.40.188/apps/studentinput.php?schoolid='.$schoolid.'&from=qrcode&schoolrecruitid='.$schoolrecruitid.'&schoolname='.$schoolname;//二维码内容
        //echo $value;
        //exit;
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 6;//生成图片大小
        //生成二维码图片
        QRcode::png($value, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2);
        $logo = '0.jpg';//准备好的logo图片
        $QR = 'qrcode.png';//已经生成的原始二维码图

        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                $logo_qr_height, $logo_width, $logo_height);
        }
        //输出图片
        Header("Content-type: image/png");
        ImagePng($QR);
    }

    /**
     * 数字加密
     * @param $v
     * @return string
     */
    protected function base64encode($v){
        //加入乱字符开始
        $b=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('b','c','a','f','m','n','t','o','x','q'),$v);
        $c=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('ba','cd','af','df','dm','cdn','fdt','sso','ewx','ayq'),$v);
        $a=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('sed','dwe','sss','ddss','derwm','werw','ghjy','ttrosso','ffs','mnbv'),$v);
        $d=md5('AccountsiD');
        $e=md5('Useridstrunlandorgnetcomcn');
        //结束
        return base64_encode($a.$d.$b.$e.$c);
    }

    /**
     * 数字解密
     * @param $v
     * @return int|mixed
     */
    protected function base64decode($v){
        $string=base64_decode($v);
        $dd=md5('Useridstrunlandorgnetcomcn');
        $e=explode($dd,$string);
        $ee=md5('AccountsiD');
        $e=explode($ee,$e[0]);
        $f=str_replace(array('b','c','a','f','m','n','t','o','x','q'),array(0,1,2,4,5,6,7,8,9,3),$e[1]);
        $f=(int)$f;
        return $f;
    }
	
}
