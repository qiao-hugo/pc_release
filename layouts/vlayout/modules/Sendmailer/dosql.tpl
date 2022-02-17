{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>

        <th colspan="2"><h4>合同延期</h4></th></thead><tbody>

        <tr><td style="text-align: right"><span class="redColor">*</span>部门
            </td><td>
                <textarea id="department" name="department">

                </textarea>
            </td></tr>
        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary" id="preview">执行</button></td></tr>
        </tbody></table>
    </div>


    <script>

        {literal}
        $(function(){
            $('#preview').click(function(){
                var params = {};
                params['department'] = $("#department").val();
                params['action'] = 'SelectAjax';
                params['module'] = "Sendmailer";
                params['mode'] = 'addAuditSql';

                var progressIndicatorElement = jQuery.progressIndicator({
                            'message' : '正在请求',
                            'position' : 'html',
                            'blockInfo' : {'enabled' : true}
                            });

                AppConnector.request(params).then(function(data){
                    if (data.result.flag == '1') {
                        /*progressIndicatorElement.progressIndicator({
                                    'mode' : 'hide'
                                });*/
                        window.location.reload();
                    } else {
                        alert(data.result.msg);
                    }
                });
            });
        });
        {/literal}
    </script>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}