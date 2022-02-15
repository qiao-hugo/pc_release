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
 * Vtiger Entity Record Model Class
 */
class Sendmailer_Record_Model extends Vtiger_Record_Model {



	/**
	 * 获取客户信息
	 * @param Vtiger_Request $request
	 */
	public static function getAccountInfos(Vtiger_Request $request) {

		$departmentid = $request->get('departmentid');
        $inorout=$request->get('inorout');
		$parrm_array = Array();
		//获取客户信息
        $userid=getDepartmentUser($departmentid);
        if($inorout=='outer'){
            $sqlQuery="select vtiger_account.accountid,vtiger_account.accountname,vtiger_crmentity.smownerid,
			        (select departmentname from vtiger_departments where vtiger_departments.departmentid=(select vtiger_user2department.departmentid from vtiger_user2department where vtiger_user2department.userid=vtiger_crmentity.smownerid LIMIT 0,1)) as departmentname,
				    (select last_name from vtiger_users where id=vtiger_crmentity.smownerid) as smownername,
				    vtiger_account.email1,
				    vtiger_account.industry,
				    vtiger_account.businessarea,
				    vtiger_account.address,
				    1 AS email_flag,
				    1 AS readtimes,
				    1 AS reason,
				    1 AS lastreddatetime,
				    1 AS sendtime,
				    vtiger_account.accountrank
				    from vtiger_crmentity INNER JOIN vtiger_account on(vtiger_crmentity.crmid=vtiger_account.accountid and vtiger_crmentity.deleted=0)
				    where vtiger_account.emailoptout='0'";
            $sqlQuery1="select count(1) AS counts
				    from vtiger_crmentity INNER JOIN vtiger_account on(vtiger_crmentity.crmid=vtiger_account.accountid and vtiger_crmentity.deleted=0)
				    where vtiger_account.emailoptout='0'";
					//where vtiger_account.accountrank in('gold_isv','silv_isv','bras_isv','iron_isv')";//只获取金牌，银牌，铜牌客户  //2015-4-17 young 加入铁牌
            if($departmentid!='H1') {
                $userid = getDepartmentUser($departmentid);
                $sqlQuery .= ' and vtiger_crmentity.smownerid in (' . implode(',', $userid) . ')';
                $sqlQuery1.=' and vtiger_crmentity.smownerid in (' . implode(',', $userid) . ')';
            }
            $str=$request->get('search');
            if(!empty($str['value'])){
                $newstr=self::inject_check($str['value']);
                if($newstr!=''){
                    $sqlQuery.=" AND vtiger_account.accountname LIKE '%{$newstr}%'";
                    $sqlQuery1.=" AND vtiger_account.accountname LIKE '%{$newstr}%'";
                }
            }
            //echo $sqlQuery;
        }elseif($inorout=='inner'){
            $sqlQuery="SELECT
                            vtiger_users.user_name,
                            vtiger_users.last_name,
                            vtiger_user2role.roleid,
                            vtiger_users.email1,
                            vtiger_users.department,
                            vtiger_users.email2,
                            vtiger_user2department.departmentid,
                            vtiger_departments.departmentname,
                            1 AS email_flag,
                            1 AS readtimes,
                            1 AS reason,
                            1 AS lastreddatetime,
                            1 AS sendtime,
                            vtiger_users.id
                        FROM
                            vtiger_users
                        INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
                        INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
                        LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                        WHERE
                            vtiger_users.`status`='Active'";
            $sqlQuery1="SELECT
                            count(1) AS counts
                        FROM
                            vtiger_users
                        INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
                        INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
                        LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                        WHERE
                            vtiger_users.`status`='Active'";
            if($departmentid!='H1'){
                $userid=getDepartmentUser($departmentid);
                $sqlQuery.=' AND vtiger_users.id in ('.implode(',',$userid).')';
                $sqlQuery1.=' AND vtiger_users.id in ('.implode(',',$userid).')';
            }
            $str=$request->get('search');
            if(!empty($str['value'])){
                $newstr=self::inject_check($str['value']);
                if($newstr!=''){
                    $sqlQuery.=" AND vtiger_users.last_name LIKE '%{$newstr}%'";
                    $sqlQuery1.=" AND vtiger_users.last_name LIKE '%{$newstr}%'";
                }
            }

        }

        $sqlQuery.=" limit {$request->get('start')},{$request->get('length')}";
        return self::getresult($sqlQuery,$inorout,$parrm_array,$sqlQuery1,$request->get('draw'));
	}
    private function getresult($sqlQuery,$inorout,$parrm_array,$sqlQuery1,$draw=''){
        set_time_limit(0);
        $db=PearDatabase::getInstance();
        $user_array=array();
        //echo $sqlQuery1;
        $countsResult = $db->pquery($sqlQuery1, $parrm_array);
        $counts=$db->query_result($countsResult,0,'counts');
        $result = $db->pquery($sqlQuery, $parrm_array);
        //echo $counts;
        //echo $sqlQuery;
        if (empty($result) || $db->num_rows($result)<1){
            $user_array['aaData']=array();
            $user_array['recordsTotal']=$counts;
            $user_array['recordsFiltered']=$counts;
            $user_array['draw']=$draw;
            return $user_array;
        }
        $num_rows = $db->num_rows($result);

        if($inorout=='outer'){
            for($i=0; $i<$num_rows; $i++) {
                $user_array[$i][]=$db->query_result($result,$i,'accountname');
                $user_array[$i][]=vtranslate($db->query_result($result,$i,'industry'));
                $user_array[$i][]=vtranslate($db->query_result($result,$i,'accountrank'));
                $user_array[$i][]=vtranslate($db->query_result($result,$i,'businessarea'));
                $temp=explode('#',$db->query_result($result,$i,'address'));
                $user_array[$i][]=$temp[0].$temp[1].$temp[2].$temp[3];
                $user_array[$i][]=$db->query_result($result,$i,'departmentname');
                $user_array[$i][]=$db->query_result($result,$i,'smownername');
                $email=explode('@',$db->query_result($result,$i,'email1'));
                $user_array[$i][]=count($email)==2?$email[0].'@******':'';
                //$user_array[$i][]=$db->query_result($result,$i,'email1');
                $email_flag=$db->query_result($result,$i,'email_flag')=='read'?('已阅读,最后打开时间:'.$db->query_result($result,$i,'lastreddatetime').',查看次数:'.$db->query_result($result,$i,'readtimes')):($db->query_result($result,$i,'email_flag')=='send'?'已发送':($db->query_result($result,$i,'email_flag')=='fail'?'发送失败,'.$db->query_result($result,$i,'reason'):'未发送'));
                $user_array[$i][]=$email_flag;
                $user_array[$i][]=vtranslate($db->query_result($result,$i,'sendtime'));
                if(self::checkEmails(trim($db->query_result($result,$i,'email1')))){
                    $user_array[$i][]='<i class="icon-envelope nowsend" title="发送邮件" data-id="'.$db->query_result($result,$i,'accountid').'"></i>';
                }else{
                    $user_array[$i][]='';
                }

            }
        }elseif($inorout=='inner'){
            require_once 'crmcache/role.php';
            for($i=0; $i<$num_rows; $i++) {
                //$user_array[$i]['accountid']=$db->query_result($result,$i,'id');
                $user_array[$i][]=$db->query_result($result,$i,'last_name');

                $user_array[$i][]=$db->query_result($result,$i,'departmentname')==null?'':$db->query_result($result,$i,'departmentname');
                $user_array[$i][]=str_replace('|—','',$roles[$db->query_result($result,$i,'roleid')]);
                $user_array[$i][]=$db->query_result($result,$i,'email1');
                $email_flag=$db->query_result($result,$i,'email_flag')=='read'?'已阅读,最后打开时间:'.$db->query_result($result,$i,'lastreddatetime').',查看次数:'.$db->query_result($result,$i,'readtimes'):($db->query_result($result,$i,'email_flag')=='send'?'已发送':($db->query_result($result,$i,'email_flag')=='fail'?'发送失败,'.$db->query_result($result,$i,'reason'):'未发送'));
                $user_array[$i][]=$email_flag;
                //$user_array[$i][]=$db->query_result($result,$i,'email1');
                $user_array[$i][]=vtranslate($db->query_result($result,$i,'sendtime'));
                if(self::checkEmails(trim($db->query_result($result,$i,'email1')))){
                    $user_array[$i][]='<i class="icon-envelope nowsend" title="发送邮件" data-id="'.$db->query_result($result,$i,'id').'"></i>';
                }else{
                    $user_array[$i][]='';
                }
            }
        }
        $user_array['aaData']=$user_array;
        $user_array['iTotalRecords']=$counts;
        $user_array['iTotalDisplayRecords']=$counts;
        $user_array['draw']=$draw;
        //exit;
        return $user_array;


    }
/**
     * AJax请求发送邮件信息
     * @param $inorout
     * @return array
     */
    public static function getreviced($inorout){
        if($inorout->get('inorout')=='outer'){
            $sqlQuery="select vtiger_account.accountid,vtiger_account.accountname,vtiger_crmentity.smownerid,
			        (select departmentname from vtiger_departments where vtiger_departments.departmentid=(select vtiger_user2department.departmentid from vtiger_user2department where vtiger_user2department.userid=vtiger_crmentity.smownerid LIMIT 0,1)) as departmentname,
				    (select last_name from vtiger_users where id=vtiger_crmentity.smownerid) as smownername,
				    vtiger_account.email1,
				    vtiger_account.industry,
				    vtiger_account.businessarea,
				    vtiger_account.address,
				    vtiger_account.accountrank,
				    vtiger_mailaccount.email_flag,
				    vtiger_mailaccount.readtimes,
				    vtiger_mailaccount.reason,
				    vtiger_mailaccount.lastreddatetime,
				    vtiger_mailaccount.sendtime
				    from vtiger_crmentity INNER JOIN vtiger_account on(vtiger_crmentity.crmid=vtiger_account.accountid and vtiger_crmentity.deleted=0)
					INNER JOIN vtiger_mailaccount ON vtiger_account.accountid=vtiger_mailaccount.accountid
					where vtiger_mailaccount.sendmailid=?";

            $sqlQuery1="select count(1) AS  counts
				    from vtiger_crmentity INNER JOIN vtiger_account on(vtiger_crmentity.crmid=vtiger_account.accountid and vtiger_crmentity.deleted=0)
					INNER JOIN vtiger_mailaccount ON vtiger_account.accountid=vtiger_mailaccount.accountid
					where vtiger_mailaccount.sendmailid=?";
            $str=$inorout->get('search');
            if(!empty($str['value'])){
                $newstr=self::inject_check($str['value']);
                if($newstr!=''){
                    $sqlQuery.=" AND vtiger_account.accountname LIKE '%{$newstr}%'";
                    $sqlQuery1.=" AND vtiger_account.accountname LIKE '%{$newstr}%'";
                }
            }

        }elseif($inorout->get('inorout')=='inner'){
            $sqlQuery="SELECT
                            vtiger_users.user_name,
                            vtiger_users.last_name,
                            vtiger_user2role.roleid,
                            vtiger_users.email1,
                            vtiger_users.department,
                            vtiger_users.email2,
                            vtiger_user2department.departmentid,
                            vtiger_departments.departmentname,
                            vtiger_mailaccount.email_flag,
                            vtiger_mailaccount.readtimes,
                            vtiger_mailaccount.reason,
                            vtiger_mailaccount.lastreddatetime,
                            vtiger_mailaccount.sendtime,
                            vtiger_users.id
                        FROM
                            vtiger_users
                        INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
                        INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
                        LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                         INNER JOIN vtiger_mailaccount ON vtiger_users.id=vtiger_mailaccount.accountid
					    WHERE vtiger_mailaccount.sendmailid=?";
            $sqlQuery1="SELECT
                            count(1) AS counts
                        FROM
                            vtiger_users
                        INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
                        INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
                        LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                         INNER JOIN vtiger_mailaccount ON vtiger_users.id=vtiger_mailaccount.accountid
					    WHERE vtiger_mailaccount.sendmailid=?";
            $str=$inorout->get('search');
            if(!empty($str['value'])){
                $newstr=self::inject_check($str['value']);
                if($newstr!=''){
                    $sqlQuery.=" AND vtiger_users.last_name LIKE '%{$newstr}%'";
                    $sqlQuery1.=" AND vtiger_users.last_name LIKE '%{$newstr}%'";
                }
            }

        }
        $sqlQuery.=" limit {$inorout->get('start')},{$inorout->get('length')}";
        return self::getresult($sqlQuery,$inorout->get('inorout'),array($inorout->get('recordid')),$sqlQuery1,$inorout->get('draw'));

    }
/**
     * 取得邮件发送的列表
     * @param $inorout
     * @return array
     */
    public static function getrevicesend($inorout){
        set_time_limit(0);

        if($inorout['inorout']=='outer'){
            $sqlQuery="select vtiger_account.accountid,vtiger_account.accountname,vtiger_crmentity.smownerid,
			        (select departmentname from vtiger_departments where vtiger_departments.departmentid=(select vtiger_user2department.departmentid from vtiger_user2department where vtiger_user2department.userid=vtiger_crmentity.smownerid LIMIT 0,1)) as departmentname,
				    (select last_name from vtiger_users where id=vtiger_crmentity.smownerid) as smownername,
				    vtiger_account.email1,
				    vtiger_account.industry,
				    vtiger_account.businessarea,
				    vtiger_account.address,
				    vtiger_account.accountrank,
				    vtiger_mailaccount.email_flag,
				    vtiger_mailaccount.readtimes,
				    vtiger_mailaccount.reason,
				    vtiger_mailaccount.lastreddatetime,
				    vtiger_mailaccount.sendtime
				    from vtiger_crmentity INNER JOIN vtiger_account on(vtiger_crmentity.crmid=vtiger_account.accountid and vtiger_crmentity.deleted=0)
					INNER JOIN vtiger_mailaccount ON vtiger_account.accountid=vtiger_mailaccount.accountid
					where vtiger_mailaccount.sendmailid=?";

        }elseif($inorout['inorout']=='inner'){
            $sqlQuery="SELECT
                            vtiger_users.user_name,
                            vtiger_users.last_name,
                            vtiger_user2role.roleid,
                            vtiger_users.email1,
                            vtiger_users.department,
                            vtiger_users.email2,
                            vtiger_user2department.departmentid,
                            vtiger_departments.departmentname,
                            vtiger_mailaccount.email_flag,
                            vtiger_mailaccount.readtimes,
                            vtiger_mailaccount.reason,
                            vtiger_mailaccount.lastreddatetime,
                            vtiger_mailaccount.sendtime,
                            vtiger_users.id
                        FROM
                            vtiger_users
                        INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
                        INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
                        LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                         INNER JOIN vtiger_mailaccount ON vtiger_users.id=vtiger_mailaccount.accountid
					    WHERE vtiger_mailaccount.sendmailid=?";

        }

        $db=PearDatabase::getInstance();

        $result = $db->pquery($sqlQuery, array($inorout['record_id']));

        $num_rows = $db->num_rows($result);
        $user_array=array();
        if($num_rows>0) {
            if ($inorout['inorout']=='outer') {
                for ($i = 0; $i < $num_rows; $i++) {
                    $user_array[$i]['accountname'] = $db->query_result($result, $i, 'accountname');
                    $user_array[$i]['industry'] = vtranslate($db->query_result($result, $i, 'industry'));
                    $user_array[$i]['accountrank'] = vtranslate($db->query_result($result, $i, 'accountrank'));
                    $user_array[$i]['businessarea'] = vtranslate($db->query_result($result, $i, 'businessarea'));
                    $temp = explode('#', $db->query_result($result, $i, 'address'));
                    $user_array[$i][] = $temp[0] . $temp[1] . $temp[2] . $temp[3];
                    $user_array[$i]['departmentname'] = $db->query_result($result, $i, 'departmentname');
                    $user_array[$i]['smownername'] = $db->query_result($result, $i, 'smownername');
                    $email = explode('@', $db->query_result($result, $i, 'email1'));
                    $user_array[$i][] = count($email) == 2 ? $email[0] . '@******' : '';
                    //$user_array[$i][]=$db->query_result($result,$i,'email1');
                    $email_flag = $db->query_result($result, $i, 'email_flag') == 'read' ? ('已阅读,最后打开时间:' . $db->query_result($result, $i, 'lastreddatetime') . ',查看次数:' . $db->query_result($result, $i, 'readtimes')) : ($db->query_result($result, $i, 'email_flag') == 'send' ? '已发送' : ($db->query_result($result, $i, 'email_flag') == 'fail' ? '发送失败,' . $db->query_result($result, $i, 'reason') : '未发送'));
                    $user_array[$i]['email_flag'] = $email_flag;
                    $user_array[$i]['sendtime'] = vtranslate($db->query_result($result, $i, 'sendtime'));
                    $user_array[$i]['flag']=$db->query_result($result, $i, 'email_flag') == 'read' ? 'send' : ($db->query_result($result, $i, 'email_flag') == 'send' ? 'send' : 'fail' );
                    if (self::checkEmails(trim($db->query_result($result, $i, 'email1')))) {
                        $user_array[$i]['nowsend'] = '<i class="icon-envelope nowsend" title="发送邮件" data-id="' . $db->query_result($result, $i, 'accountid') . '"></i>';
                    } else {
                        $user_array[$i]['nowsend'] = '<i class="icon-envelope nowsend" title="发送邮件" data-id="' . $db->query_result($result, $i, 'accountid') . '"></i>';
                    }

                }
            } elseif ($inorout['inorout']=='inner') {
                require_once 'crmcache/role.php';
                for ($i = 0; $i < $num_rows; $i++) {
                    //$user_array[$i]['accountid']=$db->query_result($result,$i,'id');
                    $user_array[$i]['accountname'] = $db->query_result($result, $i, 'last_name');

                    $user_array[$i]['departmentname'] = $db->query_result($result, $i, 'departmentname') == null ? '' : $db->query_result($result, $i, 'departmentname');
                    $user_array[$i]['roleid'] = str_replace('|—', '', $roles[$db->query_result($result, $i, 'roleid')]);
                    $user_array[$i]['email1'] = $db->query_result($result, $i, 'email1');
                    $email_flag = $db->query_result($result, $i, 'email_flag') == 'read' ? '已阅读,最后打开时间:' . $db->query_result($result, $i, 'lastreddatetime') . ',查看次数:' . $db->query_result($result, $i, 'readtimes') : ($db->query_result($result, $i, 'email_flag') == 'send' ? '已发送' : ($db->query_result($result, $i, 'email_flag') == 'fail' ? '发送失败,' . $db->query_result($result, $i, 'reason') : '未发送'));
                    $user_array[$i]['email_flag'] = $email_flag;
                    $user_array[$i]['flag']=$db->query_result($result, $i, 'email_flag') == 'read' ? 'send' : ($db->query_result($result, $i, 'email_flag') == 'send' ? 'send' : 'fail' );
                    $user_array[$i]['sendtime'] = vtranslate($db->query_result($result, $i, 'sendtime'));
                    if (self::checkEmails(trim($db->query_result($result, $i, 'email1')))) {
                        $user_array[$i]['nowsend'] = '<i class="icon-envelope nowsend" title="发送邮件" data-id="' . $db->query_result($result, $i, 'id') . '"></i>';
                    } else {
                        $user_array[$i]['nowsend'] = '<i class="icon-envelope nowsend" title="发送邮件" data-id="' . $db->query_result($result, $i, 'id') . '"></i>';
                    }
                }
            }
        }

        return $user_array;

    }
    static  public function getcounts($arr){
        $db=PearDatabase::getInstance();
        $query="SELECT count(vtiger_mailaccount.mailaccountid) as counts
		,sum(IF((email_flag='send' or email_flag='read'),1,0)) as send
		,sum(IF(email_flag='read',1,0)) as reader
		,sum(readtimes) as readtimes
		FROM vtiger_mailaccount
		WHERE  vtiger_mailaccount.sendmailid=?";
        $result=$db->pquery($query,array($arr['record_id']));
        return $db->query_result_rowdata($result);
    }
    private function inject_check($str){
        return preg_replace('/select|insert|and|or|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|;/','',$str);
    }
    public function checkEmails($str){
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        //$regex = '/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }

}
