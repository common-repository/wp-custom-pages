<?php
//************************ create mysql table **********************************
function wp_cp_create_table($name,$sql){
  global $wpdb;
  $table_name = $wpdb->prefix . $name;
  if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    $wpdb->query("CREATE TABLE $table_name $sql");
    }
  }

//*********************** GET GR
function wp_cp_get_rows($array){
  $default = array(
    'name' => 'rows',
    'class' => '',
    'disablec' => '',
    'style' => '',
    'selected' => '',
    'i' => ''
    );
  extract($default);
  extract($array);
  
  
   
  global $wpdb;
  $rows = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."cp_group_row` WHERE 1", ARRAY_A);
  
  $j = false;
  $content = "<select name=\"$name\" class=\"$class\" $style $disabled onchange=\"javascript: if(value == 'new'){document.getElementById('item_name_$i').disabled = false;document.getElementById('item_type_$i').disabled = false;}else{document.getElementById('item_name_$i').disabled = true;document.getElementById('item_type_$i').disabled = true;}\">/";
  if($rows != array()) $content .= "<option value=\"select\">".__("Select",'wp-custom-pages')."</option>";


  
  foreach ($rows as $row){
    
    //echo $row["id"]."=".$selected." ; ";
    if($row["rid"] == $selected){
      $select = "selected";
      }
     else{$select = "";}
      
    $content .= "<option value=\"$row[rid]\" $select>$row[name] ($row[type])</option>\n";
    $j = true;
    
    }
  if($selected == "new"){$select = "selected";}
  else{$select = "";}
  
  echo $content .= "<option value=\"new\" $select>".__('Add new row', 'wp-custom-pages')."</option></select>";
  return $j;  
  }

//*********************** CHECK VALUE IN ARRAY IF VAL
function wp_cp_array_collision($value,$array){
  $n = 0;
  for($i=0;$i<count($array);$i++){
    if($array[$i] == $value){
      $n++;
      }
    }
  if($n > 1){return true;}
  else{return false;}  
  }
  
function wp_cp_get_groups_name($array=array()){
  $default = array(
    'name' => 'level_name',
    'selected' => '',
    'class' => '',
    'style' => '',
    'echo' => true
    );
    
  extract($default);
  extract($array);
  
  global $wpdb;
  
  //get all group from mysql
  $rows = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."cp_group_name` WHERE 1", ARRAY_A);
  
  //print_r($rows);
  //check if is at least one group created
  if($rows == array()){
    echo "<div class=\"error\">".__('No group is created.','wp-custom-pages')."</div>";
    echo "<meta HTTP-EQUIV=\"REFRESH\" content=\"2; url=admin.php?page=wp_cp_groups\">";
    die();
    }
  
  //create select of all groups
  $content = " <select name=\"$name\"><option value='select'>".__('Select group','wp-custom-pages')."</option>";
  foreach($rows as $row){
    if($selected == $row["gid"]){
      $select = "selected";
      $input = "<input type='hidden' name='group_name' value='$row[name]'>";
      }
    else{$select = "";}
    
    $content .= "<option value=\"$row[gid]\" $select>$row[name]</option>";  
    }
  $content .= "</select> $input";
  
  if($echo){
    echo $content;
    return $rows;
    }
  else{return $content;}
  }

  
