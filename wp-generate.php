<?php
global $post;
if(isset($_POST["generate"])){
  $id =  $_POST["id"];
  $content = "";  
  
  $register = "<h2 id='top'>".__('Content').":</h2>";
  
  $post_parent = get_post($id);
  $title = $post_parent->post_title;
  $typ = get_the_term_list( $id, 'typ', '', ', ', '' );
  
  $content = $post_parent->post_content;
  
  $content = preg_replace("|<!-- BEGIN: register -->(.*?)<!-- END: register -->|", "", $content);
  $content = str_replace("[wp_cp_subpages]","",$content);
    
  $content = "<p id='1'><h2>1. $title ($typ)</h2>$content</p>";
  
  $register .= "<ul>";
  $register .= "<li style='list-style-type: none;'>1. <a href='#1'>$title</a> ($typ)</li>";
  
  
  $html_content = $content;
  
  preg_match_all("|\[nggallery id=([\d])+\]|", $html_content, $matches, PREG_SET_ORDER);
    foreach($matches as $match){
      $gid = $match[1];
      $gallery = wp_cp_gallery($gid);
      $html_content = preg_replace("|(\[nggallery id=)([\d]+)(\])|", $gallery, $html_content);
      }
    
    preg_match_all("|\[singlepic id=([\d]+) (.*?)\]|",$html_content, $matches, PREG_SET_ORDER);
    foreach($matches as $match){
      $pid = $match[1];
      $picture = wp_cp_picture($pid);
      $html_content = preg_replace("|\[singlepic id=$pid (.*?)\]|", $picture, $html_content);
      }
  //********************** subpages ****************************

  $subpages = wp_cp_subpages_array($id);

  $capitol_number[0] = 1;
  for($i=0;$i<count($subpages);$i++){
    $subpage = $subpages[$i];

    $level_this_page = $subpage["post_level"];
    if($i != 0) $level_previous_page = $subpages[$i-1]["post_level"];
    else $level_previous_page = 0;

    if($level_this_page > $level_previous_page) $register .= "<ul style='padding-left: 30px;'>";

    for($j=0;$j<($level_previous_page-$level_this_page);$j++){
        $register .= "</ul>";
        }

    $level = $subpage["post_level"];

    for($j=0;$j<=count($capitol_number);$j++){
      if($j > $level){
        unset($capitol_number[$j]);
        }
      }
    $capitol_number[$level]++; 
    $capitol = implode(".",$capitol_number);
    
    $page_content = "<p id='$capitol'>";
    $page_content .= "<h2>$capitol. $subpage[post_title] ($subpage[post_typ]) (<a href='#top'>top</a>)</h2>";

    $page_content .= preg_replace("|<!-- BEGIN: register -->(.*?)<!-- END: register -->|", "", $subpage["post_content"]);
    $page_content = str_replace("[wp_cp_subpages]","",$page_content);
    $page_content = wp_cp_taxonomies_replace($subpage["ID"],$page_content);
   
    $html_page_content = $page_content;
   
    preg_match_all("|\[nggallery id=([\d])+\]|", $html_page_content, $matches, PREG_SET_ORDER);
    foreach($matches as $match){
      $id = $match[1];
      $gallery = wp_cp_gallery($id);
      $html_page_content = preg_replace("|(\[nggallery id=)([\d]+)(\])|", $gallery, $html_page_content);
      }
    
    preg_match_all("|\[singlepic id=([\d]+) (.*?)\]|", $html_page_content, $matches, PREG_SET_ORDER);
    foreach($matches as $match){
      $id = $match[1];
      $picture = wp_cp_picture($id);
      $html_page_content = preg_replace("|\[singlepic id=$id (.*?)\]|", $picture, $html_page_content);
      }
    
    $content .= $page_content;
    $content .= "</p>";
    
    $html_content .= $html_page_content;
    $html_content .= "</p>";
    
    $register .= "<li style='list-style-type: none;'>$capitol. <a href='#"."$capitol'>$subpage[post_title]</a> ($subpage[post_typ])</li>";
    
    }

  for($j=0;$j<($level_previous_page-0);$j++){
        $register .= "</ul>";
        }

    
  $register .= "</ul>";
  
  $content = $register."<br>".$content;
  $html_content = $register."<br>".$html_content;

  $wp_cp_post = array(
    'post_status' => 'publish', 
    'post_type' => 'post',
    'post_parent' => 0,
    'post_title' => $title,
    'post_content' => $content
    );  
    
    
  $site_url = get_site_url();
  $header = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
  <head>
  <meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\">
  <meta name=\"generator\" content=\"PSPad editor, www.pspad.com\">
  <link rel=\"stylesheet\" type=\"text/css\" href=\"$url/wp-content/plugins/wp-custom-pages/theme/wp-custom_pages_style.css\">
  <title>$title</title>
  </head>
  <body>
  ";
  
  $footer = "</body></html>"; 
  
  if($_POST["post"]){
    $id = wp_insert_post($wp_cp_post);
    $permalink = get_permalink( $id );
    $title_slug = explode("/",$permalink);
    $title_slug = $title_slug[count($title_slug)-2];

    echo "<div class='green'>".__('Post','wp-custom-pages')." <a href='$permalink'><b>$title</b></a> ".__('was saved succesfully','wp-custom-pages')."</div>";
    }
  else{
    $title_slug = wp_cp_no_diacritic($title);
    }

  if($_POST["html"]){
    $url = get_site_url();
    $file = fopen("../wp-content/plugins/wp-custom-pages/html/$title_slug".".html","w+");
    fwrite($file,$header.$html_content.$footer);
    fclose($file);
    
    echo "<div class='blue'>".__('Page', 'wp-custom-pages')." <a href='$url/wp-content/plugins/wp-custom-pages/html/$title_slug".".html'><b>$title</b></a> ".__('was saved as html','wp-custom-pages').".</div>";
    }
  
  //echo "<div class='green'>".__("Page was generated successfully", 'wp-custom-pages')."</div>";

  }
  
