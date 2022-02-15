<?php
/* Smarty version 3.1.28-dev/79, created on 2018-01-26 20:25:07
  from "/data/httpd/vtigerCRM/apps/views/VisitingOrder/alllist.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a6b1e23b33f55_96829026',
  'file_dependency' => 
  array (
    'b5c39b8a0d02be3db9fe7624053bb3458c87c719' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/VisitingOrder/alllist.html',
      1 => 1516969485,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a6b1e23b33f55_96829026 ($_smarty_tpl) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
		<title>查看拜访订单</title>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <link href="static/css/mobiscroll.custom-2.5.0.min.css" rel="stylesheet" type="text/css" />
    <?php echo '<script'; ?>
 src="static/js/jquery.mobile-1.4.5.min.js"><?php echo '</script'; ?>
>
    <!--插件和原来的样式有点冲突重新加载一遍-->
    <?php echo '<script'; ?>
 src="static/js/bootstrap.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="static/js/mobiscroll.js"><?php echo '</script'; ?>
>
	<link href="static/css/select2c.css" rel="stylesheet" type="text/css" />
    <?php echo '<script'; ?>
 src="static/js/select2c.js?v=<?php echo $_smarty_tpl->tpl_vars['versionjs']->value;?>
"><?php echo '</script'; ?>
>
   
    <style type="text/css">
        *{
            text-shadow:none;
        }
		.select2 {
            width:100%;
            height:35px;
        }
        .headon{
            border-bottom: 2px solid #535282;
        }
    </style>
</head>

<body>
<div class="container-fluid w fix see-visit-list">
        <div class="row">
            <div class="list-head">
                <ul class="fix">
                    <li <?php if ($_smarty_tpl->tpl_vars['modulestatus']->value == '0') {?> class="headon"<?php }?> style="width:20%;"><span onclick='openurl(7)' data-ajax="false">所有的</span></li>
                    <li <?php if ($_smarty_tpl->tpl_vars['modulestatus']->value == '1') {?> class="headon"<?php }?> style="width:20%;"><span onclick='openurl(8)' data-ajax="false">待<span style="color:#f00;">我</span>审</li>
                    <li <?php if ($_smarty_tpl->tpl_vars['modulestatus']->value == '2') {?> class="headon"<?php }?> style="width:20%;"><span onclick='openurl(9)' data-ajax="false">待审核</li>
                    <li <?php if ($_smarty_tpl->tpl_vars['modulestatus']->value == '3') {?> class="headon"<?php }?> style="width:20%;"><span onclick='openurl(10)' data-ajax="false">已审核</span></li>
                    <li style="width:20%;"><span onclick='openurl(1)' data-ajax="false">添加</span></li>
				</ul>
            </div>
            <div class="tabs">
                <div class="form-group fix" style="background-color:#e8ecf8;">
                    <ul class="hd fix" style="padding: 8px 2px;">
                        <li style="font-size: 16px;" class="<?php if ($_smarty_tpl->tpl_vars['searchfilename']->value == 'accountname') {?>on <?php }?>searchfield" data-id="accountname">客户名称</li>
                        <li style="font-size: 16px;" class="<?php if ($_smarty_tpl->tpl_vars['searchfilename']->value == 'extractid') {?>on <?php }?>searchfield" data-id="extractid">负责人</li>
                        <li style="font-size: 16px;" class="<?php if ($_smarty_tpl->tpl_vars['searchfilename']->value == 'startdate') {?>on <?php }?>searchfield" data-id="startdate">开始时间</li>
                    </ul>
                    <form method="post" action="/index.php?module=VisitingOrder&action=allList" data-ajax="false" data-role="none">
                        <input type="hidden" id="searchfilename" name="searchfilename" value="<?php echo $_smarty_tpl->tpl_vars['searchfilename']->value;?>
" />
                        <input type="hidden" id="oldsearchvalue" value="<?php echo $_smarty_tpl->tpl_vars['searchvalue']->value;?>
" />
                        <div class="input-box"style="margin:5px 8px;display: none;">
                            <select name="modulestatus" style="width:100%;height:35px;">
                                <option value="0" <?php if ($_smarty_tpl->tpl_vars['modulestatus']->value == '0') {?> selected<?php }?>>所有的</option>
                                <option value="1" <?php if ($_smarty_tpl->tpl_vars['modulestatus']->value == '1') {?> selected<?php }?>>待我审</option>
                                <option value="2" <?php if ($_smarty_tpl->tpl_vars['modulestatus']->value == '2') {?> selected<?php }?>>待审核</option>
                                <option value="3" <?php if ($_smarty_tpl->tpl_vars['modulestatus']->value == '3') {?> selected<?php }?>>已审核</option>
                            </select>
                        </div>
                        <div class="input-box" id="searchcontent" style="margin-left:8px;margin-right:8px;">
                            <?php if ($_smarty_tpl->tpl_vars['searchfilename']->value == 'startdate') {?><input type="text" id="startdate" name="searchvalue" placeholder="开始时间" readonly="readonly" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['searchvalue']->value;?>
"/>
                            <?php } elseif ($_smarty_tpl->tpl_vars['searchfilename']->value == 'accountname') {?>
                            <input type="text" id="accountname" name="searchvalue" class="form-control" placeholder="请输入要查找的信息" value="<?php echo $_smarty_tpl->tpl_vars['searchvalue']->value;?>
" />
                            <?php }?>
                        </div>
                        <div class="confirm tc" style="padding:10px 0;">
                            <button id='dosave' class="btn" style="width: 100%; padding: 5px 2px;font-size: 18px;border-radius: 2px;">查&nbsp;&nbsp;&nbsp;&nbsp;找</button>
                        </div>
                    </form>
                </div>
                <div class="bd" style="padding: 0;">
                    <ul class="ttt_list">
                        <?php if (!empty($_smarty_tpl->tpl_vars['list']->value)) {?>
                    	<?php
$_from = $_smarty_tpl->tpl_vars['list']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_0_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_0_total) {
$__foreach_value_0_first = true;
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->first = $__foreach_value_0_first;
$__foreach_value_0_first = false;
$__foreach_value_0_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                        <?php $_smarty_tpl->tpl_vars['IMGMD'] = new Smarty_Variable(md5($_smarty_tpl->tpl_vars['value']->value['email']), null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'IMGMD', 0);?>
                    	 <li class="fix" style="border-bottom: 1px solid #ccc;<?php if ($_smarty_tpl->tpl_vars['value']->first) {?>border-top: 1px solid #ccc;<?php }?>padding:5px 10px;margin-bottom: 0;position: relative;">
                             
                            <a href="index.php?module=VisitingOrder&action=detail&record=<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" data-ajax="false" class="fl">
                                <div style="width:60px;height: 60px;display: inline-block;border: 1px solid #ccc;border-radius: 60px;margin-right:3px;overflow: hidden;"><img src="<?php if (isset($_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value])) {
echo $_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value];
} else { ?>../../static/img/trueland.png<?php }?>" style="width:59px;height:59px;vertical-align: inherit;"></div>
                                <div class="content" style="display: inline-block;white-space: nowrap;font-size:18px;overflow: hidden;text-overflow:ellipsis;">
                                <div class="list" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo $_smarty_tpl->tpl_vars['value']->value['accountnamer'];?>
 <span>[<?php echo $_smarty_tpl->tpl_vars['value']->value['contacts'];?>
]</span></div>
                                <div class="list" style="font-size: 14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">主题：<?php echo $_smarty_tpl->tpl_vars['value']->value['subject'];?>
 </div>
                                <div class="text" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <div class="mr20"><?php echo $_smarty_tpl->tpl_vars['value']->value['startdate'];?>
</div><div><?php echo $_smarty_tpl->tpl_vars['value']->value['outobjective'];?>
</div><div style="margin-left:10px;"><?php if ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_complete') {?><span class="label label-primary">完成</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'a_normal') {?><span class="label label-info">正常</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'a_exception') {?><span class="label label-danger">打回中</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_canceling') {?><span class="label label-default">作废中</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_cancel') {?><span class="label label-warning">作废</span><?php }?></div>
                                </div>
                                </div>
                            </a>
                             <?php if ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_complete' || $_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'a_normal') {?><div class="fr right" style="position: absolute;top:22%;right:10px;" data-toggle="modal"  onclick=mymodal("<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
")>+</div><?php }?>
                        </li>

						<?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_local_item;
}
}
if ($__foreach_value_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_item;
}
?>
                        <?php } else { ?>
                        没有拜访单
                        <?php }?>
                    </ul>
                    <input type="hidden" value='' class="accountid">
                </div>
            </div>
            <div id="loading" data-id="1" data-flag="2" class="loading" style="text-align: center;"></div>
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        </div>
    </div>
    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">选择</h4>
                </div>
                <div class="modal-body">
                    <div class="confirm tc">
                        <input type="hidden" id="selectaccount">
                        <button class="btn btn1" onclick='openurl(3)'>地点签到</button>
                        <button class="btn btn1" onclick='openurl(5)'>地点签到</button>
						<button class="btn btn1" onclick='openurl(6)'>跟进</button>
                        <!--<button class="btn btn1" onclick='openurl(4)'>图片签到</button>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo '<script'; ?>
>
   		function openurl(type){
   			if(type==1){
   				window.location.href='index.php?module=VisitingOrder&action=add';
   			}else if(type=='3'){
                if($('#selectaccount').val()){
                    window.location.href='index.php?module=VisitingOrder&action=sign&id='+$('#selectaccount').val();
                }else{
                    alert('拜访单信息有误，请刷新后重试');
                }
            }else if(type=='4'){
                    window.location.href= 'index.php?module=VisitingOrder&action=picture&id='+$('#selectaccount').val();
            }else if(type=='5'){
                    window.location.href= 'index.php?module=VisitingOrder&action=signs&id='+$('#selectaccount').val();
            }else if(type=='6'){
                window.location.href= 'index.php?module=VisitingOrder&action=vaddmod&id='+$('#selectaccount').val();
            }else if(type=='7'){
                window.location.href= '/index.php?module=VisitingOrder&action=allList&modulestatus=0';
            }else if(type=='8'){
                window.location.href= '/index.php?module=VisitingOrder&action=allList&modulestatus=1';
            }else if(type=='9'){
                window.location.href= '/index.php?module=VisitingOrder&action=allList&modulestatus=2';
            }else if(type=='10'){
                window.location.href= '/index.php?module=VisitingOrder&action=allList&modulestatus=3';
            }
   		}
        function mymodal(accountid){
            $('#selectaccount').val(accountid);
            $('#myModal').modal();
        }
   	<?php echo '</script'; ?>
>

    <?php echo '<script'; ?>
 type="text/javascript">
    
    $(function(){
    
    var totalnum=<?php echo $_smarty_tpl->tpl_vars['sum']->value;?>
;
    var userimg=<?php echo json_encode($_smarty_tpl->tpl_vars['USERIMGS']->value);?>
;
    var userselect='<?php echo $_smarty_tpl->tpl_vars['userselect']->value;?>
';

    
        if($('#searchfilename').val()=='extractid') {
            $('#searchcontent').html('');
            $('#searchcontent').html(userselect);
            $(".select2").select2({
                width: '100%',
                height: 100
            });
        }
        if($('#searchfilename').val()=='startdate') {
            var newjavascript={plugdatetime:function ($dateTxt,type,startdatetime){var curr = new Date();curr.setMonth(curr.getMonth()+1);var opt = {};opt.time = {preset : type}; opt.date = {preset : type};opt.datetime ={preset : type,minDate: startdatetime,maxDate: curr,stepMinute:5};$dateTxt.val($dateTxt.val()).scroller('destroy').scroller($.extend(opt[type],{theme: "android-ics light",mode: "scroller",display: "bottom",lang: "zh",setText: '确定',cancelText: '取消',dateOrder: 'yymmdd',timeWheels: 'HHii',dateFormat: 'yyyy-mm-dd',timeFormat: 'HH:ii',monthText: "月",dayText: "日",yearText: "年", hourText: "时",minuteText: "分"}));}}
            newjavascript.plugdatetime($("#startdate"),"date");
        }
        //禁用juqery mobile 加载自带的样式
        $(document).bind("mobileinit", function(){
            $.mobile.page.prototype.options.keepNative = "select input, textarea, a,div,ul,li,span,button,form";
        });
        $("input").attr('data-role','none');
        $("select").attr('data-role','none');
        $("a").attr('data-role','none');
        //禁止a标签ajax跳转
        $("a").attr('data-ajax','false');
        $("div,ul,li,span").attr('data-role','none');
    //$('html,body').animate({scrollTop:0}, 'slow');
    var $num = 2;//当前的页码
    $(window).scroll(function(){
        //此方法是在滚动条滚动时发生的函数
        // 当滚动到最底部以上100像素时，加载新内容
        var $doc_height,$s_top,$now_height,dataid,dataflag;
        $doc_height = $(document).height();        //这里是document的整个高度
        $s_top = $(this).scrollTop();            //当前滚动条离最顶上多少高度
        $now_height = $(this).height();            //这里的this 也是就是window对象
        dataid=$('#loading').data("id");//阻止一次请求没有完成后再次请
        dataflag=$('#loading').data("flag");//阻止当滚地到底部时刷新后自动请求
        if(($doc_height - $s_top - $now_height) < 100&& $num<=totalnum&&dataid==1&&dataflag==1) jsonajax();
        $('#loading').data("flag",1);
        if($num>totalnum)$('#loading').html("没有了").show();
    });
    $('.searchfield').on('click',function(){
        var hason=$(this).hasClass('on');
        if(!hason){
            $('.searchfield').removeClass('on');
            $(this).addClass('on');
            var dataid=$(this).data('id');
            $('#searchfilename').val(dataid);
            if(dataid=='startdate'){
                $('#searchcontent').html('');
                $('#searchcontent').html('<input type="text" id="startdate" name="searchvalue" placeholder="开始时间" readonly="readonly" class="form-control" value="">');
                var newjavascript={plugdatetime:function ($dateTxt,type,startdatetime){var curr = new Date();curr.setMonth(curr.getMonth()+1);var opt = {};opt.time = {preset : type}; opt.date = {preset : type};opt.datetime ={preset : type,minDate: startdatetime,maxDate: curr,stepMinute:5};$dateTxt.val('').scroller('destroy').scroller($.extend(opt[type],{theme: "android-ics light",mode: "scroller",display: "bottom",lang: "zh",setText: '确定',cancelText: '取消',dateOrder: 'yymmdd',timeWheels: 'HHii',dateFormat: 'yy-mm-dd',timeFormat: 'HH:ii',monthText: "月",dayText: "日",yearText: "年", hourText: "时",minuteText: "分"}));}}
                newjavascript.plugdatetime($("#startdate"),"date");
            }else if(dataid=='accountname'){
                $('#searchcontent').html('');
                $('#searchcontent').html('<input type="text" id="accountname" name="searchvalue" class="form-control" placeholder="请输入要查找的信息">');
            }else if(dataid=='extractid'){
                $('#searchcontent').html('');
                $('#searchcontent').html(userselect);
                $(".select2").select2({
                    width: '100%',
                    height: 100
                });
            }
        }
    });
	var ulWidth=$(".ttt_list").width();
    var contentWith=ulWidth-120;
    $('.content').css('width',contentWith);
    function jsonajax(){
        $('#loading').data("id",2);
        $('#loading').html("正在加载请稍后...");
        var searchfilename=$('#searchfilename').val();
        var oldsearchvalue=$('#oldsearchvalue').val();
        var modulestatus=$('select[name="modulestatus"]').val();
        $.ajax({
            url:'index.php?module=VisitingOrder&action=doallList',
            type:'POST',
            data:{"pagenum":$num++,"searchfilename":searchfilename,"searchvalue":oldsearchvalue,"modulestatus":modulestatus},
            dataType:'json',
            success:function(json){
                if(typeof json == 'object'){
                    var neirou,$row,iheight,temp_h;
                    var tttt = '';
                    var item='';
                    for(var i=0,l=json.length;i<l;i++){
                        var modulestatus=false;
                        if (json[i]['modulestatus'] == 'c_complete') {
                            tttt = '<span class="label label-primary">完成</span>';
                            modulestatus=true;
                        } else if(json[i]['modulestatus'] == 'a_normal') {
                            tttt = '<span class="label label-info">正常</span>';
                            modulestatus=true;
                        } else if(json[i]['modulestatus'] == 'a_exception') {
                            tttt = '<span class="label label-danger">打回中</span>';
                        } else if(json[i]['modulestatus'] == 'c_canceling') {
                            tttt = '<span class="label label-default">作废中</span>';
                        }else if(json[i]['modulestatus'] == 'c_cancel') {
                            tttt = '<span class="label label-warning">作废</span>';
                        }
                        var modulestatusstring=modulestatus?'<div class="fr right"  style="position: absolute;top:22%;right:10px;" data-toggle="modal"  data-ajax="false" onclick=mymodal("'+json[i]['id']+'")>+</div>':'';
                        item += '<li class="fix"  style="border-bottom: 1px solid #ccc;padding:5px 10px;margin-bottom: 0;position: relative;" data-ajax="false">' +
                            '<a href="index.php?module=VisitingOrder&action=detail&record='+json[i]['id']+'" data-ajax="false" data-role="none" class="fl"><div style="width:60px;height: 60px;display: inline-block;border: 1px solid #ccc;border-radius: 60px;margin-right:3px;overflow: hidden;"><img src="'+(userimg[json[i]['email']]!=undefined?userimg[json[i]['email']]:'/static/img/trueland.png')+'" style="width:59px;height:59px;vertical-align: inherit;"></div>'+
                            '<div style="display: inline-block;overflow: hidden;width: '+contentWith+'px;white-space: nowrap;text-overflow:ellipsis;">'+
                                '<div class="list"  style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width: '+(contentWith-2)+'px;">'+json[i]['accountnamer']+' <span>['+json[i]['contacts']+']</span></div>'+
                            '<div class="list" style="font-size: 14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width: '+(contentWith-2)+'px;">主题：'+json[i]['subject']+' </div>'+
                            '<div class="text" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width: '+(contentWith-2)+'px;">'+
                                    '<div class="mr20">'+json[i]['startdate']+'</div><div>'+json[i]['outobjective']+'</div><div style="margin-left:10px;">'+ tttt +'</div>'+
                                '</div></a>'+modulestatusstring+'</li>';
                    }
                    $('#loading').hide();
                    $('.ttt_list').append(item);
					$('.list').css('text-overflow','ellipsis');
                    $('#loading').data("id",1);
                }
            }
        });
    }

    });
    
    <?php echo '</script'; ?>
>

</body>
</html><?php }
}
