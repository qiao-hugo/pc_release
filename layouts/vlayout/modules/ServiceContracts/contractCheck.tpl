{strip}
    <table class="table table-bordered equalSplit detailview-table"><thead>
        <th colspan="2" style="color: red;">合同审查
        </th><input type="hidden" name="lastname"  value="{$LASTNAME}"/></thead><tbody>
        <tr id="insertcode"></tr>
        <tr><td style="text-align: right;">请输入
            </td><td>
                <label class="pull-left" style="height:30px;line-height:30px;">
                    <input class="span9 dateField"type="text" name="inputcode" id="inputcode" value="" autocomplete="off" placeholder="请输人合同编号" style="width:200px;">
                </label>
                <label class="pull-left usernamelabel" style="color:red;height:30px;line-height:30px;margin-left:10px;font-weight: bold;">请输人合同编号</label>
            </td></tr>
        </tbody></table>
    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="min-height:490px;">
                    <table class="table table-striped contractsno">
                        <thead>
                        <tr>
                            <th  style="color:#999;"  nowrap>合同编号<span class="label label-success sumcontracts"  style="font-size: 20px;margin-left:20px;"></span></th>
                            <th  style="color:#999;"  nowrap><span class="classes">审查人</span></th>
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
    {literal}
    <script>
        $(function() {
            $('#inputcode').focus();
            $('body').click(function () {
                $('#inputcode').focus();
            });
            $('#inputcode').keydown(function (event) {
                if (event.keyCode == 13) {
                    var contract_no=$("input[name='inputcode']").val();
                    $('#inputcode').blur();
                    $('#inputcode').attr('readonly', 'readOnly');
                    $('#inputcode').val('');
                    console.log(contract_no);
                    var param={
                        'module': 'ServiceContracts', //ServiceContracts
                        'action': 'ChangeAjax',
                        'mode': 'checkContractno',
                        'contractno':contract_no
                    }
                    AppConnector.request(param).then(function (data) {
                        if(data.result.success==true){
                            var recorID=parseInt(data.result.data.servicecontractsid);
                            console.log(data.result.data);
                            console.log(recorID);
                            var params = {
                                'module': 'ServiceContracts', //ServiceContracts
                                'action': 'ChangeAjax',
                                'mode': 'serviceconfirm',
                                "recordid": recorID
                            };
                            console.log(data);
                            var contractno=data.result.data.contract_no;
                            var lastname=$("input[name='lastname']").val();
                            AppConnector.request(params).then(
                                function (data) {
                                    if( data.success==true && data.result.flag==true ){
                                        $('<tr><td>' +contractno+ '</td><td><span class="label"  style="margin-left:20px;">' + lastname + '</span>'+'</td></tr>').insertAfter($('.contractsno thead'));
                                        $('#inputcode').removeAttr('readonly');
                                    }else if(data.result.flag==false){
                                        $('#inputcode').removeAttr('readonly');
                                        var  params = {text :data.result.message,
                                            title :'处理结果'};
                                        Vtiger_Helper_Js.showPnotify(params);
                                    }else{
                                        var  params = {text :'异常情况',
                                            title :'处理结果'};
                                        Vtiger_Helper_Js.showPnotify(params);
                                    }
                                    console.log(data);
                                    // 显示已审查的合同
                                }
                            );
                        }else{
                            $('#inputcode').removeAttr('readonly');
                            var  params = {text :data.result.message,
                                title :'处理结果'};
                            Vtiger_Helper_Js.showPnotify(params);
                        }
                    });
                    $('#inputcode').focus();
                } else {
                    $('#inputcode').removeAttr('readonly');
                }
            });
        });
{/literal}
    </script>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
