Vtiger_Edit_Js("Leads_Edit_Js",{ },{
    duplicateCheckCache:{dump:false},
	registerArea:function(){
		if(jQuery('#areadata').length>0){
			var area=jQuery('#areadata').attr('data');
			if(typeof area!='undefined'&& area.length>1){
				area=area.split('#');
				new PCAS("province","city","area",area[0],area[1],area[2]);
				jQuery('input[name=address]').val(area[3]);
			}else{
				new PCAS("province","city","area");
			}	
		}
	},
    registerRecordPreSaveEvent : function(form) {
        var thisInstance = this;
        if(typeof form == 'undefined') {
            form = this.getForm();
        }
        form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
            var result = thisInstance.duplicateCheckCache['dump'];
            if(!result){
                //e.preventDefault();
            }
        })
    },
    checkdup:function(form){
        thisInstance = this;
        var account_obj = $("input[name='company']");
        accountName = account_obj.val();
        var record = $("input[name='record']").val();//商机id
        var sparams = {
            'module':'Accounts',
            'action':'CheckDuplicate',
            'fromLeads':true,
            'accountname':accountName,
            'leadId':record
        };
        AppConnector.request(sparams).then(
            function (datas) {
                if (datas.success == true) {
                    if(datas.result['isdupli']){
                        $("input[name='company']").val('');
                        thisInstance.duplicateCheckCache['dump'] = false;
                        Vtiger_Helper_Js.showPnotify({text:'该线索客户系统中已存在',title:'客户重复'});
                        return true;
                    }else{
                        thisInstance.duplicateCheckCache['dump'] = true;
                        return false;
                    }
                }else{
                    //其他一些处理暂时先不做
                }
            })
    },
    registerblur:function(){
        var thisInstance = this;
        $('#EditView').on('blur','input[name=company]',function(){
            thisInstance.checkdup(form);
        });
        $('#EditView').on('blur','input[name=mobile]',function(){
            var record = $("input[name='record']").val();//商机id
            var sparams = {
                'module':'Leads',
                'action':'Checkmob',
                'mobile':$(this).val(),
                'leadId':record
            };
            AppConnector.request(sparams).then(
                function (datas) {
                    if (datas.success == true) {
                        if(!datas.result){
                            Vtiger_Helper_Js.showPnotify({text:'系统中存在的号码',title:'手机重复'});
                        }
                    }
        })
    })
    },
    leadsystemChange:function(){
        $("#EditView").on("change","select[name='leadsystem']",function () {
                var sparams = {
                    'module':'Leads',
                    'action':'BasicAjax',
                    'mode':'getDepartmentsByDepth',
                };
                AppConnector.request(sparams).then(
                    function (data) {
                        console.log(data);
                    }
                )
        });
    },
    leadsourceChange:function(){
        $("#EditView").on("change","select[name='leadsource']",function () {
            var thisInstance=this;
            var leadsource = $(this).val();
            var recordid = $("input[name='record']").val();
            if(leadsource=='SCRM'){
                $("select[name='leadstype']").val("payspread");
                $('select[name="leadstype"]').prop("disabled",true);
                $('select[name="leadstype"]').trigger('liszt:updated');

                $("select[name='leadsystem']").prop("disabled",true);
                $('select[name="leadsystem"]').trigger('liszt:updated');
            }else{
                $("select[name='leadstype']").val("");
                $('select[name="leadstype"]').prop("disabled",false);
                $('select[name="leadstype"]').trigger('liszt:updated');

                $("select[name='leadsourcetnum']").val("");
                $('select[name="leadsourcetnum"] option').each(function(){
                    this.style='display:list-item'}
                );
                $('select[name="leadsourcetnum"]').trigger('liszt:updated');
            }
            $("select[name='sourcecategory']").val("");
            $('select[name="sourcecategory"] option').each(function(){
                console.log($(this).val());
                this.style='display:list-item'
            });
            $('select[name="sourcecategory"]').trigger('liszt:updated');
            var sparams = {
                'module':'Leads',
                'action':'BasicAjax',
                'mode':'filterSource',
                'leadsource':leadsource,
                'record':recordid
            };
            AppConnector.request(sparams).then(
                function (datas) {
                    console.log(datas);
                    if (datas.success == true) {
                        $('select[name="leadsourcetnum"] option').each(function(k,v){
                                if($(this).val()==''){
                                    return true;
                                }
                                if(datas.data.filterSource.indexOf($(this).val())==-1){
                                    this.style='display:none'
                                }else{
                                    this.style='display:list-item'
                                }
                            }
                        );
                        $('select[name="leadsourcetnum"]').trigger('liszt:updated');
                        var i=0;
                        var assigned_user_id=[];
                        if(!recordid){
                            // if(leadsource=='SCRM'){
                            //     $('select[name="assigned_user_id"] option').each(function(k,v){
                            //         if($(this).val()==''){
                            //             return true;
                            //         }
                            //         if(datas.data.assignList.indexOf($(this).val())==-1){
                            //             this.style='display:none'
                            //         }else{
                            //             assigned_user_id.push($(this).val());
                            //             this.style='display:list-item'
                            //         }
                            //         i++;
                            //     });
                            //     console.log(assigned_user_id[0]);
                            //     if(assigned_user_id.length>0){
                            //         $('select[name="assigned_user_id"]').val(assigned_user_id[0]);
                            //     }
                            //     $('select[name="assigned_user_id"]').trigger('liszt:updated');
                            // }else{
                            //     $('select[name="assigned_user_id"] option').each(function(k,v){
                            //         this.style='display:list-item'
                            //     });
                            //     $('select[name="assigned_user_id"]').trigger('liszt:updated');
                            // }
                        }
                    }else {
                        $('select[name="leadsourcetnum"] option').each(function(){
                            console.log($(this).val());
                            thisInstance.style='display:none'}
                        );
                        $('select[name="leadsourcetnum"]').trigger('liszt:updated');
                        // if(!recordid) {
                        //     $('select[name="assigned_user_id"] option').each(function () {
                        //             console.log($(this).val());
                        //             thisInstance.style = 'display:list-item'
                        //         }
                        //     );
                        //     $('select[name="assigned_user_id"]').trigger('liszt:updated');
                        // }
                    }
                })
        })
    },

    leadsourcetnumChange:function(){
        $("#EditView").on("change","select[name='leadsourcetnum']",function () {
            var thisInstance=this;
            var leadsource = $("select[name='leadsource']").val();
            var recordid = $("input[name='record']").val();
            var leadsourcetnum = $(this).val();
            $("select[name='sourcecategory']").val("");
            $('select[name="sourcecategory"]').trigger('liszt:updated');

            if(leadsource=='SCRM'){
                var sparams = {
                    'module':'Leads',
                    'action':'BasicAjax',
                    'mode':'filterSourceNum',
                    'leadsourcetnum':leadsourcetnum,
                    'record':recordid
                };
                AppConnector.request(sparams).then(
                    function (datas) {
                        console.log(datas);
                        if (datas.success == true) {
                            $('select[name="sourcecategory"] option').each(function(k,v){
                                    if($(this).val()==''){
                                        return true;
                                    }
                                    if(datas.data.indexOf($(this).val())==-1){
                                        this.style='display:none'
                                    }else{
                                        this.style='display:list-item'
                                    }
                                }
                            );
                            $('select[name="sourcecategory"]').trigger('liszt:updated');
                        }else {
                            $('select[name="sourcecategory"] option').each(function(){
                                console.log($(this).val());
                                thisInstance.style='display:none'}
                            );
                            $('select[name="sourcecategory"]').trigger('liszt:updated');
                        }
                    })
            }else{
                $('select[name="sourcecategory"] option').each(function(){
                    console.log($(this).val());
                    this.style='display:list-item'
                });
                $('select[name="sourcecategory"]').trigger('liszt:updated');
            }
        })
    },
    initData:function(){
        $("select[name='leadbelongsystem']").append(aaaaa);
        $('select[name="leadbelongsystem"]').prop("disabled",true);
        $('select[name="leadbelongsystem"]').trigger('liszt:updated');

    },
    changeSmowner:function(){
        $("#EditView").on("change","select[name='assigned_user_id']",function () {
            var leadsource = $("select[name='leadsource']").val();
            if(leadsource!='SCRM'){
                return;
            }
            var sparams = {
                    'module':'Leads',
                    'action':'BasicAjax',
                    'mode':'getBelongSystem',
                    'userid':$(this).val(),
                };
                AppConnector.request(sparams).then(
                    function (datas) {
                        console.log(datas);
                        if (datas.success == true) {
                            console.log(datas.departmentid);
                            $("select[name='leadbelongsystem']").val(datas.departmentid);
                            $('select[name="leadbelongsystem"]').trigger('liszt:updated');

                        }
                    }
                )
        });
    },
    saveData:function(){
        $(".btn-success").on("click",function (e) {
            var locationprovince = $("#Leads_editView_fieldName_locationprovince").val();
            var record = $("input[name='record']").val();
            if (!locationprovince && ( record=='' || record=='0' || record==undefined)){
                e.preventDefault();
                Vtiger_Helper_Js.showMessage({type: 'error', text: '省不能为空'});
                return;
            };
            $('select[name="leadstype"]').attr("disabled",false);
            $('select[name="leadbelongsystem"]').attr("disabled",false);
        })
    },
	registerBasicEvents : function(container) {
		this._super(container);
        app.registerEventForDatePickerFields=function(){
            $('#Leads_editView_fieldName_mapcreattime').datetimepicker({
                format: "yyyy-mm-dd hh:ii:ss",
                language:  'zh-CN',
                autoclose: true,
                todayBtn: true,
                pickerPosition: "bottom-left",
                showMeridian: 0,
                minuteStep:1
            });
        };
		this.registerArea();
        this.registerRecordPreSaveEvent(container);
        this.registerblur();
        this.leadsourceChange();
        this.leadsourcetnumChange();
        this.leadsystemChange();
        this.initData();
        this.changeSmowner();
        this.saveData();
    }
});