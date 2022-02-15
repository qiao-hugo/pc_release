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
            <div>

                <div id="calendarview"></div>
            </div>
        </div>

    </div>
    <link rel="stylesheet" media="screen" type="text/css" href="libraries/fullcalendar/fullcalendar.css">

    <script type="text/javascript" src="libraries/fullcalendar/moment.min.js"></script>
    {*{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}*}
    <script type="text/javascript" src="libraries/fullcalendar/fullcalendar.min.js"></script>



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

        {/literal}

        events:'/index.php?module=SalesDaily&action=BasicAjax&mode=getNodaily&__vtrftk='+csrfMagicToken

{literal}
    });
    {/literal}
</script>
{/strip}