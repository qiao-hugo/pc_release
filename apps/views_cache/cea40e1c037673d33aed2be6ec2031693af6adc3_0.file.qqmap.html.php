<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-18 10:47:18
  from "/data/httpd/vtigerCRM/apps/views/qqmap.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a372c365bc941_22933979',
  'file_dependency' => 
  array (
    'cea40e1c037673d33aed2be6ec2031693af6adc3' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/qqmap.html',
      1 => 1495098386,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a372c365bc941_22933979 ($_smarty_tpl) {
?>
 <input type='hidden' id='lat' value='0' >
<input type='hidden' id='lng' value='0' >
<iframe id="mapPage" width="100%" height="100%" frameborder=0
        src="http://apis.map.qq.com/tools/locpicker?search=0&mapdraggable=0&zoom=15&type=1&key=34IBZ-ZTRRG-WI4Q2-IYFXG-MBRUO-FEBVC&referer=myapp">
</iframe>





<?php }
}
