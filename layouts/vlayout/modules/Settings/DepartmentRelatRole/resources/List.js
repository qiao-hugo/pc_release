Settings_Vtiger_List_Js("Settings_DepartmentRelatRole_List_Js",{
},{
    getDefaultParams:function(){
        var pageNumber=jQuery('#pageNumber').val();
        var module=app.getModuleName();
        var parent=app.getParentModuleName();
        var cvId=this.getCurrentCvId();
        var orderBy=jQuery('#orderBy').val();
        var sortOrder=jQuery("#sortOrder").val();
        var DepartFilter=jQuery('#DepartFilter').val();
        var orderBy=jQuery('#orderBy').val();
        var sortOrder=jQuery("#sortOrder").val();
        var searchvalue=$('#searchvalue').val();
        var params={
            'module':module,
            'parent':parent,
            'page':pageNumber,
            'view':"List",
            "search_value":searchvalue,
            "search_key":$('#searchtype').val(),
            'viewname':cvId,
            'orderby':orderBy,
            'sortorder':sortOrder
        }
        return params;
    },
    addData:function(){
        $('.addButton').click(function(){
            var msg={width:'400px',
                'message':"创建部门职位角色关联!",
                action:function(){
                    var department=$('#selectdepartment').val();
                    if($('#selectdepartment option:selected')==0 || $('#selectrole option:selected').length==0){
                        Vtiger_Helper_Js.showPnotify('请选择相关的部门角色!');
                        return false;
                    }
                    var params={};
                    params.data={
                        'module' : app.getModuleName(),
                        'parent' : app.getParentModuleName(),
                        'action' : 'EditAjax',
                        'mode'   : 'checkDuplicate',
                        'department' : department,

                    };
                    params.async=false;
                    var flag=false;
                    AppConnector.request(params).then(
                        function(data){
                            flag=data.result.success
                            location.reload();
                        },
                        function(error){

                        }
                    )
                    if(flag){
                        Vtiger_Helper_Js.showPnotify('部门已存在不允许添加!');
                        return false;
                    }
                    return true;
                }
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var department=$('#selectdepartment').val();
                var role=$('#selectrole').val();
                var remark=$('#remark').val();
                var params={
                    'module' : app.getModuleName(),
                    'parent' : app.getParentModuleName(),
                    'action' : 'EditAjax',
                    'mode'   : 'dataChange',
                    'role' : role,
                    'remark' : remark,
                    'department' : department
                };

                AppConnector.request(params).then(
                    function(data){
                        location.reload();
                    },
                    function(error){

                    }
                )
            });
            var role=$('#role').val();
            var department=$('#department').val()
            var rolestr=''
            var departmentstr=''
            role=JSON.parse(role);
            department=JSON.parse(department);
            $.each(role,function(key,value){
                rolestr+='<option value="'+key+'">'+value+'</option>';
            });
            $.each(department,function(key,value){
                departmentstr+='<option value="'+key+'">'+value+'</option>';
            });
            $('.modal-body').append('<div style="height:300px;overflow-x:auto;"><table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium" colspan="2"><label class="muted pull-right marginRight10px">部门:</label></td><td class="fieldValue medium" colspan="2"><div class="row-fluid"><span class="span6"><select id="selectdepartment" name="department">'+departmentstr+'</select></span></div></td></tr><tr><td class="fieldLabel medium" colspan="2"><label class="muted pull-right marginRight10px">角色:</label></td><td class="fieldValue medium" colspan="2"><div class="row-fluid"><span class="span6"><select id="selectrole" name="role" multiple>'+rolestr+'</select></span></div></td></tr><tr><td class="fieldLabel medium" colspan="2"><label class="muted pull-right marginRight10px">备注:</label></td><td class="fieldValue medium" colspan="2"><div class="row-fluid"><span class="span6"><textarea id="remark" name="remark"></textarea></span></div></td></tr></tbody></table></div>').css({height:'300px',overflow:'hidden'});
            $('#selectrole').chosen();
            $('#selectdepartment').chosen();
        });
        $('body').on('click','.LBL_EDIT_RECORD',function(){
            var thisInstance=this;
            var msg={width:'400px',
                'message':"修改部门职位角色关联!",
                action:function(){
                    var department=$('#selectdepartment').val();
                    if( $('#selectrole option:selected').length==0){
                        Vtiger_Helper_Js.showPnotify('请选择相关的角色!');
                        return false;
                    }
                    return true;
                }
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var record=$(thisInstance).closest('tr').data('id');
                var role=$('#selectrole').val();
                var remark=$('#remark').val();
                var params={
                    'module' : app.getModuleName(),
                    'parent' : app.getParentModuleName(),
                    'action' : 'EditAjax',
                    'mode'   : 'dataChange',
                    'role' : role,
                    'remark' : remark,
                    'record' : record
                };

                AppConnector.request(params).then(
                    function(data){
                        location.reload();
                    },
                    function(error){

                    }
                )
            });
            var role=$('#role').val();
            var rolestr=''
            role=JSON.parse(role);
            var tableTR=$(thisInstance).closest('tr');
            var remark=tableTR.data('remark');
            var roleid=tableTR.data('roleid');
            var current_department=tableTR.find('.departmentname').text();
            roleid=roleid.split(',');
            $.each(roleid,function(key,value){
                $('#selectrole').val(value);
            });
            $.each(role,function(key,value){
                var selectedstr=$.inArray(key,roleid)>-1?' selected':'';
                rolestr+='<option value="'+key+'" '+selectedstr+'>'+value+'</option>';
            });
            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium" colspan="2"><label class="muted pull-right marginRight10px">部门:</label></td><td class="fieldValue medium" colspan="2"><div class="row-fluid"><span class="span12">'+current_department+'</span></div></td></tr><tr><td class="fieldLabel medium" colspan="2"><label class="muted pull-right marginRight10px">角色:</label></td><td class="fieldValue medium" colspan="2"><div class="row-fluid"><span class="span6"><select id="selectrole" name="role" multiple>'+rolestr+'</select></span></div></td></tr><tr><td class="fieldLabel medium" colspan="2"><label class="muted pull-right marginRight10px">备注:</label></td><td class="fieldValue medium" colspan="2"><div class="row-fluid"><span class="span6"><textarea id="remark" name="remark">'+remark+'</textarea></span></div></td></tr></tbody></table>').css({height:'300px',overflow:'hidden'});

            $('#selectrole').chosen();
            $('#selectdepartment').chosen();
        });
        $('body').on('click','.LBL_DELETE_RECORD',function(){
            var thisInstance=this;
            var msg={width:'400px',
                'message':"删除部门职位角色关联!",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var record=$(thisInstance).closest('tr').data('id');
                var params={
                    'module' : app.getModuleName(),
                    'parent' : app.getParentModuleName(),
                    'action' : 'EditAjax',
                    'mode'   : 'deletedData',
                    'record' : record
                };

                AppConnector.request(params).then(
                    function(data){
                        location.reload();
                    },
                    function(error){

                    }
                )
            });

        });
    },
    registerEvents:function(){
        this._super();
        this.addData()
        this.registerPageNavigationEventsK();
    }
});