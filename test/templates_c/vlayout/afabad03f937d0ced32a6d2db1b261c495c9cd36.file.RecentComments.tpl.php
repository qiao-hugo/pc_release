<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 16:37:11
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Accounts\RecentComments.tpl" */ ?>
<?php /*%%SmartyHeaderCode:518620a14b752d411-25307066%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'afabad03f937d0ced32a6d2db1b261c495c9cd36' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Accounts\\RecentComments.tpl',
      1 => 1622012334,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '518620a14b752d411-25307066',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ACCOUNTID' => 0,
    'COMMENTSTYPE' => 0,
    'COMMENTtype' => 0,
    'ACCOUNTINTENTIONALITY' => 0,
    'index' => 0,
    'servicecomment' => 0,
    'MODCOMMENTCONTACTS' => 0,
    'MODCOMMENTContacts' => 0,
    'COMMENTSMODE' => 0,
    'COMMENTMode' => 0,
    'TASKNAME' => 0,
    'REMARK' => 0,
    'MODULE_NAME' => 0,
    'COMMENT_TEXTAREA_DEFAULT_ROWS' => 0,
    'COMMENTS' => 0,
    'COMMENT' => 0,
    'IMAGE_PATH' => 0,
    'COMMENTOR' => 0,
    'ROLE' => 0,
    'ISCONTACT' => 0,
    'ISSHOUYAO' => 0,
    'PARENT_COMMENT_MODEL' => 0,
    'CHILD_COMMENTS_MODEL' => 0,
    'COMMENT_ALERTS_ROWS' => 0,
    'COMMENT_ALERTS_ROW' => 0,
    'PAGEHIS' => 0,
    'his' => 0,
    'COMMENTHISTORY' => 0,
    'PAGING_MODEL' => 0,
    'COMMENTSCOUNTS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620a14b765cea',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620a14b765cea')) {function content_620a14b765cea($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_replace')) include 'D:\\phpstudy_pro\\WWW\\pc_release\\libraries\\Smarty\\libs\\plugins\\modifier.replace.php';
?>
<?php $_smarty_tpl->tpl_vars["COMMENT_TEXTAREA_DEFAULT_ROWS"] = new Smarty_variable("2", null, 0);?><style>.followup11toyes{display: none;}.followup11tono{display: none;}</style><div class="commentContainer"><input id="accountId" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['ACCOUNTID']->value;?>
" /><div class="commentTitle row-fluid "><div class="addCommentBlock "><div class="control-group"><table width="100%" class="form-inline"><tr><td><label class="control-label" for="modcommenttype"><?php echo vtranslate('LBL_modcommenttype','ModComments');?>
 &nbsp;:&nbsp;</label><select class="modcommenttype" name="modcommenttype"><option value="">请选择一个选项</option><?php  $_smarty_tpl->tpl_vars['COMMENTtype'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['COMMENTtype']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['COMMENTSTYPE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['COMMENTtype']->key => $_smarty_tpl->tpl_vars['COMMENTtype']->value){
$_smarty_tpl->tpl_vars['COMMENTtype']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['COMMENTtype']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['COMMENTtype']->value;?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['COMMENTtype']->value,'ModComments');?>
</option><?php } ?></select></td><td><label class="control-label" for="modcommenttype"><span style="color: red">*</span><?php echo vtranslate('LBL_intentionality','ModComments');?>
 &nbsp;:&nbsp;</label><select class="accountintentionality" name="accountintentionality"><option value="">请选择一个选项</option><?php  $_smarty_tpl->tpl_vars['COMMENTtype'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['COMMENTtype']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ACCOUNTINTENTIONALITY']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['COMMENTtype']->key => $_smarty_tpl->tpl_vars['COMMENTtype']->value){
$_smarty_tpl->tpl_vars['COMMENTtype']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['COMMENTtype']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['index']->value;?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['COMMENTtype']->value,'ModComments');?>
</option><?php } ?></td></td></tr></table></div><input type="hidden" name="is_service" class="is_service" value="<?php echo $_smarty_tpl->tpl_vars['servicecomment']->value;?>
"><div><div class="control-group"><table width="100%" class="form-inline"><tr><td><label class="control-label" for="modcommentcontacts"><?php echo vtranslate('LBL_modcommentcontacts','ModComments');?>
 &nbsp;: &nbsp;</label><select class="modcommentcontacts" name="modcommentcontacts"><option value="">请选择一个选项</option><?php  $_smarty_tpl->tpl_vars['MODCOMMENTContacts'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['MODCOMMENTContacts']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['MODCOMMENTCONTACTS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['MODCOMMENTContacts']->key => $_smarty_tpl->tpl_vars['MODCOMMENTContacts']->value){
$_smarty_tpl->tpl_vars['MODCOMMENTContacts']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['MODCOMMENTContacts']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['MODCOMMENTContacts']->value['contactid'];?>
"><?php echo $_smarty_tpl->tpl_vars['MODCOMMENTContacts']->value['name'];?>
</option><?php } ?></select></td><td><label class="control-label" for="modcommentmode"><?php echo vtranslate('LBL_modcommentmode','ModComments');?>
 &nbsp;: &nbsp;</label><select class="modcommentmode" name="modcommentmode"><option value="">请选择一个选项</option><?php  $_smarty_tpl->tpl_vars['COMMENTMode'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['COMMENTMode']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['COMMENTSMODE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['COMMENTMode']->key => $_smarty_tpl->tpl_vars['COMMENTMode']->value){
$_smarty_tpl->tpl_vars['COMMENTMode']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['COMMENTMode']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['COMMENTMode']->value;?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['COMMENTMode']->value,'ModComments');?>
</option><?php } ?></select></td><td></td></tr><?php if ($_smarty_tpl->tpl_vars['TASKNAME']->value!=''){?> <tr><td colspan="3"><div class="bs-callout bs-callout-warning">您本次跟进有T-云任务 <span class="label label-a_normal"><?php echo $_smarty_tpl->tpl_vars['TASKNAME']->value;?>
</span>,跟进请勾选<input class="updateautotask" type="checkbox" name="updateautotask" checked><label class="control-label"></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<h4>本次跟进任务清单:</h4><?php echo $_smarty_tpl->tpl_vars['REMARK']->value;?>
</div></td></tr><?php }?></table></div></div><div><div id="firstInput" class="span12" style="margin:5px;display:none;"><div class="accordion-group"><div class="modal-footer" style="text-align:left;height:15px;line-height: 15px;"><h4 style="color: #FF9E0E;margin:0;padding:0;"><首次客户录入系统跟进>跟进内容规范提示</h4></div><div><div class="accordion-body collapse in"><div class="accordion-inner"><table width="100%" class="form-inline"><tbody><tr><td class="span3"><label class="control-label" for="modcommentcontacts">1.客户资料来源</label></td><td class="span9"><input type="text" class="span12" data-thisid="1" name="followup[1]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts">2.客户语气态度</label></td><td><input type="text" class="span12"  data-thisid="2" name="followup[2]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts"> 3.是否了解过珍岛</label></td><td><input type="text" class="span12"  data-thisid="3" name="followup[3]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts"> 4.客户质量</label></td><td></td></tr><tr><td><label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;①注册时间</label></td><td><input type="text" class="span12" data-thisid="4" name="followup[4]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;①注册资金</label></td><td><input type="text" class="span12" data-thisid="5" name="followup[5]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;①法人还是股东</label></td><td><input type="text" class="span12" data-thisid="6" name="followup[6]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;②意向点</label></td><td><input type="text" class="span12" data-thisid="7" name="followup[7]" placeholder="是否主动让加微信，客户微信同意情况，电话通话时间，客户问的问题"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts">&nbsp; &nbsp;&nbsp;③客户行业和产品</label></td><td><input type="text" class="span12" data-thisid="8" name="followup[8]"/></td></tr><tr><td span=""><label class="control-label" for="modcommentcontacts">5.邀约是否成功</label></td><td><input type="hidden"  for="modcommentcontacts"  class="span12" data-thisid="11" name="followup[11]"/><select class="span12"   data-thisid="11" name="followupinviteres" id="followupinviteres"><option value="">请选择一个选项</option><option value="是">是</option><option value="否">否</option></select></td></tr><tr class="followup11toyes"><td><label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;①邀约人物</label></td><td><input type="text" class="span12" data-thisid="12" name="followup[12]" placeholder=""/></td></tr><tr class="followup11toyes"><td><label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;②邀约时间</label></td><td><input type="text" class="span12" data-thisid="13" name="followup[13]" placeholder=""/></td></tr><tr class="followup11toyes"><td><label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;③邀约地点</label></td><td><input type="text" class="span12" data-thisid="14" name="followup[14]" placeholder=""/></td></tr><tr  class="followup11toyes"><td><label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;④所谈业务</label></td><td><input type="text" class="span12" data-thisid="15" name="followup[15]" placeholder=""/></td></tr><tr  class="followup11tono"><td span=""><label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;①本次未能邀约见面的原因</label></td><td><input type="text" class="span12" data-thisid="9" name="followup[9]" placeholder=""/></td></tr><tr  class="followup11tono"><td><label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;②预约下次电话的时间</label></td><td><input type="text" class="span12" data-thisid="10" name="followup[10]" placeholder=""/></td></tr><tr><td>&nbsp;</td><td></td></tr></tbody></table></div></div></div></div></div><div id="firstVisit" class="span12" style="margin:5px;display:none;"><div class="accordion-group"><div class="modal-footer" style="text-align:left;height:15px;line-height: 15px;"><h4 style="color: #FF9E0E;margin:0;padding:0;"><首次拜访客户后跟进>跟进内容规范提示：</h4></div><div><div class="accordion-body collapse in"><div class="accordion-inner"><table width="100%" class="form-inline"><tbody><tr><td class="span3"><label class="control-label" for="modcommentcontacts">1.公司规模，公司大概多少人</label></td><td class="span9"><input type="text" class="span12" data-thisid="1" name="followupvisit[1]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts">2.拜访的负责人/老板</label></td><td><input type="hidden" data-thisid="2" value="KP" name="followupvisit[2]"/><input type="radio"   value="KP" name="leader" checked placeholder="（若是负责人，写清楚具体职位）"/>KP    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio"   value="非KP" name="leader" placeholder="（若是负责人，写清楚具体职位）"/>非KP</td></tr><tr><td><label class="control-label" for="modcommentcontacts"> 3.拜访人性格描述</label></td><td><input type="text" class="span12"  data-thisid="3" name="followupvisit[3]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts"> 4.拜访人年龄阶段/老家哪里</label></td><td><input type="text" class="span12" data-thisid="4" name="followupvisit[4]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts"> 5.客户目前的网络现状</label></td><td><input type="text" class="span12" data-thisid="5" name="followupvisit[5]" placeholder="比如投入了那些平台，花了多少钱，做了大概多久"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts"> 6.客户问了哪些问题</label></td><td><input type="text" class="span12" data-thisid="6" name="followupvisit[6]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts"> 7.整个面谈过程中，客户对那几个点比较感兴趣</label></td><td><input type="text" class="span12" data-thisid="7" name="followupvisit[7]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts"> 8.关于我们谈判中给到客户方的信息&nbsp;: &nbsp;</label></td><td></td></tr><tr><td><label class="control-label" for="modcommentcontacts">&nbsp;&nbsp;①谈的什么产品/版本/年限</label></td><td><input type="text" class="span12" data-thisid="8" name="followupvisit[8]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts">&nbsp;&nbsp;②给客户报价多少，是否提到优惠，如果提到了，提到的优惠是什么</label></td><td><input type="text" class="span12" data-thisid="9" name="followupvisit[9]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts">&nbsp;&nbsp;③当时给客户介绍的案例是哪些</label></td><td><input type="text" class="span12" data-thisid="10" name="followupvisit[10]"/></td></tr><tr><td><label class="control-label" for="modcommentcontacts">&nbsp;&nbsp;④客户没有当场签单的原因</label></td><td><input type="text" class="span12" data-thisid="11" name="followupvisit[11]"/></td></tr><tr><td>&nbsp;</td><td></td></tr></tbody></table></div></div></div></div></div><div id="firstInput1" style="display:none;border-left: 5px solid #f0ad4e;border-radius: 3px;padding-left: 20px;box-shadow:2.9px 3.99px 3px  rgba(0,0,0,.5)"><h5 style="color: #FF9E0E;"><首次客户录入系统跟进>跟进内容规范提示：</h5><p style="margin:2px;">1.客户资料来源？<br>2.客户语气态度？<br>3.是否了解过珍岛？<br>4.客户质量<br>①注册时间，注册资金，法人还是股东？<br>②意向点：是否主动让加微信，客户微信同意情况，电话通话时间，客户问的问题<br>③客户行业和产品<br>5.本次未能邀约见面的原因（邀约成功可不写）<br>6.预约下次电话的时间（邀约成功可不写）？<br></p></div><div id="firstVisit1" style="display:none;border-left: 5px solid #f0ad4e;border-radius: 3px;padding-left: 20px;box-shadow:2.9px 3.99px 3px  rgba(0,0,0,.5) "><h5 style="color: #FF9E0E;"><首次拜访客户后跟进>跟进内容规范提示：</h5><p style="margin: 2px;">1.公司规模，公司大概多少人<br>2.拜访的负责人/老板（若是负责人，写清楚具体职位）<br>3.拜访人性格描述<br>4.拜访人年龄阶段/老家哪里<br>5.客户目前的网络现状，比如投入了那些平台，花了多少钱，做了大概多久<br>6.客户问了哪些问题<br>7.整个面谈过程中，客户对那几个点比较感兴趣？<br></p></div><textarea style="border-top: 0px solid red;" name="commentcontents" class="commentcontent"  placeholder="<?php echo vtranslate('LBL_ADD_YOUR_COMMENT_HERE',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
" rows="<?php echo $_smarty_tpl->tpl_vars['COMMENT_TEXTAREA_DEFAULT_ROWS']->value;?>
"></textarea></div><div class="pull-right"><button class="btn btn-success detailViewSaveComment" type="button" data-mode="add"><strong><?php echo vtranslate('LBL_POST',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></button></div></div></div><div class="commentsBody"><?php if (!empty($_smarty_tpl->tpl_vars['COMMENTS']->value)){?><?php  $_smarty_tpl->tpl_vars['COMMENT'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['COMMENT']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['COMMENTS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['COMMENT']->key => $_smarty_tpl->tpl_vars['COMMENT']->value){
$_smarty_tpl->tpl_vars['COMMENT']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['COMMENT']->key;
?><div class="commentDetails bs-example"><div class="commentDiv"><div class="singleComment"><div class="commentInfoHeader row-fluid" data-commentid="<?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->getId();?>
" data-parentcommentid="<?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->getId();?>
"><div class="commentTitle"><?php $_smarty_tpl->tpl_vars['PARENT_COMMENT_MODEL'] = new Smarty_variable($_smarty_tpl->tpl_vars['COMMENT']->value->getParentCommentModel(), null, 0);?><div class="row-fluid"><div class="span1"><?php $_smarty_tpl->tpl_vars['IMAGE_PATH'] = new Smarty_variable($_smarty_tpl->tpl_vars['COMMENT']->value->getImagePath(), null, 0);?><img class="alignMiddle pull-left" src="<?php if (!empty($_smarty_tpl->tpl_vars['IMAGE_PATH']->value)){?><?php echo $_smarty_tpl->tpl_vars['IMAGE_PATH']->value;?>
<?php }else{ ?><?php echo vimage_path('DefaultUserIcon.png');?>
<?php }?>"></div><div class="span11 commentorInfo"><?php $_smarty_tpl->tpl_vars['COMMENTOR'] = new Smarty_variable($_smarty_tpl->tpl_vars['COMMENT']->value->getCommentedByModel(), null, 0);?><div class="inner"><span class="commentorName"><strong><?php echo $_smarty_tpl->tpl_vars['COMMENTOR']->value->getName();?>
&nbsp;<span class="label label-a_normal"><?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['ROLE']->value[$_smarty_tpl->tpl_vars['COMMENTOR']->value->entity->roleid],'|—','');?>
</span></strong> </span><span class="pull-right"><p class="muted"><?php echo vtranslate('LBL_modcommenttype','ModComments');?>
 : <?php echo vtranslate($_smarty_tpl->tpl_vars['COMMENT']->value->get('modcommenttype'),'ModComments');?>
 <?php if ($_smarty_tpl->tpl_vars['COMMENT']->value->get('accountintentionality')!=''&&$_smarty_tpl->tpl_vars['COMMENT']->value->get('accountintentionality')!='zeropercent'){?> <?php echo vtranslate('LBL_intentionality','ModComments');?>
 : <?php echo vtranslate($_smarty_tpl->tpl_vars['COMMENT']->value->get('accountintentionality'),'Accounts');?>
<?php }?> <?php echo vtranslate('LBL_modcommentmode','ModComments');?>
 : <?php echo vtranslate($_smarty_tpl->tpl_vars['COMMENT']->value->get('modcommentmode'),'ModComments');?>
 <em><?php echo vtranslate('LBL_COMMENTED',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</em>&nbsp;<small title="<?php echo Vtiger_Util_Helper::formatDateTimeIntoDayString($_smarty_tpl->tpl_vars['COMMENT']->value->getCommentedTime());?>
"><?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->getCommentedTime();?>
</small> </p></span><div class="clearfix"></div></div><div class="commentInfoContent"><style>h4{font-size:14px;font-weight:500;font-family: 'Helvetica Neue', Helvetica, 'Microsoft Yahei', 'Hiragino Sans GB', 'WenQuanYi Micro Hei', sans-serif;}</style><div class="bs-callout bs-callout-info"><h4>&nbsp;联系人:<span class="" data-field-type="reference" data-field-name="contact_id"><?php $_smarty_tpl->tpl_vars['ISCONTACT'] = new Smarty_variable($_smarty_tpl->tpl_vars['COMMENT']->value->get('lastname'), null, 0);?><?php $_smarty_tpl->tpl_vars['ISSHOUYAO'] = new Smarty_variable($_smarty_tpl->tpl_vars['COMMENT']->value->get('shouyao'), null, 0);?><?php if (empty($_smarty_tpl->tpl_vars['ISCONTACT']->value)){?><?php if (empty($_smarty_tpl->tpl_vars['ISSHOUYAO']->value)){?>-<?php }else{ ?><a href="?module=Accounts&amp;view=Detail&amp;record=<?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->get('contact_id');?>
" onclick="return false;" data-original-title="<?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->get('shouyao');?>
"><?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->get('shouyao');?>
</a><?php }?><?php }else{ ?><a href="?module=Contacts&amp;view=Detail&amp;record=<?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->get('contact_id');?>
" onclick="return false;" data-original-title="<?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->get('lastname');?>
"><?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->get('lastname');?>
</a><?php }?></span></h4><?php if ($_smarty_tpl->tpl_vars['COMMENT']->value->get('modcommenttype')=='首次客户录入系统跟进'||$_smarty_tpl->tpl_vars['COMMENT']->value->get('modcommenttype')=='首次拜访客户后跟进'){?><?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->getFollowUp($_smarty_tpl->tpl_vars['COMMENT']->value->get('modcommenttype'),$_smarty_tpl->tpl_vars['COMMENT']->value->get('commentcontent'));?>
<?php }elseif($_smarty_tpl->tpl_vars['COMMENT']->value->get('followrole')==1){?><?php echo preg_replace("/(\\n+)/",'<br>',(preg_replace("/(\*#\*)+/",'：',(preg_replace("/(#endl#)+/",'<br>',$_smarty_tpl->tpl_vars['COMMENT']->value->get('commentcontent'))))));?>
<?php }else{ ?><?php echo preg_replace("/(\\n+)/",'<br>',$_smarty_tpl->tpl_vars['COMMENT']->value->get('commentcontent'));?>
<?php }?></div></div><div class="row-fluid"><div class="pull-right commentActions"><span><button class="btn alertComment" data-name="JobAlerts" type="button" data-url="index.php?module=JobAlerts&amp;view=Boxs&amp;mode=setJobAlerts&amp;src_record=<?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->getId();?>
&amp;accountid=<?php echo $_smarty_tpl->tpl_vars['ACCOUNTID']->value;?>
"><strong>提醒</strong></button>&nbsp;&nbsp;<button class="btn replyComment" data-name="ModComments" type="button" data-url="index.php?module=ModComments&amp;view=Boxs&amp;mode=setSubModComments&amp;src_record=<?php echo $_smarty_tpl->tpl_vars['COMMENT']->value->getId();?>
&amp;relateModule=Accounts"><strong>评论</strong></button></span><span><?php if ($_smarty_tpl->tpl_vars['PARENT_COMMENT_MODEL']->value!=false||$_smarty_tpl->tpl_vars['CHILD_COMMENTS_MODEL']->value!=null){?>&nbsp;<span>|</span>&nbsp;<a href="javascript:void(0);" class="cursorPointer detailViewThread"><?php echo vtranslate('LBL_VIEW_THREAD',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</a><?php }?></span></div></div><?php $_smarty_tpl->tpl_vars["COMMENT_ALERTS_ROWS"] = new Smarty_variable($_smarty_tpl->tpl_vars['COMMENT']->value->getAlerts(), null, 0);?><?php if (!empty($_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROWS']->value)){?><div class="alertInfoContent ">跟进提醒<?php  $_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->_loop = false;
 $_smarty_tpl->tpl_vars['his'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['COMMENT']->value->getAlerts(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->key => $_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->value){
$_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->_loop = true;
 $_smarty_tpl->tpl_vars['his']->value = $_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->key;
?><div class="bs-callout bs-callout-danger"><h4>主题：<a target="_blank" href="index.php?module=JobAlerts&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->value['jobalertsid'];?>
"><?php echo $_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->value['subject'];?>
</a></h4><h4>提醒时间：<?php echo $_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->value['alerttime'];?>
  提醒人:<?php echo $_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->value['username'];?>
 提醒状态:<?php echo vtranslate($_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->value['alertstatus'],'JobAlerts');?>
 优先级:<?php echo vtranslate($_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->value['taskpriority'],'JobAlerts');?>
</h4><?php echo nl2br($_smarty_tpl->tpl_vars['COMMENT_ALERTS_ROW']->value['alertcontent']);?>
</div><?php } ?></div><?php }?></div></div></div></div><div class="row-fluid commentActionsContainer"><div class="row-fluid"  name="editStatus"><div class="row-fluid pushUpandDown2per"><?php $_smarty_tpl->tpl_vars["PAGEHIS"] = new Smarty_variable(count($_smarty_tpl->tpl_vars['COMMENT']->value->getHistory()), null, 0);?><?php  $_smarty_tpl->tpl_vars['COMMENTHISTORY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['COMMENTHISTORY']->_loop = false;
 $_smarty_tpl->tpl_vars['his'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['COMMENT']->value->getHistory(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['COMMENTHISTORY']->key => $_smarty_tpl->tpl_vars['COMMENTHISTORY']->value){
$_smarty_tpl->tpl_vars['COMMENTHISTORY']->_loop = true;
 $_smarty_tpl->tpl_vars['his']->value = $_smarty_tpl->tpl_vars['COMMENTHISTORY']->key;
?><div class="bs-callout bs-callout-warning"><h4><?php echo $_smarty_tpl->tpl_vars['PAGEHIS']->value-$_smarty_tpl->tpl_vars['his']->value;?>
楼：<?php echo $_smarty_tpl->tpl_vars['COMMENTHISTORY']->value['createdbyer'];?>
创建于 <?php echo $_smarty_tpl->tpl_vars['COMMENTHISTORY']->value['createdtime'];?>
 <a class="replyComment" data-name="ModComments" href="javascript:void(0);" type="button" data-url="index.php?module=ModComments&amp;view=Boxs&amp;mode=setSubModComments&amp;src_record=<?php echo $_smarty_tpl->tpl_vars['COMMENTHISTORY']->value['ModCommentsid'];?>
&amp;record=<?php echo $_smarty_tpl->tpl_vars['COMMENTHISTORY']->value['id'];?>
&amp;relateModule=Accounts"><strong>修改</strong></a> <span style="color: grey"><?php if ($_smarty_tpl->tpl_vars['COMMENTHISTORY']->value['accountintentionality']!=''&&$_smarty_tpl->tpl_vars['COMMENTHISTORY']->value['accountintentionality']!='zeropercent'){?> <?php echo vtranslate('LBL_intentionality','ModComments');?>
 : <?php echo vtranslate($_smarty_tpl->tpl_vars['COMMENTHISTORY']->value['accountintentionality'],'Accounts');?>
<?php }?></span></h4><?php echo $_smarty_tpl->tpl_vars['COMMENTHISTORY']->value['modcommenthistory'];?>
</p><?php if (empty($_smarty_tpl->tpl_vars['COMMENTHISTORY']->value['modifiedcause'])==false){?><div class="bs-example"><h4>最后修改时间<?php echo $_smarty_tpl->tpl_vars['COMMENTHISTORY']->value['modifiedtime'];?>
 </h4>修改原因<?php echo $_smarty_tpl->tpl_vars['COMMENTHISTORY']->value['modifiedcause'];?>
</div><?php }?></div><?php } ?></div></div></div></div></div></div><?php } ?><?php }else{ ?><?php }?><div class="span2 pull-right""><?php if (!is_bool($_smarty_tpl->tpl_vars['PAGING_MODEL']->value->isNextPageExists())&&$_smarty_tpl->tpl_vars['COMMENTSCOUNTS']->value/$_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getCurrentPage()!=$_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getPageLimit()){?><div class="pull-right"><a href="javascript:void(0)" class="moreRecentComments nexttopage"><span class="btn"  title="下一页"><i class="icon-chevron-right" title="下一页"></i></a></div><?php }?><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getCurrentPage();?>
" class="nextpage" /><?php if (!is_bool($_smarty_tpl->tpl_vars['PAGING_MODEL']->value->isPrevPageExists())){?><div class="pull-right"><a href="javascript:void(0)" class="moreRecentComments uptopage"><span class="btn" title="上一页"><i class="icon-chevron-left" title="上一页"></i></span></a></div><?php }?></div><div style="clear:both;"></div></div></div><script>//Vtiger_Index_Js.registerTooltipEvents();</script><?php }} ?>