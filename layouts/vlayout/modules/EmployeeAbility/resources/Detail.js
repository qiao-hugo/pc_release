/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("EmployeeAbility_Detail_Js",{
	
	//It stores the Account Hierarchy response data
	accountHierarchyResponseCache : {},

	/*
	 * function to trigger Account Hierarchy action
	 * @param: Account Hierarchy Url.
	 */
	triggerAccountHierarchy : function(accountHierarchyUrl) {
		Accounts_Detail_Js.getAccountHierarchyResponseData(accountHierarchyUrl).then(
			function(data) {
				Accounts_Detail_Js.displayAccountHierarchyResponseData(data);
			}
		);

	},

	/*
	 * function to get the AccountHierarchy response data
	 */
	getAccountHierarchyResponseData : function(params) {
		var aDeferred = jQuery.Deferred();

		//Check in the cache
		if(!(jQuery.isEmptyObject(Accounts_Detail_Js.accountHierarchyResponseCache))) {
			aDeferred.resolve(Accounts_Detail_Js.accountHierarchyResponseCache);
		} else {
			AppConnector.request(params).then(
				function(data) {
					//store it in the cache, so that we dont do multiple request
					Accounts_Detail_Js.accountHierarchyResponseCache = data;
					aDeferred.resolve(Accounts_Detail_Js.accountHierarchyResponseCache);
				}
			);
		}
		return aDeferred.promise();
	},

	/*
	 * function to display the AccountHierarchy response data
	 */
	displayAccountHierarchyResponseData : function(data) {
        var callbackFunction = function(data) {
            app.showScrollBar(jQuery('#hierarchyScroll'), {
                height: '200px',
                railVisible: true,
                alwaysVisible: true,
                size: '6px'
            });
        }
        app.showModalWindow(data, function(data){
            if(typeof callbackFunction == 'function'){
                callbackFunction(data);
            }
        });
	}
},{
    registerEvents: function() {
        this._super();
        this.swithStaffLevel();
        this.operateResult();
        // this.managerinput();
        // this.uploadAbilityFile();
        // this.doUploadFile();
        // this.showFileName();
        this.employeeSub();
    },

    employeeSub:function(){
        var thisInstance = this;
        $("body").on("click",'.subData',function (event) {
            var field = $(this).data('field');
            var recordid=$('#recordId').val();
            var targetCase = $(this).parent().parent().find(".target_des").text();
            var message='<h4>?????????????????????<span style="color: red"><'+targetCase+'></span>????</h4><hr>';
            var msg={
                'message':message,
            };
            var stafflevel = $("#stafflevel").val();
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                updateInfo(recordid,'',field,'',stafflevel,'','')
            },function(error, err) {});
        })
    },

    swithStaffLevel:function(){
        $("body").on("change","#stafflevel",function(event){
            var stafflevel = $("#stafflevel").val();
            var recordid = $("input[name='recordid']").val();
            console.log(stafflevel);
            console.log(recordid);

            location.href="index.php?module=EmployeeAbility&view=Detail&record="+recordid+"&stafflevel="+stafflevel;
        });
    },
    operateResult:function () {
        var thisinstance=this;
        $("body").on("click",".operate",function (event) {
            var type = $(this).data("type");
            var field = $(this).data('field');
            var recordid=$('#recordId').val();
            var prereviewresult = $(this).parent().prev().find(".statusinfo").data("reviewresult");
            var target_des = $(this).parent().parent().find(".target_des").text();
            console.log(field);
            console.log(type);
            console.log(prereviewresult);
            var status =$(this).parent().prev().find(".statusinfo").data("status");
            console.log(status);
            var wordsub = $(this).parent().parent().find(".managerinput").val();
            var flag = false;
            if(wordsub!=undefined){
                flag = true;
            }
            if(status==undefined || status =='' || status=='completed'){
                var params = {
                    text: '???????????????',
                    type: 'notice'
                };
                Vtiger_Helper_Js.showMessage(params);
                return;
            }
            if(type=='pass'){
                if(flag&&wordsub<80){
                    alert("79?????????????????????????????????");
                    return;
                }
                var message='<h3>?????????????????????<span style="color: red"><'+target_des+'></span></h3>';
                var msg={
                    'message':message,
                };
                // var str = '<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table">' +
                //     '<tbody>' +
                //     '<tr>' +
                //     '<td class="fieldValue medium" colspan="4">' +
                //     '<div class="row-fluid"><span class="span12"><span class="span2" style="text-align: right">????????????:</span>  ' +
                //     '<select class="span4" name="status">' +
                //     '<option value="qualified">??????</option>'+
                //     '<option value="excellent">??????</option>'+
                //     '</select></span>' +
                //     '</div>' +
                //     '</td>' +
                //     '</tr>' +
                //     '</tbody></table>';
            }else{
                if(flag&&wordsub>=80){
                    alert("80??????????????????????????????");
                    return;
                }
                var message='<h3>????????????<span style="color: red"><'+target_des+'></span></h3>';
                var msg={
                    'message':message,
                    "width":800
                };
                var str = '<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table">' +
                    '<tbody>' +
                    '<tr>' +
                    '<td class="fieldValue medium" colspan="4">' +
                    '<div class="row-fluid"><span class="span12">' +
                    '<textarea class="span12" name="rejectreason"  placeholder="?????????????????????"></textarea></span>' +
                    '</div>' +
                    '</td>' +
                    '</tr>' +
                    '</tbody></table>';
            }

            thisinstance.showConfirmationBox(msg).then(function(e){
                var rejectreason = $("textarea[name='rejectreason']").val();
                var status = $("select[name='status']").val();
                console.log(rejectreason);
                console.log(status);
                var stafflevel = $("#stafflevel").val();
                updateInfo(recordid,type,field,rejectreason,stafflevel,status,prereviewresult,wordsub)

            },function(error, err) {});
            $('.modal-content .modal-body').append(str);
            $('.modal-content .modal-body').css({overflow:'hidden'});

        })
    },
    showConfirmationBox : function(data){
        var thisstance=this;
        var aDeferred = jQuery.Deferred();
        var width='800px';
        if(typeof  data['width'] != "undefined"){
            width=data['width'];
        }
        var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
                if(result){
                    if(thisstance.checkedform()){
                        aDeferred.resolve();
                    }else{
                        return false;
                    }
                } else{
                    aDeferred.reject();
                }
            }, buttons: { cancel: {
                    label: '??????',
                    className: 'btn'
                },
                confirm: {
                    label: '??????',
                    className: 'btn-success'
                }
            }});
        bootBoxModal.on('hidden',function(e){
            if(jQuery('#globalmodal').length > 0) {
                jQuery('body').addClass('modal-open');
            }
        })
        return aDeferred.promise();
    },
    checkedform:function(){
        if( $("textarea[name='rejectreason']").val()==''){
            $("textarea[name='rejectreason']").focus();
            $("textarea[name='rejectreason']").attr('data-content','<font color="red">?????????????????????!</font>');;
            $("textarea[name='rejectreason']").popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout(" $('textarea[name=rejectreason]').popover('destroy')",2000);
            return false;
        }
        return true;
    },

    // doUploadFile:function(){
    //     var thisinstance=this;
    //     $("body").on("click",'.clickupload',function (event) {
    //         window.resultfile='';
    //         $(".ke-upload-file").trigger("click")
    //         $("#drop_area").remove("secondarea");
    //         $("#drop_area").addClass("secondarea");
    //     })
    //
    //     $("body").on('click','.secondarea',function (event) {
    //         $(".ke-upload-file").trigger("click")
    //     })
    //
    // },
    // uploadAbilityFile:function(){
    //     var thisinstance=this;
    //     $("body").on("click",'.uploadabilityfile',function (event) {
    //         var message='<h3>??????</h3>';
    //         var msg={
    //             'message':message,
    //             "width":530
    //         };
    //         var recordid = $("input[name='recordid']").val();
    //         var field = $(this).data("field");
    //         var fileid = $(this).data("fileid");
    //         var loadurl = '';
    //         fieldstr = '';
    //         titlestr = '??????????????????????????????<span style="color: red">(?????????????????????????????????)</span>';
    //         if(field=='accuratesearch' || field=='searchinfo'){
    //             var fieldname='??????????????????';
    //             if(field=='accuratesearch'){
    //                 fieldname='??????????????????';
    //             }
    //             loadurl = "<a style='color: dodgerblue' href='./"+fieldname+".xlsx'>????????????</a>??????";
    //             fieldstr =           "<div style='height: 100px;margin: 20px;'>" +
    //                 "<h4>???.?????????????????????????????????????????????."+loadurl+"</h4>"+
    //                 "<div style='margin-left: 20px;font-size:16px;color: grey;'>????????????:\n<br>" +
    //                 "\n" +
    //                 "1.?????????????????????????????????????????????????????????\n<br>" +
    //                 "\n" +
    //                 "2.????????????????????????4MB</div>"+
    //                 "</div>";
    //             titlestr = '???.??????????????????????????????<span style="color: red">(?????????????????????????????????)</span>';
    //         }
    //
    //         var str = "<div>" +fieldstr+
    //             "<div style='height: 200px;margin: 20px;'>" +
    //             "<input type='hidden' id='fieldselected' value='"+field+"'>"+
    //             "<input type='hidden' id='fileidselected' value='"+fileid+"'>"+
    //             "<h4>"+titlestr+"</h4>"+
    //             "<div id='drop_area' style='margin-left: 20px;font-size:16px;color: grey;width: 400px;height: 135px;background-color: aliceblue;line-height: 135px;text-align: center;border: 1px dashed gray;'>" +
    //             "???????????????????????????<a class='clickupload'>????????????</a>"+
    //             "</div>"+
    //             '<div class="upload" style="display: none">' +
    //                 '<div style="display:inline-block;width:70px;height:30px;overflow: hidden;vertical-align: middle;" title="???????????????????????????">' +
    //                 '<div style="margin-top:-2px;">???????????????</div><div style="margin-top:-5px;">????????????</div></div>'+
    //                 '<input type="button" id="uploadButton" value="??????" title="???????????????????????????" style="display: none;">' +
    //              '</div>'+
    //             '<form id="Form" style="display: none"  method="post" enctype="multipart/form-data"><input type="file" class="ke-upload-file" name="File" ></form>'
    //         ;
    //
    //
    //         thisinstance.showConfirmationBox(msg).then(function(e){
    //             resultfile = window.resultfile;
    //             if(resultfile){
    //                 var data = resultfile;
    //             }else{
    //                 var data = $("input[name='File']")[0].files[0];
    //             }
    //             var reader = new FileReader(); //???????????????????????????
    //             reader.readAsDataURL(data); //
    //             reader.onload = function(ev) { //?????????????????????????????????
    //                 var dataURL = ev.target.result; //??????????????????????????????DataURL,?????????base64??????
    //                 var params = {
    //                     'module': 'EmployeeAbility',
    //                     'action': 'ChangeAjax',
    //                     'mode':"fileupload",
    //                     'processData': false,
    //                     'contentType': false,
    //                     'dataType':"json",
    //                     'type':'POST',
    //                     'file':dataURL,
    //                     'name':data.name,
    //                     'size':data.size,
    //                     'filedatatype':data.type,
    //                     'record':recordid,
    //                     'field':field,
    //                 };
    //                 var Message = app.vtranslate('???????????????...');
    //                 var progressIndicatorElement = jQuery.progressIndicator({
    //                     'message' : Message,
    //                     'position' : 'html',
    //                     'blockInfo' : {'enabled' : true}
    //                 });
    //                 AppConnector.request(params).then(
    //                     function(data){
    //                         progressIndicatorElement.progressIndicator({'mode' : 'hide'});
    //                         if(data.success=true){
    //                             window.location.reload();
    //                         }else{
    //                             alert(data.msg);
    //                         }
    //                     }
    //                 );
    //             }
    //         },function(error, err) {});
    //
    //         $('.modal-content .modal-body').append(str);
    //         $('.modal-content .modal-body').css({overflow:'hidden'});
    //
    //         dropAndTag();
    //     });
    // },
    // managerinput:function(){
    //   $("body").on("blur",'.managerinput',function (event) {
    //       var thisinstance=$(this);
    //       var field = $(this).data("field");
    //       var value = $(this).val();
    //
    //       if(value>100 || value<0){
    //           var params = {
    //               text: '??????????????????0???100??????',
    //               type: 'notice'
    //           };
    //           Vtiger_Helper_Js.showMessage(params);
    //           return;
    //       }
    //       console.log(value);
    //       console.log(field);
    //       var recordid=$('#recordId').val();
    //       var stafflevel = $("#stafflevel").val();
    //       var params={};
    //       params['record'] = recordid;
    //       params['action'] = 'ChangeAjax';
    //       params['module'] = 'EmployeeAbility';
    //       params['mode'] = 'operateInfo';
    //       params['type'] = '';
    //       params['field'] = field;
    //       params['rejectreason'] = '';
    //       params['stafflevel'] = stafflevel;
    //       params['prereviewresult'] = '';
    //       params['status'] = value;
    //       var Message = app.vtranslate('?????????...');
    //       var progressIndicatorElement = jQuery.progressIndicator({
    //           'message' : Message,
    //           'position' : 'html',
    //           'blockInfo' : {'enabled' : true}
    //       });
    //       AppConnector.request(params).then(
    //           function(data) {
    //               progressIndicatorElement.progressIndicator({'mode' : 'hide'});
    //               console.log(data);
    //               if(data.result.success){
    //                   console.log(thisinstance.data("field"));
    //                   // thisinstance.attr({"readonly":true});
    //                   var statusinfo = thisinstance.parent().parent().parent().find(".statusinfo");
    //                   var content = data.result.content;
    //                   statusinfo.attr('data-status',content.status);
    //                   statusinfo.attr('data-reviewresult',content.reviewresult);
    //                   statusinfo.attr('data-rejectreason',content.rejectreason);
    //                   statusinfo.attr('data-rejectnum',content.rejectnum);
    //                   statusinfo.attr('data-rejector',content.rejector);
    //                   var operatetd = thisinstance.parent().parent().parent().find('.operatetd');
    //                   console.log(value!='0');
    //                   if(value && value!='0'){
    //                       operatestr ='<a class="operate" data-type="pass" data-field="'+field+'"><span>??????</span> </a> &nbsp;&nbsp;<span style="color: grey">??????</span>';
    //                       statusinfo.text('?????????');
    //                   }else{
    //                       operatestr ='<span style="color: grey">??????</span> &nbsp;&nbsp;<span style="color: grey">??????</span>';
    //                       statusinfo.text('');
    //                   }
    //                   operatetd.html(operatestr);
    //               }
    //           },
    //           function(error,err){
    //
    //           }
    //       );
    //
    //   })
    // },
    // showFileName:function(){
    //     $("body").on("change","input[name='File']",function (event) {
    //         var files = $(this)[0].files;
    //         console.log(files);
    //         var str=files[0].name;
    //         console.log(str);
    //         console.log(files[0].size);
    //         if(files[0].size>4*1024*1024){
    //             alert("??????????????????4M");
    //             return;
    //         }
    //         // var field = $("#fieldselected").val();
    //         // var ext = GetExtensionFileName(str);
    //         // if((field=='searchinfo' || field=='accuratesearch') && ext!='xlsx'){
    //         //     alert("?????????????????????xlsx??????");
    //         //     return;
    //         // }
    //         document.getElementById('drop_area').innerHTML=str;
    //         var fileurl = window.URL.createObjectURL(files[0]);
    //         //??????????????????
    //         Array.prototype.S=String.fromCharCode(2);
    //         Array.prototype.in_array=function(e){
    //             var r=new RegExp(this.S+e+this.S);
    //             return (r.test(this.S+this.join(this.S)+this.S));
    //         };
    //         var video_type=["video/mp4","video/ogg"];
    //         if(files[0].type.indexOf('image') === 0){  //???????????????
    //             var str="<img style='max-width: 400px;height: 130px;'  src='"+fileurl+"'>";
    //             document.getElementById('drop_area').innerHTML=str;
    //         }else if(video_type.in_array(files[0].type)){   //?????????????????????????????????
    //             var str="<video width='350px' height='130px' controls='controls' src='"+fileurl+"'></video>";
    //             document.getElementById('drop_area').innerHTML=str;
    //         }else{ //??????????????????????????????
    //             //alert("?????????");
    //             // var str=files[0].name;
    //             str = (files[0].name.length>15?files[0].name.substring(0,15)+'...':files[0].name);
    //             var str='<div style="height: 20px;line-height: 20px;width:300px;margin: 0 auto;margin-top: 50px;">'+str+'</div>';
    //             document.getElementById('drop_area').innerHTML=str;
    //         }
    //     })
    // },

});

