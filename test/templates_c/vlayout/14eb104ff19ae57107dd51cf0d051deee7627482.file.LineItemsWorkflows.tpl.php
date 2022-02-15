<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 16:27:48
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\OrderChargeback\LineItemsWorkflows.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7876620b6404d5a9d6-52515981%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '14eb104ff19ae57107dd51cf0d051deee7627482' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\OrderChargeback\\LineItemsWorkflows.tpl',
      1 => 1630399636,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7876620b6404d5a9d6-52515981',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'WORKFLOWSNAME' => 0,
    'ISROLE' => 0,
    'STAGERECORDID' => 0,
    'DATA' => 0,
    'STAGES' => 0,
    'vals' => 0,
    'val' => 0,
    'SCHEDULE' => 0,
    'WORKFLOWSSTAGELIST' => 0,
    'value' => 0,
    'canChangeAuditor' => 0,
    'SALESORDERHISTORY' => 0,
    'REMARKLIST' => 0,
    'PROJECTNAME' => 0,
    'projectname' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b6404e0f66',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b6404e0f66')) {function content_620b6404e0f66($_smarty_tpl) {?>
<style>.n_hauto { height:20px;overflow:hidden;display: block;padding-left:4px;}</style><table class="table table-bordered "><thead><th colspan="1" class="detailViewBlockHeader"><?php echo vtranslate('LBL_WORKSTAGES_INFO','SalesOrder');?>
<?php if (isset($_smarty_tpl->tpl_vars['WORKFLOWSNAME']->value)){?>---<?php echo $_smarty_tpl->tpl_vars['WORKFLOWSNAME']->value;?>
<?php }?></th><th colspan="2" class="detailViewBlockHeader" style="text-align:right"><div class="form-inline"><?php if ($_smarty_tpl->tpl_vars['ISROLE']->value&&$_smarty_tpl->tpl_vars['STAGERECORDID']->value){?><?php if (!empty($_smarty_tpl->tpl_vars['DATA']->value)){?><input name="datamodule" id="datamodule" type="hidden" disabled value="<?php echo $_smarty_tpl->tpl_vars['DATA']->value['module'];?>
"/><input name="datamodulerecord" id="datamodulerecord" type="hidden" disabled  value="<?php echo $_smarty_tpl->tpl_vars['DATA']->value['record'];?>
"/><?php }?>&nbsp;<div class="btn-group"><button type="button" class="btn stagesubmit">审核</button></div><?php }?><!--&nbsp;<button class="btn stagereset" data-name="SalesOrder" type="button" data-url="index.php" id="rejectbutton">打回</button>&nbsp;<button class="btn stagereset" data-name="SalesOrder" type="button" data-url="index.php" id="remarkbutton">备注</button>&nbsp;<button class="btn stagereset" data-name="SalesOrder" type="button" data-url="index.php" id="SalesorderProjectTasksrel">工单项目任务</button>-->&nbsp;</div></th></thead><tbody><tr><td colspan="3"><div style="padding:5px;"><?php $_smarty_tpl->tpl_vars['SCHEDULE'] = new Smarty_variable('0', null, 0);?><?php $_smarty_tpl->tpl_vars['actiontime'] = new Smarty_variable('-', null, 0);?><ul class="nav nav-pills"><?php  $_smarty_tpl->tpl_vars['vals'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['vals']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['STAGES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['vals']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['vals']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['vals']->key => $_smarty_tpl->tpl_vars['vals']->value){
$_smarty_tpl->tpl_vars['vals']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['vals']->key;
 $_smarty_tpl->tpl_vars['vals']->iteration++;
 $_smarty_tpl->tpl_vars['vals']->last = $_smarty_tpl->tpl_vars['vals']->iteration === $_smarty_tpl->tpl_vars['vals']->total;
?><li style="float: none;vertical-align: middle;display: inline-block;"><?php  $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['val']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['vals']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['val']->key => $_smarty_tpl->tpl_vars['val']->value){
$_smarty_tpl->tpl_vars['val']->_loop = true;
?><span class="label <?php if ($_smarty_tpl->tpl_vars['val']->value['isaction']==2){?> label-inverse <?php }elseif($_smarty_tpl->tpl_vars['val']->value['check']==1){?> label-success <?php }elseif($_smarty_tpl->tpl_vars['val']->value["isaction"]==1){?> label-info<?php }?> "title="<?php echo $_smarty_tpl->tpl_vars['val']->value['actiontime'];?>
"><?php echo $_smarty_tpl->tpl_vars['val']->value["workflowstagesname"];?>
</span><?php if ($_smarty_tpl->tpl_vars['val']->value['isaction']==1){?><?php $_smarty_tpl->tpl_vars['SCHEDULE'] = new Smarty_variable($_smarty_tpl->tpl_vars['val']->value['schedule'], null, 0);?><?php if ($_smarty_tpl->tpl_vars['val']->value['check']==1){?><input id="stagerecordid" type="hidden"value="<?php echo $_smarty_tpl->tpl_vars['val']->value['salesorderworkflowstagesid'];?>
"/><input id="stagerecordname" type="hidden"value="<?php echo $_smarty_tpl->tpl_vars['val']->value['workflowstagesname'];?>
"/><?php $_smarty_tpl->tpl_vars['actiontime'] = new Smarty_variable($_smarty_tpl->tpl_vars['val']->value["actiontime"], null, 0);?><?php }?><?php }?><br><?php } ?></li><?php if ($_smarty_tpl->tpl_vars['vals']->last){?><?php }else{ ?><li style="float: none;vertical-align: middle;display: inline-block;"><i class="icon-arrow-right" style=""></i></li><?php }?><?php } ?></ul></div><script>$(function () {$('.schedule').val(<?php echo $_smarty_tpl->tpl_vars['SCHEDULE']->value;?>
);$('.schedule option').each(function () {if ($(this).val() <=<?php echo $_smarty_tpl->tpl_vars['SCHEDULE']->value;?>
) {$(this).attr('disabled', 'disabled');}});});</script><!-- 2015年3月31日 星期二 注释<div class="progress progress-info"><div class="bar" data-schedule="<?php echo $_smarty_tpl->tpl_vars['SCHEDULE']->value;?>
" style="width: <?php echo $_smarty_tpl->tpl_vars['SCHEDULE']->value;?>
%"><?php echo $_smarty_tpl->tpl_vars['SCHEDULE']->value;?>
%</div></div>--></td></tr><tr><td colspan="3">图例说明：<span class="label  label-inverse" title="">已经审核的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-success" title="">正在审核(有权限)的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-info" title="">正在审核(无权限)的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="label" title="">即将审核的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></tbody></table><!-- wangbin 注释 打回到指定节点--><table class="table table-bordered mergeTables detailview-table "><thead><tr><th class="detailViewBlockHeader" ><img class="cursorPointer alignMiddle blockToggle  hide  "src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="jd61"style="display: none;"><img class="cursorPointer alignMiddle blockToggle "src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="jd61">节点名称</th><th class="detailViewBlockHeader" >状态</th><th class="detailViewBlockHeader" >激活时间</th><th class="detailViewBlockHeader" >角色</th><th class="detailViewBlockHeader" >产品负责人</th><th class="detailViewBlockHeader" >负责人</th><th class="detailViewBlockHeader" >审核人</th><th class="detailViewBlockHeader" >审核时间</th><th class="detailViewBlockHeader" >日期</th></tr></thead><?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['WORKFLOWSSTAGELIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['value']->key;
?><tr"><td ><?php echo $_smarty_tpl->tpl_vars['value']->value["workflowstagesname"];?>
</td><td ><?php echo $_smarty_tpl->tpl_vars['value']->value["actionstatus"];?>
</td><td ><?php echo $_smarty_tpl->tpl_vars['value']->value["actiontime"];?>
</td><td ><?php echo $_smarty_tpl->tpl_vars['value']->value["isrole"];?>
</td><td ><a><i class="icon-hand-right pull-left hide" style="margin-top:4px">&nbsp;</i><span class="n_hauto" data-trigger="hover" data-content="<?php echo $_smarty_tpl->tpl_vars['value']->value["productid"];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value["productid"];?>
</span></a></td><td ><a><i class="icon-hand-right pull-left hide" style="margin-top:4px">&nbsp;</i><span class="n_hauto <?php if ($_smarty_tpl->tpl_vars['canChangeAuditor']->value){?>auditorid<?php }else{ ?>noauditorid<?php }?>" data-workflowstagesname="<?php echo $_smarty_tpl->tpl_vars['value']->value['workflowstagesname'];?>
" data-salesorderworkflowstagesid="<?php echo $_smarty_tpl->tpl_vars['value']->value['salesorderworkflowstagesid'];?>
" data-trigger="hover" data-content="<?php echo $_smarty_tpl->tpl_vars['value']->value["higherid"];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value["higherid"];?>
</span></a></td><td ><?php echo $_smarty_tpl->tpl_vars['value']->value["auditorid"];?>
</td><td ><?php echo $_smarty_tpl->tpl_vars['value']->value["auditortime"];?>
</td><td ><?php echo $_smarty_tpl->tpl_vars['value']->value["createdtime"];?>
</td></tr><?php } ?></table><br><table class="table table-bordered equalSplit detailview-table "><thead><?php if ($_smarty_tpl->tpl_vars['ISROLE']->value&&$_smarty_tpl->tpl_vars['STAGERECORDID']->value){?><tr><td colspan="3"><textarea class="row-fluid" required="required" id="rejectreason" placeholder="请输入打回原因"></textarea></td><td><div class="pull-right"><button type="button" class="btn btn-warning" id="realstagereset">打回 </button></div></td></tr><?php }?><tr><th class="detailViewBlockHeader"><img class="cursorPointer alignMiddle blockToggle hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="61" style="display: none;"><img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="61">操作人</th><th class="detailViewBlockHeader">从节点打回</th><th class="detailViewBlockHeader">原因</th><th class="detailViewBlockHeader">日期</th></tr></thead><?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['SALESORDERHISTORY']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['value']->key;
?><tr><td nowrap><?php echo $_smarty_tpl->tpl_vars['value']->value["last_name"];?>
</td><td nowrap><?php echo $_smarty_tpl->tpl_vars['value']->value["rejectnameto"];?>
</td><td nowrap><?php echo $_smarty_tpl->tpl_vars['value']->value["reject"];?>
</td><td nowrap><?php echo $_smarty_tpl->tpl_vars['value']->value["rejecttime"];?>
</td></tr><?php } ?></table><table class="table table-bordered equalSplit detailview-table "><thead><?php if ($_smarty_tpl->tpl_vars['ISROLE']->value&&$_smarty_tpl->tpl_vars['STAGERECORDID']->value){?><tr class="hide realremarkbutton"><td colspan="3"><textarea class="row-fluid remark" id="remarkvalue" required="required" placeholder="请输入备注信息" name="description" ></textarea></td><td colspan="2"><div class="pull-right"><button type="button" id="realremarkbutton" class="btn btn-success"><strong>保存</strong></button><a class="cancelLink" type="reset" onclick="$('.realremarkbutton').hide();">取消</a></div></td></tr><?php }?><tr><th class="detailViewBlockHeader" nowrap><img class="cursorPointer alignMiddle blockToggle hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="61" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="61">操作人</th><!--<div class="btn-group"><button type="button" class="btn stagesubmit">审核</button></div><th class="detailViewBlockHeader" nowrap>备注节点</th>--><th class="detailViewBlockHeader" nowrap>备注原因</th><th class="detailViewBlockHeader" nowrap>创建日期</th><th class="detailViewBlockHeader" nowrap>修改时间</th><th class="detailViewBlockHeader" nowrap><?php if ($_smarty_tpl->tpl_vars['ISROLE']->value&&$_smarty_tpl->tpl_vars['STAGERECORDID']->value){?><div class="pull-right"><button type="button" onclick="$('.realremarkbutton').show();$('#remarkvalue').focus();" class="btn btn-info">添加备注</button>&nbsp;</div><?php }?></th></tr></thead><?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['REMARKLIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['value']->key;
?><tr><td nowrap><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['salesorderhistoryid'];?>
"class="remarkid"><?php echo $_smarty_tpl->tpl_vars['value']->value["last_name"];?>
</td><td nowrap><?php echo $_smarty_tpl->tpl_vars['value']->value["reject"];?>
</td><td nowrap><?php echo $_smarty_tpl->tpl_vars['value']->value["rejecttime"];?>
</td><td nowrap><?php echo $_smarty_tpl->tpl_vars['value']->value["modifytime"];?>
</td><td id="editremark" nowrap></td></tr><?php } ?></table><div id="projectselectdiv" style="display:none;"><select id="projectselect"><option>--请选择项目模版--</option><?php  $_smarty_tpl->tpl_vars['projectname'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['projectname']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['PROJECTNAME']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['projectname']->key => $_smarty_tpl->tpl_vars['projectname']->value){
$_smarty_tpl->tpl_vars['projectname']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['projectname']->value['0'];?>
"><?php echo $_smarty_tpl->tpl_vars['projectname']->value['1'];?>
</option><?php } ?></select><button type="button" id="realSalesorderProjectTasksrel">工单任务生成</button></div><div id="protaskform" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"aria-hidden="true" style="z-index:1000006">准备测试数据。。。</div><!-- Button to trigger modal --><div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"aria-hidden="true" style="z-index:1000006"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="myModalLabel">修改备注信息</h3></div><div class="modal-body"><p><textarea id="editremarkval" placeholder="键入内容修改备注" style="width:100%"></textarea></p></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button><button id="realeditremark" class="btn btn-primary" data-dismiss="modal" aria-hidden="true">确认修改</button></div></div><script type="text/javascript">$('.n_hauto').popover();$('img[data-id="jd61"]').trigger('click');$('.icon-hand-right').each(function(){if($(this).next('span').html().indexOf('br')>-1){$(this).show();}})</script>
<?php }} ?>