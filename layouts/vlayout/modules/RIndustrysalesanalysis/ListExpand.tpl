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
                            <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched">
                                {foreach key=index item=value from=$DEPARTMENTUSER}
                                    <option value="{$index}">{$value}</option>
                                {/foreach}
                            </select></div></td>
                    <td><label class="pull-right">负责人</label></td>
                    <td><label class="pull-left">
                            <select id="user_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched"  style="width:100px;">
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
                            </select></label></td>
                </tr>
                <tr>
                    <td colspan="8"><label style="text-align:center"><input type="button" value="提交查询" id="PostQuery" name="PostQuery" class="btn"></label></td>
                </tr>
            </table>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartable" class="span4" style="height:380px;min-width:360px;"></div><div id="bartablev" class="span4" style="height:380px;min-width:360px;"></div><div id="bartablem" class="span4" style="height:380px;min-width:360px;"></div><div class="clearfix"></div></div>
        {*
        <div>
            <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtable" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>
    *}
    </div>
</div>
{/strip}
