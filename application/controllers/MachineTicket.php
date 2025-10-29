<?php
class MachineTicket extends MY_Controller {
    private $indexPage = "machine_ticket/index";
    private $ticketForm = "machine_ticket/form";
    private $solutionPage = "machine_ticket/machine_solution";
    private $maintenanceLBReport = "report/maintenance_report/maintenanceLB_report";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Machine Ticket";
		$this->data['headData']->controller = "machineTicket";
		$this->data['headData']->pageUrl = "machineTicket";
		$this->data['floatingMenu'] = $this->load->view('report/hr_report/floating_menu',[],true);
	}
	
	public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->ticketModel->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getMachineTicketData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMachineTicket(){
        $this->data['trans_prefix'] = "MT/".$this->shortYear."/";
        $this->data['nextTransNo'] = $this->ticketModel->nextTransNo();
        $this->data['machineData'] = $this->ticketModel->getMachineName();
        $this->data['deptData'] = $this->ticketModel->getDepartment();
        $this->load->view($this->ticketForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no']))
            $errorMessage['trans_no'] = "Trans. no. is required.";
        if(empty($data['machine_id']))
            $errorMessage['machine_id'] = "Machine is required.";
        if(empty($data['dept_id']))
            $errorMessage['dept_id'] = "Department is required.";
        if(empty($data['problem_date']))
            $errorMessage['problem_date'] = "Problem Date is required.";
        if(empty($data['problem_title']))
            $errorMessage['problem_title'] = "Problem Title is required.";
        if(empty($data['problem_detail']))
            $errorMessage['problem_detail'] = "Problem Detail is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->ticketModel->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['trans_prefix'] = "MT/".$this->shortYear."/";
        $this->data['nextTransNo'] = $this->ticketModel->nextTransNo();
        $this->data['machineData'] = $this->ticketModel->getMachineName();
        $this->data['deptData'] = $this->ticketModel->getDepartment();
        $this->data['dataRow'] = $this->ticketModel->getMachineTicket($id);
        $this->load->view($this->ticketForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->ticketModel->delete($id));
        endif;
    }

    public function getMachineSolution(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->ticketModel->getMachineTicket($id);
        $this->load->view($this->solutionPage,$this->data);
    }

    public function saveMachineSolution(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['solution_by']))
            $errorMessage['solution_by'] = "Solution By is required.";
        if(empty($data['solution_date']))
            $errorMessage['solution_date'] = "Solution Date is required.";
        if(empty($data['solution_detail']))
            $errorMessage['solution_detail'] = "Solution Detail is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->ticketModel->save($data));
        endif;
    }

    /* AAVRUTI */
    public function maintenanceLogReport(){
        $this->data['pageHeader'] = 'MACHINE BREAKDOWN MAINTENANCE HISTORY CARD';
        $this->load->view($this->maintenanceLBReport,$this->data);
    }

    public function getMachineTicketListByDate(){
        $data = $this->input->post();
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $rejectionData = $this->ticketModel->getMachineTicketListByDate($data);
            $this->printJson($rejectionData);
        endif;
    }

	public function printMaintenanceLog($pdate){
        $data['from_date']=explode('~',$pdate)[0];
        $data['to_date']=explode('~',$pdate)[1];

        $mlogData = $this->ticketModel->getMachineTicketListByDate($data); 
        
        $logo=base_url('assets/images/logo.png');
		
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-center" style="font-size:1rem;width:60%">MACHINE BREAKDOWN MAINTENANCE HISTORY CARD</td>
							<td style="width:20%;" class="text-right"><span style="font-size:0.8rem;">F/MNT/01 (00/01.01.16)</td>
						</tr>
					</table>';
        $itemList='<table id="reportTable" class="table table-bordered align-items-center itemList">
								<thead class="thead-info" id="theadData">';
                                foreach($mlogData['mlogData'] as $row)
                                {
                                    $itemList.='<tr>
                                        <td colspan="5" style="font-size:14px;height:30px;"><b>M/C NAME : </b>'.$row->item_name.'</td>
                                        <td colspan="3" style="font-size:14px;"><b>M/C NO : </b>'.$row->item_code.'</td>
                                        <td colspan="3" style="font-size:14px;" class="text-center"><b>2023-2024</b></td>
                                    </tr>';
                                }
                                $itemList.='<tr class="text-center">
										<th rowspan="2" style="width:20px;">#</th>
										<th rowspan="2" style="width:80px;">Date</th>
                                        <th rowspan="2" style="width:80px;">Time</th>
										<th rowspan="2" style="width:150px;">Details of Breakdown</th>
                                        <th rowspan="2" style="width:150px;">Details of Work Done</th>
                                        <th colspan="2">Maintenance Done</th>
                                        <th rowspan="2" style="width:80px;">Sign.</th>
                                        <th rowspan="2" style="width:80px;">Verification Status</th>
                                        <th rowspan="2" style="width:80px;">Total Breakdown Hours</th>	
                                        <th rowspan="2" style="width:80px;">Sign.</th>	
									</tr>
                                    <tr class="text-center">
                                        <th style="width:80px;">By</th>
                                        <th style="width:100px;">Date & Time</th>
                                    </tr>
								</thead>
                                <tbody id="tbodyData">'; 
                               
        $itemList.=$mlogData['tbody'].'</tbody></table>';

	    $originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';
		
		$pdfData = $originalCopy;
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='JWO-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('L','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
}
?>