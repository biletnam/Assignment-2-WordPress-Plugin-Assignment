jQuery(document).ready(function() {
	jQuery('.tick_custom_type').change(function() {
		var alert_text_nonce = jQuery('#alert_text_nonce').val();
		var post_value = jQuery(this).val();
		
		 
		if(jQuery(this).prop('checked') == true){
			var	checkbox_value = 1;
			}
		else{
			var checkbox_value = 0;	
			}	

jQuery(".spinner").show();
jQuery.ajax({
         type : "post",
         dataType : "json",
         url : myAjax.ajaxurl,
         data : {action: "get_post_type", post_value : post_value, nonce: alert_text_nonce,checkbox_value:checkbox_value},
         success: function(response) {
			jQuery(".spinner").hide();
			var post_type = response.post_type;
			var dropdown =response.dropdown;
			var checkbox_value =response.checkbox_value;
				if(checkbox_value == 1){
					jQuery(dropdown).insertAfter("."+post_type);		
					}
				else{
					jQuery("#dropdown_"+post_type).remove();
					}		
			
		}
      });   
});

	jQuery('#submit_changes').click(function() {
		var AllValues = [];
		var alert_text = jQuery('#alert_text').val();
		jQuery('.tick_custom_type').each(function() {
			var ischecked= jQuery(this).is(':checked');
			if(!ischecked){
				addValues = 0+'@@'+jQuery(this).val()+'@@'+0;
				}
			else{
				addValues = 1+'@@'+jQuery(this).val()+'@@'+jQuery('#select_'+jQuery(this).val()).val();
				}
		AllValues.push(addValues);	
	});
		var AllValuesString = AllValues.join(",");
		jQuery.ajax({
         type : "post",
         dataType : "json",
         url : myAjax.ajaxurl,
         data : {action: "update_options",AllValuesString:AllValuesString,alert_text:alert_text},
         success: function(response) {
			 alert(response);
			 
			 }
      });	
	});
	 
});
