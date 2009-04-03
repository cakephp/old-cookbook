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
	 * adds.ajax suffix to ensure full page view caching doesn't get confused
	 * and then calls containLinks so that links and forms stay within the dialog
	 */
	$('.dialogs a, .popup a, a.popup, a.dialog, a.popout, a.confirm')
		.click(function(){
			var _this = $(this);
			var title = _this.attr('title');
			if (!title) {
				title = _this.text();
			}
			var el = $('<div class="dialog"><p></p></div>');
			el
				.attr('title', title)
				.appendTo('body')
				.load(_this.attr('href') + '.ajax', function(){
					contain(el);
					el.dialog({
						autoOpen: false,
						width: 500,
						height: 400,
						modal: el.hasClass('confirm|modal')?true:false
					}).dialog('open');
				});
			return false;
		});
	/**
	 * contain method
	 *
	 * contain both links and forms
	 *
	 * @return void
	 * @access public
	 */
	function contain (base) {
		containLinks(base);
		containForms(base);
	}
	/**
	 * containLinks method
	 *
	 * For the passed base find any links within it and ajax load into the same container -
	 * unless it's a popout, in which case load in its own dialog instead
	 */
	function containLinks (base) {
		var base = $(base);
		$('a', base).click(function() {
			var _this = $(this);
			var title = _this.attr('title');
			if (!title) {
				title = _this.text();
			}

			if (_this.hasClass('popout')) {
				_this
					.click(function(){
						$('<div class="dialog" style="display;none">Loading...</div>')
							.attr('title', title)
							.appendTo('body')
							.load(_this.attr('href') + '.ajax', function(){
								contain(_this);
							}).dialog({
								autoOpen: false,
								width: 500,
								height: 400,
							});
						return false;
					});
			} else {
				base.load(_this.attr('href') + '.ajax', function() {
					contain(base);
				});
			}
			return false;
		});
	}
	/**
	 * containForms method
	 *
	 * For the passed base find any forms and ajax load into the same container
	 */
	function containForms (base) {
		var base = $(base);
		$('form', base).bind('submit', function(){
			$(this).ajaxSubmit({
				target: base,
				success: function(r) {
					base.html(r);
					containForms(base);
				},
			});
			return false;
		});
	}
	/**
	 * For any div with the class ajaxFormContainer - load the form into the same container
	 * This prevents losing any open (none-modal) dialogs in the process, hence it's here
	 */
	$('div.ajaxFormContainer').each(function(){
		containForms(this);
	});
});