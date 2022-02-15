<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolresume_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('addInterviewdate');
		$this->exposeMethod('addPersonnel');
		$this->exposeMethod('doBatchEnrollment');
        $this->exposeMethod('getuserlist');
        $this->exposeMethod('sendMail');
        $this->exposeMethod('getRecordData');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
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

	// 添加到人才库
	public function addPersonnel(Vtiger_Request $request) {
		$record = $request->get('record');
		$sql = "UPDATE vtiger_schoolresume set is_personnel=1 where schoolresumeid=?";
		global $adb;
		$adb->pquery($sql, array($record));
		$response = new Vtiger_Response();
		$response->setResult(array());
		$response->emit();
	}

	// 添加到面试记录
	public function addInterviewdate(Vtiger_Request $request) {
		$record = $request->get('record');
		$interviewdate = $request->get('interviewdate');
		$interviewer = $request->get('interviewer');

		do {
			if ( empty($interviewdate) || empty($interviewer) || empty($record) ) {
				break;
			}

			global $adb;
			// 判断是否已经添加了面试
			$sql = "select schoolinterviewid from vtiger_schoolinterview where schoolresumeid=? LIMIT 1";
			$sel_result = $adb->pquery($sql, array($record));
			$res_cnt = $adb->num_rows($sel_result);
			if ($res_cnt > 0) {
				break;
			}
            $_REQUES='';
			$request = new Vtiger_Request($_REQUES, $_REQUES);
	        $request->set('module', 'Schoolinterview');
	        $request->set('action', 'SaveAjax');
	        $request->set('planinterviewer', $interviewer);
	        $request->set('planinterviewdate', $interviewdate);
	        $request->set('schoolresumeid', $record);
	        $ressorder = new Vtiger_SaveAjax_Action();
	        $ressorderecord = $ressorder->saveRecord($request);
		} while (0);

		$response = new Vtiger_Response();
		$response->setResult(array());
		$response->emit();
	}

    /**
     * 添加录取 信息
     * @param Vtiger_Request $request
     */
	public function doBatchEnrollment(Vtiger_Request $request){
	    //error_reporting(2047);
        set_time_limit(0);
        $records=$request->get('records');
        $records=trim($records,',');
        $recordsarr=explode(',',$records);
        $p_reportaddress=$request->get('reportaddress');
        $p_reportsdate=$request->get('reportsdate');
        $p_reportsower=$request->get('reportsower');
        $db=PearDatabase::getInstance();
        $entityposition=$request->get('entityposition');
        $flag=$request->get('flag');
        if(2==$flag) {
        global $root_directory;
        $root_directoryt=rtrim($root_directory,"/");
        $root_directoryt=rtrim($root_directoryt,"\\");
        require $root_directoryt.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPWord'.DIRECTORY_SEPARATOR.'PHPWord.php';
        $PHPWord = new PHPWord();
        $tplpath=$root_directoryt.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR;
        $tpl=$tplpath.'schooltemplete.docx';


        $config['server_name']='珍岛校园招聘';
        $config['server_id']=3;
        $cleanModule=Vtiger_Record_Model::getCleanInstance('Schoolresume');
        $PHPMailer=$cleanModule->configEmailServer($config);
        }

        foreach($recordsarr as $record){

            $recordModule=Vtiger_Record_Model::getInstanceById($record,'Schoolresume');
            $entity=$recordModule->getEntity();
            $column_fields=$entity->column_fields;
            //要用的字段:email,name
            $name=$column_fields['name'];
            $email=trim($column_fields['email']);
            if(2==$flag) {
            //echo '发送邮件地址：'.$email;exit();
            if(!$this->checkEmail($email)){
                //追加提示 gaocl add 2018/03/09
                $msg=array("success"=>false,"msg"=>'邮箱为空或格式不正确');
                //continue;
                break;
            }
            $entitypositionname=vtranslate($entityposition,'Schoolresume');
            $document=$PHPWord->loadTemplate($tpl);
            $document->setValue('name',$name);
            $document->setValue('entityposition',$entitypositionname);
            $document->setValue('reportsdate',$p_reportsdate);
            $dpath=$tplpath.$record.'.docx';
            $document->save($dpath);
            $content['email']=$email;//收件人的邮箱地址
            $content['sendname']=$name;//收件人的名字
            $content['subject']=$name.'，你好！来自珍岛的邮件：录用通知函 Offer Letter，请认真阅读';//邮件主题
            /*$content['body']='<div class="">

<p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: Calibri, sans-serif;"><span lang="EN-US">Dear </span><b><span style="font-family:宋体;mso-ascii-font-family:Calibri;mso-ascii-theme-font:
minor-latin;mso-fareast-font-family:宋体;mso-fareast-theme-font:minor-fareast;
mso-hansi-font-family:Calibri;mso-hansi-theme-font:minor-latin;">&nbsp;'.$name.'</span><span><o:p></o:p></span></b></p>
<p class="MsoNormal" style="line-height: 15.75pt; margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: Calibri, sans-serif;"><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;color:#1D1B11"> </span></b><b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">&nbsp;</span></b><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11"><o:p></o:p></span></p>
<p class="MsoNormal" style="text-indent: 20pt; line-height: 15.75pt; margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: Calibri, sans-serif;"><span style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">您好！很高兴通知您，鉴于您之前表现出与该职位良好的匹配度，我们一致认为您是</span><span style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:宋体;
mso-fareast-theme-font:minor-fareast">珍岛集团（<span lang="EN-US">Trueland Group</span>）</span><span style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:宋体;
mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:宋体;color:#1D1B11">合适储备人才，欢迎加入珍岛！</span><span style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:宋体;
mso-fareast-theme-font:minor-fareast">附件是公司将为您提供的有关工作、待遇及劳动关系之条款。<span lang="EN-US"><o:p></o:p></span></span></p>
<p class="MsoNormal" style="text-indent: 20pt; line-height: 15.75pt; margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: Calibri, sans-serif;"><span style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:
宋体;mso-fareast-theme-font:minor-fareast">本邮件为系统自动发送，请在收到本函</span><b><u><span lang="EN-US">5</span></u></b><b><u><span style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-ascii-font-family:Calibri;
mso-ascii-theme-font:minor-latin;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-hansi-font-family:Calibri;mso-hansi-theme-font:minor-latin">个工作日内</span></u></b><span style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:宋体;
mso-fareast-theme-font:minor-fareast">，打印此通知函签字，并附<u>毕业生推荐表原件及就业协议书</u>（一式两份</span><span lang="EN-US">/</span><span style="mso-bidi-font-size:10.5pt;font-family:宋体;mso-fareast-font-family:宋体;
mso-fareast-theme-font:minor-fareast">三份，需填写好个人信息并签字，请学校盖章）<b>邮寄至我司</b>。我司将会在收函后一周内签章回寄，锁定岗位。<span lang="EN-US"><o:p></o:p></span></span></p>
<p class="MsoNormal" align="left" style="text-align: justify; text-indent: 20pt; margin: 0cm 0cm 0.0001pt; font-size: 10.5pt; font-family: Calibri, sans-serif;"><span style="font-family: 宋体; background-position: initial initial; background-repeat: initial initial;">若您对工作相关事项有疑义，请接洽本公司人力资源部，或联系您的专职招聘经理<span lang="EN-US"><o:p></o:p></span></span></p>
<p class="MsoNormal" align="left" style="text-align: justify; margin: 0cm 0cm 0.0001pt; font-size: 10.5pt; font-family: Calibri, sans-serif;"><span style="font-family: 宋体; background-position: initial initial; background-repeat: initial initial;">电话：<span lang="EN-US">&nbsp;0510-81013678&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span></span><span lang="EN-US"><o:p></o:p></span></p>
<p class="MsoNormal" style="text-indent: 20pt; line-height: 15.75pt; margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: Calibri, sans-serif;"><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;
mso-fareast-font-family:宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:
宋体;color:#1D1B11"><o:p>&nbsp;</o:p></span></p>
<p class="MsoNormal" style="text-indent: 20pt; line-height: 15.75pt; margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: Calibri, sans-serif;"><span lang="EN-US" style="mso-bidi-font-size:10.5pt;font-family:宋体;
mso-fareast-font-family:宋体;mso-fareast-theme-font:minor-fareast;mso-bidi-font-family:
宋体;color:#1D1B11"><o:p>&nbsp;</o:p></span></p>
<p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: Calibri, sans-serif;"><span style="font-size: 14.5pt; font-family: 华文新魏;">如果觉得珍岛不错，想介绍同学应聘应走什么流程？</span><span lang="EN-US"><o:p></o:p></span></p>
<p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: Calibri, sans-serif;"><span style="font-family: 宋体;">感谢推荐，投递简历到</span><u><span lang="EN-US" style=" font-size:14.5pt ; ; ; ;;mso-fareast-font-family:
宋体;color:red;mso-font-kerning:0pt "><a href="mailto:xiaoyuan@71360.com" target="_blank">xiaoyuan@713<wbr>60.com</a></span></u><span style="font-family: 宋体;">，或者与您的专职招聘经理联系。<span lang="EN-US"><o:p></o:p></span></span></p>
</div>
';//邮件内容
            */
            $content['body']='<div><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><span style="font-family: 宋体; color: rgb(29, 27, 17);">您好！很高兴通知您，鉴于您之前表现出与该职位良好的匹配度，我们一致认为您是</span><span style="font-family: 宋体;">珍岛集团（<span lang="EN-US">Trueland Group</span>）</span><span style="font-family: 宋体; color: rgb(29, 27, 17);">合适储备人才，欢迎加入珍岛！</span><span style="font-family: 宋体;">附件是公司将为您提供的有关工作、待遇及劳动关系之条款。<span lang="EN-US"><o:p></o:p></span></span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><span style="font-size: 10.5pt; line-height: 115%; text-indent: 21pt; background-color: window; font-family: 宋体;">本邮件为系统自动发送，若您对本函有任何疑问，</span><span style="font-size: 10.5pt; line-height: 115%; text-indent: 21pt; background-color: window; font-family: 宋体; color: rgb(29, 27, 17);">请及时联系校招负责人；</span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><b style="font-family: \'Times New Roman\', serif; font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><span style="mso-bidi-font-size:10.5pt;
                line-height:99%;font-family:宋体"><br></span></b></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><b style="font-family: \'Times New Roman\', serif; font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><span style="mso-bidi-font-size:10.5pt;
                line-height:99%;font-family:宋体">若您在<span lang="EN-US">offer</span>接收日一个月内到公司报到：</span></b><span style="font-family: 宋体; line-height: 99%; background-color: window; font-size: 10.5pt; text-indent: 21pt;">&nbsp;请打印附件文档，作为培训接待凭据。</span></p><p class="MsoNormal" style="text-indent: 21pt; line-height: 99%; margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: \'Times New Roman\', serif;"><b style="mso-bidi-font-weight:normal"><span lang="EN-US" style="mso-bidi-font-size:
                10.5pt;line-height:99%;font-family:宋体"><o:p>&nbsp;</o:p></span></b></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;">
                </p><p class="MsoNormal" style="text-indent: 21pt; line-height: 99%; margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: \'Times New Roman\', serif;"><b style="mso-bidi-font-weight:normal"><span style="mso-bidi-font-size:10.5pt;
                line-height:99%;font-family:宋体">若您不能在<span lang="EN-US">offer</span>接收日一个月内报到：</span></b></p><p class="MsoNormal" style="text-indent: 21pt; line-height: 99%; margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: \'Times New Roman\', serif;"><span style="line-height: 99%; font-family: 宋体;">为锁定岗位，</span><span style="font-size: 10.5pt; line-height: 1.5; background-color: window; text-indent: 27px; font-family: 宋体;">请在收到本函</span><b style="font-size: 10.5pt; line-height: 1.5; background-color: window; font-family: Calibri, sans-serif; text-indent: 27px;"><u><span lang="EN-US">3</span></u></b><b style="font-size: 10.5pt; line-height: 1.5; background-color: window; font-family: Calibri, sans-serif; text-indent: 27px;"><u><span style="font-family: 宋体;">个工作日内</span></u></b><span style="font-size: 10.5pt; line-height: 1.5; background-color: window; text-indent: 27px; font-family: 宋体;">，打印附件通知函签字，并附<u>毕业生推荐表原件、就业协议书</u>（一式两份</span><span lang="EN-US" style="font-size: 10.5pt; line-height: 1.5; background-color: window; font-family: Calibri, sans-serif; text-indent: 27px;">/</span><span style="font-size: 10.5pt; line-height: 1.5; background-color: window; text-indent: 27px; font-family: 宋体;">三份，需填写好个人信息并签字，请学校盖章）<b>邮寄至我司</b>。我司将会在收函后一周内签章回寄，锁定岗位。您与公司签署就业协议书后，</span><span style="font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><span style="line-height: 99%; font-family: 宋体;">双方均应履行协议，若一方违约，另一方可依法要求赔偿违约金人民币</span></span><span style="font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><u><span lang="EN-US" style="mso-bidi-font-size:
                10.5pt;line-height:99%">1000</span></u></span><span style="font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><span lang="EN-US" style=" mso-bidi-font-size:10.5pt;line-height:99% ; ; "> </span></span><span style="font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><u><span style="mso-bidi-font-size:10.5pt;line-height:99%;font-family:宋体">元。</span></u></span><span style="font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><span style="line-height: 99%; font-family: 宋体;">（如期报到即视为履约）</span></span></p>
                <p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><br></p><p class="MsoNormal" align="left" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; text-align: justify; text-indent: 20pt; font-family: Calibri, sans-serif;"><span style="font-family: 宋体;">若您对工作相关事项有疑义，请接洽本公司人力资源部，或联系您的专职招聘经理<span lang="EN-US"><o:p></o:p></span></span></p><p class="MsoNormal" align="left" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; text-align: justify; font-family: Calibri, sans-serif;"><span style="font-family: 宋体;">电话：<span lang="EN-US">&nbsp;0510-81013678&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span></span><span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><span lang="EN-US" style="font-family: 宋体; color: rgb(29, 27, 17);">&nbsp;</span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><span lang="EN-US" style="font-family: 宋体; color: rgb(29, 27, 17);">&nbsp;</span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; text-align: justify; font-family: Calibri, sans-serif;"><span style="font-size: 14.5pt; font-family: 华文新魏;">如果觉得珍岛不错，想介绍同学应聘应走什么流程？</span><span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; text-align: justify; font-family: Calibri, sans-serif;"><span style="font-family: 宋体;">感谢推荐，投递简历到</span><u><span lang="EN-US" style="font-size: 14.5pt; font-family: \'Times New Roman\', serif; color: red;"><a href="mailto:xiaoyuan@71360.com" target="_blank">xiaoyuan@713<wbr>60.com</a></span></u><span style="font-family: 宋体;">，或者与您的专职招聘经理联系。</span></p></div>';
            $content['filepath']=$dpath;//附件的绝对路径
            $content['filename']=$name.'-珍岛录用函.docx';//附件的名称此处以 收件人名字-录用通知函+.docx
            $falg=$cleanModule->sendemail($PHPMailer,$content);
            unlink($dpath);
            if(!$falg){
                //追加提示 gaocl add 2018/03/09
                $msg=array("success"=>false,"msg"=>'发送邮件失败');
                //continue;
                break;
            }
            }

            try{
                $schoolqualifiedid=$this->addSchoolqualified($request,$column_fields['schoolrecruitid']);

                $_REQUE['record']='';
                $newrequest=new Vtiger_Request($_REQUE, $_REQUE);
                $newrequest->set('schoolresumeid',$record);
                $newrequest->set('schoolrecruitid',$column_fields['schoolrecruitid']);
                $newrequest->set('p_reportaddress',$p_reportaddress);
                $newrequest->set('p_reportsdate',$p_reportsdate);
                $newrequest->set('p_reportsower',$p_reportsower);
                $newrequest->set('schoolqualifiedid',$schoolqualifiedid);
                $newrequest->set('module','Schoolqualifiedpeople');
                $newrequest->set('action','SaveAjax');
                $newrequest->set('view','Edit');
                $ressorder = new Vtiger_Save_Action();
                $ressorder->saveRecord($newrequest);
                $mailstatus=1;
                if(1==$flag){
                    $mailstatus=0;
                }
                $query="UPDATE vtiger_schoolresume SET is_resume_qualified=1,mailstatus=?,entityposition=?,reportsdate=? WHERE is_resume_qualified=0 and schoolresumeid=?";
                $db->pquery($query, array($mailstatus,$entityposition,$p_reportsdate,$record));

                //追加提示 gaocl add 2018/03/09
                $msg=array("success"=>true,"msg"=>'录取成功');
            }catch(Exception $e) {
                //追加提示 gaocl add 2018/03/09
                $msg=array("success"=>false,"msg"=>'录取失败,原因:'.$e->getMessage());
            }
        }

        /*$response = new Vtiger_Response();
        $responsesponse->setResult(array());
        $response->emit();*/

        echo json_encode($msg);
    }
    //获取用户名称,ID
    public function getuserlist(){
        $db=PearDatabase::getInstance();
        $query="SELECT id,CONCAT(vtiger_users.last_name,'[',IFNULL(vtiger_departments.departmentname,'--'),']',IF(vtiger_users.`status`!='Active','[离职]','')) as username FROM  vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid
                            LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE vtiger_users.`status`='Active'";
        $result = $db->pquery($query, array());
        $arr=array();
        while($row= $db->fetchByAssoc($result)){$arr[]=$row;};
        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();

    }
    /**
     * 获取招聘的数据
     * @param Vtiger_Request $request
     */
    public function getRecordData(Vtiger_Request $request){
        $record=$request->get('record');
        $recordModule=Vtiger_Record_Model::getInstanceById($record,'Schoolresume');
        $entity=$recordModule->getEntity();
        $column_fields=$entity->column_fields;
        $response = new Vtiger_Response();
        $response->setResult($column_fields);
        $response->emit();
    }
    /**
     * 添加招邮学校记录
     * @param Vtiger_Request $request
     * @param $record
     */
    private function addSchoolqualified(Vtiger_Request $request,$record)
    {
        $reportsdate = $request->get('reportsdate');
        $reportsower = $request->get('reportsower');
        $reportaddress = $request->get('reportaddress');
        global $current_user;
        $db = PearDatabase::getInstance();
        $sql = "SELECT vtiger_schoolrecruit.schoolrecruitid, vtiger_schoolrecruit.accompany, vtiger_schoolrecruit.remarks FROM vtiger_schoolrecruit WHERE schoolrecruitid=? LIMIT 1";
        $sel_result = $db->pquery($sql, array($record));
        $res_cnt = $db->num_rows($sel_result);
        $schoolrecruit = array();
        if ($res_cnt > 0) {
            $schoolrecruit = $db->query_result_rowdata($sel_result, 0);
        }

        if (!empty($schoolrecruit)) {

            $_REQUES['record']='';
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

            /*if (!empty($ressorderecord)) {
                $schoolqualifiedid = $ressorderecord->getId();

            }*/
            return $ressorderecord->getId();
        }
    }
    public function checkEmail($str){
        $str=trim($str);
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }

    /**
     * 发邮件
     * @param Vtiger_Request $request
     */
    public function sendMail(Vtiger_Request $request){
        set_time_limit(0);
        $record=$request->get('record');
        $p_reportsdate=$request->get('reportsdate');
        global $root_directory;
        $root_directoryt=rtrim($root_directory,"/");
        $root_directoryt=rtrim($root_directoryt,"\\");
        require $root_directoryt.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPWord'.DIRECTORY_SEPARATOR.'PHPWord.php';
        $PHPWord = new PHPWord();
        $tplpath=$root_directoryt.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR;
        $tpl=$tplpath.'schooltemplete.docx';

        $config['server_name']='珍岛校园招聘';
        $config['server_id']=3;
        $cleanModule=Vtiger_Record_Model::getCleanInstance('Schoolresume');

        $PHPMailer=$cleanModule->configEmailServer($config);
        $recordModule=Vtiger_Record_Model::getInstanceById($record,'Schoolresume');
        $entity=$recordModule->getEntity();
        $column_fields=$entity->column_fields;
        //要用的字段:email,name
        $name=$column_fields['name'];
        $entityposition=$column_fields['entityposition'];
        $email=trim($column_fields['email']);
        do {
            if (!$this->checkEmail($email)) {
                $msg=array("success"=>false,"msg"=>'邮箱不正确');
                break;

            }
            $entitypositionname = vtranslate($entityposition, 'Schoolresume');
            $document = $PHPWord->loadTemplate($tpl);
            $document->setValue('name', $name);
            $document->setValue('entityposition', $entitypositionname);
            $document->setValue('reportsdate', $p_reportsdate);
            $dpath = $tplpath . $record . '.docx';
            $document->save($dpath);
            $content['email'] = $email;//收件人的邮箱地址
            $content['sendname'] = $name;//收件人的名字
            $content['subject'] = $name . '，你好！来自珍岛的邮件：录用通知函 Offer Letter，请认真阅读';//邮件主题
            $content['body'] = '<div><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><span style="font-family: 宋体; color: rgb(29, 27, 17);">您好！很高兴通知您，鉴于您之前表现出与该职位良好的匹配度，我们一致认为您是</span><span style="font-family: 宋体;">珍岛集团（<span lang="EN-US">Trueland Group</span>）</span><span style="font-family: 宋体; color: rgb(29, 27, 17);">合适储备人才，欢迎加入珍岛！</span><span style="font-family: 宋体;">附件是公司将为您提供的有关工作、待遇及劳动关系之条款。<span lang="EN-US"><o:p></o:p></span></span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><span style="font-size: 10.5pt; line-height: 115%; text-indent: 21pt; background-color: window; font-family: 宋体;">本邮件为系统自动发送，若您对本函有任何疑问，</span><span style="font-size: 10.5pt; line-height: 115%; text-indent: 21pt; background-color: window; font-family: 宋体; color: rgb(29, 27, 17);">请及时联系校招负责人；</span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><b style="font-family: \'Times New Roman\', serif; font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><span style="mso-bidi-font-size:10.5pt;
                line-height:99%;font-family:宋体"><br></span></b></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><b style="font-family: \'Times New Roman\', serif; font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><span style="mso-bidi-font-size:10.5pt;
                line-height:99%;font-family:宋体">若您在<span lang="EN-US">offer</span>接收日一个月内到公司报到：</span></b><span style="font-family: 宋体; line-height: 99%; background-color: window; font-size: 10.5pt; text-indent: 21pt;">&nbsp;请打印附件文档，作为培训接待凭据。</span></p><p class="MsoNormal" style="text-indent: 21pt; line-height: 99%; margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: \'Times New Roman\', serif;"><b style="mso-bidi-font-weight:normal"><span lang="EN-US" style="mso-bidi-font-size:
                10.5pt;line-height:99%;font-family:宋体"><o:p>&nbsp;</o:p></span></b></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;">
                </p><p class="MsoNormal" style="text-indent: 21pt; line-height: 99%; margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: \'Times New Roman\', serif;"><b style="mso-bidi-font-weight:normal"><span style="mso-bidi-font-size:10.5pt;
                line-height:99%;font-family:宋体">若您不能在<span lang="EN-US">offer</span>接收日一个月内报到：</span></b></p><p class="MsoNormal" style="text-indent: 21pt; line-height: 99%; margin: 0cm 0cm 0.0001pt; text-align: justify; font-size: 10.5pt; font-family: \'Times New Roman\', serif;"><span style="line-height: 99%; font-family: 宋体;">为锁定岗位，</span><span style="font-size: 10.5pt; line-height: 1.5; background-color: window; text-indent: 27px; font-family: 宋体;">请在收到本函</span><b style="font-size: 10.5pt; line-height: 1.5; background-color: window; font-family: Calibri, sans-serif; text-indent: 27px;"><u><span lang="EN-US">5</span></u></b><b style="font-size: 10.5pt; line-height: 1.5; background-color: window; font-family: Calibri, sans-serif; text-indent: 27px;"><u><span style="font-family: 宋体;">个工作日内</span></u></b><span style="font-size: 10.5pt; line-height: 1.5; background-color: window; text-indent: 27px; font-family: 宋体;">，打印附件通知函签字，并附<u>毕业生推荐表原件、就业协议书</u>（一式两份</span><span lang="EN-US" style="font-size: 10.5pt; line-height: 1.5; background-color: window; font-family: Calibri, sans-serif; text-indent: 27px;">/</span><span style="font-size: 10.5pt; line-height: 1.5; background-color: window; text-indent: 27px; font-family: 宋体;">三份，需填写好个人信息并签字，请学校盖章）<b>邮寄至我司</b>。我司将会在收函后一周内签章回寄，锁定岗位。您与公司签署就业协议书后，</span><span style="font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><span style="line-height: 99%; font-family: 宋体;">双方均应履行协议，若一方违约，另一方可依法要求赔偿违约金人民币</span></span><span style="font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><u><span lang="EN-US" style="mso-bidi-font-size:
                10.5pt;line-height:99%">1000</span></u></span><span style="font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><span lang="EN-US" style=" mso-bidi-font-size:10.5pt;line-height:99% ; ; "> </span></span><span style="font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><u><span style="mso-bidi-font-size:10.5pt;line-height:99%;font-family:宋体">元。</span></u></span><span style="font-size: 10.5pt; line-height: 99%; text-indent: 21pt; background-color: window;"><span style="line-height: 99%; font-family: 宋体;">（如期报到即视为履约）</span></span></p>
                <p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><br></p><p class="MsoNormal" align="left" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; text-align: justify; text-indent: 20pt; font-family: Calibri, sans-serif;"><span style="font-family: 宋体;">若您对工作相关事项有疑义，请接洽本公司人力资源部，或联系您的专职招聘经理<span lang="EN-US"><o:p></o:p></span></span></p><p class="MsoNormal" align="left" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; text-align: justify; font-family: Calibri, sans-serif;"><span style="font-family: 宋体;">电话：<span lang="EN-US">&nbsp;0510-81013678&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span></span><span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><span lang="EN-US" style="font-family: 宋体; color: rgb(29, 27, 17);">&nbsp;</span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; line-height: 15.75pt; text-indent: 20pt; text-align: justify; font-family: Calibri, sans-serif;"><span lang="EN-US" style="font-family: 宋体; color: rgb(29, 27, 17);">&nbsp;</span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; text-align: justify; font-family: Calibri, sans-serif;"><span style="font-size: 14.5pt; font-family: 华文新魏;">如果觉得珍岛不错，想介绍同学应聘应走什么流程？</span><span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="margin-right: 0cm; margin-left: 0cm; font-size: 10.5pt; text-align: justify; font-family: Calibri, sans-serif;"><span style="font-family: 宋体;">感谢推荐，投递简历到</span><u><span lang="EN-US" style="font-size: 14.5pt; font-family: \'Times New Roman\', serif; color: red;"><a href="mailto:xiaoyuan@71360.com" target="_blank">xiaoyuan@713<wbr>60.com</a></span></u><span style="font-family: 宋体;">，或者与您的专职招聘经理联系。</span></p></div>';
            $content['filepath'] = $dpath;//附件的绝对路径
            $content['filename'] = $name . '-珍岛录用函.docx';//附件的名称此处以 收件人名字-录用通知函+.docx
            $falg = $cleanModule->sendemail($PHPMailer, $content);
            unlink($dpath);
            if(!$falg){
                $msg=array("success"=>false,"msg"=>'发送失败');
                break;
            }
            $msg=array("success"=>true,"msg"=>'发送成功');
        }while(0);
        echo json_encode($msg);
    }
	
}
