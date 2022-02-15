


Vtiger_List_Js("ClosingDate_List_Js",{
	
},{
    applicationUpdateAchievement:function () {
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click', '.applicationUpdateDate', function(e){
            var tr = $(this).closest('tr');
            var record=$(tr).data("id");
            var msg = {
                'message': '申请调整业绩核算截止日期',
                "width":"400px",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    if(!$("#date").val()){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '截止日期不能为空!'});
                        return false;
					}
                    if(!$("#remarks").val()){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '备注不能为空!'});
                        return false;
                    }
                	var params = {
                        'module': 'ClosingDate',
                        'action': 'ChangeAjax',
                        'mode': 'applicationUpdateDate',
                        'date':$("#date").val(),
                        'remarks':$("#remarks").val(),
                        'record':record
                    };
                    AppConnector.request(params).then(
                        function (data) {
                        	if(data.result.success==1){
                                window.location.reload();
							}else{
                                Vtiger_Helper_Js.showMessage({type: 'error', text:data.result.message});
							}
                        }
                    )
                }
            );
            $('.modal-body').append('<table style="margin-top: 25px;"><tr>\n' +
                '\t\t\t\t<td class="fieldLabel medium">\n' +
                '\t\t\t\t\t<label class="muted pull-right marginRight10px">截止日期（每月）:</label>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t\t<td class="fieldValue medium">\n' +
                '\t\t\t\t\t<div class="input-append row-fluid">\n' +
                '\t\t\t\t\t\t<div class="span10 row-fluid date form_datetime">\n' +
                '\t\t\t\t\t\t\t<select class="chzn-select"  id="date"><option value="1" >1号</option><option value="2" >2号</option><option value="3" >3号</option><option value="4" >4号</option><option value="5" >5号</option><option value="6" >6号</option><option value="7" >7号</option><option value="8" >8号</option><option value="9" >9号</option><option value="10" >10号</option><option value="11" >11号</option><option value="12" >12号</option><option value="13" >13号</option><option value="14" >14号</option><option value="15" >15号</option><option value="16" >16号</option><option value="17" >17号</option><option value="18" >18号</option><option value="19" >19号</option><option value="20" >20号</option><option value="21" >21号</option><option value="22" >22号</option><option value="23" >23号</option><option value="24" >24号</option><option value="25" >25号</option><option value="26" >26号</option><option value="27" >27号</option><option value="28" >28号</option></select>\n' +
                '\t\t\t\t\t\t</div>\n' +
                '\t\t\t\t\t</div>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t</tr>\n'+
                '<tr>\n' +
                '\t\t\t\t<td class="fieldLabel medium">\n' +
                '\t\t\t\t\t<label class="muted pull-right marginRight10px">备注:</label>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t\t<td class="fieldValue medium">\n' +
                '\t\t\t\t\t<textarea  id="remarks" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{&quot;mandatory&quot;:false,&quot;presence&quot;:true,&quot;quickcreate&quot;:false,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;text&quot;,&quot;name&quot;:&quot;remark&quot;,&quot;label&quot;:&quot;\u5907\u6ce8&amp;\u8bf4\u660e&quot;}"></textarea>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t</tr></table>');
		});


/*    业绩日期调整 html
*/




    },
    /*
	 * 列表数据行双击打开详细，按钮单击详细
	 */
    registerRowClickEvent: function(){
        var thisInstance = this;
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('dblclick','.listViewEntries',function(e){
            var elem = jQuery(e.currentTarget);
            var recordUrl = elem.data('recordurl');
            if(typeof recordUrl == 'undefined') {
                return;
            }
            window.open(recordUrl,'_blank');
        });
        listViewContentDiv.on('click','.icon-th-list',function(e){
            var elem = jQuery(e.currentTarget);
            var recordUrl = elem.parent().parent().parent().data('recordurl');
            if(typeof recordUrl == 'undefined') {
                return;
            }
            window.open(recordUrl,'_blank');
        });
    },
	registerEvents : function(){
		this._super();
		this.applicationUpdateAchievement();
		this.registerRowClickEvent();
	}


});
function num(obj){
    obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
    obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字
    obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个, 清除多余的
    obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
    obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
}