<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

/*********************************************
 * With modifications by
 * Daniel Jabbour
 * iWebPress Incorporated, www.iwebpress.com
 * djabbour - a t - iwebpress - d o t - com
 ********************************************/

/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/Users.php,v 1.10 2005/04/19 14:40:48 ray Exp $
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/UserInfoUtil.php');
require_once 'data/CRMEntity.php';
require_once('modules/Calendar/Activity.php');
require_once('modules/Contacts/Contacts.php');
require_once('data/Tracker.php');
require_once('include/utils/CommonUtils.php');
require_once('include/Webservices/Utils.php');
require_once('modules/Users/UserTimeZonesArray.php');
require_once 'includes/runtime/Cache.php';

// User is used to store customer information.
/** Main class for the user module
 *
 */
class Users extends CRMEntity {
    var $log;
    /**
     * @var PearDatabase
     */
    var $db;
    // Stored fields
    var $id;
    var $authenticated = false;
    var $error_string;
    var $is_admin;
    var $showbackstage;
    var $deleted;

    var $tab_name = Array('vtiger_users','vtiger_attachments','vtiger_user2role','vtiger_asteriskextensions','vtiger_user2department');
    var $tab_name_index = Array('vtiger_users'=>'id','vtiger_attachments'=>'attachmentsid','vtiger_user2role'=>'userid','vtiger_asteriskextensions'=>'userid','vtiger_user2department'=>'userid');

    var $table_name = "vtiger_users";
    var $table_index= 'id';

    // This is the list of fields that are in the lists.
    var $list_link_field= 'last_name';

    var $list_mode;
    var $popup_type;
	var $last_modifiedtime;  //最后的修改时间，用于判断是否要重新生成文件
    var $search_fields = Array(
            'Name'=>Array('vtiger_users'=>'last_name'),
            'Email'=>Array('vtiger_users'=>'email1')
    );
    var $search_fields_name = Array(
            'Name'=>'last_name',
            'Email'=>'email1',
            'Department'=>'department'
    );

    var $module_name = "Users";

    var $object_name = "User";
    var $user_preferences;
    var $homeorder_array = array('HDB','ALVT','PLVT','QLTQ','CVLVT','HLT','GRT','OLTSO','ILTI','MNL','OLTPO','LTFAQ', 'UA', 'PA');

    var $encodeFields = Array("first_name", "last_name", "description");

    // This is used to retrieve related fields from form posts.
    var $additional_column_fields = Array('reports_to_name');

    var $sortby_fields = Array('status','email1','email2','phone_work','is_admin','user_name','last_name');

    // This is the list of vtiger_fields that are in the lists.
    var $list_fields = Array(
            'Last Name'=>Array('vtiger_users'=>'last_name'),
            'Role Name'=>Array('vtiger_user2role'=>'roleid'),
    		//'Role Name'=>Array('vtiger_user2departmentid'=>'departmentid'),
            'User Name'=>Array('vtiger_users'=>'user_name'),
			'Status'=>Array('vtiger_users'=>'status'),
            'Email'=>Array('vtiger_users'=>'email1'),
            'Admin'=>Array('vtiger_users'=>'is_admin'),
            'Phone'=>Array('vtiger_users'=>'phone_work'),
    		'Department'=>array('vtiger_departments'=>'departmentid')
    );
    var $list_fields_name = Array(
            'Last Name'=>'last_name',
           
            'Role Name'=>'roleid',
            'User Name'=>'user_name',
			'Status'=>'status',
            'Email'=>'email1',
            
            'Admin'=>'is_admin',
            'Phone'=>'phone_work',
    		'Department'=>'departmentid'
    );

    //Default Fields for Email Templates -- Pavani
    var $emailTemplate_defaultFields = array('first_name','last_name','title','department','phone_home','phone_mobile','signature','email1','email2','address_street','address_city','address_state','address_country','address_postalcode');

    var $popup_fields = array('last_name');

    // This is the list of fields that are in the lists.
    var $default_order_by = "user_name";
    var $default_sort_order = 'ASC';

    var $record_id;
    var $new_schema = true;

    var $DEFAULT_PASSWORD_CRYPT_TYPE; //'BLOWFISH', /* before PHP5.3*/ MD5;
    
    //Default Widgests
    var $default_widgets = array('PLVT', 'CVLVT', 'UA');
	var $datasource='';
	var $companysource='';
	var $companycode='';
	var $current_user_roles='';
    var $companyid=-1;
	

    /** constructor function for the main user class
     instantiates the Logger class and PearDatabase Class
     *
     */

    function Users() {
        $this->log = LoggerManager::getLogger('user');
        $this->log->debug("Entering Users() method ...");
        $this->db = PearDatabase::getInstance();
        $this->DEFAULT_PASSWORD_CRYPT_TYPE = (version_compare(PHP_VERSION, '5.3.0') >= 0)?
                'PHP5.3MD5': 'MD5';
        $this->column_fields = getColumnFields('Users');
        
        
        $this->column_fields['ccurrency_name'] = '';
        $this->column_fields['currency_code'] = '';
        $this->column_fields['currency_symbol'] = '';
        $this->column_fields['conv_rate'] = '';
        $this->log->debug("Exiting Users() method ...");
    }
    /**
     * 判断用户缓存文件是否存在，是否超期
     * @param unknown $userid
     */
	public function checkUserprivileges($userid,$last_modifiedtime=''){
		//登录用户信息文件不存在需要创建
		if(!file_exists('user_privileges/user_privileges_'.$userid.'.php')){
			require_once('modules/Users/CreateUserPrivilegeFile.php');
			createUserPrivilegesfile($userid);
			createUserSharingPrivilegesfile($userid);
		}
		//防止异常的发生
		/* if(!empty($last_modifiedtime)&&$last_modifiedtime!=$user_info['usermodifiedtime']){
			require_once('modules/Users/CreateUserPrivilegeFile.php');
			createUserPrivilegesfile($userid);
			createUserSharingPrivilegesfile($userid);
		
		} */
	}
	public function delUserprivileges($userid){
		global $root_directory;
		//登录之前先删除文件
		if(file_exists($root_directory.'user_privileges/user_privileges_'.$userid.'.php')){
			unlink($root_directory.'user_privileges/user_privileges_'.$userid.'.php');
		}
	}
    // Mike Crowe Mod --------------------------------------------------------Default ordering for us
    /**
     * Function to get sort order
     * return string  $sorder    - sortorder string either 'ASC' or 'DESC'
     */
    function getSortOrder() {
        global $log;
        $log->debug("Entering getSortOrder() method ...");
        if(isset($_REQUEST['sorder']))
            $sorder = $this->db->sql_escape_string($_REQUEST['sorder']);
        else
            $sorder = (($_SESSION['USERS_SORT_ORDER'] != '')?($_SESSION['USERS_SORT_ORDER']):($this->default_sort_order));
        $log->debug("Exiting getSortOrder method ...");
        return $sorder;
    }

    /**
     * Function to get order by
     * return string  $order_by    - fieldname(eg: 'subject')
     */
    function getOrderBy() {
        global $log;
        $log->debug("Entering getOrderBy() method ...");

        $use_default_order_by = '';
        if(PerformancePrefs::getBoolean('LISTVIEW_DEFAULT_SORTING', true)) {
            $use_default_order_by = $this->default_order_by;
        }

        if (isset($_REQUEST['order_by']))
            $order_by = $this->db->sql_escape_string($_REQUEST['order_by']);
        else
            $order_by = (($_SESSION['USERS_ORDER_BY'] != '')?($_SESSION['USERS_ORDER_BY']):($use_default_order_by));
        $log->debug("Exiting getOrderBy method ...");
        return $order_by;
    }
    // Mike Crowe Mod --------------------------------------------------------

    /** Function to set the user preferences in the session
     * @param $name -- name:: Type varchar
     * @param $value -- value:: Type varchar
     *
     */
    function setPreference($name, $value) {
        if(!isset($this->user_preferences)) {
            if(isset($_SESSION["USER_PREFERENCES"]))
                $this->user_preferences = $_SESSION["USER_PREFERENCES"];
            else
                $this->user_preferences = array();
        }
        if(!array_key_exists($name,$this->user_preferences )|| $this->user_preferences[$name] != $value) {
            $this->log->debug("Saving To Preferences:". $name."=".$value);
            $this->user_preferences[$name] = $value;
            $this->savePreferecesToDB();

        }
        $_SESSION[$name] = $value;


    }


    /** Function to save the user preferences to db
     *
     */

    function savePreferecesToDB() {
        $data = base64_encode(serialize($this->user_preferences));
        $query = "UPDATE $this->table_name SET user_preferences=? where id=?";
        $result =& $this->db->pquery($query, array($data, $this->id));
        $this->log->debug("SAVING: PREFERENCES SIZE ". strlen($data)."ROWS AFFECTED WHILE UPDATING USER PREFERENCES:".$this->db->getAffectedRowCount($result));
        $_SESSION["USER_PREFERENCES"] = $this->user_preferences;
    }

