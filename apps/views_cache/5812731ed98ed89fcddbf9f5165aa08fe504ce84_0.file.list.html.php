<?php
/* Smarty version 3.1.28-dev/79, created on 2018-01-26 20:19:43
  from "/data/httpd/vtigerCRM/apps/views/VisitingOrder/list.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a6b1cdfdccba4_52933777',
  'file_dependency' => 
  array (
    '5812731ed98ed89fcddbf9f5165aa08fe504ce84' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/VisitingOrder/list.html',
      1 => 1516969159,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a6b1cdfdccba4_52933777 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html>
<head>
	<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</head>

<body>
<div class="container-fluid w fix">
        <div class="row">
            
            <div class="list-head">
                <ul class="fix">
                    <li><?php echo $_smarty_tpl->tpl_vars['dateinfo']->value['date'];?>
</li>
                    <li>星期<?php echo $_smarty_tpl->tpl_vars['dateinfo']->value['week'];?>
</li>
                    <li><?php echo $_smarty_tpl->tpl_vars['dateinfo']->value['apm'];?>
</li>
                </ul>
            </div>
            <div class="tabs">
                <ul class="hd fix">
                    <li >今日拜访</li>
                    <li class="on">我的回款</li>
                    <li>一周掉公海</li>
                </ul>
                <div class="bd">
                    <ul class="hide module_visiting_orders">
	                    <?php
$_from = $_smarty_tpl->tpl_vars['today_list']->value;
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
" class="fl" style="overflow:hidden;width:90%;">
                                    <div style="width:60px;height: 60px;display: inline-block;border: 1px solid #ccc;border-radius: 60px;margin-right:3px;overflow: hidden;"><img src="<?php if (isset($_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value])) {
echo $_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value];
} else { ?>../../static/img/trueland.png<?php }?>" style="width:59px;height:59px;vertical-align: inherit;"></div>
                                    <div style="display: inline-block;width: 70%;white-space: nowrap;font-size:16px;text-overflow:ellipsis;">
                                        <div class="list" style="font-size:16px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo $_smarty_tpl->tpl_vars['value']->value['accountnamer'];?>
 <span>[<?php echo $_smarty_tpl->tpl_vars['value']->value['contacts'];?>
]</span></div>
                                        <div class="list" style="font-size: 14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">主题：<?php echo $_smarty_tpl->tpl_vars['value']->value['subject'];?>
</div>
                                        <div class="text">
                                            <div class="mr20"><?php echo $_smarty_tpl->tpl_vars['value']->value['startdate'];?>
</div><div><?php echo $_smarty_tpl->tpl_vars['value']->value['outobjective'];?>
</div><div style="margin-left:10px;"><?php if ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_complete') {?><span class="label label-primary">完成</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'a_normal') {?><span class="label label-info">正常</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'a_exception') {?><span class="label label-danger">打回中</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_canceling') {?><span class="label label-default">作废中</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_cancel') {?><span class="label label-warning">作废</span><?php }?></div>
                                        </div>
                                    </div>
                                </a>
                                <?php if ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_complete' || $_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'a_normal') {?><div class="fr right" style="position: absolute;top:22%;right:10px;" data-toggle="modal"  data-target="#myModal"  onclick='opendl(<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['value']->value['related_to_reference'];?>
)'>+</div><?php }?>
                        </li>
						<?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_local_item;
}
} else {
?>
								今日无拜访单
						<?php
}
if ($__foreach_value_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_item;
}
?>
                    </ul>
                    <ul class=" module_my_payment">
                        <table width="100%">
                        </table>
                        <?php
$_from = $_smarty_tpl->tpl_vars['my_payment']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_1_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_1_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_1_total) {
$__foreach_value_1_first = true;
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->first = $__foreach_value_1_first;
$__foreach_value_1_first = false;
$__foreach_value_1_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                            <li class="fix">
                                <div class="list"><?php echo $_smarty_tpl->tpl_vars['value']->value['label'];?>
</div>
                                <div class="text">
                                    <div class="mr20">金额：<?php echo $_smarty_tpl->tpl_vars['value']->value['price'];?>
</div><div>回款时间：<?php echo $_smarty_tpl->tpl_vars['value']->value['reality_date'];?>
</div>
                                </div>
                            </li>
                        <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_1_saved_local_item;
}
} else {
?>
                                暂无回款信息
                        <?php
}
if ($__foreach_value_1_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_1_saved_item;
}
?>
                    </ul>
                    <ul class="hide module_my_account">
                    	<?php
$_from = $_smarty_tpl->tpl_vars['list']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_2_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_2_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_2_total) {
$__foreach_value_2_first = true;
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->first = $__foreach_value_2_first;
$__foreach_value_2_first = false;
$__foreach_value_2_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                    	<li class="fix">
                            <a href="#" class="fl">
                                <div class="list"><?php echo $_smarty_tpl->tpl_vars['value']->value['accountname'];?>
</div>
                            </a>
                             <div class="fr right" data-toggle="modal" data-target="#myModal2" onclick='opendl(<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['value']->value['accountid'];?>
)'>+</div>
                        </li>
						<?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_2_saved_local_item;
}
} else {
?>
								无7日内掉入公海客户
						<?php
}
if ($__foreach_value_2_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_2_saved_item;
}
?>
                    </ul>


                    <div id="loading" data-id="1" data-flag="2" class="loading" style="text-align: center; clear:both;">
                    </div>

                </div>

            </div>

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
                        <!--<button class="btn btn1" onclick='openurl(1)'>添加跟进</button>
                        <button class="btn btn1" onclick='openurl(2)'>添加联系人</button>-->
                        <button class="btn btn1" onclick='openurl(3)'>地点签到</button>
                        <button class="btn btn1" onclick='openurl(4)'>地点签到</button>
                        <button class="btn btn1" onclick='openurl(5)'>跟进</button>
                        <!--<button class="btn btn1" onclick='openurl(4)'>图片签到</button>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">选择</h4>
            </div>
            <div class="modal-body">
                <div class="confirm tc">
                    <button class="btn btn1" onclick='openurl(1)'>添加跟进</button>
                    <button class="btn btn1" onclick='openurl(2)'>添加联系人</button>
                    <button class="btn btn1" onclick='openurl(6)'>添加拜访单</button>
                </div>
            </div>
        </div>
    </div>
</div>
    <input type='hidden' id = 'accountid' value='0'>
    <input type='hidden' id = 'recode' value='0'>
    <?php echo '<script'; ?>
>
        var module_num_arr = [<?php echo $_smarty_tpl->tpl_vars['today_sum']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['payment_page']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['my_account_sum']->value;?>
];
        var module_index = 1;
        var module_num_now = [2, 2, 2];

        $(window).scroll(function() {
            //此方法是在滚动条滚动时发生的函数
            // 当滚动到最底部以上100像素时，加载新内容
            var $doc_height,$s_top,$now_height,dataid,dataflag;
            $doc_height = $(document).height();        //这里是document的整个高度
            $s_top = $(this).scrollTop();            //当前滚动条离最顶上多少高度
            $now_height = $(this).height();            //这里的this 也是就是window对象
            dataid=$('#loading').data("id");//阻止一次请求没有完成后再次请
            dataflag=$('#loading').data("flag");//阻止当滚地到底部时刷新后自动请求
            console.log(module_num_now[module_index] + ' ' + module_num_arr[module_index]);

            
            if(($doc_height - $s_top - $now_height) < 100 && module_num_now[module_index] <= module_num_arr[module_index] && dataid==1 && dataflag==1){ 
                $('#loading').html("正在加载...").show();
                
                if (module_index == 0) {
                    jsonajax0();
                } else if(module_index == 1){
                    jsonajax1();
                } else if(module_index == 2) {
                    jsonajax2();
                }
            }

            $('#loading').data("flag", 1);
        });

        function jsonajax0() {
            $('#loading').data("id", 2);
            $('#loading').html("正在加载请稍后...");
            $.ajax({
                url:'/index.php?module=VisitingOrder&action=ajax_visiting_order_list',
                type:'POST',
                data:"pagenum=" + module_num_now[module_index]++,
                dataType: 'html',
                success:function(item){
                    $('#loading').hide();
                    $('.module_visiting_orders').append(item);
                    $('#loading').data("id", 1);


                    if(module_num_now[module_index] > module_num_arr[module_index]) {
                        $('#loading').html("没有了").show();
                    }
                }
            });
        } 
        function jsonajax1() {
            $('#loading').data("id", 2);
            $('#loading').html("正在加载请稍后...");
            $.ajax({
                url:'/index.php?module=VisitingOrder&action=ajax_my_receivepayment_list',
                type:'POST',
                data:"pagenum=" + module_num_now[module_index]++,
                dataType: 'html',
                success:function(item){
                    $('#loading').hide();
                    $('.module_my_payment').append(item);
                    $('#loading').data("id", 1);


                    if(module_num_now[module_index] > module_num_arr[module_index]) {
                        $('#loading').html("没有了").show();
                    }
                }
            });
        } 
        function jsonajax2() {
            $('#loading').data("id", 2);
            $('#loading').html("正在加载请稍后...");
            $.ajax({
                url:'/index.php?module=VisitingOrder&action=ajax_my_account_list',
                type:'POST',
                data:"pagenum=" + module_num_now[module_index]++,
                dataType: 'html',
                success:function(item){
                    $('#loading').hide();
                    $('.module_my_account').append(item);
                    $('#loading').data("id", 1);


                    if(module_num_now[module_index] > module_num_arr[module_index]) {
                        $('#loading').html("没有了").show();
                    }
                }
            });
        }


        $('.tabs').find('li').on('click', function () {
            var o = $(this);
            var os = o.siblings('li');
            var op = o.parent('.hd');
            var ops = op.next('.bd').children('ul');
            var index = o.index();

            o.addClass('on');
            os.removeClass('on');
            ops.addClass('hide');
            ops.eq(index).removeClass('hide');
            module_index = index;

            if(module_num_now[module_index] <= module_num_arr[module_index]) {
                $('#loading').html("正在加载...").hide();
            } 
            if (module_num_arr[module_index] == 1) {
                $('#loading').html("正在加载...").hide();
            } else {
                if(module_num_now[module_index] > module_num_arr[module_index]) {
                    $('#loading').html("没有了").show();
                }
            }
        });

        function opendl(id,recode){
            console.log(id+":"+recode);
        	$('#accountid').val(id); //拜访id
            $('#recode').val(recode);//客户id
        }
        function openurl(type){
        	if(type==1){
                var accountid = $('#recode').val();
				window.location.href = "/index.php?module=VisitingOrder&action=addMod&id="+accountid;
        	}else if(type==2){
                var accountid = $('#recode').val();
        		window.location.href = "/index.php?module=Accounts&action=addContact&id="+accountid;
        	}else if(type==3){
                if($('#accountid').val()){
                    window.location.href='index.php?module=VisitingOrder&action=sign&id='+$('#accountid').val();
                }else{
                    alert('拜访单信息有误，请刷新后重试');
                }
            }else if(type==5){
                window.location.href= 'index.php?module=VisitingOrder&action=vaddmod&id='+$('#accountid').val();
            }else if(type==4){
                window.location.href= 'index.php?module=VisitingOrder&action=signs&id='+$('#accountid').val();

                //window.location.href= 'index.php?module=VisitingOrder&action=picture&id='+$('#accountid').val();
            }else if(type==6){
                    window.location.href = 'index.php?module=VisitingOrder&action=add&accountid='+$('#recode').val();//客户id;
            }
        	
        }
    <?php echo '</script'; ?>
>



</body>
</html><?php }
}
