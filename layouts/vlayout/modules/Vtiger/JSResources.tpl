<script type="text/javascript" src="data/min/?b=libraries&f=html5shim/html5.js,jquery/jquery.blockUI.js,jquery/chosen/chosen.jquery.min.js,jquery/select2/select2.min.js,jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js,jquery/jquery.class.min.js,jquery/defunkt-jquery-pjax/jquery.pjax.js,jquery/jstorage.min.js,jquery/autosize/jquery.autosize-min.js,jquery/rochal-jQuery-slimScroll/slimScroll.min.js,jquery/pnotify/jquery.pnotify.min.js,jquery/jquery.hoverIntent.minified.js,media/jquery.dataTables.js,media/dataTables.fixedColumns.js,bootstrap/js/bootstrap.js,bootstrap/js/bootbox.min.js,jquery/window.js,jquery/Fixed-Header-Table/jquery.fixedheadertable.min.js"></script>
<script src="libraries/jquery/jquery.twbsPagination.js"></script>
<link rel="stylesheet" type="text/css" href="data/min/?f=libraries/media/jquery.dataTables.css,libraries/media/dataTables.fixedColumns.css,libraries/jquery/Fixed-Header-Table/css/defaultTheme.css">
<script type="text/javascript" src="data/min/?b=resources&f=jquery.additions.js,app.js,helper.js,Connector.js,ProgressIndicator.js"></script>
<script type="text/javascript" src="resources/area.js"></script>
<script type="text/javascript" src="libraries/jquery/posabsolute-jQuery-Validation-Engine/js/jquery.validationEngine.js" ></script>
<script type="text/javascript" src="libraries/jquery/layer/layer.min.js"></script>
<script type="text/javascript" src="libraries/jquery/datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="libraries/jquery/datepicker/locales/bootstrap-datepicker.zh-CN.min.js"></script>
<script type="text/javascript" src="libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="libraries/jquery/kindeditor/kindeditor-all-min.js"></script>
<link rel="stylesheet" media="screen" type="text/css" href="libraries/jquery/kindeditor/themes/default/default.css" />
{foreach key=index item=jsModel from=$SCRIPTS}
	<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}?&v={$VTIGER_VERSION}"></script>
{/foreach}
	<!-- Added in the end since it should be after less file loaded -->
<script type="text/javascript" src="libraries/bootstrap/js/less.min.js"></script>
	<!--王斌 百度编辑器-->
<script type="text/javascript" src="libraries/ueditor/ueditor.config.js?v=1.1"></script> <!-- 配置文件 -->
<script type="text/javascript" src="libraries/ueditor/ueditor.all.min.js?v=1.1"></script> <!-- 实例化编辑器 -->
<script type="text/javascript" src="libraries/jQuery.selected.js?v=1.1"></script>