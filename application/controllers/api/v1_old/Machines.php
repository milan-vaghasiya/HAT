

<?php
class Machines extends MY_Apicontroller{

	private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);

    public function __construct() {
        parent:: __construct();        
    }
/* Create By : Avruti @29-11-2021 10:10 AM
     update by : 
     note :
*/  
    public function machinesList(){
        $total_rows = $this->machine->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/machines/machinesList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['machinesList'] = $this->machine->getMachinesList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

 /* Create By : Avruti @30-11-2021 12:38 PM
     update by : 
     note :
*/   

    public function addMachine(){
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->data['categoryList'] = $this->item->getCategoryList(5);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function getProcessData(){
        $id = $this->input->post('dept_id');
        $processData = $this->process->getDepartmentWiseProcess($id);
        $option = "";
        foreach ($processData as $row):
            $option .= '<option value="' . $row->id . '" >' . $row->process_name . '</option>';
        endforeach;
        
        $this->printJson(['status'=>1, 'option'=>$option]);
    }

    public function save(){
        $data = $this->input->post();
        if(isset($data['ftype']) and $data['ftype'] == 'activities'):
            unset($data['ftype']);
            $this->saveActivity($data);
        else:
            $errorMessage = array();
            if(empty($data['item_code']))
                $errorMessage['item_code'] = "Machine no. is required.";
            //if(empty($data['machine_brand']))
                //$errorMessage['machine_brand'] = "Brand Name is required.";
            //if(empty($data['machine_model']))
                //$errorMessage['machine_model'] = "Machine Model is required.";
            if(empty($data['location']))
                $errorMessage['location'] = "Department is required.";
            //if(empty($data['process_id']))
                //$errorMessage['process_id'] = "Process Name is required.";
            if(empty($data['category_id']))
                $errorMessage['category_id'] = "Category is required.";

            if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
            else:
                unset($data['processSelect']);
                $data['item_type']=5;
                $data['created_by'] = $this->loginId;
                $this->printJson($this->machine->save($data));
            endif;
        endif;
        
    }

    public function view(){
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->data['dataRow'] = $this->machine->getMachine($this->input->post('id'));
        $this->data['processData'] =  $this->process->getDepartmentWiseProcess($this->data['dataRow']->location);
        $this->data['categoryList'] = $this->item->getCategoryList(5);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->machine->delete($id));
        endif;
    }
}
?>