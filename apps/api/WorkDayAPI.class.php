<?php

class WorkDayAPI extends baseapi
{
    public function nextWorkDay()
    {
        $datetype = $_REQUEST['datetype'];
        $datetime = $_REQUEST['datetime'];

        $params = array(
            'fieldname' => array(
                'module' => 'Workday',
                'action' => 'getNextWorkDay',
                'datetype' => $datetype,
                'datetime' => $datetime
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        $this->_logs(array("返回结果(getNextWorkDay)：", $res));
        if (!empty($res[0])) {
            echo json_encode(array('success' => 'true', 'code' => 200, 'data' => $res[0]), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }


    }
}