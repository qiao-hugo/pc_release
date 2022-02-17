<?php

class AUTOWorkflowEventHandler extends VTEventHandler {

    /**
     * 重写handler的方法,触发规则
     * 新增时，编辑时，新增或者编辑（缺失），字段更新，删除
     * @param $eventName 事件名称，vtiger.entity.aftersave，vtiger.entity.beforesave，vtiger.entity.aftersave.final
     * @param $entityData 实例数据
     * @return void  
     */

    function handleEvent($eventName, $data) {
		
        // ServiceContracts_Record_Model::tCloudPackage;
        // var_dump($data);
       //var_dump($data->isNew());die;
        global $adb, $current_user;
        $moduleName = $data->getModuleName(); // 模块名称
		/*
        if($moduleName == 'SalesOrder'){
            $salesorderid = $data->getId();
            $servicecontractid = $data->get('servicecontractsid');
            $sql = 'SELECT sc_related_to FROM vtiger_servicecontracts WHERE servicecontractsid = ?';
            $account_result = $adb->pquery($sql,array($servicecontractid));
            if($adb->num_rows($account_result)){
                $accountid = $adb->fetchByAssoc($account_result,0)['sc_related_to'];
                //$update_flowenti_sql = 'UPDATE vtiger_autoworkflowentitys SET salesorderid = 123 WHERE accountid = 23622';
                $update_flowenti_sql = 'UPDATE vtiger_autoworkflowentitys SET salesorderid = ? WHERE accountid = ?';
                $update_taslenti_sql = 'UPDATE vtiger_autoworkflowtaskentitys SET salesorderid = ? WHERE accountid = ?';
                $adb->pquery($update_flowenti_sql,array($salesorderid,$accountid));
                $adb->pquery($update_taslenti_sql,array($salesorderid,$accountid));
            }
        }*/
        if ($moduleName == 'ServiceContracts') {
            $arrproduct = explode(",",$data->get('productid'));
            $ifcreat =  ServiceContracts_Record_Model::tCloudPackage();//特定的产品id;
            //只有特定产品才会生成工作流，编辑和新增需要确定一下;
           if (array_intersect($arrproduct, $ifcreat)) {
                // 触发阶段 vtiger.entity.aftersave.final
                $isNew = $data->isNew(); // 是否新增
                if ($eventName == 'vtiger.entity.aftersave' && $isNew) {
                    $types = $this->getModuleAutoTask($moduleName);   //后台工作流（flowid::conditiontypes）
                    if (! empty($types)) { // 类型
                        foreach ($types as $key => $val) {
                            /*
                             * if($isNew&&$val==AutoTask::$CREATED){
                             * $this->makeAutoTask($data,$key,AutoTask::$CREATED); //新增
                             * }elseif($val==AutoTask::$UPDATED){
                             * $this-> makeAutoTask($data,$key,AutoTask::$UPDATED); //编辑
                             * }elseif($key==AutoTask::$FIELDUPDATE){
                             * $this->makeAutoTask($data,$key,AutoTask::$FIELDUPDATE); //更新字段
                             * }
                             */
                            $last_flowid = $this->makeAutoTask($data, $key, 0); // 编辑
                            //wangbin 判断当前产品是否是T-云v1产品
                            if(in_array('401',$arrproduct)){
                                $upv1_sql = "UPDATE vtiger_autoworkflowtaskentitys SET isv1 = 1 WHERE autoworkflowentityid=? AND relationmodule='Salesorder'";
                                $adb->pquery($upv1_sql,array($last_flowid));
                            }
							
                        }
                    }
                }
          }
        }
<<<<<<< .mine
     // die("稍做休息");
=======
        //die("稍做休息");
>>>>>>> .r4000
    }

