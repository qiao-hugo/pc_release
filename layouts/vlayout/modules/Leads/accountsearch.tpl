{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">客户搜索</th></tr></thead><tbody>

            <tr><td style="text-align: right;vertical-align: middle;">
                <label class="pull-right" style="margin-top:5px;">
                    <input type="text" name="accountname" id="accountname" />
                </label>
            </td><td style="text-align: left"><button class="btn btn-primary" id="preview">搜索</button></td></tr>
        </tbody></table>
    <div style="margin-top:10px;">
        <div class="row-fluid span12" id="c">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;">
                <div id="bartable1" class="span12" style="height:490px;cursor:pointer;">
                <table id="tbl_Detail" class="table listViewEntriesTable" width="100%;"><thead><tr style="cursor:pointer;">
                        <th nowrap><b>客户名称</b></th>
                        <th nowrap><b>客户等级</b></th>
                        <th nowrap><b>负责人</b></th>
                       </tr>
                        </thead><tbody>

                </tbody></table>
                </div>
                <div class="clearfix"></div></div>
            </div>
        </div>
        <div class="row-fluid span6" id="d" style="display:none;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;overflow:auto;">
                <div id="tab_followup" class="span12" style="height:490px;padding-left:20px;">

                </div>
                <div class="clearfix"></div></div>
            </div>
        </div>
    </div>

    {literal}
    <script>
       $(document).ready(function(){
            $('#preview').click(function(){
                var params={};
                var module = app.getModuleName();
                var accountname=$("#accountname").val();
                if(accountname=='')return false;
                params['accountname']=accountname;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='searchAccount';
                $('#c').removeClass('span6');
                $('#d').hide();
                $('#c').addClass('span12');
                $('#tbl_Detail tbody').empty();
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在处理,请耐心等待哟',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(params).then(function (data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        if(data.success){
                            var tablestr='';
                            $.each(data.result,function(key,value){

                                tablestr+='<tr><td data-id='+value.id+' class="followup">'+value.name+'</td><td data-id='+value.id+' class="followup">'+value.rank+'</td><td><a class="updateRecordButton" data-id='+value.id+'><i title="更改客户负责人" class="icon-pencil alignMiddle"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;'+value.owerid+'</td></tr>';
                            });
                            $('#tbl_Detail tbody').append(tablestr);
                        }
                });
            });

            $('#tbl_Detail').on('click','.followup',function(){
                var params={};
                var module = app.getModuleName();
                var id=$(this).data('id');
                params['id']=id;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='getfollowup';
                $('#tab_followup').empty();
                if($('#d').is(":hidden")){
                    $('#c').removeClass('span12');
                    $('#c').addClass('span6');
                    $('#d').slideDown('slow');
                }
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在处理,请耐心等待哟',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(params).then(function (data){
                    progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        if(data.success){
                            var tablestr='';
                            $.each(data.result,function(key,value){

                                tablestr+='<div class="commentDetails bs-example"><div class="commentDiv"><div class="singleComment"><div class="commentInfoHeader row-fluid"><div class="commentTitle"><div class="row-fluid"><div class="span11 commentorInfo"><div class="inner"><span class="commentorName"><strong>'+value.username+'&nbsp;</strong> </span><span class="pull-right">	<p class="muted">跟进类型 : '+value.modcommenttype+' 跟进方式 : '+value.modcommentmode+' <em>跟进时间</em>&nbsp;<small title="">'+value.addtime+'</small> </p></span><div class="clearfix"></div></div><div class="commentInfoContent"><style>h4{font-size:14px;font-weight:500;font-family: Helvetica Neue, Helvetica, Microsoft Yahei, Hiragino Sans GB, WenQuanYi Micro Hei, sans-serif;}</style><div class="bs-callout bs-callout-info"><h4>跟进目的：'+value.modcommentpurpose+'&nbsp;联系人:<span class="" data-field-type="reference" data-field-name="contact_id">'+value.contactname+'</span></h4>'+value.commentcontent+'</div></div></div></div></div></div></div></div></div>';
                            });
                            $('#tab_followup').append('<div class="commentsBody">'+tablestr+'</div>');
                        }
                });
            });
            $('#tbl_Detail').on('click','.updateRecordButton',function(){
                var params={};
                var id=$(this).data('id');
                var instancethis=this;
                var msg={
                    'message':'<h3> 更改客户负责人</h3>',
                    "width":'260px'
                };
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正在处理,请耐心等待哟',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    var module = app.getModuleName();

                    params['id']=id;
                    params['action']='BasicAjax';
                    params['module']=module;
                    params['userid']=$('#updateuserid').val();
                    params['mode']='getUpdateUser';
                    AppConnector.request(params).then(function (data){
                        progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });
                            $(instancethis).remove();

                    });
                });
                var module = app.getModuleName();
                    var id=$(this).data('id');
                    params['id']=id;
                    params['action']='BasicAjax';
                    params['module']=module;
                    params['mode']='getListUser';
                    AppConnector.request(params).then(function (data){
                        if(data.success==true){
                            var str='';
                            var departmentid='';
                            var endfalg=0;
                            $.each(data.result,function(key,value){
                                if(value.departmentid!=departmentid){
                                    if(endfalg==2){
                                       str+='</optgroup>';
                                        endfalg=0;
                                    }
                                    str+='<optgroup label="'+value.departmentname+'">';
                                    departmentid=value.departmentid;
                                    ++endfalg;
                                }
                                str+='<option value="'+value.id+'">'+value.username+'['+value.departmentname+']</option>'

                            });
                            str+='</optgroup>';
                            $('.modal-content .modal-body').append('<div style="height:260px;"><select class="chzn-select" id="updateuserid">'+str+'</select></div>');
                            $('.chzn-select').chosen();
                        }


                    });
            });

       });
    </script>
    {/literal}
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
