<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-21 14:34:16
  from "/data/httpd/vtigerCRM/apps/views/RefillApplication/add.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a3b55e8ad61c5_76730656',
  'file_dependency' => 
  array (
    '31a5ff54fcce3dabdb5524f50d26face643513a5' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/RefillApplication/add.html',
      1 => 1491985124,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a3b55e8ad61c5_76730656 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>添加申请拜访单</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="static/css/jquery.mobile-1.4.5.min.css" />
    <?php echo '<script'; ?>
 type="text/javascript" src="static/js/jquery-2.1.0.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="static/js/jquery.mobile-1.4.5.min.js"><?php echo '</script'; ?>
>
    <style type="text/css">
        
        .t_label{
            padding: 5px 0 0px 0;
        }
        #bg{ display: none;  position: absolute;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.5;  opacity:.50;  filter: alpha(opacity=50);}  
        
    </style>
</head>
<body>

<div class="container-fluid w fix" id="demo-intro" data-role="page">
    <div data-role="header" data-position="fixed">
        <h1>添加充值申请单</h1>
        <a href="index.php?module=RefillApplication&action=index" data-transition="slide" id="main_page_back" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
    </div>

    <div data-role="main" class="ui-content">
        <form method="post" id="main_page_form"  onsubmit='return main_check()' data-ajax="false" action="index.php?module=RefillApplication&action=doadd" method="POST">
            <div class="ui-field-contain">
                <label for="servicecontractsid" class="t_label">服务合同:
                    <a href="#servicecontracts-page" style="float:right; margin-right: 10px;">选择</a>
                </label>
                <input type="text"   readonly="readonly" name="servicecontractsid_dispaly" id="servicecontractsid_dispaly">
                <input type="hidden"  check-msg="服务合同不能为空"  name="servicecontractsid" id="servicecontractsid">

                
                <label for="accountid" class="t_label">客户:
                    <a href="#account-page" style="float:right; margin-right: 10px;">选择</a>
                </label> 
                <input type="text" readonly="readonly"  name="accountid_dispaly" id="accountid_dispaly">
                <input type="hidden"  name="accountid" id="accountid">

                <label for="remarks" class="t_label">备注</label>
                <textarea name="remarks" id="remarks" rows="5" class="form-control" 
                          data-content=""></textarea>
                <br/>

                <label for="file"></label>
                <a href="#" class="uploadfile">上传附件</a>
                <input type="file" name="fileupload" id="file" style="display: none;"/>

                <ul data-role="listview" id="filevalues" data-inset="false">
                        
                </ul>
                <br/>
                <div data-role="main" style="overflow:hidden;" role="banner" class="ui-header ui-bar-inherit ui-corner-all">
                    <h1 class="ui-title" role="heading" aria-level="1">充值明细</h1>
                    <a href="#detail-page" data-icon="plus" id="goto-detail-page" class="ui-btn-right ui-link ui-btn ui-icon-plus ui-btn-icon-right ui-shadow ui-corner-all" data-role="button" role="button">&nbsp;</a>

                    <ul data-role="listview" id="rechargesheet" data-inset="true">
                      <!-- <li data-icon="delete"><a  href="#detail-page" class="rechargesheet_edit" >11111</a><a href="javascript:void(0)" class="rechargesheet_delete">data-icon="delete"</a>
                      </li> --> 
                    </ul>

                </div>

                <div class="confirm tc">
                    <button class="ui-btn ui-btn-b ui-shadow ui-corner-all">提 交</button>
                </div>
            </div>
        </form>
    </div>

    <div data-role="popup" id="main_page_popup" >
        <div></div>
    </div>
    <div data-role="popup" data-dismissible="false" id="main_page_popup_submit" >
        <div>正在提交...</div>
    </div>
</div>


<div data-role="page" id="servicecontracts-page" data-url="servicecontracts-page" source="">
    <div data-role="header" data-position="fixed">
        <h1>选择服务合同</h1>
        <a href="#demo-intro" data-rel="back" id="servicecontracts-page-back" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
        <!-- <a href="javascript:void(0)" data-rel="back2" id="servicecontracts_page_close" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-btn-active ui-btn-right ui-icon-plus ui-btn-icon-right">保存</a> -->
    </div>
    <div role="main" class="ui-content">
        <form name="servicecontracts_page_form" id="servicecontracts_page_form">
            <label for="search_servicecontracts" class="t_label">服务合同:
                <a href="javascript:void(0)" id="servicecontracts_page_search" style="float:right; margin-right: 10px;">搜索</a>
            </label>
            <input type="text" name="search_servicecontracts" id="search_servicecontracts">
        </form>

        <ul data-role="listview" id="servicecontracts_list" data-inset="true">
            <!-- <li><a href="javascript:void(0)" class="servicecontracts_list_li"></a></li> -->
        </ul>
        <div id="servicecontracts_list_display" style="display:none;">没有搜到到服务合同，请输入客户名称或者合同编号</div>
        
    </div>

    
    <div data-role="popup" data-dismissible="false" id="servicecontracts_page_popup" >
        <div>正在提交...</div>
    </div>
</div>

<div data-role="page" id="account-page" data-url="account-page" source="">
    <div data-role="header" data-position="fixed">
        <h1>选择客户</h1>
        <a href="#demo-intro" data-rel="back" id="account-page-back" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
    </div>
    <div role="main" class="ui-content">
        <form name="account_page_form" id="servicecontracts_page_form">
            <label for="search_account" class="t_label">客户:
                <a href="javascript:void(0)" id="account_page_search" style="float:right; margin-right: 10px;">搜索</a>
            </label>
            <input type="text" name="search_account" id="search_account">
        </form>

        <ul data-role="listview" id="account_list" data-inset="true">
            <!-- <li><a href="javascript:void(0)" class="servicecontracts_list_li"></a></li> -->
        </ul>
        
    </div>

    
    <div data-role="popup" data-dismissible="false" id="account_page_popup" >
        <div>正在搜索...</div>
    </div>
</div>


<div data-role="page" id="detail-page" data-url="detail-page" source="">
    <div data-role="header" data-position="fixed">
        <h1>充值明细</h1>
        <a href="#demo-intro" data-rel="back" id="detail-page-back" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
        <a href="javascript:void(0)" data-rel="back2" id="detail_page_close" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-btn-active ui-btn-right ui-icon-plus ui-btn-icon-right">保存</a>
    </div>
    <div role="main" class="ui-content">
        <form name="detail_page_form" id="detail_page_form">
            <input type="hidden" name="num" value="">
            <label for="topplatform" class="t_label"><span style="color:red;">*</span>充值平台:</label>
                <select name="topplatform"  data-native-menu="false" id="topplatform">
                    <?php
$_from = $_smarty_tpl->tpl_vars['topplatform']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_wlist_0_saved_item = isset($_smarty_tpl->tpl_vars['v']) ? $_smarty_tpl->tpl_vars['v'] : false;
$_smarty_tpl->tpl_vars['v'] = new Smarty_Variable();
$__foreach_wlist_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_wlist_0_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['v']->value) {
$__foreach_wlist_0_saved_local_item = $_smarty_tpl->tpl_vars['v'];
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['v']->value['topplatform'];?>
"><?php echo $_smarty_tpl->tpl_vars['v']->value['topplatform'];?>
</option>
                    <?php
$_smarty_tpl->tpl_vars['v'] = $__foreach_wlist_0_saved_local_item;
}
}
if ($__foreach_wlist_0_saved_item) {
$_smarty_tpl->tpl_vars['v'] = $__foreach_wlist_0_saved_item;
}
?>
                </select>

                <label for="accountzh" class="t_label" style="display: inline;"><span style="color:red;">*</span>账号:</label><a href="#" onclick="check_accountzh_input()" style="float:right; margin-right: 10px;" class="ui-link">选择</a>
                <a href="#accountzh-page" id="accountzh_search_hide" style="display: none"></a>

                <input type="text" name="accountzh" check="notEmpty" check-msg="账号不能为空" id="accountzh">
                <label for="did" class="t_label"><span style="color:red;">*</span>ID:</label>
                <input type="text" name="did"  id="did" check="notEmpty" check-msg="ID不能为空">

                <label for="rechargetype" class="t_label select"><span style="color:red;">*</span>类型:</label>
                <!-- data-native-menu 添加好一些-->
                <select name="rechargetype" id="rechargetype" data-native-menu="false" class="select" check="notEmpty">
                    <option value="c_recharge" selected>充值</option>
                    <option value="c_refund">退款</option>
                </select>

                <label for="receivementcurrencytype"  class="t_label select">货币类型:</label>
                <select name="receivementcurrencytype" class="select"  data-native-menu="false" id="receivementcurrencytype">
                    <option value="人民币" selected>人民币</option>
                    <option value="美金">美金</option>
                    <option value="日元">日元</option>
                    <option value="欧元">欧元</option>
                </select>

                <label for="exchangerate" class="t_label">汇率:</label>
                <input type="text" name="exchangerate" check="number" check-msg="汇率不能为空且为数字" value="1.0" id="exchangerate">

                <label for="rechargeamount" class="t_label"><span style="color:red;">*</span>收款金额:</label>
                <input type="text" name="rechargeamount"  check="notEmpty,number" check-msg="必填,且收款金额格式要正确" id="rechargeamount">

                <label for="prestoreadrate" class="t_label"><span style="color:red;">*</span>充值金额:</label>
                <input type="text" name="prestoreadrate" check="notEmpty,number" check-msg="必填,且充值金额格式要正确" id="prestoreadrate">

                <label for="discount" class="t_label"><span style="color:red;">*</span>返点:</label>
                <input type="text" name="discount"  id="discount" check="notEmpty" check-msg="折扣不能为空">

                <label for="tax" class="t_label">税点:</label>
                <select name="tax" class="select"  data-native-menu="false" id="tax">
                    <option value="6%">6%</option>
                    <option value="17%">17%</option>
                </select> 
                <!-- <input type="text" name="tax" check="number" check-msg="税点格式不正确" id="tax"> -->

                <label for="factorage" class="t_label">代理商服务费:</label>
                <input type="text" name="factorage" check="number" check-msg="代理商服务费格式不正确" id="factorage">

                <label for="activationfee" class="t_label">开户费:</label>
                <input type="text" name="activationfee" check="number" check-msg="开户费格式不正确" id="activationfee">

                <label for="totalcost" class="t_label">合计费用:</label>
                <input type="text" name="totalcost" check="number" check-msg="合计费用格式不正确" id="totalcost">

                <label for="dailybudget" class="t_label">每日预算:</label>
                <input type="text" name="dailybudget" check="number" check-msg="每日预算格式不正确" id="dailybudget">

                <label for="transferamount" class="t_label">转款金额:</label>
                <input type="text" name="transferamount" check="number" check-msg="转款金额格式不正确" id="transferamount">

                <label for="rebateamount" class="t_label">返点金额:</label>
                <input type="text" name="rebateamount" check="number" check-msg="返点金额格式不正确" id="rebateamount">

                <label for="totalgrossprofit" class="t_label">毛利总计:</label>
                <input type="text" name="totalgrossprofit" check="number" check-msg="毛利总计格式不正确" id="totalgrossprofit">

                <label for="servicecost" class="t_label">服务成本:</label>
                <input type="text" name="servicecost" check="number" check-msg="服务成本格式不正确" id="servicecost">

                <label for="mstatus" class="t_label">状态:</label>
                <input type="text" name="mstatus" id="mstatus">
            </form>
    </div>
    <div data-role="popup" id="popupBasicform" >
        <div id="popupfrommsg"></div>
    </div>
