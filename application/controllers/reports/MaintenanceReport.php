<?php
class MaintenanceReport extends MY_Controller
{
    private $indexPage = "report/maintenance_report/index";
    private $machine_report = "report/maintenance_report/machine_report";  
	private $preventive_maintenance_schedule = "report/maintenance_report/preventive_maintenance_schedule";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Maintenance Report";
		$this->data['headData']->controller = "reports/maintenanceReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/maintenance_report/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'MAINTENANCE REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

	public function machineReport(){
        $this->data['pageHeader'] = 'LIST OF PLANT MACHINES';
        $this->data['machineData'] = $this->machine->getMachineForReport();
        $this->load->view($this->machine_report,$this->data);
    }
	
	 public function preventiveMaintenance(){
        $this->data['pageHeader'] = 'PREVENTIVE MAINTENANCE SCHEDULE';
        $this->data['machineData'] = $this->machine->getMachineList(); 
        $this->load->view($this->preventive_maintenance_schedule,$this->data);
    }

    public function getPreventiveMaintenance($jsonData=""){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post();
        endif;
        $machineData = $this->machine->getmaintanenceData($data['machine_id']);//print_r($machineData);exit;
            $tbody="";$thead="";$i=1;
            $total=0;
            $thead =  '<tr class="text-center">
                            <th colspan="3"><h4>PREVENTIVE MAINTENANCE SCHEDULE</h4></th>
                            <th>D/MNT/02 (00/01.01.16)</th>
                        </tr>
                        <tr class="text-center">
                            <th colspan="3" class="text-left">Machine Name :- '.(!empty($machineData[0]->item_name)?$machineData[0]->item_name:'').'</th>
                            <th class="text-left">Code No.:'.(!empty($machineData[0]->item_code)?$machineData[0]->item_code:'').'</th>
                        </tr>
                        <tr>
                        <th  style="min-width:5px;">#</th>
                        <th  class="text-center"> Activity Description</th>
                        <th  style="min-width:150px;">Schedule</th>
                        <th  style="min-width:150px;">Remark</th> 
                        </tr>';
            foreach($machineData as $row): 
                    $tbody .= '<tr>
                                <td style="min-width:5px;">'.$i++.'</td>';
                                $tbody .= '<td style="min-width:150px;">'.$row->activities.'</td>';
                                $tbody .= '<td style="min-width:150px;">'.$row->checking_frequancy.'</td>';
                                $tbody .= '<td></td>;
                                </tr>';  
            endforeach;
            if($data['type'] == 1){

                $pdfData = '<table id="reportTable" class="table item-list-bb">
                <thead class="thead-info" id="theadData">'.$thead.'</thead>
                <tbody class="text-center" id="receivableData">'.$tbody.'</tbody>
                </table>';

                $htmlHeader = "<table class='table item-list-bb' style='margin-bottom:0px'>
                                    <tr>
                                        <th class='text-center' style='width:85%'><h4>AKSHAR ENGINEERS</h4></th>
                                        <th><img src='assets/images/logo.png' class='img' style='height:60px'></th>
                                    </tr>
                                </table>";
                $mpdf = $this->m_pdf->load();
                $pdfFileName='CustOrdReg'.time().'.pdf';
                $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
                $stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
                $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
                $mpdf->WriteHTML($stylesheet,1);
                $mpdf->SetDisplayMode('fullpage');
                //$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
               
                $mpdf->SetProtection(array('print'));
                
                $mpdf->SetHTMLHeader($htmlHeader);
                $mpdf->SetHTMLFooter("");
                $mpdf->AddPage('L','','','','',5,5,30,5,5,5,'','','','','','','','','','A4-L');
                $mpdf->WriteHTML($pdfData);
                $mpdf->Output($pdfFileName,'I');
            }else{
                $this->printJson(['status'=>1, 'tbody'=>$tbody, 'thead'=>$thead]);
            }
        // endif;
            // $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }
}
?>