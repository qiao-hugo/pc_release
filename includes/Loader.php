<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

global $LOADER_FILE_DIR;
$LOADER_FILE_DIR = dirname(__FILE__);

class Vtiger_Loader {
	protected static $includeCache = array();
	protected static $includePathCache = array();

	/**
	 * Static function to resolve the qualified php filename to absolute path
	 * 加载文件，从根目录开始查找，可以识别~~a/a/a.php 或者a.a.a
	 * @global <String> $LOADER_FILE_DIR
	 * @param <String> $qualifiedName
	 * @return <String> Absolute File Name
	 */
	static function resolveNameToPath($qualifiedName, $fileExtension='php') {
		global $LOADER_FILE_DIR;
		$allowedExtensions = array('php', 'js', 'css', 'less');

		$file = '';
		if(!in_array($fileExtension, $allowedExtensions)) {
			return '';
		}
		// TO handle loading vtiger files
		if (strpos($qualifiedName, '~~') === 0) {
			$file = str_replace('~~', '', $qualifiedName);
			$file = $LOADER_FILE_DIR . DIRECTORY_SEPARATOR .'..' . DIRECTORY_SEPARATOR . $file;
		} else if (strpos($qualifiedName, '~') === 0) {
			$file = str_replace('~', '', $qualifiedName);
			$file = $LOADER_FILE_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $file;
		} else {
			$file = str_replace('.', DIRECTORY_SEPARATOR, $qualifiedName) . '.' .$fileExtension;
			$file = $LOADER_FILE_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $file;
		}

		return $file;
	}

	/**
	 * Function to include a given php file through qualified file name
	 * @param <String> $qualifiedName
	 * @param <Boolean> $supressWarning
	 * @return <Boolean>
	 */
	static function includeOnce($qualifiedName, $supressWarning=false) {

		if (isset(self::$includeCache[$qualifiedName])) {
			return true;
		}

		$file = self::resolveNameToPath($qualifiedName);
	    $file=realpath($file);
		if (!file_exists($file)) {
			
			return false;
		}

		// Check file inclusion before including it
		//checkFileAccessForInclusion($file);
		
		$status = -1;
		if ($supressWarning) {
			$status = @include_once $file;
		} else {
			
			$status = include_once $file;
		}

		$success = ($status === 0)? false : true;
		
		if ($success) {
			self::$includeCache[$qualifiedName] = $file;
		}

		return $success;
	}

	static function includePath($qualifiedName) {
		// Already included?
		if (isset(self::$includePathCache[$qualifiedName])) {
			return true;
		}

		$path = realpath(self::resolveNameToPath($qualifiedName));
		self::$includePathCache[$qualifiedName] = $path;

		// TODO Check if resolvedPath is already part of include path.
		set_include_path($path . PATH_SEPARATOR . get_include_path());
		return true;
	}

