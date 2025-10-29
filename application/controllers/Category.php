<?php
class Category extends MY_Controller
{
    private $indexPage = "category/index";
    private $categoryForm = "category/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "category";
		$this->data['headData']->controller = "category";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->category->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCategoryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCategory(){
        $this->load->view($this->categoryForm);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['category']))
            $errorMessage['category'] = "Category Name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->category->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->category->getCategory($this->input->post('id'));
        $this->load->view($this->categoryForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->category->delete($id));
        endif;
    }
}
?>