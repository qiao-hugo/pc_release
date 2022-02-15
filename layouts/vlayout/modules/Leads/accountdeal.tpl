{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">成交客户列表</th></tr></thead><tbody>

            <tr><td><label class="pull-right">&nbsp;</label></td>
                    <td>
                <label class="pull-left">
                    <input  type="radio" name="subtimetype" value="signtime" checked>签订日期
                </label>
                <span class="pull-left">&nbsp;</span>
                <label class="pull-left">
                    &nbsp;&nbsp;<input type="radio" name="subtimetype" value="returntime">归还日期
                </label>
            </td>
                    </tr>
                    <tr><td><label class="pull-right">日期</label></td>
                    <td><label class="pull-left">
                            <input class="span9 dateField"type="text" id="datatime" value="{date("Y-m")}-01" readonly style="width:100px;">
                        </label>
                        <label class="pull-left" style="margin:5px 10px 0;">
                            到
                        </label>
                        <label class="pull-left">
                            <input class="span9 dateField"  type="text" name="enddatatime" data-date-format="yyyy-mm-dd" id="enddatatime" value="{date("Y-m-d")}" readonly style="width:100px;">
                        </label></td>
                    </tr>
                    <tr><td style="text-align: center" colspan="2"><button class="btn btn-primary" id="preview">查看</button></td></tr>
        </tbody></table>
    <div style="margin-top:10px;">
        <div class="row-fluid span12" id="c">
        <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;">
                <div id="bartable1" class="span12" style="height:490px;cursor:pointer;">

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
                var datetype=$("input[name='subtimetype'][type='radio']:checked").val();
                var datatime=$('#datatime').val();
                var enddatetime=$('#enddatatime').val();
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='searchDeal';
                params['datetype']=datetype;
                params['datatime']=datatime;
                params['enddatetime']=enddatetime;
                 console.log(params);
                //return ;
                /*$('#c').removeClass('span6');
                $('#d').hide();
                $('#c').addClass('span12');*/
                $('#bartable1').empty();
                var fieldtime=datetype=='signtime'?'签订日期':'归还日期';
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
                            var tablestr='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%;"><thead><tr style="cursor:pointer;"><th nowrap><b>客户名称</b></th><th nowrap><b>客户负责人</b></th><th nowrap><b>合同所属部门</b></th><th nowrap><b>合同编号</b></th><th nowrap><b>'+fieldtime+'</b></th></tr></thead><tbody>';
                            $.each(data.result,function(key,value){

                                tablestr+='<tr><td>'+value.accountname+'</td><td>'+value.username+'</td><td>'+value.username+'</td><td>'+value.contract_no+'</td><td>'+value.datetime+'</td></tr>';
                            });
                            tablestr+='</tbody></table>';
                            $('#bartable1').append(tablestr);
                            Tableinstance();
                        }
                });
            });
        function Tableinstance(){
        var table = jQuery('#tbl_Detail').DataTable({
            language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
            scrollY:"400px",
            sScrollX:"disabled",
            //paging: false,
            //searching: false,
            aLengthMenu: [ 10, 20, 50, 100, ],
            fnDrawCallback:function(){
                jQuery('#msg').html('<span style="font-size:12px;color:green;text-align:left;">超过2000条不显示</span>');
            }
        });
        }

        $('#datatime').datetimepicker({
            format: "yyyy-mm-dd",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:2,
            forceParse:0
        });
        $('#enddatatime').datetimepicker({
            format: "yyyy-mm-dd",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:2,
            forceParse:0
        });
       });
    </script>
    {/literal}
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
