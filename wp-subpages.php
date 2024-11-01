<?php
// From plugin Easily navigate pages on dashboard
function wp_cp_subpagetree_maketree($pages, $public = false) {
	// Split into messy array
	$pageAr = explode("\n", $pages);

	foreach($pageAr AS $txt) {

		$out = "";

		$re1='.*?';
		$re2='(\\d+)';

		if ($c=preg_match_all ("/".$re1.$re2."/is", $txt, $matches))
		{ 
			$int1=$matches[1][0];

			$pageID = $int1;

			// Get post status (publish|pending|draft|private|future)
			$thisPage = get_page($pageID);
			$pageStatus = $thisPage -> post_status;
			$pageURL = get_permalink($pageID);

			if ($pageStatus != "publish") {
				$pageStatus = "strikethrough";
			}

			// Get page title
			$pageTitle = trim(strip_tags($txt));

			// Make sure we don't display empty page titles
			if ($pageTitle == "") $pageTitle = __("(no title)", "page-tree");

			$linesAr[$pageID] = $pageTitle;
			if (stristr($txt, "<li class")) { // This is a line with beginning LI
				$out .= "<li>";
			}

			if ($public) {
				// Create our own link to edit page for this ID
				//$out .= "<a class=\"$pageStatus\" href=\"$pageURL\">" . $pageTitle . "</a>";
				$out .= "<a class=\"expand\" style='cursor: pointer;'>&nbsp;</a><a class=\"$pageStatus pagelink\" href=\"$pageURL\">" . $pageTitle . "</a>";
				//$out .= "<a class=\"$pageStatus\" href=\"#\">" . $pageTitle . "</a>";
			}
			else {
				$out .= "<a class=\"$pageStatus\" href=\"$pageURL\">" . $pageTitle . "</a>";
			}

			if (stristr($txt, "</li>")) { // This is a line with an ending LI
				$out .= "</li>";
			}

			$outAr[] = $out;


		}
		else { // This is a line with something else than a page (<ul>, </ul>, etc) - just add it to the pile
			$outAr[] = $txt;
		}

		// Keep all lines in $origAr just in case we want to check things again in the future
		$origAr[] = $txt;

	}

	// Print the new, pretty UL-LI by joining the array
	return join("\n", $outAr);
}


function wp_cp_subpages($content){
  global $post;
  global $wpdb;

  $args = array(
		"echo" => 0,
		"title_li" => "", 
		"link_before" => "", 
		"link_after" => "",
		'child_of'     => $post->ID,
		"sort_column" => "menu_order"
	);
	$pages = wp_list_pages($args);
  $subpages_tree = wp_cp_subpagetree_maketree($pages, true);
  if($subpages_tree != ""){
    $subpages_tree = "<div id=\"easily-navigate-pages-on-dashboard\"><ul><h2>".$post->post_title.":</h2>".$subpages_tree."</ul><div style=\"clear: right; height: 1px; overflow:hidden;\">&nbsp;</div></div>";
    } 
  
  $same_level_pages = wp_cp_other_same_level_pages($post->ID);
  $content = $same_level_pages.$content;
  
  $parent_pages = wp_cp_parent_pages($post->ID);
  $content = $parent_pages.$content;
  
  $content = wp_cp_taxonomies_replace($post->ID,$content);
  /*$subpages = wp_cp_subpages_array($post->ID);
  if($subpages != ""){
  
  $taxonomies = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."cp_group_row` WHERE `type` = 'select' OR `type` = 'checkbox'", ARRAY_A);
  //print_r($taxonomies);
  array_push($taxonomies, array('name' => 'Typ')); 
   
  if(isset($_POST["show"])){
    
    extract($_POST);
    $table_header = "";
    for($i=0;$i<count($taxonomies);$i++){
      if($show_taxonomies[$i]){
        $checked[$i] = "CHECKED";
        $table_header .= "<td style='border: 1px solid black; text-align: center;'>".$taxonomies[$i]["name"]."</td>";
        $table_taxonomies[] = wp_cp_no_diacritic($taxonomies[$i]["name"]);
        }
      else{
        $checked[$i] = "";
        }
      }
    }
  else{
    for($i=0;$i<count($taxonomies);$i++){
      $checked[$i] = "CHECKED";
      $table_header .= "<td style='border: 1px solid black; text-align: center;'>".$taxonomies[$i]["name"]."</td>";
      $table_taxonomies[] = wp_cp_no_diacritic($taxonomies[$i]["name"]);
      }
    }
  
  $page_list ="<form method='post'><div><h2>".__("Set atributes", 'wp-custom-pages').":</h2>";
  for($i=0;$i<count($taxonomies);$i++){
    $taxonomy = $taxonomies[$i]; 
    $page_list .= "<span style='padding: 2px 5px 2px 5px; margin-right: -1px; border: 1px solid black;'>$taxonomy[name]: <input type='checkbox' name='show_taxonomies[".$i."] value='$taxonomy[name]' $checked[$i]></span>";
    }
    
  $page_list .= "<span style='margin: 5px'><input type='submit' value='".__('Show', 'wp-custom-pages')."' name='show'></span></div></form>";  
  
  
               
  
  $level_max = 5;
  for($i=0;$i<count($subpages);$i++){
    $subpage = $subpages[$i];
    if($level_max < $subpage["post_level"]){
      $level_max = $subpage["post_level"]+1;
      }
    }
  //print_r($subpages);
  //echo "<br><br>";
  
  $page_list .= "<table style='color: black;' style='border-color: black;'><tr><td style='border: 1px solid black; text-align: center;'>".__('Title')."</td>$table_header</tr>";
  
  //echo $level_max;
  
  
  
  for($i=0;$i<count($subpages);$i++){
    $subpage = $subpages[$i];
    $level = $subpage["post_level"]+1;
    
    $h = $level*359/$level_max;
    $h = ceil($h);
    $h = min($h,359);
        
    $bg = "#".Color::hsv2hex($h, 55, 100);
    
    $separator = "";
    for($jk=0;$jk<$subpage["post_level"];$jk++){
      $separator .= "-";
      }               
                                              
    $padding_left = $level*15;       
    $page_list .= "<tr style='background-color: $bg;'><td style=' border: 1px solid black; text-align: left; padding: 2px;'><div style='padding-left: $padding_left"."px;'><li style='list-style-type: square;'><a href='$subpage[post_permalink]'>$subpage[post_title]</a></li></div></td>";
    
    //print_r($table_taxonomies);
    if($table_taxonomies != ""){
      foreach($table_taxonomies as $table_taxonomy){
        $taxonomy_url = get_the_term_list( $subpage["ID"], $table_taxonomy, '', ', ', '' );
        if($taxonomy_url == ""){
          $taxonomy_url = "-";
          }
        $page_list .= "<td align='center' style='border: 1px solid black'>$taxonomy_url</td>";
        }
        }
    $page_list .= "</tr>";
    }
    
  //print_r($subpages);
  //echo $post->ID;
  $page_list .= "</table>";
  }
  else{
  $page_list = "";
  }
  $content = str_replace("[wp_cp_subpages]", $page_list, $content);
  */ 
  $content = str_replace("[wp_cp_subpages]", $subpages_tree, $content);  
  return $content;
  }

