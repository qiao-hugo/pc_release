<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 13:45:57
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Vtiger\NoticeLayout.tpl" */ ?>
<?php /*%%SmartyHeaderCode:129396209ec95d370b0-83883219%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '444585467149645c4eb5ede9f3c9cadab9af5998' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Vtiger\\NoticeLayout.tpl',
      1 => 1597819338,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '129396209ec95d370b0-83883219',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LOADDATA' => 0,
    'MSGSTATUS' => 0,
    'MESSAGELINK' => 0,
    'itemessage' => 0,
    'REMINDLINK' => 0,
    'itemremind' => 0,
    'REMINDLINKREADSTATE' => 0,
    'itemremindreadstate' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_6209ec95d897f',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6209ec95d897f')) {function content_6209ec95d897f($_smarty_tpl) {?>
<div class="notice_layout"><ul class="notice_list" data-loadingdata="<?php echo $_smarty_tpl->tpl_vars['LOADDATA']->value;?>
"><li <?php if ($_smarty_tpl->tpl_vars['MSGSTATUS']->value){?>class="hover state_display"<?php }?>><a href="#" class="state_style state_styleimg"><img src="layouts/vlayout/skins/softed/images/noticeico.png"></a><div class="n_parent state_displays" <?php if ($_smarty_tpl->tpl_vars['MSGSTATUS']->value){?>style="display:block;"<?php }?>><div class="n_t"><?php echo vtranslate('LBL_CRM_STSTEM_MESSAGE');?>
<span class="n_close">×</span></div><dl class="n_dl"><?php if ($_smarty_tpl->tpl_vars['LOADDATA']->value){?><img src="/layouts/vlayout/skins/softed/images/loading.gif" width="25" height="25"><?php }else{ ?><?php  $_smarty_tpl->tpl_vars['itemessage'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['itemessage']->_loop = false;
 $_smarty_tpl->tpl_vars['keymessage'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['MESSAGELINK']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['itemessage']->key => $_smarty_tpl->tpl_vars['itemessage']->value){
$_smarty_tpl->tpl_vars['itemessage']->_loop = true;
 $_smarty_tpl->tpl_vars['keymessage']->value = $_smarty_tpl->tpl_vars['itemessage']->key;
?><?php if ($_smarty_tpl->tpl_vars['itemessage']->value['recordcount']==0){?><?php }else{ ?><dd><a href="<?php echo $_smarty_tpl->tpl_vars['itemessage']->value['linkurl'];?>
" target="<?php echo $_smarty_tpl->tpl_vars['itemessage']->value['target'];?>
">您有【<em><?php echo $_smarty_tpl->tpl_vars['itemessage']->value['recordcount'];?>
</em>】<?php echo vtranslate($_smarty_tpl->tpl_vars['itemessage']->value['linklabel']);?>
</a></dd><?php }?><?php } ?><?php }?></dl></div><i class="n_fg"></i></li><li><a href="#"><img src="layouts/vlayout/skins/softed/images/timeico.png"></a><div class="n_parent"><div class="n_t"><?php echo vtranslate('LBL_CRM_REMINDER');?>
</div><dl class="n_dl"><?php if ($_smarty_tpl->tpl_vars['LOADDATA']->value){?><img src="/layouts/vlayout/skins/softed/images/loading.gif" width="25" height="25"><?php }else{ ?><?php  $_smarty_tpl->tpl_vars['itemremind'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['itemremind']->_loop = false;
 $_smarty_tpl->tpl_vars['keyremind'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['REMINDLINK']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['itemremind']->key => $_smarty_tpl->tpl_vars['itemremind']->value){
$_smarty_tpl->tpl_vars['itemremind']->_loop = true;
 $_smarty_tpl->tpl_vars['keyremind']->value = $_smarty_tpl->tpl_vars['itemremind']->key;
?><?php if ($_smarty_tpl->tpl_vars['itemremind']->value['recordcount']==0){?><?php }else{ ?><dd><a href="<?php echo $_smarty_tpl->tpl_vars['itemremind']->value['linkurl'];?>
" target="<?php echo $_smarty_tpl->tpl_vars['itemremind']->value['target'];?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['itemremind']->value['linklabel']);?>
(<em><?php echo $_smarty_tpl->tpl_vars['itemremind']->value['recordcount'];?>
件</em>)</a></dd><?php }?><?php } ?><?php }?></dl></div><i class="n_fg"></i></li><li><a href="#"><img src="layouts/vlayout/skins/softed/images/letterico.png"></a><div class="n_parent"><div class="n_t"><?php echo vtranslate('LBL_CRM_STANDING_INFORMATION');?>
</div><dl class="n_dl"><?php if ($_smarty_tpl->tpl_vars['LOADDATA']->value){?><img src="/layouts/vlayout/skins/softed/images/loading.gif" width="25" height="25"><?php }else{ ?><?php  $_smarty_tpl->tpl_vars['itemremindreadstate'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['itemremindreadstate']->_loop = false;
 $_smarty_tpl->tpl_vars['keyremindreadstate'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['REMINDLINKREADSTATE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['itemremindreadstate']->key => $_smarty_tpl->tpl_vars['itemremindreadstate']->value){
$_smarty_tpl->tpl_vars['itemremindreadstate']->_loop = true;
 $_smarty_tpl->tpl_vars['keyremindreadstate']->value = $_smarty_tpl->tpl_vars['itemremindreadstate']->key;
?><?php if ($_smarty_tpl->tpl_vars['itemremindreadstate']->value['recordcount']==0){?><?php }else{ ?><dd><a href="<?php echo $_smarty_tpl->tpl_vars['itemremindreadstate']->value['linkurl'];?>
" target="<?php echo $_smarty_tpl->tpl_vars['itemremindreadstate']->value['target'];?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['itemremindreadstate']->value['linklabel']);?>
(<em><?php echo $_smarty_tpl->tpl_vars['itemremindreadstate']->value['recordcount'];?>
件</em>)</a></dd><?php }?><?php } ?><?php }?></dl></div><i class="n_fg"></i></li></ul></div><script type="text/javascript">
        $(document).ready(function(){
			var loadingdata=$('.notice_list').data('loadingdata');
			var isloading=0
        	$('.notice_list').hover(function(){
				if(loadingdata){
					if(isloading==0){
						isloading=1;
						$.ajax({type: "GET",
							url: "/index.php?module=WorkFlowCheck&view=List&mode=getNoticesansc",
							success: function(data){
								$('#footmsg').html(data);
							}
						});
					}
				}
			});
            $('.notice_list >li').mouseover(function(){
                $(this).addClass('hover');
                $(this).find('.n_parent').css('display','block');
            });

            $('.notice_list >li').mouseout(function(){
                $(this).removeClass('hover');
                $(this).find('.n_parent').css('display','none');
            });
            $('.n_close').click(function(){
                $(this).parent().parent().css('display','none');
            });
            //为空时，设置 暂无信息
			if(loadingdata){
				var dd = 1;
			}else{
				var dd = $('.n_dl').find("dd").length;
			}

            if(dd == 0){
                //移除闪烁图片class样式
                $(".state_styleimg").removeClass("state_style");
                $(".state_display").removeClass("hover");
                $(".state_displays").css("display","none");
                //$('.n_dl').append("<dd><font color='red'>暂无信息！</font></dd>");
            }
            $(".n_close").click(function(){
                $.ajax({type: "GET",
                    url: "/index.php?module=WorkFlowCheck&view=List&mode=setNoticesStatus",
                    success: function(msg){
                        //alert( "Data Saved: " + msg );
                    }
                })
			});
        })
</script><?php }} ?>