    /** Function to load the user preferences from db
     *
     */
    function loadPreferencesFromDB($value) {

        if(isset($value) && !empty($value)) {
            $this->log->debug("LOADING :PREFERENCES SIZE ". strlen($value));
            $this->user_preferences = unserialize(base64_decode($value));
            $_SESSION = array_merge($this->user_preferences, $_SESSION);
            $this->log->debug("Finished Loading");
            $_SESSION["USER_PREFERENCES"] = $this->user_preferences;


        }

    }
	
	protected function get_user_hash($input) {
		return strtolower(md5($input));
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
    function encrypt_password($user_password, $crypt_type='') {
        // encrypt the password.
        $salt = mb_substr($this->column_fields["user_name"], 0, 2,'utf-8');

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
    function encrypt_password_new($user_password,$crypt_type='',$user_name) {
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

    /** Function to authenticate the current user with the given password
     * @param $password -- password::Type varchar
     * @returns true if authenticated or false if not authenticated
     */
    function authenticate_user($password) {
        $usr_name = $this->column_fields["user_name"];

        $query = "SELECT * from $this->table_name where user_name=? AND user_hash=?";
        $params = array($usr_name, $password);
        $result = $this->db->requirePsSingleResult($query, $params, false);

        if(empty($result)) {
            $this->log->fatal("SECURITY: failed login by $usr_name");
            return false;
        }

        return true;
    }

    /** Function for validation check
     *
     */
    function validation_check($validate, $md5, $alt='') {
        $validate = base64_decode($validate);
        if(file_exists($validate) && $handle = fopen($validate, 'rb', true)) {
            $buffer = fread($handle, filesize($validate));
            if(md5($buffer) == $md5 || (!empty($alt) && md5($buffer) == $alt)) {
                return 1;
            }
            return -1;

        }else {
            return -1;
        }

    }

    /** Function for authorization check
     *
     */
    function authorization_check($validate, $authkey, $i) {
        $validate = base64_decode($validate);
        $authkey = base64_decode($authkey);
        if(file_exists($validate) && $handle = fopen($validate, 'rb', true)) {
            $buffer = fread($handle, filesize($validate));
            if(substr_count($buffer, $authkey) < $i)
                return -1;
        }else {
            return -1;
        }

    }
    /**
     * Checks the config.php AUTHCFG value for login type and forks off to the proper module
     *
     * @param string $user_password - The password of the user to authenticate
     * @return true if the user is authenticated, false otherwise
     */
    function doLogin($user_password) {
        global $AUTHCFG;
        $usr_name = $this->column_fields["user_name"];
        switch (strtoupper($AUTHCFG['authType'])) {
            case 'LDAP':
                $this->log->debug("Using LDAP authentication");
                require_once('modules/Users/authTypes/LDAP.php');
                $result = ldapAuthenticate($this->column_fields["user_name"], $user_password);
                if ($result == NULL) {
                    return false;
                } else {
                    return true;
                }
                break;

            case 'AD':
                $this->log->debug("Using Active Directory authentication");
                require_once('modules/Users/authTypes/adLDAP.php');
                $adldap = new adLDAP();
                if ($adldap->authenticate($this->column_fields["user_name"],$user_password)) {
                    return true;
                } else {
                    return false;
                }
                break;

            default:
                $this->log->debug("Using integrated/SQL authentication");
                $query = "SELECT crypt_type, user_name FROM $this->table_name WHERE user_name=?";
                $result = $this->db->requirePsSingleResult($query, array($usr_name), false);
                if (empty($result)) {
                    return false;
                }
                $crypt_type = $this->db->query_result($result, 0, 'crypt_type');
				$this->column_fields["user_name"] = $this->db->query_result($result, 0, 'user_name');

				$encrypted_password = $this->encrypt_password($user_password, $crypt_type);

                $query = "SELECT 1 from $this->table_name where user_name=? AND user_password=? AND status = ? AND isdimission=0";
                $result = $this->db->requirePsSingleResult($query, array($usr_name, $encrypted_password, 'Active'), false);
                if (empty($result)) {
                    return false;
                } else {
                    return true;
                }
                break;
        }
        return false;
    }


    /**
     * Load a user based on the user_name in $this
     * @return -- this if load was successul and null if load failed.
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    function load_user($user_password) {
        $usr_name = $this->column_fields["user_name"];
        if(isset($_SESSION['loginattempts'])) {
            $_SESSION['loginattempts'] += 1;
        }else {
            $_SESSION['loginattempts'] = 1;
        }
        if($_SESSION['loginattempts'] > 5) {
            $this->log->warn("SECURITY: " . $usr_name . " has attempted to login ". 	$_SESSION['loginattempts'] . " times.");
        }
        $this->log->debug("Starting user load for $usr_name");

        if( !isset($this->column_fields["user_name"]) || $this->column_fields["user_name"] == "" || !isset($user_password) || $user_password == "")
            return null;

        $authCheck = false;
        $authCheck = $this->doLogin($user_password);

        if(!$authCheck) {
            $this->log->warn("User authentication for $usr_name failed");
            return null;
        }

        // Get the fields for the user
        $query = "SELECT * from $this->table_name where user_name='$usr_name'";
        $result = $this->db->requireSingleResult($query, false);

        $row = $this->db->fetchByAssoc($result);
        $this->column_fields = $row;
        $this->id = $row['id'];

        $user_hash = $this->get_user_hash($user_password);

        // If there is no user_hash is not present or is out of date, then create a new one.
        if(!isset($row['user_hash']) || $row['user_hash'] != $user_hash) {
            $query = "UPDATE $this->table_name SET user_hash=? where id=?";
            $this->db->pquery($query, array($user_hash, $row['id']), true, "Error setting new hash for {$row['user_name']}: ");
        }
        $this->loadPreferencesFromDB($row['user_preferences']);


        if ($row['status'] != "Inactive") $this->authenticated = true;

        unset($_SESSION['loginattempts']);
        return $this;
    }

    /**
     * Get crypt type to use for password for the user.
     * Fix for: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/4923
     */
    function get_user_crypt_type() {

        $crypt_res = null;
        $crypt_type = $this->DEFAULT_PASSWORD_CRYPT_TYPE;

        // For backward compatability, we need to make sure to handle this case.
        global $adb;
        $table_cols = $adb->getColumnNames("vtiger_users");
        if(!in_array("crypt_type", $table_cols)) {
            return $crypt_type;
        }

        if(isset($this->id)) {
            // Get the type of crypt used on password before actual comparision
            $qcrypt_sql = "SELECT crypt_type from $this->table_name where id=?";
            $crypt_res = $this->db->pquery($qcrypt_sql, array($this->id), true);
        } else if(isset($this->column_fields["user_name"])) {
            $qcrypt_sql = "SELECT crypt_type from $this->table_name where user_name=?";
            $crypt_res = $this->db->pquery($qcrypt_sql, array($this->column_fields["user_name"]));
        } else {
            $crypt_type = $this->DEFAULT_PASSWORD_CRYPT_TYPE;
        }

        if($crypt_res && $this->db->num_rows($crypt_res)) {
            $crypt_row = $this->db->fetchByAssoc($crypt_res);
            $crypt_type = $crypt_row['crypt_type'];
        }
        return $crypt_type;
    }

    /**
     * @param string $user name - Must be non null and at least 1 character.
     * @param string $user_password - Must be non null and at least 1 character.
     * @param string $new_password - Must be non null and at least 1 character.
     * @return boolean - If passwords pass verification and query succeeds, return true, else return false.
     * @desc Verify that the current password is correct and write the new password to the DB.
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    function change_password($user_password, $new_password, $dieOnError = true,$isAdmin=true) {

        $usr_name = $this->column_fields["user_name"];
        global $mod_strings;
        global $current_user;
        $this->log->debug("Starting password change for $usr_name");

        if( !isset($new_password) || $new_password == "") {
            $this->error_string = $mod_strings['ERR_PASSWORD_CHANGE_FAILED_1'].$user_name.$mod_strings['ERR_PASSWORD_CHANGE_FAILED_2'];
            return false;
        }

        if (!is_admin($current_user) && $isAdmin) {
            $this->db->startTransaction();
            if(!$this->verifyPassword($user_password)) {
                $this->log->warn("Incorrect old password for $usr_name");
                $this->error_string = $mod_strings['ERR_PASSWORD_INCORRECT_OLD'];
                return false;
            }
            if($this->db->hasFailedTransaction()) {
                if($dieOnError) {
                    die("error verifying old transaction[".$this->db->database->ErrorNo()."] ".
                            $this->db->database->ErrorMsg());
                }
                return false;
            }
        }


        $user_hash = $this->get_user_hash($new_password);

        //set new password
        $crypt_type = $this->DEFAULT_PASSWORD_CRYPT_TYPE;
        $encrypted_new_password = $this->encrypt_password($new_password, $crypt_type);

        $query = "UPDATE $this->table_name SET user_password=?, confirm_password=?, user_hash=?, ".
                "crypt_type=? where id=?";
        $this->db->startTransaction();
        $this->db->pquery($query, array($encrypted_new_password, $encrypted_new_password,
                $user_hash, $crypt_type, $this->id));
        if($this->db->hasFailedTransaction()) {
            if($dieOnError) {
                die("error setting new password: [".$this->db->database->ErrorNo()."] ".
                        $this->db->database->ErrorMsg());
            }
            return false;
        }
		
		// Fill up the post-save state of the instance.
		if (empty($this->column_fields['user_hash'])) {
			$this->column_fields['user_hash'] = $user_hash;
		}
		
		$this->column_fields['user_password'] = $encrypted_new_password;
		$this->column_fields['confirm_password'] = $encrypted_new_password;

        return true;
    }

    function de_cryption($data) {
        require_once('include/utils/encryption.php');
        $de_crypt = new Encryption();
        if(isset($data)) {
            $decrypted_password = $de_crypt->decrypt($data);
        }
        return $decrypted_password;
    }
    function changepassword($newpassword) {
        require_once('include/utils/encryption.php');
        $en_crypt = new Encryption();
        if( isset($newpassword)) {
            $encrypted_password = $en_crypt->encrypt($newpassword);
        }

        return $encrypted_password;
    }

    function verifyPassword($password) {
        $query = "SELECT user_name,user_password,crypt_type FROM {$this->table_name} WHERE id=?";
        $result =$this->db->pquery($query, array($this->id));
        $row = $this->db->fetchByAssoc($result);
        $this->log->debug("select old password query: $query");
        $this->log->debug("return result of $row");
        $encryptedPassword = $this->encrypt_password($password, $row['crypt_type']);
        if($encryptedPassword != $row['user_password']) {
            return false;
        }
        return true;
    }

    function is_authenticated() {
        return $this->authenticated;
    }


    /** gives the user id for the specified user name
     * @param $user_name -- user name:: Type varchar
     * @returns user id
     */

    function retrieve_user_id($user_name) {
        global $adb;
        $query = "SELECT id,usermodifiedtime from vtiger_users where user_name=? AND deleted=0";
        $result  =$adb->pquery($query, array($user_name));
        $userid = $adb->query_result($result,0,'id');
        $this->last_modifiedtime =$adb->query_result($result,0,'usermodifiedtime');
        return $userid;
    }
	
	//获取用户完整信息
	function retrieve_user_info($user_name) {
        global $adb;
        //$result  =$adb->pquery("SELECT * from vtiger_users where user_name=? AND deleted=0 limit 1", array($user_name));
        $q_sql = "SELECT vtiger_users.*,vtiger_user2role.roleid,vtiger_departments.departmentname,vtiger_departments.departmentid from vtiger_users 
                LEFT JOIN vtiger_user2department ON(vtiger_users.id=vtiger_user2department.userid)
                LEFT JOIN vtiger_user2role ON(vtiger_users.id=vtiger_user2role.userid)
                LEFT JOIN vtiger_departments ON(vtiger_user2department.departmentid=vtiger_departments.departmentid)
                WHERE vtiger_users.user_name=? AND vtiger_users.deleted=0 limit 1";
        $result  =$adb->pquery($q_sql, array($user_name));

		$info=$adb->fetchByAssoc($result);
		$this->last_modifiedtime =$info['usermodifiedtime'];
		return $info;
    }
	
    /**
     * @return -- returns a list of all users in the system.
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    function verify_data() {
        $usr_name = $this->column_fields["user_name"];
        global $mod_strings;

        $query = "SELECT user_name from vtiger_users where user_name=? AND id<>? AND deleted=0";
        $result =$this->db->pquery($query, array($usr_name, $this->id), true, "Error selecting possible duplicate users: ");
        $dup_users = $this->db->fetchByAssoc($result);

        $query = "SELECT user_name from vtiger_users where is_admin = 'on' AND deleted=0";
        $result =$this->db->pquery($query, array(), true, "Error selecting possible duplicate vtiger_users: ");
        $last_admin = $this->db->fetchByAssoc($result);

        $this->log->debug("last admin length: ".count($last_admin));
        $this->log->debug($last_admin['user_name']." == ".$usr_name);

        $verified = true;
        if($dup_users != null) {
            $this->error_string .= $mod_strings['ERR_USER_NAME_EXISTS_1'].$usr_name.''.$mod_strings['ERR_USER_NAME_EXISTS_2'];
            $verified = false;
        }
        if(!isset($_REQUEST['is_admin']) &&
                count($last_admin) == 1 &&
                $last_admin['user_name'] == $usr_name) {
            $this->log->debug("last admin length: ".count($last_admin));

            $this->error_string .= $mod_strings['ERR_LAST_ADMIN_1'].$usr_name.$mod_strings['ERR_LAST_ADMIN_2'];
            $verified = false;
        }

        return $verified;
    }

    /** Function to return the column name array
     *
     */

    function getColumnNames_User() {

        $mergeflds = array("FIRSTNAME","LASTNAME","USERNAME","SECONDARYEMAIL","TITLE","OFFICEPHONE","DEPARTMENT",
                "MOBILE","OTHERPHONE","FAX","EMAIL",
                "HOMEPHONE","OTHEREMAIL","PRIMARYADDRESS",
                "CITY","STATE","POSTALCODE","COUNTRY");
        return $mergeflds;
    }


    function fill_in_additional_list_fields() {
        $this->fill_in_additional_detail_fields();
    }

    function fill_in_additional_detail_fields() {
        $query = "SELECT u1.first_name, u1.last_name from vtiger_users u1, vtiger_users u2 where u1.id = u2.reports_to_id AND u2.id = ? and u1.deleted=0";
        $result =$this->db->pquery($query, array($this->id), true, "Error filling in additional detail vtiger_fields") ;

        $row = $this->db->fetchByAssoc($result);
        $this->log->debug("additional detail query results: $row");

        if($row != null) {
            $this->reports_to_name = stripslashes(getFullNameFromArray('Users', $row));
        }
        else {
            $this->reports_to_name = '';
        }
    }


    /** 从文件获取用户信息[新增权限转移]创建全局对象
     * 创建超管信息
     * @param $userid -- user id:: Type integer
     * @returns user info in $this->column_fields array:: Type array
     *
     */

    function retrieveCurrentUserInfoFromFile($userid) {
    	
    	$this->checkUserprivileges($userid,'');
		checkFileAccessForInclusion('user_privileges/user_privileges_'.$userid.'.php');
        require('user_privileges/user_privileges_'.$userid.'.php');
        foreach($this->column_fields as $field=>$value_iter) {
            if(isset($user_info[$field])) {
                $this->$field = $user_info[$field];
                $this->column_fields[$field] = $user_info[$field];
            }
        }
        $this->column_fields['date_format'] = 'yyyy-mm-dd';
        $this->date_format = 'yyyy-mm-dd';
        $this->id = $userid;

        $superadmins=explode(',',SUPER_ADMIN);
        $datasource=array();
        $companysource=array();
        $this->companyid=$user_info['companyid'];
        $this->superadmin='';
        if(in_array($userid, $superadmins)){
            $this->superadmin=true;
        }
        
		if(!$this->superadmin){
			require("crmcache/tabPermission.php");
			require("crmcache/subordinateusers.php");
			require("crmcache/departmentanduserinfo.php");
            global $adb;
            $sql = "SELECT roleid FROM vtiger_user2role  WHERE userid=? ";
            $sel_result = $adb->pquery($sql, array($userid));
            $res_cnt = $adb->num_rows($sel_result);
            $row = $adb->query_result_rowdata($sel_result, 0);
            $current_user_roles = $row['roleid'];
            
            
			require('crmcache/actionPermission'.$current_user_roles.'.php');         
            require('crmcache/user2group.php');
            require('crmcache/groupshare.php');
            include('crmcache/groupsharecompany.php');

            if(!empty($user2group[$userid])){
                $groups=explode(',',$user2group[$userid]);
                foreach ($groups as  $groupid) {
                   if(!empty($groupshare['Groups:'.$groupid])){
                    $datasource[]=$groupshare['Groups:'.$groupid];
                   }
                    if(!empty($groupsharecompany['Groups:'.$groupid])){
                        $companysource[]=$groupsharecompany['Groups:'.$groupid];
                    }
                }
            }
            $cache = Vtiger_Cache::get('global','getPickListValuescompanycodeuser');
            if($cache){
                $this->companycode=$cache[$this->companyid];
            }else{
                $companyresult=$this->db->pquery('SELECT companyid,companycode FROM `vtiger_invoicecompany`',array());
                $values=array();
                if($this->db->num_rows($companyresult)){
                    while($row=$this->db->fetch_array($companyresult)){
                        $values[$row['companyid']]=$row['companycode'];
                    }
                }
                $this->companycode=$values[$this->companyid];
                Vtiger_Cache::set('global','getPickListValuescompanycodeuser',$values);
            }
				
			$this->profile_tabs_permission= $tabPermission[$current_user_roles];//该角色可访问的模块列表
			$this->current_user_departments=$current_user_departments;
			$this->current_user_roles=$current_user_roles;
			$this->second_user_roles=$second_user_roles;
			$this->datasource=$datasource;
			$this->companysource=$companysource;
			//$this->ifparentrole = false;
			if(!empty($subordinate_users[$userid])){	//是否是汇报对象
				//if(!empty($user2departmentinfo[$current_user_departments]) && count($user2departmentinfo[$current_user_departments])>1){
					//$this->ifparentrole = true;	//下属多个
					$this->subordinate_users= $subordinate_users[$userid];//角色
				//}/* else{$this->ifparentrole = false;} */
			}/* else{$this->ifparentrole = false;} */
			
			$this->viewPermission=$actionPermission;
			
			if(!empty($second_user_roles)){
				$secondroles=explode(' |##| ', $second_user_roles);
				$tempviewPermission=$this->viewPermission;
				$tempprofile_tabs_permission=$this->profile_tabs_permission;
				foreach($secondroles as $secondrole){
					require('crmcache/actionPermission'.$secondrole.'.php');
					
					$tempprofile_tabs_permission=array_merge($tempprofile_tabs_permission,$tabPermission[$secondrole]);
					foreach($actionPermission as $action=>$val){
						if(isset($tempviewPermission[$action])){
							if($val>$tempviewPermission[$action]){
								$tempviewPermission[$action]=$val;//权限向下兼容
							}else{
								continue;
							}
						}else{
							$tempviewPermission[$action]=$val;
						}
					}
					$this->profile_tabs_permission=$tempprofile_tabs_permission;
				}
				$this->viewPermission=$tempviewPermission;
				
			}
			
			$this->departmentuser=$user2departmentinfo[$current_user_departments];
			$this->sysuser=$user2departmentinfo[$user_info['user_sys']];
		}
        return $this;
    }

    /** 重写用户保存
     * @param $module -- module name:: Type varchar
     *添加默认值
     */
    function saveentity($module,$fileid = '') {
        global $current_user;//$adb added by raju for mass mailing
        $insertion_mode = $this->mode;
        if(empty($this->column_fields['time_zone'])) {
            $dbDefaultTimeZone = DateTimeField::getDBTimeZone();
            $this->column_fields['time_zone'] = $dbDefaultTimeZone;
            $this->time_zone = $dbDefaultTimeZone;
        }
        if(empty($this->column_fields['currency_id'])) {
            $this->column_fields['currency_id'] = CurrencyField::getDBCurrencyId();
        }
        if(empty($this->column_fields['date_format'])) {
            $this->column_fields['date_format'] = 'yyyy-mm-dd';
        }
		
		if(empty($this->column_fields['start_hour'])) {
            $this->column_fields['start_hour'] = '09:00';
        }
		
		if(empty($this->column_fields['dayoftheweek'])) {
            $this->column_fields['dayoftheweek'] = 'Sunday';
        }
		
		if(empty($this->column_fields['callduration'])) {
            $this->column_fields['callduration'] = 5;
        }
		
		if(empty($this->column_fields['othereventduration'])) {
            $this->column_fields['othereventduration'] = 5;
        }
		
		if(empty($this->column_fields['hour_format'])) {
            $this->column_fields['hour_format'] = 12;
        }
		
		if(empty($this->column_fields['activity_view'])) {
            $this->column_fields['activity_view'] = 'Today';
        }
		
		if(empty($this->column_fields['calendarsharedtype'])) {
            $this->column_fields['calendarsharedtype'] = 'public';
        }
		
		if(empty($this->column_fields['default_record_view'])) {
            $this->column_fields['default_record_view'] = 'Summary';
        }
		
		if(empty($this->column_fields['status'])) {
			$this->column_fields['status'] = 'Active';
		}

		if(empty($this->column_fields['currency_decimal_separator'])) {
			$this->column_fields['currency_decimal_separator'] = '.';
		}

		if(empty($this->column_fields['currency_grouping_separator'])) {
			$this->column_fields['currency_grouping_separator'] = ',';
		}
		$this->column_fields['usermodifiedtime'] = getDateFormat();
		
        $this->db->println("TRANS saveentity starts $module");
        $this->db->startTransaction();
        foreach($this->tab_name as $table_name) {
            if($table_name == 'vtiger_attachments') {
                $this->insertIntoAttachment($this->id,$module);
            }
            else {
                $this->insertIntoEntityTable($table_name, $module);
            }
        }
        //require_once('modules/Users/CreateUserPrivilegeFile.php');
        //createUserPrivilegesfile($this->id);
        //在这里不应该生产用户文件，2015-2-15
        unset($_SESSION['next_reminder_interval']);
        unset($_SESSION['next_reminder_time']);
        if($insertion_mode != 'edit') {
            $this->createAccessKey();
        }
        $this->db->completeTransaction();
        $this->db->println("TRANS saveentity ends");
    }

    function createAccessKey() {
        global $adb,$log;

        $log->info("Entering Into function createAccessKey()");
        $updateQuery = "update vtiger_users set accesskey=? where id=?";
        $insertResult = $adb->pquery($updateQuery,array(vtws_generateRandomAccessKey(16),$this->id));
        $log->info("Exiting function createAccessKey()");

    }

    /** Function to insert values in the specifed table for the specified module
     * @param $table_name -- table name:: Type varchar
     * @param $module -- module:: Type varchar
     */
    function insertIntoEntityTable($table_name, $module,$fileid = '') {
        global $log;
        $log->info("function insertIntoEntityTable ".$module.' vtiger_table name ' .$table_name);
        global $adb, $current_user;
        $insertion_mode = $this->mode;
        //Checkin whether an entry is already is present in the vtiger_table to update
        if($insertion_mode == 'edit') {
            $check_query = "select * from ".$table_name." where ".$this->tab_name_index[$table_name]."=?";
            $check_result=$this->db->pquery($check_query, array($this->id));

            $num_rows = $this->db->num_rows($check_result);

            if($num_rows <= 0) {
                $insertion_mode = '';
            }
        }

        // We will set the crypt_type based on the insertion_mode
        $crypt_type = '';

        if($insertion_mode == 'edit') {
            $update = '';
            $update_params = array();
            $tabid= getTabid($module);
            $sql = "select * from vtiger_field where tabid=? and tablename=? and displaytype in (1,3,5) and vtiger_field.presence in (0,2)";
            $params = array($tabid, $table_name);
        }
        else {
            $column = $this->tab_name_index[$table_name];
            if($column == 'id' && $table_name == 'vtiger_users') {
                $currentuser_id = $this->db->getUniqueID("vtiger_users");
                $this->id = $currentuser_id;
            }
            $qparams = array($this->id);
            $tabid= getTabid($module);
            $sql = "select * from vtiger_field where tabid=? and tablename=? and displaytype in (1,3,4,5) and vtiger_field.presence in (0,2)";
            $params = array($tabid, $table_name);

            $crypt_type = $this->DEFAULT_PASSWORD_CRYPT_TYPE;
        }

        $result = $this->db->pquery($sql, $params);
        $noofrows = $this->db->num_rows($result);
        for($i=0; $i<$noofrows; $i++) {
            $fieldname=$this->db->query_result($result,$i,"fieldname");
            $columname=$this->db->query_result($result,$i,"columnname");
            $uitype=$this->db->query_result($result,$i,"uitype");
            $typeofdata=$adb->query_result($result,$i,"typeofdata");

            $typeofdata_array = explode("~",$typeofdata);
            $datatype = $typeofdata_array[0];
            // cxh 2020-05-06 如果是用户编辑 不验证是否必填 添加
            if($_REQUEST['RENYUAN']!='RENYUANXITONG'){
                //用户新增数据校验 BY Joe 2005/04/14
                if($typeofdata_array[1]=='M' &&  empty($this->column_fields[$fieldname]) && $this->column_fields[$fieldname] !=='0'){
                    throw new AppException('错误的数据格式！');
                    exit;
                }
            }
            if(isset($this->column_fields[$fieldname])) {
                if($uitype == 56) {
                    if($this->column_fields[$fieldname] === 'on' || $this->column_fields[$fieldname] == 1) {
                        $fldvalue = 1;
                    }
                    else {
                        $fldvalue = 0;
                    }

                }elseif($uitype == 15) {
                    if($this->column_fields[$fieldname] == $app_strings['LBL_NOT_ACCESSIBLE']) {
                        //If the value in the request is Not Accessible for a picklist, the existing value will be replaced instead of Not Accessible value.
                        $sql="select $columname from  $table_name where ".$this->tab_name_index[$table_name]."=?";
                        $res = $adb->pquery($sql,array($this->id));
                        $pick_val = $adb->query_result($res,0,$columname);
                        $fldvalue = $pick_val;
                    }
                    else {
                        $fldvalue = $this->column_fields[$fieldname];
                    }
                }
                elseif($uitype == 33) {
                    if(is_array($this->column_fields[$fieldname])) {
                        $field_list = implode(' |##| ',$this->column_fields[$fieldname]);
                    }else {
                        $field_list = $this->column_fields[$fieldname];
                    }
                    $fldvalue = $field_list;
                }
                elseif($uitype == 99) {
					$plain_text = $this->column_fields[$fieldname];
                    $fldvalue = $this->encrypt_password($plain_text, $crypt_type);
					// Update the plain-text value with encrypted value and dependent fields
                    $this->column_fields[$fieldname] = $fldvalue;
                    $this->column_fields['crypt_type'] = $crypt_type;
					$this->column_fields['user_hash'] = $this->get_user_hash($plain_text);
                }
                else {
                    $fldvalue = $this->column_fields[$fieldname];
                    $fldvalue = stripslashes($fldvalue);
                }
                $fldvalue = from_html($fldvalue,($insertion_mode == 'edit')?true:false);



            }
            else {
                $fldvalue = '';
            }
            if($uitype == 31) {
                $themeList = get_themes();
                if(!in_array($fldvalue, $themeList) || $fldvalue == '') {
                    global $default_theme;
                    if(!empty($default_theme) && in_array($default_theme, $themeList)) {
                        $fldvalue = $default_theme;
                    } else {
                        $fldvalue = $themeList[0];
                    }
                }
                if($current_user->id == $this->id) {
                    $_SESSION['vtiger_authenticated_user_theme'] = $fldvalue;
                }
            } elseif($uitype == 32) {
                $languageList = Vtiger_Language::getAll();
                $languageList = array_keys($languageList);
                if(!in_array($fldvalue, $languageList) || $fldvalue == '') {
                    global $default_language;
                    if(!empty($default_language) && in_array($default_language, $languageList)) {
                        $fldvalue = $default_language;
                    } else {
                        $fldvalue = $languageList[0];
                    }
                }
                if($current_user->id == $this->id) {
                    $_SESSION['authenticated_user_language'] = $fldvalue;
                }
            }
            if($fldvalue=='') {
                $fldvalue = $this->get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
                //$fldvalue =null;
            }
            if($insertion_mode == 'edit') {
                if($i == 0) {
                    $update = $columname."=?";
                }
                else {
                    $update .= ', '.$columname."=?";
                }
                array_push($update_params, $fldvalue);
            }
            else {
                $column .= ", ".$columname;
                array_push($qparams, $fldvalue);
            }
        }

        if($insertion_mode == 'edit') {
            //Check done by Don. If update is empty the the query fails
            if(trim($update) != '') {
                $sql1 = "update $table_name set $update where ".$this->tab_name_index[$table_name]."=?";
				array_push($update_params, $this->id);
                $this->db->pquery($sql1, $update_params);
            }

        }
        else {
            // Set the crypt_type being used, to override the DB default constraint as it is not in vtiger_field
            if($table_name == 'vtiger_users' && strpos('crypt_type', $column) === false) {
                $column .= ', crypt_type';
                $qparams[]= $crypt_type;
            }
            // END
			
			if($table_name == 'vtiger_users' && strpos('user_hash', $column) === false) {
				$column .= ', user_hash';
				$qparams[] = $this->column_fields['user_hash'];
			}
            //设置工号 2017/02/28 gaocl add
            //用户编号改为6码，原有的用户编号不足6码的，前面补0
            // cxh 2020-05-28 注释掉生成工号
            /*if($table_name == 'vtiger_users' && strpos($column,'usercode') === false){
                $max=$this->db->getUniqueID("vtiger_usercode");
                $max=str_pad($max, 6, '0', STR_PAD_LEFT);
                $column .= ', usercode';
                array_push($qparams, $max);

                //用户编号改为6码，原有的用户编号不足6码的，前面补0
                $this->db->pquery('insert into vtiger_userscode (ucode,status) values(?,?)',array($max,1));
            }*/

            $sql1 = "insert into $table_name ($column) values(". generateQuestionMarks($qparams) .")";
            
            $this->db->pquery($sql1, $qparams);
        }
    }



    /** Function to insert values into the attachment table
     * @param $id -- entity id:: Type integer
     * @param $module -- module:: Type varchar
     */
    function insertIntoAttachment($id,$module) {
        global $log;
        $log->debug("Entering into insertIntoAttachment($id,$module) method.");

        foreach($_FILES as $fileindex => $files) {
            if($files['name'] != '' && $files['size'] > 0) {
                $files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
                $this->uploadAndSaveFile($id,$module,$files);
            }
        }

        $log->debug("Exiting from insertIntoAttachment($id,$module) method.");
    }

    /** Function to retreive the user info of the specifed user id The user info will be available in $this->column_fields array
     * 函数是检查在数组column_fields制定用户id的用户有效信息
     * @param $record -- record id:: Type integer
     * @param $module -- module:: Type varchar
     */
    function retrieve_entity_info($record, $module) {
        global $adb,$log;
        $log->debug("Entering into retrieve_entity_info($record, $module) method.");

        if($record == '') {
            $log->debug("record is empty. returning null");
            return null;
        }
			
        $result = Array();
        
        
        foreach($this->tab_name_index as $table_name=>$index) {
        	
            $result[$table_name] = $adb->pquery("select * from ".$table_name." where ".$index."=?", array($record));
        }
        
        
        $tabid = getTabid($module);
        $sql1 =  "select * from vtiger_field where tabid=? and vtiger_field.presence in (0,2)";
        $result1 = $adb->pquery($sql1, array($tabid));
        
        
        
        $noofrows = $adb->num_rows($result1);
        
        
        for($i=0; $i<$noofrows; $i++) {
            $fieldcolname = $adb->query_result($result1,$i,"columnname");
            $tablename = $adb->query_result($result1,$i,"tablename");
            $fieldname = $adb->query_result($result1,$i,"fieldname");
			
            $fld_value = $adb->query_result($result[$tablename],0,$fieldcolname);
            $this->column_fields[$fieldname] = $fld_value;
            $this->$fieldname = $fld_value;
			

        }
       
        $this->column_fields["record_id"] = $record;
        $this->column_fields["record_module"] = $module;
        
        $currency_query = "select * from vtiger_currency_info where id=? and currency_status='Active' and deleted=0";
        $currency_result = $adb->pquery($currency_query, array($this->column_fields["currency_id"]));
        if($adb->num_rows($currency_result) == 0) {
            $currency_query = "select * from vtiger_currency_info where id =1";
            $currency_result = $adb->pquery($currency_query, array());
        }
        $currency_array = array("$"=>"&#36;","&euro;"=>"&#8364;","&pound;"=>"&#163;","&yen;"=>"&#165;");
        $ui_curr = $currency_array[$adb->query_result($currency_result,0,"currency_symbol")];
        if($ui_curr == "")
            $ui_curr = $adb->query_result($currency_result,0,"currency_symbol");
        $this->column_fields["currency_name"]= $this->currency_name = $adb->query_result($currency_result,0,"currency_name");
        $this->column_fields["currency_code"]= $this->currency_code = $adb->query_result($currency_result,0,"currency_code");
        $this->column_fields["currency_symbol"]= $this->currency_symbol = $ui_curr;
        $this->column_fields["conv_rate"]= $this->conv_rate = $adb->query_result($currency_result,0,"conversion_rate");
		if($this->column_fields['no_of_currency_decimals'] == '')
			$this->column_fields['no_of_currency_decimals'] = $this->no_of_currency_decimals = getCurrencyDecimalPlaces();

		// TODO - This needs to be cleaned up once default values for fields are picked up in a cleaner way.
		// This is just a quick fix to ensure things doesn't start breaking when the user currency configuration is missing
		if($this->column_fields['currency_grouping_pattern'] == ''
				&& $this->column_fields['currency_symbol_placement'] == '') {

			$this->column_fields['currency_grouping_pattern'] = $this->currency_grouping_pattern = '123,456,789';
			$this->column_fields['currency_decimal_separator'] = $this->currency_decimal_separator = '.';
			$this->column_fields['currency_grouping_separator'] = $this->currency_grouping_separator = ',';
			$this->column_fields['currency_symbol_placement'] = $this->currency_symbol_placement = '$1.0';
		}

		// TODO - This needs to be cleaned up once default values for fields are picked up in a cleaner way.
		// This is just a quick fix to ensure things doesn't start breaking when the user currency configuration is missing
		if($this->column_fields['currency_grouping_pattern'] == ''
				&& $this->column_fields['currency_symbol_placement'] == '') {

			$this->column_fields['currency_grouping_pattern'] = $this->currency_grouping_pattern = '123,456,789';
			$this->column_fields['currency_decimal_separator'] = $this->currency_decimal_separator = '.';
			$this->column_fields['currency_grouping_separator'] = $this->currency_grouping_separator = ',';
			$this->column_fields['currency_symbol_placement'] = $this->currency_symbol_placement = '$1.0';
		}

		$this->id = $record;
		$log->debug("Exit from retrieve_entity_info($record, $module) method.");

        return $this;
    }


    /** Function to upload the file to the server and add the file details in the attachments table
     * @param $id -- user id:: Type varchar
     * @param $module -- module name:: Type varchar
     * @param $file_details -- file details array:: Type array
     */
    function uploadAndSaveFile($id,$module,$file_details) {
        global $log;
        $log->debug("Entering into uploadAndSaveFile($id,$module,$file_details) method.");

        global $current_user;
        global $upload_badext;

        $date_var = date('Y-m-d H:i:s');

        //to get the owner id
        $ownerid = $this->column_fields['assigned_user_id'];
        if(!isset($ownerid) || $ownerid=='')
            $ownerid = $current_user->id;

        $file = $file_details['name'];
        $binFile = sanitizeUploadFileName($file, $upload_badext);

        $filename = ltrim(basename(" ".$binFile)); //allowed filename like UTF-8 characters
        $filetype= $file_details['type'];
        $filesize = $file_details['size'];
        $filetmp_name = $file_details['tmp_name'];

        $current_id = $this->db->getUniqueID("vtiger_crmentity");

        //get the file path inwhich folder we want to upload the file
        $upload_file_path = decideFilePath();
        //upload the file in server
        $upload_status = move_uploaded_file($filetmp_name,$upload_file_path.$current_id."_".$binFile);

        $save_file = 'true';
        //only images are allowed for these modules
        if($module == 'Users') {
            $save_file = validateImageFile($file_details);
        }
        if($save_file == 'true') {

            $sql1 = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?,?,?,?,?,?,?)";
            $params1 = array($current_id, $current_user->id, $ownerid, $module." Attachment", $this->column_fields['description'], $this->db->formatString("vtiger_crmentity","createdtime",$date_var), $this->db->formatDate($date_var, true));
            $this->db->pquery($sql1, $params1);

            $sql2="insert into vtiger_attachments(attachmentsid, name, description, type, path) values(?,?,?,?,?)";
            $params2 = array($current_id, $filename, $this->column_fields['description'], $filetype, $upload_file_path);
            $result=$this->db->pquery($sql2, $params2);

            if($id != '') {
                $delquery = 'delete from vtiger_salesmanattachmentsrel where smid = ?';
                $this->db->pquery($delquery, array($id));
            }

            $sql3='insert into vtiger_salesmanattachmentsrel values(?,?)';
            $this->db->pquery($sql3, array($id, $current_id));

            //we should update the imagename in the users table
            $this->db->pquery("update vtiger_users set imagename=? where id=?", array($filename, $id));
        }
        else {
            $log->debug("Skip the save attachment process.");
        }
        $log->debug("Exiting from uploadAndSaveFile($id,$module,$file_details) method.");

        return;
    }


    /** Function to save the user information into the database
     * @param $module -- module name:: Type varchar
     *
     */
    function save($module_name, $fileid = '') {
        global $log, $adb,$current_user;
        //Save entity being called with the modulename as parameter
        require_once("include/events/include.inc");
        $currentusername=$current_user->column_fields['user_name'];
        $modifiedname=$this->column_fields['user_name'];
        if(!self::isBulkSaveMode() && $currentusername!=$modifiedname) {
            $em = new VTEventsManager($adb);
            // Initialize Event trigger cache
            $em->initTriggerCache(false,false,$module_name);
            $entityData = VTEntityData::fromCRMEntity($this);

            $em->triggerEvent("vtiger.entity.beforesave.modifiable", $entityData);

            $em->triggerEvent("vtiger.entity.beforesave", $entityData);
            $em->triggerEvent("vtiger.entity.beforesave.final", $entityData);
        }
        $this->saveentity($module_name);
		if($em && $currentusername!=$modifiedname){
            $em->triggerEvent("vtiger.entity.aftersave", $entityData);
            $em->triggerEvent("vtiger.entity.aftersave.final", $entityData);
        }
        // Added for Reminder Popup support
        $query_prev_interval = $adb->pquery("SELECT reminder_interval from vtiger_users where id=?",
                array($this->id));
        $prev_reminder_interval = $adb->query_result($query_prev_interval,0,'reminder_interval');

        //$focus->imagename = $image_upload_array['imagename'];
        $this->saveHomeStuffOrder($this->id);
        SaveTagCloudView($this->id);

        // Added for Reminder Popup support
        $this->resetReminderInterval($prev_reminder_interval);
        /* 2015-2-15 增加字段之后无法保存,因为这里重复更新了一次.
		//Creating the Privileges Flat File
        if(isset($this->column_fields['roleid'])) {
            updateUser2RoleMapping($this->column_fields['roleid'],$this->id);
        }
        if(isset($this->column_fields['departmentid'])) {
        	updateUser2DepartmentMapping($this->column_fields['departmentid'],$this->id);
        }*/
        require_once('modules/Users/CreateUserPrivilegeFile.php');
        createUserPrivilegesfile($this->id);
        createUserSharingPrivilegesfile($this->id);
        

    }


    /**
     * gives the order in which the modules have to be displayed in the home page for the specified user id
     * @param $id -- user id:: Type integer
     * @returns the customized home page order in $return_array
     */
    function getHomeStuffOrder($id) {
        global $adb;
        if(!is_array($this->homeorder_array)) {
            $this->homeorder_array = array('UA', 'PA', 'ALVT','HDB','PLVT','QLTQ','CVLVT','HLT',
                    'GRT','OLTSO','ILTI','MNL','OLTPO','LTFAQ');
        }
        $return_array = Array();
        $homeorder=Array();
        if($id != '') {
            $qry=" select distinct(vtiger_homedefault.hometype) from vtiger_homedefault inner join vtiger_homestuff  on vtiger_homestuff.stuffid=vtiger_homedefault.stuffid where vtiger_homestuff.visible=0 and vtiger_homestuff.userid=?";
            $res=$adb->pquery($qry, array($id));
            for($q=0;$q<$adb->num_rows($res);$q++) {
                $homeorder[]=$adb->query_result($res,$q,"hometype");
            }
            for($i = 0;$i < count($this->homeorder_array);$i++) {
                if(in_array($this->homeorder_array[$i],$homeorder)) {
                    $return_array[$this->homeorder_array[$i]] = $this->homeorder_array[$i];
                }else {
                    $return_array[$this->homeorder_array[$i]] = '';
                }
            }
        }else {
            for($i = 0;$i < count($this->homeorder_array);$i++) {
              if(in_array($this->homeorder_array[$i], $this->default_widgets)){                
                $return_array[$this->homeorder_array[$i]] = $this->homeorder_array[$i];
              }else{
                  $return_array[$this->homeorder_array[$i]] = '';
              }
            }
        }
        return $return_array;
    }

    function getDefaultHomeModuleVisibility($home_string,$inVal) {
        $homeModComptVisibility= 1;
        if($inVal == 'postinstall') {
            if($_REQUEST[$home_string] != '') {
                $homeModComptVisibility = 0;
            } else if(in_array($home_string, $this->default_widgets)){
                $homeModComptVisibility = 0;
            }
        }
        return $homeModComptVisibility;

    }

    function insertUserdetails($inVal) {
        global $adb;
        $uid=$this->id;
        $s1=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('ALVT',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s1,1,'Default',$uid,$visibility,'Top Accounts'));

        $s2=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('HDB',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s2,2,'Default',$uid,$visibility,'Home Page Dashboard'));

        $s3=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('PLVT',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s3,3,'Default',$uid,$visibility,'Top Potentials'));

