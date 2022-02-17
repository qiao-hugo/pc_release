<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Leads_Record_Model extends Vtiger_Record_Model {

	/**
	 * Function returns the url for converting lead
	 */
	function getConvertLeadUrl() {
		return 'index.php?module='.$this->getModuleName().'&view=ConvertLead&record='.$this->getId();
	}

	/**
	 * Static Function to get the list of records matching the search key
	 * @param <String> $searchKey
	 * @return <Array> - List of Vtiger_Record_Model or Module Specific Record Model instances
	 */
	public static function getSearchResult($searchKey, $module=false) {

		$db = PearDatabase::getInstance();

		//$deletedCondition = $this->getModule()->getDeletedRecordCondition();
		$deletedCondition = self::getModule()->getDeletedRecordCondition();
		$query = 'SELECT * FROM vtiger_crmentity
                    INNER JOIN vtiger_leaddetails ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid
                    WHERE label LIKE ? AND '.$deletedCondition;
		$params = array("%$searchKey%");
		$result = $db->pquery($query, $params);
		$noOfRows = $db->num_rows($result);

		$moduleModels = array();
		$matchingRecords = array();
		for($i=0; $i<$noOfRows; ++$i) {
			$row = $db->query_result_rowdata($result, $i);
			$row['id'] = $row['crmid'];
			$moduleName = $row['setype'];
			if(!array_key_exists($moduleName, $moduleModels)) {
				$moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
			}
			$moduleModel = $moduleModels[$moduleName];
			$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
			$recordInstance = new $modelClassName();
			$matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
		}
		return $matchingRecords;
	}

	/**
	 * Function returns Account fields for Lead Convert
	 * @return Array
	 */
	function getAccountFieldsForLeadConvert() {
		$accountsFields = array();
		$privilegeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleName = 'Accounts';

		if(!Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
			return;
		}

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		if ($moduleModel->isActive()) {
			$fieldModels = $moduleModel->getFields();
            //Fields that need to be shown
            $complusoryFields = array('industry');
			foreach ($fieldModels as $fieldName => $fieldModel) {
				if($fieldModel->isMandatory() && $fieldName != 'assigned_user_id') {
                    $keyIndex = array_search($fieldName,$complusoryFields);
                    if($keyIndex !== false) {
                        unset($complusoryFields[$keyIndex]);
                    }
					$leadMappedField = $this->getConvertLeadMappedField($fieldName, $moduleName);
					$fieldModel->set('fieldvalue', $this->get($leadMappedField));
					$accountsFields[] = $fieldModel;
				}
			}
            foreach($complusoryFields as $complusoryField) {
                $fieldModel = Vtiger_Field_Model::getInstance($complusoryField, $moduleModel);
				if($fieldModel->getPermissions('readwrite')) {
                    $industryFieldModel = $moduleModel->getField($complusoryField);
                    $industryLeadMappedField = $this->getConvertLeadMappedField($complusoryField, $moduleName);
                    $industryFieldModel->set('fieldvalue', $this->get($industryLeadMappedField));
                    $accountsFields[] = $industryFieldModel;
                }
            }
		}

		return $accountsFields;
	}

	/**
	 * Function returns Contact fields for Lead Convert
	 * @return Array
	 */
	function getContactFieldsForLeadConvert() {
		$contactsFields = array();
		$privilegeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleName = 'Contacts';

		if(!Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
			return;
		}

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		if ($moduleModel->isActive()) {
			$fieldModels = $moduleModel->getFields();
            $complusoryFields = array('firstname', 'email');
            foreach($fieldModels as $fieldName => $fieldModel) {
                if($fieldModel->isMandatory() &&  $fieldName != 'assigned_user_id') {
                    $keyIndex = array_search($fieldName,$complusoryFields);
                    if($keyIndex !== false) {
                        unset($complusoryFields[$keyIndex]);
                    }

                    $leadMappedField = $this->getConvertLeadMappedField($fieldName, $moduleName);
                    $fieldValue = $this->get($leadMappedField);
                    if ($fieldName === 'account_id') {
                        $fieldValue = $this->get('company');
                    }
                    $fieldModel->set('fieldvalue', $fieldValue);
                    $contactsFields[] = $fieldModel;
                }
            }

			foreach($complusoryFields as $complusoryField) {
                $fieldModel = Vtiger_Field_Model::getInstance($complusoryField, $moduleModel);
				if($fieldModel->getPermissions('readwrite')) {
					$leadMappedField = $this->getConvertLeadMappedField($complusoryField, $moduleName);
					$fieldModel = $moduleModel->getField($complusoryField);
					$fieldModel->set('fieldvalue', $this->get($leadMappedField));
					$contactsFields[] = $fieldModel;
				}
			}
		}
		return $contactsFields;
	}

	/**
	 * Function returns Potential fields for Lead Convert
	 * @return Array
	 */
	function getPotentialsFieldsForLeadConvert() {
		$potentialFields = array();
		$privilegeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleName = 'Potentials';

		if(!Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
			return;
		}

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		if ($moduleModel->isActive()) {
			$fieldModels = $moduleModel->getFields();

            $complusoryFields = array('amount');
			foreach($fieldModels as $fieldName => $fieldModel) {
				if($fieldModel->isMandatory() &&  $fieldName != 'assigned_user_id' && $fieldName != 'related_to'
						&& $fieldName != 'contact_id') {
                    $keyIndex = array_search($fieldName,$complusoryFields);
                    if($keyIndex !== false) {
                        unset($complusoryFields[$keyIndex]);
                    }
					$leadMappedField = $this->getConvertLeadMappedField($fieldName, $moduleName);
					$fieldModel->set('fieldvalue', $this->get($leadMappedField));
					$potentialFields[] = $fieldModel;
				}
			}
            foreach($complusoryFields as $complusoryField) {
                $fieldModel = Vtiger_Field_Model::getInstance($complusoryField, $moduleModel);
                if($fieldModel->getPermissions('readwrite')) {
                    $fieldModel = $moduleModel->getField($complusoryField);
                    $amountLeadMappedField = $this->getConvertLeadMappedField($complusoryField, $moduleName);
                    $fieldModel->set('fieldvalue', $this->get($amountLeadMappedField));
                    $potentialFields[] = $fieldModel;
                }
            }
		}
		return $potentialFields;
	}

	/**
	 * Function returns field mapped to Leads field, used in Lead Convert for settings the field values
	 * @param <String> $fieldName
	 * @return <String>
	 */
	function getConvertLeadMappedField($fieldName, $moduleName) {
		$mappingFields = $this->get('mappingFields');

		if (!$mappingFields) {
			$db = PearDatabase::getInstance();
			$mappingFields = array();

			$result = $db->pquery('SELECT * FROM vtiger_convertleadmapping', array());
			$numOfRows = $db->num_rows($result);

			$accountInstance = Vtiger_Module_Model::getInstance('Accounts');
			$accountFieldInstances = $accountInstance->getFieldsById();

			//$contactInstance = Vtiger_Module_Model::getInstance('Contacts');
			//$contactFieldInstances = $contactInstance->getFieldsById();

			//$potentialInstance = Vtiger_Module_Model::getInstance('Potentials');
			//$potentialFieldInstances = $potentialInstance->getFieldsById();

			$leadInstance = Vtiger_Module_Model::getInstance('Leads');
			$leadFieldInstances = $leadInstance->getFieldsById();

			for($i=0; $i<$numOfRows; $i++) {
				$row = $db->query_result_rowdata($result,$i);

				if(empty($row['leadfid'])) continue;

				$leadFieldInstance = $leadFieldInstances[$row['leadfid']];
				if(!$leadFieldInstance) continue;

				$leadFieldName = $leadFieldInstance->getName();
				$accountFieldInstance = $accountFieldInstances[$row['accountfid']];
				if ($row['accountfid'] && $accountFieldInstance) {
					$mappingFields['Accounts'][$accountFieldInstance->getName()] = $leadFieldName;
				}
				/*$contactFieldInstance = $contactFieldInstances[$row['contactfid']];
				if ($row['contactfid'] && $contactFieldInstance) {
					$mappingFields['Contacts'][$contactFieldInstance->getName()] = $leadFieldName;
				}
				$potentialFieldInstance = $potentialFieldInstances[$row['potentialfid']];
				if ($row['potentialfid'] && $potentialFieldInstance) {
					$mappingFields['Potentials'][$potentialFieldInstance->getName()] = $leadFieldName;
				}*/
			}
			$this->set('mappingFields', $mappingFields);
		}
		return $mappingFields[$moduleName][$fieldName];
	}

	/**
	 * Function returns the fields required for Lead Convert
	 * @return <Array of Vtiger_Field_Model>
	 */
	function getConvertLeadFields() {
		$convertFields = array();
		$accountFields = $this->getAccountFieldsForLeadConvert();
		if(!empty($accountFields)) {
			$convertFields['Accounts'] = $accountFields;
		}
        /*
		$contactFields = $this->getContactFieldsForLeadConvert();
		if(!empty($contactFields)) {
			$convertFields['Contacts'] = $contactFields;
		}

		$potentialsFields = $this->getPotentialsFieldsForLeadConvert();
		if(!empty($potentialsFields)) {
			$convertFields['Potentials'] = $potentialsFields;
		}
        */
		return $convertFields;
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
	 * @return <String>
	 */
	function getCreateTaskUrl() {
		$calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
		return $calendarModuleModel->getCreateTaskRecordUrl().'&parent_id='.$this->getId();
	}
    //steel新添加方法
    public function handleLeads(){

    }

    /**
     * 来自ModComments_Record_Model
     * @param $parentRecordId
     * @param $pagingModel
     * @param string $moduleName
     * @return array
     * @throws Exception
     */
    /**
     * 获取联系人
     * @return multitype:unknown
     */
    static public function getModcommentContacts($parentId){
        $db = PearDatabase::getInstance();
        $arr=array();
        //young.yang
        $result=$db->pquery('select leadid,lastname from vtiger_leaddetails where leadid=? limit 1',array($parentId));
        if($db->num_rows($result)) {
            $row = $db->query_result_rowdata($result, 0);
            if(!empty($row['lastname'])){
                $tmp['contactid']=$row['leadid'];
                $tmp['name']=$row['lastname'];

                $arr[]=$tmp;
            }
        }

        return $arr;
    }
    /**
     * 来自ModComments_Record_Model
     * Function returns latest comments for parent record
     * @param <Integer> $parentRecordId - parent record for which latest comment need to retrieved
     * @param <Vtiger_Paging_Model> - paging model
     * @return ModComments_Record_Model if exits or null
     */
    public static function getRecentComments($parentRecordId, $pagingModel,$moduleName=''){
        $db = PearDatabase::getInstance();
        $startIndex = $pagingModel->getStartIndex();
        $limit = $pagingModel->getPageLimit();

        /* $listView = Vtiger_ListView_Model::getInstance('ModComments');
        $queryGenerator = $listView->get('query_generator');
        $queryGenerator->setFields(array('commentcontent', 'addtime', 'related_to', 'creatorid',
                                    'modcommenttype', 'modcommentmode', 'modcommenthistory','modcommentpurpose'));

        $query = $queryGenerator->getQuery(); */

        $query = "SELECT vtiger_modcomments.commentcontent, vtiger_modcomments.addtime,
				vtiger_modcomments.related_to, vtiger_modcomments.creatorid, vtiger_modcomments.modcommenttype,
				vtiger_modcomments.modcommentmode, vtiger_modcomments.modcommenthistory, vtiger_modcomments.modcommentpurpose,
				vtiger_modcomments.modcommentsid,vtiger_modcomments.contact_id,IFNULL((select name from vtiger_contactdetails where contactid=vtiger_modcomments.contact_id),IFNULL((select lastname from vtiger_leaddetails where leadid=vtiger_modcomments.related_to ),'-')) as lastname,
				'-' as shouyao
				FROM vtiger_modcomments WHERE  ";

        //客户判断
        if($moduleName == 'Accounts'){
            $query = $query ."  vtiger_modcomments.related_to = ?  ORDER BY modcommentsid DESC
			LIMIT $startIndex, $limit";
        }else{
            $query = $query ." vtiger_modcomments.related_to = ?  ORDER BY modcommentsid DESC
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
    public function batchSendAllocateMail($leadids,$newSmownerId){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("SELECT
	c.smownerid AS olduserid,
	a.company,
	a.lastname,
	d.mobile 
FROM
	vtiger_leaddetails a
	left join vtiger_crmentity c on c.crmid=a.leadid
	left join vtiger_leadaddress d on d.leadaddressid=a.leadid
WHERE
	a.leadid in(".implode(',',$leadids).") ",array());
        while ($row=$db->fetchByAssoc($result)){
            $datas[]=$row;
        }

        foreach ($datas as $data){
            $_REQUES['record']='';
            $request= new Vtiger_Request($_REQUES,$_REQUES);
            $request->set("assigned_user_id",$newSmownerId);
            $request->set("olduserid",$data['olduserid']);
            $request->set("company",$data['company']);
            $request->set("mobile",$data['mobile']);
            $this->sendThemail($request,$newSmownerId);
            if($data['olduserid']){
                $this->sendChangeMailToOldOwner($data['olduserid'],$data['company']);
            }
        }
    }

    public function sendThemail($request,$id){
        global $adb;
        $mailconfig=self::getSettingMail();
        $assigned_user_id=$request->get('assigned_user_id');
        $query1 = "SELECT last_name, email1, email2,(SELECT email1 FROM `vtiger_users` AS a WHERE a.id = vtiger_users.reports_to_id ) AS acc, ( SELECT last_name FROM `vtiger_users` AS a WHERE a.id = vtiger_users.reports_to_id ) AS accname FROM `vtiger_users` WHERE id =?";
        $result1 = $adb->pquery($query1, array($assigned_user_id));
        if (!$adb->num_rows($result1)) {
            return;
        }
        $result1 = $adb->query_result_rowdata($result1);

        $address[]=array(
            'mail'=>trim($result1['email1']),
            'name'=>trim($result1['last_name']),
        );
        $Subject = $request->get('company') . '--商机分配跟进(系统邮件请勿回复)';
        $query2 = "SELECT vtiger_leaddetails.company,vtiger_leaddetails.sourcecategory,vtiger_leaddetails.locationcity,vtiger_leaddetails.locationprovince,vtiger_leaddetails.purproduct as leadstype,vtiger_leaddetails.company,vtiger_leadaddress.phone,vtiger_leaddetails.lastname,vtiger_leadaddress.mobile,vtiger_leadaddress.fax,vtiger_leaddetails.email,vtiger_leaddetails.leadsource,vtiger_leadsubdetails.website,vtiger_leaddetails.industry,vtiger_leaddetails.annualrevenue,IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid,vtiger_crmentity.smownerid as smownerid_owner,(select createdtime from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_leaddetails.leadid and vtiger_crmentity.deleted=0) as createdtime,(select modifiedtime from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_leaddetails.leadid and vtiger_crmentity.deleted=0) as modifiedtime,IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.modifiedby=vtiger_users.id),'--') as modifiedby,vtiger_crmentity.modifiedby as modifiedby_reference,(select description from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_leaddetails.leadid and vtiger_crmentity.deleted=0) as description,vtiger_leaddetails.address,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_leaddetails.assigner=vtiger_users.id) as assigner,vtiger_leaddetails.assignerstatus,vtiger_leaddetails.conversiontime,vtiger_leaddetails.allocatetime,vtiger_leaddetails.leadsourcetnum,vtiger_leaddetails.qq,vtiger_leaddetails.leadid FROM vtiger_leaddetails  LEFT JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid LEFT JOIN vtiger_leadsubdetails ON vtiger_leaddetails.leadid = vtiger_leadsubdetails.leadsubscriptionid LEFT JOIN vtiger_leadaddress ON vtiger_leaddetails.leadid = vtiger_leadaddress.leadaddressid LEFT JOIN vtiger_leadscf ON vtiger_leaddetails.leadid = vtiger_leadscf.leadid WHERE 1=1 and vtiger_crmentity.deleted=0 AND vtiger_leaddetails.leadid=?";
        $result2 = $adb->pquery($query2, array($id));
        $lng = translateLng("Leads");
        $str='';
        if ($adb->num_rows($result2) > 0){
            $str = '<table cellpadding="0" cellspacing="0" broder="1" style="border-collapse : collapse;border:1px solid #666;width:600px;">
                <thead>
                    <tr>
                        <th colspan="2" style="border-bottom:1px solid #666;">&nbsp;商机信息</th>
                    </tr>
                </thead>
                <tbody>
                   <tr>
                        <td style="border-bottom:1px solid #666;text-align:right;"><label>定位城市</label></td>
                        <td style="border-bottom:1px solid #666;text-align:left;"><span>&nbsp;&nbsp;'.$adb->query_result($result2,0,'locationprovince').'-'.
                $adb->query_result($result2,0,'locationcity').' </span></td>
                    </tr>
                       <tr>
                        <td style="border-bottom:1px solid #666;text-align:right;"><label>线索来源</label></td>
                        <td style="border-bottom:1px solid #666;text-align:left;"><span>&nbsp;&nbsp;'.$adb->query_result($result2,0,'leadsource').' </span></td>
                    </tr>
                       <tr>
                        <td style="border-bottom:1px solid #666;text-align:right;"><label>来源号码</label></td>
                        <td style="border-bottom:1px solid #666;text-align:left;"><span>&nbsp;&nbsp;'.$adb->query_result($result2,0,'leadsourcetnum').' </span></td>
                    </tr>
                       <tr>
                        <td style="border-bottom:1px solid #666;text-align:right;"><label>来源分类</label></td>
                        <td style="border-bottom:1px solid #666;text-align:left;"><span>&nbsp;&nbsp;'.$adb->query_result($result2,0,'sourcecategory').' </span></td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid #666;text-align:right;"><label>业务划分</label></td>
                        <td style="border-bottom:1px solid #666;text-align:left;"><span>&nbsp;&nbsp;'.$lng[$adb->query_result($result2,0,'leadstype')].' </span></td>
                    </tr>'.

                '<tr>
                        <td colspan="2" style="border-bottom:1px solid #666;text-align:center;"><label style="font-size:20px;">系统邮件请勿回复</label></td>
                    </tr>
                </tbody>
            </table>';
        }
        $Body='<div>
                <div><font size="2" face="Verdana"><font size="2" face="微软雅黑"><font size="2" face="微软雅黑"><span style="COLOR: #000000">Dear
                '. $result1['last_name'].'</span></font></font></font></div>
                <blockquote style="MARGIN-TOP: 0px; PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt" id="ntes-flashmail-quote">
                  <div>
                  <blockquote style="MARGIN-TOP: 0px; PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt"><font size="2" face="Verdana"><font size="2" face="微软雅黑"></font></font><font size="2" face="Verdana">
                    </font><div><font size="2" face="Verdana">
                    </font>
                    <div><font size="2" face="微软雅黑">
                    市场部为您分配一条商机信息：公司名称:<a href="http://192.168.1.3/index.php?module=Leads&view=Detail&record='.$id.'">'.$adb->query_result($result2,0,'company').'</a>   联系人:'.$request->get('lastname').'  【请在客户】=>【商机中跟进】</font></div></div></blockquote></div></blockquote></div>
                    <div>'.$str.'</div>
                <font size="2" face="Verdana">
                <blockquote style="PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt"><font size="2" face="Verdana"></font>
                  <blockquote style="PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt"><font size="2" face="Verdana">


                    <span>

                    <div><font size="2"><font face="微软雅黑"><span></span></font></font>&nbsp;</div>
                    <div><font size="3" face="微软雅黑"><span></span></font>&nbsp;</div></span>
                    <div align="center"><font color="#c0c0c0" size="2" face="Verdana"></font>&nbsp;</div>
                    <div align="left"><font color="#c0c0c0" size="2" face="Verdana">'.date('Y-m-d').'</font></div>
                    <div align="left"><font size="2" face="Verdana">
                    <hr style="WIDTH: 122px; HEIGHT: 2px" id="SignNameHR" align="left" size="2">
                    </font></div>
                <div align="left"><font size="2" face="微软雅黑"><span>
                    <p style="LINE-HEIGHT: 16.5pt; MARGIN: 0cm 0cm 0pt; FONT-FAMILY: 宋体; FONT-SIZE: 12pt; WORD-BREAK: break-all" class="MsoNormal"><span style="FONT-SIZE: 10pt" lang="EN-US"></span></p></span></font></div>
                    <div align="left"><font color="#c0c0c0" size="2" face="Verdana"><span></span></font>&nbsp;</div></font></blockquote></blockquote></font>';

        $cc=array();
        if($mailconfig['departmentdesignated']==1) {//部门指定的邮件指收人
            $reportsModel = Users_Privileges_Model::getInstanceById($assigned_user_id);
            $query3 = "SELECT last_name, email1, email2 FROM `vtiger_users` WHERE id in(SELECT userid FROM `vtiger_sendmail_leads` WHERE `status`='c_allocated' AND module='Leads' AND departmentid in('" . str_replace('::', "','", $reportsModel->current_user_parent_departments) . "'))";
            $resulta = $adb->run_query_allrecords($query3);
            if (!empty($resulta)) {
                foreach ($resulta as $value) {
                    $cc[]=array(
                        'mail'=>trim($value['email1']),
                        'name'=>trim($value['last_name']),
                    );
                }
            }
        }


        if($mailconfig['fixedpersonnel']==1) {//固定的收件人
            $query3 = "SELECT last_name, email1, email2 FROM `vtiger_users` LEFT JOIN vtiger_sendmail_leads ON vtiger_sendmail_leads.userid=vtiger_users.id WHERE  vtiger_sendmail_leads.`status`='c_fixed' AND module='Leads'";
            $resulta = $adb->run_query_allrecords($query3);
            if (!empty($resulta)) {
                foreach ($resulta as $value) {
                    $cc[]=array(
                        'mail'=>trim($value['email1']),
                        'name'=>trim($value['last_name']),
                    );
                }
            }
        }
        if($mailconfig['oldsmower']==1) {
            if (!$request->isEmpty('olduserid')) {//原来的负责
                $queryother = "SELECT last_name, email1 FROM `vtiger_users` WHERE id =?";
                $resultother = $adb->pquery($queryother, array($request->get('olduserid')));
                if ($adb->num_rows($resultother)) {
                    $resultothers = $adb->query_result_rowdata($resultother);
                    $cc[]=array(
                        'mail'=>trim($resultothers['email1']),
                        'name'=>trim($resultothers['last_name']),
                    );
                }
            }
        }
        if($mailconfig['smower']==1 && $mailconfig['reportto']==1){
            $cc[]=array(
                'mail'=>trim($result1['acc']),
                'name'=>trim($result1['accname']),
            );
        }

        $this->_logs(array("邮件信息",'subject'=>$Subject,'address'=>$address,'cc'=>$cc));
        Vtiger_Record_Model::sendMail($Subject,$Body,$address,'CRM系统','1',$cc);

        $this->sendFollowWx(array($assigned_user_id),$adb->query_result($result2,0,'company'),$request->get("lastname"),$request->get("mobile"));
    }

    public function sendFollowWx($notifyUserId,$company,$name,$mobile){
        $db=PearDatabase::getInstance();
        $result2 = $db->pquery("select email1,wechatid from vtiger_users where id in(".implode(',',$notifyUserId).')',array());
        if(!$db->num_rows($result2)){
            return;
        }
        while ($row2=$db->fetchByAssoc($result2)){
            $email = $row2['email1'].'|';
        }

        $title = '线索分配提醒';
        $content = '市场部为您分配一条商机信息,<br>公司名称:'.$company.'<br>联系人:'.$name.'<br>手机号:'.$mobile.'<br>详细信息可登录【客户】-【商机】页面上查看';
        $this->_logs(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>$title,'flag'=>7));
        $this->sendWechatMessage(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>$title,'flag'=>7));
    }

    public function sendForceRelatedWx($notifyUserId,$company){
        $db=PearDatabase::getInstance();
        $result2 = $db->pquery("select email1,wechatid from vtiger_users where id in(".implode(',',$notifyUserId).')',array());
        if(!$db->num_rows($result2)){
            return;
        }
        while ($row2=$db->fetchByAssoc($result2)){
            $email = $row2['email1'].'|';
        }

        $title = '客户强制关联提醒';
        $content = '市场部已对该客户进行强制关联，<br>公司名称:'.$company.'<br>详细信息可登录【客户】-【商机】页面上查看';
        $this->_logs(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>$title,'flag'=>7));
        $this->sendWechatMessage(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>$title,'flag'=>7));
    }

    public function sendSmownerChangeWx($notifyUserId,$company){
        $db=PearDatabase::getInstance();
        $result2 = $db->pquery("select email1,wechatid from vtiger_users where id in(".implode(',',$notifyUserId).')',array());
        if(!$db->num_rows($result2)){
            return;
        }
        while ($row2=$db->fetchByAssoc($result2)){
            $email = $row2['email1'].'|';
        }

        $title = '市场部已对该商机进行负责人变更';
        $content = '市场部已对该客户进行强制关联，'.$company.'<br>详细信息可登录【客户】-【商机】页面上查看';
        $this->_logs(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>$title,'flag'=>7));
        $this->sendWechatMessage(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>$title,'flag'=>7));
    }

    /**
     * 获取当前邮件设置的信息
     * @return mixed
     */
    public static function getSettingMail(){
        global $adb;
        $query="SELECT allocationaftertracking,longesttracking,smower,reportto,fixedpersonnel,departmentdesignated,oldsmower FROM vtiger_sendmail_lead_setting WHERE sendmailleadsettingid=1";
        $resulta=$adb->run_query_allrecords($query);
        return $resulta[0];
    }

    public function sendThemailRELATED(Vtiger_Request $request,$array){
        global $adb;
        $query1 = "SELECT last_name, email1, email2 FROM `vtiger_users` WHERE id in(".implode(',',$array['userid']).")";
        $result1 = $adb->run_query_allrecords($query1);
        $Subject = '商机客户强制关联(系统邮件请勿回复)';
        $str='';
        $Body='<div>
                    <div><font size="2" face="Verdana"><font size="2" face="微软雅黑"><font size="2" face="微软雅黑"><span style="COLOR: #000000">Dear
                    '. $result1[0]['last_name'].'</span></font></font></font></div>
                    <blockquote style="MARGIN-TOP: 0px; PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt" id="ntes-flashmail-quote">
                      <div>
                      <blockquote style="MARGIN-TOP: 0px; PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt"><font size="2" face="Verdana"><font size="2" face="微软雅黑"></font></font><font size="2" face="Verdana">
                        </font><div><font size="2" face="Verdana">
                        </font>
                        <div><font size="2" face="微软雅黑">
                        市场部已对该客户：公司名称:'.$array['accountname'].'  进行强制关联 </font></div></div></blockquote></div></blockquote></div>
                        <div>'.$str.'</div>
                    <font size="2" face="Verdana">
                    <blockquote style="PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt"><font size="2" face="Verdana"></font>
                      <blockquote style="PADDING-LEFT: 0px; FONT-FAMILY: Verdana; MARGIN-LEFT: 0px; FONT-SIZE: 10pt"><font size="2" face="Verdana">


                        <span>

                        <div><font size="2"><font face="微软雅黑"><span></span></font></font>&nbsp;</div>
                        <div><font size="3" face="微软雅黑"><span></span></font>&nbsp;</div></span>
                        <div align="center"><font color="#c0c0c0" size="2" face="Verdana"></font>&nbsp;</div>
                        <div align="left"><font color="#c0c0c0" size="2" face="Verdana">'.date('Y-m-d').'</font></div>
                        <div align="left"><font size="2" face="Verdana">
                        <hr style="WIDTH: 122px; HEIGHT: 2px" id="SignNameHR" align="left" size="2">
                        </font></div>
                    <div align="left"><font size="2" face="微软雅黑"><span>
                        <p style="LINE-HEIGHT: 16.5pt; MARGIN: 0cm 0cm 0pt; FONT-FAMILY: 宋体; FONT-SIZE: 12pt; WORD-BREAK: break-all" class="MsoNormal"><span style="FONT-SIZE: 10pt" lang="EN-US"></span></p></span></font></div>
                        <div align="left"><font color="#c0c0c0" size="2" face="Verdana"><span></span></font>&nbsp;</div></font></blockquote></blockquote></font>';

        foreach($result1 as $result1value){
            $result1value['email1'] = $result1value['email1'] != '' ? trim($result1value['email1']) : trim($result1value['email2']);
            $address[]=array(
                'mail'=>trim($result1value['email1']),
                'name'=>trim($result1value['last_name']),
            );
        }
        $this->_logs(array("Subject"=>$Subject,'body'=>$Body,'address'=>$address));
        Vtiger_Record_Model::sendMail($Subject,$Body,$address);

        $this->sendForceRelatedWx($array['userid'],$array['accountname']);
    }

    public function checkEmails($str){
	$str=trim($str);
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }

    /**
     * 更新是否来自市场部
     * @param $id
     * @param $leadid 商机id
     */
    static public function updateAccountMarket($id,$leadid){
        $db=PearDatabase::getInstance();
        $time=date("Y-m-d H:i:s");
        $db->pquery('UPDATE vtiger_account SET frommarketing=?,mtime=? WHERE accountid=?',array('1',$time,$id));
        $db->pquery('UPDATE vtiger_leaddetails SET accountid=?,cluefollowstatus="accounted" WHERE leadid=?',array($id,$leadid));

        $recordModel=Vtiger_Record_Model::getInstanceById($leadid,'Leads');
        $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
            Array($leadid, 'cluefollowstatus', $recordModel->entity->column_fields['cluefollowstatus'], 'accounted'));
    }

    /**
     * 取得当前的设置
     * @return array
     */
    public function getSendMailSetting(){
        $query="SELECT allocationaftertracking,longesttracking,smower,reportto,fixedpersonnel,departmentdesignated,oldsmower,protectday FROM vtiger_sendmail_lead_setting LIMIT 1";
        $db=PearDatabase::getInstance();
        $arr=$db->run_query_allrecords($query);
        if(empty($arr)){
            return array('allocationaftertracking'=>0,'longesttracking'=>0,'smower'=>0,'reportto'=>0,'fixedpersonnel'=>0,'departmentdesignated'=>0,'oldsmower'=>0,'protectday'=>0);
        }
        return $arr[0];
    }

    /**
     * 取得当前的固定人员名单
     * @return array
     */
    public function getSendMailFixed(){
        $query="SELECT userid FROM `vtiger_sendmail_leads` WHERE `status`='c_fixed' AND module='Leads'";
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array());
        $arr=array();
        while($row=$db->fetch_array($result))$arr[]=$row['userid'];

        return $arr;
    }
    public function getSendMailDepartment(){
        $query="select id,CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(vtiger_users.`status`='Active','','[离职]'))) as last_name,vtiger_sendmail_leads.departmentid FROM `vtiger_sendmail_leads` LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_sendmail_leads.userid WHERE vtiger_sendmail_leads.`status`='c_allocated' AND module='Leads'";
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array());
        $arr=array();
        while($row=$db->fetch_array($result))$arr[]=$row;

        return $arr;
    }
    static public function selectAllUser(){
        $db=PearDatabase::getInstance();

        return $db->run_query_allrecords("select id,CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users WHERE `status`='Active'");
    }
    static public function changAccountuser($leadsArr){
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_account.accountid,accountcategory,accountrank,vtiger_users.last_name,vtiger_departments.departmentname FROM vtiger_account
                        INNER JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid
												LEFT JOIN vtiger_uniqueaccountname ON vtiger_account.accountid=vtiger_uniqueaccountname.accountid
                        LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
                        LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
												WHERE vtiger_uniqueaccountname.accountname=? and vtiger_crmentity.setype='Accounts' AND vtiger_crmentity.deleted =0 ";
        $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\&|\*|\（|\）|\-|\——|\=|\+/u','',$leadsArr['company']);
        $label=strtoupper($label);
        $result = $db->pquery($query, array($label));
        $data=$db->query_result_rowdata($result);
        if($db->num_rows($result)>0){
            $sql="UPDATE vtiger_leaddetails SET accountid=?,assignerstatus='c_Related' WHERE leadid=?";
            $db->pquery($sql,array($data['accountid'],$leadsArr['record_id']));
            if($data['accountcategory']==2){
                global $current_user;
                $recordModel=Vtiger_Record_Model::getInstanceById($data['accountid'],'Accounts');
                $salerank=$recordModel->getSaleRank($leadsArr['assigned_user_id']);
                $userinfo =$db->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?", array($current_user->id));
                $departmentid = $db->query_result($userinfo, 0,'departmentid');
                $user_entered = $db->query_result($userinfo, 0,'user_entered');
                $dataresult=$recordModel->getRankDays(array($salerank,$recordModel->get('accountrank'),$departmentid,$user_entered));
                $sql="UPDATE vtiger_account SET vtiger_account.accountcategory=1,protectday=3,effectivedays=".$dataresult['protectday'].",vtiger_account.frommarketing='1',vtiger_account.mtime=".time()." WHERE  vtiger_account.accountid=?";
                $db->pquery($sql,array($data['accountid']));
                $sql="UPDATE vtiger_crmentity SET vtiger_crmentity.smownerid=?,vtiger_crmentity.modifiedby=?,vtiger_crmentity.modifiedtime='".date('Y-m-d H:i:s')."' WHERE vtiger_crmentity.crmid=?";
                $db->pquery($sql,array($leadsArr['assigned_user_id'],$leadsArr['modifiedby'],$data['accountid']));
                $id = $db->getUniqueId('vtiger_modtracker_basic');
                $datetime=date('Y-m-d H:i:s');
                $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                    array($id, $data['accountid'], 'Accounts', $current_user->id, $datetime, 0));
                $accountcategory = '公海 (商机)捡';
                $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'accountcategory', $accountcategory, 1));
                $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'assigned_user_id', $recordModel->get('assigned_user_id'), $leadsArr['assigned_user_id']));
                return array('msg'=>'客户已经关联','flag'=>1,'accountid'=>$data['accountid']);
            }else{
                return array('msg'=>'客户已经存在','flag'=>1,'accountid'=>$data['accountid']);
            }
        }else{
            return array('flag'=>0);
        }
    }

    /**
     * 客户跟进后关联商机的转化后的跟进时间,跟进进度
     * @param Vtiger_Request $request
     */
    public static function leadUpdateFllowup(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $datatime=date("Y-m-d H:i:s");
        //$datatime=time();

        $query = "UPDATE vtiger_leaddetails,vtiger_account SET vtiger_leaddetails.followuptime=?,vtiger_leaddetails.followupcontents=? WHERE vtiger_account.accountid=vtiger_leaddetails.accountid AND vtiger_account.accountid=? AND vtiger_account.frommarketing=1";
        $db->pquery($query, array($datatime,$request->get('commentcontent'),$request->get('accountid')));
    }

    public function getClueFollowStatus($recordid){
        if(!$recordid){
            return;
        }
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_leaddetails where leadid=?",array($recordid));
        if(!$db->num_rows($result)){
            return '';
        }
        $row=$db->fetchByAssoc($result,0);
        return vtranslate($row['cluefollowstatus'],'Leads');
    }

    public function getShareSetting(){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select a.*,b.last_name from vtiger_leadsharesetting a left join vtiger_users b on a.userid=b.id order by a.leadsharesettingid desc",array());
        if(!$db->num_rows($result)){
            return array();
        }
        $data=array();
        while ($row=$db->fetch_array($result)){
            $row['sharetypelng']=vtranslate($row['sharetype'],'Leads');
            $data[]=$row;
        }
        return $data;
    }

    public function setAssignPersonal($userIds,$departmentId){
        $db=PearDatabase::getInstance();
        $sql = "insert into vtiger_leadassignpersonnel (userid,apanagemanagementid,cityname,assignnum,departmentid,roleid,period)
SELECT
	aa.id,
	aa.apanagemanagementid,
	aa.cityname,
	1,
	aa.departmentid,
	aa.roleid,
	( SELECT max( period ) FROM vtiger_leadassignpersonnel WHERE cityname = aa.cityname ) AS period 
FROM
	(
SELECT
	a.id,
	c.apanagemanagementid,
	c.cityname,
	1,
	b.departmentid,
	d.roleid 
FROM
	vtiger_users a
	LEFT JOIN vtiger_user2department b ON a.id = b.userid
	LEFT JOIN vtiger_apanagemanagement c ON c.userid = a.id
	LEFT JOIN vtiger_user2role d ON d.userid = a.id 
WHERE
	a.id IN ( ".implode(",",$userIds)." ) 
	AND a.STATUS = 'Active') aa";
        $db->pquery($sql,array());

    }

    public static function getAssignList(){
        $db=PearDatabase::getInstance();
        $sql = "select b.last_name,d.departmentname,c.cityname,e.rolename,a.assignnum,a.leadassignpersonnelid from vtiger_leadassignpersonnel a left join vtiger_users b on a.userid=b.id left join vtiger_apanagemanagement c on c.userid=a.userid 
  left join vtiger_departments d  on d.departmentid=a.departmentid left join vtiger_role e on e.roleid=a.roleid order by id asc";
        $result = $db->pquery($sql,array());
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            $data[]=$row;
        }
        return $data;
    }

    public static function getAssignUsers(){
        $db=PearDatabase::getInstance();
        $sql = "select b.id from vtiger_leadassignpersonnel a left join vtiger_users b on a.userid=b.id where b.status='Active' order by b.id asc";
        $result = $db->pquery($sql,array());
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            $data[]=$row['id'];
        }
        return $data;
    }

    public function filterSource($leadSource){
        $db=PearDatabase::getInstance();
        $result =$db->pquery("select * from vtiger_leadsourcetnum where source=?",array($leadSource));
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            $data[]=$row['leadsourcetnum'];
        }

        return $data;
    }

    public function filterSourceNum($leadsourcetnum){
        $db=PearDatabase::getInstance();
        $result =$db->pquery("select * from vtiger_sourcecategory where parent=? or parent is null",array($leadsourcetnum));
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            if($row['parent']!=$leadsourcetnum){
                continue;
            }
            $data[]=$row['sourcecategory'];
        }

        return $data;
    }

    public function getCurrentAvailableShareSetting(){
        $db=PearDatabase::getInstance();
        $sql="select starttime,promotionsharing,salesharing from vtiger_leadsharesetting order by starttime desc";
        $result = $db->query($sql,array());
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            if(strtotime($row['starttime'])<time()){
                return array("promotionsharing"=>$row['promotionsharing'],'salesharing'=>$row['salesharing']);
            }
        }
        return array();
    }


    public function allocateRules($cityName){
        $db=PearDatabase::getInstance();
        $cityName=trim($cityName,'市');
        $sql="select a.* from vtiger_leadassignpersonnel a left join vtiger_users b on a.userid=b.id where a.cityname like '".$cityName."%' and b.status='Active' order by a.period asc,a.userid asc";
        $result = $db->pquery($sql,array());
        if(!$db->num_rows($result)){
            return array();
        }

        while ($row=$db->fetchByAssoc($result)){
            if($row['assignnum']>$row['periodassignnum']){
                return array(
                    'leadassignpersonnelid'=>$row['leadassignpersonnelid'],
                    'period'=>$row['period'],
                    'departmentid'=>$row['departmentid'],
                    'assignnum'=>$row['assignnum'],
                    'periodassignnum'=>$row['periodassignnum'],
                    'userid'=>$row['userid'],
                );
            }
            $period[]=$row['period'];
            $rows[]=$row;
        }
        return array();

        $period=array_unique($period);
        if(count($period)==1){
            $db->pquery("update vtiger_leadassignpersonnel set period=period+1 where leadassignpersonnelid=?",array($rows[0]['leadassignpersonnelid']));
            return array(
                'leadassignpersonnelid'=>$rows[0]['leadassignpersonnelid'],
                'period'=>($rows[0]['period']+1),
                'departmentid'=>$rows[0]['departmentid'],
                'assignnum'=>$rows[0]['assignnum'],
                'periodassignnum'=>$rows[0]['periodassignnum'],
                'userid'=>$rows[0]['userid'],
            );
        }

        foreach ($rows as $key=>$row){
            if($rows[$key]['period']>$rows[$key+1]['period']){
                $db->pquery("update vtiger_leadassignpersonnel set period=period+1 where leadassignpersonnelid=?",array($rows[$key+1]['leadassignpersonnelid']));
                return array(
                    'leadassignpersonnelid'=>$rows[$key+1]['leadassignpersonnelid'],
                    'period'=>($rows[$key+1]['period']+1),
                    'departmentid'=>$rows[$key+1]['departmentid'],
                    'assignnum'=>$rows[$key+1]['assignnum'],
                    'periodassignnum'=>$rows[$key+1]['periodassignnum'],
                    'userid'=>$rows[$key+1]['userid'],
                );
            }
        }
        return array();
    }

    public function addBusinessOpportunity(Vtiger_Request $request){
        $data=array('module'=>'Leads',
            'action'=>'addBusiness',
            'mqdata'=>array(
                'rddata'=>array(
                    'companyname' => $request->get('companyname'),
                    'lastname' => $request->get('lastname'),
                    'mobile' => $request->get('mobile'),
                    'locationprovince' => $request->get('locationprovince'),
                    'locationcity' => $request->get('locationcity'),
                    'leadsourcetnum' =>$request->get('leadsourcetnum'),
                    'sourcecategory' => $request->get('sourcecategory')
                ),
            )
        );
        $db=PearDatabase::getInstance();
        sleep(1);
        $mobile=$request->get('mobile');
        $companyName=$request->get('companyname');
        $result = $db->pquery("SELECT mobile FROM vtiger_leaddetails INNER JOIN vtiger_crmentity ON crmid = leadid INNER JOIN vtiger_leadaddress ON leadid = leadaddressid WHERE deleted = '0' AND mobile = ?",array($mobile));
        if($db->num_rows($result)) {
            return array('success' => false, 'msg' => '系统中存在的号码');
        }

        $sql ="SELECT leadid FROM vtiger_leaddetails INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid WHERE vtiger_leaddetails.company = ? AND vtiger_crmentity.setype = 'Leads'";
        $adb = PearDatabase::getInstance();
        $leadsData = $adb->pquery($sql,array($companyName));
        if($db->num_rows($leadsData)){
            return array('success' => false, 'msg' => '该线索客户系统中已存在');
        }

        $query = "SELECT accountcategory,accountrank,vtiger_users.last_name,vtiger_departments.departmentname FROM vtiger_account
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        LEFT JOIN vtiger_uniqueaccountname ON vtiger_account.accountid=vtiger_uniqueaccountname.accountid 
                        WHERE vtiger_uniqueaccountname.accountname=? AND vtiger_crmentity.deleted =0 ";
        $result2 = $db->pquery($query, array($companyName));
        if($db->num_rows($result2)){
            return array('success' => false, 'msg' => '该线索客户系统中已存在');
        }
//        $LeadRecordModel=Leads_Record_Model::getCleanInstance("Leads");
//        $rules = $LeadRecordModel->allocateRules($request->get("locationcity"));
//        if(!empty($rules)){
//            global $current_user;
//            $user = new Users();
//            $current_user = $user->retrieveCurrentUserInfoFromFile($rules['userid']);
//        }

        $rddata=$data['mqdata']['rddata'];
        $_REQUES['record']='';
        $request=new Vtiger_Request($_REQUES, $_REQUES);
        $request->set('company',$rddata['companyname']);
        $request->set('lastname',$rddata['lastname']);
        $request->set('mobile',$rddata['mobile']);
        $request->set('locationprovince',$rddata['locationprovince']);
        $request->set('locationcity',$rddata['locationcity']);
        $request->set('leadsourcetnum',$rddata['leadsourcetnum']);
        $request->set('sourcecategory',$rddata['sourcecategory']);
        $request->set('leadstype','payspread');
        $request->set('leadsource','SCRM');
        $request->set('isFromMobile',1);
        $request->set('module','Leads');
        $request->set('view','Edit');
        $request->set('action','Save');
        $ressorder=new Leads_Save_Action();
        $data = $ressorder->saveRecord($request);
        return array("success"=>true,'msg'=>'成功');
//
//        $jsonData=json_encode($data);
//        $return=array('success'=>0,'msg'=>'进入队列失败');
//        $recordModel=new Vtiger_Record_Model();
//        $flag  = $recordModel->rabbitMQPublisher($jsonData);
        return $flag;
    }

    public function addBusiness($data){
        $rddata=$data['rddata'];
        $return=array('success'=>0);
        $this->_logs(array("addBusiness",$rddata));
        do{
            $_REQUES['record']='';
            $request=new Vtiger_Request($_REQUES, $_REQUES);
            $request->set('company',$rddata['companyname']);
            $request->set('lastname',$rddata['lastname']);
            $request->set('mobile',$rddata['mobile']);
            $request->set('locationprovince',$rddata['locationprovince']);
            $request->set('locationcity',$rddata['locationcity']);
            $request->set('leadsourcetnum',$rddata['leadsourcetnum']);
            $request->set('sourcecategory',$rddata['sourcecategory']);
            $request->set('leadstype','payspread');
            $request->set('leadsource','SCRM');
            $request->set('isFromMobile',1);
            $request->set('module','Leads');
            $request->set('view','Edit');
            $request->set('action','Save');
            $ressorder=new Leads_Save_Action();
            $ressorder->saveRecord($request);
            $return=array('success'=>1);
        }while(0);
        return $return;
    }

    public function _logs($data, $file = 'log_'){
        $year	= date("Y");
        $month	= date("m");
        $dir	= './logs/tyun/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }

    public function getDepartmentsByDepth(){
        $db = PearDatabase::getInstance();
        $sql = 'select departmentid,departmentname from vtiger_departments where depth=3';
//        $sql = 'select departmentid,departmentname from vtiger_departments where depth=3 and parentdepartment like "H1::H3%"';
        $result = $db->pquery($sql,array());
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            $data[$row['departmentid']]=$row['departmentname'];
        }
        return $data;
    }

    public function sendChangeMailToOldOwner($userid,$companyName){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("select email1,last_name from vtiger_users where id=?",array($userid));
        if(!$db->num_rows($result)){
            return '';
        }
        $row=$db->fetchByAssoc($result,0);
        $Subject='商机负责人变更(系统邮件请勿回复)';
        $body='市场部已对该商机：公司名称:'.$companyName.'  进行负责人变更';
        $address=array(
            array('mail'=>trim($row['email1']), 'name'=>$row['last_name'])
        );
        $this->_logs(array("Subject"=>$Subject,'body'=>$body,'address'=>$address));
        Vtiger_Record_Model::sendMail($Subject,$body,$address);

        $this->sendSmownerChangeWx(array($userid),$companyName);
    }

    public function getLeadProtectDay(){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_sendmail_lead_setting limit 1",array());
        if(!$db->num_rows($result)){
            return 0;
        }
        $row=$db->fetchByAssoc($result,0);
        return $row['protectday'];
    }
}
