<?php
class MachineTicket extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }

/* Create By : Avruti @29-11-2021 10:10 AM
     update by : 
     note :
*/  
    public function machineTicketList(){
        $total_rows = $this->ticketModel->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/machineTicket/machineTicketList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['machineTicketList'] = $this->ticketModel->getMachineTicketList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addMachineTicket(){
        $this->data['trans_prefix'] = "MT/".$this->shortYear."/";
        $this->data['nextTransNo'] = $this->ticketModel->nextTransNo();
        $this->data['machineData'] = $this->ticketModel->getMachineName();
        $this->data['deptData'] = $this->ticketModel->getDepartment();
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

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
            $data['created_by'] = $this->loginId;
            $this->printJson($this->ticketModel->save($data));
        endif;
    }

    public function view(){
        $id = $this->input->post('id');
        $this->data['trans_prefix'] = "MT/".$this->shortYear."/";
        $this->data['nextTransNo'] = $this->ticketModel->nextTransNo();
        $this->data['machineData'] = $this->ticketModel->getMachineName();
        $this->data['deptData'] = $this->ticketModel->getDepartment();
        $this->data['dataRow'] = $this->ticketModel->getMachineTicket($id);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->ticketModel->delete($id));
        endif;
    }
}
?>