        $s4=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('QLTQ',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s4,4,'Default',$uid,$visibility,'Top Quotes'));

        $s5=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('CVLVT',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s5,5,'Default',$uid,$visibility,'Key Metrics'));

        $s6=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('HLT',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s6,6,'Default',$uid,$visibility,'Top Trouble Tickets'));

        $s7=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('UA',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s7,7,'Default',$uid,$visibility,'Upcoming Activities'));

        $s8=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('GRT',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s8,8,'Default',$uid,$visibility,'My Group Allocation'));

        $s9=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('OLTSO',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s9,9,'Default',$uid,$visibility,'Top Sales Orders'));

        $s10=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('ILTI',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s10,10,'Default',$uid,$visibility,'Top Invoices'));

        $s11=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('MNL',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s11,11,'Default',$uid,$visibility,'My New Leads'));

        $s12=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('OLTPO',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s12,12,'Default',$uid,$visibility,'Top Purchase Orders'));

        $s13=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('PA',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s13,13,'Default',$uid,$visibility,'Pending Activities'));
        ;

        $s14=$adb->getUniqueID("vtiger_homestuff");
        $visibility=$this->getDefaultHomeModuleVisibility('LTFAQ',$inVal);
        $sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
        $res=$adb->pquery($sql, array($s14,14,'Default',$uid,$visibility,'My Recent FAQs'));

        // Non-Default Home Page widget (no entry is requried in vtiger_homedefault below)
        $tc = $adb->getUniqueID("vtiger_homestuff");
        $visibility=0;
        $sql="insert into vtiger_homestuff values($tc, 15, 'Tag Cloud', $uid, $visibility, 'Tag Cloud')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s1.",'ALVT',5,'Accounts')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s2.",'HDB',5,'Dashboard')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s3.",'PLVT',5,'Potentials')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s4.",'QLTQ',5,'Quotes')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s5.",'CVLVT',5,'NULL')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s6.",'HLT',5,'HelpDesk')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s7.",'UA',5,'Calendar')";
        $adb->pquery($sql,array());

        $sql="insert into vtiger_homedefault values(".$s8.",'GRT',5,'NULL')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s9.",'OLTSO',5,'SalesOrder')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s10.",'ILTI',5,'Invoice')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s11.",'MNL',5,'Leads')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s12.",'OLTPO',5,'PurchaseOrder')";
        $adb->pquery($sql, array());

        $sql="insert into vtiger_homedefault values(".$s13.",'PA',5,'Calendar')";
        $adb->pquery($sql,array());

        $sql="insert into vtiger_homedefault values(".$s14.",'LTFAQ',5,'Faq')";
        $adb->pquery($sql, array());

    }

    /** function to save the order in which the modules have to be displayed in the home page for the specified user id
     * @param $id -- user id:: Type integer
     */
	 function saveHomeStuffOrder($id)
	 {
        global $log,$adb;
        $log->debug("Entering in function saveHomeOrder($id)");

		 if($this->mode == 'edit')
		 {
			 for($i = 0;$i < count($this->homeorder_array);$i++)
			 {
				 if($_REQUEST[$this->homeorder_array[$i]] != '')
				 {
                    $save_array[] = $this->homeorder_array[$i];
                    $qry=" update vtiger_homestuff,vtiger_homedefault set vtiger_homestuff.visible=0 where vtiger_homestuff.stuffid=vtiger_homedefault.stuffid and vtiger_homestuff.userid=".$id." and vtiger_homedefault.hometype='".$this->homeorder_array[$i]."'";//To show the default Homestuff on the the Home Page
                    $result=$adb->pquery($qry, array());
                }
				 else
				 {
                    $qry="update vtiger_homestuff,vtiger_homedefault set vtiger_homestuff.visible=1 where vtiger_homestuff.stuffid=vtiger_homedefault.stuffid and vtiger_homestuff.userid=".$id." and vtiger_homedefault.hometype='".$this->homeorder_array[$i]."'";//To hide the default Homestuff on the the Home Page
                    $result=$adb->pquery($qry, array());
                }
            }
            if($save_array !="")
                $homeorder = implode(',',$save_array);
        }
		 else
		 {
            $this->insertUserdetails('postinstall');

        }
        $log->debug("Exiting from function saveHomeOrder($id)");
    }

    /**
     * Track the viewing of a detail record.  This leverages get_summary_text() which is object specific
     * params $user_id - The user that is viewing the record.
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    function track_view($user_id, $current_module,$id='') {
        $this->log->debug("About to call vtiger_tracker (user_id, module_name, item_id)($user_id, $current_module, $this->id)");

        $tracker = new Tracker();
        $tracker->track_view($user_id, $current_module, $id, '');
    }

    /**
     * Function to get the column value of a field
     * @param $column_name -- Column name
     * @param $input_value -- Input value for the column taken from the User
     * @return Column value of the field.
     */
    function get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype='') {
        if (is_uitype($uitype, "_date_") && $fldvalue == '') {
            return null;
        }
        if ($datatype == 'I' || $datatype == 'N' || $datatype == 'NN') {
            return 0;
        }
        return $fldvalue;
    }

    /**
     * Function to reset the Reminder Interval setup and update the time for next reminder interval
     * @param $prev_reminder_interval -- Last Reminder Interval on which the reminder popup's were triggered.
     */
    function resetReminderInterval($prev_reminder_interval) {
        global $adb;
        if($prev_reminder_interval != $this->column_fields['reminder_interval'] ) {
            unset($_SESSION['next_reminder_interval']);
            unset($_SESSION['next_reminder_time']);
            $set_reminder_next = date('Y-m-d H:i');
            // NOTE date_entered has CURRENT_TIMESTAMP constraint, so we need to reset when updating the table
            $adb->pquery("UPDATE vtiger_users SET reminder_next_time=?, date_entered=? WHERE id=?",array($set_reminder_next, $this->column_fields['date_entered'], $this->id));
        }
    }

    function initSortByField($module) {
        // Right now, we do not have any fields to be handled for Sorting in Users module. This is just a place holder as it is called from Popup.php
    }

    function filterInactiveFields($module) {
        // TODO Nothing do right now
    }

    function deleteImage() {
        $sql1 = 'SELECT attachmentsid FROM vtiger_salesmanattachmentsrel WHERE smid = ?';
        $res1 = $this->db->pquery($sql1, array($this->id));
        if ($this->db->num_rows($res1) > 0) {
            $attachmentId = $this->db->query_result($res1, 0, 'attachmentsid');

            $sql2 = "DELETE FROM vtiger_crmentity WHERE crmid=? AND setype='Users Attachments'";
            $this->db->pquery($sql2, array($attachmentId));

            $sql3 = 'DELETE FROM vtiger_salesmanattachmentsrel WHERE smid=? AND attachmentsid=?';
            $this->db->pquery($sql3, array($this->id, $attachmentId));

            $sql2 = "UPDATE vtiger_users SET imagename='' WHERE id=?";
            $this->db->pquery($sql2, array($this->id));

            $sql4 = 'DELETE FROM vtiger_attachments WHERE attachmentsid=?';
            $this->db->pquery($sql4, array($attachmentId));
        }
    }

    /** Function to delete an entity with given Id */
    function trash($module, $id) {
        global $log, $current_user;

        $this->mark_deleted($id);
    }

	function transformOwnerShipAndDelete($userId,$transformToUserId){
		$adb = PearDatabase::getInstance();
		
		$em = new VTEventsManager($adb);

        // Initialize Event trigger cache
		$em->initTriggerCache();

		$entityData  = VTEntityData::fromUserId($adb, $userId);
		
		//set transform user id
		$entityData->set('transformtouserid',$transformToUserId);

        $em->triggerEvent("vtiger.entity.beforedelete", $entityData);

		vtws_transferOwnership($userId, $transformToUserId);
		//禁止删除用户
		//delete from user vtiger_table;
		//$sql = "delete from vtiger_users where id=?";
		
		//$adb->pquery($sql, array($userId));
	}

    /**
     * This function should be overridden in each module.  It marks an item as deleted.
     * @param <type> $id
     */
    function mark_deleted($id) {
        global $log, $current_user, $adb;
        $date_var = date('Y-m-d H:i:s');
        $query = "UPDATE vtiger_users set status=?,date_modified=?,modified_user_id=? where id=?";
        $adb->pquery($query, array('Inactive', $adb->formatDate($date_var, true),
                $current_user->id, $id), true,"Error marking record deleted: ");
    }

    /**
     * Function to get the user if of the active admin user.
     * @return Integer - Active Admin User ID
     */
    public static function getActiveAdminId() {
        global $adb;
        $cache = Vtiger_Cache::getInstance();
		if($cache->getAdminUserId()){
			return $cache->getAdminUserId();
		} else {
        $sql = "SELECT id FROM vtiger_users WHERE is_admin='On' and status='Active' limit 1";
        $result = $adb->pquery($sql, array());
        $adminId = 1;
        $it = new SqlResultIterator($adb, $result);
        foreach ($it as $row) {
            $adminId = $row->id;
        }
			$cache->setAdminUserId($adminId);
        return $adminId;
		}
    }

    /**
     * Function to get the active admin user object
     * @return Users - Active Admin User Instance
     */
    public static function getActiveAdminUser() {
        $adminId = self::getActiveAdminId();
        $user = new Users();
        $user->retrieveCurrentUserInfoFromFile($adminId);
        return $user;
    }

	/**
    * Function to set the user time zone and language
    * @param- $_REQUEST array
    */
    public function setUserPreferences($requestArray) {
		global $adb;
		$updateData = array();
		
		if (isset($requestArray['about']['phone'])) $updateData['phone_mobile'] = vtlib_purify ($requestArray['about']['phone']);
		if (isset($requestArray['about']['country'])) $updateData['address_country'] = vtlib_purify ($requestArray['about']['country']);
		if (isset($requestArray['about']['company_job'])) $updateData['title'] = vtlib_purify ($requestArray['about']['company_job']);
		if (isset($requestArray['about']['department'])) $updateData['department'] = vtlib_purify ($requestArray['about']['department']);

		if (isset($requestArray['lang_name'])) $updateData['language'] = vtlib_purify ($requestArray['lang_name']);
		if (isset($requestArray['time_zone'])) $updateData['time_zone']= vtlib_purify ($requestArray['time_zone']);
		if (isset($requestArray['date_format'])) $updateData['date_format']= vtlib_purify ($requestArray['date_format']);
		
		if (!empty($updateData)) {
			$updateQuery = 'UPDATE vtiger_users SET '. ( implode('=?,', array_keys($updateData)). '=?') . ' WHERE id = ?';
			$updateQueryParams = array_values($updateData);
			$updateQueryParams[] = $this->id;
			$adb->pquery($updateQuery, $updateQueryParams);
		}
	}
	
	/**
	 * Function to set the Company Logo
	 * @param- $_REQUEST array
	 * @param- $_FILE array
	 */
	public function uploadOrgLogo($requestArray, $fileArray) {
		global $adb;
		$file = $fileArray['file'];
		$logo_name = $file['name'];
		$file_size = $file['size'];
		$file_type = $file['type'];

		$filetype_array = explode("/",$file_type);
		$file_type_val = strtolower($filetype_array[1]);

		$validFileFormats =  array('jpeg', 'png', 'jpg', 'pjpeg', 'x-png', 'gif');

		if ($file_size != 0 && in_array($file_type_val, $validFileFormats)) {
			//Uploading the selected Image
			move_uploaded_file($file['tmp_name'], 'test/logo/'.$logo_name);

			//Updating Database
			$sql = 'UPDATE vtiger_organizationdetails SET logoname = ? WHERE organization_id = ?';
			$params = array(decode_html($logo_name), '1');
			$adb->pquery($sql, $params);
			copy('test/logo/'.$logo_name, 'test/logo/application.ico');
		}
	}

	/**
    * Function to update Base Currency of Product
    * @param- $_REQUEST array
    */
	public function updateBaseCurrency($requestArray) {
		global $adb;
		if (isset ($requestArray['currency_name'])) {
			$currency_name = vtlib_purify($requestArray['currency_name']);

			$result = $adb->pquery('SELECT currency_code, currency_symbol FROM vtiger_currencies WHERE currency_name = ?', array($currency_name));
			$num_rows = $adb->num_rows($result);
			if ($num_rows > 0) {
				$currency_code = decode_html($adb->query_result($result, 0, 'currency_code'));
				$currency_symbol = decode_html($adb->query_result($result, 0,'currency_symbol'));
			}

			//Updating Database
			$query = 'UPDATE vtiger_currency_info SET currency_name = ?, currency_code = ?, currency_symbol = ? WHERE id = ?';
			$params = array($currency_name, $currency_code, $currency_symbol, '1');
			$adb->pquery($query, $params);

		}
	}

	/**
    * Function to update Config file
    * @param- $_REQUEST array
    */
	public function updateConfigFile($requestArray) {
	   if (isset ($requestArray['currency_name'])) {
		   $currency_name = vtlib_purify($requestArray['currency_name']);
		   $currency_name = '$currency_name = \''.$currency_name.'\'';

		   //Updating in config inc file
		   $filename = 'config.inc.php';
		   if (file_exists($filename)) {
			   $contents = file_get_contents($filename);
			   $contents = str_replace('$currency_name = \'USA, Dollars\'', $currency_name, $contents);
			   file_put_contents($filename, $contents);
		   }
	   }
   }
}

