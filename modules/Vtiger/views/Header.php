<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

abstract class Vtiger_Header_View extends Vtiger_View_Controller {

	function __construct() {
		parent::__construct();
	}

	//Note : To get the right hook for immediate parent in PHP,
	// specially in case of deep hierarchy
	/*function preProcessParentTplName(Vtiger_Request $request) {
		return parent::preProcessTplName($request);
	}*/

	/**
	 * 确定文件扩展名在module文件夹
	 * Utility function to manage the backward compatible file load
	 * which are registered for 5.x modules (and now provided for 6.x as well).
	 */
	protected function checkFileUriInRelocatedMouldesFolder($fileuri) {
		list ($filename, $query) = explode('?', $fileuri);
		// prefix the base lookup folder (relocated file).
		/* if (strpos($filename, 'modules') === 0) {
			$filename = $filename;
		} */
		return file_exists($filename);
	}

	/**
	 * 头部的链接 包括退出 详细 后台和提醒
	 * @return <Array> - List of Vtiger_Link_Model instances
	 */
	function getHeaderLinks() {
		//$appUniqueKey = vglobal('application_unique_key');
		//$vtigerCurrentVersion = vglobal('vtiger_current_version');
		//$site_URL = vglobal('site_URL');
		//$userModel = Users_Record_Model::getCurrentUserModel();
		//$userEmail = $userModel->get('email1');
		// 1.反馈
		global $current_user;
		//print_r($current_user);
		$headerLinks = array();	
		// 提醒
		/*$headerLinks[] = array(
				'linktype' => 'REMINDERLINK',
				'linklabel' => '',//LBL_CRM_REMINDER
				'myremindercount'=>JobAlerts_Record_Model::getReminderResultCount('myreminder'),
				//'linkurl' => '?module=JobAlerts&view=List&public=myreminder',
				'linkurl' => '',
				'target'=>'_blank',
				'linkicon' => 'reminder.png',
				'childlinks' => array(
						array (
								'linktype' => 'REMINDERLINK',
								'recordcount'=>JobAlerts_Record_Model::getReminderResultCount('new'),
								'linklabel' => 'LBL_CRM_REMINDER_NEW',
								'linkurl' => '?module=JobAlerts&view=List&public=new',
								'linkicon' => '',
								'target'=>'_blank',
						),
						array (
								'linktype' => 'REMINDERLINK',
								'recordcount'=>JobAlerts_Record_Model::getReminderResultCount('wait'),
								'linklabel' => 'LBL_CRM_REMINDER_WAIT',
								'linkurl' => '?module=JobAlerts&view=List&public=wait',
								'linkicon' => '',
								'target'=>'_blank',
						),
						array (
								'linktype' => 'REMINDERLINK',
								'recordcount'=>JobAlerts_Record_Model::getReminderResultCount('finish'),
								'linklabel' => 'LBL_CRM_REMINDER_FINISH',
								'linkurl' => '?module=JobAlerts&view=List&public=finish',
								'linkicon' => '',
								'target'=>'_blank',
						),
						array (
								'linktype' => 'REMINDERLINK',
								'recordcount'=>JobAlerts_Record_Model::getReminderResultCount('myreminder'),
								'linklabel' => 'LBL_CRM_REMINDER_MY_REMINDER',
								'linkurl' => '?module=JobAlerts&view=List&public=myreminder',
								'linkicon' => '',
								'target'=>'_blank',
						),
						array (
								'linktype' => 'REMINDERLINK',
								'recordcount'=>JobAlerts_Record_Model::getReminderResultCount('relation'),
								'linklabel' => 'LBL_CRM_REMINDER_RELATION',
								'linkurl' => '?module=JobAlerts&view=List&public=relation',
								'linkicon' => '',
								'target'=>'_blank',
						)
				)
		);*/
		//array_push($headerLinks, $reminderLinks);这种写法效率低 不建议
		// 2.管理员显示系统设置
		if($current_user->is_admin=='on' || $current_user->showbackstage==1){
			$headerLinks[] = array(
				'linktype' => 'HEADERLINK',
				'linklabel' => '',//LBL_CRM_SETTINGS
				'linkurl' => '',
				'linkicon' => 'setting.png',
				'childlinks' => array(
					array (
						'linktype' => 'HEADERLINK',
						'linklabel' => 'LBL_CRM_SETTINGS',
						'linkurl' => '?module=Vtiger&parent=Settings&view=Index',
						'linkicon' => '',
					)
				)
			);	
		}
		// 3.个人设置
		$headerLinks[] = array(
				'linktype' => 'HEADERLINK',
				'linklabel' => $current_user->last_name,
				'linkurl' => '',
				'linkicon' => 'Users.png',
				'childlinks' => array(
					array (
						'linktype' => 'HEADERLINK',
						'linklabel' => 'LBL_MY_PREFERENCES',
						'linkurl' => 'index.php?module=Users&view=PreferenceDetail&record='.$current_user->id,
						'linkicon' => '',
					),
//                    array (
//                        'linktype' => 'HEADERLINK',
//                        'linklabel' => 'LBL_MY_LEAVESYTEM',
//                        'linkurl' => 'http://192.168.44.63',
//                        'linkicon' => '',
//                    ),
//                    array (
//                        'linktype' => 'HEADERLINK',
//                        'linklabel' => 'LBL_MY_CUSTOMSERVICE',
//                        'linkurl' => 'http://192.168.7.195',
//                        'linkicon' => '',
//                    ),
//                    array (
//                        'linktype' => 'HEADERLINK',
//                      'linklabel' => 'LBL_RECRUITMENTSYSTEM',
//                        'linkurl' => 'http://192.168.44.127',
//                        'linkicon' => '',
//                    ),
					//array(), // separator
					array (
						'linktype' => 'HEADERLINK',
						'linklabel' => 'LBL_SIGN_OUT',
						'linkurl' => '?module=Users&parent=Settings&action=Logout',
						'linkicon' => '',
					)
				)
			);
		//array_push($headerLinks, $userPersonalSettingsLinks);
		$headerLinkInstances = array();
		$index = 0;
		foreach($headerLinks as  $headerLink) {
			$headerLinkInstance = Vtiger_Link_Model::getInstanceFromValues($headerLink);
			foreach($headerLink['childlinks'] as $childLink) {
				$headerLinkInstance->addChildLink(Vtiger_Link_Model::getInstanceFromValues($childLink));
			}
			$headerLinkInstances[$index++] = $headerLinkInstance;
		}
        return $headerLinkInstances;
		//查不到数据 不用了
		/* $headerLinks = Vtiger_Link_Model::getAllByType(Vtiger_Link::IGNORE_MODULE, array('HEADERLINK'));
		foreach($headerLinks as $headerType => $headerLinks) {
			foreach($headerLinks as $headerLink) {
				$headerLinkInstances[$index++] = Vtiger_Link_Model::getInstanceFromLinkObject($headerLink);
			}
		} */
		//反馈去掉， 2014-12-23 /young start
			/* array (
				'linktype' => 'HEADERLINK',
				'linklabel' => 'LBL_FEEDBACK',
				'linkurl' => "javascript:window.open('http://vtiger.com/products/crm/od-feedback/index.php?version=".$vtigerCurrentVersion.
					"&email=".$userEmail."&uid=".$appUniqueKey.
					"&ui=6','feedbackwin','height=400,width=550,top=200,left=300')",
				'linkicon' => 'info.png',
				'childlinks' => array(
					array (
						'linktype' => 'HEADERLINK',
						'linklabel' => 'LBL_DOCUMENTATION',
						'linkurl' => 'http://www.baidu.com',
						'linkicon' => '',
						'target' => '_blank'
					),
					array (
						'linktype' => 'HEADERLINK',
						'linklabel' => 'LBL_VIDEO_TUTORIAL',
						'linkurl' => 'http://www.baidu.com',
						'linkicon' => '',
						'target' => '_blank'
					),
					// Note: This structure is expected to generate side-bar feedback button.
					array (
						'linktype' => 'HEADERLINK',
						'linklabel' => 'LBL_FEEDBACK',
						'linkurl' => "javascript:window.open('http://vtiger.com/products/crm/od-feedback/index.php?version=".$vtigerCurrentVersion.
							"&email=".$userEmail."&uid=".$appUniqueKey.
							"&ui=6','feedbackwin','height=400,width=550,top=200,left=300')",
						'linkicon' => '',
					)
				)
			) *///end	
	}

