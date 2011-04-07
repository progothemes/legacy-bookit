<?php
/**
 * @package ProGo
 * @subpackage BookIt
 * @since BookIt 1.0
 *
 * Defines all the functions, actions, filters, widgets, etc., for ProGo Themes' BookIt theme.
 *
 * Some actions for Child Themes to hook in to are:
 * progo_frontend_scripts, progo_frontend_styles
 *
 * Some overwriteable functions ( wrapped by "if(!function_exists(..." ) are:
 * progo_posted_on, progo_posted_in, progo_gateway_cleanup, progo_prepare_transaction_results,
 * progo_admin_menu_cleanup, progo_custom_login_logo, progo_custom_login_url, progo_metabox_cleanup ...
 *
 * Most Action / Filters hooks are set in the progo_setup function, below. overwriting that could cause quite a few things to go wrong.
 */

$content_width = 584;

global $progo_bookit_db_version;
$progo_bookit_db_version = "1.0";

/** Tell WordPress to run progo_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'progo_setup' );

if ( ! function_exists( 'progo_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_theme_support( 'post-thumbnails' ) To add support for post thumbnails.
 *
 * @since BookIt 1.0
 */
function progo_setup() {
	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style( 'editor-style.css' );
	
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'topnav' => 'Top Navigation',
		'footer' => 'Footer "Learn" Links',
	) );
	
	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'post-thumbnail', 81, 81, true );
	add_image_size( 'medium', 237, 237, true );
	
	// add custom actions
	add_action( 'admin_init', 'progo_admin_init' );
	add_action( 'widgets_init', 'progo_bookit_widgets' );
	add_action( 'admin_menu', 'progo_admin_menu_cleanup' );
	add_action( 'login_head', 'progo_custom_login_logo' );
	add_action( 'login_headerurl', 'progo_custom_login_url' );
	add_action('wp_print_scripts', 'progo_add_scripts');
	add_action('wp_print_styles', 'progo_add_styles');
	add_action( 'admin_notices', 'progo_admin_notices' );
	
	// add custom filters
	add_filter( 'default_content', 'progo_set_default_body' );
	add_filter( 'site_transient_update_themes', 'progo_update_check' );
	add_filter( 'wpsc_pre_transaction_results', 'progo_prepare_transaction_results' );
	add_filter('body_class','progo_bodyclasses');
	
	if ( !is_admin() ) {
		// brick it if not activated
		if ( get_option( 'progo_bookit_apiauth' ) != 100 ) {
			add_action( 'template_redirect', 'progo_to_twentyten' );
		}
	}
}
endif;

/********* Front-End Functions *********/

if ( ! function_exists( 'progo_posted_on' ) ):
/**
 * Prints HTML with meta information for the current post—date/time and author.
 * @since ProGo BookIt 1.0
 */
function progo_posted_on() {
	echo 'Posted by: <a class="url fn n" href="'. get_author_posts_url( get_the_author_meta( 'ID' ) ) .'">'. get_the_author() .'</a> on '. get_the_date();
}
endif;
if ( ! function_exists( 'progo_posted_in' ) ):
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 * @since ProGo BookIt 1.0
 */
function progo_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'progo' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'progo' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'progo' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;
if ( ! function_exists( 'progo_gateway_cleanup' ) ):
/**
 * checkout page FIELD LABEL formatting function
 * returns the PAYMENT GATEWAY html with revised labels
 * @param gate_code
 * @return revised gate_code html
 * @since BookIt 1.0
 */
function progo_gateway_cleanup( $gate_code ) {
	$gate_code = str_replace( array( 'Credit Card Number', 'Credit Card Expiry' ), array( 'Card Number', 'Expiration' ), $gate_code );
	return '<fieldset class="check"><table width="100%" height="155" cellpadding="0" cellspacing="0">'. $gate_code .'</table></fieldset>';
}
endif;
if ( ! function_exists( 'progo_prepare_transaction_results' ) ):
/**
 * filter for wpsc_pre_transaction_results
 * @since BookIt 1.0
 */
