/*global $, document, jQuery, window */

$(function () {

	$(document).ready(function () {
		$("#tabs").tabs();
		$('#tabs').tabs('paging', { cycle: false, follow: false, nextButton : "", prevButton : "" } );
	});

});