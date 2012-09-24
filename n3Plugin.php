<?php
/*
Plugin Name: Phoenix N3
Plugin URI: http://enetres.net/
Description: Adds an editor button for embedding media from your n3 media library / Adiciona un botón en su editor que le permite embeber contenido de su biblioteca de medios n3
Version: 1.0.0
Author: Enetres Media Solutions
Author URI: http://enetres.net/
License: GPLv3
*/


add_action('init', 'n3videoInit');


/*
* Get hooked in
*/
function n3videoInit()
{
	$allowedposttags["iframe"] = array(
	 "src" => array(),
	 "height" => array(),
	 "width" => array(),
	 "frameborder" => array(),
	 "marginheight" => array(),
	 "marginwidth" => array(),	 	 	 
	);
	


	add_filter( 'admin_init', 'n3_reg_settings');	
	add_action( 'admin_menu', 'n3_config_page' );
	add_action( 'admin_print_footer_scripts', 'n3_qtags_footer_admin', 100 );
	add_action( 'admin_print_scripts', 'n3_qtags_admin', 100 );

	
	//if ( get_user_option('rich_editing') == 'true') {
	 add_filter('mce_external_plugins', 'add_n3_tinymce_plugin');
     add_filter('mce_buttons_2', 'register_n3_button');
	 add_filter( 'tiny_mce_version', 'my_refresh_mce');
   //}

  
}

  

function add_n3_tinymce_plugin($plugin_array) {
   $plugin_array['n3Button'] = '';   
   return $plugin_array;
}

function my_refresh_mce($ver) {
  $ver += 3;
  return $ver;
}

function register_n3_button($buttons) {
   array_push($buttons, "|", "n3Button");
   return $buttons;
}



	/**
	 * Register options
	 *
	 * @return void
	 */
	function n3_reg_settings() {		
		register_setting( 'N3-options', 'N3_username');
		register_setting( 'N3-options', 'N3_apikey');
	}
	
	
	/**
	 * Create menu items
	 *
	 * Create n3 configuration menu item
	 * Create n3 JS menu item
	 * @return void
	 */
	function n3_config_page() {
		global $peb_admin_page;
		$peb_admin_page = add_options_page('Phoenix N3 Options', 'Phoenix N3 Options', 'administrator', 'N3Video', 'n3_conf');

	}




	function n3_qtags_admin() {
		global $hook_suffix;
		if ( 'post.php' != $hook_suffix && 'post-new.php' != $hook_suffix ) return;
		
	 	wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-form');            
	    wp_enqueue_script('jquery-ui-core');            
	    wp_enqueue_script('jquery-ui-widget');    
	    wp_enqueue_script('jquery-ui-tabs');  
	    wp_enqueue_script('jquery-ui-button');
	
		wp_enqueue_script( 'N3WPscripts', plugins_url('/WPscripts.js', __FILE__) ,array(),NULL);
    	wp_enqueue_script( 'N3ThemeSwitcher', plugins_url('/themeSwitcher.js', __FILE__),array(),NULL);
		
	}


/**
	 * Write the Custom JS
	 *
	 */
	function n3_qtags_footer_admin() {
		global $hook_suffix;
		if ( 'post.php' != $hook_suffix && 'post-new.php' != $hook_suffix ) return;

		$username = get_option('N3_username');
		$apikey = get_option('N3_apikey');		
		
   
	
		?>

      
        
		<link type="text/css" href="http://core.enetres.net/CoreV1/plugin/css/ui/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
		<link type="text/css" href="http://core.enetres.net/CoreV1/plugin/css/modalStyles.css" rel="stylesheet" />
            
            	
		<script type="text/javascript">
		
			jQuery(function() {
					QTags.addButton( 'n3VideoButton', 'N3Video', n3PostVideoHandler );
					
					 tinymce.create('tinymce.plugins.N3Button', {
						init : function(ed, url) {
							ed.addButton('n3Button', {
								title : 'N3Video',
								image : '<?php echo plugins_url('/phoenix-n3/n3.png') ?>',
								onclick : function() {
									editorBookMark =null;
									try
									{
										editorBookMark = tinyMCE.activeEditor.selection.getBookmark();
									}
									catch(e)
									{
															
									}
									n3_modal_object.open(n3videoItems,"visual");
								}
							});
						},
						createControl : function(n, cm) {
							return null;
						},
						getInfo : function() {
							return {
								longname : "N3 Video",
								author : 'N3 Media Solutions',
								authorurl : 'http://enetres.net/',
								infourl : 'http://enetres.net/',
								version : "1.0"
							};
						}
					});
					tinymce.PluginManager.add('n3Button', tinymce.plugins.N3Button);
    
			});
		 
		 	var n3UserName="<?php echo $username; ?>";
		 	var n3ApiKey="<?php echo $apikey; ?>";			

			var n3videoItems="<?php
				$url = 'http://core.enetres.net/CoreV1/plugin/auth.jsp?userName=' . $username .'&apiKey=' .  $apikey ;
				$pagina = @file_get_contents($url);
				
				if ($pagina===FALSE)
				{
					$pagina="<h1> Invalid request parameters <h1>";
				}
				

				$salida= str_replace ('"' , '\"' , $pagina);
				$order   = array("\r\n", "\n", "\r");				
				$salida= str_replace ( $order , '@@line@@' , $salida );
				$salida= str_replace ( '<' , '&lt;' , $salida );
				
				echo $salida ;
			?>";

		</script>
        <?php
	}


	/**
	 * The configuration page
	 *
	 */
	function n3_conf() { 
	?>
		<div class="wrap">
        
		
        <table class="form-table" style="width:400px; margin: 0; padding:0" >		
		<tr valign="middle">
			<td ><img src="<?php echo plugins_url('/phoenix-n3/n3.png') ?>" alt="Phoenix N3" style="display: inline;" /></td>
			<td ><h2 style="display: inline" ><?php _e( 'Phoenix N3 Options', 'Phoenix N3 Options' ); ?></h2></td>			
		</tr>
        </table>
        
		<form method="post" action="options.php">
		
        
        
        <?php settings_fields('N3-options'); ?>
		<?php do_settings_sections('N3Video'); ?>


		<table class="form-table" id="op_table" style="width:auto">		
		<tr valign="top">
			<td style="font-weight:bold">User Name</td>
			<td style="font-weight:bold">APIKey</td>
			
		</tr>
		<?php
			$username = get_option('N3_username');
			$apikey = get_option('N3_apikey');		
		?>
				<tr valign="top" >
					<td><input type="text" name="N3_username" value="<?php echo $username; ?>" /></td>
					<td><input type="text" name="N3_apikey" style="width:250px" value="<?php echo $apikey; ?>" /></td>


				</tr>
		</table>
        
		<table class="form-table">
		<tr valign="top">
			<td>		
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes', 'N3-Video-Options' ) ?>" /> <?php _e( 'Be sure to hit save after deleting any rows.' , 'N3-Video-Options' ); ?></p>
			
			</td>
		</tr>
		</table>
		</form>
		</div>
        
    <?php 
	}
