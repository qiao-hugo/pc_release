{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
	{assign var="MODULE_NAME" value=$MODULE_MODEL->get('name')}

					</div>
				</form>
			</div>
			{*
			<div class="related span2 marginLeftZero">
				<div class="accordion" id="accordion-284164">
            	{foreach item=CATEGORYL from=$CATEGORY name=cate}
				<div class="accordion-group">
                
                   
					<div class="accordion-heading" style="background-color: #0065a6;">
						 <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-284164" href="#accordion-element-455981{$smarty.foreach.cate.index}" style="color:#fff;">{vtranslate($CATEGORYL['info']['knowledgecolumns'], $MODULE)}</a>
					</div>
                   	{if !empty($CATEGORYL['child'])}
					<div id="accordion-element-455981{$smarty.foreach.cate.index}" class="accordion-body collapse"  style="background-color:#fff;">
						<div class="accordion-inner">
						
                            {foreach item=CATEGORYSON from=$CATEGORYL['child']}
                            	<p class="text-info" id="{$CATEGORYSON['info']['knowledgecolumns']}"><i class="icon-hand-right"></i><a  href="/index.php?module=Knowledge&view=List&filter={$CATEGORYSON['info']['knowledgecolumns']}" >{vtranslate($CATEGORYSON['info']['knowledgecolumns'], $MODULE)}</a></p>
                            	{if !empty($CATEGORYSON['child'])}
	                            	{foreach item=CATEGORYSONS from=$CATEGORYSON['child']}
	                            		<p class="text-info" id="{$CATEGORYSONS['info']['knowledgecolumns']}" style="margin-left:40px;"><i class="icon-hand-right"></i><a  href="/index.php?module=Knowledge&view=List&filter={$CATEGORYSONS['info']['knowledgecolumns']}" >{vtranslate($CATEGORYSONS['info']['knowledgecolumns'], $MODULE)}</a></p>
	                            	{/foreach}
                            	{/if}
                            {/foreach}
                        
					 	</div>
					</div>
                  	{/if}
              
                       
				</div>
				 {/foreach}
			</div>
            *}
			</div>
		</div>
	</div>
	</div>
</div>
</div>
{/strip}