<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-18 10:41:04
  from "/data/httpd/vtigerCRM/apps/views/qqmaps.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a372ac03816e0_03252669',
  'file_dependency' => 
  array (
    'b8668ca62f3974b18f781e8c614a2b1c55313b2a' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/qqmaps.html',
      1 => 1476244169,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a372ac03816e0_03252669 ($_smarty_tpl) {
?>
 <input type='hidden' id='lat' value='0' >
<input type='hidden' id='lng' value='0' >

<?php echo '<script'; ?>
 src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"><?php echo '</script'; ?>
>
 <?php echo '<script'; ?>
 type="text/javascript">

  wx.config({
      debug: false,
      appId: "<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['appId'];?>
",
      timestamp: "<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['timestamp'];?>
",
      nonceStr: "<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['nonceStr'];?>
",
      signature: "<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['signature'];?>
",
      jsApiList: ['getLocation']
  });
     wx.ready(function () {
  // 1 判断当前版本是否支持指定 JS 接口，支持批量判断
 
		wx.checkJsApi({
		  jsApiList: [
			'getNetworkType',
			'previewImage'
		  ],
		  success: function (res) {
			//alert(JSON.stringify(res));
		  }
		});
		
		wx.getLocation({
         type: 'gcj02', 
		  success: function (res) {
		  
			 var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
			 var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
			 var speed = res.speed; // 速度，以米/每秒计
			 var accuracy = res.accuracy; // 位置精度
			 
			 //var latLng = new qq.maps.LatLng(latitude, longitude);
			
			 //var geocoder = new qq.maps.Geocoder();
			
			 //geocoder.getAddress(latLng);
			
			 //geocoder.setComplete(function(result) {
				//alert(result.detail.address);			
			 //});
			
			var markUrl = "http://apis.map.qq.com/tools/locpicker?search=0&mapdraggable=0&type=1&key=34IBZ-ZTRRG-WI4Q2-IYFXG-MBRUO-FEBVC&referer=myapp&coord="+latitude+','+longitude;
			console.log(markUrl);
            //document.getElementById('markPage').src = markUrl;
            console.log(markUrl);
			$('#container').append('<iframe id="mapPage1" width="100%" height="100%" frameborder=0 src="'+markUrl+'"></iframe>');
			//$('#address').val(loc.poiaddress);
            //$('#position').val(latitude+'***'+longitude);
		
		  },
		  cancel: function (res) {
			alert('用户拒绝授权获取地理位置');
		  }
		});
	 }
	);
	wx.error(function (res) {
	  alert(res.errMsg);
	});
<?php echo '</script'; ?>
>



<?php }
}
