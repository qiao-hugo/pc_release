<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

global $Server_Path;
global $Portal_Path;
global $corpid,$Secret;
$corpid = "wx4d2151259aa58eba";
$Secret ="9n5ih34K5fFxuwAUJRiLhGY_HPvtA9p79VPfA4ltIgdsjTCGQOTWMCF6FEANlg_d";

//This is the vtiger server path ie., the url to access the vtiger server in browser
//Ex. i access my vtiger as http://mickie:90/vtiger/index.php so i will give as http://mickie:90/vtiger
$Server_Path = 'http://192.168.40.188/';

// This is the Customer Portal Path
$Portal_Path =  'http://www.appcrm.com/';

//This is the customer portal path ie., url to access the customer portal in browser 
//Ex. i access my portal as http://mickie:90/customerportal/login.php so i will give as http://mickie:90/customerportal
$Authenticate_Path = '';

//Give a temporary directory path which is used when we upload attachment
$upload_dir = 'tmp';

//These are the Proxy Settings parameters
$proxy_host = ''; //Host Name of the Proxy
$proxy_port = ''; //Port Number of the Proxy
$proxy_username = ''; //User Name of the Proxy
$proxy_password = ''; //Password of the Proxy

//The character set to be used as character encoding for all soap requests
$default_charset = 'UTF-8';//'ISO-8859-1';

$default_language = 'en_us';

$languages = Array('zh_cn'=>'chinese','en_us'=>'US English','de_de'=>'DE Deutsch','pt_br'=>'PT Brasil','fr_fr'=>'Francais', 'tr_tr'=>'Turkce Dil Paketi');

$default_timezone = 'Asia/Shanghai';
/** �����Ҫ����ϵͳĬ��ʱ�� �����⺯�� function_exists('date_default_timezone_set') Edit by Joe @20150518 */
if(isset($default_timezone)){
    date_default_timezone_set($default_timezone);
}
?>
