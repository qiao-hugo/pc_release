
{strip}
		{if $is_debug==1}
		<div id="crm_page_trace" style="display:none;position: fixed;bottom:0;right:0;font-size:14px;width:100%;z-index: 999999;color: #000;
		text-align:left;font-family:'微软雅黑';background-color:#fff;">
<div id="close_page" style="background-color:#7EC0EE;margin:3px 0;"><i class="icon-remove" style="cursor:pointer"></i></div>
<ul id="myTab" class="nav nav-tabs">
              <li class="active"><a href="#trace_home" data-toggle="tab">页面加载</a></li>
              <li><a href="#trace_data" data-toggle="tab"> 数据调用</a></li>
             <li><a href="#trace_debug" data-toggle="tab">  调试数据</a></li>
             <li><a href="#trace_files" data-toggle="tab"> 文件加载</a></li>
             <li><a href="#trace_class" data-toggle="tab"> 类加载</a></li>
             <li><a href="#trace_log" data-toggle="tab"> 日志</a></li>
			 <li><a href="#trace_error" data-toggle="tab"> 系统错误</a></li>
</ul>
<div id="myTabContent" class="tab-content" style="margin:5px">
<div id="trace_home" class="tab-pane fade in active">
                  <div style="height:300px;overflow:auto"> 
                    <pre>
                     <p>页面加载时间：{$page_load_info['a']}</p>
                     <p>内存占用：{$page_load_info['b']}</p>
                     </pre></div>
           
</div>
<div class="tab-pane fade" id="trace_data">
<div style="height:300px;overflow:auto"><pre>
                     {foreach key=k item=cssModel from=$sql_trace_all_array}
                     <p>{$k+1}.   
                    
                     {$cssModel['sql']|defined_str_replace:$cssModel['sql_param']}   <p style="color:#3a87ad"><em>
                    
                     执行时间{$cssModel['exectime']} 当前内存{$cssModel['mem']}  调用文件{'C:\Program Files\vtigercrm600\apache\htdocs\vtigerCRM'|str_replace:'':$cssModel['lines'][1]['file']} 函数 {$cssModel['lines'][1]['func']}行 {$cssModel['lines'][1]['line']}
                     
                     </em></p></p>
                     
                     {/foreach}</pre></div>
</div>
<div class="tab-pane fade" id="trace_debug">
<div style="height:300px;overflow:auto"><pre>
                      {foreach key=k item=de from=$develapor_trace}
                      <p style="color:#3a87ad">{$k}.{$de}</p>
                      {/foreach}</pre></div>
</div>
<div class="tab-pane fade" id="trace_files">
<div style="height:300px;overflow:auto"><pre>
                      {foreach key=k item=file from=$files_include}
                      <p style="color:#3a87ad">{$k+1}.{$file|getsize}</p>
                      {/foreach}</pre>
                      </div>
</div>
<div class="tab-pane fade" id="trace_class">
<div style="height:300px;overflow:auto"><pre>
                      {foreach key=k item=classload from=$class_loader_all}
                      <p style="color:#3a87ad">{$k+1}.{$classload}</p>
                      {/foreach}</pre>
                      </div>
</div>


<div class="tab-pane fade" id="trace_log">
<div style="height:300px;overflow:auto"><table class="table">
{$log_htmls}</table>
</div>
</div>

<div class="tab-pane fade" id="trace_error">
<div style="height:300px;overflow:auto"><table class="table">
<tr><td>编号</td><td>错误</td><td>错误信息</td><td>文件</td><td>行数</td><td>时间</td></tr>
                      {foreach key=k item=errarray from=$logs_array}
                      <tr><td>{$errarray['uniqueid']}</td><td>{$errarray['errno']}</td><td>{$errarray['errstr']}</td><td>{$errarray['errfile']}</td><td>{$errarray['errline']}</td><td>{$errarray['time']}</td></tr>
                      {/foreach}</table>
                      </div>
</div>

</div>
</div>
<div id="crmpage_trace_open" style="height: 30px; float: right; text-align: right;overflow: hidden; 
position: fixed; bottom: 10px; right: 20px; color: rgb(0, 0, 0); line-height: 30px; cursor: pointer; display: block;">
<img src=debug.png width="20" height="20"/>
</div>
<script>
$(function(){
	$('#crmpage_trace_open').click(function(){
		$('#crm_page_trace').css('display','');
		$('#crmpage_trace_open').css('display','none');
	});
	$('#close_page').click(function(){
		$('#crm_page_trace').css('display','none');
		$('#crmpage_trace_open').css('display','');
	});
});
var showtrace=0;
$(document).keyup(function(e){
        var key =  e.which;
        if(key == 121){
			if(showtrace==0){
				$('#crm_page_trace').css('display','none');
				$('#crmpage_trace_open').css('display','');
				showtrace=1;
			}else{
				$('#crm_page_trace').css('display','');
				$('#crmpage_trace_open').css('display','none');
				showtrace=0;
			}
        }
 });
</script>
{/if}
{/strip}
