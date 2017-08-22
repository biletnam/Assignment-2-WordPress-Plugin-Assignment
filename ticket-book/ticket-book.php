<?php 
/**
 * Plugin Name: Ticket Booking
 * Plugin URI: #
 * Description: Plugin for booking ticket number on contact form by choosing checkboxes. 
 * Version: 0.1
 * Author: Gunjan S Patel
 * License: GPLv2 or later

*/

register_activation_hook( __FILE__,array('TicketBook','install_table')); 
global $frmPluginName;
$frmPluginName = "Ticket Book";
include_once 'controller.php';
