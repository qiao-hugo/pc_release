<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-18 19:00:30
  from "/data/httpd/vtigerCRM/apps/views/SalesDaily/list.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a379fcec94f22_69638190',
  'file_dependency' => 
  array (
    '15fd8316ef9d6c64c19bc3a337180a3ba9c6bd0e' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/SalesDaily/list.html',
      1 => 1486437671,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a379fcec94f22_69638190 ($_smarty_tpl) {
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
    <style type="text/css">
        
        *{
            text-shadow:none;
        }
        

        #bg{ display: none;  position: absolute;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.5;  opacity:.50;  filter: alpha(opacity=50);}  
        

    </style>
</head>
<body>

<div class="container-fluid w fix" id="demo-intro" data-role="page">
    <div data-role="header" data-position="fixed">
        <h1>日报列表</h1>
        <a href="index.php?action=mycrm"  data-ajax="false" data-transition="slide" id="daydealcancel" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
        <a href="index.php?module=SalesDaily&action=add" data-transition="slide" data-ajax="false" data-rel="back2" id="daydealclose"  class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-btn-active ui-btn-right ui-icon-plus ui-btn-icon-right">添加</a>
    </div>

    <div data-role="main" class="ui-content">
        <?php if (!empty($_smarty_tpl->tpl_vars['list']->value)) {?>

            <ul id="data_list" data-role="listview" data-inset="true">
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
                    <li>
                        <a data-transition="slide" class="gotoOne" data-id="<?php echo $_smarty_tpl->tpl_vars['value']->value['salesdailybasicid'];?>
" href="index.php?module=SalesDaily&action=one&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['salesdailybasicid'];?>
">
                        <span style="font-size:12px;">
                           <?php if ($_smarty_tpl->tpl_vars['value']->value['islook'] == 1) {?>
                           <span class="t_span_<?php echo $_smarty_tpl->tpl_vars['value']->value['salesdailybasicid'];?>
">(新批复)</span> 
                           <?php }?>
                           <?php echo $_smarty_tpl->tpl_vars['value']->value['smownerid'];?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['value']->value['dailydatetime'];?>
</span>
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
                没有日报
            </div>
        </div>
        <?php }?>
        



   </div>
   <div id="loading"  data-id="1" data-flag="2" class="loading" style="text-align: center; padding-bottom: 10px;">加载中...
   </div>
    <!-- <ul data-role="listview" data-inset="true">

        <li data-icon="search"><input type="text" ><a href="#">搜索</a></li>
    </ul> -->
</div>

<div data-role="page" id="pifu-page" data-url="daydeal-page">
    <div data-role="header" data-position="fixed">
        <h1>日报批复</h1>
        <a href="#demo-intro" data-rel="back" id="pifucancel" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
    </div>
    <div role="main" class="ui-content">
        
        
            <form method="post" action="demoform.php">
                <input type="hidden" name="relationid"/>
                <label for="daydealmarketprice">批复人</label>
                <input type="text" value="<?php echo $_smarty_tpl->tpl_vars['username']->value;?>
" disabled="disabled" class="form-control"/>
                <label for="daydealamount">批复日期</label>
                <input  type="text" value="<?php echo $_smarty_tpl->tpl_vars['nowtime']->value;?>
" disabled="disabled" class="form-control" data-clear-btn="true"/>
                <label for="daydealfirstpayment">批复内容</label>
                <textarea id="t_textarea" class="form-control"></textarea>
                <a href="javascript:void(0)" class="ui-btn a_submit">提交</a>
            </form>

    </div>
    <div data-role="popup" id="popupBasicform" >
        <div id="popupfrommsg">批复内容不能为空</div>
    </div>

    <div id="bg"></div>  
    <div data-role="popup" data-dismissible="false" id="popupSubmit" >
        <div id="mypopupfrommsg">正在提交...</div>
    </div>
</div>
 

<?php echo '<script'; ?>
 type="text/javascript">


    
    $(function(){

        //$( ".selector" ).collapsible( "expand" );


    

        $('#daydealcancel').click(function() {
            window.location.href = '/index.php?action=mycrm';
        });

        var totalnum=<?php echo $_smarty_tpl->tpl_vars['totalnum']->value;?>
;

        
        //$('html,body').animate({scrollTop:0}, 'slow');
        var $num = 2;//当前的页码
        $(window).scroll(function(){
            //此方法是在滚动条滚动时发生的函数
            // 当滚动到最底部以上100像素时，加载新内容
            var $doc_height,$s_top,$now_height,dataid,dataflag;
            $doc_height = $(document).height();        //这里是document的整个高度
            $s_top = $(this).scrollTop();            //当前滚动条离最顶上多少高度
            $now_height = $(this).height();            //这里的this 也是就是window对象
            dataid=$('#loading').data("id");//阻止一次请求没有完成后再次请
            dataflag=$('#loading').data("flag");//阻止当滚地到底部时刷新后自动请求
            if(($doc_height - $s_top - $now_height) < 100&& $num<=totalnum&&dataid==1&&dataflag==1){
                jsonajax();
                //alert();
            }

            $('#loading').data("flag",1);
            //alert($num);
            setTimeout(function() {
                if($num > totalnum) {
                    $('#loading').html("没有了").show();
                }
            }, 2000);
            
            
        });

        $('.gotoOne').click(function () {
            var id = $(this).attr('data-id');
            $('.t_span_'+id).remove();
        });

        //遮罩层提示
        function mark(type) {
            if(type == 'show') {
                //加载一个遮罩层
                $("#popupSubmit").popup('open');
                document.getElementById("bg").style.display="block";  
                $('html,body').animate({scrollTop: '0px'}, 100);
                $('#bg').bind("touchmove",function(e){  
                    e.preventDefault();  
                });
            } else {
                $("#popupSubmit").popup('close');
                document.getElementById("bg").style.display="none";  
            }
        };
        $('.goto_pifu').click(function() {
            $('input[name=relationid]').val($(this).attr('data-id'));
        });



        $('.a_submit').click(function () {
            var textarea = $('#t_textarea').val();
            var relationid = $('input[name=relationid]').val();
            if (!textarea) {
                $("#popupBasicform").popup('open');
                return;
            }
            
            $.ajax({ 
                url: "index.php?module=SalesDaily&action=approval", 
                context: document.body, 
                data: {
                    description : textarea,
                    relationid : relationid
                },
                dataType : 'json',
                beforeSend:function() {
                    mark('show');
                },
                success: function(){
                    //mark('none');
                    $("#mypopupfrommsg").html('批复成功');
                    setTimeout(function() {
                        window.location.href='index.php?module=SalesDaily&action=slist';
                    }, 1000);
                }
            });
        });

        function jsonajax(){
            $('#loading').data("id",2);
            $('#loading').html("正在加载请稍后...");
            $.ajax({
                url:'index.php?module=SalesDaily&action=slist',
                type:'POST',
                data: {
                    pagenum: $num++,
                    type: 'ajax'
                },
                //dataType:'html',
                success:function(html){
                    if(html){
                        //$('#loading').hide();
                        $('#data_list').append(html);
                        //$('#data_list').listview('refresh');  
                        $('#data_list').listview();  
                        //
                        //$(".selector").collapsible();
                        //$(".pifu_page_button").button();
                        $('#loading').data("id",1);
                    }
                }
            });
        }

    });


    

<?php echo '</script'; ?>
>

</body>
</html><?php }
}
