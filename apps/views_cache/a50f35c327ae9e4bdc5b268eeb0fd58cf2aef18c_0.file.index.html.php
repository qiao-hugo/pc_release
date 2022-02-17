<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-18 11:55:53
  from "/data/httpd/vtigerCRM/apps/views/QrcodeLogin/index.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a373c49415795_06636301',
  'file_dependency' => 
  array (
    'a50f35c327ae9e4bdc5b268eeb0fd58cf2aef18c' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/QrcodeLogin/index.html',
      1 => 1513230393,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
  ),
),false)) {
function content_5a373c49415795_06636301 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
		<title>扫码登陆</title>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        
    <style type="text/css">
    html,body{
    	height:100%;
    }
.rowFrame{
	overflow-y:auto;
	    min-height: 50%;
}
        *{
            text-shadow:none;
        }

        .add-visit label{
        	width: 27%;
        }
        .add-visit .input-box{
        	width: 71%;
        }
        .form-group{
        margin-bottom:10px;
        }
    </style>
    
</head>
<body>

<div class="container-fluid w fix my-crm">
    <div class="row">

        <div style="height:150px;width:150px;margin: 10% auto 5% auto;background-size: 100% 100%;border: 1px solid #eee;border-radius:150px;overflow: hidden;"><img src="<?php echo $_smarty_tpl->tpl_vars['wximg']->value;?>
" style="height:150px;width:150px;"/></div>
        <div class="tc nemo-text"><?php echo $_smarty_tpl->tpl_vars['lastname']->value;?>
</div>
        <div class="confirm tc">
            <button class="btn btn2" id='doconfirm' data-type="confirm"> 单击确认</button>
        </div>
        <div class="confirm tc">
            <button class="btn btn1" id='docancel' data-type="cancel" >取消</button>
        </div>
    </div>
</div>
<?php echo '<script'; ?>
 type="text/javascript">

    $('#doconfirm,#docancel').click(function(){
        var type=$(this).data('type');
        sendMobileVerify(type);
    });
 	function sendMobileVerify(type){
 		$.ajax({
	         url: "/index.php?module=QrcodeLogin&action=doset&loginid=<?php echo $_smarty_tpl->tpl_vars['loginid']->value;?>
&status="+type,
	         type: 'GET',
	         success: function (data) {
	         	window.location.href="/index.php?module=VisitingOrder&action=vlist";
	         }
	     });
 	}
    $(function() {
        if (window.history && window.history.pushState) {
            $(window).on('popstate', function () {
                window.history.pushState('forward', null, '#');
                window.history.forward(1);
            });
        }
        window.history.pushState('forward', null, '#'); //在IE中必须得有这两行
        window.history.forward(1);
    })
 <?php echo '</script'; ?>
>
</body>
</html><?php }
}
