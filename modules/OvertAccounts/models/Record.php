<?php
/*+********
 *客户信息管理
 **********/

class OvertAccounts_Record_Model extends Vtiger_Record_Model {

	/**
	 * Function returns the details of Accounts Hierarchy
	 * @return <Array>
	 */
	function getAccountHierarchy() {
		$focus = CRMEntity::getInstance($this->getModuleName());
		$hierarchy = $focus->getAccountHierarchy($this->getId());
		$i=0;
		foreach($hierarchy['entries'] as $accountId => $accountInfo) {
			preg_match('/<a href="+/', $accountInfo[0], $matches);
			if($matches != null) {
				preg_match('/[.\s]+/', $accountInfo[0], $dashes);
				preg_match("/<a(.*)>(.*)<\/a>/i",$accountInfo[0], $name);

				$recordModel = Vtiger_Record_Model::getCleanInstance('Accounts');
				$recordModel->setId($accountId);
				$hierarchy['entries'][$accountId][0] = $dashes[0]."<a href=".$recordModel->getDetailViewUrl().">".$name[2]."</a>";
			}
		}
		return $hierarchy;
	}

	/**
	 * Function returns the url for create event
	 * @return <String>
	 */
	function getCreateEventUrl() {
		$calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
		return $calendarModuleModel->getCreateEventRecordUrl().'&parent_id='.$this->getId();
	}

	/**
	 * Function returns the url for create todo
	 * @retun <String>
	 */
	function getCreateTaskUrl() {
		$calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
		return $calendarModuleModel->getCreateTaskRecordUrl().'&parent_id='.$this->getId();
	}
    /**
	 * 客户跟进后修改CRM表的修改时间,方便按修改时间排序
	 * @author steel;
	 * @param int $crmid
	 */
	public static function updateAccountsStatus($crmid){
		$db = PearDatabase::getInstance();
		$datetime=date('Y-m-d H:i:s');

		global $current_user;
		$recordModel = Vtiger_Record_Model::getInstanceById($crmid, 'OvertAccounts');
		$moduleModel = $recordModel->getModule();
		$entity=$recordModel->entity->column_fields;
        $updateSq="UPDATE vtiger_account SET vtiger_account.lastfollowuptime='{$datetime}',vtiger_account.followuptimes=vtiger_account.followuptimes+1";

        //如果跟进人是当前客户的负责人则修改保天数
		if($entity['assigned_user_id']==$current_user->id && !in_array($entity['accountrank'],array('chan_notv','forp_notv','eigp_notv','sixp_notv'))){
            $salerank=$recordModel->getSaleRank($current_user->id);
            $userinfo =$db->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?", array($current_user->id));
            $departmentid = $db->query_result($userinfo, 0,'departmentid');
            $user_entered = $db->query_result($userinfo, 0,'user_entered');
			$result=$recordModel->getRankDays(array($salerank,$entity['accountrank'],$departmentid,$user_entered));
			$updateSq.=",vtiger_account.protectday='{$result['protectday']}',vtiger_account.effectivedays='{$result['protectday']}'";
        }
        $updateSq.=" WHERE vtiger_account.accountid=?";
        $db->pquery($updateSq, array($crmid));
        $updateSql="UPDATE vtiger_crmentity SET vtiger_crmentity.modifiedtime='{$datetime}'";

        $updateSql.=" WHERE  vtiger_crmentity.crmid=?";
		$db->pquery($updateSql, array($crmid));
        //steel 2015-06-3屏敝掉公海跟进后为变为自已的和跟进后变为正常的
        //self::getOvert($crmid);
        //客户模块加入拜访单是否24小时跟进
        //$endstarttime=date('Y-m-d H:i',time()+24*3600);
        $now=time();
        $datetime=date('Y-m-d H:i:s');
        $query="UPDATE vtiger_visitingorder SET followstatus='followup',followtime=IFNULL(followtime,?),followid=IFNULL(followid,?),dayfollowup=(if((unix_timestamp(enddate)+24*3600)>=$now,'是',dayfollowup)) WHERE vtiger_visitingorder.modulestatus='c_complete' AND vtiger_visitingorder.related_to=? AND vtiger_visitingorder.extractid=? ORDER BY vtiger_visitingorder.enddate DESC limit 1";
        $db->pquery($query,array($datetime,$current_user->id,$crmid,$current_user->id));
	}
	/**
	 * Function to check duplicate exists or not
	 * @return <boolean>
	 */
	public function checkDuplicate() {
        $record = $this->getId();
        $db = PearDatabase::getInstance();

        /*$query = "SELECT accountcategory,accountrank,vtiger_users.last_name,vtiger_departments.departmentname FROM vtiger_account
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE vtiger_crmentity.label= ? and  vtiger_crmentity.setype=? AND vtiger_crmentity.deleted =0 ";*/
        $query="SELECT accountcategory,accountrank,vtiger_users.last_name,vtiger_departments.departmentname FROM vtiger_account
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        LEFT JOIN vtiger_uniqueaccountname ON vtiger_account.accountid=vtiger_uniqueaccountname.accountid 
                        WHERE vtiger_uniqueaccountname.accountname=? AND vtiger_crmentity.deleted =0 ";

        if ($record>0){$query.=" AND vtiger_account.accountid!= {$record}";}
        //$label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;/u','',$this->getName());
        //$label=preg_replace("/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|\,|\<|\.|\>|\/|\?|\;|\:|\'|\\\"|\\|\||\`|\~|\!|\@|\#|\\$|\\\|\%|\^|\&|\*|\(|\)|\-|\_|\=|\+|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\…|\……|\&|\*|\（|\）|\-|\——|\=|\+|\，|\＜|\．|\＞|\？|\／|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\＿|\－|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\—|\——|\＝|\＋/u",'',$this->getName());
        $label=str_replace('\\','',$this->getName());
        //echo $label;
        $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$label);

