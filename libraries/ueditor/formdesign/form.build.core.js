(function(){requiredcontent='<span class="redColor">* </span>';var LPB=window.LPB=window.LPB||{plugins:[],genSource:function(){var $temptxt=$("<div>").html($("#build").html());$($temptxt).find(".component").attr({"title":null,"data-original-title":null,"data-type":null,"data-content":null,"rel":null,"trigger":null,"style":null});$($temptxt).find(".valtype").attr("data-valtype",null).removeClass("valtype");$($temptxt).find(".component").removeClass("component");$($temptxt).find("form").attr({"id":null,"style":null});$("#source").val($temptxt.html().replace(/\n\ \ \ \ \ \ \ \ \ \ \ \ /g,"\n"));}};LPB.plugins['form_name']=function(active_component,leipiplugins){var plugins='form_name',popover=$(".popover");$(popover).find("#orgvalue").val($(leipiplugins).val());$(popover).delegate(".btn-warning","click",function(e){e.preventDefault();active_component.popover("hide");});$(popover).delegate(".btn-info","click",function(e){e.preventDefault();var inputs=$(popover).find("input");$.each(inputs,function(i,e){var attr_name=$(e).attr("id");var attr_val=$("#"+attr_name).val();if(attr_name=='orgvalue'){if(attr_val==''){alert('表单名称不能为空');return;}
$(leipiplugins).attr("value",attr_val);active_component.find(".leipiplugins-orgvalue").text(attr_val);}
active_component.popover("hide");});});}})();$(document).ready(function(){$(".component").click(function(md){$(".popover").remove();md.preventDefault();var $temp;var $this=$(this);var type;if($this.parent().parent().parent().parent().attr("id")==="components"){type="main";}else{type="form";}
if(type==="main"){$temp=$("<div class='form-horizontal span6' id='temp'></div>").append($this.clone());}else{return;if($this.attr("id")!=="legend"){$("#target .component").popover({trigger:"manual"});return;}}
var $target=$("#target");$("#target fieldset").append($temp.append("\n\n\ \ \ \ ").html());$("#target .component").popover({trigger:"manual"});$temp.remove();});$("#target .component").popover({trigger:"manual"});$("#target").delegate(".component","click",function(e){e.preventDefault();$(".popover").hide();var active_component=$(this);active_component.popover("show");var inputinfo=active_component.find(".inputinfo").val();if(inputinfo!=''){var fieldinfo=eval("("+inputinfo+")");var inputs=$(".popover").find(".input");$.each(inputs,function(i,e){var attr_name=$(e).attr("id");if(attr_name=='isrequired'){if(fieldinfo[attr_name]=='true'){$(e).attr('checked',true);}}else{$(e).val(fieldinfo[attr_name]);}});}
var leipiplugins=active_component.find(".leipiplugins"),plugins=$(leipiplugins).attr("leipiplugins");$(".popover").delegate(".btn-warning","click",function(e){active_component.popover("hide");});$(".popover").delegate(".btn-danger","click",function(e){active_component.popover("hide").remove();});$(".popover").delegate("#isrequired","click",function(e){if($(this).is(':checked')){$(this).val('true')}else{$(this).val('false')}});if(typeof(LPB.plugins[plugins])=='function'){try{LPB.plugins[plugins](active_component,leipiplugins);}catch(e){alert('控件异常！');}}else{alert("控件有误或不存在！");}});});function checkRequired(popover){if(popover.find("#title").val()==''||popover.find("#name").val()==''){alert('控件名和表单名必填!');return false;};return true;}
LPB.plugins['text']=function(active_component,leipiplugins){var popover=$(".popover");$(popover).delegate(".btn-info","click",function(e){if(!checkRequired($(popover))){return;}
var inputs=$(popover).find(".input");var stringify=new Array();$.each(inputs,function(i,e){var attr_name=$(e).attr("id");var attr_val=$(e).val();stringify.push('"'+attr_name+'":"'+attr_val+'"');});retrivew(active_component,stringify);});}
LPB.plugins['textarea']=function(active_component,leipiplugins){var popover=$(".popover");$(popover).delegate(".btn-info","click",function(e){if(!checkRequired($(popover))){return;}
var inputs=$(popover).find("input");var stringify=new Array();$.each(inputs,function(i,e){var attr_name=$(e).attr("id");var attr_val=$(e).val();stringify.push('"'+attr_name+'":"'+attr_val+'"');});retrivew(active_component,stringify);});}
LPB.plugins['select']=function(active_component,leipiplugins){var popover=$(".popover");var val=$.map($(leipiplugins).find("option"),function(e,i){return $(e).text()});val=val.join("\r");$(popover).find("#value").text(val);$(popover).delegate(".btn-info","click",function(e){if(!checkRequired($(popover))){return;}
var inputs=$(popover).find(".input");if($(popover).find("textarea").length>0){var text=$(popover).find("textarea")[0];if($.trim($(text).val())==''){alert('选项不能为空！');return;}
inputs.push($(popover).find("textarea")[0]);}
var stringify=new Array();$.each(inputs,function(i,e){var attr_name=$(e).attr("id");var attr_val=$(e).val();if(attr_name=='value'){var options=attr_val.split("\n");$(leipiplugins).html("");$.each(options,function(i,e){$(leipiplugins).append("\n      ");options[i]=$.trim(e);$(leipiplugins).append($("<option>").text(options[i]));});attr_val=options.join('##');}
stringify.push('"'+attr_name+'":"'+attr_val+'"');});if($('#type').val()=='multiple'){$(leipiplugins).attr('multiple','multiple');}else{$(leipiplugins).removeAttr('multiple');}
retrivew(active_component,stringify);});}
LPB.plugins['checkbox']=function(active_component,leipiplugins){var popover=$(".popover");val=$.map($(leipiplugins),function(e,i){return $(e).val().trim()});val=val.join("\r");$(popover).find("#value").text(val);$(popover).delegate(".btn-info","click",function(e){if(!checkRequired($(popover))){return;}
var inputs=$(popover).find(".input");if($(popover).find("textarea").length>0){var text=$(popover).find("textarea")[0];if($.trim($(text).val())==''){alert('选项不能为空！');return;}
inputs.push($(popover).find("textarea")[0]);}
var stringify=new Array();var html=new Array();$.each(inputs,function(i,e){var attr_name=$(e).attr("id");var attr_val=$(e).val();if(attr_name=='value'){var checkboxes=attr_val.split("\n");$.each(checkboxes,function(i,e){checkboxes[i]=e=$.trim(e);if(e.length>0){html.push('<label class="checkbox inline"><input type="checkbox" class="leipiplugins" value="'+e+'" leipiplugins="checkbox" >'+e+'</label>');}});attr_val=checkboxes.join('##');}
stringify.push('"'+attr_name+'":"'+attr_val+'"');});var content='';if($('#type').val()=='checkbox-inline'){content=html.join('');}else{content=html.join('<br>');}
retrivew(active_component,stringify);$(active_component).find(".leipiplugins-orgvalue").empty().html(content);$("#source").val($('fieldset').html().replace(/\n\ \ \ \ \ \ \ \ \ \ \ \ /g,"\n"));});}
LPB.plugins['radio']=function(active_component,leipiplugins){var popover=$(".popover");val=$.map($(leipiplugins),function(e,i){return $(e).val().trim()});val=val.join("\r");$(popover).find("#value").text(val);$(popover).delegate(".btn-info","click",function(e){if(!checkRequired($(popover))){return;}
var inputs=$(popover).find(".input");if($(popover).find("textarea").length>0){var text=$(popover).find("textarea")[0];if($.trim($(text).val())==''){alert('选项不能为空！');return;}
inputs.push($(popover).find("textarea")[0]);}
var stringify=new Array(),html=new Array();;$.each(inputs,function(i,e){var attr_name=$(e).attr("id");var attr_val=$(e).val();if(attr_name=='value'){var checkboxes=attr_val.split("\n");$.each(checkboxes,function(i,e){checkboxes[i]=e=$.trim(e);if(e.length>0){html.push('<label class="radio inline"><input type="radio"  value="'+e+'" class="leipiplugins" leipiplugins="radio" >'+e+'\n</label>');}});attr_val=checkboxes.join('##');}
stringify.push('"'+attr_name+'":"'+attr_val+'"');});var content='';if($('#type').val()=='radio-inline'){content=html.join('');}else{content=html.join('<br>');}
retrivew(active_component,stringify);$(active_component).find(".leipiplugins-orgvalue").empty().html(content);$("#source").val($('fieldset').html().replace(/\n\ \ \ \ \ \ \ \ \ \ \ \ /g,"\n"));});}
function retrivew(active_component,stringify){var labeltitle=$('#title.input').val();if($('#isrequired').is(':checked')){labeltitle=requiredcontent+labeltitle;}
active_component.find(".leipiplugins-orgname").html(labeltitle);active_component.find(".leipiplugins").val($('#value.input').val());active_component.popover("hide");var string='{'+stringify.join(',')+'}';active_component.find(".inputinfo").val(string);$("#source").val($('fieldset').html().replace(/\n\ \ \ \ \ \ \ \ \ \ \ \ /g,"\n"));}