function progo_prepare_transaction_results() {
	global $purchase_log;
	$options = get_option( 'progo_options' );
	$purchase_log['find_us'] = '<table><tr class="firstrow"><td>Our Company Info</td></tr><tr><td>'. esc_html( $options['companyinfo'] ) .'</td></tr></table>';
}
endif;
/********* Back-End Functions *********/

if ( ! function_exists( 'progo_admin_menu_cleanup' ) ):
/**
 * hooked to 'admin_menu' by add_action in progo_setup()
 * @since BookIt 1.0
 */
function progo_admin_menu_cleanup() {
	global $menu;
	global $submenu;
	
	// lets go
	// Dashboard | ProGo Themes | Pages/Posts/Products/Media/Links/Comments | ...
	$menu[8] = $menu[5];
	$menu[7] = $menu[20];
	unset($menu[20]);
	$menu[9] = $menu[26];
	unset($menu[26]);
	
	
	add_menu_page( 'Site Settings', 'ProGo Themes', 'edit_theme_options', 'progo_site_settings', 'progo_site_settings_page', get_bloginfo( 'template_url' ) .'/images/logo_menu.png', 5 );
	add_submenu_page( 'progo_site_settings', 'Store Settings', 'Store Settings', 'edit_theme_options', 'wpsc-settings', 'options-general.php' );
	add_submenu_page( 'progo_site_settings', 'Menus', 'Menus', 'edit_theme_options', 'nav-menus.php' );
	
	$submenu['progo_site_settings'][0][0] = 'Site Settings';
	
	// add extra line
	$menu[6] = $menu[4];
	
//	wp_die('<pre>'. print_r($menu,true) .'</pre>');
}
endif;
if ( ! function_exists( 'progo_custom_login_logo' ) ):
/**
 * hooked to 'login_head' by add_action in progo_setup()
 * @since BookIt 1.0
 */
function progo_custom_login_logo() {
	if ( get_option('progo_logo') != '' ) {
		#needswork
		echo "<!-- login screen here... overwrite logo with custom logo -->\n"; 
	} else { ?>
<style type="text/css">
#login { margin-top: 6em; }
h1 a { background: url(<?php bloginfo( 'template_url' ); ?>/images/logo_progo.png) no-repeat top center; height: 80px; }
</style>
<?php }
}
endif;
if ( ! function_exists( 'progo_custom_login_url' ) ):
/**
 * hooked to 'login_headerurl' by add_action in progo_setup()
 * @uses get_option() To check if a custom logo has been uploaded to the back end
 * @return the custom URL
 * @since BookIt 1.0
 */
function progo_custom_login_url() {
	if ( get_option( 'progo_logo' ) != '' ) {
		return get_bloginfo( 'url' );
	} // else
	return 'http://www.progo.com';
}
endif;
if ( ! function_exists( 'progo_site_settings_page' ) ):
/**
 * outputs HTML for ProGo Themes "Site Settings" page
 * @uses settings_fields() for hidden form items for 'progo_options'
 * @uses do_settings_sections() for 'progo_site_settings'
 * @since BookIt 1.0
 */
function progo_site_settings_page() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"></div>
		<h2>Site Settings</h2>
		<form action="options.php" method="post" enctype="multipart/form-data"><?php
		settings_fields( 'progo_options' );
		do_settings_sections( 'progo_site_settings' );
		?><p class="submit"><input type="submit" name="updateoption" value="Update &raquo;" /></p>
		</form>
	</div>
<?php
}
endif;
if ( ! function_exists( 'progo_admin_page_styles' ) ):
/**
 * hooked to 'admin_print_styles' by add_action in progo_setup()
 * adds thickbox js for WELCOME screen styling
 * @since BookIt 1.0
 */
function progo_admin_page_styles() {
	global $pagenow;
	if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) ) {
		$thispage = $_GET['page'];
		if ( $thispage == 'progo_welcome' ) {
			wp_enqueue_style( 'dashboard' );
			wp_enqueue_style( 'global' );
			wp_enqueue_style( 'wp-admin' );
			wp_enqueue_style( 'thickbox' );
		}
	}
	wp_enqueue_style( 'progo_admin', get_bloginfo( 'template_url' ) .'/admin-style.css' );
}
endif;
if ( ! function_exists( 'progo_admin_page_scripts' ) ):
/**
 * hooked to 'admin_print_scripts' by add_action in progo_setup()
 * adds thickbox js for WELCOME screen Recommended Plugin info
 * @since BookIt 1.0
 */
