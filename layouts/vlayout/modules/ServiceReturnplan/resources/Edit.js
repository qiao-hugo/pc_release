Vtiger_Edit_Js("ServiceReturnplan_Edit_Js",{ },{

    countsequence:function(){
        $('input[name^="sequence"]').each(
            function(index,value){
                console.log(index);
                index+=1
                $(this).prev('span').text('第'+index+'次回访');
                $(this).val(index);
            }
        )
    },
    extra_option : function(){
        var thisInstance = this;
        var ckEditorInstance = new Vtiger_CkEditor_Js();
        var changeid = 1;
        $("#extra_body").on('click','.add_extra',function(){
            var aaaa='<tr> <td> <span class="redColor">*</span> <span class="label label-info">第12次回访</span> <input type="hidden" data-validation-engine="validate[required]" type="number" name="sequence[]" placeholder="排序"   style="width: 40px;"> </td> <td> <span class="redColor">*</span> <div class="input-append"> <input data-validation-engine="validate[required]" type="number" name="upperlimit[]" placeholder="上限"  style="width: 40px;"> <span class="add-on">天</span> </div> </td> <td> <span class="redColor">*</span> <div class="input-append"> <input data-validation-engine="validate[required]" type="number" name="lowerlimit[]" placeholder="下限"  style="width: 40px;"> <span class="add-on">天</span> </div> </td> <td > <textarea style="width:93%" id="uedit'+changeid+'" type="textarea" name="returnplantext[]" placeholder="备注"></textarea> </td> <td > <div> <button class="btn btn-small add_extra" type="button" > <i class=" icon-plus"></i></button><button class="btn btn-small del_extra" type="button" > <i class="icon-trash"></i></button> </div> </td> </tr>';
            $(this).closest('tr').after(aaaa);
            //ckEditorInstance.loadCkEditor("uedit"+changeid);
            changeid+=1;
            thisInstance.countsequence();
        });
        $("#extra_body").on('click','.del_extra',function(){
            if(confirm('确定删除此条记录吗？')){
                $(this).closest('tr').remove();
                thisInstance.countsequence();
            };
        });
    },
	registerBasicEvents : function(container) {
        this._super(container);
        this.extra_option();
	}
});