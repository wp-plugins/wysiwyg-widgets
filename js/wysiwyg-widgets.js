jQuery(document).ready(function(){
	
	var ww_clicked_textarea = "";
	
	jQuery(window).resize(function() { ww_updateOverlaySize(); });
	
	jQuery('.wysiwyg-overlay-toggle').live('click',function() {
		ww_clicked_textarea = jQuery(this).attr('id');
		tinyMCE.execInstanceCommand('wysiwyg-textarea','mceSetContent',false,ww_nl2br(jQuery(this).val()),true);
		switchEditors.go('wysiwyg-textarea', 'html'); switchEditors.go('wysiwyg-textarea', 'tinymce');
		jQuery("#wysiwyg-widgets-overlay-bg,#wysiwyg-widgets-window").fadeIn(400);
	});
	
	jQuery('#wysiwyg-send-to-widget').click(function() {
		tinyMCE.triggerSave();
		jQuery('#'+ww_clicked_textarea).val(jQuery("#wysiwyg-textarea").val());
		ww_closeOverlay();
	});
	
	jQuery("#wysiwyg-widgets-window .close").click(function() {
		var sure = confirm("Closing the WYSYWIG Widgets overlay will discard any changes that have been made. Really close?");
		if(sure) {
			ww_closeOverlay();
		}
	});
	
	ww_updateOverlaySize();
	
});

function ww_nl2br (str) {   
	var breakTag = '<br />';
	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

function ww_closeOverlay()
{
	jQuery("#wysiwyg-widgets-overlay-bg,#wysiwyg-widgets-window").fadeOut(400);
}

function ww_updateOverlaySize() {
	jQuery("#wysiwyg-widgets-window").css({
		height: (jQuery(window).height() - 40),
		top: '20px'
	});
	
	jQuery("#wysiwyg-textarea_ifr").height(jQuery('#wysiwyg-widgets-content').height() - 100);
}