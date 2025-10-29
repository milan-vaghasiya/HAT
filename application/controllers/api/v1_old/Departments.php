<?php
class Departments extends MY_Apicontroller{

	private $category = ["1"=>"Admin","2"=>"HR","3"=>"Purchase","4"=>"Sales","5"=>"Store","6"=>"QC","7"=>"General","8"=>"Machining"];

    public function __construct() {
        parent:: __construct();        
    }
/* Create By : Avruti @29-11-2021 10:10 AM
     update by : 
     note :
*/
    public function DepartmentList(){
        $total_rows = $this->department->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/departments/departmentList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['departmentList'] = $this->department->getDepartmentList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addDepartment(){
       // $this->data['empData'] = $this->department->getEmployees();
        $this->data['categoryData'] = $this->category;
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['name']))
            $errorMessage['name'] = "Department name is required.";
        if(empty($data['category']))
            $errorMessage['category'] = "Category is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			//unset($data['empSelect']);
            $data['created_by'] = $this->loginId;
            $this->printJson($this->department->save($data));
        endif;
    }

    public function view(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->department->getDepartment($id);
       // $this->data['empData'] = $this->department->getEmployees();
        $this->data['categoryData'] = $this->category;
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->department->delete($id));
        endif;
    }
}
?>