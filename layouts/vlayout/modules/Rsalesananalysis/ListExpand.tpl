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
                    <td>{*<label class="pull-right">负责人</label>*}</td>
                    <td>{*<label class="pull-left">
                            <select id="user_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched">
                                <option value="">请选择一项</option>
                                {foreach key=index item=value from=$USERDEPARTMENT}
                                    <option value="{$value.id}">{$value.last_name}</option>
                                {/foreach}
                            </select></label>*}</td>
                    <td><label class="pull-right">年份</label></td>
                    <td><div class="pull-left" style="margin-right:20px;">
                            <select id="timeslot" class="chzn-select referenceModulesList streched" multiple>
                                <{foreach key=index item=value from=$USERYEARS}
                                    <option value="{$value.datetimes}">{$value.datetimes}年</option>
                                {/foreach}
                            </select>
                        </div>
                    </td>

                </tr>
                <tr>
                    <td colspan="6"><label style="text-align:center"><input type="button" value="提交查询" id="PostQuery" name="PostQuery" class="btn"></label></td>
                </tr>
            </table>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartable" class="span6" style="height:400px;"></div><div id="bartablev"  class="span6" style="height:400px;"></div><div class="clearfix"></div></div></div>
        <div>
            <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtable" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>
    </div>
</div>
{/strip}
