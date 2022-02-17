<?php
/* Smarty version 3.1.28-dev/79, created on 2018-01-27 10:09:34
  from "/data/httpd/vtigerCRM/apps/views/VisitingOrder/signd.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a6bdf5ecb6e84_52126797',
  'file_dependency' => 
  array (
    '935c75d46f246bf7913ef2632e7738bbe1352c5f' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/VisitingOrder/signd.html',
      1 => 1516969160,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:qqmap.html' => 1,
  ),
),false)) {
function content_5a6bdf5ecb6e84_52126797 ($_smarty_tpl) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
		<title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</head>

<body>
	<input type="hidden" id="record" value=<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
>
    <input type="hidden" id="lang">
    <input type="hidden" id="ress" >
	<div class="container-fluid" id="XS">
		<div>
			<div id="container" style="width:100%; height:500px"><?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:qqmap.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
</div>
		</div>
        <div class="confirm tc confirm2">
            <input  type="hidden" id="address" readonly  style="width: 100%; margin: auto;">
        </div>
        <input  id="position" type="hidden">
        <div class="confirm tc confirm2" style="position: fixed;bottom: 5px;right: 0px;width: 104px;">
            <button class="btn" id="sign" onclick="sign()">签到</button>
        </div>
	</div>
	<?php echo '<script'; ?>
 type="text/javascript">
        window.addEventListener('message', function(event) {
            // 接收位置信息，用户选择确认位置点后选点组件会触发该事件，回传用户的位置信息
            var loc = event.data;
            //console.log(loc);
            //console.log(loc.poiaddress)
            //console.log(loc.latlng.lng)
            //console.log(loc.latlng.lat)
            if(loc  && loc.module == 'locationPicker') {
                $('#address').val(loc.poiaddress);
                $('#position').val(loc.latlng.lng+'***'+loc.latlng.lat);
                console.log($('#address').val());
                console.log($('#position').val());
            }
            //console.log('location', loc);
        }, false);
		  function sign(){
	        if($('#address').val()&&$(position).val()){
	            $adname = $('#address').val();
	            $adcode = $('#position').val();
	            $id = $('#record').val();
	          // console.log('index.php?module=VisitingOrder&action=dosign&id='+$id+'&adname='+$adname+'&adcode='+$adcode);
	            window.location = 'index.php?module=VisitingOrder&action=dosign&id='+$id+'&adname='+$adname+'&adcode='+$adcode;
	        }else{
	            alert('地址不能为空');
	        }
    	}
	<?php echo '</script'; ?>
>
   
</body>
</html><?php }
}
