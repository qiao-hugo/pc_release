<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 13:45:53
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Vtiger\MenuBar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14356209ec918500a9-85742556%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'df9bdf8d498cfd0debccb9531c1bd07b21a21433' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Vtiger\\MenuBar.tpl',
      1 => 1639627445,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14356209ec918500a9-85742556',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'HOME_MODULE_MODEL' => 0,
    'MYMENU' => 0,
    'token' => 0,
    'waterText' => 0,
    'HEADER_LINKS' => 0,
    'obj' => 0,
    'src' => 0,
    'title' => 0,
    'linktype' => 0,
    'myremindercount' => 0,
    'USER_MODEL' => 0,
    'childLinks' => 0,
    'href' => 0,
    'recordcount' => 0,
    'label' => 0,
    'onclick' => 0,
    'PARENT_MODULE' => 0,
    'VIEW' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_6209ec918d007',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6209ec918d007')) {function content_6209ec918d007($_smarty_tpl) {?><!--菜单列表--><div class="navbar" style="margin-bottom:0;"><div class="navbar-inner" id="nav-inner"><div class="menuBar row-fluid"><div class="span10"><ul class="nav "><li class="tabs"><a class="alignMiddle <?php if ($_smarty_tpl->tpl_vars['MODULE']->value=='Home'){?> selected <?php }?>" href="<?php echo $_smarty_tpl->tpl_vars['HOME_MODULE_MODEL']->value->getDefaultUrl();?>
"><img src="<?php echo vimage_path('home.png');?>
" alt="<?php echo vtranslate('LBL_HOME','Vtiger');?>
" title="<?php echo vtranslate('LBL_HOME','Vtiger');?>
" /></a></li><?php echo $_smarty_tpl->tpl_vars['MYMENU']->value;?>
<li class="dropdown"><a class="dropdown-toggle" href="http://192.168.44.130" target="_blank">珍岛问答系统</a></li><li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">OA办公系统<b class="caret"></b></a><ul class="dropdown-menu"><li><a href="http://192.168.7.231/" target="_blank">假务系统</a></li><li><a href="http://192.168.7.231:8081/" target="_blank">证券系统</a></li><li><a href="http://192.168.7.201:9999/" target="_blank">报销系统</a></li></ul></li><li class="dropdown"><a class="dropdown-toggle" href="http://192.168.44.157:81/" target="_blank">客服系統</a></li><li class="dropdown"><a class="dropdown-toggle" href="http://192.168.7.231:8301/" target="_blank">招聘系統</a></li><li class="dropdown"><a class="dropdown-toggle" href="http://192.168.7.231:8501/" target="_blank">人事系统</a></li><li class="dropdown"><a class="dropdown-toggle" href="http://192.168.7.231:8901/" target="_blank">中小管理系统</a></li><li class="dropdown"><a class="dropdown-toggle" href="https://predmc.71360.com/clue/index?token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
" target="_blank">臻寻客</a></li><li class="dropdown"><a class="dropdown-toggle" href="https://prein-gw.71360.com/visit-center/login?__vt_param__=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
&callback=<?php echo urlencode('https://prein-web.71360.com/visitcenterweb?original=crm');?>
" target="_blank">拜访中心</a></li><li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">SCRM<b class="caret"></b></a><ul class="dropdown-menu"><li><a href="/index.php?module=Vtiger&action=LinkToJump&type=1" target="_blank">专业版</a></li><li><a href="/index.php?module=Vtiger&action=LinkToJump&type=2" target="_blank">中小版</a></li><li><a href="/index.php?module=Vtiger&action=LinkToJump&type=3" target="_blank">商业云</a></li><li><a href="/index.php?module=Vtiger&action=LinkToJump&type=4" target="_blank">招聘版</a></li></ul></li></ul></div><input type="hidden" id="waterTextContent" value="<?php echo $_smarty_tpl->tpl_vars['waterText']->value;?>
" /><div class="span2" id="headerLinks" ><ul class="nav nav-pills pull-right" style="float:right"><?php  $_smarty_tpl->tpl_vars['obj'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['obj']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['HEADER_LINKS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['obj']->key => $_smarty_tpl->tpl_vars['obj']->value){
$_smarty_tpl->tpl_vars['obj']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['obj']->key;
?><?php $_smarty_tpl->tpl_vars["src"] = new Smarty_variable($_smarty_tpl->tpl_vars['obj']->value->getIconPath(), null, 0);?><?php $_smarty_tpl->tpl_vars["icon"] = new Smarty_variable($_smarty_tpl->tpl_vars['obj']->value->getIcon(), null, 0);?><?php $_smarty_tpl->tpl_vars["title"] = new Smarty_variable($_smarty_tpl->tpl_vars['obj']->value->getLabel(), null, 0);?><?php $_smarty_tpl->tpl_vars["childLinks"] = new Smarty_variable($_smarty_tpl->tpl_vars['obj']->value->getChildLinks(), null, 0);?><?php $_smarty_tpl->tpl_vars["href"] = new Smarty_variable($_smarty_tpl->tpl_vars['obj']->value->getUrl(), null, 0);?><?php $_smarty_tpl->tpl_vars["linktype"] = new Smarty_variable($_smarty_tpl->tpl_vars['obj']->value->getType(), null, 0);?><li class="dropdown"><?php if (!empty($_smarty_tpl->tpl_vars['src']->value)){?><a id="menubar_item_right_<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
" class="dropdown-toggle" data-toggle="dropdown" href="#"><img src="<?php echo $_smarty_tpl->tpl_vars['src']->value;?>
" alt="<?php echo vtranslate($_smarty_tpl->tpl_vars['title']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
" title="<?php echo vtranslate($_smarty_tpl->tpl_vars['title']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
" /><?php echo vtranslate($_smarty_tpl->tpl_vars['title']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php if ($_smarty_tpl->tpl_vars['linktype']->value=='REMINDERLINK'){?><?php $_smarty_tpl->tpl_vars["myremindercount"] = new Smarty_variable($_smarty_tpl->tpl_vars['obj']->value->myremindercount, null, 0);?><span class="badge badge-warning"><?php echo $_smarty_tpl->tpl_vars['myremindercount']->value;?>
</span><?php }?></a><?php }else{ ?><?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->get('first_name'), null, 0);?><?php if (empty($_smarty_tpl->tpl_vars['title']->value)){?><?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->get('last_name'), null, 0);?><?php }?><span class="dropdown-toggle" data-toggle="dropdown" href="#"><a id="menubar_item_right_<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
"  class="userName textOverflowEllipsis span" title="<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
 <i class="caret"></i> </a> </span><?php }?><?php if (!empty($_smarty_tpl->tpl_vars['childLinks']->value)){?><ul class="dropdown-menu"><?php  $_smarty_tpl->tpl_vars['obj'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['obj']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['childLinks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['obj']->key => $_smarty_tpl->tpl_vars['obj']->value){
$_smarty_tpl->tpl_vars['obj']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['obj']->key;
?><?php if ($_smarty_tpl->tpl_vars['obj']->value->getLabel()==null){?><li class="divider">&nbsp;</li><?php }else{ ?><?php $_smarty_tpl->tpl_vars["id"] = new Smarty_variable($_smarty_tpl->tpl_vars['obj']->value->getId(), null, 0);?><?php $_smarty_tpl->tpl_vars["href"] = new Smarty_variable($_smarty_tpl->tpl_vars['obj']->value->getUrl(), null, 0);?><?php $_smarty_tpl->tpl_vars["label"] = new Smarty_variable($_smarty_tpl->tpl_vars['obj']->value->getLabel(), null, 0);?><?php if ($_smarty_tpl->tpl_vars['linktype']->value=='REMINDERLINK'){?><?php $_smarty_tpl->tpl_vars["recordcount"] = new Smarty_variable($_smarty_tpl->tpl_vars['obj']->value->recordcount, null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars["recordcount"] = new Smarty_variable(0, null, 0);?><?php }?><?php $_smarty_tpl->tpl_vars["onclick"] = new Smarty_variable('', null, 0);?><?php if (stripos($_smarty_tpl->tpl_vars['obj']->value->getUrl(),'javascript:')===0){?><?php $_smarty_tpl->tpl_vars["onclick"] = new Smarty_variable(("onclick=").($_smarty_tpl->tpl_vars['href']->value), null, 0);?><?php $_smarty_tpl->tpl_vars["href"] = new Smarty_variable("javascript:;", null, 0);?><?php }?><li><?php if ($_smarty_tpl->tpl_vars['recordcount']->value!=''){?><a target="<?php echo $_smarty_tpl->tpl_vars['obj']->value->target;?>
" id="menubar_item_right_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['label']->value);?>
" <?php if ($_smarty_tpl->tpl_vars['label']->value=='Switch to old look'){?>switchLook<?php }?> href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['onclick']->value;?>
><font size="2"><?php echo vtranslate($_smarty_tpl->tpl_vars['label']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</font>(<font size="2" color="red"><?php echo $_smarty_tpl->tpl_vars['recordcount']->value;?>
件</font>)</a><?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['label']->value=='WorkSummarize'){?><a id="menubar_item_right_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['label']->value);?>
" <?php if ($_smarty_tpl->tpl_vars['label']->value=='Switch to old look'){?>switchLook<?php }?> href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['onclick']->value;?>
><?php echo vtranslate($_smarty_tpl->tpl_vars['label']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a><?php }else{ ?><a id="menubar_item_right_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['label']->value);?>
" <?php if ($_smarty_tpl->tpl_vars['label']->value=='Switch to old look'){?>switchLook<?php }?> href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['onclick']->value;?>
><?php echo vtranslate($_smarty_tpl->tpl_vars['label']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a><?php }?><?php }?></li><?php }?><?php } ?></ul><?php }?></li><?php } ?></ul></div></div><div class="clearfix"></div></div></div><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
" id='module' name='module'/><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['PARENT_MODULE']->value;?>
" id="parent" name='parent' /><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['VIEW']->value;?>
" id='view' name='view'/>
<?php }} ?>