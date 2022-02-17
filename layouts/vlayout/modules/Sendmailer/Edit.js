/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Sendmailer_Edit_Js", {} ,{
	

	/**
	 * 获取客户信息
	 */
	registerGetAccountsClickEvent:function(){
		var intace=this;
		jQuery('table').on('click','#btnGetAccounts',function(){
            var departmentid=jQuery('select[name="departmentid"]').val();
            var inorout=jQuery('select[name="inorout"]').val();

			var params = {
					'type':'GET',
					'module' : app.getModuleName(),
					'action' : 'SubRule',
					'mode':'getAccountInfos',
					'departmentid':departmentid,
                    'inorout':inorout
			};

			jQuery('.msg').html('<font color=red>正在获取信息，请稍等......</font>');
			//发送请求
            if(inorout=='inner') {
                $('#div_account_detail').html('<table id="tbl_ServiceAssignRule_Account_Detail" class="table listViewEntriesTable" width="100%"">' +
                '<thead><tr><th nowrap><b>姓名</b></th><th nowrap><b>部门</b></th><th nowrap><b>职位</b></th><th nowrap><b>邮箱</b></th></tr></thead><tbody></tbody></table>');
                //$('#pagination').html('<ul class="pagination-demo"></ul>');
            }else if(inorout=='outer'){
                $('#div_account_detail').html('<table id="tbl_ServiceAssignRule_Account_Detail" class="table listViewEntriesTable" width="100%"">' +
                '<thead><tr><th nowrap><b>客户</b></th><th nowrap><b>行业</b></th><th nowrap><b>客户等级</b></th><th nowrap><b>区域</b></th><th nowrap><b>公司所在地</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>邮箱</b></th></tr></thead><tbody></tbody></table>');

            }
            intace.Tableinstance();

		})	
	},
	

	Tableinstance:function(){
		
		var table = jQuery('.listViewEntriesTable').DataTable( {
			language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
				"sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
				"oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
			"scrollY":"300px",
            //"scrollCollapse":true,
            sScrollX:"disabled",
            "bSort": false,
            aLengthMenu: [ 50, 100, 500, 1500 ],
            "processing": true,
            "serverSide": true,
            "ajax": "/index.php?module=Sendmailer&action=SubRule&mode=getAccountInfos&departmentid="+$('select[name="departmentid"]').val()+'&inorout='+$('select[name="inorout"]').val(),
			fnDrawCallback:function(){
				jQuery('.msg').html('<font  color=red>数据加载完成</font>');
			}


		} );
	},
    registerCheckBoxClickEvent : function(){
        jQuery('#listViewEntriesMainCheckBox').live('click',function(event){
            if(jQuery(this).is(':checked')){
                jQuery(".listViewEntriesCheckBox").attr("checked", true);
            }else{
                jQuery(".listViewEntriesCheckBox").attr("checked", false);
            }


        });

    },

    insertButton:function(){
        $('select[name="departmentid"]').parent().append('<button id="btnGetAccounts" type="button" class="btn btn-primary" style="margin-left: 20px;margin-top: -22px;">获取列表</button>');
        $('select[name="departmentid"]').parent().parent().css({whiteSpace:'nowrap'});
        var ue = UE.getEditor('Sendmailer_editView_fieldName_body',{
            toolbars: [['fullscreen', 'source', 'undo', 'redo', 'bold', 'italic', 'underline', 'fontborder',  'inserttable', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc']],
            initialFrameWidth:'100%',
            initialFrameHeight:200,
            autoHeightEnabled: false,
            autoFloatEnabled: true,
            elementPathEnabled:false,
            disabledTableInTable:false,
            wordCount:false,
            catchRemoteImageEnable:true
        });
        ue.loadServerConfig();
        if($('input[name="record"]').val()>0){
            $('#btnGetAccounts').trigger('click');
        }
    },
	
	registerEvents : function() {
		this._super();
		//this.registerServiceChangeEvent();
		//this.registerAssignClickEvent();
		this.registerGetAccountsClickEvent();
		//this.registerAssignTypeChangeEvent();
		//this.registerDepartmentChangeEvent();
		//this.registerRecordPreSaveEvent();
		this.registerCheckBoxClickEvent();
        this.insertButton();
		//客服分配信息取得
		//this.getServiceAssignInfo(jQuery('select[name="serviceid"]').val());
	}
});


