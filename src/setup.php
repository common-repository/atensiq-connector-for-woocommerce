<?php 

//add_action('plugins_loaded', function(){
    load_plugin_textdomain(
        'wcat', 
        false, 
        basename(WCAT_ROOT) . '/langs'
    );
//});
