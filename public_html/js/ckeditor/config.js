/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.


	// config.toolbarGroups = [
		// { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		// { name: 'editing',     groups: [ 'spellchecker' ] },
		// { name: 'links' },
		// { name: 'insert' ,		groups: [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar' ] },
		// { name: 'others' },
		// '/',
		// { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		// { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		// { name: 'styles' ,   groups: [ 'Font','FontSize' ] },
		// { name: 'colors' },
		// { name: 'about' }
	// ];

	// config.toolbar =
	// [
		// { name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
		// { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		// { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		// { name: 'basicstyles', items : [ 'Bold','Italic','Underline' ] },
		// { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		// { name: 'links', items : [ 'Link','Unlink' ] },
		// { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar' ] },
		// '/',
		// { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		// { name: 'colors', items : [ 'TextColor','BGColor' ] },
		// { name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
	// ];
    config.extraPlugins = "placeholder";

	config.toolbar =
	[
		{ name: 'document', items : [ 'Source','-','Preview', 'CreatePlaceholder' ] },
		{ name: 'clipboard', items : [ 'PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Replace','SpellChecker' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		{ name: 'links', items : [ 'Link','Unlink' ] },
		{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar' ] },
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline' ] },
		{ name: 'styles', items : [ 'Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor'] }
	];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	// config.removeButtons = 'Underline,Subscript,Superscript';
	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;
	config.allowedContent = true;
	config.filebrowserBrowseUrl = 'filemanager/index.html';
	config.contentsLangDirection = 'rtl';
	config.pasteFromWordRemoveStyles = false;
	config.pasteFromWordRemoveFontStyles = false;
};
