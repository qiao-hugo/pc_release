{strip}
	<div id="addNotePadWidgetContainer" class='modal'>
		<div class="modal-header contentsBackground">
            <button data-dismiss="modal" class="close" title="{vtranslate('LBL_CLOSE')}">&times;</button>
			<h3 id="massEditHeader">{vtranslate('LBL_ADD', $MODULE)} {vtranslate('LBL_NOTEPAD', $MODULE)}</h3>
		</div>
		<form class="form-horizontal">
			 <div class="control-group margin0px padding1per">
				<label class="control-label">{vtranslate('LBL_NOTEPAD_NAME', $MODULE)}<span class="redColor">*</span> </label>
				<div class="controls">
					<input type="text" name="notePadName" class="input-large" data-validation-engine="validate[required]" />
				</div>
			</div>
			<div class="control-group margin0px padding1per">
				<label class="control-label">{vtranslate('LBL_NOTEPAD_CONTENT', $MODULE)}</label>
				<div class="controls">
					<textarea type="text" name="notePadContent" style="min-height: 100px;resize: none;" ></textarea>
				</div>
			</div>
				{include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
		</form>
	</div>
{/strip}