function updateInfo(recordid,type,field,rejectreason,stafflevel,status,prereviewresult,wordsub) {
    var params={};
    params['record'] = recordid;
    params['action'] = 'ChangeAjax';
    params['module'] = 'EmployeeAbility';
    params['mode'] = 'operateInfo';
    params['type'] = type;
    params['field'] = field;
    params['rejectreason'] = rejectreason;
    params['stafflevel'] = stafflevel;
    params['prereviewresult'] = prereviewresult;
    params['status'] = (status!=undefined)?status:'reject';
    params['wordsub'] = wordsub;
    var Message = app.vtranslate('?????????...');
    var progressIndicatorElement = jQuery.progressIndicator({
        'message' : Message,
        'position' : 'html',
        'blockInfo' : {'enabled' : true}
    });

    AppConnector.request(params).then(
        function(data) {
            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
            console.log(data);
            if(data.result.success){
                location.href="index.php?module=EmployeeAbility&view=Detail&record="+recordid+"&stafflevel="+stafflevel;
            }
        },
        function(error,err){

        }
    );
}

function dropAndTag() {
    var box = document.getElementById('drop_area'); //????????????
    box.addEventListener("drop",function(e){
        var fileList = e.dataTransfer.files; //??????????????????
        //?????????????????????????????????????????????
        if(fileList.length == 0){
            return false;
        }
        console.log(fileList[0].size);
        console.log(fileList[0].name);
        if(fileList[0].size>4*1024*1024){
            alert("??????????????????4M");
            return;
        }
        // var field = $("#fieldselected").val();
        // var ext = GetExtensionFileName(fileList[0].name);
        // if((field=='searchinfo' || field=='accuratesearch') && ext!='xlsx'){
        //     alert("?????????????????????xlsx??????");
        //     return;
        // }
        //???????????????????????????????????????????????????
        //??????????????????
        Array.prototype.S=String.fromCharCode(2);
        Array.prototype.in_array=function(e){
            var r=new RegExp(this.S+e+this.S);
            return (r.test(this.S+this.join(this.S)+this.S));
        };
        var video_type=["video/mp4","video/ogg"];

        //????????????url??????,???src????????????
        var fileurl = window.URL.createObjectURL(fileList[0]);
        if(fileList[0].type.indexOf('image') === 0){  //???????????????
            var str="<img style='max-width: 400px;height: 130px;'  src='"+fileurl+"'>";
            document.getElementById('drop_area').innerHTML=str;
        }else if(video_type.in_array(fileList[0].type)){   //?????????????????????????????????
            var str="<video width='350px' height='130px' controls='controls' src='"+fileurl+"'></video>";
            document.getElementById('drop_area').innerHTML=str;
        }else{ //??????????????????????????????
            //alert("?????????");
            filename =fileList[0].name.length>15?fileList[0].name.substring(0,15)+'...':fileList[0].name;
            var str='<div style="height: 20px;line-height: 20px;width:300px;margin: 0 auto;margin-top: 50px;">'+filename+'</div>';
            document.getElementById('drop_area').innerHTML=str
        }
        resultfile = fileList[0];

        window.resultfile = resultfile;
        $("#drop_area").remove("secondarea");
        $("#drop_area").addClass("secondarea");
    },false);
}

// function GetExtensionFileName(pathfilename) {
//     var reg = /(\\+)/g;
//     var pString = pathfilename.replace(reg, "#"); //????????????????????????\???\\?????????#
//     var arr = pString.split("#"); // ??????#?????????????????????????????????????????? ?????? D Program Files bg.png
//     var lastString = arr[arr.length - 1]; //?????????????????????
//     var arr2 = lastString.split("."); //   ??????"."???????????????
//     return arr2[arr2.length - 1]; //????????????????????????
// }