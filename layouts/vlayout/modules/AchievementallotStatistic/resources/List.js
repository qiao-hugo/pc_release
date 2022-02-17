


Vtiger_List_Js("AchievementallotStatistic_List_Js",{
	getDefaultParams : function() {
		var pageNumber = jQuery('#pageNumber').val();
		var module = app.getModuleName();
		var parent = app.getParentModuleName();
		var cvId = this.getCurrentCvId();
		var orderBy = jQuery('#orderBy').val();
		var sortOrder = jQuery("#sortOrder").val();
		var pub = $('#public').val();
		var filter=$('#filter').val();
		var DepartFilter=$('#DepartFilter').val();
		var params = {
			'__vtrftk':$('input[name="__vtrftk"]').val(),
			'module': module,
			'parent' : parent,
			'page' : pageNumber,
			'view' : "List",
			'viewname' : cvId,
			'orderby' : orderBy,
			'sortorder' : sortOrder,
			'public' : pub,
			'filter' :filter,
			'department':DepartFilter,
			'accountsname': $("input[name ='accountsname']").val(),
			'smown':$('select[name="smowen"]').val()
		}

        var searchValue = this.getAlphabetSearchValue();

        if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
            params['search_key'] = this.getAlphabetSearchField();
            params['search_value'] = searchValue;
            params['operator'] = "s";
        }
		return params;
	},
    applicationUpdateMonth:function(){
        var _this=this;
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click','.noclick',function(event){
                event.stopPropagation();
        });
        listViewContentDiv.on("click",".checkedinverse",function(event){
            if($(this).attr("checked")=='checked'){
                $('input[name="Detailrecord\[\]"]').prop("checked", true);
            }else{
                $('input[name="Detailrecord\[\]"]').prop("checked", false);
            }
        });
        /*$('.applicationUpdateMonth').on('click',function(){
            var Detailrecords=$('input[name="Detailrecord\[\]"]:checkbox:checked');
            if(Detailrecords.length>0){
                var records='';
                $.each(Detailrecords,function(key,value){
                    records+=$(value).val()+',';
                });
                //console.log(records.substring(0,records.length-1));
                records=records.substring(0,records.length-1);
                records=encodeURI(records);

            }
        });*/
        listViewContentDiv.on('click',".applicationWithholdAchievement",function () {
            var $_this=$(this);
            var waithold=$_this.attr('data-waithold');
            var message=waithold==1?'确认发放':'暂缓发放';
             var msg = {
                 'message':message,
                 "width":"500px",
                 "action":function(){
                     if($("#yearMonth").val()==''){
                         Vtiger_Helper_Js.showMessage({type: 'error', text: '请选择业绩月份！'});
                         return false
                     }
                     if($("#remarks").val()==''){
                         Vtiger_Helper_Js.showMessage({type: 'error', text: '备注必填！'});
                         return false
                     }
                     return true;
                 }
             };
            var tr = $(this).closest('tr');
            var record=$(tr).data("id");
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                 function(e) {
                     var params = {
                         'module': 'AchievementallotStatistic',
                         'action': 'ChangeAjax',
                         'mode': 'withHoldAchievement',
                         'yearMonth':$("#yearMonth").val(),
                         'remarks':$("#remarks").val(),
                         'record':record
                     };
                     AppConnector.request(params).then(
                         function (data) {
                             if(data.result.success){
                                 var currenttitle=$_this.attr('title');//old
                                 var updatewaithold=$_this.attr('data-updatewaithold');//new
                                 var updatetitle=$_this.attr('data-updatetitle');//new
                                 var updatecolor=$_this.attr('data-color');//new
                                 var currentupdatecolor=$_this.attr('data-currentcolor');//new

                                 $_this.attr('data-updatetitle',currenttitle)
                                 $_this.html(updatetitle);
                                 $_this.attr('title',updatetitle);
                                 $_this.attr('data-waithold',updatewaithold);
                                 $_this.attr('data-updatewaithold',waithold);
                                 $_this.attr('data-color',currentupdatecolor);//new
                                 $_this.attr('data-currentcolor',updatecolor);
                                 $_this.parent('a').css('color',updatecolor);
                             }else{
                                 Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.message});
                             }

                         }
                     )
                 }
            );
            var str='<table style="margin-top: 25px;"><tr>\n';
            if(waithold==1){
                str+='<td class="fieldLabel medium">\n' +
                    '     <label class="muted pull-right marginRight10px">修改业绩月份</label>\n' +
                    '       </td>\n' +
                    '       <td class="fieldValue medium">\n' +
                    '          <div class="input-append row-fluid">\n' +
                    '            <div class="span10 row-fluid date form_datetime">\n' +
                    '               <input  type="text" id="yearMonth"  name="budgetlockstart[]"   data-date-format="yyyy-mm-dd" readonly="" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  autocomplete="off">\n' +
                    '               <span class="add-on"><i class="icon-calendar"></i></span>\n' +
                    '</div></div></td></tr><tr>';
            }
            str+='\t\t\t\t<td class="fieldLabel medium">\n' +
                '\t\t\t\t\t<label class="muted pull-right marginRight10px">备注</label>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t\t<td class="fieldValue medium">\n' +
                '\t\t\t\t\t<textarea  id="remarks" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{&quot;mandatory&quot;:false,&quot;presence&quot;:true,&quot;quickcreate&quot;:false,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;text&quot;,&quot;name&quot;:&quot;remark&quot;,&quot;label&quot;:&quot;\u5907\u6ce8&amp;\u8bf4\u660e&quot;}"></textarea>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t</tr></table><span style="color: red;"></span>'
                $('.modal-body').append(str);
            if(waithold==1) {
                $("#yearMonth").datetimepicker({
                    format: 'yyyy-mm',
                    weekStart: 1,
                    autoclose: true,
                    startView: 3,
                    minView: 3,
                    forceParse: false,
                    language: 'zh-CN'
                })
            }
         });
        /*$(".applicationUpdateMonth").click(function () {
            var Detailrecords=$('input[name="Detailrecord\[\]"]:checkbox:checked');
            if(Detailrecords.length>0){
                var records='';
                $.each(Detailrecords,function(key,value){
                    records+=$(value).val()+',';
                });
                records=records.substring(0,records.length-1);
            }else{
                Vtiger_Helper_Js.showMessage({type:'error',text:'请勾选要修改的项'});
                return false;
            }
            var msg = {
                'message': '申请修改业绩月份',
                "width":"500px",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var params = {
                        'module': 'AchievementallotStatistic',
                        'action': 'BasicAjax',
                        'mode': 'batchAudit',
                        'yearMonth':$("#yearMonth").val(),
                        'remarks':$("#remarks").val(),
                        'records':records
                    };
                    AppConnector.request(params).then(
                        function (data) {

                            console.log(data);
                        }
                    )

                }
            );
            $('.modal-body').append('<table style="margin-top: 25px;"><tr>\n' +
                '\t\t\t\t<td class="fieldLabel medium">\n' +
                '\t\t\t\t\t<label class="muted pull-right marginRight10px">修改业绩月份</label>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t\t<td class="fieldValue medium">\n' +
                '\t\t\t\t\t<div class="input-append row-fluid">\n' +
                '\t\t\t\t\t\t<div class="span10 row-fluid date form_datetime">\n' +
                '\t\t\t\t\t\t\t<input  type="text" id="yearMonth"  name="budgetlockstart[]"   data-date-format="yyyy-mm-dd" readonly="" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  autocomplete="off">\n' +
                '\t\t\t\t\t\t\t<span class="add-on"><i class="icon-calendar"></i></span>\n' +
                '\t\t\t\t\t\t</div>\n' +
                '\t\t\t\t\t</div>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t</tr>\n'+
                '<tr>\n' +
                '\t\t\t\t<td class="fieldLabel medium">\n' +
                '\t\t\t\t\t<label class="muted pull-right marginRight10px">备注</label>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t\t<td class="fieldValue medium">\n' +
                '\t\t\t\t\t<textarea  id="remarks" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{&quot;mandatory&quot;:false,&quot;presence&quot;:true,&quot;quickcreate&quot;:false,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;text&quot;,&quot;name&quot;:&quot;remark&quot;,&quot;label&quot;:&quot;\u5907\u6ce8&amp;\u8bf4\u660e&quot;}"></textarea>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t</tr></table><span style="color: red;">注：本月到账业绩相关修改审核截止日期为下个月的3号。没有完成审核不算入当月到账业绩，请及时联系相关人员进行确认</span>');
            $("#yearMonth").datetimepicker({
                format: 'yyyy-mm',
                weekStart: 1,
                autoclose: true,
                startView: 3,
                minView: 3,
                forceParse: false,
                language: 'zh-CN'
            })
        });*/


    },
    exportData:function(){
		$(".exportData").click(function(){
			var msg = {
				'message': '确定导出吗？',
			};
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message': "努力处理中请稍等...",
                        'position': 'html',
                        'blockInfo': {
                            'enabled': true
                        }
                    });
                    var searchParamsPreFix = 'BugFreeQuery';
                    var rowOrder = "";
                    var $searchRows = $("tr[id^=SearchConditionRow]");
                    $searchRows.each(function () {
                        rowOrder += $(this).attr("id") + ",";
                    });

                    eval("$('#" + searchParamsPreFix + "_QueryRowOrder')").attr("value", rowOrder);
                    var limit = $('#limit').val();
                    var o = {};
                    var a = $('#SearchBug').serializeArray();
                    $.each(a, function () {
                        if (o[this.name] !== undefined) {
                            if (!o[this.name].push) {
                                o[this.name] = [o[this.name]];
                            }
                            o[this.name].push(this.value || '');
                        } else {
                            o[this.name] = this.value || '';
                        }
                    });
                    var form = JSON.stringify(o);
                    var departfilter = $('#DepartFilter').val();
                    var urlParams = {
                        "module": "AchievementallotStatistic",
                        "action": "BasicAjax",
                        "mode": "exportdata",
                        "page": 1,
                        "BugFreeQuery": form,
                        "limit": limit,
                        "department": departfilter
                    };
                    var url = location.search; //获取url中"?"符后的字串
                    if (url.indexOf("?") != -1) {
                        var str = url.substr(1);
                        var strs = str.split("&");
                        for (var i = 0; i < strs.length; i++) {
                            if (strs[i].split("=")[0] == 'rechargesource') {
                                urlParams[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
                                break;
                            }
                        }
                    }
                    AppConnector.request(urlParams).then(
                        function (data) {
                            progressIndicatorElement.progressIndicator({
                                'mode': 'hide'
                            })
                            if (data.success) {
                                window.location.href = 'index.php?module=AchievementallotStatistic&view=List&public=exportdata';
                            }
                        }
                    );
                })
		});
	},
    applicationUpdateAchievement:function () {
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click', '.applicationUpdateAchievement', function(e){

            if($(this).data("isover")=='confirmed'){
                Vtiger_Helper_Js.showMessage({type: 'error', text: '已完结不能调整业绩！'});
                return false;
            }
            var tr = $(this).closest('tr');
            var record=$(tr).data("id");
            var date=$("#date").val();
            var msg = {
                'message': '申请调整业绩',
                "width":"400px",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    if(!$("#adjustachievement").val()){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '调整金额不能为空!'});
                        return false;
                    }
                    if($("#adjustachievement").val()!=$("#adjustachievement").val()*1){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '调整金额有误请重新输入!'});
                        return false;
                    }
                    if(!$("#remarks").val()){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '备注不能为空!'});
                        return false;
                    }
                    var params = {
                        'module': 'AchievementallotStatistic',
                        'action': 'ChangeAjax',
                        'mode': 'applicationUpdateAchievement',
                        'adjustachievement':$("#adjustachievement").val(),
                        'remarks':$("#remarks").val(),
                        'record':record
                    };
                    AppConnector.request(params).then(
                        function (data) {
                            if(data.result.success==1){
                                var returnData=data.result.data;
                                var adjustachievement=tr.find(".adjustachievementTd").text();
                                if(adjustachievement){
                                    adjustachievement=parseFloat(adjustachievement);
                                }else{
                                    adjustachievement=0;
                                }
                                adjustachievement=adjustachievement+parseFloat(returnData.adjustachievement);
                                tr.find(".adjustachievementTd").text(gettoDecimal(adjustachievement));
                                var arriveachievement=tr.find(".arriveachievementTd").text();
                                if(arriveachievement){
                                    arriveachievement=parseFloat(arriveachievement);
                                }else{
                                    arriveachievement=0;
                                }
                                arriveachievement=arriveachievement-returnData.adjustachievement;
                                tr.find(".arriveachievementTd").text(gettoDecimal(arriveachievement));
                                //arriveachievementTd
                                var adjustremarks=returnData.adjustremarks;
                                tr.find(".adjustremarksTd").text(adjustremarks);
                                //window.location.reload();
                            }else{
                                Vtiger_Helper_Js.showMessage({type: 'error', text:data.result.message});
                            }

                        }
                    )
                }
            );
            $('.modal-body').append('<table style="margin-top: 25px;"><tr>\n' +
                '\t\t\t\t<td class="fieldLabel medium">\n' +
                '\t\t\t\t\t<label class="muted pull-right marginRight10px">业绩扣减金额</label>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t\t<td class="fieldValue medium">\n' +
                '\t\t\t\t\t<div class="input-append row-fluid">\n' +
                '\t\t\t\t\t\t<div class="span10 row-fluid date form_datetime">\n' +
                '\t\t\t\t\t\t\t\t\t<input id="adjustachievement" type="text" class="input-large"  onkeyup="num(this)" />\n'+
                '\t\t\t\t\t\t</div>\n' +
                '\t\t\t\t\t</div>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t</tr>\n'+
                '<tr>\n' +
                '\t\t\t\t<td class="fieldLabel medium">\n' +
                '\t\t\t\t\t<label class="muted pull-right marginRight10px">备注</label>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t\t<td class="fieldValue medium">\n' +
                '\t\t\t\t\t<textarea  id="remarks" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{&quot;mandatory&quot;:false,&quot;presence&quot;:true,&quot;quickcreate&quot;:false,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;text&quot;,&quot;name&quot;:&quot;remark&quot;,&quot;label&quot;:&quot;\u5907\u6ce8&amp;\u8bf4\u660e&quot;}"></textarea>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t</tr></table><span style="color: red;">注：本月到账业绩相关修改审核截止日期为下个月的'+date+'号。没有完成审核不算入当月到账业绩，请及时联系相关人员进行确认。</span>');
        });


        /*    业绩日期调整 html
              '\t\t\t\t\t\t\t<select class="chzn-select"  ><option value="1" >1号</option><option value="2" >2号</option><option value="3" >3号</option><option value="4" >4号</option><option value="5" >5号</option><option value="6" >6号</option><option value="7" >7号</option><option value="8" >8号</option><option value="9" >9号</option><option value="10" >10号</option><option value="11" >11号</option><option value="12" >12号</option><option value="13" >13号</option><option value="14" >14号</option><option value="15" >15号</option><option value="16" >16号</option><option value="17" >17号</option><option value="18" >18号</option><option value="19" >19号</option><option value="20" >20号</option><option value="21" >21号</option><option value="22" >22号</option><option value="23" >23号</option><option value="24" >24号</option><option value="25" >25号</option><option value="26" >26号</option><option value="27" >27号</option><option value="28" >28号</option></select>\n' +
        */
    },
    /**
     * 减法运算相除JS问题
     * @param arg1除数
     * @param arg2被除数
     * @returns {number}
     */

    accSub:function (arg1,arg2){
        var r1,r2,m,n;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        //动态控制精度长度
        n=(r1=r2)?r1:r2;
        return ((arg1*m-arg2*m)/m).toFixed(n);
    },

    /**
     * 加法相加的问题
     * @param arg1
     * @param arg2
     * @returns {number}
     */
    accAdd:function(arg1,arg2){
        var r1,r2,m;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2))
        var s=(arg1*m+arg2*m)/m;
        if(isNaN(s)){
            s=0;
        }
        return s;
    },
    //计算合计总额
    totalMoney:function(){
        $(".listViewPageDiv .span4").append('<span id="totalAmount" style="max-width:200px; margin-left:20px;display: inline-block">合计实际到账业绩(￥)：0</span>');
        var thisInstance=this;
        $("#listViewContents").on("change",'input[name="Detailrecord[]"]',function(){
            var totalAmount=0;
            var totalOldAmount=0;
            $('input[name="Detailrecord[]"]:checked').each(function () {
                var amount=$(this).data('amount');
                totalAmount=thisInstance.accAdd(totalAmount,amount);
                totalAmount=totalAmount.toFixed(2);
                //var oldAmount=$(this).data('oldamount');
                //totalOldAmount=thisInstance.accAdd(totalOldAmount,oldAmount);
                //totalOldAmount=totalOldAmount.toFixed(2);
            });
            $("#totalAmount").html("合计实际到账业绩(￥)："+totalAmount);
            //$("#totalOldAmount").html("合计原币金额："+totalOldAmount);
        });
        $("#listViewContents").on("click",'input[name="checkAll"]',function(){
            if($(this).prop('checked')){
                $('#listViewContents input[name="Detailrecord[]').prop('checked',true);
            }else{
                $('#listViewContents input[name="Detailrecord[]').prop('checked',false);
            }
            $('#listViewContents input[name="Detailrecord[]').trigger('change');
        });
        $("#listViewContents").ajaxComplete(function(){
            $('input[name="Detailrecord[]"').trigger('change');
        });
    },
	registerEvents : function(){
		this._super();
		this.applicationUpdateMonth();
		this.exportData();
        this.applicationUpdateAchievement();
        this.totalMoney();
	}
});
function gettoDecimal(num){
    var result = parseFloat(num);
    if (isNaN(result)) {
        return false;
    }
    result = Math.round(num * 100) / 100;
    var s_x = result.toString();
    var pos_decimal = s_x.indexOf('.');
    if (pos_decimal < 0) {
        pos_decimal = s_x.length;
        s_x += '.';
    }
    while (s_x.length <= pos_decimal + 2) {
        s_x += '0';
    }
    return s_x;
}
function num(obj){
    obj.value = obj.value.replace(/[^\d\.\-]/g,""); //清除"数字"和"."以外的字符
    obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字
    obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个, 清除多余的
    obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
    obj.value = obj.value.replace(/^(-\d+|\d+)\.(\d\d).*$/,'$1.$2'); //只能输入两个小数
}