<?php
/*
Plugin Name: WP Custom Pages
Plugin URI:
Description: Plugin for pages with some default structure with custom taxonomies. Page with subpages transform to one page with contetn.
Version: 0.5.0.2
Author: Stanislav Gunčaga
Author URI: http:www.artificium.sk
License: GPL2
*/
require("wp-functions.php");
require("wp-subpages.php");
//require("wp-colors.php");

add_action( 'init', 'wp_cp_init', 0 );
add_action('admin_menu', 'wp_cp_menu');
add_action('admin_init', 'wp_cp_style');
add_action('admin_init', 'wp_cp_requires');

register_activation_hook( __FILE__, 'wp_cp_activate' );
register_deactivation_hook( __FILE__, 'wp_cp_deactivate' );

add_filter( "the_content", "wp_cp_subpages" );
if($_GET["page"] == "wp_custom_pages"){add_filter('admin_head','myplugin_tinymce');}

function wp_cp_requires(){
  global $wpdb;
  $url = get_site_url();
  //***************************** CHECK IF EXIST  NEXTGEN GALLERY ****************
  if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."ngg_album'") == "") {
    echo "<div class='error'><a href='$url/wp-admin/plugin-install.php?tab=search&type=term&s=nextgen+gallery'>Nextgen Gallery</a>".__(' is required for plugin <b>WP Custom Pages</b>.')."</div>";
    }
  }
//***************************** BEGIN SUBPAGES TREE ***********************************
add_action( 'init', 'wp_cp_subpage_init' );
add_action('wp_head', 'wp_cp_subpage_head');

function wp_cp_subpage_init()
{
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('wp_cp_subpages_tree', plugins_url('js/subpages.js', __FILE__));
}

function wp_cp_subpage_head() {
  $siteurl = get_option('siteurl');
  $url_tree = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/theme/subpages.css';
  $url_cp = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/theme/wp-custom_pages_style.css';
  $javascript = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/js/subpages.js';
	echo "<link rel='stylesheet' type='text/css' href='$url_tree' />\n";
  echo "<link rel='stylesheet' type='text/css' href='$url_cp' />\n";
  wp_enqueue_script('my-script', '$url', array('jquery'), '1.0');
  }

//***************************** END SUBPAGES TREE ***********************************
function myplugin_tinymce(){
  wp_enqueue_script('common');
  wp_enqueue_script('jquery-color');
  wp_admin_css('thickbox');
  wp_print_scripts('post');
  wp_print_scripts('media-upload');
  wp_print_scripts('jquery');
  wp_print_scripts('jquery-ui-core');
  wp_print_scripts('jquery-ui-tabs');
  wp_print_scripts('tiny_mce');
  wp_print_scripts('editor');
  wp_print_scripts('editor-functions');
  add_thickbox();
  wp_tiny_mce();
  wp_admin_css();
  wp_enqueue_script('utils');
  do_action("admin_print_styles-post-php");
  do_action('admin_print_styles');
  remove_all_filters('mce_external_plugins');
  }

function wp_cp_style(){
	wp_register_style("wp_cp_style", WP_PLUGIN_URL."/wp-archeology/theme/style.css");
	wp_enqueue_style("plugin_style",WP_PLUGIN_URL."/wp-custom-pages/theme/style.css");
  }

//******************** ADD ADMIN MENU ********************************
function wp_cp_menu(){
  add_menu_page(get_option("wp_cp_plugin_name"), get_option("wp_cp_plugin_name"), 'manage_options', 'wp_custom_pages', 'wp_cp_add_new', '', 21);
  add_submenu_page( 'wp_custom_pages', __('Articles', 'wp-custom-pages'), __('Articles', 'wp-custom-pages'), 'manage_options', 'wp_custom_pages', 'wp_cp_add_new' );
  add_submenu_page( 'wp_custom_pages', __('Groups', 'wp-custom-pages'), __('Groups', 'wp-custom-pages'), 'manage_options', 'wp_cp_groups', 'wp_cp_groups' );
  add_submenu_page( 'wp_custom_pages', __('Settings', 'wp-custom-pages'), __('Settings', 'wp-custom-pages'), 'manage_options', 'wp_cp_settings', 'wp_cp_settings' );
  add_submenu_page( 'wp_custom_pages', __('Generate', 'wp-custom-pages'), __('Generate', 'wp-custom-pages'), 'manage_options', 'wp_cp_generate', 'wp_cp_generate' );
  }

//****************************** ADDING GROUPS ****************************
function wp_cp_groups(){
  include("wp-groups.php");
  include("theme/wp-groups-theme.php");
  }