	/**
	 * 加载模块js列表 不过应该没用
	 * 继承include/runtime/controller下的父类，
	 */
	/* function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);  //验证js加载
		$headerScripts = Vtiger_Link_Model::getAllByType(Vtiger_Link::IGNORE_MODULE, array('HEADERSCRIPT'));
		foreach($headerScripts as $headerType => $headerScripts) {
			foreach($headerScripts as $headerScript) {
				//echo $headerScript->linkurl."\n";
				if ($this->checkFileUriInRelocatedMouldesFolder($headerScript->linkurl)) {
					$headerScriptInstances[] = Vtiger_JsScript_Model::getInstanceFromLinkObject($headerScript);
				}
			}
		}
		return $headerScriptInstances;
	} */

	/**
	 * Function to get the list of Css models to be included
	 * 头部的css样式
	 * @param Vtiger_Request $request
	 * @return <Array> - List of Vtiger_CssScript_Model instances
	 */
	function getHeaderCss(Vtiger_Request $request) {
		/* $headerCssInstances = parent::getHeaderCss($request);
		$headerCss = Vtiger_Link_Model::getAllByType(Vtiger_Link::IGNORE_MODULE, array('HEADERCSS')); */
		$selectedThemeCssPath = Vtiger_Theme::getStylePath();
		//TODO : check the filename whether it is less or css and add relative less
		$isLessType = (strpos($selectedThemeCssPath, ".less") !== false)? true:false;
		$cssScriptModel = new Vtiger_CssScript_Model();
		$headerCssInstances[] = $cssScriptModel->set('href', $selectedThemeCssPath)
									->set('rel',
											$isLessType?
											Vtiger_CssScript_Model::LESS_REL :
											Vtiger_CssScript_Model::DEFAULT_REL);
											
		return $headerCssInstances;
		/* foreach($headerCss as $headerType => $cssLinks) {
			foreach($cssLinks as $cssLink) {
				if ($this->checkFileUriInRelocatedMouldesFolder($cssLink->linkurl)) {
					$headerCssInstances[] = Vtiger_CssScript_Model::getInstanceFromLinkObject($cssLink);
				}
			}
		} */	
	}

	/**
	 *  公告 暂时关闭
	 */
	function getAnnouncement() {
		//$announcement = Vtiger_Cache::get('announcement', 'value');
		$model = new Vtiger_Base_Model();
		//if(!$announcement) {
			$announcement = get_announcements();
				//Vtiger_Cache::set('announcement', 'value', $announcement);
		//}
		return $model->set('announcement', $announcement);
	}

}
