{strip}
    {literal}
    <script>
        $.fn.smartFloat = function() {
            var position = function(element) {
                var top = element.position().top; //当前元素对象element距离浏览器上边缘的距离
                var pos = element.css("position"); //当前元素距离页面document顶部的距离
                $(window).scroll(function() { //侦听滚动时
                    var scrolls = $(this).scrollTop();
                    if (scrolls > 235) { //如果滚动到页面超出了当前元素element的相对页面顶部的高度
                        $('#flalted').css({width:$('#one1').width()+2});
                        $("#flaltt1>th").each(function(i){
                            $("#flalte1>th").eq(i).css({width:$("#flaltt1>th").eq(i).width()});

                        });

                        $('#flalted').css({position: "fixed",top: 45}).removeClass('hide');


                    }else {
                        $('#flalted').addClass('hide');
                    }
                });
            };
            return $(this).each(function() {
                position($(this));
            });
};
    </script>
    {/literal}
<div style="margin-right:20px;margin-top:10px;">
    <div class="row-fluid" id="c" style="width:100%;">
        <div style="border-right:1px #ccc solid;">

            <table class="table">
                <form method="POST" action="index.php">
                <tr>
                    <td><label class="pull-right">部门</label></td>
                    <td><div class="pull-left">
                            {assign var=arr value=['H4','H5','H7','H8']}
                            <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" multiple name="department[]">
                                {foreach key=index item=value from=$DEPARTMENTUSER}
                                    <option value="{$index}" {if in_array($index,$arr)} selected{/if}>{$value}</option>
                                {/foreach}
                            </select></div></td>
                    <td><label class="pull-right">年度</label><input type="hidden" name="module" value="VCommentAnalysis"><input type="hidden" name="mode" value="getvisitexp"><input type="hidden" name="action" value="selectAjax"></td>
                    <td><label class="pull-left">
                            <input id="datayear" type="text" class="span9 dateField" name="datayear" data-date-format="yyyy"
                                   type="text" readonly  value="{'Y'|date}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                        </label>
                           </td>
                    <td><label class="pull-right">月份</label></td>
                    <td><div class="pull-left" style="margin-right:20px;">
                            <input id="datamonth" type="text" class="span9 dateField" name="datamonth" data-date-format="yyyy"
                                   type="text" readonly value="{'m'|date}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>

                        </div>
                    </td>

                </tr>

            </form>
                <tr>
                    <td colspan="6" style="text-align:center"><span style="text-align:center;"><input type="button" value="提交查询" id="visitQuery" name="visitQuery" class="btn"></span></td>
                </tr>
            </table>

        </div>
        <div style="margin:0 auto 40px;padding-right:20px;">
            <div id="bartable" style="margin:0 auto 20px;text-align: center;">

            </div>
        </div>
        <div>
        </div>
    </div>
</div>
{/strip}