function wp_cp_show_group_rows($group_id,$array=array()){
  $default = array(
    'echo' => false,
    'title_border' => '',
    'parent_id' => ''
    );
    
  extract($default);
  extract($array);
  
  $i = 0;
  
  $content = "<table cellspacing='_cp_0'>";
  $content .= "<tr><td><b>".__('Title','wp-custom-pages').":</b></td><td><input type=\"text\" name=\"title\" class=\"text400\" style=\"$title_border\"></td></tr>
  <tr>
    <td><b>".__('Parent','wp-custom-pages').":</b></td>
    <td>".wp_dropdown_pages(array('depth' => 0, 'child_of' => 0, 'selected' => $parent_id, 'echo' => 0, 'name' => 'parent_id', 'show_option_none' => _('None')))."</td>
  </tr>
  <tr><td colspan='2'><hr></td></tr>";
  
  $rows_name = "";
  
  global $wpdb;

    $rows = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."cp_group_join` LEFT JOIN wp_cp_group_row ON wp_cp_group_join.row_id = wp_cp_group_row.rid WHERE wp_cp_group_join.group_id = $group_id ORDER BY wp_cp_group_row.order", ARRAY_A);
  
  foreach($rows as $row){
    if($rows_name == "") $rows_name .= $row["name"];
    else $rows_name .= ";".$row["name"];
    $content .= "<tr><td><b>$row[name]:</b></td>";
    if($row["type"] == "Text"){
      $content .= "<td><input type='text' name='rows[$i]' class='text400'><input type='hidden' name='type[$i]' value='text'></td></tr>";
      }
    elseif($row["type"] == "Textarea"){  
      $content .= "<td><textarea name='rows[$i]'></textarea><input type='hidden' name='type[$i]' value='textarea'></td></tr>";
      } 
    elseif($row["type"] == "Picture"){
      $content .= "<td>".wp_cp_get_gallery($i)."<input type='hidden' name='type[$i]' value='picture'></td></tr>";
      }
    elseif($row["type"] == "Checkbox"){
      $content .= "<td>".wp_cp_get_checkbox($row["rid"],$i)."<input type='hidden' name='type[$i]' value='checkbox'></td></tr>";
      }
    elseif($row["type"] == "Select"){
      $content .= "<td>".wp_cp_get_select($row["rid"],$i)."<input type='hidden' name='type[$i]' value='select'></td></tr>";
      }

    $i++;  
    }
  $content .= "</table><input type='hidden' name='rows_name' value='$rows_name'>";
  echo $content;
  }
  
function wp_cp_get_gallery($i){
  global $wpdb;
  
  $galleries = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."ngg_gallery`", ARRAY_A);
  $pictures = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."ngg_pictures`", ARRAY_A);
  

 
  $content = "<div><select onchange=\"javascript: var input = document.getElementById('input_$i').value; document.getElementById('input_$i').value = '[nggallery id=' + value + ']' + ' ' + input;\"><option value=''>".__('Select gallery','wp-custom-pages')."</option>";
  foreach($galleries as $gallery){
    $content .= "<option value=\"$gallery[gid]\">$gallery[title]</option>";
    }
  $content .= "</select> <select onchange=\"javascript: var input = document.getElementById('input_$i').value; document.getElementById('input_$i').value = '[singlepic id=' + value + ' w=320 h=240 float=center]' + ' ' + input;\"><option value=''>".__('Select picture','wp-custom-pages')."</option>";
  
  foreach($pictures as $picture){
    $content .= "<option value=\"$picture[pid]\">$picture[filename]</option>";
    }
  $content .= "</select></div><textarea name='rows[$i]' id='input_$i'></textarea><input type='hidden' name='picture[$i]' value='1'>";
  return $content;
  }
  
function wp_cp_get_select($select_id,$i){
  global $wpdb;
  
  //echo $select_id;
  
  $rows = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."cp_select_join` LEFT JOIN wp_cp_select_row ON wp_cp_select_join.row_id = wp_cp_select_row.sid WHERE wp_cp_select_join.name_id = $select_id ORDER BY wp_cp_select_row.name", ARRAY_A);
  
  //echo "SELECT * FROM `".$wpdb->prefix."cp_select_join` LEFT JOIN wp_cp_select_row ON wp_cp_select_join.row_id = wp_cp_select_row.sid WHERE wp_cp_select_join.name_id = $select_id ORDER BY wp_cp_select_row.name";

  $content = "<select name='rows[$i]' Onchange=\"javascript: if(value == 'add_new_select%_cp_%5%9%7%5%3'){document.getElementById('new_select_$i').disabled = false;}else{document.getElementById('new_select_$i').disabled = true;}\"><option>Select</option>";
  foreach($rows as $row){
    $content .= "<option>$row[name]</option>";
    }
  $content .= "<option value=\"add_new_select%_cp_%5%9%7%5%3\">Add new</option>";
  $content .= "</select><input type='text' name='select_new[$i]' class='text200' DISABLED id='new_select_$i'><input type='hidden' name='name_id[$i]' value='$select_id'>";
  return $content;
  }


