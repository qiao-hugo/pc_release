<?php
/*+******************
 *编辑页面的权限控制
 * 某些模块关联生成数据
 * 只能编辑不可新增
 **********************/

Class IdcRecords_Edit_View extends Vtiger_Edit_View {
    protected $record = false;
	
	 function __construct() {

		parent::__construct();
	}

    /**
     *
     * 显示域名换行 类型
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request){
        /*
         * 自定义编辑显示标题
         * */
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        $recordId = $request->get('record');

        if(!empty($recordId)){
            $db = PearDatabase::getInstance();
            $sql="SELECT accountname FROM `vtiger_account` WHERE accountid =(SELECT related_to FROM `vtiger_idcrecords` WHERE idcrecordsid = ".$recordId.")";
            $resultsql = $db->pquery($sql);
            $accountname = $db->query_result($resultsql,'accountname');
            if ($db->num_rows($resultsql)>0) {
                $viewer->assign('RECORD_ACCOUNTNAME', $accountname);
            }
        }

        //域名换行显示，状态显示与隐藏
        $viewer->assign('IDCRECORDS_NAME', IdcRecords_Record_Model::getIdcRecordsDomainName($recordId));
        $viewer->assign('IDCRECORDS_TYPE', IdcRecords_Record_Model::getIdcRecordsType($recordId));
        parent::process($request);
    }

}