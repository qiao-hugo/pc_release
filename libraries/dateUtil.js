﻿/**
* 获取本周、本季度、本月、上月的开端日期、停止日期
*/
var now = new Date(); //当前日期
var nowDayOfWeek = now.getDay()==0?7:now.getDay(); //今天本周的第几天
var nowDay = now.getDate(); //当前日
var nowMonth = now.getMonth(); //当前月
var nowYear = now.getFullYear(); //当前年

var lastMonthDate = new Date(); //上月日期
lastMonthDate.setDate(1);
lastMonthDate.setMonth(lastMonthDate.getMonth() - 1);
var lastYear = lastMonthDate.getYear();
var lastMonth = lastMonthDate.getMonth();

//格局化日期：yyyy-MM-dd
function formatDate(date) {
    var myyear = date.getFullYear();
    var mymonth = date.getMonth() + 1;
    var myweekday = date.getDate();

    if (mymonth < 10) {
        mymonth = "0" + mymonth;
    }
    if (myweekday < 10) {
        myweekday = "0" + myweekday;
    }
    return (myyear + "-" + mymonth + "-" + myweekday);
}

//获取差值
function getDateDiff(count) {
    var dd = new Date();
    dd.setDate(dd.getDate() + count);//获取count天后的日期
    var y = dd.getFullYear();
    var m = dd.getMonth() + 1;//获取当前月份的日期
    var d = dd.getDate();

    if (m < 10) {
        m = "0" + m;
    }
    if (d < 10) {
        d = "0" + d;
    }

    return y + "-" + m + "-" + d;
}

//通过起始日期，获取后几天天数数组
function getDaysArrByStartDay(startDay, scale) {
    var dd = new Date(startDay);
    var arr = [];
    if (scale === 'week') {
        var count = 7 - 1;
        arr.push(startDay);
        for (var i = 1; i <= count; i++) {
            //防止冒泡
            dd.setDate(dd.getDate() + 1);//获取count天后的日期
            var y = dd.getFullYear();
            var m = dd.getMonth() + 1;//获取当前月份的日期
            var d = dd.getDate();

            if (m < 10) {
                m = "0" + m;
            }
            if (d < 10) {
                d = "0" + d;
            }

            arr.push(y + "-" + m + "-" + d);
        }
    } else if (scale === 'month') {
        //获取月份天数
        var y = dd.getFullYear();
        var m = dd.getMonth() + 1;//获取当前月份的日期
        var count = Number(getMonthDays(m)) - 1;

        if (m < 10) {
            m = "0" + m;
        }

        for (var i = 1; i <= count; i++) {

            if (i < 10) {
                i = "0" + i;
            }
            arr.push(y + "-" + m + "-" + i);
        }
    }
    return arr;
}

//通过今天，获取前几天天数数组
function getDaysArrByToday(count) {
    var dd = now;
    var arr = [];
    for (var i = 1; i <= count; i++) {
        //防止冒泡
        dd.setDate(dd.getDate() - 1);//获取count天后的日期
        var y = dd.getFullYear();
        var m = dd.getMonth() + 1;//获取当前月份的日期
        var d = dd.getDate();

        if (m < 10) {
            m = "0" + m;
        }
        if (d < 10) {
            d = "0" + d;
        }

        arr.push(y + "-" + m + "-" + d);
    }
    return arr;
}

//今天日期
function getToday() {
    return getDateDiff(0);
}

//昨天日期
function getYestoday() {
    return getDateDiff(-1);
}


//明天日期
function getTomorrow() {
    return getDateDiff(1);
}

//30天前
function getToday30() {
    return getDateDiff(-30);
}

 

//获得某月的天数
function getMonthDays(myMonth) {
    var monthStartDate = new Date(nowYear, myMonth, 1);
    var monthEndDate = new Date(nowYear, myMonth + 1, 1);
    var days = (monthEndDate - monthStartDate) / (1000 * 60 * 60 * 24);
    return days;
}

//获得上周的开端日期
function getLastWeekStartDate() {
    var weekStartDate = new Date(nowYear, nowMonth, nowDay - 6 - nowDayOfWeek);
    return formatDate(weekStartDate);
}

//获得上周的停止日期
function getLastWeekEndDate() {
    var weekEndDate = new Date(nowYear, nowMonth, nowDay - 7 + (7 - nowDayOfWeek));
    return formatDate(weekEndDate);
}

//获得本周的开端日期
function getWeekStartDate() {
    var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek+1);
    return formatDate(weekStartDate);
}

//获得本周的停止日期
function getWeekEndDate() {
    var weekEndDate = new Date(nowYear, nowMonth, nowDay + (7 - nowDayOfWeek));
    return formatDate(weekEndDate);
}

//获得本月的开端日期
function getMonthStartDate() {
    var monthStartDate = new Date(nowYear, nowMonth, 1);
    return formatDate(monthStartDate);
}

//获得本月的停止日期
function getMonthEndDate() {
    var monthEndDate = new Date(nowYear, nowMonth, getMonthDays(nowMonth));
    return formatDate(monthEndDate);
}

//获得上月开端时候
function getLastMonthStartDate() {
	var year = nowYear;
	if(new Date().getMonth() == 0){
		year = nowYear - 1;
	}
    var lastMonthStartDate = new Date(year, lastMonth, 1);
    return formatDate(lastMonthStartDate);
}

//获得上月停止时候
function getLastMonthEndDate() {
	var year = nowYear;
	if(new Date().getMonth() == 0){
		year = nowYear - 1;
	}
    var lastMonthEndDate = new Date(year, lastMonth, getMonthDays(lastMonth));
    return formatDate(lastMonthEndDate);
}

//获得本季度的开端月份
function getQuarterStartMonth() {
    var quarterStartMonth = 0;
    if (nowMonth < 3) {
        quarterStartMonth = 0;
    }
    if (2 < nowMonth && nowMonth < 6) {
        quarterStartMonth = 3;
    }
    if (5 < nowMonth && nowMonth < 9) {
        quarterStartMonth = 6;
    }
    if (nowMonth > 8) {
        quarterStartMonth = 9;
    }
    return quarterStartMonth;
}

//获得本季度的开端日期
function getQuarterStartDate() {

    var quarterStartDate = new Date(nowYear, getQuarterStartMonth(), 1);
    return formatDate(quarterStartDate);
}

//或的本季度的停止日期
function getQuarterEndDate() {
    var quarterEndMonth = getQuarterStartMonth() + 2;
    var quarterStartDate = new Date(nowYear, quarterEndMonth, getMonthDays(quarterEndMonth));
    return formatDate(quarterStartDate);
}

function getThisYearStartDate() {
    var yearStartDate = new Date(nowYear, 0, 1);
    return formatDate(yearStartDate);
}
function getThisYearEndDate() {
    var yearEndDate = new Date(nowYear, 11, 31);
    return formatDate(yearEndDate);
}

function getPreYearStartDate() {
    var yearStartDate = new Date(nowYear-1, 0, 1);
    return formatDate(yearStartDate);
}
function getPreYearEndDate() {
    var yearEndDate = new Date(nowYear-1, 11, 31);
    return formatDate(yearEndDate);
}