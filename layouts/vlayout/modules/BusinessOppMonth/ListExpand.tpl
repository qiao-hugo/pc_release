{strip}
    <script type="text/javascript" src="/libraries/media/jquery.dataTables.js"></script>
    <script type="text/javascript" src="/libraries/echarts/echarts.js"></script>
<div style="margin-right:20px;margin-top:10px;">
    <div class="row-fluid" id="c" style="width:100%;">
        <div style="border-right:1px #ccc solid;">
            <table class="table">
                <tr>
                    <td><label class="pull-right">部门</label></td>
                    <td><label class="pull-left">
                            <select id="system_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched">
                                <option value="">请选择一项</option>
                                {foreach key=index item=value from=$USERDEPARTMENT}
                                    <option value="{$value.leadsystem}">{vtranslate($value.leadsystem,'BusinessOppMonth')}</option>
                                {/foreach}
                            </select></label></td>
                   <td class="span5"><label class="pull-right">日期</label></td>
                    <td  class="span7"><div class="pull-left" style="margin-right:20px;">
                            <select id="timeslot" class="chzn-select referenceModulesList streched" style="width:100px;">
                                {foreach key=index item=valu from=$USERYEARS}
                                    <option value="{$valu.datetimes}" {if $valu.datetimes eq date('Y')}selected{/if}>{$valu.datetimes}年</option>
                                {/foreach}
                            </select>
                        </div></td>
                    <td width="10%" align="right">
                        <label style="text-align:right"><input type="button" value="更新" id="postrefresh" name="postrefresh" class="btn"></label>
                    </td>

                </tr>
                <tr>
                    <td colspan="5"><label style="text-align:center"><input type="button" value="提交查询" id="PostQuery" name="PostQuery" class="btn"></label></td>
                </tr>
            </table>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartablea" class="span6" style="height:420px;"></div><div id="bartable" class="span6" style="height:420px;"></div><div class="clearfix"></div></div>
        {*<div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;">
            <div id="bartable" class="span12" style="height:500px;"></div>
            <div class="clearfix"></div></div>
        </div>*}
        <div>
            <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtable" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>

    </div>
</div>
{/strip}
