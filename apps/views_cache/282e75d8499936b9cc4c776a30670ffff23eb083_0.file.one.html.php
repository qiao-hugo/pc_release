<?php
/* Smarty version 3.1.28-dev/79, created on 2018-01-10 18:09:59
  from "/data/httpd/vtigerCRM/apps/views/SupplierContracts/one.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a55e677cd2928_54030845',
  'file_dependency' => 
  array (
    '282e75d8499936b9cc4c776a30670ffff23eb083' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/SupplierContracts/one.html',
      1 => 1515578963,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a55e677cd2928_54030845 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>服务合同详情</title>
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
 src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"><?php echo '</script'; ?>
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
        <h1>采购合同详情</h1>
        <a href="#demo-intro" data-rel="back" data-transition="slide" class="back-btn ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-back ui-btn-left ui-btn-icon-left">返回</a>
        </a>
        <?php if ($_smarty_tpl->tpl_vars['ISROLE']->value == '1') {?>
        <a href="#myPopupDialog" data-rel="popup"  id="gotoMyPopupDialog" data-position-to="window" data-transition="fade" class="ui-btn ui-corner-all ui-shadow ui-btn-inline">审核</a>
        <?php }?>
    </div>

    <div data-role="main" class="ui-content">
        <div id="data_list"  data-role="collapsible-set">

                <div  class="selector" data-role="collapsible"  data-collapsed="false">
                    <h3>基本信息</h3>
                    <div class="sales_info_div">
                        <span class="sales_title_t">采购合同编号:</span>
                        <?php echo $_smarty_tpl->tpl_vars['SupplierContracts']->value['vtiger_suppliercontractscontract_no'];?>

                    </div>
                 
                    <div class="sales_info_div">
                        <span class="sales_title_t">合同状态:</span>
                        <?php echo $_smarty_tpl->tpl_vars['modulestatus']->value[$_smarty_tpl->tpl_vars['SupplierContracts']->value["vtiger_suppliercontractsmodulestatus"]];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">供应商:</span>
                        <?php echo $_smarty_tpl->tpl_vars['SupplierContracts']->value['vtiger_suppliercontractsvendorid'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">合同主体:</span>
                        <?php echo $_smarty_tpl->tpl_vars['SupplierContracts']->value['vtiger_suppliercontractsinvoicecompany'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">合同类型:</span>
                        <?php echo $_smarty_tpl->tpl_vars['CONTRACTSSTATUS']->value[$_smarty_tpl->tpl_vars['SupplierContracts']->value["vtiger_suppliercontractssuppliercontractsstatus"]];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">合同领取人:</span>
                        <?php echo $_smarty_tpl->tpl_vars['SupplierContracts']->value['smowner_owner'];?>

                    </div>


                    <div class="sales_info_div">
                        <span class="sales_title_t">合同领取时间:</span>
                        <?php echo $_smarty_tpl->tpl_vars['SupplierContracts']->value['vtiger_suppliercontractsreceivedate'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">签订人:</span>
                        <?php echo $_smarty_tpl->tpl_vars['SupplierContracts']->value['vtiger_suppliercontractssignid'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">签订时间:</span>
                        <?php echo $_smarty_tpl->tpl_vars['SupplierContracts']->value['vtiger_suppliercontractssigndate'];?>

                    </div>


                    <div class="sales_info_div">
                        <span class="sales_title_t">归还日期:</span>
                        <?php echo $_smarty_tpl->tpl_vars['SupplierContracts']->value['vtiger_suppliercontractsreturndate'];?>

                    </div>

                    <div class="sales_info_div">
                        <span class="sales_title_t">有效期限:</span>
                        <?php echo $_smarty_tpl->tpl_vars['SupplierContracts']->value['vtiger_suppliercontractseffectivetime'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t">备注:</span>
                        <?php echo $_smarty_tpl->tpl_vars['SupplierContracts']->value['vtiger_suppliercontractsremark'];?>

                    </div>
                    <div class="sales_info_div">
                        <span class="sales_title_t"></span>
                        <?php if (!empty($_smarty_tpl->tpl_vars['attr']->value)) {?>
                        <?php
$_from = $_smarty_tpl->tpl_vars['attr']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_expfilename_0_saved_item = isset($_smarty_tpl->tpl_vars['expfilename']) ? $_smarty_tpl->tpl_vars['expfilename'] : false;
$_smarty_tpl->tpl_vars['expfilename'] = new Smarty_Variable();
$__foreach_expfilename_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_expfilename_0_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['expfilename']->value) {
$__foreach_expfilename_0_saved_local_item = $_smarty_tpl->tpl_vars['expfilename'];
?>
                        <a href="index.php?module=SupplierContracts&action=download&filename=<?php echo urlencode(base64_encode($_smarty_tpl->tpl_vars['expfilename']->value['attachmentsid']));?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['expfilename']->value['name'];?>
</a><br>
                        <?php
$_smarty_tpl->tpl_vars['expfilename'] = $__foreach_expfilename_0_saved_local_item;
}
}
if ($__foreach_expfilename_0_saved_item) {
$_smarty_tpl->tpl_vars['expfilename'] = $__foreach_expfilename_0_saved_item;
}
?>
                        <?php }?>
                    </div>
                   
                </div>
            <div  class="selector" data-role="collapsible"  data-collapsed="false">
                <h3>工作流审核-<?php echo $_smarty_tpl->tpl_vars['STAGERECORDNAME']->value;?>
</h3>


                <div style="font-weight:bold;">审核节点：</div>
                <ul id="data_lists" data-role="listview" data-inset="true">
                    <?php
$_from = $_smarty_tpl->tpl_vars['WORKFLOWSSTAGELIST']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_1_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_1_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_1_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_1_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                    <li>
                        <a data-role="button" style="font-size:12px; font-weight:normal; " <?php if ($_smarty_tpl->tpl_vars['value']->value['isaction'] == '1') {?>class="workflowstages_isaction"<?php }?> data-id="<?php echo $_smarty_tpl->tpl_vars['value']->value['salesorderworkflowstagesid'];?>
" data-transition="slide"  href="#"><?php echo $_smarty_tpl->tpl_vars['value']->value['workflowstagesname'];?>
【<?php echo $_smarty_tpl->tpl_vars['value']->value['actionstatus'];?>
】<?php if ($_smarty_tpl->tpl_vars['value']->value['auditorid'] != '--') {?><br />审核人:<?php echo $_smarty_tpl->tpl_vars['value']->value['auditorid'];?>
<br />审核时间:<?php echo $_smarty_tpl->tpl_vars['value']->value['auditortime'];
}?></a>
                    </li>
                    <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_1_saved_local_item;
}
}
if ($__foreach_value_1_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_1_saved_item;
}
?>
                </ul>

                <?php if (!empty($_smarty_tpl->tpl_vars['SALESORDERHISTORY']->value)) {?>
                <div style="font-weight:bold; padding-top: 15px;">历史打回原因：</div>
                <ul id="" data-role="listview" data-inset="true">
                    <?php
$_from = $_smarty_tpl->tpl_vars['SALESORDERHISTORY']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_2_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_2_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_2_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_2_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                    <li><a data-role="button" href="#" style="font-size:12px; font-weight:normal; "><?php echo $_smarty_tpl->tpl_vars['value']->value['reject'];?>
【<?php echo $_smarty_tpl->tpl_vars['value']->value['last_name'];?>
】</a></li>
                    <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_2_saved_local_item;
}
}
if ($__foreach_value_2_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_2_saved_item;
}
?>
                </ul>
                <?php }?>
                <?php if (!empty($_smarty_tpl->tpl_vars['REMARKLIST']->value)) {?>
                <div style="font-weight:bold; padding-top: 15px;">备注：</div>
                <ul id="remarkslist" data-role="listview" data-split-icon="gear" data-split-theme="a" data-inset="true">

                    <?php
$_from = $_smarty_tpl->tpl_vars['REMARKLIST']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_3_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_3_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_3_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_3_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                    <?php $_smarty_tpl->tpl_vars['IMGMD'] = new Smarty_Variable(md5($_smarty_tpl->tpl_vars['value']->value['email1']), null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'IMGMD', 0);?>
                    <li class="ui-field-contain"><a data-role="button" href="#"><img src="<?php if (isset($_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value])) {
echo $_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value];
} else { ?>../../static/img/trueland.png<?php }?>">
                        <h2><?php echo $_smarty_tpl->tpl_vars['value']->value['reject'];?>
</h2><p><?php echo $_smarty_tpl->tpl_vars['value']->value['last_name'];?>
  <?php echo $_smarty_tpl->tpl_vars['value']->value['rejecttime'];?>
</p></a></li>
                    <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_3_saved_local_item;
}
}
if ($__foreach_value_3_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_3_saved_item;
}
?>
                </ul>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['ISROLE']->value == '1') {?>
                <form method="post" onsubmit='return t_submit()'>
                    <div class="ui-field-contain">
                        <input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['record']->value;?>
">
                        <input type="hidden" name="stagerecordid" value="<?php echo $_smarty_tpl->tpl_vars['STAGERECORDID']->value;?>
">
                        <input type="hidden" name="stagerecordname" value="<?php echo $_smarty_tpl->tpl_vars['STAGERECORDNAME']->value;?>
">
                        <textarea placeholder="输入打回原因" name="repulseinfo" id="repulseinfo" rows="5" class="form-control"
                                  data-content=""></textarea>
                        <div class="confirm tc">
                            <button class="ui-btn ui-btn-b ui-shadow ui-corner-all">打回</button>
                        </div>
                    </div>
                </form>
                <div class="ui-field-contain">

                        <textarea placeholder="输入备注信息" name="remarks" id="remarks" rows="5"
                                  data-content=""></textarea>
                    <div class="confirm tc">
                        <button class="ui-btn ui-btn-c ui-shadow ui-corner-all addremarks">添加备注</button>
                    </div>
                </div>
                <?php }?>
            </div>

            <?php if ($_smarty_tpl->tpl_vars['userid']->value == 1 || $_smarty_tpl->tpl_vars['userid']->value == 2110 || $_smarty_tpl->tpl_vars['userid']->value == 6227) {?>
            <div class="confirm tc">
                <select class="select"  data-native-menu="false" id="stylesd">
                    <option value="files_style1">代付款证明</option>
                    <option value="files_style2">验收单</option>
                    <option value="files_style3">分成单</option>
                    <option value="files_style4">合同</option>
                    <option value="files_style5">其他附件</option>
                </select>
                <button class="ui-btn ui-btn-b ui-shadow ui-corner-all" id="chooseImage">拍照上传</button>
            </div>
            <?php }?>

                


        </div>
        
   </div>
    <div data-role="popup" data-dismissible="false" id="refillApplication_examine_page_popup" >
        <div>正在提交...</div>
    </div>
    <div data-role="popup" data-dismissible="false" id="refillApplication_remarks_page_popup" >
        <div>备注信息不能为空</div>
    </div>

     <div data-role="popup" id="myPopupDialog">
      <div data-role="header">
        <h1>提醒</h1>
      </div>
      <div data-role="main" class="ui-content" style="text-align: right;">
        <a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn ui-icon-delete ui-btn-icon-notext ui-btn-right">关闭</a>
        <p>确定要审核当前的节点<?php echo $_smarty_tpl->tpl_vars['STAGERECORDNAME']->value;?>
</p>
        <a href="javascript:void(0)" id="refillApplication_examine" class="ui-btn ui-btn-inline ui-mini ui-icon-action ui-btn-icon-left" >确定</a>
      </div>
    </div> 

    
    <div id="bg"></div> 
    <?php echo '<script'; ?>
 type="text/javascript">
        $(document).bind("mobileinit", function() {
            //disable ajax nav
            $.mobile.ajaxEnabled=false
        });
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
        jsApiList: [
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage'
        ]
    });
    
    wx.ready(function () {

        // 5 图片接口
        // 5.1 拍照、本地选图
        var images = {
            localId: [],
            serverId: []
        };
        document.querySelector('#chooseImage').onclick = function () {
            wx.chooseImage({
                count: 3, // 默认9
                sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    images.localId = res.localIds;
                    var i = 0, length = images.localId.length;
                    images.serverId = [];
                    function upload() {
                        wx.uploadImage({
                            localId: images.localId[i],
                            isShowProgressTips: 1,
                            success: function (res) {
                                i++;
                                //alert('已上传：' + i + '/' + length);
                                images.serverId.push(res.serverId);
                                var params={
                                
                                "record":<?php echo $_smarty_tpl->tpl_vars['record']->value;?>
,
                                "userid":<?php echo $_smarty_tpl->tpl_vars['userid']->value;?>
,
                                "userid":<?php echo $_smarty_tpl->tpl_vars['userid']->value;?>
,
                                "style":$('#stylesd').val(),
                                "pictureid":res.serverId
                                    
                                };
                                $.ajax({
                                    url: "index.php?module=SupplierContracts&action=photograph",    //请求的url地址
                                    dataType: "json",   //返回格式为json
                                    data: params,    //参数值
                                    type: "POST",   //请求方式
                                    beforeSend: function() {
                                        //请求前的处理
                                    },
                                    success: function(req) {
                                    },
                                    complete: function() {
                                        //请求完成的处理
                                    },
                                    error: function() {
                                        //请求出错处理
                                    }
                                });
                                if (i < length) {
                                    upload();
                                }
                            },
                            fail: function (res) {
                                alert(JSON.stringify(res));
                            }
                        });
                    }
                    upload();
                    alert('已选择 ' + res.localIds+' 张图片');
                }
            });
        };

    });
    $(function(){
        
        // 审核
        $('#refillApplication_examine').click(function () {
            var stagerecordid = $('input[name=stagerecordid]').val();
            var record = $('input[name=record]').val();
            $('#myPopupDialog').popup('close');
            $.ajax({ 
                url: "index.php?module=SupplierContracts&action=examine",
                data: {
                    stagerecordid : stagerecordid,
                    record : record
                },
                type:'POST',
                beforeSend:function() {
                    mark('#refillApplication_examine_page_popup', 'show');
                },
                success: function(data){
                    alert('审核成功');
                    mark('#refillApplication_examine_page_popup', 'none');

                    setTimeout(function() {
                        window.location.reload(); 
                    }, 100);
                }
            });

        });
        //添加备注
        $('.addremarks').on('click',function(){
            var stagerecordid = $('input[name=stagerecordid]').val();
            var record = $('input[name=record]').val();
            var remarks=$('#remarks').val();
            if(remarks==''){
                mark('#refillApplication_remarks_page_popup', 'show');
                setTimeout("mark('#refillApplication_remarks_page_popup', 'none')",2000);
                return false;
            }
            $.ajax({
                url: "index.php?module=SupplierContracts&action=submitremark",
                data: {
                    stagerecordid : stagerecordid,
                    record : record,
                    reject : remarks
                },
                type:'POST',
                beforeSend:function() {
                    mark('#refillApplication_examine_page_popup', 'show');
                },
                success: function(data){
                    data = $.parseJSON( data );
                    mark('#refillApplication_examine_page_popup', 'none');
                    if (data.success) {
                        alert('备注添加成功');
                        setTimeout(function() {
                            window.location.reload();
                        }, 100);
                    }
                }
            });
        });
    });


    

    <?php echo '</script'; ?>
>
</div>

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

    // 打回
    function t_submit() {
        var repulseinfo = $.trim($('#repulseinfo').val());
        if (repulseinfo) {
            var stagerecordid = $('input[name=stagerecordid]').val();
            var record = $('input[name=record]').val();
            var isbackname = $('input[name=stagerecordname]').val();
            $.ajax({ 
                url: "index.php?module=SupplierContracts&action=repulse",
                data: {
                    stagerecordid : stagerecordid,
                    record : record,
                    repulseinfo : repulseinfo,
                    isbackname: isbackname
                },
                type:'POST',
                beforeSend:function() {
                    mark('#refillApplication_examine_page_popup', 'show');
                },
                success: function(data){
                    data = $.parseJSON( data );
                    mark('#refillApplication_examine_page_popup', 'none');
                    if (data.success) {
                        alert('打回成功');
                        setTimeout(function() {
                        window.location.reload(); 
                        }, 100);
                    }
                }
            });
        }
        return false;
    }
<?php echo '</script'; ?>
>

</body>
</html><?php }
}
