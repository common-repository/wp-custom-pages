<div class="wrap" id="wp-archeology">
<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br /></div> 
<h2><?php _e('Add New Article', 'wp-custom-pages'); ?></h2>             
<?php

//********************* CHECK RECOMANDED PLUGIN *******************************
wp_cp_recomanded_plugins();
  
if(isset($_POST["group_name_id"])){
  extract($_POST);
  }
$grups_select = wp_cp_get_groups_name(array('name' => 'group_name_id', 'selected' => "$group_name_id", "echo" => false));
?>



<form method="post"> 
<div style="float: left">
<table><tr><td>
<?php

  

echo "$grups_select</td><td>";
submit_button( __('Open', 'wp-custom-pages'), 'primary', 'show_group',false );
echo "</td></tr></table>";


if(isset($_POST["group_name_id"])){
  
  if($_POST["group_name_id"] == 'select'){}
  else{
    wp_cp_show_group_rows($group_name_id,array('title_border' => $title_border, 'parent_id' => $parent_id));
    ?>
    
   <div id="poststuff" style="width: 700px;">
   <b><?php _e('Custom text', 'wp-custom-pages'); ?>:</b>
  <div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea"><?php the_editor(''); ?>
  <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
  <?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
  </div>
  </div>         
    
    <?php
    echo submit_button( __('Save', 'wp-custom-pages'), 'primary', 'save', false );
    }  
  }
?>
</div>  
<?php
if(isset($_POST["group_name_id"]) && $_POST["group_name_id"] != 'select' && 1 == 2){
?>
<div style='border: 1px dotted black; -moz-border-radius: 8px; border-radius: 8px; margin: 5px; padding: 15px; margin-left: 650px;'>
<table>
  <tr>
    <td><?php _e('Visibility', 'wp-custom-pages'); ?>:</td>
    <td>
      <select name="visibility">
        <option><?php echo get_option("wp_cp_default_visibility"); ?></option>
        <option><?php _e('publish', 'wp-custom-pages'); ?></option>
        <option><?php _e('private', 'wp-custom-pages'); ?></option>
        <option><?php _e('password', 'wp-custom-pages'); ?></option>
      </select>
    </td>
  </tr>                                             
  <tr>
    <td><?php _e('Status', 'wp-custom-pages'); ?>:</td>
    <td>
      <select name="status">
        <option><?php echo get_option("wp_cp_default_status"); ?></option>
        <option><?php _e('publish', 'wp-custom-pages'); ?></option>
        <option><?php _e('private', 'wp-custom-pages'); ?></option>
        <option><?php _e('password', 'wp-custom-pages'); ?></option>
      </select>
    </td>
  </tr>
  <tr>
    <td><?php _e('Password', 'wp-custom-pages'); ?>:</td>
    <td><input type="text" class="text100" name="picture" value="<?php echo get_option("wp_cp_default_password"); ?>"></td>
  </tr>
  <tr>
    <td><?php _e('Comments', 'wp-custom-pages'); ?>:</td>
    <td>
      <select name="comments">
        <option><?php echo get_option("wp_cp_default_comments"); ?></option>
        <option><?php _e('open', 'wp-custom-pages'); ?></option>
        <option><?php _e('close', 'wp-custom-pages'); ?></option>
      </select>
    </td>
  </tr>
</table>

</div>
<?php
  }
?>
</form>



</div>   

