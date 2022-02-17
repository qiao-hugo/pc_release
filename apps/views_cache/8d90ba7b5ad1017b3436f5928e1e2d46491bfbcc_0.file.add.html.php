<?php
/* Smarty version 3.1.28-dev/79, created on 2018-03-30 10:59:36
  from "/data/httpd/vtigerCRM/apps/views/ActivationCode/add.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5abda81874d562_01813658',
  'file_dependency' => 
  array (
    '8d90ba7b5ad1017b3436f5928e1e2d46491bfbcc' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/ActivationCode/add.html',
      1 => 1522378722,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:qqmap.html' => 1,
  ),
),false)) {
function content_5abda81874d562_01813658 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
		<title>获取T云激活码</title>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		
        <link href="static/css/select2.css" rel="stylesheet" type="text/css" />
		<?php echo '<script'; ?>
 src="static/js/jquery.form.js?v=<?php echo $_smarty_tpl->tpl_vars['versionjs']->value;?>
"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="static/js/select2.js?v=<?php echo $_smarty_tpl->tpl_vars['versionjs']->value;?>
"><?php echo '</script'; ?>
>
         <?php echo '<script'; ?>
 src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"><?php echo '</script'; ?>
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
                    <label class="">合同编号</label>
                    <div class="input-box">
                    	<input type="hidden" id="contractid" name="contractid" value="" >
                    	<input type="hidden" id="contractowenid" value="">
                        <div class="input-group">
                           <input type="text" data-toggle="popover" data-placement="bottom" placeholder="建议输入合同编号后四位"
                                  data-content="合同编号不能为空" id="contractname_display"  name="contractname_display" class="form-control keyInput">
                           <span class="input-group-btn">
                              <input type="button" id='search1' class="btn btn-default" value="搜索">
                              <!-- <input type="button" id='scanQRCode' class="btn btn-default" value="扫一扫"> -->
                           </span>
                        </div>
                        <div>
                            <span style="color:red" id="contractowenidmsg"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>客户名称</label>
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
                    <label>代理商标识码</label>
                    <div class="input-box">
                        <input type="number" data-toggle="popover" data-placement="bottom" data-content="代理商标识码不能为空" id="agents" name="agents"  value="<?php echo $_smarty_tpl->tpl_vars['agents']->value;?>
" class="form-control"  readonly="true">
                    </div>
                </div>
                <div class="form-group fix">
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
                <div class="form-group fix">
                    <label>产品版本</label>
                    <div class="input-box">
                       <select id="productid" name="productid" class="form-control" onchange="pi(this.options[this.options.selectedIndex].value);"  data-toggle="popover" data-placement="bottom" data-content="产品必选">
							<option value='' selected>请选择产品版本</option>
							
							<option value='fb01732e-4296-11e6-ad98-00155d069461'>T-云系列V(首购)</option>
							<option value='fafdc07c-4296-11e6-ad98-00155d069461'>T-云系列V1(首购)</option>
							<option value='fb016797-4296-11e6-ad98-00155d069461'>T-云系列V2(首购)</option>
							<option value='fb016866-4296-11e6-ad98-00155d069461'>T-云系列V3(首购)</option>
							<option value='eb472d25-f1b1-11e6-a335-5254003c6d38'>T-云系列V3Plus(首购)</option>
							<option value='fb0174bf-4296-11e6-ad98-00155d069461'>T-云系列V5(首购)</option>
							<option value='b96c4ad7-27f3-4526-ab43-609d8dbd1170'>T-云系列V5Plus(首购)</option>
							<option value='ad0bee9e-516f-11e6-a2ff-52540013dadb'>T-云系列V6(首购)</option>
							<option value='eb480f94-f1b1-11e6-a335-5254003c6d38'>T-云系列V8(首购)</option>
							<option value='a36a9cac-516f-11e6-a2ff-52540013dadb'>发布宝(首购)</option>
							<option value='512cb5c8-7609-11e7-a335-5254003c6d38'>T-云系列S1(首购)</option>
							<option value='512cb5e6-7609-11e7-a335-5254003c6d38'>T-云系列S1Plus(首购)</option>
							<option value='512cb609-7609-11e7-a335-5254003c6d38'>T-云系列S2(首购)</option>
							<option value='da1832bc-bc86-459f-a14c-285b2f69e1d3'>T-云系列S3(首购)</option>
							<option value='9bb55818-37ba-49cc-9c5b-493b68a19c21'>小程序电商版（首购）</option>
							<option value='b9345acf-452d-4746-8533-4c59b6b02df8'>小程序电商（附件二旗舰服务版）</option>
							
							
							
						</select>
						<input type="hidden" id="pipre" value="">
                    </div>
                </div>
                 <div class="form-group fix">
                    <label>签到地址</label>
                    <div class="input-box">
                    <div class="input-group">
						<span class="input-group-btn">
                        	<input type="button" id='signdd' class="btn btn-default" onclick="mymodals();" value="签到">
                        </span>
                        <input type="text" data-toggle="popover" data-placement="bottom" data-content="签到地址不能为空" id="sign_address" name="sign_address" class="form-control">
                        <span class="input-group-btn">
                        	<input type="button" id='signe' class="btn btn-default" onclick="mymodal();" value="签到">
                        </span>
                    </div>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>客户手机号码</label>
                    <div class="input-box">
						<div class="input-group">
                        	<input type="number" data-toggle="popover" data-placement="bottom" data-content="客户手机号码不能为空" id="mobile" name="mobile" class="form-control">
                        	<span class="input-group-btn">
                        		<input type="button" class="btn btn-default" id="sendVerify" onclick="settime(this);" value="发送验证码">
                        	</span>
                        </div>
                        <div>
                        	<span style="color:red">激活码将与此手机号码绑定，请与客户确认。</span>
                        </div>
					</div>
                </div>
                <div class="form-group fix">
                    <label >手机验证码</label>
					<div class="input-box">
						<input type="hidden" id="isMobile" value="0">
                    	<div class="input-group">
                        	<input type="number" data-toggle="popover" data-placement="bottom" data-content="手机验证码不能为空" id="auth_code" name="auth_code" class="form-control" onkeyup="checkAuthCode();">	
                        	<span class="input-group-btn" style="font-size:10px;">
                        		<font id="isCorrect">&nbsp;</font>
                        	</span>
                        </div>
                    </div>
                </div>
                <div class="confirm tc">
                        <input type="button" id='dosave' class="btn" data-toggle="popover" data-placement="top" 
                        data-content="正在获取激活码,请稍等…" value="获取激活码">
                </div>
                <div class="confirm tc" id="showActiveCode"></div>
				
            </div>
        	</form>
            
        </div>
		
        
    </div>
    
    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
            	<div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">地址签到</h4>
                </div>
                <div class="modal-body">
                <div class="confirm tc">
                <input type="hidden" id="lang">
			    <input type="hidden" id="ress" >
				<div class="container-fluid" id="XS">
					<div>
						<div id="container" style="width:98%; height:400px"><?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:qqmap.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
</div>
					</div>
			        <div class="confirm tc confirm2">
			            <input  type="hidden" id="address" readonly  style="width: 100%; margin: auto;">
			        </div>
			        <input  id="position" type="hidden">
			        <div class="confirm tc confirm2" style="position: fixed;bottom: 45px;right: 0px;width: 104px;">
			            <button class="btn" id="sign" onclick="sign()">签到</button>
			        </div>
				
				</div>
				</div>
				</div>
            </div>
        </div>
    </div>
	<div class="modal fade" id="myModals">
        <div class="modal-dialog">
            <div class="modal-content">
            	<div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">地址签到</h4>
                </div>
                <div class="modal-body">
                <div class="confirm tc">
                
				<div class="container-fluid">
					<div>
						<div id="containers" style="width:98%; height:400px"></div>
					</div>
			      		        
			        <div class="confirm tc confirm2" style="position: fixed;bottom: 45px;right: 0px;width: 104px;">
			            <button class="btn" id="signdd" onclick="sign()">签到</button>
			        </div>
				
				</div>
				</div>
				</div>
            </div>
        </div>
    </div>
	
    
	<?php echo '<script'; ?>
 type="text/javascript">
	var Height = window.innerHeight;
	$('.rowFrame').css('height',Height+'px')
        window.addEventListener('message', function(event) {
            // 接收位置信息，用户选择确认位置点后选点组件会触发该事件，回传用户的位置信息
            var loc = event.data;
			if(loc.module=='locationPicker'){
				$('#address').val(loc.poiaddress);
				$('#position').val(loc.latlng.lng+'***'+loc.latlng.lat);
				//console.log('location', loc);
			}
        }, false);
		  function sign(){
	        if($('#address').val()&&$(position).val()){
	            $adname = $('#address').val();//获取地址
	            $adcode = $('#position').val();//获取位置
	            //$id = $('#record').val();
	            $("#sign_address").val($adname);
	            $('#myModal').modal('hide');
	            $('#myModals').modal('hide');
	          // console.log('index.php?module=VisitingOrder&action=dosign&id='+$id+'&adname='+$adname+'&adcode='+$adcode);
	            //window.location = 'index.php?module=VisitingOrder&action=dosign&id='+$id+'&adname='+$adname+'&adcode='+$adcode;
	        }else{
	            Tips.alert({
	                content: '地址不能为空'
	            });
	        }
    	}
	<?php echo '</script'; ?>
>

 <?php echo '<script'; ?>
>
 var countdown=60;
 function settime(obj){
		var mobile = $("#mobile").val();
		if(mobile == ''){
			Tips.alert({
			    content: '手机号码不能为空'
			});
			return false;
		}
		var reg = /^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
		if (!reg.test(mobile)) {
			Tips.alert({
			    content: '手机号码填写有误'
			});
		    return false;
		}
		if(countdown == 60){
			countdown = 59;
            obj.setAttribute("disabled", true);
            obj.setAttribute("class", 'btn btn-default');
			sendMobileVerify(mobile);
		}
		
		if(countdown == 0){
			obj.removeAttribute("disabled");
			obj.setAttribute("class", 'btn btn-default');
			obj.value="发送验证码";
			countdown = 60;
			return false;
		}else{
			obj.setAttribute("disabled", true);
			obj.setAttribute("class", 'btn btn-default');
			obj.value="重新发送(" + countdown + ")";
			countdown--;
		}
		setTimeout(function() {
			settime(obj) },1000);
		
	}
 
 	function sendMobileVerify(mobile){
 		$.ajax({
	         url: "/index.php?module=ActivationCode&action=ajaxGetMobileVerify&mobile="+mobile,
	         type: 'GET',
	         dataType: 'json',
	         success: function (data) {
	         	if(!data.success){
	         		Tips.alert({
	            	    content: data.msg,
	            	});
	         	}
	         }
	     });
 	}
	
 
         $(function () {
            // 下拉框加载
            $(".select2").select2({
                width: '100%',
                height: 100
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
                                 var nArr = item2.value;console.log(nArr)
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
                    $('#customername_display').val(data.accountname);//$('#related_to_display').val(data.accountname);
                    //$('#customeraddress').val(data.customeraddress);
                },error:function(){
                    Tips.alert({
                        content: 'error'
                    });
                }
            });
        }
        
        function checkAuthCode(){
        	var code = $("#auth_code").val();
        	var mobile = $("#mobile").val();
        	$.ajax({
                url: "/index.php?module=ActivationCode&action=checkAuthCode&code="+code,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if(data.success && mobile){
                        $("#isCorrect").html("√");
                        $("#isCorrect").css('color', 'green');
                        $("#isMobile").val('1');
                    }else{
                    	$("#isCorrect").html("×");
                        $("#isCorrect").css('color', 'red');
                        $("#isMobile").val('0');
                    }
                },error:function(){
                    $('#loading').hide();
                    Tips.alert({
                        content: 'error'
                    });
                }
            });
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
    
        function pi(val){
        	var str = '';
            if(val==''){
                return false;
            }
        	switch(val){
                case '512cb5c8-7609-11e7-a335-5254003c6d38':
                    str='S1';
                    break;
                case '512cb5e6-7609-11e7-a335-5254003c6d38':
                    str='S1Plus';
                    break;
                case '512cb609-7609-11e7-a335-5254003c6d38':
                    str='S2';
                    break;
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
				case 'b96c4ad7-27f3-4526-ab43-609d8dbd1170':
	    			str = 'V5 Plus';
	    			break;
	    		case 'fb0174bf-4296-11e6-ad98-00155d069461':
	    			str = 'V5';
	    			break;
				case 'da1832bc-bc86-459f-a14c-285b2f69e1d3':
	    			str = 'S3';
	    			break;
				case 'b9345acf-452d-4746-8533-4c59b6b02df8':
	    			str = '小程序电商旗舰版';
	    			break;
				case '9bb55818-37ba-49cc-9c5b-493b68a19c21':
	    			str = '小程序电商标准版';
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
				case 'c5f54cfc-36b5-11e7-a335-5254003c6d38':
                    str = 'X1';
                    break;
                case 'caa9b301-36b5-11e7-a335-5254003c6d38':
                    str = 'X2';
                    break;
				case 'c83cce8e-4993-11e7-a335-5254003c6d38':
                    str = 'V2双推';
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
                if($('#contractowenid').val()!=$('#customerowenid').val() || $('#customerowenid').val()=='' || $('#contractowenid').val()==''){
                    Tips.alert({
                        content: '合同提单人与客户负责人不是同一人'
                    });
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
                $('#agents').popover('destroy');
				if(''==$('#agents').val()){
                    $('#agents').focus();
                    $('#agents').popover('show');
                    $('.popover-content').css("color",'red');
                    setTimeout("$('#agents').popover('destroy')",2000);
					return false;
				}
                /*$('#sign_address').popover('destroy');
				if(''==$('#sign_address').val()){
                    $('#sign_address').focus();
                    $('#sign_address').popover('show');
                    setTimeout("$('#sign_address').popover('destroy')",2000);
                    $('.popover-content').css("color",'red');
					return false;
				}*/
				$('#mobile').popover('destroy');
				if(''==$('#mobile').val()){
                    $('#mobile').focus();
                    $('#mobile').popover('show');
                    setTimeout("$('#mobile').popover('destroy')",2000);
                    $('.popover-content').css("color",'red');
					return false;
				}
				
				$('#auth_code').popover('destroy');
				if(''==$('#auth_code').val()){
                    $('#auth_code').focus();
                    $('#auth_code').popover('show');
                    setTimeout("$('#auth_code').popover('destroy')",2000);
                    $('.popover-content').css("color",'red');
					return false;
				}
				if('0' == $("#isMobile").val()){
					Tips.alert({
					    content: '验证码不正确'
					});
					return false;
				}
				return true;
			}
			var dosaveflag=1;
            $('#dosave').on('click', function() {
	            if(!check()){
	                    return false;
	                }
                $('#loading').show();
	            $('#dosave').removeAttr("id");//防止多次点击提交  
				if(dosaveflag!=1){
					//防止多次提多;
					return false;
				}
				dosaveflag=2;
	            $('#myForm2').ajaxSubmit({
	                type: 'post', 
	                url:"/index.php?module=ActivationCode&action=ajaxGetSecreCode",
	                dataType :'json',
	                success: function(data) {
                        $('#loading').hide();
	                	if(data.success==1){
	                		Tips.alert({
	    					    content: '激活码已经与客户的手机号码绑定，激活时使用手机号码按操作指引即可',
								before: function(){
								},
								after: function(b){
									if(b){
										window.location.reload();
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
                                }
                            }
    					});
	                }
	            });
	            return false;
            });

			var cache = {};
		 
            //禁用juqery mobile 加载自带的样式
            $(document).bind("mobileinit", function(){
                $.mobile.page.prototype.options.keepNative = "select, input, textarea, a,div,ul,li,span,button,form";
            });
            $("input").attr('data-role','none');
            $("select").attr('data-role','none');
            $("a").attr('data-role','none');
            //禁止a标签ajax跳转
            $("a").attr('data-ajax','false');
            $("div,ul,li,span").attr('data-role','none');

	<?php echo '</script'; ?>
>
	
    
   

<?php echo '<script'; ?>
 type="text/javascript">

wx.config({
    debug: false,
    appId: "<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['appId'];?>
",
    timestamp: "<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['timestamp'];?>
",
    nonceStr: "<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['nonceStr'];?>
",
    signature: "<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['signature'];?>
",
    jsApiList: ['scanQRCode','getLocation']
});
   wx.ready(function () {
// 1 判断当前版本是否支持指定 JS 接口，支持批量判断

		/* wx.checkJsApi({
		  jsApiList: [
			'getNetworkType',
			'previewImage'
		  ],
		  success: function (res) {
			alert(JSON.stringify(res));
		  }
		}); */
		
		$("#scanQRCode").click(function(){
			wx.scanQRCode({
			    desc: 'scanQRCode desc',
			    needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
			    scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
			    success: function (res) {
			       // 回调
			       var result = res.resultStr;//当needResult 为 1 时，扫码返回的结果
			       $.ajax({
		                url: "/index.php?module=ActivationCode&action=findContractNo&id="+result,
		                type: 'GET',
		                dataType: 'json',
		                success: function (data) {
		                	if(data && data.length > 0){
		                		$("#contractid").val(data.contractNo.servicecontractsid);//把值设置到框内
			                	$("#contractname_display").val(data.contractNo.contract_no);//把值设置到框内
		                	}else{
		                		Tips.alert({
                            	    content: '该份合同可能不属于你',
                            	    define:'确定',
                            	    after:function(){
                            	    	$("#contractname_display").val('');
                            	    	$("#contractid").val('');
                            	    }
                            	});
		                	}
		                	
		                },
		                error: function(e){
		                	$("#contractid").val("");
		                	$("#contractname_display").val("");
		                	Tips.alert({
	            			    content: e
	            			});
		                }
		            });
			    },
			    error: function(res){
			          if(res.errMsg.indexOf('function_not_exist') > 0){
			               Tips.alert({
	            			    content: '版本过低请升级'
	            			});
			          }
			     }
			});
		});
		wx.getLocation({
			type: 'gcj02',
			success: function (res) {

				var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
				var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
				var speed = res.speed; // 速度，以米/每秒计
				var accuracy = res.accuracy; // 位置精度
				var markUrl = "http://apis.map.qq.com/tools/locpicker?search=0&mapdraggable=0&type=1&key=34IBZ-ZTRRG-WI4Q2-IYFXG-MBRUO-FEBVC&referer=myapp&coord="+latitude+','+longitude;
				console.log(markUrl);
				console.log(markUrl);
				$('#containers').append('<iframe id="mapPage1" width="100%" height="100%" frameborder=0 src="'+markUrl+'"></iframe>');
				//$('#address').val(loc.poiaddress);
				//$('#position').val(latitude+'***'+longitude);

			},
			error:function(res){
			},
			cancel: function (res) {
				alert('用户拒绝授权获取地理位置');
			}
		});
	 }
	);
	wx.error(function (res) {
	  Tips.alert({
		    content: res.errMsg
		});
	});
	
	function openurl(type){
		if(type=='3'){
            window.location.href='index.php?module=ActivationCode&action=sign';
        }else if(type=='4'){
            //window.location.href= 'index.php?module=ActivationCode&action=picture&id='+$('#selectaccount').val();
        }
	}
    function mymodal(){
        //$('#selectaccount').val(accountid);
        $('#myModal').modal();
    }
	function mymodals(){
        //$('#selectaccount').val(accountid);
        $('#myModals').modal();
    }
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
