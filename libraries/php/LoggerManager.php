<?php
/**********************************************************
 * 日志文件，配置文件
 * @author young.yang
 **********************************************************/
 
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__));
require_once(LOG4PHP_DIR . '/Logger.php');						//引用日志
Logger::configure($root_directory .'/log4php.config.properties');	//配置文件
class LoggerManager {
	static function getlogger($name = 'ROOT') {
		return Logger::getLogger($name);;
	}
}