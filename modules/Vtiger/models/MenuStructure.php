<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger MenuStructure Model
 */
class Vtiger_MenuStructure_Model extends Vtiger_Base_Model {

	protected $limit = 7; // Max. limit of persistent top-menu items to display.  顶部菜单固定的最大显示项
	protected $enableResponsiveMode = true; // Should the top-menu items be responsive (width) on UI?  是否是顶部菜单项UI响应

	const TOP_MENU_INDEX = 'top';
	const MORE_MENU_INDEX = 'more';
	const LEFT_MENU_INDEX	=	'left';	//young.yang
	const BUTTON_MENU_INDEX	=	'button'; //young.yang 

	/**
	 * Function to get all the top menu models
	 * @return <array> - list of Vtiger_Menu_Model instances
	 */
	public function getTop() {
		return $this->get(self::TOP_MENU_INDEX);
	}
	
	/**
	 * Function to get all the more menu models
	 * @return <array> - Associate array of Parent name mapped to Vtiger_Menu_Model instances
	 */
	public function getMore() {
		$moreTabs = $this->get(self::MORE_MENU_INDEX); 
		foreach($moreTabs as $key=>$value){ 
			if(!$value){ 
						unset($moreTabs[$key]); 
				} 
		} 
		return $moreTabs;
	}

	/**
	 * Function to get the limit for the number of menu models on the Top list
	 * @return <Number>
	 */
	public function getLimit() {
		return $this->limit;
	}
	
	/**
	 * Function to determine if the structure should support responsive UI.
	 */
	public function getResponsiveMode() {
		return $this->enableResponsiveMode;
	}

	/**
	 * Function to get an instance of the Vtiger MenuStructure Model from list of menu models
	 * 从菜单列表中返回一个ms的实例
	 * @param <array> $menuModelList - array of Vtiger_Menu_Model instances
	 * @return Vtiger_MenuStructure_Model instance
	 */
	public static function getInstanceFromMenuList($menuModelList, $selectedMenu='') {
		$structureModel = new self();
		$topMenuLimit = $structureModel->getResponsiveMode() ? 0 : $structureModel->getLimit();
		$currentTopMenuCount = 0;

		$menuListArray = array();
		$menuListArray[self::TOP_MENU_INDEX] = array();
		$menuListArray[self::MORE_MENU_INDEX] = $structureModel->getEmptyMoreMenuList();  // 初始化更多的菜单列表
		
		foreach($menuModelList as $menuModel) {
			if(($menuModel->get('tabsequence') != -1 && (!$topMenuLimit || $currentTopMenuCount < $topMenuLimit)) ) {
				$menuListArray[self::TOP_MENU_INDEX][$menuModel->get('name')] = $menuModel;
				$currentTopMenuCount++;
			}
			
			$parent = $menuModel->get('parent');
			if($parent == 'Sales' || $parent == 'Marketing'){
				$parent = 'MARKETING_AND_SALES';
			}
			
			$menuListArray[self::MORE_MENU_INDEX][strtoupper($parent)][$menuModel->get('name')] = $menuModel;
		}
		
		// 设置当前的菜单项的激活状态
		if(!empty($selectedMenu) && !array_key_exists($selectedMenu, $menuListArray[self::TOP_MENU_INDEX])) {
			$selectedMenuModel = $menuModelList[$selectedMenu];
			if($selectedMenuModel) {
				$menuListArray[self::TOP_MENU_INDEX][$selectedMenuModel->get('name')] = $selectedMenuModel;
			}
		}
		
		// Apply custom comparator
// 		foreach ($menuListArray[self::MORE_MENU_INDEX] as $parent => &$values) {
// 			uksort($values, array('Vtiger_MenuStructure_Model', 'sortMenuItemsByProcess'));
// 		}
		//uksort($menuListArray[self::TOP_MENU_INDEX], array('Vtiger_MenuStructure_Model', 'sortMenuItemsByProcess'));
		return $structureModel->setData($menuListArray);
	}
	
	/**
	 * Custom comparator to sort the menu items by process.
	 * Refer: http://php.net/manual/en/function.uksort.php
	 */
	static function sortMenuItemsByProcess($a, $b) {
		static $order = NULL;
		if ($order == NULL) {
			$order = array(
				'Campaigns',
				'Leads',
				'Contacts',
				'Accounts',
				'Potentials',
				'Quotes',
				'Invoice',
				'SalesOrder',
				'HelpDesk',
				'Faq',
				'Project',
				'Assets',
				'ServiceContracts',
				'Products',
				'Services',
				'PriceBooks',
				'Vendors',
				'PurchaseOrder',
				'MailManager',
				'Calendar',
				'Documents',
				'SMSNotifier',
				'RecycleBin'				
			);
		}
		$apos  = array_search($a, $order);
		$bpos  = array_search($b, $order);

		if ($apos === false) return PHP_INT_MAX;
		if ($bpos === false) return -1*PHP_INT_MAX;

		return ($apos - $bpos);
	}


	private function getEmptyMoreMenuList(){
		return array('MARKETING_AND_SALES'=>array(),'SUPPORT'=>array(),'INVENTORY'=>array(),'TOOLS'=>array(),'ANALYTICS'=>array());
	}
	
	//保存当前用户菜单列表到缓存
	public function getMoremenuList() {
		$menulist=$_SESSION['user_menus'];
		if(empty($menulist)){
			$moreTabs = $this->get(self::MORE_MENU_INDEX);		
			foreach($moreTabs as $parent=>$value){ 
				if($value){
					$menulist[$parent]['zh']=vtranslate("LBL_$parent");
					foreach($value as $modelname=>$model){
						$menulist[$parent]['childlist'][]=array('translatedModuleLabel'=>vtranslate($model->get('label'),$modelname),'DefaultUrl'=>$model->getDefaultUrl());
					}
				} 
			}
			$_SESSION['user_menus']=$menulist;
		}
		return $menulist;
	}
}
