/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("VisitAccountContract_List_Js",{},{
    blocknum:2,
    CommnetDetail: function(){
		var listInstance = this;
        $('#listViewContents').on('click','.opendialogc',function(){
             $(this).trigger('dblclick');
        });
        $('#listViewContents').on('dblclick','.opendialog',function(){
		var message = '<h3>点评列表</h3>';
		Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
			function(e) {});
        var module = app.getModuleName();
        var recordId=$(this).data('id');
        var postData = {
            "module": module,
            "action": "ChangeAjax",
            "record": recordId,
            'mode':'CommnetDetail',
        }
        AppConnector.request(postData).then(
            function(data){
				if(data.success){
					if(data.result.length>0) {
                        var str = '<div style="height:300px;overflow-y: auto;" id="commentDetailsall">';
                        $.each(data.result, function (k, v) {
                            var deletedicon=v.isdeleted==1?'<i title="删除" data-id="'+v.vacsid+'" class="deletedcomment icon-trash" style="font-size: 22px;color:#ccc;cursor:pointer;"></i>':'';
                            var classic=v.ismodify==1?'<span data-id="'+v.vacsid+'" class="updatecomment updateidclassic'+v.vacsid+'" data-name="classic" data-value="'+v.classicsource+'">'+v.classic+'</span>':v.classic;
                            var commentresult=v.ismodify==1?'<span data-id="'+v.vacsid+'" class="updatecomment updateidcommentresult'+v.vacsid+'" data-name="commentresult" data-value="'+v.commentresultsource+'">'+v.commentresult+'&nbsp;&nbsp;</span>':v.commentresult;
                            var remark=v.ismodify==1?'<span data-id="'+v.vacsid+'" class="updatecomment updateidremark'+v.vacsid+'" data-name="remark" data-value="'+v.remark+'">'+v.remark+'</span>':v.remark;
                            str += '<div class="commentDetails bs-example commentdetail'+v.vacsid+'" ><div class="commentDiv"><div class="singleComment"><div class="commentInfoHeader row-fluid"><div class="commentTitle"><div class="row-fluid"><div class="span1"><img class="alignMiddle pull-left" src="layouts/vlayout/skins/images/DefaultUserIcon.png"></div><div class="span11 commentorInfo"><div class="inner"><span class="commentorName"><strong><span class="label label-a_normal">' + v.username + '</span></strong> </span><span style="margin-left: 20px;">'+deletedicon+'</span><span class="pull-right"><p class="muted">音频类型 :<em>' + classic + '</em>&nbsp;&nbsp;&nbsp;点评结果:' + commentresult + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small>点评时间:' + v.commentdatetime + '</small> &nbsp;&nbsp;</p></span><div class="clearfix"></div></div><div class="commentInfoContent"><style>h4{font-size:14px;font-weight:500;}</style><div class="bs-callout bs-callout-info"><h4>备注说明：</h4>' +remark+ '</div></div><div class="row-fluid"><div class="pull-right commentActions"></div></div></div></div></div></div></div></div></div>';
                        })
                    }else{
                        var str = '<div style="text-align:center">暂无数据';
					}
					str += '</div>';
					$('.modal-content .modal-body').append(str);

				}
            },
            function(error,err){

            }
        );

        })
		
	},
	addCommnet:function(){
		var thisinstance=this;
		$('#listViewContents').on('click','.addcomments',function(){
            var message='<div><h3 style="display: inline-block;">拜访单点评</h3><b class="pull-right"><button class="btn btn-small" type="button" id="addCommnetSing" style="border:1px dashed #178fdd;border-radius:20px;width:40px;height:40px;margin-right:20px;margin-top:10px;"><i class="icon-plus" title="点击添加点评"></i></button></b></div>';
            var msg={
                'message':message,
                "width":800
            };
			var recordid=$(this).data('id');
            thisinstance.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = recordid;
                //params['classic'] = $('select[name="classic[]"]').val();
                //params['commentresult'] = $('select[name="commentresult[]"]').val();
                params['data'] = $('#formcomment').serializeArray();
                params['action'] = 'ChangeAjax';
                params['module'] = 'VisitAccountContract';
                params['mode'] = 'saveComment';
                thisinstance.blocknum=2;//重新还原
                AppConnector.request(params).then(
                    function(data) {
                        $('.commentstaus'+recordid).html('<span class="label label-success">已点评</span>');
                        //window.location.reload(true);
                    },
                    function(error,err){
                        //window.location.reload(true);
                    }
                );
            },function(error, err) {});
            $('.modal-content .modal-body').append('<form name="insertcomment" id="formcomment"><div id="insertcomment" style="height: 300px;overflow: auto"><table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody><tr><td class="fieldLabel medium"><input name="action" type="hidden" value="ChangeAjax"><input name="module" type="hidden" value="VisitAccountContract"><input name="mode" type="hidden" value="saveComment"> <input name="record" type="hidden" value="'+recordid+'"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 音频类型</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select class="chzn-select" name="classic[1]" ><option value="gnosound">无音频</option><option value="bprolusion">开场白</option><option value="dinvite">邀约</option><option value="cdemand">挖需求</option><option value="eproductpresentation">产品介绍</option><option value="fobjectionhandling">异议处理</option><option value="ageneralcomment">总点评</option></select></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 点评结果</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select class="chzn-select" name="commentresult[1]" data-id="1"><option value="enocomment">选择一个选项</option><option value="apoor">不佳</option><option value="bpreferably">较好</option><option value="cverynice">非常好</option><option value="dnormal">正常</option></select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>备注说明</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11 remarkd remarkd1" name="remark[1]" data-id="1"></textarea></span></div></td></tr></tbody></table></div></form>');
            $('.modal-content .modal-body').css({overflow:'auto'});

		});
        $('body').on('click','#addCommnetSing',function(){
            var numd=$('.Duplicates').length+1;
            var insertdata='<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="yesreplace" id="commentsyesreplace"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <i title="删除" class="icon-trash addcommdeleted alignMiddle" data-id="yesreplace"></i><span class="redColor">*</span> 音频类型</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select class="chzn-select" name="classic[]" data-id="yesreplace"><option value="gnosound">无音频</option><option value="bprolusion">开场白</option><option value="dinvite">邀约</option><option value="cdemand">挖需求</option><option value="eproductpresentation">产品介绍</option><option value="fobjectionhandling">异议处理</option><option value="ageneralcomment">总点评</option></select></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 点评结果</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select class="chzn-select" name="commentresult[]"  data-id="yesreplace"><option value="enocomment">选择一个选项</option><option value="apoor">不佳</option><option value="bpreferably">较好</option><option value="cverynice">非常好</option><option value="dnormal">正常</option></select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>备注说明</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11 remarkd remarkdyesreplace" name="remark[]" data-id="yesreplace"></textarea></span></div></td></tr></tbody></table>';
            if(numd>20){return;}/*超过20个不允许添加*/
            var extend=insertdata.replace(/\[\]|replaceyes/g,'['+thisinstance.blocknum+']');
            extend=extend.replace(/yesreplace/g,thisinstance.blocknum);
            thisinstance.blocknum++;
            $('.modal-content .modal-body #insertcomment').append(extend);

            //$('#insert'+classname).before(extend);
        });
        $('body').on('click','.addcommdeleted',function(){
            var tthis=this;
            if(confirm('确定要删除该记录吗?')){
                $('#comments'+$(tthis).data('id')).remove();
            }
        });
        $('body').on('click','.deletedcomment',function(){
            var _this=this;
            if(confirm('确定要删除该记录吗?')){
                var module = app.getModuleName();
                var recordId=$(_this).data('id');
                var postData = {
                    "module": module,
                    "action": "ChangeAjax",
                    "record": recordId,
                    'mode':'deletedCommnetDetail',
                }
                AppConnector.request(postData).then(
                    function(data){
                        if(data.success){
                            $('.commentdetail'+recordId).remove();
                        }
                    },
                    function(error,err){

                    }
                );
            }
        });
        $('body').on('dblclick','.updatecomment',function(){
            var _this=this;
            var dataname=$(_this).data('name');
            var datavalue=$(_this).attr('data-value');
            var recordid=$(this).data('id');
            console.log(dataname);
            switch(dataname){
                case "classic":
                    var str='<div class="updatefield"><input type="hidden" name="fieldname" value="classic">\
                            <select name="fieldvalue" class="fieldblur">\
                            <option value="gnosound">无音频</option>\
                            <option value="bprolusion">开场白</option>\
                            <option value="dinvite">邀约</option>\
                            <option value="cdemand">挖需求</option>\
                            <option value="eproductpresentation">产品介绍</option>\
                            <option value="fobjectionhandling">异议处理</option>\
                            <option value="ageneralcomment">总点评</option>\
                            </select></div>';


                    break;
                case "commentresult":
                    var str='<div class="updatefield"><input type="hidden" name="fieldname" value="commentresult">\
                            <select name="fieldvalue" class="fieldblur">\
                            <option value="enocomment">选择一个选项</option>\
                            <option value="apoor">不佳</option>\
                            <option value="bpreferably">较好</option>\
                            <option value="cverynice">非常好</option>\
                            <option value="dnormal">正常</option>\
                            </select></div>';
                    break;
                case "remark":
                    var str='<div class="updatefield"><input type="hidden" name="fieldname" value="remark">\
                            <textarea name="fieldvalue" class="fieldblur">'+datavalue+'</textarea></div>';
                    break;
            }
            var str=str.replace(datavalue+'"',datavalue+'" selected')
            $(_this).hide();
            $(_this).after(str);
            $('#commentDetailsall').on('click',':not(.fieldblur)',function(event){
                var _this=this;
                console.log(event.target);
                if($(event.target).is('.fieldblur')){
                    return;
                }
                if($('input[name="fieldname"]').length>0){
                    var fieldname=$('input[name="fieldname"]').val();
                    var newvalue=$('.fieldblur').val();
                    var params={};
                    params['fieldname'] = fieldname;
                    params['fieldvalue'] =newvalue;
                    params['recordid'] = recordid;
                    params['action'] = 'ChangeAjax';
                    params['module'] = 'VisitAccountContract';
                    params['mode'] = 'updateField';

                    $('.updateid'+fieldname+recordid).attr('data-value',newvalue);
                    if(fieldname =='classic' || fieldname=='commentresult'){
                        $('.updateid'+fieldname+recordid).text($('.fieldblur option:selected').text());
                    }else if(fieldname =='remark'){
                        $('.updateid'+fieldname+recordid).text(newvalue);
                    }
                    $('.updateid'+fieldname+recordid).show();
                    $('.updatefield').remove();
                    AppConnector.request(params).then(
                        function(data) {

                        },
                        function(error,err){
                            //window.location.reload(true);
                        }
                    );
                }
                $('#commentDetailsall').off('click',':not(.fieldblur)',function(event){});


            });
        });
	},
    checkedform:function(){
        var flag=false;
        $('.remarkd').each(function(){
            if($(this).val()=='') {
                var dataid=$(this).data('id')
                $('.remarkd'+dataid).focus();
                $('.remarkd'+dataid).attr('data-content','<font color="red">必填项不能为空!</font>');;
                $('.remarkd'+dataid).popover("show");
                $('.popover').css('z-index',1000010);
                $('.popover-content').css({"color":"red","fontSize":"12px"});
                setTimeout("$('.remarkd"+dataid+"').popover('destroy')",2000);
                flag=true;
                return false;//跳出each
            }
        });
        if(flag){return false;}
        return true;
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
registerEvents : function(){
	this._super();
	this.addCommnet();
	this.CommnetDetail();
	//this.Tableinstance();
	/*this.BarLinkRemove();
	this.ActiveClick();
	this.setAdvancesmoney();*/
	//this.registerLoadAjaxEvent();

}

});