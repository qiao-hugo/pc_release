<?php
/*+***********************************************************************************
 * 上海珍岛信息技术有限公司CRM
 *************************************************************************************/

class Settings_AutoWorkflows_TaskAjax_Action extends Settings_Vtiger_IndexAjax_View {
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
	function __construct() {
		parent::__construct();
        $this->exposeMethod('addProcess');
        $this->exposeMethod('setAttribute');
        $this->exposeMethod('setAutoworkflowTask');
        $this->exposeMethod('saveAttribute');
        $this->exposeMethod('saveAutoworkflowTask');
        $this->exposeMethod('saveAutoworkflowTaskdetail');
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

    /**
     * 新增节点
     * @param Vtiger_Request $request
     * @return json
     */
    public function addProcess(Vtiger_Request $request){
        global $adb;
        $autoworkflowid=$request->get('autoworkflowid');
        $autoworkflowtaskname=$request->get('autoworkflowtaskname');
        $setleft = str_ireplace('px','',$request->get('left'));
        $settop = str_ireplace('px','',$request->get('top'));
        $autoworkflowtaskid=$adb->getUniqueID('vtiger_autoworkflowtasks');
        $sql = "insert into vtiger_autoworkflowtasks(autoworkflowtaskid,autoworkflowid,autoworkflowtaskname,setleft,settop) values(?,?,?,?,?)";
        $result = $adb->pquery($sql,array($autoworkflowtaskid,$autoworkflowid,$autoworkflowtaskname,$setleft,$settop));

        $arrReturn = array('id'=>$autoworkflowtaskid,'style'=>'color:#fff;left:'.$setleft.'px;top:'.$settop.'px;','process_to'=>'','process_name'=>$autoworkflowtaskname);
        $response = new Vtiger_Response();
        $response->setResult($arrReturn);
        $response->emit();
    }

    /**
     * 设置节点属性
     * @param Vtiger_Request $request
     */
    public function setAttribute(Vtiger_Request $request){
        global $adb;
        $autoworkflowtaskid=$request->get('autoworkflowtaskid');
        if(is_numeric($autoworkflowtaskid)){
            $sql='select * from vtiger_autoworkflowtasks where autoworkflowtaskid=? limit 1 ';
            $result =  $adb->pquery($sql,array($autoworkflowtaskid));
            $row = $adb->fetchByAssoc($result,0);

            //var_dump($row);die();
            
            $viewer = new Vtiger_Viewer();
            $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());               //所有分组人员;
            $viewer->assign("GROUPUSER_MODEL",Settings_SharingAccess_RuleMember_Model::getAll());   //分组信息
            $viewer->assign("ROLE_MODEL",$allRoles = Settings_Roles_Record_Model::getAll());    	// 所有职位角色
            $viewer->assign('DATA',$row);
            $viewer->assign('RECORD',$autoworkflowtaskid);
            $viewer->view('setattribute.tpl','Settings:AutoWorkflows');
        }
        echo '';
    }

    /**
     * 保存节点属性
     * @author wangbin 2015年7月15日 修改
     * @param Vtiger_Request $request
     */
    public function saveAttribute(Vtiger_Request $request){
        global $adb;
        $arrDatas=$_REQUEST['data'];//数据
        if($arrDatas['isnotice']=='on'){
            $arrDatas['isnotice'] = '1';
        }else{
            $arrDatas['isnotice'] = '0';
        }
      
        $autoworkflowtaskid=$request->get('autoworkflowtaskid');    //id 41
        $nReturn = 0;
        if(is_array($arrDatas)&&!empty($arrDatas)){
            $arrUpdates=array();
            $arrValues=array();
            foreach($arrDatas as $key=>$val){
                array_push($arrUpdates,$key.'=?');
                if($key == "autodetails"){
                    $val = implode('##', $val);
                }
                
                array_push($arrValues,$val);
            }
            $sql = "update vtiger_autoworkflowtasks set ".implode(",", $arrUpdates)." where autoworkflowtaskid=?";
            array_push($arrValues,$autoworkflowtaskid);
            $adb->pquery($sql,$arrValues);
            $nReturn = 1;
        }
        $response = new Vtiger_Response();
        $response->setResult($nReturn);
        $response->emit();
       // $sql = 'update  vtiger_autoworkflowtasks';
    }

