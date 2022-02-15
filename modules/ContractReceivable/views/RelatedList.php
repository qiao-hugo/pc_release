<?php
/*+***************
 * 详情页面的关联列表显示
 * 
 * 
 **********/

class ContractReceivable_RelatedList_View extends Vtiger_RelatedList_View {
    function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $relatedModuleName = $request->get('relatedModule');
        $parentId = $request->get('record');
        $label = $request->get('tab_label');
        $requestedPage = $request->get('page');
        if(empty($requestedPage)) {
            $requestedPage = 1;
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page',$requestedPage);
        //获取当前记录数据验证权限

        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);


        $viewer = $this->getViewer($request);
        $viewer->assign('RELATION_MODULENAME', $relatedModuleName);



        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('PAGING', $pagingModel);

        $payments = $parentRecordModel->getAccountReceivedPayments();

        $viewer->assign('CONTRACTS', $payments['contracts']);
        $viewer->assign('TOTALSTANDARDMONEY', $payments['total_standard_money']);
        $viewer->assign('UNITPRICE', $payments['total_unit_price']);

        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('VIEW', $request->get('view'));

        return $viewer->view('RelatedList.tpl', $moduleName,true);
    }
}