function progo_admin_page_scripts() {
	global $pagenow;
	if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'progo_welcome' ) ) ) {
		wp_enqueue_script( 'thickbox' );
	}
}
endif;
if ( ! function_exists( 'progo_admin_init' ) ):
/**
 * hooked to 'admin_init' by add_action in progo_setup()
 * adds functionality for progo_admin_action to progo_reset_wpsc or new_bookit_page
 * removes meta boxes on EDIT PAGEs, and adds progo_bookit_box for BookIt pages
 * creates CRM table if it does not exist yet
 * sets admin action hooks
 * registers Site Settings
 * @since BookIt 1.0
 */
function progo_admin_init() {
	if ( isset( $_REQUEST['progo_admin_action'] ) ) {
		switch( $_REQUEST['progo_admin_action'] ) {
			case 'reset_wpsc':
				progo_reset_wpsc();
				break;
		}
	}
	
	//Removes meta boxes from pages
	remove_meta_box( 'postcustom', 'page', 'normal' );
	remove_meta_box( 'trackbacksdiv', 'page', 'normal' );
	remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
	remove_meta_box( 'commentsdiv', 'page', 'normal' );
	remove_meta_box(  'authordiv', 'page', 'normal' );
	
	$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
	
	// hack to check the db creation for CRM ?
	global $wpdb;
	global $progo_bookit_db_version;

	$table_name = $wpdb->prefix ."progo_crm";
	if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
			time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			firstname tinytext NOT NULL,
			lastname tinytext NOT NULL,
			address tinytext NOT NULL,
			city tinytext NOT NULL,
			state tinytext NOT NULL,
			zip mediumint(5) UNSIGNED NOT NULL,
			phone tinytext NOT NULL,
			email tinytext NOT NULL,
			purchased bool NOT NULL,
			UNIQUE KEY id (id)
		);";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		add_option( "progo_bookit_db_version", $progo_bookit_db_version );
	}
	// ACTION hooks
	add_action( 'admin_print_styles', 'progo_admin_page_styles' );
	add_action( 'admin_print_scripts', 'progo_admin_page_scripts' );
	
	// Site Settings page
	register_setting( 'progo_options', 'progo_options', 'progo_options_validate' );
	
	add_settings_section( 'progo_api', 'ProGo Themes API Key', 'progo_section_text', 'progo_site_settings' );
	add_settings_field( 'progo_api_key', 'API Key', 'progo_field_apikey', 'progo_site_settings', 'progo_api' );

	add_settings_section( 'progo_info', 'Site Info', 'progo_section_text', 'progo_site_settings' );
	add_settings_field( 'progo_blogname', 'Site Name', 'progo_field_blogname', 'progo_site_settings', 'progo_info' );
	add_settings_field( 'progo_blogdescription', 'Slogan', 'progo_field_blogdesc', 'progo_site_settings', 'progo_info' );
	add_settings_field( 'progo_companyinfo', 'Company Info', 'progo_field_compinf', 'progo_site_settings', 'progo_info' );

	add_settings_section( 'progo_checkout', 'Checkout Page', 'progo_section_text', 'progo_site_settings' );
	add_settings_field( 'progo_checkout', 'Headline', 'progo_field_checkout', 'progo_site_settings', 'progo_checkout' );
	
	// since there does not seem to be an actual THEME_ACTIVATION hook, we'll fake it here
	if ( get_option( 'progo_bookit_installed' ) != true ) {
		$menus = array( 'topnav', 'footer' );
		$menu_ids = array();
		foreach ( $menus as $men ) {
			// create the menu in the Menu system
			$menu_ids[$men] = wp_create_nav_menu( $men );
		}
		set_theme_mod( 'nav_menu_locations' , $menu_ids );
		
		// set our default SITE options
		progo_options_defaults();
		
		// and redirect
		wp_redirect( get_option( 'siteurl' ) . '/wp-admin/admin.php?page=progo_site_settings' );
	}
}
endif;

