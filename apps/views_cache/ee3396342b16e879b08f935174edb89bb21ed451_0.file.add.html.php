<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-18 19:00:35
  from "/data/httpd/vtigerCRM/apps/views/SalesDaily/add.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a379fd3a66bd3_19799516',
  'file_dependency' => 
  array (
    'ee3396342b16e879b08f935174edb89bb21ed451' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/SalesDaily/add.html',
      1 => 1478598903,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a379fd3a66bd3_19799516 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>添加销售日报</title>
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
    <?php echo '<script'; ?>
 src="static/js/select2.js"><?php echo '</script'; ?>
>
    <style type="text/css">
        *{
            text-shadow:none;
        }

    </style>
</head>
<body>
<div class="container-fluid w fix" id="demo-intro" data-role="page">
    <div class="row">
        <form class="form-horizontal"  style="margin:0;" onsubmit='return check()' data-ajax="false" action="index.php?module=SalesDaily&action=doadd" method="POST">
        <div class="add-report" style="margin:10px  5px 0 5px;">
            <label for="worksummarizename">主题</label>
            <input type="text" id="worksummarizename" class="form-control" name="worksummarizename" value="<?php echo date('Y-m-d');?>
 <?php echo $_smarty_tpl->tpl_vars['username']->value;?>
 销售日报" readonly="readonly" disabled/>
            <label for="content">备注</label>
            <textarea name="content" id="content" rows="5" class="form-control"  data-toggle="popover" data-placement="top"
                      data-content="今日总结不能为空"></textarea>
            <label for="todaycontent">今日总结</label>
            <textarea name="todaycontent" id="todaycontent" rows="5" class="form-control"  data-toggle="popover" data-placement="top"
                      data-content="今日感受不能为空"></textarea>
            <label for="tommorrowcontent">明日计划</label>
            <textarea name="tommorrowcontent" id="tommorrowcontent" rows="5" class="form-control"  data-toggle="popover" data-placement="top"
                          data-content="下个工作日计划不能为空"></textarea>
            <div data-role="main" style="overflow:hidden;" role="banner" class="ui-header ui-bar-inherit ui-corner-all">
                <h1 class="ui-title" role="heading" aria-level="1">近期可成交的客户</h1>
                <a href="#new-page" data-icon="plus" id="opencandeal" class="ui-btn-right ui-link ui-btn ui-icon-plus ui-btn-icon-right ui-shadow ui-corner-all" data-role="button" role="button">&nbsp;</a>
            </div>
            <div data-role="controlgroup" id="candeal-page" data-title="Inbox">
                <div role="main" class="ui-content">
                    <ul id="candeallist" class="touch listviews" data-role="listview" data-icon="false" data-split-icon="delete" data-split-theme="a">
                        <?php if (!empty($_smarty_tpl->tpl_vars['candeallist']->value)) {?>
                        <?php
$_from = $_smarty_tpl->tpl_vars['candeallist']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_CANACCOUNTI_0_saved_item = isset($_smarty_tpl->tpl_vars['CANACCOUNTI']) ? $_smarty_tpl->tpl_vars['CANACCOUNTI'] : false;
$_smarty_tpl->tpl_vars['CANACCOUNTI'] = new Smarty_Variable();
$__foreach_CANACCOUNTI_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_CANACCOUNTI_0_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['CANACCOUNTI']->value) {
$__foreach_CANACCOUNTI_0_saved_local_item = $_smarty_tpl->tpl_vars['CANACCOUNTI'];
?>
                        <li class="candealhide" data-accountid="<?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['salesdailycandealid'];?>
">

                            <a href="#demo-mail" class="ui-grid-a ui-mini" data-inline="true">
                                <h6 class="topic" style="display:inline-block; "><?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['accountname'];?>
</h6>
                                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" class="ui-grid-a ui-mini" >
                                        <input type="radio" name="prevcandealissigncontract[<?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['salesdailycandealid'];?>
]" id="candealissigncontract<?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['salesdailycandealid'];?>
a" value="1">
                                        <label for="candealissigncontract<?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['salesdailycandealid'];?>
a">已签合同</label>
                                        <input type="radio" name="prevcandealissigncontract[<?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['salesdailycandealid'];?>
]" id="candealissigncontracta<?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['salesdailycandealid'];?>
b" value="0" checked="checked">
                                        <label for="candealissigncontracta<?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['salesdailycandealid'];?>