//******************************* ADDING PAGES ****************************
function wp_cp_add_new(){   
  include("wp-article.php");
  include("theme/wp-article-theme.php");
  }

//******************************** SETTINGS *******************************
function wp_cp_settings(){   
  include("wp-settings.php");
  }

//******************************* GENERATE SUPER PAGE *********************
function wp_cp_generate(){   
  include("wp-generate.php");
  }

function wp_cp_activate(){
  //****************** ADD OPTIONS ****************************************
  
  add_option("wp_cp_dynamic_style", 1);
  add_option("wp_cp_styles_text", "color: black; font-size: 1.2em; font-weight: bold; display: inline;");
  add_option("wp_cp_styles_textarea", "color: black; font-size: 1.2em; font-weight: bold; display: block;");
  add_option("wp_cp_styles_select", "color: black; font-size: 1.2em; font-weight: bold; display: inline;");
  add_option("wp_cp_styles_checkbox", "color: black; font-size: 1.2em; font-weight: bold; display: inline;");
  add_option("wp_cp_styles_picture", "color: black; font-size: 1.2em; font-weight: bold; display: block;");
  
  add_option("wp_cp_default_visibility", "publish");
  add_option("wp_cp_default_status", "draft");
  add_option("wp_cp_default_comments", "open");
  add_option("wp_cp_default_password", "");

  add_option("wp_cp_recomanded_plugin", 1);
  
  add_option("wp_cp_plugin_name", "Custom Pages");
  

  /************
  Create Tables
  ************/

  //****************** GROUP NAME *****************************
  $sql = "(
    `gid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255)
    );";
       
  wp_cp_create_table("cp_group_name",$sql);
  //****************** GROUP ROW *****************************
  $sql = "(
    `rid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255),
    `type` VARCHAR(255),
    `order` VARCHAR(255)
    );";  
    
  wp_cp_create_table("cp_group_row",$sql);
  
  //****************** GROUP JOIN *****************************
  $sql = "(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `group_id` INT,
    `row_id` INT
    );";
  
  wp_cp_create_table("cp_group_join",$sql);
  
  //****************** SELECT ROW *****************************
  $sql = "(
    `sid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255)
    );";
  
  wp_cp_create_table("cp_select_row",$sql);
  //****************** SELECT JOIN *****************************
  $sql = "(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name_id` INT,
    `row_id` INT
    );";
  
  wp_cp_create_table("cp_select_join",$sql);

  //****************** SAVE ARTICLES *****************************
  $sql = "(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT,
    `post` LONGTEXT
    );";

  wp_cp_create_table("cp_posts",$sql);
  
  //****************** Checkbox values *****************************
  $sql = "(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `checkbox_id` INT,
    `yes_value` VARCHAR(255),
    `no_value` VARCHAR(255)
    );";

  wp_cp_create_table("cp_checkbox_values",$sql);
  }
  
  
  
//********************** DELETE OPTIONS *********************************
function wp_cp_deactivate(){
  delete_option("wp_cp_styles_text");
  delete_option("wp_cp_styles_textarea");
  delete_option("wp_cp_styles_select");
  delete_option("wp_cp_styles_checkbox");
  delete_option("wp_cp_styles_picture");

  delete_option("wp_cp_default_visibility");
  delete_option("wp_cp_default_status");
  delete_option("wp_cp_default_comments");
  delete_option("wp_cp_default_password");
  }

//************************ ADDING TAXONOMIES ******************************
function wp_cp_init(){
  load_plugin_textdomain( 'wp-custom-pages', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

  global $wpdb;
  
  $taxonomies = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."cp_group_row` WHERE `type` = 'select' OR `type` = 'checkbox'", ARRAY_A);
  foreach($taxonomies as $taxonomy){
    register_taxonomy(wp_cp_no_diacritic($taxonomy["name"]), 'page', array('label' => "$taxonomy[name]", 'sort' => true, 'args' => array('orderby' => 'term_order'), 'rewrite' => array('slug' => wp_cp_no_diacritic($taxonomy["name"]))));
    }
       
  register_taxonomy('typ', 'page', array('label' => 'Typ', 'sort' => true, 'args' => array('orderby' => 'term_order'), 'rewrite' => array('slug' => 'typ')));
                       
  register_taxonomy('filter', 'page', array('label' => 'Filter', 'sort' => true, 'args' => array('orderby' => 'term_order'), 'rewrite' => array('slug' => 'filter')));
  }
    
?>