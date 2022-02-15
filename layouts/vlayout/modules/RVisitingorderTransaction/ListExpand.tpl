{strip}
    <script type="text/javascript" src="/libraries/media/jquery.dataTables.js" xmlns="http://www.w3.org/1999/html"></script>
    <script type="text/javascript" src="/libraries/echarts/echarts.js"></script>
<div style="margin-right:20px;margin-top:10px;">
    <div class="row-fluid" id="c" style="width:100%;">
        <div style="border-right:1px #ccc solid;">
            <table class="table">
                <tr>
                    <form method="POST" action="index.php">
                    <td><label class="pull-right">部门</label></td>
                    <td><div class="pull-left">
                            {assign var=arr value=['H4','H5','H7','H8','H141','H159','H201','H202','H203','H215','H216','H217','H125','H133','H156','H160','H175','H22']}
                            <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="department[]" multiple>
                                {foreach key=index item=value from=$DEPARTMENTUSER}
                                    <option value="{$index}"{if in_array($index,$arr)} selected{/if}>{$value}</option>
                                {/foreach}
                            </select></div></td>
                    <td><label class="pull-right">日期</label></td>
                    <td><label class="pull-left">
                            <input class="span9 dateField"type="text" id="datatime" name="datetime" value="{date("Y-m")}-01" readonly style="width:100px;">
                        </label>
                        <label class="pull-left" style="margin:5px 10px 0;">
                            到<input type="hidden" name="module" value="RVisitingorderTransaction">
                            <input type="hidden" name="mode" value="getvisitexp">
                            <input type="hidden" name="action" value="selectAjax">
                        </label>
                        <label class="pull-left">
                            <input class="span9 dateField"  type="text" name="enddatatime" data-date-format="yyyy-mm-dd" id="enddatatime" value="{date("Y-m-d")}" readonly style="width:100px;">
                        </label></td>
                    <td width="10%" align="right">
                        <label style="text-align:right"><input type="button" value="更新" id="postrefresh" name="postrefresh" class="btn"></label>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align:center"><span style="text-align:center;"><button class="btn btn-primary"  title="导出前请先点击更新按钮">导出</button></span></td>
                </tr>
                </form>
                <tr>
                    <td colspan="5"><div style="text-align:center"><input type="button" value="提交查询" id="PostQuery" name="PostQuery" class="btn"></div></td>
                </tr>
            </table>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartable" style="height:500px;text-align:center;"></div></div>
        <div>
        <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
        <div id="detailtable" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;">

        </div>
        </div>
    </div>
</div>
{/strip}
