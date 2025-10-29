<?php
class MasterDetail extends MY_Controller
{
    private $indexPage = "master_detail/index";
    private $formPage = "master_detail/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Master Detail";
		$this->data['headData']->controller = "masterDetail";
        $this->data['headData']->pageUrl = "masterDetail";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows($type=4){
        $data = $this->input->post(); $data['type'] = $type;
        $result = $this->masterDetail->getDTRows($data,$type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row): 
            if($row->type == 1):
				$row->typeName = 'Material Shape';
			elseif($row->type == 2):
                $row->typeName = 'Bar Type';  
            elseif($row->type == 4):
                $row->typeName = 'Sub Group';
            else:
                $row->typeName = 'Other Type';
            endif;             
            $row->sr_no = $i++;         
            $sendData[] = getMasterDetailData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMasterDetail($type=0){
        $this->data['type'] = $type;
        $this->data['refDataList'] = $this->masterDetail->getMasterDetailList();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(3);
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();
        //if(empty($data['code']))
		//	$errorMessage['code'] = "Code is required.";		
        if(empty($data['title']))
			$errorMessage['title'] = "Title is required.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->masterDetail->save($data));
        endif;
    }

    public function edit($type=0){     
        $id = $this->input->post('id');
        $this->data['type'] = $type;
        $this->data['refDataList'] = $this->masterDetail->getMasterDetailList();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList('3');
        $this->data['dataRow'] = $this->masterDetail->getMasterDetail($id);
        $this->load->view($this->formPage, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->masterDetail->delete($id));
        endif;
    }
}
?>