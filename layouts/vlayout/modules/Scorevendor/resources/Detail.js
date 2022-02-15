/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Scorevendor_Detail_Js",{
},{
	detailViewSaveSchoolComment: function(){

		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','.detailViewSaveSchoolComment', function(e){
			var schoolid = $('input[name=schoolid]').val();
			var smodcommentpurpose = $('select[name=smodcommentpurpose]').val();
			var smodcommentcontacts = $('select[name=smodcommentcontacts]').val();
			var scommentcontents = $('textarea[name=scommentcontents]').val();
			var smodcommenttype = $('select[name=smodcommenttype]').val();
			var smodcommentmode = $('select[name=smodcommentmode]').val();

			var schoolid = $('input[name=schoolid]').val();

			if (!$.trim(scommentcontents)) {
				var  params = {text : '跟进内容不能为空', title : '错误提示'};
				Vtiger_Helper_Js.showPnotify(params);
				return false;
			}

			//参数设置
			var postData = {
				"module": 'Schoolcomments',
				"action": "SaveAjax",
				"record": jQuery('#recordId').val(),
				"schoolid": schoolid,
				smodcommentmode : smodcommentmode,
				smodcommenttype : smodcommenttype,
				scommentcontents : scommentcontents,
				smodcommentcontacts : smodcommentcontacts,
				smodcommentpurpose : smodcommentpurpose
			}


			// 遮罩层
			var progressIndicatorElement = jQuery.progressIndicator({
						'message' : '正在提交...',
						'position' : 'html',
						'blockInfo' : {'enabled' : true}
						});

			//发送请求
			AppConnector.request(postData).then(
				function(data){
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					});

					if(data.success ==  true){
						var message = app.vtranslate('跟进成功');
						var params = {
							text: message,
							type: 'notice'
						};
						Vtiger_Helper_Js.showMessage(params);
						setTimeout(function() {
							window.location.reload();
						}, 500)
					}
					//刷新页面
					
					return false;
				},
				function(error){
					console.log(error);
				}
			);
			return false;
		});

	},


	score : function () {
		// 生成评分问卷
		$('#Scorevendor_detailView_basicAction_LBL_RELATED_SCOREVENDOR').click(function () {
			//参数设置
			var postData = {
				"module": 'Scorevendor',
				"action": "BasicAjax",
				"record": jQuery('#recordId').val(),
				"mode": 'makeScorepapers',
			}
			// 遮罩层
			var progressIndicatorElement = jQuery.progressIndicator({
						'message' : '正在提交...',
						'position' : 'html',
						'blockInfo' : {'enabled' : true}
						});

			//发送请求
			AppConnector.request(postData).then(
				function(data){
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					});
					if(data.success ==  true){
						if(data.result.flag) {
							self.location='index.php?module=Scorevendor&view=Detail&record='+jQuery('#recordId').val()+'&public=show_paper';
						} else {
							var  params = {text : data.result.msg, title : '错误提示'};
							Vtiger_Helper_Js.showPnotify(params);
						}
					}
				},
				function(error){
					console.log(error);
				}
			);
		});
	},
	
	registerEvents:function(){
		this._super();
		this.score();
	}
});