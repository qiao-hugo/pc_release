<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 11:16:43
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Vtiger\BasicHeaderV2.tpl" */ ?>
<?php /*%%SmartyHeaderCode:100046209c99b97d2c9-80754855%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5756581ed6e80249d6c1486c98404ab8ba93a260' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Vtiger\\BasicHeaderV2.tpl',
      1 => 1639627445,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '100046209c99b97d2c9-80754855',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'waterText' => 0,
    'CURRENT_USER_MODEL' => 0,
    'title' => 0,
    'token' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_6209c99b9957b',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6209c99b9957b')) {function content_6209c99b9957b($_smarty_tpl) {?><input type="hidden" id="waterTextContent" value="<?php echo $_smarty_tpl->tpl_vars['waterText']->value;?>
"/><div class="page flex-col"><div class="layer1 flex-col"><div class="layer2 flex-col"><div class="group1 flex-row"><div class="group-logo"><a href="/"><img class="logo" referrerpolicy="no-referrer" src="libraries/v2/img/logo.png"/></a><span class="txt1">珍岛数字化平台</span></div><div class="group-inner"><a class="link" href="http://192.168.44.130/" target="_blank">珍岛问答系统</a><a class="link" href="http://192.168.7.231/" target="_blank">假务系统</a><a class="link" href="http://192.168.7.231:8081/" target="_blank">证券系统</a><a class="link" href="http://192.168.7.201:9999/" target="_blank">报销系统</a><span class="fa fa-chevron-down"></span><?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('first_name'), null, 0);?><?php if (empty($_smarty_tpl->tpl_vars['title']->value)){?><?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('last_name'), null, 0);?><?php }?><img class="label1" referrerpolicy="no-referrer" src="libraries/v2/img/head.png"/><span class="word4 username"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</span></div><div class="menu-nav"><div class="nav-con"><a href="http://192.168.7.231:8082/" target="_blank">设备管理系统</a><a href="http://192.168.44.157:81/" target="_blank">客服系統</a><a href="http://192.168.7.231:8301/" target="_blank">招聘系統</a><a href="http://192.168.7.231:8501/" target="_blank">人事系统</a><a href="http://192.168.7.231:8901/" target="_blank">中小管理系统</a><a href="https://predmc.71360.com/clue/index?token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
" target="_blank">臻寻客</a><a href="https://prein-gw.71360.com/visit-center/login?__vt_param__=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
&callback=<?php echo urlencode('https://prein-web.71360.com/visitcenterweb?original=4001');?>
" target="_blank">拜访中心</a><a href="/index.php?module=Vtiger&action=LinkToJump&type=1" target="_blank">SCRM专业版</a><a href="/index.php?module=Vtiger&action=LinkToJump&type=2" target="_blank">SCRM中小版</a><a href="/index.php?module=Vtiger&action=LinkToJump&type=3" target="_blank">SCRM商业云</a><a href="/index.php?module=Vtiger&action=LinkToJump&type=4" target="_blank">SCRM招聘版</a></div></div><div class="userinfo-con"><div class="userinfo"><a href="index.php?module=Users&view=PreferenceDetail&record=<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('id');?>
">个人选项</a><a href="index.php?module=Users&parent=Settings&action=Logout">注销</a></div></div></div></div><?php }} ?>