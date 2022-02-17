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
                            <select id="department_editView_fieldName_dropDown" class="chzn-selectq referenceModulesList streched" multiple>
                                {foreach key=index item=value from=$DEPARTMENTUSER}
                                    <option value="{$index}" {if $index eq 'H1'}selected{/if}>{$value}</option>
                                {/foreach}
                            </select></div></td>
                    <td><label class="pull-right">负责人</label></td>
                    <td><label class="pull-left">
                            <select id="user_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" multiple>
                                {foreach key=index item=value from=$USERDEPARTMENT}
                                    <option value="{$value.id}">{$value.last_name}</option>
                                {/foreach}
                            </select></label></td>
                    <td><label class="pull-right">年份</label></td>
                    <td><div class="pull-left" style="margin-right:20px;">
                            <select id="timeslot" class="chzn-selectq referenceModulesList streched">
                                <{foreach key=index item=value from=$USERYEARS}
                                    <option value="{$value.datetimes}" {if $value.datetimes eq date('Y')}selected{/if}>{$value.datetimes}年</option>
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
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartable" class="span12" style="height:350px;"></div><div class="clearfix"></div></div>
        <div id="listc" style="display:none;">
            <div id="msgc" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:10px;"></div>
            <div id="detailtablec" style="border:1px solid #ccc;margin:0 auto 20px;border-top:none;"></div>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartableavg"  class="span12" style="height:350px;"></div><div class="clearfix"></div></div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartables" class="span12" style="height:350px;"></div><div class="clearfix"></div></div>
        <div id="listr" style="display:none;">
            <div id="msgr" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:10px;"></div>
            <div id="detailtabler" style="border:1px solid #ccc;margin:0 auto 20px;border-top:none;"></div>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartableavgs"  class="span12" style="height:350px;"></div><div class="clearfix"></div></div>
        {*
        <div>
            <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtable" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>
    *}
    </div>
</div>
{/strip}