function wp_cp_parent_pages($id,$string = ""){
  $post = get_post($id);
  $permalink = get_permalink($id);
  if($string == ""){$separator = "";}
  else{$separator = "&#8658";}
  
  $string = "<a href='$permalink'>".$post->post_title."</a> $separator ".$string;
  $parent_id = $post->post_parent;
  
  if($parent_id != 0){
    $string = wp_cp_parent_pages($parent_id,$string);
    
    }
  if(strpos($string, '&#8658') != false){
    return $string;
    }
  }
             
function wp_cp_subpages_array($id,$level = 0,$pole = ""){
  global $post;
  $args = array(
    'numberposts'     => 0,
    'offset'          => 0,
    'orderby'         => 'post_date',
    'order'           => 'ASC',
    'post_type'       => 'page',
    'post_parent'     => $id,
    'post_status'     => 'publish' );  
  $subpages = get_posts( $args );
  
  foreach($subpages as $subpage){
    //print_r($subpage);
    $subpage = get_object_vars($subpage);
    $permalink = get_permalink( $subpage["ID"] );
    $typ = get_the_term_list( $subpage["ID"], 'typ', '', ', ', '' );
    //***************** YOU CAN ADD ANY COLUM FROM DATABASE *******************
    $list_content["ID"] = $subpage["ID"];
    $list_content["post_level"] = $level;
    $list_content["post_title"] = $subpage["post_title"];
    $list_content["post_author"] = $subpage["post_author"];
    $list_content["post_content"] = $subpage["post_content"];
    $list_content["post_typ"] = $typ;
    $list_content["post_permalink"] = $permalink;
    
    $pole[] = $list_content;
    $pole = wp_cp_subpages_array($subpage["ID"],$level+1,$pole);
    } 
  return $pole;
  }

function wp_cp_other_same_level_pages($id){
  $post = get_post($id);
  $parent_id = $post->post_parent;
  
  global $post;
  $args = array(
    'numberposts'     => 50,
    'offset'          => 0,
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'post_type'       => 'page',
    'post_parent'     => $parent_id,
    'post_status'     => 'publish' );  
  $same_level_pages = get_posts( $args );
  
  if($same_level_pages != "" && $parent_id != 0){
    $content = "<br /><div style='float: right; padding: 15px; border: 1px dotted black; -moz-border-radius: 8px; border-radius: 8px; margin: 29px 5px 5px 5px; max-width:150px;'>".__('In this category', 'wp-custom-pages').":<ul>";
    foreach($same_level_pages as $same_level_page){
      $same_level_page = get_object_vars($same_level_page);
      $permalink = get_permalink( $same_level_page["ID"] );
      
      $content .= "<li style='li-style: squer;'><a href='$permalink'>".$same_level_page["post_title"]."</a></li>";
      }
    $content .= "</ul></div>";
    }
  return $content;
  }
  
function wp_cp_taxonomies_replace($id,$content){
  preg_match_all("|\[get_taxonomy=(.*?)\]|", $content, $matches, PREG_SET_ORDER);


  foreach($matches as $match){
    $match = $match[1];
    $taxonomy = get_the_term_list( $id, $match, '', ', ', '' );

    $content = str_replace("[get_taxonomy=$match]", $taxonomy, $content);
    }

  $content .= "<br><br>";
  $content .= get_the_term_list( $id, 'typ', '<strong>Typ:</strong> ', ', ', '' );
  $content .= "<br>";
  $content .= get_the_term_list( $id, 'filter', '<strong>Filter:</strong> ', ', ', '' );
  return $content;
  }
?>