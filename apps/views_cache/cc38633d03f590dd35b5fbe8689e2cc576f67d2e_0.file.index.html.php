<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-18 15:43:08
  from "/data/httpd/vtigerCRM/apps/views/RefillApplication/index.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a37718c588218_78482357',
  'file_dependency' => 
  array (
    'cc38633d03f590dd35b5fbe8689e2cc576f67d2e' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/RefillApplication/index.html',
      1 => 1506484282,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a37718c588218_78482357 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>充值申请单</title>
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
    <style type="text/css">
        
        *{
            text-shadow:none;
        }
        
        #bg{ display: none;  position: absolute;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.5;  opacity:.50;  filter: alpha(opacity=50);}  
        

    </style>
</head>
<body>

<div class="container-fluid w fix ui-page-theme-d" id="demo-intro" data-role="page">
    <div data-role="header" data-position="fixed">
        <h1>充值申请单</h1>
        <a href="javascript:void(0)" data-transition="slide" id="daydealcancel" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a> 
        <a href="index.php?module=RefillApplication&action=add" data-ajax="false" data-transition="slide" data-rel="back2" id="addRefillApplication" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-btn-active ui-btn-right ui-icon-plus ui-btn-icon-right">添加</a>
    </div>
	<div class="container-fluid w fix ui-page-theme-d" data-role="none">
	<form action="index.php?module=RefillApplication&action=index" method="post" data-ajax="false">
		<select name="status" id="status" data-native-menu="false">
			<option value="check" <?php if ($_smarty_tpl->tpl_vars['status']->value == 'actioning') {?>selected<?php }?>>审核中</option>
			<option value="all" <?php if ($_smarty_tpl->tpl_vars['status']->value == 'all') {?>selected<?php }?>>所有</option>
		</select>
        <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" >
            <div class="ui-controlgroup-controls" data-inset="false">
                <div class="ui-radio"><label for="radio-v-1a" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-radio-on ui-first-child">申请单编号</label><input type="radio" name="radiot" id="radio-v-1a" value="vtiger_refillapplication.refillapplicationno" <?php if ($_smarty_tpl->tpl_vars['fieldname']->value == 'vtiger_refillapplication.refillapplicationno') {?>checked="checked"<?php }?>></div>
                <div class="ui-radio"><label for="radio-v-1b" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-radio-off">服务合同</label><input type="radio" name="radiot" id="radio-v-1b" value="vtiger_servicecontracts.contract_no" <?php if ($_smarty_tpl->tpl_vars['fieldname']->value == 'vtiger_servicecontracts.contract_no') {?>checked="checked"<?php }?>></div>
                <div class="ui-radio"><label for="radio-v-1c" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-radio-off ui-last-child">客户名称</label><input type="radio" name="radiot" id="radio-v-1c" value="vtiger_account.accountname" <?php if ($_smarty_tpl->tpl_vars['fieldname']->value == 'vtiger_account.accountname') {?>checked="checked"<?php }?>></div>
            </div>
        </fieldset>
        <input type="text" name="searchvalue" id="text-basic" placeholder="请输入要查找的内容" value="<?php echo $_smarty_tpl->tpl_vars['fieldvalue']->value;?>
">
		<input type="submit" name="submit" value="搜索">
		</form>
    </div>
    <div data-role="main" class="ui-content" data-theme="d">
        <?php if (!empty($_smarty_tpl->tpl_vars['list']->value)) {?>

            <ul id="data_list" data-role="listview" data-inset="false" class="ui-nodisc-icon ui-alt-icon">
                <?php
$_from = $_smarty_tpl->tpl_vars['list']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_wlist_0_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_wlist_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_wlist_0_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_wlist_0_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                <?php $_smarty_tpl->tpl_vars['IMGMD'] = new Smarty_Variable(md5($_smarty_tpl->tpl_vars['value']->value['email']), null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'IMGMD', 0);?>
                    <li>
                        <a data-transition="slide" class="gotoOne" data-ajax="false" data-id="<?php echo $_smarty_tpl->tpl_vars['value']->value['salesdailybasicid'];?>
" href="/index.php?module=RefillApplication&action=one&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['refillapplicationid'];?>
">
                            <img src="<?php if (isset($_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value])) {
echo $_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value];
} else { ?>../../static/img/trueland.png<?php }?>" style="height:80px;width:80px;border: 1px solid #eee;border-radius:80px;overflow: hidden;">
                            <h2><?php echo $_smarty_tpl->tpl_vars['value']->value['accountid'];?>
