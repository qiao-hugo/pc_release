<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 13:45:54
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Vtiger\Footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:267596209ec922da0b9-87176537%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3467ffb9ddaa8b6e084ea5365f5f7e5d5f3fcdf2' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Vtiger\\Footer.tpl',
      1 => 1600766438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '267596209ec922da0b9-87176537',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ACTIVITY_REMINDER' => 0,
    'VTIGER_VERSION' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_6209ec922e299',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6209ec922e299')) {function content_6209ec922e299($_smarty_tpl) {?>
<input id='activityReminder' class='hide noprint' type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['ACTIVITY_REMINDER']->value;?>
"/><footer class="noprint"><div class="vtFooter"><font style="" ><?php echo vtranslate('POWEREDBY');?>
 <?php echo $_smarty_tpl->tpl_vars['VTIGER_VERSION']->value;?>
 &nbsp;&copy; <?php echo date('Y');?>
 &nbsp&nbsp;</font></div></footer><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('JSResources.tpl'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div><div class="widgetContaine_footmsg" data-url="module=WorkFlowCheck&amp;view=List&amp;mode=getNotices" data-name=""><div class="widget_contents" id="footmsg"></div></div></body><div id="dialog-message" class="hide">加载中... </div></html>
<?php }} ?>