b">未签合同</label>
                                        <input type="hidden" name="prevcandealdeleted[<?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['salesdailycandealid'];?>
]"  id="candealdeleted<?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['salesdailycandealid'];?>
" value="0"/>
                                        <input type="hidden" name="prevcandealrecordid[<?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['salesdailycandealid'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['CANACCOUNTI']->value['salesdailycandealid'];?>
"/>

                                    </fieldset>
                            </a>
                            <a href="#" class="delete">Delete</a>
                        </li>
                        <?php
$_smarty_tpl->tpl_vars['CANACCOUNTI'] = $__foreach_CANACCOUNTI_0_saved_local_item;
}
}
if ($__foreach_CANACCOUNTI_0_saved_item) {
$_smarty_tpl->tpl_vars['CANACCOUNTI'] = $__foreach_CANACCOUNTI_0_saved_item;
}
?>
                        <?php }?>
                    </ul>
                </div>
                <div id="confirm" class="ui-content" data-role="popup" data-theme="a">
                    <p id="question">确定要删除</p>
                    <div class="ui-grid-a">
                        <div class="ui-block-a">
                            <a id="yes" class="ui-btn ui-corner-all ui-mini ui-btn-a" data-rel="back">是</a>
                        </div>
                        <div class="ui-block-b">
                            <a id="cancel" class="ui-btn ui-corner-all ui-mini ui-btn-a" data-rel="back">否</a>
                        </div>
                    </div>
                </div>
            </div>
            <div data-role="main" style="overflow:hidden;" role="banner" class="ui-header ui-bar-inherit ui-corner-all">
                <h1 class="ui-title" role="heading" aria-level="1">每日收款客户</h1>
                <a href="#daydeal-page" data-icon="plus" id="opendaydeal" class="ui-btn-right ui-link ui-btn ui-icon-plus ui-btn-icon-right ui-shadow ui-corner-all" data-role="button" role="button">&nbsp;</a>
            </div>
            <div data-role="controlgroup" id="daydealviewlist" data-title="Inbox">
                <div role="main" class="ui-content">
                    <ul id="daydeallist" class="touch listviews" data-role="listview" data-icon="false" data-split-icon="delete" data-split-theme="a">

                    </ul>
                </div>
            </div>
            <dl></dl>
            <div class="confirm tc">
                <button class="ui-btn ui-btn-b ui-shadow ui-corner-all">提 交</button>
                <a href="/index.php?action=mycrm" class="ui-btn ui-shadow ui-corner-all" data-ajax="false">返回</a>
            </div>
        </div>
        </form>
    </div>
    <div data-role="popup" id="popupBasicform" data-theme="b">
        <div id="popupfrommsg">信息不完整请确认</div>
    </div>

