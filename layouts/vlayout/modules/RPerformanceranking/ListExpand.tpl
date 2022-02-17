{strip}
    <script type="text/javascript" src="/libraries/media/jquery.dataTables.js"></script>
    <script type="text/javascript" src="/libraries/echarts/echarts.js"></script>
<div style="margin-right:20px;margin-top:10px;">
    <div class="row-fluid" id="c" style="width:100%;">
        <div style="border-right:1px #ccc solid;">
            <table class="table">
                <tr>
                    <td><label class="pull-right">部门</label></td>
                    <td><div class="pull-left">
                            {assign var=arr value=['H4','H5','H7','H8']}
                            <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" multiple>
                                {foreach key=index item=value from=$DEPARTMENTUSER}
                                    <option value="{$index}"{if in_array($index,$arr)} selected{/if}>{$value}</option>
                                {/foreach}
                            </select></div></td>
                    <td><label class="pull-right">负责人</label></td>
                    <td><label class="pull-left">
                            <select id="user_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" style="width:100px;">
                                <option value="">请选择一项</option>
                                {foreach key=index item=value from=$USERDEPARTMENT}
                                    <option value="{$value.id}">{$value.last_name}</option>
                                {/foreach}
                            </select></label></td>
                    <td><label class="pull-right">日期</label></td>
                    <td><label class="pull-left">
                            <input class="span9 dateField"type="text" id="datatime" value="{date("Y-m-d")}" readonly style="width:100px;">
                        </label>
                        <label class="pull-left" style="margin:5px 10px 0;">
                            到
                        </label>
                        <label class="pull-left">
                            <input class="span9 dateField"  type="text" name="enddatatime" data-date-format="yyyy-mm-dd" id="enddatatime" value="{date("Y-m-d")}" readonly style="width:100px;">
                        </label></td>
                    <td><label class="pull-right">前</label></td>
                    <td><label class="pull-left">
                            <select id="pagenum" class="chzn-select referenceModulesList streched" style="width:60px;">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="ALL">全部</option>
                            </select></label>
                    </td>
                    <td width="10%" align="right">
                        <label style="text-align:right"><input type="button" value="更新有效回款" id="postrefresh" name="postrefresh" class="btn"></label>
                    </td>
                </tr>
                <tr>
                    <td colspan="9"><label style="text-align:center"><input type="button" value="提交查询" id="PostQuery" name="PostQuery" class="btn"></label></td>
                </tr>
            </table>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartable" class="span6" style="height:400px;"></div><div id="bartablev" class="span6" style="height:400px;"></div><div class="clearfix"></div></div>

        <div>
            <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtable" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>

    </div>
</div>
{/strip}