        $label=strtoupper($label);
        //$result = $db->pquery($query, array($label,$this->getModule()->getName()));
        $result = $db->pquery($query, array($label));
        global $data;//声明一个全局变量在不改变原结构的情况下方便调用
        $data=$db->query_result_rowdata($result);
        if($db->num_rows($result)){
            return $data['accountcategory']+1;
        }else{
            return false;
        }
		/*$query = "SELECT crmid FROM vtiger_crmentity WHERE setype = ? AND crmid = ? AND deleted = 0";
		$params = array($this->getModule()->getName(), $this->getId());



		$result = $db->pquery($query, $params);
		if ($db->num_rows($result)) {
			if($params[0]=='Accounts'){
				$result=$db->query_result_rowdata($result);
				$query = "SELECT accountcategory,accountrank,vtiger_users.last_name,vtiger_departments.departmentname FROM vtiger_account
							INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
							INNER JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
							INNER JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid INNER JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                            WHERE accountid= ? ";
                $params=array($result['crmid']);
                $record = $this->getId();
                if ($record) {
                    $query .= " AND vtiger_crmentity.crmid != ?";
                    array_push($params, $record);
                }
				$result = $db->pquery($query, $params);
                global $data;//声明一个全局变量在不改变原结构的情况下方便调用
				$data=$db->query_result_rowdata($result);
                if($result){
                    return 1;
                }
				return false;
			}
			return false;
		}
		return false;*/
	}

    /**
     * @function:公海客户跟进后变为跟进人的和保护模式变为正常
     * @functionName:getOvert
     * @author:steel
     * @time:2015-05-14 17:27
     * @param $accountid
     */
    public function getOvert($accountid){
        $recordModel = Vtiger_Record_Model::getInstanceById($accountid, 'Accounts');
        $entity=$recordModel->entity->column_fields;

        if($entity['accountcategory']==2){
            global $current_user;
            $db = PearDatabase::getInstance();
            $datetime=date('Y-m-d H:i:s');
            $sql="UPDATE vtiger_account,vtiger_crmentity SET vtiger_account.accountcategory=0,vtiger_crmentity.smownerid=?,vtiger_crmentity.modifiedtime=?,vtiger_crmentity.modifiedby=? WHERE vtiger_crmentity.crmid=vtiger_account.accountid AND vtiger_account.accountid=?";
            $db->pquery($sql,array($current_user->id,$datetime,$current_user->id,$accountid));
        }
    }


	/**
	 * 合同添加后修改最后成交时间
	 * @param unknown $accountid
	 */
	public static function updateAccountsDealtime($accountid){
		$db = PearDatabase::getInstance();
		$datetime=date('Y-m-d H:i:s');
        //$sql="SELECT sum(total) AS total FROM `vtiger_servicecontracts` WHERE modulestatus='c_complete'  AND sc_related_to=?";
        //根据回款的金额来升级客户
        $sql="SELECT sum(ifnull(unit_price,0)) AS total FROM vtiger_receivedpayments LEFT JOIN `vtiger_servicecontracts` ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid WHERE vtiger_servicecontracts.modulestatus='c_complete' AND vtiger_receivedpayments.receivedstatus = 'normal' AND vtiger_servicecontracts.sc_related_to=?";
        $result=$db->pquery($sql,array($accountid));
        $resutlttotal=$db->query_result($result,0,'total');
        if($resutlttotal>0) {
            $updateSql="UPDATE vtiger_account SET saleorderlastdealtime='{$datetime}'";
            if ($resutlttotal < 50000) {
                $updateSql .= ",accountrank='bras_isv' ";
            } elseif ($resutlttotal >= 50000 && $resutlttotal < 100000) {
                $updateSql .= ",accountrank='silv_isv' ";
            } elseif ($resutlttotal < 150000 && $resutlttotal >= 100000) {
                $updateSql .= ",accountrank='gold_isv' ";
            } else {
                $updateSql .= ",accountrank='visp_isv' ";
            }
            $updateSql .= " WHERE accountid=? ";
            $db->pquery($updateSql, array($accountid));
        }
	}

	/**
	 * Function to get List of Fields which are related from Accounts to Inventory Record.
	 * @return <array>
	 */
	public function getInventoryMappingFields() {
		return array(
				//Billing Address Fields
				array('parentField'=>'bill_city', 'inventoryField'=>'bill_city', 'defaultValue'=>''),
				array('parentField'=>'bill_street', 'inventoryField'=>'bill_street', 'defaultValue'=>''),
				array('parentField'=>'bill_state', 'inventoryField'=>'bill_state', 'defaultValue'=>''),
				array('parentField'=>'bill_code', 'inventoryField'=>'bill_code', 'defaultValue'=>''),
				array('parentField'=>'bill_country', 'inventoryField'=>'bill_country', 'defaultValue'=>''),
				array('parentField'=>'bill_pobox', 'inventoryField'=>'bill_pobox', 'defaultValue'=>''),

				//Shipping Address Fields
				array('parentField'=>'ship_city', 'inventoryField'=>'ship_city', 'defaultValue'=>''),
				array('parentField'=>'ship_street', 'inventoryField'=>'ship_street', 'defaultValue'=>''),
				array('parentField'=>'ship_state', 'inventoryField'=>'ship_state', 'defaultValue'=>''),
				array('parentField'=>'ship_code', 'inventoryField'=>'ship_code', 'defaultValue'=>''),
				array('parentField'=>'ship_country', 'inventoryField'=>'ship_country', 'defaultValue'=>''),
				array('parentField'=>'ship_pobox', 'inventoryField'=>'ship_pobox', 'defaultValue'=>'')
		);
	}

	//保护只修改protected，领取修改归属和状态
	public function updaterecord($update,$id,$owner=''){
		$db = PearDatabase::getInstance();
		$query = 'update vtiger_account set '. implode(",", $update) .' WHERE accountid='.$id;

		$db->pquery($query);
		$date=$db->formatDate(date('Y-m-d H:i:s'), true);
		$query='update vtiger_crmentity set modifiedtime='."'".$date."'".' where crmid='.$id;
		$db->pquery($query);


		if(!empty($owner)){
            //steel加入修改人
			$db->pquery('update vtiger_crmentity set smownerid='.$owner.',modifiedby='.$owner.' where crmid='.$id);
			$result=$db->run_query_allrecords('SELECT vtiger_potential.potentialid FROM vtiger_potential  
								INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid 
								LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id  
								WHERE vtiger_crmentity.deleted=0 AND vtiger_potential.potentialid > 0 and vtiger_potential.related_to = '.$id);
			$innum=array();
			if(!empty($result)){

				for($i=0;$i<count($result);$i++){
					$innum[]=$result[$i]['potentialid'];
				}

			}

			$result1=$db->run_query_allrecords('SELECT vtiger_contactdetails.contactid FROM vtiger_contactdetails  
												INNER JOIN vtiger_crmentity ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid 
												LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id  
												WHERE vtiger_crmentity.deleted=0 AND vtiger_contactdetails.contactid > 0 AND vtiger_contactdetails.accountid='.$id);
			if(!empty($result1)){

				for($i=0;$i<count($result1);$i++){
					$innum[]=$result1[$i]['contactid'];
				}
			}

			$result2=$db->run_query_allrecords('SELECT  vtiger_quotes.quoteid 
												FROM vtiger_quotes  
												INNER JOIN vtiger_crmentity ON vtiger_quotes.quoteid = vtiger_crmentity.crmid 
												LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id  
												WHERE vtiger_crmentity.deleted=0 AND vtiger_quotes.quoteid > 0 AND vtiger_quotes.accountid='.$id);

			if(!empty($result2)){

				for($i=0;$i<count($result2);$i++){
					$innum[]=$result2[$i]['quoteid'];
				}
			}

			if(!empty($innum)){
				$string=implode(',',$innum);
				$db->pquery('update vtiger_crmentity set smownerid='.$owner.' where crmid in('.$string.')');
			}
		}
	}

	/**获取已领取的某等级的客户数量
     * @param $array @array[0]:客户等级,array[1],客户状态(0,1),当前客户的负责人
     * @return mixed当前负责人已有的客户数
     */
	public function getRankCounts($array){
		$db = PearDatabase::getInstance();
        //2016-02-23steel修改
        $temparr=array('chan_notv','forp_notv','eigp_notv','sixp_notv');
        if(in_array($array[0],$temparr)){
            $query = "select vtiger_crmentity.crmid from vtiger_crmentity LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_account.protected=0 and vtiger_account.accountrank IN('chan_notv','forp_notv','eigp_notv','sixp_notv') and vtiger_account.accountcategory=? and vtiger_crmentity.deleted=0  and vtiger_crmentity.smownerid=?";
            $resault=$db->pquery($query,array($array[1],$array[2]));
            return $db->num_rows($resault);
        }
        //2016-02-23steel修改
		$query = 'select vtiger_crmentity.crmid from vtiger_crmentity LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_account.protected=0 and vtiger_account.accountrank=? and vtiger_account.accountcategory=? and vtiger_crmentity.deleted=0  and vtiger_crmentity.smownerid=?';
		$resault=$db->pquery($query,$array);
		return  $db->num_rows($resault);
	}


	//根据等级获取保护客户天数

	public function getRankDays($array){
        global $current_user;
        $db = PearDatabase::getInstance();

        $departmentid=$current_user->departmentid;
        $user_entered=$current_user->user_entered;

        // 移动端回传四个参数
        if($array[2]){
            $departmentid=$array[2];
            $user_entered=$array[3];
        }
        //查询部门 信息
        $departmentInfo=$db->pquery("SELECT d.* FROM   vtiger_departments as d  WHERE d.departmentid=? LIMIT 1 ",array($departmentid));
        $departmentInfo=$db->query_result_rowdata($departmentInfo,0);
        $departmentArray=explode("::",$departmentInfo['parentdepartment']);
        $departmentArray=array_reverse($departmentArray);
        // 获取员工当前员工阶段
        $staff_stage=$this->getStaffStage($user_entered);
        foreach ($departmentArray as $key=>$val){
            //先查询该 部门该员工阶段的该员工商务等级的是否存在 存在则继续 不存在则  按照不需要员工阶段条件的查询
            $query = 'select protectnum,protectday,isupdate from vtiger_rankprotect WHERE  department=? AND staff_stage=? AND performancerank=? AND accountrank=? limit 1 ';
            $result = $db->pquery($query, array($val,$staff_stage,$array[0],$array[1]));
            $noOfresult = $db->num_rows($result);
            if($noOfresult>0){// 如果存在继续走

            }else{//如果不存在则查询不包含员工阶段的查询 保护数
                $query = 'select protectnum,protectday,isupdate from vtiger_rankprotect WHERE  department=?  AND performancerank=?  AND accountrank=? AND  staff_stage=0 limit 1';
                $result = $db->pquery($query, array($val,$array[0],$array[1]));
                $noOfresult = $db->num_rows($result);
                if ($noOfresult>0){
                }else{
                    continue;
                }
            }
            return @$db->query_result_rowdata($result);
        }
	}

	//获取商务等级[默认初级]
	public function getSaleRank($uid){
		$db = PearDatabase::getInstance();
		$query = 'select performancerank  from vtiger_salemanager WHERE relatetoid=? limit 1';
		$resault=$db->pquery($query,array($uid));

		if($db->num_rows($resault)!=1){
			return 'juniorB';
		}

		$resault= $db->query_result_rowdata($resault);
		return $resault['performancerank'];
	}

	//查询用户可领取客户
	public function getRankLimit(){
		global  $current_user;
		$db = PearDatabase::getInstance();
		$query = 'select accountrank,count(*) as c from vtiger_crmentity LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_account.protected=0 and vtiger_account.accountcategory=0 and vtiger_crmentity.deleted=0  and vtiger_crmentity.smownerid=? group by accountrank';
		$result=$db->pquery($query,array($current_user->id));

		$noOfresult = $db->num_rows($result);
		$array=array();
		for ($i=0; $i<$noOfresult; ++$i) {
			$num = $db->fetchByAssoc($result);
			$array[$num['accountrank']]=$num['c'];
		}
        //机会客户+40%意向客户之为10个 2016-02-24新规则;
        if(!empty($array)){
            $tempnum=$array['chan_notv']+$array['forp_notv']+$array['eigp_notv']+$array['sixp_notv'];
            $array['chan_notv']=$tempnum;
	    $array['forp_notv']=$tempnum;
	    $array['eigp_notv']=$tempnum;
            $array['sixp_notv']=$tempnum;

        }

		//获取用户等级[商务默认初级]
		/*$query = 'select performancerank from vtiger_salemanager WHERE relatetoid=? limit 1';
		$result=$db->pquery($query,array($current_user->id));
		$noOfresult = $db->num_rows($result);
		if($noOfresult!=1){
			$performancerank='juniorB';
		}else{
			$info=$db->query_result_rowdata($result);
			$performancerank=$info['performancerank'];
		}*/
		$performancerank=$this->getSaleRank($current_user->id);

		$query = 'select accountrank,protectnum from vtiger_rankprotect WHERE performancerank=?';
		$result=$db->pquery($query,array($performancerank));
		$noOfresult = $db->num_rows($result);
		$residue=array();


		for ($i=0; $i<$noOfresult; ++$i) {
			$num = $db->fetchByAssoc($result);
			$residue[$num['accountrank']]=empty($array[$num['accountrank']])?$num['protectnum']:$num['protectnum']-$array[$num['accountrank']];
		}
		//编辑模式下当前等级数量加一
		if($this->getId()){
			$info=$this->getEntity()->column_fields;
			$residue[$info['accountrank']]+=1;
		}
		return $residue;
	}
    /**
	 * @author: steel
	 * @time :2015-02-13 获当前客户对应的客服
	 * @param int $parentRecordId
	 * @param unknown $pagingModel
	 * @return multitype:Ambigous <unknown, multitype:, s, --, string, mixed>
	 */
    static function getservicecomments($parentRecordId, $pagingModel){
		$db = PearDatabase::getInstance();
		$recordInstances = array();

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		$listQuery = "SELECT vtiger_servicecomments.*,vtiger_users.last_name FROM vtiger_servicecomments 
				INNER JOIN vtiger_users ON vtiger_users.id=vtiger_servicecomments.serviceid WHERE vtiger_servicecomments.assigntype='accountby' AND related_to = ? 
				ORDER BY endtime desc";

		$result = $db->pquery($listQuery, array($parentRecordId));
		$rows = $db->num_rows($result);

		for ($i=0; $i<$rows; $i++) {
			$row = $db->query_result_rowdata($result, $i);
			//$recordInstance = new self();
			//$recordInstance->setData($row)->setParent($row['crmid'], $row['module']);
			$recordInstances[] = $row;
		}
		return $recordInstances;

	}
    /**
     * @author: steel
     * @time :2015-11-04 获当前客户对应的客服信息
     * @param int $parentRecordId
     * @param unknown $pagingModel
     * @return multitype:Ambigous <unknown, multitype:, s, --, string, mixed>
     */
    static function getservicecommentsandsmower($parentRecordId, $pagingModel){
        $db = PearDatabase::getInstance();
        $recordInstances = array();
        $listQuery = "SELECT vtiger_users.last_name,vtiger_users.phone_work,vtiger_users.phone_mobile,vtiger_users.email1  FROM vtiger_servicecomments
				LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_servicecomments.serviceid WHERE vtiger_servicecomments.assigntype='accountby' AND related_to = ?
				ORDER BY endtime desc limit 1";
        $result = $db->pquery($listQuery, array($parentRecordId));
        $rows = $db->num_rows($result);
        if($rows>0){
            //客服信息
            $recordInstances['f']= $db->query_result_rowdata($result, 0);
        }
        $listQuery = "SELECT vtiger_users.last_name,vtiger_users.phone_work,vtiger_users.phone_mobile,vtiger_users.email1  FROM vtiger_account
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                    WHERE vtiger_account.accountid=?";
        $result = $db->pquery($listQuery, array($parentRecordId));
        $rows = $db->num_rows($result);
        if($rows>0){
            //客户负责人信息
            $recordInstances['h']= $db->query_result_rowdata($result, 0);
        }
        return $recordInstances;

    }
    /**
	 * @author steel 2015-02-13
	 * @deprecated 取得当前客户负责人的变历史记录
	 * @param unknown $parentRecordId
	 * @param unknown $pagingModel
	 * @return multitype:Ambigous <unknown, multitype:, s, --, string, mixed>
	 */
	static function getheads($parentRecordId, $pagingModel){
		$db = PearDatabase::getInstance();
		$recordInstances = array();

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		/*$listQuery = "SELECT vtiger_accountsmowneridhistory.*,o.last_name AS oldname,n.last_name AS newname,m.last_name AS mname FROM vtiger_accountsmowneridhistory
				LEFT JOIN vtiger_users as o ON o.id=vtiger_accountsmowneridhistory.oldsmownerid
				LEFT JOIN vtiger_users as n ON n.id=vtiger_accountsmowneridhistory.newsmownerid
				LEFT JOIN vtiger_users as m ON m.id=vtiger_accountsmowneridhistory.modifiedby
				WHERE vtiger_accountsmowneridhistory.accountid=? ORDER BY vtiger_accountsmowneridhistory.id DESC LIMIT 5";*/
        $listQuery = "SELECT vtiger_accountsmowneridhistory.*,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountsmowneridhistory.oldsmownerid LIMIT 1) AS oldname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountsmowneridhistory.newsmownerid LIMIT 1) AS newname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountsmowneridhistory.modifiedby LIMIT 1) AS mname FROM vtiger_accountsmowneridhistory
				WHERE vtiger_accountsmowneridhistory.accountid=? ORDER BY vtiger_accountsmowneridhistory.id DESC LIMIT 5";

		$result = $db->pquery($listQuery, array($parentRecordId));
		$rows = $db->num_rows($result);

		for ($i=0; $i<$rows; $i++) {
			$row = $db->query_result_rowdata($result, $i);
			//$recordInstance = new self();
			//$recordInstance->setData($row)->setParent($row['crmid'], $row['module']);
			$recordInstances[] = $row;
		}
		return $recordInstances;

	}
    /**
	 * 取得联系人表里的联系人信息显不在摘要页上
	 * @author steel 2015-03-05
	 * @param unknown $contactsid
	 * @return Ambigous <unknown, multitype:, s, --, string, mixed>
	 */
	static public function getContactsToIndex($contactsid){
		$db=PearDatabase::getInstance();
		$query = "SELECT vtiger_contactdetails.contactid as crmid,vtiger_contactdetails.*,
            (select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid = vtiger_contactdetails.contactid) as smownerid,
			(select accountname from vtiger_account where vtiger_account.accountid = vtiger_contactdetails.accountid) as accountname, 
			(select last_name from vtiger_users where vtiger_users.id = smownerid) as user_name 
			FROM vtiger_contactdetails 
			WHERE EXISTS(select crmid from vtiger_crmentity where vtiger_crmentity.crmid = vtiger_contactdetails.contactid and vtiger_crmentity.deleted = 0) 
			AND vtiger_contactdetails.accountid = {$contactsid} LIMIT 5";
		$result=$db->pquery($query);
		$rows = $db->num_rows($result);

		for ($i=0; $i<$rows; $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$recordInstances[] = $row;
		}
		return $recordInstances;
	}

    static public function getContactsrelation($contactsid){
        $db=PearDatabase::getInstance();
        $query = "SELECT vtiger_contactdetails.contactid as crmid,vtiger_contactdetails.*,
            (select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid = vtiger_contactdetails.contactid) as smownerid,
			(select accountname from vtiger_account where vtiger_account.accountid = vtiger_contactdetails.accountid) as accountname,
			(select last_name from vtiger_users where vtiger_users.id = smownerid) as user_name
			FROM vtiger_contactdetails
			WHERE EXISTS(select crmid from vtiger_crmentity where vtiger_crmentity.crmid = vtiger_contactdetails.contactid and vtiger_crmentity.deleted = 0)
			AND vtiger_contactdetails.accountid = {$contactsid} LIMIT 5";
        $result=$db->pquery($query);
        $rows = $db->num_rows($result);

        for ($i=0; $i<$rows; $i++) {
            $row = $db->fetchByAssoc($result);
            $recordInstances[] = $row;
        }
        return $recordInstances;
    }
//临时的标记
    //打标记
    public function getsignflag($array){
        $db = PearDatabase::getInstance();
        //2016-02-23steel修改
        $query = "select vtiger_crmentity.crmid from vtiger_crmentity LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_account.protected=0 and vtiger_account.accountrank IN('chan_notv','forp_notv','eigp_notv','sixp_notv') and vtiger_account.accountcategory=0 and vtiger_account.sign=1 and vtiger_crmentity.deleted=0  and vtiger_crmentity.smownerid=?";
        $resault=$db->pquery($query,$array);
        return $db->num_rows($resault);
    }
    //查询用户可领取客户
    public function getRankLimitm($userid){
        global $current_user;
        $db = PearDatabase::getInstance();
        $query = 'select accountrank,count(*) as c from vtiger_crmentity LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_account.protected=0 and vtiger_account.accountcategory=0 and vtiger_crmentity.deleted=0  and vtiger_crmentity.smownerid=? group by accountrank';
        $result = $db->pquery($query, array($userid));

        $noOfresult = $db->num_rows($result);
        $array = array();
        for ($i = 0; $i < $noOfresult; ++$i) {
            $num = $db->fetchByAssoc($result);
            $array[$num['accountrank']] = $num['c'];
        }
        $performancerank = $this->getSaleRank($userid);
        // 查询部门id
        $departmentInfo=$db->pquery("SELECT d.* FROM   vtiger_user2department as d  WHERE d.userid=? LIMIT 1 ",array($userid));
        $departmentInfo=$db->query_result_rowdata($departmentInfo,0);
        $departmentid=$departmentInfo['departmentid'];

        $userInfo=$db->pquery("SELECT d.user_entered FROM   vtiger_users as d  WHERE d.id=? LIMIT 1 ",array($userid));
        $userInfo=$db->query_result_rowdata($userInfo,0);
        $user_entered=$userInfo['user_entered'];
        //查询部门 信息
        $departmentInfo=$db->pquery("SELECT d.* FROM   vtiger_departments as d  WHERE d.departmentid=? LIMIT 1 ",array($departmentid));
        $departmentInfo=$db->query_result_rowdata($departmentInfo,0);
        $departmentArray=explode("::",$departmentInfo['parentdepartment']);
        $departmentArray=array_reverse($departmentArray);
        // 获取员工当前员工阶段
        $staff_stage=$this->getStaffStage($user_entered);
        foreach ($departmentArray as $key=>$val){
            // 先查询该 部门该员工阶段的该员工商务等级的是否存在 存在则继续 不存在则  按照不需要员工阶段条件的查询
            $query = 'select accountrank,protectnum from vtiger_rankprotect WHERE  department=? AND staff_stage=? AND performancerank=? AND accountrank=? ';
            $result = $db->pquery($query, array($val,$staff_stage,$performancerank,$_REQUEST['ranks']));
            $noOfresult = $db->num_rows($result);
            if($noOfresult>0){// 如果存在继续走

            }else{//如果不存在则查询不包含员工阶段的查询 保护数
                $query = 'select accountrank,protectnum from vtiger_rankprotect WHERE  department=?  AND performancerank=? AND staff_stage=0 AND  accountrank=? ';
                $result = $db->pquery($query, array($val,$performancerank,$_REQUEST['ranks']));
                $noOfresult = $db->num_rows($result);
                if ($noOfresult>0){
                }else{
                    continue;
                }
            }
            $residue = array();
            //保留所有等级的保护数量一并返回。
            $residue['rankProtectNum']=[];
            $residue['havingRankProtectNum']=[];
            for ($i = 0; $i < $noOfresult; ++$i) {
                $num = $db->fetchByAssoc($result);
                //总数保护数量
                $residue['rankProtectNum'][$num['accountrank']]=$num['protectnum'];
                //已有保护数量
                $residue['havingRankProtectNum'][$num['accountrank']]=$array[$num['accountrank']];
            }
            // 获取 剩余保护数量
            foreach ($residue['rankProtectNum'] as $key=>$val){
                //总数保护数量减去已有保护数量获取剩余保护数量
                $residue[$key] =$val-$array[$key];
            }
            //编辑模式下当前等级数量加一
            if ($this->getId()) {
                $info = $this->getEntity()->column_fields;
                if ($info['assigned_user_id'] == $userid) {
                    $residue[$info['accountrank']] += 1;
                }
            }
            return $residue;
        }
    }
    /////临时处理


    static public function getReport(){
		global $current_user;
		$where=getAccessibleUsers();
		$datetime=date("Y-m-d");
		$sql="SELECT count(accountid) as counts,(select last_name from vtiger_users where id=vtiger_crmentity.smownerid) last_name,
        vtiger_crmentity.smownerid
		,sum(IF(accountrank='chan_notv',1,0)) as chan_notv
		,sum(IF(accountrank='forp_notv',1,0)) as forp_notv
		,sum(IF(accountrank='norm_isv',1,0)) as norm_isv
		,sum(IF(accountrank='spec_isv',1,0)) as spec_isv
		,sum(IF(accountrank='eigp_notv',1,0)) as eigp_notv
		,sum(IF(accountrank='sixp_notv',1,0)) as sixp_notv
		,sum(IF(accountrank='visp_isv',1,0)) as visp_isv
		,sum(IF(accountrank='wlad_isv',1,0)) as wlad_isv
		,sum(IF(accountrank='wlvp_isv',1,0)) as wlvp_isv
		,sum(IF(accountrank='wlbr_isv',1,0)) as wlbr_isv
		,sum(IF(accountrank='wlsi_isv',1,0)) as wlsi_isv
		,sum(IF(accountrank='wlgo_isv',1,0)) as wlgo_isv
		,sum(IF(accountrank='iron_isv',1,0)) as iron_isv
		,sum(IF(accountrank='bras_isv',1,0)) as bras_isv
		,sum(IF(accountrank='silv_isv',1,0)) as silv_isv
		,sum(IF(accountrank='gold_isv',1,0)) as gold_isv
		
		,sum(IF(accountrank='chan_notv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daychan_notv
		,sum(IF(accountrank='forp_notv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as dayforp_notv
		,sum(IF(accountrank='norm_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daynorm_isv
		,sum(IF(accountrank='spec_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as dayspec_isv
		,sum(IF(accountrank='eigp_notv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as dayeigp_notv
		,sum(IF(accountrank='sixp_notv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daysixp_notv
		,sum(IF(accountrank='visp_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as dayvisp_isv
		,sum(IF(accountrank='wlad_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daywlad_isv
		,sum(IF(accountrank='wlvp_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daywlvp_isv
		,sum(IF(accountrank='wlbr_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daywlbr_isv
		,sum(IF(accountrank='wlsi_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daywlsi_isv
		,sum(IF(accountrank='wlgo_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daywlgo_isv
		,sum(IF(accountrank='iron_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as dayiron_isv
		,sum(IF(accountrank='bras_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daybras_isv
		,sum(IF(accountrank='silv_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daysilv_isv
		,sum(IF(accountrank='gold_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daygold_isv
		
		,sum(IF(accountrank='chan_notv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekchan_notv
		,sum(IF(accountrank='forp_notv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekforp_notv
		,sum(IF(accountrank='norm_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weeknorm_isv
		,sum(IF(accountrank='spec_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekspec_isv
		,sum(IF(accountrank='eigp_notv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekeigp_notv
		,sum(IF(accountrank='sixp_notv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weeksixp_notv
		,sum(IF(accountrank='visp_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekvisp_isv
		,sum(IF(accountrank='wlad_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekwlad_isv
		,sum(IF(accountrank='wlvp_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekwlvp_isv
		,sum(IF(accountrank='wlbr_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekwlbr_isv
		,sum(IF(accountrank='wlsi_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekwlsi_isv
		,sum(IF(accountrank='wlgo_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekwlgo_isv
		,sum(IF(accountrank='iron_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekiron_isv
		,sum(IF(accountrank='bras_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekbras_isv
		,sum(IF(accountrank='silv_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weeksilv_isv
		,sum(IF(accountrank='gold_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekgold_isv
		
		,sum(IF(accountrank='chan_notv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthchan_notv
		,sum(IF(accountrank='forp_notv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthforp_notv
		,sum(IF(accountrank='norm_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthnorm_isv
		,sum(IF(accountrank='spec_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthspec_isv
		,sum(IF(accountrank='eigp_notv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as montheigp_notv
		,sum(IF(accountrank='sixp_notv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthsixp_notv
		,sum(IF(accountrank='visp_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthvisp_isv
		,sum(IF(accountrank='wlad_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthwlad_isv
		,sum(IF(accountrank='wlvp_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthwlvp_isv
		,sum(IF(accountrank='wlbr_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthwlbr_isv
		,sum(IF(accountrank='wlsi_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthwlsi_isv
		,sum(IF(accountrank='wlgo_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthwlgo_isv
		,sum(IF(accountrank='iron_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthiron_isv
		,sum(IF(accountrank='bras_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthbras_isv
		,sum(IF(accountrank='silv_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthsilv_isv
		,sum(IF(accountrank='gold_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthgold_isv
		
		FROM vtiger_account
		LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
		LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
		WHERE ";
		if($where!='1=1'){
			$sql.=" smownerid {$where} AND ";
		}
		$sql.= " vtiger_crmentity.deleted=0 AND vtiger_account.accountcategory=0  AND vtiger_users.`status` = 'Active' GROUP BY smownerid";

		$db=PearDatabase::getInstance();
		$result=$db->pquery($sql,array());
		$rows = $db->num_rows($result);
		$recordInstances=array();
		for ($i=0; $i<$rows; $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$recordInstances[]= $row;
		}

		return $recordInstances;

	}
     /**
     * 求对应跟进记录的总条数
     * @param $id客户的ID
     * @return mixed|string
     * @throws Exception
     */
    static public function getModcommentCount($id){
        $db=Peardatabase::getInstance();
        $query="SELECT count(1) AS counts FROM `vtiger_modcomments` WHERE related_to =? AND modulename='Accounts'";
        $result=$db->pquery($query,array($id));
        return $db->query_result($result,0,'counts');

    }
    /**
     * 当前登陆的用户是否有修改客户名称客户等级权限
     * @param $filed
     * @return bool
     */
    public static function getsupperaccountupdate($filed){
        $db=PearDatabase::getInstance();
        global $current_user;
        $query="SELECT 1 FROM vtiger_supperaccountupdate WHERE deleted=0 and userid=? and field=?";
        $result=$db->pquery($query,array($current_user->id,$filed));
        $num=$db->num_rows($result);
        if($num>0){
            return true;
        }
        return false;
    }

    /*
		垫款的加减
    */
	public function setAdvancesmoney($id, $value, $msg) {
		$db=PearDatabase::getInstance();
        global $current_user;

        $sql = "SELECT advancesmoney FROM vtiger_account WHERE accountid=? LIMIT 1";
        $sel_result = $db->pquery($sql, array($id));
        $res_cnt = $db->num_rows($sel_result);
		if($res_cnt > 0) {
		    $row = $db->query_result_rowdata($sel_result, 0);

		    $sql = "UPDATE vtiger_account SET advancesmoney=advancesmoney+{$value} WHERE accountid=? LIMIT 1";
        	$db->pquery($sql, array($id));

        	// 做更新记录
	        $did = $db->getUniqueId('vtiger_modtracker_basic');
	        $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
	                        array($did, $id, 'Accounts', $current_user->id, date('Y-m-d H:i:s'), 0));
	        $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
	            Array($did, 'advancesmoney', $row['advancesmoney'], ($row['advancesmoney'] + $value) . $msg ));
		}
	}
    /**
     * 客服跟进中移过来的
     * Function returns latest comments for parent record
     * @param <Integer> $parentRecordId - parent record for which latest comment need to retrieved
     * @param <Vtiger_Paging_Model> - paging model
     * @return ModComments_Record_Model if exits or null
     */
    public function getRecentComments($parentRecordId, $pagingModel,$moduleName=''){
        $db = PearDatabase::getInstance();
        $startIndex = $pagingModel->getStartIndex();
        $limit = $pagingModel->getPageLimit();

        $query = "SELECT vtiger_modcomments.commentreturnplanid,vtiger_modcomments.commentcontent, vtiger_modcomments.addtime,
				vtiger_modcomments.related_to, vtiger_modcomments.creatorid, vtiger_modcomments.modcommenttype, 
				vtiger_modcomments.modcommentmode, vtiger_modcomments.modcommenthistory, vtiger_modcomments.modcommentpurpose,
				vtiger_modcomments.modcommentsid,vtiger_modcomments.contact_id,IFNULL((select name from vtiger_contactdetails where contactid=vtiger_modcomments.contact_id),IFNULL((select linkname from vtiger_account where accountid=vtiger_modcomments.related_to ),'-')) as lastname,
				IFNULL((select linkname from vtiger_account where accountid=vtiger_modcomments.related_to ),'-') as shouyao 
				FROM vtiger_modcomments WHERE  ";

        //客户判断

        $where=getAccessibleUsers('Accounts','List',true);
        if($where !='1=1'){//如果不是管理员走这里
            $recordModule=self::getInstanceById($parentRecordId);
            $column_fields=$recordModule->getEntity()->column_fields;//找到该记录对应的客户信息
            $shareAccountQuery='SELECT 1 FROM vtiger_shareaccount WHERE sharestatus=1 AND accountid=? AND userid in('.implode(',',$where).')';
            $shareAccountResult=$db->pquery($shareAccountQuery,array($parentRecordId));
            $realoperate=setoperate($parentRecordId,'Accounts');
            if(in_array($column_fields['assigned_user_id'],$where) || !$db->num_rows($shareAccountResult) || $realoperate==$_REQUEST['realoperate']){
                //当前登录人的的权限包含客户负责人或登录没有对应的客户共享商务,或其它模块跳过来的
                //则说明当前登录人可能是客服,商务自已或是商务上级,共享部门带过来的可以查看客户的权限.那么该当前登录人可以查看所有的跟进信息
                $query = $query ."  vtiger_modcomments.related_to = ? ORDER BY modcommentsid DESC LIMIT $startIndex, $limit";
            }else{
                //是共享商务或是共享商务的上级
                //可以查看自已的或是自已下级的跟进
                $query = $query ."  vtiger_modcomments.related_to = ?  AND vtiger_modcomments.creatorid IN(".implode(',',$where).") ORDER BY modcommentsid DESC LIMIT $startIndex, $limit";
            }
        }else{
            $query = $query ."  vtiger_modcomments.related_to = ?  ORDER BY modcommentsid DESC
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
        for ($i=0; $i<$rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $recordInstance = new ModComments_Record_Model();

            $recordInstance->setData($row);
            //跟进提醒修改 2014-12-22/gaocl start
            $recordInstance->setAlerts(empty($alertModcomments[$row['modcommentsid']])?array():$alertModcomments[$row['modcommentsid']]);

            //跟进提醒修改 2014-12-22/gaocl end
            $recordInstance->setHistory(empty($subcomments[$row['modcommentsid']])?array():$subcomments[$row['modcommentsid']]);
            $recordInstances[] = $recordInstance;
        }

        return $recordInstances;
    }

    //获取掉入公海的天数(客户从临时区掉公海后一段时间（5天）内不允许领取(主要是防止商务频繁给客户打电话)) gaocl add 2018/02/28
    public function getFallToovertDays($id){
        $db = PearDatabase::getInstance();
        $sql = "SELECT fall_toovert_time FROM vtiger_account WHERE accountid=? LIMIT 1";
        $sel_result = $db->pquery($sql, array($id));
        $res_cnt = $db->num_rows($sel_result);
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            if(!empty($row['fall_toovert_time'])){
                $fall_toovert_time = strtotime(date("Y-m-d",strtotime($row['fall_toovert_time'])));
                $cur_date = strtotime(date("Y-m-d"));
                return round(($cur_date-$fall_toovert_time)/86400);
            }
        }
        return -1;
    }

    //有关链接的方法
    /**
     * 生成记录访问链接[关联数据详细访问授权] By Joe
     */
    public function getDetailViewUrl($module_name="OvertAccounts") {
        $module = $this->getModule();
        $realoperate='';
        if(isset($_REQUEST['view']) && $_REQUEST['view']=='Detail'){
            $realoperate='&realoperate='.setoperate($this->getId(),$this->getModuleName());
        }
        return 'index.php?module='.$module_name.'&view='.$module->getDetailViewName().'&record='.$this->getId().$realoperate;
    }
    public function exportGrouprt($module,$classname,$id=0){
        if($id==0)
        {
            global $current_user;
            $id = $current_user->id;
        }
        $db=PearDatabase::getInstance();
        $query="SELECT 1 FROM vtiger_exportmanage WHERE deleted=0 AND userid=? AND module=? AND classname=?";
        $result=$db->pquery($query,array($id,$module,$classname));
        $num=$db->num_rows($result);
        if($num){
            return true;
        }
        return false;
    }


    public function collectCustomers(Vtiger_Request $request){
        $recordId = $request->get('record');
        if(empty($recordId)){
            return  array('success'=>false,'message'=>'客户ID不能为空');
        }
        $type=$request->get('type');
        //自定义查询条件
        /*VTCacheUtils::updateFieldInfo(6, 'protected', 1, 'protected', 'protected', 'vtiger_account', 2, 'V~M', 0);
        VTCacheUtils::updateFieldInfo(6, 'accountrank',2 ,'accountrank','accountrank','vtiger_account', 2, 'V~M', 0);
        VTCacheUtils::updateFieldInfo(6, 'accountcategory',3,'accountcategory','accountcategory','vtiger_account', 2, 'V~M', 0);
        VTCacheUtils::updateFieldInfo(6, 'smownerid',4,'smownerid','smownerid','vtiger_crmentity', 2, 'V~M', 0);
        */
        //数据权限与列表一致
        vglobal('currentView','List');
        $db=PearDatabase::getInstance();
        //防止并发
        $lockresult=$db->pquery("SELECT 1 FROM vtiger_lockaccountid WHERE lockaccountid=?",array($recordId));
        if($db->num_rows($lockresult)>0){
            return  array('success'=>false,'message'=>'客户已锁定,其他人正在操作！');
        }
        $db->pquery("INSERT INTO vtiger_lockaccountid(lockaccountid) VALUES (?)",array($recordId));
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Accounts');
        $moduleModel = $recordModel->getModule();
        $entity=$recordModel->entity->column_fields;
        //受保护的客户
        global  $current_user,$currentModule;
        $currentModule = 'Accounts';
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($request->get("userid"));

        $result1 = array('success'=>true);
        if($entity['protected']==1){
            if($type=='UNPROTECTED' &&  !empty($current_user->viewPermission['Accounts/Protect'])){
                $recordModel->updaterecord(array('protected=0'),$recordId);
                $db=PearDatabase::getInstance();
                $sql='DELETE FROM vtiger_accountprotect WHERE accountid=?';
                $db->pquery($sql,array($recordId));
                $array[0]=array('fieldname'=>'accountcategory','prevalue'=>'已保护', 'postvalue'=>'取消保护');
                $this->setModTracker2($recordId,$array);
            }
        }else{
            if($type=='OVERT' && $entity['accountcategory']!=2 ){
                //自己或下属的客户放到公海，客户降级到机会客户，取消未成交销售机会
                //2015-03-11更改为放入公海不再判断客户等级
                //$recordModel->updaterecord(array('accountcategory=2'),$recordId);
                $datetime=date('Y-m-d H:i:s');

                $db->pquery("REPLACE INTO `vtiger_accountgonghaiinrel`(accountid,userid,smownerid,createdtime) VALUES(?,?,?,?)",array($recordId,$current_user->id,$entity['assigned_user_id'],$datetime));
                $id = $db->getUniqueId('vtiger_modtracker_basic');
                $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                    array($id , $recordId, 'Accounts', $current_user->id, $datetime, 0));
                $accountcategory=$entity['accountcategory']==0?'正常 扔':($entity['accountcategory']==1?'临时区 扔':'');
                $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'accountcategory',$accountcategory, 2));
                if(strstr($entity['accountrank'],'_isv')){
                    //$result1 = array('success'=>false,'message'=>'已成交客户不能转入公海！');
                    //2015-06-03 steel 更新规则为已成交客户丢公海不掉等级
                    //追加更新掉入公海时间 gaocl edit 2018/02/28
                    //$recordModel->updaterecord(array('accountcategory=2'),$recordId);
                    $recordModel->updaterecord(array('accountcategory=2','fall_toovert_time=NOW()'),$recordId);
                }else{
                    //2015-06-03 steel 更新规则为未成交客户丢公海等级变为机会客户
                    //追加更新掉入公海时间 gaocl edit 2018/02/28
                    //$recordModel->updaterecord(array('accountcategory=2',"accountrank='chan_notv'"),$recordId);
                    $recordModel->updaterecord(array('accountcategory=2',"accountrank='chan_notv'",'fall_toovert_time=NOW()'),$recordId);
                }
            }elseif ($type=='TEMPORARY' && $entity['accountcategory']==2){ //公海捡入临时区的
                /*$accountnum=$recordModel->getRankCounts(array($entity['accountrank'],1,(int)$current_user->id));
                //加入保护客户数量
                if(5>$accountnum) {
                    $recordModel->updaterecord(array('accountcategory=1', 'protectday=3'), $recordId, $current_user->id);
                }else{
                    $result1 = array('success'=>false,'message'=>'LBL_NOPROTECT');
                }*/
                if($entity['assigned_user_id']!=$current_user->id){
                    $db = PearDatabase::getInstance();
                    $resultss=$db->pquery('SELECT highseasaccountnumid FROM `vtiger_highseasaccountnum` WHERE userid=? AND createdtime=?',array($current_user->id,date('Y-m-d')));

                    if($db->num_rows($resultss)<1000) {
                        //临时区最大客户数量调整为300个
                        $result_tmp =$db->pquery('SELECT 1 FROM vtiger_account a 
                                                 JOIN vtiger_crmentity  b ON(a.accountid=b.crmid)
                                                WHERE a.accountcategory=1 AND b.deleted=0 AND b.smownerid=? ',array($current_user->id));
                        $cnt_tmp = $db->num_rows($result_tmp);
                        if($cnt_tmp > 300) {
                            $result1 = array('success'=>false,'message'=>'临时区最大客户数量不能超过300个('.$cnt_tmp.'个)');
                        }else{
                            //客户从临时区掉公海后一段时间（5天）内不允许领取(主要是防止商务频繁给客户打电话) gaocl 2018/02/28 add
                            $days = $recordModel->getFallToovertDays($recordId);
                            if($days>=0 && $days <= 5) {
                                $result1 = array('success' => false, 'message' => '客户从临时区掉公海后5天内不允许领取！如需处理，中小商务邮件至中小监察，非中小邮件至erp110@71360.com');
                            }else{
                                $sql="SELECT r.relatetoid FROM vtiger_receivedpayments as r INNER JOIN  vtiger_servicecontracts as s ON s.servicecontractsid=r.relatetoid WHERE s.sc_related_to=?";
                                $results=$db->pquery($sql,array($recordId));
                                $numrowscount=$db->num_rows($results);
                                // 如果是铁牌、银牌、铜牌、金牌、VIP 不可被领取到临时区
                                if($numrowscount>0 && in_array($entity['accountrank'],array('iron_isv','bras_isv','silv_isv','gold_isv','visp_isv'))){
                                    $result1 = array('success' => false, 'message' => vtranslate($entity['accountrank'])."客户不可以被领取到临时区。");
                                }else{
                                    $salerank=$recordModel->getSaleRank($current_user->id);
                                    $userinfo =$db->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?", array($current_user->id));
                                    $departmentid = $db->query_result($userinfo, 0,'departmentid');
                                    $user_entered = $db->query_result($userinfo, 0,'user_entered');
                                    $result=$recordModel->getRankDays(array($salerank,$entity['accountrank'],$departmentid,$user_entered));
                                    $datetime = date('Y-m-d H:i:s');
                                    $id = $db->getUniqueId('vtiger_modtracker_basic');
                                    $db->pquery('INSERT vtiger_highseasaccountnum(userid,createdtime,accountid) VALUES(?,?,?)',array($current_user->id,date('Y-m-d'),$recordId));
                                    $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                                        array($id, $recordId, 'Accounts', $current_user->id, $datetime, 0));
                                    $accountcategory = '公海 捡';
                                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                                        Array($id, 'accountcategory', $accountcategory, 1));
                                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                                        Array($id, 'assigned_user_id', $entity['assigned_user_id'], $current_user->id));
                                    $recordModel->updaterecord(array('accountcategory=1',"accountrank=(IF(accountrank='forp_notv','chan_notv',accountrank ))", 'protectday=3','effectivedays='.$result['protectday']), $recordId, $current_user->id);
                                }
                            }
                        }
                    }else{
                        $result1 = array('success'=>false,'message'=>'今天你已领取了1000次客户,明天再来吧!');
                    }
                }else{
                    $result1 = array('success'=>false,'message'=>'不允许领取自已的客户');
                }
            }elseif ($type=='SELF' && $entity['accountcategory']!=0){
                //领取客户限制(该等级客户保护数量和天数)
                $db=PearDatabase::getInstance();
                $salerank=$recordModel->getSaleRank($current_user->id);
                $accountRankStr = vtranslate($entity['accountrank'],'RankProtect');
                $userinfo =$db->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?", array($current_user->id));
                $departmentid = $db->query_result($userinfo, 0,'departmentid');
                $user_entered = $db->query_result($userinfo, 0,'user_entered');
                $result=$recordModel->getRankDays(array($salerank,$entity['accountrank'],$departmentid,$user_entered));
                //已领取的客户总数
                $accountnum=$recordModel->getRankCounts(array($entity['accountrank'],0,(int)$current_user->id));
                if($result['protectnum']>$accountnum){
                    //当前客户不能领取自已在公海的客户steel 2016-02-24
                    if($entity['assigned_user_id']!=$current_user->id || $entity['accountcategory']==1 || !in_array($entity['accountrank'],array('chan_notv','forp_notv','eigp_notv','sixp_notv'))){
                        if($entity['accountcategory']==2){
                            $resultss=$db->pquery('SELECT highseasaccountnumid FROM `vtiger_highseasaccountnum` WHERE userid=? AND createdtime=?',array($current_user->id,date('Y-m-d')));
                            if($db->num_rows($resultss)<100){
                                //客户从临时区掉公海后一段时间（5天）内不允许领取(主要是防止商务频繁给客户打电话) gaocl 2018/02/28 add
                                $days = $recordModel->getFallToovertDays($recordId);

                                if($days>=0 && $days <= 5){
                                    $result1 = array('success'=>false,'message'=>'客户从临时区掉公海后5天内不允许领取！如需处理，中小商务邮件至中小监察，非中小邮件至erp110@71360.com');
                                }else{
                                    $db->pquery('INSERT vtiger_highseasaccountnum(userid,createdtime,accountid) VALUES(?,?,?)',array($current_user->id,date('Y-m-d'),$recordId));
                                    $datetime=date('Y-m-d H:i:s');
                                    $db->pquery("REPLACE INTO `vtiger_accountgonghaioutrel`( accountid,userid,createdtime) VALUES(?,?,?)",array($recordId,$current_user->id,$datetime));
                                    $id= $db->getUniqueId('vtiger_modtracker_basic');
                                    $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                                        array($id , $recordId, 'Accounts', $current_user->id, $datetime, 0));
                                    $accountcategory='公海 捡';
                                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                                        Array($id, 'accountcategory',$accountcategory, 0));
                                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                                        Array($id, 'assigned_user_id',$entity['assigned_user_id'], $current_user->id));

                                    $recordModel->updaterecord(array('accountcategory=0',"accountrank=(IF(accountrank='forp_notv','chan_notv',accountrank ))", 'protectday=' . $result['protectday'],'effectivedays='.$result['protectday']), $recordId, $current_user->id,true);
                                }
                            }else{
                                $result1 = array('success'=>false,'message'=>'今天你已领取了100次客户,明天再来吧!');
                            }
                        }elseif($entity['accountcategory']==1 && $entity['assigned_user_id']==$current_user->id){
                            //临时区领的客户只能自已领自已作用可以避免两人同一时间打开公海,一个先领入领时区另一个后领入正常
                            $recordModel->updaterecord(array('accountcategory=0',"accountrank=(IF(accountrank='forp_notv','chan_notv',accountrank ))",'protectday=effectivedays'),$recordId,$current_user->id,true);
                            $array[0]=array('fieldname'=>'accountcategory','prevalue'=>'临时区 领', 'postvalue'=>0);
                            $this->setModTracker2($recordId,$array);
                        }else{
                            $result1 = array('success'=>false,'message'=>'该客户已经被领取了');
                        }
                    }else{
                        $result1 = array('success'=>false,'message'=>'不允许领取自已的客户');
                    }
                }else{
                    $result1 = array('success'=>false,'message'=>$accountRankStr.'等级客户保护数量'.$result['protectnum'].'个，您当前已有'.$accountRankStr.'等级客户'.$accountnum.'个，已达保护数量，不可领取该等级客户。');
                }
            }elseif($type=='PROTECTED' && !empty($current_user->viewPermission['Accounts/Protect'])){
                $db=PearDatabase::getInstance();
                $query="SELECT protectnum FROM vtiger_protectsetting WHERE userid=? limit 1";
                $result=$db->pquery($query,array($current_user->id));
                if($db->num_rows($result)){
                    $rowData=$db->raw_query_result_rowdata($result);
                    $query="SELECT 1 FROM vtiger_accountprotect WHERE userid=?";
                    $resultnum=$db->pquery($query,array($current_user->id));
                    if($db->num_rows($resultnum)<$rowData['protectnum']) {
                        $recordModel->updaterecord(array('protected=1'), $recordId);
                        $sql="INSERT INTO vtiger_accountprotect(accountid,userid) VALUES(?,?)";
                        $db->pquery($sql,array($recordId,$current_user->id));
                        $array[0]=array('fieldname'=>'accountcategory','prevalue'=>'未保护', 'postvalue'=>'已保护');
                        $this->setModTracker2($recordId,$array);
                    }else{
                        $result1 = array('success'=>false,'message'=>'您当前只能保护'.$rowData['protectnum'].'个客户！已超过最大限度');
                    }
                }else{
                    $result1 = array('success' => false, 'message' => '请联系管理员设置权限！');
                }
            }elseif($type=='NOSIGN' && $entity['assigned_user_id']==$current_user->id){
                $recordModel->updaterecord(array('sign=0'),$recordId);
            }elseif($type=='SIGN'&& $entity['assigned_user_id']==$current_user->id){
                if($recordModel->getsignflag(array($current_user->id))<15){
                    $recordModel->updaterecord(array('sign=1'), $recordId);
                }else{
                    $result1 = array('success'=>false,'message'=>'只能打15个标记！');
                }
            }else{
                $result1 = array('success'=>false,'message'=>'错误的操作！');
            }
        }
        $db->pquery("DELETE FROM vtiger_lockaccountid WHERE lockaccountid=?",array($recordId));
        return $result1;
    }
    public function setModTracker2($recordId,$array){
        $db=PearDatabase::getInstance();
        global $current_user;
        $datetime=date('Y-m-d H:i:s');
        $id= $db->getUniqueId('vtiger_modtracker_basic');
        $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id , $recordId, 'Accounts', $current_user->id, $datetime, 0));
        foreach($array as $value){
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, $value['fieldname'],$value['prevalue'], $value['postvalue']));
        }
    }
}