</div>
<div data-role="page" id="accountzh-page" data-url="accountzh-page" source="">
    <div data-role="header" data-position="fixed">
        <h1>选择账户</h1>
        <a href="#detail-page" data-rel="back" id="accountzh-page-back" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
    </div>
    <div role="main" class="ui-content">
        <input type="button" id="accountzh_page_search" value="搜索">
        <!--<label for="search_account" class="t_label">客户:
            <a href="javascript:void(0)" id="accountzh_page_search" style="float:right; margin-right: 10px;">搜索</a>
        </label>-->
        <ul data-role="listview" id="accountzh_list" data-inset="true">
            <!-- <li><a href="javascript:void(0)" class="servicecontracts_list_li"></a></li> -->
        </ul>
        <div id="accountzh_list_display" style="display:none;">没有搜到历史账号</div>
    </div>
    <div data-role="popup" data-dismissible="false" id="accountzh_page_popup" >
        <div>正在搜索...</div>
    </div>

</div>

<div id="bg"></div>  
<!-- <div data-role="page" data-dialog="true" id="pagetwo">
  <div data-role="header">
    <h1>提示</h1>
  </div>
  <div data-role="main" class="ui-content">
    <p></p>
  </div>
</div>  -->


<?php echo '<script'; ?>
 type="text/javascript"> 
    //遮罩层提示
    var mark = function(page_mark, type) {
        if(type == 'show') {
            //加载一个遮罩层
            $(page_mark).popup('open');
            document.getElementById("bg").style.display="block";  
            $('html,body').animate({scrollTop: '0px'}, 100);
            $('#bg').bind("touchmove",function(e){  
                e.preventDefault();  
            });
        } else {
            $(page_mark).popup('close');
            document.getElementById("bg").style.display="none";  
        }
    };
    var num = 1;
    var getDetailsPageData = function(e) {
        var topplatform = $.trim($('select[name=topplatform]').val()); //充值平台
        var accountzh = $.trim($('input[name=accountzh]').val());

        var flag = true;
        $('#detail_page_form input').each(function() {
            var check = $(this).attr('check');
            check = check ? check : '';
            if (check.indexOf('notEmpty') >= 0) {
                var v = $.trim($(this).val());
                if (!v) {
                    $("#popupBasicform").find('#popupfrommsg').text($(this).attr('check-msg'));
                    $("#popupBasicform").popup('open');
                    flag = false;
                    return false;
                }
            }
            if (check.indexOf('number') >= 0) {
                var v = $.trim($(this).val());
                if (isNaN(v)) {
                    $("#popupBasicform").find('#popupfrommsg').text($(this).attr('check-msg'));
                    $("#popupBasicform").popup('open');
                    flag = false;
                    return false;
                }
            }
        });


        // 当“类型”选择到 退款时，“转账金额”变为必选项。
        var rechargetype = $('select[name=rechargetype]').val();
        if (rechargetype == 'c_refund') {
            var transferamount = $.trim($('input[name=transferamount]').val());
            if (! parseFloat(transferamount)) {
                $("#popupBasicform").find('#popupfrommsg').text('类型为退款时，转款金额不能为空');
                $("#popupBasicform").popup('open');
                flag = false;
                return false;
            }
        }

        if (!flag) {
            return ;
        }
        
        var page_source = $('#detail-page').attr('source');
        if (page_source) {
            num = page_source.replace('inserti', '');
        } else {
            num ++;
        }


        var title = accountzh + '['+topplatform+']';
        var hidden_html = '<input type="hidden" name="inserti['+num+']" value="'+num+'">';
        $('#detail_page_form input,#detail_page_form select').each(function() {
            var v = $.trim($(this).val());
            var name = $(this).attr('name');
            if (name == 'num') {
                //hidden_html += '<input type="hidden" name="'+name+'" value="t'+num+'">';
                hidden_html += '';
            } else {
                hidden_html += '<input type="hidden" name="'+name+'['+num+']" value="'+v+'">';
            }
        });

        if (page_source) {
            $('#inserti'+num).remove();
        }
        var str='<li data-icon="delete" id="inserti'+num+'"><a href="#detail-page" class="rechargesheet_edit" >'+title+'</a><a href="javascript:void(0)" class="rechargesheet_delete">data-icon="delete"</a>'+hidden_html+'</li>';
        $('#rechargesheet').append(str);

        /*var page_source = $('#detail-page').attr('source');
        if (page_source) {
            alert(title);
            var str='<a href="#detail-page" class="rechargesheet_edit" >'+title+'</a><a href="javascript:void(0)" class="rechargesheet_delete">data-icon="delete"</a>'+hidden_html+'';
            $('#t'+page_source).html(str);
        } else {
            var str='<li data-icon="delete" id="t'+num+'"><a href="#detail-page" class="rechargesheet_edit" >'+title+'</a><a href="javascript:void(0)" class="rechargesheet_delete">data-icon="delete"</a>'+hidden_html+'</li>';
            $('#rechargesheet').append(str);
        }*/

        //$('#detail-page-back').trigger("click");
        $.mobile.changePage( "#demo-intro",  "slideup");
        //$('#demo-intro').page();
        $("#rechargesheet" ).listview("refresh");
        //window.history.go(-1);
    };
    $('#detail_page_close').click(function() {
        getDetailsPageData();
    });

    // 修改明细
    $("#rechargesheet").on("click","a.rechargesheet_edit", function() {
        var $li = $(this).parent();
        var numflag = $li.attr('id');
        $li.find('select,input').each(function (){
            var name = $(this).attr('name');
            name = name.replace('['+numflag+']', '');
            $('#'+name).val($(this).val());
        });
        $('#detail-page').attr('source', numflag);
    });
    

    // 删除明细
    $("#rechargesheet").on("click","a.rechargesheet_delete", function() {
        $(this).parent().remove();
        $("#rechargesheet" ).listview("refresh");
    });

    // 跳转到 充值明细
    $('#goto-detail-page').click(function() {
        $('#detail_page_form input').each(function(){
            var  name = $(this).attr('name');
            if (name == 'exchangerate') {
                $(this).val('1.0');
            } else {
                $(this).val('');
            }
        });
        $('#detail-page').attr('source', '');
    });

    // 客户搜索
    $('#account_page_search').click(function() {
        var search_account = $.trim($('#search_account').val());
        if (!search_account) {
            return;
        }
        $.ajax({ 
            url: "index.php?module=RefillApplication&action=search_account", 
            data: {
                company : search_account
            },
            type:'POST',
            beforeSend:function() {
                mark('#account_page_popup', 'show');
            },
            success: function(data){
                mark('#account_page_popup', 'none');

                data = JSON.parse(data);
                if (data) {
                    var str = '';
                    for(var i in data) {
                        var label = data[i]['label'];
                        var id = data[i]['id'];
                        str += '<li><a href="#demo-intro" class="account_list_li" t_label="'+label+'"  t_id="'+id+'" class="account_list_li">'+label+'</a></li>';
                    }
                    $('#account_list').append(str);
                    $("#account_list" ).listview("refresh");
                }
            }
        });
    });

    // 服务合同搜索
    $('#servicecontracts_page_search').click(function() {
        //mark('#servicecontracts_page_popup', 'show');
        var search_servicecontracts = $.trim($('input[name=search_servicecontracts]').val());
        if (!search_servicecontracts) {
            return;
        }

        $.ajax({ 
            url: "index.php?module=RefillApplication&action=search_servicecontracts", 
            data: {
                search_servicecontracts : search_servicecontracts
            },
            type:'POST',
            beforeSend:function() {
                mark('#servicecontracts_page_popup', 'show');
            },
            success: function(data){
                mark('#servicecontracts_page_popup', 'none');

                data = JSON.parse(data);
                if (data) {
                    var str = '';
                    for(var i in data) {
                        var servicecontractsid = data[i]['servicecontractsid'];
                        var contract_no = data[i]['contract_no'];
                        var accountname = data[i]['accountname'];

                        var accountid = data[i]['accountid'];

                        str += '<li><a href="#demo-intro" accountname="'+accountname+'" accountid="'+accountid+'" contract_no="'+contract_no+'" accountname="'+accountname+'" servicecontractsid="'+servicecontractsid+'"  class="servicecontracts_list_li">'+contract_no+'['+accountname+']</a></li>';
                    }
                    if (str=='') {
                        $('#servicecontracts_list_display').show();
                    } else {
                        $('#servicecontracts_list_display').hide();
                    }
                    $('#servicecontracts_list').html('');
                    $('#servicecontracts_list').append(str);
                    $("#servicecontracts_list" ).listview("refresh");
                }
            }
        });
    });

    function check_accountzh_input(){
        var accountid = $.trim($('#accountid').val());
        if (accountid == "") {
            alert("客户未设置!");
            return;
        }
        var topplatform = $("#topplatform").val();
        if (topplatform == "") {
            alert("充值平台未设置!");
            return;
        }
        $("#accountid_dispaly")
        $('#accountzh_list').empty();
        $("#accountzh_search_hide").click();
    }
    // 客户账户搜索
    $('#accountzh_page_search').click(function() {
        var accountid = $.trim($('#accountid').val());
        if (accountid == "") {
            return;
        }
        var topplatform = $("#topplatform").val();
        if (topplatform == "") {
            return;
        }
        $.ajax({
            url: "index.php?module=RefillApplication&action=search_accountzh",
            data: {
                accountid : accountid,
                topplatform : topplatform
            },
            type:'POST',
            beforeSend:function() {
                mark('#accountzh_page_popup', 'show');
            },
            success: function(data){
                mark('#accountzh_page_popup', 'none');
                $('#accountzh_list').empty();
                data = JSON.parse(data);
                if (data) {
                    var str = '';
                    for(var i in data) {
                        var label = data[i]['accountzh'];
                        var id = data[i]['did'];
                        str += '<li><a href="#detail-page" class="accountzh_list_li" t_label="'+label+'"  t_id="'+id+'" class="accountzh_list_li">'+label+'(ID:'+id+')</a></li>';
                    }
                    $('#accountzh_list').append(str);
                    $("#accountzh_list" ).listview("refresh");

                    $('#accountzh_list_display').hide();
                }else{
                    $('#accountzh_list_display').show();
                }

            }
        });

    });

    // 点击服务合同
    $('#servicecontracts_list').on("click","a.servicecontracts_list_li", function() {
        var contract_no = $(this).attr('contract_no');
        var accountname = $(this).attr('accountname');
        var servicecontractsid = $(this).attr('servicecontractsid');
        var accountid = $(this).attr('accountid');
        var accountname = $(this).attr('accountname');
        $('#accountid').val(accountid);
        $('#accountid_dispaly').val(accountname);
        $('#servicecontractsid').val(servicecontractsid);
        $('#servicecontractsid_dispaly').val(contract_no);
    });
    $('.uploadfile').on('click', function(e) {
        e.preventDefault();

        $('#file').trigger('click');
    })
    $('input[type="file"]').on('change',function(){
        var filename=$(this)[0].files[0];

		      
        
        var reader = new FileReader(); 
        var data = new FormData();
        data.append('uploadfiles',filename);
        

        var url='index.php?module=RefillApplication&action=upload';

        var xhr = new XMLHttpRequest();
        //xhr.upload.addEventListener("progress", uploadProgress, false);//监听上传进度
        xhr.addEventListener("load", uploadComplete, false);
        //xhr.addEventListener("error", uploadFailed, false);
        xhr.open("POST", url);
        xhr.send(data);
        function uploadComplete(evt) {
            /* 服务器端返回响应时候触发event事件*/

            var jsonparse=JSON.parse(evt.target.responseText);
            if(jsonparse.success){
                var str='<li><label>'+jsonparse.result.filename+'</label><br><input type="hidden" name="files[]" value="'+jsonparse.result.filename+'##'+jsonparse.result.id+'"></li>';
                $('#filevalues').append(str)
            }else{
                alert('上传失败');
                //alert(jsonparse.msg.type);
            }

        }

    });

    //account_list_li 点击客户
    $('#account_list').on("click","a.account_list_li", function() {
        var t_label = $(this).attr('t_label');
        var t_id = $(this).attr('t_id');
        $('#accountid').val(t_id);
        $('#accountid_dispaly').val(t_label);

        $('#servicecontractsid').val('');
        $('#servicecontractsid_dispaly').val('');
    });

    //accountzh_list_li 点击账户
    $('#accountzh_list').on("click","a.accountzh_list_li", function() {
        var t_label = $(this).attr('t_label');
        var t_id = $(this).attr('t_id');
        $('#accountzh').val(t_label);
        $('#did').val(t_id);
    });

    // 添加提交数据
    function main_check() {
        // 服务合同不能为空
        /*var servicecontractsid = $('#servicecontractsid').val();
        if (!servicecontractsid) {
            $("#main_page_popup").find('div').text($('#servicecontractsid').attr('check-msg'));
            $("#main_page_popup").popup('open');
            return false;
        }*/ 

        var li_num = $('#rechargesheet').find('li').size();
        if (li_num == 0) {
            $("#main_page_popup").find('div').text('请添加充值明细');
            $("#main_page_popup").popup('open');
            return false;
        }

        mark('#main_page_popup_submit', 'show');

        $.ajax({
            type: "POST",
            url: 'index.php?module=RefillApplication&action=doadd',
            data: $('#main_page_form').serialize(),// 你的formid
            success: function(data) {
                //mark('#main_page_popup_submit', 'none');
                data = JSON.parse(data);
                if(parseInt(data[1]) > 0){
                	$("#main_page_popup").find('div').text('当前客户已垫款' + data[1] + '元');
                }
                if (parseInt(data[0]) > 0) {
                    setTimeout(function() {
                        window.location.href="index.php?module=RefillApplication&action=index";
                    }, 100);
                    //$('#main_page_back').trigger('click');
                } else {
                    alert('未知错误，请重新添加');
                }
            }
        });
        return false;
    }
<?php echo '</script'; ?>
>


</body>
</html><?php }
}
