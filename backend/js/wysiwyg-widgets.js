var edCanvas;

jQuery(document).ready(function(){
    
    /**
     * Get active texteditor for WP Media Uploading
     */
    jQuery('.editor_media_buttons a').live('click', function(){
        edCanvas = jQuery('textarea[id^=widget-wysiwyg_widgets_widget]', jQuery(this).parents('div.widget')).get();
    });
    
    /**
     * Activate WYSIWYG Editor upon opening widget.
     */
    jQuery('div.widget:has(textarea[id^=widget-wysiwyg_widgets_widget]) a.widget-action').live('click', function(){
        var txt_area = jQuery('textarea[id^=widget-wysiwyg_widgets_widget]', jQuery(this).parents('div.widget'));
        WYSIWYG_Widgets.instantiate_editor(txt_area.attr('id'));
        return false;
    });
    
    /**
     * Get HTML value of WYSIWYG Editor for saving widget
     */
    jQuery('input[id^=widget-wysiwyg_widgets_widget][id$=savewidget]').live('click', function(){
        var txt_area = jQuery('textarea[id^=widget-wysiwyg_widgets_widget]', jQuery(this).parents('div.widget'));
        
        if (typeof(tinyMCE.get(txt_area.attr('id'))) == "object") {
            WYSIWYG_Widgets.deactivate_editor(txt_area.attr('id'));
        }
                 
        jQuery(this).unbind('ajaxSuccess').ajaxSuccess(function(e, x, s) {
            var txt_area = jQuery('textarea[id^=widget-wysiwyg_widgets_widget]', jQuery(this).parents('div.widget'));
            WYSIWYG_Widgets.instantiate_editor(txt_area.attr('id'));
        });
        
        return true;
    });
    
    /**
     * Switch to visual mode
     */
    jQuery('a[id^=widget-wysiwyg_widgets_widget][id$=visual]').live('click', function(){
        jQuery(this).addClass('active');
        jQuery('a[id^=widget-wysiwyg_widgets_widget][id$=html]', jQuery(this).parents('div.widget')).removeClass('active');
        jQuery('input[id^=widget-wysiwyg_widgets_widget][id$=type]', jQuery(this).parents('div.widget')).val('visual');
        WYSIWYG_Widgets.activate_editor(jQuery('textarea[id^=widget-wysiwyg_widgets_widget]', jQuery(this).parents('div.widget')).attr('id'));
        return false;
    });
    
    /**
     * Switch to HTML mode
     */
    jQuery('a[id^=widget-wysiwyg_widgets_widget][id$=html]').live('click', function(){
        jQuery(this).addClass('active');
        jQuery('a[id^=widget-wysiwyg_widgets_widget][id$=visual]', jQuery(this).parents('div.widget')).removeClass('active');
        jQuery('input[id^=widget-wysiwyg_widgets_widget][id$=type]', jQuery(this).parents('div.widget')).val('html');
        WYSIWYG_Widgets.deactivate_editor(jQuery('textarea[id^=widget-wysiwyg_widgets_widget]', jQuery(this).parents('div.widget')).attr('id'));
        return false;
    });
});

window.WYSIWYG_Widgets = {
    
    activate_editor : function (id) {
        jQuery('#'+id).addClass("mceEditor");
        if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
            WYSIWYG_Widgets.deactivate_editor(id);
            tinyMCE.execCommand("mceAddControl", false, id);
        }
    },
    
    deactivate_editor : function(id) {
        console.log("Deactivating editor");
        if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
            if (typeof(tinyMCE.get(id)) == "object") {
                var content = tinyMCE.get(id).getContent();
                tinyMCE.execCommand("mceRemoveControl", false, id);
                jQuery('textarea#'+id).val(content);
            }
        }
    },
    
    instantiate_editor : function(id) {
        jQuery('div.widget:has(#' + id + ') input[id^=widget-wysiwyg_widgets_widget][id$=type][value=visual]').each(function() {
            
            if (jQuery('div.widget:has(#' + id + ') :animated').size() == 0 && typeof(tinyMCE.get(id)) != "object" && jQuery('#' + id).is(':visible')) {
                jQuery('a[id^=widget-wysiwyg_widgets_widget][id$=visual]', jQuery(this).parents('div.widget')).click();
            }
            
            else if (typeof(tinyMCE.get(id)) != "object") {
                setTimeout(function(){ WYSIWYG_Widgets.instantiate_editor(id);}, 250);
                return;
            }
        });
    }  
    
    
}