<?php
/* Smarty version 3.1.28-dev/79, created on 2018-01-26 20:25:43
  from "/data/httpd/vtigerCRM/apps/views/VisitingOrder/add.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a6b1e470bbc65_52916922',
  'file_dependency' => 
  array (
    '1954801cc9ba029ada5e3ed021e4af851aa8d501' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/VisitingOrder/add.html',
      1 => 1516969157,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a6b1e470bbc65_52916922 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
		<title>添加拜访单</title>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		
		<link href="static/css/jquery.mobile-1.3.0.min.css" rel="stylesheet" type="text/css" />
		<link href="static/css/mobiscroll.custom-2.5.0.min.css" rel="stylesheet" type="text/css" />
        <link href="static/css/select2.css" rel="stylesheet" type="text/css" />
		<?php echo '<script'; ?>
 src="static/js/jquery.form.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="static/js/select2.js"><?php echo '</script'; ?>
>
    <style type="text/css">
        *{
            text-shadow:none;
        }
    </style>

</head>

<body>

<div class="container-fluid w fix">
        <div class="row">
           
            <form id='myForm2' onsubmit='return check()'  method="POST">
            <div class="add-visit">
              <!--  <div class="form-group fix">
                    <label>主题</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-placement="top" 
                        data-content="主题不能为空" type="text" placeholder=""  id="subject" name="subject"  class="form-control">
                    </div>
                </div>-->
                <div class="form-group fix">
                    <label>客户</label>
                    <div class="input-box">
                    	<!--input data-toggle="popover" data-placement="top" 
                        data-content="客户不能为空" class="form-control keyInput" type="text" id="related_to_display"  name="related_to_display" -->
						<input type="hidden" id="related_to" name="related_to" value="<?php echo $_smarty_tpl->tpl_vars['accountid']->value;?>
" >

                        <div class="input-group">
                           <input type="text" data-toggle="popover" data-placement="bottom"
                                  data-content="客户不能为空" id="related_to_display"  name="related_to_display" class="form-control keyInput">
                           <span class="input-group-btn">
                              <button id='search' class="btn btn-default" type="button">
                                 查询
                              </button>
                           </span>
                        </div>

                    </div>

                </div>
                <div class="form-group fix">
                    <label>客户地址</label>
                    <div class="input-box">
                        <input type="text" id="customeraddress" name="customeraddress" class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>联系人</label>
                    <div class="input-box">
                        <input type="text" id="contacts" name="contacts" class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label >目的地</label>
					<div class="input-box">
						<input type="text" id="destination" name="destination"  class="form-control">
					</div>
                </div>
                <div class="form-group fix">
                    <label>拜访目的</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-placement="top" 
                        data-content="拜访目的不能为空" type="text" id="purpose" name="purpose" class="form-control">
                    </div>
                </div>
                 <div class="form-group fix">
                    <label>陪同人</label>
                    <div class="input-box">

                        <select name="accompanyuser[]" id="accompanyuser" multiple="multiple" class="select2">
                            <?php
$_from = $_smarty_tpl->tpl_vars['deparment_user']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_i_0_saved_item = isset($_smarty_tpl->tpl_vars['i']) ? $_smarty_tpl->tpl_vars['i'] : false;
$__foreach_i_0_saved_key = isset($_smarty_tpl->tpl_vars['myId']) ? $_smarty_tpl->tpl_vars['myId'] : false;
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable();
$__foreach_i_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_i_0_total) {
$_smarty_tpl->tpl_vars['myId'] = new Smarty_Variable();
foreach ($_from as $_smarty_tpl->tpl_vars['myId']->value => $_smarty_tpl->tpl_vars['i']->value) {
$__foreach_i_0_saved_local_item = $_smarty_tpl->tpl_vars['i'];
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['i']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['i']->value['last_name'];?>
</option>
                            <?php
$_smarty_tpl->tpl_vars['i'] = $__foreach_i_0_saved_local_item;
}
}
if ($__foreach_i_0_saved_item) {
$_smarty_tpl->tpl_vars['i'] = $__foreach_i_0_saved_item;
}
if ($__foreach_i_0_saved_key) {
$_smarty_tpl->tpl_vars['myId'] = $__foreach_i_0_saved_key;
}
?>
                        </select>
                    </div>
                </div>
                <div class="form-group fix hide">
                    <label>外出类型</label>
                    <div class="input-box">
                        <select  id="outobjective" name="outobjective" class="form-control" >
							<option value='拜访' selected>拜访</option>
							<option value='会议'>会议</option>
							<option value='学习'>学习</option>
							<option value='出差'>出差</option>
						</select>
                    </div>
                </div>
                <div class="form-group fix hide">
                    <label>提单人</label>
                    <div class="input-box">
                        <input type="text" id="extractname" name="extractname" value='<?php echo $_smarty_tpl->tpl_vars['username']->value;?>
' readonly="readonly" class="form-control">
                    </div>
                </div>
                
                <div class="form-group fix">
                    <label>开始日期</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-placement="top" 
                        data-content="开始日期不能为空" type="text" id="startdate" name="startdate" placeholder="开始时间" readonly="readonly" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>结束日期</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-placement="top" 
                        data-content="结束日期不能为空" type="text" id="enddate" name="enddate" placeholder="结束时间" readonly="readonly" class="form-control"  value="">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>备注</label>
                    <div class="input-box">
                        <textarea class="form-control" id="remark" name="remark" rows="5"></textarea>
                    </div>
                </div>
                <div class="confirm tc">
                    <button id='dosave' class="btn" data-toggle="popover" data-placement="top" 
                        data-content="成功添加拜访单,正在跳转请稍等">保 存</button>
                </div>

            </div>
        	</form>
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        </div>
    </div>

 <?php echo '<script'; ?>
>
         $(function () {
            // 下拉框加载
            $(".select2").select2({
                width: '100%',
                height: 100
            });

            $('#search').on('click', function () {
                var o = $('#related_to_display');
                var ov = o.val();
                var op = o.parent();
                var sb = [];
                o.next('ul').remove();
                $('.delefalg').remove();
                var dheight=$(document).height();
                dheight=dheight*0.5;
                oul = op.append('<ul class="keyText delefalg" style="max-height:'+dheight+'px;overflow:auto;"></ul>');

                if (ov) {
                    op.addClass('keyBox');
                    $.ajax({
                        //url: '/index.php?module=Accounts&action=searchAccount&company='+ov,
                        url: '/index.php?module=VisitingOrder&action=searchAccount&company='+ov,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            if (data && data.length > 0) {
                                for (var i = 0;i<data.length; i++) {
                                     var item2=data[i];
                                     var nArr = item2.value;
                                     var oli=op.children('ul');
                                    oli.append("<li onclick='change_select_id("+item2.id+",\""+nArr+"\")'>" + nArr + '</li>');

                                }
                              
                            }
                        }
                    });
                }
            });
            /*
            $('#related_to_display').on('keyup', function () {
                var o = $(this);
                var ov = o.val();
                var op = o.parent();
                var sb = [];
                o.next('ul').remove();
                oul = op.append('<ul class="keyText"></ul>');

                if (ov) {
                    op.addClass('keyBox');
                    
                    $.ajax({
                        url: '/index.php?module=Accounts&action=searchAccount&company='+ov,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            
                            if (data && data.length > 0) {

                                for (var i = 0;i<data.length; i++) {

                                     var item2=data[i];
                                     var nArr = item2.value;
                                     var oli=op.children('ul');
                                    oli.append("<li onclick='select_id("+item2.id+")'>" + nArr + '</li>');
                                   
                                }
                              
                            }
                        }
                    });
                }
            });
        */
        });
        blankFixExt('keyInput', 'keyText');
        function blankFixExt(node, targetNode) {
            $(document).bind('click', function (e) {
                var o = "." + node + ",." + node + " *";
                
                if (!$(e.target).is(o)&& (e.target.id)!='search') {
                    $('.' + targetNode).hide();
                }
            });
         }
        function select_id(id){
            var idval = id;
            $.ajax({
                //url: "/index.php?module=Accounts&action=getAccountMsg&id="+id,
                url: "/index.php?module=VisitingOrder&action=getAccountMsg&id="+id,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if(data==''){
                        alert('客户信息不全');
                        return false;
                    }
                    //$('#destination').val(data.address);
                    $('#related_to').val(data.accountid);
                    $('#contacts').val(data.linkname);
                    //$('#related_to_display').val(data.accountname);
                    $('#customeraddress').val(data.customeraddress);
                }
            });

            
        }
         function change_select_id(id,value){
             var idval = id;
             $.ajax({
                 //url: "/index.php?module=Accounts&action=getAccountMsg&id="+id,
                 url: "/index.php?module=VisitingOrder&action=getAccountMsg&id="+id,
                 type: 'GET',
                 dataType: 'json',
                 success: function (data) {
                     if(data==''){
                         alert('客户信息不全');
                         return false;
                     }
                     $('#destination').val(data.address);
                     $('#related_to').val(data.accountid);
                     $('#contacts').val(data.linkname);
                     $('#related_to_display').val(value);
                     $('#customeraddress').val(data.customeraddress);
                 }
             });

            
        }

        $(document).ready(function(){
            $accountid=$('#related_to').val();
            if($accountid>0){
                select_id($accountid);
            }
        });

 <?php echo '</script'; ?>
