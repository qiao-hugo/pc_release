<?php
/* Smarty version 3.1.28-dev/79, created on 2018-04-09 09:54:00
  from "/data/httpd/vtigerCRM/apps/views/main/mycrm.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5acac7b81c3884_85486278',
  'file_dependency' => 
  array (
    'c11b9496401139fb4d750c3048d3761ae1329653' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/main/mycrm.html',
      1 => 1523238820,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5acac7b81c3884_85486278 ($_smarty_tpl) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
		<title>我的crm</title>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</head>

<body>

<div class="container-fluid w fix my-crm">
        <div class="row">
            
            <div class="logo"></div>
            <div class="tc nemo-text"><?php echo $_smarty_tpl->tpl_vars['lastname']->value;?>
</div>
            
			<div class="nineblock">
                <ul class="fix">
                    <li>
                        <a href="index.php?module=VisitingOrder&action=add">
                            <span class="icon-folder-open"></span>
                            新建拜访单
                        </a>
                    </li>
                    <li class="blockcenter">
                        <a href="index.php?module=VisitingOrder&action=allList">
                            <span class="icon-book"></span>
                            拜访单
                        </a>
                    </li>
                    <li>
                        <a href="index.php?module=Accounts&action=goaddAcoundUI">
                            <span class="icon-user"></span>
                            新建客户
                        </a>
                    </li>
                </ul>
                <ul class="fix">
                    <li>
                        <a href="index.php?module=Salestaget&action=index">
                            <span class="icon-bar-chart"></span>
                            销售周报分析
                        </a>
                    </li>
                    <li class="blockcenter">
                        <a href="index.php?module=SalesDaily&action=slist">
                            <span class="icon-calendar"></span>
                            销售日报
                        </a>
                    </li>
                    <li>
                        <a href="index.php?module=RefillApplication&action=index">
                            <span class="icon-credit-card"></span>
                            充值申请单
                        </a>
                    </li>
                </ul>
                <ul class="fix">
                    <li>
                        <a href="index.php?module=ActivationCode&action=add">
                            <span class="icon-qrcode"></span>
                            获取T云激活码
                        </a>
                    </li>
                    <li class="blockcenter">
                        <a href="index.php?module=ServiceContracts&action=index">
                            <span class="icon-file"></span>
                            服务合同
                        </a>
                    </li>
                    <li>
                        <a href="index.php?module=ContractsAgreement&action=index">
                            <span class="icon-paste"></span>
                            服务合同补充
                        </a>
                    </li>
                </ul>
                <ul class="fix">
                    <li>
                        <a href="index.php?module=ExtensionTrial&action=index">
                            <span class="icon-time"></span>
                            合同延期审核
                        </a>
                    </li>
                    <li class="blockcenter">
                        <a href="index.php?module=SupplierContracts&action=index">
                            <span class="icon-file"></span>
                            采购合同
                        </a>
                    </li>
                    <li>
                        <a href="index.php?module=SuppContractsAgreement&action=index">
                            <span class="icon-copy"></span>
                            采购合同补充
                        </a>
                    </li>
                </ul>
            </div>
			<?php if ($_smarty_tpl->tpl_vars['userid']->value == 2110 || $_smarty_tpl->tpl_vars['userid']->value == 199) {?>
			<div class="confirm tc">
				<?php if ($_smarty_tpl->tpl_vars['userid']->value == 2110) {?><button class="btn btn1" onclick = 'openurl(8)'><i class="icon-file-alt">服务合同特别</i></button><?php }?>
				<button class="btn btn2" onclick = 'openurl(10)'><i class="icon-file-alt">T云升级&续费</i></button>
            </div>
			<?php }?>
			
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        </div>
    </div>
   
   	<?php echo '<script'; ?>
>
   		function openurl(type){
   			if(type==1){
   				window.location.href='index.php?module=VisitingOrder&action=allList';
   			}
        if(type==2){
            window.location.href='/index.php?module=VisitingOrder&action=add';
        }
        if(type==3){
            window.location.href='/index.php?module=Accounts&action=goaddAcoundUI';
        }
        if(type==4){
            window.location.href='/index.php?module=Salestaget&action=index';
        }
		if(type==5){
            window.location.href='/index.php?module=SalesDaily&action=slist';
        }
        if(type==6){
            window.location.href='/index.php?module=RefillApplication&action=index';
        }
        if(type==7){
            window.location.href='/index.php?module=ActivationCode&action=add';
        }
        if(type==8){
            window.location.href='/index.php?module=ServiceContracts&action=index';
        }
		if(type==9){
            window.location.href='/index.php?module=ExtensionTrial&action=index';
        }
            if(type==10){
                window.location.href='/index.php?module=ActivationCode&action=tyunupgrade';
            }
			if(type==11){
                window.location.href='/index.php?module=SupplierContracts&action=index';
            }
            if(type==12){
                window.location.href='/index.php?module=ContractsAgreement&action=index';
            }
            if(type==13){
                window.location.href='/index.php?module=SuppContractsAgreement&action=index';
            }
   		}
   	<?php echo '</script'; ?>
>
	
</body>
</html><?php }
}
