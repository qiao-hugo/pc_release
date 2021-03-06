<?php
/* Smarty version 3.1.28-dev/79, created on 2018-01-15 11:38:01
  from "/data/httpd/vtigerCRM/apps/views/ActivationCode/upgrade.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a5c2219a8ae77_35596846',
  'file_dependency' => 
  array (
    '94e566785ee14c0ab300b2daa31b59c4121bdaa4' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/ActivationCode/upgrade.html',
      1 => 1504852764,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
  ),
),false)) {
function content_5a5c2219a8ae77_35596846 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
		<title>获取T云激活码</title>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <link href="static/css/select2.css" rel="stylesheet" type="text/css" />
        <link href="static/css/mobiscroll.custom-2.5.0.min.css" rel="stylesheet" type="text/css" />
		<?php echo '<script'; ?>
 src="static/js/jquery.form.js?v=<?php echo $_smarty_tpl->tpl_vars['versionjs']->value;?>
"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="static/js/select2.js?v=<?php echo $_smarty_tpl->tpl_vars['versionjs']->value;?>
"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="static/js/mobiscroll.js?v=<?php echo $_smarty_tpl->tpl_vars['versionjs']->value;?>
"><?php echo '</script'; ?>
>

        
    <style type="text/css">
    html,body{
    	height:100%;
    }
.rowFrame{
	overflow-y:auto;
	    min-height: 50%;
}
        *{
            text-shadow:none;
        }
        .ui-page{
        height:100%;
        }
        
        .footer{
        	position: absolute;
        	bottom:0
        }
        .close{
        	color:#fff;
        	text-shadow:0 1px 0 #000;
        	opacity: 1;
        }
        .add-visit label{
        	width: 27%;
        }
        .add-visit .input-box{
        	width: 71%;
        }
        .form-group{
        margin-bottom:10px;
        }
    #loading{background-color:#000000;height:100%;width:100%;position:fixed;z-index:99999;margin:0px;padding:0px;top:0px;opacity: 0.5;}
    #loading-center{width:100%;height:100%;position: relative;}
    #loading-center-absolute {position:absolute;left:50%;top:50%;height:20px;width:100px;margin-top:-10px;margin-left:-50px;}
    .object{width:20px;height:20px;color:#333;font-size:10px;text-align:center;line-height:20px;background-color: #FFF;-moz-border-radius: 50% 50% 50% 50%;-webkit-border-radius: 50% 50% 50% 50%;border-radius: 50% 50% 50% 50%;margin-right: 20px;margin-bottom: 20px;position: absolute;opacity: 1;}
    #object_one{-webkit-animation: object 2s linear infinite;animation: object 2s linear infinite;}
    #object_two{-webkit-animation: object 2s linear infinite -.4s;animation: object 2s linear infinite -.4s;}
    #object_three{-webkit-animation: object 2s linear infinite -.8s;animation: object 2s linear infinite -.8s;}
    #object_four{-webkit-animation: object 2s linear infinite -1.2s;animation: object 2s linear infinite -1.2s;}
    #object_five{-webkit-animation: object 2s linear infinite -1.6s;animation: object 2s linear infinite -1.6s;}
    @-webkit-keyframes object{0% {left:100px;top:0} 80% {left:0;top:0;} 85% {left:0;top:-20px;width:20px;height:20px;} 90% {width:40px;height:15px;} 95% {left:100px;top:-20px;width:20px;height:20px;} 100% {left:100px; top:0; }}
    @keyframes object{0% { left:100px;top:0} 80% {left:0;top:0;} 85% {left:0;top:-20px;width:20px;height:20px;} 90% {width:40px; height:15px;} 95% {left:100px;top:-20px;width:20px;height: 20px;} 100% {left: 100px; top:0; }}
    </style>
    
</head>
<body>
<div class="container-fluid w fix rowFrame" style='padding-bottom:0'>
<!-- <div id='test'></div> -->
        <div class="row ">
            <form id='myForm2' onsubmit='return check()'  method="POST">
            <div class="add-visit">
                <div class="form-group fix">
                    <label>类型</label>
                    <div class="input-box">
                        <select id="classid" name="classid" class="form-control" onchange="pci(this.options[this.options.selectedIndex].value);"  data-toggle="popover" data-placement="bottom" data-content="产品必选">
                            <option value='upgrade' selected>升级</option>
                            <option value='renewal'>续费</option>
                        </select>
                        <input type="hidden" id="pcipre" value="upgrade">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>CRM客户名称</label>
                    <div class="input-box">
                        <input type="hidden" id="customerid" name="customerid" value="" >
                        <input type="hidden" id="customerowenid" value="" >
                        <div class="input-group">
                            <input type="text" data-toggle="popover" data-placement="bottom" data-content="客户名称不能为空" id="customername_display" name="customername_display" class="form-control keyInput">
                            <span class="input-group-btn">
                        		<input type="button" id='search2' class="btn btn-default" value="搜索">
                        	</span>
                        </div>
                        <div>
                            <span style="color:red" id="customerowenidmsg"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>用户名</label>
                    <div class="input-box">
                        <input type="hidden" id="scontract" name="scontract" value="" >
                        <input type="hidden" id="scontractowenid" value="">
                        <input type="hidden" id="activecode" name="activecode" value="">
                        <input type="hidden" id="oldproductid" name="oldproductid" value="">
                        <input type="hidden" id="agents" name="agents"  value="<?php if ($_smarty_tpl->tpl_vars['userid']->value == 199) {?>10642<?php } else {
echo $_smarty_tpl->tpl_vars['agents']->value;
}?>" class="form-control"  readonly="true">
                        <div class="input-group">
                        <input type="text" id="LoginName" data-toggle="popover" data-placement="bottom" data-content="用户名必填!" name="LoginName" class="form-control">
                            <span class="input-group-btn">
                                  <input type="button" id='LoginNameSearch' class="btn btn-default" value="搜索">
                            </span>
                        </div>
                        <div>
                            <span style="color:red" id="LoginNamemsg"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>原版本</label>
                    <div class="input-box">
                        <input type="text" id="oldproductidName" data-toggle="popover" readonly data-placement="bottom" data-content="原版本名称!" name="oldproductidName" class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>原到期时间</label>
                    <div class="input-box">
                        <input type="text" id="CloseDate" data-toggle="popover" readonly data-placement="bottom" data-content="到期时间必填!" name="CloseDate" class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label class="">合同编号</label>
                    <div class="input-box">
                        <input type="hidden" id="contractid" name="contractid" value="" >
                        <input type="hidden" id="contractowenid" value="">
                        <div class="input-group">
                            <input type="text" data-toggle="popover" data-placement="bottom" placeholder="建议输入合同编号后四位"
                                   data-content="合同编号不能为空" id="contractname_display"  name="contractname_display" class="form-control keyInput">
                            <span class="input-group-btn">
                              <input type="button" id='search1' class="btn btn-default" value="搜索">
                           </span>
                        </div>
                        <div>
                            <span style="color:red" id="contractowenidmsg"></span>
                        </div>
                    </div>
                </div>


                <div class="form-group fix" id="productyear">
                    <label>年限</label>
					<div class="input-box">
						<select id="productlife" name="productlife" class="form-control" onchange="pl(this.options[this.options.selectedIndex].value);" data-toggle="popover" data-placement="bottom" data-content="年限必选">
							<option value='' selected>请选择年限</option>
							<option value='1'>一年</option>
							<option value='2'>二年</option>
							<option value='3'>三年</option>
							<option value='4'>四年</option>
							<option value='5'>五年</option>
							<option value='6'>六年</option>
							<option value='7'>七年</option>
							<option value='8'>八年</option>
							<option value='9'>九年</option>
							<option value='10'>十年</option>
						</select>
						<input type="hidden" id="plpre" value="">
					</div>
                </div>
                <div id="changedelted">
                <div class="form-group fix">
                    <label>产品版本</label>
                    <div class="input-box productadd">
                       <select id="productid" name="productid" class="form-control" onchange="pi(this.options[this.options.selectedIndex].value);"  data-toggle="popover" data-placement="bottom" data-content="产品必选">
							<option value='' selected>请选择产品版本</option>

						</select>
						<input type="hidden" id="pipre" value="">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>升级日期</label>
                    <div class="input-box">
                        <input type="text" id="upgradedate" name="upgradedate" class="form-control" data-toggle="popover" data-placement="bottom" data-content="升级日期必填!">
                    </div>
                </div>
                </div>

                <div class="confirm tc">
                        <input type="button" id='dosave' class="btn" data-toggle="popover" data-placement="top" 
                        data-content="正在,请稍等…" value="确定升级">
                </div>
                <div class="confirm tc" id="showActiveCode"></div>
				
            </div>
        	</form>
            
        </div>

    </div>

 <?php echo '<script'; ?>
>
     
     var upgradeproduct=<?php echo $_smarty_tpl->tpl_vars['upgrade']->value;?>
;
     var downgradeproduct=<?php echo $_smarty_tpl->tpl_vars['downgrade']->value;?>
;
     
	    $(function () {
            // 下拉框加载
            $(".select2").select2({
                width: '100%',
                height: 100
            });
            $('#LoginNameSearch').on('click', function () {
                var o = $('#LoginName');
                var ov =  o.val();
                var op = o.parent();
                var sb = [];
                if('' == ov){
                    Tips.alert({
                        content: '请填写用户名',
                    });
                    return;
                }
                o.next('ul').remove();
                $('.delefalg').remove();
                var dheight=$(document).height();
                dheight=dheight*0.5;
                oul = op.append('<ul class="keyText delefalg" id="keytext2" style="max-height:'+dheight+'px;overflow:auto;"></ul>');

                if (ov) {
                    op.addClass('keyBox');
                    $('#loading').show();
                    $.ajax({
                        url: '/index.php?module=ActivationCode&action=getUserMsg&LoginName='+ov,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $('#loading').hide();
                            if (data.success) {
                                var classid=$('select[name="classid"]').val();
                                var updowngradeproduct=classid=='upgrade'?upgradeproduct[data.message.ProductID]:downgradeproduct[data.message.ProductID];
                                var dataCondtractcode=data.message.ContractCode==null?'':data.message.ContractCode;
                                $('#CloseDate').val(data.message.CloseDate);
                                $('#scontract').val(dataCondtractcode);
                                $('#oldproductidName').val(data.message.ProductTitle);
                                $('#oldproductid').val(data.message.ProductID);
                                $('#LoginNamemsg').text('原合同编号:'+dataCondtractcode);
                                if(updowngradeproduct!==undefined)
                                {
                                    var selectproduct='<select id="productid" name="productid" class="form-control" onchange="pi(this.options[this.options.selectedIndex].value);"  data-toggle="popover" data-placement="bottom" data-content="产品必选">';
                                    $.each(updowngradeproduct,function(key,value){
                                        selectproduct+='<option value="'+value['product']+'">'+value['name']+'</option>';
                                    });
                                    selectproduct+='</select><input type="hidden" id="pipre" value="">';
                                    $('.productadd').html(selectproduct);
                                }

                            }else{
                                Tips.alert({
                                    content: '找不到用户对应的信息',
                                    define:'确定',
                                    after:function(){
                                        $('#CloseDate').val('');
                                        $('#scontractid').val('');
                                        $('#oldproductidName').val('');
                                        $('#oldproductid').val('');
                                        $('#LoginNamemsg').text('');
                                    }
                                });

                            }
                        },error:function(){
                            $('#loading').hide();
                            Tips.alert({
                                content: 'error'
                            });
                        }
                    });
                }
            });

            $('#search1').on('click', function () {
                var o = $('#contractname_display');
                var ov = o.val();
                var op = o.parent();
                var sb = [];
                if('' == ov){
                	Tips.alert({
                	    content: '合同编号不能为空',
                	});
                	return;
                }
                o.next('ul').remove();
                $('.delefalg').remove();
                var dheight=$(document).height();
                dheight=dheight*0.5;
                oul = op.append('<ul class="keyText delefalg" style="max-height:'+dheight+'px;overflow:auto;"></ul>');

                if (ov) {
                    op.addClass('keyBox');
                    $('#loading').show();
                    $.ajax({
                        url: '/index.php?module=ActivationCode&action=searchContract&contract_no='+ov,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $('#loading').hide();
                            if (data && data.length > 0) {
                                for (var i = 0;i<data.length; i++) {
                                	//console.log(data[i].item.servicecontracts_no);alert(data[i]['servicecontracts_no']);
                                     var item2=data[i];
                                     var nArr = item2.contract_no;
                                     var nid = item2.servicecontractsid;
                                     var username = item2.username;
                                     var userid = item2.userid;
                                     var oli=op.children('ul');
                                    oli.append("<li onclick='select_id1(\""+nArr+"\", \""+nid+"\", \""+username+"\", \""+userid+"\" )'>" + nArr + '</li>');

                                }

                            }else{
                            	Tips.alert({
                            	    content: '找不到合同编号',
                            	    define:'确定',
                            	    after:function(){
                            	    	$("#contractname_display").val('');
                            	    	$("#contractid").val('');
                            	    }
                            	});
                            	
                            }
                        },error:function(){
                            $('#loading').hide();
                            Tips.alert({
                                content: 'error'
                            });
                        }
                    });
                }
            });
            
        });
        blankFixExt('keyInput', 'keyText');
        function blankFixExt(node, targetNode) {
            $(document).bind('click', function (e) {
                var o = "." + node + ",." + node + " *";
                
                if (!$(e.target).is(o)&& (e.target.id)!='search1') {
                    $('.' + targetNode).hide();
                }
            });
         }
        function select_id1(id, cid,username,userid){
        	$("#contractid").val(cid);
        	$("#contractname_display").val(id);
        	$("#contractowenid").val(userid);
        	$("#contractowenidmsg").text('合同提单人:'+username);
        }
        
        $('#search2').on('click', function () {
            var o = $('#customername_display');
            var ov = o.val();
            var op = o.parent();
            var sb = [];
            if('' == ov){
            	Tips.alert({
            	    content: '客户名称不能为空',
            	});
            	return;
            }
            o.next('ul').remove();
            $('.delefalg').remove();
            var dheight=$(document).height();
            dheight=dheight*0.5;
            oul = op.append('<ul id="keyText2" class="keyText delefalg" style="max-height:'+dheight+'px;overflow:auto;"></ul>');

            if (ov) {
                $('#loading').show();
                op.addClass('keyBox');
                $.ajax({
                    url: '/index.php?module=Accounts&action=searchAccount&company='+ov,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#loading').hide();
                        if (data && data.length > 0) {
                            for (var i = 0;i<data.length; i++) {
                                 var item2=data[i];
                                 var nArr = item2.value;
                                 var oli=op.children('ul');
                                oli.append("<li onclick='select_id2("+item2.id+")'>" + nArr + '</li>');
                            }
                          $("#keyText2").show();
                        }else{
                        	Tips.alert({
                        	    content: '找不到客户',
                        	    define:'确定',
                        	    after:function(){
                        	    	$("#customername_display").val('');
                        	    	$("#customerid").val('');
                        	    }
                        	});
                        }
                        $('#scontract').val('');
                        //$('#scontractowenid').val('');
                        //$('#activecode').val('');
                        //$('#scontractname_display').val('');
                        $('#LoginNamemsg').text('');
                    },error:function(){
                        $('#loading').hide();
                        Tips.alert({
                            content: 'error'
                        });
                    }
                });
            }
        });

        function select_id2(id){
            var idval = id;
            $.ajax({
                url: "/index.php?module=Accounts&action=getAccountMsg&id="+id,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if(data==''){
                        Tips.alert({
            			    content: '客户信息不全'
            			});
                        return false;
                    }
                    //$('#destination').val(data.address);
                    $('#customerid').val(data.accountid);
                    $('#customerowenid').val(data.userid);
                    $('#customerowenidmsg').text('客户负责人:'+data.username);
                    //$('#contacts').val(data.linkname);
                    $('#ssearchcontract').removeAttr("disabled");
                    $('#customername_display').val(data.accountname);//$('#related_to_display').val(data.accountname);
                    //$('#customeraddress').val(data.customeraddress);
                },error:function(){
                    Tips.alert({
                        content: 'error'
                    });
                }
            });
        }
        function select_id3(id, cid,username,userid,activecode,productid){
            $("#scontractid").val(cid);
            $("#scontractname_display").val(id);
            $("#activecode").val(activecode);
            $("#oldproductid").val(productid);
            $("#scontractowenid").val(userid);
            $("#scontractowenidmsg").text('原合同签订人:'+username);
        }
        function addList(){
            return '<div id="changedelted">\
                        <div class="form-group fix">\
                        <label>产品版本</label>\
                        <div class="input-box productadd">\
                            <select id="productid" name="productid" class="form-control" onchange="pi(this.options[this.options.selectedIndex].value);"  data-toggle="popover" data-placement="bottom" data-content="产品必选">\
                            <option value="" selected>请选择产品版本</option>\
                            </select>\
                         <input type="hidden" id="pipre" value="">\
                        </div>\
                </div>\
                <div class="form-group fix">\
                <label>升级日期</label>\
                <div class="input-box">\
                <input type="text" id="upgradedate" name="upgradedate" class="form-control" data-toggle="popover" data-placement="bottom" data-content="升级日期必填!">\
                </div>\
                </div>\
                </div>';
        }
        function pl(val){
        	var str = '';
        	if(val==''){
        	    return false;
            }
        	switch(val){
	    		case '1':
	    			str = '一年';
	    			break;
	    		case '2':
	    			str = '二年';
	    			break;
	    		case '3':
	    			str = '三年';
	    			break;
	    		case '4':
	    			str = '四年';
	    			break;
	    		case '5':
	    			str = '五年';
	    			break;
	    		case '6':
	    			str = '六年';
	    			break;
	    		case '7':
	    			str = '七年';
	    			break;
	    		case '8':
	    			str = '八年';
	    			break;
	    		case '9':
	    			str = '九年';
	    			break;
	    		case '10':
	    			str = '十年';
	    			break;
    		}
        	
        	Tips.confirm({
                content: '您选择了' + str + '的服务时间',
                define: '确定',
                cancel: '取消',
                before: function(){
                },
                after: function(b){
                	if(b){
                		$("#plpre").val(val);
                	}else{
                		$("#productlife").val($("#plpre").val());
                	}
                }
            });
        }
        function pci(val){
            var str = '';
            if(val==''){
                return false;
            }
            switch(val){
                case 'upgrade':
                    str = '升级';
                    break;
                case 'renewal':
                    str = '续费';
                    break;

            }
            Tips.confirm({
                content: '您选择' + str,
                define: '确定',
                cancel: '取消',
                before: function(){
                },
                after: function(b){
                    if(b){
                        $("#pcipre").val(val);
                        if(val=='upgrade'){
                            $('#productyear').after(addList());
                            var newjavascript={plugdatetime:function ($dateTxt,type,startdatetime){var curr = new Date();curr.setMonth(curr.getMonth()+1);var opt = {};opt.time = {preset : type}; opt.date = {preset : type};opt.datetime ={preset : type,minDate: startdatetime,maxDate: curr,stepMinute:5};$dateTxt.val('').scroller('destroy').scroller($.extend(opt[type],{theme: "android-ics light",mode: "scroller",display: "bottom",lang: "zh",startYear: startdatetime.getFullYear(),minDate:startdatetime,endYear: startdatetime.getFullYear() + 11,setText: '确定',cancelText: '取消',dateOrder: 'yymmdd',timeWheels: 'HHii',dateFormat: 'yy-mm-dd',timeFormat: 'HH:ii',monthText: "月",dayText: "日",yearText: "年", hourText: "时",minuteText: "分"}));}}
                            
                            var currentdatetime=new Date('<?php echo date('Y,m,d');?>
');
                            
                            newjavascript.plugdatetime($("#upgradedate"),"date",currentdatetime);
                            $('#dosave').val('确定升级');
                        }else{
                            $('#changedelted').remove();
                            $('#dosave').val('确定续费');
                        }
                        $('input[name="LoginName"]').val('');
                        $('#oldproductidName').val('');
                        $('#CloseDate').val('');
                        $('#productid').find('option').remove();


                    }else{
                        $("#classid").val($("#pcipre").val());
                    }

                }
            });
        }
        function pi(val){
        	var str = '';
            if(val==''){
                return false;
            }
        	switch(val){
	    		case 'fb01732e-4296-11e6-ad98-00155d069461':
	    			str = 'V';
	    			break;
	    		case 'fafdc07c-4296-11e6-ad98-00155d069461':
	    			str = 'V1';
	    			break;
	    		case 'fb016797-4296-11e6-ad98-00155d069461':
	    			str = 'V2';
	    			break;
	    		case 'fb016866-4296-11e6-ad98-00155d069461':
	    			str = 'V3';
	    			break;
	    		case 'eb472d25-f1b1-11e6-a335-5254003c6d38':
	    			str = 'V3 Plus';
	    			break;
	    		case 'fb0174bf-4296-11e6-ad98-00155d069461':
	    			str = 'V5';
	    			break;
	    		case 'ad0bee9e-516f-11e6-a2ff-52540013dadb':
	    			str = 'V6';
	    			break;
	    		case 'eb480f94-f1b1-11e6-a335-5254003c6d38':
	    			str = 'V8';
	    			break;
	    		case 'a36a9cac-516f-11e6-a2ff-52540013dadb':
	    			str = '发布宝';
	    			break;
    		}
        	Tips.confirm({
                content: '您选择' + str + '版本',
                define: '确定',
                cancel: '取消',
                before: function(){
                },
                after: function(b){
                	if(b){
                		$("#pipre").val(val);
                	}else{
                		$("#productid").val($("#pipre").val());
                	}
                }
            });
        }
 <?php echo '</script'; ?>
>


	<?php echo '<script'; ?>
 type="text/javascript">

			function check(){
                $('#customername_display').popover('destroy');
                if(''==$('#customerid').val()){
                    $('#customername_display').focus();
                    $("#customername_display").val('');
                    $("#customerid").val('');
                    $('#customername_display').popover('show');
                    $('.popover-content').css("color",'red');
                    setTimeout("$('#customername_display').popover('destroy')",2000);
                    return false;
                }
                $('#scontractid').popover('destroy');
                if(''==$('#scontractid').val()){
                    $('#scontractname_display').focus();
                    $("#scontractname_display").val('');
                    $("#scontractid").val('');
                    $('#scontractname_display').popover("show");
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#scontractname_display').popover('destroy')",2000);
                    return false;
                }
				$('#contractid').popover('destroy');
				if(''==$('#contractid').val()){
                    $('#contractname_display').focus();
                    $("#contractname_display").val('');
                    $("#contractid").val('');
                    $('#contractname_display').popover("show");
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#contractname_display').popover('destroy')",2000);
					return false;
				}

                if($('#contractowenid').val()!=$('#customerowenid').val() || $('#customerowenid').val()=='' || $('#contractowenid').val()==''){
                    Tips.alert({
                        content: '合同提单人与客户负责人不是同一人'
                    });
                    return false;
                }
                $('#usercode').popover('destroy');
                if(''==$('#usercode').val()){
                    $('#usercode').focus();
                    $('#usercode').popover('show');
                    setTimeout("$('#usercode').popover('destroy')",2000);
                    $('.popover-content').css("color",'red');
                    return false;
                }
                $('#productlife').popover('destroy');
                if(''==$('#productlife').val()){
                    $('#productlife').focus();
                    $('#productlife').popover('show');
                    setTimeout("$('#productlife').popover('destroy')",2000);
                    $('.popover-content').css("color",'red');
                    return false;
                }
                $('#productid').popover('destroy');
                if(''==$('#productid').val()){
                    $('#productid').focus();
                    $('#productid').popover('show');
                    setTimeout("$('#productid').popover('destroy')",2000);
                    $('.popover-content').css("color",'red');
                    return false;
                }
                $('#upgradedate').popover('destroy');
                if(''==$('#upgradedate').val()) {
                    $('#upgradedate').popover('show');
                    setTimeout("$('#upgradedate').popover('destroy')", 2000);
                    $('.popover-content').css("color", 'red');
                    return false;
                }
                /*if(''==$('#activecode').val()){
                    Tips.alert({
                        content: "原合同的激活码不存在,无法继续操作"
                    });
                    return false;
                }*/

				return true;
			}
			var dosaveflag=1;
            $('#dosave').on('click', function() {
	            if(!check()){
	                    return false;
                }
                if(dosaveflag!=1){
                    return false;
                }
                dosaveflag=2;
                $('#loading').show();
	            $('#dosave').removeAttr("id");//防止多次点击提交               
	            $('#myForm2').ajaxSubmit({
	                type: 'post', 
	                url:"/index.php?module=ActivationCode&action=doUpgradeARenew",
	                dataType :'json',
	                success: function(data) {
                        $('#loading').hide();
	                	if(data.success==1){
	                		Tips.alert({
	    					    content: '操作成功',
                                before: function(){
                                },
                                after: function(b){
                                    if(b){
                                        window.location.reload();
                                    }else{
                                    }
                                }
	    					});

	                	}else{
	                		Tips.alert({
	    					    content: data.msg,
                                before: function(){
                                },
                                after: function(b){
                                    if(b){
                                        window.location.reload();
                                    }else{
                                    }
                                }
	    					});
	                	}
	                },
	                error:function(){
                        $('#loading').hide();
	                	Tips.alert({
    					    content: 'error',
                            before: function(){
                            },
                            after: function(b){
                                if(b){
                                    window.location.reload();
                                }else{
                                }
                            }
    					});
	                }
	            });
	            return false;
            });

	<?php echo '</script'; ?>
