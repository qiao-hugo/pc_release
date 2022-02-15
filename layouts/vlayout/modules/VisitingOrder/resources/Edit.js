/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("VisitingOrder_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	rowSequenceHolder : false,
	registerReferenceSelectionEvent : function(container) {
		this._super(container);
		var thisInstance = this;
		//wangbin 2015-1-13 修改之前拜访单关联列表,input获取name值有所变化.
		jQuery('input[name="related_to"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){accountlist(data['source_module']);});
		function accountlist(sourcemodule){
			if(sourcemodule=='Accounts'){
				var Accountid=$('input[name="related_to"]');
				if(Accountid.val().length>0){
					thisInstance.loadWidgetNote(Accountid.val());
				}

			}
		}
	},
	loadWidgetNote : function(id){
		$accountid=id;
		var params={};
		params['accountid'] =$accountid ;                  //公司的id
		params['module'] = 'VisitingOrder';
		params['action'] = 'SaveAjax';
		params['mode'] = 'autofillvisitingorder';
		AppConnector.request(params).then(
				function(data){
					if(data.success==true){
						address=data.result[0].address;
						if(address !==null){
							re=new RegExp("#","g");
							var	address=address.replace(re,"");
							$('#VisitingOrder_editView_fieldName_accountaddress').val(address);
						}

						//console.log(data);
						contact=data.result['1'];
						$("select#contactorselect").remove();
						if(data.result[1].length!==0){
							if(contact.length==1){
								$('#VisitingOrder_editView_fieldName_contacts').val(contact[0].name);
							}else{

								var str="";
								$.each(contact,function(n,value){
									str += "<option value="+value.name+'>'+value.name+"</option>";
								})
								newstr = "<select id='contactorselect'>"+str+"</select>"
								$('#VisitingOrder_editView_fieldName_contacts').closest('span').append(newstr);
								$("#contactorselect").on('change',function(){
									$('#VisitingOrder_editView_fieldName_contacts').val($(this).val());
							})

									$('#VisitingOrder_editView_fieldName_contacts').val($("#contactorselect option:first").val());
							;}
						}
					}
				})
	},
    saveData:function() {
        $(".btn-success").on("click", function (e) {
            var destination = $("#VisitingOrder_editView_fieldName_destination").val();
            var check_destination = $("#check_destination").val();
            var destinationcode = $("#VisitingOrder_editView_fieldName_destinationcode").val();
            console.log(destination);
            console.log(check_destination);
            console.log(destinationcode);
            if (!destination) {
                e.preventDefault();
                Vtiger_Helper_Js.showMessage({type: 'error', text: '拜访地址为空或未定位到该位置，请刷新后重试'});
                return;
            }
            if ( !destinationcode) {
                e.preventDefault();
                Vtiger_Helper_Js.showMessage({type: 'error', text: '必须在下拉列表选择位置'});
                return;
            }
            if(destination.trim()!=check_destination.trim()){
                e.preventDefault();
                alert("定位坐标和所选择的拜访地址不是同一位置");
                return;
            }
        })
    },
	selectLi:function(container){
		container.on("click",'.map',function (k, v) {
            var lng = $(this).data("lng");
            var lat = $(this).data("lat");
            console.log(lng);
            console.log(lat);
            var destination = $(this).text();
            console.log(destination);
            var latlng = lat+','+lng;
            var src = "https://apis.map.qq.com/ws/staticmap/v2/?center="+latlng+"&zoom=16&size=1680*300&maptype=roadmap&markers=size:large|color:red|label:k|"+latlng+"&key=YQSBZ-DN7WP-NWGDE-L7OWN-4ZYU2-GCBJU&labels=border:1|size:20|color:0xff0000|bgcolor:white|anchor:3|offset:0_-18|定位地点|"+latlng;
            $("#mapPage").attr("src",src);

			$("#VisitingOrder_editView_fieldName_destinationcode").val(lng+"***"+lat);
            $("#VisitingOrder_editView_fieldName_destination").val(destination);
            $("#check_destination").val(destination);
            $("#addresslist").hide();
        })
	},
    inputAddress:function(){
        $('#VisitingOrder_editView_fieldName_destination').on('keyup',function (e) {
            var destination = $("#VisitingOrder_editView_fieldName_destination").val();
            if (destination == '') {
                $('#addresslist').hide();
                return;
            }
            var postData = {};
            postData.data = {};
            postData.url = 'https://apis.map.qq.com/ws/place/v1/suggestion?keyword=' + destination + '&region=全国&key=YQSBZ-DN7WP-NWGDE-L7OWN-4ZYU2-GCBJU&page_index=1&page_size=10&output=jsonp';
            postData.type = "get";
            postData.dataType = "jsonp";
            AppConnector.request(postData).then(function (data) {
                if (data.count > 0) {
                    $("#addresslist").empty();
                    datas = data.data;
                    var option_str = '';
                    $.each(datas, function (k, v) {
                        option_str += "<li class='map' data-lat=" + v.location.lat + " data-lng=" + v.location.lng + ">" + v.address + v.title + "</li>";
                    });
                    $("#addresslist").html(option_str);
                    $('#addresslist').show();
                }
            });
        });
    },
	changeOutObjective:function(){
		var outobjectiveValue=$('[name="subject"]');
		$('select[name="outobjective"]').on('change',function(){
			var outobjectiveOBJ=$('[name="subject"]');
			var outobjectiveOBJParent=$('[name="subject"]').parent();
			var thisValue=$(this).val();
			if(thisValue!='拜访'){
				if(!outobjectiveOBJ.is('#VisitingOrder_editView_input_subject')){
					outobjectiveOBJParent.html('<input id="VisitingOrder_editView_input_subject" type="text" class="input-large " data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="subject" value="">');
				}
			}else{
				if(!outobjectiveOBJ.is('#VisitingOrder_editView_select_outobjective')){
					outobjectiveOBJParent.html(outobjectiveValue);
					$('[name="subject"]').removeClass('chzn-done');
					outobjectiveValue.chosen().trigger("liszt:updated");
				}
			}
		})
		this.outObjective();
	},
	outObjective:function(){
		var record=$('input[name="record"]').val();
		var outobjective=$('select[name="outobjective"]').val();
		if(outobjective!='拜访' && record>0){
			var subjectVal=$('[name="subject"]').val();

			$('[name="subject"]').parent().html('<input id="VisitingOrder_editView_input_subject" type="text" class="input-large " data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="subject" value="'+subjectVal+'">');
		}

	},
	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.saveData();
		this.selectLi(container);
		this.inputAddress(container);
		this.registerReferenceSelectionEvent(container);
		var time = $("#VisitingOrder_editView_fieldName_startdate").val();
		var endtime = app.addOneHour();
        //var date = new Date($.ajax({async: false}).getResponseHeader("Date"));
        //var bombay = date + (3600000 * 8);
        //var times = new Date(bombay);
		var startDate=$('#VisitingOrder_editView_fieldName_startdate').data('sdate');
		$('#VisitingOrder_editView_fieldName_startdate').datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			language:  'zh-CN',
	        autoclose: true,
	        todayBtn: true,
	        pickerPosition: "bottom-left",
	        showMeridian: 0,
             startDate:startDate
	    });
        //enddate=new Date(date);
        var enddate=$('#VisitingOrder_editView_fieldName_enddate').data("edate");
        //enddate.setMinutes(enddate.getMinutes()+30);
        $('#VisitingOrder_editView_fieldName_enddate').datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			language:  'zh-CN',
	        autoclose: true,
	        todayBtn: true,
	        pickerPosition: "bottom-left",
	        showMeridian: 0,
            startDate:enddate
	    });
	    //$("#VisitingOrder_editView_fieldName_enddate").val(endtime);

        //点击空白处隐藏弹出层，下面为滑动消失效果和淡出消失效果。
        $("#page").click(function(event){
            var _con = $('#addresslist');  // 设置目标区域
            var _con2 = $('#VisitingOrder_editView_fieldName_destination');  // 设置目标区域
            if(!_con.is(event.target) && _con.has(event.target).length === 0 && !_con2.is(event.target) && _con2.has(event.target).length === 0){ // Mark 1
                //$('#divTop').slideUp('slow');  //滑动消失
                $('#addresslist').hide(1000);     //淡出消失
            }
        });
	this.changeOutObjective();
        $('#VisitingOrder_editView_fieldName_destination').on("focus",function () {
            $("#addresslist").show();
        });
        // var sto;
        // jQuery("#VisitingOrder_editView_fieldName_destination").keyup(function(){
        //     $("#VisitingOrder_editView_fieldName_destinationcode").val('');
        //     try{ clearTimeout(sto); }catch(e) {};
        //     sto=setTimeout(myFun,500);
        // }).blur(function(){
        //     try{ clearTimeout(sto); }catch(e) {};
        // });
        // function myFun(){
        //     var destination = $("#VisitingOrder_editView_fieldName_destination").val();
        //     console.log(destination);
        //     var postData= {
        //         'module': 'VisitingOrder',
        //         'action': 'ChangeAjax',
        //         'mode': 'locationAddress',
        //         'keyword':destination,
		// 		'dataType':'json'
        //     };
        //     AppConnector.request(postData).then(
        //         function (data) {
        //             console.log(data);
        //             if (data.count > 0) {
        //                 $("#addresslist").empty();
        //                 datas = data.data;
        //                 var option_str = '';
        //                 $.each(datas, function (k, v) {
        //                     option_str += "<li class='map' data-lat=" + v.location.lat + " data-lng=" + v.location.lng + ">" + v.address + v.title + "</li>";
        //                 });
        //                 $("#addresslist").append(option_str);
        //                 flag = false;
        //                 $('#addresslist').show();
        //             }
        //         }
        //     );
        // }
		//

        // $('#VisitingOrder_editView_fieldName_destination').on("input",function (e) {
        //     var destination = $("#VisitingOrder_editView_fieldName_destination").val();
        //         console.log(destination);
        //         var postData= {
        //             'module': 'VisitingOrder',
        //             'action': 'ChangeAjax',
        //             'mode': 'locationAddress',
        //             'keyword':destination
        //         };
        //     setTimeout(function () {
        //         AppConnector.request(postData).then(
        //             function (data) {
        //                 console.log(data);
        //                 if (data.count > 0) {
        //                     $("#addresslist").empty();
        //                     datas = data.data;
        //                     var option_str = '';
        //                     $.each(datas, function (k, v) {
        //                         option_str += "<li class='map' data-lat=" + v.location.lat + " data-lng=" + v.location.lng + ">" + v.address + v.title + "</li>";
        //                     });
        //                     $("#addresslist").append(option_str);
        //                     flag = false;
        //                     $('#addresslist').show();
        //                 }
        //             }
        //         );
        //     },1000);
        // })

        // //点击空白处隐藏弹出层，下面为滑动消失效果和淡出消失效果。
        // $("#page").click(function(event){
        //     var _con = $('#addresslist');  // 设置目标区域
        //     var _con2 = $('#VisitingOrder_editView_fieldName_destination');  // 设置目标区域
        //     if(!_con.is(event.target) && _con.has(event.target).length === 0 && !_con2.is(event.target) && _con2.has(event.target).length === 0){ // Mark 1
        //         //$('#divTop').slideUp('slow');  //滑动消失
        //         $('#addresslist').hide(1000);     //淡出消失
        //     }
        // });
        // $('#VisitingOrder_editView_fieldName_destination').on("focus",function () {
        //     $("#addresslist").show();
        // });
        // var flag = true;
        // $('#VisitingOrder_editView_fieldName_destination').on('compositionstart', function () {
        // 	console.log(1);
        //     flag = false;
        // });
        // $('#VisitingOrder_editView_fieldName_destination').on('compositionend', function () {
        //     console.log(2);
        //     flag = true;
        // });
		//
        // var lastTime;
        // $(function(){
        //     $('#VisitingOrder_editView_fieldName_destination').keyup(function (e) {
        //         lastTime = e.timeStamp;
        //         setTimeout(function () {
        //             if (lastTime - e.timeStamp == 0) {
        //                 var destination = $("#VisitingOrder_editView_fieldName_destination").val();
        //                 if (flag) {
        //                     console.log(destination);
        //                     var postData= {
        //                         'module': 'VisitingOrder',
        //                         'action': 'ChangeAjax',
        //                         'mode': 'locationAddress',
        //                         'keyword':destination
        //                     };
		//
        //                     AppConnector.request(postData).then(
        //                         function(data){
        //                             console.log(data);
        //                             if(data.count>0){
        //                                 $("#addresslist").empty();
        //                                 datas = data.data;
        //                                 var option_str='';
        //                                 $.each(datas,function (k, v) {
        //                                     option_str += "<li class='map' data-lat="+v.location.lat+" data-lng="+v.location.lng+">"+v.address+v.title+"</li>";
        //                                 });
        //                                 $("#addresslist").append(option_str);
        //                                 flag =false;
        //                                 $('#addresslist').show();
        //                             }
        //                         }
        //                     );
        //                 }
        //             }
        //         }, 500);
        //     });
        // })
	}
});




