class Users_CRMSetup {

	/**
	 * Function to get user setup status
	 * @param- User id
	 * @return-is First User or not
	 */
	public static function isFirstUser($user) {
		global $adb;

		$isFirstUser = false;
		if (is_admin($user)) {
			$query = 'SELECT COUNT(*) AS count FROM vtiger_crmsetup';
			$result = $adb->pquery($query, array());
			$count = $adb->query_result($result, 0, 'count');
			if (!$count) {
				$isFirstUser = true;
			}
		}
		return $isFirstUser;
	}
	
	/**
	 * Function to get user setup status
	 * @return-is First User or not
	 */
	public static function insertEntryIntoCRMSetup($id) {
		global $adb;

		//updating user setup status into database
		$insertQuery = 'INSERT INTO vtiger_crmsetup (userid, setup_status) VALUES (?, ?)';
		$adb->pquery($insertQuery, array($id, '1'));

	}
	/**
	 * Function to get user setup status
	 * @param- User id
	 * @return-Setup Status of user
	 */
	public static function getUserSetupStatus($id) {
		global $adb;

		$userSetupStatus = false;
		$query = 'SELECT 1 FROM vtiger_crmsetup WHERE userid = ? AND setup_status = ?';
		$result = $adb->pquery($query, array($id, '1'));
		$num_rows = $adb->num_rows($result);
		if ($num_rows === 0) {
			$userSetupStatus = true;
		}
		return $userSetupStatus;
	}

