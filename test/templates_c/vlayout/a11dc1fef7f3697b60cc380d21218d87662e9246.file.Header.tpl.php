<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 13:45:53
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Vtiger\Header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:135846209ec917dde93-28519939%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a11dc1fef7f3697b60cc380d21218d87662e9246' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Vtiger\\Header.tpl',
      1 => 1639985058,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '135846209ec917dde93-28519939',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'PAGETITLE' => 0,
    'MODULE_NAME' => 0,
    'STYLES' => 0,
    'cssModel' => 0,
    'VTIGER_VERSION' => 0,
    'CURRENT_USER_MODEL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_6209ec917f7ed',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6209ec917f7ed')) {function content_6209ec917f7ed($_smarty_tpl) {?>
<!DOCTYPE html><html><head><title><?php echo vtranslate($_smarty_tpl->tpl_vars['PAGETITLE']->value,$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</title><link REL="SHORTCUT ICON" HREF="favicon.ico"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="renderer" content="webkit"><meta name="viewport" content="width=device-width, initial-scale=1.0" /><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><link rel="stylesheet" href="data/min/?b=libraries&f=jquery/chosen/chosen.css,jquery/jquery-ui/css/custom-theme/jquery-ui-1.8.16.custom.css,jquery/select2/select2.css,bootstrap/css/bootstrap.css,jquery/posabsolute-jQuery-Validation-Engine/css/validationEngine.jquery.css,guidersjs/guiders-1.2.6.css,jquery/pnotify/jquery.pnotify.default.css,jquery/pnotify/use for pines style icons/jquery.pnotify.default.icons.css" type="text/css" media="screen" /><!--<link rel="stylesheet" href="libraries/bootstrap/css/dataTables.bootstrap.css" type="text/css" media="screen" />--><link rel="stylesheet" href="resources/styles.css" type="text/css" media="screen" /><link rel="stylesheet" href="libraries/jquery/select2/select2.css" /><?php  $_smarty_tpl->tpl_vars['cssModel'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cssModel']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['STYLES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cssModel']->key => $_smarty_tpl->tpl_vars['cssModel']->value){
$_smarty_tpl->tpl_vars['cssModel']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['cssModel']->key;
?><link rel="<?php echo $_smarty_tpl->tpl_vars['cssModel']->value->getRel();?>
" href="<?php echo $_smarty_tpl->tpl_vars['cssModel']->value->getHref();?>
?&v=<?php echo $_smarty_tpl->tpl_vars['VTIGER_VERSION']->value;?>
" type="<?php echo $_smarty_tpl->tpl_vars['cssModel']->value->getType();?>
" media="<?php echo $_smarty_tpl->tpl_vars['cssModel']->value->getMedia();?>
" /><?php } ?><link rel="stylesheet" media="screen" type="text/css" href="libraries/jquery/datepicker/css/bootstrap-datepicker.min.css" /><link rel="stylesheet" media="screen" type="text/css" href="libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.css" /><style type="text/css">@media print {.noprint { display:none; }}</style><script type="text/javascript" src="libraries/jquery/jquery.min.js"></script><script type="text/javascript" src="libraries/jquery/Fixed-Header-Table/tableHeadFixer.js"></script></head><script>function changeWidth() {var a = $(window).width();if(a < 1650){(document.getElementsByClassName("bodyContents"))[0].style.marginTop = '90px';}else{(document.getElementsByClassName("bodyContents"))[0].style.marginTop = '40px';}}window.onload = function () {console.log(1);changeWidth();};$(function () {$(window).resize(function () {console.log(3);changeWidth();});})</script><body data-skinpath="layouts/vlayout/skins/softed" data-language="zh_cn"><?php $_smarty_tpl->tpl_vars['CURRENT_USER_MODEL'] = new Smarty_variable(Users_Record_Model::getCurrentUserModel(), null, 0);?><input type="hidden" id="start_day" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('dayoftheweek');?>
" /><input type="hidden" id="row_type" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('rowheight');?>
" /><input type="hidden" id="current_user_id" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('id');?>
" /><div id="page"><!-- container which holds data temporarly for pjax calls --><div id="pjaxContainer" class="hide noprint"></div>
<?php }} ?>