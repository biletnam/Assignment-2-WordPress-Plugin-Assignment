<div class="wrap">
   <h2>Simple Alert</h2>
   
	<table cellspacing="0" class="wp-list-table widefat simple_alert_table" style="width:70%; margin:20px 0px 0px 0px;">
      <tbody>
        <tr>
          <td>
            <input type="hidden" name="alert_text_nonce" value="<?php echo wp_create_nonce("alert_text_nonce"); ?>" id="alert_text_nonce" />
			<input type="text" name="alert_text" value="<?php echo get_option('alert_text');?>" id="alert_text" placeholder="Enter Text"/>
          </td>
        </tr>
		
		<tr class='checkboxes-list'>
			<td><table width="100%">
			<?php
$types = get_post_types($args);
foreach( $types as $key => $type ){
	if(!empty(get_option('alert_'.$type))){
	$Arrays = maybe_unserialize(get_option('alert_'.$type));
	$ExistedDropDown = $this->selection_box_list($type);
		if($Arrays['c'] == 1){
			$checked = 'checked';
			$ExistedDropDown = $ExistedDropDown;
		}else{
			$checked = '';
			$ExistedDropDown = '';
		}
		
}

echo "<tr><td class='".$type."'><label><input type='checkbox' id='".$type."' class='tick_custom_type' value='".$type."' name='check_list[]' $checked />&nbsp; $type</label></td>".$ExistedDropDown."</tr>";
	
}
?>
	</table></td>
		</tr>
		<tr>
          <td colspan="2" align="left">
            <input type="submit" name="submit_changes" value="Save Changes" class="button button-primary button-large" id="submit_changes"/>
          </td>
        </tr>
       </tbody>
    </table>
  <br class="clear">
</div>