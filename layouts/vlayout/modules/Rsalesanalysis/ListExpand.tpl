{strip}
    <script type="text/javascript" src="/libraries/media/jquery.dataTables.js"></script>
    <script type="text/javascript" src="/libraries/echarts/echarts.js"></script>
<div style="margin-right:20px;margin-top:10px;">
    <div class="row-fluid" id="c" style="width:100%;">
        <div style="border-right:1px #ccc solid;">
            <table class="table">
                <tr>
                    {assign var=arr value=['H4','H5','H7','H8']}
                    <td><label class="pull-right">部门</label></td>
                    <td><div class="pull-left">
                            <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" multiple>
                                {foreach key=index item=value from=$DEPARTMENTUSER}
                                    <option value="{$index}" {if in_array($index,$arr)} selected{/if}>{str_replace(array('|','—'),array('',''),$value)}</option>
                                {/foreach}
                            </select></div></td>
                    <td><label class="pull-right">负责人</label></td>
                    <td><label class="pull-left">
                            <select id="user_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" multiple>
                                {foreach key=index item=value from=$USERDEPARTMENT}
                                    <option value="{$value.id}">{$value.last_name}</option>
                                {/foreach}
                            </select></label></td>
                    <td><label class="pull-right">日期</label></td>
                    <td><div class="pull-left" style="margin-right:20px;">
                            <select id="timeslot" class="chzn-select referenceModulesList streched" style="width:100px;">
                                    {foreach key=index item=valu from=$USERYEARS}
                                    <option value="{$valu.datetimes}">{$valu.datetimes}年</option>
                                    {/foreach}
                                    <option value="14">本周</option>
                                    <option value="13" selected>本月</option>
                                    <option value="15">时间段</option>
                                    <option value="1">一月</option>
                                    <option value="2">二月</option>
                                    <option value="3">三月</option>
                                    <option value="4">四月</option>
                                    <option value="5">五月</option>
                                    <option value="6">六月</option>
                                    <option value="7">七月</option>
                                    <option value="8">八月</option>
                                    <option value="9">九月</option>
                                    <option value="10">十月</option>
                                    <option value="11">十一月</option>
                                    <option value="12">十二月</option>

                            </select>
                        </div>
                    </td>

                </tr>
                <tr>
                    <td colspan="6"><label style="text-align:center"><input type="button" value="提交查询" id="PostQuery" name="PostQuery" class="btn"></label></td>
                </tr>
            </table>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartablea" style="height:400px;"></div></div>

        <div id="listdataa" style="display:none">
            <div id="msga" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtablea" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartableb" style="height:400px;"></div></div>

        <div id="listdatab" style="display:none">
            <div id="msgb" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtableb" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartablec" style="height:400px;"></div></div>

        <div id="listdatac" style="display:none">
            <div id="msgc" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtablec" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartabled" style="height:400px;"></div></div>

        <div id="listdatad" style="display:none">
            <div id="msgd" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtabled" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>

    </div>
</div>
{/strip}