</div>
<div data-role="page" id="new-page" data-url="new-page">
    <div data-role="header" data-position="fixed">
        <h1>近期可成交的客户</h1>
        <a href="#demo-intro" data-rel="back" id="candealcancel" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
        <a href="#" data-rel="back1" id="candealclose" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-btn-active ui-btn-right ui-icon-plus ui-btn-icon-right">添加</a>
    </div>
    <div role="main" class="ui-content">
        <div class="form-group">
            <label for="candealaccountmsg">客户名称</label>
            <select  id="candealaccountmsg" class="select2" data-role="none" style="width:100%">
                <option value="0">请选择客户</option>
                <?php if (!empty($_smarty_tpl->tpl_vars['accountinfo']->value)) {?>
                <?php
$_from = $_smarty_tpl->tpl_vars['accountinfo']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_CACCOUNTI_1_saved_item = isset($_smarty_tpl->tpl_vars['CACCOUNTI']) ? $_smarty_tpl->tpl_vars['CACCOUNTI'] : false;
$_smarty_tpl->tpl_vars['CACCOUNTI'] = new Smarty_Variable();
$__foreach_CACCOUNTI_1_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_CACCOUNTI_1_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['CACCOUNTI']->value) {
$__foreach_CACCOUNTI_1_saved_local_item = $_smarty_tpl->tpl_vars['CACCOUNTI'];
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['CACCOUNTI']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['CACCOUNTI']->value['name'];?>
</option>
                <?php
$_smarty_tpl->tpl_vars['CACCOUNTI'] = $__foreach_CACCOUNTI_1_saved_local_item;
}
}
if ($__foreach_CACCOUNTI_1_saved_item) {
$_smarty_tpl->tpl_vars['CACCOUNTI'] = $__foreach_CACCOUNTI_1_saved_item;
}
?>
                <?php }?>
            </select>
        </div>
        <label for="candealaccountcontent">客户情况</label>
        <textarea id="candealaccountcontent" rows="5" class="form-control"></textarea>
        <label for="candealproduct">产品</label>
        <input id="candealproduct" type="text" id="tommorrowcontent2" rows="5" class="form-control"  data-clear-btn="true"/>
        <label for="candealquote">报价</label>
        <input id="candealquote" type="number" rows="5" class="form-control"  data-clear-btn="true"/>
        <label for="candealfirstpayment">首付款</label>
        <input id="candealfirstpayment" type="number" rows="5" class="form-control" data-clear-btn="true"/>
    </div>
    <div data-role="popup" id="popupcandealBasic" data-theme="b">
        <div id="popupcandealmsg"></div>
    </div>
