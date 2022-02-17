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
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{assign var="dateFormat" value=$USER_MODEL->get('date_format')}
	<div id="app">
		<template>
			<div class="block">
				<el-date-picker v-model="value2" name="fs"  @change="dateChange" type="daterange" align="center" end-placeholder="结束日期" start-placeholder="开始日期">
				</el-date-picker>
			</div>
		</template>
	</div>
	<input type="hidden" name="startdate" value="{$startdate}">
	<input type="hidden" name="enddate" value="{$enddate}">
{/strip}
<script src="/libraries/jquery/chosen/chosen.jquery.min.js"></script>
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/element-ui@2.15.2/lib/index.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/element-ui@2.15.2/lib/theme-chalk/index.css">
    {literal}
    <script>
        initDatePicker();
        $('.chzn-select').chosen();

        /**
         * 初始化initDatePickert
         * @returns {{value2: (number|Date)[]}}
         */
        function initDatePicker() {
            var Main = {
                data() {
                    return {
                        value2: [$("input[name='startdate']").val(),$("input[name='enddate']").val()],
                    };
                },
                methods: {
                    dateChange(value) {
                        $("input[name='startdate']").val(dateToString(value[0]));
                        $("input[name='enddate']").val(dateToString(value[1]));
                    },
                }
            };
            var Ctor = Vue.extend(Main);
            new Ctor().$mount('#app');
        }

        /**
         * js date对象转字符串
         * @param date
         * @returns {string}
         */
        function dateToString(date) {
            var y = date.getFullYear();
            var m = date.getMonth() + 1;
            m = m < 10 ? ('0' + m) : m;
            var d = date.getDate();
            d = d < 10 ? ('0' + d) : d;
            var h = date.getHours();
            h = h < 10 ? ('0' + h) : h;
            var minute = date.getMinutes();
            minute = minute < 10 ? ('0' + minute) : minute;
            var second= date.getSeconds();
            second = minute < 10 ? ('0' + second) : second;
            // return y + '-' + m + '-' + d+' '+h+':'+minute+':'+ second;
            return y + '-' + m + '-' + d;
        }
    </script>
{/literal}
{/strip}
