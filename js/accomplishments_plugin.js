(function() {
    // Creates the plugin
    tinymce.create( 'tinymce.plugins.accomplishments', {
        // Creates control instances based on the control's id.
        // Our button's id is "accomplishments_button"
        createControl : function( id, controlManager ) {
            if ( id == 'accomplishments_button' ) {
                // Creates the button
                var button = controlManager.createButton( 'accomplishments_button', {
                        title   : 'Accomplishments Shortcode', // Title of the button
                        image   : '../wp-includes/images/smilies/icon_mrgreen.gif', // Path to the button's image
                        onclick : function() {
                            // Do something when the button is clicked :)
                        }
                });
                
                return button;
            }
            
            return null;
        }
    });

    // Registers the plugin, DON'T MISS THIS STEP!!!
    tinymce.PluginManager.add( 'accomplishments', tinymce.plugins.accomplishments );
})();