</div>
<div data-role="page" id="daydeal-page" data-url="daydeal-page">
    <div data-role="header" data-position="fixed">
        <h1>每日收款客户</h1>
        <a href="#demo-intro" data-rel="back" id="daydealcancel" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
        <a href="#demo-intro1" data-rel="back2" id="daydealclose" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-btn-active ui-btn-right ui-icon-plus ui-btn-icon-right">添加</a>
    </div>
    <div role="main" class="ui-content">
        <div class="form-group">
            <label for="dayaccountmsg">客户名称</label>
            <select  id="dayaccountmsg" class="select2" data-role="none" style="width:100%">
                <option value="0">请选择客户</option>
                <?php if (!empty($_smarty_tpl->tpl_vars['accountinfo']->value)) {?>
                <?php
$_from = $_smarty_tpl->tpl_vars['accountinfo']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_CACCOUNTI_2_saved_item = isset($_smarty_tpl->tpl_vars['CACCOUNTI']) ? $_smarty_tpl->tpl_vars['CACCOUNTI'] : false;
$_smarty_tpl->tpl_vars['CACCOUNTI'] = new Smarty_Variable();
$__foreach_CACCOUNTI_2_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_CACCOUNTI_2_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['CACCOUNTI']->value) {
$__foreach_CACCOUNTI_2_saved_local_item = $_smarty_tpl->tpl_vars['CACCOUNTI'];
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['CACCOUNTI']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['CACCOUNTI']->value['name'];?>
</option>
                <?php
$_smarty_tpl->tpl_vars['CACCOUNTI'] = $__foreach_CACCOUNTI_2_saved_local_item;
}
}
if ($__foreach_CACCOUNTI_2_saved_item) {
$_smarty_tpl->tpl_vars['CACCOUNTI'] = $__foreach_CACCOUNTI_2_saved_item;
}
?>
                <?php }?>
            </select>
        </div>
        
            <label for="daydealproduct">成交业务</label>
            <select  id="daydealproduct" class="select2" data-role="none" style="width:100%">
                <option value="0"  data-price="0" data-stepprice="0">请选择成交业务</option>
                <?php if (!empty($_smarty_tpl->tpl_vars['productsinfo']->value)) {?>
                <?php
$_from = $_smarty_tpl->tpl_vars['productsinfo']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_DAYPRODUCT_3_saved_item = isset($_smarty_tpl->tpl_vars['DAYPRODUCT']) ? $_smarty_tpl->tpl_vars['DAYPRODUCT'] : false;
$_smarty_tpl->tpl_vars['DAYPRODUCT'] = new Smarty_Variable();
$__foreach_DAYPRODUCT_3_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_DAYPRODUCT_3_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['DAYPRODUCT']->value) {
$__foreach_DAYPRODUCT_3_saved_local_item = $_smarty_tpl->tpl_vars['DAYPRODUCT'];
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['DAYPRODUCT']->value['id'];?>
"  data-price="<?php echo $_smarty_tpl->tpl_vars['DAYPRODUCT']->value['marketprice'];?>
" data-stepprice="<?php echo $_smarty_tpl->tpl_vars['DAYPRODUCT']->value['performance'];?>
"><?php echo $_smarty_tpl->tpl_vars['DAYPRODUCT']->value['name'];?>
</option>
                <?php
$_smarty_tpl->tpl_vars['DAYPRODUCT'] = $__foreach_DAYPRODUCT_3_saved_local_item;
}
}
if ($__foreach_DAYPRODUCT_3_saved_item) {
$_smarty_tpl->tpl_vars['DAYPRODUCT'] = $__foreach_DAYPRODUCT_3_saved_item;
}
?>
                <?php }?>
            </select>
            <label for="daydealmarketprice">市场价</label>
            <input id="daydealmarketprice" type="number"  class="form-control"/>
            <label for="daydealamount">成交金额</label>
            <input id="daydealamount" type="number"  class="form-control" data-clear-btn="true"/>
            <label for="daydealfirstpayment">收款</label>
            <input id="daydealfirstpayment" type="number"  class="form-control" data-clear-btn="true"/>
            <label for="daydealallamount" class="select">是否全款</label>
            <select  id="daydealallamount" data-native-menu="false">
                <option>是否全款</option>
                <option value="1">是</option>
                <option value="0" selected>否</option>
            </select>
       
            <label for="daydealpaymentnature" class="select">到款性质</label>
            <select  id="daydealpaymentnature" data-native-menu="false">
                <option>到款性质</option>
                <option value="firstpaymentnature" selected>首付款</option>
                <option value="lastpaymentnature">尾款</option>
            </select>
        
            <label for="daydealdiscount">折扣</label>

            <input id="daydealdiscount" type="hidden"  class="form-control"/>
            <input id="daydealdiscounttext" type="text" readonly />
            <label for="daydealarrivalamount">到账业绩</label>
            <input id="daydealarrivalamount" type="number"  class="form-control" readonly />
    </div>
    <div data-role="popup" id="popupBasic" data-theme="b">
        <div id="popupmsg"></div>
    </div>
</div>


