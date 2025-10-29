<?php
class CftAuthorization extends MY_Controller{
    private $indexPage = "cft_authorization";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Cft Authorization";
		$this->data['headData']->controller = "cftAuthorization";
        $this->data['headData']->pageUrl = "cftAuthorization";
	}
	
	public function index(){
        $this->data['empDataList'] = $this->employee->getEmployeeList();   
        $this->data['cftData'] = $this->cftAuthorization->getCftList();
        $emp_id = ''; $i=1;
        foreach($this->data['cftData'] as $row):
            if($i==1):
                $emp_id.=$row->emp_id;
            else:
                $emp_id.=','.$row->emp_id;
            endif; $i++;
        endforeach;
        $this->data['empData']=$emp_id;
        $this->load->view($this->indexPage,$this->data);
    }

    public function saveCftAuth(){
        $data = $this->input->post();
        $errorMessage = "";

        if(empty($data['emp_id']))
            $errorMessage .= "Somthing went wrong.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>2,'message'=>$errorMessage]);
        else:
            //$data['created_by'] = $this->session->userdata('loginId');
            $response = $this->cftAuthorization->saveCftAuth($data);
            $this->printJson($this->setCftAuh($data['emp_id']));
        endif;
    }

    public function setCftAuh($id)
    {
        $cftData = $this->cftAuthorization->getCftList($id);
        $cftHtml = '';
        if (!empty($cftData)) :
            $i = 1; $html = ""; 
            foreach ($cftData as $row) :
                $cftHtml .= '<tr id="'.$row->id.'">
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->emp_name.'</td>
                        <td>'.$row->name.'</td>
                        <td>'.$row->title.'</td>
                        <td class="text-center">'.$row->sequence.'</td>
                        </tr>';
            endforeach;
        else :
            $cftHtml .= '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
        endif;
        return ['status' => 1, "cftHtml" => $cftHtml];
    }

    public function updateEmpSequance(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['id']))
			$errorMessage['id'] = "Cft ID is required.";
		
		if(empty($errorMessage)):
			$this->printJson($this->cftAuthorization->updateEmpSequance($data));			
		endif;
    }
}
?>