if ( ! function_exists( 'progo_bookit_widgets' ) ):
/**
 * registers a sidebar area for the WIDGETS page
 * and registers various Widgets
 * @since BookIt 1.0.57
 */
function progo_bookit_widgets() {
	register_sidebar(array(
		'name' => 'Right Column',
		'id' => 'sidebar',
		'description' => 'For the right column of the site. If no widgets appear below, "Testimonials", "Investor Resources", "Twitter", and "Facebook" will show',
		'before_widget' => '<div class="block %1$s %2$s">',
		'after_widget' => '</div><div class="e"></div></div>',
		'before_title' => '<h3 class="title"><span class="spacer">',
		'after_title' => '</span></h3><div class="inside">'
	));
	register_sidebar(array(
		'name' => 'Blog Sidebar',
		'id' => 'blogside',
		'description' => 'Right Column for Blog area',
		'before_widget' => '<div class="block %1$s %2$s">',
		'after_widget' => '</div><div class="e"></div></div>',
		'before_title' => '<h3 class="title"><span class="spacer">',
		'after_title' => '</span></h3><div class="inside">'
	));
	register_sidebar(array(
		'name' => 'Footer',
		'id' => 'fwidgets',
		'description' => 'Widgets for the Footer area',
		'before_widget' => '<li class="fblock %1$s %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="title">',
		'after_title' => '</h3>'
	));
	register_sidebar(array(
		'name' => 'Contact Page',
		'id' => 'contact',
		'description' => 'Right Column for Contact page',
		'before_widget' => '<div class="block %1$s %2$s">',
		'after_widget' => '</div><div class="e"></div></div>',
		'before_title' => '<h3 class="title"><span class="spacer">',
		'after_title' => '</span></h3><div class="inside">'
	));
	register_sidebar(array(
		'name' => 'Cart',
		'id' => 'cart',
		'description' => 'Shopping Cart additional widgets',
		'before_widget' => '<div class="block %1$s %2$s">',
		'after_widget' => '</div><div class="e"></div></div>',
		'before_title' => '<h3 class="title"><span class="spacer">',
		'after_title' => '</span></h3><div class="inside">'
	));
	register_sidebar(array(
		'name' => 'Book David',
		'id' => 'bookspeaker',
		'description' => 'Additional widget area',
		'before_widget' => '<div class="block %1$s %2$s">',
		'after_widget' => '</div><div class="e"></div></div>',
		'before_title' => '<h3 class="title"><span class="spacer">',
		'after_title' => '</span></h3><div class="inside">'
	));
	
	$included_widgets = array( 'Social', 'Tweets', 'FBLikeBox', 'Family', 'InvestorResources','BookIt' );
	foreach ( $included_widgets as $wi ) {
		require_once( 'widgets/widget-'. strtolower($wi) .'.php' );
		register_widget( 'ProGo_Widget_'. $wi );
	}
}
endif;
if ( ! function_exists( 'progo_metabox_cleanup' ) ):
/**
 * fires after wpsc_meta_boxes hook, so we can overwrite a lil bit
 * @since BookIt 1.0
 */
function progo_metabox_cleanup() {
	global $wp_meta_boxes;
	global $post_type;
	global $post;
	
	switch($post_type) {
		case 'wpsc-product':
			if ( isset( $wp_meta_boxes['wpsc-product'] ) ) {
				// unhook wpsc's Product Images metabox and add our own instead
				remove_meta_box( 'wpsc_product_image_forms', 'wpsc-product', 'normal' );
				add_meta_box( 'progo_product_image_forms', 'Product Images', 'progo_product_image_forms', 'wpsc-product', 'normal', 'high' );
				// sort the wpsc-product main column meta boxes so Product Images is #1
				$wp_meta_boxes['wpsc-product']['normal']['high'] = progo_arraytotop( $wp_meta_boxes['wpsc-product']['normal']['high'], 'progo_product_image_forms' );
				
				// also move PRICE to just under SUBMITdiv on right
				// Backup and delete element from parent array
				$toparr = array(
					'submitdiv' => $wp_meta_boxes['wpsc-product']['side']['core']['submitdiv'],
					'wpsc_price_control_forms' => $wp_meta_boxes['wpsc-product']['side']['low']['wpsc_price_control_forms']
				);
				unset($wp_meta_boxes['wpsc-product']['side']['core']['submitdiv']);
				unset($wp_meta_boxes['wpsc-product']['side']['low']['wpsc_price_control_forms']);
				// Merge the two arrays together so our widget is at the beginning
				$wp_meta_boxes['wpsc-product']['side']['core'] = array_merge( $toparr, $wp_meta_boxes['wpsc-product']['side']['core'] );
			}
			break;
	}
}
endif;
add_action( 'do_meta_boxes', 'progo_metabox_cleanup' );

