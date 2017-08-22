<?php
class TicketBook{
	public $pluginUri;
    public $pluginDir;
	public $pluginTmpdir;
    public $pluginName;
    public static $table_name = "";
	
function __construct() {
		global $frmPluginName,$wpdb;
		self::$table_name=$wpdb->prefix .'ticket_book';
		$this->pluginName=$frmPluginName;
        $this->pluginDir = dirname(__FILE__) . "/";
        $this->pluginUri=WP_CONTENT_URL . "/plugins/".basename(dirname(__FILE__)). "/";
        add_action( 'wpcf7_init',array($this,'custom_add_form_ticket_clock'));
		add_action('wpcf7_before_send_mail',array($this,'save_form'));
}

public static function install_table(){
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	global $wpdb;

	$field_name="booked";
	$field_num=100;
	
	$sql_query = "CREATE TABLE IF NOT EXISTS `".self::$table_name."` ( `book_id` INT(11) NOT NULL AUTO_INCREMENT ,".TicketBook::create_field_dynamic($field_num,$field_name).", PRIMARY KEY (`book_id`)) ENGINE = InnoDB;";
	dbDelta($sql_query);
	
	$record_query = "SELECT *FROM `".self::$table_name."` ";
	if(count($wpdb->get_results($wpdb->prepare($record_query, ARRAY_A))) == 0){
		$insert_query = "INSERT INTO `".self::$table_name."` (`book_id`,".TicketBook::all_created_fields($field_num,$field_name).") VALUES (NULL,".TicketBook::all_fields_values($field_num).")";
	dbDelta($insert_query);
	}

}

public static function create_field_dynamic($field_num,$field_name){
	for($i=1;$i<=$field_num;$i++){
    	$field[$i]="`".$field_name."_".$i."` INT(1) NOT NULL";
	}
	return implode(',',$field);
}

public static function all_created_fields($field_num,$field_name){
	for($i=1;$i<=$field_num;$i++){
    	$field[$i]="`".$field_name."_".$i."`";
	}
	return implode(',',$field);
}

public static function all_fields_values($field_num){
	for($i=1;$i<=$field_num;$i++){
    	$field[$i]= "0";
	}
	return implode(',',$field);
}

public function custom_add_form_ticket_clock(){
wpcf7_add_form_tag( 'ticket_book_cf7',array($this,'custom_ticket_book_handler')); // "clock" is the type of the form-tag
}

public function custom_ticket_book_handler( $tag ) {
global $wpdb;
$field_name="booked";
$total_checkboxes = 100;

$Allfields = TicketBook::all_created_fields($total_checkboxes,$field_name);
$Allrecord = "SELECT ".$Allfields." FROM `".self::$table_name."` ";
$Records= $wpdb->get_results($Allrecord, ARRAY_A);
$html = '';

foreach($Records[0] as $Fields=>$FieldValues){
	if($FieldValues == 1){
	$disabled = 'disabled';
	}
	else{
	$disabled = '';
	}
	
	$SplitFields= explode('booked_',$Fields);
	$Ids = $SplitFields[1];
	
$html .= '<input type="checkbox" name="booked[]" value="'.$Ids.'" '.$disabled.' />';				
}	
return $html; 
}


public function save_form($wpcf7){
	global $wpdb;
	require_once ABSPATH . 'wp-content/plugins/contact-form-7/includes/submission.php';
	$submission = WPCF7_Submission::get_instance();
	$submited = array();
    $submited['posted_data'] = $submission->get_posted_data();
 	$TicketsArray = $submited['posted_data']['booked'];
$wpdb->query($wpdb->prepare("UPDATE `wp_ticket_book` SET ".$this->update_field_dynamic($TicketsArray)."",ARRAY_A));
}

public function update_field_dynamic($TicketsArray){
	$fields = array();
	foreach($TicketsArray as $values){
		$fields[]= "`booked_".$values."` = '1'";
	}
	return implode(',',$fields);
}

}
$TicketBookObj=new TicketBook();