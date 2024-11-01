<?php
global $wpdb;

if(isset($_POST["group_name_id"])){
  if($_POST["group_name_id"] == 'select'){
    echo "<div class='yellow'>".__('Please, select group.', 'wp-custom-pages')."</div>";
    }       
  }
  
if(isset($_POST["save"])){
  extract($_POST);
  //print_r($_POST);
  $dynamic_style = get_option("wp_cp_dynamic_style");
  
  //************************ Get text from TinyMCE ************************* 
  $content_tinymce = "<!-- BEGIN: tinymce -->".$content."<!-- END: tinymce -->";
  $content = "";
  
  //************************ title is required *****************************
  if($title == ""){
    echo "<div class=\"error\" style=\"margin-bottom: 5px;\">".__('Title is not filled.', 'wp-custom-pages')."</div>";
    $title_border = "border: 1px solid red;";
    }
  else{
    $register = "<span><!-- BEGIN: register --><div style='border: 1px dotted black; float: right; margin: 5px; padding: 10px; -moz-border-radius: 8px; border-radius: 8px;'>".__('Content', 'wp-custom-pages').":<ul>";
    $tags = "";
    $rows_name = explode(";",$rows_name);
    $i = 0;
    
    //************ walt throu all articles rows *****************************
    foreach($rows as $row){
      $row_name = wp_cp_no_diacritic($rows_name[$i]);

      //******************************* Add new select **********************
      if($row == "add_new_select%_cp_%5%9%7%5%3"){
        //********************** Get new select value ***********************
        $row = $select_new[$i];

        //********************** Look in DB if not exists ********************
        $check_select = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."cp_select_row` WHERE `name` = '$row'", ARRAY_A);
        
        //********************** If select doesnt exist add to DB ***********
        if($check_select == array()){
          $wpdb->insert( $wpdb->prefix.'cp_select_row', array( 'name' => $row ), array( '%s' ));
          //******************* Get new ID **********************************
          $select_row_id = $wpdb->insert_id; 
          $wpdb->insert( $wpdb->prefix.'cp_select_join', array( 'name_id' => $name_id[$i], 'row_id' => $select_row_id), array( '%s','%s' ));
          }   
        }
      //*********************** Adding select udes before ********************
      if($type[$i] == "select"){
        if($dynamic_style) $style = "class='wp_cp_select'";
        else $style = "style='".get_option("wp_cp_styles_select")."'";

        $taxonomy_slug = wp_cp_no_diacritic($rows_name[$i]);
        $content .= "<p id='".$rows_name[$i]."'><!-- BEGIN: $row_name --><span $style>".$rows_name[$i].": </span>[get_taxonomy=$taxonomy_slug]<!-- END: $row_name --></p>";
        
        $tags[wp_cp_no_diacritic($rows_name[$i])] = "$row";
        
        $register .= "<li style='li-style: squer'><a href='#".$rows_name[$i]."'>".$rows_name[$i]."</a></li>";
        }
      elseif($type[$i] == "checkbox"){
        if($dynamic_style) $style = "class='wp_cp_checkbox'";
        else $style = "style='".get_option("wp_cp_styles_checkbox")."'";
      
        $taxonomy_slug = wp_cp_no_diacritic($rows_name[$i]);
        
        $content .= "<p id='".$rows_name[$i]."'><!-- BEGIN: $row_name --><span $style>".$rows_name[$i].": </span>[get_taxonomy=$taxonomy_slug]<!-- END: $row_name --></p>";

        $tags[wp_cp_no_diacritic($rows_name[$i])] = $row;
        
        $register .= "<li style='li-style: squer'><a href='#".$rows_name[$i]."'>".$rows_name[$i]."</a></li>";
        }
      elseif($type[$i] == "picture"){
        if($dynamic_style) $style = "class='wp_cp_picture'";
        else $style = "style='".get_option("wp_cp_styles_picture")."'";
        
        $content .= "<p id='".$rows_name[$i]."'><!-- BEGIN: $row_name --><span $style>".$rows_name[$i].":</span><p style=\"text-align: center;\">$row<!-- END: $row_name --></p>";
        $register .= "<li style='li-style: squer'><a href='#".$rows_name[$i]."'>".$rows_name[$i]."</a></li>";
        }
      elseif($type[$i] == "text"){
        if($dynamic_style) $style = "class='wp_cp_text'";
        else $style = "style='".get_option("wp_cp_styles_text")."'";
        
        $content .= "<p id='".$rows_name[$i]."'><!-- BEGIN: $row_name --><span $style>".$rows_name[$i].": </span>$row<!-- END: $row_name --></p>";
        $register .= "<li style='li-style: squer'><a href='#".$rows_name[$i]."'>".$rows_name[$i]."</a></li>";
        }
      elseif($type[$i] == "textarea"){
        if($dynamic_style) $style = "class='wp_cp_textarea'";
        else $style = "style='".get_option("wp_cp_styles_textarea")."'";
        
        $content .= "<p id='".$rows_name[$i]."'><!-- BEGIN: $row_name --><span $style>".$rows_name[$i].":</span>$row<!-- END: $row_name --></p>";
        $register .= "<li style='li-style: squer'><a href='#".$rows_name[$i]."'>".$rows_name[$i]."</a></li>";
        }          
      $i++;              
      }
    $register .= "</ul></div><!-- END: register --></span>"; 

    $content = $register.$content;
    $content .= $content_tinymce;  
    $content .= "[wp_cp_subpages]";
    
    if(isset($tags) && $tags != ""){
      $tags_to_post = implode(",",$tags);
      }
    else{
      $tags_to_post = "";
      }
    
    
    $wp_cp_post = array(
      'post_status' => 'publish',
      'post_type' => 'page',
      'post_parent' => $parent_id,
      'post_title' => $title,
      'post_content' => $content,
      'tags_input'	=> $tags_to_post
      );
    
    $id = wp_insert_post($wp_cp_post);

    $array_to_insert = wp_cp_array_to_string($_POST);
    $array_to_insert = substr($array_to_insert, 0, -2);
    $array_to_insert .= ";";
    
    $wpdb->insert( $wpdb->prefix.'cp_posts', array( 'post_id' => $id, 'post' => $array_to_insert));
    
    //*************************** ADD TAXONOMIES *******************************
    if($tags != ""){
      foreach($tags as $tag_name => $tag_value){
        wp_set_post_terms( $id, $tag_value ,$tag_name ,false);
        wp_set_post_terms( $id, $tag_value ,'filter' ,true);
        }
      }
    
    wp_set_post_terms( $id, $group_name ,'typ' ,false);
    wp_set_post_terms( $id, $group_name ,'filter' ,true);
    
    $parent_post_filter = wp_cp_parent_pages_filter($id);
    wp_set_post_terms( $id, $parent_post_filter ,'filter' ,true);
    
    if($id == 0){
      echo "<div class=\"red\">".__('Article', 'wp-custom-pages')." <b>$title</b> ".__('was not created. Try refresh site.', 'wp-custom-pages')."</div>";
      }
    else{
      $permalink = get_permalink($id);
      echo "<div class=\"green\">".__('Article', 'wp-custom-pages')." <a href='$permalink'><b>$title</b></a> ".__('was added successfully. You can add another.', 'wp-custom-pages')."</div>";
      }
    }
  }

//********************* GET PARENT PAGES **************************************
function wp_cp_parent_pages_filter($id,$string = ""){
  $post = get_post($id);
  $permalink = get_permalink($id);
  if($string == ""){$separator = "";}
  else{$separator = ", ";}
  
  $string = $post->post_title."$separator".$string;
  $parent_id = $post->post_parent;
  if($parent_id != 0){
    $string = wp_cp_parent_pages_filter($parent_id,$string);
    }
  return $string;
  }
?>