>


	<?php echo '<script'; ?>
 type="text/javascript">

			function check(){
				 $('#subject').popover('destroy');
				if(''==$('#subject').val()){
                    $('#subject').focus();
                    $('#subject').popover("show");
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#subject').popover('destroy')",2000);
					return false;
				}
                /*$('#related_to_display').popover('destroy');
				if(''==$('#related_to').val()){
                    $('#related_to_display').focus();
                    $('#related_to_display').popover('show');
                    $('.popover-content').css("color",'red');
                    setTimeout("$('#related_to_display').popover('destroy')",2000);
					return false;
				}*/
                /*$('#purpose').popover('destroy');
                     if(''==$('#purpose').val()){
                     $('#purpose').focus();
                     $('#purpose').popover('show');
                     $('.popover-content').css("color",'red');
                     setTimeout("$('#related_to_display').popover('destroy')",2000);
                     return false;
                 }*/
                $('#startdate').popover('destroy');
				if(''==$('#startdate').val()){
                    $('#startdate').focus();
                    $('#startdate').popover('show');
                    $('.popover-content').css("color",'red');
                    setTimeout("$('#startdate').popover('destroy')",2000);
					return false;
				}
                $('#enddate').popover('destroy');
				if(''==$('#enddate').val()){
                    $('#enddate').focus();
                    $('#enddate').popover('show');
                    setTimeout("$('#enddate').popover('destroy')",2000);
                    $('.popover-content').css("color",'red');
					return false;
				}
                var startdatetimstamp=new Date($('#startdate').val().replace(/-/g,'/'));
                startdatetimstamp=Date.parse(startdatetimstamp);
                var enddatetimstamp=new Date($('#enddate').val().replace(/-/g,'/'));
                enddatetimstamp=Date.parse(enddatetimstamp);
                var flagtime=1800000;
                if(enddatetimstamp-startdatetimstamp<flagtime){
                    $('#enddate').attr('data-content','开始时间大于结束时间或小于半小时');
                    $('#enddate').popover('show');
                    $('.popover-content').css("color",'red');
                    setTimeout("$('#enddate').popover('destroy')",2000);
                    return false;
                }
				
				return true;
			}
            $('#dosave').on('click', function() {
            if(!check()){
                    return false;
                }  
            $('#dosave').removeAttr("id");//防止多次点击提交               
            $('#myForm2').ajaxSubmit({
                type: 'post', 
                url:"/index.php?module=VisitingOrder&action=doadd",
                dataType :'json',
                success: function(data) { 
                    if(data.res=='success'){
 						window.location.href='/index.php?module=VisitingOrder&action=allList';
                        $('#dosave').popover('show');
                        //提交后在没有跟转新页面前不让再点击
                        $('#dosave').removeAttr("id");
                        $('#myForm2').resetForm();
                    }else{
                        alert('error01');
                    }
                },
                error:function(){
                    alert('error');
                }
            });
            return false;
            });

			var cache = {};
		  /*
			$( "#related_to_display" ).autocomplete({
	            	source: function( request, response ) {
	            		var term = request.term;
		                if ( term in cache ) {
		                    response( $.map( cache[ term ], function( item ) {
		                        return {
			                                Id:item.id,
			                                value:item.value
			                       		}
		                    }));
		                    return;
		                }
	                	$.ajax({
			                    url: "/index.php?module=Accounts&action=searchAccount",
			                    dataType: "json",
			                    data:{
			                        company: request.term
			                    },
			                    success: function( data ) {
			                        response( $.map( data, function( item ) {
			                            return {
			                                Id:item.id,
			                                value:item.value
			                            }
			                        }));
			                    }
	                  });
	            },
	            minLength: 2,
	            max:5,
	            select: function( event, ui ) {	            	
	            	var idval = ui.item.Id;
	                $.post("/index.php?module=Accounts&action=getAccountMsg",
						  {
						    id:idval
						  },
						  function(data,status){

						  	var val = eval('(' + data + ')');

						    $('#destination').val(val.address);
						    $('#related_to').val(val.accountid);
						    $('#contacts').val(val.linkname);

						  });
	            }
	        });
			*/
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
 src="static/js/bootstrap-datetimepicker.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="static/js/bootstrap-datetimepicker.zh-CN.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="static/js/moment.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="static/js/daterangepicker.js"><?php echo '</script'; ?>
