<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Workday_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
        $this->exposeMethod('getMothWork');
        $this->exposeMethod('setMothDay');

        $this->exposeMethod('getMothWorkHighSeas');
        $this->exposeMethod('setMothDayHighSeas');
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
    public function getMothWork(Vtiger_Request $request){
        $date=$request->get('date');
        $start=$request->get('start');
        $end=$request->get('end');

        $currentdatatim=($start+$end)/2;
        if(empty($currentdatatim)){
            $monthdays=date('t');
            $years=date('Y');
            $months=date('m');
            $yearmonth=date('Y-m');
        }else{
            $monthdays=date('t',$currentdatatim);
            $years=date('Y',$currentdatatim);
            $months=date('m',$currentdatatim);
            $yearmonth=date('Y-m',$currentdatatim);
        }
        $arrtemp=array();

        $db=PearDatabase::getInstance();
        $result=$db->pquery("SELECT dateday,datetype FROM vtiger_workday WHERE left(dateday,7) = ?",array($yearmonth));
        $datanum=$db->num_rows($result);
        if($datanum) {

            $arrflag=array();
            while($rawData=$db->fetch_array($result)){
                if($rawData['datetype']=='holiday'){
                    $arrflag[]=substr($rawData['dateday'],8);
                }
            }


            for ($i = 1; $i <= $monthdays; $i++) {
                $isholiday = date('w', strtotime($yearmonth . '-' . $i));
                if (in_array($i,$arrflag)) {
                    //????????????
                    $temp['backgroundColor'] = '#468847';//??????
                    $temp['textColor'] = '#ffffff';//??????
                    $temp['title'] = '??????';
                }else if (($isholiday == 0 || $isholiday == 6) && !in_array($i,$arrflag)) {
                    //????????????
                    $temp['backgroundColor'] = '#3a87ad';//??????
                    $temp['textColor'] = '#f89406';//??????
                    $temp['title'] = '??????';
                } else {
                    $temp['backgroundColor'] = '#3a87ad';//??????
                    $temp['textColor'] = '#fffff';//??????
                    $temp['title'] = '??????';

                }
                $temp['start'] = $yearmonth . '-' . $i;


                $temp['id'] = $years . $months . $i;
                $arrtemp[] = $temp;
            }
        }else{
            for ($i = 1; $i <= $monthdays; $i++) {
                $isholiday = date('w', strtotime($yearmonth . '-' . $i));
                if ($isholiday == 0 || $isholiday == 6) {
                    //????????????
                    $temp['backgroundColor'] = '#468847';//??????
                    $temp['textColor'] = '#FF0000';//??????
                    $temp['title'] = '??????';
                } else {
                    $temp['backgroundColor'] = '#3a87ad';//??????
                    $temp['title'] = '??????';
                    $temp['textColor'] = '#ffffff';//??????
                }
                $temp['start'] = $yearmonth . '-' . $i;
                //$temp['title']='<select name="ids['.$years.$months.$i.']"><option value="work">???</option><option value="holiday">???</option></select>';


                $temp['id'] = $years . $months . $i;
                $arrtemp[] = $temp;
            }
        }
        return json_encode($arrtemp);

    }
    public function setMothDay(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $datetime=$request->get('datetime');
        //$workdayid=$request->get('recordid');
        $workdayid=date('Ymd',strtotime($datetime));
        $datetype=$request->get('datetype');

        $db->pquery("REPLACE INTO vtiger_workday(workdayid,dateday,datetype) values(?,?,?)",array($workdayid,$datetime,$datetype));
    }
    /**
     * @param Vtiger_Request $request
     * @return string
     * @author: steel.liu
     * @Date:xxx
     * ?????????????????????
     */
    public function getMothWorkHighSeas(Vtiger_Request $request){
        $date=$request->get('date');
        $start=$request->get('start');
        $end=$request->get('end');

        $currentdatatim=($start+$end)/2;
        if(empty($currentdatatim)){
            $monthdays=date('t');
            $years=date('Y');
            $months=date('m');
            $yearmonth=date('Y-m');
        }else{
            $monthdays=date('t',$currentdatatim);
            $years=date('Y',$currentdatatim);
            $months=date('m',$currentdatatim);
            $yearmonth=date('Y-m',$currentdatatim);
        }
        $arrtemp=array();

        $db=PearDatabase::getInstance();
        $result=$db->pquery("SELECT dateday,datetype FROM vtiger_workdayhighseas WHERE left(dateday,7) = ?",array($yearmonth));
        $datanum=$db->num_rows($result);
        if($datanum) {

            $arrflag=array();
            while($rawData=$db->fetch_array($result)){
                if($rawData['datetype']=='holiday'){
                    $arrflag[]=substr($rawData['dateday'],8);
                }
            }


            for ($i = 1; $i <= $monthdays; $i++) {
                $isholiday = date('w', strtotime($yearmonth . '-' . $i));
                if (in_array($i,$arrflag)) {
                    //????????????
                    $temp['backgroundColor'] = '#468847';//??????
                    $temp['textColor'] = '#ffffff';//??????
                    $temp['title'] = '?????????';
                }else if (($isholiday == 0 || $isholiday == 6) && !in_array($i,$arrflag)) {
                    //????????????
                    $temp['backgroundColor'] = '#3a87ad';//??????
                    $temp['textColor'] = '#f89406';//??????
                    $temp['title'] = '??????';
                } else {
                    $temp['backgroundColor'] = '#3a87ad';//??????
                    $temp['textColor'] = '#fffff';//??????
                    $temp['title'] = '??????';

                }
                $temp['start'] = $yearmonth . '-' . $i;


                $temp['id'] = $years . $months . $i;
                $arrtemp[] = $temp;
            }
        }else{
            for ($i = 1; $i <= $monthdays; $i++) {
                $isholiday = date('w', strtotime($yearmonth . '-' . $i));
                /*if ($isholiday == 0 || $isholiday == 6) {
                    //????????????
                    $temp['backgroundColor'] = '#468847';//??????
                    $temp['textColor'] = '#FF0000';//??????
                    $temp['title'] = '?????????';
                } else {*/
                    $temp['backgroundColor'] = '#3a87ad';//??????
                    $temp['title'] = '??????';
                    $temp['textColor'] = '#ffffff';//??????
                //}
                $temp['start'] = $yearmonth . '-' . $i;
                //$temp['title']='<select name="ids['.$years.$months.$i.']"><option value="work">???</option><option value="holiday">???</option></select>';


                $temp['id'] = $years . $months . $i;
                $arrtemp[] = $temp;
            }
        }
        return json_encode($arrtemp);

    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * ?????????????????????
     */
    public function setMothDayHighSeas(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $datetime=$request->get('datetime');
        //$workdayid=$request->get('recordid');
        $workdayid=date('Ymd',strtotime($datetime));
        $datetype=$request->get('datetype');

        $db->pquery("REPLACE INTO vtiger_workdayhighseas(workdayhighseasid,dateday,datetype) values(?,?,?)",array($workdayid,$datetime,$datetype));
    }
}
