<?php
/* Smarty version 3.1.28-dev/79, created on 2018-01-26 20:20:29
  from "/data/httpd/vtigerCRM/apps/views/VisitingOrder/detail.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a6b1d0ddf0650_73288581',
  'file_dependency' => 
  array (
    'ed6ca7056c938968dfbc94d4c9c70a549144093e' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/VisitingOrder/detail.html',
      1 => 1516969159,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a6b1d0ddf0650_73288581 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</head>
<body>
<div class="container-fluid w fix visit-list" style="padding-bottom:52px;">
    <div class="row">
        
        <div class="cont-box">
            <input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['record_id'];?>
"/>
            <input type="hidden" name="stagerecordid" value="<?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['Workflows']['STAGERECORDID'];?>
"/>
            <input type="hidden" name="checkname" value="<?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['Workflows']['STAGERECORDNAME'];?>
"/>
            <ul class="list">
                <li>客户：<span><?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['accountnamer'];?>
</span></li>
                <li class="fix"><div class="fl">联系人：<span><?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['contacts'];?>
</span></div><div class="fr" style="max-width: 200px;overflow:hidden;white-space: nowrap;">拜访目地：<span><?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['purpose'];?>
</span></div></li>
                <li class="fix"><div class="fl">外出类型：<span><?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['outobjective'];?>
</span></div><div class="fr">提单人：<span><?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['usersname'];?>
</span></div></li>
               <!-- <li class="fix"><div class="fl">签到图片:<span data-toggle="modal" data-target="#myModal1"><?php if ($_smarty_tpl->tpl_vars['DETAIL']->value['pictures']) {
echo $_smarty_tpl->tpl_vars['DETAIL']->value['pictures']['fieldname'];
}?></span></div></li>-->
                <li class="fix"><div class="f1">陪同人：<span><?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['accompanyuser'];?>
</span></div></li>
                <li class="fix"><div class="f1">客户地址：<span><?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['customeraddress'];?>
</span></div></li>
                <!-- <li class="fix"><div class="f1">提单人签到地址：<span><?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['signaddress'];?>
</span></div></li> -->
                <?php if ($_smarty_tpl->tpl_vars['ISSIGN']->value == 1) {?>
                    <?php
$_from = $_smarty_tpl->tpl_vars['DETAIL']->value['t_accompany'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_v_0_saved_item = isset($_smarty_tpl->tpl_vars['v']) ? $_smarty_tpl->tpl_vars['v'] : false;
$_smarty_tpl->tpl_vars['v'] = new Smarty_Variable();
$__foreach_v_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_v_0_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['v']->value) {
$__foreach_v_0_saved_local_item = $_smarty_tpl->tpl_vars['v'];
?>

                        <?php if ($_smarty_tpl->tpl_vars['v']->value['visitsigntype'] == '提单人') {?>
                            <li class="fix"><div class="f1">提单人签到地址<?php echo $_smarty_tpl->tpl_vars['v']->value['signnum'];?>
：<span><?php echo $_smarty_tpl->tpl_vars['v']->value['signaddress'];?>
</span></div></li>
                        <?php } else { ?>
                            <li class="fix"><div class="f1"><?php echo $_smarty_tpl->tpl_vars['v']->value['last_name'];?>
签到地址<?php echo $_smarty_tpl->tpl_vars['v']->value['signnum'];?>
：<span><?php echo $_smarty_tpl->tpl_vars['v']->value['signaddress'];?>
</span></div></li>
                        <?php }?>

                        
                    <?php
$_smarty_tpl->tpl_vars['v'] = $__foreach_v_0_saved_local_item;
}
}
if ($__foreach_v_0_saved_item) {
$_smarty_tpl->tpl_vars['v'] = $__foreach_v_0_saved_item;
}
?>
                <?php }?>
                <li>状态：<span><?php echo $_smarty_tpl->tpl_vars['MODULESTATUS']->value[$_smarty_tpl->tpl_vars['DETAIL']->value['modulestatus']];?>
</span></li>
                <li>开始日期：<span><?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['startdate'];?>
</span></li>
                <li>结束日期：<span><?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['enddate'];?>
</span></li>
            </ul>
            <div class="remarks">备注：</div>
            <ul class="remarks-box" style="min-height: 60px;">
                <li><?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['remark'];?>
</li>
            </ul>
            <?php if ($_smarty_tpl->tpl_vars['ISREVOKE']->value) {?>
            <div class="confirm tc">
                <button class="btn btn1" id="btn4">撤销</button>
            </div>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['DETAIL']->value['Workflows']['ISROLE'] && $_smarty_tpl->tpl_vars['DETAIL']->value['Workflows']['STAGERECORDID']) {?>
            <div class="confirm tc">
                <button class="btn btn1" id="btn1" data-toggle="modal" data-target="#myModal">同意<?php if ($_smarty_tpl->tpl_vars['DETAIL']->value['modulestatus'] == 'c_canceling') {?>撤销<?php }?></button>
                <button class="btn btn2" id="btn2" data-toggle="modal" data-target="#myModal">拒绝<?php if ($_smarty_tpl->tpl_vars['DETAIL']->value['modulestatus'] == 'c_canceling') {?>撤销<?php }?></button>
            </div>
            <?php }?>
        </div>
        
        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    </div>
</div>
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="confirm tc">
                    <button class="btn btn1" id="btn3" aria-label="Close" data-dismiss="modal" aria-hidden="true">确定</button>
                    <button class="btn btn2" aria-label="Close" data-dismiss="modal" aria-hidden="true">取消</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="flag" data-id="1"></div>

<div class="modal fade" id="myModal1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="confirm tc">
                    <img width="100%" src='<?php echo $_smarty_tpl->tpl_vars['DETAIL']->value["pictures"]["base64_image_content"];?>
'>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<?php echo '<script'; ?>
>
    $(function () {
        _scale = true
        if (_scale) {
            var width = window.screen.width || 640;
            var scale = width / 640;
            document.querySelector("meta[name=viewport]").setAttribute("content", "target-densitydpi=device-dpi,width=640,user-scalable='no',initial-scale=" + scale);
        }
    })
<?php echo '</script'; ?>
>-->

<?php echo '<script'; ?>
>
    $(function(){
        var id='';
        $("#btn1,#btn2").on('click',function(){
            id=$(this).attr('id');
            if(id=='btn1'){
                $(".modal-title").html('确定要审核吗');
            }else{
                $(".modal-title").html('确定打回吗');
            }
        });

        $("#btn3").on('click',function(){
            var dataid=$('#flag').data('id');
            if(dataid==1){
                doit(id);
            }
        });
        function doit(id){
            $('#flag').attr('data-id',2);
            $('#btn1,#btn2').remove();
            if(id=='btn1'){
                var params={
                
                "record":<?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['record_id'];?>
,
                "stagerecordid":"<?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['Workflows']['STAGERECORDID'];?>
",
                        "stagerekey":"stagerecordid",
                        "mode":"updateSalseorderWorkflowStages",
                        "isbackname":"<?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['Workflows']['STAGERECORDNAME'];?>
",
                        "checkname":"checkname",
                        "customer":0,
                        "customername":''
                
                };
            }else{
                var params={
                    
                    "record":<?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['record_id'];?>
,
                    "stagerecordid":"<?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['Workflows']['STAGERECORDID'];?>
",
                            "stagerekey":"isrejectid",
                            "mode":"backall",
                            "isbackname":"<?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['Workflows']['STAGERECORDNAME'];?>
",
                            "checkname":"isbackname",
                            "reject":"移动端打回",
                            "actionnode":0
                    
                };
            }

            $.ajax({
                url: "index.php?module=VisitingOrder&action=doWorkflowStages",    //请求的url地址
                dataType: "json",   //返回格式为json
                data: params,    //参数值
                type: "POST",   //请求方式
                beforeSend: function() {
                    //请求前的处理
                },
                success: function(req) {
                    window.location.reload();
                },
                complete: function() {
                    window.location.reload();
                    //请求完成的处理
                },
                error: function() {
                    window.location.reload();
                    //请求出错处理
                }
            });
        }
        $("#btn4").on('click',function(){
            Tips.confirm({
                content: '确定要撤销该拜访单吗?',
                define: '确定',
                cancel: '取消',
                before: function(){
                },
                after: function(b){
                    if(b){
                        $.ajax({
                            url: "index.php?module=VisitingOrder&action=doRevoke",    //请求的url地址
                            dataType: "json",   //返回格式为json
                            data: {"record":<?php echo $_smarty_tpl->tpl_vars['DETAIL']->value['record_id'];?>
},    //参数值
                            type: "POST",   //请求方式
                            beforeSend: function() {
                                //请求前的处理
                            },
                            success: function(req) {
                                window.location.reload();
                            },
                            complete: function() {
                                window.location.reload();
                                //请求完成的处理
                            },
                            error: function() {
                                window.location.reload();
                                //请求出错处理
                            }
                        });
                    }else{

                    }
                }
            });
        });
    });

<?php echo '</script'; ?>
>

</body>
</html>
<?php }
}