</h2><p>【<?php echo $_smarty_tpl->tpl_vars['value']->value['servicecontractsid'];?>
】</p>
                        </a>
                    </li>
                <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_0_saved_local_item;
}
}
if ($__foreach_wlist_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_0_saved_item;
}
?>
            </ul>
        <?php } else { ?>
        <div class="panel panel-default">
            <div class="panel-heading" data-parent="#accordion3" style="text-align: center;">
                没有充值申请单
            </div>
        </div>
        <?php }?>
        



   </div>
   <div id="loading"  data-id="1" data-flag="2" data-searchfieldname="<?php echo $_smarty_tpl->tpl_vars['fieldname']->value;?>
"  data-searchvalue="<?php echo $_smarty_tpl->tpl_vars['fieldvalue']->value;?>
" data-searchflag="<?php echo $_smarty_tpl->tpl_vars['searchflag']->value;?>
" class="loading" data-totalnum="<?php echo $_smarty_tpl->tpl_vars['totalnum']->value;?>
" data-status="<?php if ($_smarty_tpl->tpl_vars['status']->value == 'all') {?>all<?php } else { ?>check<?php }?>" style="text-align: center; padding-bottom: 10px;">加载中...
   </div>

   <?php echo '<script'; ?>
 type="text/javascript">
  
    
    $(function(){
        //$( ".selector" ).collapsible( "expand" );
    

        /*$('#addRefillApplication').click(function() {
            window.location.href = '/index.php?module=RefillApplication&action=add';
        });*/

        $('#daydealcancel').click(function() {
            window.location.href = '/index.php?action=mycrm';
        });

        

        
        //$('html,body').animate({scrollTop:0}, 'slow');
        var $num = 2;//当前的页码
        $(window).scroll(function(){
            //此方法是在滚动条滚动时发生的函数
            // 当滚动到最底部以上100像素时，加载新内容
            var $doc_height,$s_top,$now_height,dataid,dataflag,datastatus;
            $doc_height = $(document).height();        //这里是document的整个高度
            $s_top = $(this).scrollTop();            //当前滚动条离最顶上多少高度
            $now_height = $(this).height();            //这里的this 也是就是window对象
            dataid=$('#loading').attr("data-id");//阻止一次请求没有完成后再次请
            dataflag=$('#loading').attr("data-flag");//阻止当滚地到底部时刷新后自动请求
            datastatus=$('#loading').attr("data-status");//取当前的状态
            var status = $("#status").val();
			var totalnum=$('#loading').attr("data-totalnum");
            if((($doc_height - $s_top - $now_height) < 100&& $num<=totalnum&&dataid==1&&dataflag==1) || datastatus!=status){
                jsonajax();
                //alert();
            }

            $('#loading').attr("data-flag",1);
            //alert($num);
            setTimeout(function() {
                if($num > totalnum) {
                    $('#loading').html("没有了").show();
                }
            }, 2000);
            
        });

        function jsonajax(){
            $('#loading').attr("data-id",2);
            $('#loading').html("正在加载请稍后...");
            var status = $("#status").val();//取当前审核的条件
            var datastatus=$('#loading').attr("data-status");//取当前已经存在的审核 条件
            $('#loading').attr("data-status",status);//当当前的审核条件给已经存在的条件
            var radiovalue='';
            var searchvalue='';
            if($('#loading').attr("data-searchflag")==2){
                //只有通过搜索过来的才加载该查询条件,
                radiovalue= $('input[name="radiot"]:checked').val();
                searchvalue= $('input[name="searchvalue"]').val();
                //如果又更新条件没有点搜索则保留原来的条件
                if(searchvalue!=$('#loading').attr("data-searchvalue") || $('#loading').attr("data-searchfieldname") !=radiovalue){
                    searchvalue=$('#loading').attr("data-searchvalue");
                    radiovalue=$('#loading').attr("data-searchfieldname");
                }
            }
            if(status!=datastatus){
                $num=1;//条件不一样重新从第一页开始
            }
            $.ajax({
                url:'index.php?module=RefillApplication&action=index&type=ajax',
                type:'POST',
                data: {
                    pagenum: $num++,
                    type: 'ajax',
                    'radiot':radiovalue,
                    'searchvalue':searchvalue,
                    status:status,
                },
                //dataType:'html',
                success:function(html){
                    if(html){
                        if(status!=datastatus){
                            $('#data_list').html('');
                        }
                        $('#loading').hide();
                        $('#data_list').append(html);
                        $('#data_list').listview('refresh');  
                        $('#data_list').listview();
                        $('#loading').attr("data-id",1);
                        //
                        //$(".selector").collapsible();
                        //$(".pifu_page_button").button();
                        //$('#loading').data("id",1);
                    }
                }
            });
        }
    });
    
    <?php echo '</script'; ?>
>


</div>





</body>
</html><?php }
}