function wp_cp_gallery($id){
  global $wpdb;
  
  $pictures = $wpdb->get_results( "SELECT filename FROM ".$wpdb->prefix."ngg_pictures WHERE `galleryid` = $id" );
  $gallery =  $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."ngg_gallery WHERE `gid` = $id" );
  $gallery = $gallery[0];
  $gallery_slug = $gallery->slug;

  $content = "<p><div>";
  foreach($pictures as $picture){
    $filename = $picture->filename;
    
    $content .= "<img src='$site_url/wp-content/gallery/$gallery_slug/$filename' width='290' style='padding: 5px; margin: 5px; border: 1px solid gray;'>";
    }
  $content .= "</div><div style='clear: both;'></div></p>";
  return $content;
  }
            
function wp_cp_picture($id){
  global $wpdb;
  
  $picture = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."ngg_pictures WHERE `pid` = $id" );
  $filename = $picture->filename;
  $gid = $picture-gid;
  $gallery =  $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."ngg_gallery WHERE `gid` = $gid" );
  $gallery_slug = $gallery->slug;
  
  $site_url = get_site_url();
  $content = "<div style='text-align: center'><img src='$site_url/wp-content/gallery/$gallery_slug/$filename' width='600'style='padding: 5px; margin: 5px; border: 1px solid gray;></div><div style='clear: both;'></div>";
  return $content;
  } 

?>
<div class="wrap" id="wp-archeology">
<div id="icon-options-general" class="icon32"><br /></div> 
<h2><?php _e('Generate one page from all subpages', 'wp-custom-pages'); ?></h2>
<form method="post">
<table>
  <tr>
    <td colspan="2"><?php echo wp_dropdown_pages(array('depth' => 0, 'child_of' => 0, 'selected' => $_POST["id"], 'echo' => 0, 'name' => 'id', 'show_option_none' => __('Select page','wp-custom-pages'))); ?></td>
  </tr>
  <tr>
    <td><?php _e('Save as post', 'wp-custom-pages'); ?>:</td>
    <td><input type='checkbox' name='post' value='1' CHECKED></td>
  </tr>
  <tr>
    <td><?php _e('Save as html', 'wp-custom-pages'); ?>:</td>
    <td><input type='checkbox' name='html' value='1' CHECKED></td>
  </tr>
  <tr>
    <td colspan="2"><?php echo submit_button( __('Generate', 'wp-custom-pages'), 'primary', 'generate',false ); ?></td>
  </tr>
</table>
</form>
</div>