/********* core ProGo Themes' BookIt functions *********/

if ( ! function_exists( 'progo_add_scripts' ) ):
/**
 * hooked to 'wp_print_scripts' by add_action in progo_setup()
 * adds front-end js
 * @since BookIt 1.0
 */
function progo_add_scripts() {
	if ( !is_admin() ) {
		wp_register_script( 'progo', get_bloginfo('template_url') .'/js/progo-frontend.js', array('jquery'), '1.0' );
		wp_enqueue_script( 'progo' );
		do_action('progo_frontend_scripts');
		
		// check for SHARETHIS
		$stwidg = get_option('st_widget');
		if($stwidg != '' && is_page(5) ) {
			$stwidg = '';
			update_option('st_widget',$widget);
		}
	}
}
endif;
if ( ! function_exists( 'progo_add_styles' ) ):
/**
 * hooked to 'wp_print_styles' by add_action in progo_setup()
 * @since BookIt 1.0
 */
function progo_add_styles() {
	do_action('progo_frontend_styles');
}
endif;
if ( ! function_exists( 'progo_reset_wpsc' ) ):
/**
 * sets WPSC image/thumbnail sizes to ProGo recommended settings
 * also updates wpsc_email_receipt
 * @since BookIt 1.0
 */
function progo_reset_wpsc(){
	check_admin_referer( 'progo_reset_wpsc' );
	//set thumbnail & main image size to desired dimensions
	update_option( 'product_image_width', 246 );
	update_option( 'product_image_height', 276 );
	update_option( 'single_view_image_width', 246 );
	update_option( 'single_view_image_height', 276 );
	
	update_option( 'wpsc_email_receipt', "Any items to be shipped will be processed as soon as possible, any items that can be downloaded can be downloaded using the links on this page. All prices include tax and postage and packaging where applicable.\n\n%product_list%%total_price%%find_us%" );
	
	wp_redirect( get_option('siteurl') .'/wp-admin/' );
	exit();
}
endif;
if ( ! function_exists( 'progo_arraytotop' ) ):
/**
 * helper function to bring a given element to the start of an array
 * @param parent array
 * @param element to bring to the top
 * @return sorted array
 * @since BookIt 1.0
 */
function progo_arraytotop($arr, $totop) {
	// Backup and delete element from parent array
	$toparr = array($totop => $arr[$totop]);
	unset($arr[$totop]);
	// Merge the two arrays together so our widget is at the beginning
	return array_merge( $toparr, $arr );
}
endif;
/**
 * ProGo Site Settings Options defaults
 * @since BookIt 1.0
 */
function progo_options_defaults() {
	// Define default option settings
	$tmp = get_option( 'progo_options' );
    if ( !is_array( $tmp ) ) {
		$def = array(
			"logo" => "",
			"favicon" => "",
			"blogname" => get_option( 'blogname' ),
			"blogdescription" => get_option( 'blogdescription' ),
			"credentials" => "",
			"companyinfo" => "We sincerely thank you for your patronage.\nThe Our Company Staff\n\nOur Company, Inc.\n1234 Address St\nSuite 43\nSan Diego, CA 92107\n619-555-5555",
			"checkout" => "Please review your order"
		);
		update_option( 'progo_options', $def );
	}
	
	update_option( 'progo_bookit_installed', true );
	update_option( 'progo_bookit_apikey', '' );
	update_option( 'progo_bookit_apiauth', 'new' );
	
	update_option( 'wpsc_ignore_theme', true );
	
	// set large image size
	update_option( 'large_size_w', 584 );
	update_option( 'large_size_h', 354 );
	// set embed size
	update_option( 'embed_size_w', 584 );
	update_option( 'embed_size_h', 354 );
}

