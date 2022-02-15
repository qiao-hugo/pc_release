/*
 * jQuery selected 1.0 Bate
 * 
 * Copyright (c) 2012 Relict
 *
 * http://files.cnblogs.com/Relict/jQuery.selected.js
 *
 *
 */
(function ($) {
	$.fn.selected = function (settings, extraSettings) {
		var options;
		options = {
			NextSelId: '#nextsel',
			SelTextId: '#seltext',
			Separator: '  ',
			SelStrSet: [{ name: 'selected', subname: 'selected'}]
		};
		return this.each(function () {
			$.extend(options, settings, extraSettings);

			var $$, $$next;
			$$ = $(this);
			$$next = $(options.NextSelId);
			$$.append("<!--selected 1.0 Bate Copyright (c) 2012 Relict-->");
			$.each(options.SelStrSet, function () {
				var options = this;
				$$.append("<option value=" + options.name + ">" + options.name + "</option>");
			});
			function selchage() {
				$$.children("option").each(function (i, o) {
					if ($(this).attr("selected")) {
						$$next.children("option").remove();
						var temp = options.SelStrSet[i].subname.split("|");
						for (k = 0; k < temp.length; k++) {
							$$next.append("<option value=" + temp[k] + ">" + temp[k] + "</option>");
						};
					};
				});
			}
			function setText() {
				$(options.SelTextId).val($$.val() + options.Separator + $$next.val());
			}
			$$.change(function () {
				selchage();
				setText();
			});
			$$next.change(function () {
				setText();
			})
			selchage();
		});
	}
})(jQuery);