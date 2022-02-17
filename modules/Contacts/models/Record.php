<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Contacts_Record_Model extends Vtiger_Record_Model {

	public function getId() {
		return $this->get('id');
	}
	/**
	 * Function returns the url for create event
	 * @return <String>
	 */
	function getCreateEventUrl() {
		$calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
		return $calendarModuleModel->getCreateEventRecordUrl().'&contact_id='.$this->getId();
	}

	/**
	 * Function returns the url for create todo
	 * @return <String>
	 */
	function getCreateTaskUrl() {
		$calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
		return $calendarModuleModel->getCreateTaskRecordUrl().'&contact_id='.$this->getId();
	}


	/**
	 * Function to get List of Fields which are related from Contacts to Inventory Record
	 * @return <array>
	 */
	public function getInventoryMappingFields() {
		return array(
				array('parentField'=>'account_id', 'inventoryField'=>'account_id', 'defaultValue'=>''),

				//Billing Address Fields
				array('parentField'=>'mailingcity', 'inventoryField'=>'bill_city', 'defaultValue'=>''),
				array('parentField'=>'mailingstreet', 'inventoryField'=>'bill_street', 'defaultValue'=>''),
				array('parentField'=>'mailingstate', 'inventoryField'=>'bill_state', 'defaultValue'=>''),
				array('parentField'=>'mailingzip', 'inventoryField'=>'bill_code', 'defaultValue'=>''),
				array('parentField'=>'mailingcountry', 'inventoryField'=>'bill_country', 'defaultValue'=>''),
				array('parentField'=>'mailingpobox', 'inventoryField'=>'bill_pobox', 'defaultValue'=>''),

				//Shipping Address Fields
				array('parentField'=>'otherstreet', 'inventoryField'=>'ship_street', 'defaultValue'=>''),
				array('parentField'=>'othercity', 'inventoryField'=>'ship_city', 'defaultValue'=>''),
				array('parentField'=>'otherstate', 'inventoryField'=>'ship_state', 'defaultValue'=>''),
				array('parentField'=>'otherzip', 'inventoryField'=>'ship_code', 'defaultValue'=>''),
				array('parentField'=>'othercountry', 'inventoryField'=>'ship_country', 'defaultValue'=>''),
				array('parentField'=>'otherpobox', 'inventoryField'=>'ship_pobox', 'defaultValue'=>'')
		);
	}

	/**
	 * Function to get Image Details
	 * @return <array> Image Details List
	 */
	public function getImageDetails() {
		$db = PearDatabase::getInstance();
		$imageDetails = array();
		$recordId = $this->getId();

		if ($recordId) {
			$sql = "SELECT vtiger_attachments.*, vtiger_crmentity.setype FROM vtiger_attachments
						INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
						WHERE vtiger_crmentity.setype = 'Contacts Image' and vtiger_seattachmentsrel.crmid = ?";

			$result = $db->pquery($sql, array($recordId));

			$imageId = $db->query_result($result, 0, 'attachmentsid');
			$imagePath = $db->query_result($result, 0, 'path');
			$imageName = $db->query_result($result, 0, 'name');

			//decode_html - added to handle UTF-8 characters in file names
			$imageOriginalName = decode_html($imageName);

			//urlencode - added to handle special characters like #, %, etc.,
			$imageName = urlencode($imageName);

			$imageDetails[] = array(
					'id' => $imageId,
					'orgname' => $imageOriginalName,
					'path' => $imagePath.$imageId,
					'name' => $imageName
			);
		}
		return $imageDetails;
	}

	function mobileSaveContact(Vtiger_Request $request){
        global $current_user,$currentModule;
        $currentModule = 'Contacts';
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($request->get("userid"));
	    $request->set("module",'Contacts');
        $request->set("action",'Save');
        $request->set("sourceModule",'Accounts');
        $request->set("relationOperation",true);
        $request->set("sourceRecord",$request->get("accountid"));
        $request->set("account_id",$request->get("accountid"));
        $request->set("name",$request->get("name"));
        $request->set("mobile",$request->get("mobile"));
        $request->set("gendertype",$request->get("gender"));
        $request->set("accountid",$request->get("accountid"));
        $saveModel = new Contacts_Save_Action();
        $recordModel = $saveModel->getRecordModelFromRequest($request);
        $recordModel->save();
        return array(array('success'=>true,'msg'=>''));
    }

    function accountContacts(Vtiger_Request $request){
	    $db = PearDatabase::getInstance();
	    $result = $db->pquery("select a.accountid,a.accountname,a.serviceid as serviceid,b.last_name as servicename,
  a.linkname,a.gender,a.mobile,a.phone,a.title,a.email1 as email,a.makedecision
from vtiger_account a left join vtiger_users b on a.serviceid=b.id where a.accountid=?",array($request->get("accountId")));
	    if(!$db->num_rows($result)){
	        return array("success"=>false,'msg'=>'没有对应客户');
        }
        $lng = translateLng("Accounts");
	    $row = $db->fetchByAssoc($result,0);
	    $returnData = array();
        $returnData['accountInfo'] = array(
	        'accountName'=>$row['accountname'],
	        'accountId'=>$row['accountid'],
	        'serviceid'=>$row['serviceid'],
	        'serviceName'=>$row['servicename'],
        );
        $returnData['firstContact'] = array(
            "linkName"=>$row['linkname'],
            "gender"=>$lng[$row['gender']],
            "mobile"=>$row['mobile'],
            "phone"=>$row['phone'],
            "title"=>$row['title'],
            "makeDecision"=>$lng[$row['makedecision']],
            "email"=>$row['email'],
        );
        $returnData['contactList']=$this->getContactList($request->get("accountId"));
        $returnData['success']=true;
        return $returnData;
    }

    function getContactList($accountId){
	    $db = PearDatabase::getInstance();
        $lng = translateLng("Accounts");
        $contactList=array();
        $result2 = $db->pquery("select * from vtiger_contactdetails a left join vtiger_crmentity b on a.contactid=b.crmid where a.accountid=? and b.deleted=0",array($accountId));
        if($db->num_rows($result2)){
            while ($row=$db->fetchByAssoc($result2)){
                $contactList[] = array(
                    "contactId"=>$row['contactid'],
                    "linkName"=>$row['name'],
                    "gender"=>$lng[$row['gender']],
                    "mobile"=>$row['mobile'],
                    "phone"=>$row['phone'],
                    "title"=>$row['title'],
                    "makeDecision"=>$lng[$row['makedecision']],
                    "email"=>$row['email'],
                );
            }
        }
        return $contactList;
    }

    function addContact(Vtiger_Request $request){
        global $current_user,$currentModule;
        $currentModule = 'Contacts';
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($request->get("userid"));
        $request->set("module",'Contacts');
        $request->set("action",'Save');
        $request->set("sourceModule",'Accounts');
        if($request->get("record")){
            $request->set("mode",'edit');
        }
        $request->set("relationOperation",true);
        $request->set("sourceRecord",$request->get("accountid"));
        $request->set("account_id",$request->get("accountid"));
        $request->set("name",$request->get("name"));
        $request->set("mobile",$request->get("mobile"));
        $request->set("phone",$request->get("phone"));
        $request->set("assigned_user_id",$request->get("userid"));
        $request->set("title",$request->get("title"));
        $request->set("makedecisiontype",$request->get("makeDecision"));
        $request->set("email",$request->get("email"));
        $request->set("gendertype",$request->get("gender"));
        $request->set("accountid",$request->get("accountid"));
        $saveModel = new Contacts_Save_Action();
        $recordModel = $saveModel->getRecordModelFromRequest($request);
        $recordModel->save();
        return array('success'=>true,'msg'=>'','contactId'=>$recordModel->getId());
    }


}
