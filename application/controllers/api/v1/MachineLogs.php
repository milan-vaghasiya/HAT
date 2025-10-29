<?php 

defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );

header('Content-Type:application/json');

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE,OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

class MachineLogs extends CI_Controller{
    public function __construct(){
        parent::__construct();   
        $this->load->model('masterModel');
        $this->load->model('MachineLogModel','machineLog');
        $this->load->model('ItemModel','item');
    }
    
    public function printJson($data){
		print json_encode($data);exit;
	}
	
	public function save(){
	    $data = $this->input->post();
	    $this->printJson($this->machineLog->save($data));
	}
	
	public function list(){
	    header('Content-Type:text/html');
	    $result = $this->machineLog->getMachineLogs();
	    
	    $html = '<html><head>
                    <style>
                    table, th, td {
                      border: 1px solid black;
                      border-collapse: collapse;
                    }
                    </style>
                    </head>
                    <body style="margin:2%;" align="center"> <h2>Machine Logs</h2> <table style="width:100%;">
                   
	            <thead>
	                <tr>
	                    <th style="width:8%;">#</th>
	                    <th>Production Time</th>
	                    <th>Spindle ON Time</th>
	                    <th>Ideal Time</th>
	                    <th>Part Count</th>
	                    <th>Tool No</th>
	                    <th>Device No.</th>
	                    <th>N1</th>
	                    <th>N2</th>
	                    <th>N3</th>
	                    <th>N4</th>
	                    <th>N5</th>
	                </tr>
	            </thead><tbody>';
	            
	   foreach($result as $row):
	       $html .= '<tr>
	            <td align="center">'.$row->id.'</td>
	            <td align="center">'.$row->production_time.'</td>
	            <td align="center">'.$row->spindle_on_time.'</td>
	            <td align="center">'.$row->ideal_time.'</td>
	            <td align="center">'.$row->part_count.'</td>
	            <td align="center">'.$row->tool_no.'</td>
	            <td align="center">'.$row->device_no.'</td>
	            <td align="center">'.$row->n1.'</td>
	            <td align="center">'.$row->n2.'</td>
	            <td align="center">'.$row->n3.'</td>
	            <td align="center">'.$row->n4.'</td>
	            <td align="center">'.$row->n5.'</td>
	       </tr>';
	   endforeach;
	   
	   $html .= '</tbody></table></body></html>';
	   
	   echo $html;exit;
	}
}

?>