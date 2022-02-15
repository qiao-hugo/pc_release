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
class ModComments_Record_Model extends Vtiger_Record_Model {

	/**
	 * Functions gets the comment id
	 */
	public function getId() {
		//TODO : check why is modcommentsid is not set 2014-10-29 young 已经解决
		$id = $this->get('id');
		if(empty($id)) {
			return $this->get('modcommentsid');
		}
		return $this->get('modcommentsid');
	}

	public function setId($id) {
		return $this->set('id', $id);
	}
	/**
	 * 获取跟进评论
	 * @return Value|string
	 */
	public function getHistory(){
		return $this->historyrecord;
	}
	public function setHistory($record){
		$this->historyrecord=$record;
	}
	public function getAlerts(){
		return $this->jobalerts;
	}
	public function setAlerts($jobalerts){
		$this->jobalerts=$jobalerts;
	}
	
	static public function getModcommentmode(){
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT vtiger_modcommentmode.* FROM vtiger_modcommentmode', array());
		$arr=array();
		if($db->num_rows($result)) {
			for($i=0;$i<$db->num_rows($result);$i++){
			$row = $db->query_result_rowdata($result, $i);
			$arr[]=$row['modcommentmode'];
			}
		}
 		return $arr;
	}
	static public function getModcommenttype(){
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT * FROM vtiger_modcommenttype', array());
		$arr=array();
		if($db->num_rows($result)) {
			for($i=0;$i<$db->num_rows($result);$i++){
			$row = $db->query_result_rowdata($result, $i);
			$arr[]=$row['modcommenttype'];
			}
		}
 		return $arr;
	}
	
	/**
	 * 获取联系人
	 * @return multitype:unknown
	 */
	static public function getModcommentContacts($parentId){
		$db = PearDatabase::getInstance();
		$arr=array();
		//young.yang
		$result=$db->pquery('select accountid,linkname from vtiger_account where accountid=? limit 1',array($parentId));
		if($db->num_rows($result)) {
			$row = $db->query_result_rowdata($result, 0);
			if(!empty($row['linkname'])){
				$tmp['contactid']=$row['accountid'];
				$tmp['name']=$row['linkname'];
				
				$arr[]=$tmp;
			}
		}
		
		$result = $db->pquery('SELECT contactid,name FROM vtiger_contactdetails where accountid=? and not isnull(name)', array($parentId));
		
		if($db->num_rows($result)) {
			for($i=0;$i<$db->num_rows($result);$i++){
				$row = $db->query_result_rowdata($result, $i);
				$tmp['contactid']=$row['contactid'];
				$tmp['name']=$row['name'];
				$arr[]=$tmp;
			}
		}
		
		
		return $arr;
	}
	
	/**
	 * 获取提醒信息
	 * @param unknown $parentIds
	 */
	static public function getAlertModcomments($parentIds){
		$db = PearDatabase::getInstance();
	
		$query="SELECT 
				vtiger_jobalerts.moduleid AS modcommentsid,
				(select GROUP_CONCAT(last_name) from vtiger_users where FIND_IN_SET(vtiger_users.id,REPLACE(vtiger_jobalerts.alertid,' |##| ',','))>0) as username,
				vtiger_jobalerts.*
				FROM vtiger_jobalerts where vtiger_jobalerts.moduleid in($parentIds) and vtiger_jobalerts.modulename='ModComments' 
				order by vtiger_jobalerts.jobalertsid desc";
		
		$result = $db->pquery($query, array());
		$arr=array();
		if($db->num_rows($result)) {
			while ($row=$db->fetch_array($result)){
				$arr[$row['modcommentsid']][]=$row;
			}
		}
		return $arr;
	}
	
