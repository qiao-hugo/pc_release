<?php
/*+***********
 * 暂时保留修改密码
 *************/

class Users_DetailView_Model extends Vtiger_DetailView_Model {
    
    
    /**
	 * Function to get the detail view links (links and widgets)
	 * @param <array> $linkParams - parameters which will be used to calicaulate the params
	 * @return <array> - array of link models in the format as below
	 *                   array('linktype'=>list of link models);
	 */
	public function getDetailViewLinks($linkParams) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$recordModel = $this->getRecord();
		$recordId = $recordModel->getId();
        global $current_user,$adb;
        $userid= $current_user->id;
        $result=$adb->pquery('SELECT id FROM `vtiger_user2setting` WHERE FIND_IN_SET(?,userid) AND FIND_IN_SET(?,setting)',array($userid,1));
        global $site_URL,$sso_URL;
        if (($currentUserModel->isAdminUser() == true || $currentUserModel->get('id') == $recordId) || $adb->num_rows($result)>0) {
			$recordModel = $this->getRecord();

			$detailViewLinks = array(
				array(
				'linktype' => 'DETAILVIEWBASIC',
				'linklabel' => 'LBL_EDIT',
				'linkurl' => $recordModel->getEditViewUrl(),
				'linkicon' => ''
				),
				array(
					'linktype' => 'DETAILVIEWBASIC',
					'linklabel' => 'LBL_CHANGE_PASSWORD',
					//'linkurl' => "javascript:Users_Detail_Js.triggerChangePassword('index.php?module=Users&view=EditAjax&mode=changePassword&recordId=$recordId','Users')",
                    'linkurl' => "javascript:window.location.href='".$sso_URL."login?backUrl=".urlencode(trim($site_URL,'/').'/index.php')."&changePassword=1'",
                    'linkicon' => ''
				)
			);

			foreach ($detailViewLinks as $detailViewLink) {
				$linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
			}
			

			$detailViewPreferenceLinks = array(
				array(
					'linktype' => 'DETAILVIEWPREFERENCE',
					'linklabel' => 'LBL_CHANGE_PASSWORD',
                    'linkurl' => "javascript:window.location.href='".$sso_URL."login?backUrl=".urlencode(trim($site_URL,'/').'/index.php')."&changePassword=1'",
					//'linkurl' => "javascript:Users_Detail_Js.triggerChangePassword('index.php?module=Users&view=EditAjax&mode=changePassword&recordId=$recordId','Users')",
					'linkicon' => ''
				),
				/*array(
					'linktype' => 'DETAILVIEWPREFERENCE',
					'linklabel' => 'LBL_EDIT',
					'linkurl' => $recordModel->getPreferenceEditViewUrl(),
					'linkicon' => ''
				)
				*/
			);

			foreach ($detailViewPreferenceLinks as $detailViewLink) {
				$linkModelList['DETAILVIEWPREFERENCE'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
			}

			if($currentUserModel->isAdminUser() && $currentUserModel->get('id') != $recordId){
				$detailViewActionLinks = array(
					array(
						'linktype' => 'DETAILVIEW',
						'linklabel' => 'LBL_DELETE',
						'linkurl' => 'javascript:Users_Detail_Js.triggerDeleteUser("' . $recordModel->getDeleteUrl() . '")',
						'linkicon' => ''
					)
				);

				foreach ($detailViewActionLinks as $detailViewLink) {
					$linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
				}
			}
			return $linkModelList;
		}
	}
}