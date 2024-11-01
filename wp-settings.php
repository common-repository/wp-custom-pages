<?php
if(isset($_POST["text"])){
  //print_r($_POST);
  extract($_POST);

  update_option("wp_cp_dynamic_style", $dynamic_style);
  update_option("wp_cp_styles_text", $text);
  update_option("wp_cp_styles_textarea", $textarea);
  update_option("wp_cp_styles_select", $select);
  update_option("wp_cp_styles_checkbox", $checkbox);
  update_option("wp_cp_styles_picture", $picture);
  
  update_option("wp_cp_default_visibility", $visibility);
  update_option("wp_cp_default_status", $status);
  update_option("wp_cp_default_password", $password);
  update_option("wp_cp_default_comments", $comments);
  
  update_option("wp_cp_plugin_name", $plugin_name);

  if($dynamic_style){
    $content = "
    span.wp_cp_text{".$text."}
    span.wp_cp_textarea{".$textarea."}
    span.wp_cp_select{".$select."}
    span.wp_cp_checkbox{".$checkbox."}
    span.wp_cp_picture{".$picture."}
    ";

    $url = get_site_url();
    $file = fopen("../wp-content/plugins/wp-custom-pages/theme/wp-custom_pages_style.css","w+");
    fwrite($file,$content);
    fclose($file);
    }
  }


?>


<div class="wrap" id="wp-archeology">
<div id="icon-options-general" class="icon32"><br /></div> 
<h2><?php _e('Settings', 'wp-custom-pages'); ?></h2>             
<?php
wp_cp_recomanded_plugins();
?>
<form method="post">
<table>
  <tr>
    <td><h2><?php _e('Styles', 'wp-custom-pages'); ?></h2></td>
  </tr>
  <tr>
    <td><?php _e('Set style', 'wp-custom-pages'); ?>:</td>
    <td>
        <select name="dynamic_style" title="<?php _e('Dynamic - when you change settings, style change for all pages. Static - style change only for new generated pages.', 'wp-custom-pages'); ?>">
        <option value="1" <?php if(get_option("wp_cp_dynamic_style")) echo "SELECTED"; ?>><?php _e('Dynamic', 'wp-custom-pages'); ?></option>
        <option value="0" <?php if(!get_option("wp_cp_dynamic_style")) echo "SELECTED"; ?>><?php _e('Static', 'wp-custom-pages'); ?></option>
      </select>
    </td>
  </tr>
  <tr>
    <td><?php _e('Text', 'wp-custom-pages'); ?>:</td>
    <td><input type="text" class="text600" name="text" value="<?php echo get_option("wp_cp_styles_text"); ?>"></td>
  </tr>
  <tr>
    <td><?php _e('Textarea', 'wp-custom-pages'); ?>:</td>
    <td><input type="text" class="text600" name="textarea" value="<?php echo get_option("wp_cp_styles_textarea"); ?>"></td>
  </tr>
  <tr>
    <td><?php _e('Select', 'wp-custom-pages'); ?>:</td>
    <td><input type="text" class="text600" name="select" value="<?php echo get_option("wp_cp_styles_select"); ?>"></td>
  </tr>
  <tr>
    <td><?php _e('Checkbox', 'wp-custom-pages'); ?>:</td>
    <td><input type="text" class="text600" name="checkbox" value="<?php echo get_option("wp_cp_styles_checkbox"); ?>"></td>
  </tr>
  <tr>
    <td><?php _e('Picture', 'wp-custom-pages'); ?>:</td>
    <td><input type="text" class="text600" name="picture" value="<?php echo get_option("wp_cp_styles_picture"); ?>"></td>
  </tr>
  <tr>
    <td colspan="2"><hr /></td>

  </tr>
  <tr>
    <td><?php _e('Plugin name', 'wp-custom-pages'); ?>:</td>
    <td><input type="text" class="text400" name="plugin_name" value="<?php echo get_option("wp_cp_plugin_name"); ?>"></td>
  </tr>

  <!-- ********* DEFAULTNE NASTAVENIA ********************* -->
  <?php
  if(1==2){
  ?>
  <tr>
    <td><h2><?php _e('Defaults', 'wp-custom-pages'); ?></h2></td>
  </tr>           
  <tr>
    <td><?php _e('Default visibility', 'wp-custom-pages'); ?>:</td>
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
    <td><?php _e('Default status', 'wp-custom-pages'); ?>:</td>
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
    <td><?php _e('Default password', 'wp-custom-pages'); ?>:</td>
    <td><input type="text" class="text200" name="password" value="<?php echo get_option("wp_cp_default_password"); ?>"></td>
  </tr>
  <tr>
    <td><?php _e('Display comments', 'wp-custom-pages'); ?>:</td>
    <td>
      <select name="comments">
        <option><?php echo get_option("wp_cp_default_comments"); ?></option>
        <option><?php _e('open', 'wp-custom-pages'); ?></option>
        <option><?php _e('close', 'wp-custom-pages'); ?></option>
      </select>
    </td>
  </tr>
  <?php
  }
  ?>
  <tr>
    <td></td>
    <td align="left"><?php submit_button( __('Save', 'wp-custom-pages'), 'primary', 'show_group',false ); ?></td>
  </tr>
</table>
</form> 
</div>

