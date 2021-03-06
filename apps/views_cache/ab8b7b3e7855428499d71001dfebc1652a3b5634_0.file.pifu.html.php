<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-18 23:38:00
  from "/data/httpd/vtigerCRM/apps/views/SalesDaily/pifu.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a37e0d8718ff2_42839575',
  'file_dependency' => 
  array (
    'ab8b7b3e7855428499d71001dfebc1652a3b5634' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/SalesDaily/pifu.html',
      1 => 1486367358,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a37e0d8718ff2_42839575 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>销售日报详情</title>
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
        .sales_title_t{
            font-size: 12px;
            font-weight:bold;
        }
        .sales_info_div{
             font-size: 12px;

            margin-bottom: 8px;
        }

        #bg{ display: none;  position: absolute;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.5;  opacity:.50;  filter: alpha(opacity=50);}  
        

    </style>
</head>
<body>


<div data-role="page" class="container-fluid w fix" id="demo-intro" >
    <div data-role="header" data-position="fixed">
        <h1>日报批复</h1>
        <a href="#demo-intro" data-rel="back" id="pifucancel" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
    </div>
    <div role="main" class="ui-content">
        
        
            <form method="post" action="demoform.php">
                <input type="hidden" name="relationid" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"/>
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
    });


    

<?php echo '</script'; ?>
>

</body>
</html><?php }
}
