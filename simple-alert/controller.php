<?php
class SimpleAlert{
	public $pluginUri;
    public $jscriptUri;
	public $pluginDir;
    public $pluginTmpdir;
    public $pluginName;
	
function __construct() {
		global $frmPluginName,$wpdb;
		$this->pluginName=$frmPluginName;
        $this->pluginDir = dirname(__FILE__) . "/";
        $this->pluginTmpdir.= $this->pluginDir . "views/";
       	$this->pluginUri=WP_CONTENT_URL . "/plugins/".basename(dirname(__FILE__)). "/";
        $this->jscriptUri=$this->pluginUri . "js/";
		$this->myAjax = 'myAjax';

add_action("admin_menu",array($this,"menu"));
add_action( 'init', array($this,'my_script_enqueuer'));
add_action('wp_ajax_get_post_type',array($this,'get_selection_boxes'));
add_action('wp_ajax_nopriv_get_post_type',array($this,'get_selection_boxes'));
add_action('wp_ajax_update_options',array($this,'update_options'));
add_action('wp_ajax_nopriv_update_options',array($this,'update_options'));
add_action( 'wp', array($this,'check_custom_post_type'));
}

public function menu() {
        add_menu_page($this->pluginName, $this->pluginName, "activate_plugins", "simple_alert_settings", array($this, "simple_alert_settings"));
 }
 
public function simple_alert_settings() {
        include_once $this->pluginTmpdir . 'simple_alert_settings.php';
    }

public function my_script_enqueuer() {
   wp_register_script( "jquery-script", $this->jscriptUri."custom.js");
   wp_localize_script( 'jquery-script', $this->myAjax, array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
   wp_enqueue_script('jquery-script',$this->jscriptUri."custom.js");

}

public function get_selection_boxes() {
$post_value= $_POST['post_value'];
$checkbox_value= $_POST['checkbox_value'];

$dropdown_result = $this->selection_box_list($post_value);
$final_result_array = array('post_type'=> $post_value,'checkbox_value' =>$checkbox_value,'dropdown' =>$dropdown_result);
$final_result = json_encode($final_result_array);

echo $final_result;
die();
}

public function selection_box_list($post_value) {
    global $wpdb;
	$custom_post_type = $post_value;
    $results = $wpdb->get_results($wpdb->prepare( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = %s and post_status = 'publish'", $custom_post_type ), ARRAY_A );
	$output = "";
	$output .= '<td id="dropdown_'.$post_value.'"><select name="select_'.$post_value.'" id="select_'.$post_value.'">';
	$output .= '<option value="0">-select '.$post_value.'-</option>';
	foreach( $results as $index => $post ) {
		if(!empty(get_option('alert_'.$post_value))){
			$Arrays = maybe_unserialize(get_option('alert_'.$post_value));
				if($post['ID'] == $Arrays['post_id']){
				    $selected = 'selected';
				}else{
					$selected = '';
				}
		}		
	$output .= '<option value="' . $post['ID'] . '" '.$selected.' >' . $post['post_title'] . '</option>';
    }
	$output .= '</select></td>';
	return $output;
}


public function update_options(){
	$AllValuesString = $_POST['AllValuesString'];
	$alert_text = $_POST['alert_text'];
	$all_values_array = explode(",",$AllValuesString);
		foreach($all_values_array as $FilterValues){
			$FilterValuesArray = explode("@@",$FilterValues);
			$CheckBoxValue =$FilterValuesArray[0];
			$CustomValue = 'alert_'.$FilterValuesArray[1];
			$DropDownValue = $FilterValuesArray[2];
		
		$UpdateValueArray = array('c'=>$CheckBoxValue,'post_id' => $DropDownValue);
		update_option($CustomValue,maybe_serialize($UpdateValueArray));
}
update_option('alert_text',maybe_serialize($alert_text));
echo json_encode('successfully updated');
die();

}

public function check_custom_post_type()
{
if ( is_singular() ) {
	global $post;
	$CurrentPostType = get_post_type();
	$CurrentPostId = $post->ID;
	$SerializedResult = get_option('alert_'.$CurrentPostType);
	$ResultValues = maybe_unserialize($SerializedResult);

	if($CurrentPostId == $ResultValues['post_id']){
		echo '<script type="text/javascript">alert("'.get_option('alert_text').'")</script>';
	}
}

}
}
$SimpleAlert=new SimpleAlert();