/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
 
var site_url = 'http://vserver/islam/ayacompany.com/';


CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
   config.filebrowserBrowseUrl 			= site_url+'assets/js/ckeditor/kcfinder/browse.php?opener=ckeditor&type=files';
   config.filebrowserImageBrowseUrl     = site_url+'assets/js/ckeditor/kcfinder/browse.php?opener=ckeditor&type=images';
   config.filebrowserFlashBrowseUrl     = site_url+'assets/js/ckeditor/kcfinder/browse.php?opener=ckeditor&type=flash';
   config.filebrowserUploadUrl 			= site_url+'assets/js/ckeditor/kcfinder/upload.php?opener=ckeditor&type=files';
   config.filebrowserImageUploadUrl     = site_url+'assets/js/ckeditor/kcfinder/upload.php?opener=ckeditor&type=images';
   config.filebrowserFlashUploadUrl     = site_url+'assets/js/ckeditor/kcfinder/upload.php?opener=ckeditor&type=flash';
};

CKEDITOR.config.allowedContent = true;
config.allowedContent = true;
config.format_tags = 'p;h1;h2;h3;h4;h5;h6;pre;address;div;i;span';
CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
CKEDITOR.config.autoParagraph = false;
config.protectedSource.push( /<\?[\s\S]*?\?>/g );   // PHP Code
