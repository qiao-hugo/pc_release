<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Scorevendor_SaveScore_Action extends Vtiger_Save_Action {

	function __construct() {
        parent::__construct();
    }

	function checkPermission(Vtiger_Request $request) {

        return;
	}

    // 百分比转小数
    static public function percentageToNumber($percentage) {
        if(!empty($percentage)){
            $percentage = intval($percentage);
            return round($percentage / 100, 2);
        }
        return 0;
    }

    /**
     * @ruthor steel
     * @time 2015-05-04
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
        $recordId = $request->get('record');
        $scorevendorModel = Vtiger_DetailView_Model::getInstance('Scorevendor', $recordId)->getRecord();;
        $entity = $scorevendorModel->entity->column_fields;

        $scoreModelEntity = Scoremodel_Record_Model::getScoremodel($entity['scoremodelid']);
        $scoreContent = $scoreModelEntity['scoremodel_content'];
        $scoreData = $_REQUEST['question']; 

        // 总分
        $score_total = 0;
        foreach ($scoreData as $scoreData_vale) {
            $scoreparData = $scoreContent[$scoreData_vale]; // 组件数据
            $scorepaper_itme_type = $scoreparData['scorepaper_itme_type']; 

            $answer = $_REQUEST[$scorepaper_itme_type][$scoreData_vale]; // 答案
            $scoreContent[$scoreData_vale]['answer'] = $answer;  // 保存答案

            switch ($scorepaper_itme_type) {
                case 'o_text':
                    $answer = empty($answer) ? '' : $answer;
                    // 计算分数 根据输入的字数判断  这里如果没有到上限或者下限 默认为0
                    $score_item = 0;
                    $str_len = mb_strlen($answer, 'utf-8');
                    $scorepara_info = $scoreparData['scorepaper_itme_scorepara_info'];
                    foreach ($scorepara_info as $value) {
                        if($str_len <= $value['scorepara_lower'] && $str_len >= $value['scorepara_upper']) {
                            $score_item = $value['scorepara_score'];
                        }
                    }
                    $scoreContent[$scoreData_vale]['score_item'] = $score_item; //保存分数

                    $score_actual = round($score_item * self::percentageToNumber($scoreparData['scorepaper_itme_weight'])); 
                    $scoreContent[$scoreData_vale]['score_actual'] = $score_actual;//实际分数
                    $score_total += $score_actual;
                    break;
                case 'o_number':
                    $answer = empty($answer) ? 0 : $answer;
                    $answer = intval($answer);
                    $score_item = $answer;                      // 分数直接等于答案

                    $scoreContent[$scoreData_vale]['score_item'] = $score_item; //保存分数

                    $score_actual = round($score_item * self::percentageToNumber($scoreparData['scorepaper_itme_weight'])); 
                    $scoreContent[$scoreData_vale]['score_actual'] = $score_actual;//实际分数
                    $score_total += $score_actual;

                    break; 
                case 'o_select':
                    $answer = empty($answer) ? '' : $answer;
                    $score_item = 0;

                    $scorepara_info = $scoreparData['scorepaper_itme_scorepara_info'];
                    foreach ($scorepara_info as $value) {
                        if($answer == $value['scoreparaid']) {
                            $score_item = $value['scorepara_score'];
                        }
                    }
                    $scoreContent[$scoreData_vale]['score_item'] = $score_item; //保存分数

                    $score_actual = round($score_item * self::percentageToNumber($scoreparData['scorepaper_itme_weight'])); 
                    $scoreContent[$scoreData_vale]['score_actual'] = $score_actual;//实际分数
                    $score_total += $score_actual;
                    break;
                case 'o_check':
                    $answer = is_array($answer) ? $answer : array();
                    $scorepara_info = $scoreparData['scorepaper_itme_scorepara_info'];
                    $score_item = 0;
                    if (count($answer) > 0 && count($scorepara_info) > 0) {
                        foreach ($scorepara_info as $value) {
                            if(in_array($value['scoreparaid'], $answer)) {
                                $score_item += $value['scorepara_score'];
                            }
                        }
                    }
                    $scoreContent[$scoreData_vale]['score_item'] = $score_item; //保存分数

                    $score_actual = round($score_item * self::percentageToNumber($scoreparData['scorepaper_itme_weight'])); 
                    $scoreContent[$scoreData_vale]['score_actual'] = $score_actual;//实际分数
                    $score_total += $score_actual;
                    break;
                case 'o_radio':
                    $answer = empty($answer) ? '' : $answer;
                    $score_item = 0;

                    $scorepara_info = $scoreparData['scorepaper_itme_scorepara_info'];
                    foreach ($scorepara_info as $value) {
                        if($answer == $value['scoreparaid']) {
                            $score_item = $value['scorepara_score'];
                        }
                    }
                    $scoreContent[$scoreData_vale]['score_item'] = $score_item; //保存分数

                    $score_actual = round($score_item * self::percentageToNumber($scoreparData['scorepaper_itme_weight'])); 
                    $scoreContent[$scoreData_vale]['score_actual'] = $score_actual;//实际分数
                    $score_total += $score_actual;
                    break;
                case 'o_numberinterval':
                    $answer = empty($answer) ? 0 : $answer;
                    $answer = intval($answer);
                    $scorepara_info = $scoreparData['scorepaper_itme_scorepara_info'];

                    $score_item = 0;
                    foreach ($scorepara_info as $value) {
                        if($answer <= $value['scorepara_lower'] && $answer >= $value['scorepara_upper']) {
                            $score_item = $value['scorepara_score'];
                        }
                    }
                    $scoreContent[$scoreData_vale]['score_item'] = $score_item; //保存分数

                    $score_actual = round($score_item * self::percentageToNumber($scoreparData['scorepaper_itme_weight'])); 
                    $scoreContent[$scoreData_vale]['score_actual'] = $score_actual;//实际分数
                    $score_total += $score_actual;
                    break;
                default:
                    break;
            }
        }

        // 保存总分 和 提交的数据
        $db = PearDatabase::getInstance();
        $sql = "update vtiger_scorevendor set is_score=1, scoretotal=?, scoredate=?, scorecontent=? where scorevendorid=?";
        $db->pquery($sql, array($score_total, date('Y-m-d H:i'), addslashes(json_encode($scoreContent)), $recordId ));

        $record = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'Scorevendor');
        $loadUrl = $recordModel->getDetailViewUrl();
        if(empty($loadUrl)){
            $loadUrl="index.php";
        }
        header("Location: $loadUrl");
		/*$response = new Vtiger_Response();
		$response->setResult($return);
		$response->emit();*/
	}
}
