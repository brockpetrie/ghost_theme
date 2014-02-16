jQuery(document).ready(function() {
	FastClick.attach(document.body);
	jQuery('header#header > div.notifier').remove().appendTo('header#header');
	if (!jQuery('#nav > ul.structure > li:contains("Blueprints")').length) jQuery('#nav > ul.structure > li:last-child').remove();
	var navItem = jQuery('#nav ul.structure span.avatar').parent();
	navItem.remove().appendTo('#nav ul.structure').addClass('usermenu');
});