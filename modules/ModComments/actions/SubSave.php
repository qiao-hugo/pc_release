<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ModComments_SubSave_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
		//$recordId = $request->get('record');
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();
		//写数据
		$edit = $request->get('edit');
		$modcommentsid=$request->get('src_record');
		$modcommenthistory = $request->get('modcommenthistory');
		$accountintentionality = $request->get('accountintentionality');
        $accountid = '';
		if($edit){
			$id=$edit;
			$modifiedby= $currentUserModel->id;
			$modifiedtime = getDateFormat();
			$modifiedcause = $request->get('modifiedcause');
			$result=$db->pquery('select modcommentsid from vtiger_submodcomments where id=?',array($id));
			if($db->num_rows($result)){
				$db->pquery('update vtiger_submodcomments set modifiedby=?,modifiedtime=?,modifiedcause=?,modcommenthistory=?,accountintentionality=? where id=?',array($modifiedby,$modifiedtime,$modifiedcause,$modcommenthistory,$accountintentionality,$id));
                $sql = "select a.moduleid,a.modulename from vtiger_modcomments a left join vtiger_submodcomments b on a.modcommentsid=b.modcommentsid where b.id=? limit 1";
                $result = $db->pquery($sql,array($edit));
                if($db->num_rows($result)){
                    $row = $db->fetchByAssoc($result,0);
                    if($row['modulename']=='Accounts'){
                        //修改对应的客户表中的意向度
                        $db->pquery("update vtiger_account set intentionality=? where accountid=?",array($accountintentionality,$row['moduleid']));
                        $accountid = $row['moduleid'];
                    }
                }

			}else{
			throw new AppException('更新失败');}
		}else{
			$creatorid = $currentUserModel->id;
			$createdtime = getDateFormat();
			//echo "insert into vtiger_submodcomments(modcommentsid,creatorid,createdtime,modcommenthistory) values($modcommentsid,$creatorid,$createdtime,$modcommenthistory)";die();
			$db->pquery("insert into vtiger_submodcomments(modcommentsid,creatorid,createdtime,modcommenthistory,accountintentionality) values(?,?,?,?,?)",array($modcommentsid,$creatorid,$createdtime,$modcommenthistory,$accountintentionality));
            $sql = "select a.moduleid,a.modulename from vtiger_modcomments a  where a.modcommentsid=? limit 1";
            $result = $db->pquery($sql,array($modcommentsid));
            if($db->num_rows($result)){
                $row = $db->fetchByAssoc($result,0);
                if($row['modulename']=='Accounts'){
                    //修改对应的客户表中的意向度
                    $db->pquery("update vtiger_account set intentionality=? where accountid=?",array($accountintentionality,$row['moduleid']));
                    $accountid = $row['moduleid'];
                }
            }
		}
        $title = '';
        if($accountid){
            $result2 = $db->pquery("select accountcategory from vtiger_account where accountid=? limit 1",array($accountid));
            $row2 = $db->fetchByAssoc($result2,0);
            switch ($row2['accountcategory']){
                case 1:
                    $title = '临时区';
                    break;
                case 2:
                    $title = '公海';
                    break;
                default:
                    $title = '';
                    break;
            }
        }


        //读取
		$recordModel = ModComments_Record_Model::getSubModcomments($modcommentsid);
		
		
		$resultResponse['success']=true;
		$resultResponse['accountcategory']=$title;
		//$result=$recordModel[$modcommentsid];
		
		
		$resultResponse['result'] = (empty($recordModel[$modcommentsid])?array():$recordModel[$modcommentsid]);
		
		$response = new Vtiger_Response();
		//$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($resultResponse);
		$response->emit();
	}
	
}
