// ***********************
// Attention: Do not move this file to another Folder.
// -> The PageRenderer (called in BackendController) expects a "JavaScript" folder!
// ***********************

// http://typo3.sascha-ende.de/docs/development/extensions-general/use-datepicker-in-own-backend-module/
requirejs(['jquery', 'twbs/bootstrap-datetimepicker'], function ($) {
	'use strict';

	$(function () {
		$('#datepicker-starttime').datetimepicker(
			{
				format:'YYYY-MM-DD',
				minDate: $('#datepicker-min-date').val(),
				maxDate: Date.now(),

				// Fix by MF: Define icons here, otherwise no icons are shown in datepicker popup
				icons: {
					next: 'fa fa-chevron-circle-right',
					previous: 'fa fa-chevron-circle-left'
				}
			}
		);
	});
});
