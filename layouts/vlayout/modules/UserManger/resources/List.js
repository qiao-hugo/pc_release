/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("UserManger_List_Js",{},{
    checkUsers:function(){
        $("body").on('click','.all_user',function () {
            var is_check = $(this).is(':checked');
            $(".separte_user").each(function (k,v) {
                if(is_check){
                    $(v).attr('checked',true);
                }else{
                    $(v).attr('checked',false);
                }
            })
        });

    },
    transferPost:function(){
      $("body").on('click','#batch_transfer',function () {
          var flag = false;
          var flag2 = false;
          $('.separte_user').each(function (e) {
              if($(this).attr('checked')){
                  flag = true;
                  console.log($(this).data('stafftype'));
                  console.log($(this).data('graduatetime'));
                  if(!$(this).data('stafftype') || !$(this).data('graduatetime')){
                      flag2 = true;
                  }
              }
          });
          if(!flag){
              var params = {
                  text: '<h4>请至少选中一个用户</h4>',
                  type: 'notice'
              };
              Vtiger_Helper_Js.showMessage(params);
              return;
          }

          if(flag2){
              var params2 = {
                  text: '<h4>请先填写勾选用户的员工类型和毕业时间</h4>',
                  type: 'notice'
              };
              Vtiger_Helper_Js.showMessage(params2);
              return;
          }

          var params_r = [];
          params_r['action'] = 'ChangeAjax';
          params_r['module'] = 'UserManger';
          params_r['mode'] = 'transfer';
          AppConnector.request(params_r).then(
              function(data) {
                  if(data.result.success){
                      str = '<div id="myModal" class="modal" style="">\n' +
                          '\t<div class="modal-dialog">\n' +
                          '\t\t<div class="modal-content">\n' +
                          '\t\t\t<div class="modal-header">\n' +
                          '\t\t\t\t<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>\n' +
                          '\t\t\t\t<h4 class="modal-title">调岗信息<span style="color: red;font-size: 12px;">(以下字段为目标调动信息，如果无变动，可不填写)</span></h4>\n' +
                          '\t\t\t\t<div style="margin-top: 20px;" id="supervisor">\n';
                      // return;
                      var datas = data.result;
                      $("#supervisor").empty();
                      var flag = false;
                      str += '\n' +
                          '\t\t\t\t</div>\n' +
                          '\t\t\t</div>\n' +
                          '\t\t\t<div class="modal-body" style="max-height:500px;">\n' +
                          '\n' +
                          '\t\t\t\t<div class="confirm tc">\n';
                      var str2 = '';
                      $('.separte_user').each(function (e) {
                          if($(this).attr('checked')){
                              str2 +=                 '<tr style="text-align: center;">\n' +
                                  '                            <td >'+$(this).data('last_name')+'</td>\n' +
                                  '                            <td>'+$(this).data('departmentid')+'</td>\n' +
                                  '                            <td>'+$(this).data('reports_to_id')+'</td>\n' +
                                  '                        </tr>\n' +
                                  '<input type="hidden" class="already_checked" name="checked_id" data-companyid="'+$(this).data('companyid')+'" data-title="'+$(this).data('title')+'" data-roleid="'+$(this).data('roleid')+'"  data-employeelevel="'+$(this).data('employeelevel')+'" data-invoicecompany="'+$(this).data('invoicecompany')+'" data-department="'+$(this).data('department')+'" data-departmentid_reference="'+$(this).data('departmentid_reference')+'" data-stafftype="'+$(this).data('stafftype')+'" data-graduatetime="'+$(this).data('graduatetime')+'"  data-reports_to_id="'+$(this).data('reports_to_id_reference')+'" value="'+$(this).data('id')+'">';
                              flag = true;
                          }
                      });
                      if(!flag){
                          var params = {
                              text: '<h4>请至少选中一个用户</h4>',
                              type: 'notice'
                          };
                          Vtiger_Helper_Js.showMessage(params);
                          return;
                      }

                      //上级
                      var str_reports_to_id = '<select class="chzn-select referenceModulesList streched" name="reports_to_id">\n';
                      str_reports_to_id += '<option value="">请选择一项</option>';
                      $(datas.reports_to_id).each(function (k, v) {
                          str_reports_to_id += '<option value="'+v.id+'">'+v.last_name+'</option>'
                      });
                      str_reports_to_id += '</select>';
                     //所属公司
                      var str_invoicecompany = '<select class="chzn-select referenceModulesList streched" name="invoicecompany">\n';
                      str_invoicecompany += '<option value="">请选择一项</option>';
                      $(datas.invoicecompany).each(function (k, v) {
                          str_invoicecompany += '<option value="'+v.id+'">'+v.cname+'</option>'
                      });
                      str_invoicecompany += '</select>';
                    console.log(data);
                      var str_employeelevel = '<select class="chzn-select eferenceModulesList streched" name="employeelevel">\n';
                      str_employeelevel += '<option value="">请选择一项</option>';
                      $(datas.employeelevel).each(function (k, v) {
                          str_employeelevel += '<option value="'+v.id+'">'+v.employeelevel+'</option>'
                      });
                      str_employeelevel += '</select>';

                      var str_departmentid = '<select class="chzn-select referenceModulesList streched" name="departmentid">\n';
                      str_departmentid += '<option value="">请选择一项</option>';
                      $.each(datas.departmentid,function (k, v) {
                          str_departmentid += '<option value="'+k+'">'+v+'</option>'
                      });
                      str_departmentid += '</select>';

                      var roleid =datas.roleid;
                      var str_roleid = '<select class="chzn-select referenceModulesList streched" name="roleid">\n';
                      str_roleid += '<option value="">请选择一项</option>';
                      $(roleid).each(function (k, v) {
                          str_roleid += '<option value="'+v.roleid+'">'+v.rolename+'</option>'
                      });
                      str_roleid += '</select>';
                      str += '<input type="hidden" name="companyid" value="" />';
                      str +='                    <table class="table table-bordered equalSplit detailview-table" style="border-color:white !important;"><thead>\n' +
                          '                            </thead><tbody>\n' +
                          '                        <tr><td style="text-align: right;border-color: white;"><span class="redColor">*</span>调动类型\n' +
                          '                            </td><td  style="border-color:white;" colspan="2">\n' +
                          '                                <label class="pull-left">\n' +
                          '                                    <select  class="chzn-select referenceModulesList streched" id="transfertype" name="type">\n' +
                          '                                    <option value="">请选择一项</option>\n' +
                          '                                    <option value="barrack">新兵营转出</option>\n' +
                          '                                    <option value="transferin">部门职位调动</option>\n' +
                          '                                    </select>\n' +
                          '                                </label>\n' +
                          '                            </td></tr>\n' +
                          '\n' +
                          '                        <tr><td style="text-align: right;border-color: white;"><span class="redColor">*</span>调动生效日期\n' +
                          '                            </td><td  style="border-color:white;"  colspan="2">\n' +

                          '<div class="row-fluid"><span class="span10">' +
                          '<div class="input-append row-fluid">' +
                          '<div class="span10 row-fluid date form_datetime">' +
                          '<input  type="text" class="dateField" name="effectivetime" data-date-format="yyyy-mm-dd" readonly="" value="" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo=""}>'+
                          '<span class="add-on"><i class="icon-calendar"></i></span></div></div></span></div>'+
                          '                            </td></tr>\n' +
                          '\n' +
                          '                        <tr><td style="text-align: right;border-color: white;"><span class="redColor"></span>所属公司\n' +
                          '                            </td><td  style="border-color:white;"  colspan="2">\n' +
                          '                                <label class="pull-left">\n' +
                          str_invoicecompany +
                          '                                </label>\n' +
                          '                            </td></tr>\n' +
                          '\n' +
                          '                        <tr><td style="text-align: right;border-color: white;"><span class="redColor"></span>直接上级\n' +
                          '                            </td><td  style="border-color:white;"  colspan="2">\n' +
                          '                                <label class="pull-left">\n' +
                          str_reports_to_id +
                          '                                </label>\n' +
                          '                            </td></tr>\n' +
                          '\n' +
                          '                        <tr id="tremployeelevel" style="display: none"><td style="text-align: right;border-color: white;"><span class="redColor"></span>员工级别\n' +
                          '                            </td><td  style="border-color:white;"  colspan="2">\n' +
                          '                                <label class="pull-left">\n' +
                          str_employeelevel +
                          '                                </label>\n' +
                          '                            </td></tr>\n' +
                          '\n' +
                          '                        <tr><td style="text-align: right;border-color: white;"><span class="redColor"></span>部门\n' +
                          '                            </td><td  style="border-color:white;"  colspan="2">\n' +
                          '                                <label class="pull-left">\n' +
                          str_departmentid +
                          '                                </label>\n' +
                          '                            </td></tr>\n' +
                          '\n' +
                          '                        <tr><td style="text-align: right;border-color: white;"><span class="redColor"></span>职位角色\n' +
                          '                            </td><td  style="border-color:white;"  colspan="2">\n' +
                          '                                <label class="pull-left">\n' +
                          str_roleid +
                          '                                </label>\n' +
                          '                            </td></tr>\n' +
                          '\n' +
                          '                        <tr><td style="text-align: right;border-color: white;"><span class="redColor"></span>部门(手写)\n' +
                          '                            </td><td  style="border-color:white;"  colspan="2">\n' +
                          '                                <label class="pull-left">\n' +
                          '                                    <input  type="text"  name="department">\n' +
                          '                                </label>\n' +
                          '                            </td></tr>\n' +
                          '\n' +
                          '                        <tr><td style="text-align: right;border-color: white;"><span class="redColor"></span>职位(手写)\n' +
                          '                            </td><td  style="border-color:white;"  colspan="2">\n' +
                          '                                <label class="pull-left">\n' +
                          '                                    <input  type="text"  name="title">\n' +
                          '                                </label>\n' +
                          '                            </td></tr>\n' +
                          '\n' +
                          '        </tbody></table>';

                      str += '<table class="table table-bordered equalSplit" style="text-align: center">\n' +
                          '                        <thead style="font-size: 14px;font-weight: bold">\n' +
                          '                        <td>姓名</td>\n' +
                          '                        <td>部门</td>\n' +
                          '                        <td>原直属上级</td>\n' +
                          '                        </thead>\n' +
                          '                        <tbody>\n' +
                          str2 +
                          '                        </tbody>\n' +
                          '                    </table>';
                      str +=                        '\n' +
                          '\t\t\t\t</div>\n' +
                          '\t\t\t</div>\n' +
                          '\t\t\t<div class="modal-footer">\n' +
                          '\t\t\t\t<div class=" pull-right cancelLinkContainer"><a class="cancelLink" type="reset" data-dismiss="modal">取消</a></div>\n' +
                          '\t\t\t\t<button class="btn btn-success" id="transferPost" type="submit">确定</button>\n' +
                          '\t\t\t</div>\n' +
                          '\t\t</div>\n' +
                          '\t</div>\n' +
                          '</div>';
                      app.showModalWindow(str);
                      $('.modal-backdrop').css({
                          "opacity":"0.6",
                          "z-index":"0"
                      });
                      return;
                  }
                  var params = {
                      text: data.result,
                      type: 'notice'
                  };
                  Vtiger_Helper_Js.showMessage(params);
              },
              function(error,err){
              }
          );
      })
    },
    multiUpdate:function(){
        $("body").on('click','#batch_adjust_superior',function () {
            var params_r = [];
            params_r['action'] = 'ChangeAjax';
            params_r['module'] = 'UserManger';
            params_r['mode'] = 'getUsers';
            AppConnector.request(params_r).then(
                function(data) {
                    if(data.result.success){
                        str = '<div id="myModal" class="modal" style="">\n' +
                            '\t<div class="modal-dialog">\n' +
                            '\t\t<div class="modal-content">\n' +
                            '\t\t\t<div class="modal-header">\n' +
                            '\t\t\t\t<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>\n' +
                            '\t\t\t\t<h4 class="modal-title">直属上级修改</h4>\n' +
                            '\t\t\t\t<div style="margin-top: 20px;" id="supervisor">\n';

                        var datas = data.result.data;
                        $("#supervisor").empty();
                        str += '<span style="font-size: 14px;">直属上级:</span>\n' +
                            '                        <select id="user_id" class="chzn-select referenceModulesList streched" name="supervisor_id">\n' +
                            '\n';
                        str += '<option value="">请选择一项</option>';
                        $(datas).each(function (k, v) {
                            str += '<option value="'+v.id+'">'+v.last_name+'</option>'
                        });
                        str += '</select>';
                        var flag = false;
                        var flag2 = false;
                        str += '\n' +
                        '\t\t\t\t</div>\n' +
                        '\t\t\t</div>\n' +
                        '\t\t\t<div class="modal-body">\n' +
                        '\n' +
                        '\t\t\t\t<div class="confirm tc">\n';
                        var str2 = '';
                        $('.separte_user').each(function (e) {
                            if($(this).attr('checked')){
                                str2 +=                 '<tr style="text-align: center;">\n' +
                                    '                            <td >'+$(this).data('last_name')+'</td>\n' +
                                    '                            <td>'+$(this).data('departmentid')+'</td>\n' +
                                    '                            <td>'+$(this).data('reports_to_id')+'</td>\n' +
                                    '                        </tr>\n' +
                                    '<input type="hidden" class="already_checked" name="checked_id" value="'+$(this).data('id')+'">';
                                flag = true;
                                if(!$(this).data('stafftype') || !$(this).data('graduatetime')){
                                    flag2 = true;
                                }
                            }
                        });
                        if(!flag){
                            var params = {
                                text: '<h4>请至少选中一个用户</h4>',
                                type: 'notice'
                            };
                            Vtiger_Helper_Js.showMessage(params);
                            return;
                        }

                        if(flag2){
                            var params2 = {
                                text: '<h4>请先填写勾选用户的员工类型和毕业时间</h4>',
                                type: 'notice'
                            };
                            Vtiger_Helper_Js.showMessage(params2);
                            return;
                        }

                         str += '<table class="table table-bordered equalSplit" style="text-align: center">\n' +
                            '                        <thead style="font-size: 14px;font-weight: bold">\n' +
                            '                        <td>姓名</td>\n' +
                            '                        <td>部门</td>\n' +
                            '                        <td>原直属上级</td>\n' +
                            '                        </thead>\n' +
                            '                        <tbody>\n' +
                            str2 +
                            '                        </tbody>\n' +
                            '                    </table>';
                        str +=                        '\n' +
                            '\t\t\t\t</div>\n' +
                            '\t\t\t</div>\n' +
                            '\t\t\t<div class="modal-footer">\n' +
                            '\t\t\t\t<div class=" pull-right cancelLinkContainer"><a class="cancelLink" type="reset" data-dismiss="modal">取消</a></div>\n' +
                            '\t\t\t\t<button class="btn btn-success" id="multiUpdate" type="submit">确定</button>\n' +
                            '\t\t\t</div>\n' +
                            '\t\t</div>\n' +
                            '\t</div>\n' +
                            '</div>';
                        app.showModalWindow(str);
                        $('.modal-backdrop').css({
                            "opacity":"0.6",
                            "z-index":"0"
                        });
                        return;
                    }
                    var params = {
                        text: data.result,
                        type: 'notice'
                    };
                    Vtiger_Helper_Js.showMessage(params);
                },
                function(error,err){
                }
            );

        });

    },
    makeAllCache:function(){
		$('#clearcache').on('click',function(){
            var message='<h4>确定要更新缓存?</h4>';
            var msg={
                'message':message,
                "width":400
            };
            var icon=this;
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['action'] = 'Cacheinfo';
                params['module'] = 'UserManger';
                params['mode'] = 'makeAllCache';
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '缓存更新,请耐心等待,待更新完成后再进行其他操作,该窗口请不要关闭...',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        var params = {
                            text: data.result,
                            type: 'notice'
                        };
                        Vtiger_Helper_Js.showMessage(params);
                    },
                    function(error,err){
                    }
                );
            },function(error, err) {});
		});
	},
    transferSuccess:function(){
      $('body').on('click','#transferPost',function () {
          var error_params = {
              type: 'error'
          };

          var type = $('select[name=type]').val();
          if(!type){
              error_params['text'] = '调动类型不能为空';
              Vtiger_Helper_Js.showMessage(error_params);
              return;
          }

          var effectivetime = $('input[name=effectivetime]').val();
          if(!effectivetime){
              error_params['text'] = '调动生效日期不能为空';
              Vtiger_Helper_Js.showMessage(error_params);
              return;
          }

          var companyid = $("select[name=invoicecompany]").val();
          var invoicecompany = $("select[name=invoicecompany] option:selected").text();
          var reports_to_id = $("select[name=reports_to_id]").val();
          var employeelevel = $('select[name=employeelevel]').val();
          var departmentid = $('select[name=departmentid]').val();
          var roleid = $('select[name=roleid]').val();
          var department = $('input[name=department]').val();
          var title = $('input[name=title]').val();

          if(!companyid && !reports_to_id && !employeelevel && !departmentid &&!roleid && !department && !title){
              error_params['text'] = '请至少设置一项调动信息';
              Vtiger_Helper_Js.showMessage(error_params);
              return;
          }
          var ids = [];
          var is_same = false;
          $('.already_checked').each(function (k,v) {
              ids[k] = $(v).val();
              old_invoicecompany = $(v).data('invoicecompany');
              old_reports_to_id = $(v).data('reports_to_id');
              old_employeelevel = $(v).data('employeelevel');
              old_departmentid = $(v).data('departmentid_reference');
              old_roleid = $(v).data('roleid');
              old_department = $(v).data('department');
              old_title = $(v).data('title');
              old_companyid=$(v).data('companyid');
              if((companyid && old_invoicecompany==invoicecompany) && old_reports_to_id==reports_to_id && old_employeelevel == employeelevel && old_departmentid == departmentid &&
                  old_roleid == roleid && old_department== department && old_title == title){
                  is_same = true;
              }
              console.log(old_invoicecompany);
              console.log(invoicecompany);
              console.log(old_reports_to_id);
              console.log(reports_to_id);
              console.log(old_employeelevel);
              console.log(employeelevel);
              console.log(old_departmentid);
              console.log(departmentid);
              console.log(old_roleid);
              console.log(roleid);
              console.log(old_department);
              console.log(department);
              console.log(old_title);
              console.log(title);
          });

          if(is_same){
              error_params['text'] = '请至少设置一项调动信息2';
              Vtiger_Helper_Js.showMessage(error_params);
              return;
          }

              var message='<h4>请确认转岗信息正确无误,提交后将无法修改?</h4>';
              var msg={
                  'message':message,
                  "width":400
              };
              var icon=this;
              Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                  var params = {
                      'reports_to_id':reports_to_id,
                      'employeelevel':employeelevel,
                      'departmentid':departmentid,
                      'roleid':roleid,
                      'department':department,
                      'title':title,
                      'userids':ids,
                      'type':type,
                      'effectivetime':effectivetime,
                      'old_companyid':old_companyid,
                      'companyid':companyid,
                      'invoicecompany':companyid ? invoicecompany:''
                  }

                  params['action'] = 'ChangeAjax';
                  params['module'] = 'UserManger';
                  params['mode'] = 'doTransfer';
                  AppConnector.request(params).then(
                      function(data) {
                          console.log(data);
                          if(data.success){
                              var params2 = {
                                  text:data.result.msg
                              };
                              alert(data.result.msg);
                              $('.close').trigger('click');
                              window.location.reload();
                          }else{
                              var params2 = {
                                  text:data.error.message
                              };
                              Vtiger_Helper_Js.showMessage(params2);
                          }
                      },
                      function(error,err){
                      }
                  );
              },function(error, err) {});


      })
    },
    submitSuccess:function(){
        $('body').on('click','#multiUpdate',function () {
            var reports_to_id = $("#user_id").val();
            var ids = [];
            $('.already_checked').each(function (k,v) {
                ids[k] = $(v).val();
            });

            var error_params = {
                type: 'error'
            };
            if(!reports_to_id){
                error_params['text'] = '请选择直属上级';
                Vtiger_Helper_Js.showMessage(error_params);
                return;
            }
            if(ids.length<1){
                error_params['text'] = '请选择要更换上级的用户';
                Vtiger_Helper_Js.showMessage(error_params);
                return;
            }

            var params = {
                'reports_to_id':reports_to_id,
                'ids':ids
            };
            params['action'] = 'ChangeAjax';
            params['module'] = 'UserManger';
            params['mode'] = 'multiUpdateSupervisor';
            AppConnector.request(params).then(
                function(data) {
                    if(data.success){
                        var params2 = {
                            text:data.result.msg
                        };
                        Vtiger_Helper_Js.showMessage(params2);
                        window.location.reload();
                    }else{
                        var params2 = {
                            text:data.error.message
                        };
                        Vtiger_Helper_Js.showMessage(params2);
                    }
                },
                function(error,err){
                }
            );

        })
    },
    transfertypechange:function(){
      $("body").on('change','#transfertype',function () {
          transfertypeval = $("#transfertype").val();
          if(transfertypeval =='transferin'){
              $("#tremployeelevel").show();
          }else {
              $("select[name=employeelevel]").val('');
              $("#tremployeelevel").hide();
          }
      })
    },
    registerEvents : function(){
        this._super();
        this.makeAllCache();
        this.multiUpdate();
        this.checkUsers();
        this.submitSuccess();
        this.transferPost();
        this.transfertypechange();
        this.transferSuccess();
    }

});