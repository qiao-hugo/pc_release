<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-18 23:43:07
  from "/data/httpd/vtigerCRM/apps/views/Salestaget/index.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a37e20b0c8396_52149128',
  'file_dependency' => 
  array (
    '46819a993813f611490fa62421492f6616a42397' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/Salestaget/index.html',
      1 => 1477292669,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a37e20b0c8396_52149128 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>周报目标</title>
	<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

	<!-- <link href="static/css/jquery.mobile-1.3.0.min.css" rel="stylesheet" type="text/css" />
	<link href="static/css/mobiscroll.custom-2.5.0.min.css" rel="stylesheet" type="text/css" />
	<link href="static/css/select2.css" rel="stylesheet" type="text/css" />
	<?php echo '<script'; ?>
 src="static/js/jquery.form.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="static/js/select2.js"><?php echo '</script'; ?>
> -->
</head>
<body>
	<div class="content">
		
		<h1><?php if ($_smarty_tpl->tpl_vars['is_depa']->value == '1') {?>部门<?php }?>周目标</h1>
		<div>
			<h3 style="text-align: center;"><?php echo $_smarty_tpl->tpl_vars['weekData']->value['startdate'];?>
 - <?php echo $_smarty_tpl->tpl_vars['weekData']->value['enddate'];?>
</h3>
			<div class="left">

				<ul>
					<li class="li01">
						<span class="li_span">邀约目标</span><br/>
						<span class="li_span li_span_num">
							<?php if ($_smarty_tpl->tpl_vars['weekData']->value['weekinvitationtarget'] == '') {?>
								0
							<?php } else { ?>
								<?php echo $_smarty_tpl->tpl_vars['weekData']->value['weekinvitationtarget'];?>

							<?php }?>
						<span class="list_span2">个</span></span>
					</li>

					<li class="li02">
						<span class="li_span">拜访目标</span><br/>
						<span class="li_span li_span_num">
							<?php if ($_smarty_tpl->tpl_vars['weekData']->value['weekvisittarget'] == '') {?>
								0
							<?php } else { ?>
								<?php echo $_smarty_tpl->tpl_vars['weekData']->value['weekvisittarget'];?>

							<?php }?>
						<span class="list_span2">个</span></span></li>
					<li class="li03">

						<span class="li_span">业绩目标</span><br/>
						<span class="li_span li_span_num">
							<?php if ($_smarty_tpl->tpl_vars['weekData']->value['weekachievementtargt'] == '') {?>
								0
							<?php } else { ?>
								<?php echo $_smarty_tpl->tpl_vars['weekData']->value['weekachievementtargt'];?>

							<?php }?>
						<span class="list_span2">元</span></span></li>
				</ul>
			</div>
			<div class="right">
				
				<div class="right_div right_div1">
					
					<div class="right_div01 zhanbi_div" data-state="<?php echo $_smarty_tpl->tpl_vars['weekData']->value['weekinvitationrate'];?>
"></div>
					<div class="baifenbi"><?php echo $_smarty_tpl->tpl_vars['weekData']->value['weekinvitationrate'];?>
</div>
				</div>
				<div class="right_div right_div2">
					<div class="right_div02 zhanbi_div"  data-state="<?php echo $_smarty_tpl->tpl_vars['weekData']->value['weekvisitrate'];?>
">
					</div>
					<div class="baifenbi"><?php echo $_smarty_tpl->tpl_vars['weekData']->value['weekvisitrate'];?>
</div>
				</div>
				<div class="right_div right_div3">
					<div class="right_div03 zhanbi_div"  data-state="<?php echo $_smarty_tpl->tpl_vars['weekData']->value['weekachievementrate'];?>
">
					</div>
					<div class="baifenbi"><?php echo $_smarty_tpl->tpl_vars['weekData']->value['weekachievementrate'];?>
</div>
				</div>
				<div style="clear: both;"></div>
				<div class="t_right_div">
					邀约
				</div>
				<div class="t_right_div">
					拜访
				</div>
				<div class="t_right_div">
					业绩
				</div>

			</div>
		</div>
	</div>



	<div class="content">
		<h1><?php if ($_smarty_tpl->tpl_vars['is_depa']->value == '1') {?>部门<?php }?>月目标</h1>
		<div>
			<h3 style="text-align: center;"><?php echo $_smarty_tpl->tpl_vars['monthData']->value['startdate'];?>
 - <?php echo $_smarty_tpl->tpl_vars['monthData']->value['enddate'];?>
</h3>
			<div class="left">
				<ul>
					<li class="li01">
						<span class="li_span">邀约目标</span><br/>
						<span class="li_span li_span_num">
							<?php if ($_smarty_tpl->tpl_vars['monthData']->value['invitationtarget'] == '') {?>
								0
							<?php } else { ?>
								<?php echo $_smarty_tpl->tpl_vars['monthData']->value['invitationtarget'];?>

							<?php }?>
							<span class="list_span2">个</span>
						</span>
					</li>

					<li class="li02">
						<span class="li_span">拜访目标</span><br/>
						<span class="li_span li_span_num">
							<?php if ($_smarty_tpl->tpl_vars['monthData']->value['visittarget'] == '') {?>
								0
							<?php } else { ?>
								<?php echo $_smarty_tpl->tpl_vars['monthData']->value['visittarget'];?>

							<?php }?>
						<span class="list_span2">个</span></span></li>
					<li class="li03">

						<span class="li_span">业绩目标</span><br/>
						<span class="li_span li_span_num">
							<?php if ($_smarty_tpl->tpl_vars['monthData']->value['achievementtargt'] == '') {?>
								0
							<?php } else { ?>
								<?php echo $_smarty_tpl->tpl_vars['monthData']->value['achievementtargt'];?>

							<?php }?>
						<span class="list_span2">元</span></span></li>
				</ul>
			</div>
			<div class="right">
				
				<div class="right_div right_div1">
					<div class="right_div01 zhanbi_div" data-state="<?php echo $_smarty_tpl->tpl_vars['monthData']->value['invitationrate'];?>
">
						
					</div>
					<div class="baifenbi">
						<?php echo $_smarty_tpl->tpl_vars['monthData']->value['invitationrate'];?>

					</div>
				</div>
				<div class="right_div right_div2">
					<div class="right_div02 zhanbi_div" data-state="<?php echo $_smarty_tpl->tpl_vars['monthData']->value['visitrate'];?>
">
					</div>
					<div class="baifenbi">
						<?php echo $_smarty_tpl->tpl_vars['monthData']->value['visitrate'];?>

					</div>
				</div>
				<div class="right_div right_div3">
					<div class="right_div03 zhanbi_div" data-state="<?php echo $_smarty_tpl->tpl_vars['monthData']->value['achievementrate'];?>
">
					</div>
					<div class="baifenbi">
						<?php echo $_smarty_tpl->tpl_vars['monthData']->value['achievementrate'];?>

					</div>
				</div>
				<div style="clear: both;"></div>
				<div class="t_right_div">
					邀约
				</div>
				<div class="t_right_div">
					拜访
				</div>
				<div class="t_right_div">
					业绩
				</div>

				<div style="clear: both;"></div>
			</div>
		</div>
	</div>

	<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>




	<style type="text/css">
		
		.content {
			width: 96%;
			margin:  0 auto;
			font-family: "Arial","Microsoft YaHei","黑体","宋体",sans-serif;
			font-size: 10pt;
			color: #666;
		}
		.content h1{
			color: #A7C0DE;

		}
		.content .left{
			float: left;
			width: 40%;
			text-align: center;
		}
		.content .left ul {
			margin-top:  10pt;
		}
		.content .left ul li{
			margin-bottom: 15pt;
		}
		.content .left ul li.li01{
			color: #F4C069;
		}
		.content .left ul li.li02{
			color: #746186;
		}
		.content .left ul li.li03{
			color: #6499D1;
		}
		.content .left ul li.li04{
			color: #6AB59B;
		}
		.li_span{
			color: #666;
		}
		.list_span2{
			font-size: 10pt;
		}
		.li_span_num{
			font-size: 20pt;
		}

		.content .right{
			float: left;
			width: 60%;
		}
		.content .right .right_div{
			float: left;
			width: 3em;
			height: 200px;
			margin-left: 0.5em;
			background: #fff;
			border: 1px solid #fff;
		}
		.content .right .t_right_div{
			float: left;
			width: 3em;
			height: 60pt;
			margin-left: 0.5em;
			background: #fff;
			/* border: 1px solid #fff; */
			text-align: center;
		}
		.content .right .right_div1{
			background: #F4C069;
		}
		.content .right .right_div2{
			background: #746186;
		}
		.content .right .right_div3{
			background: #6499D1;
		}
		.content .right .right_div4{
			background: #6AB59B;
		}
		.right_div01,.right_div02,.right_div03,.right_div04{
			width: 3em;
			background: #fff;
			/* border: 1px solid #fff; */
		}
		/* .right_div01{
			height: 100px;
		}
		.right_div02{
			height: 200px;
		}
		.right_div03{
			height: 400px;
		} */

		.baifenbi {
			text-align: center;
			color: #fff;
		}
		.zhanbi_div {
			position: relative;
			left: -1px;
			top:  -1px;
		}
		
	</style>
	
	<?php echo '<script'; ?>
 type="text/javascript">
		$('.zhanbi_div').each(function() {
			var num = $(this).attr('data-state'); // 百分比
			if (!num) {
				num = 1;
			}
			num = parseInt(num);
			if (num > 100) {
				num = 100;
			}
			if (num <= 0) {
				num = 1;
			}

			num = 100 - num;

			$(this).css({height: parseInt(200*num/100) + 'px'});
		});

	<?php echo '</script'; ?>
>
	
</body>
</html><?php }
}
