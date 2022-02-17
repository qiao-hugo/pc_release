{strip}
    <link rel="stylesheet" href="/libraries/jorgchart/jquery.jOrgChart.css" >
    <script type="text/javascript" src="/libraries/jorgchart/jquery.jOrgChart.js"></script>
    <style>
        a {
            text-decoration: none;
            color: #fff;
            font-size: 12px;
        }
        a:hover{
            text-decoration: none;
            color: #fff;
        }
        .jOrgChart .node {
            width: 120px;
            height: 50px;
            line-height: 50px;
            border-radius: 4px;
            margin: 0 8px;
        }
    </style>
<div style="margin-right:20px;margin-top:10px;">
    <div class="row-fluid" id="c" style="width:100%;">
        <div style="border-right:1px #ccc solid;">
            <table class="table">
                <tr>
                    <td><label class="pull-right">负责人</label></td>
                    <td><label class="pull-left">
                            <select id="user_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" style="width:100px;">
                                <option value="">请选择一项</option>
                                {foreach key=index item=value from=$USERDEPARTMENT}
                                    <option value="{$value.id}" {if $value['id'] eq  $USERID}selected{/if}>{$value.last_name}</option>
                                {/foreach}
                            </select></label></td>

                    <td width="10%" align="right">
                        <label style="text-align:right"><input type="button" value="更新" id="postrefresh" name="postrefresh" class="btn"></label>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><label style="text-align:center"><input type="button" value="提交查询" id="PostQuery" name="PostQuery" class="btn"></label></td>
                </tr>
            </table>
        </div>

        <div  style="width:100%;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding:40px 0;overflow-x:auto; ">
            <div id="jOrgChart"></div>
        </div>

    </div>
</div>
{/strip}
