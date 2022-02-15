{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
<div class="container-fluid">
    {literal}
<script>
$(function(){
	$('.loadinImg').attr('src','');
    $("#flaltt").smartFloat();


});
$.fn.smartFloat = function() {
    var position = function(element) {
        var top = element.position().top; //当前元素对象element距离浏览器上边缘的距离
        var pos = element.css("position"); //当前元素距离页面document顶部的距离
        $(window).scroll(function() { //侦听滚动时
            var scrolls = $(this).scrollTop();

            if (scrolls > 91) { //如果滚动到页面超出了当前元素element的相对页面顶部的高度
                /*
                if (window.XMLHttpRequest) { //如果不是ie6
                    element.css({ //设置css
                        position: "fixed", //固定定位,即不再跟随滚动
                        top: 42 //距离页面顶部为0
                                           }).addClass("shadow"); //加上阴影样式.shadow
                } else { //如果是ie6
                    element.css({
                        top: scrolls  //与页面顶部距离
                    });
                }*/
                $('#flalted').css({width:$('#one1').width()});
                $("#flaltt>td").each(function(i){
                    $("#flalte1>td").eq(i).css({width:$("#flaltt>td").eq(i).width()});

                });
                $('#flalted').css({position: "fixed", //固定定位,即不再跟随滚动
                        top: 42}).removeClass('hide');
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
	<div class="row-fluid">
		<div class="span12">
			<div class="accordion" id="accordion-31884">
				<div class="accordion-group">
					<div class="accordion-heading">
						<h4 style="margin-left:20px;">新增客户统计</h4>
					</div>
					<div style="width-height:100px;overflow:auto">
						<div id="accordion-element-416238" class="accordion-body">
							<div class="accordion-inner">
							<table class="hide listViewEntriesTable">
							<thead>
							<tr><th></th></tr>
							</thead>
							</table>
                                <table class="table hide" id="flalted" style="z-index:1029;">
                                    <tr id="flalte1" class="success">

                                        <td>负责人　　</td>
                                        <td>统计时间　　</td>
                                        <td>机会客户</td>
                                        <td>40%意向客户</td>
                                        <td>60%意向客户</td>
                                        <td>80%意向客户</td>
                                        <td>准客户</td>
                                        <td>特殊关系客户</td>
                                        <td>铁牌成交客户</td>
                                        <td>铜牌成交客户</td>
                                        <td>银牌成交客户</td>
                                        <td>金牌成交客户</td>
                                        <td>VIP成交客户</td>
                                    </tr>
                                </table>
								<table class="table table-bordered table-hover" style="position:relative;" id="one1">
                                    <tr class="success" id="flaltt">

                                        <td>负责人　　</td>
                                        <td>统计时间　　</td>
                                        <td>机会客户</td>
                                        <td>40%意向客户</td>
                                        <td>60%意向客户</td>
                                        <td>80%意向客户</td>
                                        <td>准客户</td>
                                        <td>特殊关系客户</td>
                                        <td>铁牌成交客户</td>
                                        <td>铜牌成交客户</td>
                                        <td>银牌成交客户</td>
                                        <td>金牌成交客户</td>
                                        <td>VIP成交客户</td>
                                    </tr>


									<tbody id="flaltt">
									{foreach item=SIGNRECORD  from=$REPORT}
											{if $SIGNRECORD['last_name'] neq ''}
												<tr class="" data-id="{$SIGNRECORD['smownerid']}">
													<td rowspan="4">{$SIGNRECORD['last_name']}</td>
													<td>当天新增客户</td>
													<td class="getDetail" data-class="dayadd" data-type="chan_notv" data-value="{$SIGNRECORD['daychan_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['daychan_notv']}</span></td>
													<td class="getDetail" data-class="dayadd" data-type="forp_notv" data-value="{$SIGNRECORD['dayforp_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['dayforp_notv']}</span></td>
													<td class="getDetail" data-class="dayadd" data-type="sixp_notv" data-value="{$SIGNRECORD['daysixp_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['daysixp_notv']}</span></td>
													<td class="getDetail" data-class="dayadd" data-type="eigp_notv" data-value="{$SIGNRECORD['dayeigp_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['dayeigp_notv']}</span></td>
													<td class="getDetail" data-class="dayadd" data-type="norm_isv" data-value="{$SIGNRECORD['daynorm_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['daynorm_isv']}</span></td>
													<td class="getDetail" data-class="dayadd" data-type="spec_isv" data-value="{$SIGNRECORD['dayspec_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['dayspec_isv']}</span></td>
													<td class="getDetail" data-class="dayadd" data-type="iron_isv" data-value="{$SIGNRECORD['dayiron_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['dayiron_isv']}</span></td>
													<td class="getDetail" data-class="dayadd" data-type="bras_isv" data-value="{$SIGNRECORD['daybras_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['daybras_isv']}</span></td>
													<td class="getDetail" data-class="dayadd" data-type="silv_isv" data-value="{$SIGNRECORD['daysilv_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['daysilv_isv']}</span></td>
													<td class="getDetail" data-class="dayadd" data-type="gold_isv" data-value="{$SIGNRECORD['daygold_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['daygold_isv']}</span></td>
													<td class="getDetail" data-class="dayadd" data-type="visp_isv" data-value="{$SIGNRECORD['dayvisp_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['dayvisp_isv']}</span></td>
												</tr>
												<tr data-id="{$SIGNRECORD['smownerid']}">
													<td>本周新增客户</td>
													<td class="getDetail" data-class="weekadd" data-type="chan_notv" data-value="{$SIGNRECORD['weekchan_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['weekchan_notv']}</span></td>
													<td class="getDetail" data-class="weekadd" data-type="forp_notv" data-value="{$SIGNRECORD['weekforp_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['weekforp_notv']}</span></td>
													<td class="getDetail" data-class="weekadd" data-type="sixp_notv" data-value="{$SIGNRECORD['weeksixp_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['weeksixp_notv']}</span></td>
													<td class="getDetail" data-class="weekadd" data-type="eigp_notv" data-value="{$SIGNRECORD['weekeigp_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['weekeigp_notv']}</span></td>
													<td class="getDetail" data-class="weekadd" data-type="norm_isv" data-value="{$SIGNRECORD['weeknorm_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['weeknorm_isv']}</span></td>
													<td class="getDetail" data-class="weekadd" data-type="spec_isv" data-value="{$SIGNRECORD['weekspec_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['weekspec_isv']}</span></td>
													<td class="getDetail" data-class="weekadd" data-type="iron_isv" data-value="{$SIGNRECORD['weekiron_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['weekiron_isv']}</span></td>
													<td class="getDetail" data-class="weekadd" data-type="bras_isv" data-value="{$SIGNRECORD['weekbras_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['weekbras_isv']}</span></td>
													<td class="getDetail" data-class="weekadd" data-type="silv_isv" data-value="{$SIGNRECORD['weeksilv_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['weeksilv_isv']}</span></td>
													<td class="getDetail" data-class="weekadd" data-type="gold_isv" data-value="{$SIGNRECORD['weekgold_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['weekgold_isv']}</span></td>
													<td class="getDetail" data-class="weekadd" data-type="visp_isv" data-value="{$SIGNRECORD['weekvisp_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['weekvisp_isv']}</span></td>
												</tr>
												<tr data-id="{$SIGNRECORD['smownerid']}">
													<td>本月新增客户</td>
													<td class="getDetail" data-class="monthadd" data-type="chan_notv" data-value="{$SIGNRECORD['monthchan_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['monthchan_notv']}</span></td>
													<td class="getDetail" data-class="monthadd" data-type="forp_notv" data-value="{$SIGNRECORD['monthforp_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['monthforp_notv']}</span></td>
													<td class="getDetail" data-class="monthadd" data-type="sixp_notv" data-value="{$SIGNRECORD['monthsixp_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['monthsixp_notv']}</span></td>
													<td class="getDetail" data-class="monthadd" data-type="eigp_notv" data-value="{$SIGNRECORD['montheigp_notv']}" style="cursor:pointer;"><span>{$SIGNRECORD['montheigp_notv']}</span></td>
													<td class="getDetail" data-class="monthadd" data-type="norm_isv" data-value="{$SIGNRECORD['monthnorm_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['monthnorm_isv']}</span></td>
													<td class="getDetail" data-class="monthadd" data-type="spec_isv" data-value="{$SIGNRECORD['monthspec_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['monthspec_isv']}</span></td>
													<td class="getDetail" data-class="monthadd" data-type="iron_isv" data-value="{$SIGNRECORD['monthiron_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['monthiron_isv']}</span></td>
													<td class="getDetail" data-class="monthadd" data-type="bras_isv" data-value="{$SIGNRECORD['monthbras_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['monthbras_isv']}</span></td>
													<td class="getDetail" data-class="monthadd" data-type="silv_isv" data-value="{$SIGNRECORD['monthsilv_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['monthsilv_isv']}</span></td>
													<td class="getDetail" data-class="monthadd" data-type="gold_isv" data-value="{$SIGNRECORD['monthgold_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['monthgold_isv']}</span></td>
													<td class="getDetail" data-class="monthadd" data-type="visp_isv" data-value="{$SIGNRECORD['monthvisp_isv']}" style="cursor:pointer;"><span>{$SIGNRECORD['monthvisp_isv']}</span></td>
												</tr>
												<tr>
													<td>总客户数</td>
													<td>{$SIGNRECORD['chan_notv']}</td>
													<td>{$SIGNRECORD['forp_notv']}</td>
													<td>{$SIGNRECORD['sixp_notv']}</td>
													<td>{$SIGNRECORD['eigp_notv']}</td>
													<td>{$SIGNRECORD['norm_isv']}</td>
													<td>{$SIGNRECORD['spec_isv']}</td>
													<td>{$SIGNRECORD['iron_isv']}</td>
													<td>{$SIGNRECORD['bras_isv']}</td>
													<td>{$SIGNRECORD['silv_isv']}</td>
													<td>{$SIGNRECORD['gold_isv']}</td>
													<td>{$SIGNRECORD['visp_isv']}</td>
												</tr>
											{/if}
									{/foreach}
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	{literal}
	<script type="text/javascript">
		$(document).ready(function(){
		    $('#one1').on("click",".getDetail",function(){
				var userid=$(this).closest("tr").data("id");
				var dataClass=$(this).data("class");
				var datatype=$(this).data("type");
				var datavalue=$(this).data("value");
				var thisInstance=this;
				if(datavalue>0){
                    var postData={};
                    postData.data = {
                        "module": "Accounts",
                        "action": "ChangeAjax",
                        "dataClass": dataClass,
                        'mode': 'getAccountReportList',
                        'userid' : userid,
                        'datatype' : datatype
                    }
                    postData.async=false;

                    var Message = app.vtranslate('正在请求...');

                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : Message,
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(postData).then(
                        function(data){
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                            if (data.success) {
                                var str='';
                                $.each(data.result,function(key,value){
                                    str+='<a style="color:red;" href="/index.php?module=Accounts&view=Detail&record='+value.accountid+'" target=_blank>'+value.accountname+'</a><br>'
								});
                                $(thisInstance).children("span").attr('data-content','<font color="red">'+str+'</font>');
                                $(thisInstance).children("span").popover("show");

                            }

                        },
                        function(error,err){}
                    );
                }
        	});
		});

	</script>
    {/literal}
    {include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
