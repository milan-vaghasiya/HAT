<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
     	
function get_setting($setting_name = "")
{
	$CI =& get_instance();
	$CI->db = $CI->load->database('default',true);
	$result = $CI->db->where('setting_name',$setting_name)->get('master_setting')->row()->preferance;
	return $result;
}

function get_all_setting()
{
	$CI =& get_instance();
	$CI->db = $CI->load->database('default',true);
	$result = $CI->db->get('master_setting')->result_array();
	return $result;
}

?>