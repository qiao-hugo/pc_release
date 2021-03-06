<?php
$_REQUEST['testbak']=11;
require_once('include/utils/utils.php');
require_once('include/logging.php');


//$log =& LoggerManager::getLogger('RecurringInvoice');
//$log->debug("invoked RecurringInvoice");

/*客服跟进天数更新设置*/

updateUserEncryptPassword();

/*
 * 用户密码加密更新处理
 */
function updateUserEncryptPassword(){
	global $adb, $log;
	//用户数据取得
	$listQuery ="select * from vtiger_users";
	$result = $adb->pquery($listQuery, array());
	if (empty($result) || $adb->num_rows($result)==0){
		echo '没有要处理的用户数据!';
		return;
	}
	//密码加密更新处理
	$updateSql = "update vtiger_users set user_password=? where id=?";
	$num_rows=$adb->num_rows($result);
	for($i=0; $i<$num_rows; $i++) {
		$id=$adb->query_result($result,$i,'id');
		$user_name=$adb->query_result($result,$i,'user_name');
		$crypt_type=$adb->query_result($result,$i,'crypt_type');
		$new_password='Zhendao@321@';
		$encrypted_new_password = encrypt_password($user_name,$new_password, $crypt_type);
		
		$adb->pquery($updateSql, array($encrypted_new_password,$id));
	}
	echo '密码加密更新成功!更新件数:'.$num_rows;
}

/**
 * @return string encrypted password for storage in DB and comparison against DB password.
 * @param string $user_name - Must be non null and at least 2 characters
 * @param string $user_password - Must be non null and at least 1 character.
 * @desc Take an unencrypted username and password and return the encrypted password
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
 * All Rights Reserved..
 * Contributor(s): ______________________________________..
 */
function encrypt_password($user_name,$user_password, $crypt_type='') {
	// encrypt the password.
	$salt = mb_substr($user_name, 0, 2,'utf-8');

	// Fix for: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/4923
	if($crypt_type == '') {
		// Try to get the crypt_type which is in database for the user
		$crypt_type = $this->get_user_crypt_type();
	}

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

?>
