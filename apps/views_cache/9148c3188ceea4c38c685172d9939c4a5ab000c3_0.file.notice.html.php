<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-18 11:07:58
  from "/data/httpd/vtigerCRM/apps/views/main/notice.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a37310e508856_85307826',
  'file_dependency' => 
  array (
    '9148c3188ceea4c38c685172d9939c4a5ab000c3' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/main/notice.html',
      1 => 1510568120,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a37310e508856_85307826 ($_smarty_tpl) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
		<title>消息中心</title>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</head>

<body>


<div class="container-fluid w fix message">
        <div class="row">
           
            <div class="message-box">
                <ul class="fix">
                    <li>
                        <a href="index.php?module=VisitingOrder&action=unaudited">
                            <div class="circle"><img src="static/img/a3.png" /></div>
                            <div class="text">待办提醒<span>(<?php echo $_smarty_tpl->tpl_vars['dgjsum']->value;?>
)</span></div>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?module=VisitingOrder&action=pass">
                            <div class="circle"><img src="static/img/a4.png" /></div>
                            <div class="text">待审核提醒<span>(<?php echo $_smarty_tpl->tpl_vars['dshsum']->value;?>
)</span></div>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?module=Knowledge&action=notice">
                            <div class="circle"><img src="static/img/a5.png" /></div>
                            <div class="text">公告<span></span></div>
                        </a>
                    </li>
                </ul>
                <ul class="fix">
                    <li>
                        <a href="index.php?module=ExtensionTrial&action=getExtendedReminder">
                            <div class="circle"><img src="static/img/a5.png" /></div>
                            <div class="text">合同超期提醒<span></span></div>
                        </a>
                    </li>
                    <li>
                        
                    </li>
                    <li>
                        
                    </li>


                </ul>
            </div>
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        </div>
    </div>

	
</body>
</html><?php }
}
