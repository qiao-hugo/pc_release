<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * 
 *************************************************************************************/

class OrganizationChart_selectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getUserData');

        $this->exposeMethod('getRefreshUserData');

    }
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    public function process(Vtiger_Request $request) {
		$mode=$request->getMode();
        if(!empty($mode)){
            echo $this->invokeExposedMethod($mode,$request);
            exit;
        }
	}
    //打开时加载
    public function getUserData(Vtiger_Request $request){
        $userid=$request->get('userid');
        $arr['data']=array();
        if($userid>0){
            $db=PearDatabase::getInstance();
            $query='SELECT json FROM vtiger_organizationchart WHERE userid=?';
            $result=$db->pquery($query,array($userid));
            if($db->num_rows($result)){
                $json=$db->fetch_row($result);
                echo str_replace("&quot;",'"',$json['json']);
                exit;
            }else{
                $arr['data']=array($this->createUserData($request));
                $sql='REPLACE INTO vtiger_organizationchart(userid,json,updatetime) VALUES(?,?,?)';
                $json=json_encode($arr,JSON_UNESCAPED_UNICODE);
                $updatetime=time();
                $db->pquery($sql,array($userid,$json,$updatetime));
            }
        }

        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        exit;
    }
    public function getRefreshUserData(Vtiger_Request $request){
        $userid=$request->get('userid');
        $db=PearDatabase::getInstance();
        $query='SELECT updatetime FROM vtiger_organizationchart WHERE userid=?';
        $result=$db->pquery($query,array($userid));
        $data['msg']='更新完成';
        if($userid>0){
            if(!$db->num_rows($result)){
                $arr['data']=array($this->createUserData($request));
                $sql='REPLACE INTO vtiger_organizationchart(userid,json,updatetime) VALUES(?,?,?)';
                $json=json_encode($arr,JSON_UNESCAPED_UNICODE);
                $updatetime=time();
                $db->pquery($sql,array($userid,$json,$updatetime));
                $data['msg']='更新完成';
            }elseif($db->num_rows($result)){
                $updatetime=$db->fetch_row($result);
                $datetime=time();
                if($datetime-$updatetime['updatetime']>86400){
                    $arr['data']=array($this->createUserData($request));
                    $sql='REPLACE INTO vtiger_organizationchart(userid,json,updatetime) VALUES(?,?,?)';
                    $json=json_encode($arr,JSON_UNESCAPED_UNICODE);
                    $updatetime=time();
                    $db->pquery($sql,array($userid,$json,$updatetime));
                    $data['msg']='更新完成';
                }else{
                    $data['msg']='当天只能更新一次';
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($data);
        $response->emit();
    }
    //按条件查询
    public function createUserData(Vtiger_Request $request){
        global $adb;
        $userid=$request->get('userid');
        //$userid=38;
        $sql = "select id,status,concat(vtiger_users.last_name,'[',IFNULL(vtiger_departments.departmentname,''),']',if(status='Active','','[离职]')) AS name,reports_to_id AS pid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid where reports_to_id>0 AND status='Active' ORDER BY  reports_to_id";
        $result = $adb->pquery($sql);
        $noOfresult = $adb->num_rows($result);
        $reports=array();
        for ($i=0; $i<$noOfresult; ++$i) {
            $list = $adb->fetchByAssoc($result);
            $list['childrens']=array();
            $reports[$list['id']]=$list;
        }
        function make_tree($list,$pk='id',$pid='pid',$child='childrens',$root=38){
            $tree=array();
            foreach($list as $key=> $val){

                if($val[$pid]==$root){
                    unset($list[$key]);
                    if(!empty($list)){
                        $child=make_tree($list,$pk,$pid,$child,$val[$pk]);
                        if(!empty($child)){
                            $val['childrens']=$child;
                        }
                    }
                    $tree[]=$val;
                }
            }
            return $tree;
        }

        $data=empty($reports[$reports[$userid]['pid']])?array('id'=>$reports[$userid]['pid'],'pid'=>0,'name'=>'顶级',"status"=>"Inactive"):$reports[$reports[$userid]['pid']];
        $data['childrens']=array($reports[$userid]);
        $data['childrens'][0]['childrens']=make_tree($reports,'id','pid','childrens',$userid);
        return $data;
    }


}