    /**
     * 设置任务细节部分 2015年7月30日  wangbin
     * @param Vtiger_Request $request
     * @return mixed
     */
    public function setAutoworkflowTask(Vtiger_Request $request){
        global $adb;
       // var_dump($request);die;
        $source_record = $request->get(source_record);//工作流id
        $autoworkflowtaskid = $request->get(autoworkflowtaskid);//任务流节点id

        //读取邮件模版表里所有邮件模版
        $selmailsql = "SELECT * FROM `vtiger_emailtemplates` WHERE deleted = ?";
        $mailresult = $adb->pquery($selmailsql,array(0));
        $arrMaili = array();
        //$row = $adb->fetchByAssoc($result,0);
        for($i=0;$i<$adb->num_rows($mailresult);$i++){
            $arrMaili[] = $adb->query_result_rowdata($mailresult,$i);
        }   

      /*   if(is_numeric($autoworkflowtaskid)){
            $secttaskplesql = "SELECT contentjson FROM `vtiger_autoworkflowtasks_tasktemplets` WHERE autoworkflowtaskid = ? AND tasktypeid = ? LIMIT 1";
            $taskresult = $adb->pquery($secttaskplesql,array($autoworkflowtaskid,0));
            $row = $adb->fetchByAssoc( $taskresult ,0)['contentjson'];
            $data = (json_decode(str_replace('&quot;','"',$row)));  //系统本身会将双引号过滤，这里需要恢复过来
        } */
        $mail = $this->setpart($autoworkflowtaskid,"0");//邮件信息
        $func = $this->setpart($autoworkflowtaskid,"2");//自定义函数

        $viewer = new Vtiger_Viewer(); //自定义smarty类，不继承任何的信息
        $viewer->assign("ALLMAIL",$arrMaili); //邮件模版                  
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());               //所有分组人员;
        $viewer->assign("GROUPUSER_MODEL",Settings_SharingAccess_RuleMember_Model::getAll());   //分组信息
        $viewer->assign("ROLE_MODEL",$allRoles = Settings_Roles_Record_Model::getAll());    	// 所有职位角色
        $viewer->assign('RECORD',$autoworkflowtaskid);
        $viewer->assign('DATA',$mail);
        $viewer->assign('FUNC',$func);
        $viewer->view('setautoworkflowtask.tpl','Settings:AutoWorkflows');  //可通过异步调用
    }
    /**
     * @author 2015年7月30日 wangbin  根据不同 tasktype 读取vtiger_autoworkflowtasks_tasktemplets 
     */
    public function setpart($autoworkflowtaskid,$tasktypeid){
        global $adb;
        if(is_numeric($autoworkflowtaskid)){
            $secttaskplesql = "SELECT contentjson FROM `vtiger_autoworkflowtasks_tasktemplets` WHERE autoworkflowtaskid = ? AND tasktypeid = ? LIMIT 1";
            $taskresult = $adb->pquery($secttaskplesql,array($autoworkflowtaskid,$tasktypeid));
            $row = $adb->fetchByAssoc( $taskresult ,$tasktypeid);
            $json = $row['contentjson'];
            $err = str_replace('&quot;','"',$json);
            return json_decode(str_replace('&quot;','"',$json));  //系统本身会将双引号过滤，这里需要恢复过来
        }
    }
    
   /**保存任务细节部分
    * @param Vtiger_Request $request
    */
   public function saveAutoworkflowTaskdetail(Vtiger_Request $request){
       $autoworkflowtaskid=$request->get('autoworkflowtaskid'); //任务id;
       global $adb;
       $jsonmail = json_encode($request->get('mail'));
       $jsonfunc = json_encode($request->get('func'));

       $this->save_part($jsonmail, $autoworkflowtaskid, "0");
       $this->save_part($jsonfunc, $autoworkflowtaskid, "2");
       
   }
   
   
   /*
    * @author wangbin 2015年7月30日 保存任务节点详细根据不同typeid update or insert vtiger_autoworkflowtasks_tasktemplets;
    * @param string $json 表单数据
    * @param int    $autoworkflowtaskid 任务节点id
    * @param int    $tasktypeid 不同表单id 邮件 0 ,接口 1  自定义函数 2
    * @param null
    */
   public function save_part($json,$autoworkflowtaskid,$tasktypeid){
       global $adb;
       $ifsql = "SELECT * FROM `vtiger_autoworkflowtasks_tasktemplets` WHERE autoworkflowtaskid = ? AND tasktypeid = ?";
       $ifresult = $adb->pquery("$ifsql",array($autoworkflowtaskid,$tasktypeid));
       if($adb->num_rows($ifresult)>0){
           $updatesql = "UPDATE `vtiger_autoworkflowtasks_tasktemplets` SET  contentjson = ? WHERE autoworkflowtaskid=? AND tasktypeid = ?";
           $adb->pquery("$updatesql",array($json,$autoworkflowtaskid,$tasktypeid));
       }else {
           $insertsql = "INSERT INTO `vtiger_autoworkflowtasks_tasktemplets` (contentjson,autoworkflowtaskid,tasktypeid) VALUES (?, ?,?)";
           $adb->pquery("$insertsql",array($json,$autoworkflowtaskid,$tasktypeid));
       }
   }

    /**
     * 保存任务
     * @param Vtiger_Request $request
     */
    public function saveAutoworkflowTask(Vtiger_Request $request){
        global $adb;
        $isSuccess=0;
        $arrDatas=$request->get('data');
        $record=$request->get('autoworkflowid');
        if(is_array($arrDatas)&&!empty($arrDatas)){
            foreach($arrDatas as  $key=>$val){
                $left=$val['left'];
                $top=$val['top'];
                $process_to=implode(',',$val['process_to']);
                $process_from=implode(',',$val['process_from']);
                $style="color:#fff;left:{$left}px;top:{$top}px;";
                $sql = 'update vtiger_autoworkflowtasks set setleft=?,settop=?,process_to=?,style=?,process_from=? where autoworkflowtaskid=? and autoworkflowid=?';

                $adb->pquery($sql,array($left,$top,$process_to,$style,$process_from,$key,$record));
                $isSuccess=1;
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($isSuccess);
        $response->emit();
    }

    /**
     * 删除连接线
     * @param Vtiger_Request $request
     */
    public function deleteProcess(Vtiger_Request $request){

    }
}