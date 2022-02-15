<?php

class EmployeeAbility_Record_Model extends Vtiger_Record_Model
{
    public function getColumnInfo(){
        global $adb;
        $sql = "select * from vtiger_employee_ability_column order by rank asc";
        $result = $adb->pquery($sql,array());
        if(!$adb->num_rows($result)){
            return array();
        }
        $columns = array();
        while ($row = $adb->fetchByAssoc($result)){
            $columns[$row['columnname']] = $row;
        }
        return $columns;
    }
    public function getColumns(){
        global $adb;
        $sql = "select * from vtiger_employee_ability_column order by rank asc";
        $result = $adb->pquery($sql,array());
        if(!$adb->num_rows($result)){
            return array();
        }
        $columns = array();
        while ($row = $adb->fetchByAssoc($result)){
            $columns[$row['stafflevel']][] = $row['columnname'];
        }
        return $columns;
    }

    public function getDefaultValueAndColumns(){
        global $adb;
        $sql = "select * from vtiger_employee_ability_column  order by rank asc";
        $result = $adb->pquery($sql,array());
        if(!$adb->num_rows($result)){
            return array();
        }
        $columns = array();
        while ($row = $adb->fetchByAssoc($result)){
            $value = $row['defaultvalue'];
            if(in_array($row['columnname'],$this->getSpecialColumns())){
                $value = 'underreviewer';
            }
            $columns[$row['columnname']] = $value;
        }
        return $columns;
    }

    public function getColleageColumns(){
        global $adb;
        $sql = "select * from vtiger_employee_ability_column  order by rank asc";
        $result = $adb->pquery($sql,array());
        if(!$adb->num_rows($result)){
            return array();
        }
        $columns = array();
        while ($row = $adb->fetchByAssoc($result)){
            if($row['defaultvalue']!='nosubmitted'){
                $columns[] = $row['columnname'];
            }
        }
        return $columns;
    }

    public function getSpecialColumns(){
        global $adb;
        $sql = "select * from vtiger_employee_ability_column where defaultvalue=1 order by rank asc";
        $result = $adb->pquery($sql,array());
        if(!$adb->num_rows($result)){
            return array();
        }
        $columns = array();
        while ($row = $adb->fetchByAssoc($result)){
            $columns[] = $row['columnname'];
        }
        return $columns;
    }


    function getStaffLevelByField($field){
        $columns = $this->getColumns();
        if(in_array($field,$columns['junior'])){
            return 'junior';
        }
        if(in_array($field,$columns['intermediate'])){
            return 'intermediate';
        }
        return 'senior';
    }

