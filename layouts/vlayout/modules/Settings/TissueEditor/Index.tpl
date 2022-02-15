{strip}
	<div class="container-fluid" id="menuEditorContainer">
		<div class="widget_header row-fluid">
			<div class="span8"><h3>体系管理</h3></div>
		</div>
		<hr>
		
		<div class="contents">
			<form name="menuEditor" action="index.php" method="post" class="form-horizontal" id="menuEditor">
				<input type="hidden" name="module" value="{$MODULE_NAME}" />
				<input type="hidden" name="action" value="Save" />
				<input type="hidden" name="parent" value="Settings" />
				<div class="row-fluid paddingTop20">
					<select data-placeholder="管理体系" id="menuListSelectElement" class="select2 span12" multiple="" data-validation-engine="validate[required]" >
						{foreach key=PARENT_NAME item=MODULES_LIST from=$ALL_MODULES}
							<option value="{$PARENT_NAME}" {if in_array($PARENT_NAME, $SELECTED_MODULES)} selected {/if}>{$MODULES_LIST}</option>
								
							
						{/foreach}
					</select>
				</div>
                <div class="row-fluid paddingTop20">
                    <div class=" span6">
                        <button class="btn btn-success  pull-right" type="submit" name="saveMenusList">
                            <strong>{vtranslate('LBL_SAVE', 'MenuEditor')}</strong>
                        </button>
                    </div>
                </div>
				<input type="hidden" name="selectedModulesList" value='' />
				
			</form>
		</div>	
	</div>
{/strip}