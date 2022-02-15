<?php
/*+************
 * 数据记录模型 用于新增编辑
 ***************/
class Vtiger_Record_Model extends Vtiger_Base_Model {

	protected $module = false;
	public static $instanceRecord=array();

	public function getId() {
		return $this->get('id');
	}
	public function setId($value) {
		return $this->set('id',$value);
	}
	public function getName() {
		$displayName = $this->get('label');
		if(empty($displayName)) {
			$displayName = $this->getDisplayName();
		}
		return Vtiger_Util_Helper::toSafeHTML(decode_html($displayName));
	}
	public function getModule() {
		return $this->module;
	}
	/**
	 * 模块名称
	 */
	public function getModuleName() {
		return $this->getModule()->get('name');
	}

	/**
	 * 设置当前记录属于哪个module模型实例 参数为module或moduleName
	 */
	public function setModule($moduleName) {
		$this->module = Vtiger_Module_Model::getInstance($moduleName);
		return $this;
	}
	public function setModuleFromInstance($module) {
		$this->module = $module;
		return $this;
	}


	/**
	 * 当前模块名文件设置和读取如Account.php
	 */
	public function getEntity() {
		return $this->entity;
	}
	public function setEntity($entity) {
		$this->entity = $entity;
		return $this;
	}

	/**
	 * 原始数据的设置和读取
	 */
	public function getRawData() {
		return $this->rawData;
	}
	public function setRawData($data) {
		$this->rawData = $data;
		return $this;
	}

