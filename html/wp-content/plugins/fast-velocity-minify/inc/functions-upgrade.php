<?php

function fastvelocity_version_check() {
	global $fastvelocity_plugin_version;
	
	# current FVM install date, create if it doesn't exist
	$ver = get_option("fastvelocity_plugin_version");
	if ($ver == false) { $ver = '0.0.0'; }
	
	# save on upgrade
	if ($ver != $fastvelocity_plugin_version) {
		update_option( "fastvelocity_plugin_version", $fastvelocity_plugin_version);
	}
	
	# compare versions (0.1.2)
	$dots = explode('.', $ver);
	if(!is_array($dots) || count($dots) != 3) { return false; }
	
	
	# changed options in 2.4.0
	if($dots[0] < 2 || ($dots[0] == 2 && $dots[1] < 4)) {
	
		# delete some old fields and define them on a radio option, by this priority
		if(get_option("fastvelocity_css_hide_googlefonts") != false) {
			update_option( "fastvelocity_gfonts_method", 3);
			delete_option('fastvelocity_min_force_inline_googlefonts');
			delete_option('fastvelocity_min_async_googlefonts');
			delete_option('fastvelocity_css_hide_googlefonts');
		}
		
		if(get_option("fastvelocity_min_async_googlefonts") != false) {
			update_option( "fastvelocity_gfonts_method", 2);
			delete_option('fastvelocity_min_force_inline_googlefonts');
			delete_option('fastvelocity_min_async_googlefonts');
			delete_option('fastvelocity_css_hide_googlefonts');
		}
		
		if(get_option("fastvelocity_min_force_inline_googlefonts") != false) {
			update_option( "fastvelocity_gfonts_method", 1);
			delete_option('fastvelocity_min_force_inline_googlefonts');
			delete_option('fastvelocity_min_async_googlefonts');
			delete_option('fastvelocity_css_hide_googlefonts');
		}
	
	}

}
add_action( 'plugins_loaded', 'fastvelocity_version_check' );