if ( ! function_exists( 'progo_options_validate' ) ):
/**
 * ProGo Site Settings Options validation function
 * from register_setting( 'progo_options', 'progo_options', 'progo_options_validate' );
 * in progo_admin_init()
 * also handles uploading of custom Site Logo
 * @param $input options to validate
 * @return $input after validation has taken place
 * @since BookIt 1.0
 */
function progo_options_validate( $input ) {
	// do validation here...
	$arr = array( 'blogname', 'blogdescription', 'checkout', 'apikey', 'companyinfo' );
	foreach ( $arr as $opt ) {
		$input[$opt] = wp_kses( $input[$opt], array() );
	}
	
	// save blogname & blogdescription to other options as well
	$arr = array( 'blogname', 'blogdescription' );
	foreach ( $arr as $opt ) {
		if ( $input[$opt] != get_option( $opt ) ) {
			update_option( $opt, $input[$opt] );
		}
	}
	
	// store API KEY in its own option
	if ( $input['apikey'] != get_option( 'progo_bookit_apikey' ) ) {
		update_option( 'progo_bookit_apikey', substr( $input['apikey'], 0, 39 ) );
	}
	unset( $input['apikey'] );
	
	// check SUPPORT field & set option['support_email'] flag if we have an email
	$input['support_email'] = is_email( $input['support'] );
	update_option('progo_settings_just_saved',1);
	
	return $input;
}
endif;

/********* more helper functions *********/

/**
 * outputs HTML for "API Key" field on Site Settings page
 * @since BookIt 1.0
 */
function progo_field_apikey() {
	$opt = get_option( 'progo_bookit_apikey', true );
	echo '<input id="apikey" name="progo_options[apikey]" class="regular-text" type="text" value="'. esc_html( $opt ) .'" maxlength="39" />';
	$apiauth = get_option( 'progo_bookit_apiauth', true );
	switch($apiauth) {
		case 100:
			echo ' <img src="'. get_bloginfo('template_url') .'/images/check.jpg" alt="aok" class="kcheck" />';
			break;
		default:
			echo ' <img src="'. get_bloginfo('template_url') .'/images/x.jpg" alt="X" class="kcheck" title="'. $apiauth .'" />';
			break;
	}
	echo '<br /><span class="description">You API Key was sent via email when you purchased the BookIt theme from ProGo Themes.</span>';
}

if ( ! function_exists( 'progo_field_blogname' ) ):
/**
 * outputs HTML for "Site Name" field on Site Settings page
 * @since BookIt 1.0
 */
function progo_field_blogname() {
	$opt = get_option( 'blogname' );
	echo '<input id="blogname" name="progo_options[blogname]" class="regular-text" type="text" value="'. esc_html( $opt ) .'" />';
}
endif;
if ( ! function_exists( 'progo_field_blogdesc' ) ):
/**
 * outputs HTML for "Slogan" field on Site Settings page
 * @since BookIt 1.0
 */
function progo_field_blogdesc() {
	$opt = get_option( 'blogdescription' ); ?>
<input id="blogdescription" name="progo_options[blogdescription]" class="regular-text" type="text" value="<?php esc_html_e( $opt ); ?>" />
<?php }
endif;
if ( ! function_exists( 'progo_field_compinf' ) ):
/**
 * outputs HTML for "Company Info" field on Site Settings page
 * @since BookIt 1.0
 */
function progo_field_compinf() {
	$options = get_option( 'progo_options' ); ?>
<textarea id="progo_companyinfo" name="progo_options[companyinfo]" style="width: 95%;" rows="5"><?php esc_html_e( $options['companyinfo'] ); ?></textarea><br />
<span class="description">This text appears at the end of Transaction Results pages and email receipts.</span>
<?php }
endif;
if ( ! function_exists( 'progo_field_checkout' ) ):
/**
 * outputs HTML for "Checkout Headline" field on Site Settings page
 * @since BookIt 1.0
 */