    public function updateDetailContent($field,$value,$recordid,$columnvalues,$type){
        $stafflevel = $this->getStaffLevelByField($field);
        global $adb,$current_user,$zhongxiaozongjian,$zhongxiaojingli;
        $sql = "select * from vtiger_employee_ability_detail where stafflevel=? and employeeabilityid=? limit 1";
        $result = $adb->pquery($sql,array($stafflevel,$recordid));
        if(!$adb->num_rows($result)){
            return array();
        }
        $row = $adb->fetchByAssoc($result,0);
        $content =json_decode(str_replace("&quot;", '"', $row['content']), true);
        if($content[$field]['status']=='completed'){
            return array();
        }
        $status = $value;
        if($type=='pass'){
            if($current_user->roleid==$zhongxiaojingli && $content[$field]['nextreviewer'] ==1){
                $userRecordModel = Vtiger_Record_Model::getCleanInstance("Users");
                $last_name = $userRecordModel->managerByCurrentUser($row['userid'],$zhongxiaozongjian);
                $columnvalues['nextreviewer'] = 2;
                $columnvalues['reviewresult'] =  $current_user->last_name.':通过#n#'.($last_name?$last_name:'总监').':待处理';
                $status = 'inreview';
                $columnvalues['status'] = $status;
            }elseif($current_user->roleid==$zhongxiaozongjian && $content[$field]['nextreviewer'] ==2){
                $previewresult = explode("#n#",$content[$field]['reviewresult']);
                $columnvalues['nextreviewer'] = 3;
                $columnvalues['reviewresult'] =  $previewresult[0].'#n#'.$current_user->last_name.':通过#n#'.'监察:待处理';
                $status = 'inreview';
                $columnvalues['status'] = $status;
            }else{
                $previewresult = explode("#n#",$content[$field]['reviewresult']);
                $columnvalues['nextreviewer'] = 0;
                $columnvalues['reviewresult'] =  $previewresult[0].'#n#'.$previewresult[1].'#n#'.$current_user->last_name.':通过';
                $status = 'completed';
                $columnvalues['status'] = $status;
            }
        }elseif($type=='reject'){
            $columnvalues['rejectnum'] =1+$content[$field]['rejectnum'];

            //插入驳回历史
            $rejectParams = array(
              'employeeabilityid'=>$recordid,
              'rejectcolumn'=>$field,
              'rejectreason'=>$columnvalues['rejectreason'],
              'rejectnum'=>$columnvalues['rejectnum'],
              'rejector'=>$columnvalues['rejector'],
              'rejecttime'=>$columnvalues['rejecttime'],
            );
            $this->insertRejectHistory($rejectParams);
        }


        foreach ($columnvalues as $key=>$columnvalue){
            $content[$field][$key] = $columnvalue;
        }

        //修改详情表
        $updateSql = "update vtiger_employee_ability_detail set content=? where employeeabilitydetailid=?";
        $updateListSql = "update vtiger_employee_ability set  ".$field."=? where employeeabilityid=?";
        if($this->isFinishCurrentLevelTask($content)){
            $updateSql = "update vtiger_employee_ability_detail set content=?,status=1 where employeeabilitydetailid=?";
            $updateListSql = "update vtiger_employee_ability set  ".$field."=?,stafflevel='".$this->upgradeLevel($stafflevel,$row['employeeabilityid'])."' where employeeabilityid=?";
        }
        $adb->pquery($updateSql,array(json_encode($content),$row['employeeabilitydetailid']));
        $colleageColumns = $this->getColleageColumns();
        if(!in_array($field,$colleageColumns)){
            $adb->pquery($updateListSql,array($columnvalues['status'],$row['employeeabilityid']));
        }else{
            $adb->pquery($updateListSql,array($content[$field]['wordsub'],$row['employeeabilityid']));
        }

        return $content[$field];
    }

    public function upgradeLevel($stafflevel,$employeeabilityid){
         global $adb;
        switch ($stafflevel){
            case 'junior':
                $result = $adb->pquery("select status from vtiger_employee_ability_detail where stafflevel=? and employeeabilityid=?",array('intermediate',$employeeabilityid));
                $status = 0;
                if($adb->num_rows($result)){
                    $row = $adb->fetchByAssoc($result,0);
                    $status = $row['status'];
                }
                $upgradelevel = $status?'senior':'intermediate';
                break;
            case 'intermediate':
                $result = $adb->pquery("select status from vtiger_employee_ability_detail where stafflevel=? and employeeabilityid=?",array('junior',$employeeabilityid));
                $status = 0;
                if($adb->num_rows($result)){
                    $row = $adb->fetchByAssoc($result,0);
                    $status = $row['status'];
                }
                $upgradelevel = $status?'senior':'junior';
                break;
            case 'senior':
                $result = $adb->pquery("select status,stafflevel from vtiger_employee_ability_detail where stafflevel in(?,?) and employeeabilityid=?",array('junior','intermediate',$employeeabilityid));
                $upgradelevel = 'senior';
                if($adb->num_rows($result)){
                    while ($row = $adb->fetchByAssoc($result)){
                        if($row['stafflevel']=='junior' && $row['status']==0){
                            return 'junior';
                        }
                        if($row['stafflevel']=='intermediate' && $row['status']==0){
                            $upgradelevel = 'intermediate';
                        }
                    }
                }
                break;
            default:
                $upgradelevel = 'senior';
        }
        return $upgradelevel;
    }

    public function isFinishCurrentLevelTask($content){
        $status = array();
        foreach ($content as $key=>$value){
            $status[] = $value['status'];
        }
        $status = array_unique($status);
        if(in_array('',$status)||in_array('underreviewer',$status) || in_array('reject',$status) ||
            in_array('inreview',$status) ){
            return false;
        }
        return true;
    }

