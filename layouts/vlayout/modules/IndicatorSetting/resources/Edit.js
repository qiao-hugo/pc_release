/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("IndicatorSetting_Edit_Js", {}, {
    registerEventaddfallinto: function () {
        $('body').on('click', '.btn-success', function (e) {
            var flag = true;
            $('.form-verify').each(function () {
                if (!$(this).val()) {
                    $(this).css('border', '1px solid red');
                    flag = false;
                }
            });

            var flag2 = true;
            $('.form-select-verify').each(function () {
                var select_val = $(this).val();
                if (select_val && select_val.length == 1) {
                    $(this).next().children('ul').css('border', '1px solid red');
                    flag2 = false;
                }
            });
            if (!flag2) {
                Vtiger_Helper_Js.showPnotify('或者关系至少选择两个选项');
                e.preventDefault();
            }

            if (flag) {
                $("#EditView").submit(function () {
                    params = {
                        text: '提交成功',
                        'type': 'success'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                });
            } else {
                Vtiger_Helper_Js.showPnotify('必填字段不能为空');
                e.preventDefault();
            }


        });

        $("input").focus(function () {
            $(this).css("border", "1px solid #ccc");
        });

        $("ul").click(function () {
            $(this).css("border", "1px solid #ccc");
        });

        $('body').on('click', '.addfallinto', function () {
            var key = $(this).attr('data-staff_key');
            key = "html_" + key;
            $(this).parent().parent().after(eval(key));
        });

        $('body').on('click', '.deletefallinto', function () {
            var id = $(this).parent().parent().children(':first').val();
            if (!id) {
                $(this).closest('tr').remove();
                return;
            }
            var key = $(this).attr('data-staff_key');
            var key = 'first_html_' + key;
            var current_node = $(this).closest('tr');
            var prev_node = $(this).parent().parent().prev();
            var prev_id = prev_node.children(':first').val();
            var next_node = current_node.next();
            var next_id = next_node.children(':first').val();
            Vtiger_Helper_Js.showConfirmationBox({'message': '确定删除该特殊条件设置吗?'}).then(function (data) {
                    params = {
                        'module': 'IndicatorSetting',
                        'action': 'DeleteAjax',
                        'record': id,
                        'mode': 'special_operation'
                    };
                    AppConnector.request(params).then(
                        function (data) {
                            if (data.success == true) {
                                current_node.remove();
                                if (!prev_id && !next_id) {
                                    prev_node.after(eval(key));
                                }
                            } else {
                                Vtiger_Helper_Js.showPnotify(data.error.message);
                            }
                        });
                },
                function (error, err) {
                }
            );
        });
    },

    registerEvents: function (container) {
        this._super(container);
        this.registerEventaddfallinto();

    }
});


