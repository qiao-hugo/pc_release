<?php
/*+********
 *客户信息管理
 **********/

class ContractDelaySign_Record_Model extends Vtiger_Record_Model {
    public $tyunContinueColumn=array('matchdate','isdelay','creator');
    public $noTyunContinueColumn=array('delaydays','applydate','applyconfirmdate','file','activedate','modulestatus','creator','reason','file','contractsignstatus');
}