	/**
	 * Function to get packages list from CRM
	 * @return <Array> List of packages
	 */
	public static function getPackagesList() {
		$restrictedModulesList = array('Emails', 'ModComments', 'Rss', 'Portal', 'Integration',
			'PBXManager', 'Dashboard', 'Home', 'vtmessages', 'vttwitter');

		$packagesList = array(
			'Tools' => array(
				'label' => 'Contact Management',
				'imageName' => 'BasicPackage.png',
				'description' => 'Unify and store your contacts alongside detailed notes, documents, emails, calendar events, and more. Additionally, configure what information each CRM user can see and update, and automate business activities such as email responses and contact information updates.',
				'modules' => array(
					'Contacts' => 'Contacts',
					'Accounts' => 'Organizations',
					'MailManager' => 'Mail Manager',
					'Reports' => 'Reports',
					'Access Control' => 'Access Control',
					'Workflows' => 'Workflows',
					'Mail Converter' => 'Mail Converter',
					'Web-to-lead forms' => 'Web-to-lead forms'
				)),
			'Sales' => array(
				'label' => 'Sales Automation',
				'imageName' => 'SalesAutomation.png',
				'description' => 'Capture Leads from your website, or import lists of them, then develop a process for qualifying and turning them into potential sales opportunities, and another for winning those potential opportunities. Additionally, track and segment your sales funnel, individual, and team, performance areas.',
				'modules' => array(
					'Potentials' => 'Opportunities'
				)),
			'Marketing' => array(
				'label' => 'Marketing',
				'imageName' => 'Marketing.png',
				'description' => 'Send individual, targeted, or bulk emails to your contacts, leads, and customers, and see how they engage with each communication, with tools to analyze and improve campaign performance.',
				'modules' => array()),
			
			'Support' => array(
				'label' => 'Support',
				'imageName' => 'Support.png',
				'description' => 'Create and track customer requests/tasks via tickets, and even allow your customers to create and monitor their own tickets and details through a professional customer portal.',
				'modules' => array(
					'HelpDesk' => 'Tickets',
					'ServiceContracts' => 'Service Contracts',
					'CustomerPortal' => 'Customer Portal'
				)),
			'Inventory' => array(
				'label' => 'Invoicing & Inventory Management',
				'imageName' => 'Inventory.png',
				'description' => 'Build a database of your products and services, maintain inventories, standard prices and prices books, and use these to create quotes, invoices, and sales orders. Additionally, send and collect payment requests online.',
				'modules' => array(
					'Quotes' => 'Quotes',
					'Invoice' => 'Invoice',
					'SalesOrder' => 'Sales Order',
					'PurchaseOrder' => 'Purchase Orders',
					'PriceBooks' => 'Price Books'
				)),
			'Project' => array(
				'label' => 'Project Management',
				'imageName' => 'ProjectManagement.png',
				'description' => 'Build and manage customer-associated projects, with detailed tasks that can be assigned to CRM users and placed on their calendars.',
				'modules' => array(
					'Project' => 'Projects',
					'ProjectTask' => 'Tasks',
					'ProjectMilestone' => 'Milestones'
				))
		);

		global $adb;
		$result = $adb->pquery('SELECT parent, name, tablabel FROM vtiger_tab', array());
		$numOfRows = $adb->num_rows($result);

		for ($i = 0; $i < $numOfRows; $i++) {
			$moduleName = $adb->query_result($result, $i, 'name');
			$moduleExists = false;

			foreach ($packagesList as $packageName => $packageInfo) {
				if (in_array($moduleName, $restrictedModulesList) || array_key_exists($moduleName, $packageInfo['modules'])) {
					$moduleExists = true;
				}
			}

			if (!$moduleExists) {
				$parentName = $adb->query_result($result, $i, 'parent');

				if ($parentName) {
					if (array_key_exists($parentName, $packagesList)) {
						$packagesList[$parentName]['modules'][$moduleName] = $adb->query_result($result, $i, 'tablabel');
					} else {
						$packagesList[$parentName] = array('label' => $parentName,
							'modules' => array($moduleName => $adb->query_result($result, $i, 'tablabel')));
					}
				}
			}
		}
		return $packagesList;
	}

}
?>