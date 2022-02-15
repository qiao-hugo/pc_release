{strip}
    <table class="table table-bordered equalSplit detailview-table">
        <thead>
        <th colspan="2">合同领用人变更</th>
        </thead>
        <tbody>
        {*<tr><td style="text-align: right">部门
            </td><td>
                <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>*}
        <tr>
            <td style="text-align: right;" class="inputtitle">合同原领取人</td>
            <td>
                <label class="pull-left" style="height:30px;line-height:30px;">
                    <input class="span9 dateField"type="text" name="inputcode" id="inputcode" value="" autocomplete="off" placeholder="合同原领取人工号" style="width:200px;">
                </label>
                <label class="pull-left usernamelabel" style="height:30px;line-height:30px;margin-left:10px;margin-right:10px;font-weight: bold;"><span class="label label-info">扫描工牌背面的条码,获取工号,然后Enter回车进入下一步</span></label>
                <button type="button" class="btn btn-small btn-info" id="reset" style="display: none">结束</button>
            </td>
        </tr>
        <tr>
            <td style="text-align: right;">已变更的合同</td>
            <td>
                <ul class="unstyled" id="contractList">
                </ul>
            </td>
        </tr>
        </tbody>
    </table>
    </div>
    <style rel="stylesheet">
        ul.unstyled li{
            line-height: 26px;
            font-size: 13px;
        }
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
        var type = 'srcuser';
        var srcUserCode='';//原合同领取人工号
        var srcuserid=0;//原合同领取人
        var srcUserSignPath='';//原合同领取人签名
        var dstUserCode='';//新合同领取人工号
        var dstuserid=0;//新领取人
        var dstUserSignPath='';//新合同领取人签名
        function clearinput(){
            $('#inputcode').val('');
        }
        function bodytrigger(){
            $('#inputcode').focus();
        }
        $(function(){
            $('#inputcode').focus();
            $('body').click(function(){
                $('#inputcode').focus();
            });
            $('#reset').on('click',function() {
                type = 'srcuser';
                srcuserid = 0;
                srcUserSignPath = '';
                dstuserid = 0;
                dstUserSignPath = '';
                $('.usernamelabel').html('<span class="label label-info">扫描工牌背面的条码,获取工号,然后Enter回车进入下一步</span>');
                $('.inputtitle').text('合同原领取人');
                $('#inputcode').val('').attr('placeholder','合同原领取人工号').focus();
                $('#contractList').empty();
                $(this).hide();
            });

            $('#inputcode').keydown(function(event) {
                if(event.keyCode==13){
                    var inputcode = $('#inputcode').val();
                    if (inputcode == '') {
                        return false;
                    }
                    if (type == 'srcuser' || type == 'dstuser') {
                        if(type == 'dstuser') {
                            if(inputcode == srcUserCode) {
                                var  params = {text : '',
                                    title :'新领取人不能与原领取人相同'};
                                Vtiger_Helper_Js.showPnotify(params);
                                $('#inputcode').val('');
                                return false;
                            }
                        }
                        var params = [];
                        params['module'] = 'ServiceContracts';
                        params['action']='BasicAjax';
                        params['mode']='changeLeadCheak';
                        params['inputcode']= inputcode;
                        params['type'] = type;
                        $('#inputcode').blur().attr('disabled', true).val('');
                        AppConnector.request(params).then(function(data) {
                            $('#inputcode').focus().attr('disabled', false);
                                if(data.result.rstatus == 'success') {
                                if (type == 'srcuser') {
                                    srcuserid = data.result.id;
                                    srcuserusername = data.result.username;
                                    var par={};
                                    par['stats'] = 'srcuser';
                                    par['userCode'] = inputcode;
                                    par['username'] = srcuserusername;
                                    signature(par);
                                } else if (type == 'dstuser') {
                                    dstuserid = data.result.id;
                                    dstuserusername = data.result.username;
                                    var par={};
                                    par['stats'] = 'dstuser';
                                    par['userCode'] = inputcode;
                                    par['username'] = dstuserusername;
                                    signature(par);
                                }
                            } else {
                                var  params = {text : '',
                                    title :data.result.msg};
                                Vtiger_Helper_Js.showPnotify(params);
                            }
                            $('#inputcode').attr('disabled', false).focus();
                        });
                    } else if(type=='contract') {
                        var params = [];
                        params['module'] = 'ServiceContracts';
                        params['action'] = 'BasicAjax';
                        params['mode'] = 'changeLead';
                        params['contactNo']= inputcode;
                        params['srcuserid']= srcuserid;
                        params['srcUserSignPath']= srcUserSignPath;
                        params['dstuserid']= dstuserid;
                        params['dstUserSignPath']= dstUserSignPath;
                        $('#inputcode').attr('disabled', true).val('').blur();
                        AppConnector.request(params).then(function(data){
                                if(data.result.rstatus == 'success') {
                                    var  params = {text: '',
                                        title: data.result.msg,
                                        type: 'success'
                                    };
                                    $('#contractList').append('<li>' + data.result.contractNo+ '</li>');
                                    Vtiger_Helper_Js.showPnotify(params);
                                } else {
                                    var  params = {text : '',
                                        title :data.result.msg};
                                    Vtiger_Helper_Js.showPnotify(params);
                                }
                            $('#inputcode').attr('disabled', false).focus();
                        });
                    }
                }
            });
            function signature(params) {
                var message='姓名:'+params['username']+'<span color="red">请签名</span>';
                var windowwith=$(window).width();
                var windowheight= windowwith*0.3;
                var msg = {
                    'message':message,
                    "width":windowwith
                };
                params['action'] = 'BasicAjax';
                params['module'] = 'ServiceContracts';
                params['mode'] = 'preSaveChangeLeadSign';
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    params['image'] = $('#canvssign').jSignature("getData", "default").toString();
                    AppConnector.request(params).then(
                        function(data) {
                            isSign = 0;
                            if (params.stats=='srcuser') {
                                type = 'dstuser';
                                srcUserSignPath = data.result.signpath;
                                srcUserCode = params['userCode'];
                                $('.usernamelabel').html('<span class="label label-warning">扫描工牌背面的条码,获取工号,然后Enter回车</span>');
                                $('.inputtitle').text('合同新领取人');
                                $('#inputcode').attr('placeholder','请输入合同新领取人工号').focus();
                            } else if(params.stats=='dstuser') {
                                type = 'contract';
                                dstUserSignPath = data.result.signpath;
                                dstUserCode = params['userCode'];
                                $('.usernamelabel').html('<span class="label label-success">扫描合同条码，获取合同编号，然后Enter进入下一步<span>');
                                $('.inputtitle').text('合同编号');
                                $('#inputcode').attr('placeholder','合同编号或条码').focus();
                                $('#reset').show();
                            }
                        },
                        function(error, err) {
                        }
                    );
                },function(error, err) {
                    setTimeout (function() {
                        $('#inputcode').focus();
                    }, 500)
                });
                $('.modal-content .modal-body').append('<div id="canvssign" ondragstart="return false" oncontextmenu="return false" onselectstart="return false" oncopy="return false" oncut="return false" style="-moz-user-select:none;width:100%;height:'+windowheight+'px;border:1px solid #ccc;margin:10px 0 0;overflow:hidden;"></div>');
                $('.modal-content .modal-body').css({overflow:'hidden'});
                $('#canvssign').jSignature();
                $('<input type="button" value="清空" style="float:left;margin-left:'+(windowwith/2)+'px;">').bind('click', function(e){
                    $('#canvssign').jSignature('reset')
                }).appendTo('.modal-content .modal-footer');
            }

        /*$('#inputcode').keydown(function(event){
            if(event.keyCode==13){
                if($('#inputcode').val()==''){
                    $('#inputcode').val('');
                    return false;
                }
                if($('#inputcode').val()==usercode){
                    var  params = {text : '',
                        title :'工号重复录入!'};
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }
                if(isSign==1){
                    var  params = {text : '',
                        title :'新领取人请先签名!'};
                    Vtiger_Helper_Js.showPnotify(params);
                    srcuserid=0;
                    isSign=0;
                    $('#inputcode').val('');
                    $('.usernamelabel').text('<span class="label label-success">扫描工牌背面的条码,获取工号,然后Enter回车</span>');
                    $('.inputtitle').text('新的合同领取人');
                    $('#inputcode').attr('placeholder','合同新领取人工号');
                    return false;
                }
                if(isSign==2){
                    var  params = {text : '',
                        title :'原合同领取人请先签名!'};

                    Vtiger_Helper_Js.showPnotify(params);
                    dstuserid=0;
                    isSign=0;
                    $('#inputcode').val('');
                    $('.usernamelabel').html('<span class="label label-warning">扫描工牌背面的条码,获取工号,然后Enter回车进入下一步</span>');
                    $('.inputtitle').text('合同现在的领取人');
                    $('#inputcode').attr('placeholder','合同原领取人工号');
                    return false;
                }
                $('#inputcode').blur();
                $('#inputcode').attr('readonly','readOnly');
                var params={};
                var module = app.getModuleName();
                var inputcode=$('input[name="inputcode"]').val();
                params['inputcode']=inputcode;
                params['srcuserid']=srcuserid;
                params['dstuserid']=dstuserid;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='changeLead';
                params['recordid']=recordid;
                $('#inputcode').val('');
                AppConnector.request(params).then(function(data){
                    $('#inputcode').focus();
                    $('#inputcode').removeAttr('readonly');
                    if(data.result!=null) {
                        clearinput();
                        bodytrigger();
                        if(data.result.rstatus=='contOK'){
                            $('.usernamelabel').html('<span class="label label-warning">扫描工牌背面的条码,获取工号,然后Enter回车进入下一步');
                            $('#inputcode').attr('placeholder','合同原领取人工号');
                            $('.inputtitle').text('合同原领取人');

                            recordid=data.result.msg;

                        }else if(data.result.rstatus=='srcuserOK'){
                            srcuserid=data.result.msg;
                            srcuserusername=data.result.username;
                            isSign=1;
                            var par={};
                            par['stats']='srcuser';
                            par['usercode']=inputcode;
                            par['recordid']=recordid;
                            par['srcuserid']=srcuserid;
                            par['username']=srcuserusername;
                            signContract(par);
                        }else if(data.result.rstatus=='dstuserOK'){
                            dstuserid=data.result.msg;
                            dstuserusername=data.result.username;
                            isSign=2;
                            var par={};
                            par['stats']='dstuser';
                            par['recordid']=recordid;
                            par['dstuserid']=dstuserid;
                            par['username']=dstuserusername;

                            signContract(par);
                        }else if(data.result.rstatus=='msgerr'){
                            //超领提示
                            var  params = {text : '',
                                title :data.result.msg};

                            Vtiger_Helper_Js.showPnotify(params);
                        }
                    }
                });
            }else{
                //t1=setTimeout("clearinput()",100);
                //clearTimeout(t1);
            }
        });*/
        /*function signContract(params){
            var message='姓名:'+params['username']+'<span color="red">请签名</span>';
            var windowwith=$(window).width();
            var windowheight=windowwith*0.25;
            var msg={
                'message':message,
                "width":windowwith
            };
            params['action'] = 'BasicAjax';
            params['module'] = 'ServiceContracts';
            params['mode'] = 'saveChangeLeadSign';
            $('#inputcode').blur();
            $('#inputcode').attr('readonly','readOnly');
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                params['image'] = $('#canvssign').jSignature("getData", "default").toString();
                AppConnector.request(params).then(
                    function(data) {
                        $('#inputcode').blur();
                        $('#inputcode').removeAttr('readonly');
                        isSign=0;
                        if(params.stats=='dstuser'){
                            recordid=0;
                            srcuserid=0;//原合同领取人
                            dstuserid=0;//新领取人
                            usercode='';//用户工事情
                            $('.usernamelabel').html('<span class="label label-inverse">扫描合同条码，获取合同编号，然后Enter进入下一步<span>');
                            $('#inputcode').attr('placeholder','合同编号或条码');
                            $('.inputtitle').text('合同编号');
                        }else if(params.stats=='srcuser'){
                            usercode=params.usercode;
                            $('.usernamelabel').html('<span class="label label-success">扫描工牌背面的条码,获取工号,然后Enter回车</span>');
                            $('#inputcode').attr('placeholder','请输入合同新领取人工号');
                            $('.inputtitle').text('合同新领取人');
                        }
                        ///window.location.reload(true);
                    },
                    function(error,err){
                        //window.location.reload(true);
                    }
                );
            },function(error, err) {});
            $('.modal-content .modal-body').append('<div id="canvssign" ondragstart="return false" oncontextmenu="return false" onselectstart="return false" oncopy="return false" oncut="return false" style="-moz-user-select:none;width:100%;height:'+windowheight+'px;border:1px solid #ccc;margin:10px 0 0;overflow:hidden;"></div>');
            $('.modal-content .modal-body').css({overflow:'hidden'});
            $('#canvssign').jSignature();
            $('<input type="button" value="清空" style="float:left;margin-left:'+(windowwith/2)+'px;">').bind('click', function(e){
                $('#canvssign').jSignature('reset')
            }).appendTo('.modal-content .modal-footer');
        }*/

        });

{/literal}
    </script>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
