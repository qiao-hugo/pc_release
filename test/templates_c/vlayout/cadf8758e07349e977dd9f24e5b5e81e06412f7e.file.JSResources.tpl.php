<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 13:45:54
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Vtiger\JSResources.tpl" */ ?>
<?php /*%%SmartyHeaderCode:92736209ec922e9820-17813513%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cadf8758e07349e977dd9f24e5b5e81e06412f7e' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Vtiger\\JSResources.tpl',
      1 => 1592276828,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '92736209ec922e9820-17813513',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SCRIPTS' => 0,
    'jsModel' => 0,
    'VTIGER_VERSION' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_6209ec922f137',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6209ec922f137')) {function content_6209ec922f137($_smarty_tpl) {?><script type="text/javascript" src="data/min/?b=libraries&f=html5shim/html5.js,jquery/jquery.blockUI.js,jquery/chosen/chosen.jquery.min.js,jquery/select2/select2.min.js,jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js,jquery/jquery.class.min.js,jquery/defunkt-jquery-pjax/jquery.pjax.js,jquery/jstorage.min.js,jquery/autosize/jquery.autosize-min.js,jquery/rochal-jQuery-slimScroll/slimScroll.min.js,jquery/pnotify/jquery.pnotify.min.js,jquery/jquery.hoverIntent.minified.js,media/jquery.dataTables.js,media/dataTables.fixedColumns.js,bootstrap/js/bootstrap.js,bootstrap/js/bootbox.min.js,jquery/window.js,jquery/Fixed-Header-Table/jquery.fixedheadertable.min.js"></script>
<script src="libraries/jquery/jquery.twbsPagination.js"></script>
<link rel="stylesheet" type="text/css" href="data/min/?f=libraries/media/jquery.dataTables.css,libraries/media/dataTables.fixedColumns.css,libraries/jquery/Fixed-Header-Table/css/defaultTheme.css">
<script type="text/javascript" src="data/min/?b=resources&f=jquery.additions.js,app.js,helper.js,Connector.js,ProgressIndicator.js"></script>
<script type="text/javascript" src="resources/area.js"></script>
<script type="text/javascript" src="libraries/jquery/posabsolute-jQuery-Validation-Engine/js/jquery.validationEngine.js" ></script>
<script type="text/javascript" src="libraries/jquery/layer/layer.min.js"></script>
<script type="text/javascript" src="libraries/jquery/datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="libraries/jquery/datepicker/locales/bootstrap-datepicker.zh-CN.min.js"></script>
<script type="text/javascript" src="libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="libraries/jquery/kindeditor/kindeditor-all-min.js"></script>
<link rel="stylesheet" media="screen" type="text/css" href="libraries/jquery/kindeditor/themes/default/default.css" />
<?php  $_smarty_tpl->tpl_vars['jsModel'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['jsModel']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['SCRIPTS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['jsModel']->key => $_smarty_tpl->tpl_vars['jsModel']->value){
$_smarty_tpl->tpl_vars['jsModel']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['jsModel']->key;
?>
	<script type="<?php echo $_smarty_tpl->tpl_vars['jsModel']->value->getType();?>
" src="<?php echo $_smarty_tpl->tpl_vars['jsModel']->value->getSrc();?>
?&v=<?php echo $_smarty_tpl->tpl_vars['VTIGER_VERSION']->value;?>
"></script>
<?php } ?>
	<!-- Added in the end since it should be after less file loaded -->
<script type="text/javascript" src="libraries/bootstrap/js/less.min.js"></script>
	<!--王斌 百度编辑器-->
<script type="text/javascript" src="libraries/ueditor/ueditor.config.js?v=1.1"></script> <!-- 配置文件 -->
<script type="text/javascript" src="libraries/ueditor/ueditor.all.min.js?v=1.1"></script> <!-- 实例化编辑器 -->
<script type="text/javascript" src="libraries/jQuery.selected.js?v=1.1"></script><?php }} ?>