	/**
	 * 获取子类
	 * @param unknown $parentIds
	 */
	static public function getSubModcomments($parentIds,$isTrans=false){
		$db = PearDatabase::getInstance();
		
		//echo 'SELECT vtiger_submodcomments.modcommentsid,a.first_name AS createdby, b.first_name AS modifiedby, modcommenthistory, vtiger_submodcomments.createdtime, vtiger_submodcomments.modifiedtime, vtiger_submodcomments.modifiedcause FROM `vtiger_submodcomments` LEFT JOIN vtiger_users AS a ON vtiger_submodcomments.creatorid = a.id LEFT JOIN vtiger_users AS b ON vtiger_submodcomments.modifiedtime = b.id WHERE modcommentsid ='.$parentIds;die();
		$result = $db->pquery("SELECT vtiger_submodcomments.id,vtiger_submodcomments.modcommentsid,vtiger_submodcomments.creatorid,b.picturepath,vtiger_submodcomments.modifiedby,(select last_name from vtiger_users where vtiger_submodcomments.creatorid = vtiger_users.id ) AS createdbyer, (select last_name from vtiger_users where vtiger_submodcomments.creatorid = vtiger_users.id  ) AS modifiedbyer, modcommenthistory, vtiger_submodcomments.createdtime, vtiger_submodcomments.modifiedtime, vtiger_submodcomments.modifiedcause,vtiger_submodcomments.accountintentionality FROM `vtiger_submodcomments` LEFT JOIN vtiger_users AS a ON vtiger_submodcomments.creatorid = a.id LEFT JOIN vtiger_wexinpicture AS b ON vtiger_submodcomments.creatorid = b.userid WHERE modcommentsid in($parentIds) order by vtiger_submodcomments.id desc", array());
		$arr=array();
		if($db->num_rows($result)) {
			while ($row=$db->fetch_array($result)){
			    if(!$isTrans){
                    $row['accountintentionality'] = vtranslate($row['accountintentionality'],'Accounts','zh_cn');
                }
                $arr[$row['modcommentsid']][]=$row;
            }
		}
		
		return $arr;
	}
	
	/**
	 * 返回子评论根据id
	 * @param unknown $id
	 * @return Ambigous <multitype:, unknown, s, --, string, mixed>
	 */
	static public function getSubModcommentsById($id){
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT vtiger_submodcomments.modcommentsid,vtiger_submodcomments.creatorid,vtiger_submodcomments.modifiedby,a.first_name AS createdbyer, b.first_name AS modifiedbyer, modcommenthistory, vtiger_submodcomments.createdtime, vtiger_submodcomments.modifiedtime, vtiger_submodcomments.modifiedcause FROM `vtiger_submodcomments` LEFT JOIN vtiger_users AS a ON vtiger_submodcomments.creatorid = a.id LEFT JOIN vtiger_users AS b ON vtiger_submodcomments.modifiedtime = b.id WHERE vtiger_submodcomments.id=? limit 1', array($id));
		$arr=array();
		if($db->num_rows($result)) {
			$arr=$db->query_result_rowdata($result,0);
		}
		return $arr;
	}
	/**
	 * Function returns url to get child comments
	 * @return <String> - url
	 */
	public function getChildCommentsUrl() {
		return $this->getDetailViewUrl().'&mode=showChildComments';
	}
	
	public function getImagePath() {
		$commentor = $this->getCommentedByModel();
		if($commentor) {
			$customer = $this->get('customer');
			if (!empty($customer)) {
				return 'CustomerPortal.png';
			} else {
				$imagePath = $commentor->getImageDetails();
				if (!empty($imagePath[0]['name'])) {
					return '../' . $imagePath[0]['path'] . '_' . $imagePath[0]['name'];
				}
			}
		}
		return false;
	}
	