>

    <link href="static/css/datetimepicker-min.css" rel="stylesheet" />
	<?php echo '<script'; ?>
 src="static/js/jquery.mobile-1.3.0.min.js"><?php echo '</script'; ?>
>
<!--插件和原来的样式有点冲突重新加载一遍-->
<?php echo '<script'; ?>
 src="static/js/bootstrap.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="static/js/mobiscroll.js"><?php echo '</script'; ?>
>
    <!--时间插件结束-->
    <?php echo '<script'; ?>
>
        $(function () {
            // 仅选择日期
            /*$(".reservation").datetimepicker({
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0,
                showMeridian: 1,
                format: "yyyy-mm-dd hh:ii"
            });*/
			var newjavascript={plugdatetime:function ($dateTxt,type,startdatetime){var curr = new Date();curr.setMonth(curr.getMonth()+1);var opt = {};opt.time = {preset : type}; opt.date = {preset : type};opt.datetime ={preset : type,minDate: startdatetime,maxDate: curr,stepMinute:5};$dateTxt.val('').scroller('destroy').scroller($.extend(opt[type],{theme: "android-ics light",mode: "scroller",display: "bottom",lang: "zh",setText: '确定',cancelText: '取消',dateOrder: 'yymmdd',timeWheels: 'HHii',dateFormat: 'yy-mm-dd',timeFormat: 'HH:ii',monthText: "月",dayText: "日",yearText: "年", hourText: "时",minuteText: "分"}));}}
            var currentdatetime=new Date();
            newjavascript.plugdatetime($("#startdate"),"datetime",currentdatetime);
            var endcurrentdatetime= new Date();
            endcurrentdatetime.setMinutes(endcurrentdatetime.getMinutes()+30)
            newjavascript.plugdatetime($("#enddate"),"datetime",endcurrentdatetime);
            
            //给一个默认的值插件加载时把它清空了这里重新给一下
            //$("#startdate").val('<?php echo date('Y-m-d H:i');?>
');
            //$("#enddate").val('<?php echo date('Y-m-d H:i',time()+1800);?>
');
            
        })
    <?php echo '</script'; ?>
>

</body>
</html><?php }
}
