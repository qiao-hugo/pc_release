<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-18 21:15:35
  from "/data/httpd/vtigerCRM/apps/views/SalesDaily/one.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a37bf770f55d4_25086892',
  'file_dependency' => 
  array (
    '703ee49a2174c2eb981c2f7406382a0b996dd1df' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/SalesDaily/one.html',
      1 => 1486367358,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a37bf770f55d4_25086892 ($_smarty_tpl) {
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

<div class="container-fluid w fix" id="demo-intro" data-role="page">
    <div data-role="header" data-position="fixed">
        <h1>日报详情</h1>
        <a href="#demo-intro" data-rel="back" data-transition="slide" id="daydealcancel" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
        <a href="index.php?module=SalesDaily&action=goto_approval_ui&id=<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" data-transition="slide" data-rel="back2" id="daydealclose" data-ajax="false" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-btn-active ui-btn-right ui-icon-plus ui-btn-icon-right">批复</a>
    </div>

    <div data-role="main" class="ui-content">
        <div id="data_list"  data-role="collapsible-set">

                <div  class="selector" data-role="collapsible"  data-collapsed="false">
                    <h3>基本信息</h3>
                    <div class="sales_info_div">
                        <span class="sales_title_t">填写日期:</span>
                        <?php echo $_smarty_tpl->tpl_vars['data']->value['basic']['createdtime'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">日报日期:</span>
                        <?php echo $_smarty_tpl->tpl_vars['data']->value['basic']['dailydatetime'];?>
 
                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">填写人员:</span>
                        <?php echo $_smarty_tpl->tpl_vars['data']->value['basic']['last_name'];?>
 
                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">客户名称:</span>
                        <?php echo $_smarty_tpl->tpl_vars['data']->value['basic']['accountname'];?>
 
                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">备　　注:</span>
                        <?php echo $_smarty_tpl->tpl_vars['data']->value['basic']['content'];?>
 
                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">今日总结:</span>
                        <?php echo $_smarty_tpl->tpl_vars['data']->value['basic']['todaycontent'];?>
 
                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">明日计划:</span>
                        <?php echo $_smarty_tpl->tpl_vars['data']->value['basic']['tommorrowcontent'];?>
 
                    </div>
                    
                </div>

                <div  class="selector" data-role="collapsible">
                    <h5 >每日新增40%客户</h5>
                    <ul data-icon="false"  data-role="listview" data-inset="true">
                    <?php
$_from = $_smarty_tpl->tpl_vars['data']->value['foutnotv'];
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
                                <a  href="#">
                                    <div class="sales_info_div">
                                        <span class="sales_title_t">客户名称:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['accountname'];?>

                                    </div>
                                    <div class="sales_info_div">
                                        <span class="sales_title_t">姓　　名:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['linkname'];?>

                                    </div>
                                    <div class="sales_info_div">
                                        <span class="sales_title_t">手　　机:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['mobile'];?>

                                    </div>
                                    <div class="sales_info_div">
                                        <span class="sales_title_t">职　　位:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['title'];?>

                                    </div>
                                    <div class="sales_info_div">
                                        <span class="sales_title_t">拜访时间:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['startdatetime'];?>

                                    </div>
                                    <div class="sales_info_div">
                                        <span class="sales_title_t">回访内容:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['mcontent'];?>

                                    </div>
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
                </div>

                <div  class="selector" data-role="collapsible">
                    <h5 >近期可成交的客户</h5> 
                    <ul data-icon="false"  data-role="listview" data-inset="true">
                        <?php
$_from = $_smarty_tpl->tpl_vars['data']->value['candeal'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_wlist_1_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_wlist_1_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_wlist_1_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_wlist_1_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                        <li>
                            <a  href="#">
                                <div class="sales_info_div">
                                    <span class="sales_title_t">客户名称:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['accountname'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">姓　　名:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['contactname'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">手　　机:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['mobile'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">职　　位:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['title'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">产　　品:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['productname'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">报　　价:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['quote'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">首　付款:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['firstpayment'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">已签合同:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['issigncontract'];?>

                                </div>
                            </a>
                        </li>
                        <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_1_saved_local_item;
}
}
if ($__foreach_wlist_1_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_1_saved_item;
}
?>
                    </ul>
                </div>

                <div class="selector" data-role="collapsible">
                    <h5 >每日收款客户</h5>
                    <ul data-icon="false"  data-role="listview" data-inset="true">
                        <?php
$_from = $_smarty_tpl->tpl_vars['data']->value['daydeal'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_wlist_2_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_wlist_2_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_wlist_2_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_wlist_2_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                        <li>
                            <a  href="#">
                                <div class="sales_info_div">
                                    <span class="sales_title_t">客户名称:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['accountname'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">成交业务:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['productname'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">市场价:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['marketprice'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">成交金额:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['dealamount'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">是否全款:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['allamount'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">到款性质:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['paymentnature'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">收款:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['firstpayment'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">拜访次数:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['visitingordercount'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">行业:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['industry'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">拜访对象:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['visitingobj'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">到账业绩:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['arrivalamount'];?>

                                </div>
                            </a>
                        </li>
                        <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_2_saved_local_item;
}
}
if ($__foreach_wlist_2_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_2_saved_item;
}
?>
                    </ul>
                </div>
                <div class="selector" data-role="collapsible">
                    <h5 >次日拜访情况</h5>
                    <ul data-icon="false"  data-role="listview" data-inset="true">
                        <?php
$_from = $_smarty_tpl->tpl_vars['data']->value['nextdayvisit'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_wlist_3_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_wlist_3_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_wlist_3_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_wlist_3_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                        <li>
                            <a  href="#">
                                <div class="sales_info_div">
                                    <span class="sales_title_t">客户名称:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['arrivalamount'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">姓名:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['contacts'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">是否老板:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['title'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">第几次拜访:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['visitingordernum'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">有陪访:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['isvisitor'];?>

                                </div>
                                <div class="sales_info_div">
                                    <span class="sales_title_t">陪访者:</span><?php echo $_smarty_tpl->tpl_vars['value']->value['withvisitor'];?>

                                </div>
                            </a>
                        </li>
                        <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_3_saved_local_item;
}
}
if ($__foreach_wlist_3_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_3_saved_item;
}
?>
                    </ul>
                </div>
                <div class="selector" data-role="collapsible">
                    <h5 >批复信息</h5>
                    <ul data-icon="false"  data-role="listview" data-inset="true">
                        <?php if ($_smarty_tpl->tpl_vars['data']->value['approvalData'] != '') {?>
                            <?php
$_from = $_smarty_tpl->tpl_vars['data']->value['approvalData'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_v_4_saved_item = isset($_smarty_tpl->tpl_vars['v']) ? $_smarty_tpl->tpl_vars['v'] : false;
$_smarty_tpl->tpl_vars['v'] = new Smarty_Variable();
$__foreach_v_4_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_v_4_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['v']->value) {
$__foreach_v_4_saved_local_item = $_smarty_tpl->tpl_vars['v'];
?>
                            <li>
                                <a  href="#">
                                    <div class="sales_info_div">
                                        <span class="sales_title_t">批复　人:</span><?php echo $_smarty_tpl->tpl_vars['v']->value['create_last_name'];?>

                                    </div>
                                    <div class="sales_info_div">
                                        <span class="sales_title_t">批复时间:</span><?php echo $_smarty_tpl->tpl_vars['v']->value['createtime'];?>

                                    </div>
                                    <div class="sales_info_div">
                                        <span class="sales_title_t">批复内容:</span><?php echo $_smarty_tpl->tpl_vars['v']->value['description'];?>

                                    </div>
                                </a>
                            </li>
                            <?php
$_smarty_tpl->tpl_vars['v'] = $__foreach_v_4_saved_local_item;
}
}
if ($__foreach_v_4_saved_item) {
$_smarty_tpl->tpl_vars['v'] = $__foreach_v_4_saved_item;
}
?>
                        <?php }?>
                    </ul>
                </div>
        </div>
        
   </div>
</div>


<!-- <div data-role="page" id="pifu-page" data-url="daydeal-page">
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
</div> -->


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
    });


    

<?php echo '</script'; ?>
>

</body>
</html><?php }
}
