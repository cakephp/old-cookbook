/* SVN FILE: $Id: popup.js 874 2009-03-24 13:30:58Z ad7six $ */
/**
 * popup.js for making dialogs
 *
 * Requires jquery-ui and the forms plugin
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.vendors.js
 * @since         v 1.0
 * @version       $Revision: 874 $
 * @modifiedby    $LastChangedBy: ad7six $
 * @lastmodified  $Date: 2009-03-24 14:30:58 +0100 (Tue, 24 Mar 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
$(function() {
	/**
	 * Attach jquery-ui.dialog to links that match the conditions
	 * Runs on first load
	 */
	$('a.confirm').addClass('popup modal');
	$('.dialogs a, a.dialog, .popup a, a.popup, a.popout').click(function(){
		return doModal(this);
	});
	/**
	 * contain method
	 *
	 * Contain both links and forms to the defined base dom element
	 *
	 * @return void
	 * @access public
	 */
	function contain (base) {
		containLinks(base);
		containForms(base);
	}
	/**
	 * containLinks
	 *
	 * For the passed base dom element, find any links within it and pass to doModal
	 */
	function containLinks (base) {
		var base = $(base);
		$('a', base).click(function() {
			return doModal($(this), base);
		});
	}
	/**
	 * containForms method
	 *
	 * For the passed base dom element, find any forms and ajax load into the same container
	 * For any links that are within the base - force to open in a (new/orphan) dialog
	 */
	function containForms (base) {
		var base = $(base);
		$('form', base).bind('submit', function(){
			$(this).ajaxSubmit({
				target: base,
				success: function(r) {
					base.html(r);
					containForms(base);
					$('a', base).click(function() {
						return doModal($(this));
					});
				},
			});
			return false;
		});
	}
	/**
	 * doModal method
	 *
	 * For the passed link dom element
	 * 	Use the link title attribute if set, or the link text if not, as the dialog title
	 * 	If the link has the class 'breakout' do not do anything (load the link normally)
	 * 	If no base is defined (links found on first page load & links inside an ajax-form) load the contents
	 * 	into a dialog window and display it
	 * 		Any links or forms inside the dialog will be recursively handled in the same manner
	 * 	If a base is defined, load the link's contents into the base
	 *
	 * Modal window properties can be modfied by adding css classes to the a tags:
	 * 	breakout - prevent the link target from being loaded into the base if appropriate
	 * 	confirm|modal - force a modal window. defaults to none-modal
	 * 	noResize - prevent users from being able to resize the window
	 * 	noDrag - prevent users from being able to move the window
	 *
	 * E.g. to create a link which is a modal, none-resizable, none-movable dialog, you'd create:
	 * 	<a href="source url" class="popup modal noResize noDrag">Click here to be forced to read/choose/etc. something</a>
	 * E.g. to use for a div already in the source:
	 * 	<a href="#sourceId" class="popup">Click here to see something quickly</a>
	 *
	 * The result of loading GET requests by ajax is 'cached' such that there is only one ajax request per dialog -
	 * 	a link within a dialog however, will override this caching mechanism.
	 *
	 */
	function doModal(a, base) {
		a = $(a);
		if (base) {
			base = $(base);
		}
		var title = a.attr('title');
		if (!title) {
			title = a.text();
		}
		var targetId = a.attr('href');
		if (targetId[0] == '#') {
			targetId = targetId.replace(/[#]/g, "");
		} else {
			targetId = 'mialog-' + targetId.replace(/[\/]/g, "");
		}
		if (base && a.hasClass('breakout')) {
			return;
		} else if (!base || a.hasClass('popout')) {
			var el = $('#' + targetId);
			if (el.length) {
				if (!el.parent('.ui-dialog').length) {
					el.attr('title', title)
						.dialog({
							width: 500,
							height: 400,
							modal: a.hasClass('modal')?true:false,
							resizable: a.hasClass('noResize')?false:true,
							draggable: a.hasClass('noDrag')?false:true
						});
				}
				el.dialog('open');
				return false;
			}
			el = $('<div class="dialog"></div>');
			el
				.attr('title', title)
				.attr('id', targetId)
				.appendTo('body')
				.load(a.attr('href') + '.ajax', function(){
					contain(el);
					el.dialog({
						width: 500,
						height: 400,
						modal: a.hasClass('modal')?true:false,
						resizable: a.hasClass('noResize')?false:true,
						draggable: a.hasClass('noDrag')?false:true
					});
					var titleBar = $('.ui-dialog-titlebar', el.parent());
					if (targetId[0] != '#') {
						var leave = $('<a><span class="ui-icon ui-icon-arrowthick-1-ne"></span></a>')
							.addClass('leavePopup breakout ui-corner-all')
							.attr('href', a.attr('href'))
							.attr('title', 'Open in a new window (' + a.attr('href') + ')')
							.attr('target', '_blank')
							.hover(
								function() {
									leave.addClass('ui-state-hover');
								},
								function() {
									leave.removeClass('ui-state-hover');
								}
							)
							.focus(function() {
								leave.addClass('ui-state-focus');
							})
							.blur(function() {
								leave.removeClass('ui-state-focus');
							})
							.appendTo(titleBar);
						}
				});
		} else {
			/* Invasion of the dialog snatchers, change the id, fix the height and width so it doesn't resize on load */
			var titleBar = $('.ui-dialog-titlebar', base.parent());
			base
				.attr('id', targetId)
				.css('width', base.width() + 'px')
				.css('height', base.height() + 'px')
				.load(a.attr('href') + '.ajax', function() {
					$('.ui-dialog-title', titleBar).text(title);
					$('.leavePopup', titleBar)
						.attr('href', a.attr('href'))
						.attr('title', 'Open in a new window (' + a.attr('href') + ')')
					contain(base);
				});
		}
		return false;
	}
	/**
	 * For any div with the class ajaxFormContainer - load the form into the same container
	 * This prevents losing any open (none-modal) dialogs in the process, hence it's here
	 */
	$('div.container').each(function(){
		containForms(this);
	});
});