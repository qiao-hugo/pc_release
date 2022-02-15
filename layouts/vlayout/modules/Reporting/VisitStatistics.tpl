{strip}
    {literal}
    <script>
        $.fn.smartFloat = function() {
        var position = function(element) {
        var top = element.position().top; //当前元素对象element距离浏览器上边缘的距离
        var pos = element.css("position"); //当前元素距离页面document顶部的距离
        $(window).scroll(function() { //侦听滚动时
            var scrolls = $(this).scrollTop();

            if (scrolls > 250) { //如果滚动到页面超出了当前元素element的相对页面顶部的高度
                /*
                if (window.XMLHttpRequest) { //如果不是ie6
                    element.css({ //设置css
                        position: "fixed", //固定定位,即不再跟随滚动
                        top: 45 //距离页面顶部为0
                                           }).addClass("shadow"); //加上阴影样式.shadow
                } else { //如果是ie6
                    element.css({
                        top: scrolls  //与页面顶部距离
                    });
                }*/
                $('#flalted').css({width:$('#one1').width()+2});
                $("#flaltt1>th").each(function(i){
                    $("#flalte1>th").eq(i).css({width:$("#flaltt1>th").eq(i).width()});

                });
                $("#flaltt2>th").each(function(i){
                    $("#flalte2>th").eq(i).css({width:$("#flaltt2>th").eq(i).width()});
                });
                $('#flalted').css({position: "fixed", //固定定位,即不再跟随滚动
                        top: 45}).removeClass('hide');
                //alert(scrolls+'a'+top);

            }else {
                /*
                element.css({ //如果当前元素element未滚动到浏览器上边缘，则使用默认样式
                    position: pos,
                    top: top
                }).removeClass("shadow");//移除阴影样式.shadow
                */
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
                    <td><label class="pull-right">负责人</label><input type="hidden" name="module" value="Reporting"><input type="hidden" name="mode" value="getvisitexp"><input type="hidden" name="action" value="selectAjax"></td>
                    <td><label class="pull-left">
                            <select id="user_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="userid">
                                <option value="">请选择一项</option>
                                {foreach key=index item=value from=$USERDEPARTMENT}
                                    <option value="{$value.id}">{$value.last_name}</option>
                                {/foreach}
                            </select></label></td>
                    {*<td><label class="pull-right">日期</label></td>
                    <td><label class="pull-left">
                            <input class="span9 dateField"type="text" id="datatime" value="{date("Y-m")}-01" readonly style="width:100px;">
                        </label>
                        <label class="pull-left" style="margin:5px 10px 0;">
                            到
                        </label>
                        <label class="pull-left">
                            <input class="span9 dateField"  type="text" name="enddatatime" data-date-format="yyyy-mm-dd" id="enddatatime" value="{date("Y-m-d")}" readonly style="width:100px;">
                        </label>
                    </td>*}
                    <td><label class="pull-right">年份</label></td>
                    <td><div class="pull-left" style="margin-right:20px;">
                            <select id="timeslot" class="chzn-select referenceModulesList streched" name="datetime">
                                <{foreach key=index item=value from=$USERYEARS}
                                <option value="{$value.datetimes}" {if $value.datetimes eq date('Y')}selected{/if}>{$value.datetimes}年</option>
                                {/foreach}
                            </select>
                        </div>
                    </td>
                    <td width="10%" align="right">
                        <label style="text-align:right"><input type="button" value="更新" id="visitrefresh" name="visitrefresh" class="btn"></label>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align:center"><span style="text-align:center;"><button class="btn btn-primary">导出</button></span></td>
                </tr>
            </form>
                <tr>
                    <td colspan="7" style="text-align:center"><span style="text-align:center;"><input type="button" value="提交查询" id="visitQuery" name="visitQuery" class="btn"></span></td>
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