function progo_field_checkout() {
	$options = get_option( 'progo_options' );
	?>
<input id="progo_checkout" name="progo_options[checkout]" value="<?php esc_html_e( $options['checkout'] ); ?>" class="regular-text" type="text" />
<span class="description">Headline at the top of the <a href="../products-page/checkout/">Checkout</a> page</span>
<?php }
endif;
if ( ! function_exists( 'progo_section_text' ) ):
/**
 * (dummy) function called by 
 * add_settings_section( 'progo_theme', 'Theme Customization', 'progo_section_text', 'progo_site_settings' );
 * and
 * add_settings_section( 'progo_info', 'Site Info', 'progo_section_text', 'progo_site_settings' );
 * @since BookIt 1.0
 */
function progo_section_text() {
	// echo '<p>intro text...</p>';	
}
endif;
if ( ! function_exists( 'progo_set_default_body' ) ):
/**
 * hooked to 'default_content' by add_filter in progo_setup()
 * adds default bullet point copy to BODY field for new PRODUCTS
 * @since BookIt 1.0
 */
function progo_set_default_body( $content ) {
	global $post_type;
	if ( $post_type == 'wpsc-product' ) {
		$default_line = "Add a 1-2 Line Benefit Point About Your Product";
		$content = "<ul>";
		for ( $i=0; $i<3; $i++ ) {
			$content .="
	<li>". $default_line ."</li>";
		}
		$content .= "
</ul>";
	}
	return $content;
}
endif;

/**
 * hooked to 'admin_notices' by add_action in progo_setup()
 * used to display "Settings updated" message after Site Settings page has been saved
 * @uses get_option() To check if our Site Settings were just saved.
 * @uses update_option() To save the setting to only show the message once.
 * @since BookIt 1.0
 */
function progo_admin_notices() {	
	// api auth check
	$apiauth = get_option( 'progo_bookit_apiauth', true );
	if( $apiauth != '100' ) {
	?>
	<div id="message" class="error">
		<p><?php
        switch($apiauth) {
			case 'new':	// key has not been entered yet
				echo '<a href="admin.php?page=progo_site_settings" title="Site Settings">Please enter your ProGo Themes API Key to Activate your theme.</a>';
				break;
			case '999': // invalid key?
				echo 'Your ProGo Themes API Key appears to be invalid. <a href="admin.php?page=progo_site_settings" title="Site Settings">Please double check it.</a>';
				break;
			case '300': // wrong site URL?
				echo '<a href="admin.php?page=progo_site_settings" title="Site Settings">The ProGo Themes API Key you entered</a> is already bound to another URL.';
				break;
		}
		?></p>
	</div>
<?php
	}
	
	if( get_option('progo_settings_just_saved')==true ) {
	?>
	<div id="message" class="updated fade">
		<p>Settings updated. <a href="<?php bloginfo('url'); ?>/">View site</a></p>
	</div>
<?php
		update_option('progo_settings_just_saved',false);
	}
}

/**
 * hooked to 'site_transient_update_themes' by add_filter in progo_setup()
 * checks ProGo-specific URL to see if our theme is up to date!
 * @param array of checked Themes
 * @uses get_allowed_themes() To retrieve list of all installed themes.
 * @uses wp_remote_post() To check remote URL for updates.
 * @return checked data array
 * @since BookIt 1.0
 */