	/**
	 * Function to get the class name of a given Component, of given Type, for a given Module
	 * @param <String> $componentType
	 * @param <String> $componentName
	 * @param <String> $moduleName
	 * @return <String> Required Class Name
	 * @throws AppException
	 */
	public static function getComponentClassName($componentType, $componentName, $moduleName='Vtiger') {
		$key=$componentType.'##'.$componentName.'##'.$moduleName;
		$realyClassName= Vtiger_Cache::get('global','loadrealyClassName');
	if(!empty($realyClassName[$key])){
		return $realyClassName[$key];
	}else{
		// Change component type from view to views, action to actions to navigate to the right path.
		$componentTypeDirectory = strtolower($componentType).'s';
		// Fall Back Directory & Fall Back Class  退回或者备用
		$fallBackModuleDir = $fallBackModuleClassPath = 'Vtiger';
		// Intermediate Fall Back Directories & Classes, before relying on final fall back  在最终依赖备用类之前使用的中间的类或者文件
		$firstFallBackModuleDir = $firstFallBackModuleClassPath = '';
		$secondFallBackDir = $secondFallBackClassPath = '';  //在最终依赖备用类之前使用的中间的类或者文件
		// Default module directory & class name   默认的module文件夹和类名
		$moduleDir = $moduleClassPath = $moduleName;
		// Change the Module directory & class, along with intermediate fall back directory and class, if module names has submodule as well
		// 如果模块含有子模块，改变module的文件夹和类，中间类文件夹随着一同
		if(strpos($moduleName, ':') > 0) {  //含有子模块
			$moduleHierarchyParts = explode(':', $moduleName);//module层级
			$moduleDir = str_replace(':', '.', $moduleName);//格式为modules.moduledir.
			$moduleClassPath = str_replace(':', '_', $moduleName);
			$actualModule = $moduleHierarchyParts[count($moduleHierarchyParts)-1];	//活动的module
			$secondFallBackModuleDir= $secondFallBackModuleClassPath =  $actualModule;	//子类
			if($actualModule != 'Users') {
				$baseModule = $moduleHierarchyParts[0];
				if($baseModule == 'Settings')  $baseModule = 'Settings:Vtiger';
				$firstFallBackDir = str_replace(':', '.', $baseModule);	//父类
				$firstFallBackClassPath = str_replace(':', '_', $baseModule);
			}
		}
		// Build module specific file path and class name
		// 1.当前module,无论是setting下的，还是module下的，
		//echo 'modules.'.$moduleDir.'.'.$componentTypeDirectory.'.'.$componentName.'|';
		$moduleSpecificComponentFilePath = Vtiger_Loader::resolveNameToPath('modules.'.$moduleDir.'.'.$componentTypeDirectory.'.'.$componentName);
		$moduleSpecificComponentClassName = $moduleClassPath.'_'.$componentName.'_'.$componentType;
		//echo $moduleSpecificComponentFilePath;
		if(file_exists($moduleSpecificComponentFilePath)) {
			$realyClassName[$key]=$moduleSpecificComponentClassName;
			Vtiger_Cache::set('global','loadrealyClassName',$realyClassName);
			return $moduleSpecificComponentClassName;
		}

		// 2.当是后台setting时，会优先返回到后台的vtiger目录下
		// Build first intermediate fall back file path and class name
		if(!empty($firstFallBackDir) && !empty($firstFallBackClassPath)) {
			$fallBackComponentFilePath = Vtiger_Loader::resolveNameToPath('modules.'.$firstFallBackDir.'.'.$componentTypeDirectory.'.'.$componentName);
			$fallBackComponentClassName = $firstFallBackClassPath.'_'.$componentName.'_'.$componentType;

			if(file_exists($fallBackComponentFilePath)) {
				$realyClassName[$key]=$fallBackComponentClassName;
				Vtiger_Cache::set('global','loadrealyClassName',$realyClassName);
				return $fallBackComponentClassName;
			}
		}
		// 3.主要指的是user;
		// Build intermediate fall back file path and class name
		if(!empty($secondFallBackModuleDir) && !empty($secondFallBackModuleClassPath)) {
			$fallBackComponentFilePath = Vtiger_Loader::resolveNameToPath('modules.'.$secondFallBackModuleDir.'.'.$componentTypeDirectory.'.'.$componentName);
			$fallBackComponentClassName = $secondFallBackModuleClassPath.'_'.$componentName.'_'.$componentType;
			
			if(file_exists($fallBackComponentFilePath)) {
				$realyClassName[$key]=$fallBackComponentClassName;
				Vtiger_Cache::set('global','loadrealyClassName',$realyClassName);
				return $fallBackComponentClassName;
			}
		}
		// 4.默认
		// Build fall back file path and class name
		$fallBackComponentFilePath = Vtiger_Loader::resolveNameToPath('modules.'.$fallBackModuleDir.'.'.$componentTypeDirectory.'.'.$componentName);
		$fallBackComponentClassName = $fallBackModuleClassPath.'_'.$componentName.'_'.$componentType;
		if(file_exists($fallBackComponentFilePath)) {
			$realyClassName[$key]=$fallBackComponentClassName;
			Vtiger_Cache::set('global','loadrealyClassName',$realyClassName);
			return $fallBackComponentClassName;
		}
		throw new AppException('Handler not found.');
		
	}
		
	}

	/**
	 * Function to auto load the required class files matching the directory pattern modules/xyz/types/Abc.php for class xyz_Abc_Type  Contacts_ListView_Model
	 * @param <String> $className 
	 * @return <Boolean>
	 */
	public static function autoLoad($className) {

		$parts = explode('_', $className);
		$noOfParts = count($parts);
		if($noOfParts > 2) {
			$filePath = 'modules.';
			// Append modules and sub modules names to the path
			for($i=0; $i<($noOfParts-2); ++$i) {
				$filePath .= $parts[$i]. '.';
			}
			$fileName = $parts[$noOfParts-2];
			$fileComponentName = strtolower($parts[$noOfParts-1]).'s';
			$filePath .= $fileComponentName. '.' .$fileName;
			$GLOBALS['class_loader_all'][]=$filePath;
			
            return Vtiger_Loader::includeOnce($filePath);
		}

		return false;
	}
}

function vimport($qualifiedName) {
	return Vtiger_Loader::includeOnce($qualifiedName);
}

function vimport_try($qualifiedName) {
	return Vtiger_Loader::includeOnce($qualifiedName, true);
}

function vimport_path($qualifiedName) {
	return Vtiger_Loader::includePath($qualifiedName);
}

spl_autoload_register('Vtiger_Loader::autoLoad'); //核心，注册自动加载函数，
