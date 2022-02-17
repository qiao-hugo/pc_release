<?php
/* Smarty version 3.1.28-dev/79, created on 2018-03-15 15:58:41
  from "/data/httpd/vtigerCRM/apps/views/RefillApplication/one.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5aaa27b10893e6_12834073',
  'file_dependency' => 
  array (
    '20b6c2c782c702bd7491940a22d508d3d3f757cb' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/RefillApplication/one.html',
      1 => 1521100715,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5aaa27b10893e6_12834073 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>充值申请单详情</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="static/css/jquery.mobile-1.4.5.min.css" />
    <link href="static/css/select2.css" rel="stylesheet" type="text/css" />
    <?php echo '<script'; ?>
 type="text/javascript" src="static/js/jquery-2.1.0.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="static/js/jquery.mobile-1.4.5.min.js"><?php echo '</script'; ?>
>
    <style type="text/css">
        
        *{
            text-shadow:none;
        }
        .sales_title_t{
            font-size: 12px;
            font-weight:bold;
        }
        .sales_info_div{
             font-size: 12px;

            margin-bottom: 8px;
        }

        #bg{ display: none;  position: absolute;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.5;  opacity:.50;  filter: alpha(opacity=50);}  
        

    </style>
</head>
<body>

<div class="container-fluid w fix" id="demo-intro" data-role="page">
    <div data-role="header" data-position="fixed">
        <h1>充值申请单详情</h1>
        <a href="#demo-intro" data-rel="back" data-transition="slide" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
        </a>
        <?php if ($_smarty_tpl->tpl_vars['ISROLE']->value == '1') {?>
        <a href="#myPopupDialog" data-rel="popup"  id="gotoMyPopupDialog" data-position-to="window" data-transition="fade" class="ui-btn ui-corner-all ui-shadow ui-btn-inline">审核</a>
        <?php }?>
    </div>

    <div data-role="main" class="ui-content">
        <div id="data_list"  data-role="collapsible-set">

                <div  class="selector" data-role="collapsible"  data-collapsed="false">
                    <h3>基本信息</h3>
                    <div class="sales_info_div">
                        <span class="sales_title_t">申请单编号:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['refillapplicationno'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">申请人:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['last_name'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">申请时间:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['refillcreatedtime'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">服务合同:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['contract_no'];?>
 <a href="index.php?module=RefillApplication&action=receive&id=<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['servicecontractsid'];?>
">回款</a>
                    </div>
                    <?php if ($_smarty_tpl->tpl_vars['advancesmoney']->value > 0) {?>
                    <div class="sales_info_div">
                        <span class="sales_title_t">已垫款:</span>
                        <?php echo $_smarty_tpl->tpl_vars['advancesmoney']->value;?>

                    </div>
                    <?php }?>
                    <div class="sales_info_div">
                        <span class="sales_title_t">客户:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['accountname'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">附件:</span>
                        <?php if (!empty($_smarty_tpl->tpl_vars['refillApplication']->value['file'])) {?>
                        <?php $_smarty_tpl->tpl_vars['expfile'] = new Smarty_Variable(explode('*|*',$_smarty_tpl->tpl_vars['refillApplication']->value['file']), null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'expfile', 0);?>
                        <?php
$_from = $_smarty_tpl->tpl_vars['expfile']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_filename_0_saved_item = isset($_smarty_tpl->tpl_vars['filename']) ? $_smarty_tpl->tpl_vars['filename'] : false;
$_smarty_tpl->tpl_vars['filename'] = new Smarty_Variable();
$__foreach_filename_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_filename_0_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['filename']->value) {
$__foreach_filename_0_saved_local_item = $_smarty_tpl->tpl_vars['filename'];
?>
                        <?php $_smarty_tpl->tpl_vars['expfilename'] = new Smarty_Variable(explode('##',$_smarty_tpl->tpl_vars['filename']->value), null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'expfilename', 0);?>
                        <a href="index.php?module=RefillApplication&action=download&filename=<?php echo urlencode(base64_encode($_smarty_tpl->tpl_vars['expfilename']->value[1]));?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['expfilename']->value[0];?>
</a><br>
                        <?php
$_smarty_tpl->tpl_vars['filename'] = $__foreach_filename_0_saved_local_item;
}
}
if ($__foreach_filename_0_saved_item) {
$_smarty_tpl->tpl_vars['filename'] = $__foreach_filename_0_saved_item;
}
?>
                        <?php }?>
                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">备注:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['remarks'];?>

                    </div>
                    <hr style="height:2px;border:none;border-top:2px dotted #185598;">
                    <div class="sales_info_div">
                        <span class="sales_title_t">充值平台:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['topplatform'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">账户:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['accountzh'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">ID:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['did'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">类型:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['t_rechargetype'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">货币类型:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['receivementcurrencytype'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">汇率:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['exchangerate'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">收款金额:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['rechargeamount'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">充值金额:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['prestoreadrate'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">返点:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['discount'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">税点:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['tax'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">代理商服务费:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['factorage'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">开户费:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['activationfee'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">合计费用:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['totalcost'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">每日预算:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['dailybudget'];?>

                    </div>



                    <div class="sales_info_div">
                        <span class="sales_title_t">转款金额:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['transferamount'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">返点金额:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['rebateamount'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">毛利总计:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['totalgrossprofit'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">服务成本:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['servicecost'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">状态:</span>
                        <?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['mstatus'];?>

                    </div>

                    
                </div>

                <?php if (!empty($_smarty_tpl->tpl_vars['rechargesheet']->value)) {?>
                <?php
$_from = $_smarty_tpl->tpl_vars['rechargesheet']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_wlist_1_saved = isset($_smarty_tpl->tpl_vars['__smarty_foreach_wlist']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_wlist'] : false;
$__foreach_wlist_1_saved_item = isset($_smarty_tpl->tpl_vars['refillApplication']) ? $_smarty_tpl->tpl_vars['refillApplication'] : false;
$_smarty_tpl->tpl_vars['refillApplication'] = new Smarty_Variable();
$__foreach_wlist_1_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
$_smarty_tpl->tpl_vars['__smarty_foreach_wlist'] = new Smarty_Variable(array('index' => -1));
if ($__foreach_wlist_1_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['refillApplication']->value) {
$_smarty_tpl->tpl_vars['__smarty_foreach_wlist']->value['index']++;
$__foreach_wlist_1_saved_local_item = $_smarty_tpl->tpl_vars['refillApplication'];
?>
                <div  class="selector" data-role="collapsible">
                    <h3 >充值明细<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_foreach_wlist']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_wlist']->value['index'] : null)+1;?>
 </h3>
                    
					<div class="sales_info_div">
						<span class="sales_title_t">充值平台:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['topplatform'];?>

					</div>
					<div class="sales_info_div" style="white-space:normal;">
						<span class="sales_title_t">账户:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['accountzh'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">ID:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['did'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">类型:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['t_rechargetype'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">货币类型:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['receivementcurrencytype'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">汇率:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['exchangerate'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">充值金额:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['rechargeamount'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">预存广告费:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['prestoreadrate'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">折扣:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['discount'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">税点:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['tax'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">代理商服务费:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['factorage'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">开户费:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['activationfee'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">合计费用:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['totalcost'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">每日预算:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['dailybudget'];?>

					</div>

					<div class="sales_info_div">
						<span class="sales_title_t">转款金额:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['transferamount'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">返点金额:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['rebateamount'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">毛利总计:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['totalgrossprofit'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">服务成本:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['servicecost'];?>

					</div>
					<div class="sales_info_div">
						<span class="sales_title_t">状态:</span>
						<?php echo $_smarty_tpl->tpl_vars['refillApplication']->value['mstatus'];?>

					</div>
                               
                </div>
                <?php
$_smarty_tpl->tpl_vars['refillApplication'] = $__foreach_wlist_1_saved_local_item;
}
}
if ($__foreach_wlist_1_saved) {
$_smarty_tpl->tpl_vars['__smarty_foreach_wlist'] = $__foreach_wlist_1_saved;
}
if ($__foreach_wlist_1_saved_item) {
$_smarty_tpl->tpl_vars['refillApplication'] = $__foreach_wlist_1_saved_item;
}
?>
                <?php }?>

                

                <div  class="selector" data-role="collapsible"  data-collapsed="false">
                    <h3>工作流审核-<?php echo $_smarty_tpl->tpl_vars['STAGERECORDNAME']->value;?>
</h3>


                    <div style="font-weight:bold;">审核节点：</div>
                    <ul id="data_list" data-role="listview" data-inset="true">
                        <?php
$_from = $_smarty_tpl->tpl_vars['WORKFLOWSSTAGELIST']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_2_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_2_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_2_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_2_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>  
                        <li>
                            <a data-role="button" style="font-size:12px; font-weight:normal; " <?php if ($_smarty_tpl->tpl_vars['value']->value['isaction'] == '1') {?>class="workflowstages_isaction"<?php }?> data-id="<?php echo $_smarty_tpl->tpl_vars['value']->value['salesorderworkflowstagesid'];?>
" data-transition="slide"  href="#"><?php echo $_smarty_tpl->tpl_vars['value']->value['workflowstagesname'];?>
【<?php echo $_smarty_tpl->tpl_vars['value']->value['actionstatus'];?>
】<?php if ($_smarty_tpl->tpl_vars['value']->value['auditorid'] != '--') {?><br />审核人:<?php echo $_smarty_tpl->tpl_vars['value']->value['auditorid'];?>
<br />审核时间:<?php echo $_smarty_tpl->tpl_vars['value']->value['auditortime'];
}?></a>
                        </li>
                        <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_2_saved_local_item;
}
}
if ($__foreach_value_2_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_2_saved_item;
}
?>
                    </ul>

                    <?php if (!empty($_smarty_tpl->tpl_vars['SALESORDERHISTORY']->value)) {?>
                    <div style="font-weight:bold; padding-top: 15px;">历史打回原因：</div>
                    <ul id="" data-role="listview" data-inset="true">
                        <?php
$_from = $_smarty_tpl->tpl_vars['SALESORDERHISTORY']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_3_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_3_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_3_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_3_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                        <li><a data-role="button" href="#" style="font-size:12px; font-weight:normal; "><?php echo $_smarty_tpl->tpl_vars['value']->value['reject'];?>
【<?php echo $_smarty_tpl->tpl_vars['value']->value['last_name'];?>
】</a></li>
                        <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_3_saved_local_item;
}
}
if ($__foreach_value_3_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_3_saved_item;
}
?>
                    </ul>
                    <?php }?>
                    <?php if (!empty($_smarty_tpl->tpl_vars['REMARKLIST']->value)) {?>
                    <div style="font-weight:bold; padding-top: 15px;">备注：</div>
                    <ul id="remarkslist" data-role="listview" data-split-icon="gear" data-split-theme="a" data-inset="true">

                        <?php
$_from = $_smarty_tpl->tpl_vars['REMARKLIST']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_4_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_4_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_4_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_4_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                        <?php $_smarty_tpl->tpl_vars['IMGMD'] = new Smarty_Variable(md5($_smarty_tpl->tpl_vars['value']->value['email1']), null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'IMGMD', 0);?>
                        <li class="ui-field-contain"><a data-role="button" href="#"><img src="<?php if (isset($_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value])) {
echo $_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value];
} else { ?>../../static/img/trueland.png<?php }?>">
                            <h2><?php echo $_smarty_tpl->tpl_vars['value']->value['reject'];?>
</h2><p><?php echo $_smarty_tpl->tpl_vars['value']->value['last_name'];?>
  <?php echo $_smarty_tpl->tpl_vars['value']->value['rejecttime'];?>
</p></a></li>
                        <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_4_saved_local_item;
}
}
if ($__foreach_value_4_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_4_saved_item;
}
?>
                    </ul>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['ISROLE']->value == '1') {?>
                        <form method="post" onsubmit='return t_submit()'>
                        <div class="ui-field-contain">
                            <input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['record']->value;?>
">
                            <input type="hidden" name="stagerecordid" value="<?php echo $_smarty_tpl->tpl_vars['STAGERECORDID']->value;?>
">
                            <input type="hidden" name="stagerecordname" value="<?php echo $_smarty_tpl->tpl_vars['STAGERECORDNAME']->value;?>
">
                            <textarea placeholder="输入打回原因" name="repulseinfo" id="repulseinfo" rows="5" class="form-control"
                              data-content=""></textarea>
                            <div class="confirm tc">
                                <button class="ui-btn ui-btn-b ui-shadow ui-corner-all">打回</button>
                            </div>
                        </div>
                        </form>
                    <div class="ui-field-contain">

                        <textarea placeholder="输入备注信息" name="remarks" id="remarks" rows="5"
                                  data-content=""></textarea>
                        <div class="confirm tc">
                            <button class="ui-btn ui-btn-c ui-shadow ui-corner-all addremarks">添加备注</button>
                        </div>
                    </div>
                    <?php }?>
                </div>

                


        </div>
        
   </div>
    <div data-role="popup" data-dismissible="false" id="refillApplication_examine_page_popup" >
        <div>正在提交...</div>
    </div>
    <div data-role="popup" data-dismissible="false" id="refillApplication_remarks_page_popup" >
        <div>备注信息不能为空</div>
    </div>

     <div data-role="popup" id="myPopupDialog">
      <div data-role="header">
        <h1>提醒</h1>
      </div>
      <div data-role="main" class="ui-content" style="text-align: right;">
        <a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn ui-icon-delete ui-btn-icon-notext ui-btn-right">关闭</a>
        <p>确定要审核当前的节点<?php echo $_smarty_tpl->tpl_vars['STAGERECORDNAME']->value;?>
</p>
        <a href="javascript:void(0)" id="refillApplication_examine" class="ui-btn ui-btn-inline ui-mini ui-icon-action ui-btn-icon-left" >确定</a>
      </div>
    </div> 

    
    <div id="bg"></div> 
    <?php echo '<script'; ?>
 type="text/javascript">
    
    $(function(){
        
        // 审核
        $('#refillApplication_examine').click(function () {
            var stagerecordid = $('input[name=stagerecordid]').val();
            var record = $('input[name=record]').val();
            $('#myPopupDialog').popup('close');
            $.ajax({ 
                url: "index.php?module=RefillApplication&action=examine", 
                data: {
                    stagerecordid : stagerecordid,
                    record : record
                },
                type:'POST',
                beforeSend:function() {
                    mark('#refillApplication_examine_page_popup', 'show');
                },
                success: function(data){
                    alert('审核成功');
                    mark('#refillApplication_examine_page_popup', 'none');

                    setTimeout(function() {
                        window.location.reload(); 
                    }, 100);
                }
            });

        });
        //添加备注
        $('.addremarks').on('click',function(){
            var stagerecordid = $('input[name=stagerecordid]').val();
            var record = $('input[name=record]').val();
            var remarks=$('#remarks').val();
            if(remarks==''){
                mark('#refillApplication_remarks_page_popup', 'show');
                setTimeout("mark('#refillApplication_remarks_page_popup', 'none')",2000);
                return false;
            }
            $.ajax({
                url: "index.php?module=RefillApplication&action=submitremark",
                data: {
                    stagerecordid : stagerecordid,
                    record : record,
                    reject : remarks
                },
                type:'POST',
                beforeSend:function() {
                    mark('#refillApplication_examine_page_popup', 'show');
                },
                success: function(data){
                    data = $.parseJSON( data );
                    mark('#refillApplication_examine_page_popup', 'none');
                    if (data.success) {
                        alert('备注添加成功');
                        setTimeout(function() {
                            window.location.reload();
                        }, 100);
                    }
                }
            });
        });
    });


    

    <?php echo '</script'; ?>
>
</div>

<?php echo '<script'; ?>
 type="text/javascript">
    //遮罩层提示
    var mark = function(page_mark, type) {
        if(type == 'show') {
            //加载一个遮罩层
            $(page_mark).popup('open');
            document.getElementById("bg").style.display="block";  
            $('html,body').animate({scrollTop: '0px'}, 100);
            $('#bg').bind("touchmove",function(e){  
                e.preventDefault();  
            });
        } else {
            $(page_mark).popup('close');
            document.getElementById("bg").style.display="none";  
        }
    };

    // 打回
    function t_submit() {
        var repulseinfo = $.trim($('#repulseinfo').val());
        if (repulseinfo) {
            var stagerecordid = $('input[name=stagerecordid]').val();
            var record = $('input[name=record]').val();
            var isbackname = $('input[name=stagerecordname]').val();
            $.ajax({ 
                url: "index.php?module=RefillApplication&action=repulse", 
                data: {
                    stagerecordid : stagerecordid,
                    record : record,
                    repulseinfo : repulseinfo,
                    isbackname: isbackname
                },
                type:'POST',
                beforeSend:function() {
                    mark('#refillApplication_examine_page_popup', 'show');
                },
                success: function(data){
                    data = $.parseJSON( data );
                    mark('#refillApplication_examine_page_popup', 'none');
                    if (data.success) {
                        alert('打回成功');
                        setTimeout(function() {
                        window.location.reload(); 
                        }, 100);
                    }
                }
            });
        }
        return false;
    }
<?php echo '</script'; ?>
>

</body>
</html><?php }
}
