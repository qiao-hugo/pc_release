{strip}
<div style="margin-right:20px;margin-top:10px;">
    <div class="row-fluid" id="c" style="width:100%;">
        <div style="border-right:1px #ccc solid;">
            <table class="table">
                <form name="formtable" id="formtable">
                    <tr>
                        <td colspan="12" style="text-align: left;"><span style="font-size: 14px;font-weight: bold;">条件设置栏</span><br/>

                            不设置条件默认查询全部应收明细</td>
                    </tr>
                <tr>
                    <td><label class="pull-right">部门</label></td>
                    <td>
                        <div class="pull-left">
                            {*assign var=arr value=['H4','H5','H7','H8']*}
                            {assign var=arr value=[]}
                            <select id="departmentids" name="departmentids[]" class="chzn-select referenceModulesList streched" multiple>
                                {foreach key=index item=value from=$DEPARTMENTUSER}
                                    <option value="{$index}"{if in_array($index,$arr)} selected{/if}>{$value}</option>
                                {/foreach}
                            </select>
                        </div>
                    </td>
                    <td><label class="pull-right">应收时间</label></td>
                    <td>
                            <label class="pull-left">
                                <input class="span9 dateField"type="text" name="datetime" id="datetime" value="{date("Y-m-d",strtotime("-3 month"))}" readonly style="width:100px;">
                            </label>
                            <label class="pull-left" style="margin:5px 10px 0;">
                                ~
                            </label>
                            <label class="pull-left">
                                <input class="span9 dateField"  type="text" name="enddatetime" data-date-format="yyyy-mm-dd" id="enddatetime" value="{date("Y-m-d")}" readonly style="width:100px;">
                            </label>
                            <label class="pull-left" style="margin-left:10px;">
                                <input type="checkbox" name="checkboxed" id="checkboxed" value="1" title="勾选按时间查询">
                            </label>
                        </div>
                    </td>
                    <td><label class="pull-right">逾期天数</label></td>
                    <td>
                        <label class="pull-left">
                            <select name="overduedayscondition" id="overduedayscondition" class="chzn-select referenceModulesList streched" style="width:120px;">
                                <option value="lqt">小于等于</option>
                                <option value="gqt">大于等于</option>
                                <option value="qt">等于</option>
                            </select></label>
                        <label class="pull-left">&nbsp;</label>
                        <label class="pull-left">
                            <input class="span9 dateField"  type="number" name="overduedays" id="overduedays" value="" min="0" step="1" style="width:80px;">
                        </label>
                    </td>
                    <td><label class="pull-right">收款情况</label></td>
                    <td>
                        <label class="pull-left">
                            <select id="collection" name="collection" class="chzn-select referenceModulesList streched" style="width:100px;">
                                <option value="all">全部</option>
                                <option value="normal">正常</option>
                                <option value="overduereceived">逾期已收</option>
                                <option value="overduecollection">逾期未收</option>
                            </select>
                        </label>
                    </td>
                    <td><label class="pull-right">业务类型</label></td>
                    <td><label class="pull-left">
                            <select id="businesstype" name="businesstype" class="chzn-select referenceModulesList streched" style="width:100px;">
                                <option value="0">全部</option>
                                <option value="1">小SaaS</option>
                                <option value="2">大SaaS</option>
                            </select></label>
                    </td>
                </tr>
                </form>
                <tr>
                    <td colspan="12" style="text-align: center;"><input type="button" value="提交查询" id="PostQuery" name="PostQuery" data-classtype="getAccountData" class="btn"></td>
                </tr>
            </table>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding:5px;">
            <div id="bartable" style="min-height:400px;">
            </div>
            <div class="clearfix"></div>
        </div>
</div>
{/strip}