function progo_update_check($data) {
	if ( is_admin() == false ) {
		return $data;
	}
	
	$themes = get_allowed_themes();
	
	if ( isset( $data->checked ) == false ) {
		$checked = array();
		// fill CHECKED array - not sure if this is necessary for all but doesnt take a long time?
		foreach ( $themes as $thm ) {
			// we don't care to check CHILD themes
			if( $thm['Parent Theme'] == '') {
				$checked[$thm[Template]] = $thm[Version];
			}
		}
		$data->checked = $checked;
	}
	if ( isset( $data->response ) == false ) {
		$data->response = array();
	}
	
	$request = array(
		'slug' => "bookit",
		'version' => $data->checked[bookit],
		'siteurl' => get_bloginfo('url')
	);
	
	// Start checking for an update
	global $wp_version;
	$apikey = get_option('progo_bookit_apikey',true);
	if ( $apikey != '' ) {
		$apikey = substr( strtolower( str_replace( '-', '', $apikey ) ), 0, 32);
	}
	$checkplz = array(
		'body' => array(
			'action' => 'theme_update', 
			'request' => serialize($request),
			'api-key' => $apikey
		),
		'user-agent' => 'WordPress/'. $wp_version .'; '. get_bloginfo('url')
	);

	$raw_response = wp_remote_post('http://www.progo.com/updatecheck/', $checkplz);
	
	if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
		$response = unserialize($raw_response['body']);
		
	if ( !empty( $response ) ) {
		// got response back. check authcode
		//wp_die('response:<br /><pre>'. print_r($response,true) .'</pre><br /><br />apikey: '. $apikey );
		// only save AUTHCODE if APIKEY is not blank.
		if ( $apikey != '' ) {
			update_option( 'progo_bookit_apiauth', $response[authcode] );
		} else {
			update_option( 'progo_bookit_apiauth', 'new' );
		}
		if ( version_compare($data->checked[bookit], $response[new_version], '<') ) {
			$data->response[bookit] = array(
				'new_version' => $response[new_version],
				'url' => $response[url],
				'package' => $response[package]
			);
		}
	}
	
	return $data;
}

function progo_to_twentyten() {
	$msg = 'This ProGo Themes site is currently not Activated.';
	
	if(current_user_can('edit_pages')) {
		$msg .= '<br /><br /><a href="'. trailingslashit(get_bloginfo('url')) .'wp-admin/admin.php?page=progo_site_settings">Click here to update your API Key</a>';
	}
	wp_die($msg);
}

if ( ! function_exists( 'progo_product_image_forms' ) ):
/**
 * html for WPSC product images meta box
 * @since BookIt 1.0
 */
function progo_product_image_forms() {

    global $post;
    
    edit_multiple_image_gallery( $post );

	$tab = has_post_thumbnail($post->ID) ? 'gallery' : 'type';
    ?>
    <p><strong <?php if ( isset( $display ) ) echo $display; ?>><a href="media-upload.php?parent_page=wpsc-edit-products&post_id=<?php echo $post->ID; ?>&type=image&tab=<?php echo esc_attr($tab); ?>&TB_iframe=1&width=640&height=566" class="thickbox" title="Manage Your Product Images"><?php _e( 'Manage Product Images', 'wpsc' ); ?></a></strong></p>
<?php
}
endif;

function progo_bookit_init() {	
	// add "Family" Custom Post Type
	register_post_type( 'progo_family',
		array(
			'labels' => array(
				'name' => 'Family Members',
				'singular_name' => 'Member',
				'add_new_item' => 'Add New Member',
				'edit_item' => 'Edit Member',
				'new_item' => 'New Member',
				'view_item' => 'View Member',
				'search_items' => 'Search Members',
				'not_found' =>  'No family members found',
				'not_found_in_trash' => 'No family members found in Trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Family'
			),
			'public' => true,
			'public_queryable' => true,
			'exclude_from_search' => true,
			'show_in_menu' => true,
			'menu_position' => 10,
			'hierarchical' => false,
			'supports' => array('title','editor','thumbnail','revisions','page-attributes')
		)
	);
}
add_action( 'init', 'progo_bookit_init' );

// [family title="Meet"]
function progo_bookit_family_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'title' => "Meet the Family"
	), $atts ) );
	
	$oot = '<div class="block family inmain"><h3 class="title"><span class="spacer">'. $title .'</span></h3><div class="inside">';
	//echo "<p>random $num testimonial here...</p>";
	$args = array('post_type' => 'progo_family', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' );
	$fam = get_posts($args);
	foreach($fam as $t) {
		$oot .= '<a href="'. get_bloginfo('url') .'/family/#'. $t->post_name .'" class="thm">'. get_the_post_thumbnail($t->ID) . $t->post_title .'</a>';
	}
	$oot .= '</div><div class="e"></div></div>';
	return $oot;
}
add_shortcode( 'family', 'progo_bookit_family_shortcode' );

function progo_bodyclasses($classes) {
	if(is_archive() || is_single() || is_page(99)) $classes[] = 'blog';
	return $classes;
}