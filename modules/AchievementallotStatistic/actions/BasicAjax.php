<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AchievementallotStatistic_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
        $this->exposeMethod('exportdata');
        $this->exposeMethod('addDepartment');
        $this->exposeMethod('delDepartID');
        $this->exposeMethod('deletedBaiduvSetting');
        $this->exposeMethod('saveBaiduvSetting');
        $this->exposeMethod('deletedBaiduvWages');
        $this->exposeMethod('saveBaiduvWages');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}

    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}

	}

    /**
     * @param Vtiger_Request $request
     * 自定义导出xlsx文件--替换以前的csv
     */
    public function exportdata(Vtiger_Request $request){
        set_time_limit(0);
        global $site_URL,$current_user,$root_directory,$adb,$currentView;
        $currentView='List';
        $listViewModel = Vtiger_ListView_Model::getInstance("AchievementallotStatistic");
        $listQuery = $listViewModel->getQuery();
        $listViewModel->getSearchWhere();
        $listQuery.=$this->getUserWhere();
        $queryGenerator = $listViewModel->get('query_generator');

        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $listQuery .= ' ORDER BY vtiger_departments.parentdepartment DESC ';

        $listQuery=str_replace(',vtiger_achievementallot_statistic.achievementallotid FROM vtiger_achievementallot_statistic',',vtiger_achievementallot_statistic.departmentid,vtiger_achievementallot_statistic.achievementallotid FROM vtiger_achievementallot_statistic LEFT JOIN vtiger_departments ON vtiger_departments.departmentid = vtiger_achievementallot_statistic.departmentid',$listQuery);

        //有业绩月份搜索时，需要替换这个条件
        $getMonth=$listViewModel->getSearchWhereAchievementmonth();
        if($getMonth){
            $getMonths=$getMonth;
            $str="vtiger_achievementallot_statistic.achievementmonth >= '".$getMonth." 00:00:00'";
            $getMonth="vtiger_achievementallot_statistic.achievementmonth >= '".$getMonth."'";
            $listQuery=str_replace($str,$getMonth,$listQuery);
            $str="vtiger_achievementallot_statistic.achievementmonth <= '".$getMonths." 00:00:00'";
            $getMonths="vtiger_achievementallot_statistic.achievementmonth <= '".$getMonths."'";
            $listQuery=str_replace($str,$getMonths,$listQuery);
        }

        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();

        include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $phpexecl=new PHPExcel();
        $phpexecl->getProperties()->setCreator("liu ganglin")
            ->setLastModifiedBy("liu ganglin")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("AccountPlatform");

        $headerCodes = getExcelHeaderCode(count($listViewHeaders)+5);
        $headerArray = [];
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    if($listViewHeaders[$key]['ishidden']){
                        continue;
                    }
                    $headerArray[$key] = $listViewHeaders[$key];
                }
            }
        }

        if(empty($headerArray)){
            $headerArray = $listViewHeaders;
        }

        $step = 0;
        foreach($headerArray as $key => $val){
            if($val['ishidden']){
                continue;
            }
            $headerTitle = vtranslate($key,'AchievementallotStatistic');
            $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step].'1',$headerTitle);
            $step++;
            //导出表格在“区域”后增加体系
            if($key == 'Receivedpaymentownid'){
                $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step].'1','一级部门');
                $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+1].'1','二级部门');
                $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+2].'1','三级部门');
                $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+3].'1','四级部门');
                $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+4].'1','五级部门');
                $step += 5;
            }
            
        }

        ini_set('memory_limit','512M');
        $path=$root_directory.'temp/';
        $filename=$path.'achievementallotstatistic'.$current_user->id.'.xlsx';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);

        $listViewModel->isAllCount=1;
        $listCount = $listViewModel->getListViewCount();
        $limitStep=1000;
        $num=ceil($listCount/$limitStep);
        $cnt = 0;
        include 'crmcache/departmentanduserinfo.php';
        $current = 2;
        $depar = array(
            ['value'=>'','start'=>'','end'=>''],
            ['value'=>'','start'=>'','end'=>''],
            ['value'=>'','start'=>'','end'=>''],
            ['value'=>'','start'=>'','end'=>''],
            ['value'=>'','start'=>'','end'=>''],
        );
        for($i=0;$i<$num;$i++) {
            $limitSQL=" limit ".$i*$limitStep.",".$limitStep;
            $result = $adb->pquery($listQuery . $limitSQL, array());
            while ($value = $adb->fetch_array($result)) {
                $achievementallotid = $value['achievementallotid'];
                $departmentid = $value['departmentid'];
                $step = 0;
                foreach ($headerArray as $keyheader => $valueheader) {
                    if($valueheader['ishidden']){
                        continue;
                    }
                    if ($valueheader['uitype'] == 10) {
                        $currnetValue = uitypeformat($valueheader, $value, 'AchievementallotStatistic');
                        $pattern = '/<[^>]+>/';
                        $currnetValue = preg_replace($pattern, '', $currnetValue);
                    } elseif ($valueheader['uitype'] == 15) {
                        $currnetValue = vtranslate($value[$keyheader], 'AchievementallotStatistic');
                        $pattern = '/<[^>]+>/';
                        $currnetValue = preg_replace($pattern, '', $currnetValue);
                    }elseif ($valueheader['uitype'] == 53) {
                        $currnetValue = uitypeformat($valueheader, $value, 'AchievementallotStatistic');
                        $pattern = '/<[^>]+>/';
                        $currnetValue = preg_replace($pattern, '', $currnetValue);
                    }else {
                        $currnetValue = uitypeformat($valueheader, $value, 'AchievementallotStatistic');
                    }
                    $phpexecl->setActiveSheetIndex(0)->setCellValueExplicit($headerCodes[$step].$current, $currnetValue);
                    $step++;

                    //导出表格的“业务员”后增加体系结构
                    if($keyheader == 'Receivedpaymentownid'){
                        $parentdepartment = $departmenttoparent[$departmentid];
                        $parentDepartmentArr = explode('::', $parentdepartment);
                        $parentDepartmentArr = array_values(array_diff($parentDepartmentArr, ['H1']));
                        for ($j=0; $j < 5; $j++) {
                            if(!isset($parentDepartmentArr[$j])){
                                $departmentsName = '';
                            }else{
                                $departmentsName = $cachedepartment[$parentDepartmentArr[$j]];
                            }
                            $phpexecl->setActiveSheetIndex(0)->setCellValueExplicit($headerCodes[$step+$j].$current, $departmentsName);

                            if($departmentsName != $depar[$j]['value'] && $depar[$j]['start'] != $depar[$j]['end']){
                                $phpexecl->setActiveSheetIndex(0)->mergeCells($depar[$j]['start'].':'.$depar[$j]['end']); 
                                $phpexecl->setActiveSheetIndex(0)->getStyle($depar[$j]['start'])->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            }

                            if($departmentsName == ''){
                                $depar[$j]['value'] = '';
                                $depar[$j]['end'] = $headerCodes[$step+$j].$current;
                                $depar[$j]['start'] = $headerCodes[$step+$j].$current;
                            }else{
                                if($departmentsName == $depar[$j]['value']){
                                    $depar[$j]['end'] = $headerCodes[$step+$j].$current;
                                }else{
                                    $depar[$j]['value'] = $departmentsName;
                                    $depar[$j]['start'] = $headerCodes[$step+$j].$current;
                                    $depar[$j]['end'] = $headerCodes[$step+$j].$current;
                                }
                            }
                        }
                        $step += 5;
                    }
                }
                
                ++$cnt;
                if ($limitStep == $cnt) {
                    $cnt = 0;
                }
                $current++;
            }
        }

        //合并最后的相同部门
        foreach($depar as $val){
            if($val['start'] != $val['end']){
                $phpexecl->setActiveSheetIndex(0)->mergeCells($val['start'].':'.$val['end']); 
                $phpexecl->setActiveSheetIndex(0)->getStyle($val['start'])->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            }
        }
        // 设置工作表的名称
        $phpexecl->getActiveSheet()->setTitle('销售业绩明细表');
        $phpexecl->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');

        $objWriter->save($filename);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

    public function addDepartment(Vtiger_Request $request){
        $userid=$request->get("userid");
        $dempart=$request->get("department");
        $data='添加失败';
        do {
            if(empty($userid)){
                break;
            }
            if(empty($dempart)){
                break;
            }
            $value="({$userid},'".implode(',',$dempart)."','AchievementallotStatistic')";

            $value=rtrim($value,',');
            $sql="INSERT INTO vtiger_custompermtable(userid,permissions,`module`) VALUES{$value}";
            $delsql="DELETE FROM vtiger_custompermtable WHERE userid=?";
            $db=PearDatabase::getInstance();
            $db->pquery($delsql,array($userid));
            $db->pquery($sql,array());
            $data='添加成功';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }
    public function delDepartID(Vtiger_Request $request){
        $id=$request->get("did");
        $delsql="DELETE FROM vtiger_custompermtable WHERE custompermtableid=?";
        $db=PearDatabase::getInstance();
        $db->pquery($delsql,array($id));
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }
    /**
     * 数据导出调整为按部门导出
     * @return string
     */
    public function getUserWhere(){
        global $adb,$current_user;
        $query="SELECT permissions FROM vtiger_custompermtable WHERE module='AchievementallotStatistic' and userid=? limit 1";
        $result=$adb->pquery($query,array($current_user->id));
        $where=array();
        if($adb->num_rows($result)){
            $permissions=$result->fields['permissions'];
            $permissionsArray=explode(',',$permissions);
            foreach($permissionsArray as $value){
                $where=array_merge($where,getDepartmentUser($value));
            }
        }
        $listQuery='';
        $searchDepartment = $_REQUEST['department'];
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){
            $userid=getDepartmentUser($searchDepartment);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_achievementallot_statistic.receivedpaymentownid in ('.implode(',',$where).')';
        }else{
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_achievementallot_statistic.receivedpaymentownid in ('.implode(',',$where).')';
        }
        $listQuery.=" AND (vtiger_achievementallot_statistic.achievementmonth IS NOT NULL OR vtiger_achievementallot_statistic.is_deduction=1)";
        return $listQuery;

    }

    /**
     * 保存CWSH
     * @param Vtiger_Request $request
     */
    public function saveBaiduvSetting(Vtiger_Request $request){
        $department=$request->get('department');
        $settingmonth=$request->get('settingmonth');
        $peoplenum=$request->get('peoplenum');
        $monthpeoplemoney=$request->get('monthpeoplemoney');
        $unmonthpeoplemoney=$request->get('unmonthpeoplemoney');
        $quarterlytasks=$request->get('quarterlytasks');
        $db = PearDatabase::getInstance();
        $sql="select * from vtiger_baiduvsetting where department=? and settingmonth=?";
        $result=$db->pquery($sql,array($department,$settingmonth));
        if($db->num_rows($result)){
            $rs['flag']=false;
            $rs['msg']='已存在此部门的数据';
        }else{
            $rs['flag']=true;
            $data['department']=$department;
            $data['settingmonth']=$settingmonth;
            $data['peoplenum']=$peoplenum;
            $data['monthpeoplemoney']=$monthpeoplemoney;
            $data['unmonthpeoplemoney']=$unmonthpeoplemoney;
            $data['quarterlytasks']=$quarterlytasks;
            $db->run_insert_data('vtiger_baiduvsetting',$data);
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($rs);
        $response->emit();
    }

    /**
     * 删除百度v设置
     * @param Vtiger_Request $request
     */
    public function deletedBaiduvSetting(Vtiger_Request $request){
        $id=$request->get('id');
        $db = PearDatabase::getInstance();
        $sql='DELETE FROM vtiger_baiduvsetting WHERE baiduvsettingid=?';
        $db->pquery($sql,array($id));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }

    /**
     * 保存百度v用户工资
     * @param Vtiger_Request $request
     */
    public function saveBaiduvWages(Vtiger_Request $request){
        $userid=$request->get('userid');
        $setmonth=$request->get('setmonth');
        $staffwages=$request->get('staffwages');

        $db = PearDatabase::getInstance();
        $sql="select * from vtiger_baiduvstaffwages where userid=? and setmonth=?";
        $result=$db->pquery($sql,array($userid,$setmonth));
        if($db->num_rows($result)){
            $rs['flag']=false;
            $rs['msg']='已存在此用户的数据';
        }else{
            $rs['flag']=true;
            $data['userid']=$userid;
            $data['setmonth']=$setmonth;
            $data['staffwages']=$staffwages;
            $db->run_insert_data('vtiger_baiduvstaffwages',$data);
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($rs);
        $response->emit();
    }

    /**
     * 删除用户工资信息
     * @param Vtiger_Request $request
     */
    public function deletedBaiduvWages(Vtiger_Request $request){
        $id=$request->get('id');
        $db = PearDatabase::getInstance();
        $sql='DELETE FROM vtiger_baiduvstaffwages WHERE baiduvstaffwagesid=?';
        $db->pquery($sql,array($id));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }
}