	//有关链接的方法
	/**
	 * 生成记录访问链接[关联数据详细访问授权] By Joe
	 */
	public function getDetailViewUrl() {
		$module = $this->getModule();
		$realoperate='';
		if(isset($_REQUEST['view']) && $_REQUEST['view']=='Detail'){
			$realoperate='&realoperate='.setoperate($this->getId(),$this->getModuleName());
		}
		return 'index.php?module='.$this->getModuleName().'&view='.$module->getDetailViewName().'&record='.$this->getId().$realoperate;
	}
	/**
	 * 设置完整信息访问参数
	 */
	public function getFullDetailViewUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&view='.$module->getDetailViewName().'&record='.$this->getId().'&mode=showDetailViewByMode&requestMode=full';
	}
	/**
	 * 创建编辑链接
	 */
	public function getEditViewUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&view='.$module->getEditViewName().'&record='.$this->getId();
	}
	/**
	 * 数据变更记录链接
	 */
	public function getUpdatesUrl() {
		return $this->getDetailViewUrl()."&mode=showRecentActivities&page=1&tab_label=LBL_UPDATES";
	}
	/**
	 * 删除链接
	 */
	public function getDeleteUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&action='.$module->getDeleteActionName().'&record='.$this->getId();
	}


	/**
	 * Function to get the Display Name for the record
	 * @return <String> - Entity Display Name for the record
	 */
	public function getDisplayName() {
		return getFullNameFromArray($this->getModuleName(),$this->getData());
	}
	/**
	 * Function to retieve display value for a field
	 * @param <String> $fieldName - field name for which values need to get
	 * @return <String>
	 */
	public function getDisplayValue($fieldName,$recordId = false) {
		if(empty($recordId)) {
			$recordId = $this->getId();
		}
		$fieldModel = $this->getModule()->getField($fieldName);
		if($fieldModel) {
			return $fieldModel->getDisplayValue($this->get($fieldName), $recordId, $this);
		}
		return false;
	}

	/**
	 * Function returns the Vtiger_Field_Model
	 * @param <String> $fieldName - field name
	 * @return <Vtiger_Field_Model>
	 */
	public function getField($fieldName) {
		return $this->getModule()->getField($fieldName);
	}

	/**
	 * Function returns all the field values in user format
	 * @return <Array>
	 */
	public function getDisplayableValues() {
		$displayableValues = array();
		$data = $this->getData();
		foreach($data as $fieldName=>$value) {
			$fieldValue = $this->getDisplayValue($fieldName);
			$displayableValues[$fieldName] = ($fieldValue) ? $fieldValue : $value;
		}
		return $displayableValues;
	}

	/**
	 * 数据的新增和编辑保存
	 */
	public function save() {
		$this->getModule()->saveRecord($this);
	}
	/**
	 * 删除
	 */
	public function delete() {
		$this->getModule()->deleteRecord($this);
	}

	/**
	 * 没有ID的记录模型[新增数据] 含模块tab，field，模块.php等信息 同下
	 */
	public static function getCleanInstance($moduleName) {
		$focus = CRMEntity::getInstance($moduleName);
		$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
		$instance = new $modelClassName();
		return $instance->setData($focus->column_fields)->setModule($moduleName)->setEntity($focus);
	}
	/**
	 * 基于记录ID和模块名创建的对象 [编辑数据]
	 */
	public static function getInstanceById($recordId,$module=null,$flag=false) {
		if(is_object($module) && is_a($module, 'Vtiger_Module_Model')) {
			$moduleName = $module->get('name');
		} elseif (is_string($module)) {
			$module = Vtiger_Module_Model::getInstance($module);
			$moduleName = $module->get('name');
		} elseif(empty($module)) {
			$moduleName = getSalesEntityType($recordId);
			$module = Vtiger_Module_Model::getInstance($moduleName);
		}
		if(empty(self::$instanceRecord[$moduleName.$recordId]) || $flag){
            $focus = CRMEntity::getInstance($moduleName);  //当前模块的主的文件的实例化
            $focus->id = $recordId;
            $focus->retrieve_entity_info($recordId, $moduleName);
            $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);//实例化
            $instance = new $modelClassName();
            return self::$instanceRecord[$moduleName.$recordId]=$instance->setData($focus->column_fields)->set('id',$recordId)->setModuleFromInstance($module)->setEntity($focus);
        }
        return self::$instanceRecord[$moduleName.$recordId];
	}

	/**
	 * Static Function to get the list of records matching the search key
	 * @param <String> $searchKey
	 * @return <Array> - List of Vtiger_Record_Model or Module Specific Record Model instances
	 */
	public static function getSearchResult($searchKey, $module=false) {
		$db = PearDatabase::getInstance();

		$query = 'SELECT label, crmid, setype, createdtime FROM vtiger_crmentity WHERE label LIKE ? AND vtiger_crmentity.deleted = 0';
		$params = array("%$searchKey%");

		if($module !== false) {
			$query .= ' AND setype = ?';
			$params[] = $module;
		}
		//Remove the ordering for now to improve the speed
		//$query .= ' ORDER BY createdtime DESC';

		$result = $db->pquery($query, $params);
		$noOfRows = $db->num_rows($result);

		$moduleModels = $matchingRecords = $leadIdsList = array();
		for($i=0; $i<$noOfRows; ++$i) {
			$row = $db->query_result_rowdata($result, $i);
			if ($row['setype'] === 'Leads') {
				$leadIdsList[] = $row['crmid'];
			}
		}
		$convertedInfo = Leads_Module_Model::getConvertedInfo($leadIdsList);

		for($i=0, $recordsCount = 0; $i<$noOfRows && $recordsCount<100; ++$i) {
			$row = $db->query_result_rowdata($result, $i);
			if ($row['setype'] === 'Leads' && $convertedInfo[$row['crmid']]) {
				continue;
			}
			if(Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
				$row['id'] = $row['crmid'];
				$moduleName = $row['setype'];
				if(!array_key_exists($moduleName, $moduleModels)) {
					$moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
				}
				$moduleModel = $moduleModels[$moduleName];
				$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
				$recordInstance = new $modelClassName();
				$matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
				$recordsCount++;
			}
		}
		return $matchingRecords;
	}

	/**
	 * 用户编辑权限
	 */
	public function isEditable() {
		if(isPermitted($this->getModuleName(), 'EditView', $this->getId())=='yes'){
			return true;
		}
		return false;
	}
	/**
	 * 删除权限
	 */
	public function isDeletable() {
		if(isPermitted($this->getModuleName(), 'Delete', $this->getId())=='yes'){
			return true;
		}
		return false;
	}

	/**
	 * Funtion to get Duplicate Record Url
	 * @return <String>
	 */
	public function getDuplicateRecordUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&view='.$module->getEditViewName().'&record='.$this->getId().'&isDuplicate=true';

	}
	/**
	 * Function to get Display value for RelatedList
	 * @param <String> $value
	 * @return <String>
	 */
	public function getRelatedListDisplayValue($fieldName) {
		$fieldModel = $this->getModule()->getField($fieldName);
		return $fieldModel->getRelatedListDisplayValue($this->get($fieldName));
	}


	/**
	 * 查询记录的描述信息[需关联主表]
	 * @return <String> Descrption
	 */
	public function getDescriptionValue() {
		$description = $this->get('description');
		if(empty($description)) {
			$db = PearDatabase::getInstance();
			$result = $db->pquery("SELECT description FROM vtiger_crmentity WHERE crmid = ?", array($this->getId()));
			$description =  $db->query_result($result, 0, "description");
		}
		return $description;
	}

	/**
	 * 转移数据关联
	 * @param <Array> $recordIds
	 * @return <Boolean> true/false
	 */
	public function transferRelationInfoOfRecords($recordIds = array()) {
		if ($recordIds) {
			$moduleName = $this->getModuleName();
			$focus = CRMEntity::getInstance($moduleName);
			if (method_exists($focus, 'transferRelatedRecords')) {
				$focus->transferRelatedRecords($moduleName, $recordIds, $this->getId());
			}
		}
		return true;
	}

	/**
	 *删除图片
	 */
	public function deleteImage($imageId) {
		return true;
		$db = PearDatabase::getInstance();
		$checkResult = $db->pquery('SELECT crmid FROM vtiger_seattachmentsrel WHERE attachmentsid = ?', array($imageId));
		$crmId = $db->query_result($checkResult, 0, 'crmid');
		if ($this->getId() === $crmId) {
			$db->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid = ?', array($imageId));
			$db->pquery('DELETE FROM vtiger_seattachmentsrel WHERE attachmentsid = ?', array($imageId));
			return true;
		}
		return false;
	}
    //编辑时再次验证提交变更的充值申请单是否是该原服务合同的且没有关联回款的满足条件的充值申请单
    public  function  getListAboutServiceContractRefillapplicationAginCheck($serviceContractId,$rechargesource,$record){
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT r.refillapplicationid,r.refillapplicationno,r.rechargesource,IFNULL((select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',(if(`status`=\'Active\',\'\',\'[离职]\'))) as last_name from vtiger_users where c.smownerid=vtiger_users.id),\'--\') as smownerid,c.createdtime,r.grossadvances,r.actualtotalrecharge,r.totalreceivables  FROM vtiger_refillapplication as r  LEFT JOIN vtiger_crmentity as c ON r.refillapplicationid = c.crmid WHERE  c.deleted=0 AND r.modulestatus="c_complete" AND  r.servicecontractsid =? AND r.rechargesource=? AND r.refillapplicationid IN(SELECT cd.detail_refillapplicationid FROM vtiger_changecontract_detail as cd  WHERE cd.refillapplicationid =? ) AND NOT EXISTS ( SELECT (vtiger_refillapprayment.refillapptotal-vtiger_refillapprayment.refundamount) as money FROM vtiger_refillapprayment WHERE vtiger_refillapprayment.refillapplicationid=r.refillapplicationid AND vtiger_refillapprayment.deleted=0 HAVING money>0 )', array($serviceContractId,$rechargesource,$record));
        $list=array();
        while ($rowData=$db->fetch_array($result)){
            $list[]= $rowData;
        }
        return $list;
    }
	// 获取变更服务合同原服务合同相关的 充值申请单的list数组
    public  function getListAboutServiceContractRefillapplication($serviceContractId,$rechargesource){
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT r.refillapplicationid,r.refillapplicationno,r.rechargesource,IFNULL((select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',(if(`status`=\'Active\',\'\',\'[离职]\'))) as last_name from vtiger_users where c.smownerid=vtiger_users.id),\'--\') as smownerid,c.createdtime,r.grossadvances,r.actualtotalrecharge,r.totalreceivables  FROM vtiger_refillapplication as r  LEFT JOIN vtiger_crmentity as c ON r.refillapplicationid = c.crmid WHERE  c.deleted=0 AND r.modulestatus="c_complete" AND  r.servicecontractsid =? AND r.rechargesource=? AND  NOT EXISTS ( SELECT (vtiger_refillapprayment.refillapptotal-vtiger_refillapprayment.refundamount) as money FROM vtiger_refillapprayment WHERE vtiger_refillapprayment.refillapplicationid=r.refillapplicationid AND vtiger_refillapprayment.deleted=0 HAVING money>0 )', array($serviceContractId,$rechargesource));
        $list=array();
        while ($rowData=$db->fetch_array($result)){
            $list[]= $rowData;
        }
        return $list;
	}
    // 或变更充值申请单的list数组
    public  function getListChangeRefillapplicationDetail($ids){
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT r.refillapplicationid,r.refillapplicationno,r.rechargesource,IFNULL((select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',(if(`status`=\'Active\',\'\',\'[离职]\'))) as last_name from vtiger_users where c.smownerid=vtiger_users.id),\'--\') as smownerid,c.createdtime,r.grossadvances,r.actualtotalrecharge,r.totalreceivables  FROM vtiger_refillapplication as r  LEFT JOIN vtiger_crmentity as c ON r.refillapplicationid = c.crmid WHERE r.refillapplicationid IN( SELECT detail_refillapplicationid FROM vtiger_changecontract_detail WHERE vtiger_changecontract_detail.refillapplicationid=? )', array($ids));
        $list=array();
        while ($rowData=$db->fetch_array($result)){
            $list[]= $rowData;
        }
        return $list;
    }
     /**
     * 工作流的审核主要用于移动端
     * 2015-01-08 steel 从工单数据模块迁移过来，去掉了模板的加载
     * @param Vtiger_Request $request
     */
    public function getWorkflowsMobile(Vtiger_Request $request){
        $moduleName = $request->getModule();
        $recordId=$request->get('record');
        $db=PearDatabase::getInstance();
        global $current_user;
        //$salesorderModule=Vtiger_Record_Model::getInstanceById($recordId,'SalesOrder');
        // 如果modulename 是AchievementSummary 则记录id 替换
        if($moduleName=='AchievementallotStatistic'){
            // 特殊获取审核流程中 salesorderid 即recordid 替换掉
            $sql="SELECT * FROM `vtiger_achievementallot_statistic` WHERE `achievementallotid` = ? LIMIT 1";
            $result = $db->pquery($sql,array($recordId));
            $resultDetail = $db->query_result_rowdata($result,0);
            $recordId=$resultDetail['crmid'];
        }

        //获取工作流
        $modelModule = SalesorderWorkflowStages_Record_Model::getInstanceById($recordId);
        $model=$modelModule->getAll($recordId,$moduleName);
        //$modelcount = count($model);
        $roleandworkflowsstages=getWorkflowsByUserid();
        //$temp=array();
        $isrole=0;
        if(!empty($roleandworkflowsstages)){
            $roleandworkflowsstages=explode(',',$roleandworkflowsstages);
        }else{
            $roleandworkflowsstages=array();
        }

        //页面审核按钮根据权限生成
        //$user=getAccessibleUsers('WorkFlowCheck','List',true);
        $user=Users_Privileges_Model::getInstanceById($current_user->id);
        //end
        //$stagerecordid= 0 ;
        //获取当前活动节点
        //管理员或有下级审核权限的显示审核
        $workObj=new WorkFlowCheck_ListView_Model();
        $allStagers = $workObj->getActioning($moduleName,$recordId);
        $isaction=0;
        $workflowsid=0;

        foreach($model as $key=>$val){
            if($val['isaction']==1){
                //管理员或者有审核节点
                if($current_user->is_admin=='on' || isset($allStagers[$val['salesorderworkflowstagesid']])){
                    //审核所有或下属
                    //if($user=='1=1'|| in_array($val['smcreatorid'],$user)){
                    //if(isset($allStagers[$val['salesorderworkflowstagesid']])){
                    $val['check']=1;
                    $isrole=1;

                    if(empty($stagerecordid)){
                        $stagerecordid=$val['salesorderworkflowstagesid'];
                        $stagerecordname=$val['workflowstagesname'];
                        $workflowsstageid=$val['workflowstagesid'];
                        $salesorderid=$val['salesorderid'];
                        //$_SESSION['isyourcode']=$moduleName.$recordId;//当前人有审核的权限
                        if($val['productid']){
                            $result=$db->pquery('select salesorderproductsrelid from vtiger_salesorderproductsrel where (servicecontractsid=? or salesorderid=?) and productid=?',array($salesorderid,$salesorderid,$val['productid']));
                            if($db->num_rows($result)){
                                $data=array('module'=>'SalesorderProductsrel','record'=>$db->query_result($result, 0,'salesorderproductsrelid'));
                            }
                        }else{
                            $data=array('module'=>$val['modulename'],'record'=>$val['salesorderid']);
                        }
                    }
                    //}
                }
                $workflowsid=$val['workflowsid'];
            }
            $models[$val['sequence']][$key]=$val;  //将 workflowstagesid 换成 sequence为兼容自动生成的节点没有 workflowstagesid =0；
        }
        /*if($isaction==0){
            unset($_SESSION['isyourcode']);
        }*/

        //actionid
        //获取当前活动时间
        $db=PearDatabase::getInstance();
        //获取打回历史
        $salesorderhistory = $db->pquery('SELECT last_name,rejecttime,reject,rejectname,rejectnameto FROM vtiger_salesorderhistory soh,vtiger_users user WHERE soh.rejectid=user.id and soh.salesorderid=? ORDER BY soh.salesorderhistoryid DESC', array($recordId));
        $t_salesorderhistory = array();
        while($rawData=$db->fetch_array($salesorderhistory)) {
            $t_salesorderhistory[] = $rawData;
        }

        //获取备注列表
        $remarklists = $db->pquery('SELECT salesorderhistoryid,modifytime,last_name,email1, rejecttime, reject, rejectname, rejectnameto, rejectid FROM vtiger_salesorderremark soh LEFT JOIN vtiger_users USER ON soh.rejectid = USER.id where soh.salesorderid =? ORDER BY soh.salesorderhistoryid DESC',array($recordId));
        $remarklist = array();
        while($rawData=$db->fetch_array($remarklists)) {
            $remarklist[] = $rawData;
        }
        //获取流程节点审核
        $workflowsstagelist = $db->pquery("SELECT a.workflowsname,a.workflowsid,salesorderworkflowstagesid,workflowstagesid, workflowstagesname, isaction, IF ( isaction = 2, '已审核', IF ( isaction = 1, '审核中', '未激活' )) AS actionstatus, actiontime, IF ( ishigher = 1, ( SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '<br>' ) AS last_name FROM vtiger_users WHERE vtiger_salesorderworkflowstages.higherid = vtiger_users.id ), ( SELECT ( SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '<br>' ) FROM vtiger_users WHERE id IN ( SELECT vtiger_user2role.userid FROM vtiger_user2role WHERE vtiger_user2role.roleid IN ( SELECT vtiger_role.roleid FROM vtiger_role WHERE FIND_IN_SET( vtiger_role.roleid, REPLACE ( vtiger_workflowstages.isrole, ' |##| ', ',' ))))) FROM vtiger_workflowstages WHERE vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid AND vtiger_workflowstages.isrole IN ('H102', 'H104', 'H90'))) AS higherid, IFNULL(( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_salesorderworkflowstages.auditorid = vtiger_users.id ), '--' ) AS auditorid, auditortime, createdtime, ( SELECT ( SELECT GROUP_CONCAT(rolename) FROM vtiger_role WHERE FIND_IN_SET( vtiger_role.roleid, REPLACE ( vtiger_workflowstages.isrole, ' |##| ', ',' ))) FROM vtiger_workflowstages WHERE vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid ) AS isrole, ( SELECT ( SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '<br>' ) FROM vtiger_users WHERE FIND_IN_SET( vtiger_users.id, REPLACE ( vtiger_products.productman, ' |##| ', ',' ))) FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderworkflowstages.productid ) AS productid FROM vtiger_salesorderworkflowstages left join vtiger_workflows a on a.workflowsid=vtiger_salesorderworkflowstages.workflowsid WHERE salesorderid = ? ORDER BY vtiger_salesorderworkflowstages.createdtime desc,vtiger_salesorderworkflowstages.sequence ASC",array($recordId));

        $t_workflowsstagelist = array();
        while($rawData=$db->fetch_array($workflowsstagelist)) {
            if($workflowsid==$rawData['workflowsid']){
                $t_workflowsstagelist[] = $rawData;
            }else{
                $t_completeworkflowsstagelist[$rawData['workflowsid']][] = $rawData;
            }
        }


        //注释掉，是老的代码
        /*$recordModel = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
                $moduleModel = $recordModel->getModule();
                /*$fieldList = $moduleModel->getFields();
                $requestFieldList = array_intersect_key( $fieldList);

                foreach($requestFieldList as $fieldName=>$fieldValue){
                    $fieldModel = $fieldList[$fieldName];

                    if($fieldModel->isEditable()) {
                        $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
                    }
                }*/

        //wangbin 2015年03月25日 星期三
        $sqlproject = "SELECT `projectid`,`projectname` FROM  `vtiger_project`";
        $projects = $db->pquery($sqlproject,array());

        $projectarr = array();

        while($row=$db->fetch_array($projects)){
            $projectarr[] = array($row['projectid'],$row['projectname']);
        }


        //$arr['STAGES']=$models; //工作流stagesid
        //$arr['STAGESCOUNT']=count($models);//工作流的数量
        $arr['ISROLE']=$isrole;   //是否有权限审核
        $arr['STAGERECORDID']=$stagerecordid;//当前工作流的id
        $arr['STAGERECORDNAME']=$stagerecordname;//当前工作流的名字
        $arr['SALESORDERHISTORY']=$t_salesorderhistory;//打回历史记录
        $arr['WORKFLOWSSTAGELIST']=$t_workflowsstagelist;//审核节点
        $arr['COMPLETEWORKFLOWSSTAGELIST']=$t_completeworkflowsstagelist;//已完结工作流审核节点
        $arr['REMARKLIST']=$remarklist;
        $arr['workflowsstageid']=$workflowsstageid;
        $arr['WORKFLOWSNAME']='';
        $arr['WORKFLOWSID']=$workflowsid;

        if($recordId&&$workflowsid){
            $result = $db->pquery("select b.workflowsname from vtiger_salesorderworkflowstages a left join  vtiger_workflows b on a.workflowsid=b.workflowsid where a.salesorderid=? and a.modulename=? and a.workflowsid=? limit 1",array($recordId,$moduleName,$workflowsid));
            $row = $db->fetchByAssoc($result,0);
            $arr['WORKFLOWSNAME']=$row['workflowsname'];
        }
        //$arr['DATA']=$data;
        //$arr['USER']=$user->id;
        //$arr['RECORD']=$recordId;

        //$arr['PROJECTNAME']=$projectarr;
        return $arr;
    }

    /** 发送邮件
     * @param $Subject 邮件主题
     * @param $body 邮件内容
     * @param array $address 收件人地址和名称 如：$address[] = array("mail"=>$email1,"name"=>$last_name);
     * @param array $cc 抄送人地址和名称 如：$cc[] = array("mail"=>$email1,"name"=>$last_name);
     * @param $fromname 邮件来源名称 详细参照表vtiger_systems
     * @param string $sysid 邮件来源id 1:CRM系统发送,2:.. 详细参照表vtiger_systems的id字段
     */
    public static function sendMail($Subject,$body,$address=array(),$fromname='CRM系统',$sysid='1',$cc=array()) {
        $selfThis=new self();
        $data=array('module'=>'Users',
            'action'=>'sendMailByMessageQuery',
            'mqdata'=>array(
                'Subject'=>$Subject,
                'body'=>$body,
                'address'=>$address,
                'fromname'=>$fromname,
                'sysid'=>$sysid,
                'cc'=>$cc
                )
            );
        $jsonData=json_encode($data);
        if($selfThis->rabbitMQPublisher($jsonData)){
            return ;
        };
        global $adb, $current_user;
        $query = "SELECT * FROM `vtiger_systems` WHERE server_type='email' AND id=?";
        $result = $adb->pquery($query, array($sysid));
        $result = $adb->query_result_rowdata($result, 0);
        $path = dirname(dirname(dirname(__FILE__)));

        require_once $path.'/Emails/class.phpmailer.php';
        $mailer=new PHPMailer();

        $mailer->IsSmtp();
        //$mailer->SMTPDebug = true;
        $mailer->SMTPAuth=$result['smtp_auth'];
        $mailer->Host=$result['server'];
        //$mailer->Host='smtp.qq.com';
        $mailer->SMTPSecure = "SSL";
        //$mailer->Port = $result['server_port'];
        $mailer->Username = $result['server_username'];//用户名
        $mailer->Password = $result['server_password'];//密码
        $mailer->From = $result['from_email_field'];//发件人
        $mailer->FromName = $fromname;
        //收件人地址设置
        if(empty($address) || count($address) == 0) exit;
        foreach($address as $value){
            $bl = self::checkEmail($value["mail"]);
            if($bl == false) continue;
            $mailer->AddAddress($value["mail"], $value["name"]);//收件人的地址
        }
        //抄送人地址设置
        if(!empty($cc) && count($cc) > 0) {
            foreach($cc as $value){
                $bl = self::checkEmail($value["mail"]);
                if($bl == false) continue;
                $mailer->AddCC($value["mail"], $value["name"]);//抄送人的地址
            }
        }
        $mailer->WordWrap = 100;
        $mailer->IsHTML(true);
        $mailer->Subject = $Subject;
        $mail_body = $body;
        //$mail_body .= '<br><br>&nbsp;以上,请及时处理。<br><br>';
        $mail_body .= '<br />&nbsp;<font color="red">(系统邮件,请勿回复)</font>';

        $mailer->Body = $mail_body;

        $email_flag=$mailer->Send()?'SENT':'Faile';
    }
    /** 队列方式发送邮件
     * @param $Subject 邮件主题
     * @param $body 邮件内容
     * @param array $address 收件人地址和名称 如：$address[] = array("mail"=>$email1,"name"=>$last_name);
     * @param array $cc 抄送人地址和名称 如：$cc[] = array("mail"=>$email1,"name"=>$last_name);
     * @param $fromname 邮件来源名称 详细参照表vtiger_systems
     * @param string $sysid 邮件来源id 1:CRM系统发送,2:.. 详细参照表vtiger_systems的id字段
     */
    public static function sendMailByMessageQuery($data) {
        $Subject=$data['Subject'];
        $body=$data['body'];
        $address=$data['address'];
        $fromname=$data['fromname'];
        $sysid=$data['sysid'];
        $cc=$data['cc'];
        global  $adb,$root_directory;
        try{
            $adb=PearDatabase::getInstance();
            $query = "SELECT * FROM `vtiger_systems` WHERE server_type='email' AND id=?";
            $result = $adb->pquery($query, array($sysid));
            $result = $adb->query_result_rowdata($result, 0);
        }catch (Exception $exception){
            $adb=new PearDatabase();
            $adb->connect();
            $query = "SELECT * FROM `vtiger_systems` WHERE server_type='email' AND id=?";
            $result = $adb->pquery($query, array($sysid));
            $result = $adb->query_result_rowdata($result, 0);
        }
        if(!class_exists('PHPMailer')){
            include $root_directory.'cron/class.phpmailer.php';
        }
        $mailer=new PHPMailer();

        $mailer->IsSmtp();
        //$mailer->SMTPDebug = true;
        $mailer->SMTPAuth=$result['smtp_auth'];
        $mailer->Host=$result['server'];
        //$mailer->Host='smtp.qq.com';
        $mailer->SMTPSecure = "SSL";
        //$mailer->Port = $result['server_port'];
        $mailer->Username = $result['server_username'];//用户名
        $mailer->Password = $result['server_password'];//密码
        $mailer->From = $result['from_email_field'];//发件人
        $mailer->FromName = $fromname;
        //收件人地址设置
        if(empty($address) || count($address) == 0) exit;
        foreach($address as $value){
            $bl = self::checkEmail($value["mail"]);
            if($bl == false) continue;
            $mailer->AddAddress($value["mail"], $value["name"]);//收件人的地址
        }
        //抄送人地址设置
        if(!empty($cc) && count($cc) > 0) {
            foreach($cc as $value){
                $bl = self::checkEmail($value["mail"]);
                if($bl == false) continue;
                $mailer->AddCC($value["mail"], $value["name"]);//抄送人的地址
            }
        }
        $mailer->WordWrap = 100;
        $mailer->IsHTML(true);
        $mailer->Subject = $Subject;
        $mail_body = $body;
        //$mail_body .= '<br><br>&nbsp;以上,请及时处理。<br><br>';
        $mail_body .= '<br />&nbsp;<font color="red">(系统邮件,请勿回复)</font>';

        $mailer->Body = $mail_body;

        $email_flag=$mailer->Send()?'SENT':'Faile';
        return array($email_flag,$mailer->ErrorInfo);
    }
    public static function checkEmail($str){
        $str=trim($str);
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }
    /**
     * 取模块对应的个人权限
     * @param $module
     * @param $classname
     * @param int $id
     * @return bool
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function personalAuthority($module,$classname,$id=0){
        if($id==0)
        {
            global $current_user;
            $id = $current_user->id;
        }
        $returnData = Vtiger_Cache::get('PERSONALAUTHORITY','GETPERSONALAUTHORITY');
        if(!$returnData){
            global $adb;
            $db=PearDatabase::getInstance();
            $query="SELECT userid,classname,module FROM vtiger_exportmanage WHERE deleted=0";
            $result=$db->pquery($query,array());
            $returnData=array();
            while($row=$adb->fetchByAssoc($result)){
                $returnData[]=$row['classname'].$row['userid'].$row['module'];
            }
            Vtiger_Cache::set('PERSONALAUTHORITY','GETPERSONALAUTHORITY',$returnData);
        }
        $searchkey=$classname.$id.$module;
        if(in_array($searchkey,$returnData)){
            return true;
        }
        return false;
    }
    /**
     * 取模块对应的个人权限，移动端调用
     * @param $module
     * @param $classname
     * @param int $id
     * @return bool
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function personalAuthorityMobile($request){
        $module=$request->get('modulename');
        $classname=$request->get('classname');
        $userid=$request->get('userid');
        return $this->personalAuthority($module,$classname,$userid);
    }
    /**
     * 微信企业号信息
     * @param Vtiger_Request $request
     */
    public function sendWechatMessage($data){
        global $m_crm_url;
        $userkey='c0b3Ke0Q4c%2BmGXycVaQ%2BUEcbU0ldxTBeeMAgUILM0PK5Q59cEp%2B40n6qUSJiPQ';
        $mqdata=array('module'=>'Users',
            'action'=>'sendWechatByMessageQuery',
            'mqdata'=>$data
        );
        $jsonData=json_encode($mqdata);
        if($this->rabbitMQPublisher($jsonData)){
            return ;
        }
        //$url = $m_crm_url."/api.php";
        $url = $m_crm_url."/api.php";
        $ch  = curl_init();
        $data['tokenauth']=$userkey;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);
    }
    /**
     * 微信企业号信息
     * @param Vtiger_Request $request
     */
    public function sendWechatByMessageQuery($data){
        global $m_crm_url;
        $userkey='c0b3Ke0Q4c%2BmGXycVaQ%2BUEcbU0ldxTBeeMAgUILM0PK5Q59cEp%2B40n6qUSJiPQ';

        //$url = $m_crm_url."/api.php";
        $url =$m_crm_url."/api.php";
        $ch  = curl_init();
        $data['tokenauth']=$userkey;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);
    }
    /**
     * 添加更新记录
     * @param $sourceModule
     * @param $sourceId
     * @param $array
     * @param string $table
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function setModTracker($sourceModule, $sourceId, $array,$table='') {
        global $adb, $current_user;
        $currentTime = date('Y-m-d H:i:s');
        if(!empty($table)){
            $sql = "SELECT * FROM {$table['tablename']} WHERE {$table['fieldName']}=? LIMIT 1";
            $sel_result = $adb->pquery($sql, array($sourceId));
            if($adb->num_rows($sel_result)){
                $row = $adb->query_result_rowdata($sel_result, 0);
                foreach($array as $key=>$value){
                    $array[$key]['oldValue']=$row[$key];
                }
            }
        }
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $sourceId, $sourceModule, $current_user->id, $currentTime, 0));
        $sql='INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)';
        foreach($array as $key=>$value){
            $adb->pquery($sql,
                Array($id, $key, $value['oldValue'], $value['currentValue']));
        }
    }

    /**
     * rabbitmq消息发布者
     * @param $data
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     */
    public function rabbitMQPublisher($data){
        global $rabbitmqConfig;
        $flag=true;
        if(!class_exists('AMQPConnection')){
            return false;
        }
        try {
            $conn = new AMQPConnection($rabbitmqConfig['config']);
            if ($conn->connect()) {
                $channel = new AMQPChannel($conn);
                $ex = new AMQPExchange($channel);
                $ex->setName($rabbitmqConfig['exchangeName']);
                $ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
                $ex->setFlags(AMQP_DURABLE); //持久化
                $ex->declareExchange();
                $ex->publish($data, $rabbitmqConfig['routeName']);
                $conn->disconnect();
            }else{
                $flag=false;
            }
        }catch (Exception $e){
            $flag=false;
        }
        return $flag;
    }
    public function https_requestcomm($url,$data=null,$curlset=null,$islog=false){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        if($islog){
            $this->comm_logs(array('请求URL',$url));
            $this->comm_logs(array('发送DATA',$data));
            $this->comm_logs(array('返回DATA',$output));
        }
        return $output;
    }
    /**
     * 写日志，用于测试,可以开启关闭
     * @param data mixed
     */
    public function comm_logs($data, $file = 'logs_'){
		global $root_directory;
        $year	= date("Y");
        $month	= date("m");
        $dir	= $root_directory.'logs/common/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '---------' . date('H:i:s') . '-------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }

    /**
     * 文件上传
     * @param $url
     * @param $path
     * @param $minetype
     * @param $postname
     * @param array $curlset
     * @return bool|string
     */
    public function CURLfileUpload($url,$path,$minetype,$postname,$curlset=array(),$islog=false){
        //1.初识化curl
        $curl = curl_init();
        if (class_exists('CURLFile')) {
            $data = array('file' => new CURLFile(realpath($path),$minetype,$postname));//>=5.5
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
            $data = array('file' => '@' . realpath($path));//<=5.5
        }
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        //curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, true );
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true );
        curl_setopt($curl, CURLOPT_TIMEOUT, 100 );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output=curl_exec($curl);
        curl_close($curl);
        if($islog){
            $this->comm_logs(array('请求URL',$url));
            $this->comm_logs(array('发送DATA',$data));
            $this->comm_logs(array('返回DATA',$output));
        }
        return $output;
    }
    /**
     * CURL数据发送
     * @param $absUrl
     * @param $params
     * @param array $headers
     * @param string $method
     * @param bool $islog
     * @return array
     */
    public function https_requestcomm2($absUrl,$params, $headers=array(),$islog=false,$method="post")
    {
        $curl = curl_init();
        $method = strtolower($method);
        $opts = array();
        $requestTime = null;
        if ($method === 'get' || $method === 'delete') {
            if ($method === 'get') {
                $opts[CURLOPT_HTTPGET] = 1;
            } else {
                $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            }
            if (count($params) > 0) {
                $encoded =http_build_query($params);
                $absUrl = "$absUrl?$encoded";
            }
        } elseif ($method === 'post' || $method === 'put') {
            if ($method === 'post') {
                $opts[CURLOPT_POST] = 1;
            } else {
                $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            }
            $rawRequestBody = $params !== null ? json_encode($params,JSON_UNESCAPED_UNICODE) : '';
            $opts[CURLOPT_POSTFIELDS] = $rawRequestBody;
        } else {
            throw new Error\Api("Unrecognized method $method");
        }
        $opts[CURLOPT_URL] = $absUrl;         //设置路径，也可以在curl_init()初始化回话的时候
        $opts[CURLOPT_RETURNTRANSFER] = true; //true代表将curl_exec()获取的信息以字符串返回，而不是直接输出
        $opts[CURLOPT_CONNECTTIMEOUT] = 30;   //在尝试连接时等待的秒数。设置为0，则无限等待
        $opts[CURLOPT_TIMEOUT] = 80;          //允许 cURL 函数执行的最长秒数
        $opts[CURLOPT_HTTPHEADER] = $headers; //设置 HTTP 头字段的数组。格式： array('Content-type: text/plain','Content-                              length: 100')

        $opts[CURLOPT_SSL_VERIFYPEER] = false; //FALSE禁止 cURL 验证对等证书
        $opts[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($curl, $opts);
        $rbody = curl_exec($curl);
        if (!defined('CURLE_SSL_CACERT_BADFILE')) {
            define('CURLE_SSL_CACERT_BADFILE', 77);  // constant not defined in PHP
        }
        $errno = curl_errno($curl);
        if ($errno == CURLE_SSL_CACERT ||
            $errno == CURLE_SSL_PEER_CERTIFICATE ||
            $errno == CURLE_SSL_CACERT_BADFILE) {
            array_push(
                $headers,
                'X-Pingpp-Client-Info: {"ca":"using Pingpp-supplied CA bundle"}'
            );
            $cert = $this->caBundle();
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_CAINFO, $cert);
            $rbody = curl_exec($curl);
        }

        $rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if($islog){
            $this->comm_logs(array('请求URL',$absUrl));
            $this->comm_logs(array('发送DATA',$params));
            $this->comm_logs(array('发送DATA',json_encode($params,JSON_UNESCAPED_UNICODE)));
            $this->comm_logs(array('返回DATA',array($rbody, $rcode)));
        }
        return array($rbody, $rcode);
    }
    /**
     * 获取在职员工列表
     * @return array
     */
    public function getUserIDName(){
        global $adb;
        $query="SELECT id,CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE `status` = 'Active'";
        return $adb->run_query_allrecords($query);
    }

    /**
     * bcustomer
     * @return array|bool|mixed
     */
    public function getBcustomer(){
        $returnData = Vtiger_Cache::get('BCUSTOMER','BCUSTOMER1');
        if(!$returnData){
            global $adb;
            $db=PearDatabase::getInstance();
            $query='SELECT DISTINCT contractid FROM vtiger_activationcode WHERE `status` in(0,1) AND isbcustomer=1';
            $result=$db->pquery($query,array());
            $returnData=array();
            while($row=$adb->fetchByAssoc($result)){
                $returnData[]=$row['contractid'];
            }
            Vtiger_Cache::set('BCUSTOMER','BCUSTOMER1',$returnData);
        }
        return $returnData;
    }
}
