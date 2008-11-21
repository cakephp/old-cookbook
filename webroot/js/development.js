$(function() {
	/* Hide XDebug errors */
	$('table.xdebug-error').hide().before('<a href="#" class="debugToggle">Error Message! Show</a>');
	$('a.debugToggle').click(function(){
		if ($('a.debugToggle').next().toggle().is(':visible')) {
			$(this).text('Hide Error');
		} else {
			$(this).text('Show Error');
		}
		return false;
	});
	positionSqlLogs('outOfSight');
	$('.cake-sql-log').hover(function() {
		$(this).addClass("hover");
		positionSqlLogs('visible', this);
	},function(){
		$(this).removeClass("hover");
		positionSqlLogs('outOfSight');
	});
});
function positionSqlLogs(where, what) {
	$('.cake-sql-log:first').ajaxStop(function() {
		positionSqlLogs('outOfSight');
	});
	if (where == 'outOfSight') {
		var zIndex = 100;
		var count = 0;
		$('.cake-sql-log').each(function() {
			if (!$(this).parent().hasClass('sqlWrap')) {
				$(this).wrap('<div class="sqlWrap">');
			}
			$(this).css('left', '85%');
			$(this).css('z-index', zIndex);
			if (count) {
				$(this).css('bottom', count * 15 + 'px');
			}
			count++;
			zIndex--;
		});
	} else if (what) {
		$(what).css('left', 0);
		$(what).css('bottom', 0);
		$(what).css('z-index', 1000);
	}
}
