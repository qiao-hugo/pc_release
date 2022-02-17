<?php
/*+******************
 *编辑页面的权限控制
 * 某些模块关联生成数据
 * 只能编辑不可新增
 **********************/

Class IronAccount_Edit_View extends Vtiger_Edit_View {
    protected $record = false;
	
	 function __construct() {

		parent::__construct();
	}

    /**
     *
     * 客服跟进，转铁牌客户
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {

        $db = PearDatabase::getInstance();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $related_to = $request->get('related_to');
        if(!empty($related_to)){
            $sql="SELECT accountname FROM `vtiger_account` WHERE accountid =?";
            $resultdb=$db-> pquery($sql,array($related_to));
            if($db->num_rows($resultdb)>0) {
                $accountname = $db->query_result($resultdb, 0, 'accountname');
            }else{
                $accountname = '客户名称查找不到';
            }

            $viewer->assign('HIDDEN_RELATED_TO', $related_to);
            $viewer->assign('HIDDEN_ACCOUNTNAME', $accountname);
        }

        parent::process($request);
    }

}