	/**
	 * Function to create an instance of ModComment_Record_Model
	 * @param <Integer> $record
	 * @return ModComment_Record_Model
	 */
	public static function getInstanceById($record,$module = NULL,$flag = false) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT vtiger_modcomments.* FROM vtiger_modcomments 
					WHERE modcommentsid = ? ', array($record));
		if($db->num_rows($result)) {
			$row = $db->query_result_rowdata($result, $i);
			$self = new self();
			$self->setData($row);
			return $self;
		}
		return false;
	}

	/**
	 * 返回父类信息
	 * @history
	 * 	2014-10-27 young 返回false
	 * @return boolean|Ambigous <ModComment_Record_Model, boolean, ModComments_Record_Model>
	 */
	public function getParentCommentModel() {
		return false;
		$recordId = $this->get('parent_comments');
		if(!empty($recordId))
			return ModComments_Record_Model::getInstanceById($recordId, 'ModComments');

		return false;
	}

	/**
	 * Function returns the parent Record Model(Contacts, Accounts etc)
	 * @return <Vtiger_Record_Model>
	 */
	public function getParentRecordModel() {
		$parentRecordId = $this->get('related_to');
		if(!empty($parentRecordId))
		return Vtiger_Record_Model::getInstanceById($parentRecordId);

		return false;
	}

	/**
	 * Function returns the commentor Model (Users Model)
	 * @return <Vtiger_Record_Model>
	 */
	public function getCommentedByModel() {
		$customer = $this->get('customer');
		if(!empty($customer)) {
			return Vtiger_Record_Model::getInstanceById($customer, 'Contacts');
		} else {  
			$commentedBy = $this->get('creatorid');
			if($commentedBy)
			return Vtiger_Record_Model::getInstanceById($commentedBy, 'Users');
		}
		return false;
	}

	/**
	 * Function returns the commented time
	 * @return <String>
	 */
	public function getCommentedTime() {
		$commentTime = $this->get('addtime');
		return $commentTime;
	}

	/**
	 * Function returns the commented time
	 * @return <String>
	 */
	public function getModifiedTime() {
		$commentTime = $this->get('modifiedtime');
		return $commentTime;
	}
	/**
	 * Function returns latest comments for parent record
	 * @param <Integer> $parentRecordId - parent record for which latest comment need to retrieved
	 * @param <Vtiger_Paging_Model> - paging model
	 * @return ModComments_Record_Model if exits or null
	 */
	public static function getRecentComments($parentRecordId, $pagingModel,$moduleName='',$is_array = 0){
		$db = PearDatabase::getInstance();
		$startIndex = $pagingModel->getStartIndex();
		$limit = $pagingModel->getPageLimit();
		
		/* $listView = Vtiger_ListView_Model::getInstance('ModComments');
		$queryGenerator = $listView->get('query_generator');
		$queryGenerator->setFields(array('commentcontent', 'addtime', 'related_to', 'creatorid',
									'modcommenttype', 'modcommentmode', 'modcommenthistory','modcommentpurpose'));

		$query = $queryGenerator->getQuery(); */
		
		$query = "SELECT vtiger_modcomments.commentreturnplanid,vtiger_modcomments.commentcontent, vtiger_modcomments.addtime,
				vtiger_modcomments.related_to, vtiger_modcomments.creatorid, vtiger_modcomments.modcommenttype, 
				vtiger_modcomments.modcommentmode, vtiger_modcomments.modcommenthistory, vtiger_modcomments.modcommentpurpose,
				vtiger_modcomments.modcommentsid,vtiger_modcomments.contact_id,IFNULL((select name from vtiger_contactdetails where contactid=vtiger_modcomments.contact_id),IFNULL((select linkname from vtiger_account where accountid=vtiger_modcomments.related_to ),'-')) as lastname,
				IFNULL((select linkname from vtiger_account where accountid=vtiger_modcomments.related_to ),'-') as shouyao ,
                vtiger_modcomments.accountintentionality,followrole
				FROM vtiger_modcomments WHERE  ";
		
		//客户判断
		if($moduleName == 'Accounts') {
		    $accountRecordModel = Accounts_Record_Model::getInstanceById($parentRecordId,"Accounts");
		    $smownerdepartment = $accountRecordModel->getSmownerDepartmentId($parentRecordId);
            $userRecordModel = Users_Record_Model::getCleanInstance("Users");
            $departmentid = $smownerdepartment['departmentid'];
            $smownerid = $smownerdepartment['smownerid'];
            if($userRecordModel->isChannelUser($departmentid)){
                $accountMoudle = Accounts_Module_Model::getCleanInstance("Accounts");
                $shareAccountUserIds = $accountMoudle->getShareAccountUserIds($parentRecordId);
                global $current_user;
                if($current_user->id==$smownerid){
                    $query = $query . "  vtiger_modcomments.related_to = ? ";
                    if(!empty($shareAccountUserIds)){
                        $query .=" and vtiger_modcomments.creatorid not in  (".implode(",",$shareAccountUserIds).")";
                    }
                    $query .= " ORDER BY modcommentsid DESC LIMIT $startIndex, $limit";

                }elseif(in_array($current_user->id,$shareAccountUserIds)){
                    $query = $query . "  vtiger_modcomments.related_to = ? and vtiger_modcomments.creatorid=".$current_user->id." 
                     ORDER BY modcommentsid DESC LIMIT $startIndex, $limit";
                }else{
                    $users = array_keys($userRecordModel->getAccessibleUsers());
                    array_push($users,$current_user->id);
                    $query = $query . "  vtiger_modcomments.related_to = ? and vtiger_modcomments.creatorid in 
                        (".implode(",",$users).")  ORDER BY modcommentsid DESC LIMIT $startIndex, $limit";
                }

            }else{
                $query = $query . "  vtiger_modcomments.related_to = ?  ORDER BY modcommentsid DESC LIMIT $startIndex, $limit";
            }

        }elseif($moduleName=='SalesDaily'){
            $query = $query ."  vtiger_modcomments.moduleid = ? and vtiger_modcomments.modulename='".$moduleName."'  ORDER BY modcommentsid DESC
			LIMIT $startIndex, $limit";
		}else{
			$query = $query ."  vtiger_modcomments.moduleid = ?  ORDER BY modcommentsid DESC
			LIMIT $startIndex, $limit";
		}
		
		$result = $db->pquery($query, array($parentRecordId));
		$rows = $db->num_rows($result);
		
		
		$recordIds='';
		for ($i=0; $i<$rows; $i++) {
			if($i==0){
				$recordIds=$db->query_result($result, $i,'modcommentsid');
			}else{
				$recordIds=$recordIds.','.$db->query_result($result, $i,'modcommentsid');
			}
		}
		
		//跟进提醒修改 2014-12-22/gaocl start
		//获取跟进提醒数据
		$alertModcomments=ModComments_Record_Model::getAlertModcomments($recordIds);
		//跟进提醒修改 2014-12-22/gaocl end
		
		//批量获取评论，提醒数据
		$subcomments=ModComments_Record_Model::getSubModcomments($recordIds);
		//print_r($alertModcomments);die();
		//加入
                $row_data = array();
                if($is_array == 1){
                    for ($i=0; $i<$rows; $i++) {
                            $row = $db->query_result_rowdata($result, $i);
                            if(!empty($row['creatorid'])){
                                
                            }
                             
                            $row_data['valueMap'] = $row;
                            $row_data['historyrecord'] = (empty($alertModcomments[$row['modcommentsid']])?array():$alertModcomments[$row['modcommentsid']]);
                            $row_data['jobalerts'] =  empty($subcomments[$row['modcommentsid']])?array():$subcomments[$row['modcommentsid']];
                            
                            $recordInstances[] = $row_data;
                    }
                }else{
                    for ($i=0; $i<$rows; $i++) {
                            $row = $db->query_result_rowdata($result, $i);
                            $recordInstance = new self();

                            $recordInstance->setData($row);
                            //跟进提醒修改 2014-12-22/gaocl start
                            $recordInstance->setAlerts(empty($alertModcomments[$row['modcommentsid']])?array():$alertModcomments[$row['modcommentsid']]);

                            //跟进提醒修改 2014-12-22/gaocl end
                            $recordInstance->setHistory(empty($subcomments[$row['modcommentsid']])?array():$subcomments[$row['modcommentsid']]);
                            $recordInstances[] = $recordInstance;
                    }
                }
//                print_r($recordInstance);exit;
		return $recordInstances;
	}

	/**
	 * Function returns all the parent comments model
	 * @param <Integer> $parentId
	 * @return ModComments_Record_Model(s)
	 */
	public static function getAllParentComments($parentId) {
		$db = PearDatabase::getInstance();

		$listView = Vtiger_ListView_Model::getInstance('ModComments');
		$queryGenerator = $listView->get('query_generator');
		$queryGenerator->setFields(array('commentcontent', 'addtime', 'related_to', 'creatorid',
									'modcommenttype', 'modcommentmode', 'modcommenthistory','modcommentsid'));
		$query = $queryGenerator->getQuery();
		//客户判断
		if($moduleName == 'Accounts'){
			$query = $query ." AND related_to = ?  ORDER BY addtime DESC
			LIMIT $startIndex, $limit";
		}else{
			$query = $query ." AND moduleid = ?  ORDER BY addtime DESC
			LIMIT $startIndex, $limit";
		}
		//Condition are directly added as query_generator transforms the
		//reference field and searches their entity names
		$query = $query ."  ORDER BY addtime DESC";
		//echo $query;die();
		$result = $db->pquery($query, array());
		$rows = $db->num_rows($result);
		
		
		
		for ($i=0; $i<$rows; $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$recordInstance = new self();
			$recordInstance->setData($row);
			//$recordInstance->setHistory(empty($subcomments[$row['modcommentsid']])?array():$subcomments[$row['modcommentsid']]);
			$recordInstances[] = $recordInstance;
		}
		
		
		return $recordInstances;
	}

	

	/**
	 * Function to get details for user have the permissions to do actions
	 * @return <Boolean> - true/false
	 */
	public function isEditable() {
		return false;
	}

	/**
	 * Function to get details for user have the permissions to do actions
	 * @return <Boolean> - true/false
	 */
	public function isDeletable() {
		return false;
	}

    /**
     * 跟进内容模板替换
     * @param $modcommenttype
     * @param $commentcontent
     * @return mixed
     */
	public function getFollowUp($modcommenttype,$commentcontent){
	    if('首次客户录入系统跟进'==$modcommenttype){
	        if(strpos($commentcontent,'9*#*是#endl') !==false){
                return str_replace(
                    array('&nbsp;1*#*','#endl#2*#*','#endl#3*#*','#endl#4*#*','#endl#5*#*','#endl#6*#*','#endl#7*#*','#endl#8*#*','#endl#9*#*','#endl#10*#*','#endl#11*#*','#endl#12*#*','#endl#13*#*','#endl#14*#*','#endl#15*#*'),
                    array(
                        '<b>1.客户资料来源&nbsp;:</b>',
                        '<br><b>2.客户语气态度&nbsp;:</b>',
                        '<br><b>3.是否了解过珍岛&nbsp;:</b>',
                        '<br><b>4.客户质量&nbsp;:<br>&nbsp; &nbsp;&nbsp;①注册时间&nbsp;: &nbsp;</b>',
                        '<b>&nbsp; &nbsp;&nbsp;注册资金&nbsp;: &nbsp;</b>',
                        '<b>&nbsp; &nbsp;&nbsp;法人还是股东&nbsp;: &nbsp;</b>',
                        '<br><b>&nbsp; &nbsp;&nbsp;②意向点&nbsp;:&nbsp;</b>',
                        '<br><b>&nbsp; &nbsp;&nbsp;③客户行业和产品&nbsp;: &nbsp;</b>',
                        '<br><b>5.邀约是否成功&nbsp;:</b>',
                        '<br><b>&nbsp; &nbsp;&nbsp;邀约人物&nbsp;:&nbsp;</b>',
                        '<br><b>&nbsp; &nbsp;&nbsp;邀约时间&nbsp;:&nbsp;</b>',
                        '<br><b>&nbsp; &nbsp;&nbsp;邀约地点&nbsp;:&nbsp;</b>',
                        '<br><b>&nbsp; &nbsp;&nbsp;所谈业务&nbsp;:&nbsp;</b>',
                    ),'&nbsp;'.$commentcontent
                );
            }elseif (strpos($commentcontent,'9*#*否#endl') !==false){
                return str_replace(
                    array('&nbsp;1*#*','#endl#2*#*','#endl#3*#*','#endl#4*#*','#endl#5*#*','#endl#6*#*','#endl#7*#*','#endl#8*#*','#endl#9*#*','#endl#10*#*#endl#11*#*#endl#12*#*#endl#13*#*','#endl#14*#*','#endl#15*#*'),
                    array(
                        '<b>1.客户资料来源&nbsp;:</b>',
                        '<br><b>2.客户语气态度&nbsp;:</b>',
                        '<br><b>3.是否了解过珍岛&nbsp;:</b>',
                        '<br><b>4.客户质量&nbsp;:<br>&nbsp; &nbsp;&nbsp;①注册时间&nbsp;: &nbsp;</b>',
                        '<b>&nbsp; &nbsp;&nbsp;注册资金&nbsp;: &nbsp;</b>',
                        '<b>&nbsp; &nbsp;&nbsp;法人还是股东&nbsp;: &nbsp;</b>',
                        '<br><b>&nbsp; &nbsp;&nbsp;②意向点&nbsp;:&nbsp;</b>',
                        '<br><b>&nbsp; &nbsp;&nbsp;③客户行业和产品&nbsp;: &nbsp;</b>',
                        '<br><b>5.邀约是否成功&nbsp;:</b>',
                        '',
                        '<br><b>&nbsp; &nbsp;&nbsp;本次未能邀约见面的原因&nbsp;:&nbsp;</b>',
                        '<br><b>&nbsp; &nbsp;&nbsp;预约下次电话的时间&nbsp;:&nbsp;</b>',
                    ),'&nbsp;'.$commentcontent
                );
            }
            return str_replace(
                array('&nbsp;1*#*','#endl#2*#*','#endl#3*#*','#endl#4*#*','#endl#5*#*','#endl#6*#*','#endl#7*#*','#endl#8*#*','#endl#9*#*','#endl#10*#*'),
                array(
                    '<b>1.客户资料来源&nbsp;:</b>',
                    '<br><b>2.客户语气态度&nbsp;:</b>',
                    '<br><b>3.是否了解过珍岛&nbsp;:</b>',
                    '<br><b>4.客户质量&nbsp;:<br>&nbsp; &nbsp;&nbsp;①注册时间&nbsp;: &nbsp;</b>',
                    '<b>&nbsp; &nbsp;&nbsp;注册资金&nbsp;: &nbsp;</b>',
                    '<b>&nbsp; &nbsp;&nbsp;法人还是股东&nbsp;: &nbsp;</b>',
                    '<br><b>&nbsp; &nbsp;&nbsp;②意向点&nbsp;:&nbsp;</b>',
                    '<br><b>&nbsp; &nbsp;&nbsp;③客户行业和产品&nbsp;: &nbsp;</b>',
                    '<br><b>5.本次未能邀约见面的原因&nbsp;:</b>',
                    '<br><b>6.预约下次电话的时间&nbsp;:</b> '
                    ),'&nbsp;'.$commentcontent
            );
        }else if('首次拜访客户后跟进'==$modcommenttype){
            return str_replace(
                array('&nbsp;1*#*','#endl#2*#*','#endl#3*#*','#endl#4*#*','#endl#5*#*','#endl#6*#*','#endl#7*#*','#endl#8*#*','#endl#9*#*','#endl#10*#*','#endl#11*#*'),
                array(
                    '<b>1.公司规模，公司大概多少人&nbsp;:&nbsp;</b>',
                    '<br><b>2.拜访的负责人/老板&nbsp;:&nbsp;</b>',
                    '<br><b>3.拜访人性格描述&nbsp;:&nbsp;</b>',
                    '<br><b>4.拜访人年龄阶段/老家哪里&nbsp;:&nbsp;</b>',
                    '<br><b>5.客户目前的网络现状&nbsp;:&nbsp;</b>',
                    '<br><b>6.客户问了哪些问题&nbsp;:&nbsp;</b>',
                    '<br><b>7.整个面谈过程中，客户对那几个点比较感兴趣&nbsp;:&nbsp;</b>',
                    '<br><b>8.关于我们谈判中给到客户方的信息&nbsp;:&nbsp;</b><br><b>&nbsp; &nbsp;&nbsp;①谈的什么产品/版本/年限&nbsp;:&nbsp;</b>',
                    '<br><b>&nbsp; &nbsp;&nbsp;②给客户报价多少，是否提到优惠，如果提到了，提到的优惠是什么&nbsp;:&nbsp;</b>',
                    '<br><b>&nbsp; &nbsp;&nbsp;③当时给客户介绍的案例是哪些&nbsp;:&nbsp;</b>',
                    '<br><b>&nbsp; &nbsp;&nbsp;④客户没有当场签单的原因&nbsp;:&nbsp;</b>',
                ),'&nbsp;'.$commentcontent
            );
        }
    }
    
        static function getAccountIntentionality($isTrans=false){
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT * FROM vtiger_intentionality order by sortorderid asc', array());
        $arr=array();
        if($db->num_rows($result)) {
            for($i=0;$i<$db->num_rows($result);$i++){
                $row = $db->query_result_rowdata($result, $i);
                if(!$isTrans){
                    $arr[]=$row['intentionality'];
                }else{
                    $arr[$row['intentionality']]= vtranslate($row['intentionality'],'ModComments','zh_cn');
                }
            }
        }
        return $arr;
    }

    /**
     * 获取联系人
     * @return multitype:unknown
     */
    static public function getAccountContacts(Vtiger_Request $request){
        $parentId = $request->get("accountid");
        $db = PearDatabase::getInstance();
        $arr=array();
        //young.yang
        $result=$db->pquery('select accountid,linkname from vtiger_account where accountid=? limit 1',array($parentId));
        if($db->num_rows($result)) {
            $row = $db->query_result_rowdata($result, 0);
            if(!empty($row['linkname'])){
                $tmp['key']=$row['accountid'];
                $tmp['value']=$row['linkname'];

                $arr[]=$tmp;
            }
        }

        $result = $db->pquery('SELECT contactid,name FROM vtiger_contactdetails where accountid=? and not isnull(name)', array($parentId));

        if($db->num_rows($result)) {
            for($i=0;$i<$db->num_rows($result);$i++){
                $row = $db->query_result_rowdata($result, $i);
                $tmp['key']=$row['contactid'];
                $tmp['value']=$row['name'];
                $arr[]=$tmp;
            }
        }


        return $arr;
    }
}