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
            var message='<h4>确认要提交任务<span style="color: red"><'+targetCase+'></span>吗?</h4><hr>';
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
                    text: '暂不可操作',
                    type: 'notice'
                };
                Vtiger_Helper_Js.showMessage(params);
                return;
            }
            if(type=='pass'){
                if(flag&&wordsub<80){
                    alert("79以下为不合格，不能通过");
                    return;
                }
                var message='<h3>确定要通过任务<span style="color: red"><'+target_des+'></span></h3>';
                var msg={
                    'message':message,
                };
                // var str = '<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table">' +
                //     '<tbody>' +
                //     '<tr>' +
                //     '<td class="fieldValue medium" colspan="4">' +
                //     '<div class="row-fluid"><span class="span12"><span class="span2" style="text-align: right">通过类型:</span>  ' +
                //     '<select class="span4" name="status">' +
                //     '<option value="qualified">合格</option>'+
                //     '<option value="excellent">优秀</option>'+
                //     '</select></span>' +
                //     '</div>' +
                //     '</td>' +
                //     '</tr>' +
                //     '</tbody></table>';
            }else{
                if(flag&&wordsub>=80){
                    alert("80以上为合格，不能驳回");
                    return;
                }
                var message='<h3>驳回任务<span style="color: red"><'+target_des+'></span></h3>';
                var msg={
                    'message':message,
                    "width":800
                };
                var str = '<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table">' +
                    '<tbody>' +
                    '<tr>' +
                    '<td class="fieldValue medium" colspan="4">' +
                    '<div class="row-fluid"><span class="span12">' +
                    '<textarea class="span12" name="rejectreason"  placeholder="请输入驳回原因"></textarea></span>' +
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
                    label: '取消',
                    className: 'btn'
                },
                confirm: {
                    label: '确认',
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
            $("textarea[name='rejectreason']").attr('data-content','<font color="red">必填项不能为空!</font>');;
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
    //         var message='<h3>上传</h3>';
    //         var msg={
    //             'message':message,
    //             "width":530
    //         };
    //         var recordid = $("input[name='recordid']").val();
    //         var field = $(this).data("field");
    //         var fileid = $(this).data("fileid");
    //         var loadurl = '';
    //         fieldstr = '';
    //         titlestr = '请选择需要上传的文件<span style="color: red">(重复上传将覆盖原有数据)</span>';
    //         if(field=='accuratesearch' || field=='searchinfo'){
    //             var fieldname='查找资料模板';
    //             if(field=='accuratesearch'){
    //                 fieldname='精找资料模板';
    //             }
    //             loadurl = "<a style='color: dodgerblue' href='./"+fieldname+".xlsx'>点击下载</a>模板";
    //             fieldstr =           "<div style='height: 100px;margin: 20px;'>" +
    //                 "<h4>一.请按照模板格式填写要上传的文件."+loadurl+"</h4>"+
    //                 "<div style='margin-left: 20px;font-size:16px;color: grey;'>注意事项:\n<br>" +
    //                 "\n" +
    //                 "1.模板中表头名称不可更改，表头行不可删除\n<br>" +
    //                 "\n" +
    //                 "2.上传文件请勿超过4MB</div>"+
    //                 "</div>";
    //             titlestr = '二.请选择需要上传的文件<span style="color: red">(重复上传将覆盖原有数据)</span>';
    //         }
    //
    //         var str = "<div>" +fieldstr+
    //             "<div style='height: 200px;margin: 20px;'>" +
    //             "<input type='hidden' id='fieldselected' value='"+field+"'>"+
    //             "<input type='hidden' id='fileidselected' value='"+fileid+"'>"+
    //             "<h4>"+titlestr+"</h4>"+
    //             "<div id='drop_area' style='margin-left: 20px;font-size:16px;color: grey;width: 400px;height: 135px;background-color: aliceblue;line-height: 135px;text-align: center;border: 1px dashed gray;'>" +
    //             "将文件拖到此处，或<a class='clickupload'>点击上传</a>"+
    //             "</div>"+
    //             '<div class="upload" style="display: none">' +
    //                 '<div style="display:inline-block;width:70px;height:30px;overflow: hidden;vertical-align: middle;" title="文件名请勿包含空格">' +
    //                 '<div style="margin-top:-2px;">文件名请勿</div><div style="margin-top:-5px;">包含空格</div></div>'+
    //                 '<input type="button" id="uploadButton" value="上传" title="文件名请勿包含空格" style="display: none;">' +
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
    //             var reader = new FileReader(); //实例化文件读取对象
    //             reader.readAsDataURL(data); //
    //             reader.onload = function(ev) { //文件读取成功完成时触发
    //                 var dataURL = ev.target.result; //获得文件读取成功后的DataURL,也就是base64编码
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
    //                 var Message = app.vtranslate('正在上传中...');
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
    //               text: '输入数值需在0到100之间',
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
    //       var Message = app.vtranslate('处理中...');
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
    //                       operatestr ='<a class="operate" data-type="pass" data-field="'+field+'"><span>通过</span> </a> &nbsp;&nbsp;<span style="color: grey">驳回</span>';
    //                       statusinfo.text('待审核');
    //                   }else{
    //                       operatestr ='<span style="color: grey">通过</span> &nbsp;&nbsp;<span style="color: grey">驳回</span>';
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
    //             alert("文件不能超过4M");
    //             return;
    //         }
    //         // var field = $("#fieldselected").val();
    //         // var ext = GetExtensionFileName(str);
    //         // if((field=='searchinfo' || field=='accuratesearch') && ext!='xlsx'){
    //         //     alert("导入格式必须为xlsx文件");
    //         //     return;
    //         // }
    //         document.getElementById('drop_area').innerHTML=str;
    //         var fileurl = window.URL.createObjectURL(files[0]);
    //         //规定视频格式
    //         Array.prototype.S=String.fromCharCode(2);
    //         Array.prototype.in_array=function(e){
    //             var r=new RegExp(this.S+e+this.S);
    //             return (r.test(this.S+this.join(this.S)+this.S));
    //         };
    //         var video_type=["video/mp4","video/ogg"];
    //         if(files[0].type.indexOf('image') === 0){  //如果是图片
    //             var str="<img style='max-width: 400px;height: 130px;'  src='"+fileurl+"'>";
    //             document.getElementById('drop_area').innerHTML=str;
    //         }else if(video_type.in_array(files[0].type)){   //如果是规定格式内的视频
    //             var str="<video width='350px' height='130px' controls='controls' src='"+fileurl+"'></video>";
    //             document.getElementById('drop_area').innerHTML=str;
    //         }else{ //其他格式，输出文件名
    //             //alert("不预览");
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
    var Message = app.vtranslate('处理中...');
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
    var box = document.getElementById('drop_area'); //拖拽区域
    box.addEventListener("drop",function(e){
        var fileList = e.dataTransfer.files; //获取文件对象
        //检测是否是拖拽文件到页面的操作
        if(fileList.length == 0){
            return false;
        }
        console.log(fileList[0].size);
        console.log(fileList[0].name);
        if(fileList[0].size>4*1024*1024){
            alert("文件不能超过4M");
            return;
        }
        // var field = $("#fieldselected").val();
        // var ext = GetExtensionFileName(fileList[0].name);
        // if((field=='searchinfo' || field=='accuratesearch') && ext!='xlsx'){
        //     alert("导入格式必须为xlsx文件");
        //     return;
        // }
        //拖拉图片到浏览器，可以实现预览功能
        //规定视频格式
        Array.prototype.S=String.fromCharCode(2);
        Array.prototype.in_array=function(e){
            var r=new RegExp(this.S+e+this.S);
            return (r.test(this.S+this.join(this.S)+this.S));
        };
        var video_type=["video/mp4","video/ogg"];

        //创建一个url连接,供src属性引用
        var fileurl = window.URL.createObjectURL(fileList[0]);
        if(fileList[0].type.indexOf('image') === 0){  //如果是图片
            var str="<img style='max-width: 400px;height: 130px;'  src='"+fileurl+"'>";
            document.getElementById('drop_area').innerHTML=str;
        }else if(video_type.in_array(fileList[0].type)){   //如果是规定格式内的视频
            var str="<video width='350px' height='130px' controls='controls' src='"+fileurl+"'></video>";
            document.getElementById('drop_area').innerHTML=str;
        }else{ //其他格式，输出文件名
            //alert("不预览");
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
//     var pString = pathfilename.replace(reg, "#"); //用正则表达式来将\或\\替换成#
//     var arr = pString.split("#"); // 以“#”为分隔符，将字符分解为数组 例如 D Program Files bg.png
//     var lastString = arr[arr.length - 1]; //取最后一个字符
//     var arr2 = lastString.split("."); //   再以"."作为分隔符
//     return arr2[arr2.length - 1]; //将后缀名返回出来
// }