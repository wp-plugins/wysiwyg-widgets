jQuery(document).ready(function(){
	
	var ww_clicked_textarea;
	
	jQuery(window).resize(function() { ww_updateOverlaySize(); });
	
	jQuery('.wysiwyg_overlay_toggle').live('click',function() {
		ww_clicked_textarea = jQuery(this);
		tinyMCE.execInstanceCommand('wysiwyg_textarea','mceSetContent',false,jQuery(this).val(),true);
		jQuery("#wysiwyg_widgets_overlay_bg,#wysiwyg_widgets_window").fadeIn(400,function() {
			switchEditors.go('wysiwyg_textarea', 'html'); switchEditors.go('wysiwyg_textarea', 'tinymce');
		});
		ww_updateOverlaySize();
	});
	
	jQuery('#wysiwyg_send_to_widget').click(function() {
		ww_clicked_textarea.val(tinyMCE.activeEditor.getContent());
		ww_closeOverlay();
	});
	
	jQuery(document).keyup(function(e) {
		if (e.keyCode == 27) { ww_askToClose(); } 
	});
	jQuery("#wysiwyg_widgets_window .close").click(function() {
		ww_askToClose();
	});
	
	ww_updateOverlaySize();
	
});

function ww_askToClose()
{
	var sure = confirm("Closing the WYSYWIG Widgets overlay will discard any changes that have been made. Really close?");
	if(sure) {
		ww_closeOverlay();
	}
}

function ww_closeOverlay()
{
	jQuery("#wysiwyg_widgets_overlay_bg,#wysiwyg_widgets_window").fadeOut(400);
}

function ww_updateOverlaySize() {
	jQuery("#wysiwyg_widgets_window").css({
		height: (jQuery(window).height() - 40),
		top: '20px'
	});
	
	jQuery("#wysiwyg_textarea_ifr").height(jQuery('#wysiwyg_widgets_content').height() - 160);
}