function wp_cp_no_diacritic($text){
  $prevodni_tabulka = Array(
    'ä'=>'a',
    'Ä'=>'A',
    'á'=>'a',
    'Á'=>'A',
    'à'=>'a',
    'À'=>'A',
    'ã'=>'a',
    'Ã'=>'A',
    'â'=>'a',
    'Â'=>'A',
    'č'=>'c',
    'Č'=>'C',
    'ć'=>'c',
    'Ć'=>'C',
    'ď'=>'d',
    'Ď'=>'D',
    'ě'=>'e',
    'Ě'=>'E',
    'é'=>'e',
    'É'=>'E',
    'ë'=>'e',
    'Ë'=>'E',
    'è'=>'e',
    'È'=>'E',
    'ê'=>'e',
    'Ê'=>'E',
    'í'=>'i',
    'Í'=>'I',
    'ï'=>'i',
    'Ï'=>'I',
    'ì'=>'i',
    'Ì'=>'I',
    'î'=>'i',
    'Î'=>'I',
    'ľ'=>'l',
    'Ľ'=>'L',
    'ĺ'=>'l',
    'Ĺ'=>'L',
    'ń'=>'n',
    'Ń'=>'N',
    'ň'=>'n',
    'Ň'=>'N',
    'ñ'=>'n',
    'Ñ'=>'N',
    'ó'=>'o',
    'Ó'=>'O',
    'ö'=>'o',
    'Ö'=>'O',
    'ô'=>'o',
    'Ô'=>'O',
    'ò'=>'o',
    'Ò'=>'O',
    'õ'=>'o',
    'Õ'=>'O',
    'ő'=>'o',
    'Ő'=>'O',
    'ř'=>'r',
    'Ř'=>'R',
    'ŕ'=>'r',
    'Ŕ'=>'R',
    'š'=>'s',
    'Š'=>'S',
    'ś'=>'s',
    'Ś'=>'S',
    'ť'=>'t',
    'Ť'=>'T',
    'ú'=>'u',
    'Ú'=>'U',
    'ů'=>'u',
    'Ů'=>'U',
    'ü'=>'u',
    'Ü'=>'U',
    'ù'=>'u',
    'Ù'=>'U',
    'ũ'=>'u',
    'Ũ'=>'U',
    'û'=>'u',
    'Û'=>'U',
    'ý'=>'y',
    'Ý'=>'Y',
    'ž'=>'z',
    'Ž'=>'Z',
    'ź'=>'z',
    'Ź'=>'Z',
    ' ' => '-'
    );
  
  $text = strtr($text, $prevodni_tabulka);
  $text = strtolower($text);
  return $text;
  }
  
function wp_cp_array_to_string($array,$content = "array("){
  
  foreach($array as $key => $value){
    $content .= "'$key' => ";
    if(is_array($value)){
      $content .= "array( ";
      $content = wp_cp_array_to_string($value,$content);
      }
    else{
      $content .= "'$value', ";
      }
    
    
    }
  $content = substr($content, 0, -2);
  $content .= "), ";  
  return $content;
  }
  
function wp_cp_recomanded_plugins(){
  if(get_option("wp_cp_recomanded_plugin")){
  if(isset($_GET["recomended_plugin"])){
    update_option("wp_cp_recomanded_plugin",0);
    }
  else{
    if(array_search("cms-tree-page-view/index.php", get_option("active_plugins")) === false){
      $url = get_site_url();
      echo "<div class='green'>".__('Pugin','wp-custom-pages')." <a href='$url/wp-admin/plugin-install.php?tab=search&type=term&s=CMS+Tree+Page+View'>CMS Tree Page View</a> ".__("is highly recommended. <a href='$url/wp-admin/admin.php?page=wp_custom_pages&recomended_plugin=0'>Turn this off</a>",'wp-custom-pages')."</div>";
      }
    }
  }
  }
  
function wp_cp_get_checkbox($id,$i){
  global $wpdb;
  
  $checkbox_value = $wpdb->get_row("SELECT * FROM `wp_cp_checkbox_values` WHERE `checkbox_id` = $id", ARRAY_A);
  $content = "<select name='rows[$i]'><option>$checkbox_value[no_value]</option><option>$checkbox_value[yes_value]</option></select>";
  return $content;
  }
?>