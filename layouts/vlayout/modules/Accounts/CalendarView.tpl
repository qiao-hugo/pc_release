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
    <div class="row-fluid">
        <div class="span12">
            <p><!-- Divider --></p>
            <div id="calendarview"></div>
        </div>
    </div>
    <div style="display: none;">
    <div id="showbox" style="width:260px;height: 120px;"><h3 style="height: 30px; line-height: 30px; border-bottom: 1px solid #d3d3d3; font-size: 14px;">请选择</h3><div id="showids" style="width:100%;text-align: center;margin-top:30px;"></div></div>
    </div>
    </div>
<link rel="stylesheet" media="screen" type="text/css" href="libraries/fullcalendar/fullcalendar.css">
<link rel="stylesheet" type="text/css" href="libraries/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript" src="libraries/fullcalendar/moment.min.js"></script>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
<script type="text/javascript" src="libraries/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript" src="libraries/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="libraries/fancybox/jquery.fancybox-1.3.4.pack.js"></script>



    <script>
{literal}

    $('#calendarview').fullCalendar({
        editable:true,
        monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
        monthNamesShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
        dayNames: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
        dayNamesShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
        today: ["今天"],
        firstDay: 1,
        buttonText: {
            today: '本月',
            month: '月',
            week: '周',
            day: '日',
            prev: '上一月',
            next: '下一月'
        },
        dayClick: function(date, allDay, jsEvent, view) {//单击层
			var selDate =$.fullCalendar.formatDate(date,'yyyy-MM-dd');
    	},
    	eventClick: function(calEvent, jsEvent, view) {
    	            var selDate =$.fullCalendar.formatDate(calEvent.start,'yyyy-MM-dd');
                    $(this).css('border-color', 'red');
                    $('#showids').empty();
                    if(calEvent.backgroundColor=='#3a87ad'){
                        var workdaytype=' checked';
                        var holidaytype='';
                        var datetypeflag='work';
                    }else{
                        var workdaytype='';
                        var holidaytype='checked';
                        var datetypeflag='holiday';
                    }
                    $('#showids').append('<span class="label label-a_exception"><label style="display:inline-block;"><input type="radio" name="datetype" value="work"'+workdaytype+'>&nbsp;&nbsp;执行</label></span><label  style="width:40px;display:inline-block;"></label><span class="label label-b_actioning"><label style="display:inline-block;"><input type="radio" name="datetype" value="holiday"'+holidaytype+'>&nbsp;&nbsp;不执行</label></span>');
                    $.fancybox({
				        'type':'inline',
				        'href':'#showbox',
				        'onClosed':function(){
				            var datetype=$("input[name='datetype']:checked").val();
				            if(datetypeflag==datetype){
				                return ;
				            }
				            var params={
                                    'module' : 'Workday',
                                    'action' :	'BasicAjax',
                                    'recordid' :	calEvent.id,
                                    'datetype':$("input[name='datetype']:checked").val(),
                                    'datetime':selDate,
                                    'mode'   : 	'setMothDayHighSeas'
                                };
                                AppConnector.request(params).then(
                                    function(data){
                                        $("#calendarview").fullCalendar('refetchEvents');
                                    });
				        },
			        });
                },
        {/literal}

        events:'/index.php?module=Workday&action=BasicAjax&mode=getMothWorkHighSeas&__vtrftk='+csrfMagicToken

{literal}
    });
    {/literal}
</script>
{/strip}