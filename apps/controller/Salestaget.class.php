<?php

class Salestaget extends baseapp{
	#周报
	public function index(){
		//$params = array('userid'=>$this->userid);
		$params = array('userid'=>$this->userid);
		$res = $this->call('getSalestagetInfo', $params);

		/*if($this->userid == '3668'){
			var_dump($res);
			exit;
		}*/
		$weekData = $res[0]['weekData'];
		$monthData = $res[0]['monthData'];

		

		if (!empty($weekData)) {
			$weekData['startdate'] = date('m月d日', strtotime($weekData['startdate']));
			$weekData['enddate'] = date('m月d日', strtotime($weekData['enddate']));
		}
		if (!empty($monthData)) {
			$monthData['startdate'] = $monthData['month'] . '月1日';
			$ttt = $monthData['year'] . '-' . $monthData['month'] . '-01';
			$monthData['enddate'] = date('m月d日', strtotime('+1 month -1 day', strtotime($ttt) ));
		}

		if($weekData[0] == 1) {
			$week = date('w');
			$weekData['startdate'] = date('Y-m-d', strtotime( '+'. 1-$week .' days' ));;
			$weekData['enddate'] = date('Y-m-d', strtotime( '+'. 7-$week .' days' ));
			// 当前月的最后一天
			$ttt = date('Y-m-01');
			$monthLastDay = date('Y-m-d', strtotime('+1 month -1 day', strtotime($ttt) ));
			if (strtotime($weekData['enddate']) > strtotime($monthLastDay) ) {
				$weekData['enddate'] = $monthLastDay;
			}
			$weekData['startdate'] = date('m月d日', strtotime($weekData['startdate']));
			$weekData['enddate'] = date('m月d日', strtotime($weekData['enddate']));
		}
		if($monthData[0] == 1) {
			$monthData['startdate'] = date('Y-m-01');
			$monthData['enddate'] = date('Y-m-d', strtotime('+1 month -1 day', strtotime($monthData['startdate']) ));
			$monthData['startdate'] = date('m月d日', strtotime($monthData['startdate']));
			$monthData['enddate'] = date('m月d日', strtotime($monthData['enddate']));
		}
		$this->smarty->assign('weekData', $weekData);
		$this->smarty->assign('monthData', $monthData);
		$this->smarty->assign('is_depa', $res[0]['is_depa']);
		$this->smarty->display('Salestaget/index.html');
	}
   
}

