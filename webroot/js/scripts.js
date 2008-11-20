$(document).ready(function() {
	$.extend({
		/**
		 * Make the ajax calls base-url (subfolder) independent
		 * call $.('/a/url') to get /cakeInstallIsHere/a/url out
		 */
		url: function(url) {
			return baseUrl + url.replace(/^\/+/, '');
		}
	});
	$('div.comments').hide();
	var loc = location;

	$('a.show-comment').click(function(){
		var anchor = $(this).attr('href').split('#')[1];
		var nodeId = anchor.split('-')[1];
		$("#comments-" + nodeId)
			.load($.url('/comments/' + nodeId))
			.slideDown();
		var targetOffset = $("#comments-" + nodeId).offset().top;
		$('html,body')
			.animate({scrollTop: targetOffset}, 1000);
		return false;
	});

	var currentUrl = document.location.toString();
	var anchor = currentUrl.split('#')[1];
	if (anchor) {
		/* Direct request for an anchor trigger equivalent link onclick */
		$('a[@href="#' + anchor + '"]').click();
	}
	/* Toggle Code */
	$('pre.code').before('<a class="codeToggle" href="#">Plain Text View</a>');
	$('a.codeToggle').click().toggle(function() {
			$(this).next().show();
			$(this).next().next().hide();
			$(this).text('Code View');
			return false;
		}, function() {
			$(this).next().hide();
			$(this).next().next().show();
			$(this).text('Plain Text View');
			return false;
	});
	/* admin "show on hover" */
	$('ul.tree-options').hide().after('&nbsp;<a href="#" class="treeOptionsToggle">Show Options</a>');
	$('a.treeOptionsToggle').click(function(){
		if ($(this).prev('ul').toggle().is(':visible')) {
			$(this).text('Hide Options');	
		} else {
			$(this).text('Show Options');	
		}
		return false;
	});
});