    /**
     * 动态生成自动化的任务
     * @param $data 数据实例
     * @param $entityid 自动化任务id
     * @param $type 任务类型
     * @throws Exception
     */
    function makeAutoTask($data,$entityid,$type){
        global  $adb, $current_user;
        $nMakeAutoTask = true;
        $moduleName = $data->getModuleName();           // 模块名称
        $recordId = $data->getId();                 // 主键 369151
        
        $vtEntityDelta = new VTEntityDelta();       //获取字段差异
        $delta = $vtEntityDelta->getEntityDelta($moduleName, $recordId, true);
        
        /* 根据字段值来确定是否要生成
        $vtEntityDelta = new VTEntityDelta();       //获取字段差异
        $delta = $vtEntityDelta->getEntityDelta($moduleName, $recordId, true);      //获取有变化的字段
        if($type == 1){
            $strSql = 'SELECT * FROM vtiger_autoworkflowtasks_tasktemplets  WHERE autoworkflowtaskid = ? AND tasktypeid = ? LIMIT 1';
            $result = $adb->pquery($strSql,array($entityid,$type));
            if($adb->num_rows($result)>0){
                $jsonContent = $adb->query_result($result,0,'contentjson');
                $arrFieldConditions = json_decode($jsonContent,true);                //字段条件
                foreach($arrFieldConditions as $field=>$val){
                    if(isset($delta[$field])){
                        if($delta[$field] !=$val){
                            $nMakeAutoTask = false;break;
                        }
                    }else{
                        break;
                    }
                }
            }
            //取出有差异的字段跟基准的做比较，如果全部成功则为true
        }*/
        if($nMakeAutoTask){
            $ownerid = $data->get('assigned_user_id');
            $accountid = $data->get('sc_related_to'); //369014
            //新建工作流
            $date_var = date("Y-m-d H:i:s");
            $date = $adb->formatDate($date_var, true);

            $insertflowsql = "INSERT INTO `vtiger_autoworkflowentitys` (autoworkflowid, autoworkflowentityname, modulename, crmid, deleted, createdtime, creatorid, accountid ) VALUES (?, (SELECT autoworkflowname FROM vtiger_autoworkflows WHERE autoworkflowid = ?), ?, ?, ?, ?, ?, ?)";
            $adb->pquery($insertflowsql,array($entityid,$entityid,$moduleName,$recordId,0,$date,$ownerid,$accountid));
             $last_flowid = $adb->getLastInsertID(); //新增的前台 autotask_flowid;
           //添加两个字段
            $sql = 'INSERT INTO vtiger_autoworkflowtaskentitys (relationmodule,autoworkflowentityid,autorole, autodetails, isaction, autoworkflowid, tabid, modulename, crmid, autoworkflowtaskname, autoworkflowtaskid, creatorid, createdtime, accountid, moulestatus, process_to, process_from, settop, style, setleft ) SELECT mountmodule,?,autorole, autodetails, ?, ?, ?, ?, ?, autoworkflowtaskname, autoworkflowtaskid, ?, NOW(), ?, autostatus, process_to, process_from, settop, style, setleft FROM vtiger_autoworkflowtasks WHERE autoworkflowid =?';
            //var_dump(array(0,$entityid,0,$moduleName,$recordId,$ownerid,$accountid,$entityid));die;
            $adb->pquery($sql,array($last_flowid,0,$entityid,0,$moduleName,$recordId,$ownerid,$accountid,$entityid));
            return $last_flowid;
        }
    }

    /**
     * 根据模块返回对于的自动化的数组列表
     * @param $module 模块名称
     * @return array 返回数组，空位null
     */
    function getModuleAutoTask($module){
        global  $adb, $current_user;
        $sql = 'select autoworkflowid,execution_condition from vtiger_autoworkflows where modulename =?';
        $result = $adb->pquery($sql,array($module));
        if($adb->num_rows($result)>0){
            $temp = array();
            while($rawData=$adb->fetch_array($result)) {
                $temp[$rawData['autoworkflowid']]=$rawData['execution_condition'];
            }
            return $temp;
        }
        return null;
    }
}
?>