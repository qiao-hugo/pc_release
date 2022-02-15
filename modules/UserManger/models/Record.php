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
 * ModComments Record Model
 */
vimport('~~/include/Webservices/Custom/ChangePassword.php');
class UserManger_Record_Model extends Vtiger_Record_Model {
    public $BASE_REQUEST_URL = "https://api-qidac1.yunxuetang.cn/";
    public $API_KEY = "37ad4e8f-2bb7-4411-87c1-16f9b32f971d";
    public $SECRET_KEY = "b6e328ae-6e5a-496e-9962-8723d05a389f";
    public function getRoles($request){
        global $adb;
        $query='SELECT parentdepartment FROM `vtiger_departments` WHERE departmentid=?';
        $departmentid=$request->get('departmentid');
        $result=$adb->pquery($query,array($departmentid));
        $parentdepartmentRow=$adb->raw_query_result_rowdata($result,0);
        $parentdepartment=$parentdepartmentRow['parentdepartment'];
        $parentdepartment=explode('::',$parentdepartment);
        $parentdepartment=array_reverse($parentdepartment);
        $query='SELECT departmentid,roleid FROM `vtiger_departmentrelatrole`';
        $result=$adb->pquery($query,array());
        //include 'crmcache/role.php';
        $flag=false;
        if($adb->num_rows($result)){
            $roleids=array();
            while($row=$adb->fetch_array($result)){
                $roleids[$row['departmentid']]=$row['roleid'];
            }
            $roleidstr='';
            foreach($parentdepartment as $value){
                if(!empty($roleids[$value])){
                    $flag=true;
                    $roleidstr=$roleids[$value];
                    break;
                }
            }
            if($flag){
                $roleids=explode(',',$roleidstr);
                $returnArr=array();
                foreach($roleids as $value){
                    $returnArr[$value]=$value;
                }
            }
            unset($returnArr['H1']);
        }
        $where='';
        if(!empty($returnArr)){
            $where=" WHERE roleid in('".implode("','",$returnArr)."') ";
        }
        $query='SELECT * FROM vtiger_role '.$where.'ORDER BY jobcategory DESC';

        $result=$adb->pquery($query,array());
        $returnArr=array();
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $jobcategory=!empty($row['jobcategory'])?$row['jobcategory']:'Other';
                $returnArr[]=array('id'=>$row['roleid'],'name'=>$row['rolename'],'category'=>$jobcategory,'categoryname'=>vtranslate($jobcategory,'UserManger'));
            }
        }
        return $returnArr;
    }
    public static function getUsermangerInfo($recorid){
        global $adb;
        $result=$adb->pquery("SELECT ownornot FROM  `vtiger_usermanger`  WHERE `usermangerid` = ? ",array($recorid));
        $result=$adb->query_result_rowdata($result,0);
        return  $result['ownornot'];
    }
    /**
     * 更改密码
     * @param $request
     * @return array
     * @throws WebServiceException
     */
    public function changePassword($request){
        global $adb;
        $id=$this->get('userid');
        $newPassword = $request->get('new_password');
        $query = 'SELECT * FROM vtiger_users  WHERE id=?';
        $result = $adb->pquery($query, array($id));
        if ($adb->num_rows($result)) {
            $resultData = $adb->raw_query_result_rowdata($result, 0);
            $salt = mb_substr($resultData['user_name'], 0, 2, 'utf-8');
            if ($resultData['crypt_type'] == 'MD5') {
                $salt = '$1$' . $salt . '$';
            } elseif ($resultData['crypt_type'] == 'BLOWFISH') {
                $salt = '$2$' . $salt . '$';
            } elseif ($resultData['crypt_type'] == 'PHP5.3MD5') {
                $salt = '$1$' . str_pad($salt, 9, '0');
            }
            $encrypted_password = crypt($newPassword, $salt);
            $updateSql = "update vtiger_users set user_password=? where id=?";
            $adb->pquery($updateSql, array($encrypted_password, $id));
        }
        $userModel = vglobal('current_user');
        $is_admin=$userModel->is_admin;
        $oldPassword = '';
        $userModel->is_admin='on';
        $wsUserId = vtws_getWebserviceEntityId('Users', $this->get('userid'));
        $wsStatus = vtws_changePassword($wsUserId, $oldPassword, $newPassword, $newPassword, $userModel);
        $userModel->is_admin=$is_admin;
        return $wsStatus;
    }
    public function getAuditsettings($auditsettingtype="ServiceContracts") {
        $db=PearDatabase::getInstance();
        $sql = "SELECT auditsettingsid,auditsettingtype,
   vtiger_auditsettings.department,
   (SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.oneaudituid) AS oneaudituid,vtiger_auditsettings.oneaudituid as oneaudituidn, 
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.towaudituid ),'--') AS towaudituid, vtiger_auditsettings.towaudituid as towaudituidn,
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.audituid3 ),'--') AS audituid3,vtiger_auditsettings.audituid3 as audituid3n,
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.audituid4 ),'--') AS audituid4,
   vtiger_auditsettings.audituid5
   FROM vtiger_auditsettings WHERE auditsettingtype=? ORDER BY auditsettingsid DESC";
        //return $db->run_query_allrecords($sql,array($auditsettingtype));
        return $db->pquery($sql,array($auditsettingtype));
    }
    public function getInvoicecompany(){
        global $adb;
        $result=$adb->pquery('SELECT * FROM `vtiger_invoicecompany`',array());
        $data=array();
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $data[]=array('id'=>$row['companyid'],'cname'=>$row['invoicecompany']);
            }
        }
        return $data;
    }

    public function getEmployeelevel(){
        global $adb;
        $result=$adb->pquery('SELECT * FROM `vtiger_employeelevel`',array());
        $data=array();
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $data[]=array('id'=>$row['employeelevel'],'employeelevel'=>vtranslate($row['employeelevel'],'Users','zh_cn'));
            }
        }
        return $data;
    }

    public function getRole(){
        global $adb;
        $result=$adb->pquery('SELECT * FROM `vtiger_role`',array());
        $data=array();
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $data[]=array('roleid'=>$row['roleid'],'rolename'=>$row['rolename']);
            }
        }
        return $data;
    }
     /**
     * @param $length
     * @return bool|string
     * 生成随机数
     */
    public function getRandom($length){
        return substr(time(),$length*-1);
    }

    /**
     * @param $orignal
     * @return string
     * SHA-256编码;
     */
    public function SHA256Encrypt($orignal){
        return hash("sha256",$orignal);
    }

    /**
     * 云课堂消息队列消费者
     * @param $data
     * @return bool|string
     */
    public function sendYxtangByMessageQuery($data){
        $dataAction=$data['dataAction'];
        $yxTangURL=$this->yxtangURL();
        $sendUrl=$this->BASE_REQUEST_URL.$yxTangURL[$dataAction];
        $yxTangToke=$this->yxtangtoke();
        $yxTangData=array_merge($yxTangToke,$data['datas']);
        $headers[]='Content-Type: application/json';
        return $this->https_requestcomm2($sendUrl,$yxTangData,$headers,true);
    }
    public function yxtangURL(){
        return array(
            'deleteous'=>'v1/udp/sy/deleteous',//删除部门
            'ous'=>'v1/udp/sy/ous',//同步部门
            'updgrade'=>'v1/udp/sy/updgrade',//同步职级
            'position'=>'v1/udp/sy/position',//同步职位
            'users'=>'v1/udp/sy/users',//同步账号
            'disabledusers'=>'v1/udp/sy/disabledusers',//禁用账号
        );
    }
    public function yxtangtoke(){
        $salt=$this->getRandom(4);
        return array("apikey"=>$this->API_KEY,"salt"=>$salt,"signature"=>$this->SHA256Encrypt($this->SECRET_KEY.$salt));
    }

}