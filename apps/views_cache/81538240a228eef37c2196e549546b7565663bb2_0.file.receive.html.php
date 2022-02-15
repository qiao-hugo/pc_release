<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-19 16:57:49
  from "/data/httpd/vtigerCRM/apps/views/RefillApplication/receive.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a38d48d704cd9_49914804',
  'file_dependency' => 
  array (
    '81538240a228eef37c2196e549546b7565663bb2' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/RefillApplication/receive.html',
      1 => 1488937006,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a38d48d704cd9_49914804 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>充值申请单回款</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="static/css/jquery.mobile-1.4.5.min.css" />
    <link href="static/css/select2.css" rel="stylesheet" type="text/css" />
    
	<link href="static/css/font-awesome.min.css" rel="stylesheet">
	<link href="static/css/Style.css" rel="stylesheet">
	<link href="static/css/common.css" rel="stylesheet">
    
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
        <h1>充值申请单回款</h1>
        <a href="#demo-intro" data-rel="back" data-transition="slide" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
        </a>
    </div>

    <div class="container-fluid w fix">
        
        <div class="row">
        	<div class="to-do-audit">
                <ul>
                    	<?php
$_from = $_smarty_tpl->tpl_vars['receiveData']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_0_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_0_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_0_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>				
    					<li>
                            
                            <div class="text">
                                <div class="fix">
                                	<div class="fl">汇款抬头：<?php echo $_smarty_tpl->tpl_vars['value']->value['paytitle'];?>
</div>
                                    <div class="fl">公司账号：<?php echo $_smarty_tpl->tpl_vars['value']->value['owncompany'];?>
</div>
                                    <div class="fl">回款时间：<?php echo $_smarty_tpl->tpl_vars['value']->value['reality_date'];?>
</div>
                                    <div class="fl">金额：<?php echo $_smarty_tpl->tpl_vars['value']->value['unit_price'];?>
</div>
                                </div>
                            </div>
                            <div class="list">创建时间：<?php echo $_smarty_tpl->tpl_vars['value']->value['createdtime'];?>
</div>
                        </li>
    					<?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_local_item;
}
}
if ($__foreach_value_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_item;
}
?>
                </ul>
            </div>
        </div>
        
   </div>




    
   
   
</div>

</body>
</html><?php }
}