>
   

<!--时间插件结束-->
<?php echo '<script'; ?>
>
    
    $(function () {
        var newjavascript={plugdatetime:function ($dateTxt,type,startdatetime){var curr = new Date();curr.setMonth(curr.getMonth()+1);var opt = {};opt.time = {preset : type}; opt.date = {preset : type};opt.datetime ={preset : type,minDate: startdatetime,maxDate: curr,stepMinute:5};$dateTxt.val('').scroller('destroy').scroller($.extend(opt[type],{theme: "android-ics light",mode: "scroller",display: "bottom",lang: "zh",setText: '确定',cancelText: '取消',dateOrder: 'yymmdd',timeWheels: 'HHii',startYear: startdatetime.getFullYear(),minDate:startdatetime,endYear: startdatetime.getFullYear() + 11,dateFormat: 'yy-mm-dd',timeFormat: 'HH:ii',monthText: "月",dayText: "日",yearText: "年", hourText: "时",minuteText: "分"}));}}
        
        var currentdatetime=new Date('<?php echo date('Y,m,d');?>
');
        
        newjavascript.plugdatetime($("#upgradedate"),"date",currentdatetime);
    })
    
<?php echo '</script'; ?>
>

<div id="loading" style="display: none;">
    <div id="loading-center">
        <div id="loading-center-absolute">
            <div class="object" id="object_one"style="background-color:green;"></div>
            <div class="object" id="object_two" style="left:20px;">理</div>
            <div class="object" id="object_three" style="left:40px;">处</div>
            <div class="object" id="object_four" style="left:60px;">在</div>
            <div class="object" id="object_five" style="left:80px;">正</div>
        </div>
    </div>
</div>
</body>
</html><?php }
}