<?php echo '<script'; ?>
 type="text/javascript">
        $.mobile.defaultPageTransition="none";
        $.mobile.defaultPageTransition="none";
        //重置每日可成交的客户
        $(document).on("pageshow", "#new-page", function() {
		    $("#candealaccountcontent").val('');//客户情况
		    $("#candealproduct").val('');//产品
		    $("#candealquote").val('');//报价
		    $("#candealfirstpayment").val('');//首付款
		});
		$('#candealclose').on('tap',function(){
		    var candealaccountmsg=$("#candealaccountmsg").val();//客户信息
		    if(parseInt(candealaccountmsg)==0){
		        $('#popupcandealmsg').text('客户信息不能为空');
		        $("#popupcandealBasic").popup('open');
		        return false;
		    }
		    var candealaccountname=$("#candealaccountmsg  option:selected").text();//客户名称
		    var candealaccountcontent=$("#candealaccountcontent").val();//客户情况
		    var candealproduct=$("#candealproduct").val();//产品
		    var candealquote=$("#candealquote").val();//报价
		    var candealfirstpayment=$("#candealfirstpayment").val();//首付款
		    var str='<li><a href="#demo-mail" class="ui-grid-a ui-mini" data-inline="true">'+
                    '<h6 class="topic" style="display:inline-block; ">'+candealaccountname+'</h6>'+
                    '<input type="hidden" name="candealaccountmsg['+candealaccountmsg+']" value="'+candealaccountmsg+'">'+
                    '<input type="hidden" name="candealaccountname['+candealaccountmsg+']" value="'+candealaccountname+'">'+
                    '<input type="hidden" name="candealaccountcontent['+candealaccountmsg+']" value="'+candealaccountcontent+'">'+
                    '<input type="hidden" name="candealproduct['+candealaccountmsg+']" value="'+candealproduct+'">'+
                    '<input type="hidden" name="candealquote['+candealaccountmsg+']" value="'+candealquote+'">'+
                    '<input type="hidden" name="candealfirstpayment['+candealaccountmsg+']" value="'+candealfirstpayment+'">'       
                    '</a><a href="#" class="delete">Delete</a></li>';

		    $('#candeallist').append(str);
		    $('#candealcancel').trigger("click");
		    $('#demo-intro').page();
		    $("#candeallist" ).listview("refresh");
        });
        //重置今日收款的客户
        $(document).on("pageshow", "#daydeal-page", function() {
		    $("#daydealproduct").select2('data', {id: '0', text: '请选择成交业务'});
		    $("#daydealmarketprice").val('');//市场价
		    $("#daydealamount").val('');//成交金额
		    $("#daydealfirstpayment").val('');//成交金额
		    $("#daydealarrivalamount").val('');//到账业绩
		});
		$('#daydealclose').on('tap',function(){
		    var dayaccountmsg=$("#dayaccountmsg").val();//客户信息
		    var dayaccountmsgname=$("#dayaccountmsg  option:selected").text();//客户名称
		    var daydealproduct=$("#daydealproduct").val();//产品的id
		    var daydealproductname=$("#daydealproduct option:selected").text();//产品的名称
		    var daydealstepprice=$("#daydealproduct option:selected").data('stepprice');//产品的名称
		    var daydealmarketprice=$("#daydealmarketprice").val();//市场价
		    var daydealamount=$("#daydealamount").val();//成交金额
		    var daydealfirstpayment=$("#daydealfirstpayment").val();//成交金额
		    var daydealpaymentnature=$("#daydealpaymentnature").val();//到款性质
		    var daydealallamount=$("#daydealallamount").val();//是否全款
		    var daydealarrivalamount=$("#daydealarrivalamount").val();//到账业绩

            if(dayaccountmsg==0){
		        $('#popupmsg').text('请选择择客户');
		        $("#popupBasic").popup('open');
		        return false;
		    }
		    if(parseInt(dayaccountmsg)==0 ||parseInt(daydealproduct)==0 || daydealmarketprice=='' || daydealamount=='' || daydealpaymentnature==""){
		        $('#popupmsg').text('信息不完整请确认');
		        $("#popupBasic").popup('open');
		        return false;
		    }
		    var str='<li><a href="#demo-mail" class="ui-grid-a ui-mini" data-inline="true">'+
                    '<h6 class="topic" style="display:inline-block; ">'+dayaccountmsgname+'</h6>'+
                    '<input type="hidden" name="dayaccountmsg['+dayaccountmsg+']" value="'+dayaccountmsg+'">'+
                    '<input type="hidden" name="dayaccountmsgname['+dayaccountmsg+']" value="'+dayaccountmsgname+'">'+
                    '<input type="hidden" name="daydealproduct['+dayaccountmsg+']" value="'+daydealproduct+'">'+
                    '<input type="hidden" name="daydealstepprice['+dayaccountmsg+']" value="'+daydealstepprice+'">'+
                    '<input type="hidden" name="daydealproductname['+dayaccountmsg+']" value="'+daydealproductname+'">'+
                    '<input type="hidden" name="daydealfirstpayment['+dayaccountmsg+']" value="'+daydealfirstpayment+'">'+
                    '<input type="hidden" name="daydealmarketprice['+dayaccountmsg+']" value="'+daydealmarketprice+'">'+
                    '<input type="hidden" name="daydealamount['+dayaccountmsg+']" value="'+daydealamount+'">'+       
                    '<input type="hidden" name="daydealpaymentnature['+dayaccountmsg+']" value="'+daydealpaymentnature+'">'+      
                    '<input type="hidden" name="daydealallamount['+dayaccountmsg+']" value="'+daydealallamount+'">'+      
                    '<input type="hidden" name="daydealarrivalamount['+dayaccountmsg+']" value="'+daydealarrivalamount+'">'+      
                    '</a><a href="#" class="delete">删除</a></li>';
            $('#daydeallist').append(str);
		    $('#daydealcancel').trigger("click");
		    $('#demo-intro').page();
		    $('#daydeallist').listview("refresh");
        });


        $(document).on('change','#daydealproduct',function(){
            var daydealmarketprice=$(this).find('option:selected').data('price');
            $('#daydealmarketprice').val(daydealmarketprice);
        });
        //格式化输入只能转入数字或小数保留两位
        function formatNumber(_this){
            _this.val(_this.val().replace(/,/g,''));//只能输入数字小数点
            _this.val(_this.val().replace(/[^0-9.]/g,''));//只能输入数字小数点
            _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
            _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
            _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
            _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
        }
        /**
         * 除法运算相除JS问题
         * @param arg1除数
         * @param arg2被除数
         * @returns {number}
         */
        function accDiv(arg1,arg2){
            var t1=0,t2=0,r1,r2;
            try{t1=arg1.toString().split(".")[1].length}catch(e){}
            try{t2=arg2.toString().split(".")[1].length}catch(e){}
            with(Math){
                r1=Number(arg1.toString().replace(".",""))
                r2=Number(arg2.toString().replace(".",""))
                return (r1/r2)*pow(10,t2-t1);
            }
        }
        $(document).on('input','#daydealmarketprice,#daydealamount,#daydealfirstpayment',function(){
            var daydealproduct=$("#daydealproduct").val();//产品的id
            if(parseInt(daydealproduct)==0){
                $('#popupmsg').text('请先选择成交业务');
		        $("#popupBasic").popup('open');
                return false;
            }
            var _this=this;
            formatNumber($(this));
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }
            CalculationResults();
         }).on('vmouseout','#daydealmarketprice,#daydealamount,#daydealfirstpayment',function(){
            var _this=this;
            formatNumber($(this));
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }
            CalculationResults();
         })
         //计算到账业绩
         function CalculationResults(){
            var daydealmarketprice=$('#daydealmarketprice').val();//市场价
            var daydealamount=$('#daydealamount').val();//成交金额
            var stepprice=$('#daydealproduct option:selected').data('stepprice');//成本
            var daydealfirstpayment=$('#daydealfirstpayment').val();//收款


            if(parseInt(daydealmarketprice)==0 || isNaN(parseInt(daydealmarketprice)) || parseInt(daydealamount)==0 || isNaN(parseInt(daydealamount))){
                return false;
            }
            //成交金额大于市场价
            if(parseFloat(daydealamount)>=parseFloat(daydealmarketprice)){
                $('#daydealdiscounttext').val('不打折');
                $('#daydealdiscount').val(1);
                if(parseInt(daydealfirstpayment)>0){
                    if(stepprice>1){
                        //公式:首付款-首付款/成交金额*成本
                        var currentprice=daydealfirstpayment-daydealfirstpayment/daydealamount*stepprice;
                    }else{
                        //不打折的就是其他产品的收款金额
                        var currentprice=daydealfirstpayment;
                    }
                    currentprice=currentprice>0?parseFloat(currentprice):0;
                    $('#daydealarrivalamount').val(currentprice.toFixed(2));
                }else{
                    $('#daydealarrivalamount').val(0);
                }
            }else{
                var discount=daydealamount/daydealmarketprice*10
                $('#daydealdiscounttext').val(discount.toFixed(2)+'折');
                $('#daydealdiscount').val(discount.toFixed(2));
                if(parseInt(daydealfirstpayment)>0){
                    if(accDiv(daydealamount,daydealmarketprice)>=0.75){
                        if(stepprice>1){
                            //公式:首付款*折扣-首付款/成交金额*成本
                            //折扣:折扣(成交金额/合同金额)大于1则按1计算
                            var currentprice=daydealfirstpayment*daydealamount/daydealmarketprice-daydealfirstpayment/daydealamount*stepprice;
                        }else{
                            var currentprice=daydealfirstpayment*daydealamount/daydealmarketprice;
                        }
                    }else{
                        //小于75折则不算业绩
                        var currentprice=0;
                    }
                    currentprice=currentprice>0?parseFloat(currentprice):0;
                    $('#daydealarrivalamount').val(currentprice.toFixed(2));
                }else{
                    $('#daydealarrivalamount').val(0);
                }
            }
         }
        $(".select2").select2();

        function check(){
            if( $("#content").val() =='' || $("#todaycontent").val() =='' || $("#tommorrowcontent").val() ==''){
		        $("#popupBasicform").popup('open');
		        return false;
            }
            return true;
        }

        $( document ).on("swipeleft swiperight", ".listviews li", function( event ) {
            var listitem = $( this ),
                dir = event.type === "swipeleft" ? "left" : "right",
                transition = $.support.cssTransform3d ? dir : false;
                confirmAndDelete( listitem, transition );
        });
        //if (!$.mobile.support.touch) {
            //$( ".listviews" ).removeClass( "touch" );
            $(document).on( "click",".delete", function() {
                var listitem = $( this ).parent( "li" );
                confirmAndDelete( listitem );
            });
        //}

        function confirmAndDelete(listitem, transition ) {
            console.log(listitem.hasClass('candealhide'));
            listitem.children( ".ui-btn" ).addClass( "ui-btn-active" );
            $("#confirm .topic").remove();
            listitem.find(".topic").clone().insertAfter( "#question" );
            $( "#confirm" ).popup( "open" );
            $( "#confirm #yes" ).on( "click", function() {
                if(listitem.hasClass('candealhide')){
                    var accountid=listitem.data('accountid');
                    $('#candealdeleted'+accountid).val(1);
                    listitem.css('display','none');
                }else{
                    if (transition) {

                    listitem.remove();
                    listitem.addClass( transition ).on( "webkitTransitionEnd transitionend otransitionend", function() {
                            listitem.remove();
                            $( "#list" ).listview( "refresh" ).find( ".border-bottom" ).removeClass( "border-bottom" );
                        }).prev( "li" ).children( "a" ).addClass( "border-bottom" ).end().end().children( ".ui-btn" ).removeClass( "ui-btn-active" );
                    }else {
                        listitem.remove();
                        $("#list").listview("refresh");
                    }
                }

            });
            $( "#confirm #cancel" ).on("click", function() {
                listitem.children( ".ui-btn" ).removeClass( "ui-btn-active" );
                $( "#confirm #yes" ).off();
            });
        }

	<?php echo '</script'; ?>
>

</body>
</html><?php }
}
