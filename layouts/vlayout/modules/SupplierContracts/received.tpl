{strip}
    <table class="table table-bordered equalSplit detailview-table"><thead>
        <th colspan="2">采购/费用{if $CSELECTED eq 1}合同领取{elseif  $CSELECTED eq 2}合同归还{elseif  $CSELECTED eq 3}合同(未签)归还{/if}</th></thead><tbody>
        {*<tr><td style="text-align: right">部门
            </td><td>
                <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>*}
        <tr id="insertcode"></tr>
        <tr><td style="text-align: right;">请输入
            </td><td>
                <label class="pull-left" style="height:30px;line-height:30px;">
                    <input class="span9 dateField"type="text" name="inputcode" id="inputcode" value="" autocomplete="off" placeholder="请输人工号" style="width:200px;">
                </label>
                <label class="pull-left usernamelabel" style="color:red;height:30px;line-height:30px;margin-left:10px;font-weight: bold;">请输入工号</label>
            </td></tr>
            <tr><td style="text-align: right">&nbsp;
            </td><td>
                    {if $CSELECTED eq 1}
                        <label class="pull-left" style="display:none;">
                            <input  type="radio" name="cselected" value="1" checked>
                        </label>
                        <span class="pull-left" style="font-size: 20px;color:red;">&nbsp;&nbsp;采购/费用合同领取&nbsp;&nbsp;&nbsp;</span>
                    {elseif $CSELECTED eq 2}
                        <label class="pull-left"  style="display:none;">
                            <input type="radio" name="cselected" value="2" checked>
                        </label>
                        <span class="pull-left" style="font-size: 20px;color:red;">&nbsp;&nbsp;采购/费用合同归还&nbsp;&nbsp;&nbsp;</span>
                    {elseif $CSELECTED eq 3}
                        <label class="pull-left" style="display:none;">
                            <input type="radio" name="cselected" value="3" checked>
                        </label>
                        <span class="pull-left" style="font-size: 20px;color:red;">&nbsp;&nbsp;采购/费用合同(未签)归还(请走作废)&nbsp;&nbsp;&nbsp;</span>
                    {elseif $CSELECTED eq 4}
                        <label class="pull-left" style="display:none;">
                            <input type="radio" name="cselected" value="4" checked>
                        </label>
                        <span class="pull-left" style="font-size: 20px;color:red;">&nbsp;&nbsp;采购/费用合同(未签)作废&nbsp;&nbsp;&nbsp;</span>
                    {/if}
            </td></tr>
        </tbody></table>
    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="min-height:490px;">
                    <table class="table table-striped contractsno">
                        <thead>
                        <tr>
                            <th  style="color:#999;"  nowrap>采购/费用合同编号<span class="label label-success sumcontracts"  style="font-size: 20px;margin-left:20px;"></span></th>
                            <th  style="color:#999;"  nowrap><span class="classes">领取人</span></th>
                        </tr>
                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div></div>
            </div>
        </div>
    </div>
    <style rel="stylesheet">
        input:-webkit-input-placeholder, textarea::-webkit-input-placeholder {
        　　color: #ff0000;
        }
        　　input:-moz-placeholder, textarea:-moz-placeholder {
        　　color:#ff0000;
        　　}
        　　input::-moz-placeholder, textarea::-moz-placeholder {
        　　color:#ff0000;
        　　}
        　　input:-ms-input-placeholder, textarea:-ms-input-placeholder {
        　　color:#ff0000;
        　　}
    </style>
    <script src="/libraries/jSignature/jSignature.min.noconflict.js"></script>
    {literal}
    <script>
        var userid=0;
        var usercode='';
        var usernamelabel='';
        var sumcontracts=0;
        var sumusernum=0;
        var colorflag=0;
        var colorchange=0;
        var signpath='';
        var userqrcodeid=0;
        var superconllar=0;
        var supermanid=0;
        var supermanflag=0;
        var supermanname='';
        var supermancode='';
        var isSign=1;//签名是否
        var hander=null;
        var looper=0;
        function clearinput(){
            $('#inputcode').val('');
        }
        function setsuperconllar(){
            superconllar=1;
            $('#inputcode').attr('placeholder','请输入超领审核人的工号');
            $('#inputcode').focus();
        }
        function bodytrigger(){
            $('#inputcode').focus();
        }

        $(function(){
            $('#inputcode').focus();
            $('body').click(function(){
                $('#inputcode').focus();
            });
            //$('input[name="cselected"]').click(function(){
                var clabel=$('input[name="cselected"]').val()==1?'领取人':($('input[name="cselected"]').val()==2?'归还人':($('input[name="cselected"]').val()==3?'(未签)归还':'(未签)作废'));
                /*if($(this).val()!=1){
                    supermanflag=0;
                    $('.usernamelabel').text(usernamelabel);
                }*/
                userid=0;
                usercode='';
                usernamelabel='';
                superconllar=0;
                supermanid=0;
                supermanflag=0;
                supermanname='';
                userqrcodeid=0
                signpath='';
                supermancode='';
                isSign=1;
                looper=0

                /*if($(this).val()==3 || $(this).val()==4){
                    $('.usernamelabel').text('请输入合同编号或条码');
                    $('#inputcode').attr('placeholder','请输入合同编号或条码');
                }else{*/
                    $('.usernamelabel').text('请输入工号');
                    $('#inputcode').attr('placeholder','请输入工号');
                //}

                $('#inputcode').focus();
                $('.classes').text(clabel);
            //});
            $('#inputcode').keydown(function(event){
                if(event.keyCode==13){
                    if($('#inputcode').val()==''){
                        $('#inputcode').val('');
                        return false;
                    }
                    if($('#inputcode').val()==usercode){
                        $('#inputcode').val('');
                        return false;
                    }
                    if($('#inputcode').val()==supermancode && superconllar==1){
                        $('#inputcode').val('');
                        return false;
                    }
                    if(isSign==0){
                        var  params = {text : '',
                            title :'请先签名!'};

                        Vtiger_Helper_Js.showPnotify(params);
                        userid=0;
                        userqrcodeid=0;
                        looper=0
                        isSign=1;
                        $('#inputcode').val('');
                        $('.usernamelabel').text('请输入工号');
                        $('#inputcode').attr('placeholder','请输入工号');
                        return false;
                    }
                    $('#inputcode').blur();
                    $('#inputcode').attr('readonly','readOnly');
                    var params={};
                    var module = app.getModuleName();
                    var inputcode=$('input[name="inputcode"]').val();
                    var mode=$('input[name="cselected"]:checked').val()==1?'Received':($('input[name="cselected"]:checked').val()==2?'Returned':($('input[name="cselected"]:checked').val()==3?'NoSignReturned':'NotSignInvalid'));
                    params['inputcode']=inputcode;
                    params['userid']=userid;
                    params['userqrcodeid']=userqrcodeid;
                    params['action']='BasicAjax';
                    params['module']=module;
                    params['mode']=mode;
                    params['superconllar']=superconllar;
                    params['signpath']=signpath;
                    params['supermanid']=supermanid;
                    /*console.log(mode);
                    return false;*/
                    $('#inputcode').val('');
                    AppConnector.request(params).then(function(data){
                        $('#inputcode').focus();
                        $('#inputcode').removeAttr('readonly');
                        if(data.result!=null) {
                            if(data.result.rstatus=='userset'){
                                /**领取人处理**/
                                if(colorflag==0&&userid!=data.result.userid){
                                    colorflag=1;
                                    colorchange++;
                                    if(colorchange>3){
                                        colorchange=0;
                                    }
                                    sumusernum=0;
                                }
                                $('#inputcode').attr('placeholder','请输入合同编号');
                                userid=data.result.userid;
                                usercode=data.result.ucode;
                                usernamelabel=data.result.username;
                                $('.usernamelabel').text(data.result.username);
                                isSign=0//重置状态
                                var par={};
                                par['stats']=mode;
                                signContract(par);
                            }else if(data.result.rstatus=='no_status'){
                               alert(data.result.msg);
                            }else if(data.result.rstatus=='contractok'){
                                /***合同处理***/
                                sumusernum++;
                                var usercolor=colorchange==0?'success':(colorchange==1?'inverse':(colorchange==2?'info':'warning'));
                                var usrmsg=mode=='Received'?'领取':(mode=='Returned'?'归还':(mode=='NoSignReturned'?'(未签)归还':'(未签)作废'));
                                var usrmsgcolor=mode=='Received'?'success':'warning';
                                var supermanflagl=supermanflag==0?'':'<span class="label label-warning"  style="margin-left:20px;">超领审核人: '+supermanname+'</span>';
                                var contractnoStr=mode=='Returned'?'<a href="/index.php?module=SupplierContracts&view=Detail&record='+data.result.contractid+'" target="_blank">'+data.result.contractno+'</a>':data.result.contractno;
                                $('<tr><td>' +contractnoStr+'</td><td><span class="label label-'+usercolor+'"  style="margin-left:20px;">'+usernamelabel+'</span><span class="label label-'+usrmsgcolor+'"  style="margin-left:20px;">'+usrmsg+'</span><span class="label label-'+usercolor+'"  style="font-size: 18px;margin-left:20px;">'+sumusernum+'</span>'+supermanflagl+'</td></tr>').insertAfter($('.contractsno thead'));
                                sumcontracts++;
                                colorflag=0;
                                $('.sumcontracts').text(sumcontracts);
                            }else if(data.result.rstatus=='super_status'){
                                //超领提示
                                var message='<span class="label label-warning">'+usernamelabel+'</span>有<span class="label label-important" style="font-size: 18px;margin:0 20px;">'+data.result.msg+'</span>份合同,未归还,不允许再领取？';
                                var msg={
                                    'message':message
                                };
                                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                                    /***确认超领***/
                                    /*setsuperconllar();//取消超领*/
                                    setTimeout(bodytrigger,100);
                                },function(error, err) {}).fail(function(){setTimeout(bodytrigger,100);/***取消超领***/});
                            }else if(data.result.rstatus=='do_ok'){
                                var  params = {text :  data.result.msg,
                                                title :'处理完成'};

                                Vtiger_Helper_Js.showPnotify(params);
                            }
                        }
                    });
                }else{
                    //t1=setTimeout("clearinput()",100);
                    //clearTimeout(t1);
                }
            });
            function signContract(params){
                var message='请签写您的<font color="red">姓名</font>';
                var windowwith=$(window).width();
                var windowheight=windowwith*0.25;
                var msg={
                    'message':message,
                    "width":windowwith
                };
                params['action'] = 'BasicAjax';
                params['module'] = 'SupplierContracts';
                params['mode'] = 'savesignimage';

                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                    params['image'] = $('#canvssign').jSignature("getData", "default").toString();
                    AppConnector.request(params).then(
                        function(data) {
                            isSign=1;
                            signpath=data.result;
                            {/literal}
                            $('#insertcode').html('<td><label  class="pull-right">' +
                                '<img id="qrcodeid" src="/index.php?module=SupplierContracts&view=ListAjax&mode=getQrcode&status={if $CSELECTED eq 1}Received{elseif  $CSELECTED eq 2}Returned{elseif  $CSELECTED eq 3}NoSignReturned{/if}" data-url="/index.php?module=SupplierContracts&view=ListAjax&mode=getQrcode&status={if $CSELECTED eq 1}Received{elseif  $CSELECTED eq 2}Returned{elseif  $CSELECTED eq 3}NoSignReturned{/if}"></label>' +
                            '</td><td id="useridinfo"><button id="getUserCode">换一张</button></td>');
                            {literal}

                            hander=setInterval("getStatus()",3000);
                        },
                        function(error,err){
                        }
                    );
                },function(error, err) {});
                $('.modal-content .modal-body').append('<div id="canvssign" ondragstart="return false" oncontextmenu="return false" onselectstart="return false" oncopy="return false" oncut="return false" style="-moz-user-select:none;width:100%;height:'+windowheight+'px;border:1px solid #ccc;margin:10px 0 0;overflow:hidden;"></div>');
                $('.modal-content .modal-body').css({overflow:'hidden'});
                $('#canvssign').jSignature();
                $('<input type="button" value="清空" style="float:left;margin-left:'+(windowwith/2)+'px;">').bind('click', function(e){
                    $('#canvssign').jSignature('reset')
                }).appendTo('.modal-content .modal-footer');
            }
            $("#page").on('click','#getUserCode',function(){
                var srcs=$("#qrcodeid").data("url");
                $("#qrcodeid").attr("src",srcs+'&img='+Math.random());
            });
        });
        function getStatus(params){
            if(looper>=120){
                clearInterval(hander);
            }
            ++looper;
            var params={};
            params.data = {
                "module": "SupplierContracts",
                "view": "ListAjax",
                "mode": "getLoginStatus",{/literal}
                "status":"{if $CSELECTED eq 1}Received{elseif  $CSELECTED eq 2}Returned{elseif  $CSELECTED eq 3}NoSignReturned{/if}"{literal}
            };
            params.async=false;
            AppConnector.request(params).then(
                function(data){
                    var objdata=JSON.parse(data);
                    if (objdata.success) {
                        if (objdata.status == 2) {
                            userqrcodeid=objdata.userid;
                            $('#useridinfo').html('<h4 style="color:red;">'+objdata.username+"</h4>");
                            looper=130;
                            clearInterval(hander);
                        }
                    }
                });

        }
{/literal}
    </script>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