    public function staffLevel()
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select stafflevel from vtiger_employee_ability where employeeabilityid=?", array($this->getId()));
        if ($db->num_rows($result)) {
            return $db->fetchByAssoc($result, 0)['stafflevel'];
        }
    }

    public function getFileContents($contents){
        foreach ($contents as $key=>$content){
            $contents[$key]['filestr'] = EmployeeAbility_FileUpload_UIType::getDisplayValue($content['fileid']);
        }
        return $contents;
    }

    public function staffAbilityContentByLevel($recordId, $staffLevel)
    {
        global $adb;
        if ($staffLevel == 'all') {
            $sql = "select content from vtiger_employee_ability_detail where employeeabilityid=?";
            $result = $adb->pquery($sql, array($recordId));
            if ($adb->num_rows($result)) {
                $contentArr = array();
                while ($row = $adb->fetchByAssoc($result)) {
                    $content = json_decode(str_replace("&quot;", '"', $row['content']), true);
                    $content= $this->getFileContents($content);
                    $contentArr = array_merge($contentArr, $content);
                }
                return $contentArr;
            }

        } else {
            $sql = "select content from vtiger_employee_ability_detail where employeeabilityid = ? and stafflevel=? limit 1";
            $result = $adb->pquery($sql, array($recordId, $staffLevel));
            if ($adb->num_rows($result)) {
                $row = $adb->fetchByAssoc($result, 0);
                $content = str_replace("&quot;", '"', $row['content']);
                $content = json_decode($content, true);
                $content= $this->getFileContents($content);
                return $content;
            }
        }
        return $this->defaultJson($staffLevel);
    }

    public function insertAbility($userid,$departmentid)
    {
        global $adb;
        $defaultValues = $this->getDefaultValueAndColumns();
        $defaultValues['userid'] = $userid;
        $defaultValues['departmentid'] = $departmentid;
        $sql = "INSERT INTO vtiger_employee_ability(".implode(',',array_keys($defaultValues)).") values(".generateQuestionMarks($defaultValues).")";
        $adb->pquery($sql, $defaultValues);
        $result = $adb->pquery("select employeeabilityid from vtiger_employee_ability order by employeeabilityid desc limit 1",array());
        $row = $adb->fetchByAssoc($result,0);
        return $row['employeeabilityid'];
    }

    public function insertAbilityDetail($recordid, $userid)
    {
        global $adb;
        $junior = $this->defaultJson('junior',$userid);
        $intermediate = $this->defaultJson('intermediate');
        $senior = $this->defaultJson('senior');
        $sql = "insert into vtiger_employee_ability_detail  (`employeeabilityid`,`userid`,`stafflevel`,`content`) 
values(?,?,?,'" . json_encode($junior,JSON_UNESCAPED_UNICODE) . "'),(?,?,?,'" . json_encode($intermediate,JSON_UNESCAPED_UNICODE) . "'),(?,?,?,'" . json_encode($senior,JSON_UNESCAPED_UNICODE) . "')";
        $params = array(
            $recordid, $userid, 'junior',
            $recordid, $userid, 'intermediate',
            $recordid, $userid, 'senior'
        );
        $adb->pquery($sql, $params);
    }

    public function defaultJson($staffLevel,$userid=0)
    {
        $baseData = array(
            'wordsub' => 'nosubmitted',
            'step'=>0,
            'status' => '',
            'rejector' => '',
            'rejectreason' => '',
            'fileid' => '',
            'reviewresult'=>'',
            'nextreviewer'=>0,
            'rejectnum'=>0
        );

        $colleageDatas = array(
            "wordsub" => 0,
            'step'=>0,
            'status' => '',
            'rejector' => '',
            'rejectreason' => '',
            'fileid' => '',
            'reviewresult'=>'',
            'nextreviewer'=>0,
            'rejectnum'=>0
        );
        $columns =$this->getColumns();
        $colleageColumns = $this->getColleageColumns();
        $datas = array();
        $lastColumns = $columns[$staffLevel];
        if ($staffLevel == 'all') {
            $result = [];
            array_map(function ($value) use (&$result) {
                $result = array_merge($result, array_values($value));
            }, $columns);
//            foreach ($columns as $column){
//                $result = array_merge($result,$column);
//            }
            $lastColumns = $result;
        }

        foreach ($lastColumns as $column) {
            $data = $baseData;
            if (in_array($column, $colleageColumns)) {
                $colleageDatas2 = $colleageDatas;
                if(in_array($column,$this->getSpecialColumns())){
                    $colleageDatas2['step'] = 1;
                    $colleageDatas2['status'] = 'underreviewer';
                    $colleageDatas2['nextreviewer'] = 1;
                    $colleageDatas2['reviewresult']="经理:待审核#n#";
                }
                $data = $colleageDatas2;
            }
            $datas[$column] = $data;
        }
        return $datas;
    }

    public function getUserName($recordid){
        global $adb;
        $sql = "select b.last_name from vtiger_employee_ability a left join vtiger_users b on a.userid=b.id where a.employeeabilityid=? limit  1";
        $result = $adb->pquery($sql,array($recordid));
        if($adb->num_rows($result)){
            $row = $adb->fetchByAssoc($result,0);
            return $row['last_name'];
        }
        return '';
    }

    public function firstSubInitJson($field){
        $colleageColumns = $this->getColleageColumns();
        $columnvalues = array(
            'wordsub' => 'submitted',
            'status' => 'underreviewer',
            'step'=>1,
            'nextreviewer'=>1,
            'reviewresult'=>'经理:待处理',
            'rejector' => '',
            'rejectreason' => '',
            'subtime'=>date("Y-m-d H:i:s")
        );
        if(in_array($field,$colleageColumns)){
            $columnvalues['wordsub'] = 0;
        }
        return $columnvalues;
    }

    public function typeColumnData($type,$field,$wordsub='',$rejectreason=''){
        global $current_user;
        switch ($type) {
            case "pass":
                $columnvalues = array(
                    'rejector' => '',
                    'rejectreason' => '',
                    'passtime'=>date("Y-m-d H:i:s")
                );
                if(in_array($field,$this->getColleageColumns()) && $wordsub){
                    $columnvalues['wordsub'] = $wordsub;
                }
                break;
            case "reject":
                $columnvalues = array(
                    'rejector' => $current_user->last_name,
                    'rejectreason' => $rejectreason,
                    'status' => 'reject',
                    'reviewresult'=>'',
                    'nextreviewer'=>0,
                    'step'=>0,
                    'rejecttime'=>date("Y-m-d H:i:s")
                );
                if(in_array($field,$this->getColleageColumns()) && $wordsub){
                    $columnvalues['wordsub'] = $wordsub;
                }
                if(in_array($field,$this->getSpecialColumns())){
                    $columnvalues['step'] = 1;
                    $columnvalues['nextreviewer'] = 1;
                    $columnvalues['reviewresult'] = '经理:待审核';
                }
                break;
            default:
                $columnvalues = $this->firstSubInitJson($field);
                break;
        }
        return $columnvalues;
    }


    /**
     * 插入驳回历史
     *
     * @param $rejectParams
     */
    public function insertRejectHistory($rejectParams){
        global  $adb;
        $sql = "insert into vtiger_employee_ability_reject_history (`employeeabilityid`,`rejectcolumn`,`rejectreason`,`rejectnum`,`rejector`,`rejecttime`) values (?,?,?,?,?,?)";
        $adb->pquery($sql,array($rejectParams['employeeabilityid'],$rejectParams['rejectcolumn'],$rejectParams['rejectreason'],
            $rejectParams['rejectnum'],$rejectParams['rejector'],$rejectParams['rejecttime']));
    }

    /**
     * 根据员工能力id和字段查询最新一条的驳回历史
     *
     * @param $recordid
     * @param $fieldname
     * @return array
     */
    public function lastRejectHistory($recordId,$fieldName){
        global $adb;
        $sql = "select * from vtiger_employee_ability_reject_history where employeeabilityid=? and rejectcolumn=? order by employeeabilityrejecthistoryid desc limit 1";
        $result = $adb->pquery($sql,array($recordId,$fieldName));
        if(!$adb->num_rows($result)){
            return array();
        }
        return $adb->fetchByAssoc($result,0);
    }
     

}
