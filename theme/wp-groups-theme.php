<div class="wrap" id="wp-archeology">
<div id="icon-options-general" class="icon32"><br /></div>
<h2><?php _e('Add New Group', 'wp-custom-pages'); ?></h2>
<?php
wp_cp_recomanded_plugins();
?>
<form method="post">
<input type="hidden" name="add_i" value="<?php echo $add_i; ?>">

<!-- ***************************** LEVEL NAME ***************************** -->
<table>
  <tr>
    <td><?php _e('Name', 'wp-custom-pages'); ?>:</td>
    <td><input type="text" name="name" value="<?php echo $name; ?>" class="text400" <?php echo $missing_name; ?>></td>
    <td><?php submit_button( __('Add row', 'wp-custom-pages'), 'primary', 'add_item', false ); ?></td>
  </tr>

</table>

<!-- ***************************** ADDING LEVEL ROWS ***************************** -->
<table>
    <tr>
      <th></th>
      <th><?php _e('Used befor', 'wp-custom-pages'); ?>:</th>
      <th><?php _e('Name', 'wp-custom-pages'); ?>:</th>
      <th><?php _e('Type', 'wp-custom-pages'); ?>:</th>
    </tr>
    <tr>
      <td>1.</td>
      <td><?php _e("Required row", 'wp-custom-pages'); ?></td>
      <td><input type="text" value="<?php _e('Title', 'wp-custom-pages'); ?>" class="text200" DISABLED></td>
      <td>
        <select DISABLED>
          <option><?php if(!isset($item_type[$i])){_e('Text', 'wp-custom-pages');} else{echo $item_type[$i];} ?></option>
          <option><?php _e('Textarea', 'wp-custom-pages'); ?></option>
          <option><?php _e('Select', 'wp-custom-pages'); ?></option>
          <option><?php _e('Checkbox', 'wp-custom-pages'); ?></option>
          <option><?php _e('Picture', 'wp-custom-pages'); ?></option>
          <option><?php _e('Select type', 'wp-custom-pages'); ?></option>
        </select>
      </td>
    </tr>

  <?php
  for($i=0;$i<$add_i;$i++){
  ?>
    <tr>
    <td><?php echo $i+2; ?>.</td>
    <td><?php $disable = wp_cp_get_rows(array('name' => "row_used_before[$i]", 'selected' =>"$row_used_before[$i]", 'style' => "$row_collision[$i]", 'i' =>$i)); ?></td>
    <td><input type="text" name="item_name[<?php echo $i; ?>]" value="<?php echo $item_name[$i]; ?>" class="text200" <?php echo "id=\"item_name_$i\""; echo $missing_item_type_name[$i]; if($disable && $row_used_before[$i] != "new"){echo "DISABLED";} ?>></td>
    <td>

    <script type="text/javascript">
    function checkbox_values_<?php echo $i; ?>(value){
      if(value == 'Checkbox'){
        document.getElementById('checkbox_values_<?php echo $i; ?>').style.display = 'block';
        }
      else{
        document.getElementById('checkbox_values_<?php echo $i; ?>').style.display = 'none';
        }
      }
    </script>


    <select name="item_type[<?php echo $i; ?>]" <?php echo "id=\"item_type_$i\""; echo $missing_item_type_name[$i]; if($disable && $row_used_before[$i] != "new"){echo "DISABLED";} ?> onchange="javascript: checkbox_values_<?php echo $i; ?>(value);">

    <?php if(!isset($item_type[$i]) || $item_type[$i] == "select"){echo "<option value='select'>".__('Select type', 'wp-custom-pages')."</option>";} ?>
    <option value="Text" <?php if($item_type[$i] == "Text") echo "SELECTED"; ?>><?php _e('Text', 'wp-custom-pages'); ?></option>
    <option value="Textarea" <?php if($item_type[$i] == "Textarea") echo "SELECTED"; ?>><?php _e('Textarea', 'wp-custom-pages'); ?></option>
    <option value="Select" <?php if($item_type[$i] == "Select") echo "SELECTED"; ?>><?php _e('Select', 'wp-custom-pages'); ?></option>
    <option value="Checkbox"  <?php if($item_type[$i] == "Checkbox") echo "SELECTED"; ?>><?php _e('Checkbox', 'wp-custom-pages'); ?></option>
    <option value="Picture"  <?php if($item_type[$i] == "Picture") echo "SELECTED"; ?>><?php _e('Picture', 'wp-custom-pages'); ?></option>

    </select>
    </td>
  </tr>
  <tr>
    <td> </td>


    <td colspan="3">
      <div id="checkbox_values_<?php echo $i; ?>" style="display: <?php if($item_type[$i] == "Checkbox") echo "block"; else echo "none";?>;" class="checkbox">
      <table>
        <tr>
          <td><b><?php _e('Checkbox Yes value'); ?>:</b></td>
          <td><input type="text" name="checkbox_yes[<?php echo $i; ?>]"" value="<?php echo $checkbox_yes[$i]; ?>" class="text200" style="<?php echo $checkbox_style[$i]; ?>"></td>
        </tr>
        <tr>
          <td><b><?php _e('Checkbox No value'); ?>:</b></td>
          <td><input type="text" name="checkbox_no[<?php echo $i; ?>]"" value="<?php echo $checkbox_no[$i]; ?>" class="text200" style="<?php echo $checkbox_style[$i]; ?>"></td>
        </tr>
      </table>
      </div>
    </td>
  </tr>
  <?php
    }
  ?>
  <tr>
    <td><?php echo $i+2; ?>.</td>
    <td><?php _e("Required row", 'wp-custom-pages'); ?></td>
    <td><input type="text" value="<?php _e('Custom text', 'wp-custom-pages'); ?>" class="text200" DISABLED></td>
    <td>

    <select DISABLED>
    <option><?php _e('TinyMCE', 'wp-custom-pages'); ?></option>
    <option value="Text"><?php _e('Text', 'wp-custom-pages'); ?></option>
    <option value="Textarea"><?php _e('Textarea', 'wp-custom-pages'); ?></option>
    <option value="Select"><?php _e('Select', 'wp-custom-pages'); ?></option>
    <option value="Checkbox"><?php _e('Checkbox', 'wp-custom-pages'); ?></option>
    <option value="Picture"><?php _e('Select type', 'wp-custom-pages'); ?></option>
    </select>

    </td>
  </tr>
  <tr>
    <td></td><td></td><td></td>
    <td align="left"><?php submit_button( _('Save'), 'primary', 'save_group', false ); ?></td>
  </tr>
</table>
</form>

</div>
