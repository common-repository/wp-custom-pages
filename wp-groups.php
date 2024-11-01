<?php
global $wpdb;


//************************* number of rows *********************************
$add_i = 0;

extract($_POST);
//print_r($_POST);
//************************ add new row *************************************
if(isset($add_item)) $add_i++;
  
$save = true;  

if(isset($save_group)){
  //*********************** GROUP NAME IS REQUIRED *************************
  if($name == ""){
    $save = false;
    $missing_name = "style=\"border: 1px solid red;\"";
    echo "<div class=\"red\">".__('Name is not filled.', 'wp-custom-pages')."</div>";
    }
  else{
    $check_name = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."cp_group_name WHERE name = '$name'");
    if($check_name != ""){
      $save = false;
      $missing_name = "style=\"color: red; border: 1px solid red;\"";
      echo "<div class=\"red\">".__('Name have to be unique. Group ', 'wp-custom-pages')."<b>".$name."</b>".__(' exists. Use another.', 'wp-custom-pages')."</div>";
      }
    }

  $first_time_item_type = 0;
  $first_time_row_collision = 0;
  $first_time_missing_checkbox = 0;
  
  for($i=0;$i<$add_i;$i++){
    if($item_type[$i] == "Checkbox" && ($checkbox_no[$i] == "" || $checkbox_yes[$i] == "") && $item_name[$i] != ""){
      $checkbox_style[$i] = "border: 1px solid green;";
      $save = false;

      if($first_time_missing_checkbox++ == 0){
        echo "<div class=\"green\">".__('Yes and No values are required.', 'wp-custom-pages')."</div>";
        }
      }
  
    if($item_type[$i] == "select" && $item_name[$i] != "" && $row_used_before[$i] == "new"){
      //Isset new row an item_name is filled but type is not selected
      $save = false;
      $missing_item_type_name[$i] = "style=\"border: 1px solid #3300CC; color:#3300CC;\"";
      if($first_time_item_type++ == 0){
        echo "<div class=\"blue\">".__('Type is not selected.', 'wp-custom-pages')."</div>";
        }
      }        

    if(wp_cp_array_collision($row_used_before[$i],$row_used_before) && $row_used_before[$i] != "new" && $row_used_before[$i] != "select"){
      //cant set rows used before twise
      $row_collision[$i] = "style=\"border: 1px solid #FFCC33;\"" ;
      if($first_time_row_collision++ == 0){
        echo "<div class=\"yellow\">"._('Can not select the same row twise.')."</div>";
        }
      $save = false;
      }
    else{
      $row_collision[$i] = "";
      }
    }

  if($save === true){
    //insert group name
    if($wpdb->insert( $wpdb->prefix.'cp_group_name', array( 'name' => $name), array( '%s'))){
      $group_id = $wpdb->insert_id;
      }                  
    //insert group row
    for($i=0;$i<$add_i;$i++){
      //insert new row
      if($row_used_before[$i] == "new" && $item_name[$i] != ""){
        
        if($item_type[$i] == "Text") $order = 0;
        elseif($item_type[$i] == "Textarea") $order = 3;
        elseif($item_type[$i] == "Select") $order = 1;
        elseif($item_type[$i] == "Checkbox") $order = 2;
        elseif($item_type[$i] == "Picture") $order = 4;
        
        $wpdb->insert( $wpdb->prefix.'cp_group_row', array( 'name' => $item_name[$i], 'type' => $item_type[$i], 'order' => $order ), array( '%s', '%s', '%s'));
        $row_id = $wpdb->insert_id;
        if($item_type[$i] == "Checkbox") $wpdb->insert( $wpdb->prefix.'cp_checkbox_values', array( 'checkbox_id' => $row_id, 'yes_value' => $checkbox_yes[$i], 'no_value' => $checkbox_no[$i] ), array( '%s', '%s', '%s'));
        }
      else{
        $row_id = $row_used_before[$i]; 
        }
      $wpdb->insert( $wpdb->prefix.'cp_group_join', array( 'group_id' => $group_id, 'row_id' => $row_id ), array( '%s', '%s')); 
      
      }
    echo "<div class=\"green\">".__('Group')." <b>".$name."</b> ".__('was created.')."</div>";
    }
  else{
    
    }
  }
?>    