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

class VCommentAnalysis_selectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getVCASIC');
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
    public function getvisitsdata(Vtiger_Request $request){
        $datayear=$request->get('datayear');
        $datamonth=$request->get('datamonth');
        $departmentid=$request->get('department');
        $departmentid=empty($departmentid)?array('H1'):$departmentid;
        $query="SELECT
                    vtiger_visitdepartment.deparmentid,
                    vtiger_visitdepartment.visitdepartmentid,
                    visitingnum,
                    visitingcommnum,
                    classic,
                    commentresult,
                    poornumber,
                    poorproportion,
                    schedule,
                    scheduleremark,
                    vtiger_visitdepartment.statussummary,
                    createdtime
                FROM
                    vtiger_visitcommentanalysis
                LEFT JOIN vtiger_visitdepartment
                ON vtiger_visitcommentanalysis.visitdepartmentid=vtiger_visitdepartment.visitdepartmentid
                WHERE
                deleted = 0
                AND vtiger_visitdepartment.deparmentid in('".implode("','",$departmentid)."')
                AND vtiger_visitdepartment.`year`={$datayear}
                AND vtiger_visitdepartment.`month`='{$datamonth}'
                ORDER BY
                vtiger_visitdepartment.deparmentid,
                    vtiger_visitcommentanalysis.classic,
                    vtiger_visitcommentanalysis.commentresult";
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        if($num){
            $array=array();
            $data=array();
            include 'crmcache/departmentanduserinfo.php';
            for($i=0;$i<$num;$i++){
                $data[]=$db->query_result_rowdata($result,$i);
                $depart=$db->query_result($result,$i,'deparmentid');
                $classic=$db->query_result($result,$i,'classic');
                ++$array[$depart]['departnum'];
                $array[$depart]['departname']=$cachedepartment[$depart];
                ++$array[$depart][$classic];

            }
            return array('data'=>$data,'datadep'=>$array);
        }else{

            return array();
        }
    }
    public function getVCASIC(Vtiger_Request $request){
        $data=$this->getvisitsdata($request);

        if(!empty($data)){
            $datayear=$request->get('datayear');
            $datamonth=$request->get('datamonth');
            $array=$data['data'];
            $dep=$data['datadep'];
            $text='';
            $tempdep='';
            $tempclassid='';
            foreach($array as $key1=>$value1){
                $text.='<tr>';
                $tempstr='';
                if($value1['deparmentid']!=$tempdep){
                    $text.='<td style="text-align: center;vertical-align:middle;" title="部门" rowspan="'.$dep[$value1['deparmentid']]['departnum'].'">'.$dep[$value1['deparmentid']]['departname'].'</td>';
                    $tempdep=$value1['deparmentid'];
                    $text.='<td style="text-align: center;vertical-align:middle;" title="拜访单数量" rowspan="'.$dep[$value1['deparmentid']]['departnum'].'">'.$value1['visitingnum'].'</td>
                    <td style="text-align: center;vertical-align:middle;" title="点评数量" rowspan="'.$dep[$value1['deparmentid']]['departnum'].'">'.$value1['visitingcommnum'].'</td>';
                    $tempstr='
                    <td style="width:20%" title="总评估" rowspan="'.$dep[$value1['deparmentid']]['departnum'].'">'.$value1['statussummary'].'</td>
                    <td style="width:20%" title="改进进度" rowspan="'.$dep[$value1['deparmentid']]['departnum'].'">'.$value1['scheduleremark'].'</td>
                    <td title="进度" rowspan="'.$dep[$value1['deparmentid']]['departnum'].'">'.$value1['schedule'].'%</td>';
                }
                if(($value1['deparmentid'].$value1['deparmentid'])!=$tempclassid) {
                    $text .= '<td style="text-align: center;vertical-align:middle;" title="类型" rowspan="'.$dep[$value1['deparmentid']]['classic'].'">' .vtranslate($value1['classic'],'VisitAccountContract'). '</td>';
                    $tempclassid=$value1['deparmentid'].$value1['classic'];
                }
                $text.='<td style="text-align: center;vertical-align:middle;" title="点评结果">'.vtranslate($value1['commentresult'],'VisitAccountContract').'</td>
                    <td style="text-align: center;vertical-align:middle;" title="数量">'.$value1['poornumber'].'</td>
                    <td >'.number_format(($value1['poornumber']/$value1['visitingcommnum']*100),2).'%</td>
                    '.$tempstr.'
                </tr>';


            }
            $table='
                <table class="table table-bordered hide" id="flalted" style="z-index:100000;">
                    <thead>
                    <tr id="flalte1"  style="background-color:#ffffff;">
                        <th  style="text-align: center;vertical-align:middle;">部门&nbsp;'.$datayear.'-'.$datamonth.'</th>
                        <th  style="text-align: center;vertical-align:middle;">拜访单数量</th>
                        <th  style="text-align: center;vertical-align:middle;">点评数量</th>
                        <th  style="text-align: center;vertical-align:middle;">类型</th>
                        <th  style="text-align: center;vertical-align:middle;">点评结果</th>
                        <th  style="text-align: center;vertical-align:middle;">数量</th>
                        <th style="text-align: center;vertical-align:middle;">占比</th>
                        <th style="text-align: center;vertical-align:middle;">总评估</th>
                        <th style="text-align: center;vertical-align:middle;">改进状况</th>
                        <th style="text-align: center;vertical-align:middle;">改进进度</th>
                    </tr>
                    </thead>
                    
                </table>';
            $table.='
                <table class="table table-bordered table-striped" id="one1">
                    <thead>
                    <tr id="flaltt1">
                        <th  style="text-align: center;vertical-align:middle;">部门&nbsp;'.$datayear.'-'.$datamonth.'</th>
                        <th  style="text-align: center;vertical-align:middle;">拜访单数量</th>
                        <th  style="text-align: center;vertical-align:middle;">点评数量</th>
                        <th  style="text-align: center;vertical-align:middle;">类型</th>
                        <th  style="text-align: center;vertical-align:middle;">点评结果</th>
                        <th  style="text-align: center;vertical-align:middle;">数量</th>
                        <th style="text-align: center;vertical-align:middle;">占比</th>
                        <th style="text-align: center;vertical-align:middle;">总评估</th>
                        <th style="text-align: center;vertical-align:middle;">改进状况</th>
                        <th style="text-align: center;vertical-align:middle;">改进进度</th>
                    </tr>
                    </thead>
                    <tbody>
                    '.$text.'
                    </tbody>
                </table><br><br><br>';
            echo $table;
            exit;


        }else{

            echo '<table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="text-align: center;vertical-align:middle;">没有记录</th>
                    </tr></thead></table>';
            